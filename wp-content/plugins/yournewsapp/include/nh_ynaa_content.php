<?php
if($_GET[$this->requesvar['id']]){
//echo 1; die();
$queried_post = get_post($_GET[$this->requesvar['id']]);

$content = $queried_post->post_content;
    $content =  apply_filters( 'nh_html_content_before', $content );

    global $post;
    $post = $queried_post;
    setup_postdata( $post );

    $content = apply_filters('the_content', $content);

$content = str_replace(']]>', ']]&gt;', $content);
$search = array("\r\n", 'src="//', "src='//", 'data-game="//');
$replace = array("\n",'src="http://', "src='http://", 'data-game="http://');
$content = str_replace($search, $replace,$content);

//$content = preg_replace('/[\x00-\x1F\x80-\xFF]/', '',$content);
$content = preg_replace('/[\x00-\x1F\x80-\x9F]/u', '',$content);
$content = str_replace(array("\n","\r", "\t", chr(10),chr(13),'\n'),'',$content);
$content = $this->nh_ynaa_get_appcontent($content);

$plugins_url = plugins_url();
$css = '
@import url("'.($plugins_url).'/yournewsapp/fonts/stylesheet.css");
body {
font-family:"Roboto Condensed",Roboto, Helvetica, sans-serif;
text-align:justify;
margin:0;
padding:0;
}
/*img:not(.nh-img-space) {
width: 100% ;
margin-bottom: 10px;
height: auto ;
}*/
img.nh-img-space{
background: url("'.($plugins_url).'/yournewsapp/img/2-1.gif") no-repeat center;
background-size: cover;
}
img.wp-smiley, img.nh-no-resize {
width:auto;
}

ul, ol{
margin:0 0 0 20px;
}
iframe {
/*width:100% !important; */
}
img {
width:100%;
height: auto;
}

figure.wp-caption {
width: 100% !important;
margin-left: inherit;
}

.nh-elastic-video {
position: relative;
padding-bottom: 55%;
padding-top: 15px;
height: 0;
overflow: hidden;
}
.nh-elastic-video iframe {
position: absolute;
top: 0;
left: 0;
width: 100%;
height: 100%;
}

a {word-wrap:break-word;
}

#wpadminbar {
display:none;
}
';
if($this->css_settings['css']) $this->general_settings['css'] = $this->css_settings['css'];
$this->general_settings['css'] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '',$this->general_settings['css']);
$this->general_settings['css'] = str_replace('../fonts/stylesheet.css',plugins_url( 'fonts/stylesheet.css' , __FILE__ ),$this->general_settings['css']);
$css ='<style type="text/css">'.$css.'body{color:'.$this->general_settings['ct'].';   background-color: '.$this->general_settings['c2'].';} a {color:'.$this->general_settings['cm'].';}'.$this->general_settings['css'].'</style>';

if(strpos($content,'<html><head><meta charset="utf-8"></head>')){
$content = str_replace('<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /></head>','<html data-html="html1"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no"><link href="https://necolas.github.io/normalize.css/3.0.1/normalize.css" rel="stylesheet" type="text/css">'.$css.'</head>',$content);
}
elseif(strpos($content,'<html>')) {
$content = str_replace('<html>','<html data-html="html2b"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no"><link href="https://necolas.github.io/normalize.css/3.0.1/normalize.css" rel="stylesheet" type="text/css">'.$css.'</head>',$content);
}
else {
$content = '<!doctype html><html data-html="html3"><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no"><link href="https://necolas.github.io/normalize.css/3.0.1/normalize.css" rel="stylesheet" type="text/css">'.$css.'</head><body>'.$content.'</body></html>';
}
}


$search = array("\r\n", 'src="//', "src='//");
$replace = array("\n",'src="http://', "src='http://");
$content = str_replace($search, $replace,$content);
$content = str_replace('</body>', '<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js" type="text/javascript"></script></body>', $content);

$script = '<script type="text/javascript">';

    $script	.= '$( document ).ready(function() {
    if($(".wpcf7-not-valid").length>0){
        $(".wpcf7-not-valid").first().focus();
        //$("body").scrollTo(".wpcf7-not-valid");
    }
    });';


    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    if(is_plugin_active('bj-lazy-load/bj-lazy-load.php')){
        $content = str_replace('</body>','<script src="'.plugins_url().'/bj-lazy-load/js/combined.min.js" type="text/javascript"></script></body>', $content);
}
elseif(is_plugin_active('crazy-lazy/crazy-lazy.php')){
//  $content = str_replace('</body>','<script src="'.plugins_url().'/crazy-lazy/js/lazyload.min.js" type="text/javascript"></script></body>', $content);
$script .= '$( document ).ready(function() {
$("img.crazy_lazy").each(function() {
var $t = $(this);
console.log($t.attr("data-src"));
$t
.attr("src",$t.attr("data-src"))
.removeAttr("style")
;

});
});';

}

$script	.= '</script>';
$content = str_replace('</body>',$script.'</body>',$content);
$content = str_replace('action="/?ynaa=', 'action="'.get_bloginfo('url').'/?ynaa=', $content);

$content =  apply_filters( 'nh_html_content', $content, $queried_post->ID );

?>