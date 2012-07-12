<?php
/*
Plugin Name: Marquee-Plus
Version: 4.4
Description: Allows you to put Rotating Marquee's In your Posts and Pages. I will appreciate if you will give me queries and suggestions regarding this plugin.
Author: Know How Media
Author URI: http://www.khmedia.in
License: GPL2

/*  Copyright 2011 Know How Media

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

function show_marqueeplus1()
{
    $mpfeed1 = get_option('mpfeed1');

	if($mpfeed1 == 'Yes')
		$marqueeplus_value_1 = getfeed(html_entity_decode(get_option('marqueeplus_nrc_text1'), ENT_COMPAT));
	else
	    $marqueeplus_value_1 = html_entity_decode(get_option('marqueeplus_nrc_text1'), ENT_COMPAT);

    $marqueeplus_value_1_dir = get_option('marqueeplus_nrc_text1_dir');
    $marqueeplus_speed1 = get_option('marqueeplus_speed1');
    $marqueeplus_width1 = get_option('marqueeplus_width1');
    $marqueeplus_height1 = get_option('marqueeplus_height1');
    $marqueeplus_bgcolor1 = get_option('marqueeplus_bgcolor1');
    $marqueeplus_fgcolor1 = get_option('marqueeplus_fgcolor1');

    if(!empty($marqueeplus_value_1))
    {
        $output .= "<font color='".$marqueeplus_fgcolor1."'><marquee style='font-size:12px;font-family:arial;' bgcolor='".$marqueeplus_bgcolor1."' height='".$marqueeplus_height1."' width='".$marqueeplus_width1."' scrollamount='".$marqueeplus_speed1."' onMouseOver='this.stop();' onMouseOut='this.start();' BEHAVIOR=SCROLL DIRECTION=".$marqueeplus_value_1_dir."> $marqueeplus_value_1"."</marquee>"."</font>";
    }
	return $output;
}

function show_marqueeplus2()
{
    $mpfeed2 = get_option('mpfeed2');

	if($mpfeed2 == 'Yes')
	    $marqueeplus_value_2 = getfeed(html_entity_decode(get_option('marqueeplus_nrc_text2'), ENT_COMPAT));
	else
	    $marqueeplus_value_2 = html_entity_decode(get_option('marqueeplus_nrc_text2'), ENT_COMPAT);
	    
    $marqueeplus_value_2_dir = get_option('marqueeplus_nrc_text2_dir');
    $marqueeplus_speed2 = get_option('marqueeplus_speed2');
    $marqueeplus_width2 = get_option('marqueeplus_width2');
    $marqueeplus_height2 = get_option('marqueeplus_height2');
    $marqueeplus_bgcolor2 = get_option('marqueeplus_bgcolor2');
    $marqueeplus_fgcolor2 = get_option('marqueeplus_fgcolor1');

    if(!empty($marqueeplus_value_2))
    {
        $output .= "<font color='".$marqueeplus_fgcolor2."'><marquee style='font-size:12px;font-family:arial;' bgcolor='".$marqueeplus_bgcolor2."' height='".$marqueeplus_height2."' width='".$marqueeplus_width2."' scrollamount='".$marqueeplus_speed2."' onMouseOver='this.stop();' onMouseOut='this.start();' BEHAVIOR=SCROLL DIRECTION=".$marqueeplus_value_2_dir."> $marqueeplus_value_2"."</marquee>"."</font>";
    }
    return $output;
}

function show_marqueeplus3()
{
    $mpfeed3 = get_option('mpfeed3');

	if($mpfeed3 == 'Yes')
	    $marqueeplus_value_3 = getfeed(html_entity_decode(get_option('marqueeplus_nrc_text3'), ENT_COMPAT));
	else
		$marqueeplus_value_3 = html_entity_decode(get_option('marqueeplus_nrc_text3'), ENT_COMPAT);
		
    $marqueeplus_value_3_dir = get_option('marqueeplus_nrc_text3_dir');
    $marqueeplus_speed3 = get_option('marqueeplus_speed3');
    $marqueeplus_width3 = get_option('marqueeplus_width3');
    $marqueeplus_height3 = get_option('marqueeplus_height3');
    $marqueeplus_bgcolor3 = get_option('marqueeplus_bgcolor3');
    $marqueeplus_fgcolor3 = get_option('marqueeplus_fgcolor1');

    if(!empty($marqueeplus_value_3))
    {
        $output .= "<font color='".$marqueeplus_fgcolor3."'><marquee style='font-size:12px;font-family:arial;' bgcolor='".$marqueeplus_bgcolor3."' height='".$marqueeplus_height3."' width='".$marqueeplus_width3."' scrollamount='".$marqueeplus_speed3."' onMouseOver='this.stop();' onMouseOut='this.start();' BEHAVIOR=SCROLL DIRECTION=".$marqueeplus_value_3_dir."> $marqueeplus_value_3"."</marquee>"."</font>";
    }
    return $output;
}

function marqueeplus_process($content)
{
    if (strpos($content, "<!-- marqueeplus1 -->") !== FALSE)
    {
        $content = preg_replace('/<p>\s*<!--(.*)-->\s*<\/p>/i', "<!--$1-->", $content);
        $content = str_replace('<!-- marqueeplus1 -->', show_marqueeplus1(), $content);
    }
    if (strpos($content, "<!-- marqueeplus2 -->") !== FALSE)
    {
        $content = preg_replace('/<p>\s*<!--(.*)-->\s*<\/p>/i', "<!--$1-->", $content);
        $content = str_replace('<!-- marqueeplus2 -->', show_marqueeplus2(), $content);
    }
    if (strpos($content, "<!-- marqueeplus3 -->") !== FALSE)
    {
        $content = preg_replace('/<p>\s*<!--(.*)-->\s*<\/p>/i', "<!--$1-->", $content);
        $content = str_replace('<!-- marqueeplus3 -->', show_marqueeplus3(), $content);
    }
    return $content;
}

function marqueeplus_optionspage(){
	$version_mp = 4.4;
	if($_POST['marqueeplus_save']){
		$options = array (
			'marqueeplus_nrc_text1',
			'marqueeplus_nrc_text2',
			'marqueeplus_nrc_text3',
			'marqueeplus_nrc_text1_dir',
			'marqueeplus_nrc_text2_dir',
			'marqueeplus_nrc_text3_dir',
			'marqueeplus_speed1',
			'marqueeplus_speed2',
			'marqueeplus_speed3',
			'marqueeplus_width1',
			'marqueeplus_height1',
			'marqueeplus_bgcolor1',
			'marqueeplus_fgcolor1',
			'marqueeplus_width2',
			'marqueeplus_height2',
			'marqueeplus_bgcolor2',
			'marqueeplus_fgcolor2',
			'marqueeplus_width3',
			'marqueeplus_height3',
			'marqueeplus_bgcolor3',
			'marqueeplus_fgcolor3',
			'mpfeed1',
			'mpfeed2',
			'mpfeed3',
			'mpfeedcnt'
		);
		
		foreach ($options as $value)
		{
			switch ($value)
			{
				case 'marqueeplus_nrc_text1':
				case 'marqueeplus_nrc_text2':
				case 'marqueeplus_nrc_text3':
					update_option($value, htmlentities(stripslashes($_POST[$value]), ENT_COMPAT));
					break;
				default:
					update_option($value, $_POST[$value]);
			}
		}

		echo '<div class="updated"><p>Options Saved</p></div>';
	}

	?>
	<div class="wrap">
	
	<h2>Marquee-Plus Options</h2>
	<form method="post" id="marqueeplus_options">
		<fieldset class="options">
		<legend>Now, its the time give your blog a new look.</legend>
		<legend>Enter the proper Marquee-Text(s) as per your needs. You can add atmost 3 marquee texts to your pages/posts.</legend>
		<table class="form-table">
			
			<tr valign="top"> 
				<th width="33%" scope="row">Marquee Text(s) (HTML Tags Allowed):</th> 
				<td>
				<strong>Text 1: </strong>Is Feed (Yes/No)? <input name="mpfeed1" type="text" id="mpfeed1" value="<?php echo get_option('mpfeed1') ;?>" size="3"/><br /><textarea name="marqueeplus_nrc_text1" cols=60 rows=4 id="marqueeplus_nrc_text1" /><?php echo get_option('marqueeplus_nrc_text1') ;?></textarea><br />
				<strong>Text 2: </strong>Is Feed (Yes/No)? <input name="mpfeed2" type="text" id="mpfeed2" value="<?php echo get_option('mpfeed2') ;?>" size="3"/><br><textarea name="marqueeplus_nrc_text2" cols=60 rows=4 id="marqueeplus_nrc_text2" /><?php echo get_option('marqueeplus_nrc_text2') ;?></textarea><br />
				<strong>Text 3: </strong>Is Feed (Yes/No)? <input name="mpfeed3" type="text" id="mpfeed3" value="<?php echo get_option('mpfeed3') ;?>" size="3"/><br><textarea name="marqueeplus_nrc_text3" cols=60 rows=4 id="marqueeplus_nrc_text3" /><?php echo get_option('marqueeplus_nrc_text3') ;?></textarea><br /><br />
				</td>
			<tr valign="top">
				<th width="33%" scope="row">Text Options:</th>
				<td>
				Text 1: Direction (LEFT/RIGHT/UP/DOWN): <br /><input name="marqueeplus_nrc_text1_dir" type="text" id="marqueeplus_nrc_text1_dir" value="<?php echo get_option('marqueeplus_nrc_text1_dir') ;?>" size="5"/> Speed (0-10): <input name="marqueeplus_speed1" type="text" id="marqueeplus_speed1" value="<?php echo get_option('marqueeplus_speed1') ;?>" size="2"/> Width: <input name="marqueeplus_width1" type="text" id="marqueeplus_width1" value="<?php echo get_option('marqueeplus_width1') ;?>" size="2"/> Height: <input name="marqueeplus_height1" type="text" id="marqueeplus_height1" value="<?php echo get_option('marqueeplus_height1') ;?>" size="2"/> BG: <input name="marqueeplus_bgcolor1" type="text" id="marqueeplus_bgcolor1" value="<?php echo get_option('marqueeplus_bgcolor1') ;?>" size="5"/> FG: <input name="marqueeplus_fgcolor1" type="text" id="marqueeplus_fgcolor1" value="<?php echo get_option('marqueeplus_fgcolor1') ;?>" size="5"/><br /><br />
				Text 2: Direction (LEFT/RIGHT/UP/DOWN): <br /><input name="marqueeplus_nrc_text2_dir" type="text" id="marqueeplus_nrc_text2_dir" value="<?php echo get_option('marqueeplus_nrc_text2_dir') ;?>" size="5"/> Speed (0-10): <input name="marqueeplus_speed2" type="text" id="marqueeplus_speed2" value="<?php echo get_option('marqueeplus_speed2') ;?>" size="2"/> Width: <input name="marqueeplus_width2" type="text" id="marqueeplus_width2" value="<?php echo get_option('marqueeplus_width2') ;?>" size="2"/> Height: <input name="marqueeplus_height2" type="text" id="marqueeplus_height2" value="<?php echo get_option('marqueeplus_height2') ;?>" size="2"/> BG: <input name="marqueeplus_bgcolor2" type="text" id="marqueeplus_bgcolor2" value="<?php echo get_option('marqueeplus_bgcolor2') ;?>" size="5"/> FG: <input name="marqueeplus_fgcolor2" type="text" id="marqueeplus_fgcolor2" value="<?php echo get_option('marqueeplus_fgcolor2') ;?>" size="5"/><br /><br />
				Text 3: Direction (LEFT/RIGHT/UP/DOWN): <br /><input name="marqueeplus_nrc_text3_dir" type="text" id="marqueeplus_nrc_text3_dir" value="<?php echo get_option('marqueeplus_nrc_text3_dir') ;?>" size="5"/> Speed (0-10): <input name="marqueeplus_speed3" type="text" id="marqueeplus_speed3" value="<?php echo get_option('marqueeplus_speed3') ;?>" size="2"/> Width: <input name="marqueeplus_width3" type="text" id="marqueeplus_width3" value="<?php echo get_option('marqueeplus_width3') ;?>" size="2"/> Height: <input name="marqueeplus_height3" type="text" id="marqueeplus_height3" value="<?php echo get_option('marqueeplus_height3') ;?>" size="2"/> BG: <input name="marqueeplus_bgcolor3" type="text" id="marqueeplus_bgcolor3" value="<?php echo get_option('marqueeplus_bgcolor3') ;?>" size="5"/> FG: <input name="marqueeplus_fgcolor3" type="text" id="marqueeplus_fgcolor3" value="<?php echo get_option('marqueeplus_fgcolor3') ;?>" size="5"/><br /><br />
				How many feeds you want in the result (required): <input name="mpfeedcnt" type="text" id="mpfeedcnt" value="<?php echo get_option('mpfeedcnt') ;?>" size="2"/>
				</td>
			</tr>
			</tr>
		<tr>
        <th width="33%" scope="row">Save settings :</th> 
        <td>
		<input type="submit" name="marqueeplus_save" value="Save Settings" />
        </td>
        </tr>
		<tr>
        <th scope="row" style="text-align:right; vertical-align:top;">
        <td>
		<h3>Above all ?</h3>
		<p>Why don't you write a post about marquee-plus at <a href='http://www.khmedia.in'>Know How Media</a></p>
		<h3>Problems, Questions, Suggestions ?</h3>
		<p>Catch me at <a href="http://www.khmedia.in/contact-us" target="_blank">Contact Author</a>.</p>
		<p>You can also visit our <a href="http://www.khmedia.in/forum" target="_blank">Community</a> for any queries.</p>
        </td>
        </tr>
		</table>
		<h3>Usage:</h3>
		1. Php inserts can be used as <strong>&lt;?php echo show_marqueeplus1(); ?&gt;</strong>, <strong>&lt;?php echo show_marqueeplus2(); ?&gt;</strong> or <br /><strong>&lt;?php echo show_marqueeplus3(); ?&gt;</strong>.<br />
		2. You can also use trigger text such as <strong>&lt;!-- marqueeplus1 --&gt;</strong>, <strong>&lt;!-- marqueeplus2 --&gt;</strong> or <strong>&lt;!-- marqueeplus3 --&gt;</strong>.<br />
		3. Triggers can only be inserted through HTML view of the wordpress editor.<br />
		4. Feed Address must start with http. For Ex. http://feeds2.feedburner.com/abc.<br />
		5. Currently this version works with PHP 5.0 or above.
		<h3>Visit <a href="http://www.khmedia.in">our site</a> for more information. Thank you.</h3>
		Plugin developed by <a href="http://www.khmedia.in/" target="_blank">Know How Media</a>.
		<h3>Version</h3><?php echo "This is stable version ".$version_mp ?>
		</fieldset>
	</form>
	</table>
	</div>
	<?php
}

/******************************************************************************************************************
   RSS PARSING
******************************************************************************************************************/
 
function parseRSS($url) { 
//PARSE RSS FEED
	$feedeed = implode('', file($url));
	$parser = xml_parser_create();
    xml_parse_into_struct($parser, $feedeed, $valueals, $index);
    xml_parser_free($parser);
 
//CONSTRUCT ARRAY
	foreach($valueals as $keyey => $valueal){
		if($valueal['type'] != 'cdata') {
			$item[$keyey] = $valueal;
		}
	}
 
	$i = 0;
 
	foreach($item as $key => $value){
 		if($value['type'] == 'open') {
			$i++;
			$itemame[$i] = $value['tag'];
 
	} elseif($value['type'] == 'close') {
		$feed = $values[$i];
		$item = $itemame[$i];
		$i--;
 
	if(count($values[$i])>1){
		$values[$i][$item][] = $feed;
		} else {
			$values[$i][$item] = $feed;
		}
 
		} else {
			$values[$i][$value['tag']] = $value['value'];  
		}
	}
 
	//RETURN ARRAY VALUES
	return $values[0];
}

/*
Please do not remove the link provided below. If you liked this plugin then I request you please just give me a backlink on your site. I will really appreciate this. Please don't remove this link if you are a real Human Being and Understood mine work, time and effort that I gave you for your wordpress site.
*/
function marqueeplus_footer()
{
?>
<small><center><strong><a href="http://www.petelower.com/" target='_blank'></a> <strong><a href="http://www.i9mh.com" target='_blank'></a></strong></center></small>
<?php
}


function getfeed($url) {	
	//PARSE THE RSS FEED INTO ARRAY
	$count = get_option('mpfeedcnt');	
	$xml = parseRSS($url);
 
	foreach($xml['RSS']['CHANNEL']['ITEM'] as $item) {
	if($count > 0){
		$feeddata .= "<p class=\"indexBoxNews\"><b>{$item['TITLE']}{$link}</b>: {$item['DESCRIPTION']}{$link} <a href=\"{$item['LINK']}\" target=\"_blank\" class=\"indexBoxNews\">{$link}View It.</a></p>";
	}
	$count--;
	}
	return $feeddata;
} 
 
 
function marqueeplus_adminmenu()
{
	if (function_exists('add_options_page')) {	
		add_options_page('marqueeplus_optionspage', 'Marquee-Plus', 9, basename(__FILE__),'marqueeplus_optionspage');
	}
}

add_filter('the_content', 'marqueeplus_process');
add_action('wp_footer','marqueeplus_footer');
add_action('admin_menu','marqueeplus_adminmenu',1);
?>
