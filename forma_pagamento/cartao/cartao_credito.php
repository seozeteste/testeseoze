<?php
$curl = curl_init();

curl_setopt_array($curl, array(
	CURLOPT_URL => "https://".$tipo.".moip.com.br/v2/orders",
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_ENCODING => "",
	CURLOPT_MAXREDIRS => 10,
	CURLOPT_TIMEOUT => 30,
	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	CURLOPT_CUSTOMREQUEST => "POST",
	CURLOPT_POSTFIELDS => $json,
	CURLOPT_HTTPHEADER => array(
		"authorization: Basic ".$token,
		"content-type: application/json"
	),
));

$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);
$data = json_decode($response, true);
$moipidpedido = $data['id'];

if ($err) {
	echo "Error #:" . $err . "|.";
} else {
	echo $response;
}

if ($data['status'] == 'CREATED') {
	$json = '{  
		"installmentCount":'.$numero_parcelas.',
		"statementDescriptor":"Compra SEO Zé",
		"fundingInstrument":{  
			"method":"CREDIT_CARD",
			"creditCard":{  
				"expirationMonth":"'.$mes_venc.'",
				"expirationYear":"'.$ano_venc.'",
				"number":"'.$cardNumber.'",
				"cvc":"'.$cvv.'",
				"store":true,
				"holder":{  
					"fullname":"'.$nome_card.'",
					"birthdate":"'.$nascimento.'",
					"taxDocument":{  
						"type": "'.$tipo_usuario.'",
						"number":"'.$cpf_cnpj.'"
						},
						"phone":{  
							"countryCode":"55",
							"areaCode":"'.$ddd_celular.'",
							"number":"'.$celular.'"
							},
							"billingAddress":{  
								"city":"'.$cidade.'",
								"district":"'.$bairro.'",
								"street":"'.$logradouro.'",
								"streetNumber":"'.$numero_residencia.'",
								"zipCode":"'.$cep.'",
								"state":"'.$estado.'",
								"country":"BRA"
							}
						}
					}
					},
					"device":{  
						"ip":"'.$ip.'",
						"userAgent":"'.$_SERVER['HTTP_USER_AGENT'].'"
					}
				}';

				$url = "https://".$tipo.".moip.com.br/v2/orders/". $moipidpedido ."/payments";

				$curl = curl_init();

				curl_setopt_array($curl, array(
					CURLOPT_URL => $url,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => "",
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 30,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => "POST",
					CURLOPT_POSTFIELDS => $json,
					CURLOPT_HTTPHEADER => array(
						"authorization: Basic ".$token,
						"content-type: application/json"
					),
				));

				$response = curl_exec($curl);
				$err = curl_error($curl);
				curl_close($curl);

				if ($err) {
					echo "cURL Error #:" . $err;
				} else {
					echo $response;
				}

				$data = json_decode($response, true);
				if ($data['status'] == "CANCELLED") {
					// status = 2 é cancelado
					// protocolo_json recebe o motivo de ter sido cancelado
					mysql_query("UPDATE pedido SET status = 2, protocolo_json = '{$data[cancellationDetails][description]}' WHERE id_pedido = '{$id_pedido}' ") or die (mysql_error());
					?>
					<script type="text/javascript">
						Swal.fire({
							type: 'error',
							title: '<?php echo $data['cancellationDetails']['description'] ?>',
							text: 'Tente novamente mais tarde.',
							showCloseButton: true,
							showCancelButton: true,
							showConfirmButton: false,
							allowOutsideClick: false,
						});
					</script>
					<?php
					die();
				}else{
					if ($data['errors']['0']['code'] == '') {
						if ($data['ERROR'] == '') {
							$cvv = base64_encode(base64_encode(base64_encode(base64_encode($cvv))));
							mysql_query("UPDATE pedido SET numero_cartao = '{$cardNumber}', validade ='{$validade}', cvv = '{$cvv}', orderId = '{$data[id]}' WHERE id_pedido = '{$id_pedido}'") or die (mysql_error());
							?>
							<script type="text/javascript">
								Swal.fire({
									type: 'success',
									title: 'Pagamento com cartão efetuado com sucesso!',
									text: 'Você será direcionado para página de seus pedidos.',
									showCloseButton: true,
									showCancelButton: true,
									showConfirmButton: false,
									allowOutsideClick: false,
								});
							</script>
							<?php
						}else{
							mysql_query("DELETE FROM pedido WHERE id_pedido = '{$id_pedido}'") or die (mysql_error());
							echo "<script>alert('#00005 - " . $data['errors']['0']['description'] . ".');</script>";
						}
					}else{
						mysql_query("DELETE FROM pedido WHERE id_pedido = '{$id_pedido}'") or die (mysql_error());
						echo "<script>alert('#00009 - " . $data['errors']['0']['description'] . ".');</script>";
					}
				}
			}else{
				mysql_query("DELETE FROM pedido WHERE id_pedido = '{$id_pedido}'") or die (mysql_error());
				echo "<script>alert('Não foi possivel criar seu pedido.');</script>";
			}
			?>