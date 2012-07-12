<?php
add_action('wp_ajax_mrt-unsubscribed-form',create_function('',"echo mrt_unsubscribe_shortcode(); exit();"));
add_action('wp_ajax_nopriv_mrt-unsubscribed-form',create_function('',"echo mrt_unsubscribe_shortcode(); exit();"));

/* ====================================================	*/
/* ==				  REGISTER SHORTCODE			==	*/
/* ====================================================	*/
function mrt_register_shortcode()
{
	global $wpTextMessageMobileUnRegisterWidget;
	
	$header		= get_option("mrt_sms_header");
	$footer		= get_option("mrt_sms_footer");
	$captcha	= get_option('mrt_widget_captcha');
	$red_link	= get_option('mrt_sms_redirect_link');
	$redirect	= get_option('mrt_redirect_activ');
	$widget_id	= "mobile-register-widget-0";

	if($captcha == 'on') :
			
		if(isset($_SESSION[$widget_id]['captcha_1']) || isset($_SESSION[$widget_id]['captcha_2'])) :
			$label1	= $_SESSION[$widget_id]['captcha_1'];
			$label2	= $_SESSION[$widget_id]['captcha_2'];
		else :
			$label1	= $_SESSION[$widget_id]['captcha_1']	= rand(1,20);
			$label2	= $_SESSION[$widget_id]['captcha_2']	= rand(1,20);
		endif;
		
	endif;
	
	ob_start();
	
	?>
    <div class="mrtShortcodeForm" id="mrtRegisterForm">
    <h2><?php echo $header; ?></h2>
    <form method="post" class="mrtForm" action="">
	
	<p>
    	<label for="widget-mobile-register-widget-number">Phone Number</label> <br />
        <input type="text" style="width: 80%;" value="" name="widget-mobile-register-widget[0][number]" id="mobile-register-widget-number" />
	</p>
	<p>
    	<label for="widget-mobile-register-widget-carrier">Carrier</label> <br />
        <select id="widget-mobile-register-widget-carrier" name="widget-mobile-register-widget[0][carrier]">
			<?php mrt_sms_carrier_echo_options(); ?>
		</select>
	</p>
	<?php
	
		
	if($captcha == 'on') :
		$label	= $label1.' + '.$label2;	
		?>
	<p>
    	<label for="widget-mobile-register-widget-captcha"><?php echo $label; ?></label> <br />
        <input type="text" style="width: 80%;" value="" name="widget-mobile-register-widget[0][captcha]" id="widget-mobile-register-widget-captcha" />
	</p>
    	<?php
	endif;
		
	?>
       	<p class='message-wrapper'></p>
        <p><input type="submit" name="submit" value="Register" /></p>
         	
		<input type="hidden" name="widget_id" value="<?php echo $widget_id; ?>" />
        <input type="hidden" name="action" value="register_mobile_phone" />
        <p><?php echo $footer; ?></p>
	</form>
    </div>
	
	<script type="text/javascript" language="javascript1.2">
	
	function delayer()
		{ window.location = "<?php echo $red_link; ?>" }
		
		jQuery(document).ready(function(){
		jQuery('#mrtRegisterForm form').submit(function(){
			var ajaxurl	= "<?php echo admin_url('admin-ajax.php'); ?>";
			var data	= jQuery(this).serialize();
				
			jQuery.ajax({
	           	url		: "<?php echo admin_url('admin-ajax.php'); ?>",
	            type	: 'POST',
	            data	: data,
				success	: function(results)
				{
					var string	= results.substring(0, results.length-2);
					var success	= results.substring(results.length-1, results.length-2);
					jQuery('#mrtRegisterForm .message-wrapper').html(string).fadeIn('fast').delay(10000).fadeOut('slow');
						
					<?php if($redirect == "on") : ?>
					if(success == 1) { setTimeout('delayer()', 5000) }
					<?php endif; ?>
				}
			});

			return false;
		});
	});
	</script>
    <?php
	
	$content	= ob_get_contents();
	
	ob_end_clean();
	
	return $content;
}

/* ====================================================	*/
/* ==			   UNSUBSCRIBED SHORTCODE			==	*/
/* ====================================================	*/

function mrt_unsubscribe_shortcode()
{
	global $wpTextMessageMobileRegisterWidget;
	
	$header		= get_option("mrt_sms_header");
	$footer		= get_option("mrt_sms_footer");
	$captcha	= get_option('mrt_widget_captcha');
	$red_link	= get_option('mrt_sms_redirect_link');
	$redirect	= get_option('mrt_redirect_activ');
	$widget_id	= "mobile-unsubscribe-widget-0";

	if($captcha == 'on') :
			
		if(isset($_SESSION[$widget_id]['captcha_1']) || isset($_SESSION[$widget_id]['captcha_2'])) :
			$label1	= $_SESSION[$widget_id]['captcha_1'];
			$label2	= $_SESSION[$widget_id]['captcha_2'];
		else :
			$label1	= $_SESSION[$widget_id]['captcha_1']	= rand(1,20);
			$label2	= $_SESSION[$widget_id]['captcha_2']	= rand(1,20);
		endif;
		
	endif;
	
	ob_start();
	
	?>
    <div class="mrtShortcodeForm" id="mrtUnsubscibedForm">
    <h2>Unsubscribe Wordpress Text Message</h2>
    <form method="post" class="mrtForm" action="">
	<p>
    	<label for="widget-mobile-unsubscribe-widget-3-number">Phone Number</label> <br />
        <input id="widget-mobile-unsubscribe-widget-3-number" name="widget-mobile-unsubscribe-widget[0][number]" value="" style="width:80%" type="text" />
	</p>

	<?php
	if($captcha == 'on') :
		$label	= $label1.' + '.$label2;	
		?>    
    <p>

    	<label for="widget-mobile-unsubscribe-widget-3-captcha"><?php echo $label; ?></label> <br />
        <input id="widget-mobile-unsubscribe-widget-3-captcha" name="widget-mobile-unsubscribe-widget[0][captcha]" value="" style="width:80%" type="text" />
	</p>
    <?php endif; ?>
    
    <p class="message-wrapper"></p>
	
    <p>
    	<input name="submit" value="Unsubscribe" type="submit" />
	</p>
	
    <input name="widget_id" value="<?php echo $widget_id; ?>" type="hidden" />
    <input name="action" value="unregister_mobile_phone" type="hidden" />
    
    </form>
		
    </div>
	
	<script type="text/javascript" language="javascript1.2">
	
	function delayer()
		{ window.location = "<?php echo $red_link; ?>" }
		
		jQuery(document).ready(function(){
		jQuery('#mrtUnsubscibedForm form').submit(function(){
			var ajaxurl	= "<?php echo admin_url('admin-ajax.php'); ?>";
			var data	= jQuery(this).serialize();
				
			jQuery.ajax({
				
	           	url		: "<?php echo admin_url('admin-ajax.php'); ?>",
	            type	: 'POST',
	            data	: data,
				success	: function(results)
				{
					var string		= results.substring(0, results.length-2);
					var success		= results.substring(results.length-2, results.length-1);
					
					jQuery('#mrtUnsubscibedForm .message-wrapper').html(string).fadeIn('fast').delay(10000).fadeOut('slow');
					
					<?php if($redirect == "on") : ?>
					if(success == 1) { setTimeout('delayer()', 5000) }
					<?php endif; ?>
				}
			});
			return false;
		});
	});
	</script>
    <?php
	
	$content	= ob_get_contents();
	
	ob_end_clean();
	
	return $content;
}

?>