<?php

class JC_Submenu_Nav_Walker extends Walker_Nav_Menu {

	private $hierarchical = true;	// diplsay menu as hierarchy
	private $dynamic_count = 1;		// dynamic population count

	private $menu_start = 0; 	// menu start depth
	private $menu_depth = 0; 	// menu depth

	private $section_menu = false;	// display as menu section
	private $split_menu = false;	// display as split menu
	private $split_trigger_depth = 0;
	private $active_title = '';
	private $parent_label = false; // override parent menu item with custom label

	private $_section_ids = array();
	private $selected_section_ids = array(); 	// current id and all ansestor ids

	public function __construct($args = array()){
		$this->hierarchical = isset($args['hierarchical']) && $args['hierarchical'] == 0 ? 0 : 1;
		$this->split_menu = isset($args['split_menu']) ? true : false;
		$this->section_menu = isset($args['section_menu']) && is_bool($args['section_menu']) ? $args['section_menu'] : false;
			
		// Display Split Menu Section
		$this->menu_start_item = isset($args['menu_item']) ? intval($args['menu_item']) : 0;
		$this->menu_start = isset($args['menu_start']) ? intval($args['menu_start']) : 0;
		$this->menu_depth = isset($args['menu_depth']) ? intval($args['menu_depth']) : 5;
		$this->show_parent = isset($args['show_parent'])  && $args['show_parent'] == 1 ? 1 : 0;
		$this->split_trigger_depth = isset($args['trigger_depth']) && $args['trigger_depth'] > 0 ? $args['trigger_depth'] : 0;
		$this->parent_label = isset($args['parent_label']) ? $args['parent_label'] : false;
	}

	function replace_template_vars($output = ''){

		$output = str_replace('{{PARENT_TITLE}}', $this->active_title, $output);
		return $output;
	}

	function start_el( &$output, $item, $depth = 0, $args = array(), $current_object_id = 0 ) {
		
		// clone to unlink from $args
		if(is_object($args)){
			$temp = clone($args);
		}else{
			$temp = $args;
		}

		$item_args = apply_filters( 'jcs/menu_item_args', $temp, $item);
		parent::start_el($output, $item, $depth, $item_args, $current_object_id);
	}
 
	function end_el( &$output, $item, $depth = 0, $args = array() ) {
		
		// clone to unlink from $args
		if(is_object($args)){
			$temp = clone($args);
		}else{
			$temp = $args;
		}

		$item_args = apply_filters( 'jcs/menu_item_args', $temp, $item);
		parent::end_el($output, $item, $depth, $item_args);
	}

	function start_lvl( &$output, $depth = 0, $args = array() ) {

		if( $this->hierarchical == 1 ){

			$classes = apply_filters( 'jcs/menu_level_class', array('sub-menu'), $depth, $args);
			$level_classes = "";
			$indent = str_repeat("\t", $depth);

			if(is_array($classes) && !empty($classes)){
				$level_classes = implode(" ", $classes);
			}
			
			$output .= "\n$indent<ul class=\"$level_classes\">\n";
		}
	}
 
	function end_lvl( &$output, $depth = 0, $args = array() ) {

		if( $this->hierarchical == 1 ){

			$indent = str_repeat("\t", $depth);
			$output .= "$indent</ul>\n";
		}
	}

	/**
	 * Display array of elements hierarchically.
	 *
	 * It is a generic function which does not assume any existing order of
	 * elements. max_depth = -1 means flatly display every element. max_depth =
	 * 0 means display all levels. max_depth > 0  specifies the number of
	 * display levels.
	 *
	 * @since 2.1.0
	 *
	 * @param array $elements
	 * @param int $max_depth
	 * @return string
	 */
	function walk( $elements, $max_depth, ...$args) {

		//$args = array_slice(func_get_args(), 2);
		$output = '';

		if ($max_depth < -1) //invalid parameter
			return $output;

		if (empty($elements)) //nothing to walk
			return $output;

		$id_field = $this->db_fields['id'];
		$parent_field = $this->db_fields['parent'];

		// flat display
		if ( -1 == $max_depth ) {
			$empty_array = array();
			foreach ( $elements as $e )
				$this->display_element( $e, $empty_array, 1, 0, $args, $output );
			return $output;
		}

		/**
		 * Loop through all menu items checking to see if if any items have been
		 * marked for auto population using this plugin
		 */
		
		global $jcsubmenu;

		if($jcsubmenu->public_walker){
			$elements = $this->attach_elements($elements);
		}

		$elements = $this->_process_menu($elements);

		// escape if no elements are left
		if(empty($elements)){
			return false;
		}

		/*
		 * need to display in hierarchical order
		 * separate elements into two buckets: top level and children elements
		 * children_elements is two dimensional array, eg.
		 * children_elements[10][] contains all sub-elements whose parent is 10.
		 */
		$top_level_elements = array();
		$children_elements  = array();

		foreach ( $elements as $e) {

			if ( 0 == $e->$parent_field ){
				$top_level_elements[] = $e;
			}else{
				$children_elements[ $e->$parent_field ][] = $e;
			}
		}

		/*
		 * when none of the elements is top level
		 * assume the first one must be root of the sub elements
		 */
		if ( empty($top_level_elements) ) {

			$first = array_slice( $elements, 0, 1 );
			$root = $first[0];

			$top_level_elements = array();
			$children_elements  = array();
			foreach ( $elements as $e) {
				if ( $root->$parent_field == $e->$parent_field )
					$top_level_elements[] = $e;
				else
					$children_elements[ $e->$parent_field ][] = $e;
			}
		}

		foreach ( $top_level_elements as $e )
			$this->display_element( $e, $children_elements, $max_depth, 0, $args, $output );

		/*
		 * if we are displaying all levels, and remaining children_elements is not empty,
		 * then we got orphans, which should be displayed regardless
		 */
		if ( ( $max_depth == 0 ) && count( $children_elements ) > 0 ) {
			$empty_array = array();
			foreach ( $children_elements as $orphans )
				foreach( $orphans as $op )
					$this->display_element( $op, $empty_array, 1, 0, $args, $output );
		 }

		 return $output;
	}

	public function attach_elements($elements){

		$id_field = $this->db_fields['id'];
		$parent_field = $this->db_fields['parent'];

		// copy to new array to keep menu item order
		$new_elements = array();
		foreach($elements as $k => $e){
			$break = false;
			
			// Hide element for logged in users
			if(!is_user_logged_in() && intval(SubmenuModel::get_meta($e->$id_field, 'admin')) == 1){
				unset($elements[$k]);
				$break = true;
			}

			if(!$break){
				$new_elements[] = $e;
				$current_dynamic_parent = false;

				// check to see if auto populate flag has been set
				if(SubmenuModel::get_meta($e->$id_field, 'autopopulate') == 1){
					
					$type = SubmenuModel::get_meta($e->$id_field, 'populate-type');
					$value = SubmenuModel::get_meta($e->$id_field, 'populate-value');

					/**
					 * Added: 6/3/14
					 * Replace dynamic item with populated menu items
					 * TODO: Fix overriding of all menu items
					 */
					$parent = false;
					$childpop = SubmenuModel::get_meta($e->$id_field, 'childpop');
					if($childpop == 1){
						$parent = true;
						array_pop($new_elements);
					}

					if($type == 'post'){
						$new_elements = array_merge($new_elements, $this->_populate_post_items($e, $value, $parent));
					}elseif($type == 'page'){
						$new_elements = array_merge($new_elements, $this->_populate_page_items($e, $value, $parent));
					}elseif($type == 'tax'){
						$new_elements = array_merge($new_elements, $this->_populate_tax_items($e, $value, $parent));
					}elseif($type == 'archive'){
						$new_elements = array_merge($new_elements, $this->_populate_archive_items($e, $value, $parent));
					}
				}
			}
		}

		$elements = $new_elements;

		// filter to allow modification of populated menu items, e.g. set current menu item
		if($this->split_menu){
			$elements = apply_filters( 'jcs/split_menu/populated_elements', $elements);
		}elseif($this->section_menu){
			$elements = apply_filters( 'jcs/section_menu/populated_elements', $elements);
		}

		$elements = apply_filters( 'jcs/populated_elements', $elements);

		return $elements;
	}

	public function _process_menu($elements = array()){

		$id_field = $this->db_fields['id'];
		$parent_field = $this->db_fields['parent'];

		// set menu item status
		$elements = $this->_set_elements_state($elements);

		// Set Menu Item Depth 
		$elements = $this->set_elements_depth($elements, 0, true);

		// Set Menu Classes
		$elements = $this->_set_element_classes($elements);
	
		if($this->section_menu || $this->split_menu){		

			// start template replacement function
			add_filter( 'jcs/split_widget_title', array($this, 'replace_template_vars'));

			// process section of menu
			if($this->section_menu == true){

				$new_elems = $this->_process_menu_section($elements);
			}

			// process split menu
			if($this->split_menu == true){

				$new_elems = $this->_process_split_menu($elements);
			}

			// process elements to display
			foreach($new_elems as $k => $elm){

				// add data to elements
				if($elm->menu_depth > $this->menu_start){
					
					if($elm->menu_depth >= ($this->menu_start + $this->menu_depth)){
						// remove items that are too deep
						unset($new_elems[$k]);
					}
				}elseif($elm->menu_depth == $this->menu_start){

					// change the parent title
					if($this->show_parent){
						$this->active_title = $new_elems[$k]->title;

						if($this->parent_label){
							$new_elems[$k]->title = $this->parent_label;
						}
					}

					// need to change to parent = 0
					$new_elems[$k]->$parent_field = 0;
				}else{

					// set the active title if split menu and the current parent is not being shown
					if(!$this->show_parent && $this->split_menu){
						$this->active_title = $new_elems[$k]->title;
					}

					// unset elements beneath
					unset($new_elems[$k]);
				}
			}

			$elements = $new_elems;
		}

		return $elements;
	}

	public function _process_menu_section($elements = array()){
		
		$new_elems = array();

		foreach($elements as $item){
			if(($this->menu_start_item == $item->db_id && $this->show_parent == 1) || $item->menu_item_parent == $this->menu_start_item || in_array($item->menu_item_parent, $this->_section_ids)){

				// set depth start from first item
				if(empty($new_elems)){
					$this->menu_start = $item->menu_depth;

					if($this->show_parent)
						$this->menu_depth++;
				}

				$new_elems[] = $item;	

				if(!in_array($item->db_id, $this->_section_ids)){
					$this->_section_ids[] = $item->db_id;	
				}
			}
		}

		return $new_elems;
	}

	public function _process_split_menu($elements = array()){

		$new_elems = array();
		$old_elems = $elements;
		$test_elms = $elements;
		$section_parents = array();
		$parent_elem = false;
		$parent_count = 0;
		$keep_element_ids = array();
		$keep_element_parents = array();
		$selected_depth = 0;
		$id_field = $this->db_fields['id'];
		$parent_field = $this->db_fields['parent'];

		// get relevent parent id
		if($this->menu_start > 0){
			foreach($old_elems as $elm){

				/**
				 * Added: 6/3/14
				 * Get current menu item
				 */
				if((isset($elm->classes) && in_array('current-menu-item', $elm->classes)) || ( isset($elm->current) && $elm->current == 1)){
					$keep_element_ids[] = $elm->$id_field;
					$keep_element_parents[] = $elm->$parent_field;
					$selected_depth = isset($elm->menu_depth) ? $elm->menu_depth: 0;
				}
				
				if(in_array($elm->$id_field, $this->selected_section_ids)){

					/**
					 * Added: 6/3/14
					 * Comment out if statement
					 */
					// if($elm->menu_depth == $this->menu_start - 1){
						$new_elems[] = $elm;
						$section_parents[] = $elm->$id_field;
					// }
				}
			}

			/**
			 * Added: 6/3/14
			 * Build a list of ids
			 * Display Menu items which are only one item deeper than current selection
			 */
			$break = false;
			while(!$break){
				$break = true;
				foreach($old_elems as $elm){
					if(in_array($elm->$parent_field, $keep_element_ids) && !in_array($elm->$id_field, $keep_element_ids) && 
						($this->split_trigger_depth == 0 || $elm->menu_depth <= $selected_depth + $this->split_trigger_depth)){
						$keep_element_ids[] = $elm->$id_field;
						$break = false;
					}
				}
			}

			/**
			 * Added: 6/3/14
			 * Build list of parent items to keep
			 */
			
			if(isset($keep_element_ids[0])){

				$temp = array($keep_element_ids[0]); // first element is the active menu item
				$break = false;
				while(!$break){
					$break = true;
					foreach($old_elems as $elm){
						if(in_array($elm->$id_field, $keep_element_parents) && !in_array($elm->$parent_field, $keep_element_parents)){
							$keep_element_parents[] = $elm->$parent_field;
							$break = false;
						}
					}
				}
			}

			/**
			 * Added: 6/3/14
			 * Add elements matching parent id
			 */
			foreach($old_elems as $elm){
				if(in_array($elm->$parent_field, $keep_element_parents)){
					$keep_element_ids[] = $elm->$id_field;
				}
			}

		}else{
			$section_parents = array(0);
		}

		if($this->show_parent && $this->menu_start > 0){
			$this->menu_start--;
			$this->menu_depth++;
		}

		while($parent_count < count($section_parents)){

			$parent_count = count($section_parents);

			foreach($old_elems as $elm){

				if(in_array($elm->$parent_field, $section_parents) && !in_array($elm->$id_field, $section_parents)){
					$section_parents[] = $elm->$id_field;
					$new_elems[] = $elm;
				}
			}	
		}

		/**
		 * Added: 6/3/14
		 * Remove elements which are not in $keep_element_ids
		 */
		foreach($test_elms as $k =>$elm){
			if(!in_array($elm->$id_field, $keep_element_ids)){
				unset($test_elms[$k]);
			}elseif($elm->$parent_field == 0 && (!isset($elm->split_section) || $elm->split_section != 1) && (!isset($elm->current) || $elm->current != 1) && (!isset($elm->current_item_parent) || $elm->current_item_parent != 1) && (!isset($elm->current_item_ancestor) || $elm->current_item_ancestor != 1)){
				unset($test_elms[$k]);
			}
		}

		return $test_elms;
	}

	public function set_elements_depth($elements, $parent = 0, $menu = false){
		/**
		 * Set Menu Item Depth
		 */
		$menu_depths = array();
		$menu_elements = array();
		$id_field = $this->db_fields['id'];
		$parent_field = $this->db_fields['parent'];
		$counter = 0;

		$depth_field = $menu ? 'menu_depth' : 'depth';

		while(count($menu_elements) < count($elements) && $counter < 5){

			foreach($elements as $k => $e){
				if($e->$parent_field == $parent){

					if(!isset($menu_depths[0]) || !is_array($menu_depths[0])){
						$menu_depths[0] = array();
					}

					// add id to $menu_elements
					if(!isset($elements[$k]->$depth_field)){
						$menu_elements[] = $e->$id_field;
						$menu_depths[0][] = $e->$id_field;
						$elements[$k]->$depth_field = 0;	
					}

				}else{

					$break = false;
					foreach($menu_depths as $tax_depth => $parents){
						foreach($parents as $parent_id){
							
							if($e->$parent_field == $parent_id){

								if(!isset($menu_depths[$tax_depth+1]) || !is_array($menu_depths[$tax_depth+1])){
									$menu_depths[$tax_depth+1] = array();
								}

								// add id to $menu_elements
								if(!isset($elements[$k]->$depth_field)){
									$menu_elements[] = $e->$id_field;	
									$menu_depths[$tax_depth+1][] = $e->$id_field;
									$elements[$k]->$depth_field = $tax_depth+1;
								}
								
								$break = true;
								continue;
							}
						}

						if($break)
							continue;
					}
				}
			}
			$counter++;
		}

		return $elements;
	}

	public function _set_elements_state($elements){

		$parent_field = $this->db_fields['parent'];
		$id_field = $this->db_fields['id'];
		$current_item_found = false;
		$parent_item_found = false;
		$item_id = 0;
		$prev_item_id = 0;
		$break = false;

		while(!$break){

			$break = true;

			foreach($elements as &$element){

				if(!$current_item_found && isset($element->current) && $element->current == 1){

					$current_item_found = true;
					$item_id = $element->$parent_field;
					$break = false;
					$this->selected_section_ids[] = $element->$id_field;
					$element->split_section = 1;
				}

				if($current_item_found && $item_id == $element->$id_field){

					$item_id = $element->$parent_field;
					$element->split_section = 1;
					$break = false;

					if(!$parent_item_found){
						$element->classes[] = 'current-menu-parent';
						$element->current_item_parent = 1;
						$parent_item_found = true;
					}

					$element->classes[] = 'current-menu-ancestor';
					$element->current_item_ancestor = 1;
					$this->selected_section_ids[] = $element->$id_field;
				}

				if($current_item_found && $item_id == 0){
					break;
				}
			}

			if($item_id == 0 || $item_id == $prev_item_id){
				$break = true;
			}

			$prev_item_id = $item_id;	
		}

		return $elements;
	}

	/**
	 * Find all elements with children and add parent classes
	 *
	 * @param array $elements
	 */
	public function _set_element_classes($elements){

		$parent_ids = array();
		$id_field = $this->db_fields['id'];
		$parent_field = $this->db_fields['parent'];

		foreach($elements as $element){

			$parent_id = $element->$parent_field;
			if($parent_id <= 0){
				continue;
			}

			if(!in_array($parent_id, $parent_ids)){
				$parent_ids[] = $parent_id;
			}

		}

		foreach($elements as $i => $element){
			if(!in_array($element->$id_field, $parent_ids)){
				continue;
			}

			if (!in_array('menu-item-has-children', $element->classes)) {
				$elements[$i]->classes[] = 'menu-item-has-children';
			}
		}

		return $elements;
	}

	/**
	 * Generate Elements from page items
	 * @param stdObj $menu_item Current Menu Item Being Populated
	 * @param array $page_parent_id Page to get children of
	 * @param  bool $replace_parent
	 * @return array list of elements
	 */
	public function _populate_page_items($menu_item, $page_parent_id, $replace_parent = false){
			
		global $post;

		$elements = array();
		$id_field = $this->db_fields['id'];
		$parent_field = $this->db_fields['parent'];
		$order = SubmenuModel::get_meta($menu_item->$id_field, 'page-order');
		$orderby = SubmenuModel::get_meta($menu_item->$id_field, 'page-orderby');
		$exclude = SubmenuModel::get_meta($menu_item->$id_field, 'page-exclude');

		$page_query = array( 
			'hierarchical' => 1, 
			'child_of' => $page_parent_id,
			'sort_order' => $order,
			'sort_column' => $orderby ,
			'exclude' => $exclude
		);

		// apply filters
		$page_query = apply_filters( 'jcs/page_query_args', $page_query );
		$page_query = apply_filters('jcs/page_'.$menu_item->$id_field.'_query_args', $page_query );

		// run page query
		$pages = get_pages($page_query);

		foreach($pages as $p){
			
			$p->$id_field = $p->ID;
			$p->object = 'page';
			$p->object_id = $p->ID;


			$p->title = apply_filters( 'jcs/item_title', $p->post_title, $p->ID, 'page' );
			$p->title = apply_filters( 'jcs/page_item_title', $p->title, $p->ID );

			$p->url = apply_filters( 'jcs/item_url', get_permalink( $p->ID), $p->ID, 'page' );
			$p->url = apply_filters( 'jcs/page_item_url', $p->url, $p->ID );

			$p->classes = array();

			if($p->post_parent == $page_parent_id){

				// remove childpop item
				// $p->$parent_field = $menu_item->$id_field;	
				if($replace_parent){	
					$p->$parent_field = $menu_item->$parent_field;
				}else{
					$p->$parent_field = $menu_item->$id_field;	
				}
				
			}else{
				$p->$parent_field = $p->post_parent;
			}

			// add classes
			$p->classes = apply_filters( 'jcs/item_classes', $p->classes, $p->ID, 'page');
			$p->classes = apply_filters( 'jcs/page_item_classes', $p->classes, $p->ID);

			// check if this page is the current page
			if( is_page($p->ID) && $post->ID == $p->ID){

				// $current_dynamic_parent = $p->$parent_field;
				$p->classes[] = 'current-menu-item';
				$p->split_section = true;
				$p->current = 1;
			}
			
			$elements[] = clone($p);
		}

		return $elements;
	}

	/**
	 * Generate Elements of post type
	 * @param stdObj $menu_item Current Menu Item Being Populated
	 * @param array $page_parent_id Post type to populate with
	 * @param  bool $replace_parent
	 * @return array list of elements
	 */
	public function _populate_post_items($menu_item, $post_type, $replace_parent = false){
		global $post;
		$id_field = $this->db_fields['id'];
		$parent_field = $this->db_fields['parent'];
		$elements = array();

		$limit = SubmenuModel::get_meta($menu_item->$id_field, 'post-limit');
		$order = SubmenuModel::get_meta($menu_item->$id_field, 'post-order');
		$orderby = SubmenuModel::get_meta($menu_item->$id_field, 'post-orderby');
		$post_tax = SubmenuModel::get_meta($menu_item->$id_field, 'post-tax');
		$post_term = intval(SubmenuModel::get_meta($menu_item->$id_field, 'post-term'));

		$post_query = array(
			'post_type' => $post_type, 
			'posts_per_page' => $limit,
			'order' => $order,
			'orderby' => $orderby
		);

		// add taxonomy filter
		if( !empty($post_tax) && taxonomy_exists( $post_tax ) ){
			$tax_args = array( 'taxonomy' => $post_tax, 'field' => 'id' );

			if(get_term_by( 'id', $post_term, $post_tax)){
				$tax_args['terms'] = $post_term;
			}

			$post_query['tax_query'] = array(
				$tax_args
			);
		}

		// apply filters
		$post_query = apply_filters( 'jcs/post_query_args', $post_query );
		$post_query = apply_filters('jcs/post_'.$menu_item->$id_field.'_query_args', $post_query );

		// change id field to parent field
		if($replace_parent){
			$id_field = $this->db_fields['parent'];
		}

		// run post type query
		$post_type_query = new WP_Query($post_query);

		if($post_type_query->have_posts()){
			foreach($post_type_query->posts as $p){
				
				// set menu item variables
				$p->$id_field = $p->ID;
				$p->object = 'post';
				$p->object_id = $p->ID;

				$p->title = apply_filters( 'jcs/item_title', $p->post_title, $p->ID, $post_type );
				$p->title = apply_filters( 'jcs/post_item_title', $p->title, $p->ID );

				$p->url = apply_filters( 'jcs/item_url', get_permalink( $p->ID), $p->ID, $post_type );
				$p->url = apply_filters( 'jcs/post_item_url', $p->url, $p->ID );

				$p->$parent_field = $menu_item->$id_field;
				$p->classes = array();

				if(is_post_type_hierarchical($post_type )){

					if($p->post_parent == $post_type){
						
						// remove childpop item
						if($replace_parent){	
							$p->$parent_field = $menu_item->$parent_field;
						}else{
							$p->$parent_field = $menu_item->$id_field;	
						}

					}else{
						$p->$parent_field = $p->post_parent;
					}
				}

				// add classes
				$p->classes = apply_filters( 'jcs/item_classes', $p->classes, $p->ID, $post_type);
				$p->classes = apply_filters( 'jcs/post_item_classes', $p->classes, $p->ID);
				
				// check if post item is the current page
				if(is_single($post) && is_singular( $post_type ) && $post->ID == $p->ID){
					$p->classes[] = 'current-menu-item';
					$p->split_section = true;
					$p->current = 1;
				}
				
				$elements[] = clone($p);
			}
		}
		
		return $elements;
	}

	/**
	 * Generate Elements of taxonomy
	 * @param stdObj $menu_item Current Menu Item Being Populated
	 * @param array $page_parent_id Taxonomy to populate from
	 * @param  bool $replace_parent
	 * @return array list of elements
	 */
	public function _populate_tax_items($menu_item, $taxonomy, $replace_parent = false){

		global $post;
		$id_field = $this->db_fields['id'];
		$parent_field = $this->db_fields['parent'];

		$dynamic_item_prefix = str_repeat(0, $this->dynamic_count);

		$tax_parent_id = $menu_item->$id_field;
		
		$order = SubmenuModel::get_meta($tax_parent_id, 'tax-order');
		$orderby = SubmenuModel::get_meta($tax_parent_id, 'tax-orderby');
		$hide = SubmenuModel::get_meta($tax_parent_id, 'tax-empty');
		$exclude = SubmenuModel::get_meta($tax_parent_id, 'tax-exclude');
		$tax_max_depth = intval(SubmenuModel::get_meta($tax_parent_id, 'tax-depth'));
		$tax_term = intval(SubmenuModel::get_meta($tax_parent_id, 'tax-term'));

		$term_query = array(
			'hide_empty' => $hide,
			'order' => $order,
			'orderby' => $orderby,
			// 'exclude' => $exclude,
			'exclude_tree' => $exclude,
			'child_of' => $tax_term
		);

		// apply filters
		$term_query = apply_filters( 'jcs/term_query_args', $term_query ); 
		$term_query = apply_filters('jcs/term_'.$menu_item->$id_field.'_query_args', $term_query );

		// run term query
		$terms = get_terms( $taxonomy, $term_query );

		$tax_elements = array();

		// change id field to parent field

		foreach($terms as $t){
			$t->$id_field = $dynamic_item_prefix . $t->term_id;
			$t->ID = $t->term_id;
			// $t->db_id = $t->term_id;
			$t->object = 'term';
			$t->object_id = $t->term_id;

			$t->title = apply_filters( 'jcs/item_title', $t->name, $t->ID, 'term' );
			$t->title = apply_filters( 'jcs/term_item_title', $t->title, $t->ID );

			$t->url = apply_filters( 'jcs/item_url', get_term_link( $t, $taxonomy ), $t->ID , 'term');
			$t->url = apply_filters( 'jcs/term_item_url', $t->url, $t->ID );

			$t->classes = array();
			
			if($t->parent == 0 || $t->parent == $tax_term){

				// remove childpop item
				if($replace_parent){	
					$t->$parent_field = $menu_item->$parent_field;
				}else{
					$t->$parent_field = $tax_parent_id;
				}
				
			}else{
				$t->$parent_field = $dynamic_item_prefix . $t->parent;
			}

			// add classes
			$t->classes = apply_filters( 'jcs/item_classes', $t->classes, $t->ID, 'term');
			$t->classes = apply_filters( 'jcs/term_item_classes', $t->classes, $t->ID);

			if((is_category() && is_category( $t->ID )) || (is_tag() && is_tag( $t->slug )) || is_tax( $taxonomy, $t->slug ) || ( is_singular() && has_term( $t->term_id, $taxonomy ) ) ){
				$t->classes[] = 'current-menu-item';
				$t->split_section = true;
				$t->current = 1;
			}
			
			$tax_elements[] = clone($t);	
		}

		// term depth
		if($tax_max_depth > 0){
			$tax_elements = $this->set_elements_depth($tax_elements, $tax_parent_id);
			
			foreach($tax_elements as $tag_key => $tag_elem){
				
				if($tag_elem->depth >= $tax_max_depth){
					unset($tax_elements[$tag_key]);
				}
			}
		}

		$this->dynamic_count++;

		return $tax_elements;
	}

	/**
	 * Generate Elements from post archive
	 * @param stdObj $menu_item Current Menu Item Being Populated
	 * @param array $page_parent_id Post type to populate with
	 * @param  bool $replace_parent
	 * @return array list of elements
	 */
	public function _populate_archive_items($menu_item, $post_type, $replace_parent = false){
		
		global $post, $wp;

		$current_url = add_query_arg( $wp->query_string, '', home_url( $wp->request ) );
		//quickfix
		if(strpos($current_url, '/?') === false && strpos($current_url, '?') !== false){
			$current_url = str_replace('?', '/?', $current_url);
		}

		$id_field = $this->db_fields['id'];
		$parent_field = $this->db_fields['parent'];
		$return = wp_get_archives( array('format' => 'custom', 'echo' => false));
		$test = preg_replace_callback('/<a href=["\']?(.*?)[\'"]?>(.*?)<\/a>/', array($this, 'extract_archive_month'), $return);
		$elements = array();
		$dynamic_item_prefix = str_repeat(0, $this->dynamic_count);
		$archive_parent_id = $menu_item->$id_field;
		$group_by_year = SubmenuModel::get_meta($archive_parent_id, 'archive-group');

		$id = 0;

		if(!empty($this->links)){

			$years = array();

			if($group_by_year){
				
				$this->get_archive_years();

				foreach($this->year_links as $year => $year_data){

					$id++;

					$element = new StdClass();
					$element->title = $year_data['title'];
					$element->url = $year_data['url'];
					$element->$id_field = $dynamic_item_prefix.$id;
					$element->ID = $dynamic_item_prefix.$id;

					$years[$year] = $dynamic_item_prefix.$id;

					// remove childpop item
					if($replace_parent){	
						$element->$parent_field = $menu_item->$parent_field;
					}else{
						$element->$parent_field = $menu_item->$id_field;	
					}

					if(is_year() && strcasecmp($current_url, $element->url) == 0){
						$element->current = 1;
						$element->classes[] = 'current-menu-item';
						$element->split_section = true;
					}

					$elements[] = clone($element);
				}
			}

			foreach($this->links as $link){
				
				$id++;

				$element = new StdClass();
				$element->title = $link['title'];
				$element->url = $link['url'];
				$element->$id_field = $dynamic_item_prefix.$id;
				$element->ID = $dynamic_item_prefix.$id;

				if(array_key_exists($link['year'], $years)){

					$element->$parent_field = $years[$link['year']];
				}else{
					
					// remove childpop item
					if($replace_parent){	
						$element->$parent_field = $menu_item->$parent_field;
					}else{
						$element->$parent_field = $menu_item->$id_field;	
					}	
				}

				if((is_month() || is_year()) && strcasecmp($current_url, $element->url) == 0){
					$element->current = 1;
					$element->classes[] = 'current-menu-item';
					$element->split_section = true;
				}

				$elements[] = clone($element);
			}

			
		}

		return $elements;
	}

	public function get_archive_years(){
		$return = wp_get_archives( array('format' => 'custom', 'echo' => false, 'type' => 'yearly'));
		$test = preg_replace_callback('/<a href=["\']?(.*?)[\'"]?>(.*?)<\/a>/', array($this, 'extract_archive_year'), $return);
		return $this->year_links;
	}

	var $links = array();
	var $year_links = array();
	var $years = array();

	public function extract_archive_year($data){
		if(isset($data[1]) && isset($data[2])){
			
			$year = $data[2];

			// create list of years
			if(!in_array($year, $this->years)){
				$this->years[] = $year;
			}

			$this->year_links[$year] = array('title' => $year, 'url' => $data[1]);
		}
		return '';
	}

	public function extract_archive_month($data){
		if(isset($data[1]) && isset($data[2])){
			
			// extract year and month from title
			$date = explode(' ', $data[2]);
			$month = $date[0];
			$year = $date[1];

			$this->links[] = array('title' => $data[2], 'url' => $data[1], 'year' => $year, 'month' => $month);
		}
		return '';
	}
}