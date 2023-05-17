<div class="row brc_order_produto_item_hospede">
	<input type="hidden" name="brc_order_produto_var_id[]" value="<?php echo $variation_data['id']; ?>" />
	<input type="hidden" name="brc_order_produto_var_nome[]" value="Quarto: <?php echo $brc_excursao_variations_nome; ?> - Hotel: <?php echo get_the_title($brc_excursao_variations_hotel); ?>" />
	
	<div class="col-sm-4 col">
		<label>Nome do hóspede</label>
		<input name="_hopede_nome[]" type="text" placeholder="Nome do hóspede" required="required" />
	</div>
	<div class="col-sm-3 col">
		<label>CPF do hóspede</label>
		<input name="_hopede_cpf[]" type="text" class="cpf" placeholder="Nome do hóspede" required="required" />
	</div>
	<div class="col-sm-3 col">
		<?php if($brc_excursao_ingresso): ?>
			<label>&nbsp;</label>
			<label><input name="_ingresso_verf[]" value="yes" type="checkbox"/> Adicionar Ingresso? + <?php echo wc_price($brc_excursao_ingresso_preco); ?></label>
			<input type="hidden" name="_ingresso[]" value="no" />
		<?php endif; ?>
	</div>
	<div class="col-sm-2 col">
		<label>&nbsp;</label>
		<a href="#" class="brc_admin_btn btn_grid btn_vermelho button brc_order_produto_remove_hospede" title="Remover Hóspede">
		<span class="dashicons dashicons-no-alt"></span>Remover Hóspede</a>
	</div>
</div>