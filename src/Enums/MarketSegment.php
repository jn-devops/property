<?php

namespace Homeful\Property\Enums;

use Whitecube\Price\Price;

enum MarketSegment: string
{
    case OPEN = 'open';
    case ECONOMIC = 'economic';
    case SOCIALIZED = 'socialized';

    public static function fromPrice(Price $total_contract_price, DevelopmentType $developmentType = DevelopmentType::HORIZONTAL): self
    {
        return with($total_contract_price->base()->getAmount()->toFloat(), function ($value) use ($developmentType) {
            return match ($developmentType) {
                DevelopmentType::HORIZONTAL => match (true) {
                    $value <= config('property.market.ceiling.horizontal.socialized') => MarketSegment::SOCIALIZED,
                    $value <= config('property.market.ceiling.horizontal.economic') => MarketSegment::ECONOMIC,
                    default => MarketSegment::OPEN
                },
                DevelopmentType::VERTICAL => match (true) {
                    $value <= config('property.market.ceiling.vertical.socialized') => MarketSegment::SOCIALIZED,
                    $value <= config('property.market.ceiling.vertical.economic') => MarketSegment::ECONOMIC,
                    default => MarketSegment::OPEN
                },
            };
        });
    }

    public function defaultDisposableIncomeRequirementMultiplier(): float
    {
        return match ($this) {
            self::OPEN => config('property.market.disposable-income-requirement-multiplier.open', 0.30), // 30%
            self::ECONOMIC => config('property.market.disposable-income-requirement-multiplier.economic', 0.35), //35%
            self::SOCIALIZED => config('property.market.disposable-income-requirement-multiplier.socialized', 0.35), //35%
        };
    }

    public function defaultLoanableValueMultiplier(): float
    {
        return match ($this) {
            self::OPEN => config('property.market.loanable-value-multiplier.open', 0.90), // 90%
            self::ECONOMIC => config('property.market.loanable-value-multiplier.economic', 0.95), //95%
            self::SOCIALIZED => config('property.market.loanable-value-multiplier.socialized', 1.00), //100%
        };
    }
}
