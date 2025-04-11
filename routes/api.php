<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;

Route::get('api/user/{name}/projects', [ProjectController::class, 'indexJson']);
