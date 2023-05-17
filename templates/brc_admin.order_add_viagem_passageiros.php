<div class="row brc_order_produto_item_passageiro">
	<input type="hidden" name="brc_order_produto_passageiro[]" value="<?php echo $product_id; ?>" />
	
	<div class="col-sm-4 col">
		<label>Nome do passageiro</label>
		<input name="_passageiro_nome[]" type="text" placeholder="Nome do passageiro" required="required" />
	</div>
	<div class="col-sm-3 col">
		<label>CPF do passageiro</label>
		<input name="_passageiro_cpf[]" type="text" class="cpf" placeholder="Nome do passageiro" required="required" />
	</div>
	<div class="col-sm-3 col">
		<label>&nbsp;</label>
	</div>
	<div class="col-sm-2 col">
		<label>&nbsp;</label>
		<a href="#" class="brc_admin_btn btn_grid btn_vermelho button brc_order_produto_remove_passageiro" title="Remover passageiro">
		<span class="dashicons dashicons-no-alt"></span>Remover passageiro</a>
	</div>
</div>