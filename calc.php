<?php
/**
 * Plugin Name: AT
 * Description: AT internal plugin
 * Version: 2.0
 * Author: IDD
 */

// Function to add a menu page to the left sidebar in the WordPress dashboard
function at_internal_plugin_admin_menu() {
    add_menu_page(
        'AT',
        'AT',
        'manage_options',
        'at-internal-plugin',
        'at_internal_submenu_page',
        'dashicons-lock', 
        6 
    );
}
add_action('admin_menu', 'at_internal_plugin_admin_menu');

// Function to create the submenu page
function at_internal_submenu_page() {
    ?>
    <div class="wrap">
        <h1>AT</h1>
        <form method="post" action="">
            <?php
            // Add a nonce for security
            wp_nonce_field('at_obfuscate_nonce_action', 'at_obfuscate_nonce');

            // Get the current value of the checkbox
            $is_checked = get_option('at_obfuscate_enabled', false);
            ?>
            <label>
                <input type="checkbox" name="at_obfuscate_enabled" <?php checked($is_checked); ?>>
                Turn on/off encryption code
            </label>
            <br><br>
            <input type="submit" class="button button-primary" value="Save Changes">
        </form>
    </div>
    <?php

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && check_admin_referer('at_obfuscate_nonce_action', 'at_obfuscate_nonce')) {
        $new_value = isset($_POST['at_obfuscate_enabled']);
        update_option('at_obfuscate_enabled', $new_value);

        // Redirect to the same page to avoid resubmission
        wp_redirect($_SERVER['REQUEST_URI']);
        exit;
    }
}

// Function that activates the encryption code
function at_obfuscate_html_output($buffer) {
    $encoded_html = base64_encode($buffer);

    $script = <<<EOD
<script>
    const decodedHTML = atob("$encoded_html");
    document.write(decodedHTML);
</script>
EOD;

    return $script;
}

// Function that checks the checkbox state and activates encryption
function at_check_checkbox() {
    if (get_option('at_obfuscate_enabled', false)) {
        ob_start('at_obfuscate_html_output');
    }
}
add_action('init', 'at_check_checkbox');
