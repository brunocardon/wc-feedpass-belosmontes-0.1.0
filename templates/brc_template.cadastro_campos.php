<div class="vc_row">
<?php
	global $woocommerce;
	
	if(is_plugin_active('woocommerce/woocommerce.php')){
		$checkout = $woocommerce->countries->get_address_fields();
	}
	
	if($checkout){
		$n_checkout = $checkout;
		$checkout = array();
		
		// REORDENA
		$checkout['billing_first_name'] = $n_checkout['billing_first_name'];
		$checkout['billing_last_name'] = $n_checkout['billing_last_name'];
		$checkout['billing_cpf'] = $n_checkout['billing_cpf'];
		$checkout['billing_email'] = $n_checkout['billing_email'];
		$checkout['billing_cellphone'] = $n_checkout['billing_cellphone'];
		//$checkout['billing_phone'] = $n_checkout['billing_phone'];
		$checkout['billing_postcode'] = $n_checkout['billing_postcode'];
		$checkout['billing_country'] = $n_checkout['billing_country'];
		$checkout['billing_state'] = $n_checkout['billing_state'];
		$checkout['billing_address_1'] = $n_checkout['billing_address_1'];
		$checkout['billing_number'] = $n_checkout['billing_number'];
		$checkout['billing_city'] = $n_checkout['billing_city'];
		$checkout['billing_neighborhood'] = $n_checkout['billing_neighborhood'];
		$checkout['billing_address_2'] = $n_checkout['billing_address_2'];
		
		
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
			$input_args = array(
				'class' 		=> array(),
				//'label' 		=> $j['label'],
				'placeholder' 	=> $j['label'],
				'default' 		=> $_POST[$k],
			);
			
			$col_class = 'vc_col-sm-4';
			
			switch($k){
				case 'billing_first_name':
				case 'billing_last_name':
					$col_class = 'vc_col-sm-6';
				break;
				case 'billing_cpf':
					$input_args['input_class'][] = 'i-cpf';
					$col_class = 'vc_col-sm-4';
				break;
				case 'billing_email':
					$col_class = 'vc_col-sm-4';
				break;
				case 'billing_phone':
				case 'billing_cellphone':
					$input_args['input_class'][] = 'i-tel';
					$col_class = 'vc_col-sm-4';
				break;
				
				case 'billing_address_1':
					$col_class = 'vc_col-sm-10';
				break;
				case 'billing_number':
					$col_class = 'vc_col-sm-2';
					$input_args['placeholder'] = 'n&#186;';
				break;
				
				case 'billing_state':
					$input_args['label'] = $j['label'];
					$input_args['type'] = 'state';
					$input_args['country'] = key($countries_allowed);
					$input_args['input_class'] = array('select2');
					$col_class = 'vc_col-sm-6';
				break;
				case 'billing_country':
					$input_args['type'] = 'country';
					$col_class = 'vc_col-sm-6';
				break;
				
				
				default:
					if($j['validate'][0]){
						$input_args['type'] = $j['validate'][0];
					}else{
						$input_args['type'] = 'text';
					}
				break;
			}
			
			//------------------------
			$input_args['class'][] = $col_class;
			$input_args['class'][] = 'field-wrapper';
			$input_args['input_class'][] = $k;
			$input_args['input_class'][] = 'form-field';
			
			woocommerce_form_field($k, $input_args);
		}
	}
?>
</div>
<div class="vc_row">
	<div class="vc_col-sm-12 field-wrapper text-center"><hr></div>
	
	<div class="vc_col-sm-4 field-wrapper">
		<input type="password" class="form-field" name="asenha" placeholder="Senha" />
	</div>
	<div class="vc_col-sm-4 field-wrapper">
		<input type="password" class="form-field" name="bsenha" placeholder="Repita a senha" />
	</div>
	
	<div class="vc_col-sm-12 field-wrapper text-center"><hr></div>
	
	<div class="vc_col-sm-4 field-wrapper">
		<input type="submit" class="form-submit vp_vc_btn vp_vc_btn-xs" value="cadastrar"/>
	</div>
</div>