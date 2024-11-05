<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
die;
$con=connect();

$sql="select a.barcode_no, --count(a.barcode_no)--
a.po_breakdown_id, b.entry_form, b.dtls_id, b.id, b.po_breakdown_id as next_po
from pro_roll_details a, pro_roll_details b
where a.entry_form in (183) and a.re_transfer=0 and a.barcode_no=b.barcode_no and b.id> a.id and b.status_active=1 and a.status_active=1 and b.entry_form !=64
and a.barcode_no not in (19020087901,19020087902,19020087925,19020088601,19020089711,19020089712,19020089713,19020090530,19020091189,19020091527,19020092126,19020092185,19020092186,19020092371,19020093477,19020093941,19020094481,19020094650,19020094798,19020094799,19020094950,19020095728,19020096521,19020096767,19020103680,19020502860,19020502861,19020503526,20020448692,20020481673,21020042439,21020163247)

--group by a.barcode_no
--having count(a.barcode_no) >2
order by a.barcode_no";

$sql_data = sql_select($sql);
$issue_dtls_ids_arr=array();
foreach ($sql_data as $row) 
{
	if($row[csf("entry_form")]==61){
		$issue_dtls_ids_arr[$row[csf("dtls_id")]] = $row[csf("dtls_id")];
	}
}

$issue_dtls_ids_arr=array_filter($issue_dtls_ids_arr);

if(!empty($issue_dtls_ids_arr))
{
	$dtlsCond = $dtls_id_cond = ""; 
	$dtlsCond_1 = $dtls_id_cond_1 = ""; 
	$all_issue_dtls_ids = implode(",", $issue_dtls_ids_arr);
	if($db_type==2 && count($issue_dtls_ids_arr)>999)
	{
		$issue_dtls_ids_arr_chunk=array_chunk($issue_dtls_ids_arr,999) ;
		foreach($issue_dtls_ids_arr_chunk as $chunk_arr)
		{
			$dtlsCond.=" a.id in(".implode(",",$chunk_arr).") or ";
			$dtlsCond_1.=" dtls_id in(".implode(",",$chunk_arr).") or ";
		}
				
		$dtls_id_cond.=" and (".chop($dtlsCond,'or ').")";			
		$dtls_id_cond_1.=" and (".chop($dtlsCond_1,'or ').")";			
		
	}
	else
	{ 	
		
		$dtls_id_cond=" and a.id in($all_issue_dtls_ids)";
		$dtls_id_cond_1=" and dtls_id in($all_issue_dtls_ids)";
	}

	$issue_sql="select b.barcode_no, a.id, a.issue_qnty, a.prod_id, a.trans_id, a.inserted_by from inv_grey_fabric_issue_dtls a, pro_roll_details b where a.id=b.dtls_id and b.entry_form=61 and a.mst_id= b.mst_id and a.status_active=1 and b.status_active=1 $dtls_id_cond";

	$issue_sql_data = sql_select($issue_sql);
	foreach ($issue_sql_data as $row) 
	{
		$issue_ref_arr[$row[csf("barcode_no")]][$row[csf("id")]]["prod_id"] = $row[csf("prod_id")];
		$issue_ref_arr[$row[csf("barcode_no")]][$row[csf("id")]]["issue_qnty"] = $row[csf("issue_qnty")];
		$issue_ref_arr[$row[csf("barcode_no")]][$row[csf("id")]]["trans_id"] = $row[csf("trans_id")];
		$issue_ref_arr[$row[csf("barcode_no")]][$row[csf("id")]]["inserted_by"] = $row[csf("inserted_by")];
	}

	$already_inserted_issue_sql = sql_select("select dtls_id from order_wise_pro_details where entry_form=61 and status_active=1 $dtls_id_cond_1");
	foreach ($already_inserted_issue_sql as $row) 
	{
		$already_inserted_data[$row[csf("dtls_id")]] = $row[csf("dtls_id")];
	}
	
}
	

$field_array_proportionate = "id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, quantity, cause_of_insert, inserted_by, insert_date";
$insertQry="";

foreach ($sql_data as  $row) 
{
	//echo "UPDATE pro_roll_details set booking_without_order=0 where id= ".$row[csf("id")]."  <br />";
	$update_roll_table=execute_query("UPDATE pro_roll_details set booking_without_order=0 where id= ".$row[csf("id")]);

	if($row[csf("entry_form")]==61 && $already_inserted_data[$row[csf("dtls_id")]] =="")
	{

		$prod_id = $issue_ref_arr[$row[csf("barcode_no")]][$row[csf("dtls_id")]]["prod_id"];
		$issue_qnty = $issue_ref_arr[$row[csf("barcode_no")]][$row[csf("dtls_id")]]["issue_qnty"];
		$trans_id = $issue_ref_arr[$row[csf("barcode_no")]][$row[csf("dtls_id")]]["trans_id"];
		$inserted_by = $issue_ref_arr[$row[csf("barcode_no")]][$row[csf("dtls_id")]]["inserted_by"];

		$already_inserted_data[$row[csf("dtls_id")]] = $row[csf("dtls_id")];

		$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);

		$data_array_prop = "(" . $id_prop . ", " . $trans_id . ", 2, 61, " . $row[csf("dtls_id")] . ", " . $row[csf("po_breakdown_id")] . " ," . $prod_id . ", " . $issue_qnty . ", " . "'sample to order transfer roll table booking without flag synced to 0 and order wise row inserted'" .",". $inserted_by . ", '" . $pc_date_time . "')";

		$insert_stat = sql_insert("order_wise_pro_details", $field_array_proportionate, $data_array_prop, 0);

		if($update_roll_table && $insert_stat )
		{

		}else{
			echo "insert into order_wise_pro_details (".$field_array_proportionate.") values ".$data_array_prop."<br>";
			oci_rollback($con);
			die;
		}

		//$insertQry.="insert into order_wise_pro_details (".$field_array_proportionate.") values ".$data_array_prop."<br>";

	}
	
}


//echo $insertQry;

/*oci_commit($con); 
echo "Success";
die;*/
?>