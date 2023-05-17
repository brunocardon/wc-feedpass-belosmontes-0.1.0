<?php 
	$ufs = get_uf();
	$terms = get_terms( array(
		'taxonomy' => 'brc_cidade',
		'hide_empty' => false,
	));
?>
<div class="form-field form-required term-brc_cidade-wrap">
	<label for="tag-brc_cidade">Cidade</label>
	<select name="tag-brc_cidade" id="tag-brc_cidade" aria-required="true">
		<option value="0">--</option>
		<?php
			if(!is_wp_error($terms)){
				foreach($terms as $k => $j){
					$brc_uf = get_term_meta($j->term_id, 'brc_uf', true);
					echo '<option value="'. $j->term_id .'">'. $j->name . ' / '. strtoupper($ufs[$brc_uf]['sigla']) .'</option>';
				}
			}
		?>
	</select>
	<p>Escolha a cidade do ponto de embarque.</p>
</div>