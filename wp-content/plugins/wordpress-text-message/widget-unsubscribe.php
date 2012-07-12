<?php

global $wpTextMessageMobileUnRegisterWidget;

class wpTextMessageMobileUnRegisterWidget extends wpTextMessageWidgetApp
{
	var $options	= array(
		'carrier'	=> array(
			"metropcs"	=> "Metro PCS",
			"verizon" 	=> "Verizon",
			"tmobile" 	=> "T-Mobile",
			"vmobile" 	=> "Virgin Mobile",
			"cingular" 	=> "Cingular(GoPhone)",
			"nextel" 	=> "Nextel",
			"alltel" 	=> "Alltel",
			"sprint" 	=> "Sprint",
			"attmob" 	=> "AT&amp;T Mobility(Cingular)",
			"attwire" 	=> "AT&amp;T Wireless",
			"uscell" 	=> "US Cellular",
		)
	);
	// =================================================
	// initialization the widget */
	// =================================================
	function wpTextMessageMobileUnRegisterWidget() 
	{
		/* Widget settings. */
		$widget_ops = array( 
			'classname' 	=> 'mobile-unsubscribe-widget',
			'description' 	=> 'Unsubscribe mobile phone widget' 
		);

		/* Widget control settings. */
		$control_ops = array( 
			'width' 	=> 300, 
			'height' 	=> 350, 
			'id_base' 	=> 'mobile-unsubscribe-widget' 
		);

		// id_base must be same withe WP_Widget('name')
		/* Create the widget. */
		$this->WP_Widget( 'mobile-unsubscribe-widget', 'WP Text Message ( Unsubscribe )', $widget_ops, $control_ops );
	}
	
	// =================================================
	/* show the widget */
	// =================================================
	function widget( $args, $instance ) 
	{
		extract( $args );

		/* User-selected settings. */
		$title 		= apply_filters('widget_title', $instance['title'] );
		$captcha	= get_option('mrt_widget_captcha');
		$red_link	= get_option('mrt_sms_unregister_redirect_link');
		$redirect	= get_option('mrt_redirect_activ');
		
		if($captcha == 'on') :
			
			if(isset($_SESSION[$widget_id]['captcha_1']) || isset($_SESSION[$widget_id]['captcha_2'])) :
				$label1	= $_SESSION[$widget_id]['captcha_1'];
				$label2	= $_SESSION[$widget_id]['captcha_2'];
			else :
				$label1	= $_SESSION[$widget_id]['captcha_1']	= rand(1,20);
				$label2	= $_SESSION[$widget_id]['captcha_2']	= rand(1,20);
			endif;
			
		endif;
		
		echo $before_widget;
		echo $before_title.$title.$after_title;
		
		?><form class="mrt-unsubscribe mrtForm" method="post" action=""><?php
		
		$this->generateInputText('Phone Number','number','');
		
		if($captcha == 'on') :
			$label	= $label1.' + '.$label2;	
			$this->generateInputText($label,'captcha','');
		endif;
		
		?>
        	<p class="message-wrapper"></p>
            <p><input type="submit" name="submit" value="Unsubscribe" /></p>
            <input type="hidden" name="widget_id" value="<?php echo $widget_id; ?>" />
            <input type="hidden" name="action" value="unregister_mobile_phone" />
        	<p><?php echo $footer; ?></p>
		</form>
		<?php
		
		echo $after_widget;
		
		?>
		<script type="text/javascript" language="javascript1.2">
		
		function delayer()
		{ window.location = "<?php echo $red_link; ?>" }
		
		jQuery(document).ready(function(){
			jQuery('#<?php echo $widget_id; ?> form.mrt-unsubscribe').submit(function(){
				var ajaxurl	= "<?php echo admin_url('admin-ajax.php'); ?>";
				var data	= jQuery(this).serialize();
				
				jQuery.ajax({
	            	url		: "<?php echo admin_url('admin-ajax.php'); ?>",
	                type	: 'POST',
	                data	: data,
					success	: function(results)
					{
						var string	= results.substring(0, results.length-1);
						var success	= results.substring(results.length, results.length-1);
						jQuery('#<?php echo $widget_id; ?> form.mrt-unsubscribe .message-wrapper').html(string).fadeIn('fast').delay(10000).fadeOut('slow');
						
						<?php if($redirect == "on" && !empty($red_link)) : ?>
						if(success == 1) { setTimeout('delayer()', 5000) }
						<?php endif; ?>
					}
				});
				return false;
			});
		});
		</script>
        <?php

	}

	// =================================================
	/* shortcode */
	// =================================================
	
	function shortcode()
	{
		?><form method="post" class="mrtForm" action=""><?php
		
		$this->generateInputText('Phone Number','number','');
			
		?>
			<p class="message-wrapper"></p>
			<p><input type="submit" name="submit" value="Register" /></p>
				
			<input type="hidden" name="widget_id" value="<?php echo $widget_id; ?>" />
			<input type="hidden" name="action" value="register_mobile_phone" />
			<p><?php echo $footer; ?></p>
		<?php
			
		echo $after_title;
			
		?>
		
		<script type="text/javascript" language="javascript1.2">
		
		function delayer()
			{ window.location = "<?php echo $red_link; ?>" }
			
			jQuery(document).ready(function(){
			jQuery('#<?php echo $widget_id; ?> form').submit(function(){
				var ajaxurl	= "<?php echo admin_url('admin-ajax.php'); ?>";
				var data	= jQuery(this).serialize();
					
				jQuery.ajax({
					url		: "<?php echo admin_url('admin-ajax.php'); ?>",
					type	: 'POST',
					data	: data,
					success	: function(results)
					{
						var string	= results.substring(0, results.length-1);
						jQuery('#<?php echo $widget_id; ?> .message-wrapper').html(string).fadeIn('fast').delay(10000).fadeOut('slow');
						
						<?php if($redirect == "on") : ?>
						setTimeout('delayer()', 5000)
						<?php endif; ?>
					}
				});
				return false;
			});
		});
		</script>
		<?php
	}
	
	// =================================================
	/* update the widget */
	// =================================================
	function update( $new_instance, $old_instance ) 
	{
		$instance = $old_instance;
		$instance['title'] 		= strip_tags( $new_instance['title'] );	

		return $instance;
	}

	// =================================================
	/* the form */
	// =================================================	
	function form( $instance ) 
	{

		/* Set up some default widget settings. */
		/* Set up some default widget settings. */
		$defaults = array( 
			'title' 	=> 'Unsubscribe Your Phone', 
		);
		
		$instance = wp_parse_args( (array) $instance, $defaults ); 
        
       	$this->generateInputText('Title','title',$instance['title']);
	}
}

$wpTextMessageMobileUnRegisterWidget = new wpTextMessageMobileUnRegisterWidget;
?>