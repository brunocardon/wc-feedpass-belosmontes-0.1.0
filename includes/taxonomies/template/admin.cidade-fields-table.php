<?php 
	$ufs = get_uf();
	$term_id = $_GET['tag_ID'];
	$brc_uf = get_term_meta($term_id, 'brc_uf', true);
?>

<table class="form-table" role="presentation">
	<tbody>
		<tr class="form-field form-required term-name-wrap">
			<th scope="row"><label for="tag-brc_uf">Estado (UF)</label></th>
			<td>
				<select name="tag-brc_uf" id="tag-brc_uf" aria-required="true">
					<option value="0">--</option>
					<?php
						if($ufs){
							foreach($ufs as $k => $j){
								echo '<option value="'. $j['sigla'] .'" '. ($brc_uf==$j['sigla']?'selected':'') .'>'. $j['nome'] . ' ('. strtoupper($j['sigla']) .')' .'</option>';
							}
						}
					?>
				</select>
				<p class="description">Escolha o estado (UF) da cidade.</p>
			</td>
		</tr>
	</tbody>
</table>