<?php

add_action('init','mrt_subscriber_processing');
// ajax action
add_action('wp_ajax_nopriv_register_mobile_phone'	,'mrt_register_phone');
add_action('wp_ajax_nopriv_unregister_mobile_phone'	,'mrt_unregister_phone');
add_action('wp_ajax_register_mobile_phone'			,'mrt_register_phone');
add_action('wp_ajax_unregister_mobile_phone'		,'mrt_unregister_phone');


/* ================================================================	*/
/* ==						MRT REGISTER PHONE					==	*/
/* ================================================================	*/

function mrt_register_phone()
{
	$success	= true;
	$message	= "<ul>";
	$widget_id	= end(explode('-',$_POST['widget_id']));
	$data		= $_POST['widget-mobile-register-widget'][$widget_id];
	
	if(sizeof($data) > 0) :
	foreach($data as $key => $value) :
		
		switch($key) :
			
			case "number"	: if(empty($value) || is_null($value)) :
							  	$message .= "<li>The number field is empty</li>";
							  	$success	= false;
							  elseif(!is_numeric($value)) :
							  	$message .= "<li>Invalid number</li>";
								$success	= false;
							  elseif(strlen($value) != 10) :
							  	$message .= "<li>Please input 10 chars</li>";
								$success	= false;
							  endif;
					  		  break;
							
			case "captcha" 	: if(empty($value)) :
							  	$message .= "<li>The captcha field is empty</li>";
							  	$success	= false;
							  else :
							  
							  	$cap1	= $_SESSION[$_POST['widget_id']]['captcha_1'];
								$cap2	= $_SESSION[$_POST['widget_id']]['captcha_2'];	
								
								if($value <> $cap1 + $cap2) :
									$success	= false;
									
									$message	.= "<li>wrong/old captcha</li>";
								endif;
								
							  endif;
					  		  break;
			
		endswitch;
	
	
	endforeach;
	endif;
	
	if($success == true) :
	
		$check	= mrt_check_exists_subscriber($data);
		
		if(!$check) :
			
			mrt_subscriber_insert($data);
			
			$message	.= "<li>You have subscribed, thank you!</li>";
			$message	= "<div class='success'>".$message."</div>1";
			unset($_SESSION[$_POST['widget_id']]);
		else :
			$message	.= "<li>Your number is registered already</li>";
			$message	= "<div class='error'>".$message."</div>0";
		endif;
	else :
		$message	= "<div class='error'>".$message."</div>0";
	endif;
	
	echo($message);
	
	exit();
}

/* ================================================================	*/
/* ==						MRT UNREGISTER PHONE				==	*/
/* ================================================================	*/

function mrt_unregister_phone()
{
	$success	= true;
	$message	= "<ul>";
	$widget_id	= end(explode('-',$_POST['widget_id']));
	$data		= $_POST['widget-mobile-unsubscribe-widget'][$widget_id];
	
	if(sizeof($data) > 0) :
	foreach($data as $key => $value) :
		
		switch($key) :
			
			case "number"	: if(empty($value) || is_null($value)) :
							  	$message .= "<li>The phone number field is empty</li>";
							  	$success	= false;
							  else :
								$data['number']	= mrt_check_exists_number($value);
								if(!$data['number']) :
									$message	.= "<li>The number doesn't exist</li>";
									$success	= false;
								endif;
							  	
							  endif;
					  		  break;
							
			case "captcha" 	: if(empty($value)) :
							  	$message .= "<li>The captcha field is empty</li>";
							  	$success	= false;
							  else :
							  
							  	$cap1	= $_SESSION[$_POST['widget_id']]['captcha_1'];
								$cap2	= $_SESSION[$_POST['widget_id']]['captcha_2'];
								
								if($value <> $cap1 + $cap2) :
									$success	= false;
									
									$message	.= "<li>wrong/old captcha</li>";
								endif;
								
							  endif;
					  		  break;
			
		endswitch;
	
	
	endforeach;
	endif;
	
	if($success == true) :
		
		mrt_subscrbier_delete_by_number($data['number']);
		
		$message	.= "<li>Your phone number is already unsubscribed. Thank You</li>";
		$message	= "<div class='success'>".$message."</div>1";
		
		unset($_SESSION[$_POST['widget_id']]);

	else :
		$message	= "<div class='error'>".$message."</div>0";
	endif;
	
	echo($message);
	exit();	
}

/* ============================================================	*/
/* ==					CHECK VALID CARRIER					==	*/
/* ============================================================	*/

function mrt_check_valid_carrier($url)
{	
	global $wpdb;
	
	$query	= "SELECT carrier ".
			  "FROM ".$wpdb->prefix."mrt_sms_carrier ".
			  "WHERE url = '".$url."' ";
			  
	$carrier= $wpdb->get_var($query);
	
	if(empty($carrier) || is_null($carrier)) :
		return false;
	endif;
	
	return $carrier;
}

function mrt_check_exists_number($number)
{
	global $wpdb;

	$table	= $wpdb->prefix."mrt_sms_list";
	
	$query	= "SELECT * FROM ".$table." ".
			  "WHERE number = '".$number."' ";
			  
	$result	= $wpdb->get_row($query);
	
	if(is_object($result)) :
		return $result->number;
	endif;
			  
	return false;	
}

function mrt_check_exists_subscriber($data)
{
	global $wpdb;

	$table	= $wpdb->prefix."mrt_sms_list";
	
	$query	= "SELECT * FROM ".$table." ".
			  "WHERE number = '".$data['number']."' ".
			  "AND carrier = '".$data['carrier']."'";
			  
	return $wpdb->get_row($query);
			   
}


function mrt_subscriber_processing()
{
	global $wpdb;	

	if(isset($_REQUEST['page']) && $_REQUEST['page'] == 'wptext-message-subscribers' && 
	   isset($_REQUEST['action']) && $_REQUEST['action'] == 'delete' && 
	   isset($_REQUEST['id']) && !is_null($_REQUEST['id']) && 
	   isset($_REQUEST['_wpnonce']) && wp_verify_nonce($_REQUEST['_wpnonce'],'wptext-delete-subscriber')) :
	
		mrt_subscriber_delete($_REQUEST['id']);
		
		$admin_url	= admin_url('admin.php?page=wptext-message-subscribers&wp-message=5');
		wp_redirect($admin_url);
		
	elseif(isset($_POST['subscriber']) &&
			isset($_POST['subscriber']['number']) && $_POST['subscriber']['number'] <> '' && 
			isset($_POST['subscriber']['carrier']) && $_POST['subscriber']['carrier'] <> '' &&
			mrt_verify('wptext-add-subscriber','_wpnonce')) :
			
		$args	= array(
			'number'	=> $_POST['subscriber']['number'],
			'carrier'	=> $_POST['subscriber']['carrier']
		);
			
		mrt_subscriber_insert($args);
		
		$admin_url	= admin_url('admin.php?page=wptext-message-add-subscriber&wp-message=6');
		wp_redirect($admin_url);
			  
	endif;
}

function mrt_subscriber_delete($id)
{
	global $wpdb;
	
	$table	= $wpdb->prefix."mrt_sms_list";
	$query	= "DELETE FROM ".$table." ".
			  "WHERE id = ".$id."; ";
				  
	$wpdb->query($query);
}

function mrt_subscrbier_delete_by_phone($number,$carrier)
{
	global $wpdb;
	
	$table	= $wpdb->prefix."mrt_sms_list";
	$query	= "DELETE FROM ".$table." ".
			  "WHERE number = '".$number."' AND carrier = '".$carrier."' ; ";
				  
	$wpdb->query($query);
}

function mrt_subscrbier_delete_by_number($number)
{
	global $wpdb;
	
	$table	= $wpdb->prefix."mrt_sms_list";
	$query	= "DELETE FROM ".$table." ".
			  "WHERE number = '".$number."' ; ";
				  
	$wpdb->query($query);	
}

function mrt_subscriber_insert($args)
{
	global $wpdb;
	
	$table	= $wpdb->prefix."mrt_sms_list";
	$query	= "INSERT INTO ".$table." VALUES('','".$args['number']."','".$args['carrier']."','','".date("l, d F Y H:i:s")."');";
				  
	$wpdb->query($query);
}

function mrt_sms_subscribers_page() 
{ 
	global $wpdb;
	?>
    <br /><em>For comments, suggestions, bug reporting, etc please <a href="http://www.totalbounty.com/forums/topic/wordpress-text-message/">use our official forum page for the plugin</a>.</em>
	
	<div class="wrap">
		<h2>Wordpress Text Message Subscribers</h2>
		<em>Click on a header to sort.</em><br />
		<table class="sortable widefat" cellspacing="0">
			<thead>
			<tr>
				<th scope="col" >ID</th>
				<th scope="col" >Phone Number</th>
				<th scope="col" >Carrier</th>
				<th scope="col" >Submit Date</th>
				<th scope="col" >Action</th>
			</tr>
			</thead>
			<tbody>
	<?php

   $table_name = $wpdb->prefix . "mrt_sms_list";
   $result = $wpdb->get_results("SELECT * FROM " . $table_name);

	if($result) :

 	foreach ($result as $results) :
    	
		$tablenum = $results->number;
      	$tablecar = $results->carrier;
      	$tabledate = $results->date;
      	$tableid = $results->id;

		$delete_url	= wp_nonce_url(admin_url('admin.php?page=wptext-message-subscribers&action=delete&id='.$tableid),'wptext-delete-subscriber');
	?>
	<tr onmouseover="this.style.backgroundColor='lightblue';" onmouseout="this.style.backgroundColor='white';">
		<td><?php echo $tableid; ?></td>
		<td><?php echo $tablenum; ?></td>
		<td><?php echo $tablecar; ?></td>
		<td><?php echo $tabledate; ?></td>
		<td>
        	<a href="<?php echo $delete_url ?>" onclick="javascript:check=confirm( '<?php echo "Delete this subscriber?"?>');if(check==false) return false;"><?php _e('Delete') ?></a>
        </td>
	</tr>
	<?php  
	endforeach;
	
	else :
	?><tr><td colspan="7" align="center"><strong>No entries found</strong></td></tr><?php
	endif;
	?>
			</tbody>
		</table>
	</div>
		
   	<div style="margin-top:50px">
		Wordpress Text Message Plugin enhanced by <a href="http://www.jtpratt.com/" title="JTPratt Media">JTPratt Media</a>, now sponsored by <a href="http://www.totalbounty.com">Total Bounty Marketplace</a><br />
     	Visit the <a href="http://www.totalbounty.com/freebies/wordpress-text-message/">Wordpress Text Message Plugin Page</a><br />
      	Based on the Original SMS Text Message Plugin by <a href="http://semperfiwebdesign.com/" title="Semper Fi Web Design">Semper Fi Web Design</a>
	</div>

<?php 
} 

function mrt_sms_add_subscribe() 
{
	?>
    <h1> Add Subscribers </h1>

	<p>
    	You can manually add subscribers to your SMS text list<br />
		here one by one.  This feature would be used if you were<br />
		collecting cell phone numbers as part of an opt-in giveaway<br />
		or contest where people won't be physically visiting your<br />
		website - or other methods of obtaining opt-in leads who<br />
		want updates from your site but didn't enter the number<br />
		themselves using the front end widget.
	</p>

    <form name='mrt_sub_form' id='mrt_sub_form' method='POST' action=''>
    
		Phone number<br />
        
        <input name="subscriber[number]" type="text" /><br />
        
        <br />
		Carrier<br />
        <select name="subscriber[carrier]">
			<?php mrt_sms_carrier_echo_options(); ?>
		</select>
        <br /><br />
		
        
        <p>
        	<?php wp_nonce_field('wptext-add-subscriber'); ?>
        	<input type="submit" name="action" value="Add Subscriber" />
        </p>
	</form>

	<?php
    }
?>
