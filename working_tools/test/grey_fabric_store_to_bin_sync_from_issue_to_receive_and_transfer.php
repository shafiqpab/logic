<?
// 192.168.100.4/fakirfashion_erp/working_tools/test/grey_fabric_store_to_bin_sync_from_issue_to_receive_and_transfer.php?order=''&job=''&barcode=''

// 192.168.100.4/fakirfashion_erp/working_tools/test/grey_fabric_store_to_bin_sync_from_issue_to_receive_and_transfer.php
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../includes/common.php');
$user_id=1;
$con=connect();

/*$order = $_GET['order'];
$job = $_GET['job'];
$barcode = $_GET['barcode'];*/
// echo $order.'='.$job.'='.$barcode;die;


/*if (str_replace("'","",$order)=="" || str_replace("'","",$job)=="") 
{
	echo "Please Input Order and Job";
	disconnect($con);
	die;
}
$barcode_cond="";
if (str_replace("'","",$barcode)!="") 
{
	$barcode_cond=" and b.barcode_no=$barcode ";
}*/

$order="33812-D";
$job="FFL-23-00022";

$po_sql="SELECT b.barcode_no from WO_PO_BREAK_DOWN a, PRO_ROLL_DETAILS b
where a.id=b.PO_BREAKDOWN_ID and a.PO_NUMBER='$order' and a.JOB_NO_MST='$job' and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and b.ENTRY_FORM in(22,58,82,83,110,180,183)
group by b.barcode_no";
// echo $po_sql;die;
$po_sql_result=sql_select($po_sql);

execute_query("DELETE FROM INSERT_BARCODE_NO WHERE userid = $user_id and entry_form=777");
oci_commit($con);
foreach($po_sql_result as $val)
{
    if( $barcode_no_check[$val[csf('barcode_no')]] =="" )
    {
        $barcode_no_check[$val[csf('barcode_no')]]=$val[csf('barcode_no')];
        $barcodeno = $val[csf('barcode_no')];
        // echo "insert into INSERT_BARCODE_NO (userid, barcode_no, entry_form) values ($user_id,$barcodeno,777)<br>";
        $r_id=execute_query("insert into INSERT_BARCODE_NO (userid, barcode_no, entry_form) values ($user_id,$barcodeno,777)");
    }   
}
oci_commit($con);
//echo "Success";
//die;



$splited_sql="select barcode_no from  pro_roll_details where roll_split_from > 0  and status_active = 1";
//entry_form = 82  and
$splited_data = sql_select($splited_sql);
foreach ($splited_data as $row)
{
	$splited_data_arr[$row[csf("barcode_no")]] = $row[csf("barcode_no")];
}

$barcode_data=sql_select("SELECT b.id, b.entry_form, b.dtls_id, b.barcode_no, b.po_breakdown_id, b.booking_without_order from INSERT_BARCODE_NO a, pro_roll_details b 
where a.barcode_no=b.barcode_no and a.entry_form=777 and userid=1 and b.entry_form in (22,58,82,83,84,110,180,183,61) and b.status_active =1 and b.status_active =1 and b.barcode_no >0 order by b.barcode_no, b.id desc"); //and b.barcode_no in (22020137180)
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
if (!empty($issue_dtls_arr)) 
{
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

	$issue_dtls_sql = sql_select("SELECT id, trans_id, store_name, floor_id, room, rack, self from inv_grey_fabric_issue_dtls  where  status_active =1 and is_deleted =0 $all_issue_dtls_id_cond");

	foreach ($issue_dtls_sql as $val)
	{
		$issue_dtls_trans_data[$val[csf("id")]]["trans_id"] 	= $val[csf("trans_id")];
		$issue_dtls_trans_data[$val[csf("id")]]["store_name"] 	= $val[csf("store_name")];
		$issue_dtls_trans_data[$val[csf("id")]]["floor_id"] 	= $val[csf("floor_id")];
		$issue_dtls_trans_data[$val[csf("id")]]["room"] 		= $val[csf("room")];
		$issue_dtls_trans_data[$val[csf("id")]]["rack"] 		= $val[csf("rack")];
		$issue_dtls_trans_data[$val[csf("id")]]["self"] 		= $val[csf("self")];
	}
}

//rcv and issue return transaction ref
if (!empty($rcv_n_iss_ret_dtls_arr)) 
{
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

	$rcv_n_iss_ret_sql = sql_select("SELECT a.id, a.trans_id,b.mst_id, b.store_id, b.floor_id, b.room, b.rack, b.self from pro_grey_prod_entry_dtls a, inv_transaction b  where  a.trans_id = b.id and a.status_active =1 and a.is_deleted =0 $all_rcv_n_iss_ret_dtls_id_cond");

	foreach ($rcv_n_iss_ret_sql as $val)
	{
		$rcv_n_iss_ret_dtls_trans[$val[csf("id")]]["from_trans"] 	= $val[csf("trans_id")];
		$rcv_n_iss_ret_dtls_trans[$val[csf("id")]]["mst_id"] 		= $val[csf("mst_id")];
		$rcv_n_iss_ret_dtls_trans[$val[csf("id")]]["store_id"] 		= $val[csf("store_id")];
		$rcv_n_iss_ret_dtls_trans[$val[csf("id")]]["floor_id"] 		= $val[csf("floor_id")];
		$rcv_n_iss_ret_dtls_trans[$val[csf("id")]]["room"] 			= $val[csf("room")];
		$rcv_n_iss_ret_dtls_trans[$val[csf("id")]]["rack"] 			= $val[csf("rack")];
		$rcv_n_iss_ret_dtls_trans[$val[csf("id")]]["self"] 			= $val[csf("self")];
	}
}


//transfer transaction ref
if (!empty($trans_dtls_arr)) 
{
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

	$transfer_sql_arr = sql_select("SELECT id, trans_id, to_trans_id, from_store, to_store, floor_id, room, rack, shelf, to_floor_id, to_room, to_rack, to_shelf from inv_item_transfer_dtls  where  status_active =1 and is_deleted =0 and item_category = 13 $all_trans_dtls_id_cond");

	foreach ($transfer_sql_arr as $val)
	{
		$transfer_dtls_trans_arr[$val[csf("id")]]["from_trans"] 	= $val[csf("trans_id")];
		$transfer_dtls_trans_arr[$val[csf("id")]]["to_trans"] 		= $val[csf("to_trans_id")];

		$transfer_dtls_trans_arr[$val[csf("id")]]["from_store"] 	= $val[csf("from_store")];
		$transfer_dtls_trans_arr[$val[csf("id")]]["from_floor_id"] 	= $val[csf("floor_id")];
		$transfer_dtls_trans_arr[$val[csf("id")]]["from_room"] 		= $val[csf("room")];
		$transfer_dtls_trans_arr[$val[csf("id")]]["from_rack"] 		= $val[csf("rack")];
		$transfer_dtls_trans_arr[$val[csf("id")]]["from_shelf"] 	= $val[csf("shelf")];

		$transfer_dtls_trans_arr[$val[csf("id")]]["to_store"] 		= $val[csf("to_store")];
		$transfer_dtls_trans_arr[$val[csf("id")]]["to_floor_id"] 	= $val[csf("to_floor_id")];
		$transfer_dtls_trans_arr[$val[csf("id")]]["to_room"] 		= $val[csf("to_room")];
		$transfer_dtls_trans_arr[$val[csf("id")]]["to_rack"] 		= $val[csf("to_rack")];
		$transfer_dtls_trans_arr[$val[csf("id")]]["to_shelf"] 		= $val[csf("to_shelf")];
	}
}

foreach ($barcode_data as $row)
{
	if($splited_data_arr[$row[csf("barcode_no")]] == "")
	{
		if($barcode_no_chk[$row[csf("barcode_no")]] =="")
		{
			$barcode_no_chk[$row[csf("barcode_no")]] = $row[csf("barcode_no")];
			$pre_store_id="";
			$pre_floor_id="";
			$pre_room_id="";
			$pre_rack_id="";
			$pre_shelf_id="";
		}

		if($row[csf("entry_form")] == 61)
		{
			if($pre_store_id == "")
			{
				$pre_store_id = $issue_dtls_trans_data[$row[csf("dtls_id")]]["store_name"];
			}
			if($pre_floor_id == "")
			{
				$pre_floor_id = $issue_dtls_trans_data[$row[csf("dtls_id")]]["floor_id"];
			}
			if($pre_room_id == "")
			{
				$pre_room_id = $issue_dtls_trans_data[$row[csf("dtls_id")]]["room"];
			}
			if($pre_rack_id == "")
			{
				$pre_rack_id = $issue_dtls_trans_data[$row[csf("dtls_id")]]["rack"];
			}
			if($pre_shelf_id == "")
			{
				$pre_shelf_id = $issue_dtls_trans_data[$row[csf("dtls_id")]]["self"];
			}

			if($pre_store_id == 0 || $pre_store_id =="" || $pre_floor_id == 0 || $pre_floor_id == "" || $pre_room_id == 0 || $pre_room_id == "")
			{
				echo "Failed run script;<br>Ref Not Found in ".$row[csf("entry_form")] ." Page.<br>Barcode No : ".$row[csf("barcode_no")];
				oci_rollback($con);
				disconnect($con);
				die;
			}

			$issue_trans_id = $issue_dtls_trans_data[$row[csf("dtls_id")]]["trans_id"];

			//echo "UPDATE inv_grey_fabric_issue_dtls set store_name = '$pre_store_id', floor_id = '$pre_floor_id', room = '$pre_room_id', rack = '$pre_rack_id', self = '$pre_shelf_id' where id = ".$row[csf("dtls_id")] ." entry_form=".$row[csf("entry_form")]."<br>";
			//echo "UPDATE inv_transaction set store_id = '$pre_store_id', floor_id = '$pre_floor_id', room = '$pre_room_id', rack = '$pre_rack_id', self = '$pre_shelf_id' where id = ".$issue_trans_id ." entry_form=".$row[csf("entry_form")]."<br><br>";
			
			$rid1=execute_query("UPDATE inv_grey_fabric_issue_dtls set store_name = '$pre_store_id', floor_id = '$pre_floor_id', room = '$pre_room_id', rack = '$pre_rack_id', self = '$pre_shelf_id' where id = ".$row[csf("dtls_id")],0);
			$rid2=execute_query("UPDATE inv_transaction set store_id = '$pre_store_id', floor_id = '$pre_floor_id', room = '$pre_room_id', rack = '$pre_rack_id', self = '$pre_shelf_id' where id = ".$issue_trans_id,0);
			if($rid1 ==0 && $rid2 ==0)
			{
				echo "Failed run script <br>";
				echo "UPDATE inv_grey_fabric_issue_dtls set store_name = '$pre_store_id', floor_id = '$pre_floor_id', room = '$pre_room_id', rack = '$pre_rack_id', self = '$pre_shelf_id' where id = ".$row[csf("dtls_id")]."<br>";
				echo "UPDATE inv_transaction set store_id = '$pre_store_id', floor_id = '$pre_floor_id', room = '$pre_room_id', rack = '$pre_rack_id', self = '$pre_shelf_id' where id = ".$issue_trans_id ."<br><br>";
				oci_rollback($con);
				disconnect($con);
				die;
			}
		}

		if($row[csf("entry_form")] == 58 || $row[csf("entry_form")] == 84)
		{
			if($pre_store_id == "")
			{
				$pre_store_id = $rcv_n_iss_ret_dtls_trans[$row[csf("dtls_id")]]["store_id"];
			}
			if($pre_floor_id == "")
			{
				$pre_floor_id = $rcv_n_iss_ret_dtls_trans[$row[csf("dtls_id")]]["floor_id"];
			}
			if($pre_room_id == "")
			{
				$pre_room_id = $rcv_n_iss_ret_dtls_trans[$row[csf("dtls_id")]]["room"];
			}
			if($pre_rack_id == "")
			{
				$pre_rack_id = $rcv_n_iss_ret_dtls_trans[$row[csf("dtls_id")]]["rack"];
			}
			if($pre_shelf_id == "")
			{
				$pre_shelf_id = $rcv_n_iss_ret_dtls_trans[$row[csf("dtls_id")]]["self"];
			}
			
			if($pre_store_id == 0 || $pre_store_id =="" || $pre_floor_id == 0 || $pre_floor_id == "" || $pre_room_id == 0 || $pre_room_id == "")
			{
				echo "Failed run script;<br>Ref Not Found in ".$row[csf("entry_form")] ." Page.<br>Barcode No : ".$row[csf("barcode_no")];
				oci_rollback($con);
				disconnect($con);
				die;
			}

			$rcv_iss_ret_trans_id = $rcv_n_iss_ret_dtls_trans[$row[csf("dtls_id")]]["from_trans"];
			$rcv_iss_ret_mst_id = $rcv_n_iss_ret_dtls_trans[$row[csf("dtls_id")]]["mst_id"];

			if($row[csf("entry_form")] == 58)
			{
				//echo "UPDATE inv_receive_master set store_id = '$pre_store_id', floor_id = '$pre_floor_id', room = '$pre_room_id', rack = '$pre_rack_id', self = '$pre_shelf_id' where id = ".$rcv_iss_ret_mst_id ." entry_form=".$row[csf("entry_form")]."<br>";
				
				$rid3=execute_query("UPDATE inv_receive_master set store_id = '$pre_store_id' where id = ".$rcv_iss_ret_mst_id ." and entry_form=".$row[csf("entry_form")],0);
				if($rid3 ==0)
				{
					echo "Failed run script <br>";
					echo "UPDATE inv_receive_master set store_id = '$pre_store_id' where id = ".$rcv_iss_ret_mst_id ." and entry_form=".$row[csf("entry_form")] ."<br>";
					oci_rollback($con);
					disconnect($con);
					die;
				}
			}
			//echo "UPDATE inv_transaction set store_id = '$pre_store_id', floor_id = '$pre_floor_id', room = '$pre_room_id', rack = '$pre_rack_id', self = '$pre_shelf_id' where id = ".$rcv_iss_ret_trans_id ." entry_form=".$row[csf("entry_form")]."<br><br>";
			
			$rid4=execute_query("UPDATE inv_transaction set store_id = '$pre_store_id', floor_id = '$pre_floor_id', room = '$pre_room_id', rack = '$pre_rack_id', self = '$pre_shelf_id' where id = ".$rcv_iss_ret_trans_id,0);
			if($rid4 ==0)
			{
				echo "Failed run script <br>";
				echo "UPDATE inv_transaction set store_id = '$pre_store_id', floor_id = '$pre_floor_id', room = '$pre_room_id', rack = '$pre_rack_id', self = '$pre_shelf_id' where id = ".$rcv_iss_ret_trans_id."<br><br>";
				oci_rollback($con);
				disconnect($con);
				die;
			}
			$rid44=execute_query("UPDATE pro_grey_prod_entry_dtls set floor_id = '$pre_floor_id', room = '$pre_room_id', rack = '$pre_rack_id', self = '$pre_shelf_id' where id = ".$row[csf("dtls_id")],0);
			if($rid44 ==0)
			{
				echo "Failed run script <br>";
				echo "UPDATE pro_grey_prod_entry_dtls set floor_id = '$pre_floor_id', room = '$pre_room_id', rack = '$pre_rack_id', self = '$pre_shelf_id' where id = ".$row[csf("dtls_id")]."<br><br>";
				oci_rollback($con);
				disconnect($con);
				die;
			}
		}

		if($row[csf("entry_form")] == 82 || $row[csf("entry_form")] == 83 || $row[csf("entry_form")] == 110 || $row[csf("entry_form")] == 180 || $row[csf("entry_form")] == 183)
		{
			if($pre_store_id == "")
			{
				$pre_store_id = $transfer_dtls_trans_arr[$row[csf("dtls_id")]]["to_store"];
			}
			if($pre_floor_id == "")
			{
				$pre_floor_id = $transfer_dtls_trans_arr[$row[csf("dtls_id")]]["to_floor_id"];
			}
			if($pre_room_id == "")
			{
				$pre_room_id = $transfer_dtls_trans_arr[$row[csf("dtls_id")]]["to_room"];
			}
			if($pre_rack_id == "")
			{
				$pre_rack_id = $transfer_dtls_trans_arr[$row[csf("dtls_id")]]["to_rack"];
			}
			if($pre_shelf_id == "")
			{
				$pre_shelf_id = $transfer_dtls_trans_arr[$row[csf("dtls_id")]]["to_shelf"];
			}

			if($pre_store_id == 0 || $pre_store_id =="" || $pre_floor_id == 0 || $pre_floor_id == "" || $pre_room_id == 0 || $pre_room_id == "")
			{
				echo "Failed run script;<br>Ref Not Found in ".$row[csf("entry_form")] ." Page.<br>Barcode No : ".$row[csf("barcode_no")];
				oci_rollback($con);
				disconnect($con);
				die;
			}

			$from_trans_id = $transfer_dtls_trans_arr[$row[csf("dtls_id")]]["from_trans"];
			$to_trans_id = $transfer_dtls_trans_arr[$row[csf("dtls_id")]]["to_trans"];

			$from_store_id = $transfer_dtls_trans_arr[$row[csf("dtls_id")]]["from_store"];
			$from_floor_id = $transfer_dtls_trans_arr[$row[csf("dtls_id")]]["from_floor_id"];
			$from_room_id = $transfer_dtls_trans_arr[$row[csf("dtls_id")]]["from_room"];
			$from_rack_id = $transfer_dtls_trans_arr[$row[csf("dtls_id")]]["from_rack"];
			$from_shelf_id = $transfer_dtls_trans_arr[$row[csf("dtls_id")]]["from_shelf"];

			//echo "UPDATE inv_item_transfer_dtls set from_store = '$from_store_id', floor_id = '$from_floor_id', room = '$from_room_id', rack = '$from_rack_id', shelf = '$from_shelf_id', to_store='$pre_store_id', to_floor_id = '$pre_floor_id', to_room = '$pre_room_id', to_rack = '$pre_rack_id', to_shelf = '$pre_shelf_id' where id = ".$row[csf("dtls_id")] ." entry_form=".$row[csf("entry_form")]."<br>";
			
			$rid5=execute_query("UPDATE inv_item_transfer_dtls set from_store = '$from_store_id', floor_id = '$from_floor_id', room = '$from_room_id', rack = '$from_rack_id', shelf = '$from_shelf_id', to_store='$pre_store_id', to_floor_id = '$pre_floor_id', to_room = '$pre_room_id', to_rack = '$pre_rack_id', to_shelf = '$pre_shelf_id' where id = ".$row[csf("dtls_id")],0);
			if($rid5 ==0)
			{
				echo "Failed run script <br>";
				echo "UPDATE inv_item_transfer_dtls set from_store = '$from_store_id', floor_id = '$from_floor_id', room = '$from_room_id', rack = '$from_rack_id', shelf = '$from_shelf_id', to_store='$pre_store_id', to_floor_id = '$pre_floor_id', to_room = '$pre_room_id', to_rack = '$pre_rack_id', to_shelf = '$pre_shelf_id' where id = ".$row[csf("dtls_id")] ."<br>";

				oci_rollback($con);
				disconnect($con);
				die;
			}

			//echo "UPDATE inv_transaction set store_id = '$from_store_id', floor_id = '$from_floor_id', room = '$from_room_id', rack = '$from_rack_id', self = '$from_shelf_id' where id = ".$from_trans_id."<br>";
			//echo "UPDATE inv_transaction set store_id = '$pre_store_id', floor_id = '$pre_floor_id', room = '$pre_room_id', rack = '$pre_rack_id', self = '$pre_shelf_id' where id = ".$to_trans_id;
			
			$rid6=execute_query("UPDATE inv_transaction set store_id = '$from_store_id', floor_id = '$from_floor_id', room = '$from_room_id', rack = '$from_rack_id', self = '$from_shelf_id' where id = ".$from_trans_id,0);
			$rid7=execute_query("UPDATE inv_transaction set store_id = '$pre_store_id', floor_id = '$pre_floor_id', room = '$pre_room_id', rack = '$pre_rack_id', self = '$pre_shelf_id' where id = ".$to_trans_id,0);
			if($rid6 ==0 && $rid7 ==0)
			{
				echo "Failed run script <br>";
				echo "UPDATE inv_transaction set store_id = '$from_store_id', floor_id = '$from_floor_id', room = '$from_room_id', rack = '$from_rack_id', self = '$from_shelf_id' where id = ".$from_trans_id."<br>";
				echo "UPDATE inv_transaction set store_id = '$pre_store_id', floor_id = '$pre_floor_id', room = '$pre_room_id', rack = '$pre_rack_id', self = '$pre_shelf_id' where id = ".$to_trans_id;

				oci_rollback($con);
				disconnect($con);
				die;
			}
			//echo "<br><br>";

			$pre_store_id 	= $from_store_id;
			$pre_floor_id 	= $from_floor_id;
			$pre_room_id 	= $from_room_id;
			$pre_rack_id 	= $from_rack_id;
			$pre_shelf_id 	= $from_shelf_id;
		}
	}
}

execute_query("DELETE FROM INSERT_BARCODE_NO WHERE userid = $user_id and entry_form=777");
oci_commit($con);
echo "Success";
die;
?>