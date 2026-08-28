<?php
namespace BitApps\WPValidator\Rules;

use BitApps\WPValidator\Helpers;
use BitApps\WPValidator\Rule;

class MaxFileRule extends Rule
{
    use Helpers;

    protected $message = "The :attribute may not be greater than :max kilobytes";

    protected $requireParameters = ['max'];

    public function validate($value)
    {
        $this->checkRequiredParameter($this->requireParameters);

        if ($this->isEmpty($value)) {
            return false;
        }

        $maxSizeKB = (int) $this->getParameter('max');
        $maxSizeBytes = $maxSizeKB * 1024; // Convert KB to bytes

        $fileSize = $this->getFileSize($value);
        if ($fileSize === false) {
            return false;
        }

        return $fileSize <= $maxSizeBytes;
    }

    private function getFileSize($value)
    {
        if (is_numeric($value)) {
            $attachmentId = (int) $value;
            $filePath = get_attached_file($attachmentId);
            if (empty($filePath) || !file_exists($filePath)) {
                return false;
            }

            return $this->getAttachmentSizeBytes($attachmentId, $filePath);
        }

        if (is_array($value) && isset($value['size'])) {
            return (int) $value['size'];
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

