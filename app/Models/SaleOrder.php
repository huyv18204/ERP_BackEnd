<?php

namespace App\Models;

use App\Enums\SaleOrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SaleOrder extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        "code",
        'order_date',
        'customer_id',
        'total_price',
        'total_amount',
        'status'
    ];


    public function sale_order_items()
    {
        return $this->hasMany(SaleOrderItem::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    protected $casts = [
        'status' => SaleOrderStatus::class,
    ];
}
