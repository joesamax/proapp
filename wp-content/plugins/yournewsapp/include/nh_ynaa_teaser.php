<?php



    if(!get_option($this->teaser_settings_key))   {
        //echo 'Keine settings';
        $returnarray['error']=$this->nh_ynaa_errorcode(18);
    }
    else {

        if($_GET[$this->requesvar['ts']]) $ts= $_GET[$this->requesvar['ts']];
        else $ts = 0;
        $returnarray['changes']=0;
        $ts_getoption = get_option( 'nh_ynaa_teaser_settings_ts' );

        if($ts<$ts_getoption) {
            $returnarray['changes']=1;
            $ts = $ts_getoption;
        }
        if($_GET[$this->requesvar['cat_include']]){
            $cat_include = explode(',',$_GET[$this->requesvar['cat_include']]);
        }
        //var_dump($cat_include);
        //var_dump($this->teaser_settings);
        if(isset($_GET[$this->requesvar['meta']])){
            // echo "META";
            $catitems = ($this->nh_ynaa_articles(0,3,'full'));
            // var_dump($catitems);
            if($catitems && is_array($catitems)){

                if(isset($catitems['articles']['error']['error_code']) && $catitems['articles']['error']['error_code']!= 0){
                    $returnarray['error']=$this->nh_ynaa_errorcode(18);
                }
                else {

                    if($catitems['articles']['items']){
                        foreach($catitems['articles']['items'] as $item){

                            $item['apectFill'] = 1;
                            $item['post_ts'] = $item['timestamp'];
                            $item['id'] = $item['post_id'];
                            $item['thumb'] = $this->nh_relativeToAbsolutePath($item['img']);
                            if($ts < $item['post_ts']) {
                                $returnarray['changes']=1;
                                $ts = $item['post_ts'];
                            }
                            $returnarray['items'][] =   $item;
                        }
                    }
                    else $returnarray['error']=$this->nh_ynaa_errorcode(18);


                }

            }
            else {
                $returnarray['error']=$this->nh_ynaa_errorcode(18);
            }
        }
        elseif((!isset($this->teaser_settings['source']) || $this->teaser_settings['source']=='indi') && $this->teaser_settings['teaser']){

            if(is_array($this->teaser_settings['teaser']) && count($this->teaser_settings['teaser'])>0){
                $teasers = $this->teaser_settings['teaser'];
                $i = 1;
                foreach($teasers as $k=>$teaser){
                   // if($k && $k=='type') continue;
                    //var_dump($teaser);
                    if($teaser['type']=='cat'){
                        $item = $this->ny_ynaa_teaser_action($teaser['id'],'cat');
                        //var_dump($item);
                        //$item['title_']= $item['title'];
                        $item['title'] =  preg_replace_callback("/(&#[0-9]+;)/", function($m) { return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES"); },$item['title']);
                        $item['title'] = html_entity_decode($item['title']);
                        $returnarray['items'][]=array('pos'=>(int)$i, 'apectFill'=>1, 'type' => 'cat', 'id'=> (int) $teaser['id'], 'title'=> $item['title'], 'thumb'=>$this->nh_relativeToAbsolutePath($item['img']), 'cat_id'=>(int) $teaser['id'], 'post_ts'=>0, 'post_date'=>0, 'extraRequestParam'=>'&'.$this->prefix.'type='.$teaser['type']);
                        $i++;
                        continue;
                    }
                    if($teaser['type']=='webview'){
                        //var_dump($item);
                        $teaser['title'] =  preg_replace_callback("/(&#[0-9]+;)/", function($m) { return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES"); },$teaser['title']);
                        $teaser['title'] = html_entity_decode($teaser['title']);
                        $returnarray['items'][]=array('pos'=>(int)$i, 'apectFill'=>1, 'type' => 'webview', 'id'=> (int) $teaser['id'], 'item_id'=>-3, 'title'=> $teaser['title'], 'thumb'=>$teaser['img'], 'cat_id'=>0, 'post_ts'=>0, 'post_date'=>0, 'url'=>$teaser['url'], 'extraRequestParam'=>'&'.$this->prefix.'type='.$teaser['type']);
                        $i++;
                        continue;
                    }
                    if($teaser['type']=='favorites' || $teaser['type']=='search'){
                        //var_dump($item);
                        $teaser['title'] =  preg_replace_callback("/(&#[0-9]+;)/", function($m) { return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES"); },$teaser['title']);
                        $teaser['title'] = html_entity_decode($teaser['title']);
                        $returnarray['items'][]=array('pos'=>(int)$i, 'apectFill'=>1, 'type' => $teaser['type'], 'id'=> (int) $teaser['id'], 'item_id'=>-3, 'title'=> $teaser['title'], 'thumb'=>$teaser['img'], 'cat_id'=>0, 'post_ts'=>0, 'post_date'=>0, 'extraRequestParam'=>'&'.$this->prefix.'type='.$teaser['type']);
                        $i++;
                        continue;
                    }
                    if(isset($teaser['customfilter'])){
                        //var_dump($teaser);

                        if(has_filter($teaser['customfilter'])) {
                            $returnarray['items'][] = apply_filters($teaser['customfilter'],$teaser, $i);
                            $i++;
                            continue;
                        }
                        $returnarray['items'][]=array('pos'=>(int)$i, 'apectFill'=>1, 'type' => $teaser['type'], 'id'=> (int) $teaser['id'], 'item_id'=>null, 'title'=> $teaser['title'], 'thumb'=>$teaser['img'], 'cat_id'=>0, 'post_ts'=>0, 'post_date'=>0, 'extraRequestParam'=>'&'.$this->prefix.'type='.$teaser['type']);
                        $i++;
                        continue;
                    }



                    $p = get_post($teaser['id']);

                    if($p){
                        //var_dump($p);
                        if( strtotime($p->post_modified) > $ts){
                            $returnarray['changes']=1;
                            $ts = strtotime($p->post_modified);
                        }
                        $category = get_the_category($teaser['id']);

                        if($_GET[$this->requesvar['meta']]){
                            if(!$category[0]->term_id || is_null($category[0]->term_id)) continue;
                            if($cat_include && !in_array($category[0]->term_id,$cat_include)) continue;
                        }
                        if(get_post_type($teaser['id'])=='event') $category[0]->term_id=0;
                        $posttitle = str_replace(array("\\r","\\n","\r", "\n"),'',trim(html_entity_decode(strip_tags(do_shortcode($p->post_title)), ENT_NOQUOTES, 'UTF-8')));
                        $posttitle =  preg_replace_callback("/(&#[0-9]+;)/", function($m) { return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES"); },$posttitle);
                        if(is_null($category[0]->term_id)) $category[0]->term_id=0;
                        $returnarray['items'][]=array('pos'=>(int)$i, 'apectFill'=>1, /*'type' => get_post_type($teaser)*/ 'type'=>$teaser['type'], 'id'=> (int) $teaser['id'], 'title'=> $posttitle, 'thumb'=>$this->nh_getthumblepic($teaser['id'], 'full'), 'cat_id'=>(int) $category[0]->term_id, 'post_ts'=>strtotime($p->post_modified), 'post_date'=>strtotime($p->post_date), 'extraRequestParam'=>'&'.$this->prefix.'post_type='.($p->post_type));
                        $i++;
                        unset($category);
                    }
                }

            }
            else {

                $returnarray['error']=$this->nh_ynaa_errorcode(18);
            }


        }
        elseif(isset($this->teaser_settings['source']) && $this->teaser_settings['source']!='indi' ){

            if(!$this->teaser_settings['limit']){

                $returnarray['error']=$this->nh_ynaa_errorcode(18);
            }
            else{
                //var_dump($this->teaser_settings);
                if($this->teaser_settings['source']=='cat' && !$this->teaser_settings['source']){

                    $returnarray['error']=$this->nh_ynaa_errorcode(18);
                }
                elseif($this->teaser_settings['source']=='cat'){

                    $catitems = ($this->nh_ynaa_articles($this->teaser_settings['cat'],$this->teaser_settings['limit'],'full'));

                    if($catitems && is_array($catitems)){

                        if(isset($catitems['articles']['error']['error_code']) && $catitems['articles']['error']['error_code']!= 0)
                            $returnarray['error']=$this->nh_ynaa_errorcode(18);
                        else {

                            if($catitems['articles']['items']){
                                foreach($catitems['articles']['items'] as $item){
                                    $item['cat_id']=(int)$this->teaser_settings['cat'];
                                    $item['apectFill'] = 1;
                                    $item['post_ts'] = $item['timestamp'];
                                    $item['extraRequestParam'] = '&'.$this->prefix.'post_type='.$item['post_type'];

                                    $item['post_ts'] = $item['timestamp'];
                                    if($ts < $item['post_ts']) {
                                        $returnarray['changes']=1;
                                        $ts = $item['post_ts'];
                                    }
                                    $returnarray['items'][] = 	$item;
                                }
                            }
                            else $returnarray['error']=$this->nh_ynaa_errorcode(18);


                        }

                    }
                    else {
                        $returnarray['error']=$this->nh_ynaa_errorcode(18);
                    }
                }
                elseif($this->teaser_settings['source']=='recent'){

                    //var_dump($this->teaser_settings['cat'],$this->teaser_settings['limit']);
                    $_GET[$this->requesvar['option']]=1;
                    $_GET[$this->requesvar['sorttype']]='recent';
                    $_GET[$this->requesvar['limit']]=$this->teaser_settings['limit'];
                    $this->homepreset_settings['sorttype']='date-desc';
                    unset($this->homepreset_settings['posttype']);
                    $this->homepreset_settings['posttypes'] = array('post'=>1);
                    $catitems = ($this->nh_ynaa_articles(0,$this->teaser_settings['limit'],'full'));

                    if($catitems && is_array($catitems)){

                        if(isset($catitems['articles']['error']['error_code']) && $catitems['articles']['error']['error_code']!= 0)
                            $returnarray['error']=$this->nh_ynaa_errorcode(18);
                        else {

                            if($catitems['articles']['items']){
                                foreach($catitems['articles']['items'] as $item){

                                    $item['apectFill'] = 1;
                                    $item['post_ts'] = $item['timestamp'];
                                    $item['id'] = $item['post_id'];
                                    $item['thumb'] = $this->nh_relativeToAbsolutePath($item['img']);
                                    if($ts < $item['post_ts']) {
                                        $returnarray['changes']=1;
                                        $ts = $item['post_ts'];
                                    }
                                    $returnarray['items'][] = 	$item;
                                }
                            }
                            else $returnarray['error']=$this->nh_ynaa_errorcode(18);


                        }

                    }
                    else {
                        $returnarray['error']=$this->nh_ynaa_errorcode(18);
                    }
                }
                else{
                    $returnarray['error']=$this->nh_ynaa_errorcode(37);
                }
            }

        }
        else {
            $returnarray['error']=$this->nh_ynaa_errorcode(18);
        }
        $returnarray['timestamp']= (int)$ts;

    }
    if($returnarray['changes']==0 && isset($returnarray['items'])) {
        unset($returnarray['items']);

    }
    $returnarray = apply_filters('nh_teaser_array',$returnarray);


?>