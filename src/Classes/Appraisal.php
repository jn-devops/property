<?php

namespace Homeful\Property\Classes;

use Brick\Math\RoundingMode;
use Whitecube\Price\Price;
use Brick\Money\Money;

class Appraisal
{
    protected Price $lot_appraisal;
    protected Price $house_appraisal;

    public function __construct(Price|Money|float $lot_appraisal, Price|Money|float $house_appraisal)
    {
        $this->lot_appraisal = $lot_appraisal instanceof Price ? $lot_appraisal
            : ( $lot_appraisal instanceof Money
                ? new Price($lot_appraisal)
                : new Price(Money::of($lot_appraisal, 'PHP'))
            );
        $this->house_appraisal = $house_appraisal instanceof Price ? $house_appraisal
            : ( $house_appraisal instanceof Money
                ? new Price($house_appraisal)
                : new Price(Money::of($house_appraisal, 'PHP'))
            );
    }

    public function getLotAppraisal(): Price
    {
        return $this->lot_appraisal;
    }

    public function getHouseAppraisal(): Price
    {
        return $this->house_appraisal;
    }

    public function getTotalAppraisal(): Price
    {
        $total = new Price(Money::of(0, 'PHP'));
        $total->addModifier('lot', $this->lot_appraisal, roundingMode: RoundingMode::CEILING);
        $total->addModifier('house', $this->house_appraisal, roundingMode: RoundingMode::CEILING);

        return $total;
    }
}
