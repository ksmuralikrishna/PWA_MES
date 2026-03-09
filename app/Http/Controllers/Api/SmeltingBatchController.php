<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SmeltingBatch;
use App\Models\SmeltingRawMaterial;
use App\Models\SmeltingFluxChemical;
use App\Models\SmeltingProcessDetail;
use App\Models\SmeltingTemperatureRecord;
use App\Models\SmeltingOutputBlock;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SmeltingBatchController extends Controller
{
    // ══════════════════════════════════════════════════════════════════
    // INDEX  GET /api/smelting-batches
    // ══════════════════════════════════════════════════════════════════
    public function index(Request $request): JsonResponse
    {
        $query = SmeltingBatch::with([
            'rawMaterials',
            'fluxChemicals',
            'processDetails',
            'temperatureRecords',
            'outputBlocks',
        ])->where('is_active', true);

        if ($request->filled('status'))
            $query->where('status', $request->status);
        if ($request->filled('rotary_no'))
            $query->where('rotary_no', $request->rotary_no);
        if ($request->filled('date_from'))
            $query->whereDate('date', '>=', $request->date_from);
        if ($request->filled('date_to'))
            $query->whereDate('date', '<=', $request->date_to);
        if ($request->filled('search'))
            $query->where('batch_no', 'like', '%' . $request->search . '%');

        $batches = $query->orderByDesc('created_at')
            ->paginate($request->get('per_page', 20));

        // Stats for index page cards
        $stats = [
            'total' => SmeltingBatch::where('is_active', true)->count(),
            'draft' => SmeltingBatch::where('is_active', true)->where('status', 'draft')->count(),
            'submitted' => SmeltingBatch::where('is_active', true)->where('status', 'submitted')->count(),
            'this_month' => SmeltingBatch::where('is_active', true)->whereMonth('date', now()->month)->count(),
        ];

        return response()->json(['status' => 'ok', 'data' => $batches, 'stats' => $stats]);
    }

    // ══════════════════════════════════════════════════════════════════
    // GENERATE BATCH NO  GET /api/smelting-batches/generate-batch-no
    // Returns next auto number: SMT-2026-0001
    // ══════════════════════════════════════════════════════════════════
    public function generateBatchNo(): JsonResponse
    {
        $year = now()->format('Y');
        $prefix = 'SMT-' . $year . '-';

        $last = SmeltingBatch::where('batch_no', 'like', $prefix . '%')
            ->orderByDesc('batch_no')
            ->value('batch_no');

        $next = $last ? (int) substr($last, strlen($prefix)) + 1 : 1;
        $batchNo = $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);

        return response()->json(['status' => 'ok', 'batch_no' => $batchNo]);
    }

    // ══════════════════════════════════════════════════════════════════
    // STORE  POST /api/smelting-batches
    // ══════════════════════════════════════════════════════════════════
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'batch_no' => 'required|string|unique:smelting_batches,batch_no',
            'rotary_no' => 'required|integer|in:1,2',
            'date' => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
        ]);

        try {
            DB::beginTransaction();
            $userId = auth()->id();

            $batch = SmeltingBatch::create([
                'batch_no' => $request->batch_no,
                'rotary_no' => $request->rotary_no,
                'date' => $request->date,
                'start_time' => $request->date . ' ' . ($request->start_time ?? '00:00') . ':00',
                'end_time' => $request->end_time ? $request->date . ' ' . $request->end_time . ':00' : null,
                'lpg_consumption' => $request->lpg_consumption,
                'o2_consumption' => $request->o2_consumption,
                'id_fan_initial' => $request->id_fan_initial,
                'id_fan_final' => $request->id_fan_final,
                'id_fan_consumption' => $this->calcDiff($request->id_fan_final, $request->id_fan_initial),
                'rotary_power_initial' => $request->rotary_power_initial,
                'rotary_power_final' => $request->rotary_power_final,
                'rotary_power_consumption' => $this->calcDiff($request->rotary_power_final, $request->rotary_power_initial),
                'output_material' => $request->output_material,
                'output_qty' => $request->output_qty,
                'status' => 0,
                'is_active' => true,
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);

            $this->saveChildren($batch, $request, $userId);

            DB::commit();

            return response()->json([
                'status' => 'ok',
                'message' => 'Smelting batch created.',
                'data' => $batch->load(['rawMaterials', 'fluxChemicals', 'processDetails', 'temperatureRecords', 'outputBlocks']),
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Smelting store failed', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // ══════════════════════════════════════════════════════════════════
    // SHOW  GET /api/smelting-batches/{id}
    // ══════════════════════════════════════════════════════════════════
    public function show($id): JsonResponse
    {
        $batch = SmeltingBatch::with([
            'rawMaterials',
            'fluxChemicals',
            'processDetails',
            'temperatureRecords',
            'outputBlocks',
        ])->findOrFail($id);

        return response()->json(['status' => 'ok', 'data' => $batch]);
    }

    // ══════════════════════════════════════════════════════════════════
    // UPDATE  PUT /api/smelting-batches/{id}
    // ══════════════════════════════════════════════════════════════════
    public function update(Request $request, $id): JsonResponse
    {
        $batch = SmeltingBatch::findOrFail($id);

        if ($batch->status === 'submitted') {
            return response()->json(['status' => 'error', 'message' => 'Batch already submitted.'], 422);
        }

        try {
            DB::beginTransaction();
            $userId = auth()->id();

            $batch->update([
                'rotary_no' => $request->rotary_no ?? $batch->rotary_no,
                'date' => $request->date ?? $batch->date,
                'start_time' => $request->filled('start_time')
                    ? ($request->date ?? $batch->date->format('Y-m-d')) . ' ' . $request->start_time . ':00'
                    : $batch->start_time,
                'end_time' => $request->filled('end_time')
                    ? ($request->date ?? $batch->date->format('Y-m-d')) . ' ' . $request->end_time . ':00'
                    : $batch->end_time,
                'lpg_consumption' => $request->lpg_consumption ?? $batch->lpg_consumption,
                'o2_consumption' => $request->o2_consumption ?? $batch->o2_consumption,
                'id_fan_initial' => $request->id_fan_initial ?? $batch->id_fan_initial,
                'id_fan_final' => $request->id_fan_final ?? $batch->id_fan_final,
                'id_fan_consumption' => $this->calcDiff($request->id_fan_final ?? $batch->id_fan_final, $request->id_fan_initial ?? $batch->id_fan_initial),
                'rotary_power_initial' => $request->rotary_power_initial ?? $batch->rotary_power_initial,
                'rotary_power_final' => $request->rotary_power_final ?? $batch->rotary_power_final,
                'rotary_power_consumption' => $this->calcDiff($request->rotary_power_final ?? $batch->rotary_power_final, $request->rotary_power_initial ?? $batch->rotary_power_initial),
                'output_material' => $request->output_material ?? $batch->output_material,
                'output_qty' => $request->output_qty ?? $batch->output_qty,
                'updated_by' => $userId,
            ]);

            $this->saveChildren($batch, $request, $userId, delete: true);

            DB::commit();

            return response()->json([
                'status' => 'ok',
                'message' => 'Batch updated.',
                'data' => $batch->fresh(['rawMaterials', 'fluxChemicals', 'processDetails', 'temperatureRecords', 'outputBlocks']),
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Smelting update failed', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // ══════════════════════════════════════════════════════════════════
    // AUTOSAVE  POST /api/smelting-batches/{id}/autosave
    // ══════════════════════════════════════════════════════════════════
    public function autosave(Request $request, $id): JsonResponse
    {
        $batch = SmeltingBatch::findOrFail($id);

        if ($batch->status === 'submitted') {
            return response()->json(['status' => 'error', 'message' => 'Already submitted.'], 400);
        }

        try {
            DB::beginTransaction();
            $userId = auth()->id();

            // Update header fields that are present
            $headerFields = [
                'rotary_no',
                'date',
                'start_time',
                'end_time',
                'lpg_consumption',
                'o2_consumption',
                'id_fan_initial',
                'id_fan_final',
                'rotary_power_initial',
                'rotary_power_final',
                'output_material',
                'output_qty'
            ];

            $updates = ['updated_by' => $userId];
            foreach ($headerFields as $f) {
                if ($request->filled($f))
                    $updates[$f] = $request->input($f);
            }

            // Recalculate consumptions if relevant fields provided
            $idFanFinal = $request->id_fan_final ?? $batch->id_fan_final;
            $idFanInitial = $request->id_fan_initial ?? $batch->id_fan_initial;
            $rpFinal = $request->rotary_power_final ?? $batch->rotary_power_final;
            $rpInitial = $request->rotary_power_initial ?? $batch->rotary_power_initial;

            $updates['id_fan_consumption'] = $this->calcDiff($idFanFinal, $idFanInitial);
            $updates['rotary_power_consumption'] = $this->calcDiff($rpFinal, $rpInitial);

            $batch->update($updates);
            $this->saveChildren($batch, $request, $userId, delete: true);

            DB::commit();

            return response()->json(['status' => 'ok', 'saved_at' => now()->format('H:i:s')]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // ══════════════════════════════════════════════════════════════════
    // SUBMIT  POST /api/smelting-batches/{id}/submit
    // ══════════════════════════════════════════════════════════════════
    public function submit($id): JsonResponse
    {
        $batch = SmeltingBatch::findOrFail($id);

        if ($batch->status === 1) {
            return response()->json(['status' => 'error', 'message' => 'Already submitted.'], 422);
        }

        $batch->update(['status' => 1, 'updated_by' => auth()->id()]);

        return response()->json(['status' => 'ok', 'message' => 'Batch submitted and locked.']);
    }
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|integer|in:0,1,2,3,4',
        ]);

        $receiving = SmeltingBatch::findOrFail($id);

        // Prevent cancelling if already in downstream process
        if ($request->status == 4 && $receiving->status >= 2) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Cannot cancel — lot is already in downstream processing.',
            ], 422);
        }

        $receiving->update([
            'status'     => $request->status,
            'updated_by' => auth()->id(),
        ]);

        return response()->json([
            'status'  => 'ok',
            'message' => 'Status updated successfully.',
            'data'    => ['status' => $receiving->status],
        ]);
    }
    // ══════════════════════════════════════════════════════════════════
    // DESTROY  DELETE /api/smelting-batches/{id}
    // ══════════════════════════════════════════════════════════════════
    public function destroy($id): JsonResponse
    {
        $batch = SmeltingBatch::findOrFail($id);

        if ($batch->status === 'submitted') {
            return response()->json(['status' => 'error', 'message' => 'Cannot delete submitted batch.'], 422);
        }

        $batch->update(['is_active' => false, 'updated_by' => auth()->id()]);

        return response()->json(['status' => 'ok', 'message' => 'Batch deleted.']);
    }

    // ══════════════════════════════════════════════════════════════════
    // PRIVATE HELPERS
    // ══════════════════════════════════════════════════════════════════

    private function calcDiff($final, $initial): ?float
    {
        if (is_numeric($final) && is_numeric($initial)) {
            $diff = (float) $final - (float) $initial;
            return $diff >= 0 ? round($diff, 3) : null;
        }
        return null;
    }

    private function saveChildren(SmeltingBatch $batch, Request $request, int $userId, bool $delete = false): void
    {
        // ── Raw Materials ─────────────────────────────────────────────
        if ($request->has('raw_materials')) {
            if ($delete)
                SmeltingRawMaterial::where('smelting_batch_id', $batch->id)->delete();
            foreach ($request->raw_materials ?? [] as $row) {
                if (empty($row['raw_material_id']))
                    continue;
                $qty = (float) ($row['raw_material_qty'] ?? 0);
                $yieldPct = (float) ($row['raw_material_yield_pct'] ?? 0);
                SmeltingRawMaterial::create([
                    'smelting_batch_id' => $batch->id,
                    'raw_material_id' => $row['raw_material_id'],
                    'bbsu_batch_id' => $row['bbsu_batch_id'] ?? null,
                    'bbsu_batch_no' => $row['bbsu_batch_no'] ?? null,
                    'raw_material_qty' => $qty,
                    'raw_material_yield_pct' => $yieldPct,
                    'expected_output_qty' => $yieldPct > 0 ? round($qty * $yieldPct / 100, 3) : 0,
                    'is_active' => true,
                    'status' => 0,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]);
            }
        }

        // ── Flux Chemicals ────────────────────────────────────────────
        if ($request->has('flux_chemicals')) {
            if ($delete)
                SmeltingFluxChemical::where('smelting_batch_id', $batch->id)->delete();
            foreach ($request->flux_chemicals ?? [] as $row) {
                if (empty($row['chemical_id']))
                    continue;
                SmeltingFluxChemical::create([
                    'smelting_batch_id' => $batch->id,
                    'chemical_id' => $row['chemical_id'],
                    'qty' => $row['qty'] ?? 0,
                    'is_active' => true,
                    'status' => 0,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]);
            }
        }

        // ── Process Details ───────────────────────────────────────────
        if ($request->has('process_details')) {
            if ($delete)
                SmeltingProcessDetail::where('smelting_batch_id', $batch->id)->delete();
            foreach ($request->process_details ?? [] as $row) {
                if (empty($row['process_name']))
                    continue;
                $totalTime = 0;
                if (!empty($row['start_time']) && !empty($row['end_time'])) {
                    try {
                        $start = \Carbon\Carbon::parse($row['start_time']);
                        $end = \Carbon\Carbon::parse($row['end_time']);
                        $totalTime = max(0, round($end->diffInMinutes($start, false) * -1, 2));
                        if ($totalTime <= 0)
                            $totalTime = round($end->diffInMinutes($start), 2);
                    } catch (\Exception $e) {
                        $totalTime = 0;
                    }
                }
                SmeltingProcessDetail::create([
                    'smelting_batch_id' => $batch->id,
                    'process_name' => $row['process_name'],
                    'start_time' => $row['start_time'] ?? null,
                    'end_time' => $row['end_time'] ?? null,
                    'total_time' => $totalTime,
                    'firing_mode' => $row['firing_mode'] ?? null,
                    'is_active' => true,
                    'status' => 0,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]);
            }
        }

        // ── Temperature Records ───────────────────────────────────────
        if ($request->has('temperature_records')) {
            if ($delete)
                SmeltingTemperatureRecord::where('smelting_batch_id', $batch->id)->delete();
            foreach ($request->temperature_records ?? [] as $row) {
                SmeltingTemperatureRecord::create([
                    'smelting_batch_id' => $batch->id,
                    'record_time' => $row['record_time'] ?? null,
                    'inside_temp_before_charging' => $row['inside_temp_before_charging'] ?? null,
                    'process_gas_chamber_temp' => $row['process_gas_chamber_temp'] ?? null,
                    'shell_temp' => $row['shell_temp'] ?? null,
                    'bag_house_temp' => $row['bag_house_temp'] ?? null,
                    'is_active' => true,
                    'status' => 0,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]);
            }
        }

        // ── Output Blocks ─────────────────────────────────────────────
        if ($request->has('output_blocks')) {
            if ($delete)
                SmeltingOutputBlock::where('smelting_batch_id', $batch->id)->delete();
            foreach ($request->output_blocks ?? [] as $row) {
                if (empty($row['material_id']))
                    continue;
                SmeltingOutputBlock::create([
                    'smelting_batch_id' => $batch->id,
                    'material_id' => $row['material_id'],
                    'block_sl_no' => $row['block_sl_no'] ?? 0,
                    'block_weight' => $row['block_weight'] ?? 0,
                    'is_active' => true,
                    'status' => 0,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]);
            }
        }
    }

    // ══════════════════════════════════════════════════════════════════
    // MATERIALS LIST  GET /api/materials
    // Returns SELECT * FROM materials (items table) for dropdowns
    // ══════════════════════════════════════════════════════════════════
    public function getMaterials(Request $request): JsonResponse
    {
        $query = DB::table('materials');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $perPage = (int) $request->get('per_page', 500);
        $materials = $query->select('id', 'name', 'unit')->orderBy('name')->paginate($perPage);

        return response()->json(['status' => 'ok', 'data' => $materials]);
    }

    // ══════════════════════════════════════════════════════════════════
    // BBSU LOTS FOR MATERIAL  GET /api/smelting-batches/bbsu-lots/{materialId}
    //
    // Returns all submitted BBSU batches where the given material_id
    // is the output_material. For each batch it computes:
    //   output_qty        — total output qty recorded in that BBSU batch
    //   already_used_qty  — sum of raw_material_qty already assigned to
    //                       ANY smelting batch from this BBSU batch
    //   available_qty     — output_qty - already_used_qty
    //
    // The optional ?exclude_smelting_id=X param excludes the current
    // smelting batch from the "already used" calculation so that when
    // editing a draft the user sees the full picture.
    // ══════════════════════════════════════════════════════════════════
    public function getBbsuLots(Request $request, int $materialId): JsonResponse
    {
        $excludeSmeltingId = $request->get('exclude_smelting_id');

        // All submitted BBSU batches whose output_material matches
        $bbsuBatches = DB::table('bbsu_batches as bb')
            ->join('bbsu_output_materials as bom', 'bom.bbsu_batch_id', '=', 'bb.id')
            ->join('materials as mat', 'mat.id', '=', 'bom.output_material_id') // adjust column name if different
            ->where('bom.output_material_id', $materialId)
            ->where('bb.status', 'submitted')
            ->where('bb.is_active', true)
            ->select(
                'bb.id',
                'bb.batch_no',
                'bom.output_material_id as output_material',
                DB::raw('SUM(bom.qty) as output_qty'),
                'mat.material_name as material_name',
                'mat.unit as material_unit'
            )
            ->groupBy('bb.id', 'bb.batch_no', 'bom.output_material_id', 'mat.material_name', 'mat.unit')
            ->orderByDesc('bb.created_at')
            ->get();

        if ($bbsuBatches->isEmpty()) {
            return response()->json([
                'status' => 'ok',
                'data' => [],
                'message' => 'No BBSU batches found for this material.',
            ]);
        }

        $bbsuIds = $bbsuBatches->pluck('id')->toArray();

        // How much has already been consumed from each BBSU batch
        // across ALL smelting batches (optionally excluding current draft)
        $usedQuery = DB::table('smelting_raw_materials as srm')
            ->join('smelting_batches as sb', 'sb.id', '=', 'srm.smelting_batch_id')
            ->whereIn('srm.bbsu_batch_id', $bbsuIds)
            ->where('sb.is_active', true)
            ->where('srm.is_active', true);

        if ($excludeSmeltingId) {
            $usedQuery->where('srm.smelting_batch_id', '!=', $excludeSmeltingId);
        }

        $usedMap = $usedQuery
            ->groupBy('srm.bbsu_batch_id')
            ->select('srm.bbsu_batch_id', DB::raw('SUM(srm.raw_material_qty) as used_qty'))
            ->pluck('used_qty', 'bbsu_batch_id');

        // Build result
        $result = $bbsuBatches->map(function ($b) use ($usedMap) {
            $outputQty = (float) $b->output_qty;
            $usedQty = (float) ($usedMap[$b->id] ?? 0);
            $availableQty = max(0, $outputQty - $usedQty);

            return [
                'bbsu_batch_id' => $b->id,
                'batch_no' => $b->batch_no,
                'material_id' => $b->output_material,
                'material_name' => $b->material_name,
                'material_unit' => $b->material_unit ?? 'KG',
                'output_qty' => round($outputQty, 3),
                'already_used_qty' => round($usedQty, 3),
                'available_qty' => round($availableQty, 3),
            ];
        })->filter(fn($b) => $b['available_qty'] > 0 || true) // show all, even 0 available
            ->values();

        return response()->json(['status' => 'ok', 'data' => $result]);
    }
}