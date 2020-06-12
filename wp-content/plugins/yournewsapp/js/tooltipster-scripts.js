/* Tooltipster Scripts v1.0.1 */
jQuery(document).ready(function ($) {
                /*$('.tooltip').tooltipster({
                    theme: 'tooltipster-shadow',
                    animation: 'grow',
                    delay: 100,
                    multiple: true,
                  });*/
    activeTooltip();
              });


function activeTooltip(){
    jQuery('.tooltip').tooltipster({
        theme: 'tooltipster-shadow',
        animation: 'grow',
        delay: 100,
        multiple: true,
    });
}