<div class="wrap">
	<div id="icon-tools" class="icon32"><br></div>
	<h2>JC Submenu</h2>

	<form action="options.php" method="post" enctype="multipart/form-data">  
            <?php  
            settings_fields('jcs_settings');
            do_settings_sections('tab_settings');  
            // do_settings_sections($tabs[$tab])
            ?>  
            <p class="submit">  
                <input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />  
            </p>  
        </form> 

</div>