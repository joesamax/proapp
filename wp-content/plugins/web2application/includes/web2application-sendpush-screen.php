<?php
if ( ! defined( 'ABSPATH' ) ) exit;
	global $w2a_options;
	
	function w2a_enqueue_media_lib_uploader() {

		//Core media script
		wp_enqueue_media();
	
		// Your custom js file
		wp_register_script( 'media-lib-uploader-js', plugins_url( 'media-lib-uploader.js' , __FILE__ ), array('jquery') );
		wp_enqueue_script( 'media-lib-uploader-js' );
	}
	add_action('admin_enqueue_scripts', 'w2a_enqueue_media_lib_uploader');
	

	
//if nonces ok	
if (isset($_POST['send_push'])) {
    if(wp_verify_nonce($_REQUEST['w2a_push_submit_post'], 'w2a_push')){
    //	echo 'send push process start';
        if (isset($_POST['Push_Title']) && isset($_POST['Push_Message']) && $_POST['Push_Title'] != "" && $_POST['Push_Message'] != "") {

                //sanitize input fields
                $w2aPushTitle = sanitize_text_field($_POST['Push_Title']);
                $w2aPushMessage = sanitize_text_field($_POST['Push_Message']);
                $w2aPushImage = sanitize_text_field($_POST['image']);

                //send the data for pushing
                $url = 'http://www.web2application.com/w2a/api-process/send_push_from_plugin.php';
                $data = array('api_domain' => $_SERVER['SERVER_NAME'], 'api_key' => trim($w2a_options['w2a_api_key']),'push_title' => $w2aPushTitle, 'push_text' => $w2aPushMessage, 'push_image_url' => $w2aPushImage, 'push_link' => sanitize_text_field($_POST["Push_Link"]) );

                $options = array(
                    'http' => array(
                        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                        'method'  => 'POST',
                        'content' => http_build_query($data),
                    ),
                );
                $context  = stream_context_create($options);
                $html = file_get_contents($url, false, $context);
			
                if ($html != ""){	
                    echo '<div id="web2app-error-mesage">';
                    echo $html;
                    echo '</div>';
                }
        } else {
            echo '<div id="web2app-error-mesage">';
            echo _e('Missing Title Or Body', 'w2a_domain');
            echo '</div>';
        }
	// if nonces not ok
	} else {
		echo '<div id="web2app-error-mesage">';
			echo _e('oops... some thing wrong. Please reload the page and try again', 'w2a_domain');
		echo '</div>';
	}		
} //end if send
	
	
// woocomerce version check
function woocommerce_version_check( $version = '3.0.0' ) {
    if ( class_exists( 'WooCommerce' ) ) {
        global $woocommerce;
        if( version_compare( $woocommerce->version, $version, ">=" ) ) {
            return true;
        }
    }
    return false;
} // end

// get woocommerce version
function get_woo_version_number() {
    // If get_plugins() isn't available, require it
	if ( ! function_exists( 'get_plugins' ) )
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	
        // Create the plugins folder and file variables
	$plugin_folder = get_plugins( '/' . 'woocommerce' );
	$plugin_file = 'woocommerce.php';
	
	// If the plugin version number is set, return it 
	if ( isset( $plugin_folder[$plugin_file]['Version'] ) ) {
		return $plugin_folder[$plugin_file]['Version'];

	} else {
		return NULL;
	}
} // end


// get appId to check api key validity
$url = 'https://www.web2application.com/w2a/api-process/get_app_id.php?api_domain='.$_SERVER['SERVER_NAME'].'&api_key='.trim($w2a_options['w2a_api_key']);
$appId = file_get_contents($url);
// check
$disabled = ($appId == 'Wrong API. Please Check Your API Key' || trim($w2a_options['w2a_api_key']) == "") ? true : false;
?>

<!--link href="//web2application.com/w2a/user/lib/fontawesome-picker/css/fontawesome-iconpicker.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>-->

<h2><?php _e('Send Push Notification Screen','w2a_domain'); ?></h2>
<p class="description"><?php _e('From this Page you can send push notifications to all your users. If you need explantion about how to send the push notification, please ', 'w2a_domain'); ?><a href="http://web2application.com/send-push-notifications-throw-wordpress-plugin/" target="_blank"><?php _e('Click Here '); ?></a>
<div class="my-section">
<form method="post">
	
    <table class="form-table">
        <tbody>
            <tr>
                <th scope="row"><label><?php _e('Push Title','w2a_domain'); ?></label></th>
                <td><input name="Push_Title" type="text" id="Push_Title" value="<?php echo get_bloginfo( 'name' );?>" class="form-control col-md-4" <?php if ($disabled) { echo "disabled"; } ?> />
                    <p class="description"><?php _e('Please Enter Your Push Title', 'w2a_domain'); ?></p></td>
            </tr>
            <tr>
                <th scope="row"><label><?php _e('Push Message','w2a_domain'); ?></label></th>
                <td><input name="Push_Message" type="text" id="Push_Message" value="" class="form-control col-md-4" <?php if ($disabled) { echo "disabled"; } ?> />
                    <p class="description"><?php _e('Please Enter Your Message', 'w2a_domain'); ?></p></td>
            </tr>
            <tr>
                <th scope="row"><label><?php _e('Push Image','w2a_domain'); ?></label></th>
                <td>  
                    <input id="image-url" type="text" name="image" value=""/>
                  <input id="w2a-upload-button" type="button" class="button" value="Upload Or Select Image"  />
                  <p class="description"><?php _e('Please Select Image Or Paste Full Image Url. example : http://domain.com/image.jpg', 'w2a_domain'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label><?php _e('Push Link','w2a_domain'); ?></label></th>
                <td>
                    <select name="Push_Link" type="text" id="Push_Link" value="<?php echo 'http://'.$_SERVER['SERVER_NAME']; ?>" class="form-control col-md-4">
                            <option value="<?php echo get_home_url(); ?>"><?php _e('Home Page', 'w2a_domain'); ?></option>

                            <optgroup label="<?php _e('Last Posts', 'w2a_domain'); ?>">
                            <?php
                            $recent_posts = wp_get_recent_posts();
								
                            foreach( $recent_posts as $recent ){
                                echo '<option value="' . get_site_url().'/?p='.$recent["ID"] . '">' . $recent["post_title"] . '</option>';
                            }
                            wp_reset_query();
                            ?>

                            <optgroup label="<?php _e('Last Pages', 'w2a_domain'); ?>">
                            <?php
                            $pages = get_pages(); 
                            foreach( $pages as $page ){
                                echo '<option value="' . get_site_url().'/?p='.$page->ID . '">' . $page->post_title .  '</option>';
                            }
                            wp_reset_query();
                            ?>

                            <?php if ( class_exists( 'WooCommerce' ) ) { ?>
                                <optgroup label="<?php _e('Last products', 'w2a_domain'); ?>">
                            <?php
								// check
                                /*if( get_woo_version_number() >= 3.0 ) {
                                    $products = wc_get_products();
                                } else {
                                    $products = get_products();
                                }*/
								
								/*$products = wc_get_products();
								foreach( $products as $product ){
                                    echo '<option value="' . get_site_url().'/?p='.$product->get_id() . '">' .  $product->get_name() . '</option>';
                                }
                                wp_reset_query();*/
																		
								$args = array('post_type' => 'product', 
											  'posts_per_page' => 12);
								$loop = new WP_Query( $args );
								if ( $loop->have_posts() ) {
									while ( $loop->have_posts() ) : $loop->the_post();
										//echo '<option value="' . get_site_url().'/?p='.$product->get_id() . '">' .  $product->get_name() . '</option>';
										echo '<option value="' . get_permalink() .'">' . get_the_title() . '</option>';
									endwhile;
								} else {
									echo __( 'No products found' );
								}
								wp_reset_postdata();
                            ?>
                            <?php } ?>
                    </select>

                    <p class="description"><?php _e('The page or post that the push will lead to', 'w2a_domain'); ?></p>
                </td>
            </tr>
            <?php wp_nonce_field('w2a_push', 'w2a_push_submit_post'); ?>
		</tbody>
    </table> 
		
    <input type="submit" value="<?php _e('Send Push Notification', 'w2a_domain'); ?>" name="send_push" class="button button-primary" <?php if ($disabled) { echo "disabled"; } ?> />
</form> 
</div>    

<script>
	jQuery(document).ready(function($){
	
	  var w2aMediaUploader;
	
	  $('#w2a-upload-button').click(function(e) {
		e.preventDefault();
		// If the uploader object has already been created, reopen the dialog
		  if (w2aMediaUploader) {
		  w2aMediaUploader.open();
		  return;
		}
		// Extend the wp.media object
		w2aMediaUploader = wp.media.frames.file_frame = wp.media({
		  title: 'Choose Image',
		  button: {
		  text: 'Choose Image'
		}, multiple: false });
	
		// When a file is selected, grab the URL and set it as the text field's value
		w2aMediaUploader.on('select', function() {
		  attachment = w2aMediaUploader.state().get('selection').first().toJSON();
		  $('#image-url').val(attachment.url);
		});
		// Open the uploader dialog
		w2aMediaUploader.open();
	  });
	
	});
</script>

<?php ?>
