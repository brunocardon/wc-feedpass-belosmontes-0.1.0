<?php
	class FEPATaxHotelLocal{
		protected static $instance = null;
		
		function __construct(){
			// add stuffs...
			self::reg_brc_hotel_local();
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
		 * Add taxonomy ´brc_hotel_local´ 
		 * Post Type: hoteis
		 * 
		 */
		private function reg_brc_hotel_local(){
			register_taxonomy('brc_hotel_local', array('hoteis'),
				array(
					'hierarchical'      => true,
					'labels'            => array(
						'name'              => "Locais",
						'singular_name'     => "Local",
						'search_items'      => "Procurar Locais",
						'all_items'         => "Todas as Locais",
						'parent_item'       => "Local Pai",
						'parent_item_colon' => "Local Pai:",
						'edit_item'         => "Editar Local",
						'update_item'       => "Atualizar Local",
						'add_new_item'      => "Adicionar novo Local",
						'new_item_name'     => "Novo nome de Local",
						'menu_name'         => "Locais",
					),
					'show_ui'           => true,
					'show_admin_column' => true,
					'query_var'         => true,
					'rewrite'           => array( 'slug' => 'hotel-locais' ),
				)
			);
			add_action('cmb2_meta_boxes', array($this, 'brc_hotel_local_edit_register_custom_metaboxes'));
		}
		public function brc_hotel_local_edit_register_custom_metaboxes(){
			$taxonomy = 'brc_hotel_local';
			$prefix = 'brc_tax_';
			$general = new_cmb2_box(array(
				'id' 			=> $prefix . 'geral', // brc_prod_geral
				'title'         => 'Dados do Local',
				'object_types'  => array('term'),
				'taxonomies'  	=> array($taxonomy),
			));
			
			$Sigla = $general->add_field( array(
				'name'    		=> 'Sigla',
				'id'      		=> $prefix.'sigla',
				'type'    		=> 'text',
			));
		}	
		
		// .FEPATaxHotelLocal
	}
?>