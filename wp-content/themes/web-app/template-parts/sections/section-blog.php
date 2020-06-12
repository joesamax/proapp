<?php

/**
* Blog Section
*
* @package web-app
*/

if (get_theme_mod( 'web_app_blog_option','no' )=='yes') {
    $blog_category = get_theme_mod( 'web_app_blog_section_cat');
    $blogreadmore_button = get_theme_mod( 'web_app_blog_readmore', esc_html__( 'Read More', 'web-app' ) );
    $number = get_theme_mod( 'web_app_blog_num',3 );
?>
    <section class="blog-section"> <!-- blog section starting from here -->
        <div class="container">

        <?php $section_title =  get_theme_mod('web_app_blog_title',esc_html__( 'Blog  Section Title','web-app') );
         if(!empty( $section_title ) ):    ?>
            <header class="entry-header heading">
                    <h2 class="entry-title"><?php echo esc_html( $section_title );?></h2>
            </header>
         <?php endif; ?>
         
        <?php
        if ( !empty( $blog_category) ) {
            $loop = new WP_Query(array('post_type'=>'post','posts_per_page'=>absint( $number ),'category_name'=>esc_html( $blog_category) ) );
        } else{
            $loop = new WP_Query( array( 'post_type'=>'post','posts_per_page'=>absint( $number ) ) );
        } 
        if($loop->have_posts()): ?>
            <div class="row">
                <?php
                while($loop->have_posts()) {
                $loop->the_post();
                $image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'web-app-blog-image', true );
                ?>
                <div class="custom-col-4">
                    <article class="post">
                        <figure class="post-featured-image">
                            <img src="<?php echo esc_url($image[0]);?>" />
                        </figure>
                        <header class="entry-header">
                            <h3 class="entry-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h3>
                        </header>
                        <div class="post-details">
                             <?php web_app_posted_on();?>  
                        </div>
                        <div class="entry-content">
                            <p> <?php echo esc_html(wp_trim_words(get_the_content(),25,'&hellip;')); ?></p>
                        </div>
						<?php if(!empty($blogreadmore_button)){ ?>
						    <a href="<?php the_permalink();?>" class="box-button"><?php echo esc_html( $blogreadmore_button );?><span></span></a>
						<?php } ?>
                    </article>
                </div>
                <?php 
                }
                wp_reset_postdata(); 
                ?>
            </div>
        <?php endif; 
        ?>
        </div>
    </section>
<?php }  ?>    