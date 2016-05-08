<?php

$conf = require_once 'config.php';

// -----------------------------------------------------------------------------------------------
//                          Abifunktsioonid
// -----------------------------------------------------------------------------------------------

/**
 * Exception mis oskab võimaluse korral HTTP vastusest välja lugeda veateate.
 */
class EDIException extends Exception
{

    public function __construct($path, $code, $contentType, $body)
    {
        $err = "VIGA! {$code} : {$code}";
        if ($contentType == 'plain/text') { // Kui Content-Type on plain/text, siis on body-s veateade.
            $err .= " " . $body;
        }

        parent::__construct($err);
    }
}

/**
 * Abifunktsioon HTTP päringute tegemiseks.
 *
 * Sõltub globaalsest muutujast $conf.
 *
 * @param string      $pathOrUri kas path või täispikk URI.
 * @param array       $header    päise parameetrid
 * @param null|string $payload   päringuga kaasa pandav sisu. Kui puudub, siis NULL
 * @param string      $method    mis HTTP meetodiga peab päringu tegema. Võimalikud väärtused: get, post, head, delete
 *
 * @return array nelja elemendiline massiiv. Elemendid on:
 *               * code - vastuse kood (täisarv)
 *               * contentType - vastuse meediatüüp (string)
 *               * body - vastuse sisu (string)
 *               * header - vastuse päis (assotsiatiivne massiiv)
 *
 */
function request($pathOrUri, $header, $payload = null, $method = 'get')
{
    global $conf;

    if (strpos($pathOrUri, 'http') === 0) {
        $url = $pathOrUri;
    } else {
        $path = $pathOrUri;
        $url = "{$conf['protocol']}://{$conf['host']}{$path}";
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_VERBOSE, $conf['curl_verbose']);
    curl_setopt($ch, CURLOPT_HEADER, 1);

    $method = strtoupper($method);
    if ($method == 'POST') {
        curl_setopt($ch, CURLOPT_POST, 1);
        if (! is_null($payload)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        }
    } elseif (in_array($method, ['DELETE', 'HEAD'])) {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    } else {
        curl_setopt($ch, CURLOPT_POST, 0);
    }

    $output = curl_exec($ch);

    $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $rawHeader = substr($output, 0, $header_size);
    $rawHeader = explode("\n", $rawHeader);
    $header = [];
    foreach ($rawHeader as $item) {
        $parts = explode(":", $item, 2);
        if (count($parts) == 2) {
            $header[trim($parts[0])] = trim($parts[1]);
        }
    }
    $body = substr($output, $header_size);

    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);

    curl_close($ch);

    return [$code, $contentType, $body, $header];
}

// -----------------------------------------------------------------------------------------------
//                     Abifunktsioonid EDI-ga suhtlemiseks 
// -----------------------------------------------------------------------------------------------

/**
 * Registreeri klient EDI-is.
 *
 * @param string $clientId kliendi äriregistrikood
 *
 * @return void
 * @throws EDIException kui registreerimine ebaõnnestub.
 */
function registerClientInEDI($clientId)
{
    global $conf;

    $path = "/edi/clients/$clientId";
    $header = [
        "Authorization: Bearer {$conf['tokenMediator']}",
        "Accept: application/vnd.ki.edi+json; version=1.0",
    ];
    list($code, $contentType, $body, $header) = request($path, $header);

    if ($code == 200) {
        // klient on EDI-s registreeritud. Kontrollime, kas see on registreeritud meile?
        $data = json_decode($body, $assoc = true);
        if ($data['mediatorId'] != $conf['mediatorId']) {
            $err = "Klient saatjat ({$conf['senderId']}) ei saa EDI-s registreerida, " .
                "sest see on registreeritud juba teise vahendaja {$data['mediatorId']} poolt.";
            throw new Exception($err);
        }
    } elseif ($code == 404) {
        // Klient ei ole EDI-s, lisame.
        $path = "/edi/clients";
        $data = [
            'clientId'   => $clientId,
            'mediatorId' => $conf['mediatorId']
        ];
        $payload = json_encode($data);
        $header = [
            "Authorization: Bearer {$conf['tokenMediator']}",
            "Content-Type: application/vnd.ki.edi+json; version=1.0",
        ];

        list($code, $contentType, $body, $header) = request($path, $header, $payload, $method = 'post');

        if ($code != 201) {
            throw new EDIException($path, $code, $contentType, $body);
        }
    } else {
        throw new EDIException($path, $code, $contentType, $body);
    }
}

/**
 * Uuenda kliendi token-it EDI-s.
 *
 * @param string $clientId kliendi äriregistrikood
 *
 * @return string uus token
 * @throws EDIException
 */
function refreshClientToken($clientId)
{
    global $conf;

    $path = "/edi/clients/tokens";
    $data = [
        'clientId' => $clientId,
    ];
    $payload = json_encode($data);
    $header = [
        "Authorization: Bearer {$conf['tokenMediator']}",
        "Content-Type: application/vnd.ki.edi+json; version=1.0",
    ];

    list($code, $contentType, $body, $header) = request($path, $header, $payload, $method = 'post');
    if ($code == 201) {
        $data = json_decode($body, $assoc = true);
        $token = $data['token'];

        return $token;
    } else {
        throw new EDIException($path, $code, $contentType, $body);
    }
}

/**
 * @param string $clientId kliendi äriregistrikood
 *
 * @return bool tõene kui $clientId on EDI-s registreeritud ja temale saab saata EDI vahendusel e-arveid.
 */
function isReceiverInEDI($clientId)
{
    $path = "/edi/purchase-invoices/receivers/{$clientId}";
    $header = [];

    list($code, $contentType, $body, $header) = request($path, $header, null, $method = 'head');

    return $code == 200;
}

/**
 * Saada e-arve so uploadi e-arve EDI-sse.
 *
 * @param string $xml         Eesti E-arve XML versioon 1.11
 * @param string $tokenSender klient saatja token
 *
 * @return string üles laetud arve URI-i.
 * @throws EDIException kui saatmine ebaõnnestub.
 */
function sendInvoice($xml, $tokenSender)
{
    $path = '/edi/sales-invoices';
    $header = [
        "Authorization: Bearer {$tokenSender}",
        "Content-Type: application/vnd.ki.einvoice+xml; version=1.11",
    ];
    list($code, $contentType, $body, $header) = request($path, $header, $payload = $xml, $method = 'post');

    if ($code == 201) {
        return $header['Location'];
    } else {
        throw new EDIException($path, $code, $contentType, $body, $header);
    }
}

/**
 * @param string $tokenReceiver klient saaja token
 *
 * @return array alla laadimist ootavate ostuarve URI-de massiiv
 * @throws EDIException
 */
function getPurchaseInvoices($tokenReceiver)
{
    $path = '/edi/purchase-invoices';
    $header = [
        "Authorization: Bearer {$tokenReceiver}",
        "Accept: application/vnd.ki.edi+json; version=1.0",
    ];
    list($code, $contentType, $body, $header) = request($path, $header);
    if ($code == 200) {
        $data = json_decode($body, $assoc = true);

        return $data;
    } else {
        throw new EDIException($path, $code, $contentType, $body, $header);
    }
}

/**
 * Lae ostuarve EDI-st alla.
 *
 * @param string $uri           ostuarve URI
 * @param string $tokenReceiver klient saaja token
 *
 * @return string ostuarve
 * @throws EDIException
 */
function downloadPurchaseInvoice($uri, $tokenReceiver)
{
    $header = [
        "Authorization: Bearer {$tokenReceiver}",
        "Accept: application/vnd.ki.einvoice+xml; version=1.11",
    ];
    list($code, $contentType, $body, $header) = request($uri, $header);
    if ($code == 200) {
        return $body;
    } else {
        throw new EDIException($uri, $code, $contentType, $body, $header);
    }
}

/**
 * Kustuta ostuarve EDI-s.
 *
 * @param string $uri           ostuarve URI
 * @param string $tokenReceiver klient saaja token
 *
 * @return void
 *
 * @throws EDIException kui kustutamine ebaõnnestub
 */
function deletePurchaseInvoice($uri, $tokenReceiver)
{
    $header = [
        "Authorization: Bearer {$tokenReceiver}",
    ];
    list($code, $contentType, $body, $header) = request($uri, $header, null, $method = 'delete');
    if ($code != 204) {
        throw new EDIException($uri, $code, $contentType, $body, $header);
    }
}

// -----------------------------------------------------------------------------------------------
//                          Näidisprogramm
// -----------------------------------------------------------------------------------------------

// Registreerima saatja.
registerClientInEDI($conf['senderId']);

registerClientInEDI($conf['receiverId']);

// Kontrollime, kas klient saaja on EDI-s.
if (! isReceiverInEDI($conf['receiverId'])) {
    // Kui klient saaja pole EDI-s, siis registreerime enda nimele.
    registerClientInEDI($conf['receiverId']);
}

// KLIENT SAATJA SAADAB MÜÜGIARVE

$xml = file_get_contents(__DIR__ . '/invoice.xml');
$xml = str_replace('XXXXXXXX', $conf['receiverId'], $xml); // dummy-arves peab olema saaja äriregistrikood õige
$xml = str_replace('YYYYYYYY', $conf['senderId'], $xml); // dummy-arves peab olema saatja äriregistrikood õige
$tokenSender = refreshClientToken($conf['senderId']);
$uri = sendInvoice($xml, $tokenSender);

// KLIENT SAAJA LAEB ALLA OSTUARVEID

$tokenReceiver = refreshClientToken($conf['receiverId']);

$purchaseInvoices = getPurchaseInvoices($tokenReceiver);
while (count($purchaseInvoices) > 0) {

    foreach ($purchaseInvoices as $uri) {
        $xml = downloadPurchaseInvoice($uri, $tokenReceiver);
        deletePurchaseInvoice($uri, $tokenReceiver);
    }

    // EDI tagastab alla laadimiseks korraga limiteeritud arvu müügiarveid.
    // Seepärast korratakse alla laadimist ootavate müügiarvete küsimist pärast seda,
    // kui eelmise päringu tulemuses olnud müügiarved on edukalt alla laetud ja serveris kustutatud.
    $purchaseInvoices = getPurchaseInvoices($tokenReceiver);
}







    
    