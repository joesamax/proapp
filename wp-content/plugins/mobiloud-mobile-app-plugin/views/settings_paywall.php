<?php ini_set( 'display_errors', 0 ); ?>
<div class="ml2-block">
	<div class="ml2-header"><h2>Paywall Settings</h2></div>
	<div class="ml2-body" id="ml-login-settings">


		<?php

			$ml_paywall_settings = array(
				// defaults
				'ml_enable_paywall' => '',
				'sblock_content' => 'Html code for the paywall goes here!',
				'sblock_title' => 'Become a Premium Member',
				'sblock_css' => 'body.ml-subscription {
				color: #666;
				font-size: 14px;
			}
			.ml-subscription .wrapper {
				max-width: 800px;
				margin: 0 auto;
				padding: 40px 20px 0 20px;
				text-align: center;
			}
			.ml-subscription h2 {
				color: #333;
				font-size: 21px;
				font-weight:600;
				margin-bottom:30px;
			}
			.ml-subscription h1 {
				color: #333;
				font-size: 24px;
				font-weight:bold;
			}
			.ml-subscription img {
				margin: 0 auto 15px auto;
			}
			.ml-subscription p.description {
				font-size: 18px;
				line-height: 1.4em;
			}

			.ml-subscription p a {
				color: #0b97c4;
				font-weight: bold;
				text-decoration: none;
			}

			.ml-paywall__button {
				color: #fff;
				background: #0b97c4;
				display: inline-block;
				padding: 20px 30px;
				margin: 0 0 20px 0;
				text-decoration: none;
				font-size: 18px;
				font-weight: bold;
				border-radius: 6px;
			}
			.terms-conditions {
				padding: 15px 0;
				text-align: left;
			}
			.terms-conditions h3 {
				color: #555;
				font-size: 18px;
			}
			#ml-subscription-close {
				position: absolute;
				top: 0;
				left: 0;
				font-size: 36px;
				padding: 10px;
				display: block;
				border-radius: 50%;
				transform: rotate(45deg);
				font-weight: 200;
				line-height: 30px;
				width: 30px;
			}
			.separator {
				display:block;
				border-bottom:1px solid #ddd;
				padding-top:20px;
				margin-bottom:30px;
			}',
				'pblock_title' => '',
				'pblock_content' => '',
				'pblock_css' => '',
				'pblock_button' => 'Subscribe Now',
			);

			$ml_paywall_settings_option = Mobiloud::get_option( 'ml_paywall_settings' );
			if ( count( $ml_paywall_settings_option ) > 0 ) {
				$ml_paywall_settings = $ml_paywall_settings_option;
			}

		?>

		<div class="ml-col-row">
			<label for="ml_enable_paywall">Enable Paywall: </label>
			<input id="ml_enable_paywall" type="checkbox" value="1" <?php Mobiloud_Admin::echo_if_set( $ml_paywall_settings['ml_enable_paywall'], '1', 'checked' ); ?> name="ml_paywall_settings[ml_enable_paywall]" />
			Enabled
		</div>
		<br/><br/>

		<h2>Subscription screen settings</h2>

		<div class="ml-col-row">
			<label>Title: </label>
			<input size="80" value="<?php Mobiloud_Admin::echo_if_set( stripslashes( $ml_paywall_settings['sblock_title'] ) ); ?>" name="ml_paywall_settings[sblock_title]" type="text" />
		</div>
		<br/>

		<div class="ml-col-row">
			<label>HTML Content: </label>
			<textarea class="ml-editor-area ml-show" name="ml_paywall_settings[sblock_content]"><?php Mobiloud_Admin::echo_if_set( stripslashes( $ml_paywall_settings['sblock_content'] ) ); ?></textarea>
		</div>
		<br/>

		<div class="ml-col-row">
			<label>CSS rules: </label>
			<textarea class="ml-editor-area ml-show" name="ml_paywall_settings[sblock_css]"><?php Mobiloud_Admin::echo_if_set( stripslashes( $ml_paywall_settings['sblock_css'] ) ); ?></textarea>
		</div>
		<br/>

		<h2>Paywall Block Settings</h2>

		<div class="ml-col-row">
			<label>Title: </label>
			<input size="80" value="<?php Mobiloud_Admin::echo_if_set( stripslashes( $ml_paywall_settings['pblock_title'] ) ); ?>" name="ml_paywall_settings[pblock_title]" type="text" />
		</div>
		<br/>

		<div class="ml-col-row">
			<label>Button Text: </label>
			<input size="40" value="<?php Mobiloud_Admin::echo_if_set( stripslashes( $ml_paywall_settings['pblock_button'] ) ); ?>" name="ml_paywall_settings[pblock_button]" type="text" />
		</div>
		<br/>

		<div class="ml-col-row">
			<label>HTML Content: </label>
			<textarea class="ml-editor-area ml-show" name="ml_paywall_settings[pblock_content]"><?php Mobiloud_Admin::echo_if_set( stripslashes( $ml_paywall_settings['pblock_content'] ) ); ?></textarea>
		</div>
		<br/>

		<div class="ml-col-row">
			<label>CSS rules: </label>
			<textarea class="ml-editor-area ml-show" name="ml_paywall_settings[pblock_css]"><?php Mobiloud_Admin::echo_if_set( stripslashes( $ml_paywall_settings['pblock_css'] ) ); ?></textarea>
		</div>
		<br/>

	</div>
</div>
