<?php

namespace Homeful\Property\Data;

use Homeful\Property\Classes\Appraisal;
use Spatie\LaravelData\Data;

class AppraisalData extends Data
{
    public function __construct(
        public float $lot_appraisal,
        public float $house_appraisal,
        public float $total_appraisal,
    ){}

    public static function fromObject(Appraisal $appraisal): self
    {
        return new self(
            lot_appraisal: $appraisal->getLotAppraisal()->inclusive()->getAmount()->toFloat(),
            house_appraisal: $appraisal->getHouseAppraisal()->inclusive()->getAmount()->toFloat(),
            total_appraisal: $appraisal->getTotalAppraisal()->inclusive()->getAmount()->toFloat()
        );
    }
}
