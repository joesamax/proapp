<?php
/*
Plugin Name: Blappsta Plugin
Version: 0.8.8.8

Plugin URI: http://wordpress.org/plugins/yournewsapp/
Description: Blappsta your blog. your app. - The Wordpress Plugin for Blappsta App
Author: Nebelhorn Medien GmbH
Author URI: http://www.nebelhorn.com
Min WP Version: 3.0
License: GPL2
*/

if (isset($_GET['debug'])&& $_GET['debug']==2) {
    error_reporting(-1);
} else {
    error_reporting(0);
}
//Version Number
//Temp fix folder problem
global $nh_ynaa_version;
$nh_ynaa_version = "0.8.8.8";
global $nh_ynaa_db_version;
$nh_ynaa_db_version=1.2;

//Hook for loading
global $nh_menu_hook_ynaa;

//Query vars
define('QUERY_VARS_YNAA', 'ynaa');




require_once('classes/error_trap.php');

require_once('classes/lang.php');


if (!class_exists('NH_YNAA_Plugin')) {
    class NH_YNAA_Plugin
    {
        /*
        * For easier overriding we declared the keys
        * here as well as our tabs array which is populated
        * when registering settings
        */
        private $general_settings_key = 'nh_ynaa_general_settings';    //App Setting like color etc.
        private $menu_settings_key = 'nh_ynaa_menu_settings';        //App Menu Settings
        private $teaser_settings_key = 'nh_ynaa_teaser_settings';    //App Teaser Settings
        private $push_settings_key = 'nh_ynaa_push_settings';        //App Push Settings
        private $categories_settings_key = 'nh_ynaa_categories_settings';
        //App Push Settings
        private $homepreset_settings_key = 'nh_ynaa_homepreset_settings';        //App Homepreset Settings
        private $css_settings_key = 'nh_ynaa_css_settings';        //CSS settings
        private $plugin_options_key = 'nh_ynaa_plugin_options';        //Plugin Settings
        private $plugin_settings_tabs = array();                    //All Tabs for the Plugin
        public $plugin_settings_ext_tabs = array();
        public $addon_settings_tab_page="nh_ynaa_addon_settings";

        public $appmenus_pre = array();                                //Vordefinerte App Menüs
        public $teaser_support_type = array();

        private $prefix = '';

        /**
         * @return array
         */
        public function getTeaserSupportType()
        {
            return $this->teaser_support_type;
        }

        /**
         * @return string
         */
        public function __getCategoriesSettingsKey()
        {
            return $this->categories_settings_key;
        }
        /*
        public function __get($name){
            return $this->$name;
        }*/
        /**
         * @param array $teaser_support_type
         */
        public function setTeaserSupportType($teaser_support_type)
        {
            $this->teaser_support_type = $teaser_support_type;
        }

        /**
         * @return array
         */
        public function getAppmenusPre()
        {
            return $this->appmenus_pre;
        }

        /**
         * @param int $key
         * @param string $appmenus_pre
         */
        public function setAppmenusPre($key, $appmenus_pre)
        {
            $this->appmenus_pre[$key] = $appmenus_pre;
        }


        private $requesvar ; // Define Get POST Requst Var


        private $exclude_posts;




        /*
        *Konstanten
        */
        private $logo_image_width;
        private $logo_image_height;

        /**
         * Construct the plugin object
         */
        public function __construct($logo_image_width=708, $logo_image_height=120)
        {
            $this->logo_image_width = $logo_image_width;
            $this->logo_image_height = $logo_image_height;

            $this->nh_set_request_var();

            //Action Initial App and Set WP Options
            add_action('init', array( &$this, 'nh_ynaa_load_settings' ));

            //update routine
            add_action('init', array(&$this, 'nh_ynaa_update_routine'), 1);
            //Action on Plugin Setting Page
            add_action('admin_init', array( &$this, 'nh_ynaa_register_general_settings' ));

            add_action('admin_init', array( &$this, 'nh_ynaa_register_menu_settings' ));

            add_action('admin_init', array( &$this, 'nh_ynaa_register_homepreset_settings' ));
            add_action('admin_init', array( &$this, 'nh_ynaa_register_categories_settings' ));
            add_action('admin_init', array( &$this, 'nh_ynaa_register_teaser_settings' ));
            add_action('admin_init', array( &$this, 'nh_ynaa_register_push_settings' ));
            add_action('admin_init', array( &$this, 'nh_ynaa_register_css_settings' ));

            //add_action( 'admin_init', array( &$this, 'nh_ynaa_qrcode_page' ) );

            //Action to add Menu in Settings
            add_action('admin_menu', array( &$this, 'nh_ynaa_add_admin_menus' ));

            //Action Load JS Script & Css Style Files
            add_action('admin_enqueue_scripts', array(&$this, 'nh_ynaa_scripts' ));

            //Action Ad Meta Box in Post for sen Push and to select if Post shown in App

            add_action('add_meta_boxes', array(&$this,'nh_ynaa_add_custom_box' ));

            //Action Save if Post visible in  App
            add_action('save_post', array(&$this,'nh_ynaa_save_postdata' ));

            //Action category update timestamp
            add_action('edit_category', array(&$this,'nh_ynaa_edit_category' ));
            add_action('create_category', array(&$this,'nh_ynaa_edit_category' ));
            add_action('delete_category', array(&$this,'nh_ynaa_edit_category' ));

            //Action Ajax Update Teaser Settings
            add_action('wp_ajax_ny_ynaa_teaser_action', array(&$this,'ny_ynaa_teaser_action'));

            //Action Ajax searcg
            add_action('wp_ajax_nh_search_action', array(&$this,'nh_search_action'));

            //Action Ajax Send Push
            add_action('wp_ajax_ny_ynaa_push_action', array(&$this,'ny_ynaa_push_action'));

            //Action Ajax location
            add_action('wp_ajax_nh_ynaa_googlemap_action', array(&$this,'nh_ynaa_google_action'));
            add_action("wp_ajax_nopriv_nh_ynaa_googlemap_action", array(&$this,"nh_must_login"));

            //Add new Blog in Multisite
            add_action('wpmu_new_blog', array(&$this,'nh_new_blog'), 100, 6);

            add_action('update_option_nh_ynaa_menu_settings', array($this,'nh_update_option_ynaa_menu_settings'), 10, 2);
            add_action('update_option_nh_ynaa_general_settings', array($this,'nh_update_option_ynaa_general_settings'), 10, 2);
            add_action('update_option_nh_ynaa_css_settings', array($this,'nh_update_option_ynaa_css_settings'), 10, 2);
            add_action('update_option_nh_ynaa_teaser_settings', array($this,'nh_update_option_ynaa_teaser_settings'), 10, 2);
            add_action('update_option_nh_ynaa_homepreset_settings', array($this,'nh_update_option_ynaa_homepreset_settings'), 10, 2);
            add_action('update_option_nh_ynaa_push_settings', array($this,'nh_update_option_ynaa_push_settings'), 10, 2);
            add_action('update_option_nh_ynaa_categories_settings', array($this,'nh_update_option_ynaa_categories_settings'), 10, 2);



        } // END public function __construct

        /*
         * Return otion key values
         */
        public function nh_return_option_key($optionKey)
        {
            return $this->$optionKey;
        }

        /**
         * @return mixed
         */
        public function getRequesvar()
        {
            return $this->requesvar;
        }



        /**
        * SET up all REquest, POST , GET VAr Name
        */

        private function nh_set_request_var($prefix = '')
        {

            if (isset($_GET['nh_prefix'])) {
                $this->prefix = $_GET['nh_prefix'].'_';
            }
            $this->requesvar['id']= $this->prefix.'id';
            $this->requesvar['url']= $this->prefix.'url';
            $this->requesvar['option']= $this->prefix.'option';
            $this->requesvar['ts']= $this->prefix.'ts';
            $this->requesvar['sorttype']= $this->prefix.'sorttype';
            $this->requesvar['post_id']= $this->prefix.'post_id';
            $this->requesvar['post_ts']= $this->prefix.'post_ts';
            $this->requesvar['limit']= $this->prefix.'limit';
            $this->requesvar['offset']= $this->prefix.'offset';
            $this->requesvar['n']= $this->prefix.'n';
            $this->requesvar['action']= $this->prefix.'action';
            $this->requesvar['key']= $this->prefix.'key';
            $this->requesvar['comment']= $this->prefix.'comment';
            $this->requesvar['name']= $this->prefix.'name';
            $this->requesvar['email']= $this->prefix.'email';
            $this->requesvar['comment_id']= $this->prefix.'comment_id';
            $this->requesvar['cat_include']=$this->prefix.'cat_include';
            $this->requesvar['meta']=$this->prefix.'meta';

            //App Infos
            $this->requesvar['lang']= $this->prefix.'lang';
            $this->requesvar['b']= $this->prefix.'b';
            $this->requesvar['h']= $this->prefix.'h';
            $this->requesvar['pl']= $this->prefix.'pl';
            $this->requesvar['av']= $this->prefix.'av';
            $this->requesvar['d']= $this->prefix.'d';

            //Backend
            $this->requesvar['tab']= $this->prefix.'tab';
        }// END unction nh_set_request_var

        /**
        * Active Multisite
        */
        public static function nh_ynaa_activate($networkwide)
        {
            global $wpdb;

            if (function_exists('is_multisite') && is_multisite()) {
                // check if it is a network activation - if so, run the activation function for each blog id
                if ($networkwide) {
                    $old_blog = $wpdb->blogid;
                    // Get all blog ids
                    $blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
                    foreach ($blogids as $blog_id) {
                        switch_to_blog($blog_id);
                        NH_YNAA_Plugin::_nh_ynaa_activate();
                    }
                    switch_to_blog($old_blog);
                    return;
                }
            }
            NH_YNAA_Plugin::_nh_ynaa_activate();
            NH_YNAA_Plugin::nh_update_db_check();
        }// END public static function nh_ynaa_activate

        /**
         * Crete new BLOG
         *
         */
        public static function nh_ynaa_add_blog($blog_id, $user_id=0)
        {
            switch_to_blog($blog_id);
            NH_YNAA_Plugin::_nh_ynaa_activate();
        }

        /**
         * Activate the plugin
         */
        public static function _nh_ynaa_activate()
        {
            //ADD version nummer to WP options
            global $nh_ynaa_version;
            //Preset app menu
            $menu_array[0] = array('title'=>__('Browse', 'nh-ynaa'),'status'=>1,'pos'=>1, 'id'=>0, 'type'=>'app', 'type_text'=>'App');
            //$menu_array[1] = array('title'=>__('Subscription','nh-ynaa'),'status'=>1,'pos'=>2, 'id'=>-99, 'type'=>'app', 'type_text'=>'App');
            $menu_array[2] = array('title'=>__('Notifications', 'nh-ynaa'),'status'=>1,'pos'=>3, 'id'=>2, 'type'=>'pushCenter', 'type_text'=>__('Pushcenter', 'nh-ynaa'));
            $ts = time();

            $nh_ynaa_menu_settings = array('menu'=>$menu_array,'ts'=>$ts);
            //$menu_array[2] = array('title'=>__('Map','nh-ynaa'),'status'=>1,'pos'=>3, 'id'=>-98, 'type'=>'map', 'type_text'=>'App');
            //$menu_array[5] = array('title'=>__('Events','nh-ynaa'),'status'=>1,'pos'=>3, 'id'=>1, 'type'=>'app', 'type_text'=>'App');

            //Main Pre Setting for App
            $css = '';
            include('include/default_css.php');
            /*foreach(self::$lang_de as $k=>$v){
                $lang_en[$k]=$k;

            }*/
            $lang = 'en';
            if (get_bloginfo('language')=='de-DE' || get_bloginfo('language')=='de-CH') {
                $lang='de';
                $menu_array[0]['title']='Stöbern';
                $menu_array[2]['title']='Benachrichtigungen';
            }
            if ('open'==get_option('default_comment_status')) {
                $comments =1;
            } else {
                $comments =0;
            }

            /* check if Avada theme is active */
            $is_avada = 0;
            $current_theme = wp_get_theme();
            if ($current_theme->Name === "Avada") {
                /* by default, use avada portfolio categories */
                $is_avada = 1;
            }
            $nh_ynaa_general_settings=(array('sort'=>1,'c1'=>'#808080', 'cm'=>'#212121','c2'=>'#ffffff', 'cn'=>'#f2f2f2', 'ct'=>'#212121', 'ch'=>'#808080', 'csh'=>'#000000','ts'=>$ts, 'comments'=>$comments, 'logo'=> plugins_url('img/placeholder.png', __FILE__),  'lang'=>$lang, 'homescreentype'=>1, 'sorttype'=> 'recent' , 'min-img-size-for-resize'=>100, 'theme'=>1, 'avada-categories'=>$is_avada, 'showFeatureImageInPost'=>1, 'relatedPosts'=>1, 'relatedDesign'=>3, 'relatedCount'=>4, 'useFeatureImageOriginalSize'=>0));
            $nh_ynnn_css_settings = array('css'=> $css, $ts=>$ts);

            //Preset teaser

            $nh_ynaa_teaser_settings = array('ts'=>0,'teaser'=>false);

            //ADD Options in Wp-Option table
            $ts_setting = get_option('nh_ynaa_css_settings_ts');
            if (!$ts_setting || is_null($ts_setting)) {
                update_option('nh_ynaa_css_settings', $nh_ynnn_css_settings);
                update_option('nh_ynaa_css_settings_ts', $ts);
            }
            update_option('nh_ynaa_plugin_version', $nh_ynaa_version);
            $ts_setting = get_option('nh_ynaa_general_settings');
            if (!$ts_setting || is_null($ts_setting)) {
                update_option('nh_ynaa_general_settings', $nh_ynaa_general_settings);
            }
            $ts_setting = get_option('nh_ynaa_general_settings_ts');
            if (!$ts_setting || is_null($ts_setting)) {
                update_option('nh_ynaa_general_settings_ts', $ts);
            }
            $ts_setting = get_option('nh_ynaa_menu_settings');
            if (!$ts_setting || is_null($ts_setting)) {
                update_option('nh_ynaa_menu_settings', $nh_ynaa_menu_settings);
            }
            $ts_setting = get_option('nh_ynaa_menu_settings_ts');
            if (!$ts_setting || is_null($ts_setting)) {
                update_option('nh_ynaa_menu_settings_ts', $ts);
            }


            $ts_setting = get_option('nh_ynaa_teaser_settings');
            if (!$ts_setting || is_null($ts_setting)) {
                $args = array(
                    'numberposts' => 3,
                    'offset' => 0,
                    'orderby' => 'post_date',
                    'order' => 'DESC',
                    'post_type' => 'post',
                    'post_status' => 'publish' );

                $recent_posts = wp_get_recent_posts($args, ARRAY_A);
                $nh_ynaa_teaser_settings['limit'] = 3;
                $nh_ynaa_teaser_settings['source'] = 'recent';
                $nh_ynaa_teaser_settings['hidehome'] = 1;


                if ($recent_posts) {
                    foreach ($recent_posts as $recent) {
                        $nh_ynaa_teaser_settings['teaser'][]=$recent["ID"];
                    }
                }

                add_option('nh_ynaa_teaser_settings', $nh_ynaa_teaser_settings);
            }
            $ts_setting = get_option('nh_ynaa_teaser_settings_ts');
            if (!$ts_setting || is_null($ts_setting)) {
                add_option('nh_ynaa_teaser_settings_ts', $ts);
            }

            $nh_ynaa_homepreset_settings['ts'] = $ts;
            $args = array(
                'type'                     => 'post',

                'orderby'                  => 'name',
                'order'                    => 'ASC',
                'hide_empty'               => 1,
                'hierarchical'             => 1,
                'taxonomy'                 => NH_YNAA_Plugin::nh_find_taxonomies_with_avada($is_avada)
            );
            $categories = get_categories($args);
            $nh_ynaa_categories_settings = array();
            //$nh_ynaa_categories_settings['items'][-1] = array('img'=>'','cat_name'=>__('Events'));
            //$nh_ynaa_categories_settings['items'][-1] = array('img'=>'','cat_name'=>__('Locations'));
            if ($categories) {
                $i=1;
                foreach ($categories as $category) {
                    $nh_ynaa_homepreset_settings['items'][] = array('img'=>'', 'title'=>$category->name, 'allowRemove'=>1, 'id' => $category->term_id, 'type'=>'cat', 'id2'=>$i);
                    $nh_ynaa_categories_settings[$category->term_id] = array('img'=>'', 'cat_name'=>$category->name, 'cat_order'=>'date-desc', 'hidecat'=>0, "usecatimg"=>0);
                    $i++;
                }
                $nh_ynaa_categories_settings['ts'] = $ts;
            }
            $nh_ynaa_homepreset_settings['homescreentype']=1;
            $nh_ynaa_homepreset_settings['sorttype']='date-desc';
            $nh_ynaa_homepreset_settings['posttype']['post']=1;

            $ts_setting = get_option('nh_ynaa_homepreset_settings');
            if (!$ts_setting || is_null($ts_setting)) {
                update_option('nh_ynaa_homepreset_settings', $nh_ynaa_homepreset_settings);
            }
            $ts_setting = get_option('nh_ynaa_homepreset_settings_ts');
            if (!$ts_setting || is_null($ts_setting)) {
                update_option('nh_ynaa_homepreset_settings_ts', $ts);
            }
            $ts_setting = get_option('nh_ynaa_push_settings');
            if (!$ts_setting || is_null($ts_setting)) {
                update_option('nh_ynaa_push_settings', array('pushshow'=>1, 'appkey'=>'APPKEY', 'pushsecret'=>'PUSHSECRET')); /**no autopush enter pushkeys later*/
            }
            $ts_setting = get_option('nh_ynaa_push_settings_ts');
            if (!$ts_setting || is_null($ts_setting)) {
                update_option('nh_ynaa_push_settings_ts', $ts);
            }
            $ts_setting = get_option('nh_ynaa_categories_settings');
            if (!$ts_setting || is_null($ts_setting)) {
                update_option('nh_ynaa_categories_settings', $nh_ynaa_categories_settings);
            }
            $ts_setting = get_option('nh_ynaa_categories_settings_ts');
            if (!$ts_setting || is_null($ts_setting)) {
                update_option('nh_ynaa_categories_settings_ts', $ts);
            }
            $ts_setting = get_option('nh_ynaa_articles_ts');
            if (!$ts_setting || is_null($ts_setting)) {
                update_option('nh_ynaa_articles_ts', $ts);
            }
        } // END public static function nh_ynaa_activate


        /**
         * Add Location Table
         */
         public static function nh_add_db_tables()
         {
             global $wpdb;
             global $nh_ynaa_db_version;
             $installed_ver = get_option("nh_ynaa_db_version");
             $table_name = $wpdb->prefix . "nh_locations";
             if ($installed_ver != $nh_ynaa_db_version) {
                 $sql = "CREATE TABLE `$table_name` (
								`location_id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
								`post_id` BIGINT(20) UNSIGNED NOT NULL,
								`blog_id` BIGINT(20) UNSIGNED NOT NULL,
								`location_slug` VARCHAR(200) NOT NULL,
								`location_name` TEXT NOT NULL,
								`location_owner` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0',
								`location_address` VARCHAR(200) NOT NULL,
								`location_town` VARCHAR(200) NOT NULL,
								`location_state` VARCHAR(200) NOT NULL,
								`location_postcode` VARCHAR(10) NOT NULL,
								`location_region` VARCHAR(200) NOT NULL,
								`location_country` CHAR(2) NOT NULL,
								`location_latitude` FLOAT(10,6) NOT NULL,
								`location_longitude` FLOAT(10,6) NOT NULL,
								`post_content` LONGTEXT NOT NULL,
								`location_status` INT(1) NOT NULL,
								`location_private` TINYINT(1) NOT NULL DEFAULT '0',
								`location_stamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
								`location_update_stamp` DATETIME NOT NULL,
								`location_pintype` VARCHAR(50) NOT NULL DEFAULT 'red',
								PRIMARY KEY (`location_id`),
								INDEX `location_state` (`location_state`),
								INDEX `location_region` (`location_region`),
								INDEX `location_country` (`location_country`),
								INDEX `post_id` (`post_id`),
								INDEX `blog_id` (`blog_id`)
							);
							";
                 require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
                 dbDelta($sql);
                 update_option("nh_ynaa_db_version", $nh_ynaa_db_version);
             }
         }


         /*
         * Plugin DB check
         */
         public static function nh_update_db_check()
         {
             global $nh_ynaa_db_version;

             if (get_option('nh_ynaa_db_version') != $nh_ynaa_db_version) {
                 NH_YNAA_Plugin::nh_add_db_tables();
             }

             global $nh_ynaa_version;
             $nh_ynaa_version_old =  get_option('nh_ynaa_plugin_version');
             if ($nh_ynaa_version_old != $nh_ynaa_version) {
                 if (ini_get('allow_url_fopen')) {
                     $content = @file_get_contents('http://www.blappsta.com?bas=extra_infos&url='.urlencode(get_bloginfo('url')));
                     if ($content) {
                         $json=json_decode($content, true);
                         update_option('nh_ynaa_blappsta', $json);
                         update_option('nh_ynaa_blappsta_ts', time());
                     }
                 }
             }
             if (!$nh_ynaa_version_old || $nh_ynaa_version_old <'0.7.2') {
                 $general_settings_old =  get_option('nh_ynaa_general_settings');

                 if ($general_settings_old['css']) {
                     update_option('nh_ynaa_css_settings', array('ts'=>$general_settings_old['ts'], 'css'=>$general_settings_old['css']));
                     update_option('nh_ynaa_css_settings_ts', $general_settings_old['ts']);
                 }
             }
             if (!$nh_ynaa_version_old || $nh_ynaa_version_old <'0.7.5') {
                 if (!get_option('nh_ynaa_teaser_settings_ts')) {
                     $teaser_settings_old =  get_option('nh_ynaa_teaser_settings');
                     $teaser_settings_old['limit'] = 4;
                     $teaser_settings_old['source'] = 'indi';
                     if (!$teaser_settings_old['ts']) {
                         $teaser_settings_old['ts']=time();
                     }
                     update_option('nh_ynaa_teaser_settings', $teaser_settings_old);
                     update_option('nh_ynaa_teaser_settings_ts', $teaser_settings_old['ts']);
                 }

                 if (!get_option('nh_ynaa_homepreset_settings_ts')) {
                     $general_settings_old =  get_option('nh_ynaa_general_settings');
                     $homepreset_settings_old =  get_option('nh_ynaa_homepreset_settings');
                     $homepreset_settings_old['homescreentype'] = $general_settings_old['homescreentype'];
                     $homepreset_settings_old['sorttype'] = $general_settings_old['sorttype'];


                     if (!$homepreset_settings_old['ts']) {
                         $homepreset_settings_old['ts']=time();
                     }
                     update_option('nh_ynaa_homepreset_settings', $homepreset_settings_old);
                     update_option('nh_ynaa_homepreset_settings_ts', $homepreset_settings_old['ts']);
                 }

                 if (!get_option('nh_ynaa_general_settings_ts')) {
                     $general_settings_old =  get_option('nh_ynaa_general_settings');
                     if (!$general_settings_old['ts']) {
                         $general_settings_old['ts'] = 0;
                     }
                     update_option('nh_ynaa_general_settings_ts', $general_settings_old['ts']);
                 }
             }
             if ($nh_ynaa_version_old != $nh_ynaa_version) {
                 update_option("nh_ynaa_plugin_version", $nh_ynaa_version);
             }
         }


        /**
         * Deative multisite
        */
        public static function nh_ynaa_deactivate($networkwide)
        {
            global $wpdb;

            if (function_exists('is_multisite') && is_multisite()) {
                // check if it is a network activation - if so, run the activation function
                // for each blog id
                if ($networkwide) {
                    $old_blog = $wpdb->blogid;
                    // Get all blog ids
                    $blogids = $wpdb->get_col("SELECT blog_id FROM {$wpdb->blogs}");
                    foreach ($blogids as $blog_id) {
                        switch_to_blog($blog_id);
                        NH_YNAA_Plugin::_nh_ynaa_deactivate();
                    }
                    switch_to_blog($old_blog);
                    return;
                }
            }
            NH_YNAA_Plugin::_nh_ynaa_deactivate();
        } // END public static function nh_ynaa_deactivate

        /**
         * Deactivate the plugin
         */
        public static function _nh_ynaa_deactivate()
        {
            //DELETE all  from WP options
        /*	delete_option('nh_ynaa_plugin_version');
            delete_option('nh_ynaa_general_settings');
            delete_option('nh_ynaa_menu_settings');
            delete_option('nh_ynaa_homepreset_settings');
            delete_option('nh_ynaa_teaser_settings');
//			delete_option('nh_ynaa_events_settings');
            delete_option('nh_ynaa_push_settings');
            delete_option('nh_ynaa_categories_settings');*/
        } // END public static function nh_ynaa_deactivate


        /*
         * Loads the general, menu, teaser and push settings from
         * the database into their respective arrays. Uses
         * array_merge to merge with default values if they're
         * missing.
         * And Setup default App Menu settings
         */
        public function nh_ynaa_load_settings()
        {
            $this->general_settings = (array) get_option($this->general_settings_key);
            $this->menu_settings = (array) get_option($this->menu_settings_key);
            $this->homepreset_settings = (array) get_option($this->homepreset_settings_key);
            $this->teaser_settings = (array) get_option($this->teaser_settings_key);
            $this->push_settings = (array) get_option($this->push_settings_key);
            $this->categories_settings = (array) get_option($this->categories_settings_key);
            $this->css_settings = (array) get_option($this->css_settings_key);

            // Merge with defaults
            $this->general_settings = array_merge(array(
                'general_option' => __('General value', 'nh-ynaa')
            ), $this->general_settings);

            $this->menu_settings = array_merge(array(
                'menu_option' => __('Menu value', 'nh-ynaa')
            ), $this->menu_settings);

            $this->teaser_settings = array_merge(array(
                'teaser_option' => __('Teaser value', 'nh-ynaa')
            ), $this->teaser_settings);

            $this->push_settings = array_merge(array(
                'push_option' => __('Push value', 'nh-ynaa')
            ), $this->push_settings);

            $this->css_settings = array_merge(array(
                'css_settings' => __('CSS', 'nh-ynaa')
            ), $this->css_settings);

            //set app menu
            $this->appmenus_pre[0] = array('title'=>__('Browse', 'nh-ynaa'),'status'=>1,'pos'=>1, 'id'=>0, 'type'=>'app', 'type_text'=>'App', 'link-typ'=>'cat');
            //$this->appmenus_pre[1] = array('title'=>__('Subscription','nh-ynaa'),'status'=>1,'pos'=>2, 'id'=>-99, 'type'=>'app', 'type_text'=>'App', 'link-typ'=>'cat');

            if (isset($this->general_settings['social_fbid'], $this->general_settings['social_fbsecretid'], $this->general_settings['social_fbappid'])) {
                $this->appmenus_pre[-2] = array('title'=>__('Facebook', 'nh-ynaa'),'status'=>1,'pos'=>3, 'id'=>-2, 'type'=>'fb', 'type_text'=>'Facebook', 'link-typ'=>'fb');
            }

            if ($this->general_settings['eventplugin']) {
                $this->appmenus_pre[-1] = array('title'=>__('Events', 'nh-ynaa'),'status'=>0,'pos'=>5, 'id'=>-1, 'type'=>'events', 'type_text'=>'App');
            }

            if (isset($this->general_settings['location'])) {
                $this->appmenus_pre[-98] = array('title'=>__('Map', 'nh-ynaa'),'status'=>1,'pos'=>6, 'id'=>-98, 'type'=>'map', 'type_text'=>__('App', 'nh-ynaa'), 'link-typ'=>'cat');
            }

            $this->appmenus_pre[-3] = array('title'=>__('Extern URL', 'nh-ynaa'),'status'=>1,'pos'=>7, 'id'=>-3, 'type'=>'webview', 'type_text'=>__('URL', 'nh-ynaa'), 'link-typ'=>'cat', 'extrafields'=>array('url'=>array('type'=>'url', 'label'=>__('URL'), 'value'=>'http://')));

            $this->appmenus_pre[-96] = array('title'=>__('Notifications', 'nh-ynaa'),'status'=>1,'pos'=>8, 'id'=>-96, 'type'=>'pushCenter', 'type_text'=>__('Pushcenter', 'nh-ynaa'), 'link-typ'=>'cat');

            //$this->appmenus_pre[-95] = array('title'=>__('Car Finder','nh-ynaa'),'status'=>1,'pos'=>9, 'id'=>-95, 'type'=>'carFinder', 'type_text'=>__('Car Finder', 'nh-ynaa'), 'link-typ'=>'cat');
            $this->appmenus_pre[-94] = array('title'=>__('Favorites', 'nh-ynaa'),'status'=>1,'pos'=>10, 'id'=>-94, 'type'=>'favorites', 'type_text'=>__('Favorites', 'nh-ynaa'), 'link-typ'=>'cat');
            $this->appmenus_pre[-93] = array('title'=>__('Search', 'nh-ynaa'),'status'=>1,'pos'=>11, 'id'=>-93, 'type'=>'search', 'type_text'=>__('Search', 'nh-ynaa'), 'link-typ'=>'cat');

            $this->teaser_support_type[] = 'webview';
            $this->teaser_support_type[] = 'favorites';
            $this->teaser_support_type[] = 'search';
        } // END  function nh_ynaa_load_settings()




        /**
         *Update routine
        */
        public function nh_ynaa_update_routine()
        {
            //$this->general_settings[''];
        }



        /*
        * Multisite loade Settings for new blog
        */
        public static function nh_new_blog($domain, $path, $title, $user_id, $meta, $site_id)
        {
            global $wpdb;
            $old_blog = $wpdb->blogid;
            update_option('nh_ynaa_general_settings_uma', "$domain, $path, $title, $user_id, $meta, $site_id");
            switch_to_blog($domain);
            NH_YNAA_Plugin::_nh_ynaa_activate();
            switch_to_blog($old_blog);
            /*
             if ( ! function_exists( 'is_plugin_active_for_network' ) )
                require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
              if (is_plugin_active_for_network('nh_ynaa/nh_ynaa_plugin.php')) {

                switch_to_blog($domain);
                NH_YNAA_Plugin::_nh_ynaa_activate();
                switch_to_blog($old_blog);
            }*/
        }//END  nh_new_blog


        /*
         * Registers the general settings via the Settings API,
         * appends the setting to the tabs array of the object.
         */
        public function nh_ynaa_qrcode_page()
        {
            $this->plugin_settings_tabs['qrcode'] = __('QR-Code', 'nh-ynaa');
        } // END function nh_ynaa_qrcode_page


        /*
         * Registers the homepreset settings via the Settings API,
         * appends the setting to the tabs array of the object.
         */
        public function nh_ynaa_register_homepreset_settings()
        {
            $this->plugin_settings_tabs[$this->homepreset_settings_key] = __('Startscreen', 'nh-ynaa');
            register_setting($this->homepreset_settings_key, $this->homepreset_settings_key);

            //Homepreset
            //add_settings_section( 'app_homepreset_settings', __('App Homepreset Settings<br><span>(Only if in startscreen view categories selected)</span>', 'nh-ynaa'), array( &$this, 'nh_ynaa_homepreset_settings_desc' ), $this->homepreset_settings_key );
            add_settings_section('app_homepreset_settings', null, array( &$this, 'nh_ynaa_homepreset_settings_desc' ), $this->homepreset_settings_key);
        }    // END function nh_ynaa_register_homepreset_settings

        /*
         * Registers the general settings via the Settings API,
         * appends the setting to the tabs array of the object.
         */
        public function nh_ynaa_register_general_settings()
        {
            $this->plugin_settings_tabs[$this->general_settings_key] = __('App Settings', 'nh-ynaa');

            register_setting($this->general_settings_key, $this->general_settings_key, array(&$this,'nh_ynaa_validate_setting'));

            //Logo
            add_settings_section('logo_setting', __('Logo', 'nh-ynaa'), array( &$this, 'nh_ynaa_section_general_logo' ), $this->general_settings_key);
            add_settings_field('ynaa-logo', __('Select Logo', 'nh-ynaa'). ' ('.$this->logo_image_width.'x'.$this->logo_image_height.')', array( &$this, 'nh_ynaa_field_general_option_logo' ), $this->general_settings_key, 'logo_setting', array('field'=>'logo'));
            //THEME
            add_settings_section('theme_setting', __('Theme', 'nh-ynaa'), array( &$this, 'nh_ynaa_section_general_theme' ), $this->general_settings_key);
            add_settings_field('ynaa-theme', __('Select theme', 'nh-ynaa'), array( &$this, 'nh_ynaa_field_general_theme_select' ), $this->general_settings_key, 'theme_setting', array('field'=>'theme'));

            //Color
            add_settings_section('app_settings', __('Color And Style Settings', 'nh-ynaa'), array( &$this, 'nh_ynaa_section_general_desc' ), $this->general_settings_key);
            add_settings_field('ynaa-c1', __('Primary Color', 'nh-ynaa'), array( &$this, 'nh_ynaa_field_general_option_color' ), $this->general_settings_key, 'app_settings', array('field'=>'c1'));
            add_settings_field('ynaa-c2', __('Secondary Color', 'nh-ynaa'), array( &$this, 'nh_ynaa_field_general_option_color' ), $this->general_settings_key, 'app_settings', array('field'=>'c2'));

            add_settings_field('ynaa-cn', __('Navigation Bar Color', 'nh-ynaa'), array( &$this, 'nh_ynaa_field_general_option_color' ), $this->general_settings_key, 'app_settings', array('field'=>'cn'));
            add_settings_field('ynaa-cm', __('Menu Text Color', 'nh-ynaa'), array( &$this, 'nh_ynaa_field_general_option_color' ), $this->general_settings_key, 'app_settings', array('field'=>'cm'));
            add_settings_field('ynaa-ch', __('Title Color', 'nh-ynaa'), array( &$this, 'nh_ynaa_field_general_option_color' ), $this->general_settings_key, 'app_settings', array('field'=>'ch'));
            //add_settings_field( 'ynaa-csh', __('Title 2 Color', 'nh-ynaa'), array( &$this, 'nh_ynaa_field_general_option_color' ), $this->general_settings_key, 'app_settings', array('field'=>'csh') );
            add_settings_field('ynaa-ct', __('Continuous Text Color', 'nh-ynaa'), array( &$this, 'nh_ynaa_field_general_option_color' ), $this->general_settings_key, 'app_settings', array('field'=>'ct'));
        //

            add_settings_field('ynaa-min-img-size-for-resize', __('Maximum width for images so they won‘t scale up (in px)', 'nh-ynaa'), array( &$this, 'nh_ynaa_field_general_option_input' ), $this->general_settings_key, 'app_settings', array('field'=>'min-img-size-for-resize'));

            //Hidden Fields
            add_settings_field('ynaa-ts', null, array( &$this, 'nh_ynaa_field_general_option_hidden' ), $this->general_settings_key, 'app_settings', array('field'=>'ts'));
            //Social Network
            add_settings_section('social_settings', __('Facebook Feed', 'nh-ynaa'), array( &$this, 'nh_ynaa_section_general_social' ), $this->general_settings_key);
            add_settings_field('ynaa-social_fbsecretid', __('Facebook App Secret', 'nh-ynaa'), array( &$this, 'nh_ynaa_field_general_social' ), $this->general_settings_key, 'social_settings', array('field'=>'social_fbsecretid'));
            add_settings_field('ynaa-social_fbappid', __('Facebook App ID', 'nh-ynaa'), array( &$this, 'nh_ynaa_field_general_social' ), $this->general_settings_key, 'social_settings', array('field'=>'social_fbappid'));
            add_settings_field('ynaa-social_fbid', __('Facebook Page ID', 'nh-ynaa'), array( &$this, 'nh_ynaa_field_general_social' ), $this->general_settings_key, 'social_settings', array('field'=>'social_fbid'));

            //Extras
            global $nh_ynaa_db_version;
            add_settings_section('extra_settings', __('Extras', 'nh-ynaa'), array( &$this, 'nh_ynaa_section_general_extra' ), $this->general_settings_key);
            add_settings_field('ynaa-lang', __('Language', 'nh-ynaa'), array( &$this, 'nh_ynaa_field_general_language' ), $this->general_settings_key, 'extra_settings', array('field'=>'lang'));
            add_settings_field('ynaa-showFeatureImageInPost', __('Activate feature image in post view', 'nh-ynaa'), array( &$this, 'nh_ynaa_field_general_extra_sort' ), $this->general_settings_key, 'extra_settings', array('field'=>'showFeatureImageInPost'));
            add_settings_field('ynaa-useFeatureImageOriginalSize', __('Use original feature image size in post view', 'nh-ynaa'), array( &$this, 'nh_ynaa_field_general_extra_sort' ), $this->general_settings_key, 'extra_settings', array('field'=>'useFeatureImageOriginalSize'));
            add_settings_field('ynaa-hidesharring', __('Hide sharing in app', 'nh-ynaa'), array( &$this, 'nh_ynaa_field_general_extra_sort' ), $this->general_settings_key, 'extra_settings', array('field'=>'hidesharing'));
            add_settings_field('ynaa-comments', __('Allow comments in app', 'nh-ynaa'), array( &$this, 'nh_ynaa_field_general_extra_sort' ), $this->general_settings_key, 'extra_settings', array('field'=>'comments'));
            add_settings_field('ynaa-hidedate', __('Hide published date', 'nh-ynaa'), array( &$this, 'nh_ynaa_field_general_extra_sort' ), $this->general_settings_key, 'extra_settings', array('field'=>'hidedate'));
            add_settings_field('ynaa-hideharing', __('Hide sharing buttons', 'nh-ynaa'), array( &$this, 'nh_ynaa_field_general_extra_sort' ), $this->general_settings_key, 'extra_settings', array('field'=>'hidesharing'));
            add_settings_field('ynaa-hideharing', __('Enable Text-to-Speech for', 'nh-ynaa'), array( &$this, 'nh_ynaa_field_general_text_to_speach' ), $this->general_settings_key, 'extra_settings', array('field'=>'textToSpeech'));
            add_settings_field('ynaa-relatedPosts', __('Show related posts', 'nh-ynaa'), array( &$this, 'nh_ynaa_field_general_extra_sort' ), $this->general_settings_key, 'extra_settings', array('field'=>'relatedPosts'));
            add_settings_field('ynaa-relatedDesign', __('Related posts theme', 'nh-ynaa'), array( &$this, 'nh_ynaa_field_general_theme_select' ), $this->general_settings_key, 'extra_settings', array('field'=>'relatedDesign'));
            add_settings_field('ynaa-relatedCount', __('Related posts count', 'nh-ynaa'), array( &$this, 'nh_ynaa_field_options_select' ), $this->general_settings_key, 'extra_settings', array('field'=>'relatedCount', 'options'=>array(1=>1, 2=>2, 3=>3, 4=>4, 5=>5, 6=>6, 7=>7, 8=>8)));

            //add_settings_field( 'ynaa-homescreentype', __('Startscreen view', 'nh-ynaa'), array( &$this, 'nh_ynaa_field_general_homescreentype' ), $this->general_settings_key, 'extra_settings' , array('field'=>'homescreentype'));
            //add_settings_field( 'ynaa-sorttype', __('Startscreen articles sorty by <br><span>(Only if startscreen view is articles or pages)</span>', 'nh-ynaa'), array( &$this, 'nh_ynaa_field_general_sorttype' ), $this->general_settings_key, 'extra_settings' , array('field'=>'sorttype'));
            //add_settings_field( 'ynaa-sort', __('Group by date', 'nh-ynaa'), array( &$this, 'nh_ynaa_field_general_extra_sort' ), $this->general_settings_key, 'extra_settings' , array('field'=>'sort'));


            add_settings_field('ynaa-eventplugin', __('Select your Event Manager:', 'nh-ynaa'), array( &$this, 'nh_ynaa_field_general_eventplugin' ), $this->general_settings_key, 'extra_settings', array('field'=>'eventplugin'));
            if (get_option('nh_ynaa_db_version') == $nh_ynaa_db_version) {
                add_settings_field('ynaa-location', __('Enable locations and activate location metabox in posts', 'nh-ynaa'), array( &$this, 'nh_ynaa_field_general_extra_sort' ), $this->general_settings_key, 'extra_settings', array('field'=>'location'));
            }
            add_settings_field('ynaa-nogallery', __('Deactivate gallery function in app', 'nh-ynaa'), array( &$this, 'nh_ynaa_field_general_extra_sort' ), $this->general_settings_key, 'extra_settings', array('field'=>'nogallery'));
            add_settings_field('ynaa-gadgetry', __('Use image from Gadgetry-Theme', 'nh-ynaa'), array( &$this, 'nh_ynaa_field_general_extra_sort' ), $this->general_settings_key, 'extra_settings', array('field'=>'gadgetry'));
            add_settings_field('ynaa-avada-categories', __('Avada Portfolio Categories', 'nh-ynaa'), array( &$this, 'nh_ynaa_field_general_extra_sort' ), $this->general_settings_key, 'extra_settings', array('field'=>'avada-categories'));
            add_settings_field('ynaa-gaTrackID', __('Google Analytics Tracking ID', 'nh-ynaa'), array( &$this, 'nh_ynaa_field_general_social' ), $this->general_settings_key, 'extra_settings', array('field'=>'gaTrackID'));

             // Debug Modus

             add_settings_section('advanced_modus', __('Advanced', 'nh-ynaa'), array( &$this, 'nh_ynaa_section_general_extra' ), $this->general_settings_key);

            add_settings_field('ynaa-blank_lines', __('Remove blank lines', 'nh-ynaa'), array( &$this, 'nh_ynaa_field_general_extra_sort' ), $this->general_settings_key, 'advanced_modus', array('field'=>'blank_lines'));

            add_settings_field('ynaa-utf8', __('Enable UTF8 encode', 'nh-ynaa'), array( &$this, 'nh_ynaa_field_general_extra_sort' ), $this->general_settings_key, 'advanced_modus', array('field'=>'utf8'));
            add_settings_field('ynaa-json_embedded', __('Use embedded JSON', 'nh-ynaa'), array( &$this, 'nh_ynaa_field_general_extra_sort' ), $this->general_settings_key, 'advanced_modus', array('field'=>'json_embedded'));
            add_settings_field('ynaa-domcontent', __('Disable dom convert', 'nh-ynaa'), array( &$this, 'nh_ynaa_field_general_extra_sort' ), $this->general_settings_key, 'advanced_modus', array('field'=>'domcontent'));
            add_settings_field('ynaa-debug', __('Enable debug mode', 'nh-ynaa'), array( &$this, 'nh_ynaa_field_general_extra_sort' ), $this->general_settings_key, 'advanced_modus', array('field'=>'debug'));

            //add_settings_field( 'ynaa-order_value', __('Order posts on overview page by', 'nh-ynaa'), array( &$this, 'nh_ynaa_field_general_extra_order' ), $this->general_settings_key, 'extra_settings' , array('field'=>'order_value'));
        } //END  function nh_ynaa_register_general_settings()

        /*
         * Validate Genere Settings Form
         * @$plugin_options array
         * return @$plugin_options array
         */
        public function nh_ynaa_validate_setting($plugin_options)
        {
            //check uploade file
            $keys = array_keys($_FILES);
            $i = 0;
            foreach ($_FILES as $image) {
                // if a files was upload
                if ($image['size']) {
                    // if it is an image
                    if (preg_match('/(jpg|jpeg|png|gif)$/', $image['type'])) {
                        $override = array('test_form' => false);
                        // save the file, and store an array, containing its location in $file
                        $file = wp_handle_upload($image, $override);
                        if (function_exists('wp_get_image_editor')) {
                            $img = wp_get_image_editor($file['file']); // Return an implementation that extends <tt>WP_Image_Editor</tt>
                            if (! is_wp_error($img)) {
                                $img->resize($this->logo_image_width, $this->logo_image_height, true);
                                $f = $img->save($file['file']);
                            }
                            $plugin_options[$keys[$i]] = dirname($file['url']).'/'.basename($f['path']);
                        } elseif ($file) {
                            $plugin_options[$keys[$i]] = $file['url'];
                        }
                    } else {       // Not an image.
                        $plugin_options[$keys[$i]] = $this->general_settings_key['logo'];
                        // Die and let the user know that they made a mistake.
                         add_settings_error('app_settings', 'invalid-logo-image-file', _('Select your Logo.'));
                    }
                }
                // Else, the user didn't upload a file.
                // Retain the image that's already on file.
                else {
                    $options = get_option($this->general_settings_key);
                    $plugin_options[$keys[$i]] = $options[$keys[$i]];
                }
                $i++;
            }
            return $plugin_options;
        } // END function nh_ynaa_validate_setting

        /*
         * Registers the Menu settings and appends the
         * key to the plugin settings tabs array.
         */
        public function nh_ynaa_register_menu_settings()
        {
            $this->plugin_settings_tabs[$this->menu_settings_key] = __('Menu', 'nh-ynaa');
            register_setting($this->menu_settings_key, $this->menu_settings_key);

            //Menu
            add_settings_section('app_menu_settings', __('App Menu Settings', 'nh-ynaa'), array( &$this, 'nh_ynaa_menu_settings_desc' ), $this->menu_settings_key);
        } //END  function nh_ynaa_register_menu_settings()

        /*
         * Registers the Teaser settings and appends the
         * key to the plugin settings tabs array.
         */
        public function nh_ynaa_register_teaser_settings()
        {
            $this->plugin_settings_tabs[$this->teaser_settings_key] = __('Teaser', 'nh-ynaa');
            register_setting($this->teaser_settings_key, $this->teaser_settings_key);
            //Teaser
            add_settings_section('app_teaser_settings', __('App Teaser Settings', 'nh-ynaa'), array( &$this, 'nh_ynaa_teaser_settings_desc' ), $this->teaser_settings_key);
        } //END  function nh_ynaa_register_teaser_settings()


        /* Register categories tab */
        public function nh_ynaa_register_categories_settings()
        {
            $this->plugin_settings_tabs[$this->categories_settings_key] = __('Categories', 'nh-ynaa');
            register_setting($this->categories_settings_key, $this->categories_settings_key);
            //categories
            add_settings_section('categories_settings', __('Categories Settings', 'nh-ynaa'), array( &$this, 'nh_ynaa_categories_settings_desc' ), $this->categories_settings_key);
        } //END function nh_ynaa_register_categories_settings

        /*
         *
        */
        public function nh_ynaa_field_general_theme_select($field)
        {
            ?>
			<select  id="<?php echo $field['field']; ?>" name="<?php echo $this->general_settings_key; ?>[<?php echo $field['field']; ?>]" class="nh-floatleft">
                    	<option value="0"><?php _e('Everest', 'nh-ynaa'); ?></option>
                        <option value="1" <?php if ($this->general_settings[$field['field']]=='1') {
                echo ' selected';
            } ?>>Nebelhorn</option>
                        <!--<option value="2" <?php if ($this->general_settings[$field['field']]=='2') {
                echo ' selected';
            } ?>><?php _e('Hallasan', 'nh-bas'); ?></option>-->
                        <option value="3" <?php if ($this->general_settings[$field['field']]=='3') {
                echo ' selected';
            } ?>><?php _e('Kilimanjaro', 'nh-ynaa'); ?></option>
                        <!--<option value="4" <?php if ($this->general_settings[$field['field']]=='4') {
                echo ' selected';
            } ?>><?php _e('Ayers Rock', 'nh-ynaa'); ?></option>-->
                    </select>
           <?php
           echo '<div class="helptext padding5">';
            switch ($field['field']) {
            case 'relatedDesign': echo(__('Select your app related posts theme.', 'nh-ynaa')); break;
                default: echo(__('Select your app theme.', 'nh-ynaa'));
           }
            echo '</div>';
        }

        /*
         * Registers the Push settings and appends the
         * key to the plugin settings tabs array.
         */
        public function nh_ynaa_register_push_settings()
        {
            $this->plugin_settings_tabs[$this->push_settings_key] = __('Push', 'nh-ynaa');
            register_setting($this->push_settings_key, $this->push_settings_key);
            //Push
            add_settings_section('app_push_settings', __('App Push Settings', 'nh-ynaa'), array( &$this, 'nh_ynaa_push_settings_desc' ), $this->push_settings_key);

            add_settings_field('ynaa-appkey', __('App Key', 'nh-ynaa'), array( &$this, 'nh_ynaa_field_push_option' ), $this->push_settings_key, 'app_push_settings', array('field'=>'appkey'));
            add_settings_field('ynaa-pushsecret', __('PUSHSECRET', 'nh-ynaa'), array( &$this, 'nh_ynaa_field_push_option' ), $this->push_settings_key, 'app_push_settings', array('field'=>'pushsecret'));
            //add_settings_field( 'ynaa-pushurl', __('PUSHURL', 'nh-ynaa'), array( &$this, 'nh_ynaa_field_push_option' ), $this->push_settings_key, 'app_push_settings' , array('field'=>'pushurl'));
            add_settings_field('ynaa-pushshow', __('Show Push Metabox', 'nh-ynaa'), array( &$this, 'nh_ynaa_field_push_checkbox' ), $this->push_settings_key, 'app_push_settings', array('field'=>'pushshow'));
            add_settings_field('ynaa-autopush', __('Automatic Push send', 'nh-ynaa'), array( &$this, 'nh_ynaa_field_push_checkbox' ), $this->push_settings_key, 'app_push_settings', array('field'=>'autopush'));
            add_settings_field('ynaa-jspush', __('Force Javascript Push', 'nh-ynaa'), array( &$this, 'nh_ynaa_field_push_checkbox' ), $this->push_settings_key, 'app_push_settings', array('field'=>'jspush'));
            add_settings_field('ynaa-hidehistory', __('Hide Push history', 'nh-ynaa'), array( &$this, 'nh_ynaa_field_push_checkbox' ), $this->push_settings_key, 'app_push_settings', array('field'=>'hidehistory'));

            //Timestamp
            add_settings_field('ynaa-ts', null, array( &$this, 'nh_ynaa_field_push_hidden' ), $this->push_settings_key, 'app_push_settings', array('field'=>'ts'));

            //iBeacon
            /*add_settings_section( 'app_ibeacon_settings', __('iBeacon Settings', 'nh-ynaa'), array( &$this, 'nh_ynaa_ibeacon_settings_desc' ), $this->push_settings_key );
            add_settings_field( 'ynaa-ts', null, array( &$this, 'nh_ynaa_field_general_option_hidden' ), $this->general_settings_key, 'app_ibeacon_settings', array('field'=>'ts') );
            add_settings_field( 'ynaa-uuid', __('UUID ', 'nh-ynaa'), array( &$this, 'nh_ynaa_field_push_option' ), $this->push_settings_key, 'app_ibeacon_settings' , array('field'=>'uuid'));
            add_settings_field( 'ynaa-welcome', __('Welcome text ', 'nh-ynaa'), array( &$this, 'nh_ynaa_field_push_option_textarea' ), $this->push_settings_key, 'app_ibeacon_settings' , array('field'=>'welcome'));
            add_settings_field( 'ynaa-silent', __('Silent intervall (sec) ', 'nh-ynaa'), array( &$this, 'nh_ynaa_field_push_option' ), $this->push_settings_key, 'app_ibeacon_settings' , array('field'=>'silent'));
            */
            $i=0;
            /*if(isset($this->push_settings['ibeacon']) && is_array($this->push_settings['ibeacon']) && count($this->push_settings['ibeacon'])>0){
                foreach($this->push_settings['ibeacon'] as $becon) {
                    add_settings_field( 'ynaa-ibeacon', __('iBeacon', 'nh-ynaa').(' '.($i+1)), array( &$this, 'nh_ynaa_field_ibeacon_content_option' ), $this->push_settings_key, 'app_ibeacon_settings' , array('field'=>ibeacon, 'key'=>$i));
                    $i++;
                }
            }*/
            //add_settings_field( 'ynaa-ibeacon', __('iBeacon ', 'nh-ynaa'), array( &$this, 'nh_ynaa_field_ibeacon_content_option' ), $this->push_settings_key, 'app_ibeacon_settings' , array('field'=>ibeacon));
        } //END  function nh_ynaa_register_push_settings()

        /*
         * Registers the Push settings and appends the
         * key to the plugin settings tabs array.
         */
        public function nh_ynaa_register_css_settings()
        {
            $this->plugin_settings_tabs[$this->css_settings_key] = __('CSS', 'nh-ynaa');
            register_setting($this->css_settings_key, $this->css_settings_key);

            add_settings_section('css_settings', __('CSS Style settings', 'nh-ynaa'), array( &$this, 'nh_ynaa_css_settings_desc' ), $this->css_settings_key);


            add_settings_field('ynaa-css', __('CSS Style', 'nh-ynaa').'<br>'.__('<span style="font-weight:normal;">Define here your CSS style for the content in the app.</span>', 'nh-ynaa'), array( &$this, 'nh_ynaa_field_general_option_css' ), $this->css_settings_key, 'css_settings', array('field'=>'css'));
            //Timestamp
            add_settings_field('ynaa-ts', null, array( &$this, 'nh_ynaa_field_css_hidden' ), $this->css_settings_key, 'css_settings', array('field'=>'ts'));
        }//END  function nh_ynaa_register_css_settings()


        public function nh_ynaa_field_options_select($field)
        {
            ?>
			<select  id="nh_<?php echo $field['field']; ?>" name="<?php echo $this->general_settings_key; ?>[<?php echo $field['field']; ?>]" class="nh-floatleft">
            	<?php
                 foreach ($field['options'] as $key => $value) {
                     echo '<option value="'.$key.'" ';
                     if (isset($this->general_settings[$field['field']]) && $this->general_settings[$field['field']]==$key) {
                         echo ' selected="selected" ';
                     }
                     echo '>'.$value.'</option>';
                 } ?>
            </select>
           <?php
           echo '<div class="helptext padding5">'.(__('Number of related posts to display', 'nh-ynaa')).'</div>';
        }//END function nh_ynaa_field_options_select

        /*
         * The following methods provide descriptions
         * for their respective sections, used as callbacks
         * with add_settings_section
         */
        /*function nh_ynaa_section_grcode(){
            echo '<div>88888</div>';
        }*/
        public function nh_ynaa_section_general_logo()
        {
        }
        public function nh_ynaa_section_general_theme()
        {
        }
        public function nh_ynaa_section_general_social()
        {
        }
        public function nh_ynaa_section_general_extra()
        {
        }
        public function nh_ynaa_section_general_desc()
        {
        }
        public function nh_ynaa_push_settings_desc()
        {
            _e('Please enter the push settings here that you have received from Team Blappsta after you have ordered your app. Only when these fields have been filled in correctly will you be able to send push notifications to your community. Please consider: If you wish to send a push notification you need to edit the post it shall be according to. There in the menus you will find the "Push Metabox".', 'nh-ynaa');
        }
        public function nh_ynaa_ibeacon_settings_desc()
        {
        }
        public function nh_ynaa_css_settings_desc()
        {
            _e('Here you can add CSS statements tthat are applied to the start view of your app. The standard CSS commands which have been defined for the website will not be regarded in your app.', 'nh-ynaa');
        }
        /*function nh_ynaa_events_settings_desc() {
            do_accordion_sections( 'nav-menus', 'side', null );
            $ynaa_menu = '';
            include('include/events.php');
        }*/
        public function nh_ynaa_menu_settings_desc()
        {
            if (function_exists('do_accordion_sections')) {
                do_accordion_sections('nav-menus', 'side', null);
            }
            $ynaa_menu = '';
            include('include/menu.php');
        }
        public function nh_ynaa_homepreset_settings_desc()
        {
            if (function_exists('do_accordion_sections')) {
                do_accordion_sections('nav-menus', 'side', null);
            }
            $ynaa_menu = '';
            include('include/homepreset.php');
        }
        public function nh_ynaa_teaser_settings_desc()
        {
            global $options;
            foreach ($options as $value) {
                if (get_option($value['id']) === false) {
                    $$value['id'] = $value['std'];
                } else {
                    $$value['id'] = get_option($value['id']);
                }
            }
            if (function_exists('do_accordion_sections')) {
                do_accordion_sections('nav-menus', 'side', null);
            }
            $ynaa_menu = '';
            include('include/teaser.php');
        }


        public function nh_ynaa_categories_settings_desc()
        {
            require 'include/nh_ynaa_categories_settings_desc_.php';
        }

        public function section_menu_desc()
        {
            _e('Set the app menu.', 'nh-ynaa');
        }



        /*
         * General Option field callback logo
         */
        public function nh_ynaa_field_general_option_logo($field)
        {
            ?>
			<input data-id="blappsta_logo_cont" type="hidden" name="<?php echo $this->general_settings_key; ?>[<?php echo $field['field']; ?>]" id="blappsta_app_logo" value="<?php echo esc_attr($this->general_settings[$field['field']]); ?>"   />
			<a href='#' class="upload_image_button" name="blappsta_app_logo"><?php _e('Change logo', 'nh-ynaa'); ?></a>
			<div id="blappsta_logo_cont" style="background-image: url('<?php echo(esc_attr($this->general_settings['logo'])); ?>')"></div>
			<?php
            //if($this->general_settings['logo'])  echo '<img src="'.(esc_attr( $this->general_settings['logo'])).'" align="middle" width="'.((int) ($this->logo_image_width/2)).'" height="'.((int) ($this->logo_image_height/2)).'" />';
            ?>

			<?php

        } // END function nh_ynaa_field_general_option_logo

        /*
         * General Option field callback color
         */
        public function nh_ynaa_field_general_option_color($field)
        {
            ?>
			<input type="text" name="<?php echo $this->general_settings_key; ?>[<?php echo $field['field']; ?>]" value="<?php echo esc_attr($this->general_settings[$field['field']]); ?>" class="my-color-field" /><?php
            switch ($field['field']) {
                case 'c1': echo '<div class="helptext">'.(__('Color of main elements, e.g. dog ears, menu button, time stamps, category names, empty tiles, bullet points, social icons, etc.', 'nh-ynaa')).'</div>'; break;
                case 'c2': echo '<div class="helptext">'.(__('Color of further elements, e.g. homescreen and posts background, elements of the commentary section', 'nh-ynaa')).'</div>'; break;
                case 'cn': echo '<div class="helptext">'.(__('Color of title bar, navigation bar, quicknavigation bar', 'nh-ynaa')).'</div>'; break;
                case 'cm': echo '<div class="helptext">'.(__('Text color in main menu and navigation menus (browse, subscribe, etc.)', 'nh-ynaa')).'</div>'; break;
                case 'ch': echo '<div class="helptext">'.(__('Text color of post headlines', 'nh-ynaa')).'</div>'; break;
                case 'csh': echo '<div class="helptext">'.(__('Text color of post sub headlines', 'nh-ynaa')).'</div>'; break;
                case 'ct': echo '<div class="helptext">'.(__('Text color of continuous text', 'nh-ynaa')).'</div>'; break;
                default: break;
            } ?>
			<?php

        }

        /*
         * General Option field callback color
         */
        public function nh_ynaa_field_general_option_input($field)
        {
            ?>
			<input type="number" name="<?php echo $this->general_settings_key; ?>[<?php echo $field['field']; ?>]" value="<?php if (isset($this->general_settings[$field['field']])) {
                echo esc_attr($this->general_settings[$field['field']]);
            } else {
                echo 100;
            } ?>" class="my-input-field nh-floatleft" /><?php
            switch ($field['field']) {
                case 'min-img-size-for-resize': echo '<div class="helptext">'.(__('Images wider than this will be scaled up to full display width', 'nh-ynaa')).'</div>'; break;

                default: break;
            } ?>
			<?php

        }



        /*
         * General Option field callback CSS
         */
        public function nh_ynaa_field_general_option_css($field)
        {
            ?>
			<textarea id="css_textarea" name="<?php echo $this->css_settings_key; ?>[<?php echo $field['field']; ?>]" class="nh-floatleft"><?php echo esc_attr($this->css_settings[$field['field']]); ?></textarea>
			<?php
            //echo '<div class="helptext">'.(__('Define here your CSS style for the content in the app.','nh-ynaa')).'</div>';
        } //END function nh_ynaa_field_general_option_css


        /*
         * General Option field hidden
         */
        public function nh_ynaa_field_general_option_hidden($field)
        {
            ?>
			<input type="hidden" name="<?php echo $this->general_settings_key; ?>[<?php echo $field['field']; ?>]" value="<?php echo time(); ?>" />
			<?php

        } // END function nh_ynaa_field_general_option_hidden


        /*
         * css Option field hidden
         */
        public function nh_ynaa_field_css_hidden($field)
        {
            ?>
			<input type="hidden" name="<?php echo $this->css_settings_key; ?>[<?php echo $field['field']; ?>]" value="<?php echo time(); ?>" />
			<?php

        } // END function nh_ynaa_field_general_option_hidden

        /**
         * css Option field hidden
         */
        public function nh_ynaa_field_push_hidden($field)
        {
            ?>
			<input type="hidden" name="<?php echo $this->push_settings_key; ?>[<?php echo $field['field']; ?>]" value="<?php echo time(); ?>" />
			<?php

        } // END function nh_ynaa_field_general_option_hidden

        /*
         * General Option field social callback
         */
        public function nh_ynaa_field_general_social($field)
        {
            ?>
			<input type="text" name="<?php echo $this->general_settings_key; ?>[<?php echo $field['field']; ?>]" value="<?php echo $this->general_settings[$field['field']]; ?>" class="nh-floatleft" />
			<?php
            switch ($field['field']) {
                case 'gaTrackID': echo '<div class="helptext">'.(__('Enter your Google Analytics Mobile App tracking ID here. You get it in your Google Analytics account.', 'nh-ynaa')).'</div>'; break;
                default: break;
            }
        } // END function nh_ynaa_field_general_social

        /*
         * General Option field hidden
         */
        public function nh_ynaa_field_menu_option_hidden($field)
        {
            ?>
			<input type="hidden" name="<?php echo $this->menu_settings_key; ?>[<?php echo $field['field']; ?>]" value="<?php echo time(); ?>" />
			<?php

        } // END function nh_ynaa_field_menu_option_hidden


        /*
         * push  Option field callback
         */
        public function nh_ynaa_field_push_option($field)
        {
            if ($this->push_settings['pushsecret'] === false || $this->push_settings['pushsecret'] === '') {
                $this->push_settings['pushsecret'] = 'PLATZHALTER PUSHSECRET';
                update_option($this->push_settings, $this->push_settings);
            } ?>
			<input type="text" id="id_<?php echo $field['field']; ?>" name="<?php echo $this->push_settings_key; ?>[<?php echo $field['field']; ?>]" value="<?php echo esc_attr($this->push_settings[$field['field']]); ?>" class="extraweit" />
			<?php

        } //END function nh_ynaa_field_push_option

        public function nh_ynaa_field_push_checkbox($field)
        {
            if (esc_attr($this->push_settings[$field['field']])=='1') {
                $check = ' checked="checked" ';
            } else {
                $check = '';
            } ?>
			<input type="checkbox" name="<?php echo $this->push_settings_key; ?>[<?php echo $field['field']; ?>]" id="<?php echo 'id_'.$field; ?>" <?php echo $check; ?> value="1"  class="my-input-field nh-floatleft" />
			<?php
            switch ($field['field']) {
                case 'jspush': echo '<div class="helptext">'.(__('Activate the checkbox case a proxy is used and the push delivery does not work.', 'nh-ynaa')).'</div>'; break;
                case 'autopush': echo '<div class="helptext">'.(__('Automatic sending of push notifications in the first publication of a post.', 'nh-ynaa')).'</div>'; break;
                case 'hidehistory': echo '<div class="helptext">'.(__('Hide push history from the app.', 'nh-ynaa')).'</div>'; break;
        default: break;
      }
        } //END function nh_ynaa_field_push_checkbox

        /*
         * push  Option field callback testarea
         */
        public function nh_ynaa_field_push_option_textarea($field)
        {
            ?>
            <textarea name="<?php echo $this->push_settings_key; ?>[<?php echo $field['field']; ?>]" class="extraweit"><?php echo esc_attr($this->push_settings[$field['field']]); ?></textarea>

			<?php

        } //END function nh_ynaa_field_push_option

        /*
         * push  Option ibeacon callback
         */
        public function nh_ynaa_field_ibeacon_content_option($field)
        {
            //var_dump($field, $this->push_settings[$field['field']]);
            ?>
            <fieldset><legend>iBeacon 1</legend>
				<label>Major</label><input type="text" name="<?php echo $this->push_settings_key; ?>[<?php echo $field['field']; ?>][0][major]" value="<?php if (isset($this->push_settings[$field['field']][0]['major'])) {
                echo esc_attr($this->push_settings[$field['field']][0]['major']);
            } ?>" class="extraweit" /><br>
				<label>Major2</label><input type="text" name="<?php echo $this->push_settings_key; ?>[<?php echo $field['field']; ?>][1][major]" value="<?php echo esc_attr($this->push_settings[$field['field']][1]['major']); ?>" class="extraweit" />

            </fieldset>
			<?php

        } //END function nh_ynaa_field_push_option

        /*
         * QR-Code Tab content
        */
        public function nh_the_qrcode_tab_content()
        {
            //	echo '<h3>'.__('QR-Code for Download Your App','nh-ynaa').'</h3>';
            //	echo '<p>'.__('To use this QR-Code for your News App. You have to install the yournewsapp from Appstore from Nebelhorn Medien.','nh-ynaa').'</p>';
            //	echo '<img src="https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=ynb://?url='.get_site_url().'&choe=UTF-8" alt="yna://?url='.get_site_url().'" />';
            //echo '<div>yna://?url='.get_site_url().'</div>';
        } //END function nh_the_qrcode_tab_content

        /*
    * Home  content
    */
        public function nh_the_home_content()
        {
            require 'include/nh_the_home_content.php';
        } //END function nh_the_home_content



        /*
         * LAngugae
        */
        public function nh_ynaa_field_general_language($field)
        {
            ?>
			<select  id="nh_language" name="<?php echo $this->general_settings_key; ?>[<?php echo $field['field']; ?>]" class="nh-floatleft">
                <option value="en"><?php _e('English', 'nh-ynaa'); ?></option>
                <option value="fr" <?php if ($this->general_settings[$field['field']]=='fr') {
                echo ' selected';
            } ?>><?php _e('French', 'nh-ynaa'); ?></option>
                <option value="nl" <?php if ($this->general_settings[$field['field']]=='nl') {
                echo ' selected';
            } ?>><?php _e('Dutch', 'nh-ynaa'); ?></option>
                <option value="de" <?php if ($this->general_settings[$field['field']]=='de') {
                echo ' selected';
            } ?>><?php _e('German', 'nh-ynaa'); ?></option>
                <option value="it" <?php if ($this->general_settings[$field['field']]=='it') {
                echo ' selected';
            } ?>><?php _e('Italian', 'nh-ynaa'); ?></option>
                <option value="hr" <?php if ($this->general_settings[$field['field']]=='hr') {
                echo ' selected';
            } ?>><?php _e('Croatian', 'nh-ynaa'); ?></option>
                <option value="pt" <?php if ($this->general_settings[$field['field']]=='pt') {
                echo ' selected';
            } ?>><?php _e('Portuguese', 'nh-ynaa'); ?></option>
                <option value="ru" <?php if ($this->general_settings[$field['field']]=='ru') {
                echo ' selected';
            } ?>><?php _e('Russian', 'nh-ynaa'); ?></option>
                <option value="es" <?php if ($this->general_settings[$field['field']]=='es') {
                echo ' selected';
            } ?>><?php _e('Spanish', 'nh-ynaa'); ?></option>
                <option value="ro" <?php if ($this->general_settings[$field['field']]=='ro') {
                echo ' selected';
            } ?>><?php _e('Romanian', 'nh-ynaa'); ?></option>
                <option value="ar" <?php if ($this->general_settings[$field['field']]=='ar') {
                echo ' selected';
            } ?>><?php _e('Arabic', 'nh-ynaa'); ?></option>
            </select>
           <?php
           echo '<div class="helptext padding5">'.(__('Interface and dialogue language', 'nh-ynaa')).'</div>';
        }

        /**
        * articles ort by
        */
        public function nh_ynaa_field_general_sorttype($field)
        {
            //var_dump($this->general_settings[$field['field']]);
            ?>
            <select   <?php if (!$this->general_settings['homescreentype'] || !isset($this->general_settings['homescreentype'])) {
                echo 'disabled';
            } ?>  id="nh_sorttype" name="<?php echo $this->general_settings_key; ?>[<?php echo $field['field']; ?>]" class="nh-floatleft">
                    	<option value="date-desc" <?php if ($this->general_settings[$field['field']]=='date-desc') {
                echo ' selected';
            } ?>><?php _e('Recent posts', 'nh-ynaa'); ?></option>
                        <option value="date-asc" <?php if ($this->general_settings[$field['field']]=='date-asc') {
                echo ' selected';
            } ?>><?php _e('Oldest posts', 'nh-ynaa'); ?></option>
                        <option value="alpha-asc" <?php if ($this->general_settings[$field['field']]=='alpha-asc') {
                echo ' selected';
            } ?>><?php _e('Alphabetically', 'nh-ynaa'); ?></option>
                        <!--<option value="popular" <?php if ($this->general_settings[$field['field']]=='popular') {
                echo ' selected';
            } ?>><?php _e('Most popular posts', 'nh-ynaa'); ?></option> -->
                    </select>
            <?php
             echo '<div class="helptext padding5">'.(__('Post order on starscreen.', 'nh-ynaa')).'</div>';
        }

        /*
         * Event  Option field callback
         */
        public function nh_ynaa_field_general_eventplugin($field)
        {
            $events_plugins_names = array(1=>'Events Manager');
            $events_plugins = array(1=>'events-manager/events-manager.php');
            //$eventmanager = false;
            foreach ($events_plugins as $k=>$events_plugin) {
                if (is_plugin_active($events_plugin)) {
                    $aktiveventplugis[$k] = $events_plugin;
                 // $eventmanager = true;
                }
            }
            if ($aktiveventplugis && count($aktiveventplugis) > 0) {
                ?>
                	<select id="eventplugin" name="<?php echo $this->general_settings_key; ?>[<?php echo $field['field']; ?>]" class="nh-floatleft">
                    	<option value="0"><?php _e('Please select'); ?></option>
                        <?php
                        foreach ($aktiveventplugis as $k=>$eventplugin) {
                            $checked= '';
                            if ($this->general_settings[$field['field']]==$k) {
                                $checked= ' selected="selected" ';
                            }
                            echo '<option value="'.$k.'" '.$checked.'>'.$events_plugins_names[$k].'</option>';
                        } ?>
                    </select>
                <?php
                 echo '<div class="helptext">'.(__('This is a Blappsta business feature.', 'nh-ynaa')).' '.(__('You can select here a event plugin to show events in your app.', 'nh-ynaa')).'</div>';
            } else {
                _e('No supported Plugin installed. Please Install the Plugin Event Manager.', 'nh-ynaa');
                echo ' <a href="http://wordpress.org/plugins/events-manager/" target="_blank">';
                _e('Plugin Directory', 'nh-ynaa');
                echo '</a>';
            }
        } // END function nh_ynaa_field_general_eventplugin

        /*
         * Check if Support Event MAnager is installed
         * Return boolean
        */
        public function nh_ynaa_check_eventmanager()
        {
            $events_plugins_names = array(1=>'Events Manager');
            $events_plugins = array(1=>'events-manager/events-manager.php');
            //$eventmanager = false;
            foreach ($events_plugins as $k=>$events_plugin) {
                if (is_plugin_active($events_plugin)) {
                    $aktiveventplugis[$k] = $events_plugin;
                 // $eventmanager = true;
                }
            }
            if ($aktiveventplugis && count($aktiveventplugis) > 0) {
                return true;
            } else {
                return false;
            }
        } // END function nh_ynaa_check:eventmanager()

        /*
         * sort  Option field callback
         */
        public function nh_ynaa_field_general_extra_sort($field)
        {
            if (esc_attr($this->general_settings[$field['field']])=='1') {
                $check = ' checked="checked" ';
            } else {
                $check = '';
            } ?>
			<input value="1" type="checkbox" name="<?php echo $this->general_settings_key; ?>[<?php echo $field['field']; ?>]" id="<?php echo $this->general_settings_key; ?>[<?php echo $field['field']; ?>]" <?php echo $check; ?> class="nh-floatleft" />
			<?php
            switch ($field['field']) {
                case 'location': echo '<div class="helptext padding0">'.(__('This is a Blappsta business feature.', 'nh-ynaa')).(__('Activate this checkbox if they want under your posts show a map with a location.', 'nh-ynaa')).'</div>'; break;
                case 'sort': echo '<div class="helptext padding0">'.(__('Create separators for periods of time between posts<br>(only if post order within categories is set to "chronologically")', 'nh-ynaa')).'</div>'; break;
                case 'comments': echo '<div class="helptext padding0">'.(__('Turn the comments section beneath posts on or off.', 'nh-ynaa')).'</div>'; break;
                case 'hidesharing': echo '<div class="helptext padding0">'.(__('Don\'t show the sharing buttons under the post in the app.', 'nh-ynaa')).'</div>'; break;
                case 'hidedate': echo '<div class="helptext padding0">'.(__('Don\'t show  published date on the top of the post in the app.', 'nh-ynaa')).'</div>'; break;
                case 'relatedPosts': echo '<div class="helptext padding0">'.(__('Show related posts under the post in the app.', 'nh-ynaa')).'</div>'; break;
                case 'gadgetry': echo '<div class="helptext padding0">'.(__('Activate the checkbox if you use gadgetry theme image as post featured image.', 'nh-ynaa')).'</div>'; break;
                case 'json_embedded': echo '<div class="helptext padding0">'.(__('Activate the checkbox if you get the tip "Recent content could not be accessed. Please connect your device to the internet and try again." in the app emulator.', 'nh-ynaa')).'</div>'; break;
                case 'utf8': echo '<div class="helptext padding0">'.(__('Activate this checkbox if the content is not displayed correctly coded.', 'nh-ynaa')).'</div>'; break;
                case 'nogallery': echo '<div class="helptext padding0">'.(__('If this checkbox is checked the gallery with images attached to the post on the post view will be disabled.', 'nh-ynaa')).'</div>'; break;
                case 'domcontent': echo '<div class="helptext padding0">'.(__('Activate this checkbox if you don\'t see any content in the detail view.', 'nh-ynaa')).'</div>'; break;
                case 'debug': echo '<div class="helptext padding0">'.(__('Activate the checkbox if you have any problems with the app, this help us to find out the error.', 'nh-ynaa')).'</div>'; break;
                case 'blank_lines': echo '<div class="helptext padding0">'.(__('Activate the checkbox if you have to many blank lines on your content page in the app.', 'nh-ynaa')).'</div>'; break;
                case 'showFeatureImageInPost': echo '<div class="helptext padding0">'.(__('Active this checkbox to bind in the feature image in post view.', 'nh-ynaa')).'</div>'; break;
                case 'useFeatureImageOriginalSize': echo '<div class="helptext padding0">'.(__('Active this checkbox to show the feature image in original size in post view.', 'nh-ynaa')).'</div>'; break;
                case 'avada-categories': echo '<div class="helptext padding0">'.(__('Treat Avada portfolio categories as normal WordPress categories', 'nh-ynaa')).'</div>'; break;
                default: break;
            }
        }

        public function nh_ynaa_field_general_text_to_speach($field)
        {
            //var_dump($this->general_settings[$field['field']]);
            $texttospeach = $this->general_settings[$field['field']];
            $check = ''; ?>
			<input value="1" type="hidden" name="<?php echo $this->general_settings_key; ?>[<?php echo $field['field']; ?>][]"  />
			<?php
            $post_types = get_post_types(array('public'=>true));
            //var_dump($post_types);
            foreach ($post_types as $k=>$post_type) {
                if ($k == 'attachment') {
                    continue;
                }
                $check = '';
                if ($texttospeach==null && $k=='post') {
                    $check = ' checked="checked" ';
                } else {
                    if (in_array($k, $texttospeach)) {
                        $check = ' checked="checked" ';
                    }
                } ?>
				<label class="padding0" for="<?php echo $this->general_settings_key; ?>[<?php echo $field['field']; ?>][<?php echo $k; ?>]"><input value="<?php echo $k; ?>" type="checkbox"
					   name="<?php echo $this->general_settings_key; ?>[<?php echo $field['field']; ?>][]"
					   id="<?php echo $this->general_settings_key; ?>[<?php echo $field['field']; ?>][<?php echo $k; ?>]" <?php echo $check; ?>
					   class=""/>
						<?php echo $post_type; ?>
				</label>
				<?php

            }
        }


        public function nh_ynaa_field_general_extra_sort_0($field)
        {
            if (isset($this->general_settings[$field['field']])) {
                $check = ' checked="checked" ';
            } else {
                $check = '';
            } ?>
			<input value="0" type="checkbox" name="<?php echo $this->general_settings_key; ?>[<?php echo $field['field']; ?>]" id="<?php echo $this->general_settings_key; ?>[<?php echo $field['field']; ?>]" <?php echo $check; ?> class="nh-floatleft" />
			<?php
            switch ($field['field']) {
                default: break;
            }
        }


        /*
        * Order option field backup
        */
        public function nh_ynaa_field_general_extra_order($field)
        {
            if (esc_attr($this->general_settings[$field['field']])=='1') {
                $check = ' checked="checked" ';
            } else {
                $check = '';
            } ?>
            <select id="<?php echo $this->general_settings_key; ?>[<?php echo $field['field']; ?>]" name="<?php echo $this->general_settings_key; ?>[<?php echo $field['field']; ?>]">
            	<option value="date"><?php _e('date', 'nh-ynaa'); ?></option>
                <option <?php if ($this->general_settings[$field['field']]=='alphabetical') {
                echo 'selected';
            } ?> value="alphabetical"><?php _e('alphabetical', 'nh-ynaa'); ?></option>
                <option <?php if ($this->general_settings[$field['field']]=='random') {
                echo 'selected';
            } ?> value="random"><?php _e('random', 'nh-ynaa'); ?></option>
            </select>
            <?php

        }



        /*
         * Advanced Option field callback, same as above.
         */
        public function field_menu_option()
        {
            ?>
			<input type="text" name="<?php echo $this->menu_settings_key; ?>['menu']" value="<?php echo esc_attr($this->menu_settings['menu']); ?>" />

			<?php

        }

        /*
         * Called during admin_menu, adds an options
         * page under Settings called My Settings, rendered
         * using the nh_ynaa_plugin_options_page method.
         */
        public function nh_ynaa_add_admin_menus()
        {
            global $nh_menu_hook_ynaa;
            $nh_menu_hook_ynaa = add_options_page('Blappsta Plugin', 'Blappsta Plugin', 'manage_options', $this->plugin_options_key, array( &$this, 'nh_ynaa_plugin_options_page' ));
            add_action("load-{$nh_menu_hook_ynaa}", array(&$this,'nh_create_help_screen'));
        }



        /*
        * Function to create Help
        */
        public function nh_create_help_screen()
        {
            $this->nh_get_blappsta_extra();
            //var_dump($_POST);
        if ($_GET['settings-updated']==='true') {
            if ($_GET['tab']==='nh_ynaa_css_settings') {
                $ts =  get_option('nh_ynaa_css_settings_ts');
                if ($this->css_settings['ts']!=$ts) {
                    update_option('nh_ynaa_css_settings_ts', $this->css_settings['ts']);
                }
                //var_dump($this->css_settings, $this->css_settings['css_settings']);
            } elseif ($_GET['tab']==='nh_ynaa_teaser_settings') {
                $ts =  get_option('nh_ynaa_teaser_settings_ts');
                if ($this->teaser_settings['ts']!=$ts) {
                    update_option('nh_ynaa_teaser_settings_ts', $this->teaser_settings['ts']);
                }
            } elseif ($_GET['tab']==='nh_ynaa_general_settings'|| !isset($_GET['tab'])) {
                $ts =  get_option('nh_ynaa_general_settings_ts');
                if ($this->general_settings['ts']!=$ts) {
                    update_option('nh_ynaa_general_settings_ts', $this->general_settings['ts']);
                }
            } elseif ($_GET['tab']==='nh_ynaa_menu_settings') {
                $ts =  get_option('nh_ynaa_menu_settings_ts');
                if ($this->menu_settings['ts']!=$ts) {
                    update_option('nh_ynaa_menu_settings_ts', $this->menu_settings['ts']);
                    update_option('nh_ynaa_general_settings_ts', $this->menu_settings['ts']);
                }
            } elseif ($_GET['tab']==='nh_ynaa_homepreset_settings') {
                $ts =  get_option('nh_ynaa_homepreset_settings_ts');
                if ($this->homepreset_settings['ts']!=$ts) {
                    update_option('nh_ynaa_homepreset_settings_ts', $this->homepreset_settings['ts']);
                    update_option('nh_ynaa_general_settings_ts', $this->homepreset_settings['ts']);
                }
            } elseif ($_GET['tab']==='nh_ynaa_categories_settings') {
                $ts =  get_option('nh_ynaa_categories_settings_ts');
                if ($this->categories_settings['ts']!=$ts) {
                    update_option('nh_ynaa_categories_settings_ts', $this->categories_settings['ts']);
                }
            } elseif ($_GET['tab']==='nh_ynaa_push_settings') {
                $ts =  get_option('nh_ynaa_push_settings_ts');

                if ($this->push_settings['ts']!=$ts) {
                    update_option('nh_ynaa_push_settings_ts', $this->push_settings['ts']);
                }
            }
        }
            if (!class_exists('WP_Screen')) {
                return;
            }
        /**
         * Create the WP_Screen object against your admin page handle
         * This ensures we're working with the right admin page
         */
        $this->admin_screen = WP_Screen::get($this->admin_page);

        /**
         * Content specified inline
         */
        $this->admin_screen->add_help_tab(
            array(
                'title'    => __('Blappsta PREVIEW app', 'nh-ynaa'),
                'id'       => 'app_preview_tab',
                'content'  => '<div id="previewapp_help2" class="">
								<h3>How to use the Blappsta PREVIEW app</h3>
								<div class="previewapp_content2">
								<div class="blappsta-plugin-header">
									<p>'.__('To get a preview on what the app would look like, please follow these steps:', 'nh-ynaa').'</p>
									<ul class="howtolist">
										<li>'.__('First of all download and install our <b>Blappsta Preview App</b> from the <a href="https://itunes.apple.com/de/app/blappsta-preview/id912390326?mt=8" target="_blank"  style="text-decoration:none;">Apple App Store</a> or from <a href="https://play.google.com/store/apps/details?id=com.blappsta.blappstaappcheck" target="_blank"   style="text-decoration:none;">Google Play&trade; Store</a>', 'nh-ynaa').'
										<br>
										<a href="https://itunes.apple.com/us/app/blappsta-preview/id912390326?mt=8&uo=4" target="itunes_store" style="display:inline-block;overflow:hidden;background:url(https://linkmaker.itunes.apple.com/htmlResources/assets/en_us//images/web/linkmaker/badge_appstore-lrg.png) no-repeat;width:135px;height:40px; padding-top:1px; margin-right:10px;@media only screen{background-image:url(https://linkmaker.itunes.apple.com/htmlResources/assets/en_us//images/web/linkmaker/badge_appstore-lrg.svg);}"></a>
          								<a href="https://play.google.com/store/apps/details?id=com.blappsta.blappstaappcheck" data-hover=""><img src="https://play.google.com/intl/en_us/badges/images/generic/en_badge_web_generic.png" alt="Android app on Google Play" width="150" style="vertical-align: bottom;"  ></a>
          								</li>
          								<li>'.__('Start the <b>Blappsta Preview App</b> and enter your blog’s URL or simply scan the QR-code below with our integrated scanner.', 'nh-ynaa').'
          								<br>
										<a href="https://chart.googleapis.com/chart?chs=125x125&cht=qr&chl=yba://?url='.get_site_url().'&choe=UTF-8"><img width="125" src="https://chart.googleapis.com/chart?chs=200x200&cht=qr&chl='.get_site_url().'&choe=UTF-8" alt="'.get_site_url().'" title="'.get_site_url().'" /></a>
										</li>
										<li>'.__('Of course all of the settings can be changed at any time. A simple „pull to refresh“ suffices in order to take over the settings in the app.', 'nh-ynaa').'</li>
									</ul>
								</div>
								<div style="float:left;">
									<div id="nh-simulator">
										<h3>'.__('Video tutorial: How to use the Blappsta PREVIEW app', 'nh-ynaa').'</h3>
										<div><iframe width="400" height="223" src="//www.youtube-nocookie.com/embed/Ng6xlcZr7Uw" frameborder="0" allowfullscreen=""></iframe></div>
									</div>
			 					</div>
			 					<div class="clear"></div>
								</div>
								</div>
								<p>'.__('If you like the app, please register on our website', 'nh-ynaa').' <a href="http://www.blappsta.com/sign-up?src=plugin&url='.urlencode(get_bloginfo('url')).'" target="_blank"  style="text-decoration:none;">www.blappsta.com</a>.</p>
          						<p>'.__('If you have any questions contact us: <a href="mailto:support@blappsta.com"  style="text-decoration:none;">support@blappsta.com</a>', 'nh-ynaa').'</p>

								',

                'callback' => false
            )
        );
            $this->admin_screen->add_help_tab(
            array(
                'title'    => __('Help'),
                'id'       => 'help_tab',
                'content'  => '<p>'.__('For help visit our website <a href="https://www.blappsta.com/hc/en-us" target="_blank">www.blappsta.com</a>.').'</p>',
                'callback' => false
            )
        );


        /**
         * Content generated by callback
         * The callback fires when tab is rendered - args: WP_Screen object, current tab
         */
        /*$this->admin_screen->add_help_tab(
            array(
                'title'    => 'Info on this Page',
                'id'       => 'page_info',
                'content'  => '',
                'callback' => create_function('','echo "<p>This is my generated content.</p>";')
            )
        );*/

        /*$this->admin_screen->set_help_sidebar(
            '<p>This is my help sidebar content.</p>'
        );*/

        /*$this->admin_screen->add_option(
            'per_page',
            array(
                'label' => 'Entries per page',
                'default' => 20,
                'option' => 'edit_per_page'
            )
        );

        $this->admin_screen->add_option(
            'layout_columns',
            array(
                'default' => 3,
                'max' => 5
            )
        );*/

        /**
         * This option will NOT show up
         */
        /*$this->admin_screen->add_option(
            'invisible_option',
            array(
                'label'	=> 'I am a custom option',
                'default' => 'wow',
                'option' => 'my_option_id'
            )
        );*/

        /**
         * But old-style metaboxes still work for creating custom checkboxes in the option panel
         * This is a little hack-y, but it works
         */
        /*add_meta_box(
            'my_meta_id',
            'My Metabox',
            array(&$this,'create_my_metabox'),
            $this->admin_page
        );*/
        }


        /*
         * Plugin Options page rendering goes here, checks
         * for active tab and replaces key with the related
         * settings key. Uses the nh_ynaa_plugin_options_tabs method
         * to render the tabs.
         */
        public function nh_ynaa_plugin_options_page()
        {
            if (!current_user_can('manage_options')) {
                wp_die(__('You do not have sufficient permissions to access this page.'));
            }
            $tab = isset($_GET[$this->requesvar['tab']]) ? $_GET[$this->requesvar['tab']] : $this->general_settings_key; ?>
			<div class="wrap">
				<!--<div id="icon-options-general" class="icon32"><br/></div>-->
				<h2><?php _e('Settings for Blappsta Plugin', 'nh-ynaa'); ?></h2>
				<?php
                    $this->nh_the_home_content();
            $this->nh_ynaa_plugin_options_tabs();
            if ($tab != 'qrcode') {
                ?>
				<form method="post" action="options.php" enctype="multipart/form-data" id="nh_ynaa_form" class="<?php echo $tab; ?>">
					<?php wp_nonce_field('update-options'); ?>
					<?php settings_fields($tab);
                $current_tab = isset($_GET[$this->requesvar['tab']]) ? $_GET[$this->requesvar['tab']] : $this->general_settings_key;
                if ($current_tab == $this->general_settings_key) {
                    echo '<div id="accordion" class="accordion-container">';
                } ?>

					<?php do_settings_sections($tab); ?>
					<?php if ($current_tab == $this->general_settings_key) {
                    echo '</div>';
                } ?>
					<div class="stickyBottom" id="ynaa_stickyBottom">

						<?php //submit_button();?>
						<p class="submit">
							<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes'); ?>" style="margin-right:20px;">
							<?php
                            $app_status = -1;
                if (ini_get('allow_url_fopen')) {
                    $content = @file_get_contents('https://www.blappsta.com?bas=extra_infos&url='.urlencode(get_bloginfo('url')));
                    if ($content) {
                        try {
                            $json=json_decode($content, true);

                            if (isset($json['app']['extra']['app_status'])) {
                                if ($json['app']['extra']['app_status']>1) {
                                    $app_status = 0; //App geordert
                                } else {
                                    $app_status = 2; //App nicht geordert
                                }
                            } else {
                                $app_status = 1; //App nicht vorhanden
                            }
                        } catch (exception $e) {
                        }
                    }
                }
                if ($app_status==-1) {
                    if ($this->push_settings['appkey'] && $this->push_settings['pushsecret']) {
                        $app_status = 0; //Push key eingetragen
                    } else {
                        $app_status = 2;
                    }
                }

                switch ($app_status) {
                                case 2:
                                case 1: _e('Click here to get your app:', 'nh-ynaa');
                                        echo ' <a href="https://www.blappsta.com/get-app/?src=plugin&url='.urlencode(get_bloginfo('url')).'" target="_blank"><input type="button"  class="button button-primary button-order" style="background-color: #ff8000; border-color: #ff8000; box-shadow: inset 0 1px 0 #ff8000;" value="'.__('Get my app', 'nh-ynaa').'"></a>'; break;
                                default: break;
                            } ?>

						</p>
					</div>

					<div style="margin-bottom:25px"></div>
				</form>
                <?php if ($tab == $this->general_settings_key) {
                                $this->nh_ynaa_simulator();
                            } ?>
                <?php

            } else {
                $this->nh_the_qrcode_tab_content();
            } ?>
			</div>
			<div class="clear"></div>
			<div style="margin-bottom:25px"></div>
			<?php

        }



        /*
         * Renders our tabs in the plugin options page,
         * walks through the object's tabs array and prints
         * them one by one. Provides the heading for the
         * nh_ynaa_plugin_options_page method.
         */
        public function nh_ynaa_plugin_options_tabs()
        {
            $current_tab = isset($_GET[$this->requesvar['tab']]) ? $_GET[$this->requesvar['tab']] : $this->general_settings_key;

            screen_icon();
            echo '<h2 class="nav-tab-wrapper" id="ynaa_nav_tab">';
            foreach ($this->plugin_settings_tabs as $tab_key => $tab_caption) {
                $active = $current_tab == $tab_key ? 'nav-tab-active' : '';
                echo '<a class="nav-tab ' . $active . '" href="?page=' . $this->plugin_options_key . '&tab=' . $tab_key . '">' . $tab_caption . '</a>';
            }
            // for extra tabs from addons
            foreach ($this->plugin_settings_ext_tabs as $tab_key => $tab_caption) {
                $active = $current_tab == $tab_key ? 'nav-tab-active' : '';
                echo '<a class="nav-tab ' . $active . '" href="?page=' . $this->
                    plugin_options_key . '&tab=' . $tab_key . '">' . $tab_caption . '</a>';
            }
            echo '</h2>';
        }




        /*
         *Load  Scripts and Styles
        */
        public function nh_ynaa_scripts($hook_suffix)
        {

            //wp_enqueue_script( 'ynaa-script-post-edit', plugins_url('js/ynaa-post-edit.js', __FILE__ ), array( 'jquery' ), '1.0', true );
            //wp_localize_script( 'ynaa-script-post-edit', 'ajax_object',
            //		array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'we_value' => 1234 ) );

            wp_enqueue_script('jquery');
            wp_enqueue_script('jquery-ui-core');
            wp_enqueue_script('jquery-ui-tabs');
            wp_enqueue_script('jquery-ui-accordion');
            wp_enqueue_script('jquery-ui-sortable');
            if ($hook_suffix =='post-new.php' || $hook_suffix =='post.php') {
                wp_register_script("ynaa-script-post-edit", plugins_url('js/ynaa-post-edit.js', __FILE__), array('jquery'));
                wp_localize_script('ynaa-script-post-edit', 'myAjax', array( 'ajaxurl' => admin_url('admin-ajax.php'), 'ajaxdata'=>array($hook_suffix)));

                wp_enqueue_script('jquery');
                wp_enqueue_script('ynaa-script-post-edit');
                wp_enqueue_style('ynaa-style-post-edit', plugins_url('css/ynaa_style_post_edit.css', __FILE__), array(), '1.0');
            }
            global $nh_menu_hook_ynaa;

            // exit function if not on my own options page!
            // $my_menu_hook_akt is generated when creating the options page, e.g.,
            // $my_menu_hook_akt = add_menu_page(...), add_submenu_page(...), etc


            if ($hook_suffix != $nh_menu_hook_ynaa) {
                return;
            }
            wp_enqueue_style('tooltipstercss', plugins_url('js/tooltipster.bundle.min.css', __FILE__));
            wp_enqueue_style('tooltipstercss-theme', plugins_url('js/tooltipster-sideTip-shadow.min.css', __FILE__));

            wp_register_style('wpb-jquery-ui-style', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/themes/ui-lightness/jquery-ui.css', false, null);
            wp_enqueue_style('wpb-jquery-ui-style');


            wp_enqueue_style('ynaa-style', plugins_url('css/ynaa_style.css', __FILE__), array(), '1.0');
            // first check that $hook_suffix is appropriate for your admin page
            wp_enqueue_style('wp-color-picker');
            wp_enqueue_script('media-upload');
            wp_enqueue_script('thickbox');

            wp_enqueue_script('ynaa-script-handle', plugins_url('js/ynaa.js', __FILE__), array( 'jquery', 'jquery-ui-core', 'jquery-ui-tabs', 'jquery-ui-accordion', 'jquery-ui-sortable', 'jquery-ui-draggable','wp-color-picker', 'media-upload','thickbox' ), '1.1', true);
            wp_enqueue_script('tooltipster', plugins_url('js/tooltipster.bundle.min.js', __FILE__), array('jquery'), '3.2.6');
            wp_enqueue_script('tooltipster-script', plugins_url('js/tooltipster-scripts.js', __FILE__), array('jquery','tooltipster'), '1.0.1');


            wp_enqueue_style('thickbox');


            $data = array('general_settings_key'=>$this->general_settings_key, 'menu_settings_key'=>$this->menu_settings_key, 'teaser_settings_key' => $this->teaser_settings_key, 'homepreset_settings_key'=>$this->homepreset_settings_key, 'delete2'=>__('Delete'), 'catText'=>__('Set default image for category', 'nh-ynaa') , 'allowremoveText' => __('Allow hide on Startscreen', 'nh-ynaa'), 'color01'=>$this->general_settings['c1'] , 'ajax_url' => admin_url('admin-ajax.php'), 'hideTitleStartscreen'=>__('Hide title from startscreen', 'nh-ynaa'), 'hideTitleStartscreenTT'=>__('Hide the tile title from the startscreen', 'nh-ynaa') );
            wp_localize_script('ynaa-script-handle', 'php_data', $data);
            //wp_localize_script( 'ynaa-script-handle', 'ajax_object',  array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
            if ('index.php' != $hook_suffix) {
                return;
            }    // Only applies to dashboard panel
        }


        /**
         * Output Json
         */
        public function nh_ynaa_template_redirect()
        {
            $ynaa_var = get_query_var('ynaa');
            do_action('nh_template:redirect', $ynaa_var);
            //remove action
            remove_action('init', 'dsq_request_handler');
            remove_action('dsq_sync_forum', 'dsq_sync_forum');
            remove_action('parse_request', 'dsq_parse_query');
            remove_action('the_posts', 'dsq_maybe_add_post_ids');
            remove_action('loop_end', 'dsq_loop_end');
            remove_action('wp_footer', 'dsq_output_footer_comment_js');
            remove_action('pre_comment_on_post', 'dsq_pre_comment_on_post');
            remove_filter('plugin_action_links', 'dsq_plugin_action_links', 10);

            header("HTTP/1.0 200 OK");
            $header = 'Content-Type: application/json; charset=utf-8';
            $start = '';
            $end = '';
            if ($ynaa_var) {
                date_default_timezone_set('UTC');
            }
            //global $wpdb;
           // $wpdb->insert('temp', array('value'=>'blappsta_plugin', 'text'=>serialize($_GET)));
            if ($this->general_settings['json_embedded']) {
                $header = 'Content-Type: text/plain; charset=UTF-8';
                $start = '[#NH_BLAPPSTA_START#]';
                $end = '[#NH_BLAPPSTA_END#]';
            }

            if ($ynaa_var=='settings' || $ynaa_var==$_GET['nh_prefix'].'_settings') {
                header($header);
                echo $start;
                $json = json_encode($this->nh_ynaa_settings());
                $json = apply_filters('nh_settings_json', $json);
                echo($json);
                echo $end;
            } elseif ($ynaa_var=='homepresets' || $ynaa_var==$_GET['nh_prefix'].'_homepresets') {
                header($header);
                echo $start;
                $json = json_encode($this->nh_ynaa_homepresets());
                $json = apply_filters('nh_homepresets_json', $json);
                echo($json);
                echo $end;
            } elseif ($ynaa_var=='teaser' || $ynaa_var==$_GET['nh_prefix'].'_teaser') {
                header($header);

                echo $start;
                $json = json_encode($this->nh_ynaa_teaser());
                $json = apply_filters('nh_teaser_json', $json);
                echo($json);
                echo $end;
            } elseif ($ynaa_var=='categories' || $ynaa_var==$_GET['nh_prefix'].'_categories') {
                header($header);
                echo $start;
                $json = json_encode($this->nh_ynaa_categories());
                $json = apply_filters('nh_categories_json', $json);
                echo($json);
                echo $end;
            } elseif ($ynaa_var=='articles' || $ynaa_var==$_GET['nh_prefix'].'_articles') {
                header($header);
                echo $start;
                $json = json_encode($this->nh_ynaa_articles());
                $json = apply_filters('nh_articles_json', $json);
                echo($json);
                echo $end;
            } elseif ($ynaa_var=='related' || $ynaa_var==$_GET['nh_prefix'].'_related') {
                header($header);
                echo $start;
                $json = json_encode($this->nh_ynaa_related());
                $json = apply_filters('nh_related_json', $json);
                echo($json);
                echo $end;
            } elseif ($ynaa_var=='article' || $ynaa_var==$_GET['nh_prefix'].'_article') {
                header($header);
                echo $start;
                $json = json_encode($this->nh_ynaa_article());
                $json = apply_filters('nh_article_json', $json);
                echo($json);
                echo $end;
               // $wpdb->insert('temp', array('value'=>'blappsta_article', 'text'=>serialize($json)));
                //$out1 = ob_get_contents();

                //ob_end_clean();
                //ob_end_flush();
                //echo substr($out1,strpos($out1,'<body>')+6,100);
            } elseif ($ynaa_var=='events' || $ynaa_var==$_GET['nh_prefix'].'_events') {
                header($header);
                echo $start;
                $json = json_encode($this->nh_ynaa_events());
                $json = apply_filters('nh_events_json', $json);
                echo($json);
                echo $end;
            } elseif ($ynaa_var=='event' || $ynaa_var==$_GET['nh_prefix'].'_event') {
                header($header);
                echo $start;
                $json = json_encode($this->nh_ynaa_event());
                $json = apply_filters('nh_event_json', $json);
                echo($json);
                echo $end;
            } elseif ($ynaa_var=='social' || $ynaa_var==$_GET['nh_prefix'].'_social') {
                header($header);
                echo $start;
                $json = json_encode($this->nh_ynaa_social());
                $json = apply_filters('nh_social_json', $json);
                echo($json);
                echo $end;
            } elseif ($ynaa_var=='comments' || $ynaa_var==$_GET['nh_prefix'].'_comments') {
                header($header);
                echo $start;
                $json = json_encode($this->nh_ynaa_comments());
                $json = apply_filters('nh_comments_json', $json);
                echo($json);
                echo $end;
            } elseif ($ynaa_var=='search' || $ynaa_var==$_GET['nh_prefix'].'_search') {
                header($header);
                echo $start;
                $json = json_encode($this->nh_ynaa_search());
                $json = apply_filters('nh_search_json', $json);
                echo($json);
                echo $end;
            } elseif ($ynaa_var=='locations' || $ynaa_var==$_GET['nh_prefix'].'_locations') {
                header($header);
                echo $start;
                $json = json_encode($this->nh_ynaa_locations());
                $json = apply_filters('nh_locations_json', $json);
                echo($json);
                echo $end;
            } elseif ($ynaa_var=='content' || $ynaa_var==$_GET['nh_prefix'].'_content') {
                header('Content-Type: text/html;charset=UTF-8');
                echo($this->nh_ynaa_content());

               // echo apply_filters('nh_html_content_last', $this->nh_ynaa_content());;
            } elseif ($ynaa_var=='yna_settings' || $ynaa_var==$_GET['nh_prefix'].'_yna_settings') {
                header($header);
                echo $start;
                $json = json_encode($this->nh_ynaa_yna_settings());
                $json = apply_filters('nh_yna_settings_json', $json);
                echo($json);
                echo $end;
            } elseif ($ynaa_var) {
                header($header);
                echo $start;
                echo(json_encode(array('error'=>$this->nh_ynaa_errorcode(11))));
                echo $end;
            } else {
                header($header);
                echo $start;
                echo(json_encode(array('error'=>$this->nh_ynaa_errorcode())));
                echo $end;
            }

            exit();
        } // END public function nh_ynaa_template_redirect()

        /**
         * Return Error Array
         */
        public function nh_ynaa_errorcode($er=10)
        {
            $errorarray = array();
            $errorarray['url']="http://".$_SERVER['HTTP_HOST'].'/?'.$_SERVER['QUERY_STRING'];
     // global $wpdb;
     // $wpdb->insert('temp',array( 'text'=>serialize(getallheaders())));

            //$errorarray['header'] = getallheaders();
            switch ($er) {
                case 0: $errorarray['error_code']= 0; $errorarray['error_message']='No Error'; break;
                case 11: $errorarray['error_code']= 11; $errorarray['error_message']='Unknown controller'; break;
                case 12: $errorarray['error_code']= 12; $errorarray['error_message']='No settings saved'; break;
                case 13: $errorarray['error_code']= 13; $errorarray['error_message']='Setting is empty'; break;
                case 14: $errorarray['error_code']= 14; $errorarray['error_message']='Menu is empty'; break;
                case 15: $errorarray['error_code']= 15; $errorarray['error_message']='No ID'; break;
                case 16: $errorarray['error_code']= 16; $errorarray['error_message']='No items for this category'; break;
                case 17: $errorarray['error_code']= 17; $errorarray['error_message']='No item whith this ID'; break;
                case 18: $errorarray['error_code']= 18; $errorarray['error_message']='No teaser set'; break;
                case 19: $errorarray['error_code']= 19; $errorarray['error_message']='No app items for this category'; break;
                case 20: $errorarray['error_code']= 20; $errorarray['error_message']='No categories'; break;
                case 21: $errorarray['error_code']= 21; $errorarray['error_message']='No Items in Categories'; break;
                case 22: $errorarray['error_code']= 22; $errorarray['error_message']='No events'; break;
                case 23: $errorarray['error_code']= 23; $errorarray['error_message']='No homepreset'; break;
                case 24: $errorarray['error_code']= 24; $errorarray['error_message']='Unknown social network'; break;
                case 25: $errorarray['error_code']= 25; $errorarray['error_message']='Facebook IDs required'; break;
                case 26: $errorarray['error_code']= 26; $errorarray['error_message']='No Facebook SDK'; break;
                case 27: $errorarray['error_code']= 27; $errorarray['error_message']='Facebook Error'; break;
                case 28: $errorarray['error_code']= 28; $errorarray['error_message']='Facebook query empty'; break;
                case 29: $errorarray['error_code']= 29; $errorarray['error_message']='Comments closed'; break;
                case 30: $errorarray['error_code']= 30; $errorarray['error_message']='Missed required value'; break;
                case 31: $errorarray['error_code']= 31; $errorarray['error_message']='email invalid'; break;
                case 32: $errorarray['error_code']= 32; $errorarray['error_message']='key already exists'; break;
                case 33: $errorarray['error_code']= 33; $errorarray['error_message']='No UUID'; break;
                case 34: $errorarray['error_code']= 34; $errorarray['error_message']='No location activ'; break;
                case 35: $errorarray['error_code']= 35; $errorarray['error_message']='This category ist now inactive for the app'; break;
                case 36: $errorarray['error_code']= 36; $errorarray['error_message']='No more itemes'; break;
                case 37: $errorarray['error_code']= 37; $errorarray['error_message']='Unknown teaser typ'; break;
                default: $errorarray['error_code']= 10; $errorarray['error_message']='Unknown Error'; break;
            }
            return ($errorarray);
        } // END private function errorcode()

        /**
         * Return Setting Array
         */
        private function nh_ynaa_settings()
        {
            $returnarray = array();
            require 'include/nh_ynaa_settings.php';


            return array('settings'=>$returnarray);
        } // END private function settings()



        /**
         * Return Homepresets Array
         */
        private function nh_ynaa_homepresets()
        {
            $returnarray['error']=$this->nh_ynaa_errorcode(0);
            require 'include/nh_ynaa_homepresets.php';
            return array('homepresets'=>$returnarray);
        } // END private function homepresets()

        /**
         * Return Teaser Array
         */
        private function nh_ynaa_teaser()
        {
            $returnarray['error']=$this->nh_ynaa_errorcode(0);
            require 'include/nh_ynaa_teaser.php';
            return array('teaser'=>$returnarray);
        } // END private function teaser()

        /**
         * Return Categories Array

         */
        private function nh_ynaa_categories()
        {
            $returnarray['error']=$this->nh_ynaa_errorcode(0);
            $returnarray['changes']=0;

            require 'include/nh_ynaa_categories.php';

            return array('categories'=>$returnarray);
        } // END private function categories()

        /**
         * Return Aricles Array
         */
        private function nh_ynaa_articles($id=0, $lim=0, $size=false, $intern = false)
        {
            do_action('nh_ynaa_articles_pre');
            $allowRemove=1;
            $returnarray['error']=$this->nh_ynaa_errorcode(0);
            if (isset($_GET[$this->requesvar['id']]) || $id) {
                if (($id)) {
                    $tempid= $id;
                } else {
                    $tempid= $_GET[$this->requesvar['id']];
                }
         // $returnarray['uma']['idwurdeübergeben'] = $tempid;
                    if ($_GET[$this->requesvar['cat_include']]) {
                        $cat_include = explode(',', $_GET[$this->requesvar['cat_include']]);
                    }
                if ($this->categories_settings[$tempid]['hidecat'] || ($_GET[$this->requesvar['meta']] && ($cat_include && !in_array($tempid, $cat_include)))) {
                    //$returnarray['uma']['$this->categories_settings[$tempid][\'hidecat\']'] = $this->categories_settings[$tempid]['hidecat'];
            //$returnarray['uma']['$cat_include'] = $cat_include;
                        $returnarray['changes']=1;
                    $returnarray['timestamp']=time();
                    $returnarray['uma_']['info_Articles'] = 'Die Kategorie wurde deaktiviert';
                    $returnarray['error']=$this->nh_ynaa_errorcode(35);
                    return array('articles'=>$returnarray);
                }
            }

            if (!empty($_GET[$this->requesvar['meta']]) && !isset($_GET[$this->requesvar['id']]) && !$id) {
                $_GET[$this->requesvar['option']]=1;
                $_GET[$this->requesvar['sorttype']] = 'date-desc';
                $this->homepreset_settings['sorttype']='date-desc';
                $_GET[$this->requesvar['limit']]=$lim;
            }

            if (($_GET[$this->requesvar['option']]==1 && $_GET[$this->requesvar['sorttype']])) {

                //$returnarray['uma']['switch'][] = '($_GET[$this->requesvar[\'option\']]==1 && $_GET[$this->requesvar[\'sorttype\']])';
                    // The Query
                    $returnarray['changes']=0;
                if ($_GET[$this->requesvar['ts']]) {
                    $returnarray['timestamp']=$_GET[$this->requesvar['ts']];
                } else {
                    $returnarray['timestamp']=0;
                }

                $timestamp = get_option('nh_ynaa_articles_ts');
                if ($returnarray['timestamp']<$timestamp) {
                    $returnarray['changes']=1;
                    $returnarray['timestamp']= $timestamp;
                }

                $img_size = 'thumbnail';
                if ($size) {
                    $img_size = $size;
                }
                $ynaa_controller = $_GET['nh_prefix'].'_articles';
                if ($_GET['ynaa'] == $ynaa_controller) {
                    $img_size= 'large';
                }
                    //var_dump($id); //debug
                    if (isset($_GET[$this->requesvar['id']])) {
                        $args['cat'] =$_GET[$this->requesvar['id']];
                    } elseif ($id) {
                        $args['cat'] =$id;
                    }

                if ($lim && $intern) {
                    $args['posts_per_page']= $lim;
                } elseif (isset($_GET[$this->requesvar['limit']])) {
                    $args['posts_per_page'] =$_GET[$this->requesvar['limit']];
                    if (isset($_GET[$this->requesvar['offset']])) {
                        $args['offset'] =$_GET[$this->requesvar['offset']];
                    }
                } else {
                    $args ['nopaging'] = true;
                }
                $hidecat = array();
                if ($this->categories_settings) {
                    foreach ($this->categories_settings as $cat_id => $cat) {
                        //var_dump($cat);

                            if ($cat['hidecat']) {
                                $hidecat[] = $cat_id * -1;
                            } elseif ($cat['hidecathome']) {
                                $hidecat[] = $cat_id * -1;
                            }
                    }
                }


                if (!empty($_GET[$this->requesvar['cat_include']])) {
                    $cat_include = explode(',', $_GET[$this->requesvar['cat_include']]);
                    foreach ($cat_include as $key => $value) {
                        $hidecat[] = $value;
                    }
                }
                if ($hidecat) {
                    if ($args['cat']) {
                        $hidecat[]=   $args['cat'];
                    }
                    $args['cat']=implode($hidecat, ',');
                    ;
                }

                if ($this->homepreset_settings['sorttype']=='alpha-asc') {
                    $args ['orderby'] = 'title';
                    $args ['order'] = 'ASC';
                } elseif ($this->homepreset_settings['sorttype']=='date-asc') {
                    $args ['order'] = 'ASC';
                        //$args ['orderby'] = 'post_date';
                } elseif ($this->homepreset_settings['sorttype']=='date-desc') {
                    $args ['order'] = 'DESC';
                      //$args ['orderby'] = 'post_date';
                }

                if ($this->homepreset_settings['homescreentype'] == 2) {
                    $args ['post_type'] = 'page';
                } elseif (isset($this->homepreset_settings['posttype'])) {
                    $args ['post_type'] = array_keys($this->homepreset_settings['posttype']);
                } else {
                    $args ['post_type'] = 'post';
                }
                    //echo $intern;
                    //Exclude Teaser ITems
                    if (!$intern  && strpos($_GET['ynaa'], 'teaser')===false && (!isset($this->teaser_settings['source']) || $this->teaser_settings['source']!='indi') && (isset($this->teaser_settings['hidehome'])) && $this->teaser_settings['limit']) {
                        $teasercat = 0;
                        //echo $this->teaser_settings['limit'];
                        if ($this->teaser_settings['source']=='cat') {
                            $teasercat =$this->teaser_settings['cat'];
                        }
                        $teaseritems = ($this->nh_ynaa_articles($teasercat, $this->teaser_settings['limit'], 'full', true));
                        //var_dump($teaseritems,$this->teaser_settings['source'],$this->teaser_settings['cat'],$teasercat);
                        if (($teaseritems)) {
                            $args['post__not_in']=$teaseritems;
                        }
                    } else {
                        //	echo 'notintern';
                    }

                $args ['post_status'] = 'publish';

                $args['meta_query'] = array(
                        'relation' => 'OR',
                        array(
                            'key' => '_nh_ynaa_hide_in_app',
                            'compare' => 'NOT EXISTS', // works!
                            'value' => '' // This is ignored, but is necessary...
                        ),
                        array(
                            'key' => '_nh_ynaa_hide_in_app',
                            'value' => '0'
                        )
                    );

                    //var_dump($args); //debug
                //	$args['post__not_in'] = get_option( '	' );
                    $the_query = new WP_Query($args);
                $post_ids_intern = array();
                    // The Loop
                    if ($the_query->have_posts()) {
                        $i=1;
                        while ($the_query->have_posts()) {
                            $the_query->the_post();

                            //var_dump($the_query->post->ID);

                            //Hide POSTS
                            /*$_nh_ynaa_meta_keys = (get_post_meta( $the_query->post->ID, '_nh_ynaa_meta_keys', true ));
                            if($_nh_ynaa_meta_keys){
                                $_nh_ynaa_meta_keys = unserialize($_nh_ynaa_meta_keys);
                                if($_nh_ynaa_meta_keys && is_array($_nh_ynaa_meta_keys)){
                                    if(is_null($_nh_ynaa_meta_keys['s'])) {
                                        //var_dump ($_nh_ynaa_meta_keys);
                                        continue;
                                    }
                                }

                            }*/

                            if (!$intern) {
                                $cat_id = 0;
                                $cat_id_array = $this->nh_getpostcategories($the_query->post->ID);

                                if ($cat_id_array) {
                                    $cat_id = (int)$cat_id_array[0];
                                }
                                $img = $this->nh_getthumblepic($the_query->post->ID);

                                $thumbnail = $this->nh_getthumblepic($the_query->post->ID, $img_size);
                                $images = $this->nh_getthumblepic_allsize($the_query->post->ID);

                                $post_type = get_post_type();

                                //Weil die App sonst nicht zu recht muss type auf post gesetzt werden
                                if ($post_type != 'page' && $post_type != 'event') {
                                    $post_type = 'article';
                                }
                                $posttitle = str_replace(array("\\r", "\\n", "\r", "\n"), '', trim(html_entity_decode(strip_tags(do_shortcode($the_query->post->post_title)), ENT_NOQUOTES, 'UTF-8')));

                                $posttitle = preg_replace_callback("/(&#[0-9]+;)/", function ($m) {
                                    return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES");
                                }, $posttitle);
                                if ($this->general_settings['theme'] == 3) {
                                    $excerpt = get_the_excerpt();
                                } else {
                                    $excerpt = '';
                                }

                                $returnarray['items'][] = array('uma'=>'test2','pos' => $i, "type" => $post_type, 'allowRemove' => 1, 'cat_id' => $cat_id, 'cat_id_array' => $cat_id_array, 'title' => $posttitle, 'img' => $thumbnail, 'thumb' => $thumbnail, 'images' => $images, 'post_id' => $the_query->post->ID, 'post_type' => $the_query->post->post_type,'extraRequestParam' => '&'.$this->prefix.'post_type='.$the_query->post->post_type, 'timestamp' => strtotime($the_query->post->post_modified), 'publish_timestamp' => strtotime($the_query->post->post_date),/*'post_date' =>strtotime($the_query->post->post_date),*/
                                    'showsubcategories' => 0, 'excerpt' => html_entity_decode(str_replace('[&hellip;]', '', strip_tags($excerpt))));

                                if (strtotime($the_query->post->post_modified) > $returnarray['timestamp']) {
                                    $returnarray['changes'] = 1;
                                    $returnarray['timestamp'] = strtotime($the_query->post->post_modified);
                                }
                            } else {
                                $post_ids_intern[]=$the_query->post->ID;
                            }
                            $i++;
                        }
                        if ($intern) {
                            return $post_ids_intern;
                        }
                    } else {
                        $returnarray['error']=$this->nh_ynaa_errorcode(36);
                    }
                    // Restore original Post Data
                    wp_reset_postdata();
                $returnarray['filter'] = 'nh_articles_array_option';
                $returnarray = apply_filters('nh_articles_array_option', $returnarray);
                return array('articles'=>$returnarray);
            }
            elseif (isset($_GET[$this->requesvar['id']]) || $id) {
                $returnarray['changes']=0;
                //PostID
                //If Post ID Check if is ist the newest Post and if hat changes
                if (isset($_GET[$this->requesvar['post_id']]) && isset($_GET[$this->requesvar['post_ts']])) {
                    $break = false;
                    $orderby = 'date';
                    $order = 'DESC';
                    $cid = $tempid;
                    if ($this->categories_settings[$cid]['cat_order']) {
                        switch ($this->categories_settings[$cid]['cat_order']) {
                            case 'alpha-asc': $orderby = 'title';    $order = 'ASC'; break;
                            case 'date-asc': $orderby = 'date';    $order = 'ASC'; break;
                            default: $orderby = 'date';    $order = 'DESC'; break;
                        }
                    }

                    $latest_cat_post = new WP_Query(array('posts_per_page' => 1,  'post_type'=>'any', 'orderby' => $orderby, 'order'=>$order , 'category__in' => array($_GET[$this->requesvar['id']])));
                    //var_dump($latest_cat_post); die();
                    //$returnarray['items'][] = $latest_cat_post;

                    if ($latest_cat_post->have_posts()) {
                        if ($latest_cat_post->posts[0]->ID == $_GET[$this->requesvar['post_id']]) {
                            $break = true;
                            if (strtotime($latest_cat_post->posts[0]->post_modified)>$_GET[$this->requesvar['post_ts']]) {
                                $ts = strtotime($latest_cat_post->posts[0]->post_modified);
                                $returnarray['changes']=1;
                                //var_dump($this->categories_settings[$_GET[$this->requesvar['id']]]);
                        /*		if ( has_post_thumbnail($latest_cat_post->posts[0]->ID)) {
                                    $post_thumbnail_image=wp_get_attachment_image_src(get_post_thumbnail_id($latest_cat_post->posts[0]->ID), 'original');
                                }
                                /*elseif($this->categories_settings[$_GET[$this->requesvar['id']]]['img']){
                                    $post_thumbnail_image=array($this->categories_settings[$_GET[$this->requesvar['id']]]['img']);
                                }*/
                        /*		else {
                                    $post_thumbnail_image=array();
                                }
                                */

                                $post_thumbnail_image[0] = $this->nh_getthumblepic($latest_cat_post->posts[0]->ID, 'original');
                                $images = $this->nh_getthumblepic_allsize($latest_cat_post->posts[0]->ID);
                                $posttitle = str_replace(array("\\r","\\n","\r", "\n"), '', trim(html_entity_decode(strip_tags(do_shortcode($latest_cat_post->posts[0]->post_title)), ENT_NOQUOTES, 'UTF-8')));
                                $posttitle = preg_replace_callback("/(&#[0-9]+;)/", function ($m) {
                                    return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES");
                                }, $posttitle);

                                $returnarray['items'][] = array('uma'=>'test3','pos'=>1, 'id'=>$latest_cat_post->posts[0]->ID,'title'=>$posttitle,'timestamp'=>strtotime($latest_cat_post->posts[0]->post_modified),'type'=>$latest_cat_post->posts[0]->post_type,'post_type'=>$latest_cat_post->posts[0]->post_type,'extraRequestParam'=>'&'.$this->prefix.'post_type='.$latest_cat_post->posts[0]->post_type, 'thumb'=> ($post_thumbnail_image[0]), 'images'=>$images, 'publish_timestamp'=> strtotime($latest_cat_post->posts[0]->post_date), 'post_date'=> strtotime($latest_cat_post->posts[0]->post_date));
                                //$returnarray['items'][]=array('pos'=>1, 'type' => $post->post_type, 'allowRemove'=> $allowRemove, 'id'=> $category->term_id, 'parent_id'=>0, 'title'=>$category->name, 'img'=>$post_thumbnail_image[0], 'post_id'=>$latest_cat_post->post->ID );
                            } else {
                                $ts = $_GET[$this->requesvar['post_ts']];
                            }
                        }
                    } else {
                        $break = true;
                        $returnarray['error']=$this->nh_ynaa_errorcode(16);
                        $ts = time();
                        $returnarray['items'][] = array();
                    }
                    if ($break) {
                        $returnarray['orderby']=$orderby;
                        $returnarray['order']=$order;
                        $returnarray['timestamp']=$ts;
                        $returnarray['error']=$this->nh_ynaa_errorcode(0);
                        $returnarray['filter'] = 'nh_articles_array_limit';
                        $returnarray = apply_filters('nh_articles_array_limit', $returnarray);
                        return array('articles'=>$returnarray);
                    }
                }

                //Kategorie ID

                if ($id) {
                    //echo 'id';
                    $cid = $id;
                    if ($lim) {
                        $limit = $lim;
                    } else {
                        $limit=999;
                    }
                } else {
                    //echo 'else id';
                    $cid = $_GET[$this->requesvar['id']];
                    //LIMIT
                    if ($_GET[$this->requesvar['limit']]) {
                        $limit=$_GET[$this->requesvar['limit']];
                    } elseif ($intern && $lim) {
                        $limit = $lim;
                    } else {
                        $limit = 999;
                    }
                }

                if ($_GET[$this->requesvar['offset']]) {
                    $offset=$_GET[$this->requesvar['offset']];
                } else {
                    $offset = 0;
                }

                //Timestamp
                if ($_GET[$this->requesvar['ts']]) {
                    $ts= $_GET[$this->requesvar['ts']];
                    //Immer cahnges true
                    //$ts=0;
                    $ts_string = date('Y-m-d H:i:s', $ts);
                } else {
                    $ts = 0;
                    $ts_string = date('0000-00-00 00:00:00');
                }
                //WP Query
                global $wpdb;
                $table_posts = $wpdb->prefix . "posts";
                $table_term_relationships = $wpdb->prefix . "term_relationships";

                $post_ids = false;
                if (!$post_ids) {
                    $orderby = 'date';
                    $order = 'DESC';
                    if ($this->categories_settings[$cid]['cat_order']) {
                        switch ($this->categories_settings[$cid]['cat_order']) {
                            case 'alpha-asc': $orderby = 'title';    $order = 'ASC'; break;
                            case 'date-asc': $orderby = 'date';    $order = 'ASC'; break;
                            default: $orderby = 'date';    $order = 'DESC'; break;
                        }
                    }

                    $args = array('posts_per_page'   => $limit, 'offset'=>$offset, 'category__in' => array($cid), 'orderby' => $orderby ,    'order' => $order);
                    if (!empty($this->exclude_posts)) {
                        $args['post__not_in'] = implode(',', $this->exclude_posts);
                    }
                    $args['meta_query'] = array(
                        'relation' => 'OR',
                        array(
                            'key' => '_nh_ynaa_hide_in_app',
                            'compare' => 'NOT EXISTS', // works!
                            'value' => '' // This is ignored, but is necessary...
                        ),
                        array(
                            'key' => '_nh_ynaa_hide_in_app',
                            'value' => '0'
                        )
                    );
                    //var_dump($args);
                    $posts_array = get_posts($args);
                    //$args = array('posts_per_page'   => -1, 'category__in' => array($cid), 'orderby' => $orderby ,	'order' => $order);
                    /*
                    $posts_array = query_posts( $args );
                    while(have_posts()) {
                         the_post();
                        $post_ids[] = $post->ID;
                    }
                    wp_reset_query();
                     * */
                    if ($posts_array) {
                        foreach ($posts_array as $po) {
                            //			var_dump($po->ID);
                            $post_ids[] = $po->ID;
                        }
                        if ($intern && $post_ids) {
                            return $post_ids;
                        }
                    }
                }
//				$post_ids = false;
                if (!$post_ids) {
                    //var_dump($this->categories_settings); die();
                    $orderby = 'date';
                    $order = 'DESC';
                    if ($this->categories_settings[$cid]['cat_order']) {
                        switch ($this->categories_settings[$cid]['cat_order']) {
                            case 'alpha-asc': $orderby = 'title';    $order = 'ASC'; break;
                            case 'date-asc': $orderby = 'date';    $order = 'ASC'; break;
                            default: $orderby = 'date';    $order = 'DESC'; break;
                        }
                    }
                    $exclude = '';
                    if (!empty($this->exclude_posts)) {
                        $exclude = ' AND p.ID NOT IN('.implode(',', $this->exclude_posts).')';
                    }
                    $post_ids = $wpdb->get_col($wpdb->prepare("select p.ID from $table_posts p
								left join $table_term_relationships tr on tr.object_id=p.ID
								where p.post_status='publish' and tr.term_taxonomy_id=$cid $exclude
								AND p.ID  NOT IN (SELECT post_id from {$wpdb->postmeta} WHERE meta_key='_nh_ynaa_hide_in_app' AND meta_value=1)
								order by p.post_$orderby $order
								LIMIT 1999", '%d'));
                }
                if ($post_ids) {
                    //var_dump($post_ids); die();
                    $returnarray['error']=$this->nh_ynaa_errorcode(0);
                    $returnarray['orderby']= $orderby;
                    $returnarray['order']= $order;
                    $returnarray['cat'] = $this->categories_settings[$cid];
                    $i=1;

                    foreach ($post_ids as $pid) {
                        if (isset($limit) && count($returnarray['items'])>=$limit) {
                            break;
                        }

                        //$postmeta = unserialize(get_post_meta( $pid, '_nh_ynaa_meta_keys', true ));
                        //if($postmeta  && $postmeta['s']!='on') continue;
                        $post = get_post($pid);
                        if ($ts < strtotime($post->post_modified)) {
                            //var_dump($_GET); die();;
                            //$returnarray['ts']=$ts;
                            $ts = strtotime($post->post_modified);
                            $returnarray['changes']=1;
                        }
                        if (!$size) {
                            $size = 'large';
                        }
                        $post_thumbnail_image[0] = $this->nh_getthumblepic($post->ID, $size);

                        $images = $this->nh_getthumblepic_allsize($post->ID);

                        $excerpt = wp_trim_words($post->post_content) ;

                        $posttitle = str_replace(array("\\r","\\n","\r", "\n"), '', trim(html_entity_decode(strip_tags(do_shortcode($post->post_title)), ENT_NOQUOTES, 'UTF-8')));
                        $posttitle = preg_replace_callback("/(&#[0-9]+;)/", function ($m) {
                            return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES");
                        }, $posttitle);

                        $returnarray['items'][] = array('uma'=>'test4','pos'=>$i, 'id'=>$post->ID,'title'=>$posttitle,'timestamp'=>strtotime($post->post_modified),'post_modified'=>($post->post_modified),'post_modified_gmt'=>($post->post_modified_gmt),'type'=>$post->post_type, 'thumb'=> ($post_thumbnail_image[0]), 'images'=>$images, 'publish_timestamp'=> strtotime($post->post_date), 'post_date'=> strtotime($post->post_date), 'post_date_wp'=> ($post->post_date), 'post_type'=> ($post->post_type), 'extraRequestParam'=> '&'.$this->prefix.'post_type='.($post->post_type), 'post_date_gmt'=> ($post->post_date_gmt), 'permalink'=>get_permalink($post),  'excerpt'=>html_entity_decode(str_replace('[&hellip;]', '', strip_tags($excerpt))));
                        $i++;
                    }
                    if (!($returnarray['items'])) {
                        $returnarray['error']=$this->nh_ynaa_errorcode(19);
                        $ts = time();
                    }
                    if ($returnarray['changes']==0 && isset($returnarray['items']) && !$id) {
                        unset($returnarray['items']);
                    }
                } else {
                    $returnarray['error']=$this->nh_ynaa_errorcode(16);
                    $ts = time();
                }

                $returnarray['timestamp']=$ts;
                $returnarray = apply_filters('nh_articles_array_one_cat', $returnarray);
            } else {
                $returnarray['error']=$this->nh_ynaa_errorcode(15);
            }
           // var_dump($returnarray);
            $returnarray = apply_filters('nh_articles_array', $returnarray);
            return array('articles'=>$returnarray);
        } // END private function articles()

        /**
         * return related posts
         */
         private function nh_ynaa_related()
         {
             if (isset($_GET[$this->requesvar['id']])) {
                 $post_categories = wp_get_post_categories($_GET[$this->requesvar['id']]);
                //var_dump($post_categories); die();
                $cat = -1;
                 foreach ($post_categories as $c) {
                     $cat = $c;
                     break;
                 }
                 $this->general_settings['theme']=3;
                 if (isset($this->general_settings['relatedCount'])) {
                     $limit=$this->general_settings['relatedCount'];
                 } else {
                     $limit =4;
                 }

                 $this->exclude_posts= array($_GET[$this->requesvar['id']]);
                 return apply_filters('nh_related_array', $this->nh_ynaa_articles($cat, $limit));
             }
         }

        /**
         * Return Aricle Array
         */
        private function nh_ynaa_article()
        {
            do_action('nh_ynaa_article_pre');
            $returnarray['error']=$this->nh_ynaa_errorcode(0);
            require 'include/nh_ynaa_article.php';
            return (array('article'=>$returnarray));
        } // END private function article()


        /**
        * Return $content HTML
        */
        private function nh_ynaa_content()
        {
            $content = '';

            require 'include/nh_ynaa_content.php';
            return $content;
        }

        /**
         * Return Social
        */
        private function nh_ynaa_social()
        {
            $returnarray['error']=$this->nh_ynaa_errorcode(24);
            if ($_GET[$this->requesvar['n']]=='fb') {
                if ($_GET[$this->requesvar['limit']]) {
                    $limit= $_GET[$this->requesvar['limit']];
                } else {
                    $limit=50;
                }
                $fb= $this->nh_ynaa_get_fbcontent($limit);
                if ($fb) {
                    if (isset($fb['error']) && is_array($fb['error'])) {
                        $returnarray['error'] = $fb['error'];
                    } else {
                        $returnarray['error']=$this->nh_ynaa_errorcode(0);
                        $returnarray['fb']=json_decode($fb);
                    }
                }
            }
            $returnarray = apply_filters('nh_social_array', $returnarray);
            return (array('social'=>$returnarray));
        } // END Funktion nh_ynaa_social

        /**
         * Return comments
        */
        private function nh_ynaa_comments()
        {
            $returnarray['error']=$this->nh_ynaa_errorcode(0);
            $returnarray['changes']=1;
            require 'include/nh_ynaa_comments.php';
            return (array('comments'=>$returnarray));
        } // END Funktion nh_ynaa_comments

        /**
         * Return iBEacon Settings
        */
        private function nh_ynaa_ibeacon()
        {

            //$returnarray['error']=$this->nh_ynaa_errorcode(0);
            if (!$this->push_settings['uuid']) {
                $returnarray['error']=$this->nh_ynaa_errorcode(33);
            } else {
                $returnarray['uuid']=$this->push_settings['uuid'];
                if ($this->push_settings['welcome']) {
                    $returnarray['welcome']=$this->push_settings['welcome'];
                }
                if ($this->push_settings['silent']) {
                    $returnarray['silent']=$this->push_settings['silent'];
                }
                /*$returnarray['uuid'] ='B9407F30-F5F8-466E-AFF9-25556B57FE6D' ;
                $returnarray['silent'] =60 ;*/
                $returnarray['identifier'] ='Beacon1' ;
                //$returnarray['welcome'] ='Willkommen bei der Frankfurter Buchmesse.' ;
                $returnarray['content'][] =array('major'=>50658, 'minor'=>42436, 'silentInterval'=>60, 'proximity'=>'CLProximityNear', 'message'=>'Willkommen bei Oettinger.', 'contentArray'=>array(7,44 )) ;
                $returnarray['content'][] =array('major'=>20535, 'minor'=>33212, 'silentInterval'=>60, 'proximity'=>'CLProximityNear', 'message'=>'Willkommen bei Dressler.', 'contentArray'=>array(7,37)) ;
            }
            return (array('ibeacon'=>$returnarray));
        }
        // END private function nh_ynaa_ibeacon

        /**
         * Return Locations
        */
        private function nh_ynaa_locations($limit=0)
        {
            $returnarray['error']=$this->nh_ynaa_errorcode(0);
            $returnarray['changes']=1;
            $returnarray['ts'] = 0;
            if ($_GET[$this->requesvar['ts']]) {
                $returnarray['ts']=$_GET[$this->requesvar['ts']];
            }

            if (!$this->general_settings['location']) {
                $returnarray['error']=$this->nh_ynaa_errorcode(34);
            } else {
                $lo_args = array(
                    'meta_query' => array(
                            array(
                                'key' => 'nh_ynaa_location_id'/*,
                                'value' => '',
                                'compare' => '!='*/
                            )
                        ),
                    'posts_per_page'=>-1
                );

                $lo_query = new WP_Query($lo_args);
                while ($lo_query->have_posts()) : $lo_query->the_post();
                $id = get_the_ID();
                    //var_dump($lo_query->post);
                    //echo '<hr>';
                    $postmeta = (get_post_meta($id, '_nh_ynaa_location', true));

                if ($postmeta) {
                    $nh_location_update_stamp = (get_post_meta($id, 'nh_location_update_stamp', true));

                    if (strtotime($nh_location_update_stamp)>$returnarray['ts']) {
                        $returnarray['ts'] = strtotime($nh_location_update_stamp);
                    }
                    $postmeta = unserialize($postmeta);
                    $cats = get_the_category();
                        //var_dump($cats);
                        if ($cats) {
                            $cat_id = $cats[0]->term_id;
                            if (!$postmeta['location_pintype']) {
                                $postmeta['location_pintype']='red';
                            }
                            $returnarray['items'][]=
                                array("title"=>$postmeta['location_name'],
                                "lat"=>$postmeta['location_latitude'],
                                "lng"=>$postmeta['location_longitude'],
                                "address"=>$postmeta['location_address'],
                                'pintype'=>$postmeta['location_pintype'],
                                "id"=> $id,
                                'posts'=>array(array('post_id'=>$id, 'type'=>$lo_query->post->post_type, 'cat_id'=>$cat_id))

                            );
                        }
                        //var_dump($returnarray['items']);
                        //echo '<hr>';

                        if ($limit==1) {
                            break;
                        }
                }

                endwhile;
                wp_reset_postdata();
                //var_dump($lo_query);

                $homepresets = $this->nh_ynaa_homepresets();
                //var_dump($homepresets);

                $returnarray['img'] = '';
                $returnarray['title'] = __('Map', 'nh-ynaa');
                if ($homepresets ["homepresets"]['items']) {
                    foreach ($homepresets ["homepresets"]['items'] as $item) {
                        if ($item['cat_id'] != -98) {
                            continue;
                        } else {
                            if ($item['img']) {
                                $returnarray['img'] = $item['img'];
                            }
                            $returnarray['title'] = $item['title'];
                            break;
                        }
                    }
                }
            }
            $returnarray = apply_filters('nh_locations_array', $returnarray);
            return (array('locations'=>$returnarray));
        }
        // END private function nh_ynaa_ibeacon

        /**
        * Return iframe with Emulator
        */
        private function nh_ynaa_simulator()
        {
            ?>
      <div id="nh-simulator">
        <!--<h3><?php _e('Preview your app in the simulator', 'nh-ynaa'); ?></h3>-->
        <h3><a href="#" class="contextual-help-link-a"><?php _e('Preview it on your own device', 'nh-ynaa'); ?></a></h3>
		<!--<div><a href="#" class="contextual-help-link-a"><?php _e('You can also try it on your own device', 'nh-ynaa'); ?></a></div>-->

        <div style="margin-top: 10px;">
        </div>
       </div>
		  <?php

        }// END private function nh_ynaa_simulator


        /**
         * Return  Settings for YNA Admin page
        */
        private function nh_ynaa_yna_settings()
        {
            $returnarray['error']=$this->nh_ynaa_errorcode(0);
            $returnarray['bloginfo']['name']=get_bloginfo('name');
            $returnarray['bloginfo']['language']=get_bloginfo('language');
            return (array('yna_settings'=>$returnarray));
        }
        // END private function nh_ynaa_yna_settings



        /**
         * Return Event Array
         */
        private function nh_ynaa_events($lim=0)
        {
            $weekdays = array(__('Sunday'), __('Monday'), __('Tuesday'), __('Wednesday'), __('Thursday'), __('Friday'), __('Saturday'));
                //WP Query
                global $wpdb;
            $table_em_events = $wpdb->prefix . "em_events";
            $table_em_locations = $wpdb->prefix . "em_locations";
            if ($lim) {
                $limit = " LIMIT $lim ";
                $limit2 = $lim;
            } elseif ($_GET[$this->requesvar['limit']]) {
                $limit = " LIMIT ".$_GET[$this->requesvar['limit']]." ";
                $limit2 = $_GET[$this->requesvar['limit']];
            } else {
                $limit = " LIMIT 9999 ";
            }

            $returnarray['changes']=0;
                //PostID
                //If Post ID Check if is ist the newest Post and if hat changes
                if (isset($_GET[$this->requesvar['post_id']]) && isset($_GET[$this->requesvar['post_ts']])) {
                    $break = false;
                    $latest_cat_post = new WP_Query(array('posts_per_page' => 1, 'post_type' => 'event'));
                    //var_dump($latest_cat_post);
                    if ($latest_cat_post->have_posts()) : while ($latest_cat_post->have_posts()) : $latest_cat_post->the_post();
                    if ($latest_cat_post->post->ID == $_GET[$this->requesvar['post_id']]) {
                        $break = true;
                        $i = 1;


                        if (strtotime($latest_cat_post->post->post_modified)>$_GET[$this->requesvar['post_ts']]) {
                            $ts = strtotime($latest_cat_post->post->post_modified);
                            $returnarray['changes']=1;

                            if (has_post_thumbnail()) {
                                $post_thumbnail_image=wp_get_attachment_image_src(get_post_thumbnail_id($latest_cat_post->post->ID), 'original');
                            } else {
                                $post_thumbnail_image="";
                            }
                            $event = $wpdb->get_row($wpdb->prepare("
									select event_id, e.post_id, event_slug, event_status, event_name, event_start_time, event_end_time, event_all_day, event_start_date, event_end_date, e.post_content, e.location_id,event_category_id, event_attributes, event_date_modified,
									l.location_name, l.location_address, l.location_town, l.location_state, l.location_postcode, l.location_region, l.location_country, l.location_latitude, l.location_longitude
									from $table_em_events e
									left join $table_em_locations l on l.location_id=e.location_id
									WHERE e.post_id=".$latest_cat_post->post->ID."
									", array('%d', '$d', '%s', '%d', '%s', '%s', '%s', '%d','%d', '%d', '%s', '%d', '%d', '%d', '%s')));
                            if ($event) {
                                //$returnarray['uma']['start_ts_gmt']=get_gmt_from_date($event->event_start_date.' '.$event->event_start_time);
                                    $start_ts = strtotime($event->event_start_date.' '.$event->event_start_time);
                                $end_ts = strtotime($event->event_end_date.' '.$event->event_end_time);
                                if (!$event->location_latitude || $event->location_latitude== null || $event->location_latitude=='null' || $event->location_latitude=='0.000000') {
                                    $event->location_latitude =  0;
                                } else {
                                    $event->location_latitude = (float) $event->location_latitude ;
                                }
                                if (!$event->location_longitude || $event->location_longitude== null || $event->location_longitude=='null' || $event->location_longitude=='0.000000') {
                                    $event->location_longitude =  0;
                                } else {
                                    $event->location_longitude = (float) $event->location_longitude ;
                                }

                                $latest_cat_post->post->post_title = str_replace(array("\\r","\\n","\r", "\n"), '', trim(html_entity_decode(strip_tags(do_shortcode($latest_cat_post->post->post_title)), ENT_NOQUOTES, 'UTF-8')));
                                $returnarray['items'][] = array(
                                        'uma'=>array('start_ts_gmt',get_gmt_from_date($event->event_start_date.' '.$event->event_start_time), 'test'=>1),
                                        'pos'=>$i,
                                        'id'=>$latest_cat_post->post->ID,
                                        'title'=>preg_replace_callback("/(&#[0-9]+;)/", function ($m) {
                                            return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES");
                                        }, $latest_cat_post->post->post_title),
                                        'timestamp'=>strtotime($latest_cat_post->post->post_modified),
                                        'post_date'=>strtotime($latest_cat_post->post->post_date),
                                        'type'=>$latest_cat_post->post->post_type,
                                        'extraRequestParam'=>'&'.$this->prefix.'post_type='.$latest_cat_post->post->post_type,
                                        'thumb'=> ($post_thumbnail_image[0]),
                                        'publish_timestamp'=> strtotime($latest_cat_post->post->post_date),
                                        'event_id'=>$event->event_id,
                                        'subtitle' => '',
                                        'start_date' => $event->event_start_date,
                                        'end_date' => $event->event_end_date,
                                        'start_time' => $event->event_start_time,
                                        'end_time' => $event->event_end_time,
                                        'start_ts' => $start_ts,
                                        'end_ts' => $end_ts,
                                        'day' =>  $event->event_all_day,
                                        'swd' => $weekdays[date('w', $start_ts)],
                                        'ewd' => $weekdays[date('w', $end_ts)],
                                        //$returnarray['start_time'] .= (__(' Uhr'));
                                        //$returnarray['end_time'] .= (__(' Uhr'));
                                        //'thumb' => $post_thumbnail_image[0],
                                        'img' => $post_thumbnail_image[0],
                                        'location' => $event->location_name,
                                        'plz' => $event->location_postcode,
                                        'city' => $event->location_town,
                                        'country' => $event->location_country,
                                        'zip' => $event->location_postcode,
                                        'address' => $event->location_address,
                                        'street' => $event->location_address,
                                        'region' => $event->location_region,
                                        'province' => $event->location_region,
                                        'extra' => '',
                                        'lat' => $event->location_latitude,
                                        'lng' => $event->location_longitude,
                                        'short_text' => strip_tags($latest_cat_post->post->post_excerpt),
                                        'sharelink'=> esc_url(get_permalink($latest_cat_post->post->ID))


                                     );
                            } else {
                                $break = true;
                                $returnarray['error']=$this->nh_ynaa_errorcode(22);
                                $ts = time();
                                $returnarray['items'][] = array();
                            }
                        } else {
                            $ts = $_GET[$this->requesvar['post_ts']];
                        }
                    } else {
                        break;
                    }

                    endwhile; else:
                        $break = true;
                    $returnarray['error']=$this->nh_ynaa_errorcode(22);
                    $ts = time();
                    $returnarray['items'][] = array();
                    endif;
                    if ($break) {
                        $returnarray['timestamp']=$ts;
                        return array('events'=>$returnarray);
                    }
                }

                //Timestamp
                if ($_GET[$this->requesvar['ts']]) {
                    $ts= $_GET[$this->requesvar['ts']];
                    $ts_string = date('Y-m-d H:i:s', $ts);
                } else {
                    $ts = 0;
                    $ts_string = date('0000-00-00 00:00:00');
                }



                //Order by post_date
                $events = $wpdb->get_results($wpdb->prepare("
							select event_id, e.post_id, event_slug, event_status, event_name, event_start_time, event_end_time, event_all_day, event_start_date, event_end_date, e.post_content, e.location_id,event_category_id, event_attributes, event_date_modified,
							l.location_name, l.location_address, l.location_town, l.location_state, l.location_postcode, l.location_region, l.location_country, l.location_latitude, l.location_longitude
							from $table_em_events e
							left join $table_em_locations l on l.location_id=e.location_id
							WHERE e.event_status=1 AND e.recurrence=0 AND (e.event_start_date >='".date('Y-m-d')."' OR e.event_end_date>='".date('Y-m-d')."')
							AND e.post_id  NOT IN (SELECT post_id from {$wpdb->postmeta} WHERE meta_key='_nh_ynaa_hide_in_app' AND meta_value=1)
							ORDER BY e.event_start_date, e.event_start_time
							$limit", array('%d', '$d', '%s', '%d', '%s', '%s', '%s', '%d','%d', '%d', '%s', '%d', '%d', '%d', '%s')));

            $i=1;
            if ($events) {
                foreach ($events as $event) {
                    if (isset($limit2) && count($returnarray['items'])>=$limit2) {
                        break;
                    }
                        //$postmeta = unserialize(get_post_meta( $event->post_id, '_nh_ynaa_meta_keys', true ));
                        //if($postmeta  && $postmeta['s']!='on') continue;
                        $post = get_post($event->post_id);
                    if ($ts < strtotime($post->post_modified)) {
                        $ts = strtotime($post->post_modified);
                        $returnarray['changes']=1;
                    }
                    if (has_post_thumbnail($post->ID)) {
                        $post_thumbnail_image=wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'medium');
                        $post_thumbnail_image_full=wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'full');
                    } else {
                        $post_thumbnail_image[0] = '';
                        $post_thumbnail_image_full[0] = '';
                    }
                    $start_ts = strtotime($event->event_start_date.' '.$event->event_start_time);
                    $end_ts = strtotime($event->event_end_date.' '.$event->event_end_time);
                    if (!$event->location_latitude || $event->location_latitude== null || $event->location_latitude=='null' || $event->location_latitude=='0.000000') {
                        $event->location_latitude =  0;
                    } else {
                        $event->location_latitude = (float) $event->location_latitude ;
                    }
                    if (!$event->location_longitude || $event->location_longitude== null || $event->location_longitude=='null' || $event->location_longitude=='0.000000') {
                        $event->location_longitude =  0;
                    } else {
                        $event->location_longitude = (float) $event->location_longitude ;
                    }
                    $post->post_title = str_replace(array("\\r","\\n","\r", "\n"), '', trim(html_entity_decode(strip_tags(do_shortcode($post->post_title)), ENT_NOQUOTES, 'UTF-8')));
                    $returnarray['items'][] = array(
                            'uma'=>array('start_ts_gmt',get_gmt_from_date($event->event_start_date.' '.$event->event_start_time), 'test'=>2),
                            'pos'=>$i,
                            'id'=>$post->ID,
                            'title'=>preg_replace_callback("/(&#[0-9]+;)/", function ($m) {
                                return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES");
                            }, $post->post_title),
                            'timestamp'=>strtotime($post->post_modified),
                            'post_date'=>strtotime($post->post_date),
                            'type'=>$post->post_type,
                            'extraRequestParam'=>'&'.$this->prefix.'post_type='.$post->post_type,
                            'thumb'=> ($post_thumbnail_image[0]),
                            'publish_timestamp'=> strtotime($post->post_date),
                            'event_id'=>$event->event_id,
                            'subtitle' => '',
                            'start_date' => date('d.m.Y', $start_ts),
                            'end_date' => date('d.m.Y', $end_ts),
                            'start_time' => date('H:i', $start_ts),
                            'end_time' => date('H:i', $end_ts),
                            'start_ts' => $start_ts,
                            'end_ts' => $end_ts,
                            'day' =>  $event->event_all_day,
                            'swd' => $weekdays[date('w', $start_ts)],
                            'ewd' => $weekdays[date('w', $end_ts)],
                            //$returnarray['start_time'] .= (__(' Uhr'));
                            //$returnarray['end_time'] .= (__(' Uhr'));
                            //'thumb' => $post_thumbnail_image[0],
                            'img' => $post_thumbnail_image_full[0],
                            'location' => $event->location_name,
                            'town' => $event->location_town,
                            'city' => $event->location_town,
                            'country' => $event->location_country,
                            'zip' => $event->location_postcode,
                            'plz' => $event->location_postcode,
                            'address' => $event->location_address,
                            'street' => $event->location_address,
                            'region' => $event->location_region,
                            'province' => $event->location_region,
                            'extra' => '',
                            'lat' => $event->location_latitude,
                            'lng' => $event->location_longitude,
                            'short_text' => htmlspecialchars_decode(strip_tags($post->post_excerpt)),
                            'sharelink'=> esc_url(get_permalink($post->ID))
                       );
                    $i++;
                }
            }
            else {
                $returnarray['error']=$this->nh_ynaa_errorcode(22);
                $ts = time();
            }


            $returnarray['timestamp']=$ts;
            return array('events'=>$returnarray);
        } // END private function nh_ynaa_events()

        /*
         * Function to get event details
        */
        private function nh_ynaa_event()
        {
            $returnarray['error']=$this->nh_ynaa_errorcode(0);

            require 'include/nh_ynaa_event.php';
            return array('event'=>$returnarray);
        }// End private function nh_ynaa_event

        /*
         * Function to get search
        */
        private function nh_ynaa_search()
        {
            $returnarray['error']=$this->nh_ynaa_errorcode(0);

            require 'include/nh_ynaa_search.php';
            //if(isset($returnarray['articles']) && false) return array($returnarray);
            //else
            //var_dump($returnarray);
            //if(isset($returnarray->articles)) return ($returnarray);
            if (is_array($returnarray) && ($returnarray['request']=='articles')) {
                //	unset($returnarray['request']);
                     return array('articles'=>$returnarray);
            } else {
                //unset($returnarray['request']);
                return array('items'=>$returnarray);
            }
        }// End private function nh_ynaa_event



        /*
         * Function to get event details
        */
        private function nh_ynaa_lang()
        {
            $returnarray = array();

            return array('lang'=>$returnarray);
        }// END private function nh_ynaa_lang


        /**
        Function to prepare Content for App
            return Formatet HTML
        */
        private function nh_ynaa_get_appcontent($html, $post = null)
        {
            //echo $html;
            require 'include/nh_ynaa_get_appcontent.php';
            return ($html);
        }//END private function nh_ynaa_get_appcontent

        /**
         *Function to get Facebook content
        */
        private function nh_ynaa_get_fbcontent($limit=50)
        {
            //echo 'fb';
            if (!$_GET[$this->requesvar['meta']] && (isset($this->general_settings['social_fbid'], $this->general_settings['social_fbsecretid'], $this->general_settings['social_fbappid']) && ($this->general_settings['social_fbid'] != '' && $this->general_settings['social_fbsecretid'] != '' && $this->general_settings['social_fbappid'] != ''))) {
                //echo 'fb3';
                if (!class_exists('Facebook')) {
                    include_once('facebook-php-sdk-master/src/facebook.php');
                }
                if (class_exists('Facebook')) {
                    //echo 'fb4';
                    $config = array(
                      'appId' => $this->general_settings['social_fbappid'],
                      'secret' => $this->general_settings['social_fbsecretid'],
                      'fileUpload' => false // optional
                    );
                    $facebook = new Facebook($config);
                    $access_token = $facebook->getAccessToken();
                    if ($access_token) {
                        $limit2 =$limit;
                        if ($limit==1) {
                            $limit2 =50;
                        }
                        $url = 'https://graph.facebook.com/'.$this->general_settings['social_fbid'].'/feed?access_token='.$access_token.'&format=json&type=post&limit='.$limit2.'&fields=id,full_picture,name,message,created_time,picture,story,type';
                        //echo $url; //debug
                        //$url = 'https://graph.facebook.com/'.$this->general_settings['social_fbid'].'/feed?access_token='.$access_token.'&format=json&type=post&fields=full_picture,message,story,picture,location&limit='.$limit2;

                        $items = $this->nh_ynaa_get_data($url, $limit);
                        if ($items) {
                            /*
                            $temp = json_decode($items);
                            //print_r($temp->data[0]->id);
                            //print_r($items);
                            foreach($temp->data as $k=>$v){
                                $picture = $this->nh_ynaa_get_data('https://graph.facebook.com/v2.0/'.$temp->data[$k]->id.'?fields=full_picture&access_token='.$access_token,1);
                                //print_r (json_decode($picture));

                                if($picture) $temp->data[$k]->picture = json_decode($picture)->full_picture;
                            }
                            $items = json_encode($temp);*/
                            //print_r(($this->nh_ynaa_get_data('https://graph.facebook.com/v2.0/'.$temp->data[0]->id.'?fields=full_picture&access_token='.$access_token,1)));
                            //	die();
                                $returnarray=$items;
                        } else {
                            $returnarray['error']=$this->nh_ynaa_errorcode(28);
                        }
                    } else {
                        $returnarray['error']=$this->nh_ynaa_errorcode(27);
                    }
                } else {
                    $returnarray['error']=$this->nh_ynaa_errorcode(26);
                    //echo 'fb5';
                }
            } else {
                $returnarray['error']=$this->nh_ynaa_errorcode(25);
            }
            //echo 'fb2';
            return $returnarray;
        } //END function nh_ynaa_get_fbcontent

        /**
         * Function get extra data from blappsta.com
         * set wp option variable
         */
         private function nh_get_blappsta_extra()
         {
             $ts =  get_option('nh_ynaa_blappsta_ts');
             if (!$ts || date('Ymd', $ts)<date('Ymd') || $_GET['update_bas']=='true') {
                 //var_dump(get_bloginfo('url'));
                $content = '';
                 if (ini_get('allow_url_fopen')) {
                     $content = @file_get_contents('http://www.blappsta.com?bas=extra_infos&url='.urlencode(get_bloginfo('url')));
                     if ($content) {
                         $json=json_decode($content, true);
                         update_option('nh_ynaa_blappsta', $json);
                         update_option('nh_ynaa_blappsta_ts', time());
                     }
                 }
             }
         }// END private function nh_get_blappsta_extra

        /*
            *gets the data from a URL
            * @$url String url
            * return strin content
        */
        public function nh_ynaa_get_data($url, $limit, $offset=0)
        {
            $data = false;
            $items = false;
            //$url = $url.'&offset='.$offset;
            if (ini_get('allow_url_fopen')) {
                $items = file_get_contents($url);
            }
            if ($items) {
                $data=$items;
            } else {
                if (function_exists('curl_version')) {
                    $ch = curl_init();
                    $timeout = 25;
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
                    $items = curl_exec($ch);
                    curl_close($ch);
                    if ($items) {
                        $data=$items;
                    }
                }
            }
            if ($limit==1 && $data) {
                $datatemp = json_decode($data, true);
                //var_dump($datatemp);
                foreach ($datatemp['data'] as $temp) {
                    if ($temp['message']) {
                        $data = (json_encode(array('data'=>array($temp),'paging'=>$datatemp['paging'])));

                        //var_dump($temp);

                        break;
                    }
                }
                /*while($datatemp['data'][0]['message'] ){
                    $data = $this->nh_ynaa_get_data($url,1,$offset++);
                }*/
            }
            return $data;
        }

        //UPDATE Option timestamps
        public function nh_update_option_ynaa_general_settings($old, $new)
        {
            $ts = $new['ts'];
            update_option('nh_ynaa_general_settings_ts', $ts);
        }
        public function nh_update_option_ynaa_menu_settings($old, $new)
        {
            $ts = $new['ts'];
            update_option('nh_ynaa_menu_settings_ts', $ts);
            update_option('nh_ynaa_general_settings_ts', $ts);
        }
        public function nh_update_option_ynaa_css_settings($old, $new)
        {
            $ts = $new['ts'];
            update_option('nh_ynaa_css_settings_ts', $ts);
            update_option('nh_ynaa_general_settings_ts', $ts);
        }
        public function nh_update_option_ynaa_teaser_settings($old, $new)
        {
            $ts = $new['ts'];
            update_option('nh_ynaa_teaser_settings_ts', $ts);
        }
        public function nh_update_option_ynaa_homepreset_settings($old, $new)
        {
            $ts = $new['ts'];
            update_option('nh_ynaa_homepreset_settings_ts', $ts);
            update_option('nh_ynaa_general_settings_ts', $ts);
            update_option('nh_ynaa_articles_ts', $ts);
        }
        public function nh_update_option_ynaa_push_settings($old, $new)
        {
            $ts = $new['ts'];
            update_option('nh_ynaa_push_settings_ts', $ts);
            update_option('nh_ynaa_general_settings_ts', $ts);
        }
        public function nh_update_option_ynaa_categories_settings($old, $new)
        {
            $ts = $new['ts'];
            update_option('nh_ynaa_categories_settings_ts', $ts);
            update_option('nh_ynaa_general_settings_ts', $ts);
            update_option('nh_ynaa_homepreset_settings_ts', $ts);
        }






        /**
         * Adds a box to the main column on the Post and Page edit screens.
         */
        public function nh_ynaa_add_custom_box()
        {
            $post_types = get_post_types();
            foreach ($post_types as $post_type) {
                if (!in_array($post_type, array( 'attachment', 'revision', 'nav_menu_item' ))) {
                    $screens[] = $post_type;
                }
            }

            foreach ($screens as $screen) {
                add_meta_box(
                    'nh_ynaa_sectionid',
                    __('Blappsta Plugin extras', 'nh_ynaa'),
                    array($this,'nh_ynaa_inner_custom_box'),
                    $screen, 'side', 'default'
                );
            }

            if ($generalsettings = get_option($this->general_settings_key)) {
                global $nh_ynaa_db_version;
                if (get_option('nh_ynaa_db_version') == $nh_ynaa_db_version) {
                    if ($generalsettings['location']) {
                        $post_types = get_post_types();
                        foreach ($post_types as $post_type) {
                            if (!in_array($post_type, array( 'attachment', 'revision', 'nav_menu_item', 'events' ))) {
                                $screens[] = $post_type;
                            }
                        }

                        foreach ($screens as $screen) {
                            add_meta_box(
                            'nh_ynaa_locationid',
                            __('Blappsta Plugin locations', 'nh_ynaa'),
                            array($this,'nh_ynaa_inner_location_box'),
                            $screen, 'normal', 'default'
                        );
                        }
                    }
                }
            }
        }

        /**
         * Prints the box content.
         *
         * @param WP_Post $post The object for the current post/page.
         */
        public function nh_ynaa_inner_location_box($post)
        {

          // Add an nonce field so we can check for it later.
          wp_nonce_field('nh_ynaa_inner_location_box', 'nh_ynaa_inner_location_box_nonce');

          /*
           * Use get_post_meta() to retrieve an existing value
           * from the database and use the value for the form.
           */
          $value = unserialize(get_post_meta($post->ID, '_nh_ynaa_location', true));
            $ynaa_location_id = (get_post_meta($post->ID, 'nh_ynaa_location_id', true));
         // var_dump($value);
          $required = ''; ?>
         <div id="nh-location-data" class="nh-location-data">
			<div id="nh_location_coordinates" style=" display:none;">
				<input id="nh_location_latitude" name="nh_location_latitude" type="hidden" value="<?php echo $value['location_latitude']; ?>" size="15" >
				<input id="nh_location_longitude" name="nh_location_longitude" type="hidden" value="<?php echo $value['location_longitude']; ?>" size="15" >

			</div>

			<table class="nh-location-data">
				<tbody>
                	<tr class="nh-location-data-name">
                    	<th><?php _e('Location Name', 'nh-ynaa'); ?>:</th>
                        <td><input id="nh_location_id" name="nh_location_id" type="hidden" value="<?php echo $ynaa_location_id; ?>" size="15"><input type="hidden" value="0" name="nh_location_del" id="nh_location_del">
                        <input type="hidden" value="0" name="nh_location_name_change" id="nh_location_name_change"><input type="hidden" value="0" name="nh_location_change" id="nh_location_change">
				<span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span><input id="nh_location_name" type="text" name="nh_location_name" value="<?php echo esc_attr(stripslashes($value['location_name'])); ?>" class="ui-autocomplete-input"><?php echo $required; ?>
				<br>
				<em id="nh-location-search-tip" style="display: none;"><?php _e('Create a location or start typing to search a previously created location.', 'nh-ynaa')?></em>
				<em id="nh-location-reset" style="display:none;"><?php _e('You cannot edit saved locations here.', 'nh-ynaa'); ?> <a href="#"><?php _e('Reset this form to create a location or search again.', 'nh-ynaa')?></a></em>
			</td>
 		</tr>
		<tr class="nh-location-data-address">
			<th><?php _e('Address:', 'nh-ynaa')?>&nbsp;</th>
			<td>
				<input id="nh_location_address" type="text" name="nh_location_address" value="<?php echo esc_attr($value['location_address'], ENT_QUOTES);
            ; ?>" class="blurlocation" /><?php echo $required; ?>
			</td>
		</tr>
		<tr class="nh-location-data-town">
			<th><?php _e('City/Town:', 'nh-ynaa')?>&nbsp;</th>
			<td>
				<input id="nh_location_town" type="text" name="nh_location_town" value="<?php echo esc_attr($value['location_town'], ENT_QUOTES); ?>" class="blurlocation" /><?php echo $required; ?>
			</td>
		</tr>
		<tr class="nh-location-data-state">
			<th><?php _e('State/County:', 'nh-ynaa')?>&nbsp;</th>
			<td>
				<input id="nh_location-state" type="text" name="nh_location_state" value="<?php echo esc_attr($value['location_state'], ENT_QUOTES); ?>" class=" blurlocation" />
			</td>
		</tr>
		<tr class="nh-location-data-postcode">
			<th><?php _e('Postcode:', 'nh-ynaa')?>&nbsp;</th>
			<td>
				<input id="nh_location_postcode" type="text" name="nh_location_postcode" value="<?php echo esc_attr($value['location_postcode'], ENT_QUOTES); ?>" class=" blurlocation" />

			</td>
		</tr>
	<!--	<tr class="nh-location-data-region">
			<th><?php _e('Region:', 'nh-ynaa')?>&nbsp;</th>
			<td>
				<input id="nh_location_region" type="text" name="nh_location_region" value="<?php echo esc_attr($value['location_region'], ENT_QUOTES); ?>" class=" blurlocation" />
			</td>
		</tr>
		-->
        <tr>
        	<th><?php _e('Pin color'); ?></th>
            <td><select name="nh_location_pintype" id="nh_location_pintype" class="">
            	<option value="red" <?php if ($value['location_pintype']=='red') {
                echo 'selected';
            } ?>><?php _e('red'); ?></option>
                <option value="green" <?php if ($value['location_pintype']=='green') {
                echo 'selected';
            } ?>><?php _e('green'); ?></option>
                <option value="purple" <?php if ($value['location_pintype']=='purple') {
                echo 'selected';
            } ?>><?php _e('purple'); ?></option>

            </select>
            </td>
        </tr>
        <tr>
        	<th></th>
            <td><a href="#del" id="reset_location"><?php _e('Reset this form to create a location.', 'nh-ynaa'); ?></a></td>
        </tr>
	</tbody>
    </table>

			<div class="nh-location-map-container">
                <div id='nh-map-404'  class="nh-location-map-404" style="display:none;">
                    <p><em><?php _e('Location not found', 'nh-ynaa'); ?></em></p>
                </div>
                <div id='nh-map' class="nhm-location-map-content" style="float:left;">
                <div style="width: 400px" id="googlemapdiv"><?php if ($value['location_latitude'] && $value['location_longitude']) {
                ?><iframe id="googlemapiframe" width="400" height="400" src="http://maps.google.de/maps?hl=de&q=<?php echo urlencode($value['location_address']).','. urlencode($value['location_postcode']).'+'. urlencode($value['location_town']).' ('.urlencode($value['location_name']).')'; ?>&ie=UTF8&t=&iwloc=A&output=embed" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe><?php

            } ?></div>
                </div>
            </div>
			<br style="clear:both;">
		</div>
         <?php

        }

        /**
         * Prints the box content.
         *
         * @param WP_Post $post The object for the current post/page.
         */
        public function nh_ynaa_inner_custom_box($post)
        {

          // Add an nonce field so we can check for it later.
          wp_nonce_field('nh_ynaa_inner_custom_box', 'nh_ynaa_inner_custom_box_nonce');

          /*
           * Use get_post_meta() to retrieve an existing value
           * from the database and use the value for the form.
           */
          $value = unserialize(get_post_meta($post->ID, '_nh_ynaa_meta_keys', true));
            $nh_ynaa_hinde_in_app = (get_post_meta($post->ID, '_nh_ynaa_hide_in_app', true));
            //var_dump($value, $post->post_type, $nh_ynaa_hinde_in_app);
            echo '<div><label  style="padding-bottom: 0;" for="nh_ynaa_hide_in_app">';
            echo '<input value="1" type="checkbox" id="nh_ynaa_hide_in_app" name="nh_ynaa_hide_in_app" ';
            if ($nh_ynaa_hinde_in_app) {
                echo ' checked="checked" ';
            }
            echo ' />&nbsp;&nbsp;';
            _e("Hide in app", 'nh-ynaa');
          /*echo '<div><label  style="padding-bottom: 0;" for="nh_ynaa_visible_app">';
            echo '<input type="checkbox" id="nh_ynaa_visible_app" name="nh_ynaa_visible_app" ';
            if($value) {
                if($value['s']) echo ' checked="checked" ';
            }
            else echo ' checked="checked" ';
            echo ' />&nbsp;&nbsp;';
            _e( "Show in App", 'nh-ynaa' );
        */
          if ($generalsettings = get_option($this->general_settings_key)) {
              if ((!isset($generalsettings['textToSpeech']) && $post->post_type='post') || in_array($post->post_type, $generalsettings['textToSpeech'])) {
                  echo '</label>';
                  echo '<label style="padding-bottom: 0;"><input value="1" type="checkbox" id="nh_ynaa_no_read" name="nh_ynaa_no_read" ';
                  if (!empty($value['noread'])) {
                      echo ' checked="checked" ';
                  }
                  echo ' />&nbsp;&nbsp;';
                  _e("Disable Text-to-Speech", 'nh-ynaa');
              }
          }
            echo '</label></div> ';

            echo '<hr style="margin-top: 20px;">';
            // Sanitize user input.
            if ($pushsettings = get_option($this->push_settings_key)) {
                if ($pushsettings['pushshow']) {
                    echo '<div><label for="nh_ynaa_pushtext" style="padding-bottom: 0; width: 98%;">';
                    _e("Push Text", 'nh-ynaa');

                    echo '<br /><textarea style="width:100%" id="nh_ynaa_pushtext" name="nh_ynaa_pushtext" maxlength="120">'.$post->post_title.'</textarea>';


                    echo '</label></div> ';

                    echo '<div><label for="nh_ynaa_sendpush" style="padding-bottom: 0;">';
                    echo '<input type="button" value="'.__('Send Push', 'nh-ynaa').'" id="nh_ynaa_sendpush" />';
                    echo '</label><span id="nh-push-dialog" title="Push" style="line-height:30px; vertical-align:text-top;"><span style="display:none;">'.__('Please wait...', 'nh-ynaa').'</span></span></div> ';
                } else {
                    _e('Activate under <a href="/wp-admin/options-general.php?page=nh_ynaa_plugin_options&tab=nh_ynaa_push_settings" target="_blank">Settings->Blappsta plugin->Push</a> "Show Push Metabox" for send push messages.', 'nh-ynaa');
                }
            } else {
                _e('Push is off. Please, supply the Push Keys on <a href="/wp-admin/options-general.php?page=nh_ynaa_plugin_options&tab=nh_ynaa_push_settings" target="_blank">Settings->Blappsta plugin->Push</a> page.', 'nh-ynaa');
            }
        }

        /**
         * When the post is saved, saves our custom data.
         *
         * @param int $post_id The ID of the post being saved.
         */
        public function nh_ynaa_save_postdata($post_id)
        {
            update_option('nh_ynaa_categories_settings_ts', time());
            update_option('nh_ynaa_teaser_settings_ts', time());

          /*
           * We need to verify this came from the our screen and with proper authorization,
           * because save_post can be triggered at other times.
           */
          if ($pushsettings = get_option($this->push_settings_key)) {
              if ($pushsettings['pushshow']) {
                  // Check if our nonce is set.
                  if (! isset($_POST['nh_ynaa_inner_custom_box_nonce'])) {
                      return $post_id;
                  }

                  $nonce = $_POST['nh_ynaa_inner_custom_box_nonce'];

                  // Verify that the nonce is valid.
                  if (! wp_verify_nonce($nonce, 'nh_ynaa_inner_custom_box')) {
                      return $post_id;
                  }
              }
          }

            if ($generalsettings = get_option($this->general_settings_key)) {
                if ($generalsettings['location']) {
                    // Check if our nonce is set.
                  if (! isset($_POST['nh_ynaa_inner_location_box_nonce'])) {
                      return $post_id;
                  }

                    $nonce = $_POST['nh_ynaa_inner_location_box_nonce'];

                  // Verify that the nonce is valid.
                  if (! wp_verify_nonce($nonce, 'nh_ynaa_inner_location_box')) {
                      return $post_id;
                  }
                }
            }

          // If this is an autosave, our form has not been submitted, so we don't want to do anything.
          if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
              return $post_id;
          }

          // Check the user's permissions.
          if ('page' == $_POST['post_type']) {
              if (! current_user_can('edit_page', $post_id)) {
                  return $post_id;
              }
          } else {
              if (! current_user_can('edit_post', $post_id)) {
                  return $post_id;
              }
          }

          /* OK, its safe for us to save the data now. */
            if (isset($_POST['nh_ynaa_no_read'])) {
                $appdata['noread'] = 1;
            } else {
                $appdata['noread'] = 0;
            }
            //if(isset($_POST['nh_ynaa_visible_app']))$appdata['s'] = ($_POST['nh_ynaa_visible_app'] );
            //else $appdata['s']=0;
            update_post_meta($post_id, '_nh_ynaa_meta_keys', serialize($appdata));
            if (isset($_POST['nh_ynaa_hide_in_app'])) {
                update_post_meta($post_id, '_nh_ynaa_hide_in_app', 1);
            } else {
                update_post_meta($post_id, '_nh_ynaa_hide_in_app', 0);
            }




            if ($generalsettings = get_option($this->general_settings_key)) {
                //update_post_meta( $post_id, '_nh_ynaa_location_name', $_POST['nh_ynaa_visible_app'].'123'. $_POST['nh_location_name'] );
               if ($_POST['nh_location_del'] && !$_POST['nh_location_change']) {
                   if ($_POST['nh_location_id']) {
                       global $wpdb;
                       global $blog_id;
                       $table_name = $wpdb->prefix ."nh_locations";
                       $wpdb->update($table_name, array('location_status'=>0, 'location_update_stamp'=>date('Y-m-d H:i:s')), array( 'location_id' => $_POST['nh_location_id'] ), array('%d'), array('%d'));
                       delete_post_meta($post_id, 'nh_ynaa_location_id');
                       delete_post_meta($post_id, '_nh_ynaa_location');
                   }
               } elseif (isset($generalsettings['location']) && $generalsettings['location']) {
                   if ((!isset($_POST['nh_location_change']) || !$_POST['nh_location_change'])) {
                       return $post_id;
                   }
                    //if((!isset($_POST['nh_location_change']) || !$_POST['nh_location_change']) &&(!isset($_POST['nh_location_address']) || $_POST['nh_location_address']=='') && (!isset($_POST['nh_location_address']) || $_POST['nh_location_address'] = '') && (!isset($_POST['nh_location_name'])|| $_POST['nh_location_name']=='') && (!isset($_POST['nh_location_postcode'])|| $_POST['nh_location_postcode']=='')) return $post_id;
                    global $wpdb;
                   global $blog_id;
                   $table_name = $wpdb->prefix ."nh_locations";
                    // update_post_meta( $post_id, '_nh_ynaa_location_name',$table_name.'123'. $_POST['nh_location_name'] );

                    $adresse = '';
                   $data['location_address'] = '';
                   $format[] = '%s';
                   if ($_POST['nh_location_address']) {
                       $data['location_address'] = $_POST['nh_location_address'];
                       $adresse .= $data['location_address'].',';
                   }


                   $data['location_town'] = '';
                   $format[] = '%s';
                   if ($_POST['nh_location_town']) {
                       $data['location_town'] = $_POST['nh_location_town'];
                       $adresse .= $data['location_town'].',';
                   }

                   $data['location_state'] = '';
                   $format[] = '%s';
                   if ($_POST['nh_location_state']) {
                       $data['location_state'] = $_POST['nh_location_state'];
                       $adresse .= $data['location_state'].',';
                   }

                   $data['location_postcode'] = '';
                   $format[] = '%s';
                   if ($_POST['nh_location_postcode']) {
                       $data['location_postcode'] = $_POST['nh_location_postcode'];
                       $adresse .= $data['location_postcode'].',';
                   }

                   $data['location_region'] = '';
                   $format[] = '%s';
                   if ($_POST['nh_location_region']) {
                       $data['location_region'] = $_POST['nh_location_region'];
                       $adresse .= $data['location_region'].',';
                   }
/*
                    $data['location_country'] = 'DE';
                    $format[] = '%s';
                    $adresse .= $data['location_country'];
*/
                    $data['location_update_stamp'] = date('Y-m-d H:i:s');
                   $format[] = '%s';


                   if ($_POST['nh_location_change']) {
                       $cord = $this->getLatLng($adresse);
                       if ($cord && is_array($cord)) {
                           $data['location_latitude'] = $cord['lat'];
                           $format[] = '%s';
                           $data['location_longitude'] = $cord['lng'];
                           $format[] = '%s';
                        /*
                            $data['post_content'] = serialize($cord);
                            $format[] = '%s';*/
                       }
                   }
                   $data['location_name']= ($_POST['nh_location_name']);
                   $format[] = '%s';
                   $data['location_slug']= sanitize_title_with_dashes($_POST['nh_location_name']);
                   $format[] = '%s';
                   $data['blog_id'] = $blog_id;
                   $format[]='%d';
                   $data['post_id']=$post_id;
                   $format[]= '%d';
                   $data['location_owner'] = get_current_user_id();
                   $format[]= '%d';
                   $data['location_pintype'] = 'red';
                   $format[] = '%s';
                   if ($_POST['nh_location_pintype']) {
                       $data['location_pintype'] = $_POST['nh_location_pintype'];
                   }


                   $data['location_status'] =1;
                   $format[] = '%d';

                   if ($_POST['nh_location_id']) {
                       if ($_POST['nh_location_change']) {
                           $wpdb->update($table_name, $data, array( 'location_id' =>$_POST['nh_location_id'] ), $format, array('%d'));
                           update_post_meta($post_id, '_nh_ynaa_location', mysql_real_escape_string(serialize($data)));
                           update_post_meta($post_id, 'nh_location_update_stamp', $data['location_update_stamp']);
                       }
                        //elseif($_POST['nh_location_change']){
                        //	$wpdb->update($table_name,$data,array( 'location_id' => 1 ),$format, array('%d') );
                        //	update_post_meta( $post_id, '_nh_ynaa_location', serialize($data));
                        //}
                   } else {
                       $wpdb->insert($table_name, $data, $format);
                       $data['id'] = $wpdb->insert_id;
                       add_post_meta($post_id, 'nh_ynaa_location_id', $data['id']);
                       add_post_meta($post_id, 'nh_location_update_stamp', $data['location_update_stamp']);
                       add_post_meta($post_id, '_nh_ynaa_location', mysql_real_escape_string(serialize($data)));
                   }
               }
            }
        }


        /**
         * Function tu category timestamp on change categries
         */
        public function nh_ynaa_edit_category($catid)
        {
            update_option('nh_ynaa_categories_settings_ts', time());
        }
        /**
        *Teaser set img post ID etc
        */
        public function ny_ynaa_teaser_action($tpid=0, $type= '')
        {
            if ($tpid) {
                $_POST['tpid']= $tpid;
            }
            if ($type) {
                $_POST['type']= $type;
            }
            $result['type'] = "success";
            if (!empty($_POST['appextra'])) {
                $result['error'] = 0;
                $result['tpid']= $_POST['tpid'];
                $result['appextra']= $_POST['appextra'];
                $result['img']='';
                $result['type'] = $_POST['type'];
                $result['title'] = ($this->appmenus_pre[$_POST['appextra']]['title']);
                if (isset($this->appmenus_pre[$_POST['appextra']]['extrafields'])) {
                    foreach ($this->appmenus_pre[$_POST['appextra']]['extrafields'] as $k=>$ar) {
                        $result['extrafield'][$k] = '<input type="'.$this->appmenus_pre[$_POST['appextra']]['extrafields'][$_POST['type']]['type'].'" name="" value="" placeholder="" style="">';
                    }
                }
            } elseif ($_POST['tpid'] && $_POST['type']=='cat') {
                $result['tpid']= $_POST['tpid'];
                $category = $this->nh_get_category($_POST['tpid']);

                if ($category) {
                    //$result['uma']['$category']= $category;
                    $result['error'] = 0;
                    $result['type'] = 'cat';
                    $cat = $this->categories_settings[$_POST['tpid']];
                    if ($cat) {
                        if ($cat['hidecat']==1) {
                            $result['error'] = 1;
                        };
                        $result['title'] = $cat['cat_name']  ;
                        if ($cat['usecatimg']==1 && !empty($cat['img'])) {
                            $result['img']= $cat['img']  ;
                        } else {
                            $post = $this->nh_wp_get_recent_posts(1, $_POST['tpid']);
                            //$result['uma']['$post_id']=$post;
                            //$result['uma']['$post[0]']=$post[0];
                            //$result['uma']['$post[0]->ID']=$post[0]->ID;
                            //$result['uma']['$post[0]->[ID]']=$post[0]['ID'];
                            $result['img']= $this->nh_getthumblepic($post[0]['ID'], 'full');
                            ;
                        }
                        $result['type'] = 'cat';
                    } else {
                        $result['title'] = $category->name;
                        $post = $this->nh_wp_get_recent_posts(1, $_POST['tpid']);
                        //$result['uma']['$post_id']=$post;
                        //$result['uma']['$post[0]']=$post[0];
                        //$result['uma']['$post[0]->ID']=$post[0]->ID;
                        //$result['uma']['$post[0]->[ID]']=$post[0]['ID'];
                        $result['img']= $this->nh_getthumblepic($post[0]['ID'], 'full');
                        ;
                    }
                }
            } elseif ($_POST['tpid']) {
                $result['tpid']= $_POST['tpid'];
                $post = get_post($_POST['tpid']);
                if ($post) {
                    $result['error'] = 0;
                }
                $result['title'] = strip_tags(get_the_title($_POST['tpid']));
                $result['img']= $this->nh_getthumblepic($_POST['tpid']);
                $result['type'] = get_post_type($_POST['tpid']);
                //$result['allowremoveText']= __('Allow hide on Startscreen','nh-ynaa');
                //$result['catText']= __('Set default image for category','nh-ynaa');
            } else {
                $result['error'] = __('No ID');
            }

            if ($tpid) {
                return    $result;
            }
            $result = json_encode($result);
            echo $result;
            die();
        }

        public function nh_wp_get_recent_posts($limit = 1, $category = 0, $type='post', $orderby ='post_date', $order = 'DESC')
        {
            $args = array(
                    'numberposts' => $limit,
                     'post_status' => 'publish',
                     'post_type' => $type,
                     'category'=>$category,
                     'orderby' => $orderby,
                    'order' => $order,
                    'offset' => 0
                    );
            $recent_posts = wp_get_recent_posts($args, ARRAY_A);
            return $recent_posts;
        }

        /**
        *Ajax search
        */
        public function nh_search_action()
        {
            global $wpdb; // this is how you get access to the database

            if (trim($_POST['s'])) {
                if ($_POST['pt']) {
                    $post_type = $_POST['pt'];
                } else {
                    $post_type = 'post';
                }
                if ($_POST['mid']) {
                    $menu_id = $_POST['mid'];
                } else {
                    $menu_id = '1';
                }


                $search_query = new WP_Query();
                $results = $search_query->query('s='.trim($_POST['s'].'&post_type='.$post_type));

                if ($results) {
                    foreach ($results as $p) {
                        $temp = "";
                        $shorttitle = $this->shortenText($p->post_title);
                        $temp .= '<li>';
                        $temp .=  '<input type="hidden" value="'.$post_type.'" name="type-menu-item-'.$post_type.$menu_id.'" id="type-menu-item-'.$post_type.$menu_id.'" >';

                        $temp .=  '<input type="hidden" value="html" name="link-typ-menu-item-'.$post_type.$menu_id.'" id="link-type-menu-item-'.$post_type.$menu_id.'">';
                        $temp .=  '<input type="hidden" value="'.$shorttitle.'" name="title-menu-item-'.$post_type.$menu_id.'" id="title-menu-item-'.$post_type.$menu_id.'">';
                        $temp .=  '<label class="menu-item-title">';
                        $temp .=  '<input type="checkbox" value="'.$p->ID.'" name="menu-item-'.$post_type.$menu_id.'" class="menu-item-checkbox" /> ';
                        $temp .=  $shorttitle.'</label>';
                        $temp .=  '</li>';
                        echo $temp;
                        $menu_id++;
                    }
                } else {
                    _e('No posts found.');
                }
            } else {
                _e('Error');
            }

            die(); // this is required to return a proper result
        }

        /**
        * Functin get thumble pic
        */
        public function nh_getthumblepic($id, $size='full')
        {
            //if(isset($_GET['ynaa'])&& ($_GET['ynaa']=='teaser' || $_GET['ynaa']=='nh_teaser' )) $size='full';
            $url ='';
            if ($id) {
                if ($this->general_settings['gadgetry']) {
                    $gadgetry_tfuse_post_options = get_post_meta($id, 'gadgetry_tfuse_post_options', true);
                    if ($this->general_settings['debug'] ==1 && $_GET['debug']==1) {
                        var_dump($gadgetry_tfuse_post_options);
                    }
                    //$gadgetry_tfuse_post_options = unserialize($gadgetry_tfuse_post_options);
                    if (is_array($gadgetry_tfuse_post_options)) {
                        if ($gadgetry_tfuse_post_options['gadgetry_single_image']) {
                            $post_thumbnail_image[0] = $gadgetry_tfuse_post_options['gadgetry_single_image'];
                        } elseif ($gadgetry_tfuse_post_options['gadgetry_thumbnail_image']) {
                            $post_thumbnail_image[0] = $gadgetry_tfuse_post_options['gadgetry_thumbnail_image'];
                        }
                    } else {
                        if (has_post_thumbnail($id)) {
                            $post_thumbnail_image=wp_get_attachment_image_src(get_post_thumbnail_id($id), $size);
                        } else {
                            $post_thumbnail_image[0] = '';
                        }
                    }
                } else {
                    if (has_post_thumbnail($id)) {
                        $post_thumbnail_image=wp_get_attachment_image_src(get_post_thumbnail_id($id), $size);
                    } else {
                        $post_thumbnail_image[0] = '';
                    }
                }
                $url = $post_thumbnail_image[0];
            }
            if ($url && substr($url, 0, 4)!='http') {
                $url = get_bloginfo('url') .$url;
            }
            return ($url);
        }

        /**
        * Functin get thumble pic
        */
        public function nh_getthumblepic_allsize($id)
        {
            $urls =array();
            $sizes =array('o'=>'original', 't'=>'thumbnail', 'm'=>'medium', 'l'=>'large', 'f'=>'full');
            if ($id) {
                if ($this->general_settings['gadgetry']) {
                    $gadgetry_tfuse_post_options = get_post_meta($id, 'gadgetry_tfuse_post_options', true);
                    if (is_array($gadgetry_tfuse_post_options)) {
                        if ($gadgetry_tfuse_post_options['gadgetry_single_image']) {
                            $urls['o'] = $gadgetry_tfuse_post_options['gadgetry_single_image'];
                        }
                        if ($gadgetry_tfuse_post_options['gadgetry_thumbnail_image']) {
                            $urls['t']  = $gadgetry_tfuse_post_options['gadgetry_thumbnail_image'];
                        }
                    } else {
                        if (has_post_thumbnail($id)) {
                            foreach ($sizes as $k => $size) {
                                $urls[$k]=wp_get_attachment_image_src(get_post_thumbnail_id($id), $size);
                                $urls[$k]= $this->nh_relativeToAbsolutePath($urls[$k][0]);
                            }
                        }
                    }
                } else {
                    if (has_post_thumbnail($id)) {
                        foreach ($sizes as $k => $size) {
                            $urls[$k]=wp_get_attachment_image_src(get_post_thumbnail_id($id), $size);
                            $urls[$k]=$this->nh_relativeToAbsolutePath($urls[$k][0]);
                        }
                    }
                }
            }
            return $urls;
        }

        /**
         * change relative path to absolute path
         */
         public function nh_relativeToAbsolutePath($url)
         {
             if ($url && substr($url, 0, 4) != 'http') {
                 $url = get_bloginfo('url') . $url;
             }
             return $url;
         }

        /**
         * Function mb_convert_encoding
         */
        public function nh_mb_convert_encoding($string)
        {
            return mb_convert_encoding($string, "UTF-8", "HTML-ENTITIES");
        }


        /**
         * Function mb_convert_encoding
         */
        public function nh_mb_convert_encodingArray($array)
        {
            return mb_convert_encoding($array[1], "UTF-8", "HTML-ENTITIES");
        }

        /**
        * Functin get categories
        */
        public function nh_getpostcategories($id)
        {
            $cat =array();
            if ($id) {
                $cats = get_the_category($id);
                if ($cats) {
                    foreach ($cats as $c) {
                        $cat[] = $c->term_id;
                    }
                }
            }
            return ($cat);
        }

        /*
        * Check login
        */
        public function nh_must_login()
        {
            echo "You must log in";
            die();
        }


        /*
        Google map load
        */
        public function nh_ynaa_google_action()
        {
            $result['type'] = "success";
            $result['vote_count'] = '$new_vote_count';
            $result = json_encode($result);
            echo $result;
            exit();
            die();
            if (!wp_verify_nonce($_REQUEST['nonce'], "my_user_vote_nonce")) {
                exit("No naughty business please");
            }

            $vote_count = get_post_meta($_REQUEST["post_id"], "votes", true);
            $vote_count = ($vote_count == '') ? 0 : $vote_count;
            $new_vote_count = $vote_count + 1;

            $vote = update_post_meta($_REQUEST["post_id"], "votes", $new_vote_count);

            if ($vote === false) {
                $result['type'] = "error";
                $result['vote_count'] = $vote_count;
            } else {
                $result['type'] = "success";
                $result['vote_count'] = $new_vote_count;
            }

            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                $result = json_encode($result);
                echo $result;
            } else {
                header("Location: ".$_SERVER["HTTP_REFERER"]);
            }

            die();
        }


        private function getattachedImages($id)
        {
            $images = array();
            $media = get_attached_media('image', $id);
            if ($media && is_array($media) && count($media)>0) {
                foreach ($media as $k=>$m) {
                    $img = wp_get_attachment_image_src($k, 'large');
                    $images['l'][] = $img[0];
                    $images['o'][] = wp_get_attachment_url($k);
                }
            }
            return $images;
        }


        /**
        *PUSH Funktion
        */
        public function ny_ynaa_push_action()
        {
            if (!($this->push_settings['appkey']) || $this->push_settings['appkey'] == '') {
                _e('No Appkey.', 'nh-ynaa');
                die();
            }
            if (!($this->push_settings['pushsecret']) || $this->push_settings['pushsecret'] == '') {
                _e('No Push Secret Key.', 'nh-ynaa');
                die();
            }
        //	if(!($this->push_settings['pushurl']) || $this->push_settings['pushurl'] == '') { _e('No Push Url.', 'nh-ynaa'); die(); }

            define('APPKEY', esc_attr($this->push_settings['appkey'])); // App Key
            define('PUSHSECRET', esc_attr($this->push_settings['pushsecret'])); // Master Secret
        //	define('PUSHURL', esc_attr( $this->push_settings['pushurl'] ));
            $device_types = array('ios', 'android');
            //$device_types = array('ios');
            $cat = '';
            if ($_POST['push_cat']) {
                $cat = (implode(',', $_POST['push_cat']));
            }
            $_POST['push_text'] = base64_encode($_POST['push_text']).'&base=64';
            //$_POST['push_text'] = str_replace(array('%E2%80%9E','%E2%80%9C'),('"'),$_POST['push_text']);
            $url= 'http://www.blappsta.com/';
            $qry_str = '?bas=pushv2&pkey='.APPKEY.'&pmkey='.PUSHSECRET.'&url='.get_bloginfo('url').'&nhcat='.$cat.'&id='.$_POST['push_post_id'].'&push_text='.($_POST['push_text']).'&type='.urlencode($_POST['post_type']);
            $qry_str = apply_filters('nh_pushQuerryString', $qry_str);
            //echo $qry_str;			echo serialize($_POST);			die();
            if (function_exists('curl_version') && empty($this->push_settings['jspush'])) {
                $ch = curl_init();
                // Set query data here with the URL
                curl_setopt($ch, CURLOPT_URL, $url . $qry_str.'&nh_mode=curl');

                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_TIMEOUT, '3');
                $push_response = trim(curl_exec($ch));
                curl_close($ch);
                echo($push_response);
            } elseif (ini_get('allow_url_fopen') && empty($this->push_settings['jspush'])) {

                //echo ('http://www.blappsta.com/?bas=push&pkey='.APPKEY.'&pmkey='.PUSHSECRET.'&url='.get_bloginfo('url').'&cat='.$cat.'&id='.$_POST['push_post_id'].'&push_text='.$_POST['push_text']);
                //die();
                echo(file_get_contents($url.(($qry_str)).'&nh_mode=fgc'));
            } else {
                echo 'nomodul';

                /*echo '<script type="text/javascript">';
                echo 'window.open("'.$url.$qry_str.'&nh_mode=js");';
                echo '</script>';*/

                echo ' '.$url . ($qry_str).'&nh_mode=js';

                //_e('Error: No supported Modul installed.', 'nh-ynaa');
            }
            die();
            return;
        }

        private function decode_characters($data)
        {
            $text = $data;

            $enc = mb_detect_encoding($text, "UTF-8,ISO-8859-1");
            $resutl_characters = iconv($enc, "UTF-8", $text);
            $resutl_characters = html_entity_decode ($resutl_characters, ENT_XML1, 'UTF-8' );
            //$resutl_characters= mb_convert_encoding($resutl_characters, 'UTF-8', 'HTML-ENTITIES');
            return $resutl_characters;
        }

    /**
    *PUSH Funktion have to combine both
    */
    public function ny_ynaa_push_action2($postid)
    {
        if (!($this->push_settings['appkey']) || $this->push_settings['appkey'] == '') {
            //_e('No Appkey.', 'nh-ynaa');
           return 2;
        }
        if (!($this->push_settings['pushsecret']) || $this->push_settings['pushsecret'] == '') {
            //_e('No Push Secret Key.', 'nh-ynaa');
        return 3;
        }
     /* if(!($this->push_settings['pushurl']) || $this->push_settings['pushurl'] == '') {
        // _e('No Push Url.', 'nh-ynaa');
        return 4;
      }
    */
      define('APPKEY', esc_attr($this->push_settings['appkey'])); // App Key
      define('PUSHSECRET', esc_attr($this->push_settings['pushsecret'])); // Master Secret
    //  define('PUSHURL', esc_attr( $this->push_settings['pushurl'] ));
      $device_types = array('ios', 'android');
      //$device_types = array('ios');
      $cat = wp_get_post_categories($postid);
        if ($cat) {
            $cat = implode(',', $cat);
        }
          //$push_text = urlencode(html_entity_decode(get_the_title($postid), ENT_COMPAT, 'UTF-8'));
        //$push_text = base64_encode(html_entity_decode(get_the_title($postid), ENT_COMPAT, 'UTF-8')).'&base=64';

       $push_text = base64_encode($this->decode_characters(get_the_title($postid))).'&base=64';
        //$push_text = base64_encode(mb_convert_encoding(get_the_title($postid), 'UTF-8', 'HTML-ENTITIES')).'&base=64';
        //if(get_bloginfo('url') == 'http://yna.blappsta.net')$push_text = 'temp'.base64_encode(wp_specialchars_decode(get_the_title($postid)));

            $url= 'http://www.blappsta.com/';
        $qry_str = '?bas=pushv2&pkey='.APPKEY.'&pmkey='.PUSHSECRET.'&url='.get_bloginfo('url').'&nhcat='.$cat.'&id='.$postid.'&push_text='.$push_text.'&type='.urlencode($_POST['post_type']);
        $qry_str = apply_filters('nh_pushQuerryString', $qry_str);
        //return $qry_str;
      if (ini_get('allow_url_fopen')) {
          $blappsta_return = (file_get_contents($url.(($qry_str)).'&nh_mode=fgc'));
      } elseif (function_exists('curl_version')) {
          $ch = curl_init();
        // Set query data here with the URL
        curl_setopt($ch, CURLOPT_URL, $url . $qry_str.'&nh_mode=curl');

          curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
          curl_setopt($ch, CURLOPT_TIMEOUT, '3');
          $push_response = trim(curl_exec($ch));
          curl_close($ch);
          $blappsta_return=($push_response);
      } else {
          return 5;
      }
        if ($blappsta_return) {
            $blappsta_return = @json_decode($blappsta_return, true);
            if (isset($blappsta_return['push status']['error']['error_code'])) {
                return $blappsta_return['push status']['error']['error_code'];
            } else {
                return 7;
            }
        } else {
            return 6;
        }
    }

        /*
         * Function to get Lan and LAt
        */
        public function getLatLng($address)
        {
            $address = str_replace(" ", "+", $address);

            $url='http://maps.googleapis.com/maps/api/geocode/json?address='.$address.'&sensor=false';
            if (ini_get('allow_url_fopen')) {
                $source = file_get_contents($url);
                $obj = json_decode($source);
                if ($obj != null) {
                    $LATITUDE = $obj->results[0]->geometry->location->lat;
                    $LONGITUDE = $obj->results[0]->geometry->location->lng;
                } else {
                    $LATITUDE = 0;
                    $LONGITUDE = 0;
                }
            } else {
                $LATITUDE = 0;
                $LONGITUDE = 0;
            }
            return array('lat'=>$LATITUDE,'lng'=>$LONGITUDE);
        }/* END function getLatLon() */

        /**
         * Helpfunction for short text
        */
        public function shortenText($text, $limit=25)
        { // Function name ShortenText
            $chars_limit = $limit; // Character length
            $chars_text = strlen($text);
            if ($chars_text > $chars_limit) {
                $text = $text." ";
                $text = substr($text, 0, $chars_limit);
                $text = substr($text, 0, strrpos($text, ' '));
                $text = $text."...";
            } // Ellipsis
             return $text;
        }  // END public function shortenText()

        public function ReSizeImagesInHTML($HTMLContent, $MaximumWidth, $MaximumHeight)
        {

            // find image tags
            preg_match_all('/<img[^>]+>/i', $HTMLContent, $rawimagearray, PREG_SET_ORDER);

            // put image tags in a simpler array
            $imagearray = array();
            for ($i = 0; $i < count($rawimagearray); $i++) {
                array_push($imagearray, $rawimagearray[$i][0]);
            }

            // put image attributes in another array
            $imageinfo = array();
            foreach ($imagearray as $img_tag) {
                preg_match_all('/(src|width|height)=("[^"]*")/i', $img_tag, $imageinfo[$img_tag]);
            }

            // combine everything into one array
            $AllImageInfo = array();
            foreach ($imagearray as $img_tag) {
                $ImageSource = str_replace('"', '', $imageinfo[$img_tag][2][0]);
                $OrignialWidth = str_replace('"', '', $imageinfo[$img_tag][2][1]);
                $OrignialHeight = str_replace('"', '', $imageinfo[$img_tag][2][2]);

                $NewWidth = $OrignialWidth;
                $NewHeight = $OrignialHeight;
                $AdjustDimensions = "F";

                if ($OrignialWidth > $MaximumWidth) {
                    $diff = $OrignialWidth-$MaximumHeight;
                    $percnt_reduced = (($diff/$OrignialWidth)*100);
                    $NewHeight = floor($OrignialHeight-(($percnt_reduced*$OrignialHeight)/100));
                    $NewWidth = floor($OrignialWidth-$diff);
                    $AdjustDimensions = "T";
                }

                if ($OrignialHeight > $MaximumHeight) {
                    $diff = $OrignialHeight-$MaximumWidth;
                    $percnt_reduced = (($diff/$OrignialHeight)*100);
                    $NewWidth = floor($OrignialWidth-(($percnt_reduced*$OrignialWidth)/100));
                    $NewHeight= floor($OrignialHeight-$diff);
                    $AdjustDimensions = "T";
                }

                $thisImageInfo = array('OriginalImageTag' => $img_tag , 'ImageSource' => $ImageSource , 'OrignialWidth' => $OrignialWidth , 'OrignialHeight' => $OrignialHeight , 'NewWidth' => $NewWidth , 'NewHeight' => $NewHeight, 'AdjustDimensions' => $AdjustDimensions);
                array_push($AllImageInfo, $thisImageInfo);
            }

            // build array of before and after tags
            $ImageBeforeAndAfter = array();
            for ($i = 0; $i < count($AllImageInfo); $i++) {
                if ($AllImageInfo[$i]['AdjustDimensions'] == "T") {
                    $NewImageTag = str_ireplace('width="' . $AllImageInfo[$i]['OrignialWidth'] . '"', 'width="' . $AllImageInfo[$i]['NewWidth'] . '"', $AllImageInfo[$i]['OriginalImageTag']);
                    $NewImageTag = str_ireplace('height="' . $AllImageInfo[$i]['OrignialHeight'] . '"', 'height="' . $AllImageInfo[$i]['NewHeight'] . '"', $NewImageTag);

                    $thisImageBeforeAndAfter = array('OriginalImageTag' => $AllImageInfo[$i]['OriginalImageTag'] , 'NewImageTag' => $NewImageTag);
                    array_push($ImageBeforeAndAfter, $thisImageBeforeAndAfter);
                }
            }

            // execute search and replace
            for ($i = 0; $i < count($ImageBeforeAndAfter); $i++) {
                $HTMLContent = str_ireplace($ImageBeforeAndAfter[$i]['OriginalImageTag'], $ImageBeforeAndAfter[$i]['NewImageTag'], $HTMLContent);
            }

            return $HTMLContent;
        }

        //Action on publish posts
        public function nh_ynaa_publish_posts($ID=0, $post=null)
        {
            if ($ID) {
                global $nh_push_return;
                if (!$this->push_settings) {
                    $this->push_settings = get_option($this->push_settings_key);
                }

                if ($this->push_settings['autopush'] && !get_post_meta($ID, 'nh_blappsta_send_push', true)) {
                    $nh_push_return = $this->ny_ynaa_push_action2($ID);
                    add_post_meta($ID, 'nh_blappsta_send_push', time(), true);
                }
                add_filter('redirect_post_location', array($this,'nh_add_get_var2'));
            }
        }


        public function nh_add_get_var($loc)
        {
            return add_query_arg('nh_pm', 1, $loc);
        }

        public function nh_add_get_var2($loc)
        {
            global $nh_push_return;
            return add_query_arg('nh_pm', $nh_push_return, $loc);
        }

        /**
         * Find taxonomies from which to retrieve categories. Will use multiple taxonomies depending on setup.
         * Method is static so it can be called from activation hook.
         */
        public static function nh_find_taxonomies_with_avada($use_avada)
        {
            $result = array('category');
            if ($use_avada) {
                $result[] = 'portfolio_category';
            }
            return $result;
        }

        public function nh_find_taxonomies()
        {
            $is_avada_active = wp_get_theme()->Name === 'Avada';
            /* refer to static method */
            return NH_YNAA_Plugin::nh_find_taxonomies_with_avada($this->general_settings['avada-categories'] && $is_avada_active);
        }

        /**
         * Find category info using get_term(). Will use multiple taxonomies depending on setup.
         * @see get_term()
         */
        public function nh_get_category($cat_id)
        {
            /* try wordpress category first */
            $result = get_term((int)$cat_id, 'category');
            if ($result) {
                return $result;
            }

            /* or, try Avada portfolio category next */
            if (taxonomy_exists('portfolio_category')) {
                return get_term((int)$cat_id, 'portfolio_category');
            }

            /* couldn't find category */
            return null;
        }

        /**
         * Check whether this is a category using term_exists(). Will use multiple taxonomies depending on setup.
         * @see term_exists()
         */
        public function nh_is_category($cat_id)
        {
            /* try wordpress category first */
            $result = term_exists((int)$cat_id, 'category');
            if ($result) {
                return true;
            }

            /* or, try Avada portfolio category next */
            if ($this->general_settings['avada-categories']) {
                return term_exists((int)$cat_id, 'portfolio_category');
            }

            /* couldn't find category */
            return false;
        }

    //Admin notice
    public function nh_ynaa_admin_notice()
    {
        if (isset($_GET['nh_pm'])) {
            echo '<div class="updated"><p>';
            switch ($_GET['nh_pm']) {
          case 0: _e('Push send successful.', 'nh-ynaa'); break;
          default: _e('Unknown error sending the push message.', 'nh-ynaa');  echo ' (Error code:'.$_GET['nh_pm'].')';  break;
        }

            echo '</p></div>';
        }
    }
    } // END class NH YNAA Plugin
} // END if(!class_exists('NH_YNAA_Plugin))

if (class_exists('NH_YNAA_Plugin')) {
    // Installation and uninstallation hooks
    register_activation_hook(__FILE__, array('NH_YNAA_Plugin', 'nh_ynaa_activate'));
    register_deactivation_hook(__FILE__, array('NH_YNAA_Plugin', 'nh_ynaa_deactivate'));

    //add_action( 'wpmu_new_blog', array('NH_YNAA_Plugin','nh_new_blog'),100,6);




    // instantiate the plugin class
    $nh_ynaa = new NH_YNAA_Plugin();
    add_action('plugins_loaded', array($nh_ynaa,'nh_update_db_check'));

  //add Notice
   add_action('admin_notices', array($nh_ynaa,'nh_ynaa_admin_notice'));

    //publish Post
    add_action('publish_post', array($nh_ynaa,'nh_ynaa_publish_posts'));

    // Add a link to the settings page onto the plugin page
    if (isset($nh_ynaa)) {
        // Add the settings link to the plugins page
        function nh_ynaa_plugin_settings_link($links)
        {
            $settings_link = '<a href="options-general.php?page=nh_ynaa_plugin_options">'.(__('Settings')).'</a>';
            array_unshift($links, $settings_link);
            return $links;
        }

        $plugin = plugin_basename(__FILE__);
        add_filter("plugin_action_links_$plugin", 'nh_ynaa_plugin_settings_link');

        //Add Query vars
        function nh_ynaa_add_query_vars_filter($vars)
        {
            $vars[] = QUERY_VARS_YNAA;
            return $vars;
        }
        add_filter('query_vars', 'nh_ynaa_add_query_vars_filter');
        if (!empty($_GET[QUERY_VARS_YNAA])) {
            add_action('template_redirect', array($nh_ynaa, 'nh_ynaa_template_redirect'), 2);
        }
    }
}


function nh_ynaa_load_textdomain()
{
    load_plugin_textdomain('nh-ynaa', false, dirname(plugin_basename(__FILE__)) . '/lang');
}
add_action('plugins_loaded', 'nh_ynaa_load_textdomain');


add_action('admin_footer', 'nh_action_javascript');
function nh_action_javascript()
{
    global $post;

    if(isset($_GET['post'])) $post = get_post($_GET['post']);
    $cat = wp_get_post_categories($post->ID);
    if ($cat) {
        $cat = implode(',', $cat);
    } ?>
<script type="text/javascript" >
//<![CDATA[
	<?php
    if (isset($post->ID)) {
        ?>
jQuery(document).ready(function($) {

	//alert('<?php echo $post->ID; ?>');

	// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php

	$('#nh_ynaa_sendpush').click(function(e) {
		//alert('<?php echo $post->post_type; ?>');
		if(<?php if ($post->post_status== 'publish') {
            echo 1;
        } else {
            echo 0;
        } ?>)	{
			if($('#nh_ynaa_pushtext').val()=='') alert('<?php _e('Insert Pushtext!', 'nh-ynaa'); ?>');
			else {
				$(this).prop('disabled', true);
				//alert('<?php _e('Pleas wait!'); ?>');
				jQuery('#nh-push-dialog span').show();
				jQuery.ajax({
					 type : "post",
					 url : ajaxurl,
					 //dataType:"json",
					 data : {action: "ny_ynaa_push_action", push_post_id:<?php echo $post->ID; ?>, push_cat:[<?php echo $cat; ?>] , push_text:$('#nh_ynaa_pushtext').val(), post_type:<?php echo "'".$post->post_type."'"; ?>},
					 success: function(data,textStatus,jqXHR ) {
						jQuery('#nh-push-dialog span').hide();

						if(data.substr(0,7)=='nomodul' ){


							jQuery.get( data.substr(8), function( data2 ) {
								if(data2['push status']['error']['error_code'] != undefined) {
									if((data2['push status']['error']['error_code']) != 0){
										alert(data2['push status']['error']['error_message']);
									}
									else alert(data2['push status']['Send Push'] );
								}
								else alert( "Error on send push. Errorcode: 1002" );
								//console.log(data2);

							})
							  .fail(function(e) {

							  	window.open( data.substr(8));
							  	alert( "Error on send push. Errorcode: 1001. Please allow Pop-Up window to send push." );
							  });


						}
						else{

							if(data && data.indexOf("Send successful")!=-1) alert('Push send success.');
                            else alert(data);
						}
						$('#nh_ynaa_sendpush').prop('disabled', false);
						 //console.log(data);
					 }
				  })   ;
			}
		}
		else alert('<?php _e('You have to publish the Post first.!', 'nh-ynaa'); ?>');
		//alert('Got this from the server: ' + e);
	});
});
<?php

    } ?>
//]]>
</script>
<?php

}


add_action('wp_ajax_nh_search_action', 'nh_search_action');

add_action('wp_enqueue_scripts', 'nh_blappsta_add_stylesheet');
function nh_blappsta_add_stylesheet()
{
    wp_enqueue_style('blappsta-style-front', plugins_url('css/blappsta.css', __FILE__), array(), '1.0');
}
//Widget
include('classes/nh-widget.php');
// register widget
add_action('widgets_init', create_function('', 'return register_widget("NH_Blappsta_Widget");'));



?>
