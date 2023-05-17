<?php
	class FEPATax{
		protected static $instance = null;
		
		function __construct(){
			// add stuffs...
			add_action('init', array($this, 'brc_remove_wc_default_tax'));
		}
		
		static function init(){
			if(null == self::$instance ){
				self::$instance = new self;
			}
			return self::$instance;
		}
		
		public function register(){
			//FEPATaxDestino::init();
			//FEPATaxLinha::init();
			FEPATaxVeiculo::init();
			FEPATaxMotorista::init();
			FEPATaxHotelLocal::init();
			FEPATaxHotelCategoria::init();
			
			FEPATaxCidade::init();
			FEPATaxPontoEmbarque::init();
			FEPATaxLinhaViagem::init();
			FEPATaxServicos::init();
		}
		
		/**
		 * Remove WooCommerce Default Taxonomies
		 * 
		 */
		public function brc_remove_wc_default_tax(){
			unregister_taxonomy('product_tag');
			unregister_taxonomy('product_cat');
		}
		
		
		
		
		// .FEPATax
	}
?>