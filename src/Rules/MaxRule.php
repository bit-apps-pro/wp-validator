<?php
namespace BitApps\WPValidator\Rules;

use BitApps\WPValidator\Helpers;
use BitApps\WPValidator\Rule;

class MaxRule extends Rule
{
    use Helpers;

    protected $message = "The :attribute may not be greater than :max characters";

    protected $requireParameters = ['max'];

    public function validate($value)
    {
        $this->checkRequiredParameter($this->requireParameters);

        $max = (int) $this->getParameter('max');

        return $this->lengthWithin($value, null, $max);
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
