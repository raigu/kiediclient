# kiediclient

Krediidiinfo [EDI REST liidese](https://services.krediidiinfo.ee/edi/) klient.

EDI all mõeldakse siin Krediidiinfo elektrooniliste dokumentide vahendusteenust, 
mille abil on võimalik integreerida infosüsteemi elektrooniliste arvete saatmist ja vastuvõtmist.

Näidisprogrammi sihtgrupiks on EDI [vahendajate](http://services.krediidiinfo.ee/wiki/index.php/EDI#M.C3.B5isted) arendajad.

Näidisprogrammi eesmärgiks on näidata ühte võimalust kuidas EDI-ga suhelda.

Programmis on näidatud järgmised tegevused:
* kliendi registreerimine EDI-s
* kliendi tokeni uuendamine
* müügiarve saatmine
* ostuarve vastuvõtmine


Skript ühendub EDI test serveriga. Kasutamiseks on vaja saada Krediidiinfo käest vahendaja *access token*.


# Installeerimine

Eeldatakse PHP 5 ja libcurl paketi olemasolu.

    $> git clone https://github.com/raigu/kiediclient.git
    $> cd kiediclient


# Seadistamine

Enne skripti käivitamist tuleb seadistada fail *config.php*

Vaikimisi on *config.php*-s sisse lülitatud *curl_verbose=1*, mille tulemusena kuvatakse konsoolile HTTP päringute (request) ja vastuste (response) päised (header) ja sisud (payload).
Kui lisad liidese tundma õppimiseks silumisinfot väljastavaid *print* käske ja curl-i logid segavad, siis kasuta *curl_verbose=0*.


# Käivitamine

    $>php main.php 


