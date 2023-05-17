<?php
/*
 * Plugin Name: FeedPass Sistema de passagens [BETA]
 * Description: Sistema de venda de passagens e turismo.
 * Plugin URI:  https://www.agenciafeedback.com.br/
 * Version:     0.1.0
 * Author:      Bruno Roberto Cardon
 * Author URI:  http://brunocardon.com.br
 * License:	 	GPL2
 * License 		URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Copyright 2021 Bruno Cardon  (email : bcardonps@yahoo.com)
 */
	
	if(!defined('FEPA_PLUGIN')) 			define('FEPA_PLUGIN', __FILE__ );
	if(!defined('FEPA_PLUGIN_NAME')) 		define('FEPA_PLUGIN_NAME', 'FeedPass Sistema de passagens [BETA]');
	if(!defined('FEPA_PLUGIN_FOLDER')) 		define('FEPA_PLUGIN_FOLDER', plugins_url('feedpass'));
	if(!defined('FEPA_PLUGIN_DIR')) 		define('FEPA_PLUGIN_DIR', untrailingslashit(dirname(FEPA_PLUGIN)));
	//if(!defined('FEPA_PLUGIN_DIR')) 		define('FEPA_PLUGIN_URL', untrailingslashit(dirname(FEPA_PLUGIN))); // modificar
	if(!defined('FEPA_PLUGIN_URL')) 		define('FEPA_PLUGIN_URL', get_template_directory_uri().'/feedpass'); // modificar
	if(!defined('FEPA_PREFIX')) 			define('FEPA_PREFIX', 'feedpass_');
	
	if(!defined('FEPA_SCRIPT_VERSION')) 	define('FEPA_SCRIPT_VERSION', 1.36); 
	
	
	$active_plugins = apply_filters('active_plugins', get_option('active_plugins'));
	if(in_array('woocommerce/woocommerce.php', $active_plugins)){
		
		require_once FEPA_PLUGIN_DIR . '/includes/plugins/cmb2/init.php';
		require_once FEPA_PLUGIN_DIR . '/includes/plugins/CMB2-grid-master/Cmb2GridPlugin.php';
		
		require_once FEPA_PLUGIN_DIR . '/includes/functions/functions.php';
		require_once FEPA_PLUGIN_DIR . '/includes/admin/admin.setup.class.php';
		require_once FEPA_PLUGIN_DIR . '/includes/admin/admin.user.class.php';
		require_once FEPA_PLUGIN_DIR . '/includes/posttype/shop_order.setup.class.php';
		require_once FEPA_PLUGIN_DIR . '/includes/posttype/shop_order.saving.class.php';
		require_once FEPA_PLUGIN_DIR . '/includes/posttype/product.setup.class.php';
		require_once FEPA_PLUGIN_DIR . '/includes/posttype/hoteis.setup.class.php';
		require_once FEPA_PLUGIN_DIR . '/includes/posttype/product.saving.class.php';
		
		require_once FEPA_PLUGIN_DIR . '/includes/taxonomies/taxonomies.setup.class.php';
		require_once FEPA_PLUGIN_DIR . '/includes/taxonomies/destino.tax.setup.class.php';
		require_once FEPA_PLUGIN_DIR . '/includes/taxonomies/hotel-categoria.tax.setup.class.php';
		require_once FEPA_PLUGIN_DIR . '/includes/taxonomies/hotel-local.tax.setup.class.php';
		require_once FEPA_PLUGIN_DIR . '/includes/taxonomies/linha.tax.setup.class.php';
		require_once FEPA_PLUGIN_DIR . '/includes/taxonomies/motorista.tax.setup.class.php';
		require_once FEPA_PLUGIN_DIR . '/includes/taxonomies/veiculo.tax.setup.class.php';
		require_once FEPA_PLUGIN_DIR . '/includes/taxonomies/servicos.tax.setup.class.php';
		
		require_once FEPA_PLUGIN_DIR . '/includes/taxonomies/cidade.tax.setup.class.php';
		require_once FEPA_PLUGIN_DIR . '/includes/taxonomies/ponto-de-embarque.tax.setup.class.php';
		require_once FEPA_PLUGIN_DIR . '/includes/taxonomies/linha-viagens.tax.setup.class.php';
		
		require_once FEPA_PLUGIN_DIR . '/includes/shortcodes/wpbakery-setup.php';
		require_once FEPA_PLUGIN_DIR . '/includes/shortcodes/pages-setup.php';
		require_once FEPA_PLUGIN_DIR . '/includes/theme/feedpass.themeclass.php';
		
		add_action('after_setup_theme', array(FEPAAdmin::init(), 'register'));
		add_action('after_setup_theme', array(FEPAAdminUser::init(), 'register'));
		add_action('after_setup_theme', array(FEPAPostTypeProduct::init(), 'register'));
		add_action('after_setup_theme', array(FEPAPostTypeShopOrder::init(), 'register'));
		add_action('after_setup_theme', array(FEPATax::init(), 'register'));
		add_action('after_setup_theme', array(FEPAPagesShortcodes::init(), 'register'));
		
		require_once FEPA_PLUGIN_DIR . '/modifiers/modify.class.php';
		add_action('after_setup_theme', array(FEPAModify::init(), 'register'));
		
		if(class_exists('WPBakeryShortCode'))
			add_action('after_setup_theme', array(FEPAVcMap::init(), 'register'));
			
	}else{
		add_action('admin_notices', function(){
			echo '<div class="error notice"><p>Para este o plugin <strong>'.FEPA_PLUGIN_NAME.'</strong> funcionar corretamente, é necessário que o woocommerce esteja ativo.</p></div>';
			return false;
		});
	}
?>