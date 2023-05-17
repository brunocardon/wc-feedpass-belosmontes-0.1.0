var $ = jQuery;
var flo = $('.full-loading')
//const flatpickr = require("flatpickr");

$(document).ready(function(){
	$.mask.definitions['A'] = "[A-Za-z]";
	$('.i-cpf').mask('999.999.999-99');
	$('.i-rg').mask('99.999.999');
	$('.i-tel').mask('(99) 99999-999?9');
	$('.i-data').mask('99/99/9999');
	$('.i-uf').mask('AA');
	$('.i-cep, .billing_postcode, #billing_postcode').mask('99999-999');
	
	var format = new Intl.NumberFormat('pt-BR', { 
		style: 'currency', 
		currency: 'BRL', 
		minimumFractionDigits: 2, 
	}); 
	
	// BUSCA FORM
	$('.fepa_busca_viagens .busca-select2').select2();
	$(".fepa_busca_viagens .date-field").flatpickr({
		dateFormat: "d/m/Y",
		"locale": "pt"
	});

	// EXCURSOES SINGLE
	var exTotals = {
		price : 0,
		pessoas : 0,
		linsId : 1,
	};
	$(document).on("ExValueChange", {
		foo : 'bar',
		exTotal : $('#excursao-valor-total'),
		exTotalField : $('#excursao-valor-total-field'),
		exTotalQuartos : $('#excursao-quartos-total'),
		
	}, function(event, addPrice){
		let exData = event.data
		let iPrice = $('input.i-price');
		let iPessoas = $('#ex-product-config .lin-dados');
		let pacTotals = 0;
		let passTotals = 0;
		
		iPrice.each(function(a,b){
			pacTotals += parseFloat($(b).val()); // valor total apenas contando pacotes
		})
		
		exTotals.price = pacTotals;
		exTotals.pessoas = iPessoas.length;
		
		// HTML
		exData.exTotal.html(format.format(exTotals.price))
		exData.exTotalQuartos.html(exTotals.pessoas)
	})
	
	if(document.getElementById('excursao-variacoes')){
		$(document).on('click', '#comprar-scroll', function(e){
			e.preventDefault();
			goToByScroll('excursao-variacoes');
		})
		$(document).on('click', '.item-btn.esgotado', function(e){
			e.preventDefault();
		})
		$(document).on('click', '.reservar-item', function(e){
			e.preventDefault();
			
			let that = $(this);
			let quartos = that.parents('.variacao-item').find('.lin-dados').length
			
			if(that.parents('.variacao-item').hasClass('resultados')){
				setTimeout(function(){
					that.parents('.variacao-item').find('.dados-wrapper').slideUp(350, function(){
						that.parents('.variacao-item').removeClass('resultados');
						that.parents('.variacao-item').find('.dados-wrapper').html('');
						
						$(document).trigger('ExValueChange', [0]);
					}) 
				}, 100);
			}
			else{
				$('.variacoes-wrapper').addClass('loading')
				jQuery.ajax({
					type		: "post",
					url			: ajax_var.url,
					data		: {
						'action' : 'brc_excursao_add_hospede',
						'varid' : that.data('varid'),
						'linsid' : exTotals.linsId, 
					},
					success		: function(data){
						data = JSON.parse(data);
						$('.variacoes-wrapper').removeClass('loading')
						
						if(data.sts){
							that.parents('.variacao-item').addClass('resultados')
							that.parents('.variacao-item').find('.dados-wrapper').append(data.html)
							that.parents('.variacao-item').find('.i-cpf').unmask();
							that.parents('.variacao-item').find('.i-cpf').mask('999.999.999-99');
							setTimeout(function(){
								that.parents('.variacao-item').find('.dados-wrapper').slideDown(350)
							}, 100);
						}else{
							alert('Ocorreu um erro inesperado, por favor tente novamente.')
						}
						
						exTotals.linsId++;
						$(document).trigger('ExValueChange', [0]);
					}
				})
			}
		})
		$(document).on('click', '.action-add-hospede', function(e){
			e.preventDefault();
			
			let that = $(this);
			let price = that.parents('.variacao-item').find('#variation_id-' + that.data('varid')).val();
			
			$('.variacoes-wrapper').addClass('loading')
			jQuery.ajax({
				type		: "post",
				url			: ajax_var.url,
				data		: {
					'action' : 'brc_excursao_add_hospede',
					'varid' : that.data('varid'),
					'linsid' : exTotals.linsId,
				},
				success		: function(data){
					data = JSON.parse(data);
					$('.variacoes-wrapper').removeClass('loading')
					
					if(data.sts){
						that.parents('.variacao-item').find('.dados-wrapper').append(data.html)
						that.parents('.variacao-item').find('.i-cpf').unmask();
						that.parents('.variacao-item').find('.i-cpf').mask('999.999.999-99');
					}else{
						alert('Ocorreu um erro inesperado, por favor tente novamente.')
					}
					
					exTotals.linsId++;
					$(document).trigger('ExValueChange', [0]);
				}
			})
		})
		$(document).on('click', '.action-remove-hospede', function(e){
			e.preventDefault();
			if(confirm('Deseja remover o item selecionado?')){
				let that = $(this);
				let price = that.parents('.variacao-item').find('#variation_id-' + that.data('varid')).val();
				that.parents('.pacote-dados-wrapper').remove();
				
				$(document).trigger('ExValueChange', [0]);
			}
		})
		$(document).on('change', '.action-ingresso', function(e){
			let that = $(this)
			if(that.prop('checked')){
				that.addClass('i-price')
				that.parent().find('.in-ver').val('yes')
			}else{
				that.removeClass('i-price')
				that.parent().find('.in-ver').val('no')
			}
			$(document).trigger('ExValueChange', [0]);
		})
		$(document).on('click', '#comprar-avancar', function(e){
			e.preventDefault()
			
			var that = $(this),
				brcform = $('#brc-single-excursao-form')
				serial = brcform.serialize()
			
			$('.variacoes-wrapper').addClass('loading')
			jQuery.ajax({
				type		: "post",
				url			: ajax_var.url,
				data		: serial,
				success		: function(data){
					data = JSON.parse(data);
					$('.variacoes-wrapper').removeClass('loading')
					
					$('#ex-product-config .brc-notices').removeClass('on')
					$('#ex-product-config .brc-notices li').remove()
					
					if(data.sts){
						if(data.redirec_url){
							window.location.href = data.redirec_url;
						}else{
							location.reload();
						}
					}else{
						goToByScroll('ex-product-config')
						if(data.error){
							$('#ex-product-config .brc-notices').addClass('on')
							for(var i in data.error.mensagem){
								$('#ex-product-config .brc-notices').append('<li>'+ data.error.mensagem[i] +'</li>')
							}
						}
					}
				}
			})
		})
		$(document).on('click', '.exl', function(e){
			e.preventDefault();
			
			let that = $(this);
			let exl = that.data('target')
			
			goToByScroll(exl, 0);
		})
		if(document.getElementById('excursao-anchor-menu')){
			$('#excursao-anchor-menu .exl').each(function(a, b){
				let e_that = $(b);
				let e_exl = e_that.data('target');
				
				if(!document.getElementById(e_exl)){
					$(b).parents('.li-exl').hide();
				}
			})
		}
		
		let crianca = {};
		$(document).on('click', '.action-add-crianca', function(e){
			e.preventDefault();
			
			let that = $(this);
			
			$('.variacoes-wrapper').addClass('loading');
			jQuery.ajax({
				type		: "post",
				url			: ajax_var.url,
				data		: {
					'action' : 'brc_excursao_add_child',
					'varid' : that.data('varid'),
					'linsid' : that.data('linid')
				},
				success		: function(data){
					data = JSON.parse(data);
					$('.variacoes-wrapper').removeClass('loading')
					
					if(data.sts){
						that.parents('.variacao-item').find('.pacote-criancas-wrapper').append(data.html)
						crianca['rules'] = data.child_rule;
					}else{
						alert('Ocorreu um erro inesperado, por favor tente novamente.')
					}
					
					$(document).trigger('ExValueChange', [0]);
				}
			})
		})
		$(document).on('change', '.crianca-idade', function(e){
			let that = $(this)
			
			if(that.val() != 'no'){
				if(crianca.rules[that.val()]['rule_detalhes']){
					that.parents('.lin-crianca').find('.rule_detalhes').html(crianca.rules[that.val()]['rule_detalhes'])
					that.parents('.lin-crianca').find('.col-descricao').show()
				}else{
					that.parents('.lin-crianca').find('.rule_detalhes').html('')
					that.parents('.lin-crianca').find('.col-descricao').hide()
				}
				
				that.parents('.lin-crianca').find('.price_html').html('+ '+format.format(crianca.rules[that.val()]['rule_preco']))
				that.parents('.lin-crianca').find('.crianca-price').val(crianca.rules[that.val()]['rule_preco'])
			}else{
				that.parents('.lin-crianca').find('.price_html').html('+ '+format.format(0))
				that.parents('.lin-crianca').find('.crianca-price').val(0)
				that.parents('.lin-crianca').find('.rule_detalhes').html('')
				that.parents('.lin-crianca').find('.col-descricao').hide()
			}
			
			$(document).trigger('ExValueChange', [0]);
		})
		$(document).on('click', '.action-remove-crianca', function(e){
			e.preventDefault();
			
			if(confirm('Deseja remover o item selecionado?')){
				let that = $(this);
				that.parents('.lin-crianca').remove();
				$(document).trigger('ExValueChange', [0]);
			}
		})
		
		$(document).on('scroll', function(e){
			let sTop = document.documentElement.scrollTop;
			let wHeigh = $(window).height();
			let prodTop = $("#ex-product-config").offset().top
			
			if(sTop >= (wHeigh - (wHeigh / 2))){
				if(sTop <= (prodTop - (wHeigh / 2))){
					$('#ex-infos-scroll').addClass('on')
				}else{
					$('#ex-infos-scroll').removeClass('on')
				}
			}else{
				$('#ex-infos-scroll').removeClass('on')
			}
			
		})
	}
	
	// VIAGENS
	if(document.getElementById('viagens-resultados')){
		var linhas = {}, lin_html = {};
		$(document).on('click', '.viagem-item-selecionar', function(e){
			e.preventDefault()
			
			let that = $(this);
			let viagemid = that.data('viagemid');
			let selvolta = that.data('selvolta');
			let oponto = that.data('oponto');
			let dponto = that.data('dponto');
			
			$('.viagem-itens-wrapper').removeClass('loading')
			$('.viagem-itens-wrapper').removeClass('item-open')
			
			if(that.parent().hasClass('resultados')){
				setTimeout(function(){
					that.parents('.viagem-item').find('.viagem-detalhes').slideUp(350, function(){
						that.parents('.viagem-item').removeClass('opened')
						that.parent().removeClass('resultados')
						that.parents('.viagem-item').find('.viagem-detalhes').html('')
					})
				}, 100);
			}else{
				$('.viagem-itens-wrapper').addClass('loading')
				jQuery.ajax({
					type		: "post",
					url			: ajax_var.url,
					data		: {
						'action' : 'brc_viagem_detalhes',
						'viagemid' : viagemid,
						'selvolta' : selvolta,
						'oponto' : oponto,
						'dponto' : dponto,
					},
					success		: function(data){
						data = JSON.parse(data);
						$('.viagem-itens-wrapper').removeClass('loading')
						$('.viagem-itens-wrapper').addClass('item-open')
						
						if(data.sts){
							that.parent().addClass('resultados')
							that.parents('.viagem-item').addClass('opened')
							that.parents('.viagem-item').find('.viagem-detalhes').append(data.html)
							that.parents('.viagem-item').find('.i-rg').unmask();
							that.parents('.viagem-item').find('.i-cpf').unmask();
							that.parents('.viagem-item').find('.i-rg').mask('99.999.999');
							that.parents('.viagem-item').find('.i-cpf').mask('999.999.999-99');
							
							linhas[viagemid] = data.linha;
							lin_html[viagemid] = data.lin_html;
							
							setTimeout(function(){
								that.parents('.viagem-item').find('.viagem-detalhes').slideDown(350)
								if($(window).width() <= 992){
									goToByScroll(that.parents('.viagem-item').find('.viagem-detalhes').attr('id'))
								}
							}, 100);
						}else{
							alert('Ocorreu um erro inesperado, por favor tente novamente.')
						}
					}
				})
			}
		})
		$(document).on('click', '.action-add-passageiro', function(e){
			e.preventDefault()
			
			var that = $(this),
				viagemid = that.data('viagemid'),
				ponto,
				ponto_price,
				lin_dados,
				ta;
			
			$('.viagem-itens-wrapper').addClass('loading')
			jQuery.ajax({
				type		: "post",
				url			: ajax_var.url,
				data		: {
					'action' : 'brc_viagem_add_passageiro',
					'viagemid' : viagemid,
				},
				success		: function(data){
					data = JSON.parse(data);
					$('.viagem-itens-wrapper').removeClass('loading')
					
					if(data.sts){
						that.parents('.viagem-detalhes-passageiros-dados').append(data.html)
						that.parents('.viagem-detalhes-passageiros-dados').find('.i-rg').unmask();
						that.parents('.viagem-detalhes-passageiros-dados').find('.i-cpf').unmask();
						that.parents('.viagem-detalhes-passageiros-dados').find('.i-rg').mask('99.999.999');
						that.parents('.viagem-detalhes-passageiros-dados').find('.i-cpf').mask('999.999.999-99');
						
						lin_dados = that.parents('.viagem-detalhes-form').find('.lin-dados')
						ta = lin_dados.length*data.price
						if(linhas[viagemid]){
							if(that.parents('.viagem-detalhes-form').find('.ponto_embarque').val() > 0){
								ponto = parseInt(that.parents('.viagem-detalhes-form').find('.ponto_embarque').val())
								ponto_price = linhas[viagemid][ponto]
								
								ta = lin_dados.length*ponto_price
							}
						}
						
						that.parents('.viagem-detalhes-form').find('.item-subtotal').attr('price', ta)
						that.parents('.viagem-detalhes-form').find('.item-subtotal').html(format.format(ta))
					}else{
						alert('Ocorreu um erro inesperado, por favor tente novamente.')
					}
				}
			})
		})
		$(document).on('click', '.action-remove-passageiro', function(e){
			e.preventDefault()
			if(confirm('Deseja remover o item selecionado?')){
				var that = $(this), oldta, ta, oldprice, price;
				var viagemid = that.parents('.viagem-detalhes-form').find('input[name="viagem_id"]').val();
				var ponto = parseInt(that.parents('.viagem-detalhes-form').find('.ponto_embarque').val()), ponto_price
				var lin_dados = that.parents('.viagem-detalhes-form').find('.lin-dados');
				
				if(linhas[viagemid] && ponto > 0){
					ponto_price = linhas[viagemid][ponto]
					price = (lin_dados.length-1)*ponto_price
				}else{
					oldta = that.parents('.viagem-detalhes-form').find('.lin-dados')
					oldta = oldta.length
					oldprice = that.parents('.viagem-detalhes-form').find('.item-subtotal').attr('price')
					oldprice = parseFloat(oldprice)/oldta
					ta = oldta - 1;
					price = ta*oldprice
				}
				that.parents('.viagem-detalhes-form').find('.item-subtotal').attr('price', price)
				that.parents('.viagem-detalhes-form').find('.item-subtotal').html(format.format(price))
				that.parents('.lin-dados').remove()
			}
		})
		$(document).on('submit', '.viagem-detalhes-form', function(e){
			e.preventDefault();
			
			var that = $(this),
				serial = that.serialize(),
				viagemid = that.data('viagemid'),
				ta;
			
			$('.viagem-itens-wrapper').addClass('loading')
			jQuery.ajax({
				type		: "post",
				url			: ajax_var.url,
				data		: serial,
				success		: function(data){
					data = JSON.parse(data);
					$('.viagem-itens-wrapper').removeClass('loading')
					
					that.find('.brc-notices').removeClass('on')
					that.find('.brc-notices li').remove()
					
					if(data.sts){
						if(data.redirec_url){
							window.location.href = data.redirec_url;
						}else{
							location.reload();
						}
					}else{ 
						goToByScroll(that.find('#viagem-detalhes-passageiros-dados-'+viagemid).attr('id'))
						if(data.error){
							that.find('.brc-notices').addClass('on')
							for(var i in data.error.mensagem){
								that.find('.brc-notices').append('<li>'+ data.error.mensagem[i] +'</li>')
							}
						}
					}
				}
			})
		})
		$(document).on('click', '.lugar-toggler', function(e){
			e.preventDefault();
			
			var that 			= $(this), 
				assento 		= that.data('assento'),
				astatus 		= that.data('status'),
				viagemid 		= that.data('viagemid'),
				preco 			= parseFloat(that.data('preco')),
				alinid 			= 'a-'+viagemid+'-'+assento,
				alinhtml 		= '<div id="'+alinid+'">'+lin_html[viagemid]+'</div>',
				ponto 			= that.parents('.viagem-detalhes-form').find('.ponto_embarque').val(),
				lin_dados;
			
			
			if(astatus == 'disponivel'){
				that.parent().removeClass('ocupado');
				that.parent().removeClass('disponivel');
				that.parent().removeClass('selecionado');
				that.parent().addClass('selecionado');
				
				that.data('status', 'selecionado')
				
				that.parents('.viagem-detalhes-form').find('.viagem-detalhes-passageiros-dados').append(alinhtml)
				lin_dados = that.parents('.viagem-detalhes-form').find('.lin-dados');
				$('#'+alinid).find('.i-rg').unmask();
				$('#'+alinid).find('.i-cpf').unmask();
				$('#'+alinid).find('.i-rg').mask('99.999.999');
				$('#'+alinid).find('.i-cpf').mask('999.999.999-99');
				
				if(linhas[viagemid] && ponto > 0){
					ponto_price = linhas[viagemid][parseInt(ponto)]
					preco = ponto_price;
					preco_total = lin_dados.length*preco
				}
				preco_total = lin_dados.length*preco
				
				
				$('#'+alinid).find('.col-valor').html(format.format(preco));
				$('#'+alinid).find('.col-assento').html('Assento: <span>'+assento+'</span>');
				$('#'+alinid).find('input[name="passageiro-assento[]"]').val(assento)
				that.parents('.viagem-detalhes-form').find('.item-subtotal').html(format.format(preco_total))
				
			}
			else if(astatus == 'selecionado'){
				that.parent().removeClass('ocupado');
				that.parent().removeClass('disponivel');
				that.parent().removeClass('selecionado');
				that.parent().addClass('disponivel');
				
				that.data('status', 'disponivel')
				
				$('#'+alinid).remove()
				lin_dados = that.parents('.viagem-detalhes-form').find('.lin-dados');
				
				if(linhas[viagemid] && ponto > 0){
					ponto_price = linhas[viagemid][parseInt(ponto)]
					
					preco = ponto_price;
					preco_total = (lin_dados.length)*preco
				}
				preco_total = (lin_dados.length)*preco
				
				that.parents('.viagem-detalhes-form').find('.item-subtotal').html(format.format(preco_total))
			}
		})
		$(document).on('change', '.ponto_embarque', function(e){
			
			var that = $(this), price,
				viagemid = that.parents('.viagem-detalhes-form').data('viagemid'),
				lin_dados = that.parents('.viagem-detalhes-form').find('.lin-dados'),
				ta;
			
			if(that.val() > 0){
				price = linhas[viagemid][that.val()] * lin_dados.length
				price = price<1?linhas[viagemid][that.val()]:price;
				
				that.parents('.viagem-detalhes-form').find('.item-lin-valor').html(format.format(linhas[viagemid][that.val()]))
				that.parents('.viagem-detalhes-form').find('.item-subtotal').html(format.format(price))
			}
		})
	}
	
	// CHECKOUT
	if(document.getElementById('order_review')){
		$(document).on('click', '.remove-cart-item', function(e){
			e.preventDefault();
			
			let that = $(this);
			let cartitem = that.data('cartitem');
			
			if(confirm('Deseja realmente remover o item?')){
				flo.fadeIn(250)
				jQuery.ajax({
					type		: "post",
					url			: ajax_var.url,
					data		: {
						'action' : 'brc_remove_cart_item',
						'cartitem' : cartitem,
					},
					success		: function(data){
						data = JSON.parse(data);
						flo.fadeOut(150)
						
						if(data.sts){
							if(data.empty){
								if(data.redirec_url){
									window.location.href = data.redirec_url;
								}else{
									jQuery('body').trigger('update_checkout');
								}
							}
							jQuery('body').trigger('update_checkout');
						}
					}
				})
			}
		})
	}
	
	
	/*
	 * VISUAL COMPOSER BLOCKS
	 * 
	 */
	// fepa_blog_carrossel
	if($('.fepa_blog_carrossel').length > 0){
		
		var blog_carrossel_thumb, blog_carrossel;
		setTimeout(function(){
			$('.fepa_blog_carrossel').each(function(a, b){
				
				blog_carrossel_thumb = $(b).find('.thumbs-wrapper-inner')
				blog_carrossel_thumb.slick({
					arrows : false,
					asNavFor: '.content-wrapper-inner',
				});
				
				blog_carrossel = $(b).find('.content-wrapper-inner')
				blog_carrossel.slick({
					infinite: true,
					asNavFor: '.thumbs-wrapper-inner',
					focusOnSelect: true,
					slidesToShow: 1,
					slidesToScroll: 1,
					prevArrow: $(b).find('.nav-prev'),
					nextArrow: $(b).find('.nav-next')
				});
			})
			
		}, 100);
	}
	
})
function goToByScroll(id, offset=0){
    id = id.replace("link", "");
    $('html,body').animate({
        scrollTop: $("#" + id).offset().top - offset
    }, 1000, 'swing');
}