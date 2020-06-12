<?php
if ( ! function_exists( 'ml_related_posts_json' ) ) {
	function ml_jetpack_image_size_json( $thumbnail_size ) {
		return ( empty( $_GET['thumb_size'] ) ) ? 192 : absint( $_GET['thumb_size'] );
	}
	add_filter( 'jetpack_relatedposts_filter_thumbnail_size', 'ml_jetpack_image_size_json' );

	function ml_jetpack_relatedposts_filter_options_json( $options ) {
		$options['size'] = 3;
		return $options;
	}
	add_action( 'jetpack_relatedposts_filter_options', 'ml_jetpack_relatedposts_filter_options_json' );

	function ml_related_posts_json( $post_id ) {
		$related = array();
		try {
			if ( class_exists( 'Jetpack_RelatedPosts' ) ) {
				$class = Jetpack_RelatedPosts::init();

				if ( method_exists( $class, 'get_for_post_id' ) ) {
					$related = $class->get_for_post_id( $post_id, array() );
				}
			}
		} catch ( Exception $e ) {
		}
		$related = apply_filters( 'ml_related_posts_data', $related, $post_id );
		return $related;
	}

	function ml_related_posts( $post_id ) {
		$related = array();

		if ( class_exists( 'Jetpack_RelatedPosts' ) ) {
			$class = Jetpack_RelatedPosts::init();

			if ( method_exists( $class, 'get_for_post_id' ) ) {
				$related = $class->get_for_post_id( $post_id, array() );
			}
		}

		$related = apply_filters( 'ml_related_posts_data', $related, $post_id );

		if ( ! empty( $related ) ) {
			$options = apply_filters(
				'ml_related_posts_options',
				array(
					'header'  => trim( Mobiloud::get_option( 'ml_related_header', '' ) ),
					'image'   => Mobiloud::get_option( 'ml_related_image' ),
					'excerpt' => Mobiloud::get_option( 'ml_related_excerpt' ),
					'date'    => Mobiloud::get_option( 'ml_related_date' ),
				),
				$post_id
			);
			if ( ! empty( $options['header'] ) ) {
				?><h3 class="ml-relatedposts-header"><?php echo esc_html( $options['header'] ); ?></h3>
												<?php
			}
			?>
			<div class="ml-relatedposts-list">
			<?php
			foreach ( $related as $item ) {
				$href       = esc_attr( $item['url'] );
				$related_id = $item['id'];
				$ml_href    = esc_attr( get_site_url() . '/ml-api/v1/post/?post_id=' . $related_id );
				?>
					<a class="ml-relatedposts-a" href="<?php echo esc_attr( $href ); ?>" data-ml_post_id="<?php echo esc_attr( $related_id ); ?>" data-ml_href="<?php echo esc_attr( $ml_href ); ?>">
						<div class="ml-relatedposts-post">
						<?php
						if ( ! empty( $options['image'] ) && ! empty( $item['img'] ) && ! empty( $item['img']['src'] ) ) {
							?>
						<span class="ml-relatedposts-img ml_followlinks" style="background-image: url(<?php echo esc_attr( $item['img']['src'] ); ?>);
									height: <?php echo esc_attr( $item['img']['height'] ); ?>px; display: block; ">
								</span>
										<?php
						}
						?>
							<h4 class="ml-relatedposts-title"><?php echo esc_html( $item['title'] ); ?></h4>
										<?php
										if ( ! empty( $options['excerpt'] ) && ! empty( $item['excerpt'] ) ) {
											?>
							<p class="ml-relatedposts-excerpt"><?php echo esc_html( $item['excerpt'] ); ?></p>
											<?php
										}
										if ( ! empty( $options['date'] ) && ! empty( $item['date'] ) ) {
											?>
							<p class="ml-relatedposts-date"><?php echo esc_html( $item['date'] ); ?></p>
											<?php
										}
										?>
						</div>
				</a>
							<?php
			}
			?>
			</div>
			<?php
		};
	}
}
$result = array();
if ( Mobiloud::get_option( 'ml_related_posts' ) ) {
	if ( isset( $_GET['related_posts'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		header( 'Content-Type: application/json' );
		$time = absint( Mobiloud::get_option( 'ml_cache_expiration', 30 ) ) * 60;
		header( "Cache-Control: public, max-age=$time, s-max-age=$time", true );

		$result = ml_related_posts_json( $post->ID );
		echo wp_json_encode( $result );
	} else {
		ml_related_posts( $post->ID );
	}
}
die();
