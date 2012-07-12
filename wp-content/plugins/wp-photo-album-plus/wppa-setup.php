<?php 
/* wppa-setup.php
* Package: wp-photo-album-plus
*
* Contains all the setup stuff
* Version 4.6.1
*
*/

/* SETUP */
// It used to be: register_activation_hook(WPPA_FILE, 'wppa_setup');
// The activation hook is useless since wp does no longer call this hook after upgrade of the plugin
// this routine is now called at action admin_init, so also after initial install
// Additionally it can now output messages about success or failure
// Just for people that rely on the healing effect of de-activating and re-activating a plugin
// we still do a setup on activation by faking that we are not up yo rev, and so invoking
// the setup on the first admin_init event. This has the advantage that we can display messages
// instead of characters of unexpected output.
// register_activation_hook(WPPA_FILE, 'wppa_activate'); is in wppa.php
function wppa_activate() {
	$old_rev = get_option('wppa_revision', '100');
	$new_rev = $old_rev - '0.01';
	update_option('wppa_revision', $new_rev);
}
// Set force to true to re-run it even when on rev (happens in wppa-settings.php)
// Force will NOT redefine constants
function wppa_setup($force = false) {
global $silent;
	global $wpdb;
	global $wppa_revno;
	global $current_user;
	global $wppa;
	
	$old_rev = get_option('wppa_revision', '100');

	if ( $old_rev >= $wppa_revno && ! $force ) return; // Nothing to do here
	
	wppa_clear_cache();	// Clear wp supercache
	
	$wppa['error'] = false;	// Init no error
		
	$create_albums = "CREATE TABLE " . WPPA_ALBUMS . " (
					id bigint(20) NOT NULL, 
					name text NOT NULL, 
					description text NOT NULL, 
					a_order smallint(5) unsigned NOT NULL, 
					main_photo bigint(20) NOT NULL, 
					a_parent bigint(20) NOT NULL,
					p_order_by int unsigned NOT NULL,
					cover_linktype tinytext NOT NULL,
					cover_linkpage bigint(20) NOT NULL,
					owner text NOT NULL,
					timestamp tinytext NOT NULL,
					PRIMARY KEY  (id) 
					) DEFAULT CHARACTER SET utf8;";
					
	$create_photos = "CREATE TABLE " . WPPA_PHOTOS . " (
					id bigint(20) NOT NULL, 
					album bigint(20) NOT NULL, 
					ext tinytext NOT NULL, 
					name text NOT NULL, 
					description longtext NOT NULL, 
					p_order smallint(5) unsigned NOT NULL,
					mean_rating tinytext NOT NULL,
					linkurl text NOT NULL,
					linktitle text NOT NULL,
					linktarget tinytext NOT NULL,
					owner text NOT NULL,
					timestamp tinytext NOT NULL,
					status tinytext NOT NULL,
					rating_count bigint(20) NOT NULL default '0',
					PRIMARY KEY  (id) 
					) DEFAULT CHARACTER SET utf8;";

	$create_rating = "CREATE TABLE " . WPPA_RATING . " (
					id bigint(20) NOT NULL,
					photo bigint(20) NOT NULL,
					value smallint(5) NOT NULL,
					user text NOT NULL,
					PRIMARY KEY  (id)
					) DEFAULT CHARACTER SET utf8;";
					
	$create_comments = "CREATE TABLE " . WPPA_COMMENTS . " (
					id bigint(20) NOT NULL,
					timestamp tinytext NOT NULL,
					photo bigint(20) NOT NULL,
					user text NOT NULL,
					ip tinytext NOT NULL,
					email text NOT NULL,
					comment text NOT NULL,
					status tinytext NOT NULL,
					PRIMARY KEY  (id)	
					) DEFAULT CHARACTER SET utf8;";
					
	$create_iptc = "CREATE TABLE " . WPPA_IPTC . " (
					id bigint(20) NOT NULL,
					photo bigint(20) NOT NULL,
					tag tinytext NOT NULL,
					description text NOT NULL,
					status tinytext NOT NULL,
					PRIMARY KEY  (id)					
					) DEFAULT CHARACTER SET utf8;";

	$create_exif = "CREATE TABLE " . WPPA_EXIF . " (
					id bigint(20) NOT NULL,
					photo bigint(20) NOT NULL,
					tag tinytext NOT NULL,
					description text NOT NULL,
					status tinytext NOT NULL,
					PRIMARY KEY  (id)					
					) DEFAULT CHARACTER SET utf8;";
					
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	
	// Create or update db tables
	$tn = array( WPPA_ALBUMS, WPPA_PHOTOS, WPPA_RATING, WPPA_COMMENTS, WPPA_IPTC, WPPA_EXIF );
	$tc = array( $create_albums, $create_photos, $create_rating, $create_comments, $create_iptc, $create_exif );
	$idx = 0;
	while ($idx < 6) {
		$a0 = wppa_table_exists($tn[$idx]);
		dbDelta($tc[$idx]);
		$a1 = wppa_table_exists($tn[$idx]);
		if ( WPPA_DEBUG ) {
			if ( ! $a0 ) {
				if ( $a1 ) wppa_ok_message('Database table '.$tn[$idx].' created.');
				else wppa_error_message('Could not create database table '.$tn[$idx]);
			}
			else wppa_ok_message('Database table '.$tn[$idx].' updated.');
		}
		$idx++;
	}
	
	// Do the things dbdelta does not do.
	// Character set
	wppa_setup_query($wpdb->prepare( "ALTER TABLE " . WPPA_ALBUMS . " MODIFY name text CHARACTER SET utf8"));
	wppa_setup_query($wpdb->prepare( "ALTER TABLE " . WPPA_PHOTOS . " MODIFY name text CHARACTER SET utf8"));
	wppa_setup_query($wpdb->prepare( "ALTER TABLE " . WPPA_ALBUMS . " MODIFY description text CHARACTER SET utf8"));
	wppa_setup_query($wpdb->prepare( "ALTER TABLE " . WPPA_PHOTOS . " MODIFY description longtext CHARACTER SET utf8"));
	wppa_setup_query($wpdb->prepare( "ALTER TABLE " . WPPA_PHOTOS . " MODIFY linktitle text CHARACTER SET utf8"));
	wppa_setup_query($wpdb->prepare( "ALTER TABLE " . WPPA_COMMENTS . " MODIFY comment text CHARACTER SET utf8"));
	wppa_setup_query($wpdb->prepare( "ALTER TABLE " . WPPA_IPTC . " MODIFY description text CHARACTER SET utf8"));
	wppa_setup_query($wpdb->prepare( "ALTER TABLE " . WPPA_EXIF . " MODIFY description text CHARACTER SET utf8"));
	// Default values
	get_currentuserinfo();
	$user = $current_user->user_login;
	wppa_setup_query($wpdb->prepare( 'UPDATE `'.WPPA_ALBUMS.'` SET `owner` = %s WHERE `owner` = %s', $user, '' ));
	wppa_setup_query($wpdb->prepare( 'UPDATE `'.WPPA_ALBUMS.'` SET `cover_linktype` = %s WHERE `cover_linktype` = %s', 'content', '' ));
	wppa_setup_query($wpdb->prepare( 'UPDATE `'.WPPA_ALBUMS.'` SET `cover_linktype` = %s WHERE `cover_linkpage` = %s', 'none', '-1' ));
	wppa_setup_query($wpdb->prepare( 'UPDATE `'.WPPA_PHOTOS.'` SET `status` = %s WHERE `status` = %s', 'publish', '' ));
	wppa_setup_query($wpdb->prepare( 'UPDATE `'.WPPA_PHOTOS.'` SET `linktarget` = %s WHERE `linktarget` = %s', '_self', '' ));

	// Convert any changed and remove obsolete setting options
	if ( $old_rev > '100' ) {	// On update only
		if ( $old_rev <= '402' ) {
			wppa_convert_setting('wppa_coverphoto_left', 'no', 'wppa_coverphoto_pos', 'right');
			wppa_convert_setting('wppa_coverphoto_left', 'yes', 'wppa_coverphoto_pos', 'left');
		}
		if ( $old_rev <= '440' ) {
			wppa_convert_setting('wppa_fadein_after_fadeout', 'yes', 'wppa_animation_type', 'fadeafter');
			wppa_convert_setting('wppa_fadein_after_fadeout', 'no', 'wppa_animation_type', 'fadeover');
		}
		if ( $old_rev <= '450' ) {
			wppa_remove_setting('wppa_fadein_after_fadeout');
			wppa_copy_setting('wppa_show_bbb', 'wppa_show_bbb_widget');
			wppa_convert_setting('wppa_comment_use_gravatar', 'yes', 'wppa_comment_gravatar', 'mm');
			wppa_convert_setting('wppa_comment_use_gravatar', 'no', 'wppa_comment_gravatar', 'none');
			wppa_remove_setting('wppa_comment_use_gravatar');
			wppa_revalue_setting('wppa_start_slide', 'yes', 'run');
			wppa_revalue_setting('wppa_start_slide', 'no', 'still');
			wppa_rename_setting('wppa_accesslevel', 'wppa_accesslevel_admin');
			wppa_remove_setting('wppa_charset');
			wppa_remove_setting('wppa_chmod');
			wppa_remove_setting('wppa_coverphoto_left');
			wppa_remove_setting('wppa_2col_treshold');
			wppa_remove_setting('wppa_album_admin_autosave');
			wppa_remove_setting('wppa_doublethevotes');
			wppa_remove_setting('wppa_halvethevotes');
			wppa_remove_setting('wppa_lightbox_overlaycolor');
			wppa_remove_setting('wppa_lightbox_overlayopacity');
			wppa_remove_setting('wppa_multisite');
			wppa_remove_setting('wppa_set_access_by');
			wppa_remove_setting('wppa_accesslevel_admin');
			wppa_remove_setting('wppa_accesslevel_upload');
			wppa_remove_setting('wppa_accesslevel_sidebar');
		}
		if ( $old_rev <= '452') {
			wppa_copy_setting('wppa_fontfamily_numbar', 'wppa_fontfamily_numbar_active');
			wppa_copy_setting('wppa_fontsize_numbar', 'wppa_fontsize_numbar_active');
			wppa_copy_setting('wppa_fontcolor_numbar', 'wppa_fontcolor_numbar_active');
			wppa_copy_setting('wppa_fontweight_numbar', 'wppa_fontweight_numbar_active');
		}
		if ( $old_rev <= '455') {	// rating_count added to WPPA_PHOTOS
			$phs = $wpdb->get_results($wpdb->prepare('SELECT `id` FROM `'.WPPA_PHOTOS), 'ARRAY_A');
			if ($phs) foreach ($phs as $ph) {
				$cnt = $wpdb->get_var($wpdb->prepare('SELECT COUNT(*) FROM `'.WPPA_RATING.'` WHERE `photo` = %s', $ph['id']));
				$wpdb->query($wpdb->prepare('UPDATE `'.WPPA_PHOTOS.'` SET `rating_count` = %s WHERE `id` = %s', $cnt, $ph['id']));
			}
		}
	}
	
	// Set default values for new options
	wppa_set_defaults();					
	
	// Check required directories
	if ( ! wppa_check_dirs() ) $wppa['error'] = true;
		
	// Copy factory supplied watermarks
	$frompath = WPPA_PATH . '/watermarks';
	$watermarks = glob($frompath . '/*.png');
	if ( is_array($watermarks) ) {
		foreach ($watermarks as $fromfile) {
			$tofile = WPPA_UPLOAD_PATH . '/watermarks/' . basename($fromfile);
			@ copy($fromfile, $tofile);
		}
	}

	// Check if this update comes with a new wppa-theme.php and/or a new wppa-style.css
	// If so, produce message
	$key = '0';
	if ( $old_rev < '460' ) {		// theme changed since...
		$usertheme_old 	= ABSPATH.'wp-content/themes/'.get_option('template').'/wppa_theme.php';
		$usertheme 		= ABSPATH.'wp-content/themes/'.get_option('template').'/wppa-theme.php';
		if ( is_file( $usertheme ) || is_file( $usertheme_old ) ) $key += '2';
	}
	if ( $old_rev < '460' ) {		// css changed since...
		$userstyle_old 	= ABSPATH.'wp-content/themes/'.get_option('template').'/wppa_style.css';
		$userstyle 		= ABSPATH.'wp-content/themes/'.get_option('template').'/wppa-style.css';
		if ( is_file( $userstyle ) || is_file( $userstyle_old ) ) $key += '1';
	}
	if ( $key ) {
		$msg = '<center>' . __('IMPORTANT UPGRADE NOTICE', 'wppa') . '</center><br/>';
		if ($key == '1' || $key == '3') $msg .= '<br/>' . __('Please CHECK your customized WPPA-STYLE.CSS file against the newly supplied one. You may wish to add or modify some attributes. Be aware of the fact that most settings can now be set in the admin settings page.', 'wppa');
		if ($key == '2' || $key == '3') $msg .= '<br/>' . __('Please REPLACE your customized WPPA-THEME.PHP file by the newly supplied one, or just remove it from your theme directory. You may modify it later if you wish. Your current customized version is NOT compatible with this version of the plugin software.', 'wppa');
		wppa_ok_message($msg);
	}
	
	// Check if db is ok
	if ( ! wppa_check_database() ) $wppa['error'] = true;
	
	// Done!
	if ( ! $wppa['error'] ) {
		update_option('wppa_revision', $wppa_revno);	
		if ( WPPA_DEBUG ) {
			if ( is_multisite() ) {
				wppa_ok_message(sprintf(__('WPPA+ successfully updated in multi site mode to db version %s.', 'wppa'), $wppa_revno));
			}
			else {
				wppa_ok_message(sprintf(__('WPPA+ successfully updated in single site mode to db version %s.', 'wppa'), $wppa_revno));
			}
		}
	}
	else {
		if ( WPPA_DEBUG ) wppa_error_message(__('An error occurred during update', 'wppa'));
	}
}
function wppa_setup_query($query) {
global $wpdb;
global $wppa;
	if ( $wpdb->query($query) === false ) $wppa['error'] = true;
}
function wppa_convert_setting($oldname, $oldvalue, $newname, $newvalue) {
	if ( get_option($oldname, 'nil') == 'nil' ) return;	// no longer exists
	if ( get_option($oldname, 'nil') == $oldvalue ) update_option($newname, $newvalue);
}
function wppa_remove_setting($oldname) {
	if ( get_option($oldname, 'nil') != 'nil' ) delete_option($oldname);
}
function wppa_rename_setting($oldname, $newname) {
	if ( get_option($oldname, 'nil') == 'nil' ) return;	// no longer exists
	update_option($newname, get_option($oldname));
	delete_option($oldname);
}
function wppa_copy_setting($oldname, $newname) {
	if ( get_option($oldname, 'nil') == 'nil' ) return;	// no longer exists
	update_option($newname, get_option($oldname));
}
function wppa_revalue_setting($oldname, $oldvalue, $newvalue) {
	if ( get_option($oldname, 'nil') == $oldvalue ) update_option($oldname, $newvalue);
}

// Set default option values if the option does not exist.
// With $force = true, all options will be set to their default value.
function wppa_set_defaults($force = false) {
global $wppa_defaults;

	$npd = '
<a href="javascript://" onClick="jQuery(\'.wppa-dtl\').css(\'display\', \'block\'); jQuery(\'.wppa-more\').css(\'display\', \'none\'); wppaOvlResize();">
<div class="wppa-more">
Camera info
</div>
</a>
<a href="javascript://" onClick="jQuery(\'.wppa-dtl\').css(\'display\', \'none\'); jQuery(\'.wppa-more\').css(\'display\', \'block\'); wppaOvlResize();">
<div class="wppa-dtl" style="display:none;" >
Hide Camera info
</div>
</a>
<div class="wppa-dtl" style="display:none;">
<br />
<style>
.wppa-label { padding: 0 3px !important; border: none !important; }
.wppa-value { padding: 0 3px 0 12px !important; border:none !important; }
</style>
<table style="margin:0; border:none;" >
<tr><td class="wppa-label" >Date Time</td><td class="wppa-value" >E#0132</td></tr>
<tr><td class="wppa-label" >Camera</td><td class="wppa-value" >E#0110</td></tr>
<tr><td class="wppa-label" >Focal length</td><td class="wppa-value" >E#920A</td></tr>
<tr><td class="wppa-label" >F-Stop</td><td class="wppa-value" >E#829D</td></tr>
<tr><td class="wppa-label" >ISO Speed Rating</td><td class="wppa-value" >E#8827</td></tr>
<tr><td class="wppa-label" >Exposure program</td><td class="wppa-value" >E#8822</td></tr>
<tr><td class="wppa-label" >Metering mode</td><td class="wppa-value" >E#9207</td></tr>
<tr><td class="wppa-label" >Flash</td><td class="wppa-value" >E#9209</td></tr>
</table>
</div>';

	$wppa_defaults = array ( 'wppa_revision' 			=> '100',
	
						// Table I: Sizes
						// A System
						'wppa_colwidth' 				=> '640',	// 1
						'wppa_resize_on_upload' 		=> 'no',	// 2
						'wppa_resize_to'				=> '0',		// 3
						'wppa_min_thumbs' 				=> '1',		// 4
						'wppa_bwidth' 					=> '1',		// 5
						'wppa_bradius' 					=> '6',		// 6
						'wppa_box_spacing'				=> '8',		// 7
						// B Fullsize
						'wppa_fullsize' 				=> '640',	// 1
						'wppa_maxheight' 				=> '480',	// 2
						'wppa_enlarge' 					=> 'no',	// 3
						'wppa_fullimage_border_width' 	=> '',		// 4
						'wppa_numbar_max'				=> '10',	// 5
						// C Thumbnails
						'wppa_thumbsize' 				=> '100',		// 1
						'wppa_thumb_aspect'				=> '0:0:none',	// 2
						'wppa_tf_width' 				=> '100',		// 3
						'wppa_tf_height' 				=> '130',		// 4
						'wppa_tn_margin' 				=> '4',			// 5
						'wppa_thumb_auto' 				=> 'yes',		// 6
						'wppa_thumb_page_size' 			=> '0',			// 7
						'wppa_popupsize' 				=> '150',		// 8
						'wppa_use_thumbs_if_fit'		=> 'yes',		// 9
						// D Covers
						'wppa_max_cover_width'			=> '1024',	// 1
						'wppa_text_frame_height'		=> '54',	// 2
						'wppa_smallsize' 				=> '150',	// 3
						'wppa_album_page_size' 			=> '0',		// 4
						// E Rating & comments
						'wppa_rating_max'				=> '5',		// 1
						'wppa_rating_prec'				=> '2',		// 2
						'wppa_gravatar_size'			=> '40',	// 3
						// F Widgets
						'wppa_topten_count' 			=> '10',	// 1
						'wppa_topten_size' 				=> '86',	// 2
						'wppa_comment_count'			=> '10',	// 3
						'wppa_comment_size'				=> '86',	// 4
						'wppa_thumbnail_widget_count'	=> '10',	// 5
						'wppa_thumbnail_widget_size'	=> '86',	// 6
						// G Overlay
						'wppa_ovl_txt_lines'			=> '4',		// 

						// Table II: Visibility
						// A Breadcrumb
						'wppa_show_bread' 					=> 'yes',	// 1
						'wppa_bc_on_search'					=> 'yes',	// 2
						'wppa_bc_on_topten'					=> 'yes',	// 3
						'wppa_show_home' 					=> 'yes',	// 4
						'wppa_bc_separator' 				=> 'raquo',	// 5
						'wppa_bc_txt' 						=> htmlspecialchars('<span style="color:red; font_size:24px;">&bull;</span>'),	// 6
						'wppa_bc_url' 						=> wppa_get_imgdir().'arrow.gif',	// 7
						'wppa_pagelink_pos'					=> 'bottom',	// 8
						// B Slideshow
						'wppa_show_startstop_navigation' 	=> 'yes',		// 1
						'wppa_show_browse_navigation' 		=> 'yes',		// 2
						'wppa_filmstrip' 					=> 'yes',		// 3
						'wppa_film_show_glue' 				=> 'yes',		// 4
						'wppa_show_full_name' 				=> 'yes',		// 5
						'wppa_show_full_desc' 				=> 'yes',		// 6
						'wppa_rating_on' 					=> 'yes',		// 7
						'wppa_rating_display_type'			=> 'graphic',	// 8
						'wppa_show_avg_rating'				=> 'yes',		// 9
						'wppa_show_comments' 				=> 'yes',		// 10
						'wppa_comment_gravatar'				=> 'none',		// 11
						'wppa_comment_gravatar_url'			=> 'http://',	// 12
						'wppa_show_bbb'						=> 'no',		// 13
						'wppa_custom_on' 					=> 'no',		// 14
						'wppa_custom_content' 				=> '<div style="color:red; font-size:24px; font-weight:bold; text-align:center;">Hello world!</div>',	// 15
						'wppa_show_slideshownumbar'  		=> 'no',		// 16
						'wppa_show_iptc'					=> 'no',		// 17
						'wppa_show_exif'					=> 'no',		// 18
						'wppa_copyright_on'					=> 'yes',		// 19
						'wppa_copyright_notice'				=> __('<span style="color:red" >Warning: Do not upload copyrighted material!</span>', 'wppa'),	// 20
						// C Thumbnails
						'wppa_thumb_text_name' 				=> 'yes',	// 1
						'wppa_thumb_text_desc' 				=> 'yes',	// 2
						'wppa_thumb_text_rating' 			=> 'yes',	// 3
						'wppa_popup_text_name' 				=> 'yes',	// 4
						'wppa_popup_text_desc' 				=> 'yes',	// 5
						'wppa_popup_text_desc_strip'		=> 'no',	// 5.1
						'wppa_popup_text_rating' 			=> 'yes',	// 6
						'wppa_show_rating_count'			=> 'no',	// 7
						// D Covers
						'wppa_show_cover_text' 				=> 'yes',	// 1
						'wppa_enable_slideshow' 			=> 'yes',	// 2
						'wppa_show_slideshowbrowselink' 	=> 'yes',	// 3
						// E Widgets
						'wppa_show_bbb_widget'				=> 'no',	// 1
						// F Overlay
						'wppa_ovl_close_txt'				=> 'CLOSE',
						'wppa_ovl_theme'					=> 'black',

						// Table III: Backgrounds
						'wppa_bgcolor_even' 			=> '#eeeeee',
						'wppa_bcolor_even' 				=> '#cccccc',
						'wppa_bgcolor_alt' 				=> '#dddddd',
						'wppa_bcolor_alt' 				=> '#bbbbbb',
						'wppa_bgcolor_nav' 				=> '#dddddd',
						'wppa_bcolor_nav' 				=> '#bbbbbb',
						'wppa_bgcolor_namedesc' 		=> '#dddddd',
						'wppa_bcolor_namedesc' 			=> '#bbbbbb',
						'wppa_bgcolor_com' 				=> '#dddddd',
						'wppa_bcolor_com' 				=> '#bbbbbb',
						'wppa_bgcolor_img'				=> '#eeeeee',
						'wppa_bcolor_img'				=> '',
						'wppa_bgcolor_fullimg' 			=> '#cccccc',
						'wppa_bcolor_fullimg' 			=> '#777777',
						'wppa_bgcolor_cus'				=> '#dddddd',
						'wppa_bcolor_cus'				=> '#bbbbbb',
						'wppa_bgcolor_numbar'			=> '#cccccc',
						'wppa_bcolor_numbar'			=> '#cccccc',
						'wppa_bgcolor_numbar_active'	=> '#333333',
						'wppa_bcolor_numbar_active'	 	=> '#333333',
						'wppa_bgcolor_iptc'				=> '#dddddd',
						'wppa_bcolor_iptc' 				=> '#bbbbbb',
						'wppa_bgcolor_exif'				=> '#dddddd',
						'wppa_bcolor_exif' 				=> '#bbbbbb',

						// Table IV: Behaviour
						// A System
						'wppa_allow_ajax'				=> 'no',
						'wppa_use_photo_names_in_urls'	=> 'no',
						// B Full size and Slideshow
						'wppa_fullvalign' 				=> 'fit',
						'wppa_fullhalign' 				=> 'center',
						'wppa_start_slide' 				=> 'run',
						'wppa_animation_type'			=> 'fadeover',
						'wppa_slideshow_timeout'		=> '2500',
						'wppa_animation_speed' 			=> '800',
						'wppa_slide_pause'				=> 'no',
						'wppa_slide_wrap'				=> 'yes',
						'wppa_fulldesc_align'			=> 'center',
						'wppa_clean_pbr'				=> 'yes',
						// C Thumbnail
						'wppa_list_photos_by' 			=> '0',
						'wppa_list_photos_desc' 		=> 'no',
						'wppa_thumbtype' 				=> 'default',
						'wppa_thumbphoto_left' 			=> 'no',
						'wppa_valign' 					=> 'center',
						'wppa_use_thumb_opacity' 		=> 'yes',
						'wppa_thumb_opacity' 			=> '85',
						'wppa_use_thumb_popup' 			=> 'yes',
						// D Albums and covers
						'wppa_list_albums_by' 			=> '0',
						'wppa_list_albums_desc' 		=> 'no',
						'wppa_coverphoto_pos'			=> 'right',
						'wppa_use_cover_opacity' 		=> 'yes',
						'wppa_cover_opacity' 			=> '85',
						// E Rating
						'wppa_rating_login' 			=> 'yes',
						'wppa_rating_change' 			=> 'yes',
						'wppa_rating_multi' 			=> 'no',
						'wppa_rating_use_ajax'			=> 'no',
						'wppa_next_on_callback'			=> 'no',
						'wppa_star_opacity'				=> '20',
						// F Comments
						'wppa_comment_login' 			=> 'no',
						'wppa_comments_desc'			=> 'no',
						'wppa_comment_moderation'		=> 'logout',
						'wppa_comment_email_required'	=> 'yes',
						// G Overlay
						'wppa_ovl_opacity'				=> '80',
						'wppa_ovl_onclick'				=> 'none',
						'wppa_ovl_anim'					=> '300',
						
						// Table V: Fonts
						'wppa_fontfamily_title' 	=> '',
						'wppa_fontsize_title' 		=> '',
						'wppa_fontcolor_title' 		=> '',
						'wppa_fontweight_title'		=> 'bold',
						'wppa_fontfamily_fulldesc' 	=> '',
						'wppa_fontsize_fulldesc' 	=> '',
						'wppa_fontcolor_fulldesc' 	=> '',
						'wppa_fontweight_fulldesc'	=> 'normal',
						'wppa_fontfamily_fulltitle' => '',
						'wppa_fontsize_fulltitle' 	=> '',
						'wppa_fontcolor_fulltitle' 	=> '',
						'wppa_fontweight_fulltitle'	=> 'normal',
						'wppa_fontfamily_nav' 		=> '',
						'wppa_fontsize_nav' 		=> '',
						'wppa_fontcolor_nav' 		=> '',
						'wppa_fontweight_nav'		=> 'normal',
						'wppa_fontfamily_thumb' 	=> '',
						'wppa_fontsize_thumb' 		=> '',
						'wppa_fontcolor_thumb' 		=> '',
						'wppa_fontweight_thumb'		=> 'normal',
						'wppa_fontfamily_box' 		=> '',
						'wppa_fontsize_box' 		=> '',
						'wppa_fontcolor_box' 		=> '',
						'wppa_fontweight_box'		=> 'normal',
						'wppa_fontfamily_numbar' 	=> '',
						'wppa_fontsize_numbar' 		=> '',
						'wppa_fontcolor_numbar' 	=> '#777777',
						'wppa_fontweight_numbar'	=> 'normal',
						'wppa_fontfamily_numbar_active' 	=> '',
						'wppa_fontsize_numbar_active' 		=> '',
						'wppa_fontcolor_numbar_active' 	=> '#777777',
						'wppa_fontweight_numbar_active'	=> 'bold',

						
						// Table VI: Links
						'wppa_mphoto_linktype' 				=> 'photo',
						'wppa_mphoto_linkpage' 				=> '0',
						'wppa_mphoto_blank'					=> 'no',
						'wppa_mphoto_overrule'				=> 'no',
						
						'wppa_thumb_linktype' 				=> 'photo',
						'wppa_thumb_linkpage' 				=> '0',
						'wppa_thumb_blank'					=> 'no',
						'wppa_thumb_overrule'				=> 'no',
						
						'wppa_topten_widget_linktype' 		=> 'photo',
						'wppa_topten_widget_linkpage' 		=> '0',
						'wppa_topten_blank'					=> 'no',
						'wppa_topten_overrule'				=> 'no',
						
						'wppa_slideonly_widget_linktype' 	=> 'widget',
						'wppa_slideonly_widget_linkpage' 	=> '0',
						'wppa_sswidget_blank'				=> 'no',
						'wppa_sswidget_overrule'			=> 'no',

						'wppa_widget_linktype' 				=> 'album',
						'wppa_widget_linkpage' 				=> '0',
						'wppa_potd_blank'					=> 'no',
						'wppa_potdwidget_overrule'			=> 'no',

						'wppa_coverimg_linktype' 			=> 'same',
						'wppa_coverimg_linkpage' 			=> '0',
						'wppa_coverimg_blank'				=> 'no',
						'wppa_coverimg_overrule'			=> 'no',

						'wppa_comment_widget_linktype'		=> 'photo',
						'wppa_comment_widget_linkpage'		=> '0',
						'wppa_comment_blank'				=> 'no',
						'wppa_comment_overrule'				=> 'no',

						'wppa_slideshow_linktype'			=> 'none',
						'wppa_slideshow_blank'				=> 'no',
						'wppa_slideshow_overrule'			=> 'no',

						'wppa_thumbnail_widget_linktype'	=> 'photo',
						'wppa_thumbnail_widget_linkpage'	=> '0',
						'wppa_thumbnail_widget_overrule'	=> 'no',
						'wppa_thumbnail_widget_blank'		=> 'no',

						'wppa_film_linktype'				=> 'slideshow',
						
						// Table VII: Security
						// B
						'wppa_user_upload_login'	=> 'yes',
						'wppa_owner_only' 			=> 'no',
						'wppa_user_upload_on'		=> 'no',
						'wppa_upload_moderate'		=> 'no',
						'wppa_comment_captcha'		=> 'no',
						'wppa_spam_maxage'			=> 'none',
						
						// Table VIII: Actions
						// A Harmless
						'wppa_setup' 				=> '',
						'wppa_backup' 				=> '',
						'wppa_load_skin' 			=> '',
						'wppa_skinfile' 			=> 'default',
						'wppa_regen' 				=> '',
						'wppa_rerate'				=> '',
						'wppa_cleanup'				=> '',
						'wppa_recup'				=> '',
						// B Irreversable
						'wppa_rating_clear' 		=> 'no',
						'wppa_iptc_clear'			=> '',
						'wppa_exif_clear'			=> '',

						// Table IX: Miscellaneous
						// A System
						'wppa_html' 					=> 'no',		// 1
						'wppa_check_balance'			=> 'no',		// 2
						'wppa_allow_debug' 				=> 'no',		// 3
						'wppa_autoclean'				=> 'yes',		// 4
						'wppa_filter_priority'			=> '1001',		// 5
						'wppa_lightbox_name'			=> 'wppa',		// 6
						'wppa_allow_foreign_shortcodes' => 'no',		// 7
						'wppa_arrow_color' 				=> 'black',
						// B New
						'wppa_max_album_newtime'		=> '0',		// 1
						'wppa_max_photo_newtime'		=> '0',		// 2
						'wppa_apply_newphoto_desc'		=> 'no',	// 3
						'wppa_newphoto_description'		=> $npd,	// 4
						// C Search
						'wppa_search_linkpage' 			=> '0',		// 1
						'wppa_excl_sep' 				=> 'no',	// 2
						'wppa_photos_only'				=> 'no',	// 3
						// D Watermark
						'wppa_watermark_on'				=> 'no',
						'wppa_watermark_user'			=> 'no',
						'wppa_watermark_file'			=> 'specimen.png',
						'wppa_watermark_pos'			=> 'cencen',
						'wppa_watermark_upload'			=> '',
						'wppa_watermark_opacity'		=> '20',
						
						'wppa_slide_order'				=> '0,1,2,3,4,5,6,7,8,9',
						'wppa_swap_namedesc' 			=> 'no',

						// Photo of the day widget
						'wppa_widgettitle'			=> __('Photo of the day', 'wppa'),
						'wppa_widget_linkurl'		=> __('Type your custom url here', 'wppa'),
						'wppa_widget_linktitle' 	=> __('Type the title here', 'wppa'),
						'wppa_widget_subtitle'		=> 'none',
						'wppa_widget_album'			=> '0',
						'wppa_widget_photo'			=> '',
						'wppa_potd_align' 			=> 'center',
						'wppa_widget_method'		=> '1',
						'wppa_widget_period'		=> '168',
						'wppa_widget_width'			=> '200',
						
						// Topten widget
						'wppa_toptenwidgettitle'	=> __('Top Ten Photos', 'wppa'),

						// Thumbnail widget
						'wppa_thumbnailwidgettitle'	=> __('Thumbnail Photos', 'wppa'),
						
						// Search widget
						'wppa_searchwidgettitle'	=> __('Search photos', 'wppa'),
						
						// Comment admin
						'wppa_comadmin_show' 		=> 'all',
						'wppa_comadmin_order' 		=> 'timestamp'
								
						);

	array_walk($wppa_defaults, 'wppa_set_default', $force);
	
	// Check for upgrade right after conversion from old wppa
	if ( ! is_numeric(get_option('wppa_fullsize')) ) update_option('wppa_fullsize', '640');
	
	return true;
}
function wppa_set_default($value, $key, $force) {
	$void_these = array(
		'wppa_revision',
		'wppa_rating_max'
		);
							
	if ( $force ) {
		if ( ! in_array($key, $void_these) ) update_option($key, $value);
	}
	else {
		if ( get_option($key, 'nil') == 'nil' ) update_option($key, $value);
	}
}

// Check if the required directories exist, if not, try to create them and optionally report it
function wppa_check_dirs() {

	if ( ! is_multisite() ) {
		// check if uploads dir exists
		$dir = ABSPATH . 'wp-content/uploads';
		if ( ! is_dir($dir) ) {
			mkdir($dir);
			if ( ! is_dir($dir) ) {
				wppa_error_message(__('The uploads directory does not exist, please do a regular WP upload first.', 'wppa').'<br/>'.$dir);
				return false;
			}
			else {
				if ( WPPA_DEBUG ) wppa_ok_message(__('Successfully created uploads directory.', 'wppa').'<br/>'.$dir);
			}
		}
		@ chmod($dir, 0755);		
	}

	// check if wppa dir exists
	$dir = WPPA_UPLOAD_PATH;
	if ( ! is_dir($dir) ) {
		mkdir($dir);
		if ( ! is_dir($dir) ) {
			wppa_error_message(__('Could not create the wppa directory.', 'wppa').wppa_credirmsg($dir));
			return false;
		}
		else {
			if ( WPPA_DEBUG ) wppa_ok_message(__('Successfully created wppa directory.', 'wppa').'<br/>'.$dir);
		}
	}
	@ chmod($dir, 0755);
	
	// check if thumbs dir exists 
	$dir = WPPA_UPLOAD_PATH.'/thumbs';
	if ( ! is_dir($dir) ) {
		mkdir($dir);
		if ( ! is_dir($dir) ) {
			wppa_error_message(__('Could not create the wppa thumbs directory.', 'wppa').wppa_credirmsg($dir));
			return false;
		}
		else {
			if ( WPPA_DEBUG ) wppa_ok_message(__('Successfully created wppa thumbs directory.', 'wppa').'<br/>'.$dir);
		}
	}
	@ chmod($dir, 0755);

	// check if watermarks dir exists 
	$dir = WPPA_UPLOAD_PATH.'/watermarks';
	if ( ! is_dir($dir) ) {
		mkdir($dir);
		if ( ! is_dir($dir) ) {
			wppa_error_message(__('Could not create the wppa watermarks directory.', 'wppa').wppa_credirmsg($dir));
			return false;
		}
		else {
			if ( WPPA_DEBUG ) wppa_ok_message(__('Successfully created wppa watermarks directory.', 'wppa').'<br/>'.$dir);
		}
	}
	@ chmod($dir, 0755);
	
	// check if depot dir exists
	if ( ! is_multisite() ) {
		// check if users depot dir exists
		$dir = ABSPATH.'wp-content/wppa-depot';
		if ( ! is_dir($dir) ) {
			mkdir($dir);
			if ( ! is_dir($dir) ) {
				wppa_error_message(__('Unable to create depot directory.', 'wppa').wppa_credirmsg($dir));
				return false;
			}
			else {
				if ( WPPA_DEBUG ) wppa_ok_message(__('Successfully created wppa depot directory.', 'wppa').'<br/>'.$dir);
			}
		}
		@ chmod($dir, 0755);
	}
	
	// check the user depot directory
	$dir = WPPA_DEPOT_PATH;
	if ( ! is_dir($dir) ) {
		mkdir($dir);
		if ( ! is_dir($dir) ) {
			wppa_error_message(__('Unable to create user depot directory', 'wppa').wppa_credirmsg($dir));
			return false;
		}
		else {
			if ( WPPA_DEBUG ) wppa_ok_message(__('Successfully created wppa user depot directory.', 'wppa').'<br/>'.$dir);
		}
	}
	@ chmod($dir, 0755);
	
	return true;
}
function wppa_credirmsg($dir) {
	$msg = ' '.sprintf(__('Ask your administrator to give you more rights, try CHMOD from table VII item 1 of the Photo Albums -> Settings admin page or create <b>%s</b> manually using an FTP program.', 'wppa'), $dir);
	return $msg;
}
