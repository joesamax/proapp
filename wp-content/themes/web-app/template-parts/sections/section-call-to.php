<?php

/**
* Call To Section
*
* @package web-app
*/

	if (get_theme_mod('web_app_call_option','no')=='yes') {  ?>

		<section class="call-to-action-section" style="background-image: url('<?php echo esc_url(get_theme_mod('web_app_call_to_image','')); ?>') ";>

			<div class="container">

				<div class="call-to-action-item-wrapper">

					<?php
					$call_to_title_one = get_theme_mod('web_app_call_to_title_one'); ?>

					
					<!-- *********************************** Call To Page First  ****************************************************-->

					<div class="call-to-action-item">

						<div class="call-action-number">
							<span>
								<?php if(!empty( $call_to_title_one ) ):  ?>
									<?php echo esc_html( $call_to_title_one );?>
								<?php endif; ?>
							</span>
						</div>

						<?php $call_to_page  = get_theme_mod('web_app_call_to_page'); ?>

						<div class="entry-content">

							<?php   if( !empty( $call_to_page ) ): 

							$args = array (                                 
								'page_id'           => absint( $call_to_page ),
								'post_status'       => 'publish',
								'post_type'         => 'page',
							);

							$loop = new WP_Query($args);
							if ( $loop->have_posts() ) : ?>	
						
								<div class="call-to-wrapper">

									<?php while ($loop->have_posts()) : $loop->the_post();?>

										<h2 class="entry-title">
											<?php the_title(); ?>
										</h2>
										 <p><?php echo esc_html(wp_trim_words(get_the_content(),60,'...')); ?></p>

									 <?php endwhile; 
						 		   wp_reset_postdata();?>

								</div>

							<?php endif;
              
             				 endif;

     					 ?> 

						</div>

					</div>


					<?php $call_to_title_two = get_theme_mod('web_app_call_to_title_two'); ?>


					<!-- *********************************** Call To Page Second  ****************************************************-->


					<div class="call-to-action-item">

						<div class="call-action-number">
							<span>
								<?php if(!empty( $call_to_title_two ) ):  ?>
									<?php echo esc_html( $call_to_title_two );?>
								<?php endif; ?>
							</span>
						</div>

						<?php $call_to_page_two  = get_theme_mod('web_app_call_to_page_two'); ?>

						<div class="entry-content">

							<?php   if( !empty( $call_to_page_two ) ): 

							$args = array (                                 
							'page_id'           => absint( $call_to_page_two ),
							'post_status'       => 'publish',
							'post_type'         => 'page',
							);

							$loop = new WP_Query($args);

							if ( $loop->have_posts() ) : ?>

							<div class="call-to-wrapper">

								<?php while ($loop->have_posts()) : $loop->the_post();?>

								<h2 class="entry-title">
										<?php the_title(); ?>
								</h2>

								 <p><?php echo esc_html(wp_trim_words(get_the_content(),60,'...')); ?></p>

							</div>

							 <?php endwhile; 
					 		   wp_reset_postdata();?>

							<?php endif;
              
             				 endif;

     						 ?> 
	
						</div>

					</div>


				</div>

			</div>

		</section>

	<?php }  ?>