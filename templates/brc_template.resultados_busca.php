<?php
    /*
        Template Name: Viagens
    */
	global $woocommerce;
	
	if(is_admin()) return false;
	
	$themes = new FEPAThemes();
	if(!$themes->buscaTemVolta()){
		$woocommerce->cart->empty_cart();
	}
	$linhs_by_pontos = get_linhs_by_pontos();
	$ufs = get_uf();
	
	
	$origem_str = false;
	$destino_str = false;
	$resultados_final = false;
	if(!$themes->verfEtapaVolta()){
		$woocommerce->cart->empty_cart();
		
		if(
			(isset($_GET['cidade-origem']) and $_GET['cidade-origem'] != '0') and
			(isset($_GET['cidade-destino']) and $_GET['cidade-destino'] != '0')
		){
			$linhas = array();
			$ponto_origem = get_term($_GET['cidade-origem']);
			if(!is_wp_error($ponto_origem)){
				$origem_str = get_pontos_full_name($ponto_origem->term_id);
				
				$ponto_destino = get_term($_GET['cidade-destino']);
				if(!is_wp_error($ponto_destino)){
					$destino_str = get_pontos_full_name($ponto_destino->term_id);
					
					if($linhs_by_pontos){
						foreach($linhs_by_pontos[$ponto_origem->term_id] as $linha_id => $details){
							$pontos_by_linha = get_pontos_by_linha($linha_id);
							
							if($pontos_by_linha[$ponto_origem->term_id]['index'] < $pontos_by_linha[$ponto_destino->term_id]['index'])
								$linhas[] = $linha_id;
						}
					}
				}
			}
			
			$viagens_args = $themes->defaultBuscaArgsLinhas($linhas);
			
			if(isset($_GET['datainicio']) and $_GET['datainicio']){
				$viagens_args['meta_query'][] = array(
					'key' 			=> 'brc_viagem_ida_data',
					'compare' 		=> '>=',
					'value' 		=> get_date_timestamp($_GET['datainicio']),
				);
			}else{
				$viagens_args['meta_query'][] = array(
					'key' 			=> 'brc_viagem_ida_data',
					'compare' 		=> '>=',
					'value' 		=> time(),
				);
			}
			
			if($themes->buscaTemVolta()){
				$viagens_args['meta_query'][] = array(
					'key' 			=> 'brc_viagem_tipo_cadastro',
					'compare' 		=> '=',
					'value' 		=> 3,
				);
			}
		}
	}
	if($themes->verfEtapaVolta()){
		if(
			(isset($_GET['cidade-origem']) and $_GET['cidade-origem'] != '0') and
			(isset($_GET['cidade-destino']) and $_GET['cidade-destino'] != '0')
		){
			$linhas = array();
			$ponto_origem = get_term($_GET['cidade-destino']);
			if(!is_wp_error($ponto_origem)){
				$origem_str = get_pontos_full_name($ponto_origem->term_id);
				
				$ponto_destino = get_term($_GET['cidade-origem']);
				if(!is_wp_error($ponto_destino)){
					$destino_str = get_pontos_full_name($ponto_destino->term_id);
					
					if($linhs_by_pontos){
						foreach($linhs_by_pontos[$ponto_origem->term_id] as $linha_id => $details){
							$pontos_by_linha = get_pontos_by_linha($linha_id);
							
							if($pontos_by_linha[$ponto_origem->term_id]['index'] < $pontos_by_linha[$ponto_destino->term_id]['index'])
								$linhas[] = $linha_id;
						}
					}
				}
			}
			
			$viagens_args = $themes->defaultBuscaArgsLinhas($linhas);
			$viagens_args['meta_query'][] = array(
				'key' 			=> 'brc_viagem_ida_data',
				'compare' 		=> '>=',
				'value' 		=> get_date_timestamp($_GET['datafim']),
			);
			$viagens_args['meta_query'][] = array(
				'key' 			=> 'brc_viagem_tipo_cadastro',
				'compare' 		=> '=',
				'value' 		=> 3,
			);
		}else{
			$woocommerce->cart->empty_cart();
		}
	}
	
	$viagens_search = new WP_Query($viagens_args);
	$resultados_final = $viagens_search->have_posts();
	?>
	<div id="viagens-resultados">
		<!-- BUSCA FORM -->
		<?php do_action('fepa_viagens_before_form'); ?>
		<div class="busca-wrapper">
			<?php include FEPA_PLUGIN_DIR . '/templates/brc_template.busca_viagens_form.php'; ?>
		</div>
		<?php do_action('fepa_viagens_after_form'); ?>
		<!-- BUSCA FORM -->
		
		
		<div class="resultados-wrapper">
			<div class="resultados-wrapper-inner">
			<?php
				if($resultados_final){
					$ccount = $viagens_search->found_posts;
					
					do_action('fepa_viagens_after_date_range');
					include include FEPA_PLUGIN_DIR . '/templates/brc_template.busca_viagens_datas.php';
					do_action('fepa_viagens_before_date_range');
				?>
					
					<!-- BUSCA TITLE -->
					<?php do_action('fepa_viagens_before_title'); ?>
					<div class="title-wrapper">
						<h2>
							<strong><?php echo $origem_str; ?></strong>
							<i class="fa fa-long-arrow-right"></i> 
							<strong><?php echo $destino_str; ?></strong>
						</h2>
					</div>
					<?php do_action('fepa_viagens_after_title'); ?>
					<!-- BUSCA TITLE -->
					
					
					<!-- BUSCA IDA E VOLTA IDENTIFIER -->
					<?php do_action('fepa_viagens_before_identifier'); ?>
					<?php
						if($themes->buscaTemVolta()){
							if($themes->verfEtapaBusca()){
								$etapa = $themes->verfEtapaBusca();
							?>
								<div class="identifier-wrapper">
									<div class="identifier-inner">
										<div class="left etapa ida <?php echo $etapa=='ida'?'current':''; ?>">
											<span>Ida</span>
										</div>
										<div class="right etapa volta <?php echo $etapa=='volta'?'current':''; ?>">
											<span>Volta</span>
										</div>
									</div>
								</div>
							<?php
							}
						}
					?>
					<?php do_action('fepa_viagens_after_identifier'); ?>
					<!-- BUSCA IDA E VOLTA INDENT -->
					
					
					<!-- BUSCA RESULT COUNT -->
					<?php do_action('fepa_viagens_before_count'); ?>
					<span class="count-results">
						Foram encontradas <strong><?php echo $ccount<10?'0'.$ccount:$ccount; ?></strong> viagens próximos a data informada.
					</span>
					<?php do_action('fepa_viagens_after_count'); ?>
					<!-- BUSCA RESULT COUNT -->
					
					
					<!-- BUSCA RESULT FEED -->
					<?php do_action('fepa_viagens_before_feed'); ?>
					<div class="viagem-itens-wrapper">
					<?php
						while($viagens_search->have_posts()){
							$viagens_search->the_post();
							
							$post_ID 				= get_the_ID();
							$tipo_cadastro 			= get_post_meta($post_ID, 'brc_viagem_tipo_cadastro', true);
							
							$linha_viagem = wp_get_post_terms($post_ID, 'brc_linha_viagem');
							if(!is_wp_error($linha_viagem)){
								$linha_viagem = $linha_viagem[0];
								$pontos_by_linha = get_pontos_by_linha($linha_viagem->term_id);
								
								$data_embarque = get_post_meta($post_ID, 'brc_viagem_ida_data', true);
								$data_embarque = get_date_timestamp(date('d/m/Y', $data_embarque)); // 00:00
								
								$ponto_origem_data = $pontos_by_linha[$ponto_origem->term_id];
								$data_embarque_ponto = strtotime(date('d-m-Y', $data_embarque).' '.$ponto_origem_data['brc_ponto_time']);
								$_price = $ponto_origem_data['brc_ponto_valor'];
								if($tipo_cadastro == 1){
									$_price = get_post_meta($post_ID, 'brc_viagem_preco', true);
								}
								
								$ponto_destino_data = $pontos_by_linha[$ponto_destino->term_id];
								$data_desembarque_ponto = strtotime(date('d-m-Y', $data_embarque).' '.$ponto_destino_data['brc_ponto_time']);
								
								$tempo_viagem = ($data_desembarque_ponto - $data_embarque_ponto)/60;
								$tempo_viagem_horas = get_min_to_hora($tempo_viagem);
								
								// ASSENTOS
								$_stock = intval(get_post_meta($post_ID, '_stock', true));
								$alerta_disp = $quantidade_assentos/3;
								$al = $_stock<$alerta_disp?true:false;
							
								$veiculo = wp_get_post_terms($post_ID, 'brc_veiculo');
								if(!is_wp_error($veiculo)){
									$veiculo = $veiculo[0];
									$prefix = get_cmb2_term_metaboxes_prefix('brc_veiculo');
									$veiculo_acomodacoes = get_term_meta($veiculo->term_id, $prefix. 'acomodacoes', true);
								}
								$date_strs = get_datepicker_attributes();
							}
						?>
							<div class="viagem-item viagem-item-<?php echo get_viagem_tipo_class($tipo_cadastro); ?>" id="viagem-<?php echo $post_ID; ?>">
								<div class="viagem-item-wrapper" id="viagens-feed-trigger-<?php echo $post_ID; ?>" >
									<div class="viagem-item-inner">
										<div class="item-dados ">
											<!-- DATA -->
											<div class="col col-date <?php echo get_viagem_tipo_class($tipo_cadastro); ?>">
												<div class="col-inner col-date-inner">
													<?php if($tipo_cadastro == 1): ?><span class="et">Ida</span><?php endif; ?>
													
													<div class="left">
														<?php echo date('H:i', $data_embarque_ponto); ?>
														<small>
															<?php echo $date_strs['dayNamesShort'][date('N', $data_embarque_ponto)-1]; ?>,
															<?php echo date('d', $data_embarque_ponto); ?> 
															<?php echo $date_strs['monthNamesShort'][date('n', $data_embarque_ponto)-1]; ?>
														</small>
													</div>
													<div class="right">
														<?php echo date('H:i', $data_desembarque_ponto); ?>
														<small>
															<?php echo $date_strs['dayNamesShort'][date('N', ($data_desembarque_ponto+($tempo_viagem*60)))-1]; ?>,
															<?php echo date('d', ($data_desembarque_ponto+($tempo_viagem*60))); ?> 
															<?php echo $date_strs['monthNamesShort'][date('n', ($data_desembarque_ponto+($tempo_viagem*60)))-1]; ?>
														</small>
													</div>
												</div>
											</div>
											<div class="col col-date <?php echo ($tipo_cadastro==1)?'':'col-sep'; ?>">
											<?php if($tipo_cadastro == 1): // bate e volta ?>
												<div class="col-inner col-date-inner">
													<span class="et">Volta</span>
													
													<div class="left">
														<?php echo date('H:i', $data_volta); ?>
														<small>
															<?php echo $date_strs['dayNamesShort'][date('N', $data_volta)-1]; ?>,
															<?php echo date('d', $data_volta); ?> 
															<?php echo $date_strs['monthNamesShort'][date('n', $data_volta)-1]; ?>
														</small>
													</div>
													<div class="right">
														<?php echo date('H:i', ($data_volta+($tempo_viagem*60))); ?>
														<small>
															<?php echo $date_strs['dayNamesShort'][date('N', ($data_volta+($tempo_viagem*60)))-1]; ?>,
															<?php echo date('d', ($data_volta+($tempo_viagem*60))); ?> 
															<?php echo $date_strs['monthNamesShort'][date('n', ($data_volta +($tempo_viagem*60)))-1]; ?>
														</small>
													</div>
												</div>
											<?php endif; ?>
											</div>
											<!-- .DATA -->
											
											<!-- ASSENTOS -->
											<div class="col col-assentos">
												<div class="col-inner col-assentos-inner">
													<span class="acomodacoes-tipo"><?php echo get_acomodacoes($veiculo_acomodacoes); ?></span>
													
													<?php if(count($pontos_by_linha)): ?>
													<span class="paradas"><?php echo count($pontos_by_linha); ?> Paradas</span><?php endif; ?>
													
													<span class="assentos-numero <?php echo $al?'al':''; ?>"><?php echo $_stock; ?> Assentos Disp.</span>
													
													<?php if(!is_wp_error($linha_viagem)): ?>
													<span class="v-linha">
														Linha: 
														<a href="#" title="<?php echo $linha_viagem->name; ?>">
															<i class="far fa-map"></i>
															<?php echo $linha_viagem->name; ?> 
														</a>
													</span>
													<?php endif; ?>
													
													
												</div>
											</div>
											<!-- .ASSENTOS -->
											
											<!-- VALOR -->
											<div class="col col-valor">
												<div class="col-inner col-valor-inner">
													<span><small>R$</small><?php echo moedaRealPrint($_price); ?></span>
												</div>
											</div>
											<!-- .VALOR -->
											
											<!-- BTN -->
											<div class="col col-btn">
												<div class="col-inner col-btn-inner">
													<span class="belm-btn belm-btn-lg">Ver detalhes</span>
												</div>
											</div>
											<!-- .BTN -->
										</div>
										<div class="item-bottom">	
											<!-- CIDADE -->
											<div class="col">
												<div class="col-inner">
													Embarque: <strong><?php echo $origem_str; ?></strong>
												</div>
											</div>
											<div class="col">
												<div class="col-inner">
													Desembarque: <strong><?php echo $destino_str; ?></strong>
												</div>
											</div>
											
											<div class="col">
												<div class="col-inner">
													Tempo de viagem : 
													<i class="fas fa-clock"></i>
													<strong><?php echo $tempo_viagem_horas; ?> <small>(aprox.)</small></strong>
												</div>
											</div>
											<!-- .CIDADE -->
										</div>
										
										<a href="#<?php the_ID(); ?>" class="viagem-item-selecionar"
											data-oponto="<?php echo $ponto_origem->term_id; ?>" 
											data-dponto="<?php echo $ponto_destino->term_id; ?>" 
											data-viagemid="<?php echo $post_ID; ?>" 
											data-selvolta="<?php echo $themes->buscaTemVolta()?'yes':'no'; ?>" 
										></a>
									</div>
									<div class="viagem-detalhes" id="viagem-detalhes-<?php echo $post_ID; ?>"></div>
								</div>
							</div>
						<?php
						}
					?>
					</div>
					<?php do_action('fepa_viagens_after_feed'); ?>
					<!-- BUSCA RESULT FEED -->
				<?php
				}else{
				?>
					<div class="no-viagens text-center">
						<i class="fa fa-frown-o"></i>
						<h2>Nenhuma viagem encontrada...</h2>
					</div>
				<?php
				}
			?>
			</div>
		</div>
		<?php do_action('blu_elated_before_container_close'); ?>
	</div>
	<?php 
?>