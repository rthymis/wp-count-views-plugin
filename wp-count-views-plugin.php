<?php
// Make sure this file is accesed only via the website
defined ( 'ABSPATH' ) or die ( 'Nope, not accessing this' );

/**
* @package Naq
*/
/*
Plugin Name: Filox Count Views
Description: Plugin to count views per post
Version: 1.0.0
Author: Rodolfos Thymis
License: GPLv2 or later
*/

class NaqPlugin {

    public $plugin;


    // Add the actions of the functions inside the class
    function __construct() {
      $this->plugin = plugin_basename( __FILE__ );
      add_action( 'admin_menu', array( $this, 'add_admin_pages' ) );
      add_filter( "plugin_action_links_$this->plugin" , array ( $this, 'settings_link') );
      // add_action('wp', array( $this, 'updatePostMeta' ) );
      // add_action('save_post', array( $this, 'addPostMeta' ) );
      add_action('wp', array( $this, 'postMeta' ) );
      add_action('add_meta_boxes', [$this, 'create_meta_box']);

    }


    // Create the meta box in the edit post page
    public function create_meta_box() {

      add_meta_box('chart_box', 'Views', [$this, 'meta_box_html'], ['post']);

    }

    // The template of the meta box
    public function meta_box_html() {
      require_once plugin_dir_path( __FILE__ ) . 'templates/single-admin.php';
    }

    // Add a settings link for the plugin in the plugins area
    public function settings_link ( $links ) {
      $settings_link = '<a href="admin.php?page=filox_count_views_plugin">Settings</a>';
      array_push ( $links, $settings_link );
      return $links;
    }

    // Add the plugin admin area
    public function add_admin_pages(){
      add_menu_page ( 'Filox Count Views', 'Filox Count Views', 'manage_options', 'filox_count_views_plugin', array( $this, 'admin_index'), 'dashicons-chart-line', null );
    }

    // The template of the plugin admin area
    public function admin_index() {
      require_once plugin_dir_path( __FILE__ ) . 'templates/admin.php';
    }

    function register() {
      add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
    }

    function activate(){
      flush_rewrite_rules();
    }

    function deactivate(){
      flush_rewrite_rules();
    }

    // Enqueue CSS and JS files

    function enqueue($hook_suffix){
      if( 'post.php' == $hook_suffix || 'post-new.php' == $hook_suffix || 'toplevel_page_filox_count_views_plugin' == $hook_suffix ) {
      wp_enqueue_style( 'mypluginstyle', plugins_url( '/assets/mystyle.css?v=1.4', __FILE__ ) );
      wp_enqueue_script( 'mypluginscript', plugins_url( '/assets/myscript.js', __FILE__ ) );
      wp_enqueue_script( 'mygooglechartscript', 'https://www.gstatic.com/charts/loader.js' );
      wp_enqueue_script( 'my-jss', 'https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js', true );
      wp_localize_script('my-jss', 'WPURLS', array( 'siteurl' => get_option('siteurl') ));
    }
  }

    function postMeta() {
      $current_post_id = get_the_ID();
      $timestamp = array(microtime(true));
      



    if ( ! get_post_meta ( $current_post_id, 'counter' ) && is_single() ) {

      update_post_meta ( $current_post_id, 'counter', array($timestamp) );

    } else if ( get_post_meta ( $current_post_id, 'counter' ) && is_single() ) {

      $counter = get_post_meta ( $current_post_id, 'counter', true);
      array_push ( $counter, $timestamp );
        update_post_meta ( $current_post_id, 'counter', $counter );
      }
      }

    }





if ( class_exists( 'NaqPlugin' ) ) {
    $naqPlugin = new NaqPlugin();
    $naqPlugin->register();
}

register_activation_hook( __FILE__, array ( $naqPlugin, 'activate' ) );

register_deactivation_hook( __FILE__, array ( $naqPlugin, 'deactivate' ) );

///////////////////////
// CUSTOM ENDPOINTS //
//////////////////////

// Endpoints for all posts
function naq_posts() {
  $args = [
    'numberposts' => 99999,
    'post_type' => 'post'
  ];

  $posts = get_posts($args);
  $data = [];

  $i = 0;
  foreach($posts as $post) {
    $data[$i][0] = $post->dateandtime_array;
    $data[$i][1] = $post->counter_array;
    $data[$i][2] = $post->post_title;
    $data[$i][3] = $post->ID;
    $i++;
  }

  return $data;
}

// Endpoints for selecting only one post via the post ID
function naq_post ( $id ) {
$params = $id->get_params();
$args = [
  'ID' => $id['id'],
  'post_type' => 'post',
  'include' => array($params['id']),
];

$posts = get_posts($args);


$data = [];
$i = 0;
foreach($posts as $post) {
  $data[$i][0] = $post->counter; //( $post->counter ? $post->counter : array(time()) );
  $data[$i][1] = $post->post_title;
  $data[$i][2] = $post->ID;
  $i++;
}
//(!$post->counter ? array(time()) : $post->counter );

return $data;

}


// Register the routes:
// /wp-json/naq/v1/posts/
// /wp-json/naq/v1/posts/ID
add_action('rest_api_init', function() {
  register_rest_route('naq/v1', 'posts', [
    'methods' => 'GET',
    'callback' => 'naq_posts',
    'permission_callback' => '__return_true'
  ]);

  register_rest_route('naq/v1', 'posts/(?P<id>[a-zA-Z0-9-]+)', [
    'methods' => 'GET',
    'callback' => 'naq_post',
    'permission_callback' => '__return_true'
  ]);
});
