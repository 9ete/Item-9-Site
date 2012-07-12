<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 */
?>

<!DOCTYPE html>
	<!--[if IE 6]>
	<html id="ie6" <?php language_attributes(); ?>>
	<![endif]-->
	<!--[if IE 7]>
	<html id="ie7" <?php language_attributes(); ?>>
	<![endif]-->
	<!--[if IE 8]>
	<html id="ie8" <?php language_attributes(); ?>>
	<![endif]-->
	<!--[if !(IE 6) | !(IE 7) | !(IE 8)  ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width" />
<title>
	<?php
		/*
		 * Print the <title> tag based on what is being viewed.
		 */
		global $page, $paged;
	
		wp_title( '|', true, 'right' );
	
		// Add the blog name.
		bloginfo( 'name' );
	
		// Add the blog description for the home/front page.
		$site_description = get_bloginfo( 'description', 'display' );
		if ( $site_description && ( is_home() || is_front_page() ) )
			echo " | $site_description";
	
		// Add a page number if necessary:
		if ( $paged >= 2 || $page >= 2 )
			echo ' | ' . sprintf( __( 'Page %s', 'twentyeleven' ), max( $paged, $page ) );
	?>
</title>
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>" />
<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_directory'); ?>/custom.css" media="screen" />
<link href='http://fonts.googleapis.com/css?family=Exo' rel='stylesheet' type='text/css'>
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<link type="image/x-icon" href="http://www.item9andthemadhatters.com/images/favicon.ico" rel="shortcut icon">
<!--[if lt IE 9]>
<script src="<?php echo get_template_directory_uri(); ?>/js/html5.js" type="text/javascript"></script>
<![endif]-->
	<?php
		/* We add some JavaScript to pages with the comment form
		 * to support sites with threaded comments (when in use).
		 */
		if ( is_singular() && get_option( 'thread_comments' ) )
			wp_enqueue_script( 'comment-reply' );
	
		/* Always have wp_head() just before the closing </head>
		 * tag of your theme, or you will break many plugins, which
		 * generally use this hook to add elements to <head> such
		 * as styles, scripts, and meta tags.
		 */
		wp_head();
	?>
<?php wp_enqueue_script("jquery"); ?>
<?php wp_head(); ?>
<script type="text/javascript"
   src="<?php bloginfo("template_url"); ?>/wp-content/themes/twentyeleven/html5-slideshow/script.js"></script>
<script type="text/javascript"
   src="<?php bloginfo("template_url"); ?>/js/peteJS.js"></script>
<!-- <script type="text/javascript">
$(document).ready(function() {
    $('.about_slideshow').cycle({
		fx: 'fade' // choose your transition type, ex: fade, scrollUp, shuffle, etc...
	});
	 $('#about_pete').cycle({
		fx:'fade' ,
		next: '#about_pete' 
		
	 })
	 
	 $('#about_max').cycle({
		fx:'fade' ,
		next: '#about_max' ,
		
		
	 });
	 $('#about_bryks').cycle({
		fx:'fade' ,
		next: '#about_bryks' ,
		
		
	 });
	  $('#about_amo').cycle({
		fx:'fade' ,
		next: '#about_amo' ,
		
		
	 });
	 $('#about_bob').cycle({
		fx:'fade' ,
		next: '#about_bob' ,
		
		
		
	 });
});
</script> -->

<script type="text/javascript">
$(document).ready(function()
{
	//test function to enable user to hide the test message from below
	$("p.test").click(function()
		{
			$(this).hide();
		});
		
	//output a test message to the screen for testing purposes between the ""
	$("p.test").html("");
	
	//force all off-site links to load in new window, while allowing on-site links to use the same window
	$("a").attr("target", function()
		{
			if(this.host == location.host) return "_self"
			else return "_blank";
		});
		
	  setInterval (rotateImage, 4500);
});
</script>

</head>

<body <?php body_class(); ?>>
<div id="page" class="hfeed">
	<header id="branding" role="banner">

			<nav id="access" role="navigation">
				<h3 class="assistive-text"><?php _e( 'Main menu', 'twentyeleven' ); ?></h3>
				<?php /*  Allow screen readers / text browsers to skip the navigation menu and get right to the good stuff. */ ?>
				<div class="skip-link"><a class="assistive-text" href="#content" title="<?php esc_attr_e( 'Skip to primary content', 'twentyeleven' ); ?>"><?php _e( 'Skip to primary content', 'twentyeleven' ); ?></a></div>
				<div class="skip-link"><a class="assistive-text" href="#secondary" title="<?php esc_attr_e( 'Skip to secondary content', 'twentyeleven' ); ?>"><?php _e( 'Skip to secondary content', 'twentyeleven' ); ?></a></div>
				<?php /* Our navigation menu.  If one isn't filled out, wp_nav_menu falls back to wp_page_menu. The menu assiged to the primary position is the one used. If none is assigned, the menu with the lowest ID is used. */ ?>
				<?php wp_nav_menu( array( 'theme_location' => 'primary' ) ); ?>
			</nav><!-- #access -->
<?php
				// Has the text been hidden?
				if ( 'blank' == get_header_textcolor() ) :
			?>
				<div class="only-search<?php if ( ! empty( $header_image ) ) : ?> with-image<?php endif; ?>">
				<?php //get_search_form(); ?>
				</div>
			<?php
				else :
			?>
				<?php //get_search_form(); ?>
			<?php endif; ?>
			<hgroup>
				<h1 id="site-title"><span><a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></span></h1>
				<h2 id="site-description"><?php bloginfo( 'description' ); ?></h2>
			</hgroup>

			
	</header><!-- #branding -->
	<div id="main">
<?php

				// Check to see if the header image has been removed
				$header_image = get_header_image();
				if ( ! empty( $header_image ) ) :
			?>
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<?php
					// The header image
					// Check if this is a post or page, if it has a thumbnail, and if it's a big one
					if ( is_singular() &&
							has_post_thumbnail( $post->ID ) &&
							( /* $src, $width, $height */ $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), array( HEADER_IMAGE_WIDTH, HEADER_IMAGE_WIDTH ) ) ) &&
							$image[1] >= HEADER_IMAGE_WIDTH ) :
						// Houston, we have a new header image!
						echo get_the_post_thumbnail( $post->ID, 'post-thumbnail' );
					else : ?><?php //header_image(); ?><?php //echo HEADER_IMAGE_WIDTH; ?><?php //echo HEADER_IMAGE_HEIGHT;  ?>
<?php

$headerImageCode = <<<EOT
<img class='padded' src='http://item9andthemadhatters.com/wp-content/uploads/2012/04/item9-iowa-city-band_Gabes_rock-music.jpg' width='850px' height='500px' alt='Item 9 Mad Hatters Iowa City Rock Band' />
EOT;

if( get_the_title($ID) == "Band Buzz")
{
echo $headerImageCode;
}

?>



				<?php endif; // end check for featured image or standard header ?>
			</a>
			<?php endif; // end check for removed header image ?>

<!-- http://item9andthemadhatters.com/wp-content/uploads/2012/03/iowa-city-rock-band_free-music_item-9-mad-hatters.jpg -->