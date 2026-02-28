<?php
// ── app/Http/Controllers/Api/SupplierController.php ──────────────

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $suppliers = Supplier::query()
            ->when($request->search, fn($q) => $q->where(function ($q) use ($request) {
                $q->where('supplier_name', 'like', "%{$request->search}%")
                  ->orWhere('supplier_code', 'like', "%{$request->search}%");
            }))
            ->when($request->is_active !== null, fn($q) => $q->where('is_active', $request->boolean('is_active')))
            ->orderBy('supplier_name')
            ->paginate($request->per_page ?? 50);

        return response()->json(['status' => 'ok', 'data' => $suppliers]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_code'       => 'required|string|max:50|unique:suppliers,supplier_code',
            'supplier_name'       => 'required|string|max:255',
            'facts_supplier_code' => 'nullable|string|max:50',
            'supplier_address'    => 'nullable|string|max:500',
            'contact_number'      => 'nullable|string|max:20',
            'supplier_email'      => 'nullable|email|max:100',
            'is_active'           => 'boolean',
        ]);

        $supplier = Supplier::create(array_merge(
            $request->only(['supplier_code', 'supplier_name', 'facts_supplier_code', 'supplier_address', 'contact_number', 'supplier_email', 'is_active']),
            ['created_by' => auth()->id(), 'updated_by' => auth()->id()]
        ));

        return response()->json(['status' => 'ok', 'message' => 'Supplier created.', 'data' => $supplier], 201);
    }

    public function show($id)
    {
        return response()->json(['status' => 'ok', 'data' => Supplier::findOrFail($id)]);
    }

    public function update(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);

        $request->validate([
            'supplier_code'       => ['sometimes', 'required', 'string', 'max:50', Rule::unique('suppliers')->ignore($supplier->id)],
            'supplier_name'       => 'sometimes|required|string|max:255',
            'facts_supplier_code' => 'nullable|string|max:50',
            'supplier_address'    => 'nullable|string|max:500',
            'contact_number'      => 'nullable|string|max:20',
            'supplier_email'      => 'nullable|email|max:100',
            'is_active'           => 'boolean',
        ]);

        $supplier->update(array_merge(
            $request->only(['supplier_code', 'supplier_name', 'facts_supplier_code', 'supplier_address', 'contact_number', 'supplier_email', 'is_active']),
            ['updated_by' => auth()->id()]
        ));

        return response()->json(['status' => 'ok', 'message' => 'Supplier updated.', 'data' => $supplier]);
    }

    public function destroy($id)
    {
        $supplier = Supplier::findOrFail($id);

        if ($supplier->receivings()->count() > 0) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Cannot delete — supplier has receiving records.',
            ], 422);
        }

        $supplier->delete();
        return response()->json(['status' => 'ok', 'message' => 'Supplier deleted.']);
    }
}
