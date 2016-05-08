<?php

return [
    'protocol'      => 'https',
    'host'          => 'services.krediidiinfo.ee',
    'tokenMediator' => 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
    'mediatorId'    => '00000000', // Vahendaja äriregistrikood
    'senderId'      => '10256137', // Arve saatja ehk klient saatja äriregistrikood
    'receiverId'    => '10256137', // Arve saaja ehk klient saaja äriregsitrikood
    'curl_verbose'  => '1', // 1- kuvatakse konsoolile päringute headerid ja bodyd.
                            //  0 - ei kuvata konsoolile silumisinfot.clear
];