<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProfileController;

Route::get('api/user/{name}/projects', [ProjectController::class, 'indexJson']);
Route::get('api/user/{name}', [ProfileController::class, 'indexJson']);
