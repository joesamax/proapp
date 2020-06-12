<?php
if ( ! defined( 'ABSPATH' ) ) exit;
// Init Options Global
global $w2a_options;

// create regular HTML Object
ob_start(); ?>

<!--<link href="//web2application.com/w2a/user/lib/fontawesome-picker/css/fontawesome-iconpicker.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>-->

<div class="wrap">

    <h2><?php _e('Web2Application Setting Page', 'w2a_domain'); ?></h2>

    <form method="post" action="options.php">

        <?php settings_fields('w2a_settings_group'); ?>

        <h3><?php _e('Web2Application Setting', 'w2a_domain'); ?></h3>
        <div class="my-section">
			<table class="form-table">
				<tbody>
					<p><?php _e('In order this plugin will work you must have API Secret Key frob Web2application console.','w2a_domain'); ?> <br />
						<?php _e('Please login to your app dashborad in our system, Go to Push Setting page from the side menu and copy this url : ','w2a_domain'); ?><strong style="font-size:20px; color:green;"><?php echo $_SERVER['SERVER_NAME']; ?> </strong> <br />
						<?php _e('Please copy the url AS IS , do not add any chars to is','w2a_domain'); ?> <br />
						<?php _e('To signin to your account or signup to the system'); ?><a href="http://web2application.com/w2a/user/login.php" target="_blank"><?php _e('Click Here '); ?></a>
					</p>
					<tr>
						<th scope="row"><label for="w2a_api_key"><?php _e('Your web2application API key','w2a_domain'); ?></label></th>
						<td><input name="w2a_settings[w2a_api_key]" type="text" id="w2a_api_key" value="<?php echo $w2a_options['w2a_api_key']; ?>" class="form-control col-md-4">
							<p class="description"><?php _e('Enter your Web2application API key . If you dont have API key and you need help ,please ', 'w2a_domain'); ?><a href="http://web2application.com/create-api-key-wordpress-plugin/" target="_blank"><?php _e('Click Here '); ?></a></p></td>
					</tr>
					
				</tbody>
			</table><br><br>
		</div>
		
		<div class="my-section" style="margin-top:20px;">
			<h3><?php _e('Web Push Setting', 'w2a_domain'); ?></h3>
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row"><label for="w2a_disable_web_push"><?php _e('Disable Web Push','w2a_domain'); ?></label></th>
						<td><?php if ($w2a_options['w2a_disable_web_push'] != "") { ?>
							<input type="checkbox" name="w2a_settings[w2a_disable_web_push]" id="w2a_disable_web_push" value="1" checked />
							<?php } else { ?>
							<input type="checkbox" name="w2a_settings[w2a_disable_web_push]" id="w2a_disable_web_push" value="1" />
							<?php } ?>
							<p class="description"><?php _e('This will turn off the web push usage.','w2a_domain'); ?></p>
						</td>
					</tr>
				</tbody>
			</table><br><br>

			<h3><?php _e('Elementor Setting', 'w2a_domain'); ?></h3>
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row"><label for="w2a_disable_elementor"><?php _e('Disable Elementor Feature','w2a_domain'); ?></label></th>
						<td><?php if ($w2a_options['w2a_disable_elementor'] == "1") { ?>
							<input type="checkbox" name="w2a_settings[w2a_disable_elementor]" id="w2a_disable_elementor" value="1" checked />
							<?php } else { ?>
							<input type="checkbox" name="w2a_settings[w2a_disable_elementor]" id="w2a_disable_elementor" value="1" />
							<?php } ?>
							<p class="description"><?php _e('This will turn off the elementor features.','w2a_domain'); ?></p>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
        <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes', 'w2a_domain'); ?>"/></p>

    </form>

</div>

<?php

echo ob_get_clean();
