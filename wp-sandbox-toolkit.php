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
 * =========================
 * ENQUEUE FRONTEND SCRIPTS & STYLES
 * =========================
 */
function my_custom_plugin_enqueue_assets() {
	$plugin_url = plugin_dir_url( __FILE__ );

	// Enqueue CSS
	wp_enqueue_style(
		'plugin-styles',
		$plugin_url . 'assets/css/plugin-styles.css',
		array(),
		'1.0.0',
		'all'
	);

	// Enqueue JavaScript
	wp_enqueue_script(
		'plugin-scripts',
		$plugin_url . 'assets/js/plugin-scripts.js',
		array( 'jquery' ),
		'1.0.0',
		true
	);

	// NEW: alert.js
	wp_enqueue_script(
		'plugin-alert-script',
		$plugin_url . 'assets/js/alert.js',
		array(),
		'1.0.0',
		true
	);
}
add_action( 'wp_enqueue_scripts', 'my_custom_plugin_enqueue_assets' );

/**
 * =========================
 * ENQUEUE ADMIN SCRIPTS & STYLES
 * =========================
 */
function my_custom_plugin_enqueue_admin_assets() {
	$plugin_url = plugin_dir_url( __FILE__ );

	// Admin CSS
	wp_enqueue_style(
		'my-custom-plugin-admin-styles',
		$plugin_url . 'assets/css/plugin-styles.css',
		array(),
		'1.0.0',
		'all'
	);

	// Admin JS
	wp_enqueue_script(
		'my-custom-plugin-admin-scripts',
		$plugin_url . 'assets/js/plugin-scripts.js',
		array( 'jquery' ),
		'1.0.0',
		true
	);

	// NEW: alert.js for admin
	wp_enqueue_script(
		'my-custom-plugin-admin-alert',
		$plugin_url . 'assets/js/alert.js',
		array(),
		'1.0.0',
		true
	);
}
add_action( 'admin_enqueue_scripts', 'my_custom_plugin_enqueue_admin_assets' );

/**
 * =========================
 * PLACEHOLDER FOR FUTURE FUNCTIONALITY
 * =========================
 */
function my_custom_plugin_custom_function() {
	// This is where you will add custom PHP functionality in the future.
	// For now, let's just log a test message
	error_log( 'My Custom Boilerplate Plugin is active!' );
}
add_action( 'init', 'my_custom_plugin_custom_function' );

/**
 * =========================
 * GRAVITY FORMS AFTER SUBMISSION WEBHOOK
 * =========================
 */
function my_custom_plugin_send_form_to_webhook( $entry, $form ) {

	// Convert entry data to array
	$data = array();
	foreach ( $form['fields'] as $field ) {
		$field_id = $field->id;
		$label    = ! empty( $field->label ) ? $field->label : 'Field ' . $field_id;
		$value    = rgar( $entry, $field_id );
		$data[ $label ] = $value;
	}

	// Webhook URL
	$webhook_url = 'https://webhook.site/48db2220-435a-433a-bc19-7dbd9f1cabf2';

	// Send POST request
	$response = wp_remote_post( $webhook_url, array(
		'method'  => 'POST',
		'body'    => json_encode( $data ),
		'headers' => array( 'Content-Type' => 'application/json' ),
		'timeout' => 10
	) );

	// Debug log success/failure
	if ( is_wp_error( $response ) ) {
		error_log( 'Webhook failed: ' . $response->get_error_message() );
	} else {
		error_log( 'Webhook Data: ' . print_r( $data, true ) );
	}
}

// Only hook if Gravity Forms is active
if ( function_exists( 'rgar' ) && class_exists( 'GFForms' ) ) {
	add_action( 'gform_after_submission', 'my_custom_plugin_send_form_to_webhook', 10, 2 );
}
