<?php

namespace App\Http\Controllers\Api;

use App\Models\Mocktest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MocktestController extends Controller
{
    public function public()
    {
        $mocktests = Mocktest::where('status', '=', true)->get();
        return response()->json($mocktests);
    }

    public function index(Request $request)
    {
        $mocktests = Mocktest::where('user_id', '=', $request->user()->id)->get();
        return response()->json($mocktests);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
        ]);

        Mocktest::create([
            'user_id' => $request->user()->id,
            'title' => $request->title,
        ]);

        return response()->json([
            'message' => 'Mocktest created successfully',
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $mocktest = Mocktest::where('user_id', '=', $request->user()->id)
            ->find($id);

        if ($mocktest ?? false) {
            $mocktest->update([
                'user_id' => $request->user()->id,
                'title' => $request->title,
            ]);

            return response()->json([
                'message' => 'Mocktest created successfully',
            ], 201);
        }
        return response()->json([
            'error' => 'Mocktest not found',
        ], 400);
    }

    public function destroy(Request $request, $id)
    {
        $mocktest = Mocktest::where('user_id', '=', $request->user()->id)->find($id);
        $mocktest->delete();

        return response()->json([
            'message' => 'Mocktest deleted successfully',
        ]);
    }
}
