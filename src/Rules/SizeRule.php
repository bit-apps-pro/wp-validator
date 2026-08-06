<?php
namespace BitApps\WPValidator\Rules;

use BitApps\WPValidator\Rule;

class SizeRule extends Rule
{
    private $message = "The :attribute field must be :size characters";

    protected $requireParameters = ['size'];

    public function validate($value): bool
    {
        $this->checkRequiredParameter($this->requireParameters);

        $size = $this->getParameter('size');

        if (is_string($value)) {
            return strlen($value) == $size;
        }
        if (is_int($value)) {
            return $value === $size;
        }

        if (is_array($value)) {
            return count($value) === $size;
        }
        return false;
    }

    public function getParamKeys()
    {
        return $this->requireParameters;
    }

    public function message()
    {
        return $this->message;
    }
}
