<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receiving extends Model
{
    protected $fillable = [
        'date',
        'supplier',
        'material',
        'invoice_qty',
        'received_qty',
        'unit',
        'vehicle_number',
        'lot_no',
        'remarks',
        'operator_id'
    ];
}
