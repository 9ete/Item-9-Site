<?php

add_action('mrt_sms_hook', 'mrt_sms_cron');
add_filter('cron_schedules', 'mrt_sms_reccurences');

if ( !wp_next_scheduled('mrt_sms_hook') ) :
	wp_schedule_event( time(), '5-minutes', 'mrt_sms_hook' ); // hourly, daily and twicedaily}
endif;

function  mrt_sms_reccurences() 
{
	return array(
		'2-minutes'			=> array(
				'interval' 	=> 120, 
				'display' 	=> '2 Minutes'		
		),
		'5-minutes'			=> array(
				'interval' 	=> 300, 
				'display' 	=> '5 Minutes'		
		),
		'10-minutes' 		=> array(
				'interval' 	=> 600, 
				'display' 	=> '10 Minutes'
		),
		'15-minutes' 		=> array(
				'interval' 	=> 900, 
				'display' 	=> '15 Minutes'
		),
		'30-minutes' 	=> array(
				'interval' 	=> 1800, 
				'display' 	=> '30 Minutes'
			),
	);
}

function  mrt_sms_cron() 
{
	global $mrt_debug;
	
	$queue	= mrt_sms_get_queue();
	mrt_sms_send($queue);
	mrt_sms_update_queue($queue);
	mrt_sms_delete_used_queue();
	
}

?>