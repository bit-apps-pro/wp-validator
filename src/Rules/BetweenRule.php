<?php
namespace BitApps\WPValidator\Rules;

use BitApps\WPValidator\Helpers;
use BitApps\WPValidator\Rule;

class BetweenRule extends Rule
{
    use Helpers;

    protected $message = "The :attribute must be between :min and :max";

    protected $requireParameters = ['min', 'max'];

    public function validate($value)
    {
        $this->checkRequiredParameter($this->requireParameters);

        $min = (int) $this->getParameter('min');

        $max = (int) $this->getParameter('max');

        $length = $this->getValueLength($value);

        if ($length === false) {
            return false;
        }

        return $length >= $min && $length <= $max;
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
