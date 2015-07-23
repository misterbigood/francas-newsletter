<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package unanim
 */
get_header();
?>
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
        //echo "lolo".get_term_by('slug', 'non-classe', 'category')->term_id.".<br />";
        $args = array(
            'orderby' => 'id',
            'order' => 'ASC',
            'hide_empty' => true,
            'exclude' => ( ($toto = get_term_by('slug', 'non-classe', 'category')->term_id) ) ? array($toto) : array(),
            /*'exclude_tree' => array(),
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
            'cache_domain' => 'core'*/
        );
        $categories = get_terms('category', $args);

        //if ($categories) {
            foreach ($categories as $category) {
                $args = array(
                    'posts_per_page' => 1,
                    //'offset' => 0,
                    'category' => $category->term_id,
                    //'category_name' => '',
                    //'orderby' => 'date',
                    //'order' => 'DESC',
                    //'include' => '',
                    //'exclude' => '',
                    //'meta_key' => '',
                    //'meta_value' => '',
                    //'post_type' => 'post',
                    //'post_mime_type' => '',
                    //'post_parent' => '',
                    'post_status' => 'publish',
                    //'suppress_filters' => true
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
        

        <div style="display: none">
        <?php if (have_posts()) : ?>

            <?php /* Start the Loop */ ?>
            <?php while (have_posts()) : the_post(); ?>

                <?php
                // Affichage des posts en fonction de leur catégorie
                // Récupérer la  catégorie enregistrée pour le post
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

            <?php the_posts_navigation(); ?>

        <?php else : ?>

            <?php get_template_part('template-parts/content', 'none'); ?>

        <?php endif; ?>
        </div>
    </main><!-- #main -->
</div><!-- #primary -->

<?php //get_sidebar(); ?>
<?php get_footer(); ?>
