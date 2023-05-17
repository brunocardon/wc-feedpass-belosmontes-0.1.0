<?php
	/*
	 * Template Order Review for product type 3 - Viagem de Linha
	 */
	global $brcthemes;
	
	$_product_ida_origem 			= get_term($cart_item['_product_ida_origem']);
	$_product_ida_origem_cidade_id 	= get_term_meta($_product_ida_origem->term_id, 'brc_cidade', true);
	$_product_ida_origem_cidade 	= get_term($_product_ida_origem_cidade_id);
	$_product_ida_origem_cidade_uf 	= get_term_meta($_product_ida_origem_cidade->term_id, 'brc_uf', true);
	
	$_product_ida_destino 			= get_term($cart_item['_product_ida_destino']);
	$_product_ida_destino_cidade_id	= get_term_meta($_product_ida_destino->term_id, 'brc_cidade', true);
	$_product_ida_destino_cidade	= get_term($_product_ida_destino_cidade_id);
	$_product_ida_destino_cidade_uf = get_term_meta($_product_ida_destino_cidade->term_id, 'brc_uf', true);
	
	?>
	<div class="fepa-order-reviews">
		<div class="trajeto-title">
			<div class="trajeto-title-inner">
				<span><?php echo $_product_ida_origem_cidade->name.'/'.strtoupper($_product_ida_origem_cidade_uf); ?></span>
				<i class="fa fa-long-arrow-right"></i>
				<span><?php echo $_product_ida_destino_cidade->name.'/'.strtoupper($_product_ida_destino_cidade_uf); ?></span>
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
				<?php echo $cart_item['Data/hora Embarque']; ?>
			</span>
			<div class="divider"></div><!-- HR -->
			<span>
				<i class="fa fa-street-view"></i> 
				<strong>Desembarque:</strong> 
				<?php echo $cart_item['Destino']; ?>
			</span>
			<span>
				<i class="far fa-clock"></i>
				<strong>Data/hora:</strong> 
				<?php echo $cart_item['Data/hora Desembarque']; ?>
			</span>
			<div class="divider"></div><!-- HR -->
			<span>
				<i class="far fa-map"></i>
				<strong>Linha:</strong> 
				<?php echo $cart_item['Linha']; ?>
			</span>
			<span>
				<i class="fa fa-male"></i> 
				<strong>Passageiro:</strong> 
				<?php echo $cart_item['Passageiro']; ?>
			</span>
			<span>
				<i class="fas fa-chair"></i>
				<strong>Assento:</strong> 
				<em><?php echo $cart_item['_passageiro_assento']; ?></em>
			</span>
		</div>
	</div>