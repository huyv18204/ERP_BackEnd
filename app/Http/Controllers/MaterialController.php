<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    public function index(Request $request)
    {

        $materials = Material::query()->orderByDesc('id')->get();

        return response()->json($materials);
    }
}
