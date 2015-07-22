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
                <?php printf('<a href="%s"><img src="%s"></a>', esc_url( get_category_link($cat[0]->term_id) ), esc_url(get_template_directory_uri().'/assets/home-reperons.png'));?>
            </div>
            <?php the_title( sprintf( '<h1 class="entry-title"><a href="%s">', esc_url( get_category_link($cat[0]->term_id) ) ), '</a></h1>' ); ?>
	</header><!-- .entry-header -->
        <div class="entry-footer">
            <?php printf('<a href="%s">%s',esc_url( get_category_link($cat[0]->term_id) ) , 'Plus...','</a>'); ?>
        </div>
</article><!-- #post-## -->
