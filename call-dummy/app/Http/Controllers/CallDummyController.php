<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use SoapClient;
use SoapFault;

class CallDummyController extends Controller
{
    private $wsdlPath;
    private $url;

    public function __construct()
    {
        $this->wsdlPath = config('wsfe.wsdl');
        $this->url      = config('wsfe.url');
    }

    public function callDummy()
    {
        $client = new SoapClient($this->wsdlPath, [
            'soap_version' => SOAP_1_1,
            'location'     => $this->url,
            'trace'        => 1,
        ]);

        try {
            $result = $client->FEDummy();

            $response = $result->FEDummyResult ?? $result;

            return response()->json([
                'AppServer'   => $response->AppServer ?? 'NO DATA',
                'DbServer'    => $response->DbServer ?? 'NO DATA',
                'AuthServer'  => $response->AuthServer ?? 'NO DATA',
            ]);

        } catch (SoapFault $e) {
            return response()->json([
                'error'   => true,
                'message' => "Error al invocar FEDummy: " . $e->getMessage(),
                'lastRequest'  => $client->__getLastRequest(),
                'lastResponse' => $client->__getLastResponse(),
            ], 500);
        }
    }
}
