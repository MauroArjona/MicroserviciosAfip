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

    public function createTA()
    {
        $response = Http::get('http://localhost:8001/api/create-ta');
        return $response->json();
    }

    public function ultimoComprobante()
    {
        $response = Http::get('http://localhost:8003/api/ultimo-comprobante');
        return $response->json();
    }

    public function solicitarCAE(Request $request)
    {
        $response = Http::post('http://localhost:8004/api/solicitar-cae', $request->all());
        return $response->json();
    }
}
