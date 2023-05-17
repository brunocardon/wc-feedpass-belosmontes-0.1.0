<?php
	class FEPATaxPontoEmbarque{
		protected static $instance = null;
		private $taxonomy = 'brc_ponto_embarque';
		private $slug = 'ponto_embarque';
		
		function __construct(){
			self::reg_brc_ponto_embarque();
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
		 * Add taxonomy ´brc_ponto_embarque´ 
		 * 
		 */
		private function reg_brc_ponto_embarque(){
			$this->taxonomy = 'brc_ponto_embarque';
			register_taxonomy($this->taxonomy, array('product'),
				array(
					'hierarchical'      => false,
					'labels'            => array(
						'name'              => "Pontos de embarque",
						'singular_name'     => "Ponto",
						'search_items'      => "Procurar Pontos",
						'all_items'         => "Todos os Pontos",
						'parent_item'       => "Ponto Pai",
						'parent_item_colon' => "Ponto Pai:",
						'edit_item'         => "Editar Pontos de embarque",
						'update_item'       => "Atualizar Pontos de embarque",
						'add_new_item'      => "Adicionor novo Pontos de embarque",
						'new_item_name'     => "Novo nome de Pontos de embarque",
						'menu_name'         => "Pontos de embarque",
					),
					'show_ui'           => true,
					'show_admin_column' => true,
					'query_var'         => true,
					'rewrite'           => array( 'slug' => 'ponto_embarque' ),
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
			include FEPA_PLUGIN_DIR . '/includes/taxonomies/template/admin.ponto_embarque-fields-table.php';
		}
		public function brc_add_form_fields_html(){
			include FEPA_PLUGIN_DIR . '/includes/taxonomies/template/admin.ponto_embarque-fields.php';
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
				'cidade' => 'Cidade',
				'posts' => $columns['posts'],
			);
			
			return $n_col;
		}
		public function brc_columns_callback($content,$column_name,$term_id){
			switch($column_name){
				case 'cidade':
					$brc_cidade = get_term_meta($term_id, 'brc_cidade', true);
					$cidade = get_term($brc_cidade);
					if(!is_wp_error($cidade)){
						
						$uf = get_term_meta($brc_cidade, 'brc_uf', true);
						$ufs = get_uf($uf);
						$content = $cidade->name.' / '.strtoupper($ufs['sigla']);
					}
					
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
				if(isset($_POST['tag-brc_cidade'])){
					update_term_meta($term_id, 'brc_cidade', $_POST['tag-brc_cidade']);
				}
			}
		}
		
		
		
		
		
		
		// .FEPATaxPontoEmbarque
	}
?>