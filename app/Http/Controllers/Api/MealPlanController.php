<?php



namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Recipe;
use Illuminate\Http\Request;

class MealPlanController extends Controller
{
    // POST /api/meal-plan
    public function generate(Request $request)
    {
        $request->validate([
           
            'date' => 'required|date',
            'goal' => 'nullable|string', // npr. "weight_loss", "maintenance", "muscle_gain"
            'preferences' => 'nullable|array', // npr. ["vegetarian", "gluten_free"]
        ]);

           $recipes = Recipe::inRandomOrder()->take(3)->get();

        $meals = [];

        foreach ($recipes as $index => $recipe) {
            // Odredimo tip obroka: doručak, ručak, večera
            $meal_type = match($index) {
                0 => 'doručak',
                1 => 'ručak',
                2 => 'večera',
            };

            // Spremamo u meals tablicu
            $meal = Meal::create([
                'user_id' => $request->user()->id,
                'recipe_id' => $recipe->id,
                'date' => $request->date,
                'meal_type' => $meal_type,
            ]);

            $meals[] = $meal;
        }

        return response()->json([
            'date' => $request->date,
            'meals' => $meals,
        ], 201);
    }
}