<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmeltingTemperatureRecord extends Model
{
    use SoftDeletes;

    protected $table = 'smelting_temperature_records';

    protected $fillable = [
        'smelting_batch_id',
        'record_time',
        'inside_temp_before_charging',
        'process_gas_chamber_temp',
        'shell_temp',
        'bag_house_temp',
        'status',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'record_time'                  => 'datetime',
        'inside_temp_before_charging'  => 'decimal:2',
        'process_gas_chamber_temp'     => 'decimal:2',
        'is_active'                    => 'boolean',
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(SmeltingBatch::class, 'smelting_batch_id');
    }
}