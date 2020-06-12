jQuery(
	function() {
		if (jQuery.fn.areYouSure) {
			jQuery( '#get_started_design form' ).areYouSure(
				{
					'fieldSelector': ":input:not(input[type=submit]):not(input[type=button]):not(#ml_preview_upload_image)"
				}
			);

			jQuery( "#get_started_menu_config form" ).areYouSure(
				{
					'fieldSelector': ":input:not(input[type=submit]):not(input[type=button]):not(select):not(input[type=text])"
				}
			);

			jQuery( "#ml_settings_general form" ).areYouSure();

			jQuery( "#ml_settings_analytics form" ).areYouSure();

			jQuery( "#ml_settings_editor form" ).areYouSure(
				{
					'fieldSelector': ":input:not(input[type=submit]):not(input[type=button]):not(select)"
				}
			);

			jQuery( "#ml_settings_membership form" ).areYouSure();

			jQuery( "#ml_settings_license form" ).areYouSure();

			jQuery( "#ml_push_settings form" ).areYouSure();

			jQuery( "#ml_settings_advertising form" ).areYouSure(
				{
					'fieldSelector': ":input:not(input[type=submit]):not(input[type=button]):not(#ml_ad_banner_position_select):not(#preview_popup_post_select)"
				}
			);

			jQuery( '#ml_settings_subscription form' ).areYouSure(
				{
					'fieldSelector': ":input:not(input[type=submit]):not(input[type=button])"
				}
			);

		}

		if (jQuery( '.ml2-sidebar' ).length) {
			jQuery( window ).on(
				'scroll resize',
				function() {
					var current_y     = jQuery( window ).scrollTop();
					var current_width = jQuery( window ).width();
					var main_y        = jQuery( '.ml2-main-area' ).offset().top;
					var $sidebar      = jQuery( '.ml2-sidebar' );
					if ((current_y > main_y - 50) && current_width >= 571) {
						if ( ! $sidebar.hasClass( 'ml-fixed' )) {
							$sidebar.css( {position:'fixed', 'right': '20px', top: '50px'} );
							$sidebar.addClass( 'ml-fixed' );
						}
					} else {
						if ($sidebar.hasClass( 'ml-fixed' )) {
							$sidebar.css( {position:'static', 'right': 'auto', top: 'auto'} );
							$sidebar.removeClass( 'ml-fixed' );
						}
					}
				}
			)
		}

		if (jQuery( '.nav-tab[data-tab]' ).length) {
			jQuery( '.nav-tab[data-tab]' ).on(
				'click',
				function() {
					var $tab = jQuery( jQuery( this ).data( 'tab' ) );
					$tab.show();
					$tab.siblings( '.nav-tab-content' ).hide();
					jQuery( this ).addClass( 'nav-tab-active' );
					jQuery( this ).siblings( '.nav-tab' ).removeClass( 'nav-tab-active' );
					return false;
				}
			)
		}

		if (jQuery( '.ml-value-get' ).length) {
			jQuery( '.ml-value-get' ).on(
				'change',
				function() {
					var $destination = jQuery( this ).closest( 'td' ).find( '.ml-value-set' );
					if ($destination.length) {
						$destination.text( jQuery( this ).val() );
					}
				}
			).trigger( 'change' );
		}

		if (jQuery( '.ml_load_ajax' ).length) {
			jQuery( '.ml_load_ajax' ).each(
				function() {
					var $that = jQuery( this );
					var data  = {
						action: 'ml_load_ajax',
						what: jQuery( this ).data( 'ml_what' ),
						ml_nonce: jQuery( '#ml_nonce_load_ajax' ).val(),
					};
					jQuery.post(
						ajaxurl,
						data,
						function(response) {
							if (response.data !== undefined) {
								$that.replaceWith( response.data );
								if (response.chosen) {
									jQuery( '#' + response.chosen ).chosen( {} );
								}
								if (response.show) {
									jQuery( response.show ).show();
								}
								if (response.ul_name) {
									jQuery( response.ul_name ).html( response.ul );
								}
							}
						}
					);
				}
			)
		}

		if (jQuery( '.ml-colorbox' ).length) {
			jQuery( '.ml-colorbox' ).each(
				function() {

					var $link_color = jQuery( this );
					$link_color.wpColorPicker(
						{
							change: function(event, ui) {
								pick_text_color( $link_color.wpColorPicker( 'color' ), jQuery( this ) );
							},
							clear: function() {
								pick_text_color( '', jQuery( this ) );
							}
						}
					);
					$link_color.trigger( 'click' ).trigger( 'keyup' );

					toggle_text_color( $link_color );
				}
			)
		}

		if (jQuery( '#ml_app_subscription_logo_button' ).length) {
			align_preview_app_sub_logo();

			var _custom_media     = true,
			_orig_send_attachment = wp.media.editor.send.attachment;

			jQuery( '#ml_app_subscription_logo_button' ).click(
				function(e) {
					var send_attachment_bkp         = wp.media.editor.send.attachment;
					var button                      = jQuery( this );
					var id                          = button.attr( 'id' ).replace( '_button', '' );
					_custom_media                   = true;
					wp.media.editor.send.attachment = function(props, attachment) {
						if (_custom_media) {
							jQuery( "#" + id ).val( attachment.url );
							load_app_sub_logo_image();
							ml_loadPreview();
						} else {
							return _orig_send_attachment.apply( this, [props, attachment] );
						}
					};

					wp.media.editor.open( button );
					return false;
				}
			);

			jQuery( ".ml-preview-app-sub-logo-remove-btn" ).click(
				function(e) {
					e.preventDefault();
					var confirmRemove = confirm( 'Are you sure you want to remove the image?' );
					if (confirmRemove) {
						jQuery( ".ml-preview-app-sub-logo-image-row" ).hide();
						jQuery( ".ml-preview-app-sub-logo-holder img" ).attr( 'src', '' );
						jQuery( "#ml_app_subscription_logo" ).val( '' );
						jQuery( "#ml_settings_subscription form" ).trigger( 'setDirty.areYouSure' );
					}
				}
			);

			jQuery( "#ml_preview_upload_image" ).keyup(
				function() {
					$ml_notify_element = jQuery( this );
					load_app_sub_logo_image();
					ml_loadPreview();
				}
			);
		}
	}
);

var text_default_color = '1e73be';
function pick_text_color(color, $link_color) {
	$link_color.val( color );
}

function toggle_text_color($link_color) {
	if ($link_color.length) {
		if ($link_color.val() === '' || '' === $link_color.val().replace( '#', '' )) {
			var default_color = 'undefined' != typeof $link_color.data( 'color' ) ? $link_color.data( 'color' ) : text_default_color;
			$link_color.val( default_color );
			pick_text_color( default_color, $link_color );
		} else {
			pick_text_color( $link_color.val(), $link_color );
		}
	}
}

var align_preview_app_sub_logo = function() {
	var $imageHolder = jQuery( ".ml-preview-app-sub-logo-holder" );
	var $image       = jQuery( "img", $imageHolder );
	if ($imageHolder.length && $image.length) {
		if ($image.height > $image.width) {
			$image.height = '100%';
			$image.width  = 'auto';
		}
	}
};

var load_app_sub_logo_image = function() {
	if (jQuery( "#ml_app_subscription_logo" ).val().length > 0) {
		jQuery( ".ml-preview-app-sub-logo-image-row" ).show();
		jQuery( ".ml-preview-app-sub-logo-holder img" ).attr( 'src', jQuery( "#ml_app_subscription_logo" ).val() );
		align_preview_app_sub_logo();
	} else {
		jQuery( ".ml-preview-app-sub-logo-image-row" ).hide();
	}
};
