<?php
	class FEPAPostTypeShopOrder{
		protected static $instance = null;
		
		
		function __construct(){
			
			add_action('woocommerce_checkout_create_order_line_item', array($this, 'brc_new_order'), 10, 4);
			add_action('woocommerce_thankyou', array($this, 'brc_new_order_thankyou'), 10, 1);
			
			add_action('manage_edit-shop_order_columns', array($this, 'brc_shop_order_columns_manage_edit'), 1000);
			add_action('manage_shop_order_posts_custom_column', array($this, 'brc_shop_order_columns_manage_edit_callback'), 1000, 1);
			
			
			add_action('add_meta_boxes', array($this, 'brc_order_edit_comprovante_metaboxes'));
			add_action('add_meta_boxes', array($this, 'brc_order_add'));
			add_action('hidden_meta_boxes', array($this, 'brc_order_edit_hide_metaboxes'), 10, 2);
			
			
			// TB SHOW ORDER
				add_action('wp_ajax_nopriv_brc_admin_order_edit_comprovante_show', 		array($this, 'brc_admin_order_edit_comprovante_show'));
				add_action('wp_ajax_brc_admin_order_edit_comprovante_show', 			array($this, 'brc_admin_order_edit_comprovante_show'));
				
				// ORDER EDIT/ADD brc_order_produto_excursao
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
				
				// [ADMIN] SAVE ORDERS
				add_action('save_post', array($this, 'brc_save_post_shop_order'), 100, 3);
		}
		static function init(){
			if(null == self::$instance ){
				self::$instance = new self;
			}
			return self::$instance;
		}
		public function register(){
			FEPAPostTypeShopOrderSaving::init();
		}
		
		/**
		 * New post 
		 * Post Type: shop_order
		 * 
		 */
		public function brc_new_order($item, $cart_item_key, $values, $order){
			
			//------------------------
			// PRODUCT
			$product_id = $item->get_product_id();
			$product_title = get_the_title($product_id);
			$brc_excursao = get_post_meta($product_id, 'brc_excursao', true);
			$brc_excursao_ingresso_stock = get_post_meta($product_id, 'brc_excursao_ingresso_stock', true);
			
			if($brc_excursao){
				$item->update_meta_data('_brc_excursao', true);
				
				$item->update_meta_data('_product_id', $values['_product_id']);
				$item->update_meta_data('_product_nome', $values['_product_nome']);
				$item->update_meta_data('_product_noites', $values['_product_noites']);
				$item->update_meta_data('_product_data', $values['_product_data']);
				$item->update_meta_data('_product_data_volta', $values['_product_data_volta']);
				
				$item->update_meta_data('_variation_pacote', $values['_variation_pacote']);
				$item->update_meta_data('_variation_hotel', $values['_variation_hotel']);
				$item->update_meta_data('_variation_hotel_id', $values['_variation_hotel_id']);
				$item->update_meta_data('_ex_variation_price', $values['_ex_variation_price']);
				
				$item->update_meta_data('_hospedes', $values['_hospedes']);
				
				$item->update_meta_data('_ingresso', $values['_ingresso']);
				if($values['_ingresso']){
					$item->update_meta_data('_ingresso_price', $values['_ingresso_price']);
					if($values['_hospedes']){
						foreach($values['_hospedes'] as $hospede){
							if($hospede['ingresso']){
								$brc_excursao_ingresso_stock--;
							}
						}
						update_post_meta($product_id, 'brc_excursao_ingresso_stock', $brc_excursao_ingresso_stock);
					}
				}
				
				$item->update_meta_data('_crianca', $values['_ingresso']);
				if($values['_crianca'])
					$item->update_meta_data('_criancas', $values['_criancas']);
				
			}
			else{
				$item->update_meta_data('Data/hora Ida', $values['Data/hora Ida']);
				$item->update_meta_data('Origem Ida', $values['Origem Ida']);
				$item->update_meta_data('Destino Ida', $values['Destino Ida']);
				$item->update_meta_data('Data/hora Volta', $values['Data/hora Volta']);
				$item->update_meta_data('Origem Volta', $values['Origem Volta']);
				$item->update_meta_data('Destino Volta', $values['Destino Volta']);
				$item->update_meta_data('Passageiro', $values['Passageiro']);
				/*--*/
				$item->update_meta_data('_brc_excursao', false);
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
				}
			}
			
			update_option('brc_order_excursao03', $order_get_id, false);
		} 
		
		/**
		 * Columns
		 * Post Type: shop_order
		 * 
		 */
		public function brc_shop_order_columns_manage_edit($columns){
			$brc_columns['cb'] 					= $columns['cb'];
			$brc_columns['order_cliente'] 		= 'Pedido';
			$brc_columns['produto'] 			= 'Produto'; // Viagem, passageiros, valores, paradas
			$brc_columns['order_date'] 			= 'Emissão';
			$brc_columns['order_total'] 		= $columns['order_total'];
			$brc_columns['order_actions'] 		= '';
			return $brc_columns;
		}
		public function brc_shop_order_columns_manage_edit_callback($column){
			global $brcpasstour_notification;
			
			$order_id 		= get_the_ID();
			$order 			= new WC_Order($order_id);
			$user_order_id 	= $order->get_user_id();
			$user 			= get_userdata($user_order_id);
			$_created_via 	= get_post_meta($order_id, '_created_via', true);
			
			switch($column){
				case 'order_cliente':
					global $pagenow, $wpdb;
					echo '<a href="'. get_edit_post_link($order_id) .'">
							<strong>#'. $order_id .' '. $user->data->display_name .' '.($_created_via=='admin'?'['.strtoupper($_created_via).']':'').'</strong>
						</a><br/>';
					echo '<span class="bullet bullet-primary">'. $order->get_payment_method_title() .'</span><br/>';
					echo '<mark class="bullet order-status status-'. $order->get_status() .'"><span>'. wc_get_order_status_name($order->get_status()) .'</span></mark><br/>'	;
					
				break;
				case 'produto':
					$brc_order_excursao = get_post_meta($order_id, 'brc_order_excursao', true);
					$col = array();
					
					if($brc_order_excursao){
						foreach($order->get_items() as $order_item_id => $order_item){
							$meta_data 			= $order_item->get_meta_data();
							$meta_data_array 	= order_item_meta_to_array($meta_data);
							$var_data 			= $meta_data_array;
							$valor 				= html_entity_decode(strip_tags(wc_price($order_item->get_total()/$order_item->get_quantity())));
							$order_user_id 		= $order->get_user_id();
							
							$produto_id 		= $order_item->get_product_id();
							$produto 			= get_post($produto_id);
							
							$col[$order_item_id]['price'] = $valor;
							if($var_data['_hospedes']){
								foreach($var_data['_hospedes'] as $hospede){
									$item = array(
										'pacote' => $var_data['_variation_pacote'],
										'item_id' => $order_item_id,
										'passageiro' => $hospede['nome'] .' ('. $hospede['cpf'] .')',
										
										'veiculo' => '<strong>Veic.</strong>['. $var_data['_veiculo'] .']',
										'assento' => '<strong>Poltrona</strong>['. $var_data['_assento'] .']',
									);
									
									if($hospede['ingresso']){
										$item['ingresso'] = true;
										$item['ingresso_price'] = $hospede['ingresso_price'];
									}
									
									$col[$order_item_id]['rows'][] = $item;
								}
							}
						}
						if($col){
							echo '<span class="bullet bullet-sm bullet-type bullet-excursao">excursão</span>';
							echo '<a href="'. get_edit_post_link($produto_id) .'"><strong>'. $produto->post_title .'</strong></a>';
							echo '<table class="brc-admin-table">';
							foreach($col as $item_id => $items){
								$price_td[$item_id] = true;
								
								foreach($items['rows'] as $k => $j){
									echo '<tr>
										<td class="text-left">#'.$j['item_id'].' '.$j['pacote'] .'</td>
										<td class="text-left">'. $j['passageiro'] .'</td>
										<td class="text-center">'. $j['veiculo'] .'</td>
										<td class="text-center">'. $j['assento'] .'</td>';
									echo '<td class="text-right">'.($j['ingresso']?'<span class="bullet bullet-sm">+ingresso R$'.moedaRealPrint($j['ingresso_price']).'</span> ':'').'</td>';
									
									if($price_td[$item_id])
										echo '<td rowspan="'.count($items['rows']).'">'. $items['price'] .'</td>';
									
									echo '</tr>';
									
									
									$price_td[$item_id] = false;
								}
							}
							echo '</table>';
							echo 'Cliente: <a href="'. get_edit_user_link($order_user_id) .'"><strong>'. get_user_meta($order_user_id, 'billing_first_name', true) .'</strong></a>';
						}
					}
					else{
						foreach($order->get_items() as $order_item_id => $order_item){
							$meta_data 			= $order_item->get_meta_data();
							$meta_data_array 	= order_item_meta_to_array($meta_data);
							$valor 				= html_entity_decode(strip_tags(wc_price($order_item->get_total()/$order_item->get_quantity())));
							$order_user_id 		= $order->get_user_id();
							
							$produto_id 		= $order_item->get_product_id();
							$produto 			= get_post($produto_id);
							
							$col[] = array(
								'passageiro' => $meta_data_array['_passageiro_nome'] .' ('. $meta_data_array['_passageiro_cpf'] .')',
								'veiculo' => '<strong>Veic.</strong>['. $meta_data_array['_veiculo'] .']',
								'assento' => '<strong>Poltrona</strong>['. $meta_data_array['_assento'] .']',
								'valor' => $valor,
							);
						}
						if($col){
							echo '<span class="bullet bullet-sm bullet-type bullet-viagem">viagem</span>';
							echo '<a href="'. get_edit_post_link($produto_id) .'"><strong>'. $produto->post_title .'</strong></a>';
							echo '<table class="brc-admin-table">';
							foreach($col as $k => $j){
								echo '<tr>
									<td class="text-left">'. $j['passageiro'] .'</td>
									<td class="text-center">'. $j['veiculo'] .'</td>
									<td class="text-center">'. $j['assento'] .'</td>
									<td class="text-right">'. $j['valor'] .'</td>
								</tr>';
							}
							echo '</table>';
							echo 'Cliente: <a href="'. get_edit_user_link($order_user_id) .'"><strong>'. get_user_meta($order_user_id, 'billing_first_name', true) .'</strong></a>';
						}
					}
				break;
				case 'order_actions':
					$base = base64_encode(get_the_ID());
					
					$brc_order_excursao = get_post_meta($order_id, 'brc_order_excursao', true);
					if(!$brc_order_excursao){
						echo '<a href="'.get_permalink(get_option('fepa_passagemticket')).'?p='. $base .'&lista=order" target="_blank" class="brc_admin_btn brc_admin_list_btn button action-ticket" title="Imprimir bilhetes de passagem">
						<span class="fas fa-ticket"></span></a>';
					}
					echo '<a href="'.get_permalink(get_option('fepa_comprovante_reserva')).'?p='. $base .'&lista=order" target="_blank" class="brc_admin_btn brc_admin_list_btn button action-ticket" title="Imprimir comprovante de reserva">
					<span class="fas fa-clipboard-check"></span></a>';
					
					if('bacs' == $order->get_payment_method()){
						$recipt = get_post_meta(get_the_ID(), 'woo-bacs-recipt', true);
						if($recipt){
							echo '<a href="#" target="_blank" data-orderid="'. $order_id .'" class="brc_admin_btn btn_comprovante brc_admin_list_btn button action-comprovante" title="Ver comprovante de depósito">
							<span class="fas fa-file-alt"></span></a>';
						}else{
							echo '<a href="#" target="_blank" data-orderid="'. $order_id .'" class="brc_admin_btn btn_comprovante btn_comprovante_nop brc_admin_list_btn button action-comprovante" title="Ver comprovante de depósito">
							<span class="fas fa-file-alt"></span></a>';
						}
					}
				break;
			}
		}
		
		public function brc_order_edit_comprovante_metaboxes(){
			$recip = get_post_meta(get_the_ID(), 'woo-bacs-recipt', true);
			if($recip){
				add_meta_box(
					'brc_order_edit_comprovante',
					'Comprovante de Depósito #'.get_the_ID(),
					array($this, 'brc_order_edit_comprovante_metaboxes_html'),
					'shop_order',
					'side',
					'high'
				);
			}
		}
		public function brc_order_edit_comprovante_metaboxes_html(){
			$recip = get_post_meta(get_the_ID(), 'woo-bacs-recipt', true);
			if($recip){
				echo '<div class="text-center">';
				echo '<a href="#" data-orderid="'. get_the_ID() .'" class="action-comprovante" title="Ver comprovante de depósito">';
				echo '<img src="'. $recip .'" alt="Comprovante de depósito #'. get_the_ID() .'"/>';
				echo '</a>';
				echo '</div>';
			}
		}
		
		public function brc_order_add(){
			add_meta_box(
				'brc_order_add',
				'Escolha o tipo de pedido',
				array($this, 'brc_order_add_html'),
				'shop_order',
				'normal',		
				'high'
			);
			add_meta_box(
				'brc_order_add_excursao',
				'Pedido de excursão para cliente',
				array($this, 'brc_order_add_excursao_html'),
				'shop_order',
				'normal',
				'high'
			);
			add_meta_box(
				'brc_order_add_viagem',
				'Pedido de viagem de linha para cliente',
				array($this, 'brc_order_add_viagem_html'),
				'shop_order',
				'normal',
				'high'
			);
		}
		public function brc_order_add_html(){
			include FEPA_PLUGIN_DIR . '/templates/brc_admin.order_add.php';
		}
		public function brc_order_add_excursao_html(){
			include FEPA_PLUGIN_DIR . '/templates/brc_admin.order_add_excursao.php';
		}
		public function brc_order_add_viagem_html(){
			include FEPA_PLUGIN_DIR . '/templates/brc_admin.order_add_viagem.php';
		}
		
		function brc_order_edit_hide_metaboxes($hidden, $screen){
			if($screen->post_type == 'shop_order'){
				foreach($hidden as $k => $j){
					$n_hidden[$j] = $j;
				}
				
				$n_hidden['woocommerce-order-data'] 			= 'woocommerce-order-data';
				$n_hidden['woocommerce-order-items'] 			= 'woocommerce-order-items';
				$n_hidden['postcustom'] 						= 'postcustom';
				$n_hidden['mymetabox_revslider_0'] 			= 'mymetabox_revslider_0';
				$n_hidden['woocommerce-order-downloads'] 		= 'woocommerce-order-downloads';
				
				unset($n_hidden['brc_order_add_viagem']);
				unset($n_hidden['brc_order_add_excursao']);
				
				if(isset($_GET['post']) and $_GET['action'] == 'edit'){
					
					$order_id = $_GET['post'];
					$order = new WC_Order($post_ID);
					$brc_order_excursao = get_post_meta($order_id, 'brc_order_excursao', true);
					if($brc_order_excursao){
						$n_hidden['brc_order_add_viagem'] = 'brc_order_add_viagem';
					}else{
						$n_hidden['brc_order_add_excursao'] = 'brc_order_add_excursao';
					}
				}else{
					$n_hidden['brc_order_add_viagem'] = 'brc_order_add_viagem';
					$n_hidden['brc_order_add_excursao'] = 'brc_order_add_excursao';
				}
			}else{
				$n_hidden = $hidden;
			}
			return $n_hidden;
		} 
	
		//--------------------------
		//--------------------------
		
		// TB SHOW ORDER
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
			
			// ORDER EDIT/ADD
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
							include FEPA_PLUGIN_DIR . '/templates/brc_admin.order_add_excursao_vars.php';
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
							include FEPA_PLUGIN_DIR . '/templates/brc_admin.order_add_excursao_hospedes.php';
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
						include FEPA_PLUGIN_DIR . '/templates/brc_admin.order_add_viagem_table.php';
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
							include FEPA_PLUGIN_DIR . '/templates/brc_admin.order_add_viagem_passageiros.php';
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
			// [ADMIN] SAVE ORDERS CREATE
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
						}
					}
				}
			}
			
		
		//--------------------------
		//--------------------------
		
		
		
		// .FEPAPostTypeShopOrder
	}
?>