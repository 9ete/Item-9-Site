<?php
/* wppa-topten-widget.php
* Package: wp-photo-album-plus
*
* display the top rated photos
* Version 4.5.5
*/

class TopTenWidget extends WP_Widget {
    /** constructor */
    function TopTenWidget() {
        parent::WP_Widget(false, $name = 'Top Ten Photos');	
		$widget_ops = array('classname' => 'wppa_topten_widget', 'description' => __( 'WPPA+ Top Ten Rated Photos', 'wppa') );
		$this->WP_Widget('wppa_topten_widget', __('Top Ten Photos', 'wppa'), $widget_ops);
    }

	/** @see WP_Widget::widget */
    function widget($args, $instance) {		
		global $wpdb;
		global $wppa_opt;
		global $wppa;

        extract( $args );
		
 		$widget_title = apply_filters('widget_title', empty( $instance['title'] ) ? __a('Top Ten Photos', 'wppa_theme') : $instance['title']);

		$instance = wp_parse_args( (array) $instance, array( 'sortby' => 'mean_rating', 'title' => '', 'album' => '' ) );

		$page = $wppa_opt['wppa_topten_widget_linkpage'];
		$max  = $wppa_opt['wppa_topten_count'];
		
		$album = $instance['album'];
		
		if ($album) {
			$thumbs = $wpdb->get_results($wpdb->prepare( 'SELECT * FROM '.WPPA_PHOTOS.' WHERE mean_rating > 0 AND album = %s ORDER BY '.$instance['sortby'].' DESC LIMIT '.$max, $album ), 'ARRAY_A' );
		}
		else {
			$thumbs = $wpdb->get_results($wpdb->prepare( 'SELECT * FROM '.WPPA_PHOTOS.' WHERE mean_rating > 0 ORDER BY '.$instance['sortby'].' DESC LIMIT '.$max ), 'ARRAY_A' );
		}
		$widget_content = "\n".'<!-- WPPA+ TopTen Widget start -->';
		$maxw = $wppa_opt['wppa_topten_size'];
		$maxh = $maxw + 18;
		if ($thumbs) foreach ($thumbs as $image) {
			
			// Make the HTML for current picture
			$widget_content .= "\n".'<div class="wppa-widget" style="width:'.$maxw.'px; height:'.$maxh.'px; margin:4px; display:inline; text-align:center; float:left;">'; 
			if ($image) {
				$no_album = !$album;
				if ($no_album) $tit = __a('View the top rated photos', 'wppa_theme'); else $tit = esc_attr(wppa_qtrans(stripslashes($image['description'])));
				$link       = wppa_get_imglnk_a('topten', $image['id'], '', $tit, '', $no_album);
				$file       = wppa_get_thumb_path_by_id($image['id']);
				$imgstyle_a = wppa_get_imgstyle_a($file, $maxw, 'center', 'ttthumb');
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
						$widget_content .= "\n\t".'<a href="'.$link['url'].'" rel="'.$wppa_opt['wppa_lightbox_name'].'[topten-'.$album.']" title="'.$title.'" target="'.$link['target'].'" >';
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
			$widget_content .= "\n\t".'<span style="font-size:9px;">'.wppa_get_rating_by_id($image['id']);
				if ( $wppa_opt['wppa_show_rating_count'] ) $widget_content .= ' ('.wppa_get_rating_count_by_id($image['id']).')';
			$widget_content .= '</span>'."\n".'</div>';
		}	
		else $widget_content .= 'There are no rated photos (yet).';
		
		$widget_content .= "\n".'<!-- WPPA+ TopTen Widget end -->';

		echo "\n".$before_widget.$before_title.$widget_title.$after_title.$widget_content.$after_widget;
    }
	
    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {				
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['album'] = $new_instance['album'];
		$instance['sortby'] = $new_instance['sortby'];
		
        return $instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {	
		global $wppa_opt;
		//Defaults
		$instance = wp_parse_args( (array) $instance, array( 'sortby' => 'mean_rating', 'title' => '', 'album' => '0') );
 		$widget_title = apply_filters('widget_title', empty( $instance['title'] ) ? $wppa_opt['wppa_toptenwidgettitle'] : $instance['title']);

		$album = $instance['album'];
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'wppa'); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $widget_title; ?>" />
		</p>
		<p><label for="<?php echo $this->get_field_id('album'); ?>"><?php _e('Album:', 'wppa'); ?></label> 
			<select class="widefat" id="<?php echo $this->get_field_id('album'); ?>" name="<?php echo $this->get_field_name('album'); ?>" >

				<?php echo wppa_album_select('', $album, true, '', '', true); ?>

			</select>
		</p>
		<p><label for="<?php echo $this->get_field_id('sortby'); ?>"><?php _e('Sort by:', 'wppa'); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id('sortby'); ?>" name="<?php echo $this->get_field_name('sortby'); ?>" >
				<option value="mean_rating" <?php if ($instance['sortby'] == 'mean_rating') echo 'selected="selected"' ?>><?php _e('Mean value', 'wppa') ?></option>
				<option value="rating_count" <?php if ($instance['sortby'] == 'rating_count') echo 'selected="selected"' ?>><?php _e('Number of votes', 'wppa') ?></option>
			</select>
		</p>

		<p><?php _e('You can set the behaviour of this widget in the <b>Photo Albums -> Settings</b> admin page.', 'wppa'); ?></p>
<?php
    }

} // class TopTenWidget

// register TopTenWidget widget
if (get_option('wppa_rating_on', 'yes') == 'yes') add_action('widgets_init', create_function('', 'return register_widget("TopTenWidget");'));
