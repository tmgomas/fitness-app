<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserPreferenceController extends Controller
{
    public function index()
    {
        $preferences = UserPreference::where('user_id', Auth::id())
            ->orderBy('updated_at', 'desc')
            ->get();

        return response()->json(['data' => $preferences]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'allergies' => 'nullable|string',
            'dietary_restrictions' => 'nullable|string',
            'disliked_foods' => 'nullable|string',
            'fitness_goals' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $preferences = UserPreference::create([
            'user_id' => Auth::id(),
            'allergies' => $request->allergies,
            'dietary_restrictions' => $request->dietary_restrictions,
            'disliked_foods' => $request->disliked_foods,
            'fitness_goals' => $request->fitness_goals
        ]);

        return response()->json(['data' => $preferences], 201);
    }

    public function show($id)
    {
        $preferences = UserPreference::where('user_id', Auth::id())
            ->where('pref_id', $id)
            ->firstOrFail();

        return response()->json(['data' => $preferences]);
    }

    public function update(Request $request, $id)
    {
        $preferences = UserPreference::where('user_id', Auth::id())
            ->where('pref_id', $id)
            ->firstOrFail();

        $validator = Validator::make($request->all(), [
            'allergies' => 'nullable|string',
            'dietary_restrictions' => 'nullable|string',
            'disliked_foods' => 'nullable|string',
            'fitness_goals' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $preferences->update($request->all());

        return response()->json(['data' => $preferences]);
    }

    public function destroy($id)
    {
        $preferences = UserPreference::where('user_id', Auth::id())
            ->where('pref_id', $id)
            ->firstOrFail();

        $preferences->delete();

        return response()->json(null, 204);
    }
}
