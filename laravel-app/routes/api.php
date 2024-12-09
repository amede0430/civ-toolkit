<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CategorieController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/




// Auth routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
;

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    // Route::apiResource("users", UserController::class);

    // Groupe pour les clients (custumer)
    Route::prefix('custumer')->middleware('custumer')->group(function () {
        // Ajoutez d'autres routes spécifiques aux clients ici.
    });

    // Groupe pour les ingénieurs (engineer)
    Route::prefix('engineer')->middleware('engineer')->group(function () {
        // Ajoutez d'autres routes spécifiques aux ingénieurs ici.
    });

    // Groupe pour les administrateurs (admin)
    Route::prefix('admin')->middleware('admin')->group(function () {
        // Ajoutez d'autres routes spécifiques aux administrateurs ici.
        Route::apiResource('categories', CategorieController::class);
    });
});
