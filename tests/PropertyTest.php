<?php

use Homeful\Property\Enums\{DevelopmentType, MarketSegment, HousingType};
use Homeful\Property\Exceptions\MaximumContractPriceBreached;
use Homeful\Property\Exceptions\MinimumContractPriceBreached;
use Homeful\Common\Interfaces\BorrowerInterface;
use Homeful\Common\Classes\{Assert, Input};
use Homeful\Property\Data\PropertyData;
use Homeful\Common\Enums\WorkArea;
use Homeful\Common\Classes\Amount;
use Homeful\Property\Property;
use Mockery\MockInterface;
use Whitecube\Price\Price;
use Brick\Money\Money;

it('has minimum price', function () {
    $property = new Property;
    $property->setTotalContractPrice(new Price(Money::of(Property::MINIMUM_CONTRACT_PRICE - 1, 'PHP')));
})->expectException(MinimumContractPriceBreached::class);

it('has maximum price', function () {
    $property = new Property;
    $property->setTotalContractPrice(new Price(Money::of(Property::MAXIMUM_CONTRACT_PRICE + 1, 'PHP')));
})->expectException(MaximumContractPriceBreached::class);

it('has market segments', function () {
    expect(MarketSegment::OPEN->getName())->toBe(config('property.market.segment.open'));
    expect(MarketSegment::OPEN->getName())->toBe('middle-income');
    expect(MarketSegment::ECONOMIC->getName())->toBe(config('property.market.segment.economic'));
    expect(MarketSegment::ECONOMIC->getName())->toBe('economic');
    expect(MarketSegment::SOCIALIZED->getName())->toBe(config('property.market.segment.socialized'));
    expect(MarketSegment::SOCIALIZED->getName())->toBe('socialized');
});

it('has housing types', function () {
    $property = new Property;
    expect($property->getHousingType())->toBe(HousingType::SINGLE_DETACHED);
    $property->setHousingType(HousingType::CONDOMINIUM);
    expect($property->getHousingType())->toBe(HousingType::CONDOMINIUM);
    $property->setHousingType(HousingType::DUPLEX);
    expect($property->getHousingType())->toBe(HousingType::DUPLEX);
    $property->setHousingType(HousingType::ROW_HOUSE);
    expect($property->getHousingType())->toBe(HousingType::ROW_HOUSE);
    $property->setHousingType(HousingType::SINGLE_ATTACHED);
    expect($property->getHousingType())->toBe(HousingType::SINGLE_ATTACHED);
    $property->setHousingType(HousingType::QUADRUPLEX);
    expect($property->getHousingType())->toBe(HousingType::QUADRUPLEX);
    $property->setHousingType(HousingType::SINGLE_DETACHED);
    expect($property->getHousingType())->toBe(HousingType::SINGLE_DETACHED);
});

it('has development types', function () {
    $property = new Property;
    expect($property->getDevelopmentType())->toBe(DevelopmentType::BP_220);
    $property->setDevelopmentType(DevelopmentType::BP_957);
    expect($property->getDevelopmentType())->toBe(DevelopmentType::BP_957);
    $property->setDevelopmentType(DevelopmentType::BP_220);
    expect($property->getDevelopmentType())->toBe(DevelopmentType::BP_220);
});

it('can get market segment from tcp', function () {
    $property = new Property;
    expect($property->setTotalContractPrice(new Price(Money::of(750000, 'PHP')))->getMarketSegment())->toBe(MarketSegment::SOCIALIZED);
    expect($property->setTotalContractPrice(new Price(Money::of(849999, 'PHP')))->getMarketSegment())->toBe(MarketSegment::SOCIALIZED);
    expect($property->setTotalContractPrice(new Price(Money::of(850000, 'PHP')))->getMarketSegment())->toBe(MarketSegment::SOCIALIZED);
    expect($property->setTotalContractPrice(new Price(Money::of(850001, 'PHP')))->getMarketSegment())->toBe(MarketSegment::ECONOMIC);
    expect($property->setTotalContractPrice(new Price(Money::of(2499999, 'PHP')))->getMarketSegment())->toBe(MarketSegment::ECONOMIC);
    expect($property->setTotalContractPrice(new Price(Money::of(2500000, 'PHP')))->getMarketSegment())->toBe(MarketSegment::ECONOMIC);
    expect($property->setTotalContractPrice(new Price(Money::of(2500001, 'PHP')))->getMarketSegment())->toBe(MarketSegment::OPEN);
});

it('has default and settable appraised value', function () {
    /** BELOW SOCIALIZED LIMIT, OVER - APPRAISED */
    $property = new Property;
    expect($property
        ->setTotalContractPrice(new Price(Money::of(849999, 'PHP')))
        ->setAppraisedValue(new Price(Money::of(900000, 'PHP')))
        ->getLoanableValueMultiplier()
    )->toBe(1.00);
    expect($property->getTotalContractPrice()->inclusive()->getAmount()->toFloat())->toBe(849999.0);
    expect($property->getAppraisedValue()->inclusive()->getAmount()->toFloat())->toBe(900000.0);
    expect($property->getLoanableValueMultiplier())->toBe(1.0);
    expect($property->getLoanableValue()->inclusive()->getAmount()->toFloat())->toBe($property->getTotalContractPrice()->multipliedBy(1.00)->inclusive()->getAmount()->toFloat());

    /** BELOW SOCIALIZED LIMIT, UNDER - APPRAISED */
    $property = new Property;
    expect($property
        ->setTotalContractPrice(new Price(Money::of(849999, 'PHP')))
        ->setAppraisedValue(new Price(Money::of(800000, 'PHP')))
        ->getLoanableValueMultiplier()
    )->toBe(1.00);
    expect($property->getTotalContractPrice()->inclusive()->getAmount()->toFloat())->toBe(849999.0);
    expect($property->getAppraisedValue()->inclusive()->getAmount()->toFloat())->toBe(800000.0);
    expect($property->getLoanableValueMultiplier())->toBe(1.0);
    expect($property->getLoanableValue()->inclusive()->getAmount()->toFloat())->toBe($property->getAppraisedValue()->multipliedBy(1.00)->inclusive()->getAmount()->toFloat());

    /** SOCIALIZED LIMIT, OVER - APPRAISED */
    $property = new Property;
    expect($property
        ->setTotalContractPrice(new Price(Money::of(850000, 'PHP')))
        ->setAppraisedValue(new Price(Money::of(900000, 'PHP')))
        ->getLoanableValueMultiplier()
    )->toBe(1.00);
    expect($property->getTotalContractPrice()->inclusive()->getAmount()->toFloat())->toBe(850000.0);
    expect($property->getAppraisedValue()->inclusive()->getAmount()->toFloat())->toBe(900000.0);
    expect($property->getLoanableValueMultiplier())->toBe(1.0);
    expect($property->getLoanableValue()->inclusive()->getAmount()->toFloat())->toBe($property->getTotalContractPrice()->multipliedBy(1.00)->inclusive()->getAmount()->toFloat());

    /** SOCIALIZED LIMIT, UNDER - APPRAISED */
    $property = new Property;
    expect($property
        ->setTotalContractPrice(new Price(Money::of(850000, 'PHP')))
        ->setAppraisedValue(new Price(Money::of(800000, 'PHP')))
        ->getLoanableValueMultiplier()
    )->toBe(1.00);
    expect($property->getTotalContractPrice()->inclusive()->getAmount()->toFloat())->toBe(850000.0);
    expect($property->getAppraisedValue()->inclusive()->getAmount()->toFloat())->toBe(800000.0);
    expect($property->getLoanableValueMultiplier())->toBe(1.0);
    expect($property->getLoanableValue()->inclusive()->getAmount()->toFloat())->toBe($property->getAppraisedValue()->multipliedBy(1.00)->inclusive()->getAmount()->toFloat());

    /** BELOW ECONOMIC LIMIT, OVER - APPRAISED */
    $property = new Property;
    expect($property
        ->setTotalContractPrice(new Price(Money::of(850001, 'PHP')))
        ->setAppraisedValue(new Price(Money::of(900000, 'PHP')))
        ->getLoanableValueMultiplier()
    )->toBe(0.95);
    expect($property->getTotalContractPrice()->inclusive()->getAmount()->toFloat())->toBe(850001.0);
    expect($property->getAppraisedValue()->inclusive()->getAmount()->toFloat())->toBe(900000.0);
    expect($property->getLoanableValueMultiplier())->toBe(0.95);
    expect($property->getLoanableValue()->inclusive()->getAmount()->toFloat())->toBe($property->getTotalContractPrice()->multipliedBy(0.95)->inclusive()->getAmount()->toFloat());

    /** BELOW ECONOMIC LIMIT, UNDER - APPRAISED */
    $property = new Property;
    expect($property
        ->setTotalContractPrice(new Price(Money::of(850001, 'PHP')))
        ->setAppraisedValue(new Price(Money::of(800000, 'PHP')))
        ->getLoanableValueMultiplier()
    )->toBe(0.95);
    expect($property->getTotalContractPrice()->inclusive()->getAmount()->toFloat())->toBe(850001.0);
    expect($property->getAppraisedValue()->inclusive()->getAmount()->toFloat())->toBe(800000.0);
    expect($property->getLoanableValueMultiplier())->toBe(0.95);
    expect($property->getLoanableValue()->inclusive()->getAmount()->toFloat())->toBe($property->getAppraisedValue()->multipliedBy(0.95)->inclusive()->getAmount()->toFloat());

    $property = new Property;
    expect($property
        ->setTotalContractPrice(new Price(Money::of(2500000, 'PHP')))
        ->setAppraisedValue(new Price(Money::of(2000000, 'PHP')))
        ->getLoanableValueMultiplier()
    )->toBe(0.95);
    expect($property->getTotalContractPrice()->inclusive()->getAmount()->toFloat())->toBe(2500000.0);
    expect($property->getAppraisedValue()->inclusive()->getAmount()->toFloat())->toBe(2000000.0);
    expect($property->getLoanableValueMultiplier())->toBe(0.95);
    expect($property->getLoanableValue()->inclusive()->getAmount()->toFloat())->toBe($property->getAppraisedValue()->multipliedBy(0.95)->inclusive()->getAmount()->toFloat());

    /** OPEN, UNDER - APPRAISED */
    $property = new Property;
    expect($property
        ->setTotalContractPrice(new Price(Money::of(2500001, 'PHP')))
        ->setAppraisedValue(new Price(Money::of(2000000, 'PHP')))
        ->getLoanableValueMultiplier()
    )->toBe(0.90);
    expect($property->getTotalContractPrice()->inclusive()->getAmount()->toFloat())->toBe(2500001.0);
    expect($property->getAppraisedValue()->inclusive()->getAmount()->toFloat())->toBe(2000000.0);
    expect($property->getLoanableValueMultiplier())->toBe(0.90);
    expect($property->getLoanableValue()->inclusive()->getAmount()->toFloat())->toBe($property->getAppraisedValue()->multipliedBy(0.90)->inclusive()->getAmount()->toFloat());

    /** OPEN, SET MULTIPLIER, UNDER - APPRAISED */
    $property = new Property;
    expect($property
        ->setTotalContractPrice(new Price(Money::of(3000000, 'PHP')))
        ->setLoanableValueMultiplier(0.8)->setAppraisedValue(new Price(Money::of(2500000, 'PHP')))
        ->getLoanableValueMultiplier()
    )->toBe(0.80);
    expect($property->getTotalContractPrice()->inclusive()->getAmount()->toFloat())->toBe(3000000.0);
    expect($property->getAppraisedValue()->inclusive()->getAmount()->toFloat())->toBe(2500000.0);
    expect($property->getLoanableValueMultiplier())->toBe(0.80);
    expect($property->getLoanableValue()->inclusive()->getAmount()->toFloat())->toBe($property->getAppraisedValue()->multipliedBy(0.80)->inclusive()->getAmount()->toFloat());
});

it('has default and settable disposable income requirement', function () {
    $property = new Property;
    expect($property->setTotalContractPrice(new Price(Money::of(849999, 'PHP')))->getDisposableIncomeRequirementMultiplier())->toBe(0.35);
    expect($property->setTotalContractPrice(new Price(Money::of(850000, 'PHP')))->getDisposableIncomeRequirementMultiplier())->toBe(0.35);
    expect($property->setTotalContractPrice(new Price(Money::of(850001, 'PHP')))->getDisposableIncomeRequirementMultiplier())->toBe(0.35);
    expect($property->setTotalContractPrice(new Price(Money::of(2499999, 'PHP')))->getDisposableIncomeRequirementMultiplier())->toBe(0.35);
    expect($property->setTotalContractPrice(new Price(Money::of(2500000, 'PHP')))->getDisposableIncomeRequirementMultiplier())->toBe(0.35);
    expect($property->setTotalContractPrice(new Price(Money::of(2500001, 'PHP')))->getDisposableIncomeRequirementMultiplier())->toBe(0.30);
    $property->setDisposableIncomeRequirementMultiplier(0.32);
    expect($property->getDisposableIncomeRequirementMultiplier())->toBe(0.32);
});

it('has property data', function () {
    $property = new Property;
    $property->setTotalContractPrice(new Price(Money::of(750000, 'PHP')))->setAppraisedValue(new Price(Money::of(740000, 'PHP')));
    $data = PropertyData::fromObject($property);
    expect($data->market_segment)->toBe($property->getMarketSegment()->getName());
    expect($data->total_contract_price)->toBe($property->getTotalContractPrice()->inclusive()->getAmount()->toFloat());
    expect($data->appraised_value)->toBe($property->getAppraisedValue()->inclusive()->getAmount()->toFloat());
    expect($data->default_loanable_value_multiplier)->toBe($property->getDefaultLoanableValueMultiplier());
    expect($data->loanable_value_multiplier)->toBe($property->getLoanableValueMultiplier());
    expect($data->loanable_value)->toBe($property->getLoanableValue()->inclusive()->getAmount()->toFloat());
    expect($data->disposable_income_requirement_multiplier)->toBe($property->getDefaultDisposableIncomeRequirementMultiplier());
    expect($data->default_disposable_income_requirement_multiplier)->toBe($property->getDefaultDisposableIncomeRequirementMultiplier());
});

dataset('borrower-30yo-25k_gmi', function () {
    return [
        fn () => tap(Mock(BorrowerInterface::class), function (MockInterface $mock) {
            $mock->shouldReceive('getRegional')->andReturn(true);
            $mock->shouldReceive('getGrossMonthlyIncome')->andReturn(new Price(Money::of(12000, 'PHP')));
        }),
    ];
});

dataset('guess-interest-rates', function () {
    return [
        fn () => ['regional' => true,  'gmi' => 12000, 'tcp' => 749999,  'guess_interest_rate' => 0.030],
        fn () => ['regional' => false, 'gmi' => 12000, 'tcp' => 749999,  'guess_interest_rate' => 0.030],
        fn () => ['regional' => true,  'gmi' => 12000, 'tcp' => 750000,  'guess_interest_rate' => 0.030],
        fn () => ['regional' => false, 'gmi' => 12000, 'tcp' => 750000,  'guess_interest_rate' => 0.030],

        fn () => ['regional' => true,  'gmi' => 14500, 'tcp' => 749999,  'guess_interest_rate' => 0.0625],
        fn () => ['regional' => false, 'gmi' => 14500, 'tcp' => 749999,  'guess_interest_rate' => 0.030],
        fn () => ['regional' => true,  'gmi' => 14500, 'tcp' => 750000,  'guess_interest_rate' => 0.0625],
        fn () => ['regional' => false, 'gmi' => 14500, 'tcp' => 750000,  'guess_interest_rate' => 0.030],

        fn () => ['regional' => true,  'gmi' => 13000, 'tcp' => 799999,  'guess_interest_rate' => 0.030],
        fn () => ['regional' => false, 'gmi' => 13000, 'tcp' => 799999,  'guess_interest_rate' => 0.030],
        fn () => ['regional' => true,  'gmi' => 13000, 'tcp' => 800000,  'guess_interest_rate' => 0.030],
        fn () => ['regional' => false, 'gmi' => 13000, 'tcp' => 800000,  'guess_interest_rate' => 0.030],

        fn () => ['regional' => true,  'gmi' => 15500, 'tcp' => 799999,  'guess_interest_rate' => 0.0625],
        fn () => ['regional' => false, 'gmi' => 15500, 'tcp' => 799999,  'guess_interest_rate' => 0.030],
        fn () => ['regional' => true,  'gmi' => 15500, 'tcp' => 800000,  'guess_interest_rate' => 0.0625],
        fn () => ['regional' => false, 'gmi' => 15500, 'tcp' => 800000,  'guess_interest_rate' => 0.030],

        fn () => ['regional' => true,  'gmi' => 15000, 'tcp' => 849999,  'guess_interest_rate' => 0.030],
        fn () => ['regional' => false, 'gmi' => 15000, 'tcp' => 849999,  'guess_interest_rate' => 0.030],
        fn () => ['regional' => true,  'gmi' => 15001, 'tcp' => 849999,  'guess_interest_rate' => 0.0625],
        fn () => ['regional' => false, 'gmi' => 15001, 'tcp' => 849999,  'guess_interest_rate' => 0.030],

        fn () => ['regional' => true,  'gmi' => 16500, 'tcp' => 849999,  'guess_interest_rate' => 0.0625],
        fn () => ['regional' => false, 'gmi' => 16500, 'tcp' => 849999,  'guess_interest_rate' => 0.030],
        fn () => ['regional' => true,  'gmi' => 16501, 'tcp' => 849999,  'guess_interest_rate' => 0.0625],
        fn () => ['regional' => false, 'gmi' => 16501, 'tcp' => 849999,  'guess_interest_rate' => 0.0625],

        fn () => ['regional' => true,  'gmi' => 16500, 'tcp' => 850000,  'guess_interest_rate' => 0.0625],
        fn () => ['regional' => false, 'gmi' => 16500, 'tcp' => 850000,  'guess_interest_rate' => 0.030],
        fn () => ['regional' => true,  'gmi' => 16501, 'tcp' => 850000,  'guess_interest_rate' => 0.0625],
        fn () => ['regional' => false, 'gmi' => 16501, 'tcp' => 850000,  'guess_interest_rate' => 0.0625],

        fn () => ['regional' => true,  'gmi' => 16500, 'tcp' => 850001,  'guess_interest_rate' => 0.0625],
        fn () => ['regional' => false, 'gmi' => 16500, 'tcp' => 850001,  'guess_interest_rate' => 0.0625],

        fn () => ['regional' => true,  'gmi' => 16500, 'tcp' => 2500000, 'guess_interest_rate' => 0.0625],
        fn () => ['regional' => false, 'gmi' => 16500, 'tcp' => 2500000, 'guess_interest_rate' => 0.0625],

        fn () => ['regional' => true,  'gmi' => 16500, 'tcp' => 2500001, 'guess_interest_rate' => 0.0700],
        fn () => ['regional' => false, 'gmi' => 16500, 'tcp' => 2500001, 'guess_interest_rate' => 0.0700],
    ];
});

it('has default interest rate per market segment', function (array $params) {
    $property = new Property;
    $borrower = tap(Mock(BorrowerInterface::class), function (MockInterface $mock) use ($params) {
        $mock
            ->shouldReceive('getRegional')->andReturn($params['regional'])
            ->shouldReceive('getGrossMonthlyIncome')->andReturn(new Price(Money::of($params['gmi'], 'PHP')));
    });
    expect($property->setTotalContractPrice(new Price(Money::of($params['tcp'], 'PHP')))->getDefaultAnnualInterestRateFromBorrower($borrower))
        ->toBe($params['guess_interest_rate']);
})->with('guess-interest-rates');

it('can also accept Money or float in setTotalContractPrice and setAppraisedValue', function () {
    $property = new Property;
    $property->setTotalContractPrice(Money::of(750000, 'PHP'))->setAppraisedValue(Money::of(700000, 'PHP'));
    expect($property->getTotalContractPrice()->inclusive()->compareTo(750000))->toBe(0);
    expect($property->getAppraisedValue()->inclusive()->compareTo(700000))->toBe(0);
    $property->setTotalContractPrice(1200000)->setAppraisedValue(1000000);
    expect($property->getTotalContractPrice()->inclusive()->compareTo(1200000))->toBe(0);
    expect($property->getAppraisedValue()->inclusive()->compareTo(1000000))->toBe(0);
});

/* SIMULATIONS */

dataset('market segment simulation', function () {
    return [
        fn() => [Input::TCP => 750000, Assert::MARKET_SEGMENT => MarketSegment::SOCIALIZED],
        fn() => [Input::TCP => 849999, Assert::MARKET_SEGMENT => MarketSegment::SOCIALIZED],
        fn() => [Input::TCP => 850000, Assert::MARKET_SEGMENT => MarketSegment::SOCIALIZED],
        fn() => [Input::TCP => 850001, Assert::MARKET_SEGMENT => MarketSegment::ECONOMIC],
        fn() => [Input::TCP => 2499999, Assert::MARKET_SEGMENT => MarketSegment::ECONOMIC],
        fn() => [Input::TCP => 2500000, Assert::MARKET_SEGMENT => MarketSegment::ECONOMIC],
        fn() => [Input::TCP => 2500001, Assert::MARKET_SEGMENT => MarketSegment::OPEN],
    ];
});

it('can derive market segment from tcp', function (array $params) {
    $property = new Property;
    expect($property->setTotalContractPrice($params[Input::TCP])->getMarketSegment())->toBe($params[Assert::MARKET_SEGMENT]);
})->with('market segment simulation');

dataset('price ceiling simulation', function () {
    return [
        fn() => [Input::MARKET_SEGMENT => MarketSegment::OPEN, Assert::PRICE_CEILING => 10000000],
        fn() => [Input::MARKET_SEGMENT => MarketSegment::ECONOMIC, Assert::PRICE_CEILING => 2500000],
        fn() => [Input::MARKET_SEGMENT => MarketSegment::SOCIALIZED, Input::WORK_AREA => WorkArea::HUC, Assert::PRICE_CEILING => 850000],
        fn() => [Input::MARKET_SEGMENT => MarketSegment::SOCIALIZED, Input::WORK_AREA => WorkArea::REGION, Assert::PRICE_CEILING => 750000],
        fn() => [Input::HOUSING_TYPE => HousingType::CONDOMINIUM, Input::STOREYS => 4, Input::FLOOR_AREA => 22.0, Assert::PRICE_CEILING => 933320],
        fn() => [Input::HOUSING_TYPE => HousingType::CONDOMINIUM, Input::STOREYS => 4, Input::FLOOR_AREA => 25.0, Assert::PRICE_CEILING => 1060591],
        fn() => [Input::HOUSING_TYPE => HousingType::CONDOMINIUM, Input::STOREYS => 4, Input::FLOOR_AREA => 25.1, Assert::PRICE_CEILING => 1145438],
        fn() => [Input::HOUSING_TYPE => HousingType::CONDOMINIUM, Input::STOREYS => 9, Input::FLOOR_AREA => 22.0, Assert::PRICE_CEILING => 1000000],
        fn() => [Input::HOUSING_TYPE => HousingType::CONDOMINIUM, Input::STOREYS => 9, Input::FLOOR_AREA => 25.0, Assert::PRICE_CEILING => 1136364],
        fn() => [Input::HOUSING_TYPE => HousingType::CONDOMINIUM, Input::STOREYS => 9, Input::FLOOR_AREA => 25.1, Assert::PRICE_CEILING => 1227273],
        fn() => [Input::HOUSING_TYPE => HousingType::CONDOMINIUM, Input::STOREYS => 10, Input::FLOOR_AREA => 22.0, Assert::PRICE_CEILING => 1320000],
        fn() => [Input::HOUSING_TYPE => HousingType::CONDOMINIUM, Input::STOREYS => 10, Input::FLOOR_AREA => 25.0, Assert::PRICE_CEILING => 1500000],
        fn() => [Input::HOUSING_TYPE => HousingType::CONDOMINIUM, Input::STOREYS => 10, Input::FLOOR_AREA => 25.1, Assert::PRICE_CEILING => 1620000],
    ];
});

it('can derive price ceiling', function (array $params) {
    $property = new Property;
    if (isset($params[Input::MARKET_SEGMENT]))
    $property->setMarketSegment($params[Input::MARKET_SEGMENT]);
    if (isset($params[Input::WORK_AREA]))
        $property->setWorkArea($params[Input::WORK_AREA]);
    if (isset($params[Input::HOUSING_TYPE]))
        $property->setHousingType($params[Input::HOUSING_TYPE]);
    if (isset($params[Input::STOREYS]))
        $property->setStoreys($params[Input::STOREYS]);
    if (isset($params[Input::FLOOR_AREA]))
        $property->setFloorArea($params[Input::FLOOR_AREA]);
    expect($property->getPriceCeiling()->inclusive()->compareTo($params[Assert::PRICE_CEILING]))->toBe(Amount::EQUAL);

})->with('price ceiling simulation');
