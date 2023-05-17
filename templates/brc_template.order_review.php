<?php
	$_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
	$post_ID = $_product->get_id();
	$tipo_cadastro = $cart_item['_product_tipo_cadastro'];
	
	if($_product->post_type == 'product_variation'){
		$variation_data = $_product->get_data();
		$var_id = $variation_data['id'];
		$product_id = $variation_data['parent_id'];
		$brc_excursao = get_post_meta($product_id, 'brc_excursao', true);
	}
	
	if($brc_excursao){
		include FEPA_PLUGIN_DIR . '/templates/brc_template.order_review-excursao.php'; 
	}else{
		include FEPA_PLUGIN_DIR . '/templates/brc_template.order_review-viagens-'.$tipo_cadastro.'.php';  
	}
?>