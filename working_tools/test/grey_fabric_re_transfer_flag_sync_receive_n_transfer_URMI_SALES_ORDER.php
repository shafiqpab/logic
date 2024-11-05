<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

$po_ids = $_GET['po_id'];
if($po_ids=="")
{
	echo "No PO found";
	die;
}

$reference_barcodes_sql = "select BARCODE_NO from  pro_roll_details a,fabric_sales_order_mst b where a.po_breakdown_id=b.id and b.job_no in('".$po_ids."')  and a.status_active = 1 and a.entry_form=133 and a.re_transfer=0 group by BARCODE_NO";

$reference_barcodes = sql_select($reference_barcodes_sql);
foreach ($reference_barcodes as $row)
{
	$ref_barcode_arr[$row["BARCODE_NO"]] = $row["BARCODE_NO"];
}
$barcodes = implode(",", $ref_barcode_arr);

$all_receive_barcodes_sql="select ID,BARCODE_NO,DTLS_ID,ENTRY_FORM,RE_TRANSFER from PRO_ROLL_DETAILS where entry_form in(58,133) and status_active=1 and is_deleted=0 and barcode_no in($barcodes) order by BARCODE_NO ASC,ENTRY_FORM DESC,DTLS_ID DESC";

$all_receive_barcodes = sql_select($all_receive_barcodes_sql);

if(empty($all_receive_barcodes ))
{
	echo "Barcode not found";
	die;
}

$po_id_arr = array();
foreach ($all_receive_barcodes as $row)
{
	$id = $row["ID"];

	if($source_rcv_arr[$row["BARCODE_NO"]] == "")
	{	
		$source_rcv_arr[$row["BARCODE_NO"]] = $row["BARCODE_NO"];
		if($row["RE_TRANSFER"] != 0)
		{
			echo "update PRO_ROLL_DETAILS set RE_TRANSFER=0 where id=$id; ".$row["ENTRY_FORM"]."=".$row["RE_TRANSFER"]." <br />";
			/*$rs=execute_query("update PRO_ROLL_DETAILS set RE_TRANSFER = 0 where id = ".$id, 0);
			if($rs == 0)
			{
				echo "Failed to run script <br>";
				echo "update PRO_ROLL_DETAILS set RE_TRANSFER=0 where id=$id; ".$row["BARCODE_NO"]." <br />";
				oci_rollback($con);
				disconnect($con);
				die;
			}*/
		}
	}
	else
	{
		if($row["RE_TRANSFER"] != 1)
		{
			echo "update PRO_ROLL_DETAILS set RE_TRANSFER=1 where id=$id; ".$row["ENTRY_FORM"]."=".$row["RE_TRANSFER"]." <br />";
			/*$rs=execute_query("update PRO_ROLL_DETAILS set RE_TRANSFER = 1 where id = ".$id, 0);
			if($rs == 0)
			{
				echo "Failed to run script <br>";
				echo "update PRO_ROLL_DETAILS set RE_TRANSFER=1 where id=$id; ".$row["BARCODE_NO"]." <br />";
				oci_rollback($con);
				disconnect($con);
				die;
			}*/
		}
	}
}
//oci_commit($con);
echo "Success";
die;
?>