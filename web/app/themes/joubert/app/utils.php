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
    // Convertir la première lettre en minuscule
    $string = lcfirst($string);

    // Ajouter un tiret avant chaque lettre majuscule précédée d'une lettre minuscule
    $string = preg_replace('/([a-z])([A-Z])/', '$1-$2', $string);

    // Remplacer les espaces par des tirets
    $string = str_replace(' ', '-', $string);

    // Retirer les accents
    $string = iconv('UTF-8', 'ASCII//TRANSLIT', $string);

    // Supprimer les caractères non alphanumériques
    $string = preg_replace('/[^a-zA-Z0-9-]/', '', $string);

    // Convertir toutes les lettres en minuscules
    $string = strtolower($string);

    return $string;
}
