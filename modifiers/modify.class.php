<?php
	class FEPAModify{
		protected static $instance = null;
		
		function __construct(){
			add_action('woocommerce_checkout_before_order_review', array($this, 'brc_checkout_before_order_review'), 99);
			add_action('woocommerce_before_calculate_totals', array($this, 'brc_calc_cart'), 99);
			add_filter('woocommerce_cart_item_name', array($this, 'brc_cart_item_name'), 10, 3 );
			add_filter('woocommerce_checkout_cart_item_quantity', array($this, 'brc_checkout_cart_item_quantity'), 10, 3 );
			add_filter('woocommerce_cart_item_subtotal', array($this, 'brc_cart_item_subtotal'), 10, 3 );
			
			
			// Remove default woocommerce single product blocks
			remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10);
			remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20);
			
			remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_title', 5);
			remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10);
			remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);
			remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20);
			remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
			remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);
			remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50);
			
			remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10);
			remove_action('woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15);
			remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);
			
			add_action('woocommerce_before_main_content', array($this, 'brc_before_main_content'), 10);
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
		
		/*
		 * Rewrite cart price
		 * 
		 */
		public function brc_calc_cart($cart_object){
			if(!WC()->session->__isset("reload_checkout")){
				foreach($cart_object->cart_contents as $key => $value){
					if($value["_product_ponto_embarque_valor"]){
						$value['data']->set_price($value["_product_ponto_embarque_valor"]);
					}
					if($value["_ex_variation_price"]){
						$value['data']->set_price($value["_ex_variation_price"]);
					}
				}  
			}  
		}
		
		/*
		 * Rewrite product name order review on checkout page
		 * Add data from viagem
		 * 
		 */
		public function brc_cart_item_name($str, $cart_item, $cart_item_key){
			include FEPA_PLUGIN_DIR . '/templates/brc_template.order_review.php'; 
		}
		
		/*
		 * Rewrite order review title
		 * 
		 */
		public function brc_checkout_before_order_review(){
			echo '<h3 id="order_review_heading" class="checkout_order_review_title">Resumo do pedido</h3>';
		}
		
		/*
		 * Rewrite product count at order review on checkout page
		 * 
		 */
		public function brc_checkout_cart_item_quantity($str, $cart_item, $cart_item_key){
			return false;
		}
		
		/*
		 * Rewrite product price at order review on checkout page
		 * 
		 */
		public function brc_cart_item_subtotal($str, $cart_item, $cart_item_key){
			include FEPA_PLUGIN_DIR . '/templates/brc_template.order_review_subtotal.php'; 
		}
		
		/*
		 * HTML markup
		 * Post Type: product: excursões
		 */
		public function brc_before_main_content(){
			include FEPA_PLUGIN_DIR . '/templates/brc_template.excursoes_before_main_content.php';
		}
		
		// .FEPAModify
	}
?>