<?php
	$produto_id = $poder_post->ID;
	$orders = get_orders_ids_by_product_id($produto_id, get_all_woocommerce_status_id());
	
	if($orders){
		$page_itens = array();
		
		foreach($orders as $order_id){
			
			// CHAMA A ORDER
			$order 					= new WC_Order($order_id);
			$emissao 				= $order->get_date_created()->date("d/m/Y");
			$forma_de_pagamento 	= $order->get_payment_method_title();
			
			foreach($order->get_items() as $order_item_id => $order_item){
				if($order_item->get_product_id() == $produto_id){
					$produto_id = $order_item->get_product_id();
					
					$ticket 	= wc_get_order_item_meta($order_item_id, '_ticket', true);
					$linha 		= wp_get_post_terms($produto_id, 'brc_linhas');
					if(!is_wp_error($linha)){
						$llinha = $linha[0];
						$linha = $llinha->name;
						
						$linhas_linha_ini = get_term_meta($llinha->term_id, 'brc_brc_linhas_linha_ini', true);
						$linhas_linha_fim = get_term_meta($llinha->term_id, 'brc_brc_linhas_linha_fim', true);
						$linhas_tarifa = get_term_meta($llinha->term_id, 'brc_brc_linhas_tarifa', true);
						$linhas_agencia = get_term_meta($llinha->term_id, 'brc_brc_linhas_agencia', true);
						$linhas_Prefixo = get_term_meta($llinha->term_id, 'brc_brc_linhas_Prefixo', true);
						$linhas_pedagio = get_term_meta($llinha->term_id, 'brc_brc_linhas_pedagio', true);
						$linhas_taxa = get_term_meta($llinha->term_id, 'brc_brc_linhas_taxa', true);
						$linhas_aliq = get_term_meta($llinha->term_id, 'brc_brc_linhas_aliq', true);
						$linhas_vtributo = get_term_meta($llinha->term_id, 'brc_brc_linhas_vtributo', true);
						$linhas_fpagamento = get_term_meta($llinha->term_id, 'brc_brc_linhas_fpagamento', true);
					}
					
					$tarifa = html_entity_decode(strip_tags(wc_price($order_item->get_total()/$order_item->get_quantity())));
					$total_da_prestacao = html_entity_decode(strip_tags(wc_price($order_item->get_total()/$order_item->get_quantity())));
					
					foreach($ticket['passageiro'] as $passageiro_id => $passageiro_item){
						
						$order_item_passageiro = $produto_id.$passageiro_item['poltrona'];
						$page_itens[$order_item_passageiro] = array(
							'args' 		=> array( 
								'page_title' 			=> utf8_decode(get_bloginfo('name').' - '.$post->post_title.' (Viagem: '. $poder_post->post_title .')'),
								'order_item_passageiro' => $order_item_passageiro,
							),
							'ticket' 	=> array(
								'nome_do_passageiro' 	=> $passageiro_item['nome_do_passageiro'],
								'identidade' 			=> $passageiro_item['identidade'],
								'cpf' 					=> $passageiro_item['cpf'],
								'de' 					=> $linhas_linha_ini,
								'para' 					=> $linhas_linha_fim,
								//'de' 					=> $ticket['de'],
								//'para' 					=> $ticket['para'],
								'linha' 				=> $linha,
								'data_viagem' 			=> $ticket['data_viagem'],
								'horario' 				=> $ticket['horario'],
								'emissao' 				=> $emissao,
								'poltrona' 				=> $passageiro_item['poltrona'],
								'order_forma_de_pagamento' 	=> $forma_de_pagamento,
								'forma_de_pagamento' 	=> $linhas_fpagamento,
								'tarifa' 				=> $linhas_tarifa,
								'total_da_prestacao' 	=> $linhas_tarifa,
								//'tarifa' 				=> $tarifa,
								//'total_da_prestacao' 	=> $total_da_prestacao,
							),
							'ticket_admin' => array(
								'linhas_linha_ini' 		=> $linhas_linha_ini,
								'linhas_linha_fim' 		=> $linhas_linha_fim,
								'linhas_tarifa' 		=> $linhas_tarifa,
								'02_agencia' 			=> $linhas_agencia,
								'02_prefixo' 			=> $linhas_Prefixo,
								'02_pedagio' 			=> $linhas_pedagio,
								'02_taxa_embarque' 		=> $linhas_taxa,
								'02_aliq_icms' 			=> $linhas_aliq,
								'02_valor_tributo' 		=> $linhas_vtributo,
							),
						);
					}
				}
			}
		}
	}

?>