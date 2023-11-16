<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/* Free/Pro Class name change */
class WCP_Pro_Folders
{

    private static $instance;

    private static $license_key_data = null;

    private static $folders;

    private static $postIds;

    private static $folderSettings = false;

    private static $foldersByUser = false;

    public function __construct()
    {
        spl_autoload_register(array($this, 'autoload'));
        add_action('init', array($this, 'create_folder_terms'), 15);
        add_action('admin_init', array($this, 'folders_register_settings'));
        add_action('admin_menu', array($this, 'admin_menu'), 10000);
        add_action('admin_enqueue_scripts', array($this, 'folders_admin_styles'));
        add_action('admin_enqueue_scripts', array($this, 'folders_admin_scripts'));
        add_filter('plugin_action_links_' . WCP_PRO_FOLDERS_PLUGIN_BASE, [$this, 'plugin_action_links']);
        add_action('admin_footer', array($this, 'admin_footer'));

        /* check for polygon media */
//        $polylang_options = get_option("polylang");
//        if(is_array($polylang_options) && isset($polylang_options['media_support']) && $polylang_options['media_support'] == 1) {
//            $polylang_options['media_support'] = 0;
//            update_option("polylang", $polylang_options);
//        }

        $customize_folders = get_option('customize_folders');
        self::$foldersByUser = isset($customize_folders['folders_by_users']) && $customize_folders['folders_by_users'] == "on"?true:false;

        add_action('parse_tax_query', array($this, 'taxonomy_archive_exclude_children'));
        add_action('admin_footer', array($this, 'admin_footer_for_media'));

        /* Save Data */
        add_action('wp_ajax_wcp_add_new_folder', array($this, 'wcp_add_new_folder'));
        /* Update Data */
        add_action('wp_ajax_wcp_update_folder', array($this, 'wcp_update_folder'));
        /* Remove Data */
        add_action('wp_ajax_wcp_remove_folder', array($this, 'wcp_remove_folder'));
        /* Remove Multple Folder */
        add_action('wp_ajax_wcp_remove_muliple_folder', array($this, 'remove_muliple_folder'));
        /* Save State Data */
        add_action('wp_ajax_save_wcp_folder_state', array($this, 'save_wcp_folder_state'));
        /* Save State Data */
        add_action('wp_ajax_wcp_save_parent_data', array($this, 'wcp_save_parent_data'));
        /* Update Parent Data */
        add_action('wp_ajax_wcp_update_parent_information', array($this, 'wcp_update_parent_information'));
        /* Update Parent Data */
        add_action('wp_ajax_wcp_save_folder_order', array($this, 'wcp_save_folder_order'));
        /* Update Parent Data */
        add_action('wp_ajax_wcp_mark_un_mark_folder', array($this, 'wcp_mark_un_mark_folder'));
        /* Lock/Unlock Folder */
        add_action('wp_ajax_wcp_lock_unlock_folder', array($this, 'wcp_lock_unlock_folder'));
        /* Update Parent Data */
        add_action('wp_ajax_wcp_make_sticky_folder', array($this, 'wcp_make_sticky_folder'));
        /* Update Parent Data */
        add_action('wp_ajax_wcp_change_post_folder', array($this, 'wcp_change_post_folder'));
        /* Update Parent Data */
        add_action('wp_ajax_wcp_change_multiple_post_folder', array($this, 'wcp_change_multiple_post_folder'));
        /* Update width Data */
        add_action('wp_ajax_wcp_change_post_width', array($this, 'wcp_change_post_width'));
        /* Update width Data */
        add_action('wp_ajax_wcp_change_folder_display_status', array($this, 'wcp_change_folder_display_status'));
        /* Update width Data */
        add_action('wp_ajax_wcp_change_all_status', array($this, 'wcp_change_all_status'));
        /* Update width Data */
        add_action('wp_ajax_save_folder_last_status', array($this, 'save_folder_last_status'));
        /* Update width Data */
        add_action('wp_ajax_save_premio_default_folder', array($this, 'save_premio_default_folder'));
        /* Update width Data */
        add_action('wp_ajax_remove_premio_default_folder', array($this, 'remove_premio_default_folder'));
        /* Update width Data */
        add_action('wp_ajax_wcp_folders_by_order', array($this, 'wcp_folders_by_order'));
        /* Update width Data */
        add_action('wp_ajax_wcp_remove_all_folders_data', array($this, 'remove_all_folders_data'));
        /* Update folders Status */
        add_action('wp_ajax_wcp_update_folders_uninstall_status', array($this, 'update_folders_uninstall_status'));
        /* Update folders Status */
        add_action('wp_ajax_wcp_update_user_folder_status', array($this, 'update_user_folder_status'));
        /* Update folders Status */
        add_action('wp_ajax_wcp_update_dynamic_folder_status', array($this, 'update_dynamic_folder_status'));
        /* Undo Functionality */
        add_action('wp_ajax_wcp_undo_folder_changes', array($this, 'wcp_undo_folder_changes'));
        self::$folders = 10;

        /* Send message on plugin deactivate */
        add_action( 'wp_ajax_folder_plugin_deactivate', array( $this, 'folder_plugin_deactivate' ) );
        /* Update Parent Data */
        add_action('wp_ajax_wcp_remove_post_folder', array($this, 'wcp_remove_post_folder'));
        /* Check for folders */
        add_action('wp_ajax_premio_check_for_other_folders', array($this, 'premio_check_for_other_folders'));
        /* Send message on owner */
        add_action( 'wp_ajax_wcp_folder_send_message_to_owner', array( $this, 'wcp_folder_send_message_to_owner' ) );
        /* Get default list */
        add_action( 'wp_ajax_wcp_get_default_list', array( $this, 'wcp_get_default_list' ) );
        /* Get default list */
        add_action( 'wp_ajax_get_folders_default_list', array( $this, 'get_folders_default_list' ) );
        /* Auto select folder for new page, post */
        add_action('new_to_auto-draft', array($this, 'new_to_auto_draft'), 10);
        /* for media */
        add_action('restrict_manage_posts', array($this, 'output_list_table_filters'), 10, 2);
        add_filter('pre_get_posts', array($this, 'filter_attachments_list'));
        add_action('wp_enqueue_media', array($this, 'output_backbone_view_filters'));
        add_action('wp_enqueue_media', array($this, 'add_media_overrides'));
        add_filter('ajax_query_attachments_args', array($this, 'filter_attachments_grid'));
        add_filter('add_attachment', array($this, 'save_media_terms'));

        /* to filter un assigned items*/
        add_filter('pre_get_posts', array($this, 'filter_record_list'));
        add_filter('pre-upload-ui', array($this, 'show_dropdown_on_media_screen'));
        add_action('add_attachment', array($this, 'add_attachment_category'));

        $options = get_option("folders_settings");

        $options = is_array($options)?$options:array();

        if (in_array("post", $options)) {
            add_filter('manage_posts_columns', array($this, 'wcp_manage_columns_head'));
            add_action('manage_posts_custom_column', array($this, 'wcp_manage_columns_content'), 10, 2);
            add_filter( 'bulk_actions-edit-post', array($this, 'custom_bulk_action' ));
        }

        if (in_array("page", $options)) {
            add_filter('manage_page_posts_columns', array($this, 'wcp_manage_columns_head'));
            add_action('manage_page_posts_custom_column', array($this, 'wcp_manage_columns_content'), 10, 2);
            add_filter( 'bulk_actions-edit-page', array($this, 'custom_bulk_action' ));
        }

        if (in_array("attachment", $options)) {
            add_filter('manage_media_columns', array($this, 'wcp_manage_columns_head'));
            add_action('manage_media_custom_column', array($this, 'wcp_manage_columns_content'), 10, 2);
            //add_filter('bulk_actions-edit-media', array($this, 'custom_bulk_action' ));
        }

        foreach ($options as $option) {
            if ($option != "post" && $option != "page" && $option != "attachment") {
                add_filter('manage_edit-'.$option.'_columns', array($this, 'wcp_manage_columns_head'), 99999);
                add_action('manage_'.$option.'_posts_custom_column', array($this, 'wcp_manage_columns_content'), 2, 2);
                add_filter( 'bulk_actions-edit-'.$option, array($this, 'custom_bulk_action' ));
            }
        }

        /* check for default folders */
        add_filter('pre_get_posts', array($this, 'check_for_default_folders'));

        add_action("wp_ajax_folder_update_status", array($this, 'folder_update_status'));

        add_filter('get_terms', array( $this, 'get_terms_filter_without_trash'), 10, 3);

        add_filter('mla_media_modal_query_final_terms', array( $this, 'media_modal_query_final_terms'), 10, 3);

        /* Pro Functions for license key - Only in PRO */
        add_action('wp_ajax_wcp_hide_folders', array($this, 'wcp_hide_folders'));
        /* Update width Data */
        add_action('wp_ajax_wcp_folder_activate_key', array($this, 'wcp_folder_activate_key'));
        /* Update width Data */
        add_action('wp_ajax_wcp_folder_deactivate_key', array($this, 'wcp_folder_deactivate_key'));
        add_action('admin_notices', array($this, 'wcp_admin_notice'));
        /* Update width Data */
        add_action('wp_ajax_wcp_folder_check_for_valid_key', array($this, 'wcp_folder_check_for_valid_key'));
        /* download media folders */
        add_action('init', array($this, 'download_folder'), 100);

        /* reset count when post/page updated */
        add_action( 'deleted_term_relationships', array($this, 'update_folder_term_relationships'), 10, 3 );

        add_action( 'added_term_relationship', array($this, 'update_folder_new_term_relationships'), 10, 3 );

        add_action( 'wp_ajax_copy_premio_folders', array($this, 'copy_premio_folders'));

        add_action( 'wp_ajax_premio_lock_unlock_all_folders', array($this, 'premio_lock_unlock_all_folders'));

        add_action( 'wp_ajax_upload_premio_folder', array($this, 'upload_premio_folder'));

        add_action( 'set_object_terms', array($this, 'set_object_terms_for_folders'), 10, 6);

        /*
         * To Remove Attachment
         * */
        add_action( 'wp_ajax_wcp_remove_scanned_media', array($this, 'remove_scanned_media'));

        add_action( 'wp_trash_post', array($this, "wcp_delete_post"));
        add_action( 'before_delete_post', array($this, "wcp_delete_post"));
        /*
         * Hide Folder CTA
         * */
        add_action( 'wp_ajax_hide_folders_cta', array($this, 'hide_folders_cta'));
    }

    public function hide_folders_cta() {
        $response = array();
        $response['status'] = 0;
        $response['error'] = 0;
        $response['data'] = array();
        $response['message'] = "";
        $postData = filter_input_array(INPUT_POST);
        $errorCounter = 0;
        if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] =  esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else {
            $nonce = self::sanitize_options($postData['nonce']);
            if(!wp_verify_nonce($nonce, 'hide_folders_cta')) {
                $response['message'] =  esc_html__("Your request is not valid", 'folders');
                $errorCounter++;
            }
        }
        if($errorCounter == 0) {
            $response['status'] = 1;
            add_option("hide_folders_cta", "yes");
        }
        echo json_encode($response); die;
    }

    public function wcp_delete_post($postID) {
        delete_transient("premio_folders_without_trash");
    }

    public function remove_scanned_media() {
        if(current_user_can('upload_files') || 1) {
            if (isset($_GET['attachment_id']) && isset($_GET['_wpnonce'])) {
                $attachment_id = folders_sanitize_text('attachment_id', 'get');
                $nonce = folders_sanitize_text('_wpnonce', 'get');
                if (wp_verify_nonce($nonce, 'wp_delete_attachment' . $attachment_id)) {
                    wp_delete_attachment(absint($attachment_id));
                }
            }
        }
        echo "1"; die;
    }

    public $newFolders = array();
    public $first_folder = 0;

    public function upload_premio_folder() {
        $response = array();
        $response['status'] = 0;
        $response['error'] = 0;
        $response['data'] = array();
        $response['message'] = "";
        $postData = filter_input_array(INPUT_POST);
        $errorCounter = 0;
        if ((!isset($postData['post_type']) || empty($postData['post_type'])) && $postData['post_type'] != 'attachment') {
            $response['message'] =  esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] =  esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else {
            $nonce = self::sanitize_options($postData['nonce']);
            $post_type = self::sanitize_options($postData['post_type']);
            if(!wp_verify_nonce($nonce, 'wcp_folder_nonce_'.$post_type)) {
                $response['message'] =  esc_html__("Your request is not valid", 'folders');
                $errorCounter++;
            }
        }
        if($errorCounter == 0) {
            $folder_data = array();
            $postData = filter_input_array(INPUT_POST);
            $folder_type = self::get_custom_post_type("attachment");
            $first_folder = 0;
            $terms = array();
            if(isset($postData['media_file_name']) && !empty($postData['media_file_name']) && is_array($postData['media_file_name'])) {
                $trash_folders = $initial_trash_folders = get_transient("premio_folders_without_trash");
                foreach($postData['media_file_name'] as $key=>$file) {
                    if(isset($_FILES['media_files_'.$key]) && $_FILES['media_files_'.$key]['error'] == 0) {
                        $folder_name = rtrim($file, $_FILES['media_files_'.$key]['name']);
                        $folder_name = trim($folder_name, "/");

                        $folder_id = $this->check_for_folder($folder_name, 0);
                        if(empty($first_folder)) {
                            $first_folder = $folder_id;
                        }
                        $upload_data = wp_upload_bits($_FILES['media_files_'.$key]['name'], null, file_get_contents($_FILES['media_files_'.$key]['tmp_name']));

                        if(!empty($upload_data) && $upload_data) {
                            $file_path = $upload_data['file'];
                            $file_name = basename( $file_path );
                            $file_type = wp_check_filetype( $file_name, null );
                            $attachment_title = sanitize_file_name( pathinfo( $file_name, PATHINFO_FILENAME ) );

                            $attachment = array(
                                'guid' => $upload_data['url'],
                                'post_mime_type' => $file_type['type'],
                                'post_title' => $attachment_title,
                                'post_content' => '',
                                'post_status' => 'inherit'
                            );

                            $attach_id = wp_insert_attachment( $attachment, $file_path, 0 );
                            if($attach_id) {
                                require_once(ABSPATH . 'wp-admin/includes/image.php');
                                $attach_data = wp_generate_attachment_metadata($attach_id, $file_path);
                                wp_update_attachment_metadata($attach_id, $attach_data);

                                if(!empty($folder_id)) {
                                    wp_set_post_terms($attach_id, $folder_id, $folder_type, false);
                                }
                            }
                        }
                    }
                }
                $response['status'] = 1;
                if(!empty($this->first_folder)) {
                    $folder = get_term($this->first_folder);
                    if(!empty($folder)) {
                        $term_nonce = wp_create_nonce('wcp_folder_term_' . $folder->term_id);

                        $folder_item = array();
                        $folder_item['parent_id'] = "#";
                        $folder_item['slug'] = $folder->slug;
                        $folder_item['nonce'] = $term_nonce;
                        $folder_item['term_id'] = $folder->term_id;
                        $folder_item['title'] = $folder->name;
                        $folder_item['is_sticky'] = 0;
                        $folder_item['is_high'] = 0;
                        $folder_item['is_locked'] = 0;
    //                        $hierarchical_terms[] = $folder_item;
                        $hierarchical_terms = self::get_child_terms("media_folder", array(), $folder->term_id, " ");

                        if(isset($trash_folders[$folder->term_taxonomy_id])) {
                            unset($trash_folders[$folder->term_taxonomy_id]);
                        }

                        if(function_exists("clean_term_cache")) {
                            clean_term_cache($folder->term_id);
                        }

                        $terms[] = $folder_item;
                        foreach ($hierarchical_terms as $hierarchical_term) {
                            $term_nonce = wp_create_nonce('wcp_folder_term_' . $hierarchical_term->term_id);
                            $folder_item = array();
                            $folder_item['parent_id'] = $hierarchical_term->parent;
                            $folder_item['slug'] = $hierarchical_term->slug;
                            $folder_item['nonce'] = $term_nonce;
                            $folder_item['term_id'] = $hierarchical_term->term_id;
                            $folder_item['title'] = trim(trim($hierarchical_term->name), "-");
                            $folder_item['is_sticky'] = 0;
                            $folder_item['is_high'] = 0;
                            $folder_item['is_locked'] = 0;
                            $terms[] = $folder_item;

                            if(isset($hierarchical_term->term_taxonomy_id) && isset($trash_folders[$hierarchical_term->term_taxonomy_id])) {
                                unset($trash_folders[$hierarchical_term->term_taxonomy_id]);
                            }

                            if(function_exists("clean_term_cache")) {
                                clean_term_cache($hierarchical_term->term_id);
                            }
                        }
                    }

                    $response['data'] = $terms;
                    $response['first_folder'] = $this->first_folder;
                }
                delete_transient("premio_folders_without_trash");
            }
        }
        echo json_encode($response); die;
    }

    public function check_for_folder($folder_name, $parent_id) {
        $folder_type = self::get_custom_post_type("attachment");
        $user_id = get_current_user_id();
        $folder_name = explode("/", $folder_name);
        if(!empty($folder_name) && count($folder_name)) {
            foreach($folder_name as $folder) {
                $folder = self::sanitize_options($folder);
                $term = term_exists($folder, $folder_type, $parent_id);
                if(!empty($term) && isset($term['term_id']) && !empty($term['term_id'])) {
                    if(empty($this->first_folder)) {
                        $this->first_folder = $term['term_id'];
                    }
                    $term_user = get_term_meta($term['term_id'], "created_by", true);
                    if($term_user == $user_id) {
                        $parent_id = $term['term_id'];
                    }
                } else {
                    $folder = trim($folder);
                    $slug = self::create_slug_from_string($folder) . "-" . time()."-".$user_id;

                    $result = wp_insert_term(
                        urldecode($folder), // the term
                        $folder_type, // the taxonomy
                        array(
                            'parent' => $parent_id,
                            'slug' => $slug
                        )
                    );

                    if (!empty($result)) {
                        add_term_meta($result['term_id'], "created_by", $user_id);
                        $terms = get_terms( array(
                            'taxonomy'      => $folder_type,
                            'hide_empty' => false,
                            'parent'   => $parent_id,
                            'hierarchical' => false,
                            'update_count_callback' => '_update_generic_term_count',
                        ));
                        $order = count($terms)+1;
                        update_term_meta($result['term_id'], "wcp_custom_order", $order);

                        if(empty($this->first_folder)) {
                            $this->first_folder = $result['term_id'];
                        }

                        if(!in_array($parent_id, $this->newFolders)) {

                            $term_nonce = wp_create_nonce('wcp_folder_term_'.$result['term_id']);

                            $folder_item = array();
                            $folder_item['parent_id'] = empty($parent) ? "#" : $parent;
                            $folder_item['slug'] = $slug;
                            $folder_item['nonce'] = $term_nonce;
                            $folder_item['term_id'] = $result['term_id'];
                            $folder_item['title'] = $folder;
                            $folder_item['is_sticky'] = 0;
                            $folder_item['is_high'] = 0;
                            $folder_item['is_locked'] = 0;

                            $this->newFolders[] = $folder_item;
                        }
                        $parent_id = $result['term_id'];
                    }
                }
            }
        }
        return $parent_id;
    }

    public function premio_lock_unlock_all_folders() {
        $response = array();
        $response['status'] = 0;
        $response['error'] = 0;
        $response['data'] = array();
        $response['message'] = "";
        $postData = filter_input_array(INPUT_POST);
        $errorCounter = 0;
        if (!isset($postData['post_type']) || empty($postData['post_type'])) {
            $response['message'] =  esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] =  esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else {
            $nonce = self::sanitize_options($postData['nonce']);
            $post_type = self::sanitize_options($postData['post_type']);
            if(!wp_verify_nonce($nonce, 'wcp_folder_nonce_'.$post_type)) {
                $response['message'] =  esc_html__("Your request is not valid", 'folders');
                $errorCounter++;
            }
        }
        if($errorCounter == 0) {
            $lock_folder = (isset($postData['lock_folders']) && $postData['lock_folders'] == 1)?1:0;
            $post_type = self::sanitize_options($postData['post_type']);
            $taxonomy = self::get_custom_post_type($post_type);
            global $wpdb;
            $folders = $wpdb->get_results(
                "SELECT " . $wpdb->terms . ".term_id FROM " . $wpdb->term_taxonomy . "
					LEFT JOIN  " . $wpdb->terms . "
					ON  " . $wpdb->term_taxonomy . ".term_id =  " . $wpdb->terms . ".term_id
					WHERE " . $wpdb->term_taxonomy . ".taxonomy = '" . $taxonomy . "'
					ORDER BY parent ASC"
            );
            if(count($folders) > 0) {
                foreach($folders as $folder) {
                    $is_locked = get_term_meta($folder->term_id, "is_locked", true);
                    if($is_locked === false) {
                        add_term_meta($folder->term_id, "is_locked", $lock_folder);
                    } else {
                        update_term_meta($folder->term_id, "is_locked", $lock_folder);
                    }
                }
            }
            $data = array();
            $data['is_locked'] = $lock_folder;
            $response['data'] = $data;
            $response['status'] = 1;
        }
        echo json_encode($response); die;
    }

    public function copy_premio_folders() {
        $response = array();
        $response['status'] = 0;
        $response['error'] = 0;
        $response['data'] = array();
        $response['parent_id'] = 0;
        $response['message'] = "";
        $postData = filter_input_array(INPUT_POST);
        $errorCounter = 0;

        if (!isset($postData['post_type']) || empty($postData['post_type'])) {
            $response['message'] =  esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if (!isset($postData['copy_from']) || empty($postData['copy_from'])) {
            $response['message'] =  esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if (!isset($postData['copy_to'])) {
            $response['message'] =  esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] =  esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else {
            $nonce = self::sanitize_options($postData['nonce']);
            $post_type = self::sanitize_options($postData['post_type']);
            if(!wp_verify_nonce($nonce, 'wcp_folder_nonce_'.$post_type)) {
                $response['message'] =  esc_html__("Your request is not valid", 'folders');
                $errorCounter++;
            }
        }
        if ($errorCounter == 0) {
            $copy_from = $postData['copy_from'];
            $copy_to = $postData['copy_to'];
            $post_type = $postData['post_type'];
            $post_type = self::get_custom_post_type($post_type);
            $term = get_term($copy_from);
            $folders = array();
            if(!empty($term) && isset($term->slug)) {

                $folder = trim($term->name);
                $slug = self::create_slug_from_string($term->name) . "-" . time();

                $old_folders = array();
                $old_folders = self::get_child_lists($post_type, $copy_from, $old_folders);
                $old_folders[] = $copy_from;

                $result = wp_insert_term(
                    $folder, // the term
                    $post_type, // the taxonomy
                    array(
                        'parent' => $copy_to,
                        'slug' => $slug
                    )
                );

                $parent_id = empty($copy_to)?"#":$copy_to;
                if (!empty($result)) {
                    $term_nonce = wp_create_nonce('wcp_folder_term_'.$result['term_id']);
                    $folder_item = array();
                    $folder_item['parent_id'] = $parent_id;
                    $folder_item['slug'] = $slug;
                    $folder_item['nonce'] = $term_nonce;
                    $folder_item['term_id'] = $result['term_id'];
                    $folder_item['title'] = $folder;
                    $folder_item['is_sticky'] = 0;
                    $folder_item['is_high'] = 0;
                    $folder_item['is_locked'] = 0;
                    $folder_item['is_active'] = 0;
                    $folder_item['child'] = array();

                    $response['parent_id'] = $result['term_id'];

                    $terms = get_terms( array(
                        'taxonomy'      => $post_type,
                        'hide_empty' => false,
                        'parent'   => $copy_to
                    ));

                    $order = 1;
                    if(!empty($terms) && is_array($terms)) {
                        $order = count($terms);
                    }

                    update_term_meta($result['term_id'], "wcp_custom_order", $order);
                    if ($order != 0) {
                        update_term_meta($copy_to, "is_active", 1);
                        $folder_item['is_active'] = 1;
                    }

                    $is_active = get_term_meta($copy_from, "is_active", true);
                    if ($is_active == 1) {
                        add_term_meta($result['term_id'], "is_active", 1);
                        $folder_item['is_active'] = 1;
                    }

                    $is_sticky = get_term_meta($copy_from, "is_folder_sticky", true);
                    if ($is_sticky == 1) {
                        add_term_meta($result['term_id'], "is_folder_sticky", 1);
                        $folder_item['is_sticky'] = 1;
                    }

                    $is_locked = get_term_meta($copy_from, "is_locked", true);
                    if ($is_locked == 1) {
                        add_term_meta($result['term_id'], "is_locked", 1);
                        $folder_item['is_locked'] = 1;
                    }

                    $status = get_term_meta($copy_from, "is_highlighted", true);
                    if ($status == 1) {
                        add_term_meta($result['term_id'], "is_highlighted", 1);
                        $folder_item['is_high'] = 1;
                    }

                    $postArray = get_posts(
                        array(
                            'posts_per_page' => -1,
                            'post_type' => $postData['post_type'],
                            'tax_query' => array(
                                array(
                                    'taxonomy' => $post_type,
                                    'field' => 'term_id',
                                    'terms' => $copy_from,
                                )
                            )
                        )
                    );
                    if (!empty($postArray)) {
                        foreach ($postArray as $p) {
                            wp_set_post_terms($p->ID, $result['term_id'], $post_type, true);
                        }
                    }

                    $folder_item['child'] = self::get_child_folders($post_type, $copy_from, $result['term_id'], $postData['post_type'], $old_folders);

                    $folders[] = $folder_item;
                }
            }
            $response['data'] = $folders;
            $response['status'] = 1;

            delete_transient("premio_folders_without_trash");
        }
        echo json_encode($response); die;
    }

    public static function get_child_lists($taxonomy, $term_id, $new_folders = array()) {
        $terms = get_terms( array(
            'taxonomy'      => $taxonomy,
            'hide_empty'    => false,
            'parent'        => $term_id,
            'orderby' => 'meta_value_num',
            'order' => 'ASC',
            'hierarchical' => false,
            'update_count_callback' => '_update_generic_term_count',
            'meta_query' => [[
                'key' => 'wcp_custom_order',
                'type' => 'NUMERIC',
            ]]
        ));
        if(!empty($terms)) {
            foreach ($terms as $term) {
                $new_folders = self::get_child_lists($taxonomy, $term->term_id, $new_folders);
                $new_folders[] = $term->term_id;
            }
        }
        return $new_folders;
    }

    public static function get_child_folders($taxonomy, $term_id, $parent_id, $post_type, $new_folders = array()) {
        $terms = get_terms( array(
            'taxonomy'      => $taxonomy,
            'hide_empty'    => false,
            'parent'        => $term_id,
            'orderby' => 'meta_value_num',
            'order' => 'ASC',
            'hierarchical' => false,
            'update_count_callback' => '_update_generic_term_count',
            'meta_query' => [[
                'key' => 'wcp_custom_order',
                'type' => 'NUMERIC',
            ]]
        ));
        $hierarchical_terms = array();
        if(!empty($terms)) {
            foreach ($terms as $key => $term) {
                if(isset($term->name)) {
                    if (in_array($term->term_id, $new_folders)) {
                        $folder = trim($term->name);
                        $slug = self::create_slug_from_string($term->name) . "-" . time();

                        $result = wp_insert_term(
                            $folder, // the term
                            $taxonomy, // the taxonomy
                            array(
                                'parent' => $parent_id,
                                'slug' => $slug
                            )
                        );

                        if (!empty($result)) {
                            $term_nonce = wp_create_nonce('wcp_folder_term_' . $result['term_id']);
                            $folder_item = array();
                            $folder_item['parent_id'] = $parent_id;
                            $folder_item['slug'] = $slug;
                            $folder_item['nonce'] = $term_nonce;
                            $folder_item['term_id'] = $result['term_id'];
                            $folder_item['title'] = $folder;
                            $folder_item['is_sticky'] = 0;
                            $folder_item['is_active'] = 0;
                            $folder_item['is_high'] = 0;
                            $folder_item['is_locked'] = 0;
                            $folder_item['child'] = array();

                            $order = $key + 1;
                            update_term_meta($result['term_id'], "wcp_custom_order", $order);

                            $is_active = get_term_meta($term->term_id, "is_active", true);
                            if ($is_active == 1) {
                                add_term_meta($result['term_id'], "is_active", 1);
                                $folder_item['is_active'] = 1;
                            }

                            $is_sticky = get_term_meta($term->term_id, "is_folder_sticky", true);
                            if ($is_sticky == 1) {
                                add_term_meta($result['term_id'], "is_folder_sticky", 1);
                                $folder_item['is_sticky'] = 1;
                            }

                            $is_locked = get_term_meta($term->term_id, "is_locked", true);
                            if ($is_locked == 1) {
                                add_term_meta($result['term_id'], "is_locked", 1);
                                $folder_item['is_locked'] = 1;
                            }

                            $status = get_term_meta($term->term_id, "is_highlighted", true);
                            if ($status == 1) {
                                add_term_meta($result['term_id'], "is_highlighted", 1);
                                $folder_item['is_high'] = 1;
                            }

                        }

                        $postArray = get_posts(
                            array(
                                'posts_per_page' => -1,
                                'post_type' => $post_type,
                                'tax_query' => array(
                                    array(
                                        'taxonomy' => $taxonomy,
                                        'field' => 'term_id',
                                        'terms' => $term->term_id,
                                    )
                                )
                            )
                        );
                        if (!empty($postArray)) {
                            foreach ($postArray as $p) {
                                wp_set_post_terms($p->ID, $result['term_id'], $taxonomy, true);
                            }
                        }

                        $new_folders[] = $result['term_id'];
                        $folder_item['child'] = self::get_child_folders($taxonomy, $term->term_id, $result['term_id'], $post_type, $new_folders);
                        $hierarchical_terms[] = $folder_item;
                    }
                }
            }
        }
        return $hierarchical_terms;
    }

    public function update_folder_new_term_relationships($object_id = "", $term_ids = array(), $taxonomy = "") {
        if(is_array($term_ids) && !empty($term_ids)) {
            $trash_folders = $initial_trash_folders = get_transient("premio_folders_without_trash");
            if($trash_folders === false) {
                $trash_folders = array();
                $initial_trash_folders = array();
            }

            foreach($term_ids as $term_id) {
                $term = get_term($term_id, $taxonomy);
                if(isset($term->term_taxonomy_id) && isset($trash_folders[$term->term_taxonomy_id])) {
                    unset($trash_folders[$term->term_taxonomy_id]);
                }
            }

            if($initial_trash_folders != $trash_folders) {
                delete_transient("premio_folders_without_trash");
                set_transient("premio_folders_without_trash", $trash_folders, 3*DAY_IN_SECONDS);
            }
        }
    }

    public function set_object_terms_for_folders($object_id, $terms, $tt_ids, $taxonomy, $append, $old_tt_ids) {
        if(!empty($object_id)) {
            $trash_folders = $initial_trash_folders = get_transient("premio_folders_without_trash");
            if(!empty($tt_ids) && is_array($tt_ids)) {
                foreach($tt_ids as $term_id) {
                    $term = get_term($term_id, $taxonomy);
                    if(isset($term->term_taxonomy_id) && isset($trash_folders[$term->term_taxonomy_id])) {
                        unset($trash_folders[$term->term_taxonomy_id]);
                    }
                }
            }

            if(!empty($old_tt_ids) && is_array($old_tt_ids)) {
                foreach($old_tt_ids as $term_id) {
                    $term = get_term($term_id, $taxonomy);
                    if(isset($term->term_taxonomy_id) && isset($trash_folders[$term->term_taxonomy_id])) {
                        unset($trash_folders[$term->term_taxonomy_id]);
                    }
                }
            }

            if($initial_trash_folders != $trash_folders) {
                delete_transient("premio_folders_without_trash");
                set_transient("premio_folders_without_trash", $trash_folders, 3*DAY_IN_SECONDS);
            }
        }
    }

    public function update_folder_term_relationships($object_id = "", $term_ids = array(), $taxonomy = "") {
        if(is_array($term_ids) && !empty($term_ids)) {
            $trash_folders = $initial_trash_folders = get_transient("premio_folders_without_trash");
            if($trash_folders === false) {
                $trash_folders = array();
                $initial_trash_folders = array();
            }

            foreach($term_ids as $term_id) {
                $term = get_term($term_id, $taxonomy);
                if(isset($term->term_taxonomy_id) && isset($trash_folders[$term->term_taxonomy_id])) {
                    unset($trash_folders[$term->term_taxonomy_id]);
                }
            }

            if($initial_trash_folders != $trash_folders) {
                delete_transient("premio_folders_without_trash");
                set_transient("premio_folders_without_trash", $trash_folders, 3*DAY_IN_SECONDS);
            }
        }
    }

    public static function check_for_setting($key, $setting, $default = "") {
        $license_data = self::get_license_key_data();
        $valid = 0;
        if (!empty($license_data)) {
            if (!empty($license_data)) {
                if ($license_data['license'] == "valid") {
                    return true;
                } else if ($license_data['license'] == "expired") {
                    return true;
                }
            }
        }

        if(self::$folderSettings === false) {
            $options = get_option("premio_folder_options");
            if($options === false || !is_array($options)) {
	            $options = array();
            }
	        self::$folderSettings = $options;
        }
        if($setting == "folders_settings") {
            if(isset(self::$folderSettings[ $setting ]) && is_array(self::$folderSettings[ $setting ])) {
                return in_array($key, self::$folderSettings[ $setting ]);
            }
        } else {
	        if ( isset( self::$folderSettings[ $setting ][ $key ] ) ) {
		        return self::$folderSettings[ $setting ][ $key ];
	        }
        }
	    return false;
    }

    public function update_user_folder_status() {
        $response = array();
        $response['status'] = 0;
        $response['error'] = 0;
        $response['data'] = array();
        $response['message'] = "";
        $postData = filter_input_array(INPUT_POST);
        $errorCounter = 0;

        if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] =  esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else {
            $nonce = self::sanitize_options($postData['nonce']);
            if(!wp_verify_nonce($nonce, 'wcp_update_user_folder_status')) {
                $response['message'] =  esc_html__("Your request is not valid", 'folders');
                $errorCounter++;
            }
        }
        if ($errorCounter == 0) {
            $status = isset($postData['status'])?$postData['status']:"";
            $status = ($status == "on")?"on":"off";
            $customize_folders = get_option('customize_folders');
            $customize_folders['folders_by_users'] = $status;
            update_option("customize_folders", $customize_folders);
            $response['status'] = 1;
        }
        echo json_encode($response); die;
    }

    public function update_dynamic_folder_status() {
        $response = array();
        $response['status'] = 0;
        $response['error'] = 0;
        $response['data'] = array();
        $response['message'] = "";
        $postData = filter_input_array(INPUT_POST);
        $errorCounter = 0;

        if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] =  esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else {
            $nonce = self::sanitize_options($postData['nonce']);
            if(!wp_verify_nonce($nonce, 'dynamic_folders_for_admin_only')) {
                $response['message'] =  esc_html__("Your request is not valid", 'folders');
                $errorCounter++;
            }
        }
        if ($errorCounter == 0) {
            $status = isset($postData['status'])?$postData['status']:"";
            $status = ($status == "on")?"on":"off";
            $customize_folders = get_option('customize_folders');
            $customize_folders['dynamic_folders_for_admin_only'] = $status;
            update_option("customize_folders", $customize_folders);
            $response['status'] = 1;
        }
        echo json_encode($response); die;
    }

    public function update_folders_uninstall_status() {
        $response = array();
        $response['status'] = 0;
        $response['error'] = 0;
        $response['data'] = array();
        $response['message'] = "";
        $postData = filter_input_array(INPUT_POST);
        $errorCounter = 0;

        if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] =  esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else {
            $nonce = self::sanitize_options($postData['nonce']);
            if(!wp_verify_nonce($nonce, 'wcp_folders_uninstall_status')) {
                $response['message'] =  esc_html__("Your request is not valid", 'folders');
                $errorCounter++;
            }
        }
        if ($errorCounter == 0) {
            $status = isset($postData['status'])?$postData['status']:"";
            $status = ($status == "on")?"on":"off";
            $customize_folders = get_option('customize_folders');
            $customize_folders['remove_folders_when_removed'] = $status;
            update_option("customize_folders", $customize_folders);
            $response['status'] = 1;
        }
        echo json_encode($response); die;
    }

    public function remove_all_folders_data() {
        $response = array();
        $response['status'] = 0;
        $response['error'] = 0;
        $response['data'] = array();
        $response['message'] = "";
        $postData = filter_input_array(INPUT_POST);
        $errorCounter = 0;

        if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] =  esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else {
            $type = self::sanitize_options($postData['type']);
            $nonce = self::sanitize_options($postData['nonce']);
            if(!wp_verify_nonce($nonce, 'remove_folders_data')) {
                $response['message'] =  esc_html__("Your request is not valid", 'folders');
                $errorCounter++;
            }
        }
        if ($errorCounter == 0) {
            self::$folders = 0;
            self::remove_folder_by_taxonomy("media_folder");
            self::remove_folder_by_taxonomy("folder");
            self::remove_folder_by_taxonomy("post_folder");
            $post_types = get_post_types( array( ), 'objects' );
            $post_array = array("page", "post", "attachment");
            foreach ( $post_types as $post_type ) {
                if(!in_array($post_type->name, $post_array)){
                    self::remove_folder_by_taxonomy($post_type->name . '_folder');
                }
            }
            delete_option('default_folders');
            $response['status'] = 1;
            $response['data'] = array(
                'items' => self::$folders
            );
        }
        echo json_encode($response); die;
    }

    public static function remove_folder_by_taxonomy($taxonomy) {
        global $wpdb;
        $folders = $wpdb->get_results(
            "SELECT * FROM " . $wpdb->term_taxonomy . "
					LEFT JOIN  " . $wpdb->terms . "
					ON  " . $wpdb->term_taxonomy . ".term_id =  " . $wpdb->terms . ".term_id
					WHERE " . $wpdb->term_taxonomy . ".taxonomy = '" . $taxonomy . "'
					ORDER BY parent ASC"
        );
        $folders = array_values( $folders );
        foreach ( $folders as $folder ) {
            $term_id = intval( $folder->term_id );
            if ( $term_id ) {
                $wpdb->delete( $wpdb->prefix . 'term_relationships', ['term_taxonomy_id' => $term_id] );
                $wpdb->delete( $wpdb->prefix . 'term_taxonomy', ['term_id' => $term_id] );
                $wpdb->delete( $wpdb->prefix . 'terms', ['term_id' => $term_id] );
                $wpdb->delete( $wpdb->prefix . 'termmeta', ['term_id' => $term_id] );
                self::$folders++;
            }
        }
    }

    public function add_media_overrides() {
        add_action( 'admin_footer', array($this, 'override_media_templates' ));
    }

    function override_media_templates(){
        if(self::is_for_this_post_type('attachment') || self::is_for_this_post_type('media')) {
            $customize_folders = get_option('customize_folders');
            $show_media_details = isset($customize_folders['show_media_details'])?$customize_folders['show_media_details']:"on";
            $media_col_settings = isset($customize_folders['media_col_settings']) && is_array($customize_folders['media_col_settings'])?$customize_folders['media_col_settings']:array("image_title","image_dimensions","image_type","image_date");
            if($show_media_details == "on") {
                $media_col_settings = !is_array($media_col_settings)?array("image_title","image_dimensions","image_type","image_date"):$media_col_settings;
                $col_count = count($media_col_settings);
                if($col_count == 2 && in_array("all", $media_col_settings)) {
                    $col_count = 1;
                }
                $col_count = ($col_count == 1)?"no-hidden":"";
                ?>
                <script type="text/html" id="tmpl-attachment-custom">
                    <div class="attachment-preview js--select-attachment type-{{ data.type }} subtype-{{ data.subtype }} {{ data.orientation }}">
                        <div class="thumbnail">
                            <# if ( data.uploading ) { #>
                            <div class="media-progress-bar"><div style="width: {{ data.percent }}%"></div></div>
                            <# } else if ( 'image' === data.type && data.size && data.size.url ) { #>
                            <div class="centered">
                                <img src="{{ data.size.url }}" draggable="false" alt="" />
                            </div>
                            <# } else { #>
                            <div class="centered">
                                <# if ( data.image && data.image.src && data.image.src !== data.icon ) { #>
                                <img src="{{ data.image.src }}" class="thumbnail" draggable="false" alt="" />
                                <# } else if ( data.sizes && data.sizes.medium ) { #>
                                <img src="{{ data.sizes.medium.url }}" class="thumbnail" draggable="false" alt="" />
                                <# } else { #>
                                <img src="{{ data.icon }}" class="icon" draggable="false" alt="" />
                                <# } #>
                            </div>
                            <div class="filename">
                                <div>{{ data.filename }}</div>
                            </div>
                            <# } #>
                        </div>
                        <# if ( data.buttons.close ) { #>
                        <button type="button" class="button-link attachment-close media-modal-icon"><span class="screen-reader-text"><?php _e( 'Remove' ); ?></span></button>
                        <# } #>
                        <div class="thumbnail-hover">
                            <div class="thumbnail-hover-box <?php echo esc_attr($col_count) ?>" >
                                <table>
                                    <?php if(in_array("image_title", $media_col_settings)) { ?>
                                        <tr>
                                            <th>Title</th>
                                            <td><span>{{ data.title }}</span></td>
                                        </tr>
                                    <?php } if(in_array("image_alt_text", $media_col_settings)) { ?>
                                        <tr>
                                            <th>Alternative Text</th>
                                            <td><span>{{ data.title }}</span></td>
                                        </tr>
                                    <?php } if(in_array("image_file_url", $media_col_settings)) { ?>
                                        <# if ( data.url ) { #>
                                        <tr>
                                            <th>File URL</th>
                                            <td><a target="_blank" href="{{ data.url }}"><span class="dashicons dashicons-admin-links"></span></a></td>
                                        </tr>
                                        <# } #>
                                    <?php } if(in_array("image_dimensions", $media_col_settings)) { ?>
                                        <# if ( 'image' === data.type && ! data.uploading && data.width && data.height ) { #>
                                        <tr>
                                            <th>Dimensions</th>
                                            <td>{{ data.width }} &times; {{ data.height }}</td>
                                        </tr>
                                        <# } #>
                                    <?php } if(in_array("image_size", $media_col_settings)) { ?>
                                        <tr>
                                            <th>Size</th>
                                            <td><span>{{ data.filesizeHumanReadable }}</span></td>
                                        </tr>
                                    <?php } if(in_array("image_file_name", $media_col_settings)) { ?>
                                        <tr>
                                            <th>File name</th>
                                            <td><span>{{ data.filename }}</span></td>
                                        </tr>
                                    <?php } if(in_array("image_type", $media_col_settings)) { ?>
                                        <tr>
                                            <th>Type</th>
                                            <td><span>{{ data.type }}</span></td>
                                        </tr>
                                    <?php } if(in_array("image_date", $media_col_settings)) { ?>
                                        <tr>
                                            <th>Date</th>
                                            <td><span>{{ data.dateFormatted }}</span></td>
                                        </tr>
                                    <?php } if(in_array("image_uploaded_by", $media_col_settings)) { ?>
                                        <tr>
                                            <th>Uploaded by</th>
                                            <td><span>{{ data.authorName }}</span></td>
                                        </tr>
                                    <?php } ?>
                                </table>
                            </div>
                        </div>
                    </div>

                    <# if ( data.buttons.check ) { #>
                    <button type="button" class="check" tabindex="-1"><span class="media-modal-icon"></span><span class="screen-reader-text"><?php _e( 'Deselect' ); ?></span></button>
                    <# } #>
                    <#
                    var maybeReadOnly = data.can.save || data.allowLocalEdits ? '' : 'readonly';
                    if ( data.describe ) {
                    if ( 'image' === data.type ) { #>
                    <input type="text" value="{{ data.caption }}" class="describe" data-setting="caption"
                           aria-label="<?php esc_attr_e( 'Caption' ); ?>"
                           placeholder="<?php esc_attr_e( 'Caption&hellip;' ); ?>" {{ maybeReadOnly }} />
                    <# } else { #>
                    <input type="text" value="{{ data.title }}" class="describe" data-setting="title"
                    <# if ( 'video' === data.type ) { #>
                    aria-label="<?php esc_attr_e( 'Video title' ); ?>"
                    placeholder="<?php esc_attr_e( 'Video title&hellip;' ); ?>"
                    <# } else if ( 'audio' === data.type ) { #>
                    aria-label="<?php esc_attr_e( 'Audio title' ); ?>"
                    placeholder="<?php esc_attr_e( 'Audio title&hellip;' ); ?>"
                    <# } else { #>
                    aria-label="<?php esc_attr_e( 'Media title' ); ?>"
                    placeholder="<?php esc_attr_e( 'Media title&hellip;' ); ?>"
                    <# } #> {{ maybeReadOnly }} />
                    <# }
                    } #>
                </script>
                <script>
                    jQuery(document).ready( function($) {
                        if( typeof wp.media.view.Attachment != 'undefined' ){
                            wp.media.view.Attachment.prototype.template = wp.media.template( 'attachment-custom' );
                        }
                    });
                </script>
            <?php }
        }
    }

    public static function hexToRgb($hex, $alpha = false) {
        $hex      = str_replace('#', '', $hex);
        $length   = strlen($hex);
        $rgb['r'] = hexdec($length == 6 ? substr($hex, 0, 2) : ($length == 3 ? str_repeat(substr($hex, 0, 1), 2) : 0));
        $rgb['g'] = hexdec($length == 6 ? substr($hex, 2, 2) : ($length == 3 ? str_repeat(substr($hex, 1, 1), 2) : 0));
        $rgb['b'] = hexdec($length == 6 ? substr($hex, 4, 2) : ($length == 3 ? str_repeat(substr($hex, 2, 1), 2) : 0));
        if ( $alpha ) {
            $rgb['a'] = $alpha;
        }
        return $rgb;
    }

    public function wcp_folders_by_order(){
        $response = array();
        $response['status'] = 0;
        $response['error'] = 0;
        $response['data'] = array();
        $response['message'] = "";
        $postData = filter_input_array(INPUT_POST);
        $errorCounter = 0;

        if (!isset($postData['order']) || empty($postData['order'])) {
            $response['message'] =  esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if (!isset($postData['type']) || empty($postData['type'])) {
            $response['message'] =  esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] =  esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if ($postData['type'] == "page" && !current_user_can("edit_pages")) {
            $response['message'] =  esc_html__("You have not permission to update width", 'folders');
            $errorCounter++;
        } else if ($postData['type'] != "page" && !current_user_can("edit_posts")) {
            $response['message'] =  esc_html__("You have not permission to update width", 'folders');
            $errorCounter++;
        } else {
            $type = self::sanitize_options($postData['type']);
            $nonce = self::sanitize_options($postData['nonce']);
            if(!wp_verify_nonce($nonce, 'wcp_folder_nonce_'.$type)) {
                $response['message'] =  esc_html__("Your request is not valid", 'folders');
                $errorCounter++;
            }
        }
        if ($errorCounter == 0) {
            $response['status'] = 1;

            $order_field = $postData['order'];

            $order_by = "";
            $order = "ASC";

            if($order_field == "a-z" || $order_field == "z-a") {
                $order_by = 'title';
                if($order_field == "z-a") {
                    $order = "DESC";
                }
            } else if($order_field == "n-o" || $order_field == "o-n") {
                $order_by = 'ID';
                if($order_field == "o-n") {
                    $order = "ASC";
                } else {
                    $order = "DESC";
                }
            }

            if(empty($order_by)) {
                $order = "";
            }

            $folder_type = self::get_custom_post_type($postData['type']);
            /* Do not change: Free/Pro Class name change */

            $sticky_open = get_option("premio_folder_sticky_status_".$postData['type']);
            $sticky_open = ($sticky_open == 1)?1:0;
            $tree_data = WCP_Pro_Tree::get_full_tree_data($folder_type, $order_by, $order, $sticky_open, self::$foldersByUser);

            $response['data'] = $tree_data['string'];
            $taxonomies = array();
            if($postData['type'] == "attachment") {
                $taxonomies = self::get_terms_hierarchical($folder_type);
            }
            $response['terms'] = $taxonomies;
        }
        echo json_encode($response); die;
    }

    public function save_folder_last_status(){
        $postData = filter_input_array(INPUT_POST);
        $error = 0;
        if (!isset($postData['post_id']) || empty($postData['post_id'])) {
            $response['message'] =  esc_html__("Your request is not valid", 'folders');
            $error = 1;
        } else if (!isset($postData['post_type']) || empty($postData['type'])) {
            $response['message'] =  esc_html__("Your request is not valid", 'folders');
            $error = 1;
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] =  esc_html__("Your request is not valid", 'folders');
            $error = 1;
        } else if ($postData['post_type'] == "page" && !current_user_can("edit_pages")) {
            $response['message'] =  esc_html__("You have not permission to update width", 'folders');
            $error = 1;
        } else if ($postData['post_type'] != "page" && !current_user_can("edit_posts")) {
            $response['message'] =  esc_html__("You have not permission to update width", 'folders');
            $error = 1;
        }
        if($error == 0) {
            $post_type = isset($postData['post_type'])?$postData['post_type']:"";
            $post_type = $this->filter_string_polyfill($post_type);
            $post_id= isset($postData['post_id'])?$postData['post_id']:"";
            $post_id = $this->filter_string_polyfill($post_id);
            if (!empty($post_type) && !empty($post_id)) {
                delete_option("last_folder_status_for" . $post_type);
                add_option("last_folder_status_for" . $post_type, $post_id);
            }
        }
    }

    public function save_premio_default_folder(){
        $response = array();
        $response['status'] = 0;
        $postData = filter_input_array(INPUT_POST);
        $error = 0;
        if (!isset($postData['post_slug']) || empty($postData['post_slug'])) {
            $response['message'] =  esc_html__("Your request is not valid", 'folders');
            $error = 1;
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] =  esc_html__("Your request is not valid", 'folders');
            $error = 1;
        } else if (!isset($postData['post_type'])) {
            $response['message'] =  esc_html__("Your request is not valid", 'folders');
            $error = 1;
        } else {
            $type = self::sanitize_options($postData['post_type']);
            $nonce = self::sanitize_options($postData['nonce']);
            if(!wp_verify_nonce($nonce, 'wcp_folder_nonce_'.$type)) {
                $response['message'] =  esc_html__("Your request is not valid", 'folders');
                $error = 1;
            }
        }
        if($error == 0) {
            $post_type = isset($_POST['post_type'])?$_POST['post_type']:"";
            $post_type = $this->filter_string_polyfill($post_type);
            $post_slug = isset($_POST['post_slug'])?$_POST['post_slug']:"";
            $post_slug = $this->filter_string_polyfill($post_slug);
            if (!empty($post_type) && !empty($post_slug)) {
                $default_folders = get_option('default_folders');
                if($default_folders === false) {
                    $default_folders = array();
                    $default_folders[$post_type] = $post_slug;
                    add_option("default_folders", $default_folders);
                } else {
                    if(!is_array($default_folders)) {
                        $default_folders = array();
                    }
                    $default_folders[$post_type] = $post_slug;
                    update_option("default_folders", $default_folders);
                }
                $response['status'] = 1;
                $response['folder_id'] = esc_attr($postData['folder_id']);
            }
        }
        echo json_encode($response);
        die;
    }

    public function remove_premio_default_folder(){
        $response = array();
        $response['status'] = 0;
        $postData = filter_input_array(INPUT_POST);
        $error = 0;
        if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] =  esc_html__("Your request is not valid", 'folders');
            $error = 1;
        } else if (!isset($postData['post_type'])) {
            $response['message'] =  esc_html__("Your request is not valid", 'folders');
            $error = 1;
        } else {
            $type = self::sanitize_options($postData['post_type']);
            $nonce = self::sanitize_options($postData['nonce']);
            if(!wp_verify_nonce($nonce, 'wcp_folder_nonce_'.$type)) {
                $response['message'] =  esc_html__("Your request is not valid", 'folders');
                $error = 1;
            }
        }
        if($error == 0) {
            $post_type = isset($_POST['post_type'])?$_POST['post_type']:"";
            $post_type = $this->filter_string_polyfill($post_type);
            $default_folders = get_option('default_folders');
            if($default_folders === false) {
                $default_folders = array();
                $default_folders[$post_type] = "";
                add_option("default_folders", $default_folders);
            } else {
                if(!is_array($default_folders)) {
                    $default_folders = array();
                }
                $default_folders[$post_type] = "";
                update_option("default_folders", $default_folders);
            }
            $response['status'] = 1;
            $response['folder_id'] = esc_attr($postData['folder_id']);
        }
        echo json_encode($response);
        die;
    }

    public function media_modal_query_final_terms($request)
    {
        if (isset($_REQUEST['action']) && $_REQUEST['action'] == "mla-query-attachments") {
            $query = isset($_REQUEST['query']) ? $_REQUEST['query'] : array();
            if (isset($query['media_folder']) && !empty($query['media_folder'])) {
                if ($query['media_folder'] == -1) {
                    $tax_query = array(
                        'taxonomy' => 'media_folder',
                        'operator' => 'NOT EXISTS',
                    );
                    $request['tax_query'] = array($tax_query);
                    $request = apply_filters('media_library_organizer_media_filter_attachments', $request, $_REQUEST);
                } else {
                    $request['media_folder'] = $query['media_folder'];
                }
            }
        }
        return $request;
    }

    public function get_terms_filter_without_trash($terms, $taxonomies, $args) {
        $isForFolders = 0;
        if(!empty($taxonomies) && is_array($taxonomies) && count($taxonomies)){
            foreach ($taxonomies as $taxonomy) {
                if (in_array($taxonomy, array("media_folder", "folder", "post_folder"))) {
                    $isForFolders = 1;
                } else {
                    $folder = substr($taxonomy, -7);
                    if ($folder == "_folder") {
                        $isForFolders = 1;
                    }
                }
            }
        }

        if($isForFolders) {
            global $wpdb;
            if (!is_array($terms) && count($terms) < 1) {
                return $terms;
            }

            $trash_folders = $initial_trash_folders = get_transient("premio_folders_without_trash");

            if ($trash_folders === false) {
                $trash_folders = array();
                $initial_trash_folders = array();
            }

            $post_table = $wpdb->prefix . "posts";
            $term_table = $wpdb->prefix . "term_relationships";
            $options = get_option('folders_settings');
            $option_array = array();
            if (!empty($options)) {
                foreach ($options as $option) {
                    $option_array[] = self::get_custom_post_type($option);
                }
            }
            foreach ($terms as $key => $term) {
                if (isset($term->term_id) && isset($term->taxonomy) && !empty($term->taxonomy) && in_array($term->taxonomy, $option_array)) {
                    $trash_count = null;
                    if (isset($trash_folders[$term->term_taxonomy_id])) {
                        $trash_count = $trash_folders[$term->term_taxonomy_id];
                    } else {
                        if (has_filter("premio_folder_item_in_taxonomy")) {
                            $post_type = "";
                            $taxonomy = $term->taxonomy;

                            if ($taxonomy == "post_folder") {
                                $post_type = "post";
                            } else if ($taxonomy == "folder") {
                                $post_type = "page";
                            } else if ($taxonomy == "media_folder") {
                                $post_type = "attachment";
                            } else {
                                $post_type = trim($taxonomy, "'_folder'");
                            }
                            $arg = array(
                                'post_type' => $post_type,
                                'taxonomy' => $taxonomy,
                            );
                            $trash_count = apply_filters("premio_folder_item_in_taxonomy", $term->term_id, $arg);
                        }

                        if ($trash_count === null) {
                            //$result = $wpdb->get_var("SELECT COUNT(*) FROM {$post_table} p JOIN {$term_table} rl ON p.ID = rl.object_id WHERE rl.term_taxonomy_id = '{$term->term_taxonomy_id}' AND p.post_status != 'trash' LIMIT 1");
                            $query = "SELECT COUNT(DISTINCT(p.ID)) 
                        FROM {$post_table} p 
                            JOIN {$term_table} rl ON p.ID = rl.object_id 
                            WHERE rl.term_taxonomy_id = '{$term->term_taxonomy_id}' AND p.post_status != 'trash' LIMIT 1";
                            $result = $wpdb->get_var($query);
                            if (intval($result) > 0) {
                                $trash_count = intval($result);
                            } else {
                                $trash_count = 0;
                            }
                        }
                    }
                    if ($trash_count === null) {
                        $trash_count = 0;
                    }
                    $terms[$key]->trash_count = $trash_count;
                    $trash_folders[$term->term_taxonomy_id] = $trash_count;
                }
            }

            if (!empty($terms) && $initial_trash_folders != $trash_folders) {
                delete_transient("premio_folders_without_trash");
                set_transient("premio_folders_without_trash", $trash_folders, 3 * DAY_IN_SECONDS);
            }
        }
        return $terms;
    }

    public function custom_bulk_action($bulk_actions) {
        $bulk_actions['move_to_folder'] = esc_html__( 'Move to Folder', 'email_to_eric');
        return $bulk_actions;
    }

    public function admin_footer_for_media(){
        echo "<style>";
        $customize_folders = get_option('customize_folders');
        if(isset($customize_folders['dropdown_color']) && !empty($customize_folders['dropdown_color'])) {
            ?>
            #media-attachment-taxonomy-filter, .post-upload-ui .folder_for_media, select.media-select-folder { border-color: <?php echo esc_attr($customize_folders['dropdown_color']) ?>; color: <?php echo esc_attr($customize_folders['dropdown_color']) ?> }
            .folder_for_media option {color:#000000;}
            .folder_for_media option:first-child {
            font-weight: bold;
            }
            <?php
        }
        echo "</style>";

        if(isset($_REQUEST['attachment-filter']) && $_REQUEST['attachment-filter'] == "trash") { ?>
            <div class="media-clean-delete-box" id="show_delete_box">
                <div class="media-clean-delete-box-overlay"></div>
                <div class="media-clean-delete-box-content">
                    <div class="media-clean-delete-box-border"></div>
                    <div class="close-icon"><span class="dashicons dashicons-no-alt"></span></div>
                    <div class="media-clean-delete-box-title">
                        <p class="title-que"><span class="dashicons dashicons-info-outline icon-background-color"></span>
                            <span class="pd"><?php esc_html_e("Are you sure about deleting the media files?","folders") ?></p></span>
                    </div>
                    <div class="media-clean-delete-box-desc">
                        Are you sure want to <b>delete the media files</b>? By doing this, you’ll <b>permanently delete</b> your selected media. You’ll <b>NOT be able to retrieve</b> the media once you <b>permanently delete it.</b> Be careful 🙏
                    </div>
                    <div class="media-clean-delete-box-footer">
                        <a href="javascript:;" class="cancel-button" id="cancel_button"><?php esc_html_e("Cancel","folders") ?></a>
                        <button class="delete-button" id="delete_button"><?php echo esc_html__("Delete permanently","folders") ?></button>
                    </div>
                </div>
            </div>

            <script>
                var isMediaFormSubmitted = false;
                var mediaTrashLink = "";
                jQuery(document).ready(function(){
                    jQuery(document).on("click", ".close-icon, .cancel-button", function(){
                        jQuery(".media-clean-delete-box").hide();
                    });
                    jQuery(document).on("click", ".media-clean-delete-box-overlay", function(){
                        jQuery(".media-clean-delete-box").hide();
                    });
                    jQuery(document).on("click", ".media-clean-delete-box-content", function(e){
                        e.stopPropagation();
                    });
                    jQuery(document).on("click", ".row-actions .delete a", function(e){
                        e.stopPropagation();
                        e.preventDefault();
                        mediaTrashLink = jQuery(this).attr("href");
                        jQuery("#show_delete_box").show();
                    });
                    jQuery(document).on("click", "#delete_button", function(e){
                        e.stopPropagation();
                        e.preventDefault();
                        window.location = mediaTrashLink;
                    });
                })
            </script>
        <?php }
    }

    public function check_for_default_folders() {
        global $typenow, $current_screen;
        $isAjax = (defined('DOING_AJAX') && DOING_AJAX)?1:0;
        $options = get_option('folders_settings');
        $options = (empty($options) || !is_array($options))?array():$options;
        $post_status = isset($_GET['post_status'])?$_GET['post_status']:"";
        $post_status = $this->filter_string_polyfill($post_status);
        $last_status = get_option("last_folder_status_for".$typenow);
        $mode_default = "";
        if(empty($post_status) && !$isAjax && (in_array($typenow, $options) || !empty($last_status)) && (isset($current_screen->base) && ($current_screen->base == "edit" || ($current_screen->base == "upload")))) {

            $requests = filter_input_array(INPUT_GET);
            $requests = empty($requests)||!is_array($requests)?array():$requests;

            if ($typenow == "attachment") {
                if(count($requests) > 0) {
                    return;
                }
            } else if ($typenow == "post") {
                if(count($requests) > 0) {
                    return;
                }
            } else {
                if(count($requests) > 1) {
                    return;
                }
            }

            $media_string = "";
            if ($typenow == "attachment" && function_exists("get_current_user_id")) {
                $mode_default = get_user_option("media_library_mode_default", get_current_user_id());
                if($mode_default !== false && ($mode_default == "list" || $mode_default == "grid")) {
                    $media_string .= "&mode=".$mode_default;
                }
                delete_user_option(get_current_user_id(), "media_library_mode_default");
            }

            if(!empty($last_status)) {
                $status = 1;
                if($last_status != "-1" && $last_status != "all") {
                    $type = self::get_custom_post_type($typenow);
                    $term = get_term_by('slug', $last_status, $type);
                    if (empty($term) || !is_object($term)) {
                        $status = 0;
                    }
                }
                delete_option("last_folder_status_for".$typenow);
                if($last_status == "all") {
                    $last_status = "";
                }
                if($status) {
                    if ($typenow == "attachment") {
                        if (!isset($_REQUEST['media_folder'])) {
                            $admin_url = admin_url("upload.php?post_type=attachment&media_folder=");
                            $admin_url .= $last_status.$media_string;
                            ?>
                            <script>
                                window.location = '<?php echo $admin_url ?>';
                            </script>
                            <?php
                            exit;
                        }
                    } else {
                        $post_type = self::get_custom_post_type($typenow);
                        $admin_url = admin_url("edit.php?post_type=" . $typenow);
                        $admin_url .= "&{$post_type}=";
                        if (!isset($_REQUEST[$post_type])) {
                            $admin_url .= $last_status.$media_string;
                            ?>
                            <script>
                                window.location = '<?php echo $admin_url ?>';
                            </script>
                            <?php
                            exit;
                        }
                    }
                }
            }

            $default_folders = get_option('default_folders');
            $default_folders = (empty($default_folders) || !is_array($default_folders))?array():$default_folders;

            $status = 1;
            if(isset($default_folders[$typenow]) && !empty($default_folders[$typenow])) {
                $type = self::get_custom_post_type($typenow);
                if($default_folders[$typenow] != -1) {
                    $term = get_term_by('slug', $default_folders[$typenow], $type);
                    if (empty($term) || !is_object($term)) {
                        $status = 0;
                    }
                }
            } else {
                $status = 0;
            }
            if($status) {
                if ($typenow == "attachment") {
                    $admin_url = admin_url("upload.php?post_type=attachment&media_folder=");
                    if (!isset($_REQUEST['media_folder'])) {
                        if (isset($default_folders[$typenow]) && !empty($default_folders[$typenow])) {
                            $admin_url .= $default_folders[$typenow].$media_string;
                            ?>
                            <script>
                                window.location = '<?php echo $admin_url ?>';
                            </script>
                            <?php
                            exit;
                        }
                    }
                } else {
                    $admin_url = admin_url("edit.php?post_type=" . $typenow);
                    if (isset($_GET['s']) && !empty($_GET['s'])) {
                        $admin_url .= "&s=" . $_GET['s'];
                    }
                    $post_type = self::get_custom_post_type($typenow);
                    $admin_url .= "&{$post_type}=";
                    if (!isset($_REQUEST[$post_type])) {
                        if (isset($default_folders[$typenow]) && !empty($default_folders[$typenow])) {
                            $admin_url .= $default_folders[$typenow].$media_string;
                            ?>
                            <script>
                                window.location = '<?php echo $admin_url ?>';
                            </script>
                            <?php
                            exit;
                        }
                    }
                }
            }
        }

        if(!empty($mode_default)) {
            $mode = get_user_option('media_library_mode', get_current_user_id()) ? get_user_option('media_library_mode', get_current_user_id()) : 'grid';
            if(($mode_default == "list" || $mode_default == "grid") && $mode != $mode_default && function_exists('get_current_user_id')) {
                update_user_option( get_current_user_id(), 'media_library_mode', $mode_default );
                if(isset($current_screen->base) && $current_screen->base == "upload") {
                    ?>
                    <script>
                        window.location.reload(true);
                    </script>
                    <?php
                }
            }
        }
    }

    public function folder_update_status() {
        if(!empty($_REQUEST['nonce']) && wp_verify_nonce($_REQUEST['nonce'], 'folder_update_status')) {
            $status = isset($_POST['status'])?$_POST['status']:"";
            $status = $this->filter_string_polyfill($status);

            $email = isset($_POST['email'])?$_POST['email']:"";
            $email = $this->filter_string_polyfill($email);
            update_option("folder_update_message", 2);
            if($status == 1) {
                $url = 'https://go.premio.io/api/update.php?email='.$email.'&plugin=folders';
                $handle = curl_init();
                curl_setopt($handle, CURLOPT_URL, $url);
                curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($handle);
                curl_close($handle);
            }
        }
        echo "1";
        die;
    }

    public function add_attachment_category($post_ID)
    {
        if(self::is_for_this_post_type('attachment') || self::is_for_this_post_type('media')) {
            $folder_id = isset($_REQUEST["folder_for_media"]) ? $_REQUEST["folder_for_media"] : null;
            if ($folder_id !== null) {
                $folder_id = (int)$folder_id;
                $folder_id = self::sanitize_options($folder_id, "int");
                if ($folder_id > 0) {
                    $post_type = self::get_custom_post_type("attachment");
                    $term = get_term($folder_id);
                    if(!empty($term) && isset($term->slug)) {
                        wp_set_object_terms($post_ID, $term->slug, $post_type );

                        $trash_folders = $initial_trash_folders = get_transient("premio_folders_without_trash");
                        if($trash_folders === false) {
                            $trash_folders = array();
                            $initial_trash_folders = array();
                        }

                        if(isset($trash_folders[$term->term_taxonomy_id])) {
                            unset($trash_folders[$term->term_taxonomy_id]);
                        }

                        if($initial_trash_folders != $trash_folders) {
                            delete_transient("premio_folders_without_trash");
                            set_transient("premio_folders_without_trash", $trash_folders, 3*DAY_IN_SECONDS);
                        }
                    }
                }
            }
        }
    }

    public function show_dropdown_on_media_screen() {
        if(self::is_for_this_post_type('attachment')) {
            $post_type = self::get_custom_post_type('attachment');
            global $typenow, $current_screen;
            /* Free/Pro Class name change */
            if(!class_exists('WCP_Pro_Tree')) {
                $files = array(
                    'WCP_Tree_View' => WCP_DS . "includes" . WCP_DS . "tree.class.php"
                );

                foreach ($files as $file) {
                    if (file_exists(dirname(dirname(__FILE__)) . $file)) {
                        include_once dirname(dirname(__FILE__)) . $file;
                    }
                }
            }
            /* Free/Pro Class name change */
            $folder_by_user = 0;
            if(self::$foldersByUser) {
                $user_id = get_current_user_id();
                $folder_by_user = $user_id;
                if(function_exists("wp_get_current_user")) {
                    $user = wp_get_current_user();
                    $user_roles = (array)$user->roles;
                    $user_roles = !is_array($user_roles) ? array() : $user_roles;
                    if (in_array("administrator", $user_roles)) {
                        $folder_by_user = 0;
                    }
                }
            }

            $options = WCP_Pro_Tree::get_folder_option_data($post_type, 0, "", $folder_by_user);?>
            <p class="attachments-category"><?php esc_html_e("Select a folder (Optional)", 'folders') ?></p>
            <p class="attachments-category"><?php esc_html_e("First select the folder, and then upload the files", 'folders') ?><br/></p>
            <p>
                <?php
                $request = $_SERVER['REQUEST_URI'];
                $request = strpos($request, "post.php");
                ?>
                <select name="folder_for_media" class="folder_for_media">
                    <option value="-1">- <?php esc_html_e('Unassigned', 'folders') ?></option>
                    <?php echo $options ?>
                    <?php if(($typenow == "attachment" && isset($current_screen->base) && $current_screen->base == "upload") || ($request !== false) || self::is_for_this_post_type('attachment') || self::is_for_this_post_type('media')) {?>
                        <option value="add-folder"><?php esc_html_e('+ Create a New Folder', 'folders') ?></option>
                    <?php } ?>
                </select>
            </p>
            <?php
        }
    }

    public function wcp_hide_folders()
    {
        $response = array();
        $response['status'] = 0;
        $response['error'] = 0;
        $response['data'] = array();
        $response['message'] = "";
        $postData = filter_input_array(INPUT_POST);
        $errorCounter = 0;

        if (!isset($postData['status']) || empty($postData['status'])) {
            $response['message'] =  esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if (!isset($postData['type']) || empty($postData['type'])) {
            $response['message'] =  esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] =  esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if ($postData['type'] == "page" && !current_user_can("edit_pages")) {
            $response['message'] =  esc_html__("You have not permission to update width", 'folders');
            $errorCounter++;
        } else if ($postData['type'] != "page" && !current_user_can("edit_posts")) {
            $response['message'] =  esc_html__("You have not permission to update width", 'folders');
            $errorCounter++;
        } else {
            $type = self::sanitize_options($postData['type']);
            $nonce = self::sanitize_options($postData['nonce']);
            if(!wp_verify_nonce($nonce, 'wcp_folder_nonce_'.$type)) {
                $response['message'] =  esc_html__("Your request is not valid", 'folders');
                $errorCounter++;
            }
        }
        if ($errorCounter == 0) {
            $type = self::sanitize_options($postData['type']);
            $status = self::sanitize_options($postData['status']);
            $optionName = "wcp_folder_display_status_" . $type;
            update_option($optionName, $status);
            $response['status'] = 1;
        }
        echo json_encode($response);
        wp_die();
    }

    public function wcp_change_folder_display_status()
    {
        $response = array();
        $response['status'] = 0;
        $response['error'] = 0;
        $response['data'] = array();
        $response['message'] = "";
        $postData = filter_input_array(INPUT_POST);
        $errorCounter = 0;
        if (!isset($postData['status']) || empty($postData['status'])) {
            $response['message'] =  esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if (!isset($postData['type']) || empty($postData['type'])) {
            $response['message'] =  esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] =  esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if ($postData['type'] == "page" && !current_user_can("edit_pages")) {
            $response['message'] =  esc_html__("You have not permission to update width", 'folders');
            $errorCounter++;
        } else if ($postData['type'] != "page" && !current_user_can("edit_posts")) {
            $response['message'] =  esc_html__("You have not permission to update width", 'folders');
            $errorCounter++;
        } else {
            $type = self::sanitize_options($postData['type']);
            $nonce = self::sanitize_options($postData['nonce']);
            if(!wp_verify_nonce($nonce, 'wcp_folder_nonce_'.$type)) {
                $response['message'] =  esc_html__("Your request is not valid", 'folders');
                $errorCounter++;
            }
        }
        if ($errorCounter == 0) {
            $type = self::sanitize_options($postData['type']);
            $width = self::sanitize_options($postData['status']);
            $optionName = "wcp_dynamic_display_status_" . $type;
            update_option($optionName, $width);
            $response['status'] = 1;
        }
        echo json_encode($response);
        wp_die();
    }

    public function premio_check_for_other_folders() {
        $response = array();
        $response['status'] = 0;
        $response['error'] = 0;
        $response['data'] = array();
        $response['message'] = "";
        $postData = filter_input_array(INPUT_POST);
        $errorCounter = 0;
        if (!isset($postData['post_id']) || empty($postData['post_id'])) {
            $response['message'] =  esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if (!isset($postData['type']) || empty($postData['type'])) {
            $response['message'] =  esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] =  esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if(!wp_verify_nonce($postData['nonce'], 'wcp_folder_nonce_'.$postData['type'])) {
            $response['message'] =  esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if(!current_user_can("manage_categories")) {
            $response['message'] =  esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        }
        if ($errorCounter == 0) {
            $folderUndoSettings = array();
            $type = self::sanitize_options($postData['type']);
            $post_id = self::sanitize_options($postData['post_id']);

            $post_id = explode(",", $post_id);

            $taxonomy = self::get_custom_post_type($type);

            foreach ($post_id as $id) {
                $terms = get_the_terms($id, $taxonomy);
                if(!empty($terms) && is_array($terms)) {
                    foreach($terms as $term) {
                        if($term->term_id != $postData['taxonomy']) {
                            $response['status'] = -1;
                            $response['data']['post_id'] = $postData['post_id'];
                            echo json_encode($response);
                            wp_die();
                        }
                    }
                }
            }
            $this->wcp_remove_post_folder();
        }
        echo json_encode($response);
        wp_die();
    }

    public function wcp_remove_post_folder() {
        $response = array();
        $response['status'] = 0;
        $response['error'] = 0;
        $response['data'] = array();
        $response['message'] = "";
        $postData = filter_input_array(INPUT_POST);
        $errorCounter = 0;
        if (!isset($postData['post_id']) || empty($postData['post_id'])) {
            $response['message'] =  esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if (!isset($postData['type']) || empty($postData['type'])) {
            $response['message'] =  esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] =  esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if(!wp_verify_nonce($postData['nonce'], 'wcp_folder_nonce_'.$postData['type'])) {
            $response['message'] =  esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if(!current_user_can("manage_categories")) {
            $response['message'] =  esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        }
        if ($errorCounter == 0) {
            $folderUndoSettings = array();
            $type = self::sanitize_options($postData['type']);
            $post_id = self::sanitize_options($postData['post_id']);

            $post_id = explode(",", $post_id);

            $taxonomy = self::get_custom_post_type($type);

            foreach($post_id as $id) {
                if(!empty($id) && is_numeric($id) && $id > 0) {
                    $terms = get_the_terms($id, $taxonomy);
                    $post_terms = array(
                        'post_id' => $id,
                        'terms' => $terms
                    );
                    $folderUndoSettings[] = $post_terms;
                    if(isset($postData['remove_from']) && $postData['remove_from'] == "current" && isset($postData['remove_from']) && $postData['remove_from'] == "current" && isset($postData['active_folder']) && is_numeric($postData['active_folder'])) {
                        wp_remove_object_terms($id, intval($postData['active_folder']), $taxonomy);
                    } else {
                        wp_delete_object_term_relationships($id, $taxonomy);
                    }
                }
            }
            delete_transient("folder_undo_settings");
            set_transient("folder_undo_settings", $folderUndoSettings, DAY_IN_SECONDS);
            $response['status'] = 1;
        }
        echo json_encode($response);
        wp_die();
    }

    public function filter_record_list($query) {
        global $typenow;

        if($typenow == "attachment") {
            return;
        }

        if(!self::is_for_this_post_type($typenow)) {
            return $query;
        }

        $taxonomy = self::get_custom_post_type($typenow);

        if ( ! isset( $query->query['post_type'] ) ) {
            return $query;
        }

        if ( ! isset( $_REQUEST[$taxonomy] ) ) {
            return $query;
        }

        $term = sanitize_text_field( $_REQUEST[$taxonomy] );
        if ( $term != "-1" ) {
            return $query;
        }

        unset( $query->query_vars[$taxonomy] );

        $tax_query = array(
            'taxonomy'  => $taxonomy,
            'operator'  => 'NOT EXISTS',
        );

        $query->set( 'tax_query', array( $tax_query ) );
        $query->tax_query = new WP_Tax_Query( array( $tax_query ) );

        return $query;
    }

    public function wcp_get_default_list() {

        $postData = filter_input_array(INPUT_POST);

        $post_type = $postData['type'];

        $ttpsts = $this->get_ttlpst($post_type);

        $empty_items = self::get_tempt_posts($post_type);

        $post_type = self::get_custom_post_type($post_type);

        $taxonomies = self::get_terms_hierarchical($post_type);

        $response = array(
            'status' => 1,
            'total_items' => $ttpsts,
            'taxonomies' => $taxonomies,
            'empty_items' => $empty_items
        );
        echo json_encode($response);
        wp_die();
    }

    function get_folders_default_list() {
        $postData = filter_input_array(INPUT_POST);

        $post_type = $postData['type'];

        $ttpsts = $this->get_ttlpst($post_type);

        $empty_items = self::get_tempt_posts($post_type);

        $post_type = self::get_custom_post_type($post_type);

        $taxonomies = self::get_terms_hierarchical($post_type);

        $response = array(
            'status' => 1,
            'total_items' => $ttpsts,
            'empty_items' => $empty_items,
            'taxonomies' => $taxonomies
        );
        echo json_encode($response);
        die;

    }

    function save_media_terms( $post_id ) {
        if ( wp_is_post_revision( $post_id ) ) {
            return;
        }
        $post = get_post($post_id);
        if($post->post_type !== 'attachment') {
            return;
        }
        $post_type = self::get_custom_post_type('attachment');
        $selected_folder = get_option("selected_{$post_type}_folder");
        if($selected_folder != null && !empty($selected_folder)) {
            $terms = get_term($selected_folder);
            if(!empty($terms) && isset($terms->term_id)) {
                wp_set_post_terms($post_id, $terms->term_id, $post_type, false);
            }
        }
    }

    public function filter_attachments_grid( $args ) {
        $taxonomy = 'media_folder';
        if ( ! isset( $args[ $taxonomy ] ) ) {
            return $args;
        }
        $term = sanitize_text_field( $args[ $taxonomy ] );
        if ( $term != "-1" ) {
            return $args;
        }
        unset( $args[ $taxonomy ] );
        $args['tax_query'] = array(
            array(
                'taxonomy'  => $taxonomy,
                'operator'  => 'NOT EXISTS',
            ),
        );
        $args = apply_filters( 'media_library_organizer_media_filter_attachments_grid', $args );
        return $args;
    }

    public function get_tempt_posts($post_type = "")
    {
        global $wpdb;

        $post_table = $wpdb->prefix."posts";
        $term_table = $wpdb->prefix."term_relationships";
        $term_taxonomy_table = $wpdb->prefix."term_taxonomy";
        $term_meta = $wpdb->prefix."termmeta";
        $taxonomy = self::get_custom_post_type($post_type);
        $tlrcds = null;
        if(has_filter("premio_folder_un_categorized_items")) {
            $tlrcds = apply_filters("premio_folder_un_categorized_items", $post_type, $taxonomy);
        }
        if($tlrcds === null) {
            $user_filter = self::$foldersByUser;
            $user_id = get_current_user_id();
            if($user_filter) {
                if(function_exists("wp_get_current_user")) {
                    $user = wp_get_current_user();
                    $user_roles = (array)$user->roles;
                    $user_roles = !is_array($user_roles) ? array() : $user_roles;
                    if (in_array("administrator", $user_roles)) {
                        $user_filter = false;
                    }
                }
            }

            if(!$user_filter) {
                if ($post_type != "attachment") {
                    $query = "SELECT COUNT(DISTINCT({$post_table}.ID)) AS total_records FROM {$post_table} WHERE 1=1  AND (
                                NOT EXISTS (
                                    SELECT 1
                                    FROM {$term_table}
                                    INNER JOIN {$term_taxonomy_table}
                                    ON {$term_taxonomy_table}.term_taxonomy_id = {$term_table}.term_taxonomy_id
                                    WHERE {$term_taxonomy_table}.taxonomy = '%s'
                                    AND {$term_table}.object_id = {$post_table}.ID
                                )
                             ) AND {$post_table}.post_type = '%s' AND (({$post_table}.post_status = 'publish' OR {$post_table}.post_status = 'future' OR {$post_table}.post_status = 'draft' OR {$post_table}.post_status = 'private' OR {$post_table}.post_status = 'pending'))";
                } else {
                    $query = "SELECT COUNT(DISTINCT({$post_table}.ID)) AS total_records FROM {$post_table} WHERE 1=1  AND (
                                NOT EXISTS (
                                        SELECT 1
                                        FROM {$term_table}
                                        INNER JOIN {$term_taxonomy_table}
                                        ON {$term_taxonomy_table}.term_taxonomy_id = {$term_table}.term_taxonomy_id
                                        WHERE {$term_taxonomy_table}.taxonomy = '%s'
                                        AND {$term_table}.object_id = {$post_table}.ID
                                    )
                                ) AND {$post_table}.post_type = '%s' AND {$post_table}.post_status = 'inherit'";
                }
            } else {
                if ($post_type != "attachment") {
                    $query = "SELECT COUNT(DISTINCT({$post_table}.ID)) AS total_records FROM {$post_table} WHERE 1=1  AND (
                                NOT EXISTS (
                                    SELECT 1
                                    FROM {$term_table}
                                    INNER JOIN {$term_taxonomy_table}
                                    ON {$term_taxonomy_table}.term_taxonomy_id = {$term_table}.term_taxonomy_id
                                    INNER JOIN {$term_meta}
                                    ON {$term_meta}.term_id = {$term_table}.term_taxonomy_id AND {$term_meta}.meta_key = 'created_by' AND {$term_meta}.meta_value = {$user_id}
                                    WHERE {$term_taxonomy_table}.taxonomy = '%s'
                                    AND {$term_table}.object_id = {$post_table}.ID
                                )
                             ) AND {$post_table}.post_type = '%s' AND (({$post_table}.post_status = 'publish' OR {$post_table}.post_status = 'future' OR {$post_table}.post_status = 'draft' OR {$post_table}.post_status = 'private' OR {$post_table}.post_status = 'pending'))";
                } else {
                    $query = "SELECT COUNT(DISTINCT({$post_table}.ID)) AS total_records FROM {$post_table} WHERE 1=1  AND (
                                NOT EXISTS (
                                        SELECT 1
                                        FROM {$term_table}
                                        INNER JOIN {$term_taxonomy_table}
                                        ON {$term_taxonomy_table}.term_taxonomy_id = {$term_table}.term_taxonomy_id
                                        INNER JOIN {$term_meta}
                                        ON {$term_meta}.term_id = {$term_table}.term_taxonomy_id AND {$term_meta}.meta_key = 'created_by' AND {$term_meta}.meta_value = {$user_id}
                                        WHERE {$term_taxonomy_table}.taxonomy = '%s'
                                        AND {$term_table}.object_id = {$post_table}.ID
                                    )
                                ) AND {$post_table}.post_type = '%s' AND {$post_table}.post_status = 'inherit'";
                }
            }

            $query = $wpdb->prepare($query, $taxonomy, $post_type);

            $tlrcds = $wpdb->get_var($query);
        }

        if(!empty($tlrcds)) {
            return $tlrcds;
        } else {
            return 0;
        }
    }

    public function output_backbone_view_filters() {

        global $typenow, $current_screen;
        $isAjax = (defined('DOING_AJAX') && DOING_AJAX)?1:0;
        $options = get_option('folders_settings');
        $options = (empty($options) || !is_array($options))?array():$options;
        $last_status = get_option("last_folder_status_for".$typenow);
        if(!$isAjax && (in_array($typenow, $options) || !empty($last_status)) && (isset($current_screen->base) && ($current_screen->base == "edit" || ($current_screen->base == "upload")))) {

            $default_folders = get_option('default_folders');
            $default_folders = (empty($default_folders) || !is_array($default_folders))?array():$default_folders;

            if(!empty($last_status)) {
                $status = 1;
                if($last_status != "-1" && $last_status != "all") {
                    $type = self::get_custom_post_type($typenow);
                    $term = get_term_by('slug', $last_status, $type);
                    if (empty($term) || !is_object($term)) {
                        $status = 0;
                    }
                }
                delete_option("last_folder_status_for".$typenow);
                if($last_status == "all") {
                    $last_status = "";
                }
                if($status) {
                    if ($typenow == "attachment") {
                        if (!isset($_REQUEST['media_folder'])) {
                            $admin_url = admin_url("upload.php?post_type=attachment&media_folder=");
                            $admin_url .= $last_status;
                            ?>
                            <script>
                                window.location = '<?php echo $admin_url ?>';
                            </script>
                            <?php
                            exit;
                        }
                    }
                }
            }

            $status = 1;
            if(isset($default_folders[$typenow]) && !empty($default_folders[$typenow])) {
                $type = self::get_custom_post_type($typenow);
                if($default_folders[$typenow] != -1) {
                    $term = get_term_by('slug', $default_folders[$typenow], $type);
                    if (empty($term) || !is_object($term)) {
                        $status = 0;
                    }
                }
            } else {
                $status = 0;
            }
            if($status) {
                if ($typenow == "attachment") {
                    $admin_url = admin_url("upload.php?post_type=attachment&media_folder=");
                    if (!isset($_REQUEST['media_folder'])) {
                        if (isset($default_folders[$typenow]) && !empty($default_folders[$typenow])) {
                            $admin_url .= $default_folders[$typenow];
                            ?>
                            <script>
                                window.location = '<?php echo $admin_url ?>';
                            </script>
                            <?php
                            exit;
                        }
                    }
                }
            }
        }

        if(!(self::is_for_this_post_type('attachment') || self::is_for_this_post_type('media'))) {
            return;
        }

        if ($typenow == "attachment") {
            /* Free/Pro URL Change */
            global $typenow;
            $is_active = 1;
            $folders = -1;

            $hasStars = self::check_for_setting("has_stars", "general");
            $hasChild = self::check_for_setting("has_child", "general");
            $hasChild = empty($hasChild)?0:1;
            $hasStars = empty($hasStars)?0:1;

            $dynamic_folders = new Premio_Pro_Folders_Dynamic_Folders();
            $all_folders = array();
            $all_folders[] = array(
                'name' => esc_html__("All Folders", "folders"),
                'value' => "dynamic-folders"
            );
            $authors = $dynamic_folders->get_author_dynamic_folders($typenow, "media_folder", "a");
            $dates = $dynamic_folders->get_date_dynamic_folders($typenow, "media_folder", "a");
            $extensions = $dynamic_folders->get_file_ext_dynamic_folders($typenow, "media_folder", "a");
            $file_extensions = array();
            $file_extensions[] = array(
                'name' => esc_html__("All Extensions", "folders"),
                'value' => 'extensions-all',
            );
            foreach ($extensions as $key=>$value) {
                $file_extensions[] = array(
                    'value' => "extensions-".$key,
                    'name' => "- ".$key
                );
            }

            $all_dynamic_folders = array_merge($all_folders, $authors, $dates, $file_extensions);

            /* Free/Pro URL Change */
            wp_enqueue_script( 'folders-media', WCP_PRO_FOLDER_URL.'assets/js/media.min.js', array( 'media-editor', 'media-views' ), false, true );
            wp_localize_script( 'folders-media', 'folders_media_options', array(
                'terms'     => self::get_terms_hierarchical('media_folder'),
                'taxonomy'  => get_taxonomy('media_folder'),
                'ajax_url'  => admin_url("admin-ajax.php"),
                'activate_url'  => $this->getRegisterKeyURL(),
                'nonce'     => wp_create_nonce('wcp_folder_nonce_attachment'),
                'is_key_active' => $is_active,
                'hasStars' => $hasStars,
                'dynamic_folders' => $all_dynamic_folders,
                'hasChildren' => $hasChild
            ));
            /* Free/Pro URL Change */
            wp_enqueue_style( 'folders-media', WCP_PRO_FOLDER_URL . 'assets/css/media.css' );
        } else if(!self::is_active_for_screen() && self::is_for_this_post_type('attachment')) {
            /* Free/Pro URL Change */
            global $typenow;
            global $current_screen;
//            echo "<pre>"; print_r($current_screen); die;
            if(!isset($current_screen->base) || $current_screen->base != "plugins") {

                remove_filter("terms_clauses", "TO_apply_order_filter");

                $is_active = 1;
                $folders = -1;

                /* Free/Pro URL Change */

                $is_rtl = 0;
                if (function_exists('is_rtl') && is_rtl()) {
                    $is_rtl = 1;
                }
                $can_manage_folder = current_user_can("manage_categories") ? 1 : 0;
                $width = 275;
                $taxonomy_status = 0;
                $selected_taxonomy = "";
                $show_in_page = false;
                $admin_url = admin_url("upload.php?post_type=attachment&media_folder=");

                $taxonomies = self::get_terms_hierarchical('media_folder');

                $folder_settings = array();
                foreach($taxonomies as $taxonomy) {
                    $is_sticky = get_term_meta($taxonomy->term_id, "is_folder_sticky", true);
                    $is_high = get_term_meta($taxonomy->term_id, "is_highlighted", true);
                    $is_locked = get_term_meta($taxonomy->term_id, "is_locked", true);
                    $folder_settings[] = array(
                        'folder_id' => $taxonomy->term_id,
                        'is_sticky' => intval($is_sticky),
                        'is_locked' => intval($is_locked),
                        'is_high' => intval($is_high),
                        'nonce' => wp_create_nonce('wcp_folder_term_'.$taxonomy->term_id),
                        'is_deleted' => 0,
                        'slug' => $taxonomy->slug,
                        'folder_count' => intval($taxonomy->trash_count)
                    );
                }

                $hasStars = self::check_for_setting("has_stars", "general");
                $hasChild = self::check_for_setting("has_child", "general");
                $hasChild = empty($hasChild)?0:1;
                $hasStars = empty($hasStars)?0:1;

                $hasValidKey = $this->check_has_valid_key();
                $hasValidKey = ($hasValidKey)?1:0;

                $dynamic_folders = new Premio_Pro_Folders_Dynamic_Folders();
                $all_folders = array();
                $all_folders[] = array(
                    'name' => esc_html__("All Folders", "folders"),
                    'value' => "dynamic-folders"
                );
                $authors = $dynamic_folders->get_author_dynamic_folders("attachment", "media_folder", "a");
                $dates = $dynamic_folders->get_date_dynamic_folders("attachment", "media_folder", "a");
                $extensions = $dynamic_folders->get_file_ext_dynamic_folders("attachment", "media_folder", "a");
                $file_extensions = array();
                $file_extensions[] = array(
                    'name' => esc_html__("All Extensions", "folders"),
                    'value' => 'extensions-all',
                );
                foreach ($extensions as $key=>$value) {
                    $file_extensions[] = array(
                        'value' => "extensions-".$key,
                        'name' => "- ".$key
                    );
                }

                $all_dynamic_folders = array_merge($all_folders, $authors, $dates, $file_extensions);

                $customize_folders = get_option('customize_folders');
                $use_folder_undo = !isset($customize_folders['use_folder_undo'])?"yes":$customize_folders['use_folder_undo'];
                $defaultTimeout = !isset($customize_folders['default_timeout'])?5:intval($customize_folders['default_timeout']);
                if(empty($defaultTimeout) || !is_numeric($defaultTimeout) || $defaultTimeout < 0) {
                    $defaultTimeout = 5;
                }
                $defaultTimeout = $defaultTimeout*1000;

                $default_folders = get_option("default_folders");
                $default_folder = "";
                if(isset($default_folders["attachment"])) {
                    $default_folder = $default_folders["attachment"];
                }
                $use_shortcuts = !isset($customize_folders['use_shortcuts'])?"yes":$customize_folders['use_shortcuts'];

                wp_dequeue_script("jquery-jstree");  // CMS Tree Page View Conflict
                wp_enqueue_script('wcp-folders-mcustomscrollbar', plugin_dir_url(dirname(__FILE__)) . 'assets/js/jquery.mcustomscrollbar.min.js', array(), WCP_PRO_FOLDER_VERSION);
                wp_enqueue_style('wcp-folders-mcustomscrollbar', plugin_dir_url(dirname(__FILE__)) . 'assets/css/jquery.mcustomscrollbar.min.css', array(), WCP_PRO_FOLDER_VERSION);
                wp_enqueue_script('folders-tree', WCP_PRO_FOLDER_URL . 'assets/js/jstree.min.js', array(), WCP_PRO_FOLDER_VERSION, true);
                wp_enqueue_script('wcp-jquery-touch', plugin_dir_url(dirname(__FILE__)) . 'assets/js/jquery.ui.touch-punch.min.js', array('jquery'), WCP_PRO_FOLDER_VERSION);
                wp_enqueue_script('folders-media', WCP_PRO_FOLDER_URL . 'assets/js/page-post-media.min.js', array('media-editor', 'media-views', 'jquery', 'jquery-ui-resizable', 'jquery-ui-draggable', 'jquery-ui-droppable', 'jquery-ui-sortable', 'backbone'), WCP_PRO_FOLDER_VERSION, true);
                wp_localize_script('folders-media', 'folders_media_options', array(
                    'terms' => $taxonomies,
                    'taxonomy' => get_taxonomy('media_folder'),
                    'ajax_url' => admin_url("admin-ajax.php"),
                    'media_page_url' => admin_url("upload.php?media_folder="),
                    'activate_url' => $this->getRegisterKeyURL(),
                    'nonce' => wp_create_nonce('wcp_folder_nonce_attachment'),
                    'is_key_active' => $is_active,
                    'folders' => $folders,
                    'upgrade_url' => admin_url('admin.php?page=wcp_folders_register'),
                    'post_type' => 'attachment',
                    'page_url' => $admin_url,
                    'current_url' => "",
                    'ajax_image' => plugin_dir_url(dirname(__FILE__)) . "assets/images/ajax-loader.gif",
                    'register_url' => "",
                    'isRTL' => $is_rtl,
                    'can_manage_folder' => $can_manage_folder,
                    'folder_width' => $width,
                    'taxonomy_status' => $taxonomy_status,
                    'selected_taxonomy' => $selected_taxonomy,
                    'show_in_page' => $show_in_page,
                    'svg_file' => WCP_PRO_FOLDER_URL . 'assets/images/pin.png',
                    'folder_settings' => $folder_settings,
                    'hasStars' => $hasStars,
                    'hasChildren' => $hasChild,
                    'hasValidLicense' => $hasValidKey,
                    'dynamic_folders' => $all_dynamic_folders,
                    'useFolderUndo' => $use_folder_undo,
                    'defaultTimeout' => $defaultTimeout,
                    'default_folder' => $default_folder,
                    'use_shortcuts' => $use_shortcuts
                ));
                /* Free/Pro URL Change */
                wp_enqueue_style('folders-tree', WCP_PRO_FOLDER_URL . 'assets/css/jstree.min.css', array(), WCP_PRO_FOLDER_VERSION);
                wp_enqueue_style('folders-folders', WCP_PRO_FOLDER_URL . 'assets/css/folders.min.css', array(), WCP_PRO_FOLDER_VERSION);
                wp_enqueue_style('folders-media', WCP_PRO_FOLDER_URL . 'assets/css/page-post-media.min.css', array(), WCP_PRO_FOLDER_VERSION);
                wp_enqueue_style('folder-icon', WCP_PRO_FOLDER_URL . 'assets/css/folder-icon.css', array(), WCP_PRO_FOLDER_VERSION);
                $width = 275;
                $width = $width - 40;
                $string = "";
                $css_text = "";
                $customize_folders = get_option('customize_folders');
                if (!isset($customize_folders['new_folder_color']) || empty($customize_folders['new_folder_color'])) {
                    $customize_folders['new_folder_color'] = "#FA166B";
                }
                $css_text .= ".media-frame a.add-new-folder { background-color: " . esc_attr($customize_folders['new_folder_color']) . "; border-color: " . esc_attr($customize_folders['new_folder_color']) . "}";
                $css_text .= ".wcp-hide-show-buttons .toggle-buttons { background-color: " . esc_attr($customize_folders['new_folder_color']) . "; }";
                $css_text .= ".folders-toggle-button span { background-color: " . esc_attr($customize_folders['new_folder_color']) . "; }";
                $css_text .= ".ui-resizable-handle.ui-resizable-e:before, .ui-resizable-handle.ui-resizable-w:before {border-color: " . esc_attr($customize_folders['new_folder_color']) . " !important}";

                if (!isset($customize_folders['folder_bg_color']) || empty($customize_folders['folder_bg_color'])) {
                    $customize_folders['folder_bg_color'] = "#FA166B";
                }
                $rgbColor = self::hexToRgb($customize_folders['folder_bg_color']);
                $css_text .= "body:not(.no-hover-css) #custom-scroll-menu .jstree-hovered:not(.jstree-clicked), body:not(.no-hover-css) #custom-scroll-menu .jstree-hovered:not(.jstree-clicked):hover, .dynamic-menu a:hover, .folder-setting-menu li a:hover { background: rgba(".$rgbColor['r'].",".$rgbColor['g'].",".$rgbColor['b'].", 0.08) !important; color: #333333;}";
                $css_text .= "body:not(.no-hover-css) #custom-scroll-menu .jstree-clicked, body:not(.no-hover-css) #custom-scroll-menu .jstree-clicked:not(.jstree-clicked):focus, #custom-scroll-menu .jstree-clicked, #custom-scroll-menu .jstree-clicked:hover { background: ".$customize_folders['folder_bg_color']." !important; color: #ffffff !important; }";
                $css_text .= "#custom-scroll-menu .jstree-hovered.wcp-drop-hover, #custom-scroll-menu .jstree-hovered.wcp-drop-hover:hover, #custom-scroll-menu .jstree-clicked.wcp-drop-hover, #custom-scroll-menu .jstree-clicked.wcp-drop-hover:hover, body #custom-scroll-menu  *.drag-in > a:hover { background: ".$customize_folders['folder_bg_color']." !important; color: #ffffff !important; }";
                $css_text .= ".drag-bot > a { border-bottom: solid 2px ".$customize_folders['folder_bg_color']."}";
                $css_text .= ".drag-up > a { border-top: solid 2px ".$customize_folders['folder_bg_color']."}";
                $css_text .= "body:not(.no-hover-css) #custom-scroll-menu *.drag-in > a.jstree-hovered, body:not(.no-hover-css) #custom-scroll-menu *.drag-in > a.jstree-hovered:hover {background: ".$customize_folders['folder_bg_color']." !important; color: #fff !important;}";
                $css_text .= ".orange-bg > span, .jstree-clicked, .header-posts a.active-item, .un-categorised-items.active-item, .sticky-folders ul li a.active-item { background-color: " . esc_attr($customize_folders['folder_bg_color']) . " !important; color: #ffffff !important; }";
                $css_text .= "body:not(.no-hover-css) .wcp-container .route .title:hover, body:not(.no-hover-css) .header-posts a:hover, body:not(.no-hover-css) .un-categorised-items:hover, body:not(.no-hover-css) .sticky-folders ul li a:hover { background: rgba(".esc_attr($rgbColor['r'].",".$rgbColor['g'].",".$rgbColor['b'].", 0.08")."); color: #333333;}";
                //$css_text .= "body:not(.no-hover-css) .wcp-container .route .title:hover, .header-posts a:hover, .un-categorised-items.active-item, .un-categorised-items:hover, .sticky-folders ul li a:hover {background: rgba(" . esc_attr($rgbColor['r'] . "," . $rgbColor['g'] . "," . $rgbColor['b'] . ", 0.08") . "); color:#444444;}";
                $css_text .= ".wcp-drop-hover {background-color: " . esc_attr($customize_folders['folder_bg_color']) . " !important; color: #ffffff; }";
                $css_text .= "#custom-menu .route .nav-icon .wcp-icon {color: " . esc_attr($customize_folders['folder_bg_color']) . " !important;}";
                $css_text .= ".mCS-3d.mCSB_scrollTools .mCSB_dragger .mCSB_dragger_bar {background-color: " . esc_attr($customize_folders['folder_bg_color']) . " !important;}";
                $css_text .= "body:not(.no-hover-css) .jstree-hovered {background: rgba(" . esc_attr($rgbColor['r'] . "," . $rgbColor['g'] . "," . $rgbColor['b'] . ", 0.08)") . "}";
                $css_text .= ".jstree-default .jstree-clicked {background:" . esc_attr($customize_folders['folder_bg_color']) . "}";
                $css_text .= ".folders-action-menu > ul > li > a:not(.disabled):hover, .folders-action-menu > ul > li > label:not(.disabled):hover {color:" . esc_attr($customize_folders['folder_bg_color']) . "}";
                $css_text .= ".jstree-closed > i.jstree-icon, .jstree-open > i.jstree-icon {color:" . esc_attr($customize_folders['folder_bg_color']) . "}";
                if (!isset($customize_folders['bulk_organize_button_color']) || empty($customize_folders['bulk_organize_button_color'])) {
                    $customize_folders['bulk_organize_button_color'] = "#FA166B";
                }
                $css_text .= "button.button.organize-button { background-color: " . esc_attr($customize_folders['bulk_organize_button_color']) . "; border-color: " . esc_attr($customize_folders['bulk_organize_button_color']) . "; }";
                $css_text .= "button.button.organize-button:hover { background-color: " . esc_attr($customize_folders['bulk_organize_button_color']) . "; border-color: " . esc_attr($customize_folders['bulk_organize_button_color']) . "; }";
                $css_text .= "#custom-scroll-menu .jstree-hovered:not(.jstree-clicked) .pfolder-folder-close, .dynamic-menu a:hover span, .dynamic-menu a:hover i {color: ". esc_attr($customize_folders['folder_bg_color']) ."}";
                $font_family = "";
                if (isset($customize_folders['folder_font']) && !empty($customize_folders['folder_font'])) {
                    $folder_fonts = self::get_font_list();
                    $font_family = $customize_folders['folder_font'];
                    if (isset($folder_fonts[$font_family])) {
                        if($font_family == "System Stack") {
                            $font_family = "-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Oxygen-Sans,Ubuntu,Cantarell,Helvetica Neue,sans-serif";
                        }
                        $css_text .= ".wcp-container, .folder-popup-form, .dynamic-menu { font-family: " . esc_attr($font_family) . " !important; }";
                        if($font_family == "-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Oxygen-Sans,Ubuntu,Cantarell,Helvetica Neue,sans-serif") {
                            $font_family = "System Stack";
                        }
                    }
                    if ($folder_fonts[$font_family] == "Default") {
                        $font_family = "";
                    }
                }
                if (isset($customize_folders['folder_size']) && !empty($customize_folders['folder_size'])) {
                    if($customize_folders['folder_size'] == "custom") {
                        $customize_folders['folder_size'] = ! isset( $customize_folders['folder_custom_font_size'] ) || empty( $customize_folders['folder_custom_font_size'] ) ? "16" : $customize_folders['folder_custom_font_size'];
                    }
                    $css_text .= ".wcp-container .route span.title-text, .header-posts a, .un-categorised-items a, .sticky-title, .sticky-folders > ul > li > a, .jstree-default .jstree-anchor { font-size: " . esc_attr($customize_folders['folder_size']) . "px; }";
                }
                if (!empty($font_family)) {
                    wp_enqueue_style('custom-google-fonts', 'https://fonts.googleapis.com/css?family=' . urlencode($font_family), false);
                }
                wp_add_inline_style('folders-media', $css_text);
            }
        }
    }

    public function get_terms_hierarchical( $taxonomy ) {
//        $terms = get_terms( array(
//            'taxonomy'      => $taxonomy,
//            'hide_empty'    => false,
//            'parent'        => 0,
//            'orderby' => 'meta_value_num',
//            'order' => 'ASC',
//            'update_count_callback' => '_update_generic_term_count',
//            'meta_query' => [[
//                'key' => 'wcp_custom_order',
//                'type' => 'NUMERIC',
//            ]]
//        ) );
//
//        if ( empty( $terms ) ) {
//            return false;
//        }
//
//        $hierarchy = _get_term_hierarchy( $taxonomy );
//
//        $hierarchical_terms = array();
//        if(!empty($terms)) {
//            foreach ($terms as $term) {
//                if(isset($term->term_id)) {
//                    $hierarchical_terms[] = $term;
//                    $hierarchical_terms = self::add_child_terms_recursive($taxonomy, $hierarchical_terms, $hierarchy, $term->term_id, 1);
//                }
//            }
//        }
//
//        return $hierarchical_terms;

        $folder_by_user = 0;
        if(self::$foldersByUser) {
            $user_id = get_current_user_id();
            $folder_by_user = $user_id;
            if(function_exists("wp_get_current_user")) {
                $user = wp_get_current_user();
                $user_roles = (array)$user->roles;
                $user_roles = !is_array($user_roles) ? array() : $user_roles;
                if (in_array("administrator", $user_roles)) {
                    $folder_by_user = 0;
                }
            }
        }

        $args = array(
            'taxonomy'      => $taxonomy,
            'hide_empty' => false,
            'parent'   => 0,
            'orderby' => 'meta_value_num',
            'order' => 'ASC',
            'hierarchical' => false,
            'update_count_callback' => '_update_generic_term_count'
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
        $terms = get_terms($args);
        $hierarchical_terms = array();
        if(!empty($terms)) {
            foreach ($terms as $term) {
                if(!empty($term) && isset($term->term_id)) {
                    $term->term_name = $term->name;
                    $hierarchical_terms[] = $term;
                    $hierarchical_terms = self::get_child_terms($taxonomy, $hierarchical_terms, $term->term_id, "-", $folder_by_user);
                }
            }
        }
        return $hierarchical_terms;
    }

    public static function get_child_terms($taxonomy, $hierarchical_terms, $term_id, $separator = "-", $folder_by_user = 0) {
        $args = array(
            'taxonomy'      => $taxonomy,
            'hide_empty'    => false,
            'parent'        => $term_id,
            'orderby' => 'meta_value_num',
            'order' => 'ASC',
            'hierarchical' => false,
            'update_count_callback' => '_update_generic_term_count'
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
        $terms = get_terms($args);
        if(!empty($terms)) {
            foreach ($terms as $term) {
                if(isset($term->name)) {
                    $term->name = $separator . " " . $term->name;
                    $term->term_name = trim($term->name, "-");
                    $hierarchical_terms[] = $term;
                    $hierarchical_terms = self::get_child_terms($taxonomy, $hierarchical_terms, $term->term_id, $separator . "-");
                }
            }
        }

        return $hierarchical_terms;
    }

    private function add_child_terms_recursive( $taxonomy, $hierarchical_terms, $hierarchy, $current_term_id, $current_depth ) {

        if ( ! isset( $hierarchy[ $current_term_id ] ) ) {
            return $hierarchical_terms;
        }

        foreach ( $hierarchy[ $current_term_id ] as $child_term_id ) {

            $child_term = get_term( $child_term_id, $taxonomy );

            $child_term->name = str_pad( '', $current_depth, '-', STR_PAD_LEFT ) . ' ' . $child_term->name;

            $hierarchical_terms[] = $child_term;

            $hierarchical_terms = self::add_child_terms_recursive( $taxonomy, $hierarchical_terms, $hierarchy, $child_term_id, ( $current_depth + 1 ) );
        }

        return $hierarchical_terms;
    }

    public function filter_attachments_list( $query ) {




        if ( ! isset( $query->query['post_type'] ) ) {
            return $query;
        }

        if ( is_array( $query->query['post_type'] ) && ! in_array( 'attachment', $query->query['post_type'] ) ) {
            return $query;
        }
        if ( ! is_array( $query->query['post_type'] ) && strpos( $query->query['post_type'], 'attachment' ) === false ) {
            return $query;
        }

        if ( ! isset($_REQUEST['media_folder']) && !isset($_REQUEST['query']['dynamic_media_folder'])) {
            return $query;
        }


        if(isset($_REQUEST['query']['dynamic_media_folder']) && !empty($_REQUEST['query']['dynamic_media_folder']) && $_REQUEST['query']['dynamic_media_folder'] != 'dynamic-folders') {
            $dynamic_folder = $_REQUEST['query']['dynamic_media_folder'];
            if(!empty($dynamic_folder)) {
                $dynamic_folder = str_replace("_anchor", "", $dynamic_folder);
                $dynamic_folder_array = explode("-", $dynamic_folder);
                $filter_type = isset($dynamic_folder_array[0])?$dynamic_folder_array[0]:"";
                $filter_value = isset($dynamic_folder_array[1])?$dynamic_folder_array[1]:"";
                if(!empty($filter_type) && !empty($filter_value)) {
                    if ($filter_type == "author") {
                        if($filter_value != "all") {
                            $query->query_vars['author'] = $filter_value;
                        }
                    }
                    else if ($filter_type == "dates") {
                        if($filter_value != "all") {

                        }
                    }
                    else if ($filter_type == "year") {
                        $query->query_vars['year'] = $filter_value;
                    }
                    else if ($filter_type == "month") {
                        $query->query_vars['year'] = $filter_value;
                        if(isset($dynamic_folder_array[2])) {
                            $query->query_vars['monthnum'] = $dynamic_folder_array[2];
                        }
                    }
                    else if ($filter_type == "day") {
                        $query->query_vars['year'] = $filter_value;
                        if(isset($dynamic_folder_array[2])) {
                            $query->query_vars['monthnum'] = $dynamic_folder_array[2];
                        }
                        if(isset($dynamic_folder_array[3])) {
                            $query->query_vars['day'] = $dynamic_folder_array[3];
                        }
                    }
                    else if($filter_type == "pages_parent") {
                        if($filter_value == "all") {
                            $results = $this->get_root_hierarchy_array();
                            if(!empty($results)) {
                                $post_ids = array();
                                foreach ($results as $row) {
                                    $post_ids[] = $row->ID;
                                }
                                $query->query_vars['post__in'] = $post_ids;
                            } else {
                                $query->query_vars['post__in'] = array(0);
                            }
                        } else {
                            $results = $this->get_child_pages_array($filter_value);
                            if(!empty($results)) {
                                $post_ids = array();
                                foreach ($results as $row) {
                                    $post_ids[] = $row->ID;
                                }
                                $query->query_vars['post__in'] = $post_ids;
                            } else {
                                $query->query_vars['post__in'] = array(0);
                            }
                        }
                    }
                    else if($filter_type == "post_category") {
                        $category__in = array();
                        $category__in[] = $filter_value;
                        $query->query_vars['category__in'] = $category__in;
                    }
                    else if($filter_type == "extensions") {
                        $dynamic_folders = new Premio_Pro_Folders_Dynamic_Folders();
                        $extension = $dynamic_folders->get_file_ext_dynamic_folders("attachment", "", "a");
                        if(isset($extension[$filter_value])) {
                            $query->query_vars['post_mime_type'] = $extension[$filter_value];
                        }
                    }
                }
            }
        } else {
            $term = sanitize_text_field(wp_unslash($_REQUEST['media_folder']));
            if ($term != "-1") {
                return $query;
            }

            unset($query->query_vars['media_folder']);

            $tax_query = array(
                'taxonomy' => 'media_folder',
                'operator' => 'NOT EXISTS',
            );

            $query->set('tax_query', array($tax_query));
            $query->tax_query = new WP_Tax_Query(array($tax_query));

            $query = apply_filters('media_library_organizer_media_filter_attachments', $query, $_REQUEST);
        }

        return $query;

    }

    public function output_list_table_filters( $post_type, $view_name )
    {
        if ($post_type != 'attachment') {
            return;
        }

        if ($view_name != 'bar') {
            return;
        }

        $current_term = false;
        if ( isset( $_REQUEST['media_folder'] ) ) {
            $current_term = sanitize_text_field($_REQUEST['media_folder']);
        }

        wp_dropdown_categories( array(
            'show_option_all'   =>  esc_html__( 'All Folders', 'folders'),
            'show_option_none'   =>  esc_html__( '(Unassigned)', 'folders'),
            'option_none_value' => -1,
            'orderby'           => 'meta_value_num',
            'order'             => 'ASC',
            'show_count'        => true,
            'hide_empty'        => false,
            'update_count_callback' => '_update_generic_term_count',
            'echo'              => true,
            'selected'          => $current_term,
            'hierarchical'      => true,
            'name'              => 'media_folder',
            'id'                => '',
            'class'             => '',
            'taxonomy'          => 'media_folder',
            'value_field'       => 'slug',
            'meta_query' => [[
                'key' => 'wcp_custom_order',
                'type' => 'NUMERIC',
            ]]
        ) );

    }


    function new_to_auto_draft($post) {

        $post_type = $post->post_type;

        if(self::is_for_this_post_type($post_type) && !isset($_REQUEST["folder_for_media"])) {

            $post_type = self::get_custom_post_type($post_type);
            $selected_folder = get_option("selected_{$post_type}_folder");

            if($selected_folder != null && !empty($selected_folder)) {
                $terms = get_term($selected_folder);
                if(!empty($terms) && isset($terms->slug)) {
                    wp_set_object_terms($post->ID, $terms->slug, $post_type );
                }
            }
        }
    }

    public function wcp_folder_send_message_to_owner() {
        if (current_user_can('manage_options')) {
            $response = array();
            $response['status'] = 0;
            $response['error'] = 0;
            $response['errors'] = array();
            $response['message'] = "";
            $errorArray = [];
            $errorMessage =  esc_html__("%s is required", 'folders');
            $postData = filter_input_array(INPUT_POST);
            if (!isset($postData['textarea_text']) || trim($postData['textarea_text']) == "") {
                $error = array(
                    "key" => "textarea_text",
                    "message" =>  esc_html__("Please enter your message", 'folders')
                );
                $errorArray[] = $error;
            }
            if (!isset($postData['user_email']) || trim($postData['user_email']) == "") {
                $error = array(
                    "key" => "user_email",
                    "message" => sprintf($errorMessage, esc_html__("Email", 'folders'))
                );
                $errorArray[] = $error;
            } else if (!filter_var($postData['user_email'], FILTER_VALIDATE_EMAIL)) {
                $error = array(
                    'key' => "user_email",
                    "message" => "Email is not valid"
                );
                $errorArray[] = $error;
            }
            if (empty($errorArray)) {
                if (!isset($postData['folder_help_nonce']) || trim($postData['folder_help_nonce']) == "") {
                    $error = array(
                        "key" => "nonce",
                        "message" =>  esc_html__("Your request is not valid", 'folders')
                    );
                    $errorArray[] = $error;
                } else {
                    if (!wp_verify_nonce($postData['folder_help_nonce'], 'wcp_folder_help_nonce')) {
                        $error = array(
                            "key" => "nonce",
                            "message" =>  esc_html__("Your request is not valid", 'folders')
                        );
                        $errorArray[] = $error;
                    }
                }
            }
            if (empty($errorArray)) {
                global $current_user;
                $text_message = self::sanitize_options($postData['textarea_text']);
                $email = self::sanitize_options($postData['user_email'], "email");
                $domain = site_url();
                $user_name = $current_user->first_name . " " . $current_user->last_name;

                $response['status'] = 1;

                /* sending message to Crisp */
                $post_message = array();

                $message_data = array();
                $message_data['key'] = "Plugin";
                $message_data['value'] = "Folders (Pro)";
                $post_message[] = $message_data;

                $message_data = array();
                $message_data['key'] = "Domain";
                $message_data['value'] = $domain;
                $post_message[] = $message_data;

                $message_data = array();
                $message_data['key'] = "Email";
                $message_data['value'] = $email;
                $post_message[] = $message_data;

                $message_data = array();
                $message_data['key'] = "Message";
                $message_data['value'] = $text_message;
                $post_message[] = $message_data;

                $api_params = array(
                    'domain' => $domain,
                    'email' => $email,
                    'url' => site_url(),
                    'name' => $user_name,
                    'message' => $post_message,
                    'plugin' => "Folders (Pro)",
                    'type' => "Need Help",
                );

                /* Request to premio.io for key activation */
                $crisp_response = wp_safe_remote_post("https://go.premio.io/crisp/crisp-send-message.php", array('body' => $api_params, 'timeout' => 15, 'sslverify' => true));

                if (is_wp_error($crisp_response)) {
                    wp_safe_remote_post("https://go.premio.io/crisp/crisp-send-message.php", array('body' => $api_params, 'timeout' => 15, 'sslverify' => false));
                }
            } else {
                $response['error'] = 1;
                $response['errors'] = $errorArray;
            }
            echo json_encode($response);
        }
    }

    public function folder_plugin_deactivate() {
        if (current_user_can('manage_options')) {
            global $current_user;
            $postData = filter_input_array(INPUT_POST);
            $errorCounter = 0;
            $response = array();
            $response['status'] = 0;
            $response['message'] = "";
            $response['valid'] = 1;
            if(!isset($postData['reason']) || empty($postData['reason'])) {
                $errorCounter++;
                $response['message'] = "Please provide reason";
            } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
                $response['message'] =  esc_html__("Your request is not valid", 'folders');
                $errorCounter++;
                $response['valid'] = 0;
            } else {
                $nonce = self::sanitize_options($postData['nonce']);
                if(!wp_verify_nonce($nonce, 'wcp_folder_deactivate_nonce')) {
                    $response['message'] =  esc_html__("Your request is not valid", 'folders');
                    $errorCounter++;
                    $response['valid'] = 0;
                }
            }
            if($errorCounter == 0) {
                $reason = $postData['reason'];
                $email = "none@none.none";
                if (isset($postData['email_id']) && !empty($postData['email_id']) && filter_var($postData['email_id'], FILTER_VALIDATE_EMAIL)) {
                    $email = $postData['email_id'];
                }
                $domain = site_url();
                $user_name = $current_user->first_name." ".$current_user->last_name;
                $subject = "Folders was removed from {$domain}";
                $headers = "MIME-Version: 1.0\r\n";
                $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
                $headers .= 'From: '.$user_name.' <'.$email.'>'.PHP_EOL ;
                $headers .= 'Reply-To: '.$user_name.' <'.$email.'>'.PHP_EOL ;
                $headers .= 'X-Mailer: PHP/' . phpversion();
                ob_start();
                ?>
                <table border="0" cellspacing="0" cellpadding="5">
                    <tr>
                        <th>Plugin</th>
                        <td>Folders Pro</td>
                    </tr>
                    <tr>
                        <th>Plugin Version</th>
                        <!-- Free Pro Version Change -->
                        <td><?php echo esc_attr(WCP_PRO_FOLDER_VERSION) ?></td>
                    </tr>
                    <tr>
                        <th>Domain</th>
                        <td><?php echo esc_attr($domain) ?></td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td><?php echo esc_attr($email) ?></td>
                    </tr>
                    <tr>
                        <th>Comment</th>
                        <td><?php echo esc_attr(nl2br($reason)) ?></td>
                    </tr>
                    <tr>
                        <th>WordPress Version</th>
                        <td><?php echo esc_attr(get_bloginfo('version')) ?></td>
                    </tr>
                    <tr>
                        <th>PHP Version</th>
                        <td><?php echo esc_attr(PHP_VERSION) ?></td>
                    </tr>
                </table>
                <?php
                $content = ob_get_clean();
                $email_id = "gal@premio.io, karina@premio.io";
                wp_mail($email_id, $subject, $content, $headers);
                $response['status'] = 1;

                /* sending message to Crisp */
                $post_message = array();

                $message_data = array();
                $message_data['key'] = "Plugin";
                $message_data['value'] = "Folders (Pro)";
                $post_message[] = $message_data;

                $message_data = array();
                $message_data['key'] = "Plugin Version";
                $message_data['value'] = WCP_PRO_FOLDER_VERSION;
                $post_message[] = $message_data;

                $message_data = array();
                $message_data['key'] = "Domain";
                $message_data['value'] = $domain;
                $post_message[] = $message_data;

                $message_data = array();
                $message_data['key'] = "Email";
                $message_data['value'] = $email;
                $post_message[] = $message_data;

                $message_data = array();
                $message_data['key'] = "WordPress Version";
                $message_data['value'] = esc_attr(get_bloginfo('version'));
                $post_message[] = $message_data;

                $message_data = array();
                $message_data['key'] = "PHP Version";
                $message_data['value'] = PHP_VERSION;
                $post_message[] = $message_data;

                $message_data = array();
                $message_data['key'] = "Message";
                $message_data['value'] = $reason;
                $post_message[] = $message_data;

                $api_params = array(
                    'edd_action' => 'activate_license',
                    'domain' => $domain,
                    'email' => $email,
                    'url' => site_url(),
                    'name' => $user_name,
                    'message' => $post_message,
                    'plugin' => "Folders (Pro)",
                    'type' => "Uninstall",
                );

                /* Sending message to Crisp API */
                $crisp_response = wp_safe_remote_post("https://go.premio.io/crisp/crisp-send-message.php", array('body' => $api_params, 'timeout' => 15, 'sslverify' => true));

                if (is_wp_error($crisp_response)) {
                    wp_safe_remote_post("https://go.premio.io/crisp/crisp-send-message.php", array('body' => $api_params, 'timeout' => 15, 'sslverify' => false));
                }
            }
            echo json_encode($response);
            wp_die();
        }
    }

    public static function ttl_fldrs() {
        $post_types = get_option('folders_settings');
        $post_types = is_array($post_types) ? $post_types : array();
        $total = 0;
        foreach ($post_types as $post_type) {
            $post_type = self::get_custom_post_type($post_type);
            $total += wp_count_terms($post_type);
        }
        return $total;
    }

    public function wcp_remove_post_item()
    {
        $response = array();
        $response['status'] = 0;
        $response['error'] = 0;
        $response['data'] = array();
        $response['message'] = "";
        $postData = filter_input_array(INPUT_POST);
        if (isset($postData['post_id']) && !empty($postData['post_id'])) {
            wp_delete_post($postData['post_id']);
            $response['status'] = 1;
        }
        echo json_encode($response);
        wp_die();
    }

    public function wcp_change_all_status()
    {
        $response = array();
        $response['status'] = 0;
        $response['error'] = 0;
        $response['data'] = array();
        $response['message'] = "";
        $postData = filter_input_array(INPUT_POST);
        $errorCounter = 0;
        if (!isset($postData['type']) || empty($postData['type'])) {
            $response['message'] =  esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] =  esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else  if (!current_user_can("manage_categories") || ($postData['type'] == "page" && !current_user_can("edit_pages"))) {
            $response['message'] =  esc_html__("You have not permission to update width", 'folders');
            $errorCounter++;
        } else if (!current_user_can("manage_categories") || ($postData['type'] != "page" && !current_user_can("edit_posts"))) {
            $response['message'] =  esc_html__("You have not permission to update width", 'folders');
            $errorCounter++;
        } else {
            $type = self::sanitize_options($postData['type']);
            $nonce = self::sanitize_options($postData['nonce']);
            if(!wp_verify_nonce($nonce, 'wcp_folder_nonce_'.$type)) {
                $response['message'] =  esc_html__("Your request is not valid", 'folders');
                $errorCounter++;
            }
        }
        if ($errorCounter == 0) {
            if (isset($postData['folders']) || !empty($postData['folders'])) {
                $status = isset($postData['status']) ? $postData['status'] : 0;
                $status = self::sanitize_options($status);
                $folders = self::sanitize_options($postData['folders']);
                $folders = trim($folders, ",");
                $folders = explode(",", $folders);
                foreach ($folders as $folder) {
                    update_term_meta($folder, "is_active", $status);
                }
            }
            $response['status'] = 1;
        }
        echo json_encode($response);
        wp_die();
    }

    public function wcp_change_post_width()
    {
        $response = array();
        $response['status'] = 0;
        $response['error'] = 0;
        $response['data'] = array();
        $response['message'] = "";
        $postData = filter_input_array(INPUT_POST);
        $errorCounter = 0;
        if (!isset($postData['width']) || empty($postData['width'])) {
            $response['message'] =  esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if (!isset($postData['type']) || empty($postData['type'])) {
            $response['message'] =  esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] =  esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if ($postData['type'] == "page" && !current_user_can("edit_pages")) {
            $response['message'] =  esc_html__("You have not permission to update width", 'folders');
            $errorCounter++;
        } else if ($postData['type'] != "page" && !current_user_can("edit_posts")) {
            $response['message'] =  esc_html__("You have not permission to update width", 'folders');
            $errorCounter++;
        } else {
            $type = self::sanitize_options($postData['type']);
            $nonce = self::sanitize_options($postData['nonce']);
            if(!wp_verify_nonce($nonce, 'wcp_folder_nonce_'.$type)) {
                $response['message'] =  esc_html__("Your request is not valid", 'folders');
                $errorCounter++;
            }
        }
        if ($errorCounter == 0) {
            $type = self::sanitize_options($postData['type']);
            $width = self::sanitize_options($postData['width'], "int");
            $optionName = "wcp_dynamic_width_for_" . $type;
            update_option($optionName, $width);
            $response['status'] = 1;
        }
        echo json_encode($response);
        wp_die();
    }

    public function wcp_change_multiple_post_folder()
    {
        $response = array();
        $response['status'] = 0;
        $response['error'] = 0;
        $response['data'] = array();
        $response['message'] = "";
        $postData = filter_input_array(INPUT_POST);
        $errorCounter = 0;
        if (!isset($postData['post_ids']) || empty($postData['post_ids'])) {
            $response['message'] =  esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if (!isset($postData['folder_id']) || empty($postData['folder_id'])) {
            $response['message'] =  esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if (!isset($postData['type']) || empty($postData['type'])) {
            $response['message'] =  esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] =  esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if ($postData['type'] == "page" && !current_user_can("edit_pages")) {
            $response['message'] =  esc_html__("You have not permission to update folder", 'folders');
            $errorCounter++;
        } else if ($postData['type'] != "page" && !current_user_can("edit_posts")) {
            $response['message'] =  esc_html__("You have not permission to update folder", 'folders');
            $errorCounter++;
        } else {
            $folder_id = self::sanitize_options($postData['folder_id']);
            if(!wp_verify_nonce($postData['nonce'], 'wcp_folder_term_'.$folder_id)) {
                $response['message'] =  esc_html__("Your request is not valid", 'folders');
                $errorCounter++;
            }
        }
        if ($errorCounter == 0) {
            $folderUndoSettings = array();
            $postID = self::sanitize_options($postData['post_ids']);
            $postID = trim($postID, ",");
            $folderID = self::sanitize_options($postData['folder_id']);
            $type = self::sanitize_options($postData['type']);
            $postArray = explode(",", $postID);
            $status = 0;
            if(isset($postData['status'])) {
                $status = self::sanitize_options($postData['status']);
            }
            $status = true;

            $taxonomy = "";
            if(isset($postData['taxonomy'])) {
                $taxonomy = self::sanitize_options($postData['taxonomy']);
            }
            if (is_array($postArray)) {
                $post_type = self::get_custom_post_type($type);
                foreach ($postArray as $post) {
                    $terms = get_the_terms($post, $post_type);
                    $post_terms = array(
                        'post_id' => $post,
                        'terms' => $terms
                    );
                    $folderUndoSettings[] = $post_terms;
                    if (!empty($terms)) {
                        foreach ($terms as $term) {
                            if(!empty($taxonomy) && ($term->term_id == $taxonomy || $term->slug == $taxonomy)) {
                                wp_remove_object_terms($post, $term->term_id, $post_type);
                            }
                        }
                    }
                    wp_set_post_terms($post, $folderID, $post_type, $status);
                }
            }
            $response['status'] = 1;
            delete_transient("folder_undo_settings");
            delete_transient("premio_folders_without_trash");
            set_transient("folder_undo_settings", $folderUndoSettings, DAY_IN_SECONDS);
        }
        echo json_encode($response);
        wp_die();
    }

    public function wcp_undo_folder_changes() {
        $response = array();
        $response['status'] = 0;
        $response['error'] = 0;
        $response['data'] = array();
        $response['message'] = "";
        $postData = filter_input_array(INPUT_POST);
        $errorCounter = 0;
        if (!isset($postData['post_type']) || empty($postData['post_type'])) {
            $response['message'] =  esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] =  esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else {
            if(!wp_verify_nonce($postData['nonce'], 'wcp_folder_nonce_'.$postData['post_type'])) {
                $response['message'] =  esc_html__("Your request is not valid", 'folders');
                $errorCounter++;
            }
        }
        if ($errorCounter == 0) {
            $response['status'] = 1;
            $folder_undo_settings = get_transient("folder_undo_settings");
            $type = self::sanitize_options($postData['post_type']);
            $post_type = self::get_custom_post_type($type);
            if(!empty($folder_undo_settings) && is_array($folder_undo_settings)) {
                $trash_folders = $initial_trash_folders = get_transient("premio_folders_without_trash");
                if($trash_folders === false) {
                    $trash_folders = array();
                    $initial_trash_folders = array();
                }
                foreach($folder_undo_settings as $item) {
                    $terms = get_the_terms($item['post_id'], $post_type);
                    if (!empty($terms)) {
                        foreach ($terms as $term) {
                            wp_remove_object_terms($item['post_id'], $term->term_id, $post_type);
                            if(isset($trash_folders[$term->term_taxonomy_id])) {
                                unset($trash_folders[$term->term_taxonomy_id]);
                            }
                        }
                    }
                    if(!empty($item['terms']) && is_array($item['terms'])) {
                        foreach($item['terms'] as $term) {
                            wp_set_post_terms($item['post_id'], $term->term_id, $post_type, true);
                            if(isset($trash_folders[$term->term_taxonomy_id])) {
                                unset($trash_folders[$term->term_taxonomy_id]);
                            }
                        }
                    }
                }

                if(!empty($terms) && $initial_trash_folders != $trash_folders) {
                    delete_transient("premio_folders_without_trash");
                    set_transient("premio_folders_without_trash", $trash_folders, 3*DAY_IN_SECONDS);
                }
            }
        }
        echo json_encode($response);
        die;
    }

    public function wcp_change_post_folder()
    {
        $response = array();
        $response['status'] = 0;
        $response['error'] = 0;
        $response['data'] = array();
        $response['message'] = "";
        $postData = filter_input_array(INPUT_POST);
        $errorCounter = 0;
        if (!isset($postData['post_id']) || empty($postData['post_id'])) {
            $errorCounter++;
            $response['message'] =  esc_html__("Your request is not valid", 'folders');
        } else if (!isset($postData['folder_id']) || empty($postData['folder_id'])) {
            $errorCounter++;
            $response['message'] =  esc_html__("Your request is not valid", 'folders');
        } else if (!isset($postData['type']) || empty($postData['type'])) {
            $errorCounter++;
            $response['message'] =  esc_html__("Your request is not valid", 'folders');
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] =  esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if ($postData['type'] == "page" && !current_user_can("edit_pages")) {
            $response['message'] =  esc_html__("You have not permission to update folder", 'folders');
            $errorCounter++;
        } else if ($postData['type'] != "page" && !current_user_can("edit_posts")) {
            $response['message'] =  esc_html__("You have not permission to update folder", 'folders');
            $errorCounter++;
        } else {
            $term_id = self::sanitize_options($postData['folder_id']);
            if(!wp_verify_nonce($postData['nonce'], 'wcp_folder_term_'.$term_id)) {
                $response['message'] =  esc_html__("Your request is not valid", 'folders');
                $errorCounter++;
            }
        }
        if ($errorCounter == 0) {
            $postID = self::sanitize_options($postData['post_id']);
            $folderID = self::sanitize_options($postData['folder_id']);
            $type = self::sanitize_options($postData['type']);
            $folder_post_type = self::get_custom_post_type($type);
            $status = 0;
            if(isset($postData['status'])) {
                $status = self::sanitize_options($postData['status']);
            }
            $status = ($status == 1)?true:false;
            $taxonomy = "";
            if(isset($postData['taxonomy'])) {
                $taxonomy = self::sanitize_options($postData['taxonomy']);
            }
            $terms = get_the_terms($postID, $folder_post_type);
            if (!empty($terms)) {
                foreach ($terms as $term) {
                    if(!empty($taxonomy) && ($term->term_id == $taxonomy || $term->slug == $taxonomy)) {
                        wp_remove_object_terms($postID, $term->term_id, $folder_post_type);
                    }
                }
            }
            wp_set_post_terms($postID, $folderID, $folder_post_type, true);
            $response['status'] = 1;
        }
        echo json_encode($response);
        wp_die();
    }

    public function wcp_mark_un_mark_folder()
    {
        $response = array();
        $response['status'] = 0;
        $response['error'] = 0;
        $response['data'] = array();
        $response['message'] = "";
        $postData = filter_input_array(INPUT_POST);
        $errorCounter = 0;
        if (!current_user_can("manage_categories")) {
            $response['message'] =  esc_html__("You have not permission to update folder", 'folders');
            $errorCounter++;
        } else if (!isset($postData['term_id']) || empty($postData['term_id'])) {
            $errorCounter++;
            $response['message'] =  esc_html__("Your request is not valid", 'folders');
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] =  esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else {
            $term_id = self::sanitize_options($postData['term_id']);
            if(!wp_verify_nonce($postData['nonce'], 'wcp_folder_term_'.$term_id)) {
                $response['message'] =  esc_html__("Your request is not valid", 'folders');
                $errorCounter++;
            }
        }
        if ($errorCounter == 0) {
            $term_id = self::sanitize_options($postData['term_id']);
            $status = get_term_meta($term_id, "is_highlighted", true);
            if ($status == 1) {
                update_term_meta($term_id, "is_highlighted", 0);
                $status = 0;
            } else {
                update_term_meta($term_id, "is_highlighted", 1);
                $status = 1;
            }
            $response['marked'] = $status;
            $response['id'] = $postData['term_id'];
            $response['status'] = 1;
        }
        echo json_encode($response);
        wp_die();
    }

    public function wcp_lock_unlock_folder()
    {
        $response = array();
        $response['status'] = 0;
        $response['error'] = 0;
        $response['data'] = array();
        $response['message'] = "";
        $postData = filter_input_array(INPUT_POST);
        $errorCounter = 0;
        if (!current_user_can("manage_categories")) {
            $response['message'] =  esc_html__("You have not permission to update folder", 'folders');
            $errorCounter++;
        } else if (!isset($postData['term_id']) || empty($postData['term_id'])) {
            $errorCounter++;
            $response['message'] =  esc_html__("Your request is not valid", 'folders');
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] =  esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else {
            $term_id = self::sanitize_options($postData['term_id']);
            if(!wp_verify_nonce($postData['nonce'], 'wcp_folder_term_'.$term_id)) {
                $response['message'] =  esc_html__("Your request is not valid", 'folders');
                $errorCounter++;
            }
        }
        if ($errorCounter == 0) {
            $term_id = self::sanitize_options($postData['term_id']);
            $status = get_term_meta($term_id, "is_locked", true);
            if ($status == 1) {
                update_term_meta($term_id, "is_locked", 0);
                $status = 0;
            } else {
                update_term_meta($term_id, "is_locked", 1);
                $status = 1;
            }
            $response['marked'] = $status;
            $response['is_locked'] = $status;
            $response['id'] = $postData['term_id'];
            $response['status'] = 1;
        }
        echo json_encode($response);
        wp_die();
    }

    public function wcp_make_sticky_folder()
    {
        $response = array();
        $response['status'] = 0;
        $response['error'] = 0;
        $response['data'] = array();
        $response['message'] = "";
        $postData = filter_input_array(INPUT_POST);
        $errorCounter = 0;
        if (!current_user_can("manage_categories")) {
            $response['message'] =  esc_html__("You have not permission to update folder", 'folders');
            $errorCounter++;
        } else if (!isset($postData['term_id']) || empty($postData['term_id'])) {
            $errorCounter++;
            $response['message'] =  esc_html__("Your request is not valid", 'folders');
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] =  esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else {
            $term_id = self::sanitize_options($postData['term_id']);
            if(!wp_verify_nonce($postData['nonce'], 'wcp_folder_term_'.$term_id)) {
                $response['message'] =  esc_html__("Your request is not valid", 'folders');
                $errorCounter++;
            }
        }
        if ($errorCounter == 0) {
            $term_id = self::sanitize_options($postData['term_id']);
            $status = get_term_meta($term_id, "is_folder_sticky", true);
            if ($status == 1) {
                update_term_meta($term_id, "is_folder_sticky", 0);
                $status = 0;
            } else {
                update_term_meta($term_id, "is_folder_sticky", 1);
                $status = 1;
            }
            $response['is_folder_sticky'] = $status;
            $response['id'] = $postData['term_id'];
            $response['status'] = 1;
        }
        echo json_encode($response);
        wp_die();
    }

    public function wcp_save_folder_order()
    {
        $response = array();
        $response['status'] = 0;
        $response['error'] = 0;
        $response['data'] = array();
        $response['message'] = "";
        $postData = filter_input_array(INPUT_POST);
        $errorCounter = 0;
        if (!current_user_can("manage_categories")) {
            $response['message'] =  esc_html__("You have not permission to update folder order", 'folders');
            $errorCounter++;
        } else if (!isset($postData['term_ids']) || empty($postData['term_ids'])) {
            $errorCounter++;
            $response['message'] =  esc_html__("Your request is not valid", 'folders');
        } else if (!isset($postData['type']) || empty($postData['type'])) {
            $errorCounter++;
            $response['message'] =  esc_html__("Your request is not valid", 'folders');
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] =  esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else {
            $type = self::sanitize_options($postData['type']);
            if(!wp_verify_nonce($postData['nonce'], 'wcp_folder_nonce_'.$type)) {
                $response['message'] =  esc_html__("Your request is not valid", 'folders');
                $errorCounter++;
            }
        }
        if ($errorCounter == 0) {
            $termIds = self::sanitize_options(($postData['term_ids']));
            $type = self::sanitize_options($postData['type']);
            $termIds = trim($termIds, ",");
            $termArray = explode(",", $termIds);
            $order = 1;
            foreach ($termArray as $term) {
                if (!empty($term)) {
                    update_term_meta($term, "wcp_custom_order", $order);
                    $order++;
                }
            }
            $term_id = self::sanitize_options($postData['term_id']);
            $parent_id = self::sanitize_options($postData['parent_id']);
            $type = self::sanitize_options($postData['type']);
            $folder_type = self::get_custom_post_type($type);
            wp_update_term($term_id, $folder_type, array(
                'parent' => $parent_id
            ));

            if($parent_id != "#" || !empty($parent_id)) {
                update_term_meta($parent_id, "is_active", 1);
            }

            $response['status'] = 1;
            $folder_type = self::get_custom_post_type($type);
            /* Free/Pro Class name change */
            $response['options'] = WCP_Pro_Tree::get_option_data_for_select($folder_type);
        }
        echo json_encode($response);
        wp_die();
    }

    public function save_wcp_folder_state()
    {
        $response = array();
        $response['status'] = 0;
        $response['error'] = 0;
        $response['data'] = array();
        $response['message'] = "";
        $postData = filter_input_array(INPUT_POST);
        $errorCounter = 0;
        if (!current_user_can("manage_categories")) {
            $response['message'] =  esc_html__("You have not permission to update folder", 'folders');
            $errorCounter++;
        } else if (!isset($postData['term_id']) || empty($postData['term_id'])) {
            $response['message'] =  esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] =  esc_html__("Unable to create folder, Your request is not valid", 'folders');
            $errorCounter++;
        } else {
            $term_id = self::sanitize_options($postData['term_id']);
            if(!wp_verify_nonce($postData['nonce'], 'wcp_folder_term_'.$term_id)) {
                $response['message'] =  esc_html__("Your request is not valid", 'folders');
                $errorCounter++;
            }
        }
        if ($errorCounter == 0) {
            $response['status'] = 1;
            $term_id = self::sanitize_options($postData['term_id']);
            $is_active = isset($postData['is_active'])?$postData['is_active']:0;
            $is_active = self::sanitize_options($is_active);
            if ($is_active == 1) {
                update_term_meta($term_id, "is_active", 1);
            } else {
                update_term_meta($term_id, "is_active", 0);
            }
        }
        echo json_encode($response);
        wp_die();
    }

    public function wcp_update_parent_information()
    {
        $response = array();
        $response['status'] = 0;
        $response['error'] = 0;
        $response['data'] = array();
        $response['message'] = "";
        $postData = filter_input_array(INPUT_POST);
        $errorCounter = 0;
        if (!current_user_can("manage_categories")) {
            $response['message'] =  esc_html__("You have not permission to update folder", 'folders');
            $errorCounter++;
        } else if (!isset($postData['term_id']) || empty($postData['term_id'])) {
            $response['message'] =  esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if (!isset($postData['type']) || empty($postData['type'])) {
            $response['message'] =  esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if (!isset($postData['parent_id'])) {
            $response['message'] =  esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] =  esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else {
            $term_id = self::sanitize_options($postData['term_id']);
            if(!wp_verify_nonce($postData['nonce'], 'wcp_folder_term_'.$term_id)) {
                $response['message'] =  esc_html__("Your request is not valid", 'folders');
                $errorCounter++;
            }
        }
        if($errorCounter == 0) {
            $term_id = self::sanitize_options($postData['term_id']);
            $parent_id = self::sanitize_options($postData['parent_id']);
            $type = self::sanitize_options($postData['type']);
            $folder_type = self::get_custom_post_type($type);
            wp_update_term($term_id, $folder_type, array(
                'parent' => $parent_id
            ));
            update_term_meta($parent_id, "is_active", 1);
            $response['status'] = 1;
        }
        echo json_encode($response);
        wp_die();
    }

    public function wcp_save_parent_data()
    {
        $response = array();
        $response['status'] = 0;
        $response['error'] = 0;
        $response['data'] = array();
        $response['message'] = "";
        $postData = filter_input_array(INPUT_POST);
        $errorCounter = 0;
        if (!isset($postData['type']) || empty($postData['type'])) {
            $response['message'] =  esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] =  esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if (!current_user_can("manage_categories")) {
            $response['message'] =  esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else {
            $type = self::sanitize_options($postData['type']);
            if(!wp_verify_nonce($postData['nonce'], 'wcp_folder_nonce_'.$type)) {
                $response['message'] =  esc_html__("Your request is not valid", 'folders');
                $errorCounter++;
            }
        }
        if ($errorCounter == 0) {
            $type = self::sanitize_options($postData['type']);
            $optionName = $type . "_parent_status";
            $response['status'] = 1;
            $is_active = isset($postData['is_active'])?$postData['is_active']:0;
            $is_active = self::sanitize_options($is_active);
            if ($is_active == 1) {
                update_option($optionName, 1);
            } else {
                update_option($optionName, 0);
            }
        }
        echo json_encode($response);
        wp_die();
    }

    public function remove_muliple_folder(){
        $response = array();
        $response['status'] = 0;
        $response['error'] = 0;
        $response['data'] = array();
        $response['message'] = "";
        $postData = filter_input_array(INPUT_POST);
        $errorCounter = 0;
        $error = "";
        if (!current_user_can("manage_categories")) {
            $error =  esc_html__("You have not permission to remove folder", 'folders');
            $errorCounter++;
        } else if (!isset($postData['term_id']) || empty($postData['term_id'])) {
            $error =  esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if (!isset($postData['type']) || empty($postData['type'])) {
            $error =  esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] =  esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else {
            $type = self::sanitize_options($postData['type']);
            if(!wp_verify_nonce($postData['nonce'], 'wcp_folder_nonce_'.$type)) {
                $error =  esc_html__("Your request is not valid", 'folders');
                $errorCounter++;
            }
        }
        if ($errorCounter == 0) {
            $term_id = self::sanitize_options($postData['term_id']);
            $type = self::sanitize_options($postData['type']);
            $response['term_ids'] = array();
            if(!empty($term_id)) {
                $term_id = trim($term_id,",");
                $term_ids = explode(",", $term_id);
                if(is_array($term_ids) && count($term_ids) > 0) {
                    foreach ($term_ids as $term) {
                        self::remove_folder_child_items($term, $type);
                    }
                    $response['term_ids'] = $term_ids;
                }
            }
            $is_active = 1;
            $folders = -1;
            $response['status'] = 1;

            $response['folders'] = $folders;
            $response['is_key_active'] = $is_active;
        } else {
            $response['error'] = 1;
            $response['message'] = $error;
        }
        echo json_encode($response);
        wp_die();
    }

    public function wcp_remove_folder()
    {
        $response = array();
        $response['status'] = 0;
        $response['error'] = 0;
        $response['data'] = array();
        $response['message'] = "";
        $postData = filter_input_array(INPUT_POST);
        $errorCounter = 0;
        if (!current_user_can("manage_categories")) {
            $error =  esc_html__("You have not permission to remove folder", 'folders');
            $errorCounter++;
        } else if (!isset($postData['term_id']) || empty($postData['term_id'])) {
            $error =  esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if (!isset($postData['type']) || empty($postData['type'])) {
            $error =  esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $error =  esc_html__("Unable to delete folder, Your request is not valid", 'folders');
            $errorCounter++;
        } else {
            $term_id = self::sanitize_options($postData['term_id']);
            if(!wp_verify_nonce($postData['nonce'], 'wcp_folder_term_'.$term_id)) {
                $error =  esc_html__("Unable to delete folder, Your request is not valid", 'folders');
                $errorCounter++;
            }
        }
        if ($errorCounter == 0) {
            $term_id = self::sanitize_options($postData['term_id']);
            $type = self::sanitize_options($postData['type']);
            self::remove_folder_child_items($term_id, $type);
            $response['status'] = 1;
            $is_active = 1;
            $folders = -1;

            $response['folders'] = $folders;
            $response['term_id'] = $term_id;
            $response['is_key_active'] = $is_active;
        } else {
            $response['error'] = 1;
            $response['message'] = $error;
        }
        echo json_encode($response);
        wp_die();
    }

    public function remove_folder_child_items($term_id, $post_type)
    {
        $folder_type = self::get_custom_post_type($post_type);
        $terms = get_terms($folder_type, array(
            'hide_empty' => false,
            'parent' => $term_id
        ));

        if (!empty($terms)) {
            foreach ($terms as $term) {
                self::remove_folder_child_items($term->term_id, $post_type);
            }
            wp_delete_term($term_id, $folder_type);
        } else {
            wp_delete_term($term_id, $folder_type);
        }
    }

    public function wcp_update_folder()
    {
        $response = array();
        $response['status'] = 0;
        $response['error'] = 0;
        $response['data'] = array();
        $response['message'] = "";
        $postData = $_REQUEST;
        $errorCounter = 0;
        if (!current_user_can("manage_categories")) {
            $error =  esc_html__("You have not permission to update folder", 'folders');
            $errorCounter++;
        } else if (!isset($postData['term_id']) || empty($postData['term_id'])) {
            $error =  esc_html__("Unable to rename folder, Your request is not valid", 'folders');
            $errorCounter++;
        } else if (!isset($postData['name']) || empty($postData['name'])) {
            $error =  esc_html__("Folder name can no be empty", 'folders');
            $errorCounter++;
        } else if (!isset($postData['type']) || empty($postData['type'])) {
            $error =  esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $error =  esc_html__("Unable to rename folder, Your request is not valid", 'folders');
            $errorCounter++;
        } else {
            $term_id = self::sanitize_options($postData['term_id']);
            if(!wp_verify_nonce($postData['nonce'], 'wcp_folder_term_'.$term_id)) {
                $error =  esc_html__("Unable to rename folder, Your request is not valid", 'folders');
                $errorCounter++;
            }
        }
        if ($errorCounter == 0) {
            $type = self::sanitize_options($postData['type']);
            $folder_type = self::get_custom_post_type($type);
            $name = self::sanitize_options($postData['name']);
            $term_id = self::sanitize_options($postData['term_id']);
            $result = wp_update_term(
                $term_id,
                $folder_type,
                array(
                    'name' => $name,
                )
            );
            if (!empty($result)) {
                $term_nonce = wp_create_nonce('wcp_folder_term_'.$result['term_id']);
                $response['id'] = $result['term_id'];
                $response['slug'] = $result['slug'];
                $response['status'] = 1;
                $response['term_title'] = $postData['name'];
                $response['nonce'] = $term_nonce;
            } else {
                $response['message'] =  esc_html__("Unable to rename folder", 'folders');
            }
        } else {
            $response['error'] = 1;
            $response['message'] = $error;
        }
        echo json_encode($response);
        wp_die();
    }

    public function create_slug_from_string($str)
    {
        $a = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'Ð', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', '?', '?', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', '?', '?', 'L', 'l', 'N', 'n', 'N', 'n', 'N', 'n', '?', 'O', 'o', 'O', 'o', 'O', 'o', 'Œ', 'œ', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'Š', 'š', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Ÿ', 'Z', 'z', 'Z', 'z', 'Ž', 'ž', '?', 'ƒ', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', '?', '?', '?', '?', '?', '?');
        $b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o');
        return strtolower(preg_replace(array('/[^a-zA-Z0-9 -]/', '/[ -]+/', '/^-|-$/'), array('', '-', ''), str_replace($a, $b, $str)));
    }

    public static function sanitize_options($value, $type = "") {
        $value = stripslashes($value);
        return $value;
    }

    public function wcp_add_new_folder()
    {
        $response = array();
        $response['status'] = 0;
        $response['error'] = 0;
        $response['login'] = 1;
        $response['data'] = array();
        $response['message'] = "";
        $response['message2'] = "";
        $postData = $_REQUEST;
        $errorCounter = 0;
        $error = esc_html__("Your request is not valid", 'folders');
        if (!current_user_can("manage_categories")) {
            $error =  esc_html__("You have not permission to add folder", 'folders');
            $errorCounter++;
        } else if (!isset($postData['name']) || empty($postData['name'])) {
            $error =  esc_html__("Folder name can no be empty", 'folders');
            $errorCounter++;
        } else if (!isset($postData['type']) || empty($postData['type'])) {
            $error =  esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['login'] = 0;
            $error =  esc_html__("Unable to create folder, Your request is not valid", 'folders');
            $errorCounter++;
        } else {
            $type = self::sanitize_options($postData['type']);
            if(!wp_verify_nonce($postData['nonce'], 'wcp_folder_nonce_'.$type)) {
                $response['login'] = 0;
                $error =  esc_html__("Unable to create folder, Your request is not valid", 'folders');
                $errorCounter++;
            }
        }
        if ($errorCounter == 0) {
            $parent = isset($postData['parent_id']) && !empty($postData['parent_id']) ? $postData['parent_id'] : 0;
            $parent = self::sanitize_options($parent);
            $type = self::sanitize_options($postData['type']);
            $folder_type = self::get_custom_post_type($type);
            $term_name = self::sanitize_options($postData['name']);
            $term = term_exists($term_name, $folder_type, $parent);
            $user_id = get_current_user_id();
            if(!empty($term) && isset($term['term_id']) && !empty($term['term_id'])) {
                $term_user = get_term_meta($term['term_id'], "created_by", true);
                if($term_user == $user_id) {
                    $response['error'] = 1;
                    $response['message'] =  esc_html__("Folder name already exists", 'folders');
                    echo json_encode($response);
                    wp_die();
                }
            }
            $folders = $postData['name'];
            $folders = explode(",", $folders);
            $foldersArray = array();
            $order = isset($postData['order']) ? $postData['order'] : 0;
            $order = self::sanitize_options($order);

            $is_active = 1;
            $created_folders = 0;

            foreach ($folders as $key => $folder) {
                $term = term_exists($folder, $folder_type, $parent);
                if(!empty($term) && isset($term['term_id']) && !empty($term['term_id'])) {
                    $term_user = get_term_meta($term['term_id'], "created_by", true);
                    if($term_user == $user_id) {
                        continue;
                    }
                }

                $folder = trim($folder);
                $slug = self::create_slug_from_string($folder) . "-" . time()."-".$user_id;

                $result = wp_insert_term(
                    urldecode($folder), // the term
                    $folder_type, // the taxonomy
                    array(
                        'parent' => $parent,
                        'slug' => $slug
                    )
                );

                if (!empty($result)) {
                    $created_folders++;
                    $response['id'] = $result['term_id'];
                    $response['status'] = 1;
                    $term = get_term($result['term_id'], $folder_type);
                    $order = $order + $key;
                    $term_nonce = wp_create_nonce('wcp_folder_term_' . $term->term_id);

                    $folder_item = array();
                    $folder_item['parent_id'] = $parent;
                    $folder_item['slug'] = $term->slug;
                    $folder_item['nonce'] = $term_nonce;
                    $folder_item['term_id'] = $result['term_id'];
                    $folder_item['title'] = $folder;
                    $folder_item['parent_id'] = empty($postData['parent_id']) ? "0" : $postData['parent_id'];
                    $folder_item['is_sticky'] = 0;
                    $folder_item['is_high'] = 0;
                    $folder_item['is_locked'] = 0;
                    $folder_item['folder_count'] = 0;

                    add_term_meta($result['term_id'], "created_by", $user_id);

                    update_term_meta($result['term_id'], "wcp_custom_order", $order);
                    if ($parent != 0) {
                        update_term_meta($parent, "is_active", 1);
                    }

                    if (isset($postData['is_duplicate']) && $postData['is_duplicate'] == true) {
                        if (isset($postData['duplicate_from']) && !empty($postData['duplicate_from'])) {
                            $term_id = $postData['duplicate_from'];

                            $term_data = get_term($term_id, $folder_type);
                            if (!empty($term_data)) {
                                $is_sticky = get_term_meta($term_id, "is_folder_sticky", true);

                                if ($is_sticky == 1) {
                                    add_term_meta($term->term_id, "is_folder_sticky", 1);
                                    $folder_item['is_sticky'] = 1;
                                }

                                $is_locked = get_term_meta($term_id, "is_locked", true);
                                if ($is_locked == 1) {
                                    add_term_meta($term->term_id, "is_locked", 1);
                                    $folder_item['is_locked'] = 1;
                                }

                                $status = get_term_meta($term_id, "is_highlighted", true);
                                if ($status == 1) {
                                    add_term_meta($term->term_id, "is_highlighted", 1);
                                    $folder_item['is_high'] = 1;
                                }

                                $postArray = get_posts(
                                    array(
                                        'posts_per_page' => -1,
                                        'post_type' => $type,
                                        'tax_query' => array(
                                            array(
                                                'taxonomy' => $folder_type,
                                                'field' => 'term_id',
                                                'terms' => $term_id,
                                            )
                                        )
                                    )
                                );
                                if (!empty($postArray)) {
                                    foreach ($postArray as $p) {
                                        wp_set_post_terms($p->ID, $term->term_id, $folder_type, true);
                                    }
                                    $folder_item['folder_count'] = count($postArray);
                                }
                            }
                        }
                    }
                    $foldersArray[] = $folder_item;
                }

                if (!empty($foldersArray)) {
                    $response['is_key_active'] = $is_active;
                    $response['folders'] = $created_folders;
                    $response['parent_id'] = empty($parent) ? "#" : $parent;

                    $response['status'] = 1;
                    $response['data'] = $foldersArray;
                }
            }
        } else {
            $response['error'] = 1;
            $response['message'] = $error;
        }
        echo json_encode($response);
        wp_die();
    }

    public function is_for_this_post_type($post_type)
    {
        $post_types = get_option('folders_settings');
        $post_types = is_array($post_types)?$post_types:array();
        return in_array($post_type, $post_types);
    }

    public function is_active_for_screen()
    {
        global $typenow, $current_screen;


        if(($typenow == "attachment" || $typenow == "media") && (isset($_REQUEST['attachment-filter']) && $_REQUEST['attachment-filter'] == "trash")) {
            return false;
        }

        $postData = filter_input_array(INPUT_POST);


//        if ((isset($postData['action']) && $postData['action'] == 'inline-save') && (isset($postData['post_type']) && self::is_for_this_post_type($postData['post_type']))) {
//            return true;
//        }
        global $current_screen;

        if (self::is_for_this_post_type($typenow) && ('edit' == $current_screen->base || 'upload' == $current_screen->base)) {
            return true;
        }

        $post_types = get_option('folders_settings');
        $post_types = is_array($post_types)?$post_types:array();

        if(empty($typenow) && (isset($current_screen->base) && 'upload' == $current_screen->base)) {
            $typenow = "attachment";
            if (self::is_for_this_post_type($typenow)) {
                return true;
            }
        }
        return false;
    }

    public function is_add_update_screen()
    {
        global $current_screen;
        $current_type = $current_screen->base;
        $action = $current_screen->action;
        $post_types = get_option('folders_settings');
        $post_types = is_array($post_types)?$post_types:array();
        global $typenow;
        if (in_array($current_type, $post_types) && in_array($action, array("add", ""))) {
            $license_data = self::get_license_key_data();

            $is_active = 1;
            $folders = -1;
            $response['folders'] = $folders;
            $response['is_key_active'] = $is_active;
        }
    }

    public static function get_custom_post_type($post_type)
    {
        if ($post_type == "post") {
            return "post_folder";
        } else if ($post_type == "page") {
            return "folder";
        } else if ($post_type == "attachment") {
            return "media_folder";
        }
        return $post_type . '_folder';
    }

    public function admin_footer()
    {
        if (self::is_active_for_screen()) {
            global $typenow;

            self::set_default_values_if_not_exists();

            $ttpsts = self::get_ttlpst($typenow);

            $ttemp = self::get_tempt_posts($typenow);

            $folder_type = self::get_custom_post_type($typenow);

            $sticky_open = get_option("premio_folder_sticky_status_".$typenow);
            $sticky_open = ($sticky_open == 1)?1:0;
            /* Do not change: Free/Pro Class name change */
            $tree_data = WCP_Pro_Tree::get_full_tree_data($folder_type, "", "", $sticky_open, self::$foldersByUser);
            $terms_data = $tree_data['string'];
            $sticky_string = $tree_data['sticky_string'];
            $terms_html = WCP_Pro_Tree::get_option_data_for_select($folder_type);
            $hasValidKey = $this->check_has_valid_key();
            $activateURL = $this->getRegisterKeyURL();
            $form_html = WCP_Pro_Forms::get_form_html($hasValidKey, $activateURL, $terms_html);

            $dynamic_folders = new Premio_Pro_Folders_Dynamic_Folders();
            $authors = $dynamic_folders->get_author_dynamic_folders($typenow, $folder_type);
            $dates = $dynamic_folders->get_date_dynamic_folders($typenow, $folder_type);
            $extensions = $dynamic_folders->get_file_ext_dynamic_folders($typenow, $folder_type);
            $post_categories = $dynamic_folders->get_post_category_dynamic_folders($typenow, $folder_type);
            $page_hierarchy = $dynamic_folders->get_page_hierarchy_dynamic_folders($typenow, $folder_type);
            $files = "";

            include_once dirname(dirname(__FILE__)) . WCP_DS . "/templates" . WCP_DS . "admin" . WCP_DS . "admin-content.php";
        }

        global $pagenow;
        if ( 'plugins.php' !== $pagenow ) {

        } else {
            if (current_user_can('manage_options')) {
                include_once dirname(dirname(__FILE__)) . WCP_DS . "/templates" . WCP_DS . "admin" . WCP_DS . "folder-deactivate-form.php";
            }
        }
    }

    public function get_ttlpst($post_type = "")
    {
        global $typenow;
        if (empty($post_type)) {
            $post_type = $typenow;
        }
        $item_count = null;
        if(has_filter("premio_folder_all_categorized_items")) {
            $item_count = apply_filters("premio_folder_all_categorized_items", $post_type);
        }
        if($item_count === null) {
            if ($post_type == "attachment") {
                $item_count = wp_count_posts($post_type)->inherit;
            } else {
                $item_count = wp_count_posts($post_type)->publish + wp_count_posts($post_type)->draft + wp_count_posts($post_type)->future + wp_count_posts($post_type)->private;
            }
        }
        return $item_count;
    }

    public function autoload()
    {
        $files = array(
            'WCP_Tree_View' => WCP_DS . "includes" . WCP_DS . "tree.class.php",
            'WCP_Form_View' => WCP_DS . "includes" . WCP_DS . "form.class.php",
            'WCP_Folder_WPML' => WCP_DS . "includes" . WCP_DS . "class-wpml.php",
            'WCP_Folder_PolyLang' => WCP_DS . "includes" . WCP_DS . "class-polylang.php",
            'WCP_Dynamic_Folders' => WCP_DS . "includes" . WCP_DS . "dynamic-folders.php",
            'Folders_Pro_Size_Meta' => WCP_DS . "includes" . WCP_DS . "size.class.php",
        );

        foreach ($files as $file) {
            if (file_exists(dirname(dirname(__FILE__)) . $file)) {
                include_once dirname(dirname(__FILE__)) . $file;
            }
        }
    }

    public function create_folder_terms()
    {
        $options = get_option('folders_settings');
        $options = is_array($options)?$options:array();
        $old_plugin_status = 0;
        $posts = array();
        if (!empty($options)) {
            foreach ($options as $option) {
                if (!(strpos($option, 'folder4') === false) && $old_plugin_status == 0) {
                    $old_plugin_status = 1;
                }
                if (in_array($option, array("page", "post", "attachment"))) {
                    $posts[] = str_replace("folder4", "", $option);
                } else {
                    $posts[] = $option;
                }
            }
            if(!empty($posts)) {
                update_option('folders_settings', $posts);
            }
        }
        if ($old_plugin_status == 1) {
            update_option("folders_show_in_menu", "on");
            $old_plugin_var = get_option("folder_old_plugin_status");
            if (empty($old_plugin_var) || $old_plugin_var == null) {
                update_option("folder_old_plugin_status", "1");
            }
        }
        $posts = get_option('folders_settings');
        if (!empty($posts)) {
            foreach ($posts as $post_type) {
                $labels = array(
                    'name' => esc_html__('Folders', 'folders'),
                    'singular_name' => esc_html__('Folder', 'folders'),
                    'all_items' => esc_html__('All Folders', 'folders'),
                    'edit_item' => esc_html__('Edit Folder', 'folders'),
                    'update_item' => esc_html__('Update Folder', 'folders'),
                    'add_new_item' => esc_html__('Add New Folder', 'folders'),
                    'new_item_name' => esc_html__('Add folder name', 'folders'),
                    'menu_name' => esc_html__('Folders', 'folders'),
                    'search_items' => esc_html__('Search Folders', 'folders'),
                    'parent_item' => esc_html__('Parent Folder', 'folders'),
                );

                $args = array(
                    'label' => esc_html__('Folder', 'folders'),
                    'labels' => $labels,
                    'show_tagcloud' => false,
                    'hierarchical' => true,
                    'public' => false,
                    'show_ui' => true,
                    'show_in_menu' => false,
                    'show_in_rest' => true,
                    'show_admin_column' => true,
                    'update_count_callback' => '_update_post_term_count',
//                    'update_count_callback' => '_update_generic_term_count',
                    'query_var' => true,
                    'rewrite' => false,
                    'capabilities' => array(
                        'manage_terms' => 'manage_categories',
                        'edit_terms'   => 'manage_categories',
                        'delete_terms' => 'manage_categories',
                        'assign_terms' => 'manage_categories'
                    )
                );

                $folder_post_type = self::get_custom_post_type($post_type);

                register_taxonomy(
                    $folder_post_type,
                    $post_type,
                    $args
                );
            }
        }

        $postData = filter_input_array(INPUT_POST);

        if(current_user_can("manage_categories") && isset($postData['folder_nonce'])) {
            if(wp_verify_nonce($postData['folder_nonce'], "folder_settings")) {
                if (isset($postData['folders_show_in_menu']) && !empty($postData['folders_show_in_menu'])) {
                    $show_menu = "off";
                    if ($postData['folders_show_in_menu'] == "on") {
                        $show_menu = "on";
                    }
                    update_option("folders_show_in_menu", $show_menu);
                }

                if (isset($postData['folders_settings1'])) {
                    $posts = array();
                    if (isset($postData['folders_settings']) && is_array($postData['folders_settings'])) {
                        foreach ($postData['folders_settings'] as $key => $val) {
                            $posts[] = $val;
                        }
                    }
                    update_option("folders_settings", $posts);
                }

                if (isset($_POST['folders_settings1'])) {
                    $posts = array();
                    if (isset($_POST['default_folders']) && is_array($_POST['default_folders'])) {
                        foreach ($_POST['default_folders'] as $key => $val) {
                            $posts[$key] = $val;
                        }
                    }
                    update_option("default_folders", $posts);
                }

                if (isset($_POST['customize_folders'])) {
                    $posts = array();
                    if (isset($_POST['customize_folders']) && is_array($_POST['customize_folders'])) {
                        foreach ($_POST['customize_folders'] as $key => $val) {
                            $posts[$key] = $val;
                        }
                    }

                    update_option("customize_folders", $posts);
                }

	            $setting_page = $this->getFolderSettingsURL();
	            if(!empty($setting_page)) {
		            $page = isset($_POST['tab_page'])?$_POST['tab_page']:"";
                    $page = $this->filter_string_polyfill($page);

                    $type = isset($_GET['setting_page'])?$_GET['setting_page']:"";
                    $type = $this->filter_string_polyfill($type);

		            $type = empty($type)?"":"&setting_page=".$type;
		            $setting_page = $setting_page.$type;
		            if(!empty($page)) {
			            $setting_page .= "&setting_page=".$page;
		            }
		            wp_redirect($setting_page."&note=1");
		            exit;
	            } else if(isset($_POST['folder_page']) && !empty($_POST['folder_page'])) {
		            wp_redirect($_POST['folder_page']);
		            exit;
	            }
            }
        }

//        $old_version = get_option("folder_old_plugin_status");
//        if($old_version !== false && $old_version == 1) {
//            $tlfs = get_option("folder_old_plugin_folder_status");
//            if($tlfs === false) {
//                $total = self::ttl_fldrs();
//                if($total <= 10) {
//                    $total = 10;
//                };
//                update_option("folder_old_plugin_folder_status", $total);
//                self::$folders = $total;
//            } else {
//                self::$folders = $tlfs;
//            }
//        }
//
//        $tlfs = get_option("folder_old_plugin_folder_status");
//        if($tlfs === false) {
//            self::$folders = 10;
//        } else {
//            self::$folders = $tlfs;
//        }
    }

    function searchForId($id, $menu)
    {
        if ($menu) {
            foreach ($menu as $key => $val) {
                if (array_key_exists(2, $val)) {
                    $stripVal = explode('=', $val[2]);
                }
                if (array_key_exists(1, $stripVal)) {
                    $stripVal = $stripVal[1];
                }
                if ($stripVal === $id) {
                    return $key;
                }
            }
        }
    }

    function create_menu_for_folders()
    {
        global $menu;
        self::check_and_set_post_type();

        $folder_types = get_option("folders_settings");
        if (empty($folder_types)) {
            return;
        }

        foreach ($folder_types as $type) {
            $itemKey = self::searchForId($type, $menu);
            switch (true) {
                case ($type == 'attachment'):
                    $itemKey = 10;
                    $edit = 'upload.php';
                    break;
                case ($type === 'post'):
                    $edit = 'edit.php';
                    $itemKey = 5;
                    break;
                default:
                    $edit = 'edit.php';
                    break;
            }

            $folder = ($type == 'attachment') ? 'media' : $type;
            $upper = ($type == 'attachment') ? 'Media' : ucwords(str_replace(array('-', '_'), ' ', $type));

            if ($type == 'page') {
                $tax_slug = 'folder';
            } else if($type == 'attachment' || $type == 'media') {
                $tax_slug = 'media_folder';
            } else {
                $tax_slug = $folder . '_folder';
            }
            $hide_empty = true;
            if ($type == 'attachment') {
                $hide_empty = false;
                add_menu_page('Media Folders', 'Media Folders', 'publish_pages', "{$edit}?post_type=attachment&media_folder=", false, 'dashicons-portfolio', "{$itemKey}.5");
            } else {
                add_menu_page($upper . ' Folders', "{$upper} Folders", 'publish_pages', "{$edit}?post_type={$type}&type=folder", false, 'dashicons-portfolio', "{$itemKey}.5");
            }

            $terms = get_terms($tax_slug, array(
                    'hide_empty' => $hide_empty,
                    'parent'   => 0,
                    'orderby' => 'meta_value_num',
                    'order' => 'ASC',
                    'hierarchical' => false,
                    'meta_query' => [[
                        'key' => 'wcp_custom_order',
                        'type' => 'NUMERIC',
                    ]]
                )
            );

            if ($terms && !empty($terms)) {
                foreach ($terms as $term) {
                    if(isset($term->trash_count) && !empty($term->trash_count)) {
                        if ($type == 'attachment') {
                            add_submenu_page("{$edit}?type=folder", $term->name, $term->name, 'publish_pages', "{$edit}?post_type=attachment&media_folder={$term->slug}", false);
                        } else {
                            add_submenu_page("{$edit}?post_type={$type}&type=folder", $term->name, $term->name, 'publish_pages', "{$edit}?post_type={$type}&{$tax_slug}={$term->slug}", false);
                        }
                    }
                }
            }
        }
    }

    function folders_admin_styles($page)
    {
        if (self::is_active_for_screen()) {
            wp_enqueue_style('wcp-folders-fa', plugin_dir_url(dirname(__FILE__)) . 'assets/css/folder-icon.css', array(), WCP_PRO_FOLDER_VERSION);
            wp_enqueue_style('wcp-folders-admin', plugin_dir_url(dirname(__FILE__)) . 'assets/css/design.css', array(), WCP_PRO_FOLDER_VERSION);
            wp_enqueue_style('wcp-folders-jstree', plugin_dir_url(dirname(__FILE__)) . 'assets/css/jstree.min.css', array(), WCP_PRO_FOLDER_VERSION);
            wp_enqueue_style('wcp-folders-mcustomscrollbar', plugin_dir_url(dirname(__FILE__)) . 'assets/css/jquery.mcustomscrollbar.min.css', array(),WCP_PRO_FOLDER_VERSION);
            wp_enqueue_style('wcp-folders-css', plugin_dir_url(dirname(__FILE__)) . 'assets/css/folders.min.css', array(), WCP_PRO_FOLDER_VERSION);
        }
        wp_enqueue_style('wcp-folders-media', plugin_dir_url(dirname(__FILE__)) . 'assets/css/media-clean.css', array(), WCP_PRO_FOLDER_VERSION);
        wp_register_style('wcp-css-handle', false);
        wp_enqueue_style('wcp-css-handle');
        $css = "
				.wcp-folder-upgrade-button {color: #FF5983; font-weight: bold;}
			";
        if (self::is_active_for_screen()) {
            global $typenow;
            $width = get_option("wcp_dynamic_width_for_" . $typenow);
            $width = esc_attr($width);
            $width = intval($width);
            $width = empty($width)||!is_numeric($width)?280:$width;
            if($width > 1200) {
                $width = 280;
            }
            $width = intval($width);
            $display_status = "wcp_dynamic_display_status_" . $typenow;
            $display_status = get_option($display_status);
            if($display_status != "hide") {
                if (!empty($width) && is_numeric($width)) {
                    $css .= ".wcp-content {width:{$width}px}";
                    if (function_exists('is_rtl') && is_rtl()) {
                        $css .= "html[dir='rtl'] body.wp-admin #wpcontent {padding-right:" . ($width + 20) . "px}";
                        $css .= "html[dir='rtl'] body.wp-admin #e-admin-top-bar-root.e-admin-top-bar--active {width: calc(100% - 160px - " . ($width) . "px)}";
                        $css .= "html[dir='rtl'] body.wp-admin #wpcontent {padding-left:0px}";
                    } else {
                        $css .= "body.wp-admin #wpcontent {padding-left:" . ($width + 20) . "px}";
                        $css .= "body.wp-admin #e-admin-top-bar-root.e-admin-top-bar--active {width: calc(100% - 160px - " . ($width) . "px)}";
                    }
                }
            } else {
                if (function_exists('is_rtl') && is_rtl()) {
                    $css .= "html[dir='rtl']  body.wp-admin #wpcontent {padding-right:20px}";
                    $css .= "html[dir='rtl'] body.wp-admin #wpcontent {padding-left:0px}";
                } else {
                    $css .= "body.wp-admin #wpcontent {padding-left:20px}";
                }
            }
            if (!empty($width) && is_numeric($width)) {
                if($width > 1200) {
                    $width = 280;
                }
                $width = intval($width);
                $css .= ".wcp-content {width: {$width}px}";
            }
            global $typenow;
            $post_type = self::get_custom_post_type($typenow);
            $css .= "body:not(.woocommerce-page) .wp-list-table th#taxonomy-{$post_type} { width: 130px !important; } @media screen and (max-width: 1180px) { body:not(.woocommerce-page) .wp-list-table th#taxonomy-{$post_type} { width: 90px !important; }} @media screen and (max-width: 960px) { body:not(.woocommerce-page) .wp-list-table th#taxonomy-{$post_type} { width: auto !important; }}";
        }
        wp_add_inline_style('wcp-css-handle', $css);

        if (self::is_active_for_screen()) {
            global $typenow;
            add_filter('views_edit-' . $typenow, array($this, 'wcp_check_for_child_folders'));
        }
    }

    function wcp_check_for_child_folders($content)
    {
        $termId = 0;
        global $typenow;
        $post_type = self::get_custom_post_type($typenow);
        if (isset($_GET[$post_type]) && !empty($_GET[$post_type])) {
            $term = $_GET[$post_type];
            $term = get_term_by("slug", $term, $post_type);
            if (!empty($term)) {
                $termId = $term->term_id;
            }
        }
        $terms = get_terms($post_type, array(
            'hide_empty' => false,
            'parent' => $termId,
            'orderby' => 'meta_value_num',
            'order' => 'ASC',
            'hierarchical' => false,
            'update_count_callback' => '_update_generic_term_count',
            'meta_query' => [[
                'key' => 'wcp_custom_order',
                'type' => 'NUMERIC',
            ]]
        ));
        $optionName = "wcp_folder_display_status_" . $typenow;
        $optionValue = get_option($optionName);
        $class = (!empty($optionValue) && $optionValue == "hide")?"":"active";
        $customize_folders = get_option('customize_folders');
        $show_in_page = isset($customize_folders['show_in_page'])?$customize_folders['show_in_page']:"hide";
        if(empty($show_in_page)) {
            $show_in_page = "hide";
        }
        if($show_in_page == "show") {
            echo '<div class="tree-structure-content ' . $class . '"><div class="tree-structure" id="list-folder-' . $termId . '" data-id="' . $termId . '">';
            echo '<ul>';
            foreach ($terms as $term) {
                $status = get_term_meta($term->term_id, "is_highlighted", true);
                ?>
                <li class="grid-view" data-id="<?php echo $term->term_id ?>" id="folder_<?php echo $term->term_id ?>">
                    <div class="folder-item is-folder" data-id="<?php echo $term->term_id ?>">
                        <a title='<?php echo $term->name ?>' id="folder_view_<?php echo $term->term_id ?>"
                           class="folder-view <?php echo ($status == 1) ? "is-high" : "" ?>"
                           data-id="<?php echo $term->term_id ?>">
                        <span class="folder item-name"><span id="wcp_folder_text_<?php echo $term->term_id ?>"
                                                             class="folder-title"><?php echo $term->name ?></span></span>
                            <!--<span class="folder-option"></span>-->
                        </a>
                    </div>
                </li>
            <?php
            }
            echo '</ul>';
            echo '<div class="clear clearfix"></div>';
            echo '</div>';
            echo '<div class="folders-toggle-button"><span></span></div>';
            echo '</div>';
        }
        if(!empty($content) && is_array($content)) {
            echo '<ul class="subsubsub">';
            foreach($content as $k=>$v) {
                echo "<li class='{$k}'>{$v}</li>";
            }
            echo '</ul>';
        }
    }

    function folders_admin_scripts($hook)
    {
        if (self::is_active_for_screen()) {
            global $typenow;

            remove_filter("terms_clauses", "TO_apply_order_filter");

            /* Free/Pro Version change */
            wp_dequeue_script("jquery-jstree");  // CMS Tree Page View Conflict
            wp_enqueue_script('wcp-folders-mcustomscrollbar', plugin_dir_url(dirname(__FILE__)) . 'assets/js/jquery.mcustomscrollbar.min.js', array(), WCP_PRO_FOLDER_VERSION);
            wp_enqueue_script('wcp-folders-jstree', plugin_dir_url(dirname(__FILE__)) . 'assets/js/jstree.js', array('jquery'), WCP_PRO_FOLDER_VERSION);
            wp_enqueue_script('wcp-jquery-touch', plugin_dir_url(dirname(__FILE__)) . 'assets/js/jquery.ui.touch-punch.min.js', array('jquery'), WCP_PRO_FOLDER_VERSION);
            wp_enqueue_script('wcp-folders-custom', plugin_dir_url(dirname(__FILE__)) . 'assets/js/folders.js', array('jquery', 'jquery-ui-resizable', 'jquery-ui-draggable', 'jquery-ui-droppable', 'jquery-ui-sortable', 'backbone'), WCP_PRO_FOLDER_VERSION);
//            wp_enqueue_script('wcp-folders-custom', plugin_dir_url(dirname(__FILE__)) . 'assets/js/custom.js', array('jquery', 'jquery-ui-resizable', 'jquery-ui-draggable', 'jquery-ui-droppable', 'jquery-ui-sortable', 'backbone'), WCP_PRO_FOLDER_VERSION);


            $post_type = self::get_custom_post_type($typenow);

            if ($typenow == "attachment") {
                $admin_url = admin_url("upload.php?post_type=attachment&media_folder=");
            } else {
                $admin_url = admin_url("edit.php?post_type=" . $typenow);
                if (isset($_GET['s']) && !empty($_GET['s'])) {      
                    $admin_url .= "&s=" . $_GET['s'];
                }
                $admin_url .= "&{$post_type}=";
            }

            $current_url = $admin_url;
            if(isset($_GET[$post_type]) && !empty($_GET[$post_type])) {
                $current_url .= $_GET[$post_type];
            }


            $is_active = 1;
            $folders = -1;
            /* For free: upgrade URL, for Pro: Register Key URL */
            $register_url = $this->getRegisterKeyURL();

            $is_rtl = 0;
            if ( function_exists( 'is_rtl' ) && is_rtl() ) {
                $is_rtl = 1;
            }

            $can_manage_folder = current_user_can("manage_categories")?1:0;
            $width = get_option("wcp_dynamic_width_for_" . $typenow);
            $width = intval($width);
            $width = empty($width)||!is_numeric($width)?280:$width;
            if($width > 1200) {
                $width = 280;
            }
            $post_type = self::get_custom_post_type($typenow);
            $taxonomy_status = 0;
            $selected_taxonomy = "";
            if(!isset($_GET[$post_type]) || empty($_GET[$post_type])) {
                $taxonomy_status = 1;
            } else if(isset($_GET[$post_type]) && !empty($_GET[$post_type])) {
                $selected_taxonomy = $_GET[$post_type];

                $term = get_term_by('slug', $selected_taxonomy, $post_type);
                if (!empty($term) && is_object($term)) {
                    $selected_taxonomy = $term->term_id;
                } else {
                    $selected_taxonomy = "";
                }
            }
            $customize_folders = get_option('customize_folders');
	        $show_in_page = isset($customize_folders['show_in_page'])?$customize_folders['show_in_page']:"hide";
	        if(empty($show_in_page)) {
		        $show_in_page = "hide";
	        }
            $use_shortcuts = !isset($customize_folders['use_shortcuts'])?"yes":$customize_folders['use_shortcuts'];
            $taxonomies = self::get_terms_hierarchical($post_type);
            $use_folder_undo = !isset($customize_folders['use_folder_undo'])?"yes":$customize_folders['use_folder_undo'];
            $defaultTimeout = !isset($customize_folders['default_timeout'])?5:intval($customize_folders['default_timeout']);
            if(empty($defaultTimeout) || !is_numeric($defaultTimeout) || $defaultTimeout < 0) {
                $defaultTimeout = 5;
            }
            $defaultTimeout = $defaultTimeout*1000;

            $folder_settings = array();
            foreach($taxonomies as $taxonomy) {
                $is_sticky = get_term_meta($taxonomy->term_id, "is_folder_sticky", true);
                $is_high = get_term_meta($taxonomy->term_id, "is_highlighted", true);
                $is_locked = get_term_meta($taxonomy->term_id, "is_locked", true);
                $folder_settings[] = array(
                    'folder_id' => $taxonomy->term_id,
                    'is_sticky' => intval($is_sticky),
                    'is_high' => intval($is_high),
                    'is_locked' => intval($is_locked),
                    'nonce' => wp_create_nonce('wcp_folder_term_'.$taxonomy->term_id),
                    'is_deleted' => 0,
                    'slug' => $taxonomy->slug,
                    'folder_count' => intval($taxonomy->trash_count)
                );
            }

            $response['terms'] = $taxonomies;
            $currentPage = (isset($_GET['paged']) && !empty($_GET['paged']) && is_numeric($_GET['paged']) && $_GET['paged'] > 0)?$_GET['paged']:1;
            $hasStars = self::check_for_setting("has_stars", "general");
            $hasChild = self::check_for_setting("has_child", "general");
            $hasChild = empty($hasChild)?0:1;
            $hasStars = empty($hasStars)?0:1;
            $hasValidKey = $this->check_has_valid_key();
            $hasValidKey = ($hasValidKey)?1:0;

            $default_folders = get_option("default_folders");
            $default_folder = 0;
            if(isset($default_folders[$typenow])) {
                $default_folder = $default_folders[$typenow];
            }

//            echo "<pre>"; print_r($default_folders); die;

            wp_localize_script('wcp-folders-custom', 'wcp_settings', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'admin_url' => admin_url(''),
                'post_type' => $typenow,
                'page_url' => $admin_url,
                'current_url' => $current_url,
                'ajax_image' => plugin_dir_url(dirname(__FILE__)) . "assets/images/ajax-loader.gif",
                'is_key_active' => $is_active,
                'folders' => $folders,
                'register_url' => $register_url,
                'isRTL' => $is_rtl,
                'nonce' => wp_create_nonce('wcp_folder_nonce_'.$typenow),
                'can_manage_folder' => $can_manage_folder,
                'folder_width' => $width,
                'taxonomy_status' => $taxonomy_status,
                'selected_taxonomy' => $selected_taxonomy,
                'show_in_page' => $show_in_page,
                'svg_file' => WCP_PRO_FOLDER_URL.'assets/images/pin.png',
                'taxonomies' => $taxonomies,
                'folder_settings' => $folder_settings,
                'hasStars' => $hasStars,
                'hasChildren' => $hasChild,
                'currentPage' => $currentPage,
                'hasValidLicense' => $hasValidKey,
                'useFolderUndo' => $use_folder_undo,
                'defaultTimeout' => $defaultTimeout,
                'default_folder' => $default_folder,
                'use_shortcuts' => $use_shortcuts
            ));
        } else {
            self::is_add_update_screen();
        }

        if($hook == "media-new.php") {
            if(self::is_for_this_post_type('attachment') || self::is_for_this_post_type('media')) {
                wp_enqueue_style( 'folders-media', WCP_PRO_FOLDER_URL . 'assets/css/media.css' );
                $is_active = 1;
                $folders = -1;

                $hasStars = self::check_for_setting("has_stars", "general");
                $hasChild = self::check_for_setting("has_child", "general");
                $hasChild = empty($hasChild)?0:1;
                $hasStars = empty($hasStars)?0:1;

                wp_enqueue_script('wcp-folders-add-new-media', plugin_dir_url(dirname(__FILE__)) . 'assets/js/new-media.min.js', array('jquery'), WCP_PRO_FOLDER_VERSION);
                wp_localize_script( 'wcp-folders-add-new-media', 'folders_media_options', array(
                    'terms'     => self::get_terms_hierarchical('media_folder'),
                    'taxonomy'  => get_taxonomy('media_folder'),
                    'ajax_url'  => admin_url("admin-ajax.php"),
                    'activate_url'  => $this->getRegisterKeyURL(),
                    'nonce'     => wp_create_nonce('wcp_folder_nonce_attachment'),
                    'is_key_active' => $is_active,
                    'folders' => $folders,
                    'hasStars' => $hasStars,
                    'hasChildren' => $hasChild
                ));
            }
        }
    }

    public function plugin_action_links($links)
    {
        array_unshift($links, '<a href="' . admin_url("admin.php?page=wcp_folders_settings") . '" >' . esc_html__('Settings', 'folders') . '</a>');
        $links['need_help'] = '<a target="_blank" href="https://premio.io/help/folders/?utm_source=pluginspage" >'.__( 'Need help?', 'folders').'</a>';
        return $links;
    }

    public static function get_instance()
    {
        if (empty(self::$instance)) {
            /* Do not change Class name here */
            self::$instance = new WCP_Pro_Folders();
        }
        return self::$instance;
    }

    public function check_and_set_post_type() {
        $options = get_option('folders_settings');
        $old_plugin_status = 0;
        $post_array = array();
        if (!empty($options) && is_array($options)) {
            foreach ($options as $key=>$val) {
                if (!(strpos($key, 'folders4') === false) && $old_plugin_status == 0) {
                    $old_plugin_status = 1;
                }
                if (in_array($key, array("folders4page", "folders4post", "folders4attachment"))) {
                    $post_array[] = str_replace("folders4", "", $key);
                }
            }
        } else {
            $post_array = array("page", "post", "attachment");
        }
        if ($old_plugin_status == 1) {
            update_option("folders_show_in_menu", "on");
            $old_plugin_var = get_option("folder_old_plugin_status");
            if (empty($old_plugin_var) || $old_plugin_var == null) {
                update_option("folder_old_plugin_status", "1");
            }
            update_option('folders_settings', $post_array);
            self::set_default_values_if_not_exists();
        }
        if (!empty($post_array) && get_option('folders_settings') === false) {
            update_option('folders_settings', $post_array);
            update_option("folders_show_in_menu", "off");
        }

        /* only in PRO, Removing Free version on activation */
        $DS = DIRECTORY_SEPARATOR;
        $dirName = ABSPATH . "wp-content{$DS}plugins{$DS}folders{$DS}";
        if(is_dir($dirName)) {
            if (is_plugin_active("folders/folders.php")) {
                deactivate_plugins("folders/folders.php");
            }
            self::delete_directory($dirName);
        }
    }

    public static function activate()
    {
        /* only in PRO, Removing Free version on activation */
        premio_folders_pro_plugin_check_for_setting();
        update_option("folder_redirect_status", 1);
        add_option("folders_pro_is_in_process", 1);
        $DS = DIRECTORY_SEPARATOR;
        $dirName = ABSPATH . "wp-content{$DS}plugins{$DS}folders{$DS}";
        if(is_dir($dirName)) {
            if (is_plugin_active("folders/folders.php")) {
                deactivate_plugins("folders/folders.php");
            }
        }
        delete_option("folders_pro_is_in_process");


    }

    public static function deactivate() {
        $customize_folders = get_option('customize_folders');
        if(isset($customize_folders['remove_folders_when_removed']) && $customize_folders['remove_folders_when_removed'] == "on") {
            self::$folders = 0;
            self::remove_folder_by_taxonomy("media_folder");
            self::remove_folder_by_taxonomy("folder");
            self::remove_folder_by_taxonomy("post_folder");
            $post_types = get_post_types(array(), 'objects');
            $post_array = array("page", "post", "attachment");
            foreach ($post_types as $post_type) {
                if (!in_array($post_type->name, $post_array)) {
                    self::remove_folder_by_taxonomy($post_type->name . '_folder');
                }
            }
            delete_option('customize_folders');
            delete_option('default_folders');
            delete_option('folders_show_in_menu');
            delete_option('folder_redirect_status');
            delete_option('folders_settings');
            delete_option('premio_folder_options');
            delete_option('folders_settings_updated');
        }
    }

    public static function get_ttl_fldrs()
    {
        return self::ttl_fldrs();
    }

    function folders_register_settings()
    {
        register_setting('folders_settings', 'folders_settings1', 'folders_settings_validate');
        register_setting('default_folders', 'default_folders');
        register_setting('customize_folders', 'customize_folders');

        self::check_and_set_post_type();

        /* check for trash */
        if(isset($_REQUEST['mode']) && $_REQUEST['mode'] == "list" && isset($_REQUEST['attachment-filter']) && $_REQUEST['attachment-filter'] == "trash") {
            if(function_exists('get_current_user_id')) {
                $mode = get_user_option('media_library_mode', get_current_user_id()) ? get_user_option('media_library_mode', get_current_user_id()) : 'grid';
                $mode_default = get_user_option("media_library_mode_default", get_current_user_id());
                if($mode_default === false || empty($mode_default)) {
                    update_user_option( get_current_user_id(), 'media_library_mode_default', $mode);
                }
            }
        }

        $option = get_option("folder_redirect_status");
        if ($option == 1) {
            update_option("folder_redirect_status", 2);
            wp_redirect($this->getRegisterKeyURL());
            exit;
        }
    }

    function getRegisterKeyURL() {
        $customize_folders = get_option("customize_folders");
        if(isset($customize_folders['show_folder_in_settings']) && $customize_folders['show_folder_in_settings'] == "yes") {
            return admin_url("options-general.php?page=wcp_folders_settings&setting_page=license-key");
        } else {
            return admin_url("admin.php?page=wcp_folders_register");
        }
    }

    function getFolderSettingsURL() {
        $customize_folders = get_option("customize_folders");
        if(isset($customize_folders['show_folder_in_settings']) && $customize_folders['show_folder_in_settings'] == "yes") {
            return admin_url("options-general.php?page=wcp_folders_settings");
        } else {
            return admin_url("admin.php?page=wcp_folders_settings");
        }
    }

    function isFoldersInSettings() {
        $customize_folders = get_option("customize_folders");
        if (isset($customize_folders['show_folder_in_settings']) && $customize_folders['show_folder_in_settings'] == "yes") {
            return true;
        }
        return false;
    }

    function wcp_manage_columns_head($defaults, $d = "")
    {
        global $typenow;
        if(($typenow == "attachment" || $typenow == "media") && (isset($_REQUEST['attachment-filter']) && $_REQUEST['attachment-filter'] == "trash")) {
            return $defaults;
        }
        global $typenow;
        $type = $typenow;
        if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'inline-save') {
            $type = self::sanitize_options($_REQUEST['post_type']);
        }

        $options = get_option("folders_settings");
        if (is_array($options) && in_array($type, $options)) {
            $columns = array(
                    'wcp_move' => '<div class="wcp-move-multiple wcp-col" title="' .  esc_html__('Move selected items', 'folders') . '"><span class="dashicons dashicons-move"></span><div class="wcp-items"></div></div>',
                ) + $defaults;
            return $columns;
        }
        return $defaults;
    }

    function wcp_manage_columns_content($column_name, $post_ID)
    {
        $postIDs = self::$postIds;
        if(!is_array($postIDs)) {
            $postIDs = array();
        }
        if(!in_array($post_ID, $postIDs)) {
            $postIDs[] = $post_ID;
            self::$postIds = $postIDs;
            global $typenow;
            $type = $typenow;
            if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'inline-save') {
                $type = self::sanitize_options($_REQUEST['post_type']);
            }

            $options = get_option("folders_settings");
            if (is_array($options) && in_array($type, $options)) {
                if ($column_name == 'wcp_move') {
                    $title = get_the_title();
                    if (strlen($title) > 20) {
                        $title = substr($title, 0, 20) . "...";
                    }
                    echo "<div class='wcp-move-file' data-id='{$post_ID}'><span class='wcp-move dashicons dashicons-move' data-id='{$post_ID}'></span><span class='wcp-item' data-object-id='{$post_ID}'>" . $title . "</span></div>";
                }
            }
        }
    }

    function taxonomy_archive_exclude_children($query)
    {
        $options = get_option("folders_settings");
        if (!empty($options)) {
            $taxonomy_slugs = array();
            foreach ($options as $option) {
                $taxonomy_slugs[] = self::get_custom_post_type($option);
            }
            if (!empty($taxonomy_slugs)) {
                $i = 0;
                foreach ($query->tax_query->queries as $tax_query_item) {
                    if (empty($taxonomy_slugs) || (isset($tax_query_item['taxonomy']) && in_array($tax_query_item['taxonomy'], $taxonomy_slugs))) {
                        $query->tax_query->queries[$i]['include_children'] = 0;
                    }
                }
            }
        }
    }

    public function admin_menu() {
        $customize_folders = get_option("customize_folders");
        if (isset($customize_folders['show_folder_in_settings']) && $customize_folders['show_folder_in_settings'] == "yes") {
            add_options_page(
                esc_html__('Folders Settings', 'folders'),
                esc_html__('Folders Settings', 'folders'),
                'manage_options',
                'wcp_folders_settings',
                array($this, 'wcp_folders_settings')
            );

            $show_in_page = !isset($customize_folders['folders_media_cleaning'])?"yes":$customize_folders['folders_media_cleaning'];

            if($show_in_page == "yes") {
                add_submenu_page(
                    "upload.php",
                    esc_html__('Media Cleaning', 'folders'),
                    esc_html__('Media Cleaning', 'folders'),
                    'manage_options',
                    'folders-media-cleaning',
                    array($this, 'wcp_folders_media_cleaning')
                );
            }
        } else {
            $menu_slug = 'wcp_folders_settings';

            // Add menu item for settings page
            $page_title =  esc_html__('Folders', 'folders');
            $menu_title =  esc_html__('Folders Settings', 'folders');
            $capability = 'manage_options';
            $callback = array($this, "wcp_folders_settings");
            $icon_url = 'dashicons-category';
            $position = 99;
            add_menu_page($page_title, $menu_title, $capability, $menu_slug, $callback, $icon_url, $position);

            $getData = filter_input_array(INPUT_GET);
            if(isset($getData['hide_folder_recommended_plugin']) && isset($getData['nonce'])) {
                if(current_user_can('manage_options')) {
                    $nonce = $getData['nonce'];
                    if(wp_verify_nonce($nonce, "folder_recommended_plugin")) {
                        update_option('hide_folder_recommended_plugin',"1");
                    }
                }
            }

            $show_in_page = !isset($customize_folders['folders_media_cleaning'])?"yes":$customize_folders['folders_media_cleaning'];

            if($show_in_page == "yes") {
                add_submenu_page(
                    "upload.php",
                    esc_html__('Media Cleaning', 'folders'),
                    esc_html__('Media Cleaning', 'folders'),
                    'manage_options',
                    'folders-media-cleaning',
                    array($this, 'wcp_folders_media_cleaning')
                );
            }

            $recommended_plugin = get_option("hide_folder_recommended_plugin");
            if($recommended_plugin === false) {
                add_submenu_page(
                    $menu_slug,
                    esc_html__('Recommended Plugins', 'folders'),
                    esc_html__('Recommended Plugins', 'folders'),
                    'manage_options',
                    'recommended-folder-plugins',
                    array($this, 'recommended_plugins')
                );
            }

            /* Do not Change Free/Pro Change for menu */
            add_submenu_page(
                $menu_slug,
                 esc_html__('License Key', 'folders'),
                 esc_html__('License Key', 'folders'),
                'manage_options',
                'wcp_folders_register',
                array($this, 'wcp_folders_register_or_register')
            );
        }

        self::check_and_set_post_type();

        $show_menu = get_option("folders_show_in_menu", true);
        if ($show_menu == "on") {
            self::create_menu_for_folders();
        }
    }

    public function theme_options() {

    }

    public function wcp_folders_media_cleaning(){
        include_once dirname(dirname(__FILE__)) . "/templates/admin/media-cleaning.php";
    }

    public function recommended_plugins() {
        include_once dirname(dirname(__FILE__)) . "/templates/admin/recommended-plugins.php";
    }

    public function wcp_folders_register_or_register()
    {
        self::set_default_values_if_not_exists();
        include_once dirname(dirname(__FILE__)) . "/templates/admin/upgrade-to-pro.php";
    }

    public function wcp_folders_settings($page) {
        self::set_default_values_if_not_exists();
        /* Only in Free, Get Folders update confirmation popup */
        $options = get_option('folders_settings');
        $options = (empty($options) || !is_array($options)) ?array():$options;
        $post_types = get_post_types( array(), 'objects' );
        $terms_data = array();
        foreach ($post_types as $post_type) {
            if(in_array($post_type->name, $options)) {
                $term = $post_type->name;
                $term = self::get_custom_post_type($term);
                $categories = self::get_terms_hierarchical($term);
                $terms_data[$post_type->name] = $categories;
            } else {
                $terms_data[$post_type->name] = array();
            }
        }
        $fonts = self::get_font_list();

        $plugins = new WCP_Pro_Folder_Plugins();
        $plugin_info = $plugins->get_plugin_information();
        $is_plugin_exists = $plugins->is_exists;
        $settingURL = $this->getFolderSettingsURL();
        $setting_page = isset($_GET['setting_page'])?$_GET['setting_page']:"folder-settings";
        $setting_page = in_array($setting_page, array("folder-settings", "customize-folders", "folders-import", "license-key", "folders-by-user"))?$setting_page:"folder-settings";
        $isInSettings = $this->isFoldersInSettings();
        $hasValidKey = $this->check_has_valid_key();
        include_once dirname(dirname(__FILE__)) . "/templates/admin/general-settings.php";
    }

    public static function get_font_list(){
        return array(
            // System fonts.
            'Default' => 'Default',
            "System Stack" => 'Default',
            'Arial' => 'Default',
            'Tahoma' => 'Default',
            'Verdana' => 'Default',
            'Helvetica' => 'Default',
            'Times New Roman' => 'Default',
            'Trebuchet MS' => 'Default',
            'Georgia' => 'Default',

            // Google Fonts (last update: 23/10/2018).
            'ABeeZee' => 'Google Fonts',
            'Abel' => 'Google Fonts',
            'Abhaya Libre' => 'Google Fonts',
            'Abril Fatface' => 'Google Fonts',
            'Aclonica' => 'Google Fonts',
            'Acme' => 'Google Fonts',
            'Actor' => 'Google Fonts',
            'Adamina' => 'Google Fonts',
            'Advent Pro' => 'Google Fonts',
            'Aguafina Script' => 'Google Fonts',
            'Akronim' => 'Google Fonts',
            'Aladin' => 'Google Fonts',
            'Aldrich' => 'Google Fonts',
            'Alef' => 'Google Fonts',
            'Alef Hebrew' => 'Google Fonts', // Hack for Google Early Access.
            'Alegreya' => 'Google Fonts',
            'Alegreya SC' => 'Google Fonts',
            'Alegreya Sans' => 'Google Fonts',
            'Alegreya Sans SC' => 'Google Fonts',
            'Alex Brush' => 'Google Fonts',
            'Alfa Slab One' => 'Google Fonts',
            'Alice' => 'Google Fonts',
            'Alike' => 'Google Fonts',
            'Alike Angular' => 'Google Fonts',
            'Allan' => 'Google Fonts',
            'Allerta' => 'Google Fonts',
            'Allerta Stencil' => 'Google Fonts',
            'Allura' => 'Google Fonts',
            'Almendra' => 'Google Fonts',
            'Almendra Display' => 'Google Fonts',
            'Almendra SC' => 'Google Fonts',
            'Amarante' => 'Google Fonts',
            'Amaranth' => 'Google Fonts',
            'Amatic SC' => 'Google Fonts',
            'Amethysta' => 'Google Fonts',
            'Amiko' => 'Google Fonts',
            'Amiri' => 'Google Fonts',
            'Amita' => 'Google Fonts',
            'Anaheim' => 'Google Fonts',
            'Andada' => 'Google Fonts',
            'Andika' => 'Google Fonts',
            'Angkor' => 'Google Fonts',
            'Annie Use Your Telescope' => 'Google Fonts',
            'Anonymous Pro' => 'Google Fonts',
            'Antic' => 'Google Fonts',
            'Antic Didone' => 'Google Fonts',
            'Antic Slab' => 'Google Fonts',
            'Anton' => 'Google Fonts',
            'Arapey' => 'Google Fonts',
            'Arbutus' => 'Google Fonts',
            'Arbutus Slab' => 'Google Fonts',
            'Architects Daughter' => 'Google Fonts',
            'Archivo' => 'Google Fonts',
            'Archivo Black' => 'Google Fonts',
            'Archivo Narrow' => 'Google Fonts',
            'Aref Ruqaa' => 'Google Fonts',
            'Arima Madurai' => 'Google Fonts',
            'Arimo' => 'Google Fonts',
            'Arizonia' => 'Google Fonts',
            'Armata' => 'Google Fonts',
            'Arsenal' => 'Google Fonts',
            'Artifika' => 'Google Fonts',
            'Arvo' => 'Google Fonts',
            'Arya' => 'Google Fonts',
            'Asap' => 'Google Fonts',
            'Asap Condensed' => 'Google Fonts',
            'Asar' => 'Google Fonts',
            'Asset' => 'Google Fonts',
            'Assistant' => 'Google Fonts',
            'Astloch' => 'Google Fonts',
            'Asul' => 'Google Fonts',
            'Athiti' => 'Google Fonts',
            'Atma' => 'Google Fonts',
            'Atomic Age' => 'Google Fonts',
            'Aubrey' => 'Google Fonts',
            'Audiowide' => 'Google Fonts',
            'Autour One' => 'Google Fonts',
            'Average' => 'Google Fonts',
            'Average Sans' => 'Google Fonts',
            'Averia Gruesa Libre' => 'Google Fonts',
            'Averia Libre' => 'Google Fonts',
            'Averia Sans Libre' => 'Google Fonts',
            'Averia Serif Libre' => 'Google Fonts',
            'Bad Script' => 'Google Fonts',
            'Bahiana' => 'Google Fonts',
            'Bai Jamjuree' => 'Google Fonts',
            'Baloo' => 'Google Fonts',
            'Baloo Bhai' => 'Google Fonts',
            'Baloo Bhaijaan' => 'Google Fonts',
            'Baloo Bhaina' => 'Google Fonts',
            'Baloo Chettan' => 'Google Fonts',
            'Baloo Da' => 'Google Fonts',
            'Baloo Paaji' => 'Google Fonts',
            'Baloo Tamma' => 'Google Fonts',
            'Baloo Tammudu' => 'Google Fonts',
            'Baloo Thambi' => 'Google Fonts',
            'Balthazar' => 'Google Fonts',
            'Bangers' => 'Google Fonts',
            'Barlow' => 'Google Fonts',
            'Barlow Condensed' => 'Google Fonts',
            'Barlow Semi Condensed' => 'Google Fonts',
            'Barrio' => 'Google Fonts',
            'Basic' => 'Google Fonts',
            'Battambang' => 'Google Fonts',
            'Baumans' => 'Google Fonts',
            'Bayon' => 'Google Fonts',
            'Belgrano' => 'Google Fonts',
            'Bellefair' => 'Google Fonts',
            'Belleza' => 'Google Fonts',
            'BenchNine' => 'Google Fonts',
            'Bentham' => 'Google Fonts',
            'Berkshire Swash' => 'Google Fonts',
            'Bevan' => 'Google Fonts',
            'Bigelow Rules' => 'Google Fonts',
            'Bigshot One' => 'Google Fonts',
            'Bilbo' => 'Google Fonts',
            'Bilbo Swash Caps' => 'Google Fonts',
            'BioRhyme' => 'Google Fonts',
            'BioRhyme Expanded' => 'Google Fonts',
            'Biryani' => 'Google Fonts',
            'Bitter' => 'Google Fonts',
            'Black And White Picture' => 'Google Fonts',
            'Black Han Sans' => 'Google Fonts',
            'Black Ops One' => 'Google Fonts',
            'Bokor' => 'Google Fonts',
            'Bonbon' => 'Google Fonts',
            'Boogaloo' => 'Google Fonts',
            'Bowlby One' => 'Google Fonts',
            'Bowlby One SC' => 'Google Fonts',
            'Brawler' => 'Google Fonts',
            'Bree Serif' => 'Google Fonts',
            'Bubblegum Sans' => 'Google Fonts',
            'Bubbler One' => 'Google Fonts',
            'Buda' => 'Google Fonts',
            'Buenard' => 'Google Fonts',
            'Bungee' => 'Google Fonts',
            'Bungee Hairline' => 'Google Fonts',
            'Bungee Inline' => 'Google Fonts',
            'Bungee Outline' => 'Google Fonts',
            'Bungee Shade' => 'Google Fonts',
            'Butcherman' => 'Google Fonts',
            'Butterfly Kids' => 'Google Fonts',
            'Cabin' => 'Google Fonts',
            'Cabin Condensed' => 'Google Fonts',
            'Cabin Sketch' => 'Google Fonts',
            'Caesar Dressing' => 'Google Fonts',
            'Cagliostro' => 'Google Fonts',
            'Cairo' => 'Google Fonts',
            'Calligraffitti' => 'Google Fonts',
            'Cambay' => 'Google Fonts',
            'Cambo' => 'Google Fonts',
            'Candal' => 'Google Fonts',
            'Cantarell' => 'Google Fonts',
            'Cantata One' => 'Google Fonts',
            'Cantora One' => 'Google Fonts',
            'Capriola' => 'Google Fonts',
            'Cardo' => 'Google Fonts',
            'Carme' => 'Google Fonts',
            'Carrois Gothic' => 'Google Fonts',
            'Carrois Gothic SC' => 'Google Fonts',
            'Carter One' => 'Google Fonts',
            'Catamaran' => 'Google Fonts',
            'Caudex' => 'Google Fonts',
            'Caveat' => 'Google Fonts',
            'Caveat Brush' => 'Google Fonts',
            'Cedarville Cursive' => 'Google Fonts',
            'Ceviche One' => 'Google Fonts',
            'Chakra Petch' => 'Google Fonts',
            'Changa' => 'Google Fonts',
            'Changa One' => 'Google Fonts',
            'Chango' => 'Google Fonts',
            'Charmonman' => 'Google Fonts',
            'Chathura' => 'Google Fonts',
            'Chau Philomene One' => 'Google Fonts',
            'Chela One' => 'Google Fonts',
            'Chelsea Market' => 'Google Fonts',
            'Chenla' => 'Google Fonts',
            'Cherry Cream Soda' => 'Google Fonts',
            'Cherry Swash' => 'Google Fonts',
            'Chewy' => 'Google Fonts',
            'Chicle' => 'Google Fonts',
            'Chivo' => 'Google Fonts',
            'Chonburi' => 'Google Fonts',
            'Cinzel' => 'Google Fonts',
            'Cinzel Decorative' => 'Google Fonts',
            'Clicker Script' => 'Google Fonts',
            'Coda' => 'Google Fonts',
            'Coda Caption' => 'Google Fonts',
            'Codystar' => 'Google Fonts',
            'Coiny' => 'Google Fonts',
            'Combo' => 'Google Fonts',
            'Comfortaa' => 'Google Fonts',
            'Coming Soon' => 'Google Fonts',
            'Concert One' => 'Google Fonts',
            'Condiment' => 'Google Fonts',
            'Content' => 'Google Fonts',
            'Contrail One' => 'Google Fonts',
            'Convergence' => 'Google Fonts',
            'Cookie' => 'Google Fonts',
            'Copse' => 'Google Fonts',
            'Corben' => 'Google Fonts',
            'Cormorant' => 'Google Fonts',
            'Cormorant Garamond' => 'Google Fonts',
            'Cormorant Infant' => 'Google Fonts',
            'Cormorant SC' => 'Google Fonts',
            'Cormorant Unicase' => 'Google Fonts',
            'Cormorant Upright' => 'Google Fonts',
            'Courgette' => 'Google Fonts',
            'Cousine' => 'Google Fonts',
            'Coustard' => 'Google Fonts',
            'Covered By Your Grace' => 'Google Fonts',
            'Crafty Girls' => 'Google Fonts',
            'Creepster' => 'Google Fonts',
            'Crete Round' => 'Google Fonts',
            'Crimson Text' => 'Google Fonts',
            'Croissant One' => 'Google Fonts',
            'Crushed' => 'Google Fonts',
            'Cuprum' => 'Google Fonts',
            'Cute Font' => 'Google Fonts',
            'Cutive' => 'Google Fonts',
            'Cutive Mono' => 'Google Fonts',
            'Damion' => 'Google Fonts',
            'Dancing Script' => 'Google Fonts',
            'Dangrek' => 'Google Fonts',
            'David Libre' => 'Google Fonts',
            'Dawning of a New Day' => 'Google Fonts',
            'Days One' => 'Google Fonts',
            'Dekko' => 'Google Fonts',
            'Delius' => 'Google Fonts',
            'Delius Swash Caps' => 'Google Fonts',
            'Delius Unicase' => 'Google Fonts',
            'Della Respira' => 'Google Fonts',
            'Denk One' => 'Google Fonts',
            'Devonshire' => 'Google Fonts',
            'Dhurjati' => 'Google Fonts',
            'Didact Gothic' => 'Google Fonts',
            'Diplomata' => 'Google Fonts',
            'Diplomata SC' => 'Google Fonts',
            'Do Hyeon' => 'Google Fonts',
            'Dokdo' => 'Google Fonts',
            'Domine' => 'Google Fonts',
            'Donegal One' => 'Google Fonts',
            'Doppio One' => 'Google Fonts',
            'Dorsa' => 'Google Fonts',
            'Dosis' => 'Google Fonts',
            'Dr Sugiyama' => 'Google Fonts',
            'Droid Arabic Kufi' => 'Google Fonts', // Hack for Google Early Access.
            'Droid Arabic Naskh' => 'Google Fonts', // Hack for Google Early Access.
            'Duru Sans' => 'Google Fonts',
            'Dynalight' => 'Google Fonts',
            'EB Garamond' => 'Google Fonts',
            'Eagle Lake' => 'Google Fonts',
            'East Sea Dokdo' => 'Google Fonts',
            'Eater' => 'Google Fonts',
            'Economica' => 'Google Fonts',
            'Eczar' => 'Google Fonts',
            'El Messiri' => 'Google Fonts',
            'Electrolize' => 'Google Fonts',
            'Elsie' => 'Google Fonts',
            'Elsie Swash Caps' => 'Google Fonts',
            'Emblema One' => 'Google Fonts',
            'Emilys Candy' => 'Google Fonts',
            'Encode Sans' => 'Google Fonts',
            'Encode Sans Condensed' => 'Google Fonts',
            'Encode Sans Expanded' => 'Google Fonts',
            'Encode Sans Semi Condensed' => 'Google Fonts',
            'Encode Sans Semi Expanded' => 'Google Fonts',
            'Engagement' => 'Google Fonts',
            'Englebert' => 'Google Fonts',
            'Enriqueta' => 'Google Fonts',
            'Erica One' => 'Google Fonts',
            'Esteban' => 'Google Fonts',
            'Euphoria Script' => 'Google Fonts',
            'Ewert' => 'Google Fonts',
            'Exo' => 'Google Fonts',
            'Exo 2' => 'Google Fonts',
            'Expletus Sans' => 'Google Fonts',
            'Fahkwang' => 'Google Fonts',
            'Fanwood Text' => 'Google Fonts',
            'Farsan' => 'Google Fonts',
            'Fascinate' => 'Google Fonts',
            'Fascinate Inline' => 'Google Fonts',
            'Faster One' => 'Google Fonts',
            'Fasthand' => 'Google Fonts',
            'Fauna One' => 'Google Fonts',
            'Faustina' => 'Google Fonts',
            'Federant' => 'Google Fonts',
            'Federo' => 'Google Fonts',
            'Felipa' => 'Google Fonts',
            'Fenix' => 'Google Fonts',
            'Finger Paint' => 'Google Fonts',
            'Fira Mono' => 'Google Fonts',
            'Fira Sans' => 'Google Fonts',
            'Fira Sans Condensed' => 'Google Fonts',
            'Fira Sans Extra Condensed' => 'Google Fonts',
            'Fjalla One' => 'Google Fonts',
            'Fjord One' => 'Google Fonts',
            'Flamenco' => 'Google Fonts',
            'Flavors' => 'Google Fonts',
            'Fondamento' => 'Google Fonts',
            'Fontdiner Swanky' => 'Google Fonts',
            'Forum' => 'Google Fonts',
            'Francois One' => 'Google Fonts',
            'Frank Ruhl Libre' => 'Google Fonts',
            'Freckle Face' => 'Google Fonts',
            'Fredericka the Great' => 'Google Fonts',
            'Fredoka One' => 'Google Fonts',
            'Freehand' => 'Google Fonts',
            'Fresca' => 'Google Fonts',
            'Frijole' => 'Google Fonts',
            'Fruktur' => 'Google Fonts',
            'Fugaz One' => 'Google Fonts',
            'GFS Didot' => 'Google Fonts',
            'GFS Neohellenic' => 'Google Fonts',
            'Gabriela' => 'Google Fonts',
            'Gaegu' => 'Google Fonts',
            'Gafata' => 'Google Fonts',
            'Galada' => 'Google Fonts',
            'Galdeano' => 'Google Fonts',
            'Galindo' => 'Google Fonts',
            'Gamja Flower' => 'Google Fonts',
            'Gentium Basic' => 'Google Fonts',
            'Gentium Book Basic' => 'Google Fonts',
            'Geo' => 'Google Fonts',
            'Geostar' => 'Google Fonts',
            'Geostar Fill' => 'Google Fonts',
            'Germania One' => 'Google Fonts',
            'Gidugu' => 'Google Fonts',
            'Gilda Display' => 'Google Fonts',
            'Give You Glory' => 'Google Fonts',
            'Glass Antiqua' => 'Google Fonts',
            'Glegoo' => 'Google Fonts',
            'Gloria Hallelujah' => 'Google Fonts',
            'Goblin One' => 'Google Fonts',
            'Gochi Hand' => 'Google Fonts',
            'Gorditas' => 'Google Fonts',
            'Gothic A1' => 'Google Fonts',
            'Goudy Bookletter 1911' => 'Google Fonts',
            'Graduate' => 'Google Fonts',
            'Grand Hotel' => 'Google Fonts',
            'Gravitas One' => 'Google Fonts',
            'Great Vibes' => 'Google Fonts',
            'Griffy' => 'Google Fonts',
            'Gruppo' => 'Google Fonts',
            'Gudea' => 'Google Fonts',
            'Gugi' => 'Google Fonts',
            'Gurajada' => 'Google Fonts',
            'Habibi' => 'Google Fonts',
            'Halant' => 'Google Fonts',
            'Hammersmith One' => 'Google Fonts',
            'Hanalei' => 'Google Fonts',
            'Hanalei Fill' => 'Google Fonts',
            'Handlee' => 'Google Fonts',
            'Hanuman' => 'Google Fonts',
            'Happy Monkey' => 'Google Fonts',
            'Harmattan' => 'Google Fonts',
            'Headland One' => 'Google Fonts',
            'Heebo' => 'Google Fonts',
            'Henny Penny' => 'Google Fonts',
            'Herr Von Muellerhoff' => 'Google Fonts',
            'Hi Melody' => 'Google Fonts',
            'Hind' => 'Google Fonts',
            'Hind Guntur' => 'Google Fonts',
            'Hind Madurai' => 'Google Fonts',
            'Hind Siliguri' => 'Google Fonts',
            'Hind Vadodara' => 'Google Fonts',
            'Holtwood One SC' => 'Google Fonts',
            'Homemade Apple' => 'Google Fonts',
            'Homenaje' => 'Google Fonts',
            'IBM Plex Mono' => 'Google Fonts',
            'IBM Plex Sans' => 'Google Fonts',
            'IBM Plex Sans Condensed' => 'Google Fonts',
            'IBM Plex Serif' => 'Google Fonts',
            'IM Fell DW Pica' => 'Google Fonts',
            'IM Fell DW Pica SC' => 'Google Fonts',
            'IM Fell Double Pica' => 'Google Fonts',
            'IM Fell Double Pica SC' => 'Google Fonts',
            'IM Fell English' => 'Google Fonts',
            'IM Fell English SC' => 'Google Fonts',
            'IM Fell French Canon' => 'Google Fonts',
            'IM Fell French Canon SC' => 'Google Fonts',
            'IM Fell Great Primer' => 'Google Fonts',
            'IM Fell Great Primer SC' => 'Google Fonts',
            'Iceberg' => 'Google Fonts',
            'Iceland' => 'Google Fonts',
            'Imprima' => 'Google Fonts',
            'Inconsolata' => 'Google Fonts',
            'Inder' => 'Google Fonts',
            'Indie Flower' => 'Google Fonts',
            'Inika' => 'Google Fonts',
            'Inknut Antiqua' => 'Google Fonts',
            'Irish Grover' => 'Google Fonts',
            'Istok Web' => 'Google Fonts',
            'Italiana' => 'Google Fonts',
            'Italianno' => 'Google Fonts',
            'Itim' => 'Google Fonts',
            'Jacques Francois' => 'Google Fonts',
            'Jacques Francois Shadow' => 'Google Fonts',
            'Jaldi' => 'Google Fonts',
            'Jim Nightshade' => 'Google Fonts',
            'Jockey One' => 'Google Fonts',
            'Jolly Lodger' => 'Google Fonts',
            'Jomhuria' => 'Google Fonts',
            'Josefin Sans' => 'Google Fonts',
            'Josefin Slab' => 'Google Fonts',
            'Joti One' => 'Google Fonts',
            'Jua' => 'Google Fonts',
            'Judson' => 'Google Fonts',
            'Julee' => 'Google Fonts',
            'Julius Sans One' => 'Google Fonts',
            'Junge' => 'Google Fonts',
            'Jura' => 'Google Fonts',
            'Just Another Hand' => 'Google Fonts',
            'Just Me Again Down Here' => 'Google Fonts',
            'K2D' => 'Google Fonts',
            'Kadwa' => 'Google Fonts',
            'Kalam' => 'Google Fonts',
            'Kameron' => 'Google Fonts',
            'Kanit' => 'Google Fonts',
            'Kantumruy' => 'Google Fonts',
            'Karla' => 'Google Fonts',
            'Karma' => 'Google Fonts',
            'Katibeh' => 'Google Fonts',
            'Kaushan Script' => 'Google Fonts',
            'Kavivanar' => 'Google Fonts',
            'Kavoon' => 'Google Fonts',
            'Kdam Thmor' => 'Google Fonts',
            'Keania One' => 'Google Fonts',
            'Kelly Slab' => 'Google Fonts',
            'Kenia' => 'Google Fonts',
            'Khand' => 'Google Fonts',
            'Khmer' => 'Google Fonts',
            'Khula' => 'Google Fonts',
            'Kirang Haerang' => 'Google Fonts',
            'Kite One' => 'Google Fonts',
            'Knewave' => 'Google Fonts',
            'KoHo' => 'Google Fonts',
            'Kodchasan' => 'Google Fonts',
            'Kosugi' => 'Google Fonts',
            'Kosugi Maru' => 'Google Fonts',
            'Kotta One' => 'Google Fonts',
            'Koulen' => 'Google Fonts',
            'Kranky' => 'Google Fonts',
            'Kreon' => 'Google Fonts',
            'Kristi' => 'Google Fonts',
            'Krona One' => 'Google Fonts',
            'Krub' => 'Google Fonts',
            'Kumar One' => 'Google Fonts',
            'Kumar One Outline' => 'Google Fonts',
            'Kurale' => 'Google Fonts',
            'La Belle Aurore' => 'Google Fonts',
            'Laila' => 'Google Fonts',
            'Lakki Reddy' => 'Google Fonts',
            'Lalezar' => 'Google Fonts',
            'Lancelot' => 'Google Fonts',
            'Lateef' => 'Google Fonts',
            'Lato' => 'Google Fonts',
            'League Script' => 'Google Fonts',
            'Leckerli One' => 'Google Fonts',
            'Ledger' => 'Google Fonts',
            'Lekton' => 'Google Fonts',
            'Lemon' => 'Google Fonts',
            'Lemonada' => 'Google Fonts',
            'Libre Barcode 128' => 'Google Fonts',
            'Libre Barcode 128 Text' => 'Google Fonts',
            'Libre Barcode 39' => 'Google Fonts',
            'Libre Barcode 39 Extended' => 'Google Fonts',
            'Libre Barcode 39 Extended Text' => 'Google Fonts',
            'Libre Barcode 39 Text' => 'Google Fonts',
            'Libre Baskerville' => 'Google Fonts',
            'Libre Franklin' => 'Google Fonts',
            'Life Savers' => 'Google Fonts',
            'Lilita One' => 'Google Fonts',
            'Lily Script One' => 'Google Fonts',
            'Limelight' => 'Google Fonts',
            'Linden Hill' => 'Google Fonts',
            'Lobster' => 'Google Fonts',
            'Lobster Two' => 'Google Fonts',
            'Londrina Outline' => 'Google Fonts',
            'Londrina Shadow' => 'Google Fonts',
            'Londrina Sketch' => 'Google Fonts',
            'Londrina Solid' => 'Google Fonts',
            'Lora' => 'Google Fonts',
            'Love Ya Like A Sister' => 'Google Fonts',
            'Loved by the King' => 'Google Fonts',
            'Lovers Quarrel' => 'Google Fonts',
            'Luckiest Guy' => 'Google Fonts',
            'Lusitana' => 'Google Fonts',
            'Lustria' => 'Google Fonts',
            'M PLUS 1p' => 'Google Fonts',
            'M PLUS Rounded 1c' => 'Google Fonts',
            'Macondo' => 'Google Fonts',
            'Macondo Swash Caps' => 'Google Fonts',
            'Mada' => 'Google Fonts',
            'Magra' => 'Google Fonts',
            'Maiden Orange' => 'Google Fonts',
            'Maitree' => 'Google Fonts',
            'Mako' => 'Google Fonts',
            'Mali' => 'Google Fonts',
            'Mallanna' => 'Google Fonts',
            'Mandali' => 'Google Fonts',
            'Manuale' => 'Google Fonts',
            'Marcellus' => 'Google Fonts',
            'Marcellus SC' => 'Google Fonts',
            'Marck Script' => 'Google Fonts',
            'Margarine' => 'Google Fonts',
            'Markazi Text' => 'Google Fonts',
            'Marko One' => 'Google Fonts',
            'Marmelad' => 'Google Fonts',
            'Martel' => 'Google Fonts',
            'Martel Sans' => 'Google Fonts',
            'Marvel' => 'Google Fonts',
            'Mate' => 'Google Fonts',
            'Mate SC' => 'Google Fonts',
            'Maven Pro' => 'Google Fonts',
            'McLaren' => 'Google Fonts',
            'Meddon' => 'Google Fonts',
            'MedievalSharp' => 'Google Fonts',
            'Medula One' => 'Google Fonts',
            'Meera Inimai' => 'Google Fonts',
            'Megrim' => 'Google Fonts',
            'Meie Script' => 'Google Fonts',
            'Merienda' => 'Google Fonts',
            'Merienda One' => 'Google Fonts',
            'Merriweather' => 'Google Fonts',
            'Merriweather Sans' => 'Google Fonts',
            'Metal' => 'Google Fonts',
            'Metal Mania' => 'Google Fonts',
            'Metamorphous' => 'Google Fonts',
            'Metrophobic' => 'Google Fonts',
            'Michroma' => 'Google Fonts',
            'Milonga' => 'Google Fonts',
            'Miltonian' => 'Google Fonts',
            'Miltonian Tattoo' => 'Google Fonts',
            'Mina' => 'Google Fonts',
            'Miniver' => 'Google Fonts',
            'Miriam Libre' => 'Google Fonts',
            'Mirza' => 'Google Fonts',
            'Miss Fajardose' => 'Google Fonts',
            'Mitr' => 'Google Fonts',
            'Modak' => 'Google Fonts',
            'Modern Antiqua' => 'Google Fonts',
            'Mogra' => 'Google Fonts',
            'Molengo' => 'Google Fonts',
            'Molle' => 'Google Fonts',
            'Monda' => 'Google Fonts',
            'Monofett' => 'Google Fonts',
            'Monoton' => 'Google Fonts',
            'Monsieur La Doulaise' => 'Google Fonts',
            'Montaga' => 'Google Fonts',
            'Montez' => 'Google Fonts',
            'Montserrat' => 'Google Fonts',
            'Montserrat Alternates' => 'Google Fonts',
            'Montserrat Subrayada' => 'Google Fonts',
            'Moul' => 'Google Fonts',
            'Moulpali' => 'Google Fonts',
            'Mountains of Christmas' => 'Google Fonts',
            'Mouse Memoirs' => 'Google Fonts',
            'Mr Bedfort' => 'Google Fonts',
            'Mr Dafoe' => 'Google Fonts',
            'Mr De Haviland' => 'Google Fonts',
            'Mrs Saint Delafield' => 'Google Fonts',
            'Mrs Sheppards' => 'Google Fonts',
            'Mukta' => 'Google Fonts',
            'Mukta Mahee' => 'Google Fonts',
            'Mukta Malar' => 'Google Fonts',
            'Mukta Vaani' => 'Google Fonts',
            'Muli' => 'Google Fonts',
            'Mystery Quest' => 'Google Fonts',
            'NTR' => 'Google Fonts',
            'Nanum Brush Script' => 'Google Fonts',
            'Nanum Gothic' => 'Google Fonts',
            'Nanum Gothic Coding' => 'Google Fonts',
            'Nanum Myeongjo' => 'Google Fonts',
            'Nanum Pen Script' => 'Google Fonts',
            'Neucha' => 'Google Fonts',
            'Neuton' => 'Google Fonts',
            'New Rocker' => 'Google Fonts',
            'News Cycle' => 'Google Fonts',
            'Niconne' => 'Google Fonts',
            'Niramit' => 'Google Fonts',
            'Nixie One' => 'Google Fonts',
            'Nobile' => 'Google Fonts',
            'Nokora' => 'Google Fonts',
            'Norican' => 'Google Fonts',
            'Nosifer' => 'Google Fonts',
            'Notable' => 'Google Fonts',
            'Nothing You Could Do' => 'Google Fonts',
            'Noticia Text' => 'Google Fonts',
            'Noto Kufi Arabic' => 'Google Fonts', // Hack for Google Early Access.
            'Noto Naskh Arabic' => 'Google Fonts', // Hack for Google Early Access.
            'Noto Sans' => 'Google Fonts',
            'Noto Sans Hebrew' => 'Google Fonts', // Hack for Google Early Access.
            'Noto Sans JP' => 'Google Fonts',
            'Noto Sans KR' => 'Google Fonts',
            'Noto Serif' => 'Google Fonts',
            'Noto Serif JP' => 'Google Fonts',
            'Noto Serif KR' => 'Google Fonts',
            'Nova Cut' => 'Google Fonts',
            'Nova Flat' => 'Google Fonts',
            'Nova Mono' => 'Google Fonts',
            'Nova Oval' => 'Google Fonts',
            'Nova Round' => 'Google Fonts',
            'Nova Script' => 'Google Fonts',
            'Nova Slim' => 'Google Fonts',
            'Nova Square' => 'Google Fonts',
            'Numans' => 'Google Fonts',
            'Nunito' => 'Google Fonts',
            'Nunito Sans' => 'Google Fonts',
            'Odor Mean Chey' => 'Google Fonts',
            'Offside' => 'Google Fonts',
            'Old Standard TT' => 'Google Fonts',
            'Oldenburg' => 'Google Fonts',
            'Oleo Script' => 'Google Fonts',
            'Oleo Script Swash Caps' => 'Google Fonts',
            'Open Sans' => 'Google Fonts',
            'Open Sans Condensed' => 'Google Fonts',
            'Open Sans Hebrew' => 'Google Fonts', // Hack for Google Early Access.
            'Open Sans Hebrew Condensed' => 'Google Fonts', // Hack for Google Early Access.
            'Oranienbaum' => 'Google Fonts',
            'Orbitron' => 'Google Fonts',
            'Oregano' => 'Google Fonts',
            'Orienta' => 'Google Fonts',
            'Original Surfer' => 'Google Fonts',
            'Oswald' => 'Google Fonts',
            'Over the Rainbow' => 'Google Fonts',
            'Overlock' => 'Google Fonts',
            'Overlock SC' => 'Google Fonts',
            'Overpass' => 'Google Fonts',
            'Overpass Mono' => 'Google Fonts',
            'Ovo' => 'Google Fonts',
            'Oxygen' => 'Google Fonts',
            'Oxygen Mono' => 'Google Fonts',
            'PT Mono' => 'Google Fonts',
            'PT Sans' => 'Google Fonts',
            'PT Sans Caption' => 'Google Fonts',
            'PT Sans Narrow' => 'Google Fonts',
            'PT Serif' => 'Google Fonts',
            'PT Serif Caption' => 'Google Fonts',
            'Pacifico' => 'Google Fonts',
            'Padauk' => 'Google Fonts',
            'Palanquin' => 'Google Fonts',
            'Palanquin Dark' => 'Google Fonts',
            'Pangolin' => 'Google Fonts',
            'Paprika' => 'Google Fonts',
            'Parisienne' => 'Google Fonts',
            'Passero One' => 'Google Fonts',
            'Passion One' => 'Google Fonts',
            'Pathway Gothic One' => 'Google Fonts',
            'Patrick Hand' => 'Google Fonts',
            'Patrick Hand SC' => 'Google Fonts',
            'Pattaya' => 'Google Fonts',
            'Patua One' => 'Google Fonts',
            'Pavanam' => 'Google Fonts',
            'Paytone One' => 'Google Fonts',
            'Peddana' => 'Google Fonts',
            'Peralta' => 'Google Fonts',
            'Permanent Marker' => 'Google Fonts',
            'Petit Formal Script' => 'Google Fonts',
            'Petrona' => 'Google Fonts',
            'Philosopher' => 'Google Fonts',
            'Piedra' => 'Google Fonts',
            'Pinyon Script' => 'Google Fonts',
            'Pirata One' => 'Google Fonts',
            'Plaster' => 'Google Fonts',
            'Play' => 'Google Fonts',
            'Playball' => 'Google Fonts',
            'Playfair Display' => 'Google Fonts',
            'Playfair Display SC' => 'Google Fonts',
            'Podkova' => 'Google Fonts',
            'Poiret One' => 'Google Fonts',
            'Poller One' => 'Google Fonts',
            'Poly' => 'Google Fonts',
            'Pompiere' => 'Google Fonts',
            'Pontano Sans' => 'Google Fonts',
            'Poor Story' => 'Google Fonts',
            'Poppins' => 'Google Fonts',
            'Port Lligat Sans' => 'Google Fonts',
            'Port Lligat Slab' => 'Google Fonts',
            'Pragati Narrow' => 'Google Fonts',
            'Prata' => 'Google Fonts',
            'Preahvihear' => 'Google Fonts',
            'Press Start 2P' => 'Google Fonts',
            'Pridi' => 'Google Fonts',
            'Princess Sofia' => 'Google Fonts',
            'Prociono' => 'Google Fonts',
            'Prompt' => 'Google Fonts',
            'Prosto One' => 'Google Fonts',
            'Proza Libre' => 'Google Fonts',
            'Puritan' => 'Google Fonts',
            'Purple Purse' => 'Google Fonts',
            'Quando' => 'Google Fonts',
            'Quantico' => 'Google Fonts',
            'Quattrocento' => 'Google Fonts',
            'Quattrocento Sans' => 'Google Fonts',
            'Questrial' => 'Google Fonts',
            'Quicksand' => 'Google Fonts',
            'Quintessential' => 'Google Fonts',
            'Qwigley' => 'Google Fonts',
            'Racing Sans One' => 'Google Fonts',
            'Radley' => 'Google Fonts',
            'Rajdhani' => 'Google Fonts',
            'Rakkas' => 'Google Fonts',
            'Raleway' => 'Google Fonts',
            'Raleway Dots' => 'Google Fonts',
            'Ramabhadra' => 'Google Fonts',
            'Ramaraja' => 'Google Fonts',
            'Rambla' => 'Google Fonts',
            'Rammetto One' => 'Google Fonts',
            'Ranchers' => 'Google Fonts',
            'Rancho' => 'Google Fonts',
            'Ranga' => 'Google Fonts',
            'Rasa' => 'Google Fonts',
            'Rationale' => 'Google Fonts',
            'Ravi Prakash' => 'Google Fonts',
            'Redressed' => 'Google Fonts',
            'Reem Kufi' => 'Google Fonts',
            'Reenie Beanie' => 'Google Fonts',
            'Revalia' => 'Google Fonts',
            'Rhodium Libre' => 'Google Fonts',
            'Ribeye' => 'Google Fonts',
            'Ribeye Marrow' => 'Google Fonts',
            'Righteous' => 'Google Fonts',
            'Risque' => 'Google Fonts',
            'Roboto' => 'Google Fonts',
            'Roboto Condensed' => 'Google Fonts',
            'Roboto Mono' => 'Google Fonts',
            'Roboto Slab' => 'Google Fonts',
            'Rochester' => 'Google Fonts',
            'Rock Salt' => 'Google Fonts',
            'Rokkitt' => 'Google Fonts',
            'Romanesco' => 'Google Fonts',
            'Ropa Sans' => 'Google Fonts',
            'Rosario' => 'Google Fonts',
            'Rosarivo' => 'Google Fonts',
            'Rouge Script' => 'Google Fonts',
            'Rozha One' => 'Google Fonts',
            'Rubik' => 'Google Fonts',
            'Rubik Mono One' => 'Google Fonts',
            'Ruda' => 'Google Fonts',
            'Rufina' => 'Google Fonts',
            'Ruge Boogie' => 'Google Fonts',
            'Ruluko' => 'Google Fonts',
            'Rum Raisin' => 'Google Fonts',
            'Ruslan Display' => 'Google Fonts',
            'Russo One' => 'Google Fonts',
            'Ruthie' => 'Google Fonts',
            'Rye' => 'Google Fonts',
            'Sacramento' => 'Google Fonts',
            'Sahitya' => 'Google Fonts',
            'Sail' => 'Google Fonts',
            'Saira' => 'Google Fonts',
            'Saira Condensed' => 'Google Fonts',
            'Saira Extra Condensed' => 'Google Fonts',
            'Saira Semi Condensed' => 'Google Fonts',
            'Salsa' => 'Google Fonts',
            'Sanchez' => 'Google Fonts',
            'Sancreek' => 'Google Fonts',
            'Sansita' => 'Google Fonts',
            'Sarala' => 'Google Fonts',
            'Sarina' => 'Google Fonts',
            'Sarpanch' => 'Google Fonts',
            'Satisfy' => 'Google Fonts',
            'Sawarabi Gothic' => 'Google Fonts',
            'Sawarabi Mincho' => 'Google Fonts',
            'Scada' => 'Google Fonts',
            'Scheherazade' => 'Google Fonts',
            'Schoolbell' => 'Google Fonts',
            'Scope One' => 'Google Fonts',
            'Seaweed Script' => 'Google Fonts',
            'Secular One' => 'Google Fonts',
            'Sedgwick Ave' => 'Google Fonts',
            'Sedgwick Ave Display' => 'Google Fonts',
            'Sevillana' => 'Google Fonts',
            'Seymour One' => 'Google Fonts',
            'Shadows Into Light' => 'Google Fonts',
            'Shadows Into Light Two' => 'Google Fonts',
            'Shanti' => 'Google Fonts',
            'Share' => 'Google Fonts',
            'Share Tech' => 'Google Fonts',
            'Share Tech Mono' => 'Google Fonts',
            'Shojumaru' => 'Google Fonts',
            'Short Stack' => 'Google Fonts',
            'Shrikhand' => 'Google Fonts',
            'Siemreap' => 'Google Fonts',
            'Sigmar One' => 'Google Fonts',
            'Signika' => 'Google Fonts',
            'Signika Negative' => 'Google Fonts',
            'Simonetta' => 'Google Fonts',
            'Sintony' => 'Google Fonts',
            'Sirin Stencil' => 'Google Fonts',
            'Six Caps' => 'Google Fonts',
            'Skranji' => 'Google Fonts',
            'Slabo 13px' => 'Google Fonts',
            'Slabo 27px' => 'Google Fonts',
            'Slackey' => 'Google Fonts',
            'Smokum' => 'Google Fonts',
            'Smythe' => 'Google Fonts',
            'Sniglet' => 'Google Fonts',
            'Snippet' => 'Google Fonts',
            'Snowburst One' => 'Google Fonts',
            'Sofadi One' => 'Google Fonts',
            'Sofia' => 'Google Fonts',
            'Song Myung' => 'Google Fonts',
            'Sonsie One' => 'Google Fonts',
            'Sorts Mill Goudy' => 'Google Fonts',
            'Source Code Pro' => 'Google Fonts',
            'Source Sans Pro' => 'Google Fonts',
            'Source Serif Pro' => 'Google Fonts',
            'Space Mono' => 'Google Fonts',
            'Special Elite' => 'Google Fonts',
            'Spectral' => 'Google Fonts',
            'Spectral SC' => 'Google Fonts',
            'Spicy Rice' => 'Google Fonts',
            'Spinnaker' => 'Google Fonts',
            'Spirax' => 'Google Fonts',
            'Squada One' => 'Google Fonts',
            'Sree Krushnadevaraya' => 'Google Fonts',
            'Sriracha' => 'Google Fonts',
            'Srisakdi' => 'Google Fonts',
            'Stalemate' => 'Google Fonts',
            'Stalinist One' => 'Google Fonts',
            'Stardos Stencil' => 'Google Fonts',
            'Stint Ultra Condensed' => 'Google Fonts',
            'Stint Ultra Expanded' => 'Google Fonts',
            'Stoke' => 'Google Fonts',
            'Strait' => 'Google Fonts',
            'Stylish' => 'Google Fonts',
            'Sue Ellen Francisco' => 'Google Fonts',
            'Suez One' => 'Google Fonts',
            'Sumana' => 'Google Fonts',
            'Sunflower' => 'Google Fonts',
            'Sunshiney' => 'Google Fonts',
            'Supermercado One' => 'Google Fonts',
            'Sura' => 'Google Fonts',
            'Suranna' => 'Google Fonts',
            'Suravaram' => 'Google Fonts',
            'Suwannaphum' => 'Google Fonts',
            'Swanky and Moo Moo' => 'Google Fonts',
            'Syncopate' => 'Google Fonts',
            'Tajawal' => 'Google Fonts',
            'Tangerine' => 'Google Fonts',
            'Taprom' => 'Google Fonts',
            'Tauri' => 'Google Fonts',
            'Taviraj' => 'Google Fonts',
            'Teko' => 'Google Fonts',
            'Telex' => 'Google Fonts',
            'Tenali Ramakrishna' => 'Google Fonts',
            'Tenor Sans' => 'Google Fonts',
            'Text Me One' => 'Google Fonts',
            'The Girl Next Door' => 'Google Fonts',
            'Tienne' => 'Google Fonts',
            'Tillana' => 'Google Fonts',
            'Timmana' => 'Google Fonts',
            'Tinos' => 'Google Fonts',
            'Titan One' => 'Google Fonts',
            'Titillium Web' => 'Google Fonts',
            'Trade Winds' => 'Google Fonts',
            'Trirong' => 'Google Fonts',
            'Trocchi' => 'Google Fonts',
            'Trochut' => 'Google Fonts',
            'Trykker' => 'Google Fonts',
            'Tulpen One' => 'Google Fonts',
            'Ubuntu' => 'Google Fonts',
            'Ubuntu Condensed' => 'Google Fonts',
            'Ubuntu Mono' => 'Google Fonts',
            'Ultra' => 'Google Fonts',
            'Uncial Antiqua' => 'Google Fonts',
            'Underdog' => 'Google Fonts',
            'Unica One' => 'Google Fonts',
            'UnifrakturCook' => 'Google Fonts',
            'UnifrakturMaguntia' => 'Google Fonts',
            'Unkempt' => 'Google Fonts',
            'Unlock' => 'Google Fonts',
            'Unna' => 'Google Fonts',
            'VT323' => 'Google Fonts',
            'Vampiro One' => 'Google Fonts',
            'Varela' => 'Google Fonts',
            'Varela Round' => 'Google Fonts',
            'Vast Shadow' => 'Google Fonts',
            'Vesper Libre' => 'Google Fonts',
            'Vibur' => 'Google Fonts',
            'Vidaloka' => 'Google Fonts',
            'Viga' => 'Google Fonts',
            'Voces' => 'Google Fonts',
            'Volkhov' => 'Google Fonts',
            'Vollkorn' => 'Google Fonts',
            'Vollkorn SC' => 'Google Fonts',
            'Voltaire' => 'Google Fonts',
            'Waiting for the Sunrise' => 'Google Fonts',
            'Wallpoet' => 'Google Fonts',
            'Walter Turncoat' => 'Google Fonts',
            'Warnes' => 'Google Fonts',
            'Wellfleet' => 'Google Fonts',
            'Wendy One' => 'Google Fonts',
            'Wire One' => 'Google Fonts',
            'Work Sans' => 'Google Fonts',
            'Yanone Kaffeesatz' => 'Google Fonts',
            'Yantramanav' => 'Google Fonts',
            'Yatra One' => 'Google Fonts',
            'Yellowtail' => 'Google Fonts',
            'Yeon Sung' => 'Google Fonts',
            'Yeseva One' => 'Google Fonts',
            'Yesteryear' => 'Google Fonts',
            'Yrsa' => 'Google Fonts',
            'Zeyada' => 'Google Fonts',
            'Zilla Slab' => 'Google Fonts',
            'Zilla Slab Highlight' => 'Google Fonts',
        );
    }

    public function set_default_values_if_not_exists()
    {

        if(is_admin() || (defined('DOING_AJAX') && DOING_AJAX)) {
            $options = get_option('folders_settings');
            $options = empty($options) || !is_array($options) ? array() : $options;
            foreach ($options as $option) {
                $post_type = self::get_custom_post_type($option);
                $terms = get_terms($post_type, array(
                        'hide_empty' => false,
                        'meta_query' => array(
                            array(
                                'key'       => 'wcp_custom_order',
                                'compare'   => 'NOT EXISTS'
                            )
                        )
                    )
                );
                if (!empty($terms)) {
                    foreach ($terms as $term) {
                        $order = get_term_meta($term->term_id, "wcp_custom_order", true);
                        if (empty($order) || $order == null) {
                            update_term_meta($term->term_id, "wcp_custom_order", "1");
                        }
                    }
                }
            }
            add_option("is_folder_checked_for_old_data", 1);
        }
    }

    /* Free and Pro major changes */
    public function check_has_valid_key()
    {
        $license_data = self::get_license_key_data();
        $valid = 0;
        if (!empty($license_data)) {
            if (!empty($license_data)) {
                if ($license_data['license'] == "valid") {
                    $valid = 1;
                } else if ($license_data['license'] == "expired") {
                    $valid = 1;
                }
            }
        }
        return $valid;
    }

    public function wcp_admin_notice()
    {
        if (self::is_active_for_screen()) {
            $url = $this->getRegisterKeyURL();
            $license_data = self::get_license_key_data();
            if (empty($license_data)) {
                ?>
                <div class="notice notice-warning is-dismissible">
                    <p><?php esc_attr_e("To receive updates, Please provide your valid", 'folders')?> <a href='<?php echo esc_url($url) ?>'><?php esc_attr_e("license key", 'folders') ?></a></p>
                </div>
                <?php
            } else {
                if ($license_data['license'] == "expired") {
                    ?>
                    <div class="notice notice-error is-dismissible">
                        <p><?php esc_attr_e("Your license key has been expired, Please renew your ", 'folders') ?><a href='<?php echo esc_url($url) ?>'>license key</a><?php esc_attr_e(" to receive updates.", 'folders') ?></p>
                    </div>
                    <?php
                }
            }
        }
    }

    public static function get_license_key_information($licenseKey)
    {
        if ($licenseKey == "") {
            return array();
        }

        $api_params = array(
            'edd_action' => 'check_license',
            'license' => $licenseKey,
            'item_id' => WCP_PRO_FOLDER_PR0DUCT_ID,
            'url' => site_url()
        );

        /* Request to premio.io for checking Licence key */
        $response = wp_safe_remote_post(WCP_PRO_FOLDER_API_URL, array('body' => $api_params, 'timeout' => 15, 'sslverify' => true));

        if (is_wp_error($response)) {
            $response = wp_safe_remote_post(WCP_PRO_FOLDER_API_URL, array('body' => $api_params, 'timeout' => 15, 'sslverify' => false));
        }

        if (is_wp_error($response)) {
            return array();                                                     // return empty array if error in response
        } else {
            $response = json_decode(wp_remote_retrieve_body($response), true);  // return response

            return $response;
        }
    }

    public static function get_license_key_data($licenseKey = '')
    {
        if (self::$license_key_data == null) {
            $license_data = get_transient("folder_license_key_data");
            if(!empty($license_data)) {
                self::$license_key_data = $license_data;
                return self::$license_key_data;
            }
            if ($licenseKey == '') {
                $licenseKey = get_option("wcp_folder_license_key");
            }
            $license_data = self::get_license_key_information($licenseKey);
            if(!empty($license_data)) {
                set_transient("folder_license_key_data", $license_data, DAY_IN_SECONDS);
            }
            self::$license_key_data = $license_data;
        }
        return self::$license_key_data;
    }

    public function check_for_license_key()
    {
        $license_data = self::get_license_key_data();
        if (!empty($license_data)) {
            if ($license_data['license'] == "valid" || $license_data['license'] == "expired") {
                return true;
            }
        }
        return false;
    }

    public function wcp_folder_activate_key()
    {
        if (current_user_can('manage_options')) {
            $response = array();
            $response['status'] = 0;
            $response['error'] = 0;
            $response['data'] = array();
            $response['message'] = "";
            $postData = filter_input_array(INPUT_POST);
            $response['status'] = "";
            if(!isset($postData['key']) || empty($postData['key'])) {
                $response['status'] = "error";
            } else if(!isset($postData['nonce']) || empty($postData['nonce'])) {
                $response['status'] = "error";
            } else if(!wp_verify_nonce($postData['nonce'], 'activate_folder_key')) {
                $response['status'] = "error";
            }
            if ($response['status'] != 'error') {
                $licenseKey = $postData['key'];
                update_option("wcp_folder_license_key", "");
                $license_data = self::activate_license_key($licenseKey);
                if (!empty($license_data)) {
                    if ($license_data['license'] == 'valid') {
                        $response['status'] = "valid";
                        update_option("wcp_folder_license_key", $licenseKey);
                    } else if ($license_data['license'] == 'invalid' && $license_data['error'] == 'expired') {
                        $response['status'] = "expired";
                        update_option("wcp_folder_license_key", $licenseKey);
                    } else if ($license_data['license'] == 'invalid' && $license_data['error'] == 'no_activations_left') {
                        $response['status'] = "no_activations";
                        update_option("wcp_folder_license_key", "");                     // set license key = blank if it is not valid or expired
                    } else {
                        update_option("wcp_folder_license_key", "");
                        $response['status'] = "error";
                    }
                } else {
                    update_option("wcp_folder_license_key", "");
                    $response['status'] = "error";
                }
            }
            echo esc_attr($response['status']);
            wp_die();
        }
    }

    public function wcp_folder_deactivate_key()
    {
        if (current_user_can('manage_options')) {
            $response = array();
            $response['status'] = 0;
            $response['error'] = 0;
            $response['data'] = array();
            $response['message'] = "";
            $response['status'] = "";
            $postData = filter_input_array(INPUT_POST);
            if(!isset($postData['nonce']) || empty($postData['nonce'])) {
                $response['status'] = "error";
            } else if(!wp_verify_nonce($postData['nonce'], 'deactivate_folder_key')) {
                $response['status'] = "error";
            }
            if ($response['status'] != 'error') {
                $response['status'] = "error";
                $licenseKey = get_option("wcp_folder_license_key");
                if (!empty($licenseKey)) {
                    $license_data = self::deactivate_license_key($licenseKey);
                    delete_transient("folder_license_key_data");
                    if (!empty($license_data)) {
                        if ($license_data['license'] == 'deactivated') {
                            $response['status'] = "unactivated";
                            update_option("wcp_folder_license_key", "");
                        } else if ($license_data['license'] == 'expired') {
                            $response['status'] = "expired";
                            update_option("wcp_folder_license_key", "");
                        } else {
                            update_option("wcp_folder_license_key", "");
                            $response['status'] = "error";
                        }
                    }
                    update_option("wcp_folder_license_key", "");
                }
            }
            echo esc_attr($response['status']);
            wp_die();
        }
    }

    public function deactivate_license_key($licenseKey)
    {
        if ($licenseKey == "") {
            return array();
        }

        $api_params = array(
            'edd_action' => 'deactivate_license',
            'license' => $licenseKey,
            'item_id' => WCP_PRO_FOLDER_PR0DUCT_ID,
            'url' => site_url()
        );

        /* Request to premio.io for key deactivation */
        $response = wp_safe_remote_post(WCP_PRO_FOLDER_API_URL, array('body' => $api_params, 'timeout' => 15, 'sslverify' => true));

        if (is_wp_error($response)) {
            $response = wp_safe_remote_post(WCP_PRO_FOLDER_API_URL, array('body' => $api_params, 'timeout' => 15, 'sslverify' => false));
        }

        if (is_wp_error($response)) {
            return array();                                                     // return empty array if error in response
        } else {
            $response = json_decode(wp_remote_retrieve_body($response), true);  // return response
            return $response;
        }
    }

    public function activate_license_key($licenseKey)
    {
        if ($licenseKey == "") {
            return array();
        }

        $api_params = array(
            'edd_action' => 'activate_license',
            'license' => $licenseKey,
            'item_id' => WCP_PRO_FOLDER_PR0DUCT_ID,
            'url' => site_url()
        );

        /* Request to premio.io for key activation */
        $response = wp_safe_remote_post(WCP_PRO_FOLDER_API_URL, array('body' => $api_params, 'timeout' => 15, 'sslverify' => true));

        if (is_wp_error($response)) {
            $response = wp_safe_remote_post(WCP_PRO_FOLDER_API_URL, array('body' => $api_params, 'timeout' => 15, 'sslverify' => false));
        }

        if (is_wp_error($response)) {
            return array();                                                     // return empty array if error in response
        } else {
            $response = json_decode(wp_remote_retrieve_body($response), true);  // return response
            return $response;
        }
    }

    public function wcp_folder_check_for_valid_key()
    {
        $response = array();
        $response['status'] = 1;
        $response['error'] = 0;
        $response['data'] = array();
        $response['message'] = "";
        $valid = 0;
        $is_expired = 0;
        $folders = 0;
        $license_data = self::get_license_key_data();
        if (!empty($license_data)) {
            if (!empty($license_data)) {
                if ($license_data['license'] == "valid") {
                    $valid = 1;
                } else if ($license_data['license'] == "expired") {
                    $valid = 1;
                    $is_expired = 1;
                }
            }
        }
        if ($valid == 0) {
            $post_types = get_option('folders_settings');
            $total = 0;
            foreach ($post_types as $post_type) {
                self::get_custom_post_type($post_type);
                $total += wp_count_terms($post_type);
            }
            $folders = $total;
        }
        $response['data'] = array(
            'is_expired' => $is_expired,
            'is_valid' => $valid,
            'folders' => $folders
        );
        echo json_encode($response);
        wp_die();
    }

    public function delete_directory($dirname) {
        if (is_dir($dirname))
            $dir_handle = opendir($dirname);
        if (!$dir_handle)
            return false;
        while($file = readdir($dir_handle)) {
            if ($file != "." && $file != "..") {
                if (!is_dir($dirname."/".$file))
                    unlink($dirname."/".$file);
                else
                    self::delete_directory($dirname.'/'.$file);
            }
        }
        closedir($dir_handle);
        rmdir($dirname);
        return true;
    }

    public function filter_string_polyfill(string $string): string
    {
        $str = preg_replace('/\x00|<[^>]*>?/', '', $string);
        return str_replace(["'", '"'], ['&#39;', '&#34;'], $str);
    }

    public function download_folder() {
        if (current_user_can('manage_options') || current_user_can("edit_pages") || current_user_can("edit_posts")) {
            $folder_id = isset($_GET['folder_id'])?$_GET['folder_id']:"";
            $folder_id = $this->filter_string_polyfill($folder_id);

            $nonce = isset($_GET['nonce'])?$_GET['nonce']:"";
            $nonce = $this->filter_string_polyfill($nonce);

            $action = isset($_GET['action'])?$_GET['action']:"";
            $action = $this->filter_string_polyfill($action);
            if (is_admin() && $action == "download-folders" && wp_verify_nonce($nonce, 'wcp_folder_term_' . $folder_id)) {

                $folder_type = self::get_custom_post_type("attachment");

                $term = get_term_by('term_id', $folder_id, $folder_type);

                if (!empty($term)) {
                    $posts = get_posts(array(
                        'post_type' => 'attachment',
                        'numberposts' => -1,
                        'tax_query' => array(
                            array(
                                'taxonomy' => $folder_type,
                                'field' => 'term_id',
                                'terms' => $folder_id,
                                'include_children' => false
                            )
                        )
                    ));
                    $file_array = array();
                    if (!empty($posts)) {
                        foreach ($posts as $post) {
                            $file_array[] = get_attached_file($post->ID);
                        }
                    }

                    if (!empty($file_array)) {

                        $zip_file = $term->name . ".zip";

                        $folder_path = wp_upload_dir();
                        $zip_path = $folder_path['basedir'] . DIRECTORY_SEPARATOR . $zip_file;

                        $zip = new ZipArchive();

                        if ($zip->open($zip_path, ZipArchive::CREATE) !== TRUE) {
                            exit("cannot open <$zip_file>\n");
                        }

                        foreach ($file_array as $file) {
                            if (file_exists($file)) {
                                $zip->addFromString(basename($file), file_get_contents($file));
                            }
                        }

                        $zip->close();

                        if (file_exists($zip_path)) {
                            header('Content-Type: application/zip');
                            header('Content-Disposition: attachment; filename="' . basename($zip_path) . '"');
                            header('Content-Length: ' . filesize($zip_path));
                            readfile($zip_path);
                            unlink($zip_path);
                            exit;
                        }
                    }
                } else {
                    wp_die("Folder does not exists");
                }
            }
        }
    }
}
