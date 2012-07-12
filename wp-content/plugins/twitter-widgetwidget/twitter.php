<?php
/*
Plugin Name: Twitter Widget...Widget
Plugin URI: http://www.tchew.biz/plugins
Description: The Twitter widget...widget plugin allows you to place a Twitter Profile widget in the sidebar. You can configure the Twitter user, size and color's.
Author: Matt Hammond
Version: 1.1
Author URI: http://www.tchew.biz
*/

add_action('widgets_init', create_function('', 'return register_widget("twitterWidget");'));

class twitterWidget extends WP_Widget {
	
	function twitterWidget() {
        parent::WP_Widget(false, $name = 'twitterWidget');	
    }

	function render($instance)
	{
		
		$tw_title 		= esc_attr($instance['tw-title']);
		$tw_userid 		= esc_attr($instance['tw-userID']);
		$tw_height 		= esc_attr($instance['tw-height']);
		$tw_width 		= (is_int(esc_attr($instance['tw-width']))) ? esc_attr($instance['tw-width']) : '"auto"';
		$tw_interval 	= esc_attr($instance['tw-interval']);
		$tw_bg_color 	= esc_attr($instance['tw-bg-color']);
		$tw_bg_text 	= esc_attr($instance['tw-bg-text']);
		$tw_tweet_text 	= esc_attr($instance['tw-tweet-text']);
		$tw_tweet_bg 	= esc_attr($instance['tw-tweet-bg']);
		$tw_tweet_links = esc_attr($instance['tw-tweet-links']);
		$tw_loop 		= esc_attr($instance['tw-loop']);
		$tw_rpp 		= esc_attr($instance['tw-rpp']);
		
	  echo "<script src='http://widgets.twimg.com/j/2/widget.js'></script>
			<script>
			new TWTR.Widget({
			  version: 2,
			  type: 'profile',
			  rpp: ". $tw_rpp . ",
			  interval: ". $tw_interval . ",
			  width: ". $tw_width . ",
			  height: ". $tw_height . ",
			  theme: {
				shell: {
				  background: '#". $tw_bg_color . "',
				  color: '#". $tw_bg_text . "'
				},
				tweets: {
				  background: '#". $tw_tweet_bg . "',
				  color: '#". $tw_tweet_text . "',
				  links: '#". $tw_tweet_links . "'
				}
			  },
			  features: {
				scrollbar: false,
				loop: " , $tw_loop , ",
				live: true,
				hashtags: true,
				timestamp: true,
				avatars: false,
				behavior: 'default'
			  }
			}).render().setUser('". $tw_userid . "').start()
			</script>";
	}
	
	function widget($args, $instance) {
	  extract($args);
	  $title = apply_filters('widget_title', $instance['tw-title'] );
	  echo $before_widget;
	  if ($title)
	  {
		  echo $before_title, $title, $after_title;
	  }
	  $this->render($instance);
	  echo $after_widget;
	}
		
	function update($new_instance, $old_instance) {				
		
		$instance = $old_instance;
		$instance['tw-title'] 		= strip_tags($new_instance['tw-title']);
		$instance['tw-userID'] 		= strip_tags($new_instance['tw-userID']);
		$instance['tw-height'] 		= strip_tags($new_instance['tw-height']);
		$instance['tw-width'] 		= strip_tags($new_instance['tw-width']);
		$instance['tw-interval'] 	= strip_tags($new_instance['tw-interval']);
		$instance['tw-bg-color'] 	= strip_tags($new_instance['tw-bg-color']);
		$instance['tw-bg-text'] 	= strip_tags($new_instance['tw-bg-text']);
		$instance['tw-tweet-text'] 	= strip_tags($new_instance['tw-tweet-text']);
		$instance['tw-tweet-bg'] 	= strip_tags($new_instance['tw-tweet-bg']);
		$instance['tw-tweet-links'] = strip_tags($new_instance['tw-tweet-links']);
		$instance['tw-loop'] 		= strip_tags($new_instance['tw-loop']);
		$instance['tw-rpp'] 		= strip_tags($new_instance['tw-rpp']);
		        
        return $instance;
    }
	
	function form($instance){
		
		$tw_title 		= esc_attr($instance['tw-title']);
		$tw_userid 		= esc_attr($instance['tw-userID']);
		$tw_height 		= esc_attr($instance['tw-height']);
		$tw_width 		= esc_attr($instance['tw-width']);
		$tw_interval 	= esc_attr($instance['tw-interval']);
		$tw_bg_color 	= esc_attr($instance['tw-bg-color']);
		$tw_bg_text 	= esc_attr($instance['tw-bg-text']);
		$tw_tweet_text 	= esc_attr($instance['tw-tweet-text']);
		$tw_tweet_bg 	= esc_attr($instance['tw-tweet-bg']);
		$tw_tweet_links = esc_attr($instance['tw-tweet-links']);
		$tw_loop 		= esc_attr($instance['tw-loop']);
		$tw_rpp 		= esc_attr($instance['tw-rpp']);
	
		?>
		
		<p>
		  <label for="<?php echo $this->get_field_id('tw-title'); ?>"><?php _e('Title:'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('tw-title'); ?>" name="<?php echo $this->get_field_name('tw-title'); ?>" type="text" value="<?php echo $tw_title; ?>" />
          
           <label for="<?php echo $this->get_field_id('tw-userID'); ?>"><?php _e('Twitter User Id:'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('tw-userID'); ?>" name="<?php echo $this->get_field_name('tw-userID'); ?>" type="text" value="<?php echo $tw_userid; ?>" />
		
		  <label for="<?php echo $this->get_field_id('tw-height'); ?>"><?php _e('Height (px):'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('tw-height'); ?>" name="<?php echo $this->get_field_name('tw-height'); ?>" type="text" size="4" maxlength="4" value="<?php echo $tw_height; ?>" />
          
		  <label for="<?php echo $this->get_field_id('tw-width'); ?>"><?php _e('Width (px or "auto"):'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('tw-width'); ?>" name="<?php echo $this->get_field_name('tw-width'); ?>" type="text" size="4" maxlength="4" value="<?php echo $tw_width; ?>" />
          
          <label for="<?php echo $this->get_field_id('tw-interval'); ?>"><?php _e('Interval (ms):'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('tw-interval'); ?>" name="<?php echo $this->get_field_name('tw-interval'); ?>" type="text" size="4" maxlength="4" value="<?php echo $tw_interval; ?>" />
          
          <label for="<?php echo $this->get_field_id('tw-bg_color'); ?>"><?php _e('Shell Background Color (hex):'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('tw-bg-color'); ?>" name="<?php echo $this->get_field_name('tw-bg-color'); ?>" type="text" size="6" maxlength="6" value="<?php echo $tw_bg_color; ?>" />
          
		  <label for="<?php echo $this->get_field_id('tw-bg-text'); ?>"><?php _e('Shell Text Color (hex):'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('tw-bg-text'); ?>" name="<?php echo $this->get_field_name('tw-bg-text'); ?>" type="text" size="6" maxlength="6" value="<?php echo $tw_bg_text; ?>" />
          
          <label for="<?php echo $this->get_field_id('tw-tweet-bg'); ?>"><?php _e('Tweet Background Color (hex):'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('tw-tweet-bg'); ?>" name="<?php echo $this->get_field_name('tw-tweet-bg'); ?>" type="text" size="6" maxlength="6" value="<?php echo $tw_tweet_bg; ?>" />
          
          <label for="<?php echo $this->get_field_id('tw-tweet-text'); ?>"><?php _e('Tweet Text Color (hex):'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('tw-tweet-text'); ?>" name="<?php echo $this->get_field_name('tw-tweet-text'); ?>" type="text" size="6" maxlength="6" value="<?php echo $tw_tweet_text; ?>" />

		  <label for="<?php echo $this->get_field_id('tw-tweet-links'); ?>"><?php _e('Link Color (hex):'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('tw-tweet-links'); ?>" name="<?php echo $this->get_field_name('tw-tweet-links'); ?>" type="text" size="6" maxlength="6" value="<?php echo $tw_tweet_links; ?>" />
          
          <label for="<?php echo $this->get_field_id('tw-rpp'); ?>"><?php _e('Number of tweets:'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('tw-rpp'); ?>" name="<?php echo $this->get_field_name('tw-rpp'); ?>" type="text" size="2" maxlength="2" value="<?php echo $tw_rpp; ?>" />
		  
		  <label for="<?php echo $this->get_field_id('tw-loop'); ?>"><?php _e('Loop (true/false):'); ?></label>
		  <select id="<?php echo $this->get_field_id('tw-loop'); ?>" name="<?php echo $this->get_field_name('tw-loop'); ?>">
		  	<?php if ($tw_loop == "true"){?>
			  <option value="true" selected="selected">Yes</option>
			  <option value="false">No</option>
			  <?php } else { ?>
			  <option value="true">Yes</option>
			  <option value="false" selected="selected">No</option>
			 <?php } ?>
		  </select>
		</p>
	<?php
	}
	
}//class twitter widget
?>