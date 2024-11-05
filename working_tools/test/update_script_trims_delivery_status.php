<?
include('../includes/common.php');
$con = connect();
//echo "gggh"; disconnect(); die;
	//, a.booking_dtls_id, a.receive_dtls_id, a.job_dtls_id
	$del_sql ="select a.id,a.break_down_details_id from trims_delivery_dtls a , trims_delivery_mst b where a.mst_id=b.id and job_dtls_id is null";
	$del_result=sql_select($del_sql); 

	//$sql = "select a.id as receive_dtls_id , a.item_group as trim_group, a.booking_dtls_id,b.id as break_id from subcon_ord_dtls a,subcon_ord_breakdown b where   a.id=b.mst_id and a.order_quantity<>0 and a.booked_qty<>0 and a.status_active=1 and a.is_deleted=0  order by a.id ASC";
	$sql = "select a.id as receive_dtls_id, b.qnty as order_quantity,b.id as break_id from subcon_ord_dtls a,subcon_ord_breakdown b where a.id=b.mst_id  and a.order_quantity<>0 and a.booked_qty<>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  order by a.id ASC"; 
	$order_result=sql_select($sql); 
	foreach ($order_result as $rows)
	{
		//$order_dtls_arr[$rows[csf("break_id")]]['break_id']		=$rows[csf("break_id")];
		$order_dtls_arr[$rows[csf("break_id")]]['order_quantity']	=$rows[csf("order_quantity")];
	}
	
	$from_date='01-10-2020'; $to_date='09-11-2020';
	/*if($db_type==0)
	{ 
		$delivery_date = "and a.delivery_date between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'"; else $delivery_date ="";
		//$ins_year_cond="year(a.insert_date)";
	}
	else
	{
		$delivery_date = "and a.delivery_date between '".change_date_format($from_date, "", "",1)."' and '".change_date_format($to_date, "", "",1)."'"; else $delivery_date ="";
	}*/
	$delivery_date = "and a.delivery_date between '".change_date_format($from_date, "", "",1)."' and '".change_date_format($to_date, "", "",1)."'"; //else $delivery_date ="";
	//echo $delivery_date;
	$delivery_sql ="select b.break_down_details_id,b.delevery_qty,trims_del from trims_delivery_mst a, trims_delivery_dtls b where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $delivery_date  ";
	$delivery_sql_res=sql_select($delivery_sql); $del_qty_arr=array();
	foreach ($delivery_sql_res as $row)
	{
		$del_qty_arr[$row[csf('break_down_details_id')]]['delevery_qty'] += $row[csf('delevery_qty')];
	}
	//echo "10**<pre>";
	//print_r($del_qty_arr); die;
	foreach ($del_qty_arr as $brkID=> $val) 
	{
		$wo_qty 		= $order_dtls_arr[$brkID]['order_quantity']*1;
		$delevery_qty 	= $val['delevery_qty']*1;
		//number_format($row[booked_qty],0)
		$wo_qty=number_format($wo_qty,0);
		$delevery_qty=number_format($delevery_qty,0);
		//echo $wo_qty.'='.$delevery_qty; die;
		if($wo_qty>$delevery_qty){
				$shipStatus=2;	
		}
		elseif($wo_qty <= $delevery_qty){
			$shipStatus=3;
		}
		else{
			$shipStatus=1;
		}

		//echo "10**".$brkID."**".$shipStatus;
		if($shipStatus > 1 )
		{
			//echo "**".$brkID."++";
			$data_array5[$brkID]=explode("*",("".$shipStatus.""));
			$hdnBrkIdArr[]=$brkID;
		}
	}
	$field_array5="delivery_status";
	if($data_array5!="")
	{
		echo "10**".bulk_update_sql_statement( "subcon_ord_breakdown", "id",$field_array5,$data_array5,$hdnBrkIdArr); die;
		$rID5=execute_query(bulk_update_sql_statement( "subcon_ord_breakdown", "id",$field_array5,$data_array5,$hdnBrkIdArr),1);
		if($rID5) $flag=1; else $flag=0;
	}

echo $rID2.'=='.$flag; die;

if($db_type==2)
{
	
	if($rID5 && $flag)
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