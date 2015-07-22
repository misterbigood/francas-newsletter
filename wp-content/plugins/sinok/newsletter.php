<?php
/**
 * @package Sinok
 * @subpackage Newsletter
 */

/**
 * Get array( slug => name,[...]) of newsletters
 * 
 * @return array
 */
function get_newsletters_config() {
    /* return array(
      '75' => 'Ile de France',
      '50' => 'Basse-Normandie',
      '33' => 'truc'
      ); */
    $taxonomies = array(
        'newsletter',
    );

    $args = array(
        'orderby' => 'slug',
        'order' => 'ASC',
        'hide_empty' => false,
        'exclude' => array(),
        'exclude_tree' => array(),
        'include' => array(),
        'number' => '',
        'fields' => 'all',
        'slug' => '',
        'parent' => '',
        'hierarchical' => true,
        'child_of' => 0,
        'childless' => false,
        'get' => '',
        'name__like' => '',
        'description__like' => '',
        'pad_counts' => false,
        'offset' => '',
        'search' => '',
        'cache_domain' => 'core'
    );

    $terms = get_terms($taxonomies, $args);
    $newsletters = array(); //var_dump($terms);
    foreach ($terms as $newsletter) {
        $newsletters[$newsletter->slug] = $newsletter->name;
    }
    return $newsletters;
}

/**
 * Get the display name of newsletter code (slug)
 * 
 * @param string $code Slug of newsletter
 * @return string The display name of newsletter
 */
function get_newsletter_name($code = null) {
    $newsletters = get_newsletters_config();

    return ( $code && is_string($code) && isset($newsletters[$code])) ? $newsletters[$code] : '';
}

/**
 * Register Custom Taxonomy
 */
function newsletter_taxonomy() {

    $labels = array(
        'name' => _x('Newsletters', 'Taxonomy General Name', 'text_domain'),
        'singular_name' => _x('Newsletter', 'Taxonomy Singular Name', 'text_domain'),
        'menu_name' => __('Newsletters', 'text_domain'),
        'all_items' => __('Toutes les newsletters', 'text_domain'),
        'parent_item' => __('Parent Item', 'text_domain'),
        'parent_item_colon' => __('Parent Item:', 'text_domain'),
        'new_item_name' => __('Nouvelle newsletter', 'text_domain'),
        'add_new_item' => __('Ajouter une newsletter', 'text_domain'),
        'edit_item' => __('Editer la newsletter', 'text_domain'),
        'update_item' => __('Mettre à jour la newsletter', 'text_domain'),
        'view_item' => __('Voir la newsletter', 'text_domain'),
        'separate_items_with_commas' => __('Separate items with commas', 'text_domain'),
        'add_or_remove_items' => __('Add or remove items', 'text_domain'),
        'choose_from_most_used' => __('Choose from the most used', 'text_domain'),
        'popular_items' => __('Popular Items', 'text_domain'),
        'search_items' => __('Search Items', 'text_domain'),
        'not_found' => __('Not Found', 'text_domain'),
    );
    $capabilities = array(
        'manage_terms' => 'manage_newsletters',
        'edit_terms' => 'manage_newsletters',
        'delete_terms' => 'manage_newsletters',
        'assign_terms' => 'edit_posts',
    );
    $args = array(
        'labels' => $labels,
        'hierarchical' => false,
        'public' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'show_in_nav_menus' => true,
        'show_tagcloud' => false,
        'capabilities' => $capabilities,
    );
    register_taxonomy('newsletter', array('post', 'article'), $args);
}

// Hook into the 'init' action
add_action('init', 'newsletter_taxonomy', 0);

/**
 * Get a Newsletter dropdown input select (for form...)
 * 'select' must be string of newsletter_slug (ex: "nl_2015_06");
 * 
 * @param array $options
 * @return string
 */
function select_newsletter_function($options = array()) {

    $current_post = get_the_ID();

    /*$select = 0;
    if ( is_array($options) && !isset($options['select']) && $current_post) {
        $newsletters = get_the_newsletter($current_post);
        if ($newsletters && is_array($newsletters)) {
            //echo'<pre>';var_dump($newsletters);echo'</pre>';
            $select = (int) $newsletters[0]->term_id;
        }
    } else {
        //var_dump($options['select']);
        if ( is_array($options) && isset($options['select'])) {
            foreach (get_terms(array('newsletter'), array()) as $k => $d) {
                if ($d->slug == $options['select']) {
                    $select = $d->term_id;
                    break;
                }
            }
        }
    }*/
    
    $select = false;
    if ( $current_post ) { 
        $newsletters = get_the_newsletter($current_post);
        if ( $newsletters && is_array($newsletters) ) {
            //echo'<pre>';var_dump($regions);echo'</pre>';
            $select = (int) $newsletters[0]->term_id;
        }
    } 
    //var_dump($select);
    $args = array(
        'show_option_all' => '',
        'show_option_none' => 'none',
        'option_none_value' => 'undefined',
        'orderby' => 'slug',
        'order' => 'DESC',
        'show_count' => 0,
        'hide_empty' => false,
        'child_of' => 0,
        'exclude' => '',
        'echo' => false, // 0 not display, 1 display (echo)
        'selected' => $select,
        'hierarchical' => 0,
        'name' => 'newsletter',
        'id' => '',
        'class' => 'postform',
        'depth' => 0,
        //'tab_index' => 0,
        'taxonomy' => 'newsletter',
        'hide_if_empty' => false,
        'value_field' => 'slug', //'term_id',
    );

    if (is_array($options))
        $args = array_merge($args, $options); 
    
    //var_dump($args);

    echo wp_dropdown_categories($args);
    return;
}

function set_newsletter_meta() {
    // remove auto created (see region_taxonomy())
    remove_meta_box('tagsdiv-newsletter', 'post', 'side');

    // create new metabox
    add_meta_box('tagsdiv-newsletter2', 'Newsletter', 'select_newsletter_function', 'post', 'side', 'core');
}

add_action('admin_menu', 'set_newsletter_meta');

/**
 * Not used!
 * Add new templates
 * @param string $page_template
 * @return string
 */
function override_template_newsletter($template) {

    if (is_tax('newsletter')) {
        $new_template = dirname(__FILE__) . '/templates/newsletter-page-template.php';
        //$new_template = locate_template( array( dirname( __FILE__ ).'/templates/region-page-template.php' ) );
        if ('' != $new_template) {
            return $new_template;
        }
    }

    return $template;
}

//add_filter('template_include', 'override_template_newsletter');

/**
 * Retrieve newsletter link URL.
 * @see get_term_link()
 *
 * @param int|object $newsletter Newsletter ID or object.
 * @return string Link on success, empty string if newsletter does not exist.
 */
function get_newsletter_link($newsletter) {
    if (!is_object($newsletter))
        $newsletter = (int) $newsletter;

    $newsletter = get_term_link($newsletter, 'newsletter');

    if (is_wp_error($newsletter))
        return '';

    return $newsletter;
}

/**
 * Retrieve newsletter parents with separator.
 *
 * @param int $id Newsletters ID.
 * @param bool $link Optional, default is false. Whether to format with link.
 * @param string $separator Optional, default is '/'. How to separate newsletters.
 * @param bool $nicename Optional, default is false. Whether to use nice name for display.
 * @param array $visited Optional. Already linked to newsletters to prevent duplicates.
 * @return string|WP_Error A list of newsletter parents on success, WP_Error on failure.
 */
function get_newsletter_parents($id, $link = false, $separator = '/', $nicename = false, $visited = array()) {
    $chain = '';
    $parent = get_term($id, 'newsletter');
    if (is_wp_error($parent))
        return $parent;

    if ($nicename)
        $name = $parent->slug;
    else
        $name = $parent->name;

    if ($parent->parent && ( $parent->parent != $parent->term_id ) && !in_array($parent->parent, $visited)) {
        $visited[] = $parent->parent;
        $chain .= get_region_parents($parent->parent, $link, $separator, $nicename, $visited);
    }

    if ($link)
        $chain .= '<a href="' . esc_url(get_region_link($parent->term_id)) . '">' . $name . '</a>' . $separator;
    else
        $chain .= $name . $separator;
    return $chain;
}

/**
 * Retrieve post newsletters.
 *
 * @param int $id Optional, default to current post ID. The post ID.
 * @return array of Object
 */
function get_the_newsletter($id = false) {
    $newsletters = get_the_terms($id, 'newsletter');
    if (!$newsletters || is_wp_error($newsletters))
        $newsletters = array();

    $newsletters = array_values($newsletters);

    foreach (array_keys($newsletters) as $key) {
        _make_cat_compat($newsletters[$key]);
    }

    /**
     * Filter the array of newsletters to return for a post.
     *
     * @param array $newsletters An array of newsletters to return for the post.
     */
    return apply_filters('get_the_newsletters', $newsletters);
}

/**
 * Retrieve newsletter list in either HTML list or custom format.
 *
 * @param string $separator Optional, default is empty string. Separator for between the newsletters.
 * @param string $parents Optional. How to display the parents.
 * @param int $post_id Optional. Post ID to retrieve newsletters.
 * @return string
 */
function get_the_newsletter_list($separator = '', $parents = '', $post_id = false) {
    global $wp_rewrite;
    if (!is_object_in_taxonomy(get_post_type($post_id), 'newsletter')) {
        /** This filter is documented in wp-includes/category-template.php */
        return apply_filters('the_newsletter', '', $separator, $parents);
    }

    $newsletters = get_the_newsletter($post_id);
    if (empty($newsletters)) {
        /** This filter is documented in wp-includes/category-template.php */
        return apply_filters('the_newsletter', __('Uncategorized'), $separator, $parents);
    }

    $rel = ( is_object($wp_rewrite) && $wp_rewrite->using_permalinks() ) ? 'rel="newsletter tag"' : 'rel="newsletter"';

    $thelist = '';
    if ('' == $separator) {
        $thelist .= '<ul class="post-newsletters">';
        foreach ($newsletters as $newsletter) {
            $thelist .= "\n\t<li>";
            switch (strtolower($parents)) {
                case 'multiple':
                    if ($newsletter->parent)
                        $thelist .= get_newsletter_parents($newsletter->parent, true, $separator);
                    $thelist .= '<a href="' . esc_url(get_newsletter_link($newsletter->term_id)) . '" ' . $rel . '>' . $newsletter->name . '</a></li>';
                    break;
                case 'single':
                    $thelist .= '<a href="' . esc_url(get_newsletter_link($newsletter->term_id)) . '"  ' . $rel . '>';
                    if ($newsletter->parent)
                        $thelist .= get_newsletter_parents($newsletter->parent, false, $separator);
                    $thelist .= $newsletter->name . '</a></li>';
                    break;
                case '':
                default:
                    $thelist .= '<a href="' . esc_url(get_newsletter_link($newsletter->term_id)) . '" ' . $rel . '>' . $newsletter->name . '</a></li>';
            }
        }
        $thelist .= '</ul>';
    } else {
        $i = 0;
        foreach ($newsletters as $newsletter) {
            if (0 < $i)
                $thelist .= $separator;
            switch (strtolower($parents)) {
                case 'multiple':
                    if ($newsletter->parent)
                        $thelist .= get_newsletter_parents($newsletter->parent, true, $separator);
                    $thelist .= '<a href="' . esc_url(get_newsletter_link($newsletter->term_id)) . '" ' . $rel . '>' . $newsletter->name . '</a>';
                    break;
                case 'single':
                    $thelist .= '<a href="' . esc_url(get_newsletter_link($newsletter->term_id)) . '" ' . $rel . '>';
                    if ($newsletter->parent)
                        $thelist .= get_newsletter_parents($newsletter->parent, false, $separator);
                    $thelist .= "$newsletter->name</a>";
                    break;
                case '':
                default:
                    $thelist .= '<a href="' . esc_url(get_newsletter_link($newsletter->term_id)) . '" ' . $rel . '>' . $newsletter->name . '</a>';
            }
            ++$i;
        }
    }

    /**
     * Filter the newsletter or list of newsletters.
     *
     * @param array  $thelist   List of newsletters for the current post.
     * @param string $separator Separator used between the newsletters.
     * @param string $parents   How to display the newsletter parents. Accepts 'multiple',
     *                          'single', or empty.
     */
    return apply_filters('the_newsletter', $thelist, $separator, $parents);
}

/**
 * Check if the current post in within any of the given newsletters.
 *
 * The given newsletters are checked against the post's newsletters' term_ids, names and slugs.
 * Newsletters given as integers will only be checked against the post's newsletters' term_ids.
 *
 * @param int|string|array $newsletter Newsletter ID, name or slug, or array of said.
 * @param int|object $post Optional. Post to check instead of the current post.
 * @return bool True if the current post is in any of the given newsletters.
 */
function in_newsletter($newsletter, $post = null) {
    if (empty($newsletter))
        return false;

    return has_newsletter($newsletter, $post);
}

/**
 * Check if the current post has any of given newsletter.
 *
 * @param string|int|array $newsletter Optional. The newsletter name/term_id/slug or array of them to check for.
 * @param int|object $post Optional. Post to check instead of the current post.
 * @return bool True if the current post has any of the given newsletters (or any newsletter, if no newsletter specified).
 */
function has_newsletter($newsletter = '', $post = null) {
    return has_term($newsletter, 'newsletter', $post);
}

/**
 * Display the newsletter list for the post.
 *
 * @param string $separator Optional, default is empty string. Separator for between the newsletters.
 * @param string $parents Optional. How to display the parents.
 * @param int $post_id Optional. Post ID to retrieve newsletters.
 */
function the_newsletter($separator = '', $parents = '', $post_id = false) {
    echo get_the_newsletter_list($separator, $parents, $post_id);
}

/**
 * Retrieve newsletter description.
 *
 * @param int $newsletter Optional. Newsletter ID. Will use global newsletter ID by default.
 * @return string Newsletter description, available.
 */
function newsletter_description($newsletter = 0) {
    return term_description($newsletter, 'newsletter');
}
