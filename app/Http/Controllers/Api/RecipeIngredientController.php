<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Recipe;
use Illuminate\Http\Request;

class RecipeIngredientController extends Controller
{
    private function ensureOwner(Request $request, Recipe $recipe)
    {
        if ($recipe->user_id !== $request->user()->id) {
            abort(response()->json(['message' => 'Unauthorized'], 403));
        }
    }

    public function attach(Request $request, Recipe $recipe)
    {
        $this->ensureOwner($request, $recipe);

        $data = $request->validate([
            'ingredient_ids' => 'required|array',
            'ingredient_ids.*' => 'exists:ingredients,id'
        ]);

        $recipe->ingredients()->syncWithoutDetaching($data['ingredient_ids']);

        return response()->json([
            'message' => 'Ingredients attached',
            'recipe' => $recipe->load('ingredients')
        ]);
    }

    public function sync(Request $request, Recipe $recipe)
    {
        $this->ensureOwner($request, $recipe);

        $data = $request->validate([
            'ingredient_ids' => 'required|array',
            'ingredient_ids.*' => 'exists:ingredients,id'
        ]);

        $recipe->ingredients()->sync($data['ingredient_ids']);

        return response()->json([
            'message' => 'Ingredients synced successfully',
            'recipe' => $recipe->load('ingredients')
        ]);
    }

    public function detach(Request $request, Recipe $recipe)
    {
        $this->ensureOwner($request, $recipe);

        $data = $request->validate([
            'ingredient_ids' => 'required|array',
            'ingredient_ids.*' => 'exists:ingredients,id'
        ]);

        $recipe->ingredients()->detach($data['ingredient_ids']);

        return response()->json([
            'message' => 'Ingredients detached',
            'recipe' => $recipe->load('ingredients')
        ]);
    }
}
