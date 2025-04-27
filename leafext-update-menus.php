<?php
/**
 * Backend Menus
 *
 * @package Updates for plugins from hupe13 hosted on Github
 **/

// Direktzugriff auf diese Datei verhindern.
defined( 'ABSPATH' ) || die();

// Add settings to plugin page
function leafext_add_action_update_links( $actions ) {
	$actions[] = '<a href="' . esc_url( admin_url( 'admin.php' ) . '?page=github-settings' ) . '">' . esc_html__( 'Settings', 'leafext-update-github' ) . '</a>';
	return $actions;
}
add_filter( 'plugin_action_links_' . LEAFEXT_UPDATE_NAME . '/leafext-update-github.php', 'leafext_add_action_update_links' );

// Add settings to network plugin page
function leafext_network_add_action_update_links( $actions, $plugin ) {
	if ( $plugin === 'leafext-update-github/leafext-update-github.php' ) {
			$actions[] = '<a href="' . esc_url( admin_url( 'admin.php' ) . '?page=github-settings' ) . '">' . esc_html__( 'Settings', 'leafext-update-github' ) . '</a>';
	}
	return $actions;
}
add_filter( 'network_admin_plugin_action_links', 'leafext_network_add_action_update_links', 10, 4 );
