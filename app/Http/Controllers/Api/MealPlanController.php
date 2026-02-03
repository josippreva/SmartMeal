<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Meal;
use App\Models\Recipe;
use Illuminate\Http\Request;

class MealPlanController extends Controller
{
    // POST /api/meal-plan
    public function generate(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'goal' => 'nullable|string',
            'preferences' => 'nullable|array',
        ]);

        // auth:sanctum već štiti rutu, ali ostavimo sigurnosno
        if (!$request->user()) {
            return response()->json([
                'message' => 'Neautorizirano. Morate biti prijavljeni.'
            ], 401);
        }

        $userId = $request->user()->id;
        $date = $request->input('date');

        // ✅ BLOKIRAJ GENERIRANJE ako već postoji barem jedan obrok za taj datum
        $alreadyHasMealsForDate = Meal::where('user_id', $userId)
            ->whereDate('date', $date)
            ->exists();

        if ($alreadyHasMealsForDate) {
            return response()->json([
                'message' => 'Za odabrani datum već imate dodan obrok u "Moji obroci". Obrišite ga ili odaberite drugi datum.'
            ], 409);
        }

        // ✅ UZMI SAMO RECEPTE ULOGIRANOG KORISNIKA
        $recipes = Recipe::where('user_id', $userId)
            ->inRandomOrder()
            ->take(3)
            ->get();

        // ✅ ako korisnik nema dovoljno recepata
        if ($recipes->count() < 3) {
            return response()->json([
                'message' => 'Nemate dovoljno recepata za generiranje plana (potrebno je barem 3 recepta).'
            ], 422);
        }

        $mealTypes = ['doručak', 'ručak', 'večera'];
        $meals = [];

        foreach ($recipes as $index => $recipe) {
            $meal_type = $mealTypes[$index] ?? 'obrok';

            // ✅ dodatna zaštita (ako ikad budeš generirala više puta istog dana)
            $exists = Meal::where('user_id', $userId)
                ->whereDate('date', $date)
                ->where('meal_type', $meal_type)
                ->exists();

            if ($exists) {
                return response()->json([
                    'message' => "Već postoji {$meal_type} za odabrani datum."
                ], 409);
            }

            $meal = Meal::create([
                'user_id'   => $userId,
                'recipe_id' => $recipe->id,
                'date'      => $date,
                'meal_type' => $meal_type,
            ])->load('recipe'); // ✅ da frontend dobije meal.recipe.name

            $meals[] = $meal;
        }

        return response()->json([
            'date'  => $date,
            'meals' => $meals,
        ], 201);
    }
}
