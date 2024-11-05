<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

$sid = $_GET["sid"];

$receive_arr=$issue_arr=array();
$receive=0;
// $grey_receive = sql_select("select b.barcode_no,b.qnty
// from inv_receive_master a, pro_grey_prod_entry_dtls c,pro_roll_details b,fabric_sales_order_mst d 
// where a.id=b.mst_id and c.id=b.dtls_id and b.po_breakdown_id=d.id and b.entry_form in(58,2) and c.trans_id>0 and a.receive_basis in(2,4,10) 
// and a.item_category=13 and b.is_sales=1 and d.company_id=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
// and a.status_active=1 and d.status_active=1 and d.is_deleted=0 and d.id=$po_id and to_char(d.insert_date,'YYYY')=2020 
// group by b.barcode_no,b.qnty");

$grey_receive = sql_select("SELECT sum(b.qnty) as qnty,b.barcode_no from inv_receive_master a,inv_transaction e,pro_grey_prod_entry_dtls c,pro_roll_details b,fabric_sales_order_mst d where a.id=e.mst_id and e.id=c.trans_id and c.id=b.dtls_id and b.po_breakdown_id=d.id and b.entry_form in(58,2) and c.trans_id>0 and a.receive_basis in(2,4,10) and a.item_category=13 and d.company_id=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and e.transaction_type=1 and d.job_no='$sid' group by b.barcode_no ");
if(!empty($grey_receive))
{
	foreach($grey_receive as $row)
	{
		$receive_arr[$row[csf("barcode_no")]]=$row[csf("qnty")];
		$receive+=$row[csf("qnty")];
	}
}

$receive_in=0;

$grey_receive_in = sql_select("SELECT sum(d.qnty) as qnty,d.barcode_no from inv_item_transfer_mst a,inv_transaction e,inv_item_transfer_dtls b,product_details_master c,pro_roll_details d,fabric_sales_order_mst f where a.entry_form=133 and a.status_active=1 and a.transfer_criteria=4 and a.id=e.mst_id and e.id=b.to_trans_id and b.from_prod_id=c.id and b.id=d.dtls_id and d.po_breakdown_id=f.id and b.status_active=1 and d.entry_form=133 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.job_no='$sid' group by d.barcode_no");
if(!empty($grey_receive_in))
{
	foreach($grey_receive_in as $row)
	{
		$receive_arr[$row[csf("barcode_no")]]+=$row[csf("qnty")];
		$roll_id_arr[$row[csf("barcode_no")]]+=$row[csf("roll_id")];
		$receive_in+=$row[csf("qnty")];
	}
}
$issue=0;

$grey_issue = sql_select("SELECT d.barcode_no,d.qnty from inv_issue_master a,inv_transaction e,inv_grey_fabric_issue_dtls b,pro_roll_details d,fabric_sales_order_mst f where a.status_active=1 and a.is_deleted=0 and a.id=e.mst_id and e.id=b.trans_id and b.id=d.dtls_id and a.item_category=13 and a.entry_form=61 and d.entry_form=61 and e.transaction_type=2 and e.status_active=1 and e.is_deleted=0 and d.po_breakdown_id=f.id and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and d.is_returned<>1 and f.job_no='$sid' ");
if(!empty($grey_issue))
{
	foreach($grey_issue as $row)
	{
		$issue_arr[$row[csf("barcode_no")]]=$row[csf("qnty")];
		$issue+=$row[csf("qnty")];
	}
}

$tr_out=0;
$grey_tr_out_arr=array();
// $grey_tr_out = sql_select("select d.barcode_no,d.qnty
// from inv_item_transfer_mst a,inv_item_transfer_dtls b,product_details_master c, pro_roll_details d 
// where a.id=b.mst_id and b.from_prod_id=c.id and a.status_active=1 and b.status_active=1 and a.entry_form=133 and a.transfer_criteria=4 
// and a.from_order_id in($po_id) and a.id = d.mst_id and d.entry_form = 133 and b.id = d.dtls_id and d.status_active=1 and d.is_deleted=0 
// group by d.barcode_no,d.qnty ");
$grey_tr_out = sql_select("SELECT d.qnty as qnty,d.barcode_no from inv_item_transfer_mst a,inv_transaction e,inv_item_transfer_dtls b,product_details_master c,pro_roll_details d,fabric_sales_order_mst f where a.status_active=1 and a.entry_form=133 and a.transfer_criteria=4 and a.id=e.mst_id and e.id=b.trans_id and b.id=d.dtls_id and b.from_prod_id=c.id and a.from_order_id=f.id and b.status_active=1 and e.status_active=1 and e.is_deleted=0 and e.transaction_type=6 and f.job_no='$sid' and a.id=d.mst_id and d.entry_form=133 and b.id=d.dtls_id and d.status_active=1 and d.is_deleted=0 ");
if(!empty($grey_tr_out))
{
	foreach($grey_tr_out as $row)
	{
		$issue_arr[$row[csf("barcode_no")]]=$row[csf("qnty")];
		$tr_out+=$issue_arr[$row[csf("barcode_no")]];
	}
}

echo "<table border='1'>";
$i=1;
$balance=0;
foreach ($receive_arr as $barcode_no => $value) {
	 if(number_format($issue_arr[$barcode_no],2)!=number_format($value,2))
	 {
		
			echo "<tr>";	
			echo "<td>".$i."</td><td>".$barcode_no ."=". number_format($value,2) ."</td><td>". ((number_format($issue_arr[$barcode_no],2)==number_format($value,2))?number_format($issue_arr[$barcode_no],2):"jahid".number_format($issue_arr[$barcode_no],2)) ."</td><td>".$roll_id_arr[$barcode_no]."</td>";
			echo "</tr>";

			// echo "<tr>";	
			// echo "<td>".$i."</td><td>".$barcode_no ."</td>";
			// echo "</tr>";
			$balance+=number_format($issue_arr[$barcode_no],2);

		$i++;
	}
}
echo "</table>";
echo "<div>".$balance."</div>";
echo "<div>".($receive+$receive_in)."</div>";
echo "<div>".($issue+$tr_out)."</div>";
//echo count($receive_arr);
//echo "<br />";
//echo count($issue_arr);
//oci_commit($con);
echo "Success";
die;

?>