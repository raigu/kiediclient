# kiediclient

Krediidiinfo [EDI REST liidese](services.krediidiinfo.ee/edi/) klient.

Näidisprogrammi eesmärgiks on näidata ühte võimalust kuidas EDI-ga suhelda.

Programmis on näidatud järgmised tegevused:
* kliendi registreerimine EDI-s
* kliendi tokeni uuendamine
* müügiarve saatmine
* ostuarvete vastuvõtmine


Skript ühendub Krediidiinfo EDI test serveriga. Kasutamiseks on vaja saada Krediidiinfo käest vahendaja *access token*.


# Installeerimine

Eeldatakse PHP 5 ja libcurl paketi olemasolu.

    $> git clone git@github.com:raigu/kiediclient.git
    $> cd kiediclient


# Seadistamine

Enne skripti käivitamist tuleb seadistada fail *config.php*

Vaikimisi on *config.php*-s sisse lülitatud *curl_verbose=1*, mille tulemusena kuvatakse konsoolile HTTP päringute (request) ja vastuste (response) päised (header) ja sisud (payload).
Kui lisad liidese tundma õppimiseks silumisinfot väljastavaid *print* käske ja curl-i logid segavad, siis kasuta *curl_verbose=0*.


# Käivitamine

    $>php main.php 


