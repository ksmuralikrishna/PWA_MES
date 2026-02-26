<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReceivingRequest;
use App\Models\Receiving;

class ReceivingController extends Controller
{
    public function store(StoreReceivingRequest $request)
    {
        $receiving = Receiving::create($request->validated());
        return response()->json(['status' => 'ok', 'data' => $receiving]);
    }

    public function index()
    {
        return response()->json(Receiving::orderBy('created_at', 'desc')->get());
    }
    
    public function getByLot($lotNo)
    {
        $receiving = Receiving::where('lot_no', $lotNo)->first();

        if (!$receiving) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lot not found'
            ], 404);
        }

        return response()->json($receiving);
    }

    /**
     * Soft cancel / close receiving
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string'
        ]);

        $receiving = Receiving::findOrFail($id);
        $receiving->status = $request->status;
        $receiving->save();

        return response()->json([
            'status' => 'ok'
        ]);
    }
}
