<?php

use Homeful\Property\Data\AppraisalData;
use Homeful\Property\Classes\Appraisal;
use Homeful\Common\Classes\Amount;

it('requires home and lot appraisals', function () {
    $appraisal = new Appraisal(147000, 695000);
    expect($appraisal->getLotAppraisal()->inclusive()->compareTo(147000))->toBe(Amount::EQUAL);
    expect($appraisal->getHouseAppraisal()->inclusive()->compareTo(695000))->toBe(Amount::EQUAL);
    expect($appraisal->getTotalAppraisal()->inclusive()->compareTo(147000 + 695000))->toBe(Amount::EQUAL);
});

it('has data', function () {
    $appraisal = new Appraisal(147000, 695000);
    $data = AppraisalData::fromObject($appraisal);
    expect($data->lot_appraisal)->toBe($appraisal->getLotAppraisal()->inclusive()->getAmount()->toFloat());
    expect($data->house_appraisal)->toBe($appraisal->getHouseAppraisal()->inclusive()->getAmount()->toFloat());
    expect($data->total_appraisal)->toBe($appraisal->getTotalAppraisal()->inclusive()->getAmount()->toFloat());
});
