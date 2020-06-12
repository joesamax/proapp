<?php
/**
 * web-app Theme Customizer
 *
 * @package web-app
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function web_app_customize_register( $wp_customize ) {
	
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';

	if ( isset( $wp_customize->selective_refresh ) ) {

		$wp_customize->selective_refresh->add_partial( 'blogname', array(
			'selector'        => '.site-title a',
			'render_callback' => 'web_app_customize_partial_blogname',
		) );
		$wp_customize->selective_refresh->add_partial( 'blogdescription', array(
			'selector'        => '.site-description',
			'render_callback' => 'web_app_customize_partial_blogdescription',
		) );
		
	}

	 /** Fa Icons List **/

  	class web_app_Customize_Icons_Control extends WP_Customize_Control {

	    public $type = 'web_app_icons';

	    public function render_content() {

		      $saved_icon_value = $this->value();
		  	   ?>
		      <label>
			        <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			        <span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>

    			        <div class="fa-icons-list">

    				          <div class="selected-icon-preview"><?php if( !empty( $saved_icon_value ) ) { echo '<i class="fa '. esc_attr($saved_icon_value) .'"></i>'; } ?>            
                              </div>

    				          <ul class="icons-list-wrapper">
    					            <?php 
    					              $web_app_icons_list = web_app_icons_array();
    					              foreach ( $web_app_icons_list as $key => $icon_value ) {
    					                if( $saved_icon_value == $icon_value ) {
    					                  echo '<li class="selected"><i class="fa '. esc_attr($icon_value) .'"></i></li>';
    					                } else {
    					                  echo '<li><i class="fa '. esc_attr($icon_value) .'"></i></li>';
    					                }
    					              }
    					            ?>
    				          </ul>

    				          <input type="hidden" class="ap-icon-value" value="" <?php $this->link(); ?>>

    			        </div>

		      </label>
	  <?php
	    }
	 }
}
add_action( 'customize_register', 'web_app_customize_register' );

/**
 * Render the site title for the selective refresh partial.
 *
 * @return void
 */
function web_app_customize_partial_blogname() {
	bloginfo( 'name' );
}

/**
 * Render the site tagline for the selective refresh partial.
 *
 * @return void
 */
function web_app_customize_partial_blogdescription() {
	bloginfo( 'description' );
}

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function web_app_customize_preview_js() {
	wp_enqueue_script( 'web-app-customizer', get_template_directory_uri() . '/js/customizer.js', array( 'customize-preview' ), '20151215', true );
}
add_action( 'customize_preview_init', 'web_app_customize_preview_js' );


function web_app_customize_backend_scripts() {
  
	// Load fontawesome
wp_enqueue_style( 'font-awesome', get_template_directory_uri().'/assets/css/font-awesome.min.css', array(), '4.4.0' );

wp_enqueue_script( 'web-app-customizer-scripts', get_template_directory_uri() . '/inc/js/customizer-scripts.js', array( 'jquery', 'customize-controls' ), '20160714', true );

wp_enqueue_style( 'web-app-customizer-style', get_template_directory_uri() . '/inc/css/customizer-style.css' );
	
}
add_action( 'customize_controls_enqueue_scripts', 'web_app_customize_backend_scripts', 10 );
