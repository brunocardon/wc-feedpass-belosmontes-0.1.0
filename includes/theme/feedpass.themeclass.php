<?php
	class FEPAThemes{
		public $vc_templates;
		
		
		function __construct(){
			$this->vc_templates = FEPA_PLUGIN_DIR . '/templates/';
			
			// DEFINIÇÕES
			//define('BRC_THEME_ASSETS_URI', get_template_directory_uri().'/assets');
			//define('BRC_THEME_ASSETS_DIR', get_template_directory().'/assets');
			
			// ACTIONS
			//add_action('wp_enqueue_scripts', array($this, 'brc_passtour_style'));
		}
		
		public function buscaTemVolta(){
			if(
				(isset($_REQUEST['datafim']) AND $_REQUEST['datafim']) or
				(isset($_REQUEST['selvolta']) AND $_REQUEST['selvolta']=='yes')
			){
				return true;
			}else{
				return false;
			}
		}
		
		public function isVolta(){
			global $woocommerce;
			
			if(is_admin())
				return false;
			
			$items = $woocommerce->cart->get_cart();
			if(count($items) > 0){
				foreach($items as $k => $j){
					$volta[] = $j['volta'];
					$etapa[] = $j['etapa'];
				}
				$vvolta = array_shift($volta);
				
				if($vvolta){
					return $vvolta;
				}
			}
			return false;
		}
		
		public function verfEtapaVolta(){
			global $woocommerce;
			
			if(is_admin())
				return false;
			
			$items = $woocommerce->cart->get_cart();
			$_order_etapa['volta'] = false;
			
			if(count($items) > 0){
				foreach($items as $k => $j){
					$_order_selvolta = isset($j['_order_selvolta'])?true:false;
					
					if(isset($j['_order_selvolta']))
						$_order_etapa[$j['_order_etapa']] = true;
				}
				if($_order_selvolta){
					if(!$_order_etapa['volta']){
						return true;
					}
				}
				
				return false;
			}
			return false;
		}
		
		public function verfEtapaBusca(){
			
			if(is_admin())
				return false;
			
			if($this->buscaTemVolta()){
				if($this->verfEtapaVolta()){
					return 'volta';
				}else{
					return 'ida';
				}
			}
			
			return false;
		}
		
		public function defaultBuscaArgs($embarque=1, $desembarque=1){
			$viagens_args = array(
				'post_type' 		=> 'product',
				'meta_key' 			=> 'brc_viagem_ida_data',
				'orderby' 			=> 'meta_value',
				'order' 			=> 'ASC',
				'meta_query' 		=> array(
					'relation' 		=> 'AND',
					/*
					array(
						'key' 			=> 'brc_viagem_ida_data',
						'compare' 		=> '>=',
						'value' 		=> get_date_timestamp($_GET['datafim']),
					),
					*/
					array(
						'key' 			=> 'brc_viagem_ida_data',
						'compare' 		=> '>=',
						'value' 		=> strtotime(date('d-m-Y H:i', current_time('timestamp')).' + 30 minutes'),
					),
					array(
						'key' 			=> 'brc_viagem_ida_data',
						'compare' 		=> '<=',
						'value' 		=> strtotime(date('d-m-Y H:i', current_time('timestamp')).' + 3 weeks'),
					),
					array(
						'key' 			=> 'brc_viagem_origem',
						'compare' 		=> 'IN',
						'value' 		=> $embarque
					),
					array(
						'key' 			=> 'brc_viagem_destino',
						'compare' 		=> 'IN',
						'value' 		=> $desembarque
					),
				),
				'posts_per_page' => -1,
			);
			
			return $viagens_args;
		}
		public function defaultBuscaArgsLinhas($linhas=array()){
			$viagens_args = array( 
				'post_type' 		=> 'product',
				'meta_key' 			=> 'brc_viagem_ida_data',
				'orderby' 			=> 'meta_value',
				'order' 			=> 'ASC',
				'meta_query' 		=> array(
					'relation' 		=> 'AND',
					array(
						'key' 			=> 'brc_viagem_ida_data',
						'compare' 		=> '>=',
						'value' 		=> strtotime(date('d-m-Y H:i', current_time('timestamp')).' + 30 minutes'),
					),
					array(
						'key' 			=> 'brc_viagem_ida_data',
						'compare' 		=> '<=',
						'value' 		=> strtotime(date('d-m-Y H:i', current_time('timestamp')).' + 3 weeks'),
					),
				),
				'posts_per_page' => -1,
			);
			if($linhas){
				$viagens_args['tax_query']['relation'] = 'AND';
				$viagens_args['tax_query'][] = array(
					'taxonomy' => 'brc_linha_viagem',
					'field' => 'term_id',
					'terms' => $linhas
				);
			}
			
			return $viagens_args;
		}
		
		
		// STYLES / SCRIPTS
		public function brc_passtour_style(){
			$brc_version = 4;
			$brc_theme_templates = array(
				'template.listagemhospedes.php',
				'template.listagempassageiros.php',
				'template.comprovante-reserva.php'
			);
			
			if(is_page_template($brc_theme_templates)){
				wp_enqueue_style('brc_themes_admins', BRC_THEME_ASSETS_URI.'/css/brc_themes_admins.css', array(), $brc_version);
			}
		}
		
		// PAGE THANK YOU
		public function get_bacs_account_details_html($echo = true, $type = 'table'){
			ob_start();
			
			$gateway    = new WC_Gateway_BACS();
			$country    = WC()->countries->get_base_country();
			$locale     = $gateway->get_country_locale();
			$bacs_info  = get_option( 'woocommerce_bacs_accounts');

			// Get sortcode label in the $locale array and use appropriate one
			$sort_code_label = isset( $locale[ $country ]['sortcode']['label'] ) ? $locale[ $country ]['sortcode']['label'] : __( 'Sort code', 'woocommerce' );

			if($type == 'list'){
			?>
				<div class="woocommerce-bacs-bank-details list">
					<h2 class="wc-bacs-bank-details-heading">Dados Bancários</h2>
				<?php
					$i = -1;
					if($bacs_info){
						foreach ( $bacs_info as $account ){
							$i++;

							$account_name   = esc_attr( wp_unslash( $account['account_name'] ) );
							$bank_name      = esc_attr( wp_unslash( $account['bank_name'] ) );
							$account_number = esc_attr( $account['account_number'] );
							$sort_code      = esc_attr( $account['sort_code'] );
							$iban_code      = esc_attr( $account['iban'] );
							$bic_code       = esc_attr( $account['bic'] );
						?>
							<address class="address" style="line-height: 1.4;margin-bottom:35px;">
								<strong class="wc-bacs-bank-details-account-name"><?php echo $account_name; ?>:</strong><br/>
								<?php _e( 'Bank name', 'woocommerce' ); ?>: <strong><?php echo $bank_name; ?></strong><br/>
								<?php _e( 'Account number', 'woocommerce' ); ?>: <strong><?php echo $account_number; ?></strong><br/>
								<?php echo $sort_code_label; ?>: <strong><?php echo $sort_code; ?></strong><br/>
								<?php _e('IBAN'); ?>: <strong><?php echo $iban_code; ?></strong><br/>
								<?php _e('BIC'); ?>: <strong><?php echo $bic_code; ?></strong><br/>
							</address>
						<?php
						}
					}
				?>
				</div>
			<?php
			}elseif($type == 'thankyou'){
			?>
				<div class="woocommerce-bacs-instructions-thankyou">
					<p><?php echo $gateway->instructions; ?></p>
					<div class="woocommerce-bacs-bank-details thankyou">
					<?php
						$i = -1;
						if($bacs_info){
							foreach ( $bacs_info as $account ){
								$i++;

								$account_name   = esc_attr( wp_unslash( $account['account_name'] ) );
								$bank_name      = esc_attr( wp_unslash( $account['bank_name'] ) );
								$account_number = esc_attr( $account['account_number'] );
								$sort_code      = esc_attr( $account['sort_code'] );
								$iban_code      = esc_attr( $account['iban'] );
								$bic_code       = esc_attr( $account['bic'] );
							?>
								<address class="address" style="line-height: 1.6;">
									<strong class="wc-bacs-bank-details-account-name"><?php echo $account_name; ?>:</strong><br/>
									<?php _e( 'Bank name', 'woocommerce' ); ?>: <strong><?php echo $bank_name; ?></strong><br/>
									<?php _e( 'Account number', 'woocommerce' ); ?>: <strong><?php echo $account_number; ?></strong><br/>
									<?php echo $sort_code_label; ?>: <strong><?php echo $sort_code; ?></strong><br/>
									<?php _e('IBAN'); ?>: <strong><?php echo $iban_code; ?></strong><br/>
									<?php _e('BIC'); ?>: <strong><?php echo $bic_code; ?></strong><br/>
								</address>
							<?php
							}
						}
					?>
					</div>
				</div>
			<?php
			}else{
			?>
				<div class="woocommerce-bacs-bank-details table">
					<h2><?php _e( 'Account details', 'woocommerce' ); ?>:</h2>
					<table class="widefat wc_input_table" cellspacing="0">
						<thead>
							<tr>
								<th class="text-left"><?php _e( 'Account name', 'woocommerce' ); ?></th>
								<th class="text-left"><?php _e( 'Account number', 'woocommerce' ); ?></th>
								<th class="text-left"><?php _e( 'Bank name', 'woocommerce' ); ?></th>
								<th class="text-left"><?php echo $sort_code_label; ?></th>
							</tr>
						</thead>
						<tbody class="accounts">
							<?php
							$i = -1;
							if ( $bacs_info ) {
								foreach ( $bacs_info as $account ) {
									$i++;

									echo '<tr class="account">
										<td class="text-left"><span class="mob">'. __( 'Account name', 'woocommerce' ) .'</span>' . esc_attr( wp_unslash( $account['account_name'] ) ) . '</td>
										<td class="text-left"><span class="mob">'. __( 'Account number', 'woocommerce' ) .'</span>' . esc_attr( $account['account_number'] ) . '</td>
										<td class="text-left"><span class="mob">'. __( 'Bank name', 'woocommerce' ) .'</span>' . esc_attr( wp_unslash( $account['bank_name'] ) ) . '</td>
										<td class="text-left"><span class="mob">'.$sort_code_label .'</span>' . esc_attr( $account['sort_code'] ) . '</td>
									</tr>';
								}
							}
							?>
						</tbody>
					</table>
				</div>
			<?php
			}
			
			$output = ob_get_clean();

			if ( $echo )
				echo $output;
			else
				return $output;
		}
		public function thank_you_bacs($txt, $order){
			if($order->get_payment_method() === 'bacs'){
				echo '
					<div class="thank_you_bacs text-center">
						<h2>Sua reseva foi feita, efetue o pagamento para confirmar.</h2>
						<p>Para confirmar sua passagem, efetue o pagamento em nossa conta bancária e envie o comprovante em seu <a href="'. $order->get_view_order_url() .'" title="Ver Pedido #'. $order->id .'">painel</a>.</p>
						<p>Ou por whatsapp: <a href="https://web.whatsapp.com/send?l=pt_pt&phone=5538991616111&text='.urlencode('Gostaria de enviar o comprovante de depósito referente ao pedido #'.$order->id).'" target="_blank" title="Whatsapp: (38) 9 9161-6111 Tim">(38) 9 9161-6111 Tim</a> | <a href="https://web.whatsapp.com/send?l=pt_pt&phone=5538999881431&text='.urlencode('Gostaria de enviar o comprovante de depósito referente ao pedido #'.$order->id).'" target="_blank" title="Whatsapp: (38) 9 9988-1431 Vivo">(38) 9 9988-1431 Vivo</a></p>
						<span class="obs">Os dados para depósito seguem junto com o resumo do pedido.</span>
				';
				echo '</div>';
			}else{
				echo '<h2>'.$txt.'</h2>';
			}
		}
		
		// ADD USERS
		public function brc_add_user($post=false){
			if(isset($post['action']) and $post['action']){
			
				$obr = array(
					'billing_first_name', 
					'billing_cpf', 
					'billing_email', 
					'billing_cellphone', 
					
					'billing_address_1',
					'billing_state',
					'billing_number',
					'billing_city',
					'billing_neighborhood',
					
					'asenha', 
					'bsenha'
				);
				
				$post['billing_email'] 	= filter_var($post['billing_email'], FILTER_VALIDATE_EMAIL)?$post['billing_email']:false;
				$post['billing_cpf'] 	= validaCPF($post['billing_cpf'])?$post['billing_cpf']:false;
				$post['asenha'] 		= md5($post['asenha']) == md5($post['bsenha'])?$post['asenha']:false;
				
				$verf = camposFormVerf($obr, $post);
				if(!$verf){
					
					$user_id = wp_insert_user(
						array(
							'user_login'   => $post['billing_email'],
							'user_pass'    => $post['asenha'],
							'first_name'   => $post['billing_first_name'],
							'last_name'    => $post['billing_last_name'],
							'user_email'   => $post['billing_email'],
							'display_name' => $post['billing_first_name'].' '.$post['billing_last_name'],
							'nickname'     => $post['billing_first_name'],
							'role'         => 'customer'
						)
					);
					if(isset($user_id->errors)){
						if(isset($user_id->errors['existing_user_email'])){
							wc_add_notice('Um cadastro referente ao e-mail informado já existe, caso não você não lembre a senha de acesso, tente recuperar <a href="'. esc_url( wp_lostpassword_url() ) .'">clicando aqui</a>', 'error');
						}else{
							wc_add_notice('Um erro INESPERADO ocorreu, por favor tente novamente.', 'error');
						}
					}else{
						update_user_meta($user_id, 'billing_cpf', $post['billing_cpf']);
						update_user_meta($user_id, 'billing_country', $post['billing_country']);
						update_user_meta($user_id, 'billing_address_1', $post['billing_address_1']);
						update_user_meta($user_id, 'billing_number', $post['billing_number']);
						update_user_meta($user_id, 'billing_address_2', $post['billing_address_2']);
						update_user_meta($user_id, 'billing_neighborhood', $post['billing_neighborhood']);
						update_user_meta($user_id, 'billing_city', $post['billing_city']);
						update_user_meta($user_id, 'billing_state', $post['billing_state']);
						update_user_meta($user_id, 'billing_cellphone', $post['billing_cellphone']);
						//update_user_meta($user_id, 'billing_phone', $post['billing_phone']);
						update_user_meta($user_id, 'billing_email', $post['billing_email']);
						update_user_meta($user_id, 'billing_first_name', $post['billing_first_name']);
						update_user_meta($user_id, 'billing_last_name', $post['billing_last_name']);
						wc_add_notice('Seu cadastro foi criado com sucesso! <br/><a href="'.get_permalink(get_option('woocommerce_myaccount_page_id')).'">Clique aqui para fazer seu login.</a>', 'success');
						
						$wc = new WC_Emails();
						$wc->customer_new_account($user_id);
					}
				}
				else{
					foreach($verf as $k => $j){
						switch($j){
							case 'billing_first_name':
								wc_add_notice('Por favor informe um <strong>NOME</strong> válido', 'error');
							break;
							case 'billing_cpf':
								wc_add_notice('Por favor informe um <strong>CPF</strong> válido', 'error');
							break;
							case 'billing_email':
								wc_add_notice('Por favor informe um <strong>E-MAIL</strong> válido', 'error');
							break;
							case 'billing_cellphone':
								wc_add_notice('Por favor informe um <strong>NÚMERO DE CELULAR</strong> válido', 'error');
							break;
							case 'billing_address_1':
							case 'billing_state':
							case 'billing_number':
							case 'billing_city':
							case 'billing_neighborhood':
								$erro['endereco'] = true;
							break;
							case 'asenha':
							case 'bsenha':
								wc_add_notice('Por favor informe as <strong>SENHAS</strong> corretamente.', 'error');
							break;
							default:
								wc_add_notice('Por favor informe os campos corretamente.', 'error');
							break;
						}
					}
					if($erro['endereco']){
						wc_add_notice('Informe o endereço completo.', 'error');
					}
				}
			}
		}
	}
?>
