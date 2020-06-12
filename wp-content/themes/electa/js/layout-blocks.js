/**
 * Electa Theme Custom Home & Blog Functionality
 *
 */
( function( $ ) {
    
    $(window).load(function() {
        
        // Initialize the Masonry plugin
        var grid = $( '.body-blocks-layout .blocks-wrap' ).masonry({
            columnWidth: '.electa-blocks-post',
            itemSelector: '.electa-blocks-post',
            percentPosition: true
        });

        // Once all images within the grid have loaded lay out the grid
        $( '.body-blocks-layout .blocks-wrap' ).imagesLoaded( function() {
            grid.masonry('layout');
        });

        // Once the layout is complete hide the loader. Triggering the window resize event fixes a small spacing issue on the grid 
        grid.one( 'layoutComplete', function() {
            $( '.blocks-wrap' ).removeClass( 'blocks-wrap-remove' );
            $(window).resize();
        } );
        
    });
    
} )( jQuery );