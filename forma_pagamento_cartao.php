<?php
////////////////// Ferramentas para integração
$tipo = "sandbox"; // api = token de produção, sandbox = token de ambiente de teste.
$token_encode = base64_encode(API_TOKEN:API_KEY); // Gera uma hash do token
$token = "jgdawlu980239423=-5iuiughxljvkçsdoíwer830=="; // Token encodado
////////////////////////////////////////CRIANDO INTEGRAÇÃO MOIP PARA CARTÃO DE CRÉDITO////////////////////////////////////////
if ($_POST['pagamento'] == 'cartao') {
	//Este POST tem a finalidade de pegar os dados do pagador na escolha de pagamento e enviar para a API
	//Dados do cartão
	$cardNumber             = 4012001037141112;
	$validade               = explode("/","09/19");
	$cvv                    = 589;
	$nome_card              = "Daniela A Porto";
	$numero_parcelas        = 1;
	$mes_venc               = trim($validade[0]); //Retorna 09 (Mês)
	$ano_venc               = trim($validade[1]); //Retorna 19 (Ano)
	//Dados do pagador
	$id_usuario       		= 1; //INT AUTO INCREMENT (CAMPO ÚNICO PARA IDENTIFICAÇÃO DO USUÁRIO)
	$nome       			= "Daniela Aparecida Porto"; // Nome do usuário pagador
	$email      			= "danny.lorena@outlook.com"; // E-mail do usuário pagador
	$celular_com_ddd    	= 19999483074; // Celular do pagador
	$ddd_celular 			= substr(19999483074, 0, 2); // Recupera somente DDD do celular
	$celular 				= substr(19999483074, 2, 9); // Recupera somente o número do celular com código
	$tipo_usuario 			= "CPF"; // Recupera se o usuário é um PJ ou PF
	$cpf_cnpj 				= 98387651044; // Recupera CPF ou CNPJ do usuário
	$logradouro 			= "Rua culto a ciência"; // Recupera Rua, Avenida e etc do usuário
	$bairro 				= "Centro"; // Recupera o bairro deste endereço
	$numero_residencia 		= 76; // Recupera o número endereço
	$cidade 				= "Campinas"; // Recupera a cidade do endereço
	$estado 				= "SP"; // Recupera o estado do endereço
	$cep 					= 13020061; // Recupera o cep deste endereço
	$pais 					= "BRA"; // Recupera o pais deste endereço
	$nascimento         	= "2000-09-30"; // Data de nascimento do usuário
	// Dados do pedido
	$id_pedido    			= 0001;
	$valor_compra			= 200.00;
	// Formatar valor para inteiro pois não aceitam . nem ,
	$formata_valor     	 	= str_replace("." , "" , $valor_compra); 
	$ip                 	= getenv("REMOTE_ADDR");
	// Acrescente caso tiver taxa ou juros
	//"subtotals": {
      //"addition":'.$formata_taxa.'
    //}

	$json = '{
		"ownId": "'.$id_pedido.'",
		"amount": {
			"currency": "BRL"
			},
			"items": [
			{
				"product": "Compra SEO Zé",
				"quantity": 1,
				"price": '.$formata_valor.'
			}
			],
			"customer": {
				"ownId": "'.$id_usuario.'",
				"fullname": "'.$nome.'",
				"email": "'.$email.'",
				"phone": {
					"countryCode": "55",
					"areaCode": "'.$ddd_celular.'",
					"number": "'.$celular.'"
					},
					"taxDocument": {
						"type": "'.$tipo_usuario.'",
						"number": "'.$cpf_cnpj.'"
						},
						"shippingAddress": {
							"street": "'.$logradouro.'",
							"streetNumber": '.$numero_residencia.',
							"district": "'.$bairro.'",
							"city": "'.$cidade.'",
							"state": "'.$estado.'",
							"country": "'.$pais.'",
							"zipCode": "'.$cep.'"
						}
					}
				}';

				include "forma_pagamento/cartao/cartao_credito.php";
			}
			?>