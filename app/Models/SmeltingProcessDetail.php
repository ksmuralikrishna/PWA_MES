<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmeltingProcessDetail extends Model
{
    use SoftDeletes;

    protected $table = 'smelting_process_details';

    protected $fillable = [
        'smelting_batch_id',
        'process_name',
        'start_time',
        'end_time',
        'total_time',
        'firing_mode',
        'status',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time'   => 'datetime',
        'total_time' => 'decimal:2',
        'is_active'  => 'boolean',
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(SmeltingBatch::class, 'smelting_batch_id');
    }
}