<?php
	////////////////// Ferramentas para integração
	$tipo = "sandbox"; // api = token de produção, sandbox = token de ambiente de teste.
	$token_encode = base64_encode(API_TOKEN:API_KEY); // Gera uma hash do token
	$token = "jgdawlu980239423=-5iuiughxljvkçsdoíwer830=="; // Token encodado

	$moipidpedido = "PAYhkjladu763290";
	$id_pedido = 0001;

	$url = "https://".$tipo.".moip.com.br/v2/payments/". $moipidpedido;

	$curl = curl_init();

	curl_setopt_array($curl, array(
		CURLOPT_URL => $url,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 30,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "GET",
		CURLOPT_HTTPHEADER => array(
			"authorization: Basic ".$token,
			"content-type: application/json"
		),
	));

	$response = curl_exec($curl);
	echo '<pre>';
	echo $response;
	$err = curl_error($curl);

	curl_close($curl);

	if ($err) {
		echo "cURL Error #:" . $err;
	} else {
		echo $response;
	}

	$data = json_decode($response, true);
	echo "<br></br>";
	echo $data["status"]." -------> ID Pedido: <b>".$id_pedido."</b>";
	echo "<br></br>";
	$status_pagamento = $data["status"];
	if ($status_pagamento == "CANCELLED") {
			// Verifica se pedido está cancelado
	}
	if ($status_pagamento == "AUTHORIZED") {
			// Verifica se está pago o pedido
	}

	echo "</br>";
	echo "</br>";
	echo "<-----------------------------------------------------Verificação de Boletos parcelados em 2x----------------------------------------------------->";

	$id_pedido = 0001;
	$looping = 1;
	while ($looping <= 2) {
		if ($looping == 1) {
				$id_ped = "PAYhkjladu763290-1"; // Pega código do primeiro boleto
			}else{
				$id_ped = "PAYhkjladu763290-2"; // Pega código do segundo boleto
			}
			echo "</br>";
			$url = "https://".$tipo.".moip.com.br/v2/payments/". $id_ped;
			$curl = curl_init();
			curl_setopt_array($curl, array(
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "GET",
				CURLOPT_HTTPHEADER => array(
					"authorization: Basic ".$token,
					"content-type: application/json"
				),
			));
			$response = curl_exec($curl);
			echo '<pre>';
			echo $response; // Retorna o json com as informações do pedido
			$err = curl_error($curl);
			curl_close($curl);
			if ($err) {
				echo "cURL Error #:" . $err;
			} else {
				echo $response;
			}
			$data = json_decode($response, true);
	        // var_dump($data);
			echo "<br></br>";
			echo $data["status"]." -------> ID Pedido: <b>".$id_pedido."_".$looping."</b>";
			echo "<br></br>";
			$status_pagamento = $data["status"];
			if ($status_pagamento == "CANCELLED") {
				if ($looping == 1) {
					// Verifica pedido cancelado, pois já venceu do primeiro boleto
				}elseif ($looping == 2) {
					// Verifica pedido cancelado, pois já venceu do segundo boleto
				}
			}
			if ($status_pagamento == "AUTHORIZED" || $status_pagamento == "SETTLED") {
				if ($looping == 1) {
					// Verifica pagamento do primeiro boleto
				}else{
					// Verifica pagamento do segundo boleto
				}
			}
			$looping ++;
		}
?>