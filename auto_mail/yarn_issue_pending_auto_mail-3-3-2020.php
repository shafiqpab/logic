<?php

	date_default_timezone_set("Asia/Dhaka");
	
	// require_once('../mailer/class.phpmailer.php');
	require_once('../includes/common.php');
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
	$prev_date = change_date_format(date('Y-m-d H:i:s', strtotime('-5 day', strtotime($current_date))),'','',1); 
	$actual_date="is null";
  

//Fabric booking Auto mail..............................................................................
//$company_library = array(1=>$company_library[1]);
foreach($company_library as $compid=>$compname)
{
	

	if($db_type==0)
	{	
		//$date_diff="(DATEDIFF('".date('Y-m-d',time())."', lc_date))";
	}
	else
	{
		$date_diff="(to_date('".date('d-M-y',time())."', 'dd-MM-yy')- to_date(a.allocation_date, 'dd-MM-yy'))";
	}


	$prod_data_arr = array();
	$prod_data = sql_select("select id, yarn_count_id,yarn_type,brand, supplier_id, lot from product_details_master where item_category_id=1");
	foreach ($prod_data as $row) {
		$prod_data_arr[$row[csf('id')]]['yarn_count'] = $row[csf('yarn_count_id')];
		$prod_data_arr[$row[csf('id')]]['yarn_type'] = $row[csf('yarn_type')];
		$prod_data_arr[$row[csf('id')]]['brand'] = $row[csf('brand')];
		$prod_data_arr[$row[csf('id')]]['lot'] = $row[csf('lot')];
	}



$sql="select a.id as sid, a.id as id,a.job_no,a.booking_no,b.buyer_name buyer_id,a.allocation_date,a.po_break_down_id,a.item_id,a.qnty allocation_qty,b.company_name company_id,b.location_name 
from inv_material_allocation_mst a,wo_po_details_master b 
where a.job_no=b.job_no and a.item_category=1 and b.company_name=$compid 
and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and (a.is_dyied_yarn!=1 or a.is_dyied_yarn is null) and $date_diff > 10 "; //and a.booking_no='AST-Fb-15-00192'
$booking_id_arr=array();$product_id_arr=array();$po_id_arr=array();
$dataArrayResult=sql_select($sql);
foreach($dataArrayResult as $rows){
	$booking_id_arr[$rows[csf('booking_no')]]=$rows[csf('booking_no')];
	$product_id_arr[$rows[csf('item_id')]]=$rows[csf('item_id')];
	$po_id_arr[$rows[csf('job_no')]]=$rows[csf('po_break_down_id')];
}



//Temporary table........................................................................start;
if($db_type==2)
{
	$con = connect();	
	$sql = "CREATE GLOBAL TEMPORARY TABLE my_temp_id_table (booking_no VARCHAR2(50)) ON COMMIT DELETE ROWS";	
	execute_query($sql);
	foreach($booking_id_arr as $booking_id){
		execute_query("INSERT INTO my_temp_id_table(booking_no)VALUES('".$booking_id."')");
	 }
	/*$data=sql_select("SELECT * FROM my_temp_id_table");	
	var_dump($data);*/
	//execute_query("DROP TABLE my_temp_id_table PURGE");
	//oci_commit($con);
}

//Temporary table........................................................................end;
		
		
		
		
		if($db_type==2)
		{
			$tem_table=", my_temp_id_table e";
			$booking_cond =" and a.booking_no=e.booking_no";
		}
		else
		{
			$lcString = implode("','",$booking_id_arr);
			$booking_cond=" and  a.booking_no  in('$lcString')";
		}	


		if($db_type==2)
		{
			
			/*$product_cond=" and (";
			$IdsArr=array_chunk($product_id_arr,990);
			foreach($IdsArr as $ids)
			{
				$product_cond.=" c.prod_id in('".implode(",",$ids)."') or ";
			}
			$product_cond=chop($product_cond,'or ');
			$product_cond.=")";*/
		}
		else
		{
			$lcString = implode(',',$product_id_arr);
			$product_cond=" and c.prod_id  in($lcString)";
		}	




$sql="select a.booking_no, c.prod_id, sum(d.cons_quantity) qty 
 from ppl_planning_info_entry_mst a,ppl_planning_info_entry_dtls b,ppl_yarn_requisition_entry c ,inv_transaction d $tem_table
where a.id=b.mst_id and b.id=c.knit_id and c.requisition_no =d.requisition_no $booking_cond $product_cond and a.company_id=$compid and c.prod_id=d.prod_id  and d.item_category=1
and d.transaction_type=2 and d.receive_basis=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0
 group by a.booking_no,c.prod_id";
$issue_data_sql_arr = sql_select($sql);
 $issue_qty_arr=array();
	foreach ($issue_data_sql_arr as $row) {
		$issue_qty_arr[$row[csf('booking_no')]][$row[csf('prod_id')]]+= $row[csf('qty')];

	}





//Temporary table........................................................................start;
if($db_type==2)
{
	$con = connect();	
	$sql = "CREATE GLOBAL TEMPORARY TABLE my_temp_id_table2 (booking_no VARCHAR2(50)) ON COMMIT DELETE ROWS";	
	execute_query($sql);
	foreach($booking_id_arr as $booking_id){
		execute_query("INSERT INTO my_temp_id_table2(booking_no)VALUES('".$booking_id."')");
	 }
}
//Temporary table........................................................................end;
	if($db_type==2)
	{
		$tem_table=", my_temp_id_table2 e";
		$booking_cond =" and a.booking_no=e.booking_no";
	}
	else
	{
		$lcString = implode("','",$booking_id_arr);
		$booking_cond=" and  a.booking_no  in('$lcString')";
	}	
		

$sql="select a.booking_no, b.prod_id, (b.cons_quantity) qty
from inv_issue_master a, inv_transaction b $tem_table
where a.id=b.mst_id and a.issue_basis=1 and b.item_category=1  and a.company_id=$compid $booking_cond and a.is_deleted=0 and b.transaction_type=2 and b.is_deleted=0 and a.status_active=1 and b.status_active=1";

$issue_data_sql_by_booking_arr = sql_select($sql);
	$issue_qty_arr2=array();
	foreach ($issue_data_sql_by_booking_arr as $row) {
		$issue_qty_arr2[$row[csf('booking_no')]][$row[csf('prod_id')]]+= $row[csf('qty')];

	}




//Temporary table........................................................................start;
$po_id_arr = array_unique(explode(',',implode(',',$po_id_arr)));
if($db_type==2)
{
	$con = connect();	
	$sql = "CREATE GLOBAL TEMPORARY TABLE my_temp_id_table3 (po_id NUMBER (11)) ON COMMIT DELETE ROWS";	
	execute_query($sql);
	foreach($po_id_arr as $po){
		execute_query("INSERT INTO my_temp_id_table3(po_id)VALUES(".$po.")");
	 }
}
//Temporary table........................................................................end;
	if($db_type==2)
	{
		$tem_table=", my_temp_id_table3 b";
		$po_cond =" and a.id=b.po_id";
	}
	else
	{
		$lcString = implode(',',$po_id_arr);
		$po_cond=" and  a.id  in($lcString)";
	}	

//Min Publish Ship Date..............................................................
$min_ship_date_arr = return_library_array("select job_no_mst,min(pub_shipment_date) as pub_shipment_date from wo_po_break_down a  $tem_table
where a.is_deleted=0 and a.status_active=1 $po_cond group by job_no_mst","job_no_mst","pub_shipment_date");






if($db_type==2)
{
	execute_query("DROP TABLE my_temp_id_table PURGE");
	execute_query("DROP TABLE my_temp_id_table2 PURGE");
	execute_query("DROP TABLE my_temp_id_table3 PURGE");
	oci_commit($con);
}


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
        <th width="100">Brand</th>
        <th width="60">Allocated Qty</th>
        <th width="60">Req. Base Issue Qty</th>
        <th width="60">Booking Base Issue Qty</th>
        <th>Yet to issue</th>
    </tr>
    <? $i=1; 
	$total_allo_qty=$total_issue_qty=$total_blance_qty=0;
	//$dataArrayResult=sql_select($sql);
	foreach($dataArrayResult as $rows){
	$bgcolor = ($i%2==0)?"#E9F3FF":"#FFFFFF";
	$rows[csf('cons_quantity')]=$issue_qty_arr[$rows[csf('booking_no')]][$rows[csf('item_id')]];
		
		//if($rows[csf('allocation_qty')] > 0.3 and $rows[csf('cons_quantity')]<$rows[csf('allocation_qty')]){
		if($rows[csf('allocation_qty')] > 0.3 && ($rows[csf('allocation_qty')]-($rows[csf('cons_quantity')]+$issue_qty_arr2[$rows[csf('booking_no')]][$rows[csf('item_id')]])) > 0.03){
			
			$total_allo_qty+=$rows[csf('allocation_qty')];
			$total_issue_qty+=$rows[csf('cons_quantity')];
			$total_issue_qty_booking+=$issue_qty_arr2[$rows[csf('booking_no')]][$rows[csf('item_id')]];
			$total_blance_qty+=$rows[csf('allocation_qty')]-($rows[csf('cons_quantity')]+$issue_qty_arr2[$rows[csf('booking_no')]][$rows[csf('item_id')]]);
	?>
    <tr bgcolor="<? echo $bgcolor; ?>">
        <td align="center"><? echo $i; ?></td>
        <td align="center"><? echo $company_library[$rows[csf('company_id')]]; ?></td>
        <td><? echo $buyer_library[$rows[csf('buyer_id')]]; ?></td>
        <td align="center"><? echo $rows[csf('job_no')]; ?></td>
        <td align="center"><? echo $rows[csf('booking_no')]; ?></td>
        <td align="center"><? echo change_date_format($min_ship_date_arr[$rows[csf('job_no')]]); ?></td>
        <td align="center"><? echo change_date_format($rows[csf('allocation_date')]); ?></td>
        <td align="center"><? echo $yarn_count_lib[$prod_data_arr[$rows[csf('item_id')]]['yarn_count']]; ?></td>
        
        <td align="center"><? echo $yarn_type[$rows[csf('item_id')]['yarn_type']]; ?></td>
        <td><? echo $prod_data_arr[$rows[csf('item_id')]]['lot']; ?></td>
        <td><? echo $brand_lib[$prod_data_arr[$rows[csf('item_id')]]['brand']]; ?></td>
        <td align="right"><? echo number_format($rows[csf('allocation_qty')],2); ?></td>
        <td align="right"><? echo number_format($rows[csf('cons_quantity')],2); ?></td>
        <td align="right"><? echo number_format($issue_qty_arr2[$rows[csf('booking_no')]][$rows[csf('item_id')]],2); ?></td>
        <td align="right"><? echo number_format($rows[csf('allocation_qty')]-($rows[csf('cons_quantity')]+$issue_qty_arr2[$rows[csf('booking_no')]][$rows[csf('item_id')]]),2); ?></td>
    </tr>
     <? $i++;}} ?>
     
    <tr>
        <th align="right" colspan="11">Total </th>
        <th align="right"><? echo number_format($total_allo_qty,2);?></th>
        <th align="right"><? echo number_format($total_issue_qty,2);?></th>
        <th align="right"><? echo number_format($total_issue_qty_booking,2);?></th>
        <th align="right"><? echo number_format($total_blance_qty,2);?></th>
    </tr>
</table>

<?

	$to="";$message="";
	$sql = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=20 AND a.MAIL_TYPE=1 and b.mail_user_setup_id=c.id and a.company_id=$compid";
	$mail_sql=sql_select($sql);
	foreach($mail_sql as $row)
	{
		if ($to=="")  $to=$row[csf('email_address')]; else $to=$to.", ".$row[csf('email_address')]; 
	}
	$header=mail_header();
	
	$subject="Yarn issue pending from 10 days left allocation";
	$message=ob_get_contents();
	ob_clean();
	//if($to!=""){echo send_mail_mailer( $to, $subject, $message, $from_mail );}
	if($_REQUEST['isview']==1){
		echo $message;
	}
	else{
		if($to!=""){echo send_mail_mailer( $to, $subject, $message, $from_mail );}
	}

		
} // End Company
	
?>






</body>
</html>