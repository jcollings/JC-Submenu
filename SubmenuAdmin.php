<?php
/**
 * Administration Menu Class
 *
 * Add custom options to nav-menu.php
 * 
 * @author James Collings <james@jclabs.co.uk>
 * @version 0.0.1
 */
class SubmenuAdmin{

	/**
	 * Plugin config
	 * @var stdClass
	 */
	private $config = null;

	/**
	 * Setup class
	 * @param stdClass $config
 	 * @return void
	 */
	public function __construct(&$config){
		$this->config = $config;

		// include js/css
		add_action( 'admin_enqueue_scripts', array($this, 'load_scripts'));

		// on menu save
		add_action( 'wp_update_nav_menu_item', array($this, 'save_nav_menu'), 10, 3);

		add_action( 'admin_notices', array( $this , 'display_admin_notification' ) );
		add_action( 'admin_init', array( $this, 'hide_admin_notification' ) );

		// register admin settings
		add_action( 'admin_init', array($this, 'register_settings' ));

		// add settings page
		add_action( 'admin_menu', array($this, 'settings_menu' ));

		// load settings
		$options = get_option( 'jcs-general_settings' );
		$this->config->edit_walker = isset($options['enable_walker']) && $options['enable_walker'] == 1 ? 1 : 0;

		if($this->config->edit_walker == 1){
			add_filter( 'wp_edit_nav_menu_walker', array($this, 'set_edit_walker'));
		}else{
			add_action( 'wp_ajax_jcs_get_menu_item', array( $this, 'ajax_get_menu_item' ) );
		}
	}

	/**
	 * Attach plugin assets
	 * @return void
	 */
	public function load_scripts(){

		// attach files
		wp_enqueue_script('jc-submenu-scripts', $this->config->plugin_url .'/assets/js/main.js', array('jquery'), $this->config->version, true);
		wp_enqueue_style('jc-submenu-admin-css', $this->config->plugin_url .'/assets/css/admin.css', array(), $this->config->version);

		// ajax files
		if($this->config->edit_walker == 0){
			wp_enqueue_script( 'jc-submenu-ajax', $this->config->plugin_url .'/assets/js/ajax.js', array('jquery'), $this->config->version );
			wp_localize_script( 'jc-submenu-ajax', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'we_value' => 1234 ) );
		}
	}

	public function settings_menu(){
		// add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );
		add_submenu_page( 'options-general.php', 'JC Submenu', 'JC Submenu', 'manage_options', 'jc-submenu', array($this, 'admin_settings_view') );
	}

	public function admin_settings_view(){
		include $this->config->plugin_dir . 'views/settings.php';
	}

	/**
	 * Save custom menu item options
	 * @param  int $menu_id         
	 * @param  int $menu_item_db_id 
	 * @param  array $args            
	 * @return void                  
	 */
	public function save_nav_menu($menu_id, $menu_item_db_id, $args){

		if(!isset($_POST['menu-item-title']) || empty($_POST['menu-item-title']))
			return false;

		foreach($_POST['menu-item-title'] as $menu_item_id => $menu_item_title){

			if(isset($_POST[$this->config->prefix.'-admin']) && array_key_exists($menu_item_id, $_POST[$this->config->prefix.'-admin'])){
				SubmenuModel::save_meta($menu_item_id, 'admin', 1);
			}elseif(isset($_POST[$this->config->prefix.'-active']) && array_key_exists($menu_item_id, $_POST[$this->config->prefix.'-active'])){
				SubmenuModel::save_meta($menu_item_id, 'admin', 0);
			}

			if(isset($_POST[$this->config->prefix.'-autopop']) && array_key_exists($menu_item_id, $_POST[$this->config->prefix.'-autopop'])){
				// save post meta for active items
				SubmenuModel::save_menu_item($menu_item_id);

			}elseif(isset($_POST[$this->config->prefix.'-active']) && array_key_exists($menu_item_id, $_POST[$this->config->prefix.'-active'])){
				// clear post meta for inactive items
				SubmenuModel::clear_menu_item($menu_item_id);
			}
		}
	}

	/**
	 * Output Usage Notification
	 * @return void
	 */
	public function display_admin_notification(){
		
		global $current_user;
		global $pagenow;

		$user_id = $current_user->ID;
		$response = get_user_meta($user_id, 'jcs-show_notification', true);

		if ( current_user_can( 'manage_options' ) && (!$response || $response < $this->config->version_check) && $pagenow == 'nav-menus.php'): ?>

		<div class="updated">
			<p>Need help with using JC Submenu? view the <a href="http://jamescollings.co.uk/wordpress-plugins/jc-submenu/" target="_blank">documentation here</a> | <a href="?jc_hide_notice=1">Hide this notice</a></p>
		</div>
		
		<?php
		endif;
	}

	/**
	 * Mark notification as viewed
	 * @return void
	 */
	public function hide_admin_notification(){
		
		global $current_user;
		$user_id = $current_user->ID;

		if(isset($_GET['jc_hide_notice']) && $_GET['jc_hide_notice'] == 1){

			delete_user_meta($user_id, 'jcs-show_notification');
			add_user_meta( $user_id, 'jcs-show_notification', $this->config->version_check, true);
		}

	}

	/**
	 * Change nav-menu.php walker
	 * @return  string
	 */
	public function set_edit_walker(){
		return 'JC_Submenu_Admin_Walker';
	}

	/**
	 * Load menu admin edit view
	 * @return void
	 */
	public function ajax_get_menu_item(){
		$item_id = intval($_POST['id']);
		include $this->config->plugin_dir . 'views/edit.php';
		die();
	}

	public function register_settings(){

        // Settings
        register_setting('jcs_settings', 'jcs'. '-general_settings', array($this, 'save_settings'));

        add_settings_section('settings', 'General Settings', array($this, 'section_settings'), 'tab_settings');

        add_settings_field('enable_walker', 'Enable Admin Walker', array($this, 'field_callback'), 'tab_settings', 'settings', array(
            'type' => 'checkbox',
            'field_id' => 'enable_walker',
            'section_id' => 'settings',
            'setting_id' => 'jcs'. '-general_settings'
        ));

    }

    /**
     * Settings Section Text
     * 
     * @return void 
     */
    public function section_settings($section)
    {
    	switch($section['id']){
    		case 'settings':
	    		echo 'Enable Admin Walker, only do this if you are having problems with editing your menus.';
    		break;
    	}
    	
    }

    /**
     * Create Settings Fields
     * 
     * @param  array $args 
     * @return void
     */
    public function field_callback($args){
        $multiple = false;
        extract($args);
        $options = get_option($setting_id);
        switch($args['type'])
        {
        	case 'checkbox':{
        		$checked = isset($options[$field_id]) && $options[$field_id] == 1 ? 'checked="checked"' : '';
        		?>
        		<input type="checkbox" class="checkbox" id="<?php echo $setting_id; ?>" name="<?php echo $setting_id; ?>[<?php echo $field_id; ?>]" value="1" <?php echo $checked; ?> />
        		<?php
        		break;
        	}
        }
    }

    /**
     * Save Settings
     * 
     * @param  array $args 
     * @return array
     */
    public function save_settings($args){

        return $args;
    }

}