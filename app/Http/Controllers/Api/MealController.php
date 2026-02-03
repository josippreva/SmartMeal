<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Meal;
use App\Models\Recipe;
use Illuminate\Http\Request;

class MealController extends Controller
{
    // GET /api/meals
    public function index(Request $request)
    {
        $meals = Meal::where('user_id', $request->user()->id)
            ->with('recipe')
            ->orderBy('date', 'asc')
            ->orderByRaw("FIELD(meal_type, 'doručak', 'ručak', 'večera')")
            ->get();

        return response()->json($meals);
    }

    // POST /api/meals
    public function store(Request $request)
    {
        $request->validate([
            'recipe_id' => 'required|exists:recipes,id',
            'date'      => 'required|date',
            'meal_type' => 'required|in:doručak,ručak,večera',
        ]);

        $userId = $request->user()->id;

        // ✅ recept mora pripadati korisniku
        $ownsRecipe = Recipe::where('id', $request->recipe_id)
            ->where('user_id', $userId)
            ->exists();

        if (!$ownsRecipe) {
            return response()->json([
                'message' => 'Ne možete odabrati recept koji nije vaš.'
            ], 403);
        }

        // ✅ spriječi duplikat: isti datum + isti tip obroka za istog korisnika
        $exists = Meal::where('user_id', $userId)
            ->where('date', $request->date)
            ->where('meal_type', $request->meal_type)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Već postoji taj tip obroka za odabrani datum.'
            ], 422);
        }

        $meal = Meal::create([
            'user_id'   => $userId,
            'recipe_id' => $request->recipe_id,
            'date'      => $request->date,
            'meal_type' => $request->meal_type,
        ]);

        return response()->json($meal->load('recipe'), 201);
    }

    // PUT /api/meals/{meal} (mijenja samo recept)
    public function update(Request $request, Meal $meal)
    {
        // ✅ ownership provjera
        if ($meal->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'recipe_id' => 'required|exists:recipes,id',
        ]);

        $userId = $request->user()->id;

        // ✅ ne može promijeniti na tuđi recept
        $ownsRecipe = Recipe::where('id', $validated['recipe_id'])
            ->where('user_id', $userId)
            ->exists();

        if (!$ownsRecipe) {
            return response()->json([
                'message' => 'Ne možete odabrati recept koji nije vaš.'
            ], 403);
        }

        $meal->update([
            'recipe_id' => $validated['recipe_id'],
        ]);

        return response()->json($meal->load('recipe'));
    }

    // DELETE /api/meals/{meal}
    public function destroy(Request $request, Meal $meal)
    {
        // ✅ ownership provjera
        if ($meal->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $meal->delete();

        return response()->json([
            'message' => 'Meal plan entry deleted successfully'
        ]);
    }
}
