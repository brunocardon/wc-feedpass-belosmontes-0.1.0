<?php
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
	
	// QUARTO UM DADOS
	if($screen->action != 'add'){
		//pre_debug($j);
		
		$qj_excursao_variations_hotel 	= get_post_meta($qj['variation_id'], 'brc_excursao_variations_hotel', true);
		$qj_excursao_variations_nome 	= get_post_meta($qj['variation_id'], 'brc_excursao_variations_nome', true);
		$qj_excursao_variations_pquarto = get_post_meta($qj['variation_id'], 'brc_excursao_variations_pquarto', true);
		$qj_attribute_quartos 			= get_post_meta($qj['variation_id'], 'attribute_quartos', true);
	}
	?>
	<div class="variations-item ajax">
		<div class="row">
			<input type="hidden" name="brc_excursao_variations_id[]" value="<?php echo $qj['variation_id']; ?>" />
			
			<div class="col-sm-3 col">
				<label>Nome do pacote <?php echo $qj['variation_id']?'#'.$qj['variation_id']:''; ?></label>
				<input name="brc_excursao_variations_nome[]" type="text" value="<?php echo $qj_excursao_variations_nome; ?>" placeholder="Nome/Tipo" required="required" />
			</div>
			<div class="col-sm-3 col">
				<label>Selecione o Hotel </label>
				<select name="brc_excursao_variations_hotel[]" class="select2 sel-viagem" required="required">
					<option value="no">--</option>
				<?php
					foreach($sel_hoteis as $k => $j){
						echo '<option value="'. $k .'" '.($qj_excursao_variations_hotel==$k?'selected':'').'>'. $j .'</option>';
					}
				?>
				</select>
			</div>
			<div class="col-sm-1 col">
				<label>Quant.</label>
				<input name="brc_excursao_variations_stock[]" type="number" min="0" value="<?php echo $qj['max_qty']; ?>" placeholder="Vagas disponíveis" required="required" />
				<em class="desc">"Estoque".</em>
			</div>
			<div class="col-sm-1 col">
				<label>P/Quarto</label>
				<input name="brc_excursao_variations_pquarto[]" type="number" min="0" value="<?php echo $qj_excursao_variations_pquarto; ?>" placeholder="Vagas" required="required" />
			</div>
			<div class="col-sm-2 col">
				<label>Valor total do pacote</label>
				<input name="brc_excursao_variations_preco[]" type="number" min="0" value="<?php echo $qj['display_price']; ?>" placeholder="Preço" step="any" required="required" />
				<em class="desc">Incluindo todas as pessoas.</em>
			</div>
			<div class="col-sm-2 col">
				<label>&nbsp;</label>
				<a href="#" data-varid="<?php echo $qj['variation_id']; ?>" 
					class="brc_admin_btn btn_grid btn_vermelho button brc_add_product_excursao_remove_variation" 
					title="Adicionar quarto">
				<span class="dashicons dashicons-no-alt"></span>Remover</a>
			</div>
			
			<div class="col-sm-10 col col-descricao">
				<label>Descrição do pacote</label>
				<input name="variable_description[]" type="text" value="<?php echo wp_strip_all_tags($qj['variation_description']); ?>" placeholder="Descreva sobre as regras e observações do pacote" />
			</div>
		</div>
	</div>