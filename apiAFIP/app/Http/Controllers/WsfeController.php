<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\WsfeService;

class WsfeController extends Controller
{
    private $wsfeService;

    public function __construct(WsfeService $wsfeService)
    {
        $this->wsfeService = $wsfeService;
    }

    public function obtenerUltimoComprobante()
    {
        $tokenData = $this->wsfeService->getTokenAndSign();

        $result = $this->wsfeService->obtenerUltimoAutorizado(
            $tokenData['token'],
            $tokenData['sign']
        );

        return response()->json($result);
    }
}
