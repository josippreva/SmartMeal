<?php


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Recipe;
use Illuminate\Http\Request;

class RecommendationController extends Controller
{
    // POST /api/recommendations
    public function getRecommendations(Request $request)
    {
       $request->validate([
        'goal' => 'nullable|string',
        'preferences' => 'nullable|array',
    ]);

    $query = Recipe::query();

    //  Goal logika
    if ($request->goal === 'weight_loss') {
        $query->where('calories', '<=', 400);
    }

    if ($request->goal === 'muscle_gain') {
        $query->where('protein', '>=', 30);
    }

    // (maintenance = bez filtera)
    $eatenToday = $request->user()
    ->meals()
    ->whereDate('date', now()->toDateString())
    ->pluck('recipe_id');

    $query->whereNotIn('id', $eatenToday);

    $recipes = $query->inRandomOrder()->take(3)->get();

    return response()->json([
        'goal' => $request->goal,
        'preferences' => $request->preferences,
        'recommendations' => $recipes,
    ]);
    }
}
