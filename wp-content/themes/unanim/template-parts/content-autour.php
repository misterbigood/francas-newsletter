<?php
/**
 * Template part for displaying posts.
 *
 * @package unanim
 */

$cat = get_the_category();
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<header class="entry-header">
            <div class="entry-category"><?php printf('<a href="%s">%s</a>',esc_url( get_category_link($cat[0]->term_id) ) , $cat[0]->name); ?></div>
  
	</header><!-- .entry-header -->	
    <div class="entry-content">
		<?php
			the_excerpt();
		?>
	</div><!-- .entry-content -->
        <div class="entry-footer">
            <?php printf('<a href="%s">%s',esc_url( get_category_link($cat[0]->term_id) ) , 'Plus...','</a>'); ?>
        </div>
</article><!-- #post-## -->
