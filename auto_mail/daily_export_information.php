<?// Developed by: sojib

date_default_timezone_set("Asia/Dhaka");
require_once('../includes/common.php');
// require_once('../mailer/class.phpmailer.php');
require_once('setting/mail_setting.php');

$strtotime = ($_REQUEST['view_date'])?strtotime($_REQUEST['view_date']):time();
$current_date = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("H:i:s", $strtotime),0))),'','',1);	
$prev_date = change_date_format(date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))),'','',1); 	
$first_date = change_date_format(date('Y-m-1 H:i:s', strtotime('-1 day', strtotime($current_date))),'','',1); 	

$is_all_company=1;

$company_lib=return_library_array("select id,company_short_name from lib_company where status_active=1 and is_deleted=0 ","id","company_short_name");// and CORE_BUSINESS=1
$del_company_lib=return_library_array("select id,company_short_name from lib_company where status_active=1 and is_deleted=0 ","id","company_short_name");// and CORE_BUSINESS=1
$buyer_lib=return_library_array("select id,short_name from lib_buyer where status_active=1 and is_deleted=0","id","short_name");
$factory_merchand=return_library_array("select a.id, a.team_member_name from lib_mkt_team_member_info a, lib_marketing_team b where a.team_id=b.id and b.team_type in (2) and a.status_active =1 and a.is_deleted=0 order by a.team_member_name","id","team_member_name");



	$where_con=" and a.ex_factory_date between '$first_date' and '$prev_date'";
	$sql="SELECT A.PO_BREAK_DOWN_ID,A.EX_FACTORY_DATE,A.EX_FACTORY_QNTY,c.INVOICE_NO,B.SYS_NUMBER as CHALLAN_NO,b.BUYER_ID,b.COMPANY_ID,b.DELIVERY_COMPANY_ID from  pro_ex_factory_mst a,  pro_ex_factory_delivery_mst b,COM_EXPORT_INVOICE_SHIP_MST c where a.delivery_mst_id=b.id and c.id=a.INVOICE_NO and b.entry_form!=85 and a.status_active=1 and c.status_active=1 and a.is_deleted=0  and c.is_deleted=0 $where_con ";
	//echo $sql;
	$delivery_sql_res=sql_select($sql);
	$dataArr=array();
	$orderArr=array();$grandDataArr=array();
	foreach($delivery_sql_res as $row){
		if(date('d-m-Y',strtotime($row[EX_FACTORY_DATE]))== date('d-m-Y',strtotime($prev_date))){
			$dataArr[$row['COMPANY_ID']][$row['BUYER_ID']][$row['PO_BREAK_DOWN_ID']]['EX_FACTORY_QNTY']+=$row[EX_FACTORY_QNTY];
			$dataArr[$row['COMPANY_ID']][$row['BUYER_ID']][$row['PO_BREAK_DOWN_ID']]['CHALLAN_NO']=$row['CHALLAN_NO'];
			$dataArr[$row['COMPANY_ID']][$row['BUYER_ID']][$row['PO_BREAK_DOWN_ID']]['INVOICE_NO']=$row['INVOICE_NO'];
			$dataArr[$row['COMPANY_ID']][$row['BUYER_ID']][$row['PO_BREAK_DOWN_ID']]['DELIVERY_COMPANY_ID']=$row['DELIVERY_COMPANY_ID'];
			$dataArr[$row['COMPANY_ID']][$row['BUYER_ID']][$row['PO_BREAK_DOWN_ID']]['EX_FACTORY_DATE']=$row[EX_FACTORY_DATE];
			$orderArr[$row['PO_BREAK_DOWN_ID']]=$row['PO_BREAK_DOWN_ID'];
		}
		$grandDataArr[$row['COMPANY_ID']]['EX_FACTORY_QNTY']+=$row['EX_FACTORY_QNTY'];
		$allOrderArr[$row['PO_BREAK_DOWN_ID']]=$row['PO_BREAK_DOWN_ID'];
		$companyByPoArr[$row['PO_BREAK_DOWN_ID']]=$row['COMPANY_ID'];
	
	}

// 	echo "<pre>";
// print_r($dataArr); 
//   echo "</pre>";die();
	
	
	
	$sql="select A.BUYER_NAME as BUYER_ID,A.JOB_NO,B.ID AS PO_ID,B.PO_NUMBER,B.PUB_SHIPMENT_DATE,B.SHIPMENT_DATE,B.UNIT_PRICE,B.PO_TOTAL_PRICE,A.SHIP_MODE, A.TOTAL_SET_QNTY ,B.PO_QUANTITY,A.ORDER_UOM,A.STYLE_REF_NO,A.FACTORY_MARCHANT,C.PLAN_CUT_QNTY from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and b.id=c.PO_BREAK_DOWN_ID  and b.status_active=1 and  a.status_active=1 and b.status_active=1 ".where_con_using_array($orderArr,0,'B.ID')."";
	//echo $sql;
	$order_sql_res=sql_select($sql);
	$poDataArr=array();
	foreach($order_sql_res as $row){
		$poDataArr[$row['PO_ID']]['PO_QUANTITY']=$row['PO_QUANTITY'];
		$poDataArr[$row['PO_ID']]['PLAN_CUT_QNTY']+=$row['PLAN_CUT_QNTY'];
		$poDataArr[$row['PO_ID']]['PO_QTY_PCS']=($row['TOTAL_SET_QNTY']*$row['PO_QUANTITY']);
		$poDataArr[$row['PO_ID']]['UNIT_PRICE']=$row['UNIT_PRICE'];
		$poDataArr[$row['PO_ID']]['SHIPMENT_DATE']=$row['SHIPMENT_DATE'];
		$poDataArr[$row['PO_ID']]['PO_NUMBER']=$row['PO_NUMBER'];
		$poDataArr[$row['PO_ID']]['SHIP_MODE']=$row['SHIP_MODE'];
		$poDataArr[$row['PO_ID']]['PO_TOTAL_PRICE']=$row['PO_TOTAL_PRICE'];
		$poDataArr[$row['PO_ID']]['PO_TOTAL_PRICE']=$row['PO_TOTAL_PRICE'];
		$poDataArr[$row['PO_ID']]['SHIP_MODE']=$row['SHIP_MODE'];
		$poDataArr[$row['PO_ID']]['JOB_NO']=$row['JOB_NO'];
		$poDataArr[$row['PO_ID']]['PO_NUMBER']=$row['PO_NUMBER'];
		$poDataArr[$row['PO_ID']]['SHIPMENT_DATE']=$row['SHIPMENT_DATE'];
		$poDataArr[$row['PO_ID']]['STYLE_REF_NO']=$row['STYLE_REF_NO'];
		$poDataArr[$row['PO_ID']]['ORDER_UOM']=$row['ORDER_UOM'];
		$poDataArr[$row['PO_ID']]['FACTORY_MARCHANT']=$row['FACTORY_MARCHANT'];
	}
// 	echo "<pre>";
// print_r($poDataArr); 
//   echo "</pre>";die();
$sql= "select b.BOOKING_NO,b.PO_BREAK_DOWN_ID as PO_ID  from WO_BOOKING_MST a,WO_BOOKING_DTLS b  where a.id=b.BOOKING_MST_ID and b.status_active=1 and  b.is_deleted=0 and a.status_active=1 and  a.is_deleted=0 and a.FABRIC_SOURCE=1 ".where_con_using_array($orderArr,0,'b.po_break_down_id')."";
 //echo $sql;die();
$bookiong_sql_res=sql_select($sql);
//print_r($bookiong_sql_res);
	$poDataArr2=array();
	foreach($bookiong_sql_res as $row){
		$poDataArr2[$row['PO_ID']]['BOOKING_NO'][$row['BOOKING_NO']]=$row['BOOKING_NO'];
		
	}
// 		 echo "<pre>";
// 		print_r($poDataArr2); 
//   echo "</pre>";die();






	$sql="select a.po_break_down_id AS PO_ID,a.SERVING_COMPANY,a.production_type,
	(CASE WHEN a.production_type=5 THEN b.production_qnty ELSE 0 END) AS OUTPUT_QTY ,
	(CASE WHEN a.production_type=8 THEN b.production_qnty ELSE 0 END) AS FINISH_QTY,
	(CASE WHEN a.production_type=1 THEN b.production_qnty ELSE 0 END) AS CUT_QTY
	from pro_garments_production_mst a, pro_garments_production_dtls b where a.id=b.mst_id  and a.production_type in(1,5,8) and a.status_active=1 ".where_con_using_array($orderArr,0,'a.po_break_down_id')." and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	//echo $sql;
	$pro_sql_res=sql_select($sql);
	 $proDataArr=array();
	 foreach($pro_sql_res as $row){
		$proDataArr[$row[PO_ID]]['OUTPUT_QTY']+=$row[OUTPUT_QTY];
		$proDataArr[$row[PO_ID]]['FINISH_QTY']+=$row[FINISH_QTY];
		$proDataArr[$row[PO_ID]]['CUT_QTY']+=$row[CUT_QTY];
		$proDataArr[$row[PO_ID]]['SERVING_COMPANY']=$row['SERVING_COMPANY'];
		$pro_data_arr[$row[PO_ID]][$row[csf('production_type')]]['SERVING_COMPANY']=$row['SERVING_COMPANY'];
		
	 }
// 	 echo "<pre>";
// 		print_r($pro_data_arr); 
//   echo "</pre>";die();
	 foreach($proDataArr as $poId=>$val){
		$str=$del_company_lib[$pro_data_arr[$poId][1]['SERVING_COMPANY']]."-".$val['CUT_QTY'];
		$proDataArr[$poId]['SER_COMPANY_CUT_QTY']=$str;
         
		$str2=$del_company_lib[$pro_data_arr[$poId][5]['SERVING_COMPANY']]."-".$val['OUTPUT_QTY'];
		//$proDataArr[$poId]['SER_COMPANY_SEW_QTY']=$str2;

		$str3=$del_company_lib[$pro_data_arr[$poId][8]['SERVING_COMPANY']]."-".$val['FINISH_QTY'];
		//proDataArr[$poId]['SER_COMPANY_FIN_QTY']=$str3;

	 }
	//  echo "<pre>";
	//  		print_r($proDataArr); 
	//    echo "</pre>";die();
	$sql = "SELECT a.order_id AS PO_ID,c.cutting_no,sum(a.size_qty) as CUTT_QTY,c.WORKING_COMPANY_ID 
	from ppl_cut_lay_bundle a, ppl_cut_lay_dtls b,ppl_cut_lay_mst c
	 where b.id=a.dtls_id and c.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 
	 and b.is_deleted=0  ".where_con_using_array($orderArr,1,'a.order_id')."group by a.order_id,c.cutting_no,c.WORKING_COMPANY_ID  ";

// 	SELECT a.order_id AS PO_ID,c.cutting_no,sum(a.size_qty) as CUTT_QTY,c.WORKING_COMPANY_ID 
// from ppl_cut_lay_bundle a, ppl_cut_lay_dtls b,ppl_cut_lay_mst c
//  where b.id=a.dtls_id and c.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 
//  and b.is_deleted=0 and (a.order_id in('72581')) group by a.order_id,c.cutting_no,c.WORKING_COMPANY_ID 

  //echo $sql;die();
  
  $pro_sql_res=sql_select($sql);
  $proDataArr=array();
  $temp_company_qty=array();
  foreach($pro_sql_res as $row){
	 $proDataArr[$row['PO_ID']]['COM_CUTT_QTY']+=$row['CUTT_QTY'];
	 $temp_company_qty[$row['PO_ID']][$row['WORKING_COMPANY_ID']]+=$row['CUTT_QTY'];

	 $proDataArr[$row['PO_ID']]['SER_COMPANY_CUT_QTY'][$row['WORKING_COMPANY_ID']]=$del_company_lib[$row['WORKING_COMPANY_ID']]."-".$temp_company_qty[$row['PO_ID']][$row['WORKING_COMPANY_ID']];

	 
  }

	$sql = "select id, embel_name,po_break_down_id as PO_ID,SERVING_COMPANY, PRODUCTION_QUANTITY from pro_garments_production_mst where production_type='3' and embel_name='1' and status_active=1 and is_deleted=0 ".where_con_using_array($orderArr,0,'po_break_down_id')." order by id  ";
	//echo $sql;die;

	$pri_sql_res=sql_select($sql);
	$temp_company_qty=array();
	foreach($pri_sql_res as $row){

		$temp_company_qty[$row['PO_ID']][$row['SERVING_COMPANY']]+=$row['PRODUCTION_QUANTITY'];

	   $proDataArr[$row['PO_ID']]['SER_COMPANY_PRINT_QTY'][$row['SERVING_COMPANY']] = $del_company_lib[$row['SERVING_COMPANY']]."-".$temp_company_qty[$row['PO_ID']][$row['SERVING_COMPANY']];
	   $proDataArr[$row['PO_ID']]['COM_PRINT_QTY']+=$row['PRODUCTION_QUANTITY'];

	   
	}


	$sql = "select id, embel_name,po_break_down_id as PO_ID,SERVING_COMPANY, PRODUCTION_QUANTITY from pro_garments_production_mst where production_type='3' and embel_name='2' and status_active=1 and is_deleted=0 ".where_con_using_array($orderArr,1,'po_break_down_id')." order by id  ";

	$pri_sql_res=sql_select($sql);
	//$proDataArr=array();
	$temp_company_qty=array();
	foreach($pri_sql_res as $row){
  
  
		$temp_company_qty[$row['PO_ID']][$row['WORKING_COMPANY_ID']]+=$row['PRODUCTION_QUANTITY'];

	   $proDataArr[$row['PO_ID']]['SER_COMPANY_EMB_QTY'][$row['SERVING_COMPANY']]=$del_company_lib[$row['SERVING_COMPANY']]."-".$temp_company_qty[$row['PO_ID']][$row['WORKING_COMPANY_ID']];
	   $proDataArr[$row['PO_ID']]['COM_EMB_QTY']+=$row['PRODUCTION_QUANTITY'];
	   
	}

	  $sql ="SELECT a.id, a.po_break_down_id as PO_ID , a.serving_company, sum(b.production_qnty) as production_quantity from pro_garments_production_mst a , pro_garments_production_dtls b where  a.production_type='5' and  a.id=b.mst_id and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 ".where_con_using_array($orderArr,1,'a.po_break_down_id')." group by a.id, a.po_break_down_id,a.serving_company,a.production_quantity ";
    // echo  $sql;die();
	  $pri_sql_res=sql_select($sql);
	  $temp_company_qty=array();
	foreach($pri_sql_res as $row){
  
  
	   //$proDataArr5[$row[PO_ID]][$row[SERVING_COMPANY]]['COM_SEW_QTY']+=$row[PRODUCTION_QUANTITY];

	   $temp_company_qty[$row['PO_ID']][$row['SERVING_COMPANY']]+=$row['PRODUCTION_QUANTITY'];
	   $proDataArr[$row[PO_ID]]['SER_COMPANY_SEW_QTY'][$row['SERVING_COMPANY']]=$del_company_lib[$row['SERVING_COMPANY']]."-".$temp_company_qty[$row['PO_ID']][$row['SERVING_COMPANY']];
	   $proDataArr[$row[PO_ID]]['COM_SEW_QTY']+=$row['PRODUCTION_QUANTITY'];

	   
	}


	  $sql= "SELECT id,po_break_down_id as PO_ID ,PRODUCTION_QUANTITY,serving_company,wo_order_no  from pro_garments_production_mst where  production_type='8' and status_active=1 and is_deleted=0 ".where_con_using_array($orderArr,1,'po_break_down_id')." order by id";
	  
  // echo $sql;die();
  $pri_sql_res=sql_select($sql);
  $temp_company_qty=array();
	foreach($pri_sql_res as $row){
  
  
		$temp_company_qty[$row['PO_ID']][$row['SERVING_COMPANY']]+=$row['PRODUCTION_QUANTITY'];

	   $proDataArr[$row['PO_ID']]['SER_COMPANY_FIN_QTY'][$row['SERVING_COMPANY']]=$del_company_lib[$row['SERVING_COMPANY']]."-".$temp_company_qty[$row['PO_ID']][$row['SERVING_COMPANY']];
	   $proDataArr[$row['PO_ID']]['COM_FIN_QTY']+=$row['PRODUCTION_QUANTITY'];

	   
	}
	
    


	$where_con1=" and a.ex_factory_date = '$prev_date'";
	$sql="SELECT a.id,a.PO_BREAK_DOWN_ID as PO_ID,b.DELIVERY_COMPANY_ID,a.EX_FACTORY_QNTY	from pro_ex_factory_mst a,pro_ex_factory_delivery_mst b where a.DELIVERY_MST_ID=b.ID and a.status_active=1  and a.is_deleted=0 and b.status_active=1  and b.is_deleted=0 $where_con1 ".where_con_using_array($orderArr,1,'a.PO_BREAK_DOWN_ID')." ";
	//echo $sql;die();

	$ex_sql_res=sql_select($sql);
	$temp_company_qty=array();
	foreach($ex_sql_res as $row){

		$temp_company_qty[$row['PO_ID']][$row['DELIVERY_COMPANY_ID']]+=$row['EX_FACTORY_QNTY'];

	   $proDataArr[$row['PO_ID']]['SER_COMPANY_EX_QTY'][$row['DELIVERY_COMPANY_ID']]=$del_company_lib[$row['DELIVERY_COMPANY_ID']]."-".$temp_company_qty[$row['PO_ID']][$row['DELIVERY_COMPANY_ID']];
	   $proDataArr[$row['PO_ID']]['EX_FACTORY_QNTY']+=$row['EX_FACTORY_QNTY'];

	   
	}



		$cha_sql="SELECT a.PO_BREAK_DOWN_ID ,B.SYS_NUMBER	from pro_ex_factory_mst a,pro_ex_factory_delivery_mst b where a.DELIVERY_MST_ID=b.ID and a.status_active=1  and a.is_deleted=0 and b.status_active=1  and b.is_deleted=0 $where_con1 ".where_con_using_array($orderArr,1,'a.PO_BREAK_DOWN_ID')." ";
	//echo $cha_sql;die();

	$ch_sql_res=sql_select($cha_sql);
	//$proDataArr=array();
	foreach($ch_sql_res as $row){
  
	   $proDataArr8[$row['PO_BREAK_DOWN_ID']]['SYS_NUMBER'].=$row['SYS_NUMBER'].',';
	}


	 $sql = "select a.ENTRY_FORM,a.BUYER_PO_IDS as PO_ID,a.SERV_COMPANY,a.COMPANY_ID,b.QCPASS_QTY from SUBCON_EMBEL_PRODUCTION_MST a, SUBCON_EMBEL_PRODUCTION_dtls b where a.id=b.mst_id and a.IS_DELETED=0 and b.IS_DELETED=0 and a.STATUS_ACTIVE=1 and b.STATUS_ACTIVE=1 and a.ENTRY_FORM in(222,315) ".where_con_using_array($orderArr,1,'a.BUYER_PO_IDS')."";
	 //echo $sql;die();
	$emb_sql_res=sql_select($sql);
	 foreach($emb_sql_res as $row){
		if($row['ENTRY_FORM']==222){$proDataArr[$row['PO_ID']]['PRINT_QTY']+=$row['QCPASS_QTY'];
			$proDataArr[$row['PO_ID']]['COMPANY_ID']=$row['COMPANY_ID'];
		}
		elseif($row['ENTRY_FORM']==315){$proDataArr[$row['PO_ID']]['EMB_QTY']+=$row['QCPASS_QTY'];
		$proDataArr[$row['PO_ID']]['COMPANY_ID']=$row['COMPANY_ID'];        
		}
	 }

	 
     
			 

	$sql="select B.PO_BREAKDOWN_ID as PO_ID,((a.NET_INVO_VALUE/a.INVOICE_VALUE) * b.CURRENT_INVOICE_VALUE) AS PO_NET_INVO_VALUE,b.CURRENT_INVOICE_VALUE as EXPORT_INVOICE_VAL,A.DISCOUNT_AMMOUNT  from COM_EXPORT_INVOICE_SHIP_MST a ,COM_EXPORT_INVOICE_SHIP_dtls b where a.id=b.MST_ID ".where_con_using_array($orderArr,0,'b.PO_BREAKDOWN_ID')."";
	$net_export_val_result=sql_select($sql);	
	foreach($net_export_val_result as $row){
		$proDataArr[$row['PO_ID']]['PO_NET_INVO_VALUE']+=$row['PO_NET_INVO_VALUE'];
		$proDataArr[$row['PO_ID']]['EXPORT_INVOICE_VAL']+=$row['EXPORT_INVOICE_VAL'];
		$proDataArr[$row['PO_ID']]['DISCOUNT_AMMOUNT']=($row['EXPORT_INVOICE_VAL']-$row['PO_NET_INVO_VALUE']);
	}


	
	$sql="select B.PO_BREAKDOWN_ID as PO_ID,((a.NET_INVO_VALUE/a.INVOICE_VALUE) * b.CURRENT_INVOICE_VALUE) AS PO_NET_INVO_VALUE,b.CURRENT_INVOICE_VALUE as EXPORT_INVOICE_VAL,A.DISCOUNT_AMMOUNT  from COM_EXPORT_INVOICE_SHIP_MST a ,COM_EXPORT_INVOICE_SHIP_dtls b where a.id=b.MST_ID ".where_con_using_array($allOrderArr,0,'b.PO_BREAKDOWN_ID')."";
	$net_export_val_result=sql_select($sql);	
	foreach($net_export_val_result as $row){
		$row['COMPANY_ID']=$companyByPoArr[$row['PO_ID']];
		$grandDataArr[$row['COMPANY_ID']]['PO_NET_INVO_VALUE']+=$row['PO_NET_INVO_VALUE'];
		$grandDataArr[$row['COMPANY_ID']]['EXPORT_INVOICE_VAL']+=$row['EXPORT_INVOICE_VAL'];
	}
	
	



//$compani_id=17;
foreach($company_lib as $compani_id=>$comapny_name){
	ob_start();	
?>


<table border="1" rules="all" width="2500">
    <caption>
        <b><?=$company_lib[$compani_id];?></b><br />
        Daily Ex-factory of Date : <?=$prev_date;?>
    </caption>
    <thead>
        <tr bgcolor="#CCCCCC">
            <th>SL</th>
            <th>Buyer </th>
            <th>Style </th>
            <th>Job No </th>
            <th>Fabric Booking </th>
            <th>PO/Order</th>
            <th>Original Ship Date</th>
            <th>Ex-Factory Date</th>
            <th>Export Invoice No </th>
            <th>Challan No </th>
            <th>Ex-Factory Delivery Company </th>
            <th>Ex-Factory Qty (Pcs)</th>
            <th>Balance Ex-fac. Qty (Pcs)</th>
            <th>UOM </th>
            <th>Order Qty</th>
            <th>Order Qty (Pcs) </th>
            <th>Order FOB $ </th>
            <th>Gross Value $</th>
            <th>Export Invoice Value $</th>
            <th>Net Value $</th>
            <th>Commision Discount</th>
            <th>Ship Mode</th>
            <th>Plan Cut Order Qty (Pcs)</th>
            <th>Cutting Company</th>
            <th>Cutting Qty (Pcs)</th>
            <th>Balance </th>
            <th>Printing Company </th>
            <th>Print Qty (Pcs)</th>
            <th>Balance</th>
            <th>Embroidery Company</th>
            <th>Embroidery Qty (Pcs)</th>
            <th>Balance</th>
            <th>Sewing Company</th>
            <th>Sewing Qty (Pcs)</th>
            <th>Balance</th>
            <th>Finishing Company</th>
            <th>Finishing Qty (Pcs)</th>
            <th>Balance</th>
            <th>Factory Merchandiser</th>
        </tr>
    </thead>

    
    <tbody>
    <? 
	$i=1;
	$sumDataArr=array();
	foreach($dataArr[$compani_id] as $buyer_id=>$buyerRows){
	foreach($buyerRows as $po_id=>$rows){
		
		$sumDataArr[EX_FACTORY_QNTY][$buyer_id]+=$proDataArr[$po_id][EX_FACTORY_QNTY];
		
		$sumDataArr[PO_QUANTITY][$buyer_id]+=$poDataArr[$po_id][PO_QUANTITY];
		$sumDataArr[PO_QTY_PCS][$buyer_id]+=$poDataArr[$po_id][PO_QTY_PCS];
		$sumDataArr[GROSS_VAL][$buyer_id]+=$poDataArr[$po_id][PO_TOTAL_PRICE];
		$sumDataArr[EXPORT_INVOICE_VAL][$buyer_id]+=$proDataArr[$po_id][EXPORT_INVOICE_VAL];
		$sumDataArr[PO_NET_INVO_VALUE][$buyer_id]+=$proDataArr[$po_id][PO_NET_INVO_VALUE];
		$sumDataArr[DISCOUNT_AMMOUNT][$buyer_id]+=$proDataArr[$po_id][DISCOUNT_AMMOUNT];
		
		
		$sumDataArr['COM_CUTT_QTY'][$buyer_id]+=$proDataArr[$po_id]['COM_CUTT_QTY'];
		$sumDataArr['COM_PRINT_QTY'][$buyer_id]+=$proDataArr[$po_id]['COM_PRINT_QTY'];
		$sumDataArr['COM_EMB_QTY'][$buyer_id]+=$proDataArr[$po_id]['COM_EMB_QTY'];
		$sumDataArr['COM_FIN_QTY'][$buyer_id]+=$proDataArr[$po_id]['COM_FIN_QTY'];
		$sumDataArr['COM_SEW_QTY'][$buyer_id]+=$proDataArr[$po_id]['COM_SEW_QTY'];
		
		$sumDataArr['BAL_CUT_QTY'][$buyer_id]+=($poDataArr[$po_id]['PO_QTY_PCS']-$proDataArr[$po_id][COM_CUTT_QTY]);
		$sumDataArr['BAL_PRINT_QTY'][$buyer_id]+=($poDataArr[$po_id]['PO_QTY_PCS']-$proDataArr[$po_id][COM_PRINT_QTY]);
		$sumDataArr['BAL_EMB_QTY'][$buyer_id]+=($poDataArr[$po_id]['PO_QTY_PCS']-$proDataArr[$po_id][COM_EMB_QTY]);
		$sumDataArr['BAL_FINISH_QTY'][$buyer_id]+=($poDataArr[$po_id]['PO_QTY_PCS']-$proDataArr[$po_id][COM_FIN_QTY]);
		$sumDataArr['BAL_OUTPUT_QTY'][$buyer_id]+=($poDataArr[$po_id]['PO_QTY_PCS']-$proDataArr[$po_id][COM_SEW_QTY]);
		$sumDataArr['EX_FACTORY_QNTY_BAL'][$buyer_id]+=($poDataArr[$po_id]['PO_QTY_PCS']-$proDataArr[$po_id]['EX_FACTORY_QNTY']);
		
		
		$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";	
		?>
        <tr bgcolor="<? echo $bgcolor; ?>">
            <td title="<?=$po_id;?>" align="center"><?=$i;?></td>
            <td><?=$buyer_lib[$buyer_id];?></td>
            <td><?=$poDataArr[$po_id]['STYLE_REF_NO'];?> </td>
            <td><?=$poDataArr[$po_id]['JOB_NO'];?></td>
            <td><?= implode(',',$poDataArr2[$po_id]['BOOKING_NO']);?></td>
            <td><?=$poDataArr[$po_id]['PO_NUMBER'];?></td>
            <td align="center"><?=change_date_format($poDataArr[$po_id]['SHIPMENT_DATE']);?></td>
            <td align="center"><?=change_date_format($rows['EX_FACTORY_DATE']);?></td>
            <td><?=$rows['INVOICE_NO'];?></td>
            <td><? $challan=rtrim($proDataArr8[$po_id]['SYS_NUMBER'],',');
			
			echo $challan;?></td>
            <td><?= implode(',',$proDataArr[$po_id]['SER_COMPANY_EX_QTY']);?></td>
            <td align="right"><?=number_format($proDataArr[$po_id]['EX_FACTORY_QNTY']);?></td>
            <td align="right"><?=number_format($poDataArr[$po_id]['PO_QTY_PCS']-$proDataArr[$po_id]['EX_FACTORY_QNTY']);?></td>
            <td align="center"><?= $unit_of_measurement[$poDataArr[$po_id]['ORDER_UOM']];?></td>
            <td align="right"><?=$poDataArr[$po_id]['PO_QUANTITY'];?></td>
            <td align="right"><?=$poDataArr[$po_id]['PO_QTY_PCS'];?></td>
            <td align="right"><?=$poDataArr[$po_id]['UNIT_PRICE'];?></td>
            <td align="right"><?=$poDataArr[$po_id]['PO_TOTAL_PRICE'];?></td>
            <td align="right"><?=number_format($proDataArr[$po_id]['EXPORT_INVOICE_VAL'],2);?></td>
            <td align="right"><?=number_format($proDataArr[$po_id]['PO_NET_INVO_VALUE'],2);?></td>
            <td align="right"><?=number_format($proDataArr[$po_id]['DISCOUNT_AMMOUNT'],2);?></td>
            <td align="center"><?= $shipment_mode[$poDataArr[$po_id]['SHIP_MODE']];?></td>
            <td align="center"><?=$poDataArr[$po_id]['PLAN_CUT_QNTY'];?></td>
            <td align="center"><?= implode(',',$proDataArr[$po_id]['SER_COMPANY_CUT_QTY']);?></td>
            <td align="right"><?=number_format($proDataArr[$po_id]['COM_CUTT_QTY']);?></td>
            <td align="right"><?=number_format(($poDataArr[$po_id]['PLAN_CUT_QNTY']-$proDataArr[$po_id]['COM_CUTT_QTY']));?></td>
            <td align="right"><?= implode(',',$proDataArr[$po_id]['SER_COMPANY_PRINT_QTY']);?></td>

            <td align="right"><?=number_format($proDataArr[$po_id]['COM_PRINT_QTY']);?></td>
            <td align="right"><?=number_format(($poDataArr[$po_id]['PO_QTY_PCS']-$proDataArr[$po_id]['COM_PRINT_QTY']));?></td>
			<td align="right"><?= implode(',',$proDataArr[$po_id]['SER_COMPANY_EMB_QTY']);?></td>
            <td align="right"><?=number_format($proDataArr[$po_id]['COM_EMB_QTY']);?></td>
            <td align="right"><?=number_format(($poDataArr[$po_id]['PO_QTY_PCS']-$proDataArr[$po_id]['COM_EMB_QTY']));?></td>
			<td align="right"><?= implode(',',$proDataArr[$po_id]['SER_COMPANY_SEW_QTY']);?></td>
            <td align="right"><?=number_format($proDataArr[$po_id]['COM_SEW_QTY']);?></td>
            <td align="right"><?=number_format(($poDataArr[$po_id]['PO_QTY_PCS']-$proDataArr[$po_id]['COM_SEW_QTY']));?></td>
			<td align="right"><?= implode(',',$proDataArr[$po_id]['SER_COMPANY_FIN_QTY']);?></td>
            <td align="right"><?=number_format($proDataArr[$po_id]['COM_FIN_QTY']);?></td>
            <td align="right"><?=number_format(($poDataArr[$po_id]['PO_QTY_PCS']-$proDataArr[$po_id]['COM_FIN_QTY']));?></td>
            <td><?=$factory_merchand[$poDataArr[$po_id]['FACTORY_MARCHANT']];?></td>
        </tr>
        <?
		$i++;
		}
		?>
        <tr bgcolor="#66CCFF">
            <td colspan="9"><?=$buyer_lib[$buyer_id];?> SubTotal : </td>
			<td></td>
			<td></td>
            <td align="right"><?=number_format($sumDataArr['EX_FACTORY_QNTY'][$buyer_id]);?></td>
            <td align="right"><?=number_format($sumDataArr['EX_FACTORY_QNTY_BAL'][$buyer_id]);?></td>
            <td></td>
            <td align="right"><?=number_format($sumDataArr['PO_QUANTITY'][$buyer_id]);?></td>
            <td align="right"><?=number_format($sumDataArr['PO_QTY_PCS'][$buyer_id]);?></td>
            <td></td>
            <td align="right"><?=number_format($sumDataArr['GROSS_VAL'][$buyer_id],2);?></td>
            <td align="right"><?=number_format($sumDataArr['EXPORT_INVOICE_VAL'][$buyer_id],2);?></td>
            <td align="right"><?=number_format($sumDataArr['PO_NET_INVO_VALUE'][$buyer_id],2);?></td>
            <td align="right"><?=number_format($sumDataArr['DISCOUNT_AMMOUNT'][$buyer_id],2);?></td>
            <td></td>
            <td></td>
            <td></td>
            <td align="right"><?=number_format($sumDataArr['COM_CUTT_QTY'][$buyer_id]);?></td>
            <td align="right"><?=number_format($sumDataArr['BAL_CUT_QTY'][$buyer_id]);?> </td>
			<td></td>
            <td align="right"><?=number_format($sumDataArr['COM_PRINT_QTY'][$buyer_id]);?></td>
            <td align="right"><?=number_format($sumDataArr['BAL_PRINT_QTY'][$buyer_id]);?></td>
			<td></td>
            <td align="right"><?=number_format($sumDataArr['COM_EMB_QTY'][$buyer_id]);?></td>
            <td align="right"><?=number_format($sumDataArr['BAL_EMB_QTY'][$buyer_id]);?></td>
			<td></td>
            <td align="right"><?=number_format($sumDataArr['COM_SEW_QTY'][$buyer_id]);?></td>
            <td align="right"><?=number_format($sumDataArr['BAL_OUTPUT_QTY'][$buyer_id]);?></td>
			<td></td>
            <td align="right"><?=number_format($sumDataArr['COM_FIN_QTY'][$buyer_id]);?></td>
            <td align="right"><?=number_format($sumDataArr['BAL_FINISH_QTY'][$buyer_id]);?></td>
            <td></td>
            
        </tr>
        <?
		}
		?>
    </tbody>
    <tfoot bgcolor="#DDD">
        <th colspan="9" align="right">Grand Total : </th>
		<th></th>
		<th></th>
        <th align="right"><?=number_format(array_sum($sumDataArr['EX_FACTORY_QNTY']));?></th>
        <th align="right"><?=number_format(array_sum($sumDataArr['EX_FACTORY_QNTY_BAL']));?></th>
        <th></th>
        <th align="right"><?=number_format(array_sum($sumDataArr['PO_QUANTITY']));?></th>
        <th align="right"><?=number_format(array_sum($sumDataArr['PO_QTY_PCS']));?></th>
        <th></th>
        <th align="right"><?=number_format(array_sum($sumDataArr['GROSS_VAL']));?></th>
        <th align="right"><?=number_format(array_sum($sumDataArr['EXPORT_INVOICE_VAL']),2);?></th>
        <th align="right"><?=number_format(array_sum($sumDataArr['PO_NET_INVO_VALUE']),2);?></th>
        <th align="right"><?=number_format(array_sum($sumDataArr['DISCOUNT_AMMOUNT']),2);?></th>
        <th></th>
        <th></th>
        <th></th>
        <th align="right"><?=number_format(array_sum($sumDataArr['COM_CUTT_QTY']));?></th>
        <th align="right"><?=number_format(array_sum($sumDataArr['BAL_CUT_QTY']));?></th>
		<th></th>
	
        <th align="right"><?=number_format(array_sum($sumDataArr['COM_PRINT_QTY']));?></th>
        <th align="right"><?=number_format(array_sum($sumDataArr['BAL_PRINT_QTY']));?></th>
		<th></th>
        <th align="right"><?=number_format(array_sum($sumDataArr['COM_EMB_QTY']));?></th>
        <th align="right"><?=number_format(array_sum($sumDataArr['BAL_EMB_QTY']));?></th>
		<th></th>
        <th align="right"><?=number_format(array_sum($sumDataArr['COM_SEW_QTY']));?></th>
        <th align="right"><?=number_format(array_sum($sumDataArr['BAL_OUTPUT_QTY']));?></th>
		<th></th>
        <th align="right"><?=number_format(array_sum($sumDataArr['COM_FIN_QTY']));?></th>
        <th align="right"><?=number_format(array_sum($sumDataArr['BAL_FINISH_QTY']));?></th>
        <th></th>
        
    </tfoot>
</table>

<table width="230" rules="all" border="1">
	<caption>Grand Total Export Qty<br /> From <?=$first_date;?> To today: </caption>
	<tbody>
        <tr>
            <th width="170" align="left">Export Qty (Pcs)</th>
            <th align="right"><?=$grandDataArr[$compani_id]['EX_FACTORY_QNTY'];?></th>
        </tr>
        <tr>
            <th align="left">Export Gross Value$</th>
            <th align="right"></th>
        </tr>
        <tr>
            <th align="left">Export Invoice Value$</th>
            <th align="right"><?=number_format($grandDataArr[$compani_id]['EXPORT_INVOICE_VAL'],2);?></th>
        </tr>
        <tr>
            <th align="left">Export Net Value$</th>
            <th align="right"><?=number_format($grandDataArr[$compani_id]['PO_NET_INVO_VALUE'],2);?></th>
        </tr>
    </tbody>
</table>


<?
		$message=ob_get_contents();
		ob_clean();
		$mail_item=86;
	
	if($is_all_company==1){
		$htmlBody.=$message;
		ob_clean();
	}
	else{
		$body="Dear Concerns,<br>Please check Last day's Ex-factory Qty and take necessary steps immediately :<br>";
	
	
		$sql = "SELECT c.email_address as MAIL FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=$mail_item and b.mail_user_setup_id=c.id and a.company_id=$compani_id and a.IS_DELETED=0 and a.STATUS_ACTIVE=1 AND a.MAIL_TYPE=1 and b.IS_DELETED=0 and b.STATUS_ACTIVE=1 and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
		$mail_sql=sql_select($sql);
		foreach($mail_sql as $row)
		{
			$toArr[$row[MAIL]]=$row[MAIL]; 
		}
	
		$to=implode(',',$toArr);
	
		$subject = "Daily Export Information";				
		$header=mailHeader();
		
		if($_REQUEST['isview']==1){
			if($to){
				echo 'Mail Item:'.$form_list_for_mail[$mail_item].'=>'.$to;
			}else{
				echo "Mail address not set. [Please set mail from  Mail Recipient Group, Mail Item: <b>".$form_list_for_mail[$mail_item]."</b>]<br>";
			}
			echo $body.$message;
		}
		else{
			if($to!="")echo sendMailMailer( $to, $subject, $body.$message, $from_mail);
		}
	}

	


}


if($is_all_company==1){
		
		$body="Dear Concerns,<br>Please check Last day's Ex-factory Qty and take necessary steps immediately :<br>";
		
		$sql = "SELECT c.email_address as MAIL FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=$mail_item and b.mail_user_setup_id=c.id   and a.IS_DELETED=0 and a.STATUS_ACTIVE=1  and b.IS_DELETED=0 and b.STATUS_ACTIVE=1   and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
		$mail_sql=sql_select($sql);
		foreach($mail_sql as $row)
		{
			$toArr[$row[MAIL]]=$row[MAIL]; 
		}
	
		$to=implode(',',$toArr);
	
		$subject = "Daily Export Information";				
		$header=mailHeader();
		
		if($_REQUEST['isview']==1){
			if($to){
				echo 'Mail Item:'.$form_list_for_mail[$mail_item].'=>'.$to;
			}else{
				echo "Mail address not set. [Please set mail from  Mail Recipient Group, Mail Item: <b>".$form_list_for_mail[$mail_item]."</b>]<br>";
			}
			echo $body.$htmlBody;
		}
		else{
			if($to!="")echo sendMailMailer( $to, $subject, $body.$htmlBody, $from_mail);
		}
}

 


?>