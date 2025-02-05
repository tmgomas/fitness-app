<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserMeasurement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserMeasurementController extends Controller
{
    public function index()
    {
        $measurements = UserMeasurement::where('user_id', Auth::id())
            ->orderBy('recorded_at', 'desc')
            ->get();

        return response()->json(['data' => $measurements]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'chest' => 'nullable|numeric',
            'waist' => 'nullable|numeric',
            'hips' => 'nullable|numeric',
            'arms' => 'nullable|numeric',
            'thighs' => 'nullable|numeric',
            'recorded_at' => 'required|date'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $measurement = UserMeasurement::create([
            'user_id' => Auth::id(),
            'chest' => $request->chest,
            'waist' => $request->waist,
            'hips' => $request->hips,
            'arms' => $request->arms,
            'thighs' => $request->thighs,
            'recorded_at' => $request->recorded_at
        ]);

        return response()->json(['data' => $measurement], 201);
    }

    public function show($id)
    {
        $measurement = UserMeasurement::where('user_id', Auth::id())
            ->where('measurement_id', $id)
            ->firstOrFail();

        return response()->json(['data' => $measurement]);
    }

    public function update(Request $request, $id)
    {
        $measurement = UserMeasurement::where('user_id', Auth::id())
            ->where('measurement_id', $id)
            ->firstOrFail();

        $validator = Validator::make($request->all(), [
            'chest' => 'nullable|numeric',
            'waist' => 'nullable|numeric',
            'hips' => 'nullable|numeric',
            'arms' => 'nullable|numeric',
            'thighs' => 'nullable|numeric',
            'recorded_at' => 'required|date'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $measurement->update($request->all());

        return response()->json(['data' => $measurement]);
    }

    public function destroy($id)
    {
        $measurement = UserMeasurement::where('user_id', Auth::id())
            ->where('measurement_id', $id)
            ->firstOrFail();

        $measurement->delete();

        return response()->json(null, 204);
    }
}
