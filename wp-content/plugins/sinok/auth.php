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

    //$role->remove_cap('delete_others_pages');
}

add_action('admin_init', 'snk_init_admin');


// the list of roles that one user can assign to other
add_filter('editable_roles', 'snk_filter_roles');

function snk_filter_roles($roles) {
    $user = wp_get_current_user();
    if (in_array('editor', $user->roles)) {
        $tmp = array_keys($roles);
        foreach ($tmp as $r) {
            if (in_array($r, array('editor', 'author'/*, 'contributor', 'subscriber'*/)))
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
            if ($ur == $role)
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

function toto_save_post($post_id, $post, $update) {

    /*
     * In production code, $slug should be set only once in the plugin,
     * preferably as a class property, rather than in each function that needs it.
     */
    $slug = 'post';

    // If this isn't a 'book' post, don't update it.
    if ($slug != $post->post_type) {
        return;
    }

    // - Update the post's metadata.

    if (isset($_POST['region'])) {
        //update_post_meta( $post_id, 'region', sanitize_text_field( $_POST['region'] ) );
        wp_set_object_terms($post_id, sanitize_text_field($_POST['region']), 'region', $append = false);
    }

    if (isset($_POST['newsletter'])) {
        //update_post_meta( $post_id, 'newsletter', sanitize_text_field( $_REQUEST['newsletter'] ) );
        wp_set_object_terms($post_id, sanitize_text_field($_POST['newsletter']), 'newsletter', $append = false);
    }

    // Checkboxes are present if checked, absent if not.
    /* if ( isset( $_REQUEST['inprint'] ) ) {
      update_post_meta( $post_id, 'inprint', TRUE );
      } else {
      update_post_meta( $post_id, 'inprint', FALSE );
      } */
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

/**
 * Prevent pages edit/delete
 */
function restrict_post_deletion($post_ID) {
    //$user = get_current_user_id();

    $user = wp_get_current_user();

    // do nothing
    if (!$user) {
        return 0;
    }

    // do nothing
    if (in_array('administrator', (array) $user->roles)) {
        return 0;
    }

    $post = get_post($post_ID);

    if (!$post) {
        throw new Exception(__FUNCTION__ . '(): Object Post not found.');
        return 0;
    }

    // do nothing
    if ($post->post_type != 'page') {
        return 0;
    }

    $restricted_pages = array('contact', 'credits', 'mentions-legales', 'newsletter', 'region');
    if (in_array($post->post_name, $restricted_pages)) {
        do_action('admin_page_access_denied');
        wp_die(__('You cannot delete this entry.'));
        exit;
    }
}

add_action('wp_trash_post', 'restrict_post_deletion', 10, 1);
add_action('before_delete_post', 'restrict_post_deletion', 10, 1);

function restrict_post_edit($post_ID) {

    $user = wp_get_current_user();

    // do nothing
    if (!$user) {
        return 0;
    }

    // do nothing
    if (in_array('administrator', (array) $user->roles)) {
        return 0;
    }

    $post = get_post($post_ID);

    if (!$post) {
        throw new Exception(__FUNCTION__ . '(): Object Post not found.');
        return 0;
    }

    // do nothing
    if ($post->post_type != 'page') {
        return 0;
    }

    $restricted_pages = array('newsletter', 'region');
    if (in_array($post->post_name, $restricted_pages)) {
        do_action('admin_page_access_denied');
        wp_die(__('You cannot modify this entry.'));
        exit;
    }
}

add_action('save_post', 'restrict_post_edit', 10, 1);

/*
function fuck() {
    
    $result = array();
    
    $args = array(
        'name' => array('newsletter', 'region'),
        'post_type' => 'page',
            //'post_status' => 'publish',
            //'numberposts' => 1
    );
    
    try {
        $query = new WP_Query($args);

        if ($query->posts) {
            foreach ($query->posts as $post) {
                array_push($result, $post->ID);
            }
        } 
        
    } catch (Exception $ex) {
        do_action('admin_page_access_denied');
        wp_die($ex->getMessage());
        exit;
    }

    wp_reset_postdata();
    
    return $result;
}

function exclude_pages_from_admin($query) {
    global $pagenow, $post_type;

    $user = wp_get_current_user();

    // do nothing
    if (!$user) {
        return;
    }

    // do nothing
    if (in_array('administrator', (array) $user->roles)) {
        return;
    }

    if (is_admin() && $pagenow == 'edit.php' && $post_type == 'page') { 
        //$query->query_vars['post__not_in'] = fuck();
        $query->set( 'post__not_in', fuck() );
    }

}
//add_action('parse_query', 'exclude_pages_from_admin');
add_action( 'pre_get_posts' ,'exclude_pages_from_admin' );
*/