<?php
namespace BitApps\WPValidator\Rules;

use BitApps\WPValidator\Helpers;
use BitApps\WPValidator\Rule;

class MimetypesRule extends Rule
{
    use Helpers;

    protected $message = "The :attribute must be a file of type: :mimetypes";

    protected $requireParameters = ['mimetypes'];

    public function validate($value)
    {
        $this->checkRequiredParameter($this->requireParameters);

        if ($this->isEmpty($value)) {
            return false;
        }

        $allowedMimes = array_map('trim', explode(',', $this->getParameter('mimetypes')));

        $fileInfo = $this->getFileInfo($value);
        if (! $fileInfo || empty($fileInfo['type'])) {
            return false;
        }

        return in_array($fileInfo['type'], $allowedMimes, true);
    }

    private function getFileInfo($value)
    {
        if (is_numeric($value)) {
            $attachmentId = (int) $value;
            $filePath     = get_attached_file($attachmentId);
            if (empty($filePath) || ! file_exists($filePath)) {
                return null;
            }

            $checked = wp_check_filetype_and_ext($filePath, basename($filePath));
            return ['type' => $checked['type'] ?? ''];
        }

        if (is_array($value) && isset($value['name'], $value['tmp_name'])) {
            $checked = wp_check_filetype_and_ext($value['tmp_name'], $value['name']);
            return ['type' => $checked['type'] ?? ''];
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
