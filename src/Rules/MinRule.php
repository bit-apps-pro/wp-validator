<?php
namespace BitApps\WPValidator\Rules;

use BitApps\WPValidator\Helpers;
use BitApps\WPValidator\Rule;

class MinRule extends Rule
{
    use Helpers;

    private $message = "The :attribute must be at least :min characters";

    protected $requireParameters = ['min'];

    public function validate($value)
    {
        $this->checkRequiredParameter($this->requireParameters);

        $min = (int) $this->getParameter('min');

        return $this->lengthWithin($value, $min);
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
