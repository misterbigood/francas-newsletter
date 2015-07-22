<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package unanim
 */

// start Sinok

//echo"<pre>";var_dump(get_nl_posts(array('nl'=>'nl_2015_06')));echo"</pre>";
// stop Sinok

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="page" class="hfeed site">
	<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'unanim' ); ?></a>

	<header id="masthead" class="site-header" role="banner">
		<div class="site-branding">
			<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
			<h2 class="site-description"><?php bloginfo( 'description' ); ?></h2>
		</div><!-- .site-branding -->

		<nav id="site-navigation" class="main-navigation" role="navigation">
			<button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false"><?php esc_html_e( 'Primary Menu', 'unanim' ); ?></button>
			<?php wp_nav_menu( array( 'theme_location' => 'primary', 'menu_id' => 'primary-menu' ) ); ?>
		</nav><!-- #site-navigation -->
	</header><!-- #masthead -->

	<div id="content" class="site-content">
            
            <!-- start Sinok -->
            <div class="sinok alert alert-warning">
                <p><strong>Sinok Test</strong></p>
                <p style="">Current newsletter: #<?php echo get_current_nl(); ?> <?php echo get_newsletter_name(get_current_nl()); ?><br ><small></small></p>
                <div>
                    <form name="newsletter_selector" method="post">
                    <?php echo select_newsletter_function(array('show_option_none' => '', 'select' => get_current_nl())); ?>
                        <input type="submit" name="send_newsletter_choice" value="Select" />
                    </form>
                </div>
                <div>
                    <p>Résults: </p>
                    <ul>
                    <?php 
                    $totoposts = get_nl_posts(array('newsletter'=>get_current_nl())); 
                    foreach($totoposts as $post) {
                        echo '<li>'.get_the_title($post->ID).' [Newsletter: '.get_the_newsletter_list(', ', '', $post->ID).'][Region: '.  get_the_region_list(', ', '', $post->ID).']</li>';
                    }
                    ?>
                    </ul>
                </div>
            </div>
            <!-- end Sinok -->

            