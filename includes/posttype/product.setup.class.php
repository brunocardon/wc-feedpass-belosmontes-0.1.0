<?php
	class FEPAPostTypeProduct{
		protected static $instance = null;
		
		private $valid_metaboxes = array(
			'submitdiv',
			'brc_product_tipo',
			'brc_product_edit',
			'brc_product_grupo',
			'brc_product_ab',
			'brc_product_clientes'
		); 
		
		function __construct(){
			// add stuffs...
			
			add_action('init', array($this, 'brc_set_menu_labels'), 5);
			add_action('admin_head', array($this, 'brc_product_edit_remove_editor'), 0);
			add_action('admin_menu', array($this, 'brc_product_menu_excursoes'), 5);
			
			add_action('get_user_option_screen_layout_product', array($this, 'brc_product_edit_screen_layout'));
			add_action('add_meta_boxes', array($this, 'brc_product_custom_metaboxes'));
			add_action('hidden_meta_boxes', array($this, 'brc_product_edit_hide_metaboxes'), 10, 2);
			add_action('product_type_selector', array($this, 'brc_product_edit_remove_product_types'));
			add_action('page_row_actions', array($this, 'brc_product_edit_remove_view'), 10, 2);
			add_action('post_row_actions', array($this, 'brc_product_edit_remove_view'), 10, 2);
			add_action('get_user_metadata', array($this, 'brc_product_edit_order_metaboxes'), 10, 3);
			
			add_action('cmb2_meta_boxes', array($this, 'brc_product_custom_metaboxes_cmb'));
			
			add_action('pre_get_posts', array($this, 'brc_product_list_order'));
			add_action('manage_posts_extra_tablenav', array($this, 'brc_order_list_legendas'));
			add_action('views_edit-product', array($this, 'brc_product_list_filter_link'), 1);
			
			add_action('manage_edit-product_columns', array($this, 'brc_product_columns_manage_edit'), 1000);
			add_action('manage_product_posts_custom_column', array($this, 'brc_product_columns_manage_edit_callback'), 1000, 1);
			
			add_action('wp_ajax_nopriv_brc_add_product_excursao_add_variation', 	array($this, 'brc_add_product_excursao_add_variation'));
			add_action('wp_ajax_brc_add_product_excursao_add_variation', 			array($this, 'brc_add_product_excursao_add_variation'));
			add_action('wp_ajax_nopriv_brc_add_product_excursao_remove_variation', 	array($this, 'brc_add_product_excursao_remove_variation'));
			add_action('wp_ajax_brc_add_product_excursao_remove_variation', 		array($this, 'brc_add_product_excursao_remove_variation'));
			
			
			add_action('wp_ajax_nopriv_brc_add_product_excursao_add_child', 	array($this, 'brc_add_product_excursao_add_child'));
			add_action('wp_ajax_brc_add_product_excursao_add_child', 			array($this, 'brc_add_product_excursao_add_child'));
		}
		
		static function init(){
			if(null == self::$instance ){
				self::$instance = new self;
			}
			return self::$instance;
		}
		
		public function register(){
			// nothing to see here...
			
			FEPAHoteis::init();
			FEPAPostTypeSavingProduct::init();
		}
		
		public function brc_set_menu_labels(){
			
			$produto_object 				= get_post_type_object('product');
			$produto_object->label 			= 'Viagens';
			$produto_object->description 	= 'Aqui você pode adicionar novas viagens para busca.';
			$produto_object->supports 		= array('title');
			$plabels 						= $produto_object->labels;
			
			foreach($produto_object->labels as $pk => $pj){
				if(
					isset($_GET['metatype']) and 
					$_GET['metatype']=='excursoes' and 
					($pk=='name' or $pk=='add_new_item')
				){
					$plabels->{$pk} = str_replace("produtos", 		"excursões", $plabels->{$pk});
					$plabels->{$pk} = str_replace("Produtos", 		"Excursões", $plabels->{$pk});
					$plabels->{$pk} = str_replace("produto", 		"excursão", $plabels->{$pk});
					$plabels->{$pk} = str_replace("Produto", 		"Excursão", $plabels->{$pk});
					
					$plabels->{$pk} = str_replace("novo", 			"nova", $plabels->{$pk});
					$plabels->{$pk} = str_replace("Nenhum", 		"Nenhuma", $plabels->{$pk});
					$plabels->{$pk} = str_replace("encontrado", 	"encontrada", $plabels->{$pk});
					$plabels->{$pk} = str_replace("Todos", 			"Todas", $plabels->{$pk});
					$plabels->{$pk} = str_replace("os", 			"as", $plabels->{$pk});
					$plabels->{$pk} = str_replace("no ", 			"na ", $plabels->{$pk});
					$plabels->{$pk} = str_replace("este ", 			"esta ", $plabels->{$pk});
					$plabels->{$pk} = str_replace("do ", 			"da ", $plabels->{$pk});
				}else{
					$plabels->{$pk} = str_replace("produtos", 		"viagens", $plabels->{$pk});
					$plabels->{$pk} = str_replace("Produtos", 		"Viagens", $plabels->{$pk});
					$plabels->{$pk} = str_replace("produto", 		"viagem", $plabels->{$pk});
					$plabels->{$pk} = str_replace("Produto", 		"Viagem", $plabels->{$pk});
					
					$plabels->{$pk} = str_replace("novo", 			"nova", $plabels->{$pk});
					$plabels->{$pk} = str_replace("Nenhum", 		"Nenhuma", $plabels->{$pk});
					$plabels->{$pk} = str_replace("encontrado", 	"encontrada", $plabels->{$pk});
					$plabels->{$pk} = str_replace("Todos", 			"Todas", $plabels->{$pk});
					$plabels->{$pk} = str_replace("os", 			"as", $plabels->{$pk});
					$plabels->{$pk} = str_replace("no ", 			"na ", $plabels->{$pk});
					$plabels->{$pk} = str_replace("este ", 			"esta ", $plabels->{$pk});
					$plabels->{$pk} = str_replace("do ", 			"da ", $plabels->{$pk});
				}
			}
			$produto_object->labels = $plabels;
		}
		
		/**
		 * Add the product division in to Excursões and Viagens
		 * 
		 */
		public function brc_product_menu_excursoes(){
			add_menu_page(
				'Todas Excursões',
				'Excursões',
				'manage_options',
				'edit.php?post_type=product&metatype=excursoes',
				'',
				'dashicons-admin-generic',
				56
			);
			add_submenu_page(
				'edit.php?post_type=product&metatype=excursoes', 		// parent_slug
				'Adicionar Excursão', 									// page_title
				'Adicionar Excursão', 									// menu_title
				'manage_options', 										// capability
				'post-new.php?post_type=product&metatype=excursoes', 	// menu_slug
				'', 													// function
				2 														// position
			);
		}
		
		/**
		 * Set the number of columns of the page edit product post type
		 * 
		 */
		public function brc_product_edit_screen_layout(){
			return 1;
		}
		
		/**
		 * Add special metaboxes 
		 * Post Type: product: excursões
		 * 
		 */
		public function brc_product_custom_metaboxes(){
			
			$post_ID = get_the_ID();
			$product = wc_get_product($post_ID);
			$brc_excursao = get_post_meta($post_ID, 'brc_excursao', true);
			$metatype_excursoes = (isset($_GET['metatype']) and $_GET['metatype']=='excursoes');
			
			// REGISTA A METABOX
			add_meta_box(
				'brc_product_tipo',
				'Escolha o tipo de cadastro',
				array($this, 'brc_product_tipo_custom_metaboxes_html'),
				'product',
				'normal',
				'high'
			);
			add_meta_box(
				'brc_product_edit',
				'Dados da '.(($brc_excursao or $metatype_excursoes)?"Excursão":"Viagem - Bate e Volta"),
				array($this, 'brc_product_custom_metaboxes_html'),
				'product',
				'normal',
				'high'
			);
			add_meta_box(
				'brc_product_grupo',
				'Dados da Viagem - Grupo de viagem',
				array($this, 'brc_product_grupo_metaboxes_html'),
				'product',
				'normal',
				'high'
			);
			add_meta_box(
				'brc_product_ab',
				'Dados da Viagem - A B com escolha de poltronas',
				array($this, 'brc_product_ab'),
				'product',
				'normal',
				'high'
			);
			add_meta_box(
				'brc_product_clientes',
				'Lista de '.(($brc_excursao)?"Hóspedes":"Passageiros"),
				array($this, 'brc_product_clientes_custom_metaboxes_html'),
				'product',
				'normal',
				'high'
			);
		}
		
		/**
		 * Add special metaboxes 
		 * Post Type: product: excursões
		 * 
		 */
		public function brc_product_custom_metaboxes_cmb(){
			
			// IMAGEM BANNER
			$prefix = get_cmb2_product_metaboxes_prefix();
			$general = new_cmb2_box(array(
				'id' 			=> $prefix . 'banner', // brc_prod_banner 
				'title'         => 'Banner da Excursão',
				'object_types'  => array('product'),
			));
			$imagem_capa = $general->add_field(array(
				'name'    => 'Imagem de capa',
				'desc'    => 'Imagem para ilustrar o banner da página da Excursão.',
				'id'      => $prefix.'imagem_capa',
				'type'    => 'file',
				'text'    => array(
					'add_upload_file_text' => 'Enviar imagem'
				),
				'preview_size' => 'medium',
			));
			
			//------------------------------------------
			// DADOS DA HOSPEDAGEM
			$prefix = get_cmb2_product_metaboxes_prefix();
			$hospedagem = new_cmb2_box(array(
				'id' 			=> $prefix . 'hospedagem', // brc_prod_banner
				'title'         => 'Dados da Hospedagem',
				'object_types'  => array('product'),
			));
			$prefix = $prefix . 'hospedagem_';
			$cmb2Grid = new \Cmb2Grid\Grid\Cmb2Grid($hospedagem);
			
			$row[1] = $cmb2Grid->addRow();
			$noites 		= $hospedagem->add_field(array(
				'name'    	=> 'Quantidade de noites',
				//'desc'    => 'Imagem para ilustrar o banner da página da Excursão.',
				'id'      	=> $prefix.'noites',
				'type'    	=> 'text',
			));
			$dormitorio 	= $hospedagem->add_field(array(
				'name'    	=> 'Tipo de dormitório',
				//'desc'    => 'Imagem para ilustrar o banner da página da Excursão.',
				'id'      	=> $prefix.'dormitorio',
				'type'    	=> 'text',
			));
			$row[1]->addColumns(array(
			   array($noites, 'class' => 'col-md-6'),
			   array($dormitorio, 'class' => 'col-md-6'),
			));
			
			$row[2] = $cmb2Grid->addRow();
			$servicos 		= $hospedagem->add_field(array(
				'name'    	=> 'Serviços Inclusos',
				'desc'   	=> 'Serviços oferecidos durante a estadia.',
				'id'      	=> $prefix.'servicos',
				'type'    	=> 'multicheck_inline',
				'options' 	=> get_servicos(),
			));
			$row[2]->addColumns(array(
			   array($servicos, 'class' => 'col-md-12'),
			));
			
			//------------------------------------------
			// DADOS DO ROTEIRO
			$prefix = get_cmb2_product_metaboxes_prefix();
			$roteiro = new_cmb2_box(array(
				'id' 			=> $prefix . 'roteiro', // brc_prod_banner
				'title'         => 'Dados do Roteiro',
				'object_types'  => array('product'),
			));
			$prefix = $prefix . 'roteiro_';
			
			$roteiro_group = $roteiro->add_field(array(
				'id'          => 'roteiro_group',
				'type'        => 'group',
				'description' => 'Adicionar etapas do roteiro',
				'options'     => array(
					'group_title'       => 'Etapa {#}',
					'add_button'        => 'Adicionar etapa',
					'remove_button'     => 'Remover etapa',
					'closed' 			=> true,
					'sortable'          => true,
				),
			));
			$roteiro->add_group_field($roteiro_group, array(
				'name'    	=> 'Nome da Etapa',
				'desc' 		=> 'Ex.: Dia 01',
				'id'      	=> 'etapa-nome',
				'type'    	=> 'text',
			));
			$roteiro->add_group_field($roteiro_group, array(
				'name'    	=> 'Título da Etapa',
				'desc' 		=> 'Ex.: Passeio pela cidade',
				'id'      	=> 'etapa-titulo',
				'type'    	=> 'text',
			));
			$roteiro->add_group_field($roteiro_group, array(
				'name'    	=> 'Descrição da Etapa',
				'desc' 		=> 'Descreva em poucas palávras o que será feito no dia/etapa.',
				'id'      	=> 'etapa-desc',
				'type'    	=> 'textarea_small',
			));
		}
		
		public function brc_product_custom_metaboxes_html(){
			$post_ID = get_the_ID();
			$product = wc_get_product($post_ID);
			$brc_excursao = get_post_meta($post_ID, 'brc_excursao', true);
			
			if($brc_excursao){
				include FEPA_PLUGIN_DIR . '/templates/brc_admin.product_excursao_fields.php';
			}else{
				include FEPA_PLUGIN_DIR . '/templates/brc_admin.product_viagem_fields.php';
			}
		}
		public function brc_product_grupo_metaboxes_html(){
			include FEPA_PLUGIN_DIR . '/templates/brc_admin.product_viagem_grupo_fields.php';
		}
		public function brc_product_ab(){
			include FEPA_PLUGIN_DIR . '/templates/brc_admin.product_viagem_ab_fields.php';
		}
		public function brc_product_clientes_custom_metaboxes_html(){
			$post_ID = get_the_ID();
			$product = wc_get_product($post_ID);
			$brc_excursao = get_post_meta($post_ID, 'brc_excursao', true);
			
			if($brc_excursao){
				include FEPA_PLUGIN_DIR . '/templates/brc_admin.product_excursao_clientes.php';
			}else{
				include FEPA_PLUGIN_DIR . '/templates/brc_admin.product_viagem_clientes.php';
			}
		}
		public function brc_product_tipo_custom_metaboxes_html(){
			include FEPA_PLUGIN_DIR . '/templates/brc_admin.product_viagem_tipo.php';
		}
		
		/**
		 * Remove content box editor
		 * Post Type: product
		 * 
		 */
		public function brc_product_edit_remove_editor(){
			$post_ID = get_the_ID();
			$product = wc_get_product($post_ID);
			$brc_excursao = get_post_meta($post_ID, 'brc_excursao', true);
			$metatype_excursoes = (isset($_GET['metatype']) and $_GET['metatype']=='excursoes');
			
			if(!$brc_excursao){
				remove_post_type_support('product', 'editor');
				remove_post_type_support('product', 'thumbnail');
				remove_post_type_support('product', 'post-thumbnails');
			}
		}
		
		/**
		 * Hide metaboxes
		 * Post Type: product
		 * 
		 */
		public function brc_product_edit_hide_metaboxes($hidden, $screen){
			global $wp_meta_boxes;
			
			$post_ID = get_the_ID();
			$product = wc_get_product($post_ID);
			$brc_excursao = get_post_meta($post_ID, 'brc_excursao', true);
			$brc_viagem_tipo_cadastro = get_post_meta($post_ID, 'brc_viagem_tipo_cadastro', true);
			$metatype_excursoes = (isset($_GET['metatype']) and $_GET['metatype']=='excursoes');
			
			if($screen->post_type == 'product'){
				
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
				
				// ESCOLHER TIPO
				if(!$brc_viagem_tipo_cadastro){
					$n_hidden['brc_product_edit'] 			= 'brc_product_edit';
					$n_hidden['brc_product_grupo'] 			= 'brc_product_grupo';
					$n_hidden['brc_product_ab'] 			= 'brc_product_ab';
				}else{
					$n_hidden['brc_product_edit'] 			= 'brc_product_edit';
					$n_hidden['brc_product_grupo'] 			= 'brc_product_grupo';
					$n_hidden['brc_product_ab'] 			= 'brc_product_ab';
					
					if($brc_viagem_tipo_cadastro=='0' or $brc_viagem_tipo_cadastro=='1')
						unset($n_hidden['brc_product_edit']);
					
					if($brc_viagem_tipo_cadastro=='2')
						unset($n_hidden['brc_product_grupo']);
					
					if($brc_viagem_tipo_cadastro=='3')
						unset($n_hidden['brc_product_ab']);
				}
				
				if($brc_excursao){
					unset($n_hidden['brc_product_edit']);
					unset($n_hidden['wpb_visual_composer']);
					unset($n_hidden['postimagediv']);
					unset($n_hidden['brc_prod_banner']);
					unset($n_hidden['brc_prod_hospedagem']);
					unset($n_hidden['brc_prod_roteiro']);
					$n_hidden['brc_product_tipo'] = 'brc_product_tipo';
				}else{
					$n_hidden['wpb_visual_composer'] 	= 'wpb_visual_composer';
					$n_hidden['postimagediv'] 			= 'postimagediv';
					$n_hidden['brc_prod_banner'] 		= 'brc_prod_banner';
					$n_hidden['brc_prod_hospedagem'] 	= 'brc_prod_hospedagem';
					$n_hidden['brc_prod_roteiro'] 		= 'brc_prod_roteiro';
				}
				
				// SEMPRE MOSTRAR LISTA DE CLIENTES 
				// PARA PRODUTOS JÁ CRIADOS
				unset($n_hidden['brc_product_clientes']);
				
				if($screen->action == 'add'){
					$n_hidden['brc_product_clientes'] = 'brc_product_clientes';
					if($metatype_excursoes){
						unset($n_hidden['brc_product_edit']);
						unset($n_hidden['wpb_visual_composer']);
						unset($n_hidden['postimagediv']);
						unset($n_hidden['brc_prod_banner']);
						unset($n_hidden['brc_prod_hospedagem']);
						unset($n_hidden['brc_prod_roteiro']);
						$n_hidden['brc_product_tipo'] = 'brc_product_tipo';
					}
				}
			}else{
				$n_hidden = $hidden;
			}
			
			return $n_hidden;
		} 
		
		/**
		 * Remove some more product options
		 * Post Type: product
		 * 
		 */
		public function brc_product_edit_remove_product_types($types){
			unset($types['grouped']);
			unset($types['external']);
			return $types;
		}
		
		/**
		 * Remove "view product"
		 * Post Type: product
		 * 
		 */
		public function brc_product_edit_remove_view($actions, $post){
			$metatype_excursoes = (isset($_GET['metatype']) and $_GET['metatype']=='excursoes');
			$screen = get_current_screen();
			if(get_post_type(get_the_ID()) == 'product' and $screen->parent_base == 'edit'){
				if(!$metatype_excursoes){
					unset($actions['inline hide-if-no-js']);
					unset($actions['view']);
				}
			}
			return $actions;
		}
		
		/**
		 * Order of metaboxes
		 * Post Type: product
		 */
		function brc_product_edit_order_metaboxes($value, $user_id, $key){
			$new_value = $value;
			if($key == 'meta-box-order_product'){
				
				$side = array(
						'radio-brc_linhasdiv',
						'radio-brc_veiculodiv',
						'radio-brc_motoristadiv',
						
						'brc_linhasdiv',
						'brc_veiculodiv',
						'brc_motoristadiv',
						'brc_prod_geral',
					'wpb_visual_composer',
						'brc_product_clientes',
						'brc_product_tipo',
						'brc_product_edit',
						'brc_product_grupo',
						'brc_product_ab',
					'woocommerce-product-data',
						'brc_destinosdiv', 					// hide
						'brc_prod_hospedagem', 				// hide
						'brc_prod_roteiro', 				// hide
					'postimagediv', 						// hide
						'brc_prod_banner', 					// hide
					'woocommerce-product-images', 			// hide
					'postexcerpt', 							// hide
					'submitdiv',
						'brc_product_edit_passageiros',
				);
				
				$new_order = array(
					'advanced' => '',
					'side' => implode(',', $side),
					'normal' => 'postcustom,slugdiv,mymetabox_revslider_0',
				);
				$new_value = array($new_order);
			}
			return $new_value;
		} 
		
		/**
		 * Lists, filters
		 * Post Type: product
		 * 
		 */
		public function brc_product_list_order($query){
			$post_type = $query->get('post_type');
			
			if($post_type == 'product' and is_admin()){
				$metatype_excursoes = (isset($_GET['metatype']) and $_GET['metatype']=='excursoes');
				
				if(!$query->query_vars['for_count']){
					if($metatype_excursoes){
						$query->set('meta_query', array(
							'relation' => 'AND',
							array(
								'key'     => 'brc_excursao',
								'value'   => 1,
								'compare' => '=',
							),
						));
						if($query->get('orderby') == ''){
							$query->set('orderby', 'meta_value');
							$query->set('meta_key', 'brc_excursao_data');
							$query->set('order', 'ASC');
						}
					}
					else{
						if(!$query->query_vars['for_order']){
							$query->set('meta_query', array(
								'relation' => 'AND',
								array(
									'key'     => 'brc_excursao',
									'value'   => 1,
									'compare' => '<',
								),
							));
							if(isset($_GET['tipo'])){
								if($_GET['tipo'] == '1'){
									$query->set('meta_query', array(
										'relation' => 'AND',
										array(
											'key' 			=> 'brc_viagem_tipo_cadastro',
											'compare' 		=> '=',
											'value' 		=> '1',
										),
									));
									$query->set('tipo', '1');
								}
								if($_GET['tipo'] == '2'){
									$query->set('meta_query', array(
										'relation' => 'AND',
										array(
											'key' 			=> 'brc_viagem_tipo_cadastro',
											'compare' 		=> '=',
											'value' 		=> '2',
										),
									));
									$query->set('tipo', '2');
								}
								if($_GET['tipo'] == '3'){
									$query->set('meta_query', array(
										'relation' => 'AND',
										array(
											'key' 			=> 'brc_viagem_tipo_cadastro',
											'compare' 		=> '=',
											'value' 		=> '3',
										),
									));
									$query->set('tipo', '3');
								}
							}
							if(isset($_GET['novas'])){
								$query->set('meta_query', array(
									'relation' => 'AND',
									array(
										'key'     => 'brc_viagem_ida_data',
										'value'   => current_time('timestamp'),
										'compare' => '>=',
									),
								));
							}
							
							if(
								!isset($_GET['novas']) and 
								!isset($_GET['tipo']) and 
								!isset($_GET['all_posts']) 
							){
								$query->set('meta_query', array(
									'relation' => 'AND',
									array(
										'key'     => 'brc_viagem_ida_data',
										'value'   => current_time('timestamp'),
										'compare' => '>=',
									),
								));
							}
							
							if($query->get('orderby') == ''){
								$query->set('orderby', 'meta_value');
								$query->set('meta_key', 'brc_viagem_ida_data');
							}
							if($query->get('order') == ''){
								$query->set('order', 'ASC');
							}
						}
					}
					
					if($query->get('order') == ''){
						$query->set('order', 'DESC');
					}
				}
			}
			
		}
		public function brc_order_list_legendas(){
			global $wp_query;
			$post_type = $wp_query->get('post_type');
			
			$metatype_excursoes = (isset($_GET['metatype']) and $_GET['metatype']=='excursoes');
			if($metatype_excursoes)
				return false;
			
			if($post_type == 'product'){
			?>
				<div class="brc_list_legendas">
					<ul>
						<li><span class="fa fa-clipboard"></span> Imprimir lista de passageiros / hóspedes</li>
						<li><span class="fa fa-ticket"></span> Imprimir bilhetes de passagem</li>
					</ul>
				</div>
			<?php
			}
		}
		public function brc_product_list_filter_link($views){
			global $wp_query;
			$metatype_excursoes = (isset($_GET['metatype']) and $_GET['metatype']=='excursoes');
			
			if($metatype_excursoes)
				return $views;
			
			if(is_admin()){
				
				//--------------
				// VIAGENS FUTURAS
				$novas_viagens_query = array(
					'post_type'   		=> 'product',
					'posts_per_page' 	=> -1,
					'meta_query' 		=> array(
						'relation' 		=> 'AND',
						array(
							'key' 			=> 'brc_viagem_ida_data',
							'compare' 		=> '>=',
							'value' 		=> current_time('timestamp'),
						),
					),
					'for_count' => true,
				);
				$novas_viagens_results = new WP_Query($novas_viagens_query);
				$class = isset($_GET['novas'])?'current':'';
				
				if(
					!isset($_GET['novas']) and 
					!isset($_GET['tipo']) and 
					!isset($_GET['all_posts']) 
				){
					$class = 'current';
				}
				
				// FILTER LINK
				$n_views['novas'] = sprintf(
					'<a href="%s" class="'. $class .'">Próximas Viagens <span class="count">(%d)</span></a>',
					admin_url('edit.php?post_type=product&novas=1'),
					$novas_viagens_results->found_posts
				);
				//--------------
				//--------------
				// TODAS VIAGENS REFORMADO
				$all_viagens_query = array(
					'post_type'   		=> 'product',
					'posts_per_page' 	=> -1,
					'for_count' 		=> true,
				);
				$all_viagens_results = new WP_Query($all_viagens_query);
				$class = isset($_GET['all_posts'])?'current':'';
				
				// FILTER LINK
				unset($views['all']);
				unset($views['publish']);
				$n_views['nall'] = sprintf(
					'<a href="%s" class="'. $class .'">Todas viagens <span class="count">(%d)</span></a>',
					admin_url('edit.php?post_type=product&all_posts=1'),
					$all_viagens_results->found_posts
				);
				//--------------
				//--------------
				// Bate e Volta
				$tipo_viagens_bv_query = array(
					'post_type'   		=> 'product',
					'posts_per_page' 	=> -1,
					'meta_query' 		=> array(
						'relation' 		=> 'AND',
						array(
							'key' 			=> 'brc_viagem_tipo_cadastro',
							'compare' 		=> '=',
							'value' 		=> '1',
						),
					),
					'for_count' => true,
				);
				
				$tipo_viagens_bv_results = new WP_Query($tipo_viagens_bv_query);
				$class = 'brc_admin_btn btn_viagem_tipo_bv brc_admin_list_btn button ';
				$class .= (isset($_GET['tipo']) and $_GET['tipo']=='1')?'current':'';
				
				// FILTER LINK
				$n_views['tipo-1'] = sprintf(
					'<a href="%s" class="'. $class .'">'.get_viagem_tipo(1).' <span class="count">(%d)</span></a>',
					admin_url('edit.php?post_type=product&tipo=1'),
					$tipo_viagens_bv_results->found_posts
				);
				//--------------
				//--------------
				// Grupo de viagem
				/*
				$tipo_viagens_bv_query = array(
					'post_type'   		=> 'product',
					'posts_per_page' 	=> -1,
					'meta_query' 		=> array(
						'relation' 		=> 'AND',
						array(
							'key' 			=> 'brc_viagem_tipo_cadastro',
							'compare' 		=> '=',
							'value' 		=> '2',
						),
					),
					'for_count' => true,
				);
				
				$tipo_viagens_bv_results = new WP_Query($tipo_viagens_bv_query);
				$class = 'brc_admin_btn btn_viagem_tipo_grupo brc_admin_list_btn button ';
				$class .= (isset($_GET['tipo']) and $_GET['tipo']=='2')?'current':'';
				
				// FILTER LINK
				$n_views['tipo-2'] = sprintf(
					'<a href="%s" class="'. $class .'">'.get_viagem_tipo(2).' <span class="count">(%d)</span></a>',
					admin_url('edit.php?post_type=product&tipo=2'),
					$tipo_viagens_bv_results->found_posts
				);
				*/
				//--------------
				//--------------
				// A B com escolha de poltronas
				$tipo_viagens_bv_query = array(
					'post_type'   		=> 'product',
					'posts_per_page' 	=> -1,
					'meta_query' 		=> array(
						'relation' 		=> 'AND',
						array(
							'key' 			=> 'brc_viagem_tipo_cadastro',
							'compare' 		=> '=',
							'value' 		=> '3',
						),
					),
					'for_count' => true,
				);
				
				$tipo_viagens_bv_results = new WP_Query($tipo_viagens_bv_query);
				$class = 'brc_admin_btn btn_viagem_tipo_ab brc_admin_list_btn button ';
				$class .= (isset($_GET['tipo']) and $_GET['tipo']=='3')?'current':'';
				
				// FILTER LINK
				$n_views['tipo-3'] = sprintf(
					'<a href="%s" class="'. $class .'">'.get_viagem_tipo(3).' <span class="count">(%d)</span></a>',
					admin_url('edit.php?post_type=product&tipo=3'),
					$tipo_viagens_bv_results->found_posts
				);
				//--------------
				
				// REFAZ ARRAY COM FILTROS
				if($views){
					foreach($views as $k => $j){
						$n_views[$k] = $j;
					}
				}
				
				
				return $n_views;
			}
			return $views;
		}
		
		/**
		 * Columns
		 * Post Type: product
		 * 
		 */
		public function brc_product_columns_manage_edit($columns){
			$metatype_excursoes = (isset($_GET['metatype']) and $_GET['metatype']=='excursoes');
			
			if($metatype_excursoes){
				$temp_columns['cb'] 			= $columns['cb'];
				$temp_columns['thumb'] 			= $columns['thumb'];
				$temp_columns['name'] 			= $columns['name'];
				$temp_columns['ex_quartos'] 	= 'Quartos';
				$temp_columns['ex_data'] 		= '<i class="fa fa-clock-o"></i> Data/Hora';
				$temp_columns['ex_ingressos'] 	= 'Ingressos';
				$temp_columns['featured'] 		= $columns['featured'];
				$temp_columns['ex_actions'] 	= '';
				//$temp_columns = $columns; 
			}
			else{
				$temp_columns['cb'] 			= $columns['cb'];
				$temp_columns['name'] 			= 'Identificação';
				$temp_columns['vi_data'] 		= '<i class="fa fa-clock-o"></i> Data/Hora';
				$temp_columns['vi_locais'] 		= 'Saída/Chegada';
				$temp_columns['vi_stock'] 		= 'Assentos';
				$temp_columns['price'] 			= $columns['price'];
				$temp_columns['taxonomy-brc_linha_viagem'] = $columns['taxonomy-brc_linha_viagem'];
				$temp_columns['taxonomy-brc_veiculo'] = $columns['taxonomy-brc_veiculo'];
				$temp_columns['taxonomy-brc_motorista'] = $columns['taxonomy-brc_motorista'];
				$temp_columns['vi_actions'] 	= '';
			}
			return $temp_columns;
		}
		public function brc_product_columns_manage_edit_callback($column){
			$post_ID = get_the_ID();
			$product = wc_get_product($post_ID);
			$brc_excursao = get_post_meta($post_ID, 'brc_excursao', true);
			
			switch($column){
				case 'ex_data':
					$brc_excursao_data = get_post_meta(get_the_ID(), 'brc_excursao_data', true);
					$brc_excursao_data_volta = get_post_meta(get_the_ID(), 'brc_excursao_data_volta', true);
					echo '<span class="horarios-embarque">Saida: '.date('d/m/Y - H:i', $brc_excursao_data).' h</span><br/>';
					echo '<span class="horarios-embarque">Retorno: '.date('d/m/Y - H:i', $brc_excursao_data_volta).' h</span>';
				break;
				case 'ex_quartos':
					if($product->is_type('variable')){
						$quartos = $product->get_available_variations();
						
						if($quartos){
							echo '<table class="quartos-table">';
							echo '<thead>';
							echo '<tr>';
							echo '<th class="nome">Quarto</th>';
							echo '<th class="quantidade">Quant.</th>';
							echo '<th class="preco">Preço</th>';
							echo '</tr>';
							echo '</thead>';
							echo '<tbody>';
							
							foreach($quartos as $qk => $qj){
								$variations_hotel = get_post_meta($qj['variation_id'], 'brc_excursao_variations_hotel', true);
								$variations_nome = get_post_meta($qj['variation_id'], 'brc_excursao_variations_nome', true);
								$variations_pquarto = get_post_meta($qj['variation_id'], 'brc_excursao_variations_pquarto', true);
								$attribute_quartos = get_post_meta($qj['variation_id'], 'attribute_quartos', true);
								
								echo '<tr>';
								echo '<td class="nome">'. $variations_nome .'</td>';
								echo '<td class="quantidade">'. $qj['max_qty'] .'</td>';
								echo '<td class="preco">R$ '. moedaRealPrint($qj['display_price']) .'</td>';
								echo '</tr>';
							}
							
							echo '</tbody>';
							echo '</table>';
						}
					}
				break;
				case 'ex_ingressos':
					$brc_excursao_ingresso = get_post_meta(get_the_ID(), 'brc_excursao_ingresso', true);
					$brc_excursao_ingresso_stock = get_post_meta(get_the_ID(), 'brc_excursao_ingresso_stock', true);
					if($brc_excursao_ingresso == 'yes'){
						echo '<i class="fa fa-check"></i>';
						echo ' (x'.$brc_excursao_ingresso_stock.')';
					}else{
						echo '<i class="fa fa-times"></i>';
					}
				break;
				case 'ex_actions':
					if(get_orders_ids_by_product_id(get_the_ID(), get_all_woocommerce_status_id())){
						echo '<a href="'.get_permalink(get_option('fepa_lista_de_hospedes')).'?v='. get_the_ID() .'&simple=1" target="_blank" class="brc_admin_btn brc_admin_list_btn button action-lista-passageiros" title="Imprimir lista de hóspedes">
						<span class="fa fa-clipboard"></span></a>';
						
						$base = base64_encode(get_the_ID());
						echo '<a href="'.get_permalink(get_option('fepa_comprovante_reserva')).'?p='. $base .'&lista=product" target="_blank" class="brc_admin_btn brc_admin_list_btn button action-ticket" title="Imprimir comprovantes de reserva">
						<span class="fas fa-clipboard-check"></span></a>';
					}
				break;
				/*--*/
				
				case 'name':
					$brc_excursao = get_post_meta(get_the_ID(), 'brc_excursao', true);
					
					if(!$brc_excursao){
						$brc_viagem_tipo_cadastro = get_post_meta(get_the_ID(), 'brc_viagem_tipo_cadastro', true);
						
						if($brc_viagem_tipo_cadastro){
							$viagem_tipo = get_viagem_tipo();
							$viagem_tipo_class = get_viagem_tipo_class();
							if($viagem_tipo_class[$brc_viagem_tipo_cadastro]){
								echo '<br/><span class="bullet bullet-sm bullet-type bullet-viagem-'.$viagem_tipo_class[$brc_viagem_tipo_cadastro].'">'. $viagem_tipo[$brc_viagem_tipo_cadastro] .'</span>';
							}
						}
					}
				break;
				case 'vi_data':
					$brc_viagem_ida_data = get_post_meta(get_the_ID(), 'brc_viagem_ida_data', true);
					$brc_viagem_volta_data = get_post_meta(get_the_ID(), 'brc_viagem_volta_data', true);
					
					echo '<span class="horarios-embarque">Saída: '.date('d/m/Y H:i', $brc_viagem_ida_data).'</span>';
					echo '<span class="horarios-desembarque">Chegada: '.date('d/m/Y H:i', $brc_viagem_volta_data).'</span>';
				break;
				case 'vi_locais':
					$embarque = get_post_meta(get_the_ID(), 'brc_viagem_origem', true);
					$desembarque = get_post_meta(get_the_ID(), 'brc_viagem_destino', true);
					
					$embarque_term = get_term($embarque, 'brc_destinos');
					$embarque_parent_term = get_term($embarque_term->parent, 'brc_destinos');
					$desembarque_term = get_term($desembarque, 'brc_destinos');
					$desembarque_parent_term = get_term($desembarque_term->parent, 'brc_destinos');
					
					
					echo '<span class="destinos-embarque">'.$embarque_parent_term->name.' ('.$embarque_term->name.')</span>';
					echo '<span class="destinos-desembarque">'.$desembarque_parent_term->name.' ('.$desembarque_term->name.')</span>';
				break;
				case 'vi_stock':
					$brc_viagem_veiculos_quant = get_post_meta(get_the_ID(), 'brc_viagem_veiculos_quant', true);
					$brc_viagem_assentos_veiculos = get_post_meta(get_the_ID(), 'brc_viagem_assentos_veiculos', true);
					//$quantidade_assentos = get_post_meta(get_the_ID(), 'brc_viagem_assentos', true);
					
					$quantidade_assentos = intval($brc_viagem_veiculos_quant)*intval($brc_viagem_assentos_veiculos);
					$_stock = get_post_meta(get_the_ID(), '_stock', true);
					
					echo '<span class="assentos-stock '. (intval($_stock)==$quantidade_assentos?'assentos-stock-full':'assentos-stock-less') .'">'. intval($_stock) .'</span>/';
					echo '<span class="assentos-total">'. $quantidade_assentos .'</span>';
				break;
				case 'vi_linha':
					
					
				break;
				case 'vi_actions':
					if(get_orders_ids_by_product_id(get_the_ID(), get_all_woocommerce_status_id())){
						echo '<a href="'.LISTA_DE_PASSAGEIROS_URI.'?v='. get_the_ID() .'&simple=1" target="_blank" class="brc_admin_btn brc_admin_list_btn button action-lista-passageiros" title="Imprimir lista de passageiros">
						<span class="fa fa-clipboard"></span></a>';
						
						$base = base64_encode(get_the_ID());
						echo '<a href="'.get_permalink(get_option('fepa_comprovante_reserva')).'?p='. $base .'&lista=product" target="_blank" class="brc_admin_btn brc_admin_list_btn button action-ticket" title="Imprimir bilhetes de passagem">
						<span class="fa fa-ticket"></span></a>';
					}
				break;
			}
		}
		
		/**
		 * Editor functions / Ajax
		 * Post Type: product
		 * 
		 */
		public function brc_add_product_excursao_add_variation(){
			ob_start();
			include FEPA_PLUGIN_DIR .'/templates/brc_admin.product_excursao_field_variation.php';
			$html = ob_get_contents();
			ob_end_clean();
			
			$ret['html'] = $html;
			$ret['sts'] = true;
			
			echo json_encode($ret);
			exit;
		}
		public function brc_add_product_excursao_remove_variation(){
			$ret = array('sts' => false);
			
			if(isset($_POST['varid'])){
				global $wpdb;
				
				$varid = $_POST['varid'];
				$vari = get_post($varid);
				
				if($vari){
					$ret['sts'] = true;
					$del = wp_delete_post($varid, true);
					$wpdb->query("DELETE FROM ".$wpdb->prefix."wc_product_meta_lookup WHERE product_id = ". $varid ."");
				}
			}
			
			echo json_encode($ret);
			exit;
		}
	
		/**
		 * Editor functions / Ajax
		 * Post Type: product
		 * 
		 */
		public function brc_add_product_excursao_add_child(){
			ob_start();
			include FEPA_PLUGIN_DIR .'/templates/brc_admin.product_excursao_field_child.php';
			$html = ob_get_contents();
			ob_end_clean();
			
			$ret['html'] = $html;
			$ret['sts'] = true;
			
			echo json_encode($ret);
			exit;
		}
		
		// .FEPAPostTypeProduct
	}