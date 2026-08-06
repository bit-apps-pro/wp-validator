<?php

namespace BitApps\WPValidator;

use stdClass;

trait Helpers
{
    protected function isEmpty($val)
    {
        if (empty($val) && !in_array($val, ['0', 0, 0.0, false], true)) {
            return true;
        }

        return false;
    }

    protected function getValueLength($value)
    {
        if (is_int($value) || is_float($value)) {
            $value = $value;
        } elseif (is_string($value)) {
            $value = mb_strlen($value, 'UTF-8');
        } elseif (is_array($value)) {
            $value = count($value);
        } else {
            return false;
        }

        return $value;

    }

    public function setNestedElement(&$data, $keys, $value)
    {
        $current = &$data;

        foreach ($keys as $key) {
            if (is_null($current)) {
                $current = [];
            }

            if (is_array($current) && !isset($current[$key])) {
                $current[$key] = [];
            } elseif (is_object($current) && !isset($current->$key)) {
                $current->$key = new stdClass();
            }

            if (is_array($current)) {
                $current = &$current[$key];
            } else {
                $current = &$current->$key;
            }
        }

        $current = $value;
        return $current;
    }

    public function getValueFromPath($keys, $data)
    {
        $counter = 0;
        while (count($keys) > $counter) {
            $path = $keys[$counter];
            if (is_object($data)) {
                $data = (array) $data;
            }
            if (isset($data[$path])) {
                $data = $data[$path];
            } else {
                return null;
            }
            $counter++;
        }
        return $data;
    }

    public function isNestedKeyExists($data, $keys)
    {
        foreach ($keys as $key) {
            if (\is_object($data) && isset($data->$key)) {
                $data = $data->$key;
            }

            if (\is_array($data) && \array_key_exists($key, $data)) {
                $data = $data[$key];
            } else {
                return false;
            }
        }

        return true;
    }
}
