<?php
namespace BitApps\WPValidator\Rules;

use BitApps\WPValidator\Helpers;
use BitApps\WPValidator\Rule;

class ImageRule extends Rule
{
    use Helpers;

    protected $message = "The :attribute must be an image";

    public function validate($value)
    {
        if ($this->isEmpty($value)) {
            return false;
        }

        $fileInfo = $this->getFileInfo($value);
        if (! $fileInfo || empty($fileInfo['type'])) {
            return false;
        }

        return in_array($fileInfo['type'], $this->getImageMimeTypes(), true);
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
            return [
                'name' => basename($filePath),
                'type' => $checked['type'] ?? '',
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

    private function getImageMimeTypes(): array
    {
        $imageExtensions = wp_get_ext_types()['image'] ?? [];
        $imageMimes      = [];
        foreach (wp_get_mime_types() as $exts => $mime) {
            foreach (explode('|', $exts) as $ext) {
                if (in_array($ext, $imageExtensions, true)) {
                    $imageMimes[] = $mime;
                    break;
                }
            }
        }

        return array_values(array_unique($imageMimes));
    }

    public function message()
    {
        return $this->message;
    }
}

