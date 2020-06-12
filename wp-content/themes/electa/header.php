<?php
/**
 * @package Electa
 */
?><!DOCTYPE html><!-- Electa.ORG -->
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
<?php wp_head(); ?>
</head>

<body <?php body_class(  ); ?>>
<div id="page">

<header id="masthead" class="site-header <?php echo ( get_header_image() ) ? '' : sanitize_html_class( 'header-nologo' ); ?>" role="banner">
    <?php if ( get_theme_mod( 'kra-header-search', false ) ) : ?>
    <div class="search-button">
        <div class="search-block">
            <div class="search-block-arrow"></div>
            <?php get_search_form(); ?>
        </div>
    </div>
    <?php endif; ?>
    
    <?php do_action ( 'electa_hook_before_branding' ); ?>
    
    <div class="site-header-inner">
    	<div class="site-branding">
            <?php if ( get_header_image() ) : ?>
                <a href="<?php echo esc_url(home_url('/')); ?>" class="site-logo-img" title="<?php echo esc_attr( get_bloginfo('name', 'display') ); ?>" rel="home"><img src="<?php esc_url( header_image() ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ) ?>" /></a>
                <?php if ( get_theme_mod( 'kra-header-slogan', false ) ) : ?>
                    <h2 class="site-description"><?php bloginfo( 'description' ); ?></h2>
                <?php endif; ?>
            <?php else : ?>
                <h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
                <?php if ( get_theme_mod( 'kra-header-slogan', false ) ) : ?>
                    <h2 class="site-description"><?php bloginfo( 'description' ); ?></h2>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        
        <?php if ( get_theme_mod( 'kra-header-social-above-nav', customizer_library_get_default( 'kra-header-social-above-nav' ) ) ) : ?>
            <?php get_template_part( '/inc/social-links' ); ?>
        <?php endif; ?>
        
        <?php do_action ( 'electa_hook_before_navigation' ); ?>
        
        <nav id="site-navigation" class="main-navigation" role="navigation">
            <span class="header-menu-button"><i class="fa fa-bars"></i><span><?php _e( 'Menu', 'electa' ); ?></span></span>
            <div id="main-menu" class="main-menu-container">
                <span class="main-menu-close"><i class="fa fa-angle-right"></i><i class="fa fa-angle-left"></i></span>
                <?php wp_nav_menu( array( 'theme_location' => 'primary' ) ); ?>
            </div>
        </nav> <!-- #site-navigation -->
        
        <?php do_action ( 'electa_hook_after_navigation' ); ?>
        
        <?php if ( !get_theme_mod( 'kra-header-social-above-nav', customizer_library_get_default( 'kra-header-social-above-nav' ) ) ) : ?>
            <?php get_template_part( '/inc/social-links' ); ?>
        <?php endif; ?>
        
        
    </div>
    
    <?php do_action ( 'electa_hook_after_in_header' ); ?>
</header><!-- #masthead -->

<div id="content" class="site-content">