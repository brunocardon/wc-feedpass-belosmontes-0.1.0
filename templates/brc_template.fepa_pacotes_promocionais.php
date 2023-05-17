<?php
	$excursoes = explode(',', $excursoes);
	$pacotes_args = array(
		'post_type' => 'product',
		'posts_per_page' => -1,
		'post__in' => $excursoes,
		'meta_query' => array(
			'relation' => 'AND',
			array(
				'key'     => 'brc_excursao',
				'value'   => 1,
				'compare' => '=',
			),
		),
	);
	if($show_all){
		unset($pacotes_args['post__in']);
	}
	$pacotes = new WP_Query($pacotes_args);
	
	if($pacotes->have_posts()){
	?>
		<div class="brc_vc_wrapper fepa_pacotes_promocionais <?php echo esc_attr($css_class); ?>">
			<div class="wrapper-inner">
			<?php
				while($pacotes->have_posts()){
					$pacotes->the_post();
					
					$product = wc_get_product(get_the_ID());
					$thumb = get_the_post_thumbnail_url(get_the_ID(), 'full');
					
					$_servicos = get_post_meta(get_the_ID(), 'brc_prod_hospedagem_servicos', true);
					$_ingresso = get_post_meta(get_the_ID(), 'brc_excursao_ingresso', true);
					$_subtitle = get_post_meta(get_the_ID(), 'brc_excursao_subtitle', true);
					$_ida = get_post_meta(get_the_ID(), 'brc_excursao_data', true);
					$_volta = get_post_meta(get_the_ID(), 'brc_excursao_data_volta', true);
					$quartos = $product->get_available_variations();
				?>
					<div class="col-item">
						<div class="col-inner">
							<div class="thumb" style="background-image:url(<?php echo $thumb; ?>);"></div>
							
							<div class="content">
								<h3>
									<span><?php the_title(); ?></span>
									<?php if($_subtitle): ?><small><?php echo $_subtitle; ?></small><?php endif; ?>
								</h3>
								<div class="mini-dados">
									<ul class="left feature">
										<li>Ida e volta</li>
										<?php if($_ingresso): ?><li>Ingressos e tickets a venda</li><?php endif; ?>
									</ul>
									<div class="right">
										<i class="fal fa-bus"></i>
										<span class="p-p p-ida"><span>Ida:</span> <?php echo date('d/m/Y H:s', $_ida); ?></span>
										<span class="p-p p-volta"><span>Volta:</span> <?php echo date('d/m/Y H:s', $_volta); ?></span>
									</div>
								</div>
								
								<?php if($quartos): ?>
								<ul class="quartos-precos">
								<?php
									foreach($quartos as $qk => $qj){
										$_nome = get_post_meta($qj['variation_id'], 'brc_excursao_variations_nome', true);
										$_price = get_post_meta($qj['variation_id'], '_price', true);
										$pquarto = get_post_meta($qj['variation_id'], 'brc_excursao_variations_pquarto', true);
									?>
										<li>
											<small>R$ </small> 
											<?php echo moedaRealPrint(floatval($_price) / intval($pquarto)); ?> 
											<span class="quarto">
												(<?php echo $_nome; ?>)
												<span class="pp">por pessoa</span>
											</span>
										</li>
									<?php
									}
								?>
								</ul>
								<?php endif; ?>
								
								<div class="infos">
									<i class="fad fa-info-circle"></i>
									Acesse para saber mais sobre pacotes e serviços inclusos.
								</div>
							</div>
							
							
							
							<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" class="f-link"></a>
						</div>
					</div>
				<?php
				}
			?>
			</div>
		</div>
	<?php
	}
?>