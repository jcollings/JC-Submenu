<p class="jc-submenu-admin description description-wide">
	<label for="edit-jc-submenu-admin-<?php echo $item_id; ?>">
		<input type="checkbox" id="edit-jc-submenu-admin-<?php echo $item_id; ?>" value="1" class="jc-submenu-admin" name="jc-submenu-admin[<?php echo $item_id; ?>]" <?php if(intval(SubmenuModel::get_meta($item_id, 'admin')) == 1): ?>checked="checked"<?php endif; ?>>
		 Visible to Logged In Users Only
	</label>
</p>
<p class="jc-submenu-autopop description description-wide">
	<label for="edit-jc-submenu-autopop-<?php echo $item_id; ?>">
		<input type="checkbox" id="edit-jc-submenu-autopop-<?php echo $item_id; ?>" value="1" class="jc-submenu-autopopulate" name="jc-submenu-autopop[<?php echo $item_id; ?>]" <?php if(intval(SubmenuModel::get_meta($item_id, 'autopopulate')) == 1): ?>checked="checked"<?php endif; ?>>
		 <strong>JC Submenu</strong> - Automatically populate submenu
	</label>
</p>
<p class="jc-submenu-childpop description description-wide hidden jc-submenu-active">
	<label for="edit-jc-submenu-childpop-<?php echo $item_id; ?>">
		<input type="checkbox" id="edit-jc-submenu-childpop-<?php echo $item_id; ?>" value="1" class="jc-submenu-childpop" name="jc-submenu-childpop[<?php echo $item_id; ?>]" <?php if(intval(SubmenuModel::get_meta($item_id, 'childpop')) != 0): ?>checked="checked"<?php endif; ?>>
		 Replace Current item with Populated Items
	</label>
</p>

<input type="hidden" name="jc-submenu-active[<?php echo $item_id; ?>]" value="1" />

<div id="jc-submenu-populate-block-<?php echo $item_id; ?>" class="jc-submenu-populate-block description-wide hidden">

	
	<p id="accord-heading-post-<?php echo $item_id; ?>" class="jc-submenu-populate-post jc-accord-heading">
		<input type="radio" id="show-jc-submenu-populate-post-<?php echo $item_id; ?>" class="jc-submenu-populate-type" value="post" name="jc-submenu-populate-type[<?php echo $item_id; ?>]" <?php if(SubmenuModel::get_meta($item_id, 'populate-type') == 'post'): ?>checked="checked"<?php endif; ?> />
		<label for="show-jc-submenu-populate-post-<?php echo $item_id; ?>">Populate from post type</label>
	</p>

	<!-- Post Population Options -->
	<div class="item-edit-panel show-post hidden">
		<?php $p_types = get_post_types(array('public' => true)); ?>
		<div class="jc-submenu-row jc_two_cols">
			<p class="jc-submenu-populate-post">
				<?php $post_type_taxs = array(); ?>
				<label>Post Type</label>
				<select id="edit-jc-submenu-populate-post-<?php echo $item_id; ?>"name="jc-submenu-populate-post[<?php echo $item_id; ?>]">
				<?php foreach($p_types as $slug => $name): ?>
					<option value="<?php echo $slug; ?>" data-taxs="<?php echo implode(' ', get_object_taxonomies( $slug )) ?>" <?php if(SubmenuModel::get_meta($item_id, 'populate-value') == $slug): ?>selected="selected"<?php endif; ?>><?php echo $name; ?></option>
				<?php endforeach; ?>
				</select>
			</p>
		</div>

		<div class="jc-submenu-row jc_two_cols">
			<p class="jc-submenu-post-tax ">
				<label>Taxonomy</label>
				<?php $taxs = get_taxonomies( array('public' => true) ); ?>
				<select id="edit-jc-submenu-post-tax-<?php echo $item_id; ?>"name="jc-submenu-post-tax[<?php echo $item_id; ?>]">
					<option value="0">All</option>
					<?php foreach($taxs as $slug => $name): ?>
					<option value="<?php echo $slug; ?>" <?php if(SubmenuModel::get_meta($item_id, 'post-tax') == $slug): ?>selected="selected"<?php endif; ?>><?php echo $name; ?></option>
					<?php endforeach; ?>
				</select>
			</p>

			<p class="jc-submenu-post-term ">
				<label>Terms</label>
				<select id="edit-jc-submenu-post-term-<?php echo $item_id; ?>"name="jc-submenu-post-term[<?php echo $item_id; ?>]">
					<?php foreach($taxs as $slug => $name): ?>

						<?php $terms = get_terms( $slug, array('hide_empty' => false) ); ?>
						<?php foreach($terms as $term): ?>
							<option value="<?php echo $term->term_id; ?>" data-tax="<?php echo $slug; ?>" <?php if(SubmenuModel::get_meta($item_id, 'post-term') == $term->term_id): ?>selected="selected"<?php endif; ?>><?php echo $term->name; ?></option>
						<?php endforeach; ?>
					<?php endforeach; ?>
				</select>
			</p>
		</div>

		<?php $order_options = SubmenuModel::get_order_options('post'); 
		$orderby = array('ASC' => 'Ascending', 'DESC' => 'Descending' ); ?>
		<div class="jc-submenu-row jc_two_cols">
			<p class="jc-submenu-post-order ">
				<label>Order</label>
				<select id="edit-jc-submenu-post-orderby-<?php echo $item_id; ?>"name="jc-submenu-post-orderby[<?php echo $item_id; ?>]">
				<?php foreach($order_options as $value => $name): ?>
					<option value="<?php echo $value; ?>" <?php if(SubmenuModel::get_meta($item_id, 'post-orderby') == $value): ?>selected="selected"<?php endif; ?>><?php echo $name; ?></option>
				<?php endforeach; ?>
				</select>
			</p>
			<p class="jc-submenu-post-orderby ">
				<label>By</label>
				<select id="edit-jc-submenu-post-order-<?php echo $item_id; ?>"name="jc-submenu-post-order[<?php echo $item_id; ?>]">
				<?php foreach($orderby as $value => $name): ?>
					<option value="<?php echo $value; ?>" <?php if(SubmenuModel::get_meta($item_id, 'post-order') == $value): ?>selected="selected"<?php endif; ?>><?php echo $name; ?></option>
				<?php endforeach; ?>
				</select>
			</p>
		</div>
		<?php 
		$post_limit = SubmenuModel::get_meta($item_id, 'post-limit');
		if(!$post_limit){
			$post_limit = 0;
		}
		?>
		<div class="jc-submenu-row jc_two_cols">
			<p class="jc-submenu-post-limit ">
				<label>Post Limit</label>
				<input type="text" id="edit-jc-submenu-post-limit-<?php echo $item_id; ?>"name="jc-submenu-post-limit[<?php echo $item_id; ?>]" value="<?php echo $post_limit; ?>" />
			</p>
		</div>
	</div>
	<!-- End of Post Population Option -->


	<p id="accord-heading-tax-<?php echo $item_id; ?>" class="jc-submenu-populate-tax jc-accord-heading">
		<input type="radio" id="show-jc-submenu-populate-tax-<?php echo $item_id; ?>" class="jc-submenu-populate-type" value="tax" name="jc-submenu-populate-type[<?php echo $item_id; ?>]" <?php if(SubmenuModel::get_meta($item_id, 'populate-type') == 'tax'): ?>checked="checked"<?php endif; ?> />
		<label for="show-jc-submenu-populate-tax<?php echo $item_id; ?>">Populate from taxonomy</label>
	</p>

	<!-- Taxonomy Population Options -->
	<div class="item-edit-panel show-tax hidden">
		<?php $taxs = get_taxonomies( array('public' => true) ); ?>
		<div class="jc-submenu-row jc_two_cols">

			<p class="jc-submenu-populate-tax ">
				<label>Taxonomies</label>
				<select id="edit-jc-submenu-populate-tax-<?php echo $item_id; ?>"name="jc-submenu-populate-tax[<?php echo $item_id; ?>]">
				<?php foreach($taxs as $slug => $name): ?>
					<?php if($slug == 'post_format'){ continue; } ?>
					<option value="<?php echo $slug; ?>" <?php if(SubmenuModel::get_meta($item_id, 'populate-value') == $slug): ?>selected="selected"<?php endif; ?>><?php echo $name; ?></option>
				<?php endforeach; ?>
				</select>
			</p>

			<p class="jc-submenu-tax-term ">
				<label>Terms</label>
				<select id="edit-jc-submenu-tax-term-<?php echo $item_id; ?>"name="jc-submenu-tax-term[<?php echo $item_id; ?>]">
					<option value="0" data-tax="0">All</option>
					<?php foreach($taxs as $slug => $name): ?>

						<?php $terms = get_terms( $slug, array('hide_empty' => false) ); ?>
						<?php foreach($terms as $term): ?>
							<option value="<?php echo $term->term_id; ?>" data-tax="<?php echo $slug; ?>" <?php if(SubmenuModel::get_meta($item_id, 'tax-term') == $term->term_id): ?>selected="selected"<?php endif; ?>><?php echo $term->name; ?></option>
						<?php endforeach; ?>
					<?php endforeach; ?>
				</select>
			</p>
		</div>
		<?php 
		$order_options = SubmenuModel::get_order_options('tax');
		$orderby = array('ASC' => 'Ascending', 'DESC' => 'Descending' ); ?>
		<div class="jc-submenu-row jc_two_cols">
			<p class="jc-submenu-tax-orderby ">
				<label>Order</label>
				<select id="edit-jc-submenu-tax-orderby-<?php echo $item_id; ?>"name="jc-submenu-tax-orderby[<?php echo $item_id; ?>]">
				<?php foreach($order_options as $value => $name): ?>
					<option value="<?php echo $value; ?>" <?php if(SubmenuModel::get_meta($item_id, 'tax-orderby') == $value): ?>selected="selected"<?php endif; ?>><?php echo $name; ?></option>
				<?php endforeach; ?>
				</select>
			</p>
			<p class="jc-submenu-tax-order ">
				<label>By</label>
				<select id="edit-jc-submenu-tax-order-<?php echo $item_id; ?>"name="jc-submenu-tax-order[<?php echo $item_id; ?>]">
				<?php foreach($orderby as $value => $name): ?>
					<option value="<?php echo $value; ?>" <?php if(SubmenuModel::get_meta($item_id, 'tax-order') == $value): ?>selected="selected"<?php endif; ?>><?php echo $name; ?></option>
				<?php endforeach; ?>
				</select>
			</p>
		</div>
		<div class="jc-submenu-row jc_two_cols">
			<p class="jc-submenu-tax-exclude ">
				<?php
				$tax_exclude = SubmenuModel::get_meta($item_id, 'tax-exclude');
				$tax_exclude = $tax_exclude ? implode(',', $tax_exclude) : '';
				?>
				<label>Exclude Terms (CSV of ID's)</label>
				<input type="text" id="edit-jc-submenu-tax-exclude-<?php echo $item_id; ?>"name="jc-submenu-tax-exclude[<?php echo $item_id; ?>]" value="<?php echo $tax_exclude; ?>" />
			</p>
			<p class="jc-submenu-tax-depth ">
				<label>Depth</label>
				<input type="text" id="edit-jc-submenu-tax-depth-<?php echo $item_id; ?>"name="jc-submenu-tax-depth[<?php echo $item_id; ?>]" value="<?php echo SubmenuModel::get_meta($item_id, 'tax-depth'); ?>" />
			</p>
		</div>
		<div class="jc-submenu-row jc_two_cols">
			<p class="jc-submenu-tax-empty ">
				<input type="checkbox" value="1" name="jc-submenu-tax-empty[<?php echo $item_id; ?>]" id="edit-jc-submenu-tax-empty-<?php echo $item_id; ?>" <?php if(SubmenuModel::get_meta($item_id, 'tax-empty') == 1): ?> checked="checked"<?php endif; ?>>
				Hide Empty Terms
			</p>
		</div>
	</div>

	<!-- End of Taxonomy Population Options -->

	<p id="accord-heading-page-<?php echo $item_id; ?>" class="jc-submenu-populate-page jc-accord-heading">
		<input type="radio" id="show-jc-submenu-populate-page-<?php echo $item_id; ?>" class="jc-submenu-populate-type" value="page" name="jc-submenu-populate-type[<?php echo $item_id; ?>]" <?php if(SubmenuModel::get_meta($item_id, 'populate-type') == 'page'): ?>checked="checked"<?php endif; ?> />
		<label for="show-jc-submenu-populate-page-<?php echo $item_id; ?>">Populate from child pages</label>
	</p>

	<!-- Page Population Options -->
	<div class="item-edit-panel show-page hidden">
		<div class="jc-submenu-row jc_two_cols">
			<p class="jc-submenu-populate-page ">
				<label>Parent Page</label>
				<?php 
				wp_dropdown_pages(array(
					'id' => 'edit-jc-submenu-populate-page-'.$item_id,
					'name' => 'jc-submenu-populate-page['.$item_id.']',
					'selected' => SubmenuModel::get_meta($item_id, 'populate-value')
				));
				?>
			</p>
		</div>

		<?php $order_options = SubmenuModel::get_order_options('page'); ?>
		<div class="jc-submenu-row jc_two_cols">
			<p class="jc-submenu-page-order ">
				<label>Order</label>
				<select id="edit-jc-submenu-page-orderby-<?php echo $item_id; ?>"name="jc-submenu-page-orderby[<?php echo $item_id; ?>]">
				<?php foreach($order_options as $value => $name): ?>
					<option value="<?php echo $value; ?>" <?php if(SubmenuModel::get_meta($item_id, 'page-orderby') == $value): ?>selected="selected"<?php endif; ?>><?php echo $name; ?></option>
				<?php endforeach; ?>
				</select>
			</p>
			<p class="jc-submenu-page-orderby ">
				<label>By</label>
				<select id="edit-jc-submenu-page-order-<?php echo $item_id; ?>"name="jc-submenu-page-order[<?php echo $item_id; ?>]">
				<?php foreach($orderby as $value => $name): ?>
					<option value="<?php echo $value; ?>" <?php if(SubmenuModel::get_meta($item_id, 'page-order') == $value): ?>selected="selected"<?php endif; ?>><?php echo $name; ?></option>
				<?php endforeach; ?>
				</select>
			</p>
		</div>

		<div class="jc-submenu-row jc_two_cols">
			<p class="jc-submenu-page-exclude ">
				<label>Exclude Pages</label>
				<?php 
				$pages = get_pages();
				$options = SubmenuModel::get_meta($item_id, 'page-exclude');
				?>
				<select id="edit-jc-submenu-page-exclude<?php echo $item_id; ?>"name="jc-submenu-page-exclude[<?php echo $item_id; ?>][]" multiple="multiple">
					<?php
					foreach($pages as $page){
						if(is_array($options) && in_array($page->ID, $options)){
							echo '<option value="'.$page->ID.'" selected="selected">'.$page->post_title.' (ID: '.$page->ID.')</option>';
						}else{
							echo '<option value="'.$page->ID.'">'.$page->post_title.' (ID: '.$page->ID.')</option>';	
						}
						
					}
					?>
				</select>
			</p>
		</div>
	</div>
	<!-- End of Page Population Options -->

	<p id="accord-heading-archive-<?php echo $item_id; ?>" class="jc-submenu-populate-archive jc-accord-heading">
		<input type="radio" id="show-jc-submenu-populate-archive-<?php echo $item_id; ?>" class="jc-submenu-populate-type" value="archive" name="jc-submenu-populate-type[<?php echo $item_id; ?>]" <?php if(SubmenuModel::get_meta($item_id, 'populate-type') == 'archive'): ?>checked="checked"<?php endif; ?> />
		<label for="show-jc-submenu-populate-archive-<?php echo $item_id; ?>">Populate from Date Archives</label>
	</p>

	<!-- Page Population Options -->
	<div class="item-edit-panel show-archive hidden">
		<div class="jc-submenu-row jc_two_cols">
			<input type="hidden" name="jc-submenu-populate-archive[<?php echo $item_id; ?>]" id="edit-jc-submenu-populate-archive-<?php echo $item_id; ?>" value="post" />
		</div>

		<div class="jc-submenu-row jc_two_cols">
			<p class="jc-submenu-archive-group ">
				<input type="checkbox" value="1" name="jc-submenu-archive-group[<?php echo $item_id; ?>]" id="edit-jc-submenu-archive-group-<?php echo $item_id; ?>" <?php if(SubmenuModel::get_meta($item_id, 'archive-group') == 1): ?> checked="checked"<?php endif; ?>>
				Group by Year
			</p>
		</div>
	</div>
</div>