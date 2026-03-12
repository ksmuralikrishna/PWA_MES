<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplierBatchController extends Controller
{
    // GET /api/suppliers
    public function index(Request $request)
    {
        $q = Supplier::where('is_active', true);

        if ($request->filled('status'))
            $q->where('status', $request->status);

        if ($request->filled('search'))
            $q->where(function ($sq) use ($request) {
                $sq->where('supplier_name', 'like', '%' . $request->search . '%')
                    ->orWhere('supplier_code', 'like', '%' . $request->search . '%')
                    ->orWhere('facts_supplier_code', 'like', '%' . $request->search . '%')
                    ->orWhere('contact_number', 'like', '%' . $request->search . '%');
            });

        $data = $q->with('createdBy')
            ->orderByDesc('created_at')
            ->paginate($request->input('per_page', 20));

        return response()->json(['status' => 'ok', 'data' => $data]);
    }

    // GET /api/suppliers/{id}
    public function show($id)
    {
        $supplier = Supplier::with('createdBy', 'updatedBy')
            ->where('is_active', true)
            ->findOrFail($id);
        return response()->json(['status' => 'ok', 'data' => $supplier]);
    }

    // POST /api/suppliers  (also handles HTML form submission)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_code' => 'required|string|max:100|unique:suppliers,supplier_code',
            'supplier_name' => 'required|string|max:255',
            'facts_supplier_code' => 'nullable|string|max:100',
            'supplier_address' => 'nullable|string',
            'contact_number' => 'nullable|string|max:50',
            'supplier_email' => 'nullable|email|max:255',
            'status' => 'nullable|string|in:1,0',
        ]);

        $supplier = Supplier::create(array_merge($validated, [
            'status' => $validated['status'] ?? 1,
            'is_active' => true,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]));

        // if ($request->expectsJson()) {
            return response()->json(['status' => 'ok', 'data' => $supplier], 201);
        // }

        // return redirect()->route('admin.mes.suppliers.edit', $supplier->id)
        //     ->with('success', 'Supplier created successfully.');
    }

    // PUT /api/suppliers/{id}  (also handles HTML form submission)
    public function update(Request $request, $id)
    {
        $supplier = Supplier::where('is_active', true)->findOrFail($id);

        $validated = $request->validate([
            'supplier_code' => 'required|string|max:100|unique:suppliers,supplier_code,' . $id,
            'supplier_name' => 'required|string|max:255',
            'facts_supplier_code' => 'nullable|string|max:100',
            'supplier_address' => 'nullable|string',
            'contact_number' => 'nullable|string|max:50',
            'supplier_email' => 'nullable|email|max:255',
            'status' => 'nullable|string|in:1,0',
        ]);

        $supplier->update(array_merge($validated, [
            'updated_by' => Auth::id(),
        ]));

        // if ($request->expectsJson()) {
            return response()->json(['status' => 'ok', 'data' => $supplier]);
        // }

        // return redirect()->route('admin.mes.suppliers.edit', $supplier->id)
        //     ->with('success', 'Supplier updated successfully.');
    }

    // DELETE /api/suppliers/{id}  (also handles HTML form submission)
    public function destroy(Request $request, $id)
    {
        $supplier = Supplier::where('is_active', true)->findOrFail($id);
        $supplier->update(['is_active' => false, 'updated_by' => Auth::id()]);

        // if ($request->expectsJson()) {
            return response()->json(['status' => 'ok', 'message' => 'Supplier deleted.']);
        // }

        // return redirect()->route('admin.mes.supplier.index')
        //     ->with('success', 'Supplier deleted successfully.');
    }
}