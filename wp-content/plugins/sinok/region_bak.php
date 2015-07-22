<?php
/**
 * @package Sinok
 * @subpackage Region
 */

/**
 * Get array( slug => name,[...]) of newsletters
 * 
 * @return array
 */
function get_regions_config() {
    /* return array(
      '75' => 'Ile de France',
      '50' => 'Basse-Normandie',
      '33' => 'truc'
      ); */
    $taxonomies = array(
        'region',
    );

    $args = array(
        'orderby' => 'name',
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
    $regions = array(); //var_dump($terms);
    foreach($terms as $region) {
        $regions[$region->slug] = $region->name;
    }
    return $regions;
}

/**
 * Get the display name of region code (slug)
 * 
 * @param string $code Slug of region
 * @return string The display name of region
 */
function get_region_name($code = null) {

    $regions = get_regions_config();

    return ( $code && is_numeric($code) && isset($regions[$code])) ? $regions[$code] : '';
}

// Register Custom Taxonomy
function region_taxonomy() {

    $labels = array(
        'name' => _x('Régions', 'Taxonomy General Name', 'text_domain'),
        'singular_name' => _x('Région', 'Taxonomy Singular Name', 'text_domain'),
        'menu_name' => __('Régions', 'text_domain'),
        'all_items' => __('Toutes les régions', 'text_domain'),
        'parent_item' => __('Parent Item', 'text_domain'),
        'parent_item_colon' => __('Parent Item:', 'text_domain'),
        'new_item_name' => __('Nouvelle région', 'text_domain'),
        'add_new_item' => __('Ajouter une région', 'text_domain'),
        'edit_item' => __('Editer la région', 'text_domain'),
        'update_item' => __('Mettre à jour la région', 'text_domain'),
        'view_item' => __('Voir la région', 'text_domain'),
        'separate_items_with_commas' => __('Separate items with commas', 'text_domain'),
        'add_or_remove_items' => __('Add or remove items', 'text_domain'),
        'choose_from_most_used' => __('Choose from the most used', 'text_domain'),
        'popular_items' => __('Popular Items', 'text_domain'),
        'search_items' => __('Search Items', 'text_domain'),
        'not_found' => __('Not Found', 'text_domain'),
    );
    $capabilities = array(
        'manage_terms' => 'manage_regions',
        'edit_terms' => 'manage_regions',
        'delete_terms' => 'manage_regions',
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
    register_taxonomy('region', array('post', 'article'), $args);
}

// Hook into the 'init' action
add_action('init', 'region_taxonomy', 0);

function toto_region_function() {
    //get_dropdown_region(true);
    
    $current_post = get_the_ID();
    
    $select = 0;
    if ( $current_post ) { 
        $regions = get_the_region($current_post);
        if ( $regions && is_array($regions) ) {
            //echo'<pre>';var_dump($regions);echo'</pre>';
            $select = (int) $regions[0]->term_id;
        }
    } 
    
    if (current_user_can('manage_regions')) {
        $args = array(
            'show_option_all' => '',
            'show_option_none' => 'none',
            'option_none_value' => 'region_undefined',
            'orderby' => 'slug',
            'order' => 'ASC',
            'show_count' => 0,
            'hide_empty' => false,
            'child_of' => 0,
            'exclude' => '',
            'echo' => false, // 0 not display, 1 display (echo)
            'selected' => $select,
            'hierarchical' => 0,
            'name' => 'region',
            'id' => '',
            'class' => 'postform',
            'depth' => 0,
            //'tab_index' => 0,
            'taxonomy' => 'region',
            'hide_if_empty' => false,
            'value_field' => 'slug', //'term_id',	
        );
        echo wp_dropdown_categories($args);
        return;
    }

    $user = wp_get_current_user();
    if ($user instanceof WP_User) {
        echo get_region_name($user->get('region'));
        echo '<input type="hidden" name="region" value="'.$user->get('region').'" />';
    }
}

function set_region_meta() {
    // remove auto created (see region_taxonomy())
    remove_meta_box('tagsdiv-region', 'post', 'side');

    // create new metabox
    add_meta_box('tagsdiv-region2', 'Région', 'toto_region_function', 'post', 'side', 'core');
}

add_action('admin_menu', 'set_region_meta');

/**
 * Not used!
 * Add new templates
 * @param string $page_template
 * @return string
 */
function override_template_region( $template )
{
    
    if ( is_tax( 'region' ) ) { 
        $new_template = dirname( __FILE__ ) . '/templates/region-page-template.php';
        //$new_template = locate_template( array( dirname( __FILE__ ).'/templates/region-page-template.php' ) );
        if ( '' != $new_template ) {
                return $new_template ;
        }
    }
    
    return $template;
}
//add_filter( 'page_template', 'override_template_region' );
//add_filter('template_include', 'override_template_region');

/**
 * Retrieve region link URL.
 * 
 * @see get_term_link()
 *
 * @param int|object $region Region ID or object.
 * @return string Link on success, empty string if region does not exist.
 */
function get_region_link( $region ) {
	if ( ! is_object( $region ) )
		$region = (int) $region;

	$region = get_term_link( $region, 'region' );

	if ( is_wp_error( $region ) )
		return '';

	return $region;
}

/**
 * Retrieve region parents with separator.
 *
 * @param int $id Region ID.
 * @param bool $link Optional, default is false. Whether to format with link.
 * @param string $separator Optional, default is '/'. How to separate regions.
 * @param bool $nicename Optional, default is false. Whether to use nice name for display.
 * @param array $visited Optional. Already linked to regions to prevent duplicates.
 * @return string|WP_Error A list of region parents on success, WP_Error on failure.
 */
function get_region_parents( $id, $link = false, $separator = '/', $nicename = false, $visited = array() ) {
	$chain = '';
	$parent = get_term( $id, 'region' );
	if ( is_wp_error( $parent ) )
		return $parent;

	if ( $nicename )
		$name = $parent->slug;
	else
		$name = $parent->name;

	if ( $parent->parent && ( $parent->parent != $parent->term_id ) && !in_array( $parent->parent, $visited ) ) {
		$visited[] = $parent->parent;
		$chain .= get_region_parents( $parent->parent, $link, $separator, $nicename, $visited );
	}

	if ( $link )
		$chain .= '<a href="' . esc_url( get_region_link( $parent->term_id ) ) . '">'.$name.'</a>' . $separator;
	else
		$chain .= $name.$separator;
	return $chain;
}

/**
 * Retrieve post regions.
 *
 * @param int $id Optional, default to current post ID. The post ID.
 * @return array
 */
function get_the_region( $id = false ) {
	$regions = get_the_terms( $id, 'region' );
	if ( ! $regions || is_wp_error( $regions ) )
		$regions = array();

	$regions = array_values( $regions );

	foreach ( array_keys( $regions ) as $key ) {
		_make_cat_compat( $regions[$key] );
	}

	/**
	 * Filter the array of regions to return for a post.
	 *
	 * @param array $regions An array of regions to return for the post.
	 */
	return apply_filters( 'get_the_regions', $regions );
}
/**
 * Retrieve region list in either HTML list or custom format.
 *
 * @param string $separator Optional, default is empty string. Separator for between the regions.
 * @param string $parents Optional. How to display the parents.
 * @param int $post_id Optional. Post ID to retrieve regions.
 * @return string
 */
function get_the_region_list( $separator = '', $parents='', $post_id = false ) {
	global $wp_rewrite;
	if ( ! is_object_in_taxonomy( get_post_type( $post_id ), 'region' ) ) { 
		/** This filter is documented in wp-includes/category-template.php */
		return apply_filters( 'the_region', '', $separator, $parents );
	}

	$regions = get_the_region( $post_id );
	if ( empty( $regions ) ) {
		/** This filter is documented in wp-includes/category-template.php */
		return apply_filters( 'the_region', __( 'Uncategorized' ), $separator, $parents );
	}

	$rel = ( is_object( $wp_rewrite ) && $wp_rewrite->using_permalinks() ) ? 'rel="region tag"' : 'rel="region"';

	$thelist = '';
	if ( '' == $separator ) {
		$thelist .= '<ul class="post-regions">';
		foreach ( $regions as $region ) {
			$thelist .= "\n\t<li>";
			switch ( strtolower( $parents ) ) {
				case 'multiple':
					if ( $region->parent )
						$thelist .= get_region_parents( $region->parent, true, $separator );
					$thelist .= '<a href="' . esc_url( get_region_link( $region->term_id ) ) . '" ' . $rel . '>' . $region->name.'</a></li>';
					break;
				case 'single':
					$thelist .= '<a href="' . esc_url( get_region_link( $region->term_id ) ) . '"  ' . $rel . '>';
					if ( $region->parent )
						$thelist .= get_region_parents( $region->parent, false, $separator );
					$thelist .= $region->name.'</a></li>';
					break;
				case '':
				default:
					$thelist .= '<a href="' . esc_url( get_region_link( $region->term_id ) ) . '" ' . $rel . '>' . $region->name.'</a></li>';
			}
		}
		$thelist .= '</ul>';
	} else {
		$i = 0;
		foreach ( $regions as $region ) {
			if ( 0 < $i )
				$thelist .= $separator;
			switch ( strtolower( $parents ) ) {
				case 'multiple':
					if ( $region->parent )
						$thelist .= get_region_parents( $region->parent, true, $separator );
					$thelist .= '<a href="' . esc_url( get_region_link( $region->term_id ) ) . '" ' . $rel . '>' . $region->name.'</a>';
					break;
				case 'single':
					$thelist .= '<a href="' . esc_url( get_region_link( $region->term_id ) ) . '" ' . $rel . '>';
					if ( $region->parent )
						$thelist .= get_region_parents( $region->parent, false, $separator );
					$thelist .= "$region->name</a>";
					break;
				case '':
				default:
					$thelist .= '<a href="' . esc_url( get_region_link( $region->term_id ) ) . '" ' . $rel . '>' . $region->name.'</a>';
			}
			++$i;
		}
	}

	/**
	 * Filter the region or list of regions.
	 *
	 * @param array  $thelist   List of regions for the current post.
	 * @param string $separator Separator used between the regions.
	 * @param string $parents   How to display the region parents. Accepts 'multiple',
	 *                          'single', or empty.
	 */
	return apply_filters( 'the_region', $thelist, $separator, $parents );
}

/**
 * Check if the current post in within any of the given regions.
 *
 * The given regions are checked against the post's regions' term_ids, names and slugs.
 * Regions given as integers will only be checked against the post's regions' term_ids.
 *
 * @param int|string|array $region Category ID, name or slug, or array of said.
 * @param int|object $post Optional. Post to check instead of the current post.
 * @return bool True if the current post is in any of the given regions.
 */
function in_region( $region, $post = null ) {
	if ( empty( $region ) )
		return false;

	return has_region( $region, $post );
}

/**
 * Check if the current post has any of given region.
 *
 * @param string|int|array $region Optional. The region name/term_id/slug or array of them to check for.
 * @param int|object $post Optional. Post to check instead of the current post.
 * @return bool True if the current post has any of the given regions (or any region, if no region specified).
 */
function has_region( $region = '', $post = null ) {
	return has_term( $region, 'region', $post );
}

/**
 * Display the region list for the post.
 *
 * @param string $separator Optional, default is empty string. Separator for between the regions.
 * @param string $parents Optional. How to display the parents.
 * @param int $post_id Optional. Post ID to retrieve regions.
 */
function the_region( $separator = '', $parents='', $post_id = false ) {
	echo get_the_region_list( $separator, $parents, $post_id );
}

/**
 * Retrieve region description.
 *
 * @param int $region Optional. Category ID. Will use global region ID by default.
 * @return string Region description, available.
 */
function region_description( $region = 0 ) {
	return term_description( $region, 'region' );
}