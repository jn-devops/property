# Homeful Property Package

The **Homeful Property Package** provides a structured way to handle property-related data, including total contract price validation, market segments, housing types, development types, loanable value calculations, fees, and more.

## Table of Contents
- [Installation](#installation)
- [Usage](#usage)
- [Classes](#classes)
- [Traits](#traits)
- [Enumerations](#enumerations)
- [Tests](#tests)

---

## Installation

To install the package, use Composer:

```sh
composer require homeful/property
```

---

## Usage

### Creating a Property Instance

```php
use Homeful\Property\Property;
use Whitecube\Price\Price;
use Brick\Money\Money;

$property = new Property;
$property->setTotalContractPrice(new Price(Money::of(1500000, 'PHP')));
```

### Setting and Retrieving Market Segments

```php
use Homeful\Property\Enums\MarketSegment;

$property->setMarketSegment(MarketSegment::ECONOMIC);
echo $property->getMarketSegment()->getName(); // Outputs "economic"
```

---

## Classes

### Property

The core class representing a property.

#### Properties:
- `total_contract_price`: Total contract price as a `Price` object.
- `appraised_value`: Appraised value as a `Price` object.
- `development_type`: The development type as an `Enum`.
- `housing_type`: The housing type as an `Enum`.
- `market_segment`: The market segment as an `Enum`.
- `work_area`: The work area as an `Enum`.
- `loanable_value_multiplier`: Loanable value multiplier.
- `disposableIncomeRequirementMultiplier`: Disposable income requirement multiplier.
- `storeys`: Number of storeys.
- `floor_area`: Floor area.
- `charges`: Collection of additional property charges.
- `appraisal`: Appraisal details.

#### Methods:
- `setTotalContractPrice(Price|Money|float $value)`
- `getTotalContractPrice(): Price`
- `setMarketSegment(MarketSegment $market_segment)`
- `getMarketSegment(): MarketSegment`
- `setAppraisedValue(Price|Money|float $value)`
- `getAppraisedValue(): Price`
- `setDevelopmentType(DevelopmentType $type)`
- `getDevelopmentType(): DevelopmentType`
- `setHousingType(HousingType $type)`
- `getHousingType(): HousingType`
- `setLoanableValueMultiplier(float $value)`
- `getLoanableValueMultiplier(): float`
- `getLoanableValue(): Price`
- `getDefaultAnnualInterestRateFromBorrower(BorrowerInterface $borrower): float`
- `getPriceCeiling(): Price`
- `addCharge(Charge $charge)`
- `getFees(): Price`
- `getSellingPrice(): Price`

---

## Traits

### HasCalculations
Handles loanable value, interest rates, and disposable income multipliers.

### HasFees
Handles mortgage redemption insurance and annual fire insurance calculations.

### HasProduct
Handles product-related attributes like SKU, processing fees, and down payment terms.

### HasProperties
Handles property attributes such as housing type, total contract price, and work area.

---

## Enumerations

### MarketSegment
Defines the property market segment:
- `OPEN`
- `ECONOMIC`
- `SOCIALIZED`

### DevelopmentType
Defines the type of development:
- `BP_957`
- `BP_220`

### HousingType
Defines the type of housing unit:
- `CONDOMINIUM`
- `DUPLEX`
- `ROW_HOUSE`
- `SINGLE_ATTACHED`
- `SINGLE_DETACHED`
- `QUADRUPLEX`
- `TOWNHOUSE`
- `TWIN_HOMES`

### Charge
Defines property-related charges:
- `PROCESSING_FEE`
- `HOME_UTILITY_CONNECTION_FEE`

---

## Tests

The package includes a series of tests to validate:
- Minimum and maximum total contract price
- Market segment determination
- Housing type assignment
- Development type assignment
- Loanable value calculation
- Price ceiling determination
- Default interest rate based on income and location
- Charge and fee calculations
- Product attributes

To run tests:

```sh
vendor/bin/pest
```
