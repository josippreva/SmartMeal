<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Meal;
use Illuminate\Http\Request;

class MealController extends Controller
{
    // GET /api/meals
    public function index(Request $request)
    {
        $meals = Meal::where('user_id', $request->user()->id)
                     ->with('recipe')
                     ->get();

        return response()->json($meals);
    }

    // POST /api/meals
    public function store(Request $request)
    {
        $request->validate([
            'recipe_id' => 'required|exists:recipes,id',
            'date' => 'required|date',
            'meal_type' => 'nullable|string'
        ]);

        $meal = Meal::create([
            'user_id' => $request->user()->id,
            'recipe_id' => $request->recipe_id,
            'date' => $request->date,
            'meal_type' => $request->meal_type,
        ]);

        return response()->json($meal, 201);
    }
}
