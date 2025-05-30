<?php
/**
 * Plugin Name:       Updates for plugins from hupe13 hosted on Github
 * Description:       If you have installed the Github versions of plugins from hupe13 on a multisite, you can receive the updates here.
 * Plugin URI:        https://leafext.de/en/
 * Update URI:        https://github.com/hupe13/leafext-update-github
 * Version:           250428
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            hupe13
 * Author URI:        https://leafext.de/en/
 * Network:           true
 * License:           GPL v2 or later
 *
 * @package Updates for plugins from hupe13 hosted on Github
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

define( 'LEAFEXT_UPDATE_FILE', __FILE__ ); // /pfad/wp-content/plugins/leafext-update-github/leafext-update-github.php .
define( 'LEAFEXT_UPDATE_DIR', plugin_dir_path( __FILE__ ) ); // /pfad/wp-content/plugins/leafext-update-github/ .
define( 'LEAFEXT_UPDATE_URL', WP_PLUGIN_URL . '/' . basename( LEAFEXT_UPDATE_DIR ) ); // https://url/wp-content/plugins/leafext-update-github/ .
define( 'LEAFEXT_UPDATE_NAME', basename( LEAFEXT_UPDATE_DIR ) ); // leafext-update-github

require_once LEAFEXT_UPDATE_DIR . 'leafext-update-menus.php';

// for translating a plugin
function leafext_update_github_plugin_textdomain() {
	if ( get_locale() === 'de_DE' ) {
		$ret = load_plugin_textdomain( 'leafext-update-github', false, LEAFEXT_UPDATE_NAME . '/github/lang/' );
	}
}
add_action( 'plugins_loaded', 'leafext_update_github_plugin_textdomain' );

// Github Update
if ( ! function_exists( 'leafext_get_repos' ) ) {
	require_once LEAFEXT_UPDATE_DIR . 'github/github-functions.php';
}
if ( ! function_exists( 'leafext_update_puc_error' ) ) {
	require_once LEAFEXT_UPDATE_DIR . 'github/github-settings.php';
	require_once LEAFEXT_UPDATE_DIR . 'github/github-check-update.php';
}
