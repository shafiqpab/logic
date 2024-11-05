<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
//$con=connect();


	if($db_type==0)
	{
		$current_date = date("Y-m-d",strtotime(add_time(date("Y-m-d",time()),0)));
		$prev_date = date('Y-m-d', strtotime('-14 day', strtotime($current_date)));
	}
	else
	{
		$current_date = change_date_format(date("Y-m-d",strtotime(add_time(date("Y-m-d",time()),0))),'','',1);
		$prev_date = change_date_format(date('Y-m-d', strtotime('-14 day', strtotime($current_date))),'','',1); 
	}

	$buyer_arr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");
	$buyer_buffer_arr=return_library_array("select id,delivery_buffer_days from  lib_buyer","id","delivery_buffer_days");
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$supp_library=return_library_array( "select id,supplier_name from lib_supplier", "id", "supplier_name"  );
	$lib_country=return_library_array( "select id,country_name from lib_country", "id", "country_name"  );
	
	$location_library=return_library_array( "select id,location_name from lib_location", "id", "location_name"  );
	$floor_library=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name"  );


 

$prev_date='01-Aug-2019';
$current_date='31-Aug-2019';






	$company_name=3;
	

 
		$sql_summary_ex_factory=return_library_array("SELECT po_break_down_id,sum(ex_factory_qnty) AS ex_factory_qnty from pro_ex_factory_mst  where status_active=1 and is_deleted=0 group by po_break_down_id",'po_break_down_id','ex_factory_qnty');
		
		$cutting_qnty=return_library_array("SELECT po_break_down_id,sum(production_quantity) AS production_quantity  from pro_garments_production_mst where production_type ='1' and is_deleted=0 and status_active=1 group by po_break_down_id",'po_break_down_id','production_quantity');
		
		$sewingin_qnty=return_library_array("SELECT po_break_down_id,sum(production_quantity) AS production_quantity  from pro_garments_production_mst where production_type ='5' and is_deleted=0 and status_active=1 group by po_break_down_id",'po_break_down_id','production_quantity');
		
		$finish_qnty=return_library_array("SELECT po_break_down_id,sum(production_quantity) AS production_quantity  from pro_garments_production_mst where production_type ='8' and is_deleted=0 and status_active=1 group by po_break_down_id",'po_break_down_id','production_quantity');

		$prev_PendingSAH=0;$prev_po_qnty=0; $prev_po_val=0; $prev_sew_qnty=0; $prev_cut_qnty=0; $prev_finish_qnty=0;
		
	
		$sql_summary=sql_select( "SELECT a.id, b.order_uom, a.shiping_status,a.pub_shipment_date,a.extended_ship_date, a.job_no_mst, a.po_quantity, b.total_set_qnty, a.plan_cut, (a.unit_price/b.total_set_qnty) as unit_price,set_smv  from wo_po_break_down a, wo_po_details_master b where b.job_no=a.job_no_mst and b.company_name=3 and a.shiping_status!=3 and a.is_confirmed=1 and a.po_quantity>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  ");  
		
		$current_month_enddate=date("Y-m-d",strtotime("-1 days"));
		foreach( $sql_summary as $row_summary)
		{
			
			$pub_shipment_date=date("Y-m-d",strtotime($row_summary[csf('pub_shipment_date')]));
			$extended_ship_date=date("Y-m-d",strtotime($row_summary[csf('extended_ship_date')]));
				
			
			if($row_summary[csf('extended_ship_date')]=='') $extended_ship_date=$current_month_enddate;  
			if($extended_ship_date<=$current_month_enddate) //Check here
			{
				if($row_summary[csf('shiping_status')]==2)
				{
					$order_ex_quantity=0;
					$order_ex_quantity=$sql_summary_ex_factory[$row_summary[csf('id')]];
				}
				else
				{
					$order_ex_quantity=0;
				}
				$pre_po_quantity=$row_summary[csf('po_quantity')]*$row_summary[csf('total_set_qnty')];
				$pre_plan_cut_qty=$row_summary[csf('plan_cut')]*$row_summary[csf('total_set_qnty')];
				$tot_pre_plan_cut+=$pre_plan_cut_qty;
				$order_quantity=$pre_po_quantity-$order_ex_quantity;
				$prev_po_qnty+=$order_quantity;
				$prev_po_val+=$order_quantity*$row_summary[csf('unit_price')];
				if($pre_plan_cut_qty-$cutting_qnty[$row_summary[csf('id')]]>0)
				{
					$prev_cut_qnty+=$pre_plan_cut_qty-$cutting_qnty[$row_summary[csf('id')]];
				}
				if($pre_po_quantity-$sewingin_qnty[$row_summary[csf('id')]]>0)
				{
					$prev_sew_qnty+=$pre_po_quantity-$sewingin_qnty[$row_summary[csf('id')]];
				}
				if($pre_po_quantity-$finish_qnty[$row_summary[csf('id')]]>0)
				{
					$prev_finish_qnty+=$pre_po_quantity-$finish_qnty[$row_summary[csf('id')]];
				}
				
				$prev_PendingSAH+=($order_quantity*$row_summary[csf('set_smv')])/60;
			}
			
		}
		
		$curr_PendingSAH=0;$curr_po_qnty=0; $curr_po_val=0; $curr_cut_qnty=0; $curr_sew_qnty=0; $curr_finish_qnty=0;
		$sql_summary2=sql_select("SELECT a.id, b.order_uom, a.shiping_status,a.pub_shipment_date, a.extended_ship_date,a.job_no_mst, a.po_quantity, b.total_set_qnty, a.plan_cut, (a.unit_price/b.total_set_qnty) as unit_price,set_smv from wo_po_break_down a, wo_po_details_master b where b.job_no=a.job_no_mst and b.company_name=3  and a.shiping_status!=3  and a.is_confirmed=1 and a.po_quantity>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		
		foreach($sql_summary2 as $row_summary2)
		{
			$pub_shipment_date=date("Y-m-d",strtotime($row_summary2[csf('pub_shipment_date')]));
			$extended_ship_date=date("Y-m-d",strtotime($row_summary2[csf('extended_ship_date')]));
			
			if($row_summary2[csf('extended_ship_date')]=='') $extended_ship_date=$current_month_enddate;
			if($extended_ship_date<=$current_month_enddate) //Check here
			{
				if($row_summary2[csf('shiping_status')]==2)
				{
					$order_ex_quantity2=0;
					$order_ex_quantity2=$sql_summary_ex_factory[$row_summary2[csf('id')]];
				}
				else
				{
					$order_ex_quantity2=0;
				}
				$curr_po_quantity=$row_summary2[csf('po_quantity')]*$row_summary2[csf('total_set_qnty')];
				$curr_plan_cut_qty=$row_summary2[csf('plan_cut')]*$row_summary2[csf('total_set_qnty')];
				$tot_curr_plan_cut+=$curr_plan_cut_qty;
				$order_quantity2=$curr_po_quantity-$order_ex_quantity2;
				$curr_po_qnty+=$order_quantity2;
				$curr_po_val+=$order_quantity2*$row_summary2[csf('unit_price')];
				if($curr_plan_cut_qty-$cutting_qnty[$row_summary2[csf('id')]]>0)
				{
					$curr_cut_qnty+=$curr_plan_cut_qty-$cutting_qnty[$row_summary2[csf('id')]];
				}
				if($curr_po_quantity-$sewingin_qnty[$row_summary2[csf('id')]]>0)
				{
					$curr_sew_qnty+=$curr_po_quantity-$sewingin_qnty[$row_summary2[csf('id')]];
				}
				if($curr_po_quantity-$finish_qnty[$row_summary2[csf('id')]]>0)
				{
					$curr_finish_qnty+=$curr_po_quantity-$finish_qnty[$row_summary2[csf('id')]];
				}
				
				
				
				$curr_PendingSAH+=($order_quantity2*$row_summary2[csf('set_smv')])/60;
			}
			
		}
		
		$curr_month=date("F",strtotime($end_month)).", ".date("Y",strtotime($end_month));
		
		$summary_grand_total_po_qny=0;
		$summary_grand_total_lc_value=0;
		$summary_grand_total_cut_qny=0;
		$summary_grand_total_sewing_qny=0;
		$summary_grand_total_finish_qny=0;
		$bgcolor1='#E9F3FF';
		$bgcolor2='#FFFFFF';
		?>


            <table width="930" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1">
                <thead>
                <th width="30">SL</th>
                <th width="130"> Month </th>
                <th width="130">Pending PO Qty. </th>
                <th width="140">Pending PO Value</th>
                <th width="80">Pending SAH</th>
                <th width="135">Pending Plan Cut Qty.</th>
                <th width="125">Cutting Pending </th>
                <th width="125">Sewing Pending</th>
                <th>Finishing Pending </th>
                </thead>
              
                <tr bgcolor="<? echo $bgcolor1; ?>" onclick="change_color('tr1st_1','<? echo $bgcolor1; ?>')" id="tr1st_1">
                    <td>1</td>
                    <td>Previous To Current Month</td>
                    <td align="right"><? echo number_format($prev_po_qnty,0); $summary_grand_total_po_qny+=$prev_po_qnty; ?></td>
                    <td align="right"><? echo number_format($prev_po_val,2); $summary_grand_total_lc_value+=$prev_po_val; ?></td>
                    <td align="right"><? echo number_format($prev_PendingSAH,2);$summary_grand_total_PendingSAH+=$prev_PendingSAH;?></td>
                    <td align="right"><? echo number_format($tot_pre_plan_cut,0); $summary_grand_total_plan_cut+=$tot_pre_plan_cut; ?></td>
                    <td align="right"><? echo number_format($prev_cut_qnty,0); $summary_grand_total_cut_qny+=$prev_cut_qnty; ?></td>
                    <td align="right"><? echo number_format($prev_sew_qnty,0); $summary_grand_total_sewing_qny+=$prev_sew_qnty; ?></td>
                    <td align="right"><? echo number_format($prev_finish_qnty,0); $summary_grand_total_finish_qny+=$prev_finish_qnty; ?></td>
                </tr>
                    <tr bgcolor="<? echo $bgcolor2; ?>" onclick="change_color('tr1st_2','<? echo $bgcolor2; ?>')" id="tr1st_2">
                    <td>2</td>
                    <td> <? echo $curr_month; ?> </td>
                    <td align="right"><? echo number_format($curr_po_qnty,0); $summary_grand_total_po_qny+=$curr_po_qnty; ?></td>
                    <td align="right"><? echo number_format($curr_po_val,2); $summary_grand_total_lc_value+=$curr_po_val; ?></td>
                    <td align="right"><? echo number_format($curr_PendingSAH,2);$summary_grand_total_PendingSAH+=$curr_PendingSAH;?></td>
                    <td align="right"><? echo number_format($tot_curr_plan_cut,0); $summary_grand_total_plan_cut+=$tot_curr_plan_cut; ?></td>
                    <td align="right"><? echo number_format($curr_cut_qnty,0); $summary_grand_total_cut_qny+=$curr_cut_qnty; ?></td>
                    <td align="right"><? echo number_format($curr_sew_qnty,0); $summary_grand_total_sewing_qny+=$curr_sew_qnty; ?></td>
                    <td align="right"><? echo number_format($curr_finish_qnty,0); $summary_grand_total_finish_qny+=$curr_finish_qnty; ?></td>
                </tr>
                <tfoot>
                    <th colspan="2" align="right">Total</th>
                    <th align="right"><? echo number_format($summary_grand_total_po_qny,0); ?></th>
                    <th align="right"><? echo number_format($summary_grand_total_lc_value,2); ?></th>
                    <th align="right"><? echo number_format($summary_grand_total_PendingSAH,2); ?></th>
                    <th align="right"><? echo number_format($summary_grand_total_plan_cut,0); ?></th>
                    <th align="right"><? echo number_format($summary_grand_total_cut_qny,0); ?></th>
                    <th align="right"><? echo number_format($summary_grand_total_sewing_qny,0); ?></th>
                    <th align="right"><? echo number_format($summary_grand_total_finish_qny,0); ?> </th>
                </tfoot>
            </table>
















