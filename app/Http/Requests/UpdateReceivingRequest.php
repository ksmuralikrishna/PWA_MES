<?php
// ── app/Http/Requests/UpdateReceivingRequest.php ─────────────────

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateReceivingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'receipt_date'   => 'sometimes|required|date',
            'supplier_id'    => 'sometimes|required|integer|exists:suppliers,id',
            'material_id'    => 'sometimes|required|integer|exists:materials,id',
            'invoice_qty'    => 'sometimes|required|numeric|min:0',
            'received_qty'   => 'sometimes|required|numeric|min:0',
            'unit'           => 'sometimes|required|string|max:50',
            'vehicle_number' => 'sometimes|required|string|max:50',
            'lot_no'         => ['sometimes', 'required', 'string', 'max:100', Rule::unique('receivings', 'lot_no')->ignore($id)],
            'remarks'        => 'nullable|string|max:1000',
        ];
    }
}
