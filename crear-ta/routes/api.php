<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AfipTAController;

Route::get('/create-ta', [AfipTAController::class, 'generateTA']);
