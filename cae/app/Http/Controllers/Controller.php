<?php

// app/Http/Controllers/CaeController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CaeController extends Controller
{
    public function solicitar(Request $request)
    {
        // Código real para solicitar CAE

        return response()->json([
            'cae' => '00001234',
            'fechaVencimiento' => '2025-06-30'
        ]);
    }
}
