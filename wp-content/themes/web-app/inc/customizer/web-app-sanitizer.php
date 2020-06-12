<?php
/**
 * Sanitization functions.
 *
 * @package web-app
 */
// Enable/Disable Options 
function web_app_sanitize_option($input){
        $option = array(
                'yes'   =>  esc_html__('Yes','web-app'),
                'no'    =>  esc_html__('No','web-app')
            );     
        if(array_key_exists($input, $option)){
            return $input;
        }
        else
            return '';
    }

// Web App Category Sanitizer

function web_app_sanitize_category_select($input){

    $web_app_cat_list = web_app_category_lists();
    if(array_key_exists($input,$web_app_cat_list)){
        return $input;
    }
    else{
        return '';
    }
}

// logo alignment
function web_app_webpagelayout($input) {
    $valid_keys = array(
        'fullwidth' => esc_html__('Full Width', 'web-app'),
        'box-layout' => esc_html__('Boxed', 'web-app')
    );
    if ( array_key_exists( $input, $valid_keys ) ) {
        return $input;
    } else {
        return '';
    }
} 

//integer sanitize
function web_app_integer_sanitize($input){
        return intval( $input );
   }
if ( ! function_exists( 'web_app_integer_sanitize' ) ) :

/**
 *  Sanitize Multiple Dropdown Taxonomies.
 *  @since 1.0.0
 */
function web_app_integer_sanitize( $input ) {
    // Make sure we have array.
    $input = (array) $input;

    // Sanitize each array element.
    $input = array_map( 'absint', $input );

    // Remove null elements.
    $input = array_values( array_filter( $input ) );

    return $input;
}
endif;

//page senitizer
if ( ! function_exists( 'web_app_sanitize_dropdown_pages' ) ) :

    /**
     * Sanitize dropdown pages.
     *
     * @since 1.0.0
     *
     * @param int                  $page_id Page ID.
     * @param WP_Customize_Setting $setting WP_Customize_Setting instance.
     * @return int|string Page ID if the page is published; otherwise, the setting default.
     */
    function web_app_sanitize_dropdown_pages( $page_id, $setting ) {

        // Ensure $input is an absolute integer.
        $page_id = absint( $page_id );

        // If $page_id is an ID of a published page, return it; otherwise, return the default.
        return ( 'publish' === get_post_status( $page_id ) ? $page_id : $setting->default );

    }

endif;

if ( ! function_exists( 'web_app_sanitize_select' ) ) :

    /**
     * Sanitize select.
     *
     * @since 1.0.0
     *
     * @param mixed                $input The value to sanitize.
     * @param WP_Customize_Setting $setting WP_Customize_Setting instance.
     * @return mixed Sanitized value.
     */
    function web_app_sanitize_select( $input, $setting ) {

        // Ensure input is a slug.
        $input = sanitize_key( $input );

        // Get list of choices from the control associated with the setting.
        $choices = $setting->manager->get_control( $setting->id )->choices;

        // If the input is a valid key, return it; otherwise, return the default.
        return ( array_key_exists( $input, $choices ) ? $input : $setting->default );

    }

endif;

// Sidebar Senitizer

function web_app_radio_sanitize_archive_sidebar($input) {
  $valid_keys = array(
        'sidebar-left' =>  esc_html__('Sidebar Left','web-app'),
        'sidebar-right' =>  esc_html__('Sidebar Right','web-app'),
        'sidebar-both' =>  esc_html__('Sidebar Both','web-app'),
        'sidebar-no' =>  esc_html__('Sidebar No','web-app'),
  );
  if ( array_key_exists( $input, $valid_keys ) ) {
     return $input;
  } else {
     return '';
  }
}