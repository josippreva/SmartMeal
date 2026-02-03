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
            'name' => 'nullable|string',
            'goal' => 'nullable|string',
            'preferences' => 'nullable|array',
        ]);

        $user = $request->user();
        $user->update($data);

        return response()->json([
            'message' => 'Profile updated',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'goal' => $user->goal,
                'preferences' => $user->preferences,
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
            'preferences' => $user->preferences,
        ]);
    }
}
