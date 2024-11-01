<?php
/**
* Plugin Name: WP Tweet Walls
* Plugin URI: http://www.solaplugins.com
* Description: Create beautiful Twitter walls on your site and add them in a flash
* Version: 1.0.4
* Author: SolaPlugins
* License: GPLv2 or later
* Text Domain: wp-tweet-walls
* Domain Path: /languages
*/

if ( !defined( 'ABSPATH' ) ) {
        die;
}

global $wpdb;
define( 'WPTW_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'WPTW_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'WPTW_WALLS_TABLE', $wpdb->prefix . 'wptw_walls' );

require_once(WPTW_PLUGIN_PATH . 'includes/functions.php');
require_once(WPTW_PLUGIN_PATH . 'includes/ajax.php');
require_once(WPTW_PLUGIN_PATH . 'includes/shortcodes.php');

add_action( 'admin_menu', 'wptw_pages' );

function wptw_pages() {
	add_menu_page( __( 'WP Tweet Walls', 'wp-tweet-walls' ), __( 'WP Tweet Walls', 'wp-tweet-walls' ), 'manage_options', 'wp_tweet_walls', 'wptw_page_main', 'dashicons-twitter' );
        add_submenu_page( 'wp_tweet_walls', __( 'Tweet Walls', 'wp-tweet-walls' ), __( 'Tweet Walls', 'wp-tweet-walls' ), 'manage_options', 'wp_tweet_walls', 'wptw_page_main' );
        
        $subpages = [];
        $subpages = apply_filters( 'wptw_subpages', $subpages );
        $subpages[] = [
                'title'       => __( 'Settings', 'wp-tweet-walls' ),
                'page_title'  => __( 'Settings', 'wp-tweet-walls' ),
                'permissions' => 'manage_options',
                'slug'        => 'wp_tweet_walls_settings',
                'callback'    => 'wptw_page_settings'
        ];

        foreach ($subpages as $subpage) {
                add_submenu_page( 'wp_tweet_walls', $subpage['title'], $subpage['page_title'], $subpage['permissions'], $subpage['slug'], $subpage['callback'] );
        }
}

function wptw_page_main() {
        if (wptw_is_first_time()) {
                include('pages/welcome.php');
        } else {
                include('pages/walls.php');
        }
}

function wptw_page_settings() {
        if (wptw_is_first_time()) {
                include('pages/welcome.php');
        } else {
                include('pages/settings.php');
        }
}


function wptw_admin_scripts() {
        wp_enqueue_style( 'wp-color-picker' ); 
        wp_register_style( 'wptw-admin-styles', WPTW_PLUGIN_URL . '/assets/css/admin-styles.css', false, '1.0.0' );
        wp_enqueue_style( 'wptw-admin-styles' );

        wp_register_script( 'wptw-twtter-widgets', '//platform.twitter.com/widgets.js' );
        wp_enqueue_script( 'wptw-twtter-widgets' );

        wp_register_script( 'wptw-admin-script', WPTW_PLUGIN_URL . '/assets/js/admin-script.js', array( 'jquery', 'wp-color-picker' ), '1.0.1' );
        wp_enqueue_script( 'wptw-admin-script' );

        $localized_data = [
        	'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'is_pro' => wptw_is_pro(),
            'nonce' => wp_create_nonce('wptw-nonce')
        ];

        wp_localize_script( 'wptw-admin-script', 'wptw_localized', $localized_data );
}

function wptw_user_scripts() {
        wp_register_script( 'wptw-twtter-widgets', '//platform.twitter.com/widgets.js' );
        wp_enqueue_script( 'wptw-twtter-widgets' );
        wp_register_style( 'wptw-user-styles', WPTW_PLUGIN_URL . '/assets/css/user-styles.css', false, '1.0.0' );
        wp_enqueue_style( 'wptw-user-styles' );

        wp_register_script( 'wptw-twitter-script', WPTW_PLUGIN_URL . '/assets/js/wptw-twitter.js', array( 'jquery' ) );
        wp_enqueue_script( 'wptw-twitter-script' );

        wp_register_script( 'wptw-user-script', WPTW_PLUGIN_URL . '/assets/js/user-script.js', array( 'jquery', 'wptw-twitter-script' ) );
        wp_enqueue_script( 'wptw-user-script' );

        $localized_data = [
        	'ajaxurl' => admin_url( 'admin-ajax.php' )
        ];

        wp_localize_script( 'wptw-user-script', 'wptw_localized', $localized_data );
}

add_action( 'admin_enqueue_scripts', 'wptw_admin_scripts' );
add_action( 'wp_enqueue_scripts', 'wptw_user_scripts' );

add_action( 'init', 'wptw_init' );

function wptw_init() {

        add_action( 'admin_print_scripts', 'wptw_hide_unrelated_notices' );
        // Update whether user has viewed the welcome page
        if (isset($_POST['wptw-submit-first-time'])) {
                update_option( 'wptw_installed', true );
        }
        wptw_create_tables();
}

register_activation_hook( __FILE__, 'wptw_activate' );

function wptw_activate() {
	wptw_create_tables();
}

if (!wptw_is_pro()) {
        add_filter( 'wptw_subpages', 'wptw_basic_pro_subpages' );
}

function wptw_basic_pro_subpages($subpages) {
         $page =  [
                'title'       => __( 'Timelines', 'wp-tweet-walls' ),
                'page_title'  => __( 'Timelines', 'wp-tweet-walls' ),
                'permissions' => 'manage_options',
                'slug'        => 'wp_tweet_walls_timelines',
                'callback'    => 'wptw_basic_pro_timelines_page'
        ];

        // Twitter Buttons page
        $buttons_page =  [
                'title'       => __( 'Twitter Buttons', 'wp-tweet-walls' ),
                'page_title'  => __( 'Twitter Buttons', 'wp-tweet-walls' ),
                'permissions' => 'manage_options',
                'slug'        => 'wp_tweet_walls_buttons',
                'callback'    => 'wptw_basic_pro_buttons_page'
        ];
        $subpages[] = $page;
        $subpages[] = $buttons_page;
        return $subpages;
}