<?php
class Mobapper_Widget extends WP_Widget {

	/**
	 * Widget setup.
	 */
	function Mobapper_Widget() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'mobapper', 'description' => __('Download mobapper plugin', 'mobapper') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'mobapper-widget' );

		/* Create the widget. */
		$this->WP_Widget( 'mobapper-widget', __('Mobapper Widget', 'mobapper'), $widget_ops, $control_ops );
	}

	/**
	 * How to display the widget on the screen.
	 */
	function widget( $args, $instance ) {
		extract( $args );

		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'] );
		$link = $instance['link'];
		//$sex = $instance['sex'];
		//$show_sex = isset( $instance['show_sex'] ) ? $instance['show_sex'] : false;

		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title )
			echo $before_title . $title . $after_title;

		/* Display name from widget settings if one was input. */
		if ( $name )
			printf( '<a href="'. __('%1$s', 'mobapper') .'">Download</a>', $link );

		/* If show sex was selected, display the user's sex. */
		//if ( $show_sex )
			//printf( '<p>' . __('I am a %1$s.', 'example.') . '</p>', $sex );

		/* After widget (defined by themes). */
		echo $after_widget;
	}

	/**
	 * Update the widget settings.
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags for title and name to remove HTML (important for text inputs). */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['link'] = strip_tags( $new_instance['link'] );

		/* No need to strip tags for sex and show_sex. */
		//$instance['sex'] = $new_instance['sex'];
		//$instance['show_sex'] = $new_instance['show_sex'];

		return $instance;
	}

	/**
	 * Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_name() function
	 * when creating your form elements. This handles the confusing stuff.
	 */
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array( 'title' => __('Mobapper', 'mobapper'), 'link' => __('http://example.com', 'mobapper'));
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'hybrid'); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p>

		<!-- Your Name: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'link' ); ?>"><?php _e('Download link:', 'mobapper'); ?></label>
			<input id="<?php echo $this->get_field_id( 'link' ); ?>" name="<?php echo $this->get_field_name( 'link' ); ?>" value="<?php echo $instance['link']; ?>" style="width:100%;" />
		</p>

		<!-- Sex: Select Box -->
		

		<!-- Show Sex? Checkbox -->
		

	<?php
	}
}

?>