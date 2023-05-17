<?php
	class FEPAAdminUser{
		protected static $instance = null;
		
		function __construct(){
			
			add_action('user_new_form', array($this, 'brc_add_user_fields'), 999 );
			add_action('editable_roles', array($this, 'brc_hide_adminstrator_editable_roles'));
			add_action('admin_enqueue_scripts', array($this, 'brc_admin_theme_style'));
			add_action('login_enqueue_scripts', array($this, 'brc_admin_theme_style'));
			add_action('user_register', array($this, 'brc_save_user'));
			
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
		
		public function brc_add_user_fields(){
			if(current_user_can('administradorsimples') ){
				include FEPA_PLUGIN_DIR . '/templates/brc_admin.user_add.php';
			}
		}
		
		public function brc_hide_adminstrator_editable_roles($roles){
			if(current_user_can('gerente')){
				unset($roles['shop_manager']);
				unset($roles['translator']);
				unset($roles['subscriber']);
				unset($roles['contributor']);
				unset($roles['author']);
				unset($roles['editor']);
			}
			if(current_user_can('administradorsimples')){
				unset($roles['shop_manager']);
				unset($roles['translator']);
				unset($roles['subscriber']);
				unset($roles['contributor']);
				unset($roles['author']);
				unset($roles['editor']);
				unset($roles['gerente']);
				unset($roles['administradorsimples']);
				unset($roles['operadordeencomenda']);
			}
			return $roles;
		}
		
		public function brc_admin_theme_style(){
			global $current_user;
			
			$role = $current_user->roles[0];
			if(
				$role == 'gerente' or
				$role == 'administradorsimples' or
				$role == 'operadordeencomenda'
			){
				echo '<style>.update-nag, .updated, .error, .is-dismissible { display: none !important; }</style>';
			}
		}
		
		public function brc_save_user($user_id){
			update_user_meta($user_id, 'billing_cpf', $_POST['billing_cpf']);
			update_user_meta($user_id, 'billing_country', $_POST['billing_country']);
			update_user_meta($user_id, 'billing_address_1', $_POST['billing_address_1']);
			update_user_meta($user_id, 'billing_number', $_POST['billing_number']);
			update_user_meta($user_id, 'billing_address_2', $_POST['billing_address_2']);
			update_user_meta($user_id, 'billing_neighborhood', $_POST['billing_neighborhood']);
			update_user_meta($user_id, 'billing_city', $_POST['billing_city']);
			update_user_meta($user_id, 'billing_state', $_POST['billing_state']);
			update_user_meta($user_id, 'billing_cellphone', $_POST['billing_cellphone']);
		}
		
		
		
		
		//.FEPAAdminUser
	}		
?>