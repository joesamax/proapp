<div class="clear"></div>
<?php
if(substr(get_bloginfo('language'),0,2)=='de'){
    ?>
    <div>
        <script type="text/javascript" src="//assets.zendesk.com/external/zenbox/v2.6/zenbox.js"></script>
        <style type="text/css" media="screen, projection" >@import url(//assets.zendesk.com/external/zenbox/v2.6/zenbox.css);</style>
        <script type="text/javascript">
            if (typeof(Zenbox) !== "undefined") {
                Zenbox.init({
                    dropboxID:   "20262591",
                    url:         "https://blappsta.zendesk.com",
                    tabTooltip:  "Support",
                    tabImageURL: "https://p3.zdassets.com/external/zenbox/images/tab_de_support_right.png",
                    tabColor:    "#ff8000",
                    tabPosition: "Right"
                });
            }
        </script>
    </div>
    <?php
}
else {
    ?>
    <div>
        <script type="text/javascript" src="//assets.zendesk.com/external/zenbox/v2.6/zenbox.js"></script>
        <style type="text/css" media="screen, projection">
            @import url(//assets.zendesk.com/external/zenbox/v2.6/zenbox.css);
        </style>
        <script type="text/javascript">
            if (typeof(Zenbox) !== "undefined") {
                Zenbox.init({
                    dropboxID:   "20262561",
                    url:         "https://blappsta.zendesk.com",
                    tabTooltip:  "Support",
                    tabImageURL: "https://p3.zdassets.com/external/zenbox/images/tab_support_right.png",
                    tabColor:    "#ff8000",
                    tabPosition: "Right"
                });
            }
        </script>
    </div>
    <?php
}
?>
