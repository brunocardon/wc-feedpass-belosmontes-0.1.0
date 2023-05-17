<?php
	foreach($quartos as $qk => $qj){
		
		// INGRESSO
		$brc_excursao_ingresso 				= get_post_meta($post_id, 'brc_excursao_ingresso', true);
		$brc_excursao_ingresso_stock 		= get_post_meta($post_id, 'brc_excursao_ingresso_stock', true);
		$brc_excursao_ingresso_preco 		= get_post_meta($post_id, 'brc_excursao_ingresso_preco', true);
		
		// QUARTO DADOS
		$brc_excursao_variations_hotel 		= get_post_meta($qj['variation_id'], 'brc_excursao_variations_hotel', true);
		$brc_excursao_variations_nome 		= get_post_meta($qj['variation_id'], 'brc_excursao_variations_nome', true);
		$brc_excursao_variations_pquarto 	= get_post_meta($qj['variation_id'], 'brc_excursao_variations_pquarto', true);
		$attribute_quartos 					= get_post_meta($qj['variation_id'], 'attribute_quartos', true);
		
		$hoteis[$brc_excursao_variations_hotel] = get_the_title($brc_excursao_variations_hotel);
		
		$esgotado = $qj['max_qty']<=0?true:false;
	?>
		<div class="brc_form_grid_inner_table">
			<h3>
				Quarto: <?php echo $brc_excursao_variations_nome; ?> - Hotel: <?php echo get_the_title($brc_excursao_variations_hotel); ?>
				<a 
					href="#" 
					class="brc_admin_btn btn_grid button brc_order_produto_add_hospede" 
					title="Adicionar Hóspede" 
					data-product_id="<?php echo $product_id; ?>"
					data-var_id="<?php echo $qj['variation_id']; ?>"
				>
				<span class="dashicons dashicons-plus"></span>Adicionar Hóspede</a>
			</h3>
			
			<!-- AJAX-02 -->
			<div class="brc_form_grid_inner_table_body"></div>
			<!-- AJAX-02 -->
		</div>
	<?php
	}
?>