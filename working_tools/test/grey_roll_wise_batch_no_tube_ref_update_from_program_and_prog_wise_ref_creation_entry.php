<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../includes/common.php');
$con=connect();

$missing_tube_batch = sql_select("select b.id as program_no, b.program_qnty, b.program_date,  b.tube_ref_no, b.batch_no, d.barcode_no, d.id as roll_table_id
from ppl_planning_info_entry_dtls b, inv_receive_master c, pro_roll_details d
where b.id=c.booking_id and b.is_sales = 1  and b.status_active = 1 and b.is_deleted = 0  and c.receive_basis=2 and c.entry_form=2 and c.id=d.mst_id and d.entry_form=2 and d.is_sales=1 and d.status_active = 1 and d.is_deleted = 0 and d.barcode_no <> 0
and d.tube_ref_no is null  and b.tube_ref_no is not null --and b.id=12065
order by b.id desc");

if(empty($missing_tube_batch))
{
	echo "Data Not Found";
	die;
}


$field_details_array = "id,program_no,planned_date,batch_no,reference_no,reference_qty,auto_created,inserted_by,insert_date";

$user_id = 1;

foreach($missing_tube_batch  as $row)
{
	$program_no 	= $row[csf("program_no")];
	$program_date 	= change_date_format($row[csf("program_date")], '', '', 1);
	$batch_no 		= $row[csf("batch_no")];
	$tube_ref_no 	= $row[csf("tube_ref_no")];
	$program_qnty 	= $row[csf("program_qnty")];
	$roll_table_id 	= $row[csf("roll_table_id")];

	if($program_dupli_chk[$row[csf("program_no")]]=="")
	{
		$program_dupli_chk[$row[csf("program_no")]] = $row[csf("program_no")];


		$id= return_next_id_by_sequence('PPL_REFERENCE_CREATION_SEQ', 'PPL_REFERENCE_CREATION', $con );
		$data_array_dtls = "(" . $id . "," . $program_no . ",'". $program_date ."','". $batch_no ."','". $tube_ref_no ."',". $program_qnty .",1,".$user_id.",'".$pc_date_time."')";

		//echo "<br> insert into PPL_REFERENCE_CREATION (".$field_details_array.") values ".$data_array_dtls."<br>";
		$rID=sql_insert("PPL_REFERENCE_CREATION",$field_details_array,$data_array_dtls,1);
		if($rID ==0)
		{
			echo " insert into PPL_REFERENCE_CREATION (".$field_details_array.") values ".$data_array_dtls;
			oci_rollback($con);
			disconnect($con);
			die;
		}
		
	}

	//echo " update pro_roll_details set tube_ref_no ='$tube_ref_no',tube_qnty='$program_qnty',batch_no='$batch_no' where id =".$roll_table_id."==".$row[csf("barcode_no")]."<br>";
    $rID2 = execute_query("update pro_roll_details set tube_ref_no = '$tube_ref_no', tube_qnty='$program_qnty', batch_no='$batch_no' where id =".$roll_table_id,0);
    if($rID2 ==0)
	{
		echo " update pro_roll_details set tube_ref_no = '$tube_ref_no', tube_qnty='$program_qnty', batch_no='$batch_no' where id =".$roll_table_id;
		oci_rollback($con);
		disconnect($con);
		die;
	}
    
}

oci_commit($con);
echo "success";
disconnect($con);
die;

 ?>