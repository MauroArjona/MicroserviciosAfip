<?php

namespace App\Services;

use SoapClient;
use SoapFault;
use Exception;

class WsfeService
{
    /*public function getTokenAndSign($cuit)
    {
        $taPath = storage_path("app/xml/$cuit/TA.xml");

        $xml = simplexml_load_file($taPath);
        if (!$xml) {
            throw new Exception("No se pudo cargar el archivo TA.xml para el CUIT $cuit en: $taPath");
        }

        return [
            "token" => (string)$xml->credentials->token,
            "sign"  => (string)$xml->credentials->sign,
        ];
    }*/

    public function obtenerUltimoAutorizado($token, $sign, $cuit)
    {
        $context = stream_context_create([
            'http' => [
                'header' => "User-Agent: PHPSoapClient"
            ],
            'ssl' => [
                'ciphers' => 'DEFAULT@SECLEVEL=1'
            ]
        ]);

        $client = new SoapClient(config('wsfe.wsdl'), [
            'soap_version'    => SOAP_1_1,
            'location'        => config('wsfe.url'),
            'trace'           => 1,
            'exceptions'      => true,
            'stream_context'  => $context,
            'cache_wsdl'      => WSDL_CACHE_NONE
        ]);

        $params = [
            'Auth' => [
                'Token' => $token,
                'Sign'  => $sign,
                'Cuit'  => (float)$cuit,
            ],
            'PtoVta'   => 1,
            'CbteTipo' => 1,
        ];

        try {
            $result = $client->FECompUltimoAutorizado($params);
            return $result;
        } catch (SoapFault $e) {
            throw new Exception("Error al invocar el WSFE para CUIT $cuit: " . $e->getMessage());
        }
    }
}
