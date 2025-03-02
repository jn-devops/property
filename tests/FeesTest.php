<?php

use Homeful\Property\Property;
use Whitecube\Price\Price;
use Brick\Money\Money;

beforeEach(function () {
    $this->property = new Property();
    $this->property->setTotalContractPrice(new Price(Money::of(1000000, 'PHP')));
    $this->property->setAppraisedValue(new Price(Money::of(1000000, 'PHP')));
    $this->property->setLoanableValueMultiplier(0.80); // 80%
});

it('calculates mortgage redemption insurance correctly', function () {
    $mri = $this->property->getMortgageRedemptionInsurance();

    expect($mri)->toBeInstanceOf(Price::class);
    expect($mri->inclusive()->getAmount()->toFloat())->toBe(180.0); // (1,000,000 * 0.80) / 1000 * 0.225
});

it('calculates annual fire insurance correctly', function () {
    $fi = $this->property->getAnnualFireInsurance();

    expect($fi)->toBeInstanceOf(Price::class);
    expect($fi->inclusive()->getAmount()->toFloat())->toBe(1700.68); // (1,000,000 * 0.80) * 0.00212584
});

it('handles the minimum loanable value correctly', function () {
    // Set the loanable value multiplier to the minimum non-zero value (e.g., 0.01 or 1%)
    $this->property->setLoanableValueMultiplier(0.01); // 1% loanable value

    $mri = $this->property->getMortgageRedemptionInsurance();
    $fi = $this->property->getAnnualFireInsurance();

    expect($mri)->toBeInstanceOf(Price::class);
    expect($fi)->toBeInstanceOf(Price::class);

    expect($mri->inclusive()->getAmount()->toFloat())->toBe(2.25); // (1,000,000 * 0.01) / 1000 * 0.225
    expect($fi->inclusive()->getAmount()->toFloat())->toBe(21.26); // (1,000,000 * 0.01) * 0.00212584
});
