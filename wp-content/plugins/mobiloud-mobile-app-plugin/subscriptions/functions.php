<?php
// returns
// WP_User if login successful
// WP_Error if error
function ml_login_wordpress( $username, $password ) {
	$creds                  = array();
	$creds['user_login']    = $username;
	$creds['user_password'] = $password;
	$creds['remember']      = true;
	$user                   = wp_signon( $creds, false );

	if ( get_class( $user ) === 'WP_User' ) {
		wp_set_current_user( $user->ID );
	}

	return $user;
}

function ml_has_groups_library() {
	return ( class_exists( 'Groups_Post_Access' ) && class_exists( 'Groups_User' ) );
}

function ml_subscriptions_enable() {
	return ( ml_has_groups_library() && get_option( 'ml_subscriptions_enable' ) !== 'false' );
}


// filter posts by capabilities for the user_id
function ml_subscriptions_filter_posts( $posts, $user_id ) {
	$filtered_posts = array();
	foreach ( $posts as $post ) {
		if ( Groups_Post_Access::user_can_read_post( $post->ID, $user_id ) ) {
			$filtered_posts[] = $post;
		}
	}

	return $filtered_posts;
}

function ml_subscriptions_post_capabilities( $post ) {
	$capabilities = array();
	foreach ( Groups_Post_Access::get_read_post_capabilities( $post->ID ) as $capability ) {
		if ( $capability !== null ) {
			$capabilities[] = $capability;
		}
	}

	return $capabilities;
}

function ml_paywall_categories_restricted( $cats, $single = false ) {
	$restricted = false;
	$terms = explode( ',', $cats );
	$rcount = 0;
	foreach ( $terms as $term ) {
		$opt = get_option( "taxonomy_" . $term );
		if ( $opt['ml_tax_paywall'] === 'true' ) {
			if ( $single ) {
				$restricted = true;
			}
			// count total restricted categories
			$rcount++;
		}
	}

	if ( $rcount === count( $terms ) ) {
		$restricted = true;
	}

	return $restricted;
}

function ml_is_content_restricted() {
	$restricted = false;
	$url = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];

	if ( strpos( $url, '/list?' ) && isset( $_GET['taxonomy'] ) && isset( $_GET['term_id'] ) ) {
		$restricted = ml_paywall_categories_restricted( sanitize_text_field( $_GET['term_id'] ) );
	}

	if ( strpos( $url, '/list?' ) && isset( $_GET['categories'] ) ) {
		$restricted = ml_paywall_categories_restricted( sanitize_text_field( $_GET['categories'] ) );
	}

	if ( ( strpos( $url, '/post?' ) || strpos( $url, '/posts?' ) ) && isset( $_GET['post_id'] ) ) {

		if ( get_post_meta( sanitize_text_field( $_GET['post_id'] ), 'ml_paywall_protected', true ) === 'true' ) {
			$restricted = true;
		} else {
			$taxes = get_taxonomies( array(
				'public' => true,
			) );

			$cats = wp_get_post_terms( sanitize_text_field( $_GET['post_id'] ), $taxes );
			$terms = array();
			foreach( $cats as $cat ) {
				$terms[] = $cat->term_id;
			}
			$restricted = ml_paywall_categories_restricted( implode( ',', $terms ), true );
		}
	}

	return $restricted;
}

function ml_validate_requests() {
	// Check if content is restricted
	ml_paywall_validate_user();
}

function ml_paywall_validate_user() {
	$url = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];

	// Check subscription headers
	if ( isset ( $_SERVER['HTTP_X_ML_VALIDATION'] ) && ! empty ( $_SERVER['HTTP_X_ML_VALIDATION'] ) && strlen( $_SERVER['HTTP_X_ML_VALIDATION'] ) > 18 ) {
		// get auth header string
		$ml_token = explode( '|', sanitize_text_field( $_SERVER['HTTP_X_ML_VALIDATION'] ) );
		$ml_users = get_users( array('meta_key' => 'ml_auth_token', 'meta_value' => $ml_token[0] ) );

		wp_set_current_user( $ml_users[0]->ID );

		// Filter to check content access for current user
		$restricted = apply_filters( 'mobiloud_paywall_loggedin_access', $url );

		if ( ! is_user_logged_in() || $restricted ) {
			ml_paywall_block();
		} else {
			header( 'X-ML-VALIDATION: ' . $ml_token[0] . '|' . time() );
		}
	} elseif ( isset ( $_SERVER['HTTP_X_ML_IS_USER_SUBSCRIBED'] ) && $_SERVER['HTTP_X_ML_IS_USER_SUBSCRIBED'] === 'true' && isset( $_SERVER['HTTP_X_ML_SUBSCRIPTION_ID'] ) ) {
		// user is subscribed, do nothing
	} elseif ( ml_is_content_restricted() ) {
		// trigger paywall block
		ml_paywall_block();
	}
}

function ml_paywall_block() {
	include MOBILOUD_PLUGIN_DIR . 'views/paywall.php';
}

function ml_paywall_meta_box() {

	$ml_post_types = get_post_types();

	foreach ( $ml_post_types as $ml_post_type ) {
		if ( $ml_post_type === 'attachment' || $ml_post_type === 'nav_menu_item' ) {
			continue;
		}
		add_meta_box(
			'mobiloud_app_paywall_metabox', __( 'MobiLoud App Paywall' ), 'ml_paywall_metabox_process', $ml_post_type, 'side', 'high'
		);
	}
}

function ml_paywall_metabox_process( $post ) {
	// Add a nonce field so we can check for it later.
	wp_nonce_field( 'ml_paywall_nonce', 'ml_paywall_nonce' );
	$value = get_post_meta( $post->ID, 'ml_paywall_protected', true );
?>
	<label>
		<input type="checkbox" name="ml_paywall_protected" <?php if ( $value === 'true' ) { echo 'checked'; } ?> />
		Protect this content with Paywall
	</label>
<?php
}

function ml_save_paywall_meta_box_data( $post_id ) {

	// Check if our nonce is set.
	if ( ! isset( $_POST['ml_paywall_nonce'] ) ) {
		return;
	}

	// Verify that the nonce is valid.
	if ( ! wp_verify_nonce( sanitize_text_field( $_POST['ml_paywall_nonce'] ), 'ml_paywall_nonce' ) ) {
		return;
	}

	// If this is an autosave, our form has not been submitted, so we don't want to do anything.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// Check the user's permissions.
	if ( isset( $_POST['post_type'] ) && 'page' === sanitize_text_field( $_POST['post_type'] ) ) {

		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return;
		}
	}
	else {
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
	}

	$option_value = 'false';
	// Make sure that it is set.
	if ( ! isset( $_POST['ml_paywall_protected'] ) ) {
		$option_value = 'false';
	} else {
		$option_value = 'true';
	}

	// Update the meta field in the database.
	update_post_meta( $post_id, 'ml_paywall_protected', $option_value );
}

function ml_taxonomy_paywall_protected( $term = null ) {

	// check if on Edit screen
	$term_protected = array(
		'ml_tax_paywall' => 'false',
	);
	if ( ! empty( $term ) && is_object( $term ) ) {
		$t_id = $term->term_id;
		$term_protected = get_option( "taxonomy_$t_id" );
	}
	?>

	<tr class="form-field">
		<th scope="row" valign="top"><label for="term_meta[ml_tax_paywall]">MobiLoud App Paywall</label></th>
		<td>
			<label>
				<input type="checkbox" name="term_meta[ml_tax_paywall]" id="term_meta[ml_tax_paywall]" <?php echo ( $term_protected['ml_tax_paywall'] === 'true' ) ? "checked" : ''; ?> />
				Protect this content with Paywall
			</label>
			<br/>
		</td>
	</tr>
	<?php
}

function ml_taxonomy_paywall_save( $term_id ) {
	$t_id = $term_id;
	$term_meta = get_option( "taxonomy_$t_id" );

	if ( isset( $_POST['term_meta']['ml_tax_paywall'] ) ) {
		$term_meta['ml_tax_paywall'] = 'true';
	} else {
		$term_meta['ml_tax_paywall'] = 'false';
	}

	// Save the option array.
	update_option( "taxonomy_$t_id", $term_meta );
}


