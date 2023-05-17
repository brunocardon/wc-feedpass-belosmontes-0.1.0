<?php
	if($themes->verfEtapaBusca()){
		switch($themes->verfEtapaBusca()){
			case 'ida':
				if(isset($_GET['datainicio']) or $_GET['datainicio']){
					$datas_pernas = gen_daterangearray($_GET['datainicio']);
					$curr = $_GET['datainicio'];
				}
			break;
			case 'volta':
				if(isset($_GET['datafim']) or $_GET['datafim']){
					$datas_pernas = gen_daterangearray($_GET['datafim']);
					$curr = $_GET['datafim'];
				}
			break;
		}
	}else{
		if(isset($_GET['datainicio']) or $_GET['datainicio']){
			$curr = $_GET['datainicio'];
			$datas_pernas = gen_daterangearray($_GET['datainicio']);
		}
	}
?>
<div class="viagens-pernas">
	<ul class="datas animados">
	<?php
		if($datas_pernas){
			foreach($datas_pernas as $k => $j){
				$http_query = array(
					'cidade-origem' => $_GET['cidade-origem'],
					'cidade-destino' => $_GET['cidade-destino']
				);
				if($themes->verfEtapaBusca()){
					switch($themes->verfEtapaBusca()){
						case 'ida':
							$http_query['datainicio'] = date('d/m/Y', $j);
							$http_query['datafim'] = $_GET['datafim'];
						break;
						case 'volta':
							$http_query['datainicio'] = $_GET['datainicio'];
							$http_query['datafim'] = date('Y-m-d', $j);
						break;
					}
				}else{
					$http_query['datainicio'] = date('d/m/Y', $j);
					$http_query['datafim'] = $_GET['datafim'];
				}
				$fepa_busca = get_permalink(get_option('fepa_busca'));
				$_link = $fepa_busca.'?'.http_build_query($http_query);
			?>
				<li>
					<a 
						href="<?php echo $_link; ?>" 
						title="data de saída: <?php echo diasemanasmall(date('N', $j)); ?>, <?php echo date('d/m', $j); ?>"
						class="item <?php echo get_date_timestamp($curr)==$k?'ativo':''; ?>
					">
						<?php echo diasemanasmall(date('N', $j)); ?>., <?php echo date('d/m', $j); ?>
					</a>
				</li>
			<?php
			}
		}
	?>
	</ul>
</div>