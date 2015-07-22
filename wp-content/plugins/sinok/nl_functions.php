<?php
/**
 * @package Sinok
 * @subpackage Newsletter
 */

/*
 * Functions and hook and...
 * for newsletter style interface
 * previous or next post by type/category/newsletternumber...
 */

    
$current_newsletter = 'undefined';/*( isset($_SESSION['current_newsletter']) )  
        ? $_SESSION['current_newsletter']
        : get_option('active_newsletter', 'undefined');//'undefined';*/

if ( isset($_GET['nl']) ) {
    set_current_nl($_GET['nl']);
}
elseif ( !isset($_GET['nl']) && isset($_SESSION['current_newsletter']) ) { 
    set_current_nl($_SESSION['current_newsletter']);
}
elseif ( !isset($_GET['nl']) && !isset($_SESSION['current_newsletter'])) {
    set_current_nl(get_option('active_newsletter', 'undefined'));
}

$current_region = 'undefined';

/**
 * Set the current newsletter slug
 * 
 * @global string $current_nl
 * @param string $value
 * @return string
 */
function set_current_nl($value) {
    global $current_newsletter;

    if (is_array($value)) {
        $current_newsletter = $value[0]->slug;
    }
    if (is_string($value)) {
        $current_newsletter = $value;
    }

    $_SESSION['current_newsletter'] = $current_newsletter;
    
    return $current_newsletter;
}

/**
 * Get the current newsletter slug
 * 
 * @global string $current_nl The current newsletter slug (id)
 * @return string
 */
function get_current_nl() {
    global $current_newsletter, $snk_messager;
    
    if ( is_tax('newsletter') && ($tax = get_queried_object()->slug) ) {
        $snk_messager->set('debug', 'Is tax');
        set_current_nl($tax);
        return $tax;
		//$tax = get_taxonomy( get_queried_object()->taxonomy );
                //var_dump($tax);
		/* translators: 1: Taxonomy singular name, 2: Current taxonomy term */
		//$title = sprintf( esc_html__( '%1$s: %2$s', 'unanim' ), $tax->labels->singular_name, single_term_title( '', false ) );
    } 
    
    return $current_newsletter;
}

/**
 * Get array of post object linked to any newsletter
 * 
 * @param array $options
 * @return array Array of object post
 */
function get_nl_posts(Array $options = array()) {

    $args = array(
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
    }

    /* if ( !array_key_exists('nl', $options) || empty($options['nl']) ) {
      $options['nl'] = get_current_nl();
      } */

    $array = array(
        'post_type' => 'post',
        /* 'nl' => '',
          'limit' => null,
          'offset' => null,
          'categories' => array(),
          'metas' => array(),
          'order' => null , */
        'tax_query' => array(
            array(
                'taxonomy' => 'newsletter',
                'field' => 'slug',
                'terms' => (isset($options['newsletter'])) ? (is_array($options['newsletter'])) ? $options['newsletter'] : (array) $options['newsletter'] : $terms,
            )
        )
    );

    if (isset($options['region'])) {
        $region = array(
            'taxonomy' => 'region',
            'field' => 'slug',
            'terms' => (is_array($options['region'])) ? $options['region'] : (array) $options['region']
        );
        $array['tax_query'][] = $region;
    }

    $options = array_merge($array, $options);

    //echo '<pre>'; var_dump($options); echo '</pre>';

    $the_query = new WP_Query($options);

    //echo '<pre>'; var_dump($the_query); echo '</pre>';
    wp_reset_postdata();
    return $the_query->posts;
}

/**
 * Get the last slug newsletter "nl_YYYY_MM"
 * 
 * @return string
 */
function get_last_newsletter() {
    /*
      $args = array(
      'post_type' => 'post',
      'tax_query' => array(
      'relation' => 'AND',
      array(
      'taxonomy' => 'region',
      'field'    => 'slug',
      'terms'    => array( 'action', 'comedy' ),
      ),
      array(
      'taxonomy' => 'actor',
      'field'    => 'term_id',
      'terms'    => array( 103, 115, 206 ),
      'operator' => 'NOT IN',
      ),
      ),
      ); */

    //$the_query = new WP_Query( $args );

    $args = array(
        'orderby' => 'slug',
        'order' => 'ASC',
        'hide_empty' => false,
    );
    $taxos = get_terms('newsletter', $args);

    //echo '<pre>';var_dump(end($taxos)); echo '</pre>';
    $taxo = ($taxos) ? end($taxos) : false;
    return ( $taxo ) ? $taxo->slug : '';
}

/* function get_dropdown_region($display = false) {
  $args = array(
  'show_option_all'    => '',
  'show_option_none'   => 'none',
  'option_none_value'  => '-1',
  'orderby'            => 'ID',
  'order'              => 'ASC',
  'show_count'         => 0,
  'hide_empty'         => false,
  'child_of'           => 0,
  'exclude'            => '',
  'echo'               => ($display===true)?true:false,
  'selected'           => 0,
  'hierarchical'       => 0,
  'name'               => 'region',
  'id'                 => '',
  'class'              => 'postform',
  'depth'              => 0,
  'tab_index'          => 0,
  'taxonomy'           => 'region',
  'hide_if_empty'      => false,
  'value_field'	     => 'slug',
  );
  wp_dropdown_categories($args);
  }
 */

/** set admin to add options.current_newsletter selection */
/* add_action('admin_menu', 'my_plugin_menu');

  function my_plugin_menu() {
  //add_options_page('My Options', 'My Plugin', 'manage_options', 'my-plugin.php', 'my_plugin_page');
  add_options_page( 'Setting Newsletter', 'Setting Newsletter', 'manage_newsletters', 'menu-settings', $function);
  } */

/* function register_sinok_newsletter_plugin_settings() {
  //register our settings
  register_setting( 'sinok-newsletter-plugin-settings-group', 'new_option_name' );
  register_setting( 'sinok-newsletter-plugin-settings-group', 'some_other_option' );
  register_setting( 'sinok-newsletter-plugin-settings-group', 'option_etc' );
  } */

/**
 * Newsletter options page...
 */
class options_newsletter_page {

    private $_options;
    
    const OPNAME = "active_newsletter";

    function __construct() {
        add_action('admin_menu', array($this, 'admin_menu'));
        //add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    function admin_menu() {
        $hook = add_options_page('Manage Newsletter', 'Settings Newsletter', 'manage_newsletters', 'newsletter-settings', array($this, 'settings_page'));
        
        //add_management_page($page_title, $menu_title, $capability, $menu_slug, $function);
        //$hook = add_management_page('Manage Newsletter', 'Settings Newsletter', 'manage_newsletters', 'newsletter-settings', array($this, 'settings_page'));
        
        //add_action( 'admin_init', 'register_sinok_newsletter_plugin_settings' );
        //add_action('admin_init', array($this, 'register_settings'));
    }

    function page_init() {
        register_setting(
                'sinok-newsletter-plugin-settings-group', // Option group
                'active_newsletter', // Option name
                array($this, 'sanitize') // Sanitize
        );

        add_settings_section(
                'setting_section_newsletter', // ID
                'Newsletter', // Title
                null,//array($this, 'print_section_info'), // Callback
                'newsletter-settings' // Page
        );

        add_settings_field(
                'active_newsletter', // ID
                'Select active newsletter', // Title 
                array($this, 'select_newsletter_callback'), // Callback
                'newsletter-settings', // Page
                'setting_section_newsletter' // Section 
        );
        
    }

    function settings_page() {
        
        /*if ( isset($_POST['create_newsletter']) && is_array($_POST['create_newsletter']) ) {
            echo '<div class="notice notice-success is-dismissible"><p><strong>Info</strong><br />'.$_POST['create_newsletter']['label'].' '.$_POST['create_newsletter']['label'].'</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Ne pas tenir compte de ce message.</span></button></div>';
        }*/
        if ( isset($_POST['submit']) ) {
            
            try {
            
                if ( isset($_POST['newsletter']) ) {
                    if ( get_option( 'active_newsletter' ) !== false ) {
                        update_option('active_newsletter', filter_var($_POST['newsletter'], FILTER_SANITIZE_STRING));
                        //throw new Exception("active_newsletter saved with value: ".filter_var($_POST['newsletter'], FILTER_SANITIZE_STRING));
                    } else {
                        $deprecated = null;
                        $autoload = 'no';
                        add_option( 'active_newsletter', filter_var($_POST['newsletter'], FILTER_SANITIZE_STRING), $deprecated, $autoload );
                    }
                }

                if ( TRUE === FALSE ):
                if ( isset($_POST['create_newsletter']) && is_array($_POST['create_newsletter']) ) {
                    var_dump($_POST['create_newsletter']);
                    $name = $label = '';
                    if ( isset($_POST['create_newsletter']['name']) ) {
                        /*$name = rtrim(filter_var($_POST['create_newsletter']['name'], FILTER_SANITIZE_STRING));
                        if ( !preg_match('/^nl_[0-9][0-9][0-9][0-9]_[0-9][0-9]$/', $name) )
                            throw new ErrorException('Name of newsletter must be formated: "nl_YYYY_MM" (ex: "nl_2015_06").');
                        if ( !$name )
                            throw new ErrorException('Name of newsletter can not be empty.');*/
                        $name = $_POST['create_newsletter']['name'];
                    }
                    if ( isset($_POST['create_newsletter']['label']) ) {
                        /*$label = rtrim(filter_var($_POST['create_newsletter']['label'], FILTER_SANITIZE_STRING));
                        if ( !$label )
                            throw new ErrorException('Label of newsletter can not be empty.');*/
                        $label = $_POST['create_newsletter']['label'];
                    }
                    
                    throw new ErrorException("create_newsletter posted and is array and values are: ".$name.' : '.$label);
                }
                endif;
                
                echo '<div class="notice notice-success is-dismissible"><p><strong>Update</strong><br />';
                echo 'success!';
                echo '</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Ne pas tenir compte de ce message.</span></button></div>';
                
            } catch (ErrorException $ex) {
                echo '<div class="notice notice-error is-dismissible">';
                echo '<p><strong>Warning</strong><br />';
                echo ''.$ex->getMessage();
                echo '</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Ne pas tenir compte de ce message.</span></button></div>';
            }
        }

        $this->options = get_option('active_newsletter');

        echo '<div class="wrap">';
        echo '<h1>Settings Newsletter W.I.P</h1>';
        echo '<div class="notice notice-success is-dismissible"><p><strong>Info</strong><br />Set list of newsletters from menu [Articles][Newsletters]</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Ne pas tenir compte de ce message.</span></button></div>';
        //echo '<form method="post" action="options">';
        
        //settings_fields( 'sinok-newsletter-plugin-settings-group' );   
        //do_settings_sections( 'newsletter-settings' );
        
        echo '<form method="post" action="">';
        
        echo '<h3>Enable Newsletter</h3>';
        echo '<table class="form-table">';
        echo '<tr><th scope="row">Select</th><td>';
            echo $this->select_newsletter_callback();
        echo '</td><tr>';
        echo '</table>';
        
        if ( TRUE === FALSE ):
        echo '<h3>Create Newsletter</h3>';
        echo '<table class="form-table">';
        echo '<tr><th scope="row">Name</th><td><input type="text" name="create_newsletter[name]" class="postform"/><td><tr>';
        echo '<tr><th scope="row">Label</th><td><input type="text" name="create_newsletter[label]" class="postform" /><td><tr>';
        echo '</table>';
        endif;
        
        submit_button(); 
        
        echo '</form>';
        echo '</div><!-- end wrap -->';
    }
    
    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    { 
        $new_input = array(); //var_dump($_POST); exit();
        
        if ( isset( $_POST['newsletter']) ) 
            $new_input = sanitize_text_field( $_POST['newsletter'] );
        
        /*if( isset( $input['id_number'] ) )
            $new_input['id_number'] = absint( $input['id_number'] );

        if( isset( $input['title'] ) )
            $new_input['title'] = sanitize_text_field( $input['title'] );
        */
        return $new_input;
    }
    
    public function select_newsletter_callback() {  
        
        if ( !$this->options ) { 
            $this->options = get_last_newsletter();
        } 
        
        $term = get_term_by("slug", $this->options, 'newsletter'); ///var_dump($term);
        return select_newsletter_function(array('show_option_none' => 'undefined', 'selected' => $term->term_id, 'value_field' => 'slug'));
        //return select_newsletter_function(array('show_option_none' => 'undefined', 'value_field' => 'slug', 'selected' => $this->options));
        /*printf(
            '<input type="text" id="id_number" name="my_option_name[id_number]" value="%s" />',
            isset( $this->options['id_number'] ) ? esc_attr( $this->options['id_number']) : ''
        );*/
    }

}

if ( is_admin() )
    $sinok_newsletter_settings_page = new options_newsletter_page();

/**
 * Get the previous newsletter slug
 * 
 * @param string $newsletter The referer slug newsletter
 * @return string the slug of previous newsletter
 */
function get_previous_nl($newsletter) {

    $newsletter = (string) trim($newsletter);/*( trim($newsletter) && is_string(trim($newsletter)) )
            ? strtolower(trim($newsletter))
            : get_current_nl();*/
    
    $args = array(
        'orderby' => 'slug',
        'order' => 'ASC',
        'hide_empty' => false,
        //'exclude' => array(get_term_by('slug', get_option('active_newsletter'), 'newsletter')->term_id), 
    );
    $taxos = get_terms('newsletter', $args);
    
    $i = 0;
    $i_found = -1;
    
    foreach($taxos as $k => $taxo) {
        
        if ( $taxo->slug == $newsletter )
            $i_found = $i;
        
        $i++;
    }
    
    if ( $i_found === -1 )
        return '';
    
    return ( isset($taxos[$i_found-1]) && $taxos[$i_found-1]->slug!='undefined')
        ? $taxos[$i_found-1]->slug 
        : '';
}

/**
 * Get the next newsletter slug
 * 
 * @param string $newsletter The referer slug newsletter
 * @return string The slug of next newsletter
 */
function get_next_nl($newsletter) {

    $newsletter = (string) trim($newsletter); /*( trim($newsletter) && is_string(trim($newsletter)) )
            ? strtolower(trim($newsletter))
            : get_current_nl();*/
    
    $args = array(
        'orderby' => 'slug',
        'order' => 'ASC',
        'hide_empty' => false,
        //'exclude' => array(get_term_by('slug', get_option('active_newsletter'), 'newsletter')->term_id)//array(get_option('active_newsletter')), 
    );
    $taxos = get_terms('newsletter', $args);
    
    $i = 0;
    $i_found = -1;
    
    foreach($taxos as $k => $taxo) {
        
        if ( $taxo->slug == $newsletter )
            $i_found = $i;
        
        $i++;
    }
    
    if ( $i_found === -1 ) 
        return '';
    
    return ( isset($taxos[$i_found+1]) && $taxos[$i_found+1]->slug!='undefined')
        ? $taxos[$i_found+1]->slug 
        : '';
}

/**
 * Generate nav link newsletter navigation & set current newsletter
 * 
 * @param string $newsletter The referer slug
 * @return string
 */
function nav_previous_nl($newsletter) {
    
    $target = get_previous_nl($newsletter);
    
    return ( $target )
        ? '<a href="/newsletter/'.$target.'" title="Newsletter précédente">'.  get_newsletter_name($target).'</a>'
        : '';
}

/**
 * Generate nav link newsletter navigation & set current newsletter
 * 
 * @param string $newsletter The referer slug
 * @return string
 */
function nav_next_nl($newsletter) {

    $target = get_next_nl($newsletter);
    
    return ($target) 
        ? '<a href="/newsletter/'.$target.'" title="Newsletter suivante">'.  get_newsletter_name($target).'</a>'
        : '';
}

