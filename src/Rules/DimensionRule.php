<?php
namespace BitApps\WPValidator\Rules;

use BitApps\WPValidator\Helpers;
use BitApps\WPValidator\Rule;

class DimensionRule extends Rule
{
    use Helpers;

    protected $message = "The :attribute has invalid image dimensions";

    protected $requireParameters = ['dimensions'];

    public function validate($value)
    {
        $this->checkRequiredParameter($this->requireParameters);

        if ($this->isEmpty($value)) {
            return false;
        }

        $constraints = $this->parseConstraints();
        if (empty($constraints)) {
            return false;
        }

        $dimensions = $this->getDimensions($value);
        if (! $dimensions) {
            return false;
        }

        return $this->checkConstraints($dimensions['width'], $dimensions['height'], $constraints);
    }

    private function parseConstraints(): array
    {
        $raw = $this->getParameter('dimensions');
        if (empty($raw)) {
            return [];
        }

        $constraints = [];
        foreach (explode(',', $raw) as $item) {
            $parts = explode('=', $item, 2);
            if (count($parts) === 2) {
                $constraints[trim($parts[0])] = trim($parts[1]);
            }
        }

        return $constraints;
    }

    private function checkConstraints(int $width, int $height, array $constraints): bool
    {
        $checks = [
            'width'      => static function ($w, $h, $v) { return $w === (int) $v; },
            'height'     => static function ($w, $h, $v) { return $h === (int) $v; },
            'min_width'  => static function ($w, $h, $v) { return $w >= (int) $v; },
            'max_width'  => static function ($w, $h, $v) { return $w <= (int) $v; },
            'min_height' => static function ($w, $h, $v) { return $h >= (int) $v; },
            'max_height' => static function ($w, $h, $v) { return $h <= (int) $v; },
        ];

        foreach ($constraints as $key => $val) {
            if ($key === 'ratio') {
                if (! $this->checkRatio($width, $height, $val)) {
                    return false;
                }
                continue;
            }

            if (isset($checks[$key]) && ! $checks[$key]($width, $height, $val)) {
                return false;
            }
        }

        return true;
    }

    private function checkRatio(int $width, int $height, string $ratio): bool
    {
        if ($height === 0) {
            return false;
        }

        if (strpos($ratio, '/') !== false) {
            list($ratioW, $ratioH) = explode('/', $ratio, 2);
            $expected = (float) $ratioW / (float) $ratioH;
        } else {
            $expected = (float) $ratio;
        }

        return abs(($width / $height) - $expected) < 0.01;
    }

    private function getDimensions($value)
    {
        $filePath = null;

        if (is_numeric($value)) {
            $attachmentId = (int) $value;
            $filePath     = get_attached_file($attachmentId);
            if (empty($filePath) || ! file_exists($filePath)) {
                return null;
            }

            $metadata = wp_get_attachment_metadata($attachmentId);
            if ($metadata && isset($metadata['width'], $metadata['height'])) {
                return [
                    'width'  => (int) $metadata['width'],
                    'height' => (int) $metadata['height'],
                ];
            }
        }

        if (is_array($value) && isset($value['tmp_name'])) {
            if (empty($value['tmp_name']) || ! is_uploaded_file($value['tmp_name'])) {
                return null;
            }
            $filePath = $value['tmp_name'];
        }

        if ($filePath && file_exists($filePath)) {
            $imageSize = @getimagesize($filePath);
            if ($imageSize && isset($imageSize[0], $imageSize[1])) {
                return [
                    'width'  => (int) $imageSize[0],
                    'height' => (int) $imageSize[1],
                ];
            }
        }

        return null;
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
