<?php

namespace Homeful\Property;

use Homeful\Property\Traits\{HasCalculations, HasProduct, HasProperties};
use Homeful\Common\Interfaces\BorrowerInterface;
use Homeful\Common\Interfaces\ProductInterface;
use Homeful\Property\Enums\DevelopmentType;
use Homeful\Property\Enums\MarketSegment;
use Homeful\Property\Enums\HousingType;
use Homeful\Property\Classes\Appraisal;
use Homeful\Property\Enums\Charge;
use Illuminate\Support\Collection;
use Homeful\Common\Enums\WorkArea;
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
 * @property Collection $charges
 * @property Appraisal $appraisal
 *
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
 * @method Property addCharge(Charge $charge)
 * @method Collection getCharges()
 * @method Price getFees()
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
 * @method string getSKU()
 * @method Property setSKU(string $value)
 * @method Price getProcessingFee()
 * @method Property setProcessingFee(Price|float $value)
 * @method float getPercentDownPayment()
 * @method Property setPercentDownPayment(float $value)
 * @method int getDownPaymentTerm()
 * @method Property setDownPaymentTerm(int $value)
 * @method float getPercentMiscellaneousFees()
 * @method Property setPercentMiscellaneousFees(float $value)
 */

class Property implements ProductInterface
{
    use HasCalculations;
    use HasProperties;
    use HasProduct;

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
    protected Collection $charges;
    protected Appraisal $appraisal;

    /**
     * arbitrary floor price
     */
    const MINIMUM_CONTRACT_PRICE = 500000; //₱0.5M

    /**
     * arbitrary ceiling price
     */
    const MAXIMUM_CONTRACT_PRICE = 5000000; //₱5M

    public function __construct()
    {
        $this->charges = new Collection;
    }

    /**
     * @param Appraisal $appraisal
     * @return $this
     */
    public function setAppraisal(Appraisal $appraisal): self
    {
        $this->appraisal = $appraisal;

        return $this;
    }

    /**
     * @return Appraisal
     */
    public function getAppraisal(): Appraisal
    {
        return $this->appraisal;
    }
}
