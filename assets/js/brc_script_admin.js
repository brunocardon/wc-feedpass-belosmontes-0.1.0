var $ = jQuery;
var flo = $('.admin_loading')
$(document).ready(function(){
	$.fn.select2.defaults.set('language', {
		errorLoading: function () {
			return 'Os resultados não puderam ser carregados.';
		},
		inputTooLong: function (args) {
			var overChars = args.input.length - args.maximum;

			var message = 'Apague ' + overChars + ' caracter';

			if (overChars != 1) {
				message += 'es';
			}
			return message;
		},
		inputTooShort: function (args) {
			var remainingChars = args.minimum - args.input.length;

			var message = 'Digite ' + remainingChars + ' ou mais caracteres';

			return message;
		},
		loadingMore: function () {
			return 'Carregando mais resultados…';
		},
		maximumSelected: function (args) {
			var message = 'Você só pode selecionar ' + args.maximum + ' ite';

			if (args.maximum == 1) {
			message += 'm';
			} else {
			message += 'ns';
			}

			return message;
		},
		noResults: function () {
			return 'Nenhum resultado encontrado';
		},
		searching: function () {
			return 'Buscando…';
		},
		removeAllItems: function () {
			return 'Remover todos os itens';
		}
	})
	$.mask.definitions['A'] = "[A-Za-z]";
	$('.cpf, .billing_cpf, #billing_cpf').mask('999.999.999-99');
	$('._rg').mask('99.999.999');
	$('.tel, .billing_cellphone, #billing_cellphone, #billing_phone').mask('(99) 99999-999?9');
	$('.data').mask('99/99/9999');
	$('.uf').mask('AA');
	$('.cep, .billing_postcode, #billing_postcode').mask('99999-999');
	
	$.fn.highlight = function(){
		this.animate({
			backgroundColor: "#fffdc0",
		}, 250)
		.delay(500)
		.animate({
			backgroundColor: "transparent",
		}, 250);
	}
	$.fn.input_highlight = function(){
		this.animate({
			backgroundColor: "#fffdc0",
			borderColor: "#ff0000",
		}, 250)
		.delay(500)
		.animate({
			backgroundColor: "#ffffff",
			borderColor: "#7e8993",
		}, 250);
	}
	
	// ADD VARIÁVEL PARA QUERY DE ADICIONAR PRODUTO (metatype=excursoes)
	if(document.getElementById('metatype')){
		
		var addNew = $('#wpbody-content').find('.page-title-action')[0];
		var metatype = $('#metatype').data('mvalue')
		
		$(addNew).attr('href', $(addNew).attr('href')+'&metatype='+metatype+'')
		
		if(metatype == 'excursoes'){
			$('#menu-posts-product').removeClass('wp-has-current-submenu');
			$('#menu-posts-product').removeClass('wp-menu-open');
			$('#menu-posts-product > a').removeClass('wp-has-current-submenu');
			$('#menu-posts-product').addClass('wp-not-current-submenu');
			
			$('#toplevel_page_edit-post_type-product-metatype-excursoes').removeClass('wp-not-current-submenu');
			$('#toplevel_page_edit-post_type-product-metatype-excursoes').addClass('wp-has-current-submenu');
			$('#toplevel_page_edit-post_type-product-metatype-excursoes').addClass('wp-menu-open');
			$('#toplevel_page_edit-post_type-product-metatype-excursoes > a').addClass('wp-has-current-submenu');
		}
	}
	
	// FORM EVENTOS excursao
	if(document.getElementById('brc_add_product_excursao_principal')){
		
		// ADICIONAR VARIATIONS
		$(document).on('click', '.brc_add_product_excursao_add_variation', function(e){
			e.preventDefault();
			
			var that = $(this);
			
			flo.fadeIn(250)
			jQuery.ajax({
				type		: "post",
				url			: ajax_var.url,
				data		: {
					'action' 	: 'brc_add_product_excursao_add_variation',
				},
				success		: function(data){
					data = JSON.parse(data);
					if(data.sts){
						
						that.parents('#brc_add_product_excursao_variations').find('select.select2').select2('destroy');
						that.parents('#brc_add_product_excursao_variations').find('.variations-ajax').append(data.html)
						that.parents('#brc_add_product_excursao_variations').find('select.select2').select2();
						
						flo.fadeOut(250)
					}else{
						alert('Um erro inesperado ocorreu.');
						flo.fadeOut(250)
					}
				}
			})
		})
		$(document).on('click', '.brc_add_product_excursao_remove_variation', function(e){
			e.preventDefault();
			if(confirm('Realmente deseja remover este item?')){
				$(this).parents('.variations-item.ajax').remove();
				
				if($(this).data('varid')){
					
					flo.fadeIn(250)
					jQuery.ajax({
						type		: "post",
						url			: ajax_var.url,
						data		: {
							'action' 	: 'brc_add_product_excursao_remove_variation',
							'varid' 	: $(this).data('varid'),
						},
						success		: function(data){
							data = JSON.parse(data);
							if(data.sts){
								flo.fadeOut(250)
							}else{
								alert('Um erro inesperado ocorreu.');
								flo.fadeOut(250)
							}
						}
					})
				}
			}
		})
		$(document).on('change', '#brc_excursao_ingresso', function(e){
			if($(this).prop("checked")){
				$('._ingresso').show();
				$('._ingresso').highlight();
			}else{
				$('._ingresso').hide();
			}
		})
		
		$(document).on('change', '#brc_excursao_has_child_rule', function(e){
			if($(this).prop("checked")){
				$('._child').show();
				$('._child').highlight();
			}else{
				$('._child').hide();
			}
		})
		$(document).on('click', '.brc_add_product_excursao_add_child', function(e){
			e.preventDefault();
			
			var that = $(this);
			
			flo.fadeIn(250)
			jQuery.ajax({
				type		: "post",
				url			: ajax_var.url,
				data		: {
					'action' 	: 'brc_add_product_excursao_add_child',
				},
				success		: function(data){
					data = JSON.parse(data);
					if(data.sts){
						that.parents('#brc_add_product_excursao_child').find('.child-ajax').append(data.html)
						flo.fadeOut(250)
					}else{
						alert('Um erro inesperado ocorreu.');
						flo.fadeOut(250)
					}
				}
			})
		})
		$(document).on('click', '.brc_add_product_excursao_remove_child', function(e){
			e.preventDefault();
			if(confirm('Realmente deseja remover este item?')){
				$(this).parents('.child-item.ajax').remove();
			}
		})
	}
	if(document.getElementById('poststuff')){
		$('#poststuff input#title[name="post_title"]').prop('required', 'required');
	}
	
	// VIAGENS UNIQUE / MASS
	if(document.getElementById('brc_add_product_viagem_principal')){
		$(document).on('change', '#brc_viagem_masscad', function(e){ // brc_viagem_masscad_wrapper
			if($(this).prop("checked")){
				$('#brc_viagem_masscad_wrapper').show();
				$('#brc_viagem_unique_wrapper').hide();
			}else{
				$('#brc_viagem_unique_wrapper').show();
				$('#brc_viagem_masscad_wrapper').hide();
			}
		})
		$(document).on('change', '#brc_viagem_grupo_masscad', function(e){ // brc_viagem_grupo_masscad_wrapper brc_viagem_grupo_unique_wrapper
			if($(this).prop("checked")){
				$('#brc_viagem_grupo_masscad_wrapper').show();
				$('#brc_viagem_grupo_unique_wrapper').hide();
			}else{
				$('#brc_viagem_grupo_unique_wrapper').show();
				$('#brc_viagem_grupo_masscad_wrapper').hide();
			}
		})
		$(document).on('change', '#brc_viagem_ab_masscad', function(e){ // brc_viagem_ab_masscad_wrapper brc_viagem_ab_unique_wrapper
			if($(this).prop("checked")){
				$('#brc_viagem_ab_masscad_wrapper').show();
				$('#brc_viagem_ab_unique_wrapper').hide();
			}else{
				$('#brc_viagem_ab_unique_wrapper').show();
				$('#brc_viagem_ab_masscad_wrapper').hide();
			}
		})
	}
	
	// ESCOLHER TIPO DE CADASTRO DE VIAGEM
	if(document.getElementById('brc_product_tipo')){
		$(document).on('change', '#brc_viagem_tipo_cadastro', function(e){
			
			$('#brc_product_edit, #brc_product_grupo, #brc_product_ab').hide();
			$('.produto-tipo-descricao').hide();
			$('.reqr').attr('required', false)
			
			switch($(this).val()){
				case '1': // brc_product_edit
					$('#brc_product_edit').show();
					$('#produto-tipo-descricao-bate-volta').show();
					$('#brc_product_edit .reqr').attr('required', true)
				break;
				case '2': // brc_product_grupo
					$('#brc_product_grupo').show();
					$('#produto-tipo-descricao-grupo').show();
					$('#brc_product_grupo .reqr').attr('required', true)
				break;
				case '3': // brc_product_ab
					$('#brc_product_ab').show();
					$('#produto-tipo-descricao-linha').show();
					$('#brc_product_ab .reqr').attr('required', true)
				break;
			}
		})
	}
	
	// DISTRIBUIR QUARTOS
	if(document.getElementById('form-viagem-passageiros')){
		$(document).on('click', '#action-distribuir-quartos', function(e){
			e.preventDefault();
			
			var that = $(this),
				produto_id = that.data('produto_id'),
				action = 'brc_tb_distribuir_quartos',
				tb_title = 'Distribuir quartos #'+produto_id,
				query = 'width=1000&height=900&action='+action+'&product_id='+produto_id;
			
			
			tb_show(tb_title, 'admin-ajax.php?'+query);
			var tb = document.getElementById('TB_ajaxContent');
			tb.setAttribute('style', '');
			$('#TB_overlay').addClass('brc_tb_distribuir_quartos_overlay');
			$('#TB_window').addClass('brc_tb_distribuir_quartos');
		})
		$(document).on('click', '#tb-distribuir-quartos-action-save', function(e){
			e.preventDefault();
			
			var serial = $("#tb-distribuir-quartos-form").serializeArray();
			var that = $(this);
			
			console.log(serial)
	
			flo.fadeIn(250)
			jQuery.ajax({
				type		: "post",
				url			: ajax_var.url,
				data		: serial, // brc_distribuir_quartos_save
				success		: function(data){
					data = JSON.parse(data);
					if(data.sts){
						alert('Ordem salva com sucesso!');
						flo.fadeOut(250)
					}else{
						alert('Um erro inesperado ocorreu.');
						flo.fadeOut(250)
					}
				}
			})
			
		})
		
		$(document).on('click', '#action-distribuir-assentos', function(e){
			e.preventDefault();
			
			var that = $(this),
				produto_id = that.data('produto_id'),
				action = 'brc_tb_distribuir_assentos',
				tb_title = 'Distribuir assentos #'+produto_id,
				query = 'width=1000&height=900&action='+action+'&product_id='+produto_id;
			
			
			tb_show(tb_title, 'admin-ajax.php?'+query);
			var tb = document.getElementById('TB_ajaxContent');
			tb.setAttribute('style', '');
			$('#TB_overlay').addClass('brc_tb_distribuir_quartos_overlay');
			$('#TB_window').addClass('brc_tb_distribuir_quartos');
		})
		$(document).on('click', '#tb-distribuir-assentos-action-save', function(e){
			e.preventDefault();
			
			var serial = $("#tb-distribuir-assentos-form").serializeArray();
			var that = $(this);
			
			console.log(serial)
			
			flo.fadeIn(250)
			jQuery.ajax({
				type		: "post",
				url			: ajax_var.url,
				data		: serial, // brc_distribuir_assentos_save
				success		: function(data){
					data = JSON.parse(data);
					if(data.sts){
						alert('Ordem salva com sucesso!');
						flo.fadeOut(250)
					}else{
						alert('Um erro inesperado ocorreu.');
						flo.fadeOut(250)
					}
				}
			})
			
		})
	}
	
	// NOTIFICATIONS
	if(document.getElementById('brc_notification_admin_settings_title-description')){
		$('.brcpasstour_notification_tags').on('click', function(e){
			e.preventDefault()
			
			var that = $(this);
			var order_id = that.data('orderid');
			var tb_title = 'Lista de tag\'s dinâmicas';
			
			tb_show(tb_title, 'admin-ajax.php?action=brcpasstour_notification_tags');
		})
	}
	if(document.getElementById('action-sms')){
		$(document).on('click', '#action-sms', function(e){
			e.preventDefault();
		
			var that 		= $(this);
			var vform 		= that.parents('#post');
			var product_id 	= vform.find('input[name^=post_ID]');
			var tb_title 	= 'Enviar mensagem via SMS #'+product_id.val();
			var formdata 	= vform.find('input[name^=passageiro]').serialize();
			var query 		= 'width=753&height=650&action=brcpasstour_notification_sms&product_id='+product_id.val()+'&'+formdata
			
			console.log(query);
			tb_show(tb_title, 'admin-ajax.php?'+query);
		})
		$(document).on('submit', '#form-passageiros-sms', function(e){
			e.preventDefault();
			flo.fadeIn(250)
			
			var that = $(this),
				formdata = that.serialize()
			
			jQuery.ajax({
				type		: "post",
				url			: ajax_var.url,
				data		: formdata,
				success		: function(data){
					data = JSON.parse(data);
					if(data.sts){
						flo.fadeOut(250)
						alert(data.label)
					}else{
						flo.fadeOut(250)
						alert(data.label)
					}
				}
			})
		})
		$(document).on('click', '#form-passageiros-sms .tag-insert', function(e){
			e.preventDefault()
			
			var that 			= $(this),
				pform 			= that.parents('#form-passageiros-sms'),
				pmensagem 		= pform.find('#notification_sms_mensagem'),
				pmensagem_txt 	= pmensagem.val(),
				tag 			= that.data('tag')
			
			pmensagem.val(pmensagem_txt+' '+tag)
			pform.find('.mensagem_count').html(pmensagem.val().length)
		})
	}
	$(document).on('keyup', '#notification_sms_mensagem', function(e){
		$(this).parents('form').find('.mensagem_count').html($(this).val().length)
	})
	
	// ORDER LIST
	if(document.getElementById('posts-filter')){
		$(document).on('click', '.action-cancelar', function(e){
			e.preventDefault();
			
			var that = $(this);
			
			if(confirm('Deseja realmente cancelar esse pedido? Essa ação não é reversível.')){
				
				flo.fadeIn(250)
				jQuery.ajax({
					type		: "post",
					url			: ajax_var.url,
					data		: {
						'action' : 'brc_order_list_cancelar',
						'orderid' : that.data('orderid'),
					},
					success		: function(data){
						data = JSON.parse(data);
						if(data.sts){
							alert('Pedido #'+ data.orderid +' cancelado com sucesso!')
							location.reload()
						}else{
							flo.fadeOut(150)
							alert('Ocorreu um erro inesperado, por favor tente novamente.')
						}
					}
				})
			}
		})
	}
	
	// ORDER EDIT
	if(document.getElementById('brc_add_order_principal')){
		$(document).on('change', 'input[name="brc_order_excursao"]', function(e){
			var that = $(this);
			if(that.val() == 'excursao'){
				$('#brc_order_add_viagem.postbox').hide();
				$('#brc_order_add_excursao.postbox').slideDown(250);
			}else{
				$('#brc_order_add_excursao.postbox').hide();
				$('#brc_order_add_viagem.postbox').slideDown(250);
			}
		})
		if(document.getElementById('brc_order_add_viagem')){
			var title_class = $('#brc_order_add_viagem_title').data('class')
			$('#brc_order_add_viagem').addClass(title_class)
		}
		$(document).on('change', '#brc_order_produto_excursao', function(e){
			var that = $(this);
			
			flo.fadeIn(250)
			jQuery.ajax({
				type		: "post",
				url			: ajax_var.url,
				data		: {
					'action' : 'brc_order_produto_excursao',
					'product_id' : that.val(),
				},
				success		: function(data){
					data = JSON.parse(data);
					flo.fadeOut(250)
					
					that.parents('#brc_add_order_excursoes').find('#brc_add_order_excursoes_vars').html('')
					
					if(data.sts){
						that.parents('#brc_add_order_excursoes').find('#brc_add_order_excursoes_vars').html(data.html)
					}else{
						alert('Por favor selecione um produto.');
					}
				}
			})
		})
		$(document).on('click', '.brc_order_produto_add_hospede', function(e){
			e.preventDefault();
			var that = $(this),
				product_id = that.data('product_id'),
				var_id = that.data('var_id'),
				aaaaa;
			
			flo.fadeIn(250)
			jQuery.ajax({
				type		: "post",
				url			: ajax_var.url,
				data		: {
					'action' : 'brc_order_produto_add_hospede',
					'product_id' : product_id,
					'var_id' : var_id,
				},
				success		: function(data){
					data = JSON.parse(data);
					flo.fadeOut(250)
					
					if(data.sts){
						that.parents('.brc_form_grid_inner_table').find('.brc_form_grid_inner_table_body').append(data.html)
						that.parents('.brc_form_grid_inner_table').find('.cpf').unmask();
						that.parents('.brc_form_grid_inner_table').find('.cpf').mask('999.999.999-99');
					}else{
						alert('Um erro inesperado ocorreu.');
					}
				}
			})
		})
		$(document).on('click', '.brc_order_produto_remove_hospede', function(e){
			e.preventDefault();
			var that = $(this);
			if(confirm('Realmente deseja remover este item?')){
				that.parents('.brc_order_produto_item_hospede').remove()
			}
		})
		$(document).on('change', 'input[name="_ingresso_verf[]"]', function(e){
			var that = $(this);
			
			if(that.prop('checked')){
				that.parents('.brc_order_produto_item_hospede').find('input[name="_ingresso[]"]').val('yes')
			}else{
				that.parents('.brc_order_produto_item_hospede').find('input[name="_ingresso[]"]').val('no')
			}
		})
		
		$(document).on('change', '#brc_order_produto_viagem', function(e){
			var that = $(this);
			
			flo.fadeIn(250)
			jQuery.ajax({
				type		: "post",
				url			: ajax_var.url,
				data		: {
					'action' : 'brc_order_produto_viagem',
					'product_id' : that.val(),
				},
				success		: function(data){
					data = JSON.parse(data);
					flo.fadeOut(250)
					
					that.parents('#brc_add_order_viagens').find('#brc_add_order_viagens_vars').html()
					
					if(data.sts){
						that.parents('#brc_add_order_viagens').find('#brc_add_order_viagens_vars').html(data.html)
					}else{
						alert('Um erro inesperado ocorreu.');
					}
				}
			})
		})
		$(document).on('click', '.brc_order_produto_add_passageiros', function(e){
			e.preventDefault();
			var that = $(this),
				product_id = that.data('product_id');
			
			flo.fadeIn(250)
			jQuery.ajax({
				type		: "post",
				url			: ajax_var.url,
				data		: {
					'action' : 'brc_order_produto_add_passageiros',
					'product_id' : product_id,
				},
				success		: function(data){
					data = JSON.parse(data);
					flo.fadeOut(250)
					
					if(data.sts){
						that.parents('.brc_form_grid_inner_table').find('.brc_form_grid_inner_table_body').append(data.html)
						that.parents('.brc_form_grid_inner_table').find('.cpf').unmask();
						that.parents('.brc_form_grid_inner_table').find('.cpf').mask('999.999.999-99');
					}else{
						alert('Um erro inesperado ocorreu.');
					}
				}
			})
		})
		$(document).on('click', '.brc_order_produto_remove_passageiro', function(e){
			e.preventDefault();
			var that = $(this);
			if(confirm('Realmente deseja remover este item?')){
				that.parents('.brc_order_produto_item_passageiro').remove()
			}
		})
		
		// SUBMIT
		$('button.save_order').on('click', function(e){
			e.preventDefault()
			flo.fadeIn(250)
			
			var formdata = $('form#post').serializeArray();
			if(formdata[7].value == 'auto-draft'){
				formdata[3].value = 'brc_admin_order_edit_submit'
			}else{
				formdata[3].value = 'brc_admin_order_edit_overwrite'
			}
			
			
			jQuery.ajax({
				type		: "post",
				url			: ajax_var.url,
				data		: formdata,
				success		: function(data){
					data = JSON.parse(data);
					if(data.sts){
						flo.fadeOut(250)
						$('form#post').submit();
						
					}else{
						if(data.mensagem){
							$('.brc_notices').html('');
							$('.brc_notices').removeClass('on');
							
							console.log(data.mensagem)
							
							$.each(data.mensagem, function(key, value){
								$('.brc_notices').append('<li class="lin">'+ value +'</li>')
								$('.brc_notices').addClass('on');
							})
							goToByScroll('wpbody');
						}
						flo.fadeOut(250)
						return false;
					}
				}
			})
		});
	}
	if(document.getElementById('order_actions')){
		$('.action-comprovante').on('click', function(e){
			e.preventDefault()
			
			var that = $(this);
			var order_id = that.data('orderid');
			var tb_title = 'Comprovante de depósito #' + order_id;
			
			tb_show(tb_title, 'admin-ajax.php?action=brc_admin_order_edit_comprovante_show&order_id='+ order_id);
		})
	}
	
	// TAX EDIT / SAVE
	if(document.getElementById('linhas-pontos-wrapper')){
		
		// Generate HTML
		var brc_linha_viagem_lin_html = '';
		jQuery.ajax({
			type		: "post",
			url			: ajax_var.url,
			data		: {
				'action' : 'brc_linha_viagem_lin_html',
			},
			success		: function(data){
				data = JSON.parse(data);
				
				if(data.sts){
					brc_linha_viagem_lin_html = data.html;
				}
			}
		})
		$("#lin-itens-wrapper").sortable({ revert: true });
		$("#lin-itens-wrapper li").disableSelection();
		
		$(document).on('click', '#linhas-pontos-add', function(e){
			e.preventDefault();
			$('#lin-itens-wrapper').append(brc_linha_viagem_lin_html)
			$('#linhas-pontos-wrapper select.select2:not(.select2-hidden-accessible)').select2({
				placeholder: "Buscar ponto de embarque...",
			});
		})
		$(document).on('click', '.remove-ponto', function(e){
			e.preventDefault();
			let that = $(this)
			
			that.parents('.lin-itens').remove();
			
		})
		
		
		
	}
	
	
	/*--*/
	$('.brc_form_grid select.select2').select2();
	$('#linhas-pontos-wrapper select.select2').select2({
		placeholder: "Buscar ponto de embarque...",
	});
})
function goToByScroll(id) {
    id = id.replace("link", "");
    $('html,body').animate({
        scrollTop: $("#" + id).offset().top
    }, 500);
}