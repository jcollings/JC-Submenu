<?php 
/*
	Plugin Name: JC Submenu
	Plugin URI: https://wordpress.org/plugins/jc-submenu/
	Description: Wordpress Submenu Plugin, automatically populate your navigation menus with custom post_types, taxonomies, or child pages. An easy to use plugin created to be a lightweight menu extension.
	Version: 0.9.1
	Author: James Collings
	Author URI: https://www.jclabs.co.uk/
 */

/**
 * JC Submenu Class
 *
 * Core plugin file, load all required classes
 * 
 * @author James Collings <james@jclabs.co.uk>
 * @version 0.9.1
 */
class JCSubmenu{

	var $version = '0.9.1';
	var $version_check = 71;
	var $plugin_dir = false;
	var $plugin_url = false;
	var $prefix = 'jc-submenu';
	var $edit_walker = false;
	var $public_walker = true;

	/**
	 * Setup plugin
 	 * @return void
	 */
	function __construct(){

		$this->plugin_dir =  plugin_dir_path( __FILE__ );
		$this->plugin_url = plugins_url( '/', __FILE__ );

		$this->load_modules();

		// add plugin hooks
		add_action('jcs/menu_section', array($this, 'output_menu_section'), 10, 2);
		add_action('jcs/split_menu', array($this, 'output_split_menu'), 10, 2);

		// add plugin shortcodes
		add_shortcode( 'jcs_split_menu', array($this, 'split_menu_shortcode') );
		add_shortcode( 'jcs_menu_section', array($this, 'menu_section_shortcode') );

		// init menu attachment
		add_action('init', array($this, 'init'));
	}

	/**
	 * Set which type of attachment is used
	 * @return void
	 */
	public function init(){

		$this->public_walker = apply_filters('jcs/enable_public_walker', true );

		if(!$this->public_walker){
			add_filter( 'wp_nav_menu_objects', array( $this, 'populate_menu_items' ));
		}else{
			add_filter( 'wp_nav_menu_args', array( $this, 'attach_menu_walker' ));
		}
	}

	/**
	 * Attach custom nav walker
	 *
	 * Hook into theme menu, attach custom walker
	 * 
	 * @param  array $args 
	 * @return array       
	 */
	function attach_menu_walker($args){
		if(empty($args['walker'])){
			$args['walker'] = new JC_Submenu_Nav_Walker();
		}
		return $args;
	}

	/**
	 * Add menu items without using a custom walker
	 * 
	 * @param  array  $menu_items
	 * @return array new menu items
	 */
	function populate_menu_items($menu_items = array()){

		$walker = new JC_Submenu_Nav_Walker();
		$menu_items = $walker->attach_elements($menu_items);
		$menu_items = $walker->_process_menu($menu_items);

		return $menu_items;
	}

	/**
	 * Load Required Modules
	 * @return void 
	 */
	function load_modules(){

		include 'walkers/AdminMenuWalker.php';
		include 'walkers/SubmenuWalker.php';
		include 'walkers/DropdownWalker.php';
		include 'widgets/SectionMenuWidget.php';
		include 'widgets/SplitMenuWidget.php';
		include 'SubmenuModel.php';
		SubmenuModel::init($this);

		include 'SubmenuAdmin.php';
		new SubmenuAdmin($this);
	}

	/**
	 * Slit Menu Section Shortcode
	 *
	 * Display a dynamic split menu section via wordpress shortcode tags
	 * 
	 * @param  array $atts 
	 * @return string
	 */
	function split_menu_shortcode($atts){
		extract(shortcode_atts( array(
			'hierarchy' => 1,
			'start' => 0,
			'depth' => 5,
			'show_parent' => 0,
			'menu' => false,
			'trigger_depth' => 0
		), $atts ));

		if(!$menu)
			return false;

		ob_start();

		do_action('jcs/split_menu', $menu, array(
			'hierarchy' => $hierarchy,
			'start' => $start,
			'depth' => $depth,
			'show_parent' => $show_parent,
			'trigger_depth' => $trigger_depth
		));

		$output = ob_get_contents();
        ob_end_clean();
        return $output;
	}

	/**
	 * Menu Section Shortcode
	 *
	 * Display section of menu via wordpress shortcode tags
	 * 
	 * @param  array $atts 
	 * @return string
	 */
	function menu_section_shortcode($atts){
		extract(shortcode_atts( array(
			'hierarchy' => 1,
			'start' => 0,
			'depth' => 5,
			'show_parent' => 0,
			'menu' => false
		), $atts ));

		if(!$menu)
			return false;

		ob_start();

		do_action('jcs/menu_section', $menu, array(
			'hierarchy' => $hierarchy,
			'start' => $start,
			'depth' => $depth,
			'show_parent' => $show_parent,
		));

		$output = ob_get_contents();
        ob_end_clean();
        return $output;
	}

	/**
	 * Output menu section
	 *
	 * Display a section of the selected menu in your theme
	 * 
	 * @param  string $menu
	 * @param  array  $args
	 * @return void
	 */
	function output_menu_section($menu, $args = array()){

		$debug = isset($args['debug']) ? $args['debug'] : false;
		$hierarchical = isset($args['hierarchy']) ? $args['hierarchy'] : 1;
		$start = isset($args['start']) ? $args['start'] : 0;
		$depth = isset($args['depth']) ? $args['depth'] : 5;
		$show_parent = isset($args['show_parent']) ? $args['show_parent'] : 0;
		
		$options = array('menu' => $menu, 'walker' => new JC_Submenu_Nav_Walker(array(
			'debug' => $debug, 
			'section_menu' => true, 
			'menu_item' => $start, 
			'menu_depth' => $depth, 
			'show_parent' => $show_parent
			))
		);

		if(isset($args['menu_class']))
			$options['menu_class'] = $args['menu_class'];

		if(isset($args['menu_id']))
			$options['menu_id'] = $args['menu_id'];

		if(isset($args['container']))
			$options['container'] = $args['container'];

		if(isset($args['container_id']))
			$options['container_id'] = $args['container_id'];

		if(isset($args['container_class']))
			$options['container_class'] = $args['container_class'];

		wp_nav_menu($options);
	}

	/**
	 * Output Split Menu
	 *
	 * Display a dynamic section of the selected menu in your theme relative to your current page
	 * 
	 * @param  string $menu
	 * @param  array  $args
	 * @return void
	 */
	function output_split_menu($menu, $args = array()){

		$hierarchical = isset($args['hierarchy']) ? $args['hierarchy'] : 1;
		$menu_start = isset($args['start']) ? $args['start'] : 1;
		$menu_depth = isset($args['depth']) ? $args['depth'] : 5;
		$show_parent = isset($args['show_parent']) ? $args['show_parent'] : 0;
		$trigger_depth = isset($args['trigger_depth']) ? $args['trigger_depth'] : 0;
		$parent_label = isset($args['parent_label']) ? $args['parent_label'] : false;

		$options = array(
			'menu' => $menu, 'walker' => new JC_Submenu_Nav_Walker(array(
			'hierarchical' => $hierarchical,
			'menu_start' => $menu_start,
			'menu_depth' => $menu_depth,
			'show_parent' => $show_parent,
			'trigger_depth' => $trigger_depth,
			'parent_label' => $parent_label,
			'split_menu' => true
			))
		);

		if(isset($args['menu_class']))
			$options['menu_class'] = $args['menu_class'];

		if(isset($args['menu_id']))
			$options['menu_id'] = $args['menu_id'];

		if(isset($args['container']))
			$options['container'] = $args['container'];

		if(isset($args['container_id']))
			$options['container_id'] = $args['container_id'];

		if(isset($args['container_class']))
			$options['container_class'] = $args['container_class'];

		wp_nav_menu($options);
	}
}

$GLOBALS['jcsubmenu'] = new JCSubmenu();
