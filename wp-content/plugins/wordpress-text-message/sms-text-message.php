<?php

global $mrt_debug,$succfail,$mrt_submitted;

/*
Plugin Name: Wordpress Text Message
Plugin URI: http://www.totalbounty.com/freebies/wordpress-text-message/
Description: Wordpress SMS text message Subscription plugin and widget, now includes shortcode [wp-text-message] for adding the form to posts and pages
Author: Total Bounty
Version: 2.08
Author URI: http://www.totalbounty.com
*/

/*
Copyright (C) 2012 Total Bounty, totalbounty.com (admin AT totalbounty DOT com)
A small part of the original code was based on the SMS Text Message plugin (no longer available) which is Copyright (C) 2008-2009 Michael Torbert, semperfiwebdesign.com (michael AT semperfiwebdesign DOT com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

if ( ! defined( 'WP_CONTENT_URL' ) )
      define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' );
if ( ! defined( 'WP_CONTENT_DIR' ) )
      define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
if ( ! defined( 'WP_PLUGIN_URL' ) )
      define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
if ( ! defined( 'WP_PLUGIN_DIR' ) )
      define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );
	  
define(WPTEXTMSGFOLDER		,plugin_dir_path( __FILE__ ));
define(WPTEXTMSGURL			,plugin_dir_url( __FILE__ ));

require_once(WPTEXTMSGFOLDER."main.php");
require_once(WPTEXTMSGFOLDER."options.php");
require_once(WPTEXTMSGFOLDER."queue.php");
require_once(WPTEXTMSGFOLDER."database.php");
require_once(WPTEXTMSGFOLDER."support.php");
require_once(WPTEXTMSGFOLDER."functions.php");
require_once(WPTEXTMSGFOLDER."carrier.php");
require_once(WPTEXTMSGFOLDER."subscribers.php");
require_once(WPTEXTMSGFOLDER."update.php");
require_once(WPTEXTMSGFOLDER."widget-layout.php");
require_once(WPTEXTMSGFOLDER."widget.php");
require_once(WPTEXTMSGFOLDER."widget-unsubscribe.php");
require_once(WPTEXTMSGFOLDER."shortcode.php");
require_once(WPTEXTMSGFOLDER."cron.php");

add_action('init'				, "mrt_session",1);
add_action("plugins_loaded"		, "mrt_sms_widget_init");
add_action('init'				, "mrt_sms_input_data");
add_action('wp_enqueue_scripts'	, "mrt_register_js");
add_action('wp_print_styles'	, "mrt_register_css");
add_action('wp_footer'			, "mrt_footer");
add_action('admin_notices'		, "mrt_sms_notification");
add_action('admin_head'			, 'mrt_sms_admin_head');
add_action('admin_menu'			, 'mrt_add_menu');
add_action('widgets_init'		, 'mrt_widget' );

add_shortcode( 'wp-text-message-register'		,'mrt_register_shortcode' );
add_shortcode( 'wp-text-message-unsubscribed'	,'mrt_unsubscribe_shortcode' );


register_activation_hook(__FILE__,'mrt_sms_install');

$send_msg = get_option("mrt_sms_is_send_msg" );

if($send_msg == 'on') :
    //add_action("publish_post", "mrt_sms_insert_queue");
    add_action("publish_post"			, "mrt_update_post");
	//add_action("edit_post"				, "mrt_update_post");
	add_action('transition_post_status'	, 'mrt_publish_post', 10, 3); 
endif;

// define default function
add_option('mrt_sms_ll_wdgt', '1');

// ========================================================================	//
// == function name : mrt_session 										 ==	//
// == return		: void												 == //
// == function		: check if session is available or not				 == //
// ==				  if not, will start a session						 == //
// ========================================================================	//

function mrt_session()
{
	if(!session_id()) :
		session_start();
	endif;	
}

// ========================================================================	//
// == function name : mrt_update_post									 ==	//
// == return		: void												 == //
// ========================================================================	//

function mrt_update_post()
{
	global $post;
	
	if(!isset($_POST['autosave']) || $_POST['autosave'] == '0') :
			
		// publish status
		if($_POST['original_publish'] == 'Publish' && $_POST['post_status'] == 'publish') :
			
			$args	= array(
				'post_id'	=> $post->ID,
				'subject'	=> '',
				'message'	=> '',
				'type'		=> 'post'
			);
			
			mrt_sms_insert_queue($args);
			
		// update status
		elseif($_POST['original_publish'] == 'Update' && $_POST['post_status'] == 'publish') :
		
			$postids	= explode(',',str_replace(' ','',get_option('mrt_sms_send_update')));
			
			if(sizeof($postids) > 0 && in_array($post->ID,$postids) && $_POST['original_publish'] == 'Update') :
				
				$args	= array(
					'post_id'	=> $post->ID,
					'subject'	=> '',
					'message'	=> '',
					'type'		=> 'post-update'
				);
				
				mrt_sms_insert_queue($args);			
				
			endif;
			
		endif;
	endif;
}

// ========================================================================	//
// == function name : mrt_publish_post									 ==	//
// == return		: void												 == //
// ========================================================================	//

function mrt_publish_post($new_status,$old_status,$post)
{
	$insert	= false;
	
	switch($old_status) :
	
		case "new"		:
		case "draft"	:
		case "future"	:
		case "pending"	:
		case "private"	: $insert	= ( $new_status == "publish" ) ? true : false;
						  break;
	
	endswitch;
	
	if($insert) :
		$args	= array(
					'post_id'	=> $post->ID,
					'subject'	=> '',
					'message'	=> '',
					'type'		=> 'post'
		);
			
		mrt_sms_insert_queue($args);
	endif;
}

function mrt_widget() 
{
	register_widget('wpTextMessageMobileRegisterWidget');
	register_widget('wpTextMessageMobileUnRegisterWidget');
}

function mrt_verify($action,$name)
{
	if(isset($_POST[$name]) && (wp_verify_nonce($_POST[$name],$action) && check_admin_referer($action))) :
		return true;
	endif;
	
	return false;
}

function mrt_sms_notification()
{
	if (current_user_can('manage_options')) :

		$batch	= get_option("mrt_sms_batch" );
		
		if(empty($batch) || is_null($batch)) :
			$link	= admin_url('admin.php?page=wptext-message-options');
			?>
            <div class="error message" style="padding:5px;">
            WordPress Text Message is not setup.  Please <a href="<?php echo $link; ?>">CLICK HERE</a> to visit the options page to enter how many users to send out per batch
            </div>
            <?php
		endif;
	endif;
	
	if(isset($_REQUEST['wp-message'])) :
	
		switch($_REQUEST['wp-message']) :
		
			case 1	: ?><div class="updated message"><p>The message has been inserted into queue</p></div><?php
					  break;
						  
			case 2	: ?><div class="error message"><p><strong>Subject</strong> and <strong>message</strong> must not be empty</p></div><?php
					  break;	
					  
			case 3	: ?><div class="updated message"><p>Options updated</p></div><?php
					  break;
					  
			case 4	: ?><div class="updated message"><p>The queue deleted</p></div><?php
					  break;
					  
			case 5	: ?><div class="updated message"><p>The subscriber deleted</p></div><?php
					  break;
					  
			case 6	: ?><div class="updated message"><p>The subscriber added</p></div><?php
					  break;	
			
			case 7	: ?><div class="updated message"><p>The new carrier added</p></div><?php
					  break;
					  
			case 8	: ?><div class="updated message"><p>The carrier updated</p></div><?php
					  break;					  
					  
			case 9	: ?><div class="updated message"><p>The carrier deleted</p></div><?php
					  break;
		
		endswitch;
	
	endif;
}

function mrt_register_js()
{
	wp_register_script('jquery.nyromodal',	WPTEXTMSGURL.'js/jquery.nyroModal.js',array('jquery'));
	wp_register_script('mrt-script'		 ,	WPTEXTMSGURL.'js/script.js',array('jquery'));
	
	wp_enqueue_script('jquery');	
	wp_enqueue_script('jquery.nyromodal');
	wp_enqueue_script('mrt-script');
}

function mrt_register_css()
{
	wp_register_style('jquery.nyromodal', WPTEXTMSGURL.'css/jquery.nyroModal.css');
	wp_register_style('mrt-style'		, WPTEXTMSGURL.'css/style.css');
	
	wp_enqueue_style('jquery.nyromodal');
	wp_enqueue_style('mrt-style');
}

function mrt_footer()
{
	?>
    <script type="text/javascript" language="javascript1.2">
	jQuery(document).ready(function(){
		jQuery('a.mrtUnsubscribeLink').click(function(e){
		
			e.preventDefault();
				
			var ajaxurl	= "<?php echo admin_url('admin-ajax.php'); ?>";
			var data 	= {
							action		: 'mrt-unsubscribed-form',
						  };
									  
			jQuery.post(ajaxurl, data, function(response) {
				jQuery.nmData(response,{
					sizes	: {
								initW	: 500,
								initH	: 400,
								w		: 500,
								h		: 400,
								minW	: 500,
								minH	: 400	
					}
				})
			});
						
			return false;
		});
	});
	</script>
    <?php	
}

function mrt_sms_admin_head()
{
	?>
	<script src="<?php echo(WPTEXTMSGURL); ?>scripts.js"></script>
	<script src="<?php echo(WPTEXTMSGURL); ?>sorttable.js"></script>
	<?php

}

function mrt_add_menu() 
{
   add_menu_page('Wordpress Text Message'				, 'Text Message', 8, 'wptext-message', 'mrt_sms_main_control',WPTEXTMSGURL.'phone.png');
   add_submenu_page('wptext-message', 'Carrier'			, 'Carrier', 8, 'wptext-message-carrier', 'mrt_sms_carrier_page');
   add_submenu_page('wptext-message', 'Options'			, 'Options', 8, 'wptext-message-options', 'mrt_sms_options_page');
   add_submenu_page('wptext-message', 'Queue'			, 'Queue', 8, 'wptext-message-queue', 'mrt_sms_queue_page');
   add_submenu_page('wptext-message', 'Subscribers'		, 'Subscribers', 8, 'wptext-message-subscribers', 'mrt_sms_subscribers_page');
   add_submenu_page('wptext-message', 'Add Subscribers'	, 'Add Subscribers', 8, 'wptext-message-add-subscriber', 'mrt_sms_add_subscribe');
   add_submenu_page('wptext-message', 'Support'			, 'Support'	, 8, 'wptext-message-support', 'mrt_sms_support_page');
}


function mrt_sms_send_email($args)
{	mrt_send_msg($args);    }

function mrt_sms_send($queue)
{
    global $wpdb,$mrt_debug;
	$batch	= $queue['batch_no'];
	
    $result = mrt_sms_get_sub_queue($batch);
	$mrt_debug	.= "Batch No ".$batch;
	
	if(sizeof($result) > 0 ) :
    foreach ($result as $results) :
	
		$sendmail	= false;
        $sendnum 	= $results->number;
        $sentcar 	= $results->carrier;
		
		
		$carrier	= mrt_sms_get_a_carrier($sentcar,'carrier');
		$carsuf		= '@'.$carrier['url'];
		
		/*
        switch ($sentcar) :

            case "metropcs"	: $carsuf = "@mymetropcs.com"; 			break;
            case "verizon"	: $carsuf = "@vtext.com";				break;
            case "tmobile"	: $carsuf = "@tmomail.net";				break;
            case "vmobile"	: $carsuf = "@vmobl.com";				break;
            case "cingular"	: $carsuf = "@cingularme.com";  		break;
            case "nextel"	: $carsuf = "@messaging.nextel.com";	break;
            case "alltel"	: $carsuf = "@message.alltel.com";		break;
            case "sprint"	: $carsuf = "@messaging.sprintpcs.com";	break;
            case "attmob"	: $carsuf = "@txt.att.net";				break;
            case "attwire"	: $carsuf = "@mobile.att.net";			break;
            case "uscell"	: $carsuf = "@email.uscc.net";			break;
			default			: $carsuf = $sentcar; break;
		
		endswitch;
		*/

        $mrt_all_from 	= get_option( "mrt_sms_from" );

        if($queue['type'] == 'post') :
		
            $post_title 	= get_the_title($queue['post_id']);
            $site_url		= get_bloginfo('url');
			$post_url		= get_permalink($queue['post_id']);
            $body 			= strip_tags('A new post (' .$post_title .') has been published on (' .$post_url. ')');
            //$body 			= 'A new post (' .$post_title .') has been published on (' .$post_url. ')';
            $subject 		= 'A new post has been created';
			$sendmail		= true;
			
		elseif($queue['type'] == 'post-update') :

            $post_title 	= get_the_title($queue['post_id']);
            $site_url		= get_bloginfo('url');
			$post_url		= get_permalink($queue['post_id']);
            //$body 			= 'A Post (' .$post_title .') has been updated on (' .$post_url. ')';
            $body 			= strip_tags('A Post (' .$post_title .') has been updated on (' .$post_url. ')');
            $subject 		= 'A Post has been updated';
			$sendmail		= true;
			
		elseif(isset($queue['subject']) && isset($queue['message']) && !empty($queue['subject']) && !empty($queue['message'])) :
		
			$subject		= $queue['subject'];
			$body			= $queue['message'];
			$sendmail		= true;
			
        endif;
		
		$sendmail 	= true;
		
        $to 		= $sendnum . $carsuf;
		$from_email	= $mrt_all_from;
		$from_name	= get_bloginfo('name');
		$charset	= 'iso-8859-1';
		
        $headers	= '';
		$headers 	.= 'Reply-To: ' . $from_email . "\r\n";
					  
		apply_filters('wp_mail_from', create_function('$i', 'return $from_email;'), 1, 100);
		apply_filters('wp_mail_from_name',create_function('$i', 'return $from_name;'), 1, 100);
		
		$headers .= 'From: ' . $from_name . ' <' . $from_email . ">\r\n";
		$headers .= 'Content-type: text/html; charset=' . $charset . "\r\n";
					
		apply_filters('wp_mail_content_type', create_function('$i', 'return "text/html";'), 1, 100);
		apply_filters('wp_mail_charset', create_function('$i', 'return $charset;'), 1, 100);
					  
		/* for testing */
		/*
		$log_file	= WPTEXTMSGFOLDER."log.txt";
		
		if(file_exists($log_file)) :
		
			$fh 	= fopen($log_file, 'a') or die("can't open file");
			$text 	= "Send to ".$sendnum.' '.$carsuf.'\r\n';
			fwrite($fh, $text);
			fclose($fh);
			
			$mrt_debug	.= "<br /> Send to : ".$queue['id'].' '.$sendnum.' '.$carsuf." ".$subject.$body;
			
		else :
			$fh 	= fopen($log_file, 'w') or die("can't open file");
			$fh 	= fopen($log_file, 'a') or die("can't open file");
			$text 	= "Send to ".$sendnum.$carsuf.'\r\n';
			fwrite($fh, $text);
			fclose($fh);
		endif;
		*/

		if(!function_exists('wp_mail')) :
			require_once(ABSPATH.'wp-includes/pluggable.php');
		endif;
		
        if (is_bool($sendmail) && $sendmail && wp_mail($to, $subject, $body, $headers)) :
            $succfail = "<font color='green'>Message successfully sent to " . $sendmail." ".$to . "</font><br />";
		else :
            $succfail = "<font color='red'>Message delivery failed to [" . $sendmail."] ".$to . "</font><br />";
        endif;
		
    endforeach;
	endif;
	
}

function mrt_send_msg($args = null)
{
    global $wpdb, $mrt_submitted;
	
    $table_name = $wpdb->prefix . "mrt_sms_list";
    $results 	= $wpdb->get_results("SELECT number, carrier FROM " . $table_name);
	
	if(sizeof($results) > 0) :
    foreach ($results as $result) :
	
		$send	= false;
		
	
        $sendnum = $result->number;
        $sentcar = $result->carrier;

		$carrier	= mrt_sms_get_a_carrier($sentcar,'carrier');
		$carsuf		= '@'.$carrier['url'];

        $mrt_all_from 	= get_option( "mrt_sms_from" );

        $body 			= $_POST['message'];
        $subject 		= $_POST['subject'];
		
        if($body == '' && $subject == '' && !is_null($args)) :
		
            $post_title 	= get_the_title($args);
            $site_url		= get_bloginfo('siteurl');
            $body 			= 'A new post <' .$post_title .'> has been created/updated on <' .$site_url. '>';
            $subject 		= 'A new post has been created';
			
        endif;
		
        $to 		= $sendnum . $carsuf;
        $headers	= 'From: ' . $mrt_all_from . "\r\n" .
            	   	  'Reply-To: ' . $mrt_all_from . "\r\n" .
            		  'X-Mailer: PHP/';

        if (mail($to, $subject, $body, $headers)) :
            $succfail = $succfail . "<font color='green'>Message successfully sent to " . $sendnum . "</font><br />";
		else :
            $succfail = $succfail . "<font color='red'>Message delivery failed to " . $sendnum . "</font><br />";
        endif;
		
    endforeach;
	endif;
}

function mrt_sms_input_data()
{
	global $mrt_submitted;
	
	if( $_POST['number'] != '' && $_POST['carrier'] != '') :
	
		$mrt_sms_number = $_POST['number'];
		$mrt_sms_carrier = $_POST['carrier'];
	
		$mrt_sms_number = ereg_replace("[^0-9]", "", $mrt_sms_number);
		$mrt_len = strlen($mrt_sms_number);
		$mrt_sub_date = date('l, jFY h:i:s');
	
		if ($mrt_len == 10) :
			global $wpdb;
			$table_name = $wpdb->prefix . "mrt_sms_list";
			$insert 	= "INSERT INTO " . $table_name .
						  "(number, carrier, date) " .
						  "VALUES ('" . $wpdb->escape($mrt_sms_number) . "','" . $wpdb->escape($mrt_sms_carrier) . "','" . $mrt_sub_date . "')";
	
			$results = $wpdb->query($wpdb->prepare( $insert ));
			$mrt_submitted = "Success! Thank you for subscribing.";
	
		elseif($mrt_len > 10) :
			$mrt_submitted = "<font color='red'>Too many digits.  Please enter only 10 digits for your phone number</font>";
		elseif($mrt_len < 10) :
			$mrt_submitted = "<font color='red'>Not enough digits.  Please enter a 10 digit phone number</font>";
		endif;
		
	endif;
}

function mrt_sms_guts_widget()
{  
	//echo "<h2>Register for SMS Updates</h2>";
	global $mrt_submitted,$mrt_sms_ll;
	
	echo $mrt_submitted;

	//testv();
	?>
    <form name='mrt_sub_form' id='mrt_sub_form' method='POST' action='<?php echo "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . "?" . $_SERVER['QUERY_STRING']; ?>'>
		Phone number<br />
        <input name="number" type="text" /><br />
        <br />
		Carrier<br />
        <select name="carrier">
			<option value="metropcs">Metro PCS</option>
			<option value="verizon">Verizon</option>
			<option value="tmobile">T-Mobile</option>
			<option value="vmobile">Virgin Mobile</option>
			<option value="cingular">Cingular(GoPhone)</option>

			<option value="nextel">Nextel</option>
			<option value="alltel">Alltel</option>
			<option value="sprint">Sprint</option>
			<option value="attmob">AT&amp;T Mobility(Cingular)</option>
			<option value="attwire">AT&amp;T Wireless</option>
			<option value="uscell">US Cellular</option>
		</select>
        <br /><br />
		<?php echo $mrt_sms_ll; ?>
	</form>
	<?php 
}

function mrt_debug($var)
{
	?><pre><?php
	print_r($var);
	?></pre><?php	
}

?>
