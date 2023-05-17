<?php
	
	// MODIFICA PADRÕES DO WOOCOMMERCE
	if(is_plugin_active('woocommerce/woocommerce.php')){
		
		// ADD LOADING OVERLAY
		if(!function_exists('brc_loading_overlay')){
			function brc_loading_overlay(){
				global $post, $title, $action, $current_screen;
				
				// ID POST TYPE
				if($current_screen->post_type){
					echo '<div class="page-admin-ID" id="admin-'. $current_screen->post_type .'"></div>';
					echo '<div class="page-admin-base" id="admin-base-'. $current_screen->base .'"></div>';
				}
				
				// ESCONDE VISUAL COMPOSER DE VIAGENS
				$brc_excursao = get_post_meta($post->ID, 'brc_excursao', true);
				if($current_screen->post_type == 'product'){
					if(!$brc_excursao){
						echo '<style> #poststuff .composer-switch{ display:none; } </style>';
					}
				}
				
				// ADICIONAR (metatype=excursoes)
				if($current_screen->post_type == 'product' and isset($_GET['metatype'])){
					echo '<div id="metatype" data-mvalue="'. $_GET['metatype'] .'"></div>';
				}
				$brc_excursao = get_post_meta($post->ID, 'brc_excursao', true);
				if($brc_excursao){
					echo '<div id="metatype" data-mvalue="excursoes"></div>';
				}
				
				// LOADING
				echo '<div class="admin_loading"></div>';
				
				// MODAL
				add_thickbox();
				echo '<div id="brc-tb-content" style="display:none;"><div class="brc-modal-inner"></div></div>';
			}
			add_action('admin_footer', 'brc_loading_overlay');
		}
		
		unregister_taxonomy('product_tag'); // REMOVE TAXONOMY TAG DE PRODUTOS
		unregister_taxonomy('product_cat'); // REMOVE TAXONOMY CATEGORIA DE PRODUTOS
		
		// MODIFICA TÍTULO EM THANKYOU PARA VIAGENS
		if(!function_exists('brc_woocommerce_order_item_name')){
			function brc_woocommerce_order_item_name($html, $item, $is_visible){
				$data = $item->get_data();
				$product_id = $data['product_id'];
				$meta_data = $item->get_meta_data();
				$meta_data_array = order_item_meta_to_array($meta_data);
				
				$brc_excursao = get_post_meta($product_id, 'brc_excursao', true);
				if($brc_excursao){
					echo $html;
				}else{
					// DESTINOS
					$_product_ida_origem_cidade 	= get_term($meta_data_array['_product_ida_origem_cidade']); // Montes claros
					$_product_ida_destino_cidade 	= get_term($meta_data_array['_product_ida_destino_cidade']);
					
					$string = $_product_ida_origem_cidade->name .' > '. $_product_ida_destino_cidade->name;
					
					//echo sprintf('<a href="%s">%s</a>', get_permalink($product_id), $string);
					echo $string;
				}
			}
			add_filter('woocommerce_order_item_name', 'brc_woocommerce_order_item_name', 10, 3);
		}
		
		// MODIFICA O NOME DO MENU WOOCOMMERCE
		if(!function_exists('brc_woocommerce_menu_rename')){
			function brc_woocommerce_menu_rename() {
				global $menu;

				// Pinpoint menu item
				$woo = brc_woocommerce_menu_search_php( 'WooCommerce', $menu );

				// Validate
				if( !$woo )
					return;
				
				$menu[$woo][0] = get_bloginfo('name');
				$menu[$woo][6] = get_site_icon_url(20);
			}
			add_action('admin_menu', 'brc_woocommerce_menu_rename', 999);
		}
		if(!function_exists('brc_woocommerce_menu_search_php')){
			function brc_woocommerce_menu_search_php($needle, $haystack){
				foreach($haystack as $key => $value){
					$current_key = $key;
					if( 
						$needle === $value 
						OR ( 
							is_array( $value )
							&& brc_woocommerce_menu_search_php( $needle, $value ) !== false 
						)
					){
						return $current_key;
					}
				}
				return false;
			}
		}
		
		// ticket PAGE
		if(!function_exists('brc_body_classes')){
			function brc_body_classes($classes){
				global $post;
				
				$brc_woocommerce_templates = array(
					'template.resumopedidoverf.php'
				);
				
				if(is_page_template($brc_woocommerce_templates)){
					$classes[] = 'eltdf-woocommerce-page';
					$classes[] = 'woocommerce-order-received';
				}
			 
				return $classes;
			}
			add_filter('body_class', 'brc_body_classes');
		}
		
	}	
