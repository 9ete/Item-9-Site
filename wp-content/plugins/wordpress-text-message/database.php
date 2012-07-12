<?php

$mrt_sms_db_version = "2.4";

function mrt_sms_get_total_sub()
{
	global $wpdb;
	
   	$table_name = $wpdb->prefix ."mrt_sms_list";
	
	$query		= "SELECT COUNT(*) as total_sub ".
			      "FROM ".$table_name." ";
				  
	return $wpdb->get_var($query);
}


function mrt_sms_insert_queue($args = NULL)
{
	global $wpdb;

	$val	= array(
		'time'		=> time(),
		'action'	=> "",
		'post_id'	=> $args['post_id'],
		'subject'	=> $args['subject'],
		'message'	=> $args['message'],
		'type'		=> $args['type'],
		'batch_no'	=> 0
	);
	
	$table_name	= $wpdb->prefix."mrt_sms_queue";
	
	$sql		= "INSERT INTO ".$table_name." VALUES ('',".
					"'".$val['time']."',".
					"'".$val['action']."',".
					"'".$val['post_id']."',".
					"'".$val['subject']."',".
					"'".$val['message']."',".
					"'".$val['type']."',".
					"'".$val['batch_no']."'".
				  ")";

	if((!is_null($args) && !is_array($args) && $_POST['post_type'] == 'post') || is_array($args)) :
		$wpdb->query($sql);
	endif;
}

function mrt_sms_install () 
{
   	global $wpdb,$mrt_sms_db_version;
	$mrt_sms_db_version = "2.4";
	
   	$table_name 		= $wpdb->prefix ."mrt_sms_list";
	$table_queue_list	= $wpdb->prefix ."mrt_sms_queue";
	
   	if($wpdb->get_var("show tables like '$table_name'") != $table_name) :
	
     	update_option('mrt_sms_header',"Wordpress Text Message"); 
     	update_option('mrt_sms_footer',"*Standard texting rates apply*");
     	update_option('mrt_sms_from',"test@yoursite.com");
      	
		$sql = "CREATE TABLE " . $table_name . " ( ".
	  				"id mediumint(9) NOT NULL AUTO_INCREMENT, ".
	  				"number text NOT NULL, ".
          			"carrier text NOT NULL, ".
	  				"mrt_frm VARCHAR(100) NOT NULL, ".
         			"date VARCHAR(100) NOT NULL, ".
          			"UNIQUE KEY id (id) ".
			   ");";

      	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

      	dbDelta($sql);
 
		add_option("mrt_sms_db_version", $mrt_sms_db_version);

   	endif;
	
	if($wpdb->get_var("show tables like '$table_queue_list'") != $table_queue_list) :
		
		$sql = "CREATE TABLE " . $table_queue_list . " ( ".
					"`id` int(11) NOT NULL AUTO_INCREMENT,".
					"`time` int NOT NULL, ".
					"`action` text NOT NULL,".
 					"`post_id` int(11) NOT NULL,".
					"`subject` varchar(23) NOT NULL,".
					"`message` text NOT NULL,".
					"`type` varchar(255) NOT NULL, ".
					"`batch_no` INT(11) NOT NULL, ".
  					"PRIMARY KEY (`id`) ".
			   ") ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
			   
			   
      	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
				   
		dbDelta($sql);
		
	endif;

	add_option('mrt_sms_max','150');

	$installed_ver = get_option( "mrt_sms_db_version" );
	
   	if( $installed_ver != $mrt_sms_db_version ) :
    	$sql= "CREATE TABLE " . $table_name . " (".
          		"id mediumint(9) NOT NULL AUTO_INCREMENT,".
          		"number text NOT NULL,".
          		"carrier text NOT NULL,".
          		"mrt_frm VARCHAR(100) NOT NULL,".
          		"date VARCHAR(100) NOT NULL,".
          		"UNIQUE KEY id (id)".
		      ");";

      	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	  
      	dbDelta($sql);

      	update_option( "mrt_sms_db_version", $mrt_sms_db_version );
      	update_option('mrt_sms_header',"Wordpress Text Message");
      	update_option('mrt_sms_footer',"*Standard texting rates apply*");
      	update_option('mrt_sms_from',"test@yoursite.com");
		
		if($wpdb->get_var("show tables like '$table_queue_list'") != $table_queue_list) :
		
			$sql = "CREATE TABLE " . $table_queue_list . " ( ".
						"`id` int(11) NOT NULL AUTO_INCREMENT,".
  						"`time` time NOT NULL, ".
						"`action` text NOT NULL,".
  						"`post_id` int(11) NOT NULL,".
  						"PRIMARY KEY (`id`) ".
				   ") ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
				   
			dbDelta($sql);
		
		endif;
		
	endif;
}
?>
