<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BbsuInputLot extends Model
{
    protected $table = 'bbsu_input_lots';

    protected $fillable = [
        'bbsu_header_id',
        'lot_number',
        'pallet_no',
        'acid_test_detail_id',
        'ulab_type',
        'ulab_description',
        'unit',
        'available_qty',
        'assigned_qty',
        'acid_pct',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'available_qty' => 'decimal:3',
        'assigned_qty'  => 'decimal:3',
        'acid_pct'      => 'decimal:2',
    ];

    public function bbsuHeader()
    {
        return $this->belongsTo(BbsuHeader::class, 'bbsu_header_id');
    }

    public function acidTestDetail()
    {
        return $this->belongsTo(AcidTestPercentageDetail::class, 'acid_test_detail_id');
    }
}