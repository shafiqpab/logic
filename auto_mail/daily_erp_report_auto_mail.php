<?php
//ini_set('memory_limit','8024M');

date_default_timezone_set("Asia/Dhaka");
//require_once('../mailer/class.phpmailer.php');
require_once('../includes/common.php');
require_once('setting/mail_setting.php');

 
$company_library = return_library_array( "select id, company_short_name from lib_company where status_active=1 and is_deleted=0", "id", "company_short_name",$con);
$floor_library = return_library_array( "select id,floor_name from lib_prod_floor where is_deleted=0 and status_active=1", "id", "floor_name");

 $conversion_rate=return_field_value("conversion_rate","currency_conversion_rate","is_deleted=0 and status_active=1 and id=(select max(id) from currency_conversion_rate where currency=2 and is_deleted=0 and status_active=1)","",$con);
 $buyer_arr = return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
 $convToTon=1000;
 $convToMilion=1000000;
 $convByLBS=2204.6;

	if($db_type==0)
	{
		$current_date = date("Y-m-d H:i:s",strtotime(add_time(date("H:i:s",time()),0)));
		if($_REQUEST['view_date']){
			$current_date = change_date_format(date("Y-m-d H:i:s",strtotime($_REQUEST['view_date'])),'','',1);
		}
		$previous_date= date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))); 
	}
	else
	{
		$current_date = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("H:i:s",time()),0))),'','',1);
		if($_REQUEST['view_date']){
			$current_date = change_date_format(date("Y-m-d H:i:s",strtotime($_REQUEST['view_date'])),'','',1);
		}
		$previous_date= change_date_format(date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))),'','',1);
	}
	
	function fn_remove_zero($int,$format){
		return $int>0?number_format($int,$format):"";
		
	}




//----------------Roder Part------------------------------------------------
	$pub_ship_cond	=" and b.po_received_date between '".$previous_date."' and '".$previous_date."'";			
	$job_sql = "SELECT a.COMPANY_NAME,b.PO_TOTAL_PRICE from wo_po_details_master a,WO_PO_BREAK_DOWN b where a.id=b.job_id and a.status_active!=0 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $pub_ship_cond";		
	
	//echo $job_sql;die;
	
	$job_sql_result = sql_select($job_sql);
	foreach ($job_sql_result as $row) {
		$company_wise_po_value_arr[$row[COMPANY_NAME]] += ($row[PO_TOTAL_PRICE]);
	}
	unset($job_sql_result);

ob_start();	
?>
<P>
    <b>Daily ERP Report</b><br />
    Date:<?= $previous_date;?> [Ex.Rate:<?=$conversion_rate;?>]
</P>
<table border="1" rules="all">
    <tr bgcolor="#CCCCCC">
        <th>Order Receive</th>
        <? foreach($company_wise_po_value_arr as $company_id=>$value){echo "<th>$company_library[$company_id]</th>";}?>
        <th>Group Total</th>
    </tr>
    <tr>
        <td title="Conversion Rate:<?=$conversion_rate;?>">Order Receive Value (USD)</td>
        <? foreach($company_wise_po_value_arr as $company_id=>$value){echo "<td align='right'>".number_format($value,2)."</td>";}?>
        <td align='right'><?= number_format(array_sum($company_wise_po_value_arr),2);?></td>
    </tr>

</table><br />

<?
//----------------Production Part-----------------------------------------------                           
	
	$production_date_con = " and a.production_date between '".$previous_date."' and '".$previous_date."'";
/*	$sql_query="select a.serving_company,a.SEWING_LINE,
	sum(case when a.production_type=1 and b.production_type=1 then b.production_qnty else 0 end) as cut_qnty ,
	sum(case when a.production_type=8 and b.production_type=8 then b.production_qnty else 0 end) as pack_qnty ,
	sum(case when a.production_type=5 and b.production_type=5 then b.production_qnty else 0 end) as sewing_output 
	
	from pro_garments_production_mst a,pro_garments_production_dtls b
	where a.production_type in(1,5,8) and b.production_type in(1,5,8) and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0  $production_date_con group by a.serving_company,a.SEWING_LINE";
*/		
	$sql_query="SELECT a.serving_company,a.production_type,a.SEWING_LINE,
    	SUM (b.production_qnty) AS qty, SUM (b.reject_qty) AS reject_qnty
    FROM pro_garments_production_mst a, pro_garments_production_dtls b
   	WHERE a.id = b.mst_id $production_date_con AND b.status_active = 1 AND b.is_deleted = 0  
	AND a.is_deleted = 0 AND a.status_active = 1 AND a.production_type in(1,5,8) and b.production_type in(1,5,8)
	GROUP BY a.serving_company, a.production_type,a.SEWING_LINE";
	 //echo $sql_query;die;
	$production_sql_result=sql_select($sql_query);			 
	foreach($production_sql_result as $val)
	{
		if($val[csf('production_type')]==1){$cut_qty_arr[$val[csf('serving_company')]]+=$val[csf('qty')];}
		else if($val[csf('production_type')]==8){$pack_qty_arr[$val[csf('serving_company')]]+=$val[csf('qty')];}
		else if($val[csf('production_type')]==5){
			$sewing_qty_arr[$val[csf('serving_company')]]+=$val[csf('qty')];
			
			$sewing_line_arr[$val[csf('serving_company')]][$val['SEWING_LINE']]=$val['SEWING_LINE'];
			$production_company_arr[$val[csf('serving_company')]]=$val[csf('serving_company')];
		}
		//$cut_qty_arr[$val[csf('serving_company')]]+=$val[csf('cut_qnty')];
		//$pack_qty_arr[$val[csf('serving_company')]]+=$val[csf('pack_qnty')];
		//$sewing_qty_arr[$val[csf('serving_company')]]+=$val[csf('sewing_output')];
		
	}
	unset($production_sql_result);
	
	
	//echo count($sewing_line_arr[3]);die;

//Exfactory--------------------
	$ex_factory_date_con = " and b.ex_factory_date between '".$previous_date."' and '".$previous_date."'";
	$ex_factory_sql="select a.delivery_company_id,sum(b.ex_factory_qnty) ex_factory_qnty  from pro_ex_factory_delivery_mst a,pro_ex_factory_mst b where b.delivery_mst_id=a.id  $ex_factory_date_con and a.is_deleted=0 and a.status_active=1 AND b.status_active IN (1, 2, 3)  and b.entry_form!=85 group by a.delivery_company_id";
	 //echo $ex_factory_sql;
	$ex_factory_sql_result = sql_select($ex_factory_sql);
	foreach($ex_factory_sql_result as $rows)
	{
		$ex_fac_qty_arr[$rows[csf("delivery_company_id")]]+=$rows[csf("ex_factory_qnty")];
		$production_company_arr[$rows[csf('delivery_company_id')]]=$rows[csf('delivery_company_id')];
	}
	unset($ex_factory_sql_result);
	


?>


<table border="1" rules="all">
    <tr bgcolor="#CCCCCC">
        <th>Garments Production</th>
        <? foreach($production_company_arr as $company_id=>$value){echo "<th title='$company_id'>$company_library[$company_id]</th>";}?>
        <th>Group Total</th>
    </tr>
    <tr>
        <td>Cutting Production</td>
        <? foreach($production_company_arr as $company_id=>$value){echo "<td align='right'>$cut_qty_arr[$company_id]</td>
		";}?>
        <td align='right'><?= $cut_qty_tot=array_sum($cut_qty_arr);?></td>
    </tr>
    <tr>
        <td>Sewing Production</td>
        <? foreach($production_company_arr as $company_id=>$value){echo "<td align='right'>$sewing_qty_arr[$company_id]</td>";}?>
        <td align='right'><?= $sewing_qty_tot=array_sum($sewing_qty_arr);?></td>
    </tr>
    <tr>
        <td>Finishing Production</td>
        <? foreach($production_company_arr as $company_id=>$value){echo "<td align='right'>$pack_qty_arr[$company_id]</td>";}?>
        <td align='right'><?= $pac_qty_tot=array_sum($pack_qty_arr);?></td>
    </tr>
    <tr>
        <td>Shipment</td>
        <? foreach($production_company_arr as $company_id=>$value){echo "<td align='right'>$ex_fac_qty_arr[$company_id]</td>";}?>
        <td align='right'><?= $ex_fac_qty_tot=array_sum($ex_fac_qty_arr);?></td>
    </tr>
    <tr bgcolor="#CCCCCC">
        <th>Total</th>
        <? foreach($production_company_arr as $company_id=>$value){echo "<th align='right'>".
		($cut_qty_arr[$company_id]+ $sewing_qty_arr[$company_id]+ $pack_qty_arr[$company_id]+ $ex_fac_qty_arr[$company_id])."
		</th>";}?>
        <th align='right'><?= ($cut_qty_tot+$sewing_qty_tot+$pac_qty_tot+$ex_fac_qty_tot)?></th>
    </tr>
</table><br />
<?
//Fabric Production part.......................................................
	
 $is_fabric_roll_level=return_field_value("fabric_roll_level","variable_settings_production","variable_list=3 and item_category_id=13 and is_deleted=0 and status_active=1)","",$con);
	$fb_pro_rec_date =" and a.receive_date between '".$previous_date."' and '".$previous_date."'";
	
	/*
	 $sql_qty="select a.COMPANY_ID,sum(c.quantity) as qtyinhouse 
	 from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.item_category=13 and a.id=b.mst_id and b.id=c.dtls_id and c.entry_form=2 and c.trans_type=1 and a.receive_basis!=4 and a.knitting_source in(1,3) $fb_pro_rec_date and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by A.COMPANY_ID
  union all
     select a.COMPANY_ID,sum(b.PRODUCT_QNTY) as qtyinhouse  from subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id  and a.product_type in(2) and b.product_type in(2)  and a.product_date between '".$previous_date."' and '".$previous_date."'   and a.is_deleted=0 and a.status_active=1  and b.is_deleted=0 and b.status_active=1 group by A.COMPANY_ID	 
	 ";*/
	 
	 
	$sql_qty="select a.KNITTING_COMPANY as COMPANY_ID, ";
	if($is_fabric_roll_level==1) // Yes
	{
		
		$sql_qty.=" sum(case when a.knitting_source=1 and b.machine_no_id>0 then g.qnty else 0 end ) as qtyinhouse, sum(case when a.knitting_source=3 then g.qnty else 0 end ) as qtyoutbound from inv_receive_master a, pro_grey_prod_entry_dtls b left join lib_machine_name d on b.machine_no_id=d.id, order_wise_pro_details c, wo_po_break_down e,  wo_po_details_master f, pro_roll_details g
		where c.po_breakdown_id=e.id and a.entry_form=2 and a.item_category=13 and a.id=b.mst_id and b.id=g.dtls_id and c.po_breakdown_id=g.po_breakdown_id and g.entry_form=2 and b.id=g.dtls_id and b.id=c.dtls_id and e.job_no_mst=f.job_no and c.entry_form=2 and c.trans_type=1  and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and a.receive_basis!=4 and b.is_deleted=0 and c.status_active=1 and c.is_sales=0 and c.is_deleted=0 and g.status_active=1 and g.is_deleted=0 $fb_pro_rec_date and  a.knitting_source=1";
	}
	else // 2 No
	{
		
		$sql_qty.=" sum(case when a.knitting_source=1 and b.machine_no_id>0 then c.quantity else 0 end ) as qtyinhouse, sum(case when a.knitting_source=3 then c.quantity else 0 end ) as qtyoutbound from inv_receive_master a, pro_grey_prod_entry_dtls b left join lib_machine_name d on b.machine_no_id=d.id, order_wise_pro_details c, wo_po_break_down e,  wo_po_details_master f
		where c.po_breakdown_id=e.id and a.entry_form=2 and a.item_category=13 and a.id=b.mst_id and b.id=c.dtls_id and e.job_no_mst=f.job_no and c.entry_form=2 and c.trans_type=1  and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and a.receive_basis!=4 and b.is_deleted=0 and c.status_active=1 and c.is_sales=0 and c.is_deleted=0 $fb_pro_rec_date and a.knitting_source=1";
	}
	
	$sql_qty.=" group by a.KNITTING_COMPANY";	 
	$sql_result=sql_select($sql_qty);
	$knit_grey_receive_qty_arr=array();
	foreach($sql_result as $row)
	{
		$knit_grey_receive_qty_arr[$row[COMPANY_ID]] += $row[csf('qtyinhouse')];
		//$knit_grey_receive_qty_arr[$row[COMPANY_ID]] += $row[csf('qtyoutbound')];
		$fb_pro_company_arr[$row[COMPANY_ID]] = $row[COMPANY_ID];
	}				
	unset($sql_result);
	

//print_r($fb_pro_company_arr);die;	

	
if ($roll_maintained_yesNo==1) 
{
	$sql_sample_sam="SELECT a.KNITTING_COMPANY as COMPANY_ID, a.knitting_source, sum(g.qnty ) as sample_qty
	  from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details g  
	  where a.entry_form=2 and a.item_category=13 and a.id=b.mst_id and a.id=g.mst_id and b.id=G.DTLS_ID and a.receive_basis!=4 and a.buyer_id>0  and a.status_active=1  and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_without_order in(1) $fb_pro_rec_date
	  group by a.KNITTING_COMPANY";
} // and g.is_sales=2
else 
{
	$sql_sample_sam="SELECT a.KNITTING_COMPANY as COMPANY_ID, sum(case when a.booking_without_order=1 then b.grey_receive_qnty end ) as sample_qty from inv_receive_master a, pro_grey_prod_entry_dtls b where a.entry_form=2 and a.item_category=13 and a.id=b.mst_id and a.receive_basis!=4 and a.buyer_id>0  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_without_order in(1) $fb_pro_rec_date group by a.KNITTING_COMPANY ";
}	
	$sql_sample_sam_result=sql_select($sql_sample_sam);
	foreach($sql_sample_sam_result as $row)
	{
		$knit_grey_receive_qty_arr[$row[COMPANY_ID]] += $row[csf('sample_qty')];
		$fb_pro_company_arr[$row[COMPANY_ID]] = $row[COMPANY_ID];
	}				
	unset($sql_sample_sam_result);
	
	
	$sql_service_samary="SELECT a.KNITTING_COMPANY as COMPANY_ID, sum(b.grey_receive_qnty) as service_qty from inv_receive_master a, pro_grey_prod_entry_dtls b where a.entry_form in(22,58) and a.receive_basis in(10,11) and a.item_category=13 and a.id=b.mst_id $fb_pro_rec_date  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $fb_pro_rec_date  group by a.KNITTING_COMPANY"; 
	$sql_service_samary_result=sql_select($sql_service_samary);
	foreach($sql_service_samary_result as $row)
	{
		$knit_grey_receive_qty_arr[$row[COMPANY_ID]] += $row[csf('service_qty')];
		$fb_pro_company_arr[$row[COMPANY_ID]] = $row[COMPANY_ID];
	}				
	unset($sql_service_samary_result);




	$sql_knit_pro="select a.KNITTING_COMPANY as COMPANY_ID,b.QNTY from INV_RECEIVE_MASTER a,PRO_ROLL_DETAILS b where a.ITEM_CATEGORY=13  and a.entry_form=2 and b.entry_form=2 and a.id=b.mst_id and a.RECEIVE_DATE between '".$previous_date."' and '".$previous_date."' and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0"; 

	$sql_knit_pro_result=sql_select($sql_knit_pro);
	$kniting_pro_qty_arr=array();
	foreach($sql_knit_pro_result as $row)
	{
		$kniting_pro_qty_arr[$row['COMPANY_ID']] += $row['QNTY'];
		$fb_pro_company_arr[$row['COMPANY_ID']] = $row['COMPANY_ID'];
	}				
	unset($sql_knit_pro_result);



	

	
 //Dyeing--------------------------------------                            
    /*$dye_date_con=" and a.process_end_date between '$previous_date' and '$previous_date'";
	
	$dye_sql="select a.SERVICE_COMPANY as company_id,sum(b.production_qty) as production_qty,c.total_trims_weight
from  pro_fab_subprocess a, pro_fab_subprocess_dtls b,pro_batch_create_mst c
where a.id = b.mst_id and a.batch_id=c.id and a.load_unload_id = 2 and a.result=1 and b.load_unload_id=2 and b.entry_page=35 and a.status_active = 1 and b.status_active=1  and c.status_active=1 $dye_date_con  group by a.SERVICE_COMPANY,c.total_trims_weight";
$dyeing_qty=0;
	$dye_sql_result = sql_select($dye_sql);
	foreach($dye_sql_result as $row)
	{
		$dyeing_qty_arr[$row[csf('company_id')]]+=($row[csf('production_qty')]+$row[csf('total_trims_weight')]);
		$fb_pro_company_arr[$row[csf('company_id')]] = $row[csf('company_id')];
	}
	unset($dye_sql_result);

*/
	 //echo $dye_sql;
	 
	 $dye_date_con="  AND f.process_end_date between '$previous_date' and '$previous_date'";
	 $dye_sql="SELECT a.working_company_id as company_id, SUM (b.batch_qnty) AS batch_qnty
				FROM pro_batch_create_dtls b, wo_po_break_down c, wo_po_details_master d,
					 pro_batch_create_mst a, pro_fab_subprocess   f
			   WHERE     f.batch_id = a.id AND a.entry_form = 0
					 AND a.id = b.mst_id AND f.batch_id = b.mst_id
					 AND f.entry_form = 35 AND f.load_unload_id = 2
					 AND a.batch_against IN (1, 2) AND b.po_id = c.id
					 AND d.job_no = c.job_no_mst AND b.status_active = 1
					 AND b.is_deleted = 0  AND a.status_active = 1
					 AND a.is_deleted = 0  AND f.status_active = 1
					 AND f.is_deleted = 0 $dye_date_con
					 AND f.result = 1 AND f.service_source = 1
			GROUP BY a.working_company_id";
	 
	  
	$dyeing_qty=0;
	$dye_sql_result = sql_select($dye_sql);
	foreach($dye_sql_result as $row)
	{
		$dyeing_qty_arr[$row[csf('company_id')]]+=$row[csf('batch_qnty')];
		$fb_pro_company_arr[$row[csf('company_id')]] = $row[csf('company_id')];
	}
	unset($dye_sql_result);
	
	/*$sub_dye_sql="select a.company_id,sum(c.batch_weight) as batch_weight
from  pro_fab_subprocess a,pro_batch_create_mst c
where a.batch_id=c.id and a.load_unload_id = 2 $dye_date_con  and a.result=1 and a.entry_form=38 and c.status_active = 1 and c.status_active=1  and c.status_active=1 group by a.company_id";
	$sub_dye_sql_result = sql_select($sub_dye_sql);
	foreach($sub_dye_sql_result as $row)
	{
		$dyeing_qty_arr[$row[csf('company_id')]]+=$row[csf('batch_weight')];
		$fb_pro_company_arr[$row[csf('company_id')]] = $row[csf('company_id')];
	}
	unset($dye_sql_result);	*/
	
	
//Finishing (Kg/Yds)...............................................
		
		$finishigSql="select a.knitting_company,b.REJECT_QTY,b.receive_qnty,b.uom from inv_receive_master a,pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.entry_form=7 and a.item_category=2 and a.receive_basis=5 and a.receive_date between '$previous_date' and '$previous_date' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0"; //and a.recv_number = 'FTML-FFPE-18-03926'
		$finishigSqlResult = sql_select($finishigSql);
		foreach ($finishigSqlResult as $row) 
		{
			$finish_qty_arr[$row[csf('knitting_company')]]+=$row[csf('receive_qnty')];
			$fb_pro_company_arr[$row[csf('knitting_company')]] = $row[csf('knitting_company')];
		}
			
			
			
		$sub_finishig_sql="select a.COMPANY_ID,12 as uom, b.product_qnty as finish_qty,b.REJECT_QNTY from subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id  and a.product_type=4 and a.product_date between '$from_date' and '$to_date' and a.is_deleted=0 and a.status_active=1  and b.is_deleted=0 and b.status_active=1
"; 
		$sub_finishig_sql_result = sql_select($sub_finishig_sql);
		foreach ($sub_finishig_sql_result as $row) 
		{
			$finish_qty_arr[$row[csf('COMPANY_ID')]]+=$row[csf('finish_qty')];
			$fb_pro_company_arr[$row[csf('COMPANY_ID')]] = $row[csf('COMPANY_ID')];
			//$finishRejQty[$row[csf('uom')]]+=$row[csf('REJECT_QNTY')];

		}
		
//	
		
		
?>


<table border="1" rules="all">
    <tr bgcolor="#CCCCCC">
        <th>Fabric Production</th>
        <? foreach($fb_pro_company_arr as $company_id=>$value){echo "<th title='$company_id'>$company_library[$company_id]</th>";}?>
        <th>Group Total</th>
    </tr>
    <tr>
        <td>Knitting Production (Ton)</td>
        <? foreach($fb_pro_company_arr as $company_id=>$value){echo "<td align='right'>".number_format(($kniting_pro_qty_arr[$company_id]/$convToTon),2)."</td>";}?>
        <td align='right'><?= number_format($knit_com_total = (array_sum($kniting_pro_qty_arr)/$convToTon),2);?></td>
    </tr>
	<tr>
        <td>Knit Grey Receive (Ton)</td>
        <? foreach($fb_pro_company_arr as $company_id=>$value){echo "<td align='right'>".number_format(($knit_grey_receive_qty_arr[$company_id]/$convToTon),2)."</td>";}?>
        <td align='right'><?= number_format($knit_greu_rec_com_total = (array_sum($knit_grey_receive_qty_arr)/$convToTon),2);?></td>
    </tr>
    <tr>
        <td>Dyeing Production (Ton)</td>
        <? foreach($fb_pro_company_arr as $company_id=>$value){echo "<td align='right'>".number_format(($dyeing_qty_arr[$company_id]/$convToTon),2)."</td>";}?>
        <td align='right'><?= number_format($dye_com_total=(array_sum($dyeing_qty_arr)/$convToTon),2);?></td>
    </tr>
    <tr>
        <td>Finishing Production (Ton)</td>
        <? foreach($fb_pro_company_arr as $company_id=>$value){echo "<td align='right'>".number_format(($finish_qty_arr[$company_id]/$convToTon),2)."</td>";}?>
        <td align='right'><?= number_format($fin_com_total=(array_sum($finish_qty_arr)/$convToTon),2);?></td>
    </tr>
    
    
    <tr bgcolor="#CCCCCC">
        <th>Total</th>
        <? foreach($fb_pro_company_arr as $company_id=>$value){echo "<th align='right'>".
		number_format((($kniting_pro_qty_arr[$company_id]+ $knit_grey_receive_qty_arr[$company_id]+$dyeing_qty_arr[$company_id]+ $finish_qty_arr[$company_id])/$convToTon),2)."
		</th>";}?>
        <th align='right'><?= number_format(($knit_com_total+$knit_greu_rec_com_total+$dye_com_total+$fin_com_total),2)?></th>
    </tr>
    
</table><br />

<?
//Stock & Value........................................................................

$receive_array = array();
		
		$sql_receive = "Select a.company_id,a.prod_id,a.receive_basis,d.pay_mode, 0 as store_id, max(a.weight_per_bag) as weight_per_bag, max(a.weight_per_cone) as weight_per_cone,
		sum(case when a.transaction_type in (1,4) and a.transaction_date<'" . $previous_date . "' then a.cons_quantity else 0 end) as rcv_total_opening,
		sum(case when a.transaction_type in (1,4) and a.transaction_date<'" . $previous_date . "' then a.cons_amount else 0 end) as rcv_total_opening_amt,
		sum(case when a.transaction_type in (1,4) and a.transaction_date<'" . $previous_date . "' then a.cons_rate else 0 end) as rcv_total_opening_rate,         	
		sum(case when a.transaction_type in (1) and c.receive_purpose<>5 and a.transaction_date between '" . $previous_date . "' and '" . $previous_date . "' then a.cons_quantity else 0 end) as purchase,
		sum(case when a.transaction_type in (1) and c.receive_purpose<>5 and a.transaction_date between '" . $previous_date . "' and '" . $previous_date . "' then a.cons_amount else 0 end) as purchase_amt,
		sum(case when a.transaction_type in (1) and c.receive_purpose=5 and a.transaction_date between '" . $previous_date . "' and '" . $previous_date . "' then a.cons_quantity else 0 end) as rcv_loan,
		sum(case when a.transaction_type in (1) and c.receive_purpose=5 and a.transaction_date between '" . $previous_date . "' and '" . $previous_date . "' then a.cons_amount else 0 end) as rcv_loan_amt,
		sum(case when a.transaction_type=4 and c.knitting_source=1 and a.transaction_date between '" . $previous_date . "' and '" . $previous_date . "' then a.cons_quantity else 0 end) as rcv_inside_return,
		sum(case when a.transaction_type=4 and c.knitting_source=1 and a.transaction_date between '" . $previous_date . "' and '" . $previous_date . "' then a.cons_amount else 0 end) as rcv_inside_return_amt,
		sum(case when a.transaction_type=4 and c.knitting_source!=1 and a.transaction_date between '" . $previous_date . "' and '" . $previous_date . "' then a.cons_quantity else 0 end) as rcv_outside_return,
		sum(case when a.transaction_type=4 and c.knitting_source!=1 and a.transaction_date between '" . $previous_date . "' and '" . $previous_date . "' then a.cons_amount else 0 end) as rcv_outside_return_amt,0 as rcv_adjustment_qty, 0 as rcv_adjustment_amt
		from inv_transaction a left join wo_yarn_dyeing_mst d on a.pi_wo_batch_no=d.id, inv_receive_master c where a.mst_id=c.id and a.transaction_type in (1,4) and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $company_cond $store_cond group by a.company_id,a.prod_id,a.receive_basis,d.pay_mode

		union all  

		Select a.company_id,a.prod_id,a.receive_basis,0 as pay_mode, 0 as store_id,max(a.weight_per_bag) as weight_per_bag, max(a.weight_per_cone) as weight_per_cone, 0 as rcv_total_opening,0 as rcv_total_opening_amt, 0 as rcv_total_opening_rate, 0 as purchase, 0 as purchase_amt,0 as rcv_loan, 0 as rcv_loan_amt,0 as rcv_inside_return,0 as rcv_inside_return_amt,0 as rcv_outside_return,0 as rcv_outside_return_amt,sum(case when a.transaction_type in (1,4) and a.receive_basis=30 and a.transaction_date<= '". $previous_date . "' then a.cons_quantity else 0 end) as rcv_adjustment_qty,sum(case when a.transaction_type in (1,4) and a.receive_basis=30 and a.transaction_date<= '" . $previous_date . "' then a.cons_amount else 0 end) as rcv_adjustment_amt from inv_transaction a where a.receive_basis=30 and a.item_category=1 and a.transaction_type in (1,4) and a.status_active=1 and a.is_deleted=0 $company_cond $store_cond group by a.company_id,a.prod_id,a.receive_basis";

//echo $sql_receive;die;


		$result_sql_receive = sql_select($sql_receive);
		foreach ($result_sql_receive as $row)
		{
			$receive_array[$row[csf("company_id")]][$row[csf("prod_id")]]['store_id'] = $row[csf("store_id")];
			$receive_array[$row[csf("company_id")]][$row[csf("prod_id")]]['pay_mode'] = $row[csf("pay_mode")];
			$receive_array[$row[csf("company_id")]][$row[csf("prod_id")]]['receive_basis'] = $row[csf("receive_basis")]; 
			$receive_array[$row[csf("company_id")]][$row[csf("prod_id")]]['rcv_total_opening'] += $row[csf("rcv_total_opening")];
			$receive_array[$row[csf("company_id")]][$row[csf("prod_id")]]['rcv_total_opening_amt'] += $row[csf("rcv_total_opening_amt")];
			$receive_array[$row[csf("company_id")]][$row[csf("prod_id")]]['purchase'] += $row[csf("purchase")];
			$receive_array[$row[csf("company_id")]][$row[csf("prod_id")]]['purchase_amt'] += $row[csf("purchase_amt")];
			$receive_array[$row[csf("company_id")]][$row[csf("prod_id")]]['rcv_loan'] += $row[csf("rcv_loan")];
			$receive_array[$row[csf("company_id")]][$row[csf("prod_id")]]['rcv_loan_amt'] += $row[csf("rcv_loan_amt")];
			$receive_array[$row[csf("company_id")]][$row[csf("prod_id")]]['rcv_inside_return'] += $row[csf("rcv_inside_return")];
			$receive_array[$row[csf("company_id")]][$row[csf("prod_id")]]['rcv_inside_return_amt'] += $row[csf("rcv_inside_return_amt")];
			$receive_array[$row[csf("company_id")]][$row[csf("prod_id")]]['rcv_outside_return'] += $row[csf("rcv_outside_return")];
			$receive_array[$row[csf("company_id")]][$row[csf("prod_id")]]['rcv_outside_return_amt'] += $row[csf("rcv_outside_return_amt")];
			$receive_array[$row[csf("company_id")]][$row[csf("prod_id")]]['weight_per_bag'] = $row[csf("weight_per_bag")];
			$receive_array[$row[csf("company_id")]][$row[csf("prod_id")]]['weight_per_cone'] = $row[csf("weight_per_cone")];
			$receive_array[$row[csf("company_id")]][$row[csf("prod_id")]]['rcv_total_opening_rate'] = $row[csf("rcv_total_opening_rate")];
			$receive_array[$row[csf("company_id")]][$row[csf("prod_id")]]['rcv_adjustment_qty'] += $row[csf("rcv_adjustment_qty")];
			$receive_array[$row[csf("company_id")]][$row[csf("prod_id")]]['rcv_adjustment_amt'] += $row[csf("rcv_adjustment_amt")];
		}

		unset($result_sql_receive);

		$issue_array = array();
					$sql_issue = "select a.prod_id, 0 as store_id,
		sum(case when a.transaction_type in (2,3) and a.transaction_date<'" . $previous_date . "' then a.cons_quantity else 0 end) as issue_total_opening,
		sum(case when a.transaction_type in (2,3) and a.transaction_date<'" . $previous_date . "' then a.cons_rate else 0 end) as issue_total_opening_rate,
		sum(case when a.transaction_type in (2,3) and a.transaction_date<'" . $previous_date . "' then a.cons_amount else 0 end) as issue_total_opening_amt,
		sum(case when a.transaction_type=2 and c.knit_dye_source=1 and c.issue_purpose<>5 and a.transaction_date  between '" . $previous_date . "' and '" . $previous_date . "' then a.cons_amount else 0 end) as issue_inside_amt,
		sum(case when a.transaction_type=2 and c.knit_dye_source=1 and c.issue_purpose<>5 and a.transaction_date  between '" . $previous_date . "' and '" . $previous_date . "' then a.cons_quantity else 0 end) as issue_inside,
		sum(case when a.transaction_type=2 and c.knit_dye_source=1 and c.issue_purpose<>5 and a.transaction_date  between '" . $previous_date . "' and '" . $previous_date . "' then a.cons_amount else 0 end) as issue_inside_amt,
		sum(case when a.transaction_type=2 and c.knit_dye_source!=1 and c.issue_purpose<>5 and a.transaction_date  between '" . $previous_date . "' and '" . $previous_date . "' then a.cons_quantity else 0 end) as issue_outside,
		sum(case when a.transaction_type=2 and c.knit_dye_source!=1 and c.issue_purpose<>5 and a.transaction_date  between '" . $previous_date . "' and '" . $previous_date . "' then a.cons_amount else 0 end) as issue_outside_amt,
		sum(case when a.transaction_type=3 and c.entry_form=8 and a.transaction_date between '" . $previous_date . "' and '" . $previous_date . "' then a.cons_quantity else 0 end) as rcv_return,
		sum(case when a.transaction_type=3 and c.entry_form=8 and a.transaction_date between '" . $previous_date . "' and '" . $previous_date . "' then a.cons_amount else 0 end) as rcv_return_amt,
		sum(case when a.transaction_type=2 and c.issue_purpose=5 and a.transaction_date between '" . $previous_date . "' and '" . $previous_date . "' then a.cons_quantity else 0 end) as issue_loan,
		sum(case when a.transaction_type=2 and c.issue_purpose=5 and a.transaction_date between '" . $previous_date . "' and '" . $previous_date . "' then a.cons_amount else 0 end) as issue_loan_amt,
		0 as issue_adjustment_qty,0 as issue_adjustment_amt
		from inv_transaction a, inv_issue_master c
		where a.mst_id=c.id and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $store_cond group by a.prod_id

		union all 

		select a.prod_id, 0 as store_id,
		0 as issue_total_opening,
		0 as issue_total_opening_rate,
		0 as issue_total_opening_amt,
		0 as issue_inside_amt,
		0 as issue_inside,
		0 as issue_inside_amt,
		0 as issue_outside,
		0 as issue_outside_amt,
		0 as rcv_return,
		0 as rcv_return_amt,
		0 as issue_loan,
		0 as issue_loan_amt,
		sum(case when a.transaction_type in (2,3) and a.receive_basis=30 and a.transaction_date<='" . $previous_date . "' then a.cons_quantity else 0 end) as issue_adjustment_qty,sum(case when a.transaction_type in (2,3) and a.receive_basis=30 and a.transaction_date<='" . $previous_date . "' then a.cons_amount else 0 end) as issue_adjustment_amt
		from inv_transaction a
		where a.item_category=1 and a.status_active=1 and a.is_deleted=0 $store_cond group by a.prod_id
		";
		  //echo $sql_issue;die;
		  
		  
		$result_sql_issue = sql_select($sql_issue);
		foreach ($result_sql_issue as $row)
		{
			$issue_array[$row[csf("prod_id")]]['store_id'] = $row[csf("store_id")];
			$issue_array[$row[csf("prod_id")]]['issue_total_opening'] += $row[csf("issue_total_opening")];
			$issue_array[$row[csf("prod_id")]]['issue_total_opening_amt'] += $row[csf("issue_total_opening_amt")];
			$issue_array[$row[csf("prod_id")]]['issue_total_opening_rate'] = $row[csf("issue_total_opening_rate")];
			$issue_array[$row[csf("prod_id")]]['issue_inside'] += $row[csf("issue_inside")];
			$issue_array[$row[csf("prod_id")]]['issue_inside_amt'] += $row[csf("issue_inside_amt")];
			$issue_array[$row[csf("prod_id")]]['issue_outside'] += $row[csf("issue_outside")];
			$issue_array[$row[csf("prod_id")]]['issue_outside_amt'] += $row[csf("issue_outside_amt")];
			$issue_array[$row[csf("prod_id")]]['rcv_return'] += $row[csf("rcv_return")];
			$issue_array[$row[csf("prod_id")]]['rcv_return_amt'] += $row[csf("rcv_return_amt")];
			$issue_array[$row[csf("prod_id")]]['issue_loan'] += $row[csf("issue_loan")];
			$issue_array[$row[csf("prod_id")]]['issue_loan_amt'] += $row[csf("issue_loan_amt")];
			$issue_array[$row[csf("prod_id")]]['issue_adjustment_qty'] += $row[csf("issue_adjustment_qty")];
			$issue_array[$row[csf("prod_id")]]['issue_adjustment_amt'] += $row[csf("issue_adjustment_amt")];
		}

		unset($result_sql_issue);
		if ($store_wise == 1) {
			$trans_criteria_cond = "";
		} else {
			$trans_criteria_cond = " and c.transfer_criteria=1";
		}
		$transfer_qty_array = array();
		$sql_transfer = "select a.prod_id,
		sum(case when a.transaction_type=6 and a.transaction_date<'" . $previous_date . "' then a.cons_quantity else 0 end) as trans_out_total_opening,
		sum(case when a.transaction_type=6 and a.transaction_date<'" . $previous_date . "' then a.cons_amount else 0 end) as trans_out_total_opening_amt,
		sum(case when a.transaction_type=6 and a.transaction_date<'" . $previous_date . "' then a.cons_rate else 0 end) as trans_out_total_opening_rate,
		sum(case when a.transaction_type=5 and a.transaction_date<'" . $previous_date . "' then a.cons_quantity else 0 end) as trans_in_total_opening,
		sum(case when a.transaction_type=5 and a.transaction_date<'" . $previous_date . "' then a.cons_rate else 0 end) as trans_in_total_opening_rate,
		sum(case when a.transaction_type=5 and a.transaction_date<'" . $previous_date . "' then a.cons_amount else 0 end) as trans_in_total_opening_amt,
		sum(case when a.transaction_type=6 and a.transaction_date between '" . $previous_date . "' and '" . $previous_date . "' then a.cons_quantity else 0 end) as transfer_out_qty,
		sum(case when a.transaction_type=6 and a.transaction_date between '" . $previous_date . "' and '" . $previous_date . "' then a.cons_amount else 0 end) as transfer_out_amt,
		sum(case when a.transaction_type=5 and a.transaction_date between '" . $previous_date . "' and '" . $previous_date . "' then a.cons_quantity else 0 end) as transfer_in_qty,
		sum(case when a.transaction_type=5 and a.transaction_date between '" . $previous_date . "' and '" . $previous_date . "' then a.cons_amount else 0 end) as transfer_in_amt
		from inv_transaction a, inv_item_transfer_mst c where a.mst_id=c.id and a.transaction_type in (5,6) and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and c.status_active=1  and c.is_deleted=0 $trans_criteria_cond $store_cond group by a.prod_id";
		 // echo $sql_transfer;die;
		$result_sql_transfer = sql_select($sql_transfer);
		foreach ($result_sql_transfer as $transRow)
		{
			$transfer_qty_array[$transRow[csf("prod_id")]]['transfer_out_qty'] = $transRow[csf("transfer_out_qty")];
			$transfer_qty_array[$transRow[csf("prod_id")]]['transfer_out_amt'] = $transRow[csf("transfer_out_amt")];
			$transfer_qty_array[$transRow[csf("prod_id")]]['transfer_in_qty'] = $transRow[csf("transfer_in_qty")];
			$transfer_qty_array[$transRow[csf("prod_id")]]['transfer_in_amt'] = $transRow[csf("transfer_in_amt")];
			$transfer_qty_array[$transRow[csf("prod_id")]]['trans_out_total_opening'] = $transRow[csf("trans_out_total_opening")];
			$transfer_qty_array[$transRow[csf("prod_id")]]['trans_out_total_opening_amt'] = $transRow[csf("trans_out_total_opening_amt")];
			$transfer_qty_array[$transRow[csf("prod_id")]]['trans_in_total_opening'] = $transRow[csf("trans_in_total_opening")];
			$transfer_qty_array[$transRow[csf("prod_id")]]['trans_in_total_opening_amt'] = $transRow[csf("trans_in_total_opening_amt")];
			$transfer_qty_array[$transRow[csf("prod_id")]]['trans_in_total_opening_rate'] = $transRow[csf("trans_in_total_opening_rate")];
			$transfer_qty_array[$transRow[csf("prod_id")]]['trans_out_total_opening_rate'] = $transRow[csf("trans_out_total_opening_rate")];
		}

		unset($result_sql_transfer);

 		$sql = "select a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit
		from product_details_master a
		where a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 $company_cond $search_cond group by a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit order by a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.id";
		 //echo $sql;die;
		$result = sql_select($sql);

		?>
		<?
        $tot_stock_value = 0; 
        foreach ($result as $row)
        {

            $transfer_in_qty = $transfer_qty_array[$row[csf("id")]]['transfer_in_qty'];
            $transfer_out_qty = $transfer_qty_array[$row[csf("id")]]['transfer_out_qty'];

            $transfer_in_amt = $transfer_qty_array[$row[csf("id")]]['transfer_in_amt'];
            $transfer_out_amt = $transfer_qty_array[$row[csf("id")]]['transfer_out_amt'];


            $trans_out_total_opening = $transfer_qty_array[$row[csf("id")]]['trans_out_total_opening'];
            $trans_in_total_opening = $transfer_qty_array[$row[csf("id")]]['trans_in_total_opening'];

            $trans_in_total_opening_amt = $transfer_qty_array[$row[csf("id")]]['trans_in_total_opening_amt'];
            $trans_out_total_opening_amt = $transfer_qty_array[$row[csf("id")]]['trans_out_total_opening_amt'];

            $pay_mode = $receive_array[$row[csf("company_id")]][$row[csf("id")]]['pay_mode'];
            $receive_basis = $receive_array[$row[csf("company_id")]][$row[csf("id")]]['receive_basis'];

            $openingBalance = ($receive_array[$row[csf("company_id")]][$row[csf("id")]]['rcv_total_opening'] + $trans_in_total_opening) - ($issue_array[$row[csf("id")]]['issue_total_opening'] + $trans_out_total_opening);

            $openingBalanceAmt = ($receive_array[$row[csf("company_id")]][$row[csf("id")]]['rcv_total_opening_amt'] + $trans_in_total_opening_amt) - ($issue_array[$row[csf("id")]]['issue_total_opening_amt'] + $trans_out_total_opening_amt);

            $openingBalanceAmt = ($receive_array[$row[csf("company_id")]][$row[csf("id")]]['rcv_total_opening_amt']  - $issue_array[$row[csf("id")]]['issue_total_opening_amt']);

            $totalRcv = $receive_array[$row[csf("company_id")]][$row[csf("id")]]['purchase'] + $receive_array[$row[csf("company_id")]][$row[csf("id")]]['rcv_inside_return'] + $receive_array[$row[csf("company_id")]][$row[csf("id")]]['rcv_outside_return'] + $receive_array[$row[csf("company_id")]][$row[csf("id")]]['rcv_loan'] + $receive_array[$row[csf("company_id")]][$row[csf("id")]]['rcv_adjustment_qty'] + $transfer_in_qty;
            
            $totalIssue = $issue_array[$row[csf("id")]]['issue_inside'] + $issue_array[$row[csf("id")]]['issue_outside'] + $issue_array[$row[csf("id")]]['rcv_return'] + $issue_array[$row[csf("id")]]['issue_loan'] + $issue_array[$row[csf("id")]]['issue_adjustment_qty'] + $transfer_out_qty;

            $totalRcvAmt = $receive_array[$row[csf("company_id")]][$row[csf("id")]]['purchase_amt'] + $receive_array[$row[csf("company_id")]][$row[csf("id")]]['rcv_inside_return_amt'] + $receive_array[$row[csf("company_id")]][$row[csf("id")]]['rcv_outside_return_amt'] + $receive_array[$row[csf("company_id")]][$row[csf("id")]]['rcv_loan_amt'] + $receive_array[$row[csf("company_id")]][$row[csf("id")]]['rcv_adjustment_amt'] + $transfer_in_amt;

            $totalIssueAmt = $issue_array[$row[csf("id")]]['issue_inside_amt'] + $issue_array[$row[csf("id")]]['issue_outside_amt'] + $issue_array[$row[csf("id")]]['rcv_return_amt'] + $issue_array[$row[csf("id")]]['issue_loan_amt'] +$issue_array[$row[csf("id")]]['issue_adjustment_amt'] + $transfer_out_qty;


            $stockInHand = $openingBalance + $totalRcv - $totalIssue;
            //$totalQty+=$stockInHand;

            $stockInHandAmt = $openingBalanceAmt + $totalRcvAmt - $totalIssueAmt;
            
                
            if (number_format($stockInHand, 2) > 0.00)
            {
                    $stock_value = 0;
                    $avg_rate = ($stockInHandAmt/$stockInHand);
                    if($avg_rate>0)
                    {
                        $avg_rate=$avg_rate;	
                    }else{
                        $avg_rate = "0.00";
                    }
                    $stock_value = $stockInHand * $avg_rate;
            

                    $yarn_stock_val_arr[$row[csf("company_id")]] += $stock_value;
                    $yarn_stock_qty_arr[$row[csf("company_id")]] += $stockInHand;
					$stock_val_com_arr[$row[csf("company_id")]]=$row[csf("company_id")];
                    
            }
        }
                

	
					
			
//Greay Febric Stock Qty.............................................................start;
	
$data_array=array();
	$trn_sql="Select a.company_id,b.id,
		sum(case when a.transaction_type=1 and a.transaction_date<'".$previous_date."' then a.cons_quantity else 0 end) as rcv_total_opening,
		sum(case when a.transaction_type=2 and a.transaction_date<'".$previous_date."' then a.cons_quantity else 0 end) as iss_total_opening,
		sum(case when a.transaction_type=3 and a.transaction_date<'".$previous_date."' then a.cons_quantity else 0 end) as rcv_return_opening,
		sum(case when a.transaction_type=4 and a.transaction_date<'".$previous_date."' then a.cons_quantity else 0 end) as iss_return_opening,
                sum(case when a.transaction_type=5 and a.transaction_date<'".$previous_date."' then a.cons_quantity else 0 end) as transfer_in_opening,
                sum(case when a.transaction_type=6 and a.transaction_date<'".$previous_date."' then a.cons_quantity else 0 end) as transfer_out_opening,
                sum(case when a.transaction_type in (1,4,5) and a.transaction_date<'".$previous_date."' then a.cons_amount else 0 end) as total_rcv_value_opening,
                sum(case when a.transaction_type in (2,3,6) and a.transaction_date<'".$previous_date."' then a.cons_amount else 0 end) as total_issue_value_opening,    
		sum(case when a.transaction_type=1 and a.transaction_date between '".$previous_date."' and '".$previous_date."' then a.cons_quantity else 0 end) as receive,
		sum(case when a.transaction_type=2 and a.transaction_date between '".$previous_date."' and '".$previous_date."' then a.cons_quantity else 0 end) as issue,
		sum(case when a.transaction_type=3 and a.transaction_date between '".$previous_date."' and '".$previous_date."' then a.cons_quantity else 0 end) as rec_return,
		sum(case when a.transaction_type=4 and a.transaction_date between '".$previous_date."' and '".$previous_date."' then a.cons_quantity else 0 end) as issue_return,
		sum(case when a.transaction_type=5 and a.transaction_date between '".$previous_date."' and '".$previous_date."' then a.cons_quantity else 0 end) as transfer_in,
		sum(case when a.transaction_type=6 and a.transaction_date between '".$previous_date."' and '".$previous_date."' then a.cons_quantity else 0 end) as transfer_out,
                sum(case when a.transaction_type in (1,4,5) and a.transaction_date between '".$previous_date."' and '".$previous_date."' then a.cons_amount else 0 end) as total_rcv_value,
                sum(case when a.transaction_type in (2,3,6) and a.transaction_date between '".$previous_date."' and '".$previous_date."' then a.cons_amount else 0 end) as total_issue_value    
		from inv_transaction a, product_details_master b
		where a.prod_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.order_id=0  and a.item_category=13 group by a.company_id,b.id order by b.id ASC";	
		
		//echo $trn_sql;
	$trnasactionData=sql_select($trn_sql);
	foreach($trnasactionData as $row)
	{
		$data_array[$row[csf("company_id")]][$row[csf("id")]]['rcv_total_opening']=$row[csf("rcv_total_opening")];
		$data_array[$row[csf("company_id")]][$row[csf("id")]]['iss_total_opening']=$row[csf("iss_total_opening")];
		$data_array[$row[csf("company_id")]][$row[csf("id")]]['rcv_return_opening']=$row[csf("rcv_return_opening")];
		$data_array[$row[csf("company_id")]][$row[csf("id")]]['iss_return_opening']=$row[csf("iss_return_opening")];
		$data_array[$row[csf("company_id")]][$row[csf("id")]]['receive']=$row[csf("receive")];
		$data_array[$row[csf("company_id")]][$row[csf("id")]]['issue']=$row[csf("issue")];
		$data_array[$row[csf("company_id")]][$row[csf("id")]]['rec_return']=$row[csf("rec_return")];
		$data_array[$row[csf("company_id")]][$row[csf("id")]]['issue_return']=$row[csf("issue_return")];
		$data_array[$row[csf("company_id")]][$row[csf("id")]]['transfer_in']=$row[csf("transfer_in")];
		$data_array[$row[csf("company_id")]][$row[csf("id")]]['transfer_out']=$row[csf("transfer_out")];
		$data_array[$row[csf("company_id")]][$row[csf("id")]]['transfer_in_opening']=$row[csf("transfer_in_opening")];
		$data_array[$row[csf("company_id")]][$row[csf("id")]]['transfer_out_opening']=$row[csf("transfer_out_opening")];
		$data_array[$row[csf("company_id")]][$row[csf("id")]]['total_rcv_value_opening']=$row[csf("total_rcv_value_opening")];
		$data_array[$row[csf("company_id")]][$row[csf("id")]]['total_issue_value_opening']=$row[csf("total_issue_value_opening")];
		$data_array[$row[csf("company_id")]][$row[csf("id")]]['total_rcv_value']=$row[csf("total_rcv_value")];
		$data_array[$row[csf("company_id")]][$row[csf("id")]]['total_issue_value']=$row[csf("total_issue_value")];
                
	}



$sql="select id, detarmination_id, gsm, dia_width, current_stock,company_id from product_details_master a where status_active=1 and is_deleted=0 and item_category_id=13 order by id"; 	
	$result = sql_select($sql);
	foreach($result as $row)
	{
		$opening_rcv= ($data_array[$row[csf("company_id")]][$row[csf("id")]]['rcv_total_opening']+$data_array[$row[csf("company_id")]][$row[csf("id")]]['iss_return_opening']+$data_array[$row[csf("company_id")]][$row[csf("id")]]['transfer_in_opening']);
		$opening_issue = $data_array[$row[csf("company_id")]][$row[csf("id")]]['iss_total_opening']+$data_array[$row[csf("company_id")]][$row[csf("id")]]['rcv_return_opening']+$data_array[$row[csf("company_id")]][$row[csf("id")]]['transfer_out_opening'];
		$opening= $opening_rcv - $opening_issue;
		
		$opening_rate = $total_value_opening = 0;
		if($opening_rcv > 0){
		$opening_rate = $data_array[$row[csf("company_id")]][$row[csf("id")]]['total_rcv_value_opening']/ $opening_rcv;
		}
		$total_value_opening = $opening *$opening_rate;
		$receive = $data_array[$row[csf("company_id")]][$row[csf("id")]]['receive'];
		$issue_return = $data_array[$row[csf("company_id")]][$row[csf("id")]]['issue_return'];
		$transfer_in = $data_array[$row[csf("company_id")]][$row[csf("id")]]['transfer_in'];
		$totalReceive=$receive+$issue_return+$transfer_in;
		$total_rcv_value = $data_array[$row[csf("company_id")]][$row[csf("id")]]['total_rcv_value'];
		$issue = $data_array[$row[csf("company_id")]][$row[csf("id")]]['issue'];
		$rec_return = $data_array[$row[csf("company_id")]][$row[csf("id")]]['rec_return'];
		$transfer_out = $data_array[$row[csf("company_id")]][$row[csf("id")]]['transfer_out'];
		$totalIssue=$issue+$rec_return+$transfer_out;
		$total_issue_value=$data_array[$row[csf("company_id")]][$row[csf("id")]]['total_issue_value'];
		
		$gray_stock_qty_arr[$row[csf("company_id")]]+=(($opening+$totalReceive)-$totalIssue);
		$stock_val_com_arr[$row[csf("company_id")]]=$row[csf("company_id")];
		
	}
		

//-----------------------------------------------end gray stock;
			
//Dyes/Chemical...........................................
	
/*	$mrr_rate_sql=sql_select("select COMPANY_ID,prod_id, sum(cons_quantity) as cons_quantiy, sum(cons_amount) as cons_amount from inv_transaction where status_active=1 and is_deleted=0 and item_category in(5,6,7,23) and transaction_type in(1,4,5)  group by COMPANY_ID,prod_id");
	$mrr_rate_arr=array();
	foreach($mrr_rate_sql as $row)
	{
		$mrr_rate_arr[$row[csf("COMPANY_ID")]][$row[csf("prod_id")]]=$row[csf("cons_amount")]/$row[csf("cons_quantiy")];
	}
			
*/		
		
	$dyes_chemical_stock_sql="select b.company_id,a.prod_id,
	sum((case when a.transaction_type in (1,4,5) then a.cons_quantity else 0 end)-
	(case when a.transaction_type in (2,3,6) then a.cons_quantity else 0 end)) stock_qty,
	sum((case when a.transaction_type in (1,4,5) then a.cons_amount else 0 end)-
	(case when a.transaction_type in (2,3,6) then a.cons_amount else 0 end)) stock_amount
	
	from inv_transaction a , product_details_master b
	 where  a.prod_id=b.id and a.item_category in(5,6,7,23) and b.item_category_id in (5,6,7,23)  and a.order_id=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
	 group by b.company_id,a.prod_id";
	
	
	$dyesChemicalStockSqlResult = sql_select($dyes_chemical_stock_sql);
		$dyesChemicalStockQtyArr=array();$dyesChemicalStockAmountUsdArr=array();
		foreach ($dyesChemicalStockSqlResult as $row) 
		{
			$dyesChemicalStockQtyArr[$row[csf("company_id")]]+=$row[csf('stock_qty')];
			$dyesChemicalStockAmountUsdArr[$row[csf("company_id")]]+=($row[csf('stock_amount')]);//$conversion_rate
			//$dyesChemicalStockAmountUsdArr[$row[csf("company_id")]]+=($row[csf('stock_qty')]*$mrr_rate_arr[$row[csf("company_id")]][$row[csf("prod_id")]])/$conversion_rate;
			
			$stock_val_com_arr[$row[csf("company_id")]]=$row[csf("company_id")];
		}


//Printing/Chemical...........................................
	$print_chemical_stock_sql="select COMPANY_ID,
	sum((case when a.transaction_type in (1,4,5) then a.cons_quantity else 0 end)-
	(case when a.transaction_type in (2,3,6) then a.cons_quantity else 0 end)) stock_qty,
	sum((case when a.transaction_type in (1,4,5) then a.cons_amount else 0 end)-
	(case when a.transaction_type in (2,3,6) then a.cons_amount else 0 end)) stock_amount
	from inv_transaction a where a.item_category=22 and a.status_active=1 and a.is_deleted=0 group by COMPANY_ID";			
	$printChemicalStockSqlResult = sql_select($print_chemical_stock_sql);
		$printChemicalStockQtyArr=array();$printChemicalStockAmountUsdArr=array();
		foreach ($printChemicalStockSqlResult as $row) 
		{
			$printChemicalStockQtyArr[$row[csf('COMPANY_ID')]]+=$row[csf('stock_qty')];
			$printChemicalStockAmountUsdArr[$row[csf('COMPANY_ID')]]+=($row[csf('stock_amount')]/$conversion_rate);

		}			

?>
<table border="1" rules="all">
    <tr bgcolor="#CCCCCC">
        <th>Stock & Value</th>
        <? foreach($stock_val_com_arr as $company_id=>$value){echo "<th>$company_library[$company_id]</th>";}?>
        <th>Qty/Value</th>
    </tr>
    <tr>
        <td>Yarn Stock (Ton)</td>
        <? foreach($stock_val_com_arr as $company_id=>$value){echo "<td align='right'>".number_format(($yarn_stock_qty_arr[$company_id]/$convToTon),2)."</td>";}?>
        <td align='right'><?= number_format($yarn_stock_qty_tot=(array_sum($yarn_stock_qty_arr)/$convToTon),2);?></td>
    </tr>
    
    
    <tr>
        <td>Yarn Value (USD) Milion</td>
        <? foreach($stock_val_com_arr as $company_id=>$value){echo "<td align='right'>".number_format(($yarn_stock_val_arr[$company_id]/$convToMilion),2)."</td>";}?>
        <td align='right'><?= number_format($yarn_stock_val_tot=(array_sum($yarn_stock_val_arr)/$convToMilion),2);?></td>
    </tr>
    
    <tr>
        <td>Grey Stock (Ton)</td>
        <? foreach($stock_val_com_arr as $company_id=>$value){echo "<td align='right'>".number_format(($gray_stock_qty_arr[$company_id]/$convToTon),2)."</td>";}?>
        <td align='right'><?= number_format($gray_stock_qty_tot=(array_sum($gray_stock_qty_arr)/$convToTon),2);?></td>
    </tr>
    
    <tr>
        <td>Dyes/Chemical Stock (Ton)</td>
        <? foreach($stock_val_com_arr as $company_id=>$value){echo "<td align='right'>".($dyesChemicalStockQtyArr[$company_id]/$convToTon)."</td>";}?>
        <td align='right'><?= number_format($dyes_che_qty_tot=(array_sum($dyesChemicalStockQtyArr)/$convToTon),2);?></td>
    </tr>
    <tr>
        <td>Dyes/Chemical Value (USD) Milion</td>
        <? foreach($stock_val_com_arr as $company_id=>$value){echo "<td align='right'>".number_format(($dyesChemicalStockAmountUsdArr[$company_id]/$convToMilion),2)."</td>";}?>
        <td align='right'><?= number_format($dyes_che_val_tot=(array_sum($dyesChemicalStockAmountUsdArr)/$convToMilion),2);?></td>
    </tr>
    
    
    
     <tr>
        <td>Printing Chemical Stock (Ton)</td>
        <? foreach($stock_val_com_arr as $company_id=>$value){echo "<td align='right'>".number_format(($printChemicalStockQtyArr[$company_id]/$convToTon),2)."</td>";}?>
        <td align='right'><?= number_format($print_che_qty_tot=(array_sum($printChemicalStockQtyArr)/$convToTon),2);?></td>
    </tr>
    <tr>
        <td>Printing Chemical Value (USD) Milion</td>
        <? foreach($stock_val_com_arr as $company_id=>$value){echo "<td align='right'>".number_format(($printChemicalStockAmountUsdArr[$company_id]/$convToMilion),2)."</td>";}?>
        <td align='right'><?= number_format($print_che_val_tot=(array_sum($printChemicalStockAmountUsdArr)/$convToMilion),2);?></td>
    </tr>
    
    <tr>
        <td>Total Ya+DC+PC Value (USD) Milion</td>
        <? foreach($stock_val_com_arr as $company_id=>$value){echo "<td align='right'>"
		.number_format($ya_dc_pc_val_tot=(($yarn_stock_val_arr[$company_id]+$dyesChemicalStockAmountUsdArr[$company_id]+$printChemicalStockAmountUsdArr[$company_id])/$convToMilion),2).
		"</td>";
		
		$grand_ya_dc_pc_val_tot+=$ya_dc_pc_val_tot;
		}?>
        <td align='right'><?= number_format($grand_ya_dc_pc_val_tot,2);?></td>
    </tr>
    
</table><br />


<?

//Machine....................................................................

$machine_sql= "select COMPANY_ID,id,category_id,machine_group,dia_width,gauge from lib_machine_name where category_id in (1,2) and is_deleted = 0 and status_active = 1";
$machine_sql_result=sql_select($machine_sql);
foreach($machine_sql_result as $row)
{
	//if($row[csf(machine_group)] and $row[csf(machine_group)] and $row[csf(gauge)] and $row[csf(category_id)]==1){
	if($row[csf(category_id)]==1){
		$machineIdArr[$row[COMPANY_ID]][$row[csf(category_id)]][$row[csf(id)]]=$row[csf(id)];
	}
	else if($row[csf(category_id)]==2)
	{
		$machineIdArr[$row[COMPANY_ID]][$row[csf(category_id)]][$row[csf(id)]]=$row[csf(id)];
	}
	if($row[COMPANY_ID]>0){$machine_company_arr[$row[COMPANY_ID]]=$row[COMPANY_ID];}
	

}
//1=kniting;2=Dyeing;
	   
	/*$sql="select a.KNITTING_COMPANY as COMPANY_ID,b.machine_no_id, 1 as active_machie_type from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and a.entry_form=2 and a.item_category=13 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.machine_no_id>0
	 and a.receive_date = '".$previous_date."'  group by a.KNITTING_COMPANY,b.machine_no_id
	 
	 union all
	 select a.KNITTING_COMPANY as COMPANY_ID,b.machine_id AS machine_no_id, 1 as active_machie_type  FROM subcon_production_mst a, subcon_production_dtls b where a.id = b.mst_id AND a.product_type = 2 AND a.status_active = 1 AND a.is_deleted = 0 AND a.product_date  = '".$previous_date."'  group by a.KNITTING_COMPANY,b.machine_id
	
	union all
	select a.WORKING_COMPANY_ID as COMPANY_ID,f.machine_id as machine_no_id, 2 as active_machie_type  from pro_batch_create_dtls b, fabric_sales_order_mst d, pro_fab_subprocess f, pro_batch_create_mst a where f.batch_id=a.id and a.working_company_id=1 and f.process_end_date = '".$previous_date."'   and f.service_source in(1) and a.entry_form=0 and a.id=b.mst_id and f.batch_id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and a.batch_against in(1,11,2,3) and b.po_id=d.id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and a.is_sales=1 and b.is_sales=1 group by  a.WORKING_COMPANY_ID,f.machine_id  
	";	   
//echo $sql;	   
$sql_result=sql_select($sql);
foreach($sql_result as $row)
{
	if($row[csf(active_machie_type)]==1){$active_kniting_machine_id_arr[$row[COMPANY_ID]][$row[csf(machine_no_id)]]=$row[csf(machine_no_id)];}
	if($row[csf(active_machie_type)]==2){$active_dyeing_machine_id_arr[$row[COMPANY_ID]][$row[csf(machine_no_id)]]=$row[csf(machine_no_id)];}
	
	if($row[COMPANY_ID]){$machine_company_arr[$row[COMPANY_ID]]=$row[COMPANY_ID];}

}*/

	$receive_date= " and a.receive_date between '".$previous_date."' and '".$previous_date."'";
	$receive_date_sub= " and a.product_date between '".$previous_date."' and '".$previous_date."'";
	$sql_result="Select a.KNITTING_COMPANY as COMPANY_ID, b.machine_no_id as MACHINE_ID, 1 as ACTIVE_MACHINE_TYPE from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and a.entry_form=2 and a.item_category=13 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.machine_no_id!=0 $receive_date 
	group by a.KNITTING_COMPANY,b.machine_no_id
	union all
	
	Select a.KNITTING_COMPANY as COMPANY_ID, b.machine_id as MACHINE_ID, 1 as ACTIVE_MACHINE_TYPE from  subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id and a.product_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.machine_id!=0 $receive_date_sub 
	group by a.KNITTING_COMPANY,b.machine_id
	
	union all
	SELECT a.service_company as COMPANY_ID, a.machine_id as MACHINE_ID, 2 as ACTIVE_MACHINE_TYPE
    FROM pro_fab_subprocess a, pro_batch_create_mst b, pro_batch_create_dtls c
   WHERE     a.batch_id = b.id
         AND a.entry_form = 35
         AND a.load_unload_id = 2
         AND b.id = c.mst_id
         AND a.status_active = 1
         AND a.is_deleted = 0
         AND b.status_active = 1
         AND b.is_deleted = 0
         AND c.status_active = 1
         AND c.is_deleted = 0
         AND b.batch_against IN (1, 2)
         AND b.is_sales != 1
         AND a.process_end_date BETWEEN '".$previous_date."' AND '".$previous_date."'
GROUP BY a.service_company, a.machine_id 
	";
	
	
	//select a.WORKING_COMPANY_ID as COMPANY_ID,f.machine_id as MACHINE_ID, 2 as ACTIVE_MACHINE_TYPE  from pro_batch_create_dtls b, fabric_sales_order_mst d, pro_fab_subprocess f, pro_batch_create_mst a where f.batch_id=a.id and a.working_company_id=1 and f.process_end_date = '".$previous_date."'   and f.service_source in(1) and a.entry_form=0 and a.id=b.mst_id and f.batch_id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and a.batch_against in(1,11,2,3) and b.po_id=d.id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and a.is_sales=1 and b.is_sales=1 group by  a.WORKING_COMPANY_ID,f.machine_id  
	
	
	 //echo $sql_result; 
	$sql_dtls=sql_select($sql_result);
	$date_data_arr=array();
	$date_total_arr=array();
	foreach ($sql_dtls as $row)
	{
		if($row[ACTIVE_MACHINE_TYPE]==1){$active_kniting_machine_id_arr[$row[COMPANY_ID]][$row[MACHINE_ID]]=$row[MACHINE_ID];}
		else if($row[ACTIVE_MACHINE_TYPE]==2){$active_dyeing_machine_id_arr[$row[COMPANY_ID]][$row[MACHINE_ID]]=$row[MACHINE_ID];}
		
		if($row[COMPANY_ID]){$machine_company_arr[$row[COMPANY_ID]]=$row[COMPANY_ID];}

	}

	
	
?>

<table cellspacing="0" border="1" rules="all" width="243">
    <tr bgcolor="#CCCCCC">
        <th rowspan="2">Dept</th>
         <? foreach($machine_company_arr as $company_id){?>
        <th colspan="3"><?=$company_library[$company_id];?> M/C</th>
        <? } ?>
    </tr>		
    <tr>
        <? foreach($machine_company_arr as $company_id){?>
        <th>Total</th>
        <th>Idle</th>
        <th>%</th>
        <? } ?>
    </tr>
    <tr>
        <td>Knitting</td>
        <? foreach($machine_company_arr as $company_id){?>
        <td align="right"><? echo count($machineIdArr[$company_id][1]);?></td>
        <td align="right" title="<?= "Total Machine:".count($machineIdArr[$company_id][1])." & Active Machine:".count($active_kniting_machine_id_arr[$company_id]);?>"><? echo $total_idle_kniting_machine=count($machineIdArr[$company_id][1])-count($active_kniting_machine_id_arr[$company_id]);?></td>
        <td align="right"><? echo number_format(($total_idle_kniting_machine*100)/count($machineIdArr[$company_id][1]),2);?></td>
        <? } ?>
    </tr>
    <tr>
        <td>Dyeing</td>
        <? foreach($machine_company_arr as $company_id){?>
        <td align="right"><? echo count($machineIdArr[$company_id][2]);?></td>
        <td align="right"><? echo $total_idle_dyeing_machine=count($machineIdArr[$company_id][2])-count($active_dyeing_machine_id_arr[$company_id]);?></td>
        <td align="right"><? echo number_format(($total_idle_dyeing_machine*100)/count($machineIdArr[$company_id][2]),2);?></td>
        <? } ?>
    </tr>
</table><br />

<?
$actual_res_sql="select A.ID,A.COMPANY_ID from PROD_RESOURCE_MST A ,PROD_RESOURCE_DTLS B WHERE A.ID=B.MST_ID  and b.PR_DATE = '".$previous_date."' ";

$actual_res_sql_result=sql_select($actual_res_sql);
foreach($actual_res_sql_result as $row)
{
	$actual_resource_lin_arr[$row[COMPANY_ID]][$row[ID]]=$row[ID];
	$actual_resource_company_arr[$row[COMPANY_ID]]=$row[COMPANY_ID];
}


?>






<table cellspacing="0" border="1" rules="all" width="243">
    <tr bgcolor="#CCCCCC">
        <th rowspan="2">Dept</th>
         <? foreach($actual_resource_company_arr as $company_id){?>
        <th colspan="3"><?=$company_library[$company_id];?></th>
        <? } ?>
    </tr>		
    <tr>
        <? foreach($actual_resource_company_arr as $company_id){?>
        <th>Total</th>
        <th>Idle</th>
        <th>%</th>
        <? } ?>
    </tr>
   
    <tr>
        <td>Sewing Line </td>
        <? foreach($actual_resource_company_arr as $company_id){?>
        <td align="right"><? echo count($actual_resource_lin_arr[$company_id]);?></td>
        
        <td align="right"><? echo $total_idle_sewing_line=count($actual_resource_lin_arr[$company_id])-count($sewing_line_arr[$company_id]);?></td>
        <td align="right"><? echo number_format(($total_idle_sewing_line*100)/count($sewing_line_arr[$company_id]),2);?></td>
        <? } ?>
    </tr>

</table><br />


<?
	
	$new_conn=integration_params(4);
	$receive_array = array();
		$sql_receive = "Select a.company_id,a.prod_id, 0 as store_id, max(a.weight_per_bag) as weight_per_bag, max(a.weight_per_cone) as weight_per_cone,
			sum(case when a.transaction_type in (1,4) and a.transaction_date<'" . $previous_date . "' then a.cons_quantity else 0 end) as rcv_total_opening,
			sum(case when a.transaction_type in (1,4) and a.transaction_date<'" . $previous_date . "' then a.cons_amount else 0 end) as rcv_total_opening_amt,
                        sum(case when a.transaction_type in (1,4) and a.transaction_date<'" . $previous_date . "' then a.cons_rate else 0 end) as rcv_total_opening_rate,
			sum(case when a.transaction_type in (1) and c.receive_purpose<>5 and a.transaction_date between '" . $previous_date . "' and '" . $previous_date . "' then a.cons_quantity else 0 end) as purchase,
			sum(case when a.transaction_type in (1) and c.receive_purpose<>5 and a.transaction_date between '" . $previous_date . "' and '" . $previous_date . "' then a.cons_amount else 0 end) as purchase_amt,
			sum(case when a.transaction_type in (1) and c.receive_purpose=5 and a.transaction_date between '" . $previous_date . "' and '" . $previous_date . "' then a.cons_quantity else 0 end) as rcv_loan,
			sum(case when a.transaction_type in (1) and c.receive_purpose=5 and a.transaction_date between '" . $previous_date . "' and '" . $previous_date . "' then a.cons_amount else 0 end) as rcv_loan_amt,
			sum(case when a.transaction_type=4 and c.knitting_source=1 and a.transaction_date between '" . $previous_date . "' and '" . $previous_date . "' then a.cons_quantity else 0 end) as rcv_inside_return,
			sum(case when a.transaction_type=4 and c.knitting_source=1 and a.transaction_date between '" . $previous_date . "' and '" . $previous_date . "' then a.cons_amount else 0 end) as rcv_inside_return_amt,
			sum(case when a.transaction_type=4 and c.knitting_source!=1 and a.transaction_date between '" . $previous_date . "' and '" . $previous_date . "' then a.cons_quantity else 0 end) as rcv_outside_return,
			sum(case when a.transaction_type=4 and c.knitting_source!=1 and a.transaction_date between '" . $previous_date . "' and '" . $previous_date . "' then a.cons_amount else 0 end) as rcv_outside_return_amt 
			from inv_transaction a, inv_receive_master c where a.mst_id=c.id and a.transaction_type in (1,4) and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $company_cond group by a.company_id,a.prod_id";

//echo $sql_receive;die;


		$result_sql_receive = sql_select($sql_receive,'',$new_conn);
		foreach ($result_sql_receive as $row)
		{
			$receive_array[$row[csf("company_id")]][$row[csf("prod_id")]]['store_id'] = $row[csf("store_id")];
			$receive_array[$row[csf("company_id")]][$row[csf("prod_id")]]['pay_mode'] = $row[csf("pay_mode")];
			$receive_array[$row[csf("company_id")]][$row[csf("prod_id")]]['receive_basis'] = $row[csf("receive_basis")]; 
			$receive_array[$row[csf("company_id")]][$row[csf("prod_id")]]['rcv_total_opening'] += $row[csf("rcv_total_opening")];
			$receive_array[$row[csf("company_id")]][$row[csf("prod_id")]]['rcv_total_opening_amt'] += $row[csf("rcv_total_opening_amt")];
			$receive_array[$row[csf("company_id")]][$row[csf("prod_id")]]['purchase'] += $row[csf("purchase")];
			$receive_array[$row[csf("company_id")]][$row[csf("prod_id")]]['purchase_amt'] += $row[csf("purchase_amt")];
			$receive_array[$row[csf("company_id")]][$row[csf("prod_id")]]['rcv_loan'] += $row[csf("rcv_loan")];
			$receive_array[$row[csf("company_id")]][$row[csf("prod_id")]]['rcv_loan_amt'] += $row[csf("rcv_loan_amt")];
			$receive_array[$row[csf("company_id")]][$row[csf("prod_id")]]['rcv_inside_return'] += $row[csf("rcv_inside_return")];
			$receive_array[$row[csf("company_id")]][$row[csf("prod_id")]]['rcv_inside_return_amt'] += $row[csf("rcv_inside_return_amt")];
			$receive_array[$row[csf("company_id")]][$row[csf("prod_id")]]['rcv_outside_return'] += $row[csf("rcv_outside_return")];
			$receive_array[$row[csf("company_id")]][$row[csf("prod_id")]]['rcv_outside_return_amt'] += $row[csf("rcv_outside_return_amt")];
			$receive_array[$row[csf("company_id")]][$row[csf("prod_id")]]['weight_per_bag'] = $row[csf("weight_per_bag")];
			$receive_array[$row[csf("company_id")]][$row[csf("prod_id")]]['weight_per_cone'] = $row[csf("weight_per_cone")];
			$receive_array[$row[csf("company_id")]][$row[csf("prod_id")]]['rcv_total_opening_rate'] = $row[csf("rcv_total_opening_rate")];
			$receive_array[$row[csf("company_id")]][$row[csf("prod_id")]]['rcv_adjustment_qty'] += $row[csf("rcv_adjustment_qty")];
			$receive_array[$row[csf("company_id")]][$row[csf("prod_id")]]['rcv_adjustment_amt'] += $row[csf("rcv_adjustment_amt")];
		}

		unset($result_sql_receive);

		$issue_array = array();
					$sql_issue = "select a.prod_id, 0 as store_id,
		sum(case when a.transaction_type in (2,3) and a.transaction_date<'" . $previous_date . "' then a.cons_quantity else 0 end) as issue_total_opening,
		sum(case when a.transaction_type in (2,3) and a.transaction_date<'" . $previous_date . "' then a.cons_rate else 0 end) as issue_total_opening_rate,
		sum(case when a.transaction_type in (2,3) and a.transaction_date<'" . $previous_date . "' then a.cons_amount else 0 end) as issue_total_opening_amt,
		sum(case when a.transaction_type=2 and c.knit_dye_source=1 and c.issue_purpose<>5 and a.transaction_date  between '" . $previous_date . "' and '" . $previous_date . "' then a.cons_amount else 0 end) as issue_inside_amt,
		sum(case when a.transaction_type=2 and c.knit_dye_source=1 and c.issue_purpose<>5 and a.transaction_date  between '" . $previous_date . "' and '" . $previous_date . "' then a.cons_quantity else 0 end) as issue_inside,
		sum(case when a.transaction_type=2 and c.knit_dye_source=1 and c.issue_purpose<>5 and a.transaction_date  between '" . $previous_date . "' and '" . $previous_date . "' then a.cons_amount else 0 end) as issue_inside_amt,
		sum(case when a.transaction_type=2 and c.knit_dye_source!=1 and c.issue_purpose<>5 and a.transaction_date  between '" . $previous_date . "' and '" . $previous_date . "' then a.cons_quantity else 0 end) as issue_outside,
		sum(case when a.transaction_type=2 and c.knit_dye_source!=1 and c.issue_purpose<>5 and a.transaction_date  between '" . $previous_date . "' and '" . $previous_date . "' then a.cons_amount else 0 end) as issue_outside_amt,
		sum(case when a.transaction_type=3 and c.entry_form=8 and a.transaction_date between '" . $previous_date . "' and '" . $previous_date . "' then a.cons_quantity else 0 end) as rcv_return,
		sum(case when a.transaction_type=3 and c.entry_form=8 and a.transaction_date between '" . $previous_date . "' and '" . $previous_date . "' then a.cons_amount else 0 end) as rcv_return_amt,
		sum(case when a.transaction_type=2 and c.issue_purpose=5 and a.transaction_date between '" . $previous_date . "' and '" . $previous_date . "' then a.cons_quantity else 0 end) as issue_loan,
		sum(case when a.transaction_type=2 and c.issue_purpose=5 and a.transaction_date between '" . $previous_date . "' and '" . $previous_date . "' then a.cons_amount else 0 end) as issue_loan_amt,
		0 as issue_adjustment_qty,0 as issue_adjustment_amt
		from inv_transaction a, inv_issue_master c
		where a.mst_id=c.id and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $store_cond group by a.prod_id

		union all 

		select a.prod_id, 0 as store_id,
		0 as issue_total_opening,
		0 as issue_total_opening_rate,
		0 as issue_total_opening_amt,
		0 as issue_inside_amt,
		0 as issue_inside,
		0 as issue_inside_amt,
		0 as issue_outside,
		0 as issue_outside_amt,
		0 as rcv_return,
		0 as rcv_return_amt,
		0 as issue_loan,
		0 as issue_loan_amt,
		sum(case when a.transaction_type in (2,3) and a.receive_basis=30 and a.transaction_date<='" . $previous_date . "' then a.cons_quantity else 0 end) as issue_adjustment_qty,sum(case when a.transaction_type in (2,3) and a.receive_basis=30 and a.transaction_date<='" . $previous_date . "' then a.cons_amount else 0 end) as issue_adjustment_amt
		from inv_transaction a
		where a.item_category=1 and a.status_active=1 and a.is_deleted=0 $store_cond group by a.prod_id
		";
		  //echo $sql_issue;die;
		  
		  
		$result_sql_issue = sql_select($sql_issue,'',$new_conn);
		foreach ($result_sql_issue as $row)
		{
			$issue_array[$row[csf("prod_id")]]['store_id'] = $row[csf("store_id")];
			$issue_array[$row[csf("prod_id")]]['issue_total_opening'] += $row[csf("issue_total_opening")];
			$issue_array[$row[csf("prod_id")]]['issue_total_opening_amt'] += $row[csf("issue_total_opening_amt")];
			$issue_array[$row[csf("prod_id")]]['issue_total_opening_rate'] = $row[csf("issue_total_opening_rate")];
			$issue_array[$row[csf("prod_id")]]['issue_inside'] += $row[csf("issue_inside")];
			$issue_array[$row[csf("prod_id")]]['issue_inside_amt'] += $row[csf("issue_inside_amt")];
			$issue_array[$row[csf("prod_id")]]['issue_outside'] += $row[csf("issue_outside")];
			$issue_array[$row[csf("prod_id")]]['issue_outside_amt'] += $row[csf("issue_outside_amt")];
			$issue_array[$row[csf("prod_id")]]['rcv_return'] += $row[csf("rcv_return")];
			$issue_array[$row[csf("prod_id")]]['rcv_return_amt'] += $row[csf("rcv_return_amt")];
			$issue_array[$row[csf("prod_id")]]['issue_loan'] += $row[csf("issue_loan")];
			$issue_array[$row[csf("prod_id")]]['issue_loan_amt'] += $row[csf("issue_loan_amt")];
			$issue_array[$row[csf("prod_id")]]['issue_adjustment_qty'] += $row[csf("issue_adjustment_qty")];
			$issue_array[$row[csf("prod_id")]]['issue_adjustment_amt'] += $row[csf("issue_adjustment_amt")];
		}

		unset($result_sql_issue);
		if ($store_wise == 1) {
			$trans_criteria_cond = "";
		} else {
			$trans_criteria_cond = " and c.transfer_criteria=1";
		}
		$transfer_qty_array = array();
		$sql_transfer = "select a.prod_id,
		sum(case when a.transaction_type=6 and a.transaction_date<'" . $previous_date . "' then a.cons_quantity else 0 end) as trans_out_total_opening,
		sum(case when a.transaction_type=6 and a.transaction_date<'" . $previous_date . "' then a.cons_amount else 0 end) as trans_out_total_opening_amt,
		sum(case when a.transaction_type=6 and a.transaction_date<'" . $previous_date . "' then a.cons_rate else 0 end) as trans_out_total_opening_rate,
		sum(case when a.transaction_type=5 and a.transaction_date<'" . $previous_date . "' then a.cons_quantity else 0 end) as trans_in_total_opening,
		sum(case when a.transaction_type=5 and a.transaction_date<'" . $previous_date . "' then a.cons_rate else 0 end) as trans_in_total_opening_rate,
		sum(case when a.transaction_type=5 and a.transaction_date<'" . $previous_date . "' then a.cons_amount else 0 end) as trans_in_total_opening_amt,
		sum(case when a.transaction_type=6 and a.transaction_date between '" . $previous_date . "' and '" . $previous_date . "' then a.cons_quantity else 0 end) as transfer_out_qty,
		sum(case when a.transaction_type=6 and a.transaction_date between '" . $previous_date . "' and '" . $previous_date . "' then a.cons_amount else 0 end) as transfer_out_amt,
		sum(case when a.transaction_type=5 and a.transaction_date between '" . $previous_date . "' and '" . $previous_date . "' then a.cons_quantity else 0 end) as transfer_in_qty,
		sum(case when a.transaction_type=5 and a.transaction_date between '" . $previous_date . "' and '" . $previous_date . "' then a.cons_amount else 0 end) as transfer_in_amt
		from inv_transaction a, inv_item_transfer_mst c where a.mst_id=c.id and a.transaction_type in (5,6) and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and c.status_active=1  and c.is_deleted=0 $trans_criteria_cond $store_cond group by a.prod_id";
		  //echo $sql_transfer;die;
		$result_sql_transfer = sql_select($sql_transfer,'',$new_conn);
		foreach ($result_sql_transfer as $transRow)
		{
			$transfer_qty_array[$transRow[csf("prod_id")]]['transfer_out_qty'] = $transRow[csf("transfer_out_qty")];
			$transfer_qty_array[$transRow[csf("prod_id")]]['transfer_out_amt'] = $transRow[csf("transfer_out_amt")];
			$transfer_qty_array[$transRow[csf("prod_id")]]['transfer_in_qty'] = $transRow[csf("transfer_in_qty")];
			$transfer_qty_array[$transRow[csf("prod_id")]]['transfer_in_amt'] = $transRow[csf("transfer_in_amt")];
			$transfer_qty_array[$transRow[csf("prod_id")]]['trans_out_total_opening'] = $transRow[csf("trans_out_total_opening")];
			$transfer_qty_array[$transRow[csf("prod_id")]]['trans_out_total_opening_amt'] = $transRow[csf("trans_out_total_opening_amt")];
			$transfer_qty_array[$transRow[csf("prod_id")]]['trans_in_total_opening'] = $transRow[csf("trans_in_total_opening")];
			$transfer_qty_array[$transRow[csf("prod_id")]]['trans_in_total_opening_amt'] = $transRow[csf("trans_in_total_opening_amt")];
			$transfer_qty_array[$transRow[csf("prod_id")]]['trans_in_total_opening_rate'] = $transRow[csf("trans_in_total_opening_rate")];
			$transfer_qty_array[$transRow[csf("prod_id")]]['trans_out_total_opening_rate'] = $transRow[csf("trans_out_total_opening_rate")];
		}

		unset($result_sql_transfer);

         $sql = "select a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit 
				from product_details_master a
				where a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 $company_cond $search_cond group by a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit order by a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.id";
		  //echo $sql;die;
		$result = sql_select($sql,'',$new_conn);

		?>
		<?
        $tot_stock_value = 0; 
        foreach ($result as $row)
        {

            $transfer_in_qty = $transfer_qty_array[$row[csf("id")]]['transfer_in_qty'];
            $transfer_out_qty = $transfer_qty_array[$row[csf("id")]]['transfer_out_qty'];

            $transfer_in_amt = $transfer_qty_array[$row[csf("id")]]['transfer_in_amt'];
            $transfer_out_amt = $transfer_qty_array[$row[csf("id")]]['transfer_out_amt'];


            $trans_out_total_opening = $transfer_qty_array[$row[csf("id")]]['trans_out_total_opening'];
            $trans_in_total_opening = $transfer_qty_array[$row[csf("id")]]['trans_in_total_opening'];

            $trans_in_total_opening_amt = $transfer_qty_array[$row[csf("id")]]['trans_in_total_opening_amt'];
            $trans_out_total_opening_amt = $transfer_qty_array[$row[csf("id")]]['trans_out_total_opening_amt'];

            $pay_mode = $receive_array[$row[csf("company_id")]][$row[csf("id")]]['pay_mode'];
            $receive_basis = $receive_array[$row[csf("company_id")]][$row[csf("id")]]['receive_basis'];

            $openingBalance = ($receive_array[$row[csf("company_id")]][$row[csf("id")]]['rcv_total_opening'] + $trans_in_total_opening) - ($issue_array[$row[csf("id")]]['issue_total_opening'] + $trans_out_total_opening);

            $openingBalanceAmt = ($receive_array[$row[csf("company_id")]][$row[csf("id")]]['rcv_total_opening_amt'] + $trans_in_total_opening_amt) - ($issue_array[$row[csf("id")]]['issue_total_opening_amt'] + $trans_out_total_opening_amt);

            $openingBalanceAmt = ($receive_array[$row[csf("company_id")]][$row[csf("id")]]['rcv_total_opening_amt']  - $issue_array[$row[csf("id")]]['issue_total_opening_amt']);

            $totalRcv = $receive_array[$row[csf("company_id")]][$row[csf("id")]]['purchase'] + $receive_array[$row[csf("company_id")]][$row[csf("id")]]['rcv_inside_return'] + $receive_array[$row[csf("company_id")]][$row[csf("id")]]['rcv_outside_return'] + $receive_array[$row[csf("company_id")]][$row[csf("id")]]['rcv_loan'] + $receive_array[$row[csf("company_id")]][$row[csf("id")]]['rcv_adjustment_qty'] + $transfer_in_qty;
            
            $totalIssue = $issue_array[$row[csf("id")]]['issue_inside'] + $issue_array[$row[csf("id")]]['issue_outside'] + $issue_array[$row[csf("id")]]['rcv_return'] + $issue_array[$row[csf("id")]]['issue_loan'] + $issue_array[$row[csf("id")]]['issue_adjustment_qty'] + $transfer_out_qty;

            $totalRcvAmt = $receive_array[$row[csf("company_id")]][$row[csf("id")]]['purchase_amt'] + $receive_array[$row[csf("company_id")]][$row[csf("id")]]['rcv_inside_return_amt'] + $receive_array[$row[csf("company_id")]][$row[csf("id")]]['rcv_outside_return_amt'] + $receive_array[$row[csf("company_id")]][$row[csf("id")]]['rcv_loan_amt'] + $receive_array[$row[csf("company_id")]][$row[csf("id")]]['rcv_adjustment_amt'] + $transfer_in_amt;

            $totalIssueAmt = $issue_array[$row[csf("id")]]['issue_inside_amt'] + $issue_array[$row[csf("id")]]['issue_outside_amt'] + $issue_array[$row[csf("id")]]['rcv_return_amt'] + $issue_array[$row[csf("id")]]['issue_loan_amt'] +$issue_array[$row[csf("id")]]['issue_adjustment_amt'] + $transfer_out_qty;


            $stockInHand = $openingBalance + $totalRcv - $totalIssue;
            //$totalQty+=$stockInHand;

            $stockInHandAmt = $openingBalanceAmt + $totalRcvAmt - $totalIssueAmt;
            
                
            if (number_format($stockInHand, 2) > 0.00)
            {
                    $stock_value = 0;
                    $avg_rate = ($stockInHandAmt/$stockInHand);
                    if($avg_rate>0)
                    {
                        $avg_rate=$avg_rate;	
                    }else{
                        $avg_rate = "0.00";
                    }
                    $stock_value = $stockInHand * $avg_rate;
            

                    $spinning_yarn_stock_val_arr[$row[csf("company_id")]] += $stock_value;
                    $spinning_yarn_stock_qty_arr[$row[csf("company_id")]] += $stockInHand;
                    
            }
        }
                

//------------------Cotton stock
 $sql="Select a.prod_id,b.id,b.item_code,b.item_group_id ,b.btb_lc_id,b.store_id,b.item_category_id,b.item_group_id,b.avg_rate_per_unit,b.sub_group_name,b.product_name_details,b.lot,b.item_size,b.order_uom,
		sum(case when a.transaction_date<'".$previous_date."' and a.transaction_type in (1,4,5)  then a.cons_quantity else 0 end) as opening_total_receive,
		sum(case when a.transaction_date<'".$previous_date."' and a.transaction_type in (2,3,6)  then a.cons_quantity else 0 end) as opening_total_issue,
		sum(case when a.transaction_type=1 and a.transaction_date  between '".$previous_date."' and '".$previous_date."' then a.cons_quantity else 0 end) as purchase,
		sum(case when a.transaction_type=3 and a.transaction_date  between '".$previous_date."' and '".$previous_date."' then a.cons_quantity else 0 end) as receive_return,
		sum(case when a.transaction_type=2 and a.transaction_date  between '".$previous_date."' and '".$previous_date."' then a.cons_quantity else 0 end) as issue,
		sum(case when a.transaction_type=4 and a.transaction_date  between '".$previous_date."' and '".$previous_date."' then a.cons_quantity else 0 end) as issue_return, 
		sum(case when a.transaction_type=5 and a.transaction_date  between '".$previous_date."' and '".$previous_date."' then a.cons_quantity else 0 end) as transfer_receive,
		sum(case when a.transaction_type=6 and a.transaction_date  between '".$previous_date."' and '".$previous_date."' then a.cons_quantity else 0 end) as transfer_issue 
		
		from inv_transaction a, product_details_master b 
		where a.prod_id=b.id and a.transaction_type in (1,2,3,4,5,6)  and a.item_category in (42,43) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $str_cond $search_string $lc_ids 
		group by a.prod_id,b.id,b.store_id,b.item_category_id,b.item_group_id,b.avg_rate_per_unit,b.sub_group_name,b.btb_lc_id,b.product_name_details,b.lot,b.item_size,b.order_uom,b.item_code order by b.item_category_id";
	 //echo  $sql; die; 
	$result = sql_select($sql,'',$new_conn);
	
	$totalClosingStockArr=array();
	foreach( $result as $row )
	{
		$openingBalance = $row[csf("opening_total_receive")]-$row[csf("opening_total_issue")];
		$totalReceive = $row[csf("purchase")]+$row[csf("issue_return")]+$row[csf("transfer_receive")];
		$totalIssue =$row[csf("issue")]+$row[csf("receive_return")]+$row[csf("transfer_issue")];
		$closingStock=$totalReceive-$totalIssue+$openingBalance;
		$totalClosingStockArr[$row[csf("item_category_id")]]+=$closingStock;
	}
	
	
	
	$yarn_production_qty_arr=array();
	$spining_rec_sql="select a.company_id as COMPANY_ID, sum(a.cons_quantity) as REC_QTY   from inv_transaction a, product_details_master b where a.prod_id=b.id and a.item_category=1 and a.transaction_type=1 and a.cons_quantity>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.transaction_date = '".$previous_date."' group by a.company_id";
	//echo $spining_rec_sql;
	
	$spining_rec_sql_result=sql_select($spining_rec_sql);
	foreach($spining_rec_sql_result as $row)
	{
		$yarn_production_qty_arr[$row[COMPANY_ID]]=$row[REC_QTY];
	}
	
	

?>

 


<table cellspacing="0" border="1" rules="all" width="243">
    <tr bgcolor="#CCCCCC">
        <th colspan="4">Sungarh Spinning</th>
    </tr>		
    <tr>
        <th>Yarn Production (Ton)</th>
        <th>Cotton Stock (Ton)</th>
        <th>Synthetic Fibre Stock(Ton)</th>
        <th>Yarn Stock (Ton)</th>
    </tr>
   
    <tr>
        <td align="right"><?= number_format(($yarn_production_qty_arr[1]/$convToTon),2);?></td>
        <td align="right"><?= number_format(($totalClosingStockArr[42]/$convByLBS),2);?></td>
        <td align="right"><?= number_format(($totalClosingStockArr[43]/$convToTon),2);?></td>
        <td align="right"><?= number_format(($spinning_yarn_stock_qty_arr[1]/$convToTon),2);?></td>
    </tr>

</table>

<?
	$emailBody=ob_get_contents();
	ob_clean();
	$to='';
	$sql = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=61 AND a.MAIL_TYPE=1 and b.mail_user_setup_id=c.id";
	$mail_sql=sql_select($sql);
	$mailArr=array();
	foreach($mail_sql as $row)
	{
		$mailArr[$row[csf('email_address')]]=$row[csf('email_address')]; 
	}
	$to=implode(',',$mailArr);
	$subject="Daily ERP Report";
	$header=mailHeader();

	if($_REQUEST['isview']==1){
		$mail_item=61;
		if($to){
			echo 'Mail Item:'.$form_list_for_mail[$mail_item].'=>'.$to;
		}else{
			echo "Mail address not set. [Please set mail from  Mail Recipient Group, Mail Item: <b>".$form_list_for_mail[$mail_item]."</b>]<br>";
		}
		echo $emailBody;
	}
	else{
		echo sendMailMailer( $to, $subject, $emailBody, $from_mail );
	}

	
	//echo $emailBody;

?>



















