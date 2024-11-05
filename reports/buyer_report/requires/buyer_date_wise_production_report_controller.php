<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$user_name=$_SESSION['logic_erp']['user_id'];
//--------------------------------------------------------------------------------------------------------------------

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location", 110, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/buyer_date_wise_production_report_controller', this.value, 'load_drop_down_floor', 'floor_td' );",0 );     	 
}

if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor", 110, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' order by floor_name","id,floor_name", 1, "-- Select --", $selected, "",0 );     	 
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
	//$costing_per_arr = return_library_array("select job_no, costing_per from wo_pre_cost_mst","job_no","costing_per");
	$tot_cost_arr=array(); $tot_cm_cost_arr=array();
	//$tot_cost_arr = return_library_array("select job_no, cm_for_sipment_sche from wo_pre_cost_dtls","job_no","cm_for_sipment_sche"); 
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
 	/*if($template==1)
	{
	*/	
		
		//print_r($_REQUEST);die;
		$garments_nature=str_replace("'","",$cbo_garments_nature);
		if($garments_nature==1)$garments_nature="";
		$type = str_replace("'","",$cbo_type);
		$location = str_replace("'","",$cbo_location);
		$file_no = str_replace("'","",$txt_file_no);
		$internal_ref = str_replace("'","",$txt_internal_ref);
		$excel_type = str_replace("'","",$excel_type);
		if(str_replace("'","",$cbo_company_name)==0)$company_name=""; else $company_name=" and b.company_name=$cbo_company_name";
		if(str_replace("'","",$cbo_buyer_name)==0)$buyer_name="";else $buyer_name=" and b.buyer_name=$cbo_buyer_name";
		if(str_replace("'","",trim($txt_date_from))=="" || str_replace("'","",trim($txt_date_to))=="") $txt_date="";
		else $txt_date=" and c.production_date between $txt_date_from and $txt_date_to";
		$fromDate = change_date_format( str_replace("'","",trim($txt_date_from)) );
		$toDate = change_date_format( str_replace("'","",trim($txt_date_to)) );
		if ($location==0) $location_cond=""; else $location_cond=" and c.location=".$location." "; 
		if(str_replace("'","",$cbo_floor)==0) $floor_name="";else $floor_name=" and c.floor_id=$cbo_floor";
		if ($file_no=="") $file_no_cond=""; else $file_no_cond=" and a.file_no='".trim($file_no)."' "; 
		if ($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and a.grouping='".trim($internal_ref)."' "; 
		//echo $file_no_cond;die;
		//cbo_garments_nature
		if(str_replace("'","",trim($txt_date_from))!="" || str_replace("'","",trim($txt_date_to))!="") $day_check=" and b.date_calc between $txt_date_from and $txt_date_to";else $day_check="";
		//echo date($txt_date_from);
		//$month=date("F",$time);
		$year_id = date('Y', strtotime($fromDate));
		if($year_id!="") $year_cond=" and a.year=$year_id ";else $year_cond="";
		//echo $year_cond;
		$all_order_sewQty_array=array();
		
		$sql_cal="select a.location_id,b.date_calc, b.day_status from  lib_capacity_calc_mst a, lib_capacity_calc_dtls b where a.id=b.mst_id  and a.status_active=1 and a.is_deleted=0 and a.comapny_id=$cbo_company_name and b.day_status=2 $day_check $year_cond group by b.date_calc,a.location_id, b.day_status";
		$result_data=sql_select($sql_cal);
		$off_day_check=array();
		foreach($result_data as $row)
		{
			$off_day_check[$row[csf('date_calc')]][$row[csf('location_id')]]['day']=$row[csf('day_status')];	
		}
		if($db_type==2) $day_chek="TO_CHAR(c.production_hour,'HH24:MI')";
		else  if($db_type==0) $day_chek="TIME_FORMAT(c.production_hour, '%H:%i' )";
		
		 $sql_order=sql_select("SELECT  c.location,c.po_break_down_id as po_id,c.production_date,c.item_number_id,
		  (CASE WHEN production_type ='1' THEN $day_chek ELSE null END) AS cutting_hr,
		  (CASE WHEN production_type ='7' THEN $day_chek ELSE null END) AS iron_hr,
		  (CASE WHEN production_type ='7' THEN $day_chek ELSE null END) AS re_iron_hr,
		  (CASE WHEN production_type ='8' THEN $day_chek ELSE null END) AS finish_hr
		  from pro_garments_production_mst c where  c.company_id=$cbo_company_name and  c.is_deleted=0 and c.status_active=1  and c.production_type ='1' $txt_date  $floor_name $location_cond $garmentsNature ");
		$day_checkarr=array();$prod_arr=array();
		foreach($sql_order as $row)
		{
			$prod_arr[$row[csf('po_id')]][$row[csf('production_date')]][$row[csf('item_number_id')]]['1']=$row[csf('cutting_hr')];
			$prod_arr[$row[csf('po_id')]][$row[csf('production_date')]][$row[csf('item_number_id')]]['7']=$row[csf('iron_hr')];
			$prod_arr[$row[csf('po_id')]][$row[csf('production_date')]][$row[csf('item_number_id')]]['7']=$row[csf('re_iron_hr')];
			$prod_arr[$row[csf('po_id')]][$row[csf('production_date')]][$row[csf('item_number_id')]]['8']=$row[csf('finish_hr')];
			$prod_arr[$row[csf('po_id')]][$row[csf('production_date')]][$row[csf('item_number_id')]]['location']=$row[csf('location')];
		}
		
		if($type==1) //--------------------------------------------Show Date Wise
		{
			ob_start();
			?>
			<div>
                <table width="3530" cellspacing="0">
                    <tr class="form_caption" style="border:none;">
                            <td colspan="40" align="center" style="border:none;font-size:14px; font-weight:bold" >Date Wise Production Report</td>
                     </tr>
                    <tr style="border:none;">
                            <td colspan="40" align="center" style="border:none; font-size:16px; font-weight:bold">
                                Company Name:<? echo $company_library[str_replace("'","",$cbo_company_name)]; ?>                                
                            </td>
                      </tr>
                      <tr style="border:none;">
                            <td colspan="40" align="center" style="border:none;font-size:12px; font-weight:bold">
                            	<? echo "From $fromDate To $toDate" ;?>
                            </td>
                      </tr>
                </table>
                <br />
                    <table width="1470" cellspacing="0" border="1" class="rpt_table" rules="all" id="" >
                        <thead>
                            <tr>
                                <th width="30">Sl.</th>    
                                <th width="80">Buyer Name</th>
                                <th width="80">Cut Qty</th>
                               <!-- <th width="80">Sent Embl.</th>
                                <th width="80">Rec. From Embl.</th>-->
                                
                                <th width="80">Sent to Print</th>
                                <th width="80">Rev Print</th>
                                <th width="80">Sent to Emb</th>
                                <th width="80">Rev Emb</th>
                                <th width="80">Sent to Wash</th>
                                <th width="80">Rev Wash</th>
                                <th width="80">Sent to Sp. Works</th>
                                <th width="80">Rev Sp. Works</th>
                                
                                <th width="80">Sew Input</th>
                                <th width="80">Sew Output</th>
                                <th width="80">Total Iron</th>
                                <th width="80">Total Iron SMV</th>
                                <th width="80">Total Re-Iron</th>
                                <th width="80">Total Finish</th>
                             </tr>
                        </thead>
                    </table>
                    <table cellspacing="0" border="1" class="rpt_table"  width="1470" rules="all" id="" >
                    <?
                    
                    $cutting_array=array();
                    /*$printing_array=array();
                    $printreceived_array=array();*/
					
					$printing_array=array();
					$printreceived_array=array();
					$emb_array=array();
					$embreceived_array=array();
					$wash_array=array();
					$washreceived_array=array();
					$sp_array=array();
					$spreceived_array=array();
					
                    $sewingin_array=array();
                    $sewingout_array=array();
                    $iron_array=array();
                    $re_iron_array=array();
                    $finish_array=array();
					//Off Day Check Array
					if($garments_nature!="") $garments_nature=" and c.garments_nature=$garments_nature";
					
					//print_r($prod_arr);
					
                    if($db_type==2) $day_chek="TO_CHAR(c.production_hour,'HH24:MI') as production_hour,";
					else  if($db_type==0) $day_chek="TIME_FORMAT(c.production_hour, '%H:%i' ) as production_hour,";
                    $sql_order=sql_select("SELECT b.buyer_name, c.po_break_down_id, c.item_number_id,c.item_number_id, c.production_date,
                    sum(CASE WHEN production_type ='1' THEN production_quantity ELSE 0 END) AS cutting_qnty,
					sum(CASE WHEN c.production_type ='2' AND c.embel_name=1 THEN c.production_quantity ELSE 0 END) AS printing_qnty,
					sum(CASE WHEN c.production_type ='3' AND c.embel_name=1 THEN c.production_quantity ELSE 0 END) AS printreceived_qnty,
					sum(CASE WHEN c.production_type ='2' AND c.embel_name=2 THEN c.production_quantity ELSE 0 END) AS emb_qnty,
					sum(CASE WHEN c.production_type ='3' AND c.embel_name=2 THEN c.production_quantity ELSE 0 END) AS embreceived_qnty,
					sum(CASE WHEN c.production_type ='2' AND c.embel_name=3 THEN c.production_quantity ELSE 0 END) AS wash_qnty,
					sum(CASE WHEN c.production_type ='3' AND c.embel_name=3 THEN c.production_quantity ELSE 0 END) AS washreceived_qnty,
					sum(CASE WHEN c.production_type ='2' AND c.embel_name=4 THEN c.production_quantity ELSE 0 END) AS sp_qnty,
					sum(CASE WHEN c.production_type ='3' AND c.embel_name=4 THEN c.production_quantity ELSE 0 END) AS spreceived_qnty,
                    sum(CASE WHEN production_type ='4' THEN production_quantity ELSE 0 END) AS sewingin_qnty,
                    sum(CASE WHEN production_type ='5' THEN production_quantity ELSE 0 END) AS sewingout_qnty,                                          
                    sum(CASE WHEN production_type ='7' THEN production_quantity ELSE 0 END) AS iron_qnty,
                    sum(CASE WHEN production_type ='7' THEN re_production_qty ELSE 0 END) AS re_iron_qnty,
                    sum(CASE WHEN production_type ='8' THEN production_quantity ELSE 0 END) AS finish_qnty
                                from 
                                    wo_po_details_master b, wo_po_break_down a, pro_garments_production_mst c
                                where  
                                    a.job_no_mst=b.job_no and a.id=c.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $txt_date $company_name $floor_name $location_cond $garmentsNature $file_no_cond $internal_ref_cond  group by b.buyer_name, c.po_break_down_id, c.item_number_id,c.item_number_id, c.production_date");
									
					/*
					sum(CASE WHEN production_type ='2' THEN production_quantity ELSE 0 END) AS printing_qnty,
                    sum(CASE WHEN production_type ='3' THEN production_quantity ELSE 0 END) AS printreceived_qnty,
					*/
                    foreach($sql_order as $sql_result)
                    {
                         $loaction_id=$prod_arr[$sql_result[csf('po_break_down_id')]][$sql_result[csf('production_date')]][$sql_result[csf('item_number_id')]]['location'];
						 $cutting_hour=$prod_arr[$sql_result[csf('po_break_down_id')]][$sql_result[csf('production_date')]][$sql_result[csf('item_number_id')]]['1'];
						$iron_hour=$prod_arr[$sql_result[csf('po_break_down_id')]][$sql_result[csf('production_date')]][$sql_result[csf('item_number_id')]]['7'];
						$re_iron_hour=$prod_arr[$sql_result[csf('po_break_down_id')]][$sql_result[csf('production_date')]][$sql_result[csf('item_number_id')]]['7'];
						$finish_hr=$prod_arr[$sql_result[csf('po_break_down_id')]][$sql_result[csf('production_date')]][$sql_result[csf('item_number_id')]]['8'];
						
					 $off_days_check=$off_day_check[$sql_result[csf('production_date')]][$loaction_id]['day'];
					  if($off_days_check!=2)
					  {
						
					 
						$time_valid="19:00";
						if($time_valid>=$cutting_hour)
						{
							 $cutting_qty=$sql_result[csf("cutting_qnty")];
						}
						else
						{
							 $cutting_qty=0;
						}
						if($time_valid>=$iron_hour)
						{
							 $tot_iron=$sql_result[csf("iron_qnty")];
						}
						else
						{
							  $tot_iron=0;
						}
						if($time_valid>=$re_iron_hour)
						{
							 $re_iron_qty=$sql_result[csf("re_iron_qnty")];
						}
						else
						{
							  $re_iron_qty=0;
						}
						if($time_valid>=$finish_hr)
						{
							 $fqty=$sql_result[csf("finish_qnty")];
						}
						else
						{
							  $fqty=0;
						}
					   
						
                        /*$printing_array[$sql_result[csf("buyer_name")]]['2']=$sql_result[csf("printing_qnty")];
                        $printreceived_array[$sql_result[csf("buyer_name")]]['3']=$sql_result[csf("printreceived_qnty")];*/
						
						$printing_array[$sql_result[csf("buyer_name")]]+=$sql_result[csf("printing_qnty")];
                        $printreceived_array[$sql_result[csf("buyer_name")]]+=$sql_result[csf("printreceived_qnty")];
						$emb_array[$sql_result[csf("buyer_name")]]+=$sql_result[csf("emb_qnty")];
                        $embreceived_array[$sql_result[csf("buyer_name")]]+=$sql_result[csf("embreceived_qnty")];
						$wash_array[$sql_result[csf("buyer_name")]]+=$sql_result[csf("wash_qnty")];
                        $washreceived_array[$sql_result[csf("buyer_name")]]+=$sql_result[csf("washreceived_qnty")];
						$sp_array[$sql_result[csf("buyer_name")]]+=$sql_result[csf("sp_qnty")];
                        $spreceived_array[$sql_result[csf("buyer_name")]]+=$sql_result[csf("spreceived_qnty")];
						
                        $sewingin_array[$sql_result[csf("buyer_name")]]['4']+=$sql_result[csf("sewingin_qnty")];
                        $sewingout_array[$sql_result[csf("buyer_name")]]['5']+=$sql_result[csf("sewingout_qnty")];
                        $cutting_array[$sql_result[csf("buyer_name")]]['1']+=$cutting_qty;	
						$iron_array[$sql_result[csf("buyer_name")]]['7']+=$tot_iron;
						$iron_smv_array[$sql_result[csf("buyer_name")]]['10']+=$tot_iron*$job_item_smv_arr[$sql_result[csf('po_break_down_id')]][$sql_result[csf('item_number_id')]]['finsmv_pcs'];
                        $re_iron_array[$sql_result[csf("buyer_name")]]['7']+=$re_iron_qty;
                        $finish_array[$sql_result[csf("buyer_name")]]['8']+=$fqty;
						}
                    }
					
                    
                     $total_po_quantity=0;$total_po_value=0;
                     $total_cut=0;$total_sent_embl=0;
                     $total_re_from_embl=0; $total_sew_input=0;
                     $total_sew_out=0;$total_iron=0;$total_re_iron=0;
                     $total_finish=0;
                     $i=1;
                     
                     // garments nature here -------------------------------							
                     if($garments_nature==1 || $garments_nature=="") $garmentsNature=""; else $garmentsNature=" and b.garments_nature=$garments_nature";
                    
                    
                    if($db_type==0)
                    {                           
                        $pro_date_sql_query="SELECT b.company_name, b.buyer_name,sum(a.po_quantity*b.total_set_qnty) as po_quantity,sum(a.po_total_price) as po_total_price,a.id,TIME_FORMAT(c.production_hour, '%H:%i' ) as production_hour
                                from lib_buyer d, wo_po_details_master b, wo_po_break_down a, pro_garments_production_mst c
                                where a.job_no_mst=b.job_no and a.id=c.po_break_down_id and b.buyer_name=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.company_name=$cbo_company_name and c.production_date between $txt_date_from and $txt_date_to $buyer_name $floor_name $location_cond $garmentsNature $file_no_cond $internal_ref_cond group by b.buyer_name order by d.buyer_name ASC"; 
                    }
                    else
                    {
                          $pro_date_sql_query="SELECT b.buyer_name,sum(a.po_quantity*b.total_set_qnty) as po_quantity,sum(a.po_total_price) as po_total_price
                                from lib_buyer d, wo_po_details_master b, wo_po_break_down a, pro_garments_production_mst c
                                where a.job_no_mst=b.job_no and a.id=c.po_break_down_id and b.buyer_name=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.company_name=$cbo_company_name and c.production_date between $txt_date_from and $txt_date_to $buyer_name $floor_name $location_cond $garmentsNature $file_no_cond $internal_ref_cond group by b.buyer_name order by b.buyer_name ASC"; 
                        
                    }
					  $pro_date_sql=sql_select($pro_date_sql_query);
					  $buyer_data_array=array(); $buyer_po_price_array=array();
					foreach($pro_date_sql as $row)
                    {
						 $buyer_data_array[$row[csf('buyer_name')]]['po']=$row[csf('po_quantity')];	
						 $buyer_po_price_array[$row[csf('buyer_name')]]['price']=$row[csf('po_total_price')];
						// $buyer_po_price_array[$row[csf('buyer_name')]]['hr']=$row[csf('production_hour')];	
					}
					
                   //echo $pro_date_sql_query;//die; 
                 
                   
                    foreach($buyer_data_array as $buyer_key=>$value)
                    {
                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						
						
                        
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                            <td width="30"><? echo $i; ?></td>
                            <td width="80"><? echo $buyer_short_library[$buyer_key]; ?></td>
                            <td width="80" align="right"><? echo number_format($cutting_array[$buyer_key]['1']); ?></td>
                           <!-- <td width="80" align="right"><?// echo number_format($printing_array[$pro_date_sql_row[csf("buyer_name")]]['2']); ?></td>
                            <td width="80" align="right"><?// echo number_format($printreceived_array[$pro_date_sql_row[csf("buyer_name")]]['3']);  ?></t-->
                            
                            <td width="80" align="right"><? echo number_format($printing_array[$buyer_key]); ?></td>
                            <td width="80" align="right"><? echo number_format($printreceived_array[$buyer_key]);  ?></td>
                            <td width="80" align="right"><? echo number_format($emb_array[$buyer_key]); ?></td>
                            <td width="80" align="right"><? echo number_format($embreceived_array[$buyer_key]);  ?></td>
                            <td width="80" align="right"><? echo number_format($wash_array[$buyer_key]); ?></td>
                            <td width="80" align="right"><? echo number_format($washreceived_array[$buyer_key]);  ?></td>
                            <td width="80" align="right"><? echo number_format($sp_array[$buyer_key]); ?></td>
                            <td width="80" align="right"><? echo number_format($spreceived_array[$buyer_key]);  ?></td>
                            
                            <td width="80" align="right"><? echo number_format($sewingin_array[$buyer_key]['4']); ?></td>
                            <td width="80" align="right"><? echo number_format($sewingout_array[$buyer_key]['5']); ?></td>
                            <td width="80" align="right"><? echo number_format($iron_array[$buyer_key]['7']); ?></td>
                            <td width="80" align="right"><? echo number_format($iron_smv_array[$buyer_key]['10']); ?></td>
                            <td width="80" align="right"><? echo number_format($re_iron_array[$buyer_key]['7']); ?></td>
                            <td width="80" align="right"><? echo number_format($finish_array[$buyer_key]['8']); ?></td>
                        </tr>	
                        
                        <?
                        
                        $total_po_quantity+=$val;
                        $total_po_value+=$buyer_po_price_array[$buyer_key]['price'];
                        $total_cut+=$cutting_array[$buyer_key]['1'];
                        /*$total_sent_embl+=$printing_array[$pro_date_sql_row[csf("buyer_name")]]['2'];
                        $total_re_from_embl+=$printreceived_array[$pro_date_sql_row[csf("buyer_name")]]['3'];*/
						$total_sent_print+=$printing_array[$buyer_key];
                        $total_re_print+=$printreceived_array[$buyer_key];
						$total_sent_embl+=$emb_array[$buyer_key];
                        $total_re_from_embl+=$embreceived_array[$buyer_key];
						$total_sent_wash+=$wash_array[$buyer_key];
                        $total_re_from_wash+=$washreceived_array[$buyer_key];
						$total_sent_sp+=$sp_array[$buyer_key];
                        $total_re_from_sp+=$spreceived_array[$buyer_key];
						
                        $total_sew_input+=$sewingin_array[$buyer_key]['4'];
                        $total_sew_out+=$sewingout_array[$buyer_key]['5'];
                        $total_iron+=$iron_array[$buyer_key]['7'];
						$total_iron_smv+=$iron_smv_array[$buyer_key]['10'];
                        $total_re_iron+=$re_iron_array[$buyer_key]['7'];
                        $total_finish+=$finish_array[$buyer_key]['8']; 
                    $i++;
                    
                }//end foreach 1st
                    $chart_data_qnty="Order Qty;".$total_po_quantity."\n"."Cutting;".$total_cut."\n"."Sew In;".$total_sew_input."\n"."Sew Out ;".$total_sew_out."\n"."Iron ;".$total_iron."\n"."Finish ;".$total_finish."\n"."Ex-Fact;".$total_ex_factory."\n";
                ?>
                </table>
                <input type="hidden" id="graph_data" value="<? echo substr($chart_data_qnty,0,-1); ?>"/>
                 <table border="1" class="tbl_bottom"  width="1470" rules="all" id="" >
                         <tr> 
                            <td width="30">&nbsp;</td> 
                            <td width="80">Total</td> 
                            <td width="80" id="tot_cutting"><? echo number_format($total_cut); ?></td>
                            
                            <td width="80" id="total_sent_print"><? echo number_format($total_sent_print); ?></td>
                            <td width="80" id="total_re_print"><? echo number_format($total_re_print); ?></td> 
                            <td width="80" id="total_sent_embl"><? echo number_format($total_sent_embl); ?></td>
                            <td width="80" id="total_re_from_embl"><? echo number_format($total_re_from_embl); ?></td>
                            <td width="80" id="total_sent_wash"><? echo number_format($total_sent_wash); ?></td>
                            <td width="80" id="total_re_from_wash"><? echo number_format($total_re_from_wash); ?></td>
                            <td width="80" id="total_sent_sp"><? echo number_format($total_sent_sp); ?></td>
                            <td width="80" id="total_re_from_sp"><? echo number_format($total_re_from_sp); ?></td>
                            
                            <td width="80" id="tot_sew_in"><? echo number_format($total_sew_input); ?></td> 
                            <td width="80" id="tot_sew_out"><? echo number_format($total_sew_out); ?></td>   
                            <td width="80" id="tot_iron"><? echo number_format($total_iron); ?></td> 
                            <td width="80" id="total_iron_smv"><? echo number_format($total_iron_smv); ?></td> 
                            <td width="80" id="tot_re_iron"><? echo number_format($total_re_iron); ?></td> 
                            <td width="80" id="tot_finish"><? echo number_format($total_finish); ?></td>
                         </tr>
                 </table>
                 <br />
                 
                 
                 
                <table width="4220" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1">
                   <thead>
                        <th width="40">SL</th>    
                        <th width="80">Working Factory</th>
                        <th width="65">Job No</th>
                        <th width="55">Year</th>
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
                        <th width="80">Cutting</th>
                        <th width="80">Sent to Print</th>
                        <th width="80">Rev Print</th>
                        <th width="80">Sent to Emb</th>
                        <th width="80">Rev Emb</th>
                        <th width="80">Sent to Wash</th>
                        <th width="80">Rev Wash</th>
                        <th width="80">Sent to Sp. Works</th>
                        <th width="80">Rev Sp. Works</th>
                        <th width="80">Sewing In (Inhouse)</th>
                        <th width="80">Sewing In (Out-bound)</th>
                        <th width="80">Total Sewing Input</th>
                        
                        <th width="80">Sewing Out (Inhouse)</th>
                        <th width="80">Sewing Out (Out-bound)</th>
                        <th width="80">Total Sewing Out </th>
                        <th width="80">Iron Qty (Inhouse)</th>
                        <th width="80">Iron Qty (Out-bound)</th>
                        <th width="80">Total Iron Qty </th>
                        <th width="80">Total Iron SMV </th>
                        <th width="80">Re-Iron Qty </th>
                        <th width="80">Total Finishing Qty </th>
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
                        <th width="80">Remarks</th>
                     </thead>
                </table>
                <div style="max-height:425px; overflow-y:scroll; width:4240px" id="scroll_body">
                    <table cellspacing="0" border="1" class="rpt_table"  width="4220" rules="all" id="table_body" >
                    <?
					
					$cutting_array=array();
					$printing_array=array();
					$printreceived_array=array();
					$emb_array=array();
					$embreceived_array=array();
					$wash_array=array();
					$washreceived_array=array();
					$sp_array=array();
					$spreceived_array=array();
					$sewingin_inhouse_array=array();
					$sewingin_outbound_array=array();
					$sewingout_inhouse_array=array();
					$sewingout_outbound_array=array();
					$iron_in_array=array();
					$iron_out_array=array();
					$iron_array=array();
					$re_iron_array=array();
					$finish_array=array();
					$carton_array=array();
					//if($db_type==2) $time_con="TO_CHAR(c.production_hour,'HH24:MI') as production_hour";
					//else if($db_type==0) $time_con="TIME_FORMAT(c.production_hour, '%H:%i' ) as production_hour";
					$sql_order=("SELECT c.po_break_down_id,c.production_date,c.item_number_id,
					sum(CASE WHEN c.production_type ='1' THEN c.production_quantity ELSE 0 END) AS cutting_qnty,
					sum(CASE WHEN c.production_type ='2' AND c.embel_name=1 THEN c.production_quantity ELSE 0 END) AS printing_qnty,
					sum(CASE WHEN c.production_type ='3' AND c.embel_name=1 THEN c.production_quantity ELSE 0 END) AS printreceived_qnty,
					sum(CASE WHEN c.production_type ='2' AND c.embel_name=2 THEN c.production_quantity ELSE 0 END) AS emb_qnty,
					sum(CASE WHEN c.production_type ='3' AND c.embel_name=2 THEN c.production_quantity ELSE 0 END) AS embreceived_qnty,
					sum(CASE WHEN c.production_type ='2' AND c.embel_name=3 THEN c.production_quantity ELSE 0 END) AS wash_qnty,
					sum(CASE WHEN c.production_type ='3' AND c.embel_name=3 THEN c.production_quantity ELSE 0 END) AS washreceived_qnty,
					sum(CASE WHEN c.production_type ='2' AND c.embel_name=4 THEN c.production_quantity ELSE 0 END) AS sp_qnty,
					sum(CASE WHEN c.production_type ='3' AND c.embel_name=4 THEN c.production_quantity ELSE 0 END) AS spreceived_qnty,
					sum(CASE WHEN c.production_source=1 and c.production_type ='4' THEN c.production_quantity ELSE 0 END) AS sewingin_inhouse_qnty,
					sum(CASE WHEN c.production_source=3 and c.production_type ='4' THEN c.production_quantity ELSE 0 END) AS sewingin_outbound_qnty,
					sum(CASE WHEN c.production_source=1 and c.production_type ='5' THEN c.production_quantity ELSE 0 END) AS sewingout_inhouse_qnty,
					sum(CASE WHEN c.production_source=3 and c.production_type ='5' THEN c.production_quantity ELSE 0 END) AS sewingout_outbound_qnty,
					sum(CASE WHEN c.production_source=1 and c.production_type ='7' THEN c.production_quantity ELSE 0 END) AS iron_in_qnty,
					sum(CASE WHEN c.production_source=3 and c.production_type ='7' THEN c.production_quantity ELSE 0 END) AS iron_out_qnty,
					sum(CASE WHEN c.production_type ='7' THEN c.production_quantity ELSE 0 END) AS iron_qnty,
					sum(CASE WHEN c.production_type ='7' THEN c.re_production_qty ELSE 0 END) AS re_iron_qnty,
					sum(CASE WHEN c.production_type ='8' THEN c.production_quantity ELSE 0 END) AS finish_qnty,
					sum(c.carton_qty) as carton_qty 
								from 
									wo_po_break_down a,wo_po_details_master b, pro_garments_production_mst c 
								where 
									a.job_id=b.id and a.id=c.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $txt_date $company_name $buyer_name $garments_nature $floor_name $location_cond     
									group by c.po_break_down_id,c.production_date,c.item_number_id");
									//echo $sql_order;die;
					foreach(sql_select($sql_order) as $sql_result)
					{
						$cutting_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]]['1']=$sql_result[csf("cutting_qnty")];
						
						$printing_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]]['2']=$sql_result[csf("printing_qnty")];
						$printreceived_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]]['3']=$sql_result[csf("printreceived_qnty")];
						
						$emb_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]]['2']=$sql_result[csf("emb_qnty")];
						$embreceived_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]]['3']=$sql_result[csf("embreceived_qnty")];
						
						$wash_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]]['2']=$sql_result[csf("wash_qnty")];
						$washreceived_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]]['3']=$sql_result[csf("washreceived_qnty")];
						
						$sp_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]]['2']=$sql_result[csf("sp_qnty")];
						$spreceived_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]]['3']=$sql_result[csf("spreceived_qnty")];
						
						$sewingin_inhouse_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]]['4']=$sql_result[csf("sewingin_inhouse_qnty")];
						$sewingin_outbound_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]]['4']=$sql_result[csf("sewingin_outbound_qnty")];
						$sewingout_inhouse_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]]['5']=$sql_result[csf("sewingout_inhouse_qnty")];
						$sewingout_outbound_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]]['5']=$sql_result[csf("sewingout_outbound_qnty")];
						$iron_in_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]]['7']=$sql_result[csf("iron_in_qnty")];
						$iron_out_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]]['7']=$sql_result[csf("iron_out_qnty")];
						$iron_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]]['7']=$sql_result[csf("iron_qnty")];
						$re_iron_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]]['7']=$sql_result[csf("re_iron_qnty")];
						$finish_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]]['8']=$sql_result[csf("finish_qnty")];
						$carton_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]]=$sql_result[csf("carton_qty")];
						
					}
					
					//echo "<pre>";print_r($finish_array);die;
					/*$smv_qty_arr=array();
					$sql_smv="select job_no, gmts_item_id,smv_pcs from wo_po_details_mas_set_details";
					$data_smv_array=sql_select($sql_smv);
					foreach($data_smv_array as $row)
					{
						$smv_qty_arr[$row[csf("job_no")]][$row[csf("gmts_item_id")]]=$row[csf("smv_pcs")];
					}*/
		
                    $total_cut=0;
                    $total_print_iss=0;
                    $total_print_re=0;
					$total_emb_iss=0;
                    $total_emb_re=0;
					$total_wash_iss=0;
                    $total_wash_re=0;
					$total_sp_iss=0;
                    $total_sp_re=0;
					$total_sew_inhouse_in=0; 
					$total_sew_outbound_in=0;
                    $total_sew_input=0;
					$total_sew_inhouse_out=0; 
					$total_sew_outbound_out=0;
                    $total_sew_out=0;
					$total_in_iron=0;
					$total_out_iron=0;
					$total_iron=0;
					$total_re_iron=0;
                    $total_finish=0;
                    $total_carton=0;
                    $total_prod_dzn=0;
                    $total_cm_value=0;
					$total_cm_cost=0;
                    
                    $i=1;
					
					
				
					if($db_type==0)
					{
						$pro_date_sql=sql_select("SELECT b.job_no_prefix_num, a.job_no_mst, a.po_number,a.grouping,a.file_no,a.unit_price, a.po_quantity, b.total_set_qnty as ratio, b.job_no_prefix_num, b.order_uom, b.buyer_name, b.style_ref_no as style, b.company_name as company_name, c.po_break_down_id, c.item_number_id, c.production_source, c.serving_company, c.location, c.embel_name, c.embel_type, c.production_date,c.production_quantity,c.production_type,c.entry_break_down_type,TIME_FORMAT(c.production_hour, '%H:%i' ) as production_hour,c.sewing_line,c.supervisor,c.remarks,c.floor_id,c.alter_qnty,c.reject_qnty 
					from 
						wo_po_break_down a,wo_po_details_master b, pro_garments_production_mst c 
					where 
						a.job_no_mst=b.job_no and a.id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $txt_date $company_name $buyer_name $floor_name $location_cond  $garments_nature $file_no_cond $internal_ref_cond  group by c.po_break_down_id, c.production_date, c.item_number_id order by c.production_date");
						
						
					}
					else
					{
						
						 $pro_date_sql=sql_select("SELECT b.job_no_prefix_num, a.job_no_mst, a.po_number,a.grouping,a.file_no,a.unit_price, b.buyer_name, b.style_ref_no as style, b.company_name as company_name,  c.po_break_down_id, b.total_set_qnty as ratio, c.item_number_id, c.production_date
					from 
						wo_po_break_down a,wo_po_details_master b, pro_garments_production_mst c 
					where 
						a.job_no_mst=b.job_no and a.id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $txt_date $company_name $buyer_name $floor_name $location_cond  $garments_nature $file_no_cond $internal_ref_cond    
						group by c.po_break_down_id, b.job_no_prefix_num, a.job_no_mst, a.po_number,a.grouping,a.file_no, b.buyer_name, b.style_ref_no,  b.total_set_qnty, b.company_name, c.item_number_id, c.production_date,a.unit_price 
						order by c.production_date");
						
					}
							
					foreach($pro_date_sql as $pro_date_sql_row)
                    {
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						 $loaction_id=$prod_arr[$pro_date_sql_row[csf('po_break_down_id')]][$pro_date_sql_row[csf('production_date')]][$pro_date_sql_row[csf('item_number_id')]]['location'];
						$cutting_hour=$prod_arr[$pro_date_sql_row[csf('po_break_down_id')]][$pro_date_sql_row[csf('production_date')]][$pro_date_sql_row[csf('item_number_id')]]['1'];
						$iron_hour=$prod_arr[$pro_date_sql_row[csf('po_break_down_id')]][$pro_date_sql_row[csf('production_date')]][$pro_date_sql_row[csf('item_number_id')]]['7'];
						$re_iron_hour=$prod_arr[$pro_date_sql_row[csf('po_break_down_id')]][$pro_date_sql_row[csf('production_date')]][$pro_date_sql_row[csf('item_number_id')]]['7'];
						$finish_hr=$prod_arr[$pro_date_sql_row[csf('po_break_down_id')]][$pro_date_sql_row[csf('production_date')]][$pro_date_sql_row[csf('item_number_id')]]['8'];
						
						
						$off_days_check=$off_day_check[$pro_date_sql_row[csf('production_date')]][$loaction_id]['day'];
						
						if($off_days_check!=2)
						{
						//$item_smv=$smv_qty_arr[$pro_date_sql_row[csf("job_no_mst")]][$pro_date_sql_row[csf("item_number_id")]];
						 $sent_to_print_qty=$printing_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['2'];
						  $rev_print_qty=$printreceived_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['3']; 
						   $sentto_emb_qty=$emb_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['2'];  
							$rev_emb_qty=$embreceived_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['3']; 
							$ironin_qty=$iron_in_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['7']; 
							$carton_qty=$carton_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]; 
							$sewingin_inhouse=$sewingin_inhouse_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['4']; 
							$sentto_wash_qty=$wash_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['2']; 
							$rev_wash_qty=$washreceived_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['3']; 
							$sentto_sp_works=$sp_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['2'];  
							$sp_rev_qty=$spreceived_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['3'];
							$sewingout_inhouse=$sewingout_inhouse_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['5']; 
							  
						$time_valid="19:00";
						if($time_valid>=$cutting_hour)
						{
							$cutting_qty=$cutting_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['1'];
						}
						else
						{
							 $cutting_qty=0;
						}
						if($time_valid>=$iron_hour)
						{
							 $tot_iron=$cutting_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['7'];
						}
						else
						{
							  $tot_iron=0;
						}
						if($time_valid>=$re_iron_hour)
						{
							 $re_iron_qty=$cutting_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['7'];
						}
						else
						{
							  $re_iron_qty=0;
						}
						if($time_valid>=$finish_hr)
						{
							 $fqty=$finish_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['8'];
						}
						else
						{
							  $fqty=0;
						}
						
                    ?>
                    	<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                            <td width="40"><? echo $i;?></td>
                            <td width="80"><p><? echo $company_short_library[$pro_date_sql_row[csf("company_name")]]; ?></p></td>
                            <td width="65" align="center"><p><? echo $pro_date_sql_row[csf("job_no_prefix_num")];?></p></td>
                            <td width="55" align="center"><p><? echo $pro_date_sql_row[csf("year")]; ?></p></td>
                            <td width="130"><p><a href="##" onClick="openmypage_order(<? echo $pro_date_sql_row[csf("po_break_down_id")];?>,<? echo $pro_date_sql_row[csf("item_number_id")];?>,'orderQnty_popup');" ><? echo $pro_date_sql_row[csf("po_number")]; ?></a></p></td>
                            <td width="100" align="center"><p><? echo $pro_date_sql_row[csf("unit_price")]; ?></p></td>
                            <td width="70" align="center"><p><? echo $job_item_smv_arr[$pro_date_sql_row[csf('po_break_down_id')]][$pro_date_sql_row[csf('item_number_id')]]['smv_pcs']; ?></p></td>
                            <td width="70" align="center"><p><? echo $job_item_smv_arr[$pro_date_sql_row[csf('po_break_down_id')]][$pro_date_sql_row[csf('item_number_id')]]['finsmv_pcs']; ?></p></td>
                            <td width="70"><p><? echo $buyer_short_library[$pro_date_sql_row[csf("buyer_name")]]; ?></p></td>
                            <td width="140"><p><? echo $pro_date_sql_row[csf("style")]; ?></p></td>
                            <td width="100" align="center"><p><? echo $pro_date_sql_row[csf("file_no")]; ?></p></td>
                            <td width="100" align="center"><p><? echo $pro_date_sql_row[csf("grouping")]; ?></p></td>
                            <td width="150"><p><? echo $garments_item[$pro_date_sql_row[csf("item_number_id")]]; ?></p></td>
                            <td width="100" align="center"><p><? echo change_date_format($pro_date_sql_row[csf("production_date")]); ?></p></td>
                            <td width="80" align="right"><a href="##" onClick="openmypage_popup(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,'Cutting Info','cutting_popup');" >
								<?
									echo  $cutting_qty;
									$total_cut+=$cutting_qty;//$cutting_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['1'];
								?>
                                </a>
                            </td>
                            <td width="80" align="right"><a href="##" onClick="openmypage_popup(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,'Printing Issue Info','printing_issue_popup');" >
								<?
									//echo $printing_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['2'];  
									echo $sent_to_print_qty;
									$total_print_iss+=$sent_to_print;//$printing_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['2'];
								?>
                                </a>
							</td>
                            <td width="80" align="right"><a href="##" onClick="openmypage_popup(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,'Priniting Receive Info','printing_receive_popup');" >
                            	<?
									echo $rev_print_qty;//$printreceived_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['3'];  
									$total_print_re+=$rev_print_qty;//$printreceived_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['3']; 
								?>
                                </a>
                            </td>
                            <td width="80" align="right"><a href="##" onClick="openmypage_popup(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,'Embroidery Issue Info','embroi_issue_popup');" >
								<?
									echo  $sentto_emb_qty;//$emb_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['2'];  
									$total_emb_iss+=$sentto_emb_qty;//$emb_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['2'];
								?>
                                </a>
							</td>
                            <td width="80" align="right"><a href="##" onClick="openmypage_popup(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,'Embroidery Receive Info','embroi_receive_popup');" >
                            	<?
									echo $rev_emb_qty;//$embreceived_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['3'];  
									$total_emb_re+=$rev_emb_qty;//$embreceived_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['3']; 
								?>
                                </a>
                            </td>
                            <td width="80" align="right"><a href="##" onClick="openmypage_popup(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,'Wash Issue Info','wash_issue_popup');" >
								<?
									echo $sentto_wash_qty;//$wash_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['2'];  
									$total_wash_iss+=$sentto_wash_qty;//$wash_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['2'];
								?>
                                </a>
							</td>
                            <td width="80" align="right"><a href="##" onClick="openmypage_popup(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,'Wash Receive Info','wash_receive_popup');" >
                            	<?
									echo $rev_wash_qty;
									//echo $washreceived_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['3'];  
									$total_wash_re+=$rev_wash_qty;//$washreceived_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['3']; 
								?>
                                </a>
                            </td>
                            <td width="80" align="right"><a href="##" onClick="openmypage_popup(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,'Special Works Issue Info','sp_issue_popup');" >
								<?
									echo $sentto_sp_works;//$sp_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['2'];  
									$total_sp_iss+=$sentto_sp_works;//$sp_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['2'];
								?>
                                </a>
							</td>
                            <td width="80" align="right"><a href="##" onClick="openmypage_popup(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,'Special Works Receive Info','sp_receive_popup');" >
                            	<? 
									echo $sp_rev_qty;
									//echo $spreceived_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['3'];  
									$total_sp_re+=$sp_rev_qty;//$spreceived_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['3']; 
								?>
                                </a>
                            </td>
                            <td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,'1','4','sewingQnty_popup');" >
                            	<?
									//$sewingin_inhouse=$sewingin_inhouse_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['4'];
									echo $sewingin_inhouse; $total_sew_inhouse_in+=$sewingin_inhouse;  
								?>
                            </a>
                            </td>
                            <td width="80" align="right">
                            
                            <?
							$sewingin_outbound=0;
                           // $sewingin_outbound=$sewingin_outbound_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['4'];
									echo $sewingin_outbound; $total_sew_outbound_in+=$sewingin_outbound;  
							?>
                            </td>
                            <td width="80" align="right"><? $sewing_input_total=$sewingin_inhouse+$sewingin_outbound; echo $sewing_input_total; $total_sew_in+=$sewing_input_total; ?>
                            </td>
                            <td width="80" align="right">
                            
                            <?
							echo $sewingout_inhouse;
							//$sewingout_inhouse=0;
                            $total_sew_inhouse_out+=$sewingout_inhouse;  
							?>
                            </td>
                            <td width="80" align="right">
                            
                            <?
                            $sewingout_outbound=0;
									echo $sewingout_outbound; $total_sew_outbound_out+=$sewingout_outbound;  
							?>
                            </td>
                            <td width="80" align="right"><? $sewing_output_total=$sewingout_inhouse+$sewingout_outbound; echo $sewing_output_total;  $total_sew_out+=$sewing_output_total;  ?>
                            </td>
                            <td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,'1','0','ironQnty_popup');" >
                            	<?
									echo $ironin_qty;//$iron_in_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['7'];  
									$total_in_iron+=$ironin_qty;//$iron_in_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['7'];  
								?>
                                </a>
                            </td>
                            <td width="80" align="right">
                            
                            <?
                            $out_iron=0; 
							$total_out_iron+=$out_iron;  
							?>
                            </td>
                            <td width="80" align="right">
                            	<?
									echo $tot_iron;
									//echo $iron_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['7'];  
									$total_iron+=$tot_iron;//$iron_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['7'];  
								?>
                            </td>
                            <td width="80" align="right">
                            	<?
									$iron_smv=($tot_iron*$job_item_smv_arr[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("item_number_id")]]['finsmv_pcs']);
									echo $iron_smv;  
									$total_iron_smv+=$iron_smv;  
								?>
                            </td>
                            <td width="80" align="right">
                            	<?
									echo $re_iron_qty;
									//echo $re_iron_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['7'];  
									$total_re_iron+=$re_iron_qty; 
								?>
                            </td>
                            <td width="80" align="right">
                            	<?
									echo $fqty;//=$finish_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['8'];  
									$total_finish+=$fqty;//$finish_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['8'];  
								?>
                            </td>
                            <td width="80" align="right">
                            	<?
								echo $carton_qty;
									//echo $carton_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]];  
									$total_carton+=$carton_qty;//$carton_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]; 
								?>
                            </td>
                            
                            <?
							
                           // $cm_per_dzn=return_field_value("cm_for_sipment_sche","wo_pre_cost_dtls","job_no='".$pro_date_sql_row[csf("job_no_mst")]."' and is_deleted=0 and status_active=1");
						   //$sewingout_inhouse+$sewingout_outbound;
						   $in_prod_dzn=$sewingout_inhouse/ 12 ; $total_in_prod_dzn+=$in_prod_dzn;
						   $out_prod_dzn=$sewingout_outbound/ 12 ; $total_out_prod_dzn+=$out_prod_dzn;
						   $prod_dzn=$sewing_output_total/ 12 ; $total_prod_dzn+=$prod_dzn;
						   
							$dzn_qnty=0; $in_cm_value=0; $out_cm_value=0; $cm_value=0; $in_cm_cost=0; $out_cm_cost=0; $cm_cost=0;
							if($costing_per_id_library[$pro_date_sql_row[csf('job_no_mst')]]==1) $dzn_qnty=12;
							else if($costing_per_id_library[$pro_date_sql_row[csf('job_no_mst')]]==3) $dzn_qnty=12*2;
							else if($costing_per_id_library[$pro_date_sql_row[csf('job_no_mst')]]==4) $dzn_qnty=12*3;
							else if($costing_per_id_library[$pro_date_sql_row[csf('job_no_mst')]]==5) $dzn_qnty=12*4;
							else $dzn_qnty=1;
							//echo $dzn_qnty."***";
							
							$cm_cost_margin=($tot_cm_cost_arr[$pro_date_sql_row[csf('job_no_mst')]]/$dzn_qnty)+$tot_margin_pcs_arr[$pro_date_sql_row[csf('job_no_mst')]];
							
							
							$dzn_qnty=$dzn_qnty*$pro_date_sql_row[csf('ratio')];
							
							$in_cm_value=($tot_cost_arr[$pro_date_sql_row[csf('job_no_mst')]]/$dzn_qnty)*$sewingout_inhouse;
							$total_in_cm_value+=$in_cm_value;
							
							$out_cm_value=($tot_cost_arr[$pro_date_sql_row[csf('job_no_mst')]]/$dzn_qnty)*$sewingout_outbound;
							$total_out_cm_value+=$out_cm_value;
							
							$cm_value=($tot_cost_arr[$pro_date_sql_row[csf('job_no_mst')]]/$dzn_qnty)*$sewing_output_total;
							$total_cm_value+=$cm_value;
							
							$in_cm_cost=($tot_cm_cost_arr[$pro_date_sql_row[csf('job_no_mst')]]/$dzn_qnty)*$sewingout_inhouse;
							$total_in_cm_cost+=$in_cm_cost;
							
							$out_cm_cost=($tot_cm_cost_arr[$pro_date_sql_row[csf('job_no_mst')]]/$dzn_qnty)*$sewingout_outbound;
							$total_out_cm_cost+=$out_cm_cost;
							
							$cm_cost=($tot_cm_cost_arr[$pro_date_sql_row[csf('job_no_mst')]]/$dzn_qnty)*$sewing_output_total;
							$total_cm_cost+=$cm_cost;
                            ?>
                            
                            <td width="80" align="right"><? if($in_prod_dzn!=0) echo number_format($in_prod_dzn,2); else echo "0"; ?></td>
                            <td width="80" align="right"><? if($out_prod_dzn!=0) echo number_format($out_prod_dzn,2); else echo "0"; ?></td>
                            <td width="80" align="right"><? if($prod_dzn!=0) echo number_format($prod_dzn,2); else echo "0"; ?></td>
                            <td width="100" align="right" ><? echo number_format($in_cm_value,2,'.',''); ?></td>
                            <td width="100" align="right" ><? echo number_format($out_cm_value,2,'.',''); ?></td>
                            <td width="100" align="right" title="(Budget Wise CM For Shipment Schedule/Dzn Qnty)*Total Sewing Out"><? echo number_format($cm_value,2,'.',''); ?></td>
                            <td width="100" align="right" title="Job Wise Unit Price*Total Sewing Out"><? echo number_format($pro_date_sql_row[csf("unit_price")]*$sewing_output_total,2,'.',''); ?></td>
                            <td width="100" align="right" ><? echo number_format($in_cm_cost,2,'.',''); ?></td>
                            <td width="100" align="right" ><? echo number_format($out_cm_cost,2,'.',''); ?></td>
                            <td width="100" align="right" title="(Budget Wise CM Cost/Dzn Qnty)*Total Sewing Out"><? echo number_format($cm_cost,2,'.',''); ?></td>
                            <td width="100" align="right" title="(Finish Qnty*(Budge Wise CM Cost/Dzn Qnty)+Budge Wise Margin Pcs Per Set)"><? echo number_format($fqty*$cm_cost_margin,2,'.',''); ?></td>
                            <td width="80">
                            	<a href="##"  onclick="openmypage_remark(<? echo $pro_date_sql_row[csf("po_break_down_id")];?>,'date_wise_production_report');" > Veiw </a>
                            </td>
                   	 </tr>
					<?	
					$all_order_sewQty_array[$pro_date_sql_row[csf("po_break_down_id")]]+=$sewing_output_total;
					$i++;
				  } //Off day check end
					
				}//end foreach 1st
				
				?>
                </table> 
                <table width="4220" cellspacing="0" border="1" class="rpt_table" rules="all" id="report_table_footer" >
                    <tfoot>
                            <th width="40" align="right"></th>
                            <th width="80" align="right"></th>
                            <th width="65" align="right"></th>
                            <th width="55" align="right"></th>
                            <th width="130" align="right"></th>
                            <th width="100" align="right"></th>
                            <th width="70" align="right"></th>
                            <th width="70" align="right"></th>
                            <th width="70" align="right"></th>
                            <th width="140" align="right"></th>
                            <th width="100" align="right"></th>
                            <th width="100" align="right"></th>
                            <th width="150" align="right"></th>
                            <th width="100" align="right">Total</th> 
                            <th width="80" align="right" id="total_cut_td" ><? echo $total_cut;?></th> 
                            <th width="80" align="right" id="total_printissue_td"><? echo $total_print_iss; ?> </th> 
                            <th width="80" align="right" id="total_printrcv_td"><?  echo $total_print_re;  ?>  </th>
                            <th width="80" align="right" id="total_emb_iss"><? echo $total_emb_iss; ?> </th> 
                            <th width="80" align="right" id="total_emb_re"><?  echo $total_emb_re;  ?>  </th>
                            <th width="80" align="right" id="total_wash_iss"><? echo $total_wash_iss; ?> </th> 
                            <th width="80" align="right" id="total_wash_re"><?  echo $total_wash_re;  ?>  </th>
                            <th width="80" align="right" id="total_sp_iss"><? echo $total_sp_iss; ?> </th> 
                            <th width="80" align="right" id="total_sp_re"><?  echo $total_sp_re;  ?>  </th>
                            <th width="80" align="right" id="total_sewin_inhouse_td"><? echo $total_sew_inhouse_in; ?></th>
                            <th width="80" align="right" id="total_sewin_outbound_td"><? echo $total_sew_outbound_in; ?></th>
                            <th width="80" align="right" id="total_sewin_td"><? echo $total_sew_input; ?></th> 
                            <th width="80" align="right" id="total_sewout_inhouse_td"><? echo $total_sew_inhouse_out; ?></th>
                            <th width="80" align="right" id="total_sewout_outbound_td"><? echo $total_sew_outbound_out; ?></th>
                            <th width="80" align="right" id="total_sewout_td"><? echo $total_sew_out; ?></th>
                            <th width="80" align="right" id="total_in_iron_td"><?  echo $total_in_iron; ?> </th>
                            <th width="80" align="right" id="total_out_iron_td"><?  echo $total_out_iron; ?> </th>
                            <th width="80" align="right" id="total_iron_td"><?  echo $total_iron; ?> </th> 
                            <th width="80" align="right" id="total_iron_smv_td"><?  echo $total_iron_smv; ?> </th>
                            <th width="80" align="right" id="total_re_iron_td"><?  echo $total_re_iron; ?> </th>
                            <th width="80" align="right" id="total_finish_td"><?  echo $total_finish; ?>  </th>   
                            <th width="80" align="right" id="total_carton_td"><? echo $total_carton; ?> </th> 
                            <th width="80" align="right" id="value_total_in_prod_dzn_td"><?  echo number_format($total_in_prod_dzn,2); ?> </th>
                            <th width="80" align="right" id="value_total_out_prod_dzn_td"><?  echo number_format($total_out_prod_dzn,2); ?> </th>
                            <th width="80" align="right" id="value_total_prod_dzn_td"><?  echo number_format($total_prod_dzn,2); ?> </th>
                            <th width="100" align="right" id="value_total_in_cm_value_td"><? echo number_format($total_in_cm_value,2); ?>  </th >
                            <th width="100" align="right" id="value_total_out_cm_value_td"><? echo number_format($total_out_cm_value,2); ?>  </th >
                            <th width="100" align="right" id="value_total_cm_value_td"><? echo number_format($total_cm_value,2); ?>  </th >
                            <th width="100" align="right"></th >
                            <th width="100" align="right" id="value_total_in_cm_cost"><? echo number_format($total_in_cm_cost,2); ?> </th >
                            <th width="100" align="right" id="value_total_out_cm_cost"><? echo number_format($total_out_cm_cost,2); ?> </th >
                            <th width="100" align="right" id="value_total_cm_cost"><? echo number_format($total_cm_cost,2); ?> </th >
                            <th width="100">&nbsp;</th>
                            <th width="80">&nbsp;</th>
                       </tfoot>
                	</table>
           		</div>     
  			</div>
<?
		}// end if condition of type
		
		//-------------------------------------------END Show Date Wise------------------------
		//-------------------------------------------Show Date Location Floor & Line Wise------------------------	
		if($type==2)
		{
			ob_start();
		?>
             <div> 
                <table width="3550"  cellspacing="0">
                    <tr class="form_caption" style="border:none;">
                            <td colspan="41" align="center" style="border:none;font-size:14px; font-weight:bold"> Date Location Floor & Line Wise Production Report</td>
                     </tr>
                    <tr style="border:none;">
                            <td colspan="41" align="center" style="border:none; font-size:16px; font-weight:bold">
                                Company Name:<? echo $company_library[str_replace("'","",$cbo_company_name)]; ?>                                
                            </td>
                      </tr>
                      <tr style="border:none;">
                            <td colspan="41" align="center" style="border:none;font-size:12px; font-weight:bold">
                            	<? echo "From $fromDate To $toDate" ;?>
                            </td>
                      </tr>
                </table>
                <table width="4300" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1">
                   <thead>
                        <th width="40">SL</th>    
                        <th width="80">Working Factory</th>
                        <th width="65">Job No</th>
                        <th width="55">Year</th>
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
                        <th width="80">Cutting</th>
                        <th width="80">Sent to Print</th>
                        <th width="80">Rev Print</th>
                        <th width="80">Sent to Emb</th>
                        <th width="80">Rev Emb</th>
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
                <div style="max-height:425px; overflow-y:scroll; width:4318px" id="scroll_body">
                <table cellspacing="0" border="1" class="rpt_table"  width="4300" rules="all" id="table_body" >
                <?
                $cutting_array=array();
                $printing_array=array();
                $printreceived_array=array();
                $sewingin_inhouse_array=array();
                $sewingin_outbound_array=array();
                $sewingin_array=array();
                $sewingout_inhouse_array=array();
                $sewingout_outbound_array=array();
                $sewingout_array=array();
                $iron_in_array=array();
                $iron_out_array=array();
                $iron_array=array();
                $re_iron_array=array();
                $finish_array=array();
                $carton_array=array();
                
                $sql_order=sql_select("SELECT c.po_break_down_id,c.production_date,c.location,c.floor_id,c.sewing_line, c.item_number_id,
                sum(CASE WHEN c.production_type ='1' THEN c.production_quantity ELSE 0 END) AS cutting_qnty,
                sum(CASE WHEN c.production_type ='2' AND c.embel_name=1 THEN c.production_quantity ELSE 0 END) AS printing_qnty,
                sum(CASE WHEN c.production_type ='3' AND c.embel_name=1 THEN c.production_quantity ELSE 0 END) AS printreceived_qnty,
                sum(CASE WHEN c.production_type ='2' AND c.embel_name=2 THEN c.production_quantity ELSE 0 END) AS emb_qnty,
                sum(CASE WHEN c.production_type ='3' AND c.embel_name=2 THEN c.production_quantity ELSE 0 END) AS embreceived_qnty,
                sum(CASE WHEN c.production_type ='2' AND c.embel_name=3 THEN c.production_quantity ELSE 0 END) AS wash_qnty,
                sum(CASE WHEN c.production_type ='3' AND c.embel_name=3 THEN c.production_quantity ELSE 0 END) AS washreceived_qnty,
                sum(CASE WHEN c.production_type ='2' AND c.embel_name=4 THEN c.production_quantity ELSE 0 END) AS sp_qnty,
                sum(CASE WHEN c.production_type ='3' AND c.embel_name=4 THEN c.production_quantity ELSE 0 END) AS spreceived_qnty,
                sum(CASE WHEN c.production_type ='4' THEN c.production_quantity ELSE 0 END) AS sewingin_qnty,
                sum(CASE WHEN c.production_source=1 and c.production_type ='4' THEN c.production_quantity ELSE 0 END) AS sewingin_inhouse_qnty,
                sum(CASE WHEN c.production_source=3 and c.production_type ='4' THEN c.production_quantity ELSE 0 END) AS sewingin_outbound_qnty,
                sum(CASE WHEN c.production_type ='5' THEN c.production_quantity ELSE 0 END) AS sewingout_qnty,
                sum(CASE WHEN c.production_source=1 and c.production_type ='5' THEN c.production_quantity ELSE 0 END) AS sewingout_inhouse_qnty,
                sum(CASE WHEN c.production_source=3 and c.production_type ='5' THEN c.production_quantity ELSE 0 END) AS sewingout_outbound_qnty,
                
                sum(CASE WHEN c.production_source=1 and c.production_type ='7' THEN c.production_quantity ELSE 0 END) AS iron_in_qnty,
                sum(CASE WHEN c.production_source=3 and c.production_type ='7' THEN c.production_quantity ELSE 0 END) AS iron_out_qnty,
                
                sum(CASE WHEN c.production_type ='7' THEN c.production_quantity ELSE 0 END) AS iron_qnty, 
                sum(CASE WHEN c.production_type ='7' THEN c.re_production_qty ELSE 0 END) AS re_iron_qnty, 
                sum(CASE WHEN c.production_type ='8' THEN c.production_quantity ELSE 0 END) AS finish_qnty, 
                sum(c.carton_qty) as carton_qty
                            from 
                                wo_po_break_down a,wo_po_details_master b, pro_garments_production_mst c 
                            where 
                                a.job_no_mst=b.job_no and a.id=c.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $txt_date $company_name $buyer_name $garments_nature $floor_name $location_cond 
                            group by c.po_break_down_id,c.production_date, c.item_number_id,c.location,c.floor_id,c.sewing_line");
                foreach($sql_order as $sql_result)
                {
                    $cutting_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]][$sql_result[csf("location")]][$sql_result[csf("floor_id")]][$sql_result[csf("sewing_line")]]['1']=$sql_result[csf("cutting_qnty")];
                    
                    $printing_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]][$sql_result[csf("location")]][$sql_result[csf("floor_id")]][$sql_result[csf("sewing_line")]]['2']=$sql_result[csf("printing_qnty")];
                    $printreceived_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]][$sql_result[csf("location")]][$sql_result[csf("floor_id")]][$sql_result[csf("sewing_line")]]['3']=$sql_result[csf("printreceived_qnty")];
                    
                    $emb_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]][$sql_result[csf("location")]][$sql_result[csf("floor_id")]][$sql_result[csf("sewing_line")]]['2']=$sql_result[csf("emb_qnty")];
                    $embreceived_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]][$sql_result[csf("location")]][$sql_result[csf("floor_id")]][$sql_result[csf("sewing_line")]]['3']=$sql_result[csf("embreceived_qnty")];
                    
                    $wash_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]][$sql_result[csf("location")]][$sql_result[csf("floor_id")]][$sql_result[csf("sewing_line")]]['2']=$sql_result[csf("wash_qnty")];
                    $washreceived_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]][$sql_result[csf("location")]][$sql_result[csf("floor_id")]][$sql_result[csf("sewing_line")]]['3']=$sql_result[csf("washreceived_qnty")];
                    
                    $sp_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]][$sql_result[csf("location")]][$sql_result[csf("floor_id")]][$sql_result[csf("sewing_line")]]['2']=$sql_result[csf("sp_qnty")];
                    $spreceived_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]][$sql_result[csf("location")]][$sql_result[csf("floor_id")]][$sql_result[csf("sewing_line")]]['3']=$sql_result[csf("spreceived_qnty")];
                    
                    $sewingin_inhouse_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]][$sql_result[csf("location")]][$sql_result[csf("floor_id")]][$sql_result[csf("sewing_line")]]['4']=$sql_result[csf("sewingin_inhouse_qnty")];
                    $sewingin_outbound_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]][$sql_result[csf("location")]][$sql_result[csf("floor_id")]][$sql_result[csf("sewing_line")]]['4']=$sql_result[csf("sewingin_outbound_qnty")];
                    $sewingin_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]][$sql_result[csf("location")]][$sql_result[csf("floor_id")]][$sql_result[csf("sewing_line")]]['4']=$sql_result[csf("sewingin_qnty")];
                    $sewingout_inhouse_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]][$sql_result[csf("location")]][$sql_result[csf("floor_id")]][$sql_result[csf("sewing_line")]]['5']=$sql_result[csf("sewingout_inhouse_qnty")];
                    $sewingout_outbound_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]][$sql_result[csf("location")]][$sql_result[csf("floor_id")]][$sql_result[csf("sewing_line")]]['5']=$sql_result[csf("sewingout_outbound_qnty")];
                    $sewingout_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]][$sql_result[csf("location")]][$sql_result[csf("floor_id")]][$sql_result[csf("sewing_line")]]['5']=$sql_result[csf("sewingout_qnty")];
                    $iron_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]][$sql_result[csf("location")]][$sql_result[csf("floor_id")]][$sql_result[csf("sewing_line")]]['7']=$sql_result[csf("iron_qnty")];
                    
                    $iron_smv_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]][$sql_result[csf("location")]][$sql_result[csf("floor_id")]][$sql_result[csf("sewing_line")]]['7']=$sql_result[csf("iron_qnty")]*$job_item_smv_arr[$sql_result[csf("po_break_down_id")]][$sql_result[csf("item_number_id")]]["finsmv_pcs"];
                    
                    $iron_in_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]][$sql_result[csf("location")]][$sql_result[csf("floor_id")]][$sql_result[csf("sewing_line")]]['7']=$sql_result[csf("iron_in_qnty")];
                    $iron_out_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]][$sql_result[csf("location")]][$sql_result[csf("floor_id")]][$sql_result[csf("sewing_line")]]['7']=$sql_result[csf("iron_out_qnty")];
                    
                    $re_iron_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]][$sql_result[csf("location")]][$sql_result[csf("floor_id")]][$sql_result[csf("sewing_line")]]['7']=$sql_result[csf("re_iron_qnty")];
                    $finish_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]][$sql_result[csf("location")]][$sql_result[csf("floor_id")]][$sql_result[csf("sewing_line")]]['8']=$sql_result[csf("finish_qnty")];
                    $carton_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]][$sql_result[csf("location")]][$sql_result[csf("floor_id")]][$sql_result[csf("sewing_line")]]=$sql_result[csf("carton_qty")];
                    
                }
                //print_r($cutting_array);
                /*$smv_qty_arr=array();
                $sql_smv="select job_no, gmts_item_id,smv_pcs from wo_po_details_mas_set_details";
                $data_smv_array=sql_select($sql_smv);
                foreach($data_smv_array as $row)
                {
                      $smv_qty_arr[$row[csf("job_no")]][$row[csf("gmts_item_id")]]=$row[csf("smv_pcs")];
                }*/
                
                $total_cut=0;
                $total_print_iss=0;
                $total_print_re=0;
                $total_sew_inhouse_in=0; 
                $total_sew_outbound_in=0; 
                $total_sew_input=0;  
                $total_sew_inhouse_out=0; 
                $total_sew_outbound_out=0; 
                $total_sew_out=0;
                $total_in_iron=0;
                $total_out_iron=0;
                $total_iron=0;
                $total_re_iron=0;
                $total_finish=0;
                $total_carton=0;
                $total_prod_dzn=0;
                $total_cm_value=0;
                $total_cm_cost=0;
                
                $i=1;
                
                if($garments_nature!="") $garments_nature=" and c.garments_nature=$garments_nature";
                
                    
                if($db_type==0)
                {
                    $pro_date_sql=sql_select("SELECT a.job_no_mst, a.po_number, a.po_quantity,a.file_no, a.grouping, b.total_set_qnty as ratio, b.job_no_prefix_num, YEAR(b.insert_date) as year, b.order_uom, b.buyer_name, b.style_ref_no as style, b.company_name as company_name, c.po_break_down_id, c.item_number_id, c.production_source, c.serving_company, c.location, c.embel_name, c.embel_type, c.production_date, c.production_quantity, c.production_type, c.entry_break_down_type, c.production_hour, c.sewing_line, c.supervisor, c.carton_qty, c.remarks, c.floor_id, c.alter_qnty, c.reject_qnty, c.prod_reso_allo
                from 
                    wo_po_break_down a,wo_po_details_master b, pro_garments_production_mst c 
                where 
                    a.job_no_mst=b.job_no and a.id=c.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $txt_date $company_name $buyer_name $garments_nature $floor_name $location_cond  $file_no_cond $internal_ref_cond group by c.po_break_down_id, c.production_date, c.location, c.floor_id, c.sewing_line order by c.production_date");
                }
                else
                {
                    $pro_date_sql=sql_select("SELECT a.job_no_mst, a.po_number,a.file_no, a.grouping, b.total_set_qnty as ratio, b.job_no_prefix_num, to_char(b.insert_date,'YYYY') as year, b.buyer_name, b.style_ref_no as style, b.company_name as company_name, c.po_break_down_id, c.item_number_id, c.production_source, c.location, c.production_date, c.sewing_line, c.floor_id, c.prod_reso_allo
                from 
                    wo_po_break_down a,wo_po_details_master b, pro_garments_production_mst c 
                where 
                    a.job_no_mst=b.job_no and a.id=c.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $txt_date $company_name $buyer_name $garments_nature $floor_name $location_cond  $file_no_cond $internal_ref_cond group by a.job_no_mst, a.po_number,a.file_no, a.grouping, b.total_set_qnty, b.job_no_prefix_num, b.buyer_name, b.style_ref_no, b.company_name, c.po_break_down_id, c.item_number_id, c.production_source, c.location, c.production_date, c.sewing_line, c.floor_id, b.insert_date, c.prod_reso_allo order by c.production_date");
                    
                }
                //echo $pro_date_sql;die;
                foreach($pro_date_sql as $pro_date_sql_row)
                {
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					 $loaction_id=$prod_arr[$pro_date_sql_row[csf('po_break_down_id')]][$pro_date_sql_row[csf('production_date')]][$pro_date_sql_row[csf('item_number_id')]]['location'];
						$cutting_hour=$prod_arr[$pro_date_sql_row[csf('po_break_down_id')]][$pro_date_sql_row[csf('production_date')]][$pro_date_sql_row[csf('item_number_id')]]['1'];
						$iron_hour=$prod_arr[$pro_date_sql_row[csf('po_break_down_id')]][$pro_date_sql_row[csf('production_date')]][$pro_date_sql_row[csf('item_number_id')]]['7'];
						$re_iron_hour=$prod_arr[$pro_date_sql_row[csf('po_break_down_id')]][$pro_date_sql_row[csf('production_date')]][$pro_date_sql_row[csf('item_number_id')]]['7'];
						$finish_hr=$prod_arr[$pro_date_sql_row[csf('po_break_down_id')]][$pro_date_sql_row[csf('production_date')]][$pro_date_sql_row[csf('item_number_id')]]['8'];
						
					$off_days_check=$off_day_check[$pro_date_sql_row[csf('production_date')]][$loaction_id]['day'];
						
						if($off_days_check!=2)
						{
							$print_to_issue=$printing_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]][$pro_date_sql_row[csf("location")]][$pro_date_sql_row[csf("floor_id")]][$pro_date_sql_row[csf("sewing_line")]]['2'];
							$print_to_recv=$printreceived_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]][$pro_date_sql_row[csf("location")]][$pro_date_sql_row[csf("floor_id")]][$pro_date_sql_row[csf("sewing_line")]]['3'];
							$embl_issue= $emb_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]][$pro_date_sql_row[csf("location")]][$pro_date_sql_row[csf("floor_id")]][$pro_date_sql_row[csf("sewing_line")]]['2'];
							$embl_recv=$embreceived_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]][$pro_date_sql_row[csf("location")]][$pro_date_sql_row[csf("floor_id")]][$pro_date_sql_row[csf("sewing_line")]]['3'];
							$wash_issue=$wash_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]][$pro_date_sql_row[csf("location")]][$pro_date_sql_row[csf("floor_id")]][$pro_date_sql_row[csf("sewing_line")]]['2'];
							$wash_recv=$washreceived_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]][$pro_date_sql_row[csf("location")]][$pro_date_sql_row[csf("floor_id")]][$pro_date_sql_row[csf("sewing_line")]]['3'];
							$sp_issue=$sp_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]][$pro_date_sql_row[csf("location")]][$pro_date_sql_row[csf("floor_id")]][$pro_date_sql_row[csf("sewing_line")]]['2'];
							$sp_recv=$spreceived_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]][$pro_date_sql_row[csf("location")]][$pro_date_sql_row[csf("floor_id")]][$pro_date_sql_row[csf("sewing_line")]]['3'];
							$sewing_in=$sewingin_inhouse_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]][$pro_date_sql_row[csf("location")]][$pro_date_sql_row[csf("floor_id")]][$pro_date_sql_row[csf("sewing_line")]]['4'];
							
						$time_valid="19:00";
						if($time_valid>=$cutting_hour)
						{
							$cutting_qty=$cutting_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]][$pro_date_sql_row[csf("location")]][$pro_date_sql_row[csf("floor_id")]][$pro_date_sql_row[csf("sewing_line")]]['1'];
						}
						else
						{
							 $cutting_qty=0;
						}
						if($time_valid>=$iron_hour)
						{
							 $tot_iron=$iron_in_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]][$pro_date_sql_row[csf("location")]][$pro_date_sql_row[csf("floor_id")]][$pro_date_sql_row[csf("sewing_line")]]['7'];
						}
						else
						{
							  $tot_iron=0;
						}
						if($time_valid>=$re_iron_hour)
						{
							 $re_iron_qty=$re_iron_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]][$pro_date_sql_row[csf("location")]][$pro_date_sql_row[csf("floor_id")]][$pro_date_sql_row[csf("sewing_line")]]['7'];
						}
						else
						{
							  $re_iron_qty=0;
						}
						if($time_valid>=$finish_hr)
						{
							// $fqty=$cutting_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['8'];
							 $fqty=$finish_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]][$pro_date_sql_row[csf("location")]][$pro_date_sql_row[csf("floor_id")]][$pro_date_sql_row[csf("sewing_line")]]['8'];  
                               // $total_finish+=$finish_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]][$pro_date_sql_row[csf("location")]][$pro_date_sql_row[csf("floor_id")]][$pro_date_sql_row[csf("sewing_line")]]['8'];
						}
						else
						{
							  $fqty=0;
						}
                    
                    $sewing_line='';
                    if($pro_date_sql_row[csf('prod_reso_allo')]==1)
                    {
                        $line_number=explode(",",$prod_reso_arr[$pro_date_sql_row[csf('sewing_line')]]);
                        foreach($line_number as $val)
                        {
                            if($sewing_line=='') $sewing_line=$line_library[$val]; else $sewing_line.=", ".$line_library[$val];
                        }
                    }
                    else $sewing_line=$line_library[$pro_date_sql_row[csf('sewing_line')]]; 
                    //$item_smv=$smv_qty_arr[$pro_date_sql_row[csf("job_no_mst")]][$pro_date_sql_row[csf("item_number_id")]];
                ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                        <td width="40"><? echo $i;?></td>
                        <td width="80"><p><? echo $company_short_library[$pro_date_sql_row[csf("company_name")]]; ?></p></td>
                        <td width="65" align="center"><p><? echo $pro_date_sql_row[csf("job_no_prefix_num")];?></p></td>
                        <td width="55" align="center"><p><? echo $pro_date_sql_row[csf("year")];?></p></td>
                        <td width="130"><p><a href="##" onClick="openmypage_order(<? echo $pro_date_sql_row[csf("po_break_down_id")];?>,<? echo $pro_date_sql_row[csf("item_number_id")]; ?>,'orderQnty_popup');" ><? echo $pro_date_sql_row[csf("po_number")]; ?></a></p></td>
                        <td width="70" align="center"><p><? echo $job_item_smv_arr[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("item_number_id")]]["smv_pcs"]; ?></p></td>
                        <td width="70" align="center"><p><? echo $job_item_smv_arr[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("item_number_id")]]["finsmv_pcs"]; ?></p></td>
                        <td width="70"><p><? echo $buyer_short_library[$pro_date_sql_row[csf("buyer_name")]]; ?></p></td>
                        <td width="130"><p><? echo $pro_date_sql_row[csf("style")]; ?></p></td>
                        <td width="100"><p><? echo $pro_date_sql_row[csf("file_no")]; ?></p></td>
                        <td width="100"><p><? echo $pro_date_sql_row[csf("grouping")]; ?></p></td>
                        <td width="130"><p><? echo $garments_item[$pro_date_sql_row[csf("item_number_id")]]; ?></p></td>
                        <td width="100" align="center"><p><? echo change_date_format($pro_date_sql_row[csf("production_date")]); ?></p></td>
                        <td width="100"><p><? echo $knitting_source[$pro_date_sql_row[csf("production_source")]]; ?></p></td>
                        <td width="100"><p><? echo $location_library[$pro_date_sql_row[csf("location")]]; ?></p></td>
                        <td width="100"><p><? echo $floor_library[$pro_date_sql_row[csf("floor_id")]]; ?></p></td>
                        <td width="100" align="center"><p><? echo $sewing_line; ?></p></td>
                        <td width="80" align="right"><a href="##" onClick="openmypage_popup_location(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,<? echo $pro_date_sql_row[csf("location")];?>,<? echo $pro_date_sql_row[csf("floor_id")];?>,<? echo $pro_date_sql_row[csf("sewing_line")];?>,'Cutting Info','cutting_popup_location');" >
                            <?
                                echo $cutting_qty; 
                                //echo $total_cut;
                                $total_cut+=$cutting_qty;//$cutting_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]][$pro_date_sql_row[csf("location")]][$pro_date_sql_row[csf("floor_id")]][$pro_date_sql_row[csf("sewing_line")]]['1'];
                            ?>
                            </a>
                        </td>
                        <td width="80" align="right"><a href="##" onClick="openmypage_popup_location(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,<? echo $pro_date_sql_row[csf("location")];?>,<? echo $pro_date_sql_row[csf("floor_id")];?>,<? echo $pro_date_sql_row[csf("sewing_line")];?>,'Printing Issue Info','printing_issue_popup_location');" >
                            <?
                                echo $print_to_issue;  
                                $total_print_iss+=$print_to_issue;
                            ?>
                            </a>
                        </td>
                        <td width="80" align="right"><a href="##" onClick="openmypage_popup_location(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,<? echo $pro_date_sql_row[csf("location")];?>,<? echo $pro_date_sql_row[csf("floor_id")];?>,<? echo $pro_date_sql_row[csf("sewing_line")];?>,'Printing Receive Info','printing_receive_popup_location');" >
                            <?
                                echo $print_to_recv;  
                                $total_print_re+=$print_to_recv;  
                            ?>
                            </a>
                        </td>
                        <td width="80" align="right"><a href="##" onClick="openmypage_popup_location(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,<? echo $pro_date_sql_row[csf("location")];?>,<? echo $pro_date_sql_row[csf("floor_id")];?>,<? echo $pro_date_sql_row[csf("sewing_line")];?>,'Embroidery Issue Info','embroi_issue_popup_location');" >
                            <?
                                echo $embl_issue; 
                                $total_emb_iss+=$embl_issue;
                            ?>
                            </a>
                        </td>
                        <td width="80" align="right"><a href="##" onClick="openmypage_popup_location(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,<? echo $pro_date_sql_row[csf("location")];?>,<? echo $pro_date_sql_row[csf("floor_id")];?>,<? echo $pro_date_sql_row[csf("sewing_line")];?>,'Embroidery Receive Info','embroi_receive_popup_location');" >
                            <?
                                echo $embl_recv;   
                                $total_emb_re+=$embl_recv;  
                            ?>
                            </a>
                        </td>
                        <td width="80" align="right"><a href="##" onClick="openmypage_popup_location(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,<? echo $pro_date_sql_row[csf("location")];?>,<? echo $pro_date_sql_row[csf("floor_id")];?>,<? echo $pro_date_sql_row[csf("sewing_line")];?>,'Wash Issue Info','wash_issue_popup_location');" >
                            <?
                                echo $wash_issue; 
                                $total_wash_iss+=$wash_issue;
                            ?>
                            </a>
                        </td>
                        <td width="80" align="right"><a href="##" onClick="openmypage_popup_location(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,<? echo $pro_date_sql_row[csf("location")];?>,<? echo $pro_date_sql_row[csf("floor_id")];?>,<? echo $pro_date_sql_row[csf("sewing_line")];?>,'Wash Receive Info','wash_receive_popup_location');" >
                            <?
                                echo $wash_recv;    
                                $total_wash_re+=$wash_recv;  
                            ?>
                            </a>
                        </td>
                        <td width="80" align="right"><a href="##" onClick="openmypage_popup_location(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,<? echo $pro_date_sql_row[csf("location")];?>,<? echo $pro_date_sql_row[csf("floor_id")];?>,<? echo $pro_date_sql_row[csf("sewing_line")];?>,'Spetial Work Info','sp_issue_popup_location');" >
                            <?
                                echo $sp_issue;  
                                $total_sp_iss+=$sp_issue;
                            ?>
                            </a>
                        </td>
                        <td width="80" align="right"><a href="##" onClick="openmypage_popup_location(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,<? echo $pro_date_sql_row[csf("location")];?>,<? echo $pro_date_sql_row[csf("floor_id")];?>,<? echo $pro_date_sql_row[csf("sewing_line")];?>,'Spetial Work Info','sp_receive_popup_location');" >
                            <?
                                echo $sp_recv;    
                                $total_sp_re+=$sp_recv;   
                            ?>
                            </a>
                        </td>
                        <td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,'1','4','sewingQnty_popup');" >
                            <?
                                $sewingin_inhouse=$sewingin_inhouse_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]][$pro_date_sql_row[csf("location")]][$pro_date_sql_row[csf("floor_id")]][$pro_date_sql_row[csf("sewing_line")]]['4'];
                                echo $sewingin_inhouse; $total_sew_inhouse_in+=$sewingin_inhouse; 
                            ?>
                        </a></td>
                        <td width="80" align="right">D<a href="##" onclick="openmypage_sew_output(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,'3','4','sewingQnty_popup');" >
                            <?
                                $sewingin_outbound=0;//$sewingin_outbound_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]][$pro_date_sql_row[csf("location")]][$pro_date_sql_row[csf("floor_id")]][$pro_date_sql_row[csf("sewing_line")]]['4'];
                                echo $sewingin_outbound; $total_sew_outbound_in+=$sewingin_outbound; 
                            ?>
                        </a></td>
                        <td width="80" align="right"><? $sewing_input_total=$sewingin_inhouse+$sewingin_outbound; echo $sewing_input_total; $total_sew_in+=$sewing_input_total; ?></td>
                        <td width="80" align="right"><a href="##" onclick="openmypage_sew_output2(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,'1','5','sewingQnty_popup',<? echo $pro_date_sql_row[csf("location")];?>,<? echo $pro_date_sql_row[csf("floor_id")];?>,<? echo $pro_date_sql_row[csf("sewing_line")];?>);" >
                            <?
                                $sewingout_inhouse=$sewing_in;
                                echo $sewingout_inhouse; $total_sew_inhouse_out+=$sewingout_inhouse; 
                            ?>
                        </a></td>
                        <td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,'3','5','sewingQnty_popup');" >
                            <?
                                $sewingout_outbound=0;//$sewingout_outbound_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]][$pro_date_sql_row[csf("location")]][$pro_date_sql_row[csf("floor_id")]][$pro_date_sql_row[csf("sewing_line")]]['5'];
                                echo $sewingout_outbound; $total_sew_outbound_out+=$sewingout_outbound; 
                            ?>
                        </a></td>
                        <td width="80" align="right"><? $sewing_output_total=$sewingout_inhouse+$sewingout_outbound; echo $sewing_output_total; $total_sew_out+=$sewing_output_total; ?></td>
                        <td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,'1','0','ironQnty_popup');" >
                            <?
                                echo $tot_iron;//$iron_in_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]][$pro_date_sql_row[csf("location")]][$pro_date_sql_row[csf("floor_id")]][$pro_date_sql_row[csf("sewing_line")]]['7'];  
                                $total_in_iron+=$tot_iron;//$iron_in_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]][$pro_date_sql_row[csf("location")]][$pro_date_sql_row[csf("floor_id")]][$pro_date_sql_row[csf("sewing_line")]]['7'];  
                            ?>
                            </a>
                        </td>
                        
                        <td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,'3','0','ironQnty_popup');" >
                            <?
                                //echo $iron_out_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]][$pro_date_sql_row[csf("location")]][$pro_date_sql_row[csf("floor_id")]][$pro_date_sql_row[csf("sewing_line")]]['7'];  
                                $total_out_iron+=0;//$iron_out_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]][$pro_date_sql_row[csf("location")]][$pro_date_sql_row[csf("floor_id")]][$pro_date_sql_row[csf("sewing_line")]]['7'];  
                            ?>
                            </a>
                        </td>
                        
                        <td width="80" align="right">
                            <?
                                echo $tot_iron; //$iron_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]][$pro_date_sql_row[csf("location")]][$pro_date_sql_row[csf("floor_id")]][$pro_date_sql_row[csf("sewing_line")]]['7'];  
                                $total_iron+=$tot_iron;//$iron_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]][$pro_date_sql_row[csf("location")]][$pro_date_sql_row[csf("floor_id")]][$pro_date_sql_row[csf("sewing_line")]]['7'];  
                            ?>
                        </td>
                         <td width="80" align="right">
                            <?
                                echo $iron_smv_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]][$pro_date_sql_row[csf("location")]][$pro_date_sql_row[csf("floor_id")]][$pro_date_sql_row[csf("sewing_line")]]['7'];  
                                $total_iron_smv+=$iron_smv_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]][$pro_date_sql_row[csf("location")]][$pro_date_sql_row[csf("floor_id")]][$pro_date_sql_row[csf("sewing_line")]]['7'];  
                            ?>
                        </td>
                        <td width="80" align="right">
                            <?
							echo  $re_iron_qty;
                               // echo $re_iron_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]][$pro_date_sql_row[csf("location")]][$pro_date_sql_row[csf("floor_id")]][$pro_date_sql_row[csf("sewing_line")]]['7'];  
                                $total_re_iron+=$re_iron_qty;//$re_iron_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]][$pro_date_sql_row[csf("location")]][$pro_date_sql_row[csf("floor_id")]][$pro_date_sql_row[csf("sewing_line")]]['7'];  
                            ?>
                        </td>
                        <td width="80" align="right">
                            <?
							echo $fqty;
                               // echo $finish_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]][$pro_date_sql_row[csf("location")]][$pro_date_sql_row[csf("floor_id")]][$pro_date_sql_row[csf("sewing_line")]]['8'];  
                                $total_finish+=$fqty;//$finish_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]][$pro_date_sql_row[csf("location")]][$pro_date_sql_row[csf("floor_id")]][$pro_date_sql_row[csf("sewing_line")]]['8']; 
                            ?>
                        </td>
                        <td width="80" align="right">
                            <?
                                echo $carton_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]][$pro_date_sql_row[csf("location")]][$pro_date_sql_row[csf("floor_id")]][$pro_date_sql_row[csf("sewing_line")]];  
                                $total_carton+=$carton_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]][$pro_date_sql_row[csf("location")]][$pro_date_sql_row[csf("floor_id")]][$pro_date_sql_row[csf("sewing_line")]]; 
                            ?>
                        </td>
                        
                        <?
                        $in_prod_dzn=$sewingout_inhouse/12;
                        $total_in_prod_dzn+=$in_prod_dzn; 
                        
                        $out_prod_dzn=$sewingout_outbound/12;
                        $total_out_prod_dzn+=$out_prod_dzn;
                        
                        $prod_dzn=$sewingout_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]][$pro_date_sql_row[csf("location")]][$pro_date_sql_row[csf("floor_id")]][$pro_date_sql_row[csf("sewing_line")]]['5']/ 12 ;
                        $total_prod_dzn+=$prod_dzn; 
                        
                        $dzn_qnty=0; $cm_value=0; $cm_cost=0;
                        if($costing_per_id_library[$pro_date_sql_row[csf('job_no_mst')]]==1) $dzn_qnty=12;
                        else if($costing_per_id_library[$pro_date_sql_row[csf('job_no_mst')]]==3) $dzn_qnty=12*2;
                        else if($costing_per_id_library[$pro_date_sql_row[csf('job_no_mst')]]==4) $dzn_qnty=12*3;
                        else if($costing_per_id_library[$pro_date_sql_row[csf('job_no_mst')]]==5) $dzn_qnty=12*4;
                        else $dzn_qnty=1;
                        
                        $dzn_qnty=$dzn_qnty*$pro_date_sql_row[csf('ratio')];
                        
                        $in_cm_value=($tot_cost_arr[$pro_date_sql_row[csf('job_no_mst')]]/$dzn_qnty)*$sewingout_inhouse;
                        $total_in_cm_value+=$in_cm_value;
                        
                        $out_cm_value=($tot_cost_arr[$pro_date_sql_row[csf('job_no_mst')]]/$dzn_qnty)*$sewingout_outbound;
                        $total_out_cm_value+=$out_cm_value;
                        
                        $cm_value=($tot_cost_arr[$pro_date_sql_row[csf('job_no_mst')]]/$dzn_qnty)*$sewingout_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]][$pro_date_sql_row[csf("location")]][$pro_date_sql_row[csf("floor_id")]][$pro_date_sql_row[csf("sewing_line")]]['5'];
                        $total_cm_value+=$cm_value;
                        
                        $in_cm_cost=($tot_cm_cost_arr[$pro_date_sql_row[csf('job_no_mst')]]/$dzn_qnty)*$sewingout_inhouse;
                        $total_in_cm_cost+=$in_cm_cost;
                        
                        $out_cm_cost=($tot_cm_cost_arr[$pro_date_sql_row[csf('job_no_mst')]]/$dzn_qnty)*$sewingout_outbound;
                        $total_out_cm_cost+=$out_cm_cost;
                        
                        $cm_cost=($tot_cm_cost_arr[$pro_date_sql_row[csf('job_no_mst')]]/$dzn_qnty)*$sewingout_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]][$pro_date_sql_row[csf("location")]][$pro_date_sql_row[csf("floor_id")]][$pro_date_sql_row[csf("sewing_line")]]['5'];
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
                        <td><a href="##"  onclick="openmypage_remark(<? echo $pro_date_sql_row[csf("po_break_down_id")];?>,'date_wise_production_report');" > Veiw </a></td>
                 </tr>
                    
                    <?
                    $all_order_sewQty_array[$pro_date_sql_row[csf("po_break_down_id")]]+=$sewing_output_total;
                $i++;
              } //Off day check end  
            }//end foreach 1st
            
            ?>
            </table>
                <table width="4300" cellspacing="0" border="1" class="rpt_table" rules="all" id="report_table_footer" >
                    <tfoot>
                        <tr>
                            <th width="40" align="right"></th>
                            <th width="80" align="right"></th>
                            <th width="65" align="right"></th>
                            <th width="55" align="right"></th>
                            <th width="130" align="right"></th>
                            <th width="70" align="right"></th>
                            <th width="70" align="right"></th>
                            <th width="70" align="right"></th>
                            <th width="130" align="right"></th>
                            <th width="100" align="right"></th>
                            <th width="100" align="right"></th>
                            <th width="130" align="right"></th>
                            <th width="100" align="right"></th>
                            <th width="100" align="right"></th>
                            <th width="100" align="right"></th>
                            <th width="100" align="right"></th> 
                            <th width="100" align="right">Total</th>
                            <th width="80" align="right" id="total_cut_td"><? echo $total_cut;?></th> 
                            <th width="80" align="right" id="total_printissue_td"><? echo $total_print_iss; ?> </th> 
                            <th width="80" align="right" id="total_printrcv_td"><?  echo $total_print_re;  ?>  </th>
                            <th width="80" align="right" id="total_emb_iss"><? echo $total_emb_iss; ?> </th> 
                            <th width="80" align="right" id="total_emb_re"><?  echo $total_emb_re;  ?>  </th>
                            <th width="80" align="right" id="total_wash_iss"><? echo $total_wash_iss; ?> </th> 
                            <th width="80" align="right" id="total_wash_re"><?  echo $total_wash_re;  ?>  </th>
                            <th width="80" align="right" id="total_sp_iss"><? echo $total_sp_iss; ?> </th> 
                            <th width="80" align="right" id="total_sp_re"><?  echo $total_sp_re;  ?>  </th>
                            <th width="80" align="right" id="total_sewin_inhouse_td"><? echo $total_sew_inhouse_in; ?></th>
                            <th width="80" align="right" id="total_sewin_outbound_td"><? echo $total_sew_outbound_in; ?></th>
                            <th width="80" align="right" id="total_sewin_td"><? echo $total_sew_input;  ?> </th> 
                            <th width="80" align="right" id="total_sewout_inhouse_td"><? echo $total_sew_inhouse_out; ?></th>
                            <th width="80" align="right" id="total_sewout_outbound_td"><? echo $total_sew_outbound_out; ?></th>
                            <th width="80" align="right" id="total_sewout_td"><? echo $total_sew_out; ?> </th>
                            
                            <th width="80" align="right" id="total_iron_in_td"><? echo $total_in_iron; ?></th>
                            <th width="80" align="right" id="total_iron_out_td"><? echo $total_out_iron; ?></th>
                            
                            <th width="80" align="right" id="total_iron_td"><?  echo $total_iron; ?>  </th> 
                            <th width="80" align="right" id="total_iron_smv_td"><?  echo $total_iron_smv; ?>  </th>
                            <th width="80" align="right" id="total_re_iron_td"><?  echo $total_re_iron; ?>  </th> 
                            <th width="80" align="right" id="total_finish_td"><?  echo $total_finish; ?>  </th>   
                            <th width="80" align="right" id="total_carton_td"><? echo $total_carton; ?> </th> 
                            <th width="80" align="right" id="value_total_in_prod_dzn_td"><?  echo number_format($total_in_prod_dzn,2); ?> </th>
                            <th width="80" align="right" id="value_total_out_prod_dzn_td"><?  echo number_format($total_out_prod_dzn,2); ?> </th>
                            <th width="80" align="right" id="value_total_prod_dzn_td"><?  echo number_format($total_prod_dzn,2); ?> </th>
                            <th width="100" align="right" id="value_total_in_cm_value_td"><? echo number_format($total_in_cm_value,2); ?> </th>
                            <th width="100" align="right" id="value_total_out_cm_value_td"><? echo number_format($total_out_cm_value,2); ?> </th>
                            <th width="100" align="right" id="value_total_cm_value_td"><? echo number_format($total_cm_value,2); ?> </th>
                            <th width="100" align="right" id="value_total_in_cm_cost"><? echo number_format($total_in_cm_cost,2); ?> </th >
                            <th width="100" align="right" id="value_total_out_cm_cost"><? echo number_format($total_out_cm_cost,2); ?> </th >
                            <th width="100" align="right" id="value_total_cm_cost"><? echo number_format($total_cm_cost,2); ?> </th >
                            <th width="">&nbsp;</th>
                         </tr>
                     </tfoot>
                </table>
              </div>
  		</div>
  
<?

				
		}// end if condition of type
		//-------------------------------------------END Date Location Floor & Line Wise------------------------
		//-------------------------------------------Show Line Wise------------------------	
		
        
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
 	
}


if($action=="orderQnty_popup")
{
	
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	
	$sql= "SELECT b.id,b.po_number,b.pub_shipment_date,b.po_quantity*(select from wo_po_details_mas_set_details set where set.job_no=a.job_no and set.gmts_item_id=$gmts_item_id) as po_quantity from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id='$po_break_down_id' and a.garments_nature='$garments_nature' and a.is_deleted=0 and a.status_active=1";
	//echo $sql;
	echo "<br />". create_list_view ( "list_view", "Order No,Order Qnty,Pub Shipment Date", "200,120,220","540","220",1, "SELECT b.id,b.po_number,b.pub_shipment_date,b.po_quantity*a.total_set_qnty as po_quantity from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id='$po_break_down_id' and a.is_deleted=0 and a.status_active=1", "", "","", 1, '0,0,0', $arr, "po_number,po_quantity,pub_shipment_date","../requires/buyer_date_wise_production_report_controller", '','0,1,3');
  		 
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
			 
			 $sql= "SELECT id,production_date,production_quantity,remarks from pro_garments_production_mst where po_break_down_id='$po_break_down_id' and production_type='1' and is_deleted=0 and status_active=1";
 			 //echo $sql;
			 echo  create_list_view ( "list_view_1", "Date,Production Qnty,Remarks", "100,120,280","500","220",1, $sql, "", "","", 1, '0,0,0', $arr, "production_date,production_quantity,remarks", "../requires/order_wise_production_report_controller", '','3,1,0');
			
 		?>
    </fieldset>
    
    <fieldset>
    <legend>Print/Embr Issue</legend>
    	<? 
			 
			  $sql= "SELECT production_date,production_quantity,remarks from pro_garments_production_mst where po_break_down_id='$po_break_down_id'  and production_type='2' and is_deleted=0 and status_active=1";
			  
			 echo  create_list_view ( "list_view_2", "Date,Production Qnty,Remarks", "100,120,280","500","220",1, $sql, "", "","", 1, '0,0,0', $arr, "production_date,production_quantity,remarks", "../requires/order_wise_production_report_controller", '','3,1,0');
		?>
    </fieldset>
    
    <fieldset>
    <legend>Print/Embr Receive</legend>
    	<? 
			 
			  $sql= "SELECT production_date,production_quantity,remarks from pro_garments_production_mst where po_break_down_id='$po_break_down_id'  and production_type='3' and is_deleted=0 and status_active=1";
			  
			 echo  create_list_view ( "list_view_3", "Date,Production Qnty,Remarks", "100,120,280","500","220",1, $sql, "", "","", 1, '0,0,0', $arr, "production_date,production_quantity,remarks", "../requires/order_wise_production_report_controller", '','3,1,0');
		?>
    </fieldset>
    
    
    <fieldset>
    <legend>Sewing Input</legend>
    	<? 
			 
			  $sql= "SELECT production_date,production_quantity,remarks from pro_garments_production_mst where po_break_down_id='$po_break_down_id'  and production_type='4' and is_deleted=0 and status_active=1";
			  
			 echo  create_list_view ( "list_view_4", "Date,Production Qnty,Remarks", "100,120,280","500","220",1, $sql, "", "","", 1, '0,0,0', $arr, "production_date,production_quantity,remarks", "../requires/order_wise_production_report_controller", '','3,1,0');
		?>
    </fieldset>
    
    
    <fieldset>
    <legend>Sewing Output</legend>
    	<? 
			 
			  $sql= "SELECT production_date,production_quantity,remarks from pro_garments_production_mst where po_break_down_id='$po_break_down_id'  and production_type='5' and is_deleted=0 and status_active=1";
			  
			 echo  create_list_view ( "list_view_5", "Date,Production Qnty,Remarks", "100,120,280","500","220",1, $sql, "", "","", 1, '0,0,0', $arr, "production_date,production_quantity,remarks", "../requires/order_wise_production_report_controller", '','3,1,0');
		?>
    </fieldset>
    
    
    <fieldset>
    <legend>Finish Input</legend>
    	<? 
			 
			  $sql= "SELECT production_date,production_quantity,remarks from pro_garments_production_mst where po_break_down_id='$po_break_down_id'  and production_type='6' and is_deleted=0 and status_active=1";
			  
			 echo  create_list_view ( "list_view_6", "Date,Production Qnty,Remarks", "100,120,280","500","220",1, $sql, "", "","", 1, '0,0,0', $arr, "production_date,production_quantity,remarks", "../requires/order_wise_production_report_controller", '','3,1,0');
		?>
    </fieldset>
    
    <fieldset>
    <legend>Finish Output</legend>
    	<? 
			 
			  $sql= "SELECT production_date,production_quantity,remarks from pro_garments_production_mst where po_break_down_id='$po_break_down_id'  and production_type='8' and is_deleted=0 and status_active=1";
			 
			  echo  create_list_view ( "list_view_7", "Date,Production Qnty,Remarks", "100,120,280","500","220",1, $sql, "", "","", 1, '0,0,0', $arr, "production_date,production_quantity,remarks", "../requires/order_wise_production_report_controller", '','3,1,0');
		?>
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
	where a.production_type=1 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
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
	where a.production_type=1 and a.item_number_id=$gmts_item_id and a.production_date='$production_date'  $location_cond $floor_cond $sewing_cond and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
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