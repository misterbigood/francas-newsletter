<?php
/**
 * The template for displaying archive pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package unanim
 */

get_header(); ?>
        <div  id="debug"><h5>Nav sinok</h5><p>Current file: <?php echo __FILE__; ?></p>
                    <p>Current newsletter: <?php echo get_current_nl();?></p></div>
	<div id="primary" class="content-area">
            <main id="main" class="site-main" role="main">
                
                
                
		<?php if ( have_posts() ) : ?>

			<header class="page-header">
				<?php
					the_archive_title( '<h1 class="page-title">', '</h1>' );
					the_archive_description( '<div class="taxonomy-description">', '</div>' );
                                        
				?>
			</header><!-- .page-header -->

			<?php /* Start the Loop */ ?>
			<?php while ( have_posts() ) : the_post(); ?>
                                
				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                                        <header class="entry-header">
                                            <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
                                        </header><!-- .entry-header -->

                                        <div class="entry-content">
                                                <?php
                                                        /* translators: %s: Name of current post */
                                                        the_content();

                                                ?>
                                        </div><!-- .entry-content -->

                                        <footer class="entry-footer">
                                                
                                        </footer><!-- .entry-footer -->
                                </article><!-- #post-## -->

			<?php endwhile; ?>

                <?php else : ?>

			<?php get_template_part( 'template-parts/content', 'none' ); ?>

		<?php endif; ?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php get_sidebar('region'); ?>
<?php get_footer(); ?>
