<?php
namespace BitApps\WPValidator\Rules;

use BitApps\WPValidator\Helpers;
use BitApps\WPValidator\Rule;

class SizeRule extends Rule
{
    use Helpers;
    private $message = "The :attribute field must be :size characters";

    protected $requireParameters = ['size'];

    public function validate($value): bool
    {
        $this->checkRequiredParameter($this->requireParameters);

        $size = (int) $this->getParameter('size');

        if (is_array($value) && isset($value['size'])) {
            return (int) round($value['size'] / 1024) === $size;
        }

        if (is_numeric($value) && function_exists('get_attached_file')) {
            $attachmentId = (int) $value;
            $filePath     = get_attached_file($attachmentId);
            if (! empty($filePath) && file_exists($filePath)) {
                return (int) round($this->getAttachmentSizeBytes($attachmentId, $filePath) / 1024) === $size;
            }
        }

        if (is_string($value)) {
            return strlen($value) === $size;
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
