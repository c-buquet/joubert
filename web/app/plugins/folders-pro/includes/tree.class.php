<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/* Free/Pro Class name Change*/
class WCP_Pro_Tree {

    var $folderSettings = null;

    public function __construct() {

    }

    public static function get_full_tree_data($post_type, $order_by = "", $order = "", $sticky_open = 0, $user_id = false) {
        $isAjax = (defined('DOING_AJAX') && DOING_AJAX)?1:0;
        $type = folders_sanitize_text($post_type, 'get');
        if((isset($type) && !empty($type)) || ! $isAjax) {
            update_option("selected_" . $post_type . "_folder", "");
        }
        if($user_id) {
            $user_id = get_current_user_id();
            $user_meta = get_userdata($user_id);
            $user_roles = $user_meta->roles;
            $user_roles = !is_array($user_roles)?array():$user_roles;
            if(in_array("administrator", $user_roles)) {
                $user_id = false;
            }
        }
        return self::get_folder_category_data($post_type, 0, 0, $order_by, $order, $sticky_open, $user_id);
    }

    public static function get_folder_category_data($post_type, $parent = 0, $parentStatus = 0, $order_by = "", $order = "", $sticky_open = 0, $user_id = false) {
        $arg = array(
            'hide_empty' => false,
            'parent'   => $parent,
            'hierarchical' => false,
            'update_count_callback' => '_update_generic_term_count',
        );
        if(!empty($order_by) && !empty($order)) {
            $arg['orderby'] = $order_by;
            $arg['order'] = $order;

            if($user_id) {
                $arg['meta_query'] = [[
                    'key' => 'created_by',
                    'type' => '=',
                    'value' => $user_id,
                ]];
            }
        } else {
            $arg['orderby'] = 'meta_value_num';
            $arg['order'] = 'ASC';
            if($user_id) {
                $arg['meta_query'] = [[
                        'key' => 'wcp_custom_order',
                        'type' => 'NUMERIC',
                    ],
                    [
                        'key' => 'created_by',
                        'type' => '=',
                        'value' => $user_id,
                    ]
                ];
            } else {
                $arg['meta_query'] = [[
                    'key' => 'wcp_custom_order',
                    'type' => 'NUMERIC',
                ]];
            }
        }

        $terms = get_terms( $post_type, $arg);

        $string = "";
        $sticky_string = "";
        $child = 0;
        $isAjax = (defined('DOING_AJAX') && DOING_AJAX)?1:0;
        if(!empty($terms)) {
            $child = count($terms);
            foreach($terms as $key=>$term) {

                if(!empty($order_by) && !empty($order)) {
                    update_term_meta($term->term_id, "wcp_custom_order", ($key+1));
                }

                $status = get_term_meta($term->term_id, "is_active", true);
                $return = self::get_folder_category_data($post_type, $term->term_id, $status, $order_by, $order, $sticky_open, $user_id);
                $type = folders_sanitize_text($post_type, 'get');
                if($post_type == "attachment") {
                    if(isset($type) && $type == $term->slug) {
                        update_option("selected_".$post_type."_folder", $term->term_id);
                    }
                } else {
                    if(isset($type) && $type == $term->slug) {
                        update_option("selected_" . $post_type . "_folder", $term->term_id);
                    }
                }

                $count = ($term->trash_count != 0)?$term->trash_count:0;

                /* Free/Pro URL Change*/
                $nonce = wp_create_nonce('wcp_folder_term_'.$term->term_id);
                $is_active = get_term_meta($term->term_id, "is_active", true);
                $is_sticky = get_term_meta($term->term_id, "is_folder_sticky", true);
                $class = "";
                if($is_active == 1 || ($is_sticky && $sticky_open)) {
                    $class .= " jstree-open";
                }

                $string .= "<li id='{$term->term_id}' class='{$class}' data-slug='{$term->slug}' data-nonce='{$nonce}' data-folder='{$term->term_id}' data-child='{$child}' data-count='{$count}' data-parent='{$parent}'>
                                {$term->name}
                                <ul>{$return['string']}</ul>
                            </li>";

                $sticky_string .= $return['sticky_string'];
            }
        }
        return array(
            'string' =>$string,
            'sticky_string' =>$sticky_string,
            'child' => $child
        );
    }

    public static function get_option_data_for_select($post_type) {
        $string = "<option value='0'>Parent Folder</option>";
        $string .=  self::get_folder_option_data($post_type, 0, '');
        return $string;
    }

    public static function get_folder_option_data($post_type, $parent = 0, $space = "", $folder_by_user = 0) {
        $args = array(
            'hide_empty' => false,
            'parent'   => $parent,
            'orderby' => 'meta_value_num',
            'order' => 'ASC',
            'hierarchical' => false
        );

        if($folder_by_user) {
            $args['meta_query'] = [[
                'key' => 'wcp_custom_order',
                'type' => 'NUMERIC',
            ],
                [
                    'key' => 'created_by',
                    'type' => '=',
                    'value' => $folder_by_user,
                ]
            ];
        } else {
            $args['meta_query'] = [[
                'key' => 'wcp_custom_order',
                'type' => 'NUMERIC',
            ]];
        }

        $terms = get_terms( $post_type, $args);

        $selected_term = get_option("selected_" . $post_type . "_folder");

        $string = "";
        if(!empty($terms)) {
            foreach($terms as $term) {
                $selected = ($selected_term == $term->term_id)?"selected":"";
                $string .= "<option {$selected} value='{$term->term_id}'>{$space}{$term->name}</option>";
                $string .= self::get_folder_option_data($post_type, $term->term_id, trim($space)."- ");
            }
        }
        return $string;
    }
}