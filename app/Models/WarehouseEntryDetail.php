<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WarehouseEntryDetail extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'material_code',
        'quantity',
        'name',
        'unit_price',
        'total_price',
        'warehouse_entry_id'
    ];
}
