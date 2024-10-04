<?php

namespace change_username;

/**
 * Enqueue assets on the "user edit" page for user swith the `edit_users` capability.
 */
function enqueue_assets() {
    global $pagenow;

    if( ! in_array($pagenow, ['profile.php', 'user-edit.php'], true)) {
        return;
    }

    if( ! current_user_can('edit_users') ) {
        return;
    }

    wp_enqueue_script('change-username', plugins_url('/assets/js/script.min.js', CHANGE_USERNAME_FILE), [], CHANGE_USERNAME_VERSION, ['strategy' => 'defer', 'in_footer' => true ]);
    wp_localize_script('change-username', 'change_username', array(
        'nonce' => wp_create_nonce('change_username'),
        'ajaxurl' => admin_url('admin-ajax.php'),
    ));
}

/**
 * Handles the AJAX request for changing a username.
 */
function ajax_handler() {
    $response = array(
        'success' => false,
        'new_nonce' => wp_create_nonce('change_username')
    );

    // check capability
    if (! current_user_can('edit_users') ) {
        $response['message'] = esc_html__('You do not have the required capability to do that.', 'change-username');
        wp_send_json($response);
        exit;
    }

    // validate nonce
    check_ajax_referer('change_username');

    // validate request
    if (empty($_POST['new_username']) || empty($_POST['current_username'])) {
        $response['message'] = 'Invalid request.';
        wp_send_json($response);
        exit;
    }

    $new_username = trim(strip_tags($_POST['new_username']));
    $old_username = trim(strip_tags($_POST['current_username']));

    // old username should be provided by the script
    // if it doesn't exist, someone is tampering with the request values
    if (!username_exists($old_username)) {
        $response['message'] = 'Invalid request.';
        wp_send_json($response);
        exit;
    }

    if ($new_username != $old_username) {
        if (mb_strlen($new_username) < 3 || mb_strlen($new_username) > 60) {
            $response['message'] = esc_html__('Username must be between 3 and 60 characters long.', 'change-username');
            wp_send_json($response);
            exit;
        }

        if(! validate_username($new_username)) {
            $response['message'] = esc_html__('This username is invalid because it uses illegal characters. Please enter a valid username.', 'change-username');
            wp_send_json($response);
            exit;
        }

        // check if username is not in list of illegal logins
        /** This filter is documented in wp-includes/user.php */
        $illegal_user_logins = array_map('strtolower', (array) apply_filters('illegal_user_logins', array()));
        if (in_array(strtolower($new_username), $illegal_user_logins, true)) {
            $response['message'] =  esc_html__('Sorry, that username is not allowed.', 'change-username');
            wp_send_json($response);
            exit;
        }

        // check if new username is in use already
        if (username_exists($new_username)) {
            $response['message'] = \sprintf(esc_html__('%1$s is already in use.', 'change-username'), $new_username );
            wp_send_json($response);
            exit;
        }

        // change the username
        change_username($old_username, $new_username);

    }

    // success response
    $response['success'] = true;
    $response['message'] = \sprintf(esc_html__('Username successfully changed to %1$s.', 'change-username'), $new_username );
    wp_send_json($response);
    exit;
}

/**
 * @param string $old_username
 * @param string $new_username
 * @return boolean
 */
function change_username( $old_username, $new_username ) {
    global $wpdb;

    // do nothing if old username does not exist.
    $user_id = username_exists( $old_username );
    if( ! $user_id ) {
        return false;
    }

    // change username
    $q  = $wpdb->prepare("UPDATE $wpdb->users SET user_login = %s WHERE user_login = %s", $new_username, $old_username);
    $wpdb->query($q);

    // change nicename if needed
    $q = $wpdb->prepare("UPDATE $wpdb->users SET user_nicename = %s WHERE user_login = %s AND user_nicename = %s", $new_username, $new_username, $old_username);
    $wpdb->query($q);

    // change display name if needed
    $q  = $wpdb->prepare("UPDATE $wpdb->users SET display_name = %s WHERE user_login = %s AND display_name = %s", $new_username, $new_username, $old_username);
    $wpdb->query($q);

    // when on multisite, check if old username is in the `site_admins` options array. if so, replace with new username to retain superadmin rights.
    if (is_multisite()) {
        $super_admins = (array) get_site_option( 'site_admins', ['admin']);
        $array_key = array_search($old_username, $super_admins);
        if ($array_key) {
            $super_admins[$array_key] = $new_username;
            update_site_option('site_admins' , $super_admins);
        }
    }

    /**
     * Fires right after a username is changed.
     *
     * @param string $old_username
     * @param string $new_username
     */
    do_action('change_username.username_changed', $old_username, $new_username);
    return true;
}
