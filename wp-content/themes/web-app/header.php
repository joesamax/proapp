<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package web-app
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<div id="page" class="site">
	
	

	<header id="masthead" class="site-header">

		<!-- header starting from here -->

		<div class="hgroup-wrap">

			<div class="container">


				<!-- ******************************************************* Header Site Identity   *****************-->

				<section class="site-branding">
					
					
					<?php $site_identity = get_theme_mod( 'site_identity_options', 'title-text' );
					$title = get_bloginfo( 'name', 'display' );
					$description    = get_bloginfo( 'description', 'display' );	

					if ( 'logo-only' == $site_identity ) { 
						if ( has_custom_logo() ){
						the_custom_logo();
						}
					} elseif ( 'logo-text' == $site_identity ) {
						if ( has_custom_logo() ) {
							the_custom_logo();
						}
						if ( $description ) {
							echo '<p class="site-description">'.esc_attr( $description ).'</p>';
						}
					} elseif ( 'title-only' == $site_identity && $title ) {

						if ( is_front_page() && is_home() ) { ?>
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a>
						<?php } else { ?>
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a>
						<?php }

					} elseif ( 'title-text' == $site_identity ) {
						if ( $title ) {
							if ( is_front_page() && is_home() ) { ?>
							<h1 class="site-title">
								<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a>
							</h1>
							<?php } else { ?>
							<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a>
							<?php }
						}
						if ( $description ) {
						echo '<p class="site-description">'.esc_attr( $description ).'</p>';	
						}
					}
					?>
					
				</section>

				<!-- ******************************************************* Header Menu  *****************-->

				<div class="hgroup-right">

					<div id="navbar" class="navbar">

						<!-- navbar starting from here -->

						<nav id="site-navigation" class="navigation main-navigation">

							<div class="menu-content-wrapper">
								<?php
								wp_nav_menu( array(
									'theme_location' => 'menu-1',

									) );
								?>
							</div>
							
						</nav><!-- #site-navigation -->

					</div>		

				
					<!-- ******************************************************* Header Search  *****************-->

					<?php  $header_fetaure = get_theme_mod('web_app_header_feature');  

					if ( 'header-search' ==  $header_fetaure ){ ?>

				 	<?php if (get_theme_mod('web_app_search_option','no')=='yes') {  ?> 

						<div id="left-search" class="search-container">
							<div class="search-toggle">
							</div>
							<div class="search-section">
								<?php get_search_form();?>
								<span class="search-arrow"></span>
							</div>
						</div>

					<?php } ?>
					<?php } ?>
				</div>
			</div>

		</div>

	 <!-- *************************** Slider And Breadcrumb Section  *****************-->

	<?php
	if( !is_home() && is_front_page()){

		do_action('web_app_slider_callback_action');
		
	} else{
		 do_action('web_app_title'); 
	} ?>

	<!-- ***********************************************************************************-->
		
	</header><!-- #masthead -->

	<div id="content" class="site-content">
