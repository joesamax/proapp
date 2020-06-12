<?php

$returnarray['changes']=1;
$returnarray['ts']=time();
$returnarray['get']= $_GET;
if(!empty($_POST)) $returnarray['post']=$_POST;



$returnarray['request'] = 'items';
$returnfilterarray = apply_filters('nh_search_array_prev',$returnarray, $_GET, $_POST);

if(isset($returnfilterarray['filter']) &&  $returnfilterarray['filter']) {
    $returnarray = array_merge($returnarray, $returnfilterarray);
    //var_dump($returnarray);
    return $returnarray;
}

//var_dump($fields2);
if(isset($_POST) && count($_POST)>0) {
    $returnarray['request'] = 'count';
   if(  isset($_POST['search']) ) {
       if(!isset($_GET['total'])) {
           $returnarray['request'] = 'articles';
           $returnarray = apply_filters('nh_search_array_post', $returnarray, $_POST);
           $limit = 20;
           $limit = isset($_GET[$this->requesvar['limit']])? $_GET[$this->requesvar['limit']]: 20;
           $offset = isset($_GET[$this->requesvar['offset']])? $_GET[$this->requesvar['offset']]: 0;

           $allsearch = new WP_Query(array("s" => $_POST['search'], 'posts_per_page' => $limit, 'offset' => $offset));
           //var_dump($allsearch);

           $i = $offset;
           $prefix = '';
           if(isset($_GET['nh_prefix'])) $prefix = '_'.$_GET['nh_prefix'];
           if (isset($allsearch->posts) ) {
               foreach ($allsearch->posts as $post) {
                   // var_dump($post);
                   $i++;
                   $items['pos'] = $i;
                   $items['type'] = 'article';
                   $items['post_type'] = $post->post_type;
                   $items['extraRequestParam'] = '&'.$prefix."post_type=".$post->post_type;

                   $items['allowRemove'] = 1;
                   $items['post_id'] = $post->ID;
                   $cat_id = 0;
                   $cat_id_array = $this->nh_getpostcategories($post->ID);
                   if ($cat_id_array) $cat_id = (int)$cat_id_array[0];
                   $items['cat_id'] = $cat_id;
                   $items['cat_id_array'] = $cat_id_array;
                   $items['title'] = str_replace(array("\\r", "\\n", "\r", "\n"), '', trim(html_entity_decode(strip_tags(do_shortcode($post->post_title)), ENT_NOQUOTES, 'UTF-8')));
                   $items['title'] =  preg_replace_callback("/(&#[0-9]+;)/", $this->nh_mb_convert_encodingArray($m),$items['title']);
                   $excerpt = get_the_excerpt($post);
                   if (!$excerpt) $excerpt = ((wp_trim_words($post->post_content, 55, '')));;
                   $items['excerpt'] = html_entity_decode(str_replace('[&hellip;]', '', strip_tags(apply_filters('the_content_excerpt',$excerpt,$post->ID))));
                   $items['timestamp'] = strtotime($post->post_modified);
                   $items['post_modified'] = $post->post_modified;
                   $items['post_modified_gmt'] = $post->post_modified_gmt;
                   $items['publish_timestamp'] = strtotime($post->post_date);
                   $items['post_date'] = strtotime($post->post_date);
                   $items['post_date_db'] = $post->post_date;
                   $items['post_date_gmt'] = $post->post_date_gmt;
                   $items['showsubcategories'] = 0;
                   //$img = $this->nh_getthumblepic($post->ID);
                   $img_size = 'large';
                   $thumbnail = $this->nh_getthumblepic($post->ID, $img_size);
                   $images = $this->nh_getthumblepic_allsize($post->ID);
                   $items['img'] = $thumbnail;
                   $items['thumb'] = $thumbnail;
                   $items['images'] = $images;

                   $returnarray['items'][] = $items;
                   //unset($items);
               }

           }

           //$returnarray['total'] = $allsearch->post_count;
           $returnarray['offset'] = $offset;
           $returnarray['limit'] = $limit;
       }

       $returnarray = apply_filters('nh_search_array_total', $returnarray, $_POST);
      // BLAAPP-281 remove trim
      //  if(isset($_POST['search'])&& trim($_POST['search']))       $totalsearch = &new WP_Query("s=" . $_POST['search'] . "&showposts=-1");
       if(isset($_POST['search'])&& trim($_POST['search']))       $totalsearch = new WP_Query("s=" . $_POST['search'] . "&showposts=-1");
       else $totalsearch->post_count = (int)wp_count_posts('post')->publish;
       $returnarray['total'] = $totalsearch->post_count;

       //}

   }
}
else {
    $returnarray['elemets'][] = array('pos'=>1,'name'=>'search','label'=>__('Search','nh-ynaa'), 'placeholder'=>__('Search...','nh-ynaa'), 'type'=>'text', 'required'=>1, 'group'=>'gp1' );
    $returnarray['elements'] = $returnarray['elemets'];


}
?>