 <?php
date_default_timezone_set("Asia/Dhaka");
require_once('../../includes/common.php');
require_once('../setting/sms_setting.php');


/*$file = 'sms_log.txt';
$current = file_get_contents($file);
$current .= "Export Info :: Date & Time: ".date("d-m-Y H:i:s",time())."\n";
file_put_contents($file, $current);*/


$satart_date = change_date_format(date("Y-m-1 H:i:s",time()),'','',1);
//$current_date = change_date_format(date("Y-m-d H:i:s",time()),'','',1);
$previous_date = change_date_format(date('Y-m-d H:i:s', strtotime('-1 day',time())),'','',1);
$satart_date = change_date_format(date("Y-m-1 H:i:s",strtotime($previous_date)),'','',1);



$buyer_lib =return_library_array( "select id, SHORT_NAME as BUYER_NAME from lib_buyer where status_active=1 and is_deleted=0", "id", "BUYER_NAME");
$company_lib =return_library_array( "select id, COMPANY_SHORT_NAME from lib_company where status_active=1 and is_deleted=0 ", "id", "COMPANY_SHORT_NAME");

$company_ids = implode(',',array_keys($company_lib));


 //$conversion_rate=return_field_value("conversion_rate","currency_conversion_rate","is_deleted=0 and status_active=1 and id=(select max(id) from currency_conversion_rate where currency=2 and is_deleted=0 and status_active=1)","",$con);

//$company_ids=1;
//Delivery part start...............................................................
	
	$delivery_sql="select a.COMPANY_ID,a.SYS_NUMBER_PREFIX_NUM,b.INVOICE_NO, b.PO_BREAK_DOWN_ID as PO_ID,  b.EX_FACTORY_DATE,B.PO_BREAK_DOWN_ID,b.EX_FACTORY_QNTY,D.JOB_NO_PREFIX_NUM as JOB_NO,D.STYLE_REF_NO,D.BUYER_NAME,D.TOTAL_SET_QNTY,C.PO_NUMBER,C.PO_QUANTITY,C.PO_TOTAL_PRICE  from PRO_EX_FACTORY_DELIVERY_MST a,PRO_EX_FACTORY_MST b,wo_po_break_down c ,wo_po_details_master d  where a.id=B.DELIVERY_MST_ID and b.PO_BREAK_DOWN_ID=c.id and C.JOB_ID=d.id and A.IS_DELETED=0 and A.STATUS_ACTIVE=1 and B.IS_DELETED=0 and B.STATUS_ACTIVE=1  and c.IS_DELETED=0 and d.IS_DELETED=0 and d.STATUS_ACTIVE=1 and a.COMPANY_ID in($company_ids) and   a.entry_form!=85 and b.EX_FACTORY_DATE between '$satart_date' and '$previous_date'";
	  //echo $delivery_sql;die;					
						
	$delivery_sql_res=sql_select($delivery_sql);
	$deliveryDataArr=array();
	foreach($delivery_sql_res as $row)
	{
		$rate = ($row[PO_TOTAL_PRICE]/($row[PO_QUANTITY]*$row[TOTAL_SET_QNTY]));
		
		
		
		if(date('d-m-Y',strtotime($row[EX_FACTORY_DATE]))==date('d-m-Y',strtotime($previous_date))){
			$deliveryDataArr[$row[COMPANY_ID]][$row[BUYER_NAME]][EX_FACTORY_QTY]+=$row[EX_FACTORY_QNTY];
			$deliveryDataArr[$row[COMPANY_ID]][$row[BUYER_NAME]][EX_FACTORY_VAL]+=$row[EX_FACTORY_QNTY]*$rate;
			$deliveryDataArr[$row[COMPANY_ID]][$row[BUYER_NAME]][STYLE_REF_NO][$row[STYLE_REF_NO]]=$row[STYLE_REF_NO];
			$deliveryDataArr[$row[COMPANY_ID]][$row[BUYER_NAME]][JOB_NO][$row[JOB_NO]]=$row[JOB_NO];
			$deliveryDataArr[$row[COMPANY_ID]][$row[BUYER_NAME]][PO_NUMBER][$row[PO_NUMBER]]=$row[PO_NUMBER];
			$deliveryDataArr[$row[COMPANY_ID]][$row[BUYER_NAME]][PO_ID][$row[PO_ID]]=$row[PO_ID];
			
			
			$deliveryDataArr[$row[COMPANY_ID]][$row[BUYER_NAME]][SYS_NUMBER][$row[SYS_NUMBER_PREFIX_NUM]]=$row[SYS_NUMBER_PREFIX_NUM];
			$deliveryDataArr[$row[COMPANY_ID]][$row[BUYER_NAME]][INVOICE_NO][$row[INVOICE_NO]]=$row[INVOICE_NO];
		
			$total_exfactory_qty_arr[$row[COMPANY_ID]]+=$row[EX_FACTORY_QNTY];
			$total_exfactory_val_arr[$row[COMPANY_ID]]+=$row[EX_FACTORY_QNTY]*$rate;
		
			if($row[INVOICE_NO]){$invoice_id_array[$row[INVOICE_NO]]=$row[INVOICE_NO];}
		
		}
		else{
			$companyPrevPoArr[$row[COMPANY_ID]][$row[PO_ID]]=$row[PO_ID];
			if($row[INVOICE_NO]){$prev_invoice_id_array[$row[INVOICE_NO]]=$row[INVOICE_NO];}
		}
		
		$grand_total_exfactory_qty_arr[$row[COMPANY_ID]]+=$row[EX_FACTORY_QNTY];
		$grand_total_exfactory_val_arr[$row[COMPANY_ID]]+=$row[EX_FACTORY_QNTY]*$rate;
		
		
		
	}


//............................................................Delivery Part end;




//Invoice part start...............................................................
	//$sqlEx = sql_select("select id,INVOICE_NO,NET_INVO_VALUE from com_export_invoice_ship_mst where status_active=1 ".where_con_using_array($invoice_id_array,0,'id')."");
	
	//$sqlEx = sql_select("select a.ID,a.INVOICE_NO,b.PO_BREAKDOWN_ID,((a.NET_INVO_VALUE/a.INVOICE_VALUE) * b.CURRENT_INVOICE_VALUE) as NET_INVO_VALUE from COM_EXPORT_INVOICE_SHIP_MST a,COM_EXPORT_INVOICE_SHIP_DTLS b where a.id=b.mst_id  and a.status_active=1 and b.status_active=1 ".where_con_using_array($invoice_id_array,0,'a.id')."");
	
	$sqlEx = sql_select("select a.ID,a.BENIFICIARY_ID,a.BUYER_ID,a.INVOICE_NO,b.PO_BREAKDOWN_ID,sum((a.NET_INVO_VALUE/a.INVOICE_VALUE) * b.CURRENT_INVOICE_VALUE) as NET_INVO_VALUE,sum(b.CURRENT_INVOICE_VALUE) as CURRENT_INVOICE_VALUE from COM_EXPORT_INVOICE_SHIP_MST a,COM_EXPORT_INVOICE_SHIP_DTLS b where a.id=b.mst_id  and a.status_active=1 and b.status_active=1 ".where_con_using_array($invoice_id_array,0,'a.id')." group by a.ID,a.BENIFICIARY_ID,a.BUYER_ID,a.INVOICE_NO,b.PO_BREAKDOWN_ID");
	
	foreach($sqlEx as $row)
	{
		$invoice_data_arr[$row[ID]]=$row[INVOICE_NO];
		$net_invo_val_arr[$row[PO_BREAKDOWN_ID]]+=$row[NET_INVO_VALUE];
		
		$current_inv_val_arr[$row[BENIFICIARY_ID]][$row[BUYER_ID]]+=$row[CURRENT_INVOICE_VALUE];
		
	}
	
	
	$sqlExPrev = sql_select("select a.ID,a.INVOICE_NO,b.PO_BREAKDOWN_ID,((a.NET_INVO_VALUE/a.INVOICE_VALUE) * b.CURRENT_INVOICE_VALUE) as NET_INVO_VALUE from COM_EXPORT_INVOICE_SHIP_MST a,COM_EXPORT_INVOICE_SHIP_DTLS b where a.id=b.mst_id  and a.status_active=1 and b.status_active=1 ".where_con_using_array($prev_invoice_id_array,0,'a.id')."");
	foreach($sqlExPrev as $row)
	{
		$prev_net_invo_val_arr[$row[PO_BREAKDOWN_ID]]+=$row[NET_INVO_VALUE];
	}

 //............................................................Invoice end;


 $recipient_number_arr=getSMSRecipient(array(item=>2));
 



foreach($company_lib as $company_id=>$company_name){
	
	$smsBody="SMS Generated On :".date('d-M-Y h:i:s A',time())."\n";	
	$smsBody.="".$company_name." "."\n";	
	$smsBody.="Export Information Date :".$previous_date."\n\n";	
	
	$smsBody_short="SMS Generated On :".date('d-M-Y h:i:s A',time())."\n";	
	$smsBody_short.="".$company_name." "."\n";	
	$smsBody_short.="Export Information Date :".$previous_date."\n\n";	
	$smsBody_short.="[Char.  Exceed]\n\n";	
	
	
	
	
	$rows[INVOICE_NO]=array();$rows[NET_INVOICE_VAL]=array();
	foreach($deliveryDataArr[$company_id] as $buyer_id=>$rows){
		
		
		foreach($rows[INVOICE_NO] as $invid){
			$rows[INVOICE_NO][$invid]=$invoice_data_arr[$invid];
		}
		
		
		foreach($rows[PO_ID] as $PO_ID){
			$rows[NET_INVOICE_VAL][$PO_ID]=$net_invo_val_arr[$PO_ID];
		}
	
	
	
		
		$smsBody.="Buyer : ".$buyer_lib[$buyer_id]."\n";	
		$smsBody.="Style : ".implode(',',$rows[STYLE_REF_NO])."\n";	
		$smsBody.="Job No : ".implode(',',$rows[JOB_NO])."\n";
		
		$PO_NUMBER=implode(',',$rows[PO_NUMBER]);
		$PO_NUMBER=(strlen($PO_NUMBER)>140)?' Char.  Exceed':$PO_NUMBER;		
			
		$smsBody.="Po No : ".$PO_NUMBER."\n";
		$smsBody.="Po Count : ".count($rows[PO_NUMBER])."\n";
		
		
		$rows[INVOICE_NO]=implode(',',$rows[INVOICE_NO]);
		$rows[INVOICE_NO]=(strlen($rows[INVOICE_NO])>140)?' Char.  Exceed':$rows[INVOICE_NO];		
		$smsBody.="Exp.Inv : ".$rows[INVOICE_NO]."\n";
		
		$smsBody.="Challan No : ".implode(',',$rows[SYS_NUMBER])."\n";	
		$smsBody.="Qnty [Pcs] : ".number_format($rows[EX_FACTORY_QTY])."\n";	
		$smsBody.="Gross Value : $ ".number_format($rows[EX_FACTORY_VAL],2)."\n";	
		
		$smsBody.="Net Value : $ ".number_format(array_sum($rows[NET_INVOICE_VAL]),2)."\n";	
		//$smsBody.="Net Value :  "."\n";	
		
		$BuyerCommisionPer=(($rows[EX_FACTORY_VAL]-array_sum($rows[NET_INVOICE_VAL])) /$rows[EX_FACTORY_VAL]) * 100;
		$smsBody.="Buyer Commision/Discount: ".number_format($BuyerCommisionPer,2)." % \n\n";
		//$smsBody.="Buyer Commision/Discount: "."  \n\n";
		
			
		$total_net_value[$company_id]+=array_sum($rows[NET_INVOICE_VAL]);
	}
	
	
	foreach($companyPrevPoArr[$company_id] as $poID){
		$prev_total_net_value[$company_id]+=$prev_net_invo_val_arr[$poID];
	}
	
	
	if($total_exfactory_qty_arr[$company_id]){
		$smsBody.="Grand Total Qnty [Pcs] : ".number_format($total_exfactory_qty_arr[$company_id])."\n";	
		$smsBody.="Grand Total Gross Value : $ ".number_format($total_exfactory_val_arr[$company_id],2)."\n";
		$smsBody.="Grand Total Net Value : $ ".number_format($total_net_value[$company_id],2)."\n\n\n";
		//$smsBody.="Grand Total Net Value :  "."\n\n";
		
		
		$smsBody_short.="Grand Total Qnty [Pcs] : ".number_format($total_exfactory_qty_arr[$company_id])."\n";	
		$smsBody_short.="Grand Total Gross Value : $ ".number_format($total_exfactory_val_arr[$company_id],2)."\n";
		$smsBody_short.="Grand Total Net Value : $ ".number_format($total_net_value[$company_id],2)."\n\n\n";
		//$smsBody_short.="Grand Total Net Value :  "."\n";
	}
		
	$smsBody.="---------------------\n";	
	$smsBody.="Grand Total Export from $satart_date to $previous_date\n";	
	$smsBody.="Export Qnty [Pcs] : ".number_format($grand_total_exfactory_qty_arr[$company_id])."\n";	
	$smsBody.="Export Gross Value : $ ".number_format($grand_total_exfactory_val_arr[$company_id],2)."\n";	
	$smsBody.="Net Value : $ ".number_format($prev_total_net_value[$company_id]+$total_net_value[$company_id],2)."\n\n\n";	
	//$smsBody.="Net Value :  "."\n\n\n";	

	$smsBody_short.="---------------------\n";	
	$smsBody_short.="Grand Total Export from $satart_date to $previous_date\n";	
	$smsBody_short.="Export Qnty [Pcs] : ".number_format($grand_total_exfactory_qty_arr[$company_id])."\n";	
	$smsBody_short.="Export Gross Value : $ ".number_format($grand_total_exfactory_val_arr[$company_id],2)."\n";	
	$smsBody_short.="Net Value : $ ".number_format($prev_total_net_value[$company_id]+$total_net_value[$company_id],2)."\n\n\n";	
	
	//echo $smsBody;
	
	$mobile_number_arr=array();
	foreach($recipient_number_arr[$company_id] as $buyerRows){
		foreach($buyerRows as $brandRows){
			foreach($brandRows as $number){
				$mobile_number_arr[]=$number;
			}
		}
	}
	
	if(count($mobile_number_arr[$company_id])){
		if(count(str_split($smsBody))>1000){
			$smsBody=$smsBody_short;
		}
		
		//sendSMS(array('01975643095'),$smsBody);//
		//sendSMS($mobile_number_arr,$smsBody);//array('01511100004,01709632668,01975643095')
	}


	echo "<pre>".$smsBody."</pre>";



}//end 

 

 
 //echo sendSMS(array('01975643095'),'test sms');


?>
