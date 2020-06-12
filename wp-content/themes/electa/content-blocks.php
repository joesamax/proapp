<?php
/**
 * The template used for displaying page content as blocks
 *
 * @package Electa
 */
$has_img = ' post-no-img';
if ( has_post_thumbnail() ) {
    $has_img = '';
    $thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'blog_standard_img' );
} ?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'electa-blocks-post' . $has_img ); ?><?php echo ( has_post_thumbnail() ) ? ' style="background-image: url(' . esc_url( $thumbnail[0] ) . ');"' : ''; ?>>
    <a href="<?php echo esc_url( get_permalink() ); ?>" class="electa-blocks-post-a">
    	<header class="entry-header">
    		<?php the_title( '<h3 class="entry-title">', '</h3>' ); ?>
    	</header><!-- .entry-header -->
    </a>
    <img src="<?php echo get_template_directory_uri(); ?>/images/blank_img.png" alt="<?php the_title(); ?>" />
</article><!-- #post-## -->
