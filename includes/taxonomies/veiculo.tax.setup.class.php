<?php
	class FEPATaxVeiculo{
		protected static $instance = null;
		
		function __construct(){
			// add stuffs...
			self::reg_brc_veiculo();
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
		 * Add taxonomy ´brc_veiculo´ 
		 * 
		 */
		private function reg_brc_veiculo(){
			$taxonomy = 'brc_veiculo';
			register_taxonomy('brc_veiculo', array('product'),
				array(
					'hierarchical'      => true,
					'labels'            => array(
						'name'              => "Veículos",
						'singular_name'     => "Veículo",
						'search_items'      => "Procurar Veículos",
						'all_items'         => "Todos os Veículos",
						'parent_item'       => "Veículo Pai",
						'parent_item_colon' => "Veículo Pai:",
						'edit_item'         => "Editar Veículo",
						'update_item'       => "Atualizar Veículo",
						'add_new_item'      => "Adicionar novo Veículo",
						'new_item_name'     => "Novo nome de Veículo",
						'menu_name'         => "Veículos",
					),
					'show_ui'           => true,
					'show_admin_column' => true,
					'query_var'         => true,
					'rewrite'           => array('slug' => 'veiculo'),
				)
			);
			add_action('cmb2_meta_boxes', array($this, $taxonomy.'_edit_register_custom_metaboxes'));
		}
		public function brc_veiculo_edit_register_custom_metaboxes(){
			$taxonomy = 'brc_veiculo';
			$prefix = get_cmb2_term_metaboxes_prefix($taxonomy);
			$general = new_cmb2_box(array(
				'id' 			=> $prefix . 'geral',
				'title'         => 'Dados do Veículo',
				'object_types'  => array('term'),
				'taxonomies'  	=> array($taxonomy),
			));
			
			$placa = $general->add_field( array(
				'name'    		=> 'Placa',
				'id'      		=> $prefix.'placa',
				'type'    		=> 'text',
			));
			$empresa = $general->add_field( array(
				'name'    		=> 'Empresa',
				'id'      		=> $prefix.'empresa',
				'type'    		=> 'text',
			));
			$acomodacoes = $general->add_field(array(
				'name'           	=> 'Acomodações',
				'id'             	=> $prefix.'acomodacoes',
				'type'           	=> 'select',
				'show_option_none' 	=> true,
				'options'          	=> get_acomodacoes(),
			));
			$brindes = $general->add_field( array(
				'name'    => 'Brindes',
				'desc'    => 'É distribuido brindes?',
				'id'      => $prefix.'brindes',
				'type'    => 'checkbox',
			));
			$wifi = $general->add_field( array(
				'name'    => 'Wifi',
				'desc'    => 'O veículo possui Wifi liberado?',
				'id'      => $prefix.'wifi',
				'type'    => 'checkbox',
			));
			$tomadas = $general->add_field( array(
				'name'    => 'Tomadas',
				'desc'    => 'O veículo possui tomadas disponíveis para seus passageiros?',
				'id'      => $prefix.'tomadas',
				'type'    => 'checkbox',
			));
			$arcondicionado = $general->add_field( array(
				'name'    => 'Ar-condicionado',
				'desc'    => 'O veículo possui Ar-condicionado?',
				'id'      => $prefix.'arcondicionado',
				'type'    => 'checkbox',
			));
			$banheiro = $general->add_field( array(
				'name'    => 'Banheiro(s)',
				'desc'    => 'O veículo possui Banheiro(s) disponíveis para seus passageiros?',
				'id'      => $prefix.'banheiro',
				'type'    => 'checkbox',
			));
		}
		
		
		
		// .FEPATaxVeiculo
	}
?>