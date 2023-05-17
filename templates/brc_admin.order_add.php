<?php	
	/**
	 * ADMIN Arquivo de template
	 * brc_order_edit_admin_add_order_metaboxes_html()
	 * 
	 */
	global $wp_roles, $woocommerce;
	
	// PEGA TODOS OS USUÁRIOS
	$all_roles 		= $wp_roles->roles; 
	$clientes 		= get_users();
	$sel_clientes 	= array();
	if($clientes){
		foreach($clientes as $k => $j){
			$sel_clientes[$j->ID] = '('. __($all_roles[$j->roles[0]]['name'], 'woocommerce') .') ';
			$cpf = get_user_meta($j->ID, 'billing_cpf', true);
			$sel_clientes[$j->ID] .= $j->data->display_name.' - '.$cpf;
		}
	}
	
	// PEGA TODOS METODOS DE PAGAMENTO
	$sel_payment = array();
	if($woocommerce->payment_gateways->get_available_payment_gateways()){
		foreach($woocommerce->payment_gateways->get_available_payment_gateways() as $k => $j){
			$sel_payment[$j->id] = $j->title;
		}
	}
	
	// PEGA TODOS STATUS
	$sel_status = wc_get_order_statuses();
	
	
	// EDIT
	if(isset($_GET['post']) and $_GET['action'] == 'edit'){
		$order_edit = true;
		$order_id 	= get_the_ID();
		$order 		= new WC_Order($order_id);
		
		$cliente 			= $order->get_user_id();
		$emissao_dia 		= $order->get_date_created()->date('Y-m-d');
		$emissao_hora 		= $order->get_date_created()->date('H:i');
		$forma_de_pagamento = $order->get_payment_method();
		$status 			= 'wc-'.$order->get_status();
		
		$_order_admin_tel 	= get_post_meta($order_id, '_order_admin_tel', true);
		$_order_not_cliente = get_post_meta($order_id, '_order_not_cliente', true);
		$brc_order_excursao = get_post_meta($order_id, 'brc_order_excursao', true);
		$brc_order_product_tipo = get_post_meta($order_id, 'brc_order_product_tipo', true);
		
		$order_type 		= $brc_order_excursao?'excursao':'viagem';
		$order_type_nome 	= $brc_order_excursao?'Excursão':'Viagem';
		
		echo '<input type="hidden" name="order-edit" value="1" />';
		
	}else{
		$order_edit = false;
		$emissao_dia = date('Y-m-d', current_time('timestamp'));
		$emissao_hora = date('H:i', current_time('timestamp'));
	}
	
	$viagem_tipo = get_viagem_tipo();
	$viagem_tipo_class = get_viagem_tipo_class();
	?>
	<input type="hidden" name="brc_viagem" value="false" />
	<div class="brc_form_grid">
		<ul class="brc_notices"></ul>
		<!-- notices -->
		
		<div id="brc_add_order_principal">
			<!-- TIPO DE CRIAÇÃO -->
			<h3>Tipo de cadastro</h3>
			<?php 
				if($order_edit){
					if($order_type == 'excursao'){
					?>
						<div class="row">
							<div class="col-sm-12 col">
								<h1 class="big-title-order <?php echo $order_type; ?>">Pedido de <?php echo $order_type_nome; ?></h1>
							</div>
						</div>
					<?php 
					}else{
					?>
						<div class="row">
							<div class="col-sm-12 col">
								<span id="brc_order_add_viagem_title" data-class="_title-<?php echo $viagem_tipo_class[$brc_order_product_tipo]; ?>" 
									class="bullet bullet-type bullet-viagem-<?php echo $viagem_tipo_class[$brc_order_product_tipo]; ?>"
								>
									<?php echo $viagem_tipo[$brc_order_product_tipo]; ?>
								</span>
							</div>
						</div>
					<?php 
					}
				}else{
				?>
					<div class="row">
						<div class="col-sm-4 col">
							<label>
								<input type="radio" name="brc_order_excursao" id="brc_order_excursao" value="excursao"/>
								Excursão
							</label>
							<em class="desc">
								Adicionar um pedido de excursão para cliente.
							</em>
						</div>
						<div class="col-sm-4 col">
							<label>
								<input type="radio" name="brc_order_excursao" id="brc_order_viagem" value="viagem"/>
								Viagem de linha
							</label>
							<em class="desc">
								Adicionar um pedido de viagem de linha para cliente.
							</em>
						</div>
					</div>
				<?php 
				}
			?>
			<div class="row">
				<div class="col-sm-3 col">
					<label for="brc_order_cliente">Selecione o cliente</label>
					<select name="brc_order_cliente" id="brc_order_cliente" class="select2">
						<option value="">--</option>
					<?php
						foreach($sel_clientes as $k => $j){
							$isel = $cliente==$k?'selected':'';
							echo '<option value="'. $k .'" '.$isel.'>'. $j .'</option>';
						}
					?>
					</select>
				</div>
				<div class="col-sm-3 col data-hora">
					<label for="brc_order_emissao">Data da emissão</label>
					<input name="brc_order_emissao_data" id="brc_order_emissao_data" type="date" value="<?php echo $emissao_dia; ?>" />
					<input name="brc_order_emissao_hora" id="brc_order_emissao_hora" type="time" value="<?php echo $emissao_hora; ?>" />
				</div>
				<div class="col-sm-2 col">
					<label for="brc_order_pagamento">Forma de pagamento</label>
					<select name="brc_order_pagamento" id="brc_order_pagamento">
						<option value="">--</option>
					<?php
						foreach($sel_payment as $k => $j){
							$isel = $forma_de_pagamento==$k?'selected':'';
							echo '<option value="'. $k .'" '.$isel.'>'. $j .'</option>';
						}
					?>
					</select>
				</div>
				<div class="col-sm-2 col">
					<label for="brc_order_pagamento_status">Status do pagamento</label>
					<select name="brc_order_pagamento_status" id="brc_order_pagamento_status">
						<option value="">--</option>
					<?php
						foreach($sel_status as $k => $j){
							$isel = $status==$k?'selected':'';
							echo '<option value="'. $k .'" '.$isel.'>'. $j .'</option>';
						}
					?>
					</select>
				</div>
				<div class="col-sm-2 col">
					<label for="brc_order_admin_tel">Telefone de contato [Admin.]</label>
					<input name="brc_order_admin_tel" class="tel" id="brc_order_admin_tel" type="text" placeholder="Telefone de contato principal..." value="<?php echo $_order_admin_tel; ?>"  />
				</div>
			</div>
			
		</div>
	</div>