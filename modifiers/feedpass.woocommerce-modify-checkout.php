<?php
	
	// MODIFICA PADRÕES DO WOOCOMMERCE
	if(is_plugin_active('woocommerce/woocommerce.php')){
		
		// MOVE REVIEW
		remove_action( 'woocommerce_checkout_order_review', 'woocommerce_order_review', 10 );
		add_action( 'woocommerce_before_checkout_sidebar', 'woocommerce_order_review', 10, 1 ); 
		
		// MOVER CUPOM
		remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 ); 
		add_action( 'woocommerce_before_checkout_sidebar', 'woocommerce_checkout_coupon_form', 20, 1 ); 
		
		// REMOVE CHECKOUT FIELDS
		if(!function_exists('brc_checkout_fields')){
			function brc_checkout_fields($fields){

				// remove billing fields
				unset($fields['billing']['billing_phone']);

				return $fields;
			}
			add_filter('woocommerce_checkout_fields' , 'brc_checkout_fields', 1);
		}
		
		
		// MODIFICA BOTÃO DE REMOVER COUPON
		if(!function_exists('brc_cart_totals_coupon_html')){
			function brc_cart_totals_coupon_html($coupon_html, $coupon, $discount_amount_html){
				
				$_url = add_query_arg('remove_coupon', rawurlencode($coupon->get_code()), wc_get_checkout_url());
				
				$brc_coupon_html = $discount_amount_html;
				$brc_coupon_html .= '<a href="'. $_url .'" class="remove woocommerce-remove-coupon" title="Remover Cupom" data-coupon="'. esc_attr($coupon->get_code()) .'">';
				$brc_coupon_html .= '<i class="fa fa-times-circle-o"></i></a>';
				
				echo $brc_coupon_html;
			}
			add_filter('woocommerce_cart_totals_coupon_html', 'brc_cart_totals_coupon_html', 1, 3); // $coupon_html, $coupon, $discount_amount_html
		}
		
		// ADICIONA CAMPO PARA PASSAGEIROS
		if(!function_exists('brc_checkout_passageiros')){
			function brc_checkout_passageiros(){
				include get_template_directory().'/vc-templates/inc.finalizar_passageiros.php';
			}
			add_action('woocommerce_checkout_before_customer_details', 'brc_checkout_passageiros');
		}
		
		// LIMPA CARRINHO PERSISTENTE DE CLIENTE
		if(!function_exists('brc_clear_persistent_cart_after_login')){
			function brc_clear_persistent_cart_after_login($user_login, $user){
				$blog_id = get_current_blog_id();
				if(metadata_exists('user', $user->ID, '_woocommerce_persistent_cart')){
					delete_user_meta( $user->ID, '_woocommerce_persistent_cart' );
				}
				if(metadata_exists('user', $user->ID, '_woocommerce_persistent_cart_' . $blog_id)){
					delete_user_meta($user->ID, '_woocommerce_persistent_cart_' . $blog_id);
				}
			}
			add_action('wp_login', 'brc_clear_persistent_cart_after_login', 10, 2);
		}
		
		// MUDA STATUS PADRÃO DE PAGAMENTO NO EMBARQUE PARA AGUARDANDO (on-hold)
		if(!function_exists('brc_payment_cod_status')){
			function brc_payment_cod_status($order_status){
				return 'on-hold';
			}
			add_filter('woocommerce_cod_process_payment_order_status', 'brc_payment_cod_status', 10);
		}
		
		// [FILTER] THANK YOU ORDER METAS SHOW
		if(!function_exists('brc_display_item_meta')){
			function brc_display_item_meta($html, $item, $args){
				$strings = array(); 
				$html = ''; 
				$args = wp_parse_args( $args, array( 
					'before' 		=> '<ul class="wc-item-meta"><li>',  
					'after' 		=> '</li></ul>',  
					'separator' 	=> '</li><li>',  
					'echo' 			=> true,  
					'autop' 		=> false,  
					'email' 		=> false,  
				) ); 
				
				$ccp = 1;
				foreach($item->get_formatted_meta_data() as $meta_id => $meta){
					$value = $args['autop'] ? wp_kses_post( $meta->display_value ) : wp_kses_post( make_clickable( trim( strip_tags( $meta->display_value ) ) ) ); 
					$key = sanitize_title($meta->display_key);
					$str = '<span class="brc_order_meta brc_meta_'. $key .'">';
					$str .='<strong class="wc-item-meta-label">' . wp_kses_post( $meta->display_key ) . ':</strong> ' . $value.'</span>'; 
					
					$hash = array();
					if(
						strstr($key, 'passageiro') or
						strstr($key, 'hopede')
					){
						
						$hash[] = $item->get_order_id();
						$hash[] = $item->get_id();
						$hash[] = $ccp;
						$link = get_permalink(get_option('fepa_comprovante_reserva')).'?p='. base64_encode(implode('|', $hash));
						
						if($email)
							$br='<br/>';
						
						$str .= $astyle.' <br/><a href="'.$link.'" target="_blank" class="brc_order_meta_print" title="Imprimir comprovante de reserva"><small>Imprimir comprovante de reserva</small></a>';
						
						if(get_the_ID() != MINHA_CONTA_ID){
							$str .= $astyle.' <br/><a href="'.MINHA_CONTA_URI.'" target="_blank" class="brc_order_meta_print" title="Acessar minha conta"><small>Acessar minha conta</small></a>';
						}
						$ccp++;
					}
					$strings[] = $str;
				}
				
				if($strings){
					$html = $args['before'] . implode( $args['separator'], $strings ) . $args['after']; 
				} 
				
				if($args['echo']){
					echo $html;
				}else{ 
					return $html; 
				} 
			}
			add_filter('woocommerce_display_item_meta', 'brc_display_item_meta', 10, 3);
		}
		
		// [FILTER] ADICIONA CSS PARA LAYOUT DE EMAIL
		if(!function_exists('brc_custom_css_email')){
			function brc_custom_css_email($css, $email){
				$custom['.brc_order_meta_print'] = '.brc_order_meta_print{
					display:inline-block;
					text-decoration:none;
					margin-top:4px;
					margin-bottom:4px;
					padding:4px 8px 3px;
					border-radius:3px;
					color:#ffffff;
					background:#e21c18;
					line-height: 1;
					font-weight: 500;
					text-transform: uppercase;
				}';

				
				return $css.' '.implode(' ', $custom);
			}
			add_filter('woocommerce_email_styles', 'brc_custom_css_email', 10, 2);
		}
		
		// MODIFICA VALOR DO PRODUTO AO ENVIAR PARA CARRINHO
		if(!function_exists('woocommerce_custom_price_to_cart_item')){
			function woocommerce_custom_price_to_cart_item($cart_object){
				if(!WC()->session->__isset("reload_checkout")){
					foreach($cart_object->cart_contents as $key => $value){
						if(isseT($value["_product_ponto_embarque_valor"])){
							$value['data']->set_price($value["_product_ponto_embarque_valor"]);
						}
					}  
				}  
			}
			add_action('woocommerce_before_calculate_totals', 'woocommerce_custom_price_to_cart_item', 99);
		}
	}
?>