<?php

class wpTextMessageWidgetApp extends WP_Widget
{
	var $name;
	var $id;
	var $value;
	var $label;
	
	// =================================================
	/* beforeInput */
	// =================================================	
	function beforeInput()
	{
		?><p><label for="<?php echo $this->id ?>"><?php echo $this->label; ?></label> <br /><?php	
	}
	
	// =================================================
	/* afterInput */
	// =================================================	
	function afterInput()
	{
		?></p><?php	
	}
	
	// =================================================
	/* generateInputText */
	// =================================================
	
	function generateInputText($label,$key,$value)
	{
		$this->label	= $label;
		$this->name		= $this->get_field_name($key);
		$this->id		= $this->get_field_id($key);
		$this->value	= $value;
		
		$this->beforeInput();
		?><input id="<?php echo $this->id; ?>" name="<?php echo $this->name; ?>" value="<?php echo $this->value; ?>" type="text" style="width:80%" /><?php
		$this->afterInput();
	}
	
	// =================================================
	/* generateSelectOptions */
	// =================================================
	
	function generateSelectOptions($label,$key,$value,$options)
	{
		$this->label	= $label;
		$this->name		= $this->get_field_name($key);
		$this->id		= $this->get_field_id($key);
		$this->value	= $value;
		
		$this->beforeInput($label,$this->name,$this->id);
		
		?><select name="<?php echo $this->name; ?>" id="<?php echo $this->id; ?>"> <?php
		
		foreach($options as $key => $the_value) :
			$selected	= ( $this->value == $key ) ? "selected='selected'" : "" ;
			?><option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo $the_value; ?></option><?php
		endforeach;
		
		?></select><?php
		
		$this->afterInput();
	}
	
	// =================================================
	/* generateCategoryOptions */
	// =================================================
	
	function generateCategoryOptions($label,$key,$value)
	{
		$this->label	= $label;
		$this->name		= $this->get_field_name($key);
		$this->id		= $this->get_field_id($key);
		$this->value	= $value;
		
		$this->beforeInput($label,$this->name,$this->id);
		
		$args = array(
			    'selected'           => $this->value,
			    'name'               => $this->name,
			    'id'                 => $this->id,
			    'class'              => 'category',
				'hide_if_empty'      => false 
		);
			
		wp_dropdown_categories($args);	
		
		$this->afterInput();
	}
	
	// =================================================
	/* generateCheckbox */
	// =================================================
	
	function generateCheckbox($label,$key,$value,$options = NULL)
	{
		$this->label	= $label;
		$this->name		= $this->get_field_name($key);
		$this->id		= $this->get_field_id($key);
		$this->value	= $value;
		
		$this->beforeInput($label,$this->name,$this->id);
		
		$checked	= ((isset($value) && !empty($value)) || (is_bool($value) && $value)) ? "checked='checked'" : "";
		
		?><input type="checkbox" name="<?php echo $this->name; ?>" <?php echo $checked; ?> value="1" /><?php
		
		$this->afterInput();
	}
	
	// =================================================
	/* generateRadio */
	// =================================================
	
	function generateRadio($label,$key,$value,$options)
	{
		$this->label	= $label;
		$this->name		= $this->get_field_name($key);
		$this->id		= $this->get_field_id($key);
		$this->value	= $value;
		
		$this->beforeInput($label,$this->name,$this->id);
		
		foreach($options as $key => $the_value) :
			$checked	= ( $this->value == $key ) ? "checked='checked'" : "" ;
			?><input type="radio" name="<?php echo $this->name; ?>" value="<?php echo $key; ?>" <?php echo $checked; ?> /><span><?php echo $the_value; ?></span> &nbsp;<?php
		endforeach;
		
		$this->afterInput();
	}
	
}

?>