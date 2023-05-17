<?php
	class FEPAPagesShortcodes{
		protected static $instance = null;
		private $FINALIZAR_RESERVA_URI;

		function __construct(){
			$this->FINALIZAR_RESERVA_URI = get_permalink(get_option('fepa_finalizar'));
			
			add_shortcode(FEPA_PREFIX.'the_title', 						array($this, 'brc_item_title')); 
			
			add_shortcode(FEPA_PREFIX.'resultados_busca', 				array($this, 'brc_resultados_busca_callback')); 
			add_action('wp_ajax_nopriv_brc_viagem_detalhes', 			array($this, 'brc_viagem_detalhes'));
			add_action('wp_ajax_brc_viagem_detalhes', 					array($this, 'brc_viagem_detalhes'));
			add_action('wp_ajax_nopriv_brc_viagem_add_passageiro', 		array($this, 'brc_viagem_add_passageiro'));
			add_action('wp_ajax_brc_viagem_add_passageiro', 			array($this, 'brc_viagem_add_passageiro'));
			add_action('wp_ajax_nopriv_brc_viagem_enviar', 				array($this, 'brc_viagem_enviar'));
			add_action('wp_ajax_brc_viagem_enviar', 					array($this, 'brc_viagem_enviar'));
			
			add_action('wp_ajax_nopriv_brc_remove_cart_item', 			array($this, 'brc_remove_cart_item'));
			add_action('wp_ajax_brc_remove_cart_item', 					array($this, 'brc_remove_cart_item'));
			
			add_shortcode(FEPA_PREFIX.'product_excursoes_template', 	array($this, 'brc_product_excursoes_template_callback')); 
			add_action('wp_ajax_nopriv_brc_excursao_add_hospede', 		array($this, 'brc_excursao_add_hospede'));
			add_action('wp_ajax_brc_excursao_add_hospede', 				array($this, 'brc_excursao_add_hospede'));
			add_action('wp_ajax_nopriv_brc_excursao_add_child', 		array($this, 'brc_excursao_add_child'));
			add_action('wp_ajax_brc_excursao_add_child', 				array($this, 'brc_excursao_add_child'));
			add_action('wp_ajax_nopriv_brc_comprar_excursao', 			array($this, 'brc_comprar_excursao'));
			add_action('wp_ajax_brc_comprar_excursao', 					array($this, 'brc_comprar_excursao'));
			
			add_shortcode(FEPA_PREFIX.'lista_hospedes', 				array($this, 'brc_lista_hospedes_template_callback'));
			add_action('template_redirect', 							array($this, 'brc_lista_hospedes_template_redirect'), 1);
			
			add_shortcode(FEPA_PREFIX.'lista_passageiros', 				array($this, 'brc_lista_passageiros_template_callback'));
			
			add_shortcode(FEPA_PREFIX.'lista_de_comprovante_reserva', 	array($this, 'brc_lista_de_comprovante_reserva_template_callback'));
			add_action('template_redirect', 							array($this, 'brc_lista_de_comprovante_reserva_template_redirect'), 1);
			
			add_shortcode(FEPA_PREFIX.'ticket', 						array($this, 'brc_ticket_template_callback'));
			add_action('template_redirect', 							array($this, 'brc_ticket_template_redirect'), 1);
		}
		static function init() {
			if(null == self::$instance ){
				self::$instance = new self;
			}
			return self::$instance;
		}
		public function register() {
			// nothing to see here...
		}
		
		
		/*
		 * Add ´the_title´ to page
		 * [feedpass_the_title]
		 */
		public function brc_item_title(){
			return get_the_title();
		}
		
		/*
		 * Actions search page results
		 * [feedpass_resultados_busca]
		 */
		public function brc_resultados_busca_callback(){
			include FEPA_PLUGIN_DIR . '/templates/brc_template.resultados_busca.php'; 
		}
		public function brc_viagem_detalhes(){
			$ret['sts'] = false;
			
			if(isset($_POST['viagemid'])){
				$product = get_post($_POST['viagemid']);
				
				if($product){
					$post_ID = $product->ID;
					$tipo_cadastro = get_post_meta($post_ID, 'brc_viagem_tipo_cadastro', true);
					$_price = get_post_meta($post_ID, '_price', true);
					
					$oponto = $_POST['oponto'];
					$dponto = $_POST['dponto'];
					
					ob_start();
					include FEPA_PLUGIN_DIR . '/templates/brc_template.busca_viagens_detalhes.php';
					$html = ob_get_contents();
					ob_end_clean();
					
					ob_start();
					include FEPA_PLUGIN_DIR . '/templates/brc_template.busca_viagens_detalhes-lin-dados.php';
					$lin_html = ob_get_contents();
					ob_end_clean();
					
					$linha = wp_get_post_terms($post_ID, 'brc_linhas');
					if(!is_wp_error($linha)){
						$linha = $linha[0];
						$linha_pontos = get_term_meta($linha->term_id, 'linha_grupo_paradas', true);
						if($linha_pontos){
							
							foreach($linha_pontos as $k => $j){
								$ret['linha'][$k+1] = $j['p-valor'];
							}
						}
					}else{
						$ret['linha'] = false;
					}
					
					$ret['html'] = $html;
					$ret['lin_html'] = $lin_html;
					$ret['sts'] = true;
				}
			}
			echo json_encode($ret);
			exit;
		}
		public function brc_viagem_add_passageiro(){
			if(isset($_POST['viagemid'])){
				$product = get_post($_POST['viagemid']);
				
				if($product){
					$post_ID = $product->ID;
					
					ob_start();
					include FEPA_PLUGIN_DIR . '/templates/brc_template.busca_viagens_detalhes-lin-dados.php';
					$html = ob_get_contents();
					ob_end_clean();
					
					$ret['html'] = $html;
					$ret['sts'] = true;
					$ret['price'] = get_post_meta($post_ID, '_price', true);;
				}
			}
			echo json_encode($ret);
			exit;
		}
		public function brc_viagem_enviar(){
			global $woocommerce, $brcthemes;
			
			$ret['sts'] = false;
			$product_id = $_POST['viagem_id'];
			$linha_id = $_POST['linha_viagem'];
			$ponto_embarque_id = $_POST['ponto_embarque'];
			$ponto_desembarque_id = $_POST['ponto_desembarque'];
			$selvolta = $_POST['selvolta']=='yes'?true:false;;
			$tipo_cadastro = get_post_meta($product_id, 'brc_viagem_tipo_cadastro', true);
			
			$linha_viagem = wp_get_post_terms($product_id, 'brc_linha_viagem');
			if(!is_wp_error($linha_viagem)){
				$linha_viagem = $linha_viagem[0];
				$pontos_by_linha = get_pontos_by_linha($linha_viagem->term_id);
				$ponto_origem_data = $pontos_by_linha[$ponto_embarque_id];
				$ponto_destino_data = $pontos_by_linha[$ponto_desembarque_id];
			}else{
				$ret['error']['mensagem'][] = 'Por favor informe o <strong>Ponto de embarque</strong>';
				echo json_encode($ret);
				exit;
			}
			
			if(
				isset($_POST['passageiro-item']) and
				isset($_POST['passageiro-nome']) and
				isset($_POST['passageiro-cpf'])
			){
				foreach($_POST['passageiro-nome'] as $k => $j){
					$kreal = $k+1;
					
					if(empty($_POST['passageiro-nome'][$k])){
						$ret['error']['mensagem'][] = 'Por favor informe o <strong>nome</strong> do <strong>PASSAGEIRO '.$kreal.'</strong>';
					}
					if(empty($_POST['passageiro-cpf'][$k])){
						$ret['error']['mensagem'][] = 'Por favor informe o <strong>CPF</strong> do <strong>PASSAGEIRO '.$kreal.'</strong>';
					}else{
						if(!validaCPF($_POST['passageiro-cpf'][$k])){
							$ret['error']['mensagem'][] = 'Informe um <strong>CPF</strong> válido para o <strong>PASSAGEIRO '.$kreal.'</strong>.'; 
						}
					}
					if(empty($_POST['passageiro-rg'][$k])){
						$ret['error']['mensagem'][] = 'Por favor informe o <strong>RG</strong> do <strong>PASSAGEIRO '.$kreal.'</strong>';
					}
					$verf_cpf[$_POST['passageiro-cpf'][$k]][] = $_POST['passageiro-cpf'][$k];
					$verf_rg[$_POST['passageiro-rg'][$k]][] = $_POST['passageiro-rg'][$k];
				}
				if($verf_cpf){
					foreach($verf_cpf as $cpf_k => $cpf_j){
						if(count($cpf_j) > 1){
							$ret['error']['mensagem']['CPF-repeat'] = 'É apenas permitido <strong>01</strong> passeiro por <strong>CPF</strong>';
						}
					}
				}else{
					$ret['error']['mensagem'][] = 'Ocorreu um erro inesperado, por favor tente novamente.';
				}
				if($verf_rg){
					foreach($verf_rg as $rg_k => $rg_j){
						if(count($rg_j) > 1){
							$ret['error']['mensagem']['CPF-repeat'] = 'É apenas permitido <strong>01</strong> passeiro por <strong>RG</strong>';
						}
					}
				}else{
					$ret['error']['mensagem'][] = 'Ocorreu um erro inesperado, por favor tente novamente.';
				}
				if(!$ret['error']){ // adicionando ao carrinho
					
					// PRODUCT
					$product = wc_get_product($product_id);
					
					$data_embarque = get_post_meta($product_id, 'brc_viagem_ida_data', true);
					$data_embarque = get_date_timestamp(date('d/m/Y', $data_embarque)); // 00:00
					
					$data_embarque_ponto = strtotime(date('d-m-Y', $data_embarque).' '.$ponto_origem_data['brc_ponto_time']);
					$data_desembarque_ponto = strtotime(date('d-m-Y', $data_embarque).' '.$ponto_destino_data['brc_ponto_time']);
					
					$tempo_viagem = ($data_desembarque_ponto - $data_embarque_ponto)/60;
					$tempo_viagem_horas = get_min_to_hora($tempo_viagem);
					
					
					//----------------------------
					//----------------------------
					//----------------------------
					//----------------------------
					// DESTINOS
					$viagem_tempo 			= get_post_meta($product_id, 'brc_viagem_tempo', true);
					$embarque 				= get_post_meta($product_id, 'brc_viagem_origem', true);
					$desembarque 			= get_post_meta($product_id, 'brc_viagem_destino', true);
					$embarque_term 			= get_term($embarque, 'brc_destinos');
					$embarque_cidade 		= get_term($embarque_term->parent, 'brc_destinos');
					$desembarque_term 		= get_term($desembarque, 'brc_destinos');
					$desembarque_cidade 	= get_term($desembarque_term->parent, 'brc_destinos');
					//----------------------------
					//----------------------------
					//----------------------------
					//----------------------------
					
					
					$product_args = array(
						'_product_id' 					=> $product_id,
						'_product_nome' 				=> get_the_title($product_id),
						'_product_linha' 				=> $linha_id,
						'_product_ida_data' 			=> $data_embarque_ponto,
						'_product_ida_origem' 			=> $ponto_embarque_id,
						//'_product_ida_origem_cidade' 	=> $embarque_term->parent,
						'_product_ida_destino' 			=> $ponto_desembarque_id,
						//'_product_ida_destino_cidade' 	=> $desembarque_term->parent,
						'_product_volta_data' 			=> $data_desembarque_ponto,
						'_product_volta_origem' 		=> $ponto_desembarque_id,
						//'_product_volta_origem_cidade' 	=> $desembarque_term->parent,
						'_product_volta_destino' 		=> $ponto_embarque_id,
						//'_product_volta_destino_cidade' => $embarque_term->parent,
						'_product_tipo_cadastro' 		=> $tipo_cadastro,
					);
					$product_args['_product_ponto_embarque_valor'] = $ponto_origem_data['brc_ponto_valor'];
					
					
					// VIAGEM DE LINHA
					if($tipo_cadastro == 1){
						if($linha){
							$brc_viagem_embarque_data = ($linha_pontos[$ponto_embarque]['p-tempo'] * 60) + $brc_viagem_ida_data;
							$product_args['Origem Ida'] = $linha_pontos[$ponto_embarque]['p-nome'];
							$product_args['Endereço'] = $linha_pontos[$ponto_embarque]['p-endereco'];
							$product_args['Data/hora Ida'] = date('d/m/Y', $brc_viagem_embarque_data).' - '.date('H:i', $brc_viagem_embarque_data).'h <small>(aprox.)</small>';
						}else{
							$product_args['Data/hora Ida'] 	= date('d/m/Y', $brc_viagem_ida_data).' - '.date('H:i', $brc_viagem_ida_data).'h';
							$product_args['Origem Ida'] 	= $embarque_cidade->name. ' ('.$embarque_term->name.')';
						}
						$product_args['Destino Ida'] 	= $desembarque_cidade->name. ' ('.$desembarque_term->name.')';
						
						$product_args['Data/hora Volta']= date('d/m/Y', $brc_viagem_volta_data).' - '.date('H:i', $brc_viagem_volta_data).'h';
						$product_args['Origem Volta'] 	= $desembarque_cidade->name. ' ('.$desembarque_term->name.')';
						$product_args['Destino Volta'] 	= $embarque_cidade->name. ' ('.$embarque_term->name.')';
					}
					if($tipo_cadastro == 2){
						if($linha){
							$brc_viagem_embarque_data = ($linha_pontos[$ponto_embarque]['p-tempo'] * 60) + $brc_viagem_ida_data;
							$product_args['Origem'] = $linha_pontos[$ponto_embarque]['p-nome'];
							$product_args['Endereço'] = $linha_pontos[$ponto_embarque]['p-endereco'];
							$product_args['Data/hora Embarque'] = date('d/m/Y', $brc_viagem_embarque_data).' - '.date('H:i', $brc_viagem_embarque_data).'h <small>(aprox.)</small>';
						}else{
							$product_args['Origem'] = $embarque_cidade->name. ' ('.$embarque_term->name.')';
							$product_args['Data/hora Embarque'] = date('d/m/Y', $brc_viagem_ida_data).' - '.date('H:i', $brc_viagem_ida_data).'h';
						}
						
						$brc_viagem_desembarque_data = ($viagem_tempo * 60) + $brc_viagem_ida_data;
						$product_args['Destino'] = $desembarque_cidade->name. ' ('.$desembarque_term->name.')';
						$product_args['Data/hora Desembarque'] = date('d/m/Y', $brc_viagem_desembarque_data).' - '.date('H:i', $brc_viagem_desembarque_data).'h <small>(aprox.)</small>';
					}
					if($tipo_cadastro == 3){
						
						$product_args['Linha'] = $linha_viagem->name;
						$product_args['Origem'] = get_pontos_full_name($ponto_embarque_id);
						//$product_args['Endereço'] = get_pontos_full_name($ponto_embarque_id);
						$product_args['Data/hora Embarque'] = date('d/m/Y', $data_embarque_ponto).' - '.date('H:i', $data_embarque_ponto).'h <small>(aprox.)</small>';
						
						$product_args['Destino'] = get_pontos_full_name($ponto_desembarque_id);
						$product_args['Data/hora Desembarque'] = date('d/m/Y', $data_desembarque_ponto).' - '.date('H:i', $data_desembarque_ponto).'h <small>(aprox.)</small>';
						
						$cart_itens = $woocommerce->cart->get_cart();
						if($selvolta){
							if(!$brcthemes->verfEtapaVolta()){ // CARRINHO VAZIO
								$woocommerce->cart->empty_cart();
								$product_args['_order_etapa'] = 'ida';
								$product_args['_order_selvolta'] = true;
								
								$ret['sts'] = true;
								$ret['set_selvolta'] = true;
							}
							else{
								$product_args['_order_etapa'] = 'volta';
								$product_args['_order_selvolta'] = true;
								
								$ret['sts'] = true;
								$ret['set_selvolta'] = false;
								$ret['redirec_url']	= $this->FINALIZAR_RESERVA_URI;
							}
						}
						else{
							$woocommerce->cart->empty_cart();
							$product_args['_order_etapa'] = 'ida';
							$product_args['_order_selvolta'] = false;
							
							$ret['sts'] = true;
							$ret['set_selvolta'] = false;
							$ret['redirec_url'] = $this->FINALIZAR_RESERVA_URI;
						}
					}
					else{
						$woocommerce->cart->empty_cart();
						$ret['sts'] = true;
						$ret['set_selvolta'] = false;
						$ret['redirec_url'] = $this->FINALIZAR_RESERVA_URI;
					}
					
					// ADICIONA DADOS INDIVIDUAIS DOS PASSAGEIROS
					foreach($_POST['passageiro-item'] as $k => $j){
						
						$product_args_k = $product_args;
						$product_args_k['Passageiro'] 		= $_POST['passageiro-nome'][$k].' ('.$_POST['passageiro-cpf'][$k].')';
						$product_args_k['_passageiro_nome'] = $_POST['passageiro-nome'][$k];
						$product_args_k['_passageiro_cpf'] 	= $_POST['passageiro-cpf'][$k];
						$product_args_k['_passageiro_rg'] 	= $_POST['passageiro-rg'][$k];
						
						if($tipo_cadastro == 3){
							$product_args_k['_passageiro_assento'] = $_POST['passageiro-assento'][$k];
						}
						
						$woocommerce->cart->add_to_cart($product_id, 1, false, false, $product_args_k);
						$woocommerce->cart->calculate_totals();
						$woocommerce->cart->set_session();
						$woocommerce->cart->maybe_set_cart_cookies();
					}
				}
			}else{
				if($tipo_cadastro == 3){
					$ret['error']['mensagem'][] = 'Por favor selecione <strong>um assento</strong>';
				}
			}
			
			echo json_encode($ret);
			exit;
		}
		
		/*
		 * Actions checkout page results
		 */
		public function brc_remove_cart_item(){
			$ret['sts'] = false;
			
			if(isset($_POST['cartitem'])){
				global $woocommerce;
				
				$woocommerce->cart->remove_cart_item($_POST['cartitem']);
				$ret['sts'] = true;
				
				// VERIFICAR SE CARRINHO ESTÁ VAZIO
				if($woocommerce->cart->get_cart_contents_count() == 0){
					$ret['empty'] = true;
					$ret['redirec_url'] = get_permalink(get_option('fepa_busca'));
				}
			}
			
			echo json_encode($ret);
			exit;
		}
		
		/*
		 * Page layout and HTML
		 * Post Type: product: excursões
		 * [feedpass_product_excursoes_template]
		 */
		public function brc_product_excursoes_template_callback(){
			include FEPA_PLUGIN_DIR . '/templates/brc_template.excursoes_template.php'; 
		}
		public function brc_excursao_add_hospede(){
			$ret['sts'] = false;
			
			if(isset($_POST['varid'])){
				$lins_id = $_POST['linsid'];
				$var_id = $_POST['varid'];
				$variation = new WC_Product_Variation($var_id);
				$variation_data = $variation->get_data(); //regular_price
				
				$post_id = $variation_data['parent_id'];
				$product = wc_get_product($post_id);
				$brc_excursao_ingresso = get_post_meta($post_id, 'brc_excursao_ingresso', true);
				$brc_excursao_has_child_rule = get_post_meta($post_id, 'brc_excursao_has_child_rule', true);
				
				if($variation_data['id'] > 0){
					if($brc_excursao_ingresso){
						$brc_excursao_ingresso_stock = get_post_meta($post_id, 'brc_excursao_ingresso_stock', true);
						$brc_excursao_ingresso_preco = get_post_meta($post_id, 'brc_excursao_ingresso_preco', true);
						
						// INGRESSO STOCK
						if($brc_excursao_ingresso_stock < 1) 
							$brc_excursao_ingresso = false;
					}
					
					$_hotel = get_post_meta($var_id, 'brc_excursao_variations_hotel', true);
					$_nome = get_post_meta($var_id, 'brc_excursao_variations_nome', true);
					$_pquarto = intval (get_post_meta($var_id, 'brc_excursao_variations_pquarto', true));
					
					$ret['price'] = $variation_data['price'];
					$ret['data'] = array(
						'_hotel' => $_hotel,
						'_nome' => $_nome,
						'_pquarto' => $_pquarto
					);
					
					ob_start();
					include FEPA_PLUGIN_DIR . '/templates/brc_template.excursao-hospedes.php';
					$html = ob_get_contents();
					ob_end_clean();
					
					$ret['html'] = $html;
					$ret['sts'] = true;
					
					echo json_encode($ret);
					exit;
				}
			}
			
			echo json_encode($ret);
			exit;
		}
		public function brc_excursao_add_child(){
			$ret['sts'] = false;
			
			if(isset($_POST['varid'])){
				$lins_id = $_POST['linsid'];
				$var_id = $_POST['varid'];
				$variation = new WC_Product_Variation($var_id);
				$variation_data = $variation->get_data();
				$post_id = $variation_data['parent_id'];
				$product = wc_get_product($post_id);
				
				$brc_excursao_has_child_rule = get_post_meta($post_id, 'brc_excursao_has_child_rule', true);
				
				if($brc_excursao_has_child_rule){
					
					$child_rule = get_post_meta($post_id, 'brc_excursao_child_rule', true);
					
					ob_start();
					include FEPA_PLUGIN_DIR . '/templates/brc_template.excursao-child.php';
					$html = ob_get_contents();
					ob_end_clean();
					
					$ret['child_rule'] = $child_rule;
					$ret['html'] = $html;
					$ret['sts'] = true;
				}
			}
			
			echo json_encode($ret);
			exit;
		}
		public function brc_comprar_excursao(){
			global $woocommerce;
			
			$ret['sts'] = false;
			
			if(isset($_POST['variacao-item'])){
				
				// VERIFICA HÓSPEDES
				$kreal = 1;
				foreach($_POST['variacao-item-hospede'] as $vd => $line_items){ // [$vd][$ld][]
					foreach($line_items as $ld => $items){
						foreach($items as $k => $j){
							if(empty($_POST['passageiro-nome'][$vd][$ld][$k]))
								$ret['error']['mensagem'][] = 'Por favor informe o <strong>nome</strong> do <strong>HÓSPEDE '.$kreal.'</strong>';
							
							if(empty($_POST['passageiro-cpf'][$vd][$ld][$k])){
								$ret['error']['mensagem'][] = 'Por favor informe o <strong>CPF</strong> do <strong>HÓSPEDE '.$kreal.'</strong>';
							}else{
								if(!validaCPF($_POST['passageiro-cpf'][$vd][$ld][$k])){
									$ret['error']['mensagem']['CPF'] = 'Informe um <strong>CPF</strong> único para cada Hóspede.';
								}
							}
							
							$verf_cpf[$_POST['passageiro-cpf'][$vd][$ld][$k]][] = $_POST['passageiro-cpf'][$vd][$ld][$k];
							$kreal++;
						}
					}
				}
				
				// VERIFICA CRIANÇAS
				$kreal = 1;
				if(isset($_POST['variacao-item-child'])){
					foreach($_POST['variacao-item-child'] as $vd => $line_items){ // [$vd][$ld][]
						foreach($line_items as $ld => $items){
							foreach($items as $k => $j){
								
								if(empty($_POST['crianca-nome'][$vd][$ld][$k]))
									$ret['error']['mensagem'][] = 'Por favor informe o <strong>nome</strong> da <strong>CRIANÇA '.$kreal.'</strong>';
								
								if($_POST['crianca-idade'][$vd][$ld][$k] == 'no')
									$ret['error']['mensagem'][] = 'Por favor informe a <strong>idade</strong> da <strong>CRIANÇA '.$kreal.'</strong>';
								
								$kreal++;
							}
						}
					}
				}
				
				// VERIFICA CPF REPETIDO
				if($verf_cpf){
					foreach($verf_cpf as $cpf_k => $cpf_j){
						if(count($cpf_j) > 1){
							$ret['error']['mensagem']['CPF-repeat'] = 'É apenas permitido <strong>01</strong> passageiro por <strong>CPF</strong>';
						}
					}
				}else{
					$ret['error']['mensagem'][] = 'Ocorreu um erro inesperado, por favor tente novamente.';
				}
				
				
				if(!$ret['error']){
					$woocommerce->cart->empty_cart();
					
					foreach($_POST['variacao-item'] as $ld => $vd){
						
						// VARIATION
						$var_id 							= $vd;
						$variation 							= new WC_Product_Variation($var_id);
						$variation_data 					= $variation->get_data();
						$brc_excursao_variations_hotel 		= get_post_meta($var_id, 'brc_excursao_variations_hotel', true);
						$brc_excursao_variations_nome 		= get_post_meta($var_id, 'brc_excursao_variations_nome', true);
						$brc_excursao_variations_pquarto 	= get_post_meta($var_id, 'brc_excursao_variations_pquarto', true);
						$attribute_quartos 					= get_post_meta($var_id, 'attribute_quartos', true);
						
						// PRODUCT
						$product_id 						= $variation_data['parent_id'];
						$brc_excursao_ingresso 				= get_post_meta($product_id, 'brc_excursao_ingresso', true);
						$brc_excursao_ingresso_stock 		= get_post_meta($product_id, 'brc_excursao_ingresso_stock', true);
						$brc_excursao_ingresso_preco 		= get_post_meta($product_id, 'brc_excursao_ingresso_preco', true);
						$brc_excursao_data 					= get_post_meta($product_id, 'brc_excursao_data', true);
						$brc_excursao_data_volta 			= get_post_meta($product_id, 'brc_excursao_data_volta', true);
						$brc_prod_hospedagem_noites 		= get_post_meta($product_id, 'brc_prod_hospedagem_noites', true);
						
						$brc_excursao_has_child_rule 		= get_post_meta($post_id, 'brc_excursao_has_child_rule', true);
						if($brc_excursao_has_child_rule)
							$child_rule = get_post_meta($post_id, 'brc_excursao_child_rule', true);
						
						$variation_args = array(
							'_product_id' 			=> $product_id,
							'_product_nome' 		=> get_the_title($product_id),
							'_product_noites' 		=> $brc_prod_hospedagem_noites,
							'_product_data' 		=> $brc_excursao_data,
							'_product_data_volta' 	=> $brc_excursao_data_volta,
							
							'_variation_pacote' 	=> $brc_excursao_variations_nome,
							'_variation_hotel' 		=> get_the_title($brc_excursao_variations_hotel),
							'_variation_hotel_id' 	=> $brc_excursao_variations_hotel,
							'_ex_variation_price' 	=> $variation_data['regular_price'],
							
							'_ingresso'				=> false,
							'_crianca'				=> false,
							'_hospedes' 			=> array(),
						);
						
						
						// ADICIONA HÓSPEDES
						if($_POST['variacao-item-hospede'][$vd][$ld]){
							foreach($_POST['variacao-item-hospede'][$vd][$ld] as $k => $j){
								
								$_hospedes = array(
									'nome' => $_POST['passageiro-nome'][$vd][$ld][$k],
									'cpf' => $_POST['passageiro-cpf'][$vd][$ld][$k],
								);
								if($_POST['in-ver'][$vd][$ld][$k] == 'yes'){
									$_hospedes['ingresso'] = true;
									$_hospedes['ingresso_price'] = $brc_excursao_ingresso_preco;
									$variation_args['_ingresso'] = true;
								}else{
									$_hospedes['ingresso'] = false;
								}
								$variation_args['_hospedes'][] = $_hospedes;
							}
						}
						
						
						// ADICIONA AS CRIANÇA
						if($_POST['variacao-item-child'][$vd][$ld]){
							$variation_args['_crianca'] = true;
							$variation_args['_criancas'] = array();
							
							foreach($_POST['variacao-item-child'][$vd][$ld] as $k => $j){
								$_criancas = array(
									'nome' => $_POST['crianca-nome'][$vd][$ld][$k],
									'idade' => $_POST['crianca-idade'][$vd][$ld][$k],
									'price' => $_POST['crianca-price'][$vd][$ld][$k],
								);
							}
							$variation_args['_criancas'][] = $_criancas;
						}
						// CALCULAR VALOR CRIANÇAS
						if($variation_args['_crianca']){
							$criancas_price_aumont = 0;
							
							if($variation_args['_criancas']){
								foreach($variation_args['_criancas'] as $crianca){
									$criancas_price_aumont += $crianca['price'];
								}
							}
							$variation_args['_ex_variation_price'] = $variation_args['_ex_variation_price'] + $criancas_price_aumont;
						}
						
						
						// ADICIONA INGRESSO NO PREÇO
						if($variation_args['_ingresso']){
							$variation_args['_ingresso_price'] = $brc_excursao_ingresso_preco;
							$ingressos_price_aumont = 0;
							
							if($variation_args['_hospedes']){
								foreach($variation_args['_hospedes'] as $hospede){
									if($hospede['ingresso']){
										$ingressos_price_aumont += $hospede['ingresso_price'];
									}
								}
							}
							$variation_args['_ex_variation_price'] = $variation_args['_ex_variation_price'] + $ingressos_price_aumont;
						}
						
						$ret['sts'] = true; 
						$ret['redirec_url'] = $this->FINALIZAR_RESERVA_URI;
						
						$woocommerce->cart->add_to_cart($product_id, 1, $var_id, false, $variation_args);
						$woocommerce->cart->calculate_totals();
						$woocommerce->cart->set_session();
						$woocommerce->cart->maybe_set_cart_cookies();
					}
				}
			}else{
				$ret['error']['mensagem'][] = 'Selecione um pacote.';
			}
			
			echo json_encode($ret);
			exit;
		}
		
		/*
		 * Page layout and HTML
		 * [feedpass_ lista_hospedes]
		 */
		public function brc_lista_hospedes_template_callback(){
			include FEPA_PLUGIN_DIR . '/templates/brc_admin.lista_hospedes.php'; 
		}
		public function brc_lista_hospedes_template_redirect($original_template){
			
			$fepa_lista_de_hospedes = get_option('fepa_lista_de_hospedes');
			$page_id = get_the_ID();
			
			if($fepa_lista_de_hospedes == $page_id){
				global $wp_query;
				
				if(
					!current_user_can('administrator') AND 
					!current_user_can('administradorsimples')
				){
					$wp_query->set_404();
					status_header(404);
					get_template_part(404);
					exit;
				}
				
				$product_id = $_GET['v'];
				$product = wc_get_product($product_id);
				if(!$product){
					$wp_query->set_404();
					status_header(404);
					get_template_part(404);
					exit;
				}
				
				$orders = get_orders_ids_by_product_id($product_id, get_all_woocommerce_status_id());
				if(!$orders){
					$wp_query->set_404();
					status_header(404);
					get_template_part(404);
					exit;
				}
				
				wp_enqueue_style('brc_themes_admins_fonts', 'https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap', false, FEPA_SCRIPT_VERSION);
				wp_enqueue_style('brc_themes_admins', FEPA_PLUGIN_URL.'/assets/css/brc_themes_admins.css', false, FEPA_SCRIPT_VERSION);
				
				$html = do_shortcode(get_the_content(false, false, $page_id));
				echo $html;
				exit;
			}
		}
		
		/*
		 * Page layout and HTML
		 * [feedpass_ lista_passageiros]
		 */
		public function brc_lista_passageiros_template_callback(){
			//include FEPA_PLUGIN_DIR . '/templates/brc_admin.ticket.php'; 
		}
		
		/*
		 * Page layout and HTML
		 * [feedpass_ lista_de_comprovante_reserva]
		 */
		public function brc_lista_de_comprovante_reserva_template_callback(){
			include FEPA_PLUGIN_DIR . '/templates/brc_admin.comprovante_reserva_template.php'; 
		}
		public function brc_lista_de_comprovante_reserva_template_redirect($original_template){
			
			$fepa_comprovante_reserva = get_option('fepa_comprovante_reserva');
			$page_id = get_the_ID();
			
			if($fepa_comprovante_reserva == $page_id){
				global $current_user, $wp_query, $post, $woocommerce, $brcpasstour_theme;
				
				if(!isset($_GET['p'])){
					$wp_query->set_404();
					status_header( 404 );
					get_template_part( 404 );
					exit();
				}
				
				$p = base64_decode($_GET['p']);
				$p = explode('|', $p);
				$order_id = $p[0];
				$order_item_id  = $p[1];
				$order_item_passageiro = $p[2];
				
				if(!$order_id){
					$wp_query->set_404();
					status_header( 404 );
					get_template_part( 404 );
					exit;
				}
				$poder_post = get_post($order_id);
				
				if(!$poder_post){
					$wp_query->set_404();
					status_header( 404 );
					get_template_part( 404 );
					exit;
				}
				if(!is_user_logged_in()){
					$post_slug = $post->post_name;
					$login_url = get_permalink(get_option('woocommerce_myaccount_page_id')).'?'.http_build_query($_GET).'&redirect_to='.$post->post_name;
					wp_redirect($login_url);
					exit;
				}
				
				if($poder_post->post_type == 'product' and $_GET['lista'] == 'product'){
					if(!current_user_can('administrator') AND !current_user_can('administradorsimples')){
						$wp_query->set_404();
						status_header(404);
						get_template_part(404);
						exit;
					}
				}else{
					$order = new WC_Order($poder_post->ID);
					if($current_user->ID != $order->get_user_id() AND (!current_user_can('administrator') AND !current_user_can('administradorsimples'))){
						$wp_query->set_404();
						status_header(404);
						get_template_part(404);
						exit;
					}
				}
				wp_enqueue_style('brc_themes_admins_fonts', 'https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap', false, FEPA_SCRIPT_VERSION);
				wp_enqueue_style('brc_themes_admins', FEPA_PLUGIN_URL.'/assets/css/brc_themes_admins.css', false, FEPA_SCRIPT_VERSION);
				
				$html = do_shortcode(get_the_content(false, false, $page_id));
				echo $html;
				exit;
			}
		}
		
		/*
		 * Page layout and HTML
		 * [feedpass_ ticket]
		 */
		public function brc_ticket_template_callback(){
			include FEPA_PLUGIN_DIR . '/templates/brc_admin.ticket.php'; 
		}
		public function brc_ticket_template_redirect(){
			$fepa_passagemticket = get_option('fepa_passagemticket');
			$page_id = get_the_ID();
			
			if($fepa_passagemticket == $page_id){
				global $current_user, $wp_query, $post;
	
				// RETORN 404
				if(!isset($_GET['p'])){
					$wp_query->set_404();
					status_header(404);
					get_template_part(404);
					exit;
				}
				
				$p = base64_decode($_GET['p']);
				$p = explode('|', $p);
				$order_id = $p[0];
				$order_item_id = $p[1];
				$order_item_passageiro = $p[2];
				$poder_post = get_post($order_id);
				$largura = 80;
				$altura = 260;
				
				if(!$poder_post){
					$wp_query->set_404();
					status_header(404);
					get_template_part(404);
					exit;
				}
				if(!is_user_logged_in()){
					$post_slug = $post->post_name;
					$login_url = get_permalink(get_option('woocommerce_myaccount_page_id')).'?'.http_build_query($_GET).'&redirect_to='.$post->post_name;
					wp_redirect($login_url);
					exit;
				}
				
				
				if(
					$_GET['lista'] == 'product' or
					$_GET['lista'] == 'order'
				){
					if(!current_user_can('administrator') AND !current_user_can('administradorsimples')){
						$wp_query->set_404();
						status_header(404);
						get_template_part(404);
						exit;
					}
				}else{
					$order = new WC_Order($poder_post->ID);
					$order_item = $order->get_items()[$order_item_id];
					$produto_id = $order_item->get_product_id();
					
					if(
						$current_user->ID != $order->get_user_id() AND 
						(
							!current_user_can('administrator') AND 
							!current_user_can('administradorsimples')
						)
					){
						$wp_query->set_404();
						status_header(404);
						get_template_part(404);
						exit;
					}
				}
				
				wp_enqueue_style('brc_themes_admins_fonts', 'https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap', false, FEPA_SCRIPT_VERSION);
				wp_enqueue_style('brc_themes_admins', FEPA_PLUGIN_URL.'/assets/css/brc_themes_admins.css', false, FEPA_SCRIPT_VERSION);
				
				$html = do_shortcode(get_the_content(false, false, $page_id));
				echo $html;
				exit;
			}
		}
		
		
		
		// .FEPAPagesShortcodes
	}
?>