<?php
// ── app/Http/Requests/StoreReceivingRequest.php ──────────────────

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReceivingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'receipt_date'   => 'required|date',
            'supplier_id'    => 'required|integer|exists:suppliers,id',
            'material_id'    => 'required|integer|exists:materials,id',
            'invoice_qty'    => 'required|numeric|min:0',
            'received_qty'   => 'required|numeric|min:0',
            'unit'           => 'required|string|max:50',
            'vehicle_number' => 'required|string|max:50',
            'lot_no'         => 'required|string|max:100|unique:receivings,lot_no',
            'remarks'        => 'nullable|string|max:1000',
        ];
    }
}