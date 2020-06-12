<?php
global $current_user;
$user_email     = Mobiloud::get_option( 'ml_user_email', $current_user->user_email );
$user_name      = Mobiloud::get_option( 'ml_user_name' );
$user_site      = get_site_url();
$plugin_url     = plugins_url();
$plugin_version = MOBILOUD_PLUGIN_VERSION;

$http_prefix = 'http';
if ( ( ! empty( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] !== 'off' ) || $_SERVER['SERVER_PORT'] == 443 ) {
	$http_prefix = 'https';
}
?>
<div class="ml2-sidebar">
	<?php if ( Mobiloud_Admin::welcome_screen_is_avalaible() ) : ?>
		<div class="ml2-preview">
			<a href="?page=mobiloud&tab=welcome"
				class="button button-hero button-primary">
				Request a Demo
			</a>
		</div>
	<?php endif; ?>

	<div class="ml2-side-block">
		<div class="ml2-side-header">Help & Support</div>
		<div class="ml2-side-body">
			<p>Make sure to check our Help Center for more details on how to build your app.</p>
			<p><a href="https://www.mobiloud.com/help/?utm_source=news-plugin&utm_medium=sidebar" target="_blank">MobiLoud Help Center</a></p>
			<p><a href="mailto:support@mobiloud.com">Send us an email</a></p>
			<p><a href="https://calendly.com/mobiloud/support">Schedule a call</a></p>
			<?php if ( Mobiloud_Admin::no_push_keys() && ! Mobiloud_Admin::welcome_screen_is_avalaible() ) : ?>
				<p><a href="https://mobiloud.com/demo/?utm_source=news-plugin&utm_medium=sidebar">Request a demo</a></p>
			<?php endif; ?>
		</div>
	</div>
	<?php if ( ! Mobiloud_Admin::no_push_keys() ) { ?>

		<div class="ml2-side-block">
			<div class="ml2-side-header">Like our service?</div>
			<div class="ml2-side-body">
				<p>Don't forget to rate it with 5 stars on WordPress.org!</p>
				<p><a href="https://wordpress.org/support/plugin/mobiloud-mobile-app-plugin/reviews/#new-post" target="_blank">Rate this plugin</a></p>
			</div>
		</div>
	<?php } ?>
</div>
