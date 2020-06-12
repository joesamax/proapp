<?php
/**
 * @package Electa
 */

$postclass = '';
if ( ! has_post_thumbnail() )
    $postclass = sanitize_html_class( 'post-no-thumbnail' );

if ( has_post_thumbnail() )
    $thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'blog_standard_img' );
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( $postclass ); ?>>
    
    <div class="post-left">
    	<header class="entry-header pc-text">
    		<?php the_title( sprintf( '<h1 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h1>' ); ?>

    		<?php if ( 'post' == get_post_type() ) : ?>
    		<div class="entry-meta">
    			<?php electa_posted_on(); ?>
    		</div><!-- .entry-meta -->
    		<?php endif; ?>
    	</header><!-- .entry-header -->

    	<div class="entry-content">
    		<?php the_excerpt(); ?>

    		<?php
    			wp_link_pages( array(
    				'before' => '<div class="page-links">' . __( 'Pages:', 'electa' ),
    				'after'  => '</div>',
    			) );
    		?>
    	</div><!-- .entry-content -->

    	<footer class="entry-footer">
    		<?php electa_entry_footer(); ?>
    	</footer><!-- .entry-footer -->
    </div>
    
    <?php if ( has_post_thumbnail() ) : ?>
    <div class="post-right">
        <img src="<?php echo esc_url( $thumbnail[0] ); ?>" alt="<?php the_title(); ?>" />
    </div>
    <?php endif; ?>
    <div class="clearboth"></div>
    
</article><!-- #post-## -->