<?php

namespace App\Enums;

enum PurchaseRequisitionStatus: string
{
    case PENDING = 'Pending';
    case REJECTED = 'Rejected';
    case APPROVED = 'Approved';

    public static function isValidValue(string $value): bool
    {
        return in_array($value, array_column(self::cases(), 'value'));
    }
}
