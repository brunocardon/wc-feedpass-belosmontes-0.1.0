<?php	
	/**
	 * ADMIN Arquivo de template
	 * brc_product_custom_metaboxes_html()
	 * 
	 */
	global $wp_roles, $woocommerce;
	$screen = get_current_screen();//action = add
	
	// PEGA TODOS HOTEIS
	$hoteis = get_posts(array(
		'post_type' 		=> 'hoteis',
		'posts_per_page' 	=> -1,
	));
	$sel_hoteis = array();
	if($hoteis){
		foreach($hoteis as $k => $j){
			$sel_hoteis[$j->ID] = $j->post_title;
		}
	}
	
	$post_ID = get_the_ID();
	$product = wc_get_product($post_ID);
	$brc_excursao = get_post_meta($post_ID, 'brc_excursao', true);
	
	if($screen->action != 'add'){
		
		$brc_excursao_subtitle = get_post_meta($post_ID, 'brc_excursao_subtitle', true);
		
		$brc_excursao_data = get_post_meta($post_ID, 'brc_excursao_data', true);
		if($brc_excursao_data){
			$brc_excursao_data_dia			= date('Y-m-d', $brc_excursao_data);
			$brc_excursao_data_hora 		= date('H:i', $brc_excursao_data);
		}
		
		$brc_excursao_data_volta = get_post_meta($post_ID, 'brc_excursao_data_volta', true);
		if($brc_excursao_data_volta){
			$brc_excursao_data_volta_dia		= date('Y-m-d', $brc_excursao_data_volta);
			$brc_excursao_data_volta_hora 		= date('H:i', $brc_excursao_data_volta);
		}
		
		$brc_excursao_tempo_viagem 		= get_post_meta($post_ID, 'brc_excursao_tempo_viagem', true);
		$brc_excursao_ingresso 			= get_post_meta($post_ID, 'brc_excursao_ingresso', true);
		$brc_excursao_ingresso_stock 	= get_post_meta($post_ID, 'brc_excursao_ingresso_stock', true);
		$brc_excursao_ingresso_preco 	= get_post_meta($post_ID, 'brc_excursao_ingresso_preco', true);
		$brc_excursao_quant_veiculos 	= get_post_meta($post_ID, 'brc_excursao_quant_veiculos', true);
		$brc_excursao_quant_assentos 	= get_post_meta($post_ID, 'brc_excursao_quant_assentos', true);
		
		if($product->is_type('variable')){
			$quartos = $product->get_available_variations();
			$p_quarto = $quartos[0];
			unset($quartos[0]);
			
			// QUARTO UM DADOS
			$brc_excursao_variations_hotel 			= get_post_meta($p_quarto['variation_id'], 'brc_excursao_variations_hotel', true);
			$brc_excursao_variations_nome 			= get_post_meta($p_quarto['variation_id'], 'brc_excursao_variations_nome', true);
			$brc_excursao_variations_pquarto 		= get_post_meta($p_quarto['variation_id'], 'brc_excursao_variations_pquarto', true);
			//$brc_excursao_variations_stock_total 	= get_post_meta($p_quarto['variation_id'], 'brc_excursao_variations_stock_total', true);
			$attribute_quartos = get_post_meta($p_quarto['variation_id'], 'attribute_quartos', true);
		}
	}
	
	//pre_debug($brc_excursao);
	
	?>
	<input type="hidden" name="brc_excursao" value="yes" />
	<div class="brc_form_grid">
		<ul class="brc_notices"></ul>
		<!-- notices -->
		
		<div id="brc_add_product_excursao_principal">
			<div class="row">
				<div class="col-sm-6 col">
					<label for="brc_excursao_subtitle">Sub-titulo</label>
					<input name="brc_excursao_subtitle" id="brc_excursao_subtitle" type="text" value="<?php echo $brc_excursao_subtitle; ?>" /> 
					<em class="desc">Adicione um sub-titulo explicativo ou incativo para melhor leitura do evento/excursão.</em>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-3 col data-hora">
					<label for="brc_excursao_data_dia">Data/hora do embarque</label>
					<input name="brc_excursao_data_dia" id="brc_excursao_data_dia" type="date" value="<?php echo $brc_excursao_data_dia; ?>" required="required" />
					<input name="brc_excursao_data_hora" id="brc_excursao_data_hora" type="time" value="<?php echo $brc_excursao_data_hora; ?>" required="required" />
				</div>
				<div class="col-sm-3 col data-hora">
					<label for="brc_excursao_data_volta_dia">Data/hora do retorno</label>
					<input name="brc_excursao_data_volta_dia" id="brc_excursao_data_volta_dia" type="date" value="<?php echo $brc_excursao_data_volta_dia; ?>" required="required" />
					<input name="brc_excursao_data_volta_hora" id="brc_excursao_data_volta_hora" type="time" value="<?php echo $brc_excursao_data_volta_hora; ?>" required="required" />
				</div>
				<div class="col-sm-3 col">
					<label for="brc_excursao_tempo_viagem">Tempo aprox. de viagem</label>
					<input name="brc_excursao_tempo_viagem" id="brc_excursao_tempo_viagem" type="number" min="0" value="<?php echo $brc_excursao_tempo_viagem; ?>" placeholder="Valor em minutos" required="required" />
					<em class="desc">Informar valor em minutos</em>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-2 col">
					<label for="brc_excursao_quant_veiculos">Quant. Veículos</label>
					<input name="brc_excursao_quant_veiculos" id="brc_excursao_quant_veiculos" type="number" min="0" value="<?php echo $brc_excursao_quant_veiculos; ?>" placeholder="Quant. Veículos" required="required" />
					<em class="desc">Quantidade de veículos disponíveis.</em>
				</div>
				<div class="col-sm-2 col">
					<label for="brc_excursao_quant_assentos">Assentos por veículo</label>
					<input name="brc_excursao_quant_assentos" id="brc_excursao_quant_assentos" type="number" min="0" value="<?php echo $brc_excursao_quant_assentos; ?>" placeholder="Quant. Veículos" required="required" />
					<em class="desc">Quantidade de assentos em cada veículo.</em>
				</div>
				<div class="col-sm-3 col">
					<label>
						<input type="checkbox" name="brc_excursao_ingresso" id="brc_excursao_ingresso" value="yes" <?php echo $brc_excursao_ingresso?'checked="checked"':''; ?> />
						Venda de ingresso? 
						<span class="_ingresso" <?php echo $brc_excursao_ingresso?'style="display:inline-block;"':''; ?>>Quant. Disp.: </span>
					</label>
					
					<input class="_ingresso" <?php echo $brc_excursao_ingresso?'style="display:block;"':''; ?> name="brc_excursao_ingresso_stock" id="brc_excursao_ingresso_stock" type="number" min="0" value="<?php echo $brc_excursao_ingresso_stock; ?>" placeholder="Quantidade" />
				</div>
				<div class="col-sm-3 col _ingresso" <?php echo $brc_excursao_ingresso?'style="display:block;"':''; ?>>
					<label>Preço do Ingresso</label>
					<input name="brc_excursao_ingresso_preco" id="brc_excursao_ingresso_preco" type="number" min="0" step="any" value="<?php echo $brc_excursao_ingresso_preco; ?>" placeholder="Preço" />
				</div>
			</div>
		</div>
		
		<div id="brc_add_product_excursao_variations">
			<div class="brc_form_grid_inner_table">
				<h3>Pacotes disponíveis</h3>
				<div class="brc_form_grid_inner_table_body">
					<div class="row">
						<input type="hidden" name="brc_excursao_variations_id[]" value="<?php echo $p_quarto['variation_id']; ?>" />
						
						<div class="col-sm-3 col">
							<label>Nome do pacote <?php echo $p_quarto['variation_id']?'#'.$p_quarto['variation_id']:''; ?></label>
							<input name="brc_excursao_variations_nome[]" type="text" value="<?php echo $brc_excursao_variations_nome; ?>" placeholder="Nome/Tipo" required="required" />
						</div>
						<div class="col-sm-3 col">
							<label>Hotel</label>
							<select name="brc_excursao_variations_hotel[]" class="select2 sel-viagem" required="required">
								<option value="no">--</option>
							<?php
								foreach($sel_hoteis as $k => $j){
									echo '<option value="'. $k .'" '.($brc_excursao_variations_hotel==$k?'selected':'').'>'. $j .'</option>';
								}
							?>
							</select>
						</div>
						
						<div class="col-sm-1 col">
							<label>Quant.</label>
							<input name="brc_excursao_variations_stock[]" type="number" min="0" value="<?php echo $p_quarto['max_qty']; ?>" placeholder="Vagas disponíveis" required="required" />
							<em class="desc">"Estoque".</em>
						</div>
						
						<div class="col-sm-1 col">
							<label>P/Quarto</label>
							<input name="brc_excursao_variations_pquarto[]" type="number" min="0" value="<?php echo $brc_excursao_variations_pquarto; ?>" placeholder="Vagas" required="required" />
						</div>
						<div class="col-sm-2 col">
							<label>Valor total do pacote</label>
							<input name="brc_excursao_variations_preco[]" type="number" min="0" value="<?php echo $p_quarto['display_price']; ?>" placeholder="Preço" step="any" required="required" />
							<em class="desc">Incluindo todas as pessoas.</em>
						</div>
						<div class="col-sm-2 col">
							<label>&nbsp;</label>
							<a href="#" class="brc_admin_btn btn_grid button brc_add_product_excursao_add_variation" title="Adicionar quarto" data-etapa="ida">
							<span class="dashicons dashicons-plus"></span> Adicionar</a>
						</div>
						
						<div class="col-sm-10 col col-descricao">
							<label>Descrição do pacote</label>
							<input name="variable_description[]" type="text" value="<?php echo wp_strip_all_tags($p_quarto['variation_description']); ?>" placeholder="Descreva sobre as regras e observações do pacote" />
						</div>
					</div>
					<div class="variations-wrapper">
						<div class="variations-ajax">
							<!-- ajax -->
						<?php
							if($screen->action != 'add'){
								if($quartos){
									foreach($quartos as $qk => $qj){
										include FEPA_PLUGIN_DIR .'/templates/brc_admin.product_excursao_field_variation.php';
									}
								}
							}
						?>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		
	<?php
		if($screen->action != 'add'){
			$brc_excursao_has_child_rule = get_post_meta($post_ID, 'brc_excursao_has_child_rule', true);
			
			if($brc_excursao_has_child_rule){
				
				$brc_excursao_child_rule = get_post_meta($post_ID, 'brc_excursao_child_rule', true);
				$f_child_rule = array_shift($brc_excursao_child_rule);
			}
		}
		?>
		<div id="brc_add_product_excursao_child">
			<div class="brc_form_grid_inner_table">
				<h3>
					<label>
						Adicionar regras para crianças
						
						<input type="checkbox" name="brc_excursao_has_child_rule" id="brc_excursao_has_child_rule" value="yes" <?php echo $brc_excursao_has_child_rule?'checked="checked"':''; ?> />
					</label>
				</h3>
				<div class="brc_form_grid_inner_table_body _child" <?php echo $brc_excursao_has_child_rule?'style="display:block;"':''; ?>>
					<div class="row">
						<input type="hidden" name="brc_add_product_excursao_child_id[]" value="1" />
						<div class="col-sm-3 col">
							<label>Nome da regra</label>
							<input name="brc_add_product_excursao_child_nome[]" type="text" value="<?php echo $f_child_rule['rule_nome']; ?>" placeholder="Nome/Tipo" required="required" />
							<em class="desc">Exp.: 0 à 6 anos</em>
						</div>
						<div class="col-sm-3 col">
							<label>Descrição</label>
							<input name="brc_add_product_excursao_child_detalhes[]" type="text" value="<?php echo $f_child_rule['rule_detalhes']; ?>" placeholder="Regras e detalhes" />
							<em class="desc">Adicione um texto para explicar mais detalhes.</em>
						</div>
						<div class="col-sm-2 col">
							<label>Valor</label>
							<input name="brc_add_product_excursao_child_preco[]" type="number" min="0" value="<?php echo $f_child_rule['rule_preco']; ?>" placeholder="Preço" step="any" required="required" />
						</div>
						<div class="col-sm-2 col-sm-offset-2 col">
							<label>&nbsp;</label>
							<a href="#" class="brc_admin_btn btn_grid button brc_add_product_excursao_add_child" title="Adicionar" data-etapa="ida">
							<span class="dashicons dashicons-plus"></span> Adicionar</a>
						</div>
					</div>
					<div class="child-wrapper">
						<div class="child-ajax">
							<!-- ajax -->
						<?php
							if($screen->action != 'add'){
								if($brc_excursao_child_rule){
									foreach($brc_excursao_child_rule as $qk => $qj){
										include FEPA_PLUGIN_DIR .'/templates/brc_admin.product_excursao_field_child.php';
									}
								}
							}
						?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>