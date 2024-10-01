<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AllocatedProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'allocated_quantity',
        'product_id',
        'sale_order_id',
        'status'
    ];
}
