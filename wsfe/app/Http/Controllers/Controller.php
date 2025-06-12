<?php

// app/Http/Controllers/WsfeController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WsfeController extends Controller
{
    public function ultimoComprobante()
    {
        // Código real para obtener el último comprobante

        return response()->json([
            'ultimo_comprobante' => 1234
        ]);
    }
}
