<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;


    protected $fillable = [
        "label",
        "parent",
        "url",
        "icon",
    ];

    // Định nghĩa quan hệ đệ quy cho menu cha
    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent');
    }

    // Định nghĩa quan hệ đệ quy cho các mục menu con
    public function children()
    {
        return $this->hasMany(Menu::class, 'parent');
    }

    // Hàm để lấy cây menu đệ quy
    public static function buildMenuTree($parentId = null)
    {
        return self::query()->where('parent', $parentId)
            ->with('children')
            ->get();
    }
}
