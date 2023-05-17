<?php
	$datainicio = false;
	$datafim = false;
	
	$ufs = get_uf();
	$terms = get_terms(array(
		'taxonomy' => 'brc_ponto_embarque',
		'hide_empty' => false,
	));
	$options_pontos_embarque = array();
	if(!is_wp_error($terms)){
		foreach($terms as $k => $j){
			$option_name = $j->name;
			$brc_cidade = get_term_meta($j->term_id, 'brc_cidade', true);
			if($brc_cidade){
				$cidade = get_term($brc_cidade);
				if(!is_wp_error($cidade)){
					$uf = get_term_meta($brc_cidade, 'brc_uf', true);
					$ufs = get_uf($uf);
					$option_name .= ' - '. $cidade->name.'/'.strtoupper($ufs['sigla']);
				}
			}
			$options_pontos_embarque[$j->term_id] =  $option_name;
		}
	}
	
	
	
	if(isset($_GET['datainicio'])){
		if($_GET['datainicio']){
			$datainicio = $_GET['datainicio'];
		}
	}
	if(isset($_GET['datafim'])){
		if($_GET['datafim']){
			$datafim = $_GET['datafim'];
		}
	}
	?>
	<form action="<?php echo get_permalink(get_option('fepa_busca')); ?>" method="get" id="busca-viagens-form">
		<div class="brc_vc_wrapper fepa_busca_viagens <?php echo esc_attr($css_class); ?>">
			<div class="wrapper-inner busca-form-loader">
				<div class="col col-enderecos">
					<div class="col-inner-wrapper col-overlay">
						<div class="col-inner col-enderecos-left">
							<select class="form-field busca-select2" name="cidade-origem" id="cidade-origem" placeholder="Origem">
								<option value="0">Origem</option>
							<?php
								if($options_pontos_embarque){
									foreach($options_pontos_embarque as $k => $j){
									?>
										<option value="<?php echo $k; ?>" <?php echo $_GET['cidade-origem']==$k?'selected':''; ?>><?php echo $j; ?></option>
									<?php
									}
								}								
							?>
							</select>
						</div>
						<div class="col-inner col-enderecos-right">
							<select class="form-field busca-select2" name="cidade-destino" id="cidade-destino" placeholder="Destino">
								<option value="0">Destino</option>
							<?php
								if($options_pontos_embarque){
									foreach($options_pontos_embarque as $k => $j){
									?>
										<option value="<?php echo $k; ?>" <?php echo $_GET['cidade-destino']==$k?'selected':''; ?>><?php echo $j; ?></option>
									<?php
									}
								}								
							?>
							</select>
						</div>
					</div>
				</div>
				
				<div class="col-separator"></div>
				
				<div class="col col-data">
					<div class="col-inner-wrapper col-overlay">
						<div class="col-inner">
							<input class="form-field data date-field" name="datainicio" id="datainicio" data-toggle="datepicker" 
							<?php echo $datainicio?'value="'.$datainicio.'"':'';  ?>
							placeholder="Escolher" type="text" />
							
							<label for="datainicio">Ida</label>
						</div>
						<span class="far fa-calendar-alt icon"></span>
					</div>
				</div>
				
				<div class="col col-data">
					<div class="col-inner-wrapper col-overlay">
						<div class="col-inner">
							<input class="form-field data date-field" name="datafim" id="datafim" data-toggle="datepicker" 
							<?php echo $datafim?'value="'.$datafim.'"':'';  ?>
							placeholder="Escolher" type="text" />
							
							<label for="datafim">Volta</label>
						</div>
						<span class="far fa-calendar-alt icon"></span>
					</div>
				</div>
				
				<div class="col col-button">
					<div class="col-inner-wrapper">
						<button type="submit" class="form-submit animado" >Buscar</button>
					</div>
				</div>
			</div>
		</div>
	</form>
	<?php
?>
