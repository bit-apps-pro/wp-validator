<?php
namespace BitApps\WPValidator\Rules;

use BitApps\WPValidator\Helpers;
use BitApps\WPValidator\Rule;

class EncodingRule extends Rule
{
    use Helpers;

    protected $message = "The :attribute must be encoded as one of: :encoding";

    protected $requireParameters = ['encoding'];

    public function validate($value)
    {
        $this->checkRequiredParameter($this->requireParameters);

        if ($this->isEmpty($value)) {
            return false;
        }

        $allowedEncodings = array_map('trim', explode(',', $this->getParameter('encoding')));

        $filePath = $this->getFilePath($value);
        if (! $filePath) {
            return false;
        }

        $contents = $this->getFileContentForEncodingDetection($filePath);
        if ($contents === false) {
            return false;
        }

        return mb_detect_encoding($contents, $allowedEncodings, true) !== false;
    }

    private function getFileContentForEncodingDetection($filePath)
    {
        if (function_exists('WP_Filesystem') && WP_Filesystem()) {
            global $wp_filesystem;
            $contents = $wp_filesystem->get_contents($filePath);
            return $contents !== false ? substr($contents, 0, 32768) : false;
        }

        return file_get_contents($filePath, false, null, 0, 32768);
    }

    private function getFilePath($value)
    {
        if (is_numeric($value)) {
            $filePath = get_attached_file((int) $value);
            return (! empty($filePath) && file_exists($filePath)) ? $filePath : null;
        }

        if (is_array($value) && isset($value['tmp_name']) && is_uploaded_file($value['tmp_name'])) {
            return $value['tmp_name'];
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
