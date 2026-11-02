<?php
namespace BitApps\WPValidator;

class InputDataContainer
{
    use Helpers;

    private $data = [];

    private $attributeKey = '';

    private $attributeLabel;

    public function __construct($data = [])
    {
        $this->data = $data;
    }

    public function setAttributeKey($key): void
    {
        $this->attributeKey = $key ?? '';
    }

    public function getAttributeKey()
    {
        return $this->attributeKey;
    }

    public function getAttributeValue($key = null)
    {
        $keys = explode('.', trim($this->attributeKey, '[]'));

        if (count($keys) > 1) {
            return $this->getValueFromPath($keys, $this->data);
        }

        if ($key !== null && isset($this->data[$key])) {
            return $this->data[$key];
        }

        return $this->data[$this->attributeKey] ?? null;
    }

    public function setAttributeLabel($value): void
    {
        $this->attributeLabel = $value;
    }

    public function getAttributeLabel()
    {
        return $this->attributeLabel;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setAttributeValue($value): void
    {
        $keys = explode('.', trim($this->attributeKey, '[]'));

        if (\count($keys) > 1) {
            $this->setNestedElement($this->data, $keys, $value);
        } else {
            $this->data[$this->attributeKey] = $value;
        }
    }
}
