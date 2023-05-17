<?php
	
	// MODIFICA PADRÕES DO WOOCOMMERCE
	if(is_plugin_active('woocommerce/woocommerce.php')){
		
		// TAX Linha ADICIONAL META BOXES
		if(!function_exists('brc_linha_edit_register_custom_metaboxes')){
			function brc_linha_edit_register_custom_metaboxes(){
				$taxonomy = 'brc_linhas';
				$prefix = get_cmb2_term_metaboxes_prefix($taxonomy);
				$general = new_cmb2_box(array(
					'id' 			=> $prefix . 'geral', // brc_prod_geral
					'title'         => 'Dados da Linha',
					'object_types'  => array('term'),
					'taxonomies'  	=> array($taxonomy),
				));
				/*
				$pontos_parada = $general->add_field( array(
					'name'    		=> 'Pontos de parada',
					'desc'    		=> 'Adicione os pontos de parada durante a trajetória da linha. <br/><em>Adicione cada item em uma linha.</em>',
					'id'      		=> $prefix.'pontos_parada',
					'type'    		=> 'textarea',
				));
				*/
				$linha_ini = $general->add_field( array(
					'name'    		=> 'Início da Linha',
					'id'      		=> $prefix.'linha_ini',
					'type'    		=> 'text',
				));
				$linha_fim = $general->add_field( array(
					'name'    		=> 'Término da Linha',
					'id'      		=> $prefix.'linha_fim',
					'type'    		=> 'text',
				));
				/*
				$tarifa = $general->add_field( array(
					'name'    		=> 'Tarifa',
					'id'      		=> $prefix.'tarifa',
					'type'    		=> 'text',
					'desc'    		=> 'Dados para Bilhete de embarque. (Valor fixo no bilhete)',
					'default' 		=> '155,00'
				));
				$agencia = $general->add_field( array(
					'name'    		=> 'Agência',
					'id'      		=> $prefix.'agencia',
					'type'    		=> 'text',
					'desc'    		=> 'Saindo de Montes Claros = MOC | Saindo de Belo Horizonte = BH',
				));
				$pedagio = $general->add_field( array(
					'name'    		=> 'Pedágio',
					'id'      		=> $prefix.'pedagio',
					'type'    		=> 'text',
					'desc'    		=> 'Valor fixo no bilhete',
				));
				$taxa = $general->add_field( array(
					'name'    		=> 'Taxa de Embarque',
					'id'      		=> $prefix.'taxa',
					'type'    		=> 'text',
					'desc'    		=> 'Valor fixo no bilhete',
				));
				$aliq = $general->add_field( array(
					'name'    		=> 'Alíq. ICMS',
					'id'      		=> $prefix.'aliq',
					'type'    		=> 'text',
					'desc'    		=> 'Valor fixo no bilhete (%)',
					'default' 		=> '18%'
				));
				$vtributo = $general->add_field( array(
					'name'    		=> 'Valor Tributo',
					'id'      		=> $prefix.'vtributo',
					'type'    		=> 'text',
					'desc'    		=> 'Valor fixo no bilhete',
				));
				$prefixo = $general->add_field( array(
					'name'    		=> 'Prefixo',
					'id'      		=> $prefix.'Prefixo',
					'type'    		=> 'text',
					'desc'    		=> 'Valor fixo no bilhete',
				));
				$fpagamento = $general->add_field( array(
					'name'    		=> 'Forma de pagamento',
					'id'      		=> $prefix.'fpagamento',
					'type'    		=> 'text',
					'desc'    		=> 'Valor fixo no bilhete',
				));
				*/
				$linha_grupo_paradas = $general->add_field(array(
					'id'          => 'linha_grupo_paradas',
					'type'        => 'group',
					'description' => 'Adicionar Pontos de Paradas',
					'options'     => array(
						'group_title'       => 'Ponto de Parada {#}',
						'add_button'        => 'Adicionar Ponto',
						'remove_button'     => 'Remover Ponto',
						'sortable'          => true,
					),
				));
				$general->add_group_field($linha_grupo_paradas, array(
					'name'    	=> 'Nome do Ponto',
					'desc' 		=> 'Descreva o nome ou como é conhecido o ponto.',
					'id'      	=> 'p-nome',
					'type'    	=> 'text',
				));
				$general->add_group_field($linha_grupo_paradas, array(
					'name'    	=> 'Endereço do Ponto',
					'desc' 		=> 'Informe o endereço completo do ponto.',
					'id'      	=> 'p-endereco',
					'type'    	=> 'text',
				));
				$general->add_group_field($linha_grupo_paradas, array(
					'name'    	=> 'Valor da passagem.',
					'desc' 		=> 'Informe o valor do custo do ponto de embarque. <br/><strong>Deixe em branco para pegar o valor normal da viagem.</strong>',
					'id'      	=> 'p-valor',
					'type'    	=> 'text',
				));
				$general->add_group_field($linha_grupo_paradas, array(
					'name'    	=> 'Tempo de viagem até o ponto.',
					'desc' 		=> 'Informe o valor em <strong>MINUTOS</strong> do tempo de viagem até o ponto, considerando a saída inicial da linha...',
					'id'      	=> 'p-tempo',
					'type'    	=> 'text',
				));
			}
			add_action('cmb2_meta_boxes', 'brc_linha_edit_register_custom_metaboxes');
		}
		
		// TAX Veículo ADICIONAL META BOXES
		if(!function_exists('brc_veiculo_edit_register_custom_metaboxes')){
			function brc_linhas_edit_register_custom_metaboxes(){
				
				$taxonomy = 'brc_veiculo';
				$prefix = get_cmb2_term_metaboxes_prefix($taxonomy);
				$general = new_cmb2_box(array(
					'id' 			=> $prefix . 'geral', // brc_prod_geral
					'title'         => 'Dados do Veículo',
					'object_types'  => array('term'),
					'taxonomies'  	=> array($taxonomy),
				));
				
				$placa = $general->add_field( array(
					'name'    		=> 'Placa',
					'id'      		=> $prefix.'placa',
					'type'    		=> 'text',
				));
				$empresa = $general->add_field( array(
					'name'    		=> 'Empresa',
					'id'      		=> $prefix.'empresa',
					'type'    		=> 'text',
				));
				$acomodacoes = $general->add_field(array(
					'name'           	=> 'Acomodações',
					'id'             	=> $prefix.'acomodacoes',
					'type'           	=> 'select',
					'show_option_none' 	=> true,
					'options'          	=> get_acomodacoes(),
				));
				$brindes = $general->add_field( array(
					'name'    => 'Brindes',
					'desc'    => 'É distribuido brindes?',
					'id'      => $prefix.'brindes',
					'type'    => 'checkbox',
				));
				$wifi = $general->add_field( array(
					'name'    => 'Wifi',
					'desc'    => 'O veículo possui Wifi liberado?',
					'id'      => $prefix.'wifi',
					'type'    => 'checkbox',
				));
				$tomadas = $general->add_field( array(
					'name'    => 'Tomadas',
					'desc'    => 'O veículo possui tomadas disponíveis para seus passageiros?',
					'id'      => $prefix.'tomadas',
					'type'    => 'checkbox',
				));
				$arcondicionado = $general->add_field( array(
					'name'    => 'Ar-condicionado',
					'desc'    => 'O veículo possui Ar-condicionado?',
					'id'      => $prefix.'arcondicionado',
					'type'    => 'checkbox',
				));
				$banheiro = $general->add_field( array(
					'name'    => 'Banheiro(s)',
					'desc'    => 'O veículo possui Banheiro(s) disponíveis para seus passageiros?',
					'id'      => $prefix.'banheiro',
					'type'    => 'checkbox',
				));
			}
			add_action('cmb2_meta_boxes', 'brc_linhas_edit_register_custom_metaboxes');
		}
		
		// TAX Motorista ADICIONAL META BOXES
		if(!function_exists('brc_motorista_edit_register_custom_metaboxes')){
			function brc_motorista_edit_register_custom_metaboxes(){
				$taxonomy = 'brc_motorista';
				$prefix = get_cmb2_term_metaboxes_prefix($taxonomy);
				$general = new_cmb2_box(array(
					'id' 			=> $prefix . 'geral', // brc_prod_geral
					'title'         => 'Dados do(a) Motorista',
					'object_types'  => array('term'),
					'taxonomies'  	=> array($taxonomy),
				));
				
				$telefone = $general->add_field( array(
					'name'    		=> 'Telefone',
					'id'      		=> $prefix.'telefone',
					'type'    		=> 'text',
				));
				$email = $general->add_field( array(
					'name'    		=> 'E-mail',
					'id'      		=> $prefix.'email',
					'type'    		=> 'text_email',
				));
				$documento = $general->add_field( array(
					'name'    		=> 'Documento CNH',
					'id'      		=> $prefix.'documento',
					'type'    		=> 'text',
				));
			}
			add_action('cmb2_meta_boxes', 'brc_motorista_edit_register_custom_metaboxes');
		}
		
	}
?>