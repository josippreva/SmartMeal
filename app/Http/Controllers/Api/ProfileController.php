<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * Update profile
     */
    public function update(Request $request)
    {
        $data = $request->validate([
            'name' => 'nullable|string|max:255',
            'goal' => 'nullable|in:weight_loss,maintenance,muscle_gain',
        ]);

        $user = $request->user();

        // update samo polja koja postoje u requestu
        if (array_key_exists('name', $data)) $user->name = $data['name'];
        if (array_key_exists('goal', $data)) $user->goal = $data['goal'];

        $user->save();

        return response()->json([
            'message' => 'Profil je uspješno ažuriran.',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'goal' => $user->goal,
            ]
        ]);
    }

    /**
     * Get current user profile
     */
    public function show(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'goal' => $user->goal,
        ]);
    }
}
