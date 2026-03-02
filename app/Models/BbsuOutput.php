<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BbsuOutput extends Model
{
    protected $table = 'bbsu_outputs';

    protected $fillable = [
        'bbsu_header_id',
        'material_name',
        'quantity',
        'yield',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'yield'    => 'decimal:2',
    ];

    public function bbsuHeader()
    {
        return $this->belongsTo(BbsuHeader::class, 'bbsu_header_id');
    }
}