<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BbsuHeader extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'bbsu_headers';   // ← must be exactly this

    protected $fillable = [
        'doc_no', 'date', 'start_time', 'end_time', 'category',
        'total_input', 'avg_acid_pct', 'initial_power', 'final_power',
        'total_power_consumption', 'total_output', 'yield',
        'status', 'is_active', 'created_by', 'updated_by',
    ];

    protected $casts = [
        'date'                    => 'date',
        'start_time'              => 'datetime',
        'end_time'                => 'datetime',
        'total_input'             => 'decimal:3',
        'avg_acid_pct'            => 'decimal:2',
        'initial_power'           => 'decimal:2',
        'final_power'             => 'decimal:2',
        'total_power_consumption' => 'decimal:2',
        'total_output'            => 'decimal:3',
        'yield'                   => 'decimal:2',
        'is_active'               => 'boolean',
        'status'                  => 'integer',
    ];

    const STATUS_DRAFT       = 0;
    const STATUS_IN_PROGRESS = 1;
    const STATUS_COMPLETED   = 2;
    const STATUS_CANCELLED   = 3;

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            0 => 'Draft',
            1 => 'In Progress',
            2 => 'Completed',
            3 => 'Cancelled',
            default => 'Unknown',
        };
    }

    protected $appends = ['status_label'];

    public function inputLots()
    {
        return $this->hasMany(BbsuInputLot::class, 'bbsu_header_id');
    }

    public function outputs()
    {
        return $this->hasMany(BbsuOutput::class, 'bbsu_header_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by')->select('id', 'name');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by')->select('id', 'name');
    }
}