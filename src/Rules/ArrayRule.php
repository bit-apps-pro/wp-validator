<?php
namespace BitApps\WPValidator\Rules;

use BitApps\WPValidator\Rule;

class ArrayRule extends Rule
{
    private $message = "The :attribute must be array";

    public function validate($value): bool
    {
        return is_array($value);
    }

    public function message()
    {
        return $this->message;
    }
}
