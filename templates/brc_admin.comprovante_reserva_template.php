<?php
	$p = base64_decode($_GET['p']);
	$p = explode('|', $p);
	$order_id = $p[0];
	$order_item_id = $p[1];
	$order_item_passageiro = $p[2];
	$poder_post = get_post($order_id);
	
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head><?php wp_head(); ?></head>
<body <?php body_class();?> itemscope itemtype="http://schema.org/WebPage">
	<div class="brc-comprovante-reserva">
	<?php
		if($poder_post->post_type == 'product' and $_GET['lista'] == 'product'){
			$product_id = $poder_post->ID;
			$orders = get_orders_ids_by_product_id($product_id, get_all_woocommerce_status_id());
			if($orders){
				foreach($orders as $orderID){
					
					//pre_debug($orderID);
					
					$order 					= new WC_Order($orderID);
					$order_user_id 			= $order->get_user_id();
					$cliente 				= '#'.$order_user_id.' '.get_user_meta($order_user_id, 'billing_first_name', true);
					$emissao 				= $order->get_date_created()->date("d/m/Y");
					$forma_de_pagamento 	= $order->get_payment_method_title();
					$status 				= wc_get_order_status_name($order->get_status('completed'));
					
					include FEPA_PLUGIN_DIR . '/templates/brc_admin.comprovante_reserva_template_body.php';
				}
			}
		}else{
			$order = new WC_Order($poder_post->ID);
			
			$orderID 				= $poder_post->ID;
			$order_user_id 			= $order->get_user_id();
			$cliente 				= '#'.$order_user_id.' '.get_user_meta($order_user_id, 'billing_first_name', true);
			$emissao 				= $order->get_date_created()->date("d/m/Y");
			$forma_de_pagamento 	= $order->get_payment_method_title();
			$status 				= wc_get_order_status_name($order->get_status('completed'));
			
			include FEPA_PLUGIN_DIR . '/templates/brc_admin.comprovante_reserva_template_body.php';
		}
	?>
	</div>
	<script>//var credenciado_imprimir = setTimeout(function(){window.print();}, 1000)</script>
</body>
</html>