<?php
	
 function getSMSRecipient($dataArr=array()){
	$smsRecSql="select a.COMPANY_ID,a.BUYER_IDS,a.BRAND_IDS,c.MOBILE_NO from SMS_GROUP_MST a,SMS_GROUP_CHILD b,USER_MAIL_ADDRESS c where a.SMS_ITEM={$dataArr[item]} and a.id=b.mst_id and b.mobile_id=c.id and C.STATUS_ACTIVE=1 and C.IS_DELETED=0 and A.STATUS_ACTIVE=1 and A.IS_DELETED=0 and B.STATUS_ACTIVE=1 and B.IS_DELETED=0";
	$smsRecSqlRes = sql_select($smsRecSql);
	foreach($smsRecSqlRes as $row)
	{
		
		foreach(explode(',',$row[BUYER_IDS]) as $buyer_id){
			foreach(explode(',',$row[BRAND_IDS]) as $brand_id){
				if($row[MOBILE_NO]!=''){$data_arr[$row[COMPANY_ID]][$buyer_id][$brand_id][]=$row[MOBILE_NO];}
			}
		}
		
		//if($row[MOBILE_NO]!=''){$data_arr[$row[COMPANY_ID]][]=$row[MOBILE_NO];}
	}
	return $data_arr;
 }

function sendSMS($mobileNumber=array(),$message){
	
	$activeApi=3;
	if($activeApi==1){api_by_msg91($mobileNumber,$message);}
	else if($activeApi==2){api_by_txtlocal($mobileNumber,$message);}
	else if($activeApi==3){api_by_grameenphone($mobileNumber,$message);}
}
	
function api_by_msg91($mobileNumber=array(),$message){	
	
	//Your authentication key
	$authKey = "YourAuthKey";
	//Multiple mobiles numbers separated by comma
	$mobileNumber =implode(',',$mobileNumber);
	//Sender ID,While using route4 sender id should be 6 characters long.
	$senderId = "102234";
	//Your message to send, Add URL encoding here.
	$message = urlencode("Test message");
	//Define route 
	$route = "default";
	//Prepare you post parameters
	$postData = array(
		'authkey' => $authKey,
		'mobiles' => $mobileNumber,
		'message' => $message,
		'sender' => $senderId,
		'route' => $route
	);
	
	//API URL
	$url="http://api.msg91.com/api/sendhttp.php";
	
	// init the resource
	$ch = curl_init();
	curl_setopt_array($ch, array(
		CURLOPT_URL => $url,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_POST => true,
		CURLOPT_POSTFIELDS => $postData
		//,CURLOPT_FOLLOWLOCATION => true
	));
	
	
	//Ignore SSL certificate verification
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	
	
	//get response
	$output = curl_exec($ch);
	
	//Print error if any
	if(curl_errno($ch))
	{
		echo 'error:' . curl_error($ch);
	}
	
	curl_close($ch);
	
	echo $output;
}//end function;

function api_by_txtlocal($numbers=array(),$message){	
	// Account details
	$apiKey = urlencode('Your apiKey');
	
	// Message details
	$numbers = array(447123456789, 447987654321);
	$sender = urlencode('Jims Autos');
	$message = rawurlencode('This is your message');
 
	$numbers = implode(',', $numbers);
 
	// Prepare data for POST request
	$data = array('apikey' => $apiKey, 'numbers' => $numbers, "sender" => $sender, "message" => $message);
 
	// Send the POST request with cURL
	$ch = curl_init('https://api.txtlocal.com/send/');
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$response = curl_exec($ch);
	curl_close($ch);
	
	// Process your response here
	echo $response;
}//end function;


function api_by_grameenphone($numbers=array(),$message){	
	$numbers = implode(',', $numbers);
	$message = str_replace('&'," n ", $message);
	
	$url="https://gpcmp.grameenphone.com/gpcmpbulk/messageplatform/controller.home";
	//$url="http://erp.norbangroup.com/erp/SMS/setting/test_action.php";
	
	//$data = array('username'=>'NCLAdmin1','password'=>'Sazzadkhan@123','apicode'=>5,'msisdn'=>$numbers,'countrycode'=>880,'cli'=>'nERP','messagetype'=>1,'message'=>$message,'messageid'=>0);
	
	$data = "username=NCLAdmin1&password=Sazzadkhan@123&apicode=5&msisdn=".$numbers."&countrycode=880&cli=nERP&messagetype=1&message=".$message."&messageid=0";
	
	
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	$head = curl_exec($ch);
	$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);

}//end function;





//echo sendSMS(array('01511100004,01552601805'),'Test sms');










?>