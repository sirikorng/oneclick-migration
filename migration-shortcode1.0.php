<?php
/*
*Plugin Name:    Migration Shortcode
*Description:    Migration button (Create Shop) using a simple shortcode.
*Author:         Zhang X. (Freelancer.com)
*Version:        1.0
*Text Domain:    migration-shortcode
*Domain Path:    https://github.com/sirikorng/migration-shortcode

	
*/


/**
 * Register shortcode.
 *
 * Registers shortcode in WordPress.
 */

function create_button_shortcode( $atts, $content = null ) {
    $main_site_url = get_site_url(null, '', 'http');
    $funcs_url = '/wp-migration/oneclick-installer-woointegration1.0.php';
    $short_code_home_html = '<form method="post" action="' . $main_site_url . $funcs_url .'">';
    $current_user = wp_get_current_user();
    $current_user_name = esc_html( $current_user->user_login );
    $short_code_home_html .= '<input class="form-control hidden" type="hidden" id="wp_user" name="wp_user" type="search" value="' . $current_user_name . '">';
    $short_code_home_html .= '<button type="submit" 
    style="background-color:orange;border-radius: 
    8px;font-size: 16px;color: white;  
    padding: 15px 32px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;
    margin: 4px 2px;
    cursor: pointer;"   class="btn btn-success btn-lg">Create Shop</button>';
    $short_code_home_html .= '</form>';

    return $short_code_home_html;
 
}

function view_button_shortcode( $atts, $content = null ) {
    $main_site_url = get_site_url(null, '', 'http');
    $current_user = wp_get_current_user();
    $current_user_name = esc_html( $current_user->user_login );
    $funcs_url = $main_site_url . '/shop/' . $current_user_name . '/wp-login.php';
    $short_code_home_html = '<form method="post" action="' . $funcs_url .'">';
    $short_code_home_html .= '<input class="form-control hidden" type="hidden" id="wp_user" name="wp_user" type="search" value="'.$current_user_name.'">';
	$short_code_home_html .= '<input class="form-control hidden" type="hidden" id="wp_pass" name="wp_pass" type="search" value="'.$user_pass_md5.'">';
    $short_code_home_html .= '<button type="submit" 
    style="background-color:orange;border-radius: 
    8px;font-size: 16px;color: white;  
    padding: 15px 32px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;
    margin: 4px 2px;
    cursor: pointer;"  
    class="btn btn-success btn-lg">View Shop</button>';
    $short_code_home_html .= '</form>';

    return $short_code_home_html;
 
}

function wp_register_migration_shortcode() {
	// Add shortcode.
    add_shortcode( 'create_shop', 'create_button_shortcode' );
    add_shortcode( 'view_shop', 'view_button_shortcode' );
}

add_action( 'init', 'wp_register_migration_shortcode', 80);

?>
