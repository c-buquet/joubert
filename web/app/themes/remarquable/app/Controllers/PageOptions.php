<?php

namespace App\Controllers;

use Sober\Controller\Controller;

class PageOptions extends Controller
{
     /**
     *
     * @return array
     */
    public static function getFooterDatas(): array
    {
        $FOOTER_DATAS = [];
        if (function_exists('get_field')) {
            $FOOTER_DATAS = [
                'logo' => get_field('logo', 'option') ? get_field('logo', 'option') : [],
                'slogan' => get_field('slogan', 'option') ? get_field('slogan', 'option') : [],
            ];

            return $FOOTER_DATAS;
        }
    }
}

