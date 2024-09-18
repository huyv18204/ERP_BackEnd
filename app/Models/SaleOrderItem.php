<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SaleOrderItem extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'code',
        'sale_order_id',
        'product',
        'unit_price',
        'delivery_date',
        'description'
    ];


    public function sale_order()
    {
        return $this->belongsTo(SaleOrder::class);
    }
}
