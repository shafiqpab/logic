<?php
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Woven Garments Price Quotation Entry.
Functionality	:	
JS Functions	:
Created by		:	Aziz 
Creation date 	: 	25-01-2021
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
	$approval_type=str_replace("'","",$cbo_approval_type);
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
	if($approval_type==1) 
	$approval_typeCond=" and a.approved in($approval_type)";
	else if($approval_type==2)  $approval_typeCond=" and a.approved in(0,2)";
	else $approval_typeCond="";
	
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
	$sql= "select a.id,a.quot_date,a.insert_date,a.inserted_by, a.costing_per,a.inquery_id,a.company_id, a.buyer_id, a.agent, b.costing_per_id, a.style_desc, a.style_ref,a.ready_to_approved, a.season,a.season_buyer_wise,a.total_set_qnty as ratio, a.approved_by,a.approved_date,a.product_code, a.pord_dept, a.offer_qnty, a.order_uom, a.est_ship_date, a.approved,a.remarks, b.fabric_cost, b.trims_cost, b.embel_cost, b.wash_cost, b.cm_cost, b.commission,b.comm_cost,b.lab_test,b.inspection,b.freight,b.common_oh,b.currier_pre_cost,b.certificate_pre_cost, b.final_cost_dzn, b.total_cost, b.final_cost_pcs, b.a1st_quoted_price, b.asking_quoted_price, b.confirm_price, b.revised_price, b.margin_dzn,b.price_with_commn_pcs from  wo_price_quotation a,  wo_price_quotation_costing_mst b where a.id=b.quotation_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_name $buyer_id_cond $search_text $approval_typeCond $txt_date $search_quotation order by a.id DESC"; 
	//echo $sql; die;
	$master_sql = sql_select($sql);	
	$buyer_wise_qout_arr=array();
	$costing_per="";
	foreach( $master_sql as $row )
	{
		if($row[csf('costing_per')]==1) $costing_per=12;
					else if($row[csf('costing_per')]==2) $costing_per=1;
					else if($row[csf('costing_per')]==3) $costing_per=24;
					else if($row[csf('costing_per')]==4) $costing_per=36;
					else if($row[csf('costing_per')]==5) $costing_per=48;
					
		$price_qout_arr[$row[csf('id')]]['quot_date']=$row[csf('quot_date')];
		$price_qout_arr[$row[csf('id')]]['insert_date']=$row[csf('insert_date')];
		$price_qout_arr[$row[csf('id')]]['inserted_by']=$row[csf('inserted_by')];
		$price_qout_arr[$row[csf('id')]]['costing_per']=$row[csf('costing_per')];
		$price_qout_arr[$row[csf('id')]]['inquery_id']=$row[csf('inquery_id')];
		$price_qout_arr[$row[csf('id')]]['company_id']=$row[csf('company_id')];
		$price_qout_arr[$row[csf('id')]]['buyer_id']=$row[csf('buyer_id')];
		$price_qout_arr[$row[csf('id')]]['agent']=$row[csf('agent')];
		
		$price_qout_arr[$row[csf('id')]]['costing_per_id']=$row[csf('costing_per_id')];
		$price_qout_arr[$row[csf('id')]]['style_desc']=$row[csf('style_desc')];
		$price_qout_arr[$row[csf('id')]]['style_ref']=$row[csf('style_ref')];
		$price_qout_arr[$row[csf('id')]]['ready_to_approved']=$row[csf('ready_to_approved')];
		$price_qout_arr[$row[csf('id')]]['season_buyer_wise']=$row[csf('season_buyer_wise')];
		$price_qout_arr[$row[csf('id')]]['ratio']=$row[csf('total_set_qnty')];
		
		$price_qout_arr[$row[csf('id')]]['product_code']=$row[csf('product_code')];
		$price_qout_arr[$row[csf('id')]]['pord_dept']=$row[csf('pord_dept')];
		$price_qout_arr[$row[csf('id')]]['offer_qnty']=$row[csf('offer_qnty')];
		$price_qout_arr[$row[csf('id')]]['offer_qnty_pcs']=$row[csf('offer_qnty')]*$row[csf('ratio')];
		$price_qout_arr[$row[csf('id')]]['order_uom']=$row[csf('order_uom')];
		$price_qout_arr[$row[csf('id')]]['est_ship_date']=$row[csf('est_ship_date')];
		$price_qout_arr[$row[csf('id')]]['approved']=$row[csf('approved')];
		$price_qout_arr[$row[csf('id')]]['approved_by']=$row[csf('approved_by')];
		$price_qout_arr[$row[csf('id')]]['approved_date']=$row[csf('approved_date')];
		$price_qout_arr[$row[csf('id')]]['remarks']=$row[csf('remarks')];
		$price_qout_arr[$row[csf('id')]]['fabric_cost']=$row[csf('fabric_cost')];
		$price_qout_arr[$row[csf('id')]]['trims_cost']=$row[csf('trims_cost')];
		$price_qout_arr[$row[csf('id')]]['embel_cost']=$row[csf('embel_cost')];
		$price_qout_arr[$row[csf('id')]]['wash_cost']=$row[csf('wash_cost')];
		$price_qout_arr[$row[csf('id')]]['cm_cost']=$row[csf('cm_cost')];
		$price_qout_arr[$row[csf('id')]]['commission']=$row[csf('commission')]/$costing_per;
		$price_qout_arr[$row[csf('id')]]['comm_cost']=$row[csf('comm_cost')];
		$price_qout_arr[$row[csf('id')]]['lab_test']=$row[csf('lab_test')];
		$price_qout_arr[$row[csf('id')]]['inspection']=$row[csf('inspection')];
		$price_qout_arr[$row[csf('id')]]['freight']=$row[csf('freight')];
		$price_qout_arr[$row[csf('id')]]['common_oh']=$row[csf('common_oh')];
		$price_qout_arr[$row[csf('id')]]['currier_pre_cost']=$row[csf('currier_pre_cost')];
		$price_qout_arr[$row[csf('id')]]['certificate_pre_cost']=$row[csf('certificate_pre_cost')];
		$price_qout_arr[$row[csf('id')]]['final_cost_dzn']=$row[csf('final_cost_dzn')];
		$price_qout_arr[$row[csf('id')]]['total_cost']=$row[csf('total_cost')];
		$price_qout_arr[$row[csf('id')]]['final_cost_pcs']=$row[csf('final_cost_pcs')];
		$price_qout_arr[$row[csf('id')]]['a1st_quoted_price']=$row[csf('a1st_quoted_price')];
		$price_qout_arr[$row[csf('id')]]['asking_quoted_price']=$row[csf('asking_quoted_price')];
		$price_qout_arr[$row[csf('id')]]['confirm_price']=$row[csf('confirm_price')];
		$price_qout_arr[$row[csf('id')]]['revised_price']=$row[csf('revised_price')];
		$price_qout_arr[$row[csf('id')]]['margin_dzn']=$row[csf('margin_dzn')];
		$price_qout_arr[$row[csf('id')]]['price_with_commn_pcs']=$row[csf('price_with_commn_pcs')];
		//Summary buyer wise
		$offer_qty_pcs=$row[csf('offer_qnty')]*$row[csf('ratio')];
		
					
		// $buyer_othercost= ($row[csf('comm_cost')]+$row[csf('lab_test')]+$row[csf('inspection')]+$row[csf('freight')]+$row[csf('common_oh')]+$row[csf('currier_pre_cost')]+$row[csf('certificate_pre_cost')]/$costing_per)*$offer_qty_pcs;
		  $buyer_othercost_sum= ($row[csf('comm_cost')]+$row[csf('lab_test')]+$row[csf('inspection')]+$row[csf('freight')]+$row[csf('common_oh')]+$row[csf('currier_pre_cost')]+$row[csf('certificate_pre_cost')]);
		$buyer_othercost=($buyer_othercost_sum/$costing_per)*$offer_qty_pcs;
					// echo $buyer_othercost.'D';
						
		$buyer_wise_qout_arr[$row[csf('buyer_id')]]['offer_qnty']+=$row[csf('offer_qnty')];
		$buyer_wise_qout_arr[$row[csf('buyer_id')]]['offer_qnty_pcs']+=$row[csf('offer_qnty')]*$row[csf('ratio')];;
		$buyer_wise_qout_arr[$row[csf('buyer_id')]]['price_with_commn_pcs']+=$row[csf('price_with_commn_pcs')];
		
	//echo $row[csf('fabric_cost')].'='.$row[csf('offer_qnty')].'='.$costing_per.',';
		if($row[csf('offer_qnty')]>0)
		{
		$sum_fabric_cost=($row[csf('fabric_cost')]/$costing_per)*$offer_qty_pcs;
		$sum_trims_cost=($row[csf('trims_cost')]/$costing_per)*$offer_qty_pcs;
		$sum_embel_cost=($row[csf('embel_cost')]/$costing_per)*$offer_qty_pcs;
		$sum_wash_cost=($row[csf('wash_cost')]/$costing_per)*$offer_qty_pcs;
		$sum_cm_cost=($row[csf('cm_cost')]/$costing_per)*$offer_qty_pcs;
		$sum_total_cost=($row[csf('total_cost')]/$costing_per)*$offer_qty_pcs;
		if($row[csf('revised_price')] > 0) $row[csf('a1st_quoted_price')]=$row[csf('revised_price')];
		
		$sum_1st_quoted_price=$row[csf('a1st_quoted_price')]*$offer_qty_pcs;
		$sum_asking_quoted_price=$row[csf('asking_quoted_price')]*$offer_qty_pcs;
		$sum_confirm_price=$row[csf('confirm_price')]*$offer_qty_pcs;
		$sum_margin_dzn=($row[csf('margin_dzn')]/$costing_per)*$offer_qty_pcs;
		$sum_commission=($row[csf('commission')]/$costing_per)*$offer_qty_pcs;
		
		
		$tot_cost_pcs=$row[csf('final_cost_pcs')]*$offer_qty_pcs;
		//echo $tot_cost_pcs.', ';
		//echo $sum_fabric_cost.'='.$offer_qty_pcs.'='.$costing_per.'<br>';
		$buyer_wise_qout_arr[$row[csf('buyer_id')]]['avg_fabric_cost']+=$sum_fabric_cost*$costing_per;//($sum_fabric_cost/$offer_qty_pcs)*$costing_per;
		$buyer_wise_qout_arr[$row[csf('buyer_id')]]['fabric_cost']+=$sum_fabric_cost;
		$buyer_wise_qout_arr[$row[csf('buyer_id')]]['avg_trims_cost']+=$sum_trims_cost*$costing_per;
		$buyer_wise_qout_arr[$row[csf('buyer_id')]]['trims_cost']+=$sum_trims_cost;
		$buyer_wise_qout_arr[$row[csf('buyer_id')]]['avg_embel_cost']+=$sum_embel_cost*$costing_per;
		$buyer_wise_qout_arr[$row[csf('buyer_id')]]['embel_cost']+=$sum_embel_cost;
		$buyer_wise_qout_arr[$row[csf('buyer_id')]]['avg_wash_cost']+=$sum_wash_cost*$costing_per;;
		$buyer_wise_qout_arr[$row[csf('buyer_id')]]['wash_cost']+=$sum_wash_cost;
		$buyer_wise_qout_arr[$row[csf('buyer_id')]]['avg_cm_cost']+=$sum_cm_cost*$costing_per;
		$buyer_wise_qout_arr[$row[csf('buyer_id')]]['cm_cost']+=$sum_cm_cost;
		$buyer_wise_qout_arr[$row[csf('buyer_id')]]['avg_other_cost']+=$buyer_othercost*$costing_per;
		$buyer_wise_qout_arr[$row[csf('buyer_id')]]['other_cost']+=$buyer_othercost;
		$buyer_wise_qout_arr[$row[csf('buyer_id')]]['buyer_amount']+=$row[csf('price_with_commn_pcs')]*$offer_qty_pcs;
		}
		//echo $sum_1st_quoted_price.'DD';
		$buyer_wise_qout_arr[$row[csf('buyer_id')]]['commission']+=$row[csf('commission')];
		$buyer_wise_qout_arr[$row[csf('buyer_id')]]['comm_cost']+=$row[csf('comm_cost')];
		$buyer_wise_qout_arr[$row[csf('buyer_id')]]['lab_test']+=$row[csf('lab_test')];
		$buyer_wise_qout_arr[$row[csf('buyer_id')]]['inspection']+=$row[csf('inspection')];
		$buyer_wise_qout_arr[$row[csf('buyer_id')]]['freight']+=$row[csf('freight')];
		$buyer_wise_qout_arr[$row[csf('buyer_id')]]['common_oh']+=$row[csf('common_oh')];
		$buyer_wise_qout_arr[$row[csf('buyer_id')]]['currier_pre_cost']+=$row[csf('currier_pre_cost')];
		$buyer_wise_qout_arr[$row[csf('buyer_id')]]['avg_1st_quoted_price']+=$sum_1st_quoted_price;
		$buyer_wise_qout_arr[$row[csf('buyer_id')]]['avg_tot_cost_pcs']+=$tot_cost_pcs;
		$buyer_wise_qout_arr[$row[csf('buyer_id')]]['avg_total_cost']+=$sum_total_cost*$costing_per;
		$buyer_wise_qout_arr[$row[csf('buyer_id')]]['total_cost']+=$sum_total_cost;
		$buyer_wise_qout_arr[$row[csf('buyer_id')]]['avg_asking_quoted_price']+=$sum_asking_quoted_price;
		//$buyer_wise_qout_arr[$row[csf('buyer_id')]]['1st_quoted_price']+=($row[csf('1st_quoted_price')]/$row[csf('offer_qnty')])*$costing_per;
		$buyer_wise_qout_arr[$row[csf('buyer_id')]]['avg_confirm_price']+=$sum_confirm_price;
		$buyer_wise_qout_arr[$row[csf('buyer_id')]]['avg_sum_margin_dzn']+=$sum_margin_dzn;
		
		$buyer_wise_qout_arr[$row[csf('buyer_id')]]['avg_sum_commission']+=$sum_commission;
		$buyer_wise_qout_arr[$row[csf('buyer_id')]]['costing_per']=$costing_per;
		
		
	}


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
	<div style="width:3505px" align="left">	
    <table width="1480" border="1" rules="all"  class="rpt_table" > 
    <caption><b style="text-align:left"> Price Quotation Statement</b> </caption>
            <thead>
                <tr style="font-size:11px"> 
                    <th width="20">SL</th>
                    <th width="100">Buyer</th>
                    <th width="80">Offered Qty</th>
                    <th width="80">Offered <br>Qty Pcs</th>
                    <th width="80">Avg.Price</th>
                    <th width="80">Amount</th>
                    <th width="80">Avg. Fabric<br>Cost/Dzn</th>
                    <th width="80">Avg. Trims <br>Cost /Dzn</th>
                    <th width="80">Avg. Embel. <br>Cost /Dzn</th>
                    <th width="80">Avg. Gmts Wash<br> Cost/Dzn</th>
                    <th width="80">Avg. CM <br>Cost/Dzn</th>
                    <th width="80">Avg. Other<br> Cost /Dzn</th>
                    <th width="80">Avg Total <br>Cost /Dzn</th>
                    <th width="80">Cost /Pcs</th>
                    <th width="80">Quot /Pcs</th>
                    <th width="80">Asking <br>Price/Pcs</th>
                    <th width="80">Conf. <br>Price/Pcs</th>
                    <th width="80">Margin/Pcs</th>
                    <th width="">Comm./Pcs</th>
                                                   
                </tr>                            	
            </thead>
        </table>
         <div style="width:1500px; max-height:350px; overflow-y:scroll" id="report_div" > 
            <table id="report_tbl2" width="1480" border="1"  class="rpt_table" rules="all">
           	
            			<?
						$m=1;$buyer_total_offer_qnty_pcs=$buyer_total_offer_qnty=$buyer_tot_amount=$$buyer_fabric_cost=$buyer_fabric_cost=$buyer_trims_cost=$buyer_embl_cost=$buyer_wash_cost=$buyer_cm_cost=$buyer_avg_1st_quoted_price=$buyer_avg_asking_quoted_price=$buyer_avg_confirm_price=$buyer_avg_sum_margin_dzn=$buyer_avg_sum_commission=$tot_avg_fabric_cost=$tot_offer_qnty_pcs=0;
						$buyer_avg_tot_cost_pcs=0;
                        foreach($buyer_wise_qout_arr as $buyer_id=>$row )  // Master queery 
						{										 
						if ($m%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$m++;
						?>
                       <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('trsum_<? echo $m; ?>','<? echo $bgcolor;?>')" id="trsum_<? echo $m;?>">
						<td width="20" bgcolor="<? echo $sl_bg_color;  ?>"> <? echo $m; ?> </td>
						<td width="100"><div style="word-wrap:break-word; width:95px"><? echo $buyer_arr[$buyer_id]; ?></div></td>
						<td width="80" align="right"><? echo number_format($row[('offer_qnty')],0); ?></td>
                        <td width="80" align="right" ><? //$tot_fabric_cost=($row[('fabric_cost')]/$costing_per)*$row[('offer_qnty')];
						$tot_amount=$row[('buyer_amount')];
														echo number_format($row[('offer_qnty_pcs')],0); ?></td>  
						<td width="80" align="right" title="Amount/OfferQty"><? echo number_format($tot_amount/$row[('offer_qnty_pcs')],2); ?></td>
                        <td width="80" align="right"><?  echo number_format($tot_amount,2); ?></td>   
						<td width="80" align="right" title="Fabric Cost/OfferQty*Costing Per(<? //echo $row[('avg_fabric_cost')];?>)"><? echo number_format($row[('avg_fabric_cost')]/$row[('offer_qnty_pcs')],2);
						$buyer_summ+=$row[('avg_fabric_cost')]/$row[('offer_qnty_pcs')];
						 ?></td>
                        <td width="80" align="right"><? //$tot_embl_cost=($row[('trims_cost')]/$costing_per)*$row[('offer_qnty')];
						
						 echo number_format($row[('avg_trims_cost')]/$row[('offer_qnty_pcs')],2); ?></td>  
						<td width="80" align="right"><? echo number_format($row[('avg_embel_cost')]/$row[('offer_qnty_pcs')],2); ?></td>  
                        <td width="80" align="right"><?  //$tot_wash_cost=($row[('wash_cost')]/$costing_per)*$row[('offer_qnty')]; 
						echo number_format($row[('avg_wash_cost')]/$row[('offer_qnty_pcs')],2); ?></td>  
						<td width="80" align="right"><? echo number_format($row[('avg_cm_cost')]/$row[('offer_qnty_pcs')],2); ?></td>
                        <td width="80" align="right"><?   echo number_format($row[('avg_other_cost')]/$row[('offer_qnty_pcs')],2); ?></td>  
						<? 
						$avg_total_cost_dzn=($row[('avg_fabric_cost')]/$row[('offer_qnty_pcs')])+($row[('avg_trims_cost')]/$row[('offer_qnty_pcs')])+($row[('avg_embel_cost')]/$row[('offer_qnty_pcs')])+($row[('avg_wash_cost')]/$row[('offer_qnty_pcs')])+($row[('avg_cm_cost')]/$row[('offer_qnty_pcs')])+($row[('avg_other_cost')]/$row[('offer_qnty_pcs')]);
                              ?> 
						<td width="80" align="right"><? echo number_format($row[('avg_total_cost')]/$row[('offer_qnty_pcs')],2); ?></td>
                        <td width="80" align="right" title=""><?  
						  echo number_format($row[('avg_tot_cost_pcs')]/$row[('offer_qnty_pcs')],2); ?></td>  
						<td width="80" align="right" title=""><? echo number_format($row[('avg_1st_quoted_price')]/$row[('offer_qnty_pcs')],2); ?></td>
                        <td width="80" align="right"><?  //$tot_all_cost=($row[('total_cost')]/$costing_per)*$row[('offer_qnty')];
						 echo number_format($row[('avg_asking_quoted_price')]/$row[('offer_qnty_pcs')],2); ?></td>   
						<td width="80" align="right"><? echo number_format($row[('avg_confirm_price')]/$row[('offer_qnty_pcs')],2); ?></td>
                        <td width="80" align="right"><? echo number_format($row[('avg_sum_margin_dzn')]/$row[('offer_qnty_pcs')],2); ?></td>
                        <td width="" align="right" title="Cost Pcs*Offer Qty"><?  //$tot_cost_pcs=$row[('final_cost_pcs')]*$row[('offer_qnty')]; 
						echo number_format($row[('avg_sum_commission')]/$row[('offer_qnty_pcs')],2); ?></td>  
                        
                        </tr>
                        <?
						$buyer_total_offer_qnty_pcs+=$row[('offer_qnty_pcs')];
						$buyer_total_offer_qnty+=$row[('offer_qnty')];
						$buyer_tot_amount+=$tot_amount;
						$buyer_fabric_cost+=$row[('avg_fabric_cost')]/$row[('offer_qnty_pcs')];
						$buyer_trims_cost+=$row[('avg_trims_cost')]/$row[('offer_qnty_pcs')];
						$buyer_embl_cost+=$row[('avg_embel_cost')]/$row[('offer_qnty_pcs')];
						$buyer_wash_cost+=$row[('avg_wash_cost')]/$row[('offer_qnty_pcs')];
						$buyer_cm_cost+=$row[('avg_cm_cost')]/$row[('offer_qnty_pcs')];
						$buyer_other_cost+=$row[('avg_other_cost')]/$row[('offer_qnty_pcs')];
						$buyer_avg_1st_quoted_price+=$row[('avg_1st_quoted_price')]/$row[('offer_qnty_pcs')];
						$buyer_avg_tot_cost_pcs+=$row[('avg_tot_cost_pcs')]/$row[('offer_qnty_pcs')];
						$buyer_avg_asking_quoted_price+=$row[('avg_asking_quoted_price')]/$row[('offer_qnty_pcs')];
						$buyer_avg_confirm_price+=$row[('avg_confirm_price')]/$row[('offer_qnty_pcs')];
						$buyer_avg_sum_margin_dzn+=$row[('avg_sum_margin_dzn')]/$row[('offer_qnty_pcs')];
						$buyer_avg_sum_commission+=$row[('avg_sum_commission')]/$row[('offer_qnty_pcs')];
						$buyer_avg_total_cost_dzn+=$row[('avg_total_cost')]/$row[('offer_qnty_pcs')];

						$tot_avg_fabric_cost += $buyer_wise_qout_arr[$buyer_id]['fabric_cost'];
						$tot_avg_trims_cost += $buyer_wise_qout_arr[$buyer_id]['trims_cost'];
						$tot_avg_embel_cost += $buyer_wise_qout_arr[$buyer_id]['embel_cost'];
						$tot_avg_wash_cost += $buyer_wise_qout_arr[$buyer_id]['wash_cost'];
						$tot_avg_cm_cost += $buyer_wise_qout_arr[$buyer_id]['cm_cost'];
						$tot_avg_other_cost += $buyer_wise_qout_arr[$buyer_id]['other_cost'];
						$tot_avg_total_cost += $buyer_wise_qout_arr[$buyer_id]['total_cost'];

						$tot_offer_qnty_pcs += $row[('offer_qnty_pcs')];
						}
						?>
                         
            </table>
             </div>
             <table width="1480" border="1" cellpadding="0" cellspacing="0" class="tbl_bottom" rules="all">
            <tr style="font-size:13px"> 
                <td width="20">&nbsp;</td>
                <td width="100">&nbsp;</td>  
                <td width="80" align="right"><? echo number_format($buyer_total_offer_qnty);?></td>
                <td width="80" align="right"><? echo number_format($buyer_total_offer_qnty_pcs);?></td>
                <td width="80" align="right"></td>
                <td width="80" align="right"><? echo number_format($buyer_tot_amount,2);?></td>
                <td width="80" align="right">
				<? echo number_format(($tot_avg_fabric_cost/$tot_offer_qnty_pcs*$costing_per),2);?>
				</td>
                <td width="80">
				<? echo number_format(($tot_avg_trims_cost/$tot_offer_qnty_pcs*$costing_per),2);?>
				</td>
                <td width="80">
				<? echo number_format(($tot_avg_embel_cost/$tot_offer_qnty_pcs*$costing_per),2);?>
				</td>
                <td width="80"><? echo number_format(($tot_avg_wash_cost/$tot_offer_qnty_pcs*$costing_per),2);?></td>
                <td width="80"><? echo number_format(($tot_avg_cm_cost/$tot_offer_qnty_pcs*$costing_per),2);?></td>
                <td width="80"><? echo number_format(($tot_avg_other_cost/$tot_offer_qnty_pcs*$costing_per),2);?></td>
                
                <td width="80"><? echo number_format(($tot_avg_total_cost/$tot_offer_qnty_pcs*$costing_per),2);?></td>
                <td width="80"><? echo number_format($buyer_avg_tot_cost_pcs,2);?></td>
                <td width="80"><? echo number_format($buyer_avg_1st_quoted_price,2);?></td>
                <td width="80"><? echo number_format($buyer_avg_asking_quoted_price,2);?></td>
                <td width="80"><? echo number_format($buyer_avg_confirm_price,2);?></td>
                <td width="80"><? echo number_format($buyer_avg_sum_margin_dzn,2);?></td>
               
                <td width=""><? echo number_format($buyer_avg_sum_commission,2);?></td>
                                                  
            </tr>     
        </table>   
            <br>
        
			<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="3505" align="left">
				<tr>
					<td  colspan="36" width="3505"><span style="height:15px; width:15px; background-color:#FF0000; float:left; margin-left:400px; margin-right:10px;"></span><span style="float:left;"> 1 day delay from buyer inquiry entry date</span></td>
				</tr>
			</table>
        <table width="3505" border="1" rules="all"  class="rpt_table" > 
            <thead>
                <tr style="font-size:11px"> 
                    <th width="20">SL</th>
                    <th width="40">ID</th> 
                    <th width="40">Comp.</th>                                    
                    <th width="50">Buyer</th>
                    <th width="50">Agent</th>
                    <th width="95">Style Desc.</th> 
                    <th width="95">Style Ref.</th> 
                    <th width="80">Season </th> 
                    <th width="60">Image</th>
                    <th width="50">Prod. Dept</th>
                    <th width="70">Offered Qty</th>
                    <th width="35">UOM</th>
                    <th width="70">Offered Qty Pcs</th>
                    <th width="50">Price</th>
                    <th width="60">Amount</th>
                    <th width="70">Est. Ship Date</th>
					<th width="70">Inquery Date</th>
					<th width="70">Quot. Insert Date</th>
					<th width="70">Insert By </th>
                    <th width="70">Price Quotation date</th>
                    <th width="70">Ready To Approve</th>
                    <th width="70">Status</th> 
                    <th width="100">Approved By</th> 
                    <th width="50" title="24">Days to confirm</th>
                    <th width="70">Fabric Cost /Dzn</th>
                    <th width="70">Total Fabric Cost</th>
                    <th width="70">Trims Cost /Dzn</th>
                    <th width="70">Total Trims Cost</th>
                    
                    <th width="70">Embel. Cost /Dzn</th>
                    <th width="70">Total Embel. Cost</th>
                     
                    <th width="70">Gmts Wash. Cost /Dzn</th>
                    <th width="70">Total Gmts Wash. Cost</th>
                     
                    <th width="70">CM Cost /Dzn</th>
                    <th width="70">Total CM Cost</th>
                    
                    <th width="70">Other Cost /Dzn</th>
                    <th width="70">Total Other Cost</th>
                    
                    <th width="70">Total Cost /Dzn</th>
                    <th width="70">Total Cost</th>
                    
                    <th width="70">Cost /Pcs</th>
                    <th width="70">Total Cost /Pcs</th>
                    
                    <th width="70">Quot /Pcs</th>
                    <th width="70">Total Quot.</th>
                    
                    <th width="70">Asking Price /Pcs</th>
                    <th width="70">Total Asking Price /Pcs</th>
                    <th width="70">Conf. Price /Pcs</th>
                    <th width="70">Total Conf. Price /Pcs</th>
                    <th width="70">Margin /Pcs</th> 
                    <th width="70">Total Margin</th>
                    <th width="70">Comm./Pcs</th>
                    <th width="70">Total Comm.</th>
                   
                    <th>Remarks</th>                                    
                </tr>                            	
            </thead>
        </table>
        <div style="width:3525px; max-height:350px; overflow-y:scroll" id="report_div" > 
            <table id="report_tbl" width="3505" border="1"  class="rpt_table" rules="all">
            <?	
				$k=0;$tot_offer_qnty=$tot_offer_qnty_pcs=$total_commission=0;
				$total_fabric_cost=$total_trims_cost=$total_embl_cost=$total_trims_cost=$total_cm_cost=$total_wash_cost=$total_other_cost=$total_all_cost=$total_cost_pcs=$total_1st_quoted_price=$total_asking_quoted_price=$total_tot_confirm_price=0;$total_amount=0;
				foreach($price_qout_arr as $qout_id=>$row )  // Master queery 
				{										 
					if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$k++;
					if($row[('confirm_price')]>0) $sl_bg_color="#66CC00";	
					else $sl_bg_color=$bgcolor;	
					if($row[('costing_per')]==1) $costing_per=12;
					else if($row[('costing_per')]==2) $costing_per=1;
					else if($row[('costing_per')]==3) $costing_per=24;
					else if($row[('costing_per')]==4) $costing_per=36;
					else if($row[('costing_per')]==5) $costing_per=48;
				
					$insert_date_time=explode(' ',$row[('insert_date')]);
					$quot_insert_date=$insert_date_time[0];
					$quot_insert_date=date('d-m-Y',strtotime($quot_insert_date));
					$quot_insert_by=$user_arr[$row[('inserted_by')]];
					//$costing_per=$row[('costing_per')];
					$quot_inquery_date=$inquery_arr[$row[('inquery_id')]]['inquery_date'];
					
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
					
					if($row[('confirm_price')]==0) $days_to_confirm = datediff("d",date("Y-m-d"),$row[('est_ship_date')]);
					else $days_to_confirm =''; 
				
					if($row[('confirm_price')]==0)
					{
						if($days_to_confirm<=2) $to_confirm_color="red"; 
						else $to_confirm_color="";	 
					}
					else $to_confirm_color="";
					$days_to_confirm = datediff("d",date("Y-m-d"),$row[('est_ship_date')]); 

					$quot_date="'" . $row[('quot_date')]. "'";					
					$style="'" . $row[('style_ref')] . "'";
                    $report_data=trim($report_type," \t\n\r").','.$qout_id.','.$row[('company_id')].','.$row[('buyer_id')].','.$style.','.$quot_date;


					?>
					<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr3_<? echo $k; ?>','<? echo $bgcolor;?>')" id="tr3_<? echo $k;?>">
						<td width="20" bgcolor="<? echo $sl_bg_color;  ?>"> <? echo $k; ?> </td>
						<td width="40"><div style="word-wrap:break-word; width:40px; text-decoration-line: underline; color: blue; cursor: pointer;" onClick="generate_report(<? echo $report_data ?>)"><? echo $qout_id; ?></div></td>	
						<td width="40"><div style="word-wrap:break-word; width:40px"><? echo $comp_arr[$row[('company_id')]]; ?></div></td>	
						<td width="50"><div style="word-wrap:break-word; width:50px"><? echo $buyer_arr[$row[('buyer_id')]]; ?></div></td>
						<td width="50"><div style="word-wrap:break-word; width:50px"><? echo $buyer_arr[$row[('agent')]]; ?></div></td>
						<td width="95"><div style="word-wrap:break-word; width:95px"><? echo trim($row[('style_desc')]); ?></div></td>
                        <td width="95"><div style="word-wrap:break-word; width:95px"><? echo trim($row[('style_ref')]); ?></div></td> 
						<td width="80"><div style="word-wrap:break-word; width:80px"><? echo $season_buyer_wise[trim($row[('season_buyer_wise')])]; ?></div></td>                               
						<td width="60">
							<? if ($image_arr[$qout_id] != ''){ ?>
							<a href="#" onClick="file_uploader ( '../../../', <? echo $qout_id; ?>,'', 'quotation_entry', 0 ,1,2)" title="Click To View Large" >
							<img height="20" width="50" src="../../../<? echo $image_arr[$qout_id]; ?>"/>
							<? } ?>
							 
						</a></td>
						<td width="50"><div style="word-wrap:break-word; width:50px"><? echo $row[('product_code')]." ". $product_dept[$row[('pord_dept')]]; ?></div></td>
						<td width="70" align="right"><div style="word-wrap:break-word; width:70px"><? echo number_format($row[('offer_qnty')]); ?></div></td>
						<td width="35"><div style="word-wrap:break-word; width:35px"><? echo $unit_of_measurement[$row[('order_uom')]]; ?></div></td>
                        <td width="70" align="right"><div style="word-wrap:break-word; width:70px"><? echo number_format($row[('offer_qnty_pcs')]); ?></div></td>
                        <td width="50" align="right"><? echo number_format($row[('price_with_commn_pcs')],2); ?></td>
                        <td width="60" align="right">
						<? 
                            $amount=$row[('price_with_commn_pcs')]*$row[('offer_qnty_pcs')];
                            echo number_format($amount,2);
							$approved_id=$row[('approved')];
                        ?>
                        </td>
						<td width="70" bgcolor="<? echo $to_confirm_color;  ?>"><div style="word-wrap:break-word; width:70px">&nbsp;<? echo change_date_format($row[('est_ship_date')]); ?></div></td>
						<td width="70" align="center"><? echo $inquery_date; ?></td>
						<td width="70" align="center" ><? echo $quot_insert_date; ?></td>
						<td width="70" align="center" ><? echo $quot_insert_by; ?></td>
                        <td width="70" align="center" bgcolor="<? echo $td_color;?>"><? echo change_date_format($row[('quot_date')]); ?></td>
                        <td width="70" align="center"><? if($row[('ready_to_approved')]==1){echo "Yes";}else{ echo "No";}  ?></td>                        
						<td width="70"><div style="word-wrap:break-word; width:70px"><? if($row[('confirm_price')]==0) echo "Under Process"; else echo "Confirm"; ?></div></td>
                        <td bgcolor="<? if( $arr_approve_by_date[$qout_id]['approved_by']=="" && $row[('approved_by')]==0){ echo 'red';}else{} ?>" width="100" align="center"><? if($user_arr[$arr_approve_by_date[$qout_id]['approved_by']]) echo $user_arr[$arr_approve_by_date[$qout_id]['approved_by']];else echo $user_arr[$row[('approved_by')]]; ?><br/> <? if($arr_approve_by_date[$qout_id]['approved_date']) echo change_date_format($arr_approve_by_date[$qout_id]['approved_date']);else echo change_date_format($row[('approved_date')]); ?></td>
                        <td width="50"><div style="word-wrap:break-word; width:50px"><? echo $days_to_confirm; ?></div></td>  
						<td width="70" align="right"><? echo number_format($row[('fabric_cost')],2); ?></td>
                        <td width="70" align="right" title="OfferQty<? echo $row[('offer_qnty_pcs')];?>/Costing Per=<? echo $costing_per;?>*Fab CostDzn=<? echo $row[('fabric_cost')];?>"><? $tot_fabric_cost=$row[('fabric_cost')]*($row[('offer_qnty_pcs')]/$costing_per);
														echo number_format($tot_fabric_cost,2); ?></td>  
						<td width="70" align="right"><? echo number_format($row[('trims_cost')],2); ?></td>
                         <td width="70" align="right"><?  $tot_trims_cost=($row[('trims_cost')]/$costing_per)*$row[('offer_qnty_pcs')];echo number_format($tot_trims_cost,2); ?></td>   
						<td width="70" align="right"><? echo number_format($row[('embel_cost')],2); ?></td>
                         <td width="70" align="right"><?  $tot_embl_cost=($row[('embel_cost')]/$costing_per)*$row[('offer_qnty_pcs')]; echo number_format($tot_embl_cost,2); ?></td>  
						<td width="70" align="right"><? echo number_format($row[('wash_cost')],2); ?></td>  
                         <td width="70" align="right"><?  $tot_wash_cost=($row[('wash_cost')]/$costing_per)*$row[('offer_qnty_pcs')]; echo number_format($tot_wash_cost,2); ?></td>  
						<td width="70" align="right"><? echo number_format($row[('cm_cost')],2); ?></td>
                         <td width="70" align="right"><?  $tot_cm_cost=($row[('cm_cost')]/$costing_per)*$row[('offer_qnty_pcs')]; echo number_format($tot_cm_cost,2); ?></td>  
						<? 
                            $othercost= $row[('comm_cost')]+$row[('lab_test')]+$row[('inspection')]+$row[('freight')]+$row[('common_oh')]+$row[('currier_pre_cost')]+$row[('certificate_pre_cost')];    ?>
						<td width="70" align="right"><? echo number_format($othercost,2); ?></td>
                         <td width="70" align="right"><?   $tot_other_cost=($othercost/$costing_per)*$row[('offer_qnty_pcs')];  echo number_format($tot_other_cost,2); ?></td>  
						<td width="70" align="right"><? echo number_format($row[('total_cost')],2); ?></td>
                         <td width="70" align="right"><?  $tot_all_cost=($row[('total_cost')]/$costing_per)*$row[('offer_qnty_pcs')]; echo number_format($tot_all_cost,2); ?></td>   
						<td width="70" align="right"><? echo number_format($row[('final_cost_pcs')],2); ?></td>
                         <td width="70" align="right" title="Cost Pcs*Offer Qty Pcs"><?  $tot_cost_pcs=$row[('final_cost_pcs')]*$row[('offer_qnty_pcs')]; echo number_format($tot_cost_pcs,2); ?></td>  
                         
						<? if($row[('revised_price')] > 0) $row[('1st_quoted_price')]=$row[('revised_price')];  ?>
						
                        <td width="70" align="right" ><? echo number_format($row[('1st_quoted_price')],2); ?></td>
                        <td width="70" align="right"><?  $tot_1st_quoted_price=$row[('1st_quoted_price')]*$row[('offer_qnty_pcs')]; echo number_format($tot_1st_quoted_price,2); ?></td> 
                        
						
                        
                        <td width="70" align="right" ><? echo number_format($row[('asking_quoted_price')],2); ?></td>  
                        <td width="70" align="right"><?   $tot_asking_quoted_price=$row[('asking_quoted_price')]*$row[('offer_qnty_pcs')]; echo number_format($tot_asking_quoted_price,2); ?></td> 
						<td width="70" align="right" ><? echo number_format($row[('confirm_price')],2); ?></td> 
                        <td width="70" align="right"><?  $tot_confirm_price=$row[('confirm_price')]*$row[('offer_qnty_pcs')]; echo number_format($tot_confirm_price,2); ?></td> 
						<? 
							if($row[("costing_per_id")]==1){$order_price_per_dzn=12;}
							else if($row[("costing_per_id")]==2){$order_price_per_dzn=1;}
							else if($row[("costing_per_id")]==3){$order_price_per_dzn=24;}
							else if($row[("costing_per_id")]==4){$order_price_per_dzn=36;}
							else if($row[("costing_per_id")]==5){$order_price_per_dzn=48;}
							
							$row[('margin_dzn')] = $row[('margin_dzn')]/$order_price_per_dzn;
						?>
						<td width="70" align="right"<? if($row[('margin_dzn')]<0) echo"bgcolor='#FF0000'";?>><? echo number_format($row[('margin_dzn')],4); ?></td>
                       <td width="70" align="right"><? $tot_margin=$row[('offer_qnty_pcs')]*$row[('margin_dzn')];echo number_format($tot_margin,2); ?></td>
						<td width="70" align="right"><? echo number_format($row[('commission')],2); ?></td>
                        <td width="70" align="right"><? $tot_commission=$row[('offer_qnty_pcs')]*$row[('commission')]; echo number_format($tot_commission,2); ?></td> 
						
						<td><div style="word-wrap:break-word; width:122px">&nbsp;<? echo $row[('remarks')]; ?></div></td>    
					</tr>
					<? 		
					
					$tot_offer_qnty += $row[('offer_qnty')];
					$tot_offer_qnty_pcs += $row[('offer_qnty_pcs')];
					$total_fabric_cost += $tot_fabric_cost;
					 $fabric_cost += $row[('fabric_cost')]; 
					 $total_amount+=$amount;
					$trims_cost += $row[('trims_cost')]; 
					$total_trims_cost += $tot_trims_cost;
					$total_embl_cost += $tot_embl_cost;
					$total_wash_cost += $tot_wash_cost;
					$total_cm_cost += $tot_cm_cost;
					$total_other_cost += $tot_other_cost;
					$total_all_cost += $tot_all_cost;
					//$total_other_cost += $tot_other_cost;
					$total_1st_quoted_price += $tot_1st_quoted_price;
					$total_cost_pcs += $tot_cost_pcs;
					$total_asking_quoted_price += $tot_asking_quoted_price;
					$total_tot_confirm_price += $tot_confirm_price;
					
					$embel_cost += $row[('embel_cost')]; 
					$wash_cost += $row[('wash_cost')]; 
					$cm_cost += $row[('cm_cost')]; 
					
					$othercost_tot += $othercost; 
					$final_cost_dzn += $row[('total_cost')]; 
					$final_cost_pcs += $row[('final_cost_pcs')];
					$st_quoted_price += $row[('a1st_quoted_price')]; 
					$asking_price += $row[('asking_quoted_price')];
					$confirm_price += $row[('confirm_price')]; 
					$margin_dzn += $row[('margin_dzn')];
					$commisioncost += $row[('commission')]; 
					$total_commission += $tot_commission; 
					$all_total_margin += $tot_margin;
				
				}// Master table query
			?>
			</table>
        </div>
        <table width="3505" border="1" cellpadding="0" cellspacing="0" class="tbl_bottom" rules="all">
            <tr style="font-size:13px"> 
                <td width="20">&nbsp;</td>
                <td width="40">&nbsp;</td>  
                <td width="40">&nbsp;</td>                                    
                <td width="50">&nbsp;</td>
                <td width="50">&nbsp;</td>
                <td width="95">&nbsp;</td>
                <td width="95">&nbsp;</td> 
                <td width="80">&nbsp;</td> 
                <td width="60">&nbsp;</td>
                <td width="50">&nbsp;</td>
                <td width="70"><? echo number_format($tot_offer_qnty);?></td>
                <td width="35">&nbsp;</td>
                <td width="70"><? echo number_format($tot_offer_qnty_pcs);?></td>
                <td width="50">&nbsp;</td>
                <td width="60"><? echo number_format($total_amount,2);?></td>
                <td width="70">&nbsp;</td>
				<td width="70">&nbsp;</td>
                <td width="70">&nbsp;</td>
				<td width="70">&nbsp;</td>
				<td width="70">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="50">&nbsp;</td>
                <td width="70"><? echo number_format($fabric_cost,2);?></td>
                <td width="70"><? echo number_format($total_fabric_cost,2);?></td>
                <td width="70"><? echo number_format($trims_cost,2);?></td>
                <td width="70"><? echo number_format($total_trims_cost,2);?></td>
                <td width="70"><? echo number_format($embel_cost,2);?></td>
                <td width="70"><? echo number_format($total_embl_cost,2);?></td>
                <td width="70"><? echo number_format($wash_cost,2);?></td>
                <td width="70"><? echo number_format($total_wash_cost,2);?></td>
                <td width="70"><? echo number_format($cm_cost,2);?></td>
                <td width="70"><? echo number_format($total_cm_cost,2);?></td>
                <td width="70"><? echo number_format($othercost_tot,2);?></td>
                <td width="70"><? echo number_format($total_other_cost,2);?></td>
                <td width="70"><? echo number_format($final_cost_dzn,2);?></td>
                <td width="70"><? echo number_format($total_all_cost,2);?></td>
                <td width="70"><? echo number_format($final_cost_pcs,2);?></td>
                
                 <td width="70"><? echo number_format($total_cost_pcs,2);?></td>
                 
                <td width="70"><? echo number_format($st_quoted_price,2);?></td>
                 <td width="70"><? echo number_format($total_1st_quoted_price,2);?></td>
                   
                <td width="70"><? echo number_format($asking_price,2);?></td>
                <td width="70"><? echo number_format($total_asking_quoted_price,2);?></td>
                <td width="70"><? echo number_format($confirm_price,2);?></td>
                <td width="70"><? echo number_format($total_tot_confirm_price,2);?></td>
                <td width="70"><? echo number_format($margin_dzn,2);?></td> 
              <td width="70"><? echo number_format($all_total_margin,2);?></td>
                <td width="70"><? echo number_format($commisioncost,2);?></td>
                <td width="70"><? echo number_format($total_commission,2);?></td>
                
                <td>&nbsp;</td>                                   
            </tr>     
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
