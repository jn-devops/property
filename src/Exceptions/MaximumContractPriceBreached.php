<?php

namespace Homeful\Property\Exceptions;

use Exception;

class MaximumContractPriceBreached extends Exception {
    protected $message = 'Maximum contract price breached!';
}
