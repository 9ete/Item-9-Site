<?php
/* wppa-adminfunctions.php
* Pachkage: wp-photo-album-plus
*
* gp admin functions
* version 4.5.5
*
* 
*/


function wppa_backup_settings() {
global $wppa_opt;
global $wppa_bu_err;
global $wppa;
	// Open file
	$fname = WPPA_DEPOT_PATH.'/settings.bak';
	if ($wppa['debug']) wppa_dbg_msg('Backing up to: '.$fname);
	
	$file = fopen($fname, 'wb');
	// Backup
	if ($file) {
		array_walk($wppa_opt, 'wppa_save_an_option', $file);
		// Close file
		fclose($file);
		if (!$wppa_bu_err) {
			wppa_ok_message(__('Settings successfully backed up', 'wppa'));
			return true;
		}
	}
	wppa_error_message(__('Unable to backup settings', 'wppa'));
	return false;
}
function wppa_save_an_option($value, $key, $file) {
global $wppa_bu_err;
	if (fwrite($file, $key.":".$value."\n") === false) {
		if ($wppa_bu_err !== true) {
			wppa_error_message(__('Error writing to settings backup file', 'wppa'));
			$wppa_bu_err = true;
		}	
	}
}

function wppa_restore_settings($fname, $type = '') {
global $wppa;

	if ($wppa['debug']) wppa_dbg_msg('Restoring from: '.$fname);
	if ( $type == 'skin' ) {
		$void_these = array(
							'wppa_revision', 
							'wppa_resize_on_upload', 
							'wppa_allow_debug', 
							'wppa_thumb_linkpage',
							'wppa_mphoto_linkpage',
							'wppa_widget_linkpage',
							'wppa_slideonly_widget_linkpage',
							'wppa_topten_widget_linkpage',
							'wppa_coverimg_linkpage',
							'wppa_search_linkpage',
							'permalink_structure',
							'wppa_rating_max'
							);
	}
	else {
		$void_these = array(
							'wppa_revision',
							'wppa_rating_max'
							);
	}
	
	// Open file
	$file = fopen($fname, 'r');
	// Restore
	if ($file) {
		$buffer = fgets($file, 4096);
		while (!feof($file)) {
			$buflen = strlen($buffer);
			if ($buflen > '0' && substr($buffer, 0, 1) != '/') {	// lines that start with '/' are comment
				$cpos = strpos($buffer, ':');
				$delta_l = $buflen - $cpos - 2;
				if ($cpos && $delta_l >= 0) {
					$slug = substr($buffer, 0, $cpos);
					$value = stripslashes(substr($buffer, $cpos+1, $delta_l));
					//wppa_dbg_msg('Doing|'.$slug.'|'.$value);
					if ( ! in_array($slug, $void_these)) update_option($slug, $value);
					else wppa_dbg_msg($slug.' skipped');
				}
			}
//else echo 'Comment: '.$buffer.'<br/>';
			
			$buffer = fgets($file, 4096);
		}
		fclose($file);
		wppa_initialize_runtime(true);
		return true;
	}
	else {
		wppa_error_message(__('Settings file not found', 'wppa'));
		return false;
	}
}

// update all thumbs 
function wppa_regenerate_thumbs() {
	global $wpdb;
	
	$thumbsize = wppa_get_minisize();

    $start = get_option('wppa_lastthumb', '-1');

	$photos = $wpdb->get_results($wpdb->prepare('SELECT * FROM `' . WPPA_PHOTOS . '` WHERE `id` > %s ORDER BY `id`', $start), 'ARRAY_A');
	
	if (!empty($photos)) {
		$count = count($photos);
		foreach ($photos as $photo) {
			$newimage = WPPA_UPLOAD_PATH.'/'.$photo['id'].'.'.$photo['ext'];
			wppa_create_thumbnail($newimage, $thumbsize, '' );
            update_option('wppa_lastthumb', $photo['id']);
			wppa_clear_cache();
            echo '.';
		}
	}		
}

// set last album 
function wppa_set_last_album($id = '') {
    global $albumid;
    global $current_user;
	
	get_currentuserinfo();

    if ( is_numeric($id) && wppa_have_access($id) ) $albumid = $id; else $albumid = '';
	$opt = 'wppa_last_album_used-'.$current_user->user_login;
    update_option($opt, $albumid);
}

// get last album
function wppa_get_last_album() {
    global $albumid;
    global $current_user;
	
	get_currentuserinfo();
    
    if (is_numeric($albumid)) $result = $albumid;
    else {
		$opt = 'wppa_last_album_used-'.$current_user->user_login;
		$result = get_option($opt, get_option('wppa_last_album_used', ''));
	}
    if (!is_numeric($result)) $result = '';
    else $albumid = $result;

	return $result; 
}

// display order options
function wppa_order_options($order, $nil, $rat = '', $timestamp = '') {
global $wppa_opt;
    if ($nil != '') { 
?>
    <option value="0"<?php if ($order == "" || $order == "0") echo (' selected="selected"'); ?>><?php echo($nil); ?></option>
<?php 
	}
?>
    <option value="1"<?php if ($order == "1") echo(' selected="selected"'); ?>><?php _e('Order #', 'wppa'); ?></option>
    <option value="2"<?php if ($order == "2") echo(' selected="selected"'); ?>><?php _e('Name', 'wppa'); ?></option>
    <option value="3"<?php if ($order == "3") echo(' selected="selected"'); ?>><?php _e('Random', 'wppa'); ?></option>  
<?php
	if ($rat != '') {
?>
	<option value="4"<?php if ($order == "4") echo(' selected="selected"'); if ( ! $wppa_opt['wppa_rating_on'] ) echo ('disabled="disabled"') ?>><?php echo($rat); ?></option>
<?php
	}
	if ($timestamp != '') {
?>
	<option value="5"<?php if ($order == "5") echo(' selected="selected"') ?>><?php echo ($timestamp) ?></option>
<?php
	}
}

// display usefull message
function wppa_update_message($msg, $fixed = false, $id = '') {
?>
    <div id="wppa-ms-<?php echo $id ?>" class="updated fade" <?php if ($fixed) echo 'style="position: fixed; width: 80%; text-align: center; text-weight:bold;"' ?>><p><strong><?php echo($msg); ?></strong></p></div>
<?php
}

// display error message
function wppa_error_message($msg, $fixed = false, $id = '') {
?>
	<div id="wppa-er-<?php echo $id ?>" class="error <?php if ($fixed == 'fixed') echo fade ?>" <?php if ($fixed == 'hidden') echo 'style="display:none;"'; if ($fixed == 'fixed') echo 'style="position: fixed;"' ?>><p><strong><?php echo($msg); ?></strong></p></div>
<?php
}
// display warning message
function wppa_warning_message($msg, $fixed = false, $id = '') {
?>
	<div id="wppa-wr-<?php echo $id ?>" class="updated <?php if ($fixed == 'fixed') echo fade ?>" <?php if ($fixed == 'hidden') echo 'style="display:none;"'; if ($fixed == 'fixed') echo 'style="position: fixed;"' ?>><p><strong><?php echo($msg); ?></strong></p></div>
<?php
}
// display ok message
function wppa_ok_message($msg, $fixed = false, $id = '') {
?>
	<div id="wppa-ok-<?php echo $id ?>" class="updated <?php if ($fixed == 'fixed') echo fade ?>" style="background-color: #e0ffe0; border-color: #55ee55;" ><p id="wppa-ok-p" ><strong><?php echo($msg); ?></strong></p></div>
<?php
}

function wppa_check_numeric($value, $minval, $target, $maxval = '') {
	if ($maxval == '') {
		if (is_numeric($value) && $value >= $minval) return true;
		wppa_error_message(__('Please supply a numeric value greater than or equal to', 'wppa') . ' ' . $minval . ' ' . __('for', 'wppa') . ' ' . $target);
	}
	else {
		if (is_numeric($value) && $value >= $minval && $value <= $maxval) return true;
		wppa_error_message(__('Please supply a numeric value greater than or equal to', 'wppa') . ' ' . $minval . ' ' . __('and less than or equal to', 'wppa') . ' ' . $maxval . ' ' . __('for', 'wppa') . ' ' . $target);
	}
	return false;
}

// check if albums 'exists'
function wppa_has_albums() {
	return wppa_have_access('any');
}

function wppa_get_users() {
global $wpdb;
	$users = $wpdb->get_results($wpdb->prepare( 'SELECT * FROM '.$wpdb->users ), 'ARRAY_A');
	return $users;
}

function wppa_user_select($select = '') {
	$result = '';
	$iam = $select == '' ? wppa_get_user() : $select;
	$users = wppa_get_users();
	$sel = $select == '--- public ---' ? 'selected="selected"' : '';
	$result .= '<option value="--- public ---" '.$sel.'>'.__('--- public ---', 'wppa').'</option>';
	foreach ($users as $usr) {
		if ($usr['user_login'] == $iam) $sel = 'selected="selected"';
		else $sel = '';
		$result .= '<option value="'.$usr['user_login'].'" '.$sel.'>'.$usr['display_name'].'</option>';
	}	
	echo ($result);
}

function wppa_copy_photo($photoid, $albumto) {
global $wpdb;

	$err = '1';
	// Check args
	if (!is_numeric($photoid) || !is_numeric($albumto)) return $err;
	
	$err = '2';
	// Find photo details
	$photo = $wpdb->get_row($wpdb->prepare( 'SELECT * FROM '.WPPA_PHOTOS.' WHERE id = %s', $photoid ), 'ARRAY_A');
	if (!$photo) return $err;
	$album = $albumto;
	$ext = $photo['ext'];
	$name = $photo['name'];
	$porder = '0';
	$desc = $photo['description'];
	$linkurl = $photo['linkurl'];
	$linktitle = $photo['linktitle'];
	$linktarget = $photo['linktarget'];
	$status = $photo['status'];
	$oldimage = WPPA_UPLOAD_PATH.'/'.$photo['id'].'.'.$ext;
	$oldthumb = WPPA_UPLOAD_PATH.'/thumbs/'.$photo['id'].'.'.$ext;
	
	$err = '3';
	// Make new db table entry
	$id = wppa_nextkey(WPPA_PHOTOS);
	$owner = wppa_get_user();
	$query = $wpdb->prepare('INSERT INTO `' . WPPA_PHOTOS . '` (`id`, `album`, `ext`, `name`, `p_order`, `description`, `mean_rating`, `linkurl`, `linktitle`, `linktarget`, `timestamp`, `owner`, `status`) VALUES (%s, %s, %s, %s, %s, %s, \'\', %s, %s, %s, %s, %s, %s)', $id, $album, $ext, $name, $porder, $desc, $linkurl, $linktitle, $linktarget, time(), $owner, $status);
	if ($wpdb->query($query) === false) return $err;

	$err = '4';
	// Find copied photo details
	$image_id = $id;			
	$newimage = WPPA_UPLOAD_PATH.'/'.$image_id.'.'.$ext;
	$newthumb = WPPA_UPLOAD_PATH.'/thumbs/'.$image_id.'.'.$ext;
	if (!$image_id) return $err;
	
	$err = '5';
	// Do the filsystem copy
	if (!copy($oldimage, $newimage)) return $err;
	$err = '6';
	if (!copy($oldthumb, $newthumb)) return $err;
	
	return false;	// No error
}

function wppa_rotate($id, $ang) {
global $wpdb;

	// Check args
	$err = '1';
	if (!is_numeric($id) || !is_numeric($ang)) return $err;
	
	// Get the ext
	$err = '2';
	$ext = $wpdb->get_var($wpdb->prepare( 'SELECT ext FROM '.WPPA_PHOTOS.' WHERE id = %s', $id ) );
	if (!$ext) return $err;
	
	// Get the image
	$err = '3';
	$file = WPPA_UPLOAD_PATH.'/'.$id.'.'.$ext;
	if (!is_file($file)) return $err;
	
	// Get the imgdetails
	$err = '4';
	$img = getimagesize($file);
	if (!$img) return $err;
	
	// Get the image
	switch ($img[2]) {
		case 1:	// gif
			$err = '5';
			$source = imagecreatefromgif($file);
			break;
		case 2: // jpg
			$err = '6';
			$source = imagecreatefromjpeg($file);
			break;
		case 3: // png
			$err = '7';
			$source = imagecreatefrompng($file);
			break;
		default: // unsupported mimetype
			$err = '10';
			$source = false;	
	}
	if (!$source) return $err;

	// Rotate the image
	$err = '11';
	$rotate = imagerotate($source, $ang, 0);
	if (!$rotate) return $err;
	
	// Save the image
	switch ($img[2]) {
		case 1:
			$err = '15';
			$bret = imagegif($rotate, $file, 95);
			break;
		case 2:
			$err = '16';
			$bret = imagejpeg($rotate, $file);
			break;
		case 3:
			$err = '17';
			$bret = imagepng($rotate, $file);
			break;
		default:
			$err = '20';
			$bret = false;
	}
	if (!$bret) return $err;
	
	// Destroy the source
	imagedestroy($source);
	// Destroy the result
	imagedestroy($rotate);

	// Recreate the thumbnail
	$err = '30';
	$thumbsize = wppa_get_minisize();
	$bret = wppa_create_thumbnail($file, $thumbsize, '' );
	if (!$bret) return $err;
	
	// Return success
	return false;
}


// Remove photo entries that have no fullsize image or thumbnail
// Additionally check the php config
function wppa_cleanup_photos($alb = '') {
	global $wpdb;
	global $wppa_opt;
	global $wppa_error_displayed;
//echo('WPPADBG'.$alb);
if ( is_multisite() ) return; // temp disabled for 4.0 bug, must be tested in a real multisite first before enabling
	
	// Check the users php config. sometimes a user 'reconfigures' his server to not having GD support...
	if ( ! function_exists('getimagesize') || ! function_exists('imagecreatefromjpeg') ) {
		if ( ! $wppa_error_displayed ) {
			wppa_error_message(__('Please check your php configuration. Currently it does not support the required functionality to manipulate photos', 'wppa'));
			$wppa_error_displayed = true;
		}
	}

	if ($alb == '') $alb = wppa_get_last_album();
	if (!is_numeric($alb)) return;

	$no_photos = '';
//	if ($alb == '0') wppa_ok_message(__('Checking database, please wait...', 'wppa'));
	$delcount = 0;
	if ($alb == '0') $entries = $wpdb->get_results($wpdb->prepare( 'SELECT id, ext, name FROM '.WPPA_PHOTOS ), ARRAY_A);
	else $entries = $wpdb->get_results($wpdb->prepare( 'SELECT id, ext, name FROM '.WPPA_PHOTOS.' WHERE album = %s', $alb ), ARRAY_A);
	if ($entries) {
		foreach ( $entries as $entry ) {
			$thumbpath = WPPA_UPLOAD_PATH.'/thumbs/'.$entry['id'].'.'.$entry['ext'];
			$imagepath = WPPA_UPLOAD_PATH.'/'.$entry['id'].'.'.$entry['ext'];
			if ( !is_file($thumbpath) ) {	// No thumb: delete fullimage conditionally 
				if ( $wppa_opt['wppa_autoclean'] == 'yes' ) {
					if (is_file($imagepath)) unlink($imagepath);
				}
				else {
					wppa_dbg_msg('Error: expected thumbnail image file does not exist: '.$thumbpath, 'red', true);
				}
			}
			if ( !is_file($imagepath) ) { // No fullimage: delete db entry
				if ( $wppa_opt['wppa_autoclean'] == 'yes' ) {
					if ($wpdb->query($wpdb->prepare('DELETE FROM `'.WPPA_PHOTOS.'` WHERE `id` = %s LIMIT 1', $entry['id']))) {
						$no_photos .= ' '.$entry['name'];
						$delcount++;
					}
				}
				else {
					wppa_dbg_msg('Error: expected fullsize image file does not exist: '.$thumbpath, 'red', true);
					wppa_dbg_msg('Please delete photo '.$entry['name'].' with id='.$entry['id'], 'red', true);
				}
			}
		}
	}
	// Now fix missing exts for upload bug in 2.3.0
	$fixcount = 0;
	$entries = $wpdb->get_results($wpdb->prepare( 'SELECT id, ext, name FROM '.WPPA_PHOTOS.' WHERE ext = ""' ), 'ARRAY_A' );
	if ($entries) {
		wppa_ok_message(__('Trying to fix '.count($entries).' entries with missing file extension, Please wait.', 'wppa'));
		foreach ($entries as $entry) {
			$tp = WPPA_UPLOAD_PATH.'/'.$entry['id'].'.';
			// Try the name
			$ext = substr(strrchr($entry['name'], "."), 1);
			if (!($ext == 'jpg' || $ext == 'JPG' || $ext == 'png' || $ext == 'PNG' || $ext == 'gif' || $ext == 'GIF')) {
				$ext = '';
			}
			if ($ext == '' && is_file($tp)) {
			// Try the type from the file
				$img = getimagesize($tp);
				if ($img[2] == 1) $ext = 'gif';
				if ($img[2] == 2) $ext = 'jpg';
				if ($img[2] == 3) $ext = 'png';
			}
			if ($ext == 'jpg' || $ext == 'JPG' || $ext == 'png' || $ext == 'PNG' || $ext == 'gif' || $ext == 'GIF') {
				
				if ($wpdb->query($wpdb->prepare( 'UPDATE '.WPPA_PHOTOS.' SET ext = "%s" WHERE id = %s', $ext, $entry['id'] ) ) ) {
					$oldimg = WPPA_UPLOAD_PATH.'/'.$entry['id'].'.';
					$newimg = WPPA_UPLOAD_PATH.'/'.$entry['id'].'.'.$ext;
					if (is_file($oldimg)) {
						copy($oldimg, $newimg);
						unlink($oldimg);
					}
					$oldimg = WPPA_UPLOAD_PATH.'/thumbs/'.$entry['id'].'.';
					$newimg = WPPA_UPLOAD_PATH.'/thumbs/'.$entry['id'].'.'.$ext;
					if (is_file($oldimg)) {
						copy($oldimg, $newimg);
						unlink($oldimg);
					}
					$fixcount++;
					wppa_ok_message(__('Fixed extension for ', 'wppa').$entry['name']);
				}
				else {
					wppa_error_message(__('Unable to fix extension for ', 'wppa').$entry['name']);
				}
			}
			else {
				wppa_error_message(__('Unknown extension for photo ', 'wppa').$entry['name'].'. '.__('Please change the name to something with the proper extension and try again!', 'wppa'));
			}
		}	
	}
	
	// Now fix orphan photos
	$orphcount = 0;
	$entries = $wpdb->get_results($wpdb->prepare( 'SELECT id FROM '.WPPA_PHOTOS.' WHERE album = 0' ), ARRAY_A);
	if ($entries) {
		$album = wppa_get_album_id(__('Orphan Photos', 'wppa'));
		if ($album == '') {
			$key = wppa_nextkey(WPPA_ALBUMS);
			$query = $wpdb->prepare('INSERT INTO `' . WPPA_ALBUMS . '` (`id`, `name`, `description`, `a_order`, `a_parent`, `p_order_by`, `main_photo`, `cover_linktype`, `cover_linkpage`, `owner`, `timestamp`) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)', $key, __('Orphan Photos', 'wppa'), '', '0', '0', '0', '0', 'content', '0', 'admin', time());
			$iret = $wpdb->query($query);
			if ($iret === false) {
				wppa_error_message('Could not create album: Orphan Photos', 'wppa');
			}
			else {
				wppa_ok_message('Album: Orphan Photos created.', 'wppa');
			}
			$album = wppa_get_album_id(__('Orphan Photos', 'wppa')); // retry
		}
		if ($album) {
			$orphcount = $wpdb->query($wpdb->prepare( 'UPDATE '.WPPA_PHOTOS.' SET album = %s WHERE album < 1', $album ) );
		}
		else {
			wppa_error_message(__('Could not recover orphanized photos.', 'wppa'));
		}
	}

	// End fix
	if ($orphcount > 0){
		wppa_ok_message(__('Database fixed.', 'wppa').' '.$orphcount.' '.__('orphanized photos recovered.', 'wppa'));
	}
	if ($delcount > 0){
		wppa_ok_message(__('Database fixed.', 'wppa').' '.$delcount.' '.__('invalid entries removed:', 'wppa').$no_photos);
	}
	if ($fixcount > 0) {
		wppa_ok_message(__('Database fixed.', 'wppa').' '.$fixcount.' '.__('missing file extensions recovered.', 'wppa'));
	}
		if ($alb == '0' && $delcount == 0 && $fixcount == 0) {
//		wppa_ok_message(__('Done. No errors found. Have a nice upload!', 'wppa'));
	}
}


function wppa_walktree($relroot, $source, $allowwppa = false, $subdirsonly = false) {

	if ( !$subdirsonly ) {
		if ($relroot == $source) $sel=' selected="selected"'; else $sel = ' ';
		echo('<option value="'.$relroot.'"'.$sel.'>'.$relroot.'</option>');
	}
	
	if ($handle = opendir(ABSPATH.$relroot)) {
		while (false !== ($file = readdir($handle))) {
			if ( $file != "." && $file != ".." && ( $file != "wppa" || $allowwppa ) ) {
				$newroot = $relroot.'/'.$file;
				if (is_dir(ABSPATH.$newroot)) {	
					wppa_walktree($newroot, $source);
				}
			}
		}
		closedir($handle);
	}
}

function wppa_sanitize_files() {

	// Get this users depot directory
	$depot = WPPA_DEPOT_PATH;
	__wppa_sanitize_files($depot);
}

function __wppa_sanitize_files($root) {
	// See what's in there
	$allowed_types = array('zip', 'jpg', 'png', 'gif', 'amf', 'pmf', 'bak');

	$paths = $root.'/*';
	$files = glob($paths);

	$count = '0';
	if ($files) foreach ($files as $file) {
		if (is_file($file)) {
			$ext = strtolower(substr(strrchr($file, "."), 1));
			if (!in_array($ext, $allowed_types)) {
				unlink($file);
				wppa_error_message(sprintf(__('File %s is of an unsupported filetype and has been removed.', 'wppa'), basename($file)));
				$count++;
			}
		}
		elseif (is_dir($file)) {
			$entry = basename($file);
			if ( $entry != '.' && $entry != '..' ) {
				__wppa_sanitize_files($file);
			}
		}
	}
	return $count;
}

// get select form element listing albums 
function wppa_album_select($exc = '', $sel = '', $addnone = FALSE, $addseparate = FALSE, $checkancestors = FALSE, $none_is_all = false, $none_is_blank = false ) {
	global $wpdb;
	$albums = $wpdb->get_results($wpdb->prepare( "SELECT * FROM ".WPPA_ALBUMS." ORDER BY name" ), 'ARRAY_A');
	
    if ($sel == '') {
        $s = wppa_get_last_album();
        if ($s != $exc) $sel = $s;
    }
    
    $result = '';
    if ($addnone) {
		if ($none_is_blank) $result .= '<option value="0"></option>';
		elseif ($none_is_all) $result .= '<option value="0">' . __('--- all ---', 'wppa') . '</option>';
		else $result .= '<option value="0">' . __('--- none ---', 'wppa') . '</option>';
	}
    
	foreach ($albums as $album) if (wppa_have_access($album)) {
		if ($sel == $album['id']) { 
            $selected = ' selected="selected" '; 
        } 
        else { $selected = ''; }
		if ($album['id'] != $exc && (!$checkancestors || !wppa_is_ancestor($exc, $album['id']))) {
			$result .= '<option value="' . $album['id'] . '"' . $selected . '>'.wppa_qtrans(stripslashes($album['name'])).'</option>';
		}
		else {
			$result .= '<option disabled="disabled" value="-3">'.wppa_qtrans(stripslashes($album['name'])).'</option>';
		}
	}
    
    if ($sel == -1) $selected = ' selected="selected" '; else $selected = '';
    if ($addseparate) $result .= '<option value="-1"' . $selected . '>' . __('--- separate ---', 'wppa') . '</option>';
	return $result;
}

function wppa_recalculate_ratings() {
global $wpdb;

	$photos = $wpdb->get_results($wpdb->prepare( 'SELECT id FROM '.WPPA_PHOTOS ), 'ARRAY_A');
	if ($photos) {
		foreach ($photos as $photo) {
			$ratings = $wpdb->get_results($wpdb->prepare( 'SELECT value FROM '.WPPA_RATING.' WHERE photo = %s', $photo['id']), 'ARRAY_A');
			$the_value = '0';
			$the_count = '0';
			if ( $ratings ) foreach ($ratings as $rating) {
				$the_value += $rating['value'];
				$the_count++;
			}
			if ($the_count) $the_value /= $the_count;
			if ($the_value == '10') $the_value = '9.99999';	// mean_rating is a text field. for sort order reasons we make 10 into 9.99999
			$iret = $wpdb->query($wpdb->prepare( 'UPDATE '.WPPA_PHOTOS.' SET mean_rating = %s WHERE id = %s', $the_value, $photo['id'] ) );
			if ($iret === false) {
				if ( $wppa['ajax'] ) {
					$wppa['error'] = true;
					$wppa['out'] = __('Unable to update mean rating', 'wppa');
				}
				else {
					wppa_error_message(__('Unable to update mean rating', 'wppa'));
				}
				return false;
			}
			$ratcount = count($ratings);
			$iret = $wpdb->query($wpdb->prepare( 'UPDATE '.WPPA_PHOTOS.' SET rating_count = %s WHERE id = %s', $ratcount, $photo['id'] ) );
		}
		return true;
	}
	else {
		if ( $wppa['ajax'] ) {
			$wppa['error'] = true;
			$wppa['out'] = __('No photos or error reading', 'wppa').WPPA_PHOTOS;
		}
		else {
			wppa_error_message(__('No photos or error reading', 'wppa').WPPA_PHOTOS);
		}
		return false;
	}
}

function wppa_check_database($verbose = false) {
global $wpdb;

	$any_error = false;
	// Check db tables
	// This is to test if dbdelta did his job in adding tables and columns
	$tn = array( WPPA_ALBUMS, WPPA_PHOTOS, WPPA_RATING, WPPA_COMMENTS, WPPA_IPTC, WPPA_EXIF );
	$flds = array( 	WPPA_ALBUMS => array(	'id' => 'bigint(20) NOT NULL', 
											'name' => 'text NOT NULL', 
											'description' => 'text NOT NULL', 
											'a_order' => 'smallint(5) unsigned NOT NULL', 
											'main_photo' => 'bigint(20) NOT NULL', 
											'a_parent' => 'bigint(20) NOT NULL',
											'p_order_by' => 'int unsigned NOT NULL',
											'cover_linktype' => 'tinytext NOT NULL',
											'cover_linkpage' => 'bigint(20) NOT NULL',
											'owner' => 'text NOT NULL',
											'timestamp' => 'tinytext NOT NULL'											
										), 
					WPPA_PHOTOS => array(	'id' => 'bigint(20) NOT NULL', 
											'album' => 'bigint(20) NOT NULL', 
											'ext' => 'tinytext NOT NULL', 
											'name' => 'text NOT NULL', 
											'description' => 'longtext NOT NULL', 
											'p_order' => 'smallint(5) unsigned NOT NULL',
											'mean_rating' => 'tinytext NOT NULL',
											'linkurl' => 'text NOT NULL',
											'linktitle' => 'text NOT NULL',
											'linktarget' => 'tinytext NOT NULL',
											'owner' => 'text NOT NULL',
											'timestamp' => 'tinytext NOT NULL',
											'status' => 'tinytext NOT NULL',
											'rating_count' => "bigint(20) default '0'"
										), 
					WPPA_RATING => array(	'id' => 'bigint(20) NOT NULL',
											'photo' => 'bigint(20) NOT NULL',
											'value' => 'smallint(5) NOT NULL',
											'user' => 'text NOT NULL'
										), 
					WPPA_COMMENTS => array(
											'id' => 'bigint(20) NOT NULL',
											'timestamp' => 'tinytext NOT NULL',
											'photo' => 'bigint(20) NOT NULL',
											'user' => 'text NOT NULL',
											'ip' => 'tinytext NOT NULL',
											'email' => 'text NOT NULL',
											'comment' => 'text NOT NULL',
											'status' => 'tinytext NOT NULL'
										), 
					WPPA_IPTC => array(
											'id' => 'bigint(20) NOT NULL',
											'photo' => 'bigint(20) NOT NULL',
											'tag' => 'tinytext NOT NULL',
											'description' => 'text NOT NULL',
											'status' => 'tinytext NOT NULL'
										), 
					WPPA_EXIF => array(
											'id' => 'bigint(20) NOT NULL',
											'photo' => 'bigint(20) NOT NULL',
											'tag' => 'tinytext NOT NULL',
											'description' => 'text NOT NULL',
											'status' => 'tinytext NOT NULL'
										) 
				);
	$errtxt = '';
	$idx = 0;
	while ($idx < 6) {
		// Test existence of table
		$ext = wppa_table_exists($tn[$idx]);
		if ( ! $ext ) {
			if ($verbose) wppa_error_message(__('Unexpected error:', 'wppa').' '.__('Missing database table:', 'wppa').' '.$tn[$idx], 'red', 'force');
			$any_error = true;
		}
		// Test columns
		else {
			$tablefields = $wpdb->get_results("DESCRIBE {$tn[$idx]};", "ARRAY_A");
			// unset flags for found fields
			foreach ( $tablefields as $field ) {					
				if ( isset( $flds[$tn[$idx]][$field['Field']] )) unset( $flds[$tn[$idx]][$field['Field']] );
			}
			// Fields left?
			if ( is_array($flds[$tn[$idx]]) ) foreach ( array_keys($flds[$tn[$idx]]) as $field ) {
				$errtxt .= '<tr><td>'.$tn[$idx].'</td><td>'.$field.'</td><td>'.$flds[$tn[$idx]][$field].'</td></tr>';
			}
		}
		$idx++;
	}
	if ( $errtxt ) {
		$fulltxt = 'The latest update failed to update the database tables required for wppa+ to function properly<br /><br />';
		$fulltxt .= 'Make sure you have the rights to issue SQL commands like <i>"ALTER TABLE tablename ADD COLUMN columname datatype"</i> and run the action on <i>Table VII-A1</i> on the Photo Albums -> Settings admin page.<br /><br />';
		$fulltxt .= 'The following table lists the missing columns:';
		$fulltxt .= '<br /><table id="wppa-err-table"><thead style="font-weight:bold;"><tr><td>Table name</td><td>Column name</td><td>Data type</td></thead>';
		$fulltxt .= $errtxt;
		$fulltxt .= '</table><b>';
		if ($verbose) wppa_error_message( $fulltxt, 'red', 'force' );
		$any_error = true;
	}
	// Check directories
	$dn = array( ABSPATH.WPPA_UPLOAD, WPPA_UPLOAD_PATH, WPPA_UPLOAD_PATH.'/thumbs', WPPA_DEPOT_PATH);
	$idx = 0;
	while ($idx < 4) {
		if ( ! file_exists($dn[$idx]) ) {	// First try to repair
			@ mkdir($dn[$idx]);
			@ chmod($dn[$idx], 0755);
		}
		else {
			@ chmod($dn[$idx], 0755);		// there are always people who destruct things
		}
		
		if ( ! file_exists($dn[$idx]) ) {	// Test again
			if ($verbose) wppa_error_message(__('Unexpected error:', 'wppa').' '.__('Missing directory:', 'wppa').' '.$dn[$idx], 'red', 'force');
			$any_error = true;
		}
		elseif ( ! is_writable($dn[$idx]) ) {
			if ($verbose) wppa_error_message(__('Unexpected error:', 'wppa').' '.__('Directory is not writable:', 'wppa').' '.$dn[$idx], 'red', 'force');
			$any_error = true;
		}
		elseif ( ! is_readable($dn[$idx]) ) {
			if ($verbose) wppa_error_message(__('Unexpected error:', 'wppa').' '.__('Directory is not readable:', 'wppa').' '.$dn[$idx], 'red', 'force');
			$any_error = true;
		}
		$idx++;
	}
	if ( $any_error ) {
		if ($verbose) wppa_error_message(__('Please de-activate and re-activate the plugin. If this problem persists, ask your administrator.', 'wppa'), 'red', 'force');
	}
	
	return ! $any_error;	// True = no error
}