<?php


$hide_empty = 1;
if(isset($this->general_settings['debug'])&& $this->general_settings['debug'] ==1 && isset($_GET['debug']) && $_GET['debug']==1){
    $hide_empty = 0;
}


$args=array(
    'orderby' => 'name',
    'order' => 'asc',
    'hide_empty'=>$hide_empty,
    'taxonomy' => $this->nh_find_taxonomies()
);
//var_dump($args); die();
if(isset($_GET[$this->requesvar['meta']]) && isset($_GET[$this->requesvar['cat_include']])){
    $args['include']=$_GET['cat_include'];
}
if(isset($_GET[$this->requesvar['ts']])) {
    $ts= $_GET[$this->requesvar['ts']];
}
else {
    $ts = 0;
}

try {
    $categories = @get_categories( $args );
}
catch(Exception $e){
    $returnarray['items']=array();
    $returnarray['exception']=$e;
}
$i=0;
$parent = array();
$cat = array();

if($categories && is_array($categories) && count($categories)>0){
    /*$homepresets = $this->nh_ynaa_homepresets();
    //var_dump($homepresets);
    //echo '<hr>';
    if($homepresets ["homepresets"]['items']){
        foreach($homepresets ["homepresets"]['items'] as $item){
                $hp[$item['cat_id']]['img'] =  $item['img'];
        }
    }*/
    $ass_cats = array();
    //var_dump($hp);
    //echo '<hr>';
    //var_dump($categories); die();
   // var_dump($this->categories_settings);
    foreach ( $categories as $category ) {
          //  var_dump($category,$this->categories_settings[$category->term_id]);

        if($this->categories_settings[$category->term_id]['hidecat']) continue;
        if($this->categories_settings[$category->term_id]['cat_name']) $category->name = $this->categories_settings[$category->term_id]['cat_name'];


        //For Sub categories

        $post_thumbnail_image[0]='';
        $post_id='';
        $allowRemove = 1;

        if($ts < get_option( 'nh_ynaa_categories_settings_ts' ))
        {
            $returnarray['changes']=1;
            $returnarray['changes2']=1;
            $ts = get_option( 'nh_ynaa_categories_settings_ts' );
        }

        $items = ($this->nh_ynaa_articles($category->term_id,1));

        // $returnarray['uma' ]['cat_item'][]=$items;
        $allcategories[$category->term_id]['title']= htmlspecialchars_decode($category->name);
        $allcategories[$category->term_id]['pos']=$i;

        if($items['articles']['items']){
            //var_dump($category); continue;
            //var_dump($items['articles']['items']);
            //continue;
            //echo '<hr>';
            if($category->parent)$parent[$category->term_id]=$category->parent;

            if($ts<=$items['articles']['items'][0]['timestamp']) {
                $returnarray['changes']=1;
                $returnarray['changes3']=1;

                $ts = $items['articles']['items'][0]['timestamp'];
            }
            //echo $items['articles']['items'][0]['thumb'].'<br>';
            if(!$items['articles']['items'][0]['thumb'] || is_null($items['articles']['items'][0]['thumb']) || $items['articles']['items'][0]['thumb'] == 'null') {
                $items['articles']['items'][0]['thumb']='';
                //echo $items['articles']['items'][0]['thumb'].'1<br>';
                /*if($this->categories_settings[$category->term_id]['img']) $items['articles']['items'][0]['thumb'] =$this->categories_settings[$category->term_id]['img'];
                else*/
                //if($hp[$category->term_id]['img']) $items['articles']['items'][0]['thumb'] = $hp[$category->term_id]['img'];
            }
            if(!$this->categories_settings[$category->term_id]['img'] || is_null($this->categories_settings[$category->term_id]['img']))
            {
                $this->categories_settings[$category->term_id]['img']='';

            }
            if($this->categories_settings[$category->term_id]['usecatimg']){
                $use_cat_img = 1;
            }
            else $use_cat_img = 0;

            $pushdefaultactive=1;
            if(isset($this->categories_settings[$category->term_id]['pDA']) && !$this->categories_settings[$category->term_id]['pDA']) $pushdefaultactive=0;

            $category->name = preg_replace_callback("/(&#[0-9]+;)/", function($m) { return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES"); },$category->name );

            $cutomvalues = array();
            foreach ($this->categories_settings[$category->term_id] as $_k=>$_v){
                if(substr($_k,0,1)=='_') $cutomvalues[$_k]=$_v;
            }
            $cat[$category->term_id]=array('pos'=>$i, 'type'=>'cat', 'id'=> (int) $category->term_id, 'parent_id'=> $category->parent, 'title'=>htmlspecialchars_decode($category->name), 'post_img'=>$this->nh_relativeToAbsolutePath($items['articles']['items'][0]['thumb']), 'img'=>$this->nh_relativeToAbsolutePath($this->categories_settings[$category->term_id]['img']), 'post_id'=>$items['articles']['items'][0]['id'] ,'post_date'=>$items['articles']['items'][0]['publish_timestamp'], 'post_ts'=>$items['articles']['items'][0]['timestamp'] ,'allowRemove'=> $allowRemove, 'itemdirekt'=>1, 'use_cat_img'=> $use_cat_img, 'pushDefaultActive'=>$pushdefaultactive )+$cutomvalues;

            //$ass_cats[$category->term_id] = array('img'=>'');
            if($this->categories_settings[$category->term_id]['showsub']){
                $cat[$category->term_id]['showsubcategories']=1;
                if($this->categories_settings[$category->term_id]['showoverviewposts']) {
                    $cat[$category->term_id]['showoverviewposts'] = 1;
                }
                else $cat[$category->term_id]['showoverviewposts'] = 0;
                //$ass_cats[$category->term_id]['showsubcategories']=1;
            }
            else {
                $cat[$category->term_id]['showsubcategories']=0;
                $cat[$category->term_id]['showoverviewposts'] = 0;
            }

            $ass_cats[$category->term_id] = array('showsubcategories'=>$cat[$category->term_id]['showsubcategories'], 'showoverviewposts'=>$cat[$category->term_id]['showoverviewposts'],'img'=>$this->nh_relativeToAbsolutePath($this->categories_settings[$category->term_id]['img']), 'pos'=>$i, 'type'=>'cat', 'id'=> (int) $category->term_id, 'parent_id'=>$category->parent, 'title'=>htmlspecialchars_decode($category->name), 'post_img'=>$this->nh_relativeToAbsolutePath($items['articles']['items'][0]['thumb']), 'post_id'=>$items['articles']['items'][0]['id'] ,'publish_timestamp'=>$items['articles']['items'][0]['publish_timestamp'],'post_date'=>$items['articles']['items'][0]['publish_timestamp'],'post_ts'=>$items['articles']['items'][0]['timestamp'] ,'allowRemove'=> $allowRemove, 'itemdirekt'=>1, 'use_cat_img'=> $use_cat_img ,'pushDefaultActive'=>$pushdefaultactive  ) + $cutomvalues;

            $i++;
            unset($items);
        }
        
    }



    //Categories in Subcategories
    if(count($parent)>0){
        //asort($parent);

        foreach($parent as $k=>$v){
            if(!$cat[$v] || (!(isset($cat[$v]['itemdirekt'])) &&  ($cat[$v]['post_ts'] < $cat[$k]['post_ts']))){

                $cat[$v]=$cat[$k];
                $cat[$v]['pos']=$allcategories[$v]['pos'];
                $cat[$v]['pos']=0;
                $cat[$v]['id']=$v;

                $cat[$v]['parent_id']=0;
                $cat[$v]['title']=$allcategories[$v]['title'];
                unset($cat[$v]['itemdirekt']);
            }

            /*if(isset($cat[$v]['subcategories'])){
                $pos = max(array_keys($cat[$v]['subcategories']));
            }
            else $pos= -1;*/
            /*if(($pos != '') || $pos ===0 ) { $pos++; }
            else {$pos=0;}
            $cat[$k]['pos']=$pos;*/
            //if($this->categories_settings[$k]['showsub'])$cat[$k]['showsubcategories']=1;
            $cat[$v]['subcategories'][]=$cat[$k];
            unset($cat[$k]);

        }
    }

    //$returnarray['items']= $cat;
    if($cat && count($cat)>0){
        foreach($cat as $k=>$v)
            $returnarray['items'][] = $v;

    }



}
else {
    $returnarray['error']=$this->nh_ynaa_errorcode(20);
}

//Events
if(!isset($_GET[$this->requesvar['meta']]) && isset($this->general_settings['eventplugin']) ){

    $items = $this->nh_ynaa_events(1);
    if($items['events']['items']){
        if($ts<=$items['events']['items'][0]['timestamp']) {
            $returnarray['changes']=1;
            $returnarray['changes4']=1;

            $ts = $items['events']['items'][0]['timestamp'];
        }
        $event_im = '';
        if(!isset($items['events']['items'][0]['thumb'])) $items['events']['items'][0]['thumb'] = '';
        //if(!$items['events']['items'][0]['thumb'] && $hp[-1]['img']) $items['events']['items'][0]['thumb'] = $hp[-1]['img'];
        //if(!$items['events']['items'][0]['thumb'] && $this->categories_settings[-1]['img']) $items['events']['items'][0]['thumb'] = $this->categories_settings[-1]['img'];
        if(isset($this->categories_settings[-1]['img'])) $event_im = $this->categories_settings[-1]['img'];
        if(!isset($this->categories_settings[-1]['cat_name'])) $this->categories_settings[-1]['cat_name']= __('Events','nh-ynaa');
        $returnarray['items'][]=array('pos'=>$i, 'type'=>'events', 'id'=> -1, 'title'=>htmlspecialchars_decode($this->categories_settings[-1]['cat_name']), 'img'=>$this->nh_relativeToAbsolutePath($items['events']['items'][0]['thumb']), 'post_id'=>$items['events']['items'][0]['id'] ,'post_ts'=>$items['events']['items'][0]['timestamp'] ,'allowRemove'=> $allowRemove);
        $ass_cats[-1]=array('pos'=>$i, 'type'=>'events', 'id'=> -1, 'title'=>$this->categories_settings[-1]['cat_name'], 'img'=>$this->nh_relativeToAbsolutePath($event_im), 'post_img'=>$this->nh_relativeToAbsolutePath($items['events']['items'][0]['thumb']), 'post_id'=>$items['events']['items'][0]['id'] ,'post_ts'=>$items['events']['items'][0]['timestamp'] ,'allowRemove'=> $allowRemove);
        $i++;
        unset($items);
    }

}

//KArte

if(!$_GET[$this->requesvar['meta']] && isset($this->general_settings['location']) ){

    $map_img = '';
    if(isset($this->categories_settings[-98]['img'])) $map_img = $this->categories_settings[-98]['img'];
    if(!isset($this->categories_settings[-98]['cat_name'])) $this->categories_settings[-98]['cat_name']= __('Map','nh-ynaa');
    //if(!$hp[-98]['img'] || $hp[-98]['img']==NULL || $hp[-98]['img']=='null') $hp[-98]['img']='';
    $returnarray['items'][]=array('pos'=>$i, 'type'=>'map', 'id'=> -98, 'title'=>$this->categories_settings[-98]['cat_name'], 'img'=>$this->nh_relativeToAbsolutePath($map_img), 'allowRemove'=> 1);
    $ass_cats[-98]=array('pos'=>$i, 'type'=>'map', 'id'=> -98, 'title'=>$this->categories_settings[-98]['cat_name'],'img'=>$this->nh_relativeToAbsolutePath($map_img), 'allowRemove'=> 1);
    $i++;
}


//Facebook
$fb = $this->nh_ynaa_get_fbcontent(1);

if($fb ){
    if(isset($fb['error']) && is_array($fb['error']) && $fb['error']['error_code']!=25){
        $returnarray['error'] = $fb['error'];

    }
    elseif(!isset($fb['error']['error_code'])) {
        $fb_img = '';
        $fb = json_decode($fb,true);
        if(!$fb['data'][0]['picture']) $fb['data'][0]['picture'] = '';
        //if(!$fb['data'][0]['picture'] && $hp[-2]['img']) $fb['data'][0]['picture'] = $hp[-2]['img'];
        if($this->categories_settings[-2]['img']) $fb_img =  $this->categories_settings[-2]['img'];
        $returnarray['items'][]=array('pos'=>$i, 'type'=>'fb', 'id'=> -2, 'title'=>__('Facebook','nh-ynaa'), 'img'=>$fb['data'][0]['picture'], 'post_id'=>$fb['data'][0]['id'] ,'post_ts'=>strtotime($fb['data'][0]['created_time']) ,'allowRemove'=> 1);
        $ass_cats[-2]=array('pos'=>$i, 'type'=>'fb', 'id'=> -2, 'title'=>__('Facebook','nh-ynaa'), 'img' => $fb_img, 'post_img'=>$fb['data'][0]['picture'], 'post_id'=>$fb['data'][0]['id'] ,'post_ts'=>strtotime($fb['data'][0]['created_time']) ,'allowRemove'=> 1);
        $i++;
    }
}
if($ass_cats && count($ass_cats)>0)
    $returnarray['ass_cats'] = $ass_cats;

$returnarray['timestamp']= (int) get_option('nh_ynaa_categories_settings_ts');

$returnarray = apply_filters('nh_categories_array',$returnarray);
?>