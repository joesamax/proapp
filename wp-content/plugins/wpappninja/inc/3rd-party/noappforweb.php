<?php
defined( 'ABSPATH' ) or die( 'Cheatin\' uh?' );

/**
 * Disable app for web
 *
 */
if ((isset($_COOKIE['HTTP_X_WPAPPNINJA']) && !preg_match('#android|ios|wpmobile|wpapp|iphone|ipad|ipod#i', $_SERVER['HTTP_USER_AGENT']) && !isset($_GET['wpappninja_simul4']) && !wp_doing_ajax()) || is_admin()) {
    
    // !is_ajax // if isadmin toujours
    
    setcookie("HTTP_X_WPAPPNINJA", "", time() - 3600);
    unset($_COOKIE['HTTP_X_WPAPPNINJA']);
    unset($_SERVER['HTTP_X_WPAPPNINJA']);
}
