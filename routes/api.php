<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RecipeController;
use App\Http\Controllers\Api\IngredientController;
use App\Http\Controllers\Api\RecipeIngredientController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\AnalyticsController;
use App\Http\Controllers\Api\RecommendationController;
use App\Http\Controllers\Api\MealController;
use App\Http\Controllers\Api\MealPlanController;
use App\Http\Controllers\AIController;

/*
|--------------------------------------------------------------------------
| JAVNE RUTE
|--------------------------------------------------------------------------
*/

// Auth
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Recepti (samo pregled)
Route::get('/recipes', [RecipeController::class, 'index']);
Route::get('/recipes/{id}', [RecipeController::class, 'show']);

// AI preporuke (ako treba biti javno)
Route::post('/ai/recommend', [AIController::class, 'recommend']);


/*
|--------------------------------------------------------------------------
| ZAŠTIĆENE RUTE (auth:sanctum)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);

    // Profil korisnika
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);

    // CRUD za recepte (SAMO vlasnik može edit/delete – logika u controlleru/policy)
    Route::post('/recipes', [RecipeController::class, 'store']);
    Route::put('/recipes/{id}', [RecipeController::class, 'update']);
    Route::delete('/recipes/{id}', [RecipeController::class, 'destroy']);

    // Sastojci recepata
    Route::post('/recipes/{recipe}/ingredients', [RecipeIngredientController::class, 'attach']);
    Route::put('/recipes/{recipe}/ingredients', [RecipeIngredientController::class, 'sync']);
    Route::delete('/recipes/{recipe}/ingredients', [RecipeIngredientController::class, 'detach']);

    // Ingredients CRUD
    Route::get('/ingredients', [IngredientController::class, 'index']);
    Route::post('/ingredients', [IngredientController::class, 'store']);

    // Meal plan
    Route::post('/meal-plan', [MealPlanController::class, 'generate']);
    Route::get('/meals', [MealController::class, 'index']);
    Route::post('/meals', [MealController::class, 'store']);

    // Preporuke
    Route::post('/recommendations', [RecommendationController::class, 'getRecommendations']);

    // Analytics
    Route::get('/analytics/daily', [AnalyticsController::class, 'daily']);
    Route::get('/analytics/weekly', [AnalyticsController::class, 'weekly']);
    Route::get('/analytics/monthly', [AnalyticsController::class, 'monthly']);
    Route::get('/analytics/all-time', [AnalyticsController::class, 'allTime']);
});
