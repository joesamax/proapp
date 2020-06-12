<?php

/**
* Testimonial Section
*
* @package web-app
*/

if (get_theme_mod('web_app_testimonial_option','no')=='yes') {  
	$testimonial_category = get_theme_mod( 'web_app_testimonial_section_cat',0 );
	$number = get_theme_mod('web_app_testimonial_num',5 );
	$wep_app_title = get_theme_mod('web_app_test_title','');
	?>


	<!-- *************************************************** Testimonial Section  ***************************************************-->

    <section class="testimonial-section" style="background-image: url('<?php echo esc_url(get_theme_mod('web_app_test_bg_image','')); ?>') ";>
        <div class="container">
        	
            <header class="entry-header heading">
				<?php if(!empty( $wep_app_title ) ):  ?>
					<h2 class="entry-title">	<?php echo esc_html( $wep_app_title );?> </h2>
				<?php endif; ?>
            </header>

            <div class="owl-carousel owl-theme testimonial-slider">

            	<?php 
	 			$args = array(
					'post_type'=>'post',
					'posts_per_page'=>absint( $number ),
				);	

				if( !empty( $testimonial_category ) ){
					$args[ 'category_name' ] = esc_html( $testimonial_category );
				} 

				$loop = new WP_Query( $args ); ?>
                	<?php while ($loop->have_posts()):
						$loop->the_post();
						$image = wp_get_attachment_image_src(get_post_thumbnail_id(), 'web-app-testimonial-thumb',true);
					?>

					<div class="testimonial-item">

	                    <div class="author-intro">

	                    	 <!-- ******************************** Starting Feature Section Image  ************-->

	                        <figure class="author-img">
	                            <img src="<?php echo esc_url($image[0]);?>" />
	                        </figure>

	                         <!-- ******************************** Starting  Feature Section Title  ************-->

	                        <span class="author-name"> <?php the_title(); ?> </span>

	                        <!-- ******************************** Starting Feature Section Review  ************-->

		                       <?php   if (get_theme_mod('web_app_testimonial_review_option','no')=='yes') {  ?>

			                        <div class="testimonial-rating">
			                            <span>
			                                <i class="fa fa-star"></i>
			                                <i class="fa fa-star"></i>
			                                <i class="fa fa-star"></i>
			                                <i class="fa fa-star"></i>
			                                <i class="fa fa-star"></i>
			                            </span>
			                        </div>

		                        <?php }  ?>

	                         	<?php   if (get_theme_mod('web_app_testimonial_quote_option','no')=='yes') {  ?>

			                        <span class="right-testmonial-quote">
			                            <i class="fa fa-quote-right"></i>
			                        </span>
			                        <span class="left-testmonial-quote">
			                            <i class="fa fa-quote-left"></i>
			                        </span>

		                        <?php }  ?>
	                        
	                    </div>

	                    <!-- ******************************** Feature Section Content  ************-->

	                    <div class="entry-content">

	                       <p><?php echo esc_html(wp_trim_words(get_the_content(),60,'...')); ?></p>
	                       
	                    </div>

					</div>	                    
                  
                     <?php endwhile; 
					wp_reset_postdata();?>
                

            </div>
        </div>
    </section>

<?php }  ?>	