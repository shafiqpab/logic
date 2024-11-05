<?
require_once('../../includes/common.php');
$lib_company = return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name"  );
$current_date = date("d-m-Y",time());
$previous_date = date('d-m-Y', strtotime('-1 day', time()));

 
function loadDoc($url,$postData){	
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


 
 
 
foreach($lib_company as $company_id=>$company_name){
	
	//Comments remove if you need this mail.
//..............................................
	$postData=array(action=>'generate_report',cbo_item_cat=>3,cbo_company_name=>$company_id,txt_date_from=>$previous_date,txt_date_to=>$previous_date,rptType=>1,is_mail_send=>1,auto_mail_user_id=>99999);
	
	loadDoc('http://202.4.104.209/erp/inventory/reports/requires/date_wise_item_recv_issue_report_controller.php',$postData);
	
	
// .....................................
	$postData=array(action=>'generate_report',cbo_item_cat=>4,cbo_company_name=>$company_id,txt_date_from=>$previous_date,txt_date_to=>$previous_date,rptType=>1,is_mail_send=>1,auto_mail_user_id=>99999);
	
	loadDoc('http://202.4.104.209/erp/inventory/reports/requires/date_wise_item_recv_issue_report_controller.php',$postData);
	



// .....................................
	$postData=array(action=>'report_generate',cbo_company_name=>$company_id,txt_exchange_rate=>83,is_mail_send=>1,auto_mail_user_id=>99999);
	loadDoc('http://202.4.104.209/erp/commercial/reports/requires/bank_liability_today_report_controller.php',$postData);
	
	
} 

?>





<script>
function loadDoc___off(url,data) {
  const xhttp = new XMLHttpRequest();
  xhttp.onload = function() {
    //document.getElementById("demo").innerHTML = this.responseText;
  }
  xhttp.open("POST",url,false);
  xhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
  xhttp.send(data);
}
</script>




