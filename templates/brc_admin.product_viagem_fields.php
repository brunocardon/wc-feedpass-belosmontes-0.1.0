<?php
	
	$dia_semana = get_dia_semana();
	$embarques 	= get_embarques();
	$screen 	= get_current_screen();
	$product_id = get_the_ID();
	
	//pre_debug($screen);
	//[action] => add
	
	$brc_viagem_masscad = get_post_meta($product_id, 'brc_viagem_masscad', true);
	$brc_viagem_masscad_ida_semana = get_post_meta($product_id, 'brc_viagem_masscad_ida_semana', true);
	$brc_viagem_masscad_ida_hora = get_post_meta($product_id, 'brc_viagem_masscad_ida_hora', true);
	$brc_viagem_masscad_volta_semana = get_post_meta($product_id, 'brc_viagem_masscad_volta_semana', true);
	$brc_viagem_masscad_volta_hora = get_post_meta($product_id, 'brc_viagem_masscad_volta_hora', true);
	$brc_viagem_masscad_periodo_ini = get_post_meta($product_id, 'brc_viagem_masscad_periodo_ini', true);
	$brc_viagem_masscad_periodo_fim = get_post_meta($product_id, 'brc_viagem_masscad_periodo_fim', true);
	
	$brc_viagem_ida_data = get_post_meta($product_id, 'brc_viagem_ida_data', true);
	$brc_viagem_unique_ida_dia = date('Y-m-d', $brc_viagem_ida_data);
	$brc_viagem_unique_ida_hora = get_post_meta($product_id, 'brc_viagem_ida_data_hora', true);
	$brc_viagem_volta_data = get_post_meta($product_id, 'brc_viagem_volta_data', true);
	$brc_viagem_unique_volta_dia = date('Y-m-d', $brc_viagem_volta_data);
	$brc_viagem_unique_volta_hora = get_post_meta($product_id, 'brc_viagem_volta_data_hora', true);
	
	$brc_viagem_tempo = get_post_meta($product_id, 'brc_viagem_tempo', true);
	$brc_viagem_origem = get_post_meta($product_id, 'brc_viagem_origem', true);
	$brc_viagem_destino = get_post_meta($product_id, 'brc_viagem_destino', true);
	$brc_viagem_preco = get_post_meta($product_id, 'brc_viagem_preco', true);
	$brc_viagem_preco = get_post_meta($product_id, '_regular_price', true);
	$brc_viagem_linha = get_post_meta($product_id, 'brc_viagem_linha', true);
	$brc_viagem_veiculos = get_post_meta($product_id, 'brc_viagem_veiculos', true);
	$brc_viagem_motorista = get_post_meta($product_id, 'brc_viagem_motorista', true);
	$brc_viagem_veiculos_quant = get_post_meta($product_id, 'brc_viagem_veiculos_quant', true);
	$brc_viagem_assentos = get_post_meta($product_id, 'brc_viagem_assentos', true);
	$brc_viagem_assentos_veiculos = get_post_meta($product_id, 'brc_viagem_assentos_veiculos', true);
	$_stock = get_post_meta($product_id, '_stock', true);
	
	?>
	<input type="hidden" name="brc_viagem" value="false" />
	<div class="brc_form_grid">
		<ul class="brc_notices"></ul>
		<!-- notices -->
		
		<div id="brc_add_product_viagem_principal">
		<?php
			if($screen->action == 'add'):
			?>
				<!-- TIPO DE CRIAÇÃO -->
				<div class="row">
					<div class="col-sm-6 col">
						<h3>Tipo de cadastro</h3>
						<label>
							<input type="checkbox" name="brc_viagem_masscad" id="brc_viagem_masscad" value="yes" <?php echo $brc_viagem_masscad?'checked="checked"':''; ?> />
							Cadastro recorrente.
						</label>
						<em class="desc">
							Caso deseje criar um cadastro em massa, selecione essa opção para poder configurar o período de cadastro de repetição.
						</em>
					</div>
				</div>
				
				<!-- CADASTRO EM MASSA -->
				<div class="brc_form_grid_inner_table masscad" id="brc_viagem_masscad_wrapper">
					<h3>Cadastro em massa</h3>
					<div class="brc_form_grid_inner_table_body">
						<div class="row">
							<div class="col-sm-2 col data-hora">
								<label for="brc_viagem_masscad_ida_semana">Data do embarque de ida</label>
								<select name="brc_viagem_masscad_ida_semana" id="brc_viagem_masscad_ida_semana">
									<option value="0">--</option>
								<?php
									foreach($dia_semana as $k => $j){
										
										echo '<option value="'. $k .'" '.($brc_viagem_masscad_ida_semana==$j?'selected':'').'>'. $j .'</option>';
									}
								?>	
								</select>
								<input name="brc_viagem_masscad_ida_hora" id="brc_viagem_masscad_ida_hora" type="time" value="<?php echo $brc_viagem_masscad_ida_hora; ?>" />
							</div>
							<div class="col-sm-2 col data-hora">
								<label for="brc_viagem_masscad_volta_semana">Data do embarque de volta</label>
								<select name="brc_viagem_masscad_volta_semana" id="brc_viagem_masscad_volta_semana" class="">
									<option value="0">--</option>
								<?php
									foreach($dia_semana as $k => $j){
										
										echo '<option value="'. $k .'" '.($brc_viagem_masscad_volta_semana==$j?'selected':'').'>'. $j .'</option>';
									}
								?>	
								</select>
								<input name="brc_viagem_masscad_volta_hora" id="brc_viagem_masscad_volta_hora" type="time" value="<?php echo $brc_viagem_masscad_volta_hora; ?>" />
							</div>
							<div class="col-sm-2 col">
								<label for="brc_viagem_masscad_periodo_ini">Período Início</label>
								<input name="brc_viagem_masscad_periodo_ini" id="brc_viagem_masscad_periodo_ini" type="date" value="<?php echo $brc_viagem_masscad_periodo_ini; ?>" />
							</div>
							<div class="col-sm-2 col">
								<label for="brc_viagem_masscad_periodo_fim">Período Final</label>
								<input name="brc_viagem_masscad_periodo_fim" id="brc_viagem_masscad_periodo_fim" type="date" value="<?php echo $brc_viagem_masscad_periodo_fim; ?>" />
							</div>
						</div>
					</div>
				</div><!-- CADASTRO EM MASSA -->
				
				<!-- CADASTRO ÚNICO -->
				<div class="brc_form_grid_inner_table uniquecad" id="brc_viagem_unique_wrapper">
					<h3>Cadastro único</h3>
					<div class="brc_form_grid_inner_table_body">
						<div class="row">
							<div class="col-sm-3 col data-hora">
								<label for="brc_viagem_unique_ida_dia">Data do embarque de ida</label>
								<input name="brc_viagem_unique_ida_dia" id="brc_viagem_unique_ida_dia" type="date" value="<?php echo $brc_viagem_unique_ida_dia; ?>" />
								<input name="brc_viagem_unique_ida_hora" id="brc_viagem_unique_ida_hora" type="time" value="<?php echo $brc_viagem_unique_ida_hora; ?>" />
							</div>
							<div class="col-sm-3 col data-hora">
								<label for="brc_viagem_unique_volta_dia">Data do embarque de volta</label>
								<input name="brc_viagem_unique_volta_dia" id="brc_viagem_unique_volta_dia" type="date" value="<?php echo $brc_viagem_unique_volta_dia; ?>" />
								<input name="brc_viagem_unique_volta_hora" id="brc_viagem_unique_volta_hora" type="time" value="<?php echo $brc_viagem_unique_volta_hora; ?>" />
							</div>
						</div>
					</div>
				</div><!-- CADASTRO ÚNICO -->
		<?php else: ?>	
			<div class="row">
				<div class="col-sm-3 col data-hora">
					<label for="brc_viagem_unique_ida_dia">Data do embarque de ida</label>
					<input name="brc_viagem_unique_ida_dia" id="brc_viagem_unique_ida_dia" type="date" value="<?php echo $brc_viagem_unique_ida_dia; ?>" />
					<input name="brc_viagem_unique_ida_hora" id="brc_viagem_unique_ida_hora" type="time" value="<?php echo $brc_viagem_unique_ida_hora; ?>" />
				</div>
				<div class="col-sm-3 col data-hora">
					<label for="brc_viagem_unique_volta_dia">Data do embarque de volta</label>
					<input name="brc_viagem_unique_volta_dia" id="brc_viagem_unique_volta_dia" type="date" value="<?php echo $brc_viagem_unique_volta_dia; ?>" />
					<input name="brc_viagem_unique_volta_hora" id="brc_viagem_unique_volta_hora" type="time" value="<?php echo $brc_viagem_unique_volta_hora; ?>" />
				</div>
			</div>
			<hr/>
		<?php endif; ?>
			
			<!-- DADOS PADRÕES -->
			<div class="row">
				<div class="col-sm-2 col">
					<label for="brc_viagem_tempo">Tempo aproximado de viagem</label>
					<input name="brc_viagem_tempo" id="brc_viagem_tempo" type="number" min="0" value="<?php echo $brc_viagem_tempo; ?>" placeholder="Valor em minutos" class="reqr" />
					<em class="desc">Informar valor em minutos</em>
				</div>
				<div class="col-sm-2 col">
					<label for="brc_viagem_origem">Local de origem</label>
					<select name="brc_viagem_origem" id="brc_viagem_origem" class="reqr">
						<option value="0">--</option>
					<?php
						if($embarques){
							foreach($embarques as $k => $j){
								
								echo '<option value="'. $k .'" '.($brc_viagem_origem==$k?'selected':'').'>'. $j .'</option>';
							}
						}
					?>	
					</select>
					<em class="desc">
						Cidade e local do embarque de origem.<br/>
						Este local também serve como ponto de chegada da viagem de volta.
					</em>
				</div>
				<div class="col-sm-2 col">
					<label for="brc_viagem_destino">Local de destino</label>
					<select name="brc_viagem_destino" id="brc_viagem_destino" class="reqr">
						<option value="0">--</option>
					<?php
						if($embarques){
							foreach($embarques as $k => $j){
								
								echo '<option value="'. $k .'" '.($brc_viagem_destino==$k?'selected':'').'>'. $j .'</option>';
							}
						}
					?>	
					</select>
					<em class="desc">
						Cidade e local do desembarque de destino.<br/>
						Este local também serve como ponto de partida da viagem de volta.
					</em>
				</div>
				<div class="col-sm-2 col">
					<label for="brc_viagem_preco">Valor da Passagem (R$)</label>
					<input name="brc_viagem_preco" id="brc_viagem_preco" type="number" step="any" min="0" value="<?php echo $brc_viagem_preco; ?>" placeholder="Valor em reais"class="reqr" />
				</div>
				<div class="col-sm-2 col">
					<label for="brc_viagem_linha">Linha da viagem</label>
					<select name="brc_viagem_linha" id="brc_viagem_linha" class="reqr">
						<option value="0">--</option>
					<?php
						$brc_linhas_args = get_terms(array(
							'taxonomy' 		=> 'brc_linha_viagem',
							'hide_empty' 	=> false,
							'orderby' 		=> 'name',
							'order' 		=> 'ASC',
							'parent' 		=> 0,
						));
						if(!is_wp_error($brc_linhas_args)){
							foreach($brc_linhas_args as $k => $j){
								echo '<option value="'. $j->term_id .'" '.($j->term_id==$brc_viagem_linha?'selected':'').'>'. $j->name .'</option>';
							}
						}
					?>	
					</select>
					<em class="desc">
						Linha para apresentar os pontos de embarque opcionais do trajeto.
					</em>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-2 col">
					<label for="brc_viagem_veiculos">Veículo(s)</label>
					<select name="brc_viagem_veiculos[]" id="brc_viagem_veiculos" class="select2" multiple="multiple">
						<option value="0">--</option>
					<?php
						$brc_veiculo_args = get_terms(array(
							'taxonomy' 		=> 'brc_veiculo',
							'hide_empty' 	=> false,
							'orderby' 		=> 'name',
							'order' 		=> 'ASC',
							'parent' 		=> 0,
						));
						if(!is_wp_error($brc_veiculo_args)){
							foreach($brc_veiculo_args as $k => $j){
								echo '<option value="'. $j->term_id .'" '.(in_array($j->term_id, $brc_viagem_veiculos)?'selected':'').'>'. $j->name .'</option>';
							}
						}
					?>	
					</select>
					<em class="desc">
						Informe o(s) modelo(s) do veículo(s).
					</em>
				</div>
				<div class="col-sm-2 col">
					<label for="brc_viagem_motorista">Motorista(s)</label>
					<select name="brc_viagem_motorista[]" id="brc_viagem_motorista" class="select2" multiple="multiple">
						<option value="0">--</option>
					<?php
						$brc_motorista_args = get_terms(array(
							'taxonomy' 		=> 'brc_motorista',
							'hide_empty' 	=> false,
							'orderby' 		=> 'name',
							'order' 		=> 'ASC',
							'parent' 		=> 0,
						));
						if(!is_wp_error($brc_motorista_args)){
							foreach($brc_motorista_args as $k => $j){
								echo '<option value="'. $j->term_id .'" '.(in_array($j->term_id, $brc_viagem_motorista)?'selected':'').'>'. $j->name .'</option>';
							}
						}
					?>	
					</select>
					<em class="desc">
						Informe o(s) motorista(s).
					</em>
				</div>
				<div class="col-sm-2 col">
					<label for="brc_viagem_veiculos_quant">Quant. Veículos</label>
					<input name="brc_viagem_veiculos_quant" id="brc_viagem_veiculos_quant" type="number" min="0" value="<?php echo $brc_viagem_veiculos_quant; ?>" placeholder="Valor em minutos"class="reqr" />
					<em class="desc">
						Quantidade de veículos disponíveis. 
					</em>
				</div>
				<div class="col-sm-2 col">
					<label for="brc_viagem_assentos_veiculos">Assentos por veículo</label>
					<input name="brc_viagem_assentos_veiculos" id="brc_viagem_assentos_veiculos" type="number" min="0" value="<?php echo intval($brc_viagem_assentos_veiculos); ?>" placeholder="Valor em minutos"class="reqr" />
					<em class="desc">
						Quantidade de assentos por veículo. 
					</em>
				</div>
				<div class="col-sm-2 col">
					<label for="brc_viagem_assentos">Assentos disponíveis (estoque)</label>
					<input name="brc_viagem_assentos" id="brc_viagem_assentos" type="number" min="0" value="<?php echo intval($_stock); ?>" placeholder="Valor em minutos"class="reqr" />
					<em class="desc">
						Quantidade total de assentos disponíveis.
					</em>
				</div>
			</div>
		</div>
	</div>