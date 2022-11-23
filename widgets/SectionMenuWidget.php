<?php
/**
 * Submenu Widget Class
 *
 * Display Wordpress Submenu Widget
 *
 * @author James Collings <james@jclabs.co.uk>
 * @version 0.0.1
 */
class JC_Section_Menu_Widget extends WP_Widget {
 	
 	/**
 	 * Register Widget
 	 */
	public function __construct() {
		parent::__construct(
	 		'jc_section_menu_widget', // Base ID
			'Section Menu Widget', // Name
			array( 'description' => __( 'JC Submenu Menu Section Widget, display a section of a the current menu.')) // Args
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
 
		$title = apply_filters( 'widget_title', $instance['title'] );
 
		echo $before_widget;
 
		if ( ! empty( $title ) )
			echo $before_title . $title . $after_title;

		do_action('jcs/menu_section', $instance['menu'], array( 
			'start' => $instance['menu_item'],
			'depth' => $instance['menu_depth'],
			'show_parent' => $instance['show_parent'],
		));
 
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
		$menu_item = isset($instance['menu_item']) ? $instance['menu_item'] : 0;
		$menu_depth = isset($instance['menu_depth']) ? $instance['menu_depth'] : 1;
		$show_parent = isset($instance['show_parent']) ? $instance['show_parent'] : 0;
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
			<label for="<?php echo $this->get_field_id( 'menu_item' ); ?>"><?php _e( 'Select Menu Part:' ); ?></label> 
			<select class="widefat" id="<?php echo $this->get_field_id( 'menu_item' ); ?>" name="<?php echo $this->get_field_name( 'menu_item' ); ?>" >
				<?php foreach($menus as $m){

					if($menu == $m->slug && $menu_item == 0){
						$items_wrap = '<optgroup id="'.$m->slug.'" label="'.$m->name.'"><option value="0" selected="selected">Root</option>%3$s</optgroup>';
					}else{
						$items_wrap = '<optgroup id="'.$m->slug.'" label="'.$m->name.'"><option value="0">Root</option>%3$s</optgroup>';
					}

					wp_nav_menu(array(
					  'menu' => $m->slug, // your theme location here
					  'container' => false,
					  'walker'         => new JC_Walker_Nav_Menu_Dropdown($menu_item),
					  'items_wrap'     => $items_wrap,
					));
				} ?>
			</select>
		</p>

		<?php $max_level = 5; ?>
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

		<p>
			<input id="<?php echo $this->get_field_id( 'show_parent' ); ?>" name="<?php echo $this->get_field_name( 'show_parent' ); ?>" type="checkbox" value="1" <?php if($show_parent == 1): ?>checked="checked"<?php endif; ?> />
			<label for="<?php echo $this->get_field_id( 'show_parent' ); ?>">Show Parent</label>
		</p>

		<script type="text/javascript">
		jQuery(document).ready(function($) {
			$('select#<?php echo $this->get_field_id( 'menu_item' ); ?>').change(function(){
				var label=$('select#<?php echo $this->get_field_id( 'menu_item' ); ?> :selected').parent().attr('id');
				console.log(label);
			    $('#<?php echo $this->get_field_id( 'menu' ); ?>').val(label);
			});
		});
		</script>


	 	<?php
	 	/*
		
		
 
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
				for($x=0; $x <= $max_level; $x++){
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
 
		<p>
			<input id="<?php echo $this->get_field_id( 'show_parent' ); ?>" name="<?php echo $this->get_field_name( 'show_parent' ); ?>" type="checkbox" value="1" <?php if($show_parent == 1): ?>checked="checked"<?php endif; ?> />
			<label for="<?php echo $this->get_field_id( 'show_parent' ); ?>">Show Parent</label>
		</p>

		<p>
			<input id="<?php echo $this->get_field_id( 'menu_hierarchy' ); ?>" name="<?php echo $this->get_field_name( 'menu_hierarchy' ); ?>" type="checkbox" value="1" <?php if($menu_hierarchy == 1): ?>checked="checked"<?php endif; ?> />
			<label for="<?php echo $this->get_field_id( 'menu_hierarchy' ); ?>">Show Hierarchy</label>
		</p>
		<?php 
		*/
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
		$instance['menu_item'] = intval($new_instance['menu_item']);
		$instance['menu_depth'] = intval($new_instance['menu_depth']);
		$instance['show_parent'] = intval( $new_instance['show_parent'] );
 
		return $instance;
	}
 
}

function register_jc_section_menu_widget(){
	register_widget( "JC_Section_Menu_Widget" );
}
add_action( 'widgets_init', 'register_jc_section_menu_widget' );