<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @package Electa
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			<section class="error-404 not-found">
				<header class="page-header">
                    <h1 class="page-title"><?php echo wp_kses_post( get_theme_mod( 'kra-website-error-head', __( 'Oops! That page can\'t be found.', 'electa' ) ) ); ?></h1>
                    
                    <?php if ( function_exists( 'bcn_display' ) ) : ?>
                        <div class="cx-breadcrumbs">
                            <?php bcn_display(); ?>
                        </div>
                    <?php endif; ?>
                    
				</header><!-- .page-header -->
                
                <i class="fa fa-ban"></i>
                
                <p><?php echo wp_kses_post( get_theme_mod( 'kra-website-error-msg', __( 'The page you are looking for can\'t be found. Please select one of the options below.', 'electa' ) ) ); ?></p>
                
                <div class="not-found-options">
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="electa-button"><?php _e( 'Return Home', 'electa' ); ?></a>
                    
                    <?php if ( get_theme_mod( 'kra-header-search', false ) ) : ?>
                        <a class="electa-button search-btn"><?php _e( 'Search', 'electa' ); ?></a>
                    <?php endif; ?>
                    
                </div>
                
			</section><!-- .error-404 -->

		</main><!-- #main -->
	</div><!-- #primary -->
    
    <?php get_sidebar(); ?>

<?php get_footer(); ?>
