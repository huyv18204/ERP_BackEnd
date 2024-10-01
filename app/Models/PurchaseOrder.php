<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends Model
{
    use HasFactory;
    use  SoftDeletes;


    protected $fillable= [
        'code',
        'supplier_id',
        'order_date',
        'status',
        'total_amount',
        'total_price'
    ];


    public function purchase_order_items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }


}
