	<div class="child-item ajax">
		<div class="row">
			<input type="hidden" name="brc_add_product_excursao_child_id[]" value="1" />
			<div class="col-sm-3 col">
				<label>Nome da regra</label>
				<input name="brc_add_product_excursao_child_nome[]" type="text" value="<?php echo $qj['rule_nome']; ?>" placeholder="Nome/Tipo" required="required" />
				<em class="desc">Exp.: 0 à 6 anos</em>
			</div>
			<div class="col-sm-3 col">
				<label>Descrição</label>
				<input name="brc_add_product_excursao_child_detalhes[]" type="text" value="<?php echo $qj['rule_detalhes']; ?>" placeholder="Regras e detalhes" />
				<em class="desc">Adicione um texto para explicar mais detalhes.</em>
			</div>
			<div class="col-sm-2 col">
				<label>Valor</label>
				<input name="brc_add_product_excursao_child_preco[]" type="number" min="0" value="<?php echo $qj['rule_preco']; ?>" placeholder="Preço" step="any" required="required" />
			</div>
			
			<div class="col-sm-2 col-sm-offset-2 col">
				<label>&nbsp;</label>
				<a href="#" class="brc_admin_btn btn_grid btn_vermelho button brc_add_product_excursao_remove_child" title="Adicionar">
				<span class="dashicons dashicons-no-alt"></span>Remover</a>
			</div>
		</div>
	</div>