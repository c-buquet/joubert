<?php

namespace App\Controllers;

use Sober\Controller\Controller;

class PageOptions extends Controller
{
    /**
     *
     * @return array
     */
    public static function getStickyButtonsOptions(): array
    {
        if (function_exists('get_field')) {
            return get_field('contact_options', 'option') ? get_field('contact_options', 'option') : [];
        }
    }

    /**
     *
     * @return array
     */
    public static function getLogo(): array
    {
        if (function_exists('get_field')) {
            return get_field('logo_desktop', 'option') ? get_field('logo_desktop', 'option') : [];
        }
    }

    /**
     *
     * @return array
     */
    public static function getLogoFooter(): array
    {
        if (function_exists('get_field')) {
            return get_field('logo_footer', 'option') ? get_field('logo_footer', 'option') : [];
        }
    }

     /**
     *
     * @return array
     */
    public static function getFooterDatas(): array
    {
        $FOOTER_DATAS = [];
        if (function_exists('get_field')) {
            $FOOTER_DATAS = [
                'title_first' => get_field('title_first', 'option') ? get_field('title_first', 'option') : [],
                'contenu' => get_field('contenu', 'option') ? get_field('contenu', 'option') : [],
                'socials' => get_field('socials', 'option') ? get_field('socials', 'option') : [],
                'title_secondary' => get_field('title_secondary', 'option') ? get_field('title_secondary', 'option') : [],
                'our_centers' => get_field('our_centers', 'option') ? get_field('our_centers', 'option') : [],
                'title_third' => get_field('title_third', 'option') ? get_field('title_third', 'option') : [],
                'our_preparations' => get_field('our_preparations', 'option') ? get_field('our_preparations', 'option') : [],
            ];

            return $FOOTER_DATAS;
        }
    }

    /**
     *
     * @return array
     */
    public static function getButtonsHeader(): array
    {
        if (function_exists('get_field')) {
            return get_field('buttons', 'option') ? get_field('buttons', 'option') : [];
        }
    }

    /**
     *
     * @return array
     */
    public static function getHeaderDatas(): array
    {
        $HEADER_DATAS = [];
        if (function_exists('get_field')) {
            $HEADER_DATAS = [
                'contact' => get_field('contact', 'option') ? get_field('contact', 'option') : [],
                'contact_email' => get_field('contact_email', 'option') ? get_field('contact_email', 'option') : [],
                'phone' => get_field('phone', 'option') ? get_field('phone', 'option') : [],
            ];

            return $HEADER_DATAS;
        }
    }
}

