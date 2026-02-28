<?php
// ── app/Http/Controllers/Api/AcidStockConditionController.php ────

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AcidStockCondition;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AcidStockConditionController extends Controller
{
    /**
     * GET /api/acid-stock-conditions
     * Used to populate the ulab_type dropdown in the form
     */
    public function index(Request $request)
    {
        $conditions = AcidStockCondition::query()
            ->when($request->is_active !== null,
                fn($q) => $q->where('is_active', $request->boolean('is_active')))
            ->orderBy('stock_code')
            ->get();

        return response()->json(['status' => 'ok', 'data' => $conditions]);
    }

    /**
     * POST /api/acid-stock-conditions
     * Admin creates a new stock condition type
     */
    public function store(Request $request)
    {
        $request->validate([
            'stock_code'  => 'required|string|max:50|unique:acid_stock_conditions,stock_code',
            'description' => 'required|string|max:255',
            'min_pct'     => 'required|numeric|min:0|max:100',
            'max_pct'     => 'required|numeric|min:0|max:100|gte:min_pct',
            'is_active'   => 'boolean',
        ]);

        $condition = AcidStockCondition::create(array_merge(
            $request->only(['stock_code', 'description', 'min_pct', 'max_pct', 'is_active']),
            ['created_by' => auth()->id(), 'updated_by' => auth()->id()]
        ));

        return response()->json([
            'status'  => 'ok',
            'message' => 'Stock condition created.',
            'data'    => $condition,
        ], 201);
    }

    /**
     * PUT /api/acid-stock-conditions/{id}
     */
    public function update(Request $request, $id)
    {
        $condition = AcidStockCondition::findOrFail($id);

        $request->validate([
            'stock_code'  => ['sometimes', 'required', 'string', 'max:50', Rule::unique('acid_stock_conditions')->ignore($condition->id)],
            'description' => 'sometimes|required|string|max:255',
            'min_pct'     => 'sometimes|required|numeric|min:0|max:100',
            'max_pct'     => 'sometimes|required|numeric|min:0|max:100',
            'is_active'   => 'boolean',
        ]);

        $condition->update(array_merge(
            $request->only(['stock_code', 'description', 'min_pct', 'max_pct', 'is_active']),
            ['updated_by' => auth()->id()]
        ));

        return response()->json(['status' => 'ok', 'message' => 'Stock condition updated.', 'data' => $condition]);
    }

    /**
     * DELETE /api/acid-stock-conditions/{id}
     */
    public function destroy($id)
    {
        $condition = AcidStockCondition::findOrFail($id);
        $condition->delete();

        return response()->json(['status' => 'ok', 'message' => 'Stock condition deleted.']);
    }
}
