	
	<?php $vd = $var_id; ?>
	<?php $ld = $lins_id; ?>
	<?php $ia = '['.$vd.']['.$ld.'][]'; ?>
	<div class="pacote-dados-wrapper"> 
		<input type="hidden" name="variacao-item[<?php echo $ld; ?>]" value="<?php echo $vd; ?>" />
		<input type="hidden" name="variacao-price[<?php echo $ld; ?>]" class="i-price" value="<?php echo $variation_data['price']; ?>" />
		
		<?php 
			$pquarto = get_post_meta($variation_data['id'], 'brc_excursao_variations_pquarto', true);
			$pp_price = floatval($variation_data['price']) / intval($pquarto);
			
		?>
		
		<div class="pacote-dados-inner-wrapper">
			<?php if($_pquarto > 0): ?>
			<?php for($i=1;$i<=$_pquarto;$i++): ?>
			<div class="lin-dados <?php echo $i<$_pquarto?'after':''; ?>">
				<input type="hidden" name="variacao-item-hospede<?php echo $ia; ?>" value="<?php echo $vd; ?>" />
				
				<div class="col-item col-hospede-nome">
					<div class="col-inner form-group">
						<span class="label">Hóspede</span> 
						<div class="inp"><input type="text" name="passageiro-nome<?php echo $ia; ?>" class="input-field" /></div>
					</div>
				</div>
				<div class="col-item col-hospede-cpf">
					<div class="col-inner form-group">
						<span class="label">CPF</span>
						<div class="inp"><input type="text" name="passageiro-cpf<?php echo $ia; ?>" class="input-field i-cpf" /></div>
					</div>
				</div>
				<div class="col-item col-hospede-valor">
					<div class="col-inner">
						<div class="cover-iconned">
							<span class="label">
								<span class="price_html">+ <?php echo wc_price($pp_price); ?></span>
							</span>
						</div>
						
						<?php if($brc_excursao_ingresso): ?>
						<div class="cover-iconned">
							<i class="p-icon fad fa-ticket-alt"></i>
							<label class="value toggle-checkbox">
								<input type="checkbox" value="<?php echo $brc_excursao_ingresso_preco; ?>" name="add-ingresso<?php echo $ia; ?>" class="action-ingresso" />
								<input type="hidden" value="no" name="in-ver<?php echo $ia; ?>" class="in-ver" />
								Incluir ingresso
							</label>
							<span class="label">
								<span class="price_html">+ <?php echo wc_price($brc_excursao_ingresso_preco); ?></span>
							</span>
						</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
			<?php endfor;?>
			
			<div class="pacote-criancas-wrapper"><!-- ajax --></div> 
			
			<div class="h-action-wrapper">
				<div>
					<a 
						href="#" 
						title="ADICIONAR HÓSPEDE" 
						class="vp_vc_btn-link h-action action-add-hospede" 
						data-varid="<?php echo $vd; ?>">
							<i class="fa fa-plus"></i>
							adicionar hóspede
					</a>
				</div>
				<?php if($brc_excursao_has_child_rule): ?>
				<?php $child_rule = get_post_meta($post_id, 'brc_excursao_child_rule', true); ?>
				<div>
					<a 
					href="#" 
					title="REMOVE HÓSPEDE" 
					class="vp_vc_btn-link h-action action-add-crianca" 
					data-linid="<?php echo $ld; ?>"
					data-varid="<?php echo $vd; ?>">
						<i class="fa fa-plus"></i>
						adicionar criança
					</a>
				</div>
				<?php endif; ?>
				<div>
					<a 
					href="#" 
					title="REMOVE HÓSPEDE" 
					class="vp_vc_btn-link h-action action-remove-hospede" 
					data-varid="<?php echo $vd; ?>">
						<i class="fa fa-times"></i>
						remover
					</a>
				</div>
			</div>
			<?php endif; ?>
		</div>
	</div>