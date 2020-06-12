<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package web-app
 */

get_header();
$image = get_theme_mod('web_app_404_bg_image','');
?>

<div id="content" class="site-content">

    <div id="primary" class="content-area">

        <main id="main" class="site-main">

	            <div class="container">

	                <div class="error-404 not-found"> 
	                	<div class="entry-content">

		                	<?php if( !empty( $image ) ) { ?>

		                        
		                            <p> <?php esc_html__('oops....','web-app')?></p>

		                            <h2 class="page-title">
		                                <span> <?php  echo esc_html__('4','web-app') ?> </span>	                                
		                                	<span class="error-icon">
			                                    <img src="<?php echo esc_url( $image );?>" alt="">
			                                </span>	                            	
		                                <span> <?php echo esc_html__('4','web-app') ?> </span>
		                            </h2>	                            
		                        

	                        	<?php } else{ ?>

	 								<p> <?php esc_html__('oops....','web-app')?></p>

		                            <h2 class="page-title">
		                                <span> <?php echo absint(4) ?> </span>
		                               <span><?php echo absint(0) ?> </span>
		                                <span><?php echo absint(4) ?> </span>
		                            </h2>

		                           
	                       	 <?php } ?> 

                       	 		<p><?php echo esc_html__('error','web-app')?></p>
	                            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="box-button"><?php echo esc_html__('back to home','web-app') ?><span></span></a>
	                    </div>	        

	                </div>

	            </div>

        </main>
    </div>

</div>

<?php
get_footer();