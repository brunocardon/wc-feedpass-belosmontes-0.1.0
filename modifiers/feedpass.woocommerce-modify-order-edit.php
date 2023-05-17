<?php
	
	// MODIFICA PADRÕES DO WOOCOMMERCE
	if(is_plugin_active('woocommerce/woocommerce.php')){
		//$brcpasstour_notification = new BRCPASSTOUR_NOTIFICATION();
		
		// ORDER ADMIN LIST
		if(!function_exists('brc_shop_order_columns_manage_edit')){
			function brc_shop_order_columns_manage_edit($columns){
				
				$brc_columns['cb'] 					= $columns['cb'];
				$brc_columns['order_cliente'] 		= 'Pedido';
				$brc_columns['produto'] 			= 'Produto'; // Viagem, passageiros, valores, paradas
				$brc_columns['order_date'] 			= 'Emissão';
				$brc_columns['order_total'] 		= $columns['order_total'];
				$brc_columns['order_actions'] 		= '';
				
				//return $columns;
				return $brc_columns;
			}
			add_filter('manage_edit-shop_order_columns', 'brc_shop_order_columns_manage_edit', 1, 25);
		}
		if(!function_exists('brc_shop_order_columns_manage_edit_callback')){
			function brc_shop_order_columns_manage_edit_callback($column){
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
						echo '<span class="bullet bullet-sm order-status status-'. $order->get_status() .'"><span>'. wc_get_order_status_name($order->get_status()) .'</span></span><br/>'	;
						
					break;
					case 'produto':
						$brc_order_excursao = get_post_meta($order_id, 'brc_order_excursao', true);
						$brc_order_product_tipo = get_post_meta($order_id, 'brc_order_product_tipo', true);
						
						if($brc_order_excursao){
							foreach($order->get_items() as $order_item_id => $order_item){
								$meta_data 			= $order_item->get_meta_data();
								$meta_data_array 	= order_item_meta_to_array($meta_data);
								$valor 				= html_entity_decode(strip_tags(wc_price($order_item->get_total()/$order_item->get_quantity())));
								$order_user_id 		= $order->get_user_id();
								
								$produto_id 		= $order_item->get_product_id();
								$produto 			= get_post($produto_id);
								
								$col[] = array(
									'passageiro' => '['.$order_item_id.'] '.$meta_data_array['_hopede_nome'] .' ('. $meta_data_array['_hopede_cpf'] .')',
									'veiculo' => '<strong>Veic.</strong>['. $meta_data_array['_veiculo'] .']',
									'assento' => '<strong>Poltrona</strong>['. $meta_data_array['_assento'] .']',
									'valor' => $valor,
									'ingresso' => $meta_data_array['_ingresso'],
									'ingresso_valor' => $meta_data_array['_ingresso_price'],
								);
							}
							if($col){
								echo '<span class="bullet bullet-sm bullet-type bullet-excursao">excursão</span>';
								echo '<a href="'. get_edit_post_link($produto_id) .'"><strong>'. $produto->post_title .'</strong></a>';
								echo '<table class="brc-admin-table">';
								foreach($col as $k => $j){
									echo '<tr>
										<td class="text-left">'. $j['passageiro'] .'</td>
										<td class="text-center">'. $j['veiculo'] .'</td>
										<td class="text-center">'. $j['assento'] .'</td>
										<td class="text-right">'.($j['ingresso']?'<span class="bullet bullet-sm">ingresso</span> ':''). $j['valor'] .'</td>
									</tr>';
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
								
								
								$passageiros[$produto_id][$order_item_id] = array(
									'passageiro' => $meta_data_array['_passageiro_nome'] .' ('. $meta_data_array['_passageiro_cpf'] .')',
									'veiculo' => '<strong>Veic.</strong>['. $meta_data_array['_veiculo'] .']',
									'assento' => '<strong>Poltrona</strong>['. $meta_data_array['_assento'] .']',
									'valor' => $valor,
								);
							}
							if($passageiros){
								foreach($passageiros as $produto_id => $col){
									$viagem_tipo = get_viagem_tipo();
									$viagem_tipo_class = get_viagem_tipo_class();
									$produto = get_post($produto_id);
									
									echo '<br/><span class="bullet bullet-sm bullet-type bullet-viagem-'.$viagem_tipo_class[$brc_order_product_tipo].'">'.$viagem_tipo[$brc_order_product_tipo] .'</span>';
									echo '<a href="'. get_edit_post_link($produto_id) .'"><strong>'. $produto_id.' -'.$produto->post_title .'</strong></a>';
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
								}
								
								echo 'Cliente: <a href="'. get_edit_user_link($order_user_id) .'"><strong>'. get_user_meta($order_user_id, 'billing_first_name', true) .'</strong></a>';
							}
						}
					break;
					case 'order_actions':
						$base = base64_encode(get_the_ID());
						
						echo '<a href="'.get_permalink(get_option('fepa_comprovante_reserva')).'?p='. $base .'&lista=order" target="_blank" class="brc_admin_btn brc_admin_list_btn button action-ticket" title="Imprimir bilhetes de passagem">
						<span class="fa fa-ticket"></span></a>';
						
						if('bacs' == $order->get_payment_method()){
							$recipt = get_post_meta(get_the_ID(), 'woo-bacs-recipt', true);
							if($recipt){
								echo '<a href="#" target="_blank" data-orderid="'. $order_id .'" class="brc_admin_btn btn_comprovante brc_admin_list_btn button action-comprovante" title="Ver comprovante de depósito">
								<span class="fa fa-file-text-o"></span></a>';
							}else{
								echo '<a href="#" target="_blank" data-orderid="'. $order_id .'" class="brc_admin_btn btn_comprovante btn_comprovante_nop brc_admin_list_btn button action-comprovante" title="Ver comprovante de depósito">
								<span class="fa fa-file-text-o"></span></a>';
							}
						}
						if($order->get_status() != 'cancelled'){
							echo '<a href="#" target="_blank" data-orderid="'. $order_id .'" class="brc_admin_btn btn_vermelho brc_admin_list_btn button action-cancelar" 
							title="Cancelar Pedido">
							<span class="fa fa-remove"></span></a>';
						}
					break;
				}
			}
			add_filter('manage_shop_order_posts_custom_column', 'brc_shop_order_columns_manage_edit_callback', 1, 25);
		}
		if(!function_exists('brc_order_list_legendas')){
			function brc_order_list_legendas(){
				global $wp_query;
				$post_type = $wp_query->get('post_type');
				
				if($post_type == 'shop_order'){
				?>
					<div class="brc_list_legendas">
						<ul>
							<li><span class="fa fa-ticket"></span> Imprimir bilhetes de passagem</li>
							<li><span class="fa fa-file-pdf-o"></span> Imprimir comprovante de reserva</li>
							<li><span class="fa fa-file-text-o"></span> Ver comprovante de depósito</li>
							<li><span class="fa fa-whatsapp"></span> Enviar mensagem via Whatsapp</li>
						</ul>
					</div>
				<?php
				}
			}
			add_action('manage_posts_extra_tablenav', 'brc_order_list_legendas');
		}
		if(!function_exists('brc_order_list_order')){
			function brc_order_list_order($query){
				$post_type = $query->get('post_type');
				
				if($post_type == 'shop_order' and is_admin()){
					if(!$query->query_vars['for_count']){
						if(isset($_GET['excursoes'])){
							if($_GET['excursoes'] == '1'){
								$query->set('meta_query', array(
									'relation' => 'AND',
									array(
										'key' 			=> 'brc_order_excursao',
										'compare' 		=> '>=',
										'value' 		=> 1,
									),
								));
								$query->set('brc_order_excursao', 'yes');
							}
						}
						if(isset($_GET['viagens'])){
							if($_GET['viagens'] == '1'){
								$query->set('meta_query', array(
									'relation' => 'AND',
									array(
										'key' 			=> 'brc_order_excursao',
										'compare' 		=> '<=',
										'value' 		=> 0,
									),
								));
								$query->set('brc_order_excursao', 'no');
							}
						}
						if(isset($_GET['bacs'])){
							if($_GET['bacs'] == '1'){
								$query->set('meta_query', array(
									'relation' => 'AND',
									array(
										'key' 			=> '_payment_method',
										'compare' 		=> '==',
										'value' 		=> 'bacs',
									),/*
									array(
										'key' 			=> 'woo-bacs-recipt',
										'compare' 		=> 'EXISTS',
									),*/
								));
							}
						}
						if(isset($_GET['created_via'])){
							if($_GET['created_via'] == 'admin'){
								$query->set('meta_query', array(
									'relation' => 'AND',
									array(
										'key' 			=> '_created_via',
										'compare' 		=> '==',
										'value' 		=> 'admin',
									),
								));
							}
						}
					}
				}
			}
			add_action('pre_get_posts', 'brc_order_list_order' );
		}
		if(!function_exists('brc_order_list_filter_link')){
			function brc_order_list_filter_link($views){
				global $wp_query;
				
				if(is_admin()){
					//--------------
					// EXCURSÕES FILTER LINK
					$excursao_query = array(
						'post_type'   		=> 'shop_order',
						'posts_per_page' 	=> -1,
						'post_status' 		=> 'any',
						'meta_query' 		=> array(
							'relation' 		=> 'AND',
							array(
								'key' 			=> 'brc_order_excursao',
								'compare' 		=> '>=',
								'value' 		=> 1,
							),
						),
						'for_count' 			=> true,
					);
					$excursao_result = new WP_Query($excursao_query);
					$class = ($wp_query->query_vars['brc_order_excursao']=='yes')?' class="current"':'';
					
					// FILTER LINK
					$views['publish_excursoes'] = sprintf(
						'<a href="%s"'. $class .'>Excursões <span class="count">(%d)</span></a>',
						admin_url('edit.php?post_type=shop_order&excursoes=1'),
						$excursao_result->found_posts
					);
					//--------------
					//--------------
					// VIAGENS FILTER LINK
					$viagem_query = array(
						'post_type'   		=> 'shop_order',
						'posts_per_page' 	=> -1,
						'post_status' 		=> 'any',
						'meta_query' 		=> array(
							'relation' 		=> 'AND',
							array(
								'key' 			=> 'brc_order_excursao',
								'compare' 		=> '<=',
								'value' 		=> 0,
							),
						),
						'for_count' 			=> true,
					);
					$viagem_result = new WP_Query($viagem_query);
					$class = ($wp_query->query_vars['brc_order_excursao']=='no')?' class="current"':'';
					
					// FILTER LINK
					$views['publish_viagens'] = sprintf(
						'<a href="%s"'. $class .'>Viagens <span class="count">(%d)</span></a>',
						admin_url('edit.php?post_type=shop_order&viagens=1'),
						$viagem_result->found_posts
					);
					//--------------
					//--------------
					// DEPÓSITOS COM COMPROVANTE FILTER LINK
					$bacs_query = array(
						'post_type'   		=> 'shop_order',
						'posts_per_page' 	=> -1,
						'post_status' 		=> 'any',
						'meta_query' 		=> array(
							'relation' 		=> 'AND',
							array(
								'key' 			=> '_payment_method',
								'compare' 		=> '==',
								'value' 		=> 'bacs',
							),/*
							array(
								'key' 			=> 'woo-bacs-recipt',
								'compare' 		=> 'EXISTS',
							),*/
						),
						'for_count' 			=> true,
					);
					$bacs_result = new WP_Query($bacs_query);
					$class = 'brc_admin_btn btn_comprovante brc_admin_list_btn button';
					$class .= ($wp_query->query_vars['brc_order_excursao']=='yes')?'current':'';
					
					// FILTER LINK
					$views['publish_bacs'] = sprintf(
						'<a href="%s" class="'. $class .'">Comprovantes de depósito <span class="count">(%d)</span></a>',
						admin_url('edit.php?post_type=shop_order&bacs=1'),
						$bacs_result->found_posts
					);
					//--------------
					//--------------
					// ADMIN USER FILTER LINK
					$created_via_query = array(
						'post_type'   		=> 'shop_order',
						'posts_per_page' 	=> -1,
						'post_status' 		=> 'any',
						'meta_query' 		=> array(
							'relation' 		=> 'AND',
							array(
								'key' 			=> '_created_via',
								'compare' 		=> '==',
								'value' 		=> 'admin',
							),
						),
						'for_count' 			=> true,
					);
					
					$created_via_result = new WP_Query($created_via_query);
					$class = '';
					$class .= ($wp_query->query_vars['brc_order_excursao']=='yes')?'current':'';
					
					// FILTER LINK
					$views['publish_created_via'] = sprintf(
						'<a href="%s" class="'. $class .'">Pedidos Manuais <span class="count">(%d)</span></a>',
						admin_url('edit.php?post_type=shop_order&created_via=admin'),
						$created_via_result->found_posts
					);
					//--------------
				}
				return $views;
			}
			add_filter('views_edit-shop_order', 'brc_order_list_filter_link', 1);
		}
		if(!function_exists('brc_order_list_bulk_actions')){
			function brc_order_list_bulk_actions($actions){
				$actions['trash'] = 'Excluir pedido';
				
				unset($actions['trash']);
				unset($actions['mark_processing']);
				unset($actions['mark_on-hold']);
				unset($actions['mark_completed']);
				
				$actions['brc_order_list_bulk_cancelar'] = 'Cancelar pedidos';
				return $actions;
			}
			add_filter('bulk_actions-edit-shop_order','brc_order_list_bulk_actions', 100, 1);
		}
		
		// MODIFICA A BUSCA NA LISTAGEM ADMIN
		if(!function_exists('brc_order_search_by_passageiro_join')){
			function brc_order_search_by_passageiro_join($join, $query){
				global $pagenow, $wpdb;
				
				if(
					!$query->query_vars['for_count'] && 
					is_admin() && 
					'edit.php' === $pagenow && 
					'shop_order' === $_GET['post_type'] && 
					!empty($_GET['s'])
				){
					$join .= "INNER JOIN ".$wpdb->prefix."woocommerce_order_items items ON items.order_id = ".$wpdb->posts.".ID ";
					$join .= "INNER JOIN ".$wpdb->prefix."woocommerce_order_itemmeta itemsmeta ON itemsmeta.order_item_id = items.order_item_id ";
				}
				return $join;
			}
			add_filter('posts_join', 'brc_order_search_by_passageiro_join', 10, 2);
		}
		if(!function_exists('brc_order_search_by_passageiro_where')){
			function brc_order_search_by_passageiro_where($where, $query){
				global $pagenow, $wpdb;
				
				if(
					!$query->query_vars['for_count'] && 
					is_admin() && 
					'edit.php' === $pagenow && 
					'shop_order' === $_GET['post_type'] && 
					!empty($_GET['s'])
				){
					// 
					$where = "AND itemsmeta.meta_value LIKE '%". $_GET['s'] ."%' AND (itemsmeta.meta_key = '_hopede_nome' OR itemsmeta.meta_key = '_passageiro_nome') AND ".$wpdb->posts.".post_type = 'shop_order'";
					$where .= "GROUP BY ".$wpdb->posts.".ID";
				}
				return $where;
			}
			add_filter('posts_where', 'brc_order_search_by_passageiro_where', 10, 2);
		}
		
		// EXCLUI UM PEDIDO DIRETAMENTE, NÃO ENVIANDO PARA LIXEIRA
		if(!function_exists('brc_order_skip_trash')){
			function brc_order_skip_trash($post_id){
				if(get_post_type($post_id) == 'shop_order'){
					wp_delete_post($post_id, true);
				}
			} 
			add_action('trashed_post', 'brc_order_skip_trash');
		}
		
		// ------------------------------
		// EDITOR
		// ADD CUSTOM METABOXES
		if(!function_exists('brc_order_edit_comprovante_metaboxes')){
			function brc_order_edit_comprovante_metaboxes(){
				$recip = get_post_meta(get_the_ID(), 'woo-bacs-recipt', true);
				if($recip){
					// REGISTA A METABOX
					add_meta_box(
						'brc_order_edit_comprovante', 							// $id
						'Comprovante de Depósito #'.get_the_ID(), 				// $title
						'brc_order_edit_comprovante_metaboxes_html', 			// $callback
						'shop_order', 											// $page
						'side', 												// $context
						'high' 													// $priority
					);
				}
			}
			function brc_order_edit_comprovante_metaboxes_html(){
				$recip = get_post_meta(get_the_ID(), 'woo-bacs-recipt', true);
					
				if($recip){
					echo '<div class="text-center">';
					echo '<a href="#" data-orderid="'. get_the_ID() .'" class="action-comprovante" title="Ver comprovante de depósito">';
					echo '<img src="'. $recip .'" alt="Comprovante de depósito #'. get_the_ID() .'"/>';
					echo '</a>';
					echo '</div>';
				}
			}
			add_action('add_meta_boxes', 'brc_order_edit_comprovante_metaboxes');
		}
		
		// ADD CUSTOM METABOXES
		if(!function_exists('brc_order_add')){
			function brc_order_add(){
				
				// REGISTA A METABOX
				add_meta_box(
					'brc_order_add', 				// $id
					'Escolha o tipo de pedido', 	// $title
					'brc_order_add_html', 			// $callback
					'shop_order', 					// $page
					'normal', 						// $context
					'high' 							// $priority
				);
				add_meta_box(
					'brc_order_add_excursao', 		// $id
					'Pedido de excursão para cliente', 	// $title
					'brc_order_add_excursao_html', 	// $callback
					'shop_order', 					// $page
					'normal', 						// $context
					'high' 							// $priority
				);
				add_meta_box(
					'brc_order_add_viagem', 		// $id
					'Pedido de viagem de linha para cliente', 	// $title
					'brc_order_add_viagem_html', 	// $callback
					'shop_order', 					// $page
					'normal', 						// $context
					'high' 							// $priority
				);
			}
			function brc_order_add_html(){
				include BRC_TEMPLATES_URL . '/brc_admin.order_add.php';
			}
			function brc_order_add_excursao_html(){
				include BRC_TEMPLATES_URL . '/brc_admin.order_add_excursao.php';
			}
			function brc_order_add_viagem_html(){
				include BRC_TEMPLATES_URL . '/brc_admin.order_add_viagem.php';
			}
			
			
			add_action('add_meta_boxes', 'brc_order_add');
		}
		
		// ESCONDER METABOXES
		if(!function_exists('brc_order_edit_hide_metaboxes')){
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
			add_filter('hidden_meta_boxes', 'brc_order_edit_hide_metaboxes', 10, 2);
		}
	}
?>