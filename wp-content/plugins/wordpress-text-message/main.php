<?php

add_action('init'	, "mrt_sms_send_standalone");

function mrt_sms_send_standalone()
{
	if( isset($_POST['send']) && $_POST['message'] != '' && $_POST['send']['subject'] != '' && mrt_verify('wptext-add-sms','_wpnonce')) :

		$args	= array(
			'post_id'	=> '',
			'message'	=> $_POST['message'],
			'subject'	=> $_POST['send']['subject'],
			'type'		=> 'message'
		);
		
		mrt_sms_insert_queue($args);
		
		$admin_url	= admin_url("admin.php?page=wptext-message&wp-message=1");
		wp_redirect($admin_url);
		
	elseif( isset($_POST['send']) && ($_POST['message'] == '' || $_POST['send']['subject'] == '') && mrt_verify('wptext-add-sms','_wpnonce') ) :
	
		$admin_url	= admin_url("admin.php?page=wptext-message&wp-message=2");
		wp_redirect($admin_url);
	
	endif;
}

function mrt_sms_meta_box1()
{
	global $succfail;
	$mrt_sms_maxlen = get_option('mrt_sms_max');
	?>

	<div style="padding: 10px;">

	<form name='mrt_send_sms_form' id='mrt_send_sms_form' method='POST' action=''>
		
        Send an SMS message to your subscribers here:
		<br /><br />
		
        <label for="sms_subject" style="display:inline-block;width:80px">Subject</label>
        <input id="sms_subject" name="send[subject]" type="text" size="23" />
        
        <br /><br />
        
		<label for="sms_message" style="display:inline-block;width:80px">Message</label>
        <textarea id="sms_message" maxlength="<?php echo $mrt_sms_maxlen; ?>" onkeyup="return ismaxlength(this)" onKeyPress="check_length(this.form,<?php echo $mrt_sms_maxlen; ?>);" 
        		  onKeyDown="check_length(this.form);" name="message" rows="5" cols="30"></textarea>
        
        <br /><br />
        
        <?php wp_nonce_field('wptext-add-sms'); ?>
		<input size="5" value=<?php echo $mrt_sms_maxlen; ?> name=text_num> Characters Left<br />
		<span class="submit"><input type="submit" value="Send Message" /></span>
        
	</form>
    
	<br /><em><a href="http://www.totalbounty.com">Total Bounty Marketplace</a> now sponsors development for this plugin.  For comments, suggestions, bug reporting, etc please<br /> <a href="http://www.totalbounty.com/forums/topic/wordpress-text-message/">use our Forum page for this plugin</a>.</em>
	</div>
	<?php
}


function mrt_sms_meta_box2(){
	?>
	<div style="padding:10px;">
		<div style="font-size:13pt;text-align:center;">Our Tips, Tricks, and Posts...</div>


		<?php
			include_once(ABSPATH . WPINC . '/feed.php');
			$rss	= fetch_feed('http://www.totalbounty.com/feed/');
			
			if (!is_wp_error( $rss ) ) : 
    			$maxitems = $rss->get_item_quantity(15); 
    			$rss_items = $rss->get_items(0, $maxitems);
			endif;
			
			if ($maxitems > 0) :
				foreach ( $rss_items as $item ) : 
				?><p><strong><a href="<?php $item->get_permalink(); ?>"><?php echo esc_html($item->get_title()); ?></a></strong></p><?php 
				endforeach; 
			endif;
			
			/*
			$feed->set_feed_url();
			$feed->strip_htmltags(array('p'));
			$feed->set_cache_location(WPTEXTMSGFOLDER."");
			$feed->init();
			$feed->handle_content_type();
			
			if ($feed->data):
				
				$items = $feed->get_items();
				
				foreach($items as $item): 
				?>
				<p>
					<strong><a href="<?php echo $item->get_link(); ?>">
					<?php echo $item->get_title(); ?></a></strong>
				</p>
				<?php
                endforeach;
			endif; 
			*/
		?>

			<div style="font-size:13pt;text-align:center;">Total Bounty Marketplace</div>
			<div style="text-align:center"><em>HTML templates, WordPress themes and plugins, PSD templates and graphics, and more!</em>
			
            <br /><br />

			<a href="http://www.totalbounty.com" target="_blank">Visit our website: www.TotalBounty.com</a>
			<a href="http://www.totalbounty.com" border="0"><img src="http://www.totalbounty.com/wp-content/themes/total_bounty/images/logo-bt.png" alt="Total Bounty Marketplace" title="Total Bounty Marketplace"></a>
		</div>
	</div>				
	<?php
}


function mrt_sms_main_control() 
{
	global $succfail;
	add_meta_box("mrt_sms", "Send Quick Message", "mrt_sms_meta_box1", "sms");
	add_meta_box("mrt_sms", "Total Bounty Recent Blog Posts", "mrt_sms_meta_box2", "sms2");
	$mrt_sms_maxlen = get_option('mrt_sms_max');
    ?>

	<div class="wrap">
		<h2><?php _e('Wordpress Text Message Control Panel') ?></h2>
		<div id="dashboard-widgets-wrap">
			<div class="metabox-holder">
				<div style="float:left; width:48%;" class="inner-sidebar1">
					<?php do_meta_boxes('sms','advanced','');  ?>	
				</div>

				<div style="float:right; width:48%; " class="inner-sidebar1">
					<?php do_meta_boxes('sms2','advanced',''); ?>	
				</div>
			</div>
            
		</div>
	</div>
   	<div style="clear:both"></div>
	<?php
   	if ( $_POST['QS_user_email_post'] ) :
    	$message = quick_subscribe_register($_POST['QS_user_email_post']);
	endif;
} 
?>
