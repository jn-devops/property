<?php

namespace Homeful\Property\Classes;

use Whitecube\Price\PriceAmendable;
use Brick\Money\AbstractMoney;
use Homeful\Property\Property;
use Whitecube\Price\Vat;
use Brick\Money\Money;

class LoanableModifier implements PriceAmendable
{
    /**
     * @var string
     */
    protected string $type;

    /**
     * @return string
     */
    public function type(): string
    {
        return $this->type;
    }

    /**
     * @var Property
     */
    protected Property $property;

    /**
     * @param Property $property
     */
    public function __construct(Property $property)
    {
        $this->property = $property;
    }

    /**
     * @param string|null $type
     * @return $this
     */
    public function setType(?string $type = null): static
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string|null
     */
    public function key(): ?string
    {
        return 'loanable';
    }

    /**
     * @return array|null
     */
    public function attributes(): ?array
    {
        return [
            'market_segment' => $this->property->getMarketSegment()->value,
            'default_loanable_value_multiplier' => $this->property->getDefaultLoanableValueMultiplier(),
            'loanable_value_multiplier' => $this->property->getLoanableValueMultiplier(),
        ];
    }

    /**
     * @return bool
     */
    public function appliesAfterVat(): bool
    {
        return false;
    }

    /**
     * @param AbstractMoney $build
     * @param float $units
     * @param bool $perUnit
     * @param AbstractMoney|null $exclusive
     * @param Vat|null $vat
     * @return AbstractMoney|null
     * @throws \Brick\Math\Exception\MathException
     */
    public function apply(AbstractMoney $build, float $units, bool $perUnit, AbstractMoney $exclusive = null, Vat $vat = null): ?AbstractMoney
    {
        if ($build instanceof Money)
            return $build->multipliedBy($this->property->getLoanableValueMultiplier());
        else
            return null;
    }
}
