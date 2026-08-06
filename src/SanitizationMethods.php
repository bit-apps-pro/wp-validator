<?php
namespace BitApps\WPValidator;

trait SanitizationMethods
{
    protected function sanitizeEmail($value)
    {
        return sanitize_email($value);
    }

    protected function sanitizeFileName($value)
    {
        return sanitize_file_name($value);
    }

    protected function sanitizeKey($value)
    {
        return sanitize_key($value);
    }

    protected function sanitizeHtmlClass($value)
    {
        return sanitize_html_class($value);
    }

    protected function sanitizeText($value)
    {
        return sanitize_text_field($value);
    }

    protected function sanitizeTitle($value)
    {
        return sanitize_title($value);
    }

    protected function sanitizeUser($value)
    {
        return sanitize_user($value);
    }

    protected function sanitizeUrl($value)
    {
        return esc_url_raw($value);
    }

    protected function sanitizeTrim($value)
    {
        return is_string($value) ? trim($value) : $value;
    }

    protected function sanitizeEscape($value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    protected function sanitizeCapitalize($value): string
    {
        return ucwords(strtolower($value));
    }

    protected function sanitizeLowercase($value): string
    {
        return strtolower($value);
    }

    protected function sanitizeUppercase($value): string
    {
        return strtoupper($value);
    }

    protected function sanitizeUcfirst($value): string
    {
        return ucfirst($value);
    }

    protected function sanitizeTextarea($value)
    {
        return sanitize_textarea_field($value);
    }

    protected function sanitizeWpksespost($value)
    {
        return wp_kses_post($value);
    }

    protected function sanitizeWpkses($value, $allowedHtml)
    {

        $allowedHtml = $this->convertToNestedArray($allowedHtml);

        return wp_kses($value, $allowedHtml);
    }

    protected function convertToNestedArray($elements): array
    {
        $result = [];

        foreach ($elements as $element) {
            // Split each element by dots to get the hierarchy
            $keys = explode('.', $element);

            // Reference to the current level in the result array
            $currentLevel = &$result;

            foreach ($keys as $key) {
                // Move to the next level in the hierarchy
                if (! isset($currentLevel[$key])) {
                    $currentLevel[$key] = [];
                }
                $currentLevel = &$currentLevel[$key];
            }
        }

        return $result;
    }
}
