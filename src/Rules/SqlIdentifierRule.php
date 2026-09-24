<?php

namespace BitApps\WPValidator\Rules;

use BitApps\WPValidator\Rule;

class SqlIdentifierRule extends Rule
{
    private $message = 'The :attribute must be a valid SQL identifier';

    public function validate($value)
    {
        return is_string($value)
            && preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/D', $value) === 1;
    }

    public function message()
    {
        return $this->message;
    }
}
