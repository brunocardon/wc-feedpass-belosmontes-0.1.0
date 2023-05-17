<div id="ex-content">
	<?php the_content(); ?>
</div>
<div id="ex-product">
	<?php $post_id = get_the_ID(); ?>
	<?php $product = wc_get_product($post_id); ?>
	
	<!-- INFORMAÇÕES DO PACOTE -->
	<?php $roteiro_group = get_post_meta($post_id, 'roteiro_group', true); ?>
	<?php if($roteiro_group): ?>
	<div id="ex-pacote-info" class="ex-block">
		<div class="l-section-h">
			<div class="main-title">
				<h3>Informações do Pacote <small>(Roteiro)</small></h3>
			</div>
			
			<div class="info-quad">
				<div class="info-header">
					<div class="col etapa">Etapa</div>
					<div class="col roteiro">Roteiro</div>
					<div class="col descricao">Descrição</div>
				</div>
				<?php foreach($roteiro_group as $k => $j): ?>
				<div class="info-body">
					<div class="col etapa"><?php echo $j['etapa-nome']; ?></div>
					<div class="col roteiro"><?php echo $j['etapa-titulo']; ?></div>
					<div class="col descricao"><?php echo nl2br($j['etapa-desc']); ?></div>
				</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
	<?php endif; ?>
	<!-- INFORMAÇÕES DO PACOTE -->
	
	<!-- VARIACAO -->
	<?php $quartos = $product->get_available_variations();?>
	<?php if($quartos):	?>
	<form action="#" id="brc-single-excursao-form">
		<input type="hidden" name="action" value="brc_comprar_excursao" />
		<div id="ex-product-config" class="ex-block">
			<div class="l-section-h">
				<div class="main-title">
					<h3>Escolher pacotes <small>(Configurar compra)</small></h3>
				</div>
				<ul class="brc-notices"></ul>
				
				<div class="variacoes-wrapper" id="excursao-variacoes">
				<?php
					foreach($quartos as $qk => $qj){
						$brc_excursao_variations_hotel 		= get_post_meta($qj['variation_id'], 'brc_excursao_variations_hotel', true);
						$brc_excursao_variations_nome 		= get_post_meta($qj['variation_id'], 'brc_excursao_variations_nome', true);
						$brc_excursao_variations_pquarto 	= get_post_meta($qj['variation_id'], 'brc_excursao_variations_pquarto', true);
						$attribute_quartos 					= get_post_meta($qj['variation_id'], 'attribute_quartos', true);
						$hoteis[$brc_excursao_variations_hotel] = get_the_title($brc_excursao_variations_hotel);
						$esgotado = $qj['max_qty']<=0?true:false;
						
						$pp_price = floatval($qj['display_price']) / intval($brc_excursao_variations_pquarto);
					?>
						<div class="variacao-item <?php echo $esgotado?'esgotado':''; ?>">
							<input type="hidden" value="<?php echo $qj['variation_id']; ?>" id="variation_id-<?php echo $qj['variation_id']; ?>" name="variation_id[]" />
							
							<div class="variacao-item-wrapper">
								<div class="variacao-item-inner">
									<div class="variacao-item-detalhes">
										<div class="col-item col-quarto">
											<div class="col-inner">
												<div class="quarto-cover cover-iconned">
													<i class="p-icon fad fa-bed"></i>
													<span class="value"><?php echo $brc_excursao_variations_nome; ?></span>
													<span class="label">Pacote</span>
												</div>
												
												<?php if($esgotado): ?>
												<span class="quarto-esgotado">ESGOTADO</span><?php endif; ?>
											</div>
										</div>
										<div class="col-item col-hotel">
											<div class="col-inner">
												<div class="cover-iconned">
													<i class="p-icon  fad fa-hotel"></i>
													<a href="<?php echo get_permalink($brc_excursao_variations_hotel); ?>" class="value" target="_blank" 
														title="<?php echo get_the_title($brc_excursao_variations_hotel); ?>">
														<?php echo get_the_title($brc_excursao_variations_hotel); ?>
													</a>
													<span class="label">Hotel</span>
												</div>
											</div>
										</div>
										<div class="col-item col-pessoas">
											<div class="col-inner">
												<div class="cover-iconned">
													<i class="p-icon fad fa-user-friends"></i>
													<span class="value">
													<?php 
														echo $brc_excursao_variations_pquarto; 
														echo $brc_excursao_variations_pquarto>1?' Pessoas':' Pessoa'; 
													?>
													</span>
													<span class="label">Máximo de pessoas</span>
													
												</div>
											</div>
										</div>
										
										<?php if($qj['variation_description']): ?>
										<div class="col-item col-descricao">
											<div class="col-inner">
												<em class="descricao"><i class="fad fa-info-circle"></i> <?php echo wp_strip_all_tags($qj['variation_description']); ?></em>
											</div>
										</div>
										<?php endif; ?>
									</div>
									
									<div class="col-item col-preco">
										<div class="col-inner">
											<span class="value"><small>R$</small><?php echo moedaRealPrint($pp_price); ?></span>
											<span class="label">Valor a vista</span>
											<span class="label">por pessoa</span>
										</div>
									</div>
									<div class="col-item col-btn">
										<div class="col-inner">
											<a href="#" title="SELECIONAR - <?php echo $brc_excursao_variations_nome; ?>" 
												class="belm-btn btn-fullw btn-group <?php echo $esgotado?'esgotado':'reservar-item'; ?> item-btn" 
												data-varid="<?php echo $qj['variation_id']; ?>"
											>	
												SELECIONAR
												<i class="fal fa-info-circle"></i>
											</a>
										</div>
									</div>
								</div>
								<div class="dados-wrapper"></div>
							</div>
						</div>
					<?php
					}
				?>
				</div>
				
				
				<?php $excursao_data = get_post_meta($post_id, 'brc_excursao_data', true); ?>
				<?php $excursao_data_volta = get_post_meta($post_id, 'brc_excursao_data_volta', true); ?>
				<?php $brc_excursao_tempo_viagem = get_post_meta($post_id, 'brc_excursao_tempo_viagem', true); ?>
				<?php $brc_excursao_tempo_viagem_horas = get_min_to_hora($brc_excursao_tempo_viagem); ?>
				<div class="variacoes-resumo">
					<div class="variacoes-resumo-inner">
						<div class="col-item col-data">
							<div class="col-inner">
								<div class="cover-iconned">
									<i class="p-icon fad fa-calendar"></i>
									<span class="value"><?php echo date('d/m/Y H:s', $excursao_data); ?></span>
									<span class="label">Data/Hora da ida</span>
								</div>
							</div>
						</div>
						<div class="col-item col-data">
							<div class="col-inner">
								<div class="cover-iconned">
									<i class="p-icon fad fa-calendar"></i>
									<span class="value"><?php echo date('d/m/Y H:s', $excursao_data_volta); ?></span>
									<span class="label">Data/Hora da volta</span>
								</div>
							</div>
						</div>
						<div class="col-item col-hora">
							<div class="col-inner">
								<div class="cover-iconned">
									<i class="p-icon fad fa-clock"></i>
									<span class="value"><?php echo $brc_excursao_tempo_viagem_horas; ?></span>
									<span class="label">Tempo de viagem</span>
								</div>
							</div>
						</div>
						<div class="col-item col-valor">
							<div class="col-inner">
								<span class="value">
									<small>R$ </small>
									<span id="excursao-valor-total">--</span>
								</span>
								<span class="label">Valor total</span>
							</div>
						</div>
						<div class="col-item col-quartos">
							<div class="col-inner">
								<span class="value">
									<span id="excursao-quartos-total">--</span>
								</span>
								<span class="label">Quantidades de pessoas</span>
							</div>
						</div>
					</div>
				</div>
				
				<div class="continuar-wrapper text-center">
					<a href="#" title="Comprar - <?php the_title(); ?>" id="comprar-avancar" class="belm-btn btn-lg btn-color-two">
						CONTINUAR COM A RESERVA
					</a>
				</div>
			</div>
		</div>
	</form>
	<?php endif; ?>
	<!-- VARIACAO -->
	
	
	<?php
		$excursao_ingresso = get_post_meta($post_id, 'brc_excursao_ingresso', true);
		if($excursao_ingresso){
			$brc_excursao_ingresso_stock = get_post_meta($post_id, 'brc_excursao_ingresso_stock', true);
			$brc_excursao_ingresso_preco = get_post_meta($post_id, 'brc_excursao_ingresso_preco', true);
			if($brc_excursao_ingresso_stock < 1){
				$excursao_ingresso = false;
			}
		}
	?>
	<?php if($excursao_ingresso): ?>
	<input type="hidden" id="brc_excursao_ingresso_preco" name="brc_excursao_ingresso_preco" value="<?php echo $brc_excursao_ingresso_preco; ?>" />
	<?php endif; ?>
	<input type="hidden" name="excursao-valor-total-field" id="excursao-valor-total-field" value="0" autocomplete="off" />
</div>

<?php
	$excursao_data = get_post_meta($post_id, 'brc_excursao_data', true);
	$excursao_data_volta = get_post_meta($post_id, 'brc_excursao_data_volta', true);
	$brc_excursao_data_dia = get_post_meta($post_id, 'brc_excursao_data_dia', true);
	$brc_excursao_data_hora = get_post_meta($post_id, 'brc_excursao_data_hora', true);
	$brc_excursao_tempo_viagem = get_post_meta($post_id, 'brc_excursao_tempo_viagem', true);
	$brc_excursao_tempo_viagem_horas = get_min_to_hora($brc_excursao_tempo_viagem);
	$brc_prod_hospedagem_noites = get_post_meta($post_id, 'brc_prod_hospedagem_noites', true);
?>
<div id="ex-infos-scroll" class="animado">
	<div class="l-section-h">
		<div class="inner">
			<ul>
				<li class="lin-data">
					<span class="value"><?php echo date('d/m/Y - H:i', $excursao_data); ?></span>
					<span class="label"><i class="fal fa-calendar"></i> Data/Hora da ida</span>
				</li>
				<li class="lin-data">
					<span class="value"><?php echo date('d/m/Y - H:i', $excursao_data_volta); ?></span>
					<span class="label"><i class="fal fa-calendar"></i> Data/Hora do Retorno</span>
				</li>
				<li class="lin-tempo">
					<span class="value"><?php echo $brc_excursao_tempo_viagem_horas; ?></span>
					<span class="label"><i class="fal fa-bus"></i> Tempo de viagem</span>
				</li>
				<li class="lin-quartos">
					<span class="value"><?php echo $brc_prod_hospedagem_noites; ?></span>
					<span class="label"><i class="fal fa-bed-alt"></i> Quantidade de noites</span>
				</li>
				<li class="lin-selecionar">
					<a href="#" data-target="ex-product-config" class="belm-btn btn-fullw exl" id="excursao-action-anchor">
						<span class="t-desk">Selecionar</span>
						<span class="t-mob">Mais Informações</span>
					</a>
				</li>
			</ul>
		</div>
	</div>
</div>