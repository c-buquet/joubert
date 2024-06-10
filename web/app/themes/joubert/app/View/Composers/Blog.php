<?php

namespace App\View\Composers;

use Roots\Acorn\View\Composer;

class Blog extends Composer
{
    public static function getPostQuery($override = []): \WP_Query
    {
        $args = [
            'post_type' => 'post'
        ];

        $args = array_merge($args, $override);

        return new \WP_Query($args);
    }

    public static function getPostTags(Array $args): array
    {
        $postTags = [];

        $tags = array_key_exists('id',$args) ? get_the_tags($args['id']) : get_the_tags();

        if (is_array($tags) || is_object($tags)) {
            foreach ($tags as $tag) {
            $tmp = [
                'title' => $tag->name,
                'slug' => $tag->slug,
                'url' => get_permalink(get_option('page_for_posts')) . "?tag=" . $tag->slug,
            ];
            $postTags[] = $tmp;
        }
        }

        return $postTags;
    }

}
