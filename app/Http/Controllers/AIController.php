<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AIController extends Controller
{
    public function recommend(Request $request)
    {
        $request->validate([
            'preferences' => 'nullable|string',
            'goals' => 'nullable|array', 
            'inventory' => 'nullable|array',
        ]);

        $user = $request->user();
        $payload = [
            'user_id' => $user->id,
            'age' => $user->age ?? 30, 
            'gender' => $user->gender ?? 'male',
            'weight' => $user->weight ?? 70,
            'height' => $user->height ?? 175,
            'activity_level' => $user->activity_level ?? 'moderate',
            'preferences' => $request->input('preferences', $user->preferences ? implode(' ', $user->preferences) : ''), 
            'goals' => $request->input('goals', ['type' => 'maintenance']), 
            'inventory' => $request->input('inventory', $user->available_ingredients ?? []),
            'allergies' => $user->allergies ?? [],
            'diet_type' => $user->diet_type ?? null,
        ];

        if ($request->has('goal') && is_string($request->input('goal'))) {
            $payload['goals'] = ['type' => $request->input('goal')];
        }

        $aiApiUrl = env('AI_API_URL', 'http://127.0.0.1:8001/recommend-meals/');

        try {
            $response = Http::post($aiApiUrl, $payload);

            if ($response->successful()) {
                return response()->json($response->json(), 200);
            } else {
                return response()->json(['error' => 'AI module returned an error', 'details' => $response->body()], $response->status());
            }

        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to connect to AI module', 'details' => $e->getMessage()], 500);
        }
    }
}