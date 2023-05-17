<?php 
	$ufs = get_uf();
	$terms = get_terms(array(
		'taxonomy' => 'brc_ponto_embarque',
		'hide_empty' => false,
	));
?>
<div class="form-field form-required term-brc_uf-wrap">
	<h3>
		Adicionar pontos 
		
		<a href="#" title="Adicionar ponto" id="linhas-pontos-add">
			<i class="fas fa-plus"></i>
		</a>
	</h3>
	
	<div id="linhas-pontos-wrapper" class="">
		<div class="lin lin-header">
			<div class="col col-drag"> </div>
			<div class="col col-ponto">Ponto de embarque</div>
			<div class="col col-horario">Horário</div>
			<div class="col col-valores">Valores (R$) *</div>
			<div class="col col-actions"> </div>
		</div>
		<ul id="lin-itens-wrapper">
			<li class="lin lin-itens">
				<div class="col col-drag"> <i class="fas fa-arrows-alt"></i> </div>
				<div class="col col-ponto">
					<select name="tag_brc_ponto[]" class="form-sel select2" aria-required="true">
						<option></option>
					<?php
						if(!is_wp_error($terms)){
							foreach($terms as $k => $j){
								$option_name = $j->name;
								$brc_cidade = get_term_meta($j->term_id, 'brc_cidade', true);
								if($brc_cidade){
									$cidade = get_term($brc_cidade);
									if(!is_wp_error($cidade)){
										$uf = get_term_meta($brc_cidade, 'brc_uf', true);
										$ufs = get_uf($uf);
										$option_name .= ' - '. $cidade->name.' / '.strtoupper($ufs['sigla']);
									}
								}
								echo '<option value="'. $j->term_id .'">'. $option_name .'</option>';
							}
						}
					?>
					</select>
				</div>
				<div class="col col-horario">
					<input type="time" name="tag_brc_ponto_time[]" class="form-input" value="" />
				</div>
				<div class="col col-valores">
					<input type="number" name="tag_brc_ponto_valor[]" step="0.01" class="form-input" value="" />
				</div>
				<div class="col col-actions">
					<a href="#" class="remove-ponto">Remover ponto</a>
				</div>
			</li>
		</ul>
	</div>
	<p>Para localizar um ponto procure pelo nome do mesmo ou cidade.</p>
	<p><em>* Os Valores corresponde ao preço da passagem do ponto referente até o final da linha.</em></p>
</div>