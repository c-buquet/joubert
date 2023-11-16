<?php
if ( ! defined('ABSPATH' ) ) exit;
/* Free/Pro Class name Change*/
class Folders_Pro_Size_Meta {
    private $meta_key;

    public function __construct() {
        $this->meta_key = "folders_media_size";
        add_filter('admin_init', array($this, 'admin_init' ) );
    }

    public function admin_init() {
        if(isset($_GET['page']) && $_GET['page'] == "folders-media-cleaning") {
            $args = [
                'post_type' => 'attachment',
                'posts_per_page' => -1,
                'post_status' => 'inherit',
                'fields' => 'ids',
                'meta_query' => [
                    [
                        'key' => $this->meta_key,
                        'compare' => 'NOT EXISTS'
                    ]
                ]
            ];
            $result = new WP_Query($args);
            if(count($result->posts) > 0) {
                foreach($result->posts as $media_id) {
                    $this->get_file_size($media_id);
                }
            }
        }
    }

    public function get_file_size( $media_id ) {
        $file_size = 0;
        $meta_data = wp_get_attachment_metadata($media_id);
        if(isset($meta_data['filesize'])) {
            $file_size = $meta_data['filesize'];
        }
        if(isset($meta_data['sizes'])) {
            foreach($meta_data['sizes'] as $size) {
                if(isset($size['filesize'])) {
                    $file_size += intval($size['filesize']);
                }
            }
        }
        if(empty($file_size)) {
            $is_file_exist = file_exists(get_attached_file( $media_id ) );
            $file_size     = 0;
            if ( $is_file_exist ) {
                $file_size = filesize(get_attached_file( $media_id ) );
            }
        }
        update_post_meta( $media_id, $this->meta_key, $file_size );
        return $file_size;
    }
}
if(class_exists("Folders_Pro_Size_Meta")) {
    $Folders_Pro_Size_Meta = new Folders_Pro_Size_Meta();
}