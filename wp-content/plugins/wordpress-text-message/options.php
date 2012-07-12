<?php

add_action('init','mrt_save_options');

function mrt_save_options()
{
	$update	= false;
	
	if(isset($_POST) && isset($_POST['mrt_save_options_nonce'])) :
	
		if(wp_verify_nonce($_POST['mrt_save_options_nonce'], 'mrt_save_options') ) :
	
			update_option('mrt_sms_header'			,$_POST['mrt_sms_header']);
			update_option('mrt_sms_footer'			,$_POST['mrt_sms_footer']);
			update_option('mrt_sms_from'			,$_POST['from_addy']);
			update_option('mrt_sms_max'				,$_POST['mrt_sms_max']);
			update_option('mrt_sms_is_send_msg'		,$_POST['send_msg']);
			update_option('mrt_sms_batch'			,$_POST['mrt_sms_batch']);
			update_option('mrt_widget_captcha'		,$_POST['mrt_widget_captcha']);
			update_option('mrt_sms_send_update'		,$_POST['mrt_sms_send_update']);
			update_option('mrt_sms_redirect_link'	,$_POST['mrt_sms_redirect_link']);
			update_option('mrt_redirect_activ'		,$_POST['mrt_redirect_activ']);
			$update = true;
		
			/*
			if($_POST['send_msg'] == '' && isset($_POST['option'])) :
				$send_msg = '';
				update_option('mrt_sms_is_send_msg',$send_msg);
				$update = true;
			endif;
			*/
			
			  
			if($update) :
				$link	= admin_url('admin.php?page=wptext-message-options&wp-message=3');
				wp_redirect($link);
			endif;
			
		else :
			wp_die('Security Check');
		endif;
		
	endif;
}

function mrt_sms_options_page() { ?>

	<div class="wrap">
      	<h2><?php _e('Wordpress Text Message Options') ?></h2>
      	<br />
        
<em><a href="http://www.totalbounty.com">Total Bounty Marketplace</a> now sponsors development for this plugin.  For comments, suggestions, bug reporting, etc please<br /> <a href="http://www.totalbounty.com/forums/topic/wordpress-text-message/">use our Forum page for this plugin</a>.</em>

        <?php $mrt_sms_header 			= get_option("mrt_sms_header");  ?>
        <?php $mrt_sms_footer			= get_option("mrt_sms_footer");  ?>
        <?php $mrt_sms_from 			= get_option("mrt_sms_from");  ?>
        <?php $mrt_sms_max 				= get_option("mrt_sms_max"); ?>
        <?php $mrt_sms_batch 			= get_option("mrt_sms_batch"); ?>
        <?php $mrt_widget_captcha		= get_option("mrt_widget_captcha"); ?>
        <?php $send_msg_option 			= get_option("mrt_sms_is_send_msg"); ?>
        <?php $mrt_sms_send_update		= get_option("mrt_sms_send_update"); ?>
        <?php $mrt_sms_redirect_link	= get_option("mrt_sms_redirect_link"); ?>
        <?php $mrt_redirect_activ		= get_option("mrt_redirect_activ"); ?>
        

      	<form name='mrt_sms_update_options3' id='mrt_sms_update_options3' method='POST' action=''>
         	<br />
            
	        <label for="mrt_sms_shortcode" style="display:inline-block;width:350px">Subscribe Form Shortcode</label>
         	<input id="mrt_sms_shortcode" name="mrt_sms_shortcode" value="wp-text-message-register" type="text" size="80" readonly="readonly" /><br />
            <em>to embed subscription form into page/post</em>

<br /><br />
	        <label for="mrt_sms_shortcode" style="display:inline-block;width:350px">Unsubscribe Form Shortcode</label>
         	<input id="mrt_sms_shortcode" name="mrt_sms_shortcode" value="wp-text-message-unsubscribed" type="text" size="80" readonly="readonly" /><br />
            <em>to embed unsubscribe form into page/post</em>
         	
            <br /><br />
            
	        <label for="mrt_sms_header" style="display:inline-block;width:350px">Header Form</label>
         	<input id="mrt_sms_header" name="mrt_sms_header" value="<?php echo $mrt_sms_header; ?>" type="text" size="80" />
         	
            <br /><br />
            
	        <label for="mrt_sms_footer" style="display:inline-block;width:350px">Footer Form</label>
         	<input id="mrt_sms_footer" name="mrt_sms_footer" value="<?php echo $mrt_sms_footer; ?>" type="text" size="80" />
         	
            <br /><br />
            
	        <label for="mrt_sms_from" style="display:inline-block;width:350px">SMS From</label>
         	<input id="mrt_sms_from" name="from_addy" value="<?php echo $mrt_sms_from; ?>" type="text" size="80" />
         	
            <br /><br />
            
	        <label for="mrt_sms_max" style="display:inline-block;width:350px">SMS Max</label>
	        <input id="mrt_sms_max" name="mrt_sms_max" value="<?php echo $mrt_sms_max; ?>" type="text" size="80" /
		 	
            <br /><br />
            
	        <label for="mrt_sms_batch" style="display:inline-block;width:350px">How Many Users Will Be Sent Per Batch Email</label>
	        <input name="mrt_sms_batch" value="<?php echo $mrt_sms_batch; ?>" type="text" size="80" /><br />
            <em>This option to avoid server from being overloaded, &lt; 25 or less is recommended</em>

            <br /><br />
            
	        <label for="mrt_sms_send_update" style="display:inline-block;width:350px">Post IDs</label>
	        <input name="mrt_sms_send_update" value="<?php echo $mrt_sms_send_update; ?>" type="text" size="80" /><br />
            <em>Send all subscribers SMS text message each time the following pages are updated ( seperated with comma )</em>

            <br /><br />   
            
	        <label for="mrt_sms_redirect_link" style="display:inline-block;width:350px">Redirect Link</label>
	        <input name="mrt_sms_redirect_link" value="<?php echo $mrt_sms_redirect_link; ?>" type="text" size="80" /><br />
            <em>Redirect to the link after visitor registers the number</em>

            <br /><br />            

           	<?php $checked	= ($mrt_redirect_activ == 'on') ? "checked='checked'" : ""; ?>
            <input type='checkbox' name='mrt_redirect_activ' id='mrt_redirect_activ' <?php echo $checked; ?> /> Activate redirect

            <br /><br />            

           	<?php $checked	= ($mrt_widget_captcha == 'on') ? "checked='checked'" : ""; ?>
            <input type='checkbox' name='mrt_widget_captcha' id='mrt_widget_captcha' <?php echo $checked; ?> /> Widget using captcha
		 	
            <br /><br />

           	<?php $checked	= ($send_msg_option == 'on') ? "checked='checked'" : ""; ?>
            <input type='checkbox' name='send_msg' id='send_msg' <?php echo $checked; ?> /> Send all subscribers SMS text message each time a new pages or posts are published or when posts are edited and republished
            
            <br /><br />
            
			<span class="submit" style="padding:0px"><input type="submit" value="Save Options" /></span>
            
            <br /><br />

			<?php wp_nonce_field('mrt_save_options','mrt_save_options_nonce'); ?>
           	<input type="hidden" id="option" name="option" value="1"> </input>

       		</form>
      	<br />        
   </div>
   <div>
     Wordpress Text Message Plugin enhanced by <a href="http://www.jtpratt.com/" title="JTPratt Media">JTPratt Media</a>, now sponsored by <a href="http://www.totalbounty.com/" title="Total Bounty">Total Bounty</a><br />
     Visit the <a href="http://www.totalbounty.com/freebies/wordpress-text-message/">Wordpress Text Message Plugin Page</a><br />
      Based on the Original SMS Text Message Plugin by <a href="http://semperfiwebdesign.com/" title="Semper Fi Web Design">Semper Fi Web Design</a> (which we've now extremely enhanced)
   </div>
<?php } 	global $mrt_sms_ll; $mrt_sms_ll = '
	<input type="submit" value="Subscribe" />
	<div style="font-size:9px"></div>';

?>
