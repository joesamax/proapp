<?php

/**
 * Theme Customizer Custom
 *
 * @package web-app
 */

/**
 * Add new options the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */

function web_app_custom_customize_register( $wp_customize ) { 

	require get_template_directory() . '/inc/customizer/web-app-sanitizer.php';

  // Web App  Category Posts List.
  $web_app_category_lists = web_app_category_lists();


  /****************  Add Deafult  Pannel   ***********************/
    
	$wp_customize->add_panel('web_app_default_setups',
		array(
			'priority' => 10,
			'capability' => 'edit_theme_options',
			'theme_supports' => '',
			'title' => esc_html__('Default/Basic Setting','web-app'),
	));

	/****************  Add Default Sections to General Panel ************/
	$wp_customize->get_section('title_tagline')->panel = 'web_app_default_setups'; //priority 20
	$wp_customize->get_section('colors')->panel = 'web_app_default_setups'; //priority 40
	$wp_customize->get_section('background_image')->panel = 'web_app_default_setups'; //priority 80
	$wp_customize->get_section('static_front_page')->panel = 'web_app_default_setups'; //priority 120

  $wp_customize->get_section( 'header_image' )->panel = 'web_app_header_settings';
  $wp_customize->get_section( 'header_image' )->title = esc_html__( 'Innerpages Header Image', 'web-app' );
  $wp_customize->get_section( 'header_image' )->priority = '25';
    
    /************************  Site Identity  ******************/

    $wp_customize->add_setting('site_identity_options', 
        array(
        'default'           => 'title-text',
        'sanitize_callback' => 'web_app_sanitize_select'
        )
    );
    $wp_customize->add_control('site_identity_options', 
        array(    
        'priority' => 20,  
        'label'     => esc_html__('Choose Options', 'web-app'),
        'section'   => 'title_tagline',
        'settings'  => 'site_identity_options',
        'type'      => 'radio',
        'choices'   =>  array(
              'logo-only'     => esc_html__('Logo Only', 'web-app'),
              'logo-text'     => esc_html__('Logo + Tagline', 'web-app'),
              'title-only'    => esc_html__('Title Only', 'web-app'),
              'title-text'    => esc_html__('Title + Tagline', 'web-app')
            )
        )
    );

 /***********************************  Starting Home Page Section **********************************************/

  $wp_customize->add_panel('web_app_homepage_setups',
    array(
      'priority' => 16,
      'capability' => 'edit_theme_options',
      'theme_supports' => '',
      'title' => esc_html__('HomePage Setting ','web-app'),
      ));

    /***********************************  Starting Main Slider **********************************************/

  $wp_customize->add_section('web_app_banner_setups',
    array(
      'priority' => 1,
      'capability' => 'edit_theme_options',
      'theme_supports' => '',
      'title' => esc_html__('Slider Section','web-app'),
      'panel' => 'web_app_homepage_setups'
    ));

    //Banner  Enable/Disable

  $wp_customize->add_setting('web_app_slider_option',
        array(
            'default'           =>  'no',
            'sanitize_callback' =>  'web_app_sanitize_option',
            )
        );
  $wp_customize->add_control('web_app_slider_option',
      array(
            'description'   =>  esc_html__('Enable/Disable Slider Section','web-app'),
            'section'       =>  'web_app_banner_setups',
            'setting'       =>  'web_app_slider_option',
            'priority'      =>  1,
            'type'          =>  'radio',
            'choices'        =>  array(
                'yes'   =>  esc_html__('Yes','web-app'),
                'no'    =>  esc_html__('No','web-app')
              )
         )
     );

  //Select Category For Slider Section

  $wp_customize->add_setting('web_app_slider_section_cat',
    array(
        'default'           =>  0,
        'sanitize_callback' =>  'web_app_sanitize_category_select',
        )
      );
  $wp_customize->add_control('web_app_slider_section_cat',
        array(
        'priority'      =>  2,
        'label'         =>  esc_html__('Select Category For Slider Section','web-app'),
        'section'       =>  'web_app_banner_setups',
        'setting'       =>  'web_app_slider_section_cat',
        'type'          =>  'select',
        'choices'       =>  $web_app_category_lists,
      )
    );

  //Slider Read More Text

  $wp_customize->add_setting('web_app_slider_readmore',
    array(
        'default'           =>  esc_html__('Buy  More','web-app'),
        'sanitize_callback' =>  'sanitize_text_field',
      )
  );

  $wp_customize->add_control('web_app_slider_readmore',
    array(
        'priority'      =>  3,
        'label'         =>  esc_html__('Buy Now','web-app'),
        'section'       =>  'web_app_banner_setups',
        'setting'       =>  'web_app_slider_readmore',
        'type'          =>  'text',  
      )                                     
  );
 $wp_customize->add_setting('web_app_page_bg_image',
  array(
      'default' => '',
      'sanitize_callback' => 'esc_url_raw'
    )
  );
$wp_customize->add_control(new WP_Customize_Image_Control( $wp_customize,'web_app_page_bg_image',
       array(
           'label'      => esc_html__( ' Slider Background Image ', 'web-app' ),
           'section'    => 'web_app_banner_setups',
           'settings'   => 'web_app_page_bg_image',
           'priority' => 10,
       )
   )
);

  // Slider Post Number Count

  $wp_customize->add_setting('web_app_slider_num', 
      array(
        'default' => 5,
          'sanitize_callback' => 'web_app_integer_sanitize',
      )
  );
    
  $wp_customize->add_control('web_app_slider_num',
    array(
        'type' => 'number',
        'label' => esc_html__('No. of Slider','web-app'),
        'section' => 'web_app_banner_setups',
        'setting' => 'web_app_slider_num',
        'input_attrs' => array(
        'min' => 1,
        'max' => 9,
      ),
    )
  );

 /***********************************  Starting Feature Sections  **********************************************/
 
 $wp_customize->add_section('web_app_feature_setups',
    array(
      'priority' => 1,
      'capability' => 'edit_theme_options',
      'theme_supports' => '',
      'title' => esc_html__('Feature Section','web-app'),
      'panel' => 'web_app_homepage_setups'
    ));

    //Banner  Enable/Disable

  $wp_customize->add_setting('web_app_feature_option',
        array(
            'default'           =>  'no',
            'sanitize_callback' =>  'web_app_sanitize_option',
            )
        );
  $wp_customize->add_control('web_app_feature_option',
      array(
            'description'   =>  esc_html__('Enable/Disable Feature Section','web-app'),
            'section'       =>  'web_app_feature_setups',
            'setting'       =>  'web_app_feature_option',
            'priority'      =>  1,
            'type'          =>  'radio',
            'choices'        =>  array(
                'yes'   =>  esc_html__('Yes','web-app'),
                'no'    =>  esc_html__('No','web-app')
              )
         )
     );

  //Select Category For Feature Section

  $wp_customize->add_setting('web_app_feature_section_cat',
    array(
        'default'           =>  0,
        'sanitize_callback' =>  'web_app_sanitize_category_select',
        )
      );
  $wp_customize->add_control('web_app_feature_section_cat',
        array(
        'priority'      =>  2,
        'label'         =>  esc_html__('Select Category For Feature Section','web-app'),
        'section'       =>  'web_app_feature_setups',
        'setting'       =>  'web_app_feature_section_cat',
        'type'          =>  'select',
        'choices'       =>  $web_app_category_lists,
      )
    );

    // Feature  Post Number Count

    $wp_customize->add_setting('web_app_feature_num', 
        array(
          'default' => 3,
            'sanitize_callback' => 'web_app_integer_sanitize',
        )
    );
      
    $wp_customize->add_control('web_app_feature_num',
      array(
          'type' => 'number',
          'label' => esc_html__('No. of Feature Post','web-app'),
          'section' => 'web_app_feature_setups',
          'setting' => 'web_app_feature_num',
          'input_attrs' => array(
          'min' => 1,
          'max' => 9,
        ),
      )
    );  

  /***********************************  Starting Service Sections  **********************************************/
 
 $wp_customize->add_section('web_app_service_setups',
    array(
      'priority' => 1,
      'capability' => 'edit_theme_options',
      'theme_supports' => '',
      'title' => esc_html__('Service Section','web-app'),
      'panel' => 'web_app_homepage_setups'
    ));

    //Banner  Enable/Disable

  $wp_customize->add_setting('web_app_service_option',
        array(
            'default'           =>  'no',
            'sanitize_callback' =>  'web_app_sanitize_option',
            )
        );
  $wp_customize->add_control('web_app_service_option',
      array(
            'description'   =>  esc_html__('Enable/Disable Service Section','web-app'),
            'section'       =>  'web_app_service_setups',
            'setting'       =>  'web_app_service_option',
            'priority'      =>  1,
            'type'          =>  'radio',
            'choices'        =>  array(
                'yes'   =>  esc_html__('Yes','web-app'),
                'no'    =>  esc_html__('No','web-app')
              )
         )
     );
  $wp_customize->add_setting('startup_business_service_page',
    array(
      'default'           =>  0,
      'sanitize_callback' =>  'web_app_sanitize_dropdown_pages',
    )
  );

  $wp_customize->add_control('startup_business_service_page',
    array(
      'priority'=>    2,
      'label'   =>    esc_html__( 'Select Page For Service  Section','web-app' ),
      'section' =>    'web_app_service_setups',
      'setting' =>    'startup_business_service_page',
      'type'    =>    'dropdown-pages',
    )                                     
  );  

  //Select Category For Service  Section

  $wp_customize->add_setting('web_app_service_section_cat',
    array(
        'default'           =>  0,
        'sanitize_callback' =>  'web_app_sanitize_category_select',
        )
      );
  $wp_customize->add_control('web_app_service_section_cat',
        array(
        'priority'      =>  2,
        'label'         =>  esc_html__('Select Category For Service Section','web-app'),
        'section'       =>  'web_app_service_setups',
        'setting'       =>  'web_app_service_section_cat',
        'type'          =>  'select',
        'choices'       =>  $web_app_category_lists,
      )
    );

    // Service  Post Number Count

    $wp_customize->add_setting('web_app_service_num', 
        array(
          'default' => 5,
            'sanitize_callback' => 'web_app_integer_sanitize',
        )
    );
      
    $wp_customize->add_control('web_app_service_num',
      array(
          'priority'      =>  3,
          'type' => 'number',
          'label' => esc_html__('No. of Service Post','web-app'),
          'section' => 'web_app_service_setups',
          'setting' => 'web_app_service_num',
          'input_attrs' => array(
          'min' => 1,
          'max' => 9,
        ),
      )
    );  

  //Select Category For Service  Section

  $wp_customize->add_setting('web_app_service_two_section_cat',
    array(
        'default'           =>  0,
        'sanitize_callback' =>  'web_app_sanitize_category_select',
        )
      );
  $wp_customize->add_control('web_app_service_two_section_cat',
        array(
        'priority'      =>  4,
        'label'         =>  esc_html__('Select Category For Service  Slider Image','web-app'),
        'section'       =>  'web_app_service_setups',
        'setting'       =>  'web_app_service_two_section_cat',
        'type'          =>  'select',
        'choices'       =>  $web_app_category_lists,
      )
    );

    // Service  Post Number Count

    $wp_customize->add_setting('web_app_service_two_num', 
        array(
          'default' => 5,
            'sanitize_callback' => 'web_app_integer_sanitize',
        )
    );
      
    $wp_customize->add_control('web_app_service_two_num',
      array(
          'priority'      =>  5,
          'type' => 'number',
          'label' => esc_html__('No. of Service Image  Post','web-app'),
          'section' => 'web_app_service_setups',
          'setting' => 'web_app_service_two_num',
          'input_attrs' => array(
          'min' => 1,
          'max' => 9,
        ),
      )
    ); 

  /***********************************  Starting Call To  Sections  **********************************************/
 
  $wp_customize->add_section('web_app_call_to_setups',
    array(
      'priority' => 1,
      'capability' => 'edit_theme_options',
      'theme_supports' => '',
      'title' => esc_html__('Call To Section','web-app'),
      'panel' => 'web_app_homepage_setups'
    ));

    //Banner  Enable/Disable

  $wp_customize->add_setting('web_app_call_option',
        array(
            'default'           =>  'no',
            'sanitize_callback' =>  'web_app_sanitize_option',
            )
        );
  $wp_customize->add_control('web_app_call_option',
      array(
          'description'   =>  esc_html__('Enable/Disable Call To Section','web-app'),
          'section'       =>  'web_app_call_to_setups',
          'setting'       =>  'web_app_call_option',
          'priority'      =>  1,
          'type'          =>  'radio',
          'choices'        =>  array(
              'yes'   =>  esc_html__('Yes','web-app'),
              'no'    =>  esc_html__('No','web-app')
            )
         )
     );

  $wp_customize->add_setting('web_app_call_to_title_one',
        array(
          'default'           =>  '',
          'sanitize_callback' =>  'sanitize_text_field',
            )
        );
  $wp_customize->add_control( 'web_app_call_to_title_one',
    array(
      'priority'      =>  2,
      'label'         =>  esc_html__('Call To  Title','web-app'),
      'section'       =>  'web_app_call_to_setups',
      'setting'       =>  'web_app_call_to_title_one',
      'type'          =>  'text',  
    )                                     
  );

   $wp_customize->add_setting('web_app_call_to_page',
    array(
      'default'           =>  0,
      'sanitize_callback' =>  'web_app_sanitize_dropdown_pages',
    )
  );

  $wp_customize->add_control('web_app_call_to_page',
    array(
      'priority'=>    3,
      'label'   =>    esc_html__( 'Select Page For Call To Action','web-app' ),
      'section' =>    'web_app_call_to_setups',
      'setting' =>    'web_app_call_to_page',
      'type'    =>    'dropdown-pages',
    )                                     
  );  

  $wp_customize->add_setting('web_app_call_to_title_two',
        array(
          'default'           =>  '',
          'sanitize_callback' =>  'sanitize_text_field',
            )
        );
  $wp_customize->add_control( 'web_app_call_to_title_two',
    array(
      'priority'      =>  4,
      'label'         =>  esc_html__('Call To  Title','web-app'),
      'section'       =>  'web_app_call_to_setups',
      'setting'       =>  'web_app_call_to_title_two',
      'type'          =>  'text',  
    )                                     
  );

   $wp_customize->add_setting('web_app_call_to_page_two',
    array(
      'default'           =>  0,
      'sanitize_callback' =>  'web_app_sanitize_dropdown_pages',
    )
  );

  $wp_customize->add_control('web_app_call_to_page_two',
    array(
      'priority'=>    5,
      'label'   =>    esc_html__( 'Select Page For Call To Action','web-app' ),
      'section' =>    'web_app_call_to_setups',
      'setting' =>    'web_app_call_to_page_two',
      'type'    =>    'dropdown-pages',
    )                                     
  );  

  $wp_customize->add_setting('web_app_call_to_image',
    array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw'
      )
    );
  $wp_customize->add_control(new WP_Customize_Image_Control( $wp_customize,'web_app_call_to_image',
         array(
             'label'      => __( 'Call To Background  Image ', 'web-app' ),
             'section'    => 'web_app_call_to_setups',
             'settings'   => 'web_app_call_to_image',
             'priority' => 10,
         )
     )
  );

/***********************************  Starting Testimonial   Sections  **********************************************/

  $wp_customize->add_section('web_app_testimonial_setups',
  array(
    'priority' => 1,
    'capability' => 'edit_theme_options',
    'theme_supports' => '',
    'title' => esc_html__(' Testimonial Section','web-app'),
    'panel' => 'web_app_homepage_setups'
  ));

  // Testimoial  Enable/Disable

  $wp_customize->add_setting('web_app_testimonial_option',
        array(
            'default'           =>  'no',
            'sanitize_callback' =>  'web_app_sanitize_option',
            )
        );
  $wp_customize->add_control('web_app_testimonial_option',
      array(
          'description'   =>  esc_html__('Enable/Disable Testimonial Section','web-app'),
          'section'       =>  'web_app_testimonial_setups',
          'setting'       =>  'web_app_testimonial_option',
          'priority'      =>  1,
          'type'          =>  'radio',
          'choices'        =>  array(
              'yes'   =>  esc_html__('Yes','web-app'),
              'no'    =>  esc_html__('No','web-app')
            )
         )
     );


  $wp_customize->add_setting('web_app_test_title',
        array(
          'default'           =>  '',
          'sanitize_callback' =>  'sanitize_text_field',
            )
        );
  $wp_customize->add_control( 'web_app_test_title',
    array(
      'priority'      =>  2,
      'label'         =>  esc_html__('Testimonial Section  Title','web-app'),
      'section'       =>  'web_app_testimonial_setups',
      'setting'       =>  'web_app_test_title',
      'type'          =>  'text',  
    )                                     
  );

  //Select Category For Testimonial  Section

  $wp_customize->add_setting('web_app_testimonial_section_cat',
    array(
        'default'           =>  0,
        'sanitize_callback' =>  'web_app_sanitize_category_select',
        )
      );
  $wp_customize->add_control('web_app_testimonial_section_cat',
        array(
        'priority'      =>  4,
        'label'         =>  esc_html__('Select Category For Testimonial Section ','web-app'),
        'section'       =>  'web_app_testimonial_setups',
        'setting'       =>  'web_app_testimonial_section_cat',
        'type'          =>  'select',
        'choices'       =>  $web_app_category_lists,
      )
    );

    // Testimonial  Post Number Count

    $wp_customize->add_setting('web_app_testimonial_num', 
        array(
          'default' => 5,
            'sanitize_callback' => 'web_app_integer_sanitize',
        )
    );
      
    $wp_customize->add_control('web_app_testimonial_num',
      array(
          'priority'      =>  5,
          'type' => 'number',
          'label' => esc_html__('No. of Testimonial Category Post','web-app'),
          'section' => 'web_app_testimonial_setups',
          'setting' => 'web_app_testimonial_num',
          'input_attrs' => array(
          'min' => 1,
          'max' => 9,
        ),
      )
    ); 

  // Testimoial Review  Enable/Disable

  $wp_customize->add_setting('web_app_testimonial_review_option',
        array(
            'default'           =>  'no',
            'sanitize_callback' =>  'web_app_sanitize_option',
            )
        );
  $wp_customize->add_control('web_app_testimonial_review_option',
      array(
          'description'   =>  esc_html__('Enable/Disable Testimonial Review ','web-app'),
          'section'       =>  'web_app_testimonial_setups',
          'setting'       =>  'web_app_testimonial_review_option',
          'priority'      =>  11,
          'type'          =>  'radio',
          'choices'        =>  array(
              'yes'   =>  esc_html__('Yes','web-app'),
              'no'    =>  esc_html__('No','web-app')
            )
         )
     );

   // Testimoial Doubble Quotes  Enable/Disable

  $wp_customize->add_setting('web_app_testimonial_quote_option',
        array(
            'default'           =>  'no',
            'sanitize_callback' =>  'web_app_sanitize_option',
            )
        );
  $wp_customize->add_control('web_app_testimonial_quote_option',
      array(
          'description'   =>  esc_html__('Enable/Disable Testimonial Quotes','web-app'),
          'section'       =>  'web_app_testimonial_setups',
          'setting'       =>  'web_app_testimonial_quote_option',
          'priority'      =>  12,
          'type'          =>  'radio',
          'choices'        =>  array(
              'yes'   =>  esc_html__('Yes','web-app'),
              'no'    =>  esc_html__('No','web-app')
            )
         )
     );
  /***********************************  Starting Blog Sections  **********************************************/
  
  $wp_customize->add_section('web_app_blog_setups',
     array(
       'priority' => 1,
       'capability' => 'edit_theme_options',
       'theme_supports' => '',
       'title' => esc_html__('Blog Section','web-app'),
       'panel' => 'web_app_homepage_setups'
     ));

     //Banner  Enable/Disable

   $wp_customize->add_setting('web_app_blog_option',
         array(
             'default'           =>  'no',
             'sanitize_callback' =>  'web_app_sanitize_option',
             )
         );
   $wp_customize->add_control('web_app_blog_option',
       array(
             'description'   =>  esc_html__('Enable/Disable Feature Section','web-app'),
             'section'       =>  'web_app_blog_setups',
             'setting'       =>  'web_app_blog_option',
             'priority'      =>  1,
             'type'          =>  'radio',
             'choices'        =>  array(
                 'yes'   =>  esc_html__('Yes','web-app'),
                 'no'    =>  esc_html__('No','web-app')
               )
          )
      );

   //Select Category For Feature Section

   $wp_customize->add_setting('web_app_blog_section_cat',
     array(
         'default'           =>  0,
         'sanitize_callback' =>  'web_app_sanitize_category_select',
         )
       );
   $wp_customize->add_control('web_app_blog_section_cat',
         array(
         'priority'      =>  2,
         'label'         =>  esc_html__('Select Category For Feature Section','web-app'),
         'section'       =>  'web_app_blog_setups',
         'setting'       =>  'web_app_blog_section_cat',
         'type'          =>  'select',
         'choices'       =>  $web_app_category_lists,
       )
     );

     // Feature  Post Number Count

     $wp_customize->add_setting('web_app_blog_num', 
         array(
           'default' => 3,
             'sanitize_callback' => 'web_app_integer_sanitize',
         )
     );
       
     $wp_customize->add_control('web_app_blog_num',
       array(
           'type' => 'number',
           'label' => esc_html__('No. of Feature Post','web-app'),
           'section' => 'web_app_blog_setups',
           'setting' => 'web_app_blog_num',
           'input_attrs' => array(
           'min' => 1,
           'max' => 9,
         ),
       )
     );  
    $wp_customize->add_setting('web_app_blog_title',
      array(
          'default'           =>  esc_html__('Blog Section Title','web-app'),
          'sanitize_callback' =>  'sanitize_text_field',
        )
    );

    $wp_customize->add_control('web_app_blog_title',
      array(
          'priority'      =>  3,
          'label'         =>  esc_html__('Buy Now','web-app'),
          'section'       =>  'web_app_blog_setups',
          'setting'       =>  'web_app_blog_title',
          'type'          =>  'text',  
        )                                     
    );  
    $wp_customize->add_setting('web_app_blog_readmore',
      array(
          'default'           =>  esc_html__('Buy  More','web-app'),
          'sanitize_callback' =>  'sanitize_text_field',
        )
    );

    $wp_customize->add_control('web_app_blog_readmore',
      array(
          'priority'      =>  3,
          'label'         =>  esc_html__('Read More','web-app'),
          'section'       =>  'web_app_blog_setups',
          'setting'       =>  'web_app_blog_readmore',
          'type'          =>  'text',  
        )                                     
    ); 

/***********************************  Starting Footer   Sections  **********************************************/

  $wp_customize->add_section('web_app_footer_setups',
  array(
    'priority' => 1,
    'capability' => 'edit_theme_options',
    'theme_supports' => '',
    'title' => esc_html__(' Footer Section','web-app'),
    'panel' => 'web_app_homepage_setups'
  ));

  $wp_customize->add_setting('web_app_footer_bg_image',
    array(
      'default' => '',
      'sanitize_callback' => 'esc_url_raw'
    )
  );
  $wp_customize->add_control(new WP_Customize_Image_Control( $wp_customize,'web_app_footer_bg_image',
    array(
      'label'      => esc_html__( ' Footer Background Image Image ', 'web-app' ),
      'section'    => 'web_app_footer_setups',
      'settings'   => 'web_app_footer_bg_image',
      'priority' => 10,
    )
  )
  );

/*********************************** Header  Sections Settings   **********************************************/


$wp_customize->add_panel('web_app_header_settings',
  array(
    'priority' => 15,
    'capability' => 'edit_theme_options',
    'theme_supports' => '',
    'title' => esc_html__('Header Setting ','web-app'),
  ));

$wp_customize->add_section('web_app_Header_optons_setups',
array(
  'priority' => 2,
  'capability' => 'edit_theme_options',
  'theme_supports' => '',
  'title' => esc_html__(' Header Options','web-app'),
  'panel' => 'web_app_header_settings'
));

  // Header Search  Enable/Disable

  $wp_customize->add_setting('web_app_search_option',
        array(
            'default'           =>  'no',
            'sanitize_callback' =>  'web_app_sanitize_option',
            )
        );
  $wp_customize->add_control('web_app_search_option',
      array(
          'description'   =>  esc_html__('Enable/Disable Header Search ','web-app'),
          'section'       =>  'web_app_Header_optons_setups',
          'setting'       =>  'web_app_search_option',
          'priority'      =>  3,
          'type'          =>  'radio',
          'choices'        =>  array(
              'yes'   =>  esc_html__('Yes','web-app'),
              'no'    =>  esc_html__('No','web-app')
            )
         )
     );

  //Breadcrumb  Enable/Disable
  $wp_customize->add_setting('web_app_breadcrumb_option',
        array(
            'default'           =>  'no',
            'sanitize_callback' =>  'web_app_sanitize_option',
            )
        );
  $wp_customize->add_control('web_app_breadcrumb_option',
      array(
            'description'   =>  esc_html__('Enable/Disable Breadcrumb','web-app'),
            'section'       =>  'web_app_Header_optons_setups',
            'setting'       =>  'web_app_breadcrumb_option',
            'priority'      =>  5,
            'type'          =>  'radio',
            'choices'        =>  array(
                'yes'   =>  esc_html__('Yes','web-app'),
                'no'    =>  esc_html__('No','web-app')
              )
         )
     );
  $wp_customize->add_setting('web_app_404_bg_image',
   array(
    'default' => '',
    'sanitize_callback' => 'esc_url_raw'
    )
  );
  $wp_customize->add_control(new WP_Customize_Image_Control( $wp_customize,'web_app_404_bg_image',
   array(
    'label'      => esc_html__( ' 404 Page  Image ', 'web-app' ),
    'section'    => 'web_app_Header_optons_setups',
    'settings'   => 'web_app_404_bg_image',
    'priority' => 70,
       )
    )
  );

  $wp_customize->add_setting('web_app_weblayout',
       array(
          'default' => 'fullwidth',
          'capability' => 'edit_theme_options',
          'sanitize_callback' => 'web_app_webpagelayout',
      ));
  $wp_customize->add_control('web_app_weblayout',
       array(
          'type' => 'radio',
          'label' => esc_html__('Choose The Layout That You Want', 'web-app'),
          'section' => 'web_app_Header_optons_setups',
          'setting' => 'web_app_weblayout',
          'choices' => array(
          'fullwidth' => esc_html__('Full  Layout', 'web-app'),
          'box-layout' => esc_html__('Boxed Layout', 'web-app')
          )
      ));

  

  /***********************************  Footer  Sections Settings   **********************************************/


  $wp_customize->add_panel('web_app_footer_settings',
    array(
      'priority' => 16,
      'capability' => 'edit_theme_options',
      'theme_supports' => '',
      'title' => esc_html__('Footer Setting ','web-app'),
    ));

  $wp_customize->add_section('web_app_footer_optons_setups',
  array(
    'priority' => 1,
    'capability' => 'edit_theme_options',
    'theme_supports' => '',
    'title' => esc_html__(' Footer  Options','web-app'),
    'panel' => 'web_app_footer_settings'
  ));

  // Footer Sections  Enable/Disable

  $wp_customize->add_setting('web_app_footer_social_option',
        array(
            'default'           =>  'no',
            'sanitize_callback' =>  'web_app_sanitize_option',
            )
        );
  $wp_customize->add_control('web_app_footer_social_option',
      array(
          'description'   =>  esc_html__('Enable/Disable Footer Social ','web-app'),
          'section'       =>  'web_app_footer_optons_setups',
          'setting'       =>  'web_app_footer_social_option',
          'priority'      =>  11,
          'type'          =>  'radio',
          'choices'        =>  array(
              'yes'   =>  esc_html__('Yes','web-app'),
              'no'    =>  esc_html__('No','web-app')
            )
         )
     );

      // Footer Sections  Enable/Disable

    $wp_customize->add_setting('web_app_footer_menu_option',
          array(
              'default'           =>  'no',
              'sanitize_callback' =>  'web_app_sanitize_option',
              )
          );
    $wp_customize->add_control('web_app_footer_menu_option',
        array(
            'description'   =>  esc_html__('Enable/Disable Footer Menu ','web-app'),
            'section'       =>  'web_app_footer_optons_setups',
            'setting'       =>  'web_app_footer_menu_option',
            'priority'      =>  11,
            'type'          =>  'radio',
            'choices'        =>  array(
                'yes'   =>  esc_html__('Yes','web-app'),
                'no'    =>  esc_html__('No','web-app')
              )
           )
       ); 


  /***********************************  Footer  Sections Settings   **********************************************/

  // Footer Copyright Section

   $wp_customize->add_section('web_app_footer_optons_setupsss',
  array(
    'priority' => 1,
    'capability' => 'edit_theme_options',
    'theme_supports' => '',
    'title' => esc_html__(' Footer  Copyright','web-app'),
    'panel' => 'web_app_footer_settings'
  ));



  $wp_customize->add_setting( 'web_app_copyright_text',
    array(
      'default' => esc_html__( '2018 Web App', 'web-app' ),
      'sanitize_callback' => 'sanitize_text_field',
    )
  );
  $wp_customize->add_control('web_app_copyright_text',
    array(
      'type' => 'text',
      'label' => esc_html__( 'Copyright Text', 'web-app' ),
      'section' => 'web_app_footer_optons_setupsss',
      'priority' => 5
    )
  );


//Archive Page Settings panel

$wp_customize->add_panel('web_app_archive_section', 
  array(
    'capabitity' => 'edit_theme_options',
    'priority' => 38,
    'title' => __('Archive Page Settings', 'web-app')
    )
);

$wp_customize->add_section('web_app_archive',
      array(
        'title' => __('Archive Sidebar Settings', 'web-app'),
        'panel' => 'web_app_archive_section'
        )
    );

  $wp_customize->add_setting('web_app_archive_setting_sidebar_option',
      array(
        'default' =>  'sidebar-right',
        'sanitize_callback' =>  'web_app_radio_sanitize_archive_sidebar'
        )
      );  

  $wp_customize->add_control('web_app_archive_setting_sidebar_option',
      array(
        'description' => __('Choose the sidebar Layout for the archive page','web-app'),
        'section' => 'web_app_archive',
        'type'    =>  'radio',
        'choices' =>  array(
            'sidebar-left' =>  __('Sidebar Left','web-app'),
            'sidebar-right' =>  __('Sidebar Right','web-app'),
            'sidebar-both' =>  __('Sidebar Both','web-app'),
            'sidebar-no' =>  __('Sidebar No','web-app'),
          )
        )
    );

  $wp_customize->add_setting('web_app_section_date',
      array(
          'default'           =>  'no',
          'sanitize_callback' =>  'web_app_sanitize_option',
          )
      );
  $wp_customize->add_control('web_app_section_date',array(
          'description'   =>  esc_html__('Enable/Disable Date On Single Post','web-app'),
          'section'       =>  'web_app_archive',
          'setting'       =>  'web_app_section_date',
          'priority'      =>  3,
          'type'          =>  'radio',
          'choices'        =>  array(
              'yes'   =>  esc_html__('Yes','web-app'),
              'no'    =>  esc_html__('No','web-app')
              )
          )
      );
    $wp_customize->add_setting('web_app_archive_submit',array(
                  'default'           =>  esc_html__('Read More ','web-app'),
                  'sanitize_callback' =>  'sanitize_text_field',
                  )
              );

   $wp_customize->add_control('web_app_archive_submit',array(
                  'priority'      =>  4,
                  'label'         =>  esc_html__('Read More ','web-app'),
                  'section'       =>  'web_app_archive',
                  'setting'       =>  'web_app_archive_submit',
                  'type'          =>  'text',  
                  )                                     
              );
  $wp_customize->add_panel('web_app_faq_template_settings',
    array(
    'priority' => 16,
    'capability' => 'edit_theme_options',
    'theme_supports' => '',
    'title' => esc_html__('Template Setting ','web-app'),  )
  );


  }
add_action( 'customize_register', 'web_app_custom_customize_register' ); 