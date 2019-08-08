<?php

/**
 * Admin Menu Builder main file
 *
 * @package Admin Menu Builder
 */

/*
Plugin Name:  CustomPost&Others 
Plugin URI:   http://imransayed.com/custom-post-tax-builder
Description:  This plugin creates custom post and taxonomies
Version:      1.0.0
Author:       MOHD. RASADIN
Author URI:   https://profiles.wordpress.org/gsayed786
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:  admin-menu-builder
Domain Path:  /languages
*/

// Include custom_post_type_functions.php
require_once 'inc/custom_post_type_functions.php';
add_action('wp_enqueue_scripts', 'load_scripts');
function load_scripts() {
    wp_enqueue_script('cpt-script', plugins_url('/js/script.js', __FILE__), array('jquery'), rand(), true);

    $options = array(
        'ajax_url' => admin_url('admin-ajax.php'),
    );
    wp_localize_script('cpt-script', 'admin_localizer', $options);
}


/**
 * Ajax Request
 */
add_action( 'wp_ajax_count_like', 'count_like' );
function count_like() {

    if( isset($_POST['post_id']) ) {
        $post_id = $_POST['post_id'];
    }

    $result = 100;

    $prev_value = intval(get_post_meta($post_id, 'cpt_post_like', true));

    $total = $prev_value + 1;

    update_post_meta($post_id, 'cpt_post_like', $total);

    echo json_encode($total);

    die();
}


