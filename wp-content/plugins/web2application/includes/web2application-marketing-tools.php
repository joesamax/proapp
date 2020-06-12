<?php
if ( ! defined( 'ABSPATH' ) ) exit;
// Init Options Global
global $w2a_options;


// UPDATE APP LINKS
if (isset($_POST['submit'])) {
	if(wp_verify_nonce($_REQUEST['w2a_update_app_links_submit_post'], 'w2a_update_app_links')){

		//sanitize input fields
		$postData = $_POST['data']; //sanitize_text_field($_POST['data']);

		// send the data to save
		$url = 'http://www.web2application.com/w2a/api-process/save_app_store_links_from_plugin.php';
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

// SAVE OFFER
if (isset($_POST['submit_offer'])) {
	if(wp_verify_nonce($_REQUEST['w2a_save_offer_submit_post'], 'w2a_save_offer')){

		//sanitize input fields
		$postData = $_POST['data']; //sanitize_text_field($_POST['data']);

		// send the data to save
		$url = 'http://www.web2application.com/w2a/api-process/save_offer_from_plugin.php';
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
	// get app marketing tools
	$url 		= 'https://www.web2application.com/w2a/api-process/get_app_marketing_tools.php?api_domain='.$_SERVER['SERVER_NAME'].'&api_key='.trim($w2a_options['w2a_api_key']);
	$app 		= file_get_contents($url);
	$row 		= json_decode($app);
}

?>

<style type="text/css">
.form-control {
    width: 400px;
}
</style>

<div class="wrap">
	<h2><?php _e('Marketing Tools - Bring More Users', 'w2a_domain'); ?></h2>

<div class="my-section">
	
	<h3><?php _e('App Download QR Code And Link', 'w2a_domain'); ?></h3>
	<table class="form-table">
        <tbody>
            <tr>
				<td width="70%" valign="top">
					<p>We made for you a special QR Code and link that you can send via whatsapp, sms, put in your site, banner links and more... <br>When a use enter the link or scan the code he will be redirect, according to his device to the right store for download</p>
					<?php if ($appId != 'Wrong API. Please Check Your API Key' && is_numeric($appId)) { ?>
					<p style="font-size:24px;"><strong>https://web2application.com/w2a/sl.php?an=<?php echo $row->app_id; ?></strong></p>
					<?php } else { ?>
					<p style="color: red;"><b>Web2Application plugin not set. Please go to Web2application -> Setting and fix your API key</b></p>
					<?php } ?>
				</td>
				<?php if ($appId != 'Wrong API. Please Check Your API Key' && is_numeric($appId)) { ?>
                <td><img src="http://web2application.com/w2a/qrcodes/temp/<?php echo $row->barcode_file_name; ?>" width="200" /></td>
				<?php } ?>
            </tr>
        </tbody>
    </table>

	<p><?php _e('Store links : (if your link needs update please copy the url from the store and update here)'); ?></p>

	<form method="post">
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row"><label><?php _e('Google Play Store URL','w2a_domain'); ?></label></th>
                    <td><input name="data[android_store_url]" type="text" value="<?php echo ($row->android_store_url); ?>" class="form-control col-md-4" <?php if ($disabled) { echo "disabled"; } ?> /></td>
                </tr>
                <tr>
                    <th scope="row"><label><?php _e('Apple Apps Store URL','w2a_domain'); ?></label></th>
                    <td><input name="data[apple_store_url]" type="text" value="<?php echo ($row->apple_store_url); ?>" class="form-control col-md-4" <?php if ($disabled) { echo "disabled"; } ?> /></td>
                </tr>
            </tbody>
        </table>
        <?php wp_nonce_field('w2a_update_app_links', 'w2a_update_app_links_submit_post'); ?>
        <p class="submit"><input type="submit" name="submit" class="button button-primary" value="<?php _e('Save Changes', 'w2a_domain'); ?>" <?php if ($disabled) { echo "disabled"; } ?> /></p>
    </form>
    
</div>

<div class="my-section" style="margin-top:20px;">

	<h3><?php _e('Special Offers To App Users Only'); ?></h3>
	<p class="description"><?php _e('You can create special offers only to the app users and advert them on social networks, mailing list,sms and ...<br>If somebody will get the link and click the system will check if your application are installed on the device and open the offer. If not, the user will be redirect to google paly or apple appstore to download your app!<br>Its a great tool to make users download your app!<br>The offer link will be available for 90 days.'); ?></p>

	<h4><?php _e('Add New Offer - Please fill the offer details:'); ?></h4>

	<form method="post">
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row"><label><?php _e('Offer Name','w2a_domain'); ?></label></th>
                    <td><input name="data[offer_name]" type="text" class="form-control col-md-4" required <?php if ($disabled) { echo "disabled"; } ?> /></td>
                </tr>
                <tr>
                    <th scope="row"><label><?php _e('Offer Original URL link in Your Website','w2a_domain'); ?></label></th>
                    <td><input name="data[offer_original_url]" type="text" class="form-control col-md-4" required <?php if ($disabled) { echo "disabled"; } ?> /></td>
                </tr>
                <tr>
                    <th scope="row"><label><?php _e('Message to users that dont have the app (Not all browsers ask it)','w2a_domain'); ?></label></th>
                    <td><input name="data[offer_dont_have_prompt]" type="text" class="form-control col-md-4" required value="Its seem that you dont have the app installed. do you want to download the app now?" <?php if ($disabled) { echo "disabled"; } ?> /></td>
                </tr>
                <tr>
                    <th scope="row"><label><?php _e('Desktop URL link in your website (To where users will redirect if they click the link from desktop computer)','w2a_domain'); ?></label></th>
                    <td><input name="data[offer_desktop_link]" type="text" class="form-control col-md-4" required <?php if ($disabled) { echo "disabled"; } ?> /></td>
                </tr>
				<input name="data[android_store_url]" type="hidden" value="<?php echo ($row->android_store_url); ?>" />
				<input name="data[apple_store_url]" type="hidden" value="<?php echo ($row->apple_store_url); ?>" />
				<input name="data[android_pack_name]" type="hidden" value="<?php echo ($row->android_pack_name); ?>" />
            </tbody>
        </table>
        <?php wp_nonce_field('w2a_save_offer', 'w2a_save_offer_submit_post'); ?>
        <p class="submit"><input type="submit" name="submit_offer" class="button button-primary" value="<?php _e('Add Offer and Get Link', 'w2a_domain'); ?>" <?php if ($disabled) { echo "disabled"; } ?> /></p>
    </form>
    <br><br><hr><br>
</div>
<div class="my-section" style="margin-top:20px;">

	<h3><?php _e('Offer Links', 'w2a_domain'); ?></h3>
    <table class="form-table">
        <thead class="thead-dark">
            <tr>
                <th scope="col" width="150">Offer Name</th>
                <th scope="col" width="200">Original Link</th>
                <th scope="col" width="200">Promotion Smart Link To Advert</th>
                <th scope="col" class="text-center" width="150">Action</th>
            </tr>
        </thead>
        <tbody>
        <?php
            // iterate
            foreach ($row->offers as $offer) {
                $currentLinkNumber = $offer->offer_auto_number;
        ?>

            <tr>
              <td><?php _e($offer->offer_name); ?></td>
              <td><?php _e($offer->offer_original_link); ?></td>
              <td><div id="specialOfferNo<?php echo $currentLinkNumber; ?>"><?php _e($offer->offer_advert_link); ?></div></td>
              <td class="text-center">
                <a href="javascript:copyToClipboard('specialOfferNo<?php echo $currentLinkNumber; ?>')">Copy Link</a>
              </td>
            </tr>

        <?php } ?>
        </tbody>
    </table>

</div>
</div>
<script type="text/javascript" charset="utf-8">
function copyToClipboard(containerid) {
    if (document.selection) {
        var range = document.body.createTextRange();
        range.moveToElementText(document.getElementById(containerid));
        range.select().createTextRange();
        document.execCommand("copy");

    } else if (window.getSelection) {
        window.getSelection().removeAllRanges();
        var range = document.createRange();
         range.selectNode(document.getElementById(containerid));
         window.getSelection().addRange(range);
         document.execCommand("Copy", false, null);
    }
}
</script>
