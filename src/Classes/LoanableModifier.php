<?php

namespace Homeful\Property\Classes;

use Brick\Money\AbstractMoney;
use Brick\Money\Money;
use Homeful\Property\Property;
use Whitecube\Price\PriceAmendable;
use Whitecube\Price\Vat;

class LoanableModifier implements PriceAmendable
{
    protected string $type;

    public function type(): string
    {
        return $this->type;
    }

    protected Property $property;

    public function __construct(Property $property)
    {
        $this->property = $property;
    }

    /**
     * @return $this
     */
    public function setType(?string $type = null): static
    {
        $this->type = $type;

        return $this;
    }

    public function key(): ?string
    {
        return 'loanable';
    }

    public function attributes(): ?array
    {
        return [
            'market_segment' => $this->property->getMarketSegment()->getName(),
            'default_loanable_value_multiplier' => $this->property->getDefaultLoanableValueMultiplier(),
            'loanable_value_multiplier' => $this->property->getLoanableValueMultiplier(),
        ];
    }

    public function appliesAfterVat(): bool
    {
        return false;
    }

    /**
     * @throws \Brick\Math\Exception\MathException
     */
    public function apply(AbstractMoney $build, float $units, bool $perUnit, ?AbstractMoney $exclusive = null, ?Vat $vat = null): ?AbstractMoney
    {
        if ($build instanceof Money) {
            return $build->multipliedBy($this->property->getLoanableValueMultiplier());
        } else {
            return null;
        }
    }
}
