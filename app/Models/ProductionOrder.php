<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductionOrder extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'code',
        'line_id',
        'factory_id',
        'product_id',
        'start_date',
        'end_date',
        'description',
        'status', 'quantity'
    ];
}
