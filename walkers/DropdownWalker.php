<?php
class JC_Walker_Nav_Menu_Dropdown extends JC_Submenu_Nav_Walker{
 
	var $item_id = 0;
 
	function __construct($id = 0){
		$this->item_id = $id;
	}
 
	public function start_lvl(&$output, $depth = 0, $args = array()){}
 
	public function end_lvl(&$output, $depth = 0, $args = array()){}
 
	public function start_el(&$output, $item, $depth = 0, $args = array(),  $current_object_id = 0){
 
		$item->title = str_repeat("&nbsp;", $depth * 4) . $item->title;
 
		parent::start_el($output, $item, $depth, $args);
		if($item->ID == $this->item_id)
			$output = str_replace('<li', '<option value="'.$item->ID.'" selected="selected"', $output);
		else
			$output = str_replace('<li', '<option value="'.$item->ID.'"', $output);
	}
 
	public function end_el(&$output, $item, $depth = 0, $args = array()){
		$output .= "</option>\n";
	}
}