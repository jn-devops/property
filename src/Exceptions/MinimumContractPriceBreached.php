<?php

namespace Homeful\Property\Exceptions;

use Exception;

class MinimumContractPriceBreached extends Exception {
    protected $message = 'Minimum contract price breached!';
}
