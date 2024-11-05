<?php
	//$url = "http://".$api_data[0][csf("norsel_weight_api")];

	//$api=$_SESSION["user_machine_api"];
	//$url = "$api";
	 $url = "http://172.16.30.9:8021/";


	$curl_handle=curl_init();
	curl_setopt($curl_handle, CURLOPT_URL,"$url");
	curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false );
	curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl_handle, CURLOPT_HEADER, false);
	$postoffice_data = curl_exec($curl_handle);
	curl_close($curl_handle);
	$postoffice_data = json_decode($postoffice_data);
	echo json_encode($postoffice_data);


  ?>