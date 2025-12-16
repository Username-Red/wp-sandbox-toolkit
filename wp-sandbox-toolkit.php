<?php
/**
 * Plugin Name: wp-sandbox-toolkit
 * Plugin URI:  https://github.com/Username-Red/wp-sandbox-toolkit
 * Description: A custom plugin boilerplate for adding scripts, styles, and future functionality.
 * Version:     4.0.2
 * Author:      Jared Greeff
 * Author URI:  https://github.com/Username-Red
 * License:     GPL2
 * Text Domain: wp-sandbox-toolkit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Get plugin version dynamically from plugin header.
 */
function wp_sandbox_toolkit_get_version() {
	static $version = null;

	if ( $version === null ) {
		$plugin_data = get_file_data( __FILE__, array( 'Version' => 'Version' ) );
		$version     = $plugin_data['Version'];
	}

	return $version;
}

/**
 * Enqueue frontend assets
 */
function my_custom_plugin_enqueue_assets() {
	$plugin_url = plugin_dir_url( __FILE__ );
	$plugin_path = plugin_dir_path( __FILE__ );
	$version = wp_sandbox_toolkit_get_version();

	// CSS
	wp_enqueue_style(
		'plugin-styles',
		$plugin_url . 'assets/css/plugin-styles.css',
		array(),
		filemtime( $plugin_path . 'assets/css/plugin-styles.css' ) // auto-update on change
	);

	// Main JS
	wp_enqueue_script(
		'plugin-scripts',
		$plugin_url . 'assets/js/plugin-scripts.js',
		array( 'jquery' ),
		filemtime( $plugin_path . 'assets/js/plugin-scripts.js' ),
		true
	);

	// alert.js
	wp_enqueue_script(
		'plugin-alert-script',
		$plugin_url . 'assets/js/alert.js',
		array(),
		filemtime( $plugin_path . 'assets/js/alert.js' ),
		true
	);

	// version.js (console logs plugin version)
	wp_enqueue_script(
		'plugin-version-js',
		$plugin_url . 'assets/js/version.js',
		array(),
		$version,
		true
	);

	wp_localize_script(
		'plugin-version-js',
		'wpSandboxToolkitVersion',
		$version
	);
}
add_action( 'wp_enqueue_scripts', 'my_custom_plugin_enqueue_assets' );

/**
 * Enqueue admin assets
 */
function my_custom_plugin_enqueue_admin_assets() {
	$plugin_url = plugin_dir_url( __FILE__ );
	$plugin_path = plugin_dir_path( __FILE__ );
	$version = wp_sandbox_toolkit_get_version();

	// Admin CSS
	wp_enqueue_style(
		'my-custom-plugin-admin-styles',
		$plugin_url . 'assets/css/plugin-styles.css',
		array(),
		filemtime( $plugin_path . 'assets/css/plugin-styles.css' )
	);

	// Admin JS
	wp_enqueue_script(
		'my-custom-plugin-admin-scripts',
		$plugin_url . 'assets/js/plugin-scripts.js',
		array( 'jquery' ),
		filemtime( $plugin_path . 'assets/js/plugin-scripts.js' ),
		true
	);

	// admin alert.js
	wp_enqueue_script(
		'my-custom-plugin-admin-alert',
		$plugin_url . 'assets/js/alert.js',
		array(),
		filemtime( $plugin_path . 'assets/js/alert.js' ),
		true
	);

	// version.js works for admin too
	wp_enqueue_script(
		'plugin-admin-version-js',
		$plugin_url . 'assets/js/version.js',
		array(),
		$version,
		true
	);

	wp_localize_script(
		'plugin-admin-version-js',
		'wpSandboxToolkitVersion',
		$version
	);
}
add_action( 'admin_enqueue_scripts', 'my_custom_plugin_enqueue_admin_assets' );

/**
 * Debug function
 */
function my_custom_plugin_custom_function() {
	error_log( 'My Custom Boilerplate Plugin is active!' );
}
add_action( 'init', 'my_custom_plugin_custom_function' );

/**
 * Gravity Forms webhook
 */
function my_custom_plugin_send_form_to_webhook( $entry, $form ) {
	error_log( 'wp-sandbox-toolkit loaded successfully' );

	$data = array();
	foreach ( $form['fields'] as $field ) {
		$field_id = $field->id;
		$label    = ! empty( $field->label ) ? $field->label : 'Field ' . $field_id;
		$value    = rgar( $entry, $field_id );
		$data[ $label ] = $value;
	}

	$webhook_url = 'https://webhook.site/48db2220-435a-433a-bc19-7dbd9f1cabf2';

	$response = wp_remote_post( $webhook_url, array(
		'method'  => 'POST',
		'body'    => json_encode( $data ),
		'headers' => array( 'Content-Type' => 'application/json' ),
		'timeout' => 10
	) );

	if ( is_wp_error( $response ) ) {
		error_log( 'Webhook failed: ' . $response->get_error_message() );
	} else {
		error_log( 'Webhook Data: ' . print_r( $data, true ) );
	}
}

if ( function_exists( 'rgar' ) && class_exists( 'GFForms' ) ) {
	add_action( 'gform_after_submission', 'my_custom_plugin_send_form_to_webhook', 10, 2 );
}
