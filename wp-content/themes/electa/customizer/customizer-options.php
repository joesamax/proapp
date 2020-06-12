<?php
/**
 * Defines customizer options
 *
 * @package Customizer Library Kaira
 */

function customizer_library_kaira_options() {

	// Theme defaults
	$primary_color = '#4965A0';
	$secondary_color = '#3e578b';
    
    $body_font_color = '#7B7D80';
    $heading_font_color = '#5A5A5A';

	// Stores all the controls that will be added
	$options = array();

	// Stores all the sections to be added
	$sections = array();

	// Adds the sections to the $options array
	$options['sections'] = $sections;
    
    $section = 'header_image';

    $sections[] = array(
        'id' => $section,
        'title' => __( 'Site Logo', 'electa' ),
        'priority' => '25'
    );
    $options['kra-header-logo-max-width'] = array(
        'id' => 'kra-header-logo-max-width',
        'label'   => __( 'Set a max-width for the logo', 'electa' ),
        'section' => $section,
        'type'    => 'number',
        'description' => __( 'This only applies if a logo image is uploaded', 'electa' )
    );
    
    // Header Settings
    $section = 'kra-header';

    $sections[] = array(
        'id' => $section,
        'title' => __( 'Header Options', 'electa' ),
        'priority' => '30'
    );
    
    $options['kra-header-slogan'] = array(
        'id' => 'kra-header-slogan',
        'label'   => __( 'Show Slogan', 'electa' ),
        'section' => $section,
        'type'    => 'checkbox',
        'default' => 0,
    );
    $options['kra-header-search'] = array(
        'id' => 'kra-header-search',
        'label'   => __( 'Show Search', 'electa' ),
        'section' => $section,
        'type'    => 'checkbox',
        'default' => 0,
    );
    $options['kra-header-social-above-nav'] = array(
        'id' => 'kra-header-social-above-nav',
        'label'   => __( 'Social Icons Above Navigation', 'electa' ),
        'section' => $section,
        'type'    => 'checkbox',
        'default' => 0,
    );
    $options['kra-upsell-header'] = array(
        'id' => 'kra-upsell-header',
        'section' => $section,
        'type'    => 'upsell',
        'description' => __( '<b>Premium Extra Features:</b><br />- Change Header to Top Layout<br />- Settings to adjust Site Title font and sizing<br />- Setting to adjust Site Tagline sizing and spacing<br />- Remove All Page titles<br />- Enable a sticky header<br />- Align Navigation - Left\Center\Right<br />- Remove Navigation lines', 'electa' )
    );
    
    
    // Homepage Settings
    $section = 'kra-homepage';

    $sections[] = array(
        'id' => $section,
        'title' => __( 'Home Layout Options', 'electa' ),
        'priority' => '30'
    );
    
    $options['kra-home-blocks-layout'] = array(
        'id' => 'kra-home-blocks-layout',
        'label'   => __( 'Enable home blocks layout', 'electa' ),
        'section' => $section,
        'type'    => 'checkbox',
        'description' => __( 'Enable this to change the layout of the home page to list posts as blocks. This shows ALL posts', 'electa' ),
        'default' => 0,
    );
    $options['kra-home-cats'] = array(
        'id' => 'kra-home-cats',
        'label'   => __( 'Home Categories', 'electa' ),
        'section' => $section,
        'type'    => 'text',
        'description' => __( 'Enter the ID\'s of the post categories you want to display in the home blocks.<br />Eg: "13,17,19"<br /><br />Get the ID\'s at <b>Posts -> Categories</b><br /><br />Leaving it blank displays all posts.', 'electa' )
    );
    $options['kra-upsell-homepage'] = array(
        'id' => 'kra-upsell-homepage',
        'section' => $section,
        'type'    => 'upsell',
        'description' => __( '<b>Premium Extra Features:</b><br />- Setting to change home blocks columns layout', 'electa' )
    );


	// Colors
	$section = 'kra-styling';
    $font_choices = customizer_library_get_font_choices();

	$sections[] = array(
		'id' => $section,
		'title' => __( 'Styling Options', 'electa' ),
		'priority' => '40'
	);

	$options['kra-main-color'] = array(
		'id' => 'kra-main-color',
		'label'   => __( 'Main Color', 'electa' ),
		'section' => $section,
		'type'    => 'color',
		'default' => $primary_color,
	);
	$options['kra-main-color-hover'] = array(
		'id' => 'kra-main-color-hover',
		'label'   => __( 'Secondary Color', 'electa' ),
		'section' => $section,
		'type'    => 'color',
		'default' => $secondary_color,
	);
    
    
    $options['kra-body-font'] = array(
        'id' => 'kra-body-font',
        'label'   => __( 'Body Font', 'electa' ),
        'section' => $section,
        'type'    => 'select',
        'choices' => $font_choices,
        'default' => 'Open Sans'
    );
    $options['kra-body-font-color'] = array(
        'id' => 'kra-body-font-color',
        'label'   => __( 'Body Font Color', 'electa' ),
        'section' => $section,
        'type'    => 'color',
        'default' => $body_font_color,
    );
    $options['kra-heading-font'] = array(
        'id' => 'kra-heading-font',
        'label'   => __( 'Headings Font', 'electa' ),
        'section' => $section,
        'type'    => 'select',
        'choices' => $font_choices,
        'default' => 'Roboto'
    );
    $options['kra-heading-font-color'] = array(
        'id' => 'kra-heading-font-color',
        'label'   => __( 'Heading Font Color', 'electa' ),
        'section' => $section,
        'type'    => 'color',
        'default' => $heading_font_color,
    );
    
    $options['kra-custom-css'] = array(
        'id' => 'kra-custom-css',
        'label'   => __( 'Custom CSS', 'electa' ),
        'section' => $section,
        'type'    => 'textarea',
        'description' => __( 'Add custom CSS to your theme. For advanced custom styling we recommend using a <a href="https://wordpress.org/plugins/so-css/" target="_blank">Custom CSS plugin</a>', 'electa' )
    );
    
    $options['kra-upsell-styling'] = array(
        'id' => 'kra-upsell-styling',
        'section' => $section,
        'type'    => 'upsell',
        'description' => __( '<b>Premium Extra Features:</b><br />- Change header background and font colors<br />- Select between mobile navigation skins - Dark / Light<br />- Customize the colors for the mobile navigation<br />- Adjust block layout title sizes', 'electa' )
    );
    
    
    // Blog Settings
    $section = 'kra-blog';

    $sections[] = array(
        'id' => $section,
        'title' => __( 'Blog Layout Options', 'electa' ),
        'priority' => '30'
    );
    
    $options['kra-blog-blocks-layout'] = array(
        'id' => 'kra-blog-blocks-layout',
        'label'   => __( 'Enable blog blocks layout', 'electa' ),
        'section' => $section,
        'type'    => 'checkbox',
        'description' => __( 'Enable this to change the layout of the blog page to list posts as blocks. This shows ALL posts', 'electa' ),
        'default' => 0,
    );
    $options['kra-blog-title'] = array(
        'id' => 'kra-blog-title',
        'label'   => __( 'Blog Page Title', 'electa' ),
        'section' => $section,
        'type'    => 'text',
        'default' => 'Blog'
    );
    $options['kra-blog-cats'] = array(
        'id' => 'kra-blog-cats',
        'label'   => __( 'Blog Categories', 'electa' ),
        'section' => $section,
        'type'    => 'text',
        'description' => __( 'Enter the ID\'s of the post categories you want to EXCLUDE from the blog, with a minus(-) before.<br />Eg: "-13,-17,-19"<br /><br /> Get the ID\'s at <b>Posts -> Categories</b>', 'electa' )
    );
    $options['kra-upsell-blog'] = array(
        'id' => 'kra-upsell-blog',
        'section' => $section,
        'type'    => 'upsell',
        'description' => __( '<b>Premium Extra Features:</b><br />- Change Blog Post Image Size/Proportion<br />- Setting to change blog blocks columns layout<br />- Enable blocks layout on Archive pages<br /><br />- Custom WooCommerce Styling<br />- Set WooCommerce Shop page to full width<br />- Set WC products per row<br />- Set WC products per page', 'electa' )
    );
    
    
    // Social Settings
    $section = 'kra-social';

    $sections[] = array(
        'id' => $section,
        'title' => __( 'Social Links', 'electa' ),
        'priority' => '40'
    );
    
    $options['kra-social-email'] = array(
        'id' => 'kra-social-email',
        'label'   => __( 'Email Address', 'electa' ),
        'section' => $section,
        'type'    => 'text',
    );
    $options['kra-social-facebook'] = array(
        'id' => 'kra-social-facebook',
        'label'   => __( 'Facebook', 'electa' ),
        'section' => $section,
        'type'    => 'text',
    );
    $options['kra-social-twitter'] = array(
        'id' => 'kra-social-twitter',
        'label'   => __( 'Twitter', 'electa' ),
        'section' => $section,
        'type'    => 'text',
    );
    
    $options['kra-upsell-social'] = array(
        'id' => 'kra-upsell-social',
        'section' => $section,
        'type'    => 'upsell',
        'description' => __( '<b>Premium Extra Features:</b><br />- Over 18 different social profile links available<br />- Setting to add any social link required', 'electa' )
    );
    
    
    // Site Text Settings
    $section = 'kra-website';

    $sections[] = array(
        'id' => $section,
        'title' => __( 'Website Text', 'electa' ),
        'priority' => '50'
    );
    
    $options['kra-website-error-head'] = array(
        'id' => 'kra-website-error-head',
        'label'   => __( '404 Error Page Heading', 'electa' ),
        'section' => $section,
        'type'    => 'text',
        'default' => __( 'Oops! That page can\'t be found.', 'electa'),
        'description' => __( 'Enter the heading for the 404 Error page', 'electa' )
    );
    $options['kra-website-error-msg'] = array(
        'id' => 'kra-website-error-msg',
        'label'   => __( 'Error 404 Message', 'electa' ),
        'section' => $section,
        'type'    => 'textarea',
        'default' => __( 'The page you are looking for can\'t be found. Please select one of the options below.', 'electa'),
        'description' => __( 'Enter the default text on the 404 error page (Page not found)', 'electa' )
    );
    $options['kra-website-nosearch-msg'] = array(
        'id' => 'kra-website-nosearch-msg',
        'label'   => __( 'No Search Results', 'electa' ),
        'section' => $section,
        'type'    => 'textarea',
        'default' => __( 'Sorry, but nothing matched your search terms. Please try again with some different keywords or return to home.', 'electa'),
        'description' => __( 'Enter the default text for when no search results are found', 'electa' )
    );
    
    $options['kra-upsell-website'] = array(
        'id' => 'kra-upsell-website',
        'section' => $section,
        'type'    => 'upsell',
        'description' => __( '<b>Premium Extra Features:</b><br />- Change site attribution text to your own copy', 'electa' )
    );

	// Adds the sections to the $options array
	$options['sections'] = $sections;

	$customizer_library = Customizer_Library::Instance();
	$customizer_library->add_options( $options );

	// To delete custom mods use: customizer_library_remove_theme_mods();

}
add_action( 'init', 'customizer_library_kaira_options' );
