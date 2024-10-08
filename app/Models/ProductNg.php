<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductNg extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [

        'ng_type_id',
        'sale_order_item_id',
        'description'
    ];

}
