<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReceivingRequest extends FormRequest
{
    // Must return true for API requests to pass
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array {
        return [
            'date'           => 'required|date',
            'supplier'       => 'required|string',
            'material'       => 'required|string',
            'invoice_qty'    => 'required|integer|min:1',
            'received_qty'   => 'required|integer|min:0',
            'unit'           => 'required|string',
            'vehicle_number' => 'required|string',
            'lot_no'         => 'required|string|unique:receivings,lot_no',
            'remarks'        => 'nullable|string',
            'operator_id'    => 'required|integer'
        ];
    }
}
