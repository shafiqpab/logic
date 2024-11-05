<?php
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');

$_SESSION['page_permission']=$permission;
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
//--------------------------------------------------------------------------------------------------------------------

$user_name = $_SESSION['logic_erp']["user_id"];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 110, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name  order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );     	 
	exit();
}


if ($action=="load_drop_down_agent")
{
	echo create_drop_down( "cbo_agent", 80, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company='$data' and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (20,21))  group by a.id,a.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Agent --", $selected, "" );  
	exit(); 	 
} 

if ($action=="dealing_merchant_dropdown")
{
	echo create_drop_down( "cbo_team_member", 65, "select id,team_member_name from lib_mkt_team_member_info where team_id='$data' and status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-- Select Team Member --", $selected, "" );
	exit();
}

if ($action=="factory_merchant_dropdown")
{
	echo create_drop_down( "cbo_factory_marchant", 80, "select id,team_member_name from lib_mkt_team_member_info where team_id='$data' and status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-- Select Team Member --", $selected, "" );
	exit();
}




if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$user_arr=return_library_array( "select id, user_name from user_passwd",'id','user_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer order by buyer_name asc", "id", "buyer_name"  );
	$agent_arr=return_library_array( "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company=$cbo_company_name and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (20,21))  order by buyer_name", "id", "buyer_name"  );
	$supplier_library = return_library_array("select id,team_member_name from lib_mkt_team_member_info where status_active=1 and is_deleted=0","id","team_member_name");

	$company_name=str_replace("'","",$cbo_company_name);
	
	
	if($db_type==0){$time_add="23:59:59";}else{$time_add="11:59:59 PM";}
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	if($txt_date_from != "" && $txt_date_to != "")
	{
		$cbo_date_category=str_replace("'","",$cbo_date_category);
		if($cbo_date_category==1)$date_cond=" and c.country_ship_date between '$txt_date_from' and '$txt_date_to'";
		else if($cbo_date_category==2)$date_cond=" and b.pub_shipment_date between '$txt_date_from' and '$txt_date_to'";
		else if($cbo_date_category==3)$date_cond=" and b.update_date between '$txt_date_from' and '$txt_date_to $time_add'";
		else if($cbo_date_category==4)$date_cond=" and b.update_date between '$txt_date_from' and '$txt_date_to $time_add'";
		else if($cbo_date_category==5)$date_cond=" and b.insert_date between '$txt_date_from' and '$txt_date_to $time_add'";
	}
	else
	{
		$date_cond='';
	}
	
	$txt_style=str_replace("'","",$txt_style);
	$txt_job_no=str_replace("'","",$txt_job_no);
	if($txt_style!=""){$style_con=" and a.style_ref_no like '%".trim($txt_style)."%'";}else{$style_con="";}
	if(str_replace("'","",$cbo_buyer_name)!=0){$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";}else{$buyer_id_cond="";}
	if($txt_job_no!=""){$job_con=" and b.job_no_mst like '%".trim($txt_job_no)."'";}else{$job_con="";}
	if(str_replace("'","",$txt_order_no)!=""){$order_con=" and b.po_number=".trim($txt_order_no)."";}else{$order_con="";}
	if(str_replace("'","",$txt_inter_ref)!=""){$inter_ref_cond=" and b.grouping=".trim($txt_inter_ref)."";}else{$inter_ref_cond="";}
	if(str_replace("'","",$cbo_agent)!=0){$agent_con=" and a.agent_name=".trim($cbo_agent)."";}else{$agent_con="";}
	if(str_replace("'","",$cbo_factory_marchant)!=0){$fac_mar_con=" and a.factory_marchant=".trim($cbo_factory_marchant)."";}else{$fac_mar_con="";}
	if(str_replace("'","",$cbo_team_member)!=0){$team_mem_con=" and a.dealing_marchant=".trim($cbo_team_member)."";}else{$team_mem_con="";}
	
	
	if(str_replace("'","",$cbo_team_leader)!=0){$team_leader_con=" and a.team_leader=".trim($cbo_team_leader)."";}else{$team_leader_con="";}
	

ob_start();	
	?>
    <div id="main_body" style="width:3090px;">
        <fieldset style="width:3090px;">


                <table width="3070">
                    <tr>
                        <td align="center" colspan="33"><strong>Cancelled Order Status  ( From : <? echo change_date_format(str_replace("'","",$txt_date_from));?> To <? echo change_date_format(str_replace("'","",$txt_date_to));?> )</strong></td>
                    </tr>
                 </table>         
                 
                 <table width="3070" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
                   <thead>
                        <th width="50">Sl</th>		
                        <th width="120">Buyer Name</th>
                        <th width="120">Agent Name</th>
                        <th width="150">Style</th>
                        <th width="80">Job No.</th>
                        <th width="80">PO Status</th>
                        <th width="100">PO No.</th>
						<th width="100">Internal Ref.</th>
                        <th width="80">PO Recv. Date</th>
                        <th width="80">Shipment Date</th>
                        <th width="80">PO Qnty (Pcs)</th>
                        <th width="80">Unit Price</th>
                        <th width="80">PO Value (USD)</th>
                        <th width="80">Attached Qty with LC/SC</th>
                        <th width="80">Fabric Booking Qnty</th>
                        <th width="80">Trims Booking Value (USD)</th>
                        <th width="80">Service Booking Value (USD)</th>
                        <th width="80">Yarn Issued (Kg)</th>
                        <th width="80">Knitting Completed</th>
                        <th width="80">Dyeing Completed</th>
                        <th width="80">Finished Fab Recv</th>
                        <th width="80">Trims Rec. Value (USD)</th>
                        <th width="80">Cutting Completed</th>
                        <th width="80">Print & Emb. Completed</th>
                        <th width="80">Sewing Completed</th>
                        <th width="80">Finishing Input</th>
                        <th width="80">Garments Fin. Completed</th>
                        <th width="100">Ex-factory Qty</th>
                        <th width="100">Dealing Merchan</th>
                        <th width="80"><? if(str_replace("'","",$cbo_order_status)==1) echo "Active "; elseif(str_replace("'","",$cbo_order_status)==2) echo "InActive "; else echo "Cancelled "; ?>   By</th>
                        <th width="150">Cancelled  Date & Time</th>
                        <th width="70">PO Insert Date</th>
                        <th width="70">Order Status</th>
                        <th>Remarks</th>
                   </thead>
                  </table>
                 
                  
                    <?
					$po_insert_date="";
					if($db_type==0)
					{
						$po_insert_date="DATE_FORMAT(b.insert_date, '%d-%b-%Y') as insert_date,";
					}
					else
					{
						$po_insert_date="TO_CHAR(b.insert_date, 'DD-MM-YYYY') as insert_date,";
					}
                   $sql_colour_size = "
						select 
							a.buyer_name,
							a.agent_name,
							a.style_ref_no,
							a.total_set_qnty,
							b.job_no_mst, 
							b.is_confirmed, 
							b.po_number,
							b.po_received_date,
							b.pub_shipment_date,
							b.id as po_id,
							b.grouping,
							$po_insert_date 
							
							a.dealing_marchant,
							
							b.po_quantity,
							b.unit_price,
							b.updated_by,
							b.update_date,
							b.details_remarks,
							b.status_active
						from 
							wo_po_details_master a, 
							wo_po_break_down b,
							wo_po_color_size_breakdown c
						where 
							a.job_no=b.job_no_mst
							and a.job_no=c.job_no_mst
							and b.id=c.po_break_down_id 
							and a.company_name = $company_name 
							and b.status_active=$cbo_order_status 
							
							$date_cond
							$buyer_id_cond
							$job_con
							$style_con
							$order_con
							$agent_con
							$fac_mar_con
							$team_mem_con
							$team_leader_con
							$inter_ref_cond
							
							and a.is_deleted=0
							and a.status_active=1
							and c.is_deleted=0
							and c.status_active=1
							";
							
							
				$sql = "
						select 
							a.buyer_name,
							a.agent_name,
							a.style_ref_no,
							a.total_set_qnty,
							b.job_no_mst, 
							b.is_confirmed, 
							b.po_number,
							b.po_received_date,
							b.pub_shipment_date,
							b.id as po_id,
							b.grouping,
							$po_insert_date 
							a.dealing_marchant,
							b.po_quantity,
							b.unit_price,
							b.updated_by,
							b.update_date,
							b.details_remarks,
							b.status_active
						from 
							wo_po_details_master a, 
							wo_po_break_down b
						where 
							a.job_no=b.job_no_mst
							and a.company_name = $company_name 
							and b.status_active=$cbo_order_status 
							
							$date_cond
							$buyer_id_cond
							$job_con
							$style_con
							$order_con
							$agent_con
							$fac_mar_con
							$team_mem_con
							$team_leader_con
							$inter_ref_cond
							and a.is_deleted=0
							and a.status_active=1
							";							
						
						if($cbo_date_category==1){$sql=$sql_colour_size;}
						//echo $sql;die;
						
						$result = sql_select( $sql );
					$i=1;
					foreach( $result as $row) 
					{
						$po_data_arr[$row[csf('po_id')]]=array(
							'po_id'=>$row[csf('po_id')],
							'buyer_name'=>$row[csf('buyer_name')],
							'agent_name'=>$row[csf('agent_name')],
							'style_ref_no'=>$row[csf('style_ref_no')],
							'job_no_mst'=>$row[csf('job_no_mst')],
							'is_confirmed'=>$row[csf('is_confirmed')],
							'po_number'=>$row[csf('po_number')],
							'grouping'=>$row[csf('grouping')],
							'insert_date'=>$row[csf('insert_date')],
							'po_received_date'=>$row[csf('po_received_date')],
							'pub_shipment_date'=>$row[csf('pub_shipment_date')],
							'po_quantityPC'=>$row[csf('po_quantity')]*$row[csf('total_set_qnty')],
							'unit_price'=>$row[csf('unit_price')]/$row[csf('total_set_qnty')],
							'updated_by'=>$row[csf('updated_by')],
							'update_date'=>$row[csf('update_date')],
							'dealing_marchant'=>$row[csf('dealing_marchant')],
							'details_remarks'=>$row[csf('details_remarks')],
							'status_active'=>$row[csf('status_active')]
						);
						$po_arr[$row[csf('po_id')]]=$row[csf('po_id')];	
					}
					$po_string =  implode(',',$po_arr);
					
		//----------------------------------------lc	
		$pq_qty= sql_select("select sum(b.attached_qnty) as qty,b.wo_po_break_down_id from com_export_lc_order_info b where b.wo_po_break_down_id in($po_string) and b.status_active =1 and b.is_deleted =0 group by b.wo_po_break_down_id") ;
		foreach($pq_qty as $row) 
			{
				$lcsc_qty_arr[$row[csf('wo_po_break_down_id')]]+=$row[csf('qty')];
			
			}
		//----------------------------------------sc	
		$pq_qty= sql_select("select sum(b.attached_qnty) as qty,b.wo_po_break_down_id from com_sales_contract_order_info b where b.wo_po_break_down_id in($po_string) and b.status_active =1 and b.is_deleted =0 group by b.wo_po_break_down_id") ;
		foreach($pq_qty as $row) 
			{
				$lcsc_qty_arr[$row[csf('wo_po_break_down_id')]]+=$row[csf('qty')];
			
			}
					
	//------------------------------------farbric,trims,service booking;
	$sqls="SELECT a.currency_id,a.exchange_rate,b.booking_type,b.po_break_down_id,b.grey_fab_qnty,b.amount,b.uom FROM wo_booking_mst a,wo_booking_dtls b WHERE b.po_break_down_id IN($po_string) and a.booking_no=b.booking_no and b.status_active=1 and b.is_deleted=0";
	$gf_qnty= sql_select($sqls);
	foreach($gf_qnty as $row) 
	{
		if($row[csf('booking_type')]==1){
			$fb_qty_arr[$row[csf('po_break_down_id')]]+=$row[csf('grey_fab_qnty')];	
			}
		else if($row[csf('booking_type')]==2 || $row[csf('booking_type')]==5){
			$tb_val_arr[$row[csf('po_break_down_id')]]+=($row[csf('amount')]/$row[csf('exchange_rate')]);	
			}
		else if($row[csf('booking_type')]==3 || $row[csf('booking_type')]==6){
			$sb_val_arr[$row[csf('po_break_down_id')]]+=($row[csf('amount')]/$row[csf('exchange_rate')]);	
			}
	
	}


//------------------------------------service booking;
	$sqls="SELECT a.ecchange_rate as exchange_rate,b.order_id,b.wo_value FROM wo_labtest_mst a,wo_labtest_order_dtls b WHERE b.order_id IN($po_string) and a.id=b.mst_id and b.status_active=1 and b.is_deleted=0";
	$gf_qnty= sql_select($sqls);
	foreach($gf_qnty as $row) 
	{
		$sb_val_arr[$row[csf('order_id')]]+=($row[csf('wo_value')]/$row[csf('exchange_rate')]);	
	
	}





	//----------------------------------------	Net Yarn Issue Qty
		$sql_yarn_iss="select a.po_breakdown_id,
				sum(CASE WHEN a.entry_form ='3' THEN a.quantity ELSE 0 END) AS issue_qnty,
				sum(CASE WHEN a.entry_form ='9' THEN a.quantity ELSE 0 END) AS return_qnty
				from order_wise_pro_details a, product_details_master b where a.prod_id=b.id and a.po_breakdown_id IN($po_string) and b.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and a.issue_purpose!=2 group by a.po_breakdown_id";
		$dataArrayIssue=sql_select($sql_yarn_iss);
		foreach($dataArrayIssue as $row)
		{
			$yi_qty_arr[$row[csf('po_breakdown_id')]]=$row[csf('issue_qnty')]-$row[csf('return_qnty')];
		}
	
	
	
	
	
	

		//-----------------Knitting Prod Qty	
		$dataArrayTrans=sql_select("select po_breakdown_id, 
								sum(CASE WHEN entry_form ='13' and trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty_knit,
								sum(CASE WHEN entry_form ='13' and trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty_knit,
								sum(CASE WHEN entry_form ='45' and trans_type=3 THEN quantity ELSE 0 END) AS grey_receive_return,
								sum(CASE WHEN entry_form ='2' THEN quantity ELSE 0 END) AS grey_receive
								
								
								from order_wise_pro_details where  po_breakdown_id in($po_string) and status_active=1 and is_deleted=0 and entry_form in(2,13,45) group by po_breakdown_id");
		foreach($dataArrayTrans as $row)
		{
			$trans_qnty_arr[$row[csf('po_breakdown_id')]]=$row[csf('transfer_in_qnty_knit')]-$row[csf('transfer_out_qnty_knit')];
			$grey_receive_return_qnty_arr[$row[csf('po_breakdown_id')]]=$row[csf('grey_receive_return')];
			$knite_complite_qnty_arr[$row[csf('po_breakdown_id')]]=$row[csf('grey_receive')];
		}


	
	//------------------Fabric Dyeing Qty	
		
	
		$sql_dye="select b.po_id,sum(b.batch_qnty) as dye_qnty from pro_batch_create_mst a,pro_batch_create_dtls b, pro_fab_subprocess c where a.id=b.mst_id and a.id=c.batch_id and b.po_id in($po_string) and c.load_unload_id=2 and c.entry_form=35 and a.batch_against<>2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.po_id";
		$resultDye=sql_select($sql_dye);
		foreach($resultDye as $dyeRow)
		{
			$fabric_dyeing_qty_arr[$dyeRow[csf('po_id')]]=$dyeRow[csf('dye_qnty')];
		}
	
	
	
	
	//----------------------finish fab
			$dataArrayTrans=sql_select("select po_breakdown_id, 
									sum(CASE WHEN entry_form ='7' THEN quantity ELSE 0 END) AS finish_receive,
									sum(CASE WHEN entry_form ='66' THEN quantity ELSE 0 END) AS finish_receive_roll_wise
									
									from order_wise_pro_details where po_breakdown_id in($po_string) and status_active=1 and is_deleted=0 and entry_form in(7,66) group by po_breakdown_id");
			foreach($dataArrayTrans as $row)
			{
				$finish_receive_qnty_arr[$row[csf('po_breakdown_id')]]=$row[csf('finish_receive')]+$row[csf('finish_receive_roll_wise')];
				
			}
		
	//-----------------------------------------------Trims Value
		$trims_in_house_val=sql_select("select a.rate,b.po_breakdown_id,b.quantity,c.exchange_rate,c.currency_id  from  inv_receive_master c,product_details_master d,inv_trims_entry_dtls a , order_wise_pro_details b where b.po_breakdown_id IN($po_string) and a.mst_id=c.id and a.trans_id=b.trans_id and a.prod_id=d.id and b.trans_type=1 and b.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	foreach($trims_in_house_val as $row)
	{
		if($row[csf('currency_id')]==1){
			$trims_val_arr[$row[csf('po_breakdown_id')]]+=($row[csf('quantity')]*$row[csf('rate')])/$row[csf('exchange_rate')];	
		}
		else
		{
			$trims_val_arr[$row[csf('po_breakdown_id')]]+=($row[csf('quantity')]*$row[csf('rate')]);	
		}
	}
	
	
	//-----------------------------------------------Cutting,embo,print,sewing output,iron,packing
	$pq_qty= sql_select("SELECT embel_name,production_type,po_break_down_id,production_quantity as qty FROM pro_garments_production_mst WHERE po_break_down_id IN($po_string) and status_active=1 and is_deleted=0 order by production_type");
	foreach($pq_qty as $row) 
		{
			
			if($row[csf('production_type')]==1 && $row[csf('qty')]){
				$cutting_qty_arr[$row[csf('po_break_down_id')]]+=$row[csf('qty')];
			}
			if($row[csf('production_type')]==3 && ($row[csf('embel_name')]==1 || $row[csf('embel_name')]==2)){
				$emb_print_qty_arr[$row[csf('po_break_down_id')]]+=$row[csf('qty')];
			}
	
			if($row[csf('production_type')]==5 && $row[csf('qty')]){
				$sewing_pro_qty_arr[$row[csf('po_break_down_id')]]+=$row[csf('qty')];
			}
		
			if($row[csf('production_type')]==7 && $row[csf('qty')]){
				$iron_qty_arr[$row[csf('po_break_down_id')]]+=$row[csf('qty')];
			}
			if($row[csf('production_type')]==8 && $row[csf('qty')]){
				$packing_fin_qty_arr[$row[csf('po_break_down_id')]]+=$row[csf('qty')];
			}
		
		}
		
	//-----------------Ex-factory Qty----------------------------------------------------------------
	
	$pq_qty= sql_select("select po_break_down_id, sum(ex_factory_qnty) as ex_factory_qnty from pro_ex_factory_mst where po_break_down_id IN($po_string) and status_active=1 and is_deleted=0 group by po_break_down_id");
	foreach($pq_qty as $row) 
	{
		$exfactory_qty_arr[$row[csf('po_break_down_id')]]=$row[csf('ex_factory_qnty')];
	}


?>
				<div style="max-height:350px; width:3090px; overflow-y:scroll; float:left;" id="scroll_body">
				<table width="3070" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" align="left" id="table_body">
					<?
					$i=1;
					foreach( $po_data_arr as $row) 
					{
						$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF"; 
					?>
                    <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>" style="cursor:pointer;">
                        <td width="50" align="center"><? echo $i;?></td>
                        <td width="120"><P><? echo $buyer_arr[$row['buyer_name']];?></P></td>
                        <td width="120"><p>&nbsp;<? echo $agent_arr[$row['agent_name']];?></p></td>
                        <td width="150"><p><? echo $row['style_ref_no'];?></p></td>
                        <td width="80" align="center"><? echo $row['job_no_mst'];?></td>
                        <td width="80" align="center"><? echo $order_status[$row['is_confirmed']];?></td>
                        <td width="100" align="center"><p><? echo $row['po_number'];?></p></td>
						<td width="100" align="center"><p><? echo $row['grouping'];?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row['po_received_date']);?></td>
                        <td width="80" align="center"><? echo change_date_format($row['pub_shipment_date']);?></td>
                        <td width="80" align="right"><? echo $row['po_quantityPC'];?></td>
                        <td width="80" align="right"><? echo number_format($row['unit_price'],2);?></td>
                        <td width="80" align="right"><? echo number_format($po_val=$row['po_quantityPC']*$row['unit_price'],2); $tot_po_val+=$po_val;?></td>
                        <td width="80" align="right"><? echo $lcsc_qty=$lcsc_qty_arr[$row['po_id']]; $tot_lcsc_qty+=$lcsc_qty;?></td>
                        <td width="80" align="right"><a href="##" onclick="openmypage_popup('<? echo $row['po_id']; ?>','Booking Info','booking_popup')"><? echo number_format($fb_qty_arr[$row['po_id']],2);?></a></td>
                        <td width="80" align="right"><a href="##" onclick="openmypage_popup('<? echo $row['po_id']; ?>','Trims Info','trims_popup')"><? echo number_format($tb_val_arr[$row['po_id']],2);?></a></td>
                        <td width="80" align="right"><? echo number_format($sb_val_arr[$row['po_id']],2);?></td>
                        <td width="80" align="right"><? echo number_format($yi_qty_arr[$row['po_id']],2);?></td>
                        <td width="80" align="right"><? echo number_format($knite_complite_qnty_arr[$row['po_id']],2);?></td>
                        <td width="80" align="right"><? echo number_format($fabric_dyeing_qty_arr[$row['po_id']],2);?></td>
                        <td width="80" align="right"><? echo number_format($finish_receive_qnty_arr[$row['po_id']],2);?></td>
                        <td width="80" align="right"><? echo number_format($trims_val_arr[$row['po_id']],2);?></td>
                        <td width="80" align="right"><? echo number_format($cutting_qty_arr[$row['po_id']],2);?></td>
                        <td width="80" align="right"><? echo number_format($emb_print_qty_arr[$row['po_id']],2);?></td>
                        <td width="80" align="right"><? echo number_format($sewing_pro_qty_arr[$row['po_id']],2);?></td>
                        <td width="80" align="right"><? echo number_format($iron_qty_arr[$row['po_id']],2);?></td>
                        <td width="80" align="right"><? echo number_format($packing_fin_qty_arr[$row['po_id']],2);?></td>
                        <td width="100" align="right"><p><? echo $exfactory_qty_arr[$row['po_id']]; ?></p></td>
                        <td width="100"><p><? echo $supplier_library[$row['dealing_marchant']];?></p></td>
                        <td width="80" align="center"><p><? echo $user_arr[$row['updated_by']];?></p></td>
                        <td width="150" align="center"><? echo $row['update_date'];?></td>
                        <td width="70" align="center"><? echo $row['insert_date'];?></td>
                        <td width="70" align="center"><? echo $row_status[$row['status_active']];?></td>
                        <td><p>&nbsp;<? echo $row['details_remarks'];?></p></td>
                     </tr>   
					<?
						$i++;
						}
					?>
                 </table>
			</div>
            
             <table width="3070" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
               <tfoot>
                    <th colspan="10" align="right">Total : </th>
                    <th width="80"></th>
                    <th width="80"></th>
                    <th width="80" align="right" id="total_po_qty"><? echo $tot_po_val;?></th>
                    <th width="80" align="right" id="total_lcsc_val"><? echo $tot_lcsc_qty;?></th>
                    <th width="80" align="right" id="total_fb_qty"></th>
                    <th width="80" align="right" id="total_tb_qty"></th>
                    <th width="80" align="right" id="total_sb_qty"></th>
                    <th width="80" align="right" id="total_yi_qty"></th>
                    <th width="80" align="right" id="total_knite_qty"></th>
                    <th width="80" align="right" id="total_dyeing_qty"></th>
                    <th width="80" align="right" id="total_ffr_val"></th>
                    <th width="80" align="right" id="total_tr_val"></th>
                    <th width="80"></th>
                    <th width="80"></th>
                    <th width="80"></th>
                    <th width="80"></th>
                    <th width="80"></th>
                    <th width="100" id="total_ex_factory_qty"> </th>
                    <th width="100"> </th>
                    <th width="80"></th>
                    <th width="150"></th>
                    <th width="70"></th>
                    <th width="219"></th>
               </tfoot>
              </table>
        </fieldset>
    </div>
	<?
	foreach (glob("../../../../ext_resource/tmp_report/$user_name*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$html=ob_get_contents();
	$name=time();
	$filename="../../../../ext_resource/tmp_report/".$user_name."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,$html);
	$filename="../../../ext_resource/tmp_report/".$user_name."_".$name.".xls";
	echo "$total_data****$filename";
	exit();
}


if($action=="booking_popup")
{
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode,'','');
	//echo $po_break_down_id;die;
	?>
    <fieldset>
    <div style="width:550px;">
        <table width="530" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1" align="left">
            <thead>
                    <tr>
                        <th width="50">Sl.</th>    
                        <th width="150">Booking No</th>
                        <th width="90">Booking Date</th>
                        <th width="90">Delivery Date</th>
                        <th>Qnty</th>
               		</tr>
            </thead>
        </table>
        <div style="max-height:280px; overflow-y:scroll; width:550px;" id="scroll_body">
            <table cellspacing="0" border="1" class="rpt_table"  width="530" rules="all" id="table_body">
            	<tbody>
				<?
                $total_quantity=0;
                $sql=sql_select("select a.booking_no, a.booking_date, a.delivery_date, sum(b.grey_fab_qnty) as qnty  from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and  b.po_break_down_id=$po_break_down_id and a.booking_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
                group by a.booking_no, a.booking_date, a.delivery_date"); 
                //echo $sql; 
                $i=1;
                foreach($sql as $resultRow)
                {
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                        <td width="50" align="center"><? echo $i;?></td>
                        <td width="150"><p><? echo $resultRow[csf("booking_no")]; ?>&nbsp;</p></td>
                        <td width="90" align="center"><p><? if($resultRow[csf("booking_date")]!="0000-00-00" && $resultRow[csf("booking_date")]!="") echo change_date_format($resultRow[csf("booking_date")]); ?>&nbsp;</p></td>
                        <td width="90" align="center"><p><? if($resultRow[csf("delivery_date")]!="0000-00-00" && $resultRow[csf("delivery_date")]!="") echo change_date_format($resultRow[csf("delivery_date")]); ?>&nbsp;</p></td>
                        <td align="right" style="padding-right:3px;"><? echo number_format($resultRow[csf("qnty")],4); ?></td>
					</tr>	
					<?		
					$total_quantity+=$resultRow[csf("qnty")];
					$i++;
                }//end foreach 1st
                ?>
                </tbody>
                <tfoot>
                    <tr> 
                        <th width="50">&nbsp;</th>
                        <th width="150">&nbsp;</th>
                        <th width="90">&nbsp;</th> 
                        <th width="90" align="right">Total:</th> 
                        <th align="right" style="padding-right:3px;"><? echo number_format($total_quantity,4); ?></th>
                    </tr>
                </tfoot>
        </table>
       </div>
     </div>
     </fieldset>
    <?
	exit();
}


if($action=="trims_popup")
{
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode,'','');
	//echo $po_break_down_id;die;
	?>
    <fieldset>
    <div style="width:550px;">
        <table width="530" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1" align="left">
            <thead>
                <tr>
                    <th width="50">Sl.</th>    
                    <th width="150">Booking No</th>
                    <th width="90">Booking Date</th>
                    <th width="90">Delivery Date</th>
                    <th>Amount</th>
                </tr>
            </thead>
        </table>
        <div style="max-height:280px; overflow-y:scroll; width:550px;" id="scroll_body">
        <table cellspacing="0" border="1" class="rpt_table"  width="530" rules="all" id="table_body">
            <tbody>
            <?
            $total_quantity=0;
            $sql=sql_select("select a.booking_no, a.booking_date, a.delivery_date, sum(b.amount/a.exchange_rate) as amount  from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and  b.po_break_down_id=$po_break_down_id and a.booking_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
            group by a.booking_no, a.booking_date, a.delivery_date"); 
            //echo $sql; 
            $i=1;
            foreach($sql as $resultRow)
            {
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                    <td width="50" align="center"><? echo $i;?></td>
                    <td width="150"><p><? echo $resultRow[csf("booking_no")]; ?>&nbsp;</p></td>
                    <td width="90" align="center"><p><? if($resultRow[csf("booking_date")]!="0000-00-00" && $resultRow[csf("booking_date")]!="") echo change_date_format($resultRow[csf("booking_date")]); ?>&nbsp;</p></td>
                    <td width="90" align="center"><p><? if($resultRow[csf("delivery_date")]!="0000-00-00" && $resultRow[csf("delivery_date")]!="") echo change_date_format($resultRow[csf("delivery_date")]); ?>&nbsp;</p></td>
                    <td align="right" style="padding-right:3px;"><? echo number_format($resultRow[csf("amount")],2); ?></td>
                </tr>	
                <?		
                $total_amt+=$resultRow[csf("amount")];
                $i++;
            }//end foreach 1st
            ?>
            </tbody>
            <tfoot>
                <tr> 
                    <th width="50">&nbsp;</th>
                    <th width="150">&nbsp;</th>
                    <th width="90">&nbsp;</th> 
                    <th width="90" align="right">Total:</th> 
                    <th align="right" style="padding-right:3px;"><? echo number_format($total_amt,2); ?></th>
                </tr>
            </tfoot>
    	</table>
       </div>
     </div>
     </fieldset>
    <?
	exit();
}


disconnect($con);
?>