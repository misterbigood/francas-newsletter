<?php
/**
 * @package Sinok
 * @subpackage Auth
 */

/*
 * Set auth and rights and groups and...
 * 
 * @todo: hook before insert/update post -> if "redactor" add category "region" pre-set by
 * 
 */

/**
 * Initialize roles and rights
 */
function snk_init_admin() {
    
    $role_admin = get_role('administrator');
    $role_admin->add_cap('manage_regions');
    $role_admin->add_cap('manage_newsletters');
    $role_admin->add_cap('edit_users_region');

    $role = get_role('editor');

    //$role->remove_cap('edit_user');
    //$role->remove_cap('edit_users');
    //$role->remove_cap('create_users');
    //$role->remove_cap('list_users');
    //$role->remove_cap('delete_user');
    //$role->remove_cap('delete_users');

    $role->add_cap('list_users');
    $role->add_cap('create_users');
    $role->add_cap('edit_users');
    $role->add_cap('delete_users');
    $role->add_cap('manage_regions');
    $role->add_cap('manage_newsletters');
    
    $role->add_cap('edit_users_region');
}

add_action('admin_init', 'snk_init_admin');


// the list of roles that one user can assign to other
add_filter('editable_roles', 'snk_filter_roles');

function snk_filter_roles($roles) {
    $user = wp_get_current_user();
    if (in_array('editor', $user->roles)) {
        $tmp = array_keys($roles);
        foreach ($tmp as $r) {
            if (in_array($r, array('author', 'contributor', 'subscriber')))
                continue;
            unset($roles[$r]);
        }
    }
    return $roles;
}

add_filter('map_meta_cap', 'snk_map_meta_cap', 10, 4);

function snk_map_meta_cap($caps, $cap, $user_id, $args) {
    $current_user = new WP_User($user_id);
    if (in_array('administrator', $current_user->roles))
        return $caps;
    switch ($cap) {
        case 'edit_user':
        case 'remove_user':
        case 'promote_user':
            if (isset($args[0]) && $args[0] == $user_id)
                break;
            elseif (!isset($args[0]))
                $caps[] = 'do_not_allow';
            $other = new WP_User(absint($args[0]));
            if ($other->has_cap('administrator') && !current_user_can('administrator'))
                $caps[] = 'do_not_allow';
            if ($other->has_cap('editor') && current_user_can('editor'))
                $caps[] = 'do_not_allow';
            break;
        case 'delete_user':
        case 'delete_users':
            if (!isset($args[0]))
                break;
            $other = new WP_User(absint($args[0]));
            foreach (array('administrator', 'editor') as $role)
                if ($other->has_cap($role))
                    if (!current_user_can($role))
                        $caps[] = 'do_not_allow';
            break;
        default:
            break;
    }
    return $caps;
}

/**
 * Check if user has a role
 * @param string $role Name of role
 * @param object $user Object WP_User or null (default) to use current user
 * @return boolean
 */
function auth_has_role($role, $user = null) {

    if (!$user) {
        $user = wp_get_current_user();
    }

    if ($user instanceof WP_User) {
        foreach ($user->roles as $ur) {
            if ($ur->name == $role)
                return true;
        }
    }

    return false;
}

/**
 * Check if user has region
 * @param string $region The slug of region
 * @param WP_User $user Object WP_User or null (default) to use current user
 * @return boolean
 */
function auth_has_region($region, WP_User $user = null) {

    if (!$user) {
        $user = wp_get_current_user();
    }

    if (!$user instanceof WP_User) {
        return false;
    }

    // $variable = get_field('field_name', $user); ??
    if ($user->has_prop('region') && $user->get('region') == $region) {
        return true;
    }

    return false;
    //return $user->has_cap($cap)
}

function toto_save_post( $post_id, $post, $update ) {

    /*
     * In production code, $slug should be set only once in the plugin,
     * preferably as a class property, rather than in each function that needs it.
     */
    $slug = 'post';

    // If this isn't a 'book' post, don't update it.
    if ( $slug != $post->post_type ) { 
        return;
    }

    // - Update the post's metadata.

    if ( isset( $_POST['region'] ) ) {
        //update_post_meta( $post_id, 'region', sanitize_text_field( $_POST['region'] ) );
        wp_set_object_terms( $post_id, sanitize_text_field( $_POST['region'] ), 'region', $append = false );
    } 

    if ( isset( $_POST['newsletter'] ) ) {
        //update_post_meta( $post_id, 'newsletter', sanitize_text_field( $_REQUEST['newsletter'] ) );
        wp_set_object_terms( $post_id, sanitize_text_field( $_POST['newsletter'] ), 'newsletter', $append = false );
    }

    // Checkboxes are present if checked, absent if not.
    /*if ( isset( $_REQUEST['inprint'] ) ) {
        update_post_meta( $post_id, 'inprint', TRUE );
    } else {
        update_post_meta( $post_id, 'inprint', FALSE );
    }*/
}
add_action('save_post', 'toto_save_post', 10, 3);


/**
 * Get the region of user
 * @param WP_User $user Object WP_User or null (default) to use current user
 * @return string The region (slug)
 */
function get_auth_region(WP_User $user = null) {

    if (!$user) {
        $user = wp_get_current_user();
    }

    if ($user instanceof WP_User) {
        return $user->get('region');
    }

    return '';
}
