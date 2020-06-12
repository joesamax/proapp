<?php
/**
 * Translation class for words in app
 * 
 * @since version  0.3.5  
 */ 
 if(!class_exists('NH_YNAA_Language'))
  {
    /**
     * Class setup tanslations for app
     * @since version  0.3.5  
     */
    class NH_YNAA_Language
    {
      /**
       * @var string $language Selected Language (default en)
       */  
       private $language='en';
       
      /**
       * 
       * @var array $trans Array for translation
       */
      private $trans;
             
      
      
      /**
       * Constructor of the class for set up default translations (en)
       */ 
      function __construct() {
          load_default_textdomain();
          $this->setTransEN();

      }  
       
     
       
      /**
       * This function returns the language.
       * 
       * @return string $language value
       */
    	function getLanguage(){
        return $this->language;
      }
        
        /**
        * This function sets the language.
        * 
        * @param string $lang Language code
        * @return void
        */
        function setLanguage($lang = 'en'){
          $this->language = $lang;
        }
        
       
        
        /**
        * This function sets the language.
        * 
        * @param string $lang Language code
        * @return array $translation
        */
        function getTranslation($lang = 'en'){
          $return = array();
          $this->setTrans($lang);
          $return = ($this->trans[1]+$this->trans[0]);          
          return $return;
        }
        
        /**
         * This function setup english translation. ($trans['en'])
         *  
         * return void;
         */         
         private function setTransEN(){
           $this->trans[0] =  (array(
            'Menu'=>'Menu',
            'Please wait...'=>'Please wait...',
            'The data are updated' => 'The data are updated',
            'More' => 'More',
            'all-day' => 'all-day',
            'Tip'=>'Tip',
            'This feed has been deleted' => 'This feed has been deleted',
            'The event has been removed from the calendar.'=>'The event has been removed from the calendar.',
            'The event was added to the calendar.' => 'The event was added to the calendar.',
            'Today'=>'Today',
            'Yesterday' => 'Yesterday',
            'The day before yesterday' =>'The day before yesterday',
            'This week' =>'This week',
            'Last week'=>'Last week',
            'The week before last' =>'The week before last',
            'Last month'=>'Last month',
            'This month' => 'This month',
            'Second last month' =>'Second last month',
            'Before two months' => 'Before two months',
            'This year' => 'This year',
            'Last year' => 'Last year',
            'Older than last year' => 'Older than last year',
            'Tomorrow' => 'Tomorrow',
            'The day after tomorrow' => 'The day after tomorrow',
            'Next week' => 'Next week',
            'The week after next' =>'The week after next',
            'Next month' => 'Next month',
            'Over the next month' => 'Over the next month',
            'Over two months' => 'Over two months',
            'Next year' => 'Next year',
            'Later next year' => 'Later next year',
            'Cancel' => 'Cancel',
            'Finished' => 'Finished',
            'Comment'=>'Comment',
            'Show' =>'Show',
            'Comments'=>'Comments',
            'required' =>'required',
            'Name' => 'Name',
            'The e-mail address is not correct' => 'The e-mail address is not correct',
            'Please enter your name.' => 'Please enter your name.',
            'Please enter your comment.' => 'Please enter your comment.',
            'Comments are being loaded ...' => 'Comments are being loaded ...',
            'Clock'=>'Clock',
            'Welcome to'=>'Welcome to', 
            'There was an error.' => 'There was an error.',
            'Redeem'=>'Redeem',
            'Add event to calendar'=>'Add event to calendar',
            'Add to calendar'=>"Add to calendar",
            'Remove event from calendar' => "Remove event from calendar",
            'from'=>"from",
            'to' => 'to',
            'starting at' => 'starting at',
            'Reply' => 'Reply',
            'You have disabled the location services for the app. You can turn them back on in the privacy settings of your device.'=>'You have disabled the location services for the app. You can turn them back on in the privacy settings of your device.',
            'Login'=>'Login',
            'Logout'=>'Logout',
            'Username' => 'Username',
            'Password' => 'Password',
            'The input is incomplete' => 'The input is incomplete',
            'Thanks' => 'Thanks',
			'No set up email account.' => 'No set up email account.',
			"No set up twitter account." => "You have not yet set up your Twitter account on this device, which is necessary for sharing. Please go to your iOS Settings > Twitter and enter your login data.",
			'Copy link' => 'Copy link',
			'Open in Safari' => 'Open in browser',
			'Notifications' => 'Notifications',
			'Select which categories to receive push-notifications from:' => 'Select which categories to receive push-notifications from:',
			'Recent content could not be accessed. Please connect your device to the internet and try again.'=>'Recent content could not be accessed. Please connect your device to the internet and try again.',
			'No set up facebook account.'=>'You have not yet set up your Facebook account on this device, which is necessary for sharing. Please go to your iOS Settings > Facebook and enter your login data.',
			'Settings'=>'Settings',
			'You have not yet set up your Facebook account on this device, which is necessary for sharing. Please go to your iOS Settings > Facebook and enter your login data.'=>'You have not yet set up your Facebook account on this device, which is necessary for sharing. Please go to your iOS Settings > Facebook and enter your login data.',
			'You have not yet set up your Twitter account on this device, which is necessary for sharing. Please go to your iOS Settings > Twitter and enter your login data.'=>'You have not yet set up your Twitter account on this device, which is necessary for sharing. Please go to your iOS Settings > Twitter and enter your login data.',
			'subscription'=>'Subscription',
			'bookmarked_headline'=> 'Bookmarked headlines',
			'bookmarked_posts' => 'Bookmarked posts',
			'recent_notifications'=> 'Recent notifications',
			'bookmarks_notifications' => 'Bookmarks & Notifications',
			'delete'=>'delete',
			'You have not bookmarked any posts yet'=>'You have not bookmarked any posts yet',
			'Share now:'=>'Share now:',
			'WhatsApp not installed'=>'WhatsApp not installed',
			'This might interest you as well' => 'This might interest you as well',
               'Onboarding_FirstText'=>"Find the messaging settings in the main menu of your app.\n\nPlease visit",
               'Onboarding_LastText'=>'and select your favourite topics for push notification.',
               'filter'=>'Filter',
               'search'=>'Search',
               'results'=>'Results:',
               'Rate_App'=>'Rate our app',
                'Rate_App_Text'=> 'If you enjoy using our app, would you mind taking a moment to rate it? Thanks for your support!',
            'Rate_App_Yes'=>'Rate it now',
            'Rate_App_No'=>'No thanks',
            'Rate_App_Later'=>'Remind me later',
               'Search_NoResultViewTitle'=>'No results found.',
               'Search_InputPlaceholder'=>'Search...',
               'No Events'=>'No Events',
               'sendmessage_errormessage'=>'Your device has not installed an app to send emails.',
               'sharing_errormessage'=> 'Your device has not installed an app to share the content.',
               'PDF_Download_Alert_Override_File'=> 'Are you sure you want to replace the existing file "#TITLE#" ?',
               'PDF_Download_Alert_Override_File_yes'=> 'Replace file',
               'PDF_Download_Alert_Override_File_cancel'=> 'Cancel',
               'PDF_Download_Alert_downloading'=> 'Downloading...',
               'PDF_Download_Alert_Install_PDFViewer_button'=> 'OK',
               'PDF_Download_Alert_Install_PDFViewer'=> 'Please install a PDF Viewer to open this file.',
               'PDF_Download_Alert__downloading_Error'=> 'An error occurred while downloading. Please try again later.'


           ));

         }
         
        /**
         * This function setup other translation. Need file 
         *  
         * return void;
         */         
         private function setTrans($transfile) {
         
           if(file_exists(plugin_dir_path(__FILE__).'lang/app_trans_'.$transfile.'.php')){
            include(plugin_dir_path(__FILE__).'lang/app_trans_'.$transfile.'.php');
                  
           }  
           if($translation) $this->trans[1] = $translation;
           else {
             $this->setTransEN();
             $this->trans[1]= $this->trans[0];
           }           
           
         }

       
        
    }
  
  
    
  }


  
?>