<?php

class Mobiloud_Admin {

	private static $initiated = false;

	public static $settings_tabs    = array(
		'design'         => array(
			'title'        => 'Design',
			'form_wrap_id' => 'get_started_design',
			'form_id'      => '',
		),
		'menu_config'    => array(
			'title'        => 'Menu',
			'form_wrap_id' => 'get_started_menu_config',
		),
		'settings'       => array(
			'title'        => 'Settings',
			'form_wrap_id' => 'ml_settings_general',
		),
		'advertising'    => array(
			'title'        => 'Advertising',
			'form_wrap_id' => 'ml_settings_advertising',
		),
		'subscription'   => array(
			'title'        => 'Subscriptions',
			'form_wrap_id' => 'ml_settings_subscription',
		),
		'analytics'      => array(
			'title'        => 'Analytics',
			'form_wrap_id' => 'ml_settings_analytics',
		),
		'editor'         => array(
			'title'            => 'Editor',
			'form_wrap_id'     => 'ml_settings_editor',
			'form_id'          => 'form_editor',
			'no_submit_button' => true,
		),
		'push'           => array(
			'title'        => 'Push',
			'form_wrap_id' => 'ml_push_settings',
		),
		'login_settings' => array(
			'title'        => 'Login',
			'form_wrap_id' => 'ml_login_settings',
		),
		'paywall'        => array(
			'title'        => 'Paywall',
			'form_wrap_id' => 'ml_settings_paywall',
		),
	);
	public static $push_tabs        = array(
		'notifications' => 'Notifications',
	);
	public static $welcome_steps    = array(
		0 => 'details',
		1 => 'success',
		2 => 'scheduled',
	);
	public static $editor_sections  = array(
		'ml_post_head'                => 'PHP Inside HEAD tag',
		'ml_html_post_head'           => 'HTML Inside HEAD tag',
		'ml_post_custom_js'           => 'Custom JS',
		'ml_post_custom_css'          => 'Custom CSS',
		'ml_post_start_body'          => 'PHP at the start of body tag',
		'ml_html_post_start_body'     => 'HTML at the start of body tag',
		'ml_post_before_details'      => 'PHP before post details',
		'ml_html_post_before_details' => 'HTML before post details',
		'ml_post_right_of_date'       => 'PHP right of date',
		'ml_post_after_details'       => 'PHP after post details',
		'ml_html_post_after_details'  => 'HTML after post details',
		'ml_post_before_content'      => 'PHP before Content',
		'ml_html_post_before_content' => 'HTML before Content',
		'ml_post_after_content'       => 'PHP after Content',
		'ml_html_post_after_content'  => 'HTML after Content',
		'ml_post_after_body'          => 'PHP at the end of body tag',
		'ml_html_post_after_body'     => 'HTML at the end of body tag',
		'ml_post_footer'              => 'PHP Footer',
	);
	public static $banner_positions = array(
		'ml_banner_above_content' => 'Above Content',
		'ml_banner_above_title'   => 'Above Title',
		'ml_banner_below_content' => 'Below Content',
	);
	private static $admin_screens   = array();

	public static function init() {
		include_once MOBILOUD_PLUGIN_DIR . 'categories.php';
		include_once MOBILOUD_PLUGIN_DIR . 'pages.php';

		if ( ! self::$initiated ) {
			self::init_hooks();
		}

		Mobiloud_App_Preview::init();
	}

	/**
	* Initializes WordPress hooks
	*/
	private static function init_hooks() {
		self::$initiated = true;
		add_action( 'admin_init', array( 'Mobiloud_Admin', 'admin_init' ) );
		add_action( 'current_screen', array( 'Mobiloud_Admin', 'current_screen' ) );
		add_action( 'admin_menu', array( 'Mobiloud_Admin', 'admin_menu' ) );
		add_action( 'admin_head', array( 'Mobiloud_Admin', 'check_mailing_list_alert' ) );
		add_action( 'wp_ajax_ml_save_editor', array( 'Mobiloud_Admin', 'save_editor' ) );
		add_action( 'wp_ajax_ml_save_editor_embed', array( 'Mobiloud_Admin', 'save_editor_embed' ) );
		add_action( 'wp_ajax_ml_save_banner', array( 'Mobiloud_Admin', 'save_banner' ) );
		add_action( 'wp_ajax_ml_tax_list', array( 'Mobiloud_Admin', 'get_tax_list' ) );
		add_action( 'wp_ajax_ml_load_ajax', array( 'Mobiloud_Admin', 'load_ajax' ) );
		add_action( 'wp_ajax_ml_schedule_dismiss', array( 'Mobiloud_Admin', 'schedule_dismiss' ) );
		add_action( 'wp_ajax_ml_cache_flush', array( 'Mobiloud_Admin', 'ajax_cache_flush' ) );

		add_action( 'wp_ajax_ml_get_tax_terms', array( 'Mobiloud_Admin', 'get_tax_terms' ) );

		add_action( 'save_post', array( 'Mobiloud_Admin', 'flush_cache_on_save' ) );
		add_action( 'transition_post_status', array( 'Mobiloud_Admin', 'flush_cache_on_transition' ), 10, 3 );

		add_action( 'wp_ajax_ml_welcome', array( 'Mobiloud_Admin', 'ajax_welcome' ) );

		if ( is_admin() && Mobiloud::get_option( 'ml_push_notification_enabled', false ) ) {
			add_action( 'add_meta_boxes', array( 'Mobiloud_Admin', 'add_push_metabox' ), 1, 2 );
			add_action( 'pre_post_update', array( 'Mobiloud_Admin', 'save_push_metabox' ) );
		}

		// add custom metabox for App Links to wp-admin menu creator
		add_filter( 'nav_menu_meta_box_object', array( 'Mobiloud_Admin', 'mobiloud_add_menu_meta_box' ), 10, 1);

		// add custom menu fields to menu
		add_filter( 'wp_setup_nav_menu_item', array( 'Mobiloud_Admin', 'ml_menu_add_custom_nav_fields' ) );

		// save menu custom fields
		add_action( 'wp_update_nav_menu_item', array( 'Mobiloud_Admin', 'ml_menu_update_custom_nav_fields'), 10, 3 );

		// edit menu walker
		add_filter( 'wp_edit_nav_menu_walker', array( 'Mobiloud_Admin', 'ml_menu_edit_walker'), 10, 2 );

		$ml_paywall_settings = Mobiloud::get_option( 'ml_paywall_settings', false );
		if ( $ml_paywall_settings && isset( $ml_paywall_settings['ml_enable_paywall'] ) && $ml_paywall_settings['ml_enable_paywall'] === '1' ) {
			add_action( 'add_meta_boxes', 'ml_paywall_meta_box' );
			add_action( 'save_post', 'ml_save_paywall_meta_box_data' );

			$taxes = get_taxonomies( array(
				'public' => true,
			) );

			foreach ( $taxes as $tax ) {
				// Add the paywall field to the Add New {tax} page
				add_action( $tax . '_add_form_fields', 'ml_taxonomy_paywall_protected', 10, 2 );

				// Add the paywall field to the Edit {tax} page
				add_action( $tax . '_edit_form_fields', 'ml_taxonomy_paywall_protected', 10, 2 );

				// Save tax paywall
				add_action( 'edited_' . $tax, 'ml_taxonomy_paywall_save', 10, 2 );
				add_action( 'create_' . $tax, 'ml_taxonomy_paywall_save', 10, 2 );
			}

		}
	}

	/**
	* Add menu meta box
	*/
	public static function mobiloud_add_menu_meta_box( $object ) {
		add_meta_box( 'mobiloud-menu-metabox', __( 'Mobiloud App Links' ), array( 'Mobiloud_Admin', 'mobiloud_menu_meta_box' ), 'nav-menus', 'side', 'default' );
		return $object;
	}

	/**
	* Add custom fields to $item nav object
	* in order to be used in custom Walker
	*
	* @access      public
	* @since       1.0
	* @return      string $menu_item
	*/
	public static function ml_menu_add_custom_nav_fields( $menu_item ) {

		$menu_item->opening_method = get_post_meta( $menu_item->ID, '_ml_menu_item_opening_method', true );
		return $menu_item;

	}

	/**
	* Save menu custom fields
	*
	* @access      public
	* @since       1.0
	* @return      void
	*/
	public static function ml_menu_update_custom_nav_fields( $menu_id, $menu_item_db_id, $args ) {
		// Check if element is properly sent.
		if ( isset( $_REQUEST['ml_custom_menu_nonce'] ) && is_array( $_REQUEST['ml_custom_menu_nonce'] ) && isset( $_REQUEST['ml_custom_menu_nonce'][ $menu_item_db_id ] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['ml_custom_menu_nonce'][ $menu_item_db_id ] ) ), 'ml-menu-item' ) ) {
			if ( isset( $_REQUEST['menu-item-ml-opening-method'] ) && is_array( $_REQUEST['menu-item-ml-opening-method'] ) && isset( $_REQUEST['menu-item-ml-opening-method'][ $menu_item_db_id ] ) ) {
				$opening_method = sanitize_text_field( wp_unslash( $_REQUEST['menu-item-ml-opening-method'][ $menu_item_db_id ] ) );
				update_post_meta( $menu_item_db_id, '_ml_menu_item_opening_method', $opening_method );
			}
		}
	}

	/**
	* Define new Walker edit
	*
	* @return string
	*/
	public static function ml_menu_edit_walker( $walker, $menu_id ) {

		return 'Mobiloud_Admin_Menu_Walker';
	}

	public static function mobiloud_menu_meta_box(){
		global $nav_menu_selected_id;
?>
		<div id="mobiloudlinks" class="categorydiv">

			<div id="tabs-panel-mobiloudlinks-all" class="tabs-panel tabs-panel-view-all tabs-panel-active">

				<ul id="mobiloudlinks-checklist-all" class="categorychecklist form-no-clear">
					<li>
						<label class="menu-item-title">
							<input type="checkbox" class="menu-item-checkbox" name="menu-item[-1][menu-item-object-id]" value="1"> Favorites
						</label>
						<input type="hidden" class="menu-item-db-id" name="menu-item[-1][menu-item-db-id]" value="0">
						<input type="hidden" class="menu-item-object" name="menu-item[-1][menu-item-object]" value="">
						<input type="hidden" class="menu-item-parent-id" name="menu-item[-1][menu-item-parent-id]" value="0">
						<input type="hidden" class="menu-item-type" name="menu-item[-1][menu-item-type]" value="favorites">
						<input type="hidden" class="menu-item-title" name="menu-item[-1][menu-item-title]" value="Favorites">
						<input type="hidden" class="menu-item-url" name="menu-item[-1][menu-item-url]" value="">
						<input type="hidden" class="menu-item-target" name="menu-item[-1][menu-item-target]" value="">
						<input type="hidden" class="menu-item-attr_title" name="menu-item[-1][menu-item-attr_title]" value="">
						<input type="hidden" class="menu-item-classes" name="menu-item[-1][menu-item-classes]" value="">
						<input type="hidden" class="menu-item-xfn" name="menu-item[-1][menu-item-xfn]" value="">
					</li>

					<li>
						<label class="menu-item-title">
							<input type="checkbox" class="menu-item-checkbox" name="menu-item[-2][menu-item-object-id]" value="1"> Settings
						</label>
						<input type="hidden" class="menu-item-db-id" name="menu-item[-2][menu-item-db-id]" value="0">
						<input type="hidden" class="menu-item-object" name="menu-item[-2][menu-item-object]" value="">
						<input type="hidden" class="menu-item-parent-id" name="menu-item[-2][menu-item-parent-id]" value="0">
						<input type="hidden" class="menu-item-type" name="menu-item[-2][menu-item-type]" value="settings">
						<input type="hidden" class="menu-item-title" name="menu-item[-2][menu-item-title]" value="Settings">
						<input type="hidden" class="menu-item-url" name="menu-item[-2][menu-item-url]" value="">
						<input type="hidden" class="menu-item-target" name="menu-item[-2][menu-item-target]" value="">
						<input type="hidden" class="menu-item-attr_title" name="menu-item[-2][menu-item-attr_title]" value="">
						<input type="hidden" class="menu-item-classes" name="menu-item[-2][menu-item-classes]" value="">
						<input type="hidden" class="menu-item-xfn" name="menu-item[-2][menu-item-xfn]" value="">
					</li>

					<li>
						<label class="menu-item-title">
							<input type="checkbox" class="menu-item-checkbox" name="menu-item[-3][menu-item-object-id]" value="1"> Home Screen
						</label>
						<input type="hidden" class="menu-item-db-id" name="menu-item[-3][menu-item-db-id]" value="0">
						<input type="hidden" class="menu-item-object" name="menu-item[-3][menu-item-object]" value="">
						<input type="hidden" class="menu-item-parent-id" name="menu-item[-3][menu-item-parent-id]" value="0">
						<input type="hidden" class="menu-item-type" name="menu-item[-3][menu-item-type]" value="home_screen">
						<input type="hidden" class="menu-item-title" name="menu-item[-3][menu-item-title]" value="Home Screen">
						<input type="hidden" class="menu-item-url" name="menu-item[-3][menu-item-url]" value="">
						<input type="hidden" class="menu-item-target" name="menu-item[-3][menu-item-target]" value="">
						<input type="hidden" class="menu-item-attr_title" name="menu-item[-3][menu-item-attr_title]" value="">
						<input type="hidden" class="menu-item-classes" name="menu-item[-3][menu-item-classes]" value="">
						<input type="hidden" class="menu-item-xfn" name="menu-item[-3][menu-item-xfn]" value="">
					</li>

					<li>
						<label class="menu-item-title">
							<input type="checkbox" class="menu-item-checkbox" name="menu-item[-4][menu-item-object-id]" value="1"> Login
						</label>
						<input type="hidden" class="menu-item-db-id" name="menu-item[-4][menu-item-db-id]" value="0">
						<input type="hidden" class="menu-item-object" name="menu-item[-4][menu-item-object]" value="">
						<input type="hidden" class="menu-item-parent-id" name="menu-item[-4][menu-item-parent-id]" value="0">
						<input type="hidden" class="menu-item-type" name="menu-item[-4][menu-item-type]" value="login">
						<input type="hidden" class="menu-item-title" name="menu-item[-4][menu-item-title]" value="Login">
						<input type="hidden" class="menu-item-url" name="menu-item[-4][menu-item-url]" value="">
						<input type="hidden" class="menu-item-target" name="menu-item[-4][menu-item-target]" value="">
						<input type="hidden" class="menu-item-attr_title" name="menu-item[-4][menu-item-attr_title]" value="">
						<input type="hidden" class="menu-item-classes" name="menu-item[-4][menu-item-classes]" value="">
						<input type="hidden" class="menu-item-xfn" name="menu-item[-4][menu-item-xfn]" value="">
					</li>
				</ul>
			</div>


			<p class="button-controls wp-clearfix">
				<span class="add-to-menu">
					<input type="submit"<?php wp_nav_menu_disabled_check( $nav_menu_selected_id ); ?> class="button-secondary submit-add-to-menu right" value="<?php esc_attr_e('Add to Menu'); ?>" name="add-mobiloudlinks-menu-item" id="submit-mobiloudlinks" />
					<span class="spinner"></span>
				</span>
			</p>

		</div><!-- /.categorydiv -->
		<?php
	}

	public static function flush_cache_on_save( $post_id ) {
		include_once MOBILOUD_PLUGIN_DIR . 'api/controllers/MLCacheController.php';
		$ml_cache = new MLCacheController();
		$ml_cache->flush_cache( 'ml_json' );
		$ml_cache->flush_post_cache( $post_id );
	}

	public static function flush_cache_on_transition( $new_status, $old_status, $post ) {
		include_once MOBILOUD_PLUGIN_DIR . 'api/controllers/MLCacheController.php';
		$ml_cache = new MLCacheController();
		$ml_cache->flush_cache( 'ml_json' );
		$ml_cache->flush_post_cache( $post->ID );
	}

	public static function flush_cache() {
		include_once MOBILOUD_PLUGIN_DIR . 'api/controllers/MLCacheController.php';
		$ml_cache = new MLCacheController();
		$ml_cache->flush_cache( 'ml_json' );
		$ml_cache->flush_cache( 'ml_post' );
	}

	public static function admin_init() {
		self::set_default_options();
		self::admin_redirect();
		// for old WordPress versions
		if ( ! function_exists( 'set_current_screen' ) ) {
			self::register_scripts();
		}
		if ( is_admin() && current_user_can( Mobiloud::capability_for_use ) ) {
			if ( ! Mobiloud::get_option( 'ml_schedule_dismiss' ) ) {
				// add_action( 'init', array( 'Mobiloud_Admin', 'add_schedule_demo' ) );
				Mobiloud_Admin::add_schedule_demo();
			}
		}
	}

	public static function current_screen() {
		if ( is_admin() ) {
			$screen = get_current_screen();
			if ( $screen instanceof WP_Screen && in_array( $screen->id, self::$admin_screens ) ) {
				self::register_scripts();
			}
		}
	}

	public static function admin_menu() {
		$image = 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9Im5vIj8+PHN2ZyAgIHhtbG5zOmRjPSJodHRwOi8vcHVybC5vcmcvZGMvZWxlbWVudHMvMS4xLyIgICB4bWxuczpjYz0iaHR0cDovL2NyZWF0aXZlY29tbW9ucy5vcmcvbnMjIiAgIHhtbG5zOnJkZj0iaHR0cDovL3d3dy53My5vcmcvMTk5OS8wMi8yMi1yZGYtc3ludGF4LW5zIyIgICB4bWxuczpzdmc9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiAgIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgICB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgICB4bWxuczpzb2RpcG9kaT0iaHR0cDovL3NvZGlwb2RpLnNvdXJjZWZvcmdlLm5ldC9EVEQvc29kaXBvZGktMC5kdGQiICAgeG1sbnM6aW5rc2NhcGU9Imh0dHA6Ly93d3cuaW5rc2NhcGUub3JnL25hbWVzcGFjZXMvaW5rc2NhcGUiICAgdmVyc2lvbj0iMS4wIiAgIGlkPSJMYXllcl8xIiAgIHg9IjBweCIgICB5PSIwcHgiICAgd2lkdGg9IjI0cHgiICAgaGVpZ2h0PSIyNHB4IiAgIHZpZXdCb3g9IjAgMCAyNCAyNCIgICBlbmFibGUtYmFja2dyb3VuZD0ibmV3IDAgMCAyNCAyNCIgICB4bWw6c3BhY2U9InByZXNlcnZlIiAgIGlua3NjYXBlOnZlcnNpb249IjAuNDguNCByOTkzOSIgICBzb2RpcG9kaTpkb2NuYW1lPSJtbC1tZW51LWljb250ci5zdmciPjxtZXRhZGF0YSAgICAgaWQ9Im1ldGFkYXRhMjkiPjxyZGY6UkRGPjxjYzpXb3JrICAgICAgICAgcmRmOmFib3V0PSIiPjxkYzpmb3JtYXQ+aW1hZ2Uvc3ZnK3htbDwvZGM6Zm9ybWF0PjxkYzp0eXBlICAgICAgICAgICByZGY6cmVzb3VyY2U9Imh0dHA6Ly9wdXJsLm9yZy9kYy9kY21pdHlwZS9TdGlsbEltYWdlIiAvPjxkYzp0aXRsZSAvPjwvY2M6V29yaz48L3JkZjpSREY+PC9tZXRhZGF0YT48ZGVmcyAgICAgaWQ9ImRlZnMyNyI+PGNsaXBQYXRoICAgICAgIGlkPSJTVkdJRF8yXy0yIj48dXNlICAgICAgICAgaGVpZ2h0PSIxMDUyLjM2MjIiICAgICAgICAgd2lkdGg9Ijc0NC4wOTQ0OCIgICAgICAgICB5PSIwIiAgICAgICAgIHg9IjAiICAgICAgICAgc3R5bGU9Im92ZXJmbG93OnZpc2libGUiICAgICAgICAgeGxpbms6aHJlZj0iI1NWR0lEXzFfLTgiICAgICAgICAgb3ZlcmZsb3c9InZpc2libGUiICAgICAgICAgaWQ9InVzZTktMSIgLz48L2NsaXBQYXRoPjxjbGlwUGF0aCAgICAgICBpZD0iY2xpcFBhdGgzMDE4Ij48dXNlICAgICAgICAgaGVpZ2h0PSIxMDUyLjM2MjIiICAgICAgICAgd2lkdGg9Ijc0NC4wOTQ0OCIgICAgICAgICB5PSIwIiAgICAgICAgIHg9IjAiICAgICAgICAgc3R5bGU9Im92ZXJmbG93OnZpc2libGUiICAgICAgICAgeGxpbms6aHJlZj0iI1NWR0lEXzFfLTgiICAgICAgICAgb3ZlcmZsb3c9InZpc2libGUiICAgICAgICAgaWQ9InVzZTMwMjAiIC8+PC9jbGlwUGF0aD48Y2xpcFBhdGggICAgICAgaWQ9ImNsaXBQYXRoMzAyMiI+PHVzZSAgICAgICAgIGhlaWdodD0iMTA1Mi4zNjIyIiAgICAgICAgIHdpZHRoPSI3NDQuMDk0NDgiICAgICAgICAgeT0iMCIgICAgICAgICB4PSIwIiAgICAgICAgIHN0eWxlPSJvdmVyZmxvdzp2aXNpYmxlIiAgICAgICAgIHhsaW5rOmhyZWY9IiNTVkdJRF8xXy04IiAgICAgICAgIG92ZXJmbG93PSJ2aXNpYmxlIiAgICAgICAgIGlkPSJ1c2UzMDI0IiAvPjwvY2xpcFBhdGg+PGNsaXBQYXRoICAgICAgIGlkPSJjbGlwUGF0aDMwMjYiPjx1c2UgICAgICAgICBoZWlnaHQ9IjEwNTIuMzYyMiIgICAgICAgICB3aWR0aD0iNzQ0LjA5NDQ4IiAgICAgICAgIHk9IjAiICAgICAgICAgeD0iMCIgICAgICAgICBzdHlsZT0ib3ZlcmZsb3c6dmlzaWJsZSIgICAgICAgICB4bGluazpocmVmPSIjU1ZHSURfMV8tOCIgICAgICAgICBvdmVyZmxvdz0idmlzaWJsZSIgICAgICAgICBpZD0idXNlMzAyOCIgLz48L2NsaXBQYXRoPjxjbGlwUGF0aCAgICAgICBpZD0iY2xpcFBhdGgzMDMwIj48dXNlICAgICAgICAgaGVpZ2h0PSIxMDUyLjM2MjIiICAgICAgICAgd2lkdGg9Ijc0NC4wOTQ0OCIgICAgICAgICB5PSIwIiAgICAgICAgIHg9IjAiICAgICAgICAgc3R5bGU9Im92ZXJmbG93OnZpc2libGUiICAgICAgICAgeGxpbms6aHJlZj0iI1NWR0lEXzFfLTgiICAgICAgICAgb3ZlcmZsb3c9InZpc2libGUiICAgICAgICAgaWQ9InVzZTMwMzIiIC8+PC9jbGlwUGF0aD48Y2xpcFBhdGggICAgICAgaWQ9ImNsaXBQYXRoMzAzNCI+PHVzZSAgICAgICAgIGhlaWdodD0iMTA1Mi4zNjIyIiAgICAgICAgIHdpZHRoPSI3NDQuMDk0NDgiICAgICAgICAgeT0iMCIgICAgICAgICB4PSIwIiAgICAgICAgIHN0eWxlPSJvdmVyZmxvdzp2aXNpYmxlIiAgICAgICAgIHhsaW5rOmhyZWY9IiNTVkdJRF8xXy04IiAgICAgICAgIG92ZXJmbG93PSJ2aXNpYmxlIiAgICAgICAgIGlkPSJ1c2UzMDM2IiAvPjwvY2xpcFBhdGg+PGNsaXBQYXRoICAgICAgIGlkPSJjbGlwUGF0aDMwMzgiPjx1c2UgICAgICAgICBoZWlnaHQ9IjEwNTIuMzYyMiIgICAgICAgICB3aWR0aD0iNzQ0LjA5NDQ4IiAgICAgICAgIHk9IjAiICAgICAgICAgeD0iMCIgICAgICAgICBzdHlsZT0ib3ZlcmZsb3c6dmlzaWJsZSIgICAgICAgICB4bGluazpocmVmPSIjU1ZHSURfMV8tOCIgICAgICAgICBvdmVyZmxvdz0idmlzaWJsZSIgICAgICAgICBpZD0idXNlMzA0MCIgLz48L2NsaXBQYXRoPjxkZWZzICAgICAgIGlkPSJkZWZzNSI+PHJlY3QgICAgICAgICBoZWlnaHQ9IjI0IiAgICAgICAgIHdpZHRoPSIyNCIgICAgICAgICBpZD0iU1ZHSURfMV8iIC8+PC9kZWZzPjxjbGlwUGF0aCAgICAgICBpZD0iU1ZHSURfMl8iPjx1c2UgICAgICAgICBpZD0idXNlOSIgICAgICAgICBvdmVyZmxvdz0idmlzaWJsZSIgICAgICAgICB4bGluazpocmVmPSIjU1ZHSURfMV8iIC8+PC9jbGlwUGF0aD48ZGVmcyAgICAgICBpZD0iZGVmczUtMiI+PHJlY3QgICAgICAgICBoZWlnaHQ9IjI0IiAgICAgICAgIHdpZHRoPSIyNCIgICAgICAgICBpZD0iU1ZHSURfMV8tOCIgICAgICAgICB4PSIwIiAgICAgICAgIHk9IjAiIC8+PC9kZWZzPjxjbGlwUGF0aCAgICAgICBpZD0iY2xpcFBhdGgzMDQ1Ij48dXNlICAgICAgICAgaWQ9InVzZTMwNDciICAgICAgICAgb3ZlcmZsb3c9InZpc2libGUiICAgICAgICAgeGxpbms6aHJlZj0iI1NWR0lEXzFfLTgiICAgICAgICAgc3R5bGU9Im92ZXJmbG93OnZpc2libGUiICAgICAgICAgeD0iMCIgICAgICAgICB5PSIwIiAgICAgICAgIHdpZHRoPSI3NDQuMDk0NDgiICAgICAgICAgaGVpZ2h0PSIxMDUyLjM2MjIiIC8+PC9jbGlwUGF0aD48Y2xpcFBhdGggICAgICAgaWQ9IlNWR0lEXzJfLTgiPjxyZWN0ICAgICAgICAgaGVpZ2h0PSIyNCIgICAgICAgICB3aWR0aD0iMjQiICAgICAgICAgaWQ9InVzZTktMiIgICAgICAgICB4PSIwIiAgICAgICAgIHk9IjAiIC8+PC9jbGlwUGF0aD48Y2xpcFBhdGggICAgICAgaWQ9ImNsaXBQYXRoMzAxOC0wIj48cmVjdCAgICAgICAgIGhlaWdodD0iMjQiICAgICAgICAgd2lkdGg9IjI0IiAgICAgICAgIGlkPSJ1c2UzMDIwLTkiICAgICAgICAgeD0iMCIgICAgICAgICB5PSIwIiAvPjwvY2xpcFBhdGg+PGNsaXBQYXRoICAgICAgIGlkPSJjbGlwUGF0aDMwMjItNSI+PHJlY3QgICAgICAgICBoZWlnaHQ9IjI0IiAgICAgICAgIHdpZHRoPSIyNCIgICAgICAgICBpZD0idXNlMzAyNC05IiAgICAgICAgIHg9IjAiICAgICAgICAgeT0iMCIgLz48L2NsaXBQYXRoPjxjbGlwUGF0aCAgICAgICBpZD0iY2xpcFBhdGgzMDI2LTciPjxyZWN0ICAgICAgICAgaGVpZ2h0PSIyNCIgICAgICAgICB3aWR0aD0iMjQiICAgICAgICAgaWQ9InVzZTMwMjgtMyIgICAgICAgICB4PSIwIiAgICAgICAgIHk9IjAiIC8+PC9jbGlwUGF0aD48Y2xpcFBhdGggICAgICAgaWQ9ImNsaXBQYXRoMzAzMC0xIj48cmVjdCAgICAgICAgIGhlaWdodD0iMjQiICAgICAgICAgd2lkdGg9IjI0IiAgICAgICAgIGlkPSJ1c2UzMDMyLTEiICAgICAgICAgeD0iMCIgICAgICAgICB5PSIwIiAvPjwvY2xpcFBhdGg+PGNsaXBQYXRoICAgICAgIGlkPSJjbGlwUGF0aDMwMzQtNiI+PHJlY3QgICAgICAgICBoZWlnaHQ9IjI0IiAgICAgICAgIHdpZHRoPSIyNCIgICAgICAgICBpZD0idXNlMzAzNi04IiAgICAgICAgIHg9IjAiICAgICAgICAgeT0iMCIgLz48L2NsaXBQYXRoPjxjbGlwUGF0aCAgICAgICBpZD0iY2xpcFBhdGgzMDM4LTQiPjxyZWN0ICAgICAgICAgaGVpZ2h0PSIyNCIgICAgICAgICB3aWR0aD0iMjQiICAgICAgICAgaWQ9InVzZTMwNDAtMyIgICAgICAgICB4PSIwIiAgICAgICAgIHk9IjAiIC8+PC9jbGlwUGF0aD48L2RlZnM+PHNvZGlwb2RpOm5hbWVkdmlldyAgICAgcGFnZWNvbG9yPSIjZmZmZmZmIiAgICAgYm9yZGVyY29sb3I9IiM2NjY2NjYiICAgICBib3JkZXJvcGFjaXR5PSIxIiAgICAgb2JqZWN0dG9sZXJhbmNlPSIxMCIgICAgIGdyaWR0b2xlcmFuY2U9IjEwIiAgICAgZ3VpZGV0b2xlcmFuY2U9IjEwIiAgICAgaW5rc2NhcGU6cGFnZW9wYWNpdHk9IjAiICAgICBpbmtzY2FwZTpwYWdlc2hhZG93PSIyIiAgICAgaW5rc2NhcGU6d2luZG93LXdpZHRoPSI3MzAiICAgICBpbmtzY2FwZTp3aW5kb3ctaGVpZ2h0PSI0ODAiICAgICBpZD0ibmFtZWR2aWV3MjUiICAgICBzaG93Z3JpZD0iZmFsc2UiICAgICBpbmtzY2FwZTp6b29tPSI5LjgzMzMzMzMiICAgICBpbmtzY2FwZTpjeD0iMy4wMjQxMzI1IiAgICAgaW5rc2NhcGU6Y3k9IjIxLjIwNTUwNSIgICAgIGlua3NjYXBlOndpbmRvdy14PSI1MjUiICAgICBpbmtzY2FwZTp3aW5kb3cteT0iNjYiICAgICBpbmtzY2FwZTp3aW5kb3ctbWF4aW1pemVkPSIwIiAgICAgaW5rc2NhcGU6Y3VycmVudC1sYXllcj0iTGF5ZXJfMSIgLz48cGF0aCAgICAgc3R5bGU9ImZpbGw6Izk5OTk5OTtmaWxsLW9wYWNpdHk6MSIgICAgIGNsaXAtcGF0aD0idXJsKCNTVkdJRF8yXykiICAgICBkPSJNIDQsMCBDIDEuNzkxLDAgMCwxLjc5MSAwLDQgbCAwLDE2IGMgMCwyLjIwOSAxLjc5MSw0IDQsNCBsIDE2LDAgYyAyLjIwOSwwIDQsLTEuNzkxIDQsLTQgTCAyNCw0IEMgMjQsMS43OTEgMjIuMjA5LDAgMjAsMCBMIDQsMCB6IG0gOS41LDMuNSBjIDAuMTI2NDcsMCAwLjI2MDA3NSwwLjAyNzgwOCAwLjM3NSwwLjA2MjUgMC4wODkzMiwwLjAyNTUxMSAwLjE2OTU2NiwwLjA1MDkyIDAuMjUsMC4wOTM3NSAwLjAyMTI2LDAuMDEyMDMzIDAuMDQxOTgsMC4wMTgwNzMgMC4wNjI1LDAuMDMxMjUgMC4xMTA4OTUsMC4wNjcwMTIgMC4xOTQ5MzcsMC4xNTQyOTg2IDAuMjgxMjUsMC4yNSAwLjA3OTE5LDAuMDg2OTk3IDAuMTMyNTAzLDAuMTc2NjQwOSAwLjE4NzUsMC4yODEyNSBsIDAuMDMxMjUsMCBjIDAuMDE1MjIsMC4wMjk2NTcgMC4wMTYyLDAuMDYzOTkyIDAuMDMxMjUsMC4wOTM3NSAwLjEzMjc5MiwwLjI2MjYwNjMgMC4yNTU2MTEsMC41MTEwNDY2IDAuMzc1LDAuNzgxMjUgMC4wMTMzNCwwLjAzMDE0NiAwLjAxODA4LDAuMDYzNTE5IDAuMDMxMjUsMC4wOTM3NSAwLjExODAzLDAuMjcxNDExMyAwLjIzOTY0OCwwLjUzMzk1OTUgMC4zNDM3NSwwLjgxMjUgMC4xMjU1MjgsMC4zMzQ4MTMyIDAuMjM5NDI0LDAuNjg3MTQ4MyAwLjM0Mzc1LDEuMDMxMjUgMC4wODY3NiwwLjI4NzQ3OTUgMC4xNzgyMjYsMC41ODEzMzQ2IDAuMjUsMC44NzUgMC4wMDQ5LDAuMDE5ODg3IC0wLjAwNDgsMC4wNDI1ODUgMCwwLjA2MjUgMC4wNzM3NywwLjMwNjUyNDcgMC4xNjE3ODksMC42MjQ3NzkgMC4yMTg3NSwwLjkzNzUgMC4wMDE4LDAuMDEwMDI3IC0wLjAwMTgsMC4wMjEyMTYgMCwwLjAzMTI1IDAuMDU4MTQsMC4zMjI1MjQzIDAuMDg1MjgsMC42NDAyMDM1IDAuMTI1LDAuOTY4NzUgMC4wODExMSwwLjY3NjgxMiAwLjEyNSwxLjM2MDc3NCAwLjEyNSwyLjA2MjUgbCAwLDAuMDMxMjUgMC4wMzEyNSwwIDAsMC4wMzEyNSBjIDAsMC42ODUgLTAuMDQ0OCwxLjM3MDEyMiAtMC4xMjUsMi4wMzEyNSAtMC4wMDEyLDAuMDEwMTkgMC4wMDEyLDAuMDIxMDYgMCwwLjAzMTI1IC0wLjAzOTQzLDAuMzE5OTc5IC0wLjA5OTAxLDAuNjIzNTk0IC0wLjE1NjI1LDAuOTM3NSAtMC4wMDM2LDAuMDIwMzEgMC4wMDM3LDAuMDQyMjEgMCwwLjA2MjUgLTAuMDU2NTEsMC4zMDM1NTQgLTAuMTE0OTIxLDAuNjA4Njg3IC0wLjE4NzUsMC45MDYyNSAtMC4wNjUyLDAuMjczNTIzIC0wLjE0MDU0OSwwLjU0NDI2NiAtMC4yMTg3NSwwLjgxMjUgLTAuMTA0OTk4LDAuMzUyNDM4IC0wLjIxNzAxNywwLjY4ODM2NSAtMC4zNDM3NSwxLjAzMTI1IC0wLjIxNjUwMSwwLjU5NjI3NSAtMC40NzEwMDIsMS4xNTU2MzcgLTAuNzUsMS43MTg3NSAtMC4wMTAzMSwwLjAyMDgxIC0wLjAyMDg2LDAuMDQxNzQgLTAuMDMxMjUsMC4wNjI1IC0wLjAwNywwLjAxODkzIDAuMDA3OCwwLjA0Mzk5IDAsMC4wNjI1IC0wLjAxNjg3LDAuMDMzNDMgLTAuMDQ1NDEsMC4wNjA0NSAtMC4wNjI1LDAuMDkzNzUgLTAuMDU1MDcsMC4xMDQ1MjUgLTAuMTA4Mjk4LDAuMTk0MjY5IC0wLjE4NzUsMC4yODEyNSAtMC4wNTQ2LDAuMDYwNDQgLTAuMTIyNjI0LDAuMTA2Nzg5IC0wLjE4NzUsMC4xNTYyNSBDIDE0LjA5NDcxLDIwLjM4OTM2NiAxMy44Mjg2NzQsMjAuNSAxMy41MzEyNSwyMC41IGMgLTAuMTAxMjg3LDAgLTAuMTg2NTU4LC0wLjAwOTYgLTAuMjgxMjUsLTAuMDMxMjUgLTAuMDc1NDYsLTAuMDE1NDQgLTAuMTQ4NTcyLC0wLjAzNDcyIC0wLjIxODc1LC0wLjA2MjUgLTAuMDA3OSwtMC4wMDMzIC0wLjAyMzM5LDAuMDAzNSAtMC4wMzEyNSwwIC0wLjE1NzI2NiwtMC4wNjY0OCAtMC4yODcxODcsLTAuMTYyMzEyIC0wLjQwNjI1LC0wLjI4MTI1IC0wLjIzNzUsLTAuMjM3MjUgLTAuMzc1LC0wLjU3NCAtMC4zNzUsLTAuOTM3NSAwLC0wLjA5OTYxIDAuMDA5MSwtMC4xOTIxMzEgMC4wMzEyNSwtMC4yODEyNSAwLjAwMjMsLTAuMDExMzIgLTAuMDAyNiwtMC4wMjAwNCAwLC0wLjAzMTI1IDAuMDA2MSwtMC4wMjIyMSAwLjAyMzkyLC0wLjA0MDc0IDAuMDMxMjUsLTAuMDYyNSAwLjAyNDU2LC0wLjA4MjgyIDAuMDU0MjIsLTAuMTQzNjQgMC4wOTM3NSwtMC4yMTg3NSBsIC0wLjAzMTI1LDAgYyAxLjAxMSwtMS45NjkgMS41NjI1LC00LjE5NzUgMS41NjI1LC02LjU2MjUgbCAwLC0wLjAzMTI1IDAsLTAuMDMxMjUgYyAwLC0wLjI5NTYyNSAtMC4wMTMzMiwtMC41ODM4ODEgLTAuMDMxMjUsLTAuODc1IEMgMTMuODM5ODgzLDEwLjUxMTI2NiAxMy43NTg1NTUsOS45MzY4NTk0IDEzLjY1NjI1LDkuMzc1IDEzLjU1MTQwNiw4LjgwODY1NzggMTMuNDE5MDc4LDguMjU5ODgwOSAxMy4yNSw3LjcxODc1IDEzLjE2NzI4NSw3LjQ1MDE5NTMgMTMuMDk3NzM0LDcuMTk5MDkzOCAxMyw2LjkzNzUgMTIuODAzMjQyLDYuNDE0NzM0NCAxMi41NjUsNS44OTg1IDEyLjMxMjUsNS40MDYyNSAxMi4zMDgyLDUuMzk3NTMgMTIuMzE2NSw1LjM4MzkwMSAxMi4zMTI1LDUuMzc1IDEyLjI4ODIxNyw1LjMyMjczMTYgMTIuMjY3MzQ3LDUuMjc0NDY4OCAxMi4yNSw1LjIxODc1IDEyLjIzNzk5LDUuMTc2NjM0NiAxMi4yMjY3MzksNS4xMzc2MzU4IDEyLjIxODc1LDUuMDkzNzUgMTIuMjAxMTk5LDUuMDA4MDY4NCAxMi4xODc1LDQuOTAzMzc1IDEyLjE4NzUsNC44MTI1IDEyLjE4NzUsNC4wODU1IDEyLjc3MywzLjUgMTMuNSwzLjUgeiBNIDguNzUsNS45Mzc1IGMgMC4zNzk0MTEzLDAgMC43MzExNDMzLDAuMTc2NTA4OSAwLjk2ODc1LDAuNDM3NSAwLjA3OTIwMiwwLjA4Njk5NyAwLjEzMjQyODksMC4xNzY2NDA5IDAuMTg3NSwwLjI4MTI1IEwgOS45Mzc1LDYuNjI1IGMgMC4wMTkyMzIsMC4wMzc1MjcgMC4wMTI0MTEsMC4wODcyNDEgMC4wMzEyNSwwLjEyNSAwLjU4OTAzMywxLjE4MDYyMTkgMC45ODg3NiwyLjQ4MDY5NjQgMS4xNTYyNSwzLjg0Mzc1IDAuMDU1ODMsMC40NTQzNTEgMC4wOTM3NSwwLjkwNTI2OCAwLjA5Mzc1LDEuMzc1IGwgMCwwLjAzMTI1IDAsMC4wMzEyNSBjIDAsMS45MjQgLTAuNDU5MjUsMy43NDA3NSAtMS4yODEyNSw1LjM0Mzc1IEwgOS45MDYyNSwxNy4zNDM3NSBjIC0wLjIyMDI4NDMsMC40MTg0NzkgLTAuNjE5MTE4MiwwLjcxODc1IC0xLjEyNSwwLjcxODc1IC0wLjcyNiwwIC0xLjMxMjUsLTAuNTg0NSAtMS4zMTI1LC0xLjMxMjUgMCwtMC4yMDU3NDQgMC4wNDA1NjUsLTAuMzg5MDQ5IDAuMTI1LC0wLjU2MjUgTCA3LjU2MjUsMTYuMTU2MjUgYyAwLjYzNCwtMS4yMzcgMSwtMi42NCAxLC00LjEyNSBsIDAsLTAuMDMxMjUgLTAuMDMxMjUsMCAwLC0wLjAzMTI1IGMgMCwtMS40ODUgLTAuMzM0NzUsLTIuODg5IC0wLjk2ODc1LC00LjEyNSBsIDAuMDMxMjUsMCBDIDcuNTQ1NTU3LDcuNzUyMzAxOCA3LjQ5NTc2NDIsNy42NjA0MzU3IDcuNDY4NzUsNy41NjI1IDcuNDQxNzM1Nyw3LjQ2NDU2NDMgNy40Mzc1LDcuMzYwNTU5MSA3LjQzNzUsNy4yNSA3LjQzNzUsNi41MjMgOC4wMjQsNS45Mzc1IDguNzUsNS45Mzc1IHoiICAgICBpZD0icGF0aDExIiAgICAgaW5rc2NhcGU6Y29ubmVjdG9yLWN1cnZhdHVyZT0iMCIgICAgIHRyYW5zZm9ybT0ibWF0cml4KDAuODQ3NDU3NjIsMCwwLDAuODQ3NDU3NjIsMS44MzA1MDg1LDEuODMwNTA4NSkiIC8+PC9zdmc+';
		// switch between main and welcome screen
		if ( isset( $_GET['tab'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			$tab = sanitize_text_field( $_GET['tab'] );
			if ( 'welcome' === $tab ) {
				self::welcome_screen_set( true );
			} elseif ( 'welcome-close' === $tab ) {
				self::welcome_screen_set( false );
			}
		}

		if ( self::welcome_screen_is_now() ) {
			self::$admin_screens[] = add_menu_page(
				'MobiLoud',
				'MobiLoud',
				Mobiloud::capability_for_use,
				'mobiloud',
				array(
					'Mobiloud_Admin',
					'menu_get_init',
				),
				$image,
				'25.90239843109'
			);
		} else {
			self::$admin_screens[] = add_submenu_page(
				'mobiloud',
				'Configuration',
				'Configuration',
				Mobiloud::capability_for_configuration,
				'mobiloud',
				array(
					'Mobiloud_Admin',
					'menu_get_started',
				)
			);
			self::$admin_screens[] = add_menu_page(
				'MobiLoud',
				'MobiLoud',
				Mobiloud::capability_for_configuration,
				'mobiloud',
				array(
					'Mobiloud_Admin',
					'menu_get_started',
				),
				$image,
				'25.90239843209'
			);
			self::$admin_screens[] = add_submenu_page(
				'mobiloud',
				'Push Notification',
				'Push Notifications',
				Mobiloud::capability_for_use,
				'mobiloud_push',
				array(
					'Mobiloud_Admin',
					'menu_push',
				)
			);
		}
	}

	public static function welcome_screen_is_now() {
		return Mobiloud::get_option( 'ml_welcome_screen_now', false );
	}

	public static function welcome_screen_set( $is_welcome = false ) {
		Mobiloud::set_option( 'ml_welcome_screen_now', $is_welcome );
	}

	public static function welcome_screen_is_avalaible() {
		// "ml_activated" is the old option
		return ! Mobiloud::get_option( 'ml_welcome_screen_not_avalaible' ) && ! Mobiloud::get_option( 'ml_activated' ) && self::no_push_keys();
	}

	public static function welcome_screen_set_not_avalaible() {
		Mobiloud::set_option( 'ml_welcome_screen_not_avalaible', true );
	}

	private static function set_default_options() {
		if ( is_null( get_option( 'ml_popup_message_on_mobile_active', null ) ) ) {
			add_option( 'ml_popup_message_on_mobile_active', false );
		}
		if ( is_null( get_option( 'ml_automatic_image_resize', null ) ) ) {
			add_option( 'ml_automatic_image_resize', false );
		}

		if ( get_option( 'affiliate_link', null ) === null ) {

			Mobiloud::set_option( 'affiliate_link', null );

			$affiliates = array( 'themecloud' => '#_l_1c' );

			foreach ( $affiliates as $affiliate => $id ) {
				if ( isset( $_SERVER[ $affiliate ] ) ) {
					Mobiloud::set_option( 'affiliate_link', $id );

				}
			}
		}
	}

	private static function admin_redirect() {
		if ( get_transient( 'ml_activation_redirect' ) && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
			delete_transient( 'ml_activation_redirect' );
			if ( isset( $_GET['activate-multi'] ) ) {
				return;
			}

			wp_safe_redirect(
				add_query_arg(
					array(
						'page'       => 'mobiloud',
						'first-time' => '1',
						'_wpnonce'   => wp_create_nonce(),
					),
					get_admin_url( null, 'admin.php' )
				)
			);
			exit();
		}
	}

	private static function register_scripts() {
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script( 'jquery-ui-dialog' );
		wp_enqueue_style( 'wp-jquery-ui-dialog' );

		wp_register_script( 'google_chart', 'https://www.google.com/jsapi', false, MOBILOUD_PLUGIN_VERSION );
		wp_enqueue_script( 'google_chart' );

		wp_register_script( 'sweetalert2-js', MOBILOUD_PLUGIN_URL . 'libs/sweetalert/sweetalert.min.js', array( 'jquery' ), MOBILOUD_PLUGIN_VERSION );
		wp_enqueue_script( 'sweetalert2-js' );

		wp_register_script( 'areyousure', MOBILOUD_PLUGIN_URL . 'libs/jquery.are-you-sure.js', array( 'jquery' ), MOBILOUD_PLUGIN_VERSION );
		wp_enqueue_script( 'areyousure' );

		wp_register_script( 'notify-js', MOBILOUD_PLUGIN_URL . 'libs/notify/notify.min.js', array( 'jquery' ), MOBILOUD_PLUGIN_VERSION );
		wp_enqueue_script( 'notify-js' );

		wp_register_script( 'mobiloud-forms', MOBILOUD_PLUGIN_URL . 'assets/js/mobiloud-forms.js', array( 'jquery', 'areyousure' ), MOBILOUD_PLUGIN_VERSION );
		wp_enqueue_script( 'mobiloud-forms' );

		wp_register_script( 'mobiloud-push', MOBILOUD_PLUGIN_URL . 'assets/js/mobiloud-push.js', false, MOBILOUD_PLUGIN_VERSION );
		wp_enqueue_script( 'mobiloud-push' );

		wp_register_script( 'mobiloud-editor', MOBILOUD_PLUGIN_URL . 'assets/js/mobiloud-editor.js', array( 'jquery', 'sweetalert2-js' ), MOBILOUD_PLUGIN_VERSION );
		wp_enqueue_script( 'mobiloud-editor' );

		wp_register_script(
			'mobiloud-menu-config',
			MOBILOUD_PLUGIN_URL . 'assets/js/mobiloud-menu-config.js',
			array(
				'jquery',
				'jquery-ui-sortable',
				'sweetalert2-js',
			),
			MOBILOUD_PLUGIN_VERSION
		);
		wp_enqueue_script( 'mobiloud-menu-config' );

		wp_register_script( 'jquerychosen', MOBILOUD_PLUGIN_URL . '/libs/chosen/chosen.jquery.min.js', array( 'jquery' ), MOBILOUD_PLUGIN_VERSION );
		wp_enqueue_script( 'jquerychosen' );

		wp_register_script( 'iscroll', MOBILOUD_PLUGIN_URL . '/libs/iscroll/iscroll.js', array( 'jquery' ), MOBILOUD_PLUGIN_VERSION );
		wp_enqueue_script( 'iscroll' );

		wp_register_script( 'resizecrop', MOBILOUD_PLUGIN_URL . '/libs/jquery.resizecrop-1.0.3.min.js', array( 'jquery' ), MOBILOUD_PLUGIN_VERSION );
		wp_enqueue_script( 'resizecrop' );

		wp_register_script( 'imgliquid', MOBILOUD_PLUGIN_URL . '/libs/imgliquid/jquery.imgliquid.js', array( 'jquery' ), MOBILOUD_PLUGIN_VERSION );
		wp_enqueue_script( 'imgliquid' );

		wp_register_style( 'jquerychosen-css', MOBILOUD_PLUGIN_URL . '/libs/chosen/chosen.css', false, MOBILOUD_PLUGIN_VERSION );
		wp_enqueue_style( 'jquerychosen-css' );

		wp_register_style( 'mobiloud-dashicons', MOBILOUD_PLUGIN_URL . '/libs/dashicons/css/dashicons.css', false, MOBILOUD_PLUGIN_VERSION );
		wp_enqueue_style( 'mobiloud-dashicons' );

		wp_register_style( 'mobiloud-style', MOBILOUD_PLUGIN_URL . '/assets/css/mobiloud-style-33.css', false, MOBILOUD_PLUGIN_VERSION );
		wp_enqueue_style( 'mobiloud-style' );

		wp_register_style( 'mobiloud_admin_post', MOBILOUD_PLUGIN_URL . '/post/css/post.css', false, MOBILOUD_PLUGIN_VERSION );
		wp_enqueue_style( 'mobiloud_admin_post' );

		if ( get_bloginfo( 'version', 'raw' ) < 4.4 ) {
			wp_register_style( 'mobiloud-style-legacy', MOBILOUD_PLUGIN_URL . '/assets/css/mobiloud-style-legacy.css', false, MOBILOUD_PLUGIN_VERSION );
			wp_enqueue_style( 'mobiloud-style-legacy' );
		}

		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_media();
		wp_enqueue_style( 'wp-color-picker' );
	}

	public static function render_view( $view, $parent = null, $data = array() ) {
		if ( $parent === null ) {
			$parent = $view;
		}
		if ( ! empty( $data ) ) {
			foreach ( $data as $key => $val ) {
				$$key = $val;
			}
		}
		if ( 'get_started' === $parent ) {
			define( 'ml_with_sidebar', true );
			define( 'ml_with_form', true );
			if ( 'settings_editor' === $view ) {
				define( 'no_submit_button', true );
			}
		} elseif ( 'push' === $parent ) {
			define( 'ml_with_sidebar', true );
		}

		include MOBILOUD_PLUGIN_DIR . 'views/header.php';

		if ( file_exists( MOBILOUD_PLUGIN_DIR . 'views/header_' . $parent . '.php' ) ) {
			include MOBILOUD_PLUGIN_DIR . 'views/header_' . $parent . '.php';
		}

		include MOBILOUD_PLUGIN_DIR . 'views/' . $view . '.php';

		include MOBILOUD_PLUGIN_DIR . 'views/footer.php';
	}

	public static function render_part_view( $view, $data = array(), $static = false ) {
		if ( ! empty( $data ) ) {
			foreach ( $data as $key => $val ) {
				$$key = $val;
			}
		}
		if ( $static ) {
			include MOBILOUD_PLUGIN_DIR . 'views/static/' . $view . '.php';
		} else {
			include MOBILOUD_PLUGIN_DIR . 'views/' . $view . '.php';
		}
	}

	public static function check_mailing_list_alert() {
		// check if maillist not alerted and initial details saved
		if ( Mobiloud::get_option( 'ml_maillist_alert', '' ) === '' && Mobiloud::get_option( 'ml_initial_details_saved', '' ) === true ) {
			Mobiloud::set_option( 'ml_maillist_alert', true );
		}
	}

	public static function menu_get_init() {
		$base_url = admin_url( 'admin.php?page=mobiloud&step=' );

		wp_register_script( 'jquery-validate', MOBILOUD_PLUGIN_URL . '/libs/jquery.validate.min.js', array( 'jquery' ), MOBILOUD_PLUGIN_VERSION );
		wp_enqueue_script( 'jquery-validate' );
		wp_register_script( 'ladda-spin-js', MOBILOUD_PLUGIN_URL . 'libs/ladda/spin.min.js', false, MOBILOUD_PLUGIN_VERSION );
		wp_enqueue_script( 'ladda-spin-js' );
		wp_register_script( 'ladda-js', MOBILOUD_PLUGIN_URL . 'libs/ladda/ladda.min.js', false, MOBILOUD_PLUGIN_VERSION );
		wp_enqueue_script( 'ladda-js' );
		wp_register_script( 'mobiloud-welcome', MOBILOUD_PLUGIN_URL . 'assets/js/mobiloud-welcome.js', false, MOBILOUD_PLUGIN_VERSION );
		wp_enqueue_script( 'mobiloud-welcome' );
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_media();

		wp_register_style( 'ladda-css', MOBILOUD_PLUGIN_URL . 'libs/ladda/ladda-themeless.min.css', false, MOBILOUD_PLUGIN_VERSION );
		wp_enqueue_style( 'ladda-css' );
		// current tab
		$active_step = mobiloud::get_option( 'ml_welcome_step', self::$welcome_steps[0] );
		if ( ! in_array( $active_step, self::$welcome_steps ) ) {
			$active_step = self::$welcome_steps[0];
		}
		if ( isset( $_GET['step'] ) && in_array( $_GET['step'], self::$welcome_steps ) ) {
			$active_step = $_GET['step'];
		}

		mobiloud::set_option( 'ml_welcome_step', $active_step );

		$active_url = admin_url( 'admin.php?page=mobiloud&tab=welcome' );
		self::render_part_view(
			'welcome_header',
			array(
				'step'     => $active_step,
				'base_url' => $base_url,
			)
		);
		self::render_part_view(
			'welcome_' . $active_step,
			array(
				'base_url'   => $base_url,
				'active_url' => $active_url,
			)
		);
		self::render_part_view(
			'welcome_footer',
			array(
				'step'       => $active_step,
				'active_url' => $active_url,
			)
		);
	}

	public static function menu_get_started() {
		if ( ! current_user_can( Mobiloud::capability_for_configuration ) ) {
			return;
		}
		if ( count( $_POST ) ) {
			self::flush_cache();
		}
		if ( ! isset( $_GET['tab'] ) || ( ! isset( self::$settings_tabs[ $_GET['tab'] ] ) && ! isset( self::$push_tabs[ $_GET['tab'] ] ) ) ) {
			$_GET['tab'] = 'design';
		}
		$tab = sanitize_text_field( $_GET['tab'] );
		switch ( $tab ) {
			default:
			case 'design':
				wp_enqueue_script( 'wp-color-picker' );
				wp_enqueue_media();
				wp_enqueue_style( 'wp-color-picker' );

				wp_register_script( 'mobiloud-app-preview-js', MOBILOUD_PLUGIN_URL . '/assets/js/mobiloud-app-preview.js', array( 'jquery', 'notify-js' ), MOBILOUD_PLUGIN_VERSION );
				wp_enqueue_script( 'mobiloud-app-preview-js' );

				wp_register_style( 'mobiloud-app-preview', MOBILOUD_PLUGIN_URL . '/assets/css/mobiloud-app-preview.css', false, MOBILOUD_PLUGIN_VERSION );
				wp_enqueue_style( 'mobiloud-app-preview' );

				global $current_user;
				wp_get_current_user();

				/**
				* Process Form
				*/
				if ( count( $_POST ) && check_admin_referer( 'ml-form-' . $tab ) ) {
					if ( isset( $_POST['ml_preview_upload_image'] ) ) {
						Mobiloud::set_option( 'ml_preview_upload_image', sanitize_text_field( $_POST['ml_preview_upload_image'] ) );
					}
					if ( isset( $_POST['ml_preview_theme_color'] ) ) {
						Mobiloud::set_option( 'ml_preview_theme_color', sanitize_hex_color( $_POST['ml_preview_theme_color'] ) );
					}
					if ( isset( $_POST['ml_article_list_view_type'] ) ) {
						Mobiloud::set_option( 'ml_article_list_view_type', sanitize_text_field( $_POST['ml_article_list_view_type'] ) );
					}
					if ( ! isset( $_POST['ml_show_android_cat_tabs'] ) ) {
						$_POST['ml_show_android_cat_tabs'] = 'false';
					}
					Mobiloud::set_option( 'ml_show_android_cat_tabs', ( $_POST['ml_show_android_cat_tabs'] == 'true' ) );

					if ( ! isset( $_POST['ml_allow_landscape'] ) ) {
						$_POST['ml_allow_landscape'] = 'false';
					}
					Mobiloud::set_option( 'ml_allow_landscape', ( $_POST['ml_allow_landscape'] == 'true' ) );

					Mobiloud::set_option( 'ml_rtl_text_enable', isset( $_POST['ml_rtl_text_enable'] ) );

					self::set_task_status( 'design', 'complete' );
				}

				if ( strlen( trim( get_option( 'ml_preview_theme_color' ) ) ) <= 2 ) {
					update_option( 'ml_preview_theme_color', '#1e73be' );
				}

				$root_url              = network_site_url( '/' );
				$plugins_url           = plugins_url();
				$mobiloudPluginUrl     = MOBILOUD_PLUGIN_URL;
				$mobiloudPluginVersion = MOBILOUD_PLUGIN_VERSION;
				$appname               = get_bloginfo( 'name' );

				self::render_view( 'get_started_design', 'get_started' );
				break;
			case 'menu_config':

				wp_enqueue_media();

				// Color picker
				wp_enqueue_script( 'wp-color-picker' );
				wp_enqueue_style( 'wp-color-picker' );

				/**
				* Process Form
				*/
				if ( count( $_POST ) && check_admin_referer( 'ml-form-' . $tab ) ) {

					$hamburger_nav = '';
					if ( ! empty( $_POST['ml-hamburger-nav'] ) ) {
						$hamburger_nav = sanitize_text_field( $_POST['ml-hamburger-nav'] );
					}
					Mobiloud::set_option( 'ml_hamburger_nav', $hamburger_nav );

					$horizontal_nav = '';
					if ( ! empty( $_POST['ml-horizontal-nav'] ) ) {
						$horizontal_nav = sanitize_text_field( $_POST['ml-horizontal-nav'] );
					}
					Mobiloud::set_option( 'ml_horizontal_nav', $horizontal_nav );

					$ml_sections_menu = '';
					if ( ! empty( $_POST['ml-sections-menu'] ) ) {
						$ml_sections_menu = sanitize_text_field( $_POST['ml-sections-menu'] );
					}
					Mobiloud::set_option( 'ml_sections_menu', $ml_sections_menu );

					/*
					* Custom menu - save hook
					*/
					do_action('ml-menu-form-section-save');

					Mobiloud::set_option( 'ml_tabbed_navigation_enabled', empty( $_POST['ml_tabbed_navigation_enabled'] ) ? '0' : '1' );

					if ( ! empty( $_POST['ml_tabbed_navigation'] ) ) {
						$tn_data = $_POST['ml_tabbed_navigation'];
						array_walk_recursive( $tn_data, 'sanitize_text_field' );

						$tn_tabs = array();

						// set the tab order
						$order = explode( ',', $tn_data['taborder'] );
						foreach( $order as $index ) {
							$tn_tabs[] = $tn_data['tabs'][ '' . absint( $index ) ];
						}
						$tn_data['tabs'] = $tn_tabs;

						unset( $tn_data['taborder'] );

						$list_type = get_option( 'ml_list_type', 'native' );
						$base_endpoint_url = trailingslashit( get_bloginfo( 'url' ) ) . 'ml-api/v2/posts';

						if ( $list_type == 'web' ) {
							$base_endpoint_url = trailingslashit( get_bloginfo( 'url' ) ) . 'ml-api/v2/list';
						}

						foreach( $tn_data['tabs'] as $i => $tab ) {

							if( isset( $tab['enabled'] ) ) {
								$tn_data['tabs'][$i]['enabled'] = '1';
							} else {
								$tn_data['tabs'][$i]['enabled'] = '0';
							}

							switch( $tab['type'] ) {
								case 'homescreen':
									$tn_data['tabs'][$i]['endpoint_url'] = $base_endpoint_url;

									unset($tn_data['tabs'][$i]['taxonomy_type']);
									unset($tn_data['tabs'][$i]['taxonomy_id']);
									unset($tn_data['tabs'][$i]['taxonomy_orderby']);
									unset($tn_data['tabs'][$i]['taxonomy_order']);

									break;

								case 'list':
									$endpoint = $base_endpoint_url . '?taxonomy=' . $tab['taxonomy_type'];
									$endpoint .= '&term_id=' . $tab['taxonomy_id'];
									$endpoint .= '&orderby=' . $tab['taxonomy_orderby'];
									$endpoint .= '&order=' . $tab['taxonomy_order'];
									$tn_data['tabs'][$i]['endpoint_url'] = $endpoint;

									break;

								case 'sections':
									$tn_data['tabs'][$i]['endpoint_url'] = trailingslashit( get_bloginfo( 'url' ) ) . 'ml-api/v2/sections';

									unset($tn_data['tabs'][$i]['taxonomy_type']);
									unset($tn_data['tabs'][$i]['taxonomy_id']);
									unset($tn_data['tabs'][$i]['taxonomy_orderby']);
									unset($tn_data['tabs'][$i]['taxonomy_order']);

									break;

								default:
									unset($tn_data['tabs'][$i]['taxonomy_type']);
									unset($tn_data['tabs'][$i]['taxonomy_id']);
									unset($tn_data['tabs'][$i]['taxonomy_orderby']);
									unset($tn_data['tabs'][$i]['taxonomy_order']);

									break;
							}

						}

						Mobiloud::set_option( 'ml_tabbed_navigation', $tn_data );
					}


					if ( isset( $_POST['ml-menu-categories_loaded'] ) ) {
						ml_remove_all_categories();
						if ( isset( $_POST['ml-menu-categories'] ) && count( $_POST['ml-menu-categories'] ) ) {
							foreach ( $_POST['ml-menu-categories'] as $cat_ID ) {
								ml_add_category( sanitize_text_field( $cat_ID ) );
							}
						}
					}

					$menu_terms = array();
					if ( ! empty( $_POST['ml-menu-terms'] ) ) {
						foreach ( $_POST['ml-menu-terms'] as $term ) {
							$menu_terms[] = $term;
						}
					}
					Mobiloud::set_option( 'ml_menu_terms', $menu_terms );

					if ( isset( $_POST['ml-menu-tags_loaded'] ) ) {
						$menu_tags = array();
						if ( isset( $_POST['ml-menu-tags'] ) && count( $_POST['ml-menu-tags'] ) ) {
							foreach ( $_POST['ml-menu-tags'] as $tag ) {
								$menu_tags[] = absint( $tag );
							}
						}
						Mobiloud::set_option( 'ml_menu_tags', $menu_tags );
					}

					if ( isset( $_POST['ml-menu-pages_loaded'] ) ) {
						ml_remove_all_pages();
						if ( isset( $_POST['ml-menu-pages'] ) && count( $_POST['ml-menu-pages'] ) ) {
							foreach ( $_POST['ml-menu-pages'] as $page_ID ) {
								ml_add_page( sanitize_text_field( $page_ID ) );
							}
						}
					}

					$menu_links = array();
					if ( isset( $_POST['ml-menu-links'] ) && count( $_POST['ml-menu-links'] ) ) {
						foreach ( $_POST['ml-menu-links'] as $menu_link ) {
							$menu_link_vals = explode( ':=:', $menu_link );
							if ( 2 == count($menu_link_vals) ) {
								$menu_links[]   = array(
									'urlTitle' => sanitize_text_field( $menu_link_vals[0] ),
									'url'      => esc_url_raw( $menu_link_vals[1] ),
								);
							}
						}
					}
					Mobiloud::set_option( 'ml_menu_urls', $menu_links );

					Mobiloud::set_option( 'ml_menu_show_favorites', $_POST['ml_menu_show_favorites'] == 'true' );

					self::set_task_status( 'menu_config', 'complete' );
				}
				self::render_view( 'get_started_menu_config', 'get_started' );
				break;

			case 'login_settings':
				wp_enqueue_media();

				// Color picker
				wp_enqueue_script( 'wp-color-picker' );
				wp_enqueue_style( 'wp-color-picker' );

				/**
				* Process Form
				*/
				if ( count( $_POST ) && check_admin_referer( 'ml-form-' . $tab ) ) {

					$login_settings = array();
					if ( ! empty( $_POST['ml_login_settings'] ) ) {
						$login_settings =  array_map( 'sanitize_text_field', wp_unslash( $_POST['ml_login_settings'] ) );
					}
					Mobiloud::set_option( 'ml_login_settings', $login_settings );

					self::set_task_status( 'login_settings', 'complete' );
				}

				self::render_view( 'settings_login', 'get_started' );

				break;

			case 'paywall':
				wp_enqueue_media();

				// Color picker
				wp_enqueue_script( 'wp-color-picker' );
				wp_enqueue_style( 'wp-color-picker' );

				/**
				* Process Form
				*/
				if ( count( $_POST ) && check_admin_referer( 'ml-form-' . $tab ) ) {

					$paywall_settings = array();
					if ( ! empty( $_POST['ml_paywall_settings'] ) && is_array( $_POST['ml_paywall_settings'] ) ) {
						$paywall_settings = $_POST['ml_paywall_settings']; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
					}
					Mobiloud::set_option( 'ml_paywall_settings', $paywall_settings );

					self::set_task_status( 'paywall', 'complete' );
				}

				self::render_view( 'settings_paywall', 'get_started' );

				break;

			case 'settings':
				wp_enqueue_media();

				// Color picker
				wp_enqueue_script( 'wp-color-picker' );
				wp_enqueue_style( 'wp-color-picker' );

				wp_register_script( 'mobiloud-settings', MOBILOUD_PLUGIN_URL . '/assets/js/mobiloud-settings.js', array( 'jquery' ) );
				wp_enqueue_script( 'mobiloud-settings' );
				/**
				* Process Form
				*/
				if ( count( $_POST ) && check_admin_referer( 'ml-form-' . $tab ) ) {
					if ( isset( $_POST['ml_app_name'] ) ) {
						Mobiloud::set_option( 'ml_app_name', sanitize_text_field( $_POST['ml_app_name'] ) );
					}
					Mobiloud::set_option( 'ml_show_email_contact_link', isset( $_POST['ml_show_email_contact_link'] ) );
					if ( isset( $_POST['ml_contact_link_email'] ) ) {
						Mobiloud::set_option( 'ml_contact_link_email', sanitize_email( $_POST['ml_contact_link_email'] ) );
					}
					if ( isset( $_POST['ml_copyright_string'] ) ) {
						Mobiloud::set_option( 'ml_copyright_string', sanitize_text_field( $_POST['ml_copyright_string'] ) );
					}
					if ( isset( $_POST['ml_default_featured_image'] ) ) {
						Mobiloud::set_option( 'ml_default_featured_image', sanitize_text_field( $_POST['ml_default_featured_image'] ) );
					}

					if ( isset( $_POST['homepagetype'] ) ) {
						switch ( sanitize_text_field( $_POST['homepagetype'] ) ) {
							case 'ml_home_article_list_enabled':
								Mobiloud::set_option( 'ml_home_article_list_enabled', true );
								Mobiloud::set_option( 'ml_home_page_enabled', false );
								Mobiloud::set_option( 'ml_home_url_enabled', false );
								break;
							case 'ml_home_page_enabled':
								Mobiloud::set_option( 'ml_home_article_list_enabled', false );
								Mobiloud::set_option( 'ml_home_page_enabled', true );
								Mobiloud::set_option( 'ml_home_url_enabled', false );
								break;
							case 'ml_home_url_enabled':
								Mobiloud::set_option( 'ml_home_article_list_enabled', false );
								Mobiloud::set_option( 'ml_home_page_enabled', false );
								Mobiloud::set_option( 'ml_home_url_enabled', true );
								break;
						}
					}
					if ( isset( $_POST['ml_home_page_id'] ) ) {
						Mobiloud::set_option( 'ml_home_page_id', sanitize_text_field( $_POST['ml_home_page_id'] ) );
					}
					if ( isset( $_POST['ml_home_url'] ) ) {
						Mobiloud::set_option( 'ml_home_url', sanitize_text_field( $_POST['ml_home_url'] ) );
					}
					if ( isset( $_POST['ml_article_list_menu_item_title'] ) ) {
						Mobiloud::set_option( 'ml_article_list_menu_item_title', sanitize_text_field( $_POST['ml_article_list_menu_item_title'] ) );
					}

					if ( isset( $_POST['ml_datetype'] ) ) {
						Mobiloud::set_option( 'ml_datetype', sanitize_text_field( $_POST['ml_datetype'] ) );
					}

					if ( isset( $_POST['ml_list_type'] ) ) {
						Mobiloud::set_option( 'ml_list_type', sanitize_text_field( $_POST['ml_list_type'] ) );
					}

					if ( isset( $_POST['ml_dateformat'] ) ) {
						Mobiloud::set_option( 'ml_dateformat', sanitize_text_field( $_POST['ml_dateformat'] ) );
					}
					Mobiloud::set_option( 'ml_article_list_enable_dates', isset( $_POST['ml_article_list_enable_dates'] ) );
					Mobiloud::set_option( 'ml_article_list_show_excerpt', isset( $_POST['ml_article_list_show_excerpt'] ) );
					Mobiloud::set_option( 'ml_article_list_show_comment_count', isset( $_POST['ml_article_list_show_comment_count'] ) );
					Mobiloud::set_option( 'ml_original_size_image_list', isset( $_POST['ml_original_size_image_list'] ) );

					$ml_excerpt_length = ! empty( $_POST['ml_excerpt_length'] ) ? absint( $_POST['ml_excerpt_length'] ) : 100;
					$ml_excerpt_length = max( array( 1, min( array( $ml_excerpt_length, 10000 ) ) ) );
					Mobiloud::set_option( 'ml_excerpt_length', $ml_excerpt_length );

					$ml_articles_per_request = ! empty( $_POST['ml_articles_per_request'] ) ? absint( $_POST['ml_articles_per_request'] ) : 15;
					$ml_articles_per_request = max( array( 1, min( array( $ml_articles_per_request, 100 ) ) ) );
					Mobiloud::set_option( 'ml_articles_per_request', $ml_articles_per_request );

					if ( isset( $_POST['ml_main_screen_tax_list_loaded'] ) ) {
						Mobiloud::set_option( 'ml_main_screen_tax_list', ! empty( $_POST['ml_main_screen_tax_list'] ) ? array_map( 'sanitize_text_field', $_POST['ml_main_screen_tax_list'] ) : array() );
					}
					if ( isset( $_POST['sticky_category_1_loaded'] ) && isset( $_POST['sticky_category_1'] ) ) {
						Mobiloud::set_option( 'sticky_category_1', sanitize_text_field( $_POST['sticky_category_1'] ) );
					}
					if ( isset( $_POST['ml_sticky_category_1_posts'] ) ) {
						Mobiloud::set_option( 'ml_sticky_category_1_posts', sanitize_text_field( $_POST['ml_sticky_category_1_posts'] ) );
					}
					if ( isset( $_POST['sticky_category_2_loaded'] ) && isset( $_POST['sticky_category_2'] ) ) {
						Mobiloud::set_option( 'sticky_category_2', sanitize_text_field( $_POST['sticky_category_2'] ) );
					}
					if ( isset( $_POST['ml_sticky_category_2_posts'] ) ) {
						Mobiloud::set_option( 'ml_sticky_category_2_posts', sanitize_text_field( $_POST['ml_sticky_category_2_posts'] ) );
					}

					$include_post_types = '';
					if ( isset( $_POST['postypes'] ) && count( array_map( 'sanitize_text_field', $_POST['postypes'] ) ) ) {
						$include_post_types = implode( ',', array_map( 'sanitize_text_field', $_POST['postypes'] ) );
					}
					Mobiloud::set_option( 'ml_article_list_include_post_types', sanitize_text_field( $include_post_types ) );

					if ( isset( $_POST['categories_loaded'] ) ) {
						$categories         = get_categories( array( 'hide_empty' => false ) );
						$exclude_categories = array();
						if ( count( $categories ) ) {
							foreach ( $categories as $category ) {
								if ( ! isset( $_POST['categories'] ) || count( array_map( 'sanitize_text_field', $_POST['categories'] ) ) === 0  || ( isset( $_POST['categories'] ) && ! in_array( wp_slash( html_entity_decode( $category->cat_name ) ), array_map( 'sanitize_text_field', $_POST['categories'] ), true ) ) ) {
									$exclude_categories[] = $category->cat_name;
								}
							}
						}

						Mobiloud::set_option( 'ml_article_list_exclude_categories', implode( ',', $exclude_categories ) );
					}
					Mobiloud::set_option( 'ml_restrict_search_results', isset( $_POST['ml_restrict_search_results'] ) );

					Mobiloud::set_option( 'ml_custom_field_enable', isset( $_POST['ml_custom_field_enable'] ) );

					if ( isset( $_POST['ml_custom_field_name'] ) ) {
						Mobiloud::set_option( 'ml_custom_field_name', sanitize_text_field( $_POST['ml_custom_field_name'] ) );
					}

					Mobiloud::set_option( 'ml_eager_loading_enable', isset( $_POST['ml_eager_loading_enable'] ) );
					Mobiloud::set_option( 'ml_hierarchical_pages_enabled', isset( $_POST['ml_hierarchical_pages_enabled'] ) );
					Mobiloud::set_option( 'ml_cache_enabled', isset( $_POST['ml_cache_enabled'] ) );
					Mobiloud::set_option( 'ml_image_cache_preload', isset( $_POST['ml_image_cache_preload'] ) );
					Mobiloud::set_option( 'ml_remove_unused_shortcodes', isset( $_POST['ml_remove_unused_shortcodes'] ) );
					Mobiloud::set_option( 'ml_fix_rsssl', isset( $_POST['ml_fix_rsssl'] ) );
					Mobiloud::set_option( 'ml_disable_notices', isset( $_POST['ml_disable_notices'] ) );
					Mobiloud::set_option( 'ml_internal_links', isset( $_POST['ml_internal_links'] ) );
					Mobiloud::set_option( 'ml_related_posts', isset( $_POST['ml_related_posts'] ) );
					if ( isset( $_POST['ml_related_header'] ) ) {
						Mobiloud::set_option( 'ml_related_header', sanitize_text_field( $_POST['ml_related_header'] ) );
					}
					Mobiloud::set_option( 'ml_related_image', isset( $_POST['ml_related_image'] ) );
					Mobiloud::set_option( 'ml_related_excerpt', isset( $_POST['ml_related_excerpt'] ) );
					Mobiloud::set_option( 'ml_related_date', isset( $_POST['ml_related_date'] ) );

					Mobiloud::set_option( 'ml_followimagelinks', ( isset( $_POST['ml_followimagelinks'] ) ? intval( $_POST['ml_followimagelinks'] ) : 0 ) );
					Mobiloud::set_option( 'ml_show_article_featuredimage', isset( $_POST['ml_show_article_featuredimage'] ) );
					Mobiloud::set_option( 'ml_original_size_featured_image', isset( $_POST['ml_original_size_featured_image'] ) );
					Mobiloud::set_option( 'ml_post_author_enabled', isset( $_POST['ml_post_author_enabled'] ) );
					Mobiloud::set_option( 'ml_page_author_enabled', isset( $_POST['ml_page_author_enabled'] ) );
					Mobiloud::set_option( 'ml_post_date_enabled', isset( $_POST['ml_post_date_enabled'] ) );
					Mobiloud::set_option( 'ml_page_date_enabled', isset( $_POST['ml_page_date_enabled'] ) );
					Mobiloud::set_option( 'ml_post_title_enabled', isset( $_POST['ml_post_title_enabled'] ) );
					Mobiloud::set_option( 'ml_page_title_enabled', isset( $_POST['ml_page_title_enabled'] ) );

					if ( isset( $_POST['ml_custom_field_url'] ) ) {
						Mobiloud::set_option( 'ml_custom_field_url', sanitize_text_field( $_POST['ml_custom_field_url'] ) );
					}
					if ( isset( $_POST['ml_custom_featured_image'] ) ) {
						Mobiloud::set_option( 'ml_custom_featured_image', sanitize_text_field( $_POST['ml_custom_featured_image'] ) );
					}

					if ( isset( $_POST['ml_comments_system'] ) ) {
						Mobiloud::set_option( 'ml_comments_system', sanitize_text_field( $_POST['ml_comments_system'] ) );
					}
					if ( isset( $_POST['ml_disqus_shortname'] ) ) {
						Mobiloud::set_option( 'ml_disqus_shortname', sanitize_text_field( $_POST['ml_disqus_shortname'] ) );
					}

					Mobiloud::set_option( 'ml_comments_rest_api_enabled', isset( $_POST['ml_comments_rest_api_enabled'] ) );

					Mobiloud::set_option( 'ml_subscriptions_enable', isset( $_POST['ml_subscriptions_enable'] ) );

					Mobiloud::set_option( 'ml_show_rating_prompt', isset( $_POST['ml_show_rating_prompt'] ) );
					if ( isset( $_POST['ml_days_interval_rating_prompt'] ) ) {
						Mobiloud::set_option( 'ml_days_interval_rating_prompt', max( array( 1, (int) $_POST['ml_days_interval_rating_prompt'] ) ) );
					}

					if ( isset( $_POST['ml_welcome_screen_url'] ) ) {
						Mobiloud::set_option( 'ml_welcome_screen_url', sanitize_text_field( $_POST['ml_welcome_screen_url'] ) );
					}
					if ( isset( $_POST['ml_welcome_screen_required_version'] ) ) {
						Mobiloud::set_option( 'ml_welcome_screen_required_version', sanitize_text_field( $_POST['ml_welcome_screen_required_version'] ) );
					}

					Mobiloud::set_option( 'ml_cache_expiration', ! empty( $_POST['ml_cache_expiration'] ) ? absint( $_POST['ml_cache_expiration'] ) : 30 );

					Mobiloud::set_option( 'ml_push_notification_settings_enabled', ( isset( $_POST['ml_push_notification_settings_enabled'] ) ? '1' : '0' ) );
					Mobiloud::set_option( 'ml_general_settings_enabled', ( isset( $_POST['ml_general_settings_enabled'] ) ? '1' : '0' ) );
					if ( isset( $_POST['ml_share_app_url'] ) ) {
						Mobiloud::set_option( 'ml_share_app_url', sanitize_text_field( $_POST['ml_share_app_url'] ) );
					}
					if ( isset( $_POST['ml_push_notification_menu'] ) ) {
						Mobiloud::set_option( 'ml_push_notification_menu', sanitize_text_field( $_POST['ml_push_notification_menu'] ) );
					}
					if ( isset( $_POST['ml_general_settings_menu'] ) ) {
						Mobiloud::set_option( 'ml_general_settings_menu', sanitize_text_field( $_POST['ml_general_settings_menu'] ) );
					}
					if ( isset( $_POST['ml_settings_title_color'] ) ) {
						Mobiloud::set_option( 'ml_settings_title_color', sanitize_hex_color( $_POST['ml_settings_title_color'] ) );
					}
					if ( isset( $_POST['ml_settings_active_switch_color'] ) ) {
						Mobiloud::set_option( 'ml_settings_active_switch_color', sanitize_hex_color( $_POST['ml_settings_active_switch_color'] ) );
					}
					if ( isset( $_POST['ml_settings_active_switch_background_color'] ) ) {
						Mobiloud::set_option( 'ml_settings_active_switch_background_color', sanitize_hex_color( $_POST['ml_settings_active_switch_background_color'] ) );
					}
					if ( isset( $_POST['ml_settings_inactive_switch_color'] ) ) {
						Mobiloud::set_option( 'ml_settings_inactive_switch_color', sanitize_hex_color( $_POST['ml_settings_inactive_switch_color'] ) );
					}
					if ( isset( $_POST['ml_settings_inactive_switch_background_color'] ) ) {
						Mobiloud::set_option( 'ml_settings_inactive_switch_background_color', sanitize_hex_color( $_POST['ml_settings_inactive_switch_background_color'] ) );
					}
				}
				self::render_view( 'settings_settings', 'get_started' );
				break;
			case 'analytics':
				/**
				* Process Form
				*/
				if ( count( $_POST ) && check_admin_referer( 'ml-form-' . $tab ) ) {
					if ( isset( $_POST['ml_google_tracking_id'] ) ) {
						Mobiloud::set_option( 'ml_google_tracking_id', sanitize_text_field( $_POST['ml_google_tracking_id'] ) );
					}
					if ( isset( $_POST['ml_fb_app_id'] ) ) {
						Mobiloud::set_option( 'ml_fb_app_id', sanitize_text_field( $_POST['ml_fb_app_id'] ) );
					}
					if ( isset( $_POST['ml_qm_api_key'] ) ) {
						Mobiloud::set_option( 'ml_qm_api_key', sanitize_text_field( $_POST['ml_qm_api_key'] ) );
					}
					if ( isset( $_POST['ml_comscore_c2'] ) ) {
						Mobiloud::set_option( 'ml_comscore_c2', sanitize_text_field( $_POST['ml_comscore_c2'] ) );
					}
					if ( isset( $_POST['ml_comscore_secret'] ) ) {
						Mobiloud::set_option( 'ml_comscore_secret', sanitize_text_field( $_POST['ml_comscore_secret'] ) );
					}
				}
				self::render_view( 'settings_analytics', 'get_started' );
				break;
			case 'subscription':
				/**
				* Process Form
				*/
				if ( count( $_POST ) && check_admin_referer( 'ml-form-' . $tab ) ) {
					Mobiloud::set_option( 'ml_app_subscription_enabled', isset( $_POST['ml_app_subscription_enabled'] ) );

					if ( isset( $_POST['ml_app_subscription_ios_in_app_purchase_id'] ) ) {
						Mobiloud::set_option( 'ml_app_subscription_ios_in_app_purchase_id', sanitize_text_field( $_POST['ml_app_subscription_ios_in_app_purchase_id'] ) );
					}
					if ( isset( $_POST['ml_app_subscription_android_in_app_purchase_id'] ) ) {
						Mobiloud::set_option( 'ml_app_subscription_android_in_app_purchase_id', sanitize_text_field( $_POST['ml_app_subscription_android_in_app_purchase_id'] ) );
					}
					if ( isset( $_POST['ml_app_subscriptions_subscribe_link_text'] ) ) {
						Mobiloud::set_option( 'ml_app_subscriptions_subscribe_link_text', sanitize_text_field( $_POST['ml_app_subscriptions_subscribe_link_text'] ) );
					}
					if ( isset( $_POST['ml_app_subscriptions_manage_subscription_link_text'] ) ) {
						Mobiloud::set_option( 'ml_app_subscriptions_manage_subscription_link_text', sanitize_text_field( $_POST['ml_app_subscriptions_manage_subscription_link_text'] ) );
					}
					if ( isset( $_POST['ml_app_subscription_logo'] ) ) {
						Mobiloud::set_option( 'ml_app_subscription_logo', sanitize_text_field( $_POST['ml_app_subscription_logo'] ) );
					}
					if ( isset( $_POST['ml_app_subscription_background_color'] ) ) {
						Mobiloud::set_option( 'ml_app_subscription_background_color', sanitize_hex_color( $_POST['ml_app_subscription_background_color'] ) );
					}

					if ( isset( $_POST['ml_app_subscription_title'] ) ) {
						Mobiloud::set_option( 'ml_app_subscription_title', sanitize_text_field( $_POST['ml_app_subscription_title'] ) );
					}
					if ( isset( $_POST['ml_app_subscription_description'] ) ) {
						Mobiloud::set_option( 'ml_app_subscription_description', sanitize_text_field( $_POST['ml_app_subscription_description'] ) );
					}
					if ( isset( $_POST['ml_app_subscription_call_to_action_color'] ) ) {
						Mobiloud::set_option( 'ml_app_subscription_call_to_action_color', sanitize_hex_color( $_POST['ml_app_subscription_call_to_action_color'] ) );
					}

					if ( isset( $_POST['ml_app_subscription_btn_title'] ) ) {
						Mobiloud::set_option( 'ml_app_subscription_btn_title', sanitize_text_field( $_POST['ml_app_subscription_btn_title'] ) );
					}
					if ( isset( $_POST['ml_app_subscription_btn_description'] ) ) {
						Mobiloud::set_option( 'ml_app_subscription_btn_description', sanitize_text_field( $_POST['ml_app_subscription_btn_description'] ) );
					}
					if ( isset( $_POST['ml_app_subscription_trial_btn_title'] ) ) {
						Mobiloud::set_option( 'ml_app_subscription_trial_btn_title', sanitize_text_field( $_POST['ml_app_subscription_trial_btn_title'] ) );
					}
					if ( isset( $_POST['ml_app_subscription_trial_btn_description'] ) ) {
						Mobiloud::set_option( 'ml_app_subscription_trial_btn_description', sanitize_text_field( $_POST['ml_app_subscription_trial_btn_description'] ) );
					}
					if ( isset( $_POST['ml_app_subscription_btn_text_color'] ) ) {
						Mobiloud::set_option( 'ml_app_subscription_btn_text_color', sanitize_hex_color( $_POST['ml_app_subscription_btn_text_color'] ) );
					}
					if ( isset( $_POST['ml_app_subscription_btn_background_color'] ) ) {
						Mobiloud::set_option( 'ml_app_subscription_btn_background_color', sanitize_hex_color( $_POST['ml_app_subscription_btn_background_color'] ) );
					}

					if ( isset( $_POST['ml_app_subscription_small_description'] ) ) {
						Mobiloud::set_option( 'ml_app_subscription_small_description', sanitize_text_field( $_POST['ml_app_subscription_small_description'] ) );
					}
					if ( isset( $_POST['ml_app_subscription_small_description_color'] ) ) {
						Mobiloud::set_option( 'ml_app_subscription_small_description_color', sanitize_hex_color( $_POST['ml_app_subscription_small_description_color'] ) );
					}
					if ( isset( $_POST['ml_app_subscription_close_btn_color'] ) ) {
						Mobiloud::set_option( 'ml_app_subscription_close_btn_color', sanitize_hex_color( $_POST['ml_app_subscription_close_btn_color'] ) );
					}

				}
				wp_enqueue_script( 'wp-color-picker' );
				wp_enqueue_media();
				wp_enqueue_style( 'wp-color-picker' );

				self::render_view( 'settings_subscription', 'get_started' );
				break;
			case 'advertising':
				/**
				* Process Form
				*/
				if ( count( $_POST ) && check_admin_referer( 'ml-form-' . $tab ) ) {
					if ( isset( $_POST['ml_privacy_policy_url'] ) ) {
						Mobiloud::set_option( 'ml_privacy_policy_url', sanitize_text_field( $_POST['ml_privacy_policy_url'] ) );
					}
					if ( isset( $_POST['ml_advertising_platform'] ) ) {
						Mobiloud::set_option( 'ml_advertising_platform', sanitize_text_field( $_POST['ml_advertising_platform'] ) );
					}

					// iOS
					Mobiloud::set_option( 'ml_ios_admob_app_id', sanitize_text_field( $_POST['ml_ios_admob_app_id'] ) );
					Mobiloud::set_option( 'ml_ios_phone_banner_unit_id', sanitize_text_field( $_POST['ml_ios_phone_banner_unit_id'] ) );
					Mobiloud::set_option( 'ml_ios_tablet_banner_unit_id', sanitize_text_field( $_POST['ml_ios_tablet_banner_unit_id'] ) );
					Mobiloud::set_option( 'ml_ios_banner_position', sanitize_text_field( $_POST['ml_ios_banner_position'] ) );
					Mobiloud::set_option( 'ml_ios_interstitial_unit_id', sanitize_text_field( $_POST['ml_ios_interstitial_unit_id'] ) );
					Mobiloud::set_option( 'ml_ios_interstitial_interval', (int) sanitize_text_field( $_POST['ml_ios_interstitial_interval'] ) );
					Mobiloud::set_option( 'ml_ios_native_ad_unit_id', sanitize_text_field( $_POST['ml_ios_native_ad_unit_id'] ) );
					Mobiloud::set_option( 'ml_ios_native_ad_interval', (int) sanitize_text_field( $_POST['ml_ios_native_ad_interval'] ) );
					Mobiloud::set_option( 'ml_ios_native_ad_type', sanitize_text_field( $_POST['ml_ios_native_ad_type'] ) );
					Mobiloud::set_option( 'ml_ios_native_ad_article_unit_id', sanitize_text_field( $_POST['ml_ios_native_ad_article_unit_id'] ) );
					Mobiloud::set_option( 'ml_ios_native_ad_article_position', sanitize_text_field( $_POST['ml_ios_native_ad_article_position'] ) );
					Mobiloud::set_option( 'ml_ios_native_ad_article_type', sanitize_text_field( $_POST['ml_ios_native_ad_article_type'] ) );

					Mobiloud::set_option( 'ml_ios_phone_banner_app_subscription_show', isset( $_POST['ml_ios_phone_banner_app_subscription_show'] ) );
					Mobiloud::set_option( 'ml_ios_tablet_banner_app_subscription_show', isset( $_POST['ml_ios_tablet_banner_app_subscription_show'] ) );
					Mobiloud::set_option( 'ml_ios_interstitial_app_subscription_show', isset( $_POST['ml_ios_interstitial_app_subscription_show'] ) );
					Mobiloud::set_option( 'ml_ios_native_ad_app_subscription_show', isset( $_POST['ml_ios_native_ad_app_subscription_show'] ) );
					Mobiloud::set_option( 'ml_ios_native_ad_article_app_subscription_show', isset( $_POST['ml_ios_native_ad_article_app_subscription_show'] ) );

					// Android
					if ( isset( $_POST['ml_android_admob_app_id'] ) ) {
						Mobiloud::set_option( 'ml_android_admob_app_id', sanitize_text_field( $_POST['ml_android_admob_app_id'] ) );
					}
					if ( isset( $_POST['ml_android_phone_banner_unit_id'] ) ) {
						Mobiloud::set_option( 'ml_android_phone_banner_unit_id', sanitize_text_field( $_POST['ml_android_phone_banner_unit_id'] ) );
					}
					if ( isset( $_POST['ml_android_tablet_banner_unit_id'] ) ) {
						Mobiloud::set_option( 'ml_android_tablet_banner_unit_id', sanitize_text_field( $_POST['ml_android_tablet_banner_unit_id'] ) );
					}
					if ( isset( $_POST['ml_android_banner_position'] ) ) {
						Mobiloud::set_option( 'ml_android_banner_position', sanitize_text_field( $_POST['ml_android_banner_position'] ) );
					}
					if ( isset( $_POST['ml_android_interstitial_unit_id'] ) ) {
						Mobiloud::set_option( 'ml_android_interstitial_unit_id', sanitize_text_field( $_POST['ml_android_interstitial_unit_id'] ) );
					}
					if ( isset( $_POST['ml_android_interstitial_interval'] ) ) {
						Mobiloud::set_option( 'ml_android_interstitial_interval', (int) sanitize_text_field( $_POST['ml_android_interstitial_interval'] ) );
					}
					if ( isset( $_POST['ml_android_native_ad_unit_id'] ) ) {
						Mobiloud::set_option( 'ml_android_native_ad_unit_id', sanitize_text_field( $_POST['ml_android_native_ad_unit_id'] ) );
					}
					if ( isset( $_POST['ml_android_native_ad_interval'] ) ) {
						Mobiloud::set_option( 'ml_android_native_ad_interval', (int) sanitize_text_field( $_POST['ml_android_native_ad_interval'] ) );
					}
					if ( isset( $_POST['ml_android_native_ad_type'] ) ) {
						Mobiloud::set_option( 'ml_android_native_ad_type', sanitize_text_field( $_POST['ml_android_native_ad_type'] ) );
					}
					if ( isset( $_POST['ml_android_native_ad_article_unit_id'] ) ) {
						Mobiloud::set_option( 'ml_android_native_ad_article_unit_id', sanitize_text_field( $_POST['ml_android_native_ad_article_unit_id'] ) );
					}
					if ( isset( $_POST['ml_android_native_ad_article_position'] ) ) {
						Mobiloud::set_option( 'ml_android_native_ad_article_position', sanitize_text_field( $_POST['ml_android_native_ad_article_position'] ) );
					}
					if ( isset( $_POST['ml_android_native_ad_article_type'] ) ) {
						Mobiloud::set_option( 'ml_android_native_ad_article_type', sanitize_text_field( $_POST['ml_android_native_ad_article_type'] ) );
					}

					Mobiloud::set_option( 'ml_android_phone_banner_app_subscription_show', isset( $_POST['ml_android_phone_banner_app_subscription_show'] ) );
					Mobiloud::set_option( 'ml_android_tablet_app_subscription_show', isset( $_POST['ml_android_tablet_app_subscription_show'] ) );
					Mobiloud::set_option( 'ml_android_interstitial_app_subscription_show', isset( $_POST['ml_android_interstitial_app_subscription_show'] ) );
					Mobiloud::set_option( 'ml_android_native_ad_app_subscription_show', isset( $_POST['ml_android_native_ad_app_subscription_show'] ) );
					Mobiloud::set_option( 'ml_android_native_ad_article_app_subscription_show', isset( $_POST['ml_android_native_ad_article_app_subscription_show'] ) );
				}
				self::render_view( 'settings_advertising', 'get_started' );
				break;
			case 'editor':
				wp_register_script( 'codemirror', MOBILOUD_PLUGIN_URL . '/libs/codemirror/lib/codemirror.js', array(), MOBILOUD_PLUGIN_VERSION );
				wp_enqueue_script( 'codemirror' );
				wp_register_script( 'codemirror-cssmode', MOBILOUD_PLUGIN_URL . '/libs/codemirror/mode/css/css.js', array('codemirror'), MOBILOUD_PLUGIN_VERSION );
				wp_enqueue_script( 'codemirror-cssmode' );
				wp_register_script( 'codemirror-phpmode', MOBILOUD_PLUGIN_URL . '/libs/codemirror/mode/php/php.js', array('codemirror'), MOBILOUD_PLUGIN_VERSION );
				wp_enqueue_script( 'codemirror-phpmode' );
				wp_register_script( 'codemirror-htmlmixed', MOBILOUD_PLUGIN_URL . '/libs/codemirror/mode/htmlmixed/htmlmixed.js', array('codemirror'), MOBILOUD_PLUGIN_VERSION );
				wp_enqueue_script( 'codemirror-htmlmixed' );
				wp_register_script( 'codemirror-javascript', MOBILOUD_PLUGIN_URL . '/libs/codemirror/mode/javascript/javascript.js', array('codemirror'), MOBILOUD_PLUGIN_VERSION );
				wp_enqueue_script( 'codemirror-javascript' );

				wp_enqueue_style( 'codemirror', MOBILOUD_PLUGIN_URL . '/libs/codemirror/lib/codemirror.css' );


				self::render_view( 'settings_editor', 'get_started' );
				break;
			case 'push':
				/**
				* Process Form
				*/
				if ( count( $_POST ) && check_admin_referer( 'ml-form-' . $tab ) ) {
					Mobiloud::set_option( 'ml_push_notification_enabled', isset( $_POST['ml_push_notification_enabled'] ) );
					Mobiloud::set_option( 'ml_pb_use_ssl', isset( $_POST['ml_pb_use_ssl'] ) );

					$include_post_types = '';
					if ( isset( $_POST['postypes'] ) && count( array_map( 'sanitize_text_field', $_POST['postypes'] ) ) ) {
						$include_post_types = implode( ',', array_map( 'sanitize_text_field', $_POST['postypes'] ) );
					}
					Mobiloud::set_option( 'ml_push_post_types', sanitize_text_field( $include_post_types ) );

					if ( isset( $_POST['ml_push_notification_categories_loaded'] ) ) {
						if ( isset( $_POST['ml_push_notification_categories'] ) ) {
							ml_push_notification_categories_clear();
							ml_push_notification_taxonomies_clear();
							if ( is_array( $_POST['ml_push_notification_categories'] ) ) {
								$tax_list = array();
								foreach ( array_map( 'sanitize_text_field', $_POST['ml_push_notification_categories'] ) as $categoryID ) {
									if ( 0 === strpos( $categoryID, 'tax:' ) ) {
										$tax_list[] = absint( str_replace( 'tax:', '', $categoryID ) );
									} else {
										ml_push_notification_categories_add( $categoryID );
									}
								}
								ml_push_notification_taxonomies_set( $tax_list );
							}
						} else {
							ml_push_notification_categories_clear();
							ml_push_notification_taxonomies_clear();
						}
					}

					Mobiloud::set_option( 'ml_pb_together', isset( $_POST['ml_pb_together'] ) );
					if ( isset( $_POST['ml_pb_chunk'] ) ) {
						Mobiloud::set_option( 'ml_pb_chunk', max( array( 100, absint( $_POST['ml_pb_chunk'] ) ) ) );
					}
					if ( isset( $_POST['ml_pb_rate'] ) ) {
						Mobiloud::set_option( 'ml_pb_rate', max( array( 1, absint( $_POST['ml_pb_rate'] ) ) ) );
					}

					Mobiloud::set_option( 'ml_pb_no_tags', isset( $_POST['ml_pb_no_tags'] ) );
					Mobiloud::set_option( 'ml_push_include_image', isset( $_POST['ml_push_include_image'] ) ? '1' : '0' );
					Mobiloud::set_option( 'ml_pb_log_enabled', isset( $_POST['ml_pb_log_enabled'] ) );

					// clear cached values
					if ( isset( $_POST['ml_pb_app_id'] ) && ( sanitize_text_field( $_POST['ml_pb_app_id'] ) !== Mobiloud::get_option( 'ml_pb_app_id' )
					|| sanitize_text_field( $_POST['ml_pb_app_id'] ) !== Mobiloud::get_option( 'ml_pb_app_id' ) ) ) {
						Mobiloud::set_option( 'ml_count_ios', 0 );
						Mobiloud::set_option( 'ml_count_android', 0 );
					}
					if ( isset( $_POST['ml_onesignal_app_id'] ) && ( sanitize_text_field( $_POST['ml_onesignal_app_id'] ) !== Mobiloud::get_option( 'ml_onesignal_app_id' )
					|| sanitize_text_field( $_POST['ml_onesignal_app_id'] ) !== Mobiloud::get_option( 'ml_onesignal_app_id' ) ) ) {
						Mobiloud::set_option( 'ml_count_total', 0 );
					}

					if ( isset( $_POST['ml_push_service'] ) ) {
						Mobiloud::set_option( 'ml_push_service', absint( $_POST['ml_push_service'] ) );
					}
					Mobiloud::set_option( 'ml_pb_app_id', sanitize_text_field( $_POST['ml_pb_app_id'] ) );
					if ( isset( $_POST['ml_pb_secret_key'] ) ) {
						Mobiloud::set_option( 'ml_pb_secret_key', sanitize_text_field( $_POST['ml_pb_secret_key'] ) );
					}
					Mobiloud::set_option( 'ml_onesignal_app_id', sanitize_text_field( $_POST['ml_onesignal_app_id'] ) );
					if ( isset( $_POST['ml_onesignal_secret_key'] ) ) {
						Mobiloud::set_option( 'ml_onesignal_secret_key', sanitize_text_field( $_POST['ml_onesignal_secret_key'] ) );
					}

					$migrate_allowed = ! empty( $_POST['ml_push_migrate_mode'] ) && Mobiloud::get_option( 'ml_pb_app_id' ) && Mobiloud::get_option( 'ml_pb_secret_key' )
					&& Mobiloud::get_option( 'ml_onesignal_app_id' ) && Mobiloud::get_option( 'ml_onesignal_secret_key' );
					Mobiloud::set_option( 'ml_push_migrate_mode', $migrate_allowed );
				}
				self::render_view( 'settings_push', 'get_started' );
				break;
		}
		if ( is_null( get_option( 'ml_license_tracked', null ) ) && strlen( Mobiloud::get_option( 'ml_pb_app_id' ) ) >= 0
		&& strlen( Mobiloud::get_option( 'ml_pb_secret_key' ) ) >= 0
		) {
			update_option( 'ml_license_tracked', true );
		}
	}

	public static function menu_push() {
		if ( ! current_user_can( Mobiloud::capability_for_use ) ) {
			return;
		}
		if ( count( $_POST ) ) {
			self::flush_cache();
		}

		if ( ! isset( $_GET['tab'] ) ) {
			$_GET['tab'] = '';
		}

		$tab = sanitize_text_field( $_GET['tab'] );
		switch ( $tab ) {
			default:
			case 'notifications':
				self::render_view( 'push_notifications', 'push' );
				break;
		}
	}


	public static function echo_if_set ( $check = null, $compare = null, $output = 'value' ) {
		switch( $output ) {
			case 'value':
				if ( isset( $check ) && ! empty( $check ) ) {
					echo esc_html( $check );
				}
				break;

			default: // 'selected'
				if ( isset( $check ) && ! empty( $check ) ) {

					if ( isset( $compare ) ) {
						if ( $compare === $check ) {
							echo esc_html( $output );
						}
					} else {
						echo esc_html( $output );
					}
				}
				break;
		}
	}

	/**
	* Get list of tasks for "Get Started" page
	*
	* @return array
	*/
	public static function get_started_tasks() {
		return self::$get_started_tasks;
	}

	/**
	* Get task CSS class (default, act ve, complete)
	*
	* @param string $task
	*/
	public static function get_task_class( $task ) {
		$class = '';
		if ( ! isset( $_GET['tab'] ) ) {
			$_GET['tab'] = '';
		}

		$tab = sanitize_text_field( $_GET['tab'] );
		if ( $task === $tab || ( ! isset( $_GET['tab'] ) && $task === 'design' ) ) {
			$class = 'current';
		}

		$class .= ' ' . self::get_task_status( $task );

		return $class;
	}

	public static function set_task_status( $task, $status ) {
		$task_statuses = Mobiloud::get_option( 'ml_get_start_tasks', false );
		if ( $task_statuses === false ) {
			$task_statuses = array(
				$task => $status,
			);
		} else {
			$task_statuses[ $task ] = $status;
		}
		Mobiloud::set_option( 'ml_get_start_tasks', $task_statuses );
	}

	public static function get_task_status( $task ) {
		$task_statuses = Mobiloud::get_option( 'ml_get_start_tasks', false );
		if ( $task_statuses !== false && isset( $task_statuses[ $task ] ) ) {
			return $task_statuses[ $task ];
		}

		return 'incomplete';
	}

	private static function isJson( $string ) {
		json_decode( $string );

		return strlen( $string ) > 0;
	}

	public static function ajax_welcome() {
		if ( Mobiloud::is_action_allowed_ajax( 'tab_welcome' , false ) ) {
			$email      = ! empty( $_POST['ml_email'] ) ? sanitize_email( $_POST['ml_email'] ) : '';
			$name       = ! empty( $_POST['ml_name'] ) ? sanitize_text_field( $_POST['ml_name'] ) : '';
			$site       = ! empty( $_POST['ml_site'] ) ? sanitize_text_field( $_POST['ml_site'] ) : '';
			$company    = ! empty( $_POST['ml_company'] ) ? sanitize_text_field( $_POST['ml_company'] ) : '';
			$phone      = ! empty( $_POST['ml_phone'] ) ? sanitize_text_field( $_POST['ml_phone'] ) : '';
			$message    = ! empty( $_POST['ml_message'] ) ? sanitize_text_field( $_POST['ml_message'] ) : '';
			$agree      = empty( $_POST['ml_agree'] ) ? 0 : 1;
			$pricing    = empty( $_POST['ml_pricing'] ) ? 0 : 1;
			$newsletter = empty( $_POST['ml_newsletter'] ) ? 0 : 1;
			$intercom   = empty( $_POST['ml_intercom'] ) ? 0 : 1;
			$type       = ! empty( $_POST['ml_apptype'] ) ? $_POST['ml_apptype'] : '';
			//$ip         = ! empty( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : '';

			Mobiloud::set_option( 'ml_initial_details_saved', true );
			Mobiloud::set_option( 'ml_user_name', sanitize_text_field( $name ) );
			Mobiloud::set_option( 'ml_user_email', sanitize_email( $email ) );
			Mobiloud::set_option( 'ml_user_site', sanitize_text_field( $site ) );
			Mobiloud::set_option( 'ml_user_company', sanitize_text_field( $company ) );
			Mobiloud::set_option( 'ml_user_apptype', sanitize_text_field( $type ) );
			Mobiloud::set_option( 'ml_user_phone', sanitize_text_field( $phone ) );
			Mobiloud::set_option( 'ml_user_message', sanitize_textarea_field( $message ) );
			Mobiloud::set_option( 'ml_user_newsletter', $newsletter );

			$url    = 'https://www.mobiloud.com/demo_plugin/';
			$params = array(
				'email'        => $email,
				'name'         => $name,
				'subid'        => $subid,
				'site'         => $site,
				'type'         => $type,
				'company_name' => $company,
				'phone'        => $phone,
				'questions'    => $message,
				'agree'        => $agree,
				'pricing'      => $pricing,
				'newsletter'   => $newsletter,
				//	'ip'           => $ip,
				'utm_source'   => 'news-plugin',
			);

			$result = wp_remote_post(
				$url,
				array(
					'body'      => $params,
					'timeout'   => 15,
					'sslverify' => false,
				)
			); // call endpoint

			// success?
			if ( ! is_wp_error( $result ) && ( '' != wp_remote_retrieve_body( $result ) ) ) {
				Mobiloud::set_option( 'ml_welcome', '1' );

				$answer = json_decode( wp_remote_retrieve_body( $result ), true );

				if ( is_array( $answer ) && ! empty( $answer['success'] ) && is_array( $answer['data'] ) ) {
					$timezone_check = true;
					if ( isset( $answer['data']['timezone'] ) && ! $answer['data']['timezone'] ) {
						$timezone_check = false;
					}
					Mobiloud::set_option( 'ml_welcome_timezone', $timezone_check );
					$next_step = admin_url( 'admin.php?page=mobiloud&step=' . self::$welcome_steps[1] . '&open=true' );

					wp_send_json_success(
						array(
							'url'            => $next_step,
							'timezone_check' => $timezone_check,
						)
					);
					die();
				}
			}
			wp_send_json_error();
			die();
		}
	}

	public static function save_editor() {
		if ( Mobiloud::is_action_allowed_ajax( 'save_editor' ) ) {
			if ( isset( self::$editor_sections[ $_POST['editor'] ] ) ) {
				Mobiloud::set_option( sanitize_text_field( $_POST['editor'] ), $_POST['value'] );
				self::flush_cache();
				echo '1';
			}
		}
		die();
	}

	public static function save_editor_embed() {
		if ( Mobiloud::is_action_allowed_ajax( 'save_editor_embed' ) ) {
			$items = isset( $_POST['items'] ) ? $_POST['items'] : array();
			if ( is_array( $items ) && count( $items ) ) {
				Mobiloud::set_option( 'ml_embedded_page_css', ! empty( $items['ml_embedded_page_css'] ) ? $items['ml_embedded_page_css'] : '' );
				Mobiloud::set_option( 'ml_embedded_header_hide', ! empty( $items['ml_embedded_header_hide'] ) );
				Mobiloud::set_option( 'ml_embedded_footer_hide', ! empty( $items['ml_embedded_footer_hide'] ) );
				Mobiloud::set_option( 'ml_embedded_android_name', ! empty( $items['ml_embedded_android_name'] ) ? $items['ml_embedded_android_name'] : '' );
				echo '1';
			}
		}
		die();
	}

	public static function save_banner() {
		if ( Mobiloud::is_action_allowed_ajax( 'save_banner' ) ) {
			if ( isset( self::$banner_positions[ $_POST['position'] ] ) ) {
				Mobiloud::set_option( sanitize_text_field( $_POST['position'] ), ! empty( $_POST['value'] ) ? $_POST['value'] : '' ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				Mobiloud::set_option( sanitize_text_field( $_POST['position'] ) . '_app_subscription_show', empty( $_POST['app_sub_show'] ) ? 0 : 1 );
				echo '1';
			}
		}
		die();
	}

	public static function get_tax_list() {
		$list = array();
		if ( Mobiloud::is_action_allowed_ajax( 'tab_menu_config' ) ) {
			if ( isset( $_POST['group'] ) ) {
				$group = sanitize_text_field( $_POST['group'] );
				$terms = get_terms( $group, array( 'hide_empty' => false ) );
				if ( count( $terms ) ) {

					foreach ( $terms as $term ) {
						$parent_name = '';
						if ( $term->parent ) {
							$parent_term = get_term_by( 'id', $term->parent, $group );
							if ( $parent_term ) {
								$parent_name = $parent_term->name . ' - ';
							}
						}
						$list[ $term->term_id ] = array(
							'id'       => $term->term_id,
							'fullname' => $parent_name . $term->name,
							'title'    => $term->name,
						);
					}
				}
			}
		}
		header( 'Content-Type: application/json' );
		wp_send_json( array( 'terms' => $list ) );
	}

	public static function get_tax_terms() {
		if ( Mobiloud::is_action_allowed_ajax( 'tab_menu_config' ) ) {
			if ( isset( $_POST['tax'] ) ) {
				$terms = get_terms( array(
					'taxonomy' => sanitize_text_field( $_POST['tax'] ),
					'hide_empty' => true,
					'hierarchical' => false,
				) );
				foreach ( $terms as $term ) {
					echo "<option value='" . esc_attr( $term->term_id ) . "'>" . esc_html( $term->name ) . "</option>";
				}
			}
			die();
		}
	}

	public static function get_pb_log_name( $web_path = false ) {
		$filename = Mobiloud::get_option( 'ml_pb_log_name' );
		if ( empty( $filename ) ) {
			$site     = str_replace( array( 'https://', 'http://', '/', ':' ), array( '', '', '_', '' ), get_site_url() );
			$filename = $site . '-mlpush' . wp_rand( 10000000, 99999999 ) . '.txt';
			Mobiloud::set_option( 'ml_pb_log_name', $filename );
		}
		$paths         = wp_upload_dir();
		$basedir       = 'basedir';
		$baseurl       = 'baseurl';
		$not_writeable = '';
		if ( ! self::writeable( $paths[ $basedir ] . '/' . $filename ) ) {
			$basedir = 'path';
			$baseurl = 'url';
			if ( ! self::writeable( $paths[ $basedir ] . '/' . $filename ) ) {
				$not_writeable = '(not-writeable)';
			}
		}
		if ( $web_path ) {
			return $not_writeable . $paths[ $baseurl ] . '/' . $filename;
		} else {
			return $not_writeable . $paths[ $basedir ] . '/' . $filename;
		}
	}

	private static function writeable( $log_file_name ) {
		if ( file_exists( $log_file_name ) && is_writable( $log_file_name ) ) {
			return true;
		} elseif ( file_exists( $log_file_name ) && ! is_writable( $log_file_name ) ) {
			return false;
		} else {
			$result = ( false !== file_put_contents( $log_file_name, date( 'Y-m-d H:i:s' ) . "\tFile created\n" ) );
			if ( $result ) {
				chmod( $log_file_name, 0666 );
				clearstatcache();
			}
			return $result;
		}
	}

	/**
	* Add "Breaking news notification" metabox
	*
	* @param string  $post_type
	* @param WP_POST $post
	*/
	public static function add_push_metabox( $post_type, $post ) {
		// show only for selected post types
		$post_types = explode( ',', Mobiloud::get_option( 'ml_push_post_types', 'post' ) );
		if ( in_array( $post_type, $post_types ) ) {
			foreach ( $post_types as $post1 ) {
				add_meta_box(
					'ml-push-matabox',
					'Breaking news notification',
					array( 'Mobiloud_Admin', 'render_ml_push_metabox' ),
					$post1,
					'advanced',
					( 'publish' !== $post->post_status ? 'high' : 'low' )
				);
			}
		}
	}

	/**
	* Show an option at metabox
	*
	* @param WP_POST $post
	*/
	public static function render_ml_push_metabox( $post ) {
		$value            = get_post_meta( $post->ID, 'ml_notification_notags', true );
		$published        = 'publish' === $post->post_status; // show option disabled when post published
		$global_value     = Mobiloud::get_option( 'ml_pb_no_tags', false );
		$globally_enabled = ! empty( $global_value ); // show option checked and disabled when global "send notifications without tags" checked
		?><input type="checkbox" name="ml_notification_notags<?php echo ( $published || $globally_enabled ) ? '_show' : ''; ?>" id="ml_notification_notags" value="1"
			<?php echo ( ! empty( $value ) || $globally_enabled ) ? ' checked="checked"' : ''; ?><?php echo ( $published || $globally_enabled ) ? ' disabled="disabled"' : ''; ?>>
		<label for="ml_notification_notags">Send a notification to all app users when this post is published.</label>
		<?php
		if ( $published || $globally_enabled ) {
		?>
			<input type="hidden" name="ml_notification_notags" value="<?php echo ! empty( $value ) ? 1 : 0; ?>">
		<?php
		}
		?>
		<p><em>When this option is checked, when a notification for this post is sent automatically, it will be delivered to all devices,
			irrespective of user's choices for notifications, resulting in a faster delivery.</em></p>
		<?php

	}

	public static function save_push_metabox( $post_id ) {
		$value = empty( $_POST['ml_notification_notags'] ) ? 0 : 1;
		update_post_meta( $post_id, 'ml_notification_notags', $value );
	}

	/**
	* Current push keys values are empty
	*/
	public static function no_push_keys() {
		return ( strlen( Mobiloud::get_option( 'ml_pb_app_id' ) ) <= 0 && strlen( Mobiloud::get_option( 'ml_pb_secret_key' ) ) <= 0
			&& strlen( Mobiloud::get_option( 'ml_onesignal_app_id' ) ) <= 0 && strlen( Mobiloud::get_option( 'ml_onesignal_secret_key' ) ) <= 0 );
	}

	/**
	* Put a spinner image and load content using ajax call.
	* require code: wp_nonce_field( 'load_ajax', 'ml_nonce_load_ajax' ); at the current page
	*
	* @param string $what what to load
	* @see function load_ajax()
	*/
	public static function load_ajax_insert( $what ) {
		?>
		<div class="ml_load_ajax" data-ml_what="<?php echo esc_attr( $what ); ?>"><img class="ml-spinner" src="<?php echo esc_attr( MOBILOUD_PLUGIN_URL . 'assets/img/spinner.gif' ); ?>"></div>
		<?php
	}

	/**
	* Return content loaded by ajax request.
	*
	* @see function load_ajax_insert()
	*/
	public static function load_ajax() {
		if ( Mobiloud::is_action_allowed_ajax( 'load_ajax' ) ) {
			$what = isset( $_POST['what'] ) ? sanitize_text_field( $_POST['what'] ) : '';

			ob_start();
			$chosen  = false;
			$ul_name = false;
			$ul      = false;
			$show    = false;
			if ( 'push_cat_tax' === $what ) {
				self::ajax_select_categories_taxonomies( 'ml_push_notification_categories', ml_get_push_notification_categories(), ml_get_push_notification_taxonomies() );
				$chosen = 'ml_push_notification_categories';
			} elseif ( 'settings_cat' === $what ) {
				self::ajax_settings_cat();
			} elseif ( 'settings_tax' === $what ) {
				self::ajax_settings_tax();
				$chosen = 'ml_main_screen_tax_list';
			} elseif ( 'settings_sticky_cat_1' === $what ) {
				self::ajax_settings_sticky_cat_1();
			} elseif ( 'settings_sticky_cat_2' === $what ) {
				self::ajax_settings_sticky_cat_2();
			} elseif ( 'menu_cat' === $what ) {
				self::ajax_menu_cat();
				$ul_name = '.ml-menu-categories-holder';
				$ul      = self::ajax_menu_cat_ul_return();
				$show    = '.ml-add-category-btn';
			} elseif ( 'menu_tags' === $what ) {
				self::ajax_menu_tags();
				$ul_name = '.ml-menu-tags-holder';
				$ul      = self::ajax_menu_tags_ul_return();
				$show    = '.ml-add-tag-btn';
			} elseif ( 'menu_page' === $what ) {
				self::ajax_menu_page();
				$ul_name = '.ml-menu-pages-holder';
				$ul      = self::ajax_menu_page_ul_return();
				$show    = '.ml-add-page-btn';
			}
			$result = ob_get_clean();

			header( 'Content-type: application/json' );
			header( 'Cache-Control: private, no-cache', true );
			echo wp_json_encode(
				array(
					'data'    => $result, // content of main block, required
					'chosen'  => $chosen, // id of chosen to init
					'ul_name' => $ul_name, // selector of ul block
					'ul'      => $ul, // content of ul block
					'show'    => $show, // selector of button to show
				)
			);
			die();
		}
	}

	private static function ajax_select_categories_taxonomies( $name, $selected_categories, $selected_tax ) {
		?>
		<input type=hidden name="<?php echo esc_attr( $name . '_loaded' ); ?>" value="1">
		<select id="<?php echo esc_attr( $name ); ?>" name='<?php echo esc_attr( $name ); ?>[]'
			data-placeholder="Select Categories..." style="width:100%;max-width:600px;" multiple class="chosen-select">
			<option></option>
			<?php
			$categories = get_categories( array( 'hide_empty' => 0 ) );
			foreach ( $categories as $c ) {
				$selected = false;
				if ( is_array( $selected_categories ) && count( $selected_categories ) > 0 ) {
					foreach ( $selected_categories as $pushCategory ) {
						if ( $pushCategory->cat_ID == $c->cat_ID ) {
							$selected = true;
						}
					}
				}
				echo '<option value="' . esc_attr( $c->cat_ID ) . '" ' . selected( $selected ) . '>Category: ' . esc_html( $c->cat_name ) . '</option>';
			}
			$taxonomies = get_taxonomies( array( '_builtin' => false ), 'objects' );

			foreach ( $taxonomies as $tax ) {
				$terms = get_terms( $tax->query_var, array( 'hide_empty' => false ) );
				if ( count( $terms ) ) {
					foreach ( $terms as $term ) {
						$parent_name = '';
						if ( $term->parent ) {
							$parent_term = get_term_by( 'id', $term->parent, $tax->query_var );
							if ( $parent_term ) {
								$parent_name = $parent_term->name . ' - ';
							}
						}
						$selected = in_array( $term->term_id, $selected_tax );
						echo '<option value="tax:' . esc_attr( $term->term_id ) . '" ' . selected( $selected ) . '>' . esc_html( "{$tax->label}: {$parent_name}{$term->name}" ) . '</option>';
					}
				}
			}
			?>
		</select>
		<?php
	}

	private static function ajax_settings_cat() {
		$categories = get_categories( 'orderby=name&hide_empty=0' );
		$wp_cats    = array();

		$excludedCategories = explode( ',', get_option( 'ml_article_list_exclude_categories', '' ) );

		foreach ( $categories as $category_list ) {
			$wp_cats[ $category_list->cat_ID ] = $category_list->cat_name;
		}
		?>
		<input type=hidden name="categories_loaded" value="1">
		<?php
		foreach ( $wp_cats as $v ) {
			$checked = ! in_array( $v, $excludedCategories );
		?>
			<div class="ml-columns ml-form-row ml-checkbox-wrap no-margin">
				<input type="checkbox" id='categories_<?php echo esc_attr( $v ); ?>' name="categories[]"
					value="<?php echo esc_attr( $v ); ?>" <?php echo checked( $checked ); ?>/>
				<label for="categories_<?php echo esc_attr( $v ); ?>"><?php echo esc_html( $v ); ?></label>
			</div>
		<?php
		}
	}

	private static function ajax_settings_tax() {
		?>
		<input type=hidden name="ml_main_screen_tax_list_loaded" value="1">
		<select id="ml_main_screen_tax_list" name='ml_main_screen_tax_list[]'
			data-placeholder="Select Taxonomies..." style="width:350px;" multiple class="chosen-select">
			<option></option>
			<?php
			$tax_list   = get_option( 'ml_main_screen_tax_list', array() ); // current tax list
			$taxonomies = get_taxonomies( array( '_builtin' => false ), 'objects' );

			foreach ( $taxonomies as $tax ) {
				$terms = get_terms( $tax->query_var, array( 'hide_empty' => false ) );
				if ( ! is_wp_error( $terms ) && count( $terms ) ) {
					foreach ( $terms as $term ) {
						$parent_name = '';
						if ( $term->parent ) {
							$parent_term = get_term_by( 'id', $term->parent, $tax->query_var );
							if ( $parent_term ) {
								$parent_name = $parent_term->name . ' - ';
							}
						}
						$selected = in_array( $tax->query_var . ':' . $term->term_id, $tax_list );
						echo "<option value='" . esc_attr( "{$tax->query_var}:{$term->term_id}" ) . "' " . selected( $selected ) .">" . esc_html( "{$tax->label}: {$parent_name}{$term->name}" ) . "</option>";
					}
				}
			}
			?>
		</select>
		<?php
	}

	private static function ajax_settings_sticky_cat_1() {
		?>
		<input type=hidden name="sticky_category_1_loaded" value="1">
		<select name="sticky_category_1">
			<option value="">Select a category</option>
			<?php
			$categories = get_categories( array( 'hide_empty' => 0 ) );
			foreach ( $categories as $c ) {
				$selected = Mobiloud::get_option( 'sticky_category_1' ) == $c->cat_ID;
				echo '<option value="' . esc_attr( $c->cat_ID ) . '" ' . selected( $selected ). '>' . esc_html( $c->cat_name ) . '</option>';
			}
			?>
		</select>
		<?php
	}

	private static function ajax_settings_sticky_cat_2() {
		?>
		<input type=hidden name="sticky_category_2_loaded" value="1">
		<select name="sticky_category_2">
			<option value="">Select a category</option>
			<?php $categories = get_categories( array( 'hide_empty' => 0 ) ); ?>
			<?php
			foreach ( $categories as $c ) {
				$selected = Mobiloud::get_option( 'sticky_category_2' ) == $c->cat_ID;
				echo '<option value="' . esc_attr( $c->cat_ID ) . '" ' . selected( $selected ) . '>' . esc_html( $c->cat_name ) . '</option>';
			}
			?>
		</select>
		<?php
	}

	private static function ajax_menu_cat() {
		?>
		<input type=hidden name="ml-menu-categories_loaded" value="1">
		<select name="ml-category" class="ml-select-add">
			<option value="">Select a category</option>
			<?php $categories = get_categories(); ?>
			<?php
			foreach ( $categories as $c ) {
				$parent_cat_name = '';
				if ( $c->parent ) {
					$parent_category = get_the_category_by_ID( $c->parent );
					if ( $parent_category ) {
						$parent_cat_name = $parent_category . ' - ';
					}
				}
				echo '<option value=' . esc_attr( $c->cat_ID ) . ' title="' . esc_attr( $c->cat_name ) . '">' . esc_html( $parent_cat_name . $c->cat_name ) . '</option>';
			}
			?>
		</select>
		<?php
	}

	private static function ajax_menu_cat_ul_return() {
		ob_start();
		$ml_categories = ml_categories();
		$ml_prev_cat   = 0;
		foreach ( $ml_categories as $cat ) {
		?>
			<li rel="<?php echo esc_attr( $cat->cat_ID ); ?>">
				<span class="dashicons-before dashicons-menu"></span><?php echo esc_html( $cat->name ); ?>
				<input type="hidden" name="ml-menu-categories[]" value="<?php echo esc_attr( $cat->cat_ID ); ?>"/>
				<a href="#" class="dashicons-before dashicons-trash ml-item-remove"></a>
			</li>
		<?php
		}
		return ob_get_clean();
	}

	private static function ajax_menu_page() {
		?>
		<input type=hidden name="ml-menu-pages_loaded" value="1">
		<select name="ml-page" class="ml-select-add">
			<option value="">Select a page</option>
			<?php $pages = get_pages(); ?>
			<?php
			foreach ( $pages as $p ) {
				echo '<option value="' . esc_attr( $p->ID ) . '">' . esc_html( $p->post_title ) . '</option>';
			}
			?>
		</select>
		<?php
	}

	private static function ajax_menu_page_ul_return() {
		ob_start();
		$ml_pages = ml_pages();
		foreach ( $ml_pages as $page ) {
		?>
			<li rel="<?php echo esc_attr( $page->ID ); ?>">
				<span class="dashicons-before dashicons-menu"></span><?php echo esc_html( $page->post_title ); ?>
				<input type="hidden" name="ml-menu-pages[]" value="<?php echo esc_attr( $page->ID ); ?>"/>
				<a href="#" class="dashicons-before dashicons-trash ml-item-remove"></a>
			</li>
		<?php
		}
		return ob_get_clean();
	}

	private static function ajax_menu_tags() {
		?>
		<input type=hidden name="ml-menu-tags_loaded" value="1">
		<select name="ml-tags" class="ml-select-add">
			<option value="">Select Tag</option>
			<?php $tags = get_terms( 'post_tag' ); ?>
			<?php
			foreach ( $tags as $tag ) {
				echo '<option value="' . esc_attr( $tag->term_id ) . '">' . esc_html( $tag->name ) . '</option>';
			}
			?>
		</select>
		<?php
	}

	private static function ajax_menu_tags_ul_return() {
		ob_start();
		$menu_tags = Mobiloud::get_option( 'ml_menu_tags', array() );
		foreach ( $menu_tags as $menu_tag ) {
			$menu_tag_object = get_term_by( 'id', $menu_tag, 'post_tag' );
		?>
			<li rel="<?php echo esc_attr( $menu_tag_object->term_id ); ?>">
				<span class="dashicons-before dashicons-menu"></span><?php echo esc_html( $menu_tag_object->name ); ?>
				<input type="hidden" name="ml-menu-tags[]"
					value="<?php echo esc_attr( $menu_tag_object->term_id ); ?>"/>
				<a href="#" class="dashicons-before dashicons-trash ml-item-remove"></a>
			</li>
			<?php
		}
		return ob_get_clean();
	}

	public static function add_schedule_demo() {
		if ( self::no_push_keys() && ! self::welcome_screen_is_now() && self::welcome_screen_is_avalaible() ) {
			$url = admin_url( 'admin.php?page=mobiloud&tab=welcome' );
			?>
			<!--			<div class="notice is-dismissible ml-schedule-demo-block0">-->
			<!--				<div class="clear"></div>-->
			<!--				<div id="ml_img_div0"><img src="--><?php //echo MOBILOUD_PLUGIN_URL . 'assets/img/icon-squared-100x100.png'; ?><!--"></div>-->
			<!--				<div id="ml_text_div0">-->
			<!--					<p>Talk to an app expert</p>-->
			<!--					<p>Schedule a demo call and learn exactly how MobiLoud can help you turn your website into native mobile apps.</p>-->
			<!--				</div>-->
			<!--				<div id="ml_btn_div0">-->
			<!--					<a href="--><?php //echo esc_attr( $url ); ?><!--" class="button button-primary ml-schedule-demo-btn">Schedule a Demo</a>-->
			<!--				</div>-->
			<!--				<div class="clear"></div>-->
			<!--			</div>-->
			<!--			<style type="text/css">-->
			<!--				.ml-schedule-demo-block0 {-->
			<!--					min-height: 100px;-->
			<!--					padding-left: 0px;-->
			<!--					padding-top: 0px;-->
			<!--					padding-bottom: 0px;-->
			<!--					border-left: 0px;-->
			<!--					display: table;-->
			<!--				}-->
			<!--				#ml_img_div0 {-->
			<!--					width: 100px;-->
			<!--					margin: 0px 20px 0px 0px;-->
			<!--					display: table-cell;-->
			<!--					vertical-align: middle;-->
			<!--				}-->
			<!--				#ml_img_div0 img{-->
			<!--					display: block;-->
			<!--					width: 100px;-->
			<!--					height: 100px;-->
			<!--					margin: 0px;-->
			<!--					padding: 0px;-->
			<!--					border-image-width: 0px;-->
			<!--				}-->
			<!--				#ml_text_div0 {-->
			<!--					display: table-cell;-->
			<!--					vertical-align: middle;-->
			<!--					padding: 0px 20px;-->
			<!--				}-->
			<!--				#ml_text_div0 > p {-->
			<!--					margin: 0px;-->
			<!--				}-->
			<!--				#ml_text_div0 > p:first-child {-->
			<!--					font-size: large;-->
			<!--				}-->
			<!--				#ml_btn_div0 {-->
			<!--					margin: 20px 0px 20px 20px;-->
			<!--					min-height: 60px;-->
			<!--					display: table-cell;-->
			<!--					vertical-align: middle;-->
			<!--				}-->
			<!--				#ml_btn_div0 .ml-schedule-demo-btn {-->
			<!--					background-color: #55b63b;-->
			<!--					box-shadow: 0 -3px 0 0 #489b32 inset;-->
			<!--					box-sizing: border-box;-->
			<!--					border: none;-->
			<!--					text-shadow: 0 -1px 1px #489b32, 1px 0 1px #489b32, 0 1px 1px #489b32, -1px 0 1px #489b32;-->
			<!--				}-->
			<!--				@media screen and (min-width: 375px) {-->
			<!--					#ml_btn_div0 .ml-schedule-demo-btn {-->
			<!--					font-size: 18px;-->
			<!--					height: 50px;-->
			<!--					line-height: 48px;-->
			<!--					padding: 0px 20px;-->
			<!--				}-->
			<!--				}-->
			<!--				@media screen and (max-width: 425px) {-->
			<!--					#ml_img_div0 {-->
			<!--					display: none;-->
			<!--				}-->
			<!--				.ml-schedule-demo-block0, #ml_text_div0, #ml_btn_div0 {-->
			<!--					display: block;-->
			<!--				}-->
			<!--				#ml_btn_div0 {-->
			<!--					text-align: center;-->
			<!--				}-->
			<!--				#ml_text_div0 {-->
			<!--					padding-top: 20px;-->
			<!--				}-->
			<!--				}-->
			<!--				@media screen and (min-width: 1024px) {-->
			<!--					#ml_text_div0 {-->
			<!--					width: 90%;-->
			<!--				}-->
			<!--				}-->
			<!--			</style>-->
			<!--			<script type="text/javascript">-->
			<!--				(function($){-->
			<!--					$(document).on('ready', function(){-->
			<!--						$('.ml-schedule-demo-block0 .notice-dismiss').on('click', function(){-->
			<!--							$.post(ajaxurl, { 'action':'ml_schedule_dismiss', 't':Math.random()});-->
			<!--						})-->
			<!--					})-->
			<!--				})(jQuery)-->
			<!--			</script>-->
			<?php
			Mobiloud::set_option( 'ml_schedule_dismiss', time() );
			Mobiloud_Admin::welcome_screen_set_not_avalaible();
			Mobiloud_Admin::welcome_screen_set( false );
			wp_redirect( $url );
			exit;
		}
	}

	public static function schedule_dismiss() {
		if ( current_user_can( Mobiloud::capability_for_use ) ) {
			Mobiloud::set_option( 'ml_schedule_dismiss', time() );
			echo 'OK';
		}
		die();
	}

	public static function ajax_cache_flush() {
		if ( Mobiloud::is_action_allowed_ajax( 'tab_settings' ) ) {
			include_once MOBILOUD_PLUGIN_DIR . 'api/controllers/MLCacheController.php';
			$ml_cache = new MLCacheController();
			$ml_cache->flush_cache( 'ml_json' );
			$ml_cache->flush_cache( 'ml_post' );
			echo 'OK';
			die();
		} else {
			echo 'no-admin';
			die();
		}
	}
}

require_once( MOBILOUD_PLUGIN_DIR . 'class.mobiloud-admin-menu-walker.php' );
