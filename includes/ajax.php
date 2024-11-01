<?php

if ( !defined( 'ABSPATH' ) ) {
	die;
}

add_action( 'wp_ajax_wptw_ajax_create_wall', 'wptw_ajax_create_wall' );
add_action( 'wp_ajax_wptw_ajax_update_wall', 'wptw_ajax_update_wall' );
add_action( 'wp_ajax_wptw_ajax_delete_wall', 'wptw_ajax_delete_wall' );
add_action( 'wp_ajax_wptw_ajax_is_limit_reached', 'wptw_ajax_is_limit_reached' );
add_action( 'wp_ajax_wptw_ajax_get_wall', 'wptw_ajax_get_wall' );

// Creates tweet wall via AJAX
function wptw_ajax_create_wall() {
	global $wpdb;
	$nonce = isset($_POST['nonce']) ? $_POST['nonce'] : '';

	if ( !wp_verify_nonce( $nonce, 'wptw-nonce' ) ) {
		die ( 'No nonce');
	}
        
	$title = isset($_POST['title']) ? sanitize_text_field( $_POST['title'] ) : '';
	$description = isset($_POST['description']) ? sanitize_textarea_field( $_POST['description'] ) : '';
	$tweets = isset($_POST['tweets']) ? $_POST['tweets'] : [];

	// Santize tweets
	foreach ($tweets as $key => $tweet) {
		$tweets[$key] = sanitize_text_field( $tweet );
	}

	$wall_id = wptw_create_wall($title, $description, $tweets);
	$wall = wptw_get_wall($wall_id);
	$data = [
		'id' => $wall_id,
		'title' => $title,
		'description' => $description,
		'tweets' => $tweets,
		'html' => wptw_wall_html($wall)
	];
	echo json_encode($data);
	wp_die();
}

// Updates tweet wall via AJAX
function wptw_ajax_update_wall() {
	global $wpdb;
	$nonce = isset($_POST['nonce']) ? $_POST['nonce'] : '';

	if ( !wp_verify_nonce( $nonce, 'wptw-nonce' ) ) {
		die ( 'No nonce');
	}

	$id = isset($_POST['id']) ? sanitize_text_field( $_POST['id'] ) : '';
	$title = isset($_POST['title']) ? sanitize_text_field( $_POST['title'] ) : '';
	$description = isset($_POST['description']) ? sanitize_textarea_field( $_POST['description'] ) : '';
	$tweets = isset($_POST['tweets']) ? $_POST['tweets'] : [];

	// Santize tweets
	foreach ($tweets as $key => $tweet) {
		$tweets[$key] = sanitize_text_field( $tweet );
	}

	$wall_id = wptw_update_wall($id, $title, $description, $tweets);
	$wall = wptw_get_wall($wall_id);
	$data = [
		'title' => $title,
		'description' => $description,
		'tweets' => $tweets,
		'html' => wptw_wall_html($wall)
	];
	echo json_encode($data);
	wp_die();
}

// Deletes tweet wall via AJAX
function wptw_ajax_delete_wall() {
	global $wpdb;
	$nonce = isset($_POST['nonce']) ? $_POST['nonce'] : '';

	if ( !wp_verify_nonce( $nonce, 'wptw-nonce' ) ) {
		die ( 'No nonce');
	}

	$id = isset($_POST['id']) ? sanitize_text_field( $_POST['id'] ) : '';
	$wall = wptw_delete_wall($id);
	
	echo json_encode($wall);
	wp_die();
}

// Gets tweet wall via AJAX
function wptw_ajax_get_wall() {
	global $wpdb;
	$nonce = isset($_POST['nonce']) ? $_POST['nonce'] : '';

	if ( !wp_verify_nonce( $nonce, 'wptw-nonce' ) ) {
		die ( 'No nonce');
	}

	$id = isset($_POST['id']) ? sanitize_text_field( $_POST['id'] ) : '';
	$wall = wptw_get_wall($id);
	
	echo json_encode($wall);
	wp_die();
}

// Checks whether the limit is reached via AJAX
function wptw_ajax_is_limit_reached() {
	global $wpdb;
	$nonce = isset($_POST['nonce']) ? $_POST['nonce'] : '';

	if ( !wp_verify_nonce( $nonce, 'wptw-nonce' ) ) {
		die ( 'No nonce');
	}
	
	$data = [
		'limit_reached' => wptw_is_limit_reached()
	];
	echo json_encode($data);
	wp_die();
}