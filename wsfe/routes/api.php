<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WsfeController;

Route::post('/ultimo-comprobante', [WsfeController::class, 'obtenerUltimoComprobante']);
