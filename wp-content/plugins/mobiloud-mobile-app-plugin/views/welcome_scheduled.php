<!-- step 3 -->
<?php
if ( ! empty( $_GET['id'] ) ) {
	if ( ! empty( $_GET['id'] ) && ! empty( $_GET['time'] ) && ! empty( $_GET['timezone'] ) ) {
		// use data from Appointlet
		$info = array(
			'id'       => sanitize_text_field( $_GET['id'] ),
			'time'     => sanitize_text_field( urldecode( $_GET['time'] ) ),
			'timezone' => sanitize_text_field( urldecode( $_GET['timezone'] ) ),
		);
		$id   = $info['id'];

//		$reschedule_link = "https://mobiloud.appointlet.com/booking/{$id}/reschedule";
		$datetime        = DateTime::createFromFormat( 'Y-m-d\TH:i:s+', $info['time'] );
		if ( $datetime && class_exists( 'DateTimeZone' ) ) {
			$timezone       = $info['timezone'];
			$timezone_value = new DateTimeZone( $timezone );
			$offset         = $timezone_value->getOffset( new DateTime() );

			$time = $datetime->getTimestamp() + $offset;
			$date = date( get_option( 'date_format' ), $time ) . ' at ' . date( get_option( 'time_format' ), $time );
		}
	}
	// no need to show welcome screen again
	Mobiloud_Admin::welcome_screen_set_not_avalaible();
}
?>

<div class="ml2-block ml2-welcome-block">
	<div class="ml2-body">
		<h3 class="text-center title_big">Your call has been scheduled!</h3>
		<br>
		<p class="text-center">We look forward to speaking with you and showing you what MobiLoud can do for you! You can ask any questions during the call, but feel free to email us beforehand: <a href="mailto:support@mobiloud.com">support@mobiloud.com</a>.</p>
		<?php if ( ! empty( $_GET['id'] ) && $datetime ) : ?>

			<div class="ml-scheedule-time-block">
				<p>Your call is scheduled for <?php echo esc_html( $date ) . esc_html( $timezone ); ?></p>
			</div>

		<?php else : ?>
			<div class="ml-scheedule-time-block">
				<p>Your call is scheduled. Check your email for call times and to reschedule. We'll send a reminder prior to the call.</p>
			</div>
			<?php // no need to show welcome screen again
			Mobiloud_Admin::welcome_screen_set_not_avalaible();
			?>
		<?php endif; ?>
		<p class="text-center"><a href="#" data-href="<?php echo( esc_attr( $active_url ) ); ?>" class="welcome_question_start">Return to the plugin.</a></p>
		<br>
		<br>
	</div>
</div>
