<?php

namespace Homeful\Property\Traits;

use Homeful\Common\Interfaces\BorrowerInterface;
use Homeful\Property\Classes\LoanableModifier;
use Homeful\Property\Enums\MarketSegment;
use Homeful\Property\Enums\HousingType;
use Homeful\Common\Classes\Amount;
use Homeful\Common\Enums\WorkArea;
use Homeful\Property\Property;
use Whitecube\Price\Price;
use Brick\Money\Money;
use Exception;

trait HasCalculations
{
    /** LOANABLE VALUE */

    /**
     * @return float
     */
    public function getDefaultLoanableValueMultiplier(): float
    {
        return $this->getMarketSegment()->defaultLoanableValueMultiplier();
    }

    /**
     * @param float $value
     * @return Property|HasCalculations
     *
     * @throws Exception
     */
    public function setLoanableValueMultiplier(float $value): self
    {
        if ($value <= 0.0) {
            throw new Exception('loanable value multiplier must be greater than 0%');
        }
        if ($value > 1) {
            throw new Exception('loanable value multiplier must be less than or equal to 100%');
        }

        $this->loanable_value_multiplier = $value;

        return $this;
    }

    /**
     * @return float
     */
    public function getLoanableValueMultiplier(): float
    {
        return $this->loanable_value_multiplier ?: $this->getDefaultLoanableValueMultiplier();
    }

    /**
     * @throws \Brick\Math\Exception\NumberFormatException
     * @throws \Brick\Math\Exception\RoundingNecessaryException
     * @throws \Brick\Money\Exception\UnknownCurrencyException
     */
    public function getLoanableValue(): Price
    {
        $appraised_value = $this->getAppraisedValue();
        $total_contract_price = $this->getTotalContractPrice();
        $price = new Price(($appraised_value->compareTo($total_contract_price) == Amount::LESS_THAN)
            ? $appraised_value->inclusive()
            : $total_contract_price->inclusive()
        );
        $price->addModifier('loanable', LoanableModifier::class, $this);

        return $price;
    }

    /** DISPOSABLE INCOME REQUIREMENT */

    /**
     * @return float
     */
    public function getDefaultDisposableIncomeRequirementMultiplier(): float
    {
        return $this->getMarketSegment()->defaultDisposableIncomeRequirementMultiplier();
    }

    /**
     * @return $this
     *
     * @throws Exception
     */
    public function setDisposableIncomeRequirementMultiplier(float $value): self
    {
        if ($value < 0.25) {
            throw new Exception('disposable income requirement multiplier must be greater than 25%');
        }
        if ($value > 1) {
            throw new Exception('disposable income requirement multiplier must be less than 100%');
        }

        $this->disposableIncomeRequirementMultiplier = $value;

        return $this;
    }

    /**
     * @return float
     */
    public function getDisposableIncomeRequirementMultiplier(): float
    {
        return $this->disposableIncomeRequirementMultiplier ?: $this->getDefaultDisposableIncomeRequirementMultiplier();
    }

    /**
     * @throws \Brick\Math\Exception\NumberFormatException
     * @throws \Brick\Math\Exception\RoundingNecessaryException
     * @throws \Brick\Money\Exception\UnknownCurrencyException
     */
    public function getDefaultAnnualInterestRateFromBorrower(BorrowerInterface $borrower): float
    {
        return $this->getDefaultAnnualInterestRate($this->getTotalContractPrice(), $borrower->getGrossMonthlyIncome(), $borrower->getRegional());
    }

    /**
     * @param Price $total_contract_price
     * @param Price $gross_monthly_income
     * @param bool $regional
     * @return float
     */
    protected function getDefaultAnnualInterestRate(Price $total_contract_price, Price $gross_monthly_income, bool $regional): float
    {
        $tcp = $total_contract_price->inclusive()->getAmount()->toFloat();
        $gmi = $gross_monthly_income->inclusive()->getAmount()->toFloat();

        return match($this->getMarketSegment()) {
            MarketSegment::SOCIALIZED, MarketSegment::ECONOMIC => match (true) {
                $tcp <= 750000 => $regional
                    ? ($gmi <= 12000 ? 0.030 : 0.0625)
                    : ($gmi <= 14500 ? 0.030 : 0.0625),
                $tcp <= 800000 => $regional
                    ? ($gmi <= 13000 ? 0.030 : 0.0625)
                    : ($gmi <= 15500 ? 0.030 : 0.0625),
                $tcp <= 850000 => $regional
                    ? ($gmi <= 15000 ? 0.030 : 0.0625)
                    : ($gmi <= 16500 ? 0.030 : 0.0625),
                default => 0.0625,
            },
            MarketSegment::OPEN => 0.07,
        };
    }

    /**
     * @return Price
     * @throws \Brick\Math\Exception\NumberFormatException
     * @throws \Brick\Math\Exception\RoundingNecessaryException
     * @throws \Brick\Money\Exception\UnknownCurrencyException
     */
    public function getPriceCeiling(): Price
    {
        $floor_area = $this->getFloorArea();
        $storeys = $this->getStoreys();
        $value = match ($this->getHousingType()) {
            HousingType::CONDOMINIUM => match (true) {
                $storeys == 0 => throw new Exception('storeys can not be zero'),
                $storeys <= 4 => match(true) {
                    $floor_area == 0.0 => throw new Exception('floor area can not be zero'),
                    $floor_area <= 22.0 => 933320.0,
                    $floor_area <= 25.0 => 1060591.0,
                    default => 1145438.0
                },
                $storeys <= 9 => match(true) {
                    $floor_area == 0.0 => throw new Exception('floor area can not be zero'),
                    $floor_area <= 22.0 => 1000000.0,
                    $floor_area <= 25.0 => 1136364.0,
                    default => 1227273.0
                },
                default => match(true) {
                    $floor_area == 0.0 => throw new Exception('floor area can not be zero'),
                    $floor_area <= 22.0 => 1320000.0,
                    $floor_area <= 25.0 => 1500000.0,
                    default => 1620000.0
                },
            },
            default => match ($this->getMarketSegment()) {
                MarketSegment::SOCIALIZED => match($this->getWorkArea()) {
                    WorkArea::HUC => 850000,
                    WorkArea::REGION => 750000
                },
                MarketSegment::ECONOMIC => 2500000,
                MarketSegment::OPEN => 10000000
            }
        };

        return new Price(Money::of($value, 'PHP'));
    }
}
