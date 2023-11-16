<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class Premio_Pro_Folders_Dynamic_Folders {
    var $post_type;
    var $taxonomy;
    var $has_dynamic_folders = 0;

    public function __construct() {
        $customize_folders = get_option('customize_folders');
        $this->has_dynamic_folders = (!isset($customize_folders['dynamic_folders']) || (isset($customize_folders['dynamic_folders']) && $customize_folders['dynamic_folders'] == "on"))?1:0;

        if($this->has_dynamic_folders) {
            if(isset($customize_folders['dynamic_folders_for_admin_only']) && $customize_folders['dynamic_folders_for_admin_only'] == "on") {
                if(function_exists("wp_get_current_user")) {
                    $user = wp_get_current_user();
                    $user_roles = (array)$user->roles;
                    $user_roles = !is_array($user_roles) ? array() : $user_roles;
                    if (!in_array("administrator", $user_roles)) {
                        $this->has_dynamic_folders = 0;
                    }
                }
            }
        }

        if($this->has_dynamic_folders) {
            add_action('pre_get_posts', array($this, 'pre_get_posts'));
        }
    }

    public function pre_get_posts($query) {
        $post_type = isset($_REQUEST['post_type'])?$_REQUEST['post_type']:"";
        $dynamic_type = isset($_REQUEST['ajax_action']) && ($_REQUEST['ajax_action'] == "premio_dynamic_folders")?1:0;
        $dynamic_folder = isset($_REQUEST['dynamic_folder']) && !empty($_REQUEST['dynamic_folder'])?$_REQUEST['dynamic_folder']:0;
        if(!empty($post_type) && !empty($dynamic_type) && $dynamic_folder) {
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
                    if($filter_value != "all") {
                        $category__in = array();
                        $category__in[] = $filter_value;
                        $query->query_vars['category__in'] = $category__in;
                    }
                }
                else if($filter_type == "extensions") {
                    if($filter_value != "all") {
                        $extension = $this->get_file_ext_dynamic_folders("attachment", "", "a");
                        if (isset($extension[$filter_value])) {
                            $query->query_vars['post_mime_type'] = $extension[$filter_value];
                        }
                    }
                }
            }
        }
        return $query;
    }

    public function get_post_category_dynamic_folders($post_type, $taxonomy) {
        if($post_type != 'post') {
            return "";
        }

        $string = "";

        if(!$this->has_dynamic_folders) {
            return $string;
        }

        $string .= "<li id='post_category-all' data-slug='dates-all' >" .esc_html__("All Categories", "folders");
        $string .= "<ul>";
        $string .= $this->get_category_list(0);
        $string .= "</ul>";
        $string .= "</li>";
        return $string;
    }

    public function get_category_list($cat = 0) {
        $next = get_categories('hide_empty=true&orderby=name&order=ASC&parent=' . $cat);
        $string = "";
        if(!$this->has_dynamic_folders) {
            return $string;
        }
        if( $next ) :
            foreach( $next as $cat ) :
                $string .= "<li id='post_category-{$cat->term_id}'>".$cat->name;
                $string .= "<ul>";
                $string .= $this->get_category_list($cat->term_id);
                $string .= "</ul>";
                $string .= "</li>";
            endforeach;
        endif;

        return $string;
    }

    public function get_file_ext_dynamic_folders($post_type, $taxonomy, $return = "s") {

        if(!$this->has_dynamic_folders) {
            if($return == "a") {
                return array();
            }
            return "";
        }

        global $wpdb;

        if ( 'attachment' == $post_type ) {
            $results = $wpdb->get_results( "SELECT DISTINCT(post_mime_type) FROM {$wpdb->posts} WHERE post_mime_type != '' ORDER BY post_mime_type ASC" );
        } else {
            if($return == "a") {
                return array();
            }
            return "";
        }

        $extList = array();
        if(!empty($results)) {
            foreach ($results as $row) {
                if(!empty($row->post_mime_type)) {
                    $mime_type = strtolower($row->post_mime_type);
                    $mime_type = explode("/", $mime_type);
                    $mime_type = array_pop($mime_type);
                    $extList[$mime_type] = $row->post_mime_type;
                }
            }
        }

        if($return == "a") {
            return $extList;
        } else {
            $string = "";
            $string .= "<li id='extensions-all' data-slug='dates-all' >" .esc_html__("All Extensions", "folders");
            $string .= "<ul>";
            foreach($extList as $key=>$value) {
                $string .= "<li id='extensions-".$key."'>";
                $string .= ".".$key;
                $string .= "</li>";
            }
            $string .= "</ul>";
            $string .= "</li>";
            return $string;
        }
    }

    public function get_date_dynamic_folders($post_type, $taxonomy, $return = "s") {
        global $wpdb;

        if(!$this->has_dynamic_folders) {
            if($return == "a") {
                return array();
            }
            return "";
        }

        $cache_key 	= array();
        $years 		= array();
        $folders 	= array();

        if ( 'attachment' == $post_type ) {
            $results = $wpdb->get_results( "SELECT post_date FROM {$wpdb->posts} WHERE post_type = 'attachment' ORDER BY post_date ASC" );
        } else {
            $results = $wpdb->get_results( $wpdb->prepare( "SELECT post_date FROM {$wpdb->posts} WHERE post_type = %s AND post_status NOT IN ('trash', 'auto-draft') ORDER BY post_date ASC", $post_type ) );
        }

        foreach ( $results as $row ) {

            // Skip blank dates
            if ( '0000-00-00 00:00:00' == $row->post_date ) continue;

            $timezone = wp_timezone();

            $date = new DateTime( $row->post_date, $timezone );

            $year 	= $date->format( 'Y' );
            $month 	= $date->format( 'm' );
            $day 	= $date->format( 'd' );

            //$dates[ $year ][ $month ][ $day ] = array();
            if ( ! isset( $years[ $year ] ) ) {
                $years[ $year ] = array(
                    'year' 		=> $year,
                    'name' 		=> $year,
                    'months' 	=> array(),
                );
            }

            if ( ! isset( $years[ $year ]['months'][ $month ] ) ) {
                $years[ $year ]['months'][ $month ] = array(
                    'month' => $month,
                    'name' 	=> $date->format( 'F' ),
                    'days' 	=> array(),
                );
            }

            if ( ! isset( $years[ $year ]['months'][ $month ]['days'][ $day ] ) ) {
                $years[ $year ]['months'][ $month ]['days'][ $day ] = array(
                    'day' 	=> $day,
                    'name' 	=> $date->format( 'j' ),
                );
            }
        }


        $string = "";

        if(!empty($results)) {
            $string .= "<li id='dates-all' data-slug='dates-all' >" .esc_html__("All Dates", "folders");
            $string .= "<ul>";
            $folders[] = array(
                'name' =>esc_html__("All Dates", "folders"),
                'value' => 'dates-all'
            );
            // Create our folders
            foreach ($years as $year) {
                $year_id = $year['year'];
                $string .= "<li id='year-" . $year['year'] . "'>";
                $string .= $year['name'];
                $folders[] = array(
                    'name' => "- ".$year['name'],
                    'value' => 'year-'.$year['year']
                );
                if (isset($year['months']) && !empty($year['months'])) {
                    $string .= "<ul>";
                    foreach ($year['months'] as $month) {
                        $month_id = $month['month'];
                        $string .= "<li id='month-" . $year_id . "-" . $month['month'] . "'>";
                        $string .= $month['name'];
                        $folders[] = array(
                            'name' => "-- ".$month['name'],
                            'value' => 'month-'.$year_id."-".$month['month']
                        );
                        if (isset($month['days']) && !empty($month['days'])) {
                            $string .= "<ul>";
                            foreach ($month['days'] as $days) {
                                $string .= "<li id='day-" . $year_id . "-" . $month_id . "-" . $days['day'] . "'>";
                                $string .= $days['name'];
                                $string .= "</li>";
                                $folders[] = array(
                                    'name' => "--- ".$days['name'],
                                    'value' => 'day-'.$year_id."-".$month_id."-".$days['day']
                                );
                            }
                            $string .= "</ul>";
                        }
                        $string .= "</li>";
                    }
                    $string .= "</ul>";
                }
                $string .= "</li>";
            }
            $string .= "</ul>";
            $string .= "</li>";
        }

        if($return == "s") {
            return $string;
        } else {
            return $folders;
        }
    }

    public function get_author_dynamic_folders( $post_type, $taxonomy, $return = "s" ) {

        if(!$this->has_dynamic_folders) {
            if($return == "a") {
                return array();
            }
            return "";
        }

        global $wpdb;

        $folders = array();

        // Fetch authors
        $results = $wpdb->get_results( $wpdb->prepare( "SELECT DISTINCT u.ID, u.display_name FROM {$wpdb->posts} p INNER JOIN {$wpdb->users} u ON p.post_author = u.ID AND post_status NOT IN ('trash', 'auto-draft') WHERE post_type = %s ORDER BY u.display_name ASC", $post_type ) );

        $folders[] = array(
            'name' =>esc_html__("All Authors", "folders"),
            'value' => "author-all"
        );

        $string = "";
        $string .= "<li id='author-all' data-slug='parent-all' >".__("All Authors", "folders");

        if(!empty($results)) {
            $string .= "<ul>";
            foreach ( $results as $row ) {
                $string .= "<li id='author-".$row->ID."' data-slug='parent-all' >".$row->display_name."</li>";

                $folders[] = array(
                    'name' => "- ".$row->display_name,
                    'value' => 'author-'.$row->ID
            );
            }
            $string .= "</ul>";
        }

        if($return == "s") {
            return $string;
        } else {
            return $folders;
        }

    }

    public static function timezone_identifier() {
        $timezone = wp_timezone_string();
        if(empty($timezone)) {
            return "UTC";
        }
        return $timezone;
    }

    public function get_page_hierarchy_dynamic_folders( $post_type, $taxonomy ) {
        if($post_type != 'page') {
            return ;
        }

        $string = "";
        $string .= "<li id='pages_parent-all' data-slug='dates-all' >" .esc_html__("All Hierarchy", "folders");
        $string .= "<ul>";
        $string .= $this->get_root_hierarchy();
        $string .= "</ul>";
        $string .= "</li>";
        return $string;
    }

    public function get_root_hierarchy_array() {
        global $wpdb;
        $post_table = $wpdb->posts;

        $query = "SELECT DISTINCT(P.ID), P.post_title 
            FROM {$post_table} AS P 
            INNER JOIN {$post_table} AS PC ON PC.post_parent = P.ID 
            WHERE PC.post_parent != 0 AND P.post_type = 'page' AND PC.post_type = 'page' AND P.post_parent = 0 AND PC.post_status != 'trash' AND P.post_status != 'trash'";
        $results = $wpdb->get_results($query);

        return $results;
    }

    public function get_root_hierarchy() {

        $results = $this->get_root_hierarchy_array();

        $string = "";
        foreach ($results as $row) {
            $string .= "<li id='pages_parent-{$row->ID}' >" .$row->post_title;
            $string .= "<ul>";
            $string .= $this->get_page_hierarchy($row->ID);
            $string .= "</ul>";
            $string .= "</li>";
        }
        return $string;
    }

    public function get_child_pages_array($parent_id = 0) {
        global $wpdb;
        $post_table = $wpdb->posts;

        $query = "SELECT DISTINCT(P.ID), P.post_title 
            FROM {$post_table} AS P 
            WHERE P.post_parent = {$parent_id} AND P.post_type = 'page' AND P.post_status != 'trash'";

        $results = $wpdb->get_results($query);
        return $results;
    }

    public function get_page_hierarchy_array($parent_id = 0) {
        global $wpdb;
        $post_table = $wpdb->posts;

        $query = "SELECT DISTINCT(P.ID), P.post_title 
            FROM {$post_table} AS P 
            INNER JOIN {$post_table} AS PC ON PC.post_parent = P.ID 
            WHERE P.post_parent = {$parent_id} AND P.post_type = 'page' AND PC.post_type = 'page'";

        $results = $wpdb->get_results($query);
        return $results;
    }

    public function get_page_hierarchy($parent_id = 0) {

        $results = $this->get_page_hierarchy_array($parent_id);
        $string = "";
        foreach ($results as $row) {
            $string .= "<li id='pages_parent-{$row->ID}' >" .$row->post_title;
            $string .= "<ul>";
            $string .= $this->get_page_hierarchy($row->ID);
            $string .= "</ul>";
            $string .= "</li>";
        }
        return $string;
    }
}
$Premio_Pro_Folders_Dynamic_Folders = new Premio_Pro_Folders_Dynamic_Folders();