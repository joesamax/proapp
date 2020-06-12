var ml_class_ok     = 'button button-hero button-primary';
var ml_class_cancel = 'button button-hero button-primary button-grey';

function ml_alert(message, icon) {
	if ( ! icon) {
		icon = '';
	}
	swal(
		{
			title: message,
			icon: icon,
			buttons: {
				ok: {
					className: ml_class_ok
				},
			}
		}
	);
}

jQuery( document ).ready(
	function ($) {
		$.validator.addMethod(
			'subscription_id',
			function (value) {
				return /^cus.+$/.test( value );
			},
			'Please enter a valid Subscription ID'
		);

		// First step
		if ($( '#ml-initial-details' ).length) {
			// Form Validation
			$( '.contact-form' ).validate(
				{
					rules: {
						website: {
							url: true
						},
						message: {
							maxlength: 100
						}
					},
					messages: {
						name: {
							required: 'Please enter your name'
						},
						email: {
							required: 'Please enter your email'
						},
						website: {
							required: 'Please enter your website\'s address'
						},
						company_name: {
							required: 'Please enter your company or site name'
						},
						phone: {
							required: 'Please enter your phone'
						}
					},
					errorPlacement: function (error, element) {
						var elParent = element.parent();
						if (elParent.hasClass( 'checkbox_lbl' )) {
							elParent.append( error );
						} else {
							error.insertAfter( element );
						}
					}
				}
			);

			var ladda = Ladda.create( document.querySelector( '.ladda-button' ) );

			$( '.contact-form' ).submit(
				function (e) {
					e.preventDefault();
					if ($( '#submit' ).prop( 'disabled' )) {
						return;
					}
					if ($( this ).valid()) {
						var ml_name       = jQuery( "#pname" ).val();
						var ml_email      = jQuery( "#pemail" ).val();
						var ml_site       = jQuery( "#psite" ).val();
						var ml_company    = jQuery( "#pcompany_name" ).val();
						var ml_phone      = jQuery( "#pphone" ).val();
						var ml_message    = jQuery( "#pmessage" ).val();
						var ml_apptype    = jQuery( 'input[name="type"]:checked' ).val();
						var ml_pricing    = jQuery( '#pricing' ).is( ':checked' ) ? 1 : 0;
						var ml_plan       = jQuery( '#plan' ).val();
						var ml_accept     = jQuery( '#accept' ).is( ':checked' ) ? 1 : 0;
						var ml_newsletter = jQuery( '#newsletter' ).is( ':checked' ) ? 1 : 0;

						if (ml_name.length <= 0 || ml_email.length <= 0 || ml_site.length <= 0 || ml_company.length <= 0 || ml_phone.length <= 0 || ! ml_accept) {
							ml_alert( 'Please complete all details' );
							return false;
						} else {
							var data = {
								action: "ml_welcome",
								ml_name: ml_name,
								ml_email: ml_email,
								ml_site: ml_site,
								ml_apptype: ml_apptype,
								ml_company: ml_company,
								ml_phone: ml_phone,
								ml_message: ml_message,
								ml_plan: ml_plan,
								ml_pricing: ml_pricing,
								ml_newsletter: ml_newsletter,
								ml_intercom: (typeof(window.intercomSettings) != 'undefined' ? 1 : 0),
								ml_nonce: jQuery( '#ml_nonce' ).val(),
							};
							$( '#submit' ).prop( 'disabled', true );
							ladda.start();
							jQuery.post(
								ajaxurl,
								data,
								function (response) {
									if (response && response.success && response.data && response.data.url ) {
										window.location = response.data.url;
									} else {
										ladda.stop();
										$( '#submit' ).removeProp( 'disabled' )
										ml_alert( 'Error. Please try again later.', 'error' );
									}
								}
							);
						}
						return true;
					};
				}
			);
		}
		// Second step
		if ($( '#submit_price' ).length) {
			$( '#submit_price' ).on(
				'click',
				function() {
					window.location = 'https://www.mobiloud.com/pricing/?utm_source=news-plugin&utm_medium=welcome-screen-pricing';
				}
			)
		}

		// Welcome question
		if ($( 'div#ml_question' ).length) {
			$( '.welcome_question_demo' ).on(
				'click',
				function() {
					swal(
						{
							title: '',
							text: $( this ).data( 'text' ),
							buttons: {
								cancel2: {
									text: "Cancel",
									value: "cancel",
									className: ml_class_cancel,
								},
								yes: {
									text: "Yes",
									value: "yes",
									className: ml_class_ok,
								},
							},
						}
					)
					.then(
						(value) => {
						if ('yes' == value) {
								window.location = $( '.welcome_question_demo' ).first().data( 'href' );
							}
						}
					);
					return false;
				}
			)
		}
		$( '.welcome_question_start' ).on(
			'click',
			function() {
				window.location = $( this ).data( 'href' );
				return false;
			}
		)
	}
);
