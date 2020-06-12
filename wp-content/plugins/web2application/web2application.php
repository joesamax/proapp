<?php
/**
* Plugin Name: web2application
* Plugin URI:  https://wordpress.org/plugins/web2application/
* Description: Transform you website into nativ android and ios apps
* Version: 3.1
* Author: Tzin Nir
* Author URI:  http://web2application.com
* License:     GPL2
* Text Domain: web2application
**/


// Exit if Accessed Directly
if(!defined('ABSPATH')){
	exit;
}

ini_set('display_errors','Off');
ini_set('error_reporting', E_ERROR);
define('WP_DEBUG', false);
define('WP_DEBUG_DISPLAY', false);

define('W2A_VERSION', '1.0');
define('W2A_TEXTDOMAIN', 'w2a_domain');



// Global Options Variable
$w2a_options = get_option('w2a_settings');

// Load Scripts
require_once(plugin_dir_path(__FILE__).'includes/web2application-scripts.php');


// Load Content
require_once(plugin_dir_path(__FILE__).'/includes/web2application-content.php');



add_action('admin_menu','web2app_addMenu');

function web2app_addMenu() {
		// the if is for add it only to the admin area
	if(is_admin()){

		// page title , menu title, capebilty (who can reach), menu slug Url,the fuction that contain the page, position
		//add_menu_page('Web2apllication','Web2Application',4,'Web2apllication-main','web2applicationMainTab');
		add_menu_page('Web2apllication','Web2apllication',4,'Web2apllication-main','w2a_general_settings');

		add_submenu_page('Web2apllication-main','Send Push','Send Push',4,'send-push','w2a_sendPushPage');

		add_submenu_page('Web2apllication-main','Tab Menu Links','Tab Menu Links',4,'web2application-tab-menus','w2a_tab_menus');

		add_submenu_page('Web2apllication-main','Marketing Tools','Marketing Tools',4,'web2application-marketing-tools','w2a_marketing_tools');

		//add_submenu_page('Web2apllication-main','General Setting','General Setting',4,'web2application-settings','w2a_general_settings');

		add_submenu_page('Web2apllication-main','Premium Settings','Premium Settings',4,'web2application-premium-settings','w2a_premium_settings');

		add_submenu_page('Web2apllication-main','Web Push Settings','Web Push Settings',4,'web2application-web-push-settings','w2a_web_push_settings');

		add_submenu_page('Web2apllication-main','Web2application site','Web2application site',4,'Web2apllication-main-2','web2applicationMainTab');
		
		// add_submenu_page('Web2apllication-main','WooCommerce Setting','WooCommerce Setting',4,'web2application-woocommerce-settings','web2_woocommerce_settings');

		// must register to DB in this section when the plugin start
		add_action('admin_init', 'w2a_register_settings');

		add_action('admin_notices', 'w2a_notify_error_notice' );

		// load functions
		update_web2app_id();
		ios_universal_link_load();
		web_push_files_load();
	}
}

function web2applicationMainTab() {
	//require_once(wp_nonce_url(plugin_dir_path(__FILE__).'/includes/web2application-main-screen.php','w2a_nonces'));
	require_once(plugin_dir_path(__FILE__).'/includes/web2application-main-screen.php');
}

function w2a_sendPushPage() {
	require_once(plugin_dir_path(__FILE__).'/includes/web2application-sendpush-screen.php');
}

function w2a_tab_menus() {
	   require_once(plugin_dir_path(__FILE__).'/includes/web2application-tab-menus.php');
}

function w2a_marketing_tools() {
	   require_once(plugin_dir_path(__FILE__).'/includes/web2application-marketing-tools.php');
}

function w2a_general_settings() {
	   require_once(plugin_dir_path(__FILE__).'/includes/web2application-settings.php');
}

function w2a_premium_settings() {
	   require_once(plugin_dir_path(__FILE__).'/includes/web2application-premium-settings.php');
}

function w2a_web_push_settings() {
	   require_once(plugin_dir_path(__FILE__).'/includes/web2application-web-push-settings.php');
}

function web2_woocommerce_settings() {
	   require_once(plugin_dir_path(__FILE__).'/includes/web2application-woocommerce-settings.php');
}

function w2a_register_settings(){
	//register to the with list
	register_setting('w2a_settings_group', 'w2a_settings');

}

function w2a_notify_error_notice() {
	// check if has api_key
	check_api_key_existence();

	// check if api_key is valid
	validate_api_key();

	// check required version of woocommerce
	$woocommerce_version_required = '3.7.0';
	if ( class_exists( 'Woocommerce' ) && !version_compare(WOOCOMMERCE_VERSION, $woocommerce_version_required, '>=') ) {
	    add_action('admin_notices', 'woocommercer_fail_load_out_of_date');
        return;
	}
}

function woocommerce_fail_load_out_of_date() {
    if (!current_user_can('update_plugins')) {
        return;
    }

    $file_path = 'woocommerce/woocommerce.php';

    $upgrade_link = wp_nonce_url(self_admin_url('update.php?action=upgrade-plugin&plugin=') . $file_path, 'upgrade-plugin_' . $file_path);
    $message = '<p>' . __('Web2Application is not working because you are using an old version of WooCommercer.', W2A_TEXTDOMAIN) . '</p>';
    $message .= '<p>' . sprintf('<a href="%s" class="button-primary">%s</a>', $upgrade_link, __('Update WooCommerce Now', W2A_TEXTDOMAIN)) . '</p>';

    echo '<div class="error">' . $message . '</div>';
}

/**
 * Load In-App Visibility
 *
 * Load the plugin after Elementor (and other plugins) are loaded.
 *
 * @since 1.0.0
 */
function inapp_visibility_for_elementor_load() {
	global $w2a_options;

    // Load localization file
    load_plugin_textdomain(W2A_TEXTDOMAIN);

	// Check if elementor feature is enabled
	if ($w2a_options['w2a_disable_elementor'] != "1") {
		// Check if elementor is installed and active
		if (!isElementorActive()) {
			if ( !current_user_can( 'activate_plugins' ) ) {
				return;
			}

			// Notice if the Elementor is not active
			if (!did_action('elementor/loaded')) {
				add_action('admin_notices', 'inapp_visibility_for_elementor_fail_load');
				return;
			}

			// Check required version
		   $elementor_version_required = '1.8.0';
		   if (!version_compare(ELEMENTOR_VERSION, $elementor_version_required, '>=')) {
			   add_action('admin_notices', 'inapp_visibility_for_elementor_fail_load_out_of_date');
			   return;
		   }
		}

		// Require the main plugin file
		require( __DIR__ . '/plugin.php' );
	}
}
add_action('plugins_loaded', 'inapp_visibility_for_elementor_load');

// function to display if elementor is out of date
function inapp_visibility_for_elementor_fail_load_out_of_date() {
    if (!current_user_can('update_plugins')) {
        return;
    }

    $file_path = 'elementor/elementor.php';

    $upgrade_link = wp_nonce_url(self_admin_url('update.php?action=upgrade-plugin&plugin=') . $file_path, 'upgrade-plugin_' . $file_path);
    $message = '<p>' . __('Web2Application is not working because you are using an old version of Elementor.', W2A_TEXTDOMAIN) . '</p>';
    $message .= '<p>' . sprintf('<a href="%s" class="button-primary">%s</a>', $upgrade_link, __('Update Elementor Now', W2A_TEXTDOMAIN)) . '</p>';

    echo '<div class="error">' . $message . '</div>';
}

// function to  display if elementor fail to load
function inapp_visibility_for_elementor_fail_load() {
  if ( ! current_user_can( 'activate_plugins' ) ) {
	  return;
  }

	$plugin = 'elementor/elementor.php';

	$activation_url = wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . $plugin . '&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_' . $plugin );
	$message = '<p>' . __( 'In order to use Web2Application features in elementor, please activate the Elementor plugin.', W2A_TEXTDOMAIN ) . '</p>';
	$message .= '<p>' . sprintf( '<a href="%s" class="button-primary">%s</a>', $activation_url, __( 'Activate Elementor Now', W2A_TEXTDOMAIN ) ) . '</p>';

	echo '<div class="error"><p>' . $message . '</p></div>';
}

// function to check if elementor is installed and active
function isElementorActive() {
    if(in_array('elementor/elementor.php', apply_filters('active_plugins', get_option('active_plugins')))){
        return true;
    }
    return false;
}

/**
 * Check API Key if exists
 */
function check_api_key_existence() {
	global $w2a_options;

	if (isset($w2a_options['w2a_api_key'])) {
		if (trim($w2a_options['w2a_api_key']) == "" ) {
			?>
			<div class="error">
				<p><?php _e( 'Web2Application plugin not set. Please go to Web2application -> Setting and fix your API key', W2A_TEXTDOMAIN ); ?></p>
			</div>
			<?php
		}
	}
}


/**
 * GET Web2App id
 */
function update_web2app_id() {
	global $w2a_options;

	// get appId
	$url = 'https://www.web2application.com/w2a/api-process/get_app_id.php?api_domain='.$_SERVER['SERVER_NAME'].'&api_key='.trim($w2a_options['w2a_api_key']);
	$newAppId = file_get_contents($url);

	// check
	if ($newAppId != 'Wrong API. Please Check Your API Key' && is_numeric($newAppId)) {
		// create file to save the $appId
		$path = $_SERVER['DOCUMENT_ROOT']."/web2app-id";

		// create a file
		$file = fopen($path, "w");
		fwrite($file, $newAppId);
		fclose($file);

		return $newAppId;
	} else {
		return 0;
	}
}


/**
 * Load Web2App id
 */
function get_web2app_id() {
	$path = $_SERVER['DOCUMENT_ROOT']."/web2app-id";
	$appId = 0;

	$handle = @fopen($path, 'r');

	if($handle){
		// get contents
		$content = file_get_contents($web2app1);

		// check
		if (is_numeric($content)) {
			$appId = $content;
		} else {
			return update_web2app_id();
		}

	} else {
		return update_web2app_id();
	}

	return $appId;
}


/**
 * Validate API Key if valid
 */
function validate_api_key() {
	global $w2a_options;

	// get appId
    $url = 'https://www.web2application.com/w2a/api-process/get_app_id.php?api_domain='.$_SERVER['SERVER_NAME'].'&api_key='.trim($w2a_options['w2a_api_key']);
    $appId = file_get_contents($url);

	// check
	if ($appId == 'Wrong API. Please Check Your API Key') { ?>
		<div class="error">
			<p><?php _e( 'Your API Key is invalid! Please go to web2application -> setting and fix your API key', W2A_TEXTDOMAIN ); ?></p>
		</div>
<?php
	} else {
		// create file to save the $appId
		$path = $_SERVER['DOCUMENT_ROOT']."/web2app-id";

		// create a file
		$file = fopen($path, "w");
		fwrite($file, $appId);
		fclose($file);
	}
}


/**
 * Load iOS Universal Link
 */
function ios_universal_link_load() {
	global $w2a_options;

	// get appId
    /*$url = 'https://www.web2application.com/w2a/api-process/get_app_id.php?api_domain='.$_SERVER['SERVER_NAME'].'&api_key='.trim($w2a_options['w2a_api_key']);
    $appId = file_get_contents($url);*/
	$appId = get_web2app_id();

	// check
	if ($appId != 'Wrong API. Please Check Your API Key' && is_numeric($appId)) {
		// get aasa file content
		$aasaUrl = 'https://www.web2application.com/w2a/webapps/'.$appId.'/apple-app-site-association';
		// check if file exist
		$handle = @fopen($aasaUrl, 'r');


		if($handle) {
			// get contents
			$json = file_get_contents($aasaUrl);

			// define folder
			$dest 		= $_SERVER['DOCUMENT_ROOT']."/";
			$filename 	= "apple-app-site-association";

			// create a file
			$aasa = fopen($dest.$filename, "w");
			fwrite($aasa, $json);
			fclose($aasa);
		}
	}
}


/**
 * Load Web Push Files
 */
function web_push_files_load() {
	global $w2a_options;

	// get appId
    /*$url = 'https://www.web2application.com/w2a/api-process/get_app_id.php?api_domain='.$_SERVER['SERVER_NAME'].'&api_key='.trim($w2a_options['w2a_api_key']);
    $appId = file_get_contents($url);*/
	$appId = get_web2app_id();

	// check
	if ($appId != 'Wrong API. Please Check Your API Key' && is_numeric($appId)) {

		// check if exist
		$web2app1 = 'https://www.web2application.com/w2a/webapps/'.$appId.'/web2app1.js';
		// check if file exist
		$handle = @fopen($web2app1, 'r');

		if($handle){
			// get contents
			$content = file_get_contents($web2app1);

			// check
			if (!empty($content)) {
				// get manifest.json file content
				$manifestUrl = 'https://www.web2application.com/w2a/webapps/'.$appId.'/manifest.json';
				$json = file_get_contents($manifestUrl);

				// get firebase-messaging-sw.js content
				$jsUrl = 'https://www.web2application.com/w2a/webapps/'.$appId.'/firebase-messaging-sw.js';
				$js = file_get_contents($jsUrl);

				// define folder
				$dest = $_SERVER['DOCUMENT_ROOT']."/";

				// create json file
				$manifest = fopen($dest."manifest.json", "w");
				fwrite($manifest, $json);
				fclose($manifest);

				// create js file
				$fm = fopen($dest."firebase-messaging-sw.js", "w");
				fwrite($fm, $js);
				fclose($fm);
			}
		}
	}
}


/**
 * Add Web Push Files
 */
function footer_scripts() {
	global $w2a_options;

	// get appId
    /*$url = 'https://www.web2application.com/w2a/api-process/get_app_id.php?api_domain='.$_SERVER['SERVER_NAME'].'&api_key='.trim($w2a_options['w2a_api_key']);
    $appId = file_get_contents($url);*/
	$appId = get_web2app_id();

	// check
	if ($appId != 'Wrong API. Please Check Your API Key' && is_numeric($appId)) {

		// check if exist
		$web2app1 = 'https://www.web2application.com/w2a/webapps/'.$appId.'/web2app1.js';
		// check if file exist
		$handle = @fopen($web2app1, 'r');

		if($handle){
			// get contents
			$content = file_get_contents($web2app1);

			// check
			if (!empty($content)) {
?>

				<!--begin add other files to footer-->
				<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
				<link rel="manifest" href="/manifest.json">
				<script src="https://www.gstatic.com/firebasejs/7.11.0/firebase.js"></script>
				<script type="text/javascript" src="https://web2application.com/w2a/webapps/<?php echo $appId; ?>/web2app1.js"></script>
				<!--end other files to footer-->

<?php
			}
		}
	}
}
if ($w2a_options['w2a_disable_web_push'] != "1") {
	add_action('wp_enqueue_scripts', 'footer_scripts');
}
?>
