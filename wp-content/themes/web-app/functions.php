<?php
/**
 * web-app functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package web-app
 */

if ( ! function_exists( 'web_app_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function web_app_setup() {
		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on web-app, use a find and replace
		 * to change 'web-app' to the name of your theme in all the template files.
		 */
		load_theme_textdomain( 'web-app', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head.
		
		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		add_theme_support( 'title-tag' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );
		add_image_size('web-app-banner-image', 400, 700 , true);
		add_image_size('web-app-feature-thumb', 1170, 489, true);
		add_image_size('web-app-service-thumb', 400, 1000, true);
		add_image_size('web-app-testimonial-thumb', 80, 80, true);


		// This theme uses wp_nav_menu() in one location.
		register_nav_menus( array(
			'menu-1' => esc_html__( 'Primary', 'web-app' ),
			'social-media'  => esc_html__( 'Social Media', 'web-app' ),
			'footer-menu'  => esc_html__( 'Footer Menu', 'web-app' ),
		) );

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support( 'html5', array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		) );

		// Set up the WordPress core custom background feature.
		add_theme_support( 'custom-background', apply_filters( 'web_app_custom_background_args', array(
			'default-color' => 'ffffff',
			'default-image' => '',
		) ) );

		// Add theme support for selective refresh for widgets.
		add_theme_support( 'customize-selective-refresh-widgets' );

		// Add support for Block Styles.
		add_theme_support( 'wp-block-styles' );

		// Add support for full and wide align images.
		add_theme_support( 'align-wide' );			

		/**
		 * Add support for core custom logo.
		 *
		 * @link https://codex.wordpress.org/Theme_Logo
		 */
		add_theme_support( 'custom-logo', array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		) );
	}
endif;
add_action( 'after_setup_theme', 'web_app_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function web_app_content_width() {
	// This variable is intended to be overruled from themes.
	// Open WPCS issue: {@link https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/issues/1043}.
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	$GLOBALS['content_width'] = apply_filters( 'web_app_content_width', 640 );
}
add_action( 'after_setup_theme', 'web_app_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function web_app_widgets_init() {

	register_sidebar( array(
		'name'          => esc_html__( 'Right Sidebar', 'web-app' ),
		'id'            => 'web-app-sidebar-right',
		'description'   => esc_html__( 'Add widgets here.', 'web-app' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
		)
    );

	register_sidebar( array(
		'name'          => esc_html__( 'Left Sidebar', 'web-app' ),
		'id'            => 'web-app-sidebar-left',
		'description'   => esc_html__( 'Add widgets here.', 'web-app' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
		)
	 );
	register_sidebar( array(
		'name'          => esc_html__( 'Top Footer ', 'web-app' ),
		'id'            => 'web-app-top-footer',
		'description'   => esc_html__( 'Add widgets here.', 'web-app' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	register_sidebar( array(
		'name'          => esc_html__( 'Footer ', 'web-app' ),
		'id'            => 'web-app-footer',
		'description'   => esc_html__( 'Add widgets here.', 'web-app' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}

add_action( 'widgets_init', 'web_app_widgets_init' );

/**
 * Enqueue scripts and styles.
 */

function web_app_scripts() {

	$web_app_font_args = array(
		
        'family' => 'Muli:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i',
    );

    wp_enqueue_style( 'wep-app-google-fonts', add_query_arg( $web_app_font_args, "//fonts.googleapis.com/css" ) );

	// Load OWl Carousel
    wp_enqueue_style( 'owl-carousel-css', get_template_directory_uri().'/assets/css/owl.carousel.css',array(), ' v2.3.4', 'all' );

    // Load OWL Theme
    wp_enqueue_style( 'owl-theme-css', get_template_directory_uri().'/assets/css/owl.theme.default.css',array(), 'v2.3.4', 'all' );


    // Font Awesome  CSS
    wp_enqueue_style( 'font-awesome', get_template_directory_uri().'/assets/css/font-awesome.min.css',array(), '4.7.0 ', 'all' );

    // Font Awesome  CSS
    wp_enqueue_style( 'meanmenu-css', get_template_directory_uri().'/assets/css/meanmenu.css',array(), '4.7.0 ', 'all' );


	wp_enqueue_style( 'web-app-style', get_stylesheet_uri() );


    // Mean Menu JS
   	wp_enqueue_script( 'meanmenu-js', get_template_directory_uri().'/assets/js/jquery.meanmenu.js', array( 'jquery' ), 'v2.0.8', true );

	// Load OWl Carousel
	wp_enqueue_script( 'owl-carousel-js', get_template_directory_uri().'/assets/js/owl.carousel.js', array( 'jquery' ), ' v2.3.4', true );

	wp_enqueue_script( 'web-app-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20151215', true );

	wp_enqueue_script( 'web-app-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20151215', true );

	wp_enqueue_script( 'wp-web-custom', get_template_directory_uri() . '/assets/js/custom.js', array( 'jquery'), '1.0.0', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}

add_action( 'wp_enqueue_scripts', 'web_app_scripts' );

/** Admin Script */
function web_app_admin_enqueue() {

    $currentscreen = get_current_screen();
    if($currentscreen->id == 'widgets'){
        wp_enqueue_media();
        wp_enqueue_script('web-app-admin-script', get_template_directory_uri().'/inc/js/admin-script.js',array('jquery'));
    }
}
add_action( 'admin_enqueue_scripts', 'web_app_admin_enqueue' );

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Custom functions file.
 */

require get_template_directory() . '/inc/custom-function.php';

/**
 *  Web-App Metabox
 */

require  get_template_directory()  . '/inc/metabox.php';

/**
 * Load Custom Customizer file.
 */
require get_template_directory() . '/inc/customizer/customizer-options.php';


/** Widget Fields **/
require get_template_directory() . '/inc/widgets/widgets-field.php'; 

/** Top Footer Table Widget **/
require get_template_directory() . '/inc/widgets/widgets-top-footer.php';

/** Footer Table Widget **/
require get_template_directory() . '/inc/widgets/footer.php';

/** TGM Plugins Activations  **/

require get_template_directory() . '/inc/class-tgm-plugin-activation.php';

/**
 * Load Jetpack compatibility file.
 */

if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}

