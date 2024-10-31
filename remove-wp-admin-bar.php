<?php
/**
 * Plugin Name: Remove WP Admin Bar
 * Plugin URI: https://wordpress.org/plugins/remove-wp-admin-bar/
 * Description: This plugin is useful to remove/hide admin bar based on selected user roles.
 * Version: 1.4
 * Requires at least: 5.2
 * Requires PHP: 5.6
 * Author: Dhrumil Kumbhani
 * Author URI: https://in.linkedin.com/in/dhrumil-kumbhani-707b7b179?original_referer=https%3A%2F%2Fwww.google.com%2F
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: remove-wp-admin-bar
 *
 * @package Remove_WP_Admin_Bar
 */

/**
 * Activate the plugin.
 */
function remove_wp_admin_bar_plugin_activate() {
    if ( ! get_option( 'remove_wp_admin_bar_user_role' ) ) {
        update_option( 'remove_wp_admin_bar_user_role', '' );
    }
}
register_activation_hook( __FILE__, 'remove_wp_admin_bar_plugin_activate' );

/**
 * Admin menu create
 */
function remove_wp_admin_bar_admin_menu() {
    add_menu_page(
        __( 'Remove WP admin bar page' ),
        __( 'Remove WP admin bar menu' ),
        'manage_options',
        'remove-wp-admin-bar-page',
        'remove_wp_admin_bar_page_contents',
        'dashicons-schedule',
        3
    );
}
add_action( 'admin_menu', 'remove_wp_admin_bar_admin_menu' );

/**
 * Admin page settings
 */
function remove_wp_admin_bar_page_contents() {
    ?>
    <h1><?php esc_html_e( 'Welcome to the Remove WP admin bar plugin.' ); ?></h1>
    <form method="POST" action="options.php">
        <?php
        settings_fields( 'remove-wp-admin-bar-page' );
        do_settings_sections( 'remove-wp-admin-bar-page' );
        submit_button();
        ?>
    </form>
    <?php
}

/**
 * Admin page settings fields
 */
function remove_wp_admin_bar_settings_init() {

    add_settings_section(
        'remove_wp_admin_bar_setting_section',
        __( 'Custom settings' ),
        'remove_wp_admin_bar_callback_function',
        'remove-wp-admin-bar-page'
    );

    add_settings_field(
        'remove_wp_admin_bar_user_role',
        __( 'Select User Roles' ),
        'remove_wp_admin_bar_setting_markup',
        'remove-wp-admin-bar-page',
        'remove_wp_admin_bar_setting_section'
    );

    register_setting( 'remove-wp-admin-bar-page', 'remove_wp_admin_bar_user_role' );
}
add_action( 'admin_init', 'remove_wp_admin_bar_settings_init' );

/**
 * Admin page section
 */
function remove_wp_admin_bar_callback_function() {
}

/**
 * Admin page body
 */
function remove_wp_admin_bar_setting_markup() {
    $options = get_option( 'remove_wp_admin_bar_user_role', array() );
    $options_value = isset( $options['remove_wp_admin_bar_user_role'] )
        ? (array) $options['remove_wp_admin_bar_user_role']
        : array();
    $user_roles = array_keys( remove_wp_admin_bar_get_user_roles() );

    foreach ( $user_roles as $role ) {
        $escaped_roles = esc_html( $role ); // Store escaped value in a variable.
        if ( isset( $options['options_value'] ) && in_array( $role, $options['options_value'] ) ) {
            ?>
            <input class="hide-wp-bar-user-roles" type="checkbox" id="<?php echo esc_attr( $escaped_roles ); ?>" name="remove_wp_admin_bar_user_role[options_value][]" value="<?php echo esc_attr( $escaped_roles ); ?>" checked>
            <label for="<?php echo esc_attr( $escaped_roles ); ?>"><?php echo esc_html( $escaped_roles ); ?></label><br />
        <?php } else { ?>
            <input class="hide-wp-bar-user-roles" type="checkbox" id="<?php echo esc_attr( $escaped_roles ); ?>" name="remove_wp_admin_bar_user_role[options_value][]" value="<?php echo esc_attr( $escaped_roles ); ?>">
            <label for="<?php echo esc_attr( $escaped_roles ); ?>"><?php echo esc_html( $escaped_roles ); ?></label><br />
            <?php
        }
    }
}

/**
 * Get user roles
 *
 * @return object
 */
function remove_wp_admin_bar_get_user_roles() {
    global $wp_roles;

    $all_roles = $wp_roles->roles;
    $editable_roles = apply_filters( 'editable_roles', $all_roles );

    return $editable_roles;
}

/**
 * Front-end hide WP admin bar for the selected role only.
 */
function remove_wp_admin_bar_style() {
    $options = get_option( 'remove_wp_admin_bar_user_role', array() );
    $options_value = isset( $options['remove_wp_admin_bar_user_role'] )
        ? (array) $options['remove_wp_admin_bar_user_role'] : array();

    $user = new WP_User( get_current_user_id() );
    if ( isset( $options['options_value'] ) && in_array( $user->roles[0], $options['options_value'] ) ) {
        show_admin_bar( false );
    }
}
add_action( 'after_setup_theme', 'remove_wp_admin_bar_style' );
