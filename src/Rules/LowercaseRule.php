<?php
namespace BitApps\WPValidator\Rules;

use BitApps\WPValidator\Rule;

class LowercaseRule extends Rule
{
    private $message = "The :attribute must be in lowercase";

    public function validate($value): bool
    {
        return $value === strtolower($value);
    }

    public function message()
    {
        return $this->message;
    }
}
