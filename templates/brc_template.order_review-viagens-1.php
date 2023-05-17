<?php
	/*
	 * Template Order Review for product type 1 - Viagem bate e volta
	 */

	// DESTINOS
	$_product_ida_origem 			= get_term($cart_item['_product_ida_origem']); // rodoviaria
	$_product_ida_origem_cidade 	= get_term($cart_item['_product_ida_origem_cidade']); // Montes claros
	$_product_ida_destino 			= get_term($cart_item['_product_ida_destino']);
	$_product_ida_destino_cidade 	= get_term($cart_item['_product_ida_destino_cidade']);
	/*--*/
	$_product_volta_origem 			= get_term($cart_item['_product_volta_origem']); // rodoviaria
	$_product_volta_origem_cidade 	= get_term($cart_item['_product_volta_origem_cidade']); // Montes claros
	$_product_volta_destino 		= get_term($cart_item['_product_volta_destino']);
	$_product_volta_destino_cidade 	= get_term($cart_item['_product_volta_destino_cidade']);
	
	$ponto_de_embarque = $_product_ida_origem->name;
	$ponto_de_embarque_endereco = false;
	$ponto_de_embarque_data = $cart_item['Data/hora Ida'];
	
	$linha = wp_get_post_terms($post_ID, 'brc_linhas');
	if(!is_wp_error($linha)){
		if($linha){
			$linha = $linha[0];
			$linha_pontos = get_term_meta($linha->term_id, 'linha_grupo_paradas', true);
			
			$ponto_de_embarque = $cart_item['_product_ponto_embarque']['p-nome'];
			$ponto_de_embarque_endereco = $cart_item['_product_ponto_embarque']['p-endereco'];
			$ponto_de_embarque_data = ($cart_item['_product_ponto_embarque']['p-tempo'] * 60) + $cart_item['_product_ida_data'];
			$ponto_de_embarque_data = date('d/m/Y', $ponto_de_embarque_data).' - '.date('H:i', $ponto_de_embarque_data).'h <small>(aprox.)</small>';
		}
	}
	
	//pre_debug($cart_item['_product_ponto_embarque']);
	
	?>
	<div class="fepa-order-reviews">
		<div class="trajeto-title">
			<div class="trajeto-title-inner">
				<span><?php echo $_product_ida_origem_cidade->name; ?></span>
				<i class="fa fa-exchange"></i>
				<span><?php echo $_product_ida_destino_cidade->name; ?></span>
			</div>
			<small class="<?php echo get_viagem_tipo_class($tipo_cadastro); ?>"><?php echo get_viagem_tipo($tipo_cadastro); ?></small>
		</div>
		<div class="dados">
			<span>
				<i class="fa fa-street-view"></i> 
				<strong>Embarque:</strong> 
				<?php echo $cart_item['Origem']; ?>
			</span>
			<?php if($cart_item['Endereço']): ?>
				<span>
					<i class="fas fa-map-marker-alt"></i>
					<strong>Endereço:</strong> 
					<?php echo $cart_item['Endereço']; ?>
				</span>
			<?php endif; ?>
			<span>
				<i class="far fa-clock"></i>
				<strong>Data/hora:</strong> 
				<?php echo $ponto_de_embarque_data; ?>
			</span>
			<div class="divider"></div><!-- HR -->
			<span>
				<i class="fa fa-street-view"></i> 
				<strong>Volta por:</strong> 
				<?php echo $_product_volta_origem->name; ?>
			</span>
			<span>
				<i class="far fa-clock"></i>
				<strong>Data/hora Volta:</strong> 
				<?php echo $cart_item['Data/hora Volta']; ?>
			</span>
			<div class="divider"></div><!-- HR -->
			<span>
				<i class="fa fa-male"></i> 
				<strong>Passageiro:</strong> 
				<?php echo $cart_item['Passageiro']; ?>
			</span>
		</div>
	</div>