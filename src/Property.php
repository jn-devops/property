<?php

namespace Homeful\Property;

use Homeful\Common\Enums\WorkArea;
use Homeful\Common\Interfaces\BorrowerInterface;
use Homeful\Property\Traits\HasCalculations;
use Homeful\Property\Enums\DevelopmentType;
use Homeful\Property\Traits\HasProperties;
use Homeful\Property\Enums\MarketSegment;
use Homeful\Property\Enums\HousingType;
use Whitecube\Price\Price;
use Brick\Money\Money;

/**
 * Class Property
 *
 * @property Price $total_contract_price
 * @property Price $appraised_value
 * @property DevelopmentType $development_type
 * @property HousingType $housing_type
 * @property WorkArea $work_area
 * @property float $floor_area
 * @property int $storeys
 * @method Property setMarketSegment(MarketSegment $market_segment)
 * @method MarketSegment getMarketSegment()
 * @method Property setWorkArea(WorkArea $work_area)
 * @method WorkArea getWorkArea()
 * @method Property setTotalContractPrice(Price|Money|float $value)
 * @method Price getTotalContractPrice()
 * @method Property setAppraisedValue(Price|Money|float $value)
 * @method Price getAppraisedValue()
 * @method Property setDevelopmentType(DevelopmentType $type)
 * @method DevelopmentType getDevelopmentType()
 * @method Property setHousingType(HousingType $type)
 * @method HousingType getHousingType()
 * @method Property setFloorArea(float $value)
 * @method float getFloorArea()
 * @method Property setStoreys(int $value)
 * @method int getStoreys()
 *
 * @property float $loanable_value_multiplier
 * @method float getDefaultLoanableValueMultiplier()
 * @method Property setLoanableValueMultiplier(float $value)
 * @method float getLoanableValueMultiplier()
 * @method Price getLoanableValue()
 * @method float getDefaultDisposableIncomeRequirementMultiplier()
 * @method Property setDisposableIncomeRequirementMultiplier(float $value)
 * @method float getDisposableIncomeRequirementMultiplier()
 * @method float getDefaultAnnualInterestRateFromBorrower(BorrowerInterface $borrower)
 * @method float getDefaultAnnualInterestRate(Price $total_contract_price, Price $gross_monthly_income, bool $regional)
 * @method Price getPriceCeiling()
 */

class Property
{
    use HasProperties;
    use HasCalculations;

    protected MarketSegment $market_segment;
    protected Price $total_contract_price;
    protected Price $appraised_value;
    protected DevelopmentType $development_type;
    protected HousingType $housing_type;
    protected float $loanable_value_multiplier = 0.0;
    protected float $disposableIncomeRequirementMultiplier = 0.0;
    protected WorkArea $work_area;
    protected float $floor_area;
    protected int $storeys;

    /**
     * arbitrary floor price
     */
    const MINIMUM_CONTRACT_PRICE = 500000; //₱0.5M

    /**
     * arbitrary ceiling price
     */
    const MAXIMUM_CONTRACT_PRICE = 5000000; //₱5M

//    public function getMarketSegment(): MarketSegment
//    {
//        return MarketSegment::fromPrice($this->total_contract_price);
//    }
}
