<?php

return [
    'protocol'      => 'https',
    'host'          => 'katseklaas.krediidiinfo.ee', # test host: katseklaas.krediidiinfo.ee; live host: services.krediidiinfo.ee
    'tokenMediator' => 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx', # vahendaja access token EDI-s. See tuleb küsida personaalselt Krediidiinfost. Oluline on täpsustada, et soovitakse TEST SERVER jaoks.
    'mediatorId'    => 'xxxxxxxx', // Vahendaja äriregistrikood. Sisesta siia oma ettevõtte äriregistrikood.
    'senderId'      => 'xxxxxxxx', // Arve saatja ehk klient saatja äriregistrikood.  Alustuseks soovitatav kasutada oma ettevõtte äriregistrikoodi.
    'receiverId'    => 'xxxxxxxx', // Arve saaja ehk klient saaja äriregsitrikood.  Alustuseks soovitatav kasutada oma ettevõtte äriregistrikoodi.
    'curl_verbose'  => '1', // 1- kuvatakse konsoolile päringute headerid ja bodyd.
                            //  0 - ei kuvata konsoolile silumisinfot.clear
];