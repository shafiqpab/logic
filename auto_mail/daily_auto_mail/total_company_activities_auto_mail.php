<?php
//Developed for youth group
/*-------------------------------------------- Comments -----------------------
Purpose			         : 	Total Company Activities auto mail.
Functionality	         :	
JS Functions	         :
Created by		         :	REZA 
Creation date 	         : 	08-07-2021
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
require_once('../mailer/class.phpmailer.php');
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


$company_lib = return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0 and id=21", "id", "company_name");

$supplier_lib = return_library_array( "select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
$yarn_count_lib = return_library_array( "select id,yarn_count from lib_yarn_count where status_active=1 and is_deleted=0","id","yarn_count");


 $floor_lib = return_library_array( "select id, FLOOR_NAME from LIB_PROD_FLOOR where status_active=1 and is_deleted=0 ", "id", "FLOOR_NAME");

	if($db_type==0)
	{
		$current_date = date("Y-m-d H:i:s",time());
		$previous_date= date('Y-m-d H:i:s', strtotime('-1 day', time())); 
	}
	else
	{
		$current_date = change_date_format(date("Y-M-d H:i:s",time()),'','',1);
		$previous_date= change_date_format(date('Y-m-d H:i:s', strtotime('-1 day', time())),'','',1);
	}


	//$current_date=$previous_date;
	
	
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
	
	


foreach($company_lib as $company_id=>$compname)/// Total Activities
{
	ob_start();
	?>
    
    
    <table width="920">
        <tr>
            <td valign="top" align="center">
                <font size="+2">Total  Production Activities of ( Date :<?  echo date("d-m-Y", strtotime($previous_date));  ?>)[Insert Date Wise]</font>
            </td>
        </tr>
        <tr>
            <td valign="top" align="center">
                <? echo $company_library[$company_id];  ?>
            </td>
        </tr>
    </table>
 
 
 <table width="920" cellpadding="0" cellspacing="0" rules="all" border="1">
    <tr>
        <td colspan="13" align="center">Order Received</td>
    </tr>
    <tr>
        <td rowspan="2" width="120" align="center">Buyer</td>
        <td rowspan="2" width="120" align="center">Job</td>
        <td rowspan="2" width="120" align="center">Internal Ref.</td>
        <td rowspan="2" width="80" align="center">Avg. Lead Time</td>
        <td colspan="3" align="center">Confirm Order</td>
        <td colspan="3" align="center">Projected Order</td>
        <td colspan="3" align="center">Total</td>
    </tr>
    <tr bgcolor="#EEE">
        <td width="85" align="center">Qty(Pcs)</td>
        <td width="85" align="center">Value(USD)</td>
        <td width="80" align="center">Avg. Rate</td>
        <td width="85" align="center">Qty.(Pcs)</td>
        <td width="85" align="center">Value(USD)</td>
        <td width="80" align="center">Avg. Rate</td>
        <td width="85" align="center">Qty.(Pcs)</td>
        <td width="85" align="center">Value(USD)</td>
        <td width="85" align="center">Avg. Rate</td>
    </tr>
    <?
    
    if($is_insert_date_active==0){
        $str_cond_b=" and b.PUB_SHIPMENT_DATE between '".$previous_date."' and '".$current_date."'";
    }
    
    $orderSql="select A.BUYER_NAME,A.JOB_NO,B.GROUPING,B.PUB_SHIPMENT_DATE,B.PO_RECEIVED_DATE,B.ID,b.IS_CONFIRMED,(a.total_set_qnty*b.po_quantity) as PO_QTY, b.po_total_price as PO_VALUE from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name = '$company_id' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.shiping_status=1 $str_cond_b"; //and b.id=28856
    //echo $orderSql;die;
    
    $orderSqlRs=sql_select($orderSql);
    $orderDataArr=array(); 
    foreach($orderSqlRs as $rows)
    {
        $daysOnHand = datediff("d",$rows[csf('po_received_date')],$rows[csf('pub_shipment_date')]);
    
        $key=$rows[BUYER_NAME].'**'.$rows[JOB_NO].'**'.$rows[GROUPING];
        $orderDataArr[QTY][$key][$rows[IS_CONFIRMED]]+=$rows[PO_QTY];
        $orderDataArr[VAL][$key][$rows[IS_CONFIRMED]]+=$rows[PO_VALUE];
        $orderDataArr[LEAD_TIME][$key]+=$daysOnHand;
    
    }
    unset($orderSqlRs);
    
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
        <td><?=$buyer_library[$buyer_id]; ?></td>
        <td><?=$job_no;?></td>
        <td><?=$internal_file;?></td>
        <td><?=$orderDataArr[LEAD_TIME][$key];?></td>
    
        <td align="right"><?=$orderDataArr[QTY][$key][1];?></td>
        <td align="right"><?=$orderDataArr[VAL][$key][1];?></td>                        
        <td align="right"><?=fn_number_format($avgRage[1],2);?></td>
    
        <td align="right"><?=$orderDataArr[QTY][$key][2];?></td>
        <td align="right"><?=$orderDataArr[VAL][$key][3];?></td>                        
        <td align="right"><?=fn_number_format($avgRage[2],2);?></td>
            
        <td align="right"><?= array_sum($orderDataArr[QTY][$key]);?></td>
        <td align="right"><?= array_sum($orderDataArr[VAL][$key]);?></td>
        <td align="right"><?=fn_number_format(array_sum($orderDataArr[QTY][$key])/array_sum($orderDataArr[VAL][$key]),2);?></td>
    </tr>
    <?	
    $i++;
    }
    ?> 
    <tr>
        <tfoot bgcolor="#EEE">
            <th colspan="4">Total</th>
            <th align="right"><?=$grandTotal[conf_qty];?></th>
            <th align="right"><?=$grandTotal[conf_val];?></th>
            <th align="right"><?=fn_number_format($grandTotal[conf_val]/$grandTotal[conf_qty],2);?></th>
            <th align="right"><?=$grandTotal[proj_qty];?></th>
            <th align="right"><?=$grandTotal[proj_val];?></th>
            <th align="right"><?=fn_number_format($grandTotal[proj_val]/$grandTotal[proj_qty],2);?></th>
            <th align="right"><?=$grandTotal[tot_qty];?></th>
            <th align="right"><?=$grandTotal[tot_val];?></th>
            <th align="right"><?=fn_number_format($grandTotal[tot_val]/$grandTotal[tot_qty],2);?></th>
        </tfoot>
    </tr>
 </table>  
    
    
 <table width="920" cellpadding="0" cellspacing="0" border="1" rules="all">
    <thead>
        <tr>
            <td colspan="9" align="center">Yarn Received</td>
        </tr>
        
        <tr bgcolor="#EEE">
            <th width="35">SL</th>
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
        
        if($is_insert_date_active==0){
            $str_cond_a=" and a.TRANSACTION_DATE between '".$previous_date."' and '".$current_date."'";
        }
        
        $yarnRecSql="select 
		a.SUPPLIER_ID,b.LOT,b.YARN_TYPE,b.YARN_COUNT_ID, b.PRODUCT_NAME_DETAILS,
		
        sum(case when a.transaction_type=1 then a.cons_quantity else 0 end) as cons_quantity,
        sum(case when a.transaction_type=5 then a.cons_quantity else 0 end) as transfer_qty,
        sum(b.avg_rate_per_unit) as AVG_RATE, 
        sum(a.cons_amount/c.exchange_rate) as CONS_AMOUNT ,
        sum(a.cons_amount) as CONS_AMOUNT_TK 
		
        from inv_transaction a, product_details_master b,inv_receive_master c
        where c.id=a.mst_id and b.id=a.prod_id and c.entry_form=1 and a.company_id=$company_id and a.item_category=1 and a.transaction_type in(1,5) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $str_cond_a 
        group by a.SUPPLIER_ID,b.LOT,b.YARN_TYPE,b.YARN_COUNT_ID, b.PRODUCT_NAME_DETAILS";				
        // echo $yarnSql;die;
        $yarnRecSqlRes=sql_select($yarnRecSql);
        $i=1;
		$grandTotal=array();
		foreach($yarnRecSqlRes as $row)
        {
            $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
			
			$grandTotal[CONS_AMOUNT]+=$row[CONS_AMOUNT];
			$grandTotal[CONS_AMOUNT_TK]+=$row[CONS_AMOUNT_TK];
			
		?>
			<tr bgcolor="<? echo $bgcolor; ?>">
				<td align="center"><? echo $i; ?></td>
				<td><?=$supplier_lib[$row[SUPPLIER_ID]]; ?></td>
				<td><?=$yarn_count_lib[$row[YARN_COUNT_ID]]; ?></td>
				<td><? echo $row[PRODUCT_NAME_DETAILS]; ?></td>
				<td><?=$yarn_type[$row[YARN_TYPE]]; ?></td>
				<td align="right"><?=$row[LOT];?></td>
				<td align="right"><?=$row[CONS_AMOUNT];?></td>
				<td align="right"><?=$row[CONS_AMOUNT_TK];?></td>
				<td align="right"><?=$row[AVG_RATE];?></td>
			</tr>
		<?	
        $i++;
       }
    ?> 
    <tr>
        <tfoot bgcolor="#EEE">
            <th align="right" colspan="6">Total</th>
            <th align="right"><?=$grandTotal[CONS_AMOUNT];?></th>
            <th align="right"><?=$grandTotal[CONS_AMOUNT_TK];?></th>
            <th align="right"></th>
        </tfoot>
    </tr>
 </table>
    
 
<?
        if($is_insert_date_active==0){
            $str_cond_a=" and a.TRANSACTION_DATE between '".$previous_date."' and '".$current_date."'";
        }

        $i=0; $tot_quantity=0; $tot_value=0;
        
        $yarnIssueSql="select a.SUPPLIER_ID,b.LOT,b.YARN_TYPE,b.YARN_COUNT_ID, b.PRODUCT_NAME_DETAILS,c.KNIT_DYE_SOURCE,c.BOOKING_NO,
		sum(a.cons_quantity) as CONS_QUANTITY,
		sum(b.avg_rate_per_unit) as AVG_RATE_PER_UNIT,
		sum(a.cons_amount) as CONS_AMOUNT,
		sum(a.RETURN_QNTY) as RETURN_QTY 
		from inv_transaction a, product_details_master b, inv_issue_master c where b.id=a.prod_id and c.id=a.mst_id and a.company_id=$company_id and a.item_category=1 and a.transaction_type in(2,3,6) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $str_cond_a group by a.SUPPLIER_ID,b.LOT,b.YARN_TYPE,b.YARN_COUNT_ID, b.PRODUCT_NAME_DETAILS,c.KNIT_DYE_SOURCE,c.BOOKING_NO";				
        $yarnIssueSqlRes=sql_select($yarnIssueSql);
		 //echo $yarnIssueSql;die;
		foreach($yarnIssueSqlRes as $row)
        {
			$yarnDataArr[$row[KNIT_DYE_SOURCE]][]=$row;
		}
        
	?> 
 
 <table width="920" cellpadding="0" cellspacing="0" rules="all" border="1">
    <thead>
        <tr>
            <th colspan="9">Yarn Issued</th>
        </tr>
        <tr bgcolor="#EEE">
            <th width="35">SL</th>
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
		   $grandTotal[CONS_AMOUNT]+=$row[CONS_QUANTITY];
		?>
		<tr bgcolor="<? echo $bgcolor; ?>">
			<td align="center"><?=$i; ?></td>
			<td><?=$row[BOOKING_NO]; ?></td>
			<td><?=$supplier_lib[$row[SUPPLIER_ID]]; ?></td>
			<td align="right"><?=$yarn_count_lib[$row[YARN_COUNT_ID]];?></td>                        
			<td align="right"><?=$row[PRODUCT_NAME_DETAILS];?></td>
			<td align="right"><?=$yarn_type[$row[YARN_TYPE]];?></td>
			<td align="right"><?=$row[LOT];?></td>
            <td align="right"><?=$row[CONS_QUANTITY];?></td>
            <td align="right"><?=$row[CONS_AMOUNT];?></td>
		</tr>
		<?	
        $i++;
        }
    ?>
        <tr bgcolor="#EEE">
            <th colspan="7" align="right">Total</th>
            <th align="right"><?=$grandTotal[CONS_QUANTITY];?></th>
            <th align="right"><?=$grandTotal[CONS_AMOUNT];?></th>
        </tr>
    
        <tr>
            <th colspan="9">Out Bound Sub-Contract</th>
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
			<td><?=$row[BOOKING_NO]; ?></td>
			<td><?=$supplier_lib[$row[SUPPLIER_ID]]; ?></td>
			<td align="right"><?=$yarn_count_lib[$row[YARN_COUNT_ID]];?></td>                        
			<td align="right"><?=$row[PRODUCT_NAME_DETAILS];?></td>
			<td align="right"><?=$yarn_type[$row[YARN_TYPE]];?></td>
			<td align="right"><?=$row[LOT];?></td>
            <td align="right"><?=$row[CONS_QUANTITY];?></td>
            <td align="right"><?=$row[CONS_AMOUNT];?></td>
		</tr>
		<?	
        $i++;
        }
    ?>
        <tr bgcolor="#EEE">
            <th colspan="7" align="right">Total</th>
            <th align="right"><?=$grandTotal[CONS_QUANTITY];?></th>
            <th align="right"><?=$grandTotal[CONS_AMOUNT];?></th>
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

//--------------------------------------------
//SMN
	 $sql_qty="Select a.FLOOR,a.BOOKING_NO, 
	 sum(case when a.knitting_source=1 and b.machine_no_id>0 then c.quantity end ) as qtyinhouse, 
	 sum(case when a.knitting_source=1 and b.machine_no_id>0 then c.reject_qty end ) as reject_qty_inhouse, 
	 sum(case when a.knitting_source=3 then c.quantity end ) as qtyoutbound, 
	 sum(case when a.knitting_source=3 then c.reject_qty end ) as reject_qty_outbound 
	 from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c,wo_po_break_down e, wo_po_details_master f where a.item_category=13 and a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=e.id and e.job_no_mst=f.job_no and c.entry_form=2 and c.trans_type=1 and a.receive_basis!=4 and a.knitting_company=$company_id   $str_cond_f and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.FLOOR,a.BOOKING_NO ";
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
	 $sql_sample_sam_with="select a.FLOOR,a.BOOKING_NO,
	sum(case when a.booking_without_order=0 and b.machine_no_id>0  then b.grey_receive_qnty end ) as with_ord_sample_qty,
	sum(case when a.booking_without_order=0 and b.machine_no_id>0  then b.reject_fabric_receive end ) as reject_fabric_receive
	from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, lib_machine_name d, wo_po_break_down e,  wo_po_details_master f where c.po_breakdown_id=e.id and a.entry_form=2 and a.item_category=13 and a.id=b.mst_id and b.id=c.dtls_id and e.job_no_mst=f.job_no and c.entry_form=2 and c.trans_type=1 and a.knitting_source=1 and a.receive_basis!=4  and a.knitting_company=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_without_order in(0)  $str_cond_f group by a.FLOOR,a.BOOKING_NO";
	//echo $sql_sample_sam_with;die;
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
//-------------------------------------------------------
	$sql_service_samary=sql_select("select a.FLOOR, sum(b.grey_receive_qnty) as service_qty,sum(b.reject_fabric_receive) as reject_fabric_receive
										from inv_receive_master a, pro_grey_prod_entry_dtls b where a.entry_form=22 and a.receive_basis=11 and a.item_category=13 and a.id=b.mst_id  and a.knitting_company=$company_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $str_cond_f group by a.FLOOR");
									
	foreach($sql_service_samary as $row)
	{
		$dataArr[$row[FLOOR]][out_qty]=$row[csf("service_qty")];
	}
	unset($sql_service_samary);

//-------------------------------------------------------
		/*$sql_sales_prod="select a.FLOOR, 
											sum(case when b.machine_no_id>0 then b.grey_receive_qnty end ) as knit_sales_in , sum(case when b.machine_no_id>0 then b.reject_fabric_receive end) as reject_fabric_receive
											from inv_receive_master a, pro_grey_prod_entry_dtls b,fabric_sales_order_mst c where  c.id=a.booking_id and c.within_group=2 and a.entry_form=2 and a.item_category=13 and a.id=b.mst_id and a.receive_basis=4 and a.knitting_source=1 and a.knitting_company=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $str_cond_f group by a.FLOOR";
		$result_sales_prod=sql_select( $sql_sales_prod);

		foreach($result_sales_prod as $row)
		{
			$knit_sales_buyer_sammary[$row[FLOOR]]['knit_sales_in']+= $row[csf('knit_sales_in')];
		}
		unset($result_sales_prod);	*/	
//-------------------------------------------------------------		

/*	if($is_insert_date_active==0){
		$str_cond_g	=" and a.product_date between '".$previous_date."' and '".$current_date."'";					
	}
				
	$sql_inhouse_sub_summ="select a.party_id, sum(b.product_qnty) as qntysubshift, sum(b.reject_qnty) as reject_qnty  from subcon_production_mst a, subcon_production_dtls b
	where a.id=b.mst_id and a.product_type=2 
	and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$compid $str_cond_g
	group by a.party_id";
	
	$nameArray_inhouse_subcon_summ=sql_select( $sql_inhouse_sub_summ);
	$tot_qty_sub_summ=$tot_qty_sub_summ_rej=0;
	foreach($nameArray_inhouse_subcon_summ as $rows)
	{
		$tot_qty_sub_summ+=$rows[csf('qntysubshift')];
		$tot_qty_sub_summ_rej+=$rows[csf('reject_qnty')];
	}		
		
	foreach($knit_buyer_samary as $buyer_id=>$rows)
	{
		$tot_qtyoutbound+=$rows[('out_qty')]+$service_buyer_data[$buyer_id];
		$tot_without_ord_qty+=$rows[('with_out_qty')];
		$tot_with_ord_qty+=$rows[('with_qty')];
		$tot_qtyinhouse+=$rows[('in_qty')];
	}

	foreach($knit_sales_buyer_sammary as $buyer_id=>$rows)
	{
		$tot_qty_sales_summ+=$rows[('knit_sales_in')];
		$tot_qty_sales_summ_rej+=$rows[('knit_sales_in_rej')];
	}

 if($type_id==1) //Inhouse
						   {
							$tot_production_qty=$tot_qtyinhouse;
							$tot_production_qty_rej=$tot_qtyinhouse_rej;
						   }
						   else  if($type_id==2) //OutBound
						   {
							$tot_production_qty=$tot_qtyoutbound;
							$tot_production_qty_rej=$tot_qtyoutbound_rej;
						   }
						   else  if($type_id==3) //With Order
						   {
							$tot_production_qty=$tot_with_ord_qty;
							$tot_production_qty_rej=$tot_with_ord_qty_rej;
						   }
						   else  if($type_id==4) //Without Order
						   {
							$tot_production_qty=$tot_without_ord_qty;
							$tot_production_qty_rej=$tot_without_ord_qty_rej;
						   }
						   else  if($type_id==5) //SubCon Order
						   {
							$tot_production_qty=$tot_qty_sub_summ;
							$tot_production_qty_rej=$tot_qty_sub_summ_rej;
						   }
						   else  if($type_id==6) //Sales Order
						   {
							$tot_production_qty=$tot_qty_sales_summ;
							$tot_production_qty_rej=$tot_qty_sales_summ_rej;
						   }		
*/		
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
		foreach($dataArr as $floor_id=>$rows)
		{
			
			$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
			?>
			<tr bgcolor="<? echo $bgcolor; ?>">
				<td align="center"><? echo $i; ?></td>
				<td><?=$floor_id;?></td>
				<td><?=$rows[in_qty]; ?></td>
				<td><?=$rows[out_qty]; ?></td>
				<td align="right"><?=$rows[with_out_qty]; ?></td>
				<td align="right"><?=$rows[with_qty]; ?></td>                 
				<td align="right"></td>
				<td align="right"></td>
			</tr>
			<?	
				$i++;
			}
			?> 
			<tr>
				<tfoot bgcolor="#EEE">
					<th>&nbsp;</th>
					<th>&nbsp;</th>
					<th>&nbsp;</th>
					<th>Total</th>
					<th align="right"></th>
					<th align="right"></th>
					<th align="right"></th>
					<th align="right"></th>
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
 
 
  
  <table width="920" cellpadding="0" cellspacing="0" rules="all" border="1">
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
    </table>
   
 
 
 <table width="920" cellpadding="0" cellspacing="0" rules="all" border="1">
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
			<td><? echo $company_library[$row[csf('knitting_company')]]; ?></td>
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
    <tr>
        <tfoot bgcolor="#EEE">
            <th width="35">SL</th>
            <th>Floor</th>
            <th>Working Company</th>
            <th>Finishing In-House</th>
            <th>Finishing Out-Bound Subcontract</th>
            <th>Finishing Sample Production</th>
            <th>In-Bound Subcontract</th>
            <th>Total Prod. Kg</th>
            <th>Delivery To Store</th>
        </tfoot>
    </tr>
 </table> 
 
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
	 //echo $proSql;

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
 ?>
 

   
  <table width="920" cellpadding="0" cellspacing="0" rules="all" border="1">
                 	<tr>
                 		<th colspan="11">Sewing Input and Output</th>
                 	</tr>
                    <tr bgcolor="#EEE">
                    	<th align="center">SL</th>
                        <th>Working Company</th>
                        <th>Floor</th>
                        <th>Finish Fabric Issue Qty KG</th>
                        <th>Lay Qty Pcs</th>
                        <th>Cutting QC Pcs</th>
                        <th>Emb Send Pcs</th>
                        <th>Emb Receive Pcs</th>
                        <th>Input Qty Pcs</th>
                        <th>Output Qty Pcs</th>
                        <th>Packing Qty Pcs</th>
                     </tr>
                    <?
								
					
						$i=1;
						foreach($dataArr as $key=>$rows)
						{
						list($wo_company_id,$floor_id)=explode('**',$key);
							
					?>
                        <tr>
                            <td align="center"><?=$i; ?></td>
                            <td><?=$company_lib[$wo_company_id]; ?></td>
                            <td><?=$floor_lib[$floor_id]; ?></td>
                            <td align="right"><?= $rows[vvv];?></td>
                            <td align="right"><?= $rows[CUT_LAY];?></td>
                            <td align="right"><?= $rows[QC_PASS_QTY];?></td>
                            <td align="right"><?= $rows[EMB_ISS];?></td>
                            <td align="right"><?= $rows[EMB_REC];?></td>
                            <td align="right"><?= $rows[SEWING_IN];?></td>
                            <td align="right"><?= $rows[SEWING_OUT];?></td>
                            <td align="right"><?= $rows[FINISHING];?></td>
                        </tr>
                    	<?
						$i++;	
						}
					?> 
                    <tfoot bgcolor="#EEE">
                    	<th align="center">SL</th>
                        <th>Working Company</th>
                        <th>Floor</th>
                        <th>Finish Fabric Issue Qty KG</th>
                        <th>Lay Qty Pcs</th>
                        <th>Cutting QC Pcs</th>
                        <th>Emb Send Pcs</th>
                        <th>Emb Receive Pcs</th>
                        <th>Input Qty Pcs</th>
                        <th>Output Qty Pcs</th>
                        <th>Packing Qty Pcs</th>
                    </tfoot>
                    
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
            
		$fullExfactoryQty="select a.DELIVERY_COMPANY_ID,a.DELIVERY_FLOOR_ID,a.BUYER_ID,b.EX_FACTORY_QNTY,(c.PO_QUANTITY*d.TOTAL_SET_QNTY) as PO_QTY_PCS,c.PO_TOTAL_PRICE from pro_ex_factory_delivery_mst a, pro_ex_factory_mst b ,WO_PO_BREAK_DOWN c,WO_PO_DETAILS_MASTER d
where a.id=b.delivery_mst_id and b.PO_BREAK_DOWN_ID=c.id and c.job_id=d.id and a.company_id=$company_id and b.shiping_status < 3 and b.ex_factory_date between '".$previous_date."' and '".$current_date."' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
		  //echo $fullExfactoryQty;die;
		
		$fullExfArray=sql_select($fullExfactoryQty);
		$dataArr=array();
		foreach($fullExfArray as $rows){
			$key=$rows[DELIVERY_COMPANY_ID].'**'.$rows[DELIVERY_FLOOR_ID].'**'.$rows[BUYER_ID];
			$dataArr[$key][EX_FACTORY_QNTY]+=$rows[EX_FACTORY_QNTY];	
			$dataArr[$key][FOB_VAL_USD]+=($rows[PO_TOTAL_PRICE]/$rows[PO_QTY_PCS])*$rows[EX_FACTORY_QNTY];	
		}

	//print_r($dataArr);
	
	/*
	$sql = "select a.job_no,a.costing_date,a.exchange_rate,a.sew_effi_percent,a.costing_per,b.total_cost,b.cm_cost,b.commission from wo_pre_cost_mst a,wo_pre_cost_dtls b
	where a.job_no in($app_job_str) and  a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$data_array=sql_select($sql);
	foreach( $data_array as $row )
	{ 
		$marginDataArr[$row[csf("job_no")]]['total_material_service_cost']=$row[csf("total_cost")]-($row[csf("cm_cost")]);
		$marginDataArr[$row[csf("job_no")]]['cm_cost']=$row[csf("cm_cost")];
		$marginDataArr[$row[csf("job_no")]]['costing_per']=$row[csf("costing_per")];
	}
	
	
	$dzn=0;
	if($marginDataArr[$row[csf('job_no')]]['costing_per']==1){$dzn=12;}
	else if($marginDataArr[$row[csf('job_no')]]['costing_per']==2){$dzn=1;}
	else if($marginDataArr[$row[csf('job_no')]]['costing_per']==3){$dzn=24;}
	else if($marginDataArr[$row[csf('job_no')]]['costing_per']==4){$dzn=36;}
	else if($marginDataArr[$row[csf('job_no')]]['costing_per']==5){$dzn=48;}

	$tot_pic_qty=$row[csf('total_set_qnty')]*$row[csf('job_quantity')];
	$avg_unit_price=$row[csf('avg_unit_price')]/$row[csf('total_set_qnty')];
	$value=$tot_pic_qty*$avg_unit_price; 
	$tmsc=($marginDataArr[$row[csf('job_no')]]['total_material_service_cost']/($dzn*$row[csf('total_set_qnty')]))*$tot_pic_qty;

	$cmValue=($value-$tmsc);*/
	
	
	$i=1;		
    foreach($dataArr as $key=>$row){
		list($work_com_id,$floor_id,$buyer_id)=explode('**',$key);
    ?>
    <tr>
        <td align="center"><?=$i; ?></td>
        <td><?=$company_lib[$work_com_id]; ?></td>
        <td><?=$floor_lib[$floor_id]; ?></td>
        <td><?=$buyer_library[$buyer_id]; ?></td>
        <td align="right"><?=$row[EX_FACTORY_QNTY]; ?></td>
        <td align="right"><?=$row[FOB_VAL_USD]; ?></td>
        <td align="right"></td>
    </tr>
    <?
        $i++;	
    }
    ?>
        
</table>
  
   
    
<? 

		$to="";
		$sql2 = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=14 AND a.MAIL_TYPE=1 and b.mail_user_setup_id=c.id and a.company_id=$company_id and c.STATUS_ACTIVE=1";
		$mail_sql2=sql_select($sql2);
		foreach($mail_sql2 as $row)
		{
			if ($to=="")  $to=$row[csf('email_address')]; else $to=$to.", ".$row[csf('email_address')]; 
		}
		
		$subject="Total Production Activities of ( Date :".date("d-m-Y", strtotime($previous_date)).")";
    	$message="";
    	$message=ob_get_contents();
    	ob_clean();
		$header=mailHeader();
		//if($to!=""){echo sendMailMailer( $to, $subject, $message, $from_mail );}
		
		echo $message;
}



?> 