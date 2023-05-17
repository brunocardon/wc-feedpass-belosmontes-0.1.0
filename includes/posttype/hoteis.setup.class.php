<?php
	class FEPAHoteis{
		protected static $instance = null;
		
		private $valid_metaboxes = array(
			'submitdiv',
			'postimagediv',
			'brc_hotel_categdiv',
			'brc_hotel_localdiv',
			'brc_hoteis_acesso',
		);
		
		function __construct(){
			
			self::reg_brc_hoteis();
			add_action('hidden_meta_boxes', array($this, 'brc_hoteis_edit_hide_metaboxes'), 10, 2);
			add_action('cmb2_meta_boxes', array($this, 'brc_hoteis_custom_metaboxes_cmb'));
			
			add_action('template_redirect', array($this, 'brc_hotel_redirect'));
		}
		static function init(){
			if(null == self::$instance ){
				self::$instance = new self;
			}
			return self::$instance;
		}
		
		public function register(){
			// nothing to see here...
		}
		
		/**
		 * Register ´hoteis´ pos type
		 * 
		 */
		private function reg_brc_hoteis(){
			register_post_type('hoteis',
				array(
					'labels' => array(
						'name' 					=> 'Hoteis',
						'singular_name' 		=> 'Hotel',
						'menu_name' 			=> 'Hoteis',
						'name_admin_bar'		=> 'Hoteis',
						'all_items'				=> 'Todos os Hoteis',
						'add_new'				=> 'Adicionor novo Hotel',
						'add_new_item'       	=> 'Adicionor novo Hotel',
						'edit_item'				=> 'Editar Hotel',
						'new_item'				=> 'Novo Hotel',
						'view_item'				=> 'Ver Hotel',
						'search_items'			=> 'Procurar Hoteis',
						'not_found'				=> 'Nenhum Hotel encontrado',
						'not_found_in_trash'	=> 'Nenhum Hotel encontrado na lixeira',
						'parent_item_colon'		=> 'Hotel pai:',
					),
					'supports'				=> array('title', 'editor', 'thumbnail', 'excerpt'),
					'public' 				=> true,
					'publicly_queryable' 	=> true,
					'has_archive' 			=> true,
					'rewrite'          	 	=> array('slug' => 'hotel'),
					'menu_position'			=> 5,
					'menu_icon'				=> 'dashicons-admin-multisite',
				)
			);
		}
		
		public function brc_hoteis_custom_metaboxes_cmb(){
			$prefix = 'brc_hoteis_';
			$acesso = new_cmb2_box(array(
				'id' 			=> $prefix . 'acesso', // brc_prod_banner
				'title'         => 'Forma de acesso',
				'object_types'  => array('hoteis'),
			));
			
			$prefix = $prefix . 'acesso_';
			$cmb2Grid = new \Cmb2Grid\Grid\Cmb2Grid($acesso);
			
			$row[1] = $cmb2Grid->addRow();
			$url = $acesso->add_field(array(
				'name'    	=> 'URL do site do Hotel',
				'desc'   	=> 'Site ou página do Hotel na internet, facbook, instagram ou demais redes sociais.',
				'id'      	=> $prefix.'url',
				'type'    	=> 'text_url',
			));
			$redirect = $acesso->add_field(array(
				'name'    	=> 'Redirecionar',
				'desc'   	=> 'Caso ative esta opção, ao acessar o hotel será redirecionado para o link informado acima.',
				'id'      	=> $prefix.'redirect',
				'type'    	=> 'checkbox',
			));
		}
		public function brc_hotel_redirect($query){
			$post_id = get_the_ID();
			$post = get_post($post_id);
			if($post){
				$post_type = $post->post_type;
				if($post_type == 'hoteis' and is_singular('hoteis') and !is_admin()){
					$url = get_post_meta($post_id, 'brc_hoteis_acesso_url', true);
					
					if($url){
						$redirect = get_post_meta($post_id, 'brc_hoteis_acesso_redirect', true);
						if($redirect){
							wp_redirect($url);
							exit;
						}
					}
				}
			}
		}
		 
		/**
		 * Hide metaboxes
		 * Post Type: hoteis
		 * 
		 */
		public function brc_hoteis_edit_hide_metaboxes($hidden, $screen){
			global $wp_meta_boxes;
			
			$post_ID = get_the_ID();
			$product = wc_get_product($post_ID);
			
			if($screen->post_type == 'hoteis'){
				
				$post_metaboxes = $wp_meta_boxes[$screen->post_type];
				foreach($post_metaboxes as $k => $type){
					foreach($type as $kk => $place){
						foreach($place as $kkk => $prio){
							$all_metas[$kkk] = $kkk;
						}
					}
				}
				
				// REMOVE VALIDS
				foreach($this->valid_metaboxes as $k => $j){
					unset($all_metas[$j]);
				}
				
				$n_hidden = $all_metas;
				foreach($hidden as $k => $j){
					$n_hidden[$j] = $j;
				}
				
				
				return $n_hidden;
			}
			return $hidden;
		}
		
		
		// .FEPAHoteis
	}
?>