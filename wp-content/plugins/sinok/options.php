<?php
/**
 * @package Sinok
 * @subpackage Option
 */

/* 
 * The plugin Options.
 * 
 * Admin page etc...
 */

/* function register_sinok_newsletter_plugin_settings() {
  //register our settings
  register_setting( 'sinok-newsletter-plugin-settings-group', 'new_option_name' );
  register_setting( 'sinok-newsletter-plugin-settings-group', 'some_other_option' );
  register_setting( 'sinok-newsletter-plugin-settings-group', 'option_etc' );
  } */


/**
 * Not used!
 */
class options_plugin_page {

    private $_options;
    
    const OPNAME = "active_newsletter";

    function __construct() {
        add_action('admin_menu', array($this, 'admin_menu'));
        add_action('admin_init', array( $this, 'page_init' ));
    }

    function admin_menu() {
        add_options_page('Manage Sinok plugin', 'Settings Sinok plugin', 'activate_plugins', 'sinok-plugin-settings', array($this, 'settings_page'));
        //add_action( 'admin_init', 'register_sinok_newsletter_plugin_settings' );
        //add_action('admin_init', array($this, 'register_settings'));
    }

    function page_init() {
        register_setting(
                'sinok-plugin-settings-group', // Option group
                'sinok_options', // Option name
                array($this, 'sanitize') // Sanitize
        );

        add_settings_section(
                'setting-section-one', // ID
                'One', // Title
                null,//array($this, 'print_section_info'), // Callback
                'sinok-plugin-settings' // Page
        );

        add_settings_field(
                'id_number', // ID
                'Set id', // Title 
                array($this, 'the_callback'), // Callback
                'sinok-plugin-settings', // Page
                'setting-section-one' // Section

        );
        add_settings_field(
                'title', // ID
                'Title', // Title 
                array($this, 'the_second_callback'), // Callback
                'sinok-plugin-settings', // Page
                'setting-section-one' // Section 
        );
        add_settings_field(
                'active_newsletter', // ID
                'Select active newsletter', // Title 
                array($this, 'newsletter_callback'), // Callback
                'sinok-plugin-settings', // Page
                'setting-section-one' // Section 
        );
    }

    function settings_page() {
        
        //if ( isset() )

        $this->options = get_option('sinok_options');

        echo '<div class="wrap">';
        echo '<h1>Settings Sinok plugin</h1>';
        echo '<div class="notice notice-success is-dismissible"><p><strong>Info</strong><br />W.I.P.</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Ne pas tenir compte de ce message.</span></button></div>';
        echo '<form method="post" action="options">';
        
        settings_fields( 'sinok-plugin-settings-group' );   
        do_settings_sections( 'sinok-plugin-settings' );
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
        /*if ( isset($_POST['sinok_options']) ) {
            return $_POST['sinok_options'];
        }*/
        
        $new_input = array(); //var_dump($_POST); exit();
        
        if( isset( $input['id_number'] ) )
            $new_input['id_number'] = absint( $input['id_number'] );

        if( isset( $input['title'] ) )
            $new_input['title'] = sanitize_text_field( $input['title'] );
        
        if ( isset( $input['active_newsletter']) ) 
            $new_input['active_newsletter'] = sanitize_text_field( $input['active_newsletter'] );
        
        return $new_input;
    }
    
    public function the_callback() {
        printf(
            '<input type="text" id="id_number" name="sinok_options[id_number]" value="%s" />',
            isset( $this->options['id_number'] ) ? esc_attr( $this->options['id_number']) : ''
        );
    }
    
    public function the_second_callback() {
        printf(
            '<input type="text" id="title" name="sinok_options[title]" value="%s" />',
            isset( $this->options['title'] ) ? esc_attr( $this->options['title']) : ''
        );
    }
    
    public function newsletter_callback() {  
        /*if ( !isset($this->options['active_newsletter']) ) { 
            $this->options['active_newsletter'] = get_last_newsletter();
        } */
        echo '<select name="sinok_options[active_newsletter]">';
            echo '<option value="undefined">undefined</option>';
        foreach(get_terms('newsletter', array()) as $k => $d) {
            $select = ($this->options['active_newsletter'] == $d->slug) ? 'selected' : '';
            echo '<option value="'.$d->slug.'" '.$select.'>'.$d->name.'</option>';
        }
        echo '</select>';
        //echo select_newsletter_function(array('show_option_none' => 'undefined', 'select' => $this->options));
        
    }

}

/*if ( is_admin() )
    $sinok_plugin_settings_page = new options_plugin_page();*/

