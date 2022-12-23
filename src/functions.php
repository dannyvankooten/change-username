<?php

namespace change_username;

/**
 * Enqueue assets on the "user edit" page for user swith the `edit_users` capability.
 */
function enqueue_assets() {
    global $pagenow;

    if( ! in_array( $pagenow, array( 'profile.php', 'user-edit.php' ) ) ) {
        return;
    }

    if( ! current_user_can( 'edit_users' ) ) {
        return;
    }

    wp_enqueue_script( 'change-username', plugins_url( '/assets/js/script.min.js', CHANGE_USERNAME_FILE ), array(), CHANGE_USERNAME_VERSION, true );
    wp_localize_script( 'change-username', 'change_username', array(
        'nonce' => wp_create_nonce( 'change_username' ),
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
    ));
}

/**
 * Handles the AJAX request for changing a username.
 */
function ajax_handler() {
    $response = array(
        'success' => false,
        'new_nonce' => wp_create_nonce( 'change_username' )
    );

    // check caps
    if( ! current_user_can( 'edit_users' ) ) {
        $response['message'] = 'You do not have the required capability to do that.';
        wp_send_json($response);
    }

    // validate nonce
    check_ajax_referer( 'change_username' );

    // validate request
    if( empty( $_POST['new_username'] ) || $_POST['old_username'] ) {
        $response['message'] = 'Invalid request.';
        wp_send_json($response);
    }

    // validate new username
    $new_username = trim( strip_tags( $_POST['new_username'] ) );
    if( ! validate_username( $new_username ) ) {
        $response['message'] = __( 'This username is invalid because it uses illegal characters. Please enter a valid username.' );
        wp_send_json($response);
    }

    // check if username is not in list of illegal logins
    /** This filter is documented in wp-includes/user.php */
    $illegal_user_logins = array_map( 'strtolower', (array) apply_filters( 'illegal_user_logins', array() ) );
    if ( in_array( $new_username, $illegal_user_logins ) ) {
        $response['message'] =  __( 'Sorry, that username is not allowed.' );
        wp_send_json($response);
    }

    // check if new username is in use already
    if( username_exists( $new_username ) ) {
        $response['message'] = sprintf( '<strong>%s</strong> is already in use.', $new_username );
        wp_send_json($response);
    }

    // change the username
    $old_username = trim( strip_tags( $_POST['current_username'] ) );
    change_username( $old_username, $new_username );

    // success response
    $response['success'] = true;
    $response['message'] = sprintf( 'Username successfully changed to <strong>%s</strong>.', $new_username );
    wp_send_json($response);
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
    $q  = $wpdb->prepare( "UPDATE $wpdb->users SET user_login = %s WHERE user_login = %s", $new_username, $old_username );
    $wpdb->query($q);

    // change nicename if needed
    $q = $wpdb->prepare( "UPDATE $wpdb->users SET user_nicename = %s WHERE user_login = %s AND user_nicename = %s", $new_username, $new_username, $old_username );
    $wpdb->query($q);

    // change display name if needed
    $q  = $wpdb->prepare( "UPDATE $wpdb->users SET display_name = %s WHERE user_login = %s AND display_name = %s", $new_username, $new_username, $old_username );
    $wpdb->query($q);

    // when on multisite, check if old username is in the `site_admins` options array. if so, replace with new username to retain superadmin rights.
    if( is_multisite() ) {
        $super_admins = (array) get_site_option( 'site_admins', array( 'admin' ) );
        $array_key = array_search( $old_username, $super_admins );
        if( $array_key ) {
            $super_admins[ $array_key ] = $new_username;
        }

        update_site_option( 'site_admins' , $super_admins );
    }

    /**
     * Fires right after a username is changed.
     *
     * @param string $old_username
     * @param string $new_username
     */
    do_action( 'change_username.username_changed', $old_username, $new_username );

    return true;

}
