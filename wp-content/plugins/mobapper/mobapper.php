<?php
/*
  Plugin Name: Mobapper
  Plugin URI: http://www.mobapper.com/
  Description: Create native mobile apps for WordPress in 3 simple steps. No coding required.
  Version: 1.2.1
  Author: Mobapper
  Author URI: http://www.mobapper.com
 */

$dir = dirname(__FILE__);
@include_once "$dir/classes/mobapper.php";
@include_once "$dir/classes/jsonbuilder.php";
@include_once "$dir/classes/fetchdata.php";
@require_once ("$dir/admin_dashboard.php");
@require_once ("$dir/mobapper_widgets.php");

function mobapper_init() {
    global $mobapper;
    add_filter('rewrite_rules_array', 'mobapper_rewrites');
    $mobapper = new MOBAPPER();
}

function mobapper_activation() {
    global $wp_rewrite;
    add_filter('rewrite_rules_array', 'mobapper_rewrites');
    $wp_rewrite->flush_rules();
}

function mobapper_deactivation() {
    global $wp_rewrite;
    $wp_rewrite->flush_rules();
}

function mobapper_rewrites($wp_rules) {
    $base = get_option('mobapper_base', 'mobapper');
    if (empty($base)) {
        return $wp_rules;
    }
    $mobapper_rules = array(
        "$base\$" => 'index.php?mobapper=info',
        "$base/(.+)\$" => 'index.php?mobapper=$matches[1]'
    );
    return array_merge($mobapper_rules, $wp_rules);
}

function mobapper_admin_menu() {
    add_menu_page('Mobapper', 'Mobapper', 'administrator', basename(__FILE__), 'mobapper_admin', plugins_url( 'mobapper/images/mobapper-title.png' ));
}

function mobapper_widgets() {
	register_widget( 'Mobapper_Widget' );
}


$default = array('exclude_categories' => '0','exclude_pages'=>'0');
if (!is_array(get_option('mobapper_settings'))) {
    add_option('mobapper_settings', $default);
}
global $mobapper_options;
$mobapper_options = get_option('mobapper_settings');
add_action('init', 'mobapper_init');
add_action('admin_menu', 'mobapper_admin_menu');
add_action( 'widgets_init', 'mobapper_widgets' );
register_activation_hook("$dir/mobapper.php", 'mobapper_activation');
register_deactivation_hook("$dir/mobapper.php", 'mobapper_deactivation');
?>
