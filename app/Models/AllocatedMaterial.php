<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AllocatedMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'allocated_quantity',
        'material_id',
        'production_order_id',
        'status'
    ];
}
