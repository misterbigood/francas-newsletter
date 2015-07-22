<?php
/**
 * The template for displaying archive pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package unanim
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">
                    
                    <h1>Taxo Newsletter <?php echo __FILE__; ?></h1>
                    
                    <div class="sinok alert alert-warning">
                        <?php get_previous_nl(get_current_nl()); ?>
                    </div>

		<?php if ( have_posts() ) : ?>

			<header class="page-header">
				<?php
					the_archive_title( '<h1 class="page-title">', '</h1>' );
					the_archive_description( '<div class="taxonomy-description">', '</div>' );
				?>
			</header><!-- .page-header -->

			<?php /* Start the Loop */ ?>
			<?php while ( have_posts() ) : the_post(); ?>

				

			<?php endwhile; ?>

                        <div class="sinok alert alert-warning newsletter-nav">
                            <span class="textleft newsletter-nav-link"><?php echo nav_previous_nl(get_current_nl()); ?></span>
                            <span class="textright newsletter-nav-link"><?php echo nav_next_nl(get_current_nl()); ?></span>
                        </div>

		<?php else : ?>

			<?php get_template_part( 'template-parts/content', 'none' ); ?>

		<?php endif; ?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
