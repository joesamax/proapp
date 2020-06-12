<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package web-app
 */
$blog_section_button_text = esc_html(get_theme_mod('web_app_archive_submit',esc_html__('Read More','web-app')));
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php if (has_post_thumbnail()): ?>
		<figure>
			<a href="<?php the_permalink();?>">
			<?php the_post_thumbnail( );?>
			</a>
		</figure>
	<?php endif; ?>

	<header class="entry-header">

		<?php
		if ( is_singular() ) :
			the_title( '<h2 class="entry-title">', '</h2>' );
			else :
			the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
			endif;
			?>
			<div class="entry-meta">
				<?php if (get_theme_mod('web_app_section_date','no')=='yes') {  ?>
					<?php web_app_posted_on();?>
				<?php }?>   
			</div><!-- .entry-meta -->

	</header><!-- .entry-header -->


	<div class="entry-content">
            <?php
            if ( is_single() ) :
                the_content();
            else:
                the_excerpt();
                ?>
                <?php if($blog_section_button_text){ ?>
                    <a href="<?php the_permalink(); ?>" class="box-button"><?php echo esc_html($blog_section_button_text); ?><span></span></a>
                  <?php } ?>  
               <?php
        endif;?>
    </div>


	
</article><!-- #post-<?php the_ID(); ?> -->
