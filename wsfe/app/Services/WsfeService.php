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
        $client = new SoapClient(config('wsfe.wsdl'), [
            'soap_version' => SOAP_1_1,
            'location'     => config('wsfe.url'),
            'trace'        => 1,
        ]);

        $params = [
            'Auth' => [
                'Token' => $token,
                'Sign'  => $sign,
                'Cuit'  => (float)$cuit,  // se recomienda castearlo a float para evitar problemas con ceros iniciales
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
