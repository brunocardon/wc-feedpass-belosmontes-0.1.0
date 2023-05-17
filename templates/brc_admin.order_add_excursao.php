<?php
	// PEGA TODAS VIAGENS
	$excursoes_args = array(
		'post_type' 		=> 'product',
		//'meta_key' 			=> 'brc_excursao_data',
		//'orderby' 			=> 'meta_value',
		'order' 			=> 'ASC',
		'meta_query' 		=> array(
			'relation' 		=> 'AND',
			array(
				'key' 			=> 'brc_excursao_data',
				'compare' 		=> '>=',
				'value' 		=> current_time('timestamp'),
			),
			array(
				'key' 			=> 'brc_excursao',
				'compare' 		=> '=',
				'value' 		=> 1,
			),
		),
		'posts_per_page' 	=> -1,
		'for_order' 		=> true,
	);
	
	
	$excursoes = get_posts($excursoes_args);
	$sel_excursoes = array();
	if($excursoes){
		foreach($excursoes as $k => $j){
			$sel_excursoes[$j->ID] = $j->post_title;
		}
	}
	if(isset($_GET['post']) and $_GET['action'] == 'edit'){
		$order_edit = true;
		$order_id = get_the_ID();
		$order = new WC_Order($order_id);
	?>
		<h3>Dados do pedido</h3>
		<table class="brc-admin-table">
			<thead>
				<tr>
					<th class="cel hash">#</th>
					<th class="cel order-hospede">Hóspede</th>
					<th class="cel order-cpf">CPF</th>
					<th class="cel order-quarto">Quarto</th>
					<th class="cel order-hotel">Hotel</th>
					<th class="cel order-onibus">Veículo</th>
					<th class="cel order-ingresso">Ingresso?</th>
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
						<td class="cel order-hospede"><?php echo $meta_data_array['_hopede_nome']; ?></td>
						<td class="cel order-cpf"><?php echo $meta_data_array['_hopede_cpf']; ?></td>
						<td class="cel order-quarto">
							<a href="<?php echo get_edit_post_link($data['parent_id']); ?>" title="<?php echo get_the_title($data['parent_id']); ?>">
								<?php echo $meta_data_array['Quarto']; ?> 
								(n: <?php echo get_quarto_numero($data['variation_id'], $meta_data_array['_quarto_numero']); ?>)
							</a>
						</td>
						<td class="cel order-hotel"><?php echo $meta_data_array['Hotel']; ?></td>
						<td class="cel order-onibus">
						<?php 
							echo '<strong>Veic.</strong>['. $meta_data_array['_veiculo'] .']';
							echo ' | ';
							echo '<strong>Poltrona</strong>['. $meta_data_array['_assento'] .']';
						?>
						</td>
						<td class="cel order-ingresso">
						<?php 
							if($ingresso){
								echo '<i class="fa fa-check" style="color:#33b101;"></i>';
							}else{
								echo '<i class="fa fa-times" style="color:#b1010a;"></i>';
							}
						?>
						</td>
						<td class="cel order-valor text-right"><?php echo $tarifa;?></td>
					</tr>
				<?php
				}
			?>
			</tbody>
			<tfoot>
				<tr>
					<td class="cel" colspan="6"></td>
					<td class="cel label text-center">Total</td>
					<td class="cel total text-right"><?php echo wc_price($order->get_total()); ?></td>
				</tr>
			</tfoot>
		</table>
	<?php
	}else{
	?>
		<div class="brc_form_grid">
			<div id="brc_add_order_excursoes">
				<!-- TIPO DE CRIAÇÃO -->
				<h3>Dados do pedido</h3>
				<div class="row">
					<div class="col-sm-5 col">
						<label for="brc_order_produto_excursao">Selecione a Excursão</label>
						<select name="brc_order_produto_excursao" id="brc_order_produto_excursao" class="select2 sel-viagem" style="width: 100%">
							<option value="">--</option>
						<?php
							foreach($sel_excursoes as $k => $j){
								echo '<option value="'. $k .'">['.$k.'] '. $j .'</option>';
							}
						?>
						</select>
					</div>
				</div><hr/>
				
				<!-- AJAX-01 -->
				<div id="brc_add_order_excursoes_vars"></div>
				<!-- AJAX-01 -->
			</div>
		</div>
	<?php
	}
?>