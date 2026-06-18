<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserPreference;

class UserPreferenceController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'key' => 'required|string',
            'value' => 'nullable|array'
        ]);

        UserPreference::updateOrCreate(
            ['user_id' => auth()->id(), 'key' => $request->key],
            ['value' => $request->value ?? []]
        );

        return response()->json(['success' => true]);
    }
}
