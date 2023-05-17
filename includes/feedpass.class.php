<?php
	include get_template_directory() . '/includes/instant-payment-receipts-in-woocommerce-for-bacs/instant-woo-bacs-payment-receipt.php';
	include get_template_directory() . '/includes/brcpasstour.inits.php';
	include get_template_directory() . '/includes/brcpasstour.widget-areas.php';
	include get_template_directory() . '/includes/brcpasstour.widgets-loader.php';
	include get_template_directory() . '/includes/brcpasstour.js_composer.php';
	
	if(!class_exists('BRCPASSTOUR')){ // brcpasstour
		class BRCPASSTOUR{
			
			protected static $instance = null;
			
			function __construct(){
				global $brcpasstour_theme, $brcpasstour_notification, $brcpasstour_cupons;
				
				define('BRC_ASSETS', get_template_directory().'/assets');
				define('BRC_ASSETS_ROOT', get_template_directory_uri().'/assets');
				define('BRC_TEMPLATES_URL', get_template_directory().'/brc-templates');
				define('BRC_INCLUDES_URL', get_template_directory().'/includes');
				
				$this->includes();
				$this->theme_steup();
				
				$brcpasstour_theme = new BRCPASSTOUR_THEME();
				$brcpasstour_notification = new BRCPASSTOUR_NOTIFICATION();
				$brcpasstour_cupons = new BRCPASSTOUR_CUPONS();
				
				//------------------------------------------------
				// ADMIN
				// [ADMIN] SAVE PRODUCTS
				add_action('save_post', array($this, 'brc_save_post_product'), 100, 3);

				// [AJAX] ADD VARIATION PARA EXCURSÃO
				add_action('wp_ajax_nopriv_brc_add_product_excursao_add_variation', 	array($this, 'brc_add_product_excursao_add_variation'));
				add_action('wp_ajax_brc_add_product_excursao_add_variation', 			array($this, 'brc_add_product_excursao_add_variation'));
				add_action('wp_ajax_nopriv_brc_add_product_excursao_remove_variation', 	array($this, 'brc_add_product_excursao_remove_variation'));
				add_action('wp_ajax_brc_add_product_excursao_remove_variation', 		array($this, 'brc_add_product_excursao_remove_variation'));

				// [AJAX] PRODUTO EDITAR QUARTOS
				add_action('wp_ajax_nopriv_brc_tb_distribuir_quartos', 					array($this, 'brc_tb_distribuir_quartos'));
				add_action('wp_ajax_brc_tb_distribuir_quartos', 						array($this, 'brc_tb_distribuir_quartos'));
				add_action('wp_ajax_nopriv_brc_distribuir_quartos_save', 				array($this, 'brc_distribuir_quartos_save'));
				add_action('wp_ajax_brc_distribuir_quartos_save', 						array($this, 'brc_distribuir_quartos_save'));
				add_action('wp_ajax_nopriv_brc_tb_distribuir_assentos', 				array($this, 'brc_tb_distribuir_assentos'));
				add_action('wp_ajax_brc_tb_distribuir_assentos', 						array($this, 'brc_tb_distribuir_assentos'));
				add_action('wp_ajax_nopriv_brc_distribuir_assentos_save', 				array($this, 'brc_distribuir_assentos_save'));
				add_action('wp_ajax_brc_distribuir_assentos_save', 						array($this, 'brc_distribuir_assentos_save'));

				// [AJAX] TB SHOW ORDER
				add_action('wp_ajax_nopriv_brc_admin_order_edit_comprovante_show', 		array($this, 'brc_admin_order_edit_comprovante_show'));
				add_action('wp_ajax_brc_admin_order_edit_comprovante_show', 			array($this, 'brc_admin_order_edit_comprovante_show'));

				// [AJAX] ORDER EDIT/ADD
				add_action('wp_ajax_nopriv_brc_order_produto_excursao', 				array($this, 'brc_order_produto_excursao'));
				add_action('wp_ajax_brc_order_produto_excursao', 						array($this, 'brc_order_produto_excursao'));
				add_action('wp_ajax_nopriv_brc_order_produto_add_hospede', 				array($this, 'brc_order_produto_add_hospede'));
				add_action('wp_ajax_brc_order_produto_add_hospede', 					array($this, 'brc_order_produto_add_hospede'));
				add_action('wp_ajax_nopriv_brc_order_produto_viagem', 					array($this, 'brc_order_produto_viagem'));
				add_action('wp_ajax_brc_order_produto_viagem', 							array($this, 'brc_order_produto_viagem'));
				add_action('wp_ajax_nopriv_brc_order_produto_add_passageiros', 			array($this, 'brc_order_produto_add_passageiros'));
				add_action('wp_ajax_brc_order_produto_add_passageiros', 				array($this, 'brc_order_produto_add_passageiros'));
				add_action('wp_ajax_nopriv_brc_admin_order_edit_submit', 				array($this, 'brc_admin_order_edit_submit'));
				add_action('wp_ajax_brc_admin_order_edit_submit', 						array($this, 'brc_admin_order_edit_submit'));
				add_action('wp_ajax_nopriv_brc_admin_order_edit_overwrite', 			array($this, 'brc_admin_order_edit_overwrite'));
				add_action('wp_ajax_brc_admin_order_edit_overwrite', 					array($this, 'brc_admin_order_edit_overwrite'));

				// SAVE ORDERS CREATE
				add_action('save_post', array($this, 'brc_save_post_shop_order'), 100, 3);
				
				// BULK CANCELAR PEDIDOS
				add_filter( 'handle_bulk_actions-edit-shop_order', 						array($this, 'brc_order_list_bulk_cancelar'), 10, 3 );
				add_action('wp_ajax_nopriv_brc_order_list_cancelar', 					array($this, 'brc_order_list_cancelar'));
				add_action('wp_ajax_brc_order_list_cancelar', 							array($this, 'brc_order_list_cancelar'));
				// EDITA VIAGEM AO MUDAR STATUS DO PEDIDO PARA CANCELADO
				add_action('woocommerce_order_status_cancelled', 						array($this, 'brc_order_cancelled'), 10, 1);
				
				// ADICIONA CUPOM PARA COMPRAS COM PAGAMENTO PROCESSADO
				add_action('woocommerce_order_status_changed', 							array($this, 'brc_order_add_cupon'), 10, 4);
				
				
				//------------------------------------------------
				//------------------------------------------------
				// FRONT-END
				// [AJAX] EXCURSÕES SINGLE
				add_action('wp_ajax_nopriv_brc_excursao_add_hospede', 	array($this, 'brc_excursao_add_hospede'));
				add_action('wp_ajax_brc_excursao_add_hospede', 			array($this, 'brc_excursao_add_hospede'));
				add_action('wp_ajax_nopriv_brc_comprar_excursao', 		array($this, 'brc_comprar_excursao'));
				add_action('wp_ajax_brc_comprar_excursao', 				array($this, 'brc_comprar_excursao'));
				add_action('woocommerce_before_calculate_totals', 		array($this, 'brc_calculate_ingresso_in_price'), 99);

				// [AJAX] VIAGENS
				add_action('wp_ajax_nopriv_brc_viagem_detalhes', 			array($this, 'brc_viagem_detalhes'));
				add_action('wp_ajax_brc_viagem_detalhes', 					array($this, 'brc_viagem_detalhes'));
				add_action('wp_ajax_nopriv_brc_viagem_add_passageiro', 		array($this, 'brc_viagem_add_passageiro'));
				add_action('wp_ajax_brc_viagem_add_passageiro', 			array($this, 'brc_viagem_add_passageiro'));
				add_action('wp_ajax_nopriv_brc_viagem_enviar', 				array($this, 'brc_viagem_enviar'));
				add_action('wp_ajax_brc_viagem_enviar', 					array($this, 'brc_viagem_enviar'));

				// [AJAX] CHECKTOU
				add_action('wp_ajax_nopriv_brc_remove_cart_item', 			array($this, 'brc_remove_cart_item'));
				add_action('wp_ajax_brc_remove_cart_item', 					array($this, 'brc_remove_cart_item'));

				// ADICIONA DADOS FINAIS APÓS PEDIDO FEITO
				add_action('woocommerce_checkout_create_order_line_item', array($this, 'brc_new_order'), 10, 4);
				add_action('woocommerce_thankyou', array($this, 'brc_new_order_thankyou'), 10, 1);
			}
			
			// SETUP
			static function init(){
				if(null == self::$instance ){
					self::$instance = new self;
				}
				return self::$instance;
			}
			
			// INCLUDE DE ARQUIVOS
			function includes(){
				global $brcthemes;
				include BRC_INCLUDES_URL . '/brcpasstour.functions.php';
				include BRC_INCLUDES_URL . '/brcpasstour.declaracoes.php';
				include BRC_INCLUDES_URL . '/brcpasstour.themeclass.php';
				
				$brcthemes = new BRCPASSTOUR_THEME();
				
				include BRC_INCLUDES_URL 	. '/brcpasstour.woocommerce-modify.php';
				include BRC_INCLUDES_URL 	. '/brcpasstour.woocommerce-notification.php';
				include BRC_INCLUDES_URL 	. '/brcpasstour.woocommerce-cupons.php';
				include BRC_INCLUDES_URL 	. '/brcpasstour.woocommerce-modify-product-edit.php';
				include BRC_INCLUDES_URL 	. '/brcpasstour.woocommerce-modify-order-edit.php';
				include BRC_INCLUDES_URL 	. '/brcpasstour.woocommerce-modify-tax-edit.php';
				include BRC_INCLUDES_URL 	. '/brcpasstour.woocommerce-modify-checkout.php';
				include BRC_INCLUDES_URL 	. '/brcpasstour.woocommerce-modify-myaccount.php';
				
				
				//include BRC_INCLUDES_URL 	. '/includes/brcpasstour.woocommerce-modify-user-edit.php';
			}
			
			// THEME SETUP
			function theme_steup(){
				add_image_size('brc_feed_default', 412, 250, true);
				add_image_size('brc_admin_menu_icon', 20, 20, false);
			}
			
			//------------------------------------------------
			// ADMIN
			// [ADMIN] SAVE PRODUCTS
			public function brc_save_post_product($post_ID, $post, $update){
				$brc_excursao = (isset($_POST['brc_excursao']) and $_POST['brc_excursao']=='yes');
				$metatype_excursoes = (isset($_GET['metatype']) and $_GET['metatype']=='excursoes');
				
				
				// ADICIONA MARCADOR DE EXCURSÃO PARA PRODUTO
				if(
					$post->post_status == 'auto-draft' or 
					$post->post_status == 'draft'
				){
					if($post->post_type == 'product'){
						if($metatype_excursoes){
							update_post_meta($post_ID, 'brc_excursao', true);
						}else{
							update_post_meta($post_ID, 'brc_excursao', false);
						}
					}
					
					// PREVINE O PROCESSO DE CONTINUAR
					return false;
				}
				
				// DIVIDE PROCESSO DE SALVAR VIAGENS / EXCURSÕES
				if($post->post_type == 'product'){
					$brc_masscad_item = get_post_meta($post_ID, 'brc_masscad_item', true);
					if(!$brc_masscad_item){
						$brc_excursao = get_post_meta($post_ID, 'brc_excursao', true);
						if($brc_excursao){
							
							// EXCURSÕES
							$this->brc_save_post_product_excursao($post_ID, $post, $update);
						}else{
							
							// VIAGENS
							$this->brc_save_post_product_viagens($post_ID, $post, $update);
						}
					}
				}
			}
			public function brc_save_post_product_excursao($post_ID, $post, $update){
				$product = wc_get_product($post_ID);
				
				// PRODUCT TYPE
				update_post_meta($post_ID, '_virtual', 'yes');
				
				$brc_excursao_data = strtotime($_POST['brc_excursao_data_dia'].' '.$_POST['brc_excursao_data_hora']);
				update_post_meta($post_ID, 'brc_excursao_data', $brc_excursao_data);
				update_post_meta($post_ID, 'brc_excursao_data_dia', $_POST['brc_excursao_data_dia']);
				update_post_meta($post_ID, 'brc_excursao_data_hora', $_POST['brc_excursao_data_hora']);
				update_post_meta($post_ID, 'brc_excursao_tempo_viagem', $_POST['brc_excursao_tempo_viagem']);
				update_post_meta($post_ID, 'brc_excursao_ingresso', $_POST['brc_excursao_ingresso']);
				update_post_meta($post_ID, 'brc_excursao_ingresso_stock', $_POST['brc_excursao_ingresso_stock']);
				update_post_meta($post_ID, 'brc_excursao_ingresso_preco', $_POST['brc_excursao_ingresso_preco']);
				update_post_meta($post_ID, 'brc_excursao_quant_veiculos', $_POST['brc_excursao_quant_veiculos']);
				update_post_meta($post_ID, 'brc_excursao_quant_assentos', $_POST['brc_excursao_quant_assentos']);
				
				update_post_meta($post_ID, 'brc_excursao_variations_hotel', $_POST['brc_excursao_variations_hotel']);
				update_post_meta($post_ID, 'brc_excursao_variations_nome', $_POST['brc_excursao_variations_nome']);
				update_post_meta($post_ID, 'brc_excursao_variations_stock', $_POST['brc_excursao_variations_stock']);
				update_post_meta($post_ID, 'brc_excursao_variations_stock_total', $_POST['brc_excursao_variations_stock_total']);
				update_post_meta($post_ID, 'brc_excursao_variations_pquarto', $_POST['brc_excursao_variations_pquarto']);
				wp_set_post_terms($post_ID, 'variable', 'product_type', false);
				
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
						update_post_meta($variation_id, 'brc_excursao_variations_stock_total', $_POST['brc_excursao_variations_stock_total'][$k]);
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
			public function brc_save_post_product_viagens($post_ID, $post, $update){
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
					wp_set_object_terms($post_ID, $brc_viagem_linha, 'brc_linhas', false);
				}else{
					$linha = wp_get_post_terms($post_ID, 'brc_linhas');
					if(!is_wp_error($linha)){
						$linha = $linha[0];
						wp_remove_object_terms($post_ID, $linha->term_id, 'brc_linhas');
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
						// DEBUG
						// update_post_meta($post_ID, 'assentos', $assentos);
						// update_post_meta($post_ID, 'mult', $mult);
						// update_post_meta($post_ID, 'parts', $parts);
						// update_post_meta($post_ID, 'ftotal', $ftotal);
						// update_post_meta($post_ID, 'fassentos', $fassentos);
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
			
			// [AJAX] ADD VARIATION PARA EXCURSÃO
			public function brc_add_product_excursao_add_variation(){
				ob_start();
				include BRC_TEMPLATES_URL .'/brc_admin.product_excursao_field_variation.php';
				$html = ob_get_contents();
				ob_end_clean();
				
				$ret['html'] = $html;
				$ret['sts'] = true;
				
				echo json_encode($ret);
				exit;
			}
			public function brc_add_product_excursao_remove_variation(){
				$ret = array('sts' => false);
				
				if(isset($_POST['varid'])){
					global $wpdb;
					
					$varid = $_POST['varid'];
					$vari = get_post($varid);
					
					if($vari){
						$ret['sts'] = true;
						$del = wp_delete_post($varid, true);
						$wpdb->query("DELETE FROM ".$wpdb->prefix."wc_product_meta_lookup WHERE product_id = ". $varid ."");
					}
				}
				
				echo json_encode($ret);
				exit;
			}
			
			// [AJAX] PRODUTO EDITAR QUARTOS
			public function brc_tb_distribuir_quartos(){
				include BRC_TEMPLATES_URL.'/brc_admin.product_excursao_distribuir_quartos.php';
				exit;
			}
			public function brc_distribuir_quartos_save(){
				$ret['sts'] = true;
				$ret['post'] = $_POST;
				
				if(isset($_POST['quarto_nome'])){ // quarto_nome[293][2]
					foreach($_POST['quarto_nome'] as $k => $j){
						update_post_meta($k, 'brc_excursao_variations_quartos_numeros', $j);
					}
				}
				if(isset($_POST['pessoa'])){ // pessoa[29]
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
			public function brc_tb_distribuir_assentos(){
				$product_id = $_GET['product_id'];
				$product = wc_get_product($product_id);
				
				if($product){
					$brc_excursao = get_post_meta($product_id, 'brc_excursao', true);
					if($brc_excursao){
						include BRC_TEMPLATES_URL.'/brc_admin.product_distribuir_assentos_excursoes.php';
					}else{
						include BRC_TEMPLATES_URL.'/brc_admin.product_distribuir_assentos_viagens.php';
					}
				}else{
					include BRC_TEMPLATES_URL.'/brc_admin.tb_error.php';
				}
				exit;
			}
			public function brc_distribuir_assentos_save(){
				$ret['sts'] = true;
				$ret['post'] = $_POST;
				
				if(isset($_POST['pessoa_veiculo'])){ // pessoa_veiculo[33]
					foreach($_POST['pessoa_veiculo'] as $k => $j){
						if($j == 'false'){
							wc_delete_order_item_meta($k, '_veiculo');
							wc_delete_order_item_meta($k, '_assento');
						}else{
							wc_update_order_item_meta($k, '_veiculo', $_POST['pessoa_veiculo'][$k]);
							wc_update_order_item_meta($k, '_assento', $_POST['pessoa_assento'][$k]);
						}
					}
					refresh_viagem_assentos($_POST['product_id']);
				}
				
				echo json_encode($ret);
				exit;
			}
			
			// [AJAX] TB SHOW ORDER
			public function brc_admin_order_edit_comprovante_show(){
				$order_id 		= $_GET['order_id'];
				$order_post 	= get_post($order_id);
				if($order_post){
					$order = new WC_Order($order_post->ID);
					$recip = get_post_meta($order_post->ID, 'woo-bacs-recipt', true);
					
					$img_types = array('png', 'jpg', 'jpeg');
					$recipt_url = $recip;
					
					$recipt_url_array = explode('.', $recipt_url);
					$recipt_url_name_array = explode('/', $recipt_url);
					$extensao = end($recipt_url_array);
					$name = end($recipt_url_name_array);
					
					if($recip){
						
						if(in_array($extensao, $img_types)){
							echo '<div id="comprovante_show" class="text-center">';
							echo '<img src="'. $recip .'" alt="Comprovante de depósito #'. $order_post->ID .'" />';
							echo '</div>';
						}else{
							echo '<iframe src="'.$recip.'" id="comprovante_show" class="text-center" style="width:100%;height:99%;"></iframe>';
						}
					}else{
						echo '<h3 style="color:#d28a00;">Comprovante não enviado</h3>';
					}
				}
				exit;
			}
			
			// [AJAX] ORDER EDIT/ADD
			public function brc_order_produto_excursao(){
				$ret['sts'] = false;
				
				if(isset($_POST['product_id'])){
					$product_id = $_POST['product_id'];
					$product_post = get_post($product_id);
					
					if($product_post){
						$product = wc_get_product($product_id);
						$quartos = $product->get_available_variations();
						
						if($quartos){
							ob_start();
							include BRC_TEMPLATES_URL .'/brc_admin.order_add_excursao_vars.php';
							$html = ob_get_contents();
							ob_end_clean();
							
							$ret['html'] = $html;
							$ret['sts'] = true;
						}
					}
				}
				
				echo json_encode($ret);
				exit;
			}
			public function brc_order_produto_add_hospede(){
				$ret['sts'] = false;
				
				if(isset($_POST['product_id'])){
					$product_id = $_POST['product_id'];
					$product_post = get_post($product_id);
					
					if($product_post){
						$product = wc_get_product($product_id);
						
						$var_id = $_POST['var_id'];
						$variation = new WC_Product_Variation($var_id);
						$variation_data = $variation->get_data(); //regular_price
						
						$brc_excursao_ingresso = get_post_meta($product_id, 'brc_excursao_ingresso', true);
						
						if($variation_data['id'] > 0){
							if($brc_excursao_ingresso){
								$brc_excursao_ingresso_stock = get_post_meta($product_id, 'brc_excursao_ingresso_stock', true);
								$brc_excursao_ingresso_preco = get_post_meta($product_id, 'brc_excursao_ingresso_preco', true);
								if($brc_excursao_ingresso_stock < 1){
									$brc_excursao_ingresso = false;
								}
							}
							
							$brc_excursao_variations_hotel = get_post_meta($variation_data['id'], 'brc_excursao_variations_hotel', true);
							$brc_excursao_variations_nome = get_post_meta($variation_data['id'], 'brc_excursao_variations_nome', true);
						
							
							ob_start();
							include BRC_TEMPLATES_URL .'/brc_admin.order_add_excursao_hospedes.php';
							$html = ob_get_contents();
							ob_end_clean();
							
							$ret['html'] = $html;
							$ret['sts'] = true;
						}
					}
				}
				
				echo json_encode($ret);
				exit;
			}
			public function brc_order_produto_viagem(){
				$ret['sts'] = false;
				
				if(isset($_POST['product_id'])){
					$product_id = $_POST['product_id'];
					$product_post = get_post($product_id);
					
					if($product_post){
						$product = wc_get_product($product_id);
						
						ob_start();
						include BRC_TEMPLATES_URL .'/brc_admin.order_add_viagem_table.php';
						$html = ob_get_contents();
						ob_end_clean();
						
						$ret['html'] = $html;
						$ret['sts'] = true;
					}
				}
				
				echo json_encode($ret);
				exit;
			}
			public function brc_order_produto_add_passageiros(){
				$ret['sts'] = false;
				
				if(isset($_POST['product_id'])){
					$product_id = $_POST['product_id'];
					$product_post = get_post($product_id);
					
					if($product_post){
						$product = wc_get_product($product_id);
						$_stock = get_post_meta($product_id, '_stock', true);
						
						if($_stock > 0){
							ob_start();
							include BRC_TEMPLATES_URL .'/brc_admin.order_add_viagem_passageiros.php';
							$html = ob_get_contents();
							ob_end_clean();
							
							$ret['html'] = $html;
							$ret['sts'] = true;
						}
					}
				}
				
				echo json_encode($ret);
				exit;
			}
			public function brc_admin_order_edit_submit(){
				$ret['sts'] = false;
				$obr = array(
					'brc_order_excursao',
					'brc_order_cliente',
					'brc_order_emissao_data',
					'brc_order_emissao_hora',
					'brc_order_pagamento',
					'brc_order_pagamento_status',
				);
				
				if($_POST['brc_order_excursao'] == 'excursao')
					$obr[] = 'brc_order_produto_excursao';
				
				if($_POST['brc_order_excursao'] == 'viagem')
					$obr[] = 'brc_order_produto_viagem';
				
				
				$verf = camposFormVerf($obr, $_POST);
				if($verf){
					
					$ret['sts'] = false;
					$ret['error'] = 'dados-pedido';
					$ret['mensagem'][] = 'Por favor preencha corretamente os "Dados do Pedido"';
					$ret['verf'] = $verf;
					
				}
				else{
					$ret['sts'] = true;
					
					if($_POST['brc_order_excursao'] == 'excursao'){
						if(isset($_POST['brc_order_produto_excursao']) and !empty($_POST['brc_order_produto_excursao'])){
							$ret['EXCURSAO'] = true;
							
							if(isset($_POST['brc_order_produto_var_id'])){
								foreach($_POST['brc_order_produto_var_id'] as $item => $var_id){
									if($var_id){
										$var_nome = $_POST['brc_order_produto_var_nome'][$item];
										// _hopede_nome
										if(empty($_POST['_hopede_nome'][$item])){
											$ret['sts'] = false;
											$ret['erro'][] = 'nome-invalido';
											$ret['mensagem'][] = '['.$var_nome.'] Por favor informe um nome válidos para o hóspede '. ($item+1) .'.';
										}
										
										// _hopede_cpf
										if(!validaCPF($_POST['_hopede_cpf'][$item])){
											$ret['sts'] = false;
											$ret['erro'][] = 'cpf-invalido';
											$ret['mensagem'][] = '['.$var_nome.'] Por favor informe CPF válidos para o hóspede '. ($item+1) .'.';
										}else{
											
											$cpfs[] = $_POST['_hopede_cpf'][$item];
										}
									}
								}
								// CPF DUPLICADO
								if(has_dupes($cpfs)){
									$ret['sts'] = false;
									$ret['cpfs'] = $cpfs;
									$ret['erro'][] = 'has_dupes-cpf';
									$ret['mensagem'][] = 'Por favor informe CPF\'s diferentes para cada hóspede.';
								}
							}else{
								$ret['sts'] = false;
								$ret['erro'][] = 'sel-var';
								$ret['mensagem'][] = 'Por favor adicione hóspedes ao pedido.';
							}
						}else{
							$ret['sts'] = false;
							$ret['erro'][] = 'sel-produto';
							$ret['mensagem'][] = 'Por favor selecione uma excursão.';
						}
					}
					elseif($_POST['brc_order_excursao'] == 'viagem'){
						if(isset($_POST['brc_order_produto_viagem']) and !empty($_POST['brc_order_produto_viagem'])){
							$ret['VIAGEM'] = true;
							
							if(isset($_POST['brc_order_produto_passageiro'])){
								foreach($_POST['brc_order_produto_passageiro'] as $item => $product_id){
									if($product_id){
										
										// _passageiro_nome
										if(empty($_POST['_passageiro_nome'][$item])){
											$ret['sts'] = false;
											$ret['erro'][] = 'nome-invalido';
											$ret['mensagem'][] = 'Por favor informe um nome válidos para o passageiro '. ($item+1) .'.';
										}
										
										// _passageiro_cpf
										if(!validaCPF($_POST['_passageiro_cpf'][$item])){
											$ret['sts'] = false;
											$ret['erro'][] = 'cpf-invalido';
											$ret['mensagem'][] = 'Por favor informe CPF válidos para o passageiro '. ($item+1) .'.';
										}else{
											
											$cpfs[] = $_POST['_passageiro_cpf'][$item];
										}
									}
								}
								// CPF DUPLICADO
								if(has_dupes($cpfs)){
									$ret['sts'] = false;
									$ret['cpfs'] = $cpfs;
									$ret['erro'][] = 'has_dupes-cpf';
									$ret['mensagem'][] = 'Por favor informe CPF\'s diferentes para cada passageiro.';
								}
							}else{
								$ret['sts'] = false;
								$ret['erro'][] = 'sel-var';
								$ret['mensagem'][] = 'Por favor adicione passageiros ao pedido.';
							}
						}else{
							$ret['sts'] = false;
							$ret['erro'][] = 'sel-produto';
							$ret['mensagem'][] = 'Por favor selecione uma viagem.';
						}
					}
				}
				
				echo json_encode($ret);
				exit;
			}
			public function brc_admin_order_edit_overwrite(){
				$ret['sts'] = false;
				$obr = array(
					'brc_order_cliente',
					'brc_order_emissao_data',
					'brc_order_emissao_hora',
					'brc_order_pagamento',
					'brc_order_pagamento_status',
				);
				$verf = camposFormVerf($obr, $_POST);
				if($verf){
					
					$ret['sts'] = false;
					$ret['error'] = 'dados-pedido';
					$ret['mensagem'][] = 'Por favor preencha corretamente os "Dados do Pedido"';
					$ret['verf'] = $verf;
					
				}else{
					$ret['sts'] = true;
				}
				
				echo json_encode($ret);
				exit;
			}
			
			// SAVE ORDERS CREATE
			public function brc_save_post_shop_order($post_ID, $post, $update){ // NOT AJAX
				if($post->post_type == 'shop_order'){
					global $wp_roles, $woocommerce, $wpdb;
					
					// CRIAÇÃO
					if($post->post_status == 'auto-draft' or $post->post_status == 'draft'){
						if(isset($_POST['brc_order_cliente'])){
							
							$order = new WC_Order($post_ID);
							$new_date = strtotime($_POST['brc_order_emissao_data'].' '.$_POST['brc_order_emissao_hora']);
							$order_total = 0;
							
							$order->set_date_created(date('Y-m-d H:i:s', $new_date));
							$order->set_date_modified(date('Y-m-d H:i:s', $new_date));
							
							update_post_meta($post_ID, '_customer_user', $_POST['brc_order_cliente']);
							update_post_meta($post_ID, '_payment_method', $_POST['brc_order_pagamento']);
							update_post_meta($post_ID, '_payment_method_title', 'Pagamento via '. get_payment_methods_title($_POST['brc_order_pagamento']));
							
							if(!empty($_POST['brc_order_admin_tel']))
								update_post_meta($post_ID, '_order_admin_tel', $_POST['brc_order_admin_tel']);
							
							if($_POST['brc_order_excursao'] == 'excursao'){
								
								// ATIVA EXCURSAO
								update_post_meta($post_ID, 'brc_order_excursao', 1);
								
								if(!empty($_POST['brc_order_produto_var_id'])){
									foreach($_POST['brc_order_produto_var_id'] as $item => $var_id){
										
										$variation = new WC_Product_Variation($var_id);
										$var_data = $variation->get_data();
										$var_meta_data = $variation->get_meta_data();
										$var_meta_data_array = order_item_meta_to_array($var_meta_data);
										$var_stock = get_post_meta($var_id, '_stock', true);
										$item_total = $var_data['price'];
										$order_total  += $var_data['price'];
										
										$product_id = $var_data['parent_id'];
										$product = wc_get_product($product_id);
										$brc_excursao_data = get_post_meta($product_id, 'brc_excursao_data', true);
										$brc_excursao_ingresso_stock = get_post_meta($product_id, 'brc_excursao_ingresso_stock', true);
										$brc_excursao_ingresso_preco = get_post_meta($product_id, 'brc_excursao_ingresso_preco', true);
										
										$order_item_id = $order->add_product($product, 1, array(
											'variation_id' => $var_id,
											
										)); 
										wc_add_order_item_meta($order_item_id, 'Quarto', $var_meta_data_array['brc_excursao_variations_nome']);
										wc_add_order_item_meta($order_item_id, 'Hotel', get_the_title($var_meta_data_array['brc_excursao_variations_hotel']));
										wc_add_order_item_meta($order_item_id, 'Data/hora', date('d/m/Y - H:i', $brc_excursao_data).'h');
										wc_add_order_item_meta($order_item_id, 'Hópede', $_POST['_hopede_nome'][$item].' ('.$_POST['_hopede_cpf'][$item].')');
										
										wc_add_order_item_meta($order_item_id, '_brc_excursao', 1);
										wc_add_order_item_meta($order_item_id, '_hopede_nome', $_POST['_hopede_nome'][$item]);
										wc_add_order_item_meta($order_item_id, '_hopede_cpf', $_POST['_hopede_cpf'][$item]);
										wc_add_order_item_meta($order_item_id, '_variation_hotel', get_the_title($var_meta_data_array['brc_excursao_variations_hotel']));
										wc_add_order_item_meta($order_item_id, '_variation_hotel_id', $var_meta_data_array['brc_excursao_variations_hotel']);
										wc_add_order_item_meta($order_item_id, '_product_nome', get_the_title($var_data['parent_id']));
										wc_add_order_item_meta($order_item_id, '_product_data', $brc_excursao_data);
										
										// ABATE ESTOQUE
										update_post_meta($var_id, '_stock', ($var_stock-1));
										
										if($_POST['_ingresso'][$item] == 'yes'){
											$order_total  += $brc_excursao_ingresso_preco;
											$item_total += $brc_excursao_ingresso_preco;
											
											wc_add_order_item_meta($order_item_id, '_ingresso', 1);
											wc_add_order_item_meta($order_item_id, '_ingresso_price', $brc_excursao_ingresso_preco);
											wc_add_order_item_meta($order_item_id, '_variation_price', $var_data['price']);
											
											update_post_meta($product_id, 'brc_excursao_ingresso_stock', ($brc_excursao_ingresso_stock-1));
										}
										
										wc_update_order_item_meta($order_item_id, '_line_subtotal', $var_data['price']);
										wc_update_order_item_meta($order_item_id, '_line_total', $item_total);
										wc_update_order_item_meta($order_item_id, '_variation_price', $item_total);
									}
								}
							}
							else{
								// ATIVA EXCURSAO OFF
								update_post_meta($post_ID, 'brc_order_excursao', 0);
								
								if(!empty($_POST['brc_order_produto_passageiro'])){
									foreach($_POST['brc_order_produto_passageiro'] as $item => $product_id){
										
										$product = wc_get_product($product_id);
										$brc_viagem_ida_data 	= get_post_meta($product_id, 'brc_viagem_ida_data', true);
										$brc_viagem_volta_data	= get_post_meta($product_id, 'brc_viagem_volta_data', true);
										$brc_viagem_stock 		= get_post_meta($product_id, '_stock', true);
										
										// DESTINOS
										$embarque 				= get_post_meta($product_id, 'brc_viagem_origem', true);
										$desembarque 			= get_post_meta($product_id, 'brc_viagem_destino', true);
										$embarque_term 			= get_term($embarque, 'brc_destinos');
										$embarque_cidade 		= get_term($embarque_term->parent, 'brc_destinos');
										$desembarque_term 		= get_term($desembarque, 'brc_destinos');
										$desembarque_cidade 	= get_term($desembarque_term->parent, 'brc_destinos');
										
										$order_total  += $product->get_data()['price'];
										
										$order_item_id = $order->add_product($product, 1);
										// COPIADO DE ADICIONAR AO CARRINHO UMA VIAGEM
										$order_item_meta = array(
											'Data/hora Ida' 		=> date('d/m/Y', $brc_viagem_ida_data).' - '.date('H:i', $brc_viagem_ida_data).'h',
											'Origem Ida' 			=> $embarque_cidade->name. ' ('.$embarque_term->name.')',
											'Destino Ida' 			=> $desembarque_cidade->name. ' ('.$desembarque_term->name.')',
											/*--*/
											'Data/hora Volta' 		=> date('d/m/Y', $brc_viagem_volta_data).' - '.date('H:i', $brc_viagem_volta_data).'h',
											'Origem Volta' 			=> $desembarque_cidade->name. ' ('.$desembarque_term->name.')',
											'Destino Volta' 		=> $embarque_cidade->name. ' ('.$embarque_term->name.')',
											/*--*/
											'Passageiro' 			=> $_POST['_passageiro_nome'][$item].' ('.$_POST['_passageiro_cpf'][$item].')',
											/*--*/
											'_passageiro_nome' 		=> $_POST['_passageiro_nome'][$item],
											'_passageiro_cpf' 		=> $_POST['_passageiro_cpf'][$item],

											'_product_id' 			=> $product_id,
											'_product_nome' 		=> get_the_title($product_id),
											'_product_ida_data' 			=> $brc_viagem_ida_data,
											'_product_ida_origem' 			=> $embarque,
											'_product_ida_origem_cidade' 	=> $embarque_term->parent,
											'_product_ida_destino' 			=> $desembarque,
											'_product_ida_destino_cidade' 	=> $desembarque_term->parent,
											'_product_volta_data' 			=> $brc_viagem_volta_data,
											'_product_volta_origem' 		=> $desembarque,
											'_product_volta_origem_cidade' 	=> $desembarque_term->parent,
											'_product_volta_destino' 		=> $embarque,
											'_product_volta_destino_cidade' => $embarque_term->parent,
										);
										foreach($order_item_meta as $key => $val){
											wc_add_order_item_meta(
												$order_item_id, 
												$key, $val
											);
										}
										
										// ABATE ESTOQUE
										update_post_meta($product_id, '_stock', ($brc_viagem_stock-1));
									}
								}
							}
							
							$order->set_total($order_total);
							update_post_meta($post_ID, '_order_total', $order_total);
							
							// FORÇAR TROCAR O STATUS
							$wpdb->query("UPDATE $wpdb->posts SET post_status = '". $_POST['brc_order_pagamento_status'] ."' WHERE ID = $post_ID");
						}
					}
					else{
						if(isset($_POST['brc_order_cliente'])){
							
							$order 		= new WC_Order($post_ID);
							$new_date 	= strtotime($_POST['brc_order_emissao_data'].' '.$_POST['brc_order_emissao_hora']);
							
							$order->set_date_created(date('Y-m-d H:i:s', $new_date));
							$order->set_date_modified(date('Y-m-d H:i:s', $new_date));
							
							update_post_meta($post_ID, '_customer_user', $_POST['brc_order_cliente']);
							update_post_meta($post_ID, '_payment_method', $_POST['brc_order_pagamento']);
							update_post_meta($post_ID, '_payment_method_title', 'Pagamento via '. get_payment_methods_title($_POST['brc_order_pagamento']));
							
							if(!empty($_POST['brc_order_admin_tel']))
								update_post_meta($post_ID, '_order_admin_tel', $_POST['brc_order_admin_tel']);
							
							// FORÇAR TROCAR O STATUS
							$wpdb->query("UPDATE $wpdb->posts SET post_status = '". $_POST['brc_order_pagamento_status'] ."' WHERE ID = $post_ID");
							$st = str_replace('wc-', '', $_POST['brc_order_pagamento_status']);
							$order->update_status($st, '');
						}
					}
				}
			}
			
			// BULK CANCELAR PEDIDOS
			public function brc_order_list_cancelar(){
				$ret['sts'] = false;
				$orderid = $_POST['orderid'];
				$order = new WC_Order($orderid);
				
				if($order->get_status() != 'cancelled'){
					
					$order->set_status('wc-cancelled', 'Pedido cancelado', false);
					$order->save();
					$ret['sts'] = true;
					$ret['orderid'] = $orderid;
				}
				
				echo json_encode($ret);
				exit;
			}
			public function brc_order_list_bulk_cancelar($redirect_to, $doaction, $post_ids){
				if($doaction !== 'brc_order_list_bulk_cancelar'){
					return $redirect_to;
				}
				foreach($post_ids as $orderid){
					
					$order = new WC_Order($orderid);
					if($order->get_status() != 'cancelled'){
						$order->set_status('wc-cancelled', 'Pedido cancelado', true);
						$order->save();
						$cancelados[] = $orderid;
					}
				}
				$redirect_to = add_query_arg('cancel_order', count($cancelados), $redirect_to);
				return $redirect_to;
			}
			// EDITA VIAGEM AO MUDAR STATUS DO PEDIDO PARA CANCELADO
			public function brc_order_cancelled($postid){
				$post = get_post($postid);
				if($post->post_type == 'shop_order'){
					
					$order = new WC_Order($postid);
					
					foreach($order->get_items() as $item_id => $item){
						
						$product_id = $item->get_product_id();
						$_assento = wc_get_order_item_meta($item_id, '_assento', true);
						
						// ATUALIZA POLTRONAS DISPONÍVEIS
						$brc_assentos_disponiveis = get_post_meta($product_id, 'brc_assentos_disponiveis', true);
						$brc_assentos_disponiveis[$_assento] = true;
						
						update_post_meta($product_id, 'brc_assentos_disponiveis', $brc_assentos_disponiveis);
					}
				}
			}
			
			// ADICIONA CUPOM PARA COMPRAS COM PAGAMENTO PROCESSADO
			public function brc_order_add_cupon($this_get_id, $this_status_transition_from, $this_status_transition_to, $instance){
				if(
					$this_status_transition_to=='completed' or 
					$this_status_transition_to=='processing'
				){
					$order_id = $this_get_id;
					$order = new WC_Order($order_id);
					$order_user_id = $order->get_user_id();
					
					gerar_cupons($order_user_id, $order_id);
				}
			}
			
			
			//------------------------------------------------
			//------------------------------------------------
			// FRONT-END
			// [AJAX] EXCURSÕES SINGLE
			public function brc_excursao_add_hospede(){
				$ret['sts'] = false;
				
				if(isset($_POST['varid'])){
					$var_id = $_POST['varid'];
					$variation = new WC_Product_Variation($var_id);
					$variation_data = $variation->get_data(); //regular_price
					
					$post_id = $variation_data['parent_id'];
					$product = wc_get_product($post_id);
					$brc_excursao_ingresso = get_post_meta($post_id, 'brc_excursao_ingresso', true);
					
					if($variation_data['id'] > 0){
						if($brc_excursao_ingresso){
							$brc_excursao_ingresso_stock = get_post_meta($post_id, 'brc_excursao_ingresso_stock', true);
							$brc_excursao_ingresso_preco = get_post_meta($post_id, 'brc_excursao_ingresso_preco', true);
							if($brc_excursao_ingresso_stock < 1){
								$brc_excursao_ingresso = false;
							}
						}
						
						ob_start();
						include BRC_TEMPLATES_URL.'/brc_template.excursao-hospedes.php';
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
			public function brc_comprar_excursao(){
				global $woocommerce;
				
				$ret['sts'] = false;
				//$ret['post'] = $_POST;
				
				if(
					isset($_POST['variacao-item']) and
					isset($_POST['passageiro-nome']) and
					isset($_POST['passageiro-cpf'])
				){
					foreach($_POST['variacao-item'] as $k => $j){
						$kreal = $k+1;
						
						if(empty($_POST['passageiro-nome'][$k])){
							$ret['error']['mensagem'][] = 'Por favor informe o <strong>nome</strong> do <strong>HÓSPEDE '.$kreal.'</strong>';
						}
						if(empty($_POST['passageiro-cpf'][$k])){
							$ret['error']['mensagem'][] = 'Por favor informe o <strong>CPF</strong> do <strong>HÓSPEDE '.$kreal.'</strong>';
						}else{
							if(!validaCPF($_POST['passageiro-cpf'][$k])){
								$ret['error']['mensagem']['CPF'] = 'Informe um <strong>CPF</strong> único para cada Hóspede.';
							}
						}
						
						$verf_cpf[$_POST['passageiro-cpf'][$k]][] = $_POST['passageiro-cpf'][$k];
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
					if(!$ret['error']){
						$woocommerce->cart->empty_cart();
						
						foreach($_POST['variacao-item'] as $k => $j){
							// VARIATION
							$var_id 							= $j;
							$variation 							= new WC_Product_Variation($var_id);
							$variation_data 					= $variation->get_data();
							$brc_excursao_variations_hotel 		= get_post_meta($var_id, 'brc_excursao_variations_hotel', true);
							$brc_excursao_variations_nome 		= get_post_meta($var_id, 'brc_excursao_variations_nome', true);
							$brc_excursao_variations_pquarto 	= get_post_meta($var_id, 'brc_excursao_variations_pquarto', true);
							$attribute_quartos 					= get_post_meta($var_id, 'attribute_quartos', true);
							
							// PRODUCT
							$product_id 						= $variation_data['parent_id'];
							//$product 							= wc_get_product($product_id);
							$brc_excursao_ingresso 				= get_post_meta($product_id, 'brc_excursao_ingresso', true);
							$brc_excursao_ingresso_stock 		= get_post_meta($product_id, 'brc_excursao_ingresso_stock', true);
							$brc_excursao_ingresso_preco 		= get_post_meta($product_id, 'brc_excursao_ingresso_preco', true);
							$brc_excursao_data 					= get_post_meta($product_id, 'brc_excursao_data', true);
							$brc_prod_hospedagem_noites 		= get_post_meta($product_id, 'brc_prod_hospedagem_noites', true);
							
							$variation_view_args = array(
								'Quarto' 	=> ' '.$brc_excursao_variations_nome,
								'Hotel' 	=> get_the_title($brc_excursao_variations_hotel),
								'Data/hora' => date('d/m/Y', $brc_excursao_data).' - '.date('H:i', $brc_excursao_data).'h',
								'Noites' 	=> $brc_prod_hospedagem_noites,
								'Hópede' 	=> $_POST['passageiro-nome'][$k].' ('.$_POST['passageiro-cpf'][$k].')',
							);
							
							$variation_args = array(
								'_hopede_nome' 					=> $_POST['passageiro-nome'][$k],
								'_hopede_cpf' 					=> $_POST['passageiro-cpf'][$k],
								
								'_variation_quarto' 			=> $brc_excursao_variations_nome,
								'_variation_hotel' 				=> get_the_title($brc_excursao_variations_hotel),
								'_variation_hotel_id' 			=> $brc_excursao_variations_hotel,
								'_variation_price' 				=> $variation_data['regular_price'],
								
								'_product_id' 					=> $product_id,
								'_product_nome' 				=> get_the_title($product_id),
								'_product_noites' 				=> $brc_prod_hospedagem_noites,
								'_product_data' 				=> $brc_excursao_data,
							);
							if($_POST['add-ingresso-ver'][$k] == 'yes'){
								$variation_args['_ingresso'] = true;
								$variation_args['_ingresso_price'] = $brc_excursao_ingresso_preco;
								$variation_view_args['Ingresso'] = ' x1 R$'.moedaRealPrint($brc_excursao_ingresso_preco);
							}else{
								$variation_args['ingresso'] = false;
							}
							
							$ret['sts'] 			= true;
							$ret['redirec_url'] 	= FINALIZAR_RESERVA_URI;
							
							$woocommerce->cart->add_to_cart($product_id, 1, $var_id, $variation_view_args, $variation_args);
							$woocommerce->cart->calculate_totals();
							$woocommerce->cart->set_session();
							$woocommerce->cart->maybe_set_cart_cookies();
						}
					}
				}
				
				echo json_encode($ret);
				exit;
			}
			public function brc_calculate_ingresso_in_price($cart_object){
				if(!WC()->session->__isset( "reload_checkout" )){
					foreach($cart_object->cart_contents as $key => $value){
						if($value["_ingresso"]){
							$value['data']->set_price($value["_variation_price"]+$value["_ingresso_price"]);
						}
					}  
				}  
			}
			
			// [AJAX] VIAGENS
			public function brc_viagem_detalhes(){
				$ret['sts'] = false;
				
				if(isset($_POST['viagemid'])){
					$product = get_post($_POST['viagemid']);
					
					if($product){
						$post_ID = $product->ID;
						$tipo_cadastro = get_post_meta($post_ID, 'brc_viagem_tipo_cadastro', true);
						$_price = get_post_meta($post_ID, '_price', true);
						
						ob_start();
						include BRC_TEMPLATES_URL .'/brc_template.busca_viagens_detalhes.php';
						$html = ob_get_contents();
						ob_end_clean();
						
						ob_start();
						include BRC_TEMPLATES_URL .'/brc_template.busca_viagens_detalhes-lin-dados.php';
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
						include BRC_TEMPLATES_URL .'/brc_template.busca_viagens_detalhes-lin-dados.php';
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
				$selvolta = $_POST['selvolta']=='yes'?true:false;;
				$tipo_cadastro = get_post_meta($product_id, 'brc_viagem_tipo_cadastro', true);
				
				// LINHAS PONTOS DE PARADA
				$linha = wp_get_post_terms($product_id, 'brc_linhas');
				if(!is_wp_error($linha)){
					if($linha){
						$linha = $linha[0];
						$linha_pontos = get_term_meta($linha->term_id, 'linha_grupo_paradas', true);
					}else{
						$linha = false;
					}
				}else{
					$linha = false;
				}
				
				// VERIFICA SE TEM LINHA ANEXADA
				if($linha){
					if($_POST['ponto_embarque'] > 0){
						$ponto_embarque = intval($_POST['ponto_embarque'])-1;
					}else{
						$ret['error']['mensagem'][] = 'Por favor informe o <strong>Ponto de embarque</strong>';
						echo json_encode($ret);
						exit;
					}
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
								$ret['error']['mensagem']['CPF'] = 'Informe um <strong>CPF</strong> único para cada passageiro.';
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
						$product 				= wc_get_product($product_id);
						$brc_viagem_ida_data 	= get_post_meta($product_id, 'brc_viagem_ida_data', true);
						$brc_viagem_volta_data	= get_post_meta($product_id, 'brc_viagem_volta_data', true);
						
						// DESTINOS
						$viagem_tempo 			= get_post_meta($product_id, 'brc_viagem_tempo', true);
						$embarque 				= get_post_meta($product_id, 'brc_viagem_origem', true);
						$desembarque 			= get_post_meta($product_id, 'brc_viagem_destino', true);
						$embarque_term 			= get_term($embarque, 'brc_destinos');
						$embarque_cidade 		= get_term($embarque_term->parent, 'brc_destinos');
						$desembarque_term 		= get_term($desembarque, 'brc_destinos');
						$desembarque_cidade 	= get_term($desembarque_term->parent, 'brc_destinos');
						
						$product_args = array(
							'_product_id' 					=> $product_id,
							'_product_nome' 				=> get_the_title($product_id),
							'_product_ida_data' 			=> $brc_viagem_ida_data,
							'_product_ida_origem' 			=> $embarque,
							'_product_ida_origem_cidade' 	=> $embarque_term->parent,
							'_product_ida_destino' 			=> $desembarque,
							'_product_ida_destino_cidade' 	=> $desembarque_term->parent,
							'_product_volta_data' 			=> $brc_viagem_volta_data,
							'_product_volta_origem' 		=> $desembarque,
							'_product_volta_origem_cidade' 	=> $desembarque_term->parent,
							'_product_volta_destino' 		=> $embarque,
							'_product_volta_destino_cidade' => $embarque_term->parent,
							
							'_product_tipo_cadastro' 		=> $tipo_cadastro,
						);
						
						if($linha){
							$product_args['_product_ponto_embarque'] = $linha_pontos[$ponto_embarque];
							if($linha_pontos[$ponto_embarque]['p-valor']){
								$product_args['_product_ponto_embarque_valor'] = $linha_pontos[$ponto_embarque]['p-valor'];
							}
						}
						
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
									$ret['redirec_url']	= FINALIZAR_RESERVA_URI;
								}
							}
							else{
								$woocommerce->cart->empty_cart();
								$product_args['_order_etapa'] = 'ida';
								$product_args['_order_selvolta'] = false;
								
								$ret['sts'] = true;
								$ret['set_selvolta'] = false;
								$ret['redirec_url'] = FINALIZAR_RESERVA_URI;
							}
						}
						else{
							$woocommerce->cart->empty_cart();
							$ret['sts'] = true;
							$ret['set_selvolta'] = false;
							$ret['redirec_url'] = FINALIZAR_RESERVA_URI;
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
				
				//$ret['sts'] = false;
				
				echo json_encode($ret);
				exit;
			}
			
			// [AJAX] CHECKTOU
			public function brc_remove_cart_item(){
				$ret['sts'] = false;
				
				if(isset($_POST['cartitem'])){
					global $woocommerce, $brcthemes;
					
					$woocommerce->cart->remove_cart_item($_POST['cartitem']);
					$ret['sts'] = true;
					
					// VERIFICAR SE CARRINHO ESTÁ VAZIO
					if($woocommerce->cart->get_cart_contents_count() == 0){
						$ret['empty'] = true;
						$ret['redirec_url'] = BUSCA_URI;
					}
				}
				
				echo json_encode($ret);
				exit;
			}
			
			// ADICIONA DADOS FINAIS APÓS PEDIDO FEITO
			public function brc_new_order($item, $cart_item_key, $values, $order){
				
				//------------------------
				// PRODUCT
				$product_id 		= $item->get_product_id();
				$product_title 		= get_the_title($product_id);
				$brc_excursao 		= get_post_meta($product_id, 'brc_excursao', true);
				$tipo_cadastro 		= get_post_meta($product_id, 'brc_viagem_tipo_cadastro', true);
				$brc_excursao_ingresso_stock = get_post_meta($product_id, 'brc_excursao_ingresso_stock', true);
				
				if($brc_excursao){
					$item->update_meta_data('_brc_excursao', true);
					$item->update_meta_data('_hopede_nome', $values['_hopede_nome']);
					$item->update_meta_data('_hopede_cpf', $values['_hopede_cpf']);
					$item->update_meta_data('_variation_hotel', $values['_variation_hotel']);
					$item->update_meta_data('_variation_hotel_id', $values['_variation_hotel_id']);
					$item->update_meta_data('_variation_price', $values['_variation_price']);
					$item->update_meta_data('_product_id', $values['_product_id']);
					$item->update_meta_data('_product_nome', $values['_product_nome']);
					$item->update_meta_data('_product_noites', $values['_product_noites']);
					$item->update_meta_data('_product_data', $values['_product_data']);
					
					if($values['_ingresso']){
						$item->update_meta_data('_ingresso', true);
						$item->update_meta_data('_ingresso_price', $values['_ingresso_price']);
						
						update_post_meta($product_id, 'brc_excursao_ingresso_stock', $brc_excursao_ingresso_stock - 1);
					}else{
						$item->update_meta_data('_ingresso', false);
					}
				}
				else{
					$item->update_meta_data('_brc_excursao', false);
					$item->update_meta_data('_product_tipo_cadastro', $tipo_cadastro);
					$item->update_meta_data('_passageiro_nome', $values['_passageiro_nome']);
					$item->update_meta_data('_passageiro_cpf', $values['_passageiro_cpf']);
					$item->update_meta_data('_passageiro_rg', $values['_passageiro_rg']);
					$item->update_meta_data('_product_id', $values['_product_id']);
					$item->update_meta_data('_product_nome', $values['_product_nome']);
					$item->update_meta_data('_product_ida_data', $values['_product_ida_data']);
					$item->update_meta_data('_product_ida_origem', $values['_product_ida_origem']);
					$item->update_meta_data('_product_ida_origem_cidade', $values['_product_ida_origem_cidade']);
					$item->update_meta_data('_product_ida_destino', $values['_product_ida_destino']);
					$item->update_meta_data('_product_ida_destino_cidade', $values['_product_ida_destino_cidade']);
					$item->update_meta_data('_product_volta_data', $values['_product_volta_data']);
					$item->update_meta_data('_product_volta_origem', $values['_product_volta_origem']);
					$item->update_meta_data('_product_volta_origem_cidade', $values['_product_volta_origem_cidade']);
					$item->update_meta_data('_product_volta_destino', $values['_product_volta_destino']);
					$item->update_meta_data('_product_volta_destino_cidade', $values['_product_volta_destino_cidade']);
					
					
					// VISUAL DADOS
					$item->update_meta_data('Passageiro', $values['Passageiro']);
					if($tipo_cadastro == 1){
						
						$item->update_meta_data('Origem Ida', $values['Origem Ida']);
						if($values['Endereço'])
							$item->update_meta_data('Endereço', $values['Endereço']);
						$item->update_meta_data('Data/hora Ida', $values['Data/hora Ida']);
						$item->update_meta_data('Destino Ida', $values['Destino Ida']);
						
						$item->update_meta_data('Data/hora Volta', $values['Data/hora Volta']);
						$item->update_meta_data('Origem Volta', $values['Origem Volta']);
						$item->update_meta_data('Destino Volta', $values['Destino Volta']);
					}
					if($tipo_cadastro == 2){
						
						$item->update_meta_data('Origem', $values['Origem']);
						if($values['Endereço'])
							$item->update_meta_data('Endereço', $values['Endereço']);
						$item->update_meta_data('Data/hora Embarque', $values['Data/hora Embarque']);
						
						$item->update_meta_data('Destino', $values['Destino']);
						$item->update_meta_data('Data/hora Desembarque', $values['Data/hora Desembarque']);
					}
					if($tipo_cadastro == 3){
						
						$item->update_meta_data('Origem', $values['Origem']);
						if($values['Endereço'])
							$item->update_meta_data('Endereço', $values['Endereço']);
						$item->update_meta_data('Data/hora Embarque', $values['Data/hora Embarque']);
						
						$item->update_meta_data('Destino', $values['Destino']);
						$item->update_meta_data('Data/hora Desembarque', $values['Data/hora Desembarque']);
						
						$item->update_meta_data('_order_etapa', $values['_order_etapa']);
						$item->update_meta_data('_order_selvolta', $values['_order_selvolta']);
						
						$item->update_meta_data('Assento', $values['_passageiro_assento']);
						$item->update_meta_data('_veiculo', 1);
						$item->update_meta_data('_assento', $values['_passageiro_assento']);
						
						// Poltronas
						if(isset($values['_passageiro_assento'])){
							$assentos_disponiveis = get_post_meta($product_id, 'brc_assentos_disponiveis', true);
							$assentos_disponiveis[$values['_passageiro_assento']] = false;
							update_post_meta($product_id, 'brc_assentos_disponiveis', $assentos_disponiveis);
						}
					}
				}
			}
			public function brc_new_order_thankyou($order_get_id){
				$order = new WC_Order($order_get_id);
				
				foreach($order->get_items() as $order_item_id => $order_item){
					
					$data = $order_item->get_data();
					$meta_data = $order_item->get_meta_data();
					$meta_data_array = order_item_meta_to_array($meta_data);
					if($meta_data_array['_brc_excursao']){
						update_post_meta($order_get_id, 'brc_order_excursao', true);
					}else{
						update_post_meta($order_get_id, 'brc_order_excursao', false);
						update_post_meta($order_get_id, 'brc_order_product_tipo', $meta_data_array['_product_tipo_cadastro']);
					}
				}
				
				update_option('brc_order_excursao03', $order_get_id, false);
			} 
			
		}
		add_action('init', array('BRCPASSTOUR', 'init'));
		//add_action('init', array('BRCPASSTOUR_NOTIFICATION', 'init'));
	}
	