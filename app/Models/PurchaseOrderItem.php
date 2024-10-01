<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrderItem extends Model
{
    use HasFactory;
    use  SoftDeletes;
    protected $fillable= [
        'purchase_order_id',
        'material_id',
        'unit_price',
        'quantity',
        'total_price'
    ];

    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}
