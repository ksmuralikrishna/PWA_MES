<?php
// ── app/Models/AcidTestPercentageDetail.php ───────────────────────

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcidTestPercentageDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'acid_test_percentage_details';

    // No timestamps on this table — add timestamps() to migration if needed
    public $timestamps = true;

    protected $fillable = [
        'acid_test_id',
        'pallet_no',
        'gross_weight',
        'net_weight',
        'ulab_type',
        'initial_weight',
        'drained_weight',
        'weight_difference',
        'avg_acid_pct',
        'remarks',
        'status',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'gross_weight'                 => 'decimal:3',
        'net_weight'                   => 'decimal:3',
        'initial_weight'               => 'decimal:3',
        'drained_weight'               => 'decimal:3',
        'weight_difference'            => 'decimal:3',
        'avg_acid_pct'                 => 'decimal:2',
        'is_active'                    => 'boolean',
    ];

    public function acidTest()
    {
        return $this->belongsTo(AcidTesting::class, 'acid_test_id');
    }

    public function stockCondition()
    {
        return $this->belongsTo(AcidStockCondition::class, 'ulab_type', 'stock_code');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by')->select('id', 'name');
    }
}
