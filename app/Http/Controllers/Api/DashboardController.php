<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

use App\Models\Recipe;
use App\Models\Meal;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $uid = $user->id;

        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        // ✅ SAFE CHECKS (da ne pukne ako kolona ne postoji)
        $recipesHasUserId = Schema::hasColumn('recipes', 'user_id');
        $recipesHasCalories = Schema::hasColumn('recipes', 'calories');
        $recipesHasPrepTime = Schema::hasColumn('recipes', 'prep_time');

        $mealsHasDate = Schema::hasColumn('meals', 'date');

        // ===== RECIPES QUERY (user filter ako postoji user_id) =====
        $recipesQ = Recipe::query();
        if ($recipesHasUserId) {
            $recipesQ->where('user_id', $uid);
        }

        $totalRecipes = (clone $recipesQ)->count();

        $avgCalories = null;
        if ($recipesHasCalories) {
            $avgCalories = (clone $recipesQ)->whereNotNull('calories')->avg('calories');
            $avgCalories = $avgCalories ? (int) round($avgCalories) : null;
        }

        $quickest = null;
        if ($recipesHasPrepTime) {
            $quickest = (clone $recipesQ)
                ->whereNotNull('prep_time')
                ->orderBy('prep_time', 'asc')
                ->select('id', 'name', 'prep_time', 'calories')
                ->first();
        }

        $recentRecipes = (clone $recipesQ)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->select('id', 'name', 'calories', 'prep_time', 'created_at')
            ->get();

        // ===== MEALS =====
        $todayMeals = collect();
        $weekMealsCount = 0;
        $weekCalories = 0;

        if ($mealsHasDate) {
            $todayMeals = Meal::where('user_id', $uid)
                ->whereDate('date', $today)
                ->with(['recipe:id,name,calories,prep_time'])
                ->orderBy('created_at', 'asc')
                ->get();

            $weekMealsCount = Meal::where('user_id', $uid)
                ->whereBetween('date', [$startOfWeek, $endOfWeek])
                ->count();

            $weekMeals = Meal::where('user_id', $uid)
                ->whereBetween('date', [$startOfWeek, $endOfWeek])
                ->with(['recipe:id,calories'])
                ->get();

            foreach ($weekMeals as $m) {
                if ($m->recipe && $m->recipe->calories) {
                    $weekCalories += (int) $m->recipe->calories;
                }
            }
        }

        return response()->json([
            "user" => [
                "id" => $uid,
                "name" => $user->name,
                "goal" => $user->goal ?? null,
            ],
            "stats" => [
                "total_recipes" => $totalRecipes,
                "today_meals_count" => $todayMeals->count(),
                "week_meals_count" => $weekMealsCount,
                "avg_recipe_calories" => $avgCalories,
                "quickest_recipe" => $quickest,
            ],
            "today_meals" => $todayMeals,
            "recent_recipes" => $recentRecipes,
            "week" => [
                "start" => $startOfWeek->toDateString(),
                "end" => $endOfWeek->toDateString(),
                "total_calories_from_meals" => $weekCalories,
            ],
        ]);
    }
}
