<?php
	global $brcpasstour_cupons;
	
	$user_id = get_current_user_id();
	$user = get_userdata($user_id);
	$email_user = $user->data->user_email;
	
	// CUPOM VIGENTE
	$cupom_args = array(
		'posts_per_page'   => -1,
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
	$coupon = $coupons[0];
	$discount_type = get_post_meta($coupon->ID, 'discount_type', true);
	$coupon_amount = get_post_meta($coupon->ID, 'coupon_amount', true);
	
	if($discount_type=='percent'){
		$coupon_amount = $coupon_amount.'%';
	}else{
		$coupon_amount = get_woocommerce_currency_symbol().$coupon_amount;
	}
?>
	<div class="brc_myaccount_cupons">
		<h2>Meus Cupons</h2>
	<?php 
		// CRIA A MENSAGEM PREVIAMENTE CONFIGURADA
		echo $brcpasstour_cupons->gen_descricao_mensagem(array(
			'QUANT' => '01',
			'PERCENT' => $coupon_amount,
			'COD' => $coupon->post_title,
		)); 
	
	
		// CUPONS USADOS
		$cupom_usados_args = array(
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
					'compare' 		=> 'EXISTS',
				),
			),
		);
		$cupom_usados = get_posts($cupom_usados_args);
		if($cupom_usados){
		?>
			<div class="used-cupons">
				<h3>Cupons já utlizados</h3>
				
				<table class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive">
					<thead>
						<tr>
							<th class="woocommerce-orders-table__header myaccount_cupons_header_codigo"><span class="nobr">Código</span></th>
							<th class="woocommerce-orders-table__header myaccount_cupons_header_pedido"><span class="nobr">Pedido</span></th>
							<th class="woocommerce-orders-table__header myaccount_cupons_header_desconto"><span class="nobr">Desconto (%)</span></th>
							<th class="woocommerce-orders-table__header myaccount_cupons_header_desconto_total"><span class="nobr">Desconto total</span></th>
							<th class="woocommerce-orders-table__header myaccount_cupons_header_data"><span class="nobr">Data</span></th>
						</tr>
					</thead>
					<tbody>
					<?php 
						foreach($cupom_usados as $coupon){ 
							
							$discount_type = get_post_meta($coupon->ID, 'discount_type', true);
							$coupon_amount = get_post_meta($coupon->ID, 'coupon_amount', true);
							
							if($discount_type=='percent'){
								$coupon_amount = $coupon_amount.'%';
							}else{
								$coupon_amount = get_woocommerce_currency_symbol().$coupon_amount;
							}
							
							$order = wh_getOrderbyCouponCode($coupon->post_title);
							$order = $order[0];
							$order_id = $order['order_id'];
							$order_item = new WC_Order($order_id);
						?>
							<tr class="woocommerce-orders-table__row order">
								<td class="woocommerce-orders-table__cell myaccount_cupons_header_codigo">
									<?php echo $coupon->post_title; ?>
								</td>
								<td class="woocommerce-orders-table__cell myaccount_cupons_header_pedido">
									<a href="<?php echo esc_url( $order_item->get_view_order_url() ); ?>" title="Pedido #<?php echo $order_id; ?>">
										#<?php echo $order_id; ?>
									</a>
								</td>
								<td class="woocommerce-orders-table__cell myaccount_cupons_header_desconto">
									<?php echo $coupon_amount; ?>
								</td>
								<td class="woocommerce-orders-table__cell myaccount_cupons_header_desconto_total">
									<?php echo wc_price($order['total_discount']); ?>
								</td>
								<td class="woocommerce-orders-table__cell myaccount_cupons_header_data">
									<?php echo $order_item->get_date_created()->date("d/m/Y");; ?>
								</td>
							</tr>
					<?php } ?>
					</tbody>
				</table>
			</div>
		<?php
		}
	?>
	</div>
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
