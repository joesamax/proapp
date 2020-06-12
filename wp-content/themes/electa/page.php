<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package Electa
 */

get_header(); ?>

	<div id="primary" class="content-area home-loop-columns-four <?php echo ( ( is_front_page() ) && ( get_theme_mod( 'kra-home-blocks-layout' ) == 1 ) ) ? sanitize_html_class( 'content-area-full' ) : ''; ?>">
		<main id="main" class="site-main" role="main">
            
            <?php if ( ( is_front_page() ) && ( get_theme_mod( 'kra-home-blocks-layout' ) == 1 ) ) : ?>
            
                <?php
                $home_cats_set = '';
                if ( get_theme_mod( 'kra-home-cats', false ) ) {
                    $home_cats_set = 'cat=' . get_theme_mod( 'kra-home-cats' ) . '';
                } else {
                    $home_cats_set = 'post_type=post';
                }

                $home_query = new WP_Query( $home_cats_set . '&posts_per_page=-1' ); ?>
                
                <?php if ( $home_query->have_posts() ) : ?>
                
                    <div class="blocks-wrap blocks-wrap-remove">

        			    <?php while ( $home_query->have_posts() ) : $home_query->the_post(); ?>
                    
            				<?php get_template_part( 'content', 'blocks' ); ?>
                            
        			    <?php endwhile; // end of the loop. ?>
                        
                    </div>
                    <div class="clearboth"></div>
                    
                    <?php electa_paging_nav(); ?>
                    
                    <?php wp_reset_query(); ?>
                        
                <?php else : ?>

                    <?php get_template_part( 'content', 'none' ); ?>

                <?php endif;  ?>
            
            <?php else : ?>
            
                <?php while ( have_posts() ) : the_post(); ?>
            
                    <?php get_template_part( 'content', 'page' ); ?>

                    <?php
                        // If comments are open or we have at least one comment, load up the comment template
                        if ( comments_open() || '0' != get_comments_number() ) :
                            comments_template();
                        endif;
                    ?>

                <?php endwhile; // end of the loop. ?>
            
            <?php endif; ?>
            
		</main><!-- #main -->
	</div><!-- #primary -->
    
    <?php if ( ( is_front_page() ) && ( get_theme_mod( 'kra-home-blocks-layout' ) == 1 ) ) : ?>
    
        <!-- Do Nothing -->
    
    <?php else : ?>
    
        <?php get_sidebar(); ?>
    
    <?php endif; ?>
    
<?php get_footer(); ?>
