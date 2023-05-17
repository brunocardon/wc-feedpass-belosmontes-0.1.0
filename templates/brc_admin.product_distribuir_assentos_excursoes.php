<?php
	$product_id = $_GET['product_id'];
	
	$v_status = get_all_woocommerce_status_id();
	unset($v_status[4]);
	unset($v_status[5]);
	unset($v_status[6]);
	$orders = get_orders_ids_by_product_id($product_id, $v_status);
	
	if($orders):
		$product = wc_get_product($product_id);
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
										$var_data = $order_item->get_data();
										$meta_data = $order_item->get_meta_data();
										$meta_data_array = order_item_meta_to_array($meta_data);
										
										if($meta_data_array['_hospedes']){
											$var_pessoa[$var_data['variation_id']][$order_item_id] = array(
												'order_id' => $order_id,
												'order_item_id' => $order_item_id,
												'hospedes' => $meta_data_array['_hospedes'],
											);
											
											foreach($meta_data_array['_hospedes'] as $k => $hospede){
												if(!$hospede['assento']){
												?>
													<li class="sortable-item" id="pessoa-<?php echo $order_item_id; ?>">
														<input type="hidden" name="pv[<?php echo $order_item_id; ?>][<?php echo $k; ?>]" value="false" />
														<input type="hidden" name="pa[<?php echo $order_item_id; ?>][<?php echo $k; ?>]" value="false" />
														<span class="handle">
															<?php echo $hospede['nome']; ?>
														</span> 
													</li>
												<?php
												}else{
													$assento_pessoa[$hospede['veiculo']][$hospede['assento']] = array(
														'order_id' => $order_id,
														'order_item_id' => $order_item_id,
														'hospedes' => $hospede,
														'k' => $k,
													);
												}
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
					<div class="container-col container-col-right ">
						<h3 class="container-title">Lista de assentos</h3>
						<p class="container-desc">
							Esta é uma representação ilustrativa da organização do veículo.
						</p>
						<div class="container-col-inner">
						<?php
							$brc_excursao_quant_veiculos = get_post_meta($product_id, 'brc_excursao_quant_veiculos', true);
							$brc_excursao_quant_assentos = get_post_meta($product_id, 'brc_excursao_quant_assentos', true);
							
							if($brc_excursao_quant_assentos){
								$brc_excursao_quant_veiculos = $brc_excursao_quant_veiculos?$brc_excursao_quant_veiculos:1;
								
								for($v=1;$v<=$brc_excursao_quant_veiculos;$v++){
									for($li=1;$li<=$brc_excursao_quant_assentos;$li++)
										$lugares_array[$li] = $li;
									
									$car_layout = array_chunk($lugares_array, 4);
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
												
												$janelas = array_chunk($j_lin, 2);
												$janelas[1] = array_reverse($janelas[1]);
												$r_lin = array_merge($janelas[0], $janelas[1]);
												
												foreach($r_lin as $k_assento => $j_assento){
													$va_fake = $j_assento;
												
												?>
													<div class="sortable-assento-item-wrapper">
														<span class="assento-num">P: <?php echo ($va<10)?'0'.$va_fake:$va_fake; ?></span>
														<ul 
															id="destino-<?php echo $v; ?>" 
															class="sortable-container sortable-destino sortable-assento" 
															data-passento="1" 
															data-vid="<?php echo $v; ?>"
															data-assentoid="<?php echo $va_fake; ?>"
														>
														<?php
															if($assento_pessoa[$v][$va_fake]){
																$j_ap = $assento_pessoa[$v][$va_fake];
															?>
																<li class="sortable-item" id="pessoa-<?php echo $j_ap['order_item_id']; ?>-<?php echo $j_ap['k']; ?>">
																	<input 
																		type="hidden" 
																		name="pv[<?php echo $j_ap['order_item_id']; ?>][<?php echo $j_ap['k']; ?>]" 
																		value="<?php echo $v; ?>"
																	/>
																	<input 
																		type="hidden" 
																		name="pa[<?php echo $j_ap['order_item_id']; ?>][<?php echo $j_ap['k']; ?>]" 
																		value="<?php echo $va_fake; ?>"
																	/>
																	<span class="handle">
																		<?php echo $j_ap['hospedes']['nome']; ?>
																	</span>
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
					
					if($(this).children().length > parseInt(passento)){
						$(ui.sender).sortable('cancel');
					}
					
					console.log(vid, assentoid)
					
					$(ui.item).find('input[name^="pv"]').val(vid)
					$(ui.item).find('input[name^="pa"]').val(assentoid)
					$(event.srcElement).parent().find('input[name^="pv"]').val(vid)
					$(event.srcElement).parent().find('input[name^="pa"]').val(assentoid)
				}
			});
		</script>
	<?php
	endif;
?>