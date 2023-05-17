<?php
	$product_id = $_GET['v'];
	$product = wc_get_product($product_id);
	$orders = get_orders_ids_by_product_id($product_id, get_all_woocommerce_status_id());
	
	$product_data 		= $product->get_data();
	$product_id 		= $product_data['id'];
	$brc_excursao_nome 	= get_the_title($product_id);
	$brc_excursao_data 	= get_post_meta($product_id, 'brc_excursao_data', true);
	
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head><?php wp_head(); ?></head>
<body <?php body_class();?> itemscope itemtype="http://schema.org/WebPage">
	<div class="brc-passsageiros-table-wrapper">
		<div class="brc-passsageiros-table-print">
			<div class="brc-passsageiros-table-a4">
				<?php include FEPA_PLUGIN_DIR . '/templates/brc_admin.lista_table_header.php'; ?>
				<table class="brc-passsageiros-table <?php echo $normal?'normal-table':'simple-table'; ?>">
					<thead>
						<tr>
							<th class="cel hash">#</th>
							<th class="cel passageiro">Hóspede</th>
							<th class="cel telefone">Telefone</th>
							<th class="cel cpf">CPF</th>
							<th class="cel assentos">Assento</th>
							<th class="cel quarto">Quarto</th>
							<th class="cel hotel">Hotel</th>
							<th class="cel ingresso">Ingresso</th>
							<th class="cel valor" colspan="2">Valor</th>
						</tr>
					</thead>
					<tbody>
					<?php
						$total = 0;
						$c = 1;
						foreach($orders as $order_id){
							$order = new WC_Order($order_id);
							$status_id = $order->get_status();
							
							foreach($order->get_items() as $order_item_id => $order_item){
						
								$var_data 			= $order_item->get_data();
								$meta_data 			= $order_item->get_meta_data();
								$meta_data_array 	= order_item_meta_to_array($meta_data);
								$valor 				= html_entity_decode(strip_tags(wc_price($order_item->get_total()/$order_item->get_quantity())));
								$order_user_id 		= $order->get_user_id();
								$cliente 			= '#'.$order_user_id.' '.get_user_meta($order_user_id, 'billing_first_name', true).'-'.$order_user_id;
								
								$tels = array();
								$_phone 			= get_user_meta($order_user_id, 'billing_phone', true);
								if($_phone)
									$tels[] = $_phone;
								
								$_cellphone 		= get_user_meta($order_user_id, 'billing_cellphone', true);
								if($_cellphone)
									$tels[] = $_cellphone;
							?>
								<tr 
									class="<?php echo $normal?'normal-sup':''; ?> <?php echo $c%2?'filled':''; ?>" 
									id="pessoa-order-item-<?php echo $order_item_id; ?>"
								>
									<td class="cel hash"><?php echo ($c)<10?'0'.($c):($c); ?></td>
									<td class="cel passageiro">
										<?php echo $meta_data_array['_hopede_nome']; ?>
										<br/>
										<span class="bullet status-<?php echo $status_id; ?>">
											<?php echo wc_get_order_status_name($status_id); ?>
										</span>
									</td>
									<td class="cel telefone"><?php echo implode('<br/>', $tels); ?></td>
									<td class="cel cpf"><?php echo $meta_data_array['_hopede_cpf']; ?></td>
									<td class="cel assentos">
										V: <?php echo $meta_data_array['_veiculo']?$meta_data_array['_veiculo']:'--'; ?> | 
										P: <?php echo $meta_data_array['_assento']?$meta_data_array['_assento']:'--'; ?>
									</td>
									<td class="cel quarto">
										<?php echo $meta_data_array['quartos']; ?> | 
										<?php echo $meta_data_array['_quarto_numero']?get_quarto_numero($var_data['variation_id'], $meta_data_array['_quarto_numero']):'--'; ?>
									</td>
									<td class="cel hotel"><?php echo $meta_data_array['Hotel']; ?></td>
									<td class="cel ingresso"><?php echo $meta_data_array['_ingresso']?'<span class="bullet">SIM</span>':'--'; ?></td>
									<td class="cel valor" colspan="2"><?php echo $valor; ?></td>
								</tr>
							<?php
								$total += $order_item->get_total()/$order_item->get_quantity();
								$c++;
							}
						}
					?>
					</tbody>
					<tfoot>
						<tr>
							<td class="cel" colspan="7"></td>
							<td class="cel label">Total</td>
							<td class="cel total"><?php echo wc_price($total); ?></td>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
	</div>
</body>
</html>