<?php
	$excurcoes = new WP_Query(array(
		'post_type' => 'product',
		'posts_per_page' => 3,
		'meta_query' => array(
			'relation' => 'AND',
			array(
				'key'     => 'brc_excursao',
				'value'   => 1,
				'compare' => '=',
			),
		),
	));
	
	
	if($excurcoes->have_posts()){
	?>
		<div class="brc_vc_wrapper fepa_excursoes <?php echo esc_attr($css_class); ?>">
			<div class="wrapper-inner">
			<?php
				while($excurcoes->have_posts()){
					$excurcoes->the_post();
					
					$product = wc_get_product(get_the_ID());
					$thumb = get_the_post_thumbnail_url(get_the_ID(), 'full');
					
					$_subtitle = get_post_meta(get_the_ID(), 'brc_excursao_subtitle', true);
					$_ida = get_post_meta(get_the_ID(), 'brc_excursao_data', true);
					$_volta = get_post_meta(get_the_ID(), 'brc_excursao_data_volta', true);
					
					$vars = array();
					$quartos = $product->get_available_variations();
					if($quartos){
						foreach($quartos as $qk => $qj){
							$_price = get_post_meta($qj['variation_id'], '_price', true);
							$pquarto = get_post_meta($qj['variation_id'], 'brc_excursao_variations_pquarto', true);
							$vars[$_price] = array('price' => $_price, 'pquarto' => $pquarto);
						}
						
						ksort($vars);
						$vars_k = array_key_first($vars);
						$a_partir = floatval($vars[$vars_k]['price']) / intval($vars[$vars_k]['pquarto']);
					}else{
						$prioe_per_pessoa = $product->get_price();
					}
					
					
				?>
					<div class="col-item">
						<div class="col-inner">
							<div class="thumb" style="background-image:url(<?php echo $thumb; ?>);"></div>
							<div class="content">
								<h3><?php the_title(); ?></h3>
								<h4><?php echo $_subtitle; ?></h4>
								
								<div class="details">
									<div class="left">
										<span class="price">
											<small>a partir de</small>
										<?php 
											$prioe_per_pessoa = $a_partir;
											echo wc_price($prioe_per_pessoa); 
										?>
											<small>por pessoa</small>
										</span>
									</div>
									<div class="right">
										<i class="fal fa-bus"></i>
										<span class="p-p p-ida"><span>Ida:</span> <?php echo date('d/m/Y H:s', $_ida); ?></span>
										<span class="p-p p-volta"><span>Volta:</span> <?php echo date('d/m/Y H:s', $_volta); ?></span>
									</div>
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