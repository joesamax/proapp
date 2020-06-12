<?php
$subscription_endpoint = trailingslashit( get_bloginfo( 'url' ) ) . 'ml-api/v2/subscription';
$ml_paywall_settings = Mobiloud::get_option( 'ml_paywall_settings' );

global $allowedposttags;
$allowed_tags = $allowedposttags;
$allowed_tags['a']['onclick'] = true;
$allowed_tags['button']['onclick'] = true;

?>
<style type="text/css">
	<?php echo stripslashes( $ml_paywall_settings['pblock_css'] ); ?>
</style>

<div id="ml-paywall" class="ml-paywall">
	<div class="ml-paywall__wrap">
		<h2><?php echo wp_kses_post( stripslashes( $ml_paywall_settings['pblock_title'] ) ); ?></h2>
		<?php echo wp_kses( stripslashes( $ml_paywall_settings['pblock_content'] ), $allowed_tags ); ?>
		<a class="ml-paywall__button" onclick='nativeFunctions.handleButton( "subscription_screen", null, "<?php echo esc_url( $subscription_endpoint ); ?>" )'>
			<?php echo wp_kses_post( stripslashes( $ml_paywall_settings['pblock_button'] ) ); ?>
		</a>
	</div>
</div>
