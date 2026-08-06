<?php
namespace BitApps\WPValidator\Rules;

use BitApps\WPValidator\Rule;

class IP6Rule extends Rule
{
    private $message = "The :attribute must be a valid IPv6 address";

    public function validate($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false;
    }

    public function message()
    {
        return $this->message;
    }
}
