<?php
if ( ! defined( 'MOBILOUD_API_REQUEST' ) ) {
	require_once( dirname( dirname( __FILE__ ) ) . '/api/compability.php' );
	ml_compability_api_result( 'post', true );
}
// GET params used by app to query for posts / pages.
// This is part of an API endpoint, as are all nonce errors that were whitelisted with ignore flags

if ( ( ! isset( $_GET['post_id'] ) ) && ( ! isset( $_GET['page_ID'] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification
	header( 'HTTP/1.1 404 Not Found' );
	exit;
}

if ( extension_loaded( 'newrelic' ) ) {
	newrelic_disable_autorum();
}

if ( empty( $post_id ) ) {
	$post_id = htmlspecialchars( esc_attr( sanitize_text_field( $_GET['post_id'] ) ) );
	$post    = get_post( $post_id );
}

if ( empty( $post ) ) {
	header( 'HTTP/1.1 404 Not Found' );
	exit;
}

if ( empty( $_GET['related_posts'] ) && empty( $_GET['related'] ) ) {
	include dirname( __FILE__ ) . '/../views/post.php';
} else {
	include dirname( __FILE__ ) . '/related.php';
}
