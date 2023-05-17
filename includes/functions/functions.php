<?php 
	
	if(!function_exists('pre_debug')){
		function pre_debug($str){
			echo '<pre>'. print_r( $str, true ).'</pre>';
		}
	}
	function get_cmb2_product_metaboxes_prefix(){
		return 'brc_prod_';
	}
	function get_cmb2_term_metaboxes_prefix($taxonomy = 'taxonomy'){
		return 'brc_'. $taxonomy .'_';
	}
	function get_datepicker_attributes(){
		return array(
			'dateFormat'      	=> 'dd-mm-yy',
			'dayNames' 			=> array('Domingo', 'Segunda-Feira', 'Terca-Feira', 'Quarta-Feira', 'Quinta-Feira', 'Sexta-Feira', 'Sabado'),
			'dayNamesMin' 		=> array('DO', 'SE', 'TE', 'QUA', 'QUI', 'SE', 'SA'),
			'dayNamesShort' 	=> array('Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab'),
			'monthNames ' 		=> explode(',', 'Janeiro,Fevereiro,Marco,Abril,Maio,Junho,Julho,Agosto,Setembro,Outrubro,Novembro,Dezembro'),
			'monthNamesShort' 	=> array('Jan', 'Fev', 'Marc', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'),
			'nextText'        	=> 'Proximo',
			'prevText'        	=> 'Anterior',
			'currentText'     	=> 'Hoje',
			'closeText'       	=> 'Feito',
			'clearText'       	=> 'Limpar',
		);
	}
	function get_timepicker_attributes(){
		return array(
			'timeOnlyTitle' => 'Escolher Hora',
			'timeText'      => 'Resumo',
			'hourText'      => 'Hora',
			'minuteText'    => 'Min.',
			'secondText'    => 'Seg.',
			'currentText'   => 'Agora',
			'closeText'     => 'Feito',
			//'timeFormat'     => 'HH:mm',
			//'timeFormat'    => _x( 'hh:mm TT', 'Valid formatting string, as per http://trentrichardson.com/examples/timepicker/',
		);
	}
	function get_destinos($term_id = false){
		$ret_destinos = array();
		
		$brc_destinos = get_terms(array(
			'taxonomy' 		=> 'brc_destinos',
			'hide_empty' 	=> false,
			'orderby' 		=> 'name',
			'order' 		=> 'ASC',
			'parent' 		=> 0,
		));
		if(!is_wp_error($brc_destinos)){
			foreach($brc_destinos as $k => $j){
				$ret_destinos[$j->term_id] = $j->name;
				
				$childrens = get_terms(array(
					'taxonomy' 		=> 'brc_destinos',
					'hide_empty' 	=> false,
					'orderby' 		=> 'name',
					'order' 		=> 'ASC',
					'parent' 		=> $j->term_id,
				));
				if(!is_wp_error($childrens)){
					foreach($childrens as $ck => $cj){
						$ret_destinos[$cj->term_id] = $j->name.' - '.$cj->name;
					}
				}
			}
		}
		
		if($term_id)
			return $ret_destinos[$term_id];
		else
			return $ret_destinos;
	}
	function get_cidades($term_id = false){
		$ret_destinos = array();
		
		$brc_destinos = get_terms(array(
			'taxonomy' 		=> 'brc_destinos',
			'hide_empty' 	=> false,
			'orderby' 		=> 'name',
			'order' 		=> 'ASC',
			'parent' 		=> 0,
		));
		if(!is_wp_error($brc_destinos)){
			foreach($brc_destinos as $k => $j){
				$ret_destinos[$j->term_id] = $j->name;
			}
		}
		
		if($term_id)
			return $ret_destinos[$term_id];
		else
			return $ret_destinos;
	}
	function get_embarques($term_id = false){
		$ret_destinos = array();
		
		$brc_destinos = get_terms(array(
			'taxonomy' 		=> 'brc_destinos',
			'hide_empty' 	=> false,
			'orderby' 		=> 'name',
			'order' 		=> 'ASC',
			'parent' 		=> 0,
		));
		if(!is_wp_error($brc_destinos)){
			foreach($brc_destinos as $k => $j){
				$childrens = get_terms(array(
					'taxonomy' 		=> 'brc_destinos',
					'hide_empty' 	=> false,
					'orderby' 		=> 'name',
					'order' 		=> 'ASC',
					'parent' 		=> $j->term_id,
				));
				if(!is_wp_error($childrens)){
					foreach($childrens as $ck => $cj){
						$ret_destinos[$cj->term_id] = $j->name.' - '.$cj->name;
					}
				}
			}
		}
		
		if($term_id)
			return $ret_destinos[$term_id];
		else
			return $ret_destinos;
	}
	function get_acomodacoes($id = false){
		$acomodacoes = array(
			1 => 'Leito',
			2 => 'Semi-leito',
			3 => 'Normal',
		);
		
		if($id)
			return $acomodacoes[$id];
		else
			return $acomodacoes;
	}
	function get_date_timestamp($date = false){ // 21/12/1989
		if($date){
			$date = str_replace('/', '-', $date);
			$date = strtotime($date);
		}
		return $date;
	}
	function get_percurso_tempo($ini=false, $fim=false){
		if($ini and $fim){
			$time = (($fim - $ini)/60)/60;
			
			if($time >= 1){
				return sprintf('%02dh %02dm', (int) $time, fmod($time, 1) * 60);
			}else{
				return $time.'m';
			}
		}else{
			
			return 0;
		}
	}
	function get_min_to_hora($temp){
		if($temp){
			$time = $temp/60;
			
			if($time >= 1){
				return sprintf('%02dh %02dm', (int) $time, fmod($time, 1) * 60);
			}else{
				return $temp.'m';
			}
		}else{
			
			return 0;
		}
	}
	function get_ticket_bilhete($pdf=false, $args, $ticket, $ticket_admin=false){
		$prefix = 'brc_ticket_admin_settings_sec';
		$ticket_admin = wp_parse_args($ticket_admin, array( 
			'logo_url' 				=> get_option('brc_ticket_admin_settings_logo_url'),
			'qrcode_url' 			=> get_option('brc_ticket_admin_settings_qrcode_url'),
			'01_titulo' 			=> get_option($prefix.'_01_titulo'),
			'01_razao_social' 		=> get_option($prefix.'_01_razao_social'),
			'01_insc_est' 			=> get_option($prefix.'_01_insc_est'),
			'01_endereco' 			=> get_option($prefix.'_01_endereco'),
			'01_extra' 				=> get_option($prefix.'_01_extra'),
			'02_prefixo' 			=> get_option($prefix.'_02_prefixo'),
			'02_agencia' 			=> get_option($prefix.'_02_agencia'),
			'02_agente' 			=> get_option($prefix.'_02_agente'),
			'02_pedagio' 			=> get_option($prefix.'_02_pedagio'),
			'02_taxa_embarque' 		=> get_option($prefix.'_02_taxa_embarque'),
			'02_aliq_icms' 			=> get_option($prefix.'_02_aliq_icms'),
			'02_valor_tributo' 		=> get_option($prefix.'_02_valor_tributo'),
			'02_anexo_01' 			=> get_option($prefix.'_02_anexo_01'),
			'02_anexo_02' 			=> get_option($prefix.'_02_anexo_02'),
			'02_anexo_03' 			=> get_option($prefix.'_02_anexo_03'),
		));
		$args = wp_parse_args($args, array( 
			'margem' 			=> 1.3,
			'altura' 			=> 230,
			'largura' 			=> 80,
			'eltd_options_blu' 	=> get_option('eltd_options_blu'),
			'page_title' 		=> utf8_decode(get_bloginfo('name')),
		));
		extract($args);
		
		$pdf->AddPage();
		$pdf->SetTitle($page_title);
		$pdf->SetAutoPageBreak(false, $margem);
		$pdf->SetFont('Arial','',10);
		$pdf->SetFillColor(200,220,255);
		
		/*
		 * SECTION 01 
		 */
		$pdf->Line($margem, $margem, ($largura-$margem), $margem); // H
		$pdf->Line($margem, $margem+(7.2), ($largura-$margem), $margem+(7.2)); // H
		$pdf->Line($margem, $margem+(7.2)+(11.7), ($largura-$margem), $margem+(7.2)+(11.7)); // H
		$pdf->Line(($largura/2), $margem, ($largura/2), $margem+(7.2)); // v
		if($ticket_admin['logo_url'])
			$pdf->Image($ticket_admin['logo_url'], $margem+(($largura/2)-$margem-17.6)/2, $margem, 17.6, 7);
		$pdf->SetXY(($largura/2), $margem);
		$pdf->SetFont('Arial','',4);
		$pdf->Cell(($largura/2)-$margem,7.2,utf8_decode($ticket_admin['01_titulo']),0,0,'C');
		
		$pdf->SetFillColor(200,220,255);
		
		$pdf->SetXY(0, $margem+(8));
		$pdf->SetFont('Arial','',7.5);
		$pdf->Cell($largura,3,utf8_decode($ticket_admin['01_razao_social']),0,1,'C',false);
		
		$pdf->SetX(0);
		$pdf->SetFont('Arial','',6);
		$pdf->Cell($largura,3,utf8_decode('CNPJ: '. $ticket_admin['01_insc_est'] .' / Insc. Est.: '. $ticket_admin['01_insc_est'] .''),0,1,'C',false);
		
		$pdf->SetX(0);
		$pdf->SetFont('Arial','',4);
		$pdf->Cell($largura,2.2,utf8_decode($ticket_admin['01_endereco']),0,1,'C',false);
		
		$pdf->SetX(0);
		$pdf->SetFont('Arial','',4);
		$pdf->Cell($largura,2.2,utf8_decode($ticket_admin['01_extra']),0,1,'C',false);
		//-----------------
		
		
		/*
		 * SECTION 02 
		 */
		$y = $margem+(7.2)+(11.7);
		$toplinmargin = 0.5;
		$pdf->SetXY(0, $y);
		
		$pdf->Line($margem, $y+(7), ($largura-$margem), $y+(7)); // H
		$pdf->Line($margem, $y+(7*2), ($largura-$margem), $y+(7*2)); // H
		$pdf->Line($margem, $y+(7*3), ($largura-$margem), $y+(7*3)); // H
		$pdf->Line($margem, $y+(7*4), ($largura-$margem), $y+(7*4)); // H
		$pdf->Line(($largura/2), $y+(7), ($largura/2), $y+(7*4)); // v
		
		$pdf->SetXY(0.3, $y+$toplinmargin);
		$pdf->SetFont('Arial','',4);
		$pdf->Cell($largura-($margem*2),2,utf8_decode('Nome do Passageiro'),0,1,'L',false);
		$pdf->SetXY(1.2, $y+2.2);
		$pdf->SetFont('Arial','',8);
		$pdf->Cell($largura-($margem*2),5,utf8_decode($ticket['nome_do_passageiro']),0,1,'L',false);
		
		$pdf->SetXY(0.3, $y+$toplinmargin+(7*1));
		$pdf->SetFont('Arial','',4);
		$pdf->Cell(($largura/2)-($margem),2,utf8_decode('Identidade'),0,1,'L',false);
		$pdf->SetXY(1.2, $y+(7*1)+2.2);
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(($largura/2)-($margem),5,utf8_decode($ticket['identidade']),0,1,'L',false);
		
		$pdf->SetXY(($largura/2)+0.3, $y+$toplinmargin+(7*1));
		$pdf->SetFont('Arial','',4);
		$pdf->Cell(($largura/2)-($margem),2,utf8_decode('CPF'),0,1,'L',false);
		$pdf->SetXY(($largura/2)+1.2, $y+(7*1)+2.2);
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(($largura/2)-($margem),5,utf8_decode($ticket['cpf']),0,1,'L',false);
		
		$pdf->SetXY(0.3, $y+$toplinmargin+(7*2));
		$pdf->SetFont('Arial','',4);
		$pdf->Cell(($largura/2)-($margem),2,utf8_decode('De'),0,1,'L',false);
		$pdf->SetXY(1.2, $y+(7*2)+2.2);
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(($largura/2)-($margem),5,utf8_decode($ticket['de']),0,1,'L',false);
		
		$pdf->SetXY(($largura/2)+0.3, $y+$toplinmargin+(7*2));
		$pdf->SetFont('Arial','',4);
		$pdf->Cell(($largura/2)-($margem),2,utf8_decode('Para'),0,1,'L',false);
		$pdf->SetXY(($largura/2)+1.2, $y+(7*2)+2.2);
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(($largura/2)-($margem),5,utf8_decode($ticket['para']),0,1,'L',false);
		
		$pdf->SetXY(0.3, $y+$toplinmargin+(7*3));
		$pdf->SetFont('Arial','',4);
		$pdf->Cell(($largura/2)-($margem),2,utf8_decode('Linha'),0,1,'L',false);
		$pdf->SetXY(1.2, $y+(7*3)+2.2);
		$pdf->SetFont('Arial','',5);
		$pdf->Cell(($largura/2)-($margem),5,utf8_decode($ticket['linha']),0,1,'L',false);
		
		$pdf->SetXY(($largura/2)+0.3, $y+$toplinmargin+(7*3));
		$pdf->SetFont('Arial','',4);
		$pdf->Cell(($largura/2)-($margem),2,utf8_decode('Prefixo'),0,1,'L',false);
		$pdf->SetXY(($largura/2)+1.2, $y+(7*3)+2.2);
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(($largura/2)-($margem),5,utf8_decode($ticket_admin['02_prefixo']),0,1,'L',false);
		//-----------------
		
		
		/*
		 * SECTION 03 - LEFT
		 */
		$y = $margem+(7.2)+(11.7)+(7*4);
		$w = ($largura/2);
		$toplinmargin = 0.5;
		$pdf->SetXY($margem, $y);
		
		$pdf->Line($margem, $y+(7), $w, $y+(7)); // H
		$pdf->Line($margem, $y+(7*2), $w, $y+(7*2)); // H
		$pdf->Line($margem, $y+(7*3), $w, $y+(7*3)); // H
		$pdf->Line($margem, $y+(7*4), $w, $y+(7*4)); // H
		$pdf->Line($w, $y, $w, $y+(7*4)); // v
		$pdf->Line(($w/2)+($margem/2), $y, ($w/2)+($margem/2), $y+(7*3)); // v
		
		$pdf->SetXY(0.3, $y+$toplinmargin+(7*0));
		$pdf->SetFont('Arial','',4);
		$pdf->Cell(($largura/2)-($margem),2,utf8_decode('Data Viagem'),0,1,'L',false);
		$pdf->SetXY(1.2, $y+(7*0)+2.2);
		$pdf->SetFont('Arial','',7);
		$pdf->Cell(($largura/2)-($margem),5,utf8_decode($ticket['data_viagem']),0,1,'L',false);
		
		$pdf->SetXY(($w/2)+($margem/2), $y+$toplinmargin+(7*0));
		$pdf->SetFont('Arial','',4);
		$pdf->Cell(($largura/2)-($margem),2,utf8_decode('Agência'),0,1,'L',false);
		$pdf->SetXY(($w/2)+($margem/2)+1.2, $y+(7*0)+2.2);
		$pdf->SetFont('Arial','',5);
		$pdf->Cell(($largura/2)-($margem),5,utf8_decode($ticket_admin['02_agencia']),0,1,'L',false);
		
		$pdf->SetXY(0.3, $y+$toplinmargin+(7*1));
		$pdf->SetFont('Arial','',4);
		$pdf->Cell(($largura/2)-($margem),2,utf8_decode('Horário'),0,1,'L',false);
		$pdf->SetXY(1.2, $y+(7*1)+2.2);
		$pdf->SetFont('Arial','',7);
		$pdf->Cell(($largura/2)-($margem),5,utf8_decode($ticket['horario']),0,1,'L',false);
		
		$pdf->SetXY(($w/2)+($margem/2), $y+$toplinmargin+(7*1));
		$pdf->SetFont('Arial','',4);
		$pdf->Cell(($largura/2)-($margem),2,utf8_decode('Emissão'),0,1,'L',false);
		$pdf->SetXY(($w/2)+($margem/2)+1.2, $y+(7*1)+2.2);
		$pdf->SetFont('Arial','',7);
		$pdf->Cell(($largura/2)-($margem),5,utf8_decode($ticket['emissao']),0,1,'L',false);
		
		$pdf->SetXY(0.3, $y+$toplinmargin+(7*2));
		$pdf->SetFont('Arial','',4);
		$pdf->Cell(($largura/2)-($margem),2,utf8_decode('Poltrona'),0,1,'L',false);
		$pdf->SetXY(1.2, $y+(7*2)+2.2);
		$pdf->SetFont('Arial','',7);
		$pdf->Cell(($largura/2)-($margem),5,utf8_decode($ticket['poltrona']),0,1,'L',false);
		
		$pdf->SetXY(($w/2)+($margem/2), $y+$toplinmargin+(7*2));
		$pdf->SetFont('Arial','',4);
		$pdf->Cell(($w/2)-($margem),2,utf8_decode('Agente'),0,1,'L',false);
		$pdf->SetXY(($w/2)+($margem/2)+1.2, $y+(7*2)+2.2);
		$pdf->SetFont('Arial','',7);
		$pdf->Cell(($w/2)-($margem),5,utf8_decode($ticket_admin['02_agente']),0,1,'L',false);
		
		$pdf->SetXY(0.3, $y+$toplinmargin+(7*3));
		$pdf->SetFont('Arial','',4);
		$pdf->Cell(($largura/2)-($margem),2,utf8_decode('Forma de Pagamento'),0,1,'L',false);
		$pdf->SetXY(1.2, $y+(7*3)+2.2);
		$pdf->SetFont('Arial','B',5.5);
		$pdf->Cell(($largura/2)-($margem),5,utf8_decode($ticket['forma_de_pagamento']),0,1,'L',false);
		//-----------------
		
		
		/*
		 * SECTION 04 - right
		 */
		$y = $margem+(7.2)+(11.7)+(7*4);
		$w = ($largura/2);
		$toplinmargin = 0.5;
		$pdf->SetXY($margem+$w, $y);
		
		$pdf->Line($w, $y+(3), $largura-$margem, $y+(3)); // H
		$pdf->Line($w, $y+(5*1)+3, $largura-$margem, $y+(5*1)+3); // H
		$pdf->Line($w, $y+(5*2)+3, $largura-$margem, $y+(5*2)+3); // H
		$pdf->Line($w, $y+(5*3)+3, $largura-$margem, $y+(5*3)+3); // H
		$pdf->Line($w, $y+(5*4)+3, $largura-$margem, $y+(5*4)+3); // H
		$pdf->Line($w, $y+(5*5)+3, $largura-$margem, $y+(5*5)+3); // H
		$pdf->Line($w+($w/2)-($margem/2), $y, $w+($w/2)-($margem/2), $y+(5*5)+3); // v
		
		$pdf->SetXY($w, $y+$toplinmargin);
		$pdf->SetFont('Arial','',4);
		$pdf->Cell(($w/2)-($margem/2),2,utf8_decode('Discriminação'),0,0,'C',false);
		$pdf->Cell(($w/2)-($margem/2),2,utf8_decode('Valores R$'),0,0,'C',false);
		$pdf->ln();
		
		$pdf->SetXY($w, $y+(5*0)+3);
		$pdf->SetFont('Arial','',7);
		$pdf->Cell(($w/2)-($margem/2),5,utf8_decode('Tarifa'),0,0,'C',false);
		$pdf->Cell(($w/2)-($margem/2),5,utf8_decode($ticket['tarifa']),0,0,'C',false);
		$pdf->ln();
		
		$pdf->SetX($w);
		$pdf->Cell(($w/2)-($margem/2),5,utf8_decode('Pedágio'),0,0,'C',false);
		$pdf->Cell(($w/2)-($margem/2),5,utf8_decode($ticket_admin['02_pedagio']),0,0,'C',false);
		$pdf->ln();
		
		$pdf->SetX($w);
		$pdf->Cell(($w/2)-($margem/2),5,utf8_decode('Taxa Embarque'),0,0,'C',false);
		$pdf->Cell(($w/2)-($margem/2),5,utf8_decode($ticket_admin['02_taxa_embarque']),0,0,'C',false);
		$pdf->ln();
		
		$pdf->SetX($w);
		$pdf->Cell(($w/2)-($margem/2),5,utf8_decode('Alíq. ICMS'),0,0,'C',false);
		$pdf->Cell(($w/2)-($margem/2),5,utf8_decode($ticket_admin['02_aliq_icms']),0,0,'C',false);
		$pdf->ln();
		
		$pdf->SetX($w);
		$pdf->Cell(($w/2)-($margem/2),5,utf8_decode('Valor Tributo'),0,0,'C',false);
		$pdf->Cell(($w/2)-($margem/2),5,utf8_decode($ticket_admin['02_valor_tributo']),0,0,'C',false);
		$pdf->ln();
		
		$pdf->Line($margem, $y+(5*6)+3+2, $largura-$margem, $y+(5*6)+3+2); // H
		$pdf->SetX($margem);
		$pdf->Cell($largura-($largura/4)-($margem*1.5),7,utf8_decode('Total da Prestação'),0,0,'R',false);
		$pdf->Cell(($largura/4)-($margem/2),7,utf8_decode($ticket['total_da_prestacao']),0,0,'C',false);
		$pdf->ln();
		//-----------------
		
		
		/*
		 * SECTION 05 - right
		 */
		$y = $y+(5*6)+3+2+1.5;
		$w = ($largura/2);
		$toplinmargin = 0.5;
		$pdf->SetXY($margem, $y);
		
		$pdf->SetFont('Arial','',7.5);
		$pdf->Cell($largura-($margem*2),3.5,utf8_decode($ticket_admin['02_anexo_01']),0,1,'C',false);
		$pdf->SetX($margem);
		$pdf->SetFont('Arial','',6);
		$pdf->Cell($largura-($margem*2),3,utf8_decode($ticket_admin['02_anexo_02']),0,1,'C',false);
		
		$pdf->SetXY(0, $y+8);
		$pdf->SetFont('Arial','',5.45);
		$pdf->MultiCell($largura,2.55,utf8_decode($ticket_admin['02_anexo_03']),0,'L',false);
		
		// QR CODE 50 mm²
		$qrsize = 35;
		if($ticket_admin['qrcode_url'])
			$pdf->Image($ticket_admin['qrcode_url'], ($largura/2)-($qrsize/2), $altura-($margem*2)-$qrsize, $qrsize, $qrsize);
		$pdf->Line($margem, $altura-$margem, $largura-$margem, $altura-$margem); // H
	
		return $pdf;
	}
	function get_all_woocommerce_status_id(){
		return array(
			'wc-pending',
			'wc-processing',
			'wc-on-hold',
			'wc-completed',
			'wc-cancelled',
			'wc-refunded',
			'wc-failed',
		);
	}
	function get_orders_ids_by_product_id($product_id, $order_status = array('wc-completed')){
		global $wpdb;
		/*
			'wc-pending' 		=> 'Pending',
			'wc-processing' 	=> 'Processing',
			'wc-on-hold' 		=> 'On-Hold',
			'wc-completed' 		=> 'Completed',
			'wc-cancelled' 		=> 'Cancelled',
			'wc-refunded' 		=> 'Refunded',
			'wc-failed' 		=> 'Failed',
		*/
		$results = $wpdb->get_col("
			SELECT order_items.order_id
			FROM {$wpdb->prefix}woocommerce_order_items as order_items
			LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as order_item_meta ON order_items.order_item_id = order_item_meta.order_item_id
			LEFT JOIN {$wpdb->posts} AS posts ON order_items.order_id = posts.ID
			WHERE posts.post_type = 'shop_order'
			AND posts.post_status IN ( '" . implode( "','", $order_status ) . "' )
			AND order_items.order_item_type = 'line_item'
			AND order_item_meta.meta_key = '_product_id'
			AND order_item_meta.meta_value = '$product_id'
		");
		if($results){
			foreach($results as $k => $j){
				$ret[$j] = $j;
			}
			$results = $ret;
		}else{
			return $results;
		}
		return $results;
	}
	function get_payment_methods_title($payment_id=false){
		global $woocommerce;
		
		$sel_payment = array();
		if($woocommerce->payment_gateways->get_available_payment_gateways()){
			foreach($woocommerce->payment_gateways->get_available_payment_gateways() as $k => $j){
				$sel_payment[$j->id] = $j->title;
			}
		}
		
		if($payment_id){
			return $sel_payment[$payment_id];
		}
		return $sel_payment;
	}
	function set_zero_in_front($num){
		return intval($num)<10?'0'.$num:$num;
	}
	
	// ST get_min_to_hora 
	function brc_get_fa_iconsarray(){
		$icons = array(
			'fa-500px' => '\f26e',
			'fa-adjust' => '\f042',
			'fa-adn' => '\f170',
			'fa-align-center' => '\f037',
			'fa-align-justify' => '\f039',
			'fa-align-left' => '\f036',
			'fa-align-right' => '\f038',
			'fa-amazon' => '\f270',
			'fa-ambulance' => '\f0f9',
			'fa-american-sign-language-interpreting' => '\f2a3',
			'fa-anchor' => '\f13d',
			'fa-android' => '\f17b',
			'fa-angellist' => '\f209',
			'fa-angle-double-down' => '\f103',
			'fa-angle-double-left' => '\f100',
			'fa-angle-double-right' => '\f101',
			'fa-angle-double-up' => '\f102',
			'fa-angle-down' => '\f107',
			'fa-angle-left' => '\f104',
			'fa-angle-right' => '\f105',
			'fa-angle-up' => '\f106',
			'fa-apple' => '\f179',
			'fa-archive' => '\f187',
			'fa-area-chart' => '\f1fe',
			'fa-arrow-circle-down' => '\f0ab',
			'fa-arrow-circle-left' => '\f0a8',
			'fa-arrow-circle-o-down' => '\f01a',
			'fa-arrow-circle-o-left' => '\f190',
			'fa-arrow-circle-o-right' => '\f18e',
			'fa-arrow-circle-o-up' => '\f01b',
			'fa-arrow-circle-right' => '\f0a9',
			'fa-arrow-circle-up' => '\f0aa',
			'fa-arrow-down' => '\f063',
			'fa-arrow-left' => '\f060',
			'fa-arrow-right' => '\f061',
			'fa-arrow-up' => '\f062',
			'fa-arrows' => '\f047',
			'fa-arrows-alt' => '\f0b2',
			'fa-arrows-h' => '\f07e',
			'fa-arrows-v' => '\f07d',
			'fa-assistive-listening-systems' => '\f2a2',
			'fa-asterisk' => '\f069',
			'fa-at' => '\f1fa',
			'fa-audio-description' => '\f29e',
			'fa-backward' => '\f04a',
			'fa-balance-scale' => '\f24e',
			'fa-ban' => '\f05e',
			'fa-bar-chart' => '\f080',
			'fa-barcode' => '\f02a',
			'fa-bars' => '\f0c9',
			'fa-battery-empty' => '\f244',
			'fa-battery-full' => '\f240',
			'fa-battery-half' => '\f242',
			'fa-battery-quarter' => '\f243',
			'fa-battery-three-quarters' => '\f241',
			'fa-bed' => '\f236',
			'fa-beer' => '\f0fc',
			'fa-behance' => '\f1b4',
			'fa-behance-square' => '\f1b5',
			'fa-bell' => '\f0f3',
			'fa-bell-o' => '\f0a2',
			'fa-bell-slash' => '\f1f6',
			'fa-bell-slash-o' => '\f1f7',
			'fa-bicycle' => '\f206',
			'fa-binoculars' => '\f1e5',
			'fa-birthday-cake' => '\f1fd',
			'fa-bitbucket' => '\f171',
			'fa-bitbucket-square' => '\f172',
			'fa-black-tie' => '\f27e',
			'fa-blind' => '\f29d',
			'fa-bluetooth' => '\f293',
			'fa-bluetooth-b' => '\f294',
			'fa-bold' => '\f032',
			'fa-bolt' => '\f0e7',
			'fa-bomb' => '\f1e2',
			'fa-book' => '\f02d',
			'fa-bookmark' => '\f02e',
			'fa-bookmark-o' => '\f097',
			'fa-braille' => '\f2a1',
			'fa-briefcase' => '\f0b1',
			'fa-btc' => '\f15a',
			'fa-bug' => '\f188',
			'fa-building' => '\f1ad',
			'fa-building-o' => '\f0f7',
			'fa-bullhorn' => '\f0a1',
			'fa-bullseye' => '\f140',
			'fa-bus' => '\f207',
			'fa-buysellads' => '\f20d',
			'fa-calculator' => '\f1ec',
			'fa-calendar' => '\f073',
			'fa-calendar-check-o' => '\f274',
			'fa-calendar-minus-o' => '\f272',
			'fa-calendar-o' => '\f133',
			'fa-calendar-plus-o' => '\f271',
			'fa-calendar-times-o' => '\f273',
			'fa-camera' => '\f030',
			'fa-camera-retro' => '\f083',
			'fa-car' => '\f1b9',
			'fa-caret-down' => '\f0d7',
			'fa-caret-left' => '\f0d9',
			'fa-caret-right' => '\f0da',
			'fa-caret-square-o-down' => '\f150',
			'fa-caret-square-o-left' => '\f191',
			'fa-caret-square-o-right' => '\f152',
			'fa-caret-square-o-up' => '\f151',
			'fa-caret-up' => '\f0d8',
			'fa-cart-arrow-down' => '\f218',
			'fa-cart-plus' => '\f217',
			'fa-cc' => '\f20a',
			'fa-cc-amex' => '\f1f3',
			'fa-cc-diners-club' => '\f24c',
			'fa-cc-discover' => '\f1f2',
			'fa-cc-jcb' => '\f24b',
			'fa-cc-mastercard' => '\f1f1',
			'fa-cc-paypal' => '\f1f4',
			'fa-cc-stripe' => '\f1f5',
			'fa-cc-visa' => '\f1f0',
			'fa-certificate' => '\f0a3',
			'fa-chain-broken' => '\f127',
			'fa-check' => '\f00c',
			'fa-check-circle' => '\f058',
			'fa-check-circle-o' => '\f05d',
			'fa-check-square' => '\f14a',
			'fa-check-square-o' => '\f046',
			'fa-chevron-circle-down' => '\f13a',
			'fa-chevron-circle-left' => '\f137',
			'fa-chevron-circle-right' => '\f138',
			'fa-chevron-circle-up' => '\f139',
			'fa-chevron-down' => '\f078',
			'fa-chevron-left' => '\f053',
			'fa-chevron-right' => '\f054',
			'fa-chevron-up' => '\f077',
			'fa-child' => '\f1ae',
			'fa-chrome' => '\f268',
			'fa-circle' => '\f111',
			'fa-circle-o' => '\f10c',
			'fa-circle-o-notch' => '\f1ce',
			'fa-circle-thin' => '\f1db',
			'fa-clipboard' => '\f0ea',
			'fa-clock-o' => '\f017',
			'fa-clone' => '\f24d',
			'fa-cloud' => '\f0c2',
			'fa-cloud-download' => '\f0ed',
			'fa-cloud-upload' => '\f0ee',
			'fa-code' => '\f121',
			'fa-code-fork' => '\f126',
			'fa-codepen' => '\f1cb',
			'fa-codiepie' => '\f284',
			'fa-coffee' => '\f0f4',
			'fa-cog' => '\f013',
			'fa-cogs' => '\f085',
			'fa-columns' => '\f0db',
			'fa-comment' => '\f075',
			'fa-comment-o' => '\f0e5',
			'fa-commenting' => '\f27a',
			'fa-commenting-o' => '\f27b',
			'fa-comments' => '\f086',
			'fa-comments-o' => '\f0e6',
			'fa-compass' => '\f14e',
			'fa-compress' => '\f066',
			'fa-connectdevelop' => '\f20e',
			'fa-contao' => '\f26d',
			'fa-copyright' => '\f1f9',
			'fa-creative-commons' => '\f25e',
			'fa-credit-card' => '\f09d',
			'fa-credit-card-alt' => '\f283',
			'fa-crop' => '\f125',
			'fa-crosshairs' => '\f05b',
			'fa-css3' => '\f13c',
			'fa-cube' => '\f1b2',
			'fa-cubes' => '\f1b3',
			'fa-cutlery' => '\f0f5',
			'fa-dashcube' => '\f210',
			'fa-database' => '\f1c0',
			'fa-deaf' => '\f2a4',
			'fa-delicious' => '\f1a5',
			'fa-desktop' => '\f108',
			'fa-deviantart' => '\f1bd',
			'fa-diamond' => '\f219',
			'fa-digg' => '\f1a6',
			'fa-dot-circle-o' => '\f192',
			'fa-download' => '\f019',
			'fa-dribbble' => '\f17d',
			'fa-dropbox' => '\f16b',
			'fa-drupal' => '\f1a9',
			'fa-edge' => '\f282',
			'fa-eject' => '\f052',
			'fa-ellipsis-h' => '\f141',
			'fa-ellipsis-v' => '\f142',
			'fa-empire' => '\f1d1',
			'fa-envelope' => '\f0e0',
			'fa-envelope-o' => '\f003',
			'fa-envelope-square' => '\f199',
			'fa-envira' => '\f299',
			'fa-eraser' => '\f12d',
			'fa-eur' => '\f153',
			'fa-exchange' => '\f0ec',
			'fa-exclamation' => '\f12a',
			'fa-exclamation-circle' => '\f06a',
			'fa-exclamation-triangle' => '\f071',
			'fa-expand' => '\f065',
			'fa-expeditedssl' => '\f23e',
			'fa-external-link' => '\f08e',
			'fa-external-link-square' => '\f14c',
			'fa-eye' => '\f06e',
			'fa-eye-slash' => '\f070',
			'fa-eyedropper' => '\f1fb',
			'fa-facebook' => '\f09a',
			'fa-facebook-official' => '\f230',
			'fa-facebook-square' => '\f082',
			'fa-fast-backward' => '\f049',
			'fa-fast-forward' => '\f050',
			'fa-fax' => '\f1ac',
			'fa-female' => '\f182',
			'fa-fighter-jet' => '\f0fb',
			'fa-file' => '\f15b',
			'fa-file-archive-o' => '\f1c6',
			'fa-file-audio-o' => '\f1c7',
			'fa-file-code-o' => '\f1c9',
			'fa-file-excel-o' => '\f1c3',
			'fa-file-image-o' => '\f1c5',
			'fa-file-o' => '\f016',
			'fa-file-pdf-o' => '\f1c1',
			'fa-file-powerpoint-o' => '\f1c4',
			'fa-file-text' => '\f15c',
			'fa-file-text-o' => '\f0f6',
			'fa-file-video-o' => '\f1c8',
			'fa-file-word-o' => '\f1c2',
			'fa-files-o' => '\f0c5',
			'fa-film' => '\f008',
			'fa-filter' => '\f0b0',
			'fa-fire' => '\f06d',
			'fa-fire-extinguisher' => '\f134',
			'fa-firefox' => '\f269',
			'fa-first-order' => '\f2b0',
			'fa-flag' => '\f024',
			'fa-flag-checkered' => '\f11e',
			'fa-flag-o' => '\f11d',
			'fa-flask' => '\f0c3',
			'fa-flickr' => '\f16e',
			'fa-floppy-o' => '\f0c7',
			'fa-folder' => '\f07b',
			'fa-folder-o' => '\f114',
			'fa-folder-open' => '\f07c',
			'fa-folder-open-o' => '\f115',
			'fa-font' => '\f031',
			'fa-font-awesome' => '\f2b4',
			'fa-fonticons' => '\f280',
			'fa-fort-awesome' => '\f286',
			'fa-forumbee' => '\f211',
			'fa-forward' => '\f04e',
			'fa-foursquare' => '\f180',
			'fa-frown-o' => '\f119',
			'fa-futbol-o' => '\f1e3',
			'fa-gamepad' => '\f11b',
			'fa-gavel' => '\f0e3',
			'fa-gbp' => '\f154',
			'fa-genderless' => '\f22d',
			'fa-get-pocket' => '\f265',
			'fa-gg' => '\f260',
			'fa-gg-circle' => '\f261',
			'fa-gift' => '\f06b',
			'fa-git' => '\f1d3',
			'fa-git-square' => '\f1d2',
			'fa-github' => '\f09b',
			'fa-github-alt' => '\f113',
			'fa-github-square' => '\f092',
			'fa-gitlab' => '\f296',
			'fa-glass' => '\f000',
			'fa-glide' => '\f2a5',
			'fa-glide-g' => '\f2a6',
			'fa-globe' => '\f0ac',
			'fa-google' => '\f1a0',
			'fa-google-plus' => '\f0d5',
			'fa-google-plus-official' => '\f2b3',
			'fa-google-plus-square' => '\f0d4',
			'fa-google-wallet' => '\f1ee',
			'fa-graduation-cap' => '\f19d',
			'fa-gratipay' => '\f184',
			'fa-h-square' => '\f0fd',
			'fa-hacker-news' => '\f1d4',
			'fa-hand-lizard-o' => '\f258',
			'fa-hand-o-down' => '\f0a7',
			'fa-hand-o-left' => '\f0a5',
			'fa-hand-o-right' => '\f0a4',
			'fa-hand-o-up' => '\f0a6',
			'fa-hand-paper-o' => '\f256',
			'fa-hand-peace-o' => '\f25b',
			'fa-hand-pointer-o' => '\f25a',
			'fa-hand-rock-o' => '\f255',
			'fa-hand-scissors-o' => '\f257',
			'fa-hand-spock-o' => '\f259',
			'fa-hashtag' => '\f292',
			'fa-hdd-o' => '\f0a0',
			'fa-header' => '\f1dc',
			'fa-headphones' => '\f025',
			'fa-heart' => '\f004',
			'fa-heart-o' => '\f08a',
			'fa-heartbeat' => '\f21e',
			'fa-history' => '\f1da',
			'fa-home' => '\f015',
			'fa-hospital-o' => '\f0f8',
			'fa-hourglass' => '\f254',
			'fa-hourglass-end' => '\f253',
			'fa-hourglass-half' => '\f252',
			'fa-hourglass-o' => '\f250',
			'fa-hourglass-start' => '\f251',
			'fa-houzz' => '\f27c',
			'fa-html5' => '\f13b',
			'fa-i-cursor' => '\f246',
			'fa-ils' => '\f20b',
			'fa-inbox' => '\f01c',
			'fa-indent' => '\f03c',
			'fa-industry' => '\f275',
			'fa-info' => '\f129',
			'fa-info-circle' => '\f05a',
			'fa-inr' => '\f156',
			'fa-instagram' => '\f16d',
			'fa-internet-explorer' => '\f26b',
			'fa-ioxhost' => '\f208',
			'fa-italic' => '\f033',
			'fa-joomla' => '\f1aa',
			'fa-jpy' => '\f157',
			'fa-jsfiddle' => '\f1cc',
			'fa-key' => '\f084',
			'fa-keyboard-o' => '\f11c',
			'fa-krw' => '\f159',
			'fa-language' => '\f1ab',
			'fa-laptop' => '\f109',
			'fa-lastfm' => '\f202',
			'fa-lastfm-square' => '\f203',
			'fa-leaf' => '\f06c',
			'fa-leanpub' => '\f212',
			'fa-lemon-o' => '\f094',
			'fa-level-down' => '\f149',
			'fa-level-up' => '\f148',
			'fa-life-ring' => '\f1cd',
			'fa-lightbulb-o' => '\f0eb',
			'fa-line-chart' => '\f201',
			'fa-link' => '\f0c1',
			'fa-linkedin' => '\f0e1',
			'fa-linkedin-square' => '\f08c',
			'fa-linux' => '\f17c',
			'fa-list' => '\f03a',
			'fa-list-alt' => '\f022',
			'fa-list-ol' => '\f0cb',
			'fa-list-ul' => '\f0ca',
			'fa-location-arrow' => '\f124',
			'fa-lock' => '\f023',
			'fa-long-arrow-down' => '\f175',
			'fa-long-arrow-left' => '\f177',
			'fa-long-arrow-right' => '\f178',
			'fa-long-arrow-up' => '\f176',
			'fa-low-vision' => '\f2a8',
			'fa-magic' => '\f0d0',
			'fa-magnet' => '\f076',
			'fa-male' => '\f183',
			'fa-map' => '\f279',
			'fa-map-marker' => '\f041',
			'fa-map-o' => '\f278',
			'fa-map-pin' => '\f276',
			'fa-map-signs' => '\f277',
			'fa-mars' => '\f222',
			'fa-mars-double' => '\f227',
			'fa-mars-stroke' => '\f229',
			'fa-mars-stroke-h' => '\f22b',
			'fa-mars-stroke-v' => '\f22a',
			'fa-maxcdn' => '\f136',
			'fa-meanpath' => '\f20c',
			'fa-medium' => '\f23a',
			'fa-medkit' => '\f0fa',
			'fa-meh-o' => '\f11a',
			'fa-mercury' => '\f223',
			'fa-microphone' => '\f130',
			'fa-microphone-slash' => '\f131',
			'fa-minus' => '\f068',
			'fa-minus-circle' => '\f056',
			'fa-minus-square' => '\f146',
			'fa-minus-square-o' => '\f147',
			'fa-mixcloud' => '\f289',
			'fa-mobile' => '\f10b',
			'fa-modx' => '\f285',
			'fa-money' => '\f0d6',
			'fa-moon-o' => '\f186',
			'fa-motorcycle' => '\f21c',
			'fa-mouse-pointer' => '\f245',
			'fa-music' => '\f001',
			'fa-neuter' => '\f22c',
			'fa-newspaper-o' => '\f1ea',
			'fa-object-group' => '\f247',
			'fa-object-ungroup' => '\f248',
			'fa-odnoklassniki' => '\f263',
			'fa-odnoklassniki-square' => '\f264',
			'fa-opencart' => '\f23d',
			'fa-openid' => '\f19b',
			'fa-opera' => '\f26a',
			'fa-optin-monster' => '\f23c',
			'fa-outdent' => '\f03b',
			'fa-pagelines' => '\f18c',
			'fa-paint-brush' => '\f1fc',
			'fa-paper-plane' => '\f1d8',
			'fa-paper-plane-o' => '\f1d9',
			'fa-paperclip' => '\f0c6',
			'fa-paragraph' => '\f1dd',
			'fa-pause' => '\f04c',
			'fa-pause-circle' => '\f28b',
			'fa-pause-circle-o' => '\f28c',
			'fa-paw' => '\f1b0',
			'fa-paypal' => '\f1ed',
			'fa-pencil' => '\f040',
			'fa-pencil-square' => '\f14b',
			'fa-pencil-square-o' => '\f044',
			'fa-percent' => '\f295',
			'fa-phone' => '\f095',
			'fa-phone-square' => '\f098',
			'fa-picture-o' => '\f03e',
			'fa-pie-chart' => '\f200',
			'fa-pied-piper' => '\f2ae',
			'fa-pied-piper-alt' => '\f1a8',
			'fa-pied-piper-pp' => '\f1a7',
			'fa-pinterest' => '\f0d2',
			'fa-pinterest-p' => '\f231',
			'fa-pinterest-square' => '\f0d3',
			'fa-plane' => '\f072',
			'fa-play' => '\f04b',
			'fa-play-circle' => '\f144',
			'fa-play-circle-o' => '\f01d',
			'fa-plug' => '\f1e6',
			'fa-plus' => '\f067',
			'fa-plus-circle' => '\f055',
			'fa-plus-square' => '\f0fe',
			'fa-plus-square-o' => '\f196',
			'fa-power-off' => '\f011',
			'fa-print' => '\f02f',
			'fa-product-hunt' => '\f288',
			'fa-puzzle-piece' => '\f12e',
			'fa-qq' => '\f1d6',
			'fa-qrcode' => '\f029',
			'fa-question' => '\f128',
			'fa-question-circle' => '\f059',
			'fa-question-circle-o' => '\f29c',
			'fa-quote-left' => '\f10d',
			'fa-quote-right' => '\f10e',
			'fa-random' => '\f074',
			'fa-rebel' => '\f1d0',
			'fa-recycle' => '\f1b8',
			'fa-reddit' => '\f1a1',
			'fa-reddit-alien' => '\f281',
			'fa-reddit-square' => '\f1a2',
			'fa-refresh' => '\f021',
			'fa-registered' => '\f25d',
			'fa-renren' => '\f18b',
			'fa-repeat' => '\f01e',
			'fa-reply' => '\f112',
			'fa-reply-all' => '\f122',
			'fa-retweet' => '\f079',
			'fa-road' => '\f018',
			'fa-rocket' => '\f135',
			'fa-rss' => '\f09e',
			'fa-rss-square' => '\f143',
			'fa-rub' => '\f158',
			'fa-safari' => '\f267',
			'fa-scissors' => '\f0c4',
			'fa-scribd' => '\f28a',
			'fa-search' => '\f002',
			'fa-search-minus' => '\f010',
			'fa-search-plus' => '\f00e',
			'fa-sellsy' => '\f213',
			'fa-server' => '\f233',
			'fa-share' => '\f064',
			'fa-share-alt' => '\f1e0',
			'fa-share-alt-square' => '\f1e1',
			'fa-share-square' => '\f14d',
			'fa-share-square-o' => '\f045',
			'fa-shield' => '\f132',
			'fa-ship' => '\f21a',
			'fa-shirtsinbulk' => '\f214',
			'fa-shopping-bag' => '\f290',
			'fa-shopping-basket' => '\f291',
			'fa-shopping-cart' => '\f07a',
			'fa-sign-in' => '\f090',
			'fa-sign-language' => '\f2a7',
			'fa-sign-out' => '\f08b',
			'fa-signal' => '\f012',
			'fa-simplybuilt' => '\f215',
			'fa-sitemap' => '\f0e8',
			'fa-skyatlas' => '\f216',
			'fa-skype' => '\f17e',
			'fa-slack' => '\f198',
			'fa-sliders' => '\f1de',
			'fa-slideshare' => '\f1e7',
			'fa-smile-o' => '\f118',
			'fa-snapchat' => '\f2ab',
			'fa-snapchat-ghost' => '\f2ac',
			'fa-snapchat-square' => '\f2ad',
			'fa-sort' => '\f0dc',
			'fa-sort-alpha-asc' => '\f15d',
			'fa-sort-alpha-desc' => '\f15e',
			'fa-sort-amount-asc' => '\f160',
			'fa-sort-amount-desc' => '\f161',
			'fa-sort-asc' => '\f0de',
			'fa-sort-desc' => '\f0dd',
			'fa-sort-numeric-asc' => '\f162',
			'fa-sort-numeric-desc' => '\f163',
			'fa-soundcloud' => '\f1be',
			'fa-space-shuttle' => '\f197',
			'fa-spinner' => '\f110',
			'fa-spoon' => '\f1b1',
			'fa-spotify' => '\f1bc',
			'fa-square' => '\f0c8',
			'fa-square-o' => '\f096',
			'fa-stack-exchange' => '\f18d',
			'fa-stack-overflow' => '\f16c',
			'fa-star' => '\f005',
			'fa-star-half' => '\f089',
			'fa-star-half-o' => '\f123',
			'fa-star-o' => '\f006',
			'fa-steam' => '\f1b6',
			'fa-steam-square' => '\f1b7',
			'fa-step-backward' => '\f048',
			'fa-step-forward' => '\f051',
			'fa-stethoscope' => '\f0f1',
			'fa-sticky-note' => '\f249',
			'fa-sticky-note-o' => '\f24a',
			'fa-stop' => '\f04d',
			'fa-stop-circle' => '\f28d',
			'fa-stop-circle-o' => '\f28e',
			'fa-street-view' => '\f21d',
			'fa-strikethrough' => '\f0cc',
			'fa-stumbleupon' => '\f1a4',
			'fa-stumbleupon-circle' => '\f1a3',
			'fa-subscript' => '\f12c',
			'fa-subway' => '\f239',
			'fa-suitcase' => '\f0f2',
			'fa-sun-o' => '\f185',
			'fa-superscript' => '\f12b',
			'fa-table' => '\f0ce',
			'fa-tablet' => '\f10a',
			'fa-tachometer' => '\f0e4',
			'fa-tag' => '\f02b',
			'fa-tags' => '\f02c',
			'fa-tasks' => '\f0ae',
			'fa-taxi' => '\f1ba',
			'fa-television' => '\f26c',
			'fa-tencent-weibo' => '\f1d5',
			'fa-terminal' => '\f120',
			'fa-text-height' => '\f034',
			'fa-text-width' => '\f035',
			'fa-th' => '\f00a',
			'fa-th-large' => '\f009',
			'fa-th-list' => '\f00b',
			'fa-themeisle' => '\f2b2',
			'fa-thumb-tack' => '\f08d',
			'fa-thumbs-down' => '\f165',
			'fa-thumbs-o-down' => '\f088',
			'fa-thumbs-o-up' => '\f087',
			'fa-thumbs-up' => '\f164',
			'fa-ticket' => '\f145',
			'fa-times' => '\f00d',
			'fa-times-circle' => '\f057',
			'fa-times-circle-o' => '\f05c',
			'fa-tint' => '\f043',
			'fa-toggle-off' => '\f204',
			'fa-toggle-on' => '\f205',
			'fa-trademark' => '\f25c',
			'fa-train' => '\f238',
			'fa-transgender' => '\f224',
			'fa-transgender-alt' => '\f225',
			'fa-trash' => '\f1f8',
			'fa-trash-o' => '\f014',
			'fa-tree' => '\f1bb',
			'fa-trello' => '\f181',
			'fa-tripadvisor' => '\f262',
			'fa-trophy' => '\f091',
			'fa-truck' => '\f0d1',
			'fa-try' => '\f195',
			'fa-tty' => '\f1e4',
			'fa-tumblr' => '\f173',
			'fa-tumblr-square' => '\f174',
			'fa-twitch' => '\f1e8',
			'fa-twitter' => '\f099',
			'fa-twitter-square' => '\f081',
			'fa-umbrella' => '\f0e9',
			'fa-underline' => '\f0cd',
			'fa-undo' => '\f0e2',
			'fa-universal-access' => '\f29a',
			'fa-university' => '\f19c',
			'fa-unlock' => '\f09c',
			'fa-unlock-alt' => '\f13e',
			'fa-upload' => '\f093',
			'fa-usb' => '\f287',
			'fa-usd' => '\f155',
			'fa-user' => '\f007',
			'fa-user-md' => '\f0f0',
			'fa-user-plus' => '\f234',
			'fa-user-secret' => '\f21b',
			'fa-user-times' => '\f235',
			'fa-users' => '\f0c0',
			'fa-venus' => '\f221',
			'fa-venus-double' => '\f226',
			'fa-venus-mars' => '\f228',
			'fa-viacoin' => '\f237',
			'fa-viadeo' => '\f2a9',
			'fa-viadeo-square' => '\f2aa',
			'fa-video-camera' => '\f03d',
			'fa-vimeo' => '\f27d',
			'fa-vimeo-square' => '\f194',
			'fa-vine' => '\f1ca',
			'fa-vk' => '\f189',
			'fa-volume-control-phone' => '\f2a0',
			'fa-volume-down' => '\f027',
			'fa-volume-off' => '\f026',
			'fa-volume-up' => '\f028',
			'fa-weibo' => '\f18a',
			'fa-weixin' => '\f1d7',
			'fa-whatsapp' => '\f232',
			'fa-wheelchair' => '\f193',
			'fa-wheelchair-alt' => '\f29b',
			'fa-wifi' => '\f1eb',
			'fa-wikipedia-w' => '\f266',
			'fa-windows' => '\f17a',
			'fa-wordpress' => '\f19a',
			'fa-wpbeginner' => '\f297',
			'fa-wpforms' => '\f298',
			'fa-wrench' => '\f0ad',
			'fa-xing' => '\f168',
			'fa-xing-square' => '\f169',
			'fa-y-combinator' => '\f23b',
			'fa-yahoo' => '\f19e',
			'fa-yelp' => '\f1e9',
			'fa-yoast' => '\f2b1',
			'fa-youtube' => '\f167',
			'fa-youtube-play' => '\f16a',
			'fa-youtube-square' => '\f166'
		);

		$fa_icons = array();
		$fa_icons[""] = "";
		foreach ($icons as $key => $value) {
			$fa_icons[$key] = $key;
		}

		return $icons;
	}
	if(!function_exists('moedaRealPrint')){
		function moedaRealPrint($valor){
			if($valor or $valor==0){
				return number_format($valor, 2, ',', '.');
			}
		}
	}
	if(!function_exists('moedaRealPrint')){
		function moedaRealPrint($valor){
			if($valor or $valor==0){
				return number_format($valor, 2, ',', '.');
			}
		}
	}
	if(!function_exists('diasemanasmall')){
		function diasemanasmall($sem=1){
			$semana = array(
				1 => 'SEG',
				2 => 'TER',
				3 => 'QUA',
				4 => 'QUI',
				5 => 'SEX',
				6 => 'SÁB',
				7 => 'DOM',
			);
			return $semana[$sem];
		}
	}
	if(!function_exists('validaCPF')){
		function validaCPF($cpf) {
			$cpf = preg_replace( '/[^0-9]/is', '', $cpf );
			 
			if (strlen($cpf) != 11) {
				return false;
			}

			if (preg_match('/(\d)\1{10}/', $cpf)) {
				return false;
			}

			for ($t = 9; $t < 11; $t++) {
				for ($d = 0, $c = 0; $c < $t; $c++) {
					$d += $cpf[$c] * (($t + 1) - $c);
				}
				$d = ((10 * $d) % 11) % 10;
				if ($cpf[$c] != $d) {
					return false;
				}
			}
			return true;
		}
	}
	if(!function_exists('has_dupes')){
		function has_dupes($array) {
			$dupe_array = array();
			foreach ($array as $val) {
				if (++$dupe_array[$val] > 1) {
					return true;
				}
			}
			return false;
		}
	}
	if(!function_exists('camposFormVerf')) {
		function camposFormVerf($obr, $post){
			foreach($obr as $i){
				if(is_array($i)){
					$keys = array_keys($i);
					$uni_ind = $keys[0];
					if(!$post[$i[$uni_ind]][$uni_ind]){
						$err[] = $i[$uni_ind].'['.$uni_ind.']';
					}
				}elseif(is_array($post[$i])){
					foreach($post[$i] as $ii=>$one){
						if(empty($one)){
							$err[] = array($ii, $i);
						}
					}
				}else{
					if(empty($post[$i])){
						$err[] = $i;
					}
				}
			}
			return $err;
		}
	}
	if(!function_exists('get_servicos')) {
		function get_servicos($key=false){
			
			$servicos = array();
			$terms = get_terms(array(
				'taxonomy' => 'brc_servicos',
				'hide_empty' => false,
			));
			if(!is_wp_error($terms)){
				foreach($terms as $term){
					$servicos[$term->slug] = $term->name;
				}
				
				if($key){
					return $servicos[$key];
				}else{
					return $servicos;
				}
			}
		}
	}
	if(!function_exists('order_item_meta_to_array')){
		function order_item_meta_to_array($meta_data=false){
			if($meta_data){
				foreach($meta_data as $k => $j){
					$inner_data = $j->get_data();
					$to_array[$inner_data['key']] = $inner_data['value'];
				}
			}
			
			return $to_array;
		}
	}
	if(!function_exists('get_quarto_numero')) {
		function get_quarto_numero($var_id, $quarto_id){
			$brc_excursao_variations_quartos_numeros = get_post_meta($var_id, 'brc_excursao_variations_quartos_numeros', true);
			if($brc_excursao_variations_quartos_numeros){
				//return $quarto_id;
				return $brc_excursao_variations_quartos_numeros[$quarto_id];
			}else{
				return '--';
			}
		}
	}
	if(!function_exists('get_dia_semana')){
		function get_dia_semana($dia='n'){
			$semana = array(
				0 => 'Domingo', 
				1 => 'Segunda-Feira', 
				2 => 'Terca-Feira', 
				3 => 'Quarta-Feira', 
				4 => 'Quinta-Feira', 
				5 => 'Sexta-Feira', 
				6 => 'Sabado'
			);
			
			if($dia!=='n')
				return $semana[$dia];
			
			return $semana;
		}
	}
	if(!function_exists('get_dates_per')) {
		function get_dates_per($semana_ida=1,$semana_volta=5,$per_ini=false,$per_final=false){
			if(!$per_ini)
				$per_ini = current_time('timestamp');
			
			if(!$per_final)
				$per_final = strtotime('today + 3 months');
			
			
			//-------
			$startDate 	= new DateTime(date('d-m-Y H:i', $per_ini));
			$endDate 	= new DateTime(date('d-m-Y H:i', $per_final));
			
			$semanas = array();
			$ida = array();
			$volta = array();
			$v_ida = true;
			$v_volta = false;
			
			while($startDate <= $endDate){
				
				// SEMANA IDA
				if($startDate->format('w') == $semana_ida and $v_ida){
					$ida[] = array(
						'_diaSemana' 		=> $startDate->format('w'),
						'_data' 			=> $startDate->format('d-m-Y'),
						'_dataTimestamp' 	=> $startDate->getTimestamp(),
					);
					$v_ida = false;
					$v_volta = true;
				}
				
				// SEMANA VOLTA
				if($startDate->format('w') == $semana_volta and $v_volta){
					$volta[] = array(
						'_diaSemana' 		=> $startDate->format('w'),
						'_data' 			=> $startDate->format('d-m-Y'),
						'_dataTimestamp' 	=> $startDate->getTimestamp(),
					);
					$v_ida = true;
					$v_volta = false;
				}
				$startDate->modify('+1 day');
			}
			if($ida){
				foreach($ida as $k => $j){
					if($j and $volta[$k]){
						$semanas[] = array(
							'ida' => $j,
							'volta' => $volta[$k],
						);
					}
				}
			}
			return $semanas;
		}
	}
	if(!function_exists('get_viagem_tipo')){
		function get_viagem_tipo($tip='n'){
			$tipos = array(
				0 => '--', 
				1 => 'Bate e Volta', 
				//2 => 'Grupo de viagem', 
				3 => 'Viagem Linha', 
			);
			
			if($tip!=='n')
				return $tipos[$tip];
			
			return $tipos;
		}
	}
	if(!function_exists('get_viagem_tipo_class')){
		function get_viagem_tipo_class($tip='n'){
			$tipos = array(
				0 => 'bv', 
				1 => 'bv', 
				2 => 'grupo', 
				3 => 'ab', 
			);
			
			if($tip!=='n')
				return $tipos[$tip];
			
			return $tipos;
		}
	}
	if(!function_exists('refresh_viagem_assentos')){
		function refresh_viagem_assentos($product_id){
			$tipo_cadastro = get_post_meta($product_id, 'brc_viagem_tipo_cadastro', true);
			
			if($tipo_cadastro == 3){
				$assentos_disponiveis_old = get_post_meta($product_id, 'brc_assentos_disponiveis', true);
				$brc_viagem_assentos = intval(get_post_meta($product_id, 'brc_viagem_assentos', true));
				
				if($assentos_disponiveis_old){
					foreach($assentos_disponiveis_old as $k => $j){
						$assentos_disponiveis[$k] = 1;
					}
				
				
					$v_status = get_all_woocommerce_status_id();
					unset($v_status[4]);
					unset($v_status[5]);
					unset($v_status[6]);
					$orders = get_orders_ids_by_product_id($product_id, $v_status);
					
					foreach($orders as $order_id){
						$order = new WC_Order($order_id);
						foreach($order->get_items() as $order_item_id => $order_item){
							if($order_item->get_product_id() == $product_id){
								
								$var_data = $order_item->get_data();
								$meta_data = $order_item->get_meta_data();
								$meta_data_array = order_item_meta_to_array($meta_data);
								
								if($meta_data_array['_assento']){
									$assentos_disponiveis[$meta_data_array['_assento']] = false;
									$brc_viagem_assentos--;
								}
							}
						}
					}
					
					update_post_meta($product_id, 'brc_assentos_disponiveis', $assentos_disponiveis);
					update_post_meta($product_id, '_stock', $brc_viagem_assentos);
				}
			}
		}
	}
	if(!function_exists('gerar_cupons')){
		function gerar_cupons($user_id, $order_id){
			$user = get_userdata($user_id);
			if($user){
				
				$email_user = $user->data->user_email;
				$cupom_args = array(
					'posts_per_page'   => 1,
					'orderby'          => 'title',
					'order'            => 'asc',
					'post_type'        => 'shop_coupon',
					'post_status'      => 'publish',
					'meta_query' 		=> array(
						'relation' 		=> 'AND',
						array(
							'key' 			=> 'customer_email',
							'compare' 		=> '=',
							'value' 		=> $email_user,
						),
						array(
							'key' 			=> 'usage_count',
							'compare' 		=> 'NOT EXISTS',
						),
					),
				);
				$coupons = get_posts($cupom_args);
				
				if($coupons){
					$coupon = $coupons[0];
					
					$cupon_order = get_post_meta($coupon->ID, 'cupon_order', true);
					if($cupon_order != $order_id){
						
						$old_coupon_amount = intval(get_post_meta($coupon->ID, 'coupon_amount', true));
						if($old_coupon_amount < 100){
							
							$_couponAmount 	= $old_coupon_amount + 10;
							$_couponType 	= "percent";
							$_couponName 	= $order_id."DESCONTO".$_couponAmount; 
							
							$_newCoupon = array(
								'post_title' 	=> $_couponName,
								'post_content' 	=> '',
								'post_status' 	=> 'publish',
								'post_author' 	=> 1,
								'post_type'     => 'shop_coupon',
							);
							$_newCouponID = wp_insert_post($_newCoupon);
							
							update_post_meta($_newCouponID, 'discount_type', 			$_couponType);
							update_post_meta($_newCouponID, 'coupon_amount', 			$_couponAmount);
							update_post_meta($_newCouponID, 'usage_limit_per_user', 	'1');
							update_post_meta($_newCouponID, 'usage_limit', 				'1');
							update_post_meta($_newCouponID, 'individual_use', 			'yes');
							update_post_meta($_newCouponID, 'customer_email', 			$email_user);
							update_post_meta($_newCouponID, 'cupon_order', 				$order_id);
							
							wp_delete_post($coupon->ID, true);
							wp_update_post($_newCouponID);
						}
					}
				}
				else{
					$_couponAmount 	= 10;
					$_couponName 	= $order_id."DESCONTO".$_couponAmount; 
					$_couponType 	= "percent";
					
					$_newCoupon = array(
						'post_title' 	=> $_couponName,
						'post_content' 	=> '',
						'post_status' 	=> 'publish',
						'post_author' 	=> 1,
						'post_type'     => 'shop_coupon',
					);
					$_newCouponID = wp_insert_post($_newCoupon);
					
					update_post_meta($_newCouponID, 'discount_type', 			$_couponType);
					update_post_meta($_newCouponID, 'coupon_amount', 			$_couponAmount);
					update_post_meta($_newCouponID, 'usage_limit_per_user', 	'1');
					update_post_meta($_newCouponID, 'usage_limit', 				'1');
					update_post_meta($_newCouponID, 'individual_use', 			'yes');
					update_post_meta($_newCouponID, 'customer_email', 			$email_user);
					update_post_meta($_newCouponID, 'cupon_order', 				$order_id);
					
					wp_update_post($_newCouponID);
				}
			}
		}
	}
	if(!function_exists('wh_getOrderbyCouponCode')){
		function wh_getOrderbyCouponCode($coupon_code){
			global $wpdb;
			$return_array = [];
			$total_discount = 0;

			$query = "SELECT
				p.ID AS order_id
				FROM
				{$wpdb->prefix}posts AS p
				INNER JOIN {$wpdb->prefix}woocommerce_order_items AS woi ON p.ID = woi.order_id
				WHERE
				p.post_type = 'shop_order' AND
				p.post_status IN ('" . implode("','", array_keys(wc_get_order_statuses())) . "') AND
				woi.order_item_type = 'coupon' AND
				woi.order_item_name = '" . $coupon_code . "';";

			$orders = $wpdb->get_results($query);

			if (!empty($orders)) {
				$dp = ( isset($filter['dp']) ? intval($filter['dp']) : 2 );
				//looping throught all the order_id
				foreach ($orders as $key => $order) {
					$order_id = $order->order_id;
					//getting order object
					$objOrder = wc_get_order($order_id);

					$return_array[$key]['order_id'] = $order_id;
					$return_array[$key]['total'] = wc_format_decimal($objOrder->get_total(), $dp);
					$return_array[$key]['total_discount'] = wc_format_decimal($objOrder->get_total_discount(), $dp);
					$total_discount += $return_array[$key]['total_discount'];
				}
		//        echo '<pre>';
		//        print_r($return_array);
			}
			$return_array['full_discount'] = $total_discount;
			return $return_array;
		}
	}
	if(!function_exists('brc_woocommerce_menu_search_php')){
		function brc_woocommerce_menu_search_php($needle, $haystack){
			foreach($haystack as $key => $value){
				$current_key = $key;
				if( 
					$needle === $value 
					OR ( 
						is_array( $value )
						&& brc_woocommerce_menu_search_php( $needle, $value ) !== false 
					)
				){
					return $current_key;
				}
			}
			return false;
		}
	}
	if(!function_exists('gen_daterangearray')){
		function gen_daterangearray($date){
			$max = 5;
			$cc = 1;
			$ini = -2;
			$date_array = array();
			$today = date('d-m-Y', time());
			$today = strtotime($today);
			$date = get_date_timestamp($date);
			$date = date('d-m-Y', $date);
			
			while($cc <= $max){
				$step = strtotime($date.' '.$ini.' days');
				if($step >= $today){
					$date_array[$step] = $step;
					$cc++;
				}
				$ini++;
			}
			return $date_array;
		}
	}
	if(!function_exists('get_uf')){
		function get_uf($sigla=false){
			$ufs = array(
				'ac' => array('nome'=>'Acre', 'sigla'=>'ac'),
				'al' => array('nome'=>'Alagoas', 'sigla'=>'al'),
				'ap' => array('nome'=>'Amapá', 'sigla'=>'ap'),
				'am' => array('nome'=>'Amazonas', 'sigla'=>'am'),
				'ba' => array('nome'=>'Bahia', 'sigla'=>'ba'),
				'ce' => array('nome'=>'Ceará', 'sigla'=>'ce'),
				'df' => array('nome'=>'Distrito Federal', 'sigla'=>'df'),
				'es' => array('nome'=>'Espírito Santo', 'sigla'=>'es'),
				'go' => array('nome'=>'Goiás', 'sigla'=>'go'),
				'ma' => array('nome'=>'Maranhão', 'sigla'=>'ma'),
				'mt' => array('nome'=>'Mato Grosso', 'sigla'=>'mt'),
				'ms' => array('nome'=>'Mato Grosso do Sul', 'sigla'=>'ms'),
				'mg' => array('nome'=>'Minas Gerais', 'sigla'=>'mg'),
				'pa' => array('nome'=>'Pará', 'sigla'=>'pa'),
				'pb' => array('nome'=>'Paraíba', 'sigla'=>'pb'),
				'pr' => array('nome'=>'Paraná', 'sigla'=>'pr'),
				'pe' => array('nome'=>'Pernambuco', 'sigla'=>'pe'),
				'pi' => array('nome'=>'Piauí', 'sigla'=>'pi'),
				'rj' => array('nome'=>'Rio de Janeiro', 'sigla'=>'rj'),
				'rn' => array('nome'=>'Rio Grande do Norte', 'sigla'=>'rn'),
				'rs' => array('nome'=>'Rio Grande do Sul', 'sigla'=>'rs'),
				'ro' => array('nome'=>'Rondônia', 'sigla'=>'ro'),
				'rr' => array('nome'=>'Roraima', 'sigla'=>'rr'),
				'sc' => array('nome'=>'Santa Catarina', 'sigla'=>'sc'),
				'sp' => array('nome'=>'São Paulo', 'sigla'=>'sp'),
				'se' => array('nome'=>'Sergipe', 'sigla'=>'se'),
				'to' => array('nome'=>'Tocantins', 'sigla'=>'to'),
			);
			
			if($sigla)
				return $ufs[$sigla];
			
			return $ufs;
		}
	}
	if(!function_exists('get_linhs_by_pontos')){
		function get_linhs_by_pontos(){
			$linhs_by_pontos = array();
			$brc_linhas_args = get_terms(array(
				'taxonomy' 		=> 'brc_linha_viagem',
				'hide_empty' 	=> false,
			));
			
			if(!is_wp_error($brc_linhas_args)){
				foreach($brc_linhas_args as $k => $term){
					$brc_ponto = get_term_meta($term->term_id, 'brc_ponto', true);
					if($brc_ponto){
						foreach($brc_ponto as $p_k => $p_j){
							$linhs_by_pontos[$p_j['brc_ponto']][$term->term_id] = array(
								'linha_id' => $term->term_id,
								'brc_ponto' => $p_j['brc_ponto'],
								'brc_ponto_time' => $p_j['brc_ponto_time'],
								'brc_ponto_valor' => $p_j['brc_ponto_valor'],
							);
						}
					}
				}
			}
			return $linhs_by_pontos;
		}
	}
	if(!function_exists('get_pontos_by_linha')){
		function get_pontos_by_linha($linha=false){
			$pontos_by_linha = array();
			$brc_linhas_args = get_terms(array(
				'taxonomy' 		=> 'brc_linha_viagem',
				'hide_empty' 	=> false,
			));
			if(!is_wp_error($brc_linhas_args)){
				foreach($brc_linhas_args as $k => $term){
					$brc_ponto = get_term_meta($term->term_id, 'brc_ponto', true);
					
					if($brc_ponto){
						
						$index=1;
						foreach($brc_ponto as $p_k => $p_j){
							$pontos_by_linha[$term->term_id][$p_j['brc_ponto']] = array(
								'linha_id' => $term->term_id, 
								'index' => $index,
								'brc_ponto' => $p_j['brc_ponto'],
								'brc_ponto_time' => $p_j['brc_ponto_time'],
								'brc_ponto_valor' => $p_j['brc_ponto_valor'],
							);
							$index++;
						}
					}
				}
			}
			
			if($linha)
				return $pontos_by_linha[$linha];
			
			
			return $linhs_by_pontos;
		}
	}
	if(!function_exists('get_pontos_full_name')){
		function get_pontos_full_name($ponto_id=false){
			$full = false;
			$ufs = get_uf();
			
			if($ponto_id){
				$ponto = get_term($ponto_id, 'brc_ponto_embarque');
				if(!is_wp_error($ponto)){
					$ponto_cidade_id = get_term_meta($ponto->term_id, 'brc_cidade', true);
					$ponto_cidade = get_term($ponto_cidade_id);
					if(!is_wp_error($ponto_cidade)){
						$ponto_cidade_uf = get_term_meta($ponto_cidade->term_id, 'brc_uf', true);
						$full = $ponto->name.' <small>('.$ponto_cidade->name.'/'.strtoupper($ufs[$ponto_cidade_uf]['sigla']).')</small>';
					}
				}
			}
			
			return $full;
		}
	}