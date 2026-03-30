<?php
namespace BitApps\WPValidator;

use BitApps\WPValidator\Exception\MethodNotFoundException;
use BitApps\WPValidator\Exception\RuleErrorException;

class Validator
{
    use Helpers, SanitizationMethods;

    private $errorBag;

    private $inputContainer;

    private $validated = [];

    private $_customMessages = [];

    private $_attributeLabels = [];

    private $_data = [];

    public function make($data, $ruleFields, $customMessages = null, $attributeLabels = null)
    {
        $this->_data            = $data;
        $this->_customMessages  = $customMessages;
        $this->_attributeLabels = $attributeLabels;

        $this->inputContainer = new InputDataContainer($data);

        $this->errorBag = new ErrorBag();

        foreach ($ruleFields as $field => $rules) {
            $this->processAndValidateField($field, $rules);
        }

        return $this;
    }

    public function processAndValidateField($field, $rules): void
    {
        $attributeLabel = $field;

        $fieldKeys = $this->processWildcardFieldKey($field);

        foreach ($fieldKeys as $fieldKey) {
            $this->validateField($fieldKey, $rules, $attributeLabel);
        }
    }

    public function processWildcardFieldKey($field): array
    {
        if (strpos($field, '*') === false) {
            return [$field];
        }

        $nestedKeyQueue   = explode('.', $field);
        $visitedFieldKeys = [];
        $dataByKey        = (array) $this->_data;

        while ($head = array_shift($nestedKeyQueue)) {
            if (trim($head) === '*') {
                $keys      = array_keys((array) $dataByKey);
                $dataByKey = count($keys) && \array_key_exists($keys[0], $dataByKey) ? $dataByKey[$keys[0]] : [];
            } else {
                $keys      = [$head];
                $dataByKey = \array_key_exists($head, $dataByKey) ? $dataByKey[$head] : [];
            }

            if ($visitedFieldKeys === []) {
                foreach ($keys as $keyToVisit) {
                    $visitedFieldKeys[$keyToVisit] = 1;
                }
            } else {
                foreach (array_keys($visitedFieldKeys) as $key) {
                    foreach ($keys as $keyToVisit) {
                        unset($visitedFieldKeys[$key]);
                        $visitedFieldKeys["{$key}.{$keyToVisit}"] = 1;
                    }
                }
            }
        }

        return array_keys($visitedFieldKeys);
    }

    public function validateField($fieldKey, $rules, $fieldLabel): void
    {
        $attributeLabel = isset($this->_attributeLabels[$fieldLabel]) ? $this->_attributeLabels[$fieldLabel] : $fieldKey;

        $this->inputContainer->setAttributeKey($fieldKey);

        $this->inputContainer->setAttributeLabel($attributeLabel);

        $value = $this->inputContainer->getAttributeValue();

        if (\in_array('nullable', $rules) && $this->isEmpty($value)) {
            $this->setValidatedData($fieldKey, $this->inputContainer->getData(), $value);
            return;
        }

        $this->validateByRules($fieldKey, $value, $rules);
        $this->setValidatedData($fieldKey, $this->inputContainer->getData(), $this->inputContainer->getAttributeValue());
    }

    public function validateByRules($fieldKey, $value, $rules): void
    {
        $validationRules = [];

        foreach ($rules as $ruleName) {
            if (\is_string($ruleName) && strpos($ruleName, 'sanitize') !== false) {
                $value = $this->applyFilter($ruleName, $value);
                $this->inputContainer->setAttributeValue($value);
            } else {
                $validationRules[] = $ruleName;
            }
        }

        foreach ($validationRules as $ruleName) {
            if (is_subclass_of($ruleName, Rule::class)) {
                $ruleClass = \is_object($ruleName) ? $ruleName : new $ruleName();
            } else {
                list($ruleName, $paramValues) = $this->parseRule($ruleName);
                $ruleClass                    = $this->resolveRule($ruleName);
            }

            $ruleClass->setInputDataContainer($this->inputContainer);
            $ruleClass->setRuleName($ruleName);

            if (! empty($paramValues)) {
                $ruleClass->setParameterValues($ruleClass->getParamKeys(), $paramValues);
            }

            $isValidated = $ruleClass->validate($this->inputContainer->getAttributeValue());

            if (! $isValidated) {
                $this->errorBag->addError($ruleClass, $this->_customMessages);

                break;
            }

            if ($ruleName === 'present' && $this->isEmpty($value)) {
                break;
            }
        }
    }

    public function fails(): bool
    {
        return ! empty($this->errorBag->getErrors());
    }

    public function errors()
    {
        return $this->errorBag->getErrors();
    }

    public function validated()
    {
        return empty($this->errors()) ? $this->validated : $this->errors();
    }

    private function resolveRule($ruleName)
    {
        if (! \is_string($ruleName)) {
            throw new RuleErrorException('Rule name must be string ');
        }

        $ruleClass = __NAMESPACE__
        . '\\Rules\\'
        . str_replace(' ', '', ucwords(str_replace('_', ' ', $ruleName)))
            . 'Rule';

        if (! class_exists($ruleClass)) {
            throw new RuleErrorException(sprintf('Unsupported validation rule: %s.', $ruleName));
        }

        return new $ruleClass();
    }

    private function parseRule($rule): array
    {
        $exp      = explode(':', $rule, 2);
        $ruleName = $exp[0];
        $params   = [];

        if (isset($exp[1])) {
            $params = explode(',', $exp[1]);
        }

        return [$ruleName, $params];
    }

    private function applyFilter($sanitize, $value)
    {
        $data = explode('|', $sanitize);

        $sanitizeName = isset($data[0]) ? explode(':', $data[0]) : [];
        $params       = isset($data[1]) ? explode(',', $data[1]) : [];

        if (\count($sanitizeName) === 2) {
            list($prefix, $suffix) = $sanitizeName;
            $sanitizationMethod    = $prefix . str_replace('_', '', ucwords($suffix, '_'));

            if (! method_exists($this, $sanitizationMethod)) {
                throw new MethodNotFoundException($sanitizationMethod);
            }

            if ($sanitizationMethod === 'sanitizeWpkses' && ! empty($params)) {
                $value = $this->{$sanitizationMethod}($value, $params);
            } else {
                $value = $this->{$sanitizationMethod}($value);
            }
        }

        return $value;
    }

    private function setValidatedData($field, $data, $value): void
    {
        $keys = explode('.', trim($field, '[]'));

        if (\count($keys) > 1 && $this->isNestedKeyExists($data, $keys)) {
            $this->setNestedElement($this->validated, $keys, $value);
        }
        if (\array_key_exists($field, $data)) {
            $this->validated[$field] = $value;
        }
    }
}
