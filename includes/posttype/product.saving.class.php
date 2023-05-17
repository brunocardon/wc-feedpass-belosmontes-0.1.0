<?php
	class FEPAPostTypeSavingProduct{
		protected static $instance = null;
		
		
		function __construct(){
			
			add_action('save_post', array($this, 'brc_save_post_product'), 100, 3);
			
			add_action('wp_ajax_nopriv_brc_tb_distribuir_quartos', 					array($this, 'brc_tb_distribuir_quartos'));
			add_action('wp_ajax_brc_tb_distribuir_quartos', 						array($this, 'brc_tb_distribuir_quartos'));
			add_action('wp_ajax_nopriv_brc_distribuir_quartos_save', 				array($this, 'brc_distribuir_quartos_save'));
			add_action('wp_ajax_brc_distribuir_quartos_save', 						array($this, 'brc_distribuir_quartos_save'));
			add_action('wp_ajax_nopriv_brc_tb_distribuir_assentos', 				array($this, 'brc_tb_distribuir_assentos'));
			add_action('wp_ajax_brc_tb_distribuir_assentos', 						array($this, 'brc_tb_distribuir_assentos'));
			add_action('wp_ajax_nopriv_brc_distribuir_assentos_save', 				array($this, 'brc_distribuir_assentos_save'));
			add_action('wp_ajax_brc_distribuir_assentos_save', 						array($this, 'brc_distribuir_assentos_save'));
		}
		
		static function init(){
			if(null == self::$instance ){
				self::$instance = new self;
			}
			return self::$instance;
		}
		
		public function register(){
			// nothing to see here...
		}
		
		/**
		 * Aditional function to save post
		 * Post Type: product
		 * 
		 */
		public function brc_save_post_product($post_ID, $post, $update){
			$brc_excursao = (isset($_POST['brc_excursao']) and $_POST['brc_excursao']=='yes');
			$metatype_excursoes = (isset($_GET['metatype']) and $_GET['metatype']=='excursoes');
			
			if($post->post_status == 'auto-draft' or  $post->post_status == 'draft'){
				if($post->post_type == 'product'){
					if($metatype_excursoes){
						update_post_meta($post_ID, 'brc_excursao', true);
					}else{
						update_post_meta($post_ID, 'brc_excursao', false);
					}
				}
				return false;
			}
			
			
			if($post->post_type == 'product'){
				$brc_masscad_item = get_post_meta($post_ID, 'brc_masscad_item', true);
				if(!$brc_masscad_item){
					$brc_excursao = get_post_meta($post_ID, 'brc_excursao', true);
					if($brc_excursao){
						$this->brc_save_post_product_excursao($post_ID, $post, $update);
					}else{
						$this->brc_save_post_product_viagens($post_ID, $post, $update);
					}
				}
			}
		}
		
		/**
		 * Aditional function to save post
		 * Post Type: product: excursões
		 * 
		 */
		public function brc_save_post_product_excursao($post_ID, $post, $update){
			$product = wc_get_product($post_ID);
			
			// PRODUCT TYPE
			update_post_meta($post_ID, '_virtual', 'yes');
			
			if($_POST['brc_excursao_subtitle'])
				update_post_meta($post_ID, 'brc_excursao_subtitle', $_POST['brc_excursao_subtitle']);
			
			$brc_excursao_data = strtotime($_POST['brc_excursao_data_dia'].' '.$_POST['brc_excursao_data_hora']);
			update_post_meta($post_ID, 'brc_excursao_data', $brc_excursao_data);
			update_post_meta($post_ID, 'brc_excursao_data_dia', $_POST['brc_excursao_data_dia']);
			update_post_meta($post_ID, 'brc_excursao_data_hora', $_POST['brc_excursao_data_hora']);
			
			$brc_excursao_data_volta = strtotime($_POST['brc_excursao_data_volta_dia'].' '.$_POST['brc_excursao_data_volta_hora']);
			update_post_meta($post_ID, 'brc_excursao_data_volta', $brc_excursao_data_volta);
			update_post_meta($post_ID, 'brc_excursao_data_volta_dia', $_POST['brc_excursao_data_volta_dia']);
			update_post_meta($post_ID, 'brc_excursao_data_volta_hora', $_POST['brc_excursao_data_volta_hora']);
			
			update_post_meta($post_ID, 'brc_excursao_tempo_viagem', $_POST['brc_excursao_tempo_viagem']);
			
			update_post_meta($post_ID, 'brc_excursao_ingresso', $_POST['brc_excursao_ingresso']);
			update_post_meta($post_ID, 'brc_excursao_ingresso_stock', $_POST['brc_excursao_ingresso_stock']);
			update_post_meta($post_ID, 'brc_excursao_ingresso_preco', $_POST['brc_excursao_ingresso_preco']);
			update_post_meta($post_ID, 'brc_excursao_quant_veiculos', $_POST['brc_excursao_quant_veiculos']);
			update_post_meta($post_ID, 'brc_excursao_quant_assentos', $_POST['brc_excursao_quant_assentos']);
			
			update_post_meta($post_ID, 'brc_excursao_variations_hotel', $_POST['brc_excursao_variations_hotel']);
			update_post_meta($post_ID, 'brc_excursao_variations_nome', $_POST['brc_excursao_variations_nome']);
			update_post_meta($post_ID, 'brc_excursao_variations_stock', $_POST['brc_excursao_variations_stock']);
			//update_post_meta($post_ID, 'brc_excursao_variations_stock_total', $_POST['brc_excursao_variations_stock_total']);
			update_post_meta($post_ID, 'brc_excursao_variations_pquarto', $_POST['brc_excursao_variations_pquarto']);
			wp_set_post_terms($post_ID, 'variable', 'product_type', false);
			
			if(isset($_POST['brc_prod_hospedagem_servicos'])){
				wp_set_post_terms($post_ID, $_POST['brc_prod_hospedagem_servicos'], 'brc_servicos', false);
			}
			
			// ADICIONA CHILD RULES
			if($_POST['brc_excursao_has_child_rule'] == 'yes'){
				$child_rule = array();
				foreach($_POST['brc_add_product_excursao_child_id'] as $k => $j){
					$child_rule[sanitize_title($_POST['brc_add_product_excursao_child_nome'][$k], '')] = array(
						'rule_nome' => $_POST['brc_add_product_excursao_child_nome'][$k],
						'rule_detalhes' => $_POST['brc_add_product_excursao_child_detalhes'][$k],
						'rule_preco' => $_POST['brc_add_product_excursao_child_preco'][$k],
					);
				}
				if($child_rule){
					update_post_meta($post_ID, 'brc_excursao_has_child_rule', true);
					update_post_meta($post_ID, 'brc_excursao_child_rule', $child_rule);
				}else{
					update_post_meta($post_ID, 'brc_excursao_has_child_rule', false);
					delete_post_meta($post_ID, 'brc_excursao_child_rule');
				}
			}else{
				update_post_meta($post_ID, 'brc_excursao_has_child_rule', false);
				delete_post_meta($post_ID, 'brc_excursao_child_rule');
			}
			
			
			// PREPARA VARIATION
			if($_POST['brc_excursao_variations_hotel']){
				foreach($_POST['brc_excursao_variations_hotel'] as $k => $j){
					
					$variation = new WC_Product_Variation($_POST['brc_excursao_variations_id'][$k]);
					if($variation->get_data()['status'] != 'publish'){
						$variation_post = array(
							'post_title'  => $product->get_name().' - '.$_POST['brc_excursao_variations_nome'][$k],
							'post_name'   => sanitize_title($product->get_name().' - '.$_POST['brc_excursao_variations_nome'][$k], 'product-'.$post_ID.'-variation'),
							'post_status' => 'publish',
							'post_parent' => $post_ID,
							'post_type'   => 'product_variation',
							'guid'        => $product->get_permalink()
						);
						$variation_id = wp_insert_post($variation_post);
						$variation = new WC_Product_Variation($variation_id);
					}else{
						$variation_id = $_POST['brc_excursao_variations_id'][$k];
					}
					
					if($_POST['variable_description'][$k])
						$variation->set_description($_POST['variable_description'][$k]);
					
					$variation->set_price($_POST['brc_excursao_variations_preco'][$k]);
					$variation->set_regular_price($_POST['brc_excursao_variations_preco'][$k]);
					$variation->set_manage_stock(true);
					$variation->set_stock_quantity($_POST['brc_excursao_variations_stock'][$k]);
					$variation->set_stock_status('');
					$variation->set_weight('');
					$variation->set_virtual(1);
					
					update_post_meta($variation_id, 'brc_excursao_variations_hotel', $_POST['brc_excursao_variations_hotel'][$k]);
					update_post_meta($variation_id, 'brc_excursao_variations_nome', $_POST['brc_excursao_variations_nome'][$k]);
					update_post_meta($variation_id, 'brc_excursao_variations_pquarto', $_POST['brc_excursao_variations_pquarto'][$k]);
					//update_post_meta($variation_id, 'brc_excursao_variations_stock_total', $_POST['brc_excursao_variations_stock_total'][$k]);
					update_post_meta($variation_id, 'attribute_quartos', $_POST['brc_excursao_variations_nome'][$k]);
					
					$variation->save();
				}
			}
			$product_attributes = array(
				'quartos' => array(
					'name' 			=> 'Quartos',
					'value' 		=> implode(' | ', $_POST['brc_excursao_variations_nome']),
					'position' 		=> 0,
					'is_visible' 	=> 0,
					'is_variation' 	=> 1,
					'is_taxonomy' 	=> 0,
				),
			);
			// VARIATION POST META ATTR
			update_post_meta($post_ID, '_product_attributes', $product_attributes);
		}
		
		/**
		 * Aditional function to save post
		 * Post Type: product: viagens
		 * 
		 */
		private function brc_save_post_product_viagens($post_ID, $post, $update){
			$product = wc_get_product($post_ID);
			$viagem_tipo_array = array(
				0 => 'brc_viagem_',
				1 => 'brc_viagem_',
				2 => 'brc_viagem_grupo_',
				3 => 'brc_viagem_ab_',
			);
			
			if(!isset($_POST['brc_viagem_tipo_cadastro'])){
				return false;
			}else{
				$viagem_tipo = $viagem_tipo_array[$_POST['brc_viagem_tipo_cadastro']];
			}
			
			// PRODUCT TYPE
			update_post_meta($post_ID, '_virtual', 'yes');
			update_post_meta($post_ID, 'brc_viagem_tipo_cadastro', $_POST['brc_viagem_tipo_cadastro']);
			
			update_post_meta($post_ID, 'brc_viagem_tempo', $_POST[$viagem_tipo.'tempo']);
			update_post_meta($post_ID, 'brc_viagem_origem', $_POST[$viagem_tipo.'origem']);
			update_post_meta($post_ID, 'brc_viagem_destino', $_POST[$viagem_tipo.'destino']);
			update_post_meta($post_ID, 'brc_viagem_preco', $_POST[$viagem_tipo.'preco']);
			update_post_meta($post_ID, 'brc_viagem_linha', $_POST[$viagem_tipo.'linha']);
			update_post_meta($post_ID, 'brc_viagem_veiculos', $_POST[$viagem_tipo.'veiculos']);
			update_post_meta($post_ID, 'brc_viagem_motorista', $_POST[$viagem_tipo.'motorista']);
			update_post_meta($post_ID, 'brc_viagem_veiculos_quant', $_POST[$viagem_tipo.'veiculos_quant']);
			update_post_meta($post_ID, 'brc_viagem_assentos', $_POST[$viagem_tipo.'assentos']);
			update_post_meta($post_ID, 'brc_viagem_assentos_veiculos', $_POST[$viagem_tipo.'assentos_veiculos']);
			
			// ORIGENS/DESTINOS
			$embarque_term = get_term($_POST[$viagem_tipo.'origem'], 'brc_destinos');
			$embarque_term_p = get_term($embarque_term->parent, 'brc_destinos');
			
			$desembarque_term = get_term($_POST[$viagem_tipo.'destino'], 'brc_destinos');
			$desembarque_term_p = get_term($desembarque_term->parent, 'brc_destinos');
			
			wp_set_object_terms($post_ID, array(
				$embarque_term->term_id, 
				$embarque_term->parent,
				$desembarque_term->term_id, 
				$desembarque_term->parent,
			), 'brc_destinos', false);
			
			// LINHA
			if($_POST[$viagem_tipo.'linha']){
				$brc_viagem_linha[] = intval($_POST[$viagem_tipo.'linha']);
				wp_set_object_terms($post_ID, $brc_viagem_linha, 'brc_linha_viagem', false);
			}else{
				$linha = wp_get_post_terms($post_ID, 'brc_linha_viagem');
				if(!is_wp_error($linha)){
					$linha = $linha[0];
					wp_remove_object_terms($post_ID, $linha->term_id, 'brc_linha_viagem');
				}
			}
			
			// VEÍCULO
			if($_POST[$viagem_tipo.'veiculos']){
				foreach($_POST[$viagem_tipo.'veiculos'] as $j){
					$brc_viagem_veiculos[] = intval($j);
					$veiculo = get_term_meta($j, 'brc_brc_veiculo_acomodacoes', true);
				}
				wp_set_object_terms($post_ID, $brc_viagem_veiculos, 'brc_veiculo', false);
			}
			
			// MOTORISTA
			if($_POST[$viagem_tipo.'motorista']){
				foreach($_POST[$viagem_tipo.'motorista'] as $j){
					$brc_viagem_motorista[] = intval($j);
				}
				wp_set_object_terms($post_ID, $brc_viagem_motorista, 'brc_motorista', false);
			}
			
			// _STOCK
			update_post_meta($post_ID, '_stock', intval($_POST[$viagem_tipo.'assentos']));
			update_post_meta($post_ID, '_manage_stock', 'yes');
			
			// ASSENTOS
			$brc_viagem_tipo_cadastro = get_post_meta($post_ID, 'brc_viagem_tipo_cadastro', true);
			if($brc_viagem_tipo_cadastro == 3){
				if($veiculo > 1){
					$brc_assentos_disponiveis = get_post_meta($post_ID, 'brc_assentos_disponiveis', true);
					$brc_viagem_assentos = $_POST[$viagem_tipo.'assentos'];
					for($i=1;$i<=$brc_viagem_assentos;$i++){
						$assentos_disponiveis[$i] = isset($brc_assentos_disponiveis[$i])?$brc_assentos_disponiveis[$i]:true;
					}
					update_post_meta($post_ID, 'brc_assentos_disponiveis', $assentos_disponiveis);
				}
				else{
					$brc_viagem_assentos = $_POST[$viagem_tipo.'assentos_veiculos'];
					$brc_assentos_disponiveis = get_post_meta($post_ID, 'brc_assentos_disponiveis', true);
					
					$assentos 	= $brc_viagem_assentos;
					$mult 		= 4;
					$parts 		= (($assentos+$mult)/$mult)+1;
					$ftotal 	= $parts*$mult;
					
					for($i=1;$i<=$ftotal;$i++){
						$fassentos[$i] = true;
					}
					for($i=1;$i<=$parts;$i++){
						$rem = ($i*$mult);
						unset($fassentos[$rem]);
					}
					
					foreach($fassentos as $k => $j){
						$assentos_disponiveis[$k] = isset($brc_assentos_disponiveis[$k])?$brc_assentos_disponiveis[$k]:true;
					}
					update_post_meta($post_ID, 'brc_assentos_disponiveis', $assentos_disponiveis);
				}
			}
			
			// _PRICE
			update_post_meta($post_ID, '_price', $_POST[$viagem_tipo.'preco']);
			update_post_meta($post_ID, '_regular_price', $_POST[$viagem_tipo.'preco']);
			
			
			//------------
			if(
				// CADASTRO EM MASSA
				isset($_POST[$viagem_tipo.'masscad']) and 
				$_POST[$viagem_tipo.'masscad'] == 'yes'
			){
				update_post_meta($post_ID, 'brc_viagem_masscad', $_POST[$viagem_tipo.'masscad']);
				update_post_meta($post_ID, 'brc_viagem_masscad_ida_semana', $_POST[$viagem_tipo.'masscad_ida_semana']);
				update_post_meta($post_ID, 'brc_viagem_masscad_ida_hora', $_POST[$viagem_tipo.'masscad_ida_hora']);
				update_post_meta($post_ID, 'brc_viagem_masscad_volta_semana', $_POST[$viagem_tipo.'masscad_volta_semana']);
				update_post_meta($post_ID, 'brc_viagem_masscad_volta_hora', $_POST[$viagem_tipo.'masscad_volta_hora']);
				update_post_meta($post_ID, 'brc_viagem_masscad_periodo_ini', $_POST[$viagem_tipo.'masscad_periodo_ini']);
				update_post_meta($post_ID, 'brc_viagem_masscad_periodo_fim', $_POST[$viagem_tipo.'masscad_periodo_fim']);
				
				$semana_ida 	= $_POST[$viagem_tipo.'masscad_ida_semana'];
				$semana_volta 	= $_POST[$viagem_tipo.'masscad_volta_semana']?$_POST[$viagem_tipo.'masscad_volta_semana']:false;
				$per_ini 		= strtotime($_POST[$viagem_tipo.'masscad_periodo_ini']);
				$per_final 		= strtotime($_POST[$viagem_tipo.'masscad_periodo_fim']);
				$dates_per 		= get_dates_per($semana_ida, $semana_volta, $per_ini, $per_final);
				if($dates_per){
					update_post_meta($post_ID, 'brc_viagem_dates_per', $dates_per);
					$current_masscad = $dates_per[0];
					
					unset($dates_per[0]);
					foreach($dates_per as $k => $j){
						
						$_title = $embarque_term_p->name.' -> '.$desembarque_term_p->name.' | ';
						if($_POST['brc_viagem_tipo_cadastro'] <= 1){
							$_title .= 'Ida: '.get_dia_semana($j['ida']['_diaSemana']).' ['.$j['ida']['_data'].'] - ';
							$_title .= 'Volta: '.get_dia_semana($j['volta']['_diaSemana']).' ['.$j['volta']['_data'].']';
						}else{
							$_title .= get_dia_semana($j['ida']['_diaSemana']).' ['.$j['ida']['_data'].']';
						}
						
						$masscad_args = array(
							'post_title' => $_title,
							'post_status' => 'publish',
							'post_type' => 'product',
							'meta_input' => array(
								'brc_masscad_item' => true,
							),
						);
						$masscad_product_id = wp_insert_post($masscad_args);
						
						// ORIGENS/DESTINOS
						$embarque_term = get_term($_POST[$viagem_tipo.'origem'], 'brc_destinos');
						$desembarque_term = get_term($_POST[$viagem_tipo.'destino'], 'brc_destinos');
						wp_set_object_terms($masscad_product_id, array(
							$embarque_term->term_id, 
							$embarque_term->parent,
							$desembarque_term->term_id, 
							$desembarque_term->parent,
						), 'brc_destinos', false);
						
						// LINHA
						if($_POST[$viagem_tipo.'linha']){
							$brc_viagem_linha[] = intval($_POST[$viagem_tipo.'linha']);
							wp_set_object_terms($masscad_product_id, $brc_viagem_linha, 'brc_linhas', false);
						}
						
						// VEÍCULO
						if($_POST[$viagem_tipo.'veiculos']){
							foreach($_POST[$viagem_tipo.'veiculos'] as $taj){
								$brc_viagem_veiculos[] = intval($taj);
							}
							wp_set_object_terms($masscad_product_id, $brc_viagem_veiculos, 'brc_veiculo', false);
						}
						
						// MOTORISTA
						if($_POST[$viagem_tipo.'motorista']){
							foreach($_POST[$viagem_tipo.'motorista'] as $taj){
								$brc_viagem_motorista[] = intval($taj);
							}
							wp_set_object_terms($masscad_product_id, $brc_viagem_motorista, 'brc_motorista', false);
						}
						
						$data = get_post_custom($post_ID);
						foreach($data as $key => $values){
							foreach($values as $value){
								switch($key){
									case 'brc_viagem_motorista':
									case 'brc_viagem_veiculos':
									case 'brc_viagem_dates_per':
									case 'brc_assentos_disponiveis':
										$vvalue = unserialize($value);
									break;
									default:
										$vvalue = $value;
									break;
								}
								
								add_post_meta($masscad_product_id, $key, $vvalue);
							}
						}
						
						$brc_viagem_ida_data = date('d-m-Y', $j['ida']['_dataTimestamp']).' '.$_POST[$viagem_tipo.'masscad_ida_hora'];
						$brc_viagem_ida_data = strtotime($brc_viagem_ida_data);
						$brc_viagem_volta_data = date('d-m-Y', $j['volta']['_dataTimestamp']).' '.$_POST[$viagem_tipo.'masscad_volta_hora'];
						$brc_viagem_volta_data = strtotime($brc_viagem_volta_data);
						
						update_post_meta($masscad_product_id, 'brc_viagem_ida_data', 		$brc_viagem_ida_data);
						update_post_meta($masscad_product_id, 'brc_viagem_ida_data_dia', 	date('d-m-Y', $brc_viagem_ida_data));
						update_post_meta($masscad_product_id, 'brc_viagem_ida_data_hora', 	$_POST[$viagem_tipo.'masscad_ida_hora']);
						update_post_meta($masscad_product_id, 'brc_viagem_volta_data', 		$brc_viagem_volta_data);
						update_post_meta($masscad_product_id, 'brc_viagem_volta_data_dia', 	date('d-m-Y', $brc_viagem_volta_data));
						update_post_meta($masscad_product_id, 'brc_viagem_volta_data_hora', $_POST[$viagem_tipo.'masscad_volta_hora']);
						
						update_post_meta($masscad_product_id, 'brc_masscad_item', false);
					}
					
					$_title = $embarque_term_p->name.' -> '.$desembarque_term_p->name.' | ';
					if($_POST['brc_viagem_tipo_cadastro'] <= 1){
						$_title .= 'Ida: '.get_dia_semana($current_masscad['ida']['_diaSemana']).' ['.$current_masscad['ida']['_data'].'] - ';
						$_title .= 'Volta: '.get_dia_semana($current_masscad['volta']['_diaSemana']).' ['.$current_masscad['volta']['_data'].']';
					}else{
						$_title .= get_dia_semana($current_masscad['ida']['_diaSemana']).' ['.$current_masscad['ida']['_data'].']';
					}
					
					wp_update_post(array(
						'ID'           	=> $post_ID,
						'post_title'   	=> $_title,
						'meta_input' 	=> array(
							'brc_masscad_item' => true,
						),
					));
					
					$brc_viagem_ida_data = date('d-m-Y', $current_masscad['ida']['_dataTimestamp']).' '.$_POST[$viagem_tipo.'masscad_ida_hora'];
					$brc_viagem_ida_data = strtotime($brc_viagem_ida_data);
					$brc_viagem_volta_data = date('d-m-Y', $current_masscad['volta']['_dataTimestamp']).' '.$_POST[$viagem_tipo.'masscad_volta_hora'];
					$brc_viagem_volta_data = strtotime($brc_viagem_volta_data);
					update_post_meta($post_ID, 'brc_viagem_ida_data', 			$brc_viagem_ida_data);
					update_post_meta($post_ID, 'brc_viagem_ida_data_dia', 		date('d-m-Y', $brc_viagem_ida_data));
					update_post_meta($post_ID, 'brc_viagem_ida_data_hora', 		$_POST[$viagem_tipo.'masscad_ida_hora']);
					update_post_meta($post_ID, 'brc_viagem_volta_data', 		$brc_viagem_volta_data);
					update_post_meta($post_ID, 'brc_viagem_volta_data_dia', 	date('d-m-Y', $brc_viagem_volta_data));
					update_post_meta($post_ID, 'brc_viagem_volta_data_hora', 	$_POST[$viagem_tipo.'masscad_volta_hora']);
					
					update_post_meta($post_ID, 'brc_masscad_item', false);
				}
			}
			else{ 
				// CADASTRO ÚNICO
				$brc_viagem_ida_data = strtotime($_POST[$viagem_tipo.'unique_ida_dia'].' '.$_POST[$viagem_tipo.'unique_ida_hora']);
				$brc_viagem_volta_data = strtotime($_POST[$viagem_tipo.'unique_volta_dia'].' '.$_POST[$viagem_tipo.'unique_volta_hora']);
				update_post_meta($post_ID, 'brc_viagem_ida_data', $brc_viagem_ida_data);
				update_post_meta($post_ID, 'brc_viagem_ida_data_dia', $_POST[$viagem_tipo.'unique_ida_dia']);
				update_post_meta($post_ID, 'brc_viagem_ida_data_hora', $_POST[$viagem_tipo.'unique_ida_hora']);
				update_post_meta($post_ID, 'brc_viagem_volta_data', $brc_viagem_volta_data);
				update_post_meta($post_ID, 'brc_viagem_volta_data_dia', $_POST[$viagem_tipo.'unique_volta_dia']);
				update_post_meta($post_ID, 'brc_viagem_volta_data_hora', $_POST[$viagem_tipo.'unique_volta_hora']);
			}
			return false;
		}
		
		/**
		 * Edit and save quartos
		 * Post Type: product: excursoes
		 * 
		 */
		public function brc_tb_distribuir_quartos(){
			include FEPA_PLUGIN_DIR . '/templates/brc_admin.product_excursao_distribuir_quartos.php';
			exit;
		}
		public function brc_distribuir_quartos_save(){
			$ret['sts'] = true;
			$ret['post'] = $_POST;
			
			if(isset($_POST['quarto_nome'])){
				foreach($_POST['quarto_nome'] as $k => $j){
					update_post_meta($k, 'brc_excursao_variations_quartos_numeros', $j);
				}
			}
			if(isset($_POST['pessoa'])){
				foreach($_POST['pessoa'] as $k => $j){
					if($j == 'false'){
						wc_delete_order_item_meta($k, '_quarto_numero');
					}else{
						wc_update_order_item_meta($k, '_quarto_numero', $j);
					}
				}
			}
			echo json_encode($ret);
			exit;
		}
		
		/**
		 * Edit and save assentos
		 * Post Type: product: viagens/excursoes
		 * 
		 */
		public function brc_tb_distribuir_assentos(){
			$product_id = $_GET['product_id'];
			$product = wc_get_product($product_id);
			
			if($product){
				$brc_excursao = get_post_meta($product_id, 'brc_excursao', true);
				if($brc_excursao){
					include FEPA_PLUGIN_DIR . '/templates/brc_admin.product_distribuir_assentos_excursoes.php';
				}else{
					include FEPA_PLUGIN_DIR . '/templates/brc_admin.product_distribuir_assentos_viagens.php';
				}
			}else{
				include FEPA_PLUGIN_DIR . '/templates/brc_admin.tb_error.php';
			}
			exit;
		}
		public function brc_distribuir_assentos_save(){
			$ret['sts'] = true;
			$ret['post'] = $_POST;
			
			// ESPECIAL PARA EXCURSÕES
			if(isset($_POST['product_id'])){
				$brc_excursao = get_post_meta($_POST['product_id'], 'brc_excursao', true);
				if($brc_excursao){
					$ret['brc_excursao'] = $brc_excursao;
					
					foreach($_POST['pv'] as $order_item_id => $hospede){ // pa pv
						$_hospedes[$order_item_id] = wc_get_order_item_meta($order_item_id, '_hospedes', true);
						
						foreach($hospede as $k => $j){
							if($j == 'false'){
								unset($_hospedes[$order_item_id][$k]['veiculo']);
								unset($_hospedes[$order_item_id][$k]['assento']);
								wc_update_order_item_meta($order_item_id, '_hospedes', $_hospedes[$order_item_id]);
							}else{
								$_hospedes[$order_item_id][$k]['veiculo'] = $_POST['pv'][$order_item_id][$k];
								$_hospedes[$order_item_id][$k]['assento'] = $_POST['pa'][$order_item_id][$k];
								wc_update_order_item_meta($order_item_id, '_hospedes', $_hospedes[$order_item_id]);
							}
						}
					}
					
					echo json_encode($ret);
					exit;
				}
			}
			
			if(isset($_POST['pessoa_veiculo'])){
				foreach($_POST['pessoa_veiculo'] as $k => $j){
					if($j == 'false'){
						wc_delete_order_item_meta($k, '_veiculo');
						wc_delete_order_item_meta($k, '_assento');
					}else{
						wc_update_order_item_meta($k, '_veiculo', $_POST['pessoa_veiculo'][$k]);
						wc_update_order_item_meta($k, '_assento', $_POST['pessoa_assento'][$k]);
					}
				}
			}
			
			echo json_encode($ret);
			exit;
		}
		
		
		// .FEPAPostTypeSavingProduct
	}
?>