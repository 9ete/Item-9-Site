<?php
/* wppa-common-functions.php
*
* Functions used in admin and in themes
* version 4.6.2
*
*/
global $wppa_api_version;
$wppa_api_version = '4-6-2-000';
// Initialize globals and option settings
function wppa_initialize_runtime($force = false) {
global $wppa;
global $wppa_opt;
global $wppa_revno;
global $wppa_api_version;
global $blog_id;
global $wpdb;

	if ($force) {
		$wppa = false; 					// destroy existing arrays
		$wppa_opt = false;
	}

	if (!is_array($wppa)) {
		$wppa = array (
			'debug' 					=> false,
			'revno' 					=> $wppa_revno,				// set in wppa.php
			'api_version' 				=> $wppa_api_version,		// set in wppa_functions.php
			'fullsize' 					=> '',
			'enlarge' 					=> false,
			'occur' 					=> '0',
			'master_occur' 				=> '0',
			'widget_occur' 				=> '0',
			'in_widget' 				=> false,
			'is_cover' 					=> '0',
			'is_slide' 					=> '0',
			'is_slideonly' 				=> '0',
			'film_on' 					=> '0',
			'browse_on' 				=> '0',
			'name_on' 					=> '0',
			'desc_on' 					=> '0',
			'numbar_on' 				=> '0',
			'single_photo' 				=> '',
			'is_mphoto' 				=> '0',
			'start_album' 				=> '',
			'align' 					=> '',
			'src' 						=> false,
			'portrait_only' 			=> false,
			'in_widget_linkurl' 		=> '',
			'in_widget_linktitle' 		=> '',
			'in_widget_timeout' 		=> '0',
			'ss_widget_valign' 			=> '',
			'album_count' 				=> '0',
			'thumb_count' 				=> '0',
			'out' 						=> '',
			'auto_colwidth' 			=> false,
			'permalink' 				=> '',
			'randseed' 					=> time() % '4711',
			'rendering_enabled' 		=> false,
			'tabcount' 					=> '0',
			'comment_id' 				=> '',
			'comment_photo' 			=> '0',
			'comment_user' 				=> '',
			'comment_email' 			=> '',
			'comment_text' 				=> '',
			'no_default' 				=> false,
			'in_widget_frame_height' 	=> '',
			'user_uploaded'				=> false,
			'current_album'				=> '0',
			'searchstring'				=> wppa_get_searchstring(),
			'searchresults'				=> '',
			'any'						=> false,
			'ajax'						=> false,
			'error'						=> false,
			'iptc'						=> false,
			'exif'						=> false,
			'is_topten'					=> false,
			'topten_count'				=> '0'

		);

		if (isset($_REQUEST['wppa-searchstring'])) $wppa['src'] = true;
		if (isset($_GET['s'])) $wppa['src'] = true;

	}
	
	if (!is_array($wppa_opt)) {
		$wppa_opt = array ( 'wppa_revision' 			=> '',
	
						// Table I: Sizes
						// A System
						'wppa_colwidth' 				=> '',	// 1
						'wppa_resize_on_upload' 		=> '',	// 2
						'wppa_resize_to'				=> '',	// 3
						'wppa_min_thumbs' 				=> '',	// 4
						'wppa_bwidth' 					=> '',	// 5
						'wppa_bradius' 					=> '',	// 6
						'wppa_box_spacing'				=> '',	// 7
						// B Fullsize
						'wppa_fullsize' 				=> '',	// 1
						'wppa_maxheight' 				=> '',	// 2
						'wppa_enlarge' 					=> '',	// 3
						'wppa_fullimage_border_width' 	=> '',	// 4
						'wppa_numbar_max'				=> '',	// 5
						// C Thumbnails
						'wppa_thumbsize' 				=> '',	// 1
						'wppa_thumb_aspect'				=> '',	// 2
						'wppa_tf_width' 				=> '',	// 3
						'wppa_tf_height' 				=> '',	// 4
						'wppa_tn_margin' 				=> '',	// 5
						'wppa_thumb_auto' 				=> '',	// 6
						'wppa_thumb_page_size' 			=> '',	// 7
						'wppa_popupsize' 				=> '',	// 8
						'wppa_use_thumbs_if_fit'		=> '',	// 9
						// D Covers
						'wppa_max_cover_width'			=> '',	// 1
						'wppa_text_frame_height'		=> '',	// 2
						'wppa_smallsize' 				=> '',	// 3
						'wppa_album_page_size' 			=> '',	// 4
						// E Rating & comments
						'wppa_rating_max'				=> '',	// 1
						'wppa_rating_prec'				=> '',	// 2
						'wppa_gravatar_size'			=> '',	// 3
						// F Widgets
						'wppa_topten_count' 			=> '',	// 1
						'wppa_topten_size' 				=> '',	// 2
						'wppa_comment_count'			=> '',	// 3
						'wppa_comment_size'				=> '',	// 4
						'wppa_thumbnail_widget_count'	=> '',	// 5
						'wppa_thumbnail_widget_size'	=> '',	// 6
						// G Overlay
						'wppa_ovl_txt_lines'			=> '',	// 
						
						// Table II: Visibility
						// A Breadcrumb
						'wppa_show_bread' 					=> '',	// 1
						'wppa_bc_on_search'					=> '',	// 2
						'wppa_bc_on_topten'					=> '',	// 3
						'wppa_show_home' 					=> '',	// 4
						'wppa_bc_separator' 				=> '',	// 5
						'wppa_bc_txt' 						=> '',	// 6
						'wppa_bc_url' 						=> '',	// 7
						'wppa_pagelink_pos'					=> '', 	// 8
						// B Slideshow
						'wppa_show_startstop_navigation' 	=> '',	// 1
						'wppa_show_browse_navigation' 		=> '',	// 2
						'wppa_filmstrip' 					=> '',	// 3
						'wppa_film_show_glue' 				=> '',	// 4
						'wppa_show_full_name' 				=> '',	// 5
						'wppa_show_full_desc' 				=> '',	// 6
						'wppa_rating_on' 					=> '',	// 7
						'wppa_rating_display_type'			=> '',	// 8
						'wppa_show_avg_rating'				=> '',	// 9
						'wppa_show_comments' 				=> '',	// 10
						'wppa_comment_gravatar'				=> '',	// 11
						'wppa_comment_gravatar_url'			=> '',	// 12
						'wppa_show_bbb'						=> '',	// 13
						'wppa_custom_on' 					=> '',	// 14
						'wppa_custom_content' 				=> '',	// 15
						'wppa_show_slideshownumbar'  		=> '',	// 16
						'wppa_show_iptc'					=> '',	// 17
						'wppa_show_exif'					=> '',	// 18
						'wppa_copyright_on'					=> '',	// 19
						'wppa_copyright_notice'				=> '',	// 20
						// C Thumbnails
						'wppa_thumb_text_name' 				=> '',	// 1
						'wppa_thumb_text_desc' 				=> '',	// 2
						'wppa_thumb_text_rating' 			=> '',	// 3
						'wppa_popup_text_name' 				=> '',	// 4
						'wppa_popup_text_desc' 				=> '',	// 5
						'wppa_popup_text_desc_strip'		=> '',	// 5.1
						'wppa_popup_text_rating' 			=> '',	// 6
						'wppa_show_rating_count'			=> '',	// 7
						// D Covers
						'wppa_show_cover_text' 				=> '',	// 1
						'wppa_enable_slideshow' 			=> '',	// 2
						'wppa_show_slideshowbrowselink' 	=> '',	// 3
						// E Widgets
						'wppa_show_bbb_widget'				=> '',	// 1
						// F Overlay
						'wppa_ovl_close_txt'				=> '',
						'wppa_ovl_theme'					=> '',

						// Table III: Backgrounds
						'wppa_bgcolor_even' 			=> '',
						'wppa_bcolor_even' 				=> '',
						'wppa_bgcolor_alt' 				=> '',
						'wppa_bcolor_alt' 				=> '',
						'wppa_bgcolor_nav' 				=> '',
						'wppa_bcolor_nav' 				=> '',
						'wppa_bgcolor_namedesc' 		=> '',
						'wppa_bcolor_namedesc' 			=> '',
						'wppa_bgcolor_com' 				=> '',
						'wppa_bcolor_com' 				=> '',
						'wppa_bgcolor_img'				=> '',
						'wppa_bcolor_img'				=> '',
						'wppa_bgcolor_fullimg' 			=> '',
						'wppa_bcolor_fullimg' 			=> '',
						'wppa_bgcolor_cus'				=> '',
						'wppa_bcolor_cus'				=> '',
						'wppa_bgcolor_numbar'			=> '',
						'wppa_bcolor_numbar'			=> '',
						'wppa_bgcolor_numbar_active'	=> '',
						'wppa_bcolor_numbar_active'	 	=> '',
						'wppa_bgcolor_iptc'				=> '',
						'wppa_bcolor_iptc' 				=> '',
						'wppa_bgcolor_exif'				=> '',
						'wppa_bcolor_exif' 				=> '',

						// Table IV: Behaviour
						// A System
						'wppa_allow_ajax'				=> '',	// 1
						'wppa_use_photo_names_in_urls'	=> '',	// 2
						// B Full size and Slideshow
						'wppa_fullvalign' 				=> '',	// 1
						'wppa_fullhalign' 				=> '',	// 2
						'wppa_start_slide' 				=> '',	// 3
						'wppa_animation_type'			=> '',	// 4
						'wppa_slideshow_timeout'		=> '',	// 5
						'wppa_animation_speed' 			=> '',	// 6
						'wppa_slide_pause'				=> '',	// 7
						'wppa_slide_wrap'				=> '',	// 8
						'wppa_fulldesc_align'			=> '',	// 9
						'wppa_clean_pbr'				=> '',	// 10
						// C Thumbnail
						'wppa_list_photos_by' 			=> '',	// 1
						'wppa_list_photos_desc' 		=> '',	// 2
						'wppa_thumbtype' 				=> '',	// 3
						'wppa_thumbphoto_left' 			=> '',	// 4
						'wppa_valign' 					=> '',	// 5
						'wppa_use_thumb_opacity' 		=> '',	// 6
						'wppa_thumb_opacity' 			=> '',	// 7
						'wppa_use_thumb_popup' 			=> '',	// 8
						// D Albums and covers
						'wppa_list_albums_by' 			=> '',	// 1
						'wppa_list_albums_desc' 		=> '',	// 2
						'wppa_coverphoto_pos'			=> '',	// 3
						'wppa_use_cover_opacity' 		=> '',	// 4
						'wppa_cover_opacity' 			=> '',	// 5
						// E Rating
						'wppa_rating_login' 			=> '',	// 1
						'wppa_rating_change' 			=> '',	// 2
						'wppa_rating_multi' 			=> '',	// 3
						'wppa_rating_use_ajax'			=> '',	// 4
						'wppa_next_on_callback'			=> '',	// 5
						'wppa_star_opacity'				=> '',	// 6
						// F Comments
						'wppa_comment_login' 			=> '',	// 1
						'wppa_comments_desc'			=> '',	// 2
						'wppa_comment_moderation'		=> '',	// 3
						'wppa_comment_email_required'	=> '',	// 4
						// G Overlay
						'wppa_ovl_opacity'				=> '',
						'wppa_ovl_onclick'				=> '',
						'wppa_ovl_anim'					=> '',


						// Table V: Fonts
						'wppa_fontfamily_title' 	=> '',
						'wppa_fontsize_title' 		=> '',
						'wppa_fontcolor_title' 		=> '',
						'wppa_fontweight_title'		=> '',
						'wppa_fontfamily_fulldesc' 	=> '',
						'wppa_fontsize_fulldesc' 	=> '',
						'wppa_fontcolor_fulldesc' 	=> '',
						'wppa_fontweight_fulldesc'	=> '',
						'wppa_fontfamily_fulltitle' => '',
						'wppa_fontsize_fulltitle' 	=> '',
						'wppa_fontcolor_fulltitle' 	=> '',
						'wppa_fontweight_fulltitle'	=> '',
						'wppa_fontfamily_nav' 		=> '',
						'wppa_fontsize_nav' 		=> '',
						'wppa_fontcolor_nav' 		=> '',
						'wppa_fontweight_nav'		=> '',
						'wppa_fontfamily_thumb' 	=> '',
						'wppa_fontsize_thumb' 		=> '',
						'wppa_fontcolor_thumb' 		=> '',
						'wppa_fontweight_thumb'		=> '',
						'wppa_fontfamily_box' 		=> '',
						'wppa_fontsize_box' 		=> '',
						'wppa_fontcolor_box' 		=> '',
						'wppa_fontweight_box'		=> '',
						'wppa_fontfamily_numbar' 	=> '',
						'wppa_fontsize_numbar' 		=> '',
						'wppa_fontcolor_numbar' 	=> '',
						'wppa_fontweight_numbar'	=> '',
						'wppa_fontfamily_numbar_active' 	=> '',
						'wppa_fontsize_numbar_active' 		=> '',
						'wppa_fontcolor_numbar_active' 	=> '',
						'wppa_fontweight_numbar_active'	=> '',
						
						// Table VI: Links
						'wppa_mphoto_linktype' 				=> '',
						'wppa_mphoto_linkpage' 				=> '',
						'wppa_mphoto_blank'					=> '',
						'wppa_mphoto_overrule'				=> '',
						
						'wppa_thumb_linktype' 				=> '',
						'wppa_thumb_linkpage' 				=> '',
						'wppa_thumb_blank'					=> '',
						'wppa_thumb_overrule'				=> '',
						
						'wppa_topten_widget_linktype' 		=> '',
						'wppa_topten_widget_linkpage' 		=> '',
						'wppa_topten_blank'					=> '',
						'wppa_topten_overrule'				=> '',
						
						'wppa_slideonly_widget_linktype' 	=> '',
						'wppa_slideonly_widget_linkpage' 	=> '',
						'wppa_sswidget_blank'				=> '',
						'wppa_sswidget_overrule'			=> '',

						'wppa_widget_linktype' 				=> '',
						'wppa_widget_linkpage' 				=> '',
						'wppa_potd_blank'					=> '',
						'wppa_potdwidget_overrule'			=> '',

						'wppa_coverimg_linktype' 			=> '',
						'wppa_coverimg_linkpage' 			=> '',
						'wppa_coverimg_blank'				=> '',
						'wppa_coverimg_overrule'			=> '',

						'wppa_comment_widget_linktype'		=> '',
						'wppa_comment_widget_linkpage'		=> '',
						'wppa_comment_blank'				=> '',
						'wppa_comment_overrule'				=> '',

						'wppa_slideshow_linktype'			=> '',
						'wppa_slideshow_blank'				=> '',
						'wppa_slideshow_overrule'			=> '',

						'wppa_thumbnail_widget_linktype'	=> '',
						'wppa_thumbnail_widget_linkpage'	=> '',
						'wppa_thumbnail_widget_overrule'	=> '',
						'wppa_thumbnail_widget_blank'		=> '',

						'wppa_film_linktype'				=> '',
						
						// Table VII: Security
						// B
						'wppa_user_upload_login'	=> '',
						'wppa_owner_only' 			=> '',
						'wppa_user_upload_on'		=> '',
						'wppa_upload_moderate'		=> '',
						'wppa_comment_captcha'		=> '',
						'wppa_spam_maxage'			=> '',
						
						// Table VIII: Actions
						// A Harmless
						'wppa_setup' 				=> '',
						'wppa_backup' 				=> '',
						'wppa_load_skin' 			=> '',
						'wppa_skinfile' 			=> '',
						'wppa_regen' 				=> '',
						'wppa_rerate'				=> '',
						'wppa_cleanup'				=> '',
						'wppa_recup'				=> '',
						// B Irreversable
						'wppa_rating_clear' 		=> '',
						'wppa_iptc_clear'			=> '',
						'wppa_exif_clear'			=> '',

						// Table IX: Miscellaneous
						'wppa_check_balance'			=> '',
						'wppa_arrow_color' 				=> '',
						'wppa_search_linkpage' 			=> '',
						'wppa_excl_sep' 				=> '',
						'wppa_photos_only'				=> '',	// 3
						
						'wppa_html' 					=> '',
						'wppa_allow_debug' 				=> '',
						'wppa_slide_order'				=> '',
						'wppa_swap_namedesc' 			=> '',
						'wppa_max_album_newtime'		=> '',
						'wppa_max_photo_newtime'		=> '',
						'wppa_lightbox_name'			=> '',
						'wppa_filter_priority'			=> '',
						'wppa_apply_newphoto_desc'		=> '',
						'wppa_newphoto_description'		=> '',
						'wppa_autoclean'				=> '',
						'wppa_watermark_on'				=> '',
						'wppa_watermark_user'			=> '',
						'wppa_watermark_file'			=> '',
						'wppa_watermark_pos'			=> '',
						'wppa_watermark_upload'			=> '',
						'wppa_watermark_opacity'		=> '',
						'wppa_allow_foreign_shortcodes' => '',

						// Photo of the day widget admin
						'wppa_widgettitle'			=> '',
						'wppa_widget_linkurl'		=> '',
						'wppa_widget_linktitle' 	=> '',
						'wppa_widget_subtitle'		=> '',
						'wppa_widget_album'			=> '',
						'wppa_widget_photo'			=> '',
						'wppa_potd_align' 			=> '',
						'wppa_widget_method'		=> '',
						'wppa_widget_period'		=> '',
						'wppa_widget_width'			=> '',
						
						// Topten widget
						'wppa_toptenwidgettitle'	=> '',

						// Thumbnail widget
						'wppa_thumbnailwidgettitle'	=> '',
						
						// Search widget
						'wppa_searchwidgettitle'	=> '',
						
						// Comment admin
						'wppa_comadmin_show' 		=> '',
						'wppa_comadmin_order' 		=> ''



		);
		array_walk($wppa_opt, 'wppa_set_options');
	}
	
	if (isset($_GET['debug']) && $wppa_opt['wppa_allow_debug']) {
		$key = $_GET['debug'] ? $_GET['debug'] : E_ALL;
		$wppa['debug'] = $key;
	}
	
	wppa_load_language();
	
	if ( ! defined( 'WPPA_UPLOAD') ) {
		if ( is_multisite() ) {
			define( 'WPPA_UPLOAD', 'wp-content/blogs.dir/'.$blog_id);
			define( 'WPPA_UPLOAD_PATH', ABSPATH.WPPA_UPLOAD.'/wppa');
			define( 'WPPA_UPLOAD_URL', get_bloginfo('wpurl').'/'.WPPA_UPLOAD.'/wppa');
			define( 'WPPA_DEPOT', 'wp-content/blogs.dir/'.$blog_id.'/wppa-depot' );			
			define( 'WPPA_DEPOT_PATH', ABSPATH.WPPA_DEPOT );					
			define( 'WPPA_DEPOT_URL', get_bloginfo('wpurl').'/'.WPPA_DEPOT );	
		}
		else {
			define( 'WPPA_UPLOAD', 'wp-content/uploads');
			define( 'WPPA_UPLOAD_PATH', ABSPATH.WPPA_UPLOAD.'/wppa' );
			define( 'WPPA_UPLOAD_URL', get_bloginfo('wpurl').'/'.WPPA_UPLOAD.'/wppa' );
			$user = is_user_logged_in() ? '/'.wppa_get_user() : '';
			define( 'WPPA_DEPOT', 'wp-content/wppa-depot'.$user );
			define( 'WPPA_DEPOT_PATH', ABSPATH.WPPA_DEPOT );
			define( 'WPPA_DEPOT_URL', get_bloginfo('wpurl').'/'.WPPA_DEPOT );
		}
	}
	
	// Delete obsolete spam
	$spammaxage = $wppa_opt['wppa_spam_maxage'];
	if ( $spammaxage != 'none' ) {
		$time = time();
		$obsolete = $time - $spammaxage;
		$iret = $wpdb->query($wpdb->prepare('DELETE FROM `'.WPPA_COMMENTS.'` WHERE `status` = %s AND `timestamp` < %s', 'spam', $obsolete));
		update_option('wppa_spam_auto_delcount', get_option('wppa_spam_auto_delcount', '0') + $iret);
	}
}

function wppa_set_options($value, $key) {
global $wppa_opt;

	if (is_admin()) {	// admin needs the raw data
		$wppa_opt[$key] = get_option($key);
	}
	else {
		$temp = get_option($key);
		switch ($temp) {
			case 'no':
				$wppa_opt[$key] = false;
				break;
			case 'yes':
				$wppa_opt[$key] = true;
				break;
			default:
				$wppa_opt[$key] = $temp;
			}	
	}
}

function wppa_load_language() {
global $wppa_locale;
global $q_config;
global $wppa;

	if ($wppa_locale) return; // Done already
	
	// See if qTranslate present and actve, if so, get locale there
	if (wppa_qtrans_enabled()) {	
		if (isset($q_config['language'])) $lang = $q_config['language'];
		if (isset($q_config['locale'][$lang])) $wppa_locale = $q_config['locale'][$lang];
	}
	else {		// Get locale from wp-config
		$wppa_locale = get_locale();
	}
	if ($wppa_locale) {
		$domain = is_admin() ? 'wppa' : 'wppa_theme';
		$mofile = WPPA_PATH.'/langs/'.$domain.'-'.$wppa_locale.'.mo';
		$bret = load_textdomain($domain, $mofile);
	}
	
	if ($wppa['debug']) {	// Diagnostic
		$wppa['out'] .= '<span style="color:blue"><small>Lang='.$lang.', Locale='.$wppa_locale.', Mofile='.$mofile;
		if (is_file($mofile)) $wppa['out'] .= ' exists.'; else $wppa['out'] .= ' does not exist.';
		if (!$bret) $bret = '0';
		$wppa['out'] .= ', loaded='.$bret.'.</small></span><br/>';	
	}
}

function wppa_phpinfo($key = -1) {
	if (is_int($key)) $k = $key; else $k = intval($key);
	if (!$k) $k = -1;
	echo("\n".'<div style="width:600px; margin: 24px auto;">'."\n");
	phpinfo($k);
	echo("\n".'</div>'."\n");
	echo("\n".'<style type="text/css">');
	echo("\n\ta:link {color: #990000; text-decoration: none; background-color: transparent;}");
	echo("\n</style>");
}

// get the url to the plugins image directory
function wppa_get_imgdir() {
	$result = WPPA_URL.'/images/';
	return $result;
}

// get album order
function wppa_get_album_order() {
global $wppa;
global $wppa_opt;

    $result = '';
    $order = $wppa_opt['wppa_list_albums_by'];
    switch ($order)
    {
    case '1':
        $result = 'ORDER BY a_order';
        break;
    case '2':
        $result = 'ORDER BY name';
        break;  
    case '3':
        $result = 'ORDER BY RAND('.$wppa['randseed'].')';
        break;
	case '5':
		$result = 'ORDER BY timestamp';
		break;
    default:
        $result = 'ORDER BY id';
    }
    if ( get_option('wppa_list_albums_desc', 'no') == 'yes' ) $result .= ' DESC';
    return $result;
}

// get photo order
function wppa_get_photo_order($id, $no_random = false) {
global $wpdb;
global $wppa;
global $wppa_opt;
    
	if ($id == 0) $order=0;
	else $order = $wpdb->get_var( $wpdb->prepare( "SELECT p_order_by FROM " . WPPA_ALBUMS . " WHERE id=%s", $id) );
    if ($order == '0') $order = $wppa_opt['wppa_list_photos_by'];
    switch ($order)
    {
    case '1':
        $result = 'ORDER BY p_order';
        break;
    case '2':
        $result = 'ORDER BY name';
        break;
    case '3':
		if ($no_random) $result = 'ORDER BY name';
        else $result = 'ORDER BY RAND('.$wppa['randseed'].')';
        break;
	case '4':
		$result = 'ORDER BY mean_rating';
		break;
	case '5':
		$result = 'ORDER BY timestamp';
		break;
	case '6':
		$result = 'ORDER BY rating_count';
		break;
    default:
        $result = 'ORDER BY id';
    }
    if ( get_option('wppa_list_photos_desc', 'no') == 'yes' ) $result .= ' DESC';
    return $result;
}

function wppa_get_rating_count_by_id($id = '') {
global $wpdb;
global $thumb;

	if (!is_numeric($id)) return '';

	if ( isset($thumb['id']) ) {
		if ( $thumb['id'] == $id ) return $thumb['rating_count'];	// Fopund in temp global photo
	}

	$query = $wpdb->prepare( 'SELECT `rating_count` FROM `'.WPPA_PHOTOS.'` WHERE `id` = %s', $id);
	return $wpdb->get_var($query);
}

function wppa_get_rating_by_id($id = '', $opt = '') {
global $wpdb;
global $wppa_opt;

	$result = '';
	if (is_numeric($id)) {
		$rating = $wpdb->get_var( $wpdb->prepare( "SELECT mean_rating FROM ".WPPA_PHOTOS." WHERE id=%s", $id ) );
		if ($rating) {
			$i = $wppa_opt['wppa_rating_prec'];
			$j = $i + '1';
			$val = sprintf('%'.$j.'.'.$i.'f', $rating);
			if ($opt == 'nolabel') $result = $val;
			else $result = sprintf(__a('Rating: %s', 'wppa_theme'), $val);
		}
	}
	return $result;
}

// See if an album is another albums ancestor
function wppa_is_ancestor($anc, $xchild) {

	$child = $xchild;
	if (is_numeric($anc) && is_numeric($child)) {
		$parent = wppa_get_parentalbumid($child);
		while ($parent > '0') {
			if ($anc == $parent) return true;
			$child = $parent;
			$parent = wppa_get_parentalbumid($child);
		}
	}
	return false;
}

// Get the albums parent
function wppa_get_parentalbumid($alb) {
global $wpdb;
    
	$query = $wpdb->prepare('SELECT `a_parent` FROM `' . WPPA_ALBUMS . '` WHERE `id` = %s', $alb);
	$result = $wpdb->get_var($query);
	
    if (!is_numeric($result)) {
		$result = 0;
	}
    return $result;
}

// get user
function wppa_get_user() {
global $current_user;

	if (is_user_logged_in()) {
		get_currentuserinfo();
		$user = $current_user->user_login;
		return $user;
	}
	else {
		return $_SERVER['REMOTE_ADDR'];
	}
}

function wppa_get_album_id($name = '') {
global $wpdb;

	if ($name == '') return '';
    $name = stripslashes($name);
    $id = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM " . WPPA_ALBUMS . " WHERE name = %s", $name ) );
    if ($id) {
		return $id;
	}
	else {
		return '';
	}
}

function wppa_get_album_name($id = '', $raw = '') {
global $wpdb;
    
    if ($id == '0') $name = is_admin() ? __('--- none ---', 'wppa') : __a('--- none ---', 'wppa_theme');
    elseif ($id == '-1') $name = is_admin() ? __('--- separate ---', 'wppa') : __a('--- separate ---', 'wppa_theme');
    else {
        if ($id == '') if (isset($_GET['album'])) $id = $_GET['album'];
        $id = $wpdb->escape($id);	
        if (is_numeric($id)) $name = $wpdb->get_var( $wpdb->prepare( "SELECT name FROM " . WPPA_ALBUMS . " WHERE id=%s", $id ) );
		else $name = '';
    }
	if ($name) {
		if ($raw != 'raw') $name = stripslashes($name);
	}
	else {
		$name = '';
	}
	if (!is_admin()) $name = wppa_qtrans($name);
	return $name;
}

// Check if an image is more landscape that the width/height ratio set in Table I item 2 and 3
function wppa_is_wider($x, $y, $refx = '', $refy = '') {
global $wppa_opt;
	if ( $refx == '' ) {
		$ratioref = $wppa_opt['wppa_fullsize'] / $wppa_opt['wppa_maxheight'];
	}
	else {
		$ratioref = $refx/$refy;
	}
	$ratio = $x / $y;
	return ($ratio > $ratioref);
}

// qtrans hook to see if qtrans is installed
function wppa_qtrans_enabled() {
	return (function_exists('qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage'));
}

// qtrans hook for multi language support of content
function wppa_qtrans($output, $lang = '') {
	if ($lang == '') {
		$output = __($output);
//		if (function_exists('qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage')) {
//			$output = qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage($output);
//		}
	} else {
		if (function_exists('qtrans_use')) {
			$output = qtrans_use($lang, $output, false);
		}
	}
	return $output;
}

function wppa_dbg_msg($txt='', $color = 'blue', $force = false) {
global $wppa;
	if ( $wppa['debug'] || $force ) echo('<span style="color:'.$color.';"><small>[WPPA+ dbg msg: '.$txt.']<br /></small></span>');
}

function wppa_dbg_url($link, $js = '') {
global $wppa;
	$result = $link;
	if ($wppa['debug']) {
		if (strpos($result, '?')) {
			if ($js == 'js') $result .= '&';
			else $result .= '&amp;';
		}
		else $result .= '?';
		$result .= 'debug='.$wppa['debug'];
	}
	return $result;
}

function wppa_get_time_since($oldtime) {

	if (is_admin()) {	// admin version
		$newtime = time();
		$diff = $newtime - $oldtime;
		if ($diff < 60) {
			if ($diff == 1) return __('1 second', 'wppa');
			else return $diff.' '.__('seconds', 'wppa');
		}
		$diff = floor($diff / 60);
		if ($diff < 60) {
			if ($diff == 1) return __('1 minute', 'wppa');
			else return $diff.' '.__('minutes', 'wppa');
		}
		$diff = floor($diff / 60);
		if ($diff < 24) {
			if ($diff == 1) return __('1 hour', 'wppa');
			else return $diff.' '.__('hours', 'wppa');
		}
		$diff = floor($diff / 24);
		if ($diff < 7) {
			if ($diff == 1) return __('1 day', 'wppa');
			else return $diff.' '.__('days', 'wppa');
		}
		elseif ($diff < 31) {
			$t = floor($diff / 7);
			if ($t == 1) return __('1 week', 'wppa');
			else return $t.' '.__('weeks', 'wppa');
		}
		$diff = floor($diff / 30.4375);
		if ($diff < 12) {
			if ($diff == 1) return __('1 month', 'wppa');
			else return $diff.' '.__('months', 'wppa');
		}
		$diff = floor($diff / 12);
		if ($diff == 1) return __('1 year', 'wppa');
		else return $diff.' '.__('years', 'wppa');
	}
	else {	// theme version
		$newtime = time();
		$diff = $newtime - $oldtime;
		if ($diff < 60) {
			if ($diff == 1) return __a('1 second', 'wppa_theme');
			else return $diff.' '.__a('seconds', 'wppa_theme');
		}
		$diff = floor($diff / 60);
		if ($diff < 60) {
			if ($diff == 1) return __a('1 minute', 'wppa_theme');
			else return $diff.' '.__a('minutes', 'wppa_theme');
		}
		$diff = floor($diff / 60);
		if ($diff < 24) {
			if ($diff == 1) return __a('1 hour', 'wppa_theme');
			else return $diff.' '.__a('hours', 'wppa_theme');
		}
		$diff = floor($diff / 24);
		if ($diff < 7) {
			if ($diff == 1) return __a('1 day', 'wppa_theme');
			else return $diff.' '.__a('days', 'wppa_theme');
		}
		elseif ($diff < 31) {
			$t = floor($diff / 7);
			if ($t == 1) return __a('1 week', 'wppa_theme');
			else return $t.' '.__a('weeks', 'wppa_theme');
		}
		$diff = floor($diff / 30.4375);
		if ($diff < 12) {
			if ($diff == 1) return __a('1 month', 'wppa_theme');
			else return $diff.' '.__a('months', 'wppa_theme');
		}
		$diff = floor($diff / 12);
		if ($diff == 1) return __a('1 year', 'wppa_theme');
		else return $diff.' '.__a('years', 'wppa_theme');
	}
}

function wppa_nextkey($table) {
// Creating a keyvalue of an auto increment primary key incidently returns the value of MAXINT
// and thereby making it impossible to add a next record.
// This routine will find a free keyvalue larger than any key used, ignoring the fact that the MAXINT key may be used.
global $wpdb;

	$name = 'wppa_'.$table.'_lastkey';
	$lastkey = get_option($name, 'nil');
	
	if ( $lastkey == 'nil' ) {	// Init option
		$lastkey = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM ".$table." WHERE id < '9223372036854775806' ORDER BY id DESC LIMIT 1" ) );
		if ( ! is_numeric($lastkey) ) $lastkey = '0';
		add_option( $name, $lastkey, '', 'no');
	}
	wppa_dbg_msg('Lastkey in '.$table.' = '.$lastkey);
	
	$result = $lastkey + '1';
	while ( ! wppa_is_id_free($table, $result) ) {
		$result++;
	}
	update_option($name, $result);
	return $result;
}

function wppa_is_id_free($type, $id) {
global $wpdb;
	if (!is_numeric($id)) return false;
	if ($id == '0') return false;
	
	$table = '';
	if ($type == 'album') $table = WPPA_ALBUMS;
	elseif ($type == 'photo') $table = WPPA_PHOTOS;
	else $table = $type;	// $type may be the tablename itsself
	
	if ($table == '') {
		echo('Unexpected error in wppa_is_id_free()');
		return false;
	}
	
	$exists = $wpdb->get_row($wpdb->prepare( 'SELECT * FROM '.$table.' WHERE id = %s', $id ), 'ARRAY_A');
	if ($exists) return false;
	return true;
}

// See if an album or any album is accessable for the current user
function wppa_have_access($alb) {
global $wpdb;
global $current_user;
global $wppa_opt;

	if ( !$alb ) return false;
	
	// See if there is any album accessable
	if ($alb == 'any') {
	
		// Administrator has always access OR If all albums are public
		if (current_user_can('administrator') || get_option('wppa_owner_only', 'no') == 'no') {
			$albs = $wpdb->get_results($wpdb->prepare( 'SELECT id FROM '.WPPA_ALBUMS ) );
			if ($albs) return true;
			else return false;	// No albums in system
		}
		
		// Any --- public --- albums?
		$albs = $wpdb->get_results($wpdb->prepare( 'SELECT id FROM '.WPPA_ALBUMS.' WHERE owner = "--- public ---"' ) );
		if ($albs) return true;
		
		// Any albums owned by this user?
		get_currentuserinfo();
		$user = $current_user->user_login;
		$albs = $wpdb->get_results($wpdb->prepare( 'SELECT id FROM '.WPPA_ALBUMS.' WHERE owner = "%s"', $user) );
		if ($albs) return true;
		else return false;	// No albums for user accessable
		
	}
	
	// See for given album data array or album number
	else {
	
		// Administrator has always access
		if (current_user_can('administrator')) return true;
		
		// If all albums are public
		if (get_option('wppa_owner_only', 'no') == 'no') return true;
		
		// Find the owner
		$owner = '';
		if (is_array($alb)) {
			$owner = $alb['owner'];
		}
		elseif (is_numeric($alb)) {
			$owner = $wpdb->get_var($wpdb->prepare( 'SELECT owner FROM '.WPPA_ALBUMS.' WHERE id = %s', $alb ) );
		}
		
		// -- public --- ?
		if ( $owner == '--- public ---' ) return true;
		
		// Find the user
		get_currentuserinfo();
		
		if ( $current_user->user_login == $owner ) return true;
	}
	return false;
}

function wppa_make_the_photo_files($file, $image_id, $ext) {
global $wppa_opt;
				
	wppa_dbg_msg('make_the_photo_files called with file='.$file.' image_id='.$image_id.' ext='.$ext);
	$img_size = getimagesize($file, $info);
	if ($img_size) {
		$newimage = WPPA_UPLOAD_PATH . '/' . $image_id . '.' . $ext;
		wppa_dbg_msg('newimage='.$newimage);
		
		if (get_option('wppa_resize_on_upload', 'no') == 'yes') {
			require_once('wppa-class-resize.php');
			// Picture sizes
			$picx = $img_size[0];
			$picy = $img_size[1];
			// Reference suzes
			if ( $wppa_opt['wppa_resize_to'] == '0') {	// from fullsize
				$refx = $wppa_opt['wppa_fullsize'];
				$refy = $wppa_opt['wppa_maxheight'];
			}
			else {										// from selection
				$screen = explode('x', $wppa_opt['wppa_resize_to']);
				$refx = $screen[0];
				$refy = $screen[1];
			}
			// Too landscape?
			if ( $picx/$picy > $refx/$refy ) {					// focus on width
				$dir = 'W';
				$siz = $refx;
				$s = $img_size[0];
			}
			else {												// focus on height
				$dir = 'H';
				$siz = $refy;
				$s = $img_size[1];
			}

			if ($s > $siz) {	
				$objResize = new wppa_ImageResize($file, $newimage, $dir, $siz);
				$objResize->destroyImage($objResize->resOriginalImage);
				$objResize->destroyImage($objResize->resResizedImage);
			}
			else {
				copy($file, $newimage);
			}
		}
		else {
			copy($file, $newimage);
		}
		
		// File successfully created ?
		if ( is_file ($newimage) ) {	
			// Create thumbnail...
			$thumbsize = wppa_get_minisize();
			wppa_create_thumbnail($newimage, $thumbsize, '' );
			// and add watermark (optionally) to fullsize image only
			wppa_add_watermark($newimage);
		} 
		else {
			if (is_admin()) wppa_error_message(__('ERROR: Resized or copied image could not be created.', 'wppa'));
			else wppa_err_alert(__('ERROR: Resized or copied image could not be created.', 'wppa_theme'));
			return false;
		}
		
		// Process the iptc data
		wppa_import_iptc($image_id, $info);
		
		// Process the exif data
		wppa_import_exif($image_id, $file);

		// Show progression
		if (is_admin()) echo('.');
		
		// Clear (super)cache
		wppa_clear_cache();
		return true;
	}
	else {
		if (is_admin()) wppa_error_message(sprintf(__('ERROR: File %s is not a valid picture file.', 'wppa'), $file));
		else wppa_err_alert(sprintf(__('ERROR: File %s is not a valid picture file.', 'wppa_theme'), $file));
		return false;
	}
}

function wppa_get_minisize() {
	$result = '100';
	
	$tmp = get_option('wppa_thumbsize', 'nil');
	if (is_numeric($tmp) && $tmp > $result) $result = $tmp;
	$tmp = get_option('wppa_smallsize', 'nil');
	if (is_numeric($tmp) && $tmp > $result) $result = $tmp;
	$tmp = get_option('wppa_popupsize', 'nil');
	if (is_numeric($tmp) && $tmp > $result) $result = $tmp;
	
	$result = ceil($result / 25) * 25;
	return $result;
}

// Create thubnail from a given fullsize image path and max size
function wppa_create_thumbnail( $file, $max_side, $effect = '') {
global $wppa_opt;
	
	// See if we are called with the right args
	if ( ! file_exists($file) ) return false;		// No file, fail
	$img_attr = getimagesize( $file );
	if ( ! $img_attr ) return false;				// Not an image, fail
	
	// Retrieve additional required info
	$asp_attr = explode(':', $wppa_opt['wppa_thumb_aspect']);
	$thumbpath = str_replace( basename( $file ), 'thumbs/' . basename( $file ), $file );
	// Source size
	$src_size_w = $img_attr[0];
	$src_size_h = $img_attr[1];
	// Mime type and thumb type
	$mime = $img_attr[2]; 
	$type = $asp_attr[2];
	// Source native aspect
	$src_asp = $src_size_h / $src_size_w;
	// Required aspect
	if ($type == 'none') {
		$dst_asp = $src_asp;
	}
	else {
		$dst_asp = $asp_attr[0] / $asp_attr[1];
	}
	
	// Create the source image
	switch ($mime) {	// mime type
		case 1: // gif
			$temp = imagecreatefromgif($file);
			$src = imagecreatetruecolor($src_size_w, $src_size_h);
			imagecopy($src, $temp, 0, 0, 0, 0, $src_size_w, $src_size_h);
			imagedestroy($temp);
			break;
		case 2:	// jpeg
			$src = imagecreatefromjpeg($file);
			break;
		case 3:	// png
			$src = imagecreatefrompng($file);
			break;
	}
	
	// Compute the destination image size
	if ( $dst_asp < 1.0 ) {	// Landscape
		$dst_size_w = $max_side;
		$dst_size_h = round($max_side * $dst_asp);
	}
	else {					// Portrait
		$dst_size_w = round($max_side / $dst_asp);
		$dst_size_h = $max_side;
	}
	
	// Create the (empty) destination image
	//echo 'dst_asp='.$dst_asp.' src_asp='.$src_asp;
	//echo ' size_w='.$dst_size_w.' size_h='.$dst_size_h;
	$dst = imagecreatetruecolor($dst_size_w, $dst_size_h);


	// Switch on what we have to do
	switch ($type) {
		case 'none':	// Use aspect from fullsize image
			$src_x = 0;
			$src_y = 0;
			$src_w = $src_size_w;
			$src_h = $src_size_h;
			$dst_x = 0;
			$dst_y = 0;
			$dst_w = $dst_size_w;
			$dst_h = $dst_size_h;
			break;
		case 'clip':	// Clip image to given aspect ratio
			if ( $src_asp < $dst_asp ) {	// Source image more landscape than destination
				$dst_x = 0;
				$dst_y = 0;
				$dst_w = $dst_size_w;
				$dst_h = $dst_size_h;
				$src_x = round(($src_size_w - $src_size_h / $dst_asp) / 2);
				$src_y = 0;
				$src_w = round($src_size_h / $dst_asp);
				$src_h = $src_size_h;
			}
			else {
				$dst_x = 0;
				$dst_y = 0;
				$dst_w = $dst_size_w;
				$dst_h = $dst_size_h;
				$src_x = 0;
				$src_y = round(($src_size_h - $src_size_w * $dst_asp) / 2);
				$src_w = $src_size_w;
				$src_h = round($src_size_w * $dst_asp);
			}
			break;
		case 'padd':	// Padd image to given aspect ratio
			if ( $src_asp < $dst_asp ) {	// Source image more landscape than destination
				$dst_x = 0;
				$dst_y = round(($dst_size_h - $dst_size_w * $src_asp) / 2);
				$dst_w = $dst_size_w;
				$dst_h = round($dst_size_w * $src_asp);
				$src_x = 0;
				$src_y = 0;
				$src_w = $src_size_w;
				$src_h = $src_size_h;
			}
			else {
				$dst_x = round(($dst_size_w - $dst_size_h / $src_asp) / 2);
				$dst_y = 0;
				$dst_w = round($dst_size_h / $src_asp);
				$dst_h = $dst_size_h;
				$src_x = 0;
				$src_y = 0;
				$src_w = $src_size_w;
				$src_h = $src_size_h;
			}
			break;
		default:		// Not implemented
			return false;
	}
	
	// Do the copy
	//echo ' dst_x='.$dst_x.' dst_y='.$dst_y.' src_x='.$src_x.' src_y='.$src_y.' dst_w='.$dst_w.' dst_h='.$dst_h.' src_w='.$src_w.' src_h='.$src_h.'<br />';
	imagecopyresampled($dst, $src, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
	
	// Save the thumb
	switch ($mime) {	// mime type
		case 1:
			imagegif($dst, $thumbpath);
			break;
		case 2:
			imagejpeg($dst, $thumbpath, 100);
			break;
		case 3:
			imagepng($dst, $thumbpath, 6);
			break;
	}
	
	// Cleanup
	imagedestroy($src);
	imagedestroy($dst);
	return true;
}

function wppa_get_searchstring() {
global $wppa;

	$src = '';
	
	if (isset($_REQUEST['wppa-searchstring'])) {
		$src = $_REQUEST['wppa-searchstring'];
	}
	elseif (isset($_GET['s'])) {	// wp search
		$src = $_GET['s'];
	}

	return stripslashes($src);
}

function wppa_add_watermark($file) {
global $wppa_opt;

	// Init
	if ( get_option('wppa_watermark_on') != 'yes' ) return;	// Watermarks off
	$user = wppa_get_user();
	
	// Find the watermark file and location
	$waterfile = WPPA_UPLOAD_PATH . '/watermarks/' . $wppa_opt['wppa_watermark_file'];	// default
	$waterpos = $wppa_opt['wppa_watermark_pos'];										// default

	if ( get_option('wppa_watermark_user') == 'yes' ) {									// user overrule?
		if ( isset($_POST['wppa-watermark-file'] ) ) {
			$waterfile = WPPA_UPLOAD_PATH . '/watermarks/' . $_POST['wppa-watermark-file'];
			update_option('wppa_watermark_file_' . $user, $_POST['wppa-watermark-file']);
		}
		elseif ( get_option('wppa_watermark_file_' . $user, 'nil') != 'nil' ) {
			$waterfile = WPPA_UPLOAD_PATH . '/watermarks/' . get_option('wppa_watermark_file_' . $user);
		}
		if ( isset($_POST['wppa-watermark-pos'] ) ) {
			$waterpos = $_POST['wppa-watermark-pos'];
			update_option('wppa_watermark_pos_' . $user, $_POST['wppa-watermark-pos']);
		}
		elseif ( get_option('wppa_watermark_pos_' . $user, 'nil') != 'nil' ) {
			$waterpos = get_option('wppa_watermark_pos_' . $user);
		}
	}

	if ( basename($waterfile) == '--- none ---' ) return;	// No watermark this time
	// Open the watermark file
	$watersize = @getimagesize($waterfile);
	if ( !is_array($watersize) ) return;	// Not a valid picture file
	$waterimage = imagecreatefrompng($waterfile);
	if ( empty( $waterimage ) or ( !$waterimage ) ) {
		wppa_dbg_msg('Watermark file '.$waterfile.' not found or corrupt');
		return;			// No image
	}
	imagealphablending($waterimage, false);
	imagesavealpha($waterimage, true);

		
	// Open the photo file
	$photosize = getimagesize($file);
	if ( !is_array($photosize) ) {
		return;	// Not a valid photo
	}
	switch ($photosize[2]) {
		case 1: $tempimage = imagecreatefromgif($file);
			$photoimage = imagecreatetruecolor($photosize[0], $photosize[1]);
			imagecopy($photoimage, $tempimage, 0, 0, 0, 0, $photosize[0], $photosize[1]);
			break;
		case 2: $photoimage = imagecreatefromjpeg($file);
			break;
		case 3: $photoimage = imagecreatefrompng($file);
			break;
	}
	if ( empty( $photoimage ) or ( !$photoimage ) ) return; 			// No image

	$ps_x = $photosize[0];
	$ps_y = $photosize[1];
	$ws_x = $watersize[0];
	$ws_y = $watersize[1];
	$src_x = 0;
	$src_y = 0;
	if ( $ws_x > $ps_x ) {
		$src_x = ($ws_x - $ps_x) / 2;
		$ws_x = $ps_x;
	}		
	if ( $ws_y > $ps_y ) {
		$src_y = ($ws_y - $ps_y) / 2;
		$ws_y = $ps_y;
	}
	
	$loy = substr( $waterpos, 0, 3);
	switch($loy) {
		case 'top': $dest_y = 0;
			break;
		case 'cen': $dest_y = ( $ps_y - $ws_y ) / 2;
			break;
		case 'bot': $dest_y = $ps_y - $ws_y;
			break;
		default: $dest_y = 0; 	// should never get here
	}
	$lox = substr( $waterpos, 3);
	switch($lox) {
		case 'lft': $dest_x = 0;
			break;
		case 'cen': $dest_x = ( $ps_x - $ws_x ) / 2;
			break;
		case 'rht': $dest_x = $ps_x - $ws_x;
			break;
		default: $dest_x = 0; 	// should never get here
	}

	wppa_imagecopymerge_alpha( $photoimage , $waterimage , $dest_x, $dest_y, $src_x, $src_y, $ws_x, $ws_y, intval($wppa_opt['wppa_watermark_opacity']) );

	// Save the result
	switch ($photosize[2]) {
		case 1: imagegif($photoimage, $file);
			break;
		case 2: imagejpeg($photoimage, $file, 100);
			break;
		case 3: imagepng($photoimage, $file, 0);
			break;
	}

	// Cleanup
	imagedestroy($photoimage);
	imagedestroy($waterimage);

}


/**
 * PNG ALPHA CHANNEL SUPPORT for imagecopymerge();
 * This is a function like imagecopymerge but it handle alpha channel well!!!
 **/

// A fix to get a function like imagecopymerge WITH ALPHA SUPPORT
// Main script by aiden dot mail at freemail dot hu
// Transformed to imagecopymerge_alpha() by rodrigo dot polo at gmail dot com
function wppa_imagecopymerge_alpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct){
    if(!isset($pct)){
        return false;
    }
    $pct /= 100;
    // Get image width and height
    $w = imagesx( $src_im );
    $h = imagesy( $src_im );
    // Turn alpha blending off
    imagealphablending( $src_im, false );
    // Find the most opaque pixel in the image (the one with the smallest alpha value)
    $minalpha = 127;
    for( $x = 0; $x < $w; $x++ )
    for( $y = 0; $y < $h; $y++ ){
        $alpha = ( imagecolorat( $src_im, $x, $y ) >> 24 ) & 0xFF;
        if( $alpha < $minalpha ){
            $minalpha = $alpha;
        }
    }
    //loop through image pixels and modify alpha for each
    for( $x = 0; $x < $w; $x++ ){
        for( $y = 0; $y < $h; $y++ ){
            //get current alpha value (represents the TANSPARENCY!)
            $colorxy = imagecolorat( $src_im, $x, $y );
            $alpha = ( $colorxy >> 24 ) & 0xFF;
            //calculate new alpha
            if( $minalpha !== 127 ){
                $alpha = 127 + 127 * $pct * ( $alpha - 127 ) / ( 127 - $minalpha );
            } else {
                $alpha += 127 * $pct;
            }
            //get the color index with new alpha
            $alphacolorxy = imagecolorallocatealpha( $src_im, ( $colorxy >> 16 ) & 0xFF, ( $colorxy >> 8 ) & 0xFF, $colorxy & 0xFF, $alpha );
            //set pixel with the new color + opacity
            if( !imagesetpixel( $src_im, $x, $y, $alphacolorxy ) ){
                return false;
            }
        }
    }
    // The image copy
    imagecopy($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h);
}

function wppa_watermark_file_select($default = false) {
global $wppa_opt;

	// Init
	$result = '';
	$user = wppa_get_user();
	
	// See what's in there
	$paths = WPPA_UPLOAD_PATH . '/watermarks/*.png';
	$files = glob($paths);
	
	// Find current selection
	$select = $wppa_opt['wppa_watermark_file'];	// default
	if ( !$default && get_option('wppa_watermark_user') == 'yes' && get_option('wppa_watermark_file_' . $user, 'nil') !== 'nil' ) {
		$select = get_option('wppa_watermark_file_' . $user);
	}
	
	// Produce the html
	$result .= '<option value="--- none ---">'.__('--- none ---', 'wppa').'</option>';
	if ( $files ) foreach ( $files as $file ) {
		$sel = $select == basename($file) ? 'selected="selected"' : '';
		$result .= '<option value="'.basename($file).'" '.$sel.'>'.basename($file).'</option>';
	}
	
	return $result;
}

function wppa_watermark_pos_select($default = false) {
global $wppa_opt;

	// Init
	$user = wppa_get_user();
	$result = '';
	$opt = array(	__('top - left', 'wppa'), __('top - center', 'wppa'), __('top - right', 'wppa'), 
					__('center - left', 'wppa'), __('center - center', 'wppa'), __('center - right', 'wppa'), 
					__('bottom - left', 'wppa'), __('bottom - center', 'wppa'), __('bottom - right', 'wppa'), );
	$val = array(	'toplft', 'topcen', 'toprht',
					'cenlft', 'cencen', 'cenrht',
					'botlft', 'botcen', 'botrht', );
	$idx = 0;

	// Find current selection
	$select = $wppa_opt['wppa_watermark_pos'];	// default
	if ( !$default && get_option('wppa_watermark_user') == 'yes' && get_option('wppa_watermark_pos_' . $user, 'nil') !== 'nil' ) {
		$select = get_option('wppa_watermark_pos_' . $user);
	}
	
	// Produce the html
	while ($idx < 9) {
		$sel = $select == $val[$idx] ? 'selected="selected"' : '';
		$result .= '<option value="'.$val[$idx].'" '.$sel.'>'.$opt[$idx].'</option>';
		$idx++;
	}
	
	return $result;
}

function wppa_table_exists($xtable) {
global $wpdb;

	$tables = $wpdb->get_results($wpdb->prepare("SHOW TABLES FROM `".DB_NAME."`"), 'ARRAY_A');
	// Some sqls do not show tables, benefit of the doubt: assume table exists
	if ( empty($tables) ) return true;
	
	// Normal check
	foreach ($tables as $table) {
		if ( is_array($table) )	foreach ( $table as $item ) {
			if ( strcasecmp($item, $xtable) == 0 ) return true;
		}
	}
	return false;
}

// Process the iptc data
function wppa_import_iptc($id, $info) {
global $wpdb;

	wppa_dbg_msg('wppa_import_iptc called for id='.$id);
	wppa_dbg_msg('array is'.( is_array($info) ? ' ' : ' NOT ' ).'available');
	wppa_dbg_msg('APP13 is '.( isset($info['APP13']) ? 'set' : 'NOT set'));
	
	// Is iptc data present?
	if ( !isset($info['APP13']) ) return false;	// No iptc data avail
//var_dump($info);
	// Parse
	$iptc = iptcparse($info['APP13']);
	if ( ! is_array($iptc) ) return false;		// No data avail 
	
	// There is iptc data for this image.
	// First delete any existing ipts data for this image
	$wpdb->query($wpdb->prepare("DELETE FROM `".WPPA_IPTC."` WHERE `photo` = %s", $id));
	// Find defined labels
	$result = $wpdb->get_results($wpdb->prepare("SELECT `tag` FROM `".WPPA_IPTC."` WHERE `photo`='0' ORDER BY `tag`"), 'ARRAY_N');
	if ( ! is_array($result) ) $result = array();
	$labels = array();
	foreach ($result as $res) {
		$labels[] = $res['0'];
	}
	foreach (array_keys($iptc) as $s) {
		if ( is_array($iptc[$s]) ) {
			$c = count ($iptc[$s]);
			for ($i=0; $i <$c; $i++) {
				// Process item
				wppa_dbg_msg('IPTC '.$s.' = '.$iptc[$s][$i]);
				// Check labels first
				if ( ! in_array( $s, $labels ) ) {
					$labels[] = $s;	// Add to labels
					// Add to db
					$key 	= wppa_nextkey(WPPA_IPTC);
					$photo 	= '0';
					$tag 	= $s;
					$desc 	= $s.':';
						if ( $s == '2#005' ) $desc = 'Graphic name:';
						if ( $s == '2#010' ) $desc = 'Urgency:';
						if ( $s == '2#015' ) $desc = 'Category:'; 
						if ( $s == '2#020' ) $desc = 'Supp categories:';
						if ( $s == '2#040' ) $desc = 'Spec instr:'; 
						if ( $s == '2#055' ) $desc = 'Creation date:';
						if ( $s == '2#080' ) $desc = 'Photographer:';
						if ( $s == '2#085' ) $desc = 'Credit byline title:';
						if ( $s == '2#090' ) $desc = 'City:';
						if ( $s == '2#095' ) $desc = 'State:';	
						if ( $s == '2#101' ) $desc = 'Country:';
						if ( $s == '2#103' ) $desc = 'Otr:';
						if ( $s == '2#105' ) $desc = 'Headline:';
						if ( $s == '2#110' ) $desc = 'Source:';
						if ( $s == '2#115' ) $desc = 'Photo source:'; 	
						if ( $s == '2#120' ) $desc = 'Caption:';
					$status = 'display';
						if ( $s == '1#090' ) $status = 'hide';
						if ( $s == '2#000' ) $status = 'hide';
					$query 	= $wpdb->prepare("INSERT INTO `".WPPA_IPTC."` (`id`, `photo`, `tag`, `description`, `status`) VALUES (%s, %s, %s, %s, %s)", $key, $photo, $tag, $desc, $status); 
					$iret 	= $wpdb->query($query);
					if ( ! $iret ) wppa_dbg_msg('Error: '.$query);
				}
				// Now add poto specific data item
				$key 	= wppa_nextkey(WPPA_IPTC);
				$photo 	= $id;
				$tag 	= $s;
				$desc 	= $iptc[$s][$i];
				$status = 'default';
				$query  = $wpdb->prepare("INSERT INTO `".WPPA_IPTC."` (`id`, `photo`, `tag`, `description`, `status`) VALUES (%s, %s, %s, %s, %s)", $key, $photo, $tag, $desc, $status); 
				$iret 	= $wpdb->query($query);
				if ( ! $iret ) wppa_dbg_msg('Error: '.$query);
			}
		}
	}
}

function wppa_import_exif($id, $file) {
global $wpdb;

	// Check filetype
	if ( ! function_exists('exif_imagetype') ) return false;
	$image_type = exif_imagetype($file);
	if ( $image_type != IMAGETYPE_JPEG ) return false;	// Not supported image type

	// Get exif data
	if ( ! function_exists('exif_read_data') ) return false;	// Not supported by the server
	$exif = @ exif_read_data($file, 'EXIF');
	if ( ! is_array($exif) ) return false;			// No data present
//var_dump($exif);
	// There is exif data for this image.
	// First delete any existing exif data for this image
	$wpdb->query($wpdb->prepare("DELETE FROM `".WPPA_EXIF."` WHERE `photo` = %s", $id));
	// Find defined labels
	$result = $wpdb->get_results($wpdb->prepare("SELECT * FROM `".WPPA_EXIF."` WHERE `photo`='0' ORDER BY `tag`"), 'ARRAY_A');
	if ( ! is_array($result) ) $result = array();
	$labels = array();
	$names  = array();
	foreach ($result as $res) {
		$labels[] = $res['tag'];
		$names[]  = $res['description'];
	}
	
	foreach (array_keys($exif) as $s) {
		// Process item
		wppa_dbg_msg('EXIF '.$s.' = '.$exif[$s]);
		
		// Check labels first
		$tag = '';
		if ( in_array( $s, $names ) ) {
			$i = 0;
			while ( $i < count($labels) ) {
				if ( $names[$i] == $s ) $tag = $labels[$i];
			}
		}
		if ( $tag == '' ) $tag = wppa_exif_tag($s);
		if ( $tag == '' ) continue;
		if ( ! in_array( $tag, $labels ) ) {
			$labels[] = $tag;	// Add to labels
			// Add to db
			$key 	= wppa_nextkey(WPPA_EXIF);
			$photo 	= '0';
			$desc 	= $s.':';
			$status = 'display';
			$query 	= $wpdb->prepare("INSERT INTO `".WPPA_EXIF."` (`id`, `photo`, `tag`, `description`, `status`) VALUES (%s, %s, %s, %s, %s)", $key, $photo, $tag, $desc, $status); 
			$iret 	= $wpdb->query($query);
			if ( ! $iret ) wppa_dbg_msg('Error: '.$query);
		}
		// Now add poto specific data item
		// If its an array...
		if ( is_array($exif[$s]) ) { // continue;
			$c = count ($exif[$s]);
			for ($i=0; $i <$c; $i++) {
				$key 	= wppa_nextkey(WPPA_EXIF);
				$photo 	= $id;
				$desc 	= $exif[$s][$i];
				$status = 'default';
				$query  = $wpdb->prepare("INSERT INTO `".WPPA_EXIF."` (`id`, `photo`, `tag`, `description`, `status`) VALUES (%s, %s, %s, %s, %s)", $key, $photo, $tag, $desc, $status); 
				$iret 	= $wpdb->query($query);
				if ( ! $iret ) wppa_dbg_msg('Error: '.$query);
			
			}
		}
		// Its not an array
		else {
			$key 	= wppa_nextkey(WPPA_EXIF);
			$photo 	= $id;
			$desc 	= $exif[$s];
			$status = 'default';
			$query  = $wpdb->prepare("INSERT INTO `".WPPA_EXIF."` (`id`, `photo`, `tag`, `description`, `status`) VALUES (%s, %s, %s, %s, %s)", $key, $photo, $tag, $desc, $status); 
			$iret 	= $wpdb->query($query);
			if ( ! $iret ) wppa_dbg_msg('Error: '.$query);
		}
	}
}

// Inverse of exif_tagname();
function wppa_exif_tag($tagname) {
global $wppa_inv_exiftags;

	// Setup inverted matrix
	if ( ! is_array($wppa_inv_exiftags) ) {
		$key = 0;
		while ( $key < 65536 ) {
			$tag = exif_tagname($key);
			if ( $tag != '' ) {
				$wppa_inv_exiftags[$tag] = $key;
			}
			$key++;
			if ( ! $key ) break;	// 16 bit server wrap around (do they still exist??? )
		}
	}
	// Search
	if ( isset($wppa_inv_exiftags[$tagname]) ) return sprintf('E#%04X',$wppa_inv_exiftags[$tagname]);
	elseif ( strlen($tagname) == 19 ) {
		if ( substr($tagname, 0, 12) == 'UndefinedTag') return 'E#'.substr($tagname, -4);
	}
	else return '';
}

// This function attemps to recover iptc and exif data from existing files in the wppa dir.
function wppa_recuperate_iptc_exif() {
global $wpdb;

	$iptc_count = '0';
	$exif_count = '0';
	$out = '';
	$files = glob( WPPA_UPLOAD_PATH.'/*.*' );
	if ( $files ) {
		foreach ( $files as $file ) {
			if ( is_file ($file) ) {					// Not a dir
				$attr = getimagesize($file, $info);
				if ( is_array($attr) ) {				// Is a picturefile
					if ( $attr[2] == IMAGETYPE_JPEG ) {	// Is a jpg
						$id = basename($file);
						$id = substr($id, 0, strpos($id, '.'));
						// Now we have $id, $file and $info
						if ( isset($info["APP13"]) ) {	// There is IPTC data
							$is_iptc = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM `".WPPA_IPTC."` WHERE `photo`=%s", $id));
							if ( ! $is_iptc ) { 	// No IPTC yet and there is: Recuperate
								wppa_import_iptc($id, $info);
								$iptc_count++;
							}						
						}
						$image_type = exif_imagetype($file);
						if ( $image_type == IMAGETYPE_JPEG ) {	// EXIF supported by server
							$is_exif = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM `".WPPA_EXIF."` WHERE `photo`=%s", $id));
							if ( ! $is_exif ) { 				// No EXIF yet
								$exif = exif_read_data($file, 'EXIF');
								if ( is_array($exif) )	{ 		// There is exif data present
									wppa_import_exif($id, $file);
									$exif_count++;
								}
							}						
						}						
					}					
				}
			}
		}
	}
	$out .= __(sprintf('%s photos with IPTC data and %s photos with EXIF data processed.', $iptc_count, $exif_count), 'wppa');
	return $out;
}

function wppa_clear_cache() {
global $cache_path;
	// If wp-super-cache is on board, clear cache
	if ( function_exists('prune_super_cache') ) {
		prune_super_cache( $cache_path . 'supercache/', true );
		prune_super_cache( $cache_path, true );
	}
}

function wppa_err_alert($msg) {
global $wppa;

	$fullmsg = '<script type="text/javascript" >jQuery(document).ready(function(e) { alert(\''.$msg.'\') } )</script>';
	if ( is_admin() ) echo $fullmsg;
	else $wppa['out'] .= $fullmsg;	
}
