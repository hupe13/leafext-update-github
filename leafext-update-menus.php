<?php
/**
 * Backend Menus
 *
 * @package Updates for Leaflet Map Extensions and DSGVO Github Versions
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

// Display array as table
if ( ! function_exists( 'leafext_html_table' ) ) {
	function leafext_html_table( $data = array() ) {
		$rows      = array();
		$cellstyle = ( is_singular() || is_archive() ) ? "style='border:1px solid #195b7a;'" : '';
		foreach ( $data as $row ) {
			$cells = array();
			foreach ( $row as $cell ) {
				$cells[] = '<td ' . $cellstyle . ">{$cell}</td>";
			}
			$rows[] = '<tr>' . implode( '', $cells ) . '</tr>' . "\n";
		}
		$head = '<div style="width:' . ( ( is_singular() || is_archive() ) ? '100' : '80' ) . '%;">';
		$head = $head . '<figure class="wp-block-table aligncenter is-style-stripes"><table border=1>';
		return $head . implode( '', $rows ) . '</table></figure></div>';
	}
}
