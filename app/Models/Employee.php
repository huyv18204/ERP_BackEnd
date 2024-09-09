<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'employee_code',
        'address',
        'phone',
        'work_date',
        'department_id',
        'name'
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
