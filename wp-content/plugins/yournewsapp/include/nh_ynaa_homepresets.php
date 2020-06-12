<?php


    if (!get_option($this->homepreset_settings_key)) {
        //echo 'Keine settings';
        $returnarray['error']=$this->nh_ynaa_errorcode(23);
    } else {
        if ($_GET[$this->requesvar['ts']]) {
            $ts= $_GET[$this->requesvar['ts']];
        } else {
            $ts = 0;
        }



        $ts_homepreset = -1;
        $returnarray['changes']=0;
        $ts_homepreset=  get_option('nh_ynaa_homepreset_settings_ts');
        //if(!$ts_homepreset) $ts = -1;
        if ($ts<$ts_homepreset) {
            $returnarray['changes']=1;
            $ts = $ts_homepreset;
            if (($_GET[$this->requesvar['meta']] && $_GET[$this->requesvar['cat_include']]) ||$this->homepreset_settings['homescreentype']==3) {
                $categoris = ($this->nh_ynaa_categories());
                //var_dump($categoris);

                if ($categoris['categories']['ass_cats'] && is_array($categoris['categories']['ass_cats']) && count($categoris['ass_cats']>0)) {
                    $i=1;
                    $allowRemove=0;
                    $cat_id = 0;
                    $img = '';

                    foreach ($categoris['categories']['ass_cats'] as $k=>$cat) {
                        if (isset($this->categories_settings[$cat['id']]['hidecathome']) && $this->categories_settings[$cat['id']]['hidecathome']) {
                            continue;
                        }
                        $item["pos"] = $i;
                        $item["type"] = $cat['type'];
                        $item["id"] = (string)$cat['id'];
                        $item["cat_id"] = $cat['id'];
                        $item["title"] = $cat['title'];
                        $item["title"] =  preg_replace_callback("/(&#[0-9]+;)/", function ($m) {
                            return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES");
                        }, $item["title"]);
                        if ($cat['post_img'] && empty($cat['use_cat_img'])) {
                            $item["img"] = $this->nh_relativeToAbsolutePath($cat['post_img']);
                        } else {
                            $item["img"] = $this->nh_relativeToAbsolutePath($cat['img']);
                        }
                        $item["post_id"] = $cat['post_id'];
                        $item["timestamp"] = $cat['post_ts'];
                        $item["publish_timestamp"] = $cat['publish_timestamp'];
                        $item["post_date"] = $cat['publish_timestamp'];

                        $item["showsubcategories"] = $cat['showsubcategories'];
                        $item["url"] = '';
                        $item["hidetitle"] = (int)$cat['hidetitle'];
                        $returnarray['items'][]=$item;
                        unset($item);
                        $i++;
                    }
                } else {
                    $returnarray['error']=$this->nh_ynaa_errorcode(23);
                }
            }
            elseif ($this->homepreset_settings['homescreentype']==1 || $this->homepreset_settings['homescreentype']==2) {
                $returnarray['homescreentype']=(int)$this->homepreset_settings['homescreentype'];
                $returnarray['sorttype']=$this->homepreset_settings['sorttype'];
            }
            else {
                if ($this->homepreset_settings['items']) {
                    $returnarray['changes']=1;
                    /*if($ts<$this->homepreset_settings['ts']) {
                        $returnarray['changes']=1;
                        $ts = $this->homepreset_settings['ts'];
                    }*/
                    $i=1;

                    if (is_array($this->homepreset_settings['items']) && count($this->homepreset_settings['items'])>0) {


                        //var_dump($this->homepreset_settings['items'] );
                        foreach ($this->appmenus_pre as $k=>$ar) {
                            $types[$k] = $ar['type'];
                        }
                        //var_dump($types,$this->homepreset_settings['items']);
                        foreach ($this->homepreset_settings['items'] as $hp) {
                            //var_dump($hp);
                            if (isset($items['articles']['items'][0])) {
                                unset($items['articles']['items'][0]);
                            }
                            $post_title= '';
                            if ($hp['type'] == 'map' && !$this->general_settings['location']) {
                                continue;
                            }
                            if ($hp['type'] == 'cat') {
                                if ($this->categories_settings[$hp['id']]['hidecat'] || !$this->nh_is_category($hp['id'])) {
                                    continue;
                                }
                            }
                            if ($hp['type'] != 'cat' && !isset($hp['customfilter']) && !in_array($hp['type'], $types)) {
                                if (get_post_status($hp['id']) != 'publish') {
                                    continue;
                                }
                            }
                            if ($hp['allowRemove']) {
                                $allowRemove = 1;
                            } else {
                                $allowRemove=0;
                            }
                            $cat_id = 0;
                            $img = '';

                            $items['articles']['items'][0]['id'] = '';
                            $items['articles']['items'][0]['timestamp'] = 0;
                            $items['articles']['items'][0]['publish_timestamp'] = 0;
                            $items['articles']['items'][0]['url'] = '';
                            $items['articles']['items'][0]['post_type'] = '';
                            $extraRequestParam = '';
                            if(isset($hp['type'])) $extraRequestParam = '&'.$this->prefix.'type='.$hp['type'];
                            if ($hp['type'] == 'cat') {
                                $cat_id    = (int) $hp['id'];
                                $items = ($this->nh_ynaa_articles($hp['id'], 1, 'full'));
                                //var_dump($items['articles']['items'][0]);
                                $items['articles']['items'][0]['url'] = '';
                                if ($items['articles']['items'][0]['thumb']) {
                                    $img = $items['articles']['items'][0]['thumb'];
                                } elseif ($this->categories_settings[$cat_id]['img']) {
                                    $img = $this->categories_settings[$cat_id]['img'];
                                } elseif ($hp['img']) {
                                    $img = $hp['img'];
                                }
                                $post_title =  $items['articles']['items'][0]['title'];
                                //$post_title  =  preg_replace_callback("/(&#[0-9]+;)/", function($m) { return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES"); },$post_title );
                                if ($this->categories_settings[$cat_id]['usecatimg']) {
                                    $img = $this->categories_settings[$cat_id]['img'];
                                }
                            }
                            elseif (in_array($hp['type'], $types)) {
                                $cat_id    = (int)  $hp['id'];
                               // var_dump($hp);
                                switch ($hp['type']) {

                                    case 'fb':
                                        $fb = $this->nh_ynaa_get_fbcontent(1);
                                        if ($fb) {
                                            if ($this->general_settings['debug'] ==1 && $_GET['debug']==1) {
                                                var_dump($fb);
                                            }
                                            $fb = json_decode($fb, true);
                                            $items['articles']['items'][0]['id']=$fb['data'][0]['id'];
                                            $items['articles']['items'][0]['timestamp']=strtotime($fb['data'][0]['created_time']);
                                            $items['articles']['items'][0]['publish_timestamp']=strtotime($fb['data'][0]['created_time']);
                                            $img = $fb['data'][0]['picture'];
                                        } break;
                                    case 'events':
                                        $event = $this->nh_ynaa_events(1);
                                        if ($event) {
                                            $items['articles']['items'][0]['id']= ($event['events']['items'][0]['id'])?:0;
                                            $items['articles']['items'][0]['timestamp']=$event['events']['items'][0]['timestamp']?:0;
                                            $items['articles']['items'][0]['publish_timestamp']=$event['events']['items'][0]['publish_timestamp']?:0;
                                            if ($hp['img']) {
                                                $img = $hp['img'];
                                            }
                                            if (!$img) {
                                                $img = $event['events']['items'][0]['thumb'];
                                            }
                                            $post_title = ($event['events']['items'][0]['title'])?$event['events']['items'][0]['title']:'';

                                            //$post_title  =  preg_replace_callback("/(&#[0-9]+;)/", function($m) { return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES"); },$post_title );
                                            //$items['articles']['items'][0]['publish_timestamp']= $event;
                                        }
                                        break;
                                    case 'event':
                                        $_GET[$this->requesvar['id']] = $hp['id'];
                                        $event = $this->nh_ynaa_event();
                                        //var_dump($event);
                                        if ($event) {
                                            $items['articles']['items'][0]['id']=(int)  $hp['id'];
                                            $items['articles']['items'][0]['timestamp']=$event['event']['timestamp'];
                                            $items['articles']['items'][0]['publish_timestamp']=$event['event']['publish_timestamp'];
                                            $items['articles']['items'][0]['post_type']='event';
                                            $post_title= ($event['event']['title']);

                                            //$post_title  =  preg_replace_callback("/(&#[0-9]+;)/", function($m) { return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES"); },$post_title );
                                            $img = $event['event']['thumb'];
                                            //$items['articles']['items'][0]['publish_timestamp']= $event;
                                        }
                                        break;
                                    case 'webview': $cat_id=($cat_id)-(100+$hp['id2']);
                                    default: $items['articles']['items'][0]['id']=$cat_id;
                                        $items['articles']['items'][0]['timestamp']=time();
                                        $items['articles']['items'][0]['publish_timestamp']=time();


                                }

                                if (!$img &&  isset($this->categories_settings[$hp['id']]['img'])) {
                                    $img = $this->categories_settings[$hp['id']]['img'];
                                }
                                if (!$img && isset($hp['img'])) {
                                    $img = $hp['img'];
                                }

                                if ($hp['url']) {
                                    if ((substr($hp['url'], 0, 7) != 'http://') && (substr($hp['url'], 0, 8) != 'https://') && (substr($ar['url'], 0, 7) != 'mailto:')) {
                                        $hp['url'] = 'http://' . $hp['url'];
                                    }
                                    $items['articles']['items'][0]['url'] = $hp['url'];
                                }
                                $cat_id    = 0;
                                $items['articles']['items'][0]['post_modified']= '';
                            }
                            elseif (isset($hp['customfilter'])) {
                                //var_dump($hp);
                                if ($hp['img']) {
                                    $img = $hp['img'];
                                }
                                if (has_filter($hp['customfilter'])) {
                                    //echo $hp['customfilter'];
                                    //echo 'has filter';
                                    $returnarray['items'][] = apply_filters($hp['customfilter'], $hp, $i);
                                    continue;
                                }
                            }
                            else {

                                $extraRequestParam= '&'.$this->prefix.'type='.$hp['type'];
                                $post_categories = wp_get_post_categories($hp['id']);
                                if ($post_categories) {
                                    foreach ($post_categories as $c) {
                                        if (!$cat_id) {
                                            $cat_id =  $c ;
                                        }
                                        break;
                                    }
                                }
                                $items['articles']['items'][0]['id'] = $hp['id'];

                                if ($hp['img']) {
                                    $img =  $hp['img'];
                                } else {
                                    $img = $this->nh_getthumblepic($hp['id']);
                                    if (!$img &&  isset($this->categories_settings[$cat_id]['img'])) {
                                        $img = $this->categories_settings[$hp['id']]['img'];
                                    }
                                }
                                /*if((!$img) && isset($categorys[$cat_id]['img'])){
                                    $img = $categorys[$cat_id]['img'];
                                }*/
                                $p = get_post($hp['id']);
                                if ($p) {
                                    //var_dump($p);
                                    $items['articles']['items'][0]['timestamp'] = strtotime($p->post_modified);
                                    $items['articles']['items'][0]['post_modified'] = ($p->post_modified);
                                    $items['articles']['items'][0]['post_modified_gmt'] = ($p->post_modified_gmt);
                                    $items['articles']['items'][0]['publish_timestamp'] = strtotime($p->post_date);
                                    $items['articles']['items'][0]['post_date_wp'] = ($p->post_date);
                                    $items['articles']['items'][0]['post_date_gmt'] = ($p->post_date_gmt);
                                    $items['articles']['items'][0]['post_type'] = ($p->post_type);
                                    $extraRequestParam = '&'.$this->prefix.'post_type='.($p->post_type);
                                    //$items['articles']['items'][0]['p'] = ($p);

                                    $p->post_title = str_replace(array("\\r","\\n","\r", "\n"), '', trim(html_entity_decode(strip_tags(do_shortcode($p->post_title)), ENT_NOQUOTES, 'UTF-8')));

                                    $post_title= ($p->post_title);
                                  //  $post_title =  preg_replace_callback("/(&#[0-9]+;)/", function($m) { return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES"); },$post_title);
                                }
                                if ($hp['type']!= 'page') {
                                    $hp['type'] = 'article';

                                }
                            }
                            $showsub = 0;

                            if ($cat_id && $this->categories_settings[$cat_id]['showsub']) {
                                $showsub=1;
                            }

                            $post_title  =  preg_replace_callback("/(&#[0-9]+;)/", function ($m) {
                                return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES");
                            }, $post_title);
                            $hp['title']  =  preg_replace_callback("/(&#[0-9]+;)/", function ($m) {
                                return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES");
                            }, $hp['title']);
                            $returnarray['items'][]=array('uma'=> $hp,'pos'=>$i, 'type' => $hp['type'], 'allowRemove'=> $allowRemove, 'id'=> (int)$hp['id'], 'cat_id'=>$cat_id,  'title'=>html_entity_decode($hp['title']), 'img'=>$this->nh_relativeToAbsolutePath($img),'post_title'=>html_entity_decode($post_title), 'post_id'=>$items['articles']['items'][0]['id'], 'timestamp'=>$items['articles']['items'][0]['timestamp'], 'post_modified'=>$items['articles']['items'][0]['post_modified'], 'post_modified_gmt'=>$items['articles']['items'][0]['post_modified_gmt'], 'publish_timestamp' =>$items['articles']['items'][0]['publish_timestamp'],'post_date_wp' =>$items['articles']['items'][0]['post_date_wp'],'post_type' =>$items['articles']['items'][0]['post_type'],'extraRequestParam'=>$extraRequestParam,'post_date_gmt' =>$items['articles']['items'][0]['post_date_gmt'],'post_date' =>$items['articles']['items'][0]['publish_timestamp'], 'showsubcategories'=>$showsub, 'url'=>$items['articles']['items'][0]['url'], 'hidetitle'=> (int)$hp['hidetitle']);
                            $i++;
                        }
                    } else {
                        $returnarray['error']=$this->nh_ynaa_errorcode(23);
                    }


                    if (!isset($returnarray['items'])) {
                        $returnarray['error']=$this->nh_ynaa_errorcode(23);
                    }
                } else {
                    $returnarray['error']=$this->nh_ynaa_errorcode(23);
                }
            }
        }
    }

    $returnarray['timestamp']=$ts;
    $returnarray = apply_filters('nh_homepresets_array', $returnarray);
  //  return array('homepresets'=>$returnarray);
