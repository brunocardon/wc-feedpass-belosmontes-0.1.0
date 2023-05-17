
	<?php $vd = $var_id; ?>
	<?php $ld = $lins_id; ?>
	<?php $ia = '['.$vd.']['.$ld.'][]'; ?>
	<div class="lin-dados lin-crianca">
		<input type="hidden" name="variacao-item-child<?php echo $ia; ?>" value="1" />
		
		<div class="col-item col-crianca-nome">
			<div class="col-inner form-group">
				<span class="label">Nome</span> 
				<div class="inp"><input type="text" name="crianca-nome<?php echo $ia; ?>" class="input-field" /></div>
			</div>
		</div>

		<div class="col-item col-crianca-idade">
			<div class="col-inner form-group">
				<span class="label">Idade</span> 
				<div class="inp">
					<select name="crianca-idade<?php echo $ia; ?>" class="input-field crianca-idade">
						<option value="no">--</option>
						<?php if($child_rule): foreach($child_rule as $k => $j): ?>
							<option value="<?php echo $k; ?>"><?php echo $j['rule_nome']; ?></option>
						<?php endforeach; endif; ?>
					</select>
				</div>
			</div>
		</div>
		<div class="col-item col-crianca-preco">
			<div class="col-inner">
				<span class="label">
					<span class="price_html">--</span>
					<input type="hidden" name="crianca-price<?php echo $ia; ?>" class="i-price crianca-price" value="0" />
				</span>
			</div>
		</div>
		<div class="col-item col-crianca-action">
			<div class="col-inner">
				<a href="#" title="REMOVER CRIANÇA" class="vp_vc_btn-link c-action action-remove-crianca">
					<i class="fa fa-times"></i>
					remover
				</a>
			</div>
		</div>
		
		<div class="col-item col-descricao" style="display:none;">
			<div class="col-inner">
				<em class="descricao"><i class="fad fa-info-circle"></i> <span class="rule_detalhes"></span></em>
			</div>
		</div>
	</div>