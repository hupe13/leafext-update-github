<?php
/**
 * Backend Menus
 *
 * @package Manage Updates of Leaflet Map Extensions and DSGVO Github Versions
 **/

// Direktzugriff auf diese Datei verhindern.
defined( 'ABSPATH' ) || die();

// Add settings to plugin page
function leafext_add_action_update_links( $actions ) {
	$actions[] = '<a href="' . esc_url( admin_url( 'admin.php' ) . '?page=' . LEAFEXT_UPDATE_NAME ) . '">' . esc_html__( 'Settings', 'leafext-update-github' ) . '</a>';
	return $actions;
}
add_filter( 'plugin_action_links_' . LEAFEXT_UPDATE_NAME . '/' . 'leafext-update-github.php' , 'leafext_add_action_update_links' );

// Add settings to network plugin page
function leafext_network_add_action_update_links( $actions, $plugin ) {
	if ( $plugin === 'leafext-update-github/leafext-update-github.php' ) {
			$actions[] = '<a href="' . esc_url( admin_url( 'admin.php' ) . '?page=' . LEAFEXT_UPDATE_NAME ) . '">' . esc_html__( 'Settings', 'leafext-update-github' ) . '</a>';
	}
	return $actions;
}
add_filter( 'network_admin_plugin_action_links', 'leafext_network_add_action_update_links', 10, 4 );

/**
 * Add menu page for admin
 */
function leafext_update_add_page() {
	// Add Submenu.
	$leafext_admin_page = add_submenu_page(
		'options-general.php',
		'Github Update',
		'Github Update',
		'manage_options',
		LEAFEXT_UPDATE_NAME,
		'leafext_update_admin'
	);
}
add_action( 'admin_menu', 'leafext_update_add_page', 99 );

// Admin page for the plugin
function leafext_update_admin() {
	leafext_token_form();
	echo '<h3>' . esc_html__( 'Github Repositories managed by this plugin', 'leafext-update-github' ) . '</h3>';
	echo '<pre>';
	//phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_var_dump
	var_dump( leafext_get_repos() );
	echo '</pre>';
}
