<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CaeService;

class CaeController extends Controller
{
    private $caeService;

    public function __construct(CaeService $caeService)
    {
        $this->caeService = $caeService;
    }

    public function solicitarCAE(Request $request)
    {
        $cuit = $request->input('cuit');
        $token = $request->input('token');
        $sign  = $request->input('sign');

        if (!$cuit || !$token || !$sign) {
            return response()->json(['error' => 'Faltan parámetros obligatorios'], 400);
        }

        try {
            $result = $this->caeService->solicitarCAE($token, $sign, $cuit);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
