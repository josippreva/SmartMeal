<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Recipe;
use Illuminate\Http\Request;

class RecipeController extends Controller
{
    // GET /api/recipes  (✅ samo recepti trenutnog korisnika)
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $query = Recipe::with('ingredients')
            ->where('user_id', $userId);

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

        // ✅ filtriranje po sastojcima (MORA imati SVE odabrane)
        if ($request->filled('ingredients')) {
            $ingredients = $request->ingredients;

            if (is_string($ingredients)) {
                $ingredients = explode(',', $ingredients);
            }

            // očisti prazne + pretvori u int + ukloni duplikate
            $ingredients = array_values(array_unique(array_filter(array_map('intval', $ingredients))));

            // AND logika: za svaki ingredient_id dodaj poseban whereHas
            foreach ($ingredients as $ingId) {
                $query->whereHas('ingredients', function ($q) use ($ingId) {
                $q->where('ingredients.id', $ingId);
             });
            }
        }


        return response()->json($query->get());
    }

    // GET /api/recipes/{id} (✅ korisnik smije vidjeti samo svoj recept)
    public function show(Request $request, $id)
    {
        $recipe = Recipe::with('ingredients')->findOrFail($id);

        if ($recipe->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

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

            // ✅ sastojci odjednom (opcionalno)
            'ingredient_ids'   => 'sometimes|array',
            'ingredient_ids.*' => 'exists:ingredients,id',
        ]);

        $validated['user_id'] = $request->user()->id;

        $ingredientIds = $validated['ingredient_ids'] ?? [];
        unset($validated['ingredient_ids']);

        $recipe = Recipe::create($validated);

        // ✅ odmah poveži sve sastojke u jednom potezu
        if (!empty($ingredientIds)) {
            $recipe->ingredients()->sync($ingredientIds);
        }

        return response()->json($recipe->load('ingredients'), 201);
    }

    // PUT /api/recipes/{id}
    public function update(Request $request, $id)
    {
        $recipe = Recipe::findOrFail($id);

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

        return response()->json($recipe->load('ingredients'));
    }

    // DELETE /api/recipes/{id}
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
