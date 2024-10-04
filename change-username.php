<?php
/*
Plugin Name: Change Username
Description: Allows you to change the username of your WordPress users.
Version: 1.0.2
Author: ibericode
Author URI: https://ibericode.com/
License: GPL-3.0-or-later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Change Username - a WordPress plugin to change usernames
Copyright (C) 2016 Danny van Kooten

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

define( 'CHANGE_USERNAME_VERSION', '1.0.1' );
define( 'CHANGE_USERNAME_FILE', __FILE__ );

/** @ignore */
function _dvk_change_username_bootstrap() {
    // do nothing for public requests
    if( ! is_admin() ) {
        return;
    }

    require __DIR__ . '/src/functions.php';
    add_action( 'admin_enqueue_scripts', 'change_username\\enqueue_assets');
    add_action( 'wp_ajax_change_username', 'change_username\\ajax_handler');
}

add_action( 'plugins_loaded', '_dvk_change_username_bootstrap' );
