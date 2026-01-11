<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Meal;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    // GET /api/analytics/daily
    public function daily(Request $request)
    {
        $user = $request->user();

        // ako nema date -> danas
        $date = $request->query('date', Carbon::today()->toDateString());

        $meals = Meal::where('user_id', $user->id)
            ->where('date', $date)
            ->with('recipe')
            ->get();

        $totals = [
            'calories' => 0,
            'protein' => 0,
            'carbs' => 0,
            'fat' => 0,
        ];

        foreach ($meals as $meal) {
            if ($meal->recipe) {
                $totals['calories'] += $meal->recipe->calories;
                $totals['protein'] += $meal->recipe->protein;
                $totals['carbs'] += $meal->recipe->carbs;
                $totals['fat'] += $meal->recipe->fat;
            }
        }

        return response()->json([
            'date' => $date,
            'total' => $totals,
            'meals' => $meals
        ]);
    }

    public function weekly(Request $request)
{
    $request->validate([
        'start_date' => 'required|date', // početak tjedna (npr. "2026-01-11")
    ]);

    $startDate = \Carbon\Carbon::parse($request->start_date)->startOfWeek(); // početak tjedna (ponedjeljak)
    $endDate = $startDate->copy()->endOfWeek(); // kraj tjedna (nedjelja)

    $user = $request->user();

    // Dohvati sve obroke tog korisnika u zadanom tjednu
    $meals = $user->meals()
        ->whereBetween('date', [$startDate, $endDate])
        ->with('recipe')
        ->get();

    // Grupiraj obroke po danima
    $weeklyData = [];

    foreach ($meals as $meal) {
        $day = $meal->date->toDateString();

        if (!isset($weeklyData[$day])) {
            $weeklyData[$day] = [
                'date' => $day,
                'total' => [
                    'calories' => 0,
                    'protein' => 0,
                    'carbs' => 0,
                    'fat' => 0,
                ],
                'meals' => []
            ];
        }

        $weeklyData[$day]['total']['calories'] += $meal->recipe->calories;
        $weeklyData[$day]['total']['protein'] += $meal->recipe->protein;
        $weeklyData[$day]['total']['carbs'] += $meal->recipe->carbs;
        $weeklyData[$day]['total']['fat'] += $meal->recipe->fat;

        $weeklyData[$day]['meals'][] = $meal;
    }

    // Sortiraj po datumu
    ksort($weeklyData);

    return response()->json(array_values($weeklyData));
}


public function monthly(Request $request)
{
    $month = $request->month; // očekuje YYYY-MM
    $userId = $request->user()->id;

    $meals = \App\Models\Meal::with('recipe')
        ->where('user_id', $userId)
        ->whereMonth('date', substr($month, 5, 2))
        ->whereYear('date', substr($month, 0, 4))
        ->get();

    $total = [
        'calories' => $meals->sum(fn($m) => $m->recipe->calories),
        'protein'  => $meals->sum(fn($m) => $m->recipe->protein),
        'carbs'    => $meals->sum(fn($m) => $m->recipe->carbs),
        'fat'      => $meals->sum(fn($m) => $m->recipe->fat),
    ];

    return response()->json([
        'month' => $month,
        'total' => $total,
        'meals' => $meals
    ]);
}

// GET /api/analytics/all-time
public function allTime(Request $request)
{
    $userId = $request->user()->id;

    $meals = \App\Models\Meal::with('recipe')
        ->where('user_id', $userId)
        ->get();

    $total = [
        'calories' => $meals->sum(fn($m) => $m->recipe->calories),
        'protein'  => $meals->sum(fn($m) => $m->recipe->protein),
        'carbs'    => $meals->sum(fn($m) => $m->recipe->carbs),
        'fat'      => $meals->sum(fn($m) => $m->recipe->fat),
    ];

    return response()->json([
        'total' => $total,
        'meals' => $meals
    ]);
}
}
