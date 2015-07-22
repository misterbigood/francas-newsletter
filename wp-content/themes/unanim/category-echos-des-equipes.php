<?php
/**
 * The template for displaying archive pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package unanim
 */

get_header(); ?>
        <div id="debug"><h5>Nav sinok</h5><p>Current file: <?php echo __FILE__; ?></p>
                    <p>Current newsletter: <?php echo get_current_nl();?></p></div>
	<div id="primary" class="content-area">
            <header class="page-header">
				<?php
					the_archive_title( '<h1 class="page-title">', '</h1>' );
					the_archive_description( '<div class="taxonomy-description">', '</div>' );
                                        
				?>
			</header><!-- .page-header -->
            <main id="main" class="site-main cols-3" role="main">
                <?php $background_class = array("jaune", "gris-clair", "gris-fonce", "gris-clair", "gris-fonce", "jaune", "gris-clair","jaune", "gris-fonce", "gris-clair", "gris-fonce","jaune");?>
                
                
		<?php if ( have_posts() ) : ?>

			

			<?php /* Start the Loop */ ?>
			<?php while ( have_posts() ) : the_post(); ?>

				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                                    <header class="entry-header">
                                            <?php //the_title( sprintf( '<h1 class="entry-title"><a href="%s">', esc_url( get_category_link($cat[0]->term_id) ) ), '</a></h1>' );
                                            the_title( '<h1 class="entry-title">', '</h1>' );?>
                                    </header><!-- .entry-header -->

                                    <div class="entry-content <?php echo $background_class[array_rand($background_class)];?>">
                                            <?php
                                                    the_content();
                                            ?>
                                    </div><!-- .entry-content -->
                                    <div class="entry-footer">
                                        
                                    </div>
                            </article><!-- #post-## -->

			<?php endwhile; ?>

                <?php else : ?>

			<?php get_template_part( 'template-parts/content', 'none' ); ?>

		<?php endif; ?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php //get_sidebar(); ?>
<?php get_footer(); ?>
