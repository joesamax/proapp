<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package web-app
 */

?>

	</div><!-- #content -->


    <footer id="colophon" class="site-footer" style="background-image: url('<?php echo esc_url(get_theme_mod('web_app_footer_bg_image','')); ?>') ">

    	<?php if(is_active_sidebar('web-app-top-footer')): ?> 

			<div class="footer-address-info">

	            <div class="container">
	                <div class="footer-address-wrap">
						<?php dynamic_sidebar('web-app-top-footer'); ?>	
	                </div>
	            </div>

	        </div>

         <?php endif;?>

		<div class="widget-area" >

			<?php if(is_active_sidebar('web-app-footer')): ?>

				<div class="container">

					<div class="row">	
			
						<?php dynamic_sidebar('web-app-footer'); ?>
							
					</div>

				</div>

			<?php endif;?>

			<div class="site-generator">

				<div class="container">

					<span class="copy-right">

						<span class="copyright-text"><?php echo esc_html( get_theme_mod( 'web_app_copyright_text',esc_html__('')));?></span>
							<?php 						 

								printf( esc_html__( '', 'web-app' ), '<a href="'.esc_url( 'https://theme404.com/' ).'" rel="designer">'.esc_html__('', 'web-app').'</a>' ); ?>
							
						</span>
						
				</div>	

			</div><!-- .site-info -->

		</div>

	</footer><!-- #colophon -->

	 <div class="back-to-top">
            <a href="#masthead" title="Go to Top" class="fa-angle-up"></a>
     </div>

</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
