<?php
	global $woocommerce;
	
	$new_user_login             = $creating && isset( $_POST['user_login'] ) ? wp_unslash( $_POST['user_login'] ) : '';
	$new_user_firstname         = $creating && isset( $_POST['first_name'] ) ? wp_unslash( $_POST['first_name'] ) : '';
	$new_user_lastname          = $creating && isset( $_POST['last_name'] ) ? wp_unslash( $_POST['last_name'] ) : '';
	$new_user_email             = $creating && isset( $_POST['email'] ) ? wp_unslash( $_POST['email'] ) : '';
	$new_user_uri               = $creating && isset( $_POST['url'] ) ? wp_unslash( $_POST['url'] ) : '';
	$new_user_role              = $creating && isset( $_POST['role'] ) ? wp_unslash( $_POST['role'] ) : '';
	$new_user_send_notification = $creating && ! isset( $_POST['send_user_notification'] ) ? false : true;
	$new_user_ignore_pass       = $creating && isset( $_POST['noconfirmation'] ) ? wp_unslash( $_POST['noconfirmation'] ) : '';
	
	if(is_plugin_active('woocommerce/woocommerce.php')){
		$checkout = $woocommerce->countries->get_address_fields();
	}
	?>
	<hr/>
	<div class="brc_user_edit">
		<h2>Dados de compra e endereço</h2>
		
		<div class="brc_form_grid">
			<ul class="brc_notices"></ul>
			<!-- notices -->
			
			<hr/>
			<div class="brc_user_edit_billing">
				<div class="row">
				<?php
					if($checkout){
						
						// PEGA ESTADOS (UF)
						$countries = new WC_Countries();
						$countries_allowed = $countries->get_allowed_countries();
						if($countries_allowed){
							$ufs = array();
							foreach($countries_allowed as $k => $j){
								$ufs = array_merge($ufs, $countries->get_states($k));
							}
						}
						
						
						foreach($checkout as $k => $j){
							if(
								$k != 'billing_first_name' and
								$k != 'billing_last_name' and
								$k != 'billing_email'
							){
								$input_args = array(
									'class' 		=> $j['class'],
									'label' 		=> $j['label'],
									'placeholder' 	=> $j['label'],
								);
								switch($k){
									case 'billing_state':
										$input_args['type'] = 'state';
										$input_args['country'] = key($countries_allowed);
										$input_args['input_class'] = array('select2');
									break;
									case 'billing_country':
										$input_args['type'] = 'country'; 
									break;
									default:
										if($j['validate'][0]){
											$input_args['type'] = $j['validate'][0];
										}else{
											$input_args['type'] = 'text';
										}
									break;
								}
								
								$input_args['class'][] = 'col-sm-4';
								$input_args['class'][] = 'col';
								$input_args['input_class'][] = $k;
								
								woocommerce_form_field($k, $input_args);
							}
						}
					}
				?>
				</div>
			</div>
		</div>
	</div>