<?php
/* wppa-thumbnail-widget.php
* Package: wp-photo-album-plus
*
* display thumbnail photos
* Version 4.5.0
*/

class ThumbnailWidget extends WP_Widget {
    /** constructor */
    function ThumbnailWidget() {
        parent::WP_Widget(false, $name = 'Thumbnail Photos');	
		$widget_ops = array('classname' => 'wppa_thumbnail_widget', 'description' => __( 'WPPA+ Thumbnails', 'wppa') );
		$this->WP_Widget('wppa_thumbnail_widget', __('Thumbnail Photos', 'wppa'), $widget_ops);
    }

	/** @see WP_Widget::widget */
    function widget($args, $instance) {		
	//	global $widget_content;
		global $wpdb;
		global $wppa_opt;
		global $wppa;

        extract( $args );
		
 		$widget_title = apply_filters('widget_title', empty( $instance['title'] ) ? __a('Thumbnail Photos', 'wppa_theme') : $instance['title']);

		$instance = wp_parse_args( (array) $instance, array( 
													'title' => '',
													'album' => 'no',
													'name' => 'no'
													) );

		$page = $wppa_opt['wppa_thumbnail_widget_linkpage'];
		$max  = $wppa_opt['wppa_thumbnail_widget_count'];
		
		$album = $instance['album'];
		$name = $instance['name'];
		
		if ($album) {
			$thumbs = $wpdb->get_results($wpdb->prepare( 'SELECT * FROM `'.WPPA_PHOTOS.'` WHERE `status` <> %s AND `album` = %s '.wppa_get_photo_order($album).' LIMIT '.$max, 'pending', $album ), 'ARRAY_A' );
		}
		else {
			$thumbs = $wpdb->get_results($wpdb->prepare( 'SELECT * FROM '.WPPA_PHOTOS.' WHERE `status` <> %s '.wppa_get_photo_order('0').' LIMIT '.$max, 'pending' ), 'ARRAY_A' );
		}
		$widget_content = "\n".'<!-- WPPA+ thumbnail Widget start -->';
		$maxw = $wppa_opt['wppa_thumbnail_widget_size'];
		$maxh = $maxw;
		if ( $name == 'yes' ) $maxh += 18;
		
		if ($thumbs) foreach ($thumbs as $image) {
			
			// Make the HTML for current picture
			$widget_content .= "\n".'<div class="wppa-widget" style="width:'.$maxw.'px; height:'.$maxh.'px; margin:4px; display:inline; text-align:center; float:left;">'; 
			if ($image) {
				$link       = wppa_get_imglnk_a('tnwidget', $image['id']);
				$file       = wppa_get_thumb_path_by_id($image['id']);
				$imgstyle_a = wppa_get_imgstyle_a($file, $maxw, 'center', 'twthumb');
				$imgstyle   = $imgstyle_a['style'];
				$width      = $imgstyle_a['width'];
				$height     = $imgstyle_a['height'];
				$usethumb	= wppa_use_thumb_file($image['id'], $width, $height) ? '/thumbs' : '';
				$imgurl 	= WPPA_UPLOAD_URL . $usethumb . '/' . $image['id'] . '.' . $image['ext'];

				$imgevents = wppa_get_imgevents('thumb', $image['id'], true);

				if ($link) $title = esc_attr(stripslashes($link['title']));
				else $title = '';
				
				if ($link) {
					if ( $link['is_url'] ) {	// Is a href
						$widget_content .= "\n\t".'<a href="'.$link['url'].'" title="'.$title.'" target="'.$link['target'].'" >';
							$widget_content .= "\n\t\t".'<img id="i-'.$image['id'].'-'.$wppa['master_occur'].'" title="'.$title.'" src="'.$imgurl.'" width="'.$width.'" height="'.$height.'" style="'.$imgstyle.'" '.$imgevents.' alt="'.esc_attr(wppa_qtrans($image['name'])).'">';
						$widget_content .= "\n\t".'</a>';
					}
					elseif ( $link['is_lightbox'] ) {
						$widget_content .= "\n\t".'<a href="'.$link['url'].'" rel="'.$wppa_opt['wppa_lightbox_name'].'[thumbnail-'.$album.']" title="'.$title.'" target="'.$link['target'].'" >';
							$widget_content .= "\n\t\t".'<img id="i-'.$image['id'].'-'.$wppa['master_occur'].'" title="'.$title.'" src="'.$imgurl.'" width="'.$width.'" height="'.$height.'" style="'.$imgstyle.'" '.$imgevents.' alt="'.esc_attr(wppa_qtrans($image['name'])).'">';
						$widget_content .= "\n\t".'</a>';
					}
					else { // Is an onclick unit
						$widget_content .= "\n\t".'<img id="i-'.$image['id'].'-'.$wppa['master_occur'].'" title="'.$title.'" src="'.$imgurl.'" width="'.$width.'" height="'.$height.'" style="'.$imgstyle.'" '.$imgevents.' onclick="'.$link['url'].'" alt="'.esc_attr(wppa_qtrans($image['name'])).'">';					
					}
				}
				else {
					$widget_content .= "\n\t".'<img id="i-'.$image['id'].'-'.$wppa['master_occur'].'" title="'.$title.'" src="'.$imgurl.'" width="'.$width.'" height="'.$height.'" style="'.$imgstyle.'" '.$imgevents.' alt="'.esc_attr(wppa_qtrans($image['name'])).'">';
				}
			}
			else {	// No image
				$widget_content .= __a('Photo not found.', 'wppa_theme');
			}
			if ($name == 'yes') $widget_content .= "\n\t".'<span style="font-size:9px;">'.__(stripslashes($image['name'])).'</span>';

			$widget_content .= "\n".'</div>';
		}	
		else $widget_content .= 'There are no photos (yet).';
		
		$widget_content .= "\n".'<!-- WPPA+ thumbnail Widget end -->';

		echo "\n".$before_widget.$before_title.$widget_title.$after_title.$widget_content.$after_widget;
    }
	
    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {				
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['album'] = $new_instance['album'];
		$instance['name'] = $new_instance['name'];
		
        return $instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {	
		global $wppa_opt;
		//Defaults
		$instance = wp_parse_args( (array) $instance, array( 
															'sortby' => 'post_title', 
															'title' => '', 
															'album' => '0',
															'name' => 'no') );
 		$widget_title = apply_filters('widget_title', empty( $instance['title'] ) ? $wppa_opt['wppa_thumbnailwidgettitle'] : $instance['title']);

		$album = $instance['album'];
		$name = $instance['name'];
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'wppa'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $widget_title; ?>" /></p>
		<p><label for="<?php echo $this->get_field_id('album'); ?>"><?php _e('Album:', 'wppa'); ?></label> 
			<select class="widefat" id="<?php echo $this->get_field_id('album'); ?>" name="<?php echo $this->get_field_name('album'); ?>" >

				<?php echo wppa_album_select('', $album, true, '', '', true); ?>

			</select>
		</p>
		<p>
			<?php _e('Show photo names:', 'wppa'); ?>
			<select id="<?php echo $this->get_field_id('name'); ?>" name="<?php echo $this->get_field_name('name'); ?>">
				<option value="no" <?php if ($name == 'no') echo 'selected="selected"' ?>><?php _e('no.', 'wppa'); ?></option>
				<option value="yes" <?php if ($name == 'yes') echo 'selected="selected"' ?>><?php _e('yes.', 'wppa'); ?></option>
			</select>
		</p>

		<p><?php _e('You can set the behaviour of this widget in the <b>Photo Albums -> Settings</b> admin page.', 'wppa'); ?></p>
<?php
    }

} // class thumbnailWidget

// register thumbnailWidget widget
add_action('widgets_init', create_function('', 'return register_widget("ThumbnailWidget");'));
