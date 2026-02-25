<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAcidTestingRequest;
use App\Models\AcidTesting;

class AcidTestingController extends Controller
{
    public function index()
    {
        return response()->json(
            AcidTesting::latest()->get()
        );
    }

    public function store(StoreAcidTestingRequest $request)
    {
        $acidTesting = AcidTesting::create($request->validated());

        return response()->json([
            'message' => 'Acid testing saved successfully',
            'data' => $acidTesting
        ], 201);
    }
}