<?php
	if(!class_exists('BRCPASSTOUR_NOTIFICATION')){ // brcpasstour
		class BRCPASSTOUR_NOTIFICATION{
			
			public $whatsapp_link;
			public $paposms_link;
			public $paposms_user;
			public $paposms_pass;
			public $mensagem;
			
			function __construct(){
				
				$this->whatsapp_link 	= 'https://api.whatsapp.com/send';
				$this->paposms_link 	= 'https://www.paposms.com/webservice/1.0/send/';
				$this->paposms_user 	= get_option('brc_notification_admin_settings_paposms_user');
				$this->paposms_pass 	= get_option('brc_notification_admin_settings_paposms_pass');
				$this->mensagem 		= 'https://www.paposms.com/webservice/1.0/send/';
				
				add_filter('woocommerce_settings_tabs_array', 					array($this, 'brc_settings_tabs_array'), 50);
				add_action('woocommerce_settings_tabs_notification', 			array($this, 'notification_settings_tab'));
				add_action('woocommerce_update_options_notification', 			array($this, 'notification_update_settings'));
				
				// ADD ORDER LIST COL
				add_filter('manage_edit-shop_order_columns', 					array($this, 'brc_shop_order_notification_columns_manage_edit'), 1, 10);
				add_filter('manage_shop_order_posts_custom_column', 			array($this, 'brc_shop_order_notification_columns_manage_edit_callback'), 1, 10);
				
				// AJAX
				add_action('wp_ajax_nopriv_brcpasstour_notification_tags', 		array($this, 'brcpasstour_notification_tags'));
				add_action('wp_ajax_brcpasstour_notification_tags', 			array($this, 'brcpasstour_notification_tags'));
				
				add_action('wp_ajax_nopriv_brcpasstour_notification_sms', 		array($this, 'brcpasstour_notification_sms'));
				add_action('wp_ajax_brcpasstour_notification_sms', 				array($this, 'brcpasstour_notification_sms'));
				
				add_action('wp_ajax_nopriv_brcpasstour_notification_sms_send', 	array($this, 'brcpasstour_notification_sms_send'));
				add_action('wp_ajax_brcpasstour_notification_sms_send', 		array($this, 'brcpasstour_notification_sms_send'));
			}
			// SETUP
			static function init(){
				if(null == self::$instance ){
					self::$instance = new self;
				}
				return self::$instance;
			}
			
			public function brc_settings_tabs_array($settings_tabs){
				$settings_tabs['notification'] = 'Notificações Via SMS/Whatsapp';
				return $settings_tabs;
			}
			
			public function notification_settings_tab(){
				woocommerce_admin_fields($this->notification_get_settings());
			}
			
			public function notification_update_settings() {
				woocommerce_update_options($this->notification_get_settings());
			}
			
			public function notification_get_settings() {
				$prefix = 'brc_notification_admin_settings_';
				$eltd_options_blu = get_option('eltd_options_blu');
				
				$mensagem_pagamento_pendente 	= get_option($prefix.'pagamento_pendente');
				$mensagem_processando 			= get_option($prefix.'processando');
				$mensagem_aguardando 			= get_option($prefix.'aguardando');
				$mensagem_concluido 			= get_option($prefix.'concluido');
				
				$settings = array(
					'section_title' => array(
						'name'     => 'Dados de Notificações Via SMS/Whatsapp',
						'type'     => 'title',
						'desc'     => 'Informe as mensagens que enviaremos para os clientes via Notificação SMS/Whatsapp. <em>Você pode utilizar algumas "tags" para inserir dados da Compra ou Cliente, para saber mais <a href="#" class="brcpasstour_notification_tags">clique aqui</a>.</em>',
						'id'       => $prefix.'title',
					),
					'paposms_user' => array(
						'name' 		=> 'Usuário de acesso do Papo SMS',
						'type' 		=> 'text',
						'desc'     	=> 'Usuário de acesso ao sistema de envio de SMS Papo SMS',
						'default' 	=> '',
						'id'   		=> $prefix.'paposms_user',
					),
					'paposms_pass' => array(
						'name' 		=> 'Senha de acesso do Papo SMS',
						'type' 		=> 'text',
						'desc'     	=> '',
						'default' 	=> '',
						'id'   		=> $prefix.'paposms_pass',
					),
					'pagamento_pendente' => array(
						'name' 		=> 'Mensagem para Pagamento pendente',
						'type' 		=> 'textarea',
						'desc'     	=> 'Você pode utilizar algumas "tags" para inserir dados da Compra ou Cliente, para saber mais <a href="#" class="brcpasstour_notification_tags">clique aqui</a>.',
						'css'     	=> 'min-width:75%;min-height:120px;',
						'default' 	=> '',
						'id'   		=> $prefix.'pagamento_pendente',
					),
					'processando' => array(
						'name' 		=> 'Mensagem para Processando (Novo Pedido)',
						'type' 		=> 'textarea',
						'desc'     	=> 'Você pode utilizar algumas "tags" para inserir dados da Compra ou Cliente, para saber mais <a href="#" class="brcpasstour_notification_tags">clique aqui</a>.',
						'css'     	=> 'min-width:75%;min-height:120px;',
						'default' 	=> '',
						'id'   		=> $prefix.'processando',
					),
					'aguardando' => array(
						'name' 		=> 'Aguardando',
						'type' 		=> 'textarea',
						'desc'     	=> 'Você pode utilizar algumas "tags" para inserir dados da Compra ou Cliente, para saber mais <a href="#" class="brcpasstour_notification_tags">clique aqui</a>.',
						'css'     	=> 'min-width:75%;min-height:120px;',
						'default' 	=> '',
						'id'   		=> $prefix.'aguardando',
					),
					'concluido' => array(
						'name' 		=> 'Concluído',
						'type' 		=> 'textarea',
						'desc'     	=> 'Você pode utilizar algumas "tags" para inserir dados da Compra ou Cliente, para saber mais <a href="#" class="brcpasstour_notification_tags">clique aqui</a>.',
						'css'     	=> 'min-width:75%;min-height:120px;',
						'default' 	=> '',
						'id'   		=> $prefix.'concluido',
					),
					
					'section_end' => array(
						'type' 	=> 'sectionend',
						'id' 	=> 'wc_settings_tab_demo_section_end',
					)
				);
				return apply_filters('wc_notification_settings', $settings);
			}
			
			public function gen_notification_mensagem($order_id = false){
				if($order_id){
					$order 			= new WC_Order($order_id);
					$order_user_id 	= $order->get_user_id();
					$order_user 	= get_userdata($order_user_id);
					$order_status 	= $order->get_status();
					
					// ORDER DADOS - GENERAL
					$str_sintax = array(
						'sitename' 			=> get_bloginfo('name'),
						'wc-order' 			=> $order_id,
						'wc-order-date' 	=> $order->get_date_created()->date("d/m/Y"),
						'wc-order-status' 	=> wc_get_order_status_name($order_status),
						'wc-payment-method' => $order->get_payment_method_title(),
					);
					
					// ORDER USER DADOS - WordPress Profile Details
					$billing_first_name 			= get_user_meta($order_user_id, 'billing_first_name', true);
					$billing_last_name 				= get_user_meta($order_user_id, 'billing_last_name', true);
					$billing_email 					= get_user_meta($order_user_id, 'billing_email', true);
					$display_name 					= $order_user->display_name;
					$str_sintax['wp-first-name'] 	= $billing_first_name;
					$str_sintax['wp-last-name'] 	= $billing_last_name;
					$str_sintax['wp-display-name'] 	= $billing_email;
					$str_sintax['wp-email'] 		= $display_name;

					
					// ORDER MOAR DADOS - WooCommerce Order Details
					$total_products = 0;
					foreach($order->get_items() as $order_item_id => $order_item){
						$produto_id 	= $order_item->get_product_id();
						$produto 		= get_post($produto_id);
						
						$product_names[] = $produto->post_title;
						$product_name_count[] = $produto->post_title .' x'.intval($order_item->get_quantity());
						$total_products += intval($order_item->get_quantity());
					}
					$str_sintax['wc-product-names'] = implode(', ', $product_names);
					$str_sintax['wc-product-name-count'] = implode(', ', $product_name_count);
					$str_sintax['wc-total-products'] = $total_products;
					$str_sintax['wc-order-amount'] = $order->get_total();
					
					$str_search = array();
					$str_replace = array();
					foreach($str_sintax as $k => $j){
						$str_search[] = '{{'.$k.'}}';
						$str_replace[] = $j;
					}
					
					$prefix = 'brc_notification_admin_settings_';
					switch($order_status){
						case 'processing':
							$mensagem = get_option($prefix.'processando');
						break;
						case 'pending':
							$mensagem = get_option($prefix.'pagamento_pendente');
						break;
						case 'on-hold':
							$mensagem = get_option($prefix.'aguardando');
						break;
						case 'completed':
							$mensagem = get_option($prefix.'concluido');
						break;
						case 'cancelled':
							return '#';
						break;
						case 'refunded':
							return '#';
						break;
						case 'failed':
							return '#';
						break;
					}
					
					$this->mensagem = str_replace($str_search, $str_replace, $mensagem);
				}
			}
			
			public function gen_notification_whatsapp_url($order_id = false){
				if($order_id){
					$this->gen_notification_mensagem($order_id);
					
					$order = new WC_Order($order_id);
					$user_order_id 	= $order->get_user_id();
					$str_search = array('(', ')', ' ', '-');
					$str_replace = array('', '', '', '');
					
					
					$billing_cellphone = get_user_meta($user_order_id, 'billing_cellphone', true);
					if($billing_cellphone){
						$this->whatsapp_cellphone = str_replace($str_search, $str_replace, $billing_cellphone);
					}
					
					$_order_admin_tel = get_post_meta($order_id, '_order_admin_tel', true);
					if($_order_admin_tel){
						$this->whatsapp_cellphone = str_replace($str_search, $str_replace, $_order_admin_tel);
					}
					
					$whatsapp_query = array(
						'phone' 	=> '55'.$this->whatsapp_cellphone,
						'text' 		=> $this->mensagem,
						'source' 	=> get_bloginfo('name'),
					);
					$whatsapp_url_query = $this->whatsapp_link.'?'.http_build_query($whatsapp_query);
					
					return $whatsapp_url_query;
				}
				return '#';
			}
			
			// ADD ORDER LIST COL
			function brc_shop_order_notification_columns_manage_edit($columns){
				
				$columns['brcpasstour_notification'] = '';
				return $columns;
			}
			function brc_shop_order_notification_columns_manage_edit_callback($column){
				global $brcpasstour_notification;
				
				$order_id 		= get_the_ID();
				$order 			= new WC_Order($order_id);
				$user_order_id 	= $order->get_user_id();
				$user 			= get_userdata($user_order_id);
				$_created_via 	= get_post_meta($order_id, '_created_via', true);
				
				switch($column){
					case 'brcpasstour_notification':
						$base = base64_encode(get_the_ID());
						
						echo '<a href="'. $this->gen_notification_whatsapp_url(get_the_ID()) .'" 
							target="_blank" 
							class="brc_admin_btn btn_whatsapp brc_admin_list_btn button action-whatsapp" 
							title="Enviar mensagem via Whatsapp"
						>
							<span class="fa fa-whatsapp"></span>
						</a>';
					break;
				}
			}
			
			
			//------------------------
			// AJAX
			public function brcpasstour_notification_sms(){
				global $brcpasstour_theme;
				
				$passageiros = false;
				if(isset($_REQUEST['product_id'])){
					
					$product_id = $_REQUEST['product_id'];
					$v_status = get_all_woocommerce_status_id();
					unset($v_status[4]);
					unset($v_status[5]);
					unset($v_status[6]);
					$orders = get_orders_ids_by_product_id($product_id, $v_status);
					
					if($orders){
						foreach($orders as $order_id){
							$order 			= new WC_Order($order_id);
							$order_user_id 		= $order->get_user_id();
							$order_user 		= get_userdata($order_user_id);
							$billing_cellphone 	= get_user_meta($order_user_id, 'billing_cellphone', true);
							$_order_admin_tel 	= get_post_meta($order_id, '_order_admin_tel', true);
							
							if($billing_cellphone){
								$str_search 	= array('(', ')', ' ', '-');
								$str_replace 	= array('', '', '', '');
								$cellphone 		= str_replace($str_search, $str_replace, $billing_cellphone);
								$billing_cpf 	= get_user_meta($order_user_id, 'billing_cpf', true);
								
								$passageiros[$j] = array(
									'nome' 		=> $order_user->display_name,
									'cpf' 		=> $billing_cpf,
									'cellphone' => $cellphone,
									'billing_cellphone' => $billing_cellphone,
								);
							}
						}
					}
				}
				
				include BRC_TEMPLATES_URL .'/brc_admin.notification_sms.php';
				exit;
			}
			public function brcpasstour_notification_sms_send(){
				$ret['sts'] = false;
				$ret['label'] = 'Ocorreu um erro inesperado, parece que alguns campos não foram imformados.';
				
				if(isset($_REQUEST['mensagem']) and isset($_REQUEST['cellphone'])){
					
					$mensagem = strip_tags($_REQUEST['mensagem']);
					$cellphone = $_REQUEST['cellphone'];
					
					$fields = array(
						"user" 				=> $this->paposms_user,
						"pass" 				=> $this->paposms_pass,
						"numbers" 			=> implode(';', $cellphone),
						"message" 			=> $mensagem,
						"return_format" 	=> "json"
					);
					
					$postvars 		= http_build_query($fields);
					$result 		= file_get_contents($this->paposms_link."?".$postvars);
					$result_array 	= json_decode($result, true);
					
					if($result_array['result'] === true){
						$ret['sts'] = true;
						$ret['label'] = $result_array['label'];
						$ret['result_array'] = $result_array;
					}else{
						$ret['sts'] = false;
						$ret['label'] = $result_array['label'];
						$ret['result_array'] = $result_array;
					}
				}
				echo json_encode($ret);
				exit;
			}
			public function brcpasstour_notification_tags(){
				include BRC_TEMPLATES_URL . '/brc_admin.sms_notification_tags.php';
				exit;
			}
		}
	}
?>