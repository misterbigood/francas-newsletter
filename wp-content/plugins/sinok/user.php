<?php
/**
 * @package Sinok
 * @subpackage Auth
 */

/**
 * Functions for user 
 */

add_action('show_user_profile', 'extra_user_profile_fields');
add_action('edit_user_profile', 'extra_user_profile_fields');

function extra_user_profile_fields($user) {
   
    echo '<h3>'._e("Extra profile information", "blank").'</h3>';
    
    if ( !current_user_can('edit_users_region') ) {
        echo get_region_name(esc_attr($user->get('region')));
        return;
    }
    
    /*$preselected = '';
    $user = wp_get_current_user();
    if ($user instanceof WP_User) {
        $preselected = esc_attr($user->get('region')); //esc_attr(get_the_author_meta('region', $user->ID))
    }*/
    $preselected = esc_attr($user->get('region'));

    echo '<table>';
    echo '<tr>';
    echo '<td><label for="user_region">Région : </label></td>'; // _e("Postal Code");
    echo '<td><select id="user_region" name="user_region" required>';
    echo '<option value="0">&nbsp;</option>';
    foreach (get_regions_config() as $code => $region) {
        $sel = ( $preselected == $code ) ? 'selected' : '';
        echo '<option value="' . $code . '" ' . $sel . '>' . $region['name'] . '</option>';
    }
    echo '</select></td>';
    echo '</tr>';
    echo '</table>';
}

add_action('personal_options_update', 'save_extra_user_profile_fields');
add_action('edit_user_profile_update', 'save_extra_user_profile_fields');

function save_extra_user_profile_fields($user_id) {

    if ( !current_user_can('edit_users_region') ) {
        return false;
    }

    update_user_meta($user_id, 'region', $_POST['user_region']);
}
