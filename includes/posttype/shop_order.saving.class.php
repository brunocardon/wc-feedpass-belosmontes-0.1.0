<?php
	class FEPAPostTypeShopOrderSaving{
		protected static $instance = null;
		
		
		function __construct(){
			
			
		}
		static function init(){
			if(null == self::$instance ){
				self::$instance = new self;
			}
			return self::$instance;
		}
		public function register(){
			FEPAPostTypeSavingProduct::init();
		}
		
		
		
		
		
		
		
		
		
		// .FEPAPostTypeShopOrderSaving
	}
?>