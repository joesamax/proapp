<?php
/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package web-app
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function web_app_body_classes( $classes ) {
	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	// Adds a class of no-sidebar when there is no sidebar present.
	if ( ! is_active_sidebar( 'web-app-sidebar-right' )  && ! is_active_sidebar( 'web-app-sidebar-left' ) ) {
		$classes[] = 'no-sidebar';
	}
	global $post;

	if ( is_singular() ){
		
		$page_id = get_post_meta( $post->ID , 'web_app_sidebar_layout', true ) ;
		if(is_front_page()){
			$post_id = get_option('page_on_front');
		}elseif( empty( $page_id ) ){
			
			$classes[] = 'sidebar-right';
		}else{
			$classes[] = get_post_meta( $post->ID , 'web_app_sidebar_layout', true ) ;
		}
		} else{
		$classes[] = get_theme_mod('web_app_archive_setting_sidebar_option','sidebar-right');
	}

	$classes[] = get_theme_mod('web_app_weblayout','fullwidth');

	return $classes;
}
add_filter( 'body_class', 'web_app_body_classes' );
/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
 */
function web_app_pingback_header() {
	if ( is_singular() && pings_open() ) {
		echo '<link rel="pingback" href="', esc_url( get_bloginfo( 'pingback_url' ) ), '">';
	}
}
add_action( 'wp_head', 'web_app_pingback_header' );
