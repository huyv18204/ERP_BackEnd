<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseRequisitionItem extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'material_id',
        'quantity',
        'purchase_requisition_id',
        'material_code'
    ];
}
