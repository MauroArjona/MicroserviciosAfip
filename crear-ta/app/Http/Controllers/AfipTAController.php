<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AfipTAService;

class AfipTAController extends Controller
{
    private $afipTAService;

    public function __construct(AfipTAService $afipTAService)
    {
        $this->afipTAService = $afipTAService;
    }

    public function generateTA(Request $request)
    {
        $cuit = $request->input('cuit');
        if (!$cuit) {
            return response()->json(['error' => 'CUIT requerido'], 400);
        }

        try {
            $taData = $this->afipTAService->generateTA($cuit);
            return response()->json($taData);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
}
