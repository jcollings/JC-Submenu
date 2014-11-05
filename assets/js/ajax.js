/**
 * Admin Ajax Function
 */
(function($){

	$.fn.jc_edit_menu = function(){

		var id = JC_Submenu.get_menu_id($(this).attr('id'));
		var _menu_item = $('#menu-item-'+id);
		
		if(_menu_item.hasClass('menu-item-edit-inactive') && !_menu_item.hasClass('jc-submenu-populated')){

			var id = JC_Submenu.get_menu_id(_menu_item.attr('id'));
			var data = {
				action: 'jcs_get_menu_item',
				id: id
			};

			// show loading 
			$("<div id='jc-submenu-loading'><strong>JC Submenu</strong>... Loading</div>").insertBefore(_menu_item.find('.menu-item-actions'));

			jQuery.post(ajax_object.ajax_url, data, function(response) {

				_menu_item.find("#jc-submenu-loading").remove();

				$(response).insertBefore(_menu_item.find('.menu-item-actions'));

				/**
				 * Display Active Menu Population Options
				 * @since 0.6
				 */
				_menu_item.find('.jc-accord-heading').each(function(){

					var accord_heading = $(this);
					var id = JC_Submenu.get_menu_id(accord_heading.attr('id'));
					var btn_handle = $('input', accord_heading);
					var btn_label = $('label', accord_heading);

					btn_handle.live('change', function(){

						$('.jc-accord-heading', $('#menu-item-'+id)).removeClass('active');
						$('.item-edit-panel', $('#menu-item-'+id)).hide();

						if($(this).attr('checked') == 'checked'){
							$( '.show-'+$(this).val() , $('#menu-item-'+id) ).show();
							accord_heading.addClass('active');
						}
					});

					btn_label.click(function(event){
						$('.jc-accord-heading input:checked', $('#menu-item-'+id)).attr('checked', false);
						btn_handle.attr('checked', 'checked').trigger('change');
						event.preventDefault();
					});

					btn_handle.filter(":checked").trigger('change');
				});
				
				/**
				 * Display JC Submenu Options
				 * @since 0.6
				 */
				_menu_item.find('input.jc-submenu-autopopulate').each(function(index){

					var options_handle = $(this);
					var id = JC_Submenu.get_menu_id(options_handle.attr('id'));

					options_handle.live('change', function(){
					    if($(this).attr('checked') == 'checked'){
					         $( '#jc-submenu-populate-block-'+id).show();
					         $('.jc-submenu-active').show();
					    }else{
					         $( '#jc-submenu-populate-block-'+id).hide();
					         $('.jc-submenu-active').hide();
					    }
					});

					options_handle.filter(':checked').trigger('change');
				});

				/**
				 * Filter Taxonomy via selected Post Type
				 * @since 0.6
				 */
				_menu_item.find('select[id^="edit-jc-submenu-populate-post"]').each(function(){

					var post_select = $(this);
					var id = JC_Submenu.get_menu_id(post_select.attr('id'));
					var tax_select = $('select[id^="edit-jc-submenu-post-tax"]', $('#menu-item-'+id));
					var term_select = $('select[id^="edit-jc-submenu-post-term"]', $('#menu-item-'+id));
					var taxs = tax_select.clone();


					post_select.live('change', function(){
						var show_taxs = post_select.find(':selected').data('taxs').split(' ');
						var selected = tax_select.find(':selected').val();

						// tax_select = taxs.clone();
						tax_select.empty();

						$('option', taxs).each(function(index){
							
							var val = $(this).val();

							if($.inArray(val, show_taxs) != -1 || val == 0){

								var clone_option = $(this).clone().attr('selected', false);

								if(val == selected){
									tax_select.append(clone_option.attr('selected', 'selected'));
								}else{
									tax_select.append(clone_option);	
								}
							}else{
								tax_select.find('option[value='+val+']').remove();
							}
						});

						// hide taxonomy filters if no options
						/*if(tax_select.find('option').size() == 1){
							tax_select.parent().hide();
						}else{
							tax_select.parent().show();
						}*/

						tax_select.trigger('change');
					});

					post_select.trigger('change');

				});
				
				/**
				 * Filter Terms depending on chosen taxonomy
				 * @since 0.6
				 */
				_menu_item.find('select[id^="edit-jc-submenu-post-tax"]').each(function(){

					var tax_select = $(this);
					var id = JC_Submenu.get_menu_id(tax_select.attr('id'));
					var term_select = $('select[id^="edit-jc-submenu-post-term"]', $('#menu-item-'+id));
					var terms = term_select.clone();

					tax_select.live('change', function(){

						var tax = tax_select.find(':selected').val();
						var selected = term_select.find(':selected').val();

						term_select.empty();

						$('option', terms).each(function(index){
							var val = $(this).val();

							if(tax == $(this).data('tax')){
								var clone_option = $(this).clone().attr('selected', false);
								if(val == selected){
									term_select.append(clone_option.attr('selected', 'selected'));
								}else{
									term_select.append(clone_option);	
								}
							}else{
								term_select.find('option[value='+val+']').remove();
							}
						});

						/*if(term_select.find('option').size() == 1){
							term_select.parent().hide();
						}else{
							term_select.parent().show();
						}*/

					});

					tax_select.trigger('change');
				});

				/**
				 * Filter Taxonomy Terms depending on chosen taxonomy
				 * @since  0.6
				 */
				_menu_item.find('select[id^="edit-jc-submenu-populate-tax"]').each(function(){

					var tax_select = $(this);
					var id = JC_Submenu.get_menu_id(tax_select.attr('id'));
					var term_select = $('select[id^="edit-jc-submenu-tax-term"]', $('#menu-item-'+id));
					var terms = term_select.clone();

					tax_select.live('change', function(){

						var tax = tax_select.find(':selected').val();
						var selected = term_select.find(':selected').val();

						term_select.empty();

						$('option', terms).each(function(index){
							var val = $(this).val();

							if(tax == $(this).data('tax') || $(this).data('tax') == 0){
								var clone_option = $(this).clone().attr('selected', false);
								if(val == selected){
									term_select.append(clone_option.attr('selected', 'selected'));
								}else{
									term_select.append(clone_option);	
								}
							}else{
								term_select.find('option[value='+val+']').remove();
							}
						});

						/*if(term_select.find('option').size() == 1){
							term_select.parent().hide();
						}else{
							term_select.parent().show();
						}*/

					});

					tax_select.trigger('change');
				});
			});

			_menu_item.addClass('jc-submenu-populated');
		}
	}

	/**
	* JS Attach Item Template
	*
	* add before .menu-item-actions
	*/
 	$(document).ready(function(){

 		$('#menu-to-edit').on('click', 'a.item-edit', function(){
 			$(this).jc_edit_menu();
 		});
 		
 	});


})(jQuery);