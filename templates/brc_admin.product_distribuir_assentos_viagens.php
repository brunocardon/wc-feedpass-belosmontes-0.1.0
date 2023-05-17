<?php
	$v_status = get_all_woocommerce_status_id();
	unset($v_status[4]);
	unset($v_status[5]);
	unset($v_status[6]);
	$orders = get_orders_ids_by_product_id($product_id, $v_status);
	
	if($orders):
		
		$veiculo = wp_get_post_terms($product_id, 'brc_veiculo');
		if(!is_wp_error($veiculo)){
			$veiculo = $veiculo[0];
			$prefix = get_cmb2_term_metaboxes_prefix('brc_veiculo');
			$veiculo_acomodacoes = get_term_meta($veiculo->term_id, $prefix. 'acomodacoes', true);
		}
		
	?>
		<div id="tb-distribuir-assentos" class="brc-sortable-area">
			<form action="#" method="post" id="tb-distribuir-assentos-form">
				<input type="hidden" name="action" value="brc_distribuir_assentos_save" />
				<input type="hidden" name="product_id" value="<?php echo $product_id; ?>" />
				<div class="container-wrapper">
					<div class="container-col container-col-left">
						<div class="container-col-inner">
							<h3 class="container-title">Passageiros</h3>
							<p class="container-desc">
								Selecione e arraste os <code><strong>Passageiros</strong></code> para os respectivos assentos, 
								conforme seja feita a distribuição.
							</p>
							<ul id="list-pessoas" class="sortable-container sortable-dados">
							<?php
								foreach($orders as $order_id){
									$order = new WC_Order($order_id);
									
									foreach($order->get_items() as $order_item_id => $order_item){
										if($order_item->get_product_id() == $product_id){
											
											$var_data = $order_item->get_data();
											$meta_data = $order_item->get_meta_data();
											$meta_data_array = order_item_meta_to_array($meta_data);
											
											if(!$meta_data_array['_assento']){
												$var_pessoa[$var_data['variation_id']][$order_item_id] = array(
													'order_id' => $order_id,
													'order_item_id' => $order_item_id,
													'_passageiro_nome' => $meta_data_array['_passageiro_nome'],
												);
											?>
												<li class="sortable-item" id="pessoa-<?php echo $order_item_id; ?>">
													<input type="hidden" name="pessoa_veiculo[<?php echo $order_item_id; ?>]" value="false" />
													<input type="hidden" name="pessoa_assento[<?php echo $order_item_id; ?>]" value="false" />
													<span class="handle"><?php echo $meta_data_array['_passageiro_nome']; ?></span>
												</li>
											<?php
											}else{
												$assento_pessoa[$meta_data_array['_veiculo']][$meta_data_array['_assento']] = array(
													'order_item_id' => $order_item_id,
													'_passageiro_nome' => $meta_data_array['_passageiro_nome'],
												);
											}
										}
									}
								}
							?>
							</ul>
							<div class="container-actions-wrapper">
								<button class="brc_admin_btn brc_admin_list_btn button" id="tb-distribuir-assentos-action-save" title="Salvar dados">
									<span class="fa fa-floppy-o"></span> Salvar dados
								</button>
							</div>
						</div>
					</div>
					<div class="container-col container-col-right <?php echo $veiculo_acomodacoes<2?'oleito':''; ?>">
						<h3 class="container-title">Lista de assentos</h3>
						<p class="container-desc">
							Esta é uma representação ilustrativa da organização do veículo.
						</p>
						<div class="container-col-inner">
						<?php
							$brc_viagem_veiculos_quant 		= get_post_meta($product_id, 'brc_viagem_veiculos_quant', true);
							$brc_viagem_assentos 			= get_post_meta($product_id, 'brc_viagem_assentos', true);
							$brc_viagem_assentos_veiculos 	= get_post_meta($product_id, 'brc_viagem_assentos_veiculos', true);
							$brc_assentos_disponiveis 		= get_post_meta($product_id, 'brc_assentos_disponiveis', true);
							
							if($brc_viagem_assentos_veiculos){
								$brc_viagem_veiculos_quant = $brc_viagem_veiculos_quant?$brc_viagem_veiculos_quant:1;
								
								for($v=1;$v<=$brc_viagem_veiculos_quant;$v++){
									if($veiculo_acomodacoes > 1){
										for($li=1;$li<=$brc_viagem_assentos_veiculos;$li++)
											$lugares_array[$li] = $li;
										
										$car_layout = array_chunk($lugares_array, 4);
									}else{
										foreach($brc_assentos_disponiveis as $kk => $jj)
											$lugares_array[$kk] = $kk;
										
										$car_layout = array_chunk($lugares_array, 3);
									}
								?>
									<div class="sortable-destino-wrapper" >
										<span class="destino-label">
											<i class="fa fa-bus"></i> 
											Veículo #<?php echo ($v<10)?'0'.$v:$v; ?>
											<?php echo $brc_excursao_variations_nome; ?> 
										</span>
										<div class="sortable-assento-wrapper">
										<?php	
											foreach($car_layout as $k_lin => $j_lin){
												
												if($veiculo_acomodacoes > 1){
													$janelas = array_chunk($j_lin, 2);
													$janelas[1] = array_reverse($janelas[1]);
													$r_lin = array_merge($janelas[0], $janelas[1]);
												}else{
													$r_lin = $j_lin;
												}
												
												foreach($r_lin as $k_assento => $j_assento){
													$va_fake = $j_assento;
												
												?>
													<div class="sortable-assento-item-wrapper"
														data-vid="<?php echo $v; ?>"
														data-assentoid="<?php echo $va_fake; ?>"
													>
														<span class="assento-num">P: <?php echo ($va_fake<10)?'0'.$va_fake:$va_fake; ?></span>
														<ul 
															id="destino-<?php echo $v; ?>-<?php echo $va_fake; ?>" 
															class="sortable-container sortable-destino sortable-assento" 
															data-passento="1" 
															data-vid="<?php echo $v; ?>"
															data-assentoid="<?php echo $va_fake; ?>"
														>
														<?php
															if($assento_pessoa[$v][$va_fake]){
																$j_ap = $assento_pessoa[$v][$va_fake];
															?>
																<li class="sortable-item" id="pessoa-<?php echo $j_ap['order_item_id']; ?>">
																	<input 
																		type="hidden" 
																		name="pessoa_veiculo[<?php echo $j_ap['order_item_id']; ?>]" 
																		value="<?php echo $v; ?>"
																	/>
																	<input 
																		type="hidden" 
																		name="pessoa_assento[<?php echo $j_ap['order_item_id']; ?>]" 
																		value="<?php echo $va_fake; ?>"
																	/>
																	<span class="handle"><?php echo $j_ap['_passageiro_nome']; ?></span>
																</li>
															<?php
															}
														?>
														</ul>
													</div>
												<?php
												}
											}
										?>
										</div>
									</div>
								<?php
								}
							}							
						?>
						</div>
					</div>
				</div>
			</form>
		</div>
		
		<script>
			$('ul.sortable-container').sortable({
				connectWith 	: "ul.sortable-container",
				handle 			: ".handle",
				placeholder 	: "phover",
				
				receive: function(event, ui) {
					var passento = $(this).data('passento')
					var vid = $(this).data('vid')
					var assentoid = $(this).data('assentoid')
					
					if ($(this).children().length > parseInt(passento)){
						$(ui.sender).sortable('cancel');
					}
					
					$(event.target).parent().find('input[name^="pessoa_veiculo"]').val(vid)
					$(event.target).parent().find('input[name^="pessoa_assento"]').val(assentoid)
				}
			});
		</script>
	<?php
	endif;
?>