<?php
	class FEPATaxHotelCategoria{
		protected static $instance = null;
		
		function __construct(){
			// add stuffs...
			self::reg_brc_hotel_categ();
		}
		
		static function init(){
			if(null == self::$instance ){
				self::$instance = new self;
			}
			return self::$instance;
		}
		
		public function register(){
			// Nothing to see here
		}
		
		/**
		 * Add taxonomy ´brc_hotel_categ´ 
		 * Post Type: hoteis
		 * 
		 */
		private function reg_brc_hotel_categ(){
			register_taxonomy('brc_hotel_categ', array('hoteis'),
				array(
					'hierarchical'      => true,
					'labels'            => array(
						'name'              => "Cateroias de Hotel",
						'singular_name'     => "Cateroia",
						'search_items'      => "Procurar Cateroias",
						'all_items'         => "Todas as Cateroias",
						'parent_item'       => "Cateroia Pai",
						'parent_item_colon' => "Cateroia Pai:",
						'edit_item'         => "Editar Cateroia",
						'update_item'       => "Atualizar Cateroia",
						'add_new_item'      => "Adicionar nova Cateroia",
						'new_item_name'     => "Novo nome de Cateroia",
						'menu_name'         => "Cateroias",
					),
					'show_ui'           => true,
					'show_admin_column' => true,
					'query_var'         => true,
					'rewrite'           => array( 'slug' => 'hotel-categoria' ),
				)
			);
		}
		
		// .FEPATaxHotelCategoria
	}
?>