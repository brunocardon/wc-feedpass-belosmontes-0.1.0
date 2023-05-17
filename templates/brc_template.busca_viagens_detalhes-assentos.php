	<div class="col onibus">
		<div class="onibus-inner">
			<div class="lugares">
			<?php
				if($veiculo_acomodacoes > 1){
					for($li=1;$li<=$quantidade_assentos;$li++)
						$lugares_array[$li] = $li;
					
					$chunk = 4;
				}
				else{
					foreach($assentos_disponiveis as $kk => $jj)
						$lugares_array[$kk] = $kk;
					
					$chunk = 3;
				}
				
				$sel = $assentos_disponiveis_quant = array_keys($assentos_disponiveis, 1);
				
				$lugares_filas = array_chunk($lugares_array, $chunk, true);
				foreach($lugares_filas as $fila){
					$fila_col = array_chunk($fila, 2, false);
					$fila_col[1] = array_reverse($fila_col[1]);
					
					echo '<div class="lfilas">';
					foreach($fila_col as $fila_col_sec){
						foreach($fila_col_sec as $lugarnum){
						?>
							<div class="lugar-assento <?php echo in_array($lugarnum, $sel)?'disponivel':'ocupado'; ?>" id="poltrona-<?php echo $post_ID; ?>-<?php echo $lugarnum; ?>">
								<a href="#<?php echo $lugarnum; ?>" class="lugar-toggler" title="Assento <?php echo $lugarnum; ?>" 
									data-assento="<?php echo $lugarnum; ?>"
									data-status="<?php echo in_array($lugarnum, $sel)?'disponivel':'ocupado'; ?>"
									data-viagemid="<?php echo $post_ID; ?>"
									data-preco="<?php echo $_price; ?>"
								>
									<?php echo $lugarnum; ?>
								</a>
							</div>
						<?php
						}
					}
					echo '</div>';
				}
			?>
			</div>
		</div>
	</div>
	<div class="col legendas mob-text-center">
		<h2>Estamos quase lá...</h2>
		<h3>Selecione o(s)  assento(s) desejado(s) e preencha com os dados do(s) passageiro(s).</h3>
		
		<div class="legendas-descricao">
			<ul>
				<li><span class="leg-disponivel">Livre</span></li>
				<li><span class="leg-selecionado">Selecionado</span></li>
				<li><span class="leg-ocupado">Ocupado</span></li>
			</ul>
		</div>
	</div>