<?php
	class FEPAVcMap{
		
		protected static $instance = null;
		private $category;
		private $prefix;

		function __construct(){
			$this->prefix = 'fepa_';
			$this->category = FEPA_PLUGIN_NAME;
			
			add_action('vc_before_init', array($this, 'fepa_excursoes'));
			add_action('vc_before_init', array($this, 'fepa_pacotes_promocionais'));
			add_action('vc_before_init', array($this, 'fepa_busca_viagens'));
			add_action('vc_before_init', array($this, 'fepa_blog_carrossel'));
		}
		static function init() {
			if(null == self::$instance ){
				self::$instance = new self;
			}
			return self::$instance;
		}
		public function register() {
			// nothing to see here...
			vc_add_shortcode_param('dropdown_multi', array($this, 'dropdown_multi_settings_field'));
		}
		
		public function dropdown_multi_settings_field($param, $value){
		   $param_line = '';
		   $param_line .= '<select multiple style="height:150px;" name="'. esc_attr( $param['param_name'] ).'" class="wpb_vc_param_value wpb-input wpb-select '. esc_attr( $param['param_name'] ).' '. esc_attr($param['type']).'">';
		   foreach ( $param['value'] as $text_val => $val ) {
			   if ( is_numeric($text_val) && (is_string($val) || is_numeric($val)) ) {
							$text_val = $val;
						}
						$text_val = __($text_val, "js_composer");
						$selected = '';

						if(!is_array($value)) {
							$param_value_arr = explode(',',$value);
						} else {
							$param_value_arr = $value;
						}

						if ($value!=='' && in_array($val, $param_value_arr)) {
							$selected = ' selected="selected"';
						}
						$param_line .= '<option class="'.$val.'" value="'.$val.'"'.$selected.'>'.$text_val.'</option>';
					}
		   $param_line .= '</select>';

		   return  $param_line;
		}
		
		public function fepa_excursoes(){
			vc_map(
				array(
					"name" 					=> "Excursões",
					"base" 					=> "fepa_excursoes",
					"class" 				=> "",
					"icon" 					=> FEPA_PLUGIN_URL . "/assets/img/agencia-feedback-vc_map-icon.png",
					"category" 				=> $this->category,
					"params" 				=> array(
						array(
							'type' 			=> 'css_editor',
							'heading' 		=> __( 'Css', 'my-text-domain' ),
							'param_name' 	=> 'css',
							'group' 		=> __( 'Design options', 'my-text-domain' ),
						),
					)
				)
			);
		}
		public function fepa_pacotes_promocionais(){
			$excurcoes = new WP_Query(array(
				'for_count' 		=> 1,
				'post_type' 		=> 'product',
				'posts_per_page' 	=> -1,
				'meta_query' 		=> array(
					array(
						'key'     => 'brc_excursao',
						'value'   => 1,
						'compare' => '=',
					),
				),
			));
			
			$value = array();
			if($excurcoes->have_posts()){
				while($excurcoes->have_posts()){
					$excurcoes->the_post();
					
					$_ida = get_post_meta(get_the_ID(), 'brc_excursao_data', true);
					$_volta = get_post_meta(get_the_ID(), 'brc_excursao_data_volta', true);
					
					$key = get_the_ID().' - '.get_the_title().' (ida: '. date('d/m/Y', $_ida) .')';
					$value[$key] = get_the_ID();
				}
			}
			
			
			vc_map(
				array(
					"name" 					=> "Pacotes promocionais",
					"base" 					=> "fepa_pacotes_promocionais",
					"class" 				=> "",
					"icon" 					=> FEPA_PLUGIN_URL . "/assets/img/agencia-feedback-vc_map-icon.png",
					"category" 				=> $this->category,
					"params" 				=> array(
						array(
							'type' 			=> 'dropdown_multi',
							'heading' 		=> 'Excursões',
							'param_name' 	=> 'excursoes',
							'description' 	=> 'Seleciona as excursões para destacar como pacotes promocionais.
												<br/>Selecione multiplas escolhas segurando o CTRL no teclado.',
							'admin_label' 	=> true,
							'group' 		=> 'Configurações Gerais',
							'value' 		=> $value,
						),
						array(
							'type' 			=> 'checkbox',
							'heading' 		=> 'Mostrar todos',
							'param_name' 	=> 'show_all',
							'description' 	=> 'Ao selecionar esta opção, serão mostrados todos os itens de excursões a partir do dia vigente.',
							'admin_label' 	=> true,
							'group' 		=> 'Configurações Gerais',
							'value' 		=> 'Sim',
						),
						array(
							'type' 			=> 'css_editor',
							'heading' 		=> __( 'Css', 'my-text-domain' ),
							'param_name' 	=> 'css',
							'group' 		=> __( 'Design options', 'my-text-domain' ),
						),
					)
				)
			);
		}
		public function fepa_busca_viagens(){
			vc_map(
				array(
					"name" 					=> "Busca Viagens",
					"base" 					=> "fepa_busca_viagens",
					"class" 				=> "",
					"icon" 					=> FEPA_PLUGIN_URL . "/assets/img/agencia-feedback-vc_map-icon.png",
					"category" 				=> $this->category,
					"params" 				=> array(
						array(
							'type' 			=> 'css_editor',
							'heading' 		=> __( 'Css', 'my-text-domain' ),
							'param_name' 	=> 'css',
							'group' 		=> __( 'Design options', 'my-text-domain' ),
						),
					)
				)
			);
		}
		public function fepa_blog_carrossel(){
			vc_map(
				array(
					"name" 					=> "Blog Carrossel",
					"base" 					=> "fepa_blog_carrossel",
					"class" 				=> "",
					"icon" 					=> FEPA_PLUGIN_URL . "/assets/img/agencia-feedback-vc_map-icon.png",
					"category" 				=> $this->category,
					"params" 				=> array(
						array(
							'type' 			=> 'css_editor',
							'heading' 		=> __( 'Css', 'my-text-domain' ),
							'param_name' 	=> 'css',
							'group' 		=> __( 'Design options', 'my-text-domain' ),
						),
					)
				)
			);
		}
	}
	class WPBakeryShortCode_Fepa_excursoes extends WPBakeryShortCode{}
	class WPBakeryShortCode_Fepa_pacotes_promocionais extends WPBakeryShortCode{}
	class WPBakeryShortCode_Fepa_busca_viagens extends WPBakeryShortCode{}
	class WPBakeryShortCode_Fepa_blog_carrossel extends WPBakeryShortCode{}

?>