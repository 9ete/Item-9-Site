<?php 
/* wppa-admin.php
* Package: wp-photo-album-plus
*
* Contains the admin menu and startups the admin pages
* Version 4.5.3
*
*/

/* CHECK INSTALLATION */
// Check setup
require_once 'wppa-setup.php';
add_action ( 'admin_init', 'wppa_setup' );

/* ADMIN MENU */
add_action( 'admin_menu', 'wppa_add_admin' );

function wppa_add_admin() {
	global $wp_roles;
	global $wpdb;

	// Make sure admin has access rights
	if ( current_user_can( 'administrator' ) ) {	
		$wp_roles->add_cap( 'administrator', 'wppa_admin' );
		$wp_roles->add_cap( 'administrator', 'wppa_upload' );
		$wp_roles->add_cap( 'administrator', 'wppa_import' );
		$wp_roles->add_cap( 'administrator', 'wppa_export' );
		$wp_roles->add_cap( 'administrator', 'wppa_settings' );
		$wp_roles->add_cap( 'administrator', 'wppa_potd' );
		$wp_roles->add_cap( 'administrator', 'wppa_comments' );
		$wp_roles->add_cap( 'administrator', 'wppa_help' );
	}
	
	// See if there are comments pending moderation
	$com_pending = '';
	$com_pending_count = $wpdb->get_var($wpdb->prepare( "SELECT COUNT(*) FROM ".WPPA_COMMENTS." WHERE status='pending'" ) );
	if ( $com_pending_count ) $com_pending = '<span class="update-plugins"><span class="plugin-count">'.$com_pending_count.'</span></span>';
	// See if there are uploads pending moderation
	$upl_pending = '';
	$upl_pending_count = $wpdb->get_var($wpdb->prepare( "SELECT COUNT(*) FROM ".WPPA_PHOTOS." WHERE status='pending'" ) );
	if ( $upl_pending_count ) $upl_pending = '<span class="update-plugins"><span class="plugin-count">'.$upl_pending_count.'</span></span>';
	// Compute total pending moderation
	$tot_pending = '';
	$tot_pending_count = '0';
	if ( current_user_can('administrator') ) $tot_pending_count += $com_pending_count;
	if ( current_user_can('wppa_admin') ) $tot_pending_count+= $upl_pending_count;	
	if ( $tot_pending_count ) $tot_pending = '<span class="update-plugins"><span class="plugin-count">'.'<b>'.$tot_pending_count.'</b>'.'</span></span>';

	$icon_url = WPPA_URL . '/images/camera16.png';
	
	// 				page_title        menu_title                                      capability    menu_slug          function      icon_url    position
	add_menu_page( 'WP Photo Album', __('Photo&thinsp;Albums', 'wppa').$tot_pending, 'wppa_admin', 'wppa_admin_menu', 'wppa_admin', $icon_url ); //,'10' );
	
	//                 parent_slug        page_title                             menu_title                             capability            menu_slug               function
	add_submenu_page( 'wppa_admin_menu',  __('Album Admin', 'wppa'),			 __('Album Admin', 'wppa').$upl_pending,'wppa_admin',        'wppa_admin_menu',      'wppa_admin' );
    add_submenu_page( 'wppa_admin_menu',  __('Upload Photos', 'wppa'),           __('Upload Photos', 'wppa'),          'wppa_upload',        'wppa_upload_photos',   'wppa_page_upload' );
	add_submenu_page( 'wppa_admin_menu',  __('Import Photos', 'wppa'),           __('Import Photos', 'wppa'),          'wppa_import',        'wppa_import_photos',   'wppa_page_import' );
	add_submenu_page( 'wppa_admin_menu',  __('Export Photos', 'wppa'),           __('Export Photos', 'wppa'),          'wppa_export',     	 'wppa_export_photos',   'wppa_page_export' );
    add_submenu_page( 'wppa_admin_menu',  __('Settings', 'wppa'),                __('Settings', 'wppa'),               'wppa_settings',      'wppa_options',         'wppa_page_options' );
	add_submenu_page( 'wppa_admin_menu',  __('Photo of the day Widget', 'wppa'), __('Photo of the day', 'wppa'),       'wppa_potd', 		 'wppa_photo_of_the_day', 'wppa_sidebar_page_options' );
	add_submenu_page( 'wppa_admin_menu',  __('Manage comments', 'wppa'),         __('Comments', 'wppa').$com_pending,  'wppa_comments',      'wppa_manage_comments', 'wppa_comment_admin' );
    add_submenu_page( 'wppa_admin_menu',  __('Help &amp; Info', 'wppa'),         __('Help &amp; Info', 'wppa'),        'wppa_help',          'wppa_help',            'wppa_page_help' );
}

/* ADMIN STYLES */
add_action( 'admin_init', 'wppa_admin_styles' );

function wppa_admin_styles() {
	wp_register_style( 'wppa_admin_style', WPPA_URL.'/wppa-admin-styles.css' );
	wp_enqueue_style( 'wppa_admin_style' );
}

/* ADMIN SCRIPTS */
add_action( 'admin_init', 'wppa_admin_scripts' );

function wppa_admin_scripts() {
	wp_register_script( 'wppa_upload_script', WPPA_URL.'/wppa-multifile-compressed.js' );
	wp_enqueue_script( 'wppa_upload_script' );
	wp_register_script( 'wppa_admin_script', WPPA_URL.'/wppa-admin-scripts.js' );
	wp_enqueue_script( 'wppa_admin_script' );
	wp_enqueue_script( 'jquery' );
}

/* ADMIN PAGE PHP's */

// Album admin page
function wppa_admin() {
	require_once 'wppa-album-admin-autosave.php';
	_wppa_admin();
}
// Upload admin page
function wppa_page_upload() {
	require_once 'wppa-upload.php';
	_wppa_page_upload();
}
// Import admin page
function wppa_page_import() {
	require_once 'wppa-upload.php';
	echo '<script type="text/javascript">/* <![CDATA[ */wppa_import = "'.__('Import', 'wppa').'"; wppa_update = "'.__('Update', 'wppa').'";/* ]]> */</script>';
	_wppa_page_import();
}
// Export admin page
function wppa_page_export() {
	require_once 'wppa-export.php';
	_wppa_page_export();
}
// Settings admin page
function wppa_page_options() {	
	require_once 'wppa-settings-autosave.php';
	_wppa_page_options();
}
// Photo of the day admin page
function wppa_sidebar_page_options() {
	require_once 'wppa-widget-admin.php';
	_wppa_sidebar_page_options();
}
// Comments admin page
function wppa_comment_admin() { 
	require_once 'wppa-comment-admin.php';
	_wppa_comment_admin();
}
// Help admin page
function wppa_page_help() {	
	require_once 'wppa-help.php';
	_wppa_page_help();
}

/* GENERAL ADMIN */

// General purpose admin functions
require_once 'wppa-admin-functions.php';
