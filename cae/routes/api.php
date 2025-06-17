<?php

// routes/api.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CaeController;

Route::post('/solicitar-cae', [CaeController::class, 'solicitarCAE']);
