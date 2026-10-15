<?php

return [
    'conditions' => [
        'new'  => 'Neuf',
        'good' => 'Bon état',
        'used' => 'Occasion',
    ],

    'locations' => [
        'Abobo', 'Adjamé', 'Anyama', 'Attécoubé', 'Bingerville', 'Cocody',
        'Koumassi', 'Marcory', 'Plateau', 'Port-Bouët', 'Songon', 'Treichville',
        'Yopougon', 'Abengourou', 'Bouaké', 'Daloa', 'Grand-Bassam', 'Korhogo',
        'Man', 'San-Pédro', 'Yamoussoukro',
    ],
    'photo_disk' => env('PHOTO_DISK', 'public'),
];
