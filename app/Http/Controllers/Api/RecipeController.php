<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Recipe;
use Illuminate\Http\Request;

class RecipeController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $query = Recipe::with('ingredients')
            ->where('user_id', $userId);

        if ($request->filled('min_calories')) {
            $query->where('calories', '>=', $request->min_calories);
        }
        if ($request->filled('max_calories')) {
            $query->where('calories', '<=', $request->max_calories);
        }

        if ($request->filled('prep_time_max')) {
            $query->where('prep_time', '<=', $request->prep_time_max);
        }
        if ($request->filled('prep_time_min')) {
            $query->where('prep_time', '>=', $request->prep_time_min);
        }

        if ($request->filled('ingredients')) {
            $ingredients = $request->ingredients;

            if (is_string($ingredients)) {
                $ingredients = explode(',', $ingredients);
            }

            $ingredients = array_values(array_unique(array_filter(array_map('intval', $ingredients))));

            foreach ($ingredients as $ingId) {
                $query->whereHas('ingredients', function ($q) use ($ingId) {
                    $q->where('ingredients.id', $ingId);
                });
            }
        }

        return response()->json($query->get());
    }

    public function show(Request $request, $id)
    {
        $recipe = Recipe::with('ingredients')->findOrFail($id);

        if ($recipe->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($recipe);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'instructions' => 'nullable|string', 
            'prep_time'    => 'required|integer|min:0',
            'ingredients'  => 'sometimes|array',
            'ingredients.*.id' => 'required|exists:ingredients,id',
            'ingredients.*.quantity' => 'required|numeric|min:0',
        ]);

        $ingredientsData = $validated['ingredients'] ?? [];
        unset($validated['ingredients']);

        $validated['user_id'] = $request->user()->id;
        $validated['calories'] = 0;
        $validated['protein']  = 0;
        $validated['carbs']    = 0;
        $validated['fat']      = 0;

        $recipe = Recipe::create($validated);

        $syncData = [];
        foreach ($ingredientsData as $ing) {
            $syncData[$ing['id']] = ['quantity' => $ing['quantity']];
        }
        if (!empty($syncData)) {
            $recipe->ingredients()->sync($syncData);
        }

        $recipe->recalculateAndSaveNutrients();

        return response()->json($recipe->load('ingredients'), 201);
    }

    public function update(Request $request, $id)
    {
        $recipe = Recipe::findOrFail($id);

        if ($recipe->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name'                   => 'required|string|max:255',
            'instructions'           => 'nullable|string',
            'prep_time'              => 'required|integer|min:0',
            'ingredients'            => 'sometimes|array',
            'ingredients.*.id'       => 'required|exists:ingredients,id',
            'ingredients.*.quantity' => 'required|numeric|min:0',
        ]);

        $ingredientsData = $validated['ingredients'] ?? [];
        unset($validated['ingredients']);

        $recipe->update($validated);

        if (!empty($ingredientsData)) {
            $syncData = [];
            foreach ($ingredientsData as $ing) {
                $syncData[$ing['id']] = ['quantity' => $ing['quantity']];
            }
            $recipe->ingredients()->sync($syncData);
        }

        $recipe->recalculateAndSaveNutrients();

        return response()->json($recipe->load('ingredients'));
    }

    public function destroy(Request $request, $id)
    {
        $recipe = Recipe::findOrFail($id);

        if ($recipe->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $recipe->delete();

        return response()->json(['message' => 'Recipe deleted successfully']);
    }
}
