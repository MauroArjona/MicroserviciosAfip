<?php

namespace App\Services;

use SoapClient;
use SoapFault;
use Exception;

class CaeService
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
            'PtoVta'   => 1,
            'CbteTipo' => 1,
        ];

        try {
            $result = $client->FECompUltimoAutorizado($params);
            return $result;
        } catch (SoapFault $e) {
            throw new Exception("Error al invocar el WSFE: " . $e->getMessage());
        }
    }

    public function solicitarCAE($token, $sign)
    {
        $client = new SoapClient(config('wsfe.wsdl'), [
            'soap_version' => SOAP_1_2,
            'location'     => config('wsfe.url'),
            'trace'        => 1,
        ]);

        // Cabecera del lote
        $feCabReq = [
            'CantReg'   => 1,
            'PtoVta'    => 1,
            'CbteTipo'  => 1,
        ];

        // Detalle del comprobante
        $feDetReq = [
            'FECAEDetRequest' => [
                [
                    'Concepto'    => 1,
                    'DocTipo'     => 80,
                    'DocNro'      => 20438961470,
                    'CbteDesde'   => 1,
                    'CbteHasta'   => 1,
                    'CbteFch'     => date('Ymd'),
                    'ImpTotal'    => 121.00,
                    'ImpTotConc'  => 0.00,
                    'ImpNeto'     => 100.00,
                    'ImpOpEx'     => 0.00,
                    'ImpIVA'      => 21.00,
                    'ImpTrib'     => 0.00,
                    'MonId'       => 'PES',
                    'MonCotiz'    => 1.000,
                    'Iva' => [
                        'AlicIva' => [
                            [
                                'Id'      => 5,
                                'BaseImp' => 100.00,
                                'Importe' => 21.00,
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $params = [
            'Auth' => [
                'Token' => $token,
                'Sign'  => $sign,
                'Cuit'  => config('wsfe.cuit'),
            ],
            'FeCAEReq' => [
                'FeCabReq' => $feCabReq,
                'FeDetReq' => $feDetReq,
            ],
        ];

        try {
            $response = $client->FECAESolicitar($params);
            return $response->FECAESolicitarResult;
        } catch (SoapFault $e) {
            throw new Exception("Error al invocar FECAESolicitar: " . $e->getMessage());
        }
    }
}
