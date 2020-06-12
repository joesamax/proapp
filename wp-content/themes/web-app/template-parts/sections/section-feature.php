<?php

/**
* Feature  Section
*
* @package web-app
*/

	if (get_theme_mod('web_app_feature_option','no')=='yes') {  ?>

		<?php
		$feature_category = ( get_theme_mod( 'web_app_feature_section_cat',0 ) );
        $number = get_theme_mod('web_app_feature_num',3 );		?>		

		<section class="featured-section">

			<?php if ( !empty( $feature_category) ) {

			$loop = new WP_Query(array('post_type'=>'post','posts_per_page'=>absint( $number ),'category_name'=>esc_html( $feature_category ) ) );
			} else{
			$loop = new WP_Query( array( 'post_type'=>'post','posts_per_page'=>absint( $number ), ) );
			} ?>

			<div class="container">
				
				<div class="featured-section-item-wrapper">

					<?php while ($loop->have_posts()):
					$loop->the_post();
					$feature_image = wp_get_attachment_image_src(get_post_thumbnail_id(), 'web-app-feature-thumb',true);
					?>

					<div class="featured-section-item">

						<!-- ******************************** Feature Section  Image  ************-->

						<figure class="featured-image">
							<img src="<?php echo esc_url($feature_image[0]);?>" />
						</figure>

						<div class="entry-content">

							<!-- ******************************** Feature Section  Title ************-->

							<h2 class="entry-title">

								<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a> 

							</h2>

							<!-- ******************************** Feature Descriptions ************-->

							<p><?php echo esc_html(wp_trim_words(get_the_content(),30,'...')); ?></p>

						</div>
						
					</div>

					<?php
					endwhile;
					wp_reset_postdata();
					?>
					
				</div>
				
			</div>

		</section>

	<?php }  ?>