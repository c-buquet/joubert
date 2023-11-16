<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<style>
    <?php
    $string = "";
    global $typenow;
    $width = get_option("wcp_dynamic_width_for_" . $typenow);
    $width = intval($width);
    if($width == null || empty($width) || $width > 1200) {
        $width = 280;
    }
    $width = $width - 40;
    $customize_folders = get_option('customize_folders');
    $dynamic_folders = (!isset($customize_folders['dynamic_folders']) || (isset($customize_folders['dynamic_folders']) && $customize_folders['dynamic_folders'] == "on"))?1:0;

    if($dynamic_folders) {
        if(isset($customize_folders['dynamic_folders_for_admin_only']) && $customize_folders['dynamic_folders_for_admin_only'] == "on") {
            $user_id = get_current_user_id();
            $user_meta = get_userdata($user_id);
            $user_roles = $user_meta->roles;
            $user_roles = !is_array($user_roles)?array():$user_roles;
            if(!in_array("administrator", $user_roles)) {
                $dynamic_folders = 0;
            }
        }
    }
    ?>
</style>
<style>
<?php
$font_family = "";
if(isset($customize_folders['folder_font']) && !empty($customize_folders['folder_font'])) {
    $font_family = $customize_folders['folder_font'];
    $folder_fonts = self::get_font_list();
    if(isset($folder_fonts[$font_family])) {
        if($font_family == "System Stack") {
            $font_family = "-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Oxygen-Sans,Ubuntu,Cantarell,Helvetica Neue,sans-serif";
        }
        ?>
        .wcp-container, .folder-popup-form, .dynamic-menu { font-family: <?php echo esc_attr($font_family) ?>; }
        <?php
    }
    if($font_family == "-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Oxygen-Sans,Ubuntu,Cantarell,Helvetica Neue,sans-serif") {
        $font_family = "System Stack";
    }
    if($folder_fonts[$font_family] == "Default") {
        $font_family = "";
    }
}
if(!isset($customize_folders['new_folder_color']) || empty($customize_folders['new_folder_color'])) {
$customize_folders['new_folder_color'] = "#f51366";
}
if(!isset($customize_folders['folder_bg_color']) || empty($customize_folders['folder_bg_color'])) {
    $customize_folders['folder_bg_color'] = "#f51366";
}
if(!isset($customize_folders['bulk_organize_button_color']) || empty($customize_folders['bulk_organize_button_color'])) {
    $customize_folders['bulk_organize_button_color'] = "#f51366";
}
$rgbColor = self::hexToRgb($customize_folders['folder_bg_color']);
?>
.add-new-folder { background-color: <?php echo esc_attr($customize_folders['new_folder_color']) ?>; border-color: <?php echo esc_attr($customize_folders['new_folder_color']) ?> }
.jstree-closed > i.jstree-icon, .jstree-open > i.jstree-icon { color: <?php echo esc_attr($customize_folders['folder_bg_color']) ?>; }
.wcp-hide-show-buttons .toggle-buttons { background-color: <?php echo esc_attr($customize_folders['new_folder_color']) ?>; }
.folders-toggle-button span { background-color: <?php echo esc_attr($customize_folders['new_folder_color']) ?>; }
.ui-resizable-handle.ui-resizable-e:before, .ui-resizable-handle.ui-resizable-w:before {border-color: <?php echo esc_attr($customize_folders['new_folder_color']) ?>;}
button.button.organize-button { background-color: <?php echo esc_attr($customize_folders['bulk_organize_button_color']) ?>; border-color: <?php echo esc_attr($customize_folders['bulk_organize_button_color']) ?>; }
button.button.organize-button:hover { background-color: <?php echo esc_attr($customize_folders['bulk_organize_button_color']) ?>; border-color: <?php echo esc_attr($customize_folders['bulk_organize_button_color']) ?>; }
body:not(.no-hover-css) #custom-scroll-menu .jstree-hovered:not(.jstree-clicked), body:not(.no-hover-css) #custom-scroll-menu .jstree-hovered:not(.jstree-clicked):hover, .dynamic-menu a.active, .dynamic-menu a:hover, .folder-setting-menu li a:hover { background: rgba(<?php echo esc_attr($rgbColor['r'].",".$rgbColor['g'].",".$rgbColor['b'].", 0.08") ?>) !important; color: #333333;}
body:not(.no-hover-css) #custom-scroll-menu .jstree-anchor:not(.jstree-clicked):focus { background: rgba(<?php echo esc_attr($rgbColor['r'].",".$rgbColor['g'].",".$rgbColor['b'].", 0.08") ?>) !important; color: #333333; }
body:not(.no-hover-css) #custom-scroll-menu .jstree-clicked, body:not(.no-hover-css) #custom-scroll-menu .jstree-clicked:not(.jstree-clicked):focus, #custom-scroll-menu .jstree-clicked, #custom-scroll-menu .jstree-clicked:hover { background: <?php echo esc_attr($customize_folders['folder_bg_color']) ?> !important; color: #ffffff !important; }
#custom-scroll-menu .jstree-hovered.wcp-drop-hover, #custom-scroll-menu .jstree-hovered.wcp-drop-hover:hover, #custom-scroll-menu .jstree-clicked.wcp-drop-hover, #custom-scroll-menu .jstree-clicked.wcp-drop-hover:hover, body #custom-scroll-menu  *.drag-in > a:hover { background: <?php echo esc_attr($customize_folders['folder_bg_color']) ?> !important; color: #ffffff !important; }
body #custom-scroll-menu  *.drag-in > a:hover > .pfolder-folder-close { color: #fff !important; }
.drag-bot > a {
    border-bottom: solid 2px <?php echo esc_attr($customize_folders['folder_bg_color']) ?>;
}
.drag-up > a {
    border-top: solid 2px <?php echo esc_attr($customize_folders['folder_bg_color']) ?>;
}
#custom-scroll-menu .jstree-hovered:not(.jstree-clicked) .pfolder-folder-close {
    color: <?php echo esc_attr($customize_folders['folder_bg_color']) ?>;
}
.folders-action-menu > ul > li > a:not(.disabled):hover, .folders-action-menu > ul > li > label:not(.disabled):hover {
    color: <?php echo esc_attr($customize_folders['folder_bg_color']) ?>;
}
.dynamic-menu a.active span i, .dynamic-menu a:hover span i, .dynamic-menu a.active span.dashicons, .dynamic-menu a:hover span.dashicons { color: <?php echo esc_attr($customize_folders['folder_bg_color']) ?> }
body:not(.no-hover-css) #custom-scroll-menu *.drag-in > a.jstree-hovered, body:not(.no-hover-css) #custom-scroll-menu *.drag-in > a.jstree-hovered:hover {
    background: <?php echo esc_attr($customize_folders['folder_bg_color']) ?> !important;
    color: #fff !important;
}
.orange-bg > span ,.wcp-container .route.active-item > h3.title, .header-posts a.active-item, .un-categorised-items.active-item, .sticky-folders ul li a.active-item { background-color: <?php echo esc_attr($customize_folders['folder_bg_color']) ?> !important; color: #ffffff; }
body:not(.no-hover-css) .wcp-container .route .title:hover, body:not(.no-hover-css) .header-posts a:hover, body:not(.no-hover-css) .un-categorised-items:hover, body:not(.no-hover-css) .sticky-folders ul li a:hover {background: rgba(<?php echo esc_attr($rgbColor['r'].",".$rgbColor['g'].",".$rgbColor['b'].", 0.08") ?>);}
.wcp-drop-hover {
    background: <?php echo esc_attr($customize_folders['folder_bg_color']) ?> !important;
}
#custom-menu .route .nav-icon .wcp-icon {color: <?php echo esc_attr($customize_folders['folder_bg_color']) ?> !important;}
.mCS-3d.mCSB_scrollTools .mCSB_dragger .mCSB_dragger_bar { background: <?php echo esc_attr($customize_folders['folder_bg_color']) ?> !important; }
.jstree-node.drag-in > a.jstree-anchor.jstree-hovered { background-color: <?php echo esc_attr($customize_folders['folder_bg_color']) ?> !important; color: #ffffff; }
<?php

if(isset($customize_folders['folder_size']) && !empty($customize_folders['folder_size'])) {
    if($customize_folders['folder_size'] == "custom") {
        $customize_folders['folder_size'] = ! isset( $customize_folders['folder_custom_font_size'] ) || empty( $customize_folders['folder_custom_font_size'] ) ? "16" : $customize_folders['folder_custom_font_size'];
    }
    ?>
    .wcp-container .route span.title-text, .header-posts a, .un-categorised-items a, .sticky-title, .sticky-folders > ul > li > a, .jstree-default .jstree-anchor { font-size: <?php echo esc_attr($customize_folders['folder_size']) ?>px; }
    <?php
}
?>
</style>
<?php if(!empty($font_family)) {
    wp_enqueue_style( 'custom-google-fonts', 'https://fonts.googleapis.com/css?family='.urlencode($font_family), false );
} ?>
<div id="media-css">

</div>
<?php
$optionName = $typenow."_parent_status";
$status = get_option($optionName);
global $typenow;
$title = ucfirst($typenow);
if($typenow == "page") {
    $title = "Pages";
} else if($typenow == "post") {
    $title = "Posts";
} else if($typenow == "attachment") {
    $title = "Files";
} else {
    $post_type = $typenow;
    $post_types = get_post_types( array( "name" => $post_type), 'objects' );
    if(!empty($post_types) && is_array($post_types) && isset($post_types[$post_type]) && isset($post_types[$post_type]->label)) {
        $title = $post_types[$post_type]->label;
    }
}
$display_status = "wcp_dynamic_display_status_" . $typenow;
$display_status = get_option($display_status);
$class_name = isset($display_status) && $display_status == "hide"?"hide-folders-area":"";
$active_class = (isset($display_status) && $display_status == "hide")?"":"active";
$active_class_2 = (isset($display_status) && $display_status == "hide")?"active":"";

/* Do not change here, Free/Pro Class name */
$post_type = WCP_Pro_Folders::get_custom_post_type($typenow);
$active = "";
$active_all_class = "";
if(!empty($post_type)) {
    if(isset($_REQUEST[$post_type]) && $_REQUEST[$post_type] == -1) {
        $active = "active-item";
    }

    if(!isset($_REQUEST[$post_type]) || $_REQUEST[$post_type] == "") {
        $active_all_class = "active-item";
    }
}
?>
<div id="wcp-content" class="<?php echo esc_attr(isset($display_status) && $display_status == "hide"?"hide-folders-area":"")  ?>" >
    <div id="wcp-content-resize">
        <div class="wcp-content">
            <div class="wcp-hide-show-buttons">
                <div class="toggle-buttons hide-folders <?php echo esc_attr($active_class)  ?>"><span class="dashicons dashicons-arrow-left"></span></div>
                <div class="toggle-buttons show-folders <?php echo esc_attr($active_class_2) ?>"><span class="dashicons dashicons-arrow-right"></span></div>
            </div>
            <div class='wcp-container'>
                <div class="sticky-wcp-custom-form">
                    <?php echo $form_html ?>
                    <div class="top-settings">
                        <div class="folder-search-form">
                            <div class="form-search-input">
                                <input type="text" value="" id="folder-search" autocomplete="off" />
                                <span><i class="pfolder-search"></i></span>
                            </div>
                        </div>
                        <div class="folder-separator"></div>
                        <div class="header-posts">
                            <a href="javascript:;" class="all-posts <?php echo esc_attr($active_all_class) ?>"><?php esc_attr_e("All ".$title, 'folders') ?> <span class="total-count"><?php echo $ttpsts ?></span></a>
                        </div>
                        <div class="un-categorised-items <?php echo esc_attr($active) ?>">
                            <a href="javascript:;" class="un-categorized-posts"><?php esc_attr_e("Unassigned ".$title, 'folders') ?> <span class="total-count total-empty"><?php echo $ttemp ?></span> </a>
                        </div>

                        <div class="sticky-folders">
                            <div class="sticky-title"><?php esc_html_e("Sticky Folders","folders") ?> <span class="pull-right"><i class="pfolder-pin"></i></span></div>
                            <ul>
                                <?php //echo $sticky_string ?>
                            </ul>
                        </div>
                        <div class="folder-separator-2"></div>
                        <?php $folder_status = get_option("premio_folder_sticky_status_".$typenow) ?>
                        <div class="sr-only">
                            <input type="file" name="upload_media_folder[]" disabled id="upload_media_folder" class="upload-media-action"  multiple directory="" webkitdirectory="" mozdirectory="" />
                        </div>
                        <div class="folders-action-menu">
                            <ul>
                                <li style="flex: 0 0 22px;"><a href="javascript:;" class="no-bg"><input type="checkbox" id="menu-checkbox" ></a></li>
                                <li><label for="upload_media_folder" class="folder-tooltip upload-media-action disabled" href="javascript:;" data-folder-tooltip="<?php esc_html_e("Upload Folder", "folders"); ?>" ><span class="dashicons dashicons-cloud-upload"></span></label></li>
                                <li><a class="folder-tooltip cut-folder-action disabled" href="javascript:;" data-folder-tooltip="<?php esc_html_e("Cut", "folders"); ?>"><span class="pfolder-cut"></span></a></li>
                                <li><a class="folder-tooltip copy-folder-action disabled" href="javascript:;" data-folder-tooltip="<?php esc_html_e("Copy", "folders"); ?>"><span class="pfolder-copy"></span></a></li>
                                <li><a class="folder-tooltip paste-folder-action disabled" href="javascript:;" data-folder-tooltip="<?php esc_html_e("Paste", "folders"); ?>"><span class="pfolder-paste"></span></a></li>
                                <!--<li><a class="folder-tooltip undo-folder-action disabled" href="javascript:;" data-folder-tooltip="<?php /*esc_html_e("Undo Changes", "folders"); */?>"><span class="pfolder-undo"></span></a></li>-->
                                <li class="folder-settings-btn1">
                                    <a class="lock-unlock-all-folders folder-tooltip open-folders" href="#" data-folder-tooltip="<?php esc_html_e("Lock all folders", "folders"); ?>"><span class="dashicons dashicons-lock"></span></a>
                                    <!--<div class="folder-setting-menu">
                                        <ul>
                                            <li><a href="javascript:;" id="lock-all-folder"><span class="dashicons dashicons-lock"></span> <?php /*esc_html_e("Lock all folders", "folders"); */?></a></li>
                                            <li><a href="javascript:;" id="unlock-all-folder"><span class="dashicons dashicons-unlock"></span> <?php /*esc_html_e("Unlock all folders", "folders"); */?></a></li>
                                        </ul>
                                    </div>-->
                                </li>
                                <li><a class="folder-tooltip delete-folder-action disabled" href="javascript:;" data-folder-tooltip="<?php esc_html_e("Delete", "folders"); ?>"><span class="pfolder-remove"></span></a></li>
                            </ul>
                        </div>
                        <div class="folder-separator-2"></div>
                    </div>
                </div>
                <div id="custom-scroll-menu">
                    <div class="ajax-preloader">
                        <div class="cssload-container">
                            <div class="cssload-tube-tunnel"></div>
                        </div>
                    </div>

                    <?php if($dynamic_folders) { ?>
                        <div class="dynamic-tree-data">
                            <div class="dynamic-folders" id="dynamic-tree-folders">
                                <ul class="dynamic-folders-list">
                                    <li id="dynamic-folders">
                                        <?php esc_html_e("Dynamic Folders", "folders") ?>
                                        <ul>
                                            <?php echo $authors ?>
                                            <?php echo $dates ?>
                                            <?php echo $extensions ?>
                                            <?php echo $post_categories ?>
                                            <?php echo $page_hierarchy ?>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="dynamic-tree-data-separator"></div>
                    <?php } ?>

                    <div class="js-tree-data">
                        <div id="js-tree-menu" class="<?php echo ($status==1)?"active":"" ?>">
                            <!--<div class="wcp-parent" id="title0"><i class="fa fa-folder-o"></i> All Folders</div>-->
                            <ul class='space first-space' id='space_0'>
                                <?php echo $terms_data; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="folder-add-update-content">
    <div class="folder-popup-form" id="add-update-folder">
        <div class="popup-form-content">
            <div class="popup-form-data">
                <div class="close-popup-button">
                    <a class="" href="javascript:;"><span></span></a>
                </div>
                <form action="" method="post" id="save-folder-form">
                    <div id="add-update-folder-title" class="add-update-folder-title">
                        <?php esc_html_e("Add a new folder", "folders") ?>
                    </div>
                    <div class="add-folder-note">
                        <?php esc_html_e("Enter your folder's name (or create more than one folder by separating the name with a comma)", "folders") ?>
                    </div>
                    <div class="folder-form-input">
                        <div class="folder-group">
                            <input id="add-update-folder-name" autocomplete="off" required="required">
                            <span class="highlight"></span><span class="folder-bar"></span>
                            <label for="add-update-folder-name"><?php esc_html_e("Folder name", "folders") ?></label>
                        </div>
                    </div>
                    <div class="folder-form-errors">
                        <span class="dashicons dashicons-info"></span> <?php esc_html_e("Please enter folder name", "folders") ?>
                    </div>
                    <?php if(!$hasValidKey) { ?>
                        <div class="folder-form-buttons hide-it pro-message" id="pro-notice">
                            <span class="pro-tip">
                                <?php esc_html_e("Pro tip", "folders") ?>
                            </span>
                            <div class="pro-notice">
                                <?php printf( esc_html__("%sActivate your license key%s to create subfolders (with 20+ amazing features) & premium support 🎉", "folders"), '<a class="inline-button" target="_blank" href="'.esc_url($this->getRegisterKeyURL()).'">', "</a>"); ?>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="folder-form-buttons">
                        <a href="javascript:;" class="form-cancel-btn"><?php esc_html_e("Cancel", "folders") ?></a>
                        <button type="submit" class="form-submit-btn" id="save-folder-data" style="width: 160px"><?php esc_html_e("Submit", "folders") ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="folder-popup-form" id="update-folder-item">
        <div class="popup-form-content">
            <div class="popup-form-data">
                <div class="close-popup-button">
                    <a class="" href="javascript:;"><span></span></a>
                </div>
                <form action="" method="post" id="update-folder-form">
                    <div id="update-folder-title" class="add-update-folder-title">
                        <?php esc_html_e("Rename folder", "folders") ?>
                    </div>
                    <div class="folder-form-input">
                        <div class="folder-group">
                            <input id="update-folder-item-name" autocomplete="off" required="required">
                            <span class="highlight"></span><span class="folder-bar"></span>
                            <label for="update-folder-item-name"><?php esc_html_e("Folder name", "folders") ?></label>
                        </div>
                    </div>
                    <div class="folder-form-errors">
                        <span class="dashicons dashicons-info"></span> <?php esc_html_e("Please enter folder name", "folders") ?>
                    </div>
                    <div class="folder-form-buttons">
                        <a href="javascript:;" class="form-cancel-btn"><?php esc_html_e("Cancel", "folders") ?></a>
                        <button type="submit" class="form-submit-btn" id="update-folder-data" style="width: 160px"><?php esc_html_e("Submit", "folders") ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="folder-popup-form" id="confirm-remove-folder">
        <div class="popup-form-content">
            <div class="popup-form-data">
                <div class="close-popup-button">
                    <a class="" href="javascript:;"><span></span></a>
                </div>
                <div class="add-update-folder-title" id="remove-folder-message">
                    <?php esc_html_e("Are you sure you want to delete the selected folder?", "folders") ?>
                </div>
                <div class="folder-form-message" id="remove-folder-notice">
                    <?php esc_html_e("Items in the folder will not be deleted.", "folders") ?>
                </div>
                <div class="folder-form-buttons">
                    <a href="javascript:;" class="form-cancel-btn"><?php esc_html_e("No, Keep it", "folders") ?></a>
                    <a href="javascript:;" class="form-submit-btn" id="remove-folder-item"><?php esc_html_e("Yes, Delete it!", "folders") ?></a>
                </div>
            </div>
        </div>
    </div>

    <div class="folder-popup-form" id="no-more-folder-credit">
        <div class="popup-form-content">
            <div class="popup-form-data">
                <div class="close-popup-button">
                    <a class="" href="javascript:;"><span></span></a>
                </div>
                <div class="add-update-folder-title" id="folder-limitation-message">

                </div>
                <div class="folder-form-message">
                    <?php esc_html_e("Unlock unlimited amount of folders by activating license key.", "folders") ?>
                </div>
                <div class="folder-form-buttons">
                    <a href="javascript:;" class="form-cancel-btn"><?php esc_html_e("Cancel", "folders") ?></a>
                    <a href="<?php echo esc_url($this->getRegisterKeyURL()) ?>" target="_blank" class="form-submit-btn"><?php esc_html_e("Activate License Key", "folders") ?></a>
                </div>
            </div>
        </div>
    </div>

    <div class="folder-popup-form" id="error-folder-popup">
        <div class="popup-form-content">
            <div class="popup-form-data">
                <div class="close-popup-button">
                    <a class="" href="javascript:;"><span></span></a>
                </div>
                <div class="add-update-folder-title" id="error-folder-popup-message">

                </div>
                <div class="folder-form-buttons">
                    <a href="javascript:;" class="form-cancel-btn"><?php esc_html_e("Close", "folders") ?></a>
                </div>
            </div>
        </div>
    </div>

    <div class="folder-popup-form" id="sub-folder-popup">
        <div class="popup-form-content">
            <div class="popup-form-data">
                <div class="close-popup-button">
                    <a class="" href="javascript:;"><span></span></a>
                </div>
                <div class="add-update-folder-title">
                    <?php esc_html_e("Sub-folders is a pro feature", "folders") ?>
                </div>
                <div class="folder-form-message" style="padding: 25px 10px;" >
                    <?php esc_html_e("Hey, it looks like you want to create sub-folders on Folders. Sub-folders is a premium feature. Activate your license key to create, access and organize your files with sub-folders.", "folders") ?>
                </div>
                <div class="folder-form-buttons">
                    <a href="javascript:;" class="form-cancel-btn"><?php esc_html_e("Cancel", "folders") ?></a>
                    <a href="<?php echo esc_url($this->getRegisterKeyURL()) ?>" target="_blank" class="form-submit-btn"><?php esc_html_e("Activate License Key", "folders") ?></a>
                </div>
            </div>
        </div>
    </div>

    <div class="folder-popup-form" id="confirm-your-change">
        <div class="popup-form-content">
            <div class="popup-form-data">
                <div class="close-popup-button">
                    <a class="" href="javascript:;"><span></span></a>
                </div>
                <div class="add-update-folder-title">
                    <?php esc_html_e("Confirm your change", "folders") ?>
                </div>
                <div class="folder-form-message" style="padding: 25px 10px;" >
                    Hey, it looks like you want to move the file to "Unassigned Files." Do you want to move the file from the current folder only or from all the folders where the file exists?
                </div>
                <div class="folder-form-buttons">
                    <input type="hidden" id="unassigned_folders" />
                    <a href="javascript:;" class="form-cancel-btn remove-from-all-folders" id="remove-from-all-folders"><?php esc_html_e("From all folders", "folders") ?></a>
                    <a href="javascript:;" class="form-submit-btn remove-from-current-folder" id="remove-from-current-folder"><?php esc_html_e("Just from this folder", "folders") ?></a>
                </div>
            </div>
        </div>
    </div>

    <div class="folder-popup-form" id="add-sub-folder-popup">
        <div class="popup-form-content">
            <div class="popup-form-data">
                <div class="close-popup-button">
                    <a class="" href="javascript:;"><span></span></a>
                </div>
                <div class="add-update-folder-title">
                    <?php esc_html_e("Add a new folder", "folders") ?>
                </div>
                <div class="folder-form-input">
                    <div class="folder-group">
                        <input id="update-folder-item-name" autocomplete="off" required="required" readonly>
                        <span class="highlight"></span><span class="folder-bar"></span>
                        <label for="update-folder-item-name"><?php esc_html_e("Folder name", "folders") ?></label>
                    </div>
                </div>
                <div class="folder-form-buttons">
                    <span class="pro-tip">
                        <?php esc_html_e("Pro tip", "folders") ?>
                    </span>
                    <div class="pro-notice">
                        <?php printf( esc_html__("%sActivate your license key%s to create subfolders (with 20+ amazing features) & premium support 🎉", "folders"), '<a class="inline-button" target="_blank" href="'.esc_url($this->getRegisterKeyURL()).'">', "</a>"); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="folder-popup-form" id="bulk-move-folder">
        <form action="" method="post" id="bulk-folder-form">
            <div class="popup-form-content">
                <div class="popup-form-data">
                    <div class="close-popup-button">
                        <a class="" href="javascript:;"><span></span></a>
                    </div>
                    <div class="popup-folder-title">
                        <?php esc_html_e("Select Folder", "folders") ?>
                    </div>
                    <div class="select-box">
                        <select id="bulk-select">
                            <option value=""><?php esc_html_e("Select Folder", "folders") ?></option>
                        </select>
                    </div>
                    <div class="folder-form-buttons">
                        <a href="javascript:;" class="form-cancel-btn"><?php esc_html_e("Cancel", "folders") ?></a>
                        <button type="submit" class="form-submit-btn" id="move-to-folder" style="width: 200px"><?php esc_html_e("Move to Folder", "folders") ?></button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="folders-undo-notification" id="do-undo">
        <div class="folders-undo-body">
            <a href="javascript:;" class="close-undo-box"><span></span></a>
            <div class="folders-undo-header"><?php esc_html_e("Action performed successfully", "folders") ?></div>
            <div class="folders-undo-body"><?php printf(esc_html__("Your action has been successfully completed. Click the %sUndo%s button to reverse the action", "folders"), "<b>", "</b>"); ?></div>
            <div class="folders-undo-footer"><button class="undo-button" type="button"><?php esc_html_e("Undo", "folders") ?></button></div>
        </div>
    </div>

    <div class="folders-undo-notification" id="undo-done">
        <div class="folders-undo-body success" style="padding: 0">
            <a href="javascript:;" class="close-undo-box"><span></span></a>
            <div class="folders-undo-header" style="color: #014737; padding: 0"><?php esc_html_e("Action reversed successfully", "folders") ?></div>
        </div>
    </div>

    <div class="folders-undo-notification" id="copy-message">
        <div class="folders-undo-body">
            <a href="javascript:;" class="close-undo-box"><span></span></a>
            <div class="folders-undo-header"><?php esc_html_e("Selection successfully copied", "folders") ?></div>
            <div class="folders-undo-body"><?php esc_html_e("Folder successfully copied in your clipboard. Navigate to your desired location and click 'Paste' button to paste the folder", "folders"); ?></div>
        </div>
    </div>

    <div class="folders-undo-notification" id="cut-message">
        <div class="folders-undo-body">
            <a href="javascript:;" class="close-undo-box"><span></span></a>
            <div class="folders-undo-header"><?php esc_html_e("Selection successfully selected for moving", "folders") ?></div>
            <div class="folders-undo-body"><?php esc_html_e('Navigate to your desired location and click "Paste" button to move the folder', "folders"); ?></div>
        </div>
    </div>

    <div class="folders-undo-notification success" id="paste-message">
        <div class="folders-undo-body">
            <a href="javascript:;" class="close-undo-box"><span></span></a>
            <div class="folders-undo-header"><?php esc_html_e("Selection successfully selected for moving", "folders") ?></div>
            <div class="folders-undo-body"><?php esc_html_e("Clipboard content has been pasted successfully.", "folders"); ?></div>
            <div class="folders-undo-footer"><button class="undo-button copy-paste-action " type="button"><?php esc_html_e("Undo", "folders") ?></button></div>
        </div>
    </div>

    <div class="folders-undo-notification success" id="folder-upload-message">
        <div class="folders-undo-body">
            <a href="javascript:;" class="close-undo-box"><span></span></a>
            <div class="folders-undo-header"><?php esc_html_e("Folder successfully uploaded", "folders") ?></div>
            <div class="folders-undo-body"><?php esc_html_e("Your folder has successfully been uploaded.", "folders"); ?></div>
        </div>
    </div>

    <div class="folder-popup-form" id="wp-config-update-notice">
        <form action="" method="post" id="bulk-folder-form">
            <div class="popup-form-content">
                <div class="popup-form-data">
                    <div class="close-popup-button">
                        <a class="" href="javascript:;"><span></span></a>
                    </div>
                    <div class="popup-folder-title">
                        <?php esc_html_e("Something went wrong", "folders") ?>
                    </div>
                    <div class="folder-form-message" style="padding: 25px 10px;" >
                        <?php esc_html_e('We couldn’t write to the file automatically. Please add the line manually to your wp-config.php. You need to modify your wp-config.php file and just before the line that says "That’s all, stop editing!", add this line:', "folders") ?><code>define( 'MEDIA_TRASH', true );</code>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="folder-popup-form" id="lock-all-folder-popup">
        <div class="popup-form-content">
            <div class="popup-form-data">
                <div class="close-popup-button">
                    <a class="" href="javascript:;"><span></span></a>
                </div>
                <div class="add-update-folder-title">
                    <?php esc_html_e("Are you sure?", "folders") ?>
                </div>
                <div class="folder-form-message" style="padding: 25px 10px;" >
                    You're about to <b>lock all folders</b>. Are you sure?
                </div>
                <div class="folder-form-buttons">
                    <input type="hidden" id="unassigned_folders" />
                    <a href="javascript:;" class="form-cancel-btn "><?php esc_html_e("Nevermind", "folders") ?></a>
                    <a href="javascript:;" class="form-submit-btn lock-all-folder"><?php esc_html_e("Lock all folders", "folders") ?></a>
                </div>
            </div>
        </div>
    </div>

    <div class="folder-popup-form" id="unlock-all-folder-popup">
        <div class="popup-form-content">
            <div class="popup-form-data">
                <div class="close-popup-button">
                    <a class="" href="javascript:;"><span></span></a>
                </div>
                <div class="add-update-folder-title">
                    <?php esc_html_e("Are you sure?", "folders") ?>
                </div>
                <div class="folder-form-message" style="padding: 25px 10px;" >
                    You're about to <b>unlock all folders</b>. Are you sure?
                </div>
                <div class="folder-form-buttons">
                    <input type="hidden" id="unassigned_folders" />
                    <a href="javascript:;" class="form-cancel-btn "><?php esc_html_e("Nevermind", "folders") ?></a>
                    <a href="javascript:;" class="form-submit-btn unlock-all-folder"><?php esc_html_e("Unlock all folders", "folders") ?></a>
                </div>
            </div>
        </div>
    </div>

    <div class="folder-popup-form" id="keyboard-shortcut">
        <div class="popup-form-content">
            <div class="popup-content" style="position: relative;">
                <div class="close-popup-button">
                    <a class="" href="javascript:;"><span></span></a>
                </div>
                <div class="import-plugin-title" style="font-weight: bold; padding: 0 0 20px 0; font-size: 16px;"><?php esc_html_e("Keyboard shortcuts (Ctrl+K)", 'folders'); ?></div>
                <div class="plugin-import-table">
                    <table class="keyboard-shortcut">
                        <tr>
                            <th><?php esc_html_e("Create New Folder", "folders") ?></th>
                            <td><span class="key-button">Shift</span><span class="plus-button">+</span><span class="key-button">N</span> </td>
                        </tr>
                        <tr>
                            <th><?php esc_html_e("Rename Folder", "folders") ?></th>
                            <td><span class="key-button">F2</span></td>
                        </tr>
                        <tr>
                            <th><?php esc_html_e("Copy Folder", "folders") ?></th>
                            <td><span class="key-button">Ctrl</span><span class="plus-button">+</span><span class="key-button">C</span> </td>
                        </tr>
                        <tr>
                            <th><?php esc_html_e("Cut Folder", "folders") ?></th>
                            <td><span class="key-button">Ctrl</span><span class="plus-button">+</span><span class="key-button">X</span> </td>
                        </tr>
                        <tr>
                            <th><?php esc_html_e("Paste Folder", "folders") ?></th>
                            <td><span class="key-button">Ctrl</span><span class="plus-button">+</span><span class="key-button">V</span> </td>
                        </tr>
                        <tr>
                            <th><?php esc_html_e("Duplicate Folder", "folders") ?></th>
                            <td><span class="key-button">Ctrl</span><span class="plus-button">+</span><span class="key-button">D</span> </td>
                        </tr>
                        <tr>
                            <th><?php esc_html_e("Delete Folder", "folders") ?></th>
                            <td><span class="key-button">Delete</span></td>
                        </tr>
                        <tr>
                            <th><?php esc_html_e("Next Folder", "folders") ?></th>
                            <td><span class="key-button"><span class="dashicons dashicons-arrow-down-alt"></span></span> </td>
                        </tr>
                        <tr>
                            <th><?php esc_html_e("Previous Folder", "folders") ?></th>
                            <td><span class="key-button"><span class="dashicons dashicons-arrow-up-alt"></span></span> </td>
                        </tr>
                        <tr>
                            <th><?php esc_html_e("Expand Folder", "folders") ?></th>
                            <td><span class="key-button"><span class="dashicons dashicons-arrow-right-alt"></span></span> </td>
                        </tr>
                        <tr>
                            <th><?php esc_html_e("Collapse Folder", "folders") ?></th>
                            <td><span class="key-button"><span class="dashicons dashicons-arrow-left-alt"></span></span> </td>
                        </tr>
                        <tr>
                            <th><?php esc_html_e("Re-order folders to upwards", "folders") ?></th>
                            <td><span class="key-button">Ctrl</span><span class="plus-button">+</span><span class="key-button"><span class="dashicons dashicons-arrow-up-alt"></span></span></td>
                        </tr>
                        <tr>
                            <th><?php esc_html_e("Re-order folders to downwards", "folders") ?></th>
                            <td><span class="key-button">Ctrl</span><span class="plus-button">+</span><span class="key-button"><span class="dashicons dashicons-arrow-down-alt"></span></span></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
