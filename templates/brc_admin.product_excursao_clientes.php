<?php	
	/**
	 * ADMIN Arquivo de template
	 * brc_product_clientes_custom_metaboxes_html() -> brc_excursao = true
	 * 
	 */
	global $brcpasstour_theme; // $brctheme = new BRCPASSTOUR_THEME();
	
	$product_id = get_the_ID();
	$orders = get_orders_ids_by_product_id($product_id, get_all_woocommerce_status_id());
	
	if($orders):
	?>
		<!-- assentos / passageiros -->
		<input type="hidden" name="product_id" value="<?php echo $product_id; ?>" />
		
		<h3>Passageiros</h3>
		<table class="brc-admin-table" id="form-viagem-passageiros">
			<thead>
				<tr>
					<!--<th class="cel hash"><input type="checkbox" class="table-list-check-all" /></th>-->
					<th class="cel hash">#</th>
					<th class="cel passageiro text-left">Hóspede</th>
					<th class="cel cpf text-left">CPF</th>
					<th class="cel cliente text-left">Cliente</th>
					<th class="cel assentos">Assentos</th>
					<th class="cel quarto">Quarto</th>
					<th class="cel hotel">Hotel</th>
					<th class="cel ingresso">Ingresso</th>
					<th class="cel pedido">Pedido</th>
					<th class="cel emissao">Emissão</th>
					<th class="cel valor text-right" colspan="2">Valor</th>
				</tr>
			</thead>
			<tbody>
			<?php
				$total = 0;
				$c = 1;
				foreach($orders as $order_id){
					$order = new WC_Order($order_id);
					
					foreach($order->get_items() as $order_item_id => $order_item){
						$var_data 			= $order_item->get_data();
						$meta_data 			= $order_item->get_meta_data();
						$meta_data_array 	= order_item_meta_to_array($meta_data);
						$valor 				= html_entity_decode(strip_tags(wc_price($order_item->get_total()/$order_item->get_quantity())));
						$order_user_id 		= $order->get_user_id();
						$cliente 			= '#'.$order_user_id.' '.get_user_meta($order_user_id, 'billing_first_name', true);
						
						$price_td[$order_item_id] = true;
						$mva = $meta_data_array;
						
						if($mva['_hospedes']){
							foreach($mva['_hospedes'] as $hospede){
							?>
								<tr 
									class="<?php echo $normal?'normal-sup':''; ?> <?php echo $c%2?'filled':''; ?>" 
									id="pessoa-order-item-<?php echo $order_item_id; ?>"
								>
									<td class="cel hash"><?php echo ($c)<10?'0'.($c):($c); ?></td>
									
									<td class="cel passageiro text-left"><?php echo $hospede['nome']; ?></td>
									<td class="cel cpf text-left"><?php echo $hospede['cpf']; ?></td>
									<td class="cel cliente text-left"><a href="<?php echo get_edit_user_link($order_user_id); ?>"><?php echo $cliente; ?></a></td>
									
									<!-- assentos -->
									<td class="cel assentos">
										V: <?php echo $hospede['veiculo']; ?> | 
										P: <?php echo $hospede['assento']; ?>
									</td>
									<td class="cel quarto">
										<?php echo $meta_data_array['quartos']; ?> | 
										<?php echo $meta_data_array['_quarto_numero']?get_quarto_numero($var_data['variation_id'], $meta_data_array['_quarto_numero']):'--'; ?>
									</td>
									<!-- assentos -->
									
									<td class="cel hotel"><?php echo get_the_title($meta_data_array['_variation_hotel_id']); ?></td>
									<td class="cel ingresso">
									<?php 
										if($hospede['ingresso']){
											echo '<span class="bullet">+ ingresso R$ '.moedaRealPrint($hospede['ingresso_price']).'</span>';
										}else{
											echo '--'; 
										}
									?>
									</td>
									<td class="cel pedido"><a href="<?php echo get_edit_post_link($order_id); ?>">#<?php echo $order_id; ?></a></td></td>
									<td class="cel emissao"><?php echo $order->get_date_created()->date("d/m/Y"); ?></td>
									
									<?php if($price_td[$order_item_id]): ?>
										<td class="cel valor text-right" colspan="2" rowspan="<?php echo count($mva['_hospedes']); ?>"><?php echo $valor; ?></td>
									<?php endif; ?>
								</tr>
							<?php
								$price_td[$order_item_id] = false;
								$c++;
							}
						}
						$total += $order_item->get_total()/$order_item->get_quantity();
					}
				}
			?>
			</tbody>
			<tfoot>
				<tr>
					<td class="cel" colspan="9"></td>
					<td class="cel label text-center">Total</td>
					<td class="cel total text-right"><?php echo wc_price($total); ?></td>
				</tr>
			</tfoot>
		</table>
		
		<button 
			class="brc_admin_btn brc_admin_list_btn button" 
			id="action-distribuir-quartos" 
			title="Distribuir quartos"
			data-produto_id="<?php echo $product_id; ?>"
		>
			<span class="fa fa-bed"></span> 
			Distribuir quartos
		</button>
		<button 
			class="brc_admin_btn brc_admin_list_btn button btn_amarelo" 
			id="action-distribuir-assentos" 
			title="Distribuir quartos"
			data-produto_id="<?php echo $product_id; ?>"
		>
			<span class="fa fa-bus"></span> 
			Distribuir assentos
		</button>
		<!-- a PRINT -->
		<a 
			href="<?php echo get_permalink(get_option('fepa_lista_de_hospedes')).'?v='. get_the_ID(); ?>&simple=1" 
			target="_blank" 
			class="brc_admin_btn brc_admin_list_btn button btn_amarelo" 
			title="Imprimir lista de hóspedes"
		>
			<span class="fa fa-clipboard"></span> 
			Imprimir lista de hóspedes
		</a>
		
		<?php $base = base64_encode($product_id); ?>
		<a 
			href="<?php echo get_permalink(get_option('fepa_comprovante_reserva')).'?p='. $base; ?>&lista=product" 
			target="_blank" 
			class="brc_admin_btn brc_admin_list_btn button action-ticket" 
			title="Imprimir comprovantes de reserva"
		>
			<span class="fas fa-clipboard-check"></span>
			Imprimir comprovantes de reserva
		</a>
	<?php
	else:
	?>
		<h3>Nenhum pedido encontrado para a Excursão.</h3>
	<?php
	endif
?>