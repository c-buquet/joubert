<?php
if(isset($_GET['scan'])  && $_GET['scan'] == 1){
    include_once WCP_PRO_FOLDER_DIR . "includes/media.clean.php";
    $media = new Media_Cleaning_List();
    ?>

    <div class="wrap">
        <h2>Media Cleaning</h2>
        <?php
        if(isset($_POST['bulk-delete-scanned-files']) && !empty($_POST['bulk-delete-scanned-files'])) {
            $delete_ids = esc_sql($_POST['bulk-delete-scanned-files']);
            if(((defined("MEDIA_TRASH") && MEDIA_TRASH == true))) {
                echo "<div class='updated notice is-dismissible'><p>" . sprintf(esc_html__("%s Media files have been moved to trash.", "folders"), count($delete_ids)) . '</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
            } else {
                echo "<div class='updated notice is-dismissible'><p>" . sprintf(esc_html__("%s Media files permanently deleted.", "folders"), count($delete_ids)) . '</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
            }
        }
        ?>
        <div>
            <form method="post">
                <?php $media->prepare_items(); ?>
                <?php $media->display(); ?>
            </form>
        </div>
    </div>

<?php } else { ?>
    <div class="media-clean-box-content">
        <div class="media-clean-box-title">
            <?php esc_html_e("Scan for unused media","folders") ?>
        </div>
        <div class="media-clean-box-border"></div>
        <?php if(!isset($_GET['scan'])) { ?>
        <div class="scan-steps scan-step-1">
            <div class="m-top">
                <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
                <lottie-player src="https://assets3.lottiefiles.com/packages/lf20_1h1casbp.json"  background="transparent"  speed="1"  style="width: 300px; height: 300px; margin: 0 auto"  loop autoplay></lottie-player>
            </div>
            <div class="media-clean-box-desc">
                <?php esc_html_e("Find unused media files which aren't used in your website. An internal trash allows you to make sure everything works properly before deleting the media entries (and files) permanently.","folders") ?>
            </div>
            <div class="m-bottom">
                <a href="<?php echo admin_url("upload.php?page=folders-media-cleaning&scan=step-2"); ?>" class="media-clean-box-button">
                    <?php esc_html_e("Get Started","folders") ?>
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M10 18C14.4183 18 18 14.4183 18 10C18 5.58172 14.4183 2 10 2C5.58172 2 2 5.58172 2 10C2 14.4183 5.58172 18 10 18ZM9.5547 7.16795C9.24784 6.96338 8.8533 6.94431 8.52814 7.11833C8.20298 7.29235 8 7.63121 8 8V12C8 12.3688 8.20298 12.7077 8.52814 12.8817C8.8533 13.0557 9.24784 13.0366 9.5547 12.8321L12.5547 10.8321C12.8329 10.6466 13 10.3344 13 10C13 9.66565 12.8329 9.35342 12.5547 9.16795L9.5547 7.16795Z" />
                    </svg>
                </a>
            </div>
        </div>
        <?php } else { ?>
            <div class="scan-steps scan-step-2">
                <div class="m-top">
                    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
                    <lottie-player src="https://assets10.lottiefiles.com/packages/lf20_rbbibjz5.json"  background="transparent"  speed="1"  style="width: 300px; height: 300px; margin: 0 auto"  loop autoplay></lottie-player>
                </div>
                <div class="media-clean-box-desc">
                    <ul>
                        <li>
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M8 16C10.1217 16 12.1566 15.1571 13.6569 13.6569C15.1571 12.1566 16 10.1217 16 8C16 5.87827 15.1571 3.84344 13.6569 2.34315C12.1566 0.842855 10.1217 0 8 0C5.87827 0 3.84344 0.842855 2.34315 2.34315C0.842855 3.84344 0 5.87827 0 8C0 10.1217 0.842855 12.1566 2.34315 13.6569C3.84344 15.1571 5.87827 16 8 16ZM11.707 6.707C11.8892 6.5184 11.99 6.2658 11.9877 6.0036C11.9854 5.7414 11.8802 5.49059 11.6948 5.30518C11.5094 5.11977 11.2586 5.0146 10.9964 5.01233C10.7342 5.01005 10.4816 5.11084 10.293 5.293L7 8.586L5.707 7.293C5.5184 7.11084 5.2658 7.01005 5.0036 7.01233C4.7414 7.0146 4.49059 7.11977 4.30518 7.30518C4.11977 7.49059 4.0146 7.7414 4.01233 8.0036C4.01005 8.2658 4.11084 8.5184 4.293 8.707L6.293 10.707C6.48053 10.8945 6.73484 10.9998 7 10.9998C7.26516 10.9998 7.51947 10.8945 7.707 10.707L11.707 6.707Z" fill="#0D9488"/>
                            </svg>
                            <b>Backup your website before deleting any file.</b> Make sure to move files to trash beforehand and ensure nothing is broken on the website before <b>permanently deleting</b>
                        </li>
                        <li>
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M8 16C10.1217 16 12.1566 15.1571 13.6569 13.6569C15.1571 12.1566 16 10.1217 16 8C16 5.87827 15.1571 3.84344 13.6569 2.34315C12.1566 0.842855 10.1217 0 8 0C5.87827 0 3.84344 0.842855 2.34315 2.34315C0.842855 3.84344 0 5.87827 0 8C0 10.1217 0.842855 12.1566 2.34315 13.6569C3.84344 15.1571 5.87827 16 8 16ZM11.707 6.707C11.8892 6.5184 11.99 6.2658 11.9877 6.0036C11.9854 5.7414 11.8802 5.49059 11.6948 5.30518C11.5094 5.11977 11.2586 5.0146 10.9964 5.01233C10.7342 5.01005 10.4816 5.11084 10.293 5.293L7 8.586L5.707 7.293C5.5184 7.11084 5.2658 7.01005 5.0036 7.01233C4.7414 7.0146 4.49059 7.11977 4.30518 7.30518C4.11977 7.49059 4.0146 7.7414 4.01233 8.0036C4.01005 8.2658 4.11084 8.5184 4.293 8.707L6.293 10.707C6.48053 10.8945 6.73484 10.9998 7 10.9998C7.26516 10.9998 7.51947 10.8945 7.707 10.707L11.707 6.707Z" fill="#0D9488"/>
                            </svg>
                            <?php esc_html_e("Any images added to other websites with the same link will show errors after deletion", "folders") ?>
                        </li>
                        <li>
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M8 16C10.1217 16 12.1566 15.1571 13.6569 13.6569C15.1571 12.1566 16 10.1217 16 8C16 5.87827 15.1571 3.84344 13.6569 2.34315C12.1566 0.842855 10.1217 0 8 0C5.87827 0 3.84344 0.842855 2.34315 2.34315C0.842855 3.84344 0 5.87827 0 8C0 10.1217 0.842855 12.1566 2.34315 13.6569C3.84344 15.1571 5.87827 16 8 16ZM11.707 6.707C11.8892 6.5184 11.99 6.2658 11.9877 6.0036C11.9854 5.7414 11.8802 5.49059 11.6948 5.30518C11.5094 5.11977 11.2586 5.0146 10.9964 5.01233C10.7342 5.01005 10.4816 5.11084 10.293 5.293L7 8.586L5.707 7.293C5.5184 7.11084 5.2658 7.01005 5.0036 7.01233C4.7414 7.0146 4.49059 7.11977 4.30518 7.30518C4.11977 7.49059 4.0146 7.7414 4.01233 8.0036C4.01005 8.2658 4.11084 8.5184 4.293 8.707L6.293 10.707C6.48053 10.8945 6.73484 10.9998 7 10.9998C7.26516 10.9998 7.51947 10.8945 7.707 10.707L11.707 6.707Z" fill="#0D9488"/>
                            </svg>
                            Some actively used files can still show up as unused files when searching. You are <b>responsible for any damage</b> if you delete anything important. So, <b>be careful</b> 🙏
                        </li>
                    </ul>
                </div>
                <div class="m-bottom">
                    <form action="<?php echo admin_url("upload.php") ?>" method="">
                        <div class="confirm-box">
                            <input type="hidden" name="terms" value="0">
                            <label for="agree_media_terms"><input type="checkbox" id="agree_media_terms" name="terms" value="1"> <?php esc_html_e("I agree to use the Media Cleaning feature", "folders"); ?></label>
                        </div>
                        <div class="confirm-button">
                            <button type="submit" disabled>Scan Media Files
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M10 18C14.4183 18 18 14.4183 18 10C18 5.58172 14.4183 2 10 2C5.58172 2 2 5.58172 2 10C2 14.4183 5.58172 18 10 18ZM9.5547 7.16795C9.24784 6.96338 8.8533 6.94431 8.52814 7.11833C8.20298 7.29235 8 7.63121 8 8V12C8 12.3688 8.20298 12.7077 8.52814 12.8817C8.8533 13.0557 9.24784 13.0366 9.5547 12.8321L12.5547 10.8321C12.8329 10.6466 13 10.3344 13 10C13 9.66565 12.8329 9.35342 12.5547 9.16795L9.5547 7.16795Z" />
                                </svg>
                            </button>
                        </div>
                        <input type="hidden" name="page" value="folders-media-cleaning">
                        <input type="hidden" name="scan" value="1">
                    </form>
                </div>
            </div>
        <?php } ?>
    </div>
<?php } ?>


<div class="media-clean-delete-box" id="show_delete_box">
    <div class="media-clean-delete-box-overlay"></div>
        <div class="media-clean-delete-box-content">
            <div class="media-clean-delete-box-border"></div>
            <div class="close-icon"><span class="dashicons dashicons-no-alt" id="close_icon"></span></div>
            <div class="media-clean-delete-box-title">
                <p class="title-que"><span class="dashicons dashicons-info-outline icon-background-color"></span>
                <span class="pd"><?php esc_html_e("Are you sure about deleting the media files?","folders") ?></p></span>
            </div>
            <div class="media-clean-delete-box-desc">
                <?php
                if((defined("MEDIA_TRASH") && MEDIA_TRASH == true)) {
                    esc_html_e("Are you sure want to delete the media files? By doing this, you’ll delete your selected media. You’ll be able to retrive the media from Trash.", "folders");
                } else {
                    printf(esc_html__("Are you sure want to delete the media files? By doing this, you’ll delete your selected media and %s. You will lose the selected media and will %s retrieve it", "folders"), "<b>".esc_html__("permanently delete it", "folders")."</b>", "<b>".esc_html__("not be able to", "folders")."</b>");
                }?>
            </div>
            <div class="media-clean-delete-box-footer">
                <a href="javascript:;" class="cancel-button" id="cancel_button"><?php esc_html_e("Cancel","folders") ?></a>
                <button class="delete-button" id="delete_button"><?php echo ((defined("MEDIA_TRASH") && MEDIA_TRASH == true))?esc_html__("Move to Trash","folders"):esc_html__("Delete permanently","folders") ?></button>
        </div>
    </div>
</div>

<div class="media-clean-delete-box" id="show_delete_form_box">
    <div class="media-clean-delete-box-overlay"></div>
    <div class="media-clean-delete-box-content">
        <div class="media-clean-delete-box-border"></div>
        <div class="close-icon"><span class="dashicons dashicons-no-alt" id="close_icon"></span></div>
        <div class="media-clean-delete-box-title">
            <p class="title-que"><span class="dashicons dashicons-info-outline icon-background-color"></span>
                <span class="pd"><?php esc_html_e("Are you sure about deleting the media files?","folders") ?></p></span>
        </div>
        <div class="media-clean-delete-box-desc">
            <?php
            if((defined("MEDIA_TRASH") && MEDIA_TRASH == true)) {
                esc_html_e("Are you sure want to delete the media files? By doing this, you’ll delete your selected media. You’ll be able to retrive the media from Trash.", "folders");
            } else {
                printf(esc_html__("Are you sure want to delete the media files? By doing this, you’ll delete your selected media and %s. You will lose the selected media and will %s retrieve it", "folders"), "<b>".esc_html__("permanently delete it", "folders")."</b>", "<b>".esc_html__("not be able to", "folders")."</b>");
            }?>
        </div>
        <div class="media-clean-delete-box-footer">
            <a href="javascript:;" class="cancel-button" id="cancel_button"><?php esc_html_e("Cancel","folders") ?></a>
            <button class="delete-button" id="delete_form_button"><?php echo ((defined("MEDIA_TRASH") && MEDIA_TRASH == true))?esc_html__("Move to Trash","folders"):esc_html__("Delete permanently","folders") ?></button>
        </div>
    </div>
</div>

<script>

    jQuery(document).ready(function(){
        var hrefLink = "";

        jQuery(document).on("click", ".show-delete-box", function (e) {
            e.preventDefault();
            jQuery("#show_delete_box").show();
            hrefLink = jQuery(this).attr('href');
        });

        jQuery(document).on("click", "#agree_media_terms", function (e) {
            if(jQuery(this).is(":checked")) {
                jQuery(".confirm-button button").prop("disabled", false);
            } else {
                jQuery(".confirm-button button").prop("disabled", true);
            }
        });

        jQuery(document).on("click", ".media-clean-delete-box-overlay", function () {
            jQuery(".media-clean-delete-box").hide();
        });

        jQuery(document).on("click", ".media-clean-delete-box-content", function (e) {
            e.stopPropagation();
        });

        jQuery(document).on("click", ".close-icon", function () {
            jQuery("#show_delete_box").hide();
            jQuery("#show_delete_form_box").hide();
        });

        jQuery(document).on("click", ".cancel-button", function () {
            jQuery("#show_delete_box").hide();
            jQuery("#show_delete_form_box").hide();
        });

        jQuery(document).on("click", "#delete_form_button", function () {
            isFormTriggered = true;
            jQuery(".wrap > div > form").trigger("submit");
        });

        var isFormTriggered = false;
        jQuery(document).on("submit", ".wrap > div > form", function () {
            if(!isFormTriggered) {
                if (jQuery("#bulk-action-selector-top").val() == "bulk-delete") {
                    jQuery("#show_delete_form_box").show();
                    return false;
                }
            }
            return true;
        });

        jQuery(document).on("click", "#delete_button", function () {
            jQuery.ajax({
                url: hrefLink,
                data: {
                    action: 'wcp_remove_scanned_media'
                },
                type: 'post',
                success: function() {
                    window.location.reload();
                }
            })
        });
    });
</script>