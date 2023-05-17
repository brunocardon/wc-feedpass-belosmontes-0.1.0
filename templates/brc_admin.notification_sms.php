<?php
	if(isset($_REQUEST['product_id'])){
		$tags = array();
		
		$product 	= get_post($_REQUEST['product_id']);
		$tag_titles = array(
			'ID' 					=> 'Viagem ID',
			'post_title' 			=> 'Viagem Nome',
			'data_embarque' 		=> 'Data do embarque',
			'data_desembarque' 		=> 'Data do desembarque',
			'tempo_viagem' 			=> 'Tempo de viagem',
			'embarque_cidade' 		=> 'Cidade de embarque',
			'desembarque_cidade' 	=> 'Cidade de desembarque',
			'_price' 				=> 'Valor da passagem',
			'veiculo_nome' 			=> 'Veículo',
			'motorista_nome' 		=> 'Motorista',
		);
		
		if($product){
			$post_ID 			= $product->ID;
			$tags['ID'] 		= $product->ID;
			$tags['post_title'] = $product->post_title;
			
			// DATAS
			$data_embarque 				= get_post_meta($post_ID, 'brc_viagem_ida_data', true);
			$tempo_viagem 				= get_post_meta($post_ID, 'brc_viagem_tempo', true);
			$brc_viagem_tipo_cadastro 	= get_post_meta($post_ID, 'brc_viagem_tipo_cadastro', true);
			
			if($brc_viagem_tipo_cadastro > 1){
				$data_desembarque = ($tempo_viagem * 60) + $data_embarque;
			}else{
				$data_desembarque = get_post_meta($post_ID, 'brc_viagem_volta_data', true);
			}
			
			$tags['data_embarque'] 		= date('H:i', $data_embarque) .' - '. date('d/m/Y', $data_embarque);
			$tags['data_desembarque'] 	= date('H:i', $data_desembarque) .' - '. date('d/m/Y', $data_desembarque);
			$tags['tempo_viagem'] 		= $tempo_viagem;
			
			// DESTINOS
			$embarque 					= get_post_meta($post_ID, 'brc_viagem_origem', true);
			$desembarque 				= get_post_meta($post_ID, 'brc_viagem_destino', true);
			$embarque_term 				= get_term($embarque, 'brc_destinos');
			$embarque_cidade 			= get_term($embarque_term->parent, 'brc_destinos');
			$desembarque_term 			= get_term($desembarque, 'brc_destinos');
			$desembarque_cidade 		= get_term($desembarque_term->parent, 'brc_destinos');
			$tags['embarque_cidade'] 	= $embarque_cidade->name;
			$tags['desembarque_cidade'] = $desembarque_cidade->name;
			
			
			// PRICE
			$_price 					= get_post_meta($post_ID, '_price', true);
			$tags['_price'] 			= 'R$ '.moedaRealPrint($_price);
			
			// VEÍCULO
			$veiculo = wp_get_post_terms($post_ID, 'brc_veiculo');
			if(!is_wp_error($veiculo)){
				$veiculo = $veiculo[0];
				$tags['veiculo_nome'] 	= $veiculo->name;
			}
			
			// VEÍCULO
			$motorista = wp_get_post_terms($post_ID, 'brc_motorista');
			if(!is_wp_error($motorista)){
				$motorista = $motorista[0];
				$tags['motorista_nome'] 	= $motorista->name;
			}
			
			//pre_debug($tags);
		}
	}
	?>
	<div class="brcpasstour_notification_sms">
		<form action="#" id="form-passageiros-sms">
			<input type="hidden" name="action" value="brcpasstour_notification_sms_send" />
			
			<div class="lin-mensagem">
				<h3>Insira sua mensagem</h3>
			<?php
				if($tags){
				?>
					<div class="tags">
						<span><strong>Inserir Dados na mensagem</strong></span>
					<?php
						foreach($tags as $k => $j){
							echo '<a href="#" data-tag="'. $j .'" class="tag-insert">'. $tag_titles[$k] .'</a>';
						}
					?>
					</div>
				<?php
				}
			?>
				<textarea name="mensagem" id="notification_sms_mensagem" placeholder="Mensagem para enviar via SMS..."></textarea>
				<p class="desc"><em>A mensagem deve conter no máximo 160 caracteres.</em> [<span class="mensagem_count">0</span>/160]</p>
				<div class="send-wrapper">
					<button type="submit" class="brc_admin_btn brc_admin_list_btn button" title="Enviar mensagem via SMS">
						<span class="fa fa-commenting-o"></span> Enviar mensagem via SMS
					</button>
				</div>
			</div>
			
			<div class="lin-cellphone">
				<div class="col col-left">
					<h3>Lista de clientes selecionados</h3>
					<p class="desc">Os clientes listados correspondem as pessoas que efetuaram a compra dos pedidos. Cada pedido pode ter mais de um passageiro.</p>
					<p class="desc"><strong>Caso deseje remover algum cliente da lista, basta clicar em seu nome pressionando o </strong><code>CTRL</code> <strong>no teclado.</strong></p>
				</div>
				<div class="col col-right">
					<select name="cellphone[]" id="notification_sms_cellphone" multiple="multiple">
					<?php
						foreach($passageiros as $k => $j){
							echo '<option value="'. $j['cellphone'] .'" selected="selected" title="'.$j['nome'] .' ['. $j['billing_cellphone'].']">'.
							$j['nome'] .' ['. $j['billing_cellphone'] .']</option>';
						}
					?>
					</select>
				</div>
			</div>
		</form>
	</div>