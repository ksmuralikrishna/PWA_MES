<?php
// ── app/Models/AcidStockCondition.php ────────────────────────────

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcidStockCondition extends Model
{
    use HasFactory;

    protected $table = 'acid_stock_conditions';

    public $timestamps = false;

    protected $fillable = [
        'stock_code',
        'description',
        'min_pct',
        'max_pct',
        'status',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'min_pct'   => 'decimal:2',
        'max_pct'   => 'decimal:2',
        'is_active' => 'boolean',
    ];
}
