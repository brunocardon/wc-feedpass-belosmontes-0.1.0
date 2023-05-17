<?php
	if(is_plugin_active('woocommerce/woocommerce.php')){
		
		// REMOVE ITEM DOWNLOADS
		if(!function_exists('brc_myaccount_menu_links')){
			function brc_myaccount_menu_links($menu_links){
				unset($menu_links['downloads']);
				return $menu_links;
			}
			add_filter('woocommerce_account_menu_items', 'brc_myaccount_menu_links');
		}
		
		// ADICIONA ITEM MEUS CUPONS
		// ADICIONADO EM FUNCTION POR CAUSA DO INIT
		if(!function_exists('brc_my_custom_endpoints')){
			function brc_my_custom_endpoints() {
				add_rewrite_endpoint('meus-cupons', EP_ROOT | EP_PAGES );
			}
			add_action('init', 'brc_my_custom_endpoints');
		}
		
		if(!function_exists('brc_my_custom_woocommerce_query_vars')){ // so you can use is_wc_endpoint_url( 'refunds-returns' )
			function brc_my_custom_woocommerce_query_vars($vars){
				$vars[] = 'meus-cupons';
				return $vars;
			}
			add_filter('query_vars', 'brc_my_custom_woocommerce_query_vars', 0);
		}
		
		if(!function_exists('brc_myaccount_menu_cupons')){
			function brc_myaccount_menu_cupons($menu_links){
				
				$new = array('meus-cupons' => 'Meus Cupons');
				$menu_links = array_slice($menu_links, 0, 1, true) + $new + array_slice( $menu_links, 1, NULL, true );
				
				return $menu_links;
			}
			add_filter('woocommerce_account_menu_items', 'brc_myaccount_menu_cupons');
		}
		
		if(!function_exists('brc_myaccount_menu_cupons_endpoint')){
			function brc_myaccount_menu_cupons_endpoint(){
				include BRC_TEMPLATES_URL.'/brc_template.myaccount-menu_cupons.php';
			}
			add_action('woocommerce_account_meus-cupons_endpoint', 'brc_myaccount_menu_cupons_endpoint');
		}
		
	}
?>