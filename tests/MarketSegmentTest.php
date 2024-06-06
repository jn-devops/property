<?php

use Homeful\Property\Enums\DevelopmentType;
use Homeful\Property\Enums\MarketSegment;
use Whitecube\Price\Price;

it('can be derived from price and development type', function () {
    expect(MarketSegment::fromPrice(Price::of(849999, 'PHP')))->toBe(MarketSegment::SOCIALIZED);
    expect(MarketSegment::fromPrice(Price::of(850000, 'PHP')))->toBe(MarketSegment::SOCIALIZED);
    expect(MarketSegment::fromPrice(Price::of(900000, 'PHP')))->toBe(MarketSegment::ECONOMIC);
    expect(MarketSegment::fromPrice(Price::of(2499999, 'PHP')))->toBe(MarketSegment::ECONOMIC);
    expect(MarketSegment::fromPrice(Price::of(2500000, 'PHP')))->toBe(MarketSegment::ECONOMIC);
    expect(MarketSegment::fromPrice(Price::of(2500001, 'PHP')))->toBe(MarketSegment::OPEN);

    expect(MarketSegment::fromPrice(Price::of(849999, 'PHP'), DevelopmentType::HORIZONTAL))->toBe(MarketSegment::SOCIALIZED);
    expect(MarketSegment::fromPrice(Price::of(850000, 'PHP'), DevelopmentType::HORIZONTAL))->toBe(MarketSegment::SOCIALIZED);
    expect(MarketSegment::fromPrice(Price::of(900000, 'PHP'), DevelopmentType::HORIZONTAL))->toBe(MarketSegment::ECONOMIC);
    expect(MarketSegment::fromPrice(Price::of(2499999, 'PHP'), DevelopmentType::HORIZONTAL))->toBe(MarketSegment::ECONOMIC);
    expect(MarketSegment::fromPrice(Price::of(2500000, 'PHP'), DevelopmentType::HORIZONTAL))->toBe(MarketSegment::ECONOMIC);
    expect(MarketSegment::fromPrice(Price::of(2500001, 'PHP'), DevelopmentType::HORIZONTAL))->toBe(MarketSegment::OPEN);

    expect(MarketSegment::fromPrice(Price::of(1799999, 'PHP'), DevelopmentType::VERTICAL))->toBe(MarketSegment::SOCIALIZED);
    expect(MarketSegment::fromPrice(Price::of(1800000, 'PHP'), DevelopmentType::VERTICAL))->toBe(MarketSegment::SOCIALIZED);
    expect(MarketSegment::fromPrice(Price::of(1800001, 'PHP'), DevelopmentType::VERTICAL))->toBe(MarketSegment::ECONOMIC);
    expect(MarketSegment::fromPrice(Price::of(2499999, 'PHP'), DevelopmentType::VERTICAL))->toBe(MarketSegment::ECONOMIC);
    expect(MarketSegment::fromPrice(Price::of(2500000, 'PHP'), DevelopmentType::VERTICAL))->toBe(MarketSegment::ECONOMIC);
    expect(MarketSegment::fromPrice(Price::of(2500001, 'PHP'), DevelopmentType::VERTICAL))->toBe(MarketSegment::OPEN);


    expect(MarketSegment::fromPrice(Price::PHP(84999900)))->toBe(MarketSegment::SOCIALIZED);
    expect(MarketSegment::fromPrice(Price::PHP(85000000)))->toBe(MarketSegment::SOCIALIZED);
    expect(MarketSegment::fromPrice(Price::PHP(90000000)))->toBe(MarketSegment::ECONOMIC);
    expect(MarketSegment::fromPrice(Price::PHP(249999900)))->toBe(MarketSegment::ECONOMIC);
    expect(MarketSegment::fromPrice(Price::PHP(250000000)))->toBe(MarketSegment::ECONOMIC);
    expect(MarketSegment::fromPrice(Price::PHP(250000100)))->toBe(MarketSegment::OPEN);
    expect(MarketSegment::fromPrice(Price::PHP(84999900), DevelopmentType::HORIZONTAL))->toBe(MarketSegment::SOCIALIZED);
    expect(MarketSegment::fromPrice(Price::PHP(85000000), DevelopmentType::HORIZONTAL))->toBe(MarketSegment::SOCIALIZED);
    expect(MarketSegment::fromPrice(Price::PHP(90000000), DevelopmentType::HORIZONTAL))->toBe(MarketSegment::ECONOMIC);
    expect(MarketSegment::fromPrice(Price::PHP(249999900), DevelopmentType::HORIZONTAL))->toBe(MarketSegment::ECONOMIC);
    expect(MarketSegment::fromPrice(Price::PHP(250000000), DevelopmentType::HORIZONTAL))->toBe(MarketSegment::ECONOMIC);
    expect(MarketSegment::fromPrice(Price::PHP(250000100), DevelopmentType::HORIZONTAL))->toBe(MarketSegment::OPEN);
});
