<!-- step 2 -->
<?php
$return_url     = get_site_url();
$timezone_check = Mobiloud::get_option( 'ml_welcome_timezone', false );
?>
<div class="ml2-block ml2-welcome-block">

	<div class="ml2-body">
		<?php if ( $timezone_check ) : ?>
			<h3 class="text-center title_big">Find a convenient time to speak to one of us</h3>
			<p>We'll tell you all about the MobiLoud platform, you'll see examples, learn about our process and next steps to get your app launched.</p>
			<br>

<!--			<script src="https://www.appointletcdn.com/loader/loader.min.js"></script>-->
<!--			<input type="submit" name="submit" id="submit" class="button button-primary button-hero"-->
<!--				data-appointlet-organization="mobiloud"-->
<!--				data-appointlet-service="5627"-->
<!--				data-appointlet-email="--><?php //echo esc_attr( mobiloud::get_option( 'ml_user_email' ) ); ?><!--"-->
<!--				data-appointlet-field-name="--><?php //echo esc_attr( mobiloud::get_option( 'ml_user_name' ) ); ?><!--"-->
<!--				data-appointlet-field-phone="--><?php //echo esc_attr( mobiloud::get_option( 'ml_user_phone' ) ); ?><!--"-->
<!--				data-appointlet-field-site="--><?php //echo esc_attr( mobiloud::get_option( 'ml_user_site' ) ); ?><!--"-->
<!--				data-appointlet-field-message="--><?php //echo esc_attr( mobiloud::get_option( 'ml_user_message' ) ); ?><!--"-->
<!--				data-appointlet-field-company-name="--><?php //echo esc_attr( mobiloud::get_option( 'ml_user_company' ) ); ?><!--"-->
<!--				data-appointlet-field-type="--><?php //echo esc_attr( mobiloud::get_option( 'ml_user_apptype' ) ); ?><!--"-->
<!--				data-appointlet-field-utm-source="news-plugin"-->
<!--				data-appointlet-query-skip_fields="1"-->
<!--				data-appointlet-query-utm_source="news-plugin"-->
<!--				data-appointlet-field-redirect-url="--><?php //echo esc_attr( $return_url ); ?><!--"-->
<!--				data-open="--><?php //echo ( isset( $_GET['open'] ) ? 1 : 0 ); ?><!--"-->
<!--				value="Schedule Your Call">-->
<!--			<br><br><br><br>-->

			<!-- Calendly inline widget begin -->
			<div class="calendly-inline-widget" data-url="https://calendly.com/mobiloud/demo?hide_event_type_details=1&primary_color=80bf28&name=<?php echo esc_attr( mobiloud::get_option( 'ml_user_name' ) ); ?>&email=<?php echo esc_attr( mobiloud::get_option( 'ml_user_email' ) ); ?>&a1=<?php echo esc_attr( mobiloud::get_option( 'ml_user_site' ) ); ?>&a2=<?php echo esc_attr( mobiloud::get_option( 'ml_user_phone' ) ); ?>&redirect_url=http://example.com" style="min-width:320px;height:1600px;"></div>
			<script type="text/javascript" src="https://assets.calendly.com/assets/external/widget.js"></script>
			<script type="text/javascript">
				function isCalendlyEvent(e) {
					return e.data.event &&
						e.data.event.indexOf('calendly') === 0;
				};

				window.addEventListener(
					'message',
					function(e) {
						if (isCalendlyEvent(e)) {

							if( e.data.event === 'calendly.event_scheduled' ) {
								window.location.href = '<?php echo trailingslashit( get_bloginfo( 'url' ) ) . 'wp-admin/admin.php?page=mobiloud&tab=welcome&step=scheduled'; ?>';
							}

						}
					}
				);
			</script>
			<!-- Calendly inline widget end -->

<!--			<script type="text/javascript">-->
<!--				jQuery(function ($) {-->
<!--					var open_booking = function() {-->
<!--						window.app2 = appointlet({-->
<!--							organization: "mobiloud",-->
<!--							email: $('#submit').data('appointlet-email'),-->
<!--							fields: {-->
<!--								"name": $('#submit').data('appointlet-field-name'),-->
<!--								"phone": $('#submit').data('appointlet-field-phone'),-->
<!--								"site": $('#submit').data('appointlet-field-site'),-->
<!--								"message": $('#submit').data('appointlet-field-message'),-->
<!--								"company-name": $('#submit').data('appointlet-field-company-name'),-->
<!--								"type": $('#submit').data('appointlet-field-type'),-->
<!--								"utm-source": $('#submit').data('appointlet-field-utm-source'),-->
<!--								"redirect-url": $('#submit').data('appointlet-field-redirect-url'),-->
<!--							},-->
<!--							query: {-->
<!--								skip_fields: true,-->
<!--								utm_source: 'news-plugin',-->
<!--							}-->
<!--						}).show();-->
<!--					}-->
<!--					if ($('#submit').data('open')) {-->
<!--						open_booking();-->
<!--					}-->
<!--					$('#submit').on('click', open_booking);-->
<!--				});-->
<!--			</script>-->
		<?php else : ?>
			<?php
			$video = 'news' == Mobiloud::get_option( 'ml_user_apptype' ) ? 'http://www.vimeo.com/296195883' : 'http://www.vimeo.com/295610034';
			?>
			<div class="ml-scheedule-time-block">
				<p>Thank you, we'll get in touch to schedule a time to talk. In the meantime, you can watch a recent webinar recording that tells you everything about MobiLoud. Got any questions? Send us an email at <a href="mailto:sales@mobiloud.com">sales@mobiloud.com</a>.</p>
			</div>
			<br>
			<br>
			<br>
			<div class="ml-welcome-video">
				<div id="embed">Loading ...</div>
			</div>
			<script>
				var videoUrl = <?php echo wp_json_encode( $video ); ?>;
				var endpoint = 'https://www.vimeo.com/api/oembed.json';
				var callback = 'embedVideo';
				var url = endpoint + '?url=' + encodeURIComponent(videoUrl) + '&callback=' + callback + '&width=' + getWidth(640);
				function embedVideo(video) {
					document.getElementById('embed').innerHTML = unescape(video.html);
				}
				function getWidth(width) {
					return Math.min(width, Math.max(
						document.body.scrollWidth,
						document.documentElement.scrollWidth,
						document.body.offsetWidth,
						document.documentElement.offsetWidth,
						document.documentElement.clientWidth
						) - 53);
				}
				function vimeo_init() {
					var js = document.createElement('script');
					js.setAttribute('type', 'text/javascript');
					js.setAttribute('src', url);
					document.getElementsByTagName('head').item(0).appendChild(js);
				}
				window.onload = vimeo_init;
			</script>
			<br>
			<br>
			<input type="submit" name="submit" id="submit_price" class="button button-primary button-hero"
				value="Sign Up Now">
			<p class="text-center"><a href="#" data-href="<?php echo( esc_attr( admin_url( 'admin.php?page=mobiloud&tab=welcome-close' ) ) ); ?>" class="welcome_question_start">Return to the plugin.</a></p>
		<?php endif; ?>
	</div>
</div>
