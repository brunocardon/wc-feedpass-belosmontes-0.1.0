<?php
	// PEGA TODAS VIAGENS
	$viagens = get_posts(array(
		'post_type' 		=> 'product',
		'meta_key' 			=> 'brc_viagem_ida_data',
		'orderby' 			=> 'meta_value',
		'order' 			=> 'ASC',
		'meta_query' 		=> array(
			'relation' 		=> 'AND',
			array(
				'key' 			=> 'brc_viagem_ida_data',
				'compare' 		=> '>=',
				'value' 		=> current_time('timestamp'),
			),
			array(
				'key' 			=> 'brc_excursao',
				'compare' 		=> '!=',
				'value' 		=> 1,
			),
			
		),
		'posts_per_page' 	=> -1,
		'for_order' 		=> true,
	));
	$sel_viagens = array();
	if($viagens){
		foreach($viagens as $k => $j){
			$sel_viagens[$j->ID] = $j->post_title;
		}
	}
	
	if(isset($_GET['post']) and $_GET['action'] == 'edit'){
		$order_edit = true;
		$order_id = get_the_ID();
		$order = new WC_Order($order_id);
		$brc_order_product_tipo = get_post_meta($order_id, 'brc_order_product_tipo', true);
	?>
		<h3>Dados do pedido</h3>
		<table class="brc-admin-table">
			<thead>
				<tr>
					<th class="cel hash">#</th>
					<th class="cel order-passageiro">Passageiro</th>
					<th class="cel order-cpf">CPF</th>
					<th class="cel order-onibus">Veículo</th>
					<th class="cel order-ida text-right"><?php echo $brc_order_product_tipo<2?'Ida':'Embarque'; ?></th>
					<th class="cel order-volta text-right"><?php echo $brc_order_product_tipo<2?'Volta':'Desembarque'; ?></th>
					<th class="cel order-valor text-right">Valor</th>
				</tr>
			</thead>
			<tbody>
			<?php
				foreach($order->get_items() as $item_id => $item){
					$product 			= $item->get_product();
					$data 				= $item->get_data();
					$meta_data 			= $item->get_meta_data();
					$meta_data_array 	= order_item_meta_to_array($meta_data);
					$tarifa 			= html_entity_decode(strip_tags(wc_price($item->get_total()/$item->get_quantity())));
					$ingresso 			= $meta_data_array['_ingresso'];
					
				?>
					
					<tr>
						<td class="cel hash">#<?php echo $item_id; ?></td>
						<td class="cel order-passageiro"><?php echo $meta_data_array['_passageiro_nome']; ?></td>
						<td class="cel order-cpf"><?php echo $meta_data_array['_passageiro_cpf']; ?></td>
						<td class="cel order-onibus">
						<?php 
							echo '<strong>Veic.</strong>['. $meta_data_array['_veiculo'] .']';
							echo ' | ';
							echo '<strong>Poltrona</strong>['. $meta_data_array['_assento'] .']';
						?>
						</td>
						<td class="cel order-ida text-right">
						<?php
							if($brc_order_product_tipo < 2){
								echo '<strong>Data/hora Ida:</strong> '.$meta_data_array['Data/hora Ida'].'<br/>';
								echo '<strong>Origem Ida:</strong> '.$meta_data_array['Origem Ida'].'<br/>';
								echo '<strong>Destino Ida:</strong> '.$meta_data_array['Destino Ida'].'<br/>';
							}else{
								echo '<strong>Origem:</strong> '.$meta_data_array['Origem'].'<br/>';
								if($meta_data_array['Endereço'])
									echo '<strong>Endereço:</strong> '.$meta_data_array['Endereço'].'<br/>';
								echo '<strong>Data/hora Embarque:</strong> '.$meta_data_array['Data/hora Embarque'].'<br/>';
							}
						?>
						</td>
						<td class="cel order-volta text-right">
						<?php
							if($brc_order_product_tipo < 2){
								echo '<strong>Data/hora Volta:</strong> '.$meta_data_array['Data/hora Volta'].'<br/>';
								echo '<strong>Origem Volta:</strong> '.$meta_data_array['Origem Volta'].'<br/>';
								echo '<strong>Destino Volta:</strong> '.$meta_data_array['Destino Volta'].'<br/>';
							}else{
								echo '<strong>Destino:</strong> '.$meta_data_array['Destino'].'<br/>';
								echo '<strong>Data/hora Desembarque:</strong> '.$meta_data_array['Data/hora Desembarque'].'<br/>';
							}
						?>
						</td>
						<td class="cel order-valor text-right"><?php echo $tarifa; ?></td>
					</tr>
				<?php
				}
			?>
			</tbody>
			<tfoot>
				<tr>
					<td class="cel" colspan="5"></td>
					<td class="cel label text-center">Total</td>
					<td class="cel total text-right"><?php echo wc_price($order->get_total()); ?></td>
				</tr>
			</tfoot>
		</table>
	<?php
	}else{
	?>
		<div class="brc_form_grid">
			<div id="brc_add_order_viagens">
				<!-- TIPO DE CRIAÇÃO -->
				<h3>Dados do pedido</h3>
				<div class="row">
					<div class="col-sm-5 col">
						<label for="brc_order_produto_viagem">Selecione a Viagem</label>
						<select name="brc_order_produto_viagem" id="brc_order_produto_viagem" class="select2 sel-viagem" style="width: 100%">
							<option value="">--</option>
						<?php
							foreach($sel_viagens as $k => $j){
								echo '<option value="'. $k .'">['.$k.'] '. $j .'</option>';
							}
						?>
						</select>
					</div>
				</div><hr/>
				
				<!-- AJAX-01 -->
				<div id="brc_add_order_viagens_vars"></div>
				<!-- AJAX-01 -->
			</div>
		</div>
	<?php
	}
?>