<?php
	class FEPATaxDestino{
		protected static $instance = null;
		
		function __construct(){
			// add stuffs...
			self::reg_brc_destinos();
		}
		
		static function init(){
			if(null == self::$instance ){
				self::$instance = new self;
			}
			return self::$instance;
		}
		
		public function register(){
			// Nothing to see here...
		}
		
		/**
		 * Add taxonomy ´brc_destinos´ 
		 * 
		 */
		private function reg_brc_destinos(){
			$taxonomy = 'brc_destinos';
			register_taxonomy('brc_destinos', array('product'),
				array(
					'hierarchical'      => true,
					'labels'            => array(
						'name'              => "Destinos",
						'singular_name'     => "Destino",
						'search_items'      => "Procurar Destinos",
						'all_items'         => "Todos os Destinos",
						'parent_item'       => "Destino Pai",
						'parent_item_colon' => "Destino Pai:",
						'edit_item'         => "Editar Destino",
						'update_item'       => "Atualizar Destino",
						'add_new_item'      => "Adicionor novo Destino",
						'new_item_name'     => "Novo nome de Destino",
						'menu_name'         => "Origens/Destinos",
					),
					'show_ui'           => true,
					'show_admin_column' => true,
					'query_var'         => true,
					'rewrite'           => array( 'slug' => 'destino' ),
				)
			);
			add_action('cmb2_meta_boxes', array($this, $taxonomy.'_edit_register_custom_metaboxes'));
		}
		public function brc_destinos_edit_register_custom_metaboxes(){
			// nothing to see here...
		}
		
		
		// .FEPATaxDestino
	}
?>