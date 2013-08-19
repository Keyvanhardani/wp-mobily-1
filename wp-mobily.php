<?php
/* 
Plugin Name: Wp-Mobily
Plugin URI: http://iappi.de/wp-mobily
Description: Select your Mobile-Theme, only for Mobile Device's.
Author: Keyvan Hardani ( iAppi.de )
Version: 1.0
Author URI: http://www.iappi.de
*/

/*  Copyright 2013  Keyvan Hardani  (email : Hardani@iappi.de)*/

$mobily_mobile_browser 	= '';
$mobily_status 			= '';
$mobily_shown_theme 		= '';

add_action('plugins_loaded', 'mobily_start', 1);
function mobily_start(){
	global $mobily_mobile_browser;
	global $mobily_status;
	global $mobily_shown_theme;
	
	$time = '0';
	$url_path = '/';
	
	$checkReturn 			=	mobily_checkMobile();
	$mobily_mobile_browser	=	$checkReturn['mobily_mobile_browser'];
	$mobily_status			=   $checkReturn['mobily_status'];
	
	$forceLayout			= '';
	
	//Force Theme Display request from visitor.
	if (isset($_COOKIE['mobily_wp_theme_layout'])){
		$forceLayout = $_COOKIE['mobily_wp_theme_layout'];
	}
	
	if (isset($_GET['mobily_wp_theme_layout'])){
		if ($_GET['mobily_wp_theme_layout'] == 'mobily'){
			$forceLayout	= 'mobile';
			setcookie('mobily_wp_theme_layout', $_GET['mobily_wp_theme_layout'], $time, $url_path);
		} else {
			$forceLayout	= 'orginal';
			setcookie('mobily_wp_theme_layout', $_GET['mobily_wp_theme_layout'], $time, $url_path);	
		}
	}
	
	if (!empty($forceLayout)){ //IF USER FORCE FOR THE THEME
		if ($forceLayout == 'mobile'){ // IF FORCED THEME IS MOBILE
			$mobily_mobile_browser = get_option('iphone_theme');
			add_filter('stylesheet', 'loadMobileStyle');
			add_filter('template', 'loadMobileTheme');
			$mobily_shown_theme = 'mobile';
		}
	} else { // NORMAL THEME [PLUGIN DEFAULT]
		if (!empty($mobily_mobile_browser)){
			add_filter('stylesheet', 'loadMobileStyle');
			add_filter('template', 'loadMobileTheme');
			$mobily_shown_theme = 'mobile';
		}
	}	
}

function loadMobileStyle(){
	global $mobily_mobile_browser;
	$mobileTheme =  $mobily_mobile_browser;
	$themeList = get_themes();
	foreach ($themeList as $theme) {
	  if ($theme['Name'] == $mobileTheme) {
		  return $theme['Stylesheet'];
	  }
	}	
}

function loadMobileTheme(){
	global $mobily_mobile_browser;
	$mobileTheme =  $mobily_mobile_browser;
	$themeList = get_themes();
	foreach ($themeList as $theme) {
	  if ($theme['Name'] == $mobileTheme) {
		  return $theme['Template'];
	  }
	}	
}

// Embed Switch Links in Theme Via Shortcode
// [show_theme_switch_link]
function wp_mobily_shorter_func( $atts ){
 	global $mobily_shown_theme;
	global $mobily_status;
	$desktopSwitchLink	= get_option('wp_mobily_shorter_for_desktop');
	if ($mobily_shown_theme){
		$return = '<a rel="external" data-ajax="false" href="'.get_bloginfo('url').'?mobily_wp_theme_layout=desktop" class="mobily-switch-btn godesktop">'.get_option('desktop_view_theme_link_text').'</a>';		
	} else {
		if ((!empty($mobily_status)) || ($desktopSwitchLink == 'yes')){
			$return = '<a href="'.get_bloginfo('url').'?mobily_wp_theme_layout=mobile" class="mobily-switch-btn gomobile">'.get_option('mobile_view_theme_link_text').'</a>';
		}
	}
	return $return;
}
add_shortcode('wp_mobily_shorter', 'wp_mobily_shorter_func');

// DETECT MOBILE BROWSER
function mobily_checkMobile(){
	$mobily_mobile_browser	  	= '';
	$mobileredirect   			= '';
	$mobily_status			  	= '';	
	$user_agent       = $_SERVER['HTTP_USER_AGENT']; // get the user agent value - this should be cleaned to ensure no nefarious input gets executed
	$accept           = $_SERVER['HTTP_ACCEPT']; // get the content accept value - this should be cleaned to ensure no nefarious input gets executed
	
	  switch(true){ // using a switch against the following statements which could return true is more efficient than the previous method of using if statements
	
		case (preg_match('/ipad/i',$user_agent)); // we find the word ipad in the user agent
		  $mobily_mobile_browser = get_option('ipad_theme'); // mobile browser is either true or false depending on the setting of ipad when calling the function
		  $mobily_status = 'Apple iPad';      
		break; // break out and skip the rest if we've had a match on the ipad // this goes before the iphone to catch it else it would return on the iphone instead
	
		case (preg_match('/ipod/i',$user_agent)||preg_match('/iphone/i',$user_agent)); // we find the words iphone or ipod in the user agent
		  $mobily_mobile_browser = get_option('iphone_theme'); // mobile browser is either true or false depending on the setting of iphone when calling the function
		  $mobily_status = 'Apple';      
		break; // break out and skip the rest if we've had a match on the iphone or ipod
	
		case (preg_match('/android/i',$user_agent));  // we find android in the user agent
		  if (preg_match('/mobile/i',$user_agent)):
			  $mobily_mobile_browser = get_option('android_theme'); // mobile browser is either true or false depending on the setting of android when calling the function
			  $mobily_status = 'Android';      
		  else :
			  $mobily_mobile_browser = get_option('android_tab_theme'); // mobile browser is either true or false depending on the setting of android when calling the function
			  $mobily_status = 'Android Tab';      
		  endif;
		  
		break; // break out and skip the rest if we've had a match on android

	} // ends the switch
	
	$return['mobily_mobile_browser']	= $mobily_mobile_browser;
	$return['mobily_status']			= $mobily_status;
	return $return;
} // END OF MOBILE CHECK FUNCTION

include('wp-mobily-inc.php');