<?php
	global $brcthemes;
	
	$_product_ida_origem 			= get_term($cart_item['_product_ida_origem']); // rodoviaria
	$_product_ida_origem_cidade 	= get_term($cart_item['_product_ida_origem_cidade']); // Montes claros
	$_product_ida_destino 			= get_term($cart_item['_product_ida_destino']);
	$_product_ida_destino_cidade 	= get_term($cart_item['_product_ida_destino_cidade']);
	
	pre_debug($cart_item['_order_etapa']);
	pre_debug($cart_item['_order_selvolta']);
	?>
	<div class="trajeto-detalhes">
		<div class="trajeto-title">
			<i class="fa fa-bus"></i>
			<div class="trajeto-title-inner">
				<span><?php echo $_product_ida_origem_cidade->name; ?></span>
				<i class="fa fa-long-arrow-right"></i>
				<span><?php echo $_product_ida_destino_cidade->name; ?></span>
			</div>
			<small class="<?php echo get_viagem_tipo_class($tipo_cadastro); ?>"><?php echo get_viagem_tipo($tipo_cadastro); ?></small>
			<span class="price">
			<?php 
				echo apply_filters(
					'woocommerce_cart_item_subtotal', 
					WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), 
					$cart_item, $cart_item_key 
				); 
			?>
				<a 
					href="#" 
					class="vp_vc_btn vp_vc_btn-tiny remove-cart-item" 
					data-cartitem="<?php echo $cart_item_key; ?>" 
					id="remove-cart-item-<?php echo $cart_item_key; ?>"
				>
					<i class="fa fa-remove"></i> 
					Passageiro
				</a>
			</span>
		</div>
		<span>
			<i class="fa fa-street-view"></i> 
			<strong>Embarque:</strong> 
			<?php echo $cart_item['Origem']; ?>
		</span>
		<?php if($cart_item['Endereço']): ?>
			<span>
				<i class="fa fa-map-marker"></i> 
				<strong>Endereço:</strong> 
				<?php echo $cart_item['Endereço']; ?>
			</span>
		<?php endif; ?>
		<span>
			<i class="fa fa-clock-o"></i> 
			<strong>Data/hora:</strong> 
			<?php echo $cart_item['Data/hora Embarque']; ?>
		</span>
		<div class="trajeto-detalhes-div"></div><!-- HR -->
		<span>
			<i class="fa fa-street-view"></i> 
			<strong>Desembarque:</strong> 
			<?php echo $cart_item['Destino']; ?>
		</span>
		<span>
			<i class="fa fa-clock-o"></i> 
			<strong>Data/hora:</strong> 
			<?php echo $cart_item['Data/hora Desembarque']; ?>
		</span>
		<div class="trajeto-detalhes-div"></div><!-- HR -->
		<span>
			<i class="fa fa-male"></i> 
			<strong>Passageiro:</strong> 
			<?php echo $cart_item['Passageiro']; ?>
		</span>
	</div>