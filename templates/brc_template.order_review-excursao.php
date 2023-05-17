<?php //pre_debug($cart_item); ?>
<div class="ex-review-item">
	<div class="inner">
		<div class="col-item col-thumb">
			<div class="col-inner">
				<?php
					$thumb = get_the_post_thumbnail_url($product_id, 'thumbnail');
					if($thumb){
						echo '<img src="'. $thumb .'" alt="'. $cart_item['_product_nome'] .'" />';
					}
				?>
			</div>
		</div>
		<div class="col-item col-title">
			<div class="col-inner">
				<small>EXCURSÃO</small>
				<a href="<?php echo get_permalink($cart_item['_product_id']); ?>" title="<?php echo $cart_item['_product_nome']; ?>"><h4><?php echo $cart_item['_product_nome']; ?></h4></a>
				<h5><?php echo get_the_title($cart_item['_variation_hotel_id']); ?></h5>
			</div>
		</div>
		<div class="col-item col-dados">
			<div class="col-inner">
				<div class="item">
					<i class="fad fa-bed"></i> 
					<strong>Pacote:</strong> 
					<?php echo $cart_item['_variation_pacote']; ?> 
				</div>
				
				<div class="item">
					<i class="fad fa-clock"></i>
					<strong>Data/hora ida:</strong> 
					<?php echo date('d/m/Y H:s', $cart_item['_product_data']); ?>
				</div>
				<div class="item">
					<i class="fad fa-clock"></i>
					<strong>Data/hora volta:</strong> 
					<?php echo date('d/m/Y H:s', $cart_item['_product_data_volta']); ?>
				</div>
				
				<?php if($cart_item['_product_noites']): ?>
				<div class="item">
					<i class="fad fa-house-night"></i>
					<strong>Noites:</strong> 
					<?php echo $cart_item['_product_noites']; ?>
				</div>
				<?php endif; ?>
				
				<?php if($cart_item['_hospedes']): ?>
				<div class="item">
					<i class="fad fa-male"></i> 
					<strong>Hóspode<?php echo count($cart_item['_hospedes'])>1?'s':''; ?></strong>
					<ul>
						<?php foreach($cart_item['_hospedes'] as $hospede): ?>
						<li>
							<?php echo $hospede['nome'].' ('.$hospede['cpf'].')'; ?>
							<?php if($hospede['ingresso']): ?>
								<span class="especial">+ingresso <?php echo wc_price($hospede['ingresso_price']); ?></span>
							<?php endif; ?>
						</li>
						<?php endforeach; ?>
					</ul>
				</div>
				<?php endif; ?>
				
				<?php if($cart_item['_crianca']): ?>
				<div class="item">
					<i class="fad fa-child"></i>
					<strong>Criança<?php echo count($cart_item['_criancas'])>1?'s':''; ?></strong>
					<ul>
						<?php foreach($cart_item['_criancas'] as $crianca): ?>
						<li>
							<?php echo $crianca['nome'].' ('.$crianca['idade'].')'; ?>
							<?php if($crianca['price']): ?>
								<span class="especial">+ <?php echo wc_price($crianca['price']); ?></span>
							<?php endif; ?>
						</li>
						<?php endforeach; ?>
					</ul>
				</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>