<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * Template Name: Home Page
 * @package web-app
 */

get_header(); ?>

        <div id="primary" class="content-area">

                <main id="main" class="site-main" role="main">

                	<?php

            	  	/** Feature Section **/
              	  	get_template_part( 'template-parts/sections/section', 'feature' );

              	  	/** Service Section **/
              	  	get_template_part( 'template-parts/sections/section', 'service' );

                    /** Call To Section **/
                    get_template_part( 'template-parts/sections/section', 'call-to' );

                    /** Testimonial Sections **/
                    get_template_part( 'template-parts/sections/section', 'testimonial' );

                    /** Blog Sections **/
                    get_template_part( 'template-parts/sections/section', 'blog' );

              	  ?>
                  
                
                </main><!-- #main -->
        </div><!-- #primary -->
        
<?php get_footer();?>