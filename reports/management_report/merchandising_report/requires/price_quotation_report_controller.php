<?php
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Woven Garments Price Quotation Entry.
Functionality	:	
JS Functions	:
Created by		:	Bilas 
Creation date 	: 	27-01-2013
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/
session_start();
include('../../../../includes/common.php');

extract($_REQUEST);
 
$pc_time= add_time(date("H:i:s",time()),360);  
$pc_date = date("Y-m-d",strtotime(add_time(date("H:i:s",time()),360)));
$permission=$_SESSION['page_permission'];

//---------------------------------------------------- Start

if ($action=="load_drop_down_buyer")
{
	

	//echo create_drop_down( "cbo_buyer_name", 160, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );     	 
	if($data != 0)
    {
	echo create_drop_down( "cbo_buyer_name", 160, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" ); 
	exit();  
    }  
    else{
        echo create_drop_down( "cbo_buyer_name", 160, "SELECT buy.id as id ,buy.buyer_name as buyer_name  from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" ); 
        exit(); 
    }
}

if ($action=="load_search_text")
{
	if($data==1) $search_txt = "Enter Job Number";
	else if($data==2) $search_txt = "Enter Style Ref No";
	else if($data==3) $search_txt = "Enter Order No";
	echo $search_txt;
	exit();
}

if ($action=="report_generate")
{	
	if(str_replace("'","",$cbo_company_name)==0 || str_replace("'","",$cbo_company_name)=="") $company_name="";else $company_name = "and a.company_id=$cbo_company_name";
	
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_id=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
	}
	if(str_replace("'","",$txt_search_text)!="") $search_text = "and a.style_ref like '%".str_replace("'","",$txt_search_text)."%'";

	if(str_replace("'","",$txt_quotation_id)!="") $search_quotation = "and a.id = '".str_replace("'","",$txt_quotation_id)."'";
	
	if(str_replace("'","",trim($txt_date_from))=="" || str_replace("'","",trim($txt_date_to))==""){
		$txt_date="";
	}
	else{
		if(str_replace("'","",trim($cbo_date_type))==1)$txt_date=" and a.est_ship_date between $txt_date_from and $txt_date_to";
		else $txt_date=" and a.quot_date between $txt_date_from and $txt_date_to";
	}
	
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$image_arr=return_library_array( "select master_tble_id, image_location from common_photo_library where form_name='quotation_entry'",'master_tble_id','image_location');
	$user_arr=return_library_array( "select id, user_full_name from user_passwd", "id", "user_full_name"  );
	$season_buyer_wise=return_library_array( "select id, season_name from lib_buyer_season", "id", "season_name"  );

	  $arr_approve_by_date=array();
	  $sql_approve= sql_select("select a.id,b.mst_id,b.approved_by,b.approved_date 
 from wo_price_quotation a, approval_history b ,wo_price_quotation_costing_mst c 
 where a.id=b.mst_id and b.entry_form=10 and  a.id=c.quotation_id $company_name $buyer_id_cond $search_text $txt_date and a.status_active=1 and a.is_deleted=0 and b.current_approval_status=1 and a.approved=1");
	foreach( $sql_approve as $row )
	{
		$arr_approve_by_date[$row[csf('id')]]['approved_by']=$row[csf('approved_by')];
		$arr_approve_by_date[$row[csf('id')]]['approved_date']=$row[csf('approved_date')];
	}
		$sql_month="SELECT a.id,a.buyer_id,a.inquery_date from wo_quotation_inquery a where a.status_active=1 and a.is_deleted=0 $company_name $buyer_id_cond";
				//echo $sql_month."jahud";
		$inqu_result=sql_select($sql_month);
		foreach( $inqu_result as $row )
		{
			$inquery_arr[$row[csf('id')]]['inquery_date']=$row[csf('inquery_date')];
		}
				
	//print_r($image_arr);
	 $sql= "select a.id,a.quot_date,a.insert_date,a.inserted_by, a.inquery_id,company_id, buyer_id, agent, costing_per_id, style_desc, a.style_ref,a.ready_to_approved, season,season_buyer_wise, product_code, pord_dept, offer_qnty, order_uom, est_ship_date, approved, remarks, fabric_cost, trims_cost, embel_cost, wash_cost, cm_cost, commission,comm_cost,lab_test,inspection,freight,common_oh,currier_pre_cost,certificate_pre_cost, final_cost_dzn, total_cost, final_cost_pcs, a1st_quoted_price, asking_quoted_price, confirm_price, revised_price, margin_dzn,price_with_commn_pcs from  wo_price_quotation a,  wo_price_quotation_costing_mst b where a.id=b.quotation_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_name $buyer_id_cond $search_text $txt_date $search_quotation order by a.id DESC"; 
	//echo $sql; die;
	$master_sql = sql_select($sql);	


	$print_report_format=return_field_value("format_id","lib_report_template","template_name =$cbo_company_name and module_id=2 and report_id=32 and is_deleted=0 and status_active=1");
	$report_formate_array = explode(',', $print_report_format);
	$report_type='';
	switch ($report_formate_array[0]) {
	case 90:
		$report_type="'preCostRpt'";
		break;
	case 91:
		$report_type="'preCostRpt2'";
		break;
	case 92:
		$report_type="'preCostRpt3'";
		break;
	case 194:
		$report_type="'preCostRpt4'";
		break;
	case 213:
		$report_type="'costingSheetRpt'";
		break;
	case 219:
		$report_type="'buyerSubmitSummery'";
		break;
	default:
		$report_type="'preCostRpt2'";
	}

	ob_start();	
	?>
	<fieldset>
	<div style="width:2956px" align="left">	
			<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="2950" align="left">
				<tr>
					<td  colspan="36" width="2950"><span style="height:15px; width:15px; background-color:#FF0000; float:left; margin-left:400px; margin-right:10px;"></span><span style="float:left;"> 1 day delay from buyer inquiry entry date</span></td>
				</tr>
			</table>
        <table width="2950" border="1" rules="all"  class="rpt_table"> 
            <thead>
                <tr style="font-size:11px"> 
                    <th width="20" style="word-break: break-all;">SL</th>
                    <th width="40" style="word-break: break-all;">ID</th> 
                    <th width="40" style="word-break: break-all;">Comp.</th>                                    
                    <th width="50" style="word-break: break-all;">Buyer</th>
                    <th width="50" style="word-break: break-all;">Agent</th>
                    <th width="95" style="word-wrap: break-word;">Style Desc.</th> 
                    <th width="95" style="word-wrap: break-word;">Style Ref.</th> 
                    <th width="80" style="word-break: break-all;">Season </th> 
                    <th width="60" style="word-break: break-all;">Image</th>
                    <th width="50" style="word-wrap: break-word;">Prod. Dept</th>
                    <th width="80" style="word-wrap: break-word;">Offered Qty</th>
                    <th width="35" style="word-break: break-all;">UOM</th>
                    <th width="80" style="word-break: break-all;">Price</th>
                    <th width="110" style="word-break: break-all;">Amount</th>
                    <th width="70" style="word-wrap: break-word;">Est. Ship Date</th>
					<th width="70" style="word-wrap: break-word;">Inquery Date</th>
					<th width="70" style="word-wrap: break-word;">Quot. Insert Date</th>
					<th width="70" style="word-break: break-all;">Insert By </th>
                    <th width="70" style="word-wrap: break-word;">Price Quotation date</th>
                    <th width="70" style="word-wrap: break-word;">Ready To Approve</th>
                    <th width="70" style="word-break: break-all;">Status</th> 
                    <th width="100" style="word-break: break-all;">Approved By</th> 
                    <th width="50" style="word-wrap: break-word;">Days to confirm</th>
                    <th width="80" style="word-wrap: break-word;">Fabric Cost /Dzn</th>
                    <th width="80" style="word-wrap: break-word;">Trims Cost /Dzn</th>
                    <th width="80" style="word-wrap: break-word;">Embel. Cost /Dzn</th>
                    <th width="80" style="word-wrap: break-word;">Gmts Wash. Cost /Dzn</th>
                    <th width="80" style="word-wrap: break-word;">CM Cost /Dzn</th>
                    <th width="80" style="word-wrap: break-word;">Other Cost /Dzn</th>
                    <th width="80" style="word-wrap: break-word;">Total Cost /Dzn</th>
                    <th width="80" style="word-wrap: break-word;">Cost /Pcs</th>
                    <th width="80" style="word-wrap: break-word;">Quot /Pcs</th>
                    <th width="80" style="word-wrap: break-word;">Asking Price /Pcs</th>
                    <th width="80" style="word-wrap: break-word;">Conf. Price /Pcs</th>
                    <th width="80" style="word-wrap: break-word;">Margin /Pcs</th> 
                    <th width="80" style="word-break: break-all;">Comm.</th>
                    <th width="110" style="word-wrap: break-word;">Total Margin</th>
                    <th style="word-break: break-all;">Remarks</th>                                    
                </tr>                            	
            </thead>
        </table>
        <div style="width:2970px; max-height:350px; overflow-y:scroll" id="scroll_body" > 
            <table id="table_body" width="2950" border="1"  class="rpt_table" rules="all">
            <?	
				$k=0;
				foreach($master_sql as $row )  // Master queery 
				{										 
					if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$k++;
					if($row[csf('confirm_price')]>0) $sl_bg_color="#66CC00";	
					else $sl_bg_color=$bgcolor;	
				
					$insert_date_time=explode(' ',$row[csf('insert_date')]);
					$quot_insert_date=$insert_date_time[0];
					$quot_insert_date=date('d-m-Y',strtotime($quot_insert_date));
					$quot_insert_by=$user_arr[$row[csf('inserted_by')]];
					$quot_inquery_date=$inquery_arr[$row[csf('inquery_id')]]['inquery_date'];
					
					if($quot_inquery_date!='0000-00-00' && $quot_inquery_date!='')
					{
						$inquery_date=date('d-m-Y',strtotime($quot_inquery_date));
						
					}
					else
					{
						$inquery_date='';
					}
					$tot_delay_day=datediff("d",$inquery_date,$quot_insert_date);
					if($tot_delay_day>1)
					{
						$td_color="red";
					}
					else
					{
						$td_color="";
					}
				//	$inquery_date=date('d-m-Y',strtotime($quot_inquery_date));
					
				//	echo $tot_delay_day;
					
					if($row[csf('confirm_price')]==0) $days_to_confirm = datediff("d",date("Y-m-d"),$row[csf('est_ship_date')]);
					else $days_to_confirm =''; 
				
					if($row[csf('confirm_price')]==0)
					{
						if($days_to_confirm<=2) $to_confirm_color="red"; 
						else $to_confirm_color="";	 
					}
					else $to_confirm_color="";
					$days_to_confirm = datediff("d",date("Y-m-d"),$row[csf('est_ship_date')]); 

					$quot_date="'" . $row[csf('quot_date')]. "'";					
					$style="'" . $row[csf('style_ref')] . "'";
                    $report_data=trim($report_type," \t\n\r").','.$row[csf('id')].','.$row[csf('company_id')].','.$row[csf('buyer_id')].','.$style.','.$quot_date;


					?>
					<tr bgcolor="<? echo $bgcolor;?>"  id="tr3_<? echo $k; ?>" style="font-size:13px">
						<td style="word-break: break-all;" width="20" bgcolor="<? echo $sl_bg_color;  ?>"> <? echo $k; ?> </td>
						<td style="word-break: break-all;" width="40"><div style="word-wrap:break-word; width:40px; text-decoration-line: underline; color: blue; cursor: pointer;" onClick="generate_report(<? echo $report_data ?>)"><? echo $row[csf('id')]; ?></div></td>	
						<td style="word-break: break-all;" width="40"><div style="word-wrap:break-word; width:40px"><? echo $comp_arr[$row[csf('company_id')]]; ?></div></td>	
						<td style="word-break: break-all;" width="50"><div style="word-wrap:break-word; width:50px"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></div></td>
						<td style="word-break: break-all;" width="50"><div style="word-wrap:break-word; width:50px"><? echo $buyer_arr[$row[csf('agent')]]; ?></div></td>
						<td style="word-break: break-all;" width="95"><div style="word-wrap:break-word; width:95px"><? echo trim($row[csf('style_desc')]); ?></div></td>
                        <td style="word-break: break-all;" width="95"><div style="word-wrap:break-word; width:95px"><? echo trim($row[csf('style_ref')]); ?></div></td> 
						<td style="word-break: break-all;" width="80"><div style="word-wrap:break-word; width:80px"><? echo $season_buyer_wise[trim($row[csf('season_buyer_wise')])]; ?></div></td>                               
						<td style="word-break: break-all;" width="60">
							<? if ($image_arr[$row[csf('id')]] != ''){ ?>
							<a href="#" onClick="file_uploader ( '../../../', <? echo $row[csf('id')]; ?>,'', 'quotation_entry', 0 ,1,2)" title="Click To View Large" >
							<img height="20" width="50" src="../../../<? echo $image_arr[$row[csf('id')]]; ?>"/>
							<? } ?>
							 
						</a></td>
						<td style="word-break: break-all;" width="50"><div style="word-wrap:break-word; width:50px"><? echo $row[csf('product_code')]." ". $product_dept[$row[csf('pord_dept')]]; ?></div></td>
						<td style="word-break: break-all;" width="70" align="right"><div style="word-wrap:break-word; width:80px"><? echo number_format($row[csf('offer_qnty')]); ?></div></td>
						<td style="word-break: break-all;" width="35"><div style="word-wrap:break-word; width:35px"><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></div></td>
                        <td style="word-break: break-all;" width="80" align="right"><? echo number_format($row[csf('price_with_commn_pcs')],4); ?></td>
                        <td style="word-break: break-all;" width="110" align="right">
						<? 
                            $amount=$row[csf('price_with_commn_pcs')]*$row[csf('offer_qnty')];
                            echo number_format($amount,4);$tot_amount+=$amount;
                        ?>
                        </td>
						<td style="word-break: break-all;" width="70" bgcolor="<? echo $to_confirm_color;  ?>"><div style="word-wrap:break-word; width:70px">&nbsp;<? echo change_date_format($row[csf('est_ship_date')]); ?></div></td>
						<td style="word-break: break-all;" width="70" align="center"><? echo $inquery_date; ?></td>
						<td style="word-break: break-all;" width="70" align="center" ><? echo $quot_insert_date; ?></td>
						<td style="word-break: break-all;" width="70" align="center" ><? echo $quot_insert_by; ?></td>
                        <td style="word-break: break-all;" width="70" align="center" bgcolor="<? echo $td_color;?>"><? echo change_date_format($row[csf('quot_date')]); ?></td>
                        <td style="word-break: break-all;" width="70" align="center"><? if($row[csf('ready_to_approved')]==1){echo "Yes";}else{ echo "No";}  ?></td>                        
						<td style="word-break: break-all;" width="70"><div style="word-wrap:break-word; width:70px"><? if($row[csf('confirm_price')]==0) echo "Under Process"; else echo "Confirm"; ?></div></td>
                        <td style="word-break: break-all;" bgcolor="<? if( $arr_approve_by_date[$row[csf('id')]]['approved_by']==""){ echo 'red';}else{} ?>" width="100" align="center"><? echo $user_arr[$arr_approve_by_date[$row[csf('id')]]['approved_by']]; ?><br/> <? echo $arr_approve_by_date[$row[csf('id')]]['approved_date']; ?></td>
                        <td style="word-break: break-all;" width="50"><div style="word-wrap:break-word; width:50px"><? echo $days_to_confirm; ?></div></td>  
						<td style="word-break: break-all;" width="80" align="right"><? echo number_format($row[csf('fabric_cost')],4); ?></td> 
						<td style="word-break: break-all;" width="80" align="right"><? echo number_format($row[csf('trims_cost')],4); ?></td> 
						<td style="word-break: break-all;" width="80" align="right"><? echo number_format($row[csf('embel_cost')],4); ?></td>
						<td style="word-break: break-all;" width="80" align="right"><? echo number_format($row[csf('wash_cost')],4); ?></td>  
						<td style="word-break: break-all;" width="80" align="right"><? echo number_format($row[csf('cm_cost')],4); ?></td>
						<? 
                            $othercost= $row[csf('comm_cost')]+$row[csf('lab_test')]+$row[csf('inspection')]+$row[csf('freight')]+     
                            $row[csf('common_oh')]+$row[csf('currier_pre_cost')]+$row[csf('certificate_pre_cost')];    ?>
						<td style="word-break: break-all;" width="80" align="right"><? echo number_format($othercost,4); ?></td>
						<td style="word-break: break-all;" width="80" align="right"><? echo number_format($row[csf('total_cost')],4); ?></td> 
						<td style="word-break: break-all;" width="80" align="right"><? echo number_format($row[csf('final_cost_pcs')],4); ?></td>
						<? if($row[csf('revised_price')] > 0) $row[csf('1st_quoted_price')]=$row[csf('revised_price')];  ?>
						<td style="word-break: break-all;" width="80" align="right" ><? echo number_format($row[csf('1st_quoted_price')],4); ?></td>
						<td style="word-break: break-all;" width="80" align="right" ><? echo number_format($row[csf('asking_quoted_price')],4); ?></td>  
						<td style="word-break: break-all;" width="80" align="right" ><? echo number_format($row[csf('confirm_price')],4); ?></td> 
						<? 
							if($row[csf("costing_per_id")]==1){$order_price_per_dzn=12;}
							else if($row[csf("costing_per_id")]==2){$order_price_per_dzn=1;}
							else if($row[csf("costing_per_id")]==3){$order_price_per_dzn=24;}
							else if($row[csf("costing_per_id")]==4){$order_price_per_dzn=36;}
							else if($row[csf("costing_per_id")]==5){$order_price_per_dzn=48;}
							
							//$row[csf('margin_dzn')] = $row[csf('margin_dzn')]/$order_price_per_dzn;
						?>
						<td style="word-break: break-all;" width="80" align="right"<? if($row[csf('asking_quoted_price')]<0) echo"bgcolor='#FF0000'";?>><? echo number_format($row[csf('confirm_price')]-$row[csf('asking_quoted_price')],4); ?></td>
						<td style="word-break: break-all;" width="80" align="right"><? echo number_format($row[csf('commission')],4); ?></td>
						<td style="word-break: break-all;" width="110" align="right"><? echo number_format($row[csf('offer_qnty')]*$row[csf('margin_dzn')],4); $total_margin=$row[csf('offer_qnty')]*$row[csf('margin_dzn')]; ?></td>
						<td style="word-break: break-all;"><div style="word-wrap:break-word; width:122px">&nbsp;<? echo $row[csf('remarks')]; ?></div></td>    
					</tr>
					<? 		
					$offer_qnty += $row[csf('offer_qnty')];
					$fabric_cost += number_format($row[csf('fabric_cost')],4); 
					$trims_cost += number_format($row[csf('trims_cost')],4); 
					$embel_cost += number_format($row[csf('embel_cost')],4); 
					$wash_cost += number_format($row[csf('wash_cost')],4); 
					$cm_cost += number_format($row[csf('cm_cost')],4); 
					
					$othercost_tot += number_format($othercost,4); 
					$final_cost_dzn += number_format($row[csf('total_cost')],4); 
					$final_cost_pcs += number_format($row[csf('final_cost_pcs')],4);
					$st_quoted_price += number_format($row[csf('a1st_quoted_price')],4); 
					$asking_price += number_format($row[csf('asking_quoted_price')],4);
					$confirm_price += number_format($row[csf('confirm_price')]-$row[csf('asking_quoted_price')],4,".",""); 
					$margin_dzn += number_format($row[csf('margin_dzn')],4);
					$commisioncost += number_format($row[csf('commission')],4); 
					$all_total_margin += $total_margin;
				
				}// Master table query
			?>
			</table>
        </div>
        <table width="2950" cellspacing="0" border="1" class="rpt_table" rules="all" id="report_table_footer" >
            <tfoot>
	            <tr style="font-size:13px"> 
	                <th width="20">&nbsp;</th>
	                <th width="40">&nbsp;</th>  
	                <th width="40">&nbsp;</th>                                    
	                <th width="50">&nbsp;</th>
	                <th width="50">&nbsp;</th>
	                <th width="95">&nbsp;</th>
	                <th width="95">&nbsp;</th> 
	                <th width="80">&nbsp;</th> 
	                <th width="60">&nbsp;</th>
	                <th width="50">&nbsp;</th>
	                <th width="80" id="tot_order_qty" style="word-break: break-all;"><? echo number_format($offer_qnty);?></th>
	                <th width="35">&nbsp;</th>
	                <th width="80">&nbsp;</th>
	                <th width="110" id="value_tot_amount" style="word-break: break-all;"><? echo number_format($tot_amount);?></th>
	                <th width="70">&nbsp;</th>
					<th width="70">&nbsp;</th>
	                <th width="70">&nbsp;</th>
					<th width="70">&nbsp;</th>
					<th width="70">&nbsp;</th>
	                <th width="70">&nbsp;</th>
	                <th width="70">&nbsp;</th>
	                <th width="100">&nbsp;</th>
	                <th width="50">&nbsp;</th>
	                <th width="80"  id="value_tot_fabric_cost" style="word-break: break-all;"><? echo number_format($fabric_cost,4);?></th>
	                <th width="80"  id="value_tot_trims_cost" style="word-break: break-all;"><? echo number_format($trims_cost,4);?></th>
	                <th width="80"  id="value_tot_embel_cost" style="word-break: break-all;"><? echo number_format($embel_cost,4);?></th>
	                <th width="80"  id="value_tot_wash_cost" style="word-break: break-all;"><? echo number_format($wash_cost,4);?></th>
	                <th width="80"  id="value_tot_cm_cost" style="word-break: break-all;"><? echo number_format($cm_cost,4);?></th>
	                <th width="80"  id="value_tot_othercost" style="word-break: break-all;"><? echo number_format($othercost_tot,4);?></th>
	                <th width="80"  id="value_tot_final_cost_dzn" style="word-break: break-all;"><? echo number_format($final_cost_dzn,4);?></th>
	                <th width="80"  id="value_tot_final_cost_pcs" style="word-break: break-all;"><? echo number_format($final_cost_pcs,4);?></th>
	                <th width="80"  id="value_tot_st_quoted_price" style="word-break: break-all;"><? echo number_format($st_quoted_price,4);?></th>
	                <th width="80"  id="value_tot_asking_price" style="word-break: break-all;"><? echo number_format($asking_price,4);?></th>
	                <th width="80"  id="value_tot_confirm_price" style="word-break: break-all;"><? echo number_format($confirm_price,4);?></th>
	                <th width="80"  id="value_tot_margin_dzn"style="word-break: break-all;"><? echo number_format($margin_dzn,4);?></th> 
	                <th width="80"  id="value_tot_commisioncost" style="word-break: break-all;"><? echo number_format($commisioncost,4);?></th>
	                <th width="110" id="value_tot_all_margin" style="word-break: break-all;"><? echo number_format($all_total_margin,4);?></th>
	                <th></th>         
					<!-- <th>&nbsp;</th>                           -->
	            </tr> 
            </tfoot>    
        </table>   
	</div>
	</fieldset>	
	<?
	$html = ob_get_contents();
	ob_clean();
	 
	foreach (glob($_SESSION['logic_erp']['user_id']."*.xls") as $filename) {
	@unlink($filename);
	}
	
	$name=time();
	$filename=$_SESSION['logic_erp']['user_id']."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc,$html);
	echo "$html****$filename";
	exit();	
}

if ($action=="report_generate2")
{	
	if(str_replace("'","",$cbo_company_name)==0 || str_replace("'","",$cbo_company_name)=="") $company_name="";else $company_name = "and a.company_id=$cbo_company_name";
	
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_id=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
	}
	if(str_replace("'","",$txt_search_text)!="") $search_text = "and a.style_ref like '%".str_replace("'","",$txt_search_text)."%'";

	if(str_replace("'","",$txt_quotation_id)!="") $search_quotation = "and a.id = '".str_replace("'","",$txt_quotation_id)."'";
	
	if(str_replace("'","",trim($txt_date_from))=="" || str_replace("'","",trim($txt_date_to))==""){
		$txt_date="";
	}
	else{
		if(str_replace("'","",trim($cbo_date_type))==1)$txt_date=" and a.est_ship_date between $txt_date_from and $txt_date_to";
		else $txt_date=" and a.quot_date between $txt_date_from and $txt_date_to";
	}
	
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$image_arr=return_library_array( "select master_tble_id, image_location from common_photo_library where form_name='quotation_entry'",'master_tble_id','image_location');
	$user_arr=return_library_array( "select id, user_full_name from user_passwd", "id", "user_full_name"  );
	$season_buyer_wise=return_library_array( "select id, season_name from lib_buyer_season", "id", "season_name"  );

	  $arr_approve_by_date=array();
	  $sql_approve= sql_select("select a.id,b.mst_id,b.approved_by,b.approved_date 
 from wo_price_quotation a, approval_history b ,wo_price_quotation_costing_mst c 
 where a.id=b.mst_id and b.entry_form=10 and  a.id=c.quotation_id $company_name $buyer_id_cond $search_text $txt_date and a.status_active=1 and a.is_deleted=0 and b.current_approval_status=1 and a.approved=1");
	foreach( $sql_approve as $row )
	{
		$arr_approve_by_date[$row[csf('id')]]['approved_by']=$row[csf('approved_by')];
		$arr_approve_by_date[$row[csf('id')]]['approved_date']=$row[csf('approved_date')];
	}
		$sql_month="SELECT a.id,a.buyer_id,a.inquery_date from wo_quotation_inquery a where a.status_active=1 and a.is_deleted=0 $company_name $buyer_id_cond";
				//echo $sql_month."jahud";
		$inqu_result=sql_select($sql_month);
		foreach( $inqu_result as $row )
		{
			$inquery_arr[$row[csf('id')]]['inquery_date']=$row[csf('inquery_date')];
		}
				
	//print_r($image_arr);
	 $sql= "select a.id,a.quot_date,a.insert_date,a.inserted_by, a.inquery_id,company_id, buyer_id, agent, costing_per_id, style_desc, a.style_ref,a.ready_to_approved, season,season_buyer_wise, product_code, pord_dept, offer_qnty, order_uom, est_ship_date, approved, remarks, fabric_cost, trims_cost, embel_cost, wash_cost, cm_cost, commission,comm_cost,lab_test,inspection,freight,common_oh,currier_pre_cost,certificate_pre_cost, final_cost_dzn, total_cost, final_cost_pcs, a1st_quoted_price, asking_quoted_price, confirm_price, revised_price, margin_dzn,price_with_commn_pcs from  wo_price_quotation a,  wo_price_quotation_costing_mst b where a.id=b.quotation_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_name $buyer_id_cond $search_text $txt_date $search_quotation order by a.id DESC"; 
	//echo $sql; die;
	$master_sql = sql_select($sql);	


	$print_report_format=return_field_value("format_id","lib_report_template","template_name =$cbo_company_name and module_id=2 and report_id=32 and is_deleted=0 and status_active=1");
	$report_formate_array = explode(',', $print_report_format);
	$report_type='';
	switch ($report_formate_array[0]) {
	case 90:
		$report_type="'preCostRpt'";
		break;
	case 91:
		$report_type="'preCostRpt2'";
		break;
	case 92:
		$report_type="'preCostRpt3'";
		break;
	case 194:
		$report_type="'preCostRpt4'";
		break;
	case 213:
		$report_type="'costingSheetRpt'";
		break;
	case 219:
		$report_type="'buyerSubmitSummery'";
		break;
	default:
		$report_type="'preCostRpt2'";
	}

	ob_start();	
	?>
	<fieldset>
	<div style="width:2956px" align="left">	
			<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="2950" align="left">
				<tr>
					<td  colspan="36" width="2950"><span style="height:15px; width:15px; background-color:#FF0000; float:left; margin-left:400px; margin-right:10px;"></span><span style="float:left;"> 1 day delay from buyer inquiry entry date</span></td>
				</tr>
			</table>
        <table width="2950" border="1" rules="all"  class="rpt_table"> 
            <thead>
                <tr style="font-size:11px"> 
                    <th width="20" style="word-break: break-all;">SL</th>
                    <th width="40" style="word-break: break-all;">ID</th> 
                    <th width="40" style="word-break: break-all;">Comp.</th>                                    
                    <th width="50" style="word-break: break-all;">Buyer</th>
                    <th width="50" style="word-break: break-all;">Agent</th>
                    <th width="95" style="word-wrap: break-word;">Style Desc.</th> 
                    <th width="95" style="word-wrap: break-word;">Style Ref.</th> 
                    <th width="80" style="word-break: break-all;">Season </th> 
                    <th width="60" style="word-break: break-all;">Image</th>
                    <th width="50" style="word-wrap: break-word;">Prod. Dept</th>
                    <th width="80" style="word-wrap: break-word;">Offered Qty</th>
                    <th width="35" style="word-break: break-all;">UOM</th>
                    <th width="80" style="word-break: break-all;">Price</th>
                    <th width="110" style="word-break: break-all;">Amount</th>
                    <th width="70" style="word-wrap: break-word;">Est. Ship Date</th>
					<th width="70" style="word-wrap: break-word;">Inquery Date</th>
					<th width="70" style="word-wrap: break-word;">Quot. Insert Date</th>
					<th width="70" style="word-break: break-all;">Insert By </th>
                    <th width="70" style="word-wrap: break-word;">Price Quotation date</th>
                    <th width="70" style="word-wrap: break-word;">Ready To Approve</th>
                    <th width="70" style="word-break: break-all;">Status</th> 
                    <th width="100" style="word-break: break-all;">Approved By</th> 
                    <th width="50" style="word-wrap: break-word;">Days to confirm</th>
                    <th width="80" style="word-wrap: break-word;">Fabric Cost /Pcs</th>
                    <th width="80" style="word-wrap: break-word;">Trims Cost /Pcs</th>
                    <th width="80" style="word-wrap: break-word;">Embel. Cost /Pcs</th>
                    <th width="80" style="word-wrap: break-word;">Gmts Wash. Cost /Pcs</th>
                    <th width="80" style="word-wrap: break-word;">CM Cost /Pcs</th>
                    <th width="80" style="word-wrap: break-word;">Other Cost /Pcs</th>
                    <th width="80" style="word-wrap: break-word;">Total Cost /Pcs</th>
                    <th width="80" style="word-wrap: break-word;">Cost /Pcs</th>
                    <th width="80" style="word-wrap: break-word;">Quot /Pcs</th>
                    <th width="80" style="word-wrap: break-word;">Asking Price /Pcs</th>
                    <th width="80" style="word-wrap: break-word;">Conf. Price /Pcs</th>
                    <th width="80" style="word-wrap: break-word;">Margin /Pcs</th> 
                    <th width="80" style="word-break: break-all;">Comm.</th>
                    <th width="110" style="word-wrap: break-word;">Total Margin</th>
                    <th  style="word-break: break-all;">Remarks</th>                                    
                </tr>                            	
            </thead>
        </table>
        <div style="width:2970px; max-height:350px; overflow-y:scroll" id="scroll_body" > 
            <table id="table_body" width="2950" border="1"  class="rpt_table" rules="all">
            <?	
				$k=0;
				foreach($master_sql as $row )  // Master queery 
				{										 
					if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$k++;
					if($row[csf('confirm_price')]>0) $sl_bg_color="#66CC00";	
					else $sl_bg_color=$bgcolor;	
				
					$insert_date_time=explode(' ',$row[csf('insert_date')]);
					$quot_insert_date=$insert_date_time[0];
					$quot_insert_date=date('d-m-Y',strtotime($quot_insert_date));
					$quot_insert_by=$user_arr[$row[csf('inserted_by')]];
					$quot_inquery_date=$inquery_arr[$row[csf('inquery_id')]]['inquery_date'];
					
					if($quot_inquery_date!='0000-00-00' && $quot_inquery_date!='')
					{
						$inquery_date=date('d-m-Y',strtotime($quot_inquery_date));
						
					}
					else
					{
						$inquery_date='';
					}
					$tot_delay_day=datediff("d",$inquery_date,$quot_insert_date);
					if($tot_delay_day>1)
					{
						$td_color="red";
					}
					else
					{
						$td_color="";
					}
				//	$inquery_date=date('d-m-Y',strtotime($quot_inquery_date));
					
				//	echo $tot_delay_day;
					
					if($row[csf('confirm_price')]==0) $days_to_confirm = datediff("d",date("Y-m-d"),$row[csf('est_ship_date')]);
					else $days_to_confirm =''; 
				
					if($row[csf('confirm_price')]==0)
					{
						if($days_to_confirm<=2) $to_confirm_color="red"; 
						else $to_confirm_color="";	 
					}
					else $to_confirm_color="";
					$days_to_confirm = datediff("d",date("Y-m-d"),$row[csf('est_ship_date')]); 

					$quot_date="'" . $row[csf('quot_date')]. "'";					
					$style="'" . $row[csf('style_ref')] . "'";
                    $report_data=trim($report_type," \t\n\r").','.$row[csf('id')].','.$row[csf('company_id')].','.$row[csf('buyer_id')].','.$style.','.$quot_date;


					?>
					<tr bgcolor="<? echo $bgcolor;?>"  id="tr3_<? echo $k; ?>" style="font-size:13px">
						<td style="word-break: break-all;" width="20" bgcolor="<? echo $sl_bg_color;  ?>"> <? echo $k; ?> </td>
						<td style="word-break: break-all;" width="40"><div style="word-wrap:break-word; width:40px; text-decoration-line: underline; color: blue; cursor: pointer;" onClick="generate_report(<? echo $report_data ?>)"><? echo $row[csf('id')]; ?></div></td>	
						<td style="word-break: break-all;" width="40"><div style="word-wrap:break-word; width:40px"><? echo $comp_arr[$row[csf('company_id')]]; ?></div></td>	
						<td style="word-break: break-all;" width="50"><div style="word-wrap:break-word; width:50px"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></div></td>
						<td style="word-break: break-all;" width="50"><div style="word-wrap:break-word; width:50px"><? echo $buyer_arr[$row[csf('agent')]]; ?></div></td>
						<td style="word-break: break-all;" width="95"><div style="word-wrap:break-word; width:95px"><? echo trim($row[csf('style_desc')]); ?></div></td>
                        <td style="word-break: break-all;" width="95"><div style="word-wrap:break-word; width:95px"><? echo trim($row[csf('style_ref')]); ?></div></td> 
						<td style="word-break: break-all;" width="80"><div style="word-wrap:break-word; width:80px"><? echo $season_buyer_wise[trim($row[csf('season_buyer_wise')])]; ?></div></td>                               
						<td style="word-break: break-all;" width="60">
							<? if ($image_arr[$row[csf('id')]] != ''){ ?>
							<a href="#" onClick="file_uploader ( '../../../', <? echo $row[csf('id')]; ?>,'', 'quotation_entry', 0 ,1,2)" title="Click To View Large" >
							<img height="20" width="50" src="../../../<? echo $image_arr[$row[csf('id')]]; ?>"/>
							<? } ?>
							 
						</a></td>
						<td style="word-break: break-all;" width="50"><div style="word-wrap:break-word; width:50px"><? echo $row[csf('product_code')]." ". $product_dept[$row[csf('pord_dept')]]; ?></div></td>
						<td style="word-break: break-all;" width="70" align="right"><div style="word-wrap:break-word; width:80px"><? echo number_format($row[csf('offer_qnty')]); ?></div></td>
						<td style="word-break: break-all;" width="35"><div style="word-wrap:break-word; width:35px"><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></div></td>
                        <td style="word-break: break-all;" width="80" align="right"><? echo number_format($row[csf('price_with_commn_pcs')],4); ?></td>
                        <td style="word-break: break-all;" width="110" align="right">
						<? 
                            $amount=$row[csf('price_with_commn_pcs')]*$row[csf('offer_qnty')];
                            echo number_format($amount,4);$tot_amount+=$amount;
                        ?>
                        </td>
						<td style="word-break: break-all;" width="70" bgcolor="<? echo $to_confirm_color;  ?>"><div style="word-wrap:break-word; width:70px">&nbsp;<? echo change_date_format($row[csf('est_ship_date')]); ?></div></td>
						<td style="word-break: break-all;" width="70" align="center"><? echo $inquery_date; ?></td>
						<td style="word-break: break-all;" width="70" align="center" ><? echo $quot_insert_date; ?></td>
						<td style="word-break: break-all;" width="70" align="center" ><? echo $quot_insert_by; ?></td>
                        <td style="word-break: break-all;" width="70" align="center" bgcolor="<? echo $td_color;?>"><? echo change_date_format($row[csf('quot_date')]); ?></td>
                        <td style="word-break: break-all;" width="70" align="center"><? if($row[csf('ready_to_approved')]==1){echo "Yes";}else{ echo "No";}  ?></td>                        
						<td style="word-break: break-all;" width="70"><div style="word-wrap:break-word; width:70px"><? if($row[csf('confirm_price')]==0) echo "Under Process"; else echo "Confirm"; ?></div></td>
                        <td style="word-break: break-all;" bgcolor="<? if( $arr_approve_by_date[$row[csf('id')]]['approved_by']==""){ echo 'red';}else{} ?>" width="100" align="center"><? echo $user_arr[$arr_approve_by_date[$row[csf('id')]]['approved_by']]; ?><br/> <? echo $arr_approve_by_date[$row[csf('id')]]['approved_date']; ?></td>
                        <td style="word-break: break-all;" width="50"><div style="word-wrap:break-word; width:50px"><? echo $days_to_confirm; ?></div></td>  
						<td style="word-break: break-all;" width="80" align="right"><? echo number_format($row[csf('fabric_cost')],4); ?></td> 
						<td style="word-break: break-all;" width="80" align="right"><? echo number_format($row[csf('trims_cost')],4); ?></td> 
						<td style="word-break: break-all;" width="80" align="right"><? echo number_format($row[csf('embel_cost')],4); ?></td>
						<td style="word-break: break-all;" width="80" align="right"><? echo number_format($row[csf('wash_cost')],4); ?></td>  
						<td style="word-break: break-all;" width="80" align="right"><? echo number_format($row[csf('cm_cost')],4); ?></td>
						<? 
                            $othercost= $row[csf('comm_cost')]+$row[csf('lab_test')]+$row[csf('inspection')]+$row[csf('freight')]+     
                            $row[csf('common_oh')]+$row[csf('currier_pre_cost')]+$row[csf('certificate_pre_cost')];    ?>
						<td style="word-break: break-all;" width="80" align="right"><? echo number_format($othercost,4); ?></td>
						<td style="word-break: break-all;" width="80" align="right"><? echo number_format($row[csf('total_cost')],4); ?></td> 
						<td style="word-break: break-all;" width="80" align="right"><? echo number_format($row[csf('final_cost_pcs')],4); ?></td>
						<? if($row[csf('revised_price')] > 0) $row[csf('1st_quoted_price')]=$row[csf('revised_price')];  ?>
						<td style="word-break: break-all;" width="80" align="right" ><? echo number_format($row[csf('1st_quoted_price')],4); ?></td>
						<td style="word-break: break-all;" width="80" align="right" ><? echo number_format($row[csf('asking_quoted_price')],4); ?></td>  
						<td style="word-break: break-all;" width="80" align="right" ><? echo number_format($row[csf('confirm_price')],4); ?></td> 
						<? 
							if($row[csf("costing_per_id")]==1){$order_price_per_dzn=12;}
							else if($row[csf("costing_per_id")]==2){$order_price_per_dzn=1;}
							else if($row[csf("costing_per_id")]==3){$order_price_per_dzn=24;}
							else if($row[csf("costing_per_id")]==4){$order_price_per_dzn=36;}
							else if($row[csf("costing_per_id")]==5){$order_price_per_dzn=48;}
							
							$row[csf('margin_dzn')] = $row[csf('margin_dzn')]/$order_price_per_dzn;
						?>
						<td style="word-break: break-all;" width="80" align="right"<? if($row[csf('margin_dzn')]<0) echo"bgcolor='#FF0000'";?>><? echo number_format($row[csf('margin_dzn')],4); ?></td>
						<td style="word-break: break-all;" width="80" align="right"><? echo number_format($row[csf('commission')],4); ?></td>
						<td style="word-break: break-all;" width="110" align="right"><? echo number_format($row[csf('offer_qnty')]*$row[csf('margin_dzn')],4); $total_margin=$row[csf('offer_qnty')]*$row[csf('margin_dzn')]; ?></td>
						<td style="word-break: break-all;"><div style="word-wrap:break-word; width:122px">&nbsp;<? echo $row[csf('remarks')]; ?></div></td>    
					</tr>
					<? 		
					$offer_qnty += $row[csf('offer_qnty')];
					$fabric_cost += number_format($row[csf('fabric_cost')],4); 
					$trims_cost += number_format($row[csf('trims_cost')],4); 
					$embel_cost += number_format($row[csf('embel_cost')],4); 
					$wash_cost += number_format($row[csf('wash_cost')],4); 
					$cm_cost += number_format($row[csf('cm_cost')],4); 
					
					$othercost_tot += number_format($othercost,4); 
					$final_cost_dzn += number_format($row[csf('total_cost')],4); 
					$final_cost_pcs += number_format($row[csf('final_cost_pcs')],4);
					$st_quoted_price += number_format($row[csf('a1st_quoted_price')],4); 
					$asking_price += number_format($row[csf('asking_quoted_price')],4);
					$confirm_price += number_format($row[csf('confirm_price')],4); 
					$margin_dzn += number_format($row[csf('margin_dzn')],4);
					$commisioncost += number_format($row[csf('commission')],4); 
					$all_total_margin += $total_margin;
				
				}// Master table query
			?>
			</table>
        </div>
        <table width="2950" cellspacing="0" border="1" class="rpt_table" rules="all" id="report_table_footer" >
            <tfoot>
	            <tr style="font-size:13px"> 
	                <th width="20">&nbsp;</th>
	                <th width="40">&nbsp;</th>  
	                <th width="40">&nbsp;</th>                                    
	                <th width="50">&nbsp;</th>
	                <th width="50">&nbsp;</th>
	                <th width="95">&nbsp;</th>
	                <th width="95">&nbsp;</th> 
	                <th width="80">&nbsp;</th> 
	                <th width="60">&nbsp;</th>
	                <th width="50">&nbsp;</th>
	                <th width="80" id="tot_order_qty" style="word-break: break-all;"><? echo number_format($offer_qnty);?></th>
	                <th width="35">&nbsp;</th>
	                <th width="80">&nbsp;</th>
	                <th width="110" id="value_tot_amount" style="word-break: break-all;"><? echo number_format($tot_amount);?></th>
	                <th width="70">&nbsp;</th>
					<th width="70">&nbsp;</th>
	                <th width="70">&nbsp;</th>
					<th width="70">&nbsp;</th>
					<th width="70">&nbsp;</th>
	                <th width="70">&nbsp;</th>
	                <th width="70">&nbsp;</th>
	                <th width="100">&nbsp;</th>
	                <th width="50">&nbsp;</th>
	                <th width="80"  id="value_tot_fabric_cost" style="word-break: break-all;"><? echo number_format($fabric_cost,4);?></th>
	                <th width="80"  id="value_tot_trims_cost" style="word-break: break-all;"><? echo number_format($trims_cost,4);?></th>
	                <th width="80"  id="value_tot_embel_cost" style="word-break: break-all;"><? echo number_format($embel_cost,4);?></th>
	                <th width="80"  id="value_tot_wash_cost" style="word-break: break-all;"><? echo number_format($wash_cost,4);?></th>
	                <th width="80"  id="value_tot_cm_cost" style="word-break: break-all;"><? echo number_format($cm_cost,4);?></th>
	                <th width="80"  id="value_tot_othercost" style="word-break: break-all;"><? echo number_format($othercost_tot,4);?></th>
	                <th width="80"  id="value_tot_final_cost_dzn" style="word-break: break-all;"><? echo number_format($final_cost_dzn,4);?></th>
	                <th width="80"  id="value_tot_final_cost_pcs" style="word-break: break-all;"><? echo number_format($final_cost_pcs,4);?></th>
	                <th width="80"  id="value_tot_st_quoted_price" style="word-break: break-all;"><? echo number_format($st_quoted_price,4);?></th>
	                <th width="80"  id="value_tot_asking_price" style="word-break: break-all;"><? echo number_format($asking_price,4);?></th>
	                <th width="80"  id="value_tot_confirm_price" style="word-break: break-all;"><? echo number_format($confirm_price,4);?></th>
	                <th width="80"  id="value_tot_margin_dzn"style="word-break: break-all;"><? echo number_format($margin_dzn,4);?></th> 
	                <th width="80"  id="value_tot_commisioncost" style="word-break: break-all;"><? echo number_format($commisioncost,4);?></th>
	                <th width="110" id="value_tot_all_margin" style="word-break: break-all;"><? echo number_format($all_total_margin,4);?></th>
	                <th></th>         
					<!-- <th>&nbsp;</th>                           -->
	            </tr> 
            </tfoot>    
        </table>   
	</div>
	</fieldset>	
	<?
	$html = ob_get_contents();
	ob_clean();
	 
	foreach (glob($_SESSION['logic_erp']['user_id']."*.xls") as $filename) {
	@unlink($filename);
	}
	
	$name=time();
	$filename=$_SESSION['logic_erp']['user_id']."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc,$html);
	echo "$html****$filename";
	exit();	
}
?>
