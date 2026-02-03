<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Meal;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class RecommendationController extends Controller
{
    // POST /api/recommendations
    public function getRecommendations(Request $request)
    {
        $request->validate([
            'goal' => 'nullable|in:weight_loss,maintenance,muscle_gain',
        ]);

        $user = $request->user();
        $userId = $user->id;

        // ✅ UZMI GOAL: ako nije poslan u requestu -> uzmi iz profila
        $goal = $request->input('goal', $user->goal);

        // ✅ samo recepti ulogiranog korisnika
        $query = Recipe::where('user_id', $userId);

        // ✅ izbaci recepte koje je korisnik već jeo danas
        $today = Carbon::today()->toDateString();

        $eatenToday = Meal::where('user_id', $userId)
            ->whereDate('date', $today)
            ->pluck('recipe_id');

        if ($eatenToday->count() > 0) {
            $query->whereNotIn('id', $eatenToday);
        }

        // ✅ logika cilja
        if ($goal === 'weight_loss') {
            // mršanje: manje kalorija
            $query->orderBy('calories', 'asc')
                  ->orderBy('protein', 'desc');
        } elseif ($goal === 'muscle_gain') {
            // masa: više proteina
            $query->orderBy('protein', 'desc')
                  ->orderBy('calories', 'desc');
        } else {
            // maintenance ili prazno: random
            $query->inRandomOrder();
        }

        $recipes = $query->take(3)->get();

        return response()->json([
            'goal' => $goal,
            'recommendations' => $recipes,
        ]);
    }
}
