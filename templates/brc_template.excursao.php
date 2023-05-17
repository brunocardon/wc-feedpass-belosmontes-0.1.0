<?php
	$post_id = get_the_ID();
	$product = wc_get_product($post_id);
	?>
<div class="eltdf-container" id="brc-single-excursao">
	<form action="" id="brc-single-excursao-form">
		<input type="hidden" name="action" value="brc_comprar_excursao" />
		
		<!-- BANNER -->
		<?php
			$imagem_capa = get_post_meta($post_id, 'brc_prod_imagem_capa_id', true);
			if($imagem_capa){
				$imagem_capa = wp_get_attachment_image_src($imagem_capa, 'full');
				$vp_imagem_capa_url = $imagem_capa[0];
			}
		?>
		<div id="excursao-banner">
			<?php if($imagem_capa): ?>
			<div class="bg" style="background-image:url(<?php echo $vp_imagem_capa_url; ?>);"></div><?php endif; ?>
			
			<div class="banner-wrapper">
				<div class="titles">
					<h3>Embarque Agora!</h3>
					<h1><?php the_title(); ?></h1>
				</div>
			</div>
		</div><!-- BANNER -->
		
		
		<!-- CONTENT -->
		<div id="excursao-content">
			<div class="eltdf-container-inner">
				<div class="vc_row inner-row">
					<div class="vc_col-sm-9 col-content">
						<div class="content-inner">
							<?php the_content(); ?>
						</div>
					</div>
					
					<div class="vc_col-sm-3 col-dados">
						<div class="dados-inner">
						<?php
							$excursao_data 				= get_post_meta($post_id, 'brc_excursao_data', true);
							$excursao_tempo_viagem 		= get_post_meta($post_id, 'brc_excursao_tempo_viagem', true);
							$prod_hospedagem_noites 	= get_post_meta($post_id, 'brc_prod_hospedagem_noites', true);
							
							$excursao_ingresso 			= get_post_meta($post_id, 'brc_excursao_ingresso', true);
							if($excursao_ingresso){
								$brc_excursao_ingresso_stock = get_post_meta($post_id, 'brc_excursao_ingresso_stock', true);
								$brc_excursao_ingresso_preco = get_post_meta($post_id, 'brc_excursao_ingresso_preco', true);
								if($brc_excursao_ingresso_stock < 1){
									$excursao_ingresso = false;
								}
							}
							
						?>
							<ul class="text-center">
								<li>
									<i class="fa fa-calendar"></i>
									<span class="value"><?php echo date('d/m/Y', $excursao_data); ?></span>
									<span class="label">Data de saída</span>
								</li>
								<li>
									<i class="fa fa-clock-o"></i>
									<span class="value"><?php echo date('H:i', $excursao_data); ?></span>
									<span class="label">Horário de saída</span>
								</li>
								<li>
									<i class="fa fa-bus"></i>
									<span class="value"><?php echo get_min_to_hora($excursao_tempo_viagem); ?></span>
									<span class="label">Tempo de viagem</span>
								</li>
								<li>
									<i class="fa fa-moon-o"></i>
									<span class="value"><?php echo $prod_hospedagem_noites; ?></span>
									<span class="label">Quantidade de noites</span>
								</li>
								
								<?php if($excursao_ingresso): ?>
									<input type="hidden" id="brc_excursao_ingresso_preco" name="brc_excursao_ingresso_preco" value="<?php echo $brc_excursao_ingresso_preco; ?>" />
								<li>
									<i class="fa fa-ticket"></i>
									<span class="label">Venda de ingresso</span>
								</li>
								<?php endif; ?>
							</ul>
							
							<div class="btn-wrapper text-center">
								<a href="#" title="Comprar - <?php the_title(); ?>" id="comprar-scroll" class="vp_vc_btn">COMPRAR</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div><!-- CONTENT -->
		
		
		<!-- VARIACAO -->
		<?php
			$quartos = $product->get_available_variations();
			if($quartos):
		?>
		<div id="excursao-variacoes">
			<div class="eltdf-container-inner">
				<h2 class="section-title"><i class="fa fa-bed"></i> Escolher Quartos</h2>
				
				<ul class="brc-notices"></ul>
				
				<ul class="variacoes-wrapper">
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
					?>asdasdasdasd
						<li class="variacao-item <?php echo $esgotado?'esgotado':''; ?>">
							<input type="hidden" value="<?php echo $qj['display_price']; ?>" id="variation_id-<?php echo $qj['variation_id']; ?>" name="variation_id[]" />
							
							<div class="variacao-item-inner">
								<div class="vc_row lin-info">
									<div class="vc_col-sm-4 col-item col-quarto mob-text-center">
										<span class="quarto-cover">
											<span class="value">
												<?php echo $brc_excursao_variations_nome; ?>
											</span>
											<span class="label">Quarto/Tipo</span>
										</span>
										
										<?php if($esgotado): ?>
										<span class="quarto-esgotado">ESGOTADO</span>
										<?php endif; ?>
									</div>
									<div class="vc_col-sm-4 col-item col-hotel text-center">
										<span class="value">
											<a href="<?php echo get_permalink($brc_excursao_variations_hotel); ?>" target="_blank" title="<?php echo get_the_title($brc_excursao_variations_hotel); ?>">
												<i class="fa fa-external-link"></i>
												<?php echo get_the_title($brc_excursao_variations_hotel); ?>
											</a>
										</span>
										<span class="label">Hotel</span>
									</div>
									<div class="vc_col-sm-2 col-item col-preco text-center">
										<span class="value">
											<?php echo $qj['price_html']; ?>
										</span>
										<span class="label">Valor a vista</span>
									</div>
									<div class="vc_col-sm-2 col-item col-btn text-center">
										<a 
											href="#" 
											title="SELECIONAR - <?php echo $brc_excursao_variations_nome; ?>" 
											class="vp_vc_btn vp_vc_btn-xs <?php echo $esgotado?'esgotado':'reservar-item'; ?> item-btn" 
											data-varid="<?php echo $qj['variation_id']; ?>"
										>
											SELECIONAR
										</a>
									</div>
								</div>
								<div class="dados-wrapper"></div>
							</div>
						</li>
					<?php
					}
				?>
				</ul>
				<div class="variacoes-resumo">
					<div class="variacoes-resumo-inner">
						<div class="col-item text-center">
							<i class="fa fa-calendar"></i>
							<span class="value"><?php echo date('d/m/Y', $excursao_data); ?></span>
							<span class="label">Data de saída</span>
						</div>
						<div class="col-item text-center">
							<i class="fa fa-clock-o"></i>
							<span class="value"><?php echo date('H:i', $excursao_data); ?></span>
							<span class="label">Horário de saída</span>
						</div>
						<div class="col-item text-right mob-text-center">
							<div class="btn-wrapper">
								<a href="#" title="Comprar - <?php the_title(); ?>" id="comprar-avancar" class="vp_vc_btn vp_vc_btn-sm">
									CONTINUAR COM A RESERVA
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php endif; ?>
		<!-- VARIACAO -->
		
		
		<!-- RESUMO -->
		<?php
			$hospedagem_noites = get_post_meta($post_id, 'brc_prod_hospedagem_noites', true);
			$brc_prod_hospedagem_dormitorio = get_post_meta($post_id, 'brc_prod_hospedagem_dormitorio', true);
			
			$hospedagem_servicos = get_post_meta($post_id, 'brc_prod_hospedagem_servicos', true);
			if($hospedagem_servicos){
				foreach($hospedagem_servicos as $k => $j){
					$h_servicos[$j] = get_servicos($j);
				}
			}
		?>
		<div id="excursao-resumo">
			<div class="eltdf-container-inner">
				<h2 class="section-title"><i class="fa fa-suitcase"></i> Resumo do Pacote</h2>
				
				<div class="vc_row lin-itens">
					<!-- hotel -->
					<div class="vc_col-sm-4 col col-hotel">
						<div class="col-inner">
							<h3>Hotéis</h3>
							<ul>
							<?php
								if($hoteis){
									foreach($hoteis as $k => $j){
									?>
										<li>
											<a href="<?php echo get_permalink($k); ?>" target="_blank">
												<i class="fa fa-building-o"></i><?php echo $j; ?> <small>(saiba mais)</small>
											</a>
										</li>
									<?php
									}
								}
							?>
							
								<?php if($brc_prod_hospedagem_dormitorio): ?>
								<li><i class="fa fa-bed"></i><?php echo $brc_prod_hospedagem_dormitorio; ?></li>
								<?php endif; ?>
								
								<?php if($hospedagem_noites): ?>
								<li><i class="fa fa-moon-o"></i><?php echo $hospedagem_noites; ?> Noites</li>
								<?php endif; ?>
								
								<?php if($h_servicos['cafe']): ?>
								<li><i class="fa fa-coffee"></i><?php echo $h_servicos['cafe']; ?></li>
								<?php endif; ?>
							</ul>
						</div>
					</div><!-- hotel -->
					
					<!-- servicos -->
					<div class="vc_col-sm-4 col col-servicos">
						<div class="col-inner">
							<h3>Serviços Inclusos</h3>
							<?php if($h_servicos): ?>
							<ul>
							<?php
								foreach($h_servicos as $k => $j){
									if($k != 'cafe'){
									?>
										<li><i class="fa fa-cube"></i><?php echo $j; ?></li>
									<?php
									}
								}									
							?>
							</ul>
							<?php endif; ?>
						</div>
					</div><!-- servicos -->
					
					<!-- hotel -->
					<div class="vc_col-sm-4 col col-comprar">
						<div class="col-inner">
							<ul class="text-center">
								<li>
									<i class="fa fa-calendar"></i>
									<span class="value"><?php echo date('d/m/Y', $excursao_data); ?></span>
									<span class="label">Data de saída</span>
								</li>
								<li>
									<i class="fa fa-clock-o"></i>
									<span class="value"><?php echo date('H:i', $excursao_data); ?></span>
									<span class="label">Horário de saída</span>
								</li>
							</ul>
							<div class="btn-wrapper text-center">
								<a href="#" title="Comprar - <?php the_title(); ?>" id="comprar-avancar" class="vp_vc_btn vp_vc_btn-sm">
									CONTINUAR COM A RESERVA
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- RESUMO -->
		
		
		<!-- INFORMAÇÕES DO PACOTE -->
		<?php
			$roteiro_group = get_post_meta($post_id, 'roteiro_group', true);
			if($roteiro_group):
		?>
		<div id="excursao-info">
			<div class="eltdf-container-inner">
				<h2 class="section-title"><i class="fa fa-info"></i> Informações do Pacote <small>(Roteiro)</small></h2>
				
				<div class="info-quad">
					<div class="info-header">
						<div class="col">Etapa</div>
						<div class="col">Roteiro</div>
						<div class="col">Descrição</div>
					</div>
					<?php foreach($roteiro_group as $k => $j): ?>
					<div class="info-body">
						<div class="col"><?php echo $j['etapa-nome']; ?></div>
						<div class="col"><?php echo $j['etapa-titulo']; ?></div>
						<div class="col"><?php echo nl2br($j['etapa-desc']); ?></div>
					</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
		<?php endif; ?>
		<!-- INFORMAÇÕES DO PACOTE -->
	</form>
</div>


<!-- FONT -->
<link 
	rel='stylesheet' 
	id='vc_google_fonts_satisfyregular-css'  
	href='https://fonts.googleapis.com/css?family=Satisfy%3Aregular&#038;subset=latin&#038;ver=6.0.3' type='text/css' media='all' />