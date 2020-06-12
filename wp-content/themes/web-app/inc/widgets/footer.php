<?php
/** 
 *
 *  Footer 
 * @package web-app
 */

add_action('widgets_init', 'web_app_top_register');

function web_app_top_register() {
    register_widget('web_app_top_Widget');
}

class web_app_top_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'web_app_top_Widget', 
            esc_html__('Web App : Footer', 'web-app'), array(
            'description' => esc_html__('This Widget show  Footer Content', 'web-app')
            )
        );
    }

    //==========================================================================================================//

    /**
     * Helper function that holds widget fields
     * Array is used in update and form functions
     */

    private function widget_fields() {
        $fields = array(
            'term_title' => array(
                'web_app_widgets_name' => 'term_title',
                'web_app_widgets_title' => esc_html__('Footer Title', 'web-app'),
                'web_app_widgets_field_type' => 'text',
            ),

			'button_text_one' => array(
			'web_app_widgets_name' => 'button_text_one',
			'web_app_widgets_title' => esc_html__('Button Text One', 'web-app'),
			'web_app_widgets_field_type' => 'text',
			),

			'button_text_one_one' => array(
			'web_app_widgets_name' => 'button_text_one_one',
			'web_app_widgets_title' => esc_html__('Button Text One Suffix ', 'web-app'),
			'web_app_widgets_field_type' => 'text',
			),

			'button_link_one' => array(
			'web_app_widgets_name' => 'button_link_one',
			'web_app_widgets_title' => esc_html__('Button Link One', 'web-app'),
			'web_app_widgets_field_type' => 'text',
			),

			'term_feature_icon_one' => array(
			'web_app_widgets_name' => 'term_feature_icon_one',
			'web_app_widgets_title' => esc_html__('Button Icon Class', 'web-app'),
			'web_app_widgets_field_type' => 'text',
			),

			'button_text_two' => array(
			'web_app_widgets_name' => 'button_text_two',
			'web_app_widgets_title' => esc_html__('Button Text Two', 'web-app'),
			'web_app_widgets_field_type' => 'text',
			),

			'button_text_one_two' => array(
			'web_app_widgets_name' => 'button_text_one_two',
			'web_app_widgets_title' => esc_html__('Button Text Two Suffix ', 'web-app'),
			'web_app_widgets_field_type' => 'text',
			),

			'button_link_two' => array(
			'web_app_widgets_name' => 'button_link_two',
			'web_app_widgets_title' => esc_html__('Button Link Two', 'web-app'),
			'web_app_widgets_field_type' => 'text',
			),

			'term_feature_icon_two' => array(
			'web_app_widgets_name' => 'term_feature_icon_two',
			'web_app_widgets_title' => esc_html__('Button Icon Class', 'web-app'),
			'web_app_widgets_field_type' => 'text',
			),

            'term_description' => array(
            'web_app_widgets_name' => 'term_description',
            'web_app_widgets_title' => esc_html__('Footer Description', 'web-app'),
            'web_app_widgets_field_type' => 'text',
            ),

			'term_image' => array(
			'web_app_widgets_name' => 'term_image',
			'web_app_widgets_title' => esc_html__(' Footer Image ', 'web-app'),
			'web_app_widgets_field_type' => 'upload',
			),

			'term_title_shortcode' => array(
            'web_app_widgets_name' => 'term_title_shortcode',
            'web_app_widgets_title' => esc_html__('Footer Contact Shortcode', 'web-app'),
            'web_app_widgets_field_type' => 'text',
            ),

        );

        return $fields;
    }

    //==========================================================================================================//

    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget($args, $instance) {
        extract($args);
        
        $title_widget = apply_filters( 'widget_title', empty( $instance['term_title'] ) ? '' : $instance['term_title'], $instance, $this->id_base );
        $term_description = isset( $instance['term_description'] ) ? $instance['term_description'] : '';

        $term_shortcode = isset( $instance['term_shortcode'] ) ? $instance['term_shortcode'] : '';

        $button_text_one = $instance['button_text_one'];
        $button_text_one_one = $instance['button_text_one_one'];
        $button_link_one = $instance['button_link_one'];
        $term_feature_icon_one = isset( $instance['term_feature_icon_one'] ) ? $instance['term_feature_icon_one'] : '';

       	$button_text_two = $instance['button_text_two'];
   		$button_text_one_two = $instance['button_text_one_two'];
        $button_link_two = $instance['button_link_two'];
     	$term_feature_icon_two = isset( $instance['term_feature_icon_two'] ) ? $instance['term_feature_icon_two'] : '';

     	$term_image = isset( $instance['term_image'] ) ? $instance['term_image'] : '' ;

     	$term_title_shortcode = isset( $instance['term_title_shortcode'] ) ? $instance['term_title_shortcode'] : '';
        ?>

		<div class="custom-col-6">

			<!-- ******************************************************* Footer Title And  Buttons  *****************-->

			<aside class="widget">

				<h2 class="widget-title">

					<?php if($title_widget){
					echo  esc_html($title_widget);
					} ?>

				</h2>

				<div class="widget-btn-wrapper">

					<a href="<?php echo esc_url($button_link_one); ?>" class="box-button">

						<?php
						if ($button_text_one == '') {

							echo esc_html__('Available on','web-app');
						} else {
							
							 echo esc_html(wp_trim_words($button_text_one,2,'')); 
						}
						?>
						<div>
							<?php
							if ($button_text_one_one == '') {

								echo esc_html__('play Store','web-app');
							} else {
								echo esc_html(wp_trim_words($button_text_one_one,2,'')); 
							}
							?>
						</div>		
						<span>	
							<?php if($term_feature_icon_one){ ?>

								<i class="fa <?php echo esc_attr($term_feature_icon_one); ?>" aria-hidden="true"></i>

							<?php } ?>  
						</span>

					</a>

					<a href="<?php echo esc_url($button_link_two); ?>" class="box-button">

						<?php
						if ($button_text_two == '') {

							echo esc_html__('Available on','web-app');
						} else {
							echo esc_html(wp_trim_words($button_text_two,2,''));
						}
						?>
						<div>
							<?php
							if ($button_text_one_two == '') {

							echo esc_html__('play Store','web-app');
							} else {
								echo esc_html(wp_trim_words($button_text_one_two,2,'')); 
							}
							?>
						</div>
						<span>
							<?php if($term_feature_icon_two){ ?>

								<i class="fa <?php echo esc_attr($term_feature_icon_two); ?>" aria-hidden="true"></i>

							<?php } ?>  
						</span>

					</a>

				</div>

			</aside>

			<!-- ******************************************************* Descriptions And Form  *****************-->

			<aside class="widget">
				<h2 class="widget-title">
					<?php if($term_description){ ?>
						<?php echo esc_html($term_description); ?>
					<?php } ?>
				</h2>
				<div class="mc4wp-form-wrapper">

					<?php if( !empty( $term_title_shortcode ) ):
						echo do_shortcode( wp_kses_post($term_title_shortcode) );		
					endif; ?>

				</div>

			</aside>

			<!-- **************************** Footer  Menu  *****************-->

			<aside class="widget">
				<ul>

					<?php  if (get_theme_mod('web_app_footer_menu_option','no')=='yes') {

						 if ( has_nav_menu( 'footer-menu' ) ) : ?>
							<?php wp_nav_menu( array(
							'theme_location'  => 'footer-menu',
							'fallback_cb'     => 'wp_page_menu',
							) ); ?>
					<?php endif; ?>
		          <?php } ?>

				</ul>
			</aside>

		</div>

		<!-- ******************************************************* Footer Images And Social Menu  *****************-->

		<div class="footer-bottom-img">

			 <?php if($term_image){ ?>
				<figure>
					<img src="<?php echo esc_url($term_image); ?>" title="<?php echo esc_html__('Footer Image','web-app'); ?>" alt="<?php echo esc_html__('Footer Image','web-app'); ?>" />
				</figure>
			 <?php } ?>

			<div class="inline-social-icons social-links">
				<!-- inline social links starting from here -->
				<ul>

				    <?php  if (get_theme_mod('web_app_footer_social_option','no')=='yes') {

						 if ( has_nav_menu( 'social-media' ) ) : ?>
							<?php wp_nav_menu( array(
							'theme_location'  => 'social-media',
							'fallback_cb'     => 'wp_page_menu',
							) ); ?>
					<?php endif; ?>
		          <?php } ?>

				</ul>

			</div>

		</div>
             
    
      <?php
      echo $after_widget;
    }

    //==========================================================================================================//
    
    //  Saving Widgets Values 


   public function update($new_instance, $old_instance) {
          $instance = $old_instance;
          $widget_fields = $this->widget_fields();
          foreach ($widget_fields as $widget_field) {
              extract($widget_field);
              $instance[$web_app_widgets_name] = web_app_widgets_updated_field_value($widget_field, $new_instance[$web_app_widgets_name]);
          }
          return $instance;
      }
      public function form($instance) {

          $widget_fields = $this->widget_fields();

          foreach ($widget_fields as $widget_field) {

              extract($widget_field);
              $web_app_widgets_field_value = !empty($instance[$web_app_widgets_name]) ? $instance[$web_app_widgets_name] : '';
              web_app_widgets_show_widget_field($this, $widget_field, $web_app_widgets_field_value);
              
          }

      }
}