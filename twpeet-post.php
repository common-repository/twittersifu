<?php   
#     /* 
#     Plugin Name: TwitterSifu
#     Plugin URI: http://www.websifu.biz/wordpress_plugin_development.php
#     Description: Plugin for tweeting your posts intelligently 
#     Author: Websifu - Malaysia Website Design, SEO Optimization and Web Marketing Agency
#     Version: 1.0.2
#     Author URI: http://www.websifu.biz  
#     */  
require_once('twpeet-admin.php');
require_once('twpeet.inc.php');

define ('twpeet_opt_1_MIN', 60);
define ('twpeet_opt_15_MIN', 60*15);
define ('twpeet_opt_30_MIN', 60*30);
define ('twpeet_opt_1_HOUR', 60*60); 
define ('twpeet_opt_4_HOURS', 4*twpeet_opt_1_HOUR); 
define ('twpeet_opt_6_HOURS', 6*twpeet_opt_1_HOUR); 
define ('twpeet_opt_12_HOURS', 12*twpeet_opt_1_HOUR); 
define ('twpeet_opt_24_HOURS', 24*twpeet_opt_1_HOUR); 
define ('twpeet_opt_48_HOURS', 48*twpeet_opt_1_HOUR); 
define ('twpeet_opt_72_HOURS', 72*twpeet_opt_1_HOUR); 
define ('twpeet_opt_168_HOURS', 168*twpeet_opt_1_HOUR); 
define ('twpeet_opt_INTERVAL', twpeet_opt_12_HOURS); 
define ('twpeet_opt_INTERVAL_SLOP', twpeet_opt_4_HOURS); 
define ('twpeet_opt_AGE_LIMIT', 30); // 120 days
define ('twpeet_opt_MAX_AGE_LIMIT', "None"); // 120 days
define ('twpeet_opt_OMIT_CATS', "");
define('twpeet_opt_TWEET_PREFIX',"");
define('twpeet_opt_ADD_DATA',"false");
define('twpeet_opt_URL_SHORTENER',"tinyurl");
define('twpeet_opt_HASHTAGS',"");

function twpeet_admin_actions() {  
   		add_menu_page('TwitterSifu', 'TwitterSifu', 10, 'TwitterSifu', 'twpeet_admin');
		add_submenu_page('TwitterSifu', 'Options', 'Options', 10,  'TwitterSifu' , 'twpeet_admin');
    }  
    
add_action('admin_menu', 'twpeet_admin_actions');  
add_action('admin_head', 'twpeet_opt_head_admin');
add_action('init','twpeet_tweet_old_post');

add_action('publish_post', 'do_twpeet_opt_tweet_post');
?>