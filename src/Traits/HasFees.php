<?php

namespace Homeful\Property\Traits;

use Brick\Math\RoundingMode;
use Whitecube\Price\Price;
use Brick\Money\Money;

/**
 * Trait HasFees
 *
 * Provides fee calculations for Mortgage Redemption Insurance (MRI) and Annual Fire Insurance (FI).
 */
trait HasFees
{
    /**
     *
     * Calculate the Mortgage Redemption Insurance (MRI) based on the loanable value.
     *
     *  MRI is calculated as 0.225 per PHP 1,000 of the loanable amount.
     * @return Price
     * @throws \Brick\Math\Exception\NumberFormatException
     * @throws \Brick\Math\Exception\RoundingNecessaryException
     * @throws \Brick\Money\Exception\UnknownCurrencyException
     */
    public function getMortgageRedemptionInsurance(): Price
    {
        $loanValue = $this->getLoanableValue()->inclusive()->getAmount()->toFloat();
        $mri = ($loanValue / 1000) * 0.225;

        return new Price(Money::of($mri, 'PHP', roundingMode: RoundingMode::CEILING));
    }

    /**
     * Calculate the Annual Fire Insurance (FI) based on the loanable value.
     *
     * FI is computed at a rate of 0.212584% of the loanable amount.
     *
     * @return Price The computed FI as a Price object.
     * @throws \Brick\Math\Exception\NumberFormatException
     * @throws \Brick\Math\Exception\RoundingNecessaryException
     * @throws \Brick\Money\Exception\UnknownCurrencyException
     */
    public function getAnnualFireInsurance(): Price
    {
        $loanValue = $this->getLoanableValue()->inclusive()->getAmount()->toFloat();
        $fireInsurance = $loanValue * 0.00212584;

        return new Price(Money::of($fireInsurance, 'PHP', roundingMode: RoundingMode::CEILING));
    }
}
