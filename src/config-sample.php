<?php

define('CONFIG', [
    'development'   => true,
    'version'       => '0.88',
    'companyName'   => 'Skol-IF Skåne',
    'siteName'      => 'Utbildningsplattform',
    'siteSlogan'    => 'Utbildningsplattform för Skolidrottsförbundet i Skåne',
    'siteDescription' => 'Utbildningsplattform som är skapade för utbildare att utbilda blivande och befintliga ledare i skolidrottsföreningar i Skåne.',
    'siteURL'       => 'https://skutb.test',
    'sitePath'      => '/',
    'locale'        => 'sv_SE',
    'language'      => 'sv-SE',
    'timezone'      => 'Europe/Stockholm',
    'paths'         => [
        'src'       => '/src',
        'lib'       => '/lib',
        'templates' => '/templates',
        'views'     => '/views',
        'images'    => '/assets/img',
        'uploads'   => '/uploads',
        'css'       => '/assets/css',
        'js'        => '/assets/js',
        'fonts'     => '/assets/fonts',
    ],
    'db'            => [
        'host'      => 'localhost',
        'database'  => 'education',
        'user'      => 'root',
        'password'  => '',
        'charset'   => 'utf8'
    ]
]);