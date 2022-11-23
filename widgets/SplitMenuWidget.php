<?php
/**
 * Submenu Widget Class
 *
 * Display Wordpress Submenu Widget
 *
 * @author James Collings <james@jclabs.co.uk>
 * @version 0.0.1
 */
class JC_Split_Menu_Widget extends WP_Widget {
 	
 	/**
 	 * Register Widget
 	 */
	public function __construct() {
		parent::__construct(
	 		'jc_split_menu_widget', // Base ID
			'Split Menu Widget', // Name
			array( 'description' => __( 'JC Submenu Split Menu Widget, display a section of a the current menu.')) // Args
		);
	}
 	
 	/**
 	 * Widget Output
 	 * @param  array $args     
 	 * @param  array $instance 
 	 * @return void           
 	 */
	public function widget( $args, $instance ) {

		extract( $args );
 
		ob_start();

		do_action('jcs/split_menu', $instance['menu'], array( 
			'hierarchy' => $instance['menu_hierarchy'],
			'start' => $instance['menu_start'],
			'depth' => $instance['menu_depth'],
			'show_parent' => $instance['show_parent'],
			'trigger_depth' => $instance['trigger_depth'],
		));

		$widget = ob_get_contents();
        ob_end_clean();

        $title = apply_filters( 'widget_title', $instance['title'] );
        $title = apply_filters( 'jcs/split_widget_title', $title );

        // hide if no menu items appear
        if(empty($widget))
        	return;
 
		echo $before_widget;
 
		if ( ! empty( $title ) )
			echo $before_title . $title . $after_title;

		echo $widget;
 
		echo $after_widget;
	}
 	
 	/**
 	 * Widget Options
 	 * @param  array $instance 
 	 * @return void
 	 */
 	public function form( $instance ) {
 
		$title = isset($instance['title']) ? $instance['title'] : '';
		$menu = isset($instance['menu']) ? $instance['menu'] : '';
		$menu_hierarchy = isset($instance['menu_hierarchy']) ? $instance['menu_hierarchy'] : 1;
		$show_parent = isset($instance['show_parent']) ? $instance['show_parent'] : 1;
		$menu_start = isset($instance['menu_start']) ? $instance['menu_start'] : 1;
		$menu_depth = isset($instance['menu_depth']) ? $instance['menu_depth'] : 5;
		$trigger_depth = isset($instance['trigger_depth']) ? $instance['trigger_depth'] : 0;
		$menus = get_terms('nav_menu');
 
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
 
		<p>
		<input class="widefat" id="<?php echo $this->get_field_id( 'menu' ); ?>" name="<?php echo $this->get_field_name( 'menu' ); ?>" type="hidden" value="<?php echo $menu; ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'menu' ); ?>"><?php _e( 'Select Menu:' ); ?></label> 
			<select class="widefat" id="<?php echo $this->get_field_id( 'menu' ); ?>" name="<?php echo $this->get_field_name( 'menu' ); ?>" >

				<?php foreach($menus as $m){
					$selected = '';
					if($m->slug == $menu){
						$selected = ' selected="selected"';
					}
					echo '<option value="'.$m->slug.'"'.$selected.'>'.$m->name.'</option>';
				} ?>
			</select>
		</p>

		<?php $max_level = 5; ?>

		<p>
			<label for="<?php echo $this->get_field_id( 'menu_start' ); ?>"><?php _e( 'Start Level:' ); ?></label> 
			<select class="widefat" id="<?php echo $this->get_field_id( 'menu_start' ); ?>" name="<?php echo $this->get_field_name( 'menu_start' ); ?>" >

				<?php 
				for($x=1; $x <= $max_level; $x++){
					$selected = '';
					if($x == $menu_start){
						$selected = ' selected="selected"';
					}
					echo '<option value="'.$x.'"'.$selected.'>'.$x.'</option>';
				}
				?>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'menu_depth' ); ?>"><?php _e( 'Menu Depth:' ); ?></label> 
			<select class="widefat" id="<?php echo $this->get_field_id( 'menu_depth' ); ?>" name="<?php echo $this->get_field_name( 'menu_depth' ); ?>" >

				<?php 
				for($x=1; $x <= $max_level; $x++){
					$selected = '';
					if($x == $menu_depth){
						$selected = ' selected="selected"';
					}
					echo '<option value="'.$x.'"'.$selected.'>'.$x.'</option>';
				}
				?>
			</select>
		</p>

		<?php $max_level = 5; ?>

		<p>
			<label for="<?php echo $this->get_field_id( 'trigger_depth' ); ?>"><?php _e( 'Trigger Depth:' ); ?></label> 
			<select class="widefat" id="<?php echo $this->get_field_id( 'trigger_depth' ); ?>" name="<?php echo $this->get_field_name( 'trigger_depth' ); ?>" >

				<?php 
				for($x=0; $x <= $max_level; $x++){
					$selected = '';
					if($x == $trigger_depth){
						$selected = ' selected="selected"';
					}
					echo '<option value="'.$x.'"'.$selected.'>'.$x.'</option>';
				}
				?>
			</select>
		</p>
 
		<p>
			<input id="<?php echo $this->get_field_id( 'show_parent' ); ?>" name="<?php echo $this->get_field_name( 'show_parent' ); ?>" type="checkbox" value="1" <?php if($show_parent == 1): ?>checked="checked"<?php endif; ?> />
			<label for="<?php echo $this->get_field_id( 'show_parent' ); ?>">Show Parent</label>
		</p>

		<p>
			<input id="<?php echo $this->get_field_id( 'menu_hierarchy' ); ?>" name="<?php echo $this->get_field_name( 'menu_hierarchy' ); ?>" type="checkbox" value="1" <?php if($menu_hierarchy == 1): ?>checked="checked"<?php endif; ?> />
			<label for="<?php echo $this->get_field_id( 'menu_hierarchy' ); ?>">Show Hierarchy</label>
		</p>
		<?php 
	}
 	
 	/**
 	 * Save Widget Options
 	 * @param  array $new_instance 
 	 * @param  array $old_instance 
 	 * @return array
 	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
 
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['menu'] = strip_tags( $new_instance['menu'] );
		$instance['menu_start'] = intval($new_instance['menu_start']);
		$instance['menu_depth'] = intval($new_instance['menu_depth']);
		$instance['menu_hierarchy'] = intval( $new_instance['menu_hierarchy'] );
		$instance['show_parent'] = intval( $new_instance['show_parent'] );
		$instance['trigger_depth'] = intval( $new_instance['trigger_depth'] );
 
		return $instance;
	}
 
}

function register_jc_split_menu_widget(){
	register_widget( "JC_Split_Menu_Widget" );
}
add_action( 'widgets_init', 'register_jc_split_menu_widget' );