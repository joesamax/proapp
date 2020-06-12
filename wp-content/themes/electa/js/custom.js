/**
 * Electa Theme Custom Functionality
 *
 */
( function( $ ) {
    
    jQuery( document ).ready( function() {
        
        // Add button to sub-menu item to show nested pages / Only used on mobile
        $( '.main-navigation li.page_item_has_children, .main-navigation li.menu-item-has-children' ).prepend( '<span class="menu-dropdown-btn"><i class="fa fa-angle-down"></i></span>' );
        // Mobile nav button functionality
        $( '.menu-dropdown-btn' ).bind( 'click', function() {
            $(this).parent().toggleClass( 'open-page-item' );
        });
        // The menu button
        $( '.header-menu-button' ).click( function(e){
            $( 'body' ).toggleClass( 'show-main-menu' );
        });
        $( '.main-menu-close' ).click( function(e){
            $( '.header-menu-button' ).click();
        });
        
		// Search Show / Hide
        $(".search-btn").toggle(function(){
            $(".search-button").addClass('search-on');
            $(".search-block").show().animate( { left: '+=5' }, 200 );
            $(".search-block .search-field").focus();
        },function(){
            $(".search-block").animate( { left: '-=5' }, 200 ).hide();
            $(".search-button").removeClass('search-on');
        });
		
    });
    
    $(window).resize(function () {
        
        
        
    }).resize();
    
    $(window).load(function() {
        
        
        
    });
    
} )( jQuery );