<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    if (Auth::check()) {
        return Redirect::route('profile.dashboard', ['name' => Auth::user()->name]);
    }
    return view('welcome');
});

Route::get('user/{name}', [ProfileController::class, 'dashboard'])->name('profile.dashboard');
Route::get('user/{name}/projects', [ProjectController::class, 'indexView'])->name('projects.view');
Route::get('user/{name}/{projectName}', [ProjectController::class, 'show'])->name('project.view');

require __DIR__.'/api.php';
require __DIR__.'/auth.php';

// // User dashboard route
// Route::get('/{username}', [ProfileController::class, 'dashboard'])->name('profile.dashboard');

// // User projects route
// Route::get('/{username}/projects', [ProjectController::class, 'index'])->name('profile.projects');

// // Individual project route
// Route::get('/{username}/{projectName}', [ProjectController::class, 'show'])->name('profile.project.show');
