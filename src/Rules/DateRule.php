<?php
namespace BitApps\WPValidator\Rules;

use BitApps\WPValidator\Rule;

class DateRule extends Rule
{
    private $message = "The :attribute is not a valid date";

    public function validate($value): bool
    {
        return strtotime($value) !== false;
    }

    public function message()
    {
        return $this->message;
    }
}
