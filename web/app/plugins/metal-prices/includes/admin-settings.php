<?php

function metal_prices_settings_page_html() {
    if (!current_user_can('manage_options')) {
        return;
    }

    if (isset($_POST['metal_prices_settings'])) {
        $api_key = sanitize_text_field($_POST['metal_prices_api_key']);
        $frequency = intval($_POST['metal_prices_frequency']);
        $spread = floatval($_POST['metal_prices_spread']);

        update_option('metal_prices_api_key', $api_key);
        update_option('metal_prices_frequency', $frequency);
        update_option('metal_prices_spread', $spread);

        // Envoyer les données à l'API via une requête POST
        $response = wp_remote_post('http://127.0.0.1:8000/api/settings', array(
            'method'    => 'POST',
            'body'      => json_encode(array(
                'goldapi_key'   => $api_key,
                'frequency' => $frequency,
                'spread'    => $spread
            )),
            'headers'   => array(
                'Content-Type' => 'application/json',
            )
        ));

        if (is_wp_error($response)) {
            $error_message = $response->get_error_message();
            echo "<div class='error'><p>Something went wrong: $error_message</p></div>";
        } else {
            echo "<div class='updated'><p>Settings saved and sent to API successfully!</p></div>";
        }
    }

    $api_key = get_option('metal_prices_api_key');
    $frequency = get_option('metal_prices_frequency');
    $spread = get_option('metal_prices_spread');
    ?>
    <div class="wrap">
        <h1>Metal Prices Settings</h1>
        <form method="post" action="">
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label for="metal_prices_api_key">API Key</label></th>
                    <td><input name="metal_prices_api_key" type="text" id="metal_prices_api_key" value="<?php echo esc_attr($api_key); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="metal_prices_frequency">Frequency (minutes)</label></th>
                    <td><input name="metal_prices_frequency" type="number" id="metal_prices_frequency" value="<?php echo esc_attr($frequency); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="metal_prices_spread">Spread (%)</label></th>
                    <td><input name="metal_prices_spread" type="number" step="0.01" id="metal_prices_spread" value="<?php echo esc_attr($spread); ?>" class="regular-text"></td>
                </tr>
            </table>
            <p class="submit"><input type="submit" name="metal_prices_settings" id="submit" class="button button-primary" value="Save Changes"></p>
        </form>
    </div>
    <?php
}

function metal_prices_settings_menu() {
    add_options_page(
        'Metal Prices Settings',
        'Metal Prices',
        'manage_options',
        'metal-prices-settings',
        'metal_prices_settings_page_html'
    );
}
add_action('admin_menu', 'metal_prices_settings_menu');

