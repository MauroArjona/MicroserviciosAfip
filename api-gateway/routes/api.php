<?php

// routes/api.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiGatewayController;

Route::get('/call-dummy', [ApiGatewayController::class, 'callDummy']);
Route::post('/create-ta', [ApiGatewayController::class, 'createTA']);
Route::post('/ultimo-comprobante', [ApiGatewayController::class, 'ultimoComprobante']);
Route::post('/solicitar-cae', [ApiGatewayController::class, 'solicitarCAE']);
