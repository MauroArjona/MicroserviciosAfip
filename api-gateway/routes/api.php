<?php

// routes/api.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiGatewayController;

Route::get('/call-dummy', [ApiGatewayController::class, 'callDummy']);
Route::get('/create-ta', [ApiGatewayController::class, 'createTA']);
Route::get('/ultimo-comprobante', [ApiGatewayController::class, 'ultimoComprobante']);
Route::post('/solicitar-cae', [ApiGatewayController::class, 'solicitarCAE']);
