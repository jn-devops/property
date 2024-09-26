<?php

namespace Homeful\Property\Data;

use Brick\Math\Exception\RoundingNecessaryException;
use Brick\Money\Exception\UnknownCurrencyException;
use Brick\Math\Exception\NumberFormatException;
use Homeful\Property\Property;
use Spatie\LaravelData\Data;

class PropertyData extends Data
{
    public function __construct(
        public string $market_segment,
        public float $total_contract_price,
        public float $appraised_value,
        public float $default_loanable_value_multiplier,
        public float $loanable_value_multiplier,
        public float $loanable_value,
        public float $disposable_income_requirement_multiplier,
        public float $default_disposable_income_requirement_multiplier,
        public string $work_area,
        public string $development_type,
        public string $housing_type,
        public int $storeys,
        public float $floor_area,
        public float $price_ceiling
    ) {}

    /**
     * @throws UnknownCurrencyException
     * @throws RoundingNecessaryException
     * @throws NumberFormatException
     */
    public static function fromObject(Property $property): self
    {
        return new self(
            market_segment: $property->getMarketSegment()->getName(),
            total_contract_price: $property->getTotalContractPrice()->inclusive()->getAmount()->toFloat(),
            appraised_value: $property->getAppraisedValue()->inclusive()->getAmount()->toFloat(),
            default_loanable_value_multiplier: $property->getDefaultLoanableValueMultiplier(),
            loanable_value_multiplier: $property->getLoanableValueMultiplier(),
            loanable_value: $property->getLoanableValue()->inclusive()->getAmount()->toFloat(),
            disposable_income_requirement_multiplier: $property->getDisposableIncomeRequirementMultiplier(),
            default_disposable_income_requirement_multiplier: $property->getDefaultDisposableIncomeRequirementMultiplier(),
            work_area: $property->getWorkArea()->getName(),
            development_type: $property->getDevelopmentType()->getName(),
            housing_type: $property->getHousingType()->getName(),
            storeys: $property->getStoreys(),
            floor_area: $property->getFloorArea(),
            price_ceiling: $property->getPriceCeiling()->inclusive()->getAmount()->toFloat()
        );
    }
}
