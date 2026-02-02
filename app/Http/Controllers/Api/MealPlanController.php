<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Meal;      // ✅ DODANO: import Meal modela
use App\Models\Recipe;
use Illuminate\Http\Request;

class MealPlanController extends Controller
{
    // POST /api/meal-plan
    public function generate(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'goal' => 'nullable|string',         // npr. "weight_loss", "maintenance", "muscle_gain"
            'preferences' => 'nullable|array',   // npr. ["vegetarian", "gluten_free"]
        ]);

        // ✅ ako nema login/auth, $request->user() može biti null -> bacit ćemo 401
        if (!$request->user()) {
            return response()->json([
                'message' => 'Unauthenticated. You must be logged in to generate a meal plan.'
            ], 401);
        }

        $recipes = Recipe::inRandomOrder()->take(3)->get();

        $meals = [];

        foreach ($recipes as $index => $recipe) {
            // Odredimo tip obroka: doručak, ručak, večera
            $meal_type = match ($index) {
                0 => 'doručak',
                1 => 'ručak',
                2 => 'večera',
                default => 'obrok',
            };

            // Spremamo u meals tablicu
            $meal = Meal::create([
                'user_id'   => $request->user()->id,
                'recipe_id' => $recipe->id,
                'date'      => $request->input('date'),
                'meal_type' => $meal_type,
            ]);

            $meals[] = $meal;
        }

        return response()->json([
            'date'  => $request->input('date'),
            'meals' => $meals,
        ], 201);
    }
}
