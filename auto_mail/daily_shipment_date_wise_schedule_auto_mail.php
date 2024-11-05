<?php
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Daily Ex-factory Schedule auto mail
Functionality	:	
JS Functions	:
Created by		:	Shajib Jaman
Creation date 	: 	20-07-2022
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/


date_default_timezone_set("Asia/Dhaka");
require_once('../includes/common.php');
// require_once('../mailer/class.phpmailer.php');
require_once('setting/mail_setting.php');

$company_library=return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0 and CORE_BUSINESS=1 ","id","company_name");
$buyer_library=return_library_array("select id,short_name from lib_buyer","id","short_name");

$commission_percent_arr=return_library_array("select job_no,commission_percent from wo_pre_cost_dtls","job_no","commission_percent");
$commission_per_arr=return_library_array("select quotation_id,commission_percent from wo_price_quotation_costing_mst","quotation_id","commission_percent");

$is_all_company=1;

// print_r($commission_percent_arr);
// $supplier_library = return_library_array("select id,team_member_name from lib_mkt_team_member_info where status_active=1 and is_deleted=0","id","team_member_name");

$team_leader=return_library_array("select id,team_leader_name from lib_marketing_team where project_type=2 and status_active =1 and is_deleted=0 order by team_leader_name","id","team_leader_name");
$dealing_merchand=return_library_array("select id,team_member_name from lib_mkt_team_member_info where  status_active =1 and is_deleted=0 order by team_member_name","id","team_member_name");

$factory_merchand=return_library_array("select a.id, a.team_member_name from lib_mkt_team_member_info a, lib_marketing_team b where a.team_id=b.id and b.team_type in (2) and a.status_active =1 and a.is_deleted=0 order by a.team_member_name","id","team_member_name");

$strtotime = ($_REQUEST['view_date'])?strtotime($_REQUEST['view_date']):time();
	
$current_date = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("H:i:s", $strtotime),0))),'','',1);	
$next_date = change_date_format(date('Y-m-d H:i:s', strtotime('+1 day', strtotime($current_date))),'','',1); 	
$date_cond	=" and a.insert_date between '".$previous_date."' and '".$current_date." 11:59:59 PM'";


// $current_date=$next_date='29-Dec-2021';
// $current_date='30-Jan-2022';
// $next_date='31-Mar-2022';



 foreach($company_library as $compid=>$compname)
 {
?>

<?
	ob_start();	


	// $sql="select a.buyer_name,a.job_no,b.id as po_id,b.po_number,b.pub_shipment_date,b.shipment_date,b.po_quantity,b.unit_price,b.po_total_price,a.ship_mode,a.factory_marchant,a.set_break_down,a.order_uom,a.style_ref_no,a.company_name,b.rfi_date from wo_po_details_master a,wo_po_break_down b where   a.job_no=b.job_no_mst and to_date(to_char(b.rfi_date, 'DD-MON-YYYY')) BETWEEN '$current_date' AND '$next_date' and a.company_name=$compid  and b.IS_CONFIRMED=1 and b.shiping_status!=3 and b.status_active=1 and  a.status_active=1 and b.status_active=1 order by b.shipment_date,a.job_no asc";

	$sql="select a.buyer_name,a.job_no,b.id as po_id,b.po_number,b.pub_shipment_date,b.shipment_date,b.po_quantity,b.unit_price,b.po_total_price,a.ship_mode,a.factory_marchant,a.set_break_down,a.order_uom,a.style_ref_no,a.company_name,b.rfi_date,a.quotation_id from wo_po_details_master a,wo_po_break_down b where   a.job_no=b.job_no_mst and to_date(to_char(b.shipment_date, 'DD-MON-YYYY')) BETWEEN '$current_date' AND '$next_date' and a.company_name=$compid  and b.IS_CONFIRMED=1 and b.shiping_status!=3 and b.status_active=1 and  a.status_active=1 and b.status_active=1 order by b.shipment_date,a.job_no asc";
    //echo $sql;
	


	$dataArr=sql_select($sql);

	//print_r("$dataArr");

	foreach($dataArr as $row){			
				
				$job_arr[$row[csf('job_no')]]=$row[csf('job_no')];
				$poId_arr[$row[csf('po_id')]]=$row[csf('po_id')];

				$date_wise_data_arr[$row[csf('company_name')]][$row[csf('shipment_date')]][$row[csf('job_no')]][$row[csf('po_id')]]['job_no']=$row[csf('job_no')];
				$date_wise_data_arr[$row[csf('company_name')]][$row[csf('shipment_date')]][$row[csf('job_no')]][$row[csf('po_id')]]['buyer_name']=$row[csf('buyer_name')];
				$date_wise_data_arr[$row[csf('company_name')]][$row[csf('shipment_date')]][$row[csf('job_no')]][$row[csf('po_id')]]['po_number']=$row[csf('po_number')];
				$date_wise_data_arr[$row[csf('company_name')]][$row[csf('shipment_date')]][$row[csf('job_no')]][$row[csf('po_id')]]['shipment_date']=$row[csf('shipment_date')];
				$date_wise_data_arr[$row[csf('company_name')]][$row[csf('shipment_date')]][$row[csf('job_no')]][$row[csf('po_id')]]['po_quantity']=$row[csf('po_quantity')];
				$date_wise_data_arr[$row[csf('company_name')]][$row[csf('shipment_date')]][$row[csf('job_no')]][$row[csf('po_id')]]['unit_price']=$row[csf('unit_price')];
				$date_wise_data_arr[$row[csf('company_name')]][$row[csf('shipment_date')]][$row[csf('job_no')]][$row[csf('po_id')]]['po_total_price']=$row[csf('po_total_price')];
				$date_wise_data_arr[$row[csf('company_name')]][$row[csf('shipment_date')]][$row[csf('job_no')]][$row[csf('po_id')]]['ship_mode']=$row[csf('ship_mode')];
				$date_wise_data_arr[$row[csf('company_name')]][$row[csf('shipment_date')]][$row[csf('job_no')]][$row[csf('po_id')]]['order_uom']=$row[csf('order_uom')];
				$date_wise_data_arr[$row[csf('company_name')]][$row[csf('shipment_date')]][$row[csf('job_no')]][$row[csf('po_id')]]['style_ref_no']=$row[csf('style_ref_no')];
				$date_wise_data_arr[$row[csf('company_name')]][$row[csf('shipment_date')]][$row[csf('job_no')]][$row[csf('po_id')]]['factory_marchant']=$row[csf('factory_marchant')];
				$date_wise_data_arr[$row[csf('company_name')]][$row[csf('shipment_date')]][$row[csf('job_no')]][$row[csf('po_id')]]['po_id']=$row[csf('po_id')];
				$date_wise_data_arr[$row[csf('company_name')]][$row[csf('shipment_date')]][$row[csf('job_no')]][$row[csf('po_id')]]['quotation_id']=$row[csf('quotation_id')];
				$date_wise_data_arr[$row[csf('company_name')]][$row[csf('shipment_date')]][$row[csf('job_no')]][$row[csf('po_id')]]['rfi_date']=$row[csf('rfi_date')];
			}
			
	$set_ratio_sql="select a.id as JOB_ID,a.buyer_name,a.job_no,a.order_uom,b.set_item_ratio from wo_po_details_master a,wo_po_details_mas_set_details b where  a.job_no=b.job_no ".where_con_using_array($job_arr,1,'a.job_no')." and a.status_active=1 ";
	// echo $set_ratio_sql;
	$set_sql_data=sql_select($set_ratio_sql);

	foreach($set_sql_data as $row){
		$job_wise_set[$row[csf('job_no')]] +=$row[csf('set_item_ratio')];
		$job_id_arr[$row[JOB_ID]] =$row[JOB_ID];
	}
	unset($set_sql_data);

		

			
	$sweing_output=sql_select("select a.po_break_down_id,
	(CASE WHEN a.production_type=5 THEN b.production_qnty ELSE 0 END) AS output_qnty ,
	(CASE WHEN a.production_type=8 THEN b.production_qnty ELSE 0 END) AS finising_qnty,
	(CASE WHEN a.production_type=1 THEN b.production_qnty ELSE 0 END) AS cutting_qnty
	from pro_garments_production_mst a, pro_garments_production_dtls b where a.id=b.mst_id  and a.production_type in(1,5,8) and a.status_active=1 ".where_con_using_array($poId_arr,1,'a.po_break_down_id')." and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	 foreach($sweing_output as $row){
		$po_wise_qnty_arr[$row[csf("po_break_down_id")]]['output_qnty']+=$row[csf("output_qnty")];
		$po_wise_qnty_arr[$row[csf("po_break_down_id")]]['finising_qnty']+=$row[csf("finising_qnty")];
		$po_wise_qnty_arr[$row[csf("po_break_down_id")]]['cutting_qnty']+=$row[csf("cutting_qnty")];
	 }
			 

	$delivery_data=sql_select("SELECT a.id,a.po_break_down_id,a.item_number_id,a.country_id,a.ex_factory_date,a.ex_factory_qnty,a.location,a.lc_sc_no,a.invoice_no,b.challan_no,a.shiping_status from  pro_ex_factory_mst a,  pro_ex_factory_delivery_mst b where a.delivery_mst_id=b.id ".where_con_using_array($poId_arr,1,'a.po_break_down_id')."  and b.entry_form!=85 and a.status_active=1 and a.is_deleted=0 order by id");

	// echo "SELECT a.id,a.po_break_down_id,a.item_number_id,a.country_id,a.ex_factory_date,a.ex_factory_qnty,a.location,a.lc_sc_no,a.invoice_no,b.challan_no,a.shiping_status from  pro_ex_factory_mst a,  pro_ex_factory_delivery_mst b where a.delivery_mst_id=b.id ".where_con_using_array($poId_arr,1,'a.po_break_down_id')."  and b.entry_form!=85 and a.status_active=1 and a.is_deleted=0 order by id";

	foreach($delivery_data as $row){
		$po_wise_qnty_arr[$row[csf("po_break_down_id")]]['ex_factory_qnty']+=$row[csf("ex_factory_qnty")];
	}

	$ex_factory_return_data=sql_select("SELECT a.id as mst_id,b.id as dtls_mst_id,c.id as dtls_id,b.po_break_down_id as po_id,b.item_number_id,c.color_size_break_down_id as color_mst_id ,b.country_id,d.size_number_id,d.color_number_id,sum(c.production_qnty) as prod_qty from  pro_ex_factory_delivery_mst a,  pro_ex_factory_mst b ,pro_ex_factory_dtls c,wo_po_color_size_breakdown d where a.id=b.delivery_mst_id  and b.id=c.mst_id and d.id=c.color_size_break_down_id and b.po_break_down_id=d.po_break_down_id ".where_con_using_array($poId_arr,1,'b.po_break_down_id')." and a.status_active=1 and a.is_deleted=0 group by a.id,  b.po_break_down_id,b.item_number_id,c.color_size_break_down_id,b.country_id,b.id,c.id,d.size_number_id,d.color_number_id order by b.po_break_down_id");
	foreach($ex_factory_return_data as $row){
		$po_wise_qnty_arr[$row[csf("po_id")]]['exFactoryReruntQnty']=$row[csf("prod_qty")];
	}
			
			
			
	$net_export_val_result=sql_select("select B.PO_BREAKDOWN_ID,((a.NET_INVO_VALUE/b.current_invoice_value)*b.current_invoice_qnty) AS PO_NET_INVO_VALUE  from COM_EXPORT_INVOICE_SHIP_MST a ,COM_EXPORT_INVOICE_SHIP_dtls b where a.id=b.MST_ID ".where_con_using_array($poId_arr,0,'b.PO_BREAKDOWN_ID')."");	

	foreach($net_export_val_result as $row){
		$net_export_val_arr[$row[PO_BREAKDOWN_ID]]=$row[PO_NET_INVO_VALUE];
	}
	
	$preCostSql="select A.JOB_NO,a.COSTING_PER,b.COMMISSION_AMOUNT from wo_pre_cost_mst a,wo_pre_cost_commiss_cost_dtls b where  a.JOB_ID=b.JOB_ID ".where_con_using_array($job_id_arr,0,'b.JOB_ID')."";
	
	
	$preCostSqlResult=sql_select($preCostSql);
	foreach($preCostSqlResult as $row){
		$pre_job_data_arr[COSTING_PER][$row[JOB_NO]]=$row[COSTING_PER];
		$pre_job_data_arr[COMMISSION_AMOUNT][$row[JOB_NO]]=$row[COMMISSION_AMOUNT]*1;
	}
	

			

?>
<!DOCTYPE html>
<html lang="bn">
<head>
  <meta charset="UTF-8">
 </head>
<style>

td {
  font-size: 12px;
  /* height: 100px; */
}
th{

  font-size: 12px;
}




</style>
			<div>
				<b align="center" style=" background-color:#e69900;"><? echo $company_library[$compid];?></b>

		<?php
				foreach ($date_wise_data_arr[$compid] as $date=>$job_data) {					
		?>
				<br>
				<h4 align="center" ><b> Daily Shipment Schedule Of Date : &nbsp;<? echo $date;?></b></h4>
                <table cellspacing="0" cellpadding="0" border="1" rules="all" style=" background-color:#ccebff;" class="rpt_table">
					<thead>
						<th width="40">SL</th>
						 
						<th width="120">Job No</th>  
						<th width="100">Style</th>
						<th width="50">Buyer</th>
						<th width="100">PO/Order</th>		
						<th width="90">RFI Date</th>				
						<th width="90">Shipment Date</th>
						<th width="40">UOM</th>
						<th width="70">Order Qty</th>
						<th width="70">Order Qty(Pcs)</th>
						<th width="40">FOB $</th>
						<th width="100">Gross Value $</th>
                        
						<th width="100">Net Value $</th>
						<th width="70">Buyer Commission %</th>
                        
                        
						<th width="40">Ship Mode</th>						
						<th width="70">Cutting Qty(Pcs)</th>
						<th width="50"><p style="color:red">Balance</p></th>
						<th width="70">Sewing Qty (Pcs)</th>
						<th width="50"><p style="color:red">Balance</b></th>
						<th width="70">Finishing Qty(Pcs)</th>
						<th width="50"><p style="color:red">Balance</b></th>
						<th width="70">Ex-Factory Qty(Pcs)</th>
						<th width="70">Ex-Factory Net Value $</th>
						<th width="50"><p style="color:red">Balance Ex-fac. Qty  (Pcs)</b></th>
						<th width="100">Factory Merchandiser</th>
					</thead>

					<?php
					$i = 1;
					$total_order_qnty=0;$total_order_set_ratioQnty=0;$total_order_value=0;$total_cutting_qnty=0;
							$total_cutting_balance=0;$total_output_qnty=0;;$total_output_balance=0;$total_finishing_qnty=0;$total_finishing_balance=0;;$total_ex_factory_qnty=0;$total_ex_factory_balance=0;
				
				$total_net_export_val=0;
				
				foreach ($job_data as $jobId=>$po_data) {
					
					foreach ($po_data as $poId=>$row) {
							
							$order_set_ratioQnty=$row['po_quantity']*$job_wise_set[$row['job_no']];
							if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
							
 
						$row[COSTING_PER]=$pre_job_data_arr[COSTING_PER][$row['job_no']];
						if($row[COSTING_PER]==1){$order_price_per_dzn=12;}
						else if($row[COSTING_PER]==2){$order_price_per_dzn=1;}
						else if($row[COSTING_PER]==3){$order_price_per_dzn=24;}
						else if($row[COSTING_PER]==4){$order_price_per_dzn=36;}
						else if($row[COSTING_PER]==5){$order_price_per_dzn=48;}
						
						
						$commission_amount = ($pre_job_data_arr[COMMISSION_AMOUNT][$row['job_no']]/$order_price_per_dzn*$row['po_quantity'])*1;
							
							
							$netAmount = (($row['po_quantity']*$row['unit_price'])-$commission_amount);
						
							$total_net_amount+=$netAmount-($row['po_total_price']*$comm)/100;;
							
							if($commission_percent_arr[$row['job_no']]){
							$comm=$commission_percent_arr[$row['job_no']];
							}else{
								$comm=$commission_percent_arr[$row['quotation_id']];
							}
							$job_id=$row['job_no'];
							
							?>
					<tr   bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')"  id="tr_<?    echo $i; ?>" >
							<td align="center"><? echo $i; ?></td>
							
							<td align="center"><p><? echo $row['job_no']; ?></p></td>
							<td align="center"><? echo $row['style_ref_no']; ?>		</td>
							<td align="center"><? echo $buyer_library[$row['buyer_name']]; ?></td>
							<td align="center"><? echo $row['po_number']; ?></td>		
							<td align="center"><p><? echo change_date_format($row['rfi_date']); ?></p></td>				
							<td align="center"><p><? echo change_date_format($row['shipment_date']); ?></p></td>
							<td align="center"><p><? echo $unit_of_measurement[$row['order_uom']]; ?></p></td>	
							<td align="right"><?  echo  number_format($row['po_quantity'], 0,'.',','); ?></td>	
							<td align="right" title="<?= "Set Ratio=".$job_wise_set[$row['job_no']];?>"><?  echo  number_format($order_set_ratioQnty, 0,'.',','); ?></td>				
							<td align="right"><p>$<? echo number_format($row['unit_price'], 2,'.',',');; ?></p></td>	
							<td align="right"><p>$<? echo fn_number_format($row['po_total_price'], 2,'.',','); ?></p></td>
							
                            
                           <td align="right" title="Commi:<?=$commission_amount;?>">$ <?=number_format($netAmount-($row['po_total_price']*$comm)/100,2);?></td> 
                           <td align="right"><?=$comm;?></td> 
                            
                            

                            
                            <td align="center"><p><? echo $shipment_mode[$row['ship_mode']]; ?></p></td>
								
							<td align="right"><? 
							if(number_format($po_wise_qnty_arr[$poId]['cutting_qnty'], 0,'.',',')!=0){
								echo number_format($po_wise_qnty_arr[$poId]['cutting_qnty'], 0,'.',',');
							}
							
							 ?>		</td>
							<td align="right"> <p style="color:red"><?
							if(number_format($po_wise_qnty_arr[$poId]['cutting_qnty']-$order_set_ratioQnty, 0,'.',',')!=0){
								echo number_format($po_wise_qnty_arr[$poId]['cutting_qnty']-$order_set_ratioQnty, 0,'.',',');
								}
							 ?></p></td>
							<td align="right" >
							<? 
							if(number_format($po_wise_qnty_arr[$poId]['output_qnty'], 0,'.',',')!=0){
								echo number_format($po_wise_qnty_arr[$poId]['output_qnty'], 0,'.',',');
							}
						    ?>
                            </td>

							<td align="right"><p style="color:red"><? 
							
							if(number_format($po_wise_qnty_arr[$poId]['output_qnty']-$order_set_ratioQnty, 0,'.',',')!=0){
								echo number_format($po_wise_qnty_arr[$poId]['output_qnty']-$order_set_ratioQnty, 0,'.',',');
								}
							
							?></p></td>
							<td align="right"><? 
							if(number_format($po_wise_qnty_arr[$poId]['finising_qnty'], 0,'.',',')!=0){
								echo number_format($po_wise_qnty_arr[$poId]['finising_qnty'], 0,'.',',');
								}
							
							 ?></td>				
							<td align="right"><p style="color:red"><? 
								if(fn_number_format($po_wise_qnty_arr[$poId]['finising_qnty']-$order_set_ratioQnty,0,"",",")!=0){
									echo fn_number_format($po_wise_qnty_arr[$poId]['finising_qnty']-$order_set_ratioQnty,0,"",",");
									}
							
						 ?></p></td>
							<td align="right"><p><? 
							if(number_format($po_wise_qnty_arr[$poId]['ex_factory_qnty']-$po_wise_qnty_arr[$poId]['exFactoryReruntQnty'], 0,'.',',')!=0){
								echo number_format($po_wise_qnty_arr[$poId]['ex_factory_qnty']-$po_wise_qnty_arr[$poId]['exFactoryReruntQnty'], 0,'.',',');

								}
						
							
						?></p></td>
                         <td align="right"><? if($po_wise_qnty_arr[$poId]['ex_factory_qnty']){ echo number_format($net_export_val_arr[$poId],2);$total_net_export_val+=$net_export_val_arr[$poId];}?></td>							
							<td align="right"><p style="color:red"><?
								if(fn_number_format(($po_wise_qnty_arr[$poId]['ex_factory_qnty']-$po_wise_qnty_arr[$poId]['exFactoryReruntQnty'])-$order_set_ratioQnty,0,"",",")!=0){
									echo fn_number_format(($po_wise_qnty_arr[$poId]['ex_factory_qnty']-$po_wise_qnty_arr[$poId]['exFactoryReruntQnty'])-$order_set_ratioQnty,0,"",",");
									}
						?></p></td>
							<td align="center"><p><? echo $factory_merchand[$row['factory_marchant']]; ?></p></td>
				</tr>
<?
							$total_order_qnty+=$row['po_quantity'];
							$total_order_set_ratioQnty+=$order_set_ratioQnty;
							$total_order_value+=$row['po_quantity']*$row['unit_price'];
							$total_cutting_qnty+=$po_wise_qnty_arr[$poId]['cutting_qnty'];;
							$total_cutting_balance+=$po_wise_qnty_arr[$poId]['cutting_qnty']-$order_set_ratioQnty;
							$total_output_qnty+=$po_wise_qnty_arr[$poId]['output_qnty'];;
							$total_output_balance+=$po_wise_qnty_arr[$poId]['output_qnty']-$order_set_ratioQnty;
							$total_finishing_qnty+=$po_wise_qnty_arr[$poId]['finising_qnty'];;
							$total_finishing_balance+=$po_wise_qnty_arr[$poId]['finising_qnty']-$order_set_ratioQnty;;
							$total_ex_factory_qnty+=$po_wise_qnty_arr[$poId]['ex_factory_qnty']-$po_wise_qnty_arr[$poId]['exFactoryReruntQnty'];;
							$total_ex_factory_balance+=($po_wise_qnty_arr[$poId]['ex_factory_qnty']-$po_wise_qnty_arr[$poId]['exFactoryReruntQnty'])-$order_set_ratioQnty;


						$i++;	
			}}
					?> 
				
				<tr bgcolor="#c2d6d6" >					
					<td  align="right" colspan="8" ><b>Grand Total : &nbsp;&nbsp;</b></td>
					<td align="right"></td>										
					<td align="right"><b><?
					if(number_format($total_order_set_ratioQnty, 0,'.',',')!=0){echo number_format ($total_order_set_ratioQnty, 0,'.',',');	}
					 ?></b></td>			
					<td align="right"></td>			
					<td align="right"><b>$<?=number_format($total_order_value, 2,'.',','); ?></b></td>
                    <td align="right"><?=number_format($total_net_amount,2);?></td> 
                    <td ></td> 
                    
                    <td align="center"></td>						
					
                    
                    <td align="right"><b><?
					if(number_format($total_cutting_qnty, 0,'.',',')!=0){echo number_format($total_cutting_qnty, 0,'.',',');}?></b></td>
					
                    
                    <td align="right"><p style="color:red"><b><?
					if(number_format($total_cutting_balance, 0,'.',',')!=0){echo number_format($total_cutting_balance, 0,'.',',');}?></b></p></td>
                    
                    
					<td align="right"><b><? if(number_format($total_output_qnty, 0,'.',',')!=0){echo number_format($total_output_qnty, 0,'.',',');} ?></b></td>
					<td align="right"><p style="color:red"><b><?	if(number_format($total_output_balance, 0,'.',',')!=0){	echo number_format($total_output_balance, 0,'.',',');}	?></b></p></td>
					<td align="right"><b><? if(number_format($total_finishing_qnty, 0,'.',',')!=0){echo number_format($total_finishing_qnty, 0,'.',',');}?></td>				
					<td align="right"><p style="color:red"><b><?	if(number_format($total_finishing_balance, 0,'.',',')!=0){echo number_format($total_finishing_balance, 0,'.',',');	} ?></b></p></td>							
                    <td align="right"><b><? if(number_format($total_ex_factory_qnty, 0,'.',',')!=0){	echo number_format($total_ex_factory_qnty, 0,'.',',');} ?></b></td>
					<td align="right"><? if($total_net_export_val) echo number_format($total_net_export_val,2);?></td>
					<td align="right"><p style="color:red"><b><?	if(number_format($total_ex_factory_balance, 0,'.',',')!=0){
						echo number_format($total_ex_factory_balance, 0,'.',',');}?></b></p></td>
					<td align="center"></td>
					</tr>
				</table>
				 <p align="left" style=" background-color: yellow;color:red">
				NB: In case of any type of mismatch with actual schedule, please immediate update your Order.<br>
			 	প্রকৃত সময়সূচীর সাথে কোন  অমিল হলে, অনুগ্রহ করে আপনার অর্ডার আপডেট করুন।
					</p> 
					
				
				<?
					}
				
		
			?>
			
		    	<p align="left" style=" background-color:#ccddff;">&nbsp;</p>
						</div>
					</html>
<?


				
			$messageTitle =" <p align='left'><b>Dear Concerns,</b></p>
 					<p align='left'><b>Please check today's & tomorrow's Shipment schedule and take necessary steps immediately :	</b></p>";	
				
				$mail_item=100;
				
				if($is_all_company==1){
					$message.=ob_get_contents();
					ob_clean();
				}
				else
				{
					$message=ob_get_contents();
					ob_clean();
				
					$sql = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=100 and b.mail_user_setup_id=c.id and a.company_id=$compid   and a.IS_DELETED=0 and a.STATUS_ACTIVE=1 AND a.MAIL_TYPE=1 and b.IS_DELETED=0 and b.STATUS_ACTIVE=1 and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
					$mail_sql=sql_select($sql);
					foreach($mail_sql as $row)
					{
						$toArr[$row[csf('email_address')]]=$row[csf('email_address')]; 
					}
	
					$to=implode(',',$toArr);
	
					$subject = " Daily Ex-factory Schedule ";				
					$header=mailHeader();
					if($_REQUEST['isview']==1){
						if($to){
							echo 'Mail Item:'.$form_list_for_mail[$mail_item].'=>'.$to;
						}else{
							echo "Mail address not set. [Please set mail from  Mail Recipient Group, Mail Item: <b>".$form_list_for_mail[$mail_item]."</b>]<br>";
						}
						echo $to.$messageTitle.$message;
					}
					else{
					  if($to!="")echo sendMailMailer( $to, $subject, $messageTitle.$message, $from_mail);
					}
					//echo $message;
				}
				
				

}
	
			if($is_all_company==1){
				
					$sql = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=100 and b.mail_user_setup_id=c.id  and a.IS_DELETED=0 and a.STATUS_ACTIVE=1  and b.IS_DELETED=0 and b.STATUS_ACTIVE=1   and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
					$mail_sql=sql_select($sql);
					foreach($mail_sql as $row)
					{
						$toArr[$row[csf('email_address')]]=$row[csf('email_address')]; 
					}
	
					$to=implode(',',$toArr);
	
					$subject = " Daily Ex-factory Schedule ";				
					$header=mailHeader();
					
					if($_REQUEST['isview']==1){
						if($to){
							echo 'Mail Item:'.$form_list_for_mail[$mail_item].'=>'.$to;
						}else{
							echo "Mail address not set. [Please set mail from  Mail Recipient Group, Mail Item: <b>".$form_list_for_mail[$mail_item]."</b>]<br>";
						}
						echo $to.$messageTitle.$message;
					}
					else{
						 if($to!="")echo sendMailMailer( $to, $subject, $messageTitle.$message, $from_mail);
					}
				
			}

//http://erp.norbangroup.com/erp/auto_mail/daily_ex_factory_schedule_auto_mail.php


?> 