<?php

namespace App\Services;

use SoapClient;
use SoapFault;
use Exception;

class WsfeService
{
    public function getTokenAndSign()
    {
        $taPath = config('wsfe.ta_path');
        $xml = simplexml_load_file($taPath);
        if (!$xml) {
            throw new Exception("No se pudo cargar el archivo TA.xml en: " . $taPath);
        }
        return [
            "token" => (string)$xml->credentials->token,
            "sign"  => (string)$xml->credentials->sign,
        ];
    }

    public function obtenerUltimoAutorizado($token, $sign)
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
                'Cuit'  => config('wsfe.cuit'),
            ],
            'PtoVta'   => 1,  // Punto de venta
            'CbteTipo' => 1,  // Tipo de comprobante (1 = Factura A)
        ];

        try {
            $result = $client->FECompUltimoAutorizado($params);
            return $result;
        } catch (SoapFault $e) {
            throw new Exception("Error al invocar el WSFE: " . $e->getMessage());
        }
    }
}
