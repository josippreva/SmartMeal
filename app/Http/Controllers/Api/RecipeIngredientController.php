<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Recipe;
use Illuminate\Http\Request;

class RecipeIngredientController extends Controller
{
    /**
     * Attach ingredients to recipe (dodaje nove, ali ne briše postojeće)
     */
    public function attach(Request $request, Recipe $recipe)
    {
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

    /**
     * Sync ingredients - potpuno zamjenjuje listu sastojaka
     * (koristi se pri editu recepta)
     */
    public function sync(Request $request, Recipe $recipe)
    {
        $data = $request->validate([
            'ingredient_ids' => 'required|array',
            'ingredient_ids.*' => 'exists:ingredients,id'
        ]);

        // sync() će automatski dodati nove i obrisati one koji nisu u listi
        $recipe->ingredients()->sync($data['ingredient_ids']);

        return response()->json([
            'message' => 'Ingredients synced successfully',
            'recipe' => $recipe->load('ingredients')
        ]);
    }

    /**
     * Detach (ukloni) određene sastojke
     */
    public function detach(Request $request, Recipe $recipe)
    {
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