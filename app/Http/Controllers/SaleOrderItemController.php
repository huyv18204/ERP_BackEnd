<?php

namespace App\Http\Controllers;

use App\Models\SaleOrderItem;
use Illuminate\Http\Request;

class SaleOrderItemController extends Controller
{
public  function index()
{
    $saleOrderItem = SaleOrderItem::query()->get();
    return response()->json($saleOrderItem);
}
}
