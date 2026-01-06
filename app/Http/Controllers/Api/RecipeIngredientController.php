<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Recipe;
use Illuminate\Http\Request;

class RecipeIngredientController extends Controller
{
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
}
