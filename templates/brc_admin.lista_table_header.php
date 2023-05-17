<?php
	$fepa_logo = get_option('fepa_logo');
?>
	<div class="brc-passsageiros-header">
		<div class="brc-passsageiros-header-logo-wrapper">
			<div class="logo">
				<img src="<?php echo $fepa_logo; ?>" alt="<?php echo get_bloginfo('name'); ?>" />
			</div>
			<div class="title">
				<h1><?php echo get_bloginfo('name'); ?></h1>
				<h2><?php echo get_bloginfo('description'); ?></h2>
				<p>Relação das Passagens Vendidas - <strong><?php echo $brc_excursao_nome; ?></strong></p>
			</div>
		</div>
		<div class="brc-passsageiros-fill-dados-wrapper">
			<table>
				<tbody>
					<tr>
						<td class="pacote"><strong>Viagem:</strong> <?php echo $brc_excursao_nome; ?></td>
						<td class="data"><strong>Data da viagem:</strong> <?php echo date('d/m/Y', $brc_excursao_data); ?></td>
					</tr>
					<tr>
						<td class="obs" colspan="3"><strong>Observações:</strong> </td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>