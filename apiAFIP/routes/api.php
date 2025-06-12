<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AfipAuthController;
use App\Http\Controllers\CallDummyController;
use App\Http\Controllers\WsfeController;
use App\Http\Controllers\CaeController;

Route::get('/obtenerAutorizacion', [AfipAuthController::class, 'obtenerAutorizacion']);
Route::get('/pingServidor', [CallDummyController::class, 'callDummy']);
Route::get('/ultimoComprobante', [WsfeController::class, 'obtenerUltimoComprobante']);
Route::get('/solicitarCae', [CaeController::class, 'solicitarCAE']);

