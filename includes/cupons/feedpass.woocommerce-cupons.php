<?php
	if(!class_exists('BRCPASSTOUR_CUPONS')){ // brcpasstour
		class BRCPASSTOUR_CUPONS{
			
			public $mensagem;
			
			function __construct(){
				add_filter('woocommerce_settings_tabs_array', 					array($this, 'brc_settings_tabs_array'), 50);
				add_action('woocommerce_settings_tabs_cupons_admin', 			array($this, 'cupons_settings_tab'));
				add_action('woocommerce_update_options_cupons_admin', 			array($this, 'cupons_update_settings'));
			}
			// SETUP
			static function init(){
				if(null == self::$instance){
					self::$instance = new self;
				}
				return self::$instance;
			}
			
			public function brc_settings_tabs_array($settings_tabs){
				$settings_tabs['cupons_admin'] = 'Bônus Cupons';
				return $settings_tabs;
			}
			
			public function cupons_settings_tab(){
				woocommerce_admin_fields($this->cupons_get_settings());
			}
			
			public function cupons_update_settings(){
				woocommerce_update_options($this->cupons_get_settings());
			}
			
			public function cupons_get_settings(){
				$prefix = 'brc_cupons_admin_settings_';
				$eltd_options_blu = get_option('eltd_options_blu');
				
				$mensagem_pagamento_pendente 	= get_option($prefix.'pagamento_pendente');
				$mensagem_processando 			= get_option($prefix.'processando');
				$mensagem_aguardando 			= get_option($prefix.'aguardando');
				$mensagem_concluido 			= get_option($prefix.'concluido');
				
				$settings = array(
					'section_title' => array(
						'name'     => 'Configurações do módulo de cupons.',
						'type'     => 'title',
						'desc'     => 'Caso a página de "Meus Cupons" na área do cliente não esteja aparecendo ou retornando <strong>ERRO 404</strong>, atualize os links permanente. Para acessar <a href="'.admin_url('options-permalink.php').'" title="Links Permanentes">clique aqui</a>.',
						'id'       => $prefix.'title',
					),
					'descricao' => array(
						'name' 		=> 'Texto descritivo e explicativo.',
						'type' 		=> 'textarea',
						'desc'     	=> '
							Adicione um texto para o cliente que acessar a página "Meus Cupons" na área do cliente. <br/>
							<em><strong>Aceita código HTML</strong>. Utilize os marcadores para inserir dados dinâmicos aonde desejar.</em><br/>
							<hr/>
							<code>{{QUANT}}</code> = Quantidade de cupons disponível;<br/>
							<code>{{PERCENT}}</code> = Valor do desconto;<br/>
							<code>{{COD}}</code> = Código do desconto;<br/>
							<hr/>
							',
						'css'     	=> 'min-width:75%;min-height:350px;',
						'default' 	=> $this->default_descricao_html(),
						'id'   		=> $prefix.'descricao',
					),
					'section_end' => array(
						'type' 	=> 'sectionend',
						'id' 	=> 'wc_settings_tab_demo_section_end',
					)
				);
				return apply_filters('wc_cupons_settings', $settings);
			}
			
			public function default_descricao_html(){
				return '<p>
	Você possuí <mark class="quant">{{QUANT}}</mark> cupom de <mark class="percent">{{PERCENT}}</mark> de desconto disponível.<br/>
	Para resgatar utilize o código <mark class="cod">{{COD}}</mark> na hora de finalizar a sua próxima compra.
</p>
<p>
	Para saber mais como utilizar e obter os cupons, Praesent fringilla enim nunc, sed eleifend libero congue quis. 
	Ut tristique, ex sollicitudin dapibus finibus, est nunc mollis justo, nec posuere tortor sapien a.
	<a href="#">Clique aqui</a>.
</p>';
			}
			
			public function gen_descricao_mensagem($str_sintax){
				$mensagem = get_option('brc_cupons_admin_settings_descricao');
				
				if(!$mensagem)
					$mensagem = $this->default_descricao_html();
				
				$str_search = array();
				$str_replace = array();
				foreach($str_sintax as $k => $j){
					$str_search[] = '{{'.$k.'}}';
					$str_replace[] = $j;
				}
				
				$this->mensagem = str_replace($str_search, $str_replace, $mensagem);
				return $this->mensagem;
			}
			
		}
	}
?>