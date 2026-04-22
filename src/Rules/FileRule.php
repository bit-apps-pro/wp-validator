<?php
namespace BitApps\WPValidator\Rules;

use BitApps\WPValidator\Helpers;
use BitApps\WPValidator\Rule;

class FileRule extends Rule
{
    use Helpers;

    protected $message = "The :attribute must be a valid file upload";

    public function validate($value)
    {
        if ($this->isEmpty($value)) {
            return false;
        }

        if (is_numeric($value)) {
            $attachmentId = (int) $value;
            $filePath     = get_attached_file($attachmentId);
            return ! empty($filePath) && file_exists($filePath);
        }

        if (is_array($value)) {
            $requiredKeys = array_flip(['name', 'type', 'tmp_name', 'error', 'size']);
            if (array_diff_key($requiredKeys, $value)) {
                return false;
            }

            if ($value['error'] !== UPLOAD_ERR_OK) {
                return false;
            }

            if (empty($value['tmp_name']) || ! is_uploaded_file($value['tmp_name'])) {
                return false;
            }

            return true;
        }

        return false;
    }

    public function message()
    {
        return $this->message;
    }
}
