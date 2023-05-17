<?php	
	/**
	 * ADMIN Arquivo de template
	 * brc_product_clientes_custom_metaboxes_html() -> brc_excursao = true
	 * 
	 */
	global $brcpasstour_theme; // $brctheme = new BRCPASSTOUR_THEME();
	
	$product_id = get_the_ID();
	$v_status = get_all_woocommerce_status_id();
	unset($v_status[4]);
	unset($v_status[5]);
	unset($v_status[6]);
	$orders = get_orders_ids_by_product_id($product_id, $v_status);
	
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
					<th class="cel passageiro text-left">Passageiro</th>
					<th class="cel cpf text-left">CPF</th>
					<th class="cel rg text-left">RG</th>
					<th class="cel cliente text-left">Cliente</th>
					<th class="cel assentos">Assentos</th>
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
						
						$item_produto_id 	= $order_item->get_product_id();
						$var_data 			= $order_item->get_data();
						$meta_data 			= $order_item->get_meta_data();
						$meta_data_array 	= order_item_meta_to_array($meta_data);
						$valor 				= html_entity_decode(strip_tags(wc_price($order_item->get_total()/$order_item->get_quantity())));
						$order_user_id 		= $order->get_user_id();
						$cliente 			= '#'.$order_user_id.' '.get_user_meta($order_user_id, 'billing_first_name', true);
						
						if($item_produto_id == $product_id){
						?>
							<tr 
								class="<?php echo $normal?'normal-sup':''; ?> <?php echo $c%2?'filled':''; ?>" 
								id="pessoa-order-item-<?php echo $order_item_id; ?>"
							>
								<!--<td class="cel hash"><input type="checkbox" name="passageiro[<?php echo $order_item_id; ?>]" class="table-list-check" value="<?php echo $order_id; ?>" /></td>-->
								<td class="cel hash"><?php echo ($c)<10?'0'.($c):($c); ?></td>
								
								<td class="cel passageiro text-left"><?php echo $meta_data_array['_passageiro_nome']; ?></td>
								<td class="cel cpf text-left"><?php echo $meta_data_array['_passageiro_cpf']; ?></td>
								<td class="cel rg text-left"><?php echo $meta_data_array['_passageiro_rg']; ?></td>
								<td class="cel cliente text-left">
									<a href="<?php echo get_edit_user_link($order_user_id); ?>"><?php echo $cliente; ?></a>
								</td>
								<td class="cel assentos">
									V: <?php echo $meta_data_array['_veiculo']; ?> | 
									P: <?php echo $meta_data_array['_assento']; ?>
								</td>
								
								<td class="cel pedido"><a href="<?php echo get_edit_post_link($order_id); ?>">#<?php echo $order_id; ?></a></td></td>
								<td class="cel emissao"><?php echo $order->get_date_created()->date("d/m/Y"); ?></td>
								<td class="cel valor text-right" colspan="2"><?php echo $valor; ?></td>
							</tr>
						<?php
							$total += $order_item->get_total()/$order_item->get_quantity();
							$c++;
						}
					}
				}
			?>
			</tbody>
			<tfoot>
				<tr>
					<td class="cel" colspan="6"></td>
					<td class="cel label text-center">Total</td>
					<td class="cel total text-right"><?php echo wc_price($total); ?></td>
				</tr>
			</tfoot>
		</table>
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
			href="<?php echo LISTA_DE_PASSAGEIROS_URI.'?v='. get_the_ID(); ?>&simple=1" 
			target="_blank" 
			class="brc_admin_btn brc_admin_list_btn button btn_amarelo" 
			title="Imprimir lista de passageiros"
		>
			<span class="fa fa-clipboard"></span> 
			Imprimir lista de passageiros
		</a>
		
		<?php $base = base64_encode($product_id); ?>
		<a 
			href="<?php echo get_permalink(get_option('fepa_comprovante_reserva')).'?p='. $base; ?>&lista=product" 
			target="_blank" 
			class="brc_admin_btn brc_admin_list_btn button action-ticket" 
			title="Imprimir bilhetes de passagem"
		>
			<span class="fa fa-ticket"></span>
			Imprimir bilhetes de passagem
		</a>
		
		<a 
			href="#" 
			target="_blank" 
			class="brc_admin_btn brc_admin_list_btn button action-sms" 
			id="action-sms" 
			title="Enviar mensagem via SMS"
			data-produto_id="<?php echo $product_id; ?>"
		>
			<span class="fa fa-commenting-o"></span> 
			Enviar mensagem via SMS
		</a>
	<?php
	else:
	?>
		<h3>Nenhum pedido encontrado para a viagem.</h3>
	<?php
	endif
?>