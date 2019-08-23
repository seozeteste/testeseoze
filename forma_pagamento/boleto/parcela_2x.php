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
         // echo "Error #:" . $err . "|.";
} else {
}


if ($data['status'] == 'CREATED') {

  if ($looping == 1) {
    $data_vencimento = date('Y-m-d', strtotime("+3 days"));
  }else{
    $data_vencimento = date('Y-m-d', strtotime("+30 days"));
  }


  $json = '{  
    "fundingInstrument":{  
     "method":"BOLETO",
     "boleto":{  
      "expirationDate":"'.$data_vencimento.'"
    }
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
        // echo "cURL Error #:" . $err;
} else {
    // echo $response;
}

$data = json_decode($response, true);

if ($data['errors']['0']['code'] == '') {
  if ($data['ERROR'] == '') {
    if ($looping == 1) {
      mysql_query("UPDATE pedido SET codigo_boleto_1 = '{$data[id]}', url_boleto_1 = '{$data["_links"]["payBoleto"]["redirectHref"]}' WHERE id_pedido_ = '{$id_pedido}'") or die (mysql_error());
    }elseif($looping == 2){
      mysql_query("UPDATE pedido SET codigo_boleto_2 = '{$data[id]}', url_boleto_2 = '{$data["_links"]["payBoleto"]["redirectHref"]}' WHERE id_pedido_ = '{$id_pedido}'") or die (mysql_error());
    }

    ?>
    <script type="text/javascript">
      Swal.fire({
        type: 'success',
        title: 'Estamos Gerando seu Boleto!',
        text: 'Ele será aberto automaticamente.',
        showCloseButton: false,
        showCancelButton: false,
        showConfirmButton: false,
        allowOutsideClick: false,
      });
      setTimeout(function() {
        location.href="index.php";
      }, 5000);
    </script>
    <?php 
    if ($looping == 1) {
      ?>
      <script type="text/javascript">
        window.open('<?=$data["_links"]["payBoleto"]["redirectHref"];?>/print','Boleto Moip',
         'toolbar=yes,menubar=yes,resizable=yes,status=no,scrollbars=yes,width=600,height=430');
       </script>
       <?php
     }
   } else {
     mysql_query("DELETE FROM pedido WHERE id_pedido = '{$id_pedido}'") or die (mysql_error());
     echo "<script>alert('#00005 - " . $data['errors']['0']['description'] . ".');</script>";
   }
 } else {
  mysql_query("DELETE FROM pedido WHERE id_pedido = '{$id_pedido}'") or die (mysql_error());
  echo "<script>alert('#00009 - " . $data['errors']['0']['description'] . ".');</script>";
}
} else { 
 mysql_query("DELETE FROM pedido WHERE id_pedido = '{$id_pedido}'") or die (mysql_error());
 echo "<script>alert('#00001 - " . $data['errors']['0']['description'] . ".');</script>";
}
?>