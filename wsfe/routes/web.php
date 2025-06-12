<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WsfeController;

Route::get('/ultimo-comprobante', [WsfeController::class, 'ultimoComprobante']);
