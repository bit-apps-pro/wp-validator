<?php
namespace BitApps\WPValidator\Rules;

use BitApps\WPValidator\Helpers;
use BitApps\WPValidator\Rule;

class ExtensionsRule extends Rule
{
    use Helpers;

    protected $message = "The :attribute must have one of the following extensions: :extensions";

    protected $requireParameters = ['extensions'];

    public function validate($value)
    {
        $this->checkRequiredParameter($this->requireParameters);

        if ($this->isEmpty($value)) {
            return false;
        }

        $allowedExtensions = array_map(function ($e) {
            return strtolower(trim($e));
        }, explode(',', $this->getParameter('extensions')));

        $fileName = $this->getFileName($value);
        if (! $fileName) {
            return false;
        }

        $wpFileType = wp_check_filetype($fileName);
        $extension  = strtolower($wpFileType['ext'] ?? '');

        return ! empty($extension) && in_array($extension, $allowedExtensions, true);
    }

    private function getFileName($value)
    {
        if (is_numeric($value)) {
            $filePath = get_attached_file((int) $value);
            return (! empty($filePath) && file_exists($filePath)) ? basename($filePath) : null;
        }

        if (is_array($value) && isset($value['name'])) {
            return $value['name'];
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
