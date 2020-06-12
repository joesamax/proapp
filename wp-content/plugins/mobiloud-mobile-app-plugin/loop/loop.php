<?php
$debug = false;

// Define custom styles for the list
$styles = array(
	'font-family-body' => '"Open Sans", Arial, sans-serif',
	'font-family-heading' => '"Montserrat", Georgia, serif',
	'font-size-body' => '1rem',
	'font-size-heading' => '18px',
	'color-text-body' => '#000',
	'color-text-heading' => '#000',
	'font-family-meta' => '"Open Sans", "Arial", sans-serif',
	'font-size-meta' => '14px',
	'link-color' => '#ED3725',
);

flush();

?><!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, minimum-scale=1, maximum-scale=1, user-scalable=no">

	<?php

	function ml_list_stylesheets() {
		wp_enqueue_style( 'onsenui', plugins_url( 'mobiloud-mobile-app-plugin/libs/onsen/css/onsenui.min.css' ) );
		wp_enqueue_style( 'onsen-components', plugins_url( 'mobiloud-mobile-app-plugin/libs/onsen/css/onsen-css-components.min.css' ) );
		wp_enqueue_style( 'mobiloud-list', plugins_url( 'mobiloud-mobile-app-plugin/loop/css/loop.css' ) );
	}

	function ml_loop_scripts() {
		wp_enqueue_script( 'onsenui', plugins_url( 'mobiloud-mobile-app-plugin/libs/onsen/js/onsenui.min.js' ), array(), false, true );

		// Filter to allow overriding of loop.js file (article list)
		$list_js_override = apply_filters( 'mobiloud_list_js_custom', null );
		if ( ! empty( $list_js_override ) ) {
			$loop_js_url = $list_js_override;
		} else {
			$loop_js_url = plugins_url( 'mobiloud-mobile-app-plugin/loop/js/loop.js' );
		}

		wp_enqueue_script( 'mobiloud-list', esc_url( $loop_js_url ), array( 'onsenui' ), false, true );
	}

	remove_all_actions( 'wp_head' );
	remove_all_actions( 'wp_footer' );
	remove_all_actions( 'wp_print_styles' );
	remove_all_actions( 'wp_enqueue_scripts' );
	remove_all_actions( 'locale_stylesheet' );
	remove_all_actions( 'wp_print_head_scripts' );
	remove_all_actions( 'wp_print_footer_scripts' );
	remove_all_actions( 'wp_shortlink_wp_head' );

	add_action( 'wp_print_styles', 'ml_list_stylesheets' );

	add_action( 'wp_head', 'wp_print_styles' );
	add_action( 'wp_print_footer_scripts', 'ml_loop_scripts', 300 );
	add_action( 'wp_print_footer_scripts', '_wp_footer_scripts', 300 );
	add_action( 'wp_footer', 'wp_print_footer_scripts', 200 );

	wp_head();
	?>

	<style type="text/css">

		body,
		body p,
		.article-list__content p {
			font-family: <?php echo $styles['font-family-body']; ?>;
			font-size: <?php echo $styles['font-size-body']; ?>;
			color: <?php echo $styles['color-text-body']; ?>;
		}
		.article-list__content h2 {
			font-size: <?php echo $styles['font-size-heading']; ?>;
			font-family: <?php echo $styles['font-family-heading']; ?>;
			color: <?php echo $styles['color-text-heading']; ?>;
		}
		.article-list__meta {
			font-family: <?php echo $styles['font-family-meta']; ?>;
			font-size: <?php echo $styles['font-size-meta']; ?>;
		}
		.article-list__meta a,
		a {
			color: <?php echo $styles['link-color']; ?>;
		}
	</style>

	<?php
	$custom_css = stripslashes( get_option( 'ml_post_custom_css' ) );
	echo $custom_css ? '<style type="text/css" media="screen">' . $custom_css . '</style>' : '';

	$custom_js = stripslashes( get_option( 'ml_post_custom_js' ) );
	echo $custom_js ? '<script>' . $custom_js . '</script>' : '';
	?>

</head>

<?php $body_class = 'ml-article-list';

if ( isset( $_GET['search'] ) ) {
	$body_class .= ' ml-search-results';
} elseif ( isset( $_GET['post_ids'] ) ) {
	$body_class .= ' ml-favorites-list';
} elseif ( isset( $_GET['post_type'] ) ) {
	$body_class .= ' ml-custom-type';
} else {
	$body_class .= ' ml-regular-list';
}

?>

<body class="<?php echo $body_class; ?>">

<?php do_action( 'mobiloud_before_content_requests' ); ?>

<ons-page id="load-more-page">

	<ons-pull-hook id="pull-hook">
	</ons-pull-hook>

	<ons-list id="article-list" class="article-list">

		<ons-list-item class="article-list__article is-placeholder">
			<div class="article-list__wrap">
				<figure class="article-list__thumb"></figure>
				<div class="article-list__content">
					<h2></h2>
					<p></p>
				</div>
			</div>
			<div class="article-list__meta">
				<a></a> <span class="author"></span>
			</div>
		</ons-list-item>

		<ons-list-item class="article-list__article is-placeholder">
			<div class="article-list__wrap">
				<figure class="article-list__thumb"></figure>
				<div class="article-list__content">
					<h2></h2>
					<p></p>
				</div>
			</div>
			<div class="article-list__meta">
				<a></a> <span class="author"></span>
			</div>
		</ons-list-item>

		<ons-list-item class="article-list__article is-placeholder">
			<div class="article-list__wrap">
				<figure class="article-list__thumb"></figure>
				<div class="article-list__content">
					<h2></h2>
					<p></p>
				</div>
			</div>
			<div class="article-list__meta">
				<a></a> <span class="author"></span>
			</div>
		</ons-list-item>

		<ons-list-item class="article-list__article is-placeholder">
			<div class="article-list__wrap">
				<figure class="article-list__thumb"></figure>
				<div class="article-list__content">
					<h2></h2>
					<p></p>
				</div>
			</div>
			<div class="article-list__meta">
				<a></a> <span class="author"></span>
			</div>
		</ons-list-item>

		<ons-list-item class="article-list__article is-placeholder">
			<div class="article-list__wrap">
				<figure class="article-list__thumb"></figure>
				<div class="article-list__content">
					<h2></h2>
					<p></p>
				</div>
			</div>
			<div class="article-list__meta">
				<a></a> <span class="author"></span>
			</div>
		</ons-list-item>

		<ons-list-item class="article-list__article is-placeholder">
			<div class="article-list__wrap">
				<figure class="article-list__thumb"></figure>
				<div class="article-list__content">
					<h2></h2>
					<p></p>
				</div>
			</div>
			<div class="article-list__meta">
				<a></a> <span class="author"></span>
			</div>
		</ons-list-item>

		<ons-list-item class="article-list__article is-placeholder">
			<div class="article-list__wrap">
				<figure class="article-list__thumb"></figure>
				<div class="article-list__content">
					<h2></h2>
					<p></p>
				</div>
			</div>
			<div class="article-list__meta">
				<a></a> <span class="author"></span>
			</div>
		</ons-list-item>

		<ons-list-item class="article-list__article is-placeholder">
			<div class="article-list__wrap">
				<figure class="article-list__thumb"></figure>
				<div class="article-list__content">
					<h2></h2>
					<p></p>
				</div>
			</div>
			<div class="article-list__meta">
				<a></a> <span class="author"></span>
			</div>
		</ons-list-item>


		<?php if ( function_exists( 'mobiloud_list_top' ) ) {
			mobiloud_list_top( $_GET );
		}
		?>

	</ons-list>

	<ons-progress-circular id="loading-more" indeterminate></ons-progress-circular>

</ons-page>

<?php

// Get list of articles json
$response = '{}';
if ( class_exists( 'MLApiController' ) ) {
	$api = new MLApiController();
	$api->set_error_handlers( $debug );

	$custom_response = apply_filters( 'mobiloud_custom_list_results', null );

	if ( ! empty( $custom_response ) ) {
		$response = $custom_response;
	} else {
		$response = $api->handle_request();
	}
}

$data = $response;

add_action( 'wp_print_footer_scripts', 'ml_loop_scripts', 300 );
add_action( 'wp_print_footer_scripts', '_wp_footer_scripts', 300 );
add_action( 'wp_footer', 'wp_print_footer_scripts', 200 );

do_action( 'wp_footer' );
?>

<script type="text/javascript">
	var mlPostsData = <?php echo $data; ?>;
	var mlFirstData = <?php echo $data; ?>;
	var loaded = mlPostsData['posts'].length;
	var rendered = 0;
	var defaultThumb = '<?php echo get_option( 'ml_default_featured_image', 'http://placehold.it/800x450' ); ?>';
	var siteURL = '<?php echo esc_url( trailingslashit( get_bloginfo( 'url' ) ) ); ?>';

	var noMorePosts = false;


	document.addEventListener( "DOMContentLoaded", function( event ) {

		if ( loaded === 0 ) {
			document.querySelector("body").innerHTML = '<h3 style="margin: 20px;">No posts found.</h3>';
		}

		if (!document.getElementById('article-list').classList.contains('rendered')) {
			renderList(mlPostsData);
		}

		document.querySelector("body").dispatchEvent(new Event('scroll'));

		var page = document.getElementById('load-more-page');

		page.onInfiniteScroll = function (done) {
			if (!noMorePosts) {
				noMorePosts = true;
				getNewArticles(siteURL, loaded, '<?php echo $_SERVER["QUERY_STRING"]; ?>')
					.then(function (more) {
						if (more === 0) {
							noMorePosts = true;
						} else {
							noMorePosts = false;
						}
						loaded += more;
						document.getElementById('loading-more').style.display = 'none';
						done(); // Important!
					});
			} else {
				// end of posts animation / effect
				document.getElementById('loading-more').style.display = 'none';
			}
		};

		// Pull hook
		var pullHook = document.getElementById('pull-hook');

		if (ons.platform.isIOS()) {
			pullHook.classList.add('ios');
		} else {
			pullHook.classList.add('android');
		}

		pullHook.addEventListener('changestate', function (event) {
			var message = '';

			switch (event.state) {
				case 'initial':
					message = 'Pull to refresh';
					break;
				case 'preaction':
					message = 'Release';
					break;
				case 'action':
					message = 'Loading...';
					break;
			}

			pullHook.innerHTML = message;
		});

		pullHook.onAction = function (done) {
			nativeFunctions.reloadWebview();
			setTimeout(done, 3000);
		};

	} );
</script>

<?php
// embed any custom JS using this action
do_action( 'mobiloud_custom_list_scripts' );
?>
</body>
</html>
