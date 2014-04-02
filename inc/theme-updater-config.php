<?php
/**
 * This file is used for theme licensing and updates using 
 * Easy Digital Downloads.
 *
 * @package WordPress
 * @subpackage notepad Sample Theme
 */

// This is the URL our updater / license checker pings. This should be the URL of the site with notepad installed
define('IDEABOX_STORE_URL', 'http://ideaboxthemes.com'); // add your own unique prefix to prevent conflicts
// The name of your product. This should match the download name in notepad exactly
define('IDEABOX_THEME_NAME', 'smart-shop-wordpress-theme'); // add your own unique prefix to prevent conflicts

function ideabox_theme_updater() {

    $test_license = trim(get_option('ideabox_theme_license_key'));

    $notepad_updater = new notepad_SL_Theme_Updater(array(
        'remote_api_url' => IDEABOX_STORE_URL, // Our store URL that is running notepad
        'version' => '1.0', // The current theme version we are running
        'license' => $test_license, // The license key (used get_option above to retrieve from DB)
        'item_name' => IDEABOX_THEME_NAME, // The name of this theme
        'author' => 'IdeaBox Themes' // The author's name
            )
    );
}

add_action('admin_init', 'ideabox_theme_updater');


/* * *********************************************
 * Sample settings page, substitute with yours
 * ********************************************* */

function ideabox_theme_license_page() {
    $license = get_option('ideabox_theme_license_key');
    $status = get_option('ideabox_theme_license_key_status');
    ?>
    <div class="wrap">
        <h2><?php _e('Theme License Options'); ?></h2>
        <form method="post" action="options.php">

    <?php settings_fields('ideabox_theme_license'); ?>

            <table class="form-table">
                <tbody>
                    <tr valign="top">
                        <th scope="row" valign="top">
    <?php _e('License Key'); ?>
                        </th>
                        <td>
                            <input id="ideabox_theme_license_key" name="ideabox_theme_license_key" type="text" class="regular-text" value="<?php esc_attr($license); ?>" />
                            <label class="description" for="ideabox_theme_license_key"><?php _e('Enter your license key'); ?></label>
                        </td>
                    </tr>
    <?php if (false !== $license) { ?>
                        <tr valign="top">
                            <th scope="row" valign="top">
        <?php _e('Activate License'); ?>
                            </th>
                            <td>
        <?php if ($status !== false && $status == 'valid') { ?>
                                    <span style="color:green;"><?php _e('active'); ?></span>
                                    <?php wp_nonce_field('notepad_sample_nonce', 'notepad_sample_nonce'); ?>
                                    <input type="submit" class="button-secondary" name="notepad_theme_license_deactivate" value="<?php _e('Deactivate License'); ?>"/>
                                <?php } else {
                                    wp_nonce_field('notepad_sample_nonce', 'notepad_sample_nonce');
                                    ?>
                                    <input type="submit" class="button-secondary" name="notepad_theme_license_activate" value="<?php _e('Activate License'); ?>"/>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <?php submit_button(); ?>

        </form>
        <?php
    }

    function ideabox_theme_register_option() {
        // creates our settings in the options table
        register_setting('ideabox_theme_license', 'ideabox_theme_license_key', 'notepad_theme_sanitize_license');
    }

    add_action('admin_init', 'ideabox_theme_register_option');

    /*     * *********************************************
     * Add our menu item
     * ********************************************* */

    function ideabox_theme_license_menu() {
        add_theme_page('Theme License', 'Theme License', 'manage_options', 'smartshop-license', 'ideabox_theme_license_page');
    }

    add_action('admin_menu', 'ideabox_theme_license_menu');


    /*     * *********************************************
     * Gets rid of the local license status option
     * when adding a new one
     * ********************************************* */

    function notepad_theme_sanitize_license($new) {
        $old = get_option('ideabox_theme_license_key');
        if ($old && $old != $new) {
            delete_option('ideabox_theme_license_key_status'); // new license has been entered, so must reactivate
        }
        return $new;
    }

    /*     * *********************************************
     * Illustrates how to activate a license key.
     * ********************************************* */

    function ideabox_theme_activate_license() {

        if (isset($_POST['notepad_theme_license_activate'])) {
            if (!check_admin_referer('notepad_sample_nonce', 'notepad_sample_nonce'))
                return; // get out if we didn't click the Activate button

            global $wp_version;

            $license = trim(get_option('ideabox_theme_license_key'));

            $api_params = array(
                'notepad_action' => 'activate_license',
                'license' => $license,
                'item_name' => urlencode(IDEABOX_THEME_NAME)
            );

            $response = wp_remote_get(add_query_arg($api_params, IDEABOX_STORE_URL), array('timeout' => 15, 'sslverify' => false));

            if (is_wp_error($response))
                return false;

            $license_data = json_decode(wp_remote_retrieve_body($response));

            // $license_data->license will be either "active" or "inactive"

            update_option('ideabox_theme_license_key_status', $license_data->license);
        }
    }

    add_action('admin_init', 'ideabox_theme_activate_license');

    /*     * *********************************************
     * Illustrates how to deactivate a license key.
     * This will descrease the site count
     * ********************************************* */

    function ideabox_theme_deactivate_license() {

        // listen for our activate button to be clicked
        if (isset($_POST['notepad_theme_license_deactivate'])) {

            // run a quick security check
            if (!check_admin_referer('notepad_sample_nonce', 'notepad_sample_nonce'))
                return; // get out if we didn't click the Activate button

                
// retrieve the license from the database
            $license = trim(get_option('ideabox_theme_license_key'));


            // data to send in our API request
            $api_params = array(
                'notepad_action' => 'deactivate_license',
                'license' => $license,
                'item_name' => urlencode(IDEABOX_THEME_NAME) // the name of our product in notepad
            );

            // Call the custom API.
            $response = wp_remote_get(add_query_arg($api_params, IDEABOX_STORE_URL), array('timeout' => 15, 'sslverify' => false));

            // make sure the response came back okay
            if (is_wp_error($response))
                return false;

            // decode the license data
            $license_data = json_decode(wp_remote_retrieve_body($response));

            // $license_data->license will be either "deactivated" or "failed"
            if ($license_data->license == 'deactivated')
                delete_option('ideabox_theme_license_key_status');
        }
    }

    add_action('admin_init', 'ideabox_theme_deactivate_license');



    /*     * *********************************************
     * Illustrates how to check if a license is valid
     * ********************************************* */

    function ideabox_theme_check_license() {

        global $wp_version;

        $license = trim(get_option('ideabox_theme_license_key'));

        $api_params = array(
            'notepad_action' => 'check_license',
            'license' => $license,
            'item_name' => urlencode(IDEABOX_THEME_NAME)
        );

        $response = wp_remote_get(add_query_arg($api_params, IDEABOX_STORE_URL), array('timeout' => 15, 'sslverify' => false));

        if (is_wp_error($response))
            return false;

        $license_data = json_decode(wp_remote_retrieve_body($response));

        if ($license_data->license == 'valid') {
            echo 'valid';
            exit;
            // this license is still valid
        } else {
            echo 'invalid';
            exit;
            // this license is no longer valid
        }
    }
    






