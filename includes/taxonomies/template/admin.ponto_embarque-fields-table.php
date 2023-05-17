<?php 
	$ufs = get_uf();
	$term_id = $_GET['tag_ID'];
	$terms = get_terms( array(
		'taxonomy' => 'brc_cidade',
		'hide_empty' => false,
	));
	$brc_cidade = get_term_meta($term_id, 'brc_cidade', true);
?>

<table class="form-table" role="presentation">
	<tbody>
		<tr class="form-field form-required term-name-wrap">
			<th scope="row"><label for="tag-brc_uf">Estado (UF)</label></th>
			<td>
				<select name="tag-brc_uf" id="tag-brc_uf" aria-required="true">
					<option value="0">--</option>
					<?php
						if(!is_wp_error($terms)){
							foreach($terms as $k => $j){
								$brc_uf = get_term_meta($j->term_id, 'brc_uf', true);
								echo '<option value="'. $j->term_id .'" '. ($brc_cidade==$j->term_id?'selected':'') .'>'. $j->name . ' / '. strtoupper($ufs[$brc_uf]['sigla']) .'</option>';
							}
						}
					?>
				</select>
				<p class="description">Escolha o estado (UF) da cidade.</p>
			</td>
		</tr>
	</tbody>
</table>