<?php
/**
 * Plugin Name:       Updates for Leaflet Map Extensions and DSGVO Github Versions
 * Plugin URI:        https://github.com/hupe13/leafext-update-github
 * Description:       If you have installed the Github versions of Leaflet Map plugins from hupe13 on a multisite, you can receive the updates here.
 * Version:           250225
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            hupe13
 * Author URI:        https://leafext.de/en/
 * Network:           true
 * License:           GPL v2 or later
 *
 * @package Updates for Leaflet Map Extensions and DSGVO Github Versions
 **/

// Direktzugriff auf diese Datei verhindern.
defined( 'ABSPATH' ) || die();

// hide the plugin on sites that are not the main site.
add_filter(
	'all_plugins',
	function ( $plugins ) {
		if ( get_current_blog_id() !== 1 ) {
			$plugin = trailingslashit( basename( __DIR__ ) ) . basename( __FILE__ );
			unset( $plugins[ $plugin ] );
		}
		return $plugins;
	}
);

// Return if not admin
if ( ! is_admin() ) {
	return;
}

if ( ! is_main_site() ) {
	return;
}

// Return if a plugin de/activation is running
//phpcs:ignore WordPress.Security.NonceVerification.Recommended -- wird nur abgefragt, nonce wird woanders gesetzt
$get = map_deep( wp_unslash( $_GET ), 'sanitize_text_field' );
if ( isset( $get['action'] ) && $get['action'] === 'activate' ) {
	return;
}
if ( isset( $get['action'] ) && $get['action'] === 'deactivate' ) {
	return;
}
if ( isset( $get['deactivate'] ) && $get['deactivate'] === 'true' ) {
	return;
}
if ( isset( $get['activate'] ) && $get['activate'] === 'true' ) {
	return;
}

define( 'LEAFEXT_UPDATE_FILE', __FILE__ ); // /pfad/wp-content/plugins/leafext-update-github/leafext-update-github.php .
define( 'LEAFEXT_UPDATE_DIR', plugin_dir_path( __FILE__ ) ); // /pfad/wp-content/plugins/leafext-update-github/ .
define( 'LEAFEXT_UPDATE_URL', WP_PLUGIN_URL . '/' . basename( LEAFEXT_UPDATE_DIR ) ); // https://url/wp-content/plugins/leafext-update-github/ .
define( 'LEAFEXT_UPDATE_NAME', basename( LEAFEXT_UPDATE_DIR ) ); // leafext-update-github

// for translating a plugin
function leafext_update_github_textdomain() {
	if ( get_locale() === 'de_DE' ) {
		load_plugin_textdomain( 'leafext-update-github', false, LEAFEXT_UPDATE_NAME . '/github/lang/' );
	}
}
add_action( 'plugins_loaded', 'leafext_update_github_textdomain' );

require_once LEAFEXT_UPDATE_DIR . 'leafext-update-menus.php';

// Github Update
if ( ! function_exists( 'leafext_get_repos' ) ) {
	require_once LEAFEXT_UPDATE_DIR . 'github/github-functions.php';
}
if ( ! class_exists( 'YahnisElsts\PluginUpdateChecker\v5\PucFactory' ) ) {
	require_once LEAFEXT_UPDATE_DIR . 'github/github-settings.php';
	require_once LEAFEXT_UPDATE_DIR . 'github/github-check-update.php';
}
