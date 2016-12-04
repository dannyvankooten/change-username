<?php

namespace change_username;

function enqueue_assets() {
    global $pagenow;

    if( ! in_array( $pagenow, array( 'profile.php', 'user-edit.php' ) ) ) {
        return;
    }

    if( ! current_user_can( 'edit_users' ) ) {
        return;
    }

    wp_enqueue_script( 'change-username', plugins_url( '/assets/js/script.js', CHANGE_USERNAME_FILE ), array(), CHANGE_USERNAME_VERSION, true );
    wp_localize_script( 'change-username', 'change_username', array(
        'nonce' => wp_create_nonce( 'change_username' ),
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
    ));
}

function ajax_handler() {
    $response = array(
        'success' => false,
        'new_nonce' => wp_create_nonce( 'change_username' )
    );

    // check caps
    if( ! current_user_can( 'edit_users' ) || empty( $_POST['new_username'] ) ) {
        $response['message'] = 'You do not have permissions to that.';
        wp_send_json($response);
    }

    // validate nonce
    check_ajax_referer( 'change_username' );

    // check if new username is in use already
    $new_username = sanitize_user( $_POST['new_username'], true );
    $old_username = $_POST['current_username'];
    if( username_exists( $new_username ) ) {
        $response['message'] = sprintf( '<strong>%s</strong> is already in use.', $new_username );
        wp_send_json($response);
    }

    change_username( $old_username, $new_username );

    // success response
    $response['success'] = true;
    $response['message'] = sprintf( 'Username successfully changed to <strong>%s</strong>.', $new_username );
    wp_send_json($response);
}

/**
 * @param string $old_username
 * @param string $new_username
 */
function change_username( $old_username, $new_username ) {
    global $wpdb;

    // change username
    $q  = $wpdb->prepare( "UPDATE $wpdb->users SET user_login = %s WHERE user_login = %s", $new_username, $old_username );
    $wpdb->query($q);

    // change nicename if needed
    $q = $wpdb->prepare( "UPDATE $wpdb->users SET user_nicename = %s WHERE user_login = %s AND user_nicename = %s", $new_username, $new_username, $old_username );
    $wpdb->query($q);

    // change display name if needed
    $q  = $wpdb->prepare( "UPDATE $wpdb->users SET display_name = %s WHERE user_login = %s AND display_name = %s", $new_username, $new_username, $old_username );
    $wpdb->query($q);
}