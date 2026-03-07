<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmeltingOutputBlock extends Model
{
    use SoftDeletes;

    protected $table = 'smelting_output_blocks';

    protected $fillable = [
        'smelting_batch_id',
        'material_id',
        'block_sl_no',
        'block_weight',
        'status',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'block_weight' => 'decimal:3',
        'is_active'    => 'boolean',
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(SmeltingBatch::class, 'smelting_batch_id');
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class, 'material_id');
    }
}