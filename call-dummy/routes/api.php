<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CallDummyController;

Route::get('/call-dummy', [CallDummyController::class, 'callDummy']);
