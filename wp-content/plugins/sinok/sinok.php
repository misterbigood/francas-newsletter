<?php

/**
 * @package Sinok
 */

/*
  Plugin Name: Sinok - Francas
  Plugin URI: http://www.sinok.fr/
  Description: for Francas, set role and capabilities, custom post types, custom options, custom taxonomues, bypass main query, add global $newsletter, etc, etc...
  Version: 0.1
  Author: Hannibal / Sinok
  Author URI: http://www.sinok.fr/
  License: Green? Square?
  Text Domain: sinok
 */


// Make sure we don't expose any info if called directly
if (!function_exists('add_action')) {
    echo 'Danger Will Robinson!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}

define('SNK_VERSION', '0.1');
define('SNK__MINIMUM_WP_VERSION', '4.0');
define('SNK__PLUGIN_URL', plugin_dir_url(__FILE__));
define('SNK__PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SNK_DELETE_LIMIT', 100000);

if ( !defined('DEBUG') ) {
    define('DEBUG', TRUE);
}

register_activation_hook(__FILE__, array('Sinok', 'plugin_activation'));
register_deactivation_hook(__FILE__, array('Sinok', 'plugin_deactivation'));

function sinok_scripts() {
    wp_register_style('sinok.css', SNK__PLUGIN_URL . 'assets/css/sinok.css', array(), SNK_VERSION);
    wp_enqueue_style('sinok.css');
    //wp_enqueue_script( 'script-name', get_template_directory_uri() . '/js/example.js', array(), '1.0.0', true );
}

add_action('wp_enqueue_scripts', 'sinok_scripts');

require_once( SNK__PLUGIN_DIR . 'messager.php' );

$snk_messager = new Messager();

myStartSession();//add_action('init', 'myStartSession', 1);
function myStartSession() {
    global $snk_messager;
    if(!session_id()) {
        session_start();
        $snk_messager->set('debug', 'session start...');
    } else {
        $snk_messager->set('debug', 'session already started...');
    }
}

require_once( SNK__PLUGIN_DIR . 'auth.php' );
require_once( SNK__PLUGIN_DIR . 'user.php' );
require_once( SNK__PLUGIN_DIR . 'options.php' );
require_once( SNK__PLUGIN_DIR . 'region.php' );
require_once( SNK__PLUGIN_DIR . 'newsletter.php' );
require_once( SNK__PLUGIN_DIR . 'nl_functions.php' );





if (isset($_SESSION['current_newsletter']))
    $snk_messager->set('debug', 'SESSION EXIST: '.$_SESSION['current_newsletter']);
else 
    $snk_messager->set('debug', 'SESSION NOT EXIST');

if (is_admin()) {
    //require_once( SNK__PLUGIN_DIR . 'class.toto-admin.php' );
    //add_action( 'init', array( 'Sinok_Admin', 'init' ) );
}

//add wrapper class around deprecated akismet functions that are referenced elsewhere
//require_once( SNK__PLUGIN_DIR . 'wrapper.php' );

/**
 * Not used!
 * By-pass main query to set current newsletter arg
 * @todo Use current newsletter
 * @param type $query
 */
function force_tax_newsletter($query) {
    global $snk_messager;
    
    if ( is_admin() )
        return;
    
    /*$args = array(
        'orderby' => 'slug',
        'order' => 'ASC',
        'hide_empty' => false,
    );
    $taxos = get_terms('newsletter', $args);

    $terms = array();
    if ($taxos) {
        foreach ($taxos as $taxo) {
            $terms[] = $taxo->slug;
        }
    }*/
    
    $snk_messager->set('debug', 'pre_get_posts tax newsletter: '.get_current_nl());
    $tax_query = array(
                array(
			'taxonomy' => 'newsletter',
			'field'    => 'slug',
			'terms'    => array(get_current_nl()),
		)
            );

    if (is_tax('newsletter')) {
        //$query->set( 'posts_per_page', 1 );
    }
    if ( $query->is_main_query() ) { 
        
        $query->set('tax_query', $tax_query);
        //$args = array_merge( $query->query_vars, array( 'tax_query' => $tax_query ) );
        //query_posts( $args );
    }
    //echo "<pre>";var_dump($query); echo"</pre>";
    //we remove the actions hooked on the '__after_loop' (post navigation)
    //remove_all_actions ( '__after_loop');
    //return;
}

add_action('pre_get_posts', 'force_tax_newsletter', 999);

