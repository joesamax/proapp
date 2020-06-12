<?php
/** 
 *
 * Top Footer 
 * @package web-app
 */

add_action('widgets_init', 'web_app_terms_register');

function web_app_terms_register() {
    register_widget('web_app_termsr_Widget');
}

class web_app_termsr_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'web_app_termsr_Widget', 
            esc_html__('Web App : Top Footer', 'web-app'), array(
            'description' => esc_html__('This Widget show Top Footer Content', 'web-app')
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
                'web_app_widgets_title' => esc_html__('Term Title', 'web-app'),
                'web_app_widgets_field_type' => 'text',
            ),

            'term_description' => array(
                'web_app_widgets_name' => 'term_description',
                'web_app_widgets_title' => esc_html__('Term Description', 'web-app'),
                'web_app_widgets_field_type' => 'text',
            ),

            'term_feature_icon' => array(
                'web_app_widgets_name' => 'term_feature_icon',
                'web_app_widgets_title' => esc_html__('Feature Icon Class', 'web-app'),
                'web_app_widgets_field_type' => 'text',
            ),

           'button_link' => array(
            'web_app_widgets_name' => 'button_link',
            'web_app_widgets_title' => esc_html__('Button Link', 'web-app'),
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
        $term_feature_icon = isset( $instance['term_feature_icon'] ) ? $instance['term_feature_icon'] : '';
        $button_link = isset( $instance['button_link'] ) ? $instance['button_link'] : '';
        
        echo $before_widget;
        ?>

  
      <?php if($term_feature_icon){ ?>
        <ul>
          <li> 
          <i class="fa <?php echo esc_attr($term_feature_icon); ?>" aria-hidden="true"></i>
           <a href="<?php echo esc_url($button_link); ?>">
              <?php if($title_widget){
               echo  esc_html($title_widget);
              } ?>
            </a>
          <br>
           <?php if($term_description){ ?>
              <?php echo esc_html($term_description); ?>
           <?php } ?>
        </li> 
      </ul>  
      <?php } ?>     
      <?php
      echo $after_widget;
    }

    //==========================================================================================================//

     public function update($new_instance, $old_instance) {
          $instance = $old_instance;
          $widget_fields = $this->widget_fields();
          foreach ($widget_fields as $widget_field) {
              extract($widget_field);
              $instance[$web_app_widgets_name] = web_app_widgets_updated_field_value($widget_field, $new_instance[$web_app_widgets_name]);
          }
          return $instance;
      }

      //==========================================================================================================//

      public function form($instance) {

          $widget_fields = $this->widget_fields();

          foreach ($widget_fields as $widget_field) {

              extract($widget_field);
              $web_app_widgets_field_value = !empty($instance[$web_app_widgets_name]) ? $instance[$web_app_widgets_name] : '';
              web_app_widgets_show_widget_field($this, $widget_field, $web_app_widgets_field_value);
              
          }

      }
}