<?php
//Developed for youth group
/*-------------------------------------------- Comments -----------------------
Purpose			         : 	Total Company Activities auto mail.
Functionality	         :	
JS Functions	         :
Created by		         :	REZA && MD MAMUN
Creation date 	         :  03-11-2021
Requirment Client        : 
Requirment By            : 
Requirment type          : 
Requirment               : 
Affected page            : 
Affected Code            :                   
DB Script                : 
Updated by 		         : 		
Update date		         : 		   
QC Performed BY	         :		
QC Date			         :	
Comments		         :  Developd for Youth Group 
-------------------------------------------------------------------------------*/
date_default_timezone_set("Asia/Dhaka");

require_once('../includes/common.php');
// require_once('../mailer/class.phpmailer.php');
require_once('setting/mail_setting.php');

$is_insert_date_active=1;

$sql="select id,team_member_name,member_contact_no from lib_mkt_team_member_info where  status_active =1 and is_deleted=0";
$data_array=sql_select($sql);
foreach( $data_array as $row )
{ 
	$dealing_merchant_arr[$row[csf("id")]]=$row[csf("team_member_name")].'<br>'.$row[csf("member_contact_no")];
}

 
$team_leader_name_arr = return_library_array( "select id,team_leader_name from lib_marketing_team where  status_active =1 and is_deleted=0", "id", "team_leader_name");
$buyer_library = return_library_array( "select id, buyer_name from lib_buyer where  status_active=1 and is_deleted=0", "id", "buyer_name");
$party_library = return_library_array( "select id, other_party_name from lib_other_party where  status_active=1 and is_deleted=0", "id", "other_party_name");
$user_arr = return_library_array( "select id,user_full_name from user_passwd where valid=1","id","user_full_name");
$country_arr = return_library_array( "select id, country_name from lib_country", "id", "country_name");


$company_lib = return_library_array( "select id, company_short_name from lib_company where status_active=1 and is_deleted=0", "id", "company_short_name");

$supplier_lib = return_library_array( "select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
$yarn_count_lib = return_library_array( "select id,yarn_count from lib_yarn_count where status_active=1 and is_deleted=0","id","yarn_count");



 $floor_lib = return_library_array( "select id, FLOOR_NAME from LIB_PROD_FLOOR where status_active=1 and is_deleted=0 ", "id", "FLOOR_NAME");

 
	if($db_type==0)
	{
		$current_date = date("Y-m-d H:i:s",time());
		if($_REQUEST['view_date']){
			$current_date = change_date_format(date("Y-m-d H:i:s",strtotime($_REQUEST['view_date'])),'','',1);
		}
		$previous_date= date('Y-m-d H:i:s', strtotime('-1 day', time())); 
	}
	else
	{
		// $current_date = change_date_format(date("Y-M-d H:i:s",time()),'','',1);
		$current_date = change_date_format(date('Y-m-d H:i:s', strtotime('-1 day', time())),'','',1);
		if($_REQUEST['view_date']){
			$current_date = change_date_format(date("Y-m-d H:i:s",strtotime($_REQUEST['view_date'])),'','',1);
		}
		$previous_date= change_date_format(date('Y-m-d H:i:s', strtotime('-1 day', time())),'','',1);
		
	}


	// echo $current_date."=".$previous_date;
	
 	if($db_type==0){
		$str_cond	=" and insert_date between '".$previous_date."' and '".$current_date."'";
		$str_cond_a	=" and a.insert_date between '".$previous_date."' and '".$current_date."'";
		$str_cond_b	=" and b.insert_date between '".$previous_date."' and '".$current_date."'";
		$str_cond_c	=" and c.insert_date between '".$previous_date."' and '".$current_date."'";
		$str_cond_d	=" and d.insert_date between '".$previous_date."' and '".$current_date."'";
		$str_cond_e	=" and approved_date between '".$previous_date."' and '".$current_date."'";
	}
	else
	{
		$str_cond	=" and insert_date between '".$previous_date."' and '".$current_date." 11:59:59 PM'";
		$str_cond_a	=" and a.insert_date between '".$previous_date."' and '".$current_date." 11:59:59 PM'";
		$str_cond_b	=" and b.insert_date between '".$previous_date."' and '".$current_date." 11:59:59 PM'";
		$str_cond_c	=" and c.insert_date between '".$previous_date."' and '".$current_date." 11:59:59 PM'";
		$str_cond_d	=" and d.insert_date between '".$previous_date."' and '".$current_date." 11:59:59 PM'";
		$str_cond_e	=" and approved_date between '".$previous_date."' and '".$current_date." 11:59:59 PM'";
	}
		
		$str_cond_f	=" and a.receive_date between '".$previous_date."' and '".$current_date."'";
		$str_cond_g	=" and a.product_date between '".$previous_date."' and '".$current_date."'";
		$str_cond_h=" and f.process_end_date between '".$previous_date."' and '".$current_date."'";
		$order_date_cond=" and b.po_received_date between '".$previous_date."' and '".$current_date."'";
	


foreach($company_lib as $company_id=>$compname)/// Total Activities
{
	ob_start();
	?>
    
    
    <table width="920">
        <tr>
            <td valign="top" align="center">
                <font size="+2">Total  Production Activities of ( Date :<?  echo date("d-m-Y", strtotime($previous_date));  ?>)[Entry Date Wise]</font>
            </td>
        </tr>
        <tr>
            <td valign="top" align="center">
                <? echo $company_lib[$company_id];  ?>
            </td>
        </tr>
    </table>
 
 
 <table width="920" cellpadding="0" cellspacing="0" rules="all" border="1">
    <tr>
        <td colspan="13" align="center"><b>Order Received</b></td>
    </tr>
    <tr>
        <td rowspan="2" width="120" align="center"><b>Buyer</b></td>
        <td rowspan="2" width="120" align="center"><b>Job</b></td>
        <td rowspan="2" width="120" align="center"><b>Internal Ref.</b></td>
        <td rowspan="2" width="80" align="center"><b>Avg. Lead Time</b></td>
        <td colspan="3" align="center"><b>Confirm Order</b></td>
        <td colspan="3" align="center"><b>Projected Order</b></td>
        <td colspan="3" align="center"><b>Total</b></td>
    </tr>
    <tr bgcolor="#EEE">
        <td width="85" align="center"><b>Qty(Pcs)</b></td>
        <td width="85" align="center"><b>Value(USD)</b></td>
        <td width="80" align="center"><b>Avg. Rate</b></td>
        <td width="85" align="center"><b>Qty.(Pcs)</b></td>
        <td width="85" align="center"><b>Value(USD)</b></td>
        <td width="80" align="center"><b>Avg. Rate</b></td>
        <td width="85" align="center"><b>Qty.(Pcs)</b></td>
        <td width="85" align="center"><b>Value(USD)</b></td>
        <td width="85" align="center"><b>Avg. Rate</b></td>
    </tr>
    <?
    
    if($is_insert_date_active==0){
        $str_cond_b=" and b.PUB_SHIPMENT_DATE between '".$previous_date."' and '".$current_date."'";
    }
    
    // $orderSql="select A.BUYER_NAME,A.JOB_NO,B.GROUPING,B.PUB_SHIPMENT_DATE,B.PO_RECEIVED_DATE,B.ID,b.IS_CONFIRMED,(a.total_set_qnty*b.po_quantity) as PO_QTY, b.po_total_price as PO_VALUE from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name = '$company_id' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.shiping_status=1 $str_cond_b"; //and b.id=28856

	$orderSql="select A.BUYER_NAME,A.JOB_NO,B.GROUPING,B.PUB_SHIPMENT_DATE,B.PO_RECEIVED_DATE,B.ID,b.IS_CONFIRMED,(a.total_set_qnty*b.po_quantity) as PO_QTY, b.po_total_price as PO_VALUE ,a.company_name 	from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0
	 and b.status_active=1 and b.shiping_status=1 $order_date_cond order by A.BUYER_NAME asc";

    //  echo $orderSql;//die;
    
    $orderSqlRs=sql_select($orderSql);
    $orderDataArr=array(); 
    foreach($orderSqlRs as $rows)
    {
        $daysOnHand = datediff("d",$rows[csf('po_received_date')],$rows[csf('pub_shipment_date')]);
    
        $key=$rows[BUYER_NAME].'**'.$rows[JOB_NO].'**'.$rows[GROUPING];
        $orderDataArr[QTY][$key][$rows[IS_CONFIRMED]]+=$rows[PO_QTY];
        $orderDataArr[VAL][$key][$rows[IS_CONFIRMED]]+=$rows[PO_VALUE];
        $orderDataArr[LEAD_TIME][$key]+=$daysOnHand;

  		  $job_wise_po_no[$rows[JOB_NO]]+=1;
    }
    unset($orderSqlRs);
    
	// echo "<pre>";
	// print_r($job_wise_po_no);
    $grandTotal=array();	
    $i=1;	
    foreach($orderDataArr[QTY] as $key=>$rowArr)
    {
        list($buyer_id,$job_no,$internal_file)=explode('**',$key);
        $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
        
        $avgRage[1]=$orderDataArr[VAL][$key][1]/$orderDataArr[QTY][$key][1];
        $avgRage[2]=$orderDataArr[VAL][$key][2]/$orderDataArr[QTY][$key][2];
        
        $grandTotal[conf_qty]+=$orderDataArr[QTY][$key][1];
        $grandTotal[conf_val]+=$orderDataArr[VAL][$key][1];
        
        $grandTotal[proj_qty]+=$orderDataArr[QTY][$key][2];
        $grandTotal[proj_val]+=$orderDataArr[VAL][$key][2];
        
        $grandTotal[tot_qty]+=array_sum($orderDataArr[QTY][$key]);
        $grandTotal[tot_val]+=array_sum($orderDataArr[VAL][$key]);
        
        
    ?>
    <tr bgcolor="<? echo $bgcolor; ?>">
        <td align="center"><?=$buyer_library[$buyer_id]; ?></td>
        <td align="center"><?=$job_no;?></td>
        <td align="center"><?=$internal_file;?></td>
        <td align="center"><?=$orderDataArr[LEAD_TIME][$key]/$job_wise_po_no[$job_no];?></td>
    
        <td align="right"><?=fn_number_format($orderDataArr[QTY][$key][1], 2,'.',',') ;?></td>
        <td align="right"><?=fn_number_format($orderDataArr[VAL][$key][1], 2,'.',',') ;?></td>                        
        <td align="right"><?=fn_number_format($avgRage[1],2);?></td>
    
        <td align="right"><?=fn_number_format($orderDataArr[QTY][$key][2], 2,'.',',') ;?></td>
        <td align="right"><?=fn_number_format($orderDataArr[VAL][$key][3], 2,'.',',') ;?></td>                        
        <td align="right"><?=fn_number_format($avgRage[2],2,'.',',');?></td>
            
        <td align="right"><?= fn_number_format(array_sum($orderDataArr[QTY][$key]), 2,'.',',') ;?></td>
        <td align="right"><?= fn_number_format(array_sum($orderDataArr[VAL][$key]), 2,'.',',') ;?></td>
        <td align="right" title="<?= array_sum($orderDataArr[VAL][$key]).'/'.array_sum($orderDataArr[QTY][$key]);?>"><?=fn_number_format(array_sum($orderDataArr[VAL][$key])/array_sum($orderDataArr[QTY][$key]),2,'.',',');?></td>
    </tr>
    <?	
    $i++;
    }
    ?> 
    <tr>
        <tfoot bgcolor="#EEE">
            <th colspan="4">Total</th>
            <th align="right"><?=fn_number_format($grandTotal[conf_qty], 2,'.',',') ;?></th>
            <th align="right"><?=fn_number_format($grandTotal[conf_val], 2,'.',',') ;?></th>
            <th align="right"><?//echo fn_number_format($grandTotal[conf_val]/$grandTotal[conf_qty],2);?></th>
            <th align="right"><?=fn_number_format($grandTotal[proj_qty], 2,'.',',') ;?></th>
            <th align="right"><?=fn_number_format($grandTotal[proj_val], 2,'.',',') ;?></th>
            <th align="right"><?=fn_number_format($grandTotal[proj_val]/$grandTotal[proj_qty],2,'.',',');?></th>
            <th align="right"><?=fn_number_format($grandTotal[tot_qty], 2,'.',',') ;?></th>
            <th align="right"><?=fn_number_format($grandTotal[tot_val], 2,'.',',') ;?></th>
            <th align="right"><?=fn_number_format($grandTotal[tot_val]/$grandTotal[tot_qty],2,'.',',');?></th>
        </tfoot>
    </tr>
 </table>  
    
    
 
  

 

 
	<?
	if($is_insert_date_active==0){
		$str_cond_f	=" and a.receive_date between '".$previous_date."' and '".$current_date."'";					
	}
	$dataArr=array();

	$sql_sample_sam="select a.FLOOR, a.BOOKING_NO,
	sum(case when a.booking_without_order=1 and b.machine_no_id>0  then b.grey_receive_qnty end ) as sample_qty,
	sum(case when a.booking_without_order=1 and b.machine_no_id>0  then b.reject_fabric_receive end ) as reject_fabric_receive
	from inv_receive_master a, pro_grey_prod_entry_dtls b where a.entry_form=2 and a.item_category=13 and a.id=b.mst_id and a.receive_basis!=4  and a.knitting_company=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_without_order=1 $str_cond_f group by a.FLOOR, a.BOOKING_NO";
	//echo $sql_sample_sam;die;
	$sql_sample_samary=sql_select( $sql_sample_sam);
	
	foreach($sql_sample_samary as $inf)
	{
		$bookingSplitArr=explode("-",$inf[BOOKING_NO]);
		$without_booking_no=$bookingSplitArr[1];
		if($without_booking_no=='SMN')
		{
			$dataArr[$inf[FLOOR]]['with_out_qty']+= $inf[csf('sample_qty')];
		}
		else if($without_booking_no=='SM')
		{
			$dataArr[$row[FLOOR]]['with_qty']+= $row[csf('sample_qty')];
		}
	}
	unset($sql_sample_samary);

	//--------------------------------------------//SMN
	 $sql_qty="Select a.FLOOR,a.BOOKING_ID, 
	 sum(case when a.knitting_source=1 and b.machine_no_id>0 then c.quantity end ) as qtyinhouse, 
	 sum(case when a.knitting_source=1 and b.machine_no_id>0 then c.reject_qty end ) as reject_qty_inhouse, 
	 sum(case when a.knitting_source=3 then c.quantity end ) as qtyoutbound, 
	 sum(case when a.knitting_source=3 then c.reject_qty end ) as reject_qty_outbound ,f.job_no,g.BOOKING_NO
	 from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c,wo_po_break_down e, wo_po_details_master f left join wo_booking_mst g on f.job_no=g.job_no  where a.item_category=13 and a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=e.id and e.job_no_mst=f.job_no and c.entry_form=2 and c.trans_type=1 and a.receive_basis!=4 and a.knitting_company=$company_id   $str_cond_f and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.FLOOR,a.BOOKING_ID ,f.job_no,g.BOOKING_NO";
	 //echo $sql_qty; die;
		$k=1;
		$sql_result=sql_select( $sql_qty);
		foreach($sql_result as $row)
		{
			$bookingSplitArr=explode("-",$row[BOOKING_NO]);
			$without_booking_no=$bookingSplitArr[1];
			if($without_booking_no!='SMN' || $without_booking_no!='SM')
			{
				$dataArr[$row[FLOOR]]['in_qty']+= $row[csf('qtyinhouse')];
				$dataArr[$row[FLOOR]]['out_qty']+= $row[csf('qtyoutbound')];
			}
			else if($without_booking_no=='SMN')
			{
				$dataArr[$row[FLOOR]]['with_out_qty']+=$row[csf('qtyinhouse')]+$row[csf('qtyoutbound')];
			}
			else if($without_booking_no=='SM')
			{
				$dataArr[$row[FLOOR]]['with_qty']+=$row[csf('qtyinhouse')]+$row[csf('qtyoutbound')];
			}

		}

	//------------------------------------------------------------------------------
	 $sql_sample_sam_with="select a.FLOOR,a.BOOKING_ID, sum(case when a.booking_without_order=0 and b.machine_no_id>0 then b.grey_receive_qnty end ) as with_ord_sample_qty, 
	 sum(case when a.booking_without_order=0 and b.machine_no_id>0 then b.reject_fabric_receive end ) as reject_fabric_receive ,f.job_no,g.BOOKING_NO
	 from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, lib_machine_name d, wo_po_break_down e, wo_po_details_master f left join wo_booking_mst g on f.job_no=g.job_no where c.po_breakdown_id=e.id and a.entry_form=2 and a.item_category=13 and a.id=b.mst_id and b.id=c.dtls_id and e.job_no_mst=f.job_no and c.entry_form=2 and c.trans_type=1 and a.knitting_source=1 and a.receive_basis!=4  and a.knitting_company=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_without_order in(0)  $str_cond_f group by a.FLOOR,a.BOOKING_ID,f.job_no,g.BOOKING_NO";
	//	echo $sql_sample_sam_with;die;
	$sql_sample_samary_with=sql_select( $sql_sample_sam_with);
	foreach($sql_sample_samary_with as $row)
	{
		$bookingSplitArr=explode("-",$row[BOOKING_NO]);
		$without_booking_no=$bookingSplitArr[1];
		if($without_booking_no=='SM')
		{
			$dataArr[$row[FLOOR]]['with_qty']+= $row[csf('with_ord_sample_qty')];
		}
		else if($without_booking_no=='SMN')
		{
			$dataArr[$row[FLOOR]]['with_out_qty']+= $row[csf('with_ord_sample_qty')];
		}
	}
	// print_r($knit_buyer_samary);
	unset($sql_sample_samary_with);
	//---------------------------------------------------------------------------------------------------------------------------------------------------------------
	// $sql_service_samary=sql_select("select a.FLOOR, sum(b.grey_receive_qnty) as service_qty,sum(b.reject_fabric_receive) as reject_fabric_receive
	// 									from inv_receive_master a, pro_grey_prod_entry_dtls b where a.entry_form=22 and a.receive_basis=11 and a.item_category=13 and a.id=b.mst_id  and a.knitting_company=$company_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $str_cond_f group by a.FLOOR");

		$sql_service_samary=sql_select("select a.FLOOR, sum(b.grey_receive_qnty) as service_qty,sum(b.reject_fabric_receive) as reject_fabric_receive
		from inv_receive_master a, pro_grey_prod_entry_dtls b where  a.item_category=13 and a.id=b.mst_id    and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $str_cond_f group by a.FLOOR");
									
	foreach($sql_service_samary as $row)
	{
		$dataArr[$row[FLOOR]][out_qty]=$row[csf("service_qty")];
	}
	unset($sql_service_samary);

	//----------------------------------------------------------------------------------------------------------------------------------------------------------------		

	$knit_date_cond=" and c.receive_date between '".$previous_date."' and '".$current_date."'";		
	$knit_date_cond2=" and spm.product_date  between '".$previous_date."' and '".$current_date."'";		
	

	$knit_prod_sql="select a.id, a.roll_no, a.barcode_no, a.po_breakdown_id, a.qnty, b.booking_no as po_number, a.rf_id,c.receive_date,d.grey_receive_qnty,d.floor_id
	from pro_roll_details a, wo_non_ord_samp_booking_mst b,inv_receive_master c ,pro_grey_prod_entry_dtls d  where a.po_breakdown_id=b.id and c.id=d.mst_id and d.id=a.dtls_id  and a.entry_form=2 and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,3) and b.is_deleted=0 $knit_date_cond order by d.floor_id desc";

		//echo $knit_prod_sql;
	$knit_prod_data=sql_select($knit_prod_sql);
	foreach($knit_prod_data as $row){

		$booking_type=explode("-",$row[csf('po_number')]);
	
		if($booking_type[1]=='SMN'){
			$floor_wise[$row[csf('floor_id')]]['knit_sample']+=$row[csf('qnty')];
		}

	}


	$knit_prod_sql2="select a.po_breakdown_id, b.po_number ,c.receive_date,d.grey_receive_qnty,d.floor_id,c.knitting_source,a.qnty	from pro_roll_details a, wo_po_break_down b,inv_receive_master c,pro_grey_prod_entry_dtls d where a.po_breakdown_id=b.id and d.id=a.dtls_id and c.id=d.mst_id and a.entry_form=2 and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,3) and b.is_deleted=0 and c.knitting_source in (1,3) $knit_date_cond order by d.floor_id desc";

	// echo $knit_prod_sql2;

	$knit_prod_data2=sql_select($knit_prod_sql2);
	foreach($knit_prod_data2 as $row){

	
		if($row[csf('knitting_source')]==1){
			$floor_wise[$row[csf('floor_id')]]['knit_inhouse']+=$row[csf('qnty')];
		}else{
			$floor_wise[$row[csf('floor_id')]]['knit_subcontract']+=$row[csf('qnty')];
		}

	}

	$delivery_date_cond=" and a.delevery_date between '".$previous_date."' and '".$current_date."'";		
	//=================================delivery to qty==========================



	$delivery_sql="select a.id, sys_number_prefix_num, a.sys_number, c.qnty, a.company_id, a.knitting_source, a.knitting_company, a.location_id,a.remarks, a.delevery_date,a.floor_ids,a.insert_date, a.barcode_type from pro_grey_prod_delivery_mst a,pro_grey_prod_delivery_dtls b,pro_roll_details c 
	where a.id=b.mst_id and  b.id=c.dtls_id and b.mst_id=c.mst_id and b.order_id=c.po_breakdown_id and c.status_active=1 and c.status_active=1 and a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0  and a.entry_form=56 and c.entry_form=56 $delivery_date_cond  group by a.id, sys_number_prefix_num, a.sys_number,a.company_id, a.knitting_source, a.knitting_company, a.location_id,a.remarks, a.delevery_date,a.floor_ids,a.insert_date, a.barcode_type,c.qnty  order by sys_number_prefix_num asc";
	// echo $delivery_sql;

	$delivery_data=sql_select($delivery_sql);
	foreach($delivery_data as $row){

	
		// if($row[csf('knitting_source')]==1){
		// 	$floor_wise[$row[csf('floor_id')]]['knit_inhouse']+=$row[csf('qnty')];
		// }else{
		// 	$floor_wise[$row[csf('floor_id')]]['knit_subcontract']+=$row[csf('qnty')];
		// }
		$floor_wise[$row[csf('floor_ids')]]['delivery_qty']+=$row[csf('qnty')];

	}

	// echo "<pre>";
	// print_r($floors_wise);


		$knit_prod_sql3="SELECT spm.production_basis, spd.id, spd.process, spd.product_qnty, spd.reject_qnty, spd.uom_id, spd.floor_id,spm.product_date
		FROM subcon_production_mst spm INNER JOIN subcon_production_dtls spd ON spm.id = spd.mst_id INNER JOIN subcon_ord_dtls sod ON spd.order_id = sod.id
		 WHERE  spm.status_active = 1 AND spm.is_deleted = 0 AND spd.status_active = 1 AND spd.is_deleted = 0 AND sod.status_active = 1 AND sod.is_deleted = 0 AND spm.production_basis = 1 $knit_date_cond2 order by spd.floor_id desc";

	//	echo $knit_prod_sql3;
		$knit_prod_data3=sql_select($knit_prod_sql3);
		foreach($knit_prod_data3 as $row){
		
	
				$floor_wise[$row[csf('floor_id')]]['in_bound_sub_contract']+=$row[csf('product_qnty')];
			
		}


	?>

 

 	<table width="920" cellpadding="0" cellspacing="0" rules="all" border="1">
        <tr>
            <th colspan="8">Knitting Production</th>
        </tr>
        <tr bgcolor="#EEE">
            <th width="35">SL</th>
            <th>Floor</th>
            <th>Knitting In-House</th>
            <th>Knitting Out-Bound Subcontract</th>
            <th>Knitting Sample Production</th>
            <th>In-Bound Subcontract</th>
            <th>Total Prod. Kg</th>
            <th>Total Delivery to Store Kg</th>
        </tr>
         <?
		$i=1; 
		$knit_in_house=0;$knit_out_house=0;$knit_sample_prod=0;$in_bound_sub=0;$delivery_qty=0;
		foreach($floor_wise as $floor_id=>$rows)
		{
			
			$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
			?>
			<tr bgcolor="<? echo $bgcolor; ?>">
				<td align="center"><? echo $i; ?></td>
				<td align="center"><?=$floor_lib[$floor_id];?></td>
				<td><?=fn_number_format($rows['knit_inhouse'], 2,'.',','); ?></td>
				<td><?=fn_number_format($rows['knit_subcontract'], 2,'.',','); ?></td>
				<td align="right"><?=fn_number_format($rows['knit_sample'], 2,'.',','); ?></td>
				<td align="right"><?=fn_number_format($rows['in_bound_sub_contract'], 2,'.',','); ?></td>                 
				<td align="right"><?=fn_number_format(array_sum($floor_wise[$floor_id]), 2,'.',',');?></td>
				<td align="right"><?=fn_number_format($rows['delivery_qty'], 2,'.',','); ?></td>
			</tr>
			<?	
				$i++;
				$sum_total+=array_sum($floor_wise[$floor_id]);
				$knit_in_house+=$rows['knit_inhouse'];
				$knit_out_house+=$rows['knit_subcontract'];
				$knit_sample_prod+=$rows['knit_sample'];
				$in_bound_sub+=$rows['in_bound_sub_contract'];
				$delivery_qty+=$rows['delivery_qty'];
				
			}
			?> 
			<tr>
				<tfoot bgcolor="#EEE">
					<th></th>
					<th>Total</th>
					<th align="right"><?=$knit_in_house; ?></th>
					<th align="right"><?=$knit_out_house; ?></th>
					<th align="right"><?=$knit_sample_prod; ?></th>
					<th align="right"><?=$in_bound_sub; ?></th>
					<th align="right"><?=fn_number_format($sum_total, 2,'.',',');?></th>
					<th align="right"><?=$delivery_qty; ?></th>
				</tfoot>
			</tr>
	</table>

 


	<?
	
	if($is_insert_date_active==0){
		$str_cond_h=" and f.process_end_date between '".$previous_date."' and '".$current_date."'";					
	}
                
                
	$date_con_smry=" and a.production_date between '$previous_date' and '$current_date'";
	$workingCompanyCondSmry=" and a.service_company=$company_id"; 
	$workingCompany_name_cond2=" and a.working_company_id=$company_id"; 
                
	$sql_qty = " (select f.FLOOR_ID,a.working_company_id,a.company_id,a.batch_no, f.batch_ext_no,f.result,f.batch_id,
	  sum(case when f.service_source=1 then  a.batch_weight end) as batch_weight,
	  SUM(case when f.service_source=1 then b.batch_qnty end) AS production_qty_inhouse,
	  SUM(case when f.service_source=3 then b.batch_qnty end) AS production_qty_outbound,
	  SUM(case WHEN a.batch_against=3 and a.booking_without_order=1 then b.batch_qnty end) AS prod_qty_sample_without_order, 
	  SUM(case WHEN a.batch_against=3 and a.booking_without_order=0 then b.batch_qnty end) AS prod_qty_sample_with_order,
	  SUM(case WHEN b.is_sales=1 then b.batch_qnty end) AS fabric_sales_order_qty  
	  from pro_batch_create_dtls b, wo_po_break_down c, wo_po_details_master d, pro_fab_subprocess f,  pro_batch_create_mst a 
	  where f.batch_id=a.id $workingCompany_name_cond2  $str_cond_h and a.entry_form=0 and  a.id=b.mst_id and f.batch_id=b.mst_id and f.entry_form=35 and f.load_unload_id=2  and b.po_id=c.id and d.job_no=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and  f.status_active=1 and f.is_deleted=0  and f.result=1  
	  group by f.FLOOR_ID,a.working_company_id,a.company_id,a.batch_no,f.batch_ext_no,f.result,f.batch_id) 
	 union ( select a.FLOOR_ID,a.working_company_id,a.company_id,a.batch_no, f.batch_ext_no,f.result,f.batch_id,
	  sum(case when f.service_source=1 then  a.batch_weight end) as batch_weight,
	  SUM(case when f.service_source=1 then b.batch_qnty end) AS production_qty_inhouse,
	  SUM(case when f.service_source=3 then b.batch_qnty end) AS production_qty_outbound,
	  SUM(case WHEN a.batch_against=3 and a.booking_without_order=1 then b.batch_qnty end) AS prod_qty_sample_without_order, 
	  SUM(case WHEN a.batch_against=3 and a.booking_without_order=0 then b.batch_qnty end) AS prod_qty_sample_with_order,
	  SUM(case WHEN b.is_sales=1 then b.batch_qnty end) AS fabric_sales_order_qty 
	  from pro_batch_create_dtls b, pro_batch_create_mst a, pro_fab_subprocess f,wo_non_ord_samp_booking_mst h 
	  where  h.booking_no=a.booking_no $companyCond  $workingCompany_name_cond2 and f.batch_id=a.id and f.batch_id=b.mst_id and a.entry_form=0  and a.id=b.mst_id and f.entry_form=35 and f.load_unload_id=2  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and  f.status_active=1 and f.is_deleted=0 $str_cond_h and f.result=1 
	  group by a.FLOOR_ID,a.working_company_id,a.company_id,a.batch_no, f.batch_ext_no,f.result,f.batch_id ) ";
 	//echo $sql_qty;die;
	$sql_result=sql_select( $sql_qty);
	$batch_id_arr=array();
	foreach($sql_result as $row)
	{
		$batch_id_arr[$row[csf('batch_id')]]=$row[csf('batch_id')];

	}
	
	
	$sql="select batch_id,batch_id from pro_recipe_entry_mst where status_active=1 and is_deleted=0 and entry_form=60 ".where_con_using_array($batch_id_arr,0,'batch_id')."";
	$adding_batch_array=return_library_array( $sql,'batch_id','batch_id');	
                
                
                
                
	$production_qty_inhouse=0;
	$production_qty_outbound=0;
	$prod_qty_sample_without_order=0;
	$prod_qty_sample_with_order=0;
	$fabric_sales_order_qty=0;
	$rft_production_qty_inhouse=0;
	$rft_production_qty_outbound=0;
	$rft_prod_qty_sample_without_order=0;
	$rft_prod_qty_sample_with_order=0;
	$rft_fabric_sales_order_qty=0;
	
	$batchIDs="";
	$dataArr=array();
	foreach($sql_result as $row)
	{
		
	/*		$production_qty_inhouse+=$row[csf('production_qty_inhouse')];
		$production_qty_outbound+=$row[csf('production_qty_outbound')];
		$prod_qty_sample_without_order+=$row[csf('prod_qty_sample_without_order')];
		$prod_qty_sample_with_order+=$row[csf('prod_qty_sample_with_order')];
		$fabric_sales_order_qty+=$row[csf('fabric_sales_order_qty')];
	*/		
		if($row[csf('batch_ext_no')]>0 && $row[csf('result')]==1){
	/*			$reprocess_production_qty_inhouse+=$row[csf('production_qty_inhouse')];
			$reprocess_production_qty_outbound+=$row[csf('production_qty_outbound')];
			$reprocess_prod_qty_sample_without_order+=$row[csf('prod_qty_sample_without_order')];
			$reprocess_prod_qty_sample_with_order+=$row[csf('prod_qty_sample_with_order')];
			$reprocess_fabric_sales_order_qty+=$row[csf('fabric_sales_order_qty')];
	*/		
			$dataArr[$row[FLOOR_ID]][re_pro_dye_qty_inhouse]+=$row[csf('production_qty_inhouse')];

		}
		
	/*		if($adding_batch_array[$row[csf('batch_id')]]=='' and $row[csf('batch_ext_no')]<1 && $row[csf('result')]==1){
			$rft_production_qty_inhouse+=$row[csf('production_qty_inhouse')];
			$rft_production_qty_outbound+=$row[csf('production_qty_outbound')];
			$rft_prod_qty_sample_without_order+=$row[csf('prod_qty_sample_without_order')];
			$rft_prod_qty_sample_with_order+=$row[csf('prod_qty_sample_with_order')];
			$rft_fabric_sales_order_qty+=$row[csf('fabric_sales_order_qty')];
		}
		
		
		if($adding_batch_array[$row[csf('batch_id')]]==$row[csf('batch_id')] && $row[csf('result')]==1){
			$adding_production_qty_inhouse+=$row[csf('production_qty_inhouse')];
			$adding_production_qty_outbound+=$row[csf('production_qty_outbound')];
			$adding_prod_qty_sample_without_order+=$row[csf('prod_qty_sample_without_order')];
			$adding_prod_qty_sample_with_order+=$row[csf('prod_qty_sample_with_order')];
			$adding_fabric_sales_order_qty+=$row[csf('fabric_sales_order_qty')];
		}
	*/		
		
		$dataArr[$row[FLOOR_ID]][dye_qty_inhouse]+=$row[csf('production_qty_inhouse')];
		$dataArr[$row[FLOOR_ID]][dye_qty_outbound]+=$row[csf('production_qty_outbound')];
		$dataArr[$row[FLOOR_ID]][dye_qty_sample_with_order]+=$row[csf('prod_qty_sample_with_order')];
		
	}
				

               
	/*$k=1;$total_summary_prod_qty=0;
		$total_production_sammary=array(1=>'Inhouse (Self Order)',3=>'Sample With Order',4=>'Sample Without Order',5=>'Inbound Subcontract',6=>'Fabric Sales Order');//2=>'Outbound-Subcon',
		$total_prod_sammaryQty=$production_qty_inhouse+$production_qty_outbound+$prod_qty_sample_with_order+$prod_qty_sample_without_order+$production_qty_subcontact+$fabric_sales_order_qty;
		
		$grnd_tot_production_qty=0;
		$grnd_total_prod_per=0;
		
		$tot_rft=0;
		$tot_adding=0;
		$tot_reprocess=0;



		   if($type_id==1) //Inhouse
		   {
			$tot_production_qty=$production_qty_inhouse;
			$rft=$rft_production_qty_inhouse;
			$adding=$adding_production_qty_inhouse;
			$reprocess=$reprocess_production_qty_inhouse;
		   }
		   else  if($type_id==2) //OutBound
		   {
			$tot_production_qty=$production_qty_outbound;
			$rft=$rft_production_qty_outbound;
			$adding=$adding_production_qty_outbound;
			$reprocess=$reprocess_production_qty_outbound;
		   }
		   else  if($type_id==3) //With Order
		   {
			$tot_production_qty=$prod_qty_sample_with_order;
			$rft=$rft_prod_qty_sample_with_order;
			$adding=$adding_prod_qty_sample_with_order;
			$reprocess=$reprocess_prod_qty_sample_with_order;
		   }
		   else  if($type_id==4) //Without Order
		   {
			$tot_production_qty=$prod_qty_sample_without_order;
			$rft=$rft_prod_qty_sample_without_order;
			$adding=$adding_prod_qty_sample_without_order;
			$reprocess=$reprocess_prod_qty_sample_without_order;
		   }
		   else  if($type_id==5) //SubCon Order
		   {
			$tot_production_qty=$production_qty_subcontact;
			$rft=$rft_production_qty_subcontact;
			$adding=$adding_production_qty_subcontact;
			$reprocess=$reprocess_production_qty_subcontact;
		   }
		   else  if($type_id==6) //Sales Order
		   {
			$tot_production_qty=$fabric_sales_order_qty;
			$rft=$rft_fabric_sales_order_qty;
			$adding=$adding_fabric_sales_order_qty;
			$reprocess=$reprocess_fabric_sales_order_qty;
		   }
		   $total_prod_per=number_format($tot_production_qty/$total_prod_sammaryQty,6,'.','');*/





	?>
 
 
  
  	<!-- <table width="920" cellpadding="0" cellspacing="0" rules="all" border="1">
        <thead>
            <tr>
                <th colspan="8"> Dyeing Completed</th>
            </tr>
            <tr>
                <th width="35">SL</th>
                <th>Floor</th>
                <th>Dyeing In-House</th>
                <th>Re-Dyeing Qty</th>
                <th>Dyeing Out-Bound Subcontract</th>
                <th>Dyeing Sample Production</th>
                <th>In-Bound Subcontract</th>
                <th>Total Prod. Kg</th>
            </tr>
        </thead>
        <tbody>
               <?
                $i=1;
                foreach($dataArr as $floor_id=>$rows)
                {
                   $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
                   ?>
                   <tr bgcolor="<? echo $bgcolor; ?>">
                    <td align="center"><? echo $i; ?></td>
                    <td align="right"><?= $floor_lib[$floor_id]; ?></td>
                    <td align="right"><?=$rows[dye_qty_inhouse]; ?></td>
                    <td align="right"><?=$rows[re_pro_dye_qty_inhouse]; ?></td>
                    <td align="right"><?=$rows[dye_qty_outbound]; ?></td>
                    <td align="right"><?=$rows[dye_qty_sample_with_order]; ?></td>
                    <td align="right"></td>
                    <td align="right"></td>
                   </tr>
                   <?	
                $i++;
                }
                ?>
                <tr bgcolor="#CCCCCC">
                    <td colspan="2" align="right">Total</td>
                    <td align="right"></td>
                    <td align="right"></td>
                    <td align="right"></td>
                    <td align="right"></td>
                    <td align="right"></td>
                    <td align="right"></td>
                </tr>
    	</tbody>
    </table> -->
   
 
 
 <!-- <table width="920" cellpadding="0" cellspacing="0" rules="all" border="1">
    <tr>
        <th colspan="9">Finish Fabric Production</th>
    </tr>
    <tr bgcolor="#EEE">
        <th width="35">SL</th>
        <th>Floor</th>
        <th>Working Company</th>
        <th>Finishing In-House</th>
        <th>Finishing Out-Bound Subcontract</th>
        <th>Finishing Sample Production</th>
        <th>In-Bound Subcontract</th>
        <th>Total Prod. Kg</th>
        <th>Delivery To Store</th>
    </tr>
    <?
     



        if($is_insert_date_active==0){
            $str_cond_b=" and a.RECEIVE_DATE between '".$previous_date."' and '".$current_date."'";					
        }
        $sql_finish="select a.knitting_company,a.knitting_source,sum(b.receive_qnty) as receive_qnty from inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.company_id=$company_id and a.entry_form=7 and a.knitting_source in(1,3) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $str_cond_b group by a.knitting_company,a.knitting_source";			


        $nameArray_finish=sql_select($sql_finish);
        $i=1;
        foreach($nameArray_finish as $row)
        {
		?>
		<tr>
			<td align="center"><? echo $i; ?></td>
			<td><? echo $company_lib[$row[csf('knitting_company')]]; ?></td>
			<td><? echo $knitting_source[$row[csf('knitting_source')]]; ?></td>
			<td align="right"></td>
			<td align="right"></td>
			<td align="right"></td>
			<td align="right"></td>
			<td align="right"></td>
			<td align="right"></td>
		</tr>
		<?	
			$i++;
		}
		?> 
   
 </table>  -->
 
 <?
 //cutting qc...............
 
	if($is_insert_date_active==0){
		$str_cond_a=" and a.ENTRY_DATE between '".$previous_date."' and '".$current_date."'";
	}
 $cutSql="select a.FLOOR_ID,a.SERVING_COMPANY,b.QC_PASS_QTY from PRO_GMTS_CUTTING_QC_MST a,PRO_GMTS_CUTTING_QC_DTLS b where a.id=b.mst_id and a.COMPANY_ID=$company_id $str_cond_a";
	$cutSqlRes=sql_select($cutSql);
	 //echo $yarnIssueSql;die;
	 $dataArr=array();
	foreach($cutSqlRes as $row)
	{
		$key=$row[SERVING_COMPANY].'**'.$row[FLOOR_ID];
		$dataArr[$key][QC_PASS_QTY]+=$row[QC_PASS_QTY];
	}
	
 //----------------------
 
	$production_date_con = " and a.production_date between '".$previous_date."' and '".$current_date."'";
	$proSql = "select A.PRODUCTION_TYPE,A.SERVING_COMPANY,A.FLOOR_ID,A.PRODUCTION_QUANTITY from pro_garments_production_mst a
	 where a.COMPANY_ID =$company_id and a.production_type in(2,3,4,5,8) and a.is_deleted=0 and a.status_active=1 $production_date_con"; //$str_cond_a
	// echo $proSql;

	$proSqlRes = sql_select($proSql);
	foreach($proSqlRes as $sew_array)
	{	
		$key=$row[SERVING_COMPANY].'**'.$row[FLOOR_ID];
		
		if($row[PRODUCTION_TYPE]==4){$dataArr[$key][SEWING_IN]+=$row[PRODUCTION_QUANTITY];}
		elseif($row[PRODUCTION_TYPE]==5){$dataArr[$key][SEWING_OUT]+=$row[PRODUCTION_QUANTITY];}
		elseif($row[PRODUCTION_TYPE]==8){$dataArr[$key][FINISHING]+=$row[PRODUCTION_QUANTITY];}
		elseif($row[PRODUCTION_TYPE]==2){$dataArr[$key][EMB_ISS]+=$row[PRODUCTION_QUANTITY];}
		elseif($row[PRODUCTION_TYPE]==3){$dataArr[$key][EMB_REC]+=$row[PRODUCTION_QUANTITY];}
	}
	//--------------------------
	$cul_lay_date_con = " and b.ENTRY_DATE between '".$previous_date."' and '".$current_date."'";
	
	$cutLaySql="SELECT b.COMPANY_ID,a.ORDER_QTY,c.FLOOR_ID  from ppl_cut_lay_dtls a, ppl_cut_lay_mst b , lib_cutting_table  c where b.id=a.mst_id and b.table_no=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0    and b.entry_form=76 $cul_lay_date_con";
	$cutLaySqlRes = sql_select($cutLaySql);
	foreach($cutLaySqlRes as $sew_array)
	{	
		$key=$row[COMPANY_ID].'**'.$row[FLOOR_ID];
		$dataArr[$key][CUT_LAY]+=$row[ORDER_QTY];
	}


	$cut_lay_data=sql_select("select b.id as   tbl_id,b.table_no,b.location_id,b.floor_id,a.id,a.job_no,a.company_id,a.working_company_id,a.entry_date,end_date,a.marker_length,a.marker_width,a.fabric_width,a.gsm,a.width_dia,a.cad_marker_cons,a.cutting_no,a.batch_id,c.marker_qty	 from ppl_cut_lay_mst a, lib_cutting_table b,ppl_cut_lay_dtls c
	where a.table_no=b.id and c.mst_id=a.id  and entry_date between '".$previous_date."' and '".$current_date."' order by b.floor_id asc");

	foreach($cut_lay_data as $row){

	   $sewing_input_output_data[$row[csf('working_company_id')]][$row[csf('floor_id')]]['cut_lay_qty']+=$row[csf('marker_qty')];
   }



   $cutt_qc_data=sql_select(" SELECT a.id,a.mst_id,a.order_id,a.item_id,a.country_id,a.color_id,a.size_id,a.color_size_id,a.bundle_no,a.number_start,a.number_end,a.bundle_qty,a.reject_qty,a.replace_qty,a.qc_pass_qty,b.floor_id,b.serving_company from pro_gmts_cutting_qc_dtls a,pro_gmts_cutting_qc_mst b where a.mst_id=b.id  and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and b.cutting_qc_date between '".$previous_date."' and '".$current_date."' order by b.floor_id asc");

	

   foreach($cutt_qc_data as $row){

	   $sewing_input_output_data[$row[csf('serving_company')]][$row[csf('floor_id')]]['qc_cut_qty']+=$row[csf('qc_pass_qty')];
	   $floor_arr[$row[csf('floor_id')]]=$row[csf('floor_id')];

   }





	 $emb_iss_data = sql_select("select a.id, a.sys_number_prefix_num, a.delivery_date,a.sys_number, a.embel_type, a.production_source, a.serving_company, a.location_id, e.floor_id, a.organic,b.cut_no,c.production_qnty,a.embel_name 	from pro_gmts_delivery_mst a,pro_garments_production_mst b ,pro_garments_production_dtls c ,pro_gmts_cutting_qc_dtls d ,pro_gmts_cutting_qc_mst e 
	 where a.id=b.delivery_mst_id and b.id=c.mst_id and a.production_type=2 and a.embel_name in (1,2) and a.status_active=1 and c.BUNDLE_NO=d.BUNDLE_NO and  d.mst_id=e.id and a.is_deleted=0 and a.company_id=$company_id ".where_con_using_array($floor_arr,1,'e.floor_id')."  and a.delivery_date between '".$previous_date."' and '".$current_date."' order by a.floor_id asc"); 


	


		foreach($emb_iss_data as $row){

			if($row[csf('embel_name')]==2){
				$sewing_input_output_data[$row[csf('serving_company')]][$row[csf('floor_id')]]['emb_iss']+=$row[csf('production_qnty')];
			}else{
				if($row[csf('serving_company')]>1){
				$sewing_input_output_data[1][$row[csf('floor_id')]]['print_iss']+=$row[csf('production_qnty')];
				}else{
					$sewing_input_output_data[$row[csf('serving_company')]][$row[csf('floor_id')]]['print_iss']+=$row[csf('production_qnty')];
				}

			}

			
		}
		
	//   echo "<pre>";
	//  print_r($sewings_input_output_data);

		$emb_recv_data =sql_select("select a.id, a.sys_number_prefix_num, a.delivery_date,a.sys_number, a.embel_type, a.production_source, a.serving_company, a.location_id, e.floor_id, a.organic,b.cut_no,c.production_qnty,a.embel_name 	from pro_gmts_delivery_mst a,pro_garments_production_mst b ,pro_garments_production_dtls c ,pro_gmts_cutting_qc_dtls d ,pro_gmts_cutting_qc_mst e 
		where a.id=b.delivery_mst_id and b.id=c.mst_id and a.production_type=3 and a.embel_name in (1,2) and a.status_active=1 and c.BUNDLE_NO=d.BUNDLE_NO and  d.mst_id=e.id and a.is_deleted=0 and a.company_id=$company_id ".where_con_using_array($floor_arr,1,'e.floor_id')."  and a.delivery_date between '".$previous_date."' and '".$current_date."' order by a.floor_id asc"); 
	

		
		foreach($emb_recv_data as $row){
			
			if($row[csf('embel_name')]==2){
				$sewing_input_output_data[$row[csf('serving_company')]][$row[csf('floor_id')]]['emb_recv']+=$row[csf('production_qnty')];
			}else{
				
				if($row[csf('serving_company')]>1){
				$sewing_input_output_data[1][$row[csf('floor_id')]]['print_recv']+=$row[csf('production_qnty')];
				}else{
					$sewing_input_output_data[$row[csf('serving_company')]][$row[csf('floor_id')]]['print_recv']+=$row[csf('production_qnty')];

				}
				
			}
			
		}

		


		$sewing_input_data=sql_select("SELECT c.id as prdid,d.id as colorsizeid, e.id as po_id, f.job_no_prefix_num, f.buyer_name, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.cut_no,c.bundle_no,c.barcode_no,c.production_qnty,c.is_rescan, e.po_number,h.serving_company,h.floor_id,c.production_type from pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e, wo_po_details_master f,pro_gmts_delivery_mst h,pro_garments_production_mst i  where h.id=i.delivery_mst_id and  f.company_name=$company_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and e.job_id=f.id and f.id=d.job_id  and c.production_type in (4,5) and c.delivery_mst_id=h.id and c.status_active=1 and c.is_deleted=0   and h.delivery_date between '".$previous_date."' and '".$current_date."' order by h.floor_id asc"); 


	


		foreach($sewing_input_data as $row){
			
			if($row[csf('production_type')]==5){
				$sewing_input_output_data[$row[csf('serving_company')]][$row[csf('floor_id')]]['sewing_output']+=$row[csf('production_qnty')];
			}else{
				$sewing_input_output_data[$row[csf('serving_company')]][$row[csf('floor_id')]]['sewing_input']+=$row[csf('production_qnty')];
			}
		}


		// $sewing_output_data=sql_select("SELECT max(c.id)  as prdid, d.id as colorsizeid, e.id as po_id, f.job_no_prefix_num, f.buyer_name, d.item_number_id,c.barcode_no, d.country_id,c.reject_qty,c.alter_qty,c.spot_qty,c.replace_qty, d.size_number_id, d.color_number_id, c.cut_no,c.bundle_no,c.is_rescan, c.production_qnty as qty, e.po_number,g.floor_id,g.serving_company from pro_garments_production_mst a,pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e, wo_po_details_master f,pro_gmts_delivery_mst g where  g.id=a.delivery_mst_id and a.id=c.mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and e.job_no_mst=f.job_no  and a.company_id=$company_id and c.production_type=5 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 and e.status_active in(1,2,3) and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0  and g.delivery_date between '".$previous_date."' and '".$current_date."' group by d.id, e.id, f.job_no_prefix_num,f.insert_date, f.buyer_name, d.item_number_id, d.country_id,c.reject_qty,c.alter_qty,c.spot_qty,c.replace_qty, d.size_number_id, d.color_number_id, c.cut_no,c.bundle_no,c.barcode_no,c.is_rescan, c.production_qnty, e.po_number,g.floor_id,,g.serving_company order by c.cut_no, length(c.bundle_no) asc, c.bundle_no asc");  

		// foreach($sewing_output_data as $row){
		// 	$sewing_sinput_output_data[$row[csf('serving_company')]][$row[csf('floor_id')]]['sewing_output']+=$row[csf('qty')];
		// }


	


		$packing_data =sql_select("SELECT id, company_id, garments_nature, po_break_down_id, serving_company, sewing_line, production_date, production_quantity, production_source, production_type, floor_id  from pro_garments_production_mst where  production_type='8' and status_active=1 and is_deleted=0 and production_date between '".$previous_date."' and '".$current_date."' order by order by floor_id asc ");

	
	foreach($packing_data as $row){

		$sewing_input_output_data[$row[csf('serving_company')]][$row[csf('floor_id')]]['packing_qty']+=$row[csf('production_quantity')];
	}

    
	// $sql_marker="select sum(marker_qty) as mark_qty from ppl_cut_lay_dtls where order_ids='".$val[csf("order_ids")]."' and gmt_item_id=".$val[csf("gmt_item_id")]." and color_id=".$val[csf("color_id")]." and status_active=1";
	// $result=sql_select($sql_marker);
	// foreach($result as $rows)
	// {
	// 	$total_marker_qty=$rows[csf("mark_qty")];
	// }

	// 	echo "<pre>";
	//  print_r($sql_datas);
 ?>	

 

   
  <table width="920" cellpadding="0" cellspacing="0" rules="all" border="1">
                 	<tr>
                 		<th colspan="10">Sewing Input and Output</th>
                 	</tr>
                    <tr bgcolor="#EEE">
                    	<th align="center">SL</th>
                        <th>Working Company</th>
                        <th>Floor</th>
                        <!-- <th>Finish Fabric Issue Qty KG</th> -->
                        <th>Lay Qty Pcs</th>
                        <th>Cutting QC Pcs</th>
						<th>Print Send Pcs</th>
                        <th>Print Receive Pcs</th>
                        <th>Emb Send Pcs</th>
                        <th>Emb Receive Pcs</th>
                        <th>Input Qty Pcs</th>
                        <th>Output Qty Pcs</th>
                        <th>Packing Qty Pcs</th>
                     </tr>
                    <?
							
						$i=1;
						foreach($sewing_input_output_data as $com_id=>$floor_data)
						{
						 foreach($floor_data as $key=>$rows)
						 {
							list($wo_company_id,$floor_id)=explode('**',$key);
							
						?>
                        <tr>
                            <td align="center"><?=$i; ?></td>
                            <td align="center"><?=$company_lib[$com_id]; ?></td>
                            <td align="center"><?=$floor_lib[$key]; ?></td>                           
                            <td align="right"><?= fn_number_format($rows['cut_lay_qty'], 2,'.',',');?></td>
                            <td align="right"><?= fn_number_format($rows['qc_cut_qty'], 2,'.',',');?></td>
							<td align="right"><?= fn_number_format($rows['print_iss'], 2,'.',',');?></td>
                            <td align="right"><?= fn_number_format($rows['print_recv'], 2,'.',',');?></td>
                            <td align="right"><?= fn_number_format($rows['emb_iss'], 2,'.',',');?></td>
                            <td align="right"><?= fn_number_format($rows['emb_recv'], 2,'.',',');?></td>
                            <td align="right"><?= fn_number_format($rows['sewing_input'], 2,'.',',');?></td>
                            <td align="right"><?= fn_number_format($rows['sewing_output'], 2,'.',',');?></td>
                            <td align="right"><?= fn_number_format($rows['packing_qty'], 2,'.',',');?></td>
                        </tr>
                    	<?
						$i++;	
						$cut_lay_qty+=$rows['cut_lay_qty'];
						$qc_cut_qty+=$rows['qc_cut_qty'];
						$print_iss+=$rows['print_iss'];
						$print_recv+=$rows['print_recv'];
						$emb_iss+=$rows['emb_iss'];
						$emb_recv+=$rows['emb_recv'];
						$sewing_input+=$rows['sewing_input'];
						$sewing_output+=$rows['sewing_output'];
						$packing_qty+=$rows['packing_qty'];
						}}
					 ?>  
					   <tr>
                            <td align="center"> </td>
                            <td></td>
                            <td><b>Total</b></td>                           
                            <td align="right"><b><?= fn_number_format($cut_lay_qty, 2,'.',',');?></b></td>
                            <td align="right"><b><?= fn_number_format($qc_cut_qty, 2,'.',',');?></b></td>
							<td align="right"><b><?= fn_number_format($print_iss, 2,'.',',');?></b></td>
                            <td align="right"><b><?= fn_number_format($print_recv, 2,'.',',');?></b></td>
                            <td align="right"><b><?= fn_number_format($emb_iss, 2,'.',',');?></b></td>
                            <td align="right"><b><?= fn_number_format($emb_recv, 2,'.',',');?></b></td>
                            <td align="right"><b><?= fn_number_format($sewing_input, 2,'.',',');?></b></td>
                            <td align="right"><b><?= fn_number_format($sewing_output, 2,'.',',');?></b></td>
                            <td align="right"><b><?= fn_number_format($packing_qty, 2,'.',',');?></b></td>
                        </tr>
				
                 </table>
                 
                 
  <table width="920" cellpadding="0" cellspacing="0" border="1" rules="all">
        <tr>
            <th colspan="7">Shipment Status</th>
        </tr>
        <tr bgcolor="#EEE">
            <th width="35" align="center">SL</th>
            <th>Working Company</th>
            <th>Floor</th>
            <th>Buyer Name</th>
            <th align="right">Shipment Qty Pcs</th>
            <th align="right">FOB Value USD</th>
            <th align="right">CM Value USD</th>
        </tr>
        <?
			$costing_per_arr = return_library_array("select job_no, costing_per from wo_pre_cost_mst","job_no","costing_per"); 
			$tot_cost_arr = return_library_array("select job_no, cm_for_sipment_sche from wo_pre_cost_dtls","job_no","cm_for_sipment_sche"); 
	
			$fullExfactoryQty="select a.DELIVERY_COMPANY_ID,a.DELIVERY_FLOOR_ID,a.BUYER_ID,d.BUYER_NAME,b.EX_FACTORY_QNTY,(c.PO_QUANTITY*d.TOTAL_SET_QNTY) as PO_QTY_PCS,c.PO_TOTAL_PRICE ,d.job_quantity,d.job_no,d.TOTAL_SET_QNTY from pro_ex_factory_delivery_mst a, pro_ex_factory_mst b ,WO_PO_BREAK_DOWN c,WO_PO_DETAILS_MASTER d  where a.id=b.delivery_mst_id and b.PO_BREAK_DOWN_ID=c.id and c.job_id=d.id and a.company_id=1 and b.ex_factory_date between '".$previous_date."' and '".$current_date."' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 order by a.BUYER_ID asc";

		

		
		   // echo $fullExfactoryQty;//die;




		 
		$fullExfArray=sql_select($fullExfactoryQty);
		$dataArr=array();
		foreach($fullExfArray as $rows){

			$costing_per=$costing_per_arr[$rows[csf('job_no')]];
			if($costing_per==1) $dzn_qnty=12;
			else if($costing_per==3) $dzn_qnty=12*2;
			else if($costing_per==4) $dzn_qnty=12*3;
			else if($costing_per==5) $dzn_qnty=12*4;
			else $dzn_qnty=1;
			$dzn_qnty=$dzn_qnty*$rows[csf('total_set_qnty')];
			$cm_per_pcs=$tot_cost_arr[$rows[csf('job_no')]]/$dzn_qnty;
			$key=$rows[DELIVERY_COMPANY_ID].'**'.$rows[DELIVERY_FLOOR_ID].'**'.$rows[BUYER_NAME];
			$dataArr[$key][EX_FACTORY_QNTY]+=$rows[EX_FACTORY_QNTY];	
			$dataArr[$key][FOB_VAL_USD]+=($rows[PO_TOTAL_PRICE]/$rows[PO_QTY_PCS])*$rows[EX_FACTORY_QNTY];			
			$dataArr[$key][CM_COST]+=$cm_per_pcs*$rows[EX_FACTORY_QNTY];
			

			
		}



	$i=1;		
    foreach($dataArr as $key=>$row){
		list($work_com_id,$floor_id,$buyer_id)=explode('**',$key);
    ?>
    <tr>
        <td align="center"><?=$i; ?></td>
        <td align="center"><?=$company_lib[$work_com_id]; ?></td>
        <td align="center"><?=$floor_lib[$floor_id]; ?></td>
        <td align="center"><?=$buyer_library[$buyer_id]; ?></td>
        <td align="right"><?=fn_number_format($row[EX_FACTORY_QNTY],2,'.',','); ?></td>
        <td align="right"><?=fn_number_format($row[FOB_VAL_USD],2,'.',','); ?></td>
        <td align="right"><?=fn_number_format($row[CM_COST],2,'.',','); ?></td>
    </tr>
    <?
        $i++;	
		$tot_ex_factory+=$row[EX_FACTORY_QNTY];
		$tot_fob+=$row[FOB_VAL_USD];
		$tot_cm_cost+=$row[CM_COST];
    }
    ?>
	 <tr>
        <td align="center"></td>
        <td></td>
        <td></td>
        <td align="right"><b>Total:</b></td>
        <td align="right"><b><?=fn_number_format($tot_ex_factory,2,'.',','); ?></b></td>
        <td align="right"><b><?=fn_number_format($tot_fob,2,'.',','); ?></b></td>
        <td align="right"><b><?=fn_number_format($tot_cm_cost,2,'.',','); ?></b></td>
    </tr>
        
</table>
  



<table width="920" cellpadding="0" cellspacing="0" border="1" rules="all">
    <thead>
        <tr>
            <td colspan="9" align="center">Yarn Received</td>
        </tr>
        
        <tr bgcolor="#EEE">
            <th width="35">SL</th>
			<th>Buyer Name</th>
            <th>Supplier Name</th>
            <th>Yarn Count</th>
            <th>Composition</th>
            <th>Yarn type</th>
            <th>Lot</th>
            <th>Received Qty Kg</th>
            <th>Total Value USD</th>
            <th>Rate</th>
        </tr>
    </thead>
    <?
        
     
		$str_cond_a=" and c.receive_date between '".$previous_date."' and '".$current_date."'";
        $yarnRecSql="select 
		a.SUPPLIER_ID,b.LOT,b.YARN_TYPE,b.YARN_COUNT_ID, b.PRODUCT_NAME_DETAILS,
		
        sum(case when a.transaction_type=1 then a.cons_quantity else 0 end) as cons_quantity,
        sum(case when a.transaction_type=5 then a.cons_quantity else 0 end) as transfer_qty,
        sum(b.avg_rate_per_unit) as AVG_RATE, 
        sum(a.cons_amount/c.exchange_rate) as CONS_AMOUNT ,
        sum(a.cons_amount) as CONS_AMOUNT_TK ,a.BUYER_ID
		
        from inv_transaction a, product_details_master b,inv_receive_master c
        where c.id=a.mst_id and b.id=a.prod_id and c.entry_form=1 and c.receive_purpose!=2 and a.item_category=1 and a.transaction_type in(1,5) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $str_cond_a	
        group by a.SUPPLIER_ID,b.LOT,b.YARN_TYPE,b.YARN_COUNT_ID, b.PRODUCT_NAME_DETAILS,a.BUYER_ID";				
     //echo $yarnRecSql;//die;

	
        $yarnRecSqlRes=sql_select($yarnRecSql);
        $i=1;
		$grandTotal=array();
		foreach($yarnRecSqlRes as $row)
        {
            $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
			$grandTotal[RECV_QTY]+=$row[csf('cons_quantity')];
			$grandTotal[CONS_AMOUNT]+=$row[CONS_AMOUNT];
			$grandTotal[CONS_AMOUNT_TK]+=$row[CONS_AMOUNT_TK];
			
		?>
			<tr bgcolor="<? echo $bgcolor; ?>">
				<td align="center"><? echo $i; ?></td>
				<td align="center"><?=$buyer_library[$row[BUYER_ID]]; ?></td>
				<td align="center"><?=$supplier_lib[$row[SUPPLIER_ID]]; ?></td>
				<td><?=$yarn_count_lib[$row[YARN_COUNT_ID]]; ?></td>
				<td align="left"><? echo $row[PRODUCT_NAME_DETAILS]; ?></td>
				<td><?=$yarn_type[$row[YARN_TYPE]]; ?></td>
				<td align="right"><?=$row[LOT];?></td>
				<td align="right"><?=fn_number_format($row[csf('cons_quantity')],2,'.',',');?></td>
				<td align="right"><?=fn_number_format($row[CONS_AMOUNT],2,'.',',');?></td>
				<td align="right"><?=fn_number_format($row[CONS_AMOUNT]/$row[csf('cons_quantity')],2,'.',',');?></td>
			</tr>
		<?	
        $i++;
       }
    ?> 
    <tr>
        <tfoot bgcolor="#EEE">
            <th align="right" colspan="7">Total</th>
            <th align="right"><?=fn_number_format($grandTotal[RECV_QTY],2,'.',',');;?></th>
            <th align="right"><?=fn_number_format($grandTotal[CONS_AMOUNT],2,'.',',');;?></th>
            <th align="right"></th>
        </tfoot>
    </tr>
 </table>
    
 
	<?
      
		
        $i=0; $tot_quantity=0; $tot_value=0;
        
        $yarnIssueSql="select a.SUPPLIER_ID,b.LOT,b.YARN_TYPE,b.YARN_COUNT_ID, b.PRODUCT_NAME_DETAILS,c.KNIT_DYE_SOURCE,c.BOOKING_NO, sum(a.cons_quantity) as CONS_QUANTITY, 
		sum(b.avg_rate_per_unit) as AVG_RATE_PER_UNIT, sum(a.cons_amount) as CONS_AMOUNT, sum(a.RETURN_QNTY) as RETURN_QTY ,a.requisition_no,  sum(a.cons_amount/d.exchange_rate) as CONS_AMOUNT_USD
		 from inv_transaction a left join inv_receive_master d on d.id=a.mst_id, product_details_master b, inv_issue_master c where b.id=a.prod_id and c.id=a.mst_id and a.item_category=1  and a.transaction_type in(2,3,6) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1and c.issue_date between '".$previous_date."' and '".$current_date."'  group by a.SUPPLIER_ID,b.LOT,b.YARN_TYPE,b.YARN_COUNT_ID, b.PRODUCT_NAME_DETAILS,c.KNIT_DYE_SOURCE,c.BOOKING_NO,a.requisition_no";				
        $yarnIssueSqlRes=sql_select($yarnIssueSql);
		// echo $yarnIssueSql;//die;
		foreach($yarnIssueSqlRes as $row)
        {
			$yarnDataArr[$row[KNIT_DYE_SOURCE]][]=$row;
			$req_no_arr[$row[csf('requisition_no')]]=$row[csf('requisition_no')];

		}
        $ref_data = sql_select("select a.job_no, a.style_ref_no,file_no,grouping, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst ");
		foreach($ref_data as $row)
        {
			$po_wise_ref_arr[$row[csf('id')]]['grouping']=$row[csf('grouping')];
		}


		$req_data=sql_select("select a.booking_no,c.requisition_no ,d.po_id,a.buyer_id	from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b,ppl_yarn_requisition_entry c,ppl_planning_entry_plan_dtls d  where a.id=b.mst_id and d.DTLS_ID=b.ID and  c.knit_id=b.id and a.status_active=1 and b.status_active=1 and c.status_active=1  ".where_con_using_array($req_no_arr,1,'c.requisition_no')."	group by a.booking_no,c.requisition_no ,d.po_id,a.buyer_id");
		
		foreach($req_data as $row)
        {
			$req_wise_booking[$row[csf('requisition_no')]]['requisition_no']=$row[csf('booking_no')];
			$req_wise_booking[$row[csf('requisition_no')]]['buyer_id']=$buyer_library[$row[csf('buyer_id')]];
			$req_wise_booking[$row[csf('requisition_no')]]['int_ref']=$po_wise_ref_arr[$row[csf('po_id')]]['grouping'];
			// $po_id_arr[$row[csf('po_id')]]=$row[csf('po_id')];
		}
	?> 
 
 <table width="920" cellpadding="0" cellspacing="0" rules="all" border="1">
    <thead>
        <tr>
            <th colspan="11">Yarn Issued</th>
        </tr>
        <tr bgcolor="#EEE">
            <th width="35">SL</th>
			<th>Buyer Name</th>
			<th>Internal Ref</th>
            <th>Booking No</th>			
            <th>Supplier</th>
            <th>Yarn Count</th>
            <th>Composition</th>
            <th>Yarn type</th>
            <th>Lot</th>
            <th>Issue Qty KG</th>
            <th>Total Value USD</th>
        </tr>
        <tr>
            <th colspan="9">In-house</th>
        </tr>
   </thead>
    <?
		$i=1;
		$grandTotal=array();
		foreach($yarnDataArr[1] as $row)
        {
            
           $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
		   $grandTotal[CONS_QUANTITY]+=$row[CONS_QUANTITY];
		   $grandTotal[CONS_AMOUNT_USD]+=$row[CONS_AMOUNT_USD];

		   $usd_id=2;
		   if($db_type==0) $conversion_date=change_date_format($row[csf("wo_date")], "Y-m-d", "-",1);
		   else $conversion_date=change_date_format($row[csf("wo_date")], "d-M-y", "-",1);
		   $currency_rate=set_conversion_rate($usd_id,$conversion_date );
		   if($row[csf("currency")]==2) $wo_currency_usd_amt=$row[csf("wo_value")];//USD
		   else $wo_currency_usd_amt=$row[csf("wo_value")]/$currency_rate;


		?>
		<tr bgcolor="<? echo $bgcolor; ?>">
			<td align="center"><?=$i; ?></td>
			<td align="center"><?=$req_wise_booking[$row[csf('requisition_no')]]['buyer_id'];?></td>
			<td align="center"><?=$req_wise_booking[$row[csf('requisition_no')]]['int_ref'];?></td>
			<td align="center"><?=$req_wise_booking[$row[csf('requisition_no')]]['requisition_no']; ?></td>
			<td align="center"><?=$supplier_lib[$row[SUPPLIER_ID]]; ?></td>
			<td align="right"><?=$yarn_count_lib[$row[YARN_COUNT_ID]];?></td>                        
			<td align="left"><?=$row[PRODUCT_NAME_DETAILS];?></td>
			<td align="right"><?=$yarn_type[$row[YARN_TYPE]];?></td>
			<td align="right"><?=$row[LOT];?></td>
            <td align="right"><?=number_format($row[CONS_QUANTITY],2,'.',',');;?></td>
            <td align="right"><?=number_format($row[CONS_AMOUNT_USD],2,'.',','); ?></td>
		</tr>
		<?	
        $i++;
        }
    ?>
        <tr bgcolor="#EEE">
            <th colspan="9" align="right">Total</th>
            <th align="right"><?=number_format($grandTotal[CONS_QUANTITY],2,'.',',');;?></th>
            <th align="right"><?=number_format($grandTotal[CONS_AMOUNT_USD],2,'.',',');;?></th>
        </tr>
    
        <tr>
            <th colspan="11">Out Bound Sub-Contract</th>
        </tr>
    	
    <?
		$i=1;
		$grandTotal=array();
		foreach($yarnDataArr[3] as $row)
        {
            
           $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
		   $grandTotal[CONS_QUANTITY]+=$row[CONS_QUANTITY];
		   $grandTotal[CONS_AMOUNT]+=$row[CONS_QUANTITY];
		?>
		<tr bgcolor="<? echo $bgcolor; ?>">
			<td align="center"><?=$i; ?></td>
			<td align="center"><?=$req_wise_booking[$row[csf('requisition_no')]]['buyer_id'];?></td>
			<td align="center"><?=$req_wise_booking[$row[csf('requisition_no')]]['int_ref'];?></td>
			<td align="center"><?=$req_wise_booking[$row[csf('requisition_no')]]['requisition_no']; ?></td>
			<td align="center"><?=$supplier_lib[$row[SUPPLIER_ID]]; ?></td>
			<td align="right"><?=$yarn_count_lib[$row[YARN_COUNT_ID]];?></td>                        
			<td align="left"><?=$row[PRODUCT_NAME_DETAILS];?></td>
			<td align="right"><?=$yarn_type[$row[YARN_TYPE]];?></td>
			<td align="right"><?=$row[LOT];?></td>
            <td align="right"><?=number_format($row[CONS_QUANTITY], 2,'.',',');?></td>
            <td align="right"><?=number_format($row[CONS_AMOUNT], 2,'.',',');?></td>
		</tr>
		<?	
        $i++;
        }
    ?>
        <tr bgcolor="#EEE">
            <th colspan="9" align="right">Total</th>
            <th align="right"><?=number_format($grandTotal[CONS_QUANTITY], 2,'.',',');?></th>
            <th align="right"><?=number_format($grandTotal[CONS_AMOUNT], 2,'.',',');?></th>
        </tr>
    
 </table> 

   
    
<? 
    	$message=ob_get_contents();
    	ob_clean();
	//Mail Setup------------------------------------------------------------------------------------

		$mail_item=14;
		$sql2 = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=$mail_item and b.mail_user_setup_id=c.id and a.company_id=$company_id and a.IS_DELETED=0 and a.STATUS_ACTIVE=1 AND a.MAIL_TYPE=1 and b.IS_DELETED=0 and b.STATUS_ACTIVE=1 and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
		$mail_sql2=sql_select($sql2);
		foreach($mail_sql2 as $row)
		{
			$toArr[$row[csf('email_address')]]=$row[csf('email_address')];
		}
		$to=implode(',',$toArr);
		
		$subject="Total Production Activities of ( Date :".date("d-m-Y", strtotime($previous_date)).")";

		$header=mailHeader();


		if($_REQUEST['isview']==1){
			if($to){
				echo 'Mail Item:'.$form_list_for_mail[$mail_item].'=>'.$to;
			}else{
				echo "Mail address not set. [Please set mail from  Mail Recipient Group, Mail Item: <b>".$form_list_for_mail[$mail_item]."</b>]<br>";
			}
			echo $message;
		}
		else{
			if($to!=""){echo sendMailMailer( $to, $subject, $message, $from_mail );}
		}

		//------------------------------------------------------------------------------------Mail Setup;

		
}



?>