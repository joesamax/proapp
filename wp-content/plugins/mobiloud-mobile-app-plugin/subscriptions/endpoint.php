<?php
ini_set( 'display_errors', 0 );

$subscription_endpoint = trailingslashit( get_bloginfo( 'url' ) ) . 'ml-api/v2/subscription';

?>
<!DOCTYPE html>
<html dir="<?php echo( get_option( 'ml_rtl_text_enable' ) === '1' ? 'rtl' : 'ltr' ); ?>">
<head>
	<meta charset="utf-8">
	<meta name="description" content="">
	<meta name="keywords" content="">
	<meta name="language" content="en"/>
	<meta name="viewport" content="width=device-width, minimum-scale=1, maximum-scale=1, user-scalable=no">

	<?php

	function ml_sections_stylesheets() {
		wp_enqueue_style( 'mobiloud-post', plugins_url( 'mobiloud-mobile-app-plugin/post/css/styles.css' ) );
		wp_enqueue_style( 'mobiloud-typeplate', plugins_url( 'mobiloud-mobile-app-plugin/post/css/_typeplate.css' ) );
	}

	remove_all_actions( 'wp_head' );
	remove_all_actions( 'wp_print_styles' );
	remove_all_actions( 'wp_enqueue_scripts' );
	remove_all_actions( 'locale_stylesheet' );
	remove_all_actions( 'wp_print_head_scripts' );
	remove_all_actions( 'wp_shortlink_wp_head' );

	add_action( 'wp_print_styles', 'ml_sections_stylesheets' );

	add_action( 'wp_head', 'wp_print_styles' );

	wp_head();

	$custom_css = stripslashes( get_option( 'ml_post_custom_css' ) );
	echo $custom_css ? '<style type="text/css" media="screen">' . $custom_css . '</style>' : '';

	$ml_paywall_settings = Mobiloud::get_option( 'ml_paywall_settings' );

	?>
	<style type="text/css">
		<?php echo wp_kses_post( stripslashes( $ml_paywall_settings['sblock_css'] ) ); ?>
	</style>
</head>
<body class="ml-subscription mb_body ml-platform-<?php echo esc_attr( strtolower( $_SERVER['HTTP_X_ML_PLATFORM'] ) ); ?>">
<div class="wrapper">
	<a id="ml-subscription-close" onclick="nativeFunctions.handleButton('close_screen', null, null)">+</a>
	<h2><?php echo esc_html( stripslashes( $ml_paywall_settings['sblock_title'] ) ); ?></h2>
	<?php
		global $allowedposttags;
		$allowed_tags = $allowedposttags;
		$allowed_tags['a']['onclick'] = true;
		$allowed_tags['button']['onclick'] = true;
	?>
	<?php echo wp_kses( stripslashes( $ml_paywall_settings['sblock_content'] ), $allowed_tags ); ?>
</div>
</body>
</html>

