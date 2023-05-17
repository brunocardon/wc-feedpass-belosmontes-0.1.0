<?php
	global $current_user, $wp_query, $post;
	
	/*
		brc_admin.ticket.php
		brc_admin.ticket-product.php
		brc_admin.ticket-order.php
		brc_admin.ticket-individual.php
	*/

	$p = base64_decode($_GET['p']);
	$p = explode('|', $p);
	$order_id = $p[0];
	$order_item_id = $p[1];
	$order_item_passageiro = $p[2];
	$poder_post = get_post($order_id);
	$largura = 80;
	$altura = 260;
	$page_itens = false;
	
	switch($_GET['lista']){
		case 'product':
			echo '---------------product';
			include FEPA_PLUGIN_DIR . '/templates/brc_admin.ticket-product.php'; 
		break;
		case 'order':
			echo '---------------order';
			include FEPA_PLUGIN_DIR . '/templates/brc_admin.ticket-order.php'; 
		break;
		default:
			echo '---------------individual';
			include FEPA_PLUGIN_DIR . '/templates/brc_admin.ticket-individual.php'; 
		break;
	}
	
	pre_debug($page_itens);
	
	if($page_itens){
		require(FEPA_PLUGIN_DIR.'/plugins/fpdf.php');
		
		$pdf = new FPDF('P','mm',array($largura,$altura));
		
		ksort($page_itens);
		foreach($page_itens as $ticket_id => $ticket_data){
			$ticket_data['args']['largura'] = $largura;
			$ticket_data['args']['altura'] = $altura;
			$pdf = get_ticket_bilhete($pdf, $ticket_data['args'], $ticket_data['ticket'], $ticket_data['ticket_admin']);
		}
		$pdf->Output();
	}else{
		/*
		$wp_query->set_404();
		status_header(404);
		get_template_part(404);
		exit;
		//*/
	}
?>

