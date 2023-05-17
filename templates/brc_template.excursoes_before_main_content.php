<?php
	$post_id = get_the_ID();
	$brc_prod_imagem_capa = get_post_meta($post_id, 'brc_prod_imagem_capa', true);
	
	// METAS
	$excursao_data = get_post_meta($post_id, 'brc_excursao_data', true);
	$excursao_data_volta = get_post_meta($post_id, 'brc_excursao_data_volta', true);
	$brc_excursao_data_dia = get_post_meta($post_id, 'brc_excursao_data_dia', true);
	$brc_excursao_data_hora = get_post_meta($post_id, 'brc_excursao_data_hora', true);
	$brc_excursao_tempo_viagem = get_post_meta($post_id, 'brc_excursao_tempo_viagem', true);
	$brc_excursao_tempo_viagem_horas = get_min_to_hora($brc_excursao_tempo_viagem);
	$brc_prod_hospedagem_noites = get_post_meta($post_id, 'brc_prod_hospedagem_noites', true);
	 
	
?>
<div id="excursoes_before_main_content" style="background-image:url('<?php echo $brc_prod_imagem_capa; ?>')">
	<div class="l-section-h">
		<div class="title-wrapper">
			<div class="title-inner">
				<h2><?php the_title(); ?></h2>
			</div>
		</div>
		
		<div class="bandeja-dados">
			<div class="col">
				<div class="inner-col">
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
								Selecionar
							</a>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<div id="excursao-anchor-menu">
		<div class="inner-wrapper">
			<nav>
				<ul>
					<li class="li-exl"><a href="#" data-target="ex-sobre" class="exl" id="exl-sobre">TUDO SOBRE <?php the_title(); ?></a></li>
					<li class="li-exl"><a href="#" data-target="ex-fazer" class="exl" id="exl-fazer">O QUE FAZER?</a></li>
					<li class="li-exl"><a href="#" data-target="ex-hotel" class="exl" id="exl-hotel">HOTEL</a></li>
					<li class="li-exl"><a href="#" data-target="ex-product" class="exl" id="exl-product">INFORMAÇÕES IMPORTANTES</a></li>
				</ul>
			</nav>
		</div>
	</div>
</div>