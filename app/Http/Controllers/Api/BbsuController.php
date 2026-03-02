<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AcidStockCondition;
use App\Models\AcidTestPercentageDetail;
use App\Models\AcidTesting;
use App\Models\BbsuHeader;
use App\Models\BbsuInputLot;
use App\Models\BbsuLotConsumption;
use App\Models\BbsuOutput;
use App\Models\Receiving;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class BbsuController extends Controller
{
    const OUTPUT_MATERIALS = [
        'METALLIC', 'PASTE', 'FINES', 'PP CHIPS',
        'ABS CHIPS', 'SEPARATOR', 'BATTERY PLATES',
        'TERMINALS', 'ACID', 'TOTAL',
    ];

    /**
     * GET /api/bbsu
     */
    public function index(Request $request)
    {
        $records = BbsuHeader::with(['createdBy', 'updatedBy'])
            ->when($request->category,        fn($q) => $q->where('category', $request->category))
            ->when($request->status !== null, fn($q) => $q->where('status', $request->status))
            ->when($request->date_from,       fn($q) => $q->whereDate('date', '>=', $request->date_from))
            ->when($request->date_to,         fn($q) => $q->whereDate('date', '<=', $request->date_to))
            ->when($request->doc_no,          fn($q) => $q->where('doc_no', 'like', "%{$request->doc_no}%"))
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 20);

        return response()->json(['status' => 'ok', 'data' => $records]);
    }

    /**
     * GET /api/bbsu/generate-doc-no
     */
    public function generateDocNo()
    {
        $today  = Carbon::today();
        $prefix = 'BBSU-' . $today->format('Ymd') . '-';

        $last = BbsuHeader::withTrashed()
            ->where('doc_no', 'like', $prefix . '%')
            ->orderBy('doc_no', 'desc')
            ->value('doc_no');

        $nextSeq = $last ? (int) substr($last, -3) + 1 : 1;

        return response()->json([
            'status' => 'ok',
            'data'   => ['doc_no' => $prefix . str_pad($nextSeq, 3, '0', STR_PAD_LEFT)],
        ]);
    }

    /**
     * GET /api/bbsu/output-materials
     */
    public function outputMaterials()
    {
        return response()->json([
            'status' => 'ok',
            'data'   => self::OUTPUT_MATERIALS,
        ]);
    }

    /**
     * GET /api/bbsu/available-lots
     */
    public function availableLots(Request $request)
    {
        $details = AcidTestPercentageDetail::with([
            'acidTest'         => fn($q) => $q->select('id', 'lot_number', 'supplier_id'),
            'acidTest.supplier'=> fn($q) => $q->select('id', 'supplier_name'),
        ])
        ->when($request->lot_number, fn($q) => $q->whereHas('acidTest',
            fn($q) => $q->where('lot_number', 'like', "%{$request->lot_number}%")
        ))
        ->get();

        $consumptions    = BbsuLotConsumption::pluck('total_assigned', 'acid_test_detail_id');
        $lotUnits        = Receiving::pluck('unit', 'lot_no');
        $stockConditions = AcidStockCondition::pluck('description', 'stock_code');

        $result     = [];
        $acidAdded  = [];

        foreach ($details as $detail) {
            $lotNumber = $detail->acidTest->lot_number ?? null;

            if ($detail->ulab_type == '5') {
                $key = $lotNumber . '_acid';
                if (isset($acidAdded[$key])) continue;

                $acidRows      = AcidTestPercentageDetail::where('acid_test_id', $detail->acid_test_id)->where('ulab_type', '5')->get();
                $totalNet      = $acidRows->sum('net_weight');
                $totalConsumed = BbsuLotConsumption::whereIn('acid_test_detail_id', $acidRows->pluck('id'))->sum('total_assigned');
                $available     = max(0, $totalNet - $totalConsumed);
                $avgAcidPct    = $acidRows->avg('avg_acid_pct');

                $acidAdded[$key] = true;
                $result[] = [
                    'acid_test_detail_id' => $detail->id,
                    'acid_test_id'        => $detail->acid_test_id,
                    'lot_number'          => $lotNumber,
                    'pallet_no'           => $detail->pallet_no,
                    'ulab_type'           => '5',
                    'ulab_description'    => 'Acid',
                    'unit'                => $lotUnits[$lotNumber] ?? 'KG',
                    'net_weight'          => $totalNet,
                    'available_qty'       => $available,
                    'acid_pct'            => round($avgAcidPct, 2),
                    'supplier_name'       => $detail->acidTest->supplier->supplier_name ?? null,
                ];
            } else {
                $consumed  = $consumptions[$detail->id] ?? 0;
                $available = max(0, $detail->net_weight - $consumed);

                if ($available <= 0) continue;

                $result[] = [
                    'acid_test_detail_id' => $detail->id,
                    'acid_test_id'        => $detail->acid_test_id,
                    'lot_number'          => $lotNumber,
                    'pallet_no'           => $detail->pallet_no,
                    'ulab_type'           => $detail->ulab_type,
                    'ulab_description'    => $stockConditions[$detail->ulab_type] ?? 'Unknown (' . $detail->ulab_type . ')',
                    'unit'                => $lotUnits[$lotNumber] ?? 'KG',
                    'net_weight'          => $detail->net_weight,
                    'available_qty'       => $available,
                    'acid_pct'            => $detail->avg_acid_pct,
                    'supplier_name'       => $detail->acidTest->supplier->supplier_name ?? null,
                ];
            }
        }

        return response()->json(['status' => 'ok', 'data' => array_values($result)]);
    }

    /**
     * POST /api/bbsu
     */
    public function store(Request $request)
    {
        $isBbsu = $request->category === 'BBSU';

        $request->validate([
            'doc_no'       => 'required|string|unique:bbsu_headers,doc_no',
            'date'         => 'required|date',
            'start_time'   => 'required|date_format:Y-m-d H:i:s',
            'end_time'     => 'nullable|date_format:Y-m-d H:i:s|after:start_time',
            'category'     => 'required|in:BBSU,Manual Cutting',
            'total_input'  => 'required|numeric|min:0',
            'avg_acid_pct' => 'required|numeric|min:0|max:100',
            'initial_power'=> ($isBbsu ? 'required' : 'nullable') . '|numeric|min:0',
            'final_power'  => ($isBbsu ? 'required' : 'nullable') . '|numeric|min:0',
            'total_output' => 'required|numeric|min:0',
            'yield'        => 'required|numeric|min:0|max:100',

            'input_lots'                       => 'required|array|min:1',
            'input_lots.*.acid_test_detail_id' => 'required|integer|exists:acid_test_percentage_details,id',
            'input_lots.*.lot_number'          => 'required|string',
            'input_lots.*.pallet_no'           => 'required|integer',
            'input_lots.*.ulab_type'           => 'required|string',
            'input_lots.*.ulab_description'    => 'nullable|string',
            'input_lots.*.unit'                => 'nullable|string',
            'input_lots.*.available_qty'       => 'required|numeric|min:0',
            'input_lots.*.assigned_qty'        => 'required|numeric|min:0.001',
            'input_lots.*.acid_pct'            => 'required|numeric|min:0',

            'outputs'                  => 'required|array|min:1',
            'outputs.*.material_name'  => 'required|string|max:100',
            'outputs.*.quantity'       => 'required|numeric|min:0',
            'outputs.*.yield'          => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Validate assigned qty doesn't exceed available
            foreach ($request->input_lots as $lot) {
                $detail   = AcidTestPercentageDetail::findOrFail($lot['acid_test_detail_id']);
                $consumed = BbsuLotConsumption::where('acid_test_detail_id', $detail->id)->value('total_assigned') ?? 0;
                $available = $detail->net_weight - $consumed;

                if ($lot['assigned_qty'] > $available) {
                    DB::rollBack();
                    return response()->json([
                        'status'  => 'error',
                        'message' => "Assigned qty ({$lot['assigned_qty']}) exceeds available qty ({$available}) for lot {$lot['lot_number']} pallet {$lot['pallet_no']}.",
                    ], 422);
                }
            }

            $powerConsumption = null;
            if ($request->filled('initial_power') && $request->filled('final_power')) {
                $powerConsumption = $request->final_power - $request->initial_power;
            }

            $header = BbsuHeader::create([
                'doc_no'                  => $request->doc_no,
                'date'                    => $request->date,
                'start_time'              => $request->start_time,
                'end_time'                => $request->end_time,
                'category'                => $request->category,
                'total_input'             => $request->total_input,
                'avg_acid_pct'            => $request->avg_acid_pct,
                'initial_power'           => $request->initial_power,
                'final_power'             => $request->final_power,
                'total_power_consumption' => $powerConsumption,
                'total_output'            => $request->total_output,
                'yield'                   => $request->yield,
                'status'                  => 1,
                'is_active'               => true,
                'created_by'              => auth()->id(),
                'updated_by'              => auth()->id(),
            ]);

            foreach ($request->input_lots as $lot) {
                BbsuInputLot::create([
                    'bbsu_header_id'      => $header->id,
                    'lot_number'          => $lot['lot_number'],
                    'pallet_no'           => $lot['pallet_no'],
                    'acid_test_detail_id' => $lot['acid_test_detail_id'],
                    'ulab_type'           => $lot['ulab_type'],
                    'ulab_description'    => $lot['ulab_description'] ?? null,
                    'unit'                => $lot['unit'] ?? null,
                    'available_qty'       => $lot['available_qty'],
                    'assigned_qty'        => $lot['assigned_qty'],
                    'acid_pct'            => $lot['acid_pct'],
                    'created_by'          => auth()->id(),
                    'updated_by'          => auth()->id(),
                ]);

                $consumption = BbsuLotConsumption::firstOrNew(['acid_test_detail_id' => $lot['acid_test_detail_id']]);
                $consumption->total_assigned = ($consumption->total_assigned ?? 0) + $lot['assigned_qty'];
                $consumption->save();
            }

            foreach ($request->outputs as $output) {
                BbsuOutput::create([
                    'bbsu_header_id' => $header->id,
                    'material_name'  => strtoupper($output['material_name']),
                    'quantity'       => $output['quantity'],
                    'yield'          => $output['yield'],
                    'created_by'     => auth()->id(),
                    'updated_by'     => auth()->id(),
                ]);
            }

            DB::commit();

            return response()->json([
                'status'  => 'ok',
                'message' => 'BBSU record created successfully.',
                'data'    => $header->load(['inputLots', 'outputs', 'createdBy']),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * GET /api/bbsu/{id}
     */
    public function show($id)
    {
        $record = BbsuHeader::with(['inputLots', 'outputs', 'createdBy', 'updatedBy'])
            ->findOrFail($id);

        return response()->json(['status' => 'ok', 'data' => $record]);
    }

    /**
     * PUT /api/bbsu/{id}
     */
    public function update(Request $request, $id)
    {
        $header = BbsuHeader::findOrFail($id);

        if ($header->status >= 2) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Cannot edit a Completed or Cancelled record.',
            ], 422);
        }

        $isBbsu = ($request->category ?? $header->category) === 'BBSU';

        $request->validate([
            'date'          => 'sometimes|required|date',
            'start_time'    => 'sometimes|required|date_format:Y-m-d H:i:s',
            'end_time'      => 'nullable|date_format:Y-m-d H:i:s|after:start_time',
            'category'      => 'sometimes|required|in:BBSU,Manual Cutting',
            'total_input'   => 'sometimes|required|numeric|min:0',
            'avg_acid_pct'  => 'sometimes|required|numeric|min:0|max:100',
            'initial_power' => ($isBbsu ? 'required' : 'nullable') . '|numeric|min:0',
            'final_power'   => ($isBbsu ? 'required' : 'nullable') . '|numeric|min:0',
            'total_output'  => 'sometimes|required|numeric|min:0',
            'yield'         => 'sometimes|required|numeric|min:0|max:100',

            'outputs'                 => 'sometimes|required|array|min:1',
            'outputs.*.material_name' => 'required|string|max:100',
            'outputs.*.quantity'      => 'required|numeric|min:0',
            'outputs.*.yield'         => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $data = $request->only([
                'date', 'start_time', 'end_time', 'category',
                'total_input', 'avg_acid_pct', 'initial_power',
                'final_power', 'total_output', 'yield',
            ]);

            if ($request->filled('initial_power') && $request->filled('final_power')) {
                $data['total_power_consumption'] = $request->final_power - $request->initial_power;
            }

            $data['updated_by'] = auth()->id();
            $header->update($data);

            if ($request->has('outputs')) {
                BbsuOutput::where('bbsu_header_id', $header->id)->delete();
                foreach ($request->outputs as $output) {
                    BbsuOutput::create([
                        'bbsu_header_id' => $header->id,
                        'material_name'  => strtoupper($output['material_name']),
                        'quantity'       => $output['quantity'],
                        'yield'          => $output['yield'],
                        'created_by'     => auth()->id(),
                        'updated_by'     => auth()->id(),
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'status'  => 'ok',
                'message' => 'BBSU record updated successfully.',
                'data'    => $header->fresh(['inputLots', 'outputs']),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * PATCH /api/bbsu/{id}/status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|integer|in:0,1,2,3']);

        $header = BbsuHeader::findOrFail($id);
        $header->update(['status' => $request->status, 'updated_by' => auth()->id()]);

        return response()->json([
            'status'  => 'ok',
            'message' => 'Status updated.',
            'data'    => ['status' => $header->status, 'status_label' => $header->status_label],
        ]);
    }

    /**
     * DELETE /api/bbsu/{id}
     */
    public function destroy($id)
    {
        $header = BbsuHeader::findOrFail($id);

        if ($header->status === 2) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Cannot delete a Completed record.',
            ], 422);
        }

        DB::beginTransaction();
        try {
            foreach ($header->inputLots as $lot) {
                $consumption = BbsuLotConsumption::where('acid_test_detail_id', $lot->acid_test_detail_id)->first();
                if ($consumption) {
                    $consumption->total_assigned = max(0, $consumption->total_assigned - $lot->assigned_qty);
                    $consumption->save();
                }
            }

            $header->inputLots()->delete();
            $header->outputs()->delete();
            $header->delete();

            DB::commit();

            return response()->json(['status' => 'ok', 'message' => 'BBSU record deleted.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}