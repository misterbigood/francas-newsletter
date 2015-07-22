<?php
/**
 * Template part for displaying posts.
 *
 * @package unanim
 */

    $cat =  get_the_category();
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
            <div class="entry-category"><?php printf('<a href="%s">%s</a>',esc_url( get_category_link($cat[0]->term_id) ) , $cat[0]->name); ?></div>
            <div class="entry-image">
                <?php printf('<a href="%s"><img src="%s"></a>', esc_url( get_category_link($cat[0]->term_id) ), esc_url(get_template_directory_uri().'/assets/home-'.$cat[0]->slug.'.png'));?>
            </div>
	</header><!-- .entry-header -->
        <div class="entry-footer">
            <?php printf('<a href="%s">Plus...</a>',esc_url( get_category_link($cat[0]->term_id) ) ); ?>
        </div>
</article><!-- #post-## -->
