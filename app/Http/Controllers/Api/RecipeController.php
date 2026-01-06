<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Recipe;
use Illuminate\Http\Request;

class RecipeController extends Controller
{
    // GET /api/recipes
    public function index(Request $request)
{
    $query = Recipe::with('ingredients'); // Uvijek učitavamo sastojke

    // Filtriranje po kalorijama
    if ($request->has('min_calories')) {
        $query->where('calories', '>=', $request->min_calories);
    }
    if ($request->has('max_calories')) {
        $query->where('calories', '<=', $request->max_calories);
    }

    // Filtriranje po vrsti obroka (ako postoji meal_type u bazi)
    if ($request->has('meal_type')) {
        $query->where('meal_type', $request->meal_type);
    }

    // Filtriranje po sastojcima (ingredient_ids ili imena)
    if ($request->has('ingredients')) {
        $ingredients = $request->ingredients;
        if (is_string($ingredients)) {
            $ingredients = explode(',', $ingredients);
        }

        $query->whereHas('ingredients', function($q) use ($ingredients) {
            $q->whereIn('id', $ingredients); // koristi ID-eve za preciznije filtriranje
        });
    }

    $recipes = $query->get();

    return response()->json($recipes);
}

    // GET /api/recipes/{id}
    public function show($id)
{
    $recipe = Recipe::with('ingredients')->findOrFail($id);
    return response()->json($recipe);
}

    // POST /api/recipes
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'calories'   => 'required|integer|min:0',
            'protein'    => 'required|numeric|min:0',
            'carbs'      => 'required|numeric|min:0',
            'fat'        => 'required|numeric|min:0',
            'prep_time'  => 'required|integer|min:0',
        ]);

        $recipe = Recipe::create($validated);

        return response()->json($recipe, 201);
    }

    // PUT /api/recipes/{id}
    public function update(Request $request, $id)
    {
        $recipe = Recipe::findOrFail($id);

        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'calories'   => 'required|integer|min:0',
            'protein'    => 'required|numeric|min:0',
            'carbs'      => 'required|numeric|min:0',
            'fat'        => 'required|numeric|min:0',
            'prep_time'  => 'required|integer|min:0',
        ]);

        $recipe->update($validated);

        return response()->json($recipe);
    }

    // DELETE /api/recipes/{id}
    public function destroy($id)
    {
        $recipe = Recipe::findOrFail($id);
        $recipe->delete();

        return response()->json(['message' => 'Recipe deleted successfully']);
    }
}
