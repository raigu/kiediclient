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
Siiski on soovitatav muuta receiverId ja senderId väärtusteks enda äriregistrikoodid, sest kui mitu arendajat katsetavad seda programmi samal ajal, võivad tulemused olla ettearvamatud.

Vaikimisi on sisse lülitatud *curl_verbose*, mille tulemusena kuvatakse konsoolile päringute ja vastuste päised ja sisud.

Programmi käivitamiseks 


# Käivitamine
$>php main.php

 
# Tulevik

Tulevikus tuleb eraldi EDI testserver ning iga arendaja peab testimiseks küsima eraldi tokeni. 
Praegu on veel testimiseks avaldatud virtuaalse vahendaja token kõigile kasutajatele.



