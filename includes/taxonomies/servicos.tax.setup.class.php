<?php
	class FEPATaxServicos{
		protected static $instance = null;
		
		function __construct(){
			// add stuffs...
			self::reg_brc_servicos();
			
			add_action('admin_menu', array($this, 'brc_servicos_menu_excursoes'), 5);
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
		 * Add taxonomy ´brc_servicos´ 
		 * 
		 */
		private function reg_brc_servicos(){
			$taxonomy = 'brc_servicos';
			register_taxonomy($taxonomy, array('product'),
				array(
					'hierarchical'      => false,
					'labels'            => array(
						'name'              => "Serviços",
						'singular_name'     => "Serviço",
						'search_items'      => "Procurar Serviços",
						'all_items'         => "Todos os Serviços",
						'parent_item'       => "Serviço Pai",
						'parent_item_colon' => "Serviço Pai:",
						'edit_item'         => "Editar Serviço",
						'update_item'       => "Atualizar Serviço",
						'add_new_item'      => "Adicionar novo Serviço",
						'new_item_name'     => "Novo nome de Serviço",
						'menu_name'         => "Serviços",
					),
					'show_ui'           => true,
					'show_admin_column' => true,
					'query_var'         => false, 
					'show_in_menu' 		=> false, 
					'rewrite'           => array('slug' => 'servico'),
				)
			);
			add_action('cmb2_meta_boxes', array($this, $taxonomy.'_edit_register_custom_metaboxes'));
		}
		
		public function brc_servicos_menu_excursoes(){
			add_submenu_page(
				'edit.php?post_type=product&metatype=excursoes', 		// parent_slug
				'Serviços', 
				'Serviços', 
				'manage_options', 
				'edit-tags.php?taxonomy=brc_servicos&post_type=product&metatype=excursoes',
				'',
				3
			);
		}
		
		// .FEPATaxServicos
	}
?>