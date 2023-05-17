<?php
	class FEPATaxLinhaViagem{
		protected static $instance = null;
		private $taxonomy = 'brc_linha_viagem';
		private $slug = 'linha_viagem';
		
		function __construct(){
			self::reg_brc_linha_viagem();
			add_filter('manage_edit-'. $this->taxonomy .'_columns', array($this, 'brc_columns'), 1);
			add_filter('manage_'. $this->taxonomy .'_custom_column', array($this, 'brc_columns_callback'),10,3);
			add_action('edited_'. $this->taxonomy, array($this, 'brc_edited'), 10, 2);
			add_action('saved_term', array($this, 'brc_saved_term'), 10, 3);
			
			add_action('wp_ajax_nopriv_brc_linha_viagem_lin_html', array($this, 'brc_linha_viagem_lin_html'));
			add_action('wp_ajax_brc_linha_viagem_lin_html', array($this, 'brc_linha_viagem_lin_html'));
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
		 * Add taxonomy ´brc_linha_viagem´ 
		 * 
		 */
		private function reg_brc_linha_viagem(){
			register_taxonomy($this->taxonomy, array('product'),
				array(
					'hierarchical'      => false,
					'labels'            => array(
						'name'              => "Linhas de Viagem",
						'singular_name'     => "Linha",
						'search_items'      => "Procurar Linhas",
						'all_items'         => "Todos as Linhas",
						'parent_item'       => "Linha Pai",
						'parent_item_colon' => "Linha Pai:",
						'edit_item'         => "Editar Linha de Viagem",
						'update_item'       => "Atualizar Linha de Viagem",
						'add_new_item'      => "Adicionor nova Linha de Viagem",
						'new_item_name'     => "Novo nome de Linha de Viagem",
						'menu_name'         => "Linhas de Viagem",
					),
					'show_ui'           => true,
					'show_admin_column' => true,
					'query_var'         => true,
					'rewrite'           => array( 'slug' => 'linha_viagem' ),
				)
			);
			add_action($this->taxonomy.'_edit_form', array($this, 'brc_add_form_fields_table_html'));
			add_action($this->taxonomy.'_add_form_fields', array($this, 'brc_add_form_fields_html'));
		}
		
		/**
		 * Add custom fields
		 * taxonomy ´brc_linha_viagem´ 
		 * 
		 */
		public function brc_add_form_fields_table_html(){
			include FEPA_PLUGIN_DIR . '/includes/taxonomies/template/admin.linha_viagem-fields-table.php';
		}
		public function brc_add_form_fields_html(){
			include FEPA_PLUGIN_DIR . '/includes/taxonomies/template/admin.linha_viagem-fields.php';
		}
		
		/**
		 * Hide columns
		 * taxonomy ´brc_linha_viagem´ 
		 * 
		 */
		public function brc_columns($columns){
			unset($columns['description']);
			unset($columns['slug']);
			$columns['posts'] = 'Quant.';
			
			$n_col = array(
				'cb' => $columns['cb'],
				'name' => $columns['name'],
				'pontos' => 'Pontos',
				'posts' => $columns['posts'],
			);
			
			return $n_col;
		}
		public function brc_columns_callback($content,$column_name,$term_id){
			switch($column_name){
				case 'pontos':
					$brc_ponto = get_term_meta($term_id, 'brc_ponto', true);
					if($brc_ponto){
						
						
						if(!is_wp_error($terms)){
							foreach($brc_ponto as $k => $j){
								$brc_ponto_embarque = get_term($j['brc_ponto'], 'brc_ponto_embarque');
								$brc_ponto_embarque_cidade = get_term_meta($brc_ponto_embarque->term_id, 'brc_cidade', true);
								
								$brc_cidade = get_term($brc_ponto_embarque_cidade, 'brc_cidade');
								$brc_cidade_uf = get_term_meta($brc_cidade->term_id, 'brc_uf', true);
								$ufs = get_uf($brc_cidade_uf);
								
								$name = $brc_ponto_embarque->name.' - '. $brc_cidade->name.' / '.strtoupper($ufs['sigla']);
								echo $name.' - '.$j['brc_ponto_time'].' <strong>[ valor: '. wc_price($j['brc_ponto_valor']) .' ]</strong><br/>';
							}
						}
					}
				break;
			}
			return $content;
		}
		
		/**
		 * Save additional data
		 * taxonomy ´brc_linha_viagem´ 
		 * 
		 */
		public function brc_edited($term_id, $tt_id){
			$ufs = get_uf();
			if(isset($_POST['tag_brc_ponto'])){
				foreach($_POST['tag_brc_ponto'] as $k => $j){
					$ponto[] = array(
						'brc_ponto' => $j,
						'brc_ponto_time' => $_POST['tag_brc_ponto_time'][$k],
						'brc_ponto_valor' => $_POST['tag_brc_ponto_valor'][$k],
					);
				}
				update_term_meta($term_id, 'brc_ponto', $ponto);
			}
		}
		public function brc_saved_term($term_id, $tt_id, $taxonomy){
			if($taxonomy == $this->taxonomy){
				if(isset($_POST['tag_brc_ponto'])){
					foreach($_POST['tag_brc_ponto'] as $k => $j){
						$ponto[] = array(
							'brc_ponto' => $j,
							'brc_ponto_time' => $_POST['tag_brc_ponto_time'][$k],
							'brc_ponto_valor' => $_POST['tag_brc_ponto_valor'][$k],
						);
					}
					update_term_meta($term_id, 'brc_ponto', $ponto);
				}
			}
		}
		
		/**
		 * Generate line HTML
		 * taxonomy ´brc_linha_viagem´ 
		 * 
		 */
		public function brc_linha_viagem_lin_html(){
			ob_start();
			include FEPA_PLUGIN_DIR . '/includes/taxonomies/template/admin.linha_viagem-fields_pontos_linhas.php';
			$html = ob_get_contents();
			ob_end_clean();
			
			$ret['html'] = $html;
			$ret['sts'] = true;
			
			echo json_encode($ret);
			exit;
		}
		
		
		
		
		
		
		
		
		
		// .FEPATaxLinhaViagem
	}
?>