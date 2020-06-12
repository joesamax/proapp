<?php
ini_set( 'display_errors', 0 );
if ( ! defined( 'MOBILOUD_API_REQUEST' ) ) {
	require_once( dirname( __FILE__ ) . '/api/compability.php' );
	ml_compability_api_result( 'comments' );
}

require 'comments/comment_iphone.php';

$show_avatars = get_option( 'show_avatars' );

function ml_render_comment( $comment, $platform = 'iphone', $reply = false ) {
	if ( $platform == 'iphone' ) {
		ml_render_iphone_comment( $comment, $reply );
	}
}

function ml_render_comments( $post_id, $platform = 'iphone', $offset = 0 ) {
	$parameters = array(
		'post_id'      => $post_id,
		// 'number' => 10,
		'offset'       => $offset,
		'status'       => 'approve',
		'order'        => 'ASC',
		'hierarchical' => 'threaded',
	);

	$comments = get_comments( $parameters );

	if ( count( $comments ) == 0 ) {
		?><h4 style='text-align: center;'><?php _e( 'No Comments' ); ?></h4><?php
	} else {
		foreach ( $comments as $comment ) {
			ml_render_comment( $comment, $platform );
		}
	}
}

function ml_render_comment_replies( $comment_id, $platform = 'iphone', $offset = 0 ) {
	$comment = get_comment( $comment_id );
	ml_render_comment( $comment, $platform, true );
}

if ( is_user_logged_in() ) {
	$c_user = wp_get_current_user();

	wp_set_current_user( $c_user->ID, $c_user->user_login );
	wp_set_auth_cookie( $user_id, true );
	do_action( 'wp_login', $c_user->user_login, $c_user );
}


?><!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, minimum-scale=1, maximum-scale=1, user-scalable=no">

	<?php

	function ml_comments_stylesheets() {
		wp_enqueue_style( 'mobiloud-typeplate', plugins_url( 'mobiloud-mobile-app-plugin/comments/css/_typeplate.css' ) );
		wp_enqueue_style( 'onsenui', plugins_url( 'mobiloud-mobile-app-plugin/libs/onsen/css/onsenui.min.css' ) );
		wp_enqueue_style( 'onsen-components', plugins_url( 'mobiloud-mobile-app-plugin/libs/onsen/css/onsen-css-components.min.css' ) );
		wp_enqueue_style( 'mobiloud-comments', plugins_url( 'mobiloud-mobile-app-plugin/comments/css/styles.css' ) );
	}

	function ml_comments_scripts() {
		wp_enqueue_script( 'onsenui', plugins_url( 'mobiloud-mobile-app-plugin/libs/onsen/js/onsenui.min.js' ), array(), false, true );

		wp_enqueue_script( 'mobiloud-list', plugins_url( 'mobiloud-mobile-app-plugin/comments/js/comments.js' ), array( 'onsenui' ), false, true );
	}

	remove_all_actions( 'wp_head' );
	remove_all_actions( 'wp_footer' );
	remove_all_actions( 'wp_print_styles' );
	remove_all_actions( 'wp_enqueue_scripts' );
	remove_all_actions( 'locale_stylesheet' );
	remove_all_actions( 'wp_print_head_scripts' );
	remove_all_actions( 'wp_print_footer_scripts' );
	remove_all_actions( 'wp_shortlink_wp_head' );

	add_action( 'wp_print_styles', 'ml_comments_stylesheets' );
	add_action( 'wp_print_footer_scripts', 'ml_comments_scripts', 30 );
	add_action( 'wp_print_footer_scripts', '_wp_footer_scripts', 30 );

	add_action( 'wp_head', 'wp_print_styles' );
	add_action( 'wp_footer', 'wp_print_footer_scripts', 20 );

	wp_head();


	$custom_css = stripslashes( get_option( 'ml_post_custom_css' ) );
	echo $custom_css ? '<style type="text/css" media="screen">' . $custom_css . '</style>' : '';
	?>
</head>
<body class="comments <?php echo ( ! $show_avatars ) ? 'no-avatars' : '' ?>">
<ons-list id="comment-list" class="comment-list" data-post-id="<?php echo sanitize_text_field( $_GET['post_id'] ); ?>">
	<?php
	if ( isset( $_GET['post_id'] ) && isset( $_GET['comment'] ) ) {
		ml_render_comment_replies( sanitize_text_field( $_GET['comment'] ) );
	} else {
		ml_render_comments( sanitize_text_field( $_GET['post_id'] ) );
	}
	?>
</ons-list>
<div id="ml-comment-form" class="comment-form <?php if ( is_user_logged_in() ) { echo 'logged-in'; } ?>">

	<?php if( get_option('comment_registration') && ! is_user_logged_in() ) : ?>
		<div class="ml-comment-login"><a class="ml_button" onclick="nativeFunctions.handleButton('login', null, null);"><?php _e('Log in to leave a Comment'); ?></a></div>
	<?php else : ?>

		<?php
		// Check if we have the email and name header set
		$have_info = ( isset( $_SERVER['HTTP_X_ML_COMMENTER'] ) );
		$commenter = explode( '|', $_SERVER['HTTP_X_ML_COMMENTER'] );

		if ( is_user_logged_in() ) {
			$src = $show_avatars ? Mobiloud::ml_get_avatar_url( get_current_user_id(), 60 ) : '';
			$have_info = true;
		} else {
			$src = $show_avatars ? Mobiloud::ml_get_avatar_url( $commenter[1], 60 ) : '';
		}


		if ( $show_avatars ) :
			?>
			<img id="form-avatar" src="<?php echo $src; ?>" />
		<?php
		endif;
		?>

		<input type="hidden" name="ml_commenter_name" id="ml-commenter-name" value="<?php echo esc_attr( $commenter[0] ); ?>" />
		<input type="hidden" name="ml_commenter_email" id="ml-commenter-email" value="<?php echo esc_attr( $commenter[1] ); ?>" />

		<textarea id="comment-form-text" class="textarea <?php echo $have_info ? 'has-details' : ''; ?>" rows="1" placeholder="<?php esc_attr_e( 'Leave a Comment' ); ?>"></textarea>
		<ons-icon onclick="submitCommentForm()" id="comment-submit" icon="md-mail-send"></ons-icon>

	<?php endif; ?>
</div>

<?php
$userEmail = '';
$userName = '';
if ( is_user_logged_in() ) {
	$nonce = wp_create_nonce( 'ml_post_comment' );
	?>
	<input type="hidden" id="restNonce" value="<?php echo $nonce ?>" />
	<input type="hidden" id="mlValidationHeader" value="<?php echo esc_attr( $_SERVER['HTTP_X_ML_VALIDATION'] ); ?>" />
	<?php
	$userEmail = $c_user->user_email;
	$userName = $c_user->display_name;
}
?>

<ons-modal id="infoModal" direction="up">
	<div style="text-align: center; margin-top: 30px;">

		<p>
			<ons-input id="commenter-name" value="<?php echo esc_attr($userName); ?>"  modifier="underbar" placeholder="<?php esc_attr_e( 'Username' ); ?>" float></ons-input>
		</p>
		<p>
			<ons-input id="commenter-email" value="<?php echo esc_attr($userEmail); ?>" modifier="underbar" placeholder="<?php esc_attr_e( 'Email' ); ?>" float></ons-input>
		</p>
		<p style="margin-top: 30px;">
			<ons-button onclick="saveUserDetails()"><?php _e( 'Save' ); ?></ons-button>
			<ons-button onclick="closeModal()"><?php _e( 'Cancel' ); ?></ons-button>
		</p>
	</div>

</ons-modal>

<ons-toast id="errorToast" animation="ascend">
	<span id="err-message"><?php
	_e( '<strong>ERROR</strong>: Please enter a username.' );
	_e( '<strong>ERROR</strong>: The email address isn&#8217;t correct.' );
	?></span>
	<button onclick="errorToast.hide()"><?php _e( 'OK' ); ?></button>
</ons-toast>

<script data-cfasync="false">
	// Cloudflare
	var __cfRLUnblockHandlers = 1 ;

	var pluginUrl = '<?php echo plugins_url( 'mobiloud-mobile-app-plugin/comments/process.php' ); ?>';
	var commentsEndpoint = '<?php echo admin_url('admin-ajax.php'); ?>';
	var commentReplyTo = '<?php echo esc_attr( sanitize_text_field( $_GET['comment'] ) ); ?>';
	var ml_comments = <?php echo wp_json_encode( array(
		'awaiting'      => __( 'Your comment is awaiting moderation.' ),
		'spam'          => __( 'This comment is currently marked as spam.' ),
		'just_now'      => human_time_diff( time(), time() ),
		'invalid_email' => __( '<strong>ERROR</strong>: The email address isn&#8217;t correct.' ),
		'user_login'    => __( '<strong>ERROR</strong>: Please enter a username.' ),
	) ); ?>;
</script>

<?php wp_footer(); ?>

</body>
</html>
