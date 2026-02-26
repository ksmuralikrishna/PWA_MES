<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receiving extends Model
{
    protected $fillable = [
        'receipt_date',
        'supplier_id',
        'material_id',
        'invoice_qty',
        'received_qty',
        'unit',
        'vehicle_number',
        'lot_no',
        'remarks',
        'created_by',
        'updated_by'
    ];
}
