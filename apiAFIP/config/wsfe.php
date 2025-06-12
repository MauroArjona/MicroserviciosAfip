<?php

return [
    'cuit' => env('AFIP_CUIT', '30717457613'),
    'wsdl' => env('AFIP_WSDL', 'https://wswhomo.afip.gov.ar/wsfev1/service.asmx?WSDL'),
    'url'  => env('AFIP_URL', 'https://wswhomo.afip.gov.ar/wsfev1/service.asmx'),
    'ta_path' => storage_path('app/xml/TA.xml'),
    'cert_path' => storage_path('app/keys/certificate.crt'), // opcional, si usás
    'key_path' => storage_path('app/keys/private.key'),      // opcional, si usás
    'passphrase' => env('AFIP_KEY_PASS', ''), // La ponés en .env, si usás clave privada
];
