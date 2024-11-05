<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
//--------------------------------------------------------------------------------------------------------------------
if ($action=="load_drop_down_location")
{
	//echo $data;
	echo create_drop_down( "cbo_location", 110, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/buyer_date_wise_prod_without_cm_report_controller', this.value, 'load_drop_down_floor', 'floor_td' );",0 );
	exit();
}

if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor", 110, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' order by floor_name","id,floor_name", 1, "-- Select --", $selected, "",0 );  
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
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$company_short_library=return_library_array( "select id,company_short_name from lib_company", "id", "company_short_name"  );
 	$buyer_short_library=return_library_array( "select id,short_name from lib_buyer", "id", "short_name"  );
 	$location_library=return_library_array( "select id,location_name from lib_location", "id", "location_name"  ); 
	$floor_library=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name"  ); 
	$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  ); 
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number'); 	

	if(str_replace("'","",trim($cbo_subcon))==1)//NO
	{
		$garments_nature=str_replace("'","",$cbo_garments_nature);
		//if($garments_nature==1)$garments_nature="";
		 if($garments_nature==1) $garmentsNature=""; else $garmentsNature=" and c.garments_nature=$garments_nature";
		$type = str_replace("'","",$cbo_type);
		$location = str_replace("'","",$cbo_location);
		if(str_replace("'","",$cbo_company_name)==0) $company_name=""; else $company_name=" and b.company_name=$cbo_company_name";
		if(str_replace("'","",$cbo_buyer_name)==0)$buyer_name="";else $buyer_name=" and b.buyer_name=$cbo_buyer_name";
		if(str_replace("'","",$cbo_floor)==0)$floor_name="";else $floor_name=" and c.floor_id=$cbo_floor";
		
		if(str_replace("'","",$cbo_floor)==0)$floor_id="";else $floor_id=" and floor_id=$cbo_floor";
		if ($location==0) $location_cond=""; else $location_cond=" and c.location=".$location." "; 
		
		if(str_replace("'","",trim($txt_date_from))=="" || str_replace("'","",trim($txt_date_to))=="")$txt_date="";
		else $txt_date=" and c.production_date between $txt_date_from and $txt_date_to";
		$fromDate = change_date_format( str_replace("'","",trim($txt_date_from)) );
		$toDate = change_date_format( str_replace("'","",trim($txt_date_to)) );
		//cbo_garments_nature
		$file_no = str_replace("'","",$txt_file_no);
		$internal_ref = str_replace("'","",$txt_internal_ref);
		if ($file_no=="") $file_no_cond=""; else $file_no_cond=" and a.file_no='".trim($file_no)."' "; 
		if ($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and a.grouping='".trim($internal_ref)."' "; 
		$year_id = date('Y', strtotime($fromDate));
		if($year_id!="") $year_cond=" and a.year=$year_id ";else $year_cond="";
	
		if($type==1) //--------------------------------------------Show Date Wise
		{
			ob_start();
			?>
			<div style="width:2960px" id="scroll_body">
                <table width="2960"  cellspacing="0"   >
                    <tr class="form_caption" style="border:none;">
                        <td colspan="36" align="center" style="border:none;font-size:14px; font-weight:bold" > Date Wise Production Report</td>
                     </tr>
                    <tr style="border:none;">
                        <td colspan="36" align="center" style="border:none; font-size:16px; font-weight:bold">
                            Company Name:<? echo $company_library[str_replace("'","",$cbo_company_name)]; ?>                                
                        </td>
                    </tr>
                    <tr style="border:none;">
                        <td colspan="36" align="center" style="border:none;font-size:12px; font-weight:bold">
                            <? echo "From $fromDate To $toDate" ;?>
                        </td>
                    </tr>
                </table>
                <div align="left">
                    <table width="1390" cellspacing="0" border="1" class="rpt_table" rules="all" id="" >
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
                                <th width="80">Sew Input (Outbound)</th>
                                <th width="80">Sew Output</th>
                                <th width="80">Sew Output (Outbound)</th>
                                <th width="80">Total Iron</th>
                                <th width="80">Total Re-Iron</th>
                                <th >Total Finish</th>
                             </tr>
                        </thead>
                    </table>
                    <div style="max-height:425px; width:1400px" >
                        <table cellspacing="0" border="1" class="rpt_table"  width="1390" rules="all" id="" >
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
						$sewingin_inhouse_array=array();
						$sewingin_outbound_array=array();
						$sewingout_array=array();
						$sewingout_inhouse_array=array();
						$sewingout_outbound_array=array();
						$iron_array=array();
						$re_iron_array=array();
						$finish_array=array();
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
						
						
						$sql_order=sql_select("SELECT b.buyer_name,b.buyer_name, c.po_break_down_id, c.item_number_id,c.item_number_id, c.production_date,
						sum(CASE WHEN production_type ='1' THEN production_quantity ELSE 0 END) AS cutting_qnty,
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
						sum(CASE WHEN production_type ='7' THEN production_quantity ELSE 0 END) AS iron_qnty,
						sum(CASE WHEN production_type ='7' THEN re_production_qty ELSE 0 END) AS re_iron_qnty,
						sum(CASE WHEN production_type ='8' THEN production_quantity ELSE 0 END) AS finish_qnty,
						sum(CASE WHEN production_type ='8' THEN reject_qnty ELSE 0 END) AS finish_rej_qnty,
						sum(CASE WHEN production_type ='5' THEN reject_qnty ELSE 0 END) AS sewingout_rej_qnty
									from 
                                        wo_po_details_master b, wo_po_break_down a, pro_garments_production_mst c
                                    where  
                                        a.job_no_mst=b.job_no and a.id=c.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $txt_date $company_name $buyer_name $floor_name $location_cond $garmentsNature group by b.buyer_name,b.buyer_name, c.po_break_down_id, c.item_number_id,c.item_number_id, c.production_date");
										//$txt_date $company_name $buyer_name $floor_name $garments_nature
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
							$cutting_array[$sql_result[csf("buyer_name")]]['1']=$cutting_qty;
							$printing_array[$sql_result[csf("buyer_name")]]=$sql_result[csf("printing_qnty")];
							$printreceived_array[$sql_result[csf("buyer_name")]]=$sql_result[csf("printreceived_qnty")];
							$emb_array[$sql_result[csf("buyer_name")]]=$sql_result[csf("emb_qnty")];
							$embreceived_array[$sql_result[csf("buyer_name")]]=$sql_result[csf("embreceived_qnty")];
							$wash_array[$sql_result[csf("buyer_name")]]=$sql_result[csf("wash_qnty")];
							$washreceived_array[$sql_result[csf("buyer_name")]]=$sql_result[csf("washreceived_qnty")];
							$sp_array[$sql_result[csf("buyer_name")]]=$sql_result[csf("sp_qnty")];
							$spreceived_array[$sql_result[csf("buyer_name")]]=$sql_result[csf("spreceived_qnty")];
							
							$sewingin_array[$sql_result[csf("buyer_name")]]['4']=$sql_result[csf("sewingin_qnty")];
							$sewingin_inhouse_array[$sql_result[csf("buyer_name")]]['4']=$sql_result[csf("sewingin_inhouse_qnty")];
							$sewingin_outbound_array[$sql_result[csf("buyer_name")]]['4']=0;//$sql_result[csf("sewingin_outbound_qnty")];
							
							$sewingout_inhouse_array[$sql_result[csf("buyer_name")]]['5']=$sql_result[csf("sewingout_inhouse_qnty")];
							$sewingout_outbound_array[$sql_result[csf("buyer_name")]]['5']=0;//$sql_result[csf("sewingout_outbound_qnty")];
							$sewingout_array[$sql_result[csf("buyer_name")]]['5']=$sql_result[csf("sewingout_qnty")];
							$sewingout_rej_array[$sql_result[csf("buyer_name")]]['5']=$sql_result[csf("sewingout_rej_qnty")];
							$iron_array[$sql_result[csf("buyer_name")]]['7']=$tot_iron;
							$re_iron_array[$sql_result[csf("buyer_name")]]['7']=$sql_result[csf("re_iron_qnty")];
							$finish_array[$sql_result[csf("buyer_name")]]['8']=$fqty;
							$finish_rej_array[$sql_result[csf("buyer_name")]]['8']=$sql_result[csf("finish_rej_qnty")];
					  }
						}
						unset($sql_order);

                         $total_po_quantity=0; $total_po_value=0;  $total_cut=0; $total_sent_embl=0; $total_re_from_embl=0; $total_sew_input=0; $total_sew_inhouse_in=0;$total_sew_outbound_in=0; $total_sew_inhouse_out=0;$total_sew_outbound_out=0; $total_sew_out=0; $total_iron=0; $total_re_iron=0; $total_finish=0;
                         $i=1;
                         
                         // garments nature here -------------------------------							
                        // if($garments_nature==1 || $garments_nature=="") $garmentsNature="";else $garmentsNature=" and b.garments_nature=$garments_nature";
						 
                         $exfactory_sql= "select b.buyer_name,sum(c.production_quantity) as production_quantity 
                                        from wo_po_details_master b, wo_po_break_down a, pro_garments_production_mst c
                                        where a.job_no_mst=b.job_no and a.id=c.po_break_down_id and c.is_deleted=0 and c.status_active=1 and c.company_id=$cbo_company_name  and c.production_date between $txt_date_from and $txt_date_to $buyer_name $location_cond $floor_name $garmentsNature group by b.buyer_name";
						//echo $exfactory_sql;die;
						$exfactory_sql_result=sql_select($exfactory_sql);	
                        $exfactory_arr=array(); 
                        foreach($exfactory_sql_result as $resRow)
						{
                            $exfactory_arr[$resRow[csf("buyer_name")]] = $resRow[csf("production_quantity")];
                        }
						unset($exfactory_sql_result);
						
						if($db_type==0)
						{                           
                        	$pro_date_sql_query="SELECT b.company_name, b.buyer_name,sum(a.po_quantity*b.total_set_qnty) as po_quantity,sum(a.po_total_price) as po_total_price
									from lib_buyer d, wo_po_details_master b, wo_po_break_down a, pro_garments_production_mst c
                                    where a.job_no_mst=b.job_no and a.id=c.po_break_down_id and b.buyer_name=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.production_date between $txt_date_from and $txt_date_to $company_name $buyer_name $floor_name  $garmentsNature $location_cond $file_no_cond $internal_ref_cond group by b.buyer_name order by d.buyer_name ASC"; 
						}
						else
						{
							$pro_date_sql_query="SELECT b.buyer_name,sum(a.po_quantity*b.total_set_qnty) as po_quantity,sum(a.po_total_price) as po_total_price
									from lib_buyer d, wo_po_details_master b, wo_po_break_down a, pro_garments_production_mst c
                                    where a.job_no_mst=b.job_no and a.id=c.po_break_down_id and b.buyer_name=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.production_date between $txt_date_from and $txt_date_to $company_name $buyer_name $floor_name $garmentsNature $location_cond $file_no_cond $internal_ref_cond group by b.buyer_name order by b.buyer_name ASC"; 
							
						}
                     // echo $pro_date_sql_query;//die; 
					   $pro_date_sql=sql_select($pro_date_sql_query);
					   
                        foreach($pro_date_sql as $pro_date_sql_row)
                        {
                            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							
                            
                        ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                                <td width="30"><? echo $i;?></td>
                                <td width="80"><p><? echo $buyer_short_library[$pro_date_sql_row[csf("buyer_name")]]; ?>&nbsp;</p></td>
                                <td width="80" align="right"><? echo number_format($cutting_array[$pro_date_sql_row[csf("buyer_name")]]['1']); ?></td>
                                <!-- <td width="80" align="right"><?// echo number_format($printing_array[$pro_date_sql_row[csf("buyer_name")]]['2']); ?></td>
                                <td width="80" align="right"><?// echo number_format($printreceived_array[$pro_date_sql_row[csf("buyer_name")]]['3']);  ?></td>-->
                                
                                <td width="80" align="right"><? echo number_format($printing_array[$pro_date_sql_row[csf("buyer_name")]]); ?></td>
                                <td width="80" align="right"><? echo number_format($printreceived_array[$pro_date_sql_row[csf("buyer_name")]]);  ?></td>
                                <td width="80" align="right"><? echo number_format($emb_array[$pro_date_sql_row[csf("buyer_name")]]); ?></td>
                                <td width="80" align="right"><? echo number_format($embreceived_array[$pro_date_sql_row[csf("buyer_name")]]);  ?></td>
                                <td width="80" align="right"><? echo number_format($wash_array[$pro_date_sql_row[csf("buyer_name")]]); ?></td>
                                <td width="80" align="right"><? echo number_format($washreceived_array[$pro_date_sql_row[csf("buyer_name")]]);  ?></td>
                                <td width="80" align="right"><? echo number_format($sp_array[$pro_date_sql_row[csf("buyer_name")]]); ?></td>
                                <td width="80" align="right"><? echo number_format($spreceived_array[$pro_date_sql_row[csf("buyer_name")]]);  ?></td>
                                
                                <td width="80" align="right"><? echo number_format($sewingin_array[$pro_date_sql_row[csf("buyer_name")]]['4']); ?></td>
                                <td width="80" align="right"><? echo number_format($sewingin_outbound_array[$pro_date_sql_row[csf("buyer_name")]]['4']); ?></td>
                                <td width="80" align="right"><? echo number_format($sewingout_array[$pro_date_sql_row[csf("buyer_name")]]['5']); ?></td>
                                <td width="80" align="right"><? echo number_format($sewingout_outbound_array[$pro_date_sql_row[csf("buyer_name")]]['5']); ?></td>
                                <td width="80" align="right"><? echo number_format($iron_array[$pro_date_sql_row[csf("buyer_name")]]['7']); ?></td>
                                <td width="80" align="right"><? echo number_format($re_iron_array[$pro_date_sql_row[csf("buyer_name")]]['7']); ?></td>
                                <td  align="right"><? echo number_format($finish_array[$pro_date_sql_row[csf("buyer_name")]]['8']); ?></td>
                            </tr>	
                            <?		
                                $total_po_quantity+=$pro_date_sql_row[csf("po_quantity")];
                                $total_po_value+=$pro_date_sql_row[csf("po_total_price")];
                                $total_cut+=$cutting_array[$pro_date_sql_row[csf("buyer_name")]]['1'];
								/*$total_sent_embl+=$printing_array[$pro_date_sql_row[csf("buyer_name")]]['2'];
								$total_re_from_embl+=$printreceived_array[$pro_date_sql_row[csf("buyer_name")]]['3'];*/
								$total_sent_print+=$printing_array[$pro_date_sql_row[csf("buyer_name")]];
								$total_re_print+=$printreceived_array[$pro_date_sql_row[csf("buyer_name")]];
								$total_sent_embl+=$emb_array[$pro_date_sql_row[csf("buyer_name")]];
								$total_re_from_embl+=$embreceived_array[$pro_date_sql_row[csf("buyer_name")]];
								$total_sent_wash+=$wash_array[$pro_date_sql_row[csf("buyer_name")]];
								$total_re_from_wash+=$washreceived_array[$pro_date_sql_row[csf("buyer_name")]];
								$total_sent_sp+=$sp_array[$pro_date_sql_row[csf("buyer_name")]];
								$total_re_from_sp+=$spreceived_array[$pro_date_sql_row[csf("buyer_name")]];
                                $total_sew_input+=$sewingin_array[$pro_date_sql_row[csf("buyer_name")]]['4'];
								$total_sew_outbound_in+=$sewingin_outbound_array[$pro_date_sql_row[csf("buyer_name")]]['4'];
                                $total_sew_out+=$sewingout_array[$pro_date_sql_row[csf("buyer_name")]]['5'];
								$total_sew_outbound_out+=$sewingout_outbound_array[$pro_date_sql_row[csf("buyer_name")]]['5'];
                                $total_iron+=$iron_array[$pro_date_sql_row[csf("buyer_name")]]['7'];
								$total_re_iron+=$re_iron_array[$pro_date_sql_row[csf("buyer_name")]]['7'];
                                $total_finish+=$finish_array[$pro_date_sql_row[csf("buyer_name")]]['8'];                                    
                          
                        $i++;
                    }//end foreach 1st
					unset($pro_date_sql);
                        $chart_data_qnty="Order Qty;".$total_po_quantity."\n"."Cutting;".$total_cut."\n"."Sew In;".$total_sew_input."\n"."Sew Out ;".$total_sew_out."\n"."Iron ;".$total_iron."\n"."Finish ;".$total_finish."\n"."Ex-Fact;".$total_ex_factory."\n";
                    ?>
                    </table>
                    <input type="hidden" id="graph_data" value="<? echo substr($chart_data_qnty,0,-1); ?>"/>
                     <table border="1" class="tbl_bottom"  width="1390" rules="all" id="" >
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
                                <td width="80" id="total_sew_outbound_in"><? echo number_format($total_sew_outbound_in); ?></td>  
                                <td width="80" id="tot_sew_out"><? echo number_format($total_sew_out); ?></td>  
                                <td width="80" id="tot_sew_outbound_out"><? echo number_format($total_sew_outbound_out); ?></td>   
                                <td width="80" id="tot_iron"><? echo number_format($total_iron); ?></td>
                                <td width="80" id="tot_re_iron"><? echo number_format($total_re_iron); ?></td> 
                                <td  id="tot_finish"><? echo number_format($total_finish); ?></td>
                             </tr>
                     </table>
                   </div>
                 </div>
                <div style="clear:both"></div>
                 <br />
                                
                <table width="3140" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1">
                   	<thead>
                        <th width="30">Sl.</th>    
                        <th width="100">Working Factory</th>
                        <th width="100">Job No</th>
                        <th width="130">Order Number</th>
                        <th width="100">Buyer Name</th>
                        <th width="130">Style Name</th>
                        <th width="100">File No</th>
                        <th width="100">Internal Ref</th>
                        <th width="130">Item Name</th>
                        <th width="100">Production Date</th>
                        <th width="80">Cutting</th>
                        <th width="80">Sent to prnt</th>
                        <th width="80">Rev prn/Emb</th>
                        
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
                        <th width="80">Total Iron Qty</th>
                        
                        <th width="80">Re-Iron Qty </th>
                        <th width="80">Finish Qty (Inhouse)</th>
                        <th width="80">Finish Qty (Out-bound)</th>
                        <th width="80">Total Finish Qty</th>
                        
                        <th width="80">Today Carton</th>
                        <th width="80">Prod/Dzn</th>
                        <th width="80">Reject Qty</th>
                        <th>Remarks</th>
                     </thead>
                </table>
                <div style="width:3140px; overflow-y: scroll; max-height:300px;" id="scroll_body2">
                    <table cellspacing="0" border="1" class="rpt_table"  width="3122" rules="all" id="table_body" >
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
					$finishin_array=array();
					$finishout_array=array();
					$finish_rej_array=array();
					$sewingout_rej_array=array();
					$carton_qty_array=array();
					
					$sql_order=sql_select("SELECT c.po_break_down_id,c.production_date,c.item_number_id,
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
					sum(CASE WHEN c.production_source=1 and c.production_type ='8' THEN c.production_quantity ELSE 0 END) AS finishin_qnty,
					sum(CASE WHEN c.production_source=3 and c.production_type ='8' THEN c.production_quantity ELSE 0 END) AS finishout_qnty,
					sum(CASE WHEN c.production_type ='8' THEN c.reject_qnty ELSE 0 END) AS finish_rej_qnty,
					sum(CASE WHEN c.production_type ='5' THEN c.reject_qnty ELSE 0 END) AS sewingout_rej_qnty,
					sum(c.carton_qty) as carton_qty
								from 
									wo_po_break_down a,wo_po_details_master b, pro_garments_production_mst c 
								where 
									a.job_no_mst=b.job_no and a.id=c.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $txt_date $company_name $buyer_name $floor_name $location_cond $garmentsNature   
									group by c.po_break_down_id,c.production_date,c.item_number_id");
					foreach($sql_order as $sql_result)
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
						$sewingout_rej_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]]['5']=$sql_result[csf("sewingout_rej_qnty")];
						$iron_in_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]]['7']=$sql_result[csf("iron_in_qnty")];
						$iron_out_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]]['7']=$sql_result[csf("iron_out_qnty")];
						$iron_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]]['7']=$sql_result[csf("iron_qnty")];
						$re_iron_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]]['7']=$sql_result[csf("re_iron_qnty")];
						$finish_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]]['8']=$sql_result[csf("finish_qnty")];
						$finishin_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]]['8']=$sql_result[csf("finishin_qnty")];
						$finishout_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]]['8']=$sql_result[csf("finishout_qnty")];
						$finish_rej_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]]['8']=$sql_result[csf("finish_rej_qnty")];
						$carton_qty_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]]['carton_qty']=$sql_result[csf("carton_qty")];
					}
					
                    $total_cut=0; $total_print_iss=0; $total_print_re=0; $total_sew_inhouse_in=0; $total_sew_outbound_in=0; $total_sew_input=0; $total_sew_inhouse_out=0; $total_sew_outbound_out=0; $total_sew_out=0; $total_in_iron=0; $total_out_iron=0; $total_iron=0; $total_finish=0; $total_finishin=0;$total_finishout=0; $total_carton=0; $total_prod_dzn=0; $rej_value=0; $total_rej_value=0;
                    //$total_cm_value=0;
                    
                    $i=1;
					//if($garments_nature!="") $garments_nature=" and c.garments_nature=$garments_nature";
						
					if($db_type==0)
					{
                    	$pro_date_sql=sql_select("SELECT b.job_no_prefix_num, a.job_no_mst, a.po_number,a.grouping,a.file_no, a.po_quantity, b.job_no_prefix_num, b.order_uom, b.buyer_name, b.style_ref_no as style, b.company_name as company_name, c.po_break_down_id,c.item_number_id,c.production_source,c.serving_company,c.location,c.embel_name,c.embel_type, c.production_date,c.production_quantity,c.production_type,c.entry_break_down_type,c.production_hour,c.sewing_line,c.supervisor,c.remarks,c.floor_id,c.alter_qnty,c.reject_qnty 
					from 
						wo_po_break_down a,wo_po_details_master b, pro_garments_production_mst c 
					where 
						a.job_no_mst=b.job_no and a.id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $txt_date $company_name $buyer_name $floor_name  $location_cond $garmentsNature $file_no_cond $internal_ref_cond group by c.po_break_down_id,c.production_date, c.item_number_id order by c.production_date");
					}
					else
					{
						$pro_date_sql=sql_select("SELECT b.job_no_prefix_num, a.po_number,a.grouping,a.file_no, b.buyer_name, b.style_ref_no as style, b.company_name as company_name,  c.po_break_down_id,c.item_number_id, c.production_date
					from 
						wo_po_break_down a,wo_po_details_master b, pro_garments_production_mst c 
					where 
						a.job_no_mst=b.job_no and a.id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $txt_date $company_name $buyer_name $floor_name $location_cond $garmentsNature $file_no_cond $internal_ref_cond  
						group by c.po_break_down_id, b.job_no_prefix_num, a.po_number,a.grouping,a.file_no, b.buyer_name, b.style_ref_no , b.company_name, c.item_number_id, c.production_date 
						order by c.production_date");
					}
                    //echo $pro_date_sql;//;die;  
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
							
						$sent_to_print_qty=$printing_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['2'];
						  $rev_print_qty=$printreceived_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['3']; 
						   $emb_issue_qty=$emb_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['2'];  
							$emb_recv_qty=$embreceived_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['3']; 
							$wash_qty=$wash_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['2']; 
							$wash_recv_qty=$washreceived_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['3']; 
							$sp_issue_qty=$sp_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['2']; 
							$sp_recv_qty=$spreceived_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['3']; 
							$rev_wash_qty=$washreceived_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['3']; 
							$sewing_in_qty=$sewingin_inhouse_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['4'];  
							$sewing_out_qty=$sewingout_inhouse_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['5']; 
							$corton_qty=$carton_qty_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]]['carton_qty'][$pro_date_sql_row[csf("item_number_id")]];	
							
							
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
							 $tot_iron=$iron_in_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['7'];
						}
						else
						{
							  $tot_iron=0;
						}
						if($time_valid>=$re_iron_hour)
						{
							 $re_iron_qty=$re_iron_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['7'];
						}
						else
						{
							  $re_iron_qty=0;
						}
						if($time_valid>=$finish_hr)
						{
							 $fqty=$finishin_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['8'];  
						}
						else
						{
							  $fqty=0;
						}	
                   	 	
						?>
                    	<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                            <td width="30"><? echo $i;?></td>
                            <td width="100"><p><? echo $company_short_library[$pro_date_sql_row[csf("company_name")]]; ?></p></td>
                            <td width="100"><p><? echo $pro_date_sql_row[csf("job_no_prefix_num")];?></p></td>
                            <td width="130"><p><a href="##" onclick="openmypage_order(<? echo $pro_date_sql_row[csf("po_break_down_id")];?>,<? echo $pro_date_sql_row[csf("item_number_id")];?>,'orderQnty_popup');" ><? echo $pro_date_sql_row[csf("po_number")]; ?></a></p></td>
                            <td width="100"><p><? echo $buyer_short_library[$pro_date_sql_row[csf("buyer_name")]]; ?></p></td>
                            <td width="130"><p><? echo $pro_date_sql_row[csf("style")]; ?></p></td>
                            <td width="100"><p><? echo $pro_date_sql_row[csf("file_no")]; ?></p></td>
                            <td width="100"><p><? echo $pro_date_sql_row[csf("grouping")]; ?></p></td>
                            <td width="130"><p><? echo $garments_item[$pro_date_sql_row[csf("item_number_id")]]; ?></p></td>
                            <td width="100"><p><? echo change_date_format($pro_date_sql_row[csf("production_date")]); ?></p></td>
                            <td width="80" align="right"><a href="##" onClick="openmypage_popup(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,'Cutting Info','cutting_popup');" >
								<?
									echo $cutting_qty;
									$total_cut+=$cutting_qty;	?></a>
                            </td>
                            <td width="80" align="right"><a href="##" onClick="openmypage_popup(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,'Printing Issue Info','printing_issue_popup');" >
								<?
									echo $sent_to_print_qty;  
									$total_print_iss+=$sent_to_print_qty;
								?></a>
							</td>
                            <td width="80" align="right"><a href="##" onClick="openmypage_popup(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,'Priniting Receive Info','printing_receive_popup');" >
                            	<?
									echo  $rev_print_qty;  
									$total_print_re+= $rev_print_qty; 
								?></a>
                            </td>
                            
                            
                            <td width="80" align="right"><a href="##" onClick="openmypage_popup(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,'Embroidery Issue Info','embroi_issue_popup');" >
								<?
									echo $emb_issue_qty; 
									$total_emb_iss+=$emb_issue_qty;
								?></a>
							</td>
                            <td width="80" align="right"><a href="##" onClick="openmypage_popup(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,'Embroidery Receive Info','embroi_receive_popup');" >
                            	<?
									echo $emb_recv_qty;  
									$total_emb_re+=$emb_recv_qty;
								?></a>
                            </td>
                            <td width="80" align="right"><a href="##" onClick="openmypage_popup(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,'Wash Issue Info','wash_issue_popup');" >
								<?
									echo $wash_qty;  
									$total_wash_iss+=$wash_qty;
								?></a>
							</td>
                            <td width="80" align="right"><a href="##" onClick="openmypage_popup(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,'Wash Receive Info','wash_receive_popup');" >
                            	<?
									echo $wash_recv_qty;  
									$total_wash_re+=$wash_recv_qty;
								?></a>
                            </td>
                            <td width="80" align="right"><a href="##" onClick="openmypage_popup(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,'Special Works Issue Info','sp_issue_popup');" >
								<?
									echo $sp_issue_qty;  
									$total_sp_iss+=$sp_issue_qty;
								?></a>
							</td>
                            <td width="80" align="right"><a href="##" onClick="openmypage_popup(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,'Special Works Receive Info','sp_receive_popup');" >
                            	<?
									echo $sp_recv_qty;  
									$total_sp_re+=$sp_recv_qty; 
								?></a>
                            </td>
                            <td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,'1','4','sewingQnty_popup');" >
                            	<?
									$sewingin_inhouse=$sewing_in_qty;
									echo $sewingin_inhouse; $total_sew_inhouse_in+=$sewingin_inhouse;  
								?>
                            </a>
                            </td>
                            <td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,'3','4','sewingQnty_popup');" >
                            	<?
									$sewingin_outbound=0;
									echo $sewingin_outbound; $total_sew_outbound_in+=$sewingin_outbound;  
								?>
                            </a>
                            </td>
                            <td width="80" align="right"><? $sewing_input_total=$sewingin_inhouse+$sewingin_outbound; echo $sewing_input_total; $total_sew_in+=$sewing_input_total; ?>
                            </td>
                            
                            <td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,'1','5','sewingQnty_popup');" >
                            	<?
									$sewingout_inhouse=$sewing_out_qty;
									echo $sewingout_inhouse; $total_sew_inhouse_out+=$sewingout_inhouse;  
								?>
                            </a>
                            </td>
                            <td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,'3','5','sewingQnty_popup');" >
                            	<?
									$sewingout_outbound=0;
									echo $sewingout_outbound; $total_sew_outbound_out+=$sewingout_outbound;  
								?>
                            </a>
                            </td>
                            <td width="80" align="right"><? $sewing_output_total=$sewingout_inhouse+$sewingout_outbound; echo $sewing_output_total;  $total_sew_out+=$sewing_output_total;  ?>
                            </td>
                            <td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,'1','0','ironQnty_popup');" >
							<? $iron_in_qty=0;
                                $iron_in_qty=$tot_iron;
								echo $iron_in_qty; $total_in_iron+=$iron_in_qty; ?></a>
                            </td>
                            <td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,'3','0','ironQnty_popup');" >
							<? $iron_out_qty=0;
                                
								$total_out_iron+=$iron_out_qty; ?></a>
                            </td>
                            <td width="80" align="right"><? $iron_qty=0;
                                $iron_qty=$tot_iron; ?>
                            </td>
                            <td width="80" align="right">
                            	<?
									echo $re_iron_qty;
									$total_re_iron+=$re_iron_qty;
								?>
                            </td>
                            <td width="80" align="right"> 
                            	<?
									echo $fqty;  
									$total_finishin+=$fqty; 
								?>
                            </td>
                            <td width="80" align="right">
                            	<?
									$finishout_qty=0;
									echo $finishout_qty;
									
									$total_finishout+=$finishout_qty;
								?>
                            </td>
                            <td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,'0','0','finishQnty_popup');" >
                            	<?
									echo $fqty;
									$total_finish+=$fqty;
								?></a>
                            </td>
                            <td width="80" align="right">
                            	<?
									echo $corton_qty;  
									$total_carton+=$corton_qty; 
								?>
                            </td>
                            <?
							if($sewing_out_qty!=0) 
							{
								$prod_dzn=($sewing_out_qty) / 12 ;
							}
							$total_prod_dzn+=$prod_dzn; 
							?>
                            <td width="80" align="right"><? if($prod_dzn!=0) echo number_format($prod_dzn,2); else echo "0"; ?></td>
 							<?
                            $cm_per_dzn=return_field_value("cm_for_sipment_sche","wo_pre_cost_dtls","job_no='".$pro_date_sql_row[csf("job_no_mst")]."' and is_deleted=0 and status_active=1");
                            ?>
                            <td width="80" align="right" ><? $rej_value=$finish_rej_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['8']+$sewingout_rej_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['5']; echo number_format($rej_value,2); $total_rej_value+=$rej_value; ?></td>
                            
                            <td width="">
                            	<a href="##"  onclick="openmypage_remark(<? echo $pro_date_sql_row[csf("po_break_down_id")];?>,'date_wise_production_report');" > Veiw </a>
                            </td>
                   	 </tr>
						<?
					$i++;
						} //Off Day Check
				}//end foreach 1st
				//echo "JJJJJJJJJJJJJJJJJJJJJJJJJJJJJJJJJJJJJJJJJJJJJJJJJJJJJJJJJ";
				?>
                </table> 
           		</div>
                <table width="3140" cellspacing="0" border="1" class="tbl_bottom" rules="all" id="report_table_footer" >
                    <tfoot>
                        <td width="30" align="right"></td>
                        <td width="100" align="right"></td>
                        <td width="100" align="right"></td>
                        <td width="130" align="right"></td>
                        <td width="100" align="right"></td>
                        <td width="130" align="right"></td>
                        <td width="100" align="right"></td>
                        <td width="100" align="right"></td>
                        <td width="130" align="right"></td>
                        <td width="100" align="right">Total</td> 
                        <td width="80" align="right" id="total_cut_td" ><? echo $total_cut;?></td> 
                        <td width="80" align="right" id="total_printissue_td"><? echo $total_print_iss; ?></td> 
                        <td width="80" align="right" id="total_printrcv_td"><?  echo $total_print_re; ?></td>
                        
                        <td width="80" align="right" id="total_emb_iss"><? echo $total_emb_iss; ?></td> 
                        <td width="80" align="right" id="total_emb_re"><? echo $total_emb_re; ?></td>
                        <td width="80" align="right" id="total_wash_iss"><? echo $total_wash_iss; ?></td> 
                        <td width="80" align="right" id="total_wash_re"><? echo $total_wash_re; ?></td>
                        <td width="80" align="right" id="total_sp_iss"><? echo $total_sp_iss; ?></td> 
                        <td width="80" align="right" id="total_sp_re"><? echo $total_sp_re; ?></td>
                        
                        <td width="80" align="right" id="total_sewin_inhouse_td"><? echo $total_sew_inhouse_in; ?></td>
                        <td width="80" align="right" id="total_sewin_outbound_td"><? echo $total_sew_outbound_in; ?></td>
                        <td width="80" align="right" id="total_sewin_td"><? echo $total_sew_input; ?></td> 
                        <td width="80" align="right" id="total_sewout_inhouse_td"><? echo $total_sew_inhouse_out; ?></td>
                        <td width="80" align="right" id="total_sewout_outbound_td"><? echo $total_sew_outbound_out; ?></td>
                        <td width="80" align="right" id="total_sewout_td"><? echo $total_sew_out; ?></td>
                        <td width="80" align="right" id="total_iron_in_td"><?  echo $total_in_iron; ?></td>
                        <td width="80" align="right" id="total_iron_out_td"><?  echo $total_out_iron; ?></td>
                        <td width="80" align="right" id="total_iron_td"><?  echo $total_iron; ?></td> 
                        <td width="80" align="right" id="total_re_iron_td"><?  echo $total_re_iron; ?></td>
                        
                        <td width="80" align="right" id="total_finishin_td"><? echo $total_finishin; ?></td>
                        <td width="80" align="right" id="total_finishout_td"><? echo $total_finishout; ?></td> 
                        <td width="80" align="right" id="total_finish_td"><? echo $total_finish; ?></td>   
                        <td width="80" align="right" id="total_carton_td"><? echo $total_carton; ?></td> 
                        <td width="80" align="right" id="total_prod_dzn_td"><?  echo number_format($total_prod_dzn,2); ?></td>
                        <td width="80" align="right" id="total_rej_value_td"><? echo number_format($total_rej_value,2); ?></td >
                        <td width="">&nbsp;</td>
                     </tfoot>
                </table>
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
                <table width="3460px"  cellspacing="0"   >
                    <tr class="form_caption" style="border:none;">
                            <td colspan="39" align="center" style="border:none;font-size:14px; font-weight:bold"> Date Location Floor & Line Wise Production Report </td>
                     </tr>
                    <tr style="border:none;">
                            <td colspan="39" align="center" style="border:none; font-size:16px; font-weight:bold">
                                Company Name:<? echo $company_library[str_replace("'","",$cbo_company_name)]; ?>                                
                            </td>
                      </tr>
                      <tr style="border:none;">
                            <td colspan="39" align="center" style="border:none;font-size:12px; font-weight:bold">
                            	<? echo "From $fromDate To $toDate" ;?>
                            </td>
                      </tr>
                </table>
                <table width="3460px" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1">
                   <thead>
                        <th width="30">Sl.</th>    
                        <th width="100">Working Factory</th>
                        <th width="100">Job No</th>
                        <th width="130">Order Number</th>
                        <th width="100">Buyer Name</th>
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
                        <th width="80">Sent to prnt</th>
                        <th width="80">Rev prn/Emb</th>
                        
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
                        <th width="80">Iron Qty</th>
                        <th width="80">Re-Iron Qty </th>
                        <th width="80">Finish Qty (Inhouse)</th>
                        <th width="80">Finish Qty (Out-bound)</th>
                        <th width="80">Total Finish Qty</th>
                        <th width="80">Today Carton</th>
                        <th width="80">Prod/Dzn</th>
                        <th width="">Remarks</th>
                    </thead>
                </table>
                <div style="max-height:425px; overflow-y:scroll; width:3478px" id="scroll_body2">
                    <table cellspacing="0" border="1" class="rpt_table"  width="3460px" rules="all" id="table_body" >
                    <?
					$cutting_array=array();
					$printing_array=array();
					$printreceived_array=array();
					
					$emb_array=array();
					$embreceived_array=array();
					$wash_array=array();
					$washreceived_array=array();
					$sp_array=array();
					$washreceived_array=array();
					
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
					$finishin_array=array();
					$finishout_array=array();
					$finish_array=array();
					$carton_array=array();
					$sql_cal="select a.location_id,b.date_calc, b.day_status from  lib_capacity_calc_mst a, lib_capacity_calc_dtls b where a.id=b.mst_id  and a.status_active=1 and a.is_deleted=0 and a.comapny_id=$cbo_company_name and b.day_status=2 $day_check $year_cond group by b.date_calc,a.location_id, b.day_status";
						$result_data=sql_select($sql_cal);
						$off_day_check=array();
						foreach($result_data as $row)
						{
						$off_day_check[$row[csf('date_calc')]][$row[csf('location_id')]]['day']=$row[csf('day_status')];	
						}
						if($db_type==2) $day_chek="TO_CHAR(c.production_hour,'HH24:MI')";
						else  if($db_type==0) $day_chek="TIME_FORMAT(c.production_hour, '%H:%i' )";
						
						$sql_order=sql_select("SELECT  c.location,c.po_break_down_id as po_id,c.production_date,c.item_number_id,c.floor_id,c.sewing_line,
						(CASE WHEN production_type ='1' THEN $day_chek ELSE null END) AS cutting_hr,
						(CASE WHEN production_type ='7' THEN $day_chek ELSE null END) AS iron_hr,
						(CASE WHEN production_type ='7' THEN $day_chek ELSE null END) AS re_iron_hr,
						(CASE WHEN production_type ='8' THEN $day_chek ELSE null END) AS finish_hr
						from pro_garments_production_mst c where  c.company_id=$cbo_company_name and  c.is_deleted=0 and c.status_active=1  and c.production_type ='1' $txt_date  $floor_name $location_cond $garmentsNature ");
						$day_checkarr=array();$prod_arr=array();
						foreach($sql_order as $row)
						{
						$prod_arr[$row[csf('po_id')]][$row[csf('production_date')]][$row[csf('item_number_id')]][$row[csf('floor_id')]][$row[csf('sewing_line')]]['1']=$row[csf('cutting_hr')];
						$prod_arr[$row[csf('po_id')]][$row[csf('production_date')]][$row[csf('item_number_id')]][$row[csf('floor_id')]][$row[csf('sewing_line')]]['7']=$row[csf('iron_hr')];
						$prod_arr[$row[csf('po_id')]][$row[csf('production_date')]][$row[csf('item_number_id')]][$row[csf('floor_id')]][$row[csf('sewing_line')]]['7']=$row[csf('re_iron_hr')];
						$prod_arr[$row[csf('po_id')]][$row[csf('production_date')]][$row[csf('item_number_id')]][$row[csf('floor_id')]][$row[csf('sewing_line')]]['8']=$row[csf('finish_hr')];
						$prod_arr[$row[csf('po_id')]][$row[csf('production_date')]][$row[csf('item_number_id')]][$row[csf('floor_id')]][$row[csf('sewing_line')]]['location']=$row[csf('location')];
						}
					
					$sql_order=sql_select("SELECT c.po_break_down_id, c.production_date, c.item_number_id, c.location, c.floor_id, c.sewing_line,
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
					sum(CASE WHEN c.production_source=1 and c.production_type ='8' THEN c.production_quantity ELSE 0 END) AS finishin_qnty, 
					sum(CASE WHEN c.production_source=3 and c.production_type ='8' THEN c.production_quantity ELSE 0 END) AS finishout_qnty, 
					sum(CASE WHEN c.production_type ='8' THEN c.production_quantity ELSE 0 END) AS finish_qnty, 
					sum(c.carton_qty) as carton_qty
								from 
									wo_po_break_down a,wo_po_details_master b, pro_garments_production_mst c 
								where 
									a.job_no_mst=b.job_no and a.id=c.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $txt_date $company_name $buyer_name $floor_name $location_cond $garmentsNature 
									group by c.po_break_down_id, c.production_date, c.item_number_id, c.location, c.floor_id, c.sewing_line");

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
						
						$iron_in_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]][$sql_result[csf("location")]][$sql_result[csf("floor_id")]][$sql_result[csf("sewing_line")]]['7']=$sql_result[csf("iron_in_qnty")];
						$iron_out_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]][$sql_result[csf("location")]][$sql_result[csf("floor_id")]][$sql_result[csf("sewing_line")]]['7']=$sql_result[csf("iron_out_qnty")];
						$iron_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]][$sql_result[csf("location")]][$sql_result[csf("floor_id")]][$sql_result[csf("sewing_line")]]['7']=$sql_result[csf("iron_qnty")];
						
						$re_iron_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]][$sql_result[csf("location")]][$sql_result[csf("floor_id")]][$sql_result[csf("sewing_line")]]['7']=$sql_result[csf("re_iron_qnty")];
						$finishin_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]][$sql_result[csf("location")]][$sql_result[csf("floor_id")]][$sql_result[csf("sewing_line")]]['8']=$sql_result[csf("finishin_qnty")];
						$finishout_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]][$sql_result[csf("location")]][$sql_result[csf("floor_id")]][$sql_result[csf("sewing_line")]]['8']=$sql_result[csf("finishout_qnty")];
						$finish_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]][$sql_result[csf("location")]][$sql_result[csf("floor_id")]][$sql_result[csf("sewing_line")]]['8']=$sql_result[csf("finish_qnty")];
						$carton_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]][$sql_result[csf("location")]][$sql_result[csf("floor_id")]][$sql_result[csf("sewing_line")]]=$sql_result[csf("carton_qty")];
					}
					
                    $total_cut=0; $total_print_iss=0; $total_print_re=0; $total_sew_inhouse_in=0; $total_sew_outbound_in=0; $total_sew_input=0;  $total_sew_inhouse_out=0; $total_sew_outbound_out=0; $total_sew_out=0; $total_in_iron=0; $total_out_iron=0; $total_iron=0; $total_re_iron=0; $total_finishin=0; $total_finishout=0; $total_finish=0; $total_carton=0;  $total_prod_dzn=0;
                    //$total_cm_value=0;
                    
                    $i=1;
					if($garments_nature!="") $garments_nature=" and c.garments_nature=$garments_nature";
					
					if($db_type==0)
					{
                    	$pro_date_sql=sql_select("SELECT b.job_no_prefix_num, a.job_no_mst, a.po_number,a.grouping,a.file_no, a.po_quantity, b.order_uom, b.buyer_name, b.style_ref_no as style, b.company_name as company_name, c.garments_nature, c.po_break_down_id, c.item_number_id, c.production_source, c.serving_company, c.location, c.embel_name, c.embel_type, c.production_date, c.production_quantity, c.production_type, c.entry_break_down_type, c.production_hour, c.sewing_line, c.supervisor, c.carton_qty, c.remarks, c.floor_id, c.alter_qnty, c.reject_qnty, c.prod_reso_allo
					from 
						wo_po_break_down a,wo_po_details_master b, pro_garments_production_mst c 
					where 
						a.job_no_mst=b.job_no and a.id=c.po_break_down_id  and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $txt_date $company_name $buyer_name $floor_name  $location_cond $garmentsNature $file_no_cond $internal_ref_cond group by c.po_break_down_id,c.production_date, c.item_number_id,c.location,c.floor_id,c.sewing_line order by c.production_date");
					}
					else
					{
						$pro_date_sql=sql_select("SELECT b.job_no_prefix_num, a.po_number,a.grouping,a.file_no,b.buyer_name,b.style_ref_no as style ,b.company_name as company_name,  c.po_break_down_id, c.item_number_id, c.production_source, c.location, c.production_date, c.sewing_line, c.carton_qty, c.floor_id, c.prod_reso_allo
						from wo_po_break_down a, wo_po_details_master b, pro_garments_production_mst c 
						where 
						a.job_no_mst=b.job_no and a.id=c.po_break_down_id and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $txt_date $company_name $buyer_name $floor_name  $location_cond $garmentsNature  $file_no_cond $internal_ref_cond 
						group by b.job_no_prefix_num, a.po_number,a.grouping,a.file_no, b.buyer_name, b.style_ref_no, b.company_name, 
c.po_break_down_id, c.item_number_id, c.production_source, c.location, c.production_date, c.sewing_line, c.carton_qty, c.floor_id, c.prod_reso_allo 
						order by c.production_date");
					}
                    //echo $pro_date_sql;die;
 					foreach($pro_date_sql as $pro_date_sql_row)
                    {
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						 
						$cutting_hour=$prod_arr[$pro_date_sql_row[csf('po_break_down_id')]][$pro_date_sql_row[csf('production_date')]][$pro_date_sql_row[csf('item_number_id')]][$pro_date_sql_row[csf('location')]][$pro_date_sql_row[csf('floor_id')]]['1'];
						$iron_hour=$prod_arr[$pro_date_sql_row[csf('po_break_down_id')]][$pro_date_sql_row[csf('production_date')]][$pro_date_sql_row[csf('item_number_id')]][$pro_date_sql_row[csf('location')]][$pro_date_sql_row[csf('floor_id')]]['7'];
						$re_iron_hour=$prod_arr[$pro_date_sql_row[csf('po_break_down_id')]][$pro_date_sql_row[csf('production_date')]][$pro_date_sql_row[csf('item_number_id')]][$pro_date_sql_row[csf('location')]][$pro_date_sql_row[csf('floor_id')]]['7'];
						$finish_hr=$prod_arr[$pro_date_sql_row[csf('po_break_down_id')]][$pro_date_sql_row[csf('production_date')]][$pro_date_sql_row[csf('item_number_id')]][$pro_date_sql_row[csf('location')]][$pro_date_sql_row[csf('floor_id')]]['8'];
						
						
						$off_days_check=$off_day_check[$pro_date_sql_row[csf('production_date')]][$pro_date_sql_row[csf('location')]]['day'];
						
						if($off_days_check!=2)
						{
							$sentto_print=$printing_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]][$pro_date_sql_row[csf("location")]][$pro_date_sql_row[csf("floor_id")]][$pro_date_sql_row[csf("sewing_line")]]['2'];
							$recv_print=$printreceived_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]][$pro_date_sql_row[csf("location")]][$pro_date_sql_row[csf("floor_id")]][$pro_date_sql_row[csf("sewing_line")]]['3'];
							$embl_issue=$emb_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]][$pro_date_sql_row[csf("location")]][$pro_date_sql_row[csf("floor_id")]][$pro_date_sql_row[csf("sewing_line")]]['2'];
							
							$embl_recv=$embreceived_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]][$pro_date_sql_row[csf("location")]][$pro_date_sql_row[csf("floor_id")]][$pro_date_sql_row[csf("sewing_line")]]['3'];
							$wash_issue=$wash_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]][$pro_date_sql_row[csf("location")]][$pro_date_sql_row[csf("floor_id")]][$pro_date_sql_row[csf("sewing_line")]]['2'];
							$wash_recv=$washreceived_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]][$pro_date_sql_row[csf("location")]][$pro_date_sql_row[csf("floor_id")]][$pro_date_sql_row[csf("sewing_line")]]['3'];
							$sp_issue=$sp_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]][$pro_date_sql_row[csf("location")]][$pro_date_sql_row[csf("floor_id")]][$pro_date_sql_row[csf("sewing_line")]]['2'];
							$sp_recv=$spreceived_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]][$pro_date_sql_row[csf("location")]][$pro_date_sql_row[csf("floor_id")]][$pro_date_sql_row[csf("sewing_line")]]['3'];
							$sewingin_inhouse=$sewingin_inhouse_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]][$pro_date_sql_row[csf("location")]][$pro_date_sql_row[csf("floor_id")]][$pro_date_sql_row[csf("sewing_line")]]['4'];
							$sewingout_inhouse=$sewingout_inhouse_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]][$pro_date_sql_row[csf("location")]][$pro_date_sql_row[csf("floor_id")]][$pro_date_sql_row[csf("sewing_line")]]['5'];
							
							
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
							 $fqty=$finishin_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]][$pro_date_sql_row[csf("location")]][$pro_date_sql_row[csf("floor_id")]][$pro_date_sql_row[csf("sewing_line")]]['8'];
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
						if($pro_date_sql_row[csf("sewing_line")]!="") $swing_line_id=$pro_date_sql_row[csf("sewing_line")]; else  $swing_line_id=0;                  	 	
                    ?>
                    	<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                            <td width="30"><? echo $i;?></td>
                            <td width="100"><p><? echo $company_short_library[$pro_date_sql_row[csf("company_name")]]; ?></p></td>
                            <td width="100"><p><? echo $pro_date_sql_row[csf("job_no_prefix_num")];?></p></td>
                            <td width="130"><p><a href="##" onclick="openmypage_order(<? echo $pro_date_sql_row[csf("po_break_down_id")];?>,<? echo $pro_date_sql_row[csf("item_number_id")]; ?>,'orderQnty_popup');" ><? echo $pro_date_sql_row[csf("po_number")]; ?></a></p></td>
                            <td width="100"><p><? echo $buyer_short_library[$pro_date_sql_row[csf("buyer_name")]]; ?></p></td>
                            <td width="130"><p><? echo $pro_date_sql_row[csf("style")]; ?></p></td>
                            <td width="100"><p><? echo $pro_date_sql_row[csf("file_no")]; ?></p></td>
                            <td width="100"><p><? echo $pro_date_sql_row[csf("grouping")]; ?></p></td>
                            <td width="130"><p><? echo $garments_item[$pro_date_sql_row[csf("item_number_id")]]; ?></p></td>
                            <td width="100"><p><? echo change_date_format($pro_date_sql_row[csf("production_date")]); ?></p></td>
                            <td width="100"><p><? echo $knitting_source[$pro_date_sql_row[csf("production_source")]]; ?></p></td>
                            
                            <td width="100"><p><? echo $location_library[$pro_date_sql_row[csf("location")]]; ?></p></td>
                            <td width="100"><p><? echo $floor_library[$pro_date_sql_row[csf("floor_id")]]; ?></p></td>
                            <td width="100" align="center"><p><? echo $sewing_line; ?></p></td>
                            
                            <td width="80" align="right"><a href="##" onClick="openmypage_popup_location(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,<? echo $pro_date_sql_row[csf("location")];?>,<? echo $pro_date_sql_row[csf("floor_id")];?>,<? echo $swing_line_id;?>,'Cutting Info','cutting_popup_location');" >
                            	<?
									echo $cutting_qty;//$cutting_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]][$pro_date_sql_row[csf("location")]][$pro_date_sql_row[csf("floor_id")]][$pro_date_sql_row[csf("sewing_line")]]['1'];  
									$total_cut+=$cutting_qty;
								?></a>
                            </td>
                            <td width="80" align="right"><a href="##" onClick="openmypage_popup_location(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,<? echo $pro_date_sql_row[csf("location")];?>,<? echo $pro_date_sql_row[csf("floor_id")];?>,'<? echo $pro_date_sql_row[csf("sewing_line")];?>','Printing Issue Info','printing_issue_popup_location');" >
                            	<?
									echo $sentto_print;  
									$total_print_iss+=$sentto_print;
								?></a>
                            </td>
                            <td width="80" align="right"><a href="##" onClick="openmypage_popup_location(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,<? echo $pro_date_sql_row[csf("location")];?>,<? echo $pro_date_sql_row[csf("floor_id")];?>,'<? echo $pro_date_sql_row[csf("sewing_line")];?>','Printing Receive Info','printing_receive_popup_location');" >
                            	<?
									echo $recv_print;  
									$total_print_re+=$recv_print;  
								?></a>
                            </td>
                            
                            <td width="80" align="right"><a href="##" onClick="openmypage_popup_location(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,<? echo $pro_date_sql_row[csf("location")];?>,<? echo $pro_date_sql_row[csf("floor_id")];?>,'<? echo $pro_date_sql_row[csf("sewing_line")];?>','Embroidery Issue Info','embroi_issue_popup_location');" >
								<?
									echo $embl_issue;  
									$total_emb_iss+=$embl_issue;
								?></a>
							</td>
                            <td width="80" align="right"><a href="##" onClick="openmypage_popup_location(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,<? echo $pro_date_sql_row[csf("location")];?>,<? echo $pro_date_sql_row[csf("floor_id")];?>,'<? echo $pro_date_sql_row[csf("sewing_line")];?>','Embroidery Receive Info','embroi_receive_popup_location');" >
                            	<?
									echo $embl_recv;  
									$total_emb_re+=$embl_recv; 
								?></a>
                            </td>
                            <td width="80" align="right"><a href="##" onClick="openmypage_popup_location(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,<? echo $pro_date_sql_row[csf("location")];?>,<? echo $pro_date_sql_row[csf("floor_id")];?>,'<? echo $pro_date_sql_row[csf("sewing_line")];?>','Wash Issue Info','wash_issue_popup_location');" >
								<?
									echo $wash_issue;  
									$total_wash_iss+=$wash_issue;
								?></a>
							</td>
                            <td width="80" align="right"><a href="##" onClick="openmypage_popup_location(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,<? echo $pro_date_sql_row[csf("location")];?>,<? echo $pro_date_sql_row[csf("floor_id")];?>,'<? echo $pro_date_sql_row[csf("sewing_line")];?>','Wash Receive Info','wash_receive_popup_location');" >
                            	<?
									echo $wash_recv;  
									$total_wash_re+=$wash_recv; 
								?></a>
                            </td>
                            <td width="80" align="right"><a href="##" onClick="openmypage_popup_location(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,<? echo $pro_date_sql_row[csf("location")];?>,<? echo $pro_date_sql_row[csf("floor_id")];?>,'<? echo $pro_date_sql_row[csf("sewing_line")];?>','Spetial Work Info','sp_issue_popup_location');" >
								<?
									echo $sp_issue;  
									$total_sp_iss+=$sp_issue;
								?></a>
							</td>
                            <td width="80" align="right"><a href="##" onClick="openmypage_popup_location(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,<? echo $pro_date_sql_row[csf("location")];?>,<? echo $pro_date_sql_row[csf("floor_id")];?>,'<? echo $pro_date_sql_row[csf("sewing_line")];?>','Spetial Work Info','sp_receive_popup_location');" >
                            	<?
									echo $sp_recv;  
									$total_sp_re+=$sp_recv; 
								?></a>
                            </td>
                            <td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,'1','4','sewingQnty_popup');" >
                            	<?
									$sewingin_inhouse=$sewingin_inhouse;
									echo $sewingin_inhouse; $total_sew_inhouse_in+=$sewingin_inhouse; 
								?>
                            </a></td>
                            <td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,'3','4','sewingQnty_popup');" >
                            	<?
									$sewingin_outbound=0;
									echo $sewingin_outbound; $total_sew_outbound_in+=$sewingin_outbound; 
								?>
                            </a></td>
                            <td width="80" align="right"><? $sewing_input_total=$sewingin_inhouse+$sewingin_outbound; echo $sewing_input_total; $total_sew_in+=$sewing_input_total; ?></td>
                            <td width="80" align="right"><a href="##" onclick="openmypage_sew_output2(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,'1','5','sewingQnty_popup',<? echo $pro_date_sql_row[csf("location")];?>,<? echo $pro_date_sql_row[csf("floor_id")];?>,'<? echo $pro_date_sql_row[csf('sewing_line')];?>');" >
                            	<?
									$sewingout_inhouse=$sewingout_inhouse;
									echo $sewingout_inhouse; $total_sew_inhouse_out+=$sewingout_inhouse; 
								?>
                            </a></td>
                            <td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,'3','5','sewingQnty_popup');" >
                            	<?
									$sewingout_outbound=0;
									echo $sewingout_outbound; $total_sew_outbound_out+=$sewingout_outbound; 
								?>
                            </a></td>
                            <td width="80" align="right"><? $sewing_output_total=$sewingout_inhouse+$sewingout_outbound; echo $sewing_output_total; $total_sew_out+=$sewing_output_total; ?></td>
                            <td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,'1','0','ironQnty_popup');" >
                            	<?
									$iron_in_qty=0;
									$iron_in_qty=$tot_iron;
									echo $iron_in_qty;
									$total_in_iron+=$iron_in_qty;  
								?></a>
                            </td>
                            <td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,'3','0','ironQnty_popup');" >
                            	<?
									$iron_out_qty=0;
									$iron_out_qty=0;;
									echo $iron_out_qty;
									$total_out_iron+=$iron_out_qty;  
								?></a>
                            </td>
                            <td width="80" align="right"><? $iron_qty=0;
									$iron_qty=$tot_iron;
									echo $iron_qty; $total_iron+=$iron_qty; ?></a>
                            </td>
                            <td width="80" align="right">
                            	<?
									echo $re_iron_qty;  
									$total_re_iron+=$re_iron_qty;  
								?>
                            </td>
                            <td width="80" align="right"> 
                            	<?
									echo $fqty;  
									$total_finishin+=$fqty; 
								?>
                            </td>
                            <td width="80" align="right">
                            	<? $out_fin=0;
									echo $out_fin;  
									$total_finishout+=$out_fin; 
								?>
                            </td>
                            <td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,'0','0','finishQnty_popup');" >
                            	<?
									echo $fqty;  
									$total_finish+=$fqty; 
								?></a>
                            </td>
                            <td width="80" align="right">
                            	<?
									echo $carton_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]][$pro_date_sql_row[csf("location")]][$pro_date_sql_row[csf("floor_id")]][$pro_date_sql_row[csf("sewing_line")]];  
									$total_carton+=$carton_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]][$pro_date_sql_row[csf("location")]][$pro_date_sql_row[csf("floor_id")]][$pro_date_sql_row[csf("sewing_line")]]; 
								?>
                            </td>
                            
                            
                            <? $prod_dzn=$sewingout_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]][$pro_date_sql_row[csf("location")]][$pro_date_sql_row[csf("floor_id")]][$pro_date_sql_row[csf("sewing_line")]]['5'] / 12 ; $total_prod_dzn+=$prod_dzn; ?>
                            <td width="80" align="right"><? if($prod_dzn!=0) echo number_format($prod_dzn,2); else echo "0"; ?></td>
 							<td width="">
                             <a href="##"  onclick="openmypage_remark(<? echo $pro_date_sql_row[csf("po_break_down_id")];?>,'date_wise_production_report');" > Veiw </a>
                            </td>
                   	 </tr>
						<?
						
					$i++;
						} //Off Day Check End
				}//end foreach 1st
				
				?>
                </table>
                </div>
                <table width="3460" cellspacing="0" border="1" class="tbl_bottom" rules="all" id="report_table_footer" >
                    <tfoot>
                        <tr>
                            <td width="30" align="right"></td>
                            <td width="100" align="right"></td>
                            <td width="100" align="right"></td>
                            <td width="130" align="right"></td>
                            <td width="100" align="right"></td>
                            <td width="130" align="right"></td>
                            <td width="100" align="right"></td>
                            <td width="100" align="right"></td>
                            <td width="130" align="right"></td>
                            <td width="100" align="right"></td>
                            <td width="100" align="right"></td>
                            <td width="100" align="right"></td>
                            <td width="100" align="right"></td> 
                            <td width="100" align="right">Total</td>
                            <td width="80" align="right" id="total_cut_td"><? echo $total_cut;?></td> 
                            <td width="80" align="right" id="total_printissue_td"><? echo $total_print_iss; ?> </td> 
                            <td width="80" align="right" id="total_printrcv_td"><?  echo $total_print_re;  ?>  </td>
                            
                            <td width="80" align="right" id="total_emb_iss"><? echo $total_emb_iss; ?> </td> 
                            <td width="80" align="right" id="total_emb_re"><?  echo $total_emb_re;  ?>  </td>
                            <td width="80" align="right" id="total_wash_iss"><? echo $total_wash_iss; ?> </td> 
                            <td width="80" align="right" id="total_wash_re"><?  echo $total_wash_re;  ?>  </td>
                            <td width="80" align="right" id="total_sp_iss"><? echo $total_sp_iss; ?> </td> 
                            <td width="80" align="right" id="total_sp_re"><?  echo $total_sp_re;  ?>  </td>
                            
                            <td width="80" align="right" id="total_sewin_inhouse_td"><? echo $total_sew_inhouse_in; ?></td>
                            <td width="80" align="right" id="total_sewin_outbound_td"><? echo $total_sew_outbound_in; ?></td>
                            <td width="80" align="right" id="total_sewin_td"><? echo $total_sew_input;  ?> </th> 
                            <td width="80" align="right" id="total_sewout_inhouse_td"><? echo $total_sew_inhouse_out; ?></td>
                            <td width="80" align="right" id="total_sewout_outbound_td"><? echo $total_sew_outbound_out; ?></td>
                            <td width="80" align="right" id="total_sewout_td"><? echo $total_sew_out; ?> </td>
                            
                            <td width="80" align="right" id="total_iron_in_td"><?  echo $total_in_iron; ?></td>
                            <td width="80" align="right" id="total_iron_out_td"><?  echo $total_out_iron; ?></td>
                            <td width="80" align="right" id="total_iron_td"><?  echo $total_iron; ?></td>
                            <td width="80" align="right" id="total_re_iron_td"><? echo $total_re_iron; ?></td>
                            <td width="80" align="right" id="total_finishin_td"><? echo $total_finishin; ?></td>
                            <td width="80" align="right" id="total_finishout_td"><? echo $total_finishout; ?></td> 
                            <td width="80" align="right" id="total_finish_td"><?  echo $total_finish; ?></td>   
                            <td width="80" align="right" id="total_carton_td"><? echo $total_carton; ?></td> 
                            <td width="80" align="right" id="total_prod_dzn_td"><?  echo number_format($total_prod_dzn,2); ?></td>
                            <td>&nbsp;</td>
                         </tr>
                     </tfoot>
                </table>
          	</div>
		<?
		}// end if condition of type
	}
	if(str_replace("'","",trim($cbo_subcon))==2) //yes
	{
		$garments_nature=str_replace("'","",$cbo_garments_nature);
		//if($garments_nature==1)$garments_nature="";
		 if($garments_nature==1) $garmentsNature=""; else $garmentsNature=" and c.garments_nature=$garments_nature";
		$type = str_replace("'","",$cbo_type);
		$location = str_replace("'","",$cbo_location);
		//echo $txt_date_from."<br>".$txt_date_to;
		if(str_replace("'","",$cbo_company_name)==0)$company_name=""; else $company_name=" and b.company_name=$cbo_company_name";
		if(str_replace("'","",$cbo_buyer_name)==0)$buyer_name="";else $buyer_name=" and b.buyer_name=$cbo_buyer_name";
		if(str_replace("'","",trim($txt_date_from))=="" || str_replace("'","",trim($txt_date_to))=="")$txt_date="";
		else $txt_date=" and c.production_date between $txt_date_from and $txt_date_to";
		$fromDate = change_date_format( str_replace("'","",trim($txt_date_from)) );
		$toDate = change_date_format( str_replace("'","",trim($txt_date_to)) );
		$file_no = str_replace("'","",$txt_file_no);
		$internal_ref = str_replace("'","",$txt_internal_ref);
		if ($file_no=="") $file_no_cond=""; else $file_no_cond=" and a.file_no='".trim($file_no)."' "; 
		if ($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and a.grouping='".trim($internal_ref)."' "; 
		
		if ($location==0) $location_cond=""; else $location_cond=" and c.location=".$location." "; 
		if(str_replace("'","",$cbo_floor)==0) $floor_name="";else $floor_name=" and c.floor_id=$cbo_floor";
		if(str_replace("'","",$cbo_floor)==0) $sub_floor_name="";else $sub_floor_name=" and b.floor_id=$cbo_floor";
		if ($location==0) $sub_location_cond=""; else $sub_location_cond=" and b.location_id=".$location." "; 
		//cbo_garments_nature
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
                <table width="3140"  cellspacing="0"   >
                    <tr class="form_caption" style="border:none;">
                        <td colspan="34" align="center" style="border:none;font-size:14px; font-weight:bold" > Date Wise Production Report</td>
                     </tr>
                    <tr style="border:none;">
                        <td colspan="34" align="center" style="border:none; font-size:16px; font-weight:bold">
                            Company Name:<? echo $company_library[str_replace("'","",$cbo_company_name)]; ?>                                
                        </td>
                    </tr>
                    <tr style="border:none;">
                        <td colspan="34" align="center" style="border:none;font-size:12px; font-weight:bold">
                            <? echo "From $fromDate To $toDate" ;?>
                        </td>
                    </tr>
                </table>
                <!--<div style="float:left; width:1020px">In-House Order Production</div>-->
                <div style="float:left; width:2080px">
                    <table width="2080" cellspacing="0" border="1" class="" rules="all" id="" >
                        <tr>
                            <td width="1400" align="left" valign="top"><div align="left" style="width:300px; background-color:#FCF"><strong>Production-Regular Order Summary</strong></div>
                                <table width="1400" cellspacing="0" border="1" class="rpt_table" rules="all" id="" >
                                    <thead>
                                        <tr>
                                            <th width="30">Sl.</th>    
                                            <th width="80">Buyer Name</th>
                                            <th width="80">Cut Qty</th>
                                            <th width="80">Sent to Print</th>
                                            <th width="80">Rev Print</th>
                                            <th width="80">Sent to Emb</th>
                                            <th width="80">Rev Emb</th>
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
                                            <th width="80">Total Finish</th>
                                        </tr>
                                    </thead>
                                </table>
                                <div style="max-height:425px; width:1400px" >
                                <table cellspacing="0" border="1" class="rpt_table"  width="1400" rules="all" id="" >
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
                                
                                $sewingin_array=array();
								$sewingin_inhouse_array=array();
								$sewingin_outbound_array=array();
                                $sewingout_array=array();
                                $sewingout_inhouse_array=array();
                                $sewingout_outbound_array=array();
                                $iron_array=array();
                                $re_iron_array=array();
                                $finish_array=array();
                                
                                $sql_order=sql_select("SELECT b.buyer_name,c.po_break_down_id, c.item_number_id,c.item_number_id, c.production_date,
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
                                sum(CASE WHEN c.production_source=1 and c.production_type ='4' THEN c.production_quantity ELSE 0 END) AS sewingin_inhouse_qnty,
                                sum(CASE WHEN c.production_source=3 and c.production_type ='4' THEN c.production_quantity ELSE 0 END) AS sewingin_outbound_qnty,
                                sum(CASE WHEN production_type ='5' THEN production_quantity ELSE 0 END) AS sewingout_qnty, 
                                sum(CASE WHEN c.production_source=1 and c.production_type ='5' THEN c.production_quantity ELSE 0 END) AS sewingout_inhouse_qnty,
                                sum(CASE WHEN c.production_source=3 and c.production_type ='5' THEN c.production_quantity ELSE 0 END) AS sewingout_outbound_qnty,
                                sum(CASE WHEN production_type ='7' THEN production_quantity ELSE 0 END) AS iron_qnty,
                                sum(CASE WHEN production_type ='7' THEN re_production_qty ELSE 0 END) AS re_iron_qnty,
                                sum(CASE WHEN production_type ='8' THEN production_quantity ELSE 0 END) AS finish_qnty
                                from 
                                wo_po_details_master b, wo_po_break_down a, pro_garments_production_mst c
                                where  
                                a.job_no_mst=b.job_no and a.id=c.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $txt_date $company_name $floor_name $location_cond $garmentsNature   group by b.buyer_name,c.po_break_down_id, c.item_number_id,c.item_number_id, c.production_date");
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
					   
									
								$cutting_array[$sql_result[csf("buyer_name")]]['1']=$cutting_qty;
								$printing_array[$sql_result[csf("buyer_name")]]=$sql_result[csf("printing_qnty")];
								$printreceived_array[$sql_result[csf("buyer_name")]]=$sql_result[csf("printreceived_qnty")];
								$emb_array[$sql_result[csf("buyer_name")]]=$sql_result[csf("emb_qnty")];
								$embreceived_array[$sql_result[csf("buyer_name")]]=$sql_result[csf("embreceived_qnty")];
								$wash_array[$sql_result[csf("buyer_name")]]=$sql_result[csf("wash_qnty")];
								$washreceived_array[$sql_result[csf("buyer_name")]]=$sql_result[csf("washreceived_qnty")];
								$sp_array[$sql_result[csf("buyer_name")]]=$sql_result[csf("sp_qnty")];
								$spreceived_array[$sql_result[csf("buyer_name")]]=$sql_result[csf("spreceived_qnty")];
								$sewingin_inhouse_array[$sql_result[csf("buyer_name")]]['4']=$sql_result[csf("sewingin_inhouse_qnty")];
								$sewingin_outbound_array[$sql_result[csf("buyer_name")]]['4']=0;//$sql_result[csf("sewingin_outbound_qnty")];
								$sewingin_array[$sql_result[csf("buyer_name")]]['4']=$sql_result[csf("sewingin_qnty")];
								$sewingout_inhouse_array[$sql_result[csf("buyer_name")]]['5']=$sql_result[csf("sewingout_inhouse_qnty")];
								$sewingout_outbound_array[$sql_result[csf("buyer_name")]]['5']=0;//$sql_result[csf("sewingout_outbound_qnty")];
								$sewingout_array[$sql_result[csf("buyer_name")]]['5']=$sql_result[csf("sewingout_qnty")];
								$iron_array[$sql_result[csf("buyer_name")]]['7']=$tot_iron;
								$re_iron_array[$sql_result[csf("buyer_name")]]['7']=$re_iron_qty;
								$finish_array[$sql_result[csf("buyer_name")]]['8']=$fqty;
					  			 }
								}
                                
                                $total_po_quantity=0; $total_po_value=0; $total_cut=0;$total_sent_embl=0; $total_re_from_embl=0; $total_sew_inhouse_in=0;$total_sew_outbound_in=0; $total_sew_input=0; $total_sew_inhouse_out=0;$total_sew_outbound_out=0; $total_sew_out=0; $total_iron=0;$total_re_iron=0; $total_finish=0;
                                $i=1;
                                
                                // garments nature here -------------------------------							
                                if($garments_nature==1 || $garments_nature=="") $garmentsNature=""; else $garmentsNature=" and b.garments_nature=$garments_nature";
                                
                                if($db_type==0)
                                {                           
									$pro_date_sql_query="SELECT b.company_name, b.buyer_name,sum(a.po_quantity*b.total_set_qnty) as po_quantity,sum(a.po_total_price) as po_total_price,a.id
									from lib_buyer d, wo_po_details_master b, wo_po_break_down a, pro_garments_production_mst c
									where a.job_no_mst=b.job_no and a.id=c.po_break_down_id and b.buyer_name=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.production_date between $txt_date_from and $txt_date_to $company_name $buyer_name $floor_name $garmentsNature  $location_cond  $file_no_cond $internal_ref_cond  group by b.buyer_name order by d.buyer_name ASC"; 
                                }
                                else
                                {
									 $pro_date_sql_query="SELECT b.buyer_name,sum(a.po_quantity*b.total_set_qnty) as po_quantity,sum(a.po_total_price) as po_total_price
									from lib_buyer d, wo_po_details_master b, wo_po_break_down a, pro_garments_production_mst c
									where a.job_no_mst=b.job_no and a.id=c.po_break_down_id and b.buyer_name=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.production_date between $txt_date_from and $txt_date_to $company_name $buyer_name $floor_name $garmentsNature  $location_cond $file_no_cond $internal_ref_cond  group by b.buyer_name order by b.buyer_name ASC"; 
                                }
                                //echo $pro_date_sql_query;//die; 
                                $pro_date_sql=sql_select($pro_date_sql_query);
                                
                                foreach($pro_date_sql as $pro_date_sql_row)
                                {
									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                                        <td width="30"><? echo $i; ?></td>
                                        <td width="80"><? echo $buyer_short_library[$pro_date_sql_row[csf("buyer_name")]]; ?></td>
                                        <td width="80" align="right"><? echo number_format($cutting_array[$pro_date_sql_row[csf("buyer_name")]]['1']); ?></td>
                                        <td width="80" align="right"><? echo number_format($printing_array[$pro_date_sql_row[csf("buyer_name")]]); ?></td>
                                        <td width="80" align="right"><? echo number_format($printreceived_array[$pro_date_sql_row[csf("buyer_name")]]);  ?></td>
                                        <td width="80" align="right"><? echo number_format($emb_array[$pro_date_sql_row[csf("buyer_name")]]); ?></td>
                                        <td width="80" align="right"><? echo number_format($embreceived_array[$pro_date_sql_row[csf("buyer_name")]]);  ?></td>
                                        <td width="80" align="right"><? echo number_format($wash_array[$pro_date_sql_row[csf("buyer_name")]]); ?></td>
                                        <td width="80" align="right"><? echo number_format($washreceived_array[$pro_date_sql_row[csf("buyer_name")]]);  ?></td>
                                        <td width="80" align="right"><? echo number_format($sp_array[$pro_date_sql_row[csf("buyer_name")]]); ?></td>
                                        <td width="80" align="right"><? echo number_format($spreceived_array[$pro_date_sql_row[csf("buyer_name")]]);  ?></td>
                                        <td width="80" align="right"><? echo number_format($sewingin_array[$pro_date_sql_row[csf("buyer_name")]]['4']); ?></td>
                                        <td width="80" align="right"><? echo number_format($sewingin_outbound_array[$pro_date_sql_row[csf("buyer_name")]]['4']); ?></td>
                                        <td width="80" align="right"><? echo number_format($sewingout_array[$pro_date_sql_row[csf("buyer_name")]]['5']); ?></td>
                                        <td width="80" align="right"><? echo number_format($sewingout_outbound_array[$pro_date_sql_row[csf("buyer_name")]]['5']); ?></td>
                                        <td width="80" align="right"><? echo number_format($iron_array[$pro_date_sql_row[csf("buyer_name")]]['7']); ?></td>
                                        <td width="80" align="right"><? echo number_format($re_iron_array[$pro_date_sql_row[csf("buyer_name")]]['7']); ?></td>
                                        <td width="80" align="right"><? echo number_format($finish_array[$pro_date_sql_row[csf("buyer_name")]]['8']); ?></td>
									</tr>	
									<?
									$total_po_quantity+=$pro_date_sql_row[csf("po_quantity")];
									$total_po_value+=$pro_date_sql_row[csf("po_total_price")];
									$total_cut+=$cutting_array[$pro_date_sql_row[csf("buyer_name")]]['1'];
									$total_sent_print+=$printing_array[$pro_date_sql_row[csf("buyer_name")]];
									$total_re_print+=$printreceived_array[$pro_date_sql_row[csf("buyer_name")]];
									$total_sent_embl+=$emb_array[$pro_date_sql_row[csf("buyer_name")]];
									$total_re_from_embl+=$embreceived_array[$pro_date_sql_row[csf("buyer_name")]];
									$total_sent_wash+=$wash_array[$pro_date_sql_row[csf("buyer_name")]];
									$total_re_from_wash+=$washreceived_array[$pro_date_sql_row[csf("buyer_name")]];
									$total_sent_sp+=$sp_array[$pro_date_sql_row[csf("buyer_name")]];
									$total_re_from_sp+=$spreceived_array[$pro_date_sql_row[csf("buyer_name")]];
									$total_sew_input+=$sewingin_array[$pro_date_sql_row[csf("buyer_name")]]['4'];
									$total_sew_outbound_in+=$sewingin_outbound_array[$pro_date_sql_row[csf("buyer_name")]]['4'];
									$total_sew_out+=$sewingout_array[$pro_date_sql_row[csf("buyer_name")]]['5'];
									$total_sew_outbound_out+=$sewingout_outbound_array[$pro_date_sql_row[csf("buyer_name")]]['5'];
									$total_iron+=$iron_array[$pro_date_sql_row[csf("buyer_name")]]['7'];
									$total_re_iron+=$re_iron_array[$pro_date_sql_row[csf("buyer_name")]]['7'];
									$total_finish+=$finish_array[$pro_date_sql_row[csf("buyer_name")]]['8']; 
									$i++;
                                }//end foreach 1st
                                $chart_data_qnty="Order Qty;".$total_po_quantity."\n"."Cutting;".$total_cut."\n"."Sew In;".$total_sew_input."\n"."Sew Out ;".$total_sew_out."\n"."Iron ;".$total_iron."\n"."Finish ;".$total_finish."\n"."Ex-Fact;".$total_ex_factory."\n";
                                ?>
                                </table>
                                    <input type="hidden" id="graph_data" value="<? echo substr($chart_data_qnty,0,-1); ?>"/>
                                    <table border="1" class="tbl_bottom"  width="1400" rules="all" id="" >
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
                                            <td width="80" id="tot_sew_outbound_in"><? echo number_format($total_sew_outbound_in); ?></td> 
                                            <td width="80" id="tot_sew_out"><? echo number_format($total_sew_out); ?></td>
                                            <td width="80" id="tot_sew_outbound_out"><? echo number_format($total_sew_outbound_out); ?></td>   
                                            <td width="80" id="tot_iron"><? echo number_format($total_iron); ?></td> 
                                            <td width="80" id="tot_re_iron"><? echo number_format($total_re_iron); ?></td> 
                                            <td id="tot_finish"><? echo number_format($total_finish); ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </td>
                            <td width="470" align="left" valign="top"><div align="left" style="width:350px; background-color:#FCF"><strong>Production-Subcontract Order(Inbound)Summary </strong></div>
                            <div style="float:left; width:470px">
                                <table width="470" cellspacing="0" border="1" class="rpt_table" rules="all" id="" >
                                    <thead>
                                        <tr>
                                            <th width="30">Sl.</th>    
                                            <th width="120">Buyer</th>
                                            <th width="80">Total Cut Qty</th>
                                            <th width="80">Total Sew Qty</th>
                                            <th width="80">Total Iron Qty</th>
                                            <th>Total Gmt. Fin. Qty</th>
                                        </tr>
                                    </thead>
                                </table>
                                <div style="max-height:425px; width:470px" >
                                <table cellspacing="0" border="1" class="rpt_table"  width="470" rules="all" id="" >
                                <?  
								$total_po_quantity=0;$total_po_value=0;$total_cut_subcon=0;$total_sew_out_subcon=0;$total_ex_factory=0;
                                $i=1;
                                if(str_replace("'","",$cbo_company_name)==0) $company_name_sub=""; else $company_name_sub="and a.company_id=$cbo_company_name";
                                if($db_type==0)
                                {
									$ex_factory_sql="select a.party_id, sum(c.order_quantity) as order_quantity 
									from subcon_ord_mst a, subcon_gmts_prod_dtls b, subcon_ord_dtls c
									where a.subcon_job=c.job_no_mst and c.id=b.order_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and b.production_date between $txt_date_from and $txt_date_to $company_name_sub $sub_floor_name $sub_location_cond group by a.party_id";
                                }
                                else
                                {
									 $ex_factory_sql="select a.party_id, sum(c.order_quantity) as order_quantity 
									from subcon_ord_mst a, subcon_gmts_prod_dtls b, subcon_ord_dtls c
									where a.subcon_job=c.job_no_mst and c.id=b.order_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and b.production_date between $txt_date_from and $txt_date_to $company_name_sub $sub_floor_name $sub_location_cond group by a.party_id";
                                }
                                //echo  $exfactory_sql;
                                $ex_factory_sql_result=sql_select($ex_factory_sql);
                                $ex_factory_arr=array(); 
                                foreach($ex_factory_sql_result as $resRow)
                                {
									$ex_factory_arr[$resRow[csf("party_id")]] = $resRow[csf("order_quantity")];
                                }
                                //var_dump($exfactory_arr);die;
                                //print_r($ex_factory_arr);die;
                                
                                //@@@@@@@@@@@@@@@@@@@@@
                                $sub_cut_sew_array=array();
                                
                                if($db_type==0)
                                {
									$production_mst_sql= sql_select("SELECT  a.party_id,
									sum(CASE WHEN production_type ='1' THEN production_qnty ELSE 0 END) AS cutting_qnty,
									sum(CASE WHEN production_type ='2' THEN production_qnty ELSE 0 END) AS sewingout_qnty,
									sum(CASE WHEN production_type ='3' THEN production_qnty  ELSE 0 END) AS ironout_qnty,
									sum(CASE WHEN production_type ='4' THEN production_qnty  ELSE 0 END) AS gmts_fin_qnty 
									from subcon_ord_mst a, subcon_gmts_prod_dtls b, subcon_ord_dtls c
									where c.job_no_mst=a.subcon_job and c.id=b.order_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and  c.is_deleted=0 and c.status_active=1 and b.production_date between $txt_date_from and $txt_date_to $company_name_sub $sub_location_cond $sub_floor_name group by a.party_id");
                                }
                                else
                                {
									$production_mst_sql=("SELECT  a.party_id,
									sum(CASE WHEN production_type ='1' THEN production_qnty ELSE 0 END) AS cutting_qnty,
									sum(CASE WHEN production_type ='2' THEN production_qnty ELSE 0 END) AS sewingout_qnty,
									sum(CASE WHEN production_type ='3' THEN production_qnty  ELSE 0 END) AS ironout_qnty,
									sum(CASE WHEN production_type ='4' THEN production_qnty  ELSE 0 END) AS gmts_fin_qnty 
									from subcon_ord_mst a, subcon_gmts_prod_dtls b, subcon_ord_dtls c
									where c.job_no_mst=a.subcon_job and c.id=b.order_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.production_date between $txt_date_from and $txt_date_to $company_name_sub $sub_location_cond $sub_floor_name group by a.party_id");
                                }
                                foreach($production_mst_sql as $sql_result)
                                {
									$sub_cut_sew_array[$sql_result[csf("party_id")]]['1']=$sql_result[csf("cutting_qnty")];
									$sub_cut_sew_array[$sql_result[csf("party_id")]]['2']=$sql_result[csf("sewingout_qnty")];
									$sub_cut_sew_array[$sql_result[csf("party_id")]]['3']=$sql_result[csf("ironout_qnty")];
									$sub_cut_sew_array[$sql_result[csf("party_id")]]['4']=$sql_result[csf("gmts_fin_qnty")];
                                }
                                //var_dump($cutting_array);
                                //@@@@@@@@@@@@@@@@@@@@@
                                if($db_type==0)
                                {
									$production_date_sql="SELECT a.party_id, sum(c.order_quantity) as po_quantity, sum(c.amount) as po_total_price       
									from subcon_ord_mst a, subcon_gmts_prod_dtls b, subcon_ord_dtls c 
									where c.job_no_mst=a.subcon_job and c.id=b.order_id and b.production_type in (1,2) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.production_date between $txt_date_from and $txt_date_to $company_name_sub $sub_location_cond $sub_floor_name  group by a.party_id order by a.party_id ASC";
                                }
                                else
                                {
									$production_date_sql="SELECT a.party_id, sum(c.order_quantity) as po_quantity, sum(c.amount) as po_total_price       
									from subcon_ord_mst a, subcon_gmts_prod_dtls b, subcon_ord_dtls c 
									where c.job_no_mst=a.subcon_job and c.id=b.order_id and b.production_type in (1,2) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.production_date between $txt_date_from and $txt_date_to $company_name_sub $sub_location_cond $sub_floor_name group by a.party_id order by a.party_id ASC";
                                }
                                //echo $production_date_sql;//die;
                                $pro_sql_result=sql_select($production_date_sql);	
                                foreach($pro_sql_result as $pro_date_sql_row)
                                {
									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                                        <td width="30"><? echo $i;?></td>
                                        <td width="120"><? echo $buyer_short_library[$pro_date_sql_row[csf("party_id")]]; ?></td>
                                        <td width="80" align="right"><? echo number_format($sub_cut_sew_array[$pro_date_sql_row[csf("party_id")]]['1']); ?></td>
                                        <td width="80" align="right"><? echo number_format($sub_cut_sew_array[$pro_date_sql_row[csf("party_id")]]['2']); ?></td>
                                        <td width="80" align="right"><? echo number_format($sub_cut_sew_array[$pro_date_sql_row[csf("party_id")]]['3']); ?></td>
                                        <td align="right"><? echo number_format($sub_cut_sew_array[$pro_date_sql_row[csf("party_id")]]['4']); ?></td>
									</tr>	
									<?		
									$total_cut_subcon+=$sub_cut_sew_array[$pro_date_sql_row[csf("party_id")]]['1'];
									$total_sew_out_subcon+=$sub_cut_sew_array[$pro_date_sql_row[csf("party_id")]]['2'];
									$total_iron_subcon+=$sub_cut_sew_array[$pro_date_sql_row[csf("party_id")]]['3'];
									$total_gmts_fin_subcon+=$sub_cut_sew_array[$pro_date_sql_row[csf("party_id")]]['4'];
									$i++;
                                }//end foreach 1st
                                //$chart_data_qnty="Order Qty;".$total_po_quantity."\n"."Cutting;".$total_cut."\n"."Sew Out ;".$total_sew_out."\n"."Ex-Fact;".$total_ex_factory."\n";
                                ?>
                                </table>
                                <table border="1" class="tbl_bottom"  width="470" rules="all" id="" >
                                    <tr> 
                                        <td width="30">&nbsp;</td> 
                                        <td width="120" align="right">Total</td> 
                                        <td width="80" id="tot_cutting"><? echo number_format($total_cut_subcon); ?></td>
                                        <td width="80" id="tot_sew_out"><? echo number_format($total_sew_out_subcon); ?></td>
                                        <td width="80" id="tot_iron_out"><? echo number_format($total_iron_subcon); ?></td> 
                                        <td id="tot_gmt_fin_out"><? echo number_format($total_gmts_fin_subcon); ?></td>   
                                    </tr>
                                </table>
                                <br />
                                    <div style="background-color:#FF9; width:400px; font-size:16px"><strong>Total Cutting: <? echo number_format($all_production_cutt=$total_cut+$total_cut_subcon,0); ?> (Pcs)&nbsp;&nbsp; & Total Sewing: <? echo number_format($all_production_sewing=$total_sew_out+$total_sew_out_subcon,0); ?> (Pcs)</strong></div><br />
                                    <div style="background-color:#FF9; width:400px; font-size:16px"><strong>Total Iron: <? echo number_format($all_production_iron=$total_iron+$total_iron_subcon,0); ?> (Pcs)&nbsp;&nbsp; & Total Gmts. Fin.: <? echo number_format($all_production_gmts_fin=$total_finish+$total_gmts_fin_subcon,0); ?> (Pcs)</strong></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">&nbsp;</td>
                        </tr>
                    </table>  
                </div>
                <br />
                <div style="clear:both"></div>

                <div align="left" style="width:220px; background-color:#FCF"><strong>Production-Regular Order Details</strong></div>               
                <table width="3140" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1">
                   <thead>
                        <th width="30">Sl.</th>    
                        <th width="100">Working Factory</th>
                        <th width="100">Job No</th>
                        <th width="130">Order Number</th>
                        <th width="100">Buyer Name</th>
                        <th width="130">Style Name</th>
                        <th width="100">File No</th>
                        <th width="100">Internal Ref</th>
                        <th width="130">Item Name</th>
                        <th width="100">Production Date</th>
                        <th width="80">Cutting</th>
                        <th width="80">Sent to prnt</th>
                        <th width="80">Rev prn/Emb</th>
                        
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
                        <th width="80">Iron Qty </th>
                        <th width="80">Re-Iron Qty </th>
                        <th width="80">Finish Qty (Inhouse)</th>
                        <th width="80">Finish Qty (Out-bound)</th>
                        <th width="80">Total Finish Qty</th>
                        <th width="80">Today Carton</th>
                        <th width="80">Prod/Dzn</th>
                        <th width="80">Reject Qty</th>
                     	<th width="">Remarks</th>
                     </thead>
                </table>
                <div style="width:3140px; overflow-y: scroll; max-height:300px;" id="scroll_body2">
                    <table cellspacing="0" border="1" class="rpt_table"  width="3122" rules="all" id="table_body" >
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

					$sewingin_array=array();
					$sewingout_inhouse_array=array();
					$sewingout_outbound_array=array();
					$sewingout_array=array();
					$iron_in_array=array();
					$iron_out_array=array();
					$iron_array=array();
					$re_iron_array=array();
					$finishin_array=array();
					$finishout_array=array();
					$finish_array=array();
					$finish_rej_array=array();
					$sewingout_rej_array=array();
					$carton_qty_array=array();
					
					$sql_order=sql_select("SELECT c.po_break_down_id, c.production_date,c.item_number_id,
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
					sum(CASE WHEN c.production_source=1 and c.production_type ='8' THEN c.production_quantity ELSE 0 END) AS finishin_qnty,
					sum(CASE WHEN c.production_source=3 and c.production_type ='8' THEN c.production_quantity ELSE 0 END) AS finishout_qnty,
					sum(CASE WHEN c.production_type ='8' THEN c.reject_qnty ELSE 0 END) AS finish_rej_qnty,
					sum(CASE WHEN c.production_type ='5' THEN c.reject_qnty ELSE 0 END) AS sewingout_rej_qnty,
					sum(c.carton_qty) as carton_qty
								from 
									wo_po_break_down a,wo_po_details_master b, pro_garments_production_mst c 
								where 
									a.job_no_mst=b.job_no and a.id=c.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $txt_date $company_name $buyer_name $garmentsNature $floor_name $location_cond    
									group by c.po_break_down_id,c.production_date,c.item_number_id");
					//echo "JJJJJJJJJJJJJJJJJJJJJJJJJJJJ";
					foreach($sql_order as $sql_result)
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
						$sewingin_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]]['4']=$sql_result[csf("sewingin_qnty")];
						$sewingout_inhouse_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]]['5']=$sql_result[csf("sewingout_inhouse_qnty")];
						$sewingout_outbound_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]]['5']=$sql_result[csf("sewingout_outbound_qnty")];
						$sewingout_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]]['5']=$sql_result[csf("sewingout_qnty")];
						$sewingout_rej_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]]['5']=$sql_result[csf("sewingout_rej_qnty")];
						$iron_in_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]]['7']=$sql_result[csf("iron_in_qnty")];
						$iron_out_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]]['7']=$sql_result[csf("iron_out_qnty")];
						$iron_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]]['7']=$sql_result[csf("iron_qnty")];
						$re_iron_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]]['7']=$sql_result[csf("re_iron_qnty")];
						$finish_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]]['8']=$sql_result[csf("finish_qnty")];
						$finishin_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]]['8']=$sql_result[csf("finishin_qnty")];
						$finishout_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]]['8']=$sql_result[csf("finishout_qnty")];
						$finish_rej_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]]['8']=$sql_result[csf("finish_rej_qnty")];
						$carton_qty_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]]['carton_qty']=$sql_result[csf("carton_qty")];
					}
					//echo "JJJJJJJJJJJJJJJJJJJJJJJJJJJJ";
					  
                    $total_cut=0; $total_print_iss=0; $total_print_re=0; $total_sew_inhouse_in=0; $total_sew_outbound_in=0; $total_sew_input=0; $total_sew_inhouse_out=0; $total_sew_outbound_out=0; $total_sew_out=0; $total_in_iron=0; $total_out_iron=0; $total_iron=0; $total_re_iron=0; $total_finishin=0; $total_finishout=0;$total_finish=0; $total_carton=0; $total_prod_dzn=0; $total_rej_value=0; $rej_value=0;
                    //$total_cm_value=0;
                    
                    $i=1;
					if($garments_nature!="") $garments_nature=" and c.garments_nature=$garments_nature";
					
					if($db_type==0)
					{
                    	$pro_date_sql=sql_select("SELECT b.job_no_prefix_num, a.job_no_mst,a.po_number,a.po_quantity,a.grouping,a.file_no,b.order_uom,b.buyer_name,b.style_ref_no as style ,b.company_name as company_name ,c.po_break_down_id,c.item_number_id,c.production_source,c.serving_company,c.location,c.embel_name,c.embel_type, c.production_date,c.production_quantity,c.production_type,c.entry_break_down_type,c.production_hour,c.sewing_line,c.supervisor,c.remarks,c.floor_id,c.alter_qnty,c.reject_qnty 
					from 
						wo_po_break_down a,wo_po_details_master b, pro_garments_production_mst c 
					where 
						a.job_no_mst=b.job_no and a.id=c.po_break_down_id and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $txt_date $company_name $buyer_name $garmentsNature $floor_name $location_cond  $file_no_cond $internal_ref_cond  group by c.po_break_down_id,c.production_date, c.item_number_id order by c.production_date");
						
					}
					else
					{
						 $pro_date_sql=sql_select("SELECT b.job_no_prefix_num, a.job_no_mst,a.po_number,a.po_quantity,a.grouping,a.file_no,b.order_uom,b.buyer_name,b.style_ref_no as style ,b.company_name as company_name ,c.po_break_down_id,c.item_number_id,c.production_date 
					from 
						wo_po_break_down a,wo_po_details_master b, pro_garments_production_mst c 
					where 
						a.job_no_mst=b.job_no and a.id=c.po_break_down_id and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $txt_date $company_name $buyer_name $garmentsNature $floor_name $location_cond  $file_no_cond $internal_ref_cond  group by a.job_no_mst, b.job_no_prefix_num,a.po_number,a.po_quantity,a.grouping,a.file_no,b.order_uom,b.buyer_name,b.style_ref_no ,b.company_name ,c.po_break_down_id,c.item_number_id,c.production_date");
						
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
							$sent_to_print_qty=$printing_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['2'];
						  $rev_print_qty=$printreceived_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['3'];
						   
						   $sentto_emb_qty=$emb_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['2'];  
							$rev_emb_qty=$embreceived_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['3']; 
							$ironin_qty=$iron_in_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['7']; 
						
							$sewingout_inhouse=$sewingout_inhouse_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['5'];
							$sewingin_inhouse=$sewingin_inhouse_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['4'];
							 
							$sentto_wash_qty=$wash_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['2']; 
							$rev_wash_qty=$wash_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['2']; 
							$sentto_sp_works=$sp_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['2']; 
							$sp_rev_qty=$spreceived_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['3']; 
							$finish_qty=$finishin_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['8'];
							$cut_qty=$cutting_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['1'];
							 $reiron_qty=$re_iron_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['7'];
							 
							$time_valid="19:00";
						if($time_valid>=$cutting_hour)
						{
							$cutting_qty=$cut_qty;
						}
						else
						{
							 $cutting_qty=0;
						}
						if($time_valid>=$iron_hour)
						{
							 $tot_iron=$ironin_qty;
						}
						else
						{
							  $tot_iron=0;
						}
						if($time_valid>=$re_iron_hour)
						{
							 $re_iron_qty=$reiron_qty;
						}
						else
						{
							  $re_iron_qty=0;
						}
						if($time_valid>=$finish_hr)
						{
							 $fqty=$finish_qty;
							 
						}
						else
						{
							  $fqty=0;
						}
                   	 	
						?>
                    	<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                            <td width="30"><? echo $i;?></td>
                            <td width="100"><p><? echo $company_short_library[$pro_date_sql_row[csf("company_name")]]; ?></p></td>
                            <td width="100"><p><? echo $pro_date_sql_row[csf("job_no_prefix_num")];?></p></td>
                            <td width="130"><p><a href="##" onclick="openmypage_order(<? echo $pro_date_sql_row[csf("po_break_down_id")];?>,<? echo $pro_date_sql_row[csf("item_number_id")];?>,'orderQnty_popup');" ><? echo $pro_date_sql_row[csf("po_number")]; ?></a></p></td>
                            <td width="100"><p><? echo $buyer_short_library[$pro_date_sql_row[csf("buyer_name")]]; ?></p></td>
                            <td width="130"><p><? echo $pro_date_sql_row[csf("style")]; ?></p></td>
                            <td width="100"><p><? echo $pro_date_sql_row[csf("file_no")]; ?></p></td>
                            <td width="100"><p><? echo $pro_date_sql_row[csf("grouping")]; ?></p></td>
                            <td width="130"><p><? echo $garments_item[$pro_date_sql_row[csf("item_number_id")]]; ?></p></td>
                            <td width="100"><p><? echo change_date_format($pro_date_sql_row[csf("production_date")]); ?></p></td>
                            
                            <td width="80" align="right"><a href="##" onClick="openmypage_popup(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,'Cutting Info','cutting_popup');" >
								<?
									echo $cutting_qty;  
									$total_cut+=$cutting_qty;
								?></a>
                            </td>
                            <td width="80" align="right"><a href="##" onClick="openmypage_popup(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,'Printing Issue Info','printing_issue_popup');" >
								<?
									echo $sent_to_print_qty;  
									$total_print_iss+=$sent_to_print_qty;
								?></a>
							</td>
                            <td width="80" align="right"><a href="##" onClick="openmypage_popup(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,'Priniting Receive Info','printing_receive_popup');" >
                            	<?
									echo $rev_print_qty;  
									$total_print_re+=$rev_print_qty; 
								?></a>
                            </td>
                            
                            <td width="80" align="right"><a href="##" onClick="openmypage_popup(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,'Embroidery Issue Info','embroi_issue_popup');" >
								<?
									echo $sentto_emb_qty;  
									$total_emb_iss+=$sentto_emb_qty;
								?></a>
							</td>
                            <td width="80" align="right"><a href="##" onClick="openmypage_popup(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,'Embroidery Receive Info','embroi_receive_popup');" >
                            	<?
									echo $rev_emb_qty;  
									$total_emb_re+=$rev_emb_qty; 
								?></a>
                            </td>
                            <td width="80" align="right"><a href="##" onClick="openmypage_popup(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,'Wash Issue Info','wash_issue_popup');" >
								<?
									echo $sentto_wash_qty;  
									$total_wash_iss+=$sentto_wash_qty;
								?></a>
							</td>
                            <td width="80" align="right"><a href="##" onClick="openmypage_popup(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,'Wash Receive Info','wash_receive_popup');" >
                            	<?
									echo $rev_wash_qty;  
									$total_wash_re+=$rev_wash_qty; 
								?></a>
                            </td>
                            <td width="80" align="right"><a href="##" onClick="openmypage_popup(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,'Special Works Issue Info','sp_issue_popup');" >
								<?
									echo $sentto_sp_works;  
									$total_sp_iss+=$sentto_sp_works;
								?></a>
							</td>
                            <td width="80" align="right"><a href="##" onClick="openmypage_popup(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,'Special Works Receive Info','sp_receive_popup');" >
                            	<?
									echo $sp_rev_qty;  
									$total_sp_re+=$sp_rev_qty; 
								?></a>
                            </td>
                            <td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,'1','4','sewingQnty_popup');" >
                            	<?
									$sewingin_inhouse=$sewingin_inhouse;
									echo $sewingin_inhouse; $total_sew_inhouse_in+=$sewingin_inhouse;  
								?>
                            </a>
                            </td>
                            <td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,'3','4','sewingQnty_popup');" >
                            	<?
									$sewingin_outbound=0;
									echo $sewingin_outbound; $total_sew_outbound_in+=$sewingin_outbound;  
								?>
                            </a>
                            </td>
                            <td width="80" align="right"><? $sewing_input_total=$sewingin_inhouse+$sewingin_outbound; echo $sewing_input_total;  $total_sew_in+=$sewing_input_total;  ?>
                            </td>
                            
                            <td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,'1','5','sewingQnty_popup');" >
                            	<?
									$sewingout_inhouse=$sewingout_inhouse;
									echo $sewingout_inhouse; $total_sew_inhouse_out+=$sewingout_inhouse;  
								?>
                            </a>
                            </td>
                            <td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,'3','5','sewingQnty_popup');" >
                            	<?
									$sewingout_outbound=0;
									echo $sewingout_outbound; $total_sew_outbound_out+=$sewingout_outbound;  
								?>
                            </a>
                            </td>
                            <td width="80" align="right"><? $sewing_output_total=$sewingout_inhouse+$sewingout_outbound; echo $sewing_output_total;  $total_sew_out+=$sewing_output_total;  ?>
                            </td>
                            <td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,'1','0','ironQnty_popup');" >
                            	<?
								$iron_in_qty=0;
								$iron_in_qty=$tot_iron;  echo $iron_in_qty; $total_in_iron+=$iron_in_qty;  
								?></a>
                            </td>
                            <td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,'3','0','ironQnty_popup');" >
                            	<?
								$iron_out_qty=0;
								$iron_out_qty=0;  echo $iron_out_qty; $total_out_iron+=$iron_out_qty;  
								?></a>
                            </td>
                            <td width="80" align="right"><? $iron_qty=0;
								$iron_qty=$tot_iron;  echo $iron_qty; $total_iron+=$iron_qty; ?></a>
                            </td>
                            <td width="80" align="right">
                            	<?
									echo $re_iron_qty;  
									$total_re_iron+=$re_iron_qty; 
								?>
                            </td>
                            <td width="80" align="right">
                            	<?
									echo $fqty;  
									$total_finishin+=$fqty;  
								?>
                            </td>
                            <td width="80" align="right">
                            	<?
									$finishout=0;
									//echo $finishout_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['8'];  
									$total_finishout+=$finishout;  
								?>
                            </td>
                            <td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,'0','0','finishQnty_popup');" >
                            	<?
									echo $fqty;  
									$total_finish+=$fqty;  
								?></a>
                            </td>
                            <td width="80" align="right">
                            	<?
									echo $carton_qty_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['carton_qty'];
									$total_carton+=$carton_qty_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['carton_qty']; 
								?>
                            </td>
                            
                            <? $prod_dzn=$sewingout_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['5'] / 12 ; $total_prod_dzn+=$prod_dzn; ?>
                            <td width="80" align="right"><? if($prod_dzn!=0) echo number_format($prod_dzn,2); else echo "0"; ?></td>
 							<?
                            //$cm_per_dzn=return_field_value("cm_for_sipment_sche","wo_pre_cost_dtls","job_no='".$pro_date_sql_row[csf("job_no_mst")]."' and is_deleted=0 and status_active=1");
                            ?>
                            <td width="80" align="right" ><? $rej_value=$finish_rej_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['8']+$sewingout_rej_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]]['5']; echo number_format($rej_value,2); $total_rej_value+=$rej_value; ?></td>                    
                            
                            <td width="">
                            	<a href="##"  onclick="openmypage_remark(<? echo $pro_date_sql_row[csf("po_break_down_id")];?>,'date_wise_production_report');" > Veiw </a>
                            </td>
                   	 </tr>
						<?	
					$i++;
					}
				}//end foreach 1st
				?>
                </table> 
                </div> 
                <table width="3140" cellspacing="0" border="1" class="tbl_bottom" rules="all" id="report_table_footer" >
                    <tfoot>
                        <td width="30" align="right"></td>
                        <td width="100" align="right"></td>
                        <td width="100" align="right"></td>
                        <td width="130" align="right"></td>
                        <td width="100" align="right"></td>
                        <td width="130" align="right"></td>
                        <td width="100" align="right"></td>
                        <td width="100" align="right"></td>
                        <td width="130" align="right"></td>
                        <td width="100" align="right">Total</td> 
                        <td width="80" align="right" id="total_cut_td" ><? echo $total_cut;?></td> 
                        <td width="80" align="right" id="total_printissue_td"><? echo $total_print_iss; ?> </td> 
                        <td width="80" align="right" id="total_printrcv_td"><?  echo $total_print_re;  ?>  </td>
                        
                        <td width="80" align="right" id="total_emb_iss"><? echo $total_emb_iss; ?> </td> 
                        <td width="80" align="right" id="total_emb_re"><?  echo $total_emb_re;  ?>  </td>
                        <td width="80" align="right" id="total_wash_iss"><? echo $total_wash_iss; ?> </td> 
                        <td width="80" align="right" id="total_wash_re"><?  echo $total_wash_re;  ?>  </td>
                        <td width="80" align="right" id="total_sp_iss"><? echo $total_sp_iss; ?> </td> 
                        <td width="80" align="right" id="total_sp_re"><?  echo $total_sp_re;  ?>  </td>
                        
                        <td width="80" align="right" id="total_sewin_inhouse_td"><? echo $total_sew_inhouse_in;  ?> </td>
                        <td width="80" align="right" id="total_sewin_outbound_td"><? echo $total_sew_outbound_in;  ?> </td>
                        <td width="80" align="right" id="total_sewin_td"><? echo $total_sew_in;  ?> </td> 
                        <td width="80" align="right" id="total_sewout_inhouse_td"><? echo $total_sew_inhouse_out;  ?> </td>
                        <td width="80" align="right" id="total_sewout_outbound_td"><? echo $total_sew_outbound_out;  ?> </td>
                        <td width="80" align="right" id="total_sewout_td"><? echo $total_sew_out;  ?> </td>
                        <td width="80" align="right" id="total_iron_in_td"><?  echo $total_in_iron; ?> </td>
                        <td width="80" align="right" id="total_iron_out_td"><?  echo $total_out_iron; ?> </td>
                        <td width="80" align="right" id="total_iron_td"><?  echo $total_iron; ?> </td>
                        <td width="80" align="right" id="total_re_iron_td"><?  echo $total_re_iron; ?> </td>  
                        <td width="80" align="right" id="total_finishin_td"><?  echo $total_finishin; ?>  </td> 
                        <td width="80" align="right" id="total_finishout_td"><?  echo $total_finishout; ?>  </td>
                        <td width="80" align="right" id="total_finish_td"><?  echo $total_finish; ?>  </td>
                          
                        <td width="80" align="right" id="total_carton_td"><? echo $total_carton; ?> </td> 
                        <td width="80" align="right" id="total_prod_dzn_td"><?  echo number_format($total_prod_dzn,2); ?> </td>
                        <td width="80" align="right" id="total_cm_value_td"><? echo number_format($total_rej_value,2); ?> </td >
                        <td width="">&nbsp;</td>
                   </tfoot>
                </table>
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
                <table width="3460px"  cellspacing="0"   >
                    <tr class="form_caption" style="border:none;">
                            <td colspan="39" align="center" style="border:none;font-size:14px; font-weight:bold"> Date Location Floor & Line Wise Production Report</td>
                     </tr>
                    <tr style="border:none;">
                            <td colspan="39" align="center" style="border:none; font-size:16px; font-weight:bold">
                                Company Name:<? echo $company_library[str_replace("'","",$cbo_company_name)]; ?>                                
                            </td>
                      </tr>
                      <tr style="border:none;">
                            <td colspan="39" align="center" style="border:none;font-size:12px; font-weight:bold">
                            	<? echo "From $fromDate To $toDate" ;?>
                            </td>
                      </tr>
                </table>
                <table width="3460" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1">
                   <thead>
                        <th width="30">Sl.</th>    
                        <th width="100">Working Factory</th>
                        <th width="100">Job No</th>
                        <th width="130">Order Number</th>
                        <th width="100">Buyer Name</th>
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
                        <th width="80">Sent to prnt</th>
                        <th width="80">Rev prn/Emb</th>
                        
                        <th width="80">Sent to Emb</th>
                        <th width="80">Rev Emb</th>
                        <th width="80">Sent to Wash</th>
                        <th width="80">Rev Wash</th>
                        <th width="80">Sent to Sp. Works</th>
                        <th width="80">Rev Sp. Works</th>
                        
                        <th width="80">Sewing Input (Inhouse)</th>
                        <th width="80">Sewing Input (Out-bound)</th>
                        <th width="80">Total Sewing Input</th>
                        <th width="80">Sewing Out (Inhouse)</th>
                        <th width="80">Sewing Out (Out-bound)</th>
                        <th width="80">Total Sewing Out</th>
                        <th width="80">Iron Qty (Inhouse)</th>
                        <th width="80">Iron Qty (Out-bound)</th>
                        <th width="80">Total Iron Qty</th>
                        <th width="80">Re-Iron Qty </th>
                        <th width="80">Finish Qty (Inhouse)</th>
                        <th width="80">Finish Qty (Out-bound)</th>
                        <th width="80">Total Finish Qty</th>
                        <th width="80">Today Carton</th>
                        <th width="80">Prod/Dzn</th>
                      	<th width="">Remarks</th>
                    </thead>
                </table>
                <div style="max-height:425px; overflow-y:scroll; width:3460px" id="scroll_body2">
                    <table cellspacing="0" border="1" class="rpt_table"  width="3440" rules="all" id="table_body" >
                    <?
					$cutting_array=array();
					$printing_array=array();
					$printreceived_array=array();
					
					$emb_array=array();
					$embreceived_array=array();
					$wash_array=array();
					$washreceived_array=array();
					$sp_array=array();
					$washreceived_array=array();
					
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
					$finishin_array=array();
					$finishout_array=array();
					$finish_array=array();
					$carton_array=array();
					
					$sql_cal="select a.location_id,b.date_calc, b.day_status from  lib_capacity_calc_mst a, lib_capacity_calc_dtls b where a.id=b.mst_id  and a.status_active=1 and a.is_deleted=0 and a.comapny_id=$cbo_company_name and b.day_status=2 $day_check $year_cond group by b.date_calc,a.location_id, b.day_status";
						$result_data=sql_select($sql_cal);
						$off_day_check=array();
						foreach($result_data as $row)
						{
						$off_day_check[$row[csf('date_calc')]][$row[csf('location_id')]]['day']=$row[csf('day_status')];	
						}
						if($db_type==2) $day_chek="TO_CHAR(c.production_hour,'HH24:MI')";
						else  if($db_type==0) $day_chek="TIME_FORMAT(c.production_hour, '%H:%i' )";
						
						$sql_order=sql_select("SELECT  c.location,c.po_break_down_id as po_id,c.production_date,c.item_number_id,c.floor_id,c.sewing_line,
						(CASE WHEN production_type ='1' THEN $day_chek ELSE null END) AS cutting_hr,
						(CASE WHEN production_type ='7' THEN $day_chek ELSE null END) AS iron_hr,
						(CASE WHEN production_type ='7' THEN $day_chek ELSE null END) AS re_iron_hr,
						(CASE WHEN production_type ='8' THEN $day_chek ELSE null END) AS finish_hr
						from pro_garments_production_mst c where  c.company_id=$cbo_company_name and  c.is_deleted=0 and c.status_active=1  and c.production_type ='1' $txt_date  $floor_name $location_cond $garmentsNature ");
						$day_checkarr=array();$prod_arr=array();
						foreach($sql_order as $row)
						{
						$prod_arr[$row[csf('po_id')]][$row[csf('production_date')]][$row[csf('item_number_id')]][$row[csf('floor_id')]][$row[csf('sewing_line')]]['1']=$row[csf('cutting_hr')];
						$prod_arr[$row[csf('po_id')]][$row[csf('production_date')]][$row[csf('item_number_id')]][$row[csf('floor_id')]][$row[csf('sewing_line')]]['7']=$row[csf('iron_hr')];
						$prod_arr[$row[csf('po_id')]][$row[csf('production_date')]][$row[csf('item_number_id')]][$row[csf('floor_id')]][$row[csf('sewing_line')]]['7']=$row[csf('re_iron_hr')];
						$prod_arr[$row[csf('po_id')]][$row[csf('production_date')]][$row[csf('item_number_id')]][$row[csf('floor_id')]][$row[csf('sewing_line')]]['8']=$row[csf('finish_hr')];
						$prod_arr[$row[csf('po_id')]][$row[csf('production_date')]][$row[csf('item_number_id')]][$row[csf('floor_id')]][$row[csf('sewing_line')]]['location']=$row[csf('location')];
						}
					
					$sql_order=sql_select("SELECT c.po_break_down_id,c.production_date, c.item_number_id,c.location,c.floor_id,c.sewing_line,
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
					sum(CASE WHEN c.production_source=1 and c.production_type ='8' THEN c.production_quantity ELSE 0 END) AS finishin_qnty, 
					sum(CASE WHEN c.production_source=3 and c.production_type ='8' THEN c.production_quantity ELSE 0 END) AS finishout_qnty, 
					sum(CASE WHEN c.production_type ='8' THEN c.production_quantity ELSE 0 END) AS finish_qnty, 
					sum(c.carton_qty) as carton_qty
								from 
									wo_po_break_down a,wo_po_details_master b, pro_garments_production_mst c 
								where 
									a.job_no_mst=b.job_no and a.id=c.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $txt_date $company_name $buyer_name $floor_name $location_cond $garmentsNature  
									group by c.po_break_down_id,c.production_date,c.item_number_id,c.location,c.floor_id,c.sewing_line");
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
						
						$iron_in_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]][$sql_result[csf("location")]][$sql_result[csf("floor_id")]][$sql_result[csf("sewing_line")]]['7']=$sql_result[csf("iron_in_qnty")];
						$iron_out_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]][$sql_result[csf("location")]][$sql_result[csf("floor_id")]][$sql_result[csf("sewing_line")]]['7']=$sql_result[csf("iron_out_qnty")];
						$iron_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]][$sql_result[csf("location")]][$sql_result[csf("floor_id")]][$sql_result[csf("sewing_line")]]['7']=$sql_result[csf("iron_qnty")];
						
						$re_iron_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]][$sql_result[csf("location")]][$sql_result[csf("floor_id")]][$sql_result[csf("sewing_line")]]['7']=$sql_result[csf("re_iron_qnty")];
						$finishin_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]][$sql_result[csf("location")]][$sql_result[csf("floor_id")]][$sql_result[csf("sewing_line")]]['8']=$sql_result[csf("finishin_qnty")];
						$finishout_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]][$sql_result[csf("location")]][$sql_result[csf("floor_id")]][$sql_result[csf("sewing_line")]]['8']=$sql_result[csf("finishout_qnty")];
						$finish_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]][$sql_result[csf("location")]][$sql_result[csf("floor_id")]][$sql_result[csf("sewing_line")]]['8']=$sql_result[csf("finish_qnty")];
						$carton_array[$sql_result[csf("po_break_down_id")]][$sql_result[csf("production_date")]][$sql_result[csf("item_number_id")]][$sql_result[csf("location")]][$sql_result[csf("floor_id")]][$sql_result[csf("sewing_line")]]=$sql_result[csf("carton_qty")];
					}
                    $total_cut=0; $total_print_iss=0; $total_print_re=0; $total_sew_inhouse_in=0; $total_sew_outbound_in=0; $total_sew_input=0; $total_sew_inhouse_out=0; $total_sew_outbound_out=0; $total_sew_out=0; $total_in_iron=0; $total_out_iron=0; $total_iron=0; $total_re_iron=0; $total_finish=0; $total_finishin=0; $total_finishout=0; $total_carton=0; $total_prod_dzn=0; 
					$i=1;
					
					if($garments_nature!="") $garments_nature=" and c.garments_nature=$garments_nature";
					
					if($db_type==0)
					{
                    	$pro_date_sql=sql_select("SELECT b.job_no_prefix_num, a.job_no_mst, a.po_number,a.grouping,a.file_no, a.po_quantity, b.order_uom, b.buyer_name, b.style_ref_no as style, b.company_name as company_name, c.garments_nature, c.po_break_down_id, c.item_number_id, c.production_source, c.serving_company, c.location, c.embel_name, c.embel_type, c.production_date, c.production_quantity, c.production_type, c.entry_break_down_type, c.production_hour, c.sewing_line, c.supervisor, c.carton_qty, c.remarks, c.floor_id, c.alter_qnty, c.reject_qnty, c.prod_reso_allo
					from 
						wo_po_break_down a,wo_po_details_master b, pro_garments_production_mst c 
					where 
						a.job_no_mst=b.job_no and a.id=c.po_break_down_id and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $txt_date $company_name $buyer_name $garmentsNature $floor_name $location_cond $file_no_cond $internal_ref_cond  group by c.po_break_down_id,c.production_date, c.item_number_id,c.location,c.floor_id,c.sewing_line order by c.production_date");
					}
					else
					{
						$pro_date_sql=sql_select("SELECT b.job_no_prefix_num, a.po_number,a.grouping,a.file_no, b.buyer_name, b.style_ref_no as style, b.company_name as company_name, c.garments_nature, c.po_break_down_id, c.item_number_id, c.production_source, c.location, c.production_date, c.sewing_line, c.carton_qty, c.floor_id, c.prod_reso_allo
					from 
						wo_po_break_down a,wo_po_details_master b, pro_garments_production_mst c 
					where 
						a.job_no_mst=b.job_no and a.id=c.po_break_down_id and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $txt_date $company_name $buyer_name $garmentsNature  $floor_name $location_cond $file_no_cond $internal_ref_cond  group by b.job_no_prefix_num, a.po_number,a.grouping,a.file_no, b.buyer_name, b.style_ref_no, b.company_name, c.garments_nature, c.po_break_down_id, c.item_number_id, c.production_source, c.location, c.production_date, c.sewing_line, c.carton_qty, c.floor_id, c.prod_reso_allo");
					}
                    //echo $pro_date_sql;die;
 					foreach($pro_date_sql as $pro_date_sql_row)
                    {
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
                   	 	
						$cutting_hour=$prod_arr[$pro_date_sql_row[csf('po_break_down_id')]][$pro_date_sql_row[csf('production_date')]][$pro_date_sql_row[csf('item_number_id')]][$pro_date_sql_row[csf('location')]][$pro_date_sql_row[csf('floor_id')]]['1'];
						$iron_hour=$prod_arr[$pro_date_sql_row[csf('po_break_down_id')]][$pro_date_sql_row[csf('production_date')]][$pro_date_sql_row[csf('item_number_id')]][$pro_date_sql_row[csf('location')]][$pro_date_sql_row[csf('floor_id')]]['7'];
						$re_iron_hour=$prod_arr[$pro_date_sql_row[csf('po_break_down_id')]][$pro_date_sql_row[csf('production_date')]][$pro_date_sql_row[csf('item_number_id')]][$pro_date_sql_row[csf('location')]][$pro_date_sql_row[csf('floor_id')]]['7'];
						$finish_hr=$prod_arr[$pro_date_sql_row[csf('po_break_down_id')]][$pro_date_sql_row[csf('production_date')]][$pro_date_sql_row[csf('item_number_id')]][$pro_date_sql_row[csf('location')]][$pro_date_sql_row[csf('floor_id')]]['8'];
						
						
						$off_days_check=$off_day_check[$pro_date_sql_row[csf('production_date')]][$pro_date_sql_row[csf('location')]]['day'];
						
						if($off_days_check!=2)
						{
							$sentto_print=$printing_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]][$pro_date_sql_row[csf("location")]][$pro_date_sql_row[csf("floor_id")]][$pro_date_sql_row[csf("sewing_line")]]['2'];
							$recv_print=$printreceived_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]][$pro_date_sql_row[csf("location")]][$pro_date_sql_row[csf("floor_id")]][$pro_date_sql_row[csf("sewing_line")]]['3'];
							$embl_issue=$emb_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]][$pro_date_sql_row[csf("location")]][$pro_date_sql_row[csf("floor_id")]][$pro_date_sql_row[csf("sewing_line")]]['2'];
							
							$embl_recv=$embreceived_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]][$pro_date_sql_row[csf("location")]][$pro_date_sql_row[csf("floor_id")]][$pro_date_sql_row[csf("sewing_line")]]['3'];
							$wash_issue=$wash_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]][$pro_date_sql_row[csf("location")]][$pro_date_sql_row[csf("floor_id")]][$pro_date_sql_row[csf("sewing_line")]]['2'];
							$wash_recv=$washreceived_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]][$pro_date_sql_row[csf("location")]][$pro_date_sql_row[csf("floor_id")]][$pro_date_sql_row[csf("sewing_line")]]['3'];
							$sp_issue=$sp_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]][$pro_date_sql_row[csf("location")]][$pro_date_sql_row[csf("floor_id")]][$pro_date_sql_row[csf("sewing_line")]]['2'];
							$sp_recv=$spreceived_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]][$pro_date_sql_row[csf("location")]][$pro_date_sql_row[csf("floor_id")]][$pro_date_sql_row[csf("sewing_line")]]['3'];
							$sewingin_inhouse=$sewingin_inhouse_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]][$pro_date_sql_row[csf("location")]][$pro_date_sql_row[csf("floor_id")]][$pro_date_sql_row[csf("sewing_line")]]['4'];
							$sewingout_inhouse=$sewingout_inhouse_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]][$pro_date_sql_row[csf("location")]][$pro_date_sql_row[csf("floor_id")]][$pro_date_sql_row[csf("sewing_line")]]['5'];
							
							
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
							 $fqty=$finishin_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]][$pro_date_sql_row[csf("location")]][$pro_date_sql_row[csf("floor_id")]][$pro_date_sql_row[csf("sewing_line")]]['8'];
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
						if($pro_date_sql_row[csf("sewing_line")]!="") $swing_line_id=$pro_date_sql_row[csf("sewing_line")]; else  $swing_line_id=0;
						
                    ?>
                    	<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                            <td width="30"><? echo $i;?></td>
                            <td width="100"><p><? echo $company_short_library[$pro_date_sql_row[csf("company_name")]]; ?></p></td>
                            <td width="100"><p><? echo $pro_date_sql_row[csf("job_no_prefix_num")];?></p></td>
                            <td width="130"><p><a href="##" onclick="openmypage_order(<? echo $pro_date_sql_row[csf("po_break_down_id")];?>,<? echo $pro_date_sql_row[csf("item_number_id")]; ?>,'orderQnty_popup');" ><? echo $pro_date_sql_row[csf("po_number")]; ?></a></p></td>
                            <td width="100"><p><? echo $buyer_short_library[$pro_date_sql_row[csf("buyer_name")]]; ?></p></td>
                            <td width="130"><p><? echo $pro_date_sql_row[csf("style")]; ?></p></td>
                            <td width="100"><p><? echo $pro_date_sql_row[csf("file_no")]; ?></p></td>
                            <td width="100"><p><? echo $pro_date_sql_row[csf("grouping")]; ?></p></td>
                            <td width="130"><p><? echo $garments_item[$pro_date_sql_row[csf("item_number_id")]]; ?></p></td>
                            <td width="100"><p><? echo change_date_format($pro_date_sql_row[csf("production_date")]); ?></p></td>
                            <td width="100"><p><? echo $knitting_source[$pro_date_sql_row[csf("production_source")]]; ?></p></td>
                            
                            <td width="100"><p><? echo $location_library[$pro_date_sql_row[csf("location")]]; ?></p></td>
                            <td width="100"><p><? echo $floor_library[$pro_date_sql_row[csf("floor_id")]]; ?></p></td>
                            <td width="100" align="center"><p><? echo $sewing_line; ?></p></td>
                            
                            <td width="80" align="right"><a href="##" onClick="openmypage_popup_location(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,<? echo $pro_date_sql_row[csf("location")];?>,<? echo $pro_date_sql_row[csf("floor_id")];?>,<? echo $swing_line_id;?>,'Cutting Info','cutting_popup_location');" >
                            	<?
									echo $cutting_qty;  
									$total_cut+=$cutting_qty;
								?></a>
                            </td>
                            <td width="80" align="right"><a href="##" onClick="openmypage_popup_location(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,<? echo $pro_date_sql_row[csf("location")];?>,<? echo $pro_date_sql_row[csf("floor_id")];?>,'<? echo $pro_date_sql_row[csf("sewing_line")];?>','Printing Issue Info','printing_issue_popup_location');" >
                            	<?
									echo $sentto_print;  
									$total_print_iss+=$sentto_print;
								?></a>
                            </td>
                            <td width="80" align="right"><a href="##" onClick="openmypage_popup_location(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,<? echo $pro_date_sql_row[csf("location")];?>,<? echo $pro_date_sql_row[csf("floor_id")];?>,'<? echo $pro_date_sql_row[csf("sewing_line")];?>','Printing Receive Info','printing_receive_popup_location');" >
                            	<?
									echo $recv_print;  
									$total_print_re+=$recv_print;  
								?></a>
                            </td>
                            
                            <td width="80" align="right"><a href="##" onClick="openmypage_popup_location(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,<? echo $pro_date_sql_row[csf("location")];?>,<? echo $pro_date_sql_row[csf("floor_id")];?>,'<? echo $pro_date_sql_row[csf("sewing_line")];?>','Embroidery Issue Info','embroi_issue_popup_location');" >
								<?
									echo $embl_issue;  
									$total_emb_iss+=$embl_issue;
								?></a>
							</td>
                            <td width="80" align="right"><a href="##" onClick="openmypage_popup_location(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,<? echo $pro_date_sql_row[csf("location")];?>,<? echo $pro_date_sql_row[csf("floor_id")];?>,'<? echo $pro_date_sql_row[csf("sewing_line")];?>','Embroidery Receive Info','embroi_receive_popup_location');" >
                            	<?
									echo $embl_recv;  
									$total_emb_re+=$embl_recv; 
								?></a>
                            </td>
                            <td width="80" align="right"><a href="##" onClick="openmypage_popup_location(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,<? echo $pro_date_sql_row[csf("location")];?>,<? echo $pro_date_sql_row[csf("floor_id")];?>,'<? echo $pro_date_sql_row[csf("sewing_line")];?>','Wash Issue Info','wash_issue_popup_location');" >
								<?
									echo $wash_issue;  
									$total_wash_iss+=$wash_issue;
								?></a>
							</td>
                            <td width="80" align="right"><a href="##" onClick="openmypage_popup_location(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,<? echo $pro_date_sql_row[csf("location")];?>,<? echo $pro_date_sql_row[csf("floor_id")];?>,'<? echo $pro_date_sql_row[csf("sewing_line")];?>','Wash Receive Info','wash_receive_popup_location');" >
                            	<?
									echo $wash_recv;  
									$total_wash_re+=$wash_recv; 
								?></a>
                            </td>
                            <td width="80" align="right"><a href="##" onClick="openmypage_popup_location(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,<? echo $pro_date_sql_row[csf("location")];?>,<? echo $pro_date_sql_row[csf("floor_id")];?>,'<? echo $pro_date_sql_row[csf("sewing_line")];?>','Spetial Work Info','sp_issue_popup_location');" >
								<?
									echo $sp_issue;  
									$total_sp_iss+=$sp_issue;
								?></a>
							</td>
                            <td width="80" align="right"><a href="##" onClick="openmypage_popup_location(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,<? echo $pro_date_sql_row[csf("location")];?>,<? echo $pro_date_sql_row[csf("floor_id")];?>,'<? echo $pro_date_sql_row[csf("sewing_line")];?>','Spetial Work Info','sp_receive_popup_location');" >
                            	<?
									echo $sp_recv;  
									$total_sp_re+=$sp_recv; 
								?></a>
                            </td>
                            <td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,'1','4','sewingQnty_popup');" >
                            	<?
									$sewingin_inhouse=$sewingin_inhouse;
									echo $sewingin_inhouse; $total_sew_inhouse_in+=$sewingin_inhouse; 
								?>
                            </a></td>
                            <td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,'3','4','sewingQnty_popup');" >
                            	<?
									$sewingin_outbound=0;
									echo $sewingin_outbound; $total_sew_outbound_in+=$sewingin_outbound; 
								?>
                            </a></td>
                            <td width="80" align="right"><? $sewing_input_total=$sewingin_inhouse+$sewingin_outbound; echo $sewing_input_total; $total_sew_in+=$sewing_input_total; ?></td>
                            <td width="80" align="right"><a href="##" onclick="openmypage_sew_output2(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,'1','5','sewingQnty_popup',<? echo $pro_date_sql_row[csf("location")];?>,<? echo $pro_date_sql_row[csf("floor_id")];?>,'<? echo $pro_date_sql_row[csf('sewing_line')];?>');" >
                            	<?
									$sewingout_inhouse=$sewingout_inhouse;
									echo $sewingout_inhouse; $total_sew_inhouse_out+=$sewingout_inhouse; 
								?>
                            </a></td>
                            <td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,'3','5','sewingQnty_popup');" >
                            	<?
									$sewingout_outbound=0;
									echo $sewingout_outbound; $total_sew_outbound_out+=$sewingout_outbound; 
								?>
                            </a></td>
                            <td width="80" align="right"><? $sewing_output_total=$sewingout_inhouse+$sewingout_outbound; echo $sewing_output_total; $total_sew_out+=$sewing_output_total; ?></td>
                            
                            <td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,'1','0','ironQnty_popup');" >
                            	<?
								$iron_in_qty=0;
								$iron_in_qty=$tot_iron;  
								echo $iron_in_qty; $total_in_iron+=$iron_in_qty;  
								?></a>
                            </td>
                            <td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,'3','0','ironQnty_popup');" >
                            	<?
								$iron_out_qty=0;
								$iron_out_qty=0;  
								echo $iron_out_qty; $total_out_iron+=$iron_out_qty;  
								?></a>
                            </td>
                            <td width="80" align="right"><? $iron_qty=0;
								$iron_qty=$tot_iron;  
								echo $iron_qty; $total_iron+=$iron_qty; ?>
                            </td>
                            <td width="80" align="right">
                            	<?
									echo $re_iron_qty;  
									$total_re_iron+=$re_iron_qty;  
								?>
                            </td>
                            <td width="80" align="right">
                            	<?
									echo $fqty;  
									$total_finishin+=$fqty; 
								?>
                            </td>
                            <td width="80" align="right">
                            	<?
									echo $fqty_out=0;  
									$total_finishout+=$fqty_out; 
								?>
                            </td>
                            <td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $pro_date_sql_row[csf("po_break_down_id")]; ?>,'<? echo $pro_date_sql_row[csf("production_date")]; ?>',<? echo $pro_date_sql_row[csf("item_number_id")];?>,'0','0','finishQnty_popup');" >
                            	<?
									echo $fqty;  
									$total_finish+=$fqty; 
								?></a>
                            </td>
                            <td width="80" align="right">
                            	<?
									echo $carton_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]][$pro_date_sql_row[csf("location")]][$pro_date_sql_row[csf("floor_id")]][$pro_date_sql_row[csf("sewing_line")]];  
									$total_carton+=$carton_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]][$pro_date_sql_row[csf("location")]][$pro_date_sql_row[csf("floor_id")]][$pro_date_sql_row[csf("sewing_line")]]; 
								?>
                            </td>
                            <? $prod_dzn=$sewingout_array[$pro_date_sql_row[csf("po_break_down_id")]][$pro_date_sql_row[csf("production_date")]][$pro_date_sql_row[csf("item_number_id")]][$pro_date_sql_row[csf("location")]][$pro_date_sql_row[csf("floor_id")]][$pro_date_sql_row[csf("sewing_line")]]['5'] / 12 ; $total_prod_dzn+=$prod_dzn; ?>
                            
                            <td width="80" align="right"><? if($prod_dzn!=0) echo number_format($prod_dzn,2); else echo "0"; ?></td>
                            <td width=""><a href="##"  onclick="openmypage_remark(<? echo $pro_date_sql_row[csf("po_break_down_id")];?>,'date_wise_production_report');" > Veiw </a>
                            </td>
                   	 </tr>
						<?
						
					$i++;
						} // Off day Check end
				}//end foreach 1st
				
				?>
                </table>
                </div>
                <table width="3460" cellspacing="0" border="1" class="tbl_bottom" rules="all" id="report_table_footer" >
                    <tfoot>
                        <td width="30" align="right"></td>
                        <td width="100" align="right"></td>
                        <td width="100" align="right"></td>
                        <td width="130" align="right"></td>
                        <td width="100" align="right"></td>
                        <td width="130" align="right"></td>
                        <td width="100" align="right"></td>
                        <td width="100" align="right"></td>
                        <td width="130" align="right"></td>
                        <td width="100" align="right"></td>
                        <td width="100" align="right"></td>
                        <td width="100" align="right"></td>
                        <td width="100" align="right"></td> 
                        <td width="100" align="right">Total</td>
                        <td width="80" align="right" id="total_cut_td"><? echo $total_cut;?></td> 
                        <td width="80" align="right" id="total_printissue_td"><? echo $total_print_iss; ?> </td> 
                        <td width="80" align="right" id="total_printrcv_td"><?  echo $total_print_re;  ?>  </td>
                        
                        <td width="80" align="right" id="total_emb_iss"><? echo $total_emb_iss; ?> </td> 
                        <td width="80" align="right" id="total_emb_re"><?  echo $total_emb_re;  ?>  </td>
                        <td width="80" align="right" id="total_wash_iss"><? echo $total_wash_iss; ?> </td> 
                        <td width="80" align="right" id="total_wash_re"><?  echo $total_wash_re;  ?>  </td>
                        <td width="80" align="right" id="total_sp_iss"><? echo $total_sp_iss; ?> </td> 
                        <td width="80" align="right" id="total_sp_re"><?  echo $total_sp_re;  ?>  </td>
                        
                        <td width="80" align="right" id="total_sewin_inhouse_td"><? echo $total_sew_inhouse_in; ?></td>
                        <td width="80" align="right" id="total_sewin_outbound_td"><? echo $total_sew_outbound_in; ?></td>
                        <td width="80" align="right" id="total_sewin_td"><? echo $total_sew_input; ?></td>
                        <td width="80" align="right" id="total_sewout_inhouse_td"><? echo $total_sew_inhouse_out; ?></td>
                        <td width="80" align="right" id="total_sewout_outbound_td"><? echo $total_sew_outbound_out; ?></td>
                        <td width="80" align="right" id="total_sewout_td"><? echo $total_sew_out;  ?></td>
                        <td width="80" align="right" id="total_iron_in_td"><? echo $total_in_iron; ?></td>
                        <td width="80" align="right" id="total_iron_out_td"><? echo $total_out_iron; ?></td>
                        <td width="80" align="right" id="total_iron_td"><? echo $total_iron; ?></td> 
                        <td width="80" align="right" id="total_re_iron_td"><? echo $total_re_iron; ?></td> 
                        <td width="80" align="right" id="total_finishin_td"><? echo $total_finishin; ?></td> 
                        <td width="80" align="right" id="total_finishout_td"><? echo $total_finishout; ?></td> 
                        <td width="80" align="right" id="total_finish_td"><? echo $total_finish; ?></td> 
                          
                        <td width="80" align="right" id="total_carton_td"><? echo $total_carton; ?> </td> 
                        <td width="80" align="right" id="total_prod_dzn_td"><?  echo number_format($total_prod_dzn,2); ?> </td>
                        <td>&nbsp;</td>
                     </tfoot>
                </table>
          </div>
		<?
		}// end if condition of type
		?>
            <br />
            <div align="left" style="width:300px; background-color:#FCF"><strong>Production-Subcontract Order (Inbound) Details</strong></div>
            <div>
                <table width="1400" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1">
                    <thead>
                        <th width="30">Sl.</th>    
                        <th width="100">Working Factory</th>
                        <th width="100">Job No</th>
                        <th width="130">Order No</th>
                        <th width="100">Buyer </th>
                        <th width="130">Style </th>
                        <th width="130">Item Name</th>
                        <th width="75">Production Date</th>
                        <th width="100">Sewing Line</th>
                        <th width="90">Cutting</th>
                        <th width="90">Sewing Output</th>
                        <th width="90">Iron Output</th>
                        <th width="90">Gmts. Finishing</th>
                        <th width="">Remarks</th>
                    </thead>
                </table>
            <div style="max-height:300px; overflow-y:scroll; width:1420px" id="scroll_body">
                <table border="1" class="rpt_table"  width="1400" rules="all" id="sub_list_view" >
                      <? 
                        //sql_select
                       // if($type==1) //--------------------------------------------Show Order Wise  $type==1 
                       // {
                        $item_id_arr=return_library_array( "select id,const_comp from lib_subcon_charge",'id','const_comp');
						
						$production_array=array();
						if($db_type==0)
						{
							$prod_sql= "SELECT c.order_id, c.production_date,
								sum(CASE WHEN c.production_type ='1' THEN  c.production_qnty  ELSE 0 END) AS cutting_qnty,
								sum(CASE WHEN c.production_type ='2' THEN  c.production_qnty  ELSE 0 END) AS sewingout_qnty,
								sum(CASE WHEN c.production_type ='3' THEN  c.production_qnty  ELSE 0 END) AS ironout_qnty,
								sum(CASE WHEN c.production_type ='4' THEN  c.production_qnty  ELSE 0 END) AS gmts_fin_qnty
							from 
								subcon_gmts_prod_dtls c
							where c.status_active=1 and c.is_deleted=0 and c.production_date between $txt_date_from and $txt_date_to   group by c.order_id, c.production_date";
						}
						else
						{
							 $prod_sql= "SELECT c.order_id, c.production_date,
								NVL(sum(CASE WHEN c.production_type ='1' THEN  c.production_qnty  ELSE 0 END),0) AS cutting_qnty,
								NVL(sum(CASE WHEN c.production_type ='2' THEN  c.production_qnty  ELSE 0 END),0) AS sewingout_qnty,
								NVL(sum(CASE WHEN c.production_type ='3' THEN  c.production_qnty  ELSE 0 END),0) AS ironout_qnty,
								NVL(sum(CASE WHEN c.production_type ='4' THEN  c.production_qnty  ELSE 0 END),0) AS gmts_fin_qnty
							from 
								subcon_gmts_prod_dtls c
							where c.status_active=1 and c.is_deleted=0 and c.production_date between $txt_date_from and $txt_date_to   group by c.order_id, c.production_date";
						}
						$prod_sql_result= sql_select($prod_sql);
						//echo $prod_sql;//die;
						foreach($prod_sql_result as $proRes)
						{
							$production_array[$proRes[csf("order_id")]][$proRes[csf("production_date")]]['cutting_qnty']=$proRes[csf("cutting_qnty")];
							$production_array[$proRes[csf("order_id")]][$proRes[csf("production_date")]]['sewingout_qnty']=$proRes[csf("sewingout_qnty")];
							$production_array[$proRes[csf("order_id")]][$proRes[csf("production_date")]]['ironout_qnty']=$proRes[csf("ironout_qnty")];
							$production_array[$proRes[csf("order_id")]][$proRes[csf("production_date")]]['gmts_fin_qnty']=$proRes[csf("gmts_fin_qnty")];
						}
						
						if($db_type==0)
						{	
                        	$order_sql= "select c.id, c.order_no, c.cust_style_ref, a.company_id, a.party_id, a.location_id, a.job_no_prefix_num, b.gmts_item_id, b.line_id, sum(CASE WHEN b.order_id=c.id THEN b.production_qnty ELSE 0 END) AS production_qnty, b.production_date, b.prod_reso_allo
                        from 
                            subcon_ord_mst a, subcon_gmts_prod_dtls b, subcon_ord_dtls c
                        where 
                             b.order_id=c.id and b.production_date between $txt_date_from and $txt_date_to and a.company_id=$cbo_company_name $sub_floor_name $sub_location_cond and a.subcon_job=c.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.order_id, b.production_date order by b.production_date";
						}
						else
						{
							 $order_sql= "select c.id, c.order_no, c.cust_style_ref, a.company_id, a.party_id, a.location_id, a.job_no_prefix_num, b.gmts_item_id, b.line_id, sum(CASE WHEN b.order_id=c.id THEN b.production_qnty ELSE 0 END) AS production_qnty,  b.production_date, b.prod_reso_allo
                        from 
                            subcon_ord_mst a, subcon_gmts_prod_dtls b, subcon_ord_dtls c
                        where 
                             b.order_id=c.id and b.production_date between $txt_date_from and $txt_date_to  and a.company_id=$cbo_company_name 
and a.subcon_job=c.job_no_mst $sub_floor_name $sub_location_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.order_id, c.id, c.order_no, c.cust_style_ref, a.company_id, a.job_no_prefix_num, a.party_id, a.company_id, a.party_id, a.location_id, b.gmts_item_id, b.line_id, b.production_date, b.prod_reso_allo order by c.id";
						}

                   // echo $order_sql;//die;
                       
						$order_sql_result=sql_select($order_sql);
                           $j=0;$k=0;
                           foreach($order_sql_result as $orderRes)
                           {
                               $j++;
                               if ($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
							   
                                $sewing_line='';
								if($pro_date_sql_row[csf('prod_reso_allo')]==1)
								{
									$line_number=explode(",",$prod_reso_arr[$orderRes[csf("line_id")]]);
									foreach($line_number as $val)
									{
										if($sewing_line=='') $sewing_line=$line_library[$val]; else $sewing_line.=", ".$line_library[$val];
									}
								}
								else $sewing_line=$line_library[$orderRes[csf("line_id")]]; 
								
                                ?>
                                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_3nd<? echo $j; ?>','<? echo $bgcolor; ?>')" id="tr_3nd<? echo $j; ?>" style="height:20px">
                                    <td width="30" ><? echo $j; ?></td>    
                                    <td width="100"><p><? echo $company_short_library[$orderRes[csf("company_id")]]; ?></p></td>
                                    <td width="100" align="center"><p><? echo $orderRes[csf("job_no_prefix_num")]; ?></p></td>
                                    <td width="130"><p><? echo $orderRes[csf("order_no")]; ?></p></td>
                                    <td width="100"><? echo $buyer_short_library[$orderRes[csf("party_id")]]; ?></td>
                                    <td width="130"><p><? echo $orderRes[csf("cust_style_ref")]; ?></p></td>
                                    <td width="130"><p><? echo $garments_item[$orderRes[csf("gmts_item_id")]];?></p></td>
                                    <td width="75" bgcolor="<? echo $color; ?>"><? echo change_date_format($orderRes[csf("production_date")]);  ?></td>
                                    <td width="100" align="center"><? echo $sewing_line; ?></td>
                                    <td width="90" align="right"><? echo $production_array[$orderRes[csf("id")]][$orderRes[csf("production_date")]]['cutting_qnty']; $total_cutt+=$production_array[$orderRes[csf("id")]][$orderRes[csf("production_date")]]['cutting_qnty']; ?></td>
                                    <td width="90" align="right"><? echo $production_array[$orderRes[csf("id")]][$orderRes[csf("production_date")]]['sewingout_qnty']; $total_sew+=$production_array[$orderRes[csf("id")]]['sewingout_qnty']; ?></td>
                                    <td width="90" align="right"><? echo $production_array[$orderRes[csf("id")]][$orderRes[csf("production_date")]]['ironout_qnty']; $total_iron_sub+=$production_array[$orderRes[csf("id")]]['ironout_qnty']; ?></td>
                                    <td width="90" align="right"><? echo $production_array[$orderRes[csf("id")]][$orderRes[csf("production_date")]]['gmts_fin_qnty']; $total_gmtfin+=$production_array[$orderRes[csf("id")]]['gmts_fin_qnty']; ?></td>

                                    <td width="">&nbsp;</td>
                                 </tr>
                            <?
                           }
                          ?>  
                        </table>
                        </div>	
                        <table border="1" class="tbl_bottom"  width="1400" rules="all" id="report_table_footer_1" >
                            <tr>
                                <td width="30"></td>
                                <td width="100"></td>
                                <td width="100"></td>
                                <td width="130"></td>
                                <td width="100"></td>
                                <td width="130">Total</td>
                                <td width="130" id="total_ord_quantity"><? echo $total_ord_quantity; ?></td>
                                <td width="75"></td>
                                <td width="100"></td>
                                <td width="90" id="total_cutt"><? echo $total_cutt; ?></td>
                                <td width="90" id="total_sew"><? echo $total_sew; ?></td>
                                <td width="90" id="total_iron_sub"><? echo $total_iron_sub; ?></td>
                                <td width="90" id="total_gmtfin"><? echo $total_gmtfin; ?></td>
                                <td width=""></td>
                             </tr>
                     </table>
			  </div>		
		<?
	}
		//-------------------------------------------END Show Date Location Floor & Line Wise------------------------
		//---------end------------//
	$html = ob_get_contents();
	ob_clean();
	$new_link=create_delete_report_file( $html, 1, 1, "../../../" );
	echo "$html";
	exit();	
}

if($action=="orderQnty_popup")
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	
	$sql= "SELECT b.id,b.po_number,b.pub_shipment_date,b.po_quantity*(select from wo_po_details_mas_set_details set where set.job_no=a.job_no and set.gmts_item_id=$gmts_item_id) as po_quantity from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id='$po_break_down_id' and a.garments_nature='$garments_nature' and a.is_deleted=0 and a.status_active=1";
	//echo $sql;
	echo "<br />". create_list_view ( "list_view", "Order No,Order Qnty,Pub Shipment Date", "200,120,220","540","220",1, "SELECT b.id,b.po_number,b.pub_shipment_date,b.po_quantity*a.total_set_qnty as po_quantity from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id='$po_break_down_id' and a.is_deleted=0 and a.status_active=1", "", "","", 1, '0,0,0', $arr, "po_number,po_quantity,pub_shipment_date","../requires/buyer_date_wise_prod_without_cm_report_controller", '','0,1,3');
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
    <legend>Iron Qty.</legend>
    	<? 
			  $sql= "SELECT production_date,production_quantity,remarks from pro_garments_production_mst where po_break_down_id='$po_break_down_id' and production_type='7' and is_deleted=0 and status_active=1";
			  
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


//cutting popup
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
	//and c.status_active=1 and c.is_deleted=0 
	$sql_color_size= sql_select("SELECT a.country_id, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id 
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c 
	where a.production_type=1 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and  a.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
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
	if($sewing_line==0 || $sewing_line=="") $sewing_cond=""; else $sewing_cond=" and a.sewing_line=$sewing_line";
	
	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");
	$sql_color_size= sql_select("SELECT a.country_id, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id 
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c 
	where a.production_type=1 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' $location_cond $floor_cond $sewing_cond and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
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
	where a.production_type=2 and a.embel_name=1 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' $location_cond $floor_cond $sewing_cond  and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  
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
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>Day Total:</th>
                <?
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th><? echo $color_size_in[$size_id]; ?></th>
                    <?
				}
				?>
                <th><? echo $grand_tot_in; ?></th>
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
exit();	
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
exit();	
}


if($action=="sewingQnty_popup")
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name"); 
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"); 
	$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name");
	$floor_library=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name");
	$sewing_line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name");
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	
	$sizearr_order=return_library_array("select size_number_id, size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");
	//var_dump();die;
	
	$po_details_sql=sql_select("select a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.id=$po_break_down_id group by a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id");
	//echo $po_details_sql;die;
	//For Show Date Location and Floor 
	if($location_id!=0) $location_cond=" and a.location in($location_id)"; else  $location_cond=""; 
	if($floor_id!=0) $floor_cond=" and a.floor_id in($floor_id)"; else  $floor_cond="";
	if($sewing_line!=0) $sewing_line_cond=" and a.sewing_line in($sewing_line)"; else  $sewing_line_cond="";
	
	if($db_type==2)
	{
		$sql= "SELECT  a.challan_no, a.floor_id, a.sewing_line, a.country_id, a.production_source, a.serving_company, c.color_number_id, LISTAGG(CAST(c.size_number_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.size_number_id) as size_number_id, a.prod_reso_allo  
		from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c 
		where a.production_type=$page and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.production_source='$prod_source' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $location_cond  $floor_cond $sewing_line_cond 
		group by  a.country_id,a.challan_no, a.floor_id, a.sewing_line, a.production_source, a.serving_company, a.prod_reso_allo,c.color_number_id
		order by a.country_id,a.challan_no, a.floor_id, a.sewing_line";
	}
	else if($db_type==0)
	{
		$sql= "SELECT a.challan_no, a.floor_id, a.sewing_line, a.country_id, a.production_source, a.serving_company, c.color_number_id, group_concat(c.size_number_id) as size_number_id, a.prod_reso_allo  
		from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c 
		where a.production_type=$page and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.production_source='$prod_source' and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $location_cond  $floor_cond $sewing_line_cond 
		group by  a.country_id,a.challan_no, a.floor_id, a.sewing_line, a.production_source, a.serving_company,a.prod_reso_allo,c.color_number_id
		order by a.country_id,a.challan_no, a.floor_id, a.sewing_line";
	}
	//echo $sql;die;
	
	
	//echo $sql; //and a.production_date='$production_date'
	$result=sql_select($sql);
	
	$sql_color_size= sql_select("SELECT a.challan_no, a.floor_id, a.sewing_line, a.country_id, a.production_source, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id 
	from pro_garments_production_mst a, pro_garments_production_dtls b,  wo_po_color_size_breakdown c 
	where a.production_type=$page and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.production_source='$prod_source' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $location_cond  $floor_cond  $sewing_line_cond
	group by   a.country_id,a.challan_no, a.floor_id, a.sewing_line, a.production_source,c.color_number_id,c.size_number_id");
	
	foreach($sql_color_size as $row)
	{
		$production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('sewing_line')]][$row[csf('production_source')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
		$summery_data[$row[csf('color_number_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}
	
	$col_width=60*count($sizearr_order);
	//$table_width=630+$col_width;
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
                    <td align="center"><p><? echo $sewing_line;  ?></p></td>
                    <td ><p><? echo $colorarr[$row[csf("color_number_id")]];  ?></p></td>
                    <?
					$color_total_in=0;
                    foreach($sizearr_order as $size_id)
                    {
						$Production_qty=0;
                        ?>
                        <td align="right"><p>
						<?
							$Production_qty=$production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('sewing_line')]][$row[csf('production_source')]][$row[csf('color_number_id')]][$size_id];
						 	echo number_format($Production_qty,0);
							 $color_total_in+=$Production_qty; $color_size_in [$size_id]+=$Production_qty; $line_color_total_in+=$Production_qty; $line_color_size_in [$size_id]+=$Production_qty;
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
exit();	
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
		$sql= "SELECT a.challan_no,a.floor_id, a.sewing_line,a.country_id,a.production_source, c.color_number_id, group_concat(c.size_number_id) as size_number_id, a.prod_reso_allo 
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
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
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
                    <td><p><? echo $country_library[$row[csf("country_id")]];  ?></p></td>
                    <td><p><? echo $knitting_source[$row[csf("production_source")]]; ?><p></td>
                    <td><p><? echo $row[csf("challan_no")];  ?></p></td>
                    <td><p><? echo $floor_library[$row[csf("floor_id")]];  ?></p></td>
                    <td align="center"><p><? echo $sewing_line; ?></p></td>
                    <td><p><? echo $colorarr[$row[csf("color_number_id")]];  ?></p></td>
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
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
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
                <th>&nbsp;</th>
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
                	<th><? echo $color_size_in[$size_id]; ?></th>
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
	
	if($prod_source=='' || $prod_source==0) $prod_source_cond=""; else  $prod_source_cond=" and production_source='$prod_source'";
	//echo $sql; //and a.production_date='$production_date'
	$result=sql_select($sql);
	
	$sql_color_size= sql_select("SELECT a.challan_no, a.floor_id, a.country_id, c.color_number_id, c.size_number_id, sum(b.production_qnty) as production_qnty
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c 
	where a.production_type=7 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $prod_source_cond
	group by a.country_id, a.challan_no, a.floor_id, a.sewing_line, c.color_number_id, c.size_number_id");
	
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
                    <? if($prod_source==1) $prod_source_caption="In-House"; else if($prod_source==3) $prod_source_caption="Out-Bound"; ?>
                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>"><? echo $prod_source_caption; ?></th>
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
							$production_break_qty=0;
							$production_break_qty=$production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('color_number_id')]][$size_id];
						 	echo number_format($production_break_qty,0) ;
							
							 $color_total+= $production_break_qty; 
							 $color_size_in [$size_id]+=$production_break_qty;
						 ?>
                        </p></td>
                        <?
                    }
                    ?>
                    <td align="right"><p><? echo number_format($color_total,0); $grand_tot_in+=$color_total; ?></p></td>
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

if($action=="finishQnty_popup")
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
		where a.production_type=8 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
		group by  a.challan_no, a.floor_id, a.country_id, c.color_number_id
		order by a.country_id, a.challan_no, a.floor_id";
	}
	else if($db_type==0)
	{
		$sql= "SELECT a.challan_no, a.floor_id, a.country_id, c.color_number_id, group_concat(c.size_number_id) as size_number_id
		from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c 
		where a.production_type=8 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
		group by a.country_id,a.challan_no, a.floor_id, c.color_number_id
		order by a.country_id,a.challan_no, a.floor_id, a.sewing_line";
	}
	//echo $sql;die;
	
	
	//echo $sql; //and a.production_date='$production_date'
	$result=sql_select($sql);
	
	$sql_color_size= sql_select("SELECT a.challan_no, a.floor_id, a.country_id, c.color_number_id, c.size_number_id,
	 sum(case when production_source=1 then b.production_qnty else 0 end) as in_quantity,
	 sum(case when production_source=3 then b.production_qnty else 0 end) as out_quantity,
	 sum(b.production_qnty) as production_qnty
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c 
	where a.production_type=8 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
	group by a.country_id,a.challan_no, a.floor_id, a.sewing_line, a.production_source,c.color_number_id,c.size_number_id");
	
	foreach($sql_color_size as $row)
	{
		$production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['in'] +=$row[csf('in_quantity')];
		$production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['out'] +=$row[csf('out_quantity')];
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
                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">In-House</th>
                    <th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Out-Bound</th>
                    <th width="80" rowspan="3" >Total</th>
                </tr>
                <tr>
                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
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
					$color_total_in=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><p>
						<?
							$production_break_qty_in=0;
							$production_break_qty_in=$production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('color_number_id')]][$size_id]['in'];
						 	echo number_format($production_break_qty_in,0) ;
							
							 $color_total_in+= $production_break_qty_in; 
							 $color_size_in [$size_id]+=$production_break_qty_in;
						 ?>
                        </p></td>
                        <?
                    }
					$color_total_out=0;
					foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><p>
						<?
							$production_break_qty_out=0;
							$production_break_qty_out=$production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('color_number_id')]][$size_id]['out'];
						 	echo number_format($production_break_qty_out,0) ;
							
							 $color_total_out+= $production_break_qty_out; 
							 $color_size_out[$size_id]+=$production_break_qty_out;
						 ?>
                        </p></td>
                        <?
                    }
                    ?>
                    <td align="right"><p><? $color_total=$color_total_in+$color_total_out; echo  number_format( $color_total,0); $grand_tot_in+=$color_total; ?></p></td>
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
				foreach($sizearr_order as $size_id)
                {
                    ?>
                    <th align="right"><? echo number_format($color_size_out[$size_id],0); ?></th>
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

