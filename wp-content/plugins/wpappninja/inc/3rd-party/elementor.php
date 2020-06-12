<?php
defined( 'ABSPATH' ) or die( 'Cheatin\' uh?' );

/**
 * Fix elementor invisible element
 *
 */
add_action('wp_head', 'wpmobile_fix_elementor', 999);
function wpmobile_fix_elementor() {

    if (!is_wpappninja()) {
        return;
    } ?>

    <style>
    .elementor-animated-content {
        visibility: visible;
    }
    </style>

    <?php
}
