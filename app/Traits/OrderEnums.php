<?php

namespace App\Traits;

trait OrderEnums
{
    public static function orderStatuses(): array
    {
        return [
            'PENDING' => 'PENDING',
            'REQUESTED' => 'REQUESTED',
            'CONFIRMED' => 'CONFIRMED',
            'DELIVERED' => 'DELIVERED',
            'CANCELLED' => 'CANCELLED'
        ];
    }

    public static function paymentTerms(): array
    {
        return [
            'PAID_ON_DELIVERY' => 'PAID_ON_DELIVERY',
            'PAID_BEFORE_DELIVERY' => 'PAID_BEFORE_DELIVERY',
            'CREDIT' => 'CREDIT'
        ];
    }
}