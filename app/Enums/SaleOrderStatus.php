<?php

namespace App\Enums;

enum SaleOrderStatus: string
{
    case PENDING = 'Pending';
    case PENDING_PRODUCTION = 'Pending Production';
    case IN_PRODUCTION = 'In Production';
    case COMPLETED_PRODUCTION = 'Completed';
    case CANCELLED = 'Cancelled';
    case ON_HOLD = 'On Hold';


    public static function isValidValue(string $value): bool
    {
        return in_array($value, array_column(self::cases(), 'value'));
    }
}
