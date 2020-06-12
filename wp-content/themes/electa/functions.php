<?php
/**
 * electa functions and definitions
 *
 * @package Electa
 */

define( 'KAIRA_THEME_VERSION' , '1.3.09' );

// Upgrade / Order Premium page
require get_template_directory() . '/upgrade/upgrade.php';

// Load WP included scripts
require get_template_directory() . '/inc/template-tags.php';
require get_template_directory() . '/inc/extras.php';
require get_template_directory() . '/inc/jetpack.php';
require get_template_directory() . '/inc/customizer.php';

// Load Customizer Library scripts
require get_template_directory() . '/customizer/customizer-options.php';
require get_template_directory() . '/customizer/customizer-library/customizer-library.php';
require get_template_directory() . '/customizer/styles.php';
require get_template_directory() . '/customizer/mods.php';

// Load TGM plugin class
require_once get_template_directory() . '/inc/class-tgm-plugin-activation.php';
// Add customizer Upgrade class
require_once( get_template_directory() . '/includes/electa-pro/class-customize.php' );

if ( ! function_exists( 'kaira_setup_theme' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function kaira_setup_theme() {
    
    /**
     * Set the content width based on the theme's design and stylesheet.
     */
    global $content_width;
    if ( ! isset( $content_width ) ) {
        $content_width = 900; /* pixels */
    }

	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on electa, use a find and replace
	 * to change 'electa' to the name of your theme in all the template files
	 */
	load_theme_textdomain( 'electa', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
	 */
	add_theme_support('post-thumbnails');
    if ( function_exists( 'add_image_size' ) ) {
        add_image_size('blog_standard_img', 580, 380, true );
    }
    
    // The custom header is used for the logo
    add_theme_support('custom-header', array(
        'width'         => 250,
        'height'        => 180,
        'flex-width' => true,
        'flex-height' => true,
        'header-text' => false,
    ));

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'electa' ),
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form', 'comment-form', 'comment-list', 'gallery', 'caption',
	) );

	// Setup the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'electa_custom_background_args', array(
		'default-color' => 'F6F6F6',
	) ) );
    
    add_theme_support( 'title-tag' );
    
    add_theme_support( 'woocommerce' );
    add_theme_support( 'wc-product-gallery-zoom' );
    add_theme_support( 'wc-product-gallery-lightbox' );
    add_theme_support( 'wc-product-gallery-slider' );
}
endif; // kaira_setup_theme
add_action( 'after_setup_theme', 'kaira_setup_theme' );

/**
 * Register widget area.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
 */
function kaira_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Sidebar', 'electa' ),
		'id'            => 'sidebar-1',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );
}
add_action( 'widgets_init', 'kaira_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function kaira_scripts() {
    if( !get_theme_mod( 'kra-body-font', false ) ) {
        wp_enqueue_style( 'electa-google-body-font-default', '//fonts.googleapis.com/css?family=Open+Sans:400,300,300italic,400italic,600,600italic,700,700italic', array(), KAIRA_THEME_VERSION );
    }
    if( !get_theme_mod( 'kra-heading-font', false ) ) {
        wp_enqueue_style( 'electa-google-heading-font-default', '//fonts.googleapis.com/css?family=Roboto:400,300,300italic,400italic,500,500italic,700,700italic', array(), KAIRA_THEME_VERSION );
    }
    wp_enqueue_style( 'electa-fontawesome', get_template_directory_uri() . '/includes/font-awesome/css/font-awesome.css', array(), '4.7.0' );
	wp_enqueue_style( 'electa-style', get_stylesheet_uri(), array(), KAIRA_THEME_VERSION );
    
    if ( kaira_is_woocommerce_activated() ) {
        wp_enqueue_style( 'electa-woocommerce-style', get_template_directory_uri().'/upgrade/css/electa-woocommerce-style.css', array(), KAIRA_THEME_VERSION );
    }
    
    if ( ( ( is_front_page() ) && ( ( get_theme_mod( 'kra-home-blocks-layout' ) == 1 ) ) ) || ( is_home() ) && ( get_theme_mod( 'kra-blog-blocks-layout' ) == 1 ) ) {
        wp_enqueue_script( 'jquery-masonry' );
        wp_enqueue_script( 'electa-masonry-custom', get_template_directory_uri() . '/js/layout-blocks.js', array('jquery'), KAIRA_THEME_VERSION, true );
    }
    
    wp_enqueue_script( 'electa-customjs', get_template_directory_uri() . '/js/custom.js', array('jquery'), KAIRA_THEME_VERSION, true );

	wp_enqueue_script( 'electa-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), KAIRA_THEME_VERSION, true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'kaira_scripts' );

/**
 * Print Electa styling settings.
 */
function kaira_custom_css_styles(){
    $custom_css = '';
    if ( get_theme_mod( 'kra-custom-css', false ) ) {
        $custom_css = get_theme_mod( 'kra-custom-css' );
    } ?>
    <style type="text/css" media="screen">
        <?php echo htmlspecialchars_decode( $custom_css ); ?>
    </style>
    <?php
}
add_action( 'wp_head', 'kaira_custom_css_styles', 11 );

/**
 * Enqueue admin styling.
 */
function kaira_load_admin_script() {
    wp_enqueue_style( 'kaira-admin-css', get_template_directory_uri() . '/upgrade/css/admin-css.css' );
}
add_action( 'admin_enqueue_scripts', 'kaira_load_admin_script' );

/**
 * Enqueue Electa custom customizer styling.
 */
function load_kaira_customizer_style() {
    wp_enqueue_script( 'kaira-customizer-js', get_template_directory_uri() . '/customizer/customizer-library/js/customizer-custom.js', array('jquery'), KAIRA_THEME_VERSION, true );
    wp_enqueue_style( 'electa-customizer-css', get_template_directory_uri() . '/customizer/customizer-library/css/customizer.css' );
}    
add_action( 'customize_controls_enqueue_scripts', 'load_kaira_customizer_style' );

/**
 * Create function to check if WooCommerce exists
 */
if ( ! function_exists( 'kaira_is_woocommerce_activated' ) ) :
    function kaira_is_woocommerce_activated() {
        if ( class_exists( 'woocommerce' ) ) { return true; } else { return false; }
    }
endif; // kaira_is_woocommerce_activated

/**
 * Add category nicenames in body and post class
 */
function kaira_add_body_home_class( $home_add_class ) {
    if ( ( ( is_front_page() ) && ( get_theme_mod( 'kra-home-blocks-layout' ) == 1 ) ) || ( ( is_home() ) && ( get_theme_mod( 'kra-blog-blocks-layout' ) == 1 ) ) ) {
        $home_add_class[] = ' body-blocks-layout';
    }
    return $home_add_class;
}
add_filter( 'body_class', 'kaira_add_body_home_class' );

/**
 * Display recommended plugins with the TGM class
 */
function kaira_register_required_plugins() {
    $plugins = array(
        // The recommended WordPress.org plugins.
        array(
            'name'      => __( 'WooCommerce', 'electa' ),
            'slug'      => 'woocommerce',
            'required'  => false,
        ),
        array(
            'name'      => __( 'WooCustomizer', 'electa' ),
            'slug'      => 'woocustomizer',
            'required'  => false,
        ),
        array(
            'name'      => __( 'Elementor Page Builder', 'electa' ),
            'slug'      => 'elementor',
            'required'  => false,
            'external_url' => 'https://kairaweb.com/go/elementor/'
        ),
        array(
            'name'      => __( 'Breadcrumb NavXT', 'electa' ),
            'slug'      => 'breadcrumb-navxt',
            'required'  => false,
        )
    );
    $config = array(
        'id'           => 'electa',
        'menu'         => 'tgmpa-install-plugins',
    );

    tgmpa( $plugins, $config );
}
add_action( 'tgmpa_register', 'kaira_register_required_plugins' );

/**
 * Register a custom Post Categories ID column
 */
function kaira_edit_cat_columns( $kaira_cat_columns ) {
    $kaira_cat_in = array( 'cat_id' => 'Category ID <span class="cat_id_note">Blocks Layouts Category ID</span>' );
    $kaira_cat_columns = kaira_cat_columns_array_push_after( $kaira_cat_columns, $kaira_cat_in, 0 );
    return $kaira_cat_columns;
}
add_filter( 'manage_edit-category_columns', 'kaira_edit_cat_columns' );

/**
 * Print the ID column
 */
function kaira_cat_custom_columns( $value, $name, $cat_id ) {
    if( 'cat_id' == $name ) 
        echo $cat_id;
}
add_filter( 'manage_category_custom_column', 'kaira_cat_custom_columns', 10, 3 );

/**
 * Insert an element at the beggining of the array
 */
function kaira_cat_columns_array_push_after( $src, $kaira_cat_in, $pos ) {
    if ( is_int( $pos ) ) {
        $R = array_merge( array_slice( $src, 0, $pos + 1 ), $kaira_cat_in, array_slice( $src, $pos + 1 ) );
    } else {
        foreach ( $src as $k => $v ) {
            $R[$k] = $v;
            if ( $k == $pos )
                $R = array_merge( $R, $kaira_cat_in );
        }
    }
    return $R;
}
