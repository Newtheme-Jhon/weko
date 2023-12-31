<?php

global $wpdb;

$atr_dir_path = ( substr( get_template_directory(),     -1 ) === '/' ) ? get_template_directory()     : get_template_directory()     . '/';
$atr_dir_uri  = ( substr( get_template_directory_uri(), -1 ) === '/' ) ? get_template_directory_uri() : get_template_directory_uri() . '/';

define( 'ATR_DIR_PATH', $atr_dir_path );
define( 'ATR_DIR_URI',  $atr_dir_uri  );

require_once ATR_DIR_PATH . 'includes/class-atr-master.php';

function atr_run_master() {
    
    $atr_master = new ATR_Master;
    $atr_master->run();
    
}

atr_run_master();

//add filter type image csv
function atr_mime_type_csv_woocommerce($mime_types){

    $types = array(
        'webp' => 'image/webp' //add webp extension
    );
    return $types;

}
add_filter('woocommerce_rest_allowed_image_mime_types', 'atr_mime_type_csv_woocommerce', 1, 1);

remove_action('storefront_loop_post', 'storefront_post_header', 10);
remove_action('storefront_loop_post', 'storefront_post_content', 30);
remove_action('storefront_loop_post', 'storefront_post_taxonomy', 40);

//Add new action hook post
function atr_theme_posts_content(){
    get_template_part('public/partials/blogs', 'weko');
}
add_action('storefront_loop_post', 'atr_theme_posts_content');

//ADD code apirest wordpress
include 'api_rest.php';