<?php

namespace Homeful\Property;

use Brick\Money\Money;
use Exception;
use Homeful\Property\Classes\LoanableModifier;
use Homeful\Property\Enums\DevelopmentType;
use Homeful\Property\Enums\MarketSegment;
use Homeful\Property\Exceptions\MaximumContractPriceBreached;
use Homeful\Property\Exceptions\MinimumContractPriceBreached;
use Whitecube\Price\Price;

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

    public function setTotalContractPrice(Price $value): self
    {
        if ($value->inclusive()->compareTo(self::MINIMUM_CONTRACT_PRICE) == -1) {
            throw new MinimumContractPriceBreached;
        }
        if ($value->inclusive()->compareTo(self::MAXIMUM_CONTRACT_PRICE) == 1) {
            throw new MaximumContractPriceBreached;
        } //TODO: Lester make some exceptions.

        $this->total_contract_price = $value;

        return $this;
    }

    public function getTotalContractPrice(): Price
    {
        return $this->total_contract_price ?? new Price(Money::of(0, 'PHP'));
    }

    /**
     * @return $this
     */
    public function setAppraisedValue(Price $value): self
    {
        $this->appraised_value = $value;

        return $this;
    }

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
}
