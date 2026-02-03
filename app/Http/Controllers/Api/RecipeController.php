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
        $query = Recipe::with('ingredients');

        // kalorije
        if ($request->filled('min_calories')) {
            $query->where('calories', '>=', $request->min_calories);
        }
        if ($request->filled('max_calories')) {
            $query->where('calories', '<=', $request->max_calories);
        }

        // vrijeme pripreme
        if ($request->filled('prep_time_max')) {
            $query->where('prep_time', '<=', $request->prep_time_max);
        }
        if ($request->filled('prep_time_min')) {
            $query->where('prep_time', '>=', $request->prep_time_min);
        }

        // filtriranje po sastojcima (ID-jevi)
        if ($request->filled('ingredients')) {
            $ingredients = $request->ingredients;
            if (is_string($ingredients)) {
                $ingredients = explode(',', $ingredients);
            }
            $query->whereHas('ingredients', function ($q) use ($ingredients) {
                $q->whereIn('ingredients.id', $ingredients);
            });
        }

        return response()->json($query->get());
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

        // Dodaj user_id trenutnog korisnika
        $validated['user_id'] = $request->user()->id;

        $recipe = Recipe::create($validated);

        return response()->json($recipe, 201);
    }

    // PUT /api/recipes/{id}
    public function update(Request $request, $id)
    {
        $recipe = Recipe::findOrFail($id);

        // Provjera vlasništva
        if ($recipe->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

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
    public function destroy(Request $request, $id)
    {
        $recipe = Recipe::findOrFail($id);

        // Provjera vlasništva
        if ($recipe->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $recipe->delete();

        return response()->json(['message' => 'Recipe deleted successfully']);
    }
}
