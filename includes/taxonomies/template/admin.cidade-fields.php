<?php $ufs = get_uf(); ?>
<div class="form-field form-required term-brc_uf-wrap">
	<label for="tag-brc_uf">Estado (UF)</label>
	<select name="tag-brc_uf" id="tag-brc_uf" aria-required="true">
		<option value="0">--</option>
		<?php
			if($ufs){
				foreach($ufs as $k => $j){
					echo '<option value="'. $j['sigla'] .'">'. $j['nome'] . ' ('. strtoupper($j['sigla']) .')' .'</option>';
				}
			}
		?>
	</select>
	<p>Escolha o estado (UF) da cidade.</p>
</div>