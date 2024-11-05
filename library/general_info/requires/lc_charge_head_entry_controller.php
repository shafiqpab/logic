<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']["user_id"];
$permission=$_SESSION['page_permission'];

//--------------------------- Start-------------------------------------//
if($action=="load_lc_currency_nam"){
	echo "(".$currency[$data].")";
}
if($action=="populate_data_from_search"){
	$data_array=sql_select("select id, company_id, bank_id, charge_for_id, currency_id, charge_date,remarks from LIB_LC_CHARGE_MST where id='$data' and is_deleted=0 and status_active=1");
	foreach ($data_array as $row)
	{ 
		echo "document.getElementById('update_id').value = '".$row[csf("id")]."';\n"; 
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";  
		echo "document.getElementById('cbo_bank_name').value = '".$row[csf("bank_id")]."';\n";  
		echo "document.getElementById('cbo_charge_name').value = '".$row[csf("charge_for_id")]."';\n";  
		echo "document.getElementById('cbo_currency_name').value = '".$row[csf("currency_id")]."';\n";  
		echo "document.getElementById('currency_nam').innerHTML = '(".$currency[$row[csf("currency_id")]].")';\n";  
		echo "document.getElementById('txt_date').value = '".change_date_format($row[csf("charge_date")])."';\n";
		echo "document.getElementById('txt_remarks').value = '".$row[csf("remarks")]."';\n";
	}

	$dtls_arr=sql_select("select id, mst_id, pay_head_id, amount from LIB_LC_CHARGE_DTLS where mst_id='$data' and is_deleted=0 and status_active=1");
	foreach ($dtls_arr as $row)
	{  
		echo "document.getElementById('txtpayamount_".$row[csf("pay_head_id")]."').value = '".$row[csf("amount")]."';\n";  
	}
}
if($action=="load_lc_charge_head_entry"){
	$bank_arr=return_library_array( "select id, bank_name from lib_bank",'id','bank_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$data_array=sql_select("select id, company_id, bank_id, charge_for_id, currency_id, charge_date from LIB_LC_CHARGE_MST where company_id='$data' and is_deleted=0 and status_active=1 order by id desc" );
	?>
	<table class="rpt_table" width="550px" cellspacing="1" rules="all">
	<thead>
		<tr>
			<th width="20">Sl</th>
			<th width="200">Company</th>
			<th width="120">Bank Name</th>
			<th width="80">Charge For</th>
			<th width="80">Currency</th>
			<th width="80">Date</th>
		</tr>		
	</thead>
	<tbody>
	<?
		$i=0;
		foreach($data_array as $row){
			$i++;
			if( $i % 2 == 0 ) $bgcolor="#E9F3FF"; else $bgcolor = "#FFFFFF";
			?>
			<tr align="center" id="tr_<? echo $key; ?>" onClick="fnc_show(<? echo $row[csf("id")]; ?>)" bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer">
			<td align="center"><?= $i;?></td>
			<td align="center"><? echo $company_arr[$row[csf("company_id")]];?></td>
			<td align="center"><? echo $bank_arr[$row[csf("bank_id")]];?></td>
			<td align="center"><? echo $lc_charge_arr[$row[csf("charge_for_id")]];?></td>
			<td align="center"><? echo $currency[$row[csf("currency_id")]];?></td>
			<td align="center"><? echo change_date_format($row[csf("charge_date")]);?></td>
			</tr>
			<?
		}

	?>
	</tbody>
	</table>

	<?
}
if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 

	if ($operation==0) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		if (is_duplicate_field( "company_id,bank_id,charge_for_id", "LIB_LC_CHARGE_MST", "company_id=$cbo_company_name and bank_id=$cbo_bank_name and charge_for_id=$cbo_charge_name and status_active=1 and is_deleted=0") == 1)
		{
			echo "11**Duplicate Company, Bank and Charge"; disconnect($con);die;
		}

		$mst_id=return_next_id("id", "LIB_LC_CHARGE_MST", 1);

		$field_array_mst="id, company_id, bank_id, charge_for_id, currency_id, charge_date, remarks, inserted_by, insert_date, status_active, is_deleted";
		$data_array_mst="(".$mst_id.",".$cbo_company_name.",".$cbo_bank_name.",".$cbo_charge_name.",".$cbo_currency_name.",".$txt_date.",".$txt_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
		// echo "10**INSERT INTO LIB_LC_CHARGE_MST (".$field_array_mst.") VALUES ".$data_array_mst; 
		// die;

		$field_array_dtls="id, mst_id, pay_head_id, amount, inserted_by, insert_date, is_deleted, status_active";
		$id_dtls=return_next_id("id", "LIB_LC_CHARGE_DTLS", 1);
		$row_num_arr = split(',',$row_num_arr);
		$data_array_dtls='';
		for($m=0; $m<sizeof($row_num_arr); $m++)
		{
			$mm=$row_num_arr[$m];
			$txtpayhead="txtpayhead_".$mm;
			$txtamount="txtpayamount_".$mm;

			if ($data_array_dtls!='') {$data_array_dtls .=",";}
			$data_array_dtls .="(".$id_dtls.",".$mst_id.",'".$$txtpayhead."','".$$txtamount."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',0,1)";
			$id_dtls++;
		}
		// echo "INSERT INTO LIB_LC_CHARGE_DTLS (".$field_array_dtls.") VALUES ".$data_array_dtls; 
		$rID=sql_insert("LIB_LC_CHARGE_MST",$field_array_mst,$data_array_mst,0);
		// echo "</br>100**$rID";
		$rID1=sql_insert("LIB_LC_CHARGE_DTLS",$field_array_dtls,$data_array_dtls,0);	
		// echo '10**'.$rID.'**'.$rID1;oci_rollback($con);die;
		
		if($db_type==0)
		{
			if($rID==1 && $rID1==1)
			{
				mysql_query("COMMIT");  
				echo "0**".$mst_id."**".$id_dtls;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID==1 && $rID1==1)
			{
				oci_commit($con);  
				echo "0**".$mst_id."**".$id_dtls;
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1) // Update Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$field_array_mst="company_id*bank_id*charge_for_id*currency_id*charge_date*remarks*updated_by*update_date";
		$data_array_mst="".$cbo_company_name."*".$cbo_bank_name."*".$cbo_charge_name."*".$cbo_currency_name."*".$txt_date."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		$field_array_dtls="id, mst_id, pay_head_id, amount, inserted_by, insert_date, is_deleted, status_active";
		$id_dtls=return_next_id("id", "LIB_LC_CHARGE_DTLS", 1);
		$row_num_arr = split(',',$row_num_arr);
		$data_array_dtls='';
		for($m=0; $m<sizeof($row_num_arr); $m++)
		{
			$mm=$row_num_arr[$m];
			$txtpayhead="txtpayhead_".$mm;
			$txtamount="txtpayamount_".$mm;

			if ($data_array_dtls!='') {$data_array_dtls .=",";}
			$data_array_dtls .="(".$id_dtls.",".$update_id.",'".$$txtpayhead."','".$$txtamount."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',0,1)";
			$id_dtls++;
		}

		$rID=sql_update("LIB_LC_CHARGE_MST",$field_array_mst,$data_array_mst,"id","".$update_id."",0);
		$rID1=execute_query("delete from LIB_LC_CHARGE_DTLS where mst_id =".$update_id."",0);
		$rID2=sql_insert("LIB_LC_CHARGE_DTLS",$field_array_dtls,$data_array_dtls,0);	
		//  echo "10**".$rID.'='.$rID1.'='.$rID2."</br>"; die;
		if($db_type==0)
		{
			if($rID==1 && $rID1==1 && $rID2==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'",'',$update_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID==1 && $rID1==1 && $rID2==1)
			{
				oci_commit($con);  
				echo "1**".str_replace("'",'',$update_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
	
		//check_table_status( $_SESSION['menu_id'],0);
		disconnect($con);
		die;
	}
	else if ($operation==2) // Delete Here----------------------------------------------------------  
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$field_array="status_active*is_deleted*updated_by*update_date";
		$data_array="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$rID=sql_delete("LIB_LC_CHARGE_MST",$field_array,$data_array,"id","".$update_id."",0);
		$rID1=sql_delete("LIB_LC_CHARGE_DTLS",$field_array,$data_array,"mst_id","".$update_id."",0);
		// echo $update_id;die;
	// echo "10**".$rID.'='.$rID1."</br>"; die;
		if($db_type==0)
		{
			if($rID==1 && $rID1==1)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'",'',$update_id);;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID==1 && $rID1==1)
			{
				oci_commit($con);  
				echo "2**".str_replace("'",'',$update_id);;
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		
		disconnect($con);
	}// Delete Here End ----------------------------------------------------------
	
}

?>