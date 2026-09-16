<?php
namespace BitApps\WPValidator\Rules;

use BitApps\WPValidator\Helpers;
use BitApps\WPValidator\Rule;

class MimesRule extends Rule
{
    use Helpers;

    protected $message = "The :attribute must be a file of type: :mimes";

    protected $requireParameters = ['mimes'];

    public function validate($value)
    {
        $this->checkRequiredParameter($this->requireParameters);

        if ($this->isEmpty($value)) {
            return false;
        }

        $allowedMimes = $this->getParameter('mimes');
        if (empty($allowedMimes)) {
            return false;
        }

        $allowedTypes = array_map('trim', explode(',', $allowedMimes));

        $fileInfo = $this->getFileInfo($value);
        if (! $fileInfo) {
            return false;
        }

        if (in_array($fileInfo['type'], $allowedTypes, true)) {
            return true;
        }

        $wpFileType = wp_check_filetype($fileInfo['name']);
        if ($wpFileType && isset($wpFileType['ext'])) {
            return in_array(strtolower($wpFileType['ext']), $allowedTypes, true);
        }

        return false;
    }

    private function getFileInfo($value)
    {
        // Handle attachment ID
        if (is_numeric($value)) {
            $attachmentId = (int) $value;
            $filePath     = get_attached_file($attachmentId);
            if (empty($filePath) || ! file_exists($filePath)) {
                return null;
            }

            $wpFileType = wp_check_filetype(basename($filePath));
            return [
                'name' => basename($filePath),
                'type' => $wpFileType['type'] ?? '',
            ];
        }

        if (is_array($value) && isset($value['name'], $value['tmp_name'])) {
            $checked = wp_check_filetype_and_ext($value['tmp_name'], $value['name']);
            return [
                'name' => $value['name'],
                'type' => $checked['type'] ?? '',
            ];
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
