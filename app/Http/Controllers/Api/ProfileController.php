<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        $data = $request->validate([
            'name' => 'nullable|string|max:255',
            'goal' => 'nullable|in:weight_loss,maintenance,muscle_gain',
            'allergies' => 'sometimes|array',
            'allergies.*' => 'string|in:gluten,eggs,milk,peanuts,tree_nuts,soy,fish,shellfish,sesame',
        ]);

        $user = $request->user();

        // fill će popuniti samo fillable (name, goal, allergies...)
        $user->fill($data);
        $user->save();

        return response()->json([
            'message' => 'Profil je uspješno ažuriran.',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'goal' => $user->goal,
                'allergies' => $user->allergies ?? [],
            ]
        ]);
    }

    public function show(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'goal' => $user->goal,
            'allergies' => $user->allergies ?? [], // ✅ bitno da front dobije
        ]);
    }
}
