<?php

add_action('init','mrt_sms_delete_queue');

function mrt_sms_get_total_bacth()
{
	$mrt_sms_batch 	= get_option("mrt_sms_batch" );
	$total_sub		= mrt_sms_get_total_sub();
	return ceil($total_sub / $mrt_sms_batch);
}

function mrt_sms_get_sub_queue($batch)
{
	global $wpdb,$mrt_debug;
	
	$total	= get_option("mrt_sms_batch");
	$limit	= $batch * $total;
	
	$table	= $wpdb->prefix."mrt_sms_list";
	$query	= "SELECT * FROM ".$table." ".
			  "ORDER BY id ASC ".
			  "LIMIT ".$limit.",".$total." ";
	
	return $wpdb->get_results($query);			  
	
}

function mrt_sms_get_queue()
{
	global $wpdb,$mrt_debug;
	
	$batch	= mrt_sms_get_total_bacth();
	
	$table	= $wpdb->prefix."mrt_sms_queue";
	$query	= "SELECT * FROM ".$table." ".
			  "WHERE `batch_no` < ".$batch." ".
			  "ORDER BY id ASC ".
			  "LIMIT 0,1";

	return $queue	= $wpdb->get_row($query,ARRAY_A);
			  
}

function mrt_sms_update_queue($queue)
{
	global $wpdb,$mrt_debug;
	
	if(sizeof($queue) > 0 ) :
		$queue['batch_no']	= intval($queue['batch_no']) + 1;
		$table	= $wpdb->prefix."mrt_sms_queue";
		$query	= "UPDATE ".$table." ".
				  "SET batch_no = '".$queue['batch_no']."' ".
				  "WHERE `id` = '".$queue['id']."' ";
				  
		$mrt_debug	.= "<br /> Next batch : ".$queue['batch_no'];
		
		$wpdb->query($query);
	endif;
}

function mrt_sms_delete_used_queue()
{
	global $wpdb;
	
	$batch	= mrt_sms_get_total_bacth();
	
	$table	= $wpdb->prefix."mrt_sms_queue";
	$query	= "DELETE FROM ".$table." ".
			  "WHERE `batch_no` >= ".$batch."; ";
			  
	$wpdb->query($query);
}

function mrt_sms_delete_queue()
{
	global $wpdb;	
	
	if(isset($_REQUEST['page']) && $_REQUEST['page'] == 'wptext-message-queue' && 
	   isset($_REQUEST['action']) && $_REQUEST['action'] == 'delete' && 
	   isset($_REQUEST['id']) && !is_null($_REQUEST['id']) && 
	   isset($_REQUEST['_wpnonce']) && wp_verify_nonce($_REQUEST['_wpnonce'],'wptext-delete-queue')) :
	
		$table	= $wpdb->prefix."mrt_sms_queue";
		$query	= "DELETE FROM ".$table." ".
				  "WHERE id = ".$_REQUEST['id']."; ";
				  
		$wpdb->query($query);
		
		$admin_url	= admin_url('admin.php?page=wptext-message-queue&wp-message=4');
		wp_redirect($admin_url);
			  
	endif;
}

function mrt_sms_queue_list()
{
	global $wpdb;
	
	
	$table_name	= $wpdb->prefix."mrt_sms_queue";	
	
	$query		= "SELECT * FROM ".$table_name." ".
				  "ORDER BY id DESC";
				  
	$result		= $wpdb->get_results($query,ARRAY_A);

	$total_batch	= mrt_sms_get_total_bacth();

	if(sizeof($result)) :
		$i = 1;
		foreach($result as $val) :
		?>
        <tr>
        	<td style="text-align:center"><?php echo $i; ?></td>
            <td><?php echo date("d F Y H:i",$val['time']); ?></td>
            <td style="text-align:center"><?php echo $val['type']; ?></td>
            <td>
            	<?php
				
				if($val['type'] == 'post' || $val['type'] == 'post-update') :
					?><a href="<?php echo get_permalink($val['post_id']); ?>"><?php echo get_the_title($val['post_id']); ?></a><?php
				else :
					echo "<strong>".$val['subject']."</strong>, ".$val['message'];
				endif;
				?>
            </td>
            <td style="text-align:center"><?php echo $val['batch_no']; ?> / <?php echo $total_batch; ?></td>
            <td style="text-align:center">
            	<?php $link	= wp_nonce_url(admin_url('admin.php?page=wptext-message-queue&action=delete&id='.$val['id']),'wptext-delete-queue') ;?>
            	<a href="<?php echo $link; ?>">delete</a>
            </td>
        </tr>
        <?php
			$i++;
		endforeach;
	endif;
}

function mrt_sms_queue_page()
{
	
	?>
	<div class="wrap">
      	<h2><?php _e('Wordpress Text Message Queue') ?></h2>
        
		<table class="widefat" cellspacing="0">
		<thead>
			<tr>
				<th scope="col" width="5%" style="text-align:center">ID</th>
				<th scope="col" width="25%" style="text-align:center">Created</th>
				<th scope="col" width="15%"  style="text-align:center">Type</th>
                <th scope="col"  style="text-align:center">Content</th>
				<th scope="col" width="15%" style="text-align:center">Process</th>
                <th scope="col" width="10%" style="text-align:center">Delete</th>
			</tr>
		</thead>
        
		<tbody>
        <?php mrt_sms_queue_list(); ?>
        </tbody>
        
        <tfoot>
			<tr>
				<th scope="col" width="5%" style="text-align:center">ID</th>
				<th scope="col" width="25%" style="text-align:center">Created</th>
				<th scope="col" width="15%"  style="text-align:center">Type</th>
                <th scope="col"  style="text-align:center">Content</th>
				<th scope="col" width="15%" style="text-align:center">Process</th>
                <th scope="col" width="10%" style="text-align:center">Delete</th>
			</tr>
        </tfoot>
        </table>
	</div>
	<?php
}

?>