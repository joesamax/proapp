<div class="header-social">
    <?php
    if( get_theme_mod( 'kra-social-email' ) ) :
        echo '<a href="' . esc_url( 'mailto:' . antispambot( get_theme_mod( 'kra-social-email' ), 1 ) ) . '" title="' . __( 'Send Us an Email', 'electa' ) . '" class="header-social-icon social-email"><i class="fa fa-envelope-o"></i></a>';
    endif;

    if( get_theme_mod( 'kra-social-facebook' ) ) :
        echo '<a href="' . esc_url( get_theme_mod( 'kra-social-facebook' ) ) . '" target="_blank" title="' . __( 'Find Us on Facebook', 'electa' ) . '" class="header-social-icon social-facebook"><i class="fa fa-facebook"></i></a>';
    endif;

    if( get_theme_mod( 'kra-social-twitter' ) ) :
        echo '<a href="' . esc_url( get_theme_mod( 'kra-social-twitter' ) ) . '" target="_blank" title="' . __( 'Follow Us on Twitter', 'electa' ) . '" class="header-social-icon social-twitter"><i class="fa fa-twitter"></i></a>';
    endif;
    
    if( get_theme_mod( 'kra-header-search' ) ) :
        echo '<i class="fa fa-search search-btn"></i>';
    endif; ?>
</div>