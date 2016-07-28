<?php defined("INCLUDED") or die("nel");

$config = [
    'maintenance' => false,

    # Datos de cuenta
    'web-user' => "",
    'web-pass' => "",

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
        'bestado' => [
            'user' => '', # RUT con puntos y guión
            'pass' => ''
        ]
    ]
];