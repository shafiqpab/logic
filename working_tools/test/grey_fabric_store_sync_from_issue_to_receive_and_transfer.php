<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

$splited_sql="select barcode_no from  pro_roll_details where roll_split_from > 0  and status_active = 1";
//entry_form = 82  and
$splited_data = sql_select($splited_sql);
foreach ($splited_data as $row)
{
	$splited_data_arr[$row[csf("barcode_no")]] = $row[csf("barcode_no")];
}

$barcode_data=sql_select("select id, entry_form, dtls_id, barcode_no, po_breakdown_id, booking_without_order from  pro_roll_details where entry_form in (22,58,82,83,84,110,180,183,61) and status_active =1 and status_active =1 and barcode_no >0 and barcode_no in (20020092693) order by barcode_no, id desc");


foreach ($barcode_data as $row)
{
	if($splited_data_arr[$row[csf("barcode_no")]] == "")
	{
		if($row[csf("entry_form")] == 58 || $row[csf("entry_form")] == 84 )
		{
			$rcv_n_iss_ret_dtls_arr[$row[csf("dtls_id")]] = $row[csf("dtls_id")];
		}
		else if($row[csf("entry_form")] == 61)
		{
			$issue_dtls_arr[$row[csf("dtls_id")]] = $row[csf("dtls_id")];
		}
		else if($row[csf("entry_form")] == 82 || $row[csf("entry_form")] == 83 || $row[csf("entry_form")] == 110 || $row[csf("entry_form")] == 180 || $row[csf("entry_form")] == 183)
		{
			$trans_dtls_arr[$row[csf("dtls_id")]] = $row[csf("dtls_id")];
		}
	}
}

//issue transaction ref
$all_issue_dtls_ids = implode(",", $issue_dtls_arr);
$all_issue_dtls_id_cond=""; $issueCond="";
if($db_type==2 && count($issue_dtls_arr)>999)
{
	$issue_dtls_arr_chunk=array_chunk($issue_dtls_arr,999) ;
	foreach($issue_dtls_arr_chunk as $chunk_arr)
	{
		$chunk_arr_value=implode(",",$chunk_arr);
		$issueCond.="  id in($chunk_arr_value) or ";
	}

	$all_issue_dtls_id_cond.=" and (".chop($issueCond,'or ').")";
}
else
{
	$all_issue_dtls_id_cond=" and id in($all_issue_dtls_ids)";
}

$issue_dtls_sql = sql_select("select id, trans_id, store_name from inv_grey_fabric_issue_dtls  where  status_active =1 and is_deleted =0 $all_issue_dtls_id_cond");

foreach ($issue_dtls_sql as $val)
{
	$issue_dtls_trans_data[$val[csf("id")]]["trans_id"] 	= $val[csf("trans_id")];
	$issue_dtls_trans_data[$val[csf("id")]]["store_name"] 	= $val[csf("store_name")];
}

//rcv and issue return transaction ref
$all_rcv_n_iss_ret_dtls_ids = implode(",", $rcv_n_iss_ret_dtls_arr);
$all_rcv_n_iss_ret_dtls_id_cond=""; $rcvIssRetCond="";
if($db_type==2 && count($rcv_n_iss_ret_dtls_arr)>999)
{
	$rcv_n_iss_ret_dtls_arr_chunk=array_chunk($rcv_n_iss_ret_dtls_arr,999) ;
	foreach($rcv_n_iss_ret_dtls_arr_chunk as $chunk_arr)
	{
		$chunk_arr_value=implode(",",$chunk_arr);
		$rcvIssRetCond.=" a.id in($chunk_arr_value) or ";
	}

	$all_rcv_n_iss_ret_dtls_id_cond.=" and (".chop($rcvIssRetCond,'or ').")";
}
else
{
	$all_rcv_n_iss_ret_dtls_id_cond=" and a.id in($all_rcv_n_iss_ret_dtls_ids)";
}

$rcv_n_iss_ret_sql = sql_select("select a.id, a.trans_id,b.mst_id, b.store_id from pro_grey_prod_entry_dtls a, inv_transaction b  where  a.trans_id = b.id and a.status_active =1 and a.is_deleted =0 $all_rcv_n_iss_ret_dtls_id_cond");

foreach ($rcv_n_iss_ret_sql as $val)
{
	$rcv_n_iss_ret_dtls_trans[$val[csf("id")]]["from_trans"] 	= $val[csf("trans_id")];
	$rcv_n_iss_ret_dtls_trans[$val[csf("id")]]["mst_id"] 	= $val[csf("mst_id")];
	$rcv_n_iss_ret_dtls_trans[$val[csf("id")]]["store_id"] 		= $val[csf("store_id")];
}


//transfer transaction ref
$all_trans_dtls_ids = implode(",", $trans_dtls_arr);
$all_trans_dtls_id_cond=""; $transCond="";
if($db_type==2 && count($trans_dtls_arr)>999)
{
	$trans_dtls_arr_chunk=array_chunk($trans_dtls_arr,999) ;
	foreach($trans_dtls_arr_chunk as $chunk_arr)
	{
		$chunk_arr_value=implode(",",$chunk_arr);
		$transCond.="  id in($chunk_arr_value) or ";
	}

	$all_trans_dtls_id_cond.=" and (".chop($transCond,'or ').")";
}
else
{
	$all_trans_dtls_id_cond=" and id in($all_trans_dtls_ids)";
}

$transfer_sql_arr = sql_select("select id, trans_id, to_trans_id, from_store, to_store from inv_item_transfer_dtls  where  status_active =1 and is_deleted =0 and item_category = 13 $all_trans_dtls_id_cond");

foreach ($transfer_sql_arr as $val)
{
	$transfer_dtls_trans_arr[$val[csf("id")]]["from_trans"] 	= $val[csf("trans_id")];
	$transfer_dtls_trans_arr[$val[csf("id")]]["to_trans"] 		= $val[csf("to_trans_id")];

	$transfer_dtls_trans_arr[$val[csf("id")]]["from_store"] 	= $val[csf("from_store")];
	$transfer_dtls_trans_arr[$val[csf("id")]]["to_store"] 		= $val[csf("to_store")];
}

foreach ($barcode_data as $row)
{
	if($splited_data_arr[$row[csf("barcode_no")]] == "")
	{
		if($barcode_no_chk[$row[csf("barcode_no")]] =="")
		{
			$barcode_no_chk[$row[csf("barcode_no")]] = $row[csf("barcode_no")];
			$pre_store_id="";
		}

		if(($row[csf("entry_form")] == 58 || $row[csf("entry_form")] == 82 || $row[csf("entry_form")] == 83 || $row[csf("entry_form")] == 110 || $row[csf("entry_form")] == 180 || $row[csf("entry_form")] == 183) && $source_rcv_arr[$row[csf("barcode_no")]] =="")
		{
			$source_rcv_arr[$row[csf("barcode_no")]] = $row[csf("barcode_no")];
			echo "update pro_roll_details set re_transfer = 0 where id = ".$row[csf("id")] ." Entry_form=".$row[csf("entry_form")]."<br>";
		}
		else if(($row[csf("entry_form")] == 58 || $row[csf("entry_form")] == 82 || $row[csf("entry_form")] == 83 || $row[csf("entry_form")] == 110 || $row[csf("entry_form")] == 180 || $row[csf("entry_form")] == 183) && $source_rcv_arr[$row[csf("barcode_no")]] !="")
		{
			echo "update pro_roll_details set re_transfer = 1 where id = ".$row[csf("id")] ." Entry_form=".$row[csf("entry_form")]."<br>";
		}

		if($row[csf("entry_form")] == 61)
		{
			if($pre_store_id == "")
			{
				$pre_store_id = $issue_dtls_trans_data[$row[csf("dtls_id")]]["store_name"];
			}

			$issue_trans_id = $issue_dtls_trans_data[$row[csf("dtls_id")]]["trans_id"];

			echo "update inv_grey_fabric_issue_dtls set store_name = '$pre_store_id' where id = ".$row[csf("dtls_id")] ." Entry_form=".$row[csf("entry_form")]."<br>";
			echo "update inv_transaction set store_id = '$pre_store_id' where id = ".$issue_trans_id ." Entry_form=".$row[csf("entry_form")]."<br><br>";
		}

		if($row[csf("entry_form")] == 58 || $row[csf("entry_form")] == 84)
		{
			if($pre_store_id == "")
			{
				$pre_store_id = $rcv_n_iss_ret_dtls_trans[$row[csf("dtls_id")]]["store_id"];
			}
			$rcv_iss_ret_trans_id = $rcv_n_iss_ret_dtls_trans[$row[csf("dtls_id")]]["from_trans"];
			$rcv_iss_ret_mst_id = $rcv_n_iss_ret_dtls_trans[$row[csf("dtls_id")]]["mst_id"];

			if($row[csf("entry_form")] == 58)
			{
				echo "update inv_receive_master set store_id = '$pre_store_id' where id = ".$rcv_iss_ret_mst_id ." Entry_form=".$row[csf("entry_form")]."<br>";
			}
			echo "update inv_transaction set store_id = '$pre_store_id' where id = ".$rcv_iss_ret_trans_id ." Entry_form=".$row[csf("entry_form")]."<br><br>";
		}

		if($row[csf("entry_form")] == 82 || $row[csf("entry_form")] == 83 || $row[csf("entry_form")] == 110 || $row[csf("entry_form")] == 180 || $row[csf("entry_form")] == 183)
		{
			if($pre_store_id == "")
			{
				$pre_store_id = $transfer_dtls_trans_arr[$row[csf("dtls_id")]]["to_store"];
			}

			$from_trans_id = $transfer_dtls_trans_arr[$row[csf("dtls_id")]]["from_trans"];
			$to_trans_id = $transfer_dtls_trans_arr[$row[csf("dtls_id")]]["to_trans"];

			$from_store_id = $transfer_dtls_trans_arr[$row[csf("dtls_id")]]["from_store"];

			echo "update inv_item_transfer_dtls set from_store = '$from_store_id', to_store='$pre_store_id' where id = ".$row[csf("dtls_id")] ." Entry_form=".$row[csf("entry_form")]."<br>";

			if($row[csf("entry_form")] != 82)
			{
				echo "update inv_transaction set store_id = '$from_store_id' where id = ".$from_trans_id."<br>";
				echo "update inv_transaction set store_id = '$pre_store_id' where id = ".$to_trans_id;
			}
			echo "<br><br>";

			$pre_store_id = $from_store_id;
		}
	}
}



/*oci_commit($con);
echo "Success";
die;*/
?>