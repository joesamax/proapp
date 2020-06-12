<?php

/**
 * The Sidebar containing the main widget areas.
 *
 * @package web-app
 */
global $post;

$post_class = 'sidebar-left';
if ( is_singular() && '' !== $post_class ){
	$post_class =  get_post_meta( $post->ID, 'web_app_sidebar_layout', true );
} else{
	$post_class = get_theme_mod('web_app_archive_setting_sidebar_option','sidebar-right');
}


if ( 'sidebar-no' == $post_class || ! is_active_sidebar( 'web-app-sidebar-left' ) ) {
	return;
}
if( $post_class=='sidebar-left' || $post_class=='sidebar-both' ){ ?>
	<div id="secondary" class="custom-col-4">

		<?php if ( is_active_sidebar( 'web-app-sidebar-left' ) ) :
			dynamic_sidebar( 'web-app-sidebar-left' ); 
		endif; ?>
	</div>
<?php    
}

