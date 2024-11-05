<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();
$user_id = $_SESSION['logic_erp']["user_id"];
//echo $user_id;die;
//die; 

/*$sqls="select id, sys_no, chalan_no from sub_material_mst  
where entry_form is null and company_id = 2 and trans_type=1 and substr(sys_no,6,5)='AOPMR' 
order by id asc";*/
$companyArr = return_library_array("select ID,IMPORTER_ID from COM_BTB_LC_MASTER_DETAILS","ID","IMPORTER_ID"); 
$sqls="select a.INVOICE_ID, c.PAYTERM_ID  from COM_IMPORT_PAYMENT a, COM_IMPORT_INVOICE_DTLS b, COM_BTB_LC_MASTER_DETAILS c
where a.INVOICE_ID=b.IMPORT_INVOICE_ID and b.BTB_LC_ID=c.id and a.status_active=1 and b.status_active=1 and c.status_active=1 and c.PAYTERM_ID in(2) and c.IMPORTER_ID=1
group by a.INVOICE_ID, c.PAYTERM_ID";
$row_data=sql_select($sqls);
$temp_table_id=return_field_value("max(id) as id","gbl_temp_report_id","1=1","id");
if($temp_table_id=="") $temp_table_id=1;
foreach($row_data as $val)
{
	$r_id=execute_query("insert into gbl_temp_report_id (id, ref_val, user_id) values ($temp_table_id,".$val['INVOICE_ID'].",$user_id)");
	if($r_id) $r_id=1; else {echo "insert into gbl_temp_report_id (id, ref_val, user_id) values ($temp_table_id,".$val['INVOICE_ID'].",$user_id)";oci_rollback($con);die;}
	$temp_table_id++; 
}
if($r_id)
{
	oci_commit($con);
}
//die;

//$field_array="id,system_number_prefix,system_number_prefix_num,system_number,company_id,invoice_id,lc_id,payment_date,inserted_by,insert_date";

//$new_return_number=explode("*",return_mrr_number( str_replace("'","",$cbo_importer_id), '', 'IMP', date("Y",time()), 5, "select system_number_prefix,system_number_prefix_num from com_import_payment_mst where company_id=$cbo_importer_id and $year_cond=".date('Y',time())." order by id DESC", "system_number_prefix", "system_number_prefix_num" ));

//$data_array="(".$id.",'".$new_return_number[1]."','".$new_return_number[2]."','".$new_return_number[0]."',".$cbo_importer_id.",".$invoice_id.",".$btb_lc_id.",".$import_payment_date.",".$user_id.",'".$pc_date_time."')";
$inc=1;
$sql_payment="select a.ID, a.INVOICE_ID, a.LC_ID, a.PAYMENT_DATE from COM_IMPORT_PAYMENT a, GBL_TEMP_REPORT_ID b where a.INVOICE_ID=b.ref_val and a.status_active=1";
$sql_payment_result=sql_select($sql_payment);
$id=return_next_id( "id", "com_import_payment_mst", 1 ) ;
$insert_mst=$update_payment=true;
foreach($sql_payment_result as $val)
{
	$sys_number_prefix_num=$inc;
	$sys_number_prefix='MKD-IMP-19-';
	$sys_number='MKD-IMP-19-'.str_pad($inc,5,"0",STR_PAD_LEFT);

	$insert_mst=execute_query("insert into com_import_payment_mst (id,system_number_prefix,system_number_prefix_num,system_number,company_id,invoice_id,lc_id,payment_date,inserted_by,insert_date) values (".$id.",'".$sys_number_prefix."','".$sys_number_prefix_num."','".$sys_number."','".$companyArr[$val["LC_ID"]]."','".$val["INVOICE_ID"]."','".$val["LC_ID"]."','".$val["PAYMENT_DATE"]."',".$user_id.",'".$pc_date_time."' )");
	if($insert_mst) 
	{
		$insert_mst=1; 
	}
	else 
	{
		$insert_mst=0; 
		echo "insert into com_import_payment_mst (id,system_number_prefix,system_number_prefix_num,system_number,company_id,invoice_id,lc_id,payment_date,inserted_by,insert_date) values (".$id.",'".$sys_number_prefix."','".$sys_number_prefix_num."','".$sys_number."','".$companyArr[$val["LC_ID"]]."','".$val["INVOICE_ID"]."','".$val["LC_ID"]."','".$val["PAYMENT_DATE"]."',".$user_id.",'".$pc_date_time."' )"; 
		oci_rollback($con);die;
	}
	$update_payment=execute_query("UPDATE com_import_payment set MST_ID='".$id."' where id=".$val["ID"]."");
	if($update_payment) 
	{
		$update_payment=1; 
	}
	else 
	{
		$update_payment=0;
		echo "UPDATE com_import_payment set MST_ID='".$id."' where id=".$val["ID"].""; 
		oci_rollback($con);die;
	}
	$inc++;
	$id++;
}

$r_id2=execute_query("delete from gbl_temp_report_id where user_id=$user_id");
if($r_id2)
{
	oci_commit($con);
}

if($insert_mst && $update_payment)
{
	oci_commit($con); 
	echo "Success";

}
else
{
	oci_rollback($con);
	echo "failed";
}



 
?>