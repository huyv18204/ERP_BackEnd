<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseRequisition extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
      'create_date',
      'code',
      'status',
      'notes'
    ];


    public function purchase_requisition_items()
    {
        return $this->hasMany(PurchaseRequisitionItem::class);
    }
}
