<?php
	$excursoes = explode(',', $excursoes);
	$posts = new WP_Query(array('post_type' => 'post'));
	if($posts->have_posts()){
		$posts_thumbs = new WP_Query(array('post_type' => 'post'));
		
	?>
		<div class="brc_vc_wrapper fepa_blog_carrossel <?php echo esc_attr($css_class); ?>">
			<div class="wrapper-inner">
				<div class="thumbs-wrapper">
					<div class="thumbs-wrapper-inner">
					<?php
						$tt=1;
						while($posts_thumbs->have_posts()){
							$posts_thumbs->the_post();
							$thumb = get_the_post_thumbnail_url(get_the_ID(), 'full');
						?>
							<div data-index="<?php echo $tt+1; ?>" class="thumb-item" style="background-image:url(<?php echo $thumb; ?>);">
								<!-- nothing to see here -->
							</div>
						<?php
							$tt++;
						}
						
						wp_reset_query();
					?>
					</div>
				</div>
				<div class="content-wrapper">
					<div class="content-flex">
						<div class="content-wrapper-inner">
						<?php
							$cc=1;
							while($posts->have_posts()){
								$posts->the_post();
							?>
								<div data-index="<?php echo $cc+1; ?>" class="content-item">
									<a href="<?php the_permalink(); ?>" class="item-inner">
										<h3><?php the_title(); ?></h3>
										<div class="excerpt"><?php the_excerpt(); ?></div>
										
										<span class="link">Leia mais.</a>
									</a>
								</div>
							<?php
								$cc++;
							}
							
							wp_reset_query();
						?>
						</div>
						<div class="content-nav-wrapper">
							<a href="#" class="nav-prev">
								<i class="fas fa-chevron-left"></i>
							</a>
							
							<a href="#" class="nav-next">
								<i class="fas fa-chevron-right"></i>
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php
	}
?>