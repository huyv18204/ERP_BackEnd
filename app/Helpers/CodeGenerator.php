<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class CodeGenerator
{
    public static function generateCode($table, $prefix)
    {
        $currentDay = date('d');
        $currentMonth = date('m');
        $prevCode = $prefix . $currentDay . $currentMonth;
        $stt = DB::table($table)->where('code', 'LIKE', $prevCode . '%')->orderByDesc('id')->first();
//        $stt = DB::table($table)->where('code', 'LIKE', $prevCode . '%')->orderByDesc('id')->first();
        if ($stt) {
            $parts = explode('-', $stt->code);
            $lastPart = (int)end($parts) + 1;
            $code = $prevCode . '-' . str_pad($lastPart, 3, '0', STR_PAD_LEFT);
        } else {
            $code = $prevCode . '-001';
        }

        return $code;
    }
}
