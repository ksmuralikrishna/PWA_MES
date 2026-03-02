<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BbsuLotConsumption extends Model
{
    protected $table = 'bbsu_lot_consumption';

    protected $fillable = [
        'acid_test_detail_id',
        'total_assigned',
    ];

    protected $casts = [
        'total_assigned' => 'decimal:3',
    ];

    public function acidTestDetail()
    {
        return $this->belongsTo(AcidTestPercentageDetail::class, 'acid_test_detail_id');
    }
}