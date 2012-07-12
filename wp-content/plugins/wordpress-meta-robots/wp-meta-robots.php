<?php
/*
Plugin Name: WordPress Meta Robots
Plugin URI: http://www.destio.de/tools/wp-meta-robots/
Description: This plugin will give you full control of the <code>meta robots</code> tag for each post or page.
Author: Designstudio, Philipp Speck
Version: 1.9
Author URI: http://www.destio.de/
*/

if ( !class_exists ('wp_meta_robots_plugin')) {
	class wp_meta_robots_plugin {

	function meta_robots_addcolumn() {
		global $wpdb;
		$wpdb->query("ALTER TABLE $wpdb->posts ADD COLUMN meta_robots varchar(20)");
	}
			
	function meta_robots_insert_post($pID) {
		global $wpdb;
		extract($_POST);
		$wpdb->query("UPDATE $wpdb->posts SET meta_robots = '$meta_robots' WHERE ID = $pID");
	}
	
	function meta_robots_options_box() {
		add_meta_box('meta_robots', 'Meta Robots', array('wp_meta_robots_plugin','meta_robots_dropdown_box'), 'page', 'side', 'low');
		add_meta_box('meta_robots', 'Meta Robots', array('wp_meta_robots_plugin','meta_robots_dropdown_box'), 'post', 'side', 'low');
	}
	
	function meta_robots_dropdown_box() {
		global $post;
		$meta_robots = $post->meta_robots;
	?>
		<fieldset id="mycustom-div">
		<div>
		<p>
		<label for="meta_robots" ></label>
			<select name="meta_robots" id="meta_robots">
			<option <?php if ($meta_robots == "index, follow") echo 'selected="selected"'?>>index, follow</option>
			<option <?php if ($meta_robots == "index, nofollow") echo 'selected="selected"'?>>index, nofollow</option>
			<option <?php if ($meta_robots == "noindex, follow") echo 'selected="selected"'?>>noindex, follow</option>
			<option <?php if ($meta_robots == "noindex, nofollow") echo 'selected="selected"'?>>noindex, nofollow</option>
			</select>
		</p>
		</div>
		</fieldset>
	<?php
	}

	function add_meta_robots_tag() {
		global $post;
		if ( is_home() || is_single() || is_page() ) {
		$meta_robots = (empty($post->meta_robots)) ? 'index, follow' : $post->meta_robots;
		echo '<meta name="robots" content="'.$meta_robots.'" />'."\n";
		} elseif ( is_category() || is_tag() || is_archive() ) {
		echo '<meta name="robots" content="noindex, follow" />'."\n";
		} else {
		echo '<meta name="robots" content="noindex, nofollow" />'."\n";
		}
	}	
	
	} // class meta_robots_plugin
}

add_action('init', array('wp_meta_robots_plugin','meta_robots_addcolumn'));
add_action('admin_menu', array('wp_meta_robots_plugin','meta_robots_options_box'));
add_action('wp_insert_post', array('wp_meta_robots_plugin','meta_robots_insert_post'));
add_action('wp_head', array('wp_meta_robots_plugin','add_meta_robots_tag'));
?>