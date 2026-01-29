<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AIController extends Controller
{
    public function recommend(Request $request)
    {
        // Validacija samo za preferencije i ciljeve
        $request->validate([
            'preferences' => 'nullable|string',
            'goals' => 'nullable|array', // goal is usually a string in frontend but backend might expect object
            'inventory' => 'nullable|array',
        ]);

        $user = $request->user();

        // Ako korisnik nema profilne podatke, možda treba vratiti grešku ili koristiti default
        // Ovdje pretpostavljamo da su podaci tu ili šaljemo default
        
        $payload = [
            'user_id' => $user->id,
            'age' => $user->age ?? 30, // Default vrijednosti ako nisu postavljene
            'gender' => $user->gender ?? 'male',
            'weight' => $user->weight ?? 70,
            'height' => $user->height ?? 175,
            'activity_level' => $user->activity_level ?? 'moderate',
            'preferences' => $request->input('preferences', $user->preferences ? implode(' ', $user->preferences) : ''), 
            'goals' => $request->input('goals', ['type' => 'maintenance']), // Frontend šalje objekt ili string? Provjerit ćemo.
            'inventory' => $request->input('inventory', $user->available_ingredients ?? []),
            'allergies' => $user->allergies ?? [],
            'diet_type' => $user->diet_type ?? null,
        ];

        // Prilagodba 'goal' polja jer frontend šalje 'goal' string (npr 'weight_loss'), a AI očekuje object `goals: {type: ...}`
        if ($request->has('goal') && is_string($request->input('goal'))) {
            $payload['goals'] = ['type' => $request->input('goal')];
        }

        // Dobivanje Python AI API URL-a iz environment varijable .env
        $aiApiUrl = env('AI_API_URL', 'http://127.0.0.1:8001/recommend-meals/');

        // Slanje POST requesta na Python AI API
        try {
            $response = Http::post($aiApiUrl, $payload);

            // Provjera uspješnosti zahtjeva prema AI modulu
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