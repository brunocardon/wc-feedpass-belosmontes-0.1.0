<?php
	class FEPAAdmin{
		protected static $instance = null;
		
		function __construct(){
			// add stuffs...
			
			add_action('admin_footer', array($this, 'brc_add_loading'));
			add_action('admin_footer', array($this, 'brc_add_thickbox'));
			add_action('admin_footer', array($this, 'brc_add_metatype_excursoes'), 10);
			add_action('admin_footer', array($this, 'brc_add_post_type_id_identifier'));
			add_action('admin_footer', array($this, 'brc_hide_wpbakery_post_type_product'));
			add_action('admin_menu', array($this, 'brc_woocommerce_menu_rename'), 999);
			
			// theme style
			add_action('wp_enqueue_scripts', array($this, 'brc_styles'));
			add_action('wp_enqueue_scripts', array($this, 'brc_script'));
			add_action('body_class', array($this, 'brc_body_class'));
			
			// woocommerce add config
			add_filter('woocommerce_get_sections_advanced', array($this, 'brc_dis_tab'), 5, 1);
			add_filter('woocommerce_get_settings_advanced', array($this, 'brc_dis_get_settings'), 5, 2 );
		}
		
		static function init(){
			if(null == self::$instance ){
				self::$instance = new self;
			}
			return self::$instance;
		}
		
		public function register(){
			add_action('admin_enqueue_scripts', array($this, 'brc_styles_admin'), 100);
			add_action('admin_enqueue_scripts', array($this, 'brc_script_admin'), 100);
		}
		
		public function brc_styles_admin(){
			wp_enqueue_style('brc_select2', 		FEPA_PLUGIN_URL.'/assets/css/select2.css', false, FEPA_SCRIPT_VERSION);
			wp_enqueue_style('brc_styles_admin', 	FEPA_PLUGIN_URL.'/assets/css/brc_admin.css', false, time());
			if(current_user_can('administradorsimples')){
				wp_enqueue_style('brc_styles_admin_simply', FEPA_PLUGIN_URL.'/assets/css/brc_admin_simple.css', false, FEPA_SCRIPT_VERSION);
			}
		}
		public function brc_styles(){

			$brc_version = time();
			wp_enqueue_style('brc_slick', 		FEPA_PLUGIN_URL.'/assets/css/slick.css', array(), FEPA_SCRIPT_VERSION);
			wp_enqueue_style('select2-css', 	FEPA_PLUGIN_URL.'/assets/css/select2.css', array(), FEPA_SCRIPT_VERSION);
			wp_enqueue_style('brc_defaults', 	FEPA_PLUGIN_URL.'/assets/css/brc_defaults.css', array(), $brc_version);
			wp_enqueue_style('brc_footer', 		FEPA_PLUGIN_URL.'/assets/css/brc_footer.css', array(), FEPA_SCRIPT_VERSION);
			wp_enqueue_style('brc_header', 		FEPA_PLUGIN_URL.'/assets/css/brc_header.css', array(), FEPA_SCRIPT_VERSION);
			wp_enqueue_style('brc_finalizar', 	FEPA_PLUGIN_URL.'/assets/css/brc_finalizar.css', array(), $brc_version);
			wp_enqueue_style('brc_themes', 		FEPA_PLUGIN_URL.'/assets/css/brc_themes.css', array(), $brc_version);
			wp_enqueue_style('brc_mods', 		FEPA_PLUGIN_URL.'/assets/css/brc_mods.css', array(), $brc_version);
			//wp_enqueue_style('brc_carousel', 	FEPA_PLUGIN_URL.'/assets/css/owl.carousel.min.css', array(), $brc_version);
			
			wp_enqueue_style('flatpickr', 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css', array(), 1);
			
		}
		public function brc_body_class($classes){
			global $post;
			if(isset($post)){
				$classes[] = $post->post_type . '-' . $post->post_name;
			}
			return $classes;
		}
		
		public function brc_script_admin(){
			wp_enqueue_script('jquery-ui-sortable');
			wp_enqueue_script('select2');
			wp_enqueue_script('brc_mask', 			FEPA_PLUGIN_URL.'/assets/js/jquery.maskedinput.js', array('jquery'), FEPA_SCRIPT_VERSION, true);
			wp_enqueue_script('brc_script_admin', 	FEPA_PLUGIN_URL.'/assets/js/brc_script_admin.js', array('jquery'), time(), true);
			wp_localize_script('brc_script_admin', 	'ajax_var', 
				array(
					'post_url' 		=> admin_url('post.php'),
					'url' 			=> admin_url('admin-ajax.php'),
					'nonce' 		=> wp_create_nonce('ajax-nonce'),
					'site_url' 		=> get_bloginfo('url'),
					'datanow' 		=> date('d/m/Y', time()),
				)
			);
		}
		public function brc_script(){
			
			$brc_version = time();
			wp_enqueue_script('flatpickr', 'https://cdn.jsdelivr.net/npm/flatpickr', array('jquery'), 1, true);
			wp_enqueue_script('flatpickr_pt', 'https://npmcdn.com/flatpickr/dist/l10n/pt.js', array('jquery'), 1, true);
			
			wp_enqueue_script('select2');
			wp_enqueue_script('brc_slick', 			FEPA_PLUGIN_URL.'/assets/js/slick.min.js', array('jquery'), FEPA_SCRIPT_VERSION, true);
			wp_enqueue_script('brc_mask', 			FEPA_PLUGIN_URL.'/assets/js/jquery.maskedinput.js', array('jquery'), FEPA_SCRIPT_VERSION, true);
			wp_enqueue_script('brc_script', 		FEPA_PLUGIN_URL.'/assets/js/brc_script.js', array('jquery'), $brc_version, true);
			wp_localize_script('brc_script', 		'ajax_var', 
				array(
					'url' 			=> admin_url('admin-ajax.php'),
					'nonce' 		=> wp_create_nonce('ajax-nonce'),
					'site_url' 		=> get_bloginfo('url'),
					'template_url' 	=> get_bloginfo('template_url'),
					'datanow' 		=> date('d/m/Y', time()),
				)
			);
		}
		
		/**
		 * Add loading div to footer in admin
		 * 
		 */
		public function brc_add_loading(){
			echo '<div class="admin_loading"></div>';
		}
		
		/**
		 * Add thickbox function to admin
		 * 
		 */
		public function brc_add_thickbox(){
			// MODAL
			add_thickbox();
			echo '<div id="brc-tb-content" style="display:none;"><div class="brc-modal-inner"></div></div>';
		}
		
		/**
		 * Add excursões metatype to post type
		 * 
		 */
		public function brc_add_metatype_excursoes(){
			global $post, $current_screen;
			
			if($current_screen->post_type == 'product' and isset($_GET['metatype'])){
				echo '<div id="metatype" data-mvalue="'. $_GET['metatype'] .'"></div>';
			}
			if($current_screen->post_type == 'product' AND !$current_screen->taxonomy){
				$brc_excursao = get_post_meta($post->ID, 'brc_excursao', true);
				if($brc_excursao){
					echo '<div id="metatype" data-mvalue="excursoes"></div>';
				}
			}
		}
		
		/**
		 * Add a div with class and id to indentify the post type ID
		 * 
		 */
		public function brc_add_post_type_id_identifier(){
			global $post, $current_screen;
			
			// ID POST TYPE
			if($current_screen->post_type){
				echo '<div class="page-admin-ID" id="admin-'. $current_screen->post_type .'"></div>';
				echo '<div class="page-admin-base" id="admin-base-'. $current_screen->base .'"></div>';
			}
		}
		
		/**
		 * Remove WpBakery from content editor of Product post type
		 * 
		 */
		public function brc_hide_wpbakery_post_type_product(){
			global $post, $current_screen;
				
			// ESCONDE VISUAL COMPOSER DE VIAGENS
			$brc_excursao = get_post_meta($post->ID, 'brc_excursao', true);
			if($current_screen->post_type == 'product'){
				if(!$brc_excursao){
					echo '<style> #poststuff .composer-switch{ display:none; } </style>';
				}
			}
		}
		
		/**
		 * Modify the WooCommerce Default menu name to the WebSite name
		 * 
		 */
		public function brc_woocommerce_menu_rename(){
			global $menu;
			
			$woo = brc_woocommerce_menu_search_php('WooCommerce', $menu);
			
			if(!$woo)
				return;
				
			$menu[$woo][0] = get_bloginfo('name');
			$menu[$woo][6] = get_site_icon_url(32);
		}
		
		/**
		 * Add a config options for plugin at WooCommerce wc-settings -> advanced
		 * 
		 */
		public function brc_dis_tab($settings_tab){
			$settings_tab['brc-disable-woocommerce-admin'] = FEPA_PLUGIN_NAME;
			return $settings_tab;
		}
		public function brc_dis_get_settings($settings, $current_section){
			global $wp_roles;
			
			$custom_settings = array();
			if('brc-disable-woocommerce-admin' == $current_section){
				$custom_settings = array(
					array(
						'name' => FEPA_PLUGIN_NAME,
						'type' => 'title',
						'desc' => __('Configura quais tipos de usuários para desativar WooCommerce Admin Console.'),
						'id'   => 'brc_dis' 
					),
				);
				
				$pages = get_pages();
				$page_options = array(0 => '--');
				if($pages){
					foreach($pages as $k => $j){
						$page_options[$j->ID] = $j->post_title;
					}
				}
				
				$custom_settings[] = array(
					'name' 		=> 'Logo do site',
					'desc' 		=> 'Adicionar logo para ilustrar material institucional e listas para impressão.',
					'type' 		=> 'text',
					'id'		=> 'fepa_logo',
				);
				$custom_settings[] = array(
					'name' 		=> 'Página de lista de hóspedes [Admin]',
					'desc' 		=> 'Adicionar ´shortcode´ <code>['.FEPA_PREFIX.'lista_hospedes]</code>',
					'type' 		=> 'select',
					'id'		=> 'fepa_lista_de_hospedes',
					'options' 	=> $page_options,
				);
				$custom_settings[] = array(
					'name' 		=> 'Página de lista de passageiros [Admin]',
					'desc' 		=> 'Adicionar ´shortcode´ <code>['.FEPA_PREFIX.'lista_passageiros]</code>',
					'type' 		=> 'select',
					'id'		=> 'fepa_lista_de_passageiros',
					'options' 	=> $page_options,
				);
				$custom_settings[] = array(
					'name' 		=> 'Página de lista de comprovante de reserva [Admin]',
					'desc' 		=> 'Adicionar ´shortcode´ <code>['.FEPA_PREFIX.'lista_de_comprovante_reserva]</code>',
					'type' 		=> 'select',
					'id'		=> 'fepa_comprovante_reserva',
					'options' 	=> $page_options,
				);
				$custom_settings[] = array(
					'name' 		=> 'Página de resultado da busca',
					'desc' 		=> 'Adicionar ´shortcode´ <code>['.FEPA_PREFIX.'resultados_busca]</code>',
					'type' 		=> 'select',
					'id'		=> 'fepa_busca',
					'options' 	=> $page_options,
				);
				$custom_settings[] = array(
					'name' 		=> 'Página de finalização da compra',
					'desc' 		=> 'Adicionar ´shortcode´ <code>['.FEPA_PREFIX.'finalizar_compra]</code><br/><em>Esse código funciona semelhante ao <code>[woocommerce_checkout]</code></em>',
					'type' 		=> 'select',
					'id'		=> 'fepa_finalizar',
					'options' 	=> $page_options,
				);
				$custom_settings[] = array(
					'name' 		=> 'Página de resultado do ticket de passagem',
					'desc' 		=> 'Adicionar ´shortcode´ <code>['.FEPA_PREFIX.'ticket]</code>',
					'type' 		=> 'select',
					'id'		=> 'fepa_passagemticket',
					'options' 	=> $page_options,
				);
				/*$custom_settings[] = array(
					'name' 		=> 'Página de ´minha conta´',
					//'desc' 		=> 'Adicionar ´shortcode´ <code>['.FEPA_PREFIX.'resultados_busca]</code>',
					'type' 		=> 'select',
					'id'		=> 'minha_conta',
					'options' 	=> $page_options,
				);*/
				$custom_settings[] = array(
					'name' 		=> 'Página de cadastro',
					//'desc' 		=> 'Adicionar ´shortcode´ <code>['.FEPA_PREFIX.'resultados_busca]</code>',
					'type' 		=> 'select',
					'id'		=> 'fepa_cadastre_se',
					'options' 	=> $page_options,
				);
				
				$custom_settings[] = array('type' => 'sectionend', 'id' => 'brc_dis');
				return $custom_settings; 
			}else{
				return $settings;
			}
		}
		
		
		
		// .FEPAAdmin
	}