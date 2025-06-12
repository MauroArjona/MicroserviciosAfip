<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaController;

Route::get('/create-ta', [TaController::class, 'create']);
