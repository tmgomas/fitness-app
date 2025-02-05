<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserHealthData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserHealthDataController extends Controller
{
    public function index()
    {
        $healthData = UserHealthData::where('user_id', Auth::id())
            ->orderBy('recorded_at', 'desc')
            ->get();

        return response()->json(['data' => $healthData]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'height' => 'nullable|numeric',
            'weight' => 'nullable|numeric',
            'bmi' => 'nullable|numeric',
            'blood_type' => 'nullable|string',
            'medical_conditions' => 'nullable|string',
            'recorded_at' => 'required|date'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $healthData = UserHealthData::create([
            'user_id' => Auth::id(),
            'height' => $request->height,
            'weight' => $request->weight,
            'bmi' => $request->bmi,
            'blood_type' => $request->blood_type,
            'medical_conditions' => $request->medical_conditions,
            'recorded_at' => $request->recorded_at
        ]);

        return response()->json(['data' => $healthData], 201);
    }

    public function show($id)
    {
        $healthData = UserHealthData::where('user_id', Auth::id())
            ->where('health_id', $id)
            ->firstOrFail();

        return response()->json(['data' => $healthData]);
    }

    public function update(Request $request, $id)
    {
        $healthData = UserHealthData::where('user_id', Auth::id())
            ->where('health_id', $id)
            ->firstOrFail();

        $validator = Validator::make($request->all(), [
            'height' => 'nullable|numeric',
            'weight' => 'nullable|numeric',
            'bmi' => 'nullable|numeric',
            'blood_type' => 'nullable|string',
            'medical_conditions' => 'nullable|string',
            'recorded_at' => 'required|date'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $healthData->update($request->all());

        return response()->json(['data' => $healthData]);
    }

    public function destroy($id)
    {
        $healthData = UserHealthData::where('user_id', Auth::id())
            ->where('health_id', $id)
            ->firstOrFail();

        $healthData->delete();

        return response()->json(null, 204);
    }
}
