<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

	$trans_sys_sql = sql_select("select a.id, a.entry_form, a.to_order_id, a.to_samp_dtls_id, a.from_order_id, a.from_samp_dtls_id
	from  inv_item_transfer_mst a
	where a.entry_form in (110,180) and a.status_active = 1 and a.is_deleted = 0 and a.to_samp_dtls_id=0 order by a.id desc");


	foreach ($trans_sys_sql as $val) 
	{
		$trans_sys_ref_arr[$val[csf("id")]]["to_samp_dtls_id"] = $val[csf("to_order_id")];

		if($val[csf("id")]=="110")
		{
			$sample_arr[$val[csf("to_order_id")]] = $val[csf("to_order_id")];
		}else{
			$sample_arr[$val[csf("from_order_id")]] = $val[csf("from_order_id")];
			$sample_arr[$val[csf("to_order_id")]] = $val[csf("to_order_id")];
			$trans_sys_ref_arr[$val[csf("id")]]["from_samp_dtls_id"] = $val[csf("from_order_id")];
		}
	}

	$sample_arr = array_filter($sample_arr);

	if(count($sample_arr)>0)
	{
		$sample_book_nos =  implode(",", $sample_arr);
		$all_samp_nos_cond=""; $barCond=""; 
		if($db_type==2 && count($sample_arr)>999)
		{
			$sample_arr_chunk=array_chunk($sample_arr,999) ;
			foreach($sample_arr_chunk as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);	
				$barCond.="  a.id in($chunk_arr_value) or ";	
			}
			$all_samp_nos_cond.=" and (".chop($barCond,'or ').")";			
		}
		else
		{
			$all_samp_nos_cond=" and a.id in($sample_book_nos)";	 
		}
	}
	else
	{
		echo "No Booking No Found";
		die;
	}


if($db_type ==0)
{
	$select_dtls = " group_concat(b.id) as dtls_id";
}else{
	$select_dtls = " listagg(b.id,',') within group (order by b.id) as dtls_id";
}

$booking_dtls_data= sql_select("select a.booking_no, a.id, $select_dtls 
from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b
where a.booking_no = b.booking_no and b.status_active = 1 and b.is_deleted = 0 $all_samp_nos_cond
group by a.booking_no, a.id");


$transfered_barcode_po=array();
foreach ($booking_dtls_data as $row) 
{
	$dtls_id = (int)$row[csf("dtls_id")];
	$booking_ref_arr[$row[csf("id")]] =  $dtls_id;
}

foreach ($trans_sys_sql as $val) 
{
	
	if($val[csf("entry_form")] == "110")
	{
		$from_order_id = 0;
	}
	else
	{
		$from_order_id =  $booking_ref_arr[$trans_sys_ref_arr[$val[csf("id")]]["from_samp_dtls_id"]];
	}

	//echo "UPDATE inv_item_transfer_mst set from_samp_dtls_id=".$from_order_id.", to_samp_dtls_id =  ".$booking_ref_arr[$trans_sys_ref_arr[$val[csf("id")]]["to_samp_dtls_id"]]." where id=".$val[csf('id')]." <br />";
	$update_transfer_mst=execute_query("UPDATE inv_item_transfer_mst set from_samp_dtls_id=".$from_order_id.", to_samp_dtls_id =  ".$booking_ref_arr[$trans_sys_ref_arr[$val[csf("id")]]["to_samp_dtls_id"]]." where id=".$val[csf('id')]);
}

oci_commit($con); 

echo "Success";
die;
?>