<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAcidTestingRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'test_date' => 'required|date',
            'lot_number' => 'required|string',
            'supplier' => 'required|string',
            'vehicle_number' => 'nullable|string',
            'avg_pallet_weight' => 'required|numeric',
            'foreign_material_weight' => 'nullable|numeric',
            'weigh_bridge_weight' => 'nullable|numeric',
        ];
    }
}
