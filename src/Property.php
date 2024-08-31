<?php

namespace Homeful\Property;

use Homeful\Property\Exceptions\MaximumContractPriceBreached;
use Homeful\Property\Exceptions\MinimumContractPriceBreached;
use Homeful\Common\Interfaces\BorrowerInterface;
use Homeful\Property\Classes\LoanableModifier;
use Homeful\Property\Enums\DevelopmentType;
use Homeful\Property\Enums\MarketSegment;
use Whitecube\Price\Price;
use Brick\Money\Money;
use Exception;

class Property
{
    /**
     * arbitrary floor price
     */
    const MINIMUM_CONTRACT_PRICE = 500000; //₱0.5M

    /**
     * arbitrary ceiling price
     */
    const MAXIMUM_CONTRACT_PRICE = 5000000; //₱5M

    //    /**
    //     * @var DevelopmentType
    //     */
    //    protected DevelopmentType $developmentType = DevelopmentType::HORIZONTAL;

    protected Price $total_contract_price;

    protected Price $appraised_value;

    protected float $loanableValueMultiplier = 0.0;

    protected float $disposableIncomeRequirementMultiplier = 0.0;

    /**
     * @return $this
     *
     * @throws MaximumContractPriceBreached
     * @throws MinimumContractPriceBreached
     * @throws \Brick\Math\Exception\MathException
     * @throws \Brick\Math\Exception\NumberFormatException
     * @throws \Brick\Math\Exception\RoundingNecessaryException
     * @throws \Brick\Money\Exception\MoneyMismatchException
     * @throws \Brick\Money\Exception\UnknownCurrencyException
     */
    public function setTotalContractPrice(Price|Money|float $value): self
    {
        $total_contract_price = $value instanceof Price
            ? $value
            : new Price(($value instanceof Money)
                ? $value
                : Money::of($value, 'PHP'));

        if ($total_contract_price->inclusive()->compareTo(self::MINIMUM_CONTRACT_PRICE) == -1) {
            throw new MinimumContractPriceBreached;
        }
        if ($total_contract_price->inclusive()->compareTo(self::MAXIMUM_CONTRACT_PRICE) == 1) {
            throw new MaximumContractPriceBreached;
        } //TODO: Lester make some exceptions.

        $this->total_contract_price = $total_contract_price;

        return $this;
    }

    /**
     * @throws \Brick\Math\Exception\NumberFormatException
     * @throws \Brick\Math\Exception\RoundingNecessaryException
     * @throws \Brick\Money\Exception\UnknownCurrencyException
     */
    public function getTotalContractPrice(): Price
    {
        return $this->total_contract_price ?? new Price(Money::of(0, 'PHP'));
    }

    /**
     * @return $this
     *
     * @throws \Brick\Math\Exception\NumberFormatException
     * @throws \Brick\Math\Exception\RoundingNecessaryException
     * @throws \Brick\Money\Exception\UnknownCurrencyException
     */
    public function setAppraisedValue(Price|Money|float $value): self
    {
        $this->appraised_value = $value instanceof Price
            ? $value
            : new Price(($value instanceof Money)
                ? $value
                : Money::of($value, 'PHP'));

        return $this;
    }

    /**
     * @throws \Brick\Math\Exception\NumberFormatException
     * @throws \Brick\Math\Exception\RoundingNecessaryException
     * @throws \Brick\Money\Exception\UnknownCurrencyException
     */
    public function getAppraisedValue(): Price
    {
        return $this->appraised_value ?? new Price(Money::of(0, 'PHP'));
    }

    public function getMarketSegment(): MarketSegment
    {
        return MarketSegment::fromPrice($this->total_contract_price);
    }

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
