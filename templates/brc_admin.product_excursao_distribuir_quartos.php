<?php
	
	$product_id = $_GET['product_id'];
	$orders = get_orders_ids_by_product_id($product_id, get_all_woocommerce_status_id());
	
	if($orders){
		$product = wc_get_product($product_id);
		$quartos = $product->get_available_variations();
	?>
		<div id="tb-distribuir-quartos" class="brc-sortable-area">
			<form action="#" method="post" id="tb-distribuir-quartos-form">
				<input type="hidden" name="action" value="brc_distribuir_quartos_save" />
				<div class="container-wrapper">
					<div class="container-col container-col-left">
						<div class="container-col-inner">
							<h3 class="container-title">Hóspedes</h3>
							<p class="container-desc">
								Selecione e arraste os <code><strong>Hóspedes</strong></code> para os respectivos quartos, 
								conforme seja feita a distribuição.
							</p>
						<?php
							foreach($orders as $order_id){
								$order = new WC_Order($order_id);
								
								foreach($order->get_items() as $order_item_id => $order_item){
									$var_data = $order_item->get_data();
									$meta_data = $order_item->get_meta_data();
									$meta_data_array = order_item_meta_to_array($meta_data);
									
									if(!$meta_data_array['_quarto_numero']){
										$var_pessoa[$var_data['variation_id']][$order_item_id] = array(
											'order_id' => $order_id,
											'order_item_id' => $order_item_id,
											'hospedes' => $meta_data_array['_hospedes'],
										);
									}else{
										$var_quarto_pessoa[$var_data['variation_id']][$meta_data_array['_quarto_numero']][$order_item_id] = $meta_data_array['_hospedes'];
									}
								}
							}
							if($quartos){
								foreach($quartos as $k => $j){
									
									// QUARTO UM DADOS
									$var_id = $j['variation_id'];
									$brc_excursao_variations_hotel 	= get_post_meta($var_id, 'brc_excursao_variations_hotel', true);
									$brc_excursao_variations_nome 	= get_post_meta($var_id, 'brc_excursao_variations_nome', true);
								?>
									<h4 class="container-title">
										<?php echo $brc_excursao_variations_nome; ?> - 
										<?php echo get_the_title($brc_excursao_variations_hotel); ?>
									</h4>
									<ul 
										id="list-pessoas-<?php echo $var_id; ?>" 
										class="sortable-container sortable-dados sortable-container-var-<?php echo $var_id; ?>"
										data-var_id="<?php echo $var_id; ?>"
									>
									<?php
										if($var_pessoa[$var_id]){
											foreach($var_pessoa[$var_id] as $order_item_id => $order_item){
											?>
												<li class="sortable-item" id="pessoa-<?php echo $order_item_id; ?>">
													<input type="hidden" name="pessoa[<?php echo $order_item_id; ?>]" value="false" />
													<span class="handle">
													<?php
														if($order_item['hospedes']){
															$nomes = array();
															foreach($order_item['hospedes'] as $hospede){
																$nomes[] = $hospede['nome'];
															}
															echo implode('<br/>', $nomes);
														}
													?>
													</span>
												</li>
											<?php
											}
										}
									?>
									</ul>
								<?php
								}
							}
						?>
							<div class="container-actions-wrapper">
								<button class="brc_admin_btn brc_admin_list_btn button" id="tb-distribuir-quartos-action-save" title="Salvar dados">
									<span class="fa fa-floppy-o"></span> Salvar dados
								</button>
							</div>
						</div>
					</div>
					<div class="container-col container-col-right">
						<h3 class="container-title">Lista de quartos</h3>
						<p class="container-desc">
							Você pode adicionar um <code><strong>nome/número</strong></code> para cada quarto para melhor organização
						</p>
						
						<?php
							if($quartos){
								foreach($quartos as $k => $j){
								?>
									<div class="container-col-inner">
									<?php
										// QUARTO UM DADOS
										$var_id = $j['variation_id'];
										$brc_excursao_variations_hotel 				= get_post_meta($var_id, 'brc_excursao_variations_hotel', true);
										$brc_excursao_variations_nome 				= get_post_meta($var_id, 'brc_excursao_variations_nome', true);
										$brc_excursao_variations_pquarto 			= get_post_meta($var_id, 'brc_excursao_variations_pquarto', true);
										$brc_excursao_variations_stock_total 		= get_post_meta($var_id, 'brc_excursao_variations_stock_total', true);
										$brc_excursao_variations_quartos_numeros 	= get_post_meta($var_id, 'brc_excursao_variations_quartos_numeros', true);
										$attribute_quartos 							= get_post_meta($var_id, 'attribute_quartos', true);
										
										$stock_pquarto 	= $brc_excursao_variations_pquarto;
										$stock_total 	= $brc_excursao_variations_stock_total;
										$stock_atual 	= $qj['max_qty'];
										$quartos_total 	= ceil($stock_total / $stock_pquarto);
										
										for($qi=1;$qi<=$quartos_total;$qi++){
										?>
											<div class="sortable-destino-wrapper" >
												<span class="destino-label">
													<i class="fad fa-bed"></i> 
													<?php echo $brc_excursao_variations_nome; ?> 
													<input type="text" placeholder="A101" 
														name="quarto_nome[<?php echo $var_id; ?>][<?php echo $qi; ?>]" 
														value="<?php echo $brc_excursao_variations_quartos_numeros[$qi]; ?>" 
													/> 
													
													<i class="fad fa-building-o"></i> 
													<span class="bullet"><?php echo get_the_title($brc_excursao_variations_hotel); ?></span>
												</span>
												<ul 
													id="destino-<?php echo $var_id; ?>-<?php echo $qi; ?>" 
													class="sortable-container sortable-destino sortable-container-var-<?php echo $var_id; ?>" 
													data-quarto_id="<?php echo $qi; ?>" 
													data-pquarto="1" 
													data-var_id="<?php echo $var_id; ?>"
												>
												<?php
													//$var_quarto_pessoa[$var_data['variation_id']][$meta_data_array['_quarto_numero']][$order_item_id]
													if($var_quarto_pessoa[$var_id][$qi]){
														foreach($var_quarto_pessoa[$var_id][$qi] as $k_vqp => $j_vqp){
														?>
															<li 
																data-order_item_id="<?php echo $k_vqp; ?>" 
																class="sortable-item"
																id='pessoa-<?php echo $k_vqp; ?>'
															>
																<input type="hidden" name="pessoa[<?php echo $k_vqp; ?>]" value="<?php echo $qi; ?>" />
																<span class="handle">
																<?php
																	if($j_vqp){
																		$nomes = array();
																		foreach($j_vqp as $hospede){
																			$nomes[] = $hospede['nome'];
																		}
																		echo implode('<br/>', $nomes);
																	}
																?>
																</span>
															</li>
														<?php
														}
													}
												?>												
												</ul>
											</div>
										<?php
										}
									?>
									</div>
								<?php
								}
							}							
						?>
						
					</div>
				</div>
			</form>
		</div>
		
		<script>
			$(document).ready(function(){
				$('.sortable-container').each(function(a,b){
					$(b).sortable({
						connectWith 	: "ul.sortable-container-var-"+$(b).data('var_id'),
						handle 			: ".handle",
						placeholder 	: "phover",
						
						receive: function(event, ui) {
							var pquarto = $(this).data('pquarto')
							var quarto_id = $(this).data('quarto_id')
							
							if ($(this).children().length > parseInt(pquarto)){
								$(ui.sender).sortable('cancel');
							}
							
							$(ui.item).find('input').val(quarto_id)
							$(event.srcElement).parent().find('input').val(quarto_id)
						}
					});
				})
			})
		</script>
	<?php
	}
?>