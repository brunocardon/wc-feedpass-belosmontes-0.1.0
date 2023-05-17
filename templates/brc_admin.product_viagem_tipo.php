<?php
	$dia_semana = get_dia_semana();
	$embarques 	= get_embarques();
	$screen 	= get_current_screen();
	$product_id = get_the_ID();
	$brc_viagem_tipo_cadastro = get_post_meta($product_id, 'brc_viagem_tipo_cadastro', true);
	$viagem_tipo = get_viagem_tipo();
	
	?>
	<input type="hidden" name="brc_viagem" value="false" />
	<div class="brc_form_grid">
		<ul class="brc_notices"></ul>
		<!-- notices -->
		
		<div id="brc_add_product_viagem_principal">
			<div class="row">
				<div class="col-sm-2 col">
					<label for="brc_viagem_tempo">Tipo de cadastro de viagem</label>
					<select name="brc_viagem_tipo_cadastro" id="brc_viagem_tipo_cadastro" required="required">
					<?php
						foreach($viagem_tipo as $k => $j){
							echo '<option value="'. $k .'" '.($brc_viagem_tipo_cadastro==$k?'selected':'').'>'. $j .'</option>';
						}
					?>	
					</select>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-6 col">
					<div class="produto-tipo-descricao <?php echo $brc_viagem_tipo_cadastro==1?'on':''; ?>" id="produto-tipo-descricao-bate-volta">
						<p>
							- As viagens "Bate e Volta" consistem em um único produto com
							dois trajetos e datas, sendo uma ida e uma volta.<br/>
							- Nesta modalidade a <strong>escolha de poltronas</strong> pelo cliente não é habilitada, 
							sendo a distribuição feita pelo administrador do site posteriormente.<br/>
							- Também é possível escolher 1 (um) ou mais veículos para serem preenchidos.
						</p>
					</div>
					<div class="produto-tipo-descricao <?php echo $brc_viagem_tipo_cadastro==2?'on':''; ?>" id="produto-tipo-descricao-grupo">
						<p>Sem Descrição</p>
					</div>
					<div class="produto-tipo-descricao <?php echo $brc_viagem_tipo_cadastro==3?'on':''; ?>" id="produto-tipo-descricao-linha">
						<p>
							- As viagens "Viagem Linha" consistem em um produto fechado com trajeto fixo sem retorno.<br/>
							- Nesta modalidade a <strong>escolha de poltronas</strong> é feita pelo cliente, 
							porém pode ser alterada pelo administrador posteriormente <br/>
							- Cadastro de apenas 1 (um) veículo.
						</p>
					</div>
				</div>
			</div>
		</div>
	</div>