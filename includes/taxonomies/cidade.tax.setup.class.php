<?php
	class FEPATaxCidade{
		protected static $instance = null;
		private $taxonomy = 'brc_cidade';
		private $slug = 'cidade';
		
		function __construct(){
			self::reg_brc_cidade();
			add_filter('manage_edit-'. $this->taxonomy .'_columns', array($this, 'brc_columns'), 1);
			add_filter('manage_'. $this->taxonomy .'_custom_column', array($this, 'brc_columns_callback'),10,3);
			add_action('edited_'. $this->taxonomy, array($this, 'brc_edited'), 10, 2);
			add_action('saved_term', array($this, 'brc_saved_term'), 10, 3);
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
		 * Add taxonomy ´brc_cidade´ 
		 * 
		 */
		private function reg_brc_cidade(){
			register_taxonomy($this->taxonomy, array('product'),
				array(
					'hierarchical'      => false,
					'labels'            => array(
						'name'              => "Cidades",
						'singular_name'     => "Cidade",
						'search_items'      => "Procurar Cidades",
						'all_items'         => "Todos as Cidades",
						'parent_item'       => "Destino Cidade",
						'parent_item_colon' => "Destino Cidade:",
						'edit_item'         => "Editar Cidade",
						'update_item'       => "Atualizar Cidade",
						'add_new_item'      => "Adicionor nova Cidade",
						'new_item_name'     => "Novo nome de Cidade",
						'menu_name'         => "Cidades",
					),
					'show_ui'           => true,
					'show_admin_column' => true,
					'query_var'         => true,
					'rewrite'           => array( 'slug' => $this->slug ),
				)
			);
			add_action($this->taxonomy.'_edit_form', array($this, 'brc_add_form_fields_table_html'));
			add_action($this->taxonomy.'_add_form_fields', array($this, 'brc_add_form_fields_html'));
		}
		
		/**
		 * Add custom fields
		 * taxonomy ´brc_cidade´ 
		 * 
		 */
		public function brc_add_form_fields_table_html(){
			include FEPA_PLUGIN_DIR . '/includes/taxonomies/template/admin.cidade-fields-table.php';
		}
		public function brc_add_form_fields_html(){
			include FEPA_PLUGIN_DIR . '/includes/taxonomies/template/admin.cidade-fields.php';
		}
		
		/**
		 * Hide columns
		 * taxonomy ´brc_cidade´ 
		 * 
		 */
		public function brc_columns($columns){
			unset($columns['description']);
			unset($columns['slug']);
			$columns['posts'] = 'Quant.';
			
			$n_col = array(
				'cb' => $columns['cb'],
				'name' => $columns['name'],
				'uf' => 'Estado (UF)',
				'posts' => $columns['posts'],
			);
			
			return $n_col;
		}
		public function brc_columns_callback($content,$column_name,$term_id){
			switch($column_name){
				case 'uf':
					$brc_uf = get_term_meta($term_id, 'brc_uf', true);
					$ufs = get_uf($brc_uf);
					$content = $ufs['nome'].' - '.strtoupper($ufs['sigla']);
				break;
			}
			return $content;
		}
		
		/**
		 * Save additional data
		 * taxonomy ´brc_cidade´ 
		 * 
		 */
		public function brc_edited($term_id, $tt_id){
			$ufs = get_uf();
			if(isset($_POST['tag-brc_uf'])){
				update_term_meta($term_id, 'brc_uf', $_POST['tag-brc_uf']);
			}
		}
		public function brc_saved_term($term_id, $tt_id, $taxonomy){
			if($taxonomy == $this->taxonomy){
				$ufs = get_uf();
				if(isset($_POST['tag-brc_uf'])){
					update_term_meta($term_id, 'brc_uf', $_POST['tag-brc_uf']);
				}
			}
		}
		
		
		// .FEPATaxCidade
	}
?>