<?php

namespace App\Services;

use SoapClient;
use SoapFault;
use Exception;

class CaeService
{
    public function solicitarCAE($token, $sign , $cuit)
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
                'Cuit'  => $cuit,
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