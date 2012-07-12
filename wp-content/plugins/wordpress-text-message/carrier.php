<?php
add_action('init','mrt_check_database_carrier');
add_action('init','mrt_save_carrier');
add_action('init','mrt_update_carrier');
add_action('init','mrt_delete_carrier');

/*******************************************************/
/**					CHECK DATABASE CARRIER			  **/
/*******************************************************/

function mrt_check_database_carrier()
{
	global $wpdb;
	$table_name	= $wpdb->prefix."mrt_sms_carrier";	
	
	if($wpdb->get_var("SHOW TABLES LIKE '".$table_name."'") != $table_name) :
	
		$query	= "CREATE TABLE IF NOT EXISTS `".$table_name."` (".
  					"`id` int(11) NOT NULL AUTO_INCREMENT,".
					"`label` VARCHAR( 255 ) NOT NULL,".
  					"`carrier` varchar(255) NOT NULL,".
  					"`url` text NOT NULL,".
  					"PRIMARY KEY (`id`)".
					") ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
					
		$wpdb->query($query);
		
		$query	= "INSERT INTO `".$table_name."` (`id`,`label`,`carrier`,`url`) ".
				  "VALUES ( NULL , 'Metro PCS','metropcs', 'mymetropcs.com'), ".
				  		 "( NULL , 'Verizon','verizon','vtext.com' ), ".
						 "( NULL , 'T-Mobile','tmobile','tmomail.net' ), ".
						 "( NULL , 'Virgin Mobile','vmobile','vmobl.com' ), ".
						 "( NULL , 'Cingular(GoPhone)','cingular','cingularme.com' ), ".
						 "( NULL , 'Nextel','nextel','messaging.nextel.com' ), ".
						 "( NULL , 'Alltel','alltel','message.alltel.com' ), ".
						 "( NULL , 'Sprint','sprint','messaging.sprintpcs.com' ), ".
						 "( NULL , 'AT&amp;T Mobility(Cingular)','attmob','txt.att.net' ), ".
						 "( NULL , 'AT&amp;T Wireless','attwire','mobile.att.net' ), ".
						 "( NULL , 'US Cellular','uscell','email.uscc.net' );";
						 
		$wpdb->query($query);
	
	endif;
}

/*******************************************************/
/**						CARRIER SAVE			  	  **/
/*******************************************************/

function mrt_save_carrier()
{
	global $wpdb;
	$table_name	= $wpdb->prefix."mrt_sms_carrier";	
	
	if(isset($_POST) && isset($_POST['mrt_save_carrier_nonce'])) :
	
		if(wp_verify_nonce($_POST['mrt_save_carrier_nonce'], 'mrt-save-carrier') ) :
		
			$query	= "INSERT INTO `".$table_name."` ".
					  "VALUES('','".$_POST['carrier']['label']."','".$_POST['carrier']['name']."','".$_POST['carrier']['url']."');";
					  
			$wpdb->query($query);
			
			$link	= admin_url('admin.php?page=wptext-message-carrier&wp-message=7');
			wp_redirect($link);
		
		endif;
	
	endif;
}

/*******************************************************/
/**						CARRIER UPDATE			  	  **/
/*******************************************************/

function mrt_update_carrier()
{
	global $wpdb;
	$table_name	= $wpdb->prefix."mrt_sms_carrier";	
	
	if(isset($_POST) && isset($_POST['mrt_update_carrier_nonce'])) :
	
		if(wp_verify_nonce($_POST['mrt_update_carrier_nonce'], 'mrt-update-carrier') ) :
		
			$query	= "UPDATE `".$table_name."` ".
					  "SET label = '".$_POST['carrier']['label']."', carrier = '".$_POST['carrier']['name']."' , url = '".$_POST['carrier']['url']."' ".
					  "WHERE id = '".$_POST['carrier']['id']."' ;";
					  
			$wpdb->query($query);
					  
			$link	= admin_url('admin.php?page=wptext-message-carrier&wp-message=8');
			wp_redirect($link);
		
		endif;
	
	endif;
}

/*******************************************************/
/**						CARRIER DELETE			  	  **/
/*******************************************************/

function mrt_delete_carrier()
{
	global $wpdb;
	$table_name	= $wpdb->prefix."mrt_sms_carrier";	
	
	if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'delete' && wp_verify_nonce($_REQUEST['_wpnonce'], 'wptext-delete-carrier') ) :
		
		$query	= "DELETE FROM `".$table_name."` ".
				  "WHERE id = '".$_REQUEST['id']."' ;";
					  
		$wpdb->query($query);
					  
		$link	= admin_url('admin.php?page=wptext-message-carrier&wp-message=9');
		wp_redirect($link);
	
	endif;
}

/*******************************************************/
/**						GET A CARRIER			  	  **/
/*******************************************************/

function mrt_sms_get_a_carrier($value,$where = 'id')
{
	global $wpdb;
	
	$table_name	= $wpdb->prefix."mrt_sms_carrier";	
	
	$query	= "SELECT * FROM `".$table_name."` ".
			  "WHERE ".$where." = '".$value."' ";
			  
	return $wpdb->get_row($query,ARRAY_A);
	
}

/*******************************************************/
/**						CARRIER LIST			  	  **/
/*******************************************************/

function mrt_sms_carrier_list()
{
	global $wpdb;
	
	
	$table_name	= $wpdb->prefix."mrt_sms_carrier";	
	
	$query		= "SELECT * FROM ".$table_name." ".
				  "ORDER BY id DESC";
				  
	$result		= $wpdb->get_results($query,ARRAY_A);

	if(sizeof($result)) :
		$i = 1;
		foreach($result as $val) :
		?>
        <tr>
        	<td style="text-align:center"><?php echo $i; ?></td>
            <td style="text-align:left"><?php echo $val['label']; ?></td>
            <td style="text-align:left"><?php echo $val['carrier']; ?></td>
            <td style="text-align:left"><?php echo $val['url']; ?></td>
            <td style="text-align:center">
            	<?php 
					$edit_link		= wp_nonce_url(admin_url('admin.php?page=wptext-message-carrier&action=edit&id='.$val['id']),'wptext-edit-carrier') ;				
					$delete_link	= wp_nonce_url(admin_url('admin.php?page=wptext-message-carrier&action=delete&id='.$val['id']),'wptext-delete-carrier') ;
				?>
                <a href="<?php echo $edit_link; ?>">edit</a> &bull;
            	<a href="<?php echo $delete_link; ?>">delete</a>
            </td>
        </tr>
        <?php
			$i++;
		endforeach;
	endif;
}

/*******************************************************/
/**						CARRIER TABLE			  	  **/
/*******************************************************/

function mrt_sms_carrier_table()
{
	?>
	<div class="wrap">
      	<h2><?php _e('Wordpress Text Message Carrier') ?></h2>
        
		<table class="widefat" cellspacing="0">
		<thead>
			<tr>
				<th scope="col" width="5%" style="text-align:center">ID</th>
				<th scope="col" width="25%" style="text-align:center">Label</th>
				<th scope="col" width="25%" style="text-align:center">Carrier</th>
				<th scope="col" width="25%"  style="text-align:center">URL</th>
                <th scope="col"  style="text-align:center">Action</th>
			</tr>
		</thead>
        
		<tbody>
        <?php mrt_sms_carrier_list(); ?>
        </tbody>
        
        <tfoot>
			<tr>
			<tr>
				<th scope="col" width="5%" style="text-align:center">ID</th>
				<th scope="col" width="25%" style="text-align:center">Label</th>
				<th scope="col" width="25%" style="text-align:center">Carrier</th>
				<th scope="col" width="25%"  style="text-align:center">URL</th>
                <th scope="col"  style="text-align:center">Action</th>
			</tr>
			</tr>
        </tfoot>
        </table>
	</div>
    <?php
}

/*******************************************************/
/**					   CARRIER ADD FORM			  	  **/
/*******************************************************/

function mrt_sms_add_carrier_form()
{

	?>
	<div class="wrap">
    	<h2>Add New Carrier</h2>

    <br /><em>For comments, suggestions, bug reporting, etc please <a href="http://www.totalbounty.com/forums/topic/wordpress-text-message/">use our official forum page for the plugin</a>.</em>

<h3><a href="http://www.totalbounty.com/freebies/wordpress-text-message/">Get a List of Cell Carriers Worldwide Here</a></h3>

<h3>Add Carriers without the http://</h3>


        <form method="post" action="">
        
        	<div style="margin-top:20px;">
	    		<label style="display:inline-block;width:150px" for="carrier-name">Carrier Name</label>
    	        <input type="text" id="carrier-name" name="carrier[name]" value="" size="50"/>
            </div>
            
        	<div style="margin-top:20px;">
	    		<label style="display:inline-block;width:150px" for="carrier-label">Carrier Label</label>
    	        <input type="text" id="carrier-label" name="carrier[label]" value="" size="50"/>
            </div>
            
        	<div style="margin-top:20px;">
	    		<label style="display:inline-block;width:150px" for="carrier-url">Carrier URL</label>
    	        <input type="text" id="carrier-url" name="carrier[url]" value="" size="50"/>
            </div>
            
            <?php wp_nonce_field('mrt-save-carrier','mrt_save_carrier_nonce'); ?>
            <div class="submit" style="margin:20px 0;padding:0px"><input type="submit" value="Add Carrier" /></div>
            
        </form>
    </div>
	<?php    	
}

/*******************************************************/
/**					   CARRIER EDIT FORM			  **/
/*******************************************************/

function mrt_sms_edit_carrier_form()
{
	$carrier	= mrt_sms_get_a_carrier($_REQUEST['id']);
	
	?>
	<div class="wrap">
    	<h2>Edit Carrier</h2>

<h3>Add Carriers without the http://</h3>

        <form method="post" action="">
        	<div style="margin-top:20px;">
	    		<label style="display:inline-block;width:150px" for="carrier-name">Carrier Name</label>
    	        <input type="text" id="carrier-name" name="carrier[name]" value="<?php echo $carrier['carrier']; ?>" size="50"/>
            </div>
            
        	<div style="margin-top:20px;">
	    		<label style="display:inline-block;width:150px" for="carrier-label">Carrier Label</label>
    	        <input type="text" id="carrier-label" name="carrier[label]" value="<?php echo $carrier['label']; ?>" size="50"/>
            </div>
            
        	<div style="margin-top:20px;">
	    		<label style="display:inline-block;width:150px" for="carrier-url">Carrier URL</label>
    	        <input type="text" id="carrier-url" name="carrier[url]" value="<?php echo $carrier['url']; ?>" size="50"/>
            </div>
            
            <?php wp_nonce_field('mrt-update-carrier','mrt_update_carrier_nonce'); ?>
            
            <div class="submit" style="margin:20px 0;padding:0px">
            	<input type="hidden" name="carrier[id]" value="<?php echo $carrier['id']; ?>" />
            	<input type="submit" value="Update Carrier" />
                <a class="button" href="<?php echo admin_url('admin.php?page=wptext-message-carrier'); ?>">Cancel</a>
			</div>
            
        </form>
    </div>
	<?php    	
}

/*******************************************************/
/**					   CARRIER EDIT FORM		  	  **/
/*******************************************************/


/*******************************************************/
/**						CARRIER PAGE			  	  **/
/*******************************************************/

function mrt_sms_carrier_page()
{
	if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit' && wp_verify_nonce($_REQUEST['_wpnonce'], 'wptext-edit-carrier')) :
		mrt_sms_edit_carrier_form();	
	else :
		mrt_sms_add_carrier_form();	
	endif;
	
	mrt_sms_carrier_table();	
}

/*******************************************************/
/**						CARRIER OPTIONS			  	  **/
/*******************************************************/

function mrt_sms_carrier_options()
{
	global $wpdb;
	
	$table_name	= $wpdb->prefix."mrt_sms_carrier";	
	
	$query		= "SELECT * FROM `".$table_name."` ".
			  	  "ORDER BY carrier ASC";
				  
	$option_array	= array();
			  
	$results		= $wpdb->get_results($query,ARRAY_A);
	
	foreach($results as $option) :
		$option_array[$option['carrier']]	= $option['label'];
	endforeach;
	
	return $option_array;
}

function mrt_sms_carrier_echo_options()
{
	$options		= mrt_sms_carrier_options();
	$option_array	= array();
	
	foreach($options as $key => $value) :
		$option_array[]	= "<option value='".$key."'>".$value."</option>";
	endforeach;
	
	echo implode(' ',$option_array);
}

?>
