<?php
/* wppa-settings-autosave.php
* Package: wp-photo-album-plus
*
* manage all options
* Version 4.6.1
*
*/

function _wppa_page_options() {
global $wpdb;
global $wppa;
global $wppa_opt;
global $blog_id; 
global $wppa_status;
global $options_error;
global $wppa_api_version;
global $wp_roles;
global $wppa_table;
global $wppa_subtable;
global $wppa_revno;
			

	// Initialize
	wppa_set_defaults();
	$options_error = false;
	
	// Things that wppa-admin-scripts.js needs to know
	echo('<script type="text/javascript">'."\n");
	echo('/* <![CDATA[ */'."\n");
		echo("\t".'wppaImageDirectory = "'.wppa_get_imgdir().'";'."\n");
		echo("\t".'wppaAjaxUrl = "'.admin_url('admin-ajax.php').'";'."\n");
	echo("/* ]]> */\n");
	echo("</script>\n");

	// Someone hit a submit button or the like?
	if ( isset($_REQUEST['wppa_settings_submit']) ) {	// Yep!
		check_admin_referer(  'wppa-nonce', 'wppa-nonce' );
		$key = $_REQUEST['wppa-key'];
		$sub = $_REQUEST['wppa-sub'];
		
		// Switch on action key
		switch ( $key ) {
							
			// Must be here
			case 'wppa_moveup':
				$sequence = get_option('wppa_slide_order');
				$indices = explode(',', $sequence);
				$temp = $indices[$sub];
				$indices[$sub] = $indices[$sub - '1'];
				$indices[$sub - '1'] = $temp;
				update_option('wppa_slide_order', implode(',', $indices));
				break;
			// Should better be here
			case 'wppa_setup':
				wppa_setup(true); // Message on success or fail is in the routine
				break;
			// Must be here
			case 'wppa_backup':
				wppa_backup_settings();	// Message on success or fail is in the routine
				break;
			// Must be here
			case 'wppa_load_skin':
				$fname = get_option('wppa_skinfile');

				if ($fname == 'restore') {
					if (wppa_restore_settings(WPPA_DEPOT_PATH.'/settings.bak', 'backup')) {
						wppa_ok_message(__('Saved settings restored', 'wppa'));
					}
					else {
						wppa_error_message(__('Unable to restore saved settings', 'wppa'));
						$options_error = true;
					}
				}
				elseif ($fname == 'default' || $fname == '') {
					if (wppa_set_defaults(true)) {						
						wppa_ok_message(__('Reset to default settings', 'wppa'));
					}
					else {
						wppa_error_message(__('Unable to set defaults', 'wppa'));
						$options_error = true;
					}
				}
				elseif (wppa_restore_settings($fname, 'skin')) {
					wppa_ok_message(sprintf(__('Skinfile %s loaded', 'wppa'), basename($fname)));
				}
				else {
					// Error printed by wppa_restore_settings()
				}
				break;
			// kan naar ajax
			case 'wppa_cleanup':
				wppa_cleanup_photos('0');
				break;
			// Must be here
			case 'wppa_watermark_upload':
				if ( isset($_FILES['file_1']) && $_FILES['file_1']['error'] != 4 ) { // Expected a fileupload for a watermark
					$file = $_FILES['file_1'];
					if ( $file['error'] ) {
						wppa_error_message(sprintf(__('Upload error %s', 'wppa'), $file['error']));
					} 
					else {
						$imgsize = getimagesize($file['tmp_name']);
						if ( !is_array($imgsize) || !isset($imgsize[2]) || $imgsize[2] != 3 ) {
							wppa_error_message(sprintf(__('Uploaded file %s is not a .png file', 'wppa'), $file['name']).' (Type='.$file['type'].').');
						}
						else {
							copy($file['tmp_name'], WPPA_UPLOAD_PATH . '/watermarks/' . basename($file['name']));
							wppa_err_alert(sprintf(__('Upload of %s done', 'wppa'), basename($file['name'])));
						}
					}
				}
				else {
					wppa_error_message(__('No file selected or error on upload', 'wppa'));
				}
				break;

			default: wppa_error_message('Unimplemnted action key: '.$key);
		}
		
		// Make sure we are uptodate
		wppa_initialize_runtime(true);

	} // wppa-settings-submit
	
	
	// See if a regeneration of thumbs is pending
	$start = get_option('wppa_lastthumb', '-2');
	if ($start != '-2') {
		$start++; 
		
		$msg = sprintf(__('Regenerating thumbnail images, starting at id=%s. Please wait...<br />', 'wppa'), $start);
		$msg .= __('If the line of dots stops growing or your browser reports Ready but you did NOT get a \'READY regenerating thumbnail images\' message, your server has given up. In that case: continue this action by clicking', 'wppa');
		$msg .= ' <a href="'.wppa_dbg_url(get_admin_url().'admin.php?page=wppa_options').'">'.__('here', 'wppa').'</a>';
		$max_time = ini_get('max_execution_time');	
		if ($max_time > '0') {
			$msg .= sprintf(__('<br /><br />Your server reports that the elapsed time for this operation is limited to %s seconds.', 'wppa'), $max_time);
			$msg .= __('<br />There may also be other restrictions set by the server, like cpu time limit.', 'wppa');
		}
		
		wppa_ok_message($msg);	// Creates element with id "wppa-ok-p"
	
		wppa_regenerate_thumbs(); 
		?>
		<script type="text/javascript">document.getElementById("wppa-ok-p").innerHTML="<strong><?php _e('READY regenerating thumbnail images.', 'wppa') ?></strong>"</script>
		<?php				
		update_option('wppa_lastthumb', '-2');
	}
	// Check database
//	if ( get_option('wppa_revision') != $wppa_revno ) 
		wppa_check_database(true);
	
?>		
	<div class="wrap">
		<?php $iconurl = WPPA_URL.'/images/settings32.png'; ?>
		<div id="icon-album" class="icon32" style="background: transparent url(<?php echo($iconurl); ?>) no-repeat">
			<br />
		</div>
		<h2><?php _e('WP Photo Album Plus Settings', 'wppa'); ?> <span style="color:blue;"><?php _e('Auto Save', 'wppa') ?></span></h2>
		<?php _e('Database revision:', 'wppa'); ?> <?php echo(get_option('wppa_revision', '100')) ?>. <?php _e('WP Charset:', 'wppa'); ?> <?php echo(get_bloginfo('charset')); ?>. <?php echo 'Current PHP version: ' . phpversion() ?>. <?php echo 'WPPA+ API Version: '.$wppa_api_version ?>.
		<br /><?php if (is_multisite()) { 
			_e('Multisite enabled. '); 
			_e('Blogid = '.$blog_id);			
		}
?>
		<!--<br /><a href="javascript:window.print();"><?php //_e('Print settings', 'wppa') ?></a><br />-->
		<a id="wppa-legon" href="javascript://" onclick="jQuery('#wppa-legenda').css('display', ''); jQuery('#wppa-legon').css('display', 'none');" ><?php _e('Show legenda', 'wppa') ?></a> 
		<div id="wppa-legenda" class="updated" style="line-height:20px; display:none" >
			<div style="float:left"><?php _e('Legenda:', 'wppa') ?></div><br />			
			<?php echo wppa_doit_button(__('Button', 'wppa')) ?><div style="float:left">&nbsp;:&nbsp;<?php _e('action that causes page reload.', 'wppa') ?></div>
			<br />
			<input type="button" onclick="if ( confirm('<?php _e('Are you sure?', 'wppa') ?>') ) return true; else return false;" class="button-secundary" style="float:left; border-radius:8px; font-size: 12px; height: 18px; margin: 0 4px; padding: 0px;" value="<?php _e('Button', 'wppa') ?>" />
			<div style="float:left">&nbsp;:&nbsp;<?php _e('action that does not cause page reload.', 'wppa') ?></div>
			<br />			
			<img src="<?php echo wppa_get_imgdir() ?>star.png" title="<?php _e('Setting unmodified', 'wppa') ?>" style="padding-left:4px; float:left; height:16px; width:16px;" /><div style="float:left">&nbsp;:&nbsp;<?php _e('Setting unmodified', 'wppa') ?></div>
			<br />
			<img src="<?php echo wppa_get_imgdir() ?>clock.png" title="<?php _e('Update in progress', 'wppa') ?>" style="padding-left:4px; float:left; height:16px; width:16px;" /><div style="float:left">&nbsp;:&nbsp;<?php _e('Update in progress', 'wppa') ?></div>
			<br />
			<img src="<?php echo wppa_get_imgdir() ?>tick.png" title="<?php _e('Setting updated', 'wppa') ?>" style="padding-left:4px; float:left; height:16px; width:16px;" /><div style="float:left">&nbsp;:&nbsp;<?php _e('Setting updated', 'wppa') ?></div>
			<br />
			<img src="<?php echo wppa_get_imgdir() ?>cross.png" title="<?php _e('Update failed', 'wppa') ?>" style="padding-left:4px; float:left; height:16px; width:16px;" /><div style="float:left">&nbsp;:&nbsp;<?php _e('Update failed', 'wppa') ?></div>
			<br />
			&nbsp;<a href="javascript://" onclick="jQuery('#wppa-legenda').css('display', 'none'); jQuery('#wppa-legon').css('display', '');" ><?php _e('Hide this', 'wppa') ?></a> 
		</div>
<?php
		// Check for inconsistencies. The potential messages are printed (display:none) and switched on/off by wppa-admin-scripts.js
		wppa_warning_message(__('You use Big Browse Buttons on slide images. Any configured links on slides like PS overrule or lightbox will not work! Check Table VI-8 and/or II-B13', 'wppa'), 'hidden', '3');
		wppa_error_message(__('You can not have popup and lightbox on thumbnails at the same time. Uncheck either Table IV-C8 or choose a different linktype in Table VI-2.', 'wppa'), 'hidden', '1');
?>		
		<form enctype="multipart/form-data" action="<?php echo(wppa_dbg_url(get_admin_url().'admin.php?page=wppa_options')) ?>" method="post">

			<?php wp_nonce_field('wppa-nonce', 'wppa-nonce'); ?>
			<input type="hidden" name="wppa-key" id="wppa-key" value="" />
			<input type="hidden" name="wppa-sub" id="wppa-sub" value="" />

			
			<?php // Table 1: Sizes ?>
			<h3><?php _e('Table I:', 'wppa'); echo(' '); _e('Sizes:', 'wppa'); ?><?php wppa_toggle_table(1) ?>
				<span style="font-weight:normal; font-size:12px;"><?php _e('This table describes all the sizes and size options (except fontsizes) for the generation and display of the WPPA+ elements.', 'wppa'); ?></span>
			</h3>

			<div id="wppa_table_1" style="display:none" >
				<table class="widefat">
					<thead style="font-weight: bold; " class="wppa_table_1">
						<tr>
							<th scope="col"><?php _e('#', 'wppa') ?></th>
							<th scope="col"><?php _e('Name', 'wppa') ?></th>
							<th scope="col"><?php _e('Description', 'wppa') ?></th>
							<th scope="col"><?php _e('Setting', 'wppa') ?></th>
							<th scope="col"><?php _e('Help', 'wppa') ?></th>
						</tr>
					</thead>
					<tbody class="wppa_table_1">
						<?php 
						$wppa_table = 'I';
						wppa_setting_subheader( 'A', '1', __('WPPA+ global system related settings', 'wppa'));
						
						$name = __('Column Width', 'wppa');
						$desc = __('The width of the main column in your theme\'s display area.', 'wppa');
						$help = esc_js(__('Enter the width of the main column in your theme\'s display area.', 'wppa'));
						$help .= '\n'.esc_js(__('You should set this value correctly to make sure the fullsize images are properly aligned horizontally.', 'wppa')); 
						$help .= '\n'.esc_js(__('You may enter auto for use in themes that have a floating content column.', 'wppa'));
						$help .= '\n'.esc_js(__('The use of \'auto\' is strongly discouraged. Do not use it unless it is strictly required.', 'wppa'));
						$slug = 'wppa_colwidth';
						$onchange = 'wppaCheckFullHalign()';
						$html = wppa_input($slug, '40px', '', __('pixels wide', 'wppa'), $onchange);
						wppa_setting($slug, '1', $name, $desc, $html, $help);

						$name = __('Resize on Upload', 'wppa');
						$desc = __('Indicate if the photos should be resized during upload.', 'wppa');
						$help = esc_js(__('If you check this item, the size of the photos will be reduced to the dimension specified in the next item during the upload/import process.', 'wppa'));
						$help .= '\n'.esc_js(__('The photos will never be stretched during upload if they are smaller.', 'wppa')); 
						$slug = 'wppa_resize_on_upload';
						$onchange = 'wppaCheckResize()';
						$html = wppa_checkbox($slug, $onchange);
						wppa_setting($slug, '2', $name, $desc, $html, $help);
						
						$name = __('Resize to', 'wppa');
						$desc = __('Resize photos to fit within a given area.', 'wppa');
						$help = esc_js(__('Specify the screensize for the unscaled photos.', 'wppa'));
						$help .= '\n'.esc_js(__('The use of a non-default value is particularly usefull when you make use of lightbox functionality.', 'wppa'));
						$slug = 'wppa_resize_to';
						$px = __('pixels', 'wppa');
						$options = array(__('Fullsize as specified above', 'wppa'), '640 x 480 '.$px, '800 x 600 '.$px, '1024 x 768 '.$px, '1200 x 900 '.$px, '1280 x 960 '.$px, '1366 x 768 '.$px, '1920 x 1080 '.$px);
						$values = array( '0', '640x480', '800x600', '1024x768', '1200x900', '1280x960', '1366x768', '1920x1080');
						$class = 're_up';
						$html = wppa_select($slug, $options, $values);
						wppa_setting('', '3', $name, $desc, $html, $help, $class);
						
						$name = __('Photocount threshold', 'wppa');
						$desc = __('Number of thumbnails in an album must exceed.', 'wppa');
						$help = esc_js(__('Photos do not show up in the album unless there are more than this number of photos in the album. This allows you to have cover photos on an album that contains only sub albums without seeing them in the list of sub albums. Usually set to 0 (always show) or 1 (for one cover photo).', 'wppa'));
						$slug = 'wppa_min_thumbs';
						$html = wppa_input($slug, '40px', '', __('pieces', 'wppa'));
						wppa_setting($slug, '4', $name, $desc, $html, $help);
						
						$name = __('Border thickness', 'wppa');
						$desc = __('Thickness of wppa+ box borders.', 'wppa');
						$help = esc_js(__('Enter the thickness for the border of the WPPA+ boxes. A number of 0 means: no border.', 'wppa'));
						$help .= '\n'.esc_js(__('WPPA+ boxes are: the navigation bars and the filmstrip.', 'wppa'));
						$slug = 'wppa_bwidth';
						$html = wppa_input($slug, '40px', '', __('pixels', 'wppa'));
						wppa_setting($slug, '5', $name, $desc, $html, $help);
						
						$name = __('Border radius', 'wppa');
						$desc = __('Radius of wppa+ box borders.', 'wppa');
						$help = esc_js(__('Enter the corner radius for the border of the WPPA+ boxes. A number of 0 means: no rounded corners.', 'wppa'));
						$help .= '\n'.esc_js(__('WPPA+ boxes are: the navigation bars and the filmstrip.', 'wppa'));
						$help .= '\n\n'.esc_js(__('Note that rounded corners are only supported by modern browsers.', 'wppa'));
						$slug = 'wppa_bradius';
						$html = wppa_input($slug, '40px', '', __('pixels', 'wppa'));
						wppa_setting($slug, '6', $name, $desc, $html, $help);
						
						$name = __('Box spacing', 'wppa');
						$desc = __('Distance between wppa+ boxes.', 'wppa');
						$help = '';
						$slug = 'wppa_box_spacing';
						$html = wppa_input($slug, '40px', '', __('pixels', 'wppa'));
						wppa_setting($slug, '7', $name, $desc, $html, $help);

						wppa_setting_subheader('B', '1', __('Fullsize photos and Slideshow related settings', 'wppa'));
						
						$name = __('Fullsize Width', 'wppa');
						$desc = __('The maximum width fullsize photos will be displayed.', 'wppa');
						$help = esc_js(__('Enter the largest size in pixels as how you want your photos to be displayed.', 'wppa'));
						$help .= '\n'.esc_js(__('This is usually the same as the Column Width (Table I-A1), but it may differ.', 'wppa'));
						$slug = 'wppa_fullsize';
						$onchange = 'wppaCheckFullHalign()';
						$html = wppa_input($slug, '40px', '', __('pixels wide', 'wppa'), $onchange);
						wppa_setting($slug, '1', $name, $desc, $html, $help);
						
						$name = __('Fullsize Height', 'wppa');
						$desc = __('The maximum height fullsize photos will be displayed.', 'wppa');
						$help = esc_js(__('Enter the largest size in pixels as how you want your photos to be displayed.', 'wppa'));
						$help .= '\n'.esc_js(__('This setting defines the height of the space reserved for full sized photos.', 'wppa'));
						$help .= '\n'.esc_js(__('If you change the width of a display by the %%size= command, this value changes proportionally to match the aspect ratio as defined by this and the previous setting.', 'wppa'));
						$slug = 'wppa_maxheight';
						$html = wppa_input($slug, '40px', '', __('pixels high', 'wppa'));
						wppa_setting($slug, '2', $name, $desc, $html, $help);

						$name = __('Stretch to fit', 'wppa');
						$desc = __('Stretch photos that are too small.', 'wppa');
						$help = esc_js(__('Fullsize images will be stretched to the Full Size at display time if they are smaller. Leaving unchecked is recommended. It is better to upload photos that fit well the sizes you use!', 'wppa'));
						$slug = 'wppa_enlarge';
						$html = wppa_checkbox($slug);
						wppa_setting($slug, '3', $name, $desc, $html, $help);

						$name = __('Fullsize borderwidth', 'wppa');
						$desc = __('The width of the border around fullsize images.', 'wppa');
						$help = esc_js(__('The border is made by the image background being larger than the image itsself (padding).', 'wppa'));
						$help .= '\n'.esc_js(__('Additionally there may be a one pixel outline of a different color. See Table III, item 7.', 'wppa'));
						$help .= '\n\n'.esc_js(__('The number you enter here is exclusive the one pixel outline.', 'wppa'));
						$help .= '\n\n'.esc_js(__('If you leave this entry empty, there will be no outline either.', 'wppa'));
						$slug = 'wppa_fullimage_border_width';
						$html = wppa_input($slug, '40px', '', __('pixels', 'wppa'));
						wppa_setting($slug, '4', $name, $desc, $html, $help);
					
						$name = __('Numbar Max', 'wppa');
						$desc = __('Maximum nubers to display.', 'wppa');
						$help = esc_js(__('In order to attemt to fit on one line, the numbers will be replaced by dots - except the current - when there are more than this number of photos in a slideshow.', 'wppa'));
						$slug = 'wppa_numbar_max';
						$html = wppa_input($slug, '40px', '', __('numbers', 'wppa'));
						$class = 'wppa_numbar';
						wppa_setting($slug, '5', $name, $desc, $html, $help, $class);

						wppa_setting_subheader('C', '1', __('Thumbnail photos related settings', 'wppa'));
						
						$name = __('Thumbnail Size', 'wppa');
						$desc = __('The size of the thumbnail images.', 'wppa');
						$help = esc_js(__('This size applies to the width or height, whichever is the largest.', 'wppa'));
						$help .= '\n'.esc_js(__('Changing the thumbnail size may result in all thumbnails being regenerated. this may take a while.', 'wppa'));
						$slug = 'wppa_thumbsize';
						$html = wppa_input($slug, '40px', '', __('pixels', 'wppa'));
						$class = 'tt_normal';
						wppa_setting($slug, '1', $name, $desc, $html, $help, $class);

						$name = __('Thumbnail Aspect', 'wppa');
						$desc = __('Aspect ration of thumbnail image', 'wppa');
						$help = '';
						$slug = 'wppa_thumb_aspect';
						$options = array(
							__('--- same as fullsize ---', 'wppa'), 
							__('--- square clipped ---', 'wppa'),
							__('4:5 landscape clipped', 'wppa'),
							__('3:4 landscape clipped', 'wppa'), 
							__('2:3 landscape clipped', 'wppa'),
							__('9:16 landscape clipped', 'wppa'),
							__('1:2 landscape clipped', 'wppa'),
							__('--- square padded ---', 'wppa'),
							__('4:5 landscape padded', 'wppa'),
							__('3:4 landscape padded', 'wppa'), 
							__('2:3 landscape padded', 'wppa'),
							__('9:16 landscape padded', 'wppa'),
							__('1:2 landscape padded', 'wppa')
							);
						$values = array(
							'0:0:none', 
							'1:1:clip',
							'4:5:clip',
							'3:4:clip', 
							'2:3:clip',
							'9:16:clip',
							'1:2:clip',
							'1:1:padd',
							'4:5:padd',
							'3:4:padd', 
							'2:3:padd',
							'9:16:padd',
							'1:2:padd'
							);
						$html = wppa_select($slug, $options, $values);
						$class = 'tt_normal';
						wppa_setting($slug, '2', $name, $desc, $html, $help, $class);
						
						$name = __('Thumbframe width', 'wppa');
						$desc = __('The width of the thumbnail frame.', 'wppa');
						$help = esc_js(__('Set the width of the thumbnail frame.', 'wppa'));
						$help .= '\n\n'.esc_js(__('Set width, height and spacing for the thumbnail frames.', 'wppa'));
						$help .= '\n'.esc_js(__('These sizes should be large enough for a thumbnail image and - optionally - the text under it.', 'wppa'));
						$slug = 'wppa_tf_width';
						$html = wppa_input($slug, '40px', '', __('pixels wide', 'wppa'));
						$class = 'tt_normal';
						wppa_setting($slug, '3', $name, $desc, $html, $help, $class);

						$name = __('Thumbframe height', 'wppa');
						$desc = __('The height of the thumbnail frame.', 'wppa');
						$help = esc_js(__('Set the height of the thumbnail frame.', 'wppa'));
						$help .= '\n\n'.esc_js(__('Set width, height and spacing for the thumbnail frames.', 'wppa'));
						$help .= '\n'.esc_js(__('These sizes should be large enough for a thumbnail image and - optionally - the text under it.', 'wppa'));
						$slug = 'wppa_tf_height';
						$html = wppa_input($slug, '40px', '', __('pixels high', 'wppa'));
						$class = 'tt_normal';
						wppa_setting($slug, '4', $name, $desc, $html, $help, $class);

						$name = __('Thumbnail spacing', 'wppa');
						$desc = __('The spacing between adjacent thumbnail frames.', 'wppa');
						$help = esc_js(__('Set the minimal spacing between the adjacent thumbnail frames', 'wppa'));
						$help .= '\n\n'.esc_js(__('Set width, height and spacing for the thumbnail frames.', 'wppa'));
						$help .= '\n'.esc_js(__('These sizes should be large enough for a thumbnail image and - optionally - the text under it.', 'wppa'));
						$slug = 'wppa_tn_margin';
						$html = wppa_input($slug, '40px', '', __('pixels', 'wppa'));
						$class = 'tt_normal';
						wppa_setting($slug, '5', $name, $desc, $html, $help, $class);
						
						$name = __('Auto spacing', 'wppa');
						$desc = __('Space the thumbnail frames automatic.', 'wppa');
						$help = esc_js(__('If you check this box, the thumbnail images will be evenly distributed over the available width.', 'wppa'));
						$help .= '\n'.esc_js(__('In this case, the thumbnail spacing value (setting I-9) will be regarded as a minimum value.', 'wppa'));
						$slug = 'wppa_thumb_auto';
						$html = wppa_checkbox($slug);
						$class = 'tt_normal';
						wppa_setting($slug, '6', $name, $desc, $html, $help, $class);
						
						$name = __('Page size', 'wppa');
						$desc = __('Max number of thumbnails per page.', 'wppa');
						$help = esc_js(__('Enter the maximum number of thumbnail images per page. A value of 0 indicates no pagination.', 'wppa'));
						$slug = 'wppa_thumb_page_size';
						$html = wppa_input($slug, '40px', '', __('thumbnails', 'wppa'));
						$class = 'tt_always';
						wppa_setting($slug, '7', $name, $desc, $html, $help, $class);

						$name = __('Popup size', 'wppa');
						$desc = __('The size of the thumbnail popup images.', 'wppa');
						$help = esc_js(__('Enter the size of the popup images. This size should be larger than the thumbnail size.', 'wppa'));
						$help .= '\n'.esc_js(__('This size should also be at least the cover image size.', 'wppa'));
						$help .= '\n'.esc_js(__('Changing the popup size may result in all thumbnails being regenerated. this may take a while.', 'wppa'));
						$help .= '\n\n'.esc_js(__('Although this setting has only visual effect if "Thumb popup" (Table IV-C8) is checked,', 'wppa'));
						$help .= ' '.esc_js(__('the value must be right as it is the physical size of the thumbnail and coverphoto images.', 'wppa'));
						$slug = 'wppa_popupsize';
						$class = 'tt_normal';
						$html = wppa_input($slug, '40px', '', __('pixels', 'wppa'));
						wppa_setting($slug, '8', $name, $desc, $html, $help, $class);
						
						$name = __('Use thumbs if fit', 'wppa');
						$desc = __('Use the thumbnail image files if they are large enough.', 'wppa');
						$help = esc_js(__('This setting speeds up page loading for small photos.', 'wppa'));
						$help .= '\n\n'.esc_js(__('Do NOT use this when your thumbnails have a forced aspect ratio (when Table I-C2 is set to anything different from --- same as fullsize ---)', 'wppa'));
						$slug = 'wppa_use_thumbs_if_fit';
						$html = wppa_checkbox($slug); 
						wppa_setting($slug, '9', $name, $desc, $html, $help);
				
						wppa_setting_subheader('D', '1', __('Album cover related settings', 'wppa'));
						
						$name = __('Max Cover width', 'wppa');
						$desc = __('Maximum width for a album cover display.', 'wppa');
						$help = esc_js(__('Display covers in 2 or more columns if the display area is wider than the given width.', 'wppa'));
						$help .= '\n'.esc_js(__('This also applies for \'thumbnails as covers\', and will NOT apply to single items.', 'wppa'));
						$slug = 'wppa_max_cover_width';
						$html = wppa_input($slug, '40px', '', __('pixels', 'wppa'));
						wppa_setting($slug, '1', $name, $desc, $html, $help);

						$name = __('Min Text frame height', 'wppa');
						$desc = __('The minimal cover text frame height.', 'wppa');
						$help = esc_js(__('The minimal height of the description field in an album cover display.', 'wppa'));
						$help .= '\n\n'.esc_js(__('This setting enables you to give the album covers the same height provided that the cover images are equally sized.', 'wppa'));
						$slug = 'wppa_text_frame_height';
						$html = wppa_input($slug, '40px', '', __('pixels', 'wppa'));
						wppa_setting($slug, '2', $name, $desc, $html, $help);

						$name = __('Coverphoto size', 'wppa');
						$desc = __('The size of the coverphoto.', 'wppa');
						$help = esc_js(__('This size applies to the width or height, whichever is the largest.', 'wppa'));
						$help .= '\n'.esc_js(__('Changing the coverphoto size may result in all thumbnails being regenerated. this may take a while.', 'wppa'));
						$slug = 'wppa_smallsize';
						$html = wppa_input($slug, '40px', '', __('pixels', 'wppa'));
						wppa_setting($slug, '3', $name, $desc, $html, $help);
						
						$name = __('Page size', 'wppa');
						$desc = __('Max number of covers per page.', 'wppa');
						$help = esc_js(__('Enter the maximum number of album covers per page. A value of 0 indicates no pagination.', 'wppa'));
						$slug = 'wppa_album_page_size';
						$html = wppa_input($slug, '40px', '', __('covers', 'wppa'));
						wppa_setting($slug, '4', $name, $desc, $html, $help);
						
						wppa_setting_subheader('E', '1', __('Rating and comment related settings', 'wppa'));
						
						$name = __('Rating size', 'wppa');
						$desc = __('Select the number of voting stars.', 'wppa');
						$help = '';
						$slug = 'wppa_rating_max';
						$options = array('Standard: 5 stars', 'Extended: 10 stars');
						$values = array('5', '10');
						$html = wppa_select($slug, $options, $values);
						$class = 'wppa_rating_';
						wppa_setting($slug, '1', $name, $desc, $html, $help, $class);
						
						$name = __('Display precision', 'wppa');
						$desc = __('Select the desired rating display precision.', 'wppa');
						$help = '';
						$slug = 'wppa_rating_prec';
						$options = array('1 '.__('decimal places', 'wppa'), '2 '.__('decimal places', 'wppa'), '3 '.__('decimal places', 'wppa'), '4 '.__('decimal places', 'wppa'));
						$values = array('1', '2', '3', '4');
						$html = wppa_select($slug, $options, $values);
						$class = 'wppa_rating_';
						wppa_setting($slug, '2', $name, $desc, $html, $help, $class);
						
						$name = __('Avatar size', 'wppa');
						$desc = __('Size of Avatar images.', 'wppa');
						$help = esc_js(__('The size of the square avatar; must be > 0 and < 256', 'wppa'));
						$slug = 'wppa_gravatar_size';
						$html = wppa_input($slug, '40px', '', __('pixels', 'wppa'));
						wppa_setting($slug, '3', $name, $desc, $html, $help);
						
						wppa_setting_subheader('F', '1', __('Widget related settings', 'wppa'));
						
						$name = __('TopTen count', 'wppa');
						$desc = __('Number of photos in TopTen widget.', 'wppa');
						$help = esc_js(__('Enter the maximum number of rated photos in the TopTen widget.', 'wppa'));
						$slug = 'wppa_topten_count';
						$html = wppa_input($slug, '40px', '', __('photos', 'wppa'));
						wppa_setting($slug, '1', $name, $desc, $html, $help, 'wppa_rating');
						
						$name = __('TopTen size', 'wppa');
						$desc = __('Size of thumbnails in TopTen widget.', 'wppa');
						$help = esc_js(__('Enter the size for the mini photos in the TopTen widget.', 'wppa'));
						$help .= '\n'.esc_js(__('The size applies to the width or height, whatever is the largest.', 'wppa'));
						$help .= '\n'.esc_js(__('Recommended values: 86 for a two column and 56 for a three column display.', 'wppa'));
						$slug = 'wppa_topten_size';
						$html = wppa_input($slug, '40px', '', __('pixels', 'wppa'));
						wppa_setting($slug, '2', $name, $desc, $html, $help, 'wppa_rating');

						$name = __('Comment count', 'wppa');
						$desc = __('Number of entries in Comment widget.', 'wppa');
						$help = esc_js(__('Enter the maximum number of entries in the Comment widget.', 'wppa'));
						$slug = 'wppa_comment_count';
						$html = wppa_input($slug, '40px', '', __('entries', 'wppa'));
						wppa_setting($slug, '3', $name, $desc, $html, $help);
						
						$name = __('Comment size', 'wppa');
						$desc = __('Size of thumbnails in Comment widget.', 'wppa');
						$help = esc_js(__('Enter the size for the mini photos in the Comment widget.', 'wppa'));
						$help .= '\n'.esc_js(__('The size applies to the width or height, whatever is the largest.', 'wppa'));
						$help .= '\n'.esc_js(__('Recommended values: 86 for a two column and 56 for a three column display.', 'wppa'));
						$slug = 'wppa_comment_size';
						$html = wppa_input($slug, '40px', '', __('pixels', 'wppa'));
						wppa_setting($slug, '4', $name, $desc, $html, $help);

						$name = __('Thumbnail count', 'wppa');
						$desc = __('Number of photos in Thumbnail widget.', 'wppa');
						$help = esc_js(__('Enter the maximum number of rated photos in the Thumbnail widget.', 'wppa'));
						$slug = 'wppa_thumbnail_widget_count';
						$html = wppa_input($slug, '40px', '', __('photos', 'wppa'));
						wppa_setting($slug, '5', $name, $desc, $html, $help);

						$name = __('Thumbnail widget size', 'wppa');
						$desc = __('Size of thumbnails in Thumbnail widget.', 'wppa');
						$help = esc_js(__('Enter the size for the mini photos in the Thumbnail widget.', 'wppa'));
						$help .= '\n'.esc_js(__('The size applies to the width or height, whatever is the largest.', 'wppa'));
						$help .= '\n'.esc_js(__('Recommended values: 86 for a two column and 56 for a three column display.', 'wppa'));
						$slug = 'wppa_thumbnail_widget_size';
						$html = wppa_input($slug, '40px', '', __('pixels', 'wppa'));
						wppa_setting($slug, '6', $name, $desc, $html, $help);
						
						wppa_setting_subheader('G', '1', __('Lightbox related settings. These settings have effect only when Table IX-A6 is set to wppa', 'wppa'));
						
						$name = __('Number of text lines', 'wppa');
						$desc = __('Number of lines on the lightbox description area, exclusive the n/m line.', 'wppa');
						$help = esc_js(__('Enter a number in the range from 0 to 24 or auto', 'wppa'));
						$slug = 'wppa_ovl_txt_lines';
						$html = wppa_input($slug, '40px', '', __('lines', 'wppa'));
						wppa_setting($slug, '1', $name, $desc, $html, $help);
						
						?>
					</tbody>
					<tfoot style="font-weight: bold;" class="wppa_table_1">
						<tr>
							<th scope="col"><?php _e('#', 'wppa') ?></th>
							<th scope="col"><?php _e('Name', 'wppa') ?></th>
							<th scope="col"><?php _e('Description', 'wppa') ?></th>
							<th scope="col"><?php _e('Setting', 'wppa') ?></th>
							<th scope="col"><?php _e('Help', 'wppa') ?></th>
						</tr>
					</tfoot>
				</table>
			</div>

			<?php // Table 2: Visibility ?>
			<h3><?php _e('Table II:', 'wppa'); echo(' '); _e('Visibility:', 'wppa'); ?><?php wppa_toggle_table(2) ?>
				<span style="font-weight:normal; font-size:12px;"><?php _e('This table describes the visibility of certain wppa+ elements.', 'wppa'); ?></span>
			</h3>
			
			<div id="wppa_table_2" style="display:none" >
				<table class="widefat">
					<thead style="font-weight: bold; " class="wppa_table_2">
						<tr>
							<th scope="col"><?php _e('#', 'wppa') ?></th>
							<th scope="col"><?php _e('Name', 'wppa') ?></th>
							<th scope="col"><?php _e('Description', 'wppa') ?></th>
							<th scope="col"><?php _e('Setting', 'wppa') ?></th>
							<th scope="col"><?php _e('Help', 'wppa') ?></th>
						</tr>
					</thead>
					<tbody class="wppa_table_2">
						<?php 
						$wppa_table = 'II';
						wppa_setting_subheader('A', '1', __('Breadcrumb related settings', 'wppa'));
						
						$name = __('Breadcrumb', 'wppa');
						$desc = __('Show breadcrumb navigation bars.', 'wppa');
						$help = esc_js(__('Indicate whether a breadcrumb navigation should be displayed', 'wppa'));
						$slug = 'wppa_show_bread';
						$onchange = 'wppaCheckBreadcrumb()';
						$html = wppa_checkbox($slug, $onchange);
						wppa_setting($slug, '1', $name, $desc, $html, $help);

						$name = __('Breadcrumb on search results', 'wppa');
						$desc = __('Show breadcrumb navigation bars on the search results page.', 'wppa');
						$help = esc_js(__('Indicate whether a breadcrumb navigation should be displayed above the search results.', 'wppa'));
						$slug = 'wppa_bc_on_search';
						$html = wppa_checkbox($slug);
						$class = 'wppa_bc';
						wppa_setting($slug, '2', $name, $desc, $html, $help, $class);
						
						$name = __('Breadcrumb on topten displays', 'wppa');
						$desc = __('Show breadcrumb navigation bars on topten displays.', 'wppa');
						$help = esc_js(__('Indicate whether a breadcrumb navigation should be displayed above the topten displays.', 'wppa'));
						$slug = 'wppa_bc_on_topten';
						$html = wppa_checkbox($slug);
						$class = 'wppa_bc';
						wppa_setting($slug, '3', $name, $desc, $html, $help, $class);
						
						$name = __('Home', 'wppa');
						$desc = __('Show "Home" in breadcrumb.', 'wppa');
						$help = esc_js(__('Indicate whether the breadcrumb navigation should start with a "Home"-link', 'wppa'));
						$slug = 'wppa_show_home';
						$html = wppa_checkbox($slug);
						$class = 'wppa_bc';
						wppa_setting($slug, '4', $name, $desc, $html, $help, $class);

						$name = __('Separator', 'wppa');
						$desc = __('Breadcrumb separator symbol.', 'wppa');
						$help = esc_js(__('Select the desired breadcrumb separator element.', 'wppa'));
						$help .= '\n'.esc_js(__('A text string may contain valid html.', 'wppa'));
						$help .= '\n'.esc_js(__('An image will be scaled automatically if you set the navigation font size.', 'wppa'));
						$slug = 'wppa_bc_separator';
						$options = array('&raquo', '&rsaquo', '&gt', '&bull', __('Text (html):', 'wppa'), __('Image (url):', 'wppa'));
						$values = array('raquo', 'rsaquo', 'gt', 'bull', 'txt', 'url');
						$onchange = 'wppaCheckBreadcrumb()';
						$html = wppa_select($slug, $options, $values, $onchange);
						$class = 'wppa_bc';
						wppa_setting($slug, '5', $name, $desc, $html, $help, $class);
						
						$name = __('Html', 'wppa');
						$desc = __('Breadcrumb separator text.', 'wppa');
						$help = esc_js(__('Enter the HTML code that produces the separator symbol you want.', 'wppa'));
						$help .= '\n'.esc_js(__('It may be as simple as \'-\' (without the quotes) or as complex as a tag like <div>..</div>.', 'wppa'));
						$slug = 'wppa_bc_txt';
						$html = wppa_input($slug, '90%', '300px');
						wppa_setting($slug, '6', $name, $desc, $html, $help, $slug);

						$name = __('Image Url', 'wppa');
						$desc = __('Full url to separator image.', 'wppa');
						$help = esc_js(__('Enter the full url to the image you want to use for the separator symbol.', 'wppa'));
						$slug = 'wppa_bc_url';
						$html = wppa_input($slug, '90%', '300px');
						wppa_setting($slug, '7', $name, $desc, $html, $help, $slug);
						
						$name = __('Pagelink position', 'wppa');
						$desc = __('The location for the pagelinks bar.', 'wppa');
						$help = '';
						$slug = 'wppa_pagelink_pos';
						$options = array(__('Top', 'wppa'), __('Bottom', 'wppa'), __('Both', 'wppa'));
						$values = array('top', 'bottom', 'both');
						$html = wppa_select($slug, $options, $values);
						wppa_setting($slug, '8', $name, $desc, $html, $help);

						wppa_setting_subheader('B', '1', __('Slideshow related settings', 'wppa'));
						
						$name = __('Start/stop', 'wppa');
						$desc = __('Show the Start/Stop slideshow bar.', 'wppa');
						$help = esc_js(__('If checked: display the start/stop slideshow navigation bar above the full-size images and slideshow', 'wppa'));
						$slug = 'wppa_show_startstop_navigation';
						$html = wppa_checkbox($slug);
						wppa_setting($slug, '1', $name, $desc, $html, $help);
						
						$name = __('Browse bar', 'wppa');
						$desc = __('Show Browse photos bar.', 'wppa');
						$help = esc_js(__('If checked: display the preveous/next navigation bar under the full-size images and slideshow', 'wppa'));
						$slug = 'wppa_show_browse_navigation';
						$html = wppa_checkbox($slug);
						wppa_setting($slug, '2', $name, $desc, $html, $help);
						
						$name = __('Filmstrip', 'wppa');
						$desc = __('Show Filmstrip navigation bar.', 'wppa');
						$help = esc_js(__('If checked: display the filmstrip navigation bar under the full_size images and slideshow', 'wppa'));
						$slug = 'wppa_filmstrip';
						$html = wppa_checkbox($slug);
						wppa_setting($slug, '3', $name, $desc, $html, $help);
						
						$name = __('Film seam', 'wppa');
						$desc = __('Show seam between end and start of film.', 'wppa');
						$help = esc_js(__('If checked: display the wrap-around point in the filmstrip', 'wppa'));
						$slug = 'wppa_film_show_glue';
						$html = wppa_checkbox($slug);
						wppa_setting($slug, '4', $name, $desc, $html, $help);

						$name = __('Fullsize name', 'wppa');
						$desc = __('Display Fullsize name.', 'wppa');
						$help = esc_js(__('If checked: display the name of the photo under the full-size images and slideshow.', 'wppa')); 
						$slug = 'wppa_show_full_name';
						$html = wppa_checkbox($slug);
						wppa_setting($slug, '5', $name, $desc, $html, $help);
						
						$name = __('Fullsize desc', 'wppa');
						$desc = __('Display Fullsize description.', 'wppa');
						$help = esc_js(__('If checked: display description under the full-size images and slideshow.', 'wppa'));
						$slug = 'wppa_show_full_desc';
						$html = wppa_checkbox($slug);
						wppa_setting($slug, '6', $name, $desc, $html, $help);

						$name = __('Rating system', 'wppa');
						$desc = __('Enable the rating system.', 'wppa');
						$help = esc_js(__('If checked, the photo rating system will be enabled.', 'wppa'));
						$slug = 'wppa_rating_on';
						$onchange = 'wppaCheckRating()';
						$html = wppa_checkbox($slug, $onchange);
						wppa_setting($slug, '7', $name, $desc, $html, $help);
						
						$name = __('Rating display type', 'wppa');
						$desc = __('Specify the type of the rating display.', 'wppa');
						$help = '';
						$slug = 'wppa_rating_display_type';
						$options = array(__('Graphic', 'wppa'), __('Numeric', 'wppa'));
						$values = array('graphic', 'numeric');
						$html = wppa_select($slug, $options, $values);
						$class = 'wppa_rating_';
						wppa_setting($slug, '8', $name, $desc, $html, $help, $class);

						$name = __('Show average rating', 'wppa');
						$desc = __('Display the avarage rating on the rating bar', 'wppa');
						$help = esc_js(__('If checked, the average rating as well as the current users rating is displayed in max 5 stars.', 'wppa'));
						$help .= '\n\n'.esc_js(__('If unchecked, only the current users rating is displayed (if any).', 'wppa'));
						$slug = 'wppa_show_avg_rating';
						$html = wppa_checkbox($slug);
						$class = 'wppa_rating_';
						wppa_setting($slug, '9', $name, $desc, $html, $help, $class);
						
						$name = __('Comments system', 'wppa');
						$desc = __('Enable the comments system.', 'wppa');
						$help = esc_js(__('Display the comments box under the fullsize images and let users enter their comments on individual photos.', 'wppa'));
						$slug = 'wppa_show_comments';
						$onchange = 'wppaCheckComments()';
						$html = wppa_checkbox($slug, $onchange);
						wppa_setting($slug, '10', $name, $desc, $html, $help);
						
						$name = __('Comment Avatar default', 'wppa');
						$desc = __('Show Avatars with the comments if not --- none ---', 'wppa');
						$help = '';
						$slug = 'wppa_comment_gravatar';
						$onchange = 'wppaCheckGravatar()';
						$options = array(	__('--- none ---', 'wppa'), 
											__('mystery man', 'wppa'), 
											__('identicon', 'wppa'), 
											__('monsterid', 'wppa'), 
											__('wavatar', 'wppa'),
											__('retro', 'wppa'),
											__('--- url ---', 'wppa')
										);
						$values = array(	'none', 
											'mm', 
											'identicon', 
											'monsterid',
											'wavatar',
											'retro',
											'url'
										);
						$class = 'wppa_comment_';
						$html = wppa_select($slug, $options, $values, $onchange);
						wppa_setting($slug, '11', $name, $desc, $html, $help, $class);
						
						$name = __('Comment Avatar url', 'wppa');
						$desc = __('Comment Avatar default url.', 'wppa');
						$help = '';
						$slug = 'wppa_comment_gravatar_url';
						$class = 'wppa_grav';
						$html = wppa_input($slug, '90%', '300px');
						wppa_setting($slug, '12', $name, $desc, $html, $help, $class);
						
						$name = __('Big Browse Buttons', 'wppa');
						$desc = __('Enable invisible browsing buttons.', 'wppa');
						$help = esc_js(__('If checked, the fullsize image is covered by two invisible areas that act as browse buttons.', 'wppa'));
						$help .= '\n\n'.esc_js(__('Make sure the Full height (Table I-B2) is properly configured to prevent these areas to overlap unwanted space.', 'wppa'));
						$help .= '\n\n'.esc_js(__('A side effect of this setting is that right clicking the image no longer enables the visitor to download the image.', 'wppa'));
						$slug = 'wppa_show_bbb';
						$html = wppa_checkbox($slug);
						wppa_setting($slug, '13', $name, $desc, $html, $help);

						$name = __('Show custom box', 'wppa');
						$desc = __('Display the custom box in the slideshow', 'wppa');
						$help = esc_js(__('You can fill the custom box with any html you like. It will not be checked, so it is your own responsability to close tags properly.', 'wppa'));
						$help .= '\n\n'.esc_js(__('The position of the box can be defined in Table IX-E.', 'wppa'));
						$slug = 'wppa_custom_on';
						$onchange = 'wppaCheckCustom()';
						$html = wppa_checkbox($slug, $onchange);
						wppa_setting($slug, '14', $name, $desc, $html, $help);
						
						$name = __('Custom content', 'wppa');
						$desc = __('The content (html) of the custom box.', 'wppa');
						$help = esc_js(__('You can fill the custom box with any html you like. It will not be checked, so it is your own responsability to close tags properly.', 'wppa'));
						$help .= '\n\n'.esc_js(__('The position of the box can be defined in Table IX-E.', 'wppa'));
						$slug = 'wppa_custom_content';
						$html = wppa_textarea($slug);
						$class = 'wppa_custom_';
						wppa_setting(false, '15', $name, $desc, $html, $help, $class);

						$name = __('Slideshow/Number bar', 'wppa');
						$desc = __('Display the Slideshow / Number bar.', 'wppa');
						$help = esc_js(__('If checked: display the number boxes on slideshow', 'wppa'));
						$slug = 'wppa_show_slideshownumbar';
						$onchange = 'wppaCheckNumbar()';
						$html = wppa_checkbox($slug, $onchange);
						wppa_setting($slug, '16', $name, $desc, $html, $help);
						
						$name = __('IPTC system', 'wppa');
						$desc = __('Enable the iptc system.', 'wppa');
						$help = esc_js(__('Display the iptc box under the fullsize images.', 'wppa'));
						$slug = 'wppa_show_iptc';
						$onchange = ''; 
						$html = wppa_checkbox($slug);
						wppa_setting($slug, '17', $name, $desc, $html, $help);

						$name = __('EXIF system', 'wppa');
						$desc = __('Enable the exif system.', 'wppa');
						$help = esc_js(__('Display the exif box under the fullsize images.', 'wppa'));
						$slug = 'wppa_show_exif';
						$onchange = ''; 
						$html = wppa_checkbox($slug); 
						wppa_setting($slug, '18', $name, $desc, $html, $help);
						
						$name = __('Show Copyright', 'wppa');
						$desc = __('Show a copyright warning on the user upload screen.', 'wppa');
						$help = '';
						$slug = 'wppa_copyright_on';
						$class = 'wppa_copyr';
						$html = wppa_checkbox($slug);
						wppa_setting($slug, '19', $name, $desc, $html, $help, $class);
						
						$name = __('Copyright notice', 'wppa');
						$desc = __('The message to be displayed.', 'wppa');
						$help = '';
						$slug = 'wppa_copyright_notice';
						$class = 'wppa_copyr';
						$html = wppa_textarea($slug);
						wppa_setting($slug, '20', $name, $desc, $html, $help, $class);

						wppa_setting_subheader('C', '1', __('Thumbnail display related settings', 'wppa'));
						
						$name = __('Thumbnail name', 'wppa');
						$desc = __('Display Thubnail name.', 'wppa');
						$help = esc_js(__('Display photo name under thumbnail images.', 'wppa'));
						$slug = 'wppa_thumb_text_name';
						$html = wppa_checkbox($slug);
						$class = 'tt_normal';
						wppa_setting($slug, '1', $name, $desc, $html, $help, $class);
						
						$name = __('Thumbnail desc', 'wppa');
						$desc = __('Display Thumbnail description.', 'wppa');
						$help = esc_js(__('Display description of the photo under thumbnail images.', 'wppa'));
						$slug = 'wppa_thumb_text_desc';
						$html = wppa_checkbox($slug);
						$class = 'tt_normal';
						wppa_setting($slug, '2', $name, $desc, $html, $help, $class);
						
						$name = __('Thumbnail rating', 'wppa');
						$desc = __('Display Thumbnail Rating.', 'wppa');
						$help = esc_js(__('Display the rating of the photo under the thumbnail image.', 'wppa'));
						$slug = 'wppa_thumb_text_rating';
						$html = '<span class="wppa_rating">'.wppa_checkbox($slug).'</span>';
						$class = 'wppa_rating_ tt_normal';
						wppa_setting($slug, '3', $name, $desc, $html, $help, $class);
						
  						$name = __('Popup name', 'wppa');
						$desc = __('Display Thubnail name on popup.', 'wppa');
						$help = esc_js(__('Display photo name under thumbnail images on the popup.', 'wppa'));
						$slug = 'wppa_popup_text_name';
						$html = wppa_checkbox($slug);
						$class = 'tt_normal wppa_popup';
						wppa_setting($slug, '4', $name, $desc, $html, $help, $class);
						
						$name = __('Popup desc', 'wppa');
						$desc = __('Display Thumbnail description on popup.', 'wppa');
						$help = esc_js(__('Display description of the photo under thumbnail images on the popup.', 'wppa'));
						$slug = 'wppa_popup_text_desc';
						$html = wppa_checkbox($slug);
						$class = 'tt_normal wppa_popup';
						wppa_setting($slug, '5', $name, $desc, $html, $help, $class);
						
						$name = __('Popup desc no links', 'wppa');
						$desc = __('Strip html anchor tags from descriptions on popups', 'wppa');
						$help = esc_js(__('Use this option to prevent the display of links that cannot be activated.', 'wppa'));
						$slug = 'wppa_popup_text_desc_strip';
						$html = wppa_checkbox($slug);
						$class = 'tt_normal wppa_popup';
						wppa_setting($slug, '5.1', $name, $desc, $html, $help, $class);
						
						$name = __('Popup rating', 'wppa');
						$desc = __('Display Thumbnail Rating on popup.', 'wppa');
						$help = esc_js(__('Display the rating of the photo under the thumbnail image on the popup.', 'wppa'));
						$slug = 'wppa_popup_text_rating';
						$html = '<span class="wppa_rating">'.wppa_checkbox($slug).'</span>';
						$class = 'wppa_rating_ tt_normal wppa_popup';
						wppa_setting($slug, '6', $name, $desc, $html, $help, $class);
						
						$name = __('Show rating count', 'wppa');
						$desc = __('Display the number of votes along with average ratings.', 'wppa');
						$help = esc_js(__('If checked, the number of votes is displayed along with average rating displays on thumbnail and popup displays.', 'wppa'));
						$slug = 'wppa_show_rating_count';
						$html = wppa_checkbox($slug);
						$class = 'wppa_rating_ tt_normal';
						wppa_setting($slug, '7', $name, $desc, $html, $help, $class);

						wppa_setting_subheader('D', '1', __('Album cover related settings', 'wppa'));
						
						$name = __('Covertext', 'wppa');
						$desc = __('Show the text on the album cover.', 'wppa');
						$help = esc_js(__('Display the album decription and the links to the album content', 'wppa'));
						$help .= '\n'.esc_js(__('If switched off, you can only link to the album using the covertitle or the coverphoto.', 'wppa'));
						$help .= '\n'.esc_js(__('Make sure you configure the coverphoto link as desired.', 'wppa'));
						$slug = 'wppa_show_cover_text';
						$html = wppa_checkbox($slug);
						wppa_setting($slug, '1', $name, $desc, $html, $help);
						
						$name = __('Slideshow', 'wppa');
						$desc = __('Enable the slideshow.', 'wppa');
						$help = esc_js(__('If you do not want slideshows: uncheck this box. Browsing full size images will remain possible.', 'wppa'));
						$slug = 'wppa_enable_slideshow';
						$html = wppa_checkbox($slug);
						wppa_setting($slug, '2', $name, $desc, $html, $help);

						$name = __('Slideshow/Browse', 'wppa');
						$desc = __('Display the Slideshow / Browse photos link on album covers', 'wppa');
						$help = esc_js(__('This setting causes the Slideshow link to be displayed on the album cover.', 'wppa'));
						$help .= '\n\n'.esc_js(__('If slideshows are disabled in item 2 in this table, you will see a browse link to fullsize images.', 'wppa'));
						$help .= '\n\n'.esc_js(__('If you do not want the browse link link either, uncheck this item.', 'wppa'));
						$slug = 'wppa_show_slideshowbrowselink';
						$html = wppa_checkbox($slug);
						wppa_setting($slug, '3', $name, $desc, $html, $help);
						
						wppa_setting_subheader('E', '1', __('Widget related settings', 'wppa'));
						
						$name = __('Big Browse Buttons in widget', 'wppa');
						$desc = __('Enable invisible browsing buttons in widget slideshows.', 'wppa');
						$help = esc_js(__('If checked, the fullsize image is covered by two invisible areas that act as browse buttons.', 'wppa'));
						$help .= '\n\n'.esc_js(__('Make sure the Full height (Table I-B2) is properly configured to prevent these areas to overlap unwanted space.', 'wppa'));
						$help .= '\n\n'.esc_js(__('A side effect of this setting is that right clicking the image no longer enables the visitor to download the image.', 'wppa'));
						$slug = 'wppa_show_bbb_widget';
						$html = wppa_checkbox($slug);
						wppa_setting($slug, '1', $name, $desc, $html, $help);

						wppa_setting_subheader('F', '1', __('Lightbox related settings. These settings have effect only when Table IX-A6 is set to wppa', 'wppa'));

						$name = __('Overlay Close label text', 'wppa');
						$desc = __('The text label for the cross exit symbol.', 'wppa');
						$help = __('This text may be multilingual according to the qTranslate short tags specs.', 'wppa');
						$slug = 'wppa_ovl_close_txt';
						$html = wppa_input($slug, '200px');
						wppa_setting($slug, '1', $name, $desc, $html, $help);
						
						$name = __('Overlay theme color', 'wppa');
						$desc = __('The color of the image border and text background.', 'wppa');
						$help = '';
						$slug = 'wppa_ovl_theme';
						$options = array(__('Black', 'wppa'), __('White', 'wppa'));
						$values = array('black', 'white');
						$html = wppa_select($slug, $options, $values);
						wppa_setting($slug, '2', $name, $desc, $html, $help);
						
						
						?>
					</tbody>
					<tfoot style="font-weight: bold;" class="wppa_table_2">
						<tr>
							<th scope="col"><?php _e('#', 'wppa') ?></th>
							<th scope="col"><?php _e('Name', 'wppa') ?></th>
							<th scope="col"><?php _e('Description', 'wppa') ?></th>
							<th scope="col"><?php _e('Setting', 'wppa') ?></th>
							<th scope="col"><?php _e('Help', 'wppa') ?></th>
						</tr>
					</tfoot>
				</table>
			</div>

			<?php // Table 3: Backgrounds ?>
			<h3><?php _e('Table III:', 'wppa'); echo(' '); _e('Backgrounds:', 'wppa'); ?><?php wppa_toggle_table(3) ?>
				<span style="font-weight:normal; font-size:12px;"><?php _e('This table describes the backgrounds of wppa+ elements.', 'wppa'); ?></span>
			</h3>

			<div id="wppa_table_3" style="display:none" >
				<table class="widefat">
					<thead style="font-weight: bold; " class="wppa_table_3">
						<tr>
							<th scope="col"><?php _e('#', 'wppa') ?></th>
							<th scope="col"><?php _e('Name', 'wppa') ?></th>
							<th scope="col"><?php _e('Description', 'wppa') ?></th>
							<th scope="col"><?php _e('Background color', 'wppa') ?></th>
							<th scope="col"><?php _e('Sample', 'wppa') ?></th>
							<th scope="col"><?php _e('Border color', 'wppa') ?></th>
							<th scope="col"><?php _e('Sample', 'wppa') ?></th>
							<th scope="col"><?php _e('Help', 'wppa') ?></th>
						</tr>
					</thead>
					<tbody class="wppa_table_3">
						<?php 
						$wppa_table = 'III';
						wppa_setting_subheader('A', '4', __('Slideshow elements backgrounds', 'wppa'));

						$name = __('Nav', 'wppa');
						$desc = __('Navigation bars.', 'wppa');
						$help = esc_js(__('Enter valid CSS colors for navigation backgrounds and borders.', 'wppa'));
						$slug1 = 'wppa_bgcolor_nav';
						$slug2 = 'wppa_bcolor_nav';
						$slug = array($slug1, $slug2);
						$html1 = wppa_input($slug1, '100px', '', '', "checkColor('".$slug1."')") . '</td><td>' . wppa_color_box($slug1);
						$html2 = wppa_input($slug2, '100px', '', '', "checkColor('".$slug2."')") . '</td><td>' . wppa_color_box($slug2);
						$html = array($html1, $html2);
						wppa_setting($slug, '1', $name, $desc, $html, $help);

						$name = __('FullImg', 'wppa');
						$desc = __('Full size Photos and slideshows.', 'wppa');
						$help = esc_js(__('Enter valid CSS colors for fullsize photo backgrounds and borders.', 'wppa'));
						$help .= '\n'.esc_js(__('The colors may be equal or "transparent"', 'wppa'));
						$help .= '\n'.esc_js(__('For more information about fullsize image borders see the help on Table I-B4', 'wppa'));
						$slug1 = 'wppa_bgcolor_fullimg';
						$slug2 = 'wppa_bcolor_fullimg';
						$slug = array($slug1, $slug2);
						$html1 = wppa_input($slug1, '100px', '', '', "checkColor('".$slug1."')") . '</td><td>' . wppa_color_box($slug1);
						$html2 = wppa_input($slug2, '100px', '', '', "checkColor('".$slug2."')") . '</td><td>' . wppa_color_box($slug2);
						$html = array($html1, $html2);
						wppa_setting($slug, '2', $name, $desc, $html, $help);
					
						$name = __('Numbar', 'wppa');
						$desc = __('Number bar box background.', 'wppa');
						$help = esc_js(__('Enter valid CSS colors for numbar box backgrounds and borders.', 'wppa'));
						$slug1 = 'wppa_bgcolor_numbar';
						$slug2 = 'wppa_bcolor_numbar';
						$slug = array($slug1, $slug2);
						$html1 = wppa_input($slug1, '100px', '', '', "checkColor('".$slug1."')") . '</td><td>' . wppa_color_box($slug1);
						$html2 = wppa_input($slug2, '100px', '', '', "checkColor('".$slug2."')") . '</td><td>' . wppa_color_box($slug2);
						$class = 'wppa_numbar';
						$html = array($html1, $html2);
						wppa_setting($slug, '3', $name, $desc, $html, $help, $class);

						$name = __('Numbar active', 'wppa');
						$desc = __('Number bar active box background.', 'wppa');
						$help = esc_js(__('Enter valid CSS colors for numbar active box backgrounds and borders.', 'wppa'));
						$slug1 = 'wppa_bgcolor_numbar_active';
						$slug2 = 'wppa_bcolor_numbar_active';
						$slug = array($slug1, $slug2);
						$html1 = wppa_input($slug1, '100px', '', '', "checkColor('".$slug1."')") . '</td><td>' . wppa_color_box($slug1);
						$html2 = wppa_input($slug2, '100px', '', '', "checkColor('".$slug2."')") . '</td><td>' . wppa_color_box($slug2);
						$class = 'wppa_numbar';
						$html = array($html1, $html2);
						wppa_setting($slug, '4', $name, $desc, $html, $help, $class);

						$name = __('Name/desc', 'wppa');
						$desc = __('Name and Description bars.', 'wppa');
						$help = esc_js(__('Enter valid CSS colors for name and description box backgrounds and borders.', 'wppa'));
						$slug1 = 'wppa_bgcolor_namedesc';
						$slug2 = 'wppa_bcolor_namedesc';
						$slug = array($slug1, $slug2);
						$html1 = wppa_input($slug1, '100px', '', '', "checkColor('".$slug1."')") . '</td><td>' . wppa_color_box($slug1);
						$html2 = wppa_input($slug2, '100px', '', '', "checkColor('".$slug2."')") . '</td><td>' . wppa_color_box($slug2);
						$html = array($html1, $html2);
						wppa_setting($slug, '5', $name, $desc, $html, $help);
						
						$name = __('Comments', 'wppa');
						$desc = __('Comment input and display areas.', 'wppa');
						$help = esc_js(__('Enter valid CSS colors for comment box backgrounds and borders.', 'wppa'));
						$slug1 = 'wppa_bgcolor_com';
						$slug2 = 'wppa_bcolor_com';
						$slug = array($slug1, $slug2);
						$html1 = wppa_input($slug1, '100px', '', '', "checkColor('".$slug1."')") . '</td><td>' . wppa_color_box($slug1);
						$html2 = wppa_input($slug2, '100px', '', '', "checkColor('".$slug2."')") . '</td><td>' . wppa_color_box($slug2);
						$class = 'wppa_comment_';
						$html = array($html1, $html2);
						wppa_setting($slug, '6', $name, $desc, $html, $help, $class);

						$name = __('Custom', 'wppa');
						$desc = __('Custom box background.', 'wppa');
						$help = esc_js(__('Enter valid CSS colors for custom box backgrounds and borders.', 'wppa'));
						$slug1 = 'wppa_bgcolor_cus';
						$slug2 = 'wppa_bcolor_cus';
						$slug = array($slug1, $slug2);
						$html1 = wppa_input($slug1, '100px', '', '', "checkColor('".$slug1."')") . '</td><td>' . wppa_color_box($slug1);
						$html2 = wppa_input($slug2, '100px', '', '', "checkColor('".$slug2."')") . '</td><td>' . wppa_color_box($slug2);
						$html = array($html1, $html2);
						wppa_setting($slug, '7', $name, $desc, $html, $help);

						$name = __('IPTC', 'wppa');
						$desc = __('IPTC display box background.', 'wppa');
						$help = esc_js(__('Enter valid CSS colors for iptc box backgrounds and borders.', 'wppa'));
						$slug1 = 'wppa_bgcolor_iptc';
						$slug2 = 'wppa_bcolor_iptc';
						$slug = array($slug1, $slug2);
						$html1 = wppa_input($slug1, '100px', '', '', "checkColor('".$slug1."')") . '</td><td>' . wppa_color_box($slug1);
						$html2 = wppa_input($slug2, '100px', '', '', "checkColor('".$slug2."')") . '</td><td>' . wppa_color_box($slug2);
						$html = array($html1, $html2);
						wppa_setting($slug, '8', $name, $desc, $html, $help);

						$name = __('EXIF', 'wppa');
						$desc = __('EXIF display box background.', 'wppa');
						$help = esc_js(__('Enter valid CSS colors for exif box backgrounds and borders.', 'wppa'));
						$slug1 = 'wppa_bgcolor_exif';
						$slug2 = 'wppa_bcolor_exif';
						$slug = array($slug1, $slug2);
						$html1 = wppa_input($slug1, '100px', '', '', "checkColor('".$slug1."')") . '</td><td>' . wppa_color_box($slug1);
						$html2 = wppa_input($slug2, '100px', '', '', "checkColor('".$slug2."')") . '</td><td>' . wppa_color_box($slug2);
						$html = array($html1, $html2);
						wppa_setting($slug, '9', $name, $desc, $html, $help);
			
						wppa_setting_subheader('B', '4', __('Other backgrounds', 'wppa'));
			
						$name = __('Even', 'wppa');
						$desc = __('Even background.', 'wppa');
						$help = esc_js(__('Enter valid CSS colors for even numbered backgrounds and borders of album covers and thumbnail displays \'As covers\'.', 'wppa'));
						$slug1 = 'wppa_bgcolor_even';
						$slug2 = 'wppa_bcolor_even';
						$slug = array($slug1, $slug2);
						$html1 = wppa_input($slug1, '100px', '', '', "checkColor('".$slug1."')") . '</td><td>' . wppa_color_box($slug1);
						$html2 = wppa_input($slug2, '100px', '', '', "checkColor('".$slug2."')") . '</td><td>' . wppa_color_box($slug2);
						$html = array($html1, $html2);
						wppa_setting($slug, '1', $name, $desc, $html, $help);
						
						$name = __('Odd', 'wppa');
						$desc = __('Odd background.', 'wppa');
						$help = esc_js(__('Enter valid CSS colors for odd numbered backgrounds and borders of album covers and thumbnail displays \'As covers\'.', 'wppa'));
						$slug1 = 'wppa_bgcolor_alt';
						$slug2 = 'wppa_bcolor_alt';
						$slug = array($slug1, $slug2);
						$html1 = wppa_input($slug1, '100px', '', '', "checkColor('".$slug1."')") . '</td><td>' . wppa_color_box($slug1);
						$html2 = wppa_input($slug2, '100px', '', '', "checkColor('".$slug2."')") . '</td><td>' . wppa_color_box($slug2);
						$html = array($html1, $html2);
						wppa_setting($slug, '2', $name, $desc, $html, $help);

						$name = __('Img', 'wppa');
						$desc = __('Cover Photos and popups.', 'wppa');
						$help = esc_js(__('Enter valid CSS colors for Cover photo and popup backgrounds and borders.', 'wppa'));
						$slug1 = 'wppa_bgcolor_img';
						$slug2 = 'wppa_bcolor_img';
						$slug = array($slug1, $slug2);
						$html1 = wppa_input($slug1, '100px', '', '', "checkColor('".$slug1."')") . '</td><td>' . wppa_color_box($slug1);
						$html2 = wppa_input($slug2, '100px', '', '', "checkColor('".$slug2."')") . '</td><td>' . wppa_color_box($slug2);
						$html = array($html1, $html2);
						wppa_setting($slug, '3', $name, $desc, $html, $help);
						
						?>
					</tbody>
					<tfoot style="font-weight: bold;" class="wppa_table_3">
						<tr>
							<th scope="col"><?php _e('#', 'wppa') ?></th>
							<th scope="col"><?php _e('Name', 'wppa') ?></th>
							<th scope="col"><?php _e('Description', 'wppa') ?></th>
							<th scope="col"><?php _e('Background color', 'wppa') ?></th>
							<th scope="col"><?php _e('Sample', 'wppa') ?></th>
							<th scope="col"><?php _e('Border color', 'wppa') ?></th>
							<th scope="col"><?php _e('Sample', 'wppa') ?></th>
							<th scope="col"><?php _e('Help', 'wppa') ?></th>
						</tr>
					</tfoot>
				</table>
			</div>
					
			<?php // Table 4: Behaviour ?>
			<h3><?php _e('Table IV:', 'wppa'); echo(' '); _e('Behaviour:', 'wppa'); ?><?php wppa_toggle_table(4) ?>
				<span style="font-weight:normal; font-size:12px;"><?php _e('This table describes the dynamic behaviour of certain wppa+ elements.', 'wppa'); ?></span>
			</h3>

			<div id="wppa_table_4" style="display:none" >
				<table class="widefat">
					<thead style="font-weight: bold; " class="wppa_table_4">
						<tr>
							<th scope="col"><?php _e('#', 'wppa') ?></th>
							<th scope="col"><?php _e('Name', 'wppa') ?></th>
							<th scope="col"><?php _e('Description', 'wppa') ?></th>
							<th scope="col"><?php _e('Setting', 'wppa') ?></th>
							<th scope="col"><?php _e('Help', 'wppa') ?></th>
						</tr>
					</thead>
					<tbody class="wppa_table_4">
						<?php 
						$wppa_table = 'IV';
						wppa_setting_subheader('A', '1', __('System related settings', 'wppa'));
						
						$name = __('Use Ajax', 'wppa');
						$desc = __('Use Ajax as much as is possible and implemented.', 'wppa');
						$help = '';
						$slug = 'wppa_allow_ajax';
						$onchange = 'wppaCheckAjax()';
						$html = wppa_checkbox($slug, $onchange);
						wppa_setting($slug, '1', $name, $desc, $html, $help);
						
						$name = __('Photo names in urls', 'wppa');
						$desc = __('Display photo names in urls, no numbers.', 'wppa');
						$help = esc_js(__('While browsing through a slideshow and Use Ajax is checked, and the browser supports history.pushState,', 'wppa'));
						$help .= ' '.esc_js(__('the photo names will be displayed in the generated urls in the browser address line.', 'wppa'));
						$help .= '\n\n'.esc_js(__('These urls are valid and can be saved for use later.', 'wppa'));
						$slug = 'wppa_use_photo_names_in_urls';
						$html = wppa_checkbox($slug);
						$class = 'wppa_allow_ajax_';
						wppa_setting($slug, '2', $name, $desc, $html, $help, $class);
						
						wppa_setting_subheader('B', '1', __('Slideshow related settings', 'wppa'));

						$name = __('V align', 'wppa');
						$desc = __('Vertical alignment of full-size images.', 'wppa');
						$help = esc_js(__('Specify the vertical alignment of fullsize images.', 'wppa'));
						$help .= '\n'.esc_js(__('If you select --- none ---, the photos will not be centered horizontally either.', 'wppa'));
						$slug = 'wppa_fullvalign';
						$options = array(__('--- none ---', 'wppa'), __('top', 'wppa'), __('center', 'wppa'), __('bottom', 'wppa'), __('fit', 'wppa'));
						$values = array('default', 'top', 'center', 'bottom', 'fit');
						$onchange = 'wppaCheckFullHalign()';
						$html = wppa_select($slug, $options, $values, $onchange);
						wppa_setting($slug, '1', $name, $desc, $html, $help);
						
						$name = __('H align', 'wppa');
						$desc = __('Horizontal alignment of full-size images.', 'wppa');
						$help = esc_js(__('Specify the horizontal alignment of fullsize images. If you specify --- none --- , no horizontal alignment will take place.', 'wppa'));
						$help .= '\n\n'.esc_js(__('This setting is only usefull when the Column Width differs from the Fullsize Width.', 'wppa'));
						$help .= '\n'.esc_js(__('(Settings I-1 and I-2)', 'wppa'));
						$slug = 'wppa_fullhalign';
						$options = array(__('--- none ---', 'wppa'), __('left', 'wppa'), __('center', 'wppa'), __('right', 'wppa'));
						$values = array('default', 'left', 'center', 'right');
						$html = wppa_select($slug, $options, $values);
						$class = 'wppa_ha';
						wppa_setting($slug, '2', $name, $desc, $html, $help, $class);
						
						$name = __('Start', 'wppa');
						$desc = __('Start slideshow running.', 'wppa');
						$help = esc_js(__('If you select "running", the slideshow will start running immediately, if you select "still at first photo", the first photo will be displayed in browse mode.', 'wppa'));
						$help .= '\n\n'.esc_js(__('If you select "still at first norated", the first photo that the visitor did not gave a rating will be displayed in browse mode.', 'wppa'));
						$slug = 'wppa_start_slide';
						$options = array(	__('running', 'wppa'), 
											__('still at first photo', 'wppa'), 
											__('still at first norated', 'wppa')
										);
						$values = array(	'run', 
											'still', 
											'norate'
										);
						$html = wppa_select($slug, $options, $values);
						$class = 'wppa_ss';
						wppa_setting($slug, '3', $name, $desc, $html, $help, $class);
												
						$name = __('Animation type', 'wppa');
						$desc = __('The way successive slides appear.', 'wppa');
						$help = esc_js(__('Select the way the old slide is to be replaced by the new one in the slideshow/browse fullsize display.', 'wppa'));
						$slug = 'wppa_animation_type';
						$options = array(	__('Fade out and in simultaneous', 'wppa'),
											__('Fade in after fade out', 'wppa'),
											__('Shift adjacent', 'wppa'),
											__('Stack on', 'wppa'),
											__('Stack off', 'wppa'),
											__('Turn over', 'wppa')
										);
						$values = array(	'fadeover',
											'fadeafter',
											'swipe',
											'stackon',
											'stackoff',
											'turnover'
									);
						$html = wppa_select($slug, $options, $values);
						wppa_setting($slug, '4', $name, $desc, $html, $help);
						
						$name = __('Timeout', 'wppa');
						$desc = __('Slideshow timeout.', 'wppa');
						$help = esc_js(__('Select the time a single slide will be visible when the slideshow is started.', 'wppa'));
						$slug = 'wppa_slideshow_timeout';
						$options = array(__('very short (1 s.)', 'wppa'), __('short (1.5 s.)', 'wppa'), __('normal (2.5 s.)', 'wppa'), __('long (4 s.)', 'wppa'), __('very long (6 s.)', 'wppa'));
						$values = array('1000', '1500', '2500', '4000', '6000');
						$html = wppa_select($slug, $options, $values);
						$class = 'wppa_ss';
						wppa_setting($slug, '5', $name, $desc, $html, $help, $class);
						
						$name = __('Speed', 'wppa');
						$desc = __('Slideshow animation speed.', 'wppa');
						$help = esc_js(__('Specify the animation speed to be used in slideshows.', 'wppa'));
						$help .= '\n'.esc_js(__('This is the time it takes a photo to fade in or out.', 'wppa'));
						$slug = 'wppa_animation_speed';
						$options = array(__('--- off ---', 'wppa'), __('very fast (200 ms.)', 'wppa'), __('fast (400 ms.)', 'wppa'), __('normal (800 ms.)', 'wppa'),  __('slow (1.2 s.)', 'wppa'), __('very slow (2 s.)', 'wppa'), __('extremely slow (4 s.)', 'wppa'));
						$values = array('10', '200', '400', '800', '1200', '2000', '4000');
						$html = wppa_select($slug, $options, $values);
						$class = 'wppa_ss';
						wppa_setting($slug, '6', $name, $desc, $html, $help, $class);
		
						$name = __('Slide hover pause', 'wppa');
						$desc = __('Running Slideshow suspends during mouse hover.', 'wppa');
						$help = '';
						$slug = 'wppa_slide_pause';
						$html = wppa_checkbox($slug);
						wppa_setting($slug, '7', $name, $desc, $html, $help);
						
						$name = __('Slideshow wrap around', 'wppa');
						$desc = __('The slideshow wraps around the start and end', 'wppa');
						$help = '';
						$slug = 'wppa_slide_wrap';
						$html = wppa_checkbox($slug);
						wppa_setting($slug, '8', $name, $desc, $html, $help);
						
						$name = __('Full desc align', 'wppa');
						$desc = __('The alignment of the descriptions under fullsize images and slideshows.', 'wppa');
						$help = '';
						$slug = 'wppa_fulldesc_align';
						$options = array(__('Left', 'wppa'), __('Center', 'wppa'), __('Right', 'wppa'));
						$values = array('left', 'center', 'right');
						$html = wppa_select($slug, $options, $values);
						wppa_setting($slug, '9', $name, $desc, $html, $help);
						
						$name = __('Remove redundant space', 'wppa');
						$desc = __('Removes unwanted &lt;p> and &lt;br> tags in fullsize descriptions.', 'wppa');
						$help = __('This setting has only effect when Table IX-A7 (foreign shortcodes) is checked.', 'wppa');
						$slug = 'wppa_clean_pbr';
						$html = wppa_checkbox($slug);
						wppa_setting($slug, '10', $name, $desc, $html, $help);
						
						wppa_setting_subheader('C', '1', __('Thumbnail related settings', 'wppa'));

						$name = __('Photo order', 'wppa');
						$desc = __('Photo ordering sequence method.', 'wppa');
						$help = esc_js(__('Specify the way the photos should be ordered. This is the default setting. You can overrule the default sorting order on a per album basis.', 'wppa'));
						$slug = 'wppa_list_photos_by';
						$options = array(__('--- none ---', 'wppa'), __('Order #', 'wppa'), __('Name', 'wppa'), __('Random', 'wppa'), __('Rating mean value', 'wppa'), __('Number of votes', 'wppa'), __('Timestamp', 'wppa'));
						$values = array('0', '1', '2', '3', '4', '6', '5');
						$html = wppa_select($slug, $options, $values);
						wppa_setting($slug, '1', $name, $desc, $html, $help);
						
						$name = __('Descending', 'wppa');
						$desc = __('Descending order.', 'wppa');
						$help = esc_js(__('If checked: largest first', 'wppa'));
						$help .= '\n'.esc_js(__('This is a system wide setting.', 'wppa'));
						$slug = 'wppa_list_photos_desc';
						$html = wppa_checkbox($slug);
						wppa_setting($slug, '2', $name, $desc, $html, $help);
						
						$name = __('Thumbnail type', 'wppa');
						$desc = __('The way the thumbnail images are displayed.', 'wppa');
						$help = esc_js(__('You may select an altenative display method for thumbnails. Note that some of the thumbnail settings do not apply to all available display methods.', 'wppa'));
						$slug = 'wppa_thumbtype';
						$options = array(__('--- default ---', 'wppa'), __('like album covers', 'wppa'), __('--- none ---', 'wppa'));
						$values = array('default', 'ascovers', 'none');
						$onchange = 'wppaCheckThumbType()';
						$html = wppa_select($slug, $options, $values, $onchange);
						wppa_setting($slug, '3', $name, $desc, $html, $help);

						$name = __('Placement', 'wppa');
						$desc = __('Thumbnail image left or right.', 'wppa');
						$help = esc_js(__('Indicate the placement position of the thumbnailphoto you wish.', 'wppa'));
						$slug = 'wppa_thumbphoto_left';
						$options = array(__('Left', 'wppa'), __('Right', 'wppa'));
						$values = array('yes', 'no');
						$html = wppa_select($slug, $options, $values);
						$class = 'tt_ascovers';
						wppa_setting($slug, '4', $name, $desc, $html, $help, $class);

						$name = __('Vertical alignment', 'wppa');
						$desc = __('Vertical alignment of thumbnails.', 'wppa');
						$help = esc_js(__('Specify the vertical alignment of thumbnail images. Use this setting when albums contain both portrait and landscape photos.', 'wppa'));
						$help .= '\n'.esc_js(__('It is NOT recommended to use the value --- default ---; it will affect the horizontal alignment also and is meant to be used with custom css.', 'wppa'));
						$slug = 'wppa_valign';
						$options = array( __('--- default ---', 'wppa'), __('top', 'wppa'), __('center', 'wppa'), __('bottom', 'wppa'));
						$values = array('default', 'top', 'center', 'bottom');
						$html = wppa_select($slug, $options, $values);
						$class = 'tt_normal';
						wppa_setting($slug, '5', $name, $desc, $html, $help, $class);
						
						$name = __('Thumb mouseover', 'wppa');
						$desc = __('Apply thumbnail mouseover effect.', 'wppa');
						$help = esc_js(__('Check this box to use mouseover effect on thumbnail images.', 'wppa'));
						$slug = 'wppa_use_thumb_opacity';
						$onchange = 'wppaCheckUseThumbOpacity()';
						$html = wppa_checkbox($slug, $onchange);
						$class = 'tt_normal';
						wppa_setting($slug, '6', $name, $desc, $html, $help, $class);
						
						$name = __('Thumb opacity', 'wppa');
						$desc = __('Initial opacity value.', 'wppa');
						$help = esc_js(__('Enter percentage of opacity. 100% is opaque, 0% is transparant', 'wppa'));
						$slug = 'wppa_thumb_opacity';
						$html = '<span class="thumb_opacity_html">'.wppa_input($slug, '50px', '', __('%', 'wppa')).'</span>';
						$class = 'tt_normal';
						wppa_setting($slug, '7', $name, $desc, $html, $help, $class);

						$name = __('Thumb popup', 'wppa');
						$desc = __('Use popup effect on thumbnail images.', 'wppa');
						$help = esc_js(__('Thumbnails pop-up to a larger image when hovered.', 'wppa'));
						$slug = 'wppa_use_thumb_popup';
						$onchange = 'wppaCheckPopup()';
						$html = wppa_checkbox($slug, $onchange);
						$class = 'tt_normal';
						wppa_setting($slug, '8', $name, $desc, $html, $help, $class);
						
						wppa_setting_subheader('D', '1', __('Album and covers related settings', 'wppa'));
						
						$name = __('Album order', 'wppa');
						$desc = __('Album ordering sequence method.', 'wppa');
						$help = esc_js(__('Specify the way the albums should be ordered.', 'wppa'));
						$slug = 'wppa_list_albums_by';
						$options = array(__('--- none ---', 'wppa'), __('Order #', 'wppa'), __('Name', 'wppa'), __('Random', 'wppa'));
						$values = array('0', '1', '2', '3');
						$html = wppa_select($slug, $options, $values);
						wppa_setting($slug, '1', $name, $desc, $html, $help);
						
						$name = __('Descending', 'wppa');
						$desc = __('Descending order.', 'wppa');
						$help = esc_js(__('If checked: largest first', 'wppa'));
						$slug = 'wppa_list_albums_desc';
						$html = wppa_checkbox($slug);
						wppa_setting($slug, '2', $name, $desc, $html, $help);
						
						$name = __('Placement', 'wppa');
						$desc = __('Cover image position.', 'wppa');
						$help = esc_js(__('Indicate the placement position of the coverphoto you wish.', 'wppa'));
						$slug = 'wppa_coverphoto_pos';
						$options = array(__('Left', 'wppa'), __('Right', 'wppa'), __('Top', 'wppa'), __('Bottom', 'wppa'));
						$values = array('left', 'right', 'top', 'bottom');
						$html = wppa_select($slug, $options, $values);
						wppa_setting($slug, '3', $name, $desc, $html, $help);

						$name = __('Cover mouseover', 'wppa');
						$desc = __('Apply coverphoto mouseover effect.', 'wppa');
						$help = esc_js(__('Check this box to use mouseover effect on cover images.', 'wppa'));
						$slug = 'wppa_use_cover_opacity';
						$onchange = 'wppaCheckUseCoverOpacity()';
						$html = wppa_checkbox($slug, $onchange);
						wppa_setting($slug, '4', $name, $desc, $html, $help);

						$name = __('Cover opacity', 'wppa');
						$desc = __('Initial opacity value.', 'wppa');
						$help = esc_js(__('Enter percentage of opacity. 100% is opaque, 0% is transparant', 'wppa'));
						$slug = 'wppa_cover_opacity';
						$html = '<span class="cover_opacity_html">'.wppa_input($slug, '50px', '', __('%', 'wppa')).'</span>';
						$class = 'tt_normal';
						wppa_setting($slug, '5', $name, $desc, $html, $help, $class);

						wppa_setting_subheader('E', '1', __('Rating related settings', 'wppa'), 'wppa_rating_');	

						$name = __('Rating login', 'wppa');
						$desc = __('Users must login to rate photos.', 'wppa');
						$help = esc_js(__('If users want to vote for a photo (rating 1..5 stars) the must login first. The avarage rating will always be displayed as long as the rating system is enabled.', 'wppa'));
						$slug = 'wppa_rating_login';
						$html = wppa_checkbox($slug);
						$class = 'wppa_rating_';
						wppa_setting($slug, '1', $name, $desc, $html, $help, $class);
						
						$name = __('Rating change', 'wppa');
						$desc = __('Users may change their ratings.', 'wppa');
						$help = esc_js(__('Users may change their ratings.', 'wppa'));
						$slug = 'wppa_rating_change';
						$html = wppa_checkbox($slug);
						$class = 'wppa_rating_';
						wppa_setting($slug, '2', $name, $desc, $html, $help, $class);
						
						$name = __('Rating multi', 'wppa');
						$desc = __('Users may give multiple votes.', 'wppa');
						$help = esc_js(__('Users may give multiple votes. (This has no effect when users may change their votes.)', 'wppa'));
						$slug = 'wppa_rating_multi';
						$html = wppa_checkbox($slug);
						$class = 'wppa_rating_';
						wppa_setting($slug, '3', $name, $desc, $html, $help, $class);

						$name = __('Rating use Ajax', 'wppa');
						$desc = __('Use Ajax technology in rating (voting)', 'wppa');
						$help = esc_js(__('If checked, the page is updated rather than reloaded after clicking a rating star.', 'wppa'));
						$help .= '\n\n'.esc_js(__('Enabling this feature ensures the fastest rating mechanism possible.', 'wppa'));
						$slug = 'wppa_rating_use_ajax';
						$html = wppa_checkbox($slug);
						$class = 'wppa_rating_';
						wppa_setting($slug, '4', $name, $desc, $html, $help, $class);
						
						$name = __('Next after vote', 'wppa');
						$desc = __('Goto next slide after voting', 'wppa');
						$help = esc_js(__('If checked, the visitor goes straight to the slide following the slide he voted. This will speed up mass voting.', 'wppa'));
						$slug = 'wppa_next_on_callback';
						$html = wppa_checkbox($slug);
						$class = 'wppa_rating_';
						wppa_setting($slug, '5', $name, $desc, $html, $help, $class);
						
						$name = __('Star off opacity', 'wppa');
						$desc = __('Rating star off state opacity value.', 'wppa');
						$help = esc_js(__('Enter percentage of opacity. 100% is opaque, 0% is transparant', 'wppa'));
						$slug = 'wppa_star_opacity';
						$html = wppa_input($slug, '50px', '', __('%', 'wppa'));
						$class = 'wppa_rating_';
						wppa_setting($slug, '6', $name, $desc, $html, $help, $class);
						
						wppa_setting_subheader('F', '1', __('Comments related settings', 'wppa'), 'wppa_comment_');
						
						$name = __('Commenting login', 'wppa');
						$desc = __('Users must be logged in to comment on photos.', 'wppa');
						$help = esc_js(__('Check this box if you want users to be logged in to be able to enter comments on individual photos.', 'wppa'));
						$slug = 'wppa_comment_login';
						$html = wppa_checkbox($slug);
						$class = 'wppa_comment_';
						wppa_setting($slug, '1', $name, $desc, $html, $help, $class);
						
						$name = __('Last comment first', 'wppa');
						$desc = __('Display the newest comment on top.', 'wppa');
						$help = esc_js(__('If checked: Display the newest comment on top.', 'wppa'));
						$help .= '\n\n'.esc_js(__('If unchecked, the comments are listed in the ordere they were entered.', 'wppa'));
						$slug = 'wppa_comments_desc';
						$html = wppa_checkbox($slug);
						$class = 'wppa_comment_';
						wppa_setting($slug, '2', $name, $desc, $html, $help, $class);
						
						$name = __('Comment moderation', 'wppa');
						$desc = __('Comments from what users need approval.', 'wppa');
						$help = esc_js(__('Select the desired users of which the comments need approval.', 'wppa'));
						$slug = 'wppa_comment_moderation';
						$options = array(__('All users', 'wppa'), __('Logged out users', 'wppa'), __('No users', 'wppa'));
						$values = array('all', 'logout', 'none');
						$html = wppa_select($slug, $options, $values);
						$class = 'wppa_comment_';
						wppa_setting($slug, '3', $name, $desc, $html, $help, $class);
						
						$name = __('Comment email required', 'wppa');
						$desc = __('Commenting users must enter their email addresses.', 'wppa');
						$help = '';
						$slug = 'wppa_comment_email_required';
						$html = wppa_checkbox($slug);
						$class = 'wppa_comment_';
						wppa_setting($slug, '4', $name, $desc, $html, $help, $class);
						
						wppa_setting_subheader('G', '1', __('Lightbox related settings. These settings have effect only when Table IX-A6 is set to wppa', 'wppa'));
						
						$name = __('Overlay opacity', 'wppa');
						$desc = __('The opacity of the lightbox overlay background.', 'wppa');
						$help = '';
						$slug = 'wppa_ovl_opacity';
						$html = wppa_input($slug, '50px', '', __('%', 'wppa'));
						wppa_setting($slug, '1', $name, $desc, $html, $help);
						
						$name = __('Click on background', 'wppa');
						$desc = __('Select the action to be taken on click on background.', 'wppa');
						$help = '';
						$slug = 'wppa_ovl_onclick';
						$options = array(__('Nothing', 'wppa'), __('Exit (close)', 'wppa'), __('Browse (left/right)', 'wppa'));
						$values = array('none', 'close', 'browse');
						$html = wppa_select($slug, $options, $values);
						wppa_setting($slug, '2', $name, $desc, $html, $help);
						
						$name = __('Overlay animation speed', 'wppa');
						$desc = __('The fade-in time of the lightbox images', 'wppa');
						$help = '';
						$slug = 'wppa_ovl_anim';
						$options = array(__('--- off ---', 'wppa'), __('very fast (100 ms.)', 'wppa'), __('fast (200 ms.)', 'wppa'), __('normal (300 ms.)', 'wppa'),  __('slow (500 ms.)', 'wppa'), __('very slow (1 s.)', 'wppa'), __('extremely slow (2 s.)', 'wppa'));
						$values = array('0', '100', '200', '300', '500', '1000', '2000');
						$html = wppa_select($slug, $options, $values);
						wppa_setting($slug, '3', $name, $desc, $html, $help);
						
						?>
					</tbody>
					<tfoot style="font-weight: bold;" class="wppa_table_4">
						<tr>
							<th scope="col"><?php _e('#', 'wppa') ?></th>
							<th scope="col"><?php _e('Name', 'wppa') ?></th>
							<th scope="col"><?php _e('Description', 'wppa') ?></th>
							<th scope="col"><?php _e('Setting', 'wppa') ?></th>
							<th scope="col"><?php _e('Help', 'wppa') ?></th>
						</tr>
					</tfoot>
				</table>
			</div>

			<?php // Table 5: Fonts ?>
			<h3><?php _e('Table V:', 'wppa'); echo(' '); _e('Fonts:', 'wppa'); ?><?php wppa_toggle_table(5) ?>
				<span style="font-weight:normal; font-size:12px;"><?php _e('This table describes the Fonts used for the wppa+ elements.', 'wppa'); ?>
						<?php _e('If you leave fields empty, your themes defaults will be used.', 'wppa'); ?></span>
			</h3>
			
			<div id="wppa_table_5" style="display:none" >
				<table class="widefat">
					<thead style="font-weight: bold; " class="wppa_table_5">
						<tr>
							<th scope="col"><?php _e('#', 'wppa') ?></th>
							<th scope="col"><?php _e('Name', 'wppa') ?></th>
							<th scope="col"><?php _e('Description', 'wppa') ?></th>
							<th scope="col" style="min-width:250px;" ><?php _e('Font family', 'wppa') ?></th>
							<th scope="col"><?php _e('Font size', 'wppa') ?></th>
							<th scope="col"><?php _e('Font color', 'wppa') ?></th>
							<th scope="col"><?php _e('Font weight', 'wppa') ?></th>
							<th scope="col"><?php _e('Help', 'wppa') ?></th>
						</tr>
					</thead>
					<tbody class="wppa_table_5">
						<?php 
						$wppa_table = 'V';
						
						$options = array(__('normal', 'wppa'), __('bold', 'wppa'), __('bolder', 'wppa'), __('lighter', 'wppa'), '100', '200', '300', '400', '500', '600', '700', '800', '900');
						$values = array('normal', 'bold', 'bolder', 'lighter', '100', '200', '300', '400', '500', '600', '700', '800', '900');
						
						$name = __('Album titles', 'wppa');
						$desc = __('Font used for Album titles.', 'wppa');
						$help = esc_js(__('Enter font name, size, color and weight for album cover titles.', 'wppa'));
						$slug1 = 'wppa_fontfamily_title';
						$slug2 = 'wppa_fontsize_title';
						$slug3 = 'wppa_fontcolor_title';
						$slug4 = 'wppa_fontweight_title';
						$slug = array($slug1, $slug2, $slug3, $slug4);
						$html1 = wppa_input($slug1, '90%', '200px', '');
						$html2 = wppa_input($slug2, '40px', '', __('pixels', 'wppa'));
						$html3 = wppa_input($slug3, '70px', '', '');
						$html4 = wppa_select($slug4, $options, $values);
						$html = array($html1, $html2, $html3, $html4);
						wppa_setting($slug, '1a,b,c,d', $name, $desc, $html, $help);

						$name = __('Fullsize desc', 'wppa');
						$desc = __('Font for fullsize photo descriptions.', 'wppa');
						$help = esc_js(__('Enter font name, size, color and weight for fullsize photo descriptions.', 'wppa'));
						$slug1 = 'wppa_fontfamily_fulldesc';
						$slug2 = 'wppa_fontsize_fulldesc';
						$slug3 = 'wppa_fontcolor_fulldesc';
						$slug4 = 'wppa_fontweight_fulldesc';
						$slug = array($slug1, $slug2, $slug3, $slug4);
						$html1 = wppa_input($slug1, '90%', '200px', '');
						$html2 = wppa_input($slug2, '40px', '', __('pixels', 'wppa'));
						$html3 = wppa_input($slug3, '70px', '', '');
						$html4 = wppa_select($slug4, $options, $values);
						$html = array($html1, $html2, $html3, $html4);
						wppa_setting($slug, '2a,b,c,d', $name, $desc, $html, $help);
						
						$name = __('Fullsize name', 'wppa');
						$desc = __('Font for fullsize photo names.', 'wppa');
						$help = esc_js(__('Enter font name, size, color and weight for fullsize photo names.', 'wppa'));
						$slug1 = 'wppa_fontfamily_fulltitle';
						$slug2 = 'wppa_fontsize_fulltitle';
						$slug3 = 'wppa_fontcolor_fulltitle';
						$slug4 = 'wppa_fontweight_fulltitle';
						$slug = array($slug1, $slug2, $slug3, $slug4);
						$html1 = wppa_input($slug1, '90%', '200px', '');
						$html2 = wppa_input($slug2, '40px', '', __('pixels', 'wppa'));
						$html3 = wppa_input($slug3, '70px', '', '');
						$html4 = wppa_select($slug4, $options, $values);
						$html = array($html1, $html2, $html3, $html4);
						wppa_setting($slug, '3a,b,c,d', $name, $desc, $html, $help);
						
						$name = __('Navigations', 'wppa');
						$desc = __('Font for navigations.', 'wppa');
						$help = esc_js(__('Enter font name, size, color and weight for navigation items.', 'wppa'));
						$slug1 = 'wppa_fontfamily_nav';
						$slug2 = 'wppa_fontsize_nav';
						$slug3 = 'wppa_fontcolor_nav';
						$slug4 = 'wppa_fontweight_nav';
						$slug = array($slug1, $slug2, $slug3, $slug4);
						$html1 = wppa_input($slug1, '90%', '200px', '');
						$html2 = wppa_input($slug2, '40px', '', __('pixels', 'wppa'));
						$html3 = wppa_input($slug3, '70px', '', '');
						$html4 = wppa_select($slug4, $options, $values);
						$html = array($html1, $html2, $html3, $html4);
						wppa_setting($slug, '4a,b,c,d', $name, $desc, $html, $help);
						
						$name = __('Thumbnails', 'wppa');
						$desc = __('Font for text under thumbnails.', 'wppa');
						$help = esc_js(__('Enter font name, size, color and weight for text under thumbnail images.', 'wppa'));
						$slug1 = 'wppa_fontfamily_thumb';
						$slug2 = 'wppa_fontsize_thumb';
						$slug3 = 'wppa_fontcolor_thumb';
						$slug4 = 'wppa_fontweight_thumb';
						$slug = array($slug1, $slug2, $slug3, $slug4);
						$html1 = wppa_input($slug1, '90%', '200px', '');
						$html2 = wppa_input($slug2, '40px', '', __('pixels', 'wppa'));
						$html3 = wppa_input($slug3, '70px', '', '');
						$html4 = wppa_select($slug4, $options, $values);
						$html = array($html1, $html2, $html3, $html4);
						wppa_setting($slug, '5a,b,c,d', $name, $desc, $html, $help);
						
						$name = __('Other', 'wppa');
						$desc = __('General font in wppa boxes.', 'wppa');
						$help = esc_js(__('Enter font name, size, color and weight for all other items.', 'wppa')); 
						$slug1 = 'wppa_fontfamily_box';
						$slug2 = 'wppa_fontsize_box';
						$slug3 = 'wppa_fontcolor_box';
						$slug4 = 'wppa_fontweight_box';
						$slug = array($slug1, $slug2, $slug3, $slug4);
						$html1 = wppa_input($slug1, '90%', '200px', '');
						$html2 = wppa_input($slug2, '40px', '', __('pixels', 'wppa'));
						$html3 = wppa_input($slug3, '70px', '', '');
						$html4 = wppa_select($slug4, $options, $values);
						$html = array($html1, $html2, $html3, $html4);
						wppa_setting($slug, '6a,b,c,d', $name, $desc, $html, $help);

						$name = __('Numbar', 'wppa');
						$desc = __('Font in wppa number bars.', 'wppa');
						$help = esc_js(__('Enter font name, size, color and weight for numberbar navigation.', 'wppa')); 
						$slug1 = 'wppa_fontfamily_numbar';
						$slug2 = 'wppa_fontsize_numbar';
						$slug3 = 'wppa_fontcolor_numbar';
						$slug4 = 'wppa_fontweight_numbar';
						$slug = array($slug1, $slug2, $slug3, $slug4);
						$html1 = wppa_input($slug1, '90%', '200px', '');
						$html2 = wppa_input($slug2, '40px', '', __('pixels', 'wppa'));
						$html3 = wppa_input($slug3, '70px', '', '');
						$html4 = wppa_select($slug4, $options, $values);
						$html = array($html1, $html2, $html3, $html4);
						wppa_setting($slug, '7a,b,c,d', $name, $desc, $html, $help);

						$name = __('Numbar Active', 'wppa');
						$desc = __('Font in wppa number bars, active item.', 'wppa');
						$help = esc_js(__('Enter font name, size, color and weight for numberbar navigation.', 'wppa')); 
						$slug1 = 'wppa_fontfamily_numbar_active';
						$slug2 = 'wppa_fontsize_numbar_active';
						$slug3 = 'wppa_fontcolor_numbar_active';
						$slug4 = 'wppa_fontweight_numbar_active';
						$slug = array($slug1, $slug2, $slug3, $slug4);
						$html1 = wppa_input($slug1, '90%', '200px', '');
						$html2 = wppa_input($slug2, '40px', '', __('pixels', 'wppa'));
						$html3 = wppa_input($slug3, '70px', '', '');
						$html4 = wppa_select($slug4, $options, $values);
						$html = array($html1, $html2, $html3, $html4);
						wppa_setting($slug, '8a,b,c,d', $name, $desc, $html, $help);
						
						?>
					</tbody>
					<tfoot style="font-weight: bold;" class="wppa_table_5">
						<tr>
							<th scope="col"><?php _e('#', 'wppa') ?></th>
							<th scope="col"><?php _e('Name', 'wppa') ?></th>
							<th scope="col"><?php _e('Description', 'wppa') ?></th>
							<th scope="col"><?php _e('Font family', 'wppa') ?></th>
							<th scope="col"><?php _e('Font size', 'wppa') ?></th>
							<th scope="col"><?php _e('Font color', 'wppa') ?></th>
							<th scope="col"><?php _e('Font weight', 'wppa') ?></th>
							<th scope="col"><?php _e('Help', 'wppa') ?></th>
						</tr>
					</tfoot>
				</table>
			</div>

			<?php // Table 6: Links ?>
			<h3><?php _e('Table VI:', 'wppa'); echo(' '); _e('Links:', 'wppa'); ?><?php wppa_toggle_table(6) ?>
				<span style="font-weight:normal; font-size:12px;"><?php _e('This table defines the link types and pages.', 'wppa'); ?></span>
			</h3>
			
			<div id="wppa_table_6" style="display:none" >
				<table class="widefat">
					<thead style="font-weight: bold; " class="wppa_table_6">
						<tr>
							<th scope="col"><?php _e('#', 'wppa') ?></th>
							<th scope="col"><?php _e('Name', 'wppa') ?></th>
							<th scope="col"><?php _e('Description', 'wppa') ?></th>
							<th scope="col"><?php _e('Link type', 'wppa') ?></th>
							<th scope="col"><?php _e('Link page', 'wppa') ?></th>
							<th scope="col"><?php _e('New tab', 'wppa') ?></th>
							<th scope="col" title="<?php _e('Photo specific link overrules', 'wppa') ?>" style="cursor: default"><?php _e('PSO', 'wppa') ?></th>
							<th scope="col"><?php _e('Help', 'wppa') ?></th>
						</tr>
					</thead>
					<tbody class="wppa_table_6">
						<?php 
						$wppa_table = 'VI';
						// Linktypes
						$options_linktype = array(__('no link at all.', 'wppa'), __('the plain photo (file).', 'wppa'), __('the full size photo in a slideshow.', 'wppa'), __('the fullsize photo on its own.', 'wppa'), __('the fullsize photo with a print button.', 'wppa'), __('lightbox.', 'wppa'));
						$values_linktype = array('none', 'file', 'photo', 'single', 'fullpopup', 'lightbox'); //, 'indiv');
						$options_linktype_album = array(__('no link at all.', 'wppa'), __('the plain photo (file).', 'wppa'), __('the content of the album.', 'wppa'), __('the full size photo in a slideshow.', 'wppa'), __('the fullsize photo on its own.', 'wppa')); //, __('the photo specific link.', 'wppa'));
						$values_linktype_album = array('none', 'file', 'album', 'photo', 'single'); //, 'indiv');
						$options_linktype_ss_widget = array(__('no link at all.', 'wppa'), __('the plain photo (file).', 'wppa'), __('defined at widget activation.', 'wppa'), __('the content of the album.', 'wppa'), __('the full size photo in a slideshow.', 'wppa'), __('the fullsize photo on its own.', 'wppa')); //, __('the photo specific link.', 'wppa'));
						$values_linktype_ss_widget = array('none', 'file', 'widget', 'album', 'photo', 'single'); //, 'indiv');
						$options_linktype_potd_widget = array(__('no link at all.', 'wppa'), __('the plain photo (file).', 'wppa'), __('defined on widget admin page.', 'wppa'), __('the content of the album.', 'wppa'), __('the full size photo in a slideshow.', 'wppa'), __('the fullsize photo on its own.', 'wppa'), __('lightbox.', 'wppa')); //, __('the photo specific link.', 'wppa'));
						$values_linktype_potd_widget = array('none', 'file', 'custom', 'album', 'photo', 'single', 'lightbox'); //, 'indiv');
						$options_linktype_cover_image = array(__('no link at all.', 'wppa'), __('the plain photo (file).', 'wppa'), __('same as title.', 'wppa'));
						$values_linktype_cover_image = array('none', 'file', 'same');

						// Linkpages
						$options_page = false;
						$options_page_post = false;
						$values_page = false;
						$values_page_post = false;
						// First
						$options_page_post[] = __('--- The same post or page ---', 'wppa');
						$values_page_post[] = '0';
						$options_page[] = __('--- Please select a page ---', 'wppa');
						$values_page[] = '0';
						// Pages if any
						$query = $wpdb->prepare( "SELECT ID, post_title, post_content FROM " . $wpdb->posts . " WHERE post_type = 'page' AND post_status = 'publish' ORDER BY post_title ASC" );
						$pages = $wpdb->get_results ($query, 'ARRAY_A');
						if ($pages) {
							foreach ($pages as $page) {
								if (stripos($page['post_content'], '%%wppa%%') !== false) {
									$options_page[] = __($page['post_title']);
									$options_page_post[] = __($page['post_title']);
									$values_page[] = $page['ID'];
									$values_page_post[] = $page['ID'];
								}
							}
						}
						else {
							$options_page[] = __('--- No page to link to (yet) ---', 'wppa');
							$values_page[] = '0';
						}

						$name = __('Mphoto', 'wppa');
						$desc = __('Media-like photo link.', 'wppa');
						$help = esc_js(__('Select the type of link you want, or no link at all.', 'wppa')); 
						$help .= '\n'.esc_js(__('If you select the fullsize photo on its own, it will be stretched to fit, regardless of that setting.', 'wppa')); /* oneofone is treated as portrait only */
						$help .= '\n'.esc_js(__('Note that a page must have at least %%wppa%% in its content to show up the photo(s).', 'wppa')); 
						$slug1 = 'wppa_mphoto_linktype';
						$slug2 = 'wppa_mphoto_linkpage';
						$slug3 = 'wppa_mphoto_blank';
						$slug4 = 'wppa_mphoto_overrule';
						$slug = array($slug1, $slug2, $slug3, $slug4);
						$onchange = 'wppaCheckMphotoLink()';
						$html1 = wppa_select($slug1, $options_linktype_album, $values_linktype_album, $onchange);
						$class = 'wppa_mlp';
						$html2 = wppa_select($slug2, $options_page, $values_page, '', $class, true);
						$class = 'wppa_mlb';
						$html3 = wppa_checkbox($slug3, '', $class);
						$html4 = wppa_checkbox($slug4);
						$html = array($html1, $html2, $html3, $html4);
						wppa_setting($slug, '1a,b,c,d', $name, $desc, $html, $help);

						$name = __('Thumbnail', 'wppa');
						$desc = __('Thumbnail link.', 'wppa');
						$help = esc_js(__('Select the type of link you want, or no link at all.', 'wppa'));
						$help .= '\n'.esc_js(__('If you select the fullsize photo on its own, it will be stretched to fit, regardless of that setting.', 'wppa')); /* oneofone is treated as portrait only */ 
						$help .= '\n'.esc_js(__('Note that a page must have at least %%wppa%% in its content to show up the photo(s).', 'wppa'));
						$slug1 = 'wppa_thumb_linktype';
						$slug2 = 'wppa_thumb_linkpage';
						$slug3 = 'wppa_thumb_blank';
						$slug4 = 'wppa_thumb_overrule';
						$slug = array($slug1, $slug2, $slug3, $slug4);
						$onchange = 'wppaCheckThumbLink()';
						$html1 = wppa_select($slug1, $options_linktype, $values_linktype, $onchange);
						$class = 'wppa_tlp';
						$html2 = wppa_select($slug2, $options_page_post, $values_page_post, '', $class);
						$class = 'wppa_tlb';
						$html3 = wppa_checkbox($slug3, '', $class);
						$html4 = wppa_checkbox($slug4);
						$html = array($html1, $html2, $html3, $html4);
						$class = 'tt_always';
						wppa_setting($slug, '2a,b,c,d', $name, $desc, $html, $help, $class);
						
						$name = __('TopTenWidget', 'wppa');
						$desc = __('TopTen widget photo link.', 'wppa');
						$help = esc_js(__('Select the type of link the top ten photos point to.', 'wppa')); 
						$slug1 = 'wppa_topten_widget_linktype'; 
						$slug2 = 'wppa_topten_widget_linkpage';
						$slug3 = 'wppa_topten_blank';
						$slug4 = 'wppa_topten_overrule';
						$slug = array($slug1, $slug2, $slug3, $slug4);
						$onchange = 'wppaCheckTopTenLink()';
						$html1 = wppa_select($slug1, $options_linktype, $values_linktype, $onchange);
						$class = 'wppa_ttlp';
						$html2 = wppa_select($slug2, $options_page, $values_page, '', $class, true);
						$class = 'wppa_ttlb';
						$html3 = wppa_checkbox($slug3, '', $class);
						$html4 = wppa_checkbox($slug4);
						$html = array($html1, $html2, $html3, $html4);
						$class = 'wppa_rating';
						wppa_setting($slug, '3a,b,c,d', $name, $desc, $html, $help, $class);
						
						$name = __('SlideWidget', 'wppa');
						$desc = __('Slideshow widget photo link.', 'wppa');
						$help = esc_js(__('Select the type of link the top ten photos point to.', 'wppa')); 
						$slug1 = 'wppa_slideonly_widget_linktype';
						$slug2 = 'wppa_slideonly_widget_linkpage';
						$slug3 = 'wppa_sswidget_blank';
						$slug4 = 'wppa_sswidget_overrule';
						$slug = array($slug1, $slug2, $slug3, $slug4);
						$onchange = 'wppaCheckSlideOnlyLink()';
						$html1 = wppa_select($slug1, $options_linktype_ss_widget, $values_linktype_ss_widget, $onchange);
						$class = 'wppa_solp';
						$html2 = wppa_select($slug2, $options_page, $values_page, '', $class, true);
						$class = 'wppa_solb';
						$html3 = wppa_checkbox($slug3, '', $class);
						$html4 = wppa_checkbox($slug4);
						$html = array($html1, $html2, $html3, $html4);
						wppa_setting($slug, '4a,b,c,d', $name, $desc, $html, $help);
						
						$name = __('PotdWidget', 'wppa');
						$desc = __('Photo Of The Day widget link.', 'wppa');
						$help = esc_js(__('Select the type of link the photo of the day points to.', 'wppa')); 
						$help .= '\n\n'.esc_js(__('If you select \'defined on widget admin page\' you can manually enter a link and title on the Photo of the day Widget Admin page.', 'wppa'));
						$slug1 = 'wppa_widget_linktype';
						$slug2 = 'wppa_widget_linkpage';
						$slug3 = 'wppa_potd_blank';
						$slug4 = 'wppa_potdwidget_overrule';
						$slug = array($slug1, $slug2, $slug3, $slug4);
						$onchange = 'wppaCheckPotdLink()';
						$html1 = wppa_select($slug1, $options_linktype_potd_widget, $values_linktype_potd_widget, $onchange);
						$class = 'wppa_potdlp';
						$html2 = wppa_select($slug2, $options_page, $values_page, '', $class, true);
						$class = 'wppa_potdlb';
						$html3 = wppa_checkbox($slug3, '', $class);
						$html4 = wppa_checkbox($slug4);
						$html = array($html1, $html2, $html3, $html4);
						wppa_setting($slug, '5a,b,c,d', $name, $desc, $html, $help);
						
						$name = __('Cover Image', 'wppa');
						$desc = __('The link from the cover image of an album.', 'wppa');
						$help = esc_js(__('Select the type of link the coverphoto points to.', 'wppa'));
						$help .= '\n\n'.esc_js(__('The link from the album title can be configured on the Edit Album page.', 'wppa'));
						$help .= '\n'.esc_js(__('This link will be used for the photo also if you select: same as title.', 'wppa'));
						$help .= '\n\n'.esc_js(__('If you specify New Tab on this line, all links from the cover will open a new tab,', 'wppa'));
						$help .= '\n'.esc_js(__('except when Ajax is activated on Table IV-A1.', 'wppa'));
						$slug1 = 'wppa_coverimg_linktype';
						$slug2 = 'wppa_coverimg_linkpage';
						$slug3 = 'wppa_coverimg_blank';
						$slug4 = 'wppa_coverimg_overrule';
						$slug = array($slug1, $slug2, $slug3, $slug4);
						$onchange = 'wppaCheckCoverImg()';
						$html1 = wppa_select($slug1, $options_linktype_cover_image, $values_linktype_cover_image, $onchange);
						$class = '';
						$html2 = '';
						$class = 'wppa_covimgbl';
						$html3 = wppa_checkbox($slug3, '', $class);
						$html4 = wppa_checkbox($slug4);
						$html = array($html1, $html2, $html3, $html4);
						wppa_setting($slug, '6a,b,c,d', $name, $desc, $html, $help);
						
						$name = __('CommentWidget', 'wppa');
						$desc = __('Comment widget photo link.', 'wppa');
						$help = esc_js(__('Select the type of link the comment widget photos point to.', 'wppa')); 
						$slug1 = 'wppa_comment_widget_linktype'; 
						$slug2 = 'wppa_comment_widget_linkpage';
						$slug3 = 'wppa_comment_blank';
						$slug4 = 'wppa_comment_overrule';
						$slug = array($slug1, $slug2, $slug3, $slug4);
						$onchange = 'wppaCheckCommentLink()';
						$html1 = wppa_select($slug1, $options_linktype, $values_linktype, $onchange);
						$class = 'wppa_cmlp';
						$html2 = wppa_select($slug2, $options_page, $values_page, '', $class, true);
						$class = 'wppa_cmlb';
						$html3 = wppa_checkbox($slug3, '', $class);
						$html4 = wppa_checkbox($slug4);
						$html = array($html1, $html2, $html3, $html4);
						wppa_setting($slug, '7a,b,c,d', $name, $desc, $html, $help);
						
						$name = __('Slideshow', 'wppa');
						$desc = __('Slideshow fullsize link', 'wppa');
						$help = esc_js(__('You can overrule lightbox but not big browse buttons with the photo specifc link.', 'wppa'));
						$slug1 = 'wppa_slideshow_linktype';
						$slug2 = '';
						$slug3 = 'wppa_slideshow_blank';
						$slug4 = 'wppa_slideshow_overrule';
						$slug = array($slug1, $slug2, $slug3, $slug4);
						$opts = array(__('no link at all.', 'wppa'), __('the plain photo (file).', 'wppa'), __('lightbox.', 'wppa'));
						$vals = array('none', 'file', 'lightbox'); 
						$onchange = 'wppaCheckSlideLink()';
						$html1 = wppa_select($slug1, $opts, $vals, $onchange);
						$html2 = '';
						$class = 'wppa_sslb';
						$html3 = wppa_checkbox($slug3, '', $class);
						$html4 = wppa_checkbox($slug4);
						$html = array($html1, $html2, $html3, $html4);
						wppa_setting($slug, '8a,,c,d', $name, $desc, $html, $help);
						
						$name = __('ThumbnailWidget', 'wppa');
						$desc = __('Thumbnail widget photo link.', 'wppa');
						$help = esc_js(__('Select the type of link the thumbnail photos point to.', 'wppa')); 
						$slug1 = 'wppa_thumbnail_widget_linktype'; 
						$slug2 = 'wppa_thumbnail_widget_linkpage';
						$slug3 = 'wppa_thumbnail_widget_blank';
						$slug4 = 'wppa_thumbnail_widget_overrule';
						$slug = array($slug1, $slug2, $slug3, $slug4);
						$onchange = 'wppaCheckThumbnailWLink()';
						$html1 = wppa_select($slug1, $options_linktype, $values_linktype, $onchange);
						$class = 'wppa_tnlp';
						$html2 = wppa_select($slug2, $options_page, $values_page, '', $class, true);
						$class = 'wppa_tnlb';
						$html3 = wppa_checkbox($slug3, '', $class);
						$html4 = wppa_checkbox($slug4);
						$html = array($html1, $html2, $html3, $html4);
						wppa_setting($slug, '9a,b,c,d', $name, $desc, $html, $help);
						
						$name = __('Film linktype', 'wppa');
						$desc = __('Direct access goto image in:', 'wppa');
						$help = esc_js(__('Select the action to be taken when the user clicks on a filmstrip image.', 'wppa'));
						$slug = 'wppa_film_linktype';
						$options = array(__('slideshow window', 'wppa'), __('lightbox overlay', 'wppa'));
						$values = array('slideshow', 'lightbox');
						$html = wppa_select($slug, $options, $values);
						wppa_setting($slug, '10', $name, $desc, $html.'<td></td><td></td><td></td>', $help);
						
						?>
					</tbody>
					<tfoot style="font-weight: bold;" class="wppa_table_6">
						<tr>
							<th scope="col"><?php _e('#', 'wppa') ?></th>
							<th scope="col"><?php _e('Name', 'wppa') ?></th>
							<th scope="col"><?php _e('Description', 'wppa') ?></th>
							<th scope="col"><?php _e('Link type', 'wppa') ?></th>
							<th scope="col"><?php _e('Link page', 'wppa') ?></th>
							<th scope="col"><?php _e('New tab', 'wppa') ?></th>
							<th scope="col" title="<?php _e('Photo specific link overrules', 'wppa') ?>" style="cursor: default"><?php _e('PSO', 'wppa') ?></th>
							<th scope="col"><?php _e('Help', 'wppa') ?></th>
						</tr>
					</tfoot>
				</table>
			</div>
					
			<?php // Table 7: Security ?>
			<h3><?php _e('Table VII:', 'wppa'); echo(' '); _e('Access and Security:', 'wppa'); ?><?php wppa_toggle_table(7) ?>
				<span style="font-weight:normal; font-size:12px;"><?php _e('This table describes the access settings for wppa+ elements and pages.', 'wppa'); ?></span>
			</h3>

			<div id="wppa_table_7" style="display:none" >
				<table class="widefat">
					<thead style="font-weight: bold; " class="wppa_table_7">
						<tr>
							<?php
								$wppacaps = array(	'wppa_admin', 
													'wppa_upload', 
													'wppa_import', 
													'wppa_export', 
													'wppa_settings', 
													'wppa_potd', 
													'wppa_comments', 
													'wppa_help'
													);
								$wppanames = array( 'Album Admin', 
													'Upload Photos', 
													'Import Photos', 
													'Export Photos', 
													'Settings', 
													'Photo of the day', 
													'Comments', 
													'Help & Info'
													);
								echo '<th scope="col">'.__('Role', 'wppa').'</th>';
								for ($i = 0; $i < count($wppacaps); $i++) echo '<th scope="col" style="width:11%;">'.$wppanames[$i].'</th>';
							?>
						</tr>
					</thead>
					<tbody class="wppa_table_7">
						<?php 
						$wppa_table = 'VII';
						wppa_setting_subheader('A', '5', __('Roles and Capability settings', 'wppa'));
						$roles = $wp_roles->roles;//get_option($wpdb->prefix . 'user_roles');
						foreach (array_keys($roles) as $key) {
							$role = $roles[$key];
							echo '<tr><td>'.$role['name'].'</td>';
							$caps = $role['capabilities'];
							for ($i = 0; $i < count($wppacaps); $i++) {
								if (isset($caps[$wppacaps[$i]])) {
									$yn = $caps[$wppacaps[$i]] ? true : false;
								}
								else $yn = false;
								$enabled = ( $key != 'administrator' );
								echo '<td>'.wppa_checkbox_e('caps-'.$wppacaps[$i].'-'.$key, $yn, '', '', $enabled).'</td>';
							};
							echo '</tr>';
						}
						?>
					</tbody>
				</table>
				<table class="widefat">
					<tbody class="wppa_table_7">
						<?php
						wppa_setting_subheader('B', '5', __('Miscellaneous scurity settings', 'wppa'));
					
						$name = __('User upload login', 'wppa');
						$desc = __('Users must be logged in to be able to upload.', 'wppa');
						$help = esc_js(__('If you uncheck this box, make sure you check the next 3 items.', 'wppa'));
						$help .= '\n'.esc_js(__('Set the owner to ---public--- of the albums that are allowed to be uploaded to.', 'wppa'));
						$slug = 'wppa_user_upload_login';
						$html = wppa_checkbox($slug);
						wppa_setting($slug, '0', $name, $desc, $html, $help);
						
						$name = __('Owners only', 'wppa');
						$desc = __('Limit album access to the album owners only.', 'wppa');
						$help = esc_js(__('If checked, users who can edit albums and/or upload/import photos can do that with their own albums and --- public --- albums only.', 'wppa')); 
						$help .= '\n'.esc_js(__('Users can give their albums to another user. Administrators can change ownership and access all albums always.', 'wppa'));
						$slug = 'wppa_owner_only';
						$html = wppa_checkbox($slug);
						wppa_setting($slug, '1', $name, $desc, $html, $help);
					
						$name = __('User upload', 'wppa');
						$desc = __('Enable visitors to upload photos.', 'wppa');
						$help = esc_js(__('If you check this item, visitors who are logged in and have wppa+ upload rights and have access to the album will see an upload photo link on album covers and thumbnail displays.', 'wppa'));
						$slug = 'wppa_user_upload_on';
						$onchange = 'wppaCheckUserUpload()';
						$html = wppa_checkbox($slug, $onchange);
						wppa_setting($slug, '2', $name, $desc, $html, $help);
						
						$name = __('Upload moderation', 'wppa');
						$desc = __('Uploaded photos need moderation.', 'wppa');
						$help = esc_js(__('If checked, photos uploaded by users who do not have photo album admin access rights need moderation.', 'wppa'));
						$help .= esc_js(__('Users who have photo album admin access rights can change the photo status to publish or featured.', 'wppa'));
						$help .= '\n\n'.esc_js(__('You can set the album admin access rights in Table VII-A.', 'wppa'));
						$slug = 'wppa_upload_moderate';
						$html = wppa_checkbox($slug);
						wppa_setting($slug, '3', $name, $desc, $html, $help);

						$name = __('Comment captcha', 'wppa');
						$desc = __('Use a simple calculate captcha on comments form.', 'wppa');
						$help = '';
						$slug = 'wppa_comment_captcha';
						$html = wppa_checkbox($slug);
						$class = 'wppa_comment_';
						wppa_setting($slug, '4', $name, $desc, $html, $help, $class);
						
						$name = __('Spam lifetime', 'wppa');
						$desc = __('Delete spam comments when older than.', 'wppa');
						$help = '';
						$slug = 'wppa_spam_maxage';
						$options = array(__('--- off ---', 'wppa'), __('10 minutes', 'wppa'), __('half an hour', 'wppa'), __('one hour', 'wppa'), __('one day', 'wppa'), __('one week', 'wppa'));
						$values = array('none', '600', '1800', '3600', '86400', '604800');
						$html = wppa_select($slug, $options, $values);
						$class = 'wppa_comment_';
						wppa_setting($slug, '5', $name, $desc, $html, $help, $class);
						
						?>
					</tbody>
					<tfoot style="font-weight: bold;" class="wppa_table_7">
						<tr>
							<th scope="col"><?php _e('#', 'wppa') ?></th>
							<th scope="col"><?php _e('Name', 'wppa') ?></th>
							<th scope="col"><?php _e('Description', 'wppa') ?></th>
							<th scope="col"><?php _e('Setting', 'wppa') ?></th>
							<th scope="col"><?php _e('Help', 'wppa') ?></th>
						</tr>
					</tfoot>
				</table>
			</div>

			<?php // Table 8: Actions ?>
			<h3><?php _e('Table VIII:', 'wppa'); echo(' '); _e('Actions:', 'wppa'); ?><?php wppa_toggle_table(8) ?>
				<span style="font-weight:normal; font-size:12px;"><?php _e('This table lists all actions that can be taken to the wppa+ system', 'wppa'); ?></span>
			</h3>
			
			<div id="wppa_table_8" style="display:none" >
				<table class="widefat">
					<thead style="font-weight: bold; " class="wppa_table_8">
						<tr>
							<th scope="col"><?php _e('#', 'wppa') ?></th>
							<th scope="col"><?php _e('Name', 'wppa') ?></th>
							<th scope="col"><?php _e('Description', 'wppa') ?></th>
							<th scope="col"><?php _e('Specification', 'wppa') ?></th>
							<th scope="col"><?php _e('Do it!', 'wppa') ?></th>
							<th scope="col"><?php _e('Help', 'wppa') ?></th>
						</tr>
					</thead>
					<tbody class="wppa_table_8">
						<?php 
						$wppa_table = 'VIII';
						
						wppa_setting_subheader('A', '2', __('Harmless and reverseable actions', 'wppa'));
						
						$name = __('Setup', 'wppa');
						$desc = __('Re-initialize plugin.', 'wppa');
						$help = esc_js(__('Re-initilizes the plugin, (re)creates database tables and sets up default settings and directories if required.', 'wppa'));
						$help .= '\n\n'.esc_js(__('This action may be required to setup blogs in a multiblog (network) site as well as in rare cases to correct initilization errors.', 'wppa'));
						$slug = 'wppa_setup';
						$html1 = '';
						$html2 = wppa_doit_button('', $slug);
						$html = array($html1, $html2);
						wppa_setting(false, '1', $name, $desc, $html, $help);

						$name = __('Backup settings', 'wppa');
						$desc = __('Save all settings into a backup file.', 'wppa');
						$help = esc_js(__('Saves all the settings into a backup file', 'wppa'));
						$slug = 'wppa_backup';
						$html1 = '';
						$html2 = wppa_doit_button('', $slug);
						$html = array($html1, $html2);
						wppa_setting(false, '2', $name, $desc, $html, $help);
						
						$name = __('Load settings', 'wppa');
						$desc = __('Restore all settings from defaults, a backup or skin file.', 'wppa');
						$help = esc_js(__('Restores all the settings from the factory supplied defaults, the backup you created or from a skin file.', 'wppa'));
						$slug1 = 'wppa_skinfile';
						$slug2 = 'wppa_load_skin';
						$files = glob(WPPA_PATH.'/theme/*.skin');
						
						$options = false;
						$values = false;
						$options[] = __('--- set to defaults ---', 'wppa');
						$values[] = 'default';
						if (is_file(WPPA_DEPOT_PATH.'/settings.bak')) {
							$options[] = __('--- restore backup ---', 'wppa');
							$values[] = 'restore';
						}
						if ( count($files) ) {
							foreach ($files as $file) {
								$fname = basename($file);
								$ext = strrchr($fname, '.');
								if ( $ext == '.skin' )  {
									$options[] = $fname;
									$values[] = $file;
								}
							}
						}
						$html1 = wppa_select($slug1, $options, $values);
						$html2 = wppa_doit_button('', $slug2);
						$html = array($html1, $html2);
						wppa_setting(false, '3', $name, $desc, $html, $help);

						$name = __('Regenerate', 'wppa');
						$desc = __('Regenerate all thumbnails.', 'wppa');
						$help = esc_js(__('Regenerate all thumbnails.', 'wppa'));
						$slug = 'wppa_regen';
						$html1 = '';
						$html2 = wppa_ajax_button('', $slug);
						$html = array($html1, $html2);
						wppa_setting(false, '4', $name, $desc, $html, $help);

						$name = __('Rerate', 'wppa');
						$desc = __('Recalculate ratings.', 'wppa');
						$help = esc_js(__('This function will recalculate all mean photo ratings from the ratings table.', 'wppa'));
						$help .= '\n'.esc_js(__('You may need this function after the re-import of previously exported photos', 'wppa'));
						$slug = 'wppa_rerate';
						$html1 = '';
						$html2 = wppa_ajax_button('', $slug);
						$html = array($html1, $html2);
						wppa_setting(false, '5', $name, $desc, $html, $help);

						$name = __('Cleanup', 'wppa');
						$desc = __('Fix and secure WPPA+ system consistency', 'wppa');
						$help = esc_js(__('This function will cleanup incomplete db entries and recover lost photos.', 'wppa'));
						$slug = 'wppa_cleanup';
						$html1 = '';
						$html2 = wppa_doit_button('', $slug);
						$html = array($html1, $html2);
						wppa_setting(false, '6', $name, $desc, $html, $help);
						
						$name = __('Recuperate', 'wppa');
						$desc = 'Recuperate IPTC and EXIF data from photos in WPPA+.';
						$help = esc_js(__('This action will attempt to find and register IPTC and EXIF data from photos in the WPPA+ system.', 'wppa'));
						$help .= '\n\n'.esc_js(__('WARNING: Photos that have been downzised during upload/import will have NO IPTC and/or EXIF data.', 'wppa'));
						$help .= '\n'.esc_js(__('If you want that data, you will have to re-import the original files. Use the update switch. You may resize them again.', 'wppa'));
						$slug = 'wppa_recup';
						$html1 = '';
						$html2 = wppa_ajax_button('', $slug);
						$html = array($html1, $html2);
						wppa_setting(false, '7', $name, $desc, $html, $help);
						
						wppa_setting_subheader('B', '2', __('Clearing and other irreverseable actions', 'wppa'));
						
						$name = __('Clear ratings', 'wppa');
						$desc = __('Reset all ratings.', 'wppa');
						$help = esc_js(__('WARNING: If checked, this will clear all ratings in the system!', 'wppa'));
						$slug = 'wppa_rating_clear';
						$html1 = '';
						$html2 = wppa_ajax_button('', $slug);
						$html = array($html1, $html2);
						wppa_setting(false, '1', $name, $desc, $html, $help);
						
						$name = __('Reset IPTC', 'wppa');
						$desc = __('Clear all IPTC data.', 'wppa');
						$help = esc_js(__('WARNING: If checked, this will clear all IPTC data in the system!', 'wppa'));
						$slug = 'wppa_iptc_clear';
						$html1 = '';
						$html2 = wppa_ajax_button('', $slug);
						$html = array($html1, $html2);
						wppa_setting(false, '2', $name, $desc, $html, $help);

						$name = __('Reset EXIF', 'wppa');
						$desc = __('Clear all EXIF data.', 'wppa');
						$help = esc_js(__('WARNING: If checked, this will clear all EXIF data in the system!', 'wppa'));
						$slug = 'wppa_exif_clear';
						$html1 = '';
						$html2 = wppa_ajax_button('', $slug);
						$html = array($html1, $html2);
						wppa_setting(false, '3', $name, $desc, $html, $help);

						?>
					</tbody>
					<tfoot style="font-weight: bold;" class="wppa_table_8">
						<tr>
							<th scope="col"><?php _e('#', 'wppa') ?></th>
							<th scope="col"><?php _e('Name', 'wppa') ?></th>
							<th scope="col"><?php _e('Description', 'wppa') ?></th>
							<th scope="col"><?php _e('Specification', 'wppa') ?></th>
							<th scope="col"><?php _e('Do it!', 'wppa') ?></th>
							<th scope="col"><?php _e('Help', 'wppa') ?></th>
						</tr>
					</tfoot>
				</table>
			</div>
				
			<?php // Table 9: Miscellaneous ?>
			<h3><?php _e('Table IX:', 'wppa'); echo(' '); _e('Miscellaneous:', 'wppa'); ?><?php wppa_toggle_table(9) ?>
				<span style="font-weight:normal; font-size:12px;"><?php _e('This table lists all settings that do not fit into an other table', 'wppa'); ?></span>
			</h3>
			
			<div id="wppa_table_9" style="display:none" >
				<table class="widefat">
					<thead style="font-weight: bold; " class="wppa_table_9">
						<tr>
							<th scope="col"><?php _e('#', 'wppa') ?></th>
							<th scope="col"><?php _e('Name', 'wppa') ?></th>
							<th scope="col"><?php _e('Description', 'wppa') ?></th>
							<th scope="col"><?php _e('Setting', 'wppa') ?></th>
							<th scope="col"><?php _e('Help', 'wppa') ?></th>
						</tr>
					</thead>
					<tbody class="wppa_table_9">
						<?php
						$wppa_table = 'IX';
						wppa_setting_subheader('A', '1', __('WPPA+ System related miscellaneous settings', 'wppa'));
						
						$name = __('Allow HTML', 'wppa');
						$desc = __('Allow HTML in album and photo descriptions.', 'wppa');
						$help = esc_js(__('If checked: html is allowed. WARNING: No checks on syntax, it is your own responsability to close tags properly!', 'wppa'));
						$slug = 'wppa_html';
						$html = wppa_checkbox($slug);
						wppa_setting($slug, '1', $name, $desc, $html, $help);

						$name = __('Check tag balance', 'wppa');
						$desc = __('Check if the HTML tags are properly closed: "balanced".', 'wppa');
						$help = esc_js(__('If the HTML tags in an album or a photo description are not in balance, the description is not updated, an errormessage is displayed', 'wppa'));
						$slug = 'wppa_check_balance';
						$html = wppa_checkbox($slug);
						wppa_setting($slug, '2', $name, $desc, $html, $help);
						
						$name = __('Allow WPPA+ Debugging', 'wppa');
						$desc = __('Allow the use of &debug=.. in urls to this site.', 'wppa');
						$help = esc_js(__('If checked: appending (?)(&)debug or (?)(&)debug=<int> to an url to this site will generate the display of special WPPA+ diagnostics, as well as php warnings', 'wppa'));
						$slug = 'wppa_allow_debug';
						$html = wppa_checkbox($slug);
						wppa_setting($slug, '3', $name, $desc, $html, $help);

						$name = __('Autoclean', 'wppa');
						$desc = __('Auto cleanup invalid database entries.', 'wppa');
						$help = esc_js(__('If checked, the database consistency will be automaticly secured after an interrupted upload or import procedure.', 'wppa'));
						$slug = 'wppa_autoclean';
						$html = wppa_checkbox($slug);
						wppa_setting($slug, '4', $name, $desc, $html, $help);

						$name = __('WPPA+ Filter priority', 'wppa');
						$desc = __('Sets the priority of the wppa+ content filter.', 'wppa');
						$help = esc_js(__('If you encounter conflicts with the theme or other plugins, increasing this value sometimes helps. Use with great care!', 'wppa'));
						$slug = 'wppa_filter_priority';
						$html = wppa_input($slug, '50px');
						wppa_setting($slug, '5', $name, $desc, $html, $help);
		
						$name = __('Lightbox keyname', 'wppa');
						$desc = __('The identifier of lightbox.', 'wppa');
						$help = esc_js(__('If you use a lightbox plugin that uses rel="lbox-id" you can enter the lbox-id here.', 'wppa'));
						$slug = 'wppa_lightbox_name';
						$class = 'wppa_alt_lightbox';
						$html = wppa_input($slug, '100px');
						wppa_setting($slug, '6', $name, $desc, $html, $help, $class);
						
						$name = __('Foreign shortcodes', 'wppa');
						$desc = __('Enable the use of non-wppa+ shortcodes in fullsize photo descriptions.', 'wppa');
						$help = esc_js(__('When checked, you can use shortcodes from other plugins in the description of photos.', 'wppa'));
						$help .= '\n\n'.esc_js(__('The shortcodes will be expanded in the descriptions of fullsize images.', 'wppa'));
						$help .= '\n'.esc_js(__('You will most likely need also to check Table IX-A1 (Allow HTML).', 'wppa'));
						$slug = 'wppa_allow_foreign_shortcodes';
						$html = wppa_checkbox($slug);
						wppa_setting($slug, '7', $name, $desc, $html, $help);
						
						$name = __('Arrow color', 'wppa');
						$desc = __('Left/right browsing arrow color.', 'wppa');
						$help = esc_js(__('Enter the color of the navigation arrows.', 'wppa'));
						$slug = 'wppa_arrow_color';
						$html = wppa_input($slug, '70px', '', '');
						wppa_setting($slug, '8', $name, $desc, $html, $help);

						wppa_setting_subheader('B', '1', __('New Album and New Photo related miscellaneous settings', 'wppa'));

						$name = __('New Album', 'wppa');
						$desc = __('Maximum time an album is indicated as New!', 'wppa');
						$help = '';
						$slug = 'wppa_max_album_newtime';
						$options = array( __('--- off ---', 'wppa'), __('One hour', 'wppa'), __('One day', 'wppa'), __('One week', 'wppa'), __('One month', 'wppa') );
						$values = array( 0, 60*60, 60*60*24, 60*60*24*7, 60*60*24*30);
						$html = wppa_select($slug, $options, $values);
						wppa_setting($slug, '1', $name, $desc, $html, $help);

						$name = __('New Photo', 'wppa');
						$desc = __('Maximum time a photo is indicated as New!', 'wppa');
						$help = '';
						$slug = 'wppa_max_photo_newtime';
						$options = array( __('--- off ---', 'wppa'), __('One hour', 'wppa'), __('One day', 'wppa'), __('One week', 'wppa'), __('One month', 'wppa') );
						$values = array( 0, 60*60, 60*60*24, 60*60*24*7, 60*60*24*30);
						$html = wppa_select($slug, $options, $values);
						wppa_setting($slug, '2', $name, $desc, $html, $help);
						
						$name = __('Apply Newphoto desc', 'wppa');
						$desc = __('Give each new photo a standard description.', 'wppa');
						$help = esc_js(__('If checked, each new photo will get the description (template) as specified in the next item.', 'wppa'));
						$slug = 'wppa_apply_newphoto_desc';
						$html = wppa_checkbox($slug);
						wppa_setting($slug, '3', $name, $desc, $html, $help);

						$name = __('New photo desc', 'wppa');
						$desc = __('The description (template) to add to a new photo.', 'wppa');
						$help = esc_js(__('Enter the default description.', 'wppa'));
						$help .= '\n\n'.esc_js(__('If you use html, please check item A-1 of this table.', 'wppa'));
						$slug = 'wppa_newphoto_description';
						$html = wppa_textarea($slug);
						wppa_setting($slug, '4', $name, $desc, $html, $help);
						
						wppa_setting_subheader('C', '1', __('Search Albums and Photos related settings', 'wppa'));
						
						$name = __('Search page', 'wppa');
						$desc = __('Display the search results on page.', 'wppa');
						$help = esc_js(__('Select the page to be used to display search results. The page MUST contain %%wppa%%.', 'wppa'));
						$help .= '\n'.esc_js(__('You may give it the title "Search results" or something alike.', 'wppa'));
						$help .= '\n'.esc_js(__('Or you ou may use the standard page on which you display the generic album.', 'wppa'));
						$slug = 'wppa_search_linkpage';
						$query = $wpdb->prepare("SELECT ID, post_title FROM " . $wpdb->posts . " WHERE post_type = 'page' AND post_status = 'publish' ORDER BY post_title ASC");
						$pages = $wpdb->get_results ($query, 'ARRAY_A');
						$options = false;
						$values = false;
						$options[] = __('--- Please select a page ---', 'wppa');
						$values[] = '0';
						if ($pages) {
							foreach ($pages as $page) {
								$options[] = __($page['post_title']);
								$values[] = $page['ID'];
							}
						}
						$html = wppa_select($slug, $options, $values, '', '', true);
						wppa_setting(false, '1', $name, $desc, $html, $help);
						
						$name = __('Exclude separate', 'wppa');
						$desc = __('Do not search \'separate\' albums.', 'wppa');
						$help = esc_js(__('When checked, albums (and photos in them) that have the parent set to --- separate --- will be excluded from being searched.', 'wppa'));
						$slug = 'wppa_excl_sep';
						$html = wppa_checkbox($slug);
						wppa_setting($slug, '2', $name, $desc, $html, $help);
						
						$name = __('Photos only', 'wppa');
						$desc = __('Search for photos only.', 'wppa');
						$help = esc_js(__('When checked, only photos will be searched for.', 'wppa'));
						$slug = 'wppa_photos_only';
						$html = wppa_checkbox($slug);
						wppa_setting($slug, '3', $name, $desc, $html, $help);
						
						wppa_setting_subheader('D', '1', __('Watermark related settings', 'wppa'));
						
						$name = __('Watermark', 'wppa');
						$desc = __('Enable the application of watermarks.', 'wppa');
						$help = esc_js(__('If checked, photos can be watermarked during upload / import.', 'wppa'));
						$slug = 'wppa_watermark_on';
						$onchange = 'wppaCheckWatermark()';
						$html = wppa_checkbox($slug, $onchange);
						wppa_setting($slug, '1', $name, $desc, $html, $help);
						
						$name = __('User Watermark', 'wppa');
						$desc = __('Uploading users may select watermark settings', 'wppa');
						$help = esc_js(__('If checked, anyone who can upload and/or import photos can overrule the default watermark settings.', 'wppa'));
						$slug = 'wppa_watermark_user';
						$class = 'wppa_watermark';
						$html = wppa_checkbox($slug);
						wppa_setting($slug, '2', $name, $desc, $html, $help, $class);
												
						$name = __('Watermark file', 'wppa');
						$desc = __('The default watermarkfile to be used.', 'wppa');
						$help = esc_js(__('Watermark files are of type png and reside in', 'wppa') . ' ' . WPPA_UPLOAD_URL . '/watermarks/');
						$help .= '\n\n'.esc_js(__('A suitable watermarkfile typically consists of a transparent background and a black text or drawing.', 'wppa'));
						$help .= '\n'.esc_js(__('The watermark image will be overlaying the photo with 80% transparency.', 'wppa'));
						$slug = 'wppa_watermark_file';
						$class = 'wppa_watermark';
						$html = '<select style="float:left; font-size:11px; height:20px; margin:0 20px 0 0; padding:0; " id="wppa_watermark_file" onchange="wppaAjaxUpdateOptionValue(\'wppa_watermark_file\', this)" >' . wppa_watermark_file_select('default') . '</select>';
						$html .= '<img id="img_wppa_watermark_file" src="'.wppa_get_imgdir().'star.png" title="'.__('Setting unmodified', 'wppa').'" style="padding-left:4px; float:left; height:16px; width:16px;" />';
						$html .= __('position:', 'wppa').'<select style="float:left; font-size:11px; height:20px; margin:0 0 0 20px; padding:0; "  id="wppa_watermark_pos" onchange="wppaAjaxUpdateOptionValue(\'wppa_watermark_pos\', this)" >' . wppa_watermark_pos_select('default') . '</select>';
						$html .= '<img id="img_wppa_watermark_pos" src="'.wppa_get_imgdir().'star.png" title="'.__('Setting unmodified', 'wppa').'" style="padding-left:4px; float:left; height:16px; width:16px;" />';
						wppa_setting(false, '3', $name, $desc, $html, $help, $class);
	
						$name = __('Upload watermark', 'wppa');
						$desc = __('Upload a new watermark file', 'wppa');
						// $help = ''; SAME AS PREVIOUS
						$slug = 'wppa_watermark_upload';
						$html = '<input id="my_file_element" type="file" name="file_1" style="float:left; height:18px; font-size: 11px;" />';
						$html .= wppa_doit_button(__('Upload it!', 'wppa'), $slug);
						wppa_setting(false, '4', $name, $desc, $html, $help, $class);
												
						$name = __('Watermark opacity', 'wppa');
						$desc = __('You can set the intensity of watermarks here.', 'wppa');
						$help = esc_js(__('The higher the number, the intenser the watermark. Value must be > 0 and <= 100.', 'wppa'));
						$slug = 'wppa_watermark_opacity';
						$html = wppa_input($slug, '50px', '', '%');
						wppa_setting($slug, '5', $name, $desc, $html, $help);

						wppa_setting_subheader('E', '1', __('Slideshow elements sequence order settings', 'wppa'));
						
						$indexopt = get_option('wppa_slide_order');
						$indexes  = explode(',', $indexopt);
						$names    = array(
							__('StartStop', 'wppa'), 
							__('SlideFrame', 'wppa'), 
							__('NameDesc', 'wppa'), 
							__('Custom', 'wppa'), 
							__('Rating', 'wppa'), 
							__('FilmStrip', 'wppa'), 
							__('Browsebar', 'wppa'), 
							__('Comments', 'wppa'),
							__('IPTC data', 'wppa'),
							__('EXIF data', 'wppa'));
						$enabled  = '<span style="color:green; float:right;">( '.__('Enabled', 'wppa');
						$disabled = '<span style="color:orange; float:right;">( '.__('Disabled', 'wppa');
						$descs = array(
							__('Start/Stop & Slower/Faster navigation bar', 'wppa') . ( $wppa_opt['wppa_show_startstop_navigation'] == 'yes' ? $enabled : $disabled ) . ' II-B1 )</span>',
							__('The Slide Frame', 'wppa') . '<span style="float:right;">'.__('( Always )', 'wppa').'</span>',
							__('Photo Name & Description Box', 'wppa') . ( ( $wppa_opt['wppa_show_full_name'] == 'yes' || $wppa_opt['wppa_show_full_desc'] == 'yes' ) ? $enabled : $disabled ) .' II-B5,6 )</span>',
							__('Custom Box', 'wppa') . ( $wppa_opt['wppa_custom_on'] == 'yes' ? $enabled : $disabled ).' II-B14 )</span>',
							__('Rating Bar', 'wppa') . ( $wppa_opt['wppa_rating_on'] == 'yes' ? $enabled : $disabled ).' II-B7 )</span>',
							__('Film Strip with embedded Start/Stop and Goto functionality', 'wppa') . ( $wppa_opt['wppa_filmstrip'] == 'yes' ? $enabled : $disabled ).' II-B3 )</span>',
							__('Browse Bar with Photo X of Y counter', 'wppa') . ( $wppa_opt['wppa_show_browse_navigation'] == 'yes' ? $enabled : $disabled ).' II-B2 )</span>',
							__('Comments Box', 'wppa') . ( $wppa_opt['wppa_show_comments'] == 'yes' ? $enabled : $disabled ).' II-B10 )</span>',
							__('IPTC box', 'wppa') . ( $wppa_opt['wppa_show_iptc'] == 'yes' ? $enabled : $disabled ).' II-B17 )</span>',
							__('EXIF box', 'wppa') . ( $wppa_opt['wppa_show_exif'] == 'yes' ? $enabled : $disabled ).' II-B18 )</span>'
							);
						$i = '0';
						while ( $i < '10' ) {
							$name = $names[$indexes[$i]];
							$desc = $descs[$indexes[$i]];
							$html = $i == '0' ? '' : wppa_doit_button(__('Move Up', 'wppa'), 'wppa_moveup', $i);
							$help = '';
							$slug = 'wppa_slide_order';
							wppa_setting($slug, $indexes[$i]+1 , $name, $desc, $html, $help);
							$i++;
						}
						
						$name = __('Swap Namedesc', 'wppa');
						$desc = __('Swap the order sequence of name and description', 'wppa');
						$help = '';
						$slug = 'wppa_swap_namedesc';
						$html = wppa_checkbox($slug);
						wppa_setting($slug, '11', $name, $desc, $html, $help);
						

						?>		

						<script type="text/javascript">wppa_moveup_url = "<?php echo wppa_dbg_url(get_admin_url().'admin.php?page=wppa_options&move_up=') ?>";</script>
						
					
		
					</tbody>
					<tfoot style="font-weight: bold;" class="wppa_table_9">
						<tr>
							<th scope="col"><?php _e('#', 'wppa') ?></th>
							<th scope="col"><?php _e('Name', 'wppa') ?></th>
							<th scope="col"><?php _e('Description', 'wppa') ?></th>
							<th scope="col"><?php _e('Setting', 'wppa') ?></th>
							<th scope="col"><?php _e('Help', 'wppa') ?></th>
						</tr>
					</tfoot>
				</table>
			</div>
		
			<?php // Table 10: IPTC Configuration ?>
			<h3><?php _e('Table X:', 'wppa'); echo(' '); _e('IPTC Configuration:', 'wppa'); ?><?php wppa_toggle_table(10) ?>
				<span style="font-weight:normal; font-size:12px;"><?php _e('This table defines the IPTC configuration', 'wppa'); ?></span>
			</h3>
			
			<div id="wppa_table_10" style="display:none" >
				<table class="widefat">
					<thead style="font-weight: bold; " class="wppa_table_10">
						<tr>
							<th scope="col"><?php _e('#', 'wppa') ?></th>
							<th scope="col"><?php _e('Tag', 'wppa') ?></th>
							<th scope="col"></th>
							<th scope="col"><?php _e('Description', 'wppa') ?></th>
							<th scope="col"><?php _e('Status', 'wppa') ?></th>
							<th scope="col"><?php _e('Help', 'wppa') ?></th>
						</tr>
					</thead>
					<tbody class="wppa_table_10">
						<?php
						$wppa_table = 'X';
						
						$labels = $wpdb->get_results($wpdb->prepare("SELECT * FROM `".WPPA_IPTC."` WHERE `photo`='0' ORDER BY `tag`"), 'ARRAY_A');
						if ( is_array( $labels ) ) {
							$i = '1';
							foreach ( $labels as $label ) {
								$name = $label['tag'];
								$desc = '';
								$help = '';
								$slug1 = 'wppa_iptc_label_'.$name;
								$slug2 = 'wppa_iptc_status_'.$name;
								$html1 = wppa_edit($slug1, $label['description']);
								$options = array(__('Display', 'wppa'), __('Hide', 'wppa'), __('Optional', 'wppa'));
								$values = array('display', 'hide', 'option');
								$html2 = wppa_select_e($slug2, $label['status'], $options, $values);
								$html = array($html1, $html2);
								wppa_setting(false, $i, $name, $desc, $html, $help);
								$i++;

							}
						}
						
						?>
					</tbody>
					<tfoot style="font-weight: bold;" class="wppa_table_10">
						<tr>
							<th scope="col"><?php _e('#', 'wppa') ?></th>
							<th scope="col"><?php _e('Tag', 'wppa') ?></th>
							<th scope="col"></th>
							<th scope="col"><?php _e('Description', 'wppa') ?></th>
							<th scope="col"><?php _e('Status', 'wppa') ?></th>
							<th scope="col"><?php _e('Help', 'wppa') ?></th>
						</tr>
					</tfoot>
				</table>
			</div>

			<?php // Table 11: EXIF Configuration ?>
			<h3><?php _e('Table XI:', 'wppa'); echo(' '); _e('EXIF Configuration:', 'wppa'); ?><?php wppa_toggle_table(11) ?>
				<span style="font-weight:normal; font-size:12px;"><?php _e('This table defines the EXIF configuration', 'wppa'); ?></span>
			</h3>
			
			<div id="wppa_table_11" style="display:none" >
				<table class="widefat">
					<thead style="font-weight: bold; " class="wppa_table_11">
						<tr>
							<th scope="col"><?php _e('#', 'wppa') ?></th>
							<th scope="col"><?php _e('Tag', 'wppa') ?></th>
							<th scope="col"></th>
							<th scope="col"><?php _e('Description', 'wppa') ?></th>
							<th scope="col"><?php _e('Status', 'wppa') ?></th>
							<th scope="col"><?php _e('Help', 'wppa') ?></th>
						</tr>
					</thead>
					<tbody class="wppa_table_11">
						<?php
						$wppa_table = 'XI';
						
						$labels = $wpdb->get_results($wpdb->prepare("SELECT * FROM `".WPPA_EXIF."` WHERE `photo`='0' ORDER BY `tag`"), 'ARRAY_A');
						if ( is_array( $labels ) ) {
							$i = '1';
							foreach ( $labels as $label ) {
								$name = $label['tag'];
								$desc = '';
								$help = '';
								$slug1 = 'wppa_exif_label_'.$name;
								$slug2 = 'wppa_exif_status_'.$name;
								$html1 = wppa_edit($slug1, $label['description']);
								$options = array(__('Display', 'wppa'), __('Hide', 'wppa'), __('Optional', 'wppa'));
								$values = array('display', 'hide', 'option');
								$html2 = wppa_select_e($slug2, $label['status'], $options, $values);
								$html = array($html1, $html2);
								wppa_setting(false, $i, $name, $desc, $html, $help);
								$i++;

							}
						}
						
						?>
					</tbody>
					<tfoot style="font-weight: bold;" class="wppa_table_11">
						<tr>
							<th scope="col"><?php _e('#', 'wppa') ?></th>
							<th scope="col"><?php _e('Tag', 'wppa') ?></th>
							<th scope="col"></th>
							<th scope="col"><?php _e('Description', 'wppa') ?></th>
							<th scope="col"><?php _e('Status', 'wppa') ?></th>
							<th scope="col"><?php _e('Help', 'wppa') ?></th>
						</tr>
					</tfoot>
				</table>
			</div>
			
			<?php // Table 12: Php configuration ?>
			<h3><?php _e('Table XII:', 'wppa'); echo(' '); _e('WPPA+ and PHP Configuration:', 'wppa'); ?><?php wppa_toggle_table(12) ?>
				<span style="font-weight:normal; font-size:12px;"><?php _e('This table lists all WPPA+ constants and PHP server configuration parameters and is read only', 'wppa'); ?></span>
			</h3>

			<div id="wppa_table_12" style="display:none" >
				<div class="wppa_table_12" style="margin-top:20px; text-align:left; ">
					<table class="widefat">
						<thead style="font-weight: bold; " class="wppa_table_12">
							<tr>
								<th scope="col"><?php _e('Name', 'wppa') ?></th>
								<th scope="col"><?php _e('Description', 'wppa') ?></th>
								<th scope="col"><?php _e('Value', 'wppa') ?></th>
							</tr>
						<tbody class="wppa_table_11">
							<tr style="color:#333;">
								<td>WPPA_ALBUMS</td>
								<td><small><?php _e('Albums db table name.', 'wppa') ?></small></td>
								<td><?php echo($wpdb->prefix . 'wppa_albums') ?></td>
							</tr>
							<tr style="color:#333;">
								<td>WPPA_PHOTOS</td>
								<td><small><?php _e('Photos db table name.', 'wppa') ?></small></td>
								<td><?php echo(WPPA_PHOTOS) ?></td>
							</tr>
							<tr style="color:#333;">
								<td>WPPA_RATING</td>
								<td><small><?php _e('Rating db table name.', 'wppa') ?></small></td>
								<td><?php echo(WPPA_RATING) ?></td>
							</tr>
							<tr style="color:#333;">
								<td>WPPA_COMMENTS</td>
								<td><small><?php _e('Comments db table name.', 'wppa') ?></small></td>
								<td><?php echo(WPPA_COMMENTS) ?></td>
							</tr>
							<tr style="color:#333;">
								<td>WPPA_IPTC</td>
								<td><small><?php _e('IPTC db table name.', 'wppa') ?></small></td>
								<td><?php echo(WPPA_IPTC) ?></td>
							</tr>
							<tr style="color:#333;">
								<td>WPPA_EXIF</td>
								<td><small><?php _e('EXIF db table name.', 'wppa') ?></small></td>
								<td><?php echo(WPPA_EXIF) ?></td>
							</tr>
							<tr style="color:#333;">
								<td>WPPA_FILE</td>
								<td><small><?php _e('Plugins main file name.', 'wppa') ?></small></td>
								<td><?php echo(WPPA_FILE) ?></td>
							</tr>
							<tr style="color:#333;">
								<td>WPPA_PATH</td>
								<td><small><?php _e('Path to plugins directory.', 'wppa') ?></small></td>
								<td><?php echo(WPPA_PATH) ?></td>
							</tr>
							<tr style="color:#333;">
								<td>WPPA_NAME</td>
								<td><small><?php _e('Plugins directory name.', 'wppa') ?></small></td>
								<td><?php echo(WPPA_NAME) ?></td>
							</tr>
							<tr style="color:#333;">
								<td>WPPA_URL</td>
								<td><small><?php _e('Plugins directory url.', 'wppa') ?></small></td>
								<td><?php echo(WPPA_URL) ?></td>
							</tr>
							<tr style="color:#333;">
								<td>WPPA_UPLOAD</td>
								<td><small><?php _e('The relative upload directory.', 'wppa') ?></small></td>
								<td><?php echo(WPPA_UPLOAD) ?></td>
							</tr>
							<tr style="color:#333;">
								<td>WPPA_UPLOAD_PATH</td>
								<td><small><?php _e('The upload directory path.', 'wppa') ?></small></td>
								<td><?php echo(WPPA_UPLOAD_PATH) ?></td>
							</tr>
							<tr style="color:#333;">
								<td>WPPA_UPLOAD_URL</td>
								<td><small><?php _e('The upload directory url.', 'wppa') ?></small></td>
								<td><?php echo(WPPA_UPLOAD_URL) ?></td>
							</tr>
							<tr style="color:#333;">
								<td>WPPA_DEPOT</td>
								<td><small><?php _e('The relative depot directory.', 'wppa') ?></small></td>
								<td><?php echo(WPPA_DEPOT) ?></td>
							</tr>
							<tr style="color:#333;">
								<td>WPPA_DEPOT_PATH</td>
								<td><small><?php _e('The depot directory path.', 'wppa') ?></small></td>
								<td><?php echo(WPPA_DEPOT_PATH) ?></td>
							</tr>
							<tr style="color:#333;">
								<td>WPPA_DEPOT_URL</td>
								<td><small><?php _e('The depot directory url.', 'wppa') ?></small></td>
								<td><?php echo(WPPA_DEPOT_URL) ?></td>
							</tr>
						</tbody>
					</table>
					<?php if ( $wppa_opt['wppa_allow_debug'] == 'yes' ) phpinfo(-1); else phpinfo(4); ?>
				</div>
			</div>
		</form>
		<script type="text/javascript">wppaInitSettings();wppaCheckInconsistencies();</script>
	</div>
	
<?php
}

function wppa_setting_subheader($lbl, $col, $txt, $cls = '') {
global $wppa_subtable;

	$wppa_subtable = $lbl;
	$colspan = $col + 3;
	echo '<tr class="'.$cls.'" style="background-color:#f0f0f0;" ><td style="color:#333;"><b>'.$lbl.'</b></td><td colspan="'.$colspan.'" style="color:#333;" ><em><b>'.$txt.'</b></em></td></tr>';
}


function wppa_setting($slug, $num, $name, $desc, $html, $help, $cls = '') {
global $wppa_status;
global $wppa_defaults;
global $wppa_table;
global $wppa_subtable;

	if ( is_array($slug) ) $slugs = $slug;
	else {
		$slugs = false;
		if ( $slug ) $slugs[] = $slug;
	}
	if ( is_array($html) ) $htmls = $html;
	else {
		$htmls = false;
		if ( $html ) $htmls[] = $html;
	}
	if ( strpos($num, ',') !== false ) {
		$nums = explode(',', $num);
		$nums[0] = substr($nums[0], 1);
	}
	else {
		$nums = false;
		if ( $num ) $nums[] = $num;
	}

	$result = "\n";
	$result .= '<tr id="'.$wppa_table.$wppa_subtable.$num.'" class="'.$cls.'" style="color:#333;">';
	$result .= '<td>'.$num.'</td>';
	$result .= '<td>'.$name.'</td>';
	$result .= '<td><small>'.$desc.'</small></td>';
	if ( $htmls ) foreach ( $htmls as $html ) {
		$result .= '<td>'.$html.'</td>';
	}
	
	if ( $help ) {
		$hlp = $name.':\n\n'.$help;
		if ( $slugs ) {
			$hlp .= '\n\n'.__('The default for this setting is:', 'wppa');
			if ( count($slugs) == 1) {
				if ( $slugs[0] != '' ) $hlp .= ' '.esc_js(wppa_dflt($slugs[0]));
			}
			else foreach ( array_keys($slugs) as $slugidx ) {
				if ( $slugs[$slugidx] != '' && isset($nums[$slugidx]) ) $hlp .= ' '.$nums[$slugidx].'. '.esc_js(wppa_dflt($slugs[$slugidx]));
			}
		}
	}
	else $hlp = __('No help available', 'wppa');

	$color = 'black';
	$char = '?';
	$fw = 'bold'; 
	$title = __('Click for help', 'wppa');
	$result .= '<td><input type="button" style="font-size: 11px; margin: 0px; padding: 0px; color: '.$color.';text-decoration: none; font-weight: '.$fw.'; cursor: pointer;" title="'.$title.'" onclick="alert('."'".$hlp."'".')" value="'.$char.'"></td>';
	$result .= '</tr>';
	
	echo $result;	

}

function wppa_input($slug, $width, $minwidth = '', $text = '', $onchange = '') {

	$html = '<input style="float:left; width: '.$width.';';
	if ($minwidth != '') $html .= ' min-width:'.$minwidth.';';
	$html .= ' font-size: 11px; margin: 0px; padding: 0px;" type="text" id="'.$slug.'"';
	if ($onchange != '') $html .= ' onchange="'.$onchange.';wppaAjaxUpdateOptionValue(\''.$slug.'\', this)"';
	else $html .= ' onchange="wppaAjaxUpdateOptionValue(\''.$slug.'\', this)"';
	$html .= ' value="'.esc_attr(get_option($slug)).'" />';	// changed stripslashes into esc_attr
	$html .= '<img id="img_'.$slug.'" src="'.wppa_get_imgdir().'star.png" title="'.__('Setting unmodified', 'wppa').'" style="padding:0 4px; float:left; height:16px; width:16px;" />';
	$html .= '<span style="float:left">'.$text.'</span>';
	
	return $html;
}

function wppa_edit($slug, $value, $width = '90%', $minwidth = '', $text = '', $onchange = '') {

	$html = '<input style="float:left; width: '.$width.';';
	if ($minwidth != '') $html .= ' min-width:'.$minwidth.';';
	$html .= ' font-size: 11px; margin: 0px; padding: 0px;" type="text" id="'.$slug.'"';
	if ($onchange != '') $html .= ' onchange="'.$onchange.';wppaAjaxUpdateOptionValue(\''.$slug.'\', this)"';
	else $html .= ' onchange="wppaAjaxUpdateOptionValue(\''.$slug.'\', this)"';
	$html .= ' value="'.esc_attr($value).'" />';
	$html .= '<img id="img_'.$slug.'" src="'.wppa_get_imgdir().'star.png" title="'.__('Setting unmodified', 'wppa').'" style="padding:0 4px; float:left; height:16px; width:16px;" />';
	$html .= $text;
	
	return $html;

}

function wppa_textarea($slug) {
	$html = '<textarea id="'.$slug.'" style="float:left; width:500px;" onchange="wppaAjaxUpdateOptionValue(\''.$slug.'\', this)" >';
	$html .= esc_textarea(stripslashes(get_option($slug))); //htmlspecialchars(stripslashes(get_option($slug)));
	$html .= '</textarea>';
	$html .= '<img id="img_'.$slug.'" src="'.wppa_get_imgdir().'star.png" title="'.__('Setting unmodified', 'wppa').'" style="padding:0 4px; float:left; height:16px; width:16px;" />';
	
	return $html;
}

function wppa_checkbox($slug, $onchange = '', $class = '') {

	$html = '<input style="float:left; height: 15px; margin: 0px; padding: 0px;" type="checkbox" id="'.$slug.'"'; 
	if (get_option($slug) == 'yes') $html .= ' checked="checked"';
	if ($onchange != '') $html .= ' onchange="'.$onchange.';wppaAjaxUpdateOptionCheckBox(\''.$slug.'\', this)"';
	else $html .= ' onchange="wppaAjaxUpdateOptionCheckBox(\''.$slug.'\', this)"';

	if ($class != '') $html .= ' class="'.$class.'"';
	$html .= ' /><img id="img_'.$slug.'" src="'.wppa_get_imgdir().'star.png" title="'.__('Setting unmodified', 'wppa').'" style="padding-left:4px; float:left; height:16px; width:16px;"';
	if ($class != '') $html .= ' class="'.$class.'"';
	$html .= ' />';
	
	return $html;
}

function wppa_checkbox_e($slug, $curval, $onchange = '', $class = '', $enabled = true) {

	$html = '<input style="float:left; height: 15px; margin: 0px; padding: 0px;" type="checkbox" id="'.$slug.'"'; 
	if ($curval) $html .= ' checked="checked"';
	if ( ! $enabled ) $html .= ' disabled="disabled"';
	if ($onchange != '') $html .= ' onchange="'.$onchange.';wppaAjaxUpdateOptionCheckBox(\''.$slug.'\', this)"';
	else $html .= ' onchange="wppaAjaxUpdateOptionCheckBox(\''.$slug.'\', this)"';

	if ($class != '') $html .= ' class="'.$class.'"';
	$html .= ' /><img id="img_'.$slug.'" src="'.wppa_get_imgdir().'star.png" title="'.__('Setting unmodified', 'wppa').'" style="padding-left:4px; float:left; height:16px; width:16px;"';
	if ($class != '') $html .= ' class="'.$class.'"';
	$html .= ' />';
	
	return $html;
}

function wppa_select($slug, $options, $values, $onchange = '', $class = '', $first_disable = false) {

	if (!is_array($options)) {
		$html = __('There are no pages (yet) to link to.', 'wppa');
		return $html;
	}
	
	$html = '<select style="float:left; font-size: 11px; height: 20px; margin: 0px; padding: 0px;" id="'.$slug.'"';
	if ($onchange != '') $html .= ' onchange="'.$onchange.';wppaAjaxUpdateOptionValue(\''.$slug.'\', this)"';
	else $html .= ' onchange="wppaAjaxUpdateOptionValue(\''.$slug.'\', this)"';

	if ($class != '') $html .= ' class="'.$class.'"';
	$html .= '>';
	
	$val = get_option($slug);
	$idx = 0;
	$cnt = count($options);
	while ($idx < $cnt) {
		$html .= "\n";
		$html .= '<option value="'.$values[$idx].'" '; 
		if ($val == $values[$idx]) $html .= ' selected="selected"'; 
		if ($idx == 0 && $first_disable) $html .= ' disabled="disabled"';
		$html .= '>'.$options[$idx].'</option>';
		$idx++;
	}
	$html .= '</select>';
	$html .= '<img id="img_'.$slug.'" class="'.$class.'" src="'.wppa_get_imgdir().'star.png" title="'.__('Setting unmodified', 'wppa').'" style="padding-left:4px; float:left; height:16px; width:16px;" />';

	return $html;
}

function wppa_select_e($slug, $curval, $options, $values, $onchange = '', $class = '') {

	if (!is_array($options)) {
		$html = __('There are no pages (yet) to link to.', 'wppa');
		return $html;
	}
	
	$html = '<select style="float:left; font-size: 11px; height: 20px; margin: 0px; padding: 0px;" id="'.$slug.'"';
	if ($onchange != '') $html .= ' onchange="'.$onchange.';wppaAjaxUpdateOptionValue(\''.$slug.'\', this)"';
	else $html .= ' onchange="wppaAjaxUpdateOptionValue(\''.$slug.'\', this)"';

	if ($class != '') $html .= ' class="'.$class.'"';
	$html .= '>';
	
	$val = $curval; // get_option($slug);
	$idx = 0;
	$cnt = count($options);
	while ($idx < $cnt) {
		$html .= "\n";
		$html .= '<option value="'.$values[$idx].'" '; 
		if ($val == $values[$idx]) $html .= ' selected="selected"'; 
		$html .= '>'.$options[$idx].'</option>';
		$idx++;
	}
	$html .= '</select>';
	$html .= '<img id="img_'.$slug.'" class="'.$class.'" src="'.wppa_get_imgdir().'star.png" title="'.__('Setting unmodified', 'wppa').'" style="padding-left:4px; float:left; height:16px; width:16px;" />';

	return $html;
}

function wppa_dflt($slug) {
global $wppa_defaults;
global $wppa;

	if ($slug == '') return '';
	
	$dflt = $wppa_defaults[$slug];

	$dft = $dflt;
	switch ($dflt) {
		case 'yes': 	$dft .= ': '.__('Checked', 'wppa'); break;
		case 'no': 		$dft .= ': '.__('Unchecked', 'wppa'); break;
		case 'none': 	$dft .= ': '.__('no link at all.', 'wppa'); break;
		case 'file': 	$dft .= ': '.__('the plain photo (file).', 'wppa'); break;
		case 'photo': 	$dft .= ': '.__('the full size photo in a slideshow.', 'wppa'); break;
		case 'single': 	$dft .= ': '.__('the fullsize photo on its own.', 'wppa'); break;
		case 'indiv': 	$dft .= ': '.__('the photo specific link.', 'wppa'); break;
		case 'album': 	$dft .= ': '.__('the content of the album.', 'wppa'); break;
		case 'widget': 	$dft .= ': '.__('defined at widget activation.', 'wppa'); break;
		case 'custom': 	$dft .= ': '.__('defined on widget admin page.', 'wppa'); break;
		case 'same': 	$dft .= ': '.__('same as title.', 'wppa'); break;
		default:
	}

	return $dft;
}

function wppa_color_box($slug) {
global $wppa_opt;

	return '<div id="colorbox-' . $slug . '" style="width:100px; height:16px; background-color:' . $wppa_opt[$slug] . '; border:1px solid #dfdfdf;" ></div>';

}

function wppa_toggle_table($i) {
?>
	<input type="button" style="border-radius:10px; " value="<?php _e('Hide', 'wppa') ?>" onclick="wppaHideTable('<?php echo($i) ?>');" id="wppa_tableHide-<?php echo($i) ?>" />
	<input type="button" style="border-radius:10px; " value="<?php _e('Show', 'wppa') ?>" onclick="wppaShowTable('<?php echo($i) ?>');" id="wppa_tableShow-<?php echo($i) ?>" />
<?php
}

function wppa_doit_button( $label = '', $key = '', $sub = '' ) {
	if ( $label == '' ) $label = __('Do it!', 'wppa');

	$result = '<input type="submit" class="button-primary" style="float:left; font-size: 11px; height: 16px; margin: 0 4px; padding: 0px;"';
	$result .= ' name="wppa_settings_submit" value="'.$label.'"';
	$result .= ' onclick="';
	if ( $key ) $result .= 'document.getElementById(\'wppa-key\').value=\''.$key.'\';';
	if ( $sub ) $result .= 'document.getElementById(\'wppa-sub\').value=\''.$sub.'\';';
	$result .= 'if ( confirm(\''.__('Are you sure?', 'wppa').'\')) return true; else return false;" />';
	
	return $result;
}

function wppa_ajax_button( $label = '', $slug ) {
	if ( $label == '' ) $label = __('Do it!', 'wppa');

	$result = '<input type="button" class="button-secundary" style="float:left; border-radius:8px; font-size: 12px; height: 16px; margin: 0 4px; padding: 0px;" value="'.$label.'"';
	
	$result .= ' onclick="if (confirm(\''.__('Are you sure?', 'wppa').'\')) wppaAjaxUpdateOptionValue(\''.$slug.'\', 0)" />';
	$result .= '<img id="img_'.$slug.'" src="'.wppa_get_imgdir().'star.png" title="'.__('Not done yet', 'wppa').'" style="padding:0 4px; float:left; height:16px; width:16px;" />';
	
	return $result;
}
