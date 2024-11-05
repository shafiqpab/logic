<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');
$user_name=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//------------------------------------------------------------------------------------------

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location", 110, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/date_wise_production_report_controller', this.value, 'load_drop_down_floor', 'floor_td' );get_php_form_data( this.value, 'eval_multi_select', 'requires/date_wise_production_report_controller' );",0 );
	exit();
}

if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor", 110, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' order by floor_name","id,floor_name", 1, "-- Select --", $selected, "",0 );
	exit();  	 
}
if ($action == "eval_multi_select") {
    echo "set_multiselect('cbo_floor','0','0','','0');\n";
    exit();
}
if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 110, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- Select --", $selected, "" );     	 
	exit();
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$company_short_library=return_library_array( "select id,company_short_name from lib_company", "id", "company_short_name");
 	$buyer_short_library=return_library_array( "select id,short_name from lib_buyer", "id", "short_name");
 	$location_library=return_library_array( "select id,location_name from lib_location", "id", "location_name"); 
	$floor_library=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name"); 
	$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"); 
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	$costing_per_id_library=return_library_array( "select job_no, costing_per from wo_pre_cost_mst", "job_no", "costing_per");
	
	$tot_cost_arr=array(); $tot_cm_cost_arr=array();
	$pre_cost_arr = sql_select("select job_no, cm_cost, cm_for_sipment_sche,margin_pcs_set from wo_pre_cost_dtls"); 
	foreach($pre_cost_arr as $row)
	{
		$tot_cost_arr[$row[csf('job_no')]]=$row[csf('cm_for_sipment_sche')];
		$tot_cm_cost_arr[$row[csf('job_no')]]=$row[csf('cm_cost')];
		$tot_margin_pcs_arr[$row[csf('job_no')]]=$row[csf('margin_pcs_set')];
	}
	unset($pre_cost_arr);
	
	$job_item_smv = sql_select("select a.id, b.gmts_item_id, b.finsmv_pcs, b.smv_pcs from wo_po_break_down a, wo_po_details_mas_set_details b where a.job_no_mst=b.job_no and a.status_active=1"); 
	$job_item_smv_arr=array();
	foreach($job_item_smv as $row)
	{
		$job_item_smv_arr[$row[csf('id')]][$row[csf('gmts_item_id')]]['finsmv_pcs']=$row[csf('finsmv_pcs')];
		$job_item_smv_arr[$row[csf('id')]][$row[csf('gmts_item_id')]]['smv_pcs']=$row[csf('smv_pcs')];
	}
	unset($job_item_smv);
	
	$garments_nature=str_replace("'","",$cbo_garments_nature);
	if($garments_nature==1)$garments_nature="";
	$type = str_replace("'","",$cbo_type);
	$location = str_replace("'","",$cbo_location);
	$cbo_floor = str_replace("'","",$cbo_floor);
	$file_no = str_replace("'","",$txt_file_no);
	$internal_ref = str_replace("'","",$txt_internal_ref);
	$excel_type = str_replace("'","",$excel_type);
	if(str_replace("'","",$cbo_company_name)==0)$company_name=""; else $company_name=" and b.company_name=$cbo_company_name";
	if(str_replace("'","",$cbo_buyer_name)==0)$buyer_name="";else $buyer_name=" and a.buyer_name=$cbo_buyer_name";
	if(str_replace("'","",trim($txt_date_from))=="" || str_replace("'","",trim($txt_date_to))=="") $txt_date="";
	else $txt_date=" and production_date between $txt_date_from and $txt_date_to";
	$fromDate = change_date_format( str_replace("'","",trim($txt_date_from)) );
	$toDate = change_date_format( str_replace("'","",trim($txt_date_to)) );
	if ($location==0) $location_cond=""; else $location_cond=" and location=".$location." "; 
	if($cbo_floor=="") $floor_name="";else $floor_name=" and floor_id in($cbo_floor)";
	echo $floor_name;
	if ($file_no=="") $file_no_cond=""; else $file_no_cond=" and b.file_no='".trim($file_no)."' "; 
	if ($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and b.grouping='".trim($internal_ref)."' "; 
		
	$all_order_sewQty_array=array();
	if($type==1)
	{
		$colspn='50';
	}
	else if($type==2)
	{
		$colspn='50';
	}
	ob_start();
	?>
	<div>
		<table width="3530" cellspacing="0">
			<tr class="form_caption" style="border:none;">
					<td colspan="50" align="center" style="border:none;font-size:14px; font-weight:bold" >Date Wise Production Report</td>
			 </tr>
			<tr style="border:none;">
					<td colspan="50" align="center" style="border:none; font-size:16px; font-weight:bold">
						Company Name:<? echo $company_library[str_replace("'","",$cbo_company_name)]; ?>                                
					</td>
			  </tr>
			  <tr style="border:none;">
					<td colspan="50" align="center" style="border:none;font-size:12px; font-weight:bold">
						<? echo "From $fromDate To $toDate" ;?>
					</td>
			  </tr>
		</table>
        <div style="max-height:225px; overflow-y:scroll; width:1568px" >
        <table width="1550" cellspacing="0" border="1" class="rpt_table" rules="all" id="" >
            <thead>
                <tr>
                    <th width="30">Sl.</th>    
                    <th width="80">Buyer Name</th>
                    <th width="80">Cut Qty</th>
                    <th width="80">Sent to Print</th>
                    <th width="80">Rev Print</th>
                    <th width="80">Sent to Emb</th>
                    <th width="80">Rev Emb</th>
                    <th width="80">Sent to Gmt</th>
                    <th width="80">Recv Gmt</th>
                    
                    <th width="80">Sent to Wash</th>
                    <th width="80">Rev Wash</th>
                    <th width="80">Sent to Sp. Works</th>
                    <th width="80">Rev Sp. Works</th>
                    
                    <th width="80">Sew Input</th>
                    <th width="80">Sew Input (Outbound)</th>
                    <th width="80">Sew Output</th>
                    <th width="80">Sew Output (Outbound)</th>
                    <th width="80">Total Iron</th>
                    <th width="80">Total Re-Iron</th>
                    <th >Total Finish</th>
                 </tr>
            </thead>
        </table>
        <table cellspacing="0" border="1" class="rpt_table"  width="1550" rules="all" id="" >
			<?
			$job_arr=array();
			if($db_type==0) $year_cond="YEAR(a.insert_date)";
			else if($db_type==2) $year_cond="to_char(a.insert_date,'YYYY')";
			$job_sql="select a.job_no_prefix_num, a.job_no, a.buyer_name, $year_cond as year, a.style_ref_no, b.id, b.po_number, b.grouping, b.file_no, b.unit_price, b.po_quantity, a.total_set_qnty as ratio, a.order_uom from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.company_name=$cbo_company_name $buyer_name $file_no_cond $internal_ref_cond";
			$job_sql_res=sql_select($job_sql); $tot_rows=0; $poIds='';
			//die;
			foreach($job_sql_res as $row)
			{
				$tot_rows++;
				$poIds.=$row[csf("id")].",";
				$job_arr[$row[csf("id")]]=$row[csf("job_no_prefix_num")].'!!'.$row[csf("job_no")].'!!'.$row[csf("buyer_name")].'!!'.$row[csf("style_ref_no")].'!!'.$row[csf("po_number")].'!!'.$row[csf("grouping")].'!!'.$row[csf("file_no")].'!!'.$row[csf("unit_price")].'!!'.$row[csf("po_quantity")].'!!'.$row[csf("ratio")].'!!'.$row[csf("order_uom")].'!!'.$row[csf("year")];
			}
			unset($job_sql_res); 
			$poIds_cond="";
			if ($file_no!="" || $internal_ref!="")
			{
				$poIds=chop($poIds,',');
				if($db_type==2 && $tot_rows>1000)
				{
					$poIds_cond=" and (";
					$poIdsArr=array_chunk(explode(",",$poIds),999);
					foreach($poIdsArr as $ids)
					{
						$ids=implode(",",$ids);
						$poIds_cond.=" po_break_down_id in ($ids) or ";
					}
					$poIds_cond=chop($poIds_cond,'or ');
					$poIds_cond.=")";
				}
				else
				{
					$poIds_cond=" and po_break_down_id in ($poIds)";
				}
			}
			
			$buyer_fullQty_arr=array();
			$prod_date_qty_arr=array();
			$prod_dlfl_qty_arr=array();
			$all_data_arr=array();
			$sql_dtls="SELECT location, company_id, floor_id, sewing_line, po_break_down_id, production_date, item_number_id, production_type, production_source, embel_name, max(prod_reso_allo) as prod_reso_allo, sum(production_quantity) as production_quantity, sum(re_production_qty) as re_production_qty, sum(reject_qnty) as reject_qnty, sum(carton_qty) as carton_qty 
			from pro_garments_production_mst 
			where company_id=$cbo_company_name and is_deleted=0 and status_active=1 $txt_date $floor_name $location_cond $garmentsNature $poIds_cond
			group by location, company_id, floor_id, sewing_line, po_break_down_id, production_date, item_number_id, production_type, production_source, embel_name
			order by production_date ASC";
			$sql_dtls_res=sql_select($sql_dtls);
			//echo $sql_dtls;  die;//$poIds_cond
			if($type==1)
			{		
				foreach($sql_dtls_res as $row)
				{
					$buyer_name_dat=""; 
					$all_job_data=$job_arr[$row[csf("po_break_down_id")]];
					$ex_job_data=explode('!!',$all_job_data);
					
					$buyer_name_dat=$ex_job_data[2];
					
					//Buyer Wise Summary array start
					$buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]]['0']['pQty']+=$row[csf("production_quantity")];
					$buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]][$row[csf("embel_name")]]['embQty']+=$row[csf("production_quantity")];
					$buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]][$row[csf("production_source")]]['sQty']+=$row[csf("production_quantity")];
					$buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]]['0']['reQty']+=$row[csf("re_production_qty")];
					$buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]][$row[csf("production_source")]]['rejectQty']+=$row[csf("reject_qnty")];
					//Details array start
					$prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("production_type")]]['0']['pQty']+=$row[csf("production_quantity")];
					$prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("production_type")]][$row[csf("embel_name")]]['embQty']+=$row[csf("production_quantity")];
					$prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("production_type")]][$row[csf("production_source")]]['sQty']+=$row[csf("production_quantity")];
					$prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("production_type")]]['0']['reQty']+=$row[csf("re_production_qty")];
					$prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("production_type")]]['0']['rejectQty']+=$row[csf("reject_qnty")];
					$prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]]['0']['0']['crtQty']+=$row[csf("carton_qty")];
					$all_data_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]].=$row[csf("item_number_id")].'**'.$row[csf("company_id")].'__';
				}
			}
			else
			{		
				foreach($sql_dtls_res as $row)
				{
					$buyer_name_dat="";
					$all_job_data=$job_arr[$row[csf("po_break_down_id")]];
					$ex_job_data=explode('!!',$all_job_data);
					
					$buyer_name_dat=$ex_job_data[2];
					
					//Buyer Wise Summary array start
					$buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]]['0']['pQty']+=$row[csf("production_quantity")];
					$buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]][$row[csf("embel_name")]]['embQty']+=$row[csf("production_quantity")];
					$buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]][$row[csf("production_source")]]['sQty']+=$row[csf("production_quantity")];
					$buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]]['0']['reQty']+=$row[csf("re_production_qty")];
					$buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]][$row[csf("production_source")]]['rejectQty']+=$row[csf("reject_qnty")];
					//Details array start
					if($row[csf("sewing_line")]=="") $row[csf("sewing_line")]=0;
					
					if($row[csf("floor_id")]=="") $row[csf("floor_id")]=0;
					
					if($row[csf("location")]=="") $row[csf("location")]=0;
					
					$prod_dlfl_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("location")]][$row[csf("floor_id")]][$row[csf("sewing_line")]][$row[csf("production_type")]]['0']['pQty']+=$row[csf("production_quantity")];
					$prod_dlfl_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("location")]][$row[csf("floor_id")]][$row[csf("sewing_line")]][$row[csf("production_type")]][$row[csf("embel_name")]]['embQty']+=$row[csf("production_quantity")];
					$prod_dlfl_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("location")]][$row[csf("floor_id")]][$row[csf("sewing_line")]][$row[csf("production_type")]][$row[csf("production_source")]]['sQty']+=$row[csf("production_quantity")];
					$prod_dlfl_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("location")]][$row[csf("floor_id")]][$row[csf("sewing_line")]][$row[csf("production_type")]]['0']['reQty']+=$row[csf("re_production_qty")];
					$prod_dlfl_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("location")]][$row[csf("floor_id")]][$row[csf("sewing_line")]][$row[csf("production_type")]]['0']['rejectQty']+=$row[csf("reject_qnty")];
					$prod_dlfl_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("location")]][$row[csf("floor_id")]][$row[csf("sewing_line")]]['0']['0']['crtQty']+=$row[csf("carton_qty")];
					
					$all_data_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("location")]][$row[csf("floor_id")]]=$row[csf("sewing_line")].'##'.$row[csf("company_id")].'##'.$row[csf("production_source")].'##'.$row[csf("prod_reso_allo")].'__';
				}
			}
			//print_r($prod_dlfl_qty_arr[6152]['02-JAN-16'][4]); die;
			unset($sql_dtls_res);
			//die;
			//$cm_per_dzn=return_library_array( "select job_no, cm_for_sipment_sche from wo_pre_cost_dtls where is_deleted=0 and status_active=1",'job_no','cm_for_sipment_sche');
			$b=1; //date_wise Summary
			$sum_embGmt_qty=$sum_embGmtRec_qty=0;
			foreach($buyer_fullQty_arr as $buyer_id=>$buyer_data)
			{
				if ($b%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$cutting_qty=$printing_qty=$printreceived_qty=$emb_qty=$embRec_qty=$wash_qty=$washRec_qty=$special_qty=$specialRec_qty=$sewIn_inQty=$sewIn_outQty=$sewOut_inQty=$sewOut_outQty=$iron_qty=$reIron_qty=$finish_qty=0;
				$cutting_qty=$buyer_data['1']['0']['pQty'];
				$printing_qty=$buyer_data['2']['1']['embQty'];
				$printreceived_qty=$buyer_data['3']['1']['embQty'];
				$emb_qty=$buyer_data['2']['2']['embQty'];
				$embRec_qty=$buyer_data['3']['2']['embQty'];
				
				$embGmt_qty=$buyer_data['2']['5']['embQty'];
				$embGmtRec_qty=$buyer_data['3']['5']['embQty'];
				
				$wash_qty=$buyer_data['2']['3']['embQty'];
				$washRec_qty=$buyer_data['3']['3']['embQty'];
				$special_qty=$buyer_data['2']['4']['embQty'];
				$specialRec_qty=$buyer_data['3']['4']['embQty'];
				$sewIn_inQty=$buyer_data['4']['0']['pQty'];
				$sewIn_outQty=$buyer_data['4']['3']['sQty'];
				$sewOut_inQty=$buyer_data['5']['0']['pQty'];
				$sewOut_outQty=$buyer_data['5']['3']['sQty'];
				$iron_qty=$buyer_data['7']['0']['pQty'];
				$reIron_qty=$buyer_data['7']['0']['reQty'];
				$finish_qty=$buyer_data['8']['0']['pQty'];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $b; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $b; ?>">
					<td width="30"><? echo $b;?></td>
					<td width="80"><p><? echo $buyer_short_library[$buyer_id]; ?>&nbsp;</p></td>
					<td width="80" align="right"><? echo number_format($cutting_qty); ?></td>
					<td width="80" align="right"><? echo number_format($printing_qty); ?></td>
					<td width="80" align="right"><? echo number_format($printreceived_qty); ?></td>
					<td width="80" align="right"><? echo number_format($emb_qty); ?></td>
					<td width="80" align="right"><? echo number_format($embRec_qty); ?></td>
                    
                    <td width="80" align="right"><? echo number_format($embGmt_qty); ?></td>
					<td width="80" align="right"><? echo number_format($embGmtRec_qty); ?></td>
                    
					<td width="80" align="right"><? echo number_format($wash_qty); ?></td>
					<td width="80" align="right"><? echo number_format($washRec_qty);  ?></td>
					<td width="80" align="right"><? echo number_format($special_qty); ?></td>
					<td width="80" align="right"><? echo number_format($specialRec_qty);  ?></td>
					<td width="80" align="right"><? echo number_format($sewIn_inQty); ?></td>
					<td width="80" align="right"><? echo number_format($sewIn_outQty); ?></td>
					<td width="80" align="right"><? echo number_format($sewOut_inQty); ?></td>
					<td width="80" align="right"><? echo number_format($sewOut_outQty); ?></td>
					<td width="80" align="right"><? echo number_format($iron_qty); ?></td>
					<td width="80" align="right"><? echo number_format($reIron_qty); ?></td>
					<td  align="right"><? echo number_format($finish_qty); ?></td>
				</tr>	
				<?
				$sumCutting_qty+=$cutting_qty;
				$sumPrinting_qty+=$printing_qty;
				$sumPrintreceived_qty+=$printreceived_qty;
				$sumEmb_qty+=$emb_qty;
				$sumEmbRec_qty+=$embRec_qty;
				$sumWash_qty+=$wash_qty;
				$sumWashRec_qty+=$washRec_qty;
				$sumSpecial_qty+=$special_qty;
				$sumSpecialRec_qty+=$specialRec_qty;
				$sumSewIn_inQty+=$sewIn_inQty;
				$sumSewIn_outQty+=$sewIn_outQty;
				$sumSewOut_inQty+=$sewOut_inQty;
				$sumSewOut_outQty+=$sewOut_outQty;
				$sumIron_qty+=$iron_qty;
				$sumReIron_qty+=$reIron_qty;
				$sumFinish_qty+=$finish_qty;
				
				$sum_embGmt_qty+=$embGmt_qty;
				$sum_embGmtRec_qty+=$embGmtRec_qty;
				$b++;	
			}
			?>
			</table>
			<table border="1" class="tbl_bottom"  width="1550" rules="all" id="" >
				 <tr> 
					<td width="30">&nbsp;</td> 
					<td width="80">Total</td> 
					<td width="80"><? echo number_format($sumCutting_qty); ?></td>
					<td width="80"><? echo number_format($sumPrinting_qty); ?></td>
					<td width="80"><? echo number_format($sumPrintreceived_qty); ?></td> 
					<td width="80"><? echo number_format($sumEmb_qty); ?></td>
					<td width="80"><? echo number_format($sumEmbRec_qty); ?></td>
                    
                   <td width="80"><? echo number_format($sum_embGmt_qty); ?></td>
					<td width="80"><? echo number_format($sum_embGmtRec_qty); ?></td>
                    
					<td width="80"><? echo number_format($sumWash_qty); ?></td>
					<td width="80"><? echo number_format($sumWashRec_qty); ?></td>
					<td width="80"><? echo number_format($sumSpecial_qty); ?></td>
					<td width="80"><? echo number_format($sumSpecialRec_qty); ?></td>
					<td width="80"><? echo number_format($sumSewIn_inQty); ?></td>
					<td width="80"><? echo number_format($sumSewIn_outQty); ?></td>  
					<td width="80"><? echo number_format($sumSewOut_inQty); ?></td>  
					<td width="80"><? echo number_format($sumSewOut_outQty); ?></td>   
					<td width="80"><? echo number_format($sumIron_qty); ?></td>
					<td width="80"><? echo number_format($sumReIron_qty); ?></td> 
					<td><? echo number_format($sumFinish_qty); ?></td>
				 </tr>
			 </table>
        </div>
        <div style="clear:both"></div>
        <br /> <?
        if($type==1) //--------------------------------------------Show Date Wise
		{ 
		?>
            <table width="4542" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1">
               <thead>
                    <th width="30">SL</th>    
                    <th width="80">Working Factory</th>
                    <th width="60">Job No</th>
                    <th width="60">Year</th>
                    <th width="130">Order Number</th>
                    <th width="100">Unit Price</th>
                    <th width="70">Sewing SMV</th>
                    <th width="70">Finish SMV</th>
                    <th width="70">Buyer Name</th>
                    <th width="140">Style Name</th>
                    <th width="100">File No</th>
                    <th width="100">Internal Ref</th>
                    <th width="150">Item Name</th>
                    <th width="100">Production Date</th>
                    <th width="80">Cutting (In-house)</th>
                    <th width="80">Cutting (Outside)</th>
                    <th width="80">Sent to Print</th>
                    <th width="80">Rev Print</th>
                    <th width="80">Sent to Emb</th>
                    <th width="80">Rev Emb</th>
                    <th width="80">Sent to Gmts Dyeing</th>
                    <th width="80">Rev. Gmts Dyeing</th>
                    
                    <th width="80">Sent to Wash</th>
                    <th width="80">Rev Wash</th>
                    <th width="80">Sent to Sp. Works</th>
                    <th width="80">Rev Sp. Works</th>
                    <th width="80">Sewing In (Inhouse)</th>
                    <th width="80">Sewing In (Out-bound)</th>
                    <th width="80">Total Sewing Input</th>
                    
                    <th width="80">Sewing Out (Inhouse)</th>
                    <th width="80">Sewing Out (Out-bound)</th>
                    <th width="80">Total Sewing Out</th>
                    <th width="80">Sewing SAH</th>
                    <th width="80">Iron Qty (Inhouse)</th>
                    <th width="80">Iron Qty (Out-bound)</th>
                    <th width="80">Total Iron Qty</th>
                    <th width="80">Total Iron SMV</th>
                    <th width="80">Re-Iron Qty </th>
                    <th width="80">Total Finishing Qty</th>
                    <th width="80">Today Carton</th>
                    <th width="80">In Prod/Dzn</th>
                    <th width="80">Out Prod/Dzn</th>
                    <th width="80">Total Prod/Dzn</th>
                    <th width="100">In CM Value</th>
                    <th width="100">Out CM Value</th>
                    <th width="100">Total CM Value</th>
                    <th width="100">FOB value (On Sewing Out Total)</th>
                    <th width="100">In CM Cost</th>
                    <th width="100">Out CM Cost</th>
                    <th width="100">Total CM Cost</th>
                    <th width="100">Turnover</th>
                    <th>Remarks</th>
                 </thead>
            </table>
            <div style="max-height:425px; overflow-y:scroll; width:4560px" id="scroll_body">
                <table cellspacing="0" border="1" class="rpt_table"  width="4542" rules="all" id="table_body" >
                <?
				$smv_qty_arr=array();
				$sql_smv="select job_no, gmts_item_id,smv_pcs from wo_po_details_mas_set_details";
				$data_smv_array=sql_select($sql_smv);
				foreach($data_smv_array as $row)
				{
					$smv_qty_arr[$row[csf("job_no")]][$row[csf("gmts_item_id")]]=$row[csf("smv_pcs")];
				}
							$tot_embgmtIssue_qty=$tot_embgmtRec_qty=0;
				foreach($all_data_arr as $po_id=>$po_data)
				{
					foreach($po_data as $prod_date=>$prod_date_data)
					{
						$ex_itemdata='';
						$ex_itemdata=array_filter(array_unique(explode('__',$prod_date_data)));
						foreach($ex_itemdata as $data_all)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$buyer_name_dat=""; $job_no_pre=''; $job_no=''; $po_no=''; $buyer_id=''; $style_ref=''; $ref_no=''; $file_no=''; $unit_price=''; $po_qty=''; $ratio=''; $order_uom=''; $job_year=''; $all_job_data=''; $all_job_data='';
							$all_job_data=$job_arr[$po_id];
							
							$ex_job_data=explode('!!',$all_job_data);
							
							$job_no_pre=$ex_job_data[0];
							$job_no=$ex_job_data[1];
							$buyer_name_dat=$ex_job_data[2];
							$style_ref=$ex_job_data[3];
							$po_no=$ex_job_data[4];
							$ref_no=$ex_job_data[5];
							$file_no=$ex_job_data[6];
							$unit_price=$ex_job_data[7];
							$po_qty=$ex_job_data[8];
							$ratio=$ex_job_data[9];
							$order_uom=$ex_job_data[10];
							$job_year=$ex_job_data[11];
							
							$item_id=''; $company_id='';
							$ex_data=array_filter(explode('**',$data_all));
							$item_id=$ex_data[0];
							$company_id=$ex_data[1];
					
							$cutting_qty=$cuttingIn_qty=$cuttingOut_qty=$print_qty=$printRec_qty=$emb_qty=$embRec_qty=$wash_qty=$washRec_qty=$special_qty=$specialRec_qty=$sewIn_inQty=$sewIn_outQty=$sewing_input_total=$sewOut_inQty=$sewOut_outQty=$sewing_output_total=$ironIn_qty=$ironOut_qty=$iron_qty_total=$reIron_qty=$finishIn_qty=$finishOut_qty=$finishing_qty=$carton_qty=$rejFinish_qty=$rejSewing_qty=$reject_Qty=0;
							$cutting_qty=$prod_date_qty_arr[$po_id][$prod_date][$item_id]['1']['0']['pQty'];
							$cuttingIn_qty=$prod_date_qty_arr[$po_id][$prod_date][$item_id]['1']['1']['sQty'];
							$cuttingOut_qty=$prod_date_qty_arr[$po_id][$prod_date][$item_id]['1']['3']['sQty'];
							$print_qty=$prod_date_qty_arr[$po_id][$prod_date][$item_id]['2']['1']['embQty'];
							$printRec_qty=$prod_date_qty_arr[$po_id][$prod_date][$item_id]['3']['1']['embQty'];
							$emb_qty=$prod_date_qty_arr[$po_id][$prod_date][$item_id]['2']['2']['embQty'];
							$embRec_qty=$prod_date_qty_arr[$po_id][$prod_date][$item_id]['3']['2']['embQty'];
							
							$embgmtRec_qty=$prod_date_qty_arr[$po_id][$prod_date][$item_id]['3']['5']['embQty'];
							$embgmtIssue_qty=$prod_date_qty_arr[$po_id][$prod_date][$item_id]['2']['5']['embQty'];
							
							$wash_qty=$prod_date_qty_arr[$po_id][$prod_date][$item_id]['2']['3']['embQty'];
							$washRec_qty=$prod_date_qty_arr[$po_id][$prod_date][$item_id]['3']['3']['embQty'];
							$special_qty=$prod_date_qty_arr[$po_id][$prod_date][$item_id]['2']['4']['embQty'];
							$specialRec_qty=$prod_date_qty_arr[$po_id][$prod_date][$item_id]['3']['4']['embQty'];
							$sewIn_inQty=$prod_date_qty_arr[$po_id][$prod_date][$item_id]['4']['1']['sQty'];
							$sewIn_outQty=$prod_date_qty_arr[$po_id][$prod_date][$item_id]['4']['3']['sQty'];
							$sewOut_inQty=$prod_date_qty_arr[$po_id][$prod_date][$item_id]['5']['1']['sQty'];
							$sewOut_outQty=$prod_date_qty_arr[$po_id][$prod_date][$item_id]['5']['3']['sQty'];
							$ironIn_qty=$prod_date_qty_arr[$po_id][$prod_date][$item_id]['7']['1']['sQty'];
							$ironOut_qty=$prod_date_qty_arr[$po_id][$prod_date][$item_id]['7']['3']['sQty'];
							$reIron_qty=$prod_date_qty_arr[$po_id][$prod_date][$item_id]['7']['0']['reQty'];
							$finishIn_qty=$prod_date_qty_arr[$po_id][$prod_date][$item_id]['8']['1']['sQty'];
							$finishOut_qty=$prod_date_qty_arr[$po_id][$prod_date][$item_id]['8']['3']['sQty'];
							$carton_qty=$prod_date_qty_arr[$po_id][$prod_date][$item_id]['0']['0']['crtQty'];
							$rejFinish_qty=$prod_date_qty_arr[$po_id][$prod_date][$item_id]['8']['0']['rejectQty'];
							$rejSewing_qty=$prod_date_qty_arr[$po_id][$prod_date][$item_id]['5']['0']['rejectQty'];
						?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
								<td width="30"><? echo $i;?></td>
								<td width="80"><p><? echo $company_short_library[$company_id]; ?></p></td>
								<td width="60" align="center"><p><? echo $job_no_pre;?></p></td>
								<td width="60" align="center"><p><? echo $job_year; ?></p></td>
								<td width="130"><p><a href="##" onClick="openmypage_order(<? echo $po_id;?>,<? echo $item_id;?>,'orderQnty_popup');" ><? echo $po_no; ?></a></p></td>
								<td width="100" align="center"><p><? echo $unit_price; ?></p></td>
								<td width="70" align="center"><p><? echo $job_item_smv_arr[$po_id][$item_id]['smv_pcs']; ?></p></td>
								<td width="70" align="center"><p><? echo $job_item_smv_arr[$po_id][$item_id]['finsmv_pcs']; ?></p></td>
								<td width="70"><p><? echo $buyer_short_library[$buyer_name_dat]; ?></p></td>
								<td width="140"><p><? echo $style_ref ?></p></td>
								<td width="100" align="center"><p><? echo $file_no; ?></p></td>
								<td width="100" align="center"><p><? echo $ref_no; ?></p></td>
								<td width="150"><p><? echo $garments_item[$item_id]; ?></p></td>
								<td width="100" align="center"><p><? echo change_date_format($prod_date); ?></p></td>
								<td width="80" align="right"><a href="##" onClick="openmypage_popup('<? echo $po_id; ?>','<? echo $prod_date; ?>','<? echo $item_id;?>','1','Cutting Info','cutting_popup');" ><? echo $cuttingIn_qty; ?></a></td>
								<td width="80" align="right"><a href="##" onClick="openmypage_popup('<? echo $po_id; ?>','<? echo $prod_date; ?>','<? echo $item_id;?>','3','Cutting Info','cutting_popup');" ><? echo $cuttingOut_qty; ?></a></td>
								<td width="80" align="right"><a href="##" onClick="openmypage_popup(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,'0','Printing Issue Info','printing_issue_popup');" ><? echo $print_qty; ?></a></td>
								<td width="80" align="right"><a href="##" onClick="openmypage_popup(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,'0','Priniting Receive Info','printing_receive_popup');" ><? echo $printRec_qty; ?></a></td>
								<td width="80" align="right"><a href="##" onClick="openmypage_popup(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,'0','Embroidery Issue Info','embroi_issue_popup');" ><? echo $emb_qty; ?></a></td>
								<td width="80" align="right"><a href="##" onClick="openmypage_popup(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,'0','Embroidery Receive Info','embroi_receive_popup');" ><? echo $embRec_qty; ?></a></td>
                                
                                <td width="80" align="right"><a href="##" onClick="openmypage_popup(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,'2','Embroidery Receive Info','embroi_gmt_dyeing_popup');" ><? echo $embgmtIssue_qty; ?></a></td>
                                
                                <td width="80" align="right"><a href="##" onClick="openmypage_popup(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,'3','Embroidery Receive Info','embroi_gmt_dyeing_popup');" ><? echo $embgmtRec_qty; ?></a></td>
                                
								<td width="80" align="right"><a href="##" onClick="openmypage_popup(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,'0','Wash Issue Info','wash_issue_popup');" ><? echo $wash_qty; ?></a></td>
								<td width="80" align="right"><a href="##" onClick="openmypage_popup(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,'0','Wash Receive Info','wash_receive_popup');" ><? echo $washRec_qty; ?></a></td>
								<td width="80" align="right"><a href="##" onClick="openmypage_popup(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,'0','Special Works Issue Info','sp_issue_popup');" ><? echo $special_qty; ?></a></td>
								<td width="80" align="right"><a href="##" onClick="openmypage_popup(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,'0','Special Works Receive Info','sp_receive_popup');" ><? echo $specialRec_qty; ?></a></td>
								<td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,'1','4','sewingQnty_popup');" ><? echo $sewIn_inQty; ?></a></td>
								<td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,'3','4','sewingQnty_popup');" ><? echo $sewIn_outQty; ?></a></td>
								<td width="80" align="right"><? $sewing_input_total=$sewIn_inQty+$sewIn_outQty; echo $sewing_input_total; ?></td>
								<td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,'1','5','sewingQnty_popup');" ><? echo $sewOut_inQty; ?></a></td>
								<td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,'3','5','sewingQnty_popup');" ><? echo $sewOut_outQty; ?></a></td>
								<td width="80" align="right"><? $sewing_output_total=$sewOut_inQty+$sewOut_outQty; echo $sewing_output_total; ?></td>
								<td width="80" align="right"><?
										$sew_out_smv=($sewOut_outQty*$job_item_smv_arr[$po_id][$item_id]['smv_pcs'])/60;
										$sew_in_smv=($sewOut_inQty*$job_item_smv_arr[$po_id][$item_id]['smv_pcs'])/60;
										echo number_format($sah_out+$sah_in,4);
									?></td>
								<td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,'1','0','ironQnty_popup');" ><? echo $ironIn_qty; ?></a></td>
								<td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,'3','0','ironQnty_popup');" ><? echo $ironOut_qty; ?></a></td>
								<td width="80" align="right"><? $iron_qty_total=$ironIn_qty+$ironOut_qty; echo $iron_qty_total; ?> </td>
								<td width="80" align="right"><? $iron_smv=($iron_qty_total*$job_item_smv_arr[$po_id][$item_id]['finsmv_pcs']); echo $iron_smv; ?></td>
								<td width="80" align="right"><? echo $reIron_qty; ?></td>
								<td width="80" align="right"><? $finishing_qty=$finishIn_qty+$finishOut_qty; echo $finishing_qty; ?></td>
								<td width="80" align="right"><? echo $carton_qty; ?></td>
								<?
								$in_prod_dzn=$sewOut_inQty/ 12; $total_in_prod_dzn+=$in_prod_dzn;
								$out_prod_dzn=$sewOut_outQty/ 12; $total_out_prod_dzn+=$out_prod_dzn;
								$prod_dzn=$sewing_output_total/ 12; $total_prod_dzn+=$prod_dzn;
								
								$dzn_qnty=0; $in_cm_value=0; $out_cm_value=0; $cm_value=0; $in_cm_cost=0; $out_cm_cost=0; $cm_cost=0;
								if($costing_per_id_library[$job_no]==1) $dzn_qnty=12;
								else if($costing_per_id_library[$job_no]==3) $dzn_qnty=12*2;
								else if($costing_per_id_library[$job_no]==4) $dzn_qnty=12*3;
								else if($costing_per_id_library[$job_no]==5) $dzn_qnty=12*4;
								else $dzn_qnty=1;
								//echo $dzn_qnty."***";
								$cm_cost_margin=($tot_cm_cost_arr[$job_no]/$dzn_qnty)+$tot_margin_pcs_arr[$job_no];
								$dzn_qnty=$dzn_qnty*$ratio;
								
								$in_cm_value=($tot_cost_arr[$job_no]/$dzn_qnty)*$sewOut_inQty;
								$total_in_cm_value+=$in_cm_value;
								
								$out_cm_value=($tot_cost_arr[$job_no]/$dzn_qnty)*$sewOut_outQty;
								$total_out_cm_value+=$out_cm_value;
								
								$cm_value=($tot_cost_arr[$job_no]/$dzn_qnty)*$sewing_output_total;
								$total_cm_value+=$cm_value;
								
								$in_cm_cost=($tot_cm_cost_arr[$job_no]/$dzn_qnty)*$sewOut_inQty;
								$total_in_cm_cost+=$in_cm_cost;
								
								$out_cm_cost=($tot_cm_cost_arr[$job_no]/$dzn_qnty)*$sewOut_outQty;
								$total_out_cm_cost+=$out_cm_cost;
								
								$cm_cost=($tot_cm_cost_arr[$job_no]/$dzn_qnty)*$sewing_output_total;
								$total_cm_cost+=$cm_cost;
								?>
								<td width="80" align="right"><? if($in_prod_dzn!=0) echo number_format($in_prod_dzn,2); else echo "0"; ?></td>
								<td width="80" align="right"><? if($out_prod_dzn!=0) echo number_format($out_prod_dzn,2); else echo "0"; ?></td>
								<td width="80" align="right"><? if($prod_dzn!=0) echo number_format($prod_dzn,2); else echo "0"; ?></td>
								<td width="100" align="right" ><? echo number_format($in_cm_value,2,'.',''); ?></td>
								<td width="100" align="right" ><? echo number_format($out_cm_value,2,'.',''); ?></td>
								<td width="100" align="right" title="(Budget Wise CM For Shipment Schedule/Dzn Qty)*Total Sewing Out"><? echo number_format($cm_value,2,'.','');?></td>
								<td width="100" align="right" title="Job Wise Unit Price*Total Sewing Out"><? echo number_format($unit_price*$sewing_output_total,2,'.',''); $total_cm_value+=$unit_price*$sewing_output_total; ?></td>
								<td width="100" align="right" ><? echo number_format($in_cm_cost,2,'.',''); ?></td>
								<td width="100" align="right" ><? echo number_format($out_cm_cost,2,'.',''); ?></td>
								<td width="100" align="right" title="(Budget Wise CM Cost/Dzn Qnty)*Total Sewing Out"><? echo number_format($cm_cost,2,'.',''); ?></td>
								<td width="100" align="right" title="(Finish Qnty*(Budge Wise CM Cost/Dzn Qnty)+Budge Wise Margin Pcs Per Set)"><? echo number_format($finishing_qty*$cm_cost_margin,2,'.',''); ?></td>
								<td align="center"><a href="##"  onclick="openmypage_remark(<? echo $po_id;?>,'date_wise_production_report');" > Veiw </a></td>
							</tr>
							<?	
							$all_order_sewQty_array[$po_id]+=$sewing_output_total;
							$tot_cutting_qty+=$cutting_qty;
							$tot_cutIn_qty+=$cuttingIn_qty;
							$tot_cutOut_qty+=$cuttingOut_qty;
							$tot_print_qty+=$print_qty;
							$tot_printRec_qty+=$printRec_qty;
							$tot_emb_qty+=$emb_qty;
							$tot_embRec_qty+=$embRec_qty;
							$tot_wash_qty+=$wash_qty;
							$tot_washRec_qty+=$washRec_qty;
							$tot_special_qty+=$special_qty;
							$tot_specialRec_qty+=$specialRec_qty;
							$tot_sewIn_inQty+=$sewIn_inQty;
							$tot_sewIn_outQty+=$sewIn_outQty;
							$tot_sewing_input+=$sewing_input_total;
							$tot_sewOut_inQty+=$sewOut_inQty;
							$tot_sewOut_outQty+=$sewOut_outQty;
							$tot_sewing_output+=$sewing_output_total;
							$tot_ironIn_qty+=$ironIn_qty;
							$tot_ironOut_qty+=$ironOut_qty;
							$tot_iron_qty+=$iron_qty_total;
							$tot_reIron_qty+=$reIron_qty;
							$tot_finishIn_qty+=$finishIn_qty;
							$tot_finishOut_qty+=$finishOut_qty;
							$tot_finishing_qty+=$finishing_qty; 
							$tot_carton_qty+=$carton_qty;
							$total_prod_dzn+=$prod_dzn;
							$tot_rejFinish_qty+=$rejFinish_qty;
							$tot_rejSewing_qty+=$rejSewing_qty;
							$tot_reject_Qty+=$reject_Qty;
							
							$tot_embgmtIssue_qty+=$embgmtIssue_qty;
							$tot_embgmtRec_qty+=$embgmtRec_qty;
							
							$i++;
						}
					}
				}//end foreach 1st
				?>
				</table>
				</div> 
				<table border="1" class="tbl_bottom"  width="4542" rules="all" id="" >
					<tr>
						<td width="30">&nbsp;</td>
						<td width="80">&nbsp;</td>
						<td width="60">&nbsp;</td>
						<td width="60">&nbsp;</td>
						<td width="130">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="70">&nbsp;</td>
						<td width="70">&nbsp;</td>
						<td width="70">&nbsp;</td>
						<td width="140">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="150">&nbsp;</td>
						<td width="100">Total</td> 
						<td width="80" align="right" id="total_cut_inhouse"><? echo $tot_cutIn_qty;?></td>
						<td width="80" align="right" id="total_cut_outside"><? echo $tot_cutOut_qty;?></td> 
						<td width="80" align="right" id="total_printissue_td"><? echo $tot_print_qty; ?></td> 
						<td width="80" align="right" id="total_printrcv_td"><? echo $tot_printRec_qty; ?></td>
						<td width="80" align="right" id="total_emb_iss"><? echo $tot_emb_qty; ?></td> 
						<td width="80" align="right" id="total_emb_re"><? echo $tot_embRec_qty;  ?></td>
                        <td width="80" align="right" id="total_emb_sent_gmt"><? echo $tot_embgmtIssue_qty;  ?></td>
                        <td width="80" align="right" id="total_emb_rec_gmt"><? echo $tot_embgmtRec_qty;  ?></td>
						<td width="80" align="right" id="total_wash_iss"><? echo $tot_wash_qty; ?></td> 
						<td width="80" align="right" id="total_wash_re"><? echo $tot_washRec_qty;  ?></td>
						<td width="80" align="right" id="total_sp_iss"><? echo $tot_special_qty; ?></td> 
						<td width="80" align="right" id="total_sp_re"><? echo $tot_specialRec_qty;  ?></td>
						<td width="80" align="right" id="total_sewin_inhouse_td"><? echo $tot_sewIn_inQty; ?></td>
						<td width="80" align="right" id="total_sewin_outbound_td"><? echo $tot_sewIn_outQty; ?></td>
						<td width="80" align="right" id="total_sewin_td"><? echo $tot_sewing_input; ?></td> 
						<td width="80" align="right" id="total_sewout_inhouse_td"><? echo $tot_sewOut_inQty; ?></td>
						<td width="80" align="right" id="total_sewout_outbound_td"><? echo $tot_sewOut_outQty; ?></td>
						<td width="80" align="right" id="total_sewout_td"><? echo $tot_sewing_output; ?></td>
						<td width="80" id="total_sah_td">&nbsp;</td>
						<td width="80" align="right" id="total_in_iron_td"><? echo $tot_ironIn_qty; ?></td>
						<td width="80" align="right" id="total_out_iron_td"><? echo $tot_ironOut_qty; ?></td>
						<td width="80" align="right" id="total_iron_td"><? echo $tot_iron_qty; ?></td> 
						<td width="80" align="right" id="total_iron_smv_td"><? echo $total_iron_smv; ?></td>
						<td width="80" align="right" id="total_re_iron_td"><? echo $tot_reIron_qty; ?></td>
						<td width="80" align="right" id="total_finish_td"><? echo $tot_finishing_qty; ?></td>   
						<td width="80" align="right" id="total_carton_td"><? echo $tot_carton_qty; ?></td> 
						<td width="80" align="right" id="value_total_in_prod_dzn_td"><? echo number_format($total_in_prod_dzn,2); ?></td>
						<td width="80" align="right" id="value_total_out_prod_dzn_td"><? echo number_format($total_out_prod_dzn,2); ?></td>
						<td width="80" align="right" id="value_total_prod_dzn_td"><?  echo number_format($total_prod_dzn,2); ?></td>
						<td width="100" align="right" id="value_total_in_cm_value_td"><? echo number_format($total_in_cm_value,2); ?></td>
						<td width="100" align="right" id="value_total_out_cm_value_td"><? echo number_format($total_out_cm_value,2); ?></td>
						<td width="100" align="right" id="value_total_cm_value_td"><? echo number_format($total_cm_value,2); ?></td>
						<td width="100" align="right" id="value_total_fob_value"><? echo number_format($total_fob_value,2); ?></td>
						<td width="100" align="right" id="value_total_in_cm_cost"><? echo number_format($total_in_cm_cost,2); ?></td>
						<td width="100" align="right" id="value_total_out_cm_cost"><? echo number_format($total_out_cm_cost,2); ?></td>
						<td width="100" align="right" id="value_total_cm_cost"><? echo number_format($total_cm_cost,2); ?></td>
						<td width="100">&nbsp;</td>
						<td>&nbsp;</td>
				   </tr>
				</table>
			</div>
		<?
        }// end if condition of type //-------------------------------------------Show Date Location Floor & Line Wise------------------------	
		else if($type==2)
		{
			ob_start();
			?>
            <table width="4542" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1">
                <thead>
                    <th width="30">SL</th>    
                    <th width="80">Working Factory</th>
                    <th width="60">Job No</th>
                    <th width="60">Year</th>
                    <th width="130">Order Number</th>
                    <th width="70">Sewing SMV</th>
                    <th width="70">Iron SMV</th>
                    <th width="70">Buyer Name</th>
                    <th width="130">Style Name</th>
                    <th width="100">File No</th>
                    <th width="100">Internal Ref</th>
                    <th width="130">Item Name</th>
                    <th width="100">Production Date</th>
                    <th width="100">Status</th>
                    <th width="100">Location</th>
                    <th width="100">Floor</th>
                    <th width="100">Sewing Line No</th>
                    <th width="80">Cutting (In-house)</th>
                    <th width="80">Cutting (Outside)</th>
                    <th width="80">Sent to Print</th>
                    <th width="80">Rev Print</th>
                    <th width="80">Sent to Emb</th>
                    <th width="80">Rev Emb</th>
                    
                    <th width="80">Sent to Gmts Dyeing</th>
                    <th width="80"> Rev. Gmts Dyeing</th>
                    
                    <th width="80">Sent to Wash</th>
                    <th width="80">Rev Wash</th>
                    <th width="80">Sent to Sp. Works</th>
                    <th width="80">Rev Sp. Works</th>
                    <th width="80">Sewing In (Inhouse)</th>
                    <th width="80">Sewing In (Out-bound)</th>
                    <th width="80">Total Sewing Input</th>
                    <th width="80">Sewing Out (Inhouse)</th>
                    <th width="80">Sewing Out (Out-bound)</th>
                    <th width="80">Total Sewing Out</th>
                    <th width="80">Iron Qty (Inhouse)</th>
                    <th width="80">Iron Qty (Out-bound)</th>
                    <th width="80">Total Iron Qty </th>
                    <th width="80">Total Iron SMV </th>
                    <th width="80">Re-Iron Qty </th>
                    <th width="80">Total Finishing Qty</th>
                    <th width="80">Today Carton</th>
                    
                    <th width="80">In Prod/Dzn</th>
                    <th width="80">Out Prod/Dzn</th>
                    <th width="80">Total Prod/Dzn</th>
                    <th width="100">In CM Value</th>
                    <th width="100">Out CM Value</th>
                    <th width="100">Total CM Value</th>
                    <th width="100">In CM Cost</th>
                    <th width="100">Out CM Cost</th>
                    <th width="100">Total CM Cost</th>
                    <th>Remarks</th>
                </thead>
            </table>
            <div style="max-height:425px; overflow-y:scroll; width:4560px" id="scroll_body">
            <table cellspacing="0" border="1" class="rpt_table"  width="4542" rules="all" id="table_body" >
            <?
			$tot_embgmtIssue_qty=$tot_embgmtRec_qty=0;
            foreach($all_data_arr as $po_id=>$po_data)
			{
				foreach($po_data as $prod_date=>$prod_date_data)
				{
					foreach($prod_date_data as $item_id=>$item_data)
					{
						foreach($item_data as $location_id=>$location_data)
						{
							foreach($location_data as $floor_id=>$floor_data)
							{
								$ex_linedata='';
								$ex_linedata=array_filter(array_unique(explode('__',$floor_data)));
								foreach($ex_linedata as $data_all)
								{
									//$row[csf("sewing_line")].'##'.$row[csf("company_id")].'##'.$row[csf("production_source")].'##'.$row[csf("prod_reso_allo")].'__';
									
									$buyer_name_dat=""; $job_no_pre=''; $job_no=''; $po_no=''; $buyer_id=''; $style_ref=''; $ref_no=''; $file_no=''; $unit_price=''; $po_qty=''; $ratio=''; $order_uom=''; $job_year=''; $all_job_data=''; $all_job_data='';
									$all_job_data=$job_arr[$po_id];
									
									$ex_job_data=explode('!!',$all_job_data);
									
									$job_no_pre=$ex_job_data[0];
									$job_no=$ex_job_data[1];
									$buyer_name_dat=$ex_job_data[2];
									$style_ref=$ex_job_data[3];
									$po_no=$ex_job_data[4];
									$ref_no=$ex_job_data[5];
									$file_no=$ex_job_data[6];
									$unit_price=$ex_job_data[7];
									$po_qty=$ex_job_data[8];
									$ratio=$ex_job_data[9];
									$order_uom=$ex_job_data[10];
									$job_year=$ex_job_data[11];
									
									$line_id=''; $company_id=''; $prod_source=''; $resource_allo='';
									$ex_data=array_filter(explode('##',$data_all));
									//echo $floor_id.'='; //die;
									if($ex_data[0]=="") $line_id=0;
									else $line_id=$ex_data[0];
									$company_id=$ex_data[1];
									$prod_source=$ex_data[2];
									$resource_allo=$ex_data[3];
									
                    				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									
									$cuttingIn_qty=$cuttingOut_qty=$print_qty=$printRec_qty=$emb_qty=$embRec_qty=$wash_qty=$washRec_qty=$special_qty=$specialRec_qty=$sewIn_inQty=$sewIn_outQty=$sewing_input_total=$sewOut_inQty=$sewOut_outQty=$sewing_output_total=$ironIn_qty=$ironOut_qty=$iron_qty_total=$reIron_qty=$finishIn_qty=$finishOut_qty=$finishing_qty=$carton_qty=$rejFinish_qty=$rejSewing_qty=$reject_Qty=0;
									$cuttingIn_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$location_id][$floor_id][$line_id]['1']['1']['sQty'];
									$cuttingOut_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$location_id][$floor_id][$line_id]['1']['3']['sQty'];
									$print_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$location_id][$floor_id][$line_id]['2']['1']['embQty'];
									$printRec_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$location_id][$floor_id][$line_id]['3']['1']['embQty'];
									$emb_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$location_id][$floor_id][$line_id]['2']['2']['embQty'];
									$embRec_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$location_id][$floor_id][$line_id]['3']['2']['embQty'];
									
									$emb_gmt_issue_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$location_id][$floor_id][$line_id]['2']['5']['embQty'];
									$emb_gmt_recv_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$location_id][$floor_id][$line_id]['3']['5']['embQty'];
									
									$wash_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$location_id][$floor_id][$line_id]['2']['3']['embQty'];
									$washRec_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$location_id][$floor_id][$line_id]['3']['3']['embQty'];
									$special_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$location_id][$floor_id][$line_id]['2']['4']['embQty'];
									$specialRec_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$location_id][$floor_id][$line_id]['3']['4']['embQty'];
									
									$sewIn_inQty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$location_id][$floor_id][$line_id]['4']['1']['sQty'];
									$sewIn_outQty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$location_id][$floor_id][$line_id]['4']['3']['sQty'];
									$sewOut_inQty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$location_id][$floor_id][$line_id]['5']['1']['sQty'];
									$sewOut_outQty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$location_id][$floor_id][$line_id]['5']['3']['sQty'];
									
									$ironIn_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$location_id][$floor_id][$line_id]['7']['1']['sQty'];
									$ironOut_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$location_id][$floor_id][$line_id]['7']['3']['sQty'];
									$reIron_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$location_id][$floor_id][$line_id]['7']['0']['reQty'];
									$finishIn_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$location_id][$floor_id][$line_id]['8']['1']['sQty'];
									$finishOut_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$location_id][$floor_id][$line_id]['8']['3']['sQty'];
									$carton_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$location_id][$floor_id][$line_id]['0']['0']['crtQty'];
									$rejFinish_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$location_id][$floor_id][$line_id]['8']['0']['rejectQty'];
									$rejSewing_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$location_id][$floor_id][$line_id]['5']['0']['rejectQty'];	
                    
									$sewing_line='';
									if($resource_allo==1)
									{
										$line_number=explode(",",$prod_reso_arr[$line_id]);
										foreach($line_number as $val)
										{
											if($sewing_line=='') $sewing_line=$line_library[$val]; else $sewing_line.=", ".$line_library[$val];
										}
									}
									else $sewing_line=$line_library[$line_id]; 
                    				//$item_smv=$smv_qty_arr[$pro_date_sql_row[csf("job_no_mst")]][$pro_date_sql_row[csf("item_number_id")]];
               						?>
                                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                                        <td width="30"><? echo $i;?></td>
                                        <td width="80"><p><? echo $company_short_library[$company_id]; ?></p></td>
                                        <td width="60" align="center"><p><? echo $job_no_pre; ?></p></td>
                                        <td width="60" align="center"><p><? echo $job_year;?></p></td>
                                        <td width="130"><p><a href="##" onClick="openmypage_order(<? echo $po_id;?>,<? echo $item_id; ?>,'orderQnty_popup');" ><? echo $po_no; ?></a></p></td>
                                        <td width="70" align="center"><p><? echo $job_item_smv_arr[$po_id][$item_id]["smv_pcs"]; ?></p></td>
                                        <td width="70" align="center"><p><? echo $job_item_smv_arr[$po_id][$item_id]["finsmv_pcs"]; ?></p></td>
                                        <td width="70"><p><? echo $buyer_short_library[$buyer_name_dat]; ?></p></td>
                                        <td width="130"><p><? echo $style_ref; ?></p></td>
                                        <td width="100"><p><? echo $file_no; ?></p></td>
                                        <td width="100"><p><? echo $ref_no; ?></p></td>
                                        <td width="130"><p><? echo $garments_item[$item_id]; ?></p></td>
                                        <td width="100" align="center"><p><? echo change_date_format($prod_date); ?></p></td>
                                        <td width="100"><p><? echo $knitting_source[$prod_source]; ?></p></td>
                                        <td width="100"><p><? echo $location_library[$location_id]; ?></p></td>
                                        <td width="100"><p><? echo $floor_library[$floor_id]; ?></p></td>
                                        <td width="100" align="center" title="<? echo $line_id; ?>"><p><? echo $sewing_line; ?></p></td>
                                        <td width="80" align="right"><a href="##" onClick="openmypage_popup_location('<? echo $po_id; ?>','<? echo $prod_date; ?>','<? echo $item_id;?>','<? echo $location_id;?>','<? echo $floor_id;?>','<? echo $line_id;?>','1','Cutting Info','cutting_popup_location');" ><? echo $cuttingIn_qty; ?></a></td>
                                        <td width="80" align="right"><a href="##" onClick="openmypage_popup_location('<? echo $po_id; ?>','<? echo $prod_date; ?>','<? echo $item_id;?>','<? echo $location_id;?>','<? echo $floor_id;?>','<? echo $line_id;?>','3','Cutting Info','cutting_popup_location');"><? echo $cuttingOut_qty;?></a></td>
                                        <td width="80" align="right"><a href="##" onClick="openmypage_popup_location(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $location_id;?>,<? echo $floor_id;?>,<? echo $line_id;?>,'1','Printing Issue Info','printing_issue_popup_location');"><? echo $print_qty; ?></a></td>
                                        <td width="80" align="right"><a href="##" onClick="openmypage_popup_location(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $location_id;?>,<? echo $floor_id;?>,<? echo $line_id;?>,'3','Printing Receive Info','printing_receive_popup_location');" ><? echo $printRec_qty; ?></a></td>
                                        <td width="80" align="right"><a href="##" onClick="openmypage_popup_location(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $location_id;?>,<? echo $floor_id;?>,<? echo $line_id;?>,'1','Embroidery Issue Info','embroi_issue_popup_location');" ><? echo $emb_qty; ?></a></td>
                                        <td width="80" align="right"><a href="##" onClick="openmypage_popup_location(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $location_id;?>,<? echo $floor_id;?>,<? echo $line_id;?>,'3','Embroidery Receive Info','embroi_receive_popup_location');" ><? echo $embRec_qty; ?></a></td>
                                        
                                        <td width="80" align="right"><a href="##" onClick="openmypage_popup_location(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $location_id;?>,<? echo $floor_id;?>,<? echo $line_id;?>,'2','Embroidery Issue Info','embroi_gmt_dyeing_popup_location');" ><? echo $emb_gmt_issue_qty; ?></a></td>
                                        <td width="80" align="right"><a href="##" onClick="openmypage_popup_location(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $location_id;?>,<? echo $floor_id;?>,<? echo $line_id;?>,'3','Embroidery Receive Info','embroi_gmt_dyeing_popup_location');" ><? echo $emb_gmt_recv_qty; ?></a></td>
                                        
                                        <td width="80" align="right"><a href="##" onClick="openmypage_popup_location(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $location_id;?>,<? echo $floor_id;?>,<? echo $line_id;?>,'1','Wash Issue Info','wash_issue_popup_location');" ><? echo $wash_qty; ?></a></td>
                                        <td width="80" align="right"><a href="##" onClick="openmypage_popup_location(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $location_id;?>,<? echo $floor_id;?>,<? echo $line_id;?>,'3','Wash Receive Info','wash_receive_popup_location');" ><? echo $washRec_qty; ?></a></td>
                                        <td width="80" align="right"><a href="##" onClick="openmypage_popup_location(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $location_id;?>,<? echo $floor_id;?>,<? echo $line_id;?>,'1','Spetial Work Info','sp_issue_popup_location');" ><? echo $special_qty; ?></a></td>
                                        <td width="80" align="right"><a href="##" onClick="openmypage_popup_location(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $location_id;?>,<? echo $floor_id;?>,<? echo $line_id;?>,'3','Spetial Work Info','sp_receive_popup_location');" ><? echo $specialRec_qty; ?></a></td>
                                        <td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,'1','4','sewingQnty_popup');" ><? echo $sewIn_inQty; ?></a></td>
                                        <td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,'3','4','sewingQnty_popup');" ><? echo $sewIn_outQty; ?></a></td>
                                        <td width="80" align="right"><? $sewing_input_total=$sewIn_inQty+$sewIn_outQty; echo $sewing_input_total; ?></td>
                                        <td width="80" align="right"><a href="##" onclick="openmypage_sew_output2(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,'1','5','sewingQnty_popup',<? echo $location_id;?>,<? echo $floor_id;?>,<? echo $line_id;?>);" ><? echo $sewOut_inQty; ?></a></td>
                                        <td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,'3','5','sewingQnty_popup');" ><? echo $sewOut_outQty; ?></a></td>
                                        <td width="80" align="right"><? $sewing_output_total=$sewOut_inQty+$sewOut_outQty; echo $sewing_output_total; ?></td>
                                        <td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,'1','0','ironQnty_popup');" ><? echo $ironIn_qty; ?></a></td>
                                        <td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,'3','0','ironQnty_popup');" ><? echo $ironOut_qty; ?></a></td>
                                        <td width="80" align="right"><? echo $iron_qty_total=$ironIn_qty+$ironOut_qty; echo $iron_qty_total; ?></td>
                                        <td width="80" align="right"><? $iron_smv=($iron_qty_total*$job_item_smv_arr[$po_id][$item_id]['finsmv_pcs']); echo $iron_smv; ?></td>
                                        <td width="80" align="right"><? echo $reIron_qty; ?></td>
                                        <td width="80" align="right"><? $finishing_qty=$finishIn_qty+$finishOut_qty; echo $finishing_qty; ?></td>
                                        <td width="80" align="right"><? echo $carton_qty; ?></td>
                                        <?
                                        $in_prod_dzn=$sewOut_inQty/12;
                                        $total_in_prod_dzn+=$in_prod_dzn; 
                                        
                                        $out_prod_dzn=$sewOut_outQty/12;
                                        $total_out_prod_dzn+=$out_prod_dzn;
                                        
                                        $prod_dzn=$sewing_output_total/ 12 ;
                                        $total_prod_dzn+=$prod_dzn; 
                                        
                                        $dzn_qnty=0; $cm_value=0; $cm_cost=0;
                                        if($costing_per_id_library[$job_no]==1) $dzn_qnty=12;
                                        else if($costing_per_id_library[$job_no]==3) $dzn_qnty=12*2;
                                        else if($costing_per_id_library[$job_no]==4) $dzn_qnty=12*3;
                                        else if($costing_per_id_library[$job_no]==5) $dzn_qnty=12*4;
                                        else $dzn_qnty=1;
                                        
                                        $dzn_qnty=$dzn_qnty*$pro_date_sql_row[csf('ratio')];
                                        
                                        $in_cm_value=($tot_cost_arr[$job_no]/$dzn_qnty)*$sewOut_inQty;
                                        $total_in_cm_value+=$in_cm_value;
                                        
                                        $out_cm_value=($tot_cost_arr[$job_no]/$dzn_qnty)*$sewOut_outQty;
                                        $total_out_cm_value+=$out_cm_value;
                                        
                                        $cm_value=($tot_cost_arr[$job_no]/$dzn_qnty)*$sewing_output_total;
                                        $total_cm_value+=$cm_value;
                                        
                                        $in_cm_cost=($tot_cm_cost_arr[$job_no]/$dzn_qnty)*$sewOut_inQty;
                                        $total_in_cm_cost+=$in_cm_cost;
                                        
                                        $out_cm_cost=($tot_cm_cost_arr[$job_no]/$dzn_qnty)*$sewOut_outQty;
                                        $total_out_cm_cost+=$out_cm_cost;
                                        
                                        $cm_cost=($tot_cm_cost_arr[$job_no]/$dzn_qnty)*$sewing_output_total;
                                        $total_cm_cost+=$cm_cost;
                                        ?>
                                        <td width="80" align="right"><? if($prod_dzn!=0) echo number_format($in_prod_dzn,2); else echo "0"; ?></td>
                                        <td width="80" align="right"><? if($prod_dzn!=0) echo number_format($out_prod_dzn,2); else echo "0"; ?></td>
                                        <td width="80" align="right"><? if($prod_dzn!=0) echo number_format($prod_dzn,2); else echo "0"; ?></td>
                                        <td align="right" width="100"><? echo number_format($in_cm_value,2,'.',''); ?></td>
                                        <td align="right" width="100"><? echo number_format($out_cm_value,2,'.',''); ?></td>
                                        <td align="right" width="100" title="(Budget Wise CM For Shipment Schedule/Dzn Qnty)*Total Sewing Out"><? echo number_format($cm_value,2,'.',''); ?></td>
                                        <td align="right" width="100"><? echo number_format($in_cm_cost,2,'.',''); ?></td>
                                        <td align="right" width="100"><? echo number_format($out_cm_cost,2,'.',''); ?></td>
                                        <td align="right" width="100" title="(Budget Wise CM Cost/Dzn Qnty)*Total Sewing Out"><? echo number_format($cm_cost,2,'.',''); ?></td>
                                        <td align="center"><a href="##" onclick="openmypage_remark(<? echo $po_id;?>,'date_wise_production_report');" > Veiw </a></td>
                                    </tr>
									<?
                                    $all_order_sewQty_array[$po_id]+=$sewing_output_total;
									$tot_cuttingIn_qty+=$cuttingIn_qty;
									$tot_cuttingOut_qty+=$cuttingOut_qty;
									
									$tot_print_qty+=$print_qty;
									$tot_printRec_qty+=$printRec_qty;
									$tot_emb_qty+=$emb_qty;
									$tot_embRec_qty+=$embRec_qty;
									$tot_wash_qty+=$wash_qty;
									$tot_washRec_qty+=$washRec_qty;
									$tot_special_qty+=$special_qty;
									$tot_specialRec_qty+=$specialRec_qty;
									$tot_sewIn_inQty+=$sewIn_inQty;
									$tot_sewIn_outQty+=$sewIn_outQty;
									$tot_sewing_input+=$sewing_input_total;
									$tot_sewOut_inQty+=$sewOut_inQty;
									$tot_sewOut_outQty+=$sewOut_outQty;
									$tot_sewing_output+=$sewing_output_total;
									$tot_ironIn_qty+=$ironIn_qty;
									$tot_ironOut_qty+=$ironOut_qty;
									$tot_iron_qty+=$iron_qty_total;
									$tot_reIron_qty+=$reIron_qty;
									$tot_finishIn_qty+=$finishIn_qty;
									$tot_finishOut_qty+=$finishOut_qty;
									$tot_finishing_qty+=$finishing_qty;
									$tot_carton_qty+=$carton_qty;
									$tot_rejFinish_qty+=$rejFinish_qty;
									$tot_rejSewing_qty+=$rejSewing_qty;
									$tot_reject_Qty+=$reject_Qty;
									
									$tot_embgmtIssue_qty+=$emb_gmt_issue_qty;
									$tot_embgmtRec_qty+=$emb_gmt_recv_qty;
                                	$i++;
								}
							}
						}
					}
				}
            }//end foreach 1st
            
            ?>
            </table>
            </div>
            <table border="1" class="tbl_bottom"  width="4542" rules="all" id="" >
                <tr>
                    <td width="30">&nbsp;</td>
                    <td width="80">&nbsp;</td>
                    <td width="60">&nbsp;</td>
                    <td width="60">&nbsp;</td>
                    <td width="130">&nbsp;</td>
                    <td width="70">&nbsp;</td>
                    <td width="70">&nbsp;</td>
                    <td width="70">&nbsp;</td>
                    <td width="130">&nbsp;</td>
                    <td width="100">&nbsp;</td>
                    <td width="100">&nbsp;</td>
                    <td width="130">&nbsp;</td>
                    <td width="100">&nbsp;</td>
                    <td width="100">&nbsp;</td>
                    <td width="100">&nbsp;</td>
                    <td width="100">&nbsp;</td> 
                    <td width="100" align="right">Total</td>
                    <td width="80" align="right" id="total_cut_inhouse"><? echo $tot_cuttingIn_qty;?></td> 
                    <td width="80" align="right" id="total_cut_outside"><? echo $tot_cuttingOut_qty;?></td> 
                    <td width="80" align="right" id="total_printissue_td"><? echo $tot_print_qty; ?> </td> 
                    <td width="80" align="right" id="total_printrcv_td"><?  echo $tot_printRec_qty; ?></td>
                    <td width="80" align="right" id="total_emb_iss"><? echo $tot_emb_qty; ?></td> 
                    <td width="80" align="right" id="total_emb_re"><?  echo $tot_embRec_qty;  ?></td>
                    
                     <td width="80" align="right" id="total_emb_gmt_iss"><? echo $tot_embgmtIssue_qty; ?></td> 
                    <td width="80" align="right" id="total_emb_gmt_re"><?  echo $tot_embgmtRec_qty;  ?></td>
                    
                    <td width="80" align="right" id="total_wash_iss"><? echo $tot_wash_qty; ?></td> 
                    <td width="80" align="right" id="total_wash_re"><?  echo $tot_washRec_qty;  ?></td>
                    <td width="80" align="right" id="total_sp_iss"><? echo $tot_special_qty; ?></td> 
                    <td width="80" align="right" id="total_sp_re"><?  echo $tot_specialRec_qty;  ?></td>
                    <td width="80" align="right" id="total_sewin_inhouse_td"><? echo $tot_sewIn_inQty; ?></td>
                    <td width="80" align="right" id="total_sewin_outbound_td"><? echo $tot_sewIn_outQty; ?></td>
                    <td width="80" align="right" id="total_sewin_td"><? echo $tot_sewing_input;  ?> </td> 
                    <td width="80" align="right" id="total_sewout_inhouse_td"><? echo $tot_sewOut_inQty; ?></td>
                    <td width="80" align="right" id="total_sewout_outbound_td"><? echo $tot_sewOut_outQty; ?></td>
                    <td width="80" align="right" id="total_sewout_td"><? echo $tot_sewing_output; ?> </td>
                    
                    <td width="80" align="right" id="total_iron_in_td"><? echo $tot_ironIn_qty; ?></td>
                    <td width="80" align="right" id="total_iron_out_td"><? echo $tot_ironOut_qty; ?></td>
                    
                    <td width="80" align="right" id="total_iron_td"><?  echo $tot_iron_qty; ?></td> 
                    <td width="80" align="right" id="total_iron_smv_td"><?  echo $total_iron_smv; ?></td>
                    <td width="80" align="right" id="total_re_iron_td"><?  echo $tot_reIron_qty; ?></td> 
                    <td width="80" align="right" id="total_finish_td"><?  echo $tot_finishing_qty; ?></td>   
                    <td width="80" align="right" id="total_carton_td"><? echo $tot_carton_qty; ?></td> 
                    <td width="80" align="right" id="value_total_in_prod_dzn_td"><?  echo number_format($total_in_prod_dzn,2); ?></td>
                    <td width="80" align="right" id="value_total_out_prod_dzn_td"><?  echo number_format($total_out_prod_dzn,2); ?></td>
                    <td width="80" align="right" id="value_total_prod_dzn_td"><?  echo number_format($total_prod_dzn,2); ?></td>
                    <td width="100" align="right" id="value_total_in_cm_value_td"><? echo number_format($total_in_cm_value,2); ?> </td>
                    <td width="100" align="right" id="value_total_out_cm_value_td"><? echo number_format($total_out_cm_value,2); ?></td>
                    <td width="100" align="right" id="value_total_cm_value_td"><? echo number_format($total_cm_value,2); ?></td>
                    <td width="100" align="right" id="value_total_in_cm_cost"><? echo number_format($total_in_cm_cost,2); ?></td>
                    <td width="100" align="right" id="value_total_out_cm_cost"><? echo number_format($total_out_cm_cost,2); ?></td>
                    <td width="100" align="right" id="value_total_cm_cost"><? echo number_format($total_cm_cost,2); ?></td>
                    <td width="">&nbsp;</td>
                </tr>
            </table>
  		</div>
		<?
	}// end if condition of type
	//-------------------------------------------END Date Location Floor & Line Wise------------------------
	//-------------------------------------------Show Line Wise------------------------	
	else if($type==3)
	{
		ob_start();
	
		$prod_reso_allocation=return_field_value("auto_update","variable_settings_production","company_name=$cbo_company_name and variable_list=23 and status_active=1 and is_deleted=0");
			
		if($prod_reso_allocation==1){
		$sql="SELECT a.job_no_mst,a.po_number,a.po_quantity,a.unit_price,b.order_uom,b.buyer_name,
					c.po_break_down_id,c.location,c.production_date,c.sewing_line,c.prod_reso_allo,sum(c.production_quantity) as production_quantity
				from 
					wo_po_break_down a,wo_po_details_master b, pro_garments_production_mst c, prod_resource_mst d
				where 
					a.job_no_mst=b.job_no and a.id=c.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $txt_date $company_name $buyer_name $garments_nature $floor_name $location_cond  $file_no_cond $internal_ref_cond and c.production_type=5 and  c.production_source=1 and d.id=c.sewing_line group by a.job_no_mst,a.po_number,a.po_quantity,a.unit_price,b.order_uom,b.buyer_name,
					c.po_break_down_id,c.location,c.production_date,c.sewing_line,c.prod_reso_allo,d.line_number order by d.line_number,c.production_date";
		}
		else
		{
			$sql="SELECT a.job_no_mst,a.po_number,a.po_quantity,a.unit_price,b.order_uom,b.buyer_name,
					c.po_break_down_id,c.location,c.production_date,c.sewing_line,c.prod_reso_allo,sum(c.production_quantity) as production_quantity
				from 
					wo_po_break_down a,wo_po_details_master b, pro_garments_production_mst c, lib_sewing_line d
				where 
					a.job_no_mst=b.job_no and a.id=c.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $txt_date $company_name $buyer_name $garments_nature $floor_name $location_cond  $file_no_cond $internal_ref_cond and c.production_type=5 and  c.production_source=1 and d.id=c.sewing_line group by a.job_no_mst,a.po_number,a.po_quantity,a.unit_price,b.order_uom,b.buyer_name,
					c.po_break_down_id,c.location,c.production_date,c.sewing_line,c.prod_reso_allo,d.line_name order by d.line_name,c.production_date";
		}
				
		$pro_date_sql=sql_select($sql);					
					
		foreach($pro_date_sql as $rows)
		{
			$key=$rows[csf('sewing_line')];
			$pro_data[$rows[csf('sewing_line')]][]=array(
				'po_break_down_id'=>$rows[csf('po_break_down_id')],
				'po_number'=>$rows[csf('po_number')],
				'buyer_name'=>$rows[csf('buyer_name')],
				'po_quantity'=>$rows[csf('po_quantity')],
				'production_quantity'=>$rows[csf('production_quantity')],
				'production_date'=>$rows[csf('production_date')],
				'location'=>$rows[csf('location')],
				'sewing_line'=>$rows[csf('sewing_line')],
				'unit_price'=>$rows[csf('unit_price')],
				'order_uom'=>$rows[csf('order_uom')],
				'job_no_mst'=>$rows[csf('job_no_mst')],
				'prod_reso_allo'=>$rows[csf('prod_reso_allo')]
			);
			$job_arr[]="'".$rows[csf('job_no_mst')]."'";	
		}
		//var_dump($pro_data);
		$job_id=implode(',',$job_arr);
		$sql="select a.job_no,a.costing_per,a.sew_smv,b.margin_pcs_set,b.cm_cost from wo_pre_cost_mst a,wo_pre_cost_dtls b where a.job_no=b.job_no and a.job_no in($job_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
		$pro_cm_sql=sql_select($sql);
		foreach($pro_cm_sql as $row)
		{
			
			if($row[csf('costing_per')]==1)$pcs=12;	
			if($row[csf('costing_per')]==2)$pcs=1;	
			if($row[csf('costing_per')]==3)$pcs=24;	
			if($row[csf('costing_per')]==4)$pcs=36;	
			if($row[csf('costing_per')]==5)	$pcs=48;
			$margin_pcs=($row[csf('margin_pcs_set')]/$pcs);
			$cm_cost_pcs=$row[csf('cm_cost')]/($row[csf('sew_smv')]*$pcs);
			
			$job_cm[$row[csf('job_no')]]=$cm_cost_pcs+$margin_pcs;	
		}
		//var_dump($job_cm);	
		?>
        <table width="1250" class="rpt_table" border="1" rules="all" id="table_header_1">
            <thead>
                <th width="30">Sl.</th>
                <th width="170">Order Number</th>
                <th width="100">Buyer Name</th>
                <th width="100">PO Quantity</th>
                <th width="100">Production Date</th>
                <th width="100">Location</th>
                <th width="100">Sewing Line No</th>
                <th width="100">Sewing Output</th>
                <th width="70">Prod/Dzn</th>
                <th width="80">Total CM</th>
                <th width="100">Prod. Value</th>
                <th width="100">Remarks</th>
            </thead>
        </table>
        <div style="max-height:415px; overflow-y:scroll; width:1270px;" id="scroll_body">
            <table width="1250" class="rpt_table" border="1" rules="all">
                <?
                
                $j=1;
                foreach($pro_data as $line_id=>$pro_date_sql)
                {
                    $total_sewingout_qnty=0; $total_prod_dzn=0; $total_cm_value=0;  $total_prod_val=0; 
                    $i=1;
                    foreach($pro_date_sql as $rows)
                    {
                        $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";	  
                        $total_sewingout_qnty+=$rows['production_quantity'];
                        $total_prod_dzn+=$rows['production_quantity'] / 12 ;
                        $total_prod_val+=$rows['production_quantity'] * $rows['unit_price'];
                        $total_cm_value+=number_format($job_cm[$rows['job_no_mst']]*$rows['production_quantity'],2);
                    
                        $sewing_line='';
                        if($rows['prod_reso_allo']==1)
                        {
                            $line_number=explode(",",$prod_reso_arr[$rows['sewing_line']]);
                            foreach($line_number as $val)
                            {
                                if($sewing_line=='') $sewing_line=$line_library[$val]; else $sewing_line.=", ".$line_library[$val];
                            }
                        }
                        else $sewing_line=$line_library[$rows['sewing_line']]; 
                    
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i.'_'.$j; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i.'_'.$j; ?>">
                            <td width="31" align="center"><? echo $i;?></td>
                            <td width="170"><? echo $rows['po_number']; ?></td>
                            <td width="100"><? echo $buyer_short_library[$rows['buyer_name']]; ?></td>
                            <td align="right" width="100"><? echo $rows['po_quantity']; ?></td>
                            <td width="100" align="center"><? echo change_date_format($rows['production_date']); ?></td>
                            <td align="left" width="100"><? echo $location_library[$rows['location']]; ?></td>
                            <td align="right" width="100"><? echo $sewing_line; ?></td>
                            <td align="right" width="100"><? echo $rows['production_quantity']; ?></td>
                            <td align="right" width="70">
                                <?
                                    $prod_dzn=$rows['production_quantity'] / 12 ;
                                    echo number_format($prod_dzn,2);
                                ?>
                            </td>
                            <td align="right" width="80"><? echo number_format($job_cm[$rows['job_no_mst']]*$rows['production_quantity'],2);?></td>
                            <td align="right" width="100">
                                <?
                                    $prod_val=$rows['production_quantity'] * $rows['unit_price'];
                                    echo number_format($prod_val,2);
                                ?>
                            </td>
                            <td align="right" width="100"></td>
                        </tr>
                     
                        <?
                        $all_order_sewQty_array[$rows['po_break_down_id']]+=$rows['production_quantity'];
                        $i++;
                    }
                //var_dump($all_order_sewQty_array);
                ?>
                    <tr bgcolor="#CCCCFF">
                        <th width="31"></th> 
                        <th width="170"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100" align="right">Sub Total:</th>
                        <th width="100" align="right">
                            <?
                                echo number_format($total_sewingout_qnty,2);
                                $grand_total_sewing+=$total_sewingout_qnty;
                            ?>
                        </th>
                        
                        
                        <th width="70" align="right">
                            <?
                                echo number_format($total_prod_dzn,2);
                                $grand_total_prod_dzn+=$total_prod_dzn;
                            ?>
                        </th>
                        <th width="80" align="right">
                            <?
                                echo number_format($total_cm_value,2);
                                $grand_total_cm_value+=$total_cm_value;
                            ?>
                        </th>
                        
                        <th width="100" align="right">
                        <?php 
                            echo number_format($total_prod_val,2);
                            $grand_total_prod_val+=$total_prod_val;
                            $total_prod_val=0;
                         ?>
                         </th>
                        <th width="100" align="right"></th>
                    </tr>
                <?php
                
                $j++;
                }
                
                ?>
             </table>
          </div>
         <table width="1250" class="rpt_table" border="1" rules="all">
            <tfoot>
                <tr>
                    <th width="31"></th>
                    <th width="170"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100">Grand Total:</th>
                    <th width="100" align="right" id="total_cut_qnty">
                        <?
                            echo number_format($grand_total_sewing,2);
                        ?>
                    </th>
                    <th width="70" align="right" id="total_cut_qnty">
                        <?
                            echo number_format($grand_total_prod_dzn,2);
                        ?>
                    </th>
                    <th width="80" align="right" id="total_cut_qnty">
                        <?
                            echo number_format($grand_total_cm_value,2);
                        ?>
                    </th>
                    <th width="100" align="right"><?php echo number_format($grand_total_prod_val,2); ?></th>
                    <th width="100" align="right" id="total_iron_input_qnty"></th>
                </tr>
            </tfoot>
         </table>
         </div>
		<?
        }//end line wise;
		
		$fab_cost_arr=array();
		$sql_pre=sql_select("select job_no, costing_per_id, fabric_cost, trims_cost, embel_cost, wash_cost, comm_cost, commission, lab_test, freight, inspection, currier_pre_cost, cm_cost, total_cost from wo_pre_cost_dtls where status_active=1 and is_deleted=0");
		foreach($sql_pre as $val)
		{
			$fab_cost_arr[$val[csf('job_no')]]['costing_per_id']=$val[csf('costing_per_id')];
			$fab_cost_arr[$val[csf('job_no')]]['fabric_cost']=$val[csf('fabric_cost')];
			$fab_cost_arr[$val[csf('job_no')]]['trims_cost']=$val[csf('trims_cost')];
			$fab_cost_arr[$val[csf('job_no')]]['embel_cost']=$val[csf('embel_cost')];
			$fab_cost_arr[$val[csf('job_no')]]['wash_cost']=$val[csf('wash_cost')];
			$fab_cost_arr[$val[csf('job_no')]]['comm_cost']=$val[csf('comm_cost')];
			$fab_cost_arr[$val[csf('job_no')]]['commission']=$val[csf('commission')];
			$fab_cost_arr[$val[csf('job_no')]]['lab_test']=$val[csf('lab_test')];
			$fab_cost_arr[$val[csf('job_no')]]['freight']=$val[csf('freight')];
			$fab_cost_arr[$val[csf('job_no')]]['inspection']=$val[csf('inspection')];
			$fab_cost_arr[$val[csf('job_no')]]['currier_pre_cost']=$val[csf('currier_pre_cost')];
			$fab_cost_arr[$val[csf('job_no')]]['cm_cost']=$val[csf('cm_cost')];
			$fab_cost_arr[$val[csf('job_no')]]['total_cost']=$val[csf('total_cost')];
		}
		$all_po_id=""; $all_job_no=""; $job_no="";
		foreach($all_order_sewQty_array as $id=>$val)
		{
			if($all_po_id=="") $all_po_id=$id; else $all_po_id.=','.$id;
		}
		$job_arr=array();
		
		$job_sql="select a.job_no_prefix_num, a.job_no, a.avg_unit_price, a.total_set_qnty as ratio, b.plan_cut, b.id, b.po_number, b.pub_shipment_date, b.po_received_date, b.po_quantity, b.unit_price, b.po_total_price from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.id in ($all_po_id) order by b.pub_shipment_date,b.id";
		//echo $job_sql;
		$job_result=sql_select($job_sql); 
		$tot_fab_cost=0; $tot_trim_cost=0; $tot_emble_cost=0; $tot_wash_cost=0; $tot_commercial_cost=0; $tot_commission_cost=0; $tot_test_cost=0; $tot_freight_cost=0; $tot_inspection_cost=0; $tot_currier_cost=0;
		foreach($job_result as $row)
		{
			$order_value+=$row[csf('po_total_price')];
			$prod_qty=$all_order_sewQty_array[$row[csf('id')]];
			$dzn_qnty=0;
			$costing_per_id=$fab_cost_arr[$row[csf('job_no')]]['costing_per_id'];
			if($costing_per_id==1) $dzn_qnty=12;
			else if($costing_per_id==3) $dzn_qnty=12*2;
			else if($costing_per_id==4) $dzn_qnty=12*3;
			else if($costing_per_id==5) $dzn_qnty=12*4;
			else $dzn_qnty=1;
			$dzn_qnty=$dzn_qnty*$row[csf('ratio')];
			
			$fab_cost=($fab_cost_arr[$row[csf('job_no')]]['fabric_cost']/$dzn_qnty)*$prod_qty;
			$tot_fab_cost+=$fab_cost;
			
			$trim_cost=($fab_cost_arr[$row[csf('job_no')]]['trims_cost']/$dzn_qnty)*$prod_qty;
			$tot_trim_cost+=$trim_cost;
			
			$emble_cost=($fab_cost_arr[$row[csf('job_no')]]['embel_cost']/$dzn_qnty)*$prod_qty;
			$tot_emble_cost+=$emble_cost;
			
			$wash_cost=($fab_cost_arr[$row[csf('job_no')]]['wash_cost']/$dzn_qnty)*$prod_qty;
			$tot_wash_cost+=$wash_cost;
			
			$commercial_cost=($fab_cost_arr[$row[csf('job_no')]]['comm_cost']/$dzn_qnty)*$prod_qty;
			$tot_commercial_cost+=$commercial_cost;
			
			$commission_cost=($fab_cost_arr[$row[csf('job_no')]]['commission']/$dzn_qnty)*$prod_qty;
			$tot_commission_cost+=$commission_cost;
			
			$test_cost=($fab_cost_arr[$row[csf('job_no')]]['lab_test']/$dzn_qnty)*$prod_qty;
			$tot_test_cost+=$test_cost;

			$freight_cost=($fab_cost_arr[$row[csf('job_no')]]['freight']/$dzn_qnty)*$prod_qty;
			$tot_freight_cost+=$freight_cost;
			
			$inspection_cost=($fab_cost_arr[$row[csf('job_no')]]['inspection']/$dzn_qnty)*$prod_qty;
			$tot_inspection_cost+=$inspection_cost;
			
			$currier_cost=($fab_cost_arr[$row[csf('job_no')]]['currier_pre_cost']/$dzn_qnty)*$prod_qty;
			$tot_currier_cost+=$currier_cost;
		}
		
		$total_cost=$tot_fab_cost+$tot_trim_cost+$tot_emble_cost+$tot_wash_cost+$tot_commercial_cost+$tot_commission_cost+$tot_test_cost+$tot_freight_cost+$tot_inspection_cost+$tot_currier_cost;
		$cm_value=$order_value-$total_cost;
		
		$tot_fab_cost_per=($tot_fab_cost/$order_value)*100;
		$tot_trim_cost_per=($tot_trim_cost/$order_value)*100;
		$tot_emble_cost_per=($tot_emble_cost/$order_value)*100;
		$tot_wash_cost_per=($tot_wash_cost/$order_value)*100;
		$tot_commercial_cost_per=($tot_commercial_cost/$order_value)*100;
		$tot_commission_cost_per=($tot_commission_cost/$order_value)*100;
		$tot_test_cost_per=($tot_test_cost/$order_value)*100;
		$tot_freight_cost_per=($tot_freight_cost/$order_value)*100;
		$tot_inspection_cost_per=($tot_inspection_cost/$order_value)*100;
		$tot_currier_cost_per=($tot_currier_cost/$order_value)*100;
		$total_cost_per=($total_cost/$order_value)*100;
		$total_value_per=($order_value/$order_value)*100;
		$total_cm_per=($cm_value/$order_value)*100;
		
		$style=$bgcolor="#E9F3FF";
		$style1=$bgcolor="#FFFFFF";
		?>
        <br />
        <div>
            <table width="450" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all" align="left">
            	<thead>
                	<tr>
                    	<th colspan="3">Budget Summary </th>
                    </tr>
                	<tr>
                		<th width="150">Particulars</th><th width="145">Amount</th><th>Percentage</th>
                    </tr>
                </thead>
                <tbody>
                	<tr bgcolor="<? echo $style; ?>">
                    	<td>Fabric Cost</td>
                        <td align="right"><? echo number_format($tot_fab_cost,2); ?></td>
                        <td align="right"><? echo number_format($tot_fab_cost_per,3); ?></td>
                    </tr>
                    <tr bgcolor="<? echo $style1; ?>">
                        <td>Trims Cost</td>
                        <td align="right"><? echo number_format($tot_trim_cost,2); ?></td>
                        <td align="right"><? echo number_format($tot_trim_cost_per,3); ?></td>
                    </tr>
                    <tr bgcolor="<? echo $style; ?>">
                        <td>Embellish Cost</td>
                        <td align="right"><? echo number_format($tot_emble_cost,2); ?></td>
                        <td align="right"><? echo number_format($tot_emble_cost_per,3); ?></td>
                    </tr>
                    <tr bgcolor="<? echo $style1; ?>">
                        <td>Gmts.Wash Cost</td>
                        <td align="right"><? echo number_format($tot_wash_cost,2); ?></td>
                        <td align="right"><? echo number_format($tot_wash_cost_per,3); ?></td>
                    </tr>
                    <tr bgcolor="<? echo $style; ?>">
                        <td>Commercial Cost</td>
                        <td align="right"><? echo number_format($tot_commercial_cost,2); ?></td>
                        <td align="right"><? echo number_format($tot_commercial_cost_per,3); ?></td>
                    </tr>
                    <tr bgcolor="<? echo $style1; ?>">
                        <td>Commision Cost</td>
                        <td align="right"><? echo number_format($tot_commission_cost,2); ?></td>
                        <td align="right"><? echo number_format($tot_commission_cost_per,2); ?></td>
                    </tr>
                    <tr bgcolor="<? echo $style; ?>">
                        <td>Testing Cost</td>
                        <td align="right"><? echo number_format($tot_test_cost,2); ?></td>
                        <td align="right"><? echo number_format($tot_test_cost_per,2); ?></td>
                    </tr>
                    <tr bgcolor="<? echo $style1; ?>">
                        <td>Freight Cost</td>
                        <td align="right"><? echo number_format($tot_freight_cost,2); ?></td>
                        <td align="right"><? echo number_format($tot_freight_cost_per,2); ?></td>
                    </tr>
                    <tr bgcolor="<? echo $style; ?>">
                        <td>Inspection Cost</td>
                        <td align="right"><? echo number_format($tot_inspection_cost,2); ?></td>
                        <td align="right"><? echo number_format($tot_inspection_cost_per,2); ?></td>
                    </tr>
                    <tr bgcolor="<? echo $style1; ?>">
                        <td>Courier Cost</td>
                        <td align="right"><? echo number_format($tot_currier_cost,2); ?></td>
                        <td align="right"><? echo number_format($tot_currier_cost_per,2); ?></td>
                    </tr>
                    <tr bgcolor="<? echo $style; ?>">
                        <td>Total Cost</td>
                        <td align="right"><? echo number_format($total_cost,2); ?></td>
                        <td align="right"><? echo number_format($total_cost_per,2); ?></td>
                    </tr>
                    <tr bgcolor="<? echo $style1; ?>">
                        <td>Total Value</td>
                        <td align="right"><? echo number_format($order_value,2); ?></td>
                        <td align="right"><? echo number_format($total_value_per,2); ?></td>
                    </tr>
                    <tr bgcolor="<? echo $style; ?>">
                        <td>CM Value</td>
                        <td align="right"><? echo number_format($cm_value,2); ?></td>
                        <td align="right"><? echo number_format($total_cm_per,2); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <?
		//-------------------------------------------END Show Date Location Floor & Line Wise------------------------
		//---------end------------//
		
	if($excel_type==0) //Show Button
	{			
	$html = ob_get_contents();
	ob_clean();
	$new_link=create_delete_report_file( $html, 1, 1, "../../../" );
	
	echo "$html";
	exit();
	}
	else //Convert to Excel Button
	{
		$html = ob_get_contents();
		ob_clean();
	//$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
	foreach (glob("$user_name*.xls") as $filename) {
	//if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename="".$user_name."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc, $html);
	echo "$filename####$excel_type"; 	
	}
	exit();
}

if($action=="orderQnty_popup")
{
	
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	
	$sql= "SELECT b.id,b.po_number,b.pub_shipment_date,b.po_quantity*(select from wo_po_details_mas_set_details set where set.job_no=a.job_no and set.gmts_item_id=$gmts_item_id) as po_quantity from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id='$po_break_down_id' and a.garments_nature='$garments_nature' and a.is_deleted=0 and a.status_active=1";
	//echo $sql;
	echo "<br />". create_list_view ( "list_view", "Order No,Order Qnty,Pub Shipment Date", "200,120,220","540","220",1, "SELECT b.id,b.po_number,b.pub_shipment_date,b.po_quantity*a.total_set_qnty as po_quantity from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id='$po_break_down_id' and a.is_deleted=0 and a.status_active=1", "", "","", 1, '0,0,0', $arr, "po_number,po_quantity,pub_shipment_date","../requires/date_wise_production_report_controller", '','0,1,3');
  		 
	exit();
}

if($action=='date_wise_production_report') 
{	
	extract($_REQUEST); 
 	echo load_html_head_contents("Remarks", "../../../", 1, 1,$unicode,'','');
 ?>
	<fieldset>
    <legend>Cutting</legend>
    	<? 
			 $i=1;
			 $sql= "SELECT id,production_date,production_quantity,remarks from pro_garments_production_mst where po_break_down_id='$po_break_down_id' and production_type='1' and is_deleted=0 and status_active=1";
			 $result=sql_select($sql); 
			 $avg_prod_qty="";
				
 		?>
 		
	<table width="500" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
		<thead>
			<th>SL No</th>
			<th>Date</th>
			<th>Production Qnty</th>
			<th>Remarks</th>
		</thead>
		<?
		foreach($result as $row)
			 {
			 	?>
		<tr>
			<td width="50"><? echo $i; ?></td>
			<td width="100"><? echo date('d-m-Y',strtotime($row[csf('production_date')]));?></td>
			<td width="120"><p align="right"><? echo $row[csf('production_quantity')];?></p></td>
			<td>
			<? echo $row[csf('remarks')];
			$avg_prod_qty+=$row[csf('production_quantity')];
			?>
				
			</td>
		</tr>
		 <? 
		 $i++;
		}
		 ?>
		<tfoot>
			<th align="right" colspan="2">Total</th>
			<th align=""><? echo $avg_prod_qty; ?></th>
		</tfoot>
	</table>
</fieldset>

    <fieldset>
    <legend>Print/Embr Issue</legend>
    	   	<? 
			 $i=1;
			 $sql= "SELECT id,production_date,production_quantity,remarks from pro_garments_production_mst where po_break_down_id='$po_break_down_id' and production_type='2' and is_deleted=0 and status_active=1";
			 $result=sql_select($sql); 
			 $avg_prod_qty="";
				
 		?>
 		
	<table width="500" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
		<thead>
			<th>SL No</th>
			<th>Date</th>
			<th>Production Qnty</th>
			<th>Remarks</th>
		</thead>
		<?
		foreach($result as $row)
			 {
			 	?>
		<tr>
			<td width="50"><? echo $i; ?></td>
			<td width="100"><? echo date('d-m-Y',strtotime($row[csf('production_date')]));?></td>
			<td width="120"><p align="right"><? echo $row[csf('production_quantity')];?></p></td>
			<td>
			<? echo $row[csf('remarks')];
			$avg_prod_qty+=$row[csf('production_quantity')];
			?>
				
			</td>
		</tr>
		 <? 
		 $i++;
		}
		 ?>
		<tfoot>
			<th align="right" colspan="2">Total</th>
			<th align=""><? echo $avg_prod_qty; ?></th>
		</tfoot>
	</table>
    </fieldset>
    
    
        <fieldset>
    <legend>Print/Embr Receive</legend>
    	   	<? 
			 $i=1;
			 $sql= "SELECT id,production_date,production_quantity,remarks from pro_garments_production_mst where po_break_down_id='$po_break_down_id' and production_type='3' and is_deleted=0 and status_active=1";
			 $result=sql_select($sql); 
			 $avg_prod_qty="";
				
 		?>
 		
	<table width="500" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
		<thead>
			<th>SL No</th>
			<th>Date</th>
			<th>Production Qnty</th>
			<th>Remarks</th>
		</thead>
		<?
		foreach($result as $row)
			 {
			 	?>
		<tr>
			<td width="50"><? echo $i; ?></td>
			<td width="100"><? echo date('d-m-Y',strtotime($row[csf('production_date')]));?></td>
			<td width="120"><p align="right"><? echo $row[csf('production_quantity')];?></p></td>
			<td>
			<? echo $row[csf('remarks')];
			$avg_prod_qty+=$row[csf('production_quantity')];
			?>
				
			</td>
		</tr>
		 <? 
		 $i++;
		}
		 ?>
		<tfoot>
			<th align="right" colspan="2">Total</th>
			<th align=""><? echo $avg_prod_qty; ?></th>
		</tfoot>
	</table>
    </fieldset>


    <fieldset>
    <legend>Sewing Input</legend>
    	   	<? 
			 $i=1;
			 $sql= "SELECT id,production_date,production_quantity,remarks from pro_garments_production_mst where po_break_down_id='$po_break_down_id' and production_type='4' and is_deleted=0 and status_active=1";
			 $result=sql_select($sql); 
			 $avg_prod_qty="";
				
 		?>
 		
	<table width="500" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
		<thead>
			<th>SL No</th>
			<th>Date</th>
			<th>Production Qnty</th>
			<th>Remarks</th>
		</thead>
		<?
		foreach($result as $row)
			 {
			 	?>
		<tr>
			<td width="50"><? echo $i; ?></td>
			<td width="100"><? echo date('d-m-Y',strtotime($row[csf('production_date')]));?></td>
			<td width="120"><p align="right"><? echo $row[csf('production_quantity')];?></p></td>
			<td>
			<? echo $row[csf('remarks')];
			$avg_prod_qty+=$row[csf('production_quantity')];
			?>
				
			</td>
		</tr>
		 <? 
		 $i++;
		}
		 ?>
		<tfoot>
			<th align="right" colspan="2">Total</th>
			<th align=""><? echo $avg_prod_qty; ?></th>
		</tfoot>
	</table>
    </fieldset>
    

        <fieldset>
    <legend>Sewing Output</legend>
    	   	<? 
			 $i=1;
			 $sql= "SELECT id,production_date,production_quantity,remarks from pro_garments_production_mst where po_break_down_id='$po_break_down_id' and production_type='5' and is_deleted=0 and status_active=1";
			 $result=sql_select($sql); 
			 $avg_prod_qty="";
				
 		?>
 		
	<table width="500" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
		<thead>
			<th>SL No</th>
			<th>Date</th>
			<th>Production Qnty</th>
			<th>Remarks</th>
		</thead>
		<?
		foreach($result as $row)
			 {
			 	?>
		<tr>
			<td width="50"><? echo $i; ?></td>
			<td width="100"><? echo date('d-m-Y',strtotime($row[csf('production_date')]));?></td>
			<td width="120"><p align="right"><? echo $row[csf('production_quantity')];?></p></td>
			<td>
			<? echo $row[csf('remarks')];
			$avg_prod_qty+=$row[csf('production_quantity')];
			?>
				
			</td>
		</tr>
		 <? 
		 $i++;
		}
		 ?>
		<tfoot>
			<th align="right" colspan="2">Total</th>
			<th align=""><? echo $avg_prod_qty; ?></th>
		</tfoot>
	</table>
    </fieldset>


    <fieldset>
    <legend>Finish Input</legend>
    	   	<? 
			 $i=1;
			 $sql= "SELECT id,production_date,production_quantity,remarks from pro_garments_production_mst where po_break_down_id='$po_break_down_id' and production_type='6' and is_deleted=0 and status_active=1";
			 $result=sql_select($sql); 
			 $avg_prod_qty="";
				
 		?>
 		
	<table width="500" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
		<thead>
			<th>SL No</th>
			<th>Date</th>
			<th>Production Qnty</th>
			<th>Remarks</th>
		</thead>
		<?
		foreach($result as $row)
			 {
			 	?>
		<tr>
			<td width="50"><? echo $i; ?></td>
			<td width="100"><? echo date('d-m-Y',strtotime($row[csf('production_date')]));?></td>
			<td width="120"><p align="right"><? echo $row[csf('production_quantity')];?></p></td>
			<td>
			<? echo $row[csf('remarks')];
			$avg_prod_qty+=$row[csf('production_quantity')];
			?>
				
			</td>
		</tr>
		 <? 
		 $i++;
		}
		 ?>
		<tfoot>
			<th align="right" colspan="2">Total</th>
			<th align=""><? echo $avg_prod_qty; ?></th>
		</tfoot>
	</table>
    </fieldset>


	    <fieldset>
    <legend>Finish Output</legend>
    	   	<? 
			 $i=1;
			 $sql= "SELECT id,production_date,production_quantity,remarks from pro_garments_production_mst where po_break_down_id='$po_break_down_id' and production_type='8' and is_deleted=0 and status_active=1";
			 $result=sql_select($sql); 
			 $avg_prod_qty="";
				
 		?>
 		
	<table width="500" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
		<thead>
			<th>SL No</th>
			<th>Date</th>
			<th>Production Qnty</th>
			<th>Remarks</th>
		</thead>
		<?
		foreach($result as $row)
			 {
			 	?>
		<tr>
			<td width="50"><? echo $i; ?></td>
			<td width="100"><? echo date('d-m-Y',strtotime($row[csf('production_date')]));?></td>
			<td width="120"><p align="right"><? echo $row[csf('production_quantity')];?></p></td>
			<td>
			<? echo $row[csf('remarks')];
			$avg_prod_qty+=$row[csf('production_quantity')];
			?>
				
			</td>
		</tr>
		 <? 
		 $i++;
		}
		 ?>
		<tfoot>
			<th align="right" colspan="2">Total</th>
			<th align=""><? echo $avg_prod_qty; ?></th>
		</tfoot>
	</table>
    </fieldset>
    
    
<?
}//end if 


//cutting_popup
if($action=='cutting_popup')
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  ); 
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  ); 
	
	
	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");
	
	$sql_color_size= sql_select("SELECT a.country_id, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id 
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c 
	where a.production_type=1 and a.item_number_id=$gmts_item_id and a.production_source=$production_source and a.production_date='$production_date' and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
	group by   c.color_number_id,a.country_id,c.size_number_id
	order by c.color_number_id,a.country_id");
	
	foreach($sql_color_size as $row)
	{
		$details_data[$row[csf('color_number_id')]][$row[csf('country_id')]]=$row[csf('country_id')];
		$color_size_data[$row[csf('color_number_id')]][$row[csf('country_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}
	
	$col_width=60*count($sizearr_order);
	$table_width=330+$col_width;


?>	
<script>

	function window_close()
	{
		parent.emailwindow.hide();
	}
	
</script>	
<fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
    <div style="100%" id="report_container">
    
        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
        	<tr>
            	<td align="center" style="text-decoration:underline; font-size:20px; font-weight:bold">Date wise cutting production</td>
            </tr>
        	<tr>
            	<td style="font-size:16px; font-weight:bold;">
                Date : <? echo change_date_format($production_date); ?> 
                <br />
                Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;
                </td>
            </tr>
        </table>
        
        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                	<th width="40" rowspan="2">SI</th>
                    <th width="100" rowspan="2">Color</th>
                    <th width="100" rowspan="2">Country Name</th>
                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
                    <th width="80" rowspan="2" >Total</th>
                </tr>
                <tr>
                <?
				$grand_tot_in=0;
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
                    <?
				}
				?>
                </tr>
                
            </thead>
            <tbody>
            <?
			$i=1;
			$k=1;
			//var_dump($result);die;
			foreach($details_data as $color_id=>$value)
			{
				foreach($value as $country_id=>$row)
				{
					if(!in_array($color_id,$temp_arr))
					{
						$temp_arr[]=$color_id;
						if($k!=1)
						{
							?>
							<tr bgcolor="#CCCCCC">
								<td >&nbsp;</td>
								<td>&nbsp;</td>
								<td align="right" style="font-weight:bold">Color Total:</td>
								<?
								foreach($sizearr_order as $size_id)
								{
									?>
									<td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
									<?
								}
								?>
								<td align="right"><? echo number_format($line_color_total_in,0); ?></td>
							</tr>
							<?
							$line_color_size_in = $line_color_total_in ="";
						}
						$k++;
					}
					
					?>
					<tr>
						<td align="center"><? echo $i;  ?></td>
                        <?
						if(!in_array($color_id,$temp_arr_color))
						{
							$temp_arr_color[]=$color_id;
							?>
							<td rowspan="<? echo count($color_size_data[$color_id]); ?>" align="center" valign="middle"><p><? echo $colorarr[$color_id];  ?></p></td>
							<?
						}
						?>
						<td ><p><? echo $country_library[$country_id];  ?></p></td>
						<?
						$color_total_in=0;
						foreach($sizearr_order as $size_id)
						{
							?>
							<td align="right"><p>
							<?
								echo number_format($color_size_data[$color_id][$country_id][$size_id],0) ;
								 $color_total_in+= $color_size_data[$color_id][$country_id][$size_id]; 
								 $color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
								 $line_color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
							 ?>
							</p></td>
							<?
						}
						$line_color_total_in+=$color_total_in; 
						?>
						<td align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
					</tr>
					<?
					$i++;
				}
				
			}
			?>
            <tr bgcolor="#CCCCCC">
                <td >&nbsp;</td>
                <td>&nbsp;</td>
                <td align="right" style="font-weight:bold">Color Total:</td>
                <?
                foreach($sizearr_order as $size_id)
                {
                    ?>
                    <td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
                    <?
                }
                ?>
                <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
            </tr>
            </tbody>
            <tfoot>
                <th >&nbsp;</th>
                <th>&nbsp;</th>
                <th >Day Total:</th>
                <?
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th ><? echo $color_size_in[$size_id]; ?></th>
                    <?
				}
				?>
                <th ><? echo $grand_tot_in; ?></th>
                
            </tfoot>
        </table>
        </div>
    </fieldset>
<?	

	
}
//cutting_popup_location
if($action=='cutting_popup_location')
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  ); 
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  ); 
	$location=str_replace("'","",$location);
	$floor_id=str_replace("'","",$floor_id);
	$sewing_line=str_replace("'","",$sewing_line);
	$location_cond=$floor_cond=$sewing_cond="";
	if($location!="") $location_cond="and a.location=$location";
	if($floor_id!="") $floor_cond=" and a.floor_id=$floor_id";
	if($sewing_line!="") $sewing_cond=" and a.sewing_line=$sewing_line";
	
	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");
	$sql_color_size= sql_select("SELECT a.country_id, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id 
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c 
	where a.production_type=1 and a.item_number_id=$gmts_item_id and a.production_source=$production_source and a.production_date='$production_date'  $location_cond $floor_cond $sewing_cond and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
	group by   c.color_number_id,a.country_id,c.size_number_id
	order by c.color_number_id,a.country_id");
	foreach($sql_color_size as $row)
	{
		$details_data[$row[csf('color_number_id')]][$row[csf('country_id')]]=$row[csf('country_id')];
		$color_size_data[$row[csf('color_number_id')]][$row[csf('country_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}
	
	$col_width=60*count($sizearr_order);
	$table_width=330+$col_width;


?>	
<script>

	function window_close()
	{
		parent.emailwindow.hide();
	}
	
</script>	
<fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
    <div style="100%" id="report_container">
    
        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
        	<tr>
            	<td align="center" style="text-decoration:underline; font-size:20px; font-weight:bold">Date wise cutting production</td>
            </tr>
        	<tr>
            	<td style="font-size:16px; font-weight:bold;">
                Date : <? echo change_date_format($production_date); ?> 
                <br />
                Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;
                </td>
            </tr>
        </table>
        
        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                	<th width="40" rowspan="2">SI</th>
                    <th width="100" rowspan="2">Color</th>
                    <th width="100" rowspan="2">Country Name</th>
                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
                    <th width="80" rowspan="2" >Total</th>
                </tr>
                <tr>
                <?
				$grand_tot_in=0;
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
                    <?
				}
				?>
                </tr>
                
            </thead>
            <tbody>
            <?
			$i=1;
			$k=1;
			//var_dump($result);die;
			foreach($details_data as $color_id=>$value)
			{
				foreach($value as $country_id=>$row)
				{
					if(!in_array($color_id,$temp_arr))
					{
						$temp_arr[]=$color_id;
						if($k!=1)
						{
							?>
							<tr bgcolor="#CCCCCC">
								<td >&nbsp;</td>
								<td>&nbsp;</td>
								<td align="right" style="font-weight:bold">Color Total:</td>
								<?
								foreach($sizearr_order as $size_id)
								{
									?>
									<td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
									<?
								}
								?>
								<td align="right"><? echo number_format($line_color_total_in,0); ?></td>
							</tr>
							<?
							$line_color_size_in = $line_color_total_in ="";
						}
						$k++;
					}
					
					?>
					<tr>
						<td align="center"><? echo $i;  ?></td>
                        <?
						if(!in_array($color_id,$temp_arr_color))
						{
							$temp_arr_color[]=$color_id;
							?>
							<td rowspan="<? echo count($color_size_data[$color_id]); ?>" align="center" valign="middle"><p><? echo $colorarr[$color_id];  ?></p></td>
							<?
						}
						?>
						<td ><p><? echo $country_library[$country_id];  ?></p></td>
						<?
						$color_total_in=0;
						foreach($sizearr_order as $size_id)
						{
							?>
							<td align="right"><p>
							<?
								echo number_format($color_size_data[$color_id][$country_id][$size_id],0) ;
								 $color_total_in+= $color_size_data[$color_id][$country_id][$size_id]; 
								 $color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
								 $line_color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
							 ?>
							</p></td>
							<?
						}
						$line_color_total_in+=$color_total_in; 
						?>
						<td align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
					</tr>
					<?
					$i++;
				}
				
			}
			?>
            <tr bgcolor="#CCCCCC">
                <td >&nbsp;</td>
                <td>&nbsp;</td>
                <td align="right" style="font-weight:bold">Color Total:</td>
                <?
                foreach($sizearr_order as $size_id)
                {
                    ?>
                    <td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
                    <?
                }
                ?>
                <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
            </tr>
            </tbody>
            <tfoot>
                <th >&nbsp;</th>
                <th>&nbsp;</th>
                <th >Day Total:</th>
                <?
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th ><? echo $color_size_in[$size_id]; ?></th>
                    <?
				}
				?>
                <th ><? echo $grand_tot_in; ?></th>
                
            </tfoot>
        </table>
        </div>
    </fieldset>
<?	

	
}

if($action=='printing_issue_popup')
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  ); 
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  ); 
	
	
	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");
	
	$location=str_replace("'","",$location);
	$floor_id=str_replace("'","",$floor_id);
	$sewing_line=str_replace("'","",$sewing_line);
	$location_cond=$floor_cond=$sewing_cond="";
	if($location!="") $location_cond="and a.location=$location";
	if($floor_id!="") $floor_cond=" and a.floor_id=$floor_id";
	if($sewing_line!="") $sewing_cond=" and a.sewing_line=$sewing_line";
	
	$sql_color_size= sql_select("SELECT a.country_id, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id 
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c 
	where a.production_type=2 and a.embel_name=1 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' $location_cond $floor_cond $sewing_cond  and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
	group by   c.color_number_id,a.country_id,c.size_number_id
	order by c.color_number_id,a.country_id");
	foreach($sql_color_size as $row)
	{
		$details_data[$row[csf('color_number_id')]][$row[csf('country_id')]]=$row[csf('country_id')];
		$color_size_data[$row[csf('color_number_id')]][$row[csf('country_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}
	
	$col_width=60*count($sizearr_order);
	$table_width=330+$col_width;


?>	
<script>

	function window_close()
	{
		parent.emailwindow.hide();
	}
	
</script>	
<fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
    <div style="100%" id="report_container">
    
        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
        	<tr>
            	<td align="center" style="text-decoration:underline; font-size:20px; font-weight:bold">Date wise printing production</td>
            </tr>
        	<tr>
            	<td style="font-size:16px; font-weight:bold;">
                Date : <? echo change_date_format($production_date); ?> 
                <br />
                Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;
                </td>
            </tr>
        </table>
        
        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                	<th width="40" rowspan="2">SI</th>
                    <th width="100" rowspan="2">Color</th>
                    <th width="100" rowspan="2">Country Name</th>
                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
                    <th width="80" rowspan="2" >Total</th>
                </tr>
                <tr>
                <?
				$grand_tot_in=0;
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
                    <?
				}
				?>
                </tr>
                
            </thead>
            <tbody>
            <?
			$i=1;
			$k=1;
			//var_dump($result);die;
			foreach($details_data as $color_id=>$value)
			{
				foreach($value as $country_id=>$row)
				{
					if(!in_array($color_id,$temp_arr))
					{
						$temp_arr[]=$color_id;
						if($k!=1)
						{
							?>
							<tr bgcolor="#CCCCCC">
								<td >&nbsp;</td>
								<td>&nbsp;</td>
								<td align="right" style="font-weight:bold">Color Total:</td>
								<?
								foreach($sizearr_order as $size_id)
								{
									?>
									<td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
									<?
								}
								?>
								<td align="right"><? echo number_format($line_color_total_in,0); ?></td>
							</tr>
							<?
							$line_color_size_in = $line_color_total_in ="";
						}
						$k++;
					}
					
					?>
					<tr>
						<td align="center"><? echo $i;  ?></td>
                        <?
						if(!in_array($color_id,$temp_arr_color))
						{
							$temp_arr_color[]=$color_id;
							?>
							<td rowspan="<? echo count($color_size_data[$color_id]); ?>" align="center" valign="middle"><p><? echo $colorarr[$color_id];  ?></p></td>
							<?
						}
						?>
						<td ><p><? echo $country_library[$country_id];  ?></p></td>
						<?
						$color_total_in=0;
						foreach($sizearr_order as $size_id)
						{
							?>
							<td align="right"><p>
							<?
								echo number_format($color_size_data[$color_id][$country_id][$size_id],0) ;
								 $color_total_in+= $color_size_data[$color_id][$country_id][$size_id]; 
								 $color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
								 $line_color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
							 ?>
							</p></td>
							<?
						}
						$line_color_total_in+=$color_total_in; 
						?>
						<td align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
					</tr>
					<?
					$i++;
				}
				
			}
			?>
            <tr bgcolor="#CCCCCC">
                <td >&nbsp;</td>
                <td>&nbsp;</td>
                <td align="right" style="font-weight:bold">Color Total:</td>
                <?
                foreach($sizearr_order as $size_id)
                {
                    ?>
                    <td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
                    <?
                }
                ?>
                <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
            </tr>
            </tbody>
            <tfoot>
                <th >&nbsp;</th>
                <th>&nbsp;</th>
                <th >Day Total:</th>
                <?
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th ><? echo $color_size_in[$size_id]; ?></th>
                    <?
				}
				?>
                <th ><? echo $grand_tot_in; ?></th>
                
            </tfoot>
        </table>
        </div>
    </fieldset>
<?	

	
}
if($action=='printing_issue_popup_location')
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  ); 
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  ); 
	
	
	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");
	
	$location=str_replace("'","",$location);
	$floor_id=str_replace("'","",$floor_id);
	$sewing_line=str_replace("'","",$sewing_line);
	$location_cond=$floor_cond=$sewing_cond="";
	if($location!="") $location_cond="and a.location=$location";
	if($floor_id!="") $floor_cond=" and a.floor_id=$floor_id";
	if($sewing_line!="") $sewing_cond=" and a.sewing_line=$sewing_line";
	
	$sql_color_size= sql_select("SELECT a.country_id, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id 
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c 
	where a.production_type=2 and a.embel_name=1 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' $location_cond $floor_cond $sewing_cond and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
	group by   c.color_number_id,a.country_id,c.size_number_id
	order by c.color_number_id,a.country_id");
	foreach($sql_color_size as $row)
	{
		$details_data[$row[csf('color_number_id')]][$row[csf('country_id')]]=$row[csf('country_id')];
		$color_size_data[$row[csf('color_number_id')]][$row[csf('country_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}
	
	$col_width=60*count($sizearr_order);
	$table_width=330+$col_width;


?>	
<script>

	function window_close()
	{
		parent.emailwindow.hide();
	}
	
</script>	
<fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
    <div style="100%" id="report_container">
    
        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
        	<tr>
            	<td align="center" style="text-decoration:underline; font-size:20px; font-weight:bold">Date wise printing production</td>
            </tr>
        	<tr>
            	<td style="font-size:16px; font-weight:bold;">
                Date : <? echo change_date_format($production_date); ?> 
                <br />
                Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;
                </td>
            </tr>
        </table>
        
        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                	<th width="40" rowspan="2">SI</th>
                    <th width="100" rowspan="2">Color</th>
                    <th width="100" rowspan="2">Country Name</th>
                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
                    <th width="80" rowspan="2" >Total</th>
                </tr>
                <tr>
                <?
				$grand_tot_in=0;
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
                    <?
				}
				?>
                </tr>
                
            </thead>
            <tbody>
            <?
			$i=1;
			$k=1;
			//var_dump($result);die;
			foreach($details_data as $color_id=>$value)
			{
				foreach($value as $country_id=>$row)
				{
					if(!in_array($color_id,$temp_arr))
					{
						$temp_arr[]=$color_id;
						if($k!=1)
						{
							?>
							<tr bgcolor="#CCCCCC">
								<td >&nbsp;</td>
								<td>&nbsp;</td>
								<td align="right" style="font-weight:bold">Color Total:</td>
								<?
								foreach($sizearr_order as $size_id)
								{
									?>
									<td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
									<?
								}
								?>
								<td align="right"><? echo number_format($line_color_total_in,0); ?></td>
							</tr>
							<?
							$line_color_size_in = $line_color_total_in ="";
						}
						$k++;
					}
					
					?>
					<tr>
						<td align="center"><? echo $i;  ?></td>
                        <?
						if(!in_array($color_id,$temp_arr_color))
						{
							$temp_arr_color[]=$color_id;
							?>
							<td rowspan="<? echo count($color_size_data[$color_id]); ?>" align="center" valign="middle"><p><? echo $colorarr[$color_id];  ?></p></td>
							<?
						}
						?>
						<td ><p><? echo $country_library[$country_id];  ?></p></td>
						<?
						$color_total_in=0;
						foreach($sizearr_order as $size_id)
						{
							?>
							<td align="right"><p>
							<?
								echo number_format($color_size_data[$color_id][$country_id][$size_id],0) ;
								 $color_total_in+= $color_size_data[$color_id][$country_id][$size_id]; 
								 $color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
								 $line_color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
							 ?>
							</p></td>
							<?
						}
						$line_color_total_in+=$color_total_in; 
						?>
						<td align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
					</tr>
					<?
					$i++;
				}
				
			}
			?>
            <tr bgcolor="#CCCCCC">
                <td >&nbsp;</td>
                <td>&nbsp;</td>
                <td align="right" style="font-weight:bold">Color Total:</td>
                <?
                foreach($sizearr_order as $size_id)
                {
                    ?>
                    <td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
                    <?
                }
                ?>
                <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
            </tr>
            </tbody>
            <tfoot>
                <th >&nbsp;</th>
                <th>&nbsp;</th>
                <th >Day Total:</th>
                <?
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th ><? echo $color_size_in[$size_id]; ?></th>
                    <?
				}
				?>
                <th ><? echo $grand_tot_in; ?></th>
                
            </tfoot>
        </table>
        </div>
    </fieldset>
<?	
}

if($action=='printing_receive_popup')
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  ); 
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  ); 
	
	
	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");
	
	$sql_color_size= sql_select("SELECT a.country_id, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id 
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c 
	where a.production_type=3 and a.embel_name=1 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
	group by   c.color_number_id,a.country_id,c.size_number_id
	order by c.color_number_id,a.country_id");
	foreach($sql_color_size as $row)
	{
		$details_data[$row[csf('color_number_id')]][$row[csf('country_id')]]=$row[csf('country_id')];
		$color_size_data[$row[csf('color_number_id')]][$row[csf('country_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}
	
	$col_width=60*count($sizearr_order);
	$table_width=330+$col_width;


?>	
<script>

	function window_close()
	{
		parent.emailwindow.hide();
	}
	
</script>	
<fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
    <div style="100%" id="report_container">
    
        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
        	<tr>
            	<td align="center" style="text-decoration:underline; font-size:20px; font-weight:bold">Date wise printing production</td>
            </tr>
        	<tr>
            	<td style="font-size:16px; font-weight:bold;">
                Date : <? echo change_date_format($production_date); ?> 
                <br />
                Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;
                </td>
            </tr>
        </table>
        
        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                	<th width="40" rowspan="2">SI</th>
                    <th width="100" rowspan="2">Color</th>
                    <th width="100" rowspan="2">Country Name</th>
                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
                    <th width="80" rowspan="2" >Total</th>
                </tr>
                <tr>
                <?
				$grand_tot_in=0;
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
                    <?
				}
				?>
                </tr>
                
            </thead>
            <tbody>
            <?
			$i=1;
			$k=1;
			//var_dump($result);die;
			foreach($details_data as $color_id=>$value)
			{
				foreach($value as $country_id=>$row)
				{
					if(!in_array($color_id,$temp_arr))
					{
						$temp_arr[]=$color_id;
						if($k!=1)
						{
							?>
							<tr bgcolor="#CCCCCC">
								<td >&nbsp;</td>
								<td>&nbsp;</td>
								<td align="right" style="font-weight:bold">Color Total:</td>
								<?
								foreach($sizearr_order as $size_id)
								{
									?>
									<td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
									<?
								}
								?>
								<td align="right"><? echo number_format($line_color_total_in,0); ?></td>
							</tr>
							<?
							$line_color_size_in = $line_color_total_in ="";
						}
						$k++;
					}
					
					?>
					<tr>
						<td align="center"><? echo $i;  ?></td>
                        <?
						if(!in_array($color_id,$temp_arr_color))
						{
							$temp_arr_color[]=$color_id;
							?>
							<td rowspan="<? echo count($color_size_data[$color_id]); ?>" align="center" valign="middle"><p><? echo $colorarr[$color_id];  ?></p></td>
							<?
						}
						?>
						<td ><p><? echo $country_library[$country_id];  ?></p></td>
						<?
						$color_total_in=0;
						foreach($sizearr_order as $size_id)
						{
							?>
							<td align="right"><p>
							<?
								echo number_format($color_size_data[$color_id][$country_id][$size_id],0) ;
								 $color_total_in+= $color_size_data[$color_id][$country_id][$size_id]; 
								 $color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
								 $line_color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
							 ?>
							</p></td>
							<?
						}
						$line_color_total_in+=$color_total_in; 
						?>
						<td align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
					</tr>
					<?
					$i++;
				}
				
			}
			?>
            <tr bgcolor="#CCCCCC">
                <td >&nbsp;</td>
                <td>&nbsp;</td>
                <td align="right" style="font-weight:bold">Color Total:</td>
                <?
                foreach($sizearr_order as $size_id)
                {
                    ?>
                    <td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
                    <?
                }
                ?>
                <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
            </tr>
            </tbody>
            <tfoot>
                <th >&nbsp;</th>
                <th>&nbsp;</th>
                <th >Day Total:</th>
                <?
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th ><? echo $color_size_in[$size_id]; ?></th>
                    <?
				}
				?>
                <th ><? echo $grand_tot_in; ?></th>
                
            </tfoot>
        </table>
        </div>
    </fieldset>
<?	
}

if($action=='printing_receive_popup_location')
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  ); 
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  ); 
	
	
	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");
	
	$location=str_replace("'","",$location);
	$floor_id=str_replace("'","",$floor_id);
	$sewing_line=str_replace("'","",$sewing_line);
	$location_cond=$floor_cond=$sewing_cond="";
	if($location!="") $location_cond="and a.location=$location";
	if($floor_id!="") $floor_cond=" and a.floor_id=$floor_id";
	if($sewing_line!="") $sewing_cond=" and a.sewing_line=$sewing_line";
	
	$sql_color_size= sql_select("SELECT a.country_id, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id 
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c 
	where a.production_type=3 and a.embel_name=1 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' $location_cond $floor_cond $sewing_cond and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
	group by   c.color_number_id,a.country_id,c.size_number_id
	order by c.color_number_id,a.country_id");
	foreach($sql_color_size as $row)
	{
		$details_data[$row[csf('color_number_id')]][$row[csf('country_id')]]=$row[csf('country_id')];
		$color_size_data[$row[csf('color_number_id')]][$row[csf('country_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}
	
	$col_width=60*count($sizearr_order);
	$table_width=330+$col_width;


?>	
<script>

	function window_close()
	{
		parent.emailwindow.hide();
	}
	
</script>	
<fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
    <div style="100%" id="report_container">
    
        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
        	<tr>
            	<td align="center" style="text-decoration:underline; font-size:20px; font-weight:bold">Date wise printing production</td>
            </tr>
        	<tr>
            	<td style="font-size:16px; font-weight:bold;">
                Date : <? echo change_date_format($production_date); ?> 
                <br />
                Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;
                </td>
            </tr>
        </table>
        
        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                	<th width="40" rowspan="2">SI</th>
                    <th width="100" rowspan="2">Color</th>
                    <th width="100" rowspan="2">Country Name</th>
                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
                    <th width="80" rowspan="2" >Total</th>
                </tr>
                <tr>
                <?
				$grand_tot_in=0;
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
                    <?
				}
				?>
                </tr>
                
            </thead>
            <tbody>
            <?
			$i=1;
			$k=1;
			//var_dump($result);die;
			foreach($details_data as $color_id=>$value)
			{
				foreach($value as $country_id=>$row)
				{
					if(!in_array($color_id,$temp_arr))
					{
						$temp_arr[]=$color_id;
						if($k!=1)
						{
							?>
							<tr bgcolor="#CCCCCC">
								<td >&nbsp;</td>
								<td>&nbsp;</td>
								<td align="right" style="font-weight:bold">Color Total:</td>
								<?
								foreach($sizearr_order as $size_id)
								{
									?>
									<td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
									<?
								}
								?>
								<td align="right"><? echo number_format($line_color_total_in,0); ?></td>
							</tr>
							<?
							$line_color_size_in = $line_color_total_in ="";
						}
						$k++;
					}
					
					?>
					<tr>
						<td align="center"><? echo $i;  ?></td>
                        <?
						if(!in_array($color_id,$temp_arr_color))
						{
							$temp_arr_color[]=$color_id;
							?>
							<td rowspan="<? echo count($color_size_data[$color_id]); ?>" align="center" valign="middle"><p><? echo $colorarr[$color_id];  ?></p></td>
							<?
						}
						?>
						<td ><p><? echo $country_library[$country_id];  ?></p></td>
						<?
						$color_total_in=0;
						foreach($sizearr_order as $size_id)
						{
							?>
							<td align="right"><p>
							<?
								echo number_format($color_size_data[$color_id][$country_id][$size_id],0) ;
								 $color_total_in+= $color_size_data[$color_id][$country_id][$size_id]; 
								 $color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
								 $line_color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
							 ?>
							</p></td>
							<?
						}
						$line_color_total_in+=$color_total_in; 
						?>
						<td align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
					</tr>
					<?
					$i++;
				}
				
			}
			?>
            <tr bgcolor="#CCCCCC">
                <td >&nbsp;</td>
                <td>&nbsp;</td>
                <td align="right" style="font-weight:bold">Color Total:</td>
                <?
                foreach($sizearr_order as $size_id)
                {
                    ?>
                    <td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
                    <?
                }
                ?>
                <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
            </tr>
            </tbody>
            <tfoot>
                <th >&nbsp;</th>
                <th>&nbsp;</th>
                <th >Day Total:</th>
                <?
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th ><? echo $color_size_in[$size_id]; ?></th>
                    <?
				}
				?>
                <th ><? echo $grand_tot_in; ?></th>
                
            </tfoot>
        </table>
        </div>
    </fieldset>
<?	
}

if($action=='embroi_issue_popup')
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  ); 
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  ); 
	
	
	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");
	
	$sql_color_size= sql_select("SELECT a.country_id, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id 
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c 
	where a.production_type=2 and a.embel_name=2 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
	group by   c.color_number_id,a.country_id,c.size_number_id
	order by c.color_number_id,a.country_id");
	foreach($sql_color_size as $row)
	{
		$details_data[$row[csf('color_number_id')]][$row[csf('country_id')]]=$row[csf('country_id')];
		$color_size_data[$row[csf('color_number_id')]][$row[csf('country_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}
	
	$col_width=60*count($sizearr_order);
	$table_width=330+$col_width;


?>	
<script>

	function window_close()
	{
		parent.emailwindow.hide();
	}
	
</script>	
<fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
    <div style="100%" id="report_container">
    
        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
        	<tr>
            	<td align="center" style="text-decoration:underline; font-size:20px; font-weight:bold">Date wise embroidery production</td>
            </tr>
        	<tr>
            	<td style="font-size:16px; font-weight:bold;">
                Date : <? echo change_date_format($production_date); ?> 
                <br />
                Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;
                </td>
            </tr>
        </table>
        
        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                	<th width="40" rowspan="2">SI</th>
                    <th width="100" rowspan="2">Color</th>
                    <th width="100" rowspan="2">Country Name</th>
                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
                    <th width="80" rowspan="2" >Total</th>
                </tr>
                <tr>
                <?
				$grand_tot_in=0;
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
                    <?
				}
				?>
                </tr>
                
            </thead>
            <tbody>
            <?
			$i=1;
			$k=1;
			//var_dump($result);die;
			foreach($details_data as $color_id=>$value)
			{
				foreach($value as $country_id=>$row)
				{
					if(!in_array($color_id,$temp_arr))
					{
						$temp_arr[]=$color_id;
						if($k!=1)
						{
							?>
							<tr bgcolor="#CCCCCC">
								<td >&nbsp;</td>
								<td>&nbsp;</td>
								<td align="right" style="font-weight:bold">Color Total:</td>
								<?
								foreach($sizearr_order as $size_id)
								{
									?>
									<td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
									<?
								}
								?>
								<td align="right"><? echo number_format($line_color_total_in,0); ?></td>
							</tr>
							<?
							$line_color_size_in = $line_color_total_in ="";
						}
						$k++;
					}
					
					?>
					<tr>
						<td align="center"><? echo $i;  ?></td>
                        <?
						if(!in_array($color_id,$temp_arr_color))
						{
							$temp_arr_color[]=$color_id;
							?>
							<td rowspan="<? echo count($color_size_data[$color_id]); ?>" align="center" valign="middle"><p><? echo $colorarr[$color_id];  ?></p></td>
							<?
						}
						?>
						<td ><p><? echo $country_library[$country_id];  ?></p></td>
						<?
						$color_total_in=0;
						foreach($sizearr_order as $size_id)
						{
							?>
							<td align="right"><p>
							<?
								echo number_format($color_size_data[$color_id][$country_id][$size_id],0) ;
								 $color_total_in+= $color_size_data[$color_id][$country_id][$size_id]; 
								 $color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
								 $line_color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
							 ?>
							</p></td>
							<?
						}
						$line_color_total_in+=$color_total_in; 
						?>
						<td align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
					</tr>
					<?
					$i++;
				}
				
			}
			?>
            <tr bgcolor="#CCCCCC">
                <td >&nbsp;</td>
                <td>&nbsp;</td>
                <td align="right" style="font-weight:bold">Color Total:</td>
                <?
                foreach($sizearr_order as $size_id)
                {
                    ?>
                    <td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
                    <?
                }
                ?>
                <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
            </tr>
            </tbody>
            <tfoot>
                <th >&nbsp;</th>
                <th>&nbsp;</th>
                <th >Day Total:</th>
                <?
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th ><? echo $color_size_in[$size_id]; ?></th>
                    <?
				}
				?>
                <th ><? echo $grand_tot_in; ?></th>
                
            </tfoot>
        </table>
        </div>
    </fieldset>
<?	
}

if($action=='embroi_issue_popup_location')
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  ); 
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  ); 
	
	
	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");
	
	$location=str_replace("'","",$location);
	$floor_id=str_replace("'","",$floor_id);
	$sewing_line=str_replace("'","",$sewing_line);
	$location_cond=$floor_cond=$sewing_cond="";
	if($location!="") $location_cond="and a.location=$location";
	if($floor_id!="") $floor_cond=" and a.floor_id=$floor_id";
	if($sewing_line!="") $sewing_cond=" and a.sewing_line=$sewing_line";
	
	$sql_color_size= sql_select("SELECT a.country_id, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id 
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c 
	where a.production_type=2 and a.embel_name=2 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' $location_cond $floor_cond $sewing_cond and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
	group by   c.color_number_id,a.country_id,c.size_number_id
	order by c.color_number_id,a.country_id");
	foreach($sql_color_size as $row)
	{
		$details_data[$row[csf('color_number_id')]][$row[csf('country_id')]]=$row[csf('country_id')];
		$color_size_data[$row[csf('color_number_id')]][$row[csf('country_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}
	
	$col_width=60*count($sizearr_order);
	$table_width=330+$col_width;


?>	
<script>

	function window_close()
	{
		parent.emailwindow.hide();
	}
	
</script>	
<fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
    <div style="100%" id="report_container">
    
        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
        	<tr>
            	<td align="center" style="text-decoration:underline; font-size:20px; font-weight:bold">Date wise embroidery production</td>
            </tr>
        	<tr>
            	<td style="font-size:16px; font-weight:bold;">
                Date : <? echo change_date_format($production_date); ?> 
                <br />
                Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;
                </td>
            </tr>
        </table>
        
        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                	<th width="40" rowspan="2">SI</th>
                    <th width="100" rowspan="2">Color</th>
                    <th width="100" rowspan="2">Country Name</th>
                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
                    <th width="80" rowspan="2" >Total</th>
                </tr>
                <tr>
                <?
				$grand_tot_in=0;
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
                    <?
				}
				?>
                </tr>
                
            </thead>
            <tbody>
            <?
			$i=1;
			$k=1;
			//var_dump($result);die;
			foreach($details_data as $color_id=>$value)
			{
				foreach($value as $country_id=>$row)
				{
					if(!in_array($color_id,$temp_arr))
					{
						$temp_arr[]=$color_id;
						if($k!=1)
						{
							?>
							<tr bgcolor="#CCCCCC">
								<td >&nbsp;</td>
								<td>&nbsp;</td>
								<td align="right" style="font-weight:bold">Color Total:</td>
								<?
								foreach($sizearr_order as $size_id)
								{
									?>
									<td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
									<?
								}
								?>
								<td align="right"><? echo number_format($line_color_total_in,0); ?></td>
							</tr>
							<?
							$line_color_size_in = $line_color_total_in ="";
						}
						$k++;
					}
					
					?>
					<tr>
						<td align="center"><? echo $i;  ?></td>
                        <?
						if(!in_array($color_id,$temp_arr_color))
						{
							$temp_arr_color[]=$color_id;
							?>
							<td rowspan="<? echo count($color_size_data[$color_id]); ?>" align="center" valign="middle"><p><? echo $colorarr[$color_id];  ?></p></td>
							<?
						}
						?>
						<td ><p><? echo $country_library[$country_id];  ?></p></td>
						<?
						$color_total_in=0;
						foreach($sizearr_order as $size_id)
						{
							?>
							<td align="right"><p>
							<?
								echo number_format($color_size_data[$color_id][$country_id][$size_id],0) ;
								 $color_total_in+= $color_size_data[$color_id][$country_id][$size_id]; 
								 $color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
								 $line_color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
							 ?>
							</p></td>
							<?
						}
						$line_color_total_in+=$color_total_in; 
						?>
						<td align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
					</tr>
					<?
					$i++;
				}
				
			}
			?>
            <tr bgcolor="#CCCCCC">
                <td >&nbsp;</td>
                <td>&nbsp;</td>
                <td align="right" style="font-weight:bold">Color Total:</td>
                <?
                foreach($sizearr_order as $size_id)
                {
                    ?>
                    <td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
                    <?
                }
                ?>
                <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
            </tr>
            </tbody>
            <tfoot>
                <th >&nbsp;</th>
                <th>&nbsp;</th>
                <th >Day Total:</th>
                <?
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th ><? echo $color_size_in[$size_id]; ?></th>
                    <?
				}
				?>
                <th ><? echo $grand_tot_in; ?></th>
                
            </tfoot>
        </table>
        </div>
    </fieldset>
<?	
}

if($action=='embroi_gmt_dyeing_popup_location')
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  ); 
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  ); 
	
	
	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");
	
	$location=str_replace("'","",$location);
	$floor_id=str_replace("'","",$floor_id);
	$sewing_line=str_replace("'","",$sewing_line);
	$location_cond=$floor_cond="";
	$sewing_cond="";
	if($location!=0) $location_cond="and a.location=$location";
	if($floor_id!=0) $floor_cond=" and a.floor_id=$floor_id";
	if($sewing_line!=0) $sewing_cond=" and a.sewing_line=$sewing_line";
	
	$sql_color_size= sql_select("SELECT a.country_id, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id 
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c 
	where a.production_type=$production_source and a.embel_name=5 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' $location_cond $floor_cond $sewing_cond and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
	group by   c.color_number_id,a.country_id,c.size_number_id
	order by c.color_number_id,a.country_id");
	foreach($sql_color_size as $row)
	{
		$details_data[$row[csf('color_number_id')]][$row[csf('country_id')]]=$row[csf('country_id')];
		$color_size_data[$row[csf('color_number_id')]][$row[csf('country_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}
	
	$col_width=60*count($sizearr_order);
	$table_width=330+$col_width;


?>	
<script>

	function window_close()
	{
		parent.emailwindow.hide();
	}
	
</script>	
<fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
    <div style="100%" id="report_container">
    
        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
        	<tr>
            	<td align="center" style="text-decoration:underline; font-size:20px; font-weight:bold">Date wise embroidery production</td>
            </tr>
        	<tr>
            	<td style="font-size:16px; font-weight:bold;">
                Date : <? echo change_date_format($production_date); ?> 
                <br />
                Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;
                </td>
            </tr>
        </table>
        
        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                	<th width="40" rowspan="2">SI</th>
                    <th width="100" rowspan="2">Color</th>
                    <th width="100" rowspan="2">Country Name</th>
                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
                    <th width="80" rowspan="2" >Total</th>
                </tr>
                <tr>
                <?
				$grand_tot_in=0;
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
                    <?
				}
				?>
                </tr>
                
            </thead>
            <tbody>
            <?
			$i=1;
			$k=1;
			//var_dump($result);die;
			foreach($details_data as $color_id=>$value)
			{
				foreach($value as $country_id=>$row)
				{
					if(!in_array($color_id,$temp_arr))
					{
						$temp_arr[]=$color_id;
						if($k!=1)
						{
							?>
							<tr bgcolor="#CCCCCC">
								<td >&nbsp;</td>
								<td>&nbsp;</td>
								<td align="right" style="font-weight:bold">Color Total:</td>
								<?
								foreach($sizearr_order as $size_id)
								{
									?>
									<td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
									<?
								}
								?>
								<td align="right"><? echo number_format($line_color_total_in,0); ?></td>
							</tr>
							<?
							$line_color_size_in = $line_color_total_in ="";
						}
						$k++;
					}
					
					?>
					<tr>
						<td align="center"><? echo $i;  ?></td>
                        <?
						if(!in_array($color_id,$temp_arr_color))
						{
							$temp_arr_color[]=$color_id;
							?>
							<td rowspan="<? echo count($color_size_data[$color_id]); ?>" align="center" valign="middle"><p><? echo $colorarr[$color_id];  ?></p></td>
							<?
						}
						?>
						<td ><p><? echo $country_library[$country_id];  ?></p></td>
						<?
						$color_total_in=0;
						foreach($sizearr_order as $size_id)
						{
							?>
							<td align="right"><p>
							<?
								echo number_format($color_size_data[$color_id][$country_id][$size_id],0) ;
								 $color_total_in+= $color_size_data[$color_id][$country_id][$size_id]; 
								 $color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
								 $line_color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
							 ?>
							</p></td>
							<?
						}
						$line_color_total_in+=$color_total_in; 
						?>
						<td align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
					</tr>
					<?
					$i++;
				}
				
			}
			?>
            <tr bgcolor="#CCCCCC">
                <td >&nbsp;</td>
                <td>&nbsp;</td>
                <td align="right" style="font-weight:bold">Color Total:</td>
                <?
                foreach($sizearr_order as $size_id)
                {
                    ?>
                    <td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
                    <?
                }
                ?>
                <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
            </tr>
            </tbody>
            <tfoot>
                <th >&nbsp;</th>
                <th>&nbsp;</th>
                <th >Day Total:</th>
                <?
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th ><? echo $color_size_in[$size_id]; ?></th>
                    <?
				}
				?>
                <th ><? echo $grand_tot_in; ?></th>
                
            </tfoot>
        </table>
        </div>
    </fieldset>
<?	
}

if($action=='embroi_receive_popup')
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  ); 
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  ); 
	
	
	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");
	
	$sql_color_size= sql_select("SELECT a.country_id, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id 
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c 
	where a.production_type=3 and a.embel_name=2 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
	group by   c.color_number_id,a.country_id,c.size_number_id
	order by c.color_number_id,a.country_id");
	foreach($sql_color_size as $row)
	{
		$details_data[$row[csf('color_number_id')]][$row[csf('country_id')]]=$row[csf('country_id')];
		$color_size_data[$row[csf('color_number_id')]][$row[csf('country_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}
	
	$col_width=60*count($sizearr_order);
	$table_width=330+$col_width;


?>	
<script>

	function window_close()
	{
		parent.emailwindow.hide();
	}
	
</script>	
<fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
    <div style="100%" id="report_container">
    
        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
        	<tr>
            	<td align="center" style="text-decoration:underline; font-size:20px; font-weight:bold">Date wise embroidery production</td>
            </tr>
        	<tr>
            	<td style="font-size:16px; font-weight:bold;">
                Date : <? echo change_date_format($production_date); ?> 
                <br />
                Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;
                </td>
            </tr>
        </table>
        
        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                	<th width="40" rowspan="2">SI</th>
                    <th width="100" rowspan="2">Color</th>
                    <th width="100" rowspan="2">Country Name</th>
                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
                    <th width="80" rowspan="2" >Total</th>
                </tr>
                <tr>
                <?
				$grand_tot_in=0;
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
                    <?
				}
				?>
                </tr>
                
            </thead>
            <tbody>
            <?
			$i=1;
			$k=1;
			//var_dump($result);die;
			foreach($details_data as $color_id=>$value)
			{
				foreach($value as $country_id=>$row)
				{
					if(!in_array($color_id,$temp_arr))
					{
						$temp_arr[]=$color_id;
						if($k!=1)
						{
							?>
							<tr bgcolor="#CCCCCC">
								<td >&nbsp;</td>
								<td>&nbsp;</td>
								<td align="right" style="font-weight:bold">Color Total:</td>
								<?
								foreach($sizearr_order as $size_id)
								{
									?>
									<td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
									<?
								}
								?>
								<td align="right"><? echo number_format($line_color_total_in,0); ?></td>
							</tr>
							<?
							$line_color_size_in = $line_color_total_in ="";
						}
						$k++;
					}
					
					?>
					<tr>
						<td align="center"><? echo $i;  ?></td>
                        <?
						if(!in_array($color_id,$temp_arr_color))
						{
							$temp_arr_color[]=$color_id;
							?>
							<td rowspan="<? echo count($color_size_data[$color_id]); ?>" align="center" valign="middle"><p><? echo $colorarr[$color_id];  ?></p></td>
							<?
						}
						?>
						<td ><p><? echo $country_library[$country_id];  ?></p></td>
						<?
						$color_total_in=0;
						foreach($sizearr_order as $size_id)
						{
							?>
							<td align="right"><p>
							<?
								echo number_format($color_size_data[$color_id][$country_id][$size_id],0) ;
								 $color_total_in+= $color_size_data[$color_id][$country_id][$size_id]; 
								 $color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
								 $line_color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
							 ?>
							</p></td>
							<?
						}
						$line_color_total_in+=$color_total_in; 
						?>
						<td align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
					</tr>
					<?
					$i++;
				}
				
			}
			?>
            <tr bgcolor="#CCCCCC">
                <td >&nbsp;</td>
                <td>&nbsp;</td>
                <td align="right" style="font-weight:bold">Color Total:</td>
                <?
                foreach($sizearr_order as $size_id)
                {
                    ?>
                    <td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
                    <?
                }
                ?>
                <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
            </tr>
            </tbody>
            <tfoot>
                <th >&nbsp;</th>
                <th>&nbsp;</th>
                <th >Day Total:</th>
                <?
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th ><? echo $color_size_in[$size_id]; ?></th>
                    <?
				}
				?>
                <th ><? echo $grand_tot_in; ?></th>
                
            </tfoot>
        </table>
        </div>
    </fieldset>
<?	
}

if($action=='embroi_gmt_dyeing_popup')
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  ); 
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  ); 
	
	
	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");
	
	$sql_color_size= sql_select("SELECT a.country_id, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id 
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c 
	where a.production_type=$production_source and a.embel_name=5 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
	group by   c.color_number_id,a.country_id,c.size_number_id
	order by c.color_number_id,a.country_id");
	foreach($sql_color_size as $row)
	{
		$details_data[$row[csf('color_number_id')]][$row[csf('country_id')]]=$row[csf('country_id')];
		$color_size_data[$row[csf('color_number_id')]][$row[csf('country_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}
	
	$col_width=60*count($sizearr_order);
	$table_width=330+$col_width;


?>	
<script>

	function window_close()
	{
		parent.emailwindow.hide();
	}
	
</script>	
<fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
    <div style="100%" id="report_container">
    
        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
        	<tr>
            	<td align="center" style="text-decoration:underline; font-size:20px; font-weight:bold">Date wise embroidery production</td>
            </tr>
        	<tr>
            	<td style="font-size:16px; font-weight:bold;">
                Date : <? echo change_date_format($production_date); ?> 
                <br />
                Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;
                </td>
            </tr>
        </table>
        
        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                	<th width="40" rowspan="2">SI</th>
                    <th width="100" rowspan="2">Color</th>
                    <th width="100" rowspan="2">Country Name</th>
                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
                    <th width="80" rowspan="2" >Total</th>
                </tr>
                <tr>
                <?
				$grand_tot_in=0;
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
                    <?
				}
				?>
                </tr>
                
            </thead>
            <tbody>
            <?
			$i=1;
			$k=1;
			//var_dump($result);die;
			foreach($details_data as $color_id=>$value)
			{
				foreach($value as $country_id=>$row)
				{
					if(!in_array($color_id,$temp_arr))
					{
						$temp_arr[]=$color_id;
						if($k!=1)
						{
							?>
							<tr bgcolor="#CCCCCC">
								<td >&nbsp;</td>
								<td>&nbsp;</td>
								<td align="right" style="font-weight:bold">Color Total:</td>
								<?
								foreach($sizearr_order as $size_id)
								{
									?>
									<td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
									<?
								}
								?>
								<td align="right"><? echo number_format($line_color_total_in,0); ?></td>
							</tr>
							<?
							$line_color_size_in = $line_color_total_in ="";
						}
						$k++;
					}
					
					?>
					<tr>
						<td align="center"><? echo $i;  ?></td>
                        <?
						if(!in_array($color_id,$temp_arr_color))
						{
							$temp_arr_color[]=$color_id;
							?>
							<td rowspan="<? echo count($color_size_data[$color_id]); ?>" align="center" valign="middle"><p><? echo $colorarr[$color_id];  ?></p></td>
							<?
						}
						?>
						<td ><p><? echo $country_library[$country_id];  ?></p></td>
						<?
						$color_total_in=0;
						foreach($sizearr_order as $size_id)
						{
							?>
							<td align="right"><p>
							<?
								echo number_format($color_size_data[$color_id][$country_id][$size_id],0) ;
								 $color_total_in+= $color_size_data[$color_id][$country_id][$size_id]; 
								 $color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
								 $line_color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
							 ?>
							</p></td>
							<?
						}
						$line_color_total_in+=$color_total_in; 
						?>
						<td align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
					</tr>
					<?
					$i++;
				}
				
			}
			?>
            <tr bgcolor="#CCCCCC">
                <td >&nbsp;</td>
                <td>&nbsp;</td>
                <td align="right" style="font-weight:bold">Color Total:</td>
                <?
                foreach($sizearr_order as $size_id)
                {
                    ?>
                    <td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
                    <?
                }
                ?>
                <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
            </tr>
            </tbody>
            <tfoot>
                <th >&nbsp;</th>
                <th>&nbsp;</th>
                <th >Day Total:</th>
                <?
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th ><? echo $color_size_in[$size_id]; ?></th>
                    <?
				}
				?>
                <th ><? echo $grand_tot_in; ?></th>
                
            </tfoot>
        </table>
        </div>
    </fieldset>
<?	
}

if($action=='embroi_receive_popup_location')
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  ); 
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  ); 
	
	
	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");
	
	$location=str_replace("'","",$location);
	$floor_id=str_replace("'","",$floor_id);
	$sewing_line=str_replace("'","",$sewing_line);
	$location_cond=$floor_cond=$sewing_cond="";
	if($location!="") $location_cond="and a.location=$location";
	if($floor_id!="") $floor_cond=" and a.floor_id=$floor_id";
	if($sewing_line!="") $sewing_cond=" and a.sewing_line=$sewing_line";
	
	$sql_color_size= sql_select("SELECT a.country_id, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id 
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c 
	where a.production_type=3 and a.embel_name=2 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' $location_cond $floor_cond $sewing_cond and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
	group by   c.color_number_id,a.country_id,c.size_number_id
	order by c.color_number_id,a.country_id");
	foreach($sql_color_size as $row)
	{
		$details_data[$row[csf('color_number_id')]][$row[csf('country_id')]]=$row[csf('country_id')];
		$color_size_data[$row[csf('color_number_id')]][$row[csf('country_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}
	
	$col_width=60*count($sizearr_order);
	$table_width=330+$col_width;


?>	
<script>

	function window_close()
	{
		parent.emailwindow.hide();
	}
	
</script>	
<fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
    <div style="100%" id="report_container">
    
        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
        	<tr>
            	<td align="center" style="text-decoration:underline; font-size:20px; font-weight:bold">Date wise embroidery production</td>
            </tr>
        	<tr>
            	<td style="font-size:16px; font-weight:bold;">
                Date : <? echo change_date_format($production_date); ?> 
                <br />
                Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;
                </td>
            </tr>
        </table>
        
        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                	<th width="40" rowspan="2">SI</th>
                    <th width="100" rowspan="2">Color</th>
                    <th width="100" rowspan="2">Country Name</th>
                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
                    <th width="80" rowspan="2" >Total</th>
                </tr>
                <tr>
                <?
				$grand_tot_in=0;
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
                    <?
				}
				?>
                </tr>
                
            </thead>
            <tbody>
            <?
			$i=1;
			$k=1;
			//var_dump($result);die;
			foreach($details_data as $color_id=>$value)
			{
				foreach($value as $country_id=>$row)
				{
					if(!in_array($color_id,$temp_arr))
					{
						$temp_arr[]=$color_id;
						if($k!=1)
						{
							?>
							<tr bgcolor="#CCCCCC">
								<td >&nbsp;</td>
								<td>&nbsp;</td>
								<td align="right" style="font-weight:bold">Color Total:</td>
								<?
								foreach($sizearr_order as $size_id)
								{
									?>
									<td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
									<?
								}
								?>
								<td align="right"><? echo number_format($line_color_total_in,0); ?></td>
							</tr>
							<?
							$line_color_size_in = $line_color_total_in ="";
						}
						$k++;
					}
					
					?>
					<tr>
						<td align="center"><? echo $i;  ?></td>
                        <?
						if(!in_array($color_id,$temp_arr_color))
						{
							$temp_arr_color[]=$color_id;
							?>
							<td rowspan="<? echo count($color_size_data[$color_id]); ?>" align="center" valign="middle"><p><? echo $colorarr[$color_id];  ?></p></td>
							<?
						}
						?>
						<td ><p><? echo $country_library[$country_id];  ?></p></td>
						<?
						$color_total_in=0;
						foreach($sizearr_order as $size_id)
						{
							?>
							<td align="right"><p>
							<?
								echo number_format($color_size_data[$color_id][$country_id][$size_id],0) ;
								 $color_total_in+= $color_size_data[$color_id][$country_id][$size_id]; 
								 $color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
								 $line_color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
							 ?>
							</p></td>
							<?
						}
						$line_color_total_in+=$color_total_in; 
						?>
						<td align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
					</tr>
					<?
					$i++;
				}
				
			}
			?>
            <tr bgcolor="#CCCCCC">
                <td >&nbsp;</td>
                <td>&nbsp;</td>
                <td align="right" style="font-weight:bold">Color Total:</td>
                <?
                foreach($sizearr_order as $size_id)
                {
                    ?>
                    <td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
                    <?
                }
                ?>
                <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
            </tr>
            </tbody>
            <tfoot>
                <th >&nbsp;</th>
                <th>&nbsp;</th>
                <th >Day Total:</th>
                <?
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th ><? echo $color_size_in[$size_id]; ?></th>
                    <?
				}
				?>
                <th ><? echo $grand_tot_in; ?></th>
                
            </tfoot>
        </table>
        </div>
    </fieldset>
<?	
}

if($action=='wash_issue_popup')
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  ); 
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  ); 
	
	
	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");
	
	$sql_color_size= sql_select("SELECT a.country_id, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id 
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c 
	where a.production_type=2 and a.embel_name=3 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
	group by   c.color_number_id,a.country_id,c.size_number_id
	order by c.color_number_id,a.country_id");
	foreach($sql_color_size as $row)
	{
		$details_data[$row[csf('color_number_id')]][$row[csf('country_id')]]=$row[csf('country_id')];
		$color_size_data[$row[csf('color_number_id')]][$row[csf('country_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}
	
	$col_width=60*count($sizearr_order);
	$table_width=330+$col_width;


?>	
<script>

	function window_close()
	{
		parent.emailwindow.hide();
	}
	
</script>	
<fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
    <div style="100%" id="report_container">
    
        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
        	<tr>
            	<td align="center" style="text-decoration:underline; font-size:20px; font-weight:bold">Date wise wash production</td>
            </tr>
        	<tr>
            	<td style="font-size:16px; font-weight:bold;">
                Date : <? echo change_date_format($production_date); ?> 
                <br />
                Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;
                </td>
            </tr>
        </table>
        
        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                	<th width="40" rowspan="2">SI</th>
                    <th width="100" rowspan="2">Color</th>
                    <th width="100" rowspan="2">Country Name</th>
                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
                    <th width="80" rowspan="2" >Total</th>
                </tr>
                <tr>
                <?
				$grand_tot_in=0;
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
                    <?
				}
				?>
                </tr>
                
            </thead>
            <tbody>
            <?
			$i=1;
			$k=1;
			//var_dump($result);die;
			foreach($details_data as $color_id=>$value)
			{
				foreach($value as $country_id=>$row)
				{
					if(!in_array($color_id,$temp_arr))
					{
						$temp_arr[]=$color_id;
						if($k!=1)
						{
							?>
							<tr bgcolor="#CCCCCC">
								<td >&nbsp;</td>
								<td>&nbsp;</td>
								<td align="right" style="font-weight:bold">Color Total:</td>
								<?
								foreach($sizearr_order as $size_id)
								{
									?>
									<td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
									<?
								}
								?>
								<td align="right"><? echo number_format($line_color_total_in,0); ?></td>
							</tr>
							<?
							$line_color_size_in = $line_color_total_in ="";
						}
						$k++;
					}
					
					?>
					<tr>
						<td align="center"><? echo $i;  ?></td>
                        <?
						if(!in_array($color_id,$temp_arr_color))
						{
							$temp_arr_color[]=$color_id;
							?>
							<td rowspan="<? echo count($color_size_data[$color_id]); ?>" align="center" valign="middle"><p><? echo $colorarr[$color_id];  ?></p></td>
							<?
						}
						?>
						<td ><p><? echo $country_library[$country_id];  ?></p></td>
						<?
						$color_total_in=0;
						foreach($sizearr_order as $size_id)
						{
							?>
							<td align="right"><p>
							<?
								echo number_format($color_size_data[$color_id][$country_id][$size_id],0) ;
								 $color_total_in+= $color_size_data[$color_id][$country_id][$size_id]; 
								 $color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
								 $line_color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
							 ?>
							</p></td>
							<?
						}
						$line_color_total_in+=$color_total_in; 
						?>
						<td align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
					</tr>
					<?
					$i++;
				}
				
			}
			?>
            <tr bgcolor="#CCCCCC">
                <td >&nbsp;</td>
                <td>&nbsp;</td>
                <td align="right" style="font-weight:bold">Color Total:</td>
                <?
                foreach($sizearr_order as $size_id)
                {
                    ?>
                    <td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
                    <?
                }
                ?>
                <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
            </tr>
            </tbody>
            <tfoot>
                <th >&nbsp;</th>
                <th>&nbsp;</th>
                <th >Day Total:</th>
                <?
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th ><? echo $color_size_in[$size_id]; ?></th>
                    <?
				}
				?>
                <th ><? echo $grand_tot_in; ?></th>
                
            </tfoot>
        </table>
        </div>
    </fieldset>
<?	
}

if($action=='wash_issue_popup_location')
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  ); 
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  ); 
	
	
	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");
	
	$location=str_replace("'","",$location);
	$floor_id=str_replace("'","",$floor_id);
	$sewing_line=str_replace("'","",$sewing_line);
	$location_cond=$floor_cond=$sewing_cond="";
	if($location!="") $location_cond="and a.location=$location";
	if($floor_id!="") $floor_cond=" and a.floor_id=$floor_id";
	if($sewing_line!="") $sewing_cond=" and a.sewing_line=$sewing_line";
	
	$sql_color_size= sql_select("SELECT a.country_id, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id 
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c 
	where a.production_type=2 and a.embel_name=3 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' $location_cond $floor_cond $sewing_cond and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
	group by   c.color_number_id,a.country_id,c.size_number_id
	order by c.color_number_id,a.country_id");
	foreach($sql_color_size as $row)
	{
		$details_data[$row[csf('color_number_id')]][$row[csf('country_id')]]=$row[csf('country_id')];
		$color_size_data[$row[csf('color_number_id')]][$row[csf('country_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}
	
	$col_width=60*count($sizearr_order);
	$table_width=330+$col_width;


?>	
<script>

	function window_close()
	{
		parent.emailwindow.hide();
	}
	
</script>	
<fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
    <div style="100%" id="report_container">
    
        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
        	<tr>
            	<td align="center" style="text-decoration:underline; font-size:20px; font-weight:bold">Date wise wash production</td>
            </tr>
        	<tr>
            	<td style="font-size:16px; font-weight:bold;">
                Date : <? echo change_date_format($production_date); ?> 
                <br />
                Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;
                </td>
            </tr>
        </table>
        
        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                	<th width="40" rowspan="2">SI</th>
                    <th width="100" rowspan="2">Color</th>
                    <th width="100" rowspan="2">Country Name</th>
                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
                    <th width="80" rowspan="2" >Total</th>
                </tr>
                <tr>
                <?
				$grand_tot_in=0;
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
                    <?
				}
				?>
                </tr>
                
            </thead>
            <tbody>
            <?
			$i=1;
			$k=1;
			//var_dump($result);die;
			foreach($details_data as $color_id=>$value)
			{
				foreach($value as $country_id=>$row)
				{
					if(!in_array($color_id,$temp_arr))
					{
						$temp_arr[]=$color_id;
						if($k!=1)
						{
							?>
							<tr bgcolor="#CCCCCC">
								<td >&nbsp;</td>
								<td>&nbsp;</td>
								<td align="right" style="font-weight:bold">Color Total:</td>
								<?
								foreach($sizearr_order as $size_id)
								{
									?>
									<td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
									<?
								}
								?>
								<td align="right"><? echo number_format($line_color_total_in,0); ?></td>
							</tr>
							<?
							$line_color_size_in = $line_color_total_in ="";
						}
						$k++;
					}
					
					?>
					<tr>
						<td align="center"><? echo $i;  ?></td>
                        <?
						if(!in_array($color_id,$temp_arr_color))
						{
							$temp_arr_color[]=$color_id;
							?>
							<td rowspan="<? echo count($color_size_data[$color_id]); ?>" align="center" valign="middle"><p><? echo $colorarr[$color_id];  ?></p></td>
							<?
						}
						?>
						<td ><p><? echo $country_library[$country_id];  ?></p></td>
						<?
						$color_total_in=0;
						foreach($sizearr_order as $size_id)
						{
							?>
							<td align="right"><p>
							<?
								echo number_format($color_size_data[$color_id][$country_id][$size_id],0) ;
								 $color_total_in+= $color_size_data[$color_id][$country_id][$size_id]; 
								 $color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
								 $line_color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
							 ?>
							</p></td>
							<?
						}
						$line_color_total_in+=$color_total_in; 
						?>
						<td align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
					</tr>
					<?
					$i++;
				}
				
			}
			?>
            <tr bgcolor="#CCCCCC">
                <td >&nbsp;</td>
                <td>&nbsp;</td>
                <td align="right" style="font-weight:bold">Color Total:</td>
                <?
                foreach($sizearr_order as $size_id)
                {
                    ?>
                    <td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
                    <?
                }
                ?>
                <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
            </tr>
            </tbody>
            <tfoot>
                <th >&nbsp;</th>
                <th>&nbsp;</th>
                <th >Day Total:</th>
                <?
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th ><? echo $color_size_in[$size_id]; ?></th>
                    <?
				}
				?>
                <th ><? echo $grand_tot_in; ?></th>
                
            </tfoot>
        </table>
        </div>
    </fieldset>
<?	
}

if($action=='wash_receive_popup')
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  ); 
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  ); 
	
	
	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");
	
	$sql_color_size= sql_select("SELECT a.country_id, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id 
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c 
	where a.production_type=3 and a.embel_name=3 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
	group by   c.color_number_id,a.country_id,c.size_number_id
	order by c.color_number_id,a.country_id");
	foreach($sql_color_size as $row)
	{
		$details_data[$row[csf('color_number_id')]][$row[csf('country_id')]]=$row[csf('country_id')];
		$color_size_data[$row[csf('color_number_id')]][$row[csf('country_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}
	
	$col_width=60*count($sizearr_order);
	$table_width=330+$col_width;


?>	
<script>

	function window_close()
	{
		parent.emailwindow.hide();
	}
	
</script>	
<fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
    <div style="100%" id="report_container">
    
        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
        	<tr>
            	<td align="center" style="text-decoration:underline; font-size:20px; font-weight:bold">Date wise wash production</td>
            </tr>
        	<tr>
            	<td style="font-size:16px; font-weight:bold;">
                Date : <? echo change_date_format($production_date); ?> 
                <br />
                Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;
                </td>
            </tr>
        </table>
        
        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                	<th width="40" rowspan="2">SI</th>
                    <th width="100" rowspan="2">Color</th>
                    <th width="100" rowspan="2">Country Name</th>
                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
                    <th width="80" rowspan="2" >Total</th>
                </tr>
                <tr>
                <?
				$grand_tot_in=0;
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
                    <?
				}
				?>
                </tr>
                
            </thead>
            <tbody>
            <?
			$i=1;
			$k=1;
			//var_dump($result);die;
			foreach($details_data as $color_id=>$value)
			{
				foreach($value as $country_id=>$row)
				{
					if(!in_array($color_id,$temp_arr))
					{
						$temp_arr[]=$color_id;
						if($k!=1)
						{
							?>
							<tr bgcolor="#CCCCCC">
								<td >&nbsp;</td>
								<td>&nbsp;</td>
								<td align="right" style="font-weight:bold">Color Total:</td>
								<?
								foreach($sizearr_order as $size_id)
								{
									?>
									<td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
									<?
								}
								?>
								<td align="right"><? echo number_format($line_color_total_in,0); ?></td>
							</tr>
							<?
							$line_color_size_in = $line_color_total_in ="";
						}
						$k++;
					}
					
					?>
					<tr>
						<td align="center"><? echo $i;  ?></td>
                        <?
						if(!in_array($color_id,$temp_arr_color))
						{
							$temp_arr_color[]=$color_id;
							?>
							<td rowspan="<? echo count($color_size_data[$color_id]); ?>" align="center" valign="middle"><p><? echo $colorarr[$color_id];  ?></p></td>
							<?
						}
						?>
						<td ><p><? echo $country_library[$country_id];  ?></p></td>
						<?
						$color_total_in=0;
						foreach($sizearr_order as $size_id)
						{
							?>
							<td align="right"><p>
							<?
								echo number_format($color_size_data[$color_id][$country_id][$size_id],0) ;
								 $color_total_in+= $color_size_data[$color_id][$country_id][$size_id]; 
								 $color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
								 $line_color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
							 ?>
							</p></td>
							<?
						}
						$line_color_total_in+=$color_total_in; 
						?>
						<td align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
					</tr>
					<?
					$i++;
				}
				
			}
			?>
            <tr bgcolor="#CCCCCC">
                <td >&nbsp;</td>
                <td>&nbsp;</td>
                <td align="right" style="font-weight:bold">Color Total:</td>
                <?
                foreach($sizearr_order as $size_id)
                {
                    ?>
                    <td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
                    <?
                }
                ?>
                <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
            </tr>
            </tbody>
            <tfoot>
                <th >&nbsp;</th>
                <th>&nbsp;</th>
                <th >Day Total:</th>
                <?
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th ><? echo $color_size_in[$size_id]; ?></th>
                    <?
				}
				?>
                <th ><? echo $grand_tot_in; ?></th>
                
            </tfoot>
        </table>
        </div>
    </fieldset>
<?	
}

if($action=='wash_receive_popup_location')
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  ); 
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  ); 
	
	
	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");
	
	$location=str_replace("'","",$location);
	$floor_id=str_replace("'","",$floor_id);
	$sewing_line=str_replace("'","",$sewing_line);
	$location_cond=$floor_cond=$sewing_cond="";
	if($location!="") $location_cond="and a.location=$location";
	if($floor_id!="") $floor_cond=" and a.floor_id=$floor_id";
	if($sewing_line!="") $sewing_cond=" and a.sewing_line=$sewing_line";
	
	$sql_color_size= sql_select("SELECT a.country_id, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id 
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c 
	where a.production_type=3 and a.embel_name=3 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' $location_cond $floor_cond $sewing_cond and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
	group by   c.color_number_id,a.country_id,c.size_number_id
	order by c.color_number_id,a.country_id");
	foreach($sql_color_size as $row)
	{
		$details_data[$row[csf('color_number_id')]][$row[csf('country_id')]]=$row[csf('country_id')];
		$color_size_data[$row[csf('color_number_id')]][$row[csf('country_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}
	
	$col_width=60*count($sizearr_order);
	$table_width=330+$col_width;


?>	
<script>

	function window_close()
	{
		parent.emailwindow.hide();
	}
	
</script>	
<fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
    <div style="100%" id="report_container">
    
        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
        	<tr>
            	<td align="center" style="text-decoration:underline; font-size:20px; font-weight:bold">Date wise wash production</td>
            </tr>
        	<tr>
            	<td style="font-size:16px; font-weight:bold;">
                Date : <? echo change_date_format($production_date); ?> 
                <br />
                Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;
                </td>
            </tr>
        </table>
        
        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                	<th width="40" rowspan="2">SI</th>
                    <th width="100" rowspan="2">Color</th>
                    <th width="100" rowspan="2">Country Name</th>
                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
                    <th width="80" rowspan="2" >Total</th>
                </tr>
                <tr>
                <?
				$grand_tot_in=0;
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
                    <?
				}
				?>
                </tr>
                
            </thead>
            <tbody>
            <?
			$i=1;
			$k=1;
			//var_dump($result);die;
			foreach($details_data as $color_id=>$value)
			{
				foreach($value as $country_id=>$row)
				{
					if(!in_array($color_id,$temp_arr))
					{
						$temp_arr[]=$color_id;
						if($k!=1)
						{
							?>
							<tr bgcolor="#CCCCCC">
								<td >&nbsp;</td>
								<td>&nbsp;</td>
								<td align="right" style="font-weight:bold">Color Total:</td>
								<?
								foreach($sizearr_order as $size_id)
								{
									?>
									<td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
									<?
								}
								?>
								<td align="right"><? echo number_format($line_color_total_in,0); ?></td>
							</tr>
							<?
							$line_color_size_in = $line_color_total_in ="";
						}
						$k++;
					}
					
					?>
					<tr>
						<td align="center"><? echo $i;  ?></td>
                        <?
						if(!in_array($color_id,$temp_arr_color))
						{
							$temp_arr_color[]=$color_id;
							?>
							<td rowspan="<? echo count($color_size_data[$color_id]); ?>" align="center" valign="middle"><p><? echo $colorarr[$color_id];  ?></p></td>
							<?
						}
						?>
						<td ><p><? echo $country_library[$country_id];  ?></p></td>
						<?
						$color_total_in=0;
						foreach($sizearr_order as $size_id)
						{
							?>
							<td align="right"><p>
							<?
								echo number_format($color_size_data[$color_id][$country_id][$size_id],0) ;
								 $color_total_in+= $color_size_data[$color_id][$country_id][$size_id]; 
								 $color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
								 $line_color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
							 ?>
							</p></td>
							<?
						}
						$line_color_total_in+=$color_total_in; 
						?>
						<td align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
					</tr>
					<?
					$i++;
				}
				
			}
			?>
            <tr bgcolor="#CCCCCC">
                <td >&nbsp;</td>
                <td>&nbsp;</td>
                <td align="right" style="font-weight:bold">Color Total:</td>
                <?
                foreach($sizearr_order as $size_id)
                {
                    ?>
                    <td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
                    <?
                }
                ?>
                <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
            </tr>
            </tbody>
            <tfoot>
                <th >&nbsp;</th>
                <th>&nbsp;</th>
                <th >Day Total:</th>
                <?
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th ><? echo $color_size_in[$size_id]; ?></th>
                    <?
				}
				?>
                <th ><? echo $grand_tot_in; ?></th>
                
            </tfoot>
        </table>
        </div>
    </fieldset>
<?	
}

if($action=='sp_issue_popup')
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  ); 
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  ); 
	
	
	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");
	
	$sql_color_size= sql_select("SELECT a.country_id, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id 
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c 
	where a.production_type=2 and a.embel_name=4 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
	group by   c.color_number_id,a.country_id,c.size_number_id
	order by c.color_number_id,a.country_id");
	foreach($sql_color_size as $row)
	{
		$details_data[$row[csf('color_number_id')]][$row[csf('country_id')]]=$row[csf('country_id')];
		$color_size_data[$row[csf('color_number_id')]][$row[csf('country_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}
	
	$col_width=60*count($sizearr_order);
	$table_width=330+$col_width;


?>	
<script>

	function window_close()
	{
		parent.emailwindow.hide();
	}
	
</script>	
<fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
    <div style="100%" id="report_container">
    
        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
        	<tr>
            	<td align="center" style="text-decoration:underline; font-size:20px; font-weight:bold">Date wise special works production</td>
            </tr>
        	<tr>
            	<td style="font-size:16px; font-weight:bold;">
                Date : <? echo change_date_format($production_date); ?> 
                <br />
                Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;
                </td>
            </tr>
        </table>
        
        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                	<th width="40" rowspan="2">SI</th>
                    <th width="100" rowspan="2">Color</th>
                    <th width="100" rowspan="2">Country Name</th>
                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
                    <th width="80" rowspan="2" >Total</th>
                </tr>
                <tr>
                <?
				$grand_tot_in=0;
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
                    <?
				}
				?>
                </tr>
                
            </thead>
            <tbody>
            <?
			$i=1;
			$k=1;
			//var_dump($result);die;
			foreach($details_data as $color_id=>$value)
			{
				foreach($value as $country_id=>$row)
				{
					if(!in_array($color_id,$temp_arr))
					{
						$temp_arr[]=$color_id;
						if($k!=1)
						{
							?>
							<tr bgcolor="#CCCCCC">
								<td >&nbsp;</td>
								<td>&nbsp;</td>
								<td align="right" style="font-weight:bold">Color Total:</td>
								<?
								foreach($sizearr_order as $size_id)
								{
									?>
									<td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
									<?
								}
								?>
								<td align="right"><? echo number_format($line_color_total_in,0); ?></td>
							</tr>
							<?
							$line_color_size_in = $line_color_total_in ="";
						}
						$k++;
					}
					
					?>
					<tr>
						<td align="center"><? echo $i;  ?></td>
                        <?
						if(!in_array($color_id,$temp_arr_color))
						{
							$temp_arr_color[]=$color_id;
							?>
							<td rowspan="<? echo count($color_size_data[$color_id]); ?>" align="center" valign="middle"><p><? echo $colorarr[$color_id];  ?></p></td>
							<?
						}
						?>
						<td ><p><? echo $country_library[$country_id];  ?></p></td>
						<?
						$color_total_in=0;
						foreach($sizearr_order as $size_id)
						{
							?>
							<td align="right"><p>
							<?
								echo number_format($color_size_data[$color_id][$country_id][$size_id],0) ;
								 $color_total_in+= $color_size_data[$color_id][$country_id][$size_id]; 
								 $color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
								 $line_color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
							 ?>
							</p></td>
							<?
						}
						$line_color_total_in+=$color_total_in; 
						?>
						<td align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
					</tr>
					<?
					$i++;
				}
				
			}
			?>
            <tr bgcolor="#CCCCCC">
                <td >&nbsp;</td>
                <td>&nbsp;</td>
                <td align="right" style="font-weight:bold">Color Total:</td>
                <?
                foreach($sizearr_order as $size_id)
                {
                    ?>
                    <td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
                    <?
                }
                ?>
                <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
            </tr>
            </tbody>
            <tfoot>
                <th >&nbsp;</th>
                <th>&nbsp;</th>
                <th >Day Total:</th>
                <?
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th ><? echo $color_size_in[$size_id]; ?></th>
                    <?
				}
				?>
                <th ><? echo $grand_tot_in; ?></th>
                
            </tfoot>
        </table>
        </div>
    </fieldset>
<?	
}

if($action=='sp_issue_popup_location')
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  ); 
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  ); 
	
	
	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");
	
	$location=str_replace("'","",$location);
	$floor_id=str_replace("'","",$floor_id);
	$sewing_line=str_replace("'","",$sewing_line);
	$location_cond=$floor_cond=$sewing_cond="";
	if($location!="") $location_cond="and a.location=$location";
	if($floor_id!="") $floor_cond=" and a.floor_id=$floor_id";
	if($sewing_line!="") $sewing_cond=" and a.sewing_line=$sewing_line";
	
	$sql_color_size= sql_select("SELECT a.country_id, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id 
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c 
	where a.production_type=2 and a.embel_name=4 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' $location_cond $floor_cond $sewing_cond and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
	group by   c.color_number_id,a.country_id,c.size_number_id
	order by c.color_number_id,a.country_id");
	foreach($sql_color_size as $row)
	{
		$details_data[$row[csf('color_number_id')]][$row[csf('country_id')]]=$row[csf('country_id')];
		$color_size_data[$row[csf('color_number_id')]][$row[csf('country_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}
	
	$col_width=60*count($sizearr_order);
	$table_width=330+$col_width;


?>	
<script>

	function window_close()
	{
		parent.emailwindow.hide();
	}
	
</script>	
<fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
    <div style="100%" id="report_container">
    
        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
        	<tr>
            	<td align="center" style="text-decoration:underline; font-size:20px; font-weight:bold">Date wise special works production</td>
            </tr>
        	<tr>
            	<td style="font-size:16px; font-weight:bold;">
                Date : <? echo change_date_format($production_date); ?> 
                <br />
                Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;
                </td>
            </tr>
        </table>
        
        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                	<th width="40" rowspan="2">SI</th>
                    <th width="100" rowspan="2">Color</th>
                    <th width="100" rowspan="2">Country Name</th>
                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
                    <th width="80" rowspan="2" >Total</th>
                </tr>
                <tr>
                <?
				$grand_tot_in=0;
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
                    <?
				}
				?>
                </tr>
                
            </thead>
            <tbody>
            <?
			$i=1;
			$k=1;
			//var_dump($result);die;
			foreach($details_data as $color_id=>$value)
			{
				foreach($value as $country_id=>$row)
				{
					if(!in_array($color_id,$temp_arr))
					{
						$temp_arr[]=$color_id;
						if($k!=1)
						{
							?>
							<tr bgcolor="#CCCCCC">
								<td >&nbsp;</td>
								<td>&nbsp;</td>
								<td align="right" style="font-weight:bold">Color Total:</td>
								<?
								foreach($sizearr_order as $size_id)
								{
									?>
									<td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
									<?
								}
								?>
								<td align="right"><? echo number_format($line_color_total_in,0); ?></td>
							</tr>
							<?
							$line_color_size_in = $line_color_total_in ="";
						}
						$k++;
					}
					
					?>
					<tr>
						<td align="center"><? echo $i;  ?></td>
                        <?
						if(!in_array($color_id,$temp_arr_color))
						{
							$temp_arr_color[]=$color_id;
							?>
							<td rowspan="<? echo count($color_size_data[$color_id]); ?>" align="center" valign="middle"><p><? echo $colorarr[$color_id];  ?></p></td>
							<?
						}
						?>
						<td ><p><? echo $country_library[$country_id];  ?></p></td>
						<?
						$color_total_in=0;
						foreach($sizearr_order as $size_id)
						{
							?>
							<td align="right"><p>
							<?
								echo number_format($color_size_data[$color_id][$country_id][$size_id],0) ;
								 $color_total_in+= $color_size_data[$color_id][$country_id][$size_id]; 
								 $color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
								 $line_color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
							 ?>
							</p></td>
							<?
						}
						$line_color_total_in+=$color_total_in; 
						?>
						<td align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
					</tr>
					<?
					$i++;
				}
				
			}
			?>
            <tr bgcolor="#CCCCCC">
                <td >&nbsp;</td>
                <td>&nbsp;</td>
                <td align="right" style="font-weight:bold">Color Total:</td>
                <?
                foreach($sizearr_order as $size_id)
                {
                    ?>
                    <td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
                    <?
                }
                ?>
                <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
            </tr>
            </tbody>
            <tfoot>
                <th >&nbsp;</th>
                <th>&nbsp;</th>
                <th >Day Total:</th>
                <?
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th ><? echo $color_size_in[$size_id]; ?></th>
                    <?
				}
				?>
                <th ><? echo $grand_tot_in; ?></th>
                
            </tfoot>
        </table>
        </div>
    </fieldset>
<?	
}

if($action=='sp_receive_popup')
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  ); 
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  ); 
	
	
	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");
	
	$location=str_replace("'","",$location);
	$floor_id=str_replace("'","",$floor_id);
	$sewing_line=str_replace("'","",$sewing_line);
	$location_cond=$floor_cond=$sewing_cond="";
	if($location!="") $location_cond="and a.location=$location";
	if($floor_id!="") $floor_cond=" and a.floor_id=$floor_id";
	if($sewing_line!="") $sewing_cond=" and a.sewing_line=$sewing_line";
	
	
	$sql_color_size= sql_select("SELECT a.country_id, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id 
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c 
	where a.production_type=3 and a.embel_name=4 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' $location_cond $floor_cond $sewing_cond and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
	group by   c.color_number_id,a.country_id,c.size_number_id
	order by c.color_number_id,a.country_id");
	foreach($sql_color_size as $row)
	{
		$details_data[$row[csf('color_number_id')]][$row[csf('country_id')]]=$row[csf('country_id')];
		$color_size_data[$row[csf('color_number_id')]][$row[csf('country_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}
	
	$col_width=60*count($sizearr_order);
	$table_width=330+$col_width;


?>	
<script>

	function window_close()
	{
		parent.emailwindow.hide();
	}
	
</script>	
<fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
    <div style="100%" id="report_container">
    
        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
        	<tr>
            	<td align="center" style="text-decoration:underline; font-size:20px; font-weight:bold">Date wise special works production</td>
            </tr>
        	<tr>
            	<td style="font-size:16px; font-weight:bold;">
                Date : <? echo change_date_format($production_date); ?> 
                <br />
                Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;
                </td>
            </tr>
        </table>
        
        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                	<th width="40" rowspan="2">SI</th>
                    <th width="100" rowspan="2">Color</th>
                    <th width="100" rowspan="2">Country Name</th>
                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
                    <th width="80" rowspan="2" >Total</th>
                </tr>
                <tr>
                <?
				$grand_tot_in=0;
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
                    <?
				}
				?>
                </tr>
                
            </thead>
            <tbody>
            <?
			$i=1;
			$k=1;
			//var_dump($result);die;
			foreach($details_data as $color_id=>$value)
			{
				foreach($value as $country_id=>$row)
				{
					if(!in_array($color_id,$temp_arr))
					{
						$temp_arr[]=$color_id;
						if($k!=1)
						{
							?>
							<tr bgcolor="#CCCCCC">
								<td >&nbsp;</td>
								<td>&nbsp;</td>
								<td align="right" style="font-weight:bold">Color Total:</td>
								<?
								foreach($sizearr_order as $size_id)
								{
									?>
									<td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
									<?
								}
								?>
								<td align="right"><? echo number_format($line_color_total_in,0); ?></td>
							</tr>
							<?
							$line_color_size_in = $line_color_total_in ="";
						}
						$k++;
					}
					
					?>
					<tr>
						<td align="center"><? echo $i;  ?></td>
                        <?
						if(!in_array($color_id,$temp_arr_color))
						{
							$temp_arr_color[]=$color_id;
							?>
							<td rowspan="<? echo count($color_size_data[$color_id]); ?>" align="center" valign="middle"><p><? echo $colorarr[$color_id];  ?></p></td>
							<?
						}
						?>
						<td ><p><? echo $country_library[$country_id];  ?></p></td>
						<?
						$color_total_in=0;
						foreach($sizearr_order as $size_id)
						{
							?>
							<td align="right"><p>
							<?
								echo number_format($color_size_data[$color_id][$country_id][$size_id],0) ;
								 $color_total_in+= $color_size_data[$color_id][$country_id][$size_id]; 
								 $color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
								 $line_color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
							 ?>
							</p></td>
							<?
						}
						$line_color_total_in+=$color_total_in; 
						?>
						<td align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
					</tr>
					<?
					$i++;
				}
				
			}
			?>
            <tr bgcolor="#CCCCCC">
                <td >&nbsp;</td>
                <td>&nbsp;</td>
                <td align="right" style="font-weight:bold">Color Total:</td>
                <?
                foreach($sizearr_order as $size_id)
                {
                    ?>
                    <td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
                    <?
                }
                ?>
                <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
            </tr>
            </tbody>
            <tfoot>
                <th >&nbsp;</th>
                <th>&nbsp;</th>
                <th >Day Total:</th>
                <?
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th ><? echo $color_size_in[$size_id]; ?></th>
                    <?
				}
				?>
                <th ><? echo $grand_tot_in; ?></th>
                
            </tfoot>
        </table>
        </div>
    </fieldset>
<?	
}

if($action=='sp_receive_popup_location')
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  ); 
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  ); 
	
	
	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");
	
	$sql_color_size= sql_select("SELECT a.country_id, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id 
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c 
	where a.production_type=3 and a.embel_name=4 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
	group by   c.color_number_id,a.country_id,c.size_number_id
	order by c.color_number_id,a.country_id");
	foreach($sql_color_size as $row)
	{
		$details_data[$row[csf('color_number_id')]][$row[csf('country_id')]]=$row[csf('country_id')];
		$color_size_data[$row[csf('color_number_id')]][$row[csf('country_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}
	
	$col_width=60*count($sizearr_order);
	$table_width=330+$col_width;


?>	
<script>

	function window_close()
	{
		parent.emailwindow.hide();
	}
	
</script>	
<fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
    <div style="100%" id="report_container">
    
        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
        	<tr>
            	<td align="center" style="text-decoration:underline; font-size:20px; font-weight:bold">Date wise special works production</td>
            </tr>
        	<tr>
            	<td style="font-size:16px; font-weight:bold;">
                Date : <? echo change_date_format($production_date); ?> 
                <br />
                Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;
                </td>
            </tr>
        </table>
        
        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                	<th width="40" rowspan="2">SI</th>
                    <th width="100" rowspan="2">Color</th>
                    <th width="100" rowspan="2">Country Name</th>
                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
                    <th width="80" rowspan="2" >Total</th>
                </tr>
                <tr>
                <?
				$grand_tot_in=0;
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
                    <?
				}
				?>
                </tr>
                
            </thead>
            <tbody>
            <?
			$i=1;
			$k=1;
			//var_dump($result);die;
			foreach($details_data as $color_id=>$value)
			{
				foreach($value as $country_id=>$row)
				{
					if(!in_array($color_id,$temp_arr))
					{
						$temp_arr[]=$color_id;
						if($k!=1)
						{
							?>
							<tr bgcolor="#CCCCCC">
								<td >&nbsp;</td>
								<td>&nbsp;</td>
								<td align="right" style="font-weight:bold">Color Total:</td>
								<?
								foreach($sizearr_order as $size_id)
								{
									?>
									<td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
									<?
								}
								?>
								<td align="right"><? echo number_format($line_color_total_in,0); ?></td>
							</tr>
							<?
							$line_color_size_in = $line_color_total_in ="";
						}
						$k++;
					}
					
					?>
					<tr>
						<td align="center"><? echo $i;  ?></td>
                        <?
						if(!in_array($color_id,$temp_arr_color))
						{
							$temp_arr_color[]=$color_id;
							?>
							<td rowspan="<? echo count($color_size_data[$color_id]); ?>" align="center" valign="middle"><p><? echo $colorarr[$color_id];  ?></p></td>
							<?
						}
						?>
						<td ><p><? echo $country_library[$country_id];  ?></p></td>
						<?
						$color_total_in=0;
						foreach($sizearr_order as $size_id)
						{
							?>
							<td align="right"><p>
							<?
								echo number_format($color_size_data[$color_id][$country_id][$size_id],0) ;
								 $color_total_in+= $color_size_data[$color_id][$country_id][$size_id]; 
								 $color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
								 $line_color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
							 ?>
							</p></td>
							<?
						}
						$line_color_total_in+=$color_total_in; 
						?>
						<td align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
					</tr>
					<?
					$i++;
				}
				
			}
			?>
            <tr bgcolor="#CCCCCC">
                <td >&nbsp;</td>
                <td>&nbsp;</td>
                <td align="right" style="font-weight:bold">Color Total:</td>
                <?
                foreach($sizearr_order as $size_id)
                {
                    ?>
                    <td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
                    <?
                }
                ?>
                <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
            </tr>
            </tbody>
            <tfoot>
                <th >&nbsp;</th>
                <th>&nbsp;</th>
                <th >Day Total:</th>
                <?
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th ><? echo $color_size_in[$size_id]; ?></th>
                    <?
				}
				?>
                <th ><? echo $grand_tot_in; ?></th>
                
            </tfoot>
        </table>
        </div>
    </fieldset>
<?	
}
if($action=="sewingQnty_popup")
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  ); 
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  ); 
	$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	$floor_library=return_library_array( "select id,floor_name from  lib_prod_floor", "id", "floor_name"  );
	$sewing_line_library=return_library_array( "select id,line_name from  lib_sewing_line", "id", "line_name"  );
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	
	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");
	$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	//var_dump();die;
	
	$po_details_sql=sql_select("select a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.id=$po_break_down_id group by a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id");
	//echo $po_details_sql;die;
	
	//For Show Date Location and Floor 
	if($location_id!=0) $location_cond=" and a.location in($location_id)"; else  $location_cond=""; 
	if($floor_id!=0) $floor_cond=" and a.floor_id in($floor_id)"; else  $floor_cond="";
	if($sewing_line!=0) $sewing_line_cond=" and a.sewing_line in($sewing_line)"; else  $sewing_line_cond="";
	
	if($db_type==2)
	{
		 $sql= "SELECT  a.challan_no, a.floor_id, a.sewing_line, a.country_id, a.production_source, a.serving_company, c.color_number_id, LISTAGG(CAST(c.size_number_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.size_number_id) as size_number_id , a.prod_reso_allo  
		from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c 
		where a.production_type=$page and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.production_source='$prod_source' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $location_cond $floor_cond $sewing_line_cond
		group by  a.country_id,a.challan_no, a.floor_id, a.sewing_line, a.production_source, a.serving_company, a.prod_reso_allo,c.color_number_id
		order by a.country_id,a.challan_no, a.floor_id, a.sewing_line";
	}
	else if($db_type==0)
	{
		 $sql= "SELECT a.challan_no,a.floor_id, a.sewing_line,a.country_id,a.production_source, a.serving_company, c.color_number_id, group_concat(c.size_number_id) as size_number_id , a.prod_reso_allo 
		from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c 
		where a.production_type=$page and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.production_source='$prod_source' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $location_cond $floor_cond $sewing_line_cond 
		group by  a.country_id,a.challan_no, a.floor_id, a.sewing_line, a.production_source, a.serving_company, a.prod_reso_allo, c.color_number_id
		order by a.country_id,a.challan_no, a.floor_id, a.sewing_line";
	}
	//echo $sql;die;
	
	
	//echo $sql; //and a.production_date='$production_date'
	$result=sql_select($sql);
	
	$sql_color_size= sql_select("SELECT a.challan_no,a.floor_id, a.sewing_line,a.country_id,a.production_source, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id 
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c 
	where a.production_type=$page and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.production_source='$prod_source' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0   $location_cond $floor_cond $sewing_line_cond
	group by   a.country_id,a.challan_no, a.floor_id, a.sewing_line, a.production_source,c.color_number_id,c.size_number_id
	order by a.country_id");
	foreach($sql_color_size as $row)
	{
		$production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('sewing_line')]][$row[csf('production_source')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
		$summery_data[$row[csf('color_number_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}
	
	$col_width=60*count($sizearr_order);
	if($prod_source==3) $table_width=750+$col_width; else $table_width=630+$col_width;
	$summer_table_width=230+$col_width;


?>	
<script>

	function print_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		d.close();
	}
	
	function window_close()
	{
		parent.emailwindow.hide();
	}
	
</script>	
<fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
		<input type="button" value="Print" onClick="print_window()" style="width:100px; margin-left:400px;"  class="formbutton" /><br />
    <div style="100%" id="report_container">
    
        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
        	<tr>
            	<td style="font-size:14px; font-weight:bold;">
                Buyer Name : <? echo $buyer_library[$po_details_sql[0][csf("buyer_name")]]; ?>&nbsp;&nbsp;&nbsp;&nbsp; Job No : <? echo $po_details_sql[0][csf("job_no")]; ?>&nbsp;&nbsp;&nbsp;&nbsp; Style No : <? echo $po_details_sql[0][csf("style_ref_no")]; ?>&nbsp;&nbsp;&nbsp;&nbsp; Garments Item : 
				<?
                    $item_data=""; 
                    $garments_item_arr=array_unique(explode(",",$po_details_sql[0][csf("gmts_item_id")])); 
                    foreach($garments_item_arr as $item_id)
                    {
                        if($item_data!="") $item_data .=", ";
                        $item_data .=$garments_item[$item_id];
                    }
                    echo $item_data;
                ?>
                <br />
                Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;Date : <? echo change_date_format($production_date); ?> 
                <br />
                Summary
                </td>
            </tr>
        </table>
        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $summer_table_width; ?>" style="margin-bottom:20px;">
            <thead>
                <tr>
                	<th width="40" rowspan="2">SI</th>
                    <th width="100" rowspan="2">Color</th>
                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
                    <th width="80" rowspan="2" >Total</th>
                </tr>
                <tr>
                <?
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
                    <?
				}
				?>
                </tr>
                
            </thead>
            <tbody>
            <?
			$i=1;
			//var_dump($result);die;
			foreach($summery_data as $color_id=>$row)
			{
				//print_r($row);die;
				
				?>
                <tr>
                    <td align="center"><? echo $i;  ?></td>
                    <td ><? echo $colorarr[$color_id];  ?></td>
                    <?
					$summry_color_total_in =0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format($row[$size_id],0) ; $summry_color_total_in+= $row[$size_id]; $summry_color_size_in [$size_id]+=$row[$size_id];?></td>
                        <?
                    }
                    ?>
                    <td align="right"><? echo  number_format( $summry_color_total_in,0); $grand_tot_in+=$summry_color_total_in; ?></td>
                </tr>
                <?
				$i++;
			}
			?>
            </tbody>
            <tfoot>
                <th >&nbsp;</th>
                <th >&nbsp;</th>
                <?
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th ><? echo $summry_color_size_in[$size_id]; ?></th>
                    <?
				}
				?>
                <th ><? echo $grand_tot_in; ?></th>
                
            </tfoot>
        </table>
        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px;">
        	<tr>
            	<td style="font-size:14px; font-weight:bold;">Details</td>
            </tr>
        </table>
        
        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                	<th width="40" rowspan="2">SI</th>
                    <th width="100" rowspan="2">Country Name</th>
                    <th width="80" rowspan="2">Source</th>
                    <?
					if($prod_source==3)
					{
						?>
                    	<th width="120" rowspan="2">Serving Company</th>
                        <?
					}
					?>
                    <th width="70" rowspan="2">Challan</th>
                    <th width="90" rowspan="2">Sewing Unit</th>
                    <th width="70" rowspan="2" >Sewing Line</th>
                    <th width="100" rowspan="2">Color</th>
                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
                    <th width="80" rowspan="2" >Total</th>
                </tr>
                <tr>
                <?
				$grand_tot_in=0;
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
                    <?
				}
				?>
                </tr>
                
            </thead>
            <tbody>
            <?
			$i=1;
			$k=1;
			//var_dump($result);die;
			foreach($result as $row)
			{
				if(!in_array($row[csf("sewing_line")],$temp_arr[$row[csf("country_id")]]))
				{
					$temp_arr[$row[csf("country_id")]][]=$row[csf("sewing_line")];
					if($k!=1)
					{
						?>
						<tr bgcolor="#CCCCCC">
							<td >&nbsp;</td>
							<td>&nbsp;</td>
							<td >&nbsp;</td>
                            <?
							if($prod_source==3)
							{
								?>
                            	<td >&nbsp;</td>
                                <?
							}
							?>
							<td >&nbsp;</td>
							<td >&nbsp;</td>
							<td >&nbsp;</td>
							<td >&nbsp;</td>
							<?
							foreach($sizearr_order as $size_id)
							{
								?>
								<td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
								<?
							}
							?>
							<td align="right"><? echo number_format($line_color_total_in,0); ?></td>
						</tr>
						<?
						$line_color_size_in = $line_color_total_in ="";
					}
					$k++;
				}
				
				$sewing_line='';
				if($row[csf('prod_reso_allo')]==1)
				{
					$line_number=explode(",",$prod_reso_arr[$row[csf('sewing_line')]]);
					foreach($line_number as $val)
					{
						if($sewing_line=='') $sewing_line=$sewing_line_library[$val]; else $sewing_line.=",".$sewing_line_library[$val];
					}
				}
				else $sewing_line=$sewing_line_library[$row[csf('sewing_line')]];
				
				?>
                <tr>
                    <td align="center"><? echo $i;  ?></td>
                    <td ><p><? echo $country_library[$row[csf("country_id")]];  ?></p></td>
                    <td ><p><? echo $knitting_source[$row[csf("production_source")]]; ?><p></td>
                    <?
					if($prod_source==3)
					{
						?>
                    	<td ><p><? echo $supplier_arr[$row[csf("serving_company")]];  ?></p></td>
                        <?
					}
					?>
                    <td ><p><? echo $row[csf("challan_no")];  ?></p></td>
                    <td ><p><? echo $floor_library[$row[csf("floor_id")]];  ?></p></td>
                    <td align="center"><p><? echo $sewing_line;;//$sewing_line_library[$row[csf("sewing_line")]];  ?></p></td>
                    <td ><p><? echo $colorarr[$row[csf("color_number_id")]];  ?></p></td>
                    <?
					$color_total_in=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><p>
						<?
						 	echo number_format($production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('sewing_line')]][$row[csf('production_source')]][$row[csf('color_number_id')]][$size_id],0) ;
							 $color_total_in+= $production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('sewing_line')]][$row[csf('production_source')]][$row[csf('color_number_id')]][$size_id]; 
							 $color_size_in [$size_id]+=$production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('sewing_line')]][$row[csf('production_source')]][$row[csf('color_number_id')]][$size_id];
							 $line_color_total_in+= $production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('sewing_line')]][$row[csf('production_source')]][$row[csf('color_number_id')]][$size_id]; 
							 $line_color_size_in [$size_id]+=$production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('sewing_line')]][$row[csf('production_source')]][$row[csf('color_number_id')]][$size_id];
						 ?>
                        </p></td>
                        <?
                    }
                    ?>
                    <td align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
                </tr>
                <?
				$i++;
			}
			?>
            <tr bgcolor="#CCCCCC">
                <td >&nbsp;</td>
                <td>&nbsp;</td>
                <td >&nbsp;</td>
                <?
				if($prod_source==3)
				{
					?>
					<td >&nbsp;</td>
					<?
				}
				?>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <?
                foreach($sizearr_order as $size_id)
                {
                    ?>
                    <td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
                    <?
                }
                ?>
                <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
            </tr>
            </tbody>
            <tfoot>
                <th >&nbsp;</th>
                <th>&nbsp;</th>
                <th >&nbsp;</th>
                <?
				if($prod_source==3)
				{
					?>
					<th >&nbsp;</th>
					<?
				}
				?>
                <th >&nbsp;</th>
                <th >&nbsp;</th>
                <th >&nbsp;</th>
                <th >&nbsp;</th>
                <?
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th ><? echo $color_size_in[$size_id]; ?></th>
                    <?
				}
				?>
                <th ><? echo $grand_tot_in; ?></th>
                
            </tfoot>
        </table>
        </div>
    </fieldset>
<?	
}

if($action=="sewingQnty_location_popup")
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  ); 
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  ); 
	$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	$floor_library=return_library_array( "select id,floor_name from  lib_prod_floor", "id", "floor_name"  );
	$sewing_line_library=return_library_array( "select id,line_name from  lib_sewing_line", "id", "line_name"  );
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	
	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");
	$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	//var_dump();die;
	
	$po_details_sql=sql_select("select a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.id=$po_break_down_id group by a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id");
	if($db_type==2)
	{
		$sql= "SELECT  a.challan_no, a.floor_id, a.sewing_line, a.country_id, a.production_source, c.color_number_id, LISTAGG(CAST(c.size_number_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.size_number_id) as size_number_id , a.prod_reso_allo  
		from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c 
		where a.production_type=5 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.location=$location and a.floor_id=$floor_id and a.sewing_line=$sewing_line and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
		group by  a.country_id,a.challan_no, a.floor_id, a.sewing_line, a.production_source, a.prod_reso_allo,c.color_number_id
		order by a.country_id,a.challan_no, a.floor_id, a.sewing_line";
	}
	else if($db_type==0)
	{
		$sql= "SELECT a.challan_no,a.floor_id, a.sewing_line,a.country_id,a.production_source, c.color_number_id, group_concat(c.size_number_id) as size_number_id , a.prod_reso_allo 
		from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c 
		where a.production_type=5 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.location=$location and a.floor_id=$floor_id and a.sewing_line=$sewing_line and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
		group by  a.country_id,a.challan_no, a.floor_id, a.sewing_line, a.production_source, a.prod_reso_allo, c.color_number_id
		order by a.country_id,a.challan_no, a.floor_id, a.sewing_line";
	}
	//echo $sql;die;
	
	
	//echo $sql; //and a.production_date='$production_date'
	$result=sql_select($sql);
	$sql_color_size= sql_select("SELECT a.challan_no,a.floor_id, a.sewing_line,a.country_id,a.production_source, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id 
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c 
	where a.production_type=5 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.location=$location and a.floor_id=$floor_id and a.sewing_line=$sewing_line and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
	group by   a.country_id,a.challan_no, a.floor_id, a.sewing_line, a.production_source,c.color_number_id,c.size_number_id
	order by a.country_id");
	foreach($sql_color_size as $row)
	{
		$production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('sewing_line')]][$row[csf('production_source')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
		$summery_data[$row[csf('color_number_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}
	
	$col_width=60*count($sizearr_order);
	$table_width=630+$col_width;
	$summer_table_width=230+$col_width;


?>	
<script>

	function print_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		d.close();
	}
	
	function window_close()
	{
		parent.emailwindow.hide();
	}
	
</script>	
<fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
		<input type="button" value="Print" onClick="print_window()" style="width:100px; margin-left:400px;"  class="formbutton" /><br />
    <div style="100%" id="report_container">
    
        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
        	<tr>
            	<td style="font-size:14px; font-weight:bold;">
                Buyer Name : <? echo $buyer_library[$po_details_sql[0][csf("buyer_name")]]; ?>&nbsp;&nbsp;&nbsp;&nbsp; Job No : <? echo $po_details_sql[0][csf("job_no")]; ?>&nbsp;&nbsp;&nbsp;&nbsp; Style No : <? echo $po_details_sql[0][csf("style_ref_no")]; ?>&nbsp;&nbsp;&nbsp;&nbsp; Garments Item : 
				<?
                    $item_data=""; 
                    $garments_item_arr=array_unique(explode(",",$po_details_sql[0][csf("gmts_item_id")])); 
                    foreach($garments_item_arr as $item_id)
                    {
                        if($item_data!="") $item_data .=", ";
                        $item_data .=$garments_item[$item_id];
                    }
                    echo $item_data;
                ?>
                <br />
                Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;Date : <? echo change_date_format($production_date); ?> 
                <br />
                Summary
                </td>
            </tr>
        </table>
        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $summer_table_width; ?>" style="margin-bottom:20px;">
            <thead>
                <tr>
                	<th width="40" rowspan="2">SI</th>
                    <th width="100" rowspan="2">Color</th>
                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
                    <th width="80" rowspan="2" >Total</th>
                </tr>
                <tr>
                <?
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
                    <?
				}
				?>
                </tr>
                
            </thead>
            <tbody>
            <?
			$i=1;
			//var_dump($result);die;
			foreach($summery_data as $color_id=>$row)
			{
				//print_r($row);die;
				
				?>
                <tr>
                    <td align="center"><? echo $i;  ?></td>
                    <td ><? echo $colorarr[$color_id];  ?></td>
                    <?
					$summry_color_total_in =0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format($row[$size_id],0) ; $summry_color_total_in+= $row[$size_id]; $summry_color_size_in [$size_id]+=$row[$size_id];?></td>
                        <?
                    }
                    ?>
                    <td align="right"><? echo  number_format( $summry_color_total_in,0); $grand_tot_in+=$summry_color_total_in; ?></td>
                </tr>
                <?
				$i++;
			}
			?>
            </tbody>
            <tfoot>
                <th >&nbsp;</th>
                <th >&nbsp;</th>
                <?
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th ><? echo $summry_color_size_in[$size_id]; ?></th>
                    <?
				}
				?>
                <th ><? echo $grand_tot_in; ?></th>
                
            </tfoot>
        </table>
        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px;">
        	<tr>
            	<td style="font-size:14px; font-weight:bold;">Details</td>
            </tr>
        </table>
        
        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                	<th width="40" rowspan="2">SI</th>
                    <th width="100" rowspan="2">Country Name</th>
                    <th width="80" rowspan="2">Source</th>
                    <th width="70" rowspan="2">Challan</th>
                    <th width="90" rowspan="2">Sewing Unit</th>
                    <th width="70" rowspan="2" >Sewing Line</th>
                    <th width="100" rowspan="2">Color</th>
                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
                    <th width="80" rowspan="2" >Total</th>
                </tr>
                <tr>
                <?
				$grand_tot_in=0;
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
                    <?
				}
				?>
                </tr>
                
            </thead>
            <tbody>
            <?
			$i=1;
			$k=1;
			//var_dump($result);die;
			foreach($result as $row)
			{
				if(!in_array($row[csf("sewing_line")],$temp_arr[$row[csf("country_id")]]))
				{
					$temp_arr[$row[csf("country_id")]][]=$row[csf("sewing_line")];
					if($k!=1)
					{
						?>
						<tr bgcolor="#CCCCCC">
							<td >&nbsp;</td>
							<td>&nbsp;</td>
							<td >&nbsp;</td>
							<td >&nbsp;</td>
							<td >&nbsp;</td>
							<td >&nbsp;</td>
							<td >&nbsp;</td>
							<?
							foreach($sizearr_order as $size_id)
							{
								?>
								<td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
								<?
							}
							?>
							<td align="right"><? echo number_format($line_color_total_in,0); ?></td>
						</tr>
						<?
						$line_color_size_in = $line_color_total_in ="";
					}
					$k++;
				}
				
				$sewing_line='';
				if($row[csf('prod_reso_allo')]==1)
				{
					$line_number=explode(",",$prod_reso_arr[$row[csf('sewing_line')]]);
					foreach($line_number as $val)
					{
						if($sewing_line=='') $sewing_line=$sewing_line_library[$val]; else $sewing_line.=",".$sewing_line_library[$val];
					}
				}
				else $sewing_line=$sewing_line_library[$row[csf('sewing_line')]];
				
				?>
                <tr>
                    <td align="center"><? echo $i;  ?></td>
                    <td ><p><? echo $country_library[$row[csf("country_id")]];  ?></p></td>
                    <td ><p><? echo $knitting_source[$row[csf("production_source")]]; ?><p></td>
                    <td ><p><? echo $row[csf("challan_no")];  ?></p></td>
                    <td ><p><? echo $floor_library[$row[csf("floor_id")]];  ?></p></td>
                    <td align="center"><p><? echo $sewing_line;;//$sewing_line_library[$row[csf("sewing_line")]];  ?></p></td>
                    <td ><p><? echo $colorarr[$row[csf("color_number_id")]];  ?></p></td>
                    <?
					$color_total_in=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><p>
						<?
						 	echo number_format($production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('sewing_line')]][$row[csf('production_source')]][$row[csf('color_number_id')]][$size_id],0) ;
							 $color_total_in+= $production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('sewing_line')]][$row[csf('production_source')]][$row[csf('color_number_id')]][$size_id]; 
							 $color_size_in [$size_id]+=$production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('sewing_line')]][$row[csf('production_source')]][$row[csf('color_number_id')]][$size_id];
							 $line_color_total_in+= $production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('sewing_line')]][$row[csf('production_source')]][$row[csf('color_number_id')]][$size_id]; 
							 $line_color_size_in [$size_id]+=$production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('sewing_line')]][$row[csf('production_source')]][$row[csf('color_number_id')]][$size_id];
						 ?>
                        </p></td>
                        <?
                    }
                    ?>
                    <td align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
                </tr>
                <?
				$i++;
			}
			?>
            <tr bgcolor="#CCCCCC">
                <td >&nbsp;</td>
                <td>&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <?
                foreach($sizearr_order as $size_id)
                {
                    ?>
                    <td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
                    <?
                }
                ?>
                <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
            </tr>
            </tbody>
            <tfoot>
                <th >&nbsp;</th>
                <th>&nbsp;</th>
                <th >&nbsp;</th>
                <th >&nbsp;</th>
                <th >&nbsp;</th>
                <th >&nbsp;</th>
                <th >&nbsp;</th>
                <?
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th ><? echo $color_size_in[$size_id]; ?></th>
                    <?
				}
				?>
                <th ><? echo $grand_tot_in; ?></th>
                
            </tfoot>
        </table>
        </div>
    </fieldset>
<?	
}

if($action=="sewingQnty_input_popup")
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  ); 
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  ); 
	$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	$floor_library=return_library_array( "select id,floor_name from  lib_prod_floor", "id", "floor_name"  );
	$sewing_line_library=return_library_array( "select id,line_name from  lib_sewing_line", "id", "line_name"  );
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	
	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");
	$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	//var_dump();die;
	
	$po_details_sql=sql_select("select a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.id=$po_break_down_id group by a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id");
	//echo $po_details_sql;die;
	
	if($db_type==2)
	{
		$sql= "SELECT  a.challan_no, a.floor_id, a.sewing_line, a.country_id, a.production_source, c.color_number_id, LISTAGG(CAST(c.size_number_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.size_number_id) as size_number_id , a.prod_reso_allo 
		from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c 
		where a.production_type=4 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
		group by  a.country_id,a.challan_no, a.floor_id, a.sewing_line, a.production_source, a.prod_reso_allo,c.color_number_id
		order by a.country_id,a.challan_no, a.floor_id, a.sewing_line";
	}
	else if($db_type==0)
	{
		$sql= "SELECT a.challan_no,a.floor_id, a.sewing_line,a.country_id,a.production_source, c.color_number_id, group_concat(c.size_number_id) as size_number_id , a.prod_reso_allo 
		from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c 
		where a.production_type=4 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
		group by  a.country_id,a.challan_no, a.floor_id, a.sewing_line, a.production_source, a.prod_reso_allo,c.color_number_id
		order by a.country_id,a.challan_no, a.floor_id, a.sewing_line";
	}
	//echo $sql;die;
	
	
	//echo $sql; //and a.production_date='$production_date'
	$result=sql_select($sql);
	
	$sql_color_size= sql_select("SELECT a.challan_no,a.floor_id, a.sewing_line,a.country_id,a.production_source, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id 
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c 
	where a.production_type=4 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
	group by   a.country_id,a.challan_no, a.floor_id, a.sewing_line, a.production_source,c.color_number_id,c.size_number_id
	order by a.country_id");
	foreach($sql_color_size as $row)
	{
		$production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('sewing_line')]][$row[csf('production_source')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
		$summery_data[$row[csf('color_number_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}
	
	$col_width=60*count($sizearr_order);
	$table_width=630+$col_width;
	$summer_table_width=230+$col_width;


?>	
<script>

	function print_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		d.close();
	}
	
	function window_close()
	{
		parent.emailwindow.hide();
	}
	
</script>	
<fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
		<input type="button" value="Print" onClick="print_window()" style="width:100px; margin-left:400px;"  class="formbutton" /><br />
    <div style="100%" id="report_container">
    
        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px;  margin-top:10px;">
        	<tr>
            	<td style="font-size:14px; font-weight:bold;">
                Buyer Name : <? echo $buyer_library[$po_details_sql[0][csf("buyer_name")]]; ?>&nbsp;&nbsp;&nbsp;&nbsp; Job No : <? echo $po_details_sql[0][csf("job_no")]; ?>&nbsp;&nbsp;&nbsp;&nbsp; Style No : <? echo $po_details_sql[0][csf("style_ref_no")]; ?>&nbsp;&nbsp;&nbsp;&nbsp; Garments Item : 
				<?
                    $item_data=""; 
                    $garments_item_arr=array_unique(explode(",",$po_details_sql[0][csf("gmts_item_id")])); 
                    foreach($garments_item_arr as $item_id)
                    {
                        if($item_data!="") $item_data .=", ";
                        $item_data .=$garments_item[$item_id];
                    }
                    echo $item_data;
                ?>
                <br />
                Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;Date : <? echo change_date_format($production_date); ?> 
                <br />
                Summary
                </td>
            </tr>
        </table>
        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $summer_table_width; ?>" style="margin-bottom:20px;">
            <thead>
                <tr>
                	<th width="40" rowspan="2">SI</th>
                    <th width="100" rowspan="2">Color</th>
                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
                    <th width="80" rowspan="2" >Total</th>
                </tr>
                <tr>
                <?
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
                    <?
				}
				?>
                </tr>
                
            </thead>
            <tbody>
            <?
			$i=1;
			//var_dump($result);die;
			foreach($summery_data as $color_id=>$row)
			{
				//print_r($row);die;
				
				?>
                <tr>
                    <td align="center"><? echo $i;  ?></td>
                    <td ><? echo $colorarr[$color_id];  ?></td>
                    <?
					$summry_color_total_in =0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format($row[$size_id],0) ; $summry_color_total_in+= $row[$size_id]; $summry_color_size_in [$size_id]+=$row[$size_id];?></td>
                        <?
                    }
                    ?>
                    <td align="right"><? echo  number_format( $summry_color_total_in,0); $grand_tot_in+=$summry_color_total_in; ?></td>
                </tr>
                <?
				$i++;
			}
			?>
            </tbody>
            <tfoot>
                <th >&nbsp;</th>
                <th >&nbsp;</th>
                <?
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th ><? echo $summry_color_size_in[$size_id]; ?></th>
                    <?
				}
				?>
                <th ><? echo $grand_tot_in; ?></th>
                
            </tfoot>
        </table>
        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px;">
        	<tr>
            	<td style="font-size:14px; font-weight:bold;">Details</td>
            </tr>
        </table>
        
        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                	<th width="40" rowspan="2">SI</th>
                    <th width="100" rowspan="2">Country Name</th>
                    <th width="80" rowspan="2">Source</th>
                    <th width="70" rowspan="2">Challan</th>
                    <th width="90" rowspan="2">Sewing Unit</th>
                    <th width="70" rowspan="2">Sewing Line</th>
                    <th width="100" rowspan="2">Color</th>
                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
                    <th width="80" rowspan="2" >Total</th>
                </tr>
                <tr>
                <?
				$grand_tot_in=0;
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
                    <?
				}
				?>
                </tr>
                
            </thead>
            <tbody>
            <?
			$i=1;
			$k=1;
			//var_dump($result);die;
			foreach($result as $row)
			{
				if(!in_array($row[csf("sewing_line")],$temp_arr[$row[csf("country_id")]]))
				{
					$temp_arr[$row[csf("country_id")]][]=$row[csf("sewing_line")];
					if($k!=1)
					{
						?>
						<tr bgcolor="#CCCCCC">
							<td >&nbsp;</td>
							<td>&nbsp;</td>
							<td >&nbsp;</td>
							<td >&nbsp;</td>
							<td >&nbsp;</td>
							<td >&nbsp;</td>
							<td >&nbsp;</td>
							<?
							foreach($sizearr_order as $size_id)
							{
								?>
								<td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
								<?
							}
							?>
							<td align="right"><? echo number_format($line_color_total_in,0); ?></td>
						</tr>
						<?
						$line_color_size_in = $line_color_total_in ="";
					}
					$k++;
				}
				
				$sewing_line='';
				if($row[csf('prod_reso_allo')]==1)
				{
					$line_number=explode(",",$prod_reso_arr[$row[csf('sewing_line')]]);
					foreach($line_number as $val)
					{
						if($sewing_line=='') $sewing_line=$sewing_line_library[$val]; else $sewing_line.=",".$sewing_line_library[$val];
					}
				}
				else $sewing_line=$sewing_line_library[$row[csf('sewing_line')]];
				
				?>
                <tr>
                    <td align="center"><? echo $i;  ?></td>
                    <td ><p><? echo $country_library[$row[csf("country_id")]];  ?></p></td>
                    <td ><p><? echo $knitting_source[$row[csf("production_source")]]; ?><p></td>
                    <td ><p><? echo $row[csf("challan_no")];  ?></p></td>
                    <td ><p><? echo $floor_library[$row[csf("floor_id")]];  ?></p></td>
                    <td align="center"><p><? echo $sewing_line;  ?></p></td>
                    <td ><p><? echo $colorarr[$row[csf("color_number_id")]];  ?></p></td>
                    <?
					$color_total_in=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><p>
						<?
						 	echo number_format($production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('sewing_line')]][$row[csf('production_source')]][$row[csf('color_number_id')]][$size_id],0) ;
							 $color_total_in+= $production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('sewing_line')]][$row[csf('production_source')]][$row[csf('color_number_id')]][$size_id]; 
							 $color_size_in [$size_id]+=$production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('sewing_line')]][$row[csf('production_source')]][$row[csf('color_number_id')]][$size_id];
							 $line_color_total_in+= $production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('sewing_line')]][$row[csf('production_source')]][$row[csf('color_number_id')]][$size_id]; 
							 $line_color_size_in [$size_id]+=$production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('sewing_line')]][$row[csf('production_source')]][$row[csf('color_number_id')]][$size_id];
						 ?>
                        </p></td>
                        <?
                    }
                    ?>
                    <td align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
                </tr>
                <?
				$i++;
			}
			?>
            <tr bgcolor="#CCCCCC">
                <td >&nbsp;</td>
                <td>&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <?
                foreach($sizearr_order as $size_id)
                {
                    ?>
                    <td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
                    <?
                }
                ?>
                <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
            </tr>
            </tbody>
            <tfoot>
                <th >&nbsp;</th>
                <th>&nbsp;</th>
                <th >&nbsp;</th>
                <th >&nbsp;</th>
                <th >&nbsp;</th>
                <th >&nbsp;</th>
                <th >&nbsp;</th>
                <?
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th ><? echo $color_size_in[$size_id]; ?></th>
                    <?
				}
				?>
                <th ><? echo $grand_tot_in; ?></th>
                
            </tfoot>
        </table>
        </div>
    </fieldset>
<?	
}

if($action=="sewingQnty_input_location_popup")
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  ); 
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  ); 
	$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	$floor_library=return_library_array( "select id,floor_name from  lib_prod_floor", "id", "floor_name"  );
	$sewing_line_library=return_library_array( "select id,line_name from  lib_sewing_line", "id", "line_name"  );
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	
	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");
	$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	//var_dump();die;
	
	$po_details_sql=sql_select("select a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.id=$po_break_down_id group by a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id");
	//echo $po_details_sql;die;
	
	if($db_type==2)
	{
		$sql= "SELECT  a.challan_no, a.floor_id, a.sewing_line, a.country_id, a.production_source, c.color_number_id, LISTAGG(CAST(c.size_number_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.size_number_id) as size_number_id , a.prod_reso_allo 
		from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c 
		where a.production_type=4 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.location=$location and a.floor_id=$floor_id and a.sewing_line=$sewing_line and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
		group by  a.country_id,a.challan_no, a.floor_id, a.sewing_line, a.production_source, a.prod_reso_allo,c.color_number_id
		order by a.country_id,a.challan_no, a.floor_id, a.sewing_line";
	}
	else if($db_type==0)
	{
		$sql= "SELECT a.challan_no,a.floor_id, a.sewing_line,a.country_id,a.production_source, c.color_number_id, group_concat(c.size_number_id) as size_number_id , a.prod_reso_allo 
		from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c 
		where a.production_type=4 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.location=$location and a.floor_id=$floor_id and a.sewing_line=$sewing_line and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
		group by  a.country_id,a.challan_no, a.floor_id, a.sewing_line, a.production_source, a.prod_reso_allo,c.color_number_id
		order by a.country_id,a.challan_no, a.floor_id, a.sewing_line";
	}
	//echo $sql;die;
	
	
	//echo $sql; //and a.production_date='$production_date'
	$result=sql_select($sql);
	
	$sql_color_size= sql_select("SELECT a.challan_no,a.floor_id, a.sewing_line,a.country_id,a.production_source, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id 
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c 
	where a.production_type=4 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.location=$location and a.floor_id=$floor_id and a.sewing_line=$sewing_line and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
	group by   a.country_id,a.challan_no, a.floor_id, a.sewing_line, a.production_source,c.color_number_id,c.size_number_id
	order by a.country_id");
	foreach($sql_color_size as $row)
	{
		$production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('sewing_line')]][$row[csf('production_source')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
		$summery_data[$row[csf('color_number_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}
	
	$col_width=60*count($sizearr_order);
	$table_width=630+$col_width;
	$summer_table_width=230+$col_width;


?>	
<script>

	function print_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		d.close();
	}
	
	function window_close()
	{
		parent.emailwindow.hide();
	}
	
</script>	
<fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
		<input type="button" value="Print" onClick="print_window()" style="width:100px; margin-left:400px;"  class="formbutton" /><br />
    <div style="100%" id="report_container">
    
        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px;  margin-top:10px;">
        	<tr>
            	<td style="font-size:14px; font-weight:bold;">
                Buyer Name : <? echo $buyer_library[$po_details_sql[0][csf("buyer_name")]]; ?>&nbsp;&nbsp;&nbsp;&nbsp; Job No : <? echo $po_details_sql[0][csf("job_no")]; ?>&nbsp;&nbsp;&nbsp;&nbsp; Style No : <? echo $po_details_sql[0][csf("style_ref_no")]; ?>&nbsp;&nbsp;&nbsp;&nbsp; Garments Item : 
				<?
                    $item_data=""; 
                    $garments_item_arr=array_unique(explode(",",$po_details_sql[0][csf("gmts_item_id")])); 
                    foreach($garments_item_arr as $item_id)
                    {
                        if($item_data!="") $item_data .=", ";
                        $item_data .=$garments_item[$item_id];
                    }
                    echo $item_data;
                ?>
                <br />
                Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;Date : <? echo change_date_format($production_date); ?> 
                <br />
                Summary
                </td>
            </tr>
        </table>
        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $summer_table_width; ?>" style="margin-bottom:20px;">
            <thead>
                <tr>
                	<th width="40" rowspan="2">SI</th>
                    <th width="100" rowspan="2">Color</th>
                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
                    <th width="80" rowspan="2" >Total</th>
                </tr>
                <tr>
                <?
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
                    <?
				}
				?>
                </tr>
                
            </thead>
            <tbody>
            <?
			$i=1;
			//var_dump($result);die;
			foreach($summery_data as $color_id=>$row)
			{
				//print_r($row);die;
				
				?>
                <tr>
                    <td align="center"><? echo $i;  ?></td>
                    <td ><? echo $colorarr[$color_id];  ?></td>
                    <?
					$summry_color_total_in =0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format($row[$size_id],0) ; $summry_color_total_in+= $row[$size_id]; $summry_color_size_in [$size_id]+=$row[$size_id];?></td>
                        <?
                    }
                    ?>
                    <td align="right"><? echo  number_format( $summry_color_total_in,0); $grand_tot_in+=$summry_color_total_in; ?></td>
                </tr>
                <?
				$i++;
			}
			?>
            </tbody>
            <tfoot>
                <th >&nbsp;</th>
                <th >&nbsp;</th>
                <?
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th ><? echo $summry_color_size_in[$size_id]; ?></th>
                    <?
				}
				?>
                <th ><? echo $grand_tot_in; ?></th>
                
            </tfoot>
        </table>
        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px;">
        	<tr>
            	<td style="font-size:14px; font-weight:bold;">Details</td>
            </tr>
        </table>
        
        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                	<th width="40" rowspan="2">SI</th>
                    <th width="100" rowspan="2">Country Name</th>
                    <th width="80" rowspan="2">Source</th>
                    <th width="70" rowspan="2">Challan</th>
                    <th width="90" rowspan="2">Sewing Unit</th>
                    <th width="70" rowspan="2">Sewing Line</th>
                    <th width="100" rowspan="2">Color</th>
                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
                    <th width="80" rowspan="2" >Total</th>
                </tr>
                <tr>
                <?
				$grand_tot_in=0;
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
                    <?
				}
				?>
                </tr>
                
            </thead>
            <tbody>
            <?
			$i=1;
			$k=1;
			//var_dump($result);die;
			foreach($result as $row)
			{
				if(!in_array($row[csf("sewing_line")],$temp_arr[$row[csf("country_id")]]))
				{
					$temp_arr[$row[csf("country_id")]][]=$row[csf("sewing_line")];
					if($k!=1)
					{
						?>
						<tr bgcolor="#CCCCCC">
							<td >&nbsp;</td>
							<td>&nbsp;</td>
							<td >&nbsp;</td>
							<td >&nbsp;</td>
							<td >&nbsp;</td>
							<td >&nbsp;</td>
							<td >&nbsp;</td>
							<?
							foreach($sizearr_order as $size_id)
							{
								?>
								<td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
								<?
							}
							?>
							<td align="right"><? echo number_format($line_color_total_in,0); ?></td>
						</tr>
						<?
						$line_color_size_in = $line_color_total_in ="";
					}
					$k++;
				}
				
				$sewing_line='';
				if($row[csf('prod_reso_allo')]==1)
				{
					$line_number=explode(",",$prod_reso_arr[$row[csf('sewing_line')]]);
					foreach($line_number as $val)
					{
						if($sewing_line=='') $sewing_line=$sewing_line_library[$val]; else $sewing_line.=",".$sewing_line_library[$val];
					}
				}
				else $sewing_line=$sewing_line_library[$row[csf('sewing_line')]];
				
				?>
                <tr>
                    <td align="center"><? echo $i;  ?></td>
                    <td ><p><? echo $country_library[$row[csf("country_id")]];  ?></p></td>
                    <td ><p><? echo $knitting_source[$row[csf("production_source")]]; ?><p></td>
                    <td ><p><? echo $row[csf("challan_no")];  ?></p></td>
                    <td ><p><? echo $floor_library[$row[csf("floor_id")]];  ?></p></td>
                    <td align="center"><p><? echo $sewing_line;  ?></p></td>
                    <td ><p><? echo $colorarr[$row[csf("color_number_id")]];  ?></p></td>
                    <?
					$color_total_in=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><p>
						<?
						 	echo number_format($production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('sewing_line')]][$row[csf('production_source')]][$row[csf('color_number_id')]][$size_id],0) ;
							 $color_total_in+= $production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('sewing_line')]][$row[csf('production_source')]][$row[csf('color_number_id')]][$size_id]; 
							 $color_size_in [$size_id]+=$production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('sewing_line')]][$row[csf('production_source')]][$row[csf('color_number_id')]][$size_id];
							 $line_color_total_in+= $production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('sewing_line')]][$row[csf('production_source')]][$row[csf('color_number_id')]][$size_id]; 
							 $line_color_size_in [$size_id]+=$production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('sewing_line')]][$row[csf('production_source')]][$row[csf('color_number_id')]][$size_id];
						 ?>
                        </p></td>
                        <?
                    }
                    ?>
                    <td align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
                </tr>
                <?
				$i++;
			}
			?>
            <tr bgcolor="#CCCCCC">
                <td >&nbsp;</td>
                <td>&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <?
                foreach($sizearr_order as $size_id)
                {
                    ?>
                    <td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
                    <?
                }
                ?>
                <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
            </tr>
            </tbody>
            <tfoot>
                <th >&nbsp;</th>
                <th>&nbsp;</th>
                <th >&nbsp;</th>
                <th >&nbsp;</th>
                <th >&nbsp;</th>
                <th >&nbsp;</th>
                <th >&nbsp;</th>
                <?
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th ><? echo $color_size_in[$size_id]; ?></th>
                    <?
				}
				?>
                <th ><? echo $grand_tot_in; ?></th>
                
            </tfoot>
        </table>
        </div>
    </fieldset>
<?	
}

if($action=="ironQnty_popup")
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  ); 
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  ); 
	$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	$floor_library=return_library_array( "select id,floor_name from  lib_prod_floor", "id", "floor_name"  );
	$sewing_line_library=return_library_array( "select id,line_name from  lib_sewing_line", "id", "line_name"  );
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	
	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");
	$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	//var_dump();die;
	
	$po_details_sql=sql_select("select a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.id=$po_break_down_id group by a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id");
	//echo $po_details_sql;die;
	
	if($db_type==2)
	{
		$sql= "SELECT a.challan_no, a.floor_id, a.country_id, c.color_number_id, LISTAGG(CAST(c.size_number_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.size_number_id) as size_number_id 
		from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c 
		where a.production_type=7 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
		group by  a.challan_no, a.floor_id, a.country_id, c.color_number_id
		order by a.country_id, a.challan_no, a.floor_id";
	}
	else if($db_type==0)
	{
		$sql= "SELECT a.challan_no, a.floor_id, a.country_id, c.color_number_id, group_concat(c.size_number_id) as size_number_id
		from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c 
		where a.production_type=7 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
		group by a.country_id,a.challan_no, a.floor_id, c.color_number_id
		order by a.country_id,a.challan_no, a.floor_id, a.sewing_line";
	}
	//echo $sql;die;
	if($prod_source!=0 || $prod_source!='' ) $prod_source_cond=" and production_source='$prod_source'";
	
	//echo $sql; //and a.production_date='$production_date'
	$result=sql_select($sql);
	
	$sql_color_size= sql_select("SELECT a.challan_no, a.floor_id, a.country_id, c.color_number_id, c.size_number_id, b.production_qnty as production_qnty
	from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c 
	where a.production_type=7 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $prod_source_cond ");
	
	foreach($sql_color_size as $row)
	{
		//$production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['in'] +=$row[csf('in_quantity')];
		//$production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['out'] +=$row[csf('out_quantity')];
		$production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('production_qnty')];
		$summery_data[$row[csf('color_number_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}
	
	$col_width=60*count($sizearr_order);
	$table_width=630+$col_width;
	$summer_table_width=230+$col_width;


?>	
<script>

	function print_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		d.close();
	}
	
	function window_close()
	{
		parent.emailwindow.hide();
	}
	
</script>	
<fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
	<input type="button" value="Print" onClick="print_window()" style="width:100px; margin-left:400px;"  class="formbutton" /><br />
    <div style="100%" id="report_container">
        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
        	<tr>
            	<td style="font-size:14px; font-weight:bold;">
                Buyer Name : <? echo $buyer_library[$po_details_sql[0][csf("buyer_name")]]; ?>&nbsp;&nbsp;&nbsp;&nbsp; Job No : <? echo $po_details_sql[0][csf("job_no")]; ?>&nbsp;&nbsp;&nbsp;&nbsp; Style No : <? echo $po_details_sql[0][csf("style_ref_no")]; ?>&nbsp;&nbsp;&nbsp;&nbsp; Garments Item : 
				<?
                    $item_data=""; 
                    $garments_item_arr=array_unique(explode(",",$po_details_sql[0][csf("gmts_item_id")])); 
                    foreach($garments_item_arr as $item_id)
                    {
                        if($item_data!="") $item_data .=", ";
                        $item_data .=$garments_item[$item_id];
                    }
                    echo $item_data;
                ?>
                <br />
                Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;Date : <? echo change_date_format($production_date); ?> 
                <br />
                Summary
                </td>
            </tr>
        </table>
        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $summer_table_width; ?>" style="margin-bottom:20px;">
            <thead>
                <tr>
                	<th width="40" rowspan="2">SI</th>
                    <th width="100" rowspan="2">Color</th>
                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
                    <th width="80" rowspan="2" >Total</th>
                </tr>
                <tr>
					<?
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <th width="60"><? echo $sizearr[$size_id]; ?></th>
                        <?
                    }
                    ?>
                </tr>
            </thead>
            <tbody>
            <?
			$i=1;
			//var_dump($result);die;
			foreach($summery_data as $color_id=>$row)
			{
				//print_r($row);die;
				?>
                <tr>
                    <td align="center"><? echo $i;  ?></td>
                    <td><? echo $colorarr[$color_id];  ?></td>
                    <?
					$summry_color_total_in =0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format($row[$size_id],0) ; $summry_color_total_in+= $row[$size_id]; $summry_color_size_in [$size_id]+=$row[$size_id];?></td>
                        <?
                    }
                    ?>
                    <td align="right"><? echo  number_format( $summry_color_total_in,0); $grand_tot_in+=$summry_color_total_in; ?></td>
                </tr>
                <?
				$i++;
			}
			?>
            </tbody>
            <tfoot>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <?
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th><? echo $summry_color_size_in[$size_id]; ?></th>
                    <?
				}
				?>
                <th><? echo $grand_tot_in; ?></th>
            </tfoot>
        </table>
        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px;">
        	<tr>
            	<td style="font-size:14px; font-weight:bold;">Details</td>
            </tr>
        </table>
        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                	<th width="40" rowspan="3">SI</th>
                    <th width="100" rowspan="3">Country Name</th>
                    <th width="80" rowspan="3">Source</th>
                    <th width="70" rowspan="3">Challan</th>
                    <th width="70" rowspan="3">Floor</th>
                    <th width="100" rowspan="3">Color</th>
                    <? if ($prod_source==1) { $prod_source="In-House"; } else if ($prod_source==3) { $prod_source="Out-Bound"; }?>
                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>"><? echo $prod_source; ?></th>
                    <th width="80" rowspan="3" >Total</th>
                </tr>
                <tr>
                    <th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
                </tr>
                <tr>
                <?
				$grand_tot_in=0;
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
                    <?
				}
				?>
                </tr>
            </thead>
            <tbody>
            <?
			$i=1;
			$k=1;
			//var_dump($result);die;
			foreach($result as $row)
			{
				?>
                <tr>
                    <td align="center"><? echo $i;  ?></td>
                    <td><p><? echo $country_library[$row[csf("country_id")]];  ?></p></td>
                    <td><p><? echo $knitting_source[$row[csf("production_source")]]; ?><p></td>
                    <td><p><? echo $row[csf("challan_no")];  ?></p></td>
                    <td><p><? echo $floor_library[$row[csf("floor_id")]];  ?></p></td>
                    <td><p><? echo $colorarr[$row[csf("color_number_id")]];  ?></p></td>
                    <?
					$color_total=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><p>
						<?
							$production_break_qty_in=0;
							$production_break_qty_in=$production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('color_number_id')]][$size_id];
						 	echo number_format($production_break_qty_in,0) ;
							
							 $color_total+= $production_break_qty_in; 
							 $color_size_in [$size_id]+=$production_break_qty_in;
						 ?>
                        </p></td>
                        <?
                    }
					
                    ?>
                    <td align="right"><p><? echo  number_format( $color_total,0); $grand_tot_in+=$color_total; ?></p></td>
                </tr>
                <?
				$i++;
			}
			?>
            </tbody>
            <tfoot>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <?
				foreach($sizearr_order as $size_id)
                {
                    ?>
                    <th align="right"><? echo number_format($color_size_in[$size_id],0); ?></th>
                    <?
                }
				
				?>
                <th><? echo $grand_tot_in; ?></th>
            </tfoot>
        </table>
        </div>
    </fieldset>
<?	
exit();
}
 

?>