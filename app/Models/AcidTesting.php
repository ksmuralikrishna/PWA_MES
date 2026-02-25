<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcidTesting extends Model
{
    protected $fillable = [
        'test_date',
        'lot_number',
        'supplier',
        'vehicle_number',
        'avg_pallet_weight',
        'foreign_material_weight',
        'weigh_bridge_weight',
    ];
}
