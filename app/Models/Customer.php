<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{

    use  SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'fax',
        'customer_code',
        'name',
        'address',
        'phone'
    ];
}
