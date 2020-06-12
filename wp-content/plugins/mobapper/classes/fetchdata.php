<?php

class MOBAPPER_FETCH_DATA {

    function __construct() {
        $this->json_builder = new MOBAPPER_JSON_BUILDER();
    }

    function get_categories($args = FALSE) {
        $this->json_builder->createjsonObject();
        $categories = get_categories($args);
        $result = array();
        $c = 0;
        foreach ($categories as $category) {

            if ($category->term_id == 1 && $category->slug == 'uncategorized') {
                
            } else {
                $result[] = $this->fetch_cat_data($category);
                $c++;
            }
        }
        $count = count($result);
        $this->json_builder->adddata('status', 'success');
        $this->json_builder->adddata('count', $count);
        $this->json_builder->adddata('categories', $result);
        $this->json_builder->endFlow($this->json_builder->toJson());
    }

    function get_category_posts() {
        $this->json_builder->createjsonObject();
        global $post;
        global $wp_query;
        $object_key = strtolower(substr(get_class($category), 9));


        if (isset($_GET['id'])) {
            $category = $this->get_category_by_id($_GET['id']);
           
            $result = array();
            if ($category['id'] == "") {
                $this->print_this_error('no data found');
            }
            $posts = $this->get_posts(array(
                'cat' => $category['id'],
                'page' => $_GET['page'],
                'posts_per_page' => $_GET['count'],
                'paged' => $_GET['page']
            ));
            while (have_posts()) {
                the_post();
                $post_result = $this->fetch_post_data($post);

                $result[] = $post_result;
            }
            $this->json_builder->adddata('status', "success");
            $this->json_builder->adddata('count', count($result));    //'pages' => (int) $wp_query->max_num_pages,
            $this->json_builder->adddata('pages', (int) $wp_query->max_num_pages); 
            $this->json_builder->adddata('category', $category); 
            $this->json_builder->adddata('posts', $result);
            $this->json_builder->endFlow($this->json_builder->toJson());
        }
        if (isset($_GET['slug'])) {
            $category = $this->get_category_by_slug($_GET['slug']);
            $result = array();
            if ($category['id'] == "") {
                $this->print_this_error('no data found');
            }
            $posts = $this->get_posts(array(
                'cat' => $category['id'],
                'page' => $_GET['page'],
                'posts_per_page' => $_GET['count'],
                'paged' => $_GET['page']
            ));
            while (have_posts()) {
                the_post();
                $post_result = $this->fetch_post_data($post);
                $result[] = $post_result;
            }
            $this->json_builder->adddata('status', "success");
            $this->json_builder->adddata('count', count($result));    
            $this->json_builder->adddata('pages', (int) $wp_query->max_num_pages); 
            $this->json_builder->adddata('category', $category); 
            $this->json_builder->adddata('posts', $result);
            $this->json_builder->adddata('posts', $result);
            $this->json_builder->endFlow($this->json_builder->toJson());
        }
    }

    function get_recent_posts() {
        $this->json_builder->createjsonObject();
        global $post;
        global $wp_query;
        $query = array(
            'page' => $_GET['page'],
            'posts_per_page' => $_GET['count'],
            'paged' => $_GET['page']
        );

        $posts = $this->get_posts($query);

        while (have_posts()) {
            the_post();
            $post_result = $this->fetch_post_data($post);

            $result[] = $post_result;
        }
        $this->json_builder->adddata('status', "success");
        $this->json_builder->adddata('count', count($result));
        $this->json_builder->adddata('total_count', (int) $wp_query->found_posts);
        $this->json_builder->adddata('pages', $wp_query->max_num_pages);
        $this->json_builder->adddata('posts', $result);
        $this->json_builder->endFlow($this->json_builder->toJson());
    }

    function get_single_post() {
        $this->json_builder->createjsonObject();
        global $post;
        if(isset($_GET['id']))
        {
            $key="p";
            $value = $_GET['id'];
        }
        else{
            $key="name";
            $value = $_GET['slug'];
        }
        $query =  array($key => $value);
        $posts = $this->get_posts($query);
        $result = array();
        while (have_posts()) {
            the_post();
            $post_result = $this->fetch_post_data($post);
            $result[] = $post_result;
        }
        $this->json_builder->adddata('status', "success");
        $this->json_builder->adddata('post', $result);
        $this->json_builder->endFlow($this->json_builder->toJson());
    }
    
    
    function get_search_posts() {
        $this->json_builder->createjsonObject();
        global $post;
        global $wp_query;
        $query = array(
            'page' => $_GET['page'],
            'posts_per_page' => $_GET['count'],
            'paged' => $_GET['page'],
            's' => trim($_GET['search_value'])
        );

        $posts = $this->get_posts($query);

        while (have_posts()) {
            the_post();
            $post_result = $this->fetch_post_data($post);

            $result[] = $post_result;
        }
        if(!$result)
        {
            $result = "";
        }
        $this->json_builder->adddata('status', "success");
        $this->json_builder->adddata('count', count($result));
        $this->json_builder->adddata('total_count', (int) $wp_query->found_posts);
        $this->json_builder->adddata('pages', $wp_query->max_num_pages);
        $this->json_builder->adddata('posts', $result);
        $this->json_builder->endFlow($this->json_builder->toJson());
    }
    
    function get_pages($ex_pagess = FALSE) {
        $this->json_builder->createjsonObject();
        global $post;
        global $wp_query;
        $query = array(
            'post_type' => 'page',
            'post_parent' => 0,
            'exclude' => $ex_pagess,
            'sort_order' => 'DESC',
            'sort_column' => 'post_title',
        );
        
        if(isset($_GET['count'])) {
            $query['number'] = $_GET['count'];
        }

        //$posts = $this->get_posts($query);
        $pages = get_pages($query);
        //die(print_r($pages));
        $result = array();
        foreach ($pages as $page) {
            $post = $page;
            $post_result = $this->fetch_post_data($post);

            $result[] = $post_result;
        }
        if(!$result)
        {
            $result = "";
        }
        $this->json_builder->adddata('status', "success");
        $this->json_builder->adddata('pages', $result);
        $this->json_builder->endFlow($this->json_builder->toJson());
    }
    
    function info()
    {
        $this->json_builder->createjsonObject();
        $this->json_builder->adddata('Version', "1.2");
        $this->json_builder->endFlow($this->json_builder->toJson());
    }

    

    public function get_posts($query = FALSE) {
        query_posts($query);
    }

    public function get_comments($post_id) {
        global $wpdb;
        $mb_comments = $wpdb->get_results($wpdb->prepare("
      SELECT *
      FROM $wpdb->comments
      WHERE comment_post_ID = %d
        AND comment_approved = 1
        AND comment_type = ''
      ORDER BY comment_date
    ", $post_id));
        $result = array();
        foreach ($mb_comments as $mb_comment) {
            $result[] = $this->fetch_comment_data($mb_comment);
        }
        return $result;
    }

    public function get_category_by_id($category_id) {
        $mobapper_category = get_term_by('id', $category_id, 'category');
        return $this->fetch_cat_data($mobapper_category);
    }

    public function get_category_by_slug($category_slug) {
        $mb_category = get_term_by('slug', $category_slug, 'category');
        return $this->fetch_cat_data($mb_category);
    }

    function print_this_error($error) {
        $this->json_builder->createjsonObject();
        $this->json_builder->adddata('status', 'failure');
        $this->json_builder->adddata('message', $error);
        $this->json_builder->endFlow($this->json_builder->toJson());
    }

    function fetch_cat_data($category) {
        $re = array();
        $re['id'] = $category->term_id;
        $re['slug'] = $category->slug;
        $re['title'] = $category->name;
        $re['description'] = $category->description;
        $re['parent'] = $category->parent;
        $re['post_count'] = $category->count;
        return $re;
    }

    function fetch_post_data($post_data) {
        
        $date_format = "c";
        
       $re = array();
        $this->id = $re['id'] = $post_data->ID;
        setup_postdata($post_data);
        $re['type'] = $post_data->post_type;
        $re['slug'] = $post_data->post_name;
        $re['url'] = get_permalink($post_data->ID);
        $re['status'] = $post_data->post_status;
        $re['title'] = get_the_title($post_data->ID);
        $re['title_plain'] = strip_tags(get_the_title($post_data->ID));
        $re['content'] = apply_filters('the_content',get_the_content($post_data->ID));
        $re['excerpt'] = apply_filters('the_excerpt', get_the_excerpt());
        $re['date'] = get_the_time($date_format);
        $re['modified'] = date($date_format, strtotime($post_data->post_modified));
        $re['categories'] = $this->fetch_post_categories();
        $re['tags'] = $this->fetch_post_tags();
        $re['author'] = $this->fetch_author($post_data->post_author);
        $re['comments'] = $this->get_comments($this->id);
        $re['attachments'] = $this->fetch_attachment($this->id);
        $re['comment_count'] = $post_data->comment_count;
        $re['comment_status'] = $post_data->comment_status;
        $re['thumbnail_details'] = $this->fetch_thumbnail();
        $re['custom_fields'] = $this->fetch_custom_fields();
        $re['custom_taxonomies'] = $this->fetch_custom_taxonomies($post_data->post_type);
       return $re;
    }

    function fetch_tag_data($mb_tag) {
        $re = array();
         $re['id'] = $mb_tag->term_id;
         $re['slug'] = $mb_tag->slug;
         $re['title'] = $mb_tag->name;
         $re['description'] = $mb_tag->description;
         $re['post_count'] = $mb_tag->count;
        return $re;
    }

    function fetch_author($id = NULL) {
        if ($id) {
            $this->a_id = $id;
        } else {
            $this->a_id = get_the_author_meta('ID');
        }
        $array = array();
        $array['id'] = $this->a_id;
        $array['slug'] = get_the_author_meta('user_nicename', $this->a_id);
        $array['name'] = get_the_author_meta('display_name', $this->a_id); 
        $array['first_name'] = get_the_author_meta('first_name', $this->a_id);
        $array['last_name'] = get_the_author_meta('last_name', $this->a_id);
        $array['nickname'] = get_the_author_meta('nickname', $this->a_id);
        $array['url'] = get_the_author_meta('user_url', $this->a_id);
        $array['description'] = get_the_author_meta('description', $this->a_id);
        return $array;
    }

    function fetch_attachment($post_id) {
        $mb_attachments = get_children(array(
            'post_type' => 'attachment',
            'post_parent' => $post_id,
            'orderby' => 'menu_order',
            'order' => 'ASC',
            'suppress_filters' => false
        ));
        $attachments = array();
        if (!empty($mb_attachments)) {
            foreach ($mb_attachments as $mb_attachment) {
                $attachments[] = $this->fetch_attachment_data($mb_attachment);
            }
        }
        return $attachments;
    }

    function fetch_thumbnail() {
        $array = array();
        $attachment_id = get_post_thumbnail_id($this->id);
        if (!$attachment_id) {
            return "0";
        }
        $thumbnail_size = "thumbnail";
        $array['thumbnail_size'] = $thumbnail_size;
        $attachment = $this->get_images($attachment_id);
        $array['thumbnail_images'] = $attachment;
        return $array;
    }
    function fetch_comment_data($mb_comment) {
           $date_format = "c";
        $array = array();
        $array['id'] = (int) $mb_comment->comment_ID;
        $array['name'] = $mb_comment->comment_author;
        $array['url'] = $mb_comment->comment_author_url;
        $array['date'] = date($date_format, strtotime($mb_comment->comment_date));
        $array['content'] = apply_filters('comment_text', $mb_comment->comment_content); 
        $array['parent'] = (int) $mb_comment->comment_parent;

        if (!empty($mb_comment->user_id)) {
            $array['author'] = $this->fetch_author($mb_comment->user_id);
        }
        return $array;
    }

    function fetch_attachment_data($mb_attachment) {
        $array = array();
        $array['id'] = (int) $mb_attachment->ID;
        $array['url'] = $mb_attachment->guid;
        $array['slug'] = $mb_attachment->post_name;
        $array['title'] = $mb_attachment->post_title;
        $array['description'] = $mb_attachment->post_content;
        $array['caption'] = $mb_attachment->post_excerpt;
        $array['parent'] = (int) $mb_attachment->post_parent;
        $array['mime_type'] = $mb_attachment->post_mime_type;

        if (substr($mb_attachment->post_mime_type, 0, 5) == 'image') {
            $array['images'] = $this->get_images($mb_attachment->ID);
        }
        return $array;
    }

    function get_images($id) {
        $array = array();
        $home = get_bloginfo('url');
        $sizes = array('thumbnail', 'medium', 'large', 'full');
        if (function_exists('get_intermediate_image_sizes')) {
            $sizes = array_merge(array('full'), get_intermediate_image_sizes());
        }
        foreach ($sizes as $size) {
            list($url, $width, $height) = wp_get_attachment_image_src($id, $size);
            $filename = ABSPATH . substr($url, strlen($home) + 1);
            if (file_exists($filename)) {
                list($measured_width, $measured_height) = getimagesize($filename);
                if ($measured_width == $width &&
                        $measured_height == $height) {
                    $array[$size] = (object) array(
                                'url' => $url,
                                'width' => $width,
                                'height' => $height
                    );
                }
            }
        }
        return $array;
    }

    function fetch_post_categories() {
        $array = array();
        if ($post_categories = get_the_category($this->id)) {
            foreach ($post_categories as $post_category) {
                $category = $this->fetch_cat_data($post_category);
                if ($category->id == 1 && $category->slug == 'uncategorized') {
                    continue;
                }
                $array[] = $category;
            }
            
        }
        return $array;
    }

    function fetch_post_tags() {

        $array = array();
        if ($post_tags = get_the_tags($this->id)) {
            foreach ($post_tags as $post_tag) {
                $array[] = $this->fetch_tag_data($post_tag);
            }
        }
        return $array;
    }

    function fetch_custom_fields() {
        $array = array();
        $mb_custom_fields = get_post_custom($this->id);
        foreach ($mb_custom_fields as $key => $value) {
            $arr = array();
            if (substr($key, 0, 1) != '_') {
                $v = $mb_custom_fields[$key];
                $arr['name'] = $key;
                $arr['value'] = $v[0];
                $array[] = $arr;
            }
        }
        return $array;
    }

    function fetch_custom_taxonomies($post_type) {

        $taxonomies = get_taxonomies(array(
            'object_type' => array($post_type),
            'public' => true,
            '_builtin' => false
                ), 'objects');
        $array = array();
        foreach ($taxonomies as $taxonomy_id => $taxonomy) {
            $taxonomy_key = "taxonomy_$taxonomy_id";
            $taxonomy_class = $taxonomy->hierarchical ? 'fetch_cat_data' : 'fetch_tag_data';
            $terms = get_the_terms($this->id, $taxonomy_id);
            if (!empty($terms)) {
                $taxonomy_terms = array();
                foreach ($terms as $term) {
                    $taxonomy_terms[] = $this->$taxonomy_class($term);
                }
                $array[$taxonomy_key] = $taxonomy_terms;
            }
        }
        return $array;
    }

    Function add_comment() {
        global $comment, $wpdb;
        add_action('comment_id_not_found', array(&$this, 'comment_id_not_found'));
        add_action('comment_closed', array(&$this, 'comment_closed'));
        add_action('comment_on_draft', array(&$this, 'comment_on_draft'));
        add_filter('comment_post_redirect', array(&$this, 'comment_post_redirect'));
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['comment_post_ID'] = $_REQUEST['post_id'];
        $_POST['author'] = $_REQUEST['name'];
        $_POST['email'] = $_REQUEST['email'];
        $_POST['url'] = empty($_REQUEST['url']) ? '' : $_REQUEST['url'];
        $_POST['comment'] = $_REQUEST['content'];
        $_POST['parent'] = empty($_REQUEST['url']) ? '' : $_REQUEST['parent'];
        error_reporting(0);
        include ABSPATH . 'wp-comments-post.php';
    }

    function comment_id_not_found() {
        $this->print_this_error("Post ID " . $_REQUEST['post_id'] . " not found.");
    }

    function comment_closed() {
        $this->print_this_error("Post is closed for comments.");
    }

    function comment_on_draft() {
       
        $this->print_this_error("You cannot comment on unpublished posts.");
    }

    function comment_post_redirect() {
        global $comment;
        $date_format = "c";
        $content = apply_filters('comment_text', $comment->comment_content);
        $this->json_builder->createjsonObject();
        $status = ($comment->comment_approved) ? 'ok' : 'pending';
        $this->json_builder->adddata('status', "success");
        $this->json_builder->adddata('comment_status', $status);
        $this->json_builder->adddata('id', $comment->comment_ID);
        $this->json_builder->adddata('name', $comment->comment_author);
        $this->json_builder->adddata('url', $comment->comment_author_url);
        $this->json_builder->adddata('date', date($date_format, strtotime($comment->comment_date)));
        $this->json_builder->adddata('content', $content);
        $this->json_builder->adddata('parent', $comment->comment_parent);
        $this->json_builder->endFlow($this->json_builder->toJson());
    }

    function get_pages_and_categories($args = FALSE, $ex_pagess = FALSE) {
        global $post;
        global $wp_query;
        $cat_result = array();
        $page_result = array();
        $this->json_builder->createjsonObject();
        $categories = get_categories($args);
        
        $c = 0;
        foreach ($categories as $category) {

            if ($category->term_id == 1 && $category->slug == 'uncategorized') {
                
            } else {
                $cat_result[] = $this->fetch_cat_data($category);
                $c++;
            }
        }
        $count = count($cat_result);
       
        $query = array(
            'post_type' => 'page',
            'post_parent' => 0,
            'exclude' => $ex_pagess,
            'sort_order' => 'DESC',
            'sort_column' => 'post_title',
            'number' => $_GET['count']
        );

        //$posts = $this->get_posts($query);
        $pages = get_pages($query);
        //die(print_r($pages));
        foreach ($pages as $page) {
            $post = $page;
            $post_result = $this->fetch_post_data($post);

            $page_result[] = $post_result;
        }
        
        if(!$page_result) {
            $page_result = "";
        }
        
        $this->json_builder->adddata('status', 'success');
        $this->json_builder->adddata('category_count', $count);
        $this->json_builder->adddata('categories', $cat_result);
        $this->json_builder->adddata('pages', $page_result);
        $this->json_builder->endFlow($this->json_builder->toJson());
    }


}

?>
