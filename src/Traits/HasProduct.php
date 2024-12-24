<?php

namespace Homeful\Property\Traits;

use Whitecube\Price\Price;
use Brick\Money\Money;

trait HasProduct
{
    protected string $sku = '';
    protected Price $processing_fee;
    protected float $percent_dp;
    protected int $dp_term;
    protected float $percent_mf;

    public function getSKU(): string
    {
        return $this->sku;
    }

    public function setSKU(string $value): self
    {
        $this->sku = $value;

        returN $this;
    }

    public function getProcessingFee(): Price
    {
        return $this->processing_fee ?? new Price(Money::of(config('property.default.processing_fee'), 'PHP'));
    }

    public function setProcessingFee(Price|float $value): self
    {
        $this->processing_fee = $value instanceof Price
            ? $value
            : new Price(Money::of($value, 'PHP'))
        ;

        return $this;
    }

    public function getPercentDownPayment(): float
    {
        return $this->percent_dp ?? config('property.default.percent_dp');
    }

    public function setPercentDownPayment(float $value): self
    {
        $this->percent_dp = $value;

        return $this;
    }

    public function getDownPaymentTerm(): int
    {
        return $this->dp_term ?? config('property.default.dp_term');
    }

    public function setDownPaymentTerm(int $value): self
    {
        $this->dp_term = $value;

        return $this;
    }

    public function getPercentMiscellaneousFees(): float
    {
        return $this->percent_mf ?? config('property.default.percent_mf');
    }

    public function setPercentMiscellaneousFees(float $value): self
    {
        $this->percent_mf = $value;

        return $this;
    }
}
