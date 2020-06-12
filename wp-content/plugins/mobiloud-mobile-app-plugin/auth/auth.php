<?php
header( 'Content-type: application/json' );

$action = sanitize_text_field( $_GET['action'] );

$user = sanitize_text_field( $_POST['username'] );
$pass = sanitize_text_field( $_POST['password'] );

do_action('mobiloud_auth_custom_endpoint', array( 'post' => $_POST, 'action' => $action ) );

switch ( $action ) {
	default:
	case 'login':
		include_once MOBILOUD_PLUGIN_DIR . 'subscriptions/functions.php';
		$ml_user = ml_login_wordpress( $user, $pass );
		if ( ! isset( $ml_user->errors ) ) {
			$userID = get_current_user_id();
			$ml_token = get_user_meta( $userID, 'ml_auth_token', true );
			// Check if user already has a token
			if ( empty( $ml_token ) ) {
				// generate the token
				$ml_token = wp_hash( $userID );
				$created = update_user_meta( $userID, 'ml_auth_token', $ml_token );

				if ( ! $created ) {
					http_response_code( 401 );
					$response = array(
						'status' => 'failed',
						'message' => 'Failed to create token for user.',
					);
					die();
				}
			}
			// Send the validation header back
			header( 'X-ML-VALIDATION: ' . $ml_token . '|' . time() );
			$response = array(
				'status' => 'ok',
				'message' => 'Login successful!',
			);

		} else {
			http_response_code( 401 );
			$response = array(
				'status' => 'failed',
				'message' => 'Invalid username, email address or incorrect password',
				'errors' => $ml_user->errors,
			);
		}
		echo wp_json_encode( $response );
	break;
}
