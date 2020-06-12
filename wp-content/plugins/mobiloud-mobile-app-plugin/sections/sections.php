<?php
ini_set( 'display_errors', 0 );

$debug = false;

flush();

// add custom menu fields to menu
function ml_menu_add_custom_nav_fields( $menu_item ) {

	$menu_item->opening_method = get_post_meta( $menu_item->ID, '_ml_menu_item_opening_method', true );
	return $menu_item;

}
add_filter( 'wp_setup_nav_menu_item', 'ml_menu_add_custom_nav_fields' );

function getMenuItemsForParent($menuSlug, $parentId) {
	$args = [
		'post_type' => 'nav_menu_item',
		'meta_key' => '_menu_item_menu_item_parent',
		'meta_value' => $parentId,
		'tax_query' => [
			[
				'taxonomy' => 'nav_menu',
				'field' => 'slug',
				'terms' => [$menuSlug]
			]
		],
		'order' => 'ASC',
		'orderby' => 'menu_order',
	];
	$tmpItems = query_posts($args);

	$items = [];
	foreach ( $tmpItems as $tmpItem ) {
		$item = new stdClass;
		$type = get_post_meta($tmpItem->ID, '_menu_item_type', true);
		$object = get_post_meta($tmpItem->ID, '_menu_item_object', true);
		$object_id = get_post_meta($tmpItem->ID, '_menu_item_object_id', true);
		$opening_method = get_post_meta($tmpItem->ID, '_ml_menu_item_opening_method', true);

		if ( empty( $opening_method ) ) {
			$opening_method = 'native';
		}

		switch($type):

			case 'post_type':
				$postId = get_post_meta( $tmpItem->ID, '_menu_item_object_id', true );
				$post = get_post($postId);
				$item->name = $post->post_title;
				$item->url = get_permalink( $postId );
				break;

			case 'taxonomy':
				$catID = get_post_meta( $tmpItem->ID, '_menu_item_object_id', true );
				$tax = get_post_meta( $tmpItem->ID, '_menu_item_object', true );
				$cat = get_term( $catID, $tax );
				$item->name = $cat->name;
				break;

			case 'custom':
				$item->name = $tmpItem->post_title;
				$item->url = get_post_meta($tmpItem->ID, '_menu_item_url', true);
				break;
		endswitch;
		$item->type = $type;
		$item->object = $object;
		$item->object_id = $object_id;
		$item->opening_method = $opening_method;
		$item->children = getMenuItemsForParent($menuSlug, $tmpItem->ID);
		$items[] = $item;
	}

	return $items;
}

// Get Sections menu
$sections_menu = Mobiloud::get_option( 'ml_sections_menu' );

?><!DOCTYPE html>
<html>
<head>

	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, minimum-scale=1, maximum-scale=1, user-scalable=no">

	<?php

	function ml_sections_stylesheets() {
		wp_enqueue_style( 'mobiloud-typeplate', plugins_url( 'mobiloud-mobile-app-plugin/sections/css/_typeplate.css' ) );
		wp_enqueue_style( 'onsenui', plugins_url( 'mobiloud-mobile-app-plugin/libs/onsen/css/onsenui.min.css' ) );
		wp_enqueue_style( 'onsen-components', plugins_url( 'mobiloud-mobile-app-plugin/libs/onsen/css/onsen-css-components.min.css' ) );
		wp_enqueue_style( 'mobiloud-sections', plugins_url( 'mobiloud-mobile-app-plugin/sections/css/sections.css' ) );
	}

	function ml_sections_scripts() {
		wp_enqueue_script( 'onsenui', plugins_url( 'mobiloud-mobile-app-plugin/libs/onsen/js/onsenui.min.js' ), array(), false, true );
	}

	remove_all_actions( 'wp_head' );
	remove_all_actions( 'wp_footer' );
	remove_all_actions( 'wp_print_styles' );
	remove_all_actions( 'wp_enqueue_scripts' );
	remove_all_actions( 'locale_stylesheet' );
	remove_all_actions( 'wp_print_head_scripts' );
	remove_all_actions( 'wp_print_footer_scripts' );
	remove_all_actions( 'wp_shortlink_wp_head' );

	add_action( 'wp_print_styles', 'ml_sections_stylesheets' );
	add_action( 'wp_print_footer_scripts', 'ml_sections_scripts', 30 );
	add_action( 'wp_print_footer_scripts', '_wp_footer_scripts', 30 );

	add_action( 'wp_head', 'wp_print_styles' );
	add_action( 'wp_footer', 'wp_print_footer_scripts', 20 );

	wp_head();


	$custom_css = stripslashes( get_option( 'ml_post_custom_css' ) );
	echo $custom_css ? '<style type="text/css" media="screen">' . $custom_css . '</style>' : '';
	?>
</head>
<body>

<ons-page id="load-more-page">

	<ons-list id="sections-placeholder" class="sections-menu">
		<?php
		for ( $i = 0; $i<=18; $i++ ) {
			echo '<ons-list-item class="is-placeholder"></ons-list-item>';
		}
		?>
	</ons-list>

	<?php
	if ( ! empty( $sections_menu ) ) {
		$menu = wp_get_nav_menu_items( $sections_menu );
		?>

		<ons-list id="sections-menu" class="sections-menu">

			<?php

			do_action( 'mobiloud_above_sections' );

			$article_list = ( get_option( 'ml_list_type', 'native' ) === 'native' ) ? 'posts' : 'list';

			foreach( $menu as $item ) {
				if ( $item->menu_item_parent !== '0' ) {
					continue;
				}

				$children = getMenuItemsForParent( $sections_menu, $item->ID );

				$item_data = ' data-ml-item-type="' . $item->type . '"';
				$item_data .= ' data-ml-item-object="' . $item->object . '"';
				$item_data .= ' data-ml-object-id="' . $item->object_id . '"';

				if ( empty( $item->opening_method ) ) {
					$item->opening_method = 'native';
				}

				$onclick = 'nativeFunctions.handlePost( ' . $item->object_id . ' )';

				if ( $item->type === 'post_type' && $item->opening_method !== 'native' ) {
					$onclick = "nativeFunctions.handleLink( '" . $item->url . "', '" . $item->title . "', '" . $item->opening_method . "' )";
				}

				if ( $item->type === 'taxonomy' ) {
					$onclick = "nativeFunctions.handleLink( '" . trailingslashit( get_bloginfo( 'url' ) ) . 'ml-api/v2/' . $article_list . '?taxonomy=' . $item->object . '&term_id=' . $item->object_id . "', '" . $item->title . "', '" . $item->opening_method . "' )";
				}

				if ( $item->type === 'custom' ) {
					if ( $item->url !== '#' ) {
						$onclick = "nativeFunctions.handleLink( '" . $item->url . "', '" . $item->title . "', '" . $item->opening_method . "' )";
					} else {
						$onclick = '';
					}
				}

				?>

				<ons-list-item tappable <?php if ( ! empty( $children ) ) { echo 'expandable' . wp_kses_post( $item_data ); } else { echo ' data-click="' . esc_attr( $onclick ) . '"' . wp_kses_post( $item_data ); } ?>>
					<?php echo esc_html( $item->title ); ?>
					<?php
					if ( ! empty( $children ) ) {

						echo '<div class="expandable-content">';

						foreach( $children as $child ) {

							$item_data = ' data-ml-item-type="' . $child->type . '"';
							$item_data .= ' data-ml-item-object="' . $child->object . '"';
							$item_data .= ' data-ml-object-id="' . $child->object_id . '"';

							$onclick = 'nativeFunctions.handlePost( ' . $child->object_id . ' )';

							if ( $child->type === 'post_type' && $child->opening_method !== 'native' ) {
								$onclick = "nativeFunctions.handleLink( '" . $child->url . "', '" . $child->name . "', '" . $child->opening_method . "' )";
							}

							if ( $child->type === 'taxonomy' ) {
								$onclick = "nativeFunctions.handleLink( '" . trailingslashit( get_bloginfo( 'url' ) ) . 'ml-api/v2/' . $article_list . '?taxonomy=' . $child->object . '&term_id=' . $child->object_id . "', '" . $child->name . "', '" . $child->opening_method . "' )";
							}

							if ( $child->type === 'custom' ) {
								if ( $child->url !== '#' ) {
									$onclick = "nativeFunctions.handleLink( '" . $child->url . "', '" . $child->title . "', '" . $child->opening_method . "' )";
								} else {
									$onclick = '';
								}
							}

							?>

							<ons-list-item data-click="<?php echo $onclick; ?>" tappable <?php echo $item_data; if ( ! empty( $child->children ) ) { echo 'expandable'; } ?>  >
								<?php echo $child->name;

								if ( ! empty( $child->children ) ) {

									echo '<div class="expandable-content">';

									foreach ( $child->children as $child ) {

										$item_data = ' data-ml-item-type="' . $child->type . '"';
										$item_data .= ' data-ml-item-object="' . $child->object . '"';
										$item_data .= ' data-ml-object-id="' . $child->object_id . '"';

										$onclick = 'nativeFunctions.handlePost( ' . $child->object_id . ' )';

										if ( $child->type === 'post_type' && $child->opening_method !== 'native' ) {
											$onclick = "nativeFunctions.handleLink( '" . $child->url . "', '" . $child->name . "', '" . $child->opening_method . "' )";
										}

										if ( $child->type === 'taxonomy' ) {
											$onclick = "nativeFunctions.handleLink( '" . trailingslashit( get_bloginfo( 'url' ) ) . 'ml-api/v2/' . $article_list . '?taxonomy=' . $child->object . '&term_id=' . $child->object_id . "', '" . $child->name . "', '" . $child->opening_method . "' )";
										}

										if ( $child->type === 'custom' ) {
											if ( $child->url !== '#' ) {
												$onclick = "nativeFunctions.handleLink( '" . $child->url . "', '" . $child->title . "', '" . $child->opening_method . "' )";
											} else {
												$onclick = '';
											}
										}

										?>

										<ons-list-item data-click="<?php echo esc_attr( $onclick ); ?>"
													   tappable <?php echo wp_kses_post( $item_data ); ?> >
											<?php echo esc_html( $child->name ); ?>
										</ons-list-item>

										<?php

									}

									echo '</div>';
								}
								?>

							</ons-list-item>

							<?php

						}

						echo '</div>';
						?>


					<?php } ?>
				</ons-list-item>

				<?php
			}

			do_action( 'mobiloud_below_sections' );

			?>
		</ons-list>

		<?php
	} else {
		echo 'No menu selected for Sections';
	}
	?>

</ons-page>

<?php wp_footer(); ?>

<script data-cfasync="false">

	document.addEventListener("DOMContentLoaded", function(event) {

		document.querySelectorAll( 'ons-list#sections-placeholder' ).forEach( e => e.parentNode.removeChild( e ) );

		document.querySelectorAll( 'ons-list-item' ).forEach( function( item ) {
			item.addEventListener( 'click', function( e )  {
				e.stopPropagation();
				if ( e.target.classList.contains( 'list-item__right' ) || e.target.parentNode.classList.contains( 'list-item__right' ) ) {
					// clicked on dropdown arrow, do nothing
					return false;
				} else {
					// run the data-click expression
					eval( item.getAttribute( 'data-click' ) );
				}
			} );
		});

	} );

</script>

<?php
// embed any custom JS using this action
do_action( 'mobiloud_custom_sections_scripts' );
?>

</body>
</html>
