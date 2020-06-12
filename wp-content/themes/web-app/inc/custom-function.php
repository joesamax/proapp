<?php

/**
 * Web App  functions and definitions
 *
 * @package web-app
 */

/**
 * My Functions
 */


//==========================================================================================================//

/**  Web App   Cateogory List **/

function web_app_category_lists(){
  $category   = get_categories();
  $cat_list   = array();
  $cat_list[0]= esc_html__('Select category','web-app');
  foreach ($category as $cat) {
    $cat_list[$cat->slug]  = $cat->name;
  }
  return $cat_list;
}

//================================== Main Slider ===================================================//

/** Web App  Slider Function **/

function web_app_slider_callback(){

if(get_theme_mod( 'web_app_slider_option','no' ) == 'yes'):

  $web_app_slider = get_theme_mod('web_app_slider_section_cat',0);
  $slider_readmore = get_theme_mod( 'web_app_slider_readmore',esc_html__('Buy Now','web-app') );
  $number = get_theme_mod('web_app_slider_num',5);

	?>

	 <section class="featured-slider" style="background-image: url('<?php echo esc_url(get_theme_mod('web_app_page_bg_image','')); ?>') ";>

		<?php
			if( !empty( $web_app_slider) ) {

				$loop = new WP_Query(
				array(
				'post_type' => 'post',    
				'category_name' => esc_html($web_app_slider),
				'posts_per_page' => absint( $number ),  
				)
				);
			}else{
				$loop = new WP_Query( array( 'post_type'=>'post','posts_per_page'=>absint( $number ), ) );
			}   
			?>

			<div class="owl-carousel owl-theme owl-slider-demo">

				<?php
				if($loop->have_posts() ) {

					while($loop->have_posts() ) {

						$loop->the_post();

						$image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'web-app-banner-image', true );
						?>

						<div class="slider-content">

							<div class="slider-text">

								<h2 class="slider-title"> <?php the_title();?> </h2>
                                
								<?php if(!empty($slider_readmore)){ ?>
		                            <a href="<?php the_permalink();?>" class="box-button"><?php echo esc_html( $slider_readmore );?><span></span></a>
		                        <?php } ?>

							</div>

							<figure class="slider-image">
								 <img src="<?php echo esc_url($image[0]);?>" />
							</figure>

						</div>
                        
						<?php 
			            }
		           		 wp_reset_postdata();
		     		   }
		    		?> 	

			</div>
			
	</section>

<?php endif;   ?>	
<?php 
}
add_action('web_app_slider_callback_action','web_app_slider_callback');

//========================================== Page Breadcrumbs ================================================================//

function web_app_sanitize_bradcrumb($input){
    $all_tags = array(
        'a'=>array(
            'href'=>array()
        )
     );
    return wp_kses($input,$all_tags);
    
}

// Web App breadcrumbs settingg


if ( ! function_exists( 'web_app_breadcrumbs' ) ) :

    function web_app_breadcrumbs() {

    global $post;
    $showOnHome = 0; // 1 - show breadcrumbs on the homepage, 0 - don't show

    $delimiter = '&gt;';

    $showCurrent = 1; // 1 - show current post/page title in breadcrumbs, 0 - don't show
    $homeLink = esc_url( home_url() );

    if (is_home() || is_front_page()) {

        if ($showOnHome == 1)
            echo '<div id="" class="trail-item trail-begin"><a href="' . esc_url($homeLink) . '">' . esc_html__('Home', 'web-app') . '</a></div></div>';
    } else {

        echo '<div  class="trail-item trail-begin" rel="home"><a href="' . esc_url($homeLink) . '">' . esc_html__('Home', 'web-app') . '</a> ' . esc_attr($delimiter) . ' ';

        if (is_category()) {
            $thisCat = get_category(get_query_var('cat'), false);
            if ($thisCat->parent != 0)
                echo get_category_parents($thisCat->parent, TRUE, ' ' . esc_html($delimiter) . ' ');
            echo '<span class="trail-items">' . esc_html__('Archive by category','web-app').' "' . single_cat_title('', false) . '"' . '</span>';
        } elseif (is_search()) {
            echo '<span class="trail-items">' . esc_html__('Search results for','web-app'). '"' . get_search_query() . '"' . '</span>';
        } elseif (is_day()) {
            echo '<a href="' . esc_url(get_year_link(get_the_time('Y'))) . '">' . esc_html(get_the_time('Y')) . '</a> ' . esc_html($delimiter) . ' ';
            echo '<a href="' . esc_url(get_month_link(get_the_time('Y')), esc_html(get_the_time('m'))) . '">' . esc_html(get_the_time('F')) . '</a> ' . esc_html($delimiter) . ' ';
            echo '<span class="trail-items">' . esc_html(get_the_time('d')) . '</span>';
        } elseif (is_month()) {
            echo '<a href="' . esc_url(get_year_link(get_the_time('Y'))) . '">' . esc_html(get_the_time('Y')) . '</a> ' . esc_html($delimiter) . ' ';
            echo '<span class="trail-items">' . esc_html(get_the_time('F')) . '</span>';
        } elseif (is_year()) {
            echo '<span class="trail-items">' . esc_html(get_the_time('Y')) . '</span>';
        } elseif (is_single() && !is_attachment()) {
            if (get_post_type() != 'post') {
                $post_type = get_post_type_object(get_post_type());
                $slug = $post_type->rewrite;
                echo '<a href="' . esc_url($homeLink) . '/' . esc_attr($slug['slug']) . '/">' . esc_attr($post_type->labels->singular_name) . '</a>';
                if ($showCurrent == 1)
                    echo ' ' . esc_attr($delimiter) . ' ' . '<span class="trail-items">' . esc_html(get_the_title()) . '</span>';
            } else {
                $cat = get_the_category();
                $cat = $cat[0];
                $cats = get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
                if ($showCurrent == 0)
                    $cats = preg_replace("#^(.+)\s$delimiter\s$#", "$1", $cats);
                echo web_app_sanitize_bradcrumb($cats);
                if ($showCurrent == 1)
                    echo '<span class="trail-items">' . esc_html(get_the_title()) . '</span>';
            }
        } elseif (!is_single() && !is_page() && get_post_type() != 'post' && !is_404()) {
            $post_type = get_post_type_object(get_post_type());
            if($post_type){
            echo '<span class="trail-items">' . esc_html($post_type->labels->singular_name) . '</span>';
            }
        } elseif (is_attachment()) {
            if ($showCurrent == 1) echo ' ' . '<span class="trail-items">' . esc_html(get_the_title()) . '</span>';
        } elseif (is_page() && !$post->post_parent) {
            if ($showCurrent == 1)
                echo '<span class="trail-items">' . esc_html(get_the_title()) . '</span>';
        } elseif (is_page() && $post->post_parent) {
            $parent_id = $post->post_parent;
            $breadcrumbs = array();
            while ($parent_id) {
                $page = get_page($parent_id);
                $breadcrumbs[] = '<a href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a>';
                $parent_id = $page->post_parent;
            }
            $breadcrumbs = array_reverse($breadcrumbs);
            for ($i = 0; $i < count($breadcrumbs); $i++) {
                echo web_app_sanitize_bradcrumb($breadcrumbs[$i]);
                if ($i != count($breadcrumbs) - 1)
                    echo ' ' . esc_html($delimiter). ' ';
            }
            if ($showCurrent == 1)
                echo ' ' . esc_html($delimiter) . ' ' . '<span class="trail-items">' . esc_html(get_the_title()) . '</span>';
        } elseif (is_tag()) {
            echo '<span class="trail-items">' . esc_html__('Posts tagged','web-app').' "' . esc_html(single_tag_title('', false)) . '"' . '</span>';
        } elseif (is_author()) {
            global $author;
            $userdata = get_userdata($author);
            echo '<span class="trail-items">' . esc_html__('Articles posted by ','web-app'). esc_html($userdata->display_name) . '</span>';
        } elseif (is_404()) {
            echo '<span class="trail-items">' . esc_html__('Error 404','web-app') . '</span>';
        }

        if (get_query_var('paged')) {
            if (is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author())
                echo ' (';
            echo esc_html__('Page', 'web-app') . ' ' . get_query_var('paged');
            if (is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author())
                echo ')';
        }

        echo '</div>';
    }
    }
endif;

//==========================================================================================================//

 if ( ! function_exists( 'web_app_page_title' ) ) :

    function web_app_page_title(){
    ?>
        <div class="page-title-wrap"  style="background-image: url(<?php header_image(); ?>);">

         <?php if (get_theme_mod('web_app_breadcrumb_option','no')=='yes') {  ?>
            
               <div class="container">

                    <?php
                    if(is_archive()) {
                        the_archive_title( '<h2 class="page-title">', '</h2>' );
                        the_archive_description( '<div>', '</div>' );

                    } elseif(is_single() || is_singular('page')) {
                        wp_reset_postdata();
                        the_title('<h2 class="page-title">', '</h2>');
                    } elseif(is_search()) {
                    ?>
                        <h2 class="page-title"><?php printf( esc_html__( 'Search Results for: %s', 'web-app' ), '<span>' . get_search_query() . '</span>' ); ?></h2>
                    <?php
                    } elseif(is_404()) {
                    ?>
                        <h2 class="page-title"><?php esc_html_e( '404 Error', 'web-app' ); ?></h2>
                    <?php
                    }
                    ?>
                   
                    <div class="breadcrumb-trail breadcrumbs">

                            <ul class="trail-items">

                                <li class="trail-item trail-end">
                                   
                                   <span itemprop="name"><?php web_app_breadcrumbs(); ?></span>

                                    
                                </li>
                            </ul>

                    </div>

                </div>
        
                <?php } ?>

        </div>
    <?php
    }

endif;

add_action('web_app_title','web_app_page_title');

//========================================== Icons List ================================================================//

/** FA icon array **/
function web_app_icons_array(){
    
    $ap_icon_list_raw = 'fa-glass, fa-music, fa-search, fa-envelope-o, fa-heart, fa-star, fa-star-o, fa-user, fa-film, fa-th-large, fa-th, fa-th-list, fa-check, fa-times, fa-search-plus, fa-search-minus, fa-power-off, fa-signal, fa-cog, fa-trash-o, fa-home, fa-file-o, fa-clock-o, fa-road, fa-download, fa-arrow-circle-o-down, fa-arrow-circle-o-up, fa-inbox, fa-play-circle-o, fa-repeat, fa-refresh, fa-list-alt, fa-lock, fa-flag, fa-headphones, fa-volume-off, fa-volume-down, fa-volume-up, fa-qrcode, fa-barcode, fa-tag, fa-tags, fa-book, fa-bookmark, fa-print, fa-camera, fa-font, fa-bold, fa-italic, fa-text-height, fa-text-width, fa-align-left, fa-align-center, fa-align-right, fa-align-justify, fa-list, fa-outdent, fa-indent, fa-video-camera, fa-picture-o, fa-pencil, fa-map-marker, fa-adjust, fa-tint, fa-pencil-square-o, fa-share-square-o, fa-check-square-o, fa-arrows, fa-step-backward, fa-fast-backward, fa-backward, fa-play, fa-pause, fa-stop, fa-forward, fa-fast-forward, fa-step-forward, fa-eject, fa-chevron-left, fa-chevron-right, fa-plus-circle, fa-minus-circle, fa-times-circle, fa-check-circle, fa-question-circle, fa-info-circle, fa-crosshairs, fa-times-circle-o, fa-check-circle-o, fa-ban, fa-arrow-left, fa-arrow-right, fa-arrow-up, fa-arrow-down, fa-share, fa-expand, fa-compress, fa-plus, fa-minus, fa-asterisk, fa-exclamation-circle, fa-gift, fa-leaf, fa-fire, fa-eye, fa-eye-slash, fa-exclamation-triangle, fa-plane, fa-calendar, fa-random, fa-comment, fa-magnet, fa-chevron-up, fa-chevron-down, fa-retweet, fa-shopping-cart, fa-folder, fa-folder-open, fa-arrows-v, fa-arrows-h, fa-bar-chart, fa-twitter-square, fa-facebook-square, fa-camera-retro, fa-key, fa-cogs, fa-comments, fa-thumbs-o-up, fa-thumbs-o-down, fa-star-half, fa-heart-o, fa-sign-out, fa-linkedin-square, fa-thumb-tack, fa-external-link, fa-sign-in, fa-trophy, fa-github-square, fa-upload, fa-lemon-o, fa-phone, fa-square-o, fa-bookmark-o, fa-phone-square, fa-twitter, fa-facebook, fa-github, fa-unlock, fa-credit-card, fa-rss, fa-hdd-o, fa-bullhorn, fa-bell, fa-certificate, fa-hand-o-right, fa-hand-o-left, fa-hand-o-up, fa-hand-o-down, fa-arrow-circle-left, fa-arrow-circle-right, fa-arrow-circle-up, fa-arrow-circle-down, fa-globe, fa-wrench, fa-tasks, fa-filter, fa-briefcase, fa-arrows-alt, fa-users, fa-link, fa-cloud, fa-flask, fa-scissors, fa-files-o, fa-paperclip, fa-floppy-o, fa-square, fa-bars, fa-list-ul, fa-list-ol, fa-strikethrough, fa-underline, fa-table, fa-magic, fa-truck, fa-pinterest, fa-pinterest-square, fa-google-plus-square, fa-google-plus, fa-money, fa-caret-down, fa-caret-up, fa-caret-left, fa-caret-right, fa-columns, fa-sort, fa-sort-desc, fa-sort-asc, fa-envelope, fa-linkedin, fa-undo, fa-gavel, fa-tachometer, fa-comment-o, fa-comments-o, fa-bolt, fa-sitemap, fa-umbrella, fa-clipboard, fa-lightbulb-o, fa-exchange, fa-cloud-download, fa-cloud-upload, fa-user-md, fa-stethoscope, fa-suitcase, fa-bell-o, fa-coffee, fa-cutlery, fa-file-text-o, fa-building-o, fa-hospital-o, fa-ambulance, fa-medkit, fa-fighter-jet, fa-beer, fa-h-square, fa-plus-square, fa-angle-double-left, fa-angle-double-right, fa-angle-double-up, fa-angle-double-down, fa-angle-left, fa-angle-right, fa-angle-up, fa-angle-down, fa-desktop, fa-laptop, fa-tablet, fa-mobile, fa-circle-o, fa-quote-left, fa-quote-right, fa-spinner, fa-circle, fa-reply, fa-github-alt, fa-folder-o, fa-folder-open-o, fa-smile-o, fa-frown-o, fa-meh-o, fa-gamepad, fa-keyboard-o, fa-flag-o, fa-flag-checkered, fa-terminal, fa-code, fa-reply-all, fa-star-half-o, fa-location-arrow, fa-crop, fa-code-fork, fa-chain-broken, fa-question, fa-info, fa-exclamation, fa-superscript, fa-subscript, fa-eraser, fa-puzzle-piece, fa-microphone, fa-microphone-slash, fa-shield, fa-calendar-o, fa-fire-extinguisher, fa-rocket, fa-maxcdn, fa-chevron-circle-left, fa-chevron-circle-right, fa-chevron-circle-up, fa-chevron-circle-down, fa-html5, fa-css3, fa-anchor, fa-unlock-alt, fa-bullseye, fa-ellipsis-h, fa-ellipsis-v, fa-rss-square, fa-play-circle, fa-ticket, fa-minus-square, fa-minus-square-o, fa-level-up, fa-level-down, fa-check-square, fa-pencil-square, fa-external-link-square, fa-share-square, fa-compass, fa-caret-square-o-down, fa-caret-square-o-up, fa-caret-square-o-right, fa-eur, fa-gbp, fa-usd, fa-inr, fa-jpy, fa-rub, fa-krw, fa-btc, fa-file, fa-file-text, fa-sort-alpha-asc, fa-sort-alpha-desc, fa-sort-amount-asc, fa-sort-amount-desc, fa-sort-numeric-asc, fa-sort-numeric-desc, fa-thumbs-up, fa-thumbs-down, fa-youtube-square, fa-youtube, fa-xing, fa-xing-square, fa-youtube-play, fa-dropbox, fa-stack-overflow, fa-instagram, fa-flickr, fa-adn, fa-bitbucket, fa-bitbucket-square, fa-tumblr, fa-tumblr-square, fa-long-arrow-down, fa-long-arrow-up, fa-long-arrow-left, fa-long-arrow-right, fa-apple, fa-windows, fa-android, fa-linux, fa-dribbble, fa-skype, fa-foursquare, fa-trello, fa-female, fa-male, fa-gratipay, fa-sun-o, fa-moon-o, fa-archive, fa-bug, fa-vk, fa-weibo, fa-renren, fa-pagelines, fa-stack-exchange, fa-arrow-circle-o-right, fa-arrow-circle-o-left, fa-caret-square-o-left, fa-dot-circle-o, fa-wheelchair, fa-vimeo-square, fa-try, fa-plus-square-o, fa-space-shuttle, fa-slack, fa-envelope-square, fa-wordpress, fa-openid, fa-university, fa-graduation-cap, fa-yahoo, fa-google, fa-reddit, fa-reddit-square, fa-stumbleupon-circle, fa-stumbleupon, fa-delicious, fa-digg, fa-pied-piper-pp, fa-pied-piper-alt, fa-drupal, fa-joomla, fa-language, fa-fax, fa-building, fa-child, fa-paw, fa-spoon, fa-cube, fa-cubes, fa-behance, fa-behance-square, fa-steam, fa-steam-square, fa-recycle, fa-car, fa-taxi, fa-tree, fa-spotify, fa-deviantart, fa-soundcloud, fa-database, fa-file-pdf-o, fa-file-word-o, fa-file-excel-o, fa-file-powerpoint-o, fa-file-image-o, fa-file-archive-o, fa-file-audio-o, fa-file-video-o, fa-file-code-o, fa-vine, fa-codepen, fa-jsfiddle, fa-life-ring, fa-circle-o-notch, fa-rebel, fa-empire, fa-git-square, fa-git, fa-hacker-news, fa-tencent-weibo, fa-qq, fa-weixin, fa-paper-plane, fa-paper-plane-o, fa-history, fa-circle-thin, fa-header, fa-paragraph, fa-sliders, fa-share-alt, fa-share-alt-square, fa-bomb, fa-futbol-o, fa-tty, fa-binoculars, fa-plug, fa-slideshare, fa-twitch, fa-yelp, fa-newspaper-o, fa-wifi, fa-calculator, fa-paypal, fa-google-wallet, fa-cc-visa, fa-cc-mastercard, fa-cc-discover, fa-cc-amex, fa-cc-paypal, fa-cc-stripe, fa-bell-slash, fa-bell-slash-o, fa-trash, fa-copyright, fa-at, fa-eyedropper, fa-paint-brush, fa-birthday-cake, fa-area-chart, fa-pie-chart, fa-line-chart, fa-lastfm, fa-lastfm-square, fa-toggle-off, fa-toggle-on, fa-bicycle, fa-bus, fa-ioxhost, fa-angellist, fa-cc, fa-ils, fa-meanpath, fa-buysellads, fa-connectdevelop, fa-dashcube, fa-forumbee, fa-leanpub, fa-sellsy, fa-shirtsinbulk, fa-simplybuilt, fa-skyatlas, fa-cart-plus, fa-cart-arrow-down, fa-diamond, fa-ship, fa-user-secret, fa-motorcycle, fa-street-view, fa-heartbeat, fa-venus, fa-mars, fa-mercury, fa-transgender, fa-transgender-alt, fa-venus-double, fa-mars-double, fa-venus-mars, fa-mars-stroke, fa-mars-stroke-v, fa-mars-stroke-h, fa-neuter, fa-genderless, fa-facebook-official, fa-pinterest-p, fa-whatsapp, fa-server, fa-user-plus, fa-user-times, fa-bed, fa-viacoin, fa-train, fa-subway, fa-medium, fa-y-combinator, fa-optin-monster, fa-opencart, fa-expeditedssl, fa-battery-full, fa-battery-three-quarters, fa-battery-half, fa-battery-quarter, fa-battery-empty, fa-mouse-pointer, fa-i-cursor, fa-object-group, fa-object-ungroup, fa-sticky-note, fa-sticky-note-o, fa-cc-jcb, fa-cc-diners-club, fa-clone, fa-balance-scale, fa-hourglass-o, fa-hourglass-start, fa-hourglass-half, fa-hourglass-end, fa-hourglass, fa-hand-rock-o, fa-hand-paper-o, fa-hand-scissors-o, fa-hand-lizard-o, fa-hand-spock-o, fa-hand-pointer-o, fa-hand-peace-o, fa-trademark, fa-registered, fa-creative-commons, fa-gg, fa-gg-circle, fa-tripadvisor, fa-odnoklassniki, fa-odnoklassniki-square, fa-get-pocket, fa-wikipedia-w, fa-safari, fa-chrome, fa-firefox, fa-opera, fa-internet-explorer, fa-television, fa-contao, fa-500px, fa-amazon, fa-calendar-plus-o, fa-calendar-minus-o, fa-calendar-times-o, fa-calendar-check-o, fa-industry, fa-map-pin, fa-map-signs, fa-map-o, fa-map, fa-commenting, fa-commenting-o, fa-houzz, fa-vimeo, fa-black-tie, fa-fonticons, fa-reddit-alien, fa-edge, fa-credit-card-alt, fa-codiepie, fa-modx, fa-fort-awesome, fa-usb, fa-product-hunt, fa-mixcloud, fa-scribd, fa-pause-circle, fa-pause-circle-o, fa-stop-circle, fa-stop-circle-o, fa-shopping-bag, fa-shopping-basket, fa-hashtag, fa-bluetooth, fa-bluetooth-b, fa-percent, fa-gitlab, fa-wpbeginner, fa-wpforms, fa-envira, fa-universal-access, fa-wheelchair-alt, fa-question-circle-o, fa-blind, fa-audio-description, fa-volume-control-phone, fa-braille, fa-assistive-listening-systems, fa-american-sign-language-interpreting, fa-deaf, fa-glide, fa-glide-g, fa-sign-language, fa-low-vision, fa-viadeo, fa-viadeo-square, fa-snapchat, fa-snapchat-ghost, fa-snapchat-square, fa-pied-piper, fa-first-order, fa-yoast, fa-themeisle, fa-google-plus-official, fa-font-awesome' ;
    $ap_icon_list = explode( ", " , $ap_icon_list_raw);
    return $ap_icon_list;
 }


add_action( 'tgmpa_register', 'web_app_register_required_plugins' );


function web_app_register_required_plugins() {
  /*
   * Array of plugin arrays. Required keys are name and slug.
   * If the source is NOT from the .org repo, then source is also required.   newsletter
   */
  $plugins = array(

    array(
      'name'        =>esc_html__('Contact Form 7','web-app'),
      'slug'        => 'contact-form-7',
      'is_callable' => false,
    ),

  array(
    'name'        => esc_html__('newsletter','web-app'),
    'slug'        => 'newsletter',
    'is_callable' => false,
    ),
  
  array(
    'name'        => esc_html__('MailChimp for WordPress','web-app'),
    'slug'        => 'mailchimp-for-wp',
    'is_callable' => false,
    ),
  array(
    'name'        => esc_html__('One Click Demo Import','web-app'),
    'slug'        => 'one-click-demo-import',
    'is_callable' => false,
    ),  



);
$config = array(
    'id'           => 'web-app',      // Unique ID for hashing notices.
    'default_path' => '',                      // Default absolute path to bundled plugins.
    'menu'         => 'tgmpa-install-plugins', // Menu slug.
    'parent_slug'  => 'themes.php',            // Parent menu slug.
    'capability'   => 'edit_theme_options',    // Capability needed to view plugin install page
    'has_notices'  => true,                    // Show admin notices or not.
    'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
    'dismiss_msg'  => '',                      // If 'dismissable' is false.
    'is_automatic' => false,                   // Automatically activate plugins.
    'message'      => '',                      // Message to output right before the plugins table.
  );

  tgmpa( $plugins, $config );
}

/**
* Remove branding
*/
add_filter( 'pt-ocdi/disable_pt_branding', '__return_true' );

/*Import demo data*/
if ( ! function_exists( 'web_app_one_click_notice' ) ) :
    function web_app_one_click_notice( $default_text ) { 
        $info_notice = sprintf( esc_html__( ' Please click %1$s to download the zip files.', 'web-app' ), '<a href="'.esc_url( 'https://demo.theme404.com/demo-content/web-app-business/demo-content.zip' ).'" rel="designer">'.esc_html__('Here', 'web-app').'</a>' );
        $default_text .= '<div class="info-text-wrapper">';
        $default_text .= '<h3>'.esc_html__( 'To import the demo data follow the following steps:','web-app' ).'</h3>';
        $default_text .= '<ol>';
         $default_text .= '<li>'.wp_kses_post( $info_notice).'</li>';
        $default_text .= '<li>'.esc_html__( 'Extract the zip file.','web-app').'</li>';
        $default_text .= '<li>'.esc_html__( 'Upload the .xml, .wie and .date files on the following options.','web-app').'</li>';
        $default_text .= '<li>'.esc_html__( 'Click on Import Demo  Data button.','web-app').'</li>';
        $default_text .= '</ol>';
        $default_text .= '</div>';

        return $default_text;
    }
    add_filter( 'pt-ocdi/plugin_intro_text', 'web_app_one_click_notice' );
endif;

/**
 * Action that happen after import
 */
if ( ! function_exists( 'web_app_after_demo_import' ) ) :
    function web_app_after_demo_import( $selected_import ) {            //Set Menu
            $primary_menu = get_term_by('name', 'primary menu', 'nav_menu'); 
            $social_menu = get_term_by('name', 'Social Menu', 'nav_menu');  
            $footer_menu  = get_term_by( 'name', 'Footer menu', 'nav_menu');

            set_theme_mod( 'nav_menu_locations' , array( 
                'menu-1' => $primary_menu->term_id,
                'social-media' => $social_menu->term_id, 
                'footer-menu' => $footer_menu->term_id, 
                ) 
            );
            //Set Front page
            $page = get_page_by_title( 'Home');
            if ( isset( $page->ID ) ) {
                update_option( 'page_on_front', $page->ID );
                update_option( 'show_on_front', 'page' );
            } 
    }
    add_action( 'pt-ocdi/after_import', 'web_app_after_demo_import' );
endif;


