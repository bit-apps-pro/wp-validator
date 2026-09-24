<?php

namespace BitApps\WPValidator\Rules;

use BitApps\WPValidator\Rule;

class SortDirectionRule extends Rule
{
    private $message = 'The :attribute must be asc or desc';

    public function validate($value)
    {
        return is_string($value)
            && in_array(strtolower($value), ['asc', 'desc'], true);
    }

    public function message()
    {
        return $this->message;
    }
}
