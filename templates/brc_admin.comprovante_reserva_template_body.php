<div class="brc-comprovante-reserva-print">
	<div class="brc-comprovante-reserva-a4">
		<div class="brc-comprovante-reserva-header">
		<?php
			$img = get_option('woocommerce_email_header_image');
			if($img){
				echo '<img src="'. esc_url($img) .'" alt="'. get_bloginfo('name', 'display') .'" />';
			}else{
				echo '<h1>'. get_bloginfo('name', 'display') .'</h1>';
			}
			?>
			<div class="titulo">COMPROVANTE DE RESERVA #<?php echo $orderID; ?></div>
		</div>
		<div class="brc-comprovante-reserva-body">
			<h2>[Pedido #<?php echo $orderID; ?>] (<?php echo $emissao; ?>)</h2>
			
			<table class="order-details">
				<thead>
					<tr>
						<th class="produto">Viagens</th>
						<th> X </th>
						<th class="valores">Valores</th>
					</tr>
				</thead>
				<tbody>
					<?php echo wc_get_email_order_items($order,array('show_image'=> false)); ?>
				</tbody>
				<tfoot>
				<?php
					$item_totals = $order->get_order_item_totals();

					if($item_totals){
						$i = 0;
						foreach($item_totals as $total){
						?>
							<tr class="<?php echo $i<1?'first':''; ?>">
								<th colspan="2"><?php echo wp_kses_post( $total['label'] ); ?></th>
								<td><?php echo wp_kses_post( $total['value'] ); ?></td>
							</tr>
						<?php
							$i++;
						}
					}
					if($order->get_customer_note()){
						?>
						<tr>
							<th colspan="2"><?php esc_html_e( 'Note:', 'woocommerce' ); ?></th>
							<td><?php echo wp_kses_post( nl2br( wptexturize( $order->get_customer_note() ) ) ); ?></td>
						</tr>
						<?php
					}
				?>
				</tfoot>
			</table>
			
			<div class="order-dados">
				<div class="order-qrcode">
				<?php
					$order_id = $order->id;
					$fepa_passagemticket = get_permalink(get_option('fepa_passagemticket'));
					$passagemticket_uri = $fepa_passagemticket.'?p='.base64_encode($order_id);
					$qrcomprovante = FEPA_PLUGIN_URL . '/includes/plugins/phpqrcode/qrcomprovante.php?p='. $passagemticket_uri;
					?>
					<img src="<?php echo $qrcomprovante; ?>" alt="<?php echo $passagemticket_uri; ?>" />
					<p>Imprima ou apresente este e-mail com o 'QRCODE' na hora do embarque para melhor detalhamento do seu pedido.</p>
				</div>
				
				<div class="order-cliente">
					<h3>Dados do Cliente</h3>
					<address>
					<?php
						$billing_first_name 	= get_user_meta($order_user_id, 'billing_first_name', true);
						$billing_email 			= get_user_meta($order_user_id, 'billing_email', true);
						$billing_phone 			= get_user_meta($order_user_id, 'billing_phone', true);
						$billing_cpf 			= get_user_meta($order_user_id, 'billing_cpf', true);
						
						echo '<strong>Nome: </strong>'. $billing_first_name .'<br/>';
						echo '<strong>E-mail: </strong>'. $billing_email .'<br/>';
						echo '<strong>Telefone: </strong>'. $billing_phone .'<br/>';
						echo '<strong>CPF: </strong>'. $billing_cpf .'<br/>';
					?>
					</address>						
				</div>
			</div>
		</div>
	</div>
</div>
