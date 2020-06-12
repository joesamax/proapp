jQuery( document ).ready(
	function() {
		jQuery( "input[name='ml_show_email_contact_link']" ).on(
			'click',
			function() {
				if (jQuery( this ).is( ':checked' )) {
					jQuery( '.ml-email-contact-row' ).show();
				} else {
					jQuery( '.ml-email-contact-row' ).hide();
				}
			}
		);
		jQuery( "input[name='ml_comments_system']" ).on(
			'click',
			function() {
				var sys = jQuery( "input[name='ml_comments_system']:checked" ).val();
				if ( 'disqus' == sys) {
					jQuery( ".ml-disqus-row" ).show();
					jQuery( ".ml-rest-api-row" ).hide();
				} else {
					jQuery( ".ml-disqus-row" ).hide();

					if ( 'wordpress' == sys ) {
						jQuery( ".ml-rest-api-row" ).show();
					} else {
						jQuery( ".ml-rest-api-row" ).hide();
					}
				}
			}
		);
		jQuery( "input#ml_comments_rest_api" ).on(
			'change',
			function() {
				var $warning = jQuery( '#ml_comments_rest_api_enabled_msg' );
				if ( jQuery( this ).is( ':checked' ) ) {
					$warning.show();
				} else {
					$warning.hide();
				}
			}
		);
		jQuery( "input[name='homepagetype']" ).on(
			'change',
			function() {
				var type = jQuery( "input[name='homepagetype']:checked" ).val();
				if ('ml_home_article_list_enabled' == type) {
					jQuery( ".ml-list-enabled" ).show();
					jQuery( ".ml-list-disabled" ).hide();
				} else {
					jQuery( ".ml-list-enabled" ).hide();
					jQuery( ".ml-list-disabled" ).show();
				}
			}
		).trigger( 'change' );
		jQuery( "#ml_show_rating_prompt" ).on(
			'change',
			function() {
				if (jQuery( this ).is( ':checked' )) {
					jQuery( ".ml-rating-items" ).show();
				} else {
					jQuery( ".ml-rating-items" ).hide();
				}
			}
		).trigger( 'change' );
		jQuery( "#ml_article_list_show_excerpt" ).on(
			'change',
			function() {
				if (jQuery( this ).is( ':checked' )) {
					jQuery( ".show_excerpt_1" ).show();
				} else {
					jQuery( ".show_excerpt_1" ).hide();
				}
			}
		);
		jQuery( "#ml_cache_enabled" ).on(
			'change',
			function() {
				if (jQuery( this ).is( ':checked' )) {
					jQuery( ".ml-cache-items" ).show();
				} else {
					jQuery( ".ml-cache-items" ).hide();
				}
			}
		);
		jQuery( "#ml_cache_flush_button" ).on(
			'click',
			function() {
				if ( ! jQuery( this ).is( ':disabled' )) {
					jQuery( this ).attr( 'disabled', 'disabled' );
					jQuery( '#ml_flush_cache_spinner' ).show();
					var data = {
						action: 'ml_cache_flush',
						t: Math.random(),
						ml_nonce: jQuery( '#ml_nonce' ).val(),
					};
					jQuery.post(
						ajaxurl,
						data,
						function(response) {
							jQuery( "#ml_cache_flush_button" ).removeAttr( 'disabled' );
							jQuery( '#ml_flush_cache_spinner' ).hide();
							if ('OK' == response) {
								sweetAlert( 'Done', '', 'success' );
							} else {
								sweetAlert( 'Error', '', 'error' );
							}
						}
					);
				}

				return false;
			}
		);

		jQuery( '.ml-color .color-picker' ).wpColorPicker();

		var _custom_media     = true,
			_orig_send_attachment = wp.media.editor.send.attachment;

		jQuery( '#ml_default_featured_image_button' ).click(
			function(e) {
				var send_attachment_bkp         = wp.media.editor.send.attachment;
				var button                      = jQuery( this );
				var id                          = button.attr( 'id' ).replace( '_button', '' );
				_custom_media                   = true;
				wp.media.editor.send.attachment = function(props, attachment) {
					if (_custom_media) {
						jQuery( "#" + id ).val( attachment.url );
					} else {
						return _orig_send_attachment.apply( this, [props, attachment] );
					}

					loadDefaultPreviewImage( id );
				};

				wp.media.editor.open( button );
				return false;
			}
		);

		jQuery( "#ml_default_article_image .ml-preview-image-remove-btn" ).click(
			function(e) {
				e.preventDefault();
				var confirmRemove = confirm( 'Are you sure you want to remove the image?' );
				if ( confirmRemove ) {
					jQuery( this ).parents( '.ml-col-half' ).find( ".ml-preview-upload-image-row" ).hide();
					jQuery( this ).parents( '.ml-col-half' ).find( ".ml-preview-image-holder img" ).attr( 'src', '' );
					jQuery( this ).parents( '.ml-col-half' ).find( ".image-selector" ).val( '' );
				}
			}
		);

		var loadDefaultPreviewImage = function( id ) {
			var input = jQuery( "#" + id );
			if ( input.val().length > 0 ) {
				input.parents( '.ml-col-half' ).find( ".ml-preview-upload-image-row" ).show();
				input.parents( '.ml-col-half' ).find( ".ml-preview-image-holder img" ).attr( 'src', input.val() );
			} else {
				input.parents( '.ml-col-half' ).find( ".ml-preview-upload-image-row" ).hide();
			}
		};
	}
);
