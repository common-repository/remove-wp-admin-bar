<?php
/**
 * Uninstall Remove WP Admin Bar Plugin
 *
 * This file is responsible for uninstalling the Remove WP Admin Bar plugin.
 * If uninstall.php is not called by WordPress, die.
 *
 * @package Remove_WP_Admin_Bar
 */

// if uninstall.php is not called by WordPress, die.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    die;
}

$option_name = 'remove_wp_admin_bar_user_role';

delete_option( $option_name );

// for site options in Multisite.
delete_site_option( $option_name );
