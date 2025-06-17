<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\WsfeService;
use Illuminate\Support\Facades\Log;

class WsfeController extends Controller
{
    private $wsfeService;

    public function __construct(WsfeService $wsfeService)
    {
        $this->wsfeService = $wsfeService;
    }

    public function obtenerUltimoComprobante(Request $request)
    {
        $cuit  = $request->input('cuit');
        $token = $request->input('token');
        $sign  = $request->input('sign');

        Log::info('Datos recibidos en WSFE:', [
            'cuit'  => $cuit,
            'token' => $token,
            'sign'  => $sign,
        ]);

        if (!$cuit || !$token || !$sign) {
            return response()->json(['error' => 'CUIT, token y sign requeridos'], 400);
        }

        try {
            $result = $this->wsfeService->obtenerUltimoAutorizado($token, $sign, $cuit);
            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Error al obtener comprobante:', ['message' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
