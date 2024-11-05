<?
include('../includes/common.php');
$con = connect();
	//, a.booking_dtls_id, a.receive_dtls_id, a.job_dtls_id
	$del_sql ="select a.id,a.break_down_details_id from trims_delivery_dtls a , trims_delivery_mst b where a.mst_id=b.id and job_dtls_id is null";
	$del_result=sql_select($del_sql); 

	$sql = "select a.id as receive_dtls_id , a.item_group as trim_group, a.booking_dtls_id,b.id as break_id from subcon_ord_dtls a,subcon_ord_breakdown b where   a.id=b.mst_id and a.order_quantity<>0 and a.booked_qty<>0 and a.status_active=1 and a.is_deleted=0  order by a.id ASC";
	$order_result=sql_select($sql); 
	foreach ($order_result as $rows)
	{
		$order_dtls_arr[$rows[csf("break_id")]]['item_group']		=$rows[csf("trim_group")];
		$order_dtls_arr[$rows[csf("break_id")]]['break_id']			=$rows[csf("break_id")];
		$order_dtls_arr[$rows[csf("break_id")]]['booking_dtls_id']	=$rows[csf("booking_dtls_id")];
		$order_dtls_arr[$rows[csf("break_id")]]['receive_dtls_id']	=$rows[csf("receive_dtls_id")];
	}

	$production_sql ="Select b.id as prod_dtls_id,b.break_id,b.qc_qty,b.job_dtls_id from trims_production_mst a, trims_production_dtls b where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and b.is_deleted=0";
	$production_sql_res=sql_select($production_sql); $production_arr=array(); $break_ids='';
	foreach ($production_sql_res as $row)
	{
		$break_ids=explode(",",$row[csf("break_id")]); $order_quantity='';
		for($i=0; $i<count($break_ids);$i++)
		{
			$production_arr[$break_ids[$i]]['job_dtls_id'] = $row[csf('job_dtls_id')];
			$production_arr[$break_ids[$i]]['prod_dtls_id'] = $row[csf('prod_dtls_id')];
		}
	} 

	foreach ($del_result as $value) 
	{
		$brkID	=$value[csf('break_down_details_id')];
		$hdnJobDtlsId 			= $production_arr[$brkID]['job_dtls_id'];
		$hdnProductionDtlsId 	= $production_arr[$brkID]['prod_dtls_id'];
		$hdnReceiveDtlsId 		= $order_dtls_arr[$brkID]['receive_dtls_id'];
		$hdnbookingDtlsId 		= $order_dtls_arr[$brkID]['booking_dtls_id'];
		$cboItemGroup 			= $order_dtls_arr[$brkID]['item_group'];

		if($value[csf('id')]!="")
		{
			$data_array2[$value[csf('id')]]=explode("*",("'".$hdnbookingDtlsId."'*'".$hdnReceiveDtlsId."'*'".$hdnJobDtlsId."'*'".$cboItemGroup."'"));
			$hdn_dtls_id_arr[]=$value[csf('id')];
		}
	}
	//echo "<pre>";print_r($data_array2);die;
	$field_array2="booking_dtls_id*receive_dtls_id*job_dtls_id*item_group";
	if($data_array2!="")
	{
		//echo "10**".bulk_update_sql_statement( "trims_delivery_dtls", "id",$field_array2,$data_array2,$hdn_dtls_id_arr);die;
		$rID2=execute_query(bulk_update_sql_statement( "trims_delivery_dtls", "id",$field_array2,$data_array2,$hdn_dtls_id_arr),1);
		if($rID2) $flag=1; else $flag=0;
	}


//echo $rID2.'=='.$flag; die;

if($db_type==2)
{
	
	if($rID2 && $flag)
	{
		oci_commit($con); 
		echo " Update Successful. <br>";die;
	}
	else
	{
		oci_rollback($con);
		echo " Update Failed";
		die;
	}
}
?>