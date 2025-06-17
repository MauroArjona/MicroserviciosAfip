<?php

// app/Http/Controllers/ApiGatewayController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ApiGatewayController extends Controller
{
    public function callDummy()
    {
        $response = Http::get('http://localhost:8002/api/call-dummy');
        return $response->json();
    }
    
    public function createTA(Request $request)
    {
        $cuit = $request->input('cuit');
        $response = Http::get('http://localhost:8001/api/create-ta', ['cuit' => $cuit]);
        return $response->json();
    }


    public function ultimoComprobante(Request $request)
    {
        $cuit = $request->input('cuit');

        if (!$cuit) {
            return response()->json(['error' => 'CUIT requerido'], 400);
        }

        // Obtener el TA desde el microservicio TA
        $taResponse = Http::get('http://localhost:8001/api/create-ta', ['cuit' => $cuit]);

        if ($taResponse->failed()) {
            return response()->json(['error' => 'Error al generar TA'], 500);
        }

        $taData = $taResponse->json();

        // Enviar token, sign y cuit al microservicio WSFE
        $wsfeResponse = Http::post('http://localhost:8003/api/ultimo-comprobante', [
            'cuit' => $cuit,
            'token' => $taData['token'],
            'sign' => $taData['sign'],
        ]);

        return $wsfeResponse->json();
    }


    public function solicitarCAE(Request $request)
    {
        $cuit = $request->input('cuit');

        if (!$cuit) {
            return response()->json(['error' => 'CUIT requerido'], 400);
        }

        // Paso 1: obtener TA
        $taResponse = Http::get('http://localhost:8001/api/create-ta', ['cuit' => $cuit]);
        if ($taResponse->failed()) {
            return response()->json(['error' => 'Error al generar TA'], 500);
        }
        $taData = $taResponse->json();

        // Paso 2: enviar a microservicio WSFE (CAE)
        $wsfeResponse = Http::post('http://localhost:8004/api/solicitar-cae', [
            'cuit' => $cuit,
            'token' => $taData['token'],
            'sign'  => $taData['sign'],
        ]);

        return $wsfeResponse->json();
    }
}
