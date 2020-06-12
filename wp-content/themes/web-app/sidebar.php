<?php
/**
 * The sidebar containing the main widget area
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package web-app
 */

global $post;

$post_class = 'sidebar-right';
if ( is_singular() && '' !== $post_class ){
	$post_class =  get_post_meta( $post->ID, 'web_app_sidebar_layout', true );
} else{
	$post_class = get_theme_mod('web_app_archive_setting_sidebar_option','sidebar-right');
}


if ( 'sidebar-no' == $post_class || ! is_active_sidebar( 'web-app-sidebar-right' ) ) {
	return;
}



if($post_class=='sidebar-right' || $post_class=='sidebar-both'){
	?>
	<div id="secondary" class="custom-col-4 "> <!-- secondary starting from here -->
		<?php dynamic_sidebar( 'web-app-sidebar-right' );   ?>
	</div>

<?php } ?>




