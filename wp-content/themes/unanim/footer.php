<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package unanim
 */

?>

	</div><!-- #content -->

	<footer id="colophon" class="site-footer" role="contentinfo">
            
            <nav class="menu-footer">
                <nav id="site-navigation" class="main-navigation main-menu-footer" role="navigation">
			<?php wp_nav_menu( array( 'theme_location' => 'primary', 'menu_id' => 'primary-menu' ) ); ?>
		</nav><!-- #site-navigation -->
            </nav>
            <div class="btn-prev"><?php echo nav_previous_nl(get_current_nl()); ?></div>
            <div class="btn-next"><?php echo nav_next_nl(get_current_nl()); ?></div>
            <div class="menu-mentions">Les francas image + mentions</div>
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
