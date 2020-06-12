<?php

if ( ! function_exists( 'setup_postdata_custom' ) ) {
	/**
	* Set up global post data.
	*
	* @since 1.5.0
	*
	* @param object $post Post data.
	*
	* @uses do_action_ref_array() Calls 'the_post'
	* @return bool True when finished.
	*/
	function setup_postdata_custom( $post ) {
		global $id, $authordata, $currentday, $currentmonth, $page, $pages, $multipage, $more, $numpages, $query;

		$id = (int) $post->ID;

		$authordata = get_userdata( $post->post_author );

		$currentday   = mysql2date( 'd.m.y', $post->post_date, false );
		$currentmonth = mysql2date( 'm', $post->post_date, false );
		$numpages     = 1;
		$multipage    = 0;
		$page         = get_query_var( 'page' );
		if ( ! $page ) {
			$page = 1;
		}
		if ( is_single() || is_page() || is_feed() ) {
			$more = 1;
		}
		$content = $post->post_content;
		$pages   = array( $post->post_content );

		/**
		* Fires once the post data has been setup.
		*
		* @since 2.8.0
		*
		* @param WP_Post &$post The Post object (passed by reference).
		*/
		do_action_ref_array( 'the_post', array( &$post, &$query ) );

		return true;
	}
}

if ( ! function_exists( 'ml_remove_shortcodes' ) ) {

	function ml_remove_shortcodes_callback( $param ) {
		global $shortcode_tags;
		// Be sure to keep active shortcodes
		$active_shortcodes = ( is_array( $shortcode_tags ) && ! empty( $shortcode_tags ) ) ? array_keys( $shortcode_tags ) : array();
		if ( in_array( $param[2], $active_shortcodes ) ) {
			return $param[0];
		} else {
			return '';
		}
	}

	// hide unused shortcodes
	function ml_remove_shortcodes( $content ) {
		global $shortcode_tags;

		if ( ! empty( $shortcode_tags ) ) {
			$patt    = "~(\[(?:\[?)/?([a-z0-9_\-]+)(?![\w-])([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?))~";
			$content = preg_replace_callback( $patt, 'ml_remove_shortcodes_callback', $content );
		} else {
			$hack    = md5( microtime() );
			$content = str_replace( '/', $hack, $content ); // avoid "/" chars in content breaks preg_replace
			// Strip all shortcodes
			$content = preg_replace( '~(?:\[/?)[^/\]]+/?\]~s', '', $content );
			$content = str_replace( $hack, '/', $content ); // set "/" back to its place
		}

		return ltrim( $content );
	}
}


if ( ! function_exists( 'ml_remove_elements' ) ) {

	function ml_remove_elements( $content ) {
		if ( strpos( $content, 'ml_remove' ) !== false ) {
			libxml_use_internal_errors();
			$d = new DOMDocument();
			$d->loadHTML( mb_convert_encoding( $content, 'HTML-ENTITIES', 'UTF-8' ), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD );
			$s = new DOMXPath( $d );

			foreach ( $s->query( '//div[contains(attribute::class, "ml_remove")]' ) as $t ) {
				$t->parentNode->removeChild( $t );
			}

			return preg_replace( '~<(?:!DOCTYPE|/?(?:html|head|body))[^>]*>\s*~i', '', html_entity_decode( $d->saveHTML() ) );
		} else {
			return $content;
		}
	}
}
$ml_related_posts_scripts = false;
if ( ! function_exists( 'ml_related_posts_js' ) ) {
	function ml_related_posts_js( $post_id ) {
		$related = array();
		if ( class_exists( 'Jetpack_RelatedPosts' ) ) {
?>
			<div id="ml_relatedposts" class="ml-relatedposts"></div>
<?php
			return true;
		};
		return false;
	}
}

$current_permalink = get_permalink( $post->ID );
if ( ! function_exists( 'ml_convert_relative_links' ) ) {
	function ml_convert_relative_links( $content ) {
		global $current_permalink;
		$content = preg_replace( "#(<\s*a\s+[^>]*href\s*=\s*[\"'])(?!http|/)([^\"'>]+)([\"'>]+)#", '$1' . $current_permalink . '$2$3', $content );

		return $content;
	}

	add_filter( 'the_content', 'ml_convert_relative_links', 20 );
}

if ( !function_exists( 'expanded_alowed_tags' ) ) {
	function expanded_alowed_tags() {
		$my_allowed = wp_kses_allowed_html( 'post' );
		// iframe
		$my_allowed['iframe'] = array(
			'src'             => array(),
			'height'          => array(),
			'width'           => array(),
			'frameborder'     => array(),
			'allowfullscreen' => array(),
		);
		// script
		$my_allowed['script'] = array(
			'type' => array(),
		);
		// form fields - input
		$my_allowed['input'] = array(
			'class' => array(),
			'id'    => array(),
			'name'  => array(),
			'value' => array(),
			'type'  => array(),
		);
		// select
		$my_allowed['select'] = array(
			'class' => array(),
			'id'    => array(),
			'name'  => array(),
			'value' => array(),
			'type'  => array(),
		);
		// select options
		$my_allowed['option'] = array(
			'selected' => array(),
		);
		// style
		$my_allowed['style'] = array(
			'types' => array(),
		);

		// style
		$my_allowed['noscript'] = array(
		);

		return $my_allowed;
	}
}

setup_postdata_custom( $post ); // enable author and other data

if ( ! isset( $custom_css ) ) {
	$custom_css = stripslashes( get_option( 'ml_post_custom_css' ) );
	echo $custom_css ? '<style type="text/css" media="screen">' . $custom_css . '</style>' : '';
}
if ( ! isset( $custom_js ) ) {
	$custom_js = stripslashes( get_option( 'ml_post_custom_js' ) );
	echo $custom_js ? '<script>' . $custom_js . '</script>' : '';
}

do_action( 'mobiloud_single_post', 'top' );

echo wp_kses( stripslashes( get_option( 'ml_banner_above_content', '' ) ), expanded_alowed_tags() );
/* Next line of code (with eval function) and other eval calls required for MobiLoud Editor settings */
eval( stripslashes( get_option( 'ml_post_start_body' ) ) );
echo wp_kses( stripslashes( get_option( 'ml_html_post_start_body', '' ) ), expanded_alowed_tags() );

eval( stripslashes( get_option( 'ml_post_before_top_banner' ) ) );

eval( stripslashes( get_option( 'ml_post_after_top_banner' ) ) );

?>
<article class="mb_article">
	<?php

	// Before post details
	do_action('mobiloud_single_extension', array( 'post' => $post, 'output' => 'before_details'  ) );
	eval( stripslashes( get_option( 'ml_post_before_details' ) ) );

	echo wp_kses( stripslashes( get_option( 'ml_html_post_before_details', '' ) ), expanded_alowed_tags() );

	$is_page = 'page' == $post->post_type;

	if ( ! $is_page ) {
		// title, date, author, meta
		echo get_option( 'ml_post_date_enabled' ) ? '<p class="mb_post_meta mb_post_date"><time title="' . esc_attr( $post->post_date ) . '">' . esc_html( date_i18n( get_option( 'date_format' ), strtotime( $post->post_date ) ) ) . '</time></p>' : '';

	} else {
		echo get_option( 'ml_page_date_enabled' ) ? '<p class="mb_post_meta mb_post_date"><time title="' . esc_attr( $post->post_date ). '">' . esc_html( date_i18n( get_option( 'date_format' ), strtotime( $post->post_date ) ) ) . '</time></p>' : '';
	}

	echo '<div class="mb_post_meta right">';
	eval( stripslashes( get_option( 'ml_post_right_of_date' ) ) );
	echo '</div>';
	?>
	<div class="mb_clear"></div>
	<?php echo wp_kses( stripslashes( get_option( 'ml_banner_above_title', '' ) ), expanded_alowed_tags() ); ?>

	<?php if ( ! $is_page ) : ?>
		<?php if ( get_option( 'ml_post_title_enabled' ) ) : ?>
			<h1 class="gamma mb_post_title"><?php echo esc_html( $post->post_title ); ?></h1>
		<?php endif; ?>
	<?php else : ?>
		<?php if ( get_option( 'ml_page_title_enabled' ) ) : ?>
			<h1 class="gamma mb_post_title"><?php echo esc_html( $post->post_title ); ?></h1>
		<?php endif; ?>
	<?php endif; ?>

	<?php
	$show_full_body = true;

	if ( 'web' == get_option( 'article_list_type', 'native' ) ) {
		$endpoint_url = trailingslashit( get_bloginfo( 'url' ) ) . 'ml-api/v2/list?author=' . get_the_author_meta( 'ID' );
		$author_link = '<p class="mb_post_meta mb_post_author"><a href="#" onclick="nativeFunctions.handleLink( \'' . $endpoint_url . '\', \'Posts by ' . get_the_author() . '\' )">' . get_the_author() . '</a></p>';
	} else {
		$author_link = '<p class="mb_post_meta mb_post_author">' . get_the_author() . '</p>';
	}

	if ( $show_full_body ) {
		if ( ! $is_page ) {
			echo get_option( 'ml_post_author_enabled' ) ? wp_kses( $author_link, expanded_alowed_tags() ) . '<div class="mb_clear"></div>' : ''; // clear because .post_meta is floated
		} else {
			echo get_option( 'ml_page_author_enabled' ) ? wp_kses( $author_link, expanded_alowed_tags() ) . '<div class="mb_clear"></div>' : ''; // clear because .post_meta is floated
		}
		// After post details
		do_action('mobiloud_single_extension', array( 'post' => $post, 'output' => 'after_details'  ) );
		eval( stripslashes( get_option( 'ml_post_after_details' ) ) );

		echo wp_kses( stripslashes( get_option( 'ml_html_post_after_details', '' ) ), expanded_alowed_tags() );

		eval( stripslashes( get_option( 'ml_post_before_content' ) ) );
		echo wp_kses( stripslashes( get_option( 'ml_html_post_before_content', '' ) ), expanded_alowed_tags() );
		do_action( 'mobiloud_single_post', 'before_content' );
		add_filter( 'the_content', 'ml_remove_elements', 70 );
		if ( Mobiloud::get_option( 'ml_remove_unused_shortcodes', true ) ) {
			add_filter( 'the_content', 'ml_remove_shortcodes', 0 );
		}
		add_filter( 'get_the_excerpt', 'ml_remove_shortcodes', 9999 );

		// Featured image banner
		do_action('mobiloud_single_extension', array( 'post' => $post, 'output' => 'before_content'  ) );


		// content

		echo '<div class="ml_post_content">';

		global $more;
		$more = 1;
		the_content();

		$embedCode = get_post_meta( $post->ID, '_video_embed_code' );

		if ( ! empty( $embedCode ) ) {
			if ( isset( $embedCode[0] ) ) {
				echo wp_kses( $embedCode[0], expanded_alowed_tags() );
			}
		}

		$embedUrl = get_post_meta( $post->ID, '_video_url' );
		if ( ! empty( $embedUrl ) ) {
			if ( isset( $embedUrl[0] ) ) {
				global $wp_embed;
				$video = $wp_embed->run_shortcode( '[embed]' . $embedUrl[0] . '[/embed]' );
				echo wp_kses( $video, expanded_alowed_tags() );
			}
		}

		echo '</div>';

		// Featured image banner
		do_action('mobiloud_single_extension', array( 'post' => $post, 'output' => 'after_content'  ) );
		eval( stripslashes( get_option( 'ml_post_after_content' ) ) );
		echo wp_kses( stripslashes( get_option( 'ml_html_post_after_content', '' ) ), expanded_alowed_tags() );

		if ( Mobiloud::get_option( 'ml_related_posts' ) ) {
			$ml_related_posts_scripts = ml_related_posts_js( $post->ID );
		}

		eval( stripslashes( get_option( 'ml_post_before_footer_banner' ) ) );

		eval( stripslashes( get_option( 'ml_post_after_footer_banner' ) ) );

	}
	?>
</article>
<?php

eval( stripslashes( get_option( 'ml_post_after_body' ) ) );
echo wp_kses( stripslashes( get_option( 'ml_html_post_after_body', '' ) ), expanded_alowed_tags() );
echo wp_kses( stripslashes( get_option( 'ml_banner_below_content', '' ) ), expanded_alowed_tags() );
do_action( 'mobiloud_single_post', 'bottom' );

?>
<script>
	var iframes = document.getElementsByTagName('iframe')
	, frameRatios = []
	, container = document.getElementsByTagName('article')[0]
	, imgs = document.getElementsByTagName('img');
	for (var i = 0; i < iframes.length; i++) {
		var frame = iframes[i];
		frameRatios[i] = frame.offsetHeight / frame.offsetWidth;
		frame.removeAttribute('width');
		frame.removeAttribute('height');
		frame.style.width = '100%';
	}
	for (var i = 0; i < imgs.length; i++) {
		var img = imgs[i];
		img.removeAttribute('width');
		img.removeAttribute('height');
		while (img = img.parentNode) {
			if (/^attachment_[0-9]+$/.test(img.id)) {
				img.removeAttribute('style');
			}
		}
	}
	window.onresize = function () {
		var containerWidth = container.offsetWidth;
		for (var i = 0; i < iframes.length; i++) {
			var frame = iframes[i];
			frame.style.height = (containerWidth * frameRatios[i]) + 'px';
		}
	};
	window.onresize();
</script>
