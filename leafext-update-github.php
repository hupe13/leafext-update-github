<?php
/**
 * Plugin Name:       Update Management for Leaflet Map Extensions and DSGVO Github
 * Plugin URI:        https://github.com/hupe13/leafext-update-github
 * Description:       Update Management for Leaflet Map Extensions and DSGVO Github
 * Version:           250216
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            hupe13
 * Author URI:        https://leafext.de/en/
 * Network:           true
 * License:           GPL v2 or later
 *
 * @package Update Management for Leaflet Map and its Extensions Github
 **/

// Direktzugriff auf diese Datei verhindern.
defined( 'ABSPATH' ) || die();

// hide the plugin on sites that are not the main site.
add_filter(
	'all_plugins',
	function ( $plugins ) {
		if ( get_current_blog_id() !== 1 ) {
			$plugin = 'leafext-update-github/leafext-update-github.php';
			unset( $plugins[ $plugin ] );
		}
		return $plugins;
	}
);

if ( ! is_admin() ) {
	return;
}

$get = map_deep( wp_unslash( $_GET ), 'sanitize_text_field' );
if ( isset ($get['action']) && $get['action'] === 'activate' ) {
	return;
}

define( 'LEAFEXT_UPDATE_FILE', __FILE__ ); // /pfad/wp-content/plugins/leafext-update-github/leafext-update-github.php .
define( 'LEAFEXT_UPDATE_DIR', plugin_dir_path( __FILE__ ) ); // /pfad/wp-content/plugins/leafext-update-github/ .
define( 'LEAFEXT_UPDATE_URL', WP_PLUGIN_URL . '/' . basename( LEAFEXT_UPDATE_DIR ) ); // https://url/wp-content/plugins/leafext-update-github/ .
define( 'LEAFEXT_UPDATE_NAME', basename( LEAFEXT_UPDATE_DIR ) ); // leafext-update-github

// for translating a plugin
function leafext_update_github_textdomain() {
	if ( get_locale() === 'de_DE' ) {
		load_plugin_textdomain( 'leafext-update-github', false, LEAFEXT_UPDATE_NAME . '/lang/' );
	}
}
add_action( 'plugins_loaded', 'leafext_update_github_textdomain' );

// Add settings to plugin page
function leafext_add_action_update_links( $actions ) {
	$actions[] = '<a href="' . esc_url( admin_url( 'admin.php' ) . '?page=' . LEAFEXT_UPDATE_NAME ) . '">' . esc_html__( 'Settings', 'leafext-update-github' ) . '</a>';
	return $actions;
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'leafext_add_action_update_links' );

// Add settings to network plugin page
function leafext_network_add_action_update_links( $actions, $plugin ) {
	if ( $plugin === 'leafext-update-github/leafext-update-github.php' ) {
			$actions[] = '<a href="' . esc_url( admin_url( 'admin.php' ) . '?page=' . LEAFEXT_UPDATE_NAME ) . '">' . esc_html__( 'Settings', 'leafext-update-github' ) . '</a>';
	}
		return $actions;
}
add_filter( 'network_admin_plugin_action_links', 'leafext_network_add_action_update_links', 10, 4 );

if ( ! function_exists( 'leafext_plugin_active' ) ) {
	function leafext_plugin_active( $plugin ) {
		if ( ! ( strpos( implode( ' ', get_option( 'active_plugins', array() ) ), '/' . $plugin . '.php' ) === false &&
			strpos( implode( ' ', array_keys( get_site_option( 'active_sitewide_plugins', array() ) ) ), '/' . $plugin . '.php' ) === false ) ) {
			return true;
		} else {
			return false;
		}
	}
}

if ( ! function_exists( 'get_plugins' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

// param slug of php file, returns dir
// in welchem Verzeichnis ist das Plugin installiert?
function leafext_github_dir( $slug ) {
	$all_plugins  = get_plugins();
	$is_installed = preg_grep( '/' . $slug . '.php/', array_keys( $all_plugins ) );
	if ( count( $is_installed ) === 0 ) {
		return '';
	} else {
		foreach ( $is_installed as $installed ) {
			$split = explode( '/', $installed );
			if ( $split[0] . '.php' !== $split[1] ) {
				return $split[0];
			}
		}
		return '';
	}
}

// Repos on Github
function leafext_get_repos() {
	$git_repos                           = array();
	$git_repos['extensions-leaflet-map'] = array(
		'url'     => 'https://github.com/hupe13/extensions-leaflet-map-github/',
		'local'   => WP_PLUGIN_DIR . '/' . leafext_github_dir( 'extensions-leaflet-map' ) . '/extensions-leaflet-map.php',
		'release' => false,
	);
	$git_repos['dsgvo-leaflet-map']      = array(
		'url'     => 'https://github.com/hupe13/extensions-leaflet-map-dsgvo/',
		'local'   => WP_PLUGIN_DIR . '/' . leafext_github_dir( 'dsgvo-leaflet-map' ) . '/dsgvo-leaflet-map.php',
		'release' => true,
	);
	foreach ( $git_repos as $git_repo => $value ) {
		// remove the same WordPress plugins from the $git_repos array
		if ( ! file_exists( $git_repos[ $git_repo ]['local'] ) ) {
			unset( $git_repos[ $git_repo ] );
		}
		// Falls es aktiv ist, ist PUC schon da.
		if ( leafext_plugin_active( $git_repo ) ) {
			unset( $git_repos[ $git_repo ] );
		}
	}
	$git_repos[ LEAFEXT_UPDATE_NAME ] = array(
		'url'     => 'https://github.com/hupe13/leafext-update-github/',
		'local'   => LEAFEXT_UPDATE_FILE,
		'release' => true,
	);
	return $git_repos;
}

use YahnisElsts\PluginUpdateChecker\v5\PucFactory;
use YahnisElsts\PluginUpdateChecker\v5p5\Vcs\GitHubApi;
require_once LEAFEXT_UPDATE_DIR . 'admin.php';
require_once LEAFEXT_UPDATE_DIR . 'pkg/plugin-update-checker/plugin-update-checker.php';

global $leafext_update_token;
global $leafext_github_denied;

if ( false === $leafext_github_denied || $leafext_update_token !== '' ) {
	$git_repos         = leafext_get_repos();
	$my_update_checker = array();
	foreach ( $git_repos as $git_repo => $value ) {
		if ( $git_repos[ $git_repo ]['local'] !== $git_repo ) {
			$my_update_checker[ $git_repo ] = PucFactory::buildUpdateChecker(
				$git_repos[ $git_repo ]['url'],
				$git_repos[ $git_repo ]['local'],
				basename( dirname( $git_repos[ $git_repo ]['local'] ) ),
			);

			// Set the branch that contains the stable release.
			$my_update_checker[ $git_repo ]->setBranch( 'main' );

			if ( $leafext_update_token !== '' ) {
				// Optional: If you're using a private repository, specify the access token like this:
				$my_update_checker[ $git_repo ]->setAuthentication( $leafext_update_token );
			}

			// update tags or release
			if ( ! $git_repos[ $git_repo ]['release'] ) {
				$my_update_checker[ $git_repo ]->addFilter(
					'vcs_update_detection_strategies',
					function ( $strategies ) {
						unset( $strategies[ GitHubApi::STRATEGY_LATEST_RELEASE ] );
						return $strategies;
					}
				);
			}
		}
	}
}

function leafext_update_puc_error( $error, $response = null, $url = null, $slug = null ) {
	if ( ! isset( $slug ) ) {
		return;
	}

	$git_repos  = leafext_get_repos();
	$valid_slug = false;
	foreach ( $git_repos as $git_repo => $value ) {
		if ( $slug === basename( dirname( $git_repos[ $git_repo ]['local'] ) ) ) {
			$valid_slug = true;
		}
	}
	if ( ! $valid_slug ) {
		return;
	}

	if ( wp_remote_retrieve_response_code( $response ) === 403 ) {
		// var_dump( 'Permission denied' );
		set_transient( 'leafext_github_403', true, DAY_IN_SECONDS );
	}
}
add_action( 'puc_api_error', 'leafext_update_puc_error', 10, 4 );
