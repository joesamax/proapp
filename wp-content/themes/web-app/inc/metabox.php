<?php
/**
 * web-app Metabox
 *
 * @package web-app
 */

add_action('add_meta_boxes', 'web_app_add_sidebar_layout_box');

function web_app_add_sidebar_layout_box()
{
    add_meta_box(
             'web_app_sidebar_layout', // $id
             esc_html__( 'Sidebar Layout','web-app' ),
             'web_app_sidebar_layout_callback', // $callback
             'page', // $page
             'normal', // $context
             'high' // $priority
         ); 
    add_meta_box(
             'web_app_sidebar_layout', // $id
             esc_html__( 'Sidebar Layout for Posts','web-app' ),
             'web_app_sidebar_layout_callback', // $callback
             'post', // $page
             'normal', // $context
             'high' // $priority
         );

}

$web_app_sidebar_layout = array(

    'sidebar-left' => array(
        'value'     => 'sidebar-left',
        'label'     => esc_html__( 'Left sidebar', 'web-app' ),
        'thumbnail' => get_template_directory_uri() . '/inc/images/sidebar-left.png'
        ), 
    'sidebar-right' => array(
        'value' => 'sidebar-right',
        'label' => esc_html__( 'Right sidebar (default)', 'web-app' ),
        'thumbnail' => get_template_directory_uri() . '/inc/images/sidebar-right.png'
        ),
    'sidebar-both' => array(
        'value'     => 'sidebar-both',
        'label'     => esc_html__( 'Both Sidebar', 'web-app' ),
        'thumbnail' => get_template_directory_uri() . '/inc/images/sidebar-both.png'
        ),
    
    'sidebar-no' => array(
        'value'     => 'sidebar-no',
        'label'     => esc_html__( 'No sidebar', 'web-app' ),
        'thumbnail' => get_template_directory_uri() . '/inc/images/sidebar-no.png'
        )   

    );

//==========================================================================================================//

function web_app_sidebar_layout_callback(){ 

    global $post , $web_app_sidebar_layout;
    wp_nonce_field( basename( __FILE__ ), 'web_app_sidebar_layout_nonce' ); 
    ?>
    <table class="form-table">
        <tr>
            <td colspan="4"><em class="f13"><?php echo esc_html__('Choose Sidebar Template','web-app');?></em></td>
        </tr>

        <tr>
            <td>
                <?php  
                foreach($web_app_sidebar_layout as $field){  
                    $web_app_sidebar_metalayout = get_post_meta( $post->ID, 'web_app_sidebar_layout', true ); ?>
                    <div class="radio-image-wrapper" style="float:left; margin-right:30px;">
                        <label class="description">
                         <span><img src="<?php echo esc_url( $field['thumbnail'] ); ?>" alt="" /></span></br>
                         <input type="radio" name="web_app_sidebar_layout" value="<?php echo esc_html($field['value']); ?>" <?php checked( $field['value'], $web_app_sidebar_metalayout ); if(empty($web_app_sidebar_metalayout) && $field['value']=='sidebar-right'){ echo "checked='checked'";} ?>/>&nbsp;<?php echo esc_attr($field['label']); ?>
                        </label>
                    </div>
                <?php } // end foreach 
                ?>
                <div class="clear"></div>
            </td>
        </tr>
    </table>
    
<?php } 

//==========================================================================================================//

/**
 * save the custom metabox data
 * @hooked to save_post hook
 */
function web_app_save_sidebar_layout( $post_id ) { 
    
    global $web_app_sidebar_layout, $post; 
    // Verify the nonce before proceeding.
    if ( !isset( $_POST[ 'web_app_sidebar_layout_nonce' ] ) || !wp_verify_nonce( sanitize_key($_POST[ 'web_app_sidebar_layout_nonce' ]), basename( __FILE__ ) ) )
        return;
    // Stop WP from clearing custom fields on autosave
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE)  
        return;
    
    if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type']) {  
        if (!current_user_can( 'edit_page', $post_id ) )  
            return $post_id;  
    } elseif (!current_user_can( 'edit_post', $post_id ) ) {  
        return $post_id;  
    }  
    
    foreach ($web_app_sidebar_layout as $field) {  
        //Execute this saving function
        $old = get_post_meta( $post_id, 'web_app_sidebar_layout', true); 
        $new = sanitize_text_field( wp_unslash( $_POST['web_app_sidebar_layout'] ) );
        if ($new && $new != $old) {  
            update_post_meta($post_id, 'web_app_sidebar_layout', $new);  
        } elseif ('' == $new && $old) {  
            delete_post_meta($post_id,'web_app_sidebar_layout', $old);  
        } 
     } // end foreach   
 }
 
 add_action('save_post', 'web_app_save_sidebar_layout');