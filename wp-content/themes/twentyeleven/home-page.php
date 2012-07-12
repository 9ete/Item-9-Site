<?php
/**
 * The template used for displaying page content in home-template.php
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<h1 class="entry-title"><?php the_title(); ?></h1>
	</header><!-- .entry-header -->

	<div class="entry-content">
		<?php the_content(); ?>
		<?php wp_link_pages( array( 'before' => '<div class="page-link"><span>' . __( 'Pages:', 'twentyeleven' ) . '</span>', 'after' => '</div>' ) ); ?>

<div id="about_indpics">

<div class="about_member">
<img src="
<?php
			//pete about column
			$aboutPete = get_post_meta($post->ID, 'about-pete', true);
			if($aboutPete!='')
			{
			echo $aboutPete;
			}else{
			echo '';
			}
?>
" alt="" />
<p>
<?php
			//pete info column
			$infoPete = get_post_meta($post->ID, 'pete-info', true);
			if($infoPete!='')
			{
			echo $infoPete;
			}else{
			echo '';
			}
?>
</p>
</div>

<div class="about_member">
<img src="
<?php
			//amo about column
			$aboutAmo = get_post_meta($post->ID, 'about-amo', true);
			if($aboutAmo !='')
			{
			echo $aboutAmo ;
			}else{
			echo '';
			}
?>
" alt="" />
<p>
<?php
			//Amo info column
			$infoAmo = get_post_meta($post->ID, 'amo-info', true);
			if($infoAmo!='')
			{
			echo $infoAmo;
			}else{
			echo '';
			}
?>
</p>
</div>

<div class="about_member">
<img src="
<?php
			//max about column
			$aboutMax = get_post_meta($post->ID, 'about-max', true);
			if($aboutMax !='')
			{
			echo $aboutMax ;
			}else{
			echo '';
			}
?>
" alt="" />
<p>
<?php
			//max info column
			$infomax = get_post_meta($post->ID, 'max-info', true);
			if($infomax!='')
			{
			echo $infomax;
			}else{
			echo '';
			}
?>
</p>
</div>

<div class="about_member">
<img src="
<?php
			//matt about column
			$aboutBryks = get_post_meta($post->ID, 'about-bryks', true);
			if($aboutBryks !='')
			{
			echo $aboutBryks ;
			}else{
			echo '';
			}
?>
" alt="" />
<p>
<?php
			//matt info column
			$infomatt = get_post_meta($post->ID, 'bryks-info', true);
			if($infomatt!='')
			{
			echo $infomatt;
			}else{
			echo '';
			}
?>
</p>
</div>

<div class="about_member">
<img src="
<?php
			//bob about column
			$aboutBob = get_post_meta($post->ID, 'about-bob', true);
			if($aboutBob !='')
			{
			echo $aboutBob ;
			}else{
			echo '';
			}
?>
" alt="" />
<p>
<?php
			//bob info column
			$infobob = get_post_meta($post->ID, 'bob-info', true);
			if($infobob!='')
			{
			echo $infobob;
			}else{
			echo '';
			}
?>
</p>
</div>

</div>

</div><!-- .entry-content -->
	<footer class="entry-meta">
		<?php edit_post_link( __( 'Edit', 'twentyeleven' ), '<span class="edit-link">', '</span>' ); ?>
	</footer><!-- .entry-meta -->
</article><!-- #post-<?php the_ID(); ?> -->
