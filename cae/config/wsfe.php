<?php
// config/wsfe.php
return [
    'wsdl' => env('AFIP_WSDL', getenv('AFIP_WSDL')),
    'url'  => env('AFIP_URL', getenv('AFIP_URL')),
];
