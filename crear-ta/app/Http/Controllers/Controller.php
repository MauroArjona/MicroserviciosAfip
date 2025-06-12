<?php

// app/Http/Controllers/TaController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TaController extends Controller
{
    public function create()
    {
        // Aquí iría tu código real para crear el TA

        return response()->json([
            'mensaje' => 'TA generado',
            'TA' => '<xml>...</xml>'
        ]);
    }
}
