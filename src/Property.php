<?php

namespace Homeful\Property;

use Homeful\Property\Enums\DevelopmentType;
use Homeful\Property\Enums\ProjectType;
use Homeful\Property\Exceptions\MaximumContractPriceBreached;
use Homeful\Property\Exceptions\MinimumContractPriceBreached;
use Homeful\Property\Enums\MarketSegment;
use Homeful\Property\Traits\HasNumbers;
use Whitecube\Price\Price;
use Brick\Money\Money;

class Property
{
    use HasNumbers;

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

    protected ProjectType $project_type;

    protected DevelopmentType $development_type;


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

    public function setProjectType(ProjectType $type): self
    {
        $this->project_type = $type;

        return $this;
    }

    public function getProjectType(): ProjectType
    {
        return $this->project_type ?? ProjectType::SINGLE_DETACHED;
    }

    public function setDevelopmentType(DevelopmentType $type): self
    {
        $this->development_type = $type;

        return $this;
    }

    public function getDevelopmentType(): DevelopmentType
    {
        return $this->development_type ?? DevelopmentType::BP_220;
    }
}
