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


$company_lib = return_library_array( "select id, company_short_name from lib_company where status_active=1 and is_deleted=0   ", "id", "company_short_name");

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
                <? echo $compname;  ?>
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

	$orderSql="select A.BUYER_NAME,A.JOB_NO,B.GROUPING,B.PUB_SHIPMENT_DATE,B.PO_RECEIVED_DATE,B.ID,b.IS_CONFIRMED,(a.total_set_qnty*b.po_quantity) as PO_QTY, b.po_total_price as PO_VALUE ,a.company_name 	from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and a.company_name = '$company_id'
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
            <th colspan="4">Total </th>
            <th align="right"><?=number_format($grandTotal[conf_qty], 2,'.',',') ;?></th>
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



//-----------------------------------------------------------------------------

$delivery_date_cond=" and a.delevery_date between '".$previous_date."' and '".$current_date."'";		
	//=================================delivery to qty==========================



	$delivery_sql="select a.id, sys_number_prefix_num, a.sys_number, c.qnty, a.company_id, a.knitting_source, a.knitting_company, a.location_id,a.remarks, a.delevery_date,a.floor_ids,a.insert_date, a.barcode_type from pro_grey_prod_delivery_mst a,pro_grey_prod_delivery_dtls b,pro_roll_details c 
	where a.id=b.mst_id and  b.id=c.dtls_id and b.mst_id=c.mst_id and b.order_id=c.po_breakdown_id and c.status_active=1 and c.status_active=1 and a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0  and a.entry_form=56 and c.entry_form=56 $delivery_date_cond  group by a.id, sys_number_prefix_num, a.sys_number,a.company_id, a.knitting_source, a.knitting_company, a.location_id,a.remarks, a.delevery_date,a.floor_ids,a.insert_date, a.barcode_type,c.qnty  order by sys_number_prefix_num asc";
	// echo $delivery_sql;
	$floor_wise=array();
	$delivery_data=sql_select($delivery_sql);
	foreach($delivery_data as $row){
		$floor_wise[$row[csf('floor_ids')]]['delivery_qty']+=$row[csf('qnty')];
	}


 
$company_working_cond=" and a.knitting_company=$company_id";
 
 



$year_field="to_char(f.insert_date,'YYYY')";
$year_field_sam="to_char(a.insert_date,'YYYY')";
if($cbo_year!=0) $job_year_cond=" and to_char(f.insert_date,'YYYY')=$cbo_year";  else $job_year_cond="";


 $from_date=$previous_date; $to_date=$previous_date;

$date_con="";
if($from_date!="" && $to_date!="") $date_con=" and a.receive_date between '$from_date' and '$to_date'";

           

	$sql_inhouse="select * from (
	(select b.kniting_charge, a.company_id,a.receive_basis,a.insert_date,a.inserted_by, a.receive_date, a.booking_no,nvl(f.booking_type, 1) booking_type, 1 as is_order, f.entry_form, listagg((cast(a.remarks as varchar2(4000))),',') within group (order by a.remarks) as remarks, listagg((cast(b.id as varchar2(4000))),',') within group (order by b.id) as dtls_id, listagg((cast(b.prod_id as varchar2(4000))),',') within group (order by b.prod_id) as prod_id, listagg((cast(b.febric_description_id as varchar2(4000))),',') within group (order by b.febric_description_id) as febric_description_id,listagg((cast(b.body_part_id as varchar2(4000))),',') within group (order by b.body_part_id) as body_part_id, listagg((cast(b.gsm as varchar2(4000))),',') within group (order by b.gsm) as gsm, listagg((cast(b.width as varchar2(4000))),',') within group (order by b.width) as width, listagg((cast(b.yarn_lot as varchar2(4000))),',') within group (order by b.yarn_lot) as yarn_lot, listagg((cast(b.yarn_count as varchar2(4000))),',') within group (order by b.yarn_count) as yarn_count, listagg((cast(b.stitch_length as varchar2(4000))),',') within group (order by b.stitch_length) as stitch_length, listagg((cast(b.brand_id as varchar2(4000))),',') within group (order by b.brand_id) as brand_id, b.machine_no_id, b.floor_id as floor_id, listagg((cast(b.color_id as varchar2(4000))),',') within group (order by b.color_id) as color_id, listagg((cast(b.color_range_id as varchar2(4000))),',') within group (order by b.color_range_id) as color_range_id, listagg((cast(c.po_breakdown_id as varchar2(4000))),',') within group (order by c.po_breakdown_id) as po_breakdown_id, e.job_no,e.sales_booking_no,sum(b.reject_fabric_receive) as reject_qty,c.is_sales,e.buyer_id unit_id,e.within_group,a.knitting_source,a.knitting_company,e.buyer_id,sum(case when b.shift_name=0 then c.quantity else 0 end) as without_shift, listagg((cast(b.yarn_prod_id as varchar2(4000))),',') within group (order by b.yarn_prod_id) as yarn_prod_id";
	foreach($shift_name as $key=>$val)
	{
		$sql_inhouse.=", sum(case when b.shift_name=$key then c.quantity else 0 end) as qntyshift".strtolower($val);
	}
	$within_group_cond = ($cbo_within_group != 0)?" and e.within_group=$cbo_within_group" : "";

	if($cbo_booking_type > 0)
	{
		if($cbo_booking_type == 89){
			$entry_form_cond = " and f.booking_type = 4 ";
		}
		else
		{
			$entry_form_cond = " and f.entry_form=$cbo_booking_type";
		}
	}
	else
	{
		$entry_form_cond = "";
	}

	
	$sql_inhouse.=" from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c,fabric_sales_order_mst e,  wo_booking_mst f  where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=e.id and e.sales_booking_no=f.booking_no and a.entry_form=2 and a.item_category=13 and c.entry_form=2 and c.trans_type=1  $cbo_company_cond $company_working_cond   and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and f.status_active=1 and f.is_deleted=0 $date_con  group by b.kniting_charge, b.machine_no_id,a.receive_date,e.job_no,e.sales_booking_no, e.within_group,a.company_id,a.receive_basis,a.insert_date,a.inserted_by, a.booking_no, f.booking_type, f.entry_form, b.floor_id, a.knitting_source,a.knitting_company,c.is_sales,e.buyer_id)";

	$sql_inhouse .= " union all  (select b.kniting_charge, a.company_id,a.receive_basis,a.insert_date,a.inserted_by, a.receive_date, a.booking_no,nvl(g.booking_type, 1) booking_type, 2 as is_order, g.entry_form_id as entry_form, listagg((cast(a.remarks as varchar2(4000))),',') within group (order by a.remarks) as remarks, listagg((cast(b.id as varchar2(4000))),',') within group (order by b.id) as dtls_id, listagg((cast(b.prod_id as varchar2(4000))),',') within group (order by b.prod_id) as prod_id, listagg((cast(b.febric_description_id as varchar2(4000))),',') within group (order by b.febric_description_id) as febric_description_id, listagg((cast(b.body_part_id as varchar2(4000))),',') within group (order by b.body_part_id) as body_part_id, listagg((cast(b.gsm as varchar2(4000))),',') within group (order by b.gsm) as gsm, listagg((cast(b.width as varchar2(4000))),',') within group (order by b.width) as width, listagg((cast(b.yarn_lot as varchar2(4000))),',') within group (order by b.yarn_lot) as yarn_lot, listagg((cast(b.yarn_count as varchar2(4000))),',') within group (order by b.yarn_count) as yarn_count, listagg((cast(b.stitch_length as varchar2(4000))),',') within group (order by b.stitch_length) as stitch_length, listagg((cast(b.brand_id as varchar2(4000))),',') within group (order by b.brand_id) as brand_id, b.machine_no_id, b.floor_id as floor_id, listagg((cast(b.color_id as varchar2(4000))),',') within group (order by b.color_id) as color_id, listagg((cast(b.color_range_id as varchar2(4000))),',') within group (order by b.color_range_id) as color_range_id, listagg((cast(c.po_breakdown_id as varchar2(4000))),',') within group (order by c.po_breakdown_id) as po_breakdown_id, e.job_no,e.sales_booking_no,sum(b.reject_fabric_receive) as reject_qty,c.is_sales,e.buyer_id unit_id,e.within_group,a.knitting_source,a.knitting_company,e.buyer_id,sum(case when b.shift_name=0 then c.quantity else 0 end) as without_shift, listagg((cast(b.yarn_prod_id as varchar2(4000))),',') within group (order by b.yarn_prod_id) as yarn_prod_id
	";

	foreach($shift_name as $key=>$val)
	{
		$sql_inhouse.=", sum(case when b.shift_name=$key then c.quantity else 0 end) as qntyshift".strtolower($val);
	}
	$within_group_cond = ($cbo_within_group != 0)?" and e.within_group=$cbo_within_group" : "";

	if($cbo_booking_type > 0)
	{   
		if($cbo_booking_type == 90)
		{
			$entry_form_cond = " and g.booking_type=4";
		}
		else
		{
			$entry_form_cond = " and g.entry_form_id=$cbo_booking_type";
		}
	}else
	{
		$entry_form_cond = "";
	}

	$sql_inhouse.=" from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c,fabric_sales_order_mst e ,wo_non_ord_samp_booking_mst g where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=e.id and e.sales_booking_no=g.booking_no and g.status_active=1 and g.is_deleted=0 and a.entry_form=2 and a.item_category=13 and c.entry_form=2 and c.trans_type=1  $cbo_company_cond $company_working_cond   and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $date_con  group by b.kniting_charge, b.machine_no_id,a.receive_date,e.job_no,e.sales_booking_no, e.within_group,a.company_id,a.receive_basis,a.insert_date,a.inserted_by, a.booking_no, g.booking_type, g.entry_form_id, b.floor_id, a.knitting_source,a.knitting_company,c.is_sales,e.buyer_id)";
	
	$sql_inhouse.=" union all (select b.kniting_charge, a.company_id,a.receive_basis,a.insert_date,a.inserted_by, a.receive_date, a.booking_no,999 as booking_type, 1 as is_order, null as entry_form, listagg((cast(a.remarks as varchar2(4000))),',') within group (order by a.remarks) as remarks, listagg((cast(b.id as varchar2(4000))),',') within group (order by b.id) as dtls_id, listagg((cast(b.prod_id as varchar2(4000))),',') within group (order by b.prod_id) as prod_id, listagg((cast(b.febric_description_id as varchar2(4000))),',') within group (order by b.febric_description_id) as febric_description_id, listagg((cast(b.body_part_id as varchar2(4000))),',') within group (order by b.body_part_id) as body_part_id, listagg((cast(b.gsm as varchar2(4000))),',') within group (order by b.gsm) as gsm, listagg((cast(b.width as varchar2(4000))),',') within group (order by b.width) as width, listagg((cast(b.yarn_lot as varchar2(4000))),',') within group (order by b.yarn_lot) as yarn_lot, listagg((cast(b.yarn_count as varchar2(4000))),',') within group (order by b.yarn_count) as yarn_count, listagg((cast(b.stitch_length as varchar2(4000))),',') within group (order by b.stitch_length) as stitch_length, listagg((cast(b.brand_id as varchar2(4000))),',') within group (order by b.brand_id) as brand_id, b.machine_no_id, b.floor_id as floor_id, listagg((cast(b.color_id as varchar2(4000))),',') within group (order by b.color_id) as color_id, listagg((cast(b.color_range_id as varchar2(4000))),',') within group (order by b.color_range_id) as color_range_id, listagg((cast(c.po_breakdown_id as varchar2(4000))),',') within group (order by c.po_breakdown_id) as po_breakdown_id, e.job_no,e.sales_booking_no,sum(b.reject_fabric_receive) as reject_qty,c.is_sales,e.buyer_id unit_id,e.within_group,a.knitting_source,a.knitting_company,e.buyer_id,sum(case when b.shift_name=0 then c.quantity else 0 end) as without_shift, listagg((cast(b.yarn_prod_id as varchar2(4000))),',') within group (order by b.yarn_prod_id) as yarn_prod_id, sum(case when b.shift_name=1 then c.quantity else 0 end) as qntyshifta, sum(case when b.shift_name=2 then c.quantity else 0 end) as qntyshiftb, sum(case when b.shift_name=3 then c.quantity else 0 end) as qntyshiftc";
	if($cbo_booking_type > 0)
	{   
		$entry_form_cond = " and a.id=0";
	}
	else
	{
		$entry_form_cond = "";
	}
	
	$sql_inhouse.=" from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c,fabric_sales_order_mst e  where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=e.id and e.within_group=2 and a.entry_form=2 and a.item_category=13 and c.entry_form=2 and c.trans_type=1  $cbo_company_cond $company_working_cond   and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $date_con  group by b.kniting_charge, b.machine_no_id,a.receive_date,e.job_no,e.sales_booking_no, e.within_group,a.company_id,a.receive_basis,a.insert_date,a.inserted_by, a.booking_no, b.floor_id, a.knitting_source,a.knitting_company,c.is_sales,e.buyer_id)
	) order by knitting_source,receive_date,machine_no_id  
	";
 

 //echo $sql_inhouse;die;
$nameArray_inhouse=sql_select( $sql_inhouse);

if(str_replace("'","",$cbo_knitting_source)==0 || str_replace("'","",$cbo_knitting_source)==2)
{
	if($from_date!="" && $to_date!="") $date_con_sub=" and a.product_date between '$from_date' and '$to_date'"; else $date_con_sub="";
	$const_comp_arr=return_library_array( "select id, const_comp from lib_subcon_charge", "id", "const_comp");
	//ubcon_ord_mst e //and e.subcon_job=b.job_no and e.subcon_job=d.job_no_mst 
	$sql_inhouse_sub=" SELECT 999 as receive_basis,a.insert_date,a.inserted_by, a.product_date as receive_date, null as booking_no, 999 as booking_type, 1 as is_order, null as entry_form, listagg((cast(a.remarks as varchar2(4000))),',') within group (order by a.remarks) as remarks, listagg((cast(b.id as varchar2(4000))),',') within group (order by b.id) as dtls_id, listagg((cast(b.cons_comp_id as varchar2(4000))),',') within group (order by b.cons_comp_id) as prod_id, 0 as febric_description_id,
	

	
	b.machine_id as machine_no_id, b.floor_id as floor_id, 
	 listagg((cast(b.color_range as varchar2(4000))),',') within group (order by b.color_range) as color_range_id, 
	 listagg((cast(b.order_id as varchar2(4000))),',') within group (order by b.order_id) as po_breakdown_id, 
	 listagg((cast(d.order_no as varchar2(4000))),',') within group (order by d.order_no) as order_nos, d.job_no_mst as job_no, null as sales_booking_no,sum(b.reject_qnty) as reject_qty,0 as is_sales, a.party_id as unit_id,0 as within_group,  2 as knitting_source, a.knitting_company,a.party_id as buyer_id,
	sum(case when b.shift=0 then b.product_qnty else 0 end) as without_shift,
	sum(case when b.shift=1 then b.product_qnty else 0 end) as qntyshifta,
	sum(case when b.shift=2 then b.product_qnty else 0 end) as qntyshiftb,
	sum(case when b.shift=3 then b.product_qnty else 0 end) as qntyshiftc,a.company_id,
	sum(d.rate) as rate, sum(d.amount) as amount 
	from subcon_production_mst a, subcon_production_dtls b, lib_machine_name c, subcon_ord_dtls d 
	where a.id=b.mst_id and b.machine_id=c.id and b.job_no=d.job_no_mst and d.id=b.order_id  and a.product_type=2 and d.status_active=1 and d.is_deleted=0 
	and a.status_active=1 and a.is_deleted=0  $company_working_cond $cbo_company_cond $date_con_sub $floor_id $buyer_id_cond $job_no_cond $order_no_cond $job_year_sub_cond $sales_order_cond $booking_no_cond $within_group_cond
	group by a.product_date,a.knitting_source,a.knitting_company,a.insert_date,a.inserted_by, b.machine_id, b.floor_id, d.job_no_mst, a.party_id,a.company_id 
	
	order by a.product_date, b.machine_id ";//and a.company_id=$cbo_company_name

	 //echo $sql_inhouse_sub;die;//$sub_company_cond $subcompany_working_cond
	 if($cbo_booking_type==0)
	 {
		$nameArray_inhouse_subcon=sql_select( $sql_inhouse_sub);
	 }
}
// echo "<pre>";print_r($nameArray_inhouse);die;

$machine_inhouse_array=$total_running_machine=$buyer_wise_production_arr=array();
foreach ($nameArray_inhouse as $row)
{                
	if($row[csf("knitting_source")]==1)//in-house
	{
		$floor_summary_arr[$row[csf('floor_id')]][1]+=$row[csf('qntyshifta')]+$row[csf('qntyshiftb')]+$row[csf('qntyshiftc')];//Floor Wise
	}
	else // out-bound subcon
	{
		$floor_summary_arr[$row[csf('floor_id')]][2]+=$row[csf('qntyshifta')]+$row[csf('qntyshiftb')]+$row[csf('qntyshiftc')];//Floor Wise
	}
}

foreach ($nameArray_inhouse_subcon as $row)
{
	$floor_summary_arr[$row[csf('floor_id')]][5]+=$row[csf('qntyshifta')]+$row[csf('qntyshiftb')]+$row[csf('qntyshiftc')];//Floor Wise
}
// echo "<pre>";print_r($floor_summary_arr);die;
if(count($floor_summary_arr)){
?>

		<br>

		<table cellspacing="0" cellpadding="0" border="1" rules="all" >
			<thead>
				<tr>
					<th colspan="9">Knitting Production</th>
				</tr>
				<tr>
					<th colspan="9">Floor Wise Knit Production Summary (In-House + Outbound + SubCon)</th>
				</tr>
				<tr>
					<th width="40">SL</th>
					<th width="120">Floor</th>
					<th width="90">Inhouse</th>
					<th width="90">Outbound-Subcon</th>
					<th width="90">Sample With Order</th>
					<th width="90">Sample Without Order</th>
					<th width="90">In Bound Subcon</th>
					<th width="100">Total</th>
					<th width="100">Total Delivery to Store Kg</th>
				</tr>
			</thead>
			<tbody>

			
		  
					<?
					$TotalDeliverytoStoreKg=$tot_qtyinhouse=$tot_qtyinbound=$tot_qtyoutbound=$tot_samplewith_qnty=$tot_samplewithout_qnty=$tot_qtywithout=$total_summ=0;
					$f=1;
					foreach($floor_summary_arr as $key=>$value)
					{
						if ($f%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$out_bound_qnty=$in_bound_qnty=0;

						$in_bound_qnty=$value[1];
						$out_bound_qnty=$value[2];
						$samplewith_qnty=$value[3];
						$samplewithout_qnty=$value[4];
						$subcon_in_qnty=$value[5];

						$tot_flr_summ=$out_bound_qnty+$in_bound_qnty+$samplewithout_qnty+$subcon_in_qnty+$samplewith_qnty;
						?>
						<tr bgcolor="<?= $bgcolor; ?>" >
							<td><?= $f; ?></td>
							<td title="<?= $key;?>"><?= $floor_lib[$key]; ?></td>
							<td align="right"><?= number_format($in_bound_qnty,2,'.',''); ?></td>
							<td align="right"><?= number_format($out_bound_qnty,2,'.',''); ?></td>
							<td align="right"><?= number_format($samplewith_qnty,2,'.',''); ?></td>
							<td align="right"><?= number_format($samplewithout_qnty,2,'.',''); ?></td>
							<td align="right"><?= number_format($subcon_in_qnty,2,'.',''); ?></td>
							<td align="right"><?=  number_format($tot_flr_summ,2,'.',''); ?></td>
							<td align="right"><?=  number_format($floor_wise[$key]['delivery_qty'],2,'.',''); ?></td>
						</tr>
						<?


						$tot_qtyinhouse+=$in_bound_qnty;
						$tot_qtyinbound+=$subcon_in_qnty;
						$tot_qtyoutbound+=$out_bound_qnty;
						$tot_samplewith_qnty+=$samplewith_qnty;
						$tot_samplewithout_qnty+=$samplewithout_qnty;
						$tot_qtywithout+=$samplewithout_qnty;
						$total_summ+=$tot_flr_summ;
						$TotalDeliverytoStoreKg += $floor_wise[$key]['delivery_qty'];
						$f++;
					}

					?>
			</tbody>
			<tfoot>
				<tr>
					<th colspan="2" align="right"><strong>Total</strong></th>
					<th align="right"><?= number_format($tot_qtyinhouse,2,'.',''); ?>&nbsp;</th>
					<th align="right"><?= number_format($tot_qtyoutbound,2,'.',''); ?>&nbsp;</th>
					<th align="right"><?= number_format($tot_samplewith_qnty,2,'.',''); ?>&nbsp;</th>
					<th align="right"><?= number_format($tot_samplewithout_qnty,2,'.',''); ?>&nbsp;</th>
					<th align="right"><?= number_format($tot_qtyinbound,2,'.',''); ?>&nbsp;</th>
					<th align="right"><?= number_format($total_summ,2,'.',''); ?>&nbsp;</th>
					<th align="right"><?= number_format($TotalDeliverytoStoreKg,2,'.',''); ?></th>
				</tr>
				<tr>
					<th colspan="2"><strong>In %</strong></th>
					<th align="right"><? $qtyinhouse_per=($tot_qtyinhouse/$total_summ)*100; echo number_format($qtyinhouse_per,2).' %'; ?>&nbsp;</th>
					<th align="right"><? $qtyoutbound_per=($tot_qtyoutbound/$total_summ)*100; echo number_format($qtyoutbound_per,2).' %'; ?>&nbsp;</th>
					<th align="right"><? $qtywith_per=($tot_samplewith_qnty/$total_summ)*100; echo number_format($qtywith_per,2).' %'; ?>&nbsp;</th>
					<th align="right"><? $qtywithout_per=($tot_samplewithout_qnty/$total_summ)*100; echo number_format($qtywithout_per,2).' %'; ?>&nbsp;</th>                                   <th align="right"><?  $qtyinbound_per=($tot_qtyinbound/$total_summ)*100; echo number_format($qtyinbound_per,2).' %';  ?>&nbsp;</th>
					<th align="right"><? echo "100 %"; ?></th>
					<th></th>
				</tr>
			</tfoot>
	</table>



<?
}








//----------------------------------------------------------------------









	
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
   
 </table>  -->
 
 <?

	$sewing_input_output_data = array();
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
	where a.table_no=b.id and c.mst_id=a.id and a.working_company_id=$company_id  and entry_date between '".$previous_date."' and '".$current_date."' order by b.floor_id asc");

	foreach($cut_lay_data as $row){

	   $sewing_input_output_data[$row[csf('working_company_id')]][$row[csf('floor_id')]]['cut_lay_qty']+=$row[csf('marker_qty')];
   }



   $cutt_qc_data=sql_select(" SELECT a.id,a.mst_id,a.order_id,a.item_id,a.country_id,a.color_id,a.size_id,a.color_size_id,a.bundle_no,a.number_start,a.number_end,a.bundle_qty,a.reject_qty,a.replace_qty,a.qc_pass_qty,b.floor_id,b.serving_company from pro_gmts_cutting_qc_dtls a,pro_gmts_cutting_qc_mst b where a.mst_id=b.id and b.serving_company=$company_id  and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and b.cutting_qc_date between '".$previous_date."' and '".$current_date."' order by b.floor_id asc");

	

   foreach($cutt_qc_data as $row){

	   $sewing_input_output_data[$row[csf('serving_company')]][$row[csf('floor_id')]]['qc_cut_qty']+=$row[csf('qc_pass_qty')];
	   $floor_arr[$row[csf('floor_id')]]=$row[csf('floor_id')];

   }





	 $emb_iss_data = sql_select("select a.id, a.sys_number_prefix_num, a.delivery_date,a.sys_number, a.embel_type, a.production_source, a.serving_company, a.location_id, e.floor_id, a.organic,b.cut_no,c.production_qnty,a.embel_name 	from pro_gmts_delivery_mst a,pro_garments_production_mst b ,pro_garments_production_dtls c ,pro_gmts_cutting_qc_dtls d ,pro_gmts_cutting_qc_mst e 
	 where a.id=b.delivery_mst_id and b.id=c.mst_id and a.production_type=2 and a.embel_name in (1,2) and a.status_active=1 and c.BUNDLE_NO=d.BUNDLE_NO and  d.mst_id=e.id and a.is_deleted=0 and a.serving_company=$company_id ".where_con_using_array($floor_arr,1,'e.floor_id')."  and a.delivery_date between '".$previous_date."' and '".$current_date."' order by a.floor_id asc"); 


	


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
		where a.id=b.delivery_mst_id and b.id=c.mst_id and a.production_type=3 and a.embel_name in (1,2) and a.status_active=1 and c.BUNDLE_NO=d.BUNDLE_NO and  d.mst_id=e.id and a.is_deleted=0 and a.serving_company=$company_id ".where_con_using_array($floor_arr,1,'e.floor_id')."  and a.delivery_date between '".$previous_date."' and '".$current_date."' order by a.floor_id asc"); 
	

		
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

		


		$sewing_input_data=sql_select("SELECT c.id as prdid,d.id as colorsizeid, e.id as po_id, f.job_no_prefix_num, f.buyer_name, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.cut_no,c.bundle_no,c.barcode_no,c.production_qnty,c.is_rescan, e.po_number,h.serving_company,h.floor_id,c.production_type from pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e, wo_po_details_master f,pro_gmts_delivery_mst h,pro_garments_production_mst i  where h.id=i.delivery_mst_id and  h.serving_company=$company_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and e.job_id=f.id and f.id=d.job_id  and c.production_type in (4,5) and c.delivery_mst_id=h.id and c.status_active=1 and c.is_deleted=0   and h.delivery_date between '".$previous_date."' and '".$current_date."' order by h.floor_id asc"); 


	


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


	


		$packing_data =sql_select("SELECT id, company_id, garments_nature, po_break_down_id, serving_company, sewing_line, production_date, production_quantity, production_source, production_type, floor_id  from pro_garments_production_mst where  production_type='8' and  serving_company=$company_id  and status_active=1 and is_deleted=0 and production_date between '".$previous_date."' and '".$current_date."' order by order by floor_id asc ");

	
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
						$cut_lay_qty=0; $qc_cut_qty=0; $print_iss=0; $print_recv=0; $emb_iss=0; $emb_recv=0; $sewing_input=0; $sewing_output=0; $packing_qty=0;
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

		$to="";
		$sql2 = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=14 and b.mail_user_setup_id=c.id and a.company_id=$company_id and c.STATUS_ACTIVE=1";
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

		//  $to='asmmi555@gmail.com';
		//if($to!=""){echo sendMailMailer( $to, $subject, $message, $from_mail );}
		
		if($_REQUEST['isview']==1){
			echo $message;
		}
		else{
			if($to!=""){echo sendMailMailer( $to, $subject, $message, $from_mail );}
		}

		
}



?> 