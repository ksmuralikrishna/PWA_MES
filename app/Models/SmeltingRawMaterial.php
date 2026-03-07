<?php
// ═══════════════════════════════════════════════════════════════════
// app/Models/SmeltingRawMaterial.php
// ═══════════════════════════════════════════════════════════════════
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmeltingRawMaterial extends Model
{
    use SoftDeletes;

    protected $table = 'smelting_raw_materials';

    protected $fillable = [
        'smelting_batch_id',
        'raw_material_id',
        'bbsu_batch_id',
        'bbsu_batch_no',
        'raw_material_qty',
        'raw_material_yield_pct',
        'expected_output_qty',
        'status',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'raw_material_qty'       => 'decimal:3',
        'raw_material_yield_pct' => 'decimal:3',
        'expected_output_qty'    => 'decimal:3',
        'is_active'              => 'boolean',
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(SmeltingBatch::class, 'smelting_batch_id');
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class, 'raw_material_id');
    }
}