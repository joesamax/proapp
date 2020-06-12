<?php
if ( ! defined( 'ABSPATH' ) ) exit;
define('WP_DEBUG', false);
// Init Options Global
global $w2a_options;
	
// create regular HTML Object
if (isset($_POST['submit'])) {
	if(wp_verify_nonce($_REQUEST['w2a_premium_submit_post'], 'w2a_premium')){
		
		//sanitize input fields
		$postData = $_POST['data']; //sanitize_text_field($_POST['data']);
		
		// send the data to save
		$url = 'http://www.web2application.com/w2a/api-process/save_premium_settings_from_plugin.php';
		$data = array('api_domain' => $_SERVER['SERVER_NAME'], 'api_key' => trim($w2a_options['w2a_api_key']), 'data' => $postData);
		
		$options = array(
                    'http' => array(
                        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                        'method'  => 'POST',
                        'content' => http_build_query($data),
                    ),
                );
        $context  = stream_context_create($options);
        $html = file_get_contents($url, false, $context);

		// check if response is not empty
        if ($html != ""){
            echo '<div id="web2app-error-mesage">';
            echo $html;
            echo '</div>';
        }

	} else {
		// display error
		echo '<div id="web2app-error-mesage">';
		echo _e('oops... some thing wrong. Please reload the page and try again', 'w2a_domain');
		echo '</div>';
	}
}

// get appId to check api key validity
$url = 'https://www.web2application.com/w2a/api-process/get_app_id.php?api_domain='.$_SERVER['SERVER_NAME'].'&api_key='.trim($w2a_options['w2a_api_key']);
$appId = file_get_contents($url);
// check
$disabled = ($appId == 'Wrong API. Please Check Your API Key' || trim($w2a_options['w2a_api_key']) == "") ? true : false;

// check
if ($appId != 'Wrong API. Please Check Your API Key' && is_numeric($appId)) {
	// get app premium settings
	$url = 'https://www.web2application.com/w2a/api-process/get_premium_settings.php?api_domain='.$_SERVER['SERVER_NAME'].'&api_key='.trim($w2a_options['w2a_api_key']);
	$settings = file_get_contents($url);
	$row = json_decode($settings);
}

?>

<style type="text/css">
.form-control {
    width: 400px;
}
</style>

<div class="wrap">

    <h2><?php _e('Web2Application Premium Setting Page', 'w2a_domain'); ?></h2>

    <form method="post">
		<div class="my-section">
        <h3><?php _e('App Settings', 'w2a_domain'); ?></h3>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row"><label><?php _e('Enable Signup Form','w2a_domain'); ?></label></th>
                    <td>
                        <table>
                            <tr>
                                <td><label for="w2a_signup_form1"><input type="radio" id="w2a_signup_form1" name="data[signup_form]" value="1" <?php echo ($row->signup_form == 1) ? "checked" : ""; ?> <?php if ($disabled) { echo "disabled"; } ?> />Yes</label></td>
                                <td><label for="w2a_signup_form0"><input type="radio" id="w2a_signup_form0" name="data[signup_form]" value="0" <?php echo ($row->signup_form == 0) ? "checked" : ""; ?> <?php if ($disabled) { echo "disabled"; } ?> />No</label></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label><?php _e('Allow Landscape','w2a_domain'); ?></label></th>
                    <td>
                        <table>
                            <tr>
                                <td><label for="w2a_allow_landscape1"><input type="radio" id="w2a_allow_landscape1" name="data[allow_landscape]" value="1" <?php echo ($row->allow_landscape == 1) ? "checked" : ""; ?> <?php if ($disabled) { echo "disabled"; } ?>/>Yes</label></td>
                                <td><label for="w2a_allow_landscape0"><input type="radio" id="w2a_allow_landscape0" name="data[allow_landscape]" value="0" <?php echo ($row->allow_landscape == 0) ? "checked" : ""; ?> <?php if ($disabled) { echo "disabled"; } ?>/>No</label></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label><?php _e('Allow Zoom In/Out','w2a_domain'); ?></label></th>
                    <td>
                        <table>
                            <tr>
                                <td><label for="w2a_allow_zoom1"><input type="radio" id="w2a_allow_zoom1" name="data[allow_zoom]" value="1" <?php echo ($row->allow_zoom == 1) ? "checked" : ""; ?> <?php if ($disabled) { echo "disabled"; } ?> />Yes</label></td>
                                <td><label for="w2a_allow_zoom0"><input type="radio" id="w2a_allow_zoom0" name="data[allow_zoom]" value="0" <?php echo ($row->allow_zoom == 0) ? "checked" : ""; ?> <?php if ($disabled) { echo "disabled"; } ?> />No</label></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label><?php _e('Support Deep Link','w2a_domain'); ?></label></th>
                    <td>
                        <table>
                            <tr>
                                <td><label for="w2a_support_deep_link1"><input type="radio" id="w2a_support_deep_link1" name="data[support_deep_link]" value="1" <?php echo ($row->support_deep_link == 1) ? "checked" : ""; ?> <?php if ($disabled) { echo "disabled"; } ?> />Yes</label></td>
                                <td><label for="w2a_support_deep_link0"><input type="radio" id="w2a_support_deep_link0" name="data[support_deep_link]" value="0" <?php echo ($row->support_deep_link == 0) ? "checked" : ""; ?> <?php if ($disabled) { echo "disabled"; } ?> />No</label></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table><br><br>
		</div>
		<div class="my-section" style="margin-top:20px;">
        <h3><?php _e('Signup Form Setting', 'w2a_domain'); ?></h3>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row"><label><?php _e('Form Label Title','w2a_domain'); ?></label></th>
                    <td><input name="data[form_label_title]" type="text" value="<?php echo ($row->form_label_title); ?>" class="form-control col-md-4" <?php if ($disabled) { echo "disabled"; } ?> /></td>
                </tr>
                <tr>
                    <th scope="row"><label><?php _e('Form Label Description','w2a_domain'); ?></label></th>
                    <td><input name="data[form_label_desc]" type="text" value="<?php echo ($row->form_label_desc); ?>" class="form-control col-md-4" <?php if ($disabled) { echo "disabled"; } ?> /></td>
                </tr>
                <tr>
                    <th scope="row"><label><?php _e('Form Label for Name Field','w2a_domain'); ?></label></th>
                    <td><input name="data[form_label_name]" type="text" value="<?php echo ($row->form_label_name); ?>" class="form-control col-md-4" <?php if ($disabled) { echo "disabled"; } ?> /></td>
                </tr>
                <tr>
                    <th scope="row"><label><?php _e('Form Label for Email Field','w2a_domain'); ?></label></th>
                    <td><input name="data[form_label_email]" type="text" value="<?php echo ($row->form_label_email); ?>" class="form-control col-md-4" <?php if ($disabled) { echo "disabled"; } ?> /></td>
                </tr>
                <tr>
                    <th scope="row"><label><?php _e('Form Label for Phone Field','w2a_domain'); ?></label></th>
                    <td><input name="data[form_label_phone]" type="text" value="<?php echo ($row->form_label_phone); ?>" class="form-control col-md-4" <?php if ($disabled) { echo "disabled"; } ?> /></td>
                </tr>
                <tr>
                    <th scope="row"><label><?php _e('Form Label for Birth Date Field','w2a_domain'); ?></label></th>
                    <td><input name="data[form_label_birth]" type="text" value="<?php echo ($row->form_label_birth); ?>" class="form-control col-md-4" <?php if ($disabled) { echo "disabled"; } ?> /></td>
                </tr>
                <tr>
                    <th scope="row"><label><?php _e('Form Label for Submit Button','w2a_domain'); ?></label></th>
                    <td><input name="data[form_label_submit]" type="text" value="<?php echo ($row->form_label_submit); ?>" class="form-control col-md-4" <?php if ($disabled) { echo "disabled"; } ?> /></td>
                </tr>
            </tbody>
        </table><br><br>
		</div>
		
		<div class="my-section" style="margin-top:20px;">
        <h3><?php _e('Advanced Settings', 'w2a_domain'); ?></h3>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row"><label><?php _e('Remember Password and Form Data in App','w2a_domain'); ?></label></th>
                    <td>
                        <table>
                            <tr>
                                <td><label for="w2a_remember_password1"><input type="radio" id="w2a_remember_password1" name="data[remember_password]" value="1" <?php echo ($row->remember_password == 1) ? "checked" : ""; ?> <?php if ($disabled) { echo "disabled"; } ?> />Yes</label></td>
                                <td><label for="w2a_remember_password0"><input type="radio" id="w2a_remember_password0" name="data[remember_password]" value="0" <?php echo ($row->remember_password == 0) ? "checked" : ""; ?> <?php if ($disabled) { echo "disabled"; } ?> />No</label></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label><?php _e('Open External Link in Browser','w2a_domain'); ?></label></th>
                    <td>
                        <table>
                            <tr>
                                <td><label for="w2a_open_to_browser0"><input type="radio" id="w2a_open_to_browser0" name="data[open_to_browser]" value="0" <?php echo ($row->open_to_browser == 0) ? "checked" : ""; ?> <?php if ($disabled) { echo "disabled"; } ?> />No</label></td>
                                <td><label for="w2a_open_to_browser1"><input type="radio" id="w2a_open_to_browser1" name="data[open_to_browser]" value="1" <?php echo ($row->open_to_browser == 1) ? "checked" : ""; ?> <?php if ($disabled) { echo "disabled"; } ?> />Open _blank tag in browser</label></td>
                                <td><label for="w2a_open_to_browser2"><input type="radio" id="w2a_open_to_browser2" name="data[open_to_browser]" value="2" <?php echo ($row->open_to_browser == 2) ? "checked" : ""; ?> <?php if ($disabled) { echo "disabled"; } ?> />Open all external link in browser</label></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label><?php _e('Disable No Internet Connection Screen','w2a_domain'); ?></label></th>
                    <td>
                        <table>
                            <tr>
                                <td><label for="w2a_disable_no_internet1"><input type="radio" id="w2a_disable_no_internet1" name="data[disable_no_internet]" value="1" <?php echo ($row->disable_no_internet == 1) ? "checked" : ""; ?> <?php if ($disabled) { echo "disabled"; } ?> />Yes</label></td>
                                <td><label for="w2a_disable_no_internet0"><input type="radio" id="w2a_disable_no_internet0" name="data[disable_no_internet]" value="0" <?php echo ($row->disable_no_internet == 0) ? "checked" : ""; ?> <?php if ($disabled) { echo "disabled"; } ?> />No</label></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label><?php _e('Disable Cached in WebView','w2a_domain'); ?></label></th>
                    <td>
                        <table>
                            <tr>
                                <td><label for="w2a_disable_webview_cache1"><input type="radio" id="w2a_disable_webview_cache1" name="data[disable_webview_cache]" value="1" <?php echo ($row->disable_webview_cache == 1) ? "checked" : ""; ?> <?php if ($disabled) { echo "disabled"; } ?> />Yes</label></td>
                                <td><label for="w2a_disable_webview_cache0"><input type="radio" id="w2a_disable_webview_cache0" name="data[disable_webview_cache]" value="0" <?php echo ($row->disable_webview_cache == 0) ? "checked" : ""; ?> <?php if ($disabled) { echo "disabled"; } ?> />No</label></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label><?php _e('Support Progress Bar','w2a_domain'); ?></label></th>
                    <td>
                        <table>
                            <tr>
                                <td><label for="w2a_allow_progress_bar0"><input type="radio" id="w2a_allow_progress_bar0" name="data[allow_progress_bar]" value="0" <?php echo ($row->allow_progress_bar == 0) ? "checked" : ""; ?> <?php if ($disabled) { echo "disabled"; } ?> />Dont show progress bar</label></td>
                                <td><label for="w2a_allow_progress_bar1"><input type="radio" id="w2a_allow_progress_bar1" name="data[allow_progress_bar]" value="1" <?php echo ($row->allow_progress_bar == 1) ? "checked" : ""; ?> <?php if ($disabled) { echo "disabled"; } ?> />Line progress bar</label></td>
                                <td><label for="w2a_allow_progress_bar2"><input type="radio" id="w2a_allow_progress_bar2" name="data[allow_progress_bar]" value="0" <?php echo ($row->allow_progress_bar == 2) ? "checked" : ""; ?> <?php if ($disabled) { echo "disabled"; } ?> />Round progress bar</label></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label><?php _e('Disable Pull to Refresh','w2a_domain'); ?></label></th>
                    <td>
                        <table>
                            <tr>
                                <td><label for="w2a_disable_pull_to_refresh1"><input type="radio" id="w2a_disable_pull_to_refresh1" name="data[disable_pull_to_refresh]" value="1" <?php echo ($row->disable_pull_to_refresh == 1) ? "checked" : ""; ?> <?php if ($disabled) { echo "disabled"; } ?> />Yes</label></td>
                                <td><label for="w2a_disable_pull_to_refresh0"><input type="radio" id="w2a_disable_pull_to_refresh0" name="data[disable_pull_to_refresh]" value="0" <?php echo ($row->disable_pull_to_refresh == 0) ? "checked" : ""; ?> <?php if ($disabled) { echo "disabled"; } ?> />No</label></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
		</div>
        <?php wp_nonce_field('w2a_premium', 'w2a_premium_submit_post'); ?>

        <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes', 'w2a_domain'); ?>"/></p>

    </form>

</div>

<?php ?>
