<?php

namespace Homeful\Property\Traits;

use Homeful\Common\Enums\WorkArea;
use Homeful\Property\Exceptions\MaximumContractPriceBreached;
use Homeful\Property\Exceptions\MinimumContractPriceBreached;
use Brick\Math\Exception\RoundingNecessaryException;
use Brick\Money\Exception\UnknownCurrencyException;
use Brick\Money\Exception\MoneyMismatchException;
use Brick\Math\Exception\NumberFormatException;
use Homeful\Property\Enums\DevelopmentType;
use Homeful\Property\Enums\MarketSegment;
use Brick\Math\Exception\MathException;
use Homeful\Property\Enums\HousingType;
use Homeful\Property\Property;
use Whitecube\Price\Price;
use Brick\Money\Money;

trait HasProperties
{
    /**
     * @param MarketSegment $market_segment
     * @return Property|HasProperties
     */
    public function setMarketSegment(MarketSegment $market_segment): self
    {
        $this->market_segment = $market_segment;

        return $this;
    }

    /**
     * @return MarketSegment
     */
    public function getMarketSegment(): MarketSegment
    {
        return $this->market_segment ?? MarketSegment::fromPrice($this->total_contract_price);
    }

    /**
     * @param WorkArea $work_area
     * @return HasProperties|Property
     */
    public function setWorkArea(WorkArea $work_area): self
    {
        $this->work_area = $work_area;

        return $this;
    }

    /**
     * @return WorkArea
     */
    public function getWorkArea(): WorkArea
    {
        return $this->work_area ?? WorkArea::default();
    }

    /**
     * @param Price|Money|float $value
     * @return HasProperties|Property
     *
     * @throws MaximumContractPriceBreached
     * @throws MinimumContractPriceBreached
     * @throws MathException
     * @throws NumberFormatException
     * @throws RoundingNecessaryException
     * @throws MoneyMismatchException
     * @throws UnknownCurrencyException
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
     * @param Price|Money|float $value
     * @return Property|HasProperties
     *
     * @throws NumberFormatException
     * @throws RoundingNecessaryException
     * @throws UnknownCurrencyException
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

    /**
     * @param DevelopmentType $type
     * @return HasProperties|Property
     */
    public function setDevelopmentType(DevelopmentType $type): self
    {
        $this->development_type = $type;

        return $this;
    }

    /**
     * @return DevelopmentType
     */
    public function getDevelopmentType(): DevelopmentType
    {
        return $this->development_type ?? DevelopmentType::BP_220;
    }

    /**
     * @param HousingType $type
     * @return HasProperties|Property
     */
    public function setHousingType(HousingType $type): self
    {
        $this->housing_type = $type;

        return $this;
    }

    /**
     * @return HousingType
     */
    public function getHousingType(): HousingType
    {
        return $this->housing_type ?? HousingType::SINGLE_DETACHED;
    }

    /**
     * @param float $value
     * @return HasProperties|Property
     */
    public function setFloorArea(float $value): self
    {
        $this->floor_area = $value;

        return $this;
    }

    /**
     * @return float
     */
    public function getFloorArea(): float
    {
        return $this->floor_area ?? 0.0;
    }

    /**
     * @param int $value
     * @return HasProperties|Property
     */
    public function setStoreys(int $value): self
    {
        $this->storeys = $value;

        return $this;
    }

    /**
     * @return int
     */
    public function getStoreys(): int
    {
        return $this->storeys ?? 0;
    }
}
