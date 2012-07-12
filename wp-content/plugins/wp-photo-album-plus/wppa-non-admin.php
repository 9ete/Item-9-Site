<?php 
/* wppa-non-admin.php
* Package: wp-photo-album-plus
*
* Contains all the non admin stuff
* Version 4.6.1
*
*/

/* API FILTER and FUNCTIONS */
require_once 'wppa-filter.php';
require_once 'wppa-slideshow.php';
require_once 'wppa-functions.php';
	
/* LOAD STYLESHEET */
add_action('wp_print_styles', 'wppa_add_style');

function wppa_add_style() {
	$userstyle = ABSPATH . 'wp-content/themes/' . get_option('template') . '/wppa-style.css';
	if ( is_file($userstyle) ) {
		wp_register_style('wppa_style', '/wp-content/themes/' . get_option('template')  . '/wppa-style.css');
		wp_enqueue_style('wppa_style');
	} else {
		wp_register_style('wppa_style', WPPA_URL.'/theme/wppa-style.css');
		wp_enqueue_style('wppa_style');
	}
}

/* SEO META TAGS */
add_action('wp_head', 'wppa_add_metatags');

function wppa_add_metatags() {
global $wpdb;

	// To make sure we are on a page that contains at least %%wppa%% we check for $_GET['wppa-album']. 
	// This also narrows the selection of featured photos to those that exist in the current album.
	if ( isset($_GET['wppa-album']) ) {
		$album = $_GET['wppa-album'];
		$photos = $wpdb->get_results($wpdb->prepare( "SELECT id, name FROM `".WPPA_PHOTOS."` WHERE `album` = %s AND `status` = %s ", $album, 'featured' ), 'ARRAY_A');
		if ( $photos ) {
			echo("\n<!-- WPPA+ BEGIN Featured photos on this page -->");
			foreach ( $photos as $photo ) {
				$id = $photo['id'];
				$name = esc_attr(__($photo['name']));
				$content = wppa_get_permalink().'wppa-photo='.$photo['id'].'&wppa-occur=1';
				echo("\n<meta name=\"".$name."\" content=\"".$content."\" >");
			}
			echo("\n<!-- WPPA+ END Featured photos on this page -->\n");
		}
	}
	// No album, give the plain photo links of all featured photos
	else {
		$photos = $wpdb->get_results($wpdb->prepare( "SELECT id, name, ext FROM `".WPPA_PHOTOS."` WHERE `status` = %s ",'featured' ), 'ARRAY_A');
		if ( $photos ) {
			echo("\n<!-- WPPA+ BEGIN Featured photos on this site -->");
			foreach ( $photos as $photo ) {
				$id = $photo['id'];
				$name = esc_attr(__($photo['name']));
				$ext = $photo['ext'];
				$content = WPPA_UPLOAD_URL.'/'.$id.'.'.$ext;
				echo("\n<meta name=\"".$name."\" content=\"".$content."\" >");
			}
			echo("\n<!-- WPPA+ END Featured photos on this site -->\n");
		}
	}
}

/* LOAD SLIDESHOW, THEME, AJAX and LIGHTBOX js, all in one file nowadays */
add_action('init', 'wppa_add_javascripts');
	
function wppa_add_javascripts() {
	wp_enqueue_script('wppa', WPPA_URL.'/wppa.js', array('jquery'));
}
	
/* LOAD WPPA+ THEME */
add_action('init', 'wppa_load_theme');
	
function wppa_load_theme() {
	$usertheme = ABSPATH . 'wp-content/themes/' . get_option('template') . '/wppa-theme.php';
	if ( is_file($usertheme) ) {
		require_once $usertheme;
	} else {
		require_once 'theme/wppa-theme.php';
	}
}
	
/* LOAD FOOTER REQD DATA */
add_action('wp_footer', 'wppa_load_footer');

function wppa_load_footer() {
global $wppa_opt;
	if ($wppa_opt['wppa_lightbox_name'] == 'wppa') {
		echo("\n<!-- start WPPA+ Footer data -->\n");
		echo('
			<div id="wppa-overlay-bg" style="text-align:center; display:none; position:fixed; top:0; left:0; z-index:100090; width:100%; height:500px; background-color:black;" onclick="wppaOvlOnclick(event)" ></div>
			<div id="wppa-overlay-ic" style="position:fixed; top:0; padding-top:10px; z-index:100095; opacity:1; box-shadow:none;" ></div>
			<img id="wppa-overlay-sp" style="position:fixed; top:200px; left:200px; z-index:100100; opacity:1; visibility:hidden; box-shadow:none;" src="'.wppa_get_imgdir().'loading.gif" />
			');
		echo("\n".'<script type="text/javascript">jQuery("#wppa-overlay-bg").css({height:screen.height+"px"});');
		if ( $wppa_opt['wppa_ovl_txt_lines'] == 'auto' ) {
			echo ("\n\t\t\t".'wppaOvlTxtHeight = "auto";');
		}
		else {
			echo ("\n\t\t\t".'wppaOvlTxtHeight = '.(($wppa_opt['wppa_ovl_txt_lines'] + 1) * 12).';');
		}
		echo('
			wppaOvlCloseTxt = "'.__($wppa_opt['wppa_ovl_close_txt']).'";
			wppaOvlOpacity = '.($wppa_opt['wppa_ovl_opacity']/100).';
			wppaOvlOnclickType = "'.$wppa_opt['wppa_ovl_onclick'].'";
			wppaOvlTheme = "'.$wppa_opt['wppa_ovl_theme'].'";
			wppaOvlAnimSpeed = '.$wppa_opt['wppa_ovl_anim'].';
			</script>');
		echo("\n<!-- end WPPA+ Footer data -->\n");
	}
}

/* LOAD JS VARS AND ENABLE RENDERING */
add_action('wp_head', 'wppa_kickoff', '100');

function wppa_kickoff() {
global $wppa;
global $wppa_opt;

	echo("\n<!-- WPPA+ Runtime parameters -->\n");
	
	echo('<script type="text/javascript">'."\n");
	echo('/* <![CDATA[ */'."\n");
	
		/* Check if wppa.js and jQuery are present */
		if ( WPPA_DEBUG || isset($_GET['wppa-debug']) || WP_DEBUG ) {
			echo("\t"."if (typeof(_wppaSlides) == 'undefined') alert('There is a problem with your theme. The file wppa.js is not loaded when it is expected (Errloc = wppa_kickoff).');");
			echo("\t"."if (typeof(jQuery) == 'undefined') alert('There is a problem with your theme. The jQuery library is not loaded when it is expected (Errloc = wppa_kickoff).');");
		}
		/* This goes into wppa_theme.js */ 
		echo("\t".'wppaBackgroundColorImage = "'.$wppa_opt['wppa_bgcolor_img'].'";'."\n");
		echo("\t".'wppaPopupLinkType = "'.$wppa_opt['wppa_thumb_linktype'].'";'."\n"); 
		//echo("\t".'wppa_popup_size = "'.$wppa_opt['wppa_popupsize'].'";'."\n");

		/* This goes into wppa_slideshow.js */
		if ($wppa_opt['wppa_animation_type']) echo("\t".'wppaAnimationType = "'.$wppa_opt['wppa_animation_type'].'";'."\n");
		echo("\t".'wppaAnimationSpeed = '.$wppa_opt['wppa_animation_speed'].';'."\n");
		echo("\t".'wppaImageDirectory = "'.wppa_get_imgdir().'";'."\n");
		if ($wppa['auto_colwidth']) echo("\t".'wppaAutoColumnWidth = true;'."\n");
		else echo("\t".'wppaAutoCoumnWidth = false;'."\n");
		echo("\t".'wppaThumbnailAreaDelta = '.wppa_get_thumbnail_area_delta().';'."\n");
		echo("\t".'wppaTextFrameDelta = '.wppa_get_textframe_delta().';'."\n");
		echo("\t".'wppaBoxDelta = '.wppa_get_box_delta().';'."\n");
		echo("\t".'wppaSlideShowTimeOut = '.$wppa_opt['wppa_slideshow_timeout'].';'."\n");		
		echo("\t".'wppaPreambule = '.wppa_get_preambule().';'."\n");
		if ($wppa_opt['wppa_film_show_glue'] == 'yes') echo("\t".'wppaFilmShowGlue = true;'."\n");
		else echo("\t".'wppaFilmShowGlue = false;'."\n");
		echo("\t".'wppaSlideShow = "'.__a('Slideshow', 'wppa_theme').'";'."\n");
		echo("\t".'wppaStart = "'.__a('Start', 'wppa_theme').'";'."\n");
		echo("\t".'wppaStop = "'.__a('Stop', 'wppa_theme').'";'."\n");
		echo("\t".'wppaPhoto = "'.__a('Photo', 'wppa_theme').'";'."\n");
		echo("\t".'wppaOf = "'.__a('of', 'wppa_theme').'";'."\n");
		echo("\t".'wppaPreviousPhoto = "'.__a('Previous photo', 'wppa_theme').'";'."\n");
		echo("\t".'wppaNextPhoto = "'.__a('Next photo', 'wppa_theme').'";'."\n");
		echo("\t".'wppaPrevP = "'.__a('Prev.', 'wppa_theme').'";'."\n");
		echo("\t".'wppaNextP = "'.__a('Next', 'wppa_theme').'";'."\n");
		echo("\t".'wppaUserName = "'.wppa_get_user().'";'."\n");
		if ($wppa_opt['wppa_rating_change'] || $wppa_opt['wppa_rating_multi']) echo("\t".'wppaRatingOnce = false;'."\n");
		else echo("\t".'wppaRatingOnce = true;'."\n");
		echo("\t".'wppaPleaseName = "'.__a('Please enter your name', 'wppa_theme').'";'."\n");
		echo("\t".'wppaPleaseEmail = "'.__a('Please enter a valid email address', 'wppa_theme').'";'."\n");
		echo("\t".'wppaPleaseComment = "'.__a('Please enter a comment', 'wppa_theme').'";'."\n");
		
		echo("\t".'wppaBGcolorNumbar = "'.$wppa_opt['wppa_bgcolor_numbar'].'";'."\n");
		echo("\t".'wppaBcolorNumbar = "'.$wppa_opt['wppa_bcolor_numbar'].'";'."\n");
		echo("\t".'wppaBGcolorNumbarActive = "'.$wppa_opt['wppa_bgcolor_numbar_active'].'";'."\n");
		echo("\t".'wppaBcolorNumbarActive = "'.$wppa_opt['wppa_bcolor_numbar_active'].'";'."\n");
		echo("\t".'wppaFontFamilyNumbar = "'.$wppa_opt['wppa_fontfamily_numbar'].'";'."\n");
		echo("\t".'wppaFontSizeNumbar = "'.$wppa_opt['wppa_fontsize_numbar'].'px";'."\n");
		echo("\t".'wppaFontColorNumbar = "'.$wppa_opt['wppa_fontcolor_numbar'].'";'."\n");
		echo("\t".'wppaFontWeightNumbar = "'.$wppa_opt['wppa_fontweight_numbar'].'";'."\n");
		echo("\t".'wppaFontFamilyNumbarActive = "'.$wppa_opt['wppa_fontfamily_numbar_active'].'";'."\n");
		echo("\t".'wppaFontSizeNumbarActive = "'.$wppa_opt['wppa_fontsize_numbar_active'].'px";'."\n");
		echo("\t".'wppaFontColorNumbarActive = "'.$wppa_opt['wppa_fontcolor_numbar_active'].'";'."\n");
		echo("\t".'wppaFontWeightNumbarActive = "'.$wppa_opt['wppa_fontweight_numbar_active'].'";'."\n");
		
		echo("\t".'wppaNumbarMax = "'.$wppa_opt['wppa_numbar_max'].'";'."\n");
		echo("\t".'wppaAjaxUrl = "'.admin_url('admin-ajax.php').'";'."\n");
		if ($wppa_opt['wppa_next_on_callback']) echo("\t".'wppaNextOnCallback = true;'."\n");
		else echo("\t".'wppaNextOnCallback = false;'."\n");
		if ($wppa_opt['wppa_rating_use_ajax']) echo("\t".'wppaRatingUseAjax = true;'."\n");
		else if ($wppa_opt['wppa_rating_use_ajax']) echo("\t".'wppaRatingUseAjax = false;'."\n");
		echo("\t".'wppaStarOpacity = '.($wppa_opt['wppa_star_opacity']/'100').';'."\n");
		// Preload checkmark and clock images
		echo("\t".'wppaTickImg.src = "'.wppa_get_imgdir().'tick.png";'."\n");
		echo("\t".'wppaClockImg.src = "'.wppa_get_imgdir().'clock.png";'."\n");
		if ($wppa_opt['wppa_slide_wrap'] == 'yes') echo("\t".'wppaSlideWrap = true;'."\n");
		else echo("\t".'wppaSlideWrap = false;'."\n");
		switch ($wppa_opt['wppa_slideshow_linktype']) {
			case 'none':
				echo("\t".'wppaLightBox = "";'."\n");		// results in omitting the anchor tag
				break;
			case 'file':
				echo("\t".'wppaLightBox = "file";'."\n");	// gives anchor tag with rel="file"
				break;
			case 'lightbox':
				echo("\t".'wppaLightBox = "'.$wppa_opt['wppa_lightbox_name'].'";'."\n");	// gives anchor tag with rel="lightbox" or the like
				break;
		}
		if ( $wppa_opt['wppa_comment_email_required'] ) echo("\t".'wppaEmailRequired = true;'."\n");
		else echo("\t".'wppaEmailRequired = false;'."\n");
		if ( is_numeric($wppa_opt['wppa_fullimage_border_width']) ) $temp = $wppa_opt['wppa_fullimage_border_width'] + '1'; else $temp = '0';
		echo("\t".'wppaSlideBorderWidth = '.$temp.';'."\n");
		if ( $wppa_opt['wppa_allow_ajax'] ) echo("\t".'wppaAllowAjax = true;'."\n"); 
		else echo("\t".'wppaAllowAjax = false;'."\n");
		if ( $wppa_opt['wppa_use_photo_names_in_urls'] ) echo("\t".'wppaUsePhotoNamesInUrls = true;'."\n"); 
		else echo("\t".'wppaUsePhotoNamesInUrls = false;'."\n"); 
		if ( $wppa_opt['wppa_thumb_blank'] ) echo("\t".'wppaThumbTargetBlank = true;'."\n");
		else echo("\t".'wppaThumbTargetBlank = false;'."\n");
		echo ("\t".'wppaRatingMax = '.$wppa_opt['wppa_rating_max'].';'."\n");
		echo ("\t".'wppaRatingDisplayType = "'.$wppa_opt['wppa_rating_display_type'].'";'."\n");
		echo ("\t".'wppaRatingPrec = '.$wppa_opt['wppa_rating_prec'].';'."\n");

	echo("/* ]]> */\n");
	echo("</script>\n");
	
	$wppa['rendering_enabled'] = true;
	echo("\n<!-- WPPA+ Rendering enabled -->\n");
	if ($wppa['debug']) {
		error_reporting($wppa['debug']);
		add_action('wp_footer', 'wppa_phpinfo');
	}
}

/* ADD ADMIN BAR */
require_once 'wppa-adminbar.php';
