<?php
	class FEPATaxLinha{
		protected static $instance = null;
		
		function __construct(){
			// add stuffs...
			self::reg_brc_linhas();
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
		 * Add taxonomy ´brc_linhas´ 
		 * 
		 */
		private function reg_brc_linhas(){
			$taxonomy = 'brc_linhas';
			register_taxonomy($taxonomy, array('product'),
				array(
					'hierarchical'      => true,
					'labels'            => array(
						'name'              => "Linhas",
						'singular_name'     => "Linha",
						'search_items'      => "Procurar Linhas",
						'all_items'         => "Todas as Linhas",
						'parent_item'       => "Linha Pai",
						'parent_item_colon' => "Linha Pai:",
						'edit_item'         => "Editar Linha",
						'update_item'       => "Atualizar Linha",
						'add_new_item'      => "Adicionar nova Linha",
						'new_item_name'     => "Novo nome de Linha",
						'menu_name'         => "Linhas",
					),
					'show_ui'           => true,
					'show_admin_column' => true,
					'query_var'         => true,
					'rewrite'           => array( 'slug' => 'linha' ),
				)
			);
			add_action('cmb2_meta_boxes', array($this, $taxonomy.'_edit_register_custom_metaboxes'));
		}
		public function brc_linhas_edit_register_custom_metaboxes(){
			$taxonomy = 'brc_linhas';
			$prefix = get_cmb2_term_metaboxes_prefix($taxonomy);
			$general = new_cmb2_box(array(
				'id' 			=> $prefix . 'geral', // brc_prod_geral
				'title'         => 'Dados da Linha',
				'object_types'  => array('term'),
				'taxonomies'  	=> array($taxonomy),
			));
			$linha_ini = $general->add_field( array(
				'name'    		=> 'Início da Linha',
				'id'      		=> $prefix.'linha_ini',
				'type'    		=> 'text',
			));
			$linha_fim = $general->add_field( array(
				'name'    		=> 'Término da Linha',
				'id'      		=> $prefix.'linha_fim',
				'type'    		=> 'text',
			));
			$linha_grupo_paradas = $general->add_field(array(
				'id'          => 'linha_grupo_paradas',
				'type'        => 'group',
				'description' => 'Adicionar Pontos de Paradas',
				'options'     => array(
					'group_title'       => 'Ponto de Parada {#}',
					'add_button'        => 'Adicionar Ponto',
					'remove_button'     => 'Remover Ponto',
					'sortable'          => true,
				),
			));
			$general->add_group_field($linha_grupo_paradas, array(
				'name'    	=> 'Nome do Ponto',
				'desc' 		=> 'Descreva o nome ou como é conhecido o ponto.',
				'id'      	=> 'p-nome',
				'type'    	=> 'text',
			));
			$general->add_group_field($linha_grupo_paradas, array(
				'name'    	=> 'Endereço do Ponto',
				'desc' 		=> 'Informe o endereço completo do ponto.',
				'id'      	=> 'p-endereco',
				'type'    	=> 'text',
			));
			$general->add_group_field($linha_grupo_paradas, array(
				'name'    	=> 'Valor da passagem.',
				'desc' 		=> 'Informe o valor do custo do ponto de embarque. <br/><strong>Deixe em branco para pegar o valor normal da viagem.</strong>',
				'id'      	=> 'p-valor',
				'type'    	=> 'text',
			));
			$general->add_group_field($linha_grupo_paradas, array(
				'name'    	=> 'Tempo de viagem até o ponto.',
				'desc' 		=> 'Informe o valor em <strong>MINUTOS</strong> do tempo de viagem até o ponto, considerando a saída inicial da linha...',
				'id'      	=> 'p-tempo',
				'type'    	=> 'text',
			));
		}
		
		
		// .FEPATaxLinha
	}
?>