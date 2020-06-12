<?php
if(isset($_GET[$this->requesvar['id']])){
    if($_GET[$this->requesvar['ts']]) $ts= $_GET[$this->requesvar['ts']];
    else $ts = 0;
    $weekdays = array(__('Sunday'), __('Monday'), __('Tuesday'), __('Wednesday'), __('Thursday'), __('Friday'), __('Saturday'));
    $returnarray['changes']=0;
    //WP Query
    global $wpdb;
    $table_em_events = $wpdb->prefix . "em_events";
    $table_em_locations = $wpdb->prefix . "em_locations";

    $event = $wpdb->get_row( $wpdb->prepare( "
					select event_id, e.post_id, event_slug, event_status, event_name, event_start_time, event_end_time, event_all_day, event_start_date, event_end_date, e.post_content, e.location_id,event_category_id, event_attributes, event_date_modified,
					l.location_name, l.location_address, l.location_town, l.location_state, l.location_postcode, l.location_region, l.location_country, l.location_latitude, l.location_longitude
					from $table_em_events e
					left join $table_em_locations l on l.location_id=e.location_id
					WHERE e.post_id=".$_GET[$this->requesvar['id']]."
					AND e.post_id  NOT IN (SELECT post_id from {$wpdb->postmeta} WHERE meta_key='_nh_ynaa_hide_in_app' AND meta_value=1)
					", array('%d', '$d', '%s', '%d', '%s', '%s', '%s', '%d','%d', '%d', '%s', '%d', '%d', '%d', '%s')));

    if($event) {
        $post = wp_get_single_post($event->post_id);
        if($post){
            if($ts < strtotime($post->post_modified)) {
                $ts = strtotime($post->post_modified);
                $returnarray['changes']=1;
            }
            /*$postmeta = unserialize(get_post_meta( $event->post_id, '_nh_ynaa_meta_keys', true ));
            if($postmeta  && $postmeta['s']!='on') {
                $returnarray['error']=$this->nh_ynaa_errorcode(15);
            }*/
            if(false);
            else {
                $returnarray['error']=$this->nh_ynaa_errorcode(0);
                if ( has_post_thumbnail($post->ID)) {
                    $post_thumbnail_image=wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'medium');
                }
                else $post_thumbnail_image[0] = '';
                $start_ts = strtotime($event->event_start_date.' '.$event->event_start_time);
                $end_ts = strtotime($event->event_end_date.' '.$event->event_end_time);
                //$content = '<div id="nh_ynaa__app_content">'.$post->post_content.'</div>';
                //$content = $this->nh_ynaa_get_appcontent($content);
                //$content = '<style type="text/css">'.$this->general_settings['css'].' body{color:'.$this->general_settings['ct'].';}</style>'.$content.'<style type="text/css">'.$this->general_settings['css'].' body{color:'.$this->general_settings['ct'].';}</style>';
                //$content = str_replace(PHP_EOL,null,$content);

                $returnarray['id']=$post->ID;
                if(isset($_GET[$this->requesvar['av']]) && (($_GET[$this->requesvar['pl']]=='ios' && $_GET[$this->requesvar['av']]>=1.7) || ($_GET[$this->requesvar['pl']]=='android' && $_GET[$this->requesvar['av']]>1.3))){
                }
                else{
                    $queried_post = get_post($returnarray['id']);
                    $content = $queried_post->post_content;
                    $content = apply_filters('the_content', $content);
                    $content = str_replace(']]>', ']]&gt;', $content);
                    $content = str_replace("\r\n",'\n',$content);
                    //$returnarray['uma']['post_content_-1']= $content;
                    $content = preg_replace('/[\x00-\x1F\x80-\x9F]/u', '',$content);
                    $content = $this->nh_ynaa_get_appcontent($content);
                    //$content = preg_replace('/[\x00-\x1F\x80-\xFF]/', '',$content);
                    if($this->css_settings['css'])$this->general_settings['css'] = $this->css_settings['css'];
                    $this->general_settings['css'] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '',$this->general_settings['css']);
                    $content = (str_replace('<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">','<!doctype html>',$content));


                    if(strpos($content,'<html><head><meta charset="utf-8"></head>'))
                        $content = str_replace('<html><head><meta charset="utf-8"></head>','<html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width"><link href="http://necolas.github.io/normalize.css/3.0.1/normalize.css" rel="stylesheet" type="text/css"><style type="text/css">'.$this->general_settings['css'].' body{color:'.$this->general_settings['ct'].';}</style></head>',$content);
                    elseif(strpos($content,'<html>'))
                        $content = str_replace('<html>','<html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width"><link href="http://necolas.github.io/normalize.css/3.0.1/normalize.css" rel="stylesheet" type="text/css"><style type="text/css">'.$this->general_settings['css'].' body{color:'.$this->general_settings['ct'].';}</style></head>',$content);
                    else $content = '<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width"><link href="http://necolas.github.io/normalize.css/3.0.1/normalize.css" rel="stylesheet" type="text/css"><style type="text/css">'.$this->general_settings['css'].' body{color:'.$this->general_settings['ct'].';}</style></head><body>'.$content.'</body></html>';
                    $returnarray['text']=$content;

                }


                if(!$event->location_latitude || $event->location_latitude== null || $event->location_latitude=='null' || $event->location_latitude=='0.000000') $event->location_latitude =  0;
                else $event->location_latitude = (float) $event->location_latitude ;
                if(!$event->location_longitude || $event->location_longitude== null || $event->location_longitude=='null' || $event->location_longitude=='0.000000') $event->location_longitude =  0;
                else $event->location_longitude = (float) $event->location_longitude ;

                $post->post_title = str_replace(array("\\r","\\n","\r", "\n"),'',trim(html_entity_decode(strip_tags(do_shortcode($post->post_title)), ENT_NOQUOTES, 'UTF-8')));
                $returnarray['title']=($post->post_title);
                $returnarray['title'] = preg_replace_callback("/(&#[0-9]+;)/", function($m) { return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES"); }, $returnarray['title']);

                $returnarray['post_date']=strtotime($post->post_date);
                $returnarray['timestamp']=strtotime($post->post_modified);
                $returnarray['type']='post';
                $returnarray['thumb']= ($post_thumbnail_image[0]);
                $returnarray['publish_timestamp']= strtotime($post->post_date);
                $returnarray['event_id']=$event->event_id;
                $returnarray['subtitle'] = '';
                $returnarray['start_date'] = date('d.m.Y',$start_ts);
                $returnarray['end_date'] = date('d.m.Y',$end_ts);
                $returnarray['start_time'] = date('H:i',$start_ts);
                $returnarray['end_time'] = date('H:i',$end_ts);
                $returnarray['start_ts'] = $start_ts;
                $returnarray['end_ts'] = $end_ts;
                $returnarray['day'] =  $event->event_all_day;
                $returnarray['swd'] = $weekdays[date('w',$start_ts)];
                $returnarray['ewd'] = $weekdays[date('w',$end_ts)];
                $returnarray['sharelink'] = esc_url( get_permalink($post->ID));

                //$returnarray['start_time'] .= (__(' Uhr'));
                //$returnarray['end_time'] .= (__(' Uhr'));
                //$returnarray['thumb'] = $post_thumbnail_image[0];
                $returnarray['images']= array($post_thumbnail_image[0]);
                $returnarray['location'] = $event->location_name;
                $returnarray['town'] = $event->location_town;
                $returnarray['city'] = $event->location_town;
                $returnarray['country'] = $event->location_country;
                $returnarray['zip'] = $event->location_postcode;
                $returnarray['address'] = $event->location_address;
                $returnarray['street'] = $event->location_address;
                $returnarray['region'] = $event->location_region;
                $returnarray['province'] = $event->location_region;
                $returnarray['extra'] = '';
                $returnarray['lat'] = $event->location_latitude;
                $returnarray['lng'] = $event->location_longitude;
                $returnarray['short_text'] = htmlspecialchars_decode($post->post_excerpt);

            }
        }
        else{
            $returnarray['error']=$this->nh_ynaa_errorcode(22);
            $ts = time();
            $returnarray['items'][] = array();
        }
    }
    else {
        $returnarray['error']=$this->nh_ynaa_errorcode(22);
        $ts = time();
        $returnarray['items'][] = array();
    }
}
else {
    $returnarray['error']=$this->nh_ynaa_errorcode(15);
}
$returnarray = apply_filters('nh_event_array',$returnarray);
?>