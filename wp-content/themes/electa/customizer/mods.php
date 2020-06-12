<?php
/**
 * Functions used to implement options
 *
 * @package Customizer Library Kaira
 */

/**
 * Enqueue Google Fonts Example
 */
function kaira_theme_fonts() {

	// Font options
	$fonts = array(
		get_theme_mod( 'kra-body-font', customizer_library_get_default( 'kra-body-font' ) ),
		get_theme_mod( 'kra-heading-font', customizer_library_get_default( 'kra-heading-font' ) )
	);

	$font_uri = customizer_library_get_google_font_uri( $fonts );

	// Load Google Fonts
	wp_enqueue_style( 'kaira_theme_fonts', $font_uri, array(), null, 'screen' );

}
add_action( 'wp_enqueue_scripts', 'kaira_theme_fonts' );