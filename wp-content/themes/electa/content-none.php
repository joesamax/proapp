<?php
/**
 * The template part for displaying a message that posts cannot be found.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Electa
 */ ?>
<section class="no-results not-found">
	<header class="page-header">
		<h1 class="page-title"><?php _e( 'Nothing Found', 'electa' ); ?></h1>
	</header><!-- .page-header -->

	<div class="page-content">
		<?php if ( is_home() && current_user_can( 'publish_posts' ) ) : ?>

			<p><?php printf( __( 'Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'electa' ), esc_url( admin_url( 'post-new.php' ) ) ); ?></p>

		<?php elseif ( is_search() ) : ?>

			<p><?php echo wp_kses_post( get_theme_mod( 'kra-website-nosearch-msg', __( 'Sorry, but nothing matched your search terms. Please try again with some different keywords or return to home.', 'electa' ) ) ); ?></p>
            
			<div class="not-found-options">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="electa-button"><?php _e( 'Return Home', 'electa' ); ?></a>
                
                <?php if ( get_theme_mod( 'kra-header-search', false ) ) : ?>
                    <a class="electa-button search-btn"><?php _e( 'Search', 'electa' ); ?></a>
                <?php endif; ?>
                
            </div>

		<?php else : ?>

			<p><?php echo wp_kses_post( get_theme_mod( 'kra-website-nosearch-msg', __( 'Sorry, but nothing matched your search terms. Please try again with some different keywords or return to home.', 'electa' ) ) ); ?></p>
            
			<div class="not-found-options">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="electa-button"><?php _e( 'Return Home', 'electa' ); ?></a>
                
                <?php if ( get_theme_mod( 'kra-header-search', false ) ) : ?>
                    <a class="electa-button search-btn"><?php _e( 'Search', 'electa' ); ?></a>
                <?php endif; ?>
                
            </div>

		<?php endif; ?>
	</div><!-- .page-content -->
</section><!-- .no-results -->
