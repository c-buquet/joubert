<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<style>
    .input-field label {
        display: block;
        font-weight: 700;
        padding-bottom: 5px;
    }
    .key-table {
        padding: 30px 0 0;
    }
    input.license-key {
        width: 100%;
        max-width: 250px;
        padding: 0 8px;
        line-height: 2;
        min-height: 30px;
        background: #FFFFFF;
        box-shadow: 0 0 0 transparent;
        border-radius: 4px;
        border: 1px solid #8c8f94;
        background-color: #fff;
        color: #2c3338;
    }
    .submit {
        text-align: left;
        max-width: 100%;
        margin-top: 0px;
        padding-top: 10px;
    }
    .folder-error-message, .folder-success-message {
        background: #fff;
        margin: 10px 0 10px 0;
        padding: 10px 10px 10px 10px;
        border-left: solid 3px #dd4b39;
    }
    .folder-success-message {
        border-left: solid 3px #00a65a;
    }
    .license-key-footer {
        font-weight: bold;
        padding-bottom: 15px;
        font-size: 16px;
        line-height: 30px;
    }
    span.error-message {
        display: inline-block;
        margin-right: 10px;
    }
    <?php if ( function_exists( 'is_rtl' ) && is_rtl() ) { ?>
    .submit {
        text-align: right;
    }
    <?php } ?>
</style>
<div class="wrap">
    <h1><?php esc_html_e( 'Folders: License Key', 'folders'); ?></h1>
    <?php
    $type = folders_sanitize_text('m', 'get');
    if(isset($type) && !empty($type)) {
        switch ($type) {
            case "error": ?>
                <div class='folder-error-message'><?php esc_html_e("Your license key is not valid", 'folders') ?></div>
                <?php break;
            case "valid": ?>
                <div class='folder-success-message'><?php esc_html_e("Your license key is activated successfully", 'folders') ?></div>
                <?php break;
            case "unactivated": ?>
                <div class='folder-success-message'><?php esc_html_e("Your license key is deactivated successfully", 'folders') ?></div>
                <?php break;
            case "expired": ?>
                <div class='folder-error-message'><?php esc_html_e("Your license has been expired", 'folders') ?></div>
                <?php break;
            case "no_activations": ?>
                <div class='folder-error-message'><?php esc_html_e("Your license was activated for another domain, please visit your ", 'folders') ?><a target="_blank" href="https://go.premio.io"><?php esc_html_e("Premio account", 'folders') ?></a></div>
                <?php break;
        }
    }
    ?>
    <div class="key-table">
        <div class="license-key-footer">
            <?php
            $licenseKey = get_option("wcp_folder_license_key");
            $licenseData = array();
            $active_status = 0;
            delete_transient("folder_license_key_data");
            if(!empty($licenseKey)) {
                $licenseData = $this->get_license_key_data($licenseKey);
                if(!empty($licenseData)) {
                    if($licenseData['license'] == "valid") {
                        $active_status = 1;
                    } else if($licenseData['license'] == "expired") {
                        $active_status = 2;
                    }
                } else {
                    $licenseKey = "";
                }
            }
            if(!$active_status) {
                esc_html_e("To receive updates, please enter your valid license key.", 'folders');
            } else if ($active_status == 1 && $licenseData['expires'] == "lifetime") {
                esc_html_e("You have a lifetime license", 'folders');
            } else if($active_status == 1 ){
                printf(esc_html__("Your license will expire on %s"), date("d M, Y",strtotime($licenseData['expires'])));
            } else if($active_status == 2 ){
                $url = WCP_PRO_FOLDER_API_URL.'/checkout/?edd_license_key='.$licenseKey."&download_id=".WCP_PRO_FOLDER_PR0DUCT_ID; ?>
                <span class='error-message'><?php printf(esc_html__("Your license has been expired on %s", 'folders'), date("d M, Y",strtotime($licenseData['expires']))) ?></span><a target="_blank" href="<?php echo esc_url($url) ?>" class="button button-primary" ><?php esc_html_e("Renew Now", 'folders') ?></a> <?php
            }
            $encLicenseKey = $licenseKey;
            if(!empty($licenseKey)) {
                $encLicenseKey = substr_replace($licenseKey,"**************",6,20);
            }
            ?>
        </div>
        <form action="" id="license_key_form">
            <div class="input-field">
                <label for="license_key"><?php esc_html_e("License Key", 'folders') ?></label>
                <?php if(!empty($licenseKey)) { ?>
                    <input class="license-key" readonly value="<?php echo esc_attr($encLicenseKey) ?>">
                    <input type="hidden" class="license-key" id="license_key" name="license_key" value="<?php echo esc_attr($licenseKey) ?>">
                <?php } else { ?>
                    <input type="text" class="license-key" id="license_key" name="license_key" value="<?php echo esc_attr($licenseKey) ?>">
                <?php } ?>
            </div>
            <div class="submit">
                <?php if(!empty($licenseKey)) { ?>
                    <a href="javascript:;" class="button secondary-button" id="deactivate_key"><?php esc_html_e("Deactivate Key", 'folders') ?></a>
                <?php } else { ?>
                    <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_html_e("Activate Key", 'folders') ?>">
                <?php } ?>
            </div>
        </form>
    </div>
</div>
<script>
    jQuery(document).ready(function(){
       jQuery("#license_key_form").submit(function(){
           licenseKey = jQuery.trim(jQuery("#license_key").val());
           jQuery.ajax({
               url:"<?php echo esc_url(admin_url("admin-ajax.php")) ?>",
               data: "key="+licenseKey+"&action=wcp_folder_activate_key&nonce=<?php echo wp_create_nonce("activate_folder_key") ?>",
               method: 'post',
               success: function(res){
                   window.location = '<?php echo esc_url(admin_url("admin.php")) ?>?page=wcp_folders_register&m='+res;
               }
           });
           return false;
       });
        jQuery("#deactivate_key").click(function(){
            licenseKey = jQuery.trim(jQuery("#license_key").val());
            jQuery.ajax({
                url:"<?php echo esc_url(admin_url("admin-ajax.php")) ?>",
                data: "key="+licenseKey+"&action=wcp_folder_deactivate_key&nonce=<?php echo wp_create_nonce("deactivate_folder_key") ?>",
                method: 'post',
                success: function(res){
                    window.location = '<?php echo esc_url(admin_url("admin.php")) ?>?page=wcp_folders_register&m='+res;
                }
            });
            return false;
        });
    });
</script>