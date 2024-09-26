<?php

namespace Homeful\Property\Enums;

use Whitecube\Price\Price;
use Brick\Money\Money;

enum Charge
{
    case PROCESSING_FEE;

    case HOME_UTILITY_CONNECTION_FEE;

    public function getName(): string
    {
        return match ($this) {
            self::PROCESSING_FEE => 'Processing Fee',
            self::HOME_UTILITY_CONNECTION_FEE => 'Home Utility Connection Fee',
        };
    }

    public function getPrice(): Price
    {
        $amount = match ($this) {
            self::PROCESSING_FEE => 10000,
            self::HOME_UTILITY_CONNECTION_FEE => 500,
        };

        return new Price(Money::of($amount, 'PHP'));
    }
}
