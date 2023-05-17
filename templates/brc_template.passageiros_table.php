<?php
	
	/**
	 * Arquivo de template
	 * CLASS BRCPASSTOUR_THEME :: html_template_passageiros_tabela
	 * 
	 */
	$normal = $this->passageiros_table_version=='normal'?true:false;
	
	// TABELA 			: Passageiro | CPF | Embarque | Poltrona | Emissão | Cliente | Status | ID Pedido | Valor
	// TABELA SIMPLES 	: Passageiro | CPF | Embarque | Poltrona | Valor
	
	$this->html_passageiros_table_head();
?>
	<div class="brc-passsageiros-table-wrapper">
		<div class="brc-passsageiros-table-print">
			<div class="brc-passsageiros-table-a4">
				<?php $this->html_passageiros_table_header(); ?>
				
				<table class="brc-passsageiros-table <?php echo $normal?'normal-table':'simple-table'; ?>">
					<thead>
						<tr>
							<th class="cel hash">#</th>
							<th class="cel passageiro">Passageiro</th>
							<th class="cel telefone">Telefone</th>
							<th class="cel cpf">CPF</th>
							<th class="cel Embarque">Embarque</th>
							<th class="cel poltrona">Poltrona</th>
							<th class="cel valor">Valor</th>
						</tr>
					</thead>
					<tbody>
					<?php
						$total = 0;
						$c = 1;
						foreach($this->passageiros_linhas_array as $k => $j){
							$total += $j['valor_unit'];
						?>
							<tr class="<?php echo $normal?'normal-sup':''; ?> <?php echo $c%2?'filled':''; ?>">
								<td class="cel hash"><?php echo ($c)<10?'0'.($c):($c); ?></td>
								<td class="cel passageiro">
								<?php 
									echo $j['passageiro']; 
									echo '<br/><span class="bullet status-'.$j['status_id'].'">'. wc_get_order_status_name($j['status_id']) .'</span>';
								?>
								</td>
								<td class="cel telefone">
								<?php 
									echo implode('<br/>', $j['phones']);
									/*foreach($j['phones'] as $aaa => $bbb){
										echo $bbb.'<small>('. $aaa .')</small><br/>';
									}*/
								?>
								</td>
								<td class="cel cpf"><?php echo $j['cpf']; ?></td>
								<td class="cel Embarque"><?php echo $j['Embarque']; ?></td>
								<td class="cel poltrona"><?php echo set_zero_in_front($j['poltrona']); ?></td>
								<td class="cel valor"><?php echo $j['valor']; ?></td>
							</tr>
							<?php if($normal){ ?>
								<tr class="normal">
									<td class="cel hash"></td>
									<td class="cel cliente" colspan="2">
										<strong>Cliente:</strong> <?php echo $j['cliente']; ?>
									</td>
									<td class="cel order_id" colspan="2">
										<strong>Pedido:</strong> #<?php echo $j['order_id']; ?> <?php echo $j['status']; ?>
									</td>
									<td class="cel emissao">
										<strong>Emissão:</strong> <?php echo $j['emissao']; ?>
									</td>
								</tr>
							<?php } ?>
						<?php
							$c++;
						}
					?>
					</tbody>
					<tfoot>
						<tr>
							<td class="cel" colspan="5"></td>
							<td class="cel label">Total</td>
							<td class="cel total"><?php echo wc_price($total); ?></td>
						</tr>
					</tfoot>
				</table>
				
			</div>
		</div>
	</div>
	<?php 
	$this->html_passageiros_table_footer();
?>
	