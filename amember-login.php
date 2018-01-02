<?php
/*
Plugin Name: WP AMember Login
Plugin URI: https://pandhu.id/wp-amember-login
Description: Plugin to override default login using AMember login with AMember API.
Version: 1.0.0
Author: Pandhu Hutomo Aditya
Author URI: https://pandhu.id
License: GNU AGPLv3
License URI: http://www.gnu.org/licenses/agpl-3.0.html
Text Domain: wp-amember-login

Copyright 2017 Pandhu Hutomo Aditya (email: aditya.pandhu@gmail.com)

WP AMember Login is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

WP AMember Login is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with WP AMember Login. If not, see https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html.
*/
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

define( 'WP_AMEMBER_LOGIN__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
require_once( WP_AMEMBER_LOGIN__PLUGIN_DIR . 'includes/class.amember-api-handler.php' );
require_once( WP_AMEMBER_LOGIN__PLUGIN_DIR . 'includes/class.setting-page.php' );
require_once( WP_AMEMBER_LOGIN__PLUGIN_DIR . 'includes/class.role-setting-page.php' );

if( is_admin() ){
    $setting_page = new Setting_Page();
		$role_setting_page = new Role_Setting_Page();
}
add_action('wp_authenticate', 'wp_amember_login_authenticate');

function wp_amember_login_authenticate(){
    $username = sanitize_text_field($_POST['log']);
    $password = sanitize_text_field($_POST['pwd']);

    $amember_api_handler = new AMember_API_Handler();
    $login_information = json_decode($amember_api_handler->login($username, $password));

    if($login_information->ok){
        $user = get_user_by('login', $login_information->login);

        if ( !is_wp_error( $user ) ){
            wp_clear_auth_cookie();
            wp_set_current_user ( $user->ID );
            wp_set_auth_cookie  ( $user->ID );

            wp_redirect(get_site_url());
            echo json_encode(array('error_code'=>0));
            exit();
        } else {
            wp_redirect(get_site_url().'/wp-login');
            echo json_encode(array('error_code'=>1));
            exit();
        }
    }
}

function wp_amember_create_user(){

}



  function wp_amember_login_settings(){

  }
