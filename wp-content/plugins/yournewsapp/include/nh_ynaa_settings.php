<?php
global $nh_ynaa;
/*
* Debug Modus
*/
if($_GET['debug']==1){
    global $wpdb;
    $returnarray['debug']['active']=1;
    $returnarray['debug']['homescreentype']=$nh_ynaa->general_settings['homescreentype'];
    $returnarray['debug']['sorttype']=$nh_ynaa->general_settings['sorttype'];
    $upload_dir = wp_upload_dir();
    $returnarray['debug']['upload_dir[baseurl]'] = $upload_dir['baseurl'];
    $returnarray['debug']['phpversion']=phpversion();
    $returnarray['debug']['date_default_timezone_get']=date_default_timezone_get();
    $returnarray['debug']['wp_timezone']= get_option('timezone_string');
    $returnarray['debug']['wp_gmt_offset']= get_option('gmt_offset');
    $returnarray['debug']['current_time']= current_time('timestamp');
    $returnarray['debug']['current_time_string']= current_time('Y-m-d H:i:s T');
    $returnarray['debug']['YNAA_TIMEZONEDIFF']= YNAA_TIMEZONEDIFF;

}



if(!get_option($nh_ynaa->nh_return_option_key('general_settings_key')))   {
   // echo 'Keine settings';
    $returnarray['error']=$nh_ynaa->nh_ynaa_errorcode(13);
}
elseif(!get_option($nh_ynaa->nh_return_option_key('menu_settings_key')))   {
    //echo 'Keine Menu';
    $returnarray['error']=$nh_ynaa->nh_ynaa_errorcode(14);
}
else {
    $requesvar = $nh_ynaa->nh_return_option_key('requesvar');

    if($_GET[$requesvar['ts']]) $ts= $_GET[$equesvar['ts']];
    else $ts = 0;
    //if($_GET[$requesvar['debug']])		$returnarray = $nh_ynaa->general_settings;
    $returnarray = $nh_ynaa->general_settings;
    unset($returnarray['social_fbsecretid']);
    unset($returnarray['social_fbappid']);
    unset($returnarray['social_fbid']);
    $returnarray['error']=$nh_ynaa->nh_ynaa_errorcode(0);
    $returnarray['url']=get_bloginfo('url');
    global $nh_ynaa_version;
    $returnarray['plugin_version']=$nh_ynaa_version;
    $returnarray['wpversion']=get_bloginfo('version');
    $returnarray['wpcharset']=get_bloginfo('charset');
    $returnarray['db_charset']=DB_CHARSET;
    $returnarray['wphtml_type']=get_bloginfo('html_type');

    $ts_general =  get_option( 'nh_ynaa_general_settings_ts' );
    //$returnarray['$ts_general']=$ts_general;
    //FAls ts nich definiert

    //if(!$ts_general) $ts = -1;
    if($ts<=$ts_general){

        $ts = $ts_general;
        if($nh_ynaa->general_settings['sort']) {
            $returnarray['sort']=1;

        }
        else {
            $returnarray['sort']=0;
        }
        if(isset($nh_ynaa->general_settings['showFeatureImageInPost'])) {
            $returnarray['showFeatureImageInPost']=(int)$nh_ynaa->general_settings['showFeatureImageInPost'];

        }

        if(isset($nh_ynaa->general_settings['useFeatureImageOriginalSize'])) {
            $returnarray['useFeatureImageOriginalSize']=(int)$nh_ynaa->general_settings['useFeatureImageOriginalSize'];
            //$returnarray['order']=1;
        }

        $returnarray['homescreentype'] = 0;
        if($nh_ynaa->homepreset_settings['homescreentype']){
            //App kann nur mit der 1 was anfangen
            //$returnarray['homescreentype']=(int) $nh_ynaa->homepreset_settings['homescreentype'];

            //App kann nichts damit anfangen, daher muss immer recent stehen
            if($nh_ynaa->homepreset_settings['homescreentype']==1 || $nh_ynaa->homepreset_settings['homescreentype']==2)
                $returnarray['homescreentype'] = 1;
            if($nh_ynaa->homepreset_settings['sorttype'])$returnarray['sorttype']=$nh_ynaa->homepreset_settings['sorttype'];
            else $returnarray['sorttype']='recent';
            $returnarray['sorttype']='recent';

        }
        else $returnarray['homescreentype']=0;

        //echo $ts;

        $lang = new NH_YNAA_Language;
        if(!$nh_ynaa->general_settings['lang']) $nh_ynaa->general_settings['lang']= 'en';
        $returnarray['lang']=$nh_ynaa->general_settings['lang'];
        $returnarray['lang_array'] = $lang->getTranslation($nh_ynaa->general_settings['lang']);

        if($nh_ynaa->general_settings['theme']) $returnarray['theme'] = (int) $nh_ynaa->general_settings['theme'];
        else $returnarray['theme']=0;
        if(!$nh_ynaa->general_settings['cm'])$nh_ynaa->general_settings['cm'] =$nh_ynaa->general_settings['c1'];
        $returnarray['changes']=1;
        $returnarray['color-01']=($nh_ynaa->general_settings['c1']);
        $returnarray['color-02']=$nh_ynaa->general_settings['c2'];
        $returnarray['color-navbar']=$nh_ynaa->general_settings['cn'];
        $returnarray['color-menu']=$nh_ynaa->general_settings['cm'];
        $returnarray['color-text']=$nh_ynaa->general_settings['ct'];
        $returnarray['color-headline']=$nh_ynaa->general_settings['ch'];
        //$returnarray['color-subheadline']=$nh_ynaa->general_settings['csh'];

        if($nh_ynaa->general_settings['logo']){
            $returnarray['logoUrl']= $nh_ynaa->nh_relativeToAbsolutePath($nh_ynaa->general_settings['logo']);
        }
        else $returnarray['logoUrl']='';
        $returnarray['hasCategories']=1;
        $returnarray['menuIsSectioned']=0;
        $returnarray['categories']=1;
        $returnarray['allowreorder']=1;

        if($nh_ynaa->general_settings['gaTrackID'])$returnarray['gaTrackID']=$nh_ynaa->general_settings['gaTrackID'];
        if($nh_ynaa->general_settings['comments'])	$returnarray['comments']=(int) $nh_ynaa->general_settings['comments'];
        else $returnarray['comments']=0;
        if(isset($nh_ynaa->general_settings['hidesharing'])) $returnarray['hidesharing']=(int) $nh_ynaa->general_settings['hidesharing'];
        if(isset($nh_ynaa->general_settings['hidedate'])) $returnarray['hidedate']=(int) $nh_ynaa->general_settings['hidedate'];

        if(!isset($nh_ynaa->general_settings['textToSpeech']) || count($nh_ynaa->general_settings['textToSpeech'])>1)
        $returnarray['showTextToSpeech']=1;
        else $returnarray['showTextToSpeech']=0;

        if(isset($nh_ynaa->push_settings['hidehistory'])){
            $returnarray['hidePushHistory']=1;
        }

        $returnarray['related']=0;
        if(isset($nh_ynaa->general_settings['relatedPosts'])) $returnarray['related']=(int) $nh_ynaa->general_settings['relatedPosts'];
        $returnarray['relatedDesign']=3;
        if(isset($nh_ynaa->general_settings['relatedDesign'])) $returnarray['relatedDesign']=(int) $nh_ynaa->general_settings['relatedDesign'];
        //$returnarray['style']='<style type="text/css">body { color:#'.$nh_ynaa->general_settings['ct'].';}'.($nh_ynaa->general_settings['css']).'</style>';

        if($nh_ynaa->menu_settings['menu']){
            //var_dump($nh_ynaa->menu_settings);
            foreach($nh_ynaa->appmenus_pre as $ar) {
                $types[] = $ar['type'];
            }

           // var_dump($types);
            foreach($nh_ynaa->menu_settings['menu'] as $k=>$ar){
                //var_dump($ar);
                if($ar['status']==0) continue;
                else {
                    $post_date = 0;
                    //echo  $ar['title'].'<br>';
                    unset($tempmenu);
                    $tempmenu['extraRequestParam']='';
                    if($ar['type'] != 'cat' && !isset($ar['customfilter']) && !in_array($ar['type'],$types) ){
                        //echo  $ar['title'];
                        //echo get_post_status($ar['item_id']);
                        //.get_post_status($ar['item_id']."\r\n";
                        if(get_post_status($ar['item_id']) != 'publish') {
                            //echo $ar['item_id'].':'.get_post_status($ar['item_id']).$ar['title']."\r\n";
                            continue;
                        }
                        /*else {
                            $get_postdata = get_postdata($ar['item_id'] );
                            $post_date = @strtotime($get_postdata['Date']);
                        }*/

                        $tempmenu['extraRequestParam'] .='&'.$this->prefix.'post_type='.@get_post_type($ar['item_id']);

                    }

                   // if($ar['id']==-99 && ($nh_ynaa->homepreset_settings['homescreentype']== '1' || $nh_ynaa->homepreset_settings['homescreentype']== '2' )) continue;

                    if(isset($ar['customfilter'])){
                        //var_dump($ar);
                       // echo $ar['customfilter'];
                        if(has_filter($ar['customfilter'])) {

                            $returnarray['menu'][] = apply_filters($ar['customfilter'],$ar, $k);

                            continue;
                        }


                    }
                    $tempmenu['pos'] =  (int) $ar['pos'];
                    $tempmenu['type'] =  $ar['type'];
                    $tempmenu['extraRequestParam'] .= '&'.$this->prefix.'type='.$ar['type'];
                    $tempmenu['id'] =  (int)$ar['id'];
                    $tempmenu['title'] =  $ar['title'];
                    $tempmenu['ts']= (int)$nh_ynaa->menu_settings['ts'];
                    $tempmenu['post_date']= $post_date;
                    if(isset($ar['content']))$tempmenu['content'] = $ar['content'];
                    if(isset($ar['item_id']))$tempmenu['item_id'] = (int)$ar['item_id'];
                    if(isset($ar['url'])){
                        if( (substr($ar['url'],0,7) != 'http://') && (substr($ar['url'],0,8) != 'https://') && (substr($ar['url'],0,7) != 'mailto:'))$ar['url'] = 'http://'.$ar['url'];
                        $tempmenu['url'] = $ar['url'];
                    }

                    $returnarray['menu'][] = $tempmenu;
                    //array_push($returnarray['menu'],$tempmenu);
                }
            }

            unset($tempmenu);

            $returnarray['menu'] = apply_filters('nh_menu_array',$returnarray['menu']);
        }
        else {
            //$returnarray['menu']['error']=$nh_ynaa->nh_ynaa_errorcode(14);
        }


    }
    else{
        $returnarray['changes']=0;
    }

    $returnarray['timestamp']=(int)$ts;
    $returnarray = apply_filters('nh_settings_array',$returnarray);
}

//return array('settings'=>$returnarray);
?>