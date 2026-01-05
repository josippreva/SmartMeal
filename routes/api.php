<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RecipeController;
use App\Http\Controllers\Api\AuthController;

// --- Javne rute ---
Route::get('/recipes', [RecipeController::class, 'index']);
Route::get('/recipes/{id}', function($id) {
    return \App\Models\Recipe::findOrFail($id);
});

// Auth rute
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// --- Zaštićene rute (samo za prijavljene korisnike) ---
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', function (Request $request) {
        return $request->user();
    });

    // Zaštićeni CRUD za recepte
    Route::post('/recipes', [RecipeController::class, 'store']);
    Route::put('/recipes/{id}', [RecipeController::class, 'update']);
    Route::delete('/recipes/{id}', [RecipeController::class, 'destroy']);


    
});


Route::middleware('auth:sanctum')->post('/recommendations', [\App\Http\Controllers\Api\RecommendationController::class, 'getRecommendations']);


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/meal-plan', [\App\Http\Controllers\Api\MealPlanController::class, 'generate']);
    Route::get('/meals', [\App\Http\Controllers\Api\MealController::class, 'index']);
    Route::post('/meals', [\App\Http\Controllers\Api\MealController::class, 'store']);
});