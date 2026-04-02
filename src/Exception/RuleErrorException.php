<?php

namespace BitApps\WPValidator\Exception;

use Exception;

class RuleErrorException extends \Exception

{
    public function __construct($ruleName, $code = 0, ?Exception $previous = null)
    {
        parent::__construct(sprintf("Unsupported validation rule: %s.", $ruleName), $code, $previous);
    }
}
