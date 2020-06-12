<?php

    if(isset($_GET[$this->requesvar['url']])) {
        if(substr($_GET[$this->requesvar['url']],0,4)!= 'http') $_GET[$this->requesvar['url']]= 'http://'.$_GET[$this->requesvar['url']];
        $_GET[$this->requesvar['id']] = url_to_postid($_GET[$this->requesvar['url']]);
        if($_GET[$this->requesvar['id']]==0 && substr($_GET[$this->requesvar['url']],0,5)=='https') $_GET[$this->requesvar['id']] = url_to_postid(substr_replace($_GET[$this->requesvar['url']],'http',0,5));

    }
    if(isset($_GET[$this->requesvar['id']]) && $_GET[$this->requesvar['id']]!=0){

        //backup main post
        global $post, $wpdb;
        if($post)$stored_post = clone $post;
        $cid = $_GET[$this->requesvar['id']];
        $returnarray['error']=$this->nh_ynaa_errorcode(0);

        $post1 = get_post( $cid);

        if($_GET[$this->requesvar['ts']]) $ts= $_GET[$this->requesvar['ts']];
        else $ts = 0;

        /*
        * Debug Modus
        */
        if($this->general_settings['debug'] ==1 && $_GET['debug']==1){
            var_dump($post1);
        }

        if($post1 && $post1->post_status=='publish'){



            $post = $post1;
            setup_postdata( $post1 );

            $returnarray['id'] = get_the_ID();
            $returnarray['error']['postid']=$returnarray['id'] ;
            //$returnarray['timestamp'] = strtotime(get_the_date('Y-m-d').' '.get_the_modified_time());

            $returnarray['timestamp'] = strtotime($post->post_modified);

            $returnarray['post_type']=$post->post_type;

            $returnarray['post_modified'] = ($post->post_modified);
            $returnarray['post_modified_gmt'] = ($post->post_modified_gmt);
            $nh_ynaa_meta_keys = unserialize(get_post_meta( $post->ID, '_nh_ynaa_meta_keys', true));
            if(get_post_meta( $post->ID, '_nh_ynaa_hide_in_app', true)) $returnarray['hideInApp']=1;
            $excerpt = get_the_excerpt();
            if(!$excerpt) {
                $excerpt = html_entity_decode(strip_tags(wp_trim_words($post->post_content, 55, '')));;
            }
            $returnarray['excerpt'] =  html_entity_decode(str_replace('[&hellip;]', '', strip_tags(apply_filters('the_content_excerpt',$excerpt,$returnarray['id']))));;

            
            if($ts<$returnarray['timestamp']) {
                $ts = $returnarray['timestamp'];
                $post_thumbnail_image[0] = $this->nh_getthumblepic($returnarray['id'],'large');
                if(!$this->general_settings['nogallery']) {
                    $returnarray['gallery'] = $this->getattachedImages($returnarray['id']);
                    /*
                    $returnarray['gallery2'] = get_posts(array(
                        'post_parent' => $returnarray['id'],
                        'post_type' => 'attachment',
                        'post_mime_type' => 'image',
                        'orderby' => 'title',
                        'order' => 'ASC'
                    ));
                    */
                    if (isset($returnarray['gallery']) && empty($returnarray['gallery'])) unset($returnarray['gallery']);
                }
                //$returnarray['title'] = str_replace(array("\\r","\\n","\r", "\n"),'',trim(html_entity_decode(strip_tags(do_shortcode($post->post_title)), ENT_NOQUOTES, 'UTF-8')));
                //post title set to default
                $returnarray['title'] = str_replace(array("\\r","\\n","\r", "\n"),'',trim(html_entity_decode(strip_tags(do_shortcode($post1->post_title)), ENT_NOQUOTES, 'UTF-8')));
                $returnarray['title'] =  preg_replace_callback("/(&#[0-9]+;)/", function($m) { return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES"); },$returnarray['title']);

                if(isset($this->general_settings['showFeatureImageInPost'])) {
                    $returnarray['showFeatureImageInPost']=(int)$this->general_settings['showFeatureImageInPost'];
                }
                if(isset($this->general_settings['useFeatureImageOriginalSize'])) {
                    $returnarray['useFeatureImageOriginalSize']=(int)$this->general_settings['useFeatureImageOriginalSize'];
                }


                //echo $content;
                //$content = $post->post_content;*/
                //$returnarray['content'] = '<html><head><style type="text/css">'.$this->general_settings['css'].';}</style></head><body>'.$content.'</body></html>';
                if($this->general_settings['debug'] ==1 && $_GET['debug']==1){
                    $returnarray['debug']['min-img-size-for-resize'] = $this->general_settings['min-img-size-for-resize'];
                }
                if(isset($_GET[$this->requesvar['av']]) && (($_GET[$this->requesvar['pl']]=='ios' && $_GET[$this->requesvar['av']]>=1.7) || ($_GET[$this->requesvar['pl']]=='android' && $_GET[$this->requesvar['av']]>1.3))){
                }
                else{

                    $queried_post = get_post($returnarray['id']);
                    $content = $queried_post->post_content;
                    if($this->general_settings['debug'] ==1 && $_GET['debug']==1){
                        $returnarray['debug']['post_content']=$content;
                    }
                    $content = apply_filters('the_content', $content);
                    if($this->general_settings['debug'] ==1 && $_GET['debug']==1){
                        $returnarray['debug']['apply_filters(post_content)']=$content;
                    }
                    $content = str_replace(']]>', ']]&gt;', $content);
                    $content = str_replace("\r\n",'\n',$content);
                    //$content = utf8_encode($content);

                    $content = preg_replace('/[\x00-\x1F\x80-\x9F]/u', '',$content);
                    $search = array('src="//', "src='//");
                    $replace = array('src="http://', "src='http://");
                    $content = str_replace($search, $replace,$content);
                    if($this->general_settings['debug'] ==1 && $_GET['debug']==1){
                        $returnarray['debug']['strrepale(]]>\r\n/[\x00-\x1F\x80-\x9F]/u,post_content)']=$content;
                    }
                    //$returnarray['uma']['post_content']= $content;
                    //$returnarray['uma']['post_content_htmlentities']= htmlentities($content,null,"UTF-8");

                    //Für nicht utf8
                    $content = $this->nh_ynaa_get_appcontent($content);
                    if($this->general_settings['debug'] ==1 && $_GET['debug']==1){
                        $returnarray['debug']['nh_ynaa_get_appcontent(post_content)']=$content;
                    }
                    //$returnarray['uma']['post_content_after_nh_ynaa_get_appcontent']= $content;
                    //$content = preg_replace('/[\x00-\x1F\x80-\xFF]/', '',$content);
                    if($this->css_settings['css'])$this->general_settings['css'] = $this->css_settings['css'];
                    $this->general_settings['css'] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '',$this->general_settings['css']);
                    //$content = (str_replace('<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">','<!doctype html>',$content));
                    $active_plugins = get_option('active_plugins');
                    if(strpos($content,'<html><head><meta charset="utf-8"></head>')){
                        $content = str_replace('<html><head><meta charset="utf-8"></head>','<html data-html="html1"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width"><link href="http://necolas.github.io/normalize.css/3.0.1/normalize.css" rel="stylesheet" type="text/css"><style type="text/css">'.$this->general_settings['css'].' body{color:'.$this->general_settings['ct'].';}</style></head>',$content);
                    }
                    elseif(strpos($content,'<html>')) {

                        if(get_bloginfo('url') == 'http://www.automotiveit.eu' || get_bloginfo('url') == 'http://automotiveit.eu' || get_bloginfo('url') == 'http://www.bailazu.de' || (is_array($active_plugins) && in_array('wpseo/wpseo.php',$active_plugins)) ){
                            $content = str_replace('<html>','<html data-html="html2a"><head><meta name="viewport" content="width=device-width"><link href="http://necolas.github.io/normalize.css/3.0.1/normalize.css" rel="stylesheet" type="text/css"><style type="text/css">'.$this->general_settings['css'].' body{color:'.$this->general_settings['ct'].';}</style></head>',$content);
                        }
                        else {
                            //$content = str_replace('<html>','<html data-html="html2"><head><meta name="viewport" content="width=device-width"><link href="http://necolas.github.io/normalize.css/3.0.1/normalize.css" rel="stylesheet" type="text/css"><style type="text/css">'.$this->general_settings['css'].' body{color:'.$this->general_settings['ct'].';}</style></head>',$content);
                            $content = str_replace('<html>','<html data-html="html2b"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width"><link href="http://necolas.github.io/normalize.css/3.0.1/normalize.css" rel="stylesheet" type="text/css"><style type="text/css">'.$this->general_settings['css'].' body{color:'.$this->general_settings['ct'].';}</style></head>',$content);
                        }
                    }
                    else {
                        $content = '<!doctype html><html data-html="html3"><meta charset="utf-8"><meta name="viewport" content="width=device-width"><link href="http://necolas.github.io/normalize.css/3.0.1/normalize.css" rel="stylesheet" type="text/css"><style type="text/css">'.$this->general_settings['css'].' body{color:'.$this->general_settings['ct'].';}</style></head><body>'.$content.'</body></html>';
                    }

                    $content = str_replace('<script type="text/javascript">aop_around(document.body, \'appendChild\'); aop_around(document.body, \'insertBefore\'); </script>','',$content);
                    $content = str_replace(array("<body>\r\n","<body>\r","<body>\n"),'<body>',$content);

                    //$returnarray['uma']['post_content_0']= $content;
                    $returnarray['content']=	apply_filters( 'nh_html_content', $content );
                    $returnarray['content']=$content;
                }
                //$returnarray['uma']['content']=$content;
                $returnarray['changes']=1;
                $returnarray['type']=get_post_type();
                $returnarray['post_type']=$returnarray['type'];
                $returnarray['format']='html';
                if($returnarray['post_type']=='page' ){

                }
                else {
                    $returnarray['post_date'] = strtotime($post->post_date);
                }

                $returnarray['publish_timestamp'] = strtotime($post->post_date);
                $returnarray['post_date_db'] = ($post->post_date);
                $returnarray['post_date_gmt'] = ($post->post_date_gmt);

                $returnarray['author_id']= (int) $post->post_author;
                $returnarray['author_name']= get_the_author_meta( 'display_name' );;

                if(isset($this->general_settings['textToSpeech'])){

                    if(in_array($returnarray['post_type'],$this->general_settings['textToSpeech'])) {
                        if(!empty($nh_ynaa_meta_keys['noread'])) $returnarray['showTextToSpeech']=0;
                        else $returnarray['showTextToSpeech']=1;
                    }
                    else {
                        $returnarray['showTextToSpeech']=0;
                    }
                }
                elseif($returnarray['post_type']=='post') {
                    $returnarray['showTextToSpeech']=1;
                }
                else {
                    $returnarray['showTextToSpeech']=0;
                }

                $returnarray['sharelink']= esc_url( get_permalink());
                //$returnarray['sharelink'] = null;
                //unset( $returnarray['sharelink']);
                $returnarray['comment_status'] = $post->comment_status;
                $args = array(
                    'post_id' => $returnarray['id'], // use post_id, not post_ID
                    'status' => 'approve',
                    'count' => true //return only the count
                );
                $comments_count = get_comments($args);
                $returnarray['comments_count']=$comments_count;
                $categories = get_the_category($returnarray['id']);
                if($categories){

                    foreach($categories as $category) {
                        if(!$returnarray['cat_id']) {
                            $returnarray['cat_id'] = $category->term_id;

                            $returnarray['catid'] = $category->term_id;
                        }
                        $returnarray['cat_id_array'][] = $category->term_id;
                    }
                }
                /*if(!$post_thumbnail_image[0] && $returnarray['catid'] && $this->categories_settings[$returnarray['catid']['img']])
                $post_thumbnail_image[0] = $this->categories_settings[$returnarray['catid']['img']];
                */
                $returnarray['img']= array('src'=>$post_thumbnail_image[0]);
                $returnarray['thumb'][]= $post_thumbnail_image[0];

                //karte temp
                $returnarray['location']=0;
                if($this->general_settings['location']){


                    $postmeta_location = (get_post_meta( $returnarray['id'], '_nh_ynaa_location', true));
                    $postmeta_location_stamp = (get_post_meta( $returnarray['id'], 'nh_location_update_stamp', true));
                    $nh_ynaa_location_id = (get_post_meta($returnarray['id'], 'nh_ynaa_location_id', true));
                    if($postmeta_location){
                        $postmeta_location = unserialize($postmeta_location);
                        if(!is_null($postmeta_location['location_latitude'])){
                            $returnarray['location']=1;
                            if(!$postmeta_location['location_pintype']) $postmeta_location['location_pintype'] = 'red';

                            $postmeta_location['location_name'] =  preg_replace_callback("/(&#[0-9]+;)/", function($m) { return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES"); },$postmeta_location['location_name']);

                            $returnarray['location_info']=array("title"=>stripslashes($postmeta_location['location_name']),"lat"=>$postmeta_location['location_latitude'],"lng"=>$postmeta_location['location_longitude'], "address"=>$postmeta_location['location_address'],  "id"=>$nh_ynaa_location_id, 'ts'=>$postmeta_location_stamp, 'cat_id'=>$returnarray['catid'], 'pintype'=>$postmeta_location['location_pintype']);
                        }
                    }
                }
                if($post->post_type=='location' && $this->general_settings['eventplugin'] && $this->general_settings['eventplugin']==1){

                    $table = $wpdb->prefix;
                    $loc = $wpdb->get_row( $wpdb->prepare( "
									select  l.location_name, l.location_address, l.location_town, l.location_state, l.location_postcode, l.location_region, l.location_country, l.location_latitude, l.location_longitude
									from ".$table."em_locations l
									WHERE 	post_id = %d", $post->ID));
                    if($loc){
                        $returnarray['location']=1;
                        $location_info['title']= $returnarray['title'];

                        $location_info['title'] =  preg_replace_callback("/(&#[0-9]+;)/", function($m) { return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES"); },$location_info['title']);
                        $location_info['lat'] = (float)$loc->location_latitude;
                        $location_info['lng'] = (float)$loc->location_longitude;
                        $location_info['address'] = $loc->location_address;
                        $location_info['id'] = $post->ID;
                        $location_info['ts'] = $post->post_modified;
                        $location_info['cat_id'] = 0;
                        $location_info['pintype'] = 'red';
                        $returnarray['location_info']= $location_info;
                    }
                }
                $returnarray['extraRequestParam']= '&'.$this->prefix."post_type=".$post->post_type;



            }
            else {
                $returnarray['changes']=0;
            }
            wp_reset_postdata();

        }
        else {
            $returnarray['error']=$this->nh_ynaa_errorcode(17);

            $returnarray['id'] = $_GET[$this->requesvar['id']];
        }

        if($stored_post) $post = clone $stored_post;

    }
    else {
        $returnarray['error']=$this->nh_ynaa_errorcode(15);
    }
    //var_dump($returnarray['content']);
    //exit(0);
    //unset($returnarray['content']);
    $returnarray = apply_filters('nh_article_array',$returnarray);
    return (array('article'=>$returnarray));



?>