<?php
add_action('admin_menu', 'mobily_mobile_create_menu');
add_action('admin_notices', 'mobily_pro_notification');


if ($_GET['hidemsg'] == 1){
	update_option('mobily_hide_pro_notice','yes');
}

function mobily_pro_notification(){
	if (get_option('mobily_hide_pro_notice') != 'yes'){
		 echo '<div class="updated">
       <p><b>Thanks for install WP-Mobily</b>. Click <a href="http://iappi.de/wp-mobily/" target="_blank">here</a><br> for details.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="options-general.php?page=wp-mobily/wp-mobily-inc.php&hidemsg=1">Hide This Message</a></p>
	   </div>';
	}
}

function mobily_mobile_create_menu() {
	add_options_page('WP-Mobily', 'WP-Mobily', 'administrator', __FILE__, 'mobily_settings_page');
	add_action('admin_init', 'register_mysettings_theme');
}

function register_mysettings_theme() {
	register_setting('wp-mobily-inc-settings-group', 'iphone_theme');
	register_setting('wp-mobily-inc-settings-group', 'ipad_theme');
	register_setting('wp-mobily-inc-settings-group', 'android_theme');
	register_setting('wp-mobily-inc-settings-group', 'android_tab_theme');

}

function mobily_settings_page() {	
	include('wp-mobily-admin/wp-mobily-top.php');
	include('wp-mobily/wp-mobily.php');
	include('wp-mobily/wp-mobily-plug.php');
}