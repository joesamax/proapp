<?php
if ( ! defined( 'MOBILOUD_API_REQUEST' ) ) {
	require_once( dirname( __FILE__ ) . '/api/compability.php' );
	ml_compability_api_result( 'version' );
}

$info = array( 'version' => '4.1.0' );
if ( isset( $_GET['callback'] ) ) {
	$callback = sanitize_text_field( $_GET['callback'] );
	if ( $callback ) {
		echo $callback . '(';
	}
}
if ( strpos( $_SERVER['REQUEST_URI'], 'version' ) !== false ) {
	echo wp_json_encode( $info );
}
if ( isset( $callback ) ) {
	echo ')';
}
