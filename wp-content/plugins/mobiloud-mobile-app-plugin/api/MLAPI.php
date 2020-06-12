<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MLAPI {

	/**
	 * Add public query vars
	 *
	 * @return array $vars
	 */
	public static function add_query_vars( $vars ) {
		$vars[] = '__ml-api';

		return $vars;
	}

	/**
	 * Add Endpoint
	 *
	 * @return void
	 */
	public static function add_endpoint() {
		foreach ( Mobiloud::get_rules() as $rule => $variable ) {
			add_rewrite_rule( $rule, 'index.php?__ml-api=' . $variable, 'top' );
		}
	}

	/**
	 * Check Requests, handle endpoints
	 */
	public static function check_requests() {
		global $wp, $wp_rewrite;

		self::handle_user_token();

		// Plain permalinks structure? Check for one of endpoints manually.
		if ( ! $wp_rewrite->using_permalinks() ) {
			$pathinfo         = isset( $_SERVER['PATH_INFO'] ) ? $_SERVER['PATH_INFO'] : '';
			list( $pathinfo ) = explode( '?', $pathinfo );
			$pathinfo         = str_replace( '%', '%25', $pathinfo );

			if ( isset( $_SERVER['REQUEST_URI'] ) ) {
				list( $req_uri, $params ) = explode( '?', $_SERVER['REQUEST_URI'] . ( false === strpos( $_SERVER['REQUEST_URI'], '?' ) ? '?' : '' ) );

				$home_path       = trim( wp_parse_url( home_url(), PHP_URL_PATH ), '/' );
				$home_path_regex = sprintf( '|^%s|i', preg_quote( $home_path, '|' ) );

				// Trim path info from the end and the leading home path from the front.
				// For path info requests, this leaves us with the requesting filename, if any.
				$req_uri = str_replace( $pathinfo, '', $req_uri );
				$req_uri = trim( $req_uri, '/' );
				$req_uri = preg_replace( $home_path_regex, '', $req_uri );
				$req_uri = trim( $req_uri, '/' );

				// Try to match with one of endpoints.
				$list = Mobiloud::get_rules();
				foreach ( $list as $rule => $variable ) {
					if ( preg_match( "!$rule!", $req_uri ) ) {
						$wp->set_query_var( '__ml-api', $variable );
						break;
					}
				}
			}
		}
		if ( ! self::ignore_request() ) {
			if ( isset( $wp->query_vars['__ml-api'] ) ) {
				Mobiloud::do_post_to_get_redirect();

				self::disable_new_relic();
				$api_endpoint_url = $wp->query_vars['__ml-api'];
				self::request( $api_endpoint_url );
				exit;
			}
		}
	}

	/**
	 * Handle Requests
	 *
	 * @return void
	 */
	protected static function request( $api_endpoint ) {
		define( 'MOBILOUD_API_REQUEST', true );
		switch ( $api_endpoint ) {
			case 'config':
				self::php_notices();
				self::add_headers( false );
				include_once MOBILOUD_PLUGIN_DIR . 'config.php';
				break;
			case 'menu':
				self::php_notices();
				self::add_headers( false );
				include_once MOBILOUD_PLUGIN_DIR . 'get_categories.php';
				break;
			case 'comments':
				include_once MOBILOUD_PLUGIN_DIR . 'comments.php';
				break;
			case 'sections':
				include_once MOBILOUD_PLUGIN_DIR . 'sections/sections.php';
				break;
			case 'disqus':
				include_once MOBILOUD_PLUGIN_DIR . '/comments/disqus.php';
				break;
			case 'page':
				include_once MOBILOUD_PLUGIN_DIR . 'get_page.php';
				break;
			case 'post':
				include_once MOBILOUD_PLUGIN_DIR . 'post/post.php';
				break;
			case 'list':
				include_once MOBILOUD_PLUGIN_DIR . 'loop/loop.php';
				break;
			case 'auth':
				include_once MOBILOUD_PLUGIN_DIR . 'auth/auth.php';
				break;
			case 'subscription':
				include_once MOBILOUD_PLUGIN_DIR . 'subscriptions/endpoint.php';
				break;
			case 'version':
				self::php_notices();
				self::add_headers( false );
				include_once MOBILOUD_PLUGIN_DIR . 'version.php';
				break;
			case 'login':
				self::php_notices();
				include_once MOBILOUD_PLUGIN_DIR . '/subscriptions/login.php';
				self::add_headers();
				break;
			case 'posts':
				include_once MOBILOUD_PLUGIN_DIR . '/api/controllers/MLApiController.php';
				$debug = true;

				// do_action( 'mobiloud_before_content_requests' );

				remove_all_actions( 'wp_login_failed' );
				remove_all_actions( 'authenticate' );

				$api = new MLApiController();
				$api->set_error_handlers( $debug );
				self::php_notices();
				self::add_headers();

				$custom_response = apply_filters( 'mobiloud_custom_posts_results', null );

				if ( ! empty( $custom_response ) ) {
					$response = $custom_response;
				} else {
					$response = $api->handle_request();
				}

				$api->send_response( $response );

				break;
			default:
				echo 'Mobiloud API v1.';
		}

	}

	private static function disable_new_relic() {
		if ( extension_loaded( 'newrelic' ) && function_exists( 'newrelic_disable_autorum' ) ) {
			newrelic_disable_autorum();
		}
	}

	public static function handle_user_token() {
		if ( isset ( $_SERVER['HTTP_X_ML_VALIDATION'] ) && ! empty ( $_SERVER['HTTP_X_ML_VALIDATION'] ) && strlen( $_SERVER['HTTP_X_ML_VALIDATION'] ) > 18 ) {
			// get auth header string
			$ml_token = explode( '|', sanitize_text_field( $_SERVER['HTTP_X_ML_VALIDATION'] ) );
			$ml_users = get_users( array('meta_key' => 'ml_auth_token', 'meta_value' => $ml_token[0] ) );

			if ( ! empty( $ml_users ) ) {
				wp_set_current_user( $ml_users[0]->ID );
				wp_set_auth_cookie( $ml_users[0]->ID, true );
			}

			if ( ! is_user_logged_in() ) {
				do_action( 'mobiloud_user_token_invalid' );
			} else {
				header( 'X-ML-VALIDATION: ' . $ml_token[0] . '|' . time() );
				return true;
			}
		} else {
			return false;
		}
	}

	private static function add_headers( $is_private = true, $is_json = true ) {
		if ( $is_json ) {
			header( 'Content-Type: application/json' );
		}
		$time = absint( Mobiloud::get_option( 'ml_cache_expiration', 30 ) ) * 60;
		header( 'Cache-Control: ' . ( $is_private ? 'private' : 'public' ) . ", max-age=$time, s-max-age=$time", true );
	}

	private static function php_notices() {
		if ( get_option( 'ml_disable_notices', true ) ) {
			$level = error_reporting();
			error_reporting( $level & ~E_NOTICE & ~E_WARNING & ( defined( 'E_STRICT' ) ? ~E_STRICT : 1 ) & ( defined( 'E_DEPRECATED' ) ? ~E_DEPRECATED : 1 ) );
		}
	}

	private static function ignore_request() {
		if ( isset( $_POST['gform_ajax'] ) && class_exists( 'RGForms' ) ) {
			add_action( 'wp', array( 'RGForms', 'ajax_parse_request' ), 10 );
			return true;
		}
		return false;
	}
}
