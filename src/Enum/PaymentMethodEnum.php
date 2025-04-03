<?php

namespace App\Enum;

enum PaymentMethodEnum : string
{
    case SHIFT4 = 'shift4';
    case ACI = 'aci';
    // Add more as needed each case should reflect a class

    public static function toArray()
    {
        return array_map(fn($case) => ["method"=>$case->value], self::cases());
    }

}
