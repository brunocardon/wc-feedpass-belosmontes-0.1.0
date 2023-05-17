<?php
	
	if(is_plugin_active('woocommerce/woocommerce.php')){
		if(!function_exists('brc_add_user_fields')){
			function brc_add_user_fields(){
				if(current_user_can('administradorsimples')){
					include get_template_directory().'/vc-templates/admin_template.add_user.php';
				}
			}
			//add_action('show_user_profile', 'brc_add_user_fields', 999 );
			//add_action('edit_user_profile', 'brc_add_user_fields', 999 );
			add_action('user_new_form', 'brc_add_user_fields', 999 );
		}
	}
?>