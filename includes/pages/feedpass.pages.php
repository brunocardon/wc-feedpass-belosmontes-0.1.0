<?php
	global $brcthemes;
	
	include_once ELATED_ROOT_DIR.'/includes/CMB2-grid-master/Cmb2GridPlugin.php';
	
	if(!function_exists('PEXP_pages')){
		function brcpasstour_pages(){
			
			define('FINALIZAR_RESERVA_URI', wc_get_checkout_url());
			
			// RESTRITOS
			$lista_de_hospedes 		= get_page_by_path('lista-de-hospedes');
			$lista_de_passageiros 	= get_page_by_path('lista-de-passageiros');
			$comprovante_reserva 	= get_page_by_path('comprovante-de-reserva');
			
			define('LISTA_DE_HOSPEDES_ID', 		$lista_de_hospedes->ID);
			define('LISTA_DE_PASSAGEIROS_ID', 	$lista_de_passageiros->ID);
			define('COMPROVANTE_RESERVA_ID', 	$comprovante_reserva->ID);
			define('LISTA_DE_HOSPEDES_URI', 	get_permalink($lista_de_hospedes));
			define('LISTA_DE_PASSAGEIROS_URI', 	get_permalink($lista_de_passageiros));
			define('COMPROVANTE_RESERVA_URI', 	get_permalink($comprovante_reserva));
			
			
			//---------------------------------
			// THEME
			$busca = get_page_by_path('nossas-linhas');
			$passagemticket = get_page_by_path('passagemticket');
			
			define('BUSCA_ID', $busca->ID);
			define('BUSCA_URI', get_permalink($busca));
			define('PASSAGEMTICKET_ID', $passagemticket->ID);
			define('PASSAGEMTICKET_URI', get_permalink($passagemticket));
			
			
			//---------------------------------
			// CLIENTE
			$minha_conta = get_option('woocommerce_myaccount_page_id');
			$cadastre_se = get_page_by_path('minha-conta/cadastrar-se');
			
			define('MINHA_CONTA_ID', $minha_conta);
			define('MINHA_CONTA_URI', get_permalink($minha_conta));
			
			define('CADASTRE_SE_ID', $cadastre_se->ID);
			define('CADASTRE_SE_URI', get_permalink($cadastre_se));
			
			
			return array(
				'ID' => array(
					'LISTA_DE_HOSPEDES_ID' => LISTA_DE_HOSPEDES_ID,
					'COMPROVANTE_RESERVA_ID' => COMPROVANTE_RESERVA_ID,
					'LISTA_DE_PASSAGEIROS_ID' => LISTA_DE_PASSAGEIROS_ID,
					'MINHA_CONTA_ID' => MINHA_CONTA_ID,
					'CADASTRE_SE_ID' => CADASTRE_SE_ID,
					'PASSAGEMTICKET_ID' => CADASTRE_SE_ID,
				),
				'URI' => array(
					'FINALIZAR_RESERVA_URI' => FINALIZAR_RESERVA_URI,
					'LISTA_DE_HOSPEDES_URI' => LISTA_DE_HOSPEDES_URI,
					'COMPROVANTE_RESERVA_URI' => COMPROVANTE_RESERVA_URI,
					'LISTA_DE_PASSAGEIROS_URI' => LISTA_DE_PASSAGEIROS_URI,
					'MINHA_CONTA_URI' => MINHA_CONTA_URI,
					'CADASTRE_SE_URI' => CADASTRE_SE_URI,
					'PASSAGEMTICKET_URI' => CADASTRE_SE_URI,
				),
			);
		}
		add_action('init', 'brcpasstour_pages');
	}
	
	if(!function_exists('brc_add_user_roles')){
		function brc_add_user_roles(){
			global $wp_roles;
			
			if(!isset($wp_roles))
				$wp_roles = new WP_Roles();
			
			$admin_role = $wp_roles->get_role('administrator');
			$wp_roles->add_role('administradorsimples', 'Administrador Simples', $admin_role->capabilities);
		}
		add_action('init', 'brc_add_user_roles');
	}
?>