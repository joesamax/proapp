<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// fix broken by rsssl json at articles list
if ( Mobiloud::get_option( 'ml_fix_rsssl' ) && ! function_exists( 'ml_rsssl_comment_remover' ) ) {
	function ml_rsssl_comment_remover( $buffer ) {
		// replace the comment with empty string
		$buffer = str_replace( 'data-rsssl="1"', '', $buffer );
		return $buffer;
	}
	add_filter( 'rsssl_fixer_output', 'ml_rsssl_comment_remover', 10, 1 );
}
?>
<!DOCTYPE html>
<html dir="<?php echo( get_option( 'ml_rtl_text_enable' ) == '1' ? 'rtl' : 'ltr' ); ?>">
<head>
	<meta charset="utf-8">
	<meta name="description" content="">
	<meta name="keywords" content="">
	<meta name="language" content="en"/>
	<meta name="viewport" content="width=device-width, minimum-scale=1, maximum-scale=1, user-scalable=no">

	<style type="text/css">
		@charset "UTF-8";
		/*!
		TYPEPLATE
		URL ........... http://typeplate.com
		VERSION ....... 1.1.3
		Github ........ https://github.com/typeplate/bower
		AUTHORS ....... Dennis Gaebel (@gryghostvisuals) & Zachary Kain (@zakkain)
		LICENSE ....... Creative Commmons Attribution 3.0 (http://creativecommons.org/licenses/by/3.0)
		LICENSE URL ... https://github.com/typeplate/bower/blob/master/license.txt
		*/
		@font-face {
			font-family: "Ampersand";
			src: local("Georgia"), local("Garamond"), local("Palatino"), local("Book Antiqua");
			unicode-range: U+0026; }
		@font-face {
			font-family: "Ampersand";
			src: local("Georgia");
			unicode-range: U+270C; }
		body {
			word-wrap: break-word; }

		pre code {
			word-wrap: normal; }

		/**
		 * Dropcap Sass @include
		 * Use the following Sass @include with any selector you feel necessary.
		 *
			@include dropcap($dropcap-float-position, $dropcap-font-size, $dropcap-font-family, $dropcap-txt-indent, $dropcap-margin, $dropcap-padding, $dropcap-color, $dropcap-line-height, $dropcap-bg);
		 *
		 * Extend this object into your custom stylesheet. Let the variables do the work.
		 *
		 */
		html {
			font: normal 100%/1.65 serif; }

		body {
			-webkit-hyphens: auto;
			-moz-hyphens: auto;
			-ms-hyphens: auto;
			-o-hyphens: auto;
			hyphens: auto;
			color: #444444; }

		small {
			font-size: 65%; }

		h1, h2, h3, h4, h5, h6 {
			text-rendering: optimizeLegibility;
			line-height: 1;
			margin-top: 0; }

		.tera {
			font-size: 117px;
			font-size: 7.3125rem;
			margin-bottom: 0.22564px;
			margin-bottom: 0.22564rem; }

		.giga {
			font-size: 90px;
			font-size: 5.625rem;
			margin-bottom: 0.29333px;
			margin-bottom: 0.29333rem; }

		.mega {
			font-size: 72px;
			font-size: 4.5rem;
			margin-bottom: 0.36667px;
			margin-bottom: 0.36667rem; }

		.alpha, h1 {
			font-size: 24px;
			font-size: 1.5rem;
			margin-bottom: 1.1px;
			margin-bottom: 1.1rem; }

		.beta, h2 {
			font-size: 22px;
			font-size: 1.375rem;
			margin-bottom: 1.2px;
			margin-bottom: 1.2rem; }

		.gamma, h3 {
			font-size: 20px;
			font-size: 1.25rem;
			margin-bottom: 1.32px;
			margin-bottom: 1.32rem; }

		.delta, h4 {
			font-size: 19px;
			font-size: 1.1875rem;
			margin-bottom: 1.38947px;
			margin-bottom: 1.38947rem; }

		.epsilon, h5 {
			font-size: 18.5px;
			font-size: 1.15625rem;
			margin-bottom: 1.42703px;
			margin-bottom: 1.42703rem; }

		.zeta, h6 {
			font-size: 18px;
			font-size: 1.125rem;
			margin-bottom: 1.46667px;
			margin-bottom: 1.46667rem; }

		p {
			margin: auto auto 1.5em; }

		abbr,
		acronym,
		blockquote,
		code,
		dir,
		kbd,
		listing,
		plaintext,
		q,
		samp,
		tt,
		var,
		xmp {
			-webkit-hyphens: none;
			-moz-hyphens: none;
			-ms-hyphens: none;
			-o-hyphens: none;
			hyphens: none; }

		pre code {
			white-space: -moz-pre-wrap;
			white-space: pre-wrap; }

		pre {
			white-space: pre; }

		code {
			white-space: pre;
			font-family: monospace; }

		/**
		 * Abbreviations Markup
		 *
			<abbr title="hyper text markup language">HMTL</abbr>
		 *
		 * Extend this object into your markup.
		 *
		 */
		abbr {
			font-variant: small-caps;
			font-weight: 600;
			text-transform: lowercase;
			color: gray; }
		abbr[title]:hover {
			cursor: help; }

		h1,
		h2,
		h3,
		h4,
		h5,
		h6 {
			color: #222222; }

		p + .drop-cap {
			text-indent: 0;
			margin-top: 0; }

		.drop-cap:first-letter {
			float: left;
			margin: inherit;
			padding: inherit;
			font-size: 4em;
			font-family: inherit;
			line-height: 1;
			text-indent: 0;
			background: transparent;
			color: inherit; }

		/**
		 * Lining Definition Style Markup
		 *
			<dl class="lining">
				<dt><b></b></dt>
				<dd></dd>
			</dl>
		 *
		 * Extend this object into your markup.
		 *
		 */
		.lining dt,
		.lining dd {
			display: inline;
			margin: 0; }
		.lining dt + dt:before,
		.lining dd + dt:before {
			content: "\A";
			white-space: pre; }
		.lining dd + dd:before {
			content: ", "; }
		.lining dd:before {
			content: ": ";
			margin-left: -0.2rem; }

		/**
		 * Dictionary Definition Style Markup
		 *
			<dl class="dictionary-style">
				<dt><b></b></dt>
					<dd></dd>
			</dl>
		 *
		 * Extend this object into your markup.
		 *
		 */
		.dictionary-style dt {
			display: inline;
			counter-reset: definitions; }
		.dictionary-style dt + dt:before {
			content: ", ";
			margin-left: -0.2rem; }
		.dictionary-style dd {
			display: block;
			counter-increment: definitions; }
		.dictionary-style dd:before {
			content: counter(definitions,decimal) ". "; }

		/**
		 * Blockquote Markup
		 *
				<figure>
					<blockquote cite="">
						<p></p>
					</blockquote>
					<figcaption>
						<cite>
							<small><a href=""></a></small>
						</cite>
					</figcaption>
				</figure>
		 *
		 * Extend this object into your markup.
		 *
		 */
		/**
		 * Pull Quotes Markup
		 *
			<aside class="pull-quote">
				<blockquote>
					<p></p>
				</blockquote>
			</aside>
		 *
		 * Extend this object into your custom stylesheet.
		 *
		 */
		.pull-quote {
			position: relative;
			padding: 1em; }
		.pull-quote:before, .pull-quote:after {
			height: 1em;
			opacity: 0.5;
			position: absolute;
			font-size: 4em;
			color: #dc976e; }
		.pull-quote:before {
			content: '“';
			top: 0;
			left: 0; }
		.pull-quote:after {
			content: '”';
			bottom: 0;
			right: 0; }


		html,
		body {
			margin: 0;
			padding: 0;
		}
		body.mb_body {
			font: normal normal 100%/1.5em Helvetica, Arial, sans-serif;
			background: #fff;
			color: #000;
			-webkit-text-size-adjust: 100%;
		}
		.clear,
		.clearfix,
		.mb_clear,
		.mb_clearfix {
			clear: both;
		}
		.mb_article {
			max-width: 768px;
			margin: 0 auto;
			padding: 2em;
		}
		.mb_article .mb_post_meta {
			font-size: 90%;
			color: #999;
			float: left;
		}
		.mb_article .mb_post_meta.right {
			float: right;
		}
		.mb_article h1.mb_post_title {
			padding: 8px 0;
			line-height: 1.25em;
			margin-bottom: 0;
		}
		.mb_article p.mb_post_date {
			margin-bottom: 0;
		}
		.mb_article a {
			color: #3399ff;
			text-decoration: underline;
		}
		.mb_article a:hover,
		.mb_article a:active,
		.mb_article a:focus {
			text-decoration: none;
		}
		.mb_article a img {
			text-decoration: none;
		}
		.mb_article img {
			max-width: 100% !important;
			height: auto;
		}
		.mb_article blockquote,
		.mb_article .wp-caption-text {
			color: #999;
		}
		.mb_article blockquote {
			margin-left: 1em;
			border-left: 2px solid #aaa;
			padding-left: 1em;
		}
		.mb_article table {
			border: none;
			border-spacing: 0;
		}
		.mb_article table th {
			background-color: #ddd;
			border-left: 1px solid #999;
		}
		.mb_article table th:first-of-type {
			border-left: 0;
		}
		.mb_article table tr:nth-child(even) {
			background-color: #f4f4f4;
		}
		.mb_article table td {
			border-top: 1px solid #999;
			border-left: 1px solid #999;
		}
		.mb_article table td:first-of-type {
			border-left: 0;
		}
		.mb_article .media-container {
			width: 100% !important;
		}
		.mb_article .media-container img,
		.mb_article .media-container embed,
		.mb_article .media-container object,
		.mb_article .media-container video {
			max-width: 100% !important;
			height: auto !important;
		}
		.mb_article iframe {
			/*max-width:100% !important;*/
		}
		@media screen and (max-width: 480px) {
			.mb_article {
				padding: 2em 1em;
			}
			.gallery .gallery-item {
				width: 100% !important;
			}
			.gallery .gallery-item img {
				width: 100%;
				height: auto;
			}
		}
		.gallery {
			margin-bottom: 20px;
		}
		.gallery-item {
			float: left;
			margin: 0 4px 4px 0;
			overflow: hidden;
			position: relative;
		}
		.gallery-columns-1 .gallery-item {
			max-width: 100%;
		}
		.gallery-columns-2 .gallery-item {
			max-width: 48%;
			max-width: -webkit-calc(46%);
			max-width: calc(50% - 4px);
		}
		.gallery-columns-3 .gallery-item {
			max-width: 32%;
			max-width: -webkit-calc(29.3%);
			max-width: calc(33.3% - 4px);
		}
		.gallery-columns-4 .gallery-item {
			max-width: 23%;
			max-width: -webkit-calc(21%);
			max-width: calc(25% - 4px);
		}
		.gallery-columns-5 .gallery-item {
			max-width: 19%;
			max-width: -webkit-calc(16%);
			max-width: calc(20% - 4px);
		}
		.gallery-columns-6 .gallery-item {
			max-width: 15%;
			max-width: -webkit-calc(12.7%);
			max-width: calc(16.7% - 4px);
		}
		.gallery-columns-7 .gallery-item {
			max-width: 13%;
			max-width: -webkit-calc(10.28%);
			max-width: calc(14.28% - 4px);
		}
		.gallery-columns-8 .gallery-item {
			max-width: 11%;
			max-width: -webkit-calc(8.5%);
			max-width: calc(12.5% - 4px);
		}
		.gallery-columns-9 .gallery-item {
			max-width: 9%;
			max-width: -webkit-calc(7.1%);
			max-width: calc(11.1% - 4px);
		}
		.gallery-columns-1 .gallery-item:nth-of-type(1n),
		.gallery-columns-2 .gallery-item:nth-of-type(2n),
		.gallery-columns-3 .gallery-item:nth-of-type(3n),
		.gallery-columns-4 .gallery-item:nth-of-type(4n),
		.gallery-columns-5 .gallery-item:nth-of-type(5n),
		.gallery-columns-6 .gallery-item:nth-of-type(6n),
		.gallery-columns-7 .gallery-item:nth-of-type(7n),
		.gallery-columns-8 .gallery-item:nth-of-type(8n),
		.gallery-columns-9 .gallery-item:nth-of-type(9n) {
			margin-right: 0;
		}
		.gallery-columns-1.gallery-size-medium figure.gallery-item:nth-of-type(1n+1),
		.gallery-columns-1.gallery-size-thumbnail figure.gallery-item:nth-of-type(1n+1),
		.gallery-columns-2.gallery-size-thumbnail figure.gallery-item:nth-of-type(2n+1),
		.gallery-columns-3.gallery-size-thumbnail figure.gallery-item:nth-of-type(3n+1) {
			clear: left;
		}
		.gallery-caption {
			background-color: rgba(0, 0, 0, 0.7);
			-webkit-box-sizing: border-box;
			-moz-box-sizing: border-box;
			box-sizing: border-box;
			color: #fff;
			font-size: 12px;
			line-height: 1.5;
			margin: 0;
			max-height: 50%;
			opacity: 0;
			padding: 6px 8px;
			position: absolute;
			bottom: 0;
			left: 0;
			text-align: left;
			width: 100%;
		}
		.gallery-caption:before {
			content: "";
			height: 100%;
			min-height: 49px;
			position: absolute;
			top: 0;
			left: 0;
			width: 100%;
		}
		.gallery-item:hover .gallery-caption {
			opacity: 1;
		}
		.gallery-columns-7 .gallery-caption,
		.gallery-columns-8 .gallery-caption,
		.gallery-columns-9 .gallery-caption {
			display: none;
		}
		/*# sourceMappingURL=styles.css.map */
	</style>


	<?php

	if ( ! function_exists( 'ml_post_stylesheets' ) ) {
		function ml_post_stylesheets() {
			wp_enqueue_style( 'mobiloud-typeplate', plugins_url( 'mobiloud-mobile-app-plugin/post/css/_typeplate.css' ) );
			wp_enqueue_style( 'mobiloud-post', plugins_url( 'mobiloud-mobile-app-plugin/post/css/styles.css' ) );
		}
	}

	if ( ! function_exists( 'ml_post_scripts' ) ) {
		function ml_post_scripts() {
			if ( Mobiloud::get_option( 'ml_related_posts' ) ) {
				wp_enqueue_script( 'jquery' );
			}
		}
	}

	remove_all_actions( 'wp_head' );
	remove_all_actions( 'wp_footer' );
	remove_all_actions( 'wp_print_styles' );
	remove_all_actions( 'wp_enqueue_scripts' );
	remove_all_actions( 'locale_stylesheet' );
	remove_all_actions( 'wp_print_head_scripts' );
	remove_all_actions( 'wp_print_footer_scripts' );
	remove_all_actions( 'wp_shortlink_wp_head' );

	add_action( 'wp_print_styles', 'ml_post_stylesheets' );
	add_action( 'wp_print_footer_scripts', 'ml_post_scripts', 30 );
	add_action( 'wp_print_footer_scripts', '_wp_footer_scripts', 30 );

	add_action( 'wp_footer', 'wp_print_footer_scripts', 20 );



	$custom_css = stripslashes( get_option( 'ml_post_custom_css' ) );
	echo $custom_css ? '<style type="text/css" media="screen">' . strip_tags( $custom_css ) . '</style>' : '';

	$custom_js = stripslashes( get_option( 'ml_post_custom_js' ) );
	echo $custom_js ? '<script>' . $custom_js . '</script>' : '';
	$GLOBALS['post'] = $post;

	/* Next line of code (with eval function) required for MobiLoud Editor settings */
	eval( stripslashes( get_option( 'ml_post_head' ) ) ); // PHP in HEAD

	echo stripslashes( get_option( 'ml_html_post_head', '' ) ); // HTML in HEAD
	?>
</head>
<body class="mb_body mb_body_single post-id__<?php echo esc_attr( $post->ID ); ?>">
<?php
do_action( 'mobiloud_before_content_requests' );
require dirname( __FILE__ ) . '/../views/body_content.php';

wp_footer();

if ( Mobiloud::get_option( 'ml_related_posts' ) ) {
	?>
	<script type="text/javascript">
		jQuery(document).on('ready', function() {
			jQuery.ajax({
				url: '<?php echo get_site_url() . '/ml-api/v1/post/?related=1&post_id=' . $post->ID; ?>',
				type: 'GET',
				dataType: 'html',
				success: function(response) {
					try {
						if (response && response.indexOf('ml-relatedposts-a') > -1 && response.indexOf('{') != 0) {
							jQuery('#ml_relatedposts').html(response);

							jQuery('.ml-relatedposts-post').on('click.related', function() {
								var $link = (jQuery(this).hasClass('ml-relatedposts-post')) ? jQuery(this).find('a:first') : jQuery(this).closest('.ml-relatedposts-post').find('a:first');
								if ($link.length && $link.attr('data-ml_href')) {
									document.location.href = $link.attr('data-ml_href');
									return false;
								}
							});
						}
					} catch (e) {}
				},
			});
		})
	</script>
	<?php
}

if ( ! strpos( $_SERVER['REQUEST_URI'], 'ml-api/v2/list' ) ) {
	// reset css and js state, because we may enqueue js and css to another post content.
	wp_styles()->reset();
	wp_styles()->done = [];
	wp_scripts()->reset();
	wp_scripts()->done = [];
}
?>

</body>
</html>
