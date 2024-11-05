<?php

	date_default_timezone_set("Asia/Dhaka");
	
	require_once('../includes/common.php');
	require_once('../mailer/class.phpmailer.php');
	require_once('setting/mail_setting.php');

	$company_library=return_library_array( "select id, company_short_name from lib_company where  status_active=1 and is_deleted=0", "id", "company_short_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	
	$yarn_count_lib = return_library_array("select id,yarn_count from lib_yarn_count where is_deleted = 0 AND status_active = 1 ORDER BY yarn_count ASC","id","yarn_count");

	
	$brand_lib = return_library_array("select id,brand_name from lib_brand where is_deleted = 0 AND status_active = 1 ORDER BY brand_name ASC","id","brand_name");
	


	/*$subject="Yarn issue pending from 10 days left allocation";
	$message='sssssssssss';
	$to='reza@logicsoftbd.com';
	echo send_mail_mailer( $to, $subject, $message, $from_mail );
	die;*/
	
	$strtotime = ($_REQUEST['view_date'])?strtotime($_REQUEST['view_date']):time();
	$tomorrow = change_date_format(date("Y-m-d H:i:s",strtotime(add_date(date("Y-m-d", $strtotime),1))),'','',1);
	$day_after_tomorrow = change_date_format(date("Y-m-d H:i:s",strtotime(add_date(date("Y-m-d", $strtotime),2))),'','',1);
	$current_date = change_date_format(date("Y-m-d H:i:s",strtotime(add_date(date("Y-m-d", $strtotime),0))),'','',1);
	$prev_date = change_date_format(date('Y-m-d H:i:s', strtotime('-10 day', strtotime($current_date))),'','',1); 
	$actual_date="is null";
	
	  
if($db_type==0){
	$str_cond	=" and a.insert_date between '2020-1-1' and '".$prev_date."'";
}
else
{
	$str_cond	=" and a.insert_date between '01-Jan-2020' and '".$prev_date." 11:59:59 PM'";
}


	//echo $str_cond;die;


//Fabric booking Auto mail..............................................................................
//$company_library = array(17=>$company_library[17]);
foreach($company_library as $compid=>$compname)
{
	

	if($db_type==0)
	{	
		$date_diff="(DATEDIFF('".date('Y-m-d',time())."', lc_date))";
	}
	else
	{
		//$date_diff="(to_date('".date('d-M-y',time())."', 'dd-MM-yy') - to_date(a.INSERT_DATE, 'dd-MM-yy'))";
		
		$date_diff="(to_date(SUBSTR ('".date('d-M-y',time())."', 1, 10 ), 'yyyy-mm-ddsssss') - to_date( substr( a.INSERT_DATE, 1, 10 ),'yyyy-mm-ddsssss' ))";
		
	}


	$prod_data_arr = array();
	$prod_data = sql_select("select id, yarn_count_id,yarn_type,brand, supplier_id, lot from product_details_master where item_category_id=1");
	foreach ($prod_data as $row) {
		$prod_data_arr[$row[csf('id')]]['yarn_count'] = $row[csf('yarn_count_id')];
		$prod_data_arr[$row[csf('id')]]['yarn_type'] = $row[csf('yarn_type')];
		$prod_data_arr[$row[csf('id')]]['brand'] = $row[csf('brand')];
		$prod_data_arr[$row[csf('id')]]['lot'] = $row[csf('lot')];
	}



$sql="select 
LISTAGG(a.po_break_down_id, ',') WITHIN GROUP (ORDER BY a.id) as po_break_down_id,

MAX(a.INSERT_DATE) as allocation_date,SUM(a.qnty) as allocation_qty,a.job_no,a.booking_no,b.buyer_name buyer_id,a.item_id,b.company_name as company_id,b.location_name 
from INV_MATERIAL_ALLOCAT_HYSTORY a,wo_po_details_master b 
where a.job_no=b.job_no and a.item_category=1 and b.company_name=$compid  
and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and (a.is_dyied_yarn!=1 or a.is_dyied_yarn is null)  $str_cond  
group by a.job_no,a.booking_no,b.buyer_name,a.item_id,b.company_name,b.location_name"; 
//and a.booking_no='AST-Fb-20-00077' 
  //echo $sql;die;
$booking_id_arr=array();$product_id_arr=array();$po_id_arr=array();
$dataArrayResult=sql_select($sql);
foreach($dataArrayResult as $rows){
	$booking_id_arr[$rows[csf('booking_no')]]=$rows[csf('booking_no')];
	foreach(explode(',',$rows[csf('po_break_down_id')]) as $po){
		$po_id_arr[$po]=$po;
	}
}

	
	$con = connect();
	execute_query("delete from tmp_poid where userid=99999");
	foreach($booking_id_arr as $booking_no){
		execute_query("insert into tmp_poid (userid, pono,type) values (99999,'".$booking_no."',2)");
	}
	
	foreach($po_id_arr as $po){
		execute_query("insert into tmp_poid (userid, poid,type) values (99999,$po,1)");
	}
	
	oci_commit($con);

		


 
$sql="select a.booking_no, c.prod_id, sum(d.cons_quantity) qty 
 from ppl_planning_info_entry_mst a,ppl_planning_info_entry_dtls b,ppl_yarn_requisition_entry c ,inv_transaction d , TMP_POID  e
where a.id=b.mst_id and b.id=c.knit_id and c.requisition_no =d.requisition_no  and a.booking_no=e.pono  and e.type=2 and e.userid=99999 $product_cond and a.company_id=$compid and c.prod_id=d.prod_id  and d.item_category=1
and d.transaction_type=2 and d.receive_basis=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0
 group by a.booking_no,c.prod_id";
 
 		
 
$issue_data_sql_arr = sql_select($sql);
 $issue_qty_arr=array();
	foreach ($issue_data_sql_arr as $row) {
		$issue_qty_arr[$row[csf('booking_no')]][$row[csf('prod_id')]]+= $row[csf('qty')];
	}

//print_r($issue_qty_arr);die;


//Temporary table........................................................................end;

$sql="select a.booking_no, b.prod_id, (b.cons_quantity) qty
from inv_issue_master a, inv_transaction b , TMP_POID e
where a.id=b.mst_id and a.issue_basis=1 and b.item_category=1  and a.company_id=$compid  and a.booking_no=e.pono and e.type=2 and e.userid=99999 and a.is_deleted=0 and b.transaction_type=2 and b.is_deleted=0 and a.status_active=1 and b.status_active=1";

 //echo $sql;die;
$issue_data_sql_by_booking_arr = sql_select($sql);
	$issue_qty_arr2=array();
	foreach ($issue_data_sql_by_booking_arr as $row) {
		$issue_qty_arr2[$row[csf('booking_no')]][$row[csf('prod_id')]]+= $row[csf('qty')];

	}




/*$ret_sql="select c.BOOKING_NO,b.PROD_ID, 0 as REQUISITION_NO,(b.cons_quantity) as CONS_QUANTITY from TMP_POID a,inv_transaction b,inv_receive_master c where c.id=b.mst_id  AND c.BOOKING_NO=a.PONO and a.type=2 and a.userid=99999 AND c.COMPANY_ID = $compid and c.RECEIVE_BASIS=1  and  b.item_category=1 and b.transaction_type=4 and b.is_deleted=0 group by c.BOOKING_NO,b.prod_id,b.cons_quantity
union all
SELECT a.BOOKING_NO, e.PROD_ID,c.REQUISITION_NO, (e.cons_quantity) AS CONS_QUANTITY FROM PPL_PLANNING_ENTRY_PLAN_DTLS a, ppl_yarn_requisition_entry  c, inv_receive_master d, inv_transaction e ,TMP_POID f
   WHERE a.DTLS_ID = c.KNIT_ID AND c.REQUISITION_NO = d.BOOKING_ID  AND d.COMPANY_ID = $compid  AND d.RECEIVE_BASIS = 3 AND d.id = e.mst_id AND   a.BOOKING_NO=f.PONO and f.type=2 and f.userid=99999 
         AND a.is_deleted = 0 AND a.status_active = 1 AND a.is_sales != 1  
GROUP BY a.booking_no, e.prod_id,c.REQUISITION_NO,e.cons_quantity";*/



$ret_sql="select c.BOOKING_NO,b.PROD_ID, 0 as REQUISITION_NO,(b.cons_quantity) as CONS_QUANTITY from TMP_POID a,inv_transaction b,inv_receive_master c where c.id=b.mst_id  AND c.BOOKING_NO=a.PONO and a.type=2 and a.userid=99999 AND c.COMPANY_ID = $compid and c.RECEIVE_BASIS=1  and  b.item_category=1 and b.transaction_type=4 and b.is_deleted=0 group by c.BOOKING_NO,b.prod_id,b.cons_quantity
union all
SELECT a.BOOKING_NO, e.PROD_ID,c.REQUISITION_NO, (e.cons_quantity) AS CONS_QUANTITY FROM PPL_PLANNING_ENTRY_PLAN_DTLS a, ppl_yarn_requisition_entry  c, inv_receive_master d, inv_transaction e ,TMP_POID f
   WHERE a.DTLS_ID = c.KNIT_ID AND c.REQUISITION_NO = d.BOOKING_ID  AND d.COMPANY_ID = $compid  AND d.RECEIVE_BASIS = 3 AND d.id = e.mst_id AND   a.PO_ID=f.poid and f.type=1 and f.userid=99999 
         AND a.is_deleted = 0 AND a.status_active = 1 AND a.is_sales != 1  
GROUP BY a.booking_no, e.prod_id,c.REQUISITION_NO,e.cons_quantity";

 //echo $ret_sql;die;
$issue_ret_result = sql_select($ret_sql);
$issue_ret_arr=array();
foreach($issue_ret_result as $row){
	$issue_ret_arr[$row[BOOKING_NO]][$row[PROD_ID]]+= $row[CONS_QUANTITY];
}

//Min Publish Ship Date..............................................................
$min_ship_date_arr = return_library_array("select job_no_mst,min(pub_shipment_date) as pub_shipment_date from wo_po_break_down a ,TMP_POID b
where a.is_deleted=0 and a.status_active=1 and a.id=b.poid and b.type=1 and b.userid=99999 group by job_no_mst","job_no_mst","pub_shipment_date");





ob_start();
?>

<table border="1" rules="all">
   <tr><td align="center" colspan="15"><strong>Yarn issue pending from 10 days left allocation</strong></td></tr>
    <tr>
        <th width="35">SL</th>
        <th width="50">Comp</th>
        <th width="130">Buyer</th>
        <th width="120">Job No.</th>
        <th width="140">Fab. Booking No</th>
        <th width="80">First Ship date</th>
        <th width="80">Allocation Date</th>
        <th width="50">Yarn Count</th>
        <th width="80">Yarn Type</th>
        <th width="60">Lot</th>
        <th width="60">Product Id</th>
        <th width="100">Brand</th>
        <th width="60">Allocated Qty</th>
        <th width="60">Req. Base Issue Qty</th>
        <th width="60">Booking Base Issue Qty</th>
        <th width="60">Issue Return Qty</th>
        <th>Yet to issue</th>
    </tr>
    <? $i=1; 
	$total_allo_qty=$total_issue_qty=$total_blance_qty=$total_issue_ret_qty=0;
	//$dataArrayResult=sql_select($sql);
	foreach($dataArrayResult as $rows){
	$bgcolor = ($i%2==0)?"#E9F3FF":"#FFFFFF";
	$rows[csf('cons_quantity')]=$issue_qty_arr[$rows[csf('booking_no')]][$rows[csf('item_id')]];
		
		if($rows[csf('allocation_qty')] > 0.3 && ($rows[csf('allocation_qty')]-($rows[csf('cons_quantity')]+$issue_qty_arr2[$rows[csf('booking_no')]][$rows[csf('item_id')]])) > 0.03){
			
			$total_allo_qty+=$rows[csf('allocation_qty')];
			$total_issue_qty+=$rows[csf('cons_quantity')];
			$total_issue_qty_booking+=$issue_qty_arr2[$rows[csf('booking_no')]][$rows[csf('item_id')]];
			$total_blance_qty+=$rows[csf('allocation_qty')]-($rows[csf('cons_quantity')]+$issue_qty_arr2[$rows[csf('booking_no')]][$rows[csf('item_id')]]);
			$total_issue_ret_qty+=$issue_ret_arr[$rows[csf('booking_no')]][$rows[csf('item_id')]];
	?>
    <tr bgcolor="<? echo $bgcolor; ?>">
        <td align="center"><? echo $i; ?></td>
        <td align="center"><? echo $company_library[$rows[csf('company_id')]]; ?></td>
        <td><? echo $buyer_library[$rows[csf('buyer_id')]]; ?></td>
        <td align="center"><? echo $rows[csf('job_no')];; ?></td>
        <td align="center"><? echo $rows[csf('booking_no')]; ?></td>
        <td align="center"><? echo change_date_format($min_ship_date_arr[$rows[csf('job_no')]]); ?></td>
        <td align="center"><? echo change_date_format($rows[csf('allocation_date')]); ?></td>
        <td align="center"><? echo $yarn_count_lib[$prod_data_arr[$rows[csf('item_id')]]['yarn_count']]; ?></td>
        
        <td align="center"><? echo $yarn_type[$prod_data_arr[$rows[csf('item_id')]]['yarn_type']]; ?></td>
        <td><? echo $prod_data_arr[$rows[csf('item_id')]]['lot']; ?></td>
        <td align="center"><? echo $rows[csf('item_id')]; ?></td>
        <td><? echo $brand_lib[$prod_data_arr[$rows[csf('item_id')]]['brand']]; ?></td>
        <td align="right"><? echo number_format($rows[csf('allocation_qty')],2); ?></td>
        <td align="right"><? echo number_format($rows[csf('cons_quantity')],2); ?></td>
        <td align="right"><? echo number_format($issue_qty_arr2[$rows[csf('booking_no')]][$rows[csf('item_id')]],2); ?></td>
        <td align="right"><?= $issue_ret_arr[$rows[csf('booking_no')]][$rows[csf('item_id')]]; ?></td>
        <td align="right"><? echo number_format(($rows[csf('allocation_qty')]-($rows[csf('cons_quantity')]+$issue_qty_arr2[$rows[csf('booking_no')]][$rows[csf('item_id')]]))+$issue_ret_arr[$rows[csf('booking_no')]][$rows[csf('item_id')]],2); ?></td>
    </tr>
     <? $i++;
	 	}
	 } ?>
     
    <tr>
        <th align="right" colspan="12">Total </th>
        <th align="right"><? echo number_format($total_allo_qty,2);?></th>
        <th align="right"><? echo number_format($total_issue_qty,2);?></th>

        <th align="right"><? echo number_format($total_issue_qty_booking,2);?></th>
        <th align="right"><?= number_format($total_issue_ret_qty,2); ?></th>
        <th align="right"><? echo number_format($total_blance_qty,2);?></th>
    </tr>
</table>

<?
	$message=ob_get_contents();
	ob_clean();

	$to="";$message="";
	$sql = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=20 and b.mail_user_setup_id=c.id and a.company_id=$compid and a.IS_DELETED=0 and a.STATUS_ACTIVE=1 AND a.MAIL_TYPE=1 and b.IS_DELETED=0 and b.STATUS_ACTIVE=1 and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
	$mail_sql=sql_select($sql);
	foreach($mail_sql as $row)
	{
		if ($to=="")  $to=$row[csf('email_address')]; else $to=$to.", ".$row[csf('email_address')]; 
	}
 
	
	$subject="Yarn issue pending from 10 days left allocation";

	$header=mailHeader();
	//if($to!=""){echo sendMailMailer( $to, $subject, $message, $from_mail );}
	
 
	
	if($_REQUEST['isview']==1){
		$mail_item=20;
        if($to){
			echo 'Mail Item:'.$form_list_for_mail[$mail_item].'=>'.$to;
		}else{
			echo "Mail address not set. [Please set mail from  Mail Recipient Group, Mail Item: <b>".$form_list_for_mail[$mail_item]."</b>]<br>";
		}
		echo $message;
	}
	else{
		if($to!=""){echo sendMailMailer( $to, $subject, $message, $from_mail );}
	}

	//echo $message;
		
} // End Company


execute_query("delete from tmp_poid where userid=99999");
	
?>






</body>
</html>