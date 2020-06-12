<div class="ml2-block">
	<div class="ml2-header"><h2><?php echo Mobiloud_Admin::$settings_tabs[ $active_tab ]['title']; ?></h2></div>
	<div class="ml2-body">
		<?php
		function echo_admob_ad_size( $option_var_name, $option_app_sub_name, $default_value = 'medium' ) {
			$current_value = Mobiloud::get_option( $option_var_name, $default_value );
		?>
			<tr valign="top" class="ml_admob_ads_wrap ml_fban_ads_wrap ml_gdfp_ads_wrap">
				<th scope="row">Ad size</th>
				<td class="ml_ad_interval">
					<?php
					$items = array(
						'small'  => array( 'Small', 100, '320x50' ),
						'medium' => array( 'Medium', 132, '320x100' ),
						'large'  => array( 'Large', 320, '300x250' ),
					);
					?>
					<select class="ml_admob_size_select" id="<?php echo esc_attr( $option_var_name ); ?>" name="<?php echo esc_attr( $option_var_name ); ?>">
						<?php
						foreach ( $items as $k => $value ) {
						?>
							<option
								value="<?php echo esc_attr( $k ); ?>" <?php selected( $current_value, $k, true ); ?>
								data-height="<?php echo esc_attr( $value[1] ); ?>" data-size="<?php echo esc_attr( $value[2] ); ?>">
								<?php echo esc_html( $value[0] ); ?>
							</option>
						<?php } ?>
					</select>
					<p class="ml_admob_ads_wrap">Please make sure the ad height in Admob is set to <span class="ml_admob_size_notice"><?php echo esc_attr( $items[ $default_value ][1] ); ?></span>dp</p>
					<p class="ml_gdfp_ads_wrap">Please make sure the ad size in Google DFP is set to <span class="ml_gdfp_size_notice"><?php echo esc_attr( $items[ $default_value ][2] ); ?></span></p>
				</td>
			</tr>
			<?php
			echo_app_sub_checkbox( $option_app_sub_name, '' );
		}
		function echo_app_sub_checkbox( $ad_unit_name, $classes = '' ) {
			$name  = $ad_unit_name . '_app_subscription_show';
			$value = get_option( $name, true ); // default value is true
			?>
			<tr valign="top" class="<?php echo $classes; ?>">
				<td colspan="2" class="ml_ad_interval">
					<input type="checkbox" id="<?php echo esc_attr( $name ); ?>" name="<?php echo esc_attr( $name ); ?>" value="1"<?php echo $value ? ' checked="checked"' : ''; ?>>
					<label for="<?php echo esc_attr( $name ); ?>">Show this ad when a subscription is active.</label>
				</td>
			</tr>
			<?php
		}
		function echo_app_checkbox( $option_name ) {
			$value = Mobiloud::get_option( $option_name, true );
			?>
			<tr valign="top">
				<td colspan="2">
					<input type="checkbox" id="<?php echo esc_attr( $option_name ); ?>" name="<?php echo esc_attr( $option_name ); ?>" value="1"<?php echo $value ? ' checked="checked"' : ''; ?>>
					<label for="<?php echo esc_attr( $option_name ); ?>">Show this ad when a subscription is active.</label>
				</td>
			</tr>
		<?php
		}

		?>
		<p>With MobiLoud's support for a number of networks and ad servers and the possibility of adding any image,
			javascript or HTML based ads within the contents of your app, the possibilities to monetize your content are
			endless.</p>
		<p>Before setting up advertising in your app <a
			href="https://www.mobiloud.com/help/knowledge-base/ads-banners/<?php echo esc_url( get_option( 'affiliate_link', null ) ); ?>?utm_source=wp-plugin-admin&utm_medium=web&utm_campaign=ads_page"
			target="_blank">read the guide in our Knowledge Base</a>.</p>
		<p>Need any help? <a class="contact" href="mailto:support@mobiloud.com">Contact our support team</a>.</p>

		<table class="form-table block-privacy-url">
			<tbody>
				<tr valign="top">
					<th scope="row">Privacy Policy URL</th>
					<td>
						<input type="url" id="ml_privacy_policy_url" name="ml_privacy_policy_url"
							value="<?php echo esc_attr( Mobiloud::get_option( 'ml_privacy_policy_url' ) ); ?>"/>
					</td>
				</tr>
			</tbody>
		</table>
		<p>	Admob and Mopub will allow you to include your own privacy policy URL into the message used to obtain the user's
			consent to use cookies or other local storage for ads. Add your own Privacy Policy URL to the field above.<br>
		</p>
	</div>
</div>
<div class="ml2-block">
	<div class="ml2-header"><h2>Banner, Interstitial and Native ads</h2></div>
	<div class="ml2-body">

		<p>The following ad platforms are supported:</p>
		<ul class="ml-info-list">
			<li><strong>AdMob</strong>: AdMob is a leading global mobile advertising network that helps you monetize
				your mobile apps. Banner ads, native ads in the list and interstitials are supported.
			</li>
			<li><strong>MoPub</strong>: MoPub is a hosted ad serving solution built specifically for mobile publishers.
				Banner ads, interstitials and native ads are supported.
			</li>
			<li><strong>Google DFP</strong>: DoubleClick for Publishers (DFP) is a flexible ad server solution offered
				by Google. Banner ads and interstitials are supported.
			</li>
			<li><strong>Facebook Audience Network</strong>: Facebook's Audience Network allows you to monetize your iOS and Android apps with targeted ads.
			</li>
		</ul>
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row">Select Advertising Platform</th>
					<td>
						<select id="ml_advertising_platform" name="ml_advertising_platform">
							<?php
								$value = Mobiloud::get_option( 'ml_advertising_platform' );
							?>
							<option value="" <?php selected( $value, '' ); ?>>
								Disabled
							</option>
							<option value="admob" <?php selected( $value, 'admob' ); ?>>
								AdMob
							</option>
							<option value="mopub" <?php selected( $value, 'mopub' ); ?>>
								MoPub
							</option>
							<option value="gdfp" <?php selected( $value, 'gdfp' ); ?>>
								Google DoubleClick (DFP)
							</option>
							<option value="fban" <?php selected( $value, 'fban' ); ?>>
								Facebook Audience Network
							</option>
						</select>
					</td>
				</tr>
			</tbody>
		</table>
		<h2 class="nav-tab-wrapper wp-clearfix">
			<a href="#" class="nav-tab nav-tab-active" data-tab="#block_ios">iOS Ad Units</a>
			<a href="#" class="nav-tab" data-tab="#block_android">Android Ad Units</a>
		</h2>


		<div class='ml-col-row nav-tab-content' id='block_ios'>
			<table class="form-table">
				<tbody>
					<tr valign="top" class="ml_admob_ads_wrap">
						<th scope="row">AdMob App ID</th>
						<td>
							<input type="text" id="ml_ios_admob_app_id" name="ml_ios_admob_app_id"
								value="<?php echo esc_attr( Mobiloud::get_option( 'ml_ios_admob_app_id' ) ); ?>"/>
						</td>
					</tr>
				</tbody>
			</table>
			<hr/>
			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row">Phone Banner ID</th>
						<td>
							<input type="text" id="ml_ios_phone_banner_unit_id" name="ml_ios_phone_banner_unit_id"
								value="<?php echo esc_attr( Mobiloud::get_option( 'ml_ios_phone_banner_unit_id' ) ); ?>"/>
						</td>
					</tr>
					<?php echo_app_checkbox( 'ml_ios_phone_banner_app_subscription_show' ); ?>
					<tr valign="top">
						<th scope="row">Tablet Banner ID</th>
						<td>
							<input type="text" id="ml_ios_tablet_banner_unit_id" name="ml_ios_tablet_banner_unit_id"
								value="<?php echo esc_attr( Mobiloud::get_option( 'ml_ios_tablet_banner_unit_id' ) ); ?>"/>
						</td>
					</tr>
					<?php echo_app_checkbox( 'ml_ios_tablet_banner_app_subscription_show' ); ?>
					<tr>
						<td colspan="2">
							<div class="ml-radio-wrap">
								<input type="radio" id="ml_ios_banner_position_top" name="ml_ios_banner_position"
									value="top" <?php echo Mobiloud::get_option( 'ml_ios_banner_position', 'bottom' ) === 'top' ? 'checked' : ''; ?>>
								<label for="ml_ios_banner_position_top">Show banners at the top of the screen</label>
							</div>
							<div class="ml-radio-wrap">
								<input type="radio" id="ml_ios_banner_position_bottom" name="ml_ios_banner_position"
									value="bottom" <?php echo Mobiloud::get_option( 'ml_ios_banner_position', 'bottom' ) === 'bottom' ? 'checked' : ''; ?>>
								<label for="ml_ios_banner_position_bottom">Show banners at the bottom of the screen
									(recommended)</label>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
			<hr/>
			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row">Interstitial Ad ID</th>
						<td>
							<input type="text" id="ml_ios_interstitial_unit_id" name="ml_ios_interstitial_unit_id"
								value="<?php echo esc_attr( Mobiloud::get_option( 'ml_ios_interstitial_unit_id' ) ); ?>"/>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">Interval</th>
						<td class="ml_ad_interval">
							<select id="ml_ios_interstitial_interval" name="ml_ios_interstitial_interval" class="ml-value-get">
								<?php for ( $a = 1; $a <= 10; $a ++ ) : ?>
									<option
										value="<?php echo esc_attr( $a ); ?>" <?php echo Mobiloud::get_option( 'ml_ios_interstitial_interval', 5 ) == $a ? 'selected="selected"' : ''; ?>>
										<?php echo esc_html( $a ); ?>
									</option>
								<?php endfor; ?>
							</select>
							<p>Show interstitial ads every <span class="ml-value-set"></span> article or page screens.</p>
						</td>
					</tr>
					<?php echo_app_sub_checkbox( 'ml_ios_interstitial' ); ?>
				</tbody>
			</table>
			<div class="ml_native_ads_wrap ml_admob_ads_wrap ml_fban_ads_wrap ml_gdfp_ads_wrap">
				<hr/>
				<table class="form-table">
					<tbody>
						<tr valign="top">
							<th scope="row">Article List Native Ad ID</th>
							<td>
								<input type="text" id="ml_ios_native_ad_unit_id" name="ml_ios_native_ad_unit_id"
									value="<?php echo esc_attr( Mobiloud::get_option( 'ml_ios_native_ad_unit_id' ) ); ?>"/>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">Interval</th>
							<td class="ml_ad_interval">
								<select id="ml_ios_native_ad_interval" name="ml_ios_native_ad_interval" class="ml-value-get">
									<?php for ( $a = 2; $a <= 10; $a ++ ) : ?>
										<option
											value="<?php echo esc_attr( $a ); ?>" <?php echo Mobiloud::get_option( 'ml_ios_native_ad_interval', 5 ) == $a ? 'selected="selected"' : ''; ?>>
											<?php echo esc_html( $a ); ?>
										</option>
									<?php endfor; ?>
								</select>
								<p>Show native ads every <span class="ml-value-set"></span> articles in the article list.</p>
							</td>
						</tr>
						<?php echo_admob_ad_size( 'ml_ios_native_ad_type', 'ml_ios_native_ad' ); ?>
					</tbody>
				</table>
			</div>
			<div class="ml_admob_ads_wrap ml_fban_ads_wrap ml_gdfp_ads_wrap">
				<hr/>
				<table class="form-table">
					<tbody>
						<tr valign="top">
							<th scope="row">Article Screen Native Ad ID</th>
							<td>
								<input type="text" id="ml_ios_native_ad_article_unit_id" name="ml_ios_native_ad_article_unit_id"
									value="<?php echo esc_attr( Mobiloud::get_option( 'ml_ios_native_ad_article_unit_id' ) ); ?>"/>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">At position</th>
							<td class="ml_ad_interval">
								<select id="ml_ios_native_ad_article_position" name="ml_ios_native_ad_article_position">
									<?php
									foreach ( array(
										'top'    => 'Above article',
										'bottom' => 'Below article',
										'both'   => 'Both above and below',
										) as $k => $value ) {
									?>
										<option
											value="<?php echo esc_attr( $k ); ?>" <?php echo Mobiloud::get_option( 'ml_ios_native_ad_article_position', 'both' ) == $k ? 'selected="selected"' : ''; ?>>
											<?php echo esc_html( $value ); ?>
										</option>
									<?php } ?>
								</select>
							</td>
						</tr>
						<?php echo_admob_ad_size( 'ml_ios_native_ad_article_type', 'ml_ios_native_ad_article' ); ?>
					</tbody>
				</table>
			</div>

		</div>

		<div class='ml-col-row nav-tab-content' id='block_android' style="display: none;">
			<table class="form-table">
				<tbody>
					<tr valign="top" class="ml_admob_ads_wrap">
						<th scope="row">AdMob App ID</th>
						<td>
							<input type="text" id="ml_android_admob_app_id" name="ml_android_admob_app_id"
								value="<?php echo esc_attr( Mobiloud::get_option( 'ml_android_admob_app_id' ) ); ?>"/>
						</td>
					</tr>
				</tbody>
			</table>
			<hr/>
			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row">Phone Banner ID</th>
						<td>
							<input type="text" id="ml_android_phone_banner_unit_id"
								name="ml_android_phone_banner_unit_id"
								value="<?php echo esc_attr( Mobiloud::get_option( 'ml_android_phone_banner_unit_id' ) ); ?>"/>
						</td>
					</tr>
					<?php echo_app_checkbox( 'ml_android_phone_banner_app_subscription_show' ); ?>
					<tr valign="top">
						<th scope="row">Tablet Banner ID</th>
						<td>
							<input type="text" id="ml_android_tablet_banner_unit_id"
								name="ml_android_tablet_banner_unit_id"
								value="<?php echo esc_attr( Mobiloud::get_option( 'ml_android_tablet_banner_unit_id' ) ); ?>"/>
						</td>
					</tr>
					<?php echo_app_checkbox( 'ml_android_tablet_app_subscription_show' ); ?>
					<tr>
						<td colspan="2">
							<div class="ml-radio-wrap">
								<input type="radio" id="ml_android_banner_position_top"
									name="ml_android_banner_position"
									value="top" <?php echo Mobiloud::get_option( 'ml_android_banner_position', 'bottom' ) === 'top' ? 'checked' : ''; ?>>
								<label for="ml_android_banner_position_top">Show banners at the top of the
									screen</label>
							</div>
							<div class="ml-radio-wrap">
								<input type="radio" id="ml_android_banner_position_bottom"
									name="ml_android_banner_position"
									value="bottom" <?php echo Mobiloud::get_option( 'ml_android_banner_position', 'bottom' ) === 'bottom' ? 'checked' : ''; ?>>
								<label for="ml_android_banner_position_bottom">Show banners at the bottom of the screen
									(recommended)</label>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
			<hr/>
			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row">Interstitial Ad ID</th>
						<td>
							<input type="text" id="ml_android_interstitial_unit_id"
								name="ml_android_interstitial_unit_id"
								value="<?php echo esc_attr( Mobiloud::get_option( 'ml_android_interstitial_unit_id' ) ); ?>"/>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">Interval</th>
						<td class="ml_ad_interval">
							<select id="ml_android_interstitial_interval" name="ml_android_interstitial_interval" class="ml-value-get">
								<?php for ( $a = 1; $a <= 10; $a ++ ) : ?>
									<option
										value="<?php echo esc_attr( $a ); ?>" <?php echo Mobiloud::get_option( 'ml_android_interstitial_interval', 5 ) == $a ? 'selected="selected"' : ''; ?>>
										<?php echo esc_html( $a ); ?>
									</option>
								<?php endfor; ?>
							</select>
							<p>Show interstitial ads every <span class="ml-value-set"></span> article or page screens.</p>
						</td>
					</tr>
					<?php echo_app_sub_checkbox( 'ml_android_interstitial' ); ?>
				</tbody>
			</table>
			<div class="ml_native_ads_wrap ml_admob_ads_wrap ml_fban_ads_wrap ml_gdfp_ads_wrap">
				<hr/>
				<table class="form-table">
					<tbody>
						<tr valign="top">
							<th scope="row">Article List Native Ad ID</th>
							<td>
								<input type="text" id="ml_android_native_ad_unit_id" name="ml_android_native_ad_unit_id"
									value="<?php echo esc_attr( Mobiloud::get_option( 'ml_android_native_ad_unit_id' ) ); ?>"/>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">Interval</th>
							<td class="ml_ad_interval">
								<select id="ml_android_native_ad_interval" name="ml_android_native_ad_interval" class="ml-value-get">
									<?php for ( $a = 2; $a <= 10; $a ++ ) : ?>
										<option
											value="<?php echo esc_attr( $a ); ?>" <?php echo Mobiloud::get_option( 'ml_android_native_ad_interval', 5 ) == $a ? 'selected="selected"' : ''; ?>>
											<?php echo esc_html( $a ); ?>
										</option>
									<?php endfor; ?>
								</select>
								<p>Show native ads every <span class="ml-value-set"></span> articles in the article list.</p>
							</td>
						</tr>
						<?php echo_admob_ad_size( 'ml_android_native_ad_type', 'ml_android_native_ad' ); ?>
					</tbody>
				</table>
			</div>
			<div class="ml_admob_ads_wrap ml_fban_ads_wrap ml_gdfp_ads_wrap">
				<hr/>
				<table class="form-table">
					<tbody>
						<tr valign="top">
							<th scope="row">Article Screen Native Ad ID</th>
							<td>
								<input type="text" id="ml_android_native_ad_article_unit_id" name="ml_android_native_ad_article_unit_id"
									value="<?php echo esc_attr( Mobiloud::get_option( 'ml_android_native_ad_article_unit_id' ) ); ?>"/>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">At position</th>
							<td class="ml_ad_interval">
								<select id="ml_android_native_ad_article_position" name="ml_android_native_ad_article_position">
									<?php
									foreach ( array(
										'top'    => 'Above article',
										'bottom' => 'Below article',
										'both'   => 'Both above and below',
										) as $k => $value ) {
									?>
										<option
											value="<?php echo esc_attr( $k ); ?>" <?php echo Mobiloud::get_option( 'ml_android_native_ad_article_position', 'both' ) == $k ? 'selected="selected"' : ''; ?>>
											<?php echo esc_html( $value ); ?>
										</option>
									<?php } ?>
								</select>
							</td>
						</tr>
						<?php echo_admob_ad_size( 'ml_android_native_ad_article_type', 'ml_android_native_ad_article' ); ?>
					</tbody>
				</table>

			</div>
		</div>
	</div>
</div>
<div class="ml2-block">
	<div class="ml2-header"><h2>Embed HTML ads within the content</h2></div>
	<div class="ml2-body">

		<div class='ml-col-row'>
			<p>You can use the editor to add HTML or Javascript code in a number of ad positions within the post and
				page screens.</p>

			<div class="ml-editor-controls">
				<select id="ml_ad_banner_position_select" name="ml_ad_banner_position_select">
					<option value="">
						Select a position...
					</option>
					<?php foreach ( Mobiloud_Admin::$banner_positions as $position_key => $position_name ) : ?>
						<option value='<?php echo esc_attr( $position_key ); ?>' ?>
							<?php echo esc_html( $position_name ); ?>
						</option>
					<?php endforeach; ?>
				</select>
				<a href="#" class='button-primary ml-save-banner-btn'>Save</a>
				<?php wp_nonce_field( 'save_banner', 'ml_nonce_save_banner' ); ?>
			</div>
			<textarea class='ml-editor-area ml-show'></textarea>
			<?php
			foreach ( Mobiloud_Admin::$banner_positions as $position_key => $position_name ) :
				$input_name  = $position_key . '_app_subscription_show';
				$input_value = get_option( $input_name, true ); // default value is true
			?>
				<textarea class='ml-editor-area'
					name='<?php echo esc_attr( $position_key ); ?>'><?php echo stripslashes( htmlspecialchars( Mobiloud::get_option( $position_key, '' ) ) ); ?></textarea>
				<input type="checkbox" class="ml-editor-area-checkbox" id="<?php echo esc_attr( $input_name ); ?>" name="<?php echo esc_attr( $input_name ); ?>" value="1"<?php echo $input_value ? ' checked="checked"' : ''; ?>>
				<label class="ml-editor-area-label" for="<?php echo esc_attr( $input_name ); ?>">Show this ad when a subscription is active.</label>

			<?php endforeach; ?>

			<h4>Preview the results</h4>
			<p>Select a post or page to preview the results of your edits.</p>
			<select id="preview_popup_post_select">
				<?php
				$posts_query         = array(
					'posts_per_page' => 10,
					'orderby'        => 'post_date',
					'order'          => 'DESC',
					'post_type',
				);
				$included_post_types = explode( ',', Mobiloud::get_option( 'ml_article_list_include_post_types', array() ) );
				foreach ( $included_post_types as $post_type ) {
					$posts_query['post_type'] = $post_type;
					$posts                    = get_posts( $posts_query );
					if ( count( $posts ) > 0 ) {
				?>
						<optgroup label="<?php echo ucfirst( $post_type ); ?>">
							<?php foreach ( $posts as $post ) { ?>

								<option
									value="<?php echo esc_attr( site_url( '/ml-api/v1/post/?post_id='. $post->ID ) ); ?>">
									<?php if ( strlen( $post->post_title ) > 40 ) { ?>

										<?php echo substr( $post->post_title, 0, 40 ); ?>

										..
									<?php } else { ?>

										<?php echo $post->post_title; ?>

									<?php } ?>
							</option><?php } ?>
						</optgroup>
				<?php
					}
				}


				?>
				<?php
				$pages = get_pages(
					array(
						'sort_order'  => 'ASC',
						'sort_column' => 'post_title',
						'post_type'   => 'page',
						'post_status' => 'publish',
					)
				);
				?>
				<optgroup label="Pages">
					<?php foreach ( $pages as $page ) { ?>

						<option value="<?php echo esc_attr( site_url( '/ml-api/v1/post/?post_id='. $page->ID ) ); ?>">
							<?php if ( strlen( $page->post_title ) > 40 ) { ?>

								<?php echo esc_html( substr( $page->post_title, 0, 40 ) ); ?>

								..
							<?php } else { ?>

								<?php echo esc_html( $page->post_title ); ?>

							<?php } ?>
					</option><?php } ?>
				</optgroup>
			</select>
			<a href='#' class='ml_open_preview_btn button-secondary ml-preview-phone-btn'>Preview on phone</a>
			<a href='#' class='ml_open_preview_btn button-secondary ml-preview-tablet-btn'>Preview on tablet</a>
		</div>
	</div>
</div>
<div id="preview_popup_content">
	<div class="iphone5s_device">
		<iframe id="preview_popup_iframe">
		</iframe>
	</div>
	<div class="ipadmini_device">
		<iframe id="preview_popup_iframe">
		</iframe>
	</div>
</div>
