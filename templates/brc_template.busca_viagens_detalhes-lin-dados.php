<div class="lin-dados">
	<input type="hidden" name="passageiro-item[]" value="yes" />
	
	<div class="col-item col-nome">
		<input type="text" name="passageiro-nome[]" placeholder="" class="input-field" />
		<span class="label">Nome do Passageiro</span> 
	</div>
	<div class="col-item col-cpf">
		<input type="text" name="passageiro-cpf[]" placeholder="" class="input-field i-cpf" />
		<span class="label">CPF</span>
	</div>
	<div class="col-item col-cpf">
		<input type="text" name="passageiro-rg[]" placeholder="" class="input-field i-rg" />
		<span class="label">RG</span>
	</div>
	<?php if($tipo_cadastro == 3): ?>
		<input type="hidden" name="passageiro-assento[]" value="0" />
		<div class="col-item item-lin-valor col-valor text-center">R$ 100,00</div>
		<div class="col-item item-lin-assento col-assento text-center">
			<span>5</span>
		</div>
	<?php else: ?>
		<div class="col-item col-remove">
			<a href="#" title="ADICIONAR PASSAGEIRO" class="vp_vc_btn-link action-add-passageiro" data-viagemid="<?php echo $post_ID; ?>"><i class="fa fa-plus"></i>adicionar passageiro</a>
			<a href="#" title="REMOVE PASSAGEIRO" class="vp_vc_btn-link action-remove-passageiro"><i class="fa fa-times"></i>remover</a>
		</div>
	<?php endif; ?>
</div>
