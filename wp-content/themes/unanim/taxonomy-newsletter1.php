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

				<?php

					$cat = get_the_category();
                switch ($cat[0]->slug) {
                    case 'engages';
                    case 'toi-moi-nous-tous-formateurs';
                        get_template_part('template-parts/content', 'engages');
                        break;
                    case 'reperons-nous';
                        get_template_part('template-parts/content', 'reperons');
                        break;
                    case 'en-actions';
                        get_template_part('template-parts/content', 'actions');
                        break;
                    case 'autour-de-nous';
                        get_template_part('template-parts/content', 'autour');
                        break;
                    default: // pour aller plus loin, @vous, dans ma région, dans l'agenda
                        get_template_part('template-parts/content', 'default');
                        break;
                }
                ?>

			<?php endwhile; ?>

                <?php else : ?>

			<?php get_template_part( 'template-parts/content', 'none' ); ?>

		<?php endif; ?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php //get_sidebar(); ?>
<?php get_footer(); ?>
