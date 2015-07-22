<?php
/**
 * The template for displaying archive pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package unanim
 */

get_header(); ?>
        <div id="debug">
    <h5>Nav sinok</h5>
    <p>Current file: <?php echo __FILE__; ?></p>
    <p>Current newsletter: <?php echo get_current_nl();?></p>
    <?php if ( DEBUG ): ?>
        <?php if ($snk_messager->get('debug')) echo "<ul><li>".implode('</li><li>', $snk_messager->get('debug'))."</li></ul>"; ?>
    <?php endif; ?>
</div>
	<div id="primary" class="content-area">
            <main id="main" class="site-main" role="main">
                
                <?php
        // start Sinok
        $args = array(
            'orderby' => 'name',
            'order' => 'ASC',
            'hide_empty' => true,
            'exclude' => ( ($toto = get_term_by('slug', 'non-classe', 'category')->term_id) ) ? array($toto) : array(),
        );
        $categories = get_terms('category', $args);

            foreach ($categories as $category) {
                $args = array(
                    'posts_per_page' => 1,
                    'category' => $category->term_id,
                    'post_status' => 'publish',
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'newsletter',
                            'field' => 'slug',
                            'terms' => array(get_current_nl()),
                        )
                    )
                );
                $c_posts = get_posts($args);

                foreach ( $c_posts as $post ) : setup_postdata( $post ); ?>
                        
                        <?php
                        switch ($category->slug) {
                            case 'engages':
                            case 'toi-moi-nous-tous-formateurs':
                                get_template_part('template-parts/content', 'engages');
                                break;
                            case 'reperons-nous':
                                get_template_part('template-parts/content', 'reperons');
                                break;
                            case 'en-actions':
                                get_template_part('template-parts/content', 'actions');
                                break;
                            case 'autour-de-nous':
                                get_template_part('template-parts/content', 'autour');
                                break;
                            default: // pour aller plus loin, @vous, dans ma région, dans l'agenda
                                get_template_part('template-parts/content', 'default');
                                break;
                        }
                        ?>
                <?php endforeach; 
                wp_reset_postdata();?>
                <?php
                
            }
        //}

        /*echo '<pre>';
        var_dump($categories);
        echo '</pre>';*/

        // end Sinok 
        ?>
                
                <?php if ( true===false): ?>
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
                
                        <div style="display: none" class="sinok alert alert-warning newsletter-nav">
                            <span class="textleft newsletter-nav-link"><?php echo nav_previous_nl(get_current_nl()); ?></span>
                            <span class="textright newsletter-nav-link"><?php echo nav_next_nl(get_current_nl()); ?></span>
                        </div>
                        <?php endif; ?>
		</main><!-- #main -->
	</div><!-- #primary -->

<?php //get_sidebar(); ?>
<?php get_footer(); ?>
