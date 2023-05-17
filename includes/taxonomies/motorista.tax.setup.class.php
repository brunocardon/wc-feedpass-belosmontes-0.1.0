<?php
	class FEPATaxMotorista{
		protected static $instance = null;
		
		function __construct(){
			// add stuffs...
			self::reg_brc_motorista();
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
		 * Add taxonomy ´brc_motorista´ 
		 * 
		 */
		private function reg_brc_motorista(){
			$taxonomy = 'brc_motorista';
			register_taxonomy('brc_motorista', array('product'),
				array(
					'hierarchical'      => true,
					'labels'            => array(
						'name'              => "Motoristas",
						'singular_name'     => "Motorista",
						'search_items'      => "Procurar Motoristas",
						'all_items'         => "Todos(as) os(as) Motoristas",
						'parent_item'       => "Motorista Pai",
						'parent_item_colon' => "Motorista Pai:",
						'edit_item'         => "Editar Motorista",
						'update_item'       => "Atualizar Motorista",
						'add_new_item'      => "Adicionar novo(a) Motorista",
						'new_item_name'     => "Novo nome de Motorista",
						'menu_name'         => "Motoristas",
					),
					'show_ui'           => true,
					'show_admin_column' => true,
					'query_var'         => true,
					'rewrite'           => array( 'slug' => 'motorista' ),
				)
			);
			add_action('cmb2_meta_boxes', array($this, $taxonomy.'_edit_register_custom_metaboxes'));
		}
		public function brc_motorista_edit_register_custom_metaboxes(){
			$taxonomy = 'brc_motorista';
			$prefix = get_cmb2_term_metaboxes_prefix($taxonomy);
			$general = new_cmb2_box(array(
				'id' 			=> $prefix . 'geral', // brc_prod_geral
				'title'         => 'Dados do(a) Motorista',
				'object_types'  => array('term'),
				'taxonomies'  	=> array($taxonomy),
			));
			
			$telefone = $general->add_field( array(
				'name'    		=> 'Telefone',
				'id'      		=> $prefix.'telefone',
				'type'    		=> 'text',
			));
			$email = $general->add_field( array(
				'name'    		=> 'E-mail',
				'id'      		=> $prefix.'email',
				'type'    		=> 'text_email',
			));
			$documento = $general->add_field( array(
				'name'    		=> 'Documento CNH',
				'id'      		=> $prefix.'documento',
				'type'    		=> 'text',
			));
		}
		
		// .FEPATaxMotorista
	}
?>