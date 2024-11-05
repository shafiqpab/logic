<?php
date_default_timezone_set("Asia/Dhaka");
require_once('includes/common.php');
require_once('mailer/class.phpmailer.php');

//$user_library=return_library_array("select id,user_full_name from user_passwd where valid=1","id","user_full_name");
$company_library=return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
$buyer_library=return_library_array("select id,buyer_name from lib_buyer","id","buyer_name");
//$supplier_library = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
//$supplier_library = return_library_array("select id,team_member_name from lib_mkt_team_member_info where status_active=1 and is_deleted=0","id","team_member_name");






	if($db_type==0)
	{
		//$current_date = date("Y-m-d H:i:s",strtotime(add_time(date("Y-m-d",time()),0)));
		//$prev_date = date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date)));
		
		$current_date = date("Y-m-d H:i:s",strtotime(add_time(date("H:i:s",time()),0)));
		$prev_date = date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))); 

		$tomorrow = date("Y-m-d H:i:s",strtotime(add_date(date("Y-m-d",time()),1)));
		$day_after_tomorrow = date("Y-m-d H:i:s",strtotime(add_date(date("Y-m-d",time()),2)));
		$select_fill="DATE_FORMAT(b.update_date, '%d-%m-%Y %H:%i:%s')";
	}
	else
	{
		$tomorrow = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("Y-m-d",time()),1))),'','',1);
		$day_after_tomorrow = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("Y-m-d",time()),2))),'','',1);
		$current_date = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("Y-m-d",time()),0))),'','',1);
		$prev_date = change_date_format(date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))),'','',1); 
		$select_fill="to_char(b.update_date,'DD-MM-YYYY HH12:MI:SS')";
	}

	
//$prev_date="10-Aug-2015";$current_date="14-Aug-2015";
//$prev_date="25-Jul-2015";$current_date="1-Aug-2015";
//$company_library=array(3=>'sss');

foreach($company_library as $compid=>$compname)
{
$flag=0;
ob_start();	
	?>
    
    <table width="900" border="0" cellpadding="0" cellspacing="0">
        <tr><td align="center"><strong style="font-size:24px;">Budget Exceeding Areas</strong></td></tr>
        <tr><td align="center"><strong style="font-size:18px;"><? echo $compname; ?></strong></td></tr>
        <tr><td align="center"><strong>Date : <? echo date('d-m-Y');?></strong></td></tr>
        <tr>
            <td valign="top" align="left">
            
                    <?
				  
//----------------------Knitting Production	
	
	
	
	$kp_sql="
		select 
			a.style_ref_no,
			a.job_no,
			c.quantity as grey_prod_qnty,
			e.booking_no
		from 
			wo_po_details_master a, 
			wo_po_break_down b,
			order_wise_pro_details c,
			product_details_master d,
			ppl_planning_entry_plan_dtls e
		 where 
			 a.job_no=b.job_no_mst 
			 and a.company_name=$compid
			 and b.id=c.po_breakdown_id
			 and c.prod_id=d.id 
			 and b.id=e.po_id 
			 and c.entry_form=2 
			 and c.is_deleted=0 
			 and c.status_active=1 
			 and c.insert_date between '".$prev_date."' and '".$current_date."'
			 and d.item_category_id=13";
	 
	$data_array=sql_select($kp_sql);
	$kp_arr=array();$job_arr=array();
	foreach ($data_array as $row)
	{ 
		$kp_arr[$row[csf("job_no")]]+=$row[csf("grey_prod_qnty")];
		$job_arr[$row[csf("job_no")]]=$row[csf("job_no")];
		$kp_booking_no_arr[$row[csf("job_no")]]=$row[csf("booking_no")];
	}

//----------------------Grey Fabric Receive Details	
	$gfr_sql="
		select 
			a.style_ref_no,
			a.job_no,
			c.order_id, 
			c.current_delivery,
			d.booking_no
		from 
			wo_po_details_master a, 
			wo_po_break_down b,
			pro_grey_prod_delivery_dtls c,
			ppl_planning_entry_plan_dtls d
		 where 
			 a.job_no=b.job_no_mst 
			 and a.company_name=$compid
			 and b.id=c.order_id
			 and b.id=d.po_id 
			 and c.entry_form=53 
			 and c.is_deleted=0 
			 and c.status_active=1 
			 and c.insert_date between '".$prev_date."' and '".$current_date."'";
	 
	$data_array=sql_select($gfr_sql);
	$gfr_arr=array();
	foreach ($data_array as $row)
	{ 
		$gfr_arr[$row[csf("job_no")]]+=$row[csf("current_delivery")];
		$job_arr[$row[csf("job_no")]]=$row[csf("job_no")];
		$gfr_booking_no_arr[$row[csf("job_no")]]=$row[csf("booking_no")];
	}


//------------------------------------Knite Gray Febric Receive
$kgfr_sql="
		select 
			a.style_ref_no,
			a.job_no,
			c.quantity as grey_fab_qnty
		from 
			wo_po_details_master a, 
			wo_po_break_down b,
			order_wise_pro_details c
		 where 
			 a.job_no=b.job_no_mst 
			 and a.company_name=$compid
			 and b.id=c.po_breakdown_id
			 and c.entry_form=22 
			 and c.is_deleted=0 
			 and c.status_active=1 
			 and c.insert_date between '".$prev_date."' and '".$current_date."'";
	
	$kgfr_data_array=sql_select($kgfr_sql);
	$kgfr_arr=array();
	foreach ($kgfr_data_array as $row)
	{ 
		$kgfr_arr[$row[csf("job_no")]]+=$row[csf("grey_fab_qnty")];
		$job_arr[$row[csf("job_no")]]=$row[csf("job_no")];
	}



//------------------------------------Grey Fabric Issue Details
$kgfi_sql="
		select 
			a.style_ref_no,
			a.job_no,
			c.quantity
		from 
			wo_po_details_master a, 
			wo_po_break_down b,
			order_wise_pro_details c
		 where 
			 a.job_no=b.job_no_mst 
			 and a.company_name=$compid
			 and b.id=c.po_breakdown_id
			 and c.entry_form=16
			 and c.trans_type=2 
			 and c.is_deleted=0 
			 and c.status_active=1 
			 and c.insert_date between '".$prev_date."' and '".$current_date."'";
	
	$kgfi_data_array=sql_select($kgfi_sql);
	$kgfi_arr=array();
	foreach ($kgfi_data_array as $row)
	{ 
		$kgfi_arr[$row[csf("job_no")]]+=$row[csf("quantity")];
		$job_arr[$row[csf("job_no")]]=$row[csf("job_no")];
	}

//------------------------------------Batch Creation Qty.
$bc_sql="
		select 
			a.style_ref_no,
			a.job_no,
			c.batch_qnty,
			d.booking_no
		from 
			wo_po_details_master a, 
			wo_po_break_down b,
			pro_batch_create_dtls c,
			pro_batch_create_mst d
		 where 
			 a.job_no=b.job_no_mst 
			 and a.company_name=$compid
			 and b.id=c.po_id
			 and c.is_deleted=0 
			 and c.status_active=1 
			 and c.insert_date between '".$prev_date."' and '".$current_date."'
			 and d.id=c.mst_id";
	
	$bc_data_array=sql_select($bc_sql);
	$bc_arr=array();
	foreach ($bc_data_array as $row)
	{ 
		$bc_arr[$row[csf("job_no")]]+=$row[csf("batch_qnty")];
		$bc_booking_arr[$row[csf("job_no")]]=$row[csf("booking_no")];
		$job_arr[$row[csf("job_no")]]=$row[csf("job_no")];
	}

//------------------------------------Dyeing Production Qty
$dp_sql="
		select 
			a.style_ref_no,
			a.job_no,
			c.batch_qnty,
			d.booking_no
		from 
			wo_po_details_master a, 
			wo_po_break_down b,
			pro_batch_create_dtls c,
			pro_batch_create_mst d
		 where 
			 a.job_no=b.job_no_mst 
			 and a.company_name=$compid
			 and b.id=c.po_id
			 and c.is_deleted=0 
			 and c.status_active=1
			 and d.entry_form in(0,36)
			 and d.id=c.mst_id
			 and c.insert_date between '".$prev_date."' and '".$current_date."'";
	
	$dp_data_array=sql_select($kgfi_sql);
	$dp_arr=array();
	foreach ($dp_data_array as $row)
	{ 
		$dp_arr[$row[csf("job_no")]]+=$row[csf("batch_qnty")];
		$dp_booking_arr[$row[csf("job_no")]]=$row[csf("booking_no")];
		$job_arr[$row[csf("job_no")]]=$row[csf("job_no")];
	}




//------------------------------------Finish Fabric Issue
$ffi_sql="
		select 
			a.style_ref_no,
			a.job_no,
			c.quantity
		from 
			wo_po_details_master a, 
			wo_po_break_down b,
			order_wise_pro_details c
		 where 
			 a.job_no=b.job_no_mst 
			 and a.company_name=$compid
			 and b.id=c.po_breakdown_id
			 and c.entry_form=18
			 and c.trans_type=2 
			 and c.is_deleted=0 
			 and c.status_active=1 
			 and c.insert_date between '".$prev_date."' and '".$current_date."'";
	
	$ffi_data_array=sql_select($ffi_sql);
	$dp_arr=array();
	foreach ($ffi_data_array as $row)
	{ 
		$ffi_arr[$row[csf("job_no")]]+=$row[csf("quantity")];
		$job_arr[$row[csf("job_no")]]=$row[csf("job_no")];
	}





//------------------------------------Cutting (Job Wise)/Sewing Production (Job Wise) /RMG Finishing (Job Wise)
$cjw_sql="
		select 
			a.style_ref_no,
			a.job_no,
			c.production_quantity,
			c.production_type 	
		from
			wo_po_details_master a, 
			wo_po_break_down b,
			pro_garments_production_mst c
		where 
			 a.job_no=b.job_no_mst 
			 and a.company_name=$compid
			 and b.id=c.po_break_down_id
			 and c.production_type in(1,5,8)
			 and c.is_deleted=0 
			 and c.status_active=1 
			 and c.insert_date between '".$prev_date."' and '".$current_date."'";
	
	$cjw_data_array=sql_select($cjw_sql);
	$cjw_arr=array();$spjw_arr=array();$rmg_arr=array();
	foreach ($cjw_data_array as $row)
	{ 
		if($row[csf("production_type")]==1){
			$cjw_arr[$row[csf("job_no")]]+=$row[csf("production_quantity")];
			$job_arr[$row[csf("job_no")]]=$row[csf("job_no")];
		}
		//Sewing Production (Job Wise)
		if($row[csf("production_type")]==5){
			$spjw_arr[$row[csf("job_no")]]+=$row[csf("production_quantity")];
			$job_arr[$row[csf("job_no")]]=$row[csf("job_no")];
		}
		//RMG Finishing (Job Wise)
		if($row[csf("production_type")]==8){
			$rmg_arr[$row[csf("job_no")]]+=$row[csf("production_quantity")];
			$job_arr[$row[csf("job_no")]]=$row[csf("job_no")];
		}
	}




//------------------------------------------------------------------------------------
	$txt_job_no=implode("','",$job_arr);
	if($db_type==0)
	{	
	   $sql = "select a.job_no,a.company_name, a.buyer_name,a.style_ref_no, a.gmts_item_id,a.order_uom, a.avg_unit_price, b.costing_per,b.budget_minute,b.approved, c.fab_knit_req_kg, c.fab_woven_req_yds, c.fab_yarn_req_kg,sum(d.plan_cut) as job_quantity,sum(d.po_quantity) as ord_qty
			from wo_po_details_master a, wo_pre_cost_mst b,wo_pre_cost_sum_dtls c,wo_po_break_down d
			where a.job_no=b.job_no and b.job_no=c.job_no and c.job_no=d.job_no_mst and a.status_active=1 and a.is_deleted =0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.job_no in('$txt_job_no') and a.company_name=$compid group by a.job_no,a.company_name, a.buyer_name,a.style_ref_no, a.gmts_item_id,a.order_uom, a.avg_unit_price, b.costing_per,b.approved,b.budget_minute, c.fab_knit_req_kg, c.fab_woven_req_yds, c.fab_yarn_req_kg order by a.job_no"; 
	}
	if($db_type==2)
	{	
	   $sql = "select a.job_no,a.company_name, a.buyer_name,a.style_ref_no, a.gmts_item_id,a.order_uom, a.avg_unit_price, b.costing_per,b.budget_minute,b.approved,c.fab_knit_req_kg, c.fab_woven_req_yds, c.fab_yarn_req_kg,sum(d.plan_cut) as job_quantity,sum(d.po_quantity) as ord_qty
			from wo_po_details_master a, wo_pre_cost_mst b,wo_pre_cost_sum_dtls c,wo_po_break_down d
			where a.job_no=b.job_no and b.job_no=c.job_no and c.job_no=d.job_no_mst and a.status_active=1 and a.is_deleted =0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0  and  a.job_no in('$txt_job_no') and a.company_name=$compid group by a.job_no,a.company_name, a.buyer_name,a.style_ref_no, a.gmts_item_id,a.order_uom, a.avg_unit_price, b.costing_per,b.approved,b.budget_minute, c.fab_knit_req_kg, c.fab_woven_req_yds, c.fab_yarn_req_kg  order by a.job_no"; //a.job_quantity as job_quantity,
	}
	//echo $sql;
$data_array=sql_select($sql);
	$buyer_arr=array(); $style_arr=array();			  
	foreach ($data_array as $row)
	{	
		if($row[csf("costing_per")]==1){$order_price_per_dzn=12;}
		else if($row[csf("costing_per")]==2){$order_price_per_dzn=1;}
		else if($row[csf("costing_per")]==3){$order_price_per_dzn=24;}
		else if($row[csf("costing_per")]==4){$order_price_per_dzn=36;}
		else if($row[csf("costing_per")]==5){$order_price_per_dzn=48;}
		$buyer_arr[$row[csf("job_no")]]=$buyer_library[$row[csf("buyer_name")]];
		$style_arr[$row[csf("job_no")]]=$row[csf("style_ref_no")];
	}
	
	
	

	
	
	$sql = "select id, job_no,item_number_id, body_part_id, fab_nature_id, color_type_id, fabric_description, avg_cons, fabric_source, rate, amount,avg_finish_cons,status_active from wo_pre_cost_fabric_cost_dtls 
			where job_no in('$txt_job_no')";
	$data_array=sql_select($sql);
		
		foreach( $data_array as $row )
        {
			    $set_item_ratio=return_field_value("set_item_ratio"," wo_po_details_mas_set_details", "job_no='".$row[csf('job_no')]."' and gmts_item_id='".$row[csf('item_number_id')]."'");

		
			   $fab_dtls_data=sql_select("select po_break_down_id,color_number_id,gmts_sizes,cons,requirment from wo_pre_cos_fab_co_avg_con_dtls where pre_cost_fabric_cost_dtls_id=".$row[csf("id")]." $txt_po_breack_down_id_cond2 and cons !=0");
			   $greycons=0;
			   foreach($fab_dtls_data as $fab_dtls_data_row )
			   {
				 
					 $sql_po_qty_fab=sql_select("select sum(c.plan_cut_qnty) as order_quantity  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id=".$fab_dtls_data_row[csf('po_break_down_id')]." and c.item_number_id='".$row[csf('item_number_id')]."' and size_number_id='".$fab_dtls_data_row[csf('gmts_sizes')]."' and  color_number_id= '".$fab_dtls_data_row[csf('color_number_id')]."' and a.status_active=1 and b.status_active=1 and c.status_active=1");
					 
					 list($sql_po_qty_row_fab)=$sql_po_qty_fab;
					 $po_qty_fab=$sql_po_qty_row_fab[csf('order_quantity')];
					 $greycons+=($po_qty_fab/($order_price_per_dzn*$set_item_ratio))*$fab_dtls_data_row[csf("requirment")];
			   }
				  
		
		$greycons_arr[$row[csf('job_no')]]+=$greycons;
		}

//--------------------------------------------------------------------

				  
				  
				  
//Knitting Production 
				?>
                 <table width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
                   <thead>
                        <tr><td colspan="9"><strong>Knitting Production</strong></td></tr>
                        <tr>
                            <th width="35"><strong>SL</strong></th>
                            <th width="150"><strong>Buyer</strong></th>
                            <th width="100"><strong>Job No</strong></th>
                            <th width="180"><strong>Style No</strong></th>
                            <th width="100"><strong>Booking No</strong></th>
                            <th width="80"><strong>BOM Qty.</strong></th>
                            <th width="80"><strong>Poduction Qty.</strong></th>
                            <th width="80"><strong>Excess Prod. Qty.</strong></th>
                            <th><strong>Excess %</strong></th>
                        </tr>
                   </thead>
					<?
					$i=1;$tot_bom_qty=0;$tot_pro_qty=0;
					foreach($kp_arr as $job_id=>$kp_qty) 
					{
						if($kp_qty>$greycons_arr[$job_id])
						{
							$tot_bom_qty+=$greycons_arr[$job_id];
							$tot_pro_qty+=$kp_qty;
					?>
                        <tr>
                            <td align="center"><? echo $i;?></td>
                            <td align="center"><? echo $buyer_arr[$job_id];?></td>
                            <td align="center"><? echo $job_id;?></td>
                            <td align="center"><? echo $style_arr[$job_id];?></td>
                            <td align="center"><? echo $kp_booking_no_arr[$job_id];?></td>
                            <td align="right"><? echo number_format($greycons_arr[$job_id]);?></td>
                            <td align="right"><? echo number_format($kp_qty);?></td>
                            <td align="right"><? $exqty=number_format($kp_qty-$greycons_arr[$job_id]);echo $exqty;?></td>
                            <td align="center"><? echo number_format(($exqty/$kp_qty)*100,2);?></td>
                        </tr>
							
                    <?
					$i++;
					$flag=1;
						}
					}
					?>
                   <tfoot>
                        <tr>
                            <th colspan="5" align="right">Total &nbsp;</th>
                            <th align="right"><? echo number_format($tot_bom_qty);?></th>
                            <th align="right"><? echo number_format($tot_pro_qty);?></th>
                            <th align="right"><? echo $texqty=number_format(($tot_pro_qty-$tot_bom_qty));?></th>
                            <th align="center"><? echo number_format(($texqty/$tot_pro_qty)*100,2);?></th>
                        </tr>
                   </tfoot>
                 </table>
                 
                 
<!-- ..........................Grey Fabric Receive Details--> 
<br />                
                 <table width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
                   <thead>
                        <tr><td colspan="9"><strong>Grey Fabric Receive Details</strong></td></tr>
                        <tr>
                            <th width="35"><strong>SL</strong></th>
                            <th width="150"><strong>Buyer</strong></th>
                            <th width="100"><strong>Job No</strong></th>
                            <th width="180"><strong>Style No</strong></th>
                            <th width="100"><strong>Booking No</strong></th>
                            <th width="80"><strong>BOM Qty.</strong></th>
                            <th width="80"><strong>Reveive Qty.</strong></th>
                            <th width="80"><strong>Excess Prod. Qty.</strong></th>
                            <th><strong>Excess %</strong></th>
                        </tr>
                   </thead>
					<?
					$i=1;$tot_bom_qty=0;$tot_pro_qty=0;
					foreach($gfr_arr as $job_id=>$qfr_qty) 
					{
						if($qfr_qty>$greycons_arr[$job_id])
						{
							$tot_bom_qty+=$greycons_arr[$job_id];
							$tot_pro_qty+=$qfr_qty;
					?>
                        <tr>
                            <td align="center"><? echo $i;?></td>
                            <td align="center"><? echo $buyer_arr[$job_id];?></td>
                            <td align="center"><? echo $job_id;?></td>
                            <td align="center"><? echo $style_arr[$job_id];?></td>
                            <td align="center"><? echo $gfr_booking_no_arr[$job_id];?></td>
                            <td align="right"><? echo number_format($greycons_arr[$job_id]);?></td>
                            <td align="right"><? echo number_format($qfr_qty);?></td>
                            <td align="right"><? $exqty=number_format($qfr_qty-$greycons_arr[$job_id]);echo $exqty;?></td>
                            <td align="center"><? echo number_format((($exqty/$qfr_qty)*100),2);?></td>
                        </tr>
							
                    <?
					$i++;
					$flag=1;
						}
					}
					?>
                   <tfoot>
                        <tr>
                            <th colspan="5" align="right">Total &nbsp;</th>
                            <th align="right"><? echo number_format($tot_bom_qty);?></th>
                            <th align="right"><? echo number_format($tot_pro_qty);?></th>
                            <th align="right"><? echo $texqty=number_format(($tot_pro_qty-$tot_bom_qty));?></th>
                            <th align="center"><? echo number_format(($texqty/$tot_pro_qty)*100,2);?></th>
                        </tr>
                   </tfoot>
                 </table>
                 
                 
 
 <!-- ..........................Knite Grey Fabric Receive--> 
<br />                
                 <table width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
                   <thead>
                        <tr><td colspan="9"><strong>Knite Grey Fabric Receive</strong></td></tr>
                        <tr>
                            <th width="35"><strong>SL</strong></th>
                            <th width="150"><strong>Buyer</strong></th>
                            <th width="100"><strong>Job No</strong></th>
                            <th width="180"><strong>Style No</strong></th>
                            <th width="100"><strong>Booking No</strong></th>
                            <th width="80"><strong>BOM Qty.</strong></th>
                            <th width="80"><strong>Poduction Qty.</strong></th>
                            <th width="80"><strong>Excess Prod. Qty.</strong></th>
                            <th><strong>Excess %</strong></th>
                        </tr>
                   </thead>
					<? 
					$i=1;$tot_bom_qty=0;$tot_pro_qty=0;
					foreach($kgfr_arr as $job_id=>$kqfr_qty) 
					{
						if($kqfr_qty>$greycons_arr[$job_id])
						{
							$tot_bom_qty+=$greycons_arr[$job_id];
							$tot_pro_qty+=$kqfr_qty;
					?>
                        <tr>
                            <td align="center"><? echo $i;?></td>
                            <td align="center"><? echo $buyer_arr[$job_id];?></td>
                            <td align="center"><? echo $job_id;?></td>
                            <td align="center"><? echo $style_arr[$job_id];?></td>
                            <td align="center"><? //echo $row['update_date'];?></td>
                            <td align="right"><? echo number_format($greycons_arr[$job_id]);?></td>
                            <td align="right"><? echo number_format($kqfr_qty);?></td>
                            <td align="right"><? $exqty=number_format($kqfr_qty-$greycons_arr[$job_id]);echo $exqty;?></td>
                            <td align="center"><? echo number_format(($exqty/$kqfr_qty)*100,2);?></td>
                        
                        </tr>
							
                    <?
					$i++;
					$flag=1;
						}
					}
					?>
                   <tfoot>
                        <tr>
                            <th colspan="5" align="right">Total &nbsp;</th>
                            <th align="right"><? echo number_format($tot_bom_qty);?></th>
                            <th align="right"><? echo number_format($tot_pro_qty);?></th>
                            <th align="right"><? echo $texqty=number_format(($tot_pro_qty-$tot_bom_qty));?></th>
                            <th align="center"><? echo number_format(($texqty/$tot_pro_qty)*100,2);?></th>
                        </tr>
                   </tfoot>
                 </table>
                 
              
               
               
 <!-- ..........................Grey Fabric Issue Details--> 
<br />                
                 <table width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
                   <thead>
                        <tr><td colspan="9"><strong>Grey Fabric Issue Details</strong></td></tr>
                        <tr>
                            <th width="35"><strong>SL</strong></th>
                            <th width="150"><strong>Buyer</strong></th>
                            <th width="100"><strong>Job No</strong></th>
                            <th width="180"><strong>Style No</strong></th>
                            <th width="100"><strong>Booking No</strong></th>
                            <th width="80"><strong>BOM Qty.</strong></th>
                            <th width="80"><strong>Issue Qty.</strong></th>
                            <th width="80"><strong>Excess Prod. Qty.</strong></th>
                            <th><strong>Excess %</strong></th>
                        </tr>
                   </thead>
					<? 
					$i=1;$tot_bom_qty=0;$tot_pro_qty=0;
					foreach($kgfi_arr as $job_id=>$kqfi_qty) 
					{
						if($kqfi_qty>$greycons_arr[$job_id])
						{
							$tot_bom_qty+=$greycons_arr[$job_id];
							$tot_pro_qty+=$kqfi_qty;
					?>
                        <tr>
                            <td align="center"><? echo $i;?></td>
                            <td align="center"><? echo $buyer_arr[$job_id];?></td>
                            <td align="center"><? echo $job_id;?></td>
                            <td align="center"><? echo $style_arr[$job_id];?></td>
                            <td align="center"><?// echo $row['update_date'];?></td>
                            <td align="right"><? echo number_format($greycons_arr[$job_id]);?></td>
                            <td align="right"><? echo number_format($kqfi_qty);?></td>
                            <td align="right"><? $exqty=number_format($kqfi_qty-$greycons_arr[$job_id]);echo $exqty;?></td>
                            <td align="center"><? echo number_format(($exqty/$kqfi_qty)*100,2);?></td>
                       
                        </tr>
							
                    <?
					$i++;
					$flag=1;
						}
					}
					?>
                   <tfoot>
                        <tr>
                            <th colspan="5" align="right">Total &nbsp;</th>
                            <th align="right"><? echo number_format($tot_bom_qty);?></th>
                            <th align="right"><? echo number_format($tot_pro_qty);?></th>
                            <th align="right"><? echo $texqty=number_format(($tot_pro_qty-$tot_bom_qty));?></th>
                            <th align="center"><? echo number_format(($texqty/$tot_pro_qty)*100,2);?></th>
                        </tr>
                   </tfoot>
                 </table>
               
               
    
 <!-- ..........................Batch Creation Qty--> 
<br />                
                 <table width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
                   <thead>
                        <tr><td colspan="9"><strong>Batch Creation Qty</strong></td></tr>
                        <tr>
                            <th width="35"><strong>SL</strong></th>
                            <th width="150"><strong>Buyer</strong></th>
                            <th width="100"><strong>Job No</strong></th>
                            <th width="180"><strong>Style No</strong></th>
                            <th width="100"><strong>Booking No</strong></th>
                            <th width="80"><strong>BOM Qty.</strong></th>
                            <th width="80"><strong>Batch Qty.</strong></th>
                            <th width="80"><strong>Excess Prod. Qty.</strong></th>
                            <th><strong>Excess %</strong></th>
                        </tr>
                   </thead>
					<?
					$i=1;$tot_bom_qty=0;$tot_pro_qty=0;
					foreach($bc_arr as $job_id=>$bc_qty) 
					{
						if($bc_qty>$greycons_arr[$job_id])
						{
							$tot_bom_qty+=$greycons_arr[$job_id];
							$tot_pro_qty+=$bc_qty;
					?>
                        <tr>
                            <td align="center"><? echo $i;?></td>
                            <td align="center"><? echo $buyer_arr[$job_id];?></td>
                            <td align="center"><? echo $job_id;?></td>
                            <td align="center"><? echo $style_arr[$job_id];?></td>
                            <td align="center"><? echo $bc_booking_arr[$job_id];?></td>
                            <td align="right"><? echo number_format($greycons_arr[$job_id]);?></td>
                            <td align="right"><? echo number_format($bc_qty);?></td>
                            <td align="right"><? $exqty=number_format($bc_qty-$greycons_arr[$job_id]);echo $exqty;?></td>
                            <td align="center"><? echo number_format(($exqty/$bc_qty)*100,2);?></td>
                        </tr>
							
                    <?
					$i++;
					$flag=1;
						}
					}
					?>
                   <tfoot>
                        <tr>
                            <th colspan="5" align="right">Total &nbsp;</th>
                            <th align="right"><? echo number_format($tot_bom_qty);?></th>
                            <th align="right"><? echo number_format($tot_pro_qty);?></th>
                            <th align="right"><? echo $texqty=number_format(($tot_pro_qty-$tot_bom_qty));?></th>
                            <th align="center"><? echo number_format(($texqty/$tot_pro_qty)*100,2);?></th>
                        </tr>
                   </tfoot>
                 </table>
               
    
 <!-- .........................Dyeing Production Qty--> 
<br />                
                 <table width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
                   <thead>
                        <tr><td colspan="9"><strong>Dyeing Production Qty</strong></td></tr>
                        <tr>
                            <th width="35"><strong>SL</strong></th>
                            <th width="150"><strong>Buyer</strong></th>
                            <th width="100"><strong>Job No</strong></th>
                            <th width="180"><strong>Style No</strong></th>
                            <th width="100"><strong>Booking No</strong></th>
                            <th width="80"><strong>BOM Qty.</strong></th>
                            <th width="80"><strong>Deying Production Qty.</strong></th>
                            <th width="80"><strong>Excess Prod. Qty.</strong></th>
                            <th><strong>Excess %</strong></th>
                        </tr>
                   </thead>
					<?
					$i=1;$tot_bom_qty=0;$tot_pro_qty=0;
					foreach($dp_arr as $job_id=>$dp_qty) 
					{
						if($dp_qty>$greycons_arr[$job_id])
						{
							$tot_bom_qty+=$greycons_arr[$job_id];
							$tot_pro_qty+=$dp_qty;
					?>
                        <tr>
                            <td align="center"><? echo $i;?></td>
                            <td align="center"><? echo $buyer_arr[$job_id];?></td>
                            <td align="center"><? echo $job_id;?></td>
                            <td align="center"><? echo $style_arr[$job_id];?></td>
                            <td align="center"><? echo $dp_booking_arr[$job_id];?></td>
                            <td align="right"><? echo number_format($greycons_arr[$job_id]);?></td>
                            <td align="right"><? echo number_format($dp_qty);?></td>
                            <td align="right"><? $exqty=number_format($dp_qty-$greycons_arr[$job_id]);echo $exqty;?></td>
                            <td align="center"><? echo number_format(($exqty/$dp_qty)*100,2);?></td>
                        
                        
                        </tr>
							
                    <?
					$i++;
					$flag=1;
						}
					}
					?>
                   <tfoot>
                        <tr>
                            <th colspan="5" align="right">Total &nbsp;</th>
                            <th align="right"><? echo number_format($tot_bom_qty);?></th>
                            <th align="right"><? echo number_format($tot_pro_qty);?></th>
                            <th align="right"><? echo $texqty=number_format(($tot_pro_qty-$tot_bom_qty));?></th>
                            <th align="center"><? echo number_format(($texqty/$tot_pro_qty)*100,2);?></th>
                        </tr>
                   </tfoot>
                 </table>
               
    
    
    
 <!-- .........................Dyeing Production Qty--> 
<br />                
                 <table width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
                   <thead>
                        <tr><td colspan="9"><strong>Finish Fabric Issue</strong></td></tr>
                        <tr>
                            <th width="35"><strong>SL</strong></th>
                            <th width="150"><strong>Buyer</strong></th>
                            <th width="100"><strong>Job No</strong></th>
                            <th width="180"><strong>Style No</strong></th>
                            <th width="100"><strong>Booking No</strong></th>
                            <th width="80"><strong>BOM Qty.</strong></th>
                            <th width="80"><strong>Deying Production Qty.</strong></th>
                            <th width="80"><strong>Excess Prod. Qty.</strong></th>
                            <th><strong>Excess %</strong></th>
                        </tr>
                   </thead>
					<?
					$i=1;$tot_bom_qty=0;$tot_pro_qty=0;
					foreach($ffi_sql as $job_id=>$ffi_qty) 
					{
						if($ffi_qty>$greycons_arr[$job_id])
						{
							$tot_bom_qty+=$greycons_arr[$job_id];
							$tot_pro_qty+=$ffi_qty;
					?>
                        <tr>
                            <td align="center"><? echo $i;?></td>
                            <td align="center"><? echo $buyer_arr[$job_id];?></td>
                            <td align="center"><? echo $job_id;?></td>
                            <td align="center"><? echo $style_arr[$job_id];?></td>
                            <td align="center"><? echo $dp_booking_arr[$job_id];?></td>
                            <td align="right"><? echo number_format($greycons_arr[$job_id]);?></td>
                            <td align="right"><? echo number_format($ffi_qty);?></td>
                            <td align="right"><? $exqty=number_format($ffi_qty-$greycons_arr[$job_id]);echo $exqty;?></td>
                            <td align="center"><? echo number_format(($exqty/$ffi_qty)*100,2);?></td>
                        
                        </tr>
							
                    <?
					$i++;
					$flag=1;
						}
					}
					?>
                   <tfoot>
                        <tr>
                            <th colspan="5" align="right">Total &nbsp;</th>
                            <th align="right"><? echo number_format($tot_bom_qty);?></th>
                            <th align="right"><? echo number_format($tot_pro_qty);?></th>
                            <th align="right"><? echo $texqty=number_format(($tot_pro_qty-$tot_bom_qty));?></th>
                            <th align="center"><? echo number_format(($texqty/$tot_pro_qty)*100,2);?></th>
                        </tr>
                   </tfoot>
                 </table>
               
    
    
 <!-- .........................Cutting (Job Wise)--> 
<br />                
                 <table width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
                   <thead>
                        <tr><td colspan="9"><strong>Cutting (Job Wise)</strong></td></tr>
                        <tr>
                            <th width="35"><strong>SL</strong></th>
                            <th width="150"><strong>Buyer</strong></th>
                            <th width="100"><strong>Job No</strong></th>
                            <th width="180"><strong>Style No</strong></th>
                            <th width="100"><strong>Booking No</strong></th>
                            <th width="80"><strong>BOM Qty.</strong></th>
                            <th width="80"><strong>Cut Qty.</strong></th>
                            <th width="80"><strong>Excess Prod. Qty.</strong></th>
                            <th><strong>Excess %</strong></th>
                        </tr>
                   </thead>
					<?
					$i=1;$tot_bom_qty=0;$tot_pro_qty=0;
					foreach($cjw_arr as $job_id=>$cjw_qty) 
					{
						if($cjw_qty>$greycons_arr[$job_id])
						{
							$tot_bom_qty+=$greycons_arr[$job_id];
							$tot_pro_qty+=$cjw_qty;
					?>
                        <tr>
                            <td align="center"><? echo $i;?></td>
                            <td align="center"><? echo $buyer_arr[$job_id];?></td>
                            <td align="center"><? echo $job_id;?></td>
                            <td align="center"><? echo $style_arr[$job_id];?></td>
                            <td align="center"><? echo $dp_booking_arr[$job_id];?></td>
                            <td align="right"><? echo number_format($greycons_arr[$job_id]);?></td>
                            <td align="right"><? echo number_format($cjw_qty);?></td>
                            <td align="right"><? $exqty=number_format($cjw_qty-$greycons_arr[$job_id]);echo $exqty;?></td>
                            <td align="center"><? echo number_format(($exqty/$cjw_qty)*100,2);?></td>
                        
                        </tr>
							
                    <?
					$i++;
					$flag=1;
						}
					}
					?>
                   <tfoot>
                        <tr>
                            <th colspan="5" align="right">Total &nbsp;</th>
                            <th align="right"><? echo number_format($tot_bom_qty);?></th>
                            <th align="right"><? echo number_format($tot_pro_qty);?></th>
                            <th align="right"><? echo $texqty=number_format(($tot_pro_qty-$tot_bom_qty));?></th>
                            <th align="center"><? echo number_format(($texqty/$tot_pro_qty)*100,2);?></th>
                        </tr>
                   </tfoot>
                 </table>
               
    
 <!-- .........................Sewing Production (Job Wise)--> 
<br />                
                 <table width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
                   <thead>
                        <tr><td colspan="9"><strong>Sewing Production (Job Wise)</strong></td></tr>
                        <tr>
                            <th width="35"><strong>SL</strong></th>
                            <th width="150"><strong>Buyer</strong></th>
                            <th width="100"><strong>Job No</strong></th>
                            <th width="180"><strong>Style No</strong></th>
                            <th width="100"><strong>Booking No</strong></th>
                            <th width="80"><strong>BOM Qty.</strong></th>
                            <th width="80"><strong>Sewing Prod. Qty.</strong></th>
                            <th width="80"><strong>Excess Prod. Qty.</strong></th>
                            <th><strong>Excess %</strong></th>
                        </tr>
                   </thead>
					<?
					$i=1;$tot_bom_qty=0;$tot_pro_qty=0;
					foreach($spjw_arr as $job_id=>$spjw_qty) 
					{
						if($spjw_qty>$greycons_arr[$job_id])
						{
							$tot_bom_qty+=$greycons_arr[$job_id];
							$tot_pro_qty+=$spjw_qty;
					?>
                        <tr>
                            <td align="center"><? echo $i;?></td>
                            <td align="center"><? echo $buyer_arr[$job_id];?></td>
                            <td align="center"><? echo $job_id;?></td>
                            <td align="center"><? echo $style_arr[$job_id];?></td>
                            <td align="center"><? echo $dp_booking_arr[$job_id];?></td>
                            <td align="right"><? echo number_format($greycons_arr[$job_id]);?></td>
                            <td align="right"><? echo number_format($spjw_qty);?></td>
                            <td align="right"><? $exqty=number_format($spjw_qty-$greycons_arr[$job_id]);echo $exqty;?></td>
                            <td align="center"><? echo number_format(($exqty/$spjw_qty)*100,2);?></td>
                        
                        </tr>
							
                    <?
					$i++;
					$flag=1;
						}
					}
					?>
                   <tfoot>
                        <tr>
                            <th colspan="5" align="right">Total &nbsp;</th>
                            <th align="right"><? echo number_format($tot_bom_qty);?></th>
                            <th align="right"><? echo number_format($tot_pro_qty);?></th>
                            <th align="right"><? echo $texqty=number_format(($tot_pro_qty-$tot_bom_qty));?></th>
                            <th align="center"><? echo number_format(($texqty/$tot_pro_qty)*100,2);?></th>
                        </tr>
                   </tfoot>
                 </table>
               
               
 <!-- .........................RMG Finishing (Job Wise)--> 
<br />                
                 <table width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
                   <thead>
                        <tr><td colspan="9"><strong>RMG Finishing (Job Wise)</strong></td></tr>
                        <tr>
                            <th width="35"><strong>SL</strong></th>
                            <th width="150"><strong>Buyer</strong></th>
                            <th width="100"><strong>Job No</strong></th>
                            <th width="180"><strong>Style No</strong></th>
                            <th width="100"><strong>Booking No</strong></th>
                            <th width="80"><strong>BOM Qty.</strong></th>
                            <th width="80"><strong>Finishing Qty.</strong></th>
                            <th width="80"><strong>Excess Prod. Qty.</strong></th>
                            <th><strong>Excess %</strong></th>
                        </tr>
                   </thead>
					<?
					$i=1;$tot_bom_qty=0;$tot_pro_qty=0;
					foreach($rmg_arr as $job_id=>$rmg_qty) 
					{
						if($rmg_qty>$greycons_arr[$job_id])
						{
							$tot_bom_qty+=$greycons_arr[$job_id];
							$tot_pro_qty+=$rmg_qty;
					?>
                        <tr>
                            <td align="center"><? echo $i;?></td>
                            <td align="center"><? echo $buyer_arr[$job_id];?></td>
                            <td align="center"><? echo $job_id;?></td>
                            <td align="center"><? echo $style_arr[$job_id];?></td>
                            <td align="center"><? echo $dp_booking_arr[$job_id];?></td>
                            <td align="right"><? echo number_format($greycons_arr[$job_id]);?></td>
                            <td align="right"><? echo number_format($rmg_qty);?></td>
                            <td align="right"><? $exqty=number_format($rmg_qty-$greycons_arr[$job_id]);echo $exqty;?></td>
                            <td align="center"><? echo number_format(($exqty/$rmg_qty)*100,2);?></td>
                        
                        </tr>
							
                    <?
					$i++;
					$flag=1;
						}
					}
					?>
                   <tfoot>
                        <tr>
                            <th colspan="5" align="right">Total &nbsp;</th>
                            <th align="right"><? echo number_format($tot_bom_qty);?></th>
                            <th align="right"><? echo number_format($tot_pro_qty);?></th>
                            <th align="right"><? echo $texqty=number_format(($tot_pro_qty-$tot_bom_qty));?></th>
                            <th align="center"><? echo number_format(($texqty/$tot_pro_qty)*100,2);?></th>
                        </tr>
                   </tfoot>
                 </table>
               
                 
            </td>
        </tr>
    </table>
    
<? 
		$sum_qty=0;
		$sum_val=0;
		$grant_sum_qty=0;
		$grant_sum_val=0;

		
	$to="";	
	$sql = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=8 and b.mail_user_setup_id=c.id and a.company_id=$compid";
	$mail_sql=sql_select($sql);
	foreach($mail_sql as $row)
	{
		if ($to=="")  $to=$row[csf('email_address')]; else $to=$to.", ".$row[csf('email_address')]; 
	}
 	$subject = "Budget Exceeding Areas";
	
	$message="";
	$message=ob_get_contents();
	ob_clean();
	$header=mail_header();
	
	//$to="muktobani@gmail.com,saeed@logicsoftbd.com,rasel.mia@logicsoftbd.com";
	if($to!="" && $flag==1)echo send_mail_mailer( $to, $subject, $message, $from_mail);
 
//echo $message;die; 
}
	
	




?> 