<?php defined("INCLUDED") or die();

$config = [
    'maintenance' => false,

    # Datos de base de datos
    'db_host' => "",
    'db_user' => "",
    'db_pass' => "",
    'db_name' => "",

    'external' => [
        'ripley' => [
            'user' => '', # RUT sin puntos ni guión
            'pass' => ''
        ],
        'banks' => [
            [
                'bank' => '',
                'user' => '', # RUT con puntos y guión
                'pass' => ''
            ]
        ]
    ]
];