<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        $data = $request->validate([
            'goal' => 'nullable|string',
            'preferences' => 'nullable|array',
        ]);

        $user = $request->user();
        $user->update($data);

        return response()->json([
            'message' => 'Profile updated',
            'user' => $user
        ]);
    }
}
