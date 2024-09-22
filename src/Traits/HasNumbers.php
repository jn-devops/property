<?php

namespace Homeful\Property\Traits;

use Homeful\Common\Interfaces\BorrowerInterface;
use Homeful\Property\Classes\LoanableModifier;
use Homeful\Property\Enums\MarketSegment;
use Whitecube\Price\Price;

trait HasNumbers
{
    /** LOANABLE VALUE */
    public function getDefaultLoanableValueMultiplier(): float
    {
        return $this->getMarketSegment()->defaultLoanableValueMultiplier();
    }

    /**
     * @return $this
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

        $this->loanableValueMultiplier = $value;

        return $this;
    }

    public function getLoanableValueMultiplier(): float
    {
        return $this->loanableValueMultiplier ?: $this->getDefaultLoanableValueMultiplier();
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
        $price = new Price(($appraised_value->compareTo($total_contract_price) == -1)
            ? $appraised_value->inclusive()
            : $total_contract_price->inclusive()
        );
        $price->addModifier('loanable', LoanableModifier::class, $this);

        return $price;
    }

    /** DISPOSABLE INCOME REQUIREMENT */
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
     * @throws \Brick\Math\Exception\MathException
     * @throws \Brick\Money\Exception\MoneyMismatchException
     */
    public function getDefaultAnnualInterestRate(Price $total_contract_price, Price $gross_monthly_income, bool $regional): float
    {
        return match (true) {
            $this->getMarketSegment() == MarketSegment::OPEN => 0.07,
            default => match (true) {
                $total_contract_price->inclusive()->compareTo(750000) <= 0 => ($regional
                    ? ($gross_monthly_income->inclusive()->compareTo(12000) <= 0 ? 0.030 : 0.0625)
                    : ($gross_monthly_income->inclusive()->compareTo(14500) <= 0 ? 0.030 : 0.0625)),
                $total_contract_price->inclusive()->compareTo(800000) <= 0 => ($regional
                    ? ($gross_monthly_income->inclusive()->compareTo(13000) <= 0 ? 0.030 : 0.0625)
                    : ($gross_monthly_income->inclusive()->compareTo(15500) <= 0 ? 0.030 : 0.0625)),
                $total_contract_price->inclusive()->compareTo(850000) <= 0 => ($regional
                    ? ($gross_monthly_income->inclusive()->compareTo(15000) <= 0 ? 0.030 : 0.0625)
                    : ($gross_monthly_income->inclusive()->compareTo(16500) <= 0 ? 0.030 : 0.0625)),
                default => 0.0625,
            }
        };
    }
}
