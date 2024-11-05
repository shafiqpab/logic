<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

$yarn_req_sql = "select id, knit_id, requisition_no, prod_id, yarn_qnty,is_dyed_yarn,total_distribution_qnty, distribution_qnty_breakdown, allocation_qnty_breakdown from ppl_yarn_requisition_entry where status_active = 1 and is_deleted = 0 and total_distribution_qnty>0";//and requisition_no in(39516) and prod_id in(21837) 

$req_data = sql_select($yarn_req_sql);

$distribution_po_string = array();
foreach ($req_data as $row)
{
	$prog_no.=$row[csf('knit_id')].",";
}

$prog_no_cond="";
if($prog_no!="")
{
	$prog_no=substr($prog_no,0,-1);
	$prog_no=implode(",",array_filter(array_unique(explode(",",$prog_no))));

	if($db_type==0) $prog_no_cond="and a.dtls_id in(".$prog_no.")";
	else
	{
		$prog_nos=explode(",",$prog_no);
		if(count($prog_nos)>990)
		{
			$prog_no_cond="and (";
			$prog_no_arr=array_chunk($prog_nos,990);
			$z=0;
			foreach($prog_no_arr as $id)
			{
				$id=implode(",",$id);
				if($z==0) $prog_no_cond.=" a.dtls_id in(".$id.")";
				else $prog_no_cond.=" or a.dtls_id in(".$id.")";
				$z++;
			}
			$prog_no_cond.=")";
		}
		else $prog_no_cond="and a.dtls_id in(".$prog_no.")";
	}
}
// product cond

if($prog_no_cond!="")
{
	$sql_plan_po = "select a.dtls_id, a.booking_no, a.po_id, sum(a.program_qnty) as program_qnty,c.requisition_no,c.prod_id from ppl_planning_entry_plan_dtls a,ppl_planning_info_entry_dtls b, ppl_yarn_requisition_entry c where a.dtls_id=b.id and b.id=c.knit_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $prog_no_cond group by a.dtls_id, a.booking_no, a.po_id, c.requisition_no,c.prod_id order by c.requisition_no, a.po_id";

	$plan_po_data = sql_select($sql_plan_po);

	$po_wise_prog_qty = array();
	$prog_no_wise_total_prog_qty = array();
	$prog_po = array();

	foreach ($plan_po_data as $row)
	{
		$po_wise_prog_qty[$row[csf('prod_id')]][$row[csf('dtls_id')]][$row[csf('po_id')]] = $row[csf('program_qnty')];

		$prog_no_wise_total_prog_qty[$row[csf('prod_id')]][$row[csf('dtls_id')]] += $row[csf('program_qnty')];
		
		$prog_po[$row[csf('prod_id')]][$row[csf('dtls_id')]][] = $row[csf('po_id')];
	}

}


//echo "<pre>";
//print_r($prog_no_wise_total_prog_qty);
//die();


foreach ($req_data as $req_row)
{

	$po_distributed_qty_arr = explode( ',', chop( $req_row[csf('distribution_qnty_breakdown')],",") );
	
	if( count($prog_po[$req_row[csf('prod_id')]][$req_row[csf('knit_id')]]) !=  count($po_distributed_qty_arr) )
	{
		$distribution_qnty_breakdown="";

		foreach ($prog_po[$req_row[csf('prod_id')]][$req_row[csf('knit_id')]]  as $key=>$poId )
		{
			$order_wise_program_qty = $po_wise_prog_qty[$req_row[csf('prod_id')]][$req_row[csf('knit_id')]][$poId];
			$tot_prog_qnty = $prog_no_wise_total_prog_qty[$req_row[csf('prod_id')]][$req_row[csf('knit_id')]];
			$prop_qnty = $req_row[csf('total_distribution_qnty')];

			//formula = (prog_qnty/tot_prog_qnty)*txt_prop_qnty;
			$distributedQty = number_format( ($order_wise_program_qty/$tot_prog_qnty)*$prop_qnty, 2, '.', '') ;

			if($distribution_qnty_breakdown=="")
			{
				$distribution_qnty_breakdown = $req_row[csf('requisition_no')]."_".$poId."_".$req_row[csf('prod_id')]."_".$distributedQty;
			}else{
				$distribution_qnty_breakdown .= ",".$req_row[csf('requisition_no')]."_".$poId."_".$req_row[csf('prod_id')]."_".$distributedQty;
			}

			$check_order_id = trim(return_field_value("id"," ppl_yarn_req_distribution","po_break_down_id=$poId and requisition_no=".$req_row[csf('requisition_no')]." and prod_id=".$req_row[csf('prod_id')]." and status_active=1 and is_deleted=0 "));

			if( $check_order_id !="" )
			{								
				$child_tbl_not_deleted_id_arr[] = $check_order_id;

				$update_child_tbl_sql = execute_query("update ppl_yarn_req_distribution set distribution_qnty = '".$distributedQty. "', updated_by = 999 where id = ".$check_order_id);

				//echo "update ppl_yarn_req_distribution set distribution_qnty = '".$distributedQty. "', updated_by = 999 where id = ".$check_order_id;
			}
		}

		$child_tbl_not_deleted_ids = implode(',',array_unique($child_tbl_not_deleted_id_arr)); 

		$delete_sql = execute_query("update ppl_yarn_req_distribution set status_active = 0, is_deleted = 1, updated_by = 999 where id not in($child_tbl_not_deleted_ids) and requisition_no =".$req_row[csf('requisition_no')]." and prod_id=".$req_row[csf('prod_id')]." and status_active=1 and is_deleted=0 ");

		//echo "update ppl_yarn_requisition_entry set distribution_qnty_breakdown = '".$distribution_qnty_breakdown. "', updated_by = 999 where id = ".$req_row[csf('id')]." and requisition_no =".$req_row[csf('requisition_no')]." and knit_id =".$req_row[csf('knit_id')]."<br>";

		$proportQ = execute_query("update ppl_yarn_requisition_entry set distribution_qnty_breakdown = '".$distribution_qnty_breakdown. "', updated_by = 999 where id = ".$req_row[csf('id')]." and requisition_no =".$req_row[csf('requisition_no')]." and knit_id =".$req_row[csf('knit_id')],0);

		//$maintable_id[] = $req_row[csf('id')];
	}

}


//echo count(array_unique($maintable_id));

//die();
echo $proportQ ."&& ". $update_child_tbl_sql ."&& ". $delete_sql; die();
//echo $db_type;die;
if ($db_type == 0) {
	if ($proportQ && $update_child_tbl_sql &&  $delete_sql) {
		mysql_query("COMMIT");
		echo "0**" . "success";
	} else {
		mysql_query("ROLLBACK");
		echo "10**"."fail";
	}
} else if ($db_type == 2 || $db_type == 1) {

	if ($proportQ && $update_child_tbl_sql && $delete_sql) {
		oci_commit($con);
		echo "0**"."success";
	} else {
		oci_rollback($con);
		echo "10**fail";
	}
}
disconnect($con);
die();
?>
