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

    public function solicitarCAE()
    {
        $tokenData = $this->caeService->getTokenAndSign();

        $result = $this->caeService->solicitarCAE(
            $tokenData['token'],
            $tokenData['sign']
        );

        return response()->json($result);
    }
}

