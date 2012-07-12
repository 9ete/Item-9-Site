<?php

function widget_mrt_sms($args) {
  extract($args);
  echo "\n <!--Wordpress Text Message WordPress plugin by Total Bounty of http://www.totalbounty.com/ \n plugin url: http://wordpress.org/extend/plugins/wordpress-text-message/-->\n";
  echo $before_widget;
  echo $before_title . get_option( "mrt_sms_header" ) . $after_title;
  mrt_sms_guts_widget();
  echo "<h6><em>" . get_option( "mrt_sms_footer" ) . "<br /><a href=http://www.totalbounty.com/freebies/wordpress-text-message/>Wordpress Text Message</a></em></h6>";
  echo $after_widget;
  echo "\n <!--End of Wordpress Text Message plugin widget-->";
}

function mrt_sms_widget_init(){
}?>
