<?php
if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}
if (!class_exists('Media_Cleaning_List')) {
    class Media_Cleaning_List extends WP_List_Table
    {

        private $_acf_object_fields = array();

        private $_found_medias = array();

        private $_acf_textual_fields = array();

        private $_acf_gallery_fields = array();

        private $meta_key;

        // Prepare item for the table
        public function prepare_items()
        {
            $this->meta_key = "folders_media_size";

            $columns = $this->get_columns();
            $hidden = $this->get_hidden_columns();
            $sortable = $this->get_sortable_columns();

            $perPage = 20;
            $currentPage = $this->get_pagenum();

            /** Process bulk action */
            $this->process_bulk_action();

            $this->_column_headers = array($columns, $hidden, $sortable);
            $this->items = $this->table_data($perPage, $currentPage);

        }

        public function get_bulk_actions()
        {
            $actions = [
                'bulk-delete' => ((defined("MEDIA_TRASH") && MEDIA_TRASH == true))?esc_html__("Move to Trash","folders"):esc_html__("Delete permanently","folders"),
            ];

            return $actions;
        }

        public function process_bulk_action()
        {
            if (isset($_POST['action']) || isset($_POST['action2'])) {
                $action = esc_attr($_POST['action']);
                $action2 = esc_attr($_POST['action2']);
            }

            // If the delete bulk action is triggered
            if (((isset($action) && $action == 'bulk-delete') || (isset($action2) && $action2 == 'bulk-delete')) && isset($_POST['bulk-delete-scanned-files'])) {

                $delete_ids = esc_sql($_POST['bulk-delete-scanned-files']);

                // loop over the array of record IDs and delete them
                foreach ($delete_ids as $id) {
                    wp_delete_attachment($id);
                }
            }
        }

        //Get the columns for the table
        public function get_columns()
        {
            $columns = array(
                'cb' => '<input type="checkbox" />',
                'title' => _x('File', 'column name'),
                'size' => _x('Size', 'column name'),
                'author' => __('Author'),
                'date' => __('Date'),
            );

            return $columns;
        }

        //Get the hidden columns for the table
        public function get_hidden_columns()
        {
            return array();
        }

        //Get sortable columns for table
        public function get_sortable_columns()
        {
            return array(
                'id' => array('id', false),
                'size' => array('media_size', false),
                'date' => array('date', false)
            );
        }

        //Get data of media files
        private function is_media_files(WP_Post $attachment)
        {
            return ('media-files' === get_post_meta($attachment->ID, '_wp_attached_file', true));
        }

        public function get_media_from_text($post_text)
        {

            preg_match_all('/wp-image-(\d*)/', $post_text, $images);
            if (empty($images)) {
                return [];
            }
            return $images[1];
        }

        /**
         *
         * @param int $post_id
         *
         * @return array Media ids
         */
        public function get_media_from_acf_fields($post_id)
        {
            // ACF PRO is installed and enabled ?
            if (!function_exists('acf_get_field_groups')) {
                return array();
            }

            $new_post = get_post($post_id);
            if (false === $new_post || is_wp_error($new_post)) {
                return array();
            }

            // Get only fields with medias
            $this->_acf_object_fields = array();
            $this->_found_medias = array();

            // Get media possible fields
            $this->recursive_get_post_media_fields(get_field_objects($post_id));

            // Use media fields to get media ids
            $this->recursive_get_post_medias(get_fields($post_id, false));


            // Keep only valid ID && remove zero values
            return array_filter(array_map('intval', $this->_found_medias));
        }

        public function get_acf_gallery_fields($post_id)
        {
            // ACF PRO is installed and enabled ?
            if (!function_exists('acf_get_field_groups')) {
                return array();
            }

            $new_post = get_post($post_id);
            if (false === $new_post || is_wp_error($new_post)) {
                return array();
            }

            // Get only fields with medias
            $this->_acf_gallery_fields = array();

            // Get media possible fields
            $this->recursive_get_post_media_fields(get_field_objects($post_id));


            // Keep only valid ID && remove zero values
            return $this->_acf_gallery_fields;
        }

        /**
         *
         * @param array $fields
         */
        private function recursive_get_post_media_fields($fields)
        {
            if (empty($fields)) {
                return;
            }

            foreach ((array)$fields as $key => $field) {
                if (in_array($field['type'], array('flexible_content'))) {
                    // Flexible is recursive structure with sub_fields into layouts
                    foreach ($field['layouts'] as $layout_field) {
                        $this->recursive_get_post_media_fields($layout_field['sub_fields']);
                    }
                } elseif (in_array($field['type'], ['repeater', 'clone', 'group'])) {
                    // Repeater, Clone and Group fields is a recursive structure with sub_fields
                    $this->recursive_get_post_media_fields($field['sub_fields']);
                } elseif (in_array($field['type'], [
                    'image',
                    'post_object',
                    'relationship',
                    'file',
                    'page_link',
                ])) {
                    // All type of ACF Fields which involve media as object
                    $this->_acf_object_fields[$field['key']] = $field['name'];
                } elseif ($field['type'] == 'gallery') {
                    // All type of ACF Fields which involve media as object
                    $this->_acf_gallery_fields[$field['type']] = $field['name'];
                } elseif (in_array($field['type'], [
                    'wysiwyg',
                    'textarea',
                ])) {
                    // All type of ACF Fields which are textual
                    $this->_acf_textual_fields[$field['key']] = $field['name'];
                }
            }
        }

        /**
         * From media fields, get media ids
         *
         * @param array $fields
         */
        private function recursive_get_post_medias($fields)
        {
            if (!empty($fields)) {
                foreach ($fields as $key => $field) {
                    if (is_array($field)) {
                        // If not final key => field, recursively relaunch
                        $this->recursive_get_post_medias($field);
                    }

                    if (empty($field) || is_array($field)) {
                        // Go to next one if empty, array (already recursively relaunched) and the key is not a media field
                        continue;
                    }

                    // Save the media ID
                    if (in_array($key, $this->_acf_object_fields)) {
                        $this->_found_medias = array_merge($this->_found_medias, (array)$field);
                    } elseif (in_array($key, $this->_acf_textual_fields)) {
                        $this->_found_medias = array_merge($this->_found_medias, $this->get_media_from_text($field));
                    }
                }
            }
        }


        //Get table list
        public function table_data($per_page = 20, $page_number = 1)
        {
            global $wpdb;
            global $images;
            global $total_posts;
            $media_ids = [];
            $data = [];
            $tb_post = $wpdb->prefix . "posts";
            $tb_postmeta = $wpdb->prefix . "postmeta";

            // Get all the post
            $post_content = $wpdb->get_results("SELECT * FROM $tb_post");

            // Get the media id of thumbnail image
            $thumbnail_ids = $wpdb->get_results("SELECT * FROM $tb_postmeta WHERE meta_key = '_thumbnail_id' ");

            foreach ($thumbnail_ids as $thumbs) {
                $media_ids[] = $thumbs->meta_value;
            }

            // Get the media id of woocommerce product image gallery
            $gallery_ids = $wpdb->get_results("SELECT meta_value FROM $tb_postmeta WHERE meta_key = '_product_image_gallery' ");

            foreach ($gallery_ids as $gallery_id) {
                $thumbs_array = explode(',', $gallery_id->meta_value);
                foreach ($thumbs_array as $thumb_array) {
                    $media_ids[] = $thumb_array;

                }
            }

            // Get the media id that added in link text
            foreach ($post_content as $posts) {
                preg_match_all('/href="([^"\\\']+)"/', $posts->post_content, $urls);
//                if (empty($urls)) {
//                    return $media_ids;
//                }
                foreach ($urls[1] as $url) {
                    // Check if retrieved media from href really exists for the current site
                    $attachment_id = $wpdb->get_col($wpdb->prepare("SELECT ID FROM {$wpdb->posts} WHERE guid='%s';", $url));
                    if (empty($attachment_id)) {
                        continue;
                    }
                    $media_ids[] = (int)$attachment_id[0];
                }
            }

            // Get the media id that added in post content
            foreach ($post_content as $post) {
                $text = $this->get_media_from_text($post->post_content);
                foreach ($text as $content) {
                    $records = $wpdb->get_results("SELECT post_id FROM $tb_postmeta WHERE post_id = {$content}  AND meta_key = '_wp_attached_file' ");
                    foreach ($records as $record) {
                        $media_ids[] = $record->post_id;
                    }
                }
            }


            // Get the media id that added in acf fields
            foreach ($post_content as $post) {
                $media = $this->get_media_from_acf_fields($post->ID);
                foreach ($media as $md) {
                    $media_ids[] = $md;
                }
            }

            // Get the media id that added in acf gallery fields

            foreach ($post_content as $post) {
                $gallery_field = $this->get_acf_gallery_fields($post->ID);
                // echo "<pre>";print_r($gallery_field);"</pre>";
                foreach ($gallery_field as $fields) {
                    $field_values = $wpdb->get_results("SELECT meta_value FROM $tb_postmeta WHERE meta_key = '$fields '");
                    // echo "<pre>";print_r($field_values);"</pre>";
                }
            }

            $args = array(
                'post_type' => 'attachment',
                'posts_per_page' => $per_page,
                'offset' => ($page_number - 1) * $per_page,
                'post__not_in' => $media_ids,
                'post_status' => 'inherit',
            );

            if(isset($_REQUEST['orderby']) && !empty($_REQUEST['orderby'])) {
                $order = (isset($_REQUEST['order']) && !empty($_REQUEST['order']))?esc_sql($_REQUEST['order']):"DESC";
                $orderby = esc_sql($_REQUEST['orderby']);
                if($orderby == "media_size") {
                    $args['orderby'] = "meta_value_num";
                    $args['meta_key'] = $this->meta_key;
                    $args['order'] = $order;
                } else if($orderby == "date") {
                    $args['orderby'] = "post_date";
                    $args['order'] = $order;
                } else if($orderby == "id") {
                    $args['orderby'] = "ID";
                    $args['order'] = $order;
                }
            } else {
                $args['orderby'] = "meta_value_num";
                $args['meta_key'] = $this->meta_key;
                $args['order'] = "DESC";
            }

            $query = new WP_Query($args);

            $total_posts = $query->found_posts;
            $this->set_pagination_args(array(
                'total_items' => $total_posts,
                'per_page' => $per_page
            ));

            foreach ($query->posts as $raw_data) {
                if ($this->is_media_files($raw_data)) {
                    continue;
                }

                $data[] = array(
                    'id' => $raw_data->ID,
                    'author' => get_the_author_meta('display_name', $raw_data->post_author),
                    'date' => $raw_data->post_date,
                );
            }

            wp_reset_query();

            return $data;
        }


        //Get a list of CSS classes for the WP_List_Table table
        protected function get_table_classes()
        {
            return array('widefat', 'fixed', 'striped', $this->_args['plural'], 'wps-list-media-unuse', 'media');
        }

        public function column_date($item)
        {
            if ('0000-00-00 00:00:00' === $item['date']) {
                $h_time = __('Unpublished');
            } else {
                $m_time = $item['date'];
                $time = get_post_time('G', true, $item['id'], false);
                if ((abs($t_diff = time() - $time)) < DAY_IN_SECONDS) {
                    if ($t_diff < 0) {
                        $h_time = sprintf(__('%s from now'), human_time_diff($time));
                    } else {
                        $h_time = sprintf(__('%s ago'), human_time_diff($time));
                    }
                } else {
                    $h_time = mysql2date(__('Y/m/d'), $m_time);
                }
            }

            echo $h_time;
        }

        public function column_title($item)
        {


            // create a nonce
            $delete_nonce = wp_create_nonce('wp_delete_attachment' . $item['id']);

            $title = (defined("MEDIA_TRASH") && MEDIA_TRASH == true) ? esc_html__("Trash", "folders"):esc_html__("Delete Permanently", "folders");
            $actions = [
                'delete' => sprintf('<a href="' . admin_url('admin-ajax.php') . '?action=%s&attachment_id=%s&_wpnonce=%s" class="show-delete-box">' . $title . '</a>', 'remove_scanned_media', absint($item['id']), $delete_nonce)
            ];

            $post_id = $item['id'];

            $post = get_post($post_id);

            list($mime) = explode('/', $post->post_mime_type);

            $title = _draft_or_post_title($post_id);
            $thumb = wp_get_attachment_image($post->ID, array(60, 60), true, array('alt' => ''));
            $link_start = $link_end = '';

            if (current_user_can('edit_post', $post->ID) && !$this->is_trash) {
                $link_start = sprintf(
                    '<a href="%s" aria-label="%s">',
                    get_edit_post_link($post->ID),
                    /* translators: %s: attachment title */
                    esc_attr(sprintf(__('&#8220;%s&#8221; (Edit)'), $title))
                );
                $link_end = '</a>';
            }

            $class = $thumb ? ' class="has-media-icon"' : '';
            ?>
            <strong<?php echo $class; ?> xmlns="http://www.w3.org/1999/html">
                <?php
                echo $link_start;
                if ($thumb) : ?>
                    <span
                            class="media-icon <?php echo sanitize_html_class($mime . '-icon'); ?>"><?php echo $thumb; ?></span>
                <?php endif;
                echo $title . $link_end;
                _media_states($post);
                ?>
            </strong>
            <p class="filename">
                <span class="screen-reader-text"><?php _e('File name:'); ?> </span>
                <?php
                $file = get_attached_file($post->ID);
                echo esc_html(wp_basename($file));
                echo $this->row_actions($actions);
                ?>
            </p>
            <?php
        }

        public function column_default($item, $column_name)
        {
            switch ($column_name) {
                case 'id':
                case 'author':
                case 'delete':
                    return $item[$column_name];

                default:
                    return print_r($item, true);
            }
        }

        function column_size($item)
        {
            $file_size = get_post_meta($item['id'], $this->meta_key, true);
            if($file_size === false || empty($file_size)) {
                $file_size = $this->get_file_size($item['id']);
            }
            if($file_size === false || empty($file_size)) {
                $is_file_exist = file_exists(get_attached_file( $item['id'] ) );
                $file_size     = '';
                if ( $is_file_exist ) {
                    $file_size = filesize(get_attached_file( $item['id'] ) );
                    update_post_meta( $item['id'], $this->meta_key, $file_size );
                }
            }
            return size_format( $file_size, 2 );

        }

        function column_cb($item)
        {
            return sprintf(
                '<input type="checkbox" name="bulk-delete-scanned-files[]" value="%s" />', $item['id']
            );
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
            update_post_meta( $media_id, $this->meta_key, $file_size );
            return $file_size;
        }
    }
}