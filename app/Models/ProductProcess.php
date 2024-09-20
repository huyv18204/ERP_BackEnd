<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductProcess extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [

        'process_id',
        'sale_order_item_id',
        'std_workTime',
        'description'
    ];
}
