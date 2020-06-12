<?php

/**
* Service Section
*
* @package web-app
*/

if (get_theme_mod('web_app_service_option','no')=='yes') {  

	$service_page  = get_theme_mod('startup_business_service_page',0);

	?>
	<section class="service-section">

		<div class="container">

		<!-- *********************************************************** Service Page For Title And Subtitle  *****************-->

			<?php   if( !empty( $service_page ) ): 
				$args = array (                                 
					'page_id'           => absint( $service_page ),
					'post_status'       => 'publish',
					'post_type'         => 'page',
				);

				$loop = new WP_Query($args);

				if ( $loop->have_posts() ) : ?>

					<header class="entry-header heading">

						<?php while ($loop->have_posts()) : $loop->the_post();?> 

							<h2 class="entry-title"> <?php the_title(); ?> </h2>

							<p><?php echo esc_html(wp_trim_words(get_the_content(),60,'...')); ?></p>

						<?php endwhile; 
						wp_reset_postdata();?>

					</header>

				<?php endif; 

		  	endif; ?>

		  	<!-- *********************************************************** Starting  Service Post Category  **************************-->

			<?php 
			$service_category = get_theme_mod( 'web_app_service_section_cat',0 );
 			$number = get_theme_mod('web_app_service_num',5 );
			$args = array(
				'post_type'=>'post',
				'posts_per_page'=>absint( $number ),
			);	

			if( !empty( $service_category ) ){
				$args[ 'category_name' ] = esc_html( $service_category );
			} 
			$loop = new WP_Query( $args );

			?>

			<div class="service-item-wrapper">

				<?php $count = 0;
			 	while ($loop->have_posts()):
					$loop->the_post();
					$count++;
					?>

					<div class="service-item">

						<span class="service-number">
							<?php echo absint( $count ) ; ?>
						</span>
						<h2 class="entry-title">
						<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						</h2>
						<p><?php echo esc_html(wp_trim_words(get_the_content(),10,'...')); ?></p>
						
					</div>

				<?php
				endwhile;
				wp_reset_postdata();
				?>

			</div>

			<!-- *********************************************************** Starting Service Image Category  ************************-->

			<?php 
			$service_image_category = get_theme_mod( 'web_app_service_two_section_cat',0 );
 			$number = get_theme_mod('web_app_service_two_num',5 );
 			?>

 			<?php 
 			$args = array(
				'post_type'=>'post',
				'posts_per_page'=>absint( $number ),
			);	

			if( !empty( $service_image_category ) ){
				$args[ 'category_name' ] = esc_html( $service_image_category );
			} 

			$loop = new WP_Query( $args );
			?>

			<div class="service-slider-wrapper">

				<div class="owl-carousel owl-theme service-slider">	

					<?php 
				 	while ($loop->have_posts()):
						$loop->the_post();
						$service_image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full',true);
						//var_dump( $service_image );
						?>

						<div class="slider-content">

							<figure class="featured-image">
								<img src="<?php echo esc_url($service_image[0]);?>" />
							</figure>
							
						</div>

					<?php
					endwhile;
					wp_reset_postdata();
					?>
					
				</div>
				
			</div>

		</div>

	</section>

<?php }  ?>