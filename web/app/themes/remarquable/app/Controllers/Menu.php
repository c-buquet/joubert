<?php

namespace App\Controllers;

use Sober\Controller\Controller;

class Menu extends Controller
{
    public static $options = [
        'current_classes' => "current-item"
    ];

    public static function make(string $location, $options = []): array
    {
        if (!has_nav_menu($location)) {
            //trigger_error("Le menu " . $location . " n'existe pas. ");
            return [];
        }

        self::$options = array_merge(self::$options, $options);

        return self::build($location);
    }


    private static function build($location)
    {
        $locations = get_nav_menu_locations();
        $menu = wp_get_nav_menu_object($locations[$location]);
        $array_menu = wp_get_nav_menu_items($menu);
        $fields = get_fields($menu);
        $items = [
            'items' => [],
            'acf' => is_array($fields) ? $fields : [],
        ];


        foreach ($array_menu as $item) {
            $item = self::parse($item);
            $item->sub = [];
            $items['items'][$item->ID] = $item;
        }

        foreach ($items['items'] as $item) {
            if ($item->menu_item_parent !== "0") {
                $items['items'][$item->menu_item_parent]->sub[] = $item;
            }
        }

        foreach ($items['items'] as $item) {
            if ($item->menu_item_parent !== "0") {
                unset($items['items'][$item->ID]);
            }
        }

        $items['items'] = array_values($items['items']);

        return $items;
    }

    private static function parse($item)
    {
        $item = self::parseClasses($item);

        return $item;
    }

    private static function parseClasses($item)
    {
        $classes = [];

        $classes[] = ($item->object_id == get_queried_object_id()) ? self::getOption('current_classes') : '';

        $item->classes = implode(' ', array_filter($classes));

        return $item;
    }

    private static function getOption($option)
    {
        return self::$options[$option] ?? null;
    }

    public static function getMenu($location)
    {
        $locations = get_nav_menu_locations();
        return wp_get_nav_menu_object($locations[$location]) ?? null;
    }

    public static function getMenuName($location)
    {
        $locations = get_nav_menu_locations();
        return wp_get_nav_menu_object($locations[$location])->name;
    }
}
