<?php
date_default_timezone_set("Asia/Dhaka");
require_once('../includes/common.php');
// require_once('../mailer/class.phpmailer.php');
require_once('setting/mail_setting.php');

	$comp_lib = return_library_array("select id, company_name from lib_company where status_active=1 and is_deleted=0", "id","company_name");
	$buyer_lib = return_library_array("select id, buyer_name from lib_buyer where  status_active=1 and is_deleted=0","id", "buyer_name");

	  
	$strtotime = ($_REQUEST['view_date'])?strtotime($_REQUEST['view_date']):time();

	$date_diff="(to_date('".date('d-M-y', $strtotime)."', 'dd-MM-yy')- to_date(a.ex_factory_date, 'dd-MM-yy'))";
	$where_con=" and a.bl_date is NULL";

	$sql="select a.id,a.is_lc,a.benificiary_id,a.buyer_id,a.lc_sc_id,a.invoice_no,a.invoice_date, a.bl_date, a.total_carton_qnty,a.ex_factory_date,$date_diff as pending_days,a.INVOICE_VALUE  from com_export_invoice_ship_mst a where a.status_active=1 and a.is_deleted=0 $where_con and  ($date_diff > 7 or a.ex_factory_date is NULL) and a.invoice_date > '1-Jul-2019' and a.buyer_id<>12";//   and a.invoice_no='123123'
	$result=sql_select($sql);
	$dataArr=array();$above14dataArr=array();
	foreach($result as $row)
	{
		
		if($row[csf('pending_days')] >7 and $row[csf('pending_days')] <15){
			$dataArr[$row[csf('benificiary_id')]][]=$row;
			$lc_sc_id_arr[$row[csf('is_lc')]][$row[csf('lc_sc_id')]]=$row[csf('lc_sc_id')];
		}
		else{
			$above14dataArr[$row[csf('benificiary_id')]][]=$row;
			$lc_sc_id_arr[$row[csf('is_lc')]][$row[csf('lc_sc_id')]]=$row[csf('lc_sc_id')];
		}
	
	}
	
	
		$sql_lc = "select id,export_lc_no as lc_sc from com_export_lc where status_active=1 and is_deleted=0 and id in(".implode(',',$lc_sc_id_arr[1]).")";

		$sql_sc = "select id,contract_no as lc_sc from com_sales_contract where status_active=1 and is_deleted=0 and id in(".implode(',',$lc_sc_id_arr[2]).")";
	
		$lc_lib = return_library_array($sql_lc, "id","lc_sc");
		$sc_lib = return_library_array($sql_sc, "id","lc_sc");
	
	
	ob_start();
	echo "Dear Sir,<br> Please Check Bill Of Lading From 8 to 14 Days Over Delay Mail Based on ".date("d-m-Y", $strtotime);
	$flag=0;
	$company_total=array();
	?>
	<table border="1" rules="all">
	<?
	foreach($dataArr as $company_id=>$company_data_arr){?>
    
    <tr>
    	<td colspan="11"><strong><? echo $comp_lib[$company_id];?></strong></td>
    </tr>
    <tr bgcolor="#CCCCCC">
        <th width="35">SL</th>
        <th>Buyer</th>
        <th>LC No</th>
        <th>Invoice No</th>
        <th>Invoice Date</th>
        <th>Invoice Value</th>
        <th>CTN Qty</th>
        <th>Ship Date</th>
        <th bgcolor="#FEE9FE">BL Date</th>
        <th>Pending Days</th>
    </tr>
   <? 
   $i=1;
   
    foreach($company_data_arr as $rows){
	$flag=1;	
	$lc_sc_id=($rows[csf('is_lc')]==1)?$lc_lib[$rows[csf('lc_sc_id')]]:$sc_lib[$rows[csf('lc_sc_id')]];
	$datediff=datediff('d',$submit_date_lib[$rows[csf('id')]],$rows[csf('bl_date')]);
	$submit_date=($datediff<8)?"":$submit_date_lib[$rows[csf('id')]];

	$company_total[$company_id]+=$rows['INVOICE_VALUE'];
	?>
    <tr>
        <td align="center"><? echo $i;?></td>
        <td><? echo $buyer_lib[$rows[csf('buyer_id')]];?></td>
        <td><? echo $lc_sc_id;?></td>
        <td><? echo $rows[csf('invoice_no')];?></td>
        <td align="center"><? echo change_date_format($rows[csf('invoice_date')]);?></td>
        <td align="right"><? echo $rows['INVOICE_VALUE'];?></td>
        <td align="right"><? echo $rows[csf('total_carton_qnty')];?></td>
        <td align="center"><? echo change_date_format($rows[csf('ex_factory_date')]);?></td>
        <td align="center" bgcolor="#FEE9FE"><? echo $rows[csf('bl_date')];?></td>
        <td align="center"><? echo $rows[csf('pending_days')];?></td>
	</tr>
    <? 
	 $i++;
	}
	?>
		<tr>
			<th colspan="5" align="">Sub Total:</th>
			<th align="right"><?=$company_total[$company_id];?></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
		</tr>	
	
	<?
	
		
	}
	?>
		<tr>
			<th colspan="5" align="">Grand Total:</th>
			<th align="right"><?=array_sum($company_total);?></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
		</tr>
		</table><br>
	<?


 


	$message=ob_get_contents();
	ob_clean();
	

	ob_start();
	echo "Dear Sir,<br> Please Check Bill Of Lading Above 14 Days Over Delay Mail Based on ".date("d-m-Y", $strtotime);
	$flag14=0;
	$company_total=array();
	?>
	<table border="1" rules="all">
	<?
	foreach($above14dataArr as $company_id=>$company_data_arr){?>
    
    <tr>
    	<td colspan="11"><strong><? echo $comp_lib[$company_id];?></strong></td>
    </tr>
    <tr bgcolor="#CCCCCC">
        <th width="35">SL</th>
        <th>Buyer</th>
        <th>LC No</th>
        <th>Invoice No</th>
        <th>Invoice Date</th>
        <th>Invoice Value</th>
        <th>CTN Qty</th>
        <th>Ship Date</th>
        <th bgcolor="#FEE9FE">BL Date</th>
        <th>Pending Days</th>
    </tr>
   <? 
   $i=1;
  
    foreach($company_data_arr as $rows){
		$flag14=1;
		$lc_sc_id=($rows[csf('is_lc')]==1)?$lc_lib[$rows[csf('lc_sc_id')]]:$sc_lib[$rows[csf('lc_sc_id')]];
		$datediff=datediff('d',$submit_date_lib[$rows[csf('id')]],$rows[csf('bl_date')]);
		$submit_date=($datediff<15)?"":$submit_date_lib[$rows[csf('id')]];
		$company_total[$company_id]+=$rows['INVOICE_VALUE'];
		?>
		<tr>
			<td align="center"><? echo $i;?></td>
			<td><? echo $buyer_lib[$rows[csf('buyer_id')]];?></td>
			<td><? echo $lc_sc_id;?></td>
			<td><? echo $rows[csf('invoice_no')];?></td>
			<td align="center"><? echo change_date_format($rows[csf('invoice_date')]);?></td>
			<td align="right"><? echo $rows['INVOICE_VALUE'];?></td>
			<td align="right"><? echo $rows[csf('total_carton_qnty')];?></td>
			<td align="center"><? echo change_date_format($rows[csf('ex_factory_date')]);?></td>
			<td align="center" bgcolor="#FEE9FE"><? echo $rows[csf('bl_date')];?></td>
			<td align="center"><? echo $rows[csf('pending_days')];?></td>
		</tr>
		<? 
		 $i++;
		}
		?>
		<tr>
			<th colspan="5" align="">Sub Total:</th>
			<th align="right"><?=$company_total[$company_id];?></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
		</tr>	
	
		<?
	 
		 
	}
	?>
	<tr>
		<th colspan="5" align="">Grand Total:</th>
		<th align="right"><?=array_sum($company_total);?></th>
		<th></th>
		<th></th>
		<th></th>
		<th></th>
	</tr>
	</table><br>
	<?


	$message_14=ob_get_contents();
	ob_clean();

	
		// $toArr=array();
		// $sql = "SELECT c.EMAIL_ADDRESS FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=21 and b.mail_user_setup_id=c.id";
		// $sql_result_arr=sql_select($sql);
		// foreach($sql_result_arr as $row)
		// {
		// 	$toArr[$row['EMAIL_ADDRESS']]=$row['EMAIL_ADDRESS']; 
		// }
		// $to = implode(',',$toArr);

		$subject="Bill Of Lading From 8 to 14 days Delay Based On  :".date("d-m-Y", $strtotime);
		$subject_14="Bill Of Lading Above 14 days Over Delay Based On  :".date("d-m-Y", $strtotime);

		$to="tahir@urmigroup.net,mainul@urmigroup.net,mohiuddin_sumon@urmigroup.net,erpsupport@urmigroup.net,sarker@logicsofbd.com";
		$to14="asif@urmigroup.net,mirashraful@urmigroup.net,shamarukh.fakhruddin@urmigroup.net,tahir@urmigroup.net,mainul@urmigroup.net,mohiuddin_sumon@urmigroup.net,erpsupport@urmigroup.net,sarker@logicsofbd.com";
		
		
		
		
		$header=mailHeader();
		//if($flag==1){echo sendMailMailer( $to, $subject, $message );}
		//if($flag14==1){echo sendMailMailer( $to14, $subject_14, $message_14 );}
		if($_REQUEST['isview']==1){
			echo $to.$message;
			echo $to14.$message_14;
		}
		else {
			if($flag==1){echo sendMailMailer( $to, $subject, $message );}
			if($flag14==1){echo sendMailMailer( $to14, $subject_14, $message_14 );}
			
		}

	

?>