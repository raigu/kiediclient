# kiediclient

Krediidiinfo EDI REST liidese klient.

Näidisprogrammi eesmärgiks on näidata ühte võimalust kuidas EDI-ga suhelda.

Programmis on näidatud järgmised tegevused:
* kliendi registreerimine EDI-s
* kliendi tokeni uuendamine
* müügiarve saatmine
* ostuarvete vastuvõtmine

# Installeerimine

Eeldatakse PHP 5 ja libcurl paketi olemasolu.

    $> git clone git@github.com:raigu/kiediclient.git
    $> cd kiediclient


# Seadistamine


Seadistamine toimub failis config.php.

Programmi proovimiseks pole vaja eraldi seadistada. 

Siiski on soovitatav muuta receiverId ja senderId väärtusteks enda äriregistrikoodid. 

Nii välditakse situatsiooni kui kaks arendajat muudavad sama kliendi tokenit. Sellisel juhul hilisem tokeni muutja muudab kehtetuks varasema tokeni. 
Samuti tekivad probleemid kui üks kustutab kõik ostuarved ära ja teine ei saa aru, miks müügiarvet enam EDI-s pole.
(NB! Lähitulevikus tuleb eraldi test-server ning iga arendaja saab oma test-tokeni, siis on sellised situatsioonid välistatud)

Vaikimisi on sisse lülitatud *curl_verbose=1*, mille tulemusena kuvatakse konsoolile päringute ja vastuste päised ja sisud.
Kui on soovida lisada oma *print* silumisinfo kuvamist ja curl-i logid segavad, siis omistada *curl_verbose=0*.



# Käivitamine

    $>php main.php 


