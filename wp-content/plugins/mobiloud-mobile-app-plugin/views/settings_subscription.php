<div class="ml2-block">
	<div class="ml2-header"><h2>General Settings</h2></div>
	<div class="ml2-body">

		<div class='ml-col-row'>
			<p>By enabling in-app subscriptions your app will start displaying the subscription buttons in all article pages and under your app main menu.</p>
			<div class="ml-form-row ml-checkbox-wrap">
				<input type="checkbox" id="ml_app_subscription_enabled" name="ml_app_subscription_enabled"
					value="true" <?php echo Mobiloud::get_option( 'ml_app_subscription_enabled' ) ? 'checked' : ''; ?>/>
				<label for="ml_app_subscription_enabled">Enable in-app subscriptions</label>
			</div>
		</div>

		<h4>Apple In-App Purchase ID</h4>
		<div class='ml-col-row'>
			<div class='ml-col-half'>
				<p>Insert your in-app purchase product identifier, this information can be found under your iTunes Connect account.</p>
			</div>
			<div class='ml-col-half'>
				<div class="ml-form-row">
					<input id="ml_app_subscription_ios_in_app_purchase_id" type="text" size="36" name="ml_app_subscription_ios_in_app_purchase_id" class="ml-input-full"
						value="<?php echo esc_attr( Mobiloud::get_option( 'ml_app_subscription_ios_in_app_purchase_id', '' ) ); ?>"/>
				</div>
			</div>
		</div>

		<h4>Google In-App Purchase ID</h4>
		<div class='ml-col-row'>
			<div class='ml-col-half'>
				<p>Insert your in-app purchase product identifier, this information can be found under your Google Developer Console.</p>
			</div>
			<div class='ml-col-half'>
				<div class="ml-form-row">
					<input id="ml_app_subscription_android_in_app_purchase_id" type="text" size="36" name="ml_app_subscription_android_in_app_purchase_id" class="ml-input-full"
						value="<?php echo esc_attr( Mobiloud::get_option( 'ml_app_subscription_android_in_app_purchase_id', '' ) ); ?>"/>
				</div>
			</div>
		</div>

		<h4>Subscribe Menu Item Text</h4>
		<div class='ml-col-row'>
			<div class='ml-col-half'>
				<p>Insert the text for your subscribe link</p>
			</div>
			<div class='ml-col-half'>
				<div class="ml-form-row">
					<input id="ml_app_subscriptions_subscribe_link_text" type="text" size="36" name="ml_app_subscriptions_subscribe_link_text" class="ml-input-full"
						value="<?php echo esc_attr( Mobiloud::get_option( 'ml_app_subscriptions_subscribe_link_text', '' ) ); ?>"/>
				</div>
			</div>
		</div>

		<h4>Manage Subscription Menu Item Text</h4>
		<div class='ml-col-row'>
			<div class='ml-col-half'>
				<p>Insert the text for your Manage Subscription link</p>
			</div>
			<div class='ml-col-half'>
				<div class="ml-form-row">
					<input id="ml_app_subscriptions_manage_subscription_link_text" type="text" size="36" name="ml_app_subscriptions_manage_subscription_link_text" class="ml-input-full"
						value="<?php echo esc_attr( Mobiloud::get_option( 'ml_app_subscriptions_manage_subscription_link_text', '' ) ); ?>"/>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="ml2-block">
	<div class="ml2-header"><h2>Design Settings</h2></div>
	<div class="ml2-body">

		<h4>Logo Image</h4>
		<div class="ml-form-row">
			<p>Upload the image that will be displayed on the top of your subscription window, usually your app logo.</p>

			<input id="ml_app_subscription_logo" type="text" size="36" name="ml_app_subscription_logo"
				value="<?php echo get_option( 'ml_app_subscription_logo' ); ?>"/>
			<input id="ml_app_subscription_logo_button" type="button" value="Upload Image" class="browser button"/>
		</div>
		<?php $logoPath = Mobiloud::get_option( 'ml_app_subscription_logo' ); ?>
		<div
			class="ml-form-row ml-preview-app-sub-logo-image-row" <?php echo ( strlen( $logoPath ) === 0 ) ? 'style="display:none;"' : ''; ?>>
			<div class='ml-preview-app-sub-logo-holder ml-preview-image-holder'>
				<img src='<?php echo esc_url( $logoPath ); ?>'/>
			</div>
			<a href='#' class='ml-preview-app-sub-logo-remove-btn'>Remove logo</a>
		</div>

		<h4>Background Color</h4>
		<div class="ml-col-row">
			<div class='ml-col-half'>
				<p>Select the color that should be displayed as the background of your subscription window.</p>
			</div>
			<div class='ml-col-half'>
				<input name="ml_app_subscription_background_color" id="ml_app_subscription_background_color" type="text"
					class="ml-colorbox" data-color="ffffff"
					value="<?php echo esc_attr( get_option( 'ml_app_subscription_background_color' ) ); ?>"/>
			</div>
		</div>

		<h4>Call-to-action Title</h4>
		<div class='ml-col-row'>
			<div class='ml-col-half'>
				<p>Insert a title for your call-to-action text.</p>
			</div>
			<div class='ml-col-half'>
				<div class="ml-form-row">
					<input id="ml_app_subscription_title" type="text" size="36" name="ml_app_subscription_title" class="ml-input-full"
						value="<?php echo esc_attr( Mobiloud::get_option( 'ml_app_subscription_title', '' ) ); ?>"/>
				</div>
			</div>
		</div>

		<h4>Call-to-action Text</h4>
		<div class='ml-col-row'>
			<div class='ml-col-half'>
				<p>The text for your call-to-action paragraph, describe and convince users to proceed with subscribing to your app using few words.</p>
			</div>
			<div class='ml-col-half'>
				<div class="ml-form-row">
					<textarea id="ml_app_subscription_description" name="ml_app_subscription_description" rows="4"
						style="width:100%"><?php echo esc_attr( Mobiloud::get_option( 'ml_app_subscription_description', '' ) ); ?></textarea>
				</div>
			</div>
		</div>

		<h4>Call-to-action Text Color</h4>
		<div class="ml-col-row">
			<div class='ml-col-half'>
				<p>The color for the call-to-action elements.</p>
			</div>
			<div class='ml-col-half'>
				<input name="ml_app_subscription_call_to_action_color" id="ml_app_subscription_call_to_action_color" type="text"
					class="ml-colorbox" data-color="000000"
					value="<?php echo esc_attr( get_option( 'ml_app_subscription_call_to_action_color' ) ); ?>"/>
			</div>
		</div>

		<h4>Subscribe Button Title</h4>
		<div class='ml-col-row'>
			<div class='ml-col-half'>
				<p>Insert the title for your subscribe button, usually "Subscribe Now" or "Start Free Trial"</p>
			</div>
			<div class='ml-col-half'>
				<div class="ml-form-row">
					<input id="ml_app_subscription_btn_title" type="text" size="36" name="ml_app_subscription_btn_title" class="ml-input-full"
						value="<?php echo esc_attr( Mobiloud::get_option( 'ml_app_subscription_btn_title', '' ) ); ?>"/>
				</div>
			</div>
		</div>

		<h4>Subscribe Button Description</h4>
		<div class='ml-col-row'>
			<div class='ml-col-half'>
				<p>Insert the description for your subscribe button, usually with the price of your subscription and duration of the trial.</p>
			</div>
			<div class='ml-col-half'>
				<div class="ml-form-row">
					<input id="ml_app_subscription_btn_description" type="text" size="36" name="ml_app_subscription_btn_description" class="ml-input-full"
						value="<?php echo esc_attr( Mobiloud::get_option( 'ml_app_subscription_btn_description', '' ) ); ?>"/>
				</div>
			</div>
		</div>

		<h4>Trial Button Title</h4>
		<div class='ml-col-row'>
			<div class='ml-col-half'>
				<p>Insert the title for your trial button, usually "Start Trial" or "Start Free Trial".</p>
			</div>
			<div class='ml-col-half'>
				<div class="ml-form-row">
					<input id="ml_app_subscription_trial_btn_title" type="text" size="36" name="ml_app_subscription_trial_btn_title" class="ml-input-full"
						value="<?php echo esc_attr( Mobiloud::get_option( 'ml_app_subscription_trial_btn_title', '' ) ); ?>"/>
				</div>
			</div>
		</div>

		<h4>Trial Button Description</h4>
		<div class='ml-col-row'>
			<div class='ml-col-half'>
				<p> Insert the description for your trial button, usually with the price of your subscription and duration of the trial.</p>
			</div>
			<div class='ml-col-half'>
				<div class="ml-form-row">
					<input id="ml_app_subscription_trial_btn_description" type="text" size="36" name="ml_app_subscription_trial_btn_description" class="ml-input-full"
						value="<?php echo esc_attr( Mobiloud::get_option( 'ml_app_subscription_trial_btn_description', '' ) ); ?>"/>
				</div>
			</div>
		</div>

		<h4>Subscribe Button Text Color</h4>
		<div class="ml-col-row">
			<div class='ml-col-half'>
				<p>The color for the subscribe button text.</p>
			</div>
			<div class='ml-col-half'>
				<input name="ml_app_subscription_btn_text_color" id="ml_app_subscription_btn_text_color" type="text"
					class="ml-colorbox" data-color="ffffff"
					value="<?php echo esc_attr( get_option( 'ml_app_subscription_btn_text_color' ) ); ?>"/>
			</div>
		</div>

		<h4>Subscribe Button Color</h4>
		<div class="ml-col-row">
			<div class='ml-col-half'>
				<p>The color for the subscribe button color.</p>
			</div>
			<div class='ml-col-half'>
				<input name="ml_app_subscription_btn_background_color" id="ml_app_subscription_btn_background_color" type="text"
					class="ml-colorbox" data-color="0000cc"
					value="<?php echo esc_attr( get_option( 'ml_app_subscription_btn_background_color' ) ); ?>"/>
			</div>
		</div>

		<h4>Small Description</h4>
		<div class='ml-col-row'>
			<div class='ml-col-half'>
				<p>A small description that will be displayed above the "Restore Purchase" button, usually with more details about your subscription model.</p>
			</div>
			<div class='ml-col-half'>
				<div class="ml-form-row">
					<input id="ml_app_subscription_small_description" type="text" size="36" name="ml_app_subscription_small_description" class="ml-input-full"
						value="<?php echo esc_attr( Mobiloud::get_option( 'ml_app_subscription_small_description', '' ) ); ?>"/>
				</div>
			</div>
		</div>

		<h4>Small Description Color</h4>
		<div class="ml-col-row">
			<div class='ml-col-half'>
				<p>The color for the small description text.</p>
			</div>
			<div class='ml-col-half'>
				<input name="ml_app_subscription_small_description_color" id="ml_app_subscription_small_description_color" type="text"
					class="ml-colorbox" data-color="000000"
					value="<?php echo esc_attr( get_option( 'ml_app_subscription_small_description_color' ) ); ?>"/>
			</div>
		</div>

		<h4>Close Button Color</h4>
		<div class="ml-col-row">
			<div class='ml-col-half'>
				<p>The color for the close button.</p>
			</div>
			<div class='ml-col-half'>
				<input name="ml_app_subscription_close_btn_color" id="ml_app_subscription_close_btn_color" type="text"
					class="ml-colorbox" data-color="0000cc"
					value="<?php echo esc_attr( get_option( 'ml_app_subscription_close_btn_color' ) ); ?>"/>
			</div>
		</div>
	</div>
</div>


