<?php
namespace BitApps\WPValidator\Rules;

use BitApps\WPValidator\Rule;

class SameRule extends Rule
{
    private $message = "The :attribute and :other must match";

    protected $requireParameters = ['other'];

    public function validate($value): bool
    {

        $this->checkRequiredParameter($this->requireParameters);

        $otherValue = $this->getInputDataContainer()->getAttributeValue($this->getParameter('other'));

        return $value === $otherValue;
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
