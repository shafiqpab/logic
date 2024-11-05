<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

$prod_idCond="and c.prod_id=31483";

$recv_sql="select  a.booking_id,c.po_breakdown_id as po_id, sum(c.quantity) as knitting_qnty,a.store_id 
 from inv_receive_master a, pro_grey_prod_entry_dtls b,order_wise_pro_details c  
 where a.id=b.mst_id and c.dtls_id=b.id and a.item_category=13 and a.entry_form=2 and c.entry_form=2 and a.receive_basis=2 and b.status_active=1 
 and b.is_deleted=0 $prod_idCond
 group by c.po_breakdown_id, a.booking_id,a.store_id ";
	 $recv_sqlx=sql_select($recv_sql);
	 $stock_arr=array();
	foreach($recv_sqlx as $row)
	{
 		$stock_arr[$row[csf("booking_id")]][$row[csf("po_id")]][$row[csf("store_id")]]["qnty"]+=$row[csf("knitting_qnty")];
 		$recv_arr[$row[csf("booking_id")]][$row[csf("po_id")]][$row[csf("store_id")]]["qnty"]+=$row[csf("knitting_qnty")];
	}

$issue_sql="select b.program_no,sum(c.quantity) as  knitting_issue_qnty,c.po_breakdown_id,b.store_name 
        from  inv_issue_master a,inv_grey_fabric_issue_dtls b , order_wise_pro_details c
        where a.id=b.mst_id and b.id = c.dtls_id
        and c.trans_type = 2
        and a.item_category=13 and a.entry_form in (16) and c.entry_form in (16)
        and a.status_active=1 and a.is_deleted=0
        and b.status_active=1 and b.is_deleted=0
        and c.status_active = 1 and c.is_deleted = 0 
        and b.program_no <> 0 and b.program_no is not null $prod_idCond group by b.program_no,c.po_breakdown_id,b.store_name ";
	$issue_sqlx=sql_select($issue_sql);   
	foreach($issue_sqlx as $row)
	{
 		$stock_arr[$row[csf("program_no")]][$row[csf("po_breakdown_id")]][$row[csf("store_name")]]["qnty"]-=$row[csf("knitting_issue_qnty")];
 		$issue_arr[$row[csf("program_no")]][$row[csf("po_breakdown_id")]][$row[csf("store_name")]]["qnty"]+=$row[csf("knitting_issue_qnty")];
	}   
   
$trans_in_sql="select b.to_program,
    sum(case when c.trans_type in(5) then c.quantity else 0 end) as item_transfer_in,
    a.to_order_id,b.to_store 
    from inv_item_transfer_dtls b,inv_item_transfer_mst a,order_wise_pro_details c
    where a.id=b.mst_id  and c.dtls_id=b.id and c.trans_type in(5) and a.status_active=1 and a.is_deleted=0 and a.item_category=13
    and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
    and c.entry_form in (13) and a.transfer_criteria in (4,6) and  b.from_program>0 and b.to_program>0 and c.is_sales <> 1 $prod_idCond 
    group by b.to_program,a.to_order_id,b.to_store";
	$trans_in_sqlx=sql_select($trans_in_sql); 
	foreach($trans_in_sqlx as $row)
	{
 		$stock_arr[$row[csf("to_program")]][$row[csf("to_order_id")]][$row[csf("to_store")]]["qnty"]+=$row[csf("item_transfer_in")];
 		$tran_in_arr[$row[csf("to_program")]][$row[csf("to_order_id")]][$row[csf("to_store")]]["qnty"]+=$row[csf("item_transfer_in")];
	}      
   
$trans_out_sql="select b.from_program,
    sum(case when c.trans_type in(6)  then c.quantity else 0 end) as item_transfer_out,
    a.from_order_id,b.from_store 
    from inv_item_transfer_dtls b,inv_item_transfer_mst a,order_wise_pro_details c
    where a.id=b.mst_id  and c.dtls_id=b.id and c.trans_type in(6) and a.status_active=1 and a.is_deleted=0 and a.item_category=13
    and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
    and c.entry_form in (13) and a.transfer_criteria in (4,6) and  b.from_program>0 and b.to_program>0 and c.is_sales <> 1 $prod_idCond
    group by b.from_program, a.from_order_id,b.from_store";
	$trans_out_sqlx=sql_select($trans_out_sql);      
	foreach($trans_out_sqlx as $row)
	{
 		$stock_arr[$row[csf("from_program")]][$row[csf("from_order_id")]][$row[csf("from_store")]]["qnty"]-=$row[csf("item_transfer_out")];
 		$tran_out_arr[$row[csf("from_program")]][$row[csf("from_order_id")]][$row[csf("from_store")]]["qnty"]+=$row[csf("item_transfer_out")];
	}   

foreach($stock_arr  as $program=>  $program_data )
{
	foreach($program_data as  $order=> $order_data )
	{
		foreach($order_data as $store=> $store_data )
		{
			foreach($store_data as $qnty )
			{
				if($qnty != 0)
				{
					$tot +=$qnty;

					$recv_arr[$program][$order][$store]["qnty"];
					$issue_arr[$program][$order][$store]["qnty"];
					$tran_in_arr[$program][$order][$store]["qnty"];
					$tran_out_arr[$program][$order][$store]["qnty"];
					echo "program: $program"."="."order: $order"."="."store: $store"."=="."qnty:" .number_format($qnty,2)."  rcv=".$recv_arr[$program][$order][$store]["qnty"].", iss=".$issue_arr[$program][$order][$store]["qnty"]." , transIN=".$tran_in_arr[$program][$order][$store]["qnty"]." , TransOUT=".$tran_out_arr[$program][$order][$store]["qnty"]."<br>";
				}
			}
		}
	}

}


echo "==============================".$tot;


/*
$apply_sql="select b.sales_booking_no from wo_booking_mst a, fabric_sales_order_mst b where a.booking_no=b.sales_booking_no and a.is_apply_last_update=1 and b.revise_no=0";
$apply_sql_res=sql_select($apply_sql); $i=0;
foreach($apply_sql_res as $row)
{
	$i++;
	//$booking_no="'".$row[csf("sales_booking_no")]."'";
}
*/
