<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
}
