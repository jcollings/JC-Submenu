<?php 
/**
 * Submenu Model
 *
 * class to interact with the database
 * 
 * @author James Collings <james@jclabs.co.uk>
 * @version 0.0.1
 */
class SubmenuModel{

	/**
	 * Plugin config
	 * @var stdClass
	 */
	static $config;

	/**
	 * Order by options
	 * @var array
	 */
	private static $order_options = array(
		'tax' => array(
			'name' => 'Name',
			'slug' => ' Slug',
			'count' => 'Tax Count',
			'id' => 'ID',
			'none' => 'None',
		),
		'post' => array(
			'title' => 'Post Name',
			'name' => 'Post Slug',
			'none' => 'None',
			'ID' => 'ID',
			'date' => 'Date',
			'comment_count' => 'Total Comments',
			'menu_order' => 'Menu Order'
		),
		'page' => array(
			'post_title' => 'Page Name',
			'menu_order' => 'Menu Order',
			'post_date' => 'Created',
			'post_modified' => 'Last Modified',
			'ID' => 'Page ID',
			'post_author' => 'Author',
			'post_name' => 'Page Slug'
		)
	);

	/**
	 * Setup class
	 * @param stdClass $config
	 * @return void
	 */
	static function init(&$config){
		self::$config = $config;
	}

	/**
	 * Save Menu item from wp admin navigation page
	 * @param  int $menu_item_id 
	 * @param  array  $args         
	 * @return void
	 */
	static function save_menu_item($menu_item_id = null, $args = array()){
		$type = self::get_post_data($menu_item_id, 'populate-type');
		$value = self::get_post_data($menu_item_id, 'populate-'.$type);

		$childpop = self::get_post_data($menu_item_id, 'childpop');
		$childpop = $childpop == 0 ? 0 : 1;

		if($type && $value){

			// validate population type
			if(!in_array($type, array('post','tax', 'page', 'archive'))){
				return 0;
			}

			if($type == 'post'){
				$post_limit = self::get_post_data($menu_item_id, 'post-limit');
				$post_order = self::get_post_data($menu_item_id, 'post-order');
				$post_orderby = self::get_post_data($menu_item_id, 'post-orderby');
				$post_tax = self::get_post_data($menu_item_id, 'post-tax');
				$post_term = self::get_post_data($menu_item_id, 'post-term');

				$post_tax = !empty($post_tax) ? $post_tax : 0;
				$post_term = !empty($post_term) ? $post_term : 0;


				$post_limit = intval($post_limit);

				// validate order
				if(!array_key_exists($post_orderby, self::get_order_options('post'))){
					echo $post_orderby;
					echo 'A';
					return 0;
				}
				if(!in_array($post_order, array('ASC', 'DESC'))){
					echo 'B';
					return 0;
				}

				self::save_meta($menu_item_id, 'post-limit', $post_limit);
				self::save_meta($menu_item_id, 'post-order', $post_order);
				self::save_meta($menu_item_id, 'post-orderby', $post_orderby);
				self::save_meta($menu_item_id, 'post-tax', $post_tax);
				self::save_meta($menu_item_id, 'post-term', $post_term);
				// self::save_post_tax($menu_item_id, $post_tax);
			}elseif($type == 'tax'){
				$tax_order = self::get_post_data($menu_item_id, 'tax-order');
				$tax_orderby = self::get_post_data($menu_item_id, 'tax-orderby');
				$tax_empty = self::get_post_data($menu_item_id, 'tax-empty');
				$tax_depth = self::get_post_data($menu_item_id, 'tax-depth');
				$tax_term = self::get_post_data($menu_item_id, 'tax-term');

				$tax_term = !empty($tax_term) ? $tax_term : 0;
				
				$tax_exclude = self::get_post_data($menu_item_id, 'tax-exclude');
				if(!empty($tax_exclude)){
					
					$temp = array();
					$tax_exclude = explode(',', str_replace(' ', '', $tax_exclude));
					
					foreach($tax_exclude as $id){
						if(intval($id) > 0 && !in_array(intval($id), $temp)){
							$temp[] = intval($id);	
						}
					}
					$tax_exclude = $temp;
				}else{
					$tax_exclude = array();
				}
				

				$tax_empty = $tax_empty == 1 ? 1 : 0; 

				// validate order
				if(!array_key_exists($tax_orderby, self::get_order_options('tax'))){
					return 0;
				}
				if(!in_array($tax_order, array('ASC', 'DESC'))){
					return 0;
				}

				self::save_meta($menu_item_id, 'tax-order', $tax_order);
				self::save_meta($menu_item_id, 'tax-orderby', $tax_orderby);
				self::save_meta($menu_item_id, 'tax-empty', $tax_empty);
				self::save_meta($menu_item_id, 'tax-depth', intval($tax_depth));
				self::save_meta($menu_item_id, 'tax-exclude', $tax_exclude);
				self::save_meta($menu_item_id, 'tax-term', $tax_term);
			}elseif($type == 'page'){

				$page_order = self::get_post_data($menu_item_id, 'page-order');
				$page_orderby = self::get_post_data($menu_item_id, 'page-orderby');
				$page_exclude = self::get_post_data($menu_item_id, 'page-exclude');

				// validate order
				if(!array_key_exists($page_orderby, self::get_order_options('page'))){
					return 0;
				}
				if(!in_array($page_order, array('ASC', 'DESC'))){
					return 0;
				}

				self::save_meta($menu_item_id, 'page-order', $page_order);
				self::save_meta($menu_item_id, 'page-orderby', $page_orderby);
				self::save_meta($menu_item_id, 'page-exclude', $page_exclude);
			}elseif($type == 'archive'){

				$archive_group = self::get_post_data($menu_item_id, 'archive-group');
				$archive_group = $archive_group == 1 ? 1 : 0; 
				self::save_meta($menu_item_id, 'archive-group', $archive_group);
			}

			// all validated save rest
			self::save_meta($menu_item_id, 'populate-type', $type);
			self::save_meta($menu_item_id, 'populate-value', $value);
			self::save_meta($menu_item_id, 'autopopulate', 1);
			self::save_meta($menu_item_id, 'childpop', $childpop);
		}

	}

	/**
	 * Save post type taxonomy filter
	 * @param $menu_item_id
	 * @param $string
	 */
	static function save_post_tax($menu_item_id = 0, $string = ''){

		// no string to parse
		if(empty($string)){
			self::save_meta($menu_item_id, 'post-tax', 0);
			self::save_meta($menu_item_id, 'post-term', 0);
			return false;
		}
			

		// save tax only
		if(strpos($string, '-') === false  && (taxonomy_exists( $string ) || $string == 0 ) ){
			self::save_meta($menu_item_id, 'post-tax', $string);
			self::save_meta($menu_item_id, 'post-term', 0);
			return true;
		}

		// save term and tax
		$split = explode('-', $string);

		if(count($split) == 2){
			$term_id = intval($split[1]);
			$tax = $split[0];

			if(taxonomy_exists( $tax ) && (get_term_by( 'id', $term_id, $tax) || $term_id == 0) ){
				self::save_meta($menu_item_id, 'post-tax', $tax);
				self::save_meta($menu_item_id, 'post-term', $term_id);
				return true;
			}
		}

		return false;
	}

	/**
	 * Capture menu item $_POST data
	 * @param $menu_item_id
	 * @param $key
	 */
	static function get_post_data($menu_item_id = 0, $key = ''){
		if(isset($_POST[self::$config->prefix.'-'.$key][$menu_item_id]) 
			&& !empty($_POST[self::$config->prefix.'-'.$key][$menu_item_id])){
			return $_POST[self::$config->prefix.'-'.$key][$menu_item_id];
		}else{
			return '';
		};
	}

	/**
	 * Update post meta data
	 * @param int $menu_item_id
	 * @param string $meta_key
	 * @param string $new_value
	 */
	static function save_meta($menu_item_id = 0, $meta_key = '', $new_value = ''){
		
		$key = self::$config->prefix.'-'.$meta_key;

		$old_value = get_post_meta( $menu_item_id, $key, true );
		if($old_value == ''){

			// fix to remove multiple saved values as add_post_meta was not set to unique... woops
			global $wpdb;
			$wpdb->query( $wpdb->prepare( "DELETE pm FROM {$wpdb->prefix}postmeta as pm WHERE pm.post_id = %d AND meta_key=%s ", $menu_item_id , $key ) );

			// add value
			add_post_meta( $menu_item_id, $key, $new_value, true );
		}else{
			// update value
			update_post_meta( $menu_item_id, $key, $new_value, $old_value );
		}
	}

	/**
	 * Remove menu item settings
	 * @param  int $menu_item_id 
	 * @return void 
	 */
	static function clear_menu_item($menu_item_id = null){
		$keys = array(
			'populate-type',
			'populate-value',
			'autopopulate',
			'post-limit',
			'post-order',
			'post-orderby',
			'tax-order',
			'tax-orderby',
			'tax-empty',
			'post-tax',
			'post-term',
			'childpop',
			'archive-group'
		);
		
		foreach($keys as $meta_key){
			$old_value = get_post_meta( $menu_item_id, self::$config->prefix.'-'.$meta_key, true );
			if($old_value != ''){
				delete_post_meta( $menu_item_id, self::$config->prefix.'-'.$meta_key, $old_value );
			}
		}
	}

	/**
	 * Retrieve orderby options
	 * @param string $type tax | post
	 */
	static function get_order_options($type){

		switch($type){
			case 'tax':
				return self::$order_options['tax'];
			break;
			case 'post':
				return self::$order_options['post'];
			break;
			case 'page':
				return self::$order_options['page'];
			break;
		}
	}

	/**
	 * Get plugin meta item
	 * @param  int $menu_item_id 
	 * @param  string $key          
	 * @return string               
	 */
	static function get_meta($menu_item_id, $key){
		return get_post_meta( $menu_item_id, self::$config->prefix.'-'.$key, true );
	}
}
?>