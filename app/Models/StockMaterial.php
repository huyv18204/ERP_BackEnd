<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockMaterial extends Model
{
    use HasFactory;
    use SoftDeletes;


    protected $fillable = [
      'material_id',
      'quantity'
    ];

    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}
