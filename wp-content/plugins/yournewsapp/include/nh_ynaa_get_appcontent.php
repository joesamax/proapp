<?php
if($this->general_settings['domcontent'])$html =  '<!doctype html><html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /></head><body class="blappsta_ok" id="post-'.$_GET[$this->requesvar['id']].'"><span data-blappsta="app_content_start"></span>'.$html.'</body></html>';
else{
    $libxml_previous_state = libxml_use_internal_errors(true);
    $dom = new DOMDocument();
    $caller = new ErrorTrap(array($dom, 'loadHTML'));
    $caller->call($html);
    if ( !$caller->ok()) {
        $html='<!doctype html><html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /></head><body class="blappsta_ok2" id="post-'.$_GET[$this->requesvar['id']].'"><span data-blappsta="app_content_start"></span>'.$html.'</body></html>';
    }
    else {
        if($this->general_settings['utf8'])	$html = mb_convert_encoding($html, 'html-entities', 'utf-8');

        $dom->validateOnParse = true;
        $dom->loadHTML('<?xml encoding="UTF-8">' . $html);
        $dom->preserveWhiteSpace = false;


        // dirty fix
        foreach ($dom->childNodes as $item)
            if ($item->nodeType == XML_PI_NODE)
                $dom->removeChild($item); // remove hack

        $dom->encoding = 'UTF-8'; // insert proper


        $imgElements  = $dom->getElementsByTagName("img");
        if(!isset($this->general_settings['min-img-size-for-resize'])) $this->general_settings['min-img-size-for-resize'] = 100;
        // echo $this->general_settings['min-img-size-for-resize'];
        $upload_dir = wp_upload_dir();
        foreach ($imgElements as $imgElement) {

            if(strpos($imgElement->getAttribute('class'),'wp-smiley')!== false) continue;
            if($imgElement->hasAttribute('width')) {

                $imgElement->removeAttribute('width');

            }



            if($imgElement->hasAttribute('data-lazy-src')) $src = $imgElement->getAttribute('data-lazy-src');
            else $src = $imgElement->getAttribute('src');
            if($upload_dir['baseurl'] == substr($src,0,strlen($upload_dir['baseurl']))){
                //echo $upload_dir['basedir'].substr($src,strlen($upload_dir['baseurl'])).'<br>';
                if($this->general_settings['min-img-size-for-resize']){

                    $src_o = $src;
                    $src = $upload_dir['basedir'].substr($src,strlen($upload_dir['baseurl']));
                    if(file_exists($src)){
                        list($w, $h) = @getimagesize($src);


                        if(isset($h) && $h>1){
                            if($w < $this->general_settings['min-img-size-for-resize'] ) {
                                $imgclass=' nh-no-resize ';
                                if($imgElement->hasAttribute('class')){
                                    $imgclass = $imgElement->getAttribute('class').$imgclass;
                                }
                                $imgElement->setAttribute('class',$imgclass);
                                continue;
                            }
                            /*
                             $imgclass=' nh-img-space ';
                             if($imgElement->hasAttribute('class')){
                               $imgclass .= $imgElement->getAttribute('class');
                             }
                             $imgElement->setAttribute('class',$imgclass);
                             */
                            else {
                                //$imgElement->setAttribute('width','100%');
                                if($imgElement->hasAttribute('height'))$imgElement->removeAttribute('height');
                            }



                        }
                    }
                    else continue;
                }
            }
            elseif( ini_get('allow_url_fopen') ) {

                if($this->general_settings['min-img-size-for-resize']){
                    if(@getimagesize(($src))){

                        list($w, $h) = @getimagesize(($src)); //var_dump($w);
                        if(isset($h) && ($h>1 ) ){
                            if(  $w < $this->general_settings['min-img-size-for-resize'] ) {
                                $imgclass=' nh-no-resize ';
                                if($imgElement->hasAttribute('class')){
                                    $imgclass = $imgElement->getAttribute('class').$imgclass;
                                }
                                $imgElement->setAttribute('class',$imgclass);
                                continue;
                            }
                            else{
                                //$imgElement->setAttribute('width','100%');
                                if($imgElement->hasAttribute('height'))$imgElement->removeAttribute('height');
                            }
                        }
                    }
                    else continue;
                }
            }
            //echo $imgElement->getAttribute("src").'<hr>';



            if($imgElement->parentNode->nodeName != 'a'){
                $clone = $imgElement->cloneNode(false);
                /*$newE = $dom->createElement('a');
                $newE->setAttribute('href', $imgElement->getAttribute("src"));
                $newE->appendChild ($clone);*/
                $newEdiv = $dom->createElement('div');
                $newEdiv->appendChild ($clone);
                $imgElement->parentNode->replaceChild($newEdiv,$imgElement);
            }
        }



        $divElements  = $dom->getElementsByTagName("div");
        foreach ($divElements as $divElement) {
            if($divElement->hasAttribute('style'))$divElement->removeAttribute('style');
        }

        //iframe tag src replace // with http://
        $iframeElements  = $dom->getElementsByTagName("iframe");
        foreach ($iframeElements as $iframeElement) {
            $clone = $iframeElement->cloneNode(false);
            $newEdiv = $dom->createElement('div');
            $newEdiv->setAttribute('class', 'nh-elastic-video');
            $newEdiv->appendChild ($clone);
            $iframeElement->parentNode->replaceChild($newEdiv,$iframeElement);
            /*if(substr($src,0,2)=='//'){
                $iframeElement->setAttribute('src','http:'.$src);
            }*/
        }



        /*//$headdom = $dom->createElement('head','<title>'..'</title>');
        $newEStyle = $dom->createElement('style',($this->general_settings['css']).' body { color:'.$this->general_settings['ct'].';}'.'');
        $newEStyle->setAttribute('type','text/css');
        //$dom->appendChild($newEStyle);

        $htmltags = $dom->getElementsByTagName ('html');
        foreach ($htmltags as $htmltag) {
            $htmltag->appendChild($newEStyle);
            break;
        }*/

        $html = $dom->saveHTML();
    }
    $html = str_replace('</body>','<span data-blappsta="app_content_end"></span></body>',$html);
    if(!isset($this->general_settings['blank_lines']) && !$this->general_settings['blank_lines']){

        $html = nl2br($html);
        $htmlsup = substr($html,0,strpos($html,'<body>'));
        $htmlsup = str_replace(array('<br />', '<br>'),'',$htmlsup);
        $html = substr($html,strpos($html,'<body>'),-7);
        $html = $htmlsup.$html;
    }

}
/*else{
    $html = '<!doctype html>
            <html>
            <head>
            <meta charset="utf-8">
            </head>
            <body>'.
            $html
            .'</body>
            </html>';
}*/
//echo strpos('<html>',$html);
//$html = '<!doctype html>'.substr($html,strpos('<html>',$html)+6);

/* Loding imges response */
//$html = str_replace('</body>','<script src="http://www.blappsta.com/wp-content/plugin/"></script></body>',$html);
//$jquery = '<script src="http://code.jquery.com/jquery-2.1.0.min.js" type="text/javascript"></script>';
/*$jquery .= '<script type="text/javascript">
  $( document ).ready(function() {

    $("img.nh-img-space").each(function(i, obj) {
      var $ob = $(obj);
      $ob.attr("src",$ob.attr("data-nh-src"));
      //$ob.css("background-image","url(\'"+($ob.attr("data-nh-src"))+"\')");

    });
    //$("img.nh-img-space").css("background-image","url(\'"+($("img.nh-img-space").attr("data-nh-src"))+"\')");

});
</script>';*/
//$html = str_replace('</body>',$jquery.'</body>',$html);


//JQUERY insert
//$jquery = '<script src="http://code.jquery.com/jquery-2.1.0.min.js" type="text/javascript"></script>';

//$html = str_replace('</body>',$jquery.'</body>',$html);
//Blappsta extra
$blappsta_extra = get_option( 'nh_ynaa_blappsta' );
if(is_array($blappsta_extra)){
    //var_dump($blappsta_extra['app']['extra']['app_extra_js']);
    if($blappsta_extra['app']['extra']['app_extra_css']){

        $html = str_replace('</body>','<style type="text/css">'.stripslashes ($blappsta_extra['app']['extra']['app_extra_css']).'</style></body>',$html);
    }
    if($blappsta_extra['app']['extra']['app_extra_js']){
        $html = str_replace('</body>',stripslashes ($blappsta_extra['app']['extra']['app_extra_js']).'</body>',$html);
    }
}
$html = str_replace('<body>', '<body id="post-'.$_GET[$this->requesvar['id']].'" class="'.(($post)?$post->post_type:'').' '.$_GET[$this->requesvar['pl']].'"><span data-blappsta="app_content_start"></span>', $html);
?>