<?php


    if($_GET[$this->requesvar['ts']])$returnarray['ts']=$_GET[$this->requesvar['ts']];
    else $returnarray['ts']=0;
    if($_GET[$this->requesvar['id']]){
        global $wpdb;
        $table_comments = $wpdb->prefix . "comments";
        //$table_comments_meta = $wpdb->prefix . "comments_meta";

        if($_GET[$this->requesvar['action']]=='add' ){
            if(!$_REQUEST[$this->requesvar['key']] || (!$_REQUEST[$this->requesvar['comment']] || trim($_REQUEST[$this->requesvar['comment']]) =='') || !$_REQUEST[$this->requesvar['name']] || !$_REQUEST[$this->requesvar['email']]  ) $returnarray['error']=$this->nh_ynaa_errorcode(30);
            elseif(!is_email($_REQUEST[$this->requesvar['email']])){
                $returnarray['error']=$this->nh_ynaa_errorcode(31);
            }
            else{
                $commentkey = $wpdb->get_var( "SELECT meta_id FROM $wpdb->commentmeta WHERE meta_key = 'ckey' AND meta_value = '".trim($_REQUEST[$this->requesvar['key']])."' LIMIT 1" );
                if($commentkey) $returnarray['error']=$this->nh_ynaa_errorcode(32);
                else {
                    $ts = time();
                    $ts = current_time('timestamp');
                    $comment_parent = 0;
                    //$wpdb->insert('temp',array('text'=>serialize($_REQUEST)), array('%s'));
                    if($_REQUEST[$this->requesvar['comment_id']]) $comment_parent = $_REQUEST[$this->requesvar['comment_id']];
                    $commentdata = array(
                        'comment_post_ID' => $_GET[$this->requesvar['id']],
                        'comment_author' => urldecode(trim($_REQUEST[$this->requesvar['name']])),
                        'comment_author_email' =>trim($_REQUEST[$this->requesvar['email']]),
                        'comment_author_url' => 'http://',
                        'comment_content' => urldecode(trim($_REQUEST[$this->requesvar['comment']])),
                        'comment_type' => '',
                        'comment_parent' => $comment_parent,
                        'user_id' => 0,
                        'comment_author_IP' => $_SERVER['REMOTE_ADDR'],
                        'comment_agent' => $_SERVER['HTTP_USER_AGENT'],
                        'comment_date' => date('Y-m-d H:i:s',$ts),
                        'comment_approved' => 0
                    );
                    if($newcommentid = wp_insert_comment($commentdata)){
                        add_comment_meta( $newcommentid, 'ckey', trim($_REQUEST[$this->requesvar['key']]) );
                        $returnarray['error']=$this->nh_ynaa_errorcode(0);
                        $returnarray['ts']=$ts;
                        $returnarray['comment_id']=$newcommentid;
                        $returnarray['changes']=1;
                        $returnarray['status']=__('Comment is in review', 'nh-ynaa');
                    }
                    else $returnarray['error']=$this->nh_ynaa_errorcode(31);
                }
            }
        }
        else{
            $post_7 = get_post($_GET[$this->requesvar['id']]);
            if($post_7->comment_status == 'open'){
                $returnarray['comment_status']=$post_7->comment_status;
                $args = array(
                    'post_id' => $_GET[$this->requesvar['id']], // use post_id, not post_ID
                    'status' => 'approve',
                    'count' => true //return only the count
                );
                $comments_count = get_comments($args);
                $returnarray['comments_count']=$comments_count;
                $comment = array();
                $returnarray['items'] = array();
                if($comments_count>0){

                    $args = array(
                        'post_id' => $_GET[$this->requesvar['id']], // use post_id, not post_ID
                        'status' => 'approve',
                        '$order' => 'ASC'

                    );

                    $comments = $wpdb->get_results( "SELECT *   FROM $wpdb->comments WHERE comment_approved=1 AND comment_parent=0 AND comment_post_id=".$_GET[$this->requesvar['id']]."  ORDER BY comment_date_gmt DESC ", ARRAY_A  );
                    if($comments){
                        foreach($comments as $com){
                            $parrent_com[$com['comment_ID']][] = $com;


                        }
                    }

                    $comments = $wpdb->get_results( "SELECT *   FROM $wpdb->comments WHERE comment_approved=1 AND comment_parent!=0 AND comment_post_id=".$_GET[$this->requesvar['id']]."   ORDER BY comment_date_gmt ASC ", ARRAY_A  );
                    if($comments){
                        foreach($comments as $com){
                            if(array_key_exists($com['comment_parent'],$parrent_com))
                                $parrent_com[$com['comment_parent']][] = $com;
                        }
                    }

                    if($parrent_com){
                        $pos = 0;

                        foreach($parrent_com as $ar){
                            //var_dump($ar);
                            $temparray=array();
                            $pos++;
                            $temparray['pos']=$pos;


                            $temparray['id']=$ar[0]['comment_ID'];
                            $temparray['text']=html_entity_decode($ar[0]['comment_content']);
                            $temparray['timestamp']=strtotime($ar[0]['comment_date']);

                            if($temparray['timestamp']>$returnarray['ts']) {
                                $returnarray['ts'] = $temparray['timestamp'];
                                $returnarray['changes']=1;
                            }

                            $temparray['datum']=date('d.m.Y, H:i',$temparray['timestamp']);

                            $temparray['author']['name']= $ar[0]['comment_author'];
                            $temparray['author']['id']=$ar[0]['user_id'];
                            $temparray['author']['email'] = $ar[0]['comment_author_email'];
                            $temparray['author']['img']=get_avatar($ar[0]['comment_author_email'],32);
                            if($temparray['author']['img']){
                                $temparray['author']['img'] = substr($temparray['author']['img'],strpos($temparray['author']['img'],'src=')+5);
                                $temparray['author']['img'] = substr($temparray['author']['img'],0,strpos($temparray['author']['img'],'\''));
                            }
                            else $temparray['author']['img']='';
                            if(count($ar)>1){
                                $pos2 = 0;
                                //$temparray2 = array();
                                $temp = array();
                                foreach($ar as $k=>$ar2){
                                    if($k==0) continue;
                                    $pos2++;
                                    $temp['pos']=$pos2;

                                    $temp['id'] = $ar2['comment_ID'];
                                    $temp['parrent_id'] = $ar[0]['comment_ID'];
                                    $temp['text'] =html_entity_decode($ar2['comment_content']);
                                    $temp['timestamp'] =strtotime($ar2['comment_date']);
                                    if($temp['timestamp']>$returnarray['ts']) {
                                        $returnarray['ts'] = $temp['timestamp'];
                                        $returnarray['changes']=1;
                                    }
                                    $temp['datum'] = date('d.m.Y, H:i',$temp['timestamp']);
                                    $temp['author']['name']= $ar2['comment_author'];
                                    $temp['author']['id']=$ar2['user_id'];

                                    $temp['author']['email'] = $ar2['comment_author_email'];
                                    $temp['author']['img']=get_avatar($ar2['comment_author_email'],30);
                                    $temp['author']['img'] = substr($temp['author']['img'],strpos($temp['author']['img'],'src=')+5);
                                    $temp['author']['img'] = substr($temp['author']['img'],0,strpos($temp['author']['img'],'\''));
                                    $temparray['subitems'][] =$temp;

                                }
                            }
                            $returnarray['items'][]=$temparray;

                        }
                    }

                    /*if($returnarray['changes']!=1){
                        unset($returnarray['items']);
                    }*/

                }
            }
            else {
                $returnarray['error']=$this->nh_ynaa_errorcode(29);
            }
        }

    }

    else {
        $returnarray['error']=$this->nh_ynaa_errorcode(15);
    }
    $returnarray = apply_filters('nh_comments_array',$returnarray);


?>