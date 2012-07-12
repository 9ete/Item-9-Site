<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content after
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 */
?>

	</div><!-- #main -->

	<footer id="colophon" role="contentinfo">

			<?php
				/* A sidebar in the footer? Yep. You can can customize
				 * your footer with three columns of widgets.
				 */
				if ( ! is_404() )
					get_sidebar( 'footer' );
			?>

			<div id="site-generator">
			<img src="http://item9andthemadhatters.com/wp-content/uploads/2012/03/item9-iowa-city-band-rock-music-footer-line.png">

<div id="social_imgs">
<a target="_blank" href="http://twitter.com/#!/Item9MadHat"><img src="http://item9andthemadhatters.com/wp-content/uploads/2012/03/iowa-city-rock-band_free-music_item-9-mad-hatters-twitter"></a>
<a href="http://www.myspace.com/item9andthemadhatters"><img src="http://item9andthemadhatters.com/wp-content/uploads/2012/03/item9-mad-hatters_iowa-city-rock-band_free-music_myspace "></a>
<a target="_blank" href="https://www.facebook.com/item9andthemadhatters?ref=ts "><img src="http://item9andthemadhatters.com/wp-content/uploads/2012/03/iowa-city-rock-band_free-music_item-9-mad-hatters-facebook"></a>
<a target="_blank" href="http://bandwith.org/bands/item-9-the-mad-hatters"><img src="http://item9andthemadhatters.com/wp-content/uploads/2012/03/	iowa-city-rock-band_free-music_item-9-mad-hatters-bandwith "></a>
<a target="_blank" href="http://www.reverbnation.com/item9themadhatters"><img src="http://item9andthemadhatters.com/wp-content/uploads/2012/03/iowa-city-rock-band_free-music_item-9-mad-hatters_reverbnation"></a>
             </div>

				<?php do_action( 'twentyeleven_credits' ); ?>
				<a class="wordpressLink" href="<?php echo esc_url( __( 'http://wordpress.org/', 'twentyeleven' ) ); ?>" title="<?php esc_attr_e( 'Semantic Personal Publishing Platform', 'twentyeleven' ); ?>" rel="generator"><?php printf( __( 'Proudly powered by %s', 'twentyeleven' ), 'WordPress' ); ?></a>
			</div>
<center><a href="/privacy-policy">Privacy Policy</a> - <a href="http://www.petelower.com">&copy; 2011-2012 LowerMedia</a></center>
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>