<?php
if (!function_exists('assetImg')) {
    /**
     * Generate URL for the current asset theme directory
     * @param string $filePath
     * @return string
     */
    function assetImg(string $filePath): string
    {
        return get_template_directory_uri() . '/resources/images/' . $filePath;
    }
}

function toKebabCase($string): string
{
    return strtolower(preg_replace('%([a-z])([A-Z])%', '\1-\2', $string));
}
