<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use Illuminate\Http\Request;

class IngredientController extends Controller
{
    // GET /api/ingredients
    public function index()
    {
        return Ingredient::all();
    }

    // POST /api/ingredients
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string|unique:ingredients,name',
            'unit'       => 'required|in:g,ml',
            'ref_amount' => 'required|numeric|min:1',
            'calories'   => 'required|numeric|min:0',
            'protein'    => 'required|numeric|min:0',
            'carbs'      => 'required|numeric|min:0',
            'fat'        => 'required|numeric|min:0',
        ]); 

        return Ingredient::create($data);
    }
}
