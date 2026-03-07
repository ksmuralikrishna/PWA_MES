<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmeltingFluxChemical extends Model
{
    use SoftDeletes;

    protected $table = 'smelting_flux_chemicals';

    protected $fillable = [
        'smelting_batch_id',
        'chemical_id',
        'qty',
        'status',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'qty'       => 'decimal:3',
        'is_active' => 'boolean',
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(SmeltingBatch::class, 'smelting_batch_id');
    }

    public function chemical(): BelongsTo
    {
        return $this->belongsTo(Material::class, 'chemical_id');
    }
}
