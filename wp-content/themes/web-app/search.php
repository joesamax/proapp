<?php
/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package web-app
 */

get_header();
$sidebar = get_theme_mod('web_app_archive_setting_sidebar_option','sidebar-right');
?>

<div class="container">	

	<div class="row">
		<?php 
			$custom_class = 'custom-col-8';
			if( 'sidebar-no' == $sidebar ){
				$custom_class = 'custom-col-12';
			} elseif ( 'sidebar-both' == $sidebar ) {
				$custom_class = 'custom-col-4';
			}else{
				$custom_class = 'custom-col-8';
			}
			if($sidebar=='sidebar-both' || $sidebar=='sidebar-left'){
				get_sidebar('left');
			}
		?> 		

		<div id="primary" class="content-area  <?php echo esc_attr( $custom_class );?>">

			<main id="main" class="site-main">

		
                    <div class="entry-content">

						<?php if ( have_posts() ) : ?>

							<header class="page-header">
								<h1 class="page-title">
									<?php
									/* translators: %s: search query. */
									printf( esc_html__( 'Search Results for: %s', 'web-app' ), '<span>' . get_search_query() . '</span>' );
									?>
								</h1>
							</header><!-- .page-header -->

							<?php
							/* Start the Loop */
							while ( have_posts() ) :
								the_post();

								/**
								 * Run the loop for the search to output the results.
								 * If you want to overload this in a child theme then include a file
								 * called content-search.php and that will be used instead.
								 */
								get_template_part( 'template-parts/content', 'search' );

							endwhile;

							the_posts_navigation();

						else :

							get_template_part( 'template-parts/content', 'none' );

						endif;
						?>

				</div>	

			</main><!-- #main -->
			
		</div><!-- #primary -->
		<?php get_sidebar( ) ?>

	</div>

</div>

<?php get_footer(); ?>
