<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();
 
//$barcode_nos = $_GET['barcode_no'];
$barcode_nos = "18020867444";
if($barcode_nos=="")
{
	echo "No barcode found";
	die;
}
$splited_sql="select barcode_no from  pro_roll_details where roll_split_from > 0  and status_active = 1 and barcode_no in($barcode_nos)";
//entry_form = 82  and
$splited_data = sql_select($splited_sql);
foreach ($splited_data as $row)
{
	$splited_data_arr[$row[csf("barcode_no")]] = $row[csf("barcode_no")];
}

$splited_mother_sql="select barcode_no from  pro_roll_split where status_active =1 and is_deleted=0 and barcode_no in($barcode_nos)";
//entry_form = 82  and
$splited_mother_data = sql_select($splited_mother_sql);
foreach ($splited_mother_data as $row)
{
	$splited_data_arr[$row[csf("barcode_no")]] = $row[csf("barcode_no")];
}

$non_ord_ref_sql="select barcode_no from  pro_roll_details where booking_without_order=1 and status_active =1 and is_deleted=0 and barcode_no in($barcode_nos)";
//entry_form = 82  and
$non_ord_ref_sql = sql_select($non_ord_ref_sql);
foreach ($non_ord_ref_sql as $row)
{
	$non_ord_ref_data[$row[csf("barcode_no")]] = $row[csf("barcode_no")];
}

$barcode_data=sql_select("select id, entry_form, dtls_id, barcode_no, po_breakdown_id, booking_without_order,re_transfer from pro_roll_details where entry_form in (2,56,22,58,82,83,84,110,180,183,61,133) and status_active =1 and status_active =1 and barcode_no >0 and barcode_no in($barcode_nos) order by barcode_no, id desc");

//20020265196,20020191342,20020183921

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
		else if($row[csf("entry_form")] == 82 || $row[csf("entry_form")] == 83 || $row[csf("entry_form")] == 110 || $row[csf("entry_form")] == 180 || $row[csf("entry_form")] == 183 || $row[csf("entry_form")] == 133)
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

$transfer_sql_arr = sql_select("select id, trans_id, to_trans_id, from_store, to_store, from_order_id, to_order_id from inv_item_transfer_dtls  where  status_active =1 and is_deleted =0 and item_category = 13 $all_trans_dtls_id_cond");

foreach ($transfer_sql_arr as $val)
{
	$transfer_dtls_trans_arr[$val[csf("id")]]["from_trans"] 	= $val[csf("trans_id")];
	$transfer_dtls_trans_arr[$val[csf("id")]]["to_trans"] 		= $val[csf("to_trans_id")];

	$transfer_dtls_trans_arr[$val[csf("id")]]["from_store"] 	= $val[csf("from_store")];
	$transfer_dtls_trans_arr[$val[csf("id")]]["to_store"] 		= $val[csf("to_store")];

	$transfer_dtls_trans_arr[$val[csf("id")]]["from_order_id"] 	= $val[csf("from_order_id")];
	$transfer_dtls_trans_arr[$val[csf("id")]]["to_order_id"] 	= $val[csf("to_order_id")];
}

foreach ($barcode_data as $row)
{
	if($non_ord_ref_data[$row[csf("barcode_no")]] == "")
	{
		if($splited_data_arr[$row[csf("barcode_no")]] == "")
		{
			if($barcode_no_chk[$row[csf("barcode_no")]] =="")
			{
				$barcode_no_chk[$row[csf("barcode_no")]] = $row[csf("barcode_no")];
				$pre_store_id="";
			}

			if(($row[csf("entry_form")] == 58 || $row[csf("entry_form")] == 82 || $row[csf("entry_form")] == 83 || $row[csf("entry_form")] == 110 || $row[csf("entry_form")] == 180 || $row[csf("entry_form")] == 183 || $row[csf("entry_form")] == 133) && $source_rcv_arr[$row[csf("barcode_no")]] =="")
			{
				$source_rcv_arr[$row[csf("barcode_no")]] = $row[csf("barcode_no")];
				//echo "update pro_roll_details set re_transfer = 0 where id = ".$row[csf("id")] ." Entry_form=".$row[csf("entry_form")]."<br>";
				if ($row[csf("re_transfer")]!=0) 
				{
					$rID=execute_query("update pro_roll_details set re_transfer = 0 where id = ".$row[csf("id")], 0);
					if($rID == 0)
					{
						echo "Failed run script <br>";
						echo "update pro_roll_details set re_transfer = 0 where id = ".$row[csf("id")]."<br>";
						oci_rollback($con);
						disconnect($con);
						die;

					}
				}
				
			}
			else if(($row[csf("entry_form")] == 58 || $row[csf("entry_form")] == 82 || $row[csf("entry_form")] == 83 || $row[csf("entry_form")] == 110 || $row[csf("entry_form")] == 180 || $row[csf("entry_form")] == 183) && $source_rcv_arr[$row[csf("barcode_no")]] !="")
			{
				//echo "update pro_roll_details set re_transfer = 1 where id = ".$row[csf("id")] ." Entry_form=".$row[csf("entry_form")]."<br>";
				if ($row[csf("re_transfer")]!=1) 
				{
					$rID1=execute_query("update pro_roll_details set re_transfer = 1 where id = ".$row[csf("id")], 0);
					if($rID1 ==0)
					{
						echo "Failed run script <br>";
						echo "update pro_roll_details set re_transfer = 1 where id = ".$row[csf("id")] ."<br>";
						oci_rollback($con);
						disconnect($con);
						die;
					}
				}
			}

			if($row[csf("entry_form")] == 61)
			{
				if($pre_store_id == "")
				{
					$pre_store_id = $issue_dtls_trans_data[$row[csf("dtls_id")]]["store_name"];

					if($pre_store_id == 0 || $pre_store_id =="")
					{
						echo "Failed run script;<br>Store Not Found in ".$entry_form_reference[$row[csf("entry_form")]] ." Page.<br>Barcode No : ".$row[csf("barcode_no")];
						oci_rollback($con);
						disconnect($con);
						die;
					}
				}

				$issue_trans_id = $issue_dtls_trans_data[$row[csf("dtls_id")]]["trans_id"];

				//echo "update inv_grey_fabric_issue_dtls set store_name = '$pre_store_id' where id = ".$row[csf("dtls_id")] ." Entry_form=".$row[csf("entry_form")]."<br>";
				//echo "update inv_transaction set store_id = '$pre_store_id' where id = ".$issue_trans_id ." Entry_form=".$row[csf("entry_form")]."<br><br>";

				$rID2=execute_query("update inv_grey_fabric_issue_dtls set store_name ='$pre_store_id' where id =".$row[csf("dtls_id")],0);
				$rID3=execute_query("update inv_transaction set store_id = '$pre_store_id' where id = ".$issue_trans_id ,0);

				if($rID2 ==0 && $rID3 ==0)
				{
					echo "Failed run script <br>";
					echo "update inv_grey_fabric_issue_dtls set store_name = '$pre_store_id' where id = ".$row[csf("dtls_id")]."<br>";
					echo "update inv_transaction set store_id = '$pre_store_id' where id = ".$issue_trans_id ."<br><br>";
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

					if($pre_store_id == 0 || $pre_store_id =="")
					{
						echo "Failed run script;<br>Store Not Found in ".$entry_form_reference[$row[csf("entry_form")]] ." Page.<br>Barcode No : ".$row[csf("barcode_no")];
						oci_rollback($con);
						disconnect($con);
						die;
					}
				}
				$rcv_iss_ret_trans_id = $rcv_n_iss_ret_dtls_trans[$row[csf("dtls_id")]]["from_trans"];
				$rcv_iss_ret_mst_id = $rcv_n_iss_ret_dtls_trans[$row[csf("dtls_id")]]["mst_id"];

				if($row[csf("entry_form")] == 58)
				{
					//echo "update inv_receive_master set store_id = '$pre_store_id' where id = ".$rcv_iss_ret_mst_id ." Entry_form=".$row[csf("entry_form")]."<br>";

					$rID4=execute_query("update inv_receive_master set store_id = '$pre_store_id' where id = ".$rcv_iss_ret_mst_id,0);
					if($rID4 ==0)
					{
						echo "Failed run script <br>";
						echo "update inv_receive_master set store_id = '$pre_store_id' where id = ".$rcv_iss_ret_mst_id ."<br>";
						oci_rollback($con);
						disconnect($con);
						die;
					}
				}
				
				//echo "update inv_transaction set store_id = '$pre_store_id' where id = ".$rcv_iss_ret_trans_id ." Entry_form=".$row[csf("entry_form")]."<br><br>";

				$rID5=execute_query("update inv_transaction set store_id = '$pre_store_id' where id = ".$rcv_iss_ret_trans_id ,0);
				if($rID5 ==0)
				{
					echo "Failed run script <br>";
					echo "update inv_transaction set store_id = '$pre_store_id' where id = ".$rcv_iss_ret_trans_id."<br><br>";
					oci_rollback($con);
					disconnect($con);
					die;
				}
			}

			if($row[csf("entry_form")] == 82 || $row[csf("entry_form")] == 83 || $row[csf("entry_form")] == 110 || $row[csf("entry_form")] == 180 || $row[csf("entry_form")] == 183 || $row[csf("entry_form")] == 133)
			{
				if($pre_store_id == "")
				{
					$pre_store_id = $transfer_dtls_trans_arr[$row[csf("dtls_id")]]["to_store"];

					if($pre_store_id == 0 || $pre_store_id =="")
					{
						echo "Failed run script;<br>Store Not Found in ".$entry_form_reference[$row[csf("entry_form")]] ." Page.<br>Barcode No : ".$row[csf("barcode_no")];
						oci_rollback($con);
						disconnect($con);
						die;
					}
				}

				$from_trans_id = $transfer_dtls_trans_arr[$row[csf("dtls_id")]]["from_trans"];
				$to_trans_id = $transfer_dtls_trans_arr[$row[csf("dtls_id")]]["to_trans"];

				$from_store_id = $transfer_dtls_trans_arr[$row[csf("dtls_id")]]["from_store"];

				//echo "update inv_item_transfer_dtls set from_store = '$from_store_id', to_store='$pre_store_id' where id = ".$row[csf("dtls_id")] ." Entry_form=".$row[csf("entry_form")]."<br>";

				$rID6=execute_query("update inv_item_transfer_dtls set from_store = '$from_store_id', to_store='$pre_store_id' where id = ".$row[csf("dtls_id")],0);
				if($rID6 ==0)
				{
					echo "Failed run script <br>";
					echo "update inv_item_transfer_dtls set from_store = '$from_store_id', to_store='$pre_store_id' where id = ".$row[csf("dtls_id")] ."<br>";

					oci_rollback($con);
					disconnect($con);
					die;
				}

				if($row[csf("entry_form")] != 82)
				{
					//echo "update inv_transaction set store_id = '$from_store_id' where id = ".$from_trans_id."<br>";
					//echo "update inv_transaction set store_id = '$pre_store_id' where id = ".$to_trans_id;

					$rID7=execute_query("update inv_transaction set store_id = '$from_store_id' where id = ".$from_trans_id,0);
					$rID8=execute_query("update inv_transaction set store_id = '$pre_store_id' where id = ".$to_trans_id,0);

					if($rID7 ==0 && $rID8 ==0)
					{
						echo "Failed run script <br>";
						echo "update inv_transaction set store_id = '$from_store_id' where id = ".$from_trans_id."<br>";
						echo "update inv_transaction set store_id = '$pre_store_id' where id = ".$to_trans_id;

						oci_rollback($con);
						disconnect($con);
						die;
					}
				}
				else
				{
					if($from_trans_id && $to_trans_id)
					{
						//echo "update inv_transaction set store_id = '$from_store_id' where id = ".$from_trans_id."<br>";
						//echo "update inv_transaction set store_id = '$pre_store_id' where id = ".$to_trans_id;

						$rID7=execute_query("update inv_transaction set store_id = '$from_store_id' where id = ".$from_trans_id,0);
						$rID8=execute_query("update inv_transaction set store_id = '$pre_store_id' where id = ".$to_trans_id,0);

						if($rID7 ==0 && $rID8 ==0)
						{
							echo "Failed run script <br>";
							echo "update inv_transaction set store_id = '$from_store_id' where id = ".$from_trans_id."<br>";
							echo "update inv_transaction set store_id = '$pre_store_id' where id = ".$to_trans_id;

							oci_rollback($con);
							disconnect($con);
							die;
						}
					}
				}
				echo "<br><br>";

				$pre_store_id = $from_store_id;
			}

			// PO update starts here ------------------>>>>>>>>>>>>>>>>>>>>>>>>>>>>

			if($po_update_chk_arr[$row[csf("barcode_no")]] =="")
			{
				$po_update_chk_arr[$row[csf("barcode_no")]] = $row[csf("barcode_no")];
				$po_breakdown_id = $row[csf("po_breakdown_id")];
			}
			else
			{
				if($po_breakdown_id)
				{

					if ($row[csf("po_breakdown_id")]!=$po_breakdown_id) 
					{
						//echo $row[csf("entry_form")]."= update pro_roll_details set po_breakdown_id = $po_breakdown_id where id = ".$row[csf("id")]."<br>";
						$rID9=execute_query("update pro_roll_details set po_breakdown_id = $po_breakdown_id where id = ".$row[csf("id")], 0);
						if($rID9 == 0)
						{
							echo "Failed run script <br>";
							echo "update pro_roll_details set po_breakdown_id = $po_breakdown_id where id = ".$row[csf("id")]."<br>";
							oci_rollback($con);
							disconnect($con);
							die;
						}
					}
				}

				if($row[csf("entry_form")] == 83 || $row[csf("entry_form")] == 82)
				{
					$from_order_id = $transfer_dtls_trans_arr[$row[csf("dtls_id")]]["from_order_id"];
					//echo "update inv_item_transfer_dtls set from_order_id = $from_order_id, to_order_id=$po_breakdown_id where id = ".$row[csf("dtls_id")]."<br>";



					if($from_order_id ==0)
					{
						echo "Failed run script <br>";
						echo "Order not found " ."<br>";

						oci_rollback($con);
						disconnect($con);
						die;
					}




					if ($transfer_dtls_trans_arr[$row[csf("dtls_id")]]["to_order_id"]!=$po_breakdown_id) 
					{
						$rID10=execute_query("update inv_item_transfer_dtls set to_order_id=$po_breakdown_id where id = ".$row[csf("dtls_id")],0);

						if($rID10 ==0)
						{
							echo "Failed run script <br>";
							echo "update inv_item_transfer_dtls set to_order_id=$po_breakdown_id where id = ".$row[csf("dtls_id")] ."<br>";

							oci_rollback($con);
							disconnect($con);
							die;
						}
					}
					

					if($from_trans_id && $to_trans_id)
					{
						/*echo $row[csf("entry_form")]."= update order_wise_pro_details set po_breakdown_id = $from_order_id where dtls_id =".$row[csf("dtls_id")]." and entry_form=".$row[csf("entry_form")]." and  trans_id = ".$from_trans_id 
						."<br>".$row[csf("entry_form")]."= update order_wise_pro_details set po_breakdown_id = $po_breakdown_id where dtls_id =".$row[csf("dtls_id")]." and  entry_form=".$row[csf("entry_form")]." and trans_id = ".$to_trans_id."<br>";*/

						$rID11=execute_query("update order_wise_pro_details set po_breakdown_id = $from_order_id where dtls_id =".$row[csf("dtls_id")]." and entry_form=".$row[csf("entry_form")]." and  trans_id = ".$from_trans_id,0);
						$rID12=execute_query("update order_wise_pro_details set po_breakdown_id = $po_breakdown_id where dtls_id =".$row[csf("dtls_id")]." and  entry_form=".$row[csf("entry_form")]." and trans_id = ".$to_trans_id,0);

						if($rID11==0 || $rID12==0)
						{
							echo "Failed run script <br>";
							echo "update order_wise_pro_details set po_breakdown_id = $from_order_id where dtls_id =".$row[csf("dtls_id")]." and entry_form=".$row[csf("entry_form")]." and  trans_id = ".$from_trans_id 
							."<br>".
							"update order_wise_pro_details set po_breakdown_id = $po_breakdown_id where dtls_id =".$row[csf("dtls_id")]." and  entry_form=".$row[csf("entry_form")]." and trans_id = ".$to_trans_id."<br>";

							oci_rollback($con);
							disconnect($con);
							die;
						}
					}

					

					$po_breakdown_id = $from_order_id;
					
				}
				else if($row[csf("entry_form")] == 2 || $row[csf("entry_form")] == 22 || $row[csf("entry_form")] == 58 || $row[csf("entry_form")] == 84)
				{
					//echo $row[csf("entry_form")]."= update pro_grey_prod_entry_dtls set order_id=$po_breakdown_id where id = ".$row[csf("dtls_id")] ."<br>";
					$rID10=execute_query("update pro_grey_prod_entry_dtls set order_id=$po_breakdown_id where id = ".$row[csf("dtls_id")],0);
					if($rID10 ==0)
					{
						echo "Failed run script <br>";
						echo "update pro_grey_prod_entry_dtls set order_id=$po_breakdown_id where id = ".$row[csf("dtls_id")] ."<br>";

						oci_rollback($con);
						disconnect($con);
						die;
					}

					//echo $row[csf("entry_form")]."= update order_wise_pro_details set po_breakdown_id = $po_breakdown_id where dtls_id =".$row[csf("dtls_id")]." and  entry_form=".$row[csf("entry_form")] ."<br>";

					$rID12=execute_query("update order_wise_pro_details set po_breakdown_id = $po_breakdown_id where dtls_id =".$row[csf("dtls_id")]." and  entry_form=".$row[csf("entry_form")],0);
					if($rID12 ==0)
					{
						echo "Failed run script <br>";
						echo "update order_wise_pro_details set po_breakdown_id = $po_breakdown_id where dtls_id =".$row[csf("dtls_id")]." and  entry_form=".$row[csf("entry_form")] ."<br>";

						oci_rollback($con);
						disconnect($con);
						die;
					}
				}
				else if($row[csf("entry_form")] == 61)
				{
					//echo "61= update order_wise_pro_details set po_breakdown_id = $po_breakdown_id where dtls_id =".$row[csf("dtls_id")]." and  entry_form=61" ."<br>";
					$rID12=execute_query("update order_wise_pro_details set po_breakdown_id = $po_breakdown_id where dtls_id =".$row[csf("dtls_id")]." and  entry_form=61",0);

					if($rID12 ==0)
					{
						echo "Failed run script <br>";
						echo "update order_wise_pro_details set po_breakdown_id = $po_breakdown_id where dtls_id =".$row[csf("dtls_id")]." and  entry_form=61" ."<br>";

						oci_rollback($con);
						disconnect($con);
						die;
					}
				}
				else if($row[csf("entry_form")] == 56)
				{
					//echo "56= update pro_grey_prod_delivery_dtls set order_id=$po_breakdown_id where id = ".$row[csf("dtls_id")] ."<br>";

					$rID10=execute_query("update pro_grey_prod_delivery_dtls set order_id=$po_breakdown_id where id = ".$row[csf("dtls_id")],0);
					if($rID10 ==0)
					{
						echo "Failed run script <br>";
						echo "update pro_grey_prod_delivery_dtls set order_id=$po_breakdown_id where id = ".$row[csf("dtls_id")] ."<br>";

						oci_rollback($con);
						disconnect($con);
						die;
					}
				}
			}
		}
		else
		{
			$split_or_splited_barcode[$row[csf("barcode_no")]] =$row[csf("barcode_no")];
		}
	}
	else
	{
		$non_ord_barcode[$row[csf("barcode_no")]] =$row[csf("barcode_no")];
	}
}


if(!empty($non_ord_barcode))
{
	echo "Non order barcodes not updated here :".implode(",", $non_ord_barcode);
	oci_rollback($con);
	disconnect($con);
	die;
}

if(!empty($split_or_splited_barcode))
{
	echo "Split or splited barcodes not updated here :".implode(",", $split_or_splited_barcode);
	oci_rollback($con);
	disconnect($con);
	die;
}

oci_rollback($con);
disconnect($con);
die;

/*oci_commit($con);
echo "Success";
die;*/
?>