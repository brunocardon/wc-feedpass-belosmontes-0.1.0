<?php
	$themes = new FEPAThemes();
	$tipo_cadastro = get_post_meta($post_ID, 'brc_viagem_tipo_cadastro', true);
	
	//$oponto = $_POST['oponto'];
	//$dponto = $_POST['dponto'];
	
	$linha_viagem = wp_get_post_terms($post_ID, 'brc_linha_viagem');
	if(!is_wp_error($linha_viagem)){
		$linha_viagem = $linha_viagem[0];
		$pontos_by_linha = get_pontos_by_linha($linha_viagem->term_id);
		$ponto_origem_data = $pontos_by_linha[$oponto];
		$_price = $ponto_origem_data['brc_ponto_valor'];
		if($tipo_cadastro == 1){
			$_price = get_post_meta($post_ID, 'brc_viagem_preco', true);
		}
		
		// ASSENTOS
		$quantidade_assentos 	= get_post_meta($post_ID, 'brc_viagem_assentos_veiculos', true);
		$assentos_disponiveis 	= get_post_meta($post_ID, 'brc_assentos_disponiveis', true);
		$assentos_disponiveis_quant = count(array_keys($assentos_disponiveis, 1));
		$veiculo 				= wp_get_post_terms($post_ID, 'brc_veiculo');
		$alerta_disp 			= $quantidade_assentos/3;
		$alerta_disp 			= $assentos_disponiveis_quant<$alerta_disp?true:false;
		
		if(!is_wp_error($veiculo)){
			$veiculo = $veiculo[0];
			$prefix = get_cmb2_term_metaboxes_prefix('brc_veiculo');
			$veiculo_acomodacoes = get_term_meta($veiculo->term_id, $prefix. 'acomodacoes', true);
		}
	}
?>
	
	<div class="re-title inner-sep">
		<?php do_action('fepa_viagens_before_result_title'); ?>
		<span><?php echo $product->post_title; ?></span>
		<?php do_action('fepa_viagens_after_result_title'); ?>
	</div>
	<?php do_action('fepa_viagens_before_result_form'); ?>
	<form action="#" class="viagem-detalhes-form" id="viagem-detalhes-form-<?php echo $post_ID; ?>" data-viagemid="<?php echo $post_ID; ?>">
		<input type="hidden" name="viagem_id" value="<?php echo $post_ID; ?>" />
		<input type="hidden" name="action" value="brc_viagem_enviar" />
		<input type="hidden" name="selvolta" value="<?php echo $themes->buscaTemVolta()?'yes':'no'; ?>" />
		
		<?php do_action('fepa_viagens_before_result_linha'); ?>
		<?php if($ponto_origem_data): ?>
			<input type="hidden" name="ponto_embarque" value="<?php echo $oponto; ?>" />
			<input type="hidden" name="ponto_desembarque" value="<?php echo $dponto; ?>" />
			<input type="hidden" name="linha_viagem" value="<?php echo $linha_viagem->term_id; ?>" />
		<?php endif; ?>
		<?php do_action('fepa_viagens_after_result_linha'); ?>
		
		
		<?php do_action('fepa_viagens_before_result_assentos'); ?>
		<?php if($tipo_cadastro == 3): ?>
		<div class="viagem-detalhes-passageiros-veiculo inner-sep">
			<?php include FEPA_PLUGIN_DIR . '/templates/brc_template.busca_viagens_detalhes-assentos.php'; ?>
		</div>
		<?php endif; ?>
		<?php do_action('fepa_viagens_after_result_assentos'); ?>
		
		
		<?php do_action('fepa_viagens_before_result_dados'); ?>
		<div class="viagem-detalhes-passageiros-dados inner-sep" id="viagem-detalhes-passageiros-dados-<?php echo $post_ID; ?>">
			<div class="viagem-detalhes-passageiros-notices">
				<ul class="brc-notices"></ul>
			</div>
			<!-- ajax -->
			<?php if($tipo_cadastro != 3): ?>
			<?php include FEPA_PLUGIN_DIR . '/templates/brc_template.busca_viagens_detalhes-lin-dados.php'; ?>
			<?php endif; ?>
		</div>
		<?php do_action('fepa_viagens_after_result_dados'); ?>
		
		
		<?php do_action('fepa_viagens_before_result_total'); ?>
		<div class="viagem-detalhes-passageiros-total inner-sep" id="viagem-detalhes-passageiros-total-<?php echo $post_ID; ?>">
			<div class="lin-total">
				
				<div class="col col-subtotal">Subtotal</div>
				<div class="col col-subtotal-val">
					<span class="item-subtotal" price="0">
					<?php if($tipo_cadastro != 3): ?>
						<?php echo wc_price($_price); ?>
					<?php else: ?>
						<?php echo wc_price(0); ?>
					<?php endif; ?>
					</span>
				</div>
				<div class="col col-btn">
					<button type="submit" title="Continuar Reserva" class="belm-btn btn-fullw action-continuar-reserva">Prosseguir</a>
				</div>
			</div>
		</div>
		<?php do_action('fepa_viagens_after_result_total'); ?>
		
	</form>
	<?php do_action('fepa_viagens_after_result_form'); ?>