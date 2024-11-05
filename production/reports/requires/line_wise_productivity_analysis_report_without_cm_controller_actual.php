<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "","");  
	exit();
}

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 140, "select id,location_name from lib_location where status_active=1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/line_wise_productivity_analysis_report_without_cm_controller', this.value, 'load_drop_down_floor', 'floor_td' );load_drop_down( 'requires/line_wise_productivity_analysis_report_without_cm_controller',document.getElementById('cbo_floor_id').value+'_'+this.value+'_'+document.getElementById('cbo_company_id').value+'_'+document.getElementById('txt_date').value, 'load_drop_down_line', 'line_td' );",0 ); 
	exit();    	 
}

if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor_id", 130, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "load_drop_down( 'requires/line_wise_productivity_analysis_report_without_cm_controller', this.value+'_'+document.getElementById('cbo_location_id').value+'_'+document.getElementById('cbo_company_id').value+'_'+document.getElementById('txt_date').value, 'load_drop_down_line', 'line_td' );",0 );     	 	
	exit();    	 
}

if ($action=="load_drop_down_line")
{
	$explode_data = explode("_",$data);
	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$explode_data[2] and variable_list=23 and is_deleted=0 and status_active=1");
	$txt_date = $explode_data[3];
	
	$cond="";
	if($prod_reso_allo==1)
	{
		$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );
		$line_array=array();
		
		if($txt_date=="")
		{
			if( $explode_data[0]==0 && $explode_data[1]!=0 ) $cond = " and location_id= $explode_data[1]";
			if( $explode_data[0]!=0 ) $cond = " and floor_id= $explode_data[0]";
			$line_data=sql_select("select id, line_number from prod_resource_mst where is_deleted=0 $cond");
		}
		else
		{
			if( $explode_data[0]==0 && $explode_data[1]!=0 ) $cond = " and a.location_id= $explode_data[1]";
			if( $explode_data[0]!=0 ) $cond = " and a.floor_id= $explode_data[0]";
			
			if($db_type==0)
			{
				$line_data=sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and b.pr_date='".change_date_format($txt_date,'yyyy-mm-dd')."' and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number");
			}
			else
			{
				$line_data=sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and b.pr_date='".change_date_format($txt_date,'','',1)."' and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number");	
			}
		}
		
		foreach($line_data as $row)
		{
			$line='';
			$line_number=explode(",",$row[csf('line_number')]);
			foreach($line_number as $val)
			{
				if($line=='') $line=$line_library[$val]; else $line.=",".$line_library[$val];
			}
			$line_array[$row[csf('id')]]=$line;
		}

		echo create_drop_down( "cbo_line", 120,$line_array,"", 1, "-- Select Line --", $selected, "",0,0 );
	}
	else
	{
		if( $explode_data[0]==0 && $explode_data[1]!=0 ) $cond = " and location_name= $explode_data[1]";
		if( $explode_data[0]!=0 ) $cond = " and floor_name= $explode_data[0]";

		echo create_drop_down( "cbo_line", 120, "select id,line_name from lib_sewing_line where is_deleted=0 and status_active=1 and floor_name!=0 $cond order by line_name","id,line_name", 1, "-- Select Line --", $selected, "",0,0 );
	}
	exit();
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$companyArr = return_library_array("select id,company_name from lib_company","id","company_name"); 
	$buyerArr = return_library_array("select id,short_name from lib_buyer","id","short_name"); 
	$locationArr = return_library_array("select id,location_name from lib_location","id","location_name"); 
	$floorArr = return_library_array("select id,floor_name from lib_prod_floor","id","floor_name"); 
	$lineArr = return_library_array("select id,line_name from lib_sewing_line","id","line_name"); 

	$type=str_replace("'","",$type);
	$comapny_id=str_replace("'","",$cbo_company_id);
	
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";
	}
	
	if(str_replace("'","",$cbo_location_id)==0) $location="%%"; else $location=str_replace("'","",$cbo_location_id);
	if(str_replace("'","",$cbo_floor_id)==0) $floor="%%"; else $floor=str_replace("'","",$cbo_floor_id);
    if(str_replace("'","",$cbo_line)==0) $line="%%"; else $line=str_replace("'","",$cbo_line);
	
	if($type==1)
	{
		$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$comapny_id and variable_list=23 and is_deleted=0 and status_active=1");
	
		if($db_type==0)
		{
			$job_po_id_arr=return_library_array( "select job_no_mst, group_concat(id) as po_id from wo_po_break_down group by job_no_mst",'job_no_mst','po_id');
		}
		else
		{
			$job_po_id_arr=return_library_array("select job_no_mst,LISTAGG(id,',') WITHIN GROUP (ORDER BY id) as po_id from wo_po_break_down group by job_no_mst",'job_no_mst','po_id');
		}
		
		$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
		
		$prod_resource_array=array();
		$dataArray=sql_select("select a.id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power, b.operator, b.helper, b.line_chief, b.smv_adjust, b.smv_adjust_type, b.target_per_hour, b.working_hour from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.company_id=$comapny_id and b.is_deleted=0");
		foreach($dataArray as $row)
		{
			$conv_pr_date=change_date_format($row[csf('pr_date')]);
			$prod_resource_array[$row[csf('id')]][$conv_pr_date]['man_power']=$row[csf('man_power')];
			$prod_resource_array[$row[csf('id')]][$conv_pr_date]['operator']=$row[csf('operator')];
			$prod_resource_array[$row[csf('id')]][$conv_pr_date]['helper']=$row[csf('helper')];
			$prod_resource_array[$row[csf('id')]][$conv_pr_date]['line_chief']=$row[csf('line_chief')];
			$prod_resource_array[$row[csf('id')]][$conv_pr_date]['active_machine']=$row[csf('active_machine')];
			$prod_resource_array[$row[csf('id')]][$conv_pr_date]['tpd']=$row[csf('target_per_hour')]*$row[csf('working_hour')];
			$prod_resource_array[$row[csf('id')]][$conv_pr_date]['tsmv']=$row[csf('man_power')]*$row[csf('working_hour')]*60;
			$prod_resource_array[$row[csf('id')]][$conv_pr_date]['smv_adjust']=$row[csf('smv_adjust')];
			$prod_resource_array[$row[csf('id')]][$conv_pr_date]['smv_adjust_type']=$row[csf('smv_adjust_type')];
		}
		
		$pr_date=change_date_format(str_replace("'","",$txt_date));
		if($db_type==0)
		{
			$sql="select a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.order_uom, a.gmts_item_id, a.set_break_down, a.total_set_qnty as ratio, group_concat(distinct(b.id)) as po_id, group_concat(concat_ws('**',b.id,b.po_number,b.po_quantity)) as po_data, c.item_number_id, c.location, c.floor_id, c.prod_reso_allo, c.sewing_line, sum(c.production_quantity) as qnty from wo_po_details_master a, wo_po_break_down b, pro_garments_production_mst c where a.job_no=b.job_no_mst and a.company_name='$comapny_id' and b.id=c.po_break_down_id and c.location like '$location' and c.floor_id like '$floor' and c.sewing_line like '$line' and production_date=$txt_date and production_type=5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $buyer_id_cond group by a.job_no, c.location, c.floor_id, c.prod_reso_allo, c.sewing_line order by c.location, c.floor_id, c.sewing_line";//, c.item_number_id
		}
		else
		{
			$sql="select a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.order_uom, a.gmts_item_id, a.set_break_down, a.total_set_qnty as ratio, LISTAGG(b.id, ',') WITHIN GROUP (ORDER BY b.id) as po_id, LISTAGG(cast(b.id || '**' || b.po_number || '**' || b.po_quantity as varchar2(4000)), ',') WITHIN GROUP (ORDER BY b.id) as po_data, c.item_number_id, c.location, c.floor_id, c.prod_reso_allo, c.sewing_line, sum(c.production_quantity) as qnty from wo_po_details_master a, wo_po_break_down b, pro_garments_production_mst c where a.job_no=b.job_no_mst and a.company_name='$comapny_id' and b.id=c.po_break_down_id and c.location like '$location' and c.floor_id like '$floor' and c.sewing_line like '$line' and production_date=$txt_date and production_type=5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $buyer_id_cond group by a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.order_uom, a.gmts_item_id, a.set_break_down, a.total_set_qnty, c.location, c.floor_id, c.prod_reso_allo, c.sewing_line, c.item_number_id order by c.location, c.floor_id, c.sewing_line";	
		}
	
		//echo $sql;
		$result = sql_select($sql);
		$i=1; $k=1; $html=''; $location_array=array(); $floor_array=array(); $buyer_data_array=array();
		$total_order_qnty=0; $total_machine_qnty=0; $tot_worker=0; $tot_operator=0; $tot_helper=0; $tot_prev_input_qnty=0; $tot_prev_out_qnty=0; 
		$tot_prev_wip=0; $tot_today_target=0; $tot_today_input_qnty=0; $tot_today_out_qnty=0; $tot_today_smv=0; $tot_item_smv=0; $tot_achv_smv=0; 
		$tot_cm_value=0; $grand_tot_prod=0; $tot_wip=0; $grand_tot_smv_used=0; $grand_tot_achv_smv=0;
		
		foreach( $result as $row )
		{
			if(in_array($row[csf("location")], $location_array))
			{
				if(!in_array($row[csf("floor_id")], $floor_array))
				{
					if($k!=1)
					{
						$floor_today_ach_perc=$floor_today_out_qnty/$floor_today_target*100;
						$floor_today_aff_perc=$floor_achv_smv/$floor_today_smv*100;
						$floor_avg_aff_perc=$floor_tot_achv_smv/$floor_tot_smv_used*100;
						
						$html.='<tr bgcolor="#CCCCCC">
								<td colspan="6" align="right">Floor Total</td>
								<td align="right">&nbsp;</td>
								<td align="right">'.$floor_machine_qnty.'&nbsp;</td>
								<td align="right">'.$floor_worker.'&nbsp;</td>
								<td align="right">'.$floor_operator.'&nbsp;</td>
								<td align="right">'.$floor_helper.'&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td align="right">'.$floor_prev_input_qnty.'&nbsp;</td>
								<td align="right">'.$floor_prev_out_qnty.'&nbsp;</td>
								<td align="right">'.$floor_prev_wip.'&nbsp;</td>
								<td align="right">'.$floor_today_target.'&nbsp;</td>
								<td align="right">'.$floor_today_input_qnty.'&nbsp;</td>
								<td align="right">'.$floor_today_out_qnty.'&nbsp;</td>
								<td align="right">'.number_format($floor_today_aff_perc,2,'.','').'&nbsp;</td>
								<td align="right">'.$floor_today_smv.'&nbsp;</td>
								<td align="right">'.$floor_item_smv.'&nbsp;</td>
								<td align="right">'.$floor_achv_smv.'&nbsp;</td>
								<td align="right">'.number_format($floor_today_ach_perc,2,'.','').'&nbsp;</td>
								<td align="right">'.$floor_tot_prod.'&nbsp;</td>
								<td>&nbsp;</td>
								<td align="right">'.$floor_wip.'&nbsp;</td>
								<td align="right">'.number_format($floor_tot_smv_used,2,'.','').'&nbsp;</td>
								<td align="right">'.number_format($floor_tot_achv_smv,2,'.','').'&nbsp;</td>
								<td align="right">'.number_format($floor_avg_aff_perc,2,'.','').'&nbsp;</td>
								<td>&nbsp;</td>
							</tr>';
							
							$floor_order_qnty=0; 
							$floor_machine_qnty=0; 
							$floor_worker=0; 
							$floor_operator=0; 
							$floor_helper=0; 
							$floor_prev_input_qnty=0; 
							$floor_prev_out_qnty=0;
							$floor_prev_wip=0;
							$floor_today_target=0; 
							$floor_today_input_qnty=0; 
							$floor_today_out_qnty=0; 
							$floor_today_smv=0; 
							$floor_item_smv=0; 
							$floor_achv_smv=0; 
							//$floor_cm_value=0; 
							$floor_tot_prod=0; 
							$floor_wip=0; 
							$floor_tot_smv_used=0; 
							$floor_tot_achv_smv=0;
					}
					
					$html.='<tr bgcolor="#EFEFEF"><td colspan="31"><b>Floor name: '.$floorArr[$row[csf('floor_id')]].'; Location name: '.$locationArr[$row[csf('location')]].'</b></td></tr>';
					$k++;
					$floor_array[]=$row[csf("floor_id")];
				}
			}
			else
			{
				if($k!=1)
				{
					$floor_today_ach_perc=$floor_today_out_qnty/$floor_today_target*100;
					$floor_today_aff_perc=$floor_achv_smv/$floor_today_smv*100;
					$floor_avg_aff_perc=$floor_tot_achv_smv/$floor_tot_smv_used*100;
					
					$location_today_ach_perc=$location_today_out_qnty/$location_today_target*100;
					$location_today_aff_perc=$location_achv_smv/$location_today_smv*100;
					$location_avg_aff_perc=$location_tot_achv_smv/$location_tot_smv_used*100;
					
					$html.='<tr bgcolor="#CCCCCC">
								<td colspan="6" align="right">Floor Total</td>
								<td align="right">&nbsp;</td>
								<td align="right">'.$floor_machine_qnty.'&nbsp;</td>
								<td align="right">'.$floor_worker.'&nbsp;</td>
								<td align="right">'.$floor_operator.'&nbsp;</td>
								<td align="right">'.$floor_helper.'&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td align="right">'.$floor_prev_input_qnty.'&nbsp;</td>
								<td align="right">'.$floor_prev_out_qnty.'&nbsp;</td>
								<td align="right">'.$floor_prev_wip.'&nbsp;</td>
								<td align="right">'.$floor_today_target.'&nbsp;</td>
								<td align="right">'.$floor_today_input_qnty.'&nbsp;</td>
								<td align="right">'.$floor_today_out_qnty.'&nbsp;</td>
								<td align="right">'.number_format($floor_today_aff_perc,2,'.','').'&nbsp;</td>
								<td align="right">'.$floor_today_smv.'&nbsp;</td>
								<td align="right">'.$floor_item_smv.'&nbsp;</td>
								<td align="right">'.$floor_achv_smv.'&nbsp;</td>
								<td align="right">'.number_format($floor_today_ach_perc,2,'.','').'&nbsp;</td>
								<td align="right">'.$floor_tot_prod.'&nbsp;</td>
								<td>&nbsp;</td>
								<td align="right">'.$floor_wip.'&nbsp;</td>
								<td align="right">'.number_format($floor_tot_smv_used,2,'.','').'&nbsp;</td>
								<td align="right">'.number_format($floor_tot_achv_smv,2,'.','').'&nbsp;</td>
								<td align="right">'.number_format($floor_avg_aff_perc,2,'.','').'&nbsp;</td>
								<td>&nbsp;</td>
							</tr>
							<tr bgcolor="#E9F3FF">
								<td colspan="6" align="right">Location Total</td>
								<td align="right">&nbsp;</td>
								<td align="right">'.$location_machine_qnty.'&nbsp;</td>
								<td align="right">'.$location_worker.'&nbsp;</td>
								<td align="right">'.$location_operator.'&nbsp;</td>
								<td align="right">'.$location_helper.'&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td align="right">'.$location_prev_input_qnty.'&nbsp;</td>
								<td align="right">'.$location_prev_out_qnty.'&nbsp;</td>
								<td align="right">'.$location_prev_wip.'&nbsp;</td>
								<td align="right">'.$location_today_target.'&nbsp;</td>
								<td align="right">'.$location_today_input_qnty.'&nbsp;</td>
								<td align="right">'.$location_today_out_qnty.'&nbsp;</td>
								<td align="right">'.number_format($location_today_ach_perc,2,'.','').'&nbsp;</td>
								<td align="right">'.$location_today_smv.'&nbsp;</td>
								<td align="right">'.$location_item_smv.'&nbsp;</td>
								<td align="right">'.$location_achv_smv.'&nbsp;</td>
								<td align="right">'.number_format($location_today_aff_perc,2,'.','').'&nbsp;</td>
								<td align="right">'.$location_tot_prod.'&nbsp;</td>
								<td>&nbsp;</td>
								<td align="right">'.$location_wip.'&nbsp;</td>
								<td align="right">'.number_format($location_tot_smv_used,2,'.','').'&nbsp;</td>
								<td align="right">'.number_format($location_tot_achv_smv,2,'.','').'&nbsp;</td>
								<td align="right">'.number_format($location_avg_aff_perc,2,'.','').'&nbsp;</td>
								<td>&nbsp;</td>
							</tr>';//<td align="right">'.number_format($floor_cm_value,2,'.','').'&nbsp;</td>
			   
					$location_order_qnty=0; 
					$location_machine_qnty=0; 
					$location_worker=0; 
					$location_operator=0; 
					$location_helper=0; 
					$location_prev_input_qnty=0; 
					$location_prev_out_qnty=0;
					$location_prev_wip=0;
					$location_today_target=0; 
					$location_today_input_qnty=0; 
					$location_today_out_qnty=0; 
					$location_today_smv=0; 
					$location_item_smv=0; 
					$location_achv_smv=0; 
					//$location_cm_value=0; 
					$location_tot_prod=0; 
					$location_wip=0; 
					$location_tot_smv_used=0; 
					$location_tot_achv_smv=0;
					
					$floor_order_qnty=0; 
					$floor_machine_qnty=0; 
					$floor_worker=0; 
					$floor_operator=0; 
					$floor_helper=0; 
					$floor_prev_input_qnty=0; 
					$floor_prev_out_qnty=0;
					$floor_prev_wip=0;
					$floor_today_target=0; 
					$floor_today_input_qnty=0; 
					$floor_today_out_qnty=0; 
					$floor_today_smv=0; 
					$floor_item_smv=0; 
					$floor_achv_smv=0; 
					//$floor_cm_value=0; 
					$floor_tot_prod=0; 
					$floor_wip=0; 
					$floor_tot_smv_used=0; 
					$floor_tot_achv_smv=0;	
				}
				
			  $html.='<tr bgcolor="#EFEFEF"><td colspan="31"><b>Floor name: '.$floorArr[$row[csf('floor_id')]].'; Location name: '.$locationArr[$row[csf('location')]].'</b></td></tr>';
				$k++;
				$location_array[]=$row[csf("location")];
				$floor_array=array();
				$floor_array[]=$row[csf("floor_id")];
			}
			
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			
			$po_number=''; $po_quantity=0; $item_smv=0; $po_array=array();
			 
			$exp_grmts_item = explode("__",$row[csf("set_break_down")]);
			foreach($exp_grmts_item as $value)
			{
				$grmts_item_qty = explode("_",$value);
				if($row[csf('item_number_id')]==$grmts_item_qty[0])
				{
					$set_qty=$grmts_item_qty[1];
					$item_smv=$grmts_item_qty[2];
					break;
				}
			}
			
			$po_data = explode(",",$row[csf("po_data")]);
			foreach($po_data as $val)
			{
				$po_val=explode("**",$val);
				$po_array[$po_val[0]]['no']=$po_val[1];
				$po_array[$po_val[0]]['qnty']=$po_val[2];
			}
			
			$po_ids = explode(",",$row[csf("po_id")]);
			foreach($po_ids as $id)
			{
				if($po_number=="") $po_number=$po_array[$id]['no']; else $po_number.=",".$po_array[$id]['no'];
				$po_quantity+=$po_array[$id]['qnty']*$set_qty;
			}
			
			$job_po_id=$job_po_id_arr[$row[csf("job_no")]]; 
			$job_po_id=$row[csf("po_id")]; 
			$mst_sql= "SELECT  
							min(CASE WHEN production_type ='4' THEN production_date END) AS frstinput_date,
							sum(CASE WHEN production_type ='4' and production_date=$txt_date and po_break_down_id in(".$job_po_id.") THEN production_quantity ELSE 0 END) AS today_input_qnty,
							sum(CASE WHEN production_type ='4' and production_date<$txt_date THEN production_quantity ELSE 0 END) AS prev_input_qnty,
							sum(CASE WHEN production_type ='5' and production_date<$txt_date THEN production_quantity ELSE 0 END) AS prev_out_qnty
						from 
							pro_garments_production_mst 
						where  
							po_break_down_id in(".$job_po_id.") and item_number_id='".$row[csf("item_number_id")]."' and location='".$row[csf("location")]."' and floor_id='".$row[csf("floor_id")]."' and prod_reso_allo='".$row[csf("prod_reso_allo")]."' and sewing_line='".$row[csf("sewing_line")]."' and is_deleted=0 and status_active=1"; 
			$dataArray = sql_select($mst_sql);
			
			$fstinput_date=$dataArray[0][csf('frstinput_date')]; 
			$prev_input_qnty=$dataArray[0][csf('prev_input_qnty')]; 
			$prev_out_qnty=$dataArray[0][csf('prev_out_qnty')]; 
			$prev_wip=$prev_input_qnty-$prev_out_qnty; 
			$today_input_qnty=$dataArray[0][csf('today_input_qnty')]; 
			$today_ach_perc=$row[csf('qnty')]/$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['tpd']*100;
			
			$total_adjustment=0;
			$smv_adjustmet_type=$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['smv_adjust_type'];
			if(str_replace("'","",$smv_adjustmet_type)==1) $total_adjustment=$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['smv_adjust'];
		    if(str_replace("'","",$smv_adjustmet_type)==2) $total_adjustment=($prod_resource_array[$row[csf('sewing_line')]][$pr_date]['smv_adjust'])*(-1);
			
			$today_smv=$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['tsmv']+$total_adjustment;
			$achv_smv=$row[csf('qnty')]*$item_smv; 
			$today_aff_perc=$achv_smv/$today_smv*100;
			$total_prod=$row[csf('qnty')]+$prev_out_qnty;	
			$wip=$prev_input_qnty+$today_input_qnty-$total_prod;
			
		   // $no_of_days=return_field_value("count(id)","lib_capacity_calc_dtls","date_calc between '$fstinput_date' and $txt_date and day_status=1");
			$dataLibArray=sql_select("select b.date_calc from lib_capacity_calc_mst a, lib_capacity_calc_dtls b where a.id=b.mst_id and a.comapny_id='$comapny_id' and b.date_calc between '$fstinput_date' and $txt_date and day_status=1");
			$no_of_days=0; $total_smv_used=0;
			foreach($dataLibArray as $libRow)
			{
				$date_calc=change_date_format($libRow[csf('date_calc')]);
				$smv_adjustmet_type=$prod_resource_array[$row[csf('sewing_line')]][$date_calc]['smv_adjust_type'];
				if(str_replace("'","",$smv_adjustmet_type)==1) $total_adjust=$prod_resource_array[$row[csf('sewing_line')]][$date_calc]['smv_adjust'];
				else if(str_replace("'","",$smv_adjustmet_type)==2) $total_adjust=($prod_resource_array[$row[csf('sewing_line')]][$date_calc]['smv_adjust'])*(-1);
				else $total_adjust=0;
				$total_smv_used+=$prod_resource_array[$row[csf('sewing_line')]][$date_calc]['tsmv']+$total_adjust;
				$no_of_days++;
			}
			//$total_smv_used=$today_smv*$no_of_days;
			$avg_per_day=$total_prod/$no_of_days;
			$total_smv_achv=$item_smv*$total_prod;
			$avg_aff_perc=$total_smv_achv/$total_smv_used*100;
			
			$sewing_line='';
			if($row[csf('prod_reso_allo')]==1)
			{
				$line_number=explode(",",$prod_reso_arr[$row[csf('sewing_line')]]);
				foreach($line_number as $val)
				{
					if($sewing_line=='') $sewing_line=$lineArr[$val]; else $sewing_line.=",".$lineArr[$val];
				}
			}
			else $sewing_line=$lineArr[$row[csf('sewing_line')]];
			
			$po_number=implode(",",array_unique(explode(",",$po_number)));
			$html.="<tr bgcolor='$bgcolor' onclick=change_color('tr_$i','$bgcolor') id=tr_$i>";
			$html.='<td width="40">'.$i.'</td>
						<td width="50"><p>'.$sewing_line.'</p></td>
						<td width="70"><p>'.$buyerArr[$row[csf('buyer_name')]].'</p></td>
						<td width="110"><p>'.$po_number.'</p></td>
						<td width="110"><p>'.$row[csf('style_ref_no')].'</p></td>
						<td width="140"><p>'.$garments_item[$row[csf('item_number_id')]].'</p></td>
						<td width="75" align="right">'.$po_quantity.'&nbsp;</td>
						<td width="70" align="right">'.$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['active_machine'].'&nbsp;</td>
						<td width="60" align="right">'.$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['man_power'].'&nbsp;</td>
						<td width="60" align="right">'.$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['operator'].'&nbsp;</td>
						<td width="60" align="right">'.$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['helper'].'&nbsp;</td>
						<td width="120"><p>'.$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['line_chief'].'&nbsp;</p></td>
						<td width="80" align="center">'.change_date_format($fstinput_date).'&nbsp;</td>
						<td width="75" align="right"><a href="##" onclick="openmypage('.$comapny_id.",'".$job_po_id."',".$row[csf('item_number_id')].",".$row[csf('location')].",".$row[csf('floor_id')].",".$row[csf('sewing_line')].",".$txt_date.",'prev_qnty',4".",".$row[csf('prod_reso_allo')].')">'.$prev_input_qnty.'</a>&nbsp;</td>
						<td width="75" align="right"><a href="##" onclick="openmypage('.$comapny_id.",'".$job_po_id."',".$row[csf('item_number_id')].",".$row[csf('location')].",".$row[csf('floor_id')].",".$row[csf('sewing_line')].",".$txt_date.",'prev_qnty',5".",".$row[csf('prod_reso_allo')].')">'.$prev_out_qnty.'</a>&nbsp;</td>
						<td width="75" align="right">'.$prev_wip.'&nbsp;</td>
						<td width="75" align="right">'.$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['tpd'].'&nbsp;</td>
						<td width="75" align="right">'.$today_input_qnty.'&nbsp;</td>
						<td width="75" align="right"><a href="##" onclick="openmypage('.$comapny_id.",'".$row[csf('po_id')]."',".$row[csf('item_number_id')].",".$row[csf('location')].",".$row[csf('floor_id')].",".$row[csf('sewing_line')].",".$txt_date.",'today_prod',5".",".$row[csf('prod_reso_allo')].')">'.$row[csf('qnty')].'</a>&nbsp;</td>
						<td width="75" align="right">'.number_format($today_ach_perc,2).'&nbsp;</td>
						<td width="75" align="right">'.$today_smv.'&nbsp;</p></td>
						<td width="70" align="right">'.$item_smv.'&nbsp;</td>
						<td width="100" align="right">'.$achv_smv.'&nbsp;</td>
						<td width="75" align="right">'.number_format($today_aff_perc,2).'&nbsp;</td>
						<td width="90" align="right"><a href="##" onclick="openmypage('.$comapny_id.",'".$job_po_id."',".$row[csf('item_number_id')].",".$row[csf('location')].",".$row[csf('floor_id')].",".$row[csf('sewing_line')].",".$txt_date.",'tot_prod',5".",".$row[csf('prod_reso_allo')].')">'.$total_prod.'</a>&nbsp;</td>
						<td width="80" align="right">'.number_format($avg_per_day,2,'.','').'&nbsp;</td>
						<td width="80" align="right">'.$wip.'&nbsp;</td>
						<td width="100" align="right"><a href="##" onclick="openmypage('.$comapny_id.",'".$row[csf('po_id')]."',".$row[csf('item_number_id')].",".$row[csf('location')].",".$row[csf('floor_id')].",".$row[csf('sewing_line')].",".$txt_date.",'tot_smv_used',"."'".$fstinput_date."'".",".$row[csf('prod_reso_allo')].')">'.number_format($total_smv_used,2,'.','').'</a>&nbsp;</td>
						<td width="100" align="right">'.number_format($total_smv_achv,2,'.','').'&nbsp;</td>
						<td align="right" width="100">'.number_format($avg_aff_perc,2,'.','').'&nbsp;</td>';
						
			 $total_po_id=explode(",",$row[csf("po_id")]);
			 $total_po_id=implode("*",$total_po_id);
			 $line_number_id=$row[csf('sewing_line')];
			 $html.='<td align="center"><input type="button"  value="View"  class="formbutton" onclick="show_line_remarks('.$comapny_id.",'".$total_po_id."','".$row[csf("floor_id")]."','".$line_number_id."','remarks_popup',".$txt_date.')"/></td>
			</tr>';//<td width="110" align="right">'.number_format($cm_value,2,'.','').'&nbsp;</td>
	   
			$i++;
			
			$total_order_qnty+=$po_quantity; 
			$total_machine_qnty+=$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['active_machine']; 
			$tot_worker+=$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['man_power']; 
			$tot_operator+=$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['operator']; 
			$tot_helper+=$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['helper']; 
			$tot_prev_input_qnty+=$prev_input_qnty; 
			$tot_prev_out_qnty+=$prev_out_qnty;
			$tot_prev_wip+=$prev_wip;
			$tot_today_target+=$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['tpd']; 
			$tot_today_input_qnty+=$today_input_qnty; 
			$tot_today_out_qnty+=$row[csf('qnty')]; 
			$tot_today_smv+=$today_smv; 
			$tot_item_smv+=$item_smv; 
			$tot_achv_smv+=$achv_smv; 
		   // $tot_cm_value+=$cm_value; 
			$grand_tot_prod+=$total_prod; 
			$tot_wip+=$wip; 
			$grand_tot_smv_used+=$total_smv_used; 
			$grand_tot_achv_smv+=$total_smv_achv;
			
			$location_order_qnty+=$po_quantity; 
			$location_machine_qnty+=$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['active_machine']; 
			$location_worker+=$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['man_power']; 
			$location_operator+=$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['operator']; 
			$location_helper+=$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['helper']; 
			$location_prev_input_qnty+=$prev_input_qnty; 
			$location_prev_out_qnty+=$prev_out_qnty;
			$location_prev_wip+=$prev_wip;
			$location_today_target+=$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['tpd']; 
			$location_today_input_qnty+=$today_input_qnty; 
			$location_today_out_qnty+=$row[csf('qnty')]; 
			$location_today_smv+=$today_smv; 
			$location_item_smv+=$item_smv; 
			$location_achv_smv+=$achv_smv; 
		   // $location_cm_value+=$cm_value; 
			$location_tot_prod+=$total_prod; 
			$location_wip+=$wip; 
			$location_tot_smv_used+=$total_smv_used; 
			$location_tot_achv_smv+=$total_smv_achv;
			
			$floor_order_qnty+=$po_quantity; 
			$floor_machine_qnty+=$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['active_machine']; 
			$floor_worker+=$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['man_power']; 
			$floor_operator+=$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['operator']; 
			$floor_helper+=$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['helper']; 
			$floor_prev_input_qnty+=$prev_input_qnty; 
			$floor_prev_out_qnty+=$prev_out_qnty;
			$floor_prev_wip+=$prev_wip;
			$floor_today_target+=$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['tpd']; 
			$floor_today_input_qnty+=$today_input_qnty; 
			$floor_today_out_qnty+=$row[csf('qnty')]; 
			$floor_today_smv+=$today_smv; 
			$floor_item_smv+=$item_smv; 
			$floor_achv_smv+=$achv_smv; 
		   // $floor_cm_value+=$cm_value; 
			$floor_tot_prod+=$total_prod; 
			$floor_wip+=$wip; 
			$floor_tot_smv_used+=$total_smv_used; 
			$floor_tot_achv_smv+=$total_smv_achv;
			
			$buyer_data_array[$row[csf('buyer_name')]]['toin']+=$today_input_qnty;
			$buyer_data_array[$row[csf('buyer_name')]]['topd']+=$row[csf('qnty')];
			$buyer_data_array[$row[csf('buyer_name')]]['tosmv']+=$today_smv;
			$buyer_data_array[$row[csf('buyer_name')]]['achv_smv']+=$achv_smv;
			$buyer_data_array[$row[csf('buyer_name')]]['tpd']+=$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['tpd'];
			$buyer_data_array[$row[csf('buyer_name')]]['man_power']+=$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['man_power'];
			$buyer_data_array[$row[csf('buyer_name')]]['operator']+=$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['operator'];
			$buyer_data_array[$row[csf('buyer_name')]]['helper']+=$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['helper'];
			
			if($duplicate_array[$row[csf('prod_reso_allo')]][$row[csf('sewing_line')]]=="")
			{
				$total_actual_machine_qnty+=$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['active_machine']; 
				$tot_actual_worker+=$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['man_power']; 
				$tot_actual_operator+=$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['operator']; 
				$tot_actual_helper+=$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['helper'];
				$tot_actual_today_target+=$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['tpd']; 
				$tot_actual_today_smv+=$today_smv; 
				$grand_tot_actual_smv_used+=$total_smv_used; 
				
				$duplicate_array[$row[csf('prod_reso_allo')]][$row[csf('sewing_line')]]=$row[csf('sewing_line')];
			}
		}
		
		if(count($result)>0)
		{
			$floor_today_ach_perc=$floor_today_out_qnty/$floor_today_target*100;
			$floor_today_aff_perc=$floor_achv_smv/$floor_today_smv*100;
			$floor_avg_aff_perc=$floor_tot_achv_smv/$floor_tot_smv_used*100;
			
			$location_today_ach_perc=$location_today_out_qnty/$location_today_target*100;
			$location_today_aff_perc=$location_achv_smv/$location_today_smv*100;
			$location_avg_aff_perc=$location_tot_achv_smv/$location_tot_smv_used*100;
			
			$html.='<tr bgcolor="#CCCCCC">
						<td colspan="6" align="right">Floor Total</td>
						<td align="right">&nbsp;</td>
						<td align="right">'.$floor_machine_qnty.'&nbsp;</td>
						<td align="right">'.$floor_worker.'&nbsp;</td>
						<td align="right">'.$floor_operator.'&nbsp;</td>
						<td align="right">'.$floor_helper.'&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td align="right">'.$floor_prev_input_qnty.'&nbsp;</td>
						<td align="right">'.$floor_prev_out_qnty.'&nbsp;</td>
						<td align="right">'.$floor_prev_wip.'&nbsp;</td>
						<td align="right">'.$floor_today_target.'&nbsp;</td>
						<td align="right">'.$floor_today_input_qnty.'&nbsp;</td>
						<td align="right">'.$floor_today_out_qnty.'&nbsp;</td>
						<td align="right">'.number_format($floor_today_ach_perc,2,'.','').'&nbsp;</td>
						<td align="right">'.$floor_today_smv.'&nbsp;</td>
						<td align="right">'.$floor_item_smv.'&nbsp;</td>
						<td align="right">'.$floor_achv_smv.'&nbsp;</td>
						<td align="right">'.number_format($floor_today_aff_perc,2,'.','').'&nbsp;</td>
						<td align="right">'.$floor_tot_prod.'&nbsp;</td>
						<td>&nbsp;</td>
						<td align="right">'.$floor_wip.'&nbsp;</td>
						<td align="right">'.number_format($floor_tot_smv_used,2,'.','').'&nbsp;</td>
						<td align="right">'.number_format($floor_tot_achv_smv,2,'.','').'&nbsp;</td>
						<td align="right">'.number_format($floor_avg_aff_perc,2,'.','').'&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr bgcolor="#E9F3FF">
						<td colspan="6" align="right">Location Total</td>
						<td align="right">&nbsp;</td>
						<td align="right">'.$location_machine_qnty.'&nbsp;</td>
						<td align="right">'.$location_worker.'&nbsp;</td>
						<td align="right">'.$location_operator.'&nbsp;</td>
						<td align="right">'.$location_helper.'&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td align="right">'.$location_prev_input_qnty.'&nbsp;</td>
						<td align="right">'.$location_prev_out_qnty.'&nbsp;</td>
						<td align="right">'.$location_prev_wip.'&nbsp;</td>
						<td align="right">'.$location_today_target.'&nbsp;</td>
						<td align="right">'.$location_today_input_qnty.'&nbsp;</td>
						<td align="right">'.$location_today_out_qnty.'&nbsp;</td>
						<td align="right">'.number_format($location_today_ach_perc,2,'.','').'&nbsp;</td>
						<td align="right">'.$location_today_smv.'&nbsp;</td>
						<td align="right">'.$location_item_smv.'&nbsp;</td>
						<td align="right">'.$location_achv_smv.'&nbsp;</td>
						<td align="right">'.number_format($location_today_aff_perc,2,'.','').'&nbsp;</td>
						<td align="right">'.$location_tot_prod.'&nbsp;</td>
						<td>&nbsp;</td>
						<td align="right">'.$location_wip.'&nbsp;</td>
						<td align="right">'.number_format($location_tot_smv_used,2,'.','').'&nbsp;</td>
						<td align="right">'.number_format($location_tot_achv_smv,2,'.','').'&nbsp;</td>
						<td align="right">'.number_format($location_avg_aff_perc,2,'.','').'&nbsp;</td>
						<td>&nbsp;</td>
					</tr>';//<td align="right">'.number_format($floor_cm_value,2,'.','').'&nbsp;</td>	<td align="right">'.number_format($location_cm_value,2,'.','').'&nbsp;</td>
		
		}
		ob_start();	
		?>
		<fieldset style="width:2630px">
			<table width="2620" cellpadding="0" cellspacing="0"> 
				<tr class="form_caption">
					<td colspan="30" align="center"><strong><? echo $report_title; ?></strong></td> 
				</tr>
				<tr class="form_caption">
					<td colspan="31" align="center"><strong><? echo $companyArr[$comapny_id]; ?></strong></td> 
				</tr>
			</table>
			<table id="table_header_1" class="rpt_table" width="2620" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
					<th width="40">SL</th>
					<th width="50">Line No</th>
					<th width="70">Buyer</th>
					<th width="110">Order No</th>
					<th width="110">Style Ref.</th>
					<th width="140">Garments Item</th>
					<th width="75">Order Qnty</th>
					<th width="70">Machine Qnty</th>
					<th width="60">Worker</th>
					<th width="60">Operator</th>
					<th width="60">Helper</th>
					<th width="120">Line Chief</th>
					<th width="80">1st Input Date</th>
					<th width="75">Prev. Input Qnty</th>
					<th width="75">Prev. Prod. Qnty</th>
					<th width="75">Prev. WIP</th>
					<th width="75">Today Target</th>
					<th width="75">Today Input</th>
					<th width="75">Today Prod.</th>
					<th width="75">Today Achv. %</th>
					<th width="75">Today SMV</th>
					<th width="70">Item SMV</th>
					<th width="100">Achieved SMV</th>
					<th width="75">Today Eff. %</th>
					<th width="90">Total Prod.</th>
					<th width="80">Avg. Prod./Day</th>
					<th width="80">WIP</th>
					<th width="100">TTL SMV Used</th>
					<th width="100">TTL SMV Achv.</th>
					<th width="100">Avg. Eff. %</th>
                    <th> Remarks</th>
				</thead>
			</table>
			<div style="width:2620px; max-height:400px; overflow-y:scroll" id="scroll_body">
				<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
					<? echo $html; ?>
					<tfoot>
                    	<?
							$grand_today_ach_perc=$tot_today_out_qnty/$tot_actual_today_target*100;
							$grand_today_aff_perc=$tot_achv_smv/$tot_actual_today_smv*100;
							$grand_avg_aff_perc=$grand_tot_achv_smv/$grand_tot_actual_smv_used*100;
                        ?>
						<tr>
							<th colspan="6" align="right">Actual Total</th>
							<th align="right"><? //echo $total_order_qnty; ?>&nbsp;</th>
							<th align="right"><? echo $total_actual_machine_qnty; ?>&nbsp;</th>
							<th align="right"><? echo $tot_actual_worker; ?>&nbsp;</th>
							<th align="right"><? echo $tot_actual_operator; ?>&nbsp;</th>
							<th align="right"><? echo $tot_actual_helper; ?>&nbsp;</th>
							<th>&nbsp;</th>
							<th>&nbsp;</th>
							<th align="right"><? echo $tot_prev_input_qnty; ?>&nbsp;</th>
							<th align="right"><? echo $tot_prev_out_qnty; ?>&nbsp;</th>
							<th align="right"><? echo $tot_prev_wip; ?>&nbsp;</th>
							<th align="right"><? echo $tot_actual_today_target; ?>&nbsp;</th>
							<th align="right"><? echo $tot_today_input_qnty; ?>&nbsp;</th>
							<th align="right"><? echo $tot_today_out_qnty; ?>&nbsp;</th>
							<th align="right"><? echo number_format($grand_today_ach_perc,2,'.',''); ?>&nbsp;</th>
							<th align="right"><? echo $tot_actual_today_smv; ?>&nbsp;</th>
							<th align="right"><? echo $tot_item_smv; ?>&nbsp;</th>
							<th align="right"><? echo $tot_achv_smv; ?>&nbsp;</th>
							<th align="right"><? echo number_format($grand_today_aff_perc,2,'.',''); ?>&nbsp;</th>
							<th align="right"><? echo $grand_tot_prod; ?>&nbsp;</th>
							<th>&nbsp;</th>
							<th align="right"><? echo $tot_wip; ?>&nbsp;</th>
							<th align="right"><? echo number_format($grand_tot_actual_smv_used,2,'.',''); ?>&nbsp;</th>
							<th align="right"><? echo number_format($grand_tot_achv_smv,2,'.',''); ?>&nbsp;</th>
							<th align="right"><? echo number_format($grand_avg_aff_perc,2,'.',''); ?>&nbsp;</th>
                            <th>&nbsp;</th>
						</tr>
					</tfoot>
				</table>
			</div>
		</fieldset>
         <br/>
         <fieldset style="width:950px">
			<label><b>No Production Line</b></label>
        	<table id="table_header_1" class="rpt_table" width="930" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
					<th width="40">SL</th>
					<th width="100">Line No</th>
					<th width="100">Floor</th>
					<th width="75">Man Power</th>
					<th width="75">Operator</th>
					<th width="75">Helper</th>
                    <th width="75">Working Hour</th>
					<th>Remarks</th>
					
				</thead>
			</table>
			<div style="width:950px; max-height:400px; overflow-y:scroll" id="scroll_body">
				<table class="rpt_table" width="930" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
                <? 
				$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
			    $sql_active_line=sql_select("select sewing_line,sum(production_quantity) as total_production from pro_garments_production_mst  where production_date=".$txt_date." and production_type=5 and status_active=1 and is_deleted=0 group by  sewing_line");

				//$actual_line_arr=array();
				foreach($sql_active_line as $inf)
				{	
				   if(str_replace("","",$inf[csf('sewing_line')])!="")
				   {
						if(str_replace("","",$actual_line_arr)!="") $actual_line_arr.=",";
					    $actual_line_arr.="'".$inf[csf('sewing_line')]."'";
				   }
				}
						//echo $actual_line_arr;die;
			$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$comapny_id and variable_list=23 and is_deleted=0 and status_active=1");
			$floorArr = return_library_array("select id,floor_name from lib_prod_floor","id","floor_name");
			 if($actual_line_arr!="") $line_cond=" and a.id not in ($actual_line_arr)";
			 $dataArray=sql_select("select a.id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power, b.operator, b.helper, b.line_chief, b.target_per_hour, b.working_hour,d.remarks from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_time d where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and a.company_id=$comapny_id and b.pr_date=".$txt_date." and d.shift_id=1 and a.is_deleted=0 and b.is_deleted=0 $line_cond");
					$j=1; $location_array=array(); $floor_array=array();
					foreach( $dataArray as $row )
					{
						if ($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tbltr_<? echo $j; ?>','<? echo $bgcolor; ?>')" id="tbltr_<? echo $j; ?>">
                        	<td width="40"><? echo $j; ?></td>
                            <td width="100"><p><? echo $lineArr[$row[csf('line_number')]]; ?>&nbsp;</p></td>
                            <td width="100" align="right"><p>&nbsp;<? echo $floorArr[$row[csf('floor_id')]]; ?></p></td>
                            <td width="75" align="right">&nbsp;<? echo $row[csf('man_power')]; ?></th>
                            <td width="75" align="right">&nbsp;<? echo $row[csf('operator')]; ?></th>
                            <td width="75" align="right">&nbsp;<? echo $row[csf('helper')]; ?></td>
                            <td width="75" align="right">&nbsp;<? echo $row[csf('working_hour')]; ?></td>
                            <td><? echo $row[csf('remarks')]; ?>&nbsp;</td>
                        </tr>
                    <?
						$j++;
						
					}
					
			
				?>
					
				</table>
			</div>
		</fieldset>
	<?    
	}
	else
	{
		$poDataArr=array();
		$poData=sql_select("select a.job_no, a.buyer_name, a.style_ref_no, a.total_set_qnty as ratio, b.id, b.po_number, b.po_quantity from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name='$comapny_id'");
		foreach($poData as $row)
		{
			$poDataArr[$row[csf('id')]]['no']=$row[csf('po_number')];
			$poDataArr[$row[csf('id')]]['job_no']=$row[csf('job_no')];
			$poDataArr[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
			$poDataArr[$row[csf('id')]]['style_ref']=$row[csf('style_ref_no')];
			$poDataArr[$row[csf('id')]]['qty']=$row[csf('ratio')]*$row[csf('po_quantity')];
		}
		
		$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$comapny_id and variable_list=23 and is_deleted=0 and status_active=1");
		if($prod_reso_allo!=1) 
		{
			echo "<div style='width:1320px;' align='center'><font style='color:#FF0000; font-size:16px'>Set Variable Settings Yes For Production Resource Allocation</font></div>"; 
			die;
		}
		
		$inputDataArray=array();
		$prod_sql="select po_break_down_id, item_number_id, location, floor_id, sewing_line, production_quantity as qnty from pro_garments_production_mst where company_id='$comapny_id' and production_date=$txt_date and production_type=4 and status_active=1 and is_deleted=0";
		$dataArray=sql_select($prod_sql);
		foreach($dataArray as $row)
		{
			$inputDataArray[$row[csf('location')]][$row[csf('floor_id')]][$row[csf('sewing_line')]]['qnty']+=$row[csf('qnty')];
			$inputDataArray[$row[csf('location')]][$row[csf('floor_id')]][$row[csf('sewing_line')]]['po_id'].=$row[csf('po_break_down_id')].",";
			$inputDataArray[$row[csf('location')]][$row[csf('floor_id')]][$row[csf('sewing_line')]]['item_id'].=$row[csf('item_number_id')].",";
		}
		
		$sql="select a.id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power, b.operator, b.helper, b.line_chief, b.target_per_hour, b.working_hour from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.company_id=$comapny_id and a.location_id like '$location' and a.floor_id like '$floor' and a.id like '$line' and pr_date=$txt_date order by a.location_id, a.floor_id, a.line_number";
		$result=sql_select($sql);
		ob_start();	
		?>
        <fieldset style="width:1310px">
			<table width="1300" cellpadding="0" cellspacing="0"> 
				<tr class="form_caption">
					<td colspan="14" align="center"><strong><? echo $report_title; ?></strong></td> 
				</tr>
				<tr class="form_caption">
					<td colspan="14" align="center"><strong><? echo $companyArr[$comapny_id]; ?></strong></td> 
				</tr>
			</table>
        	<table id="table_header_1" class="rpt_table" width="1300" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
					<th width="40">SL</th>
					<th width="80">Line No</th>
					<th width="70">Buyer</th>
					<th width="130">Order No</th>
					<th width="130">Style Ref.</th>
					<th width="150">Garments Item</th>
					<th width="80">Order Qty.</th>
					<th width="75">Machine Qty.</th>
					<th width="75">Worker</th>
					<th width="75">Operator</th>
					<th width="75">Helper</th>
                    <th width="75">Working Hour</th>
					<th width="120">Line Chief</th>
					<th>Input Qty.</th>
				</thead>
			</table>
			<div style="width:1300px; max-height:400px; overflow-y:scroll" id="scroll_body">
				<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
                <? 
					$i=1; $location_array=array(); $floor_array=array();
					foreach( $result as $row )
					{
						if(in_array($row[csf("location_id")], $location_array))
						{
							if(!in_array($row[csf("floor_id")], $floor_array))
							{
								if($i!=1)
								{
									echo '<tr bgcolor="#CCCCCC">
											<td colspan="7" align="right">Floor Total</td>
											<td align="right">'.$floor_machine_qnty.'</td>
											<td align="right">'.$floor_worker.'</td>
											<td align="right">'.$floor_operator.'</td>
											<td align="right">'.$floor_helper.'</td>
											<td align="right">'.$floor_working_hour.'</td>
											<td>&nbsp;</td>
											<td align="right">'.$floor_input_qnty.'</td>
										</tr>';
										
										$floor_machine_qnty=0; 
										$floor_worker=0; 
										$floor_operator=0; 
										$floor_helper=0; 
										$floor_working_hour=0;
										$floor_input_qnty=0; 	
								}
								
								echo '<tr bgcolor="#EFEFEF"><td colspan="14"><b>Floor name: '.$floorArr[$row[csf('floor_id')]].'; Location name: '.$locationArr[$row[csf('location_id')]].'</b></td></tr>';
								$k++;
								$floor_array[]=$row[csf("floor_id")];
							}
						}
						else
						{
							if($i!=1)
							{
								echo '<tr bgcolor="#CCCCCC">
											<td colspan="7" align="right">Floor Total</td>
											<td align="right">'.$floor_machine_qnty.'</td>
											<td align="right">'.$floor_worker.'</td>
											<td align="right">'.$floor_operator.'</td>
											<td align="right">'.$floor_helper.'</td>
											<td align="right">'.$floor_working_hour.'</td>
											<td>&nbsp;</td>
											<td align="right">'.$floor_input_qnty.'</td>
										</tr>
										<tr bgcolor="#E9F3FF">
											<td colspan="7" align="right">Location Total</td>
											<td align="right">'.$location_machine_qnty.'</td>
											<td align="right">'.$location_worker.'</td>
											<td align="right">'.$location_operator.'</td>
											<td align="right">'.$location_helper.'</td>
											<td align="right">'.$location_working_hour.'</td>
											<td>&nbsp;</td>
											<td align="right">'.$location_input_qnty.'</td>
										</tr>
										';

								$floor_machine_qnty=0; 
								$floor_worker=0; 
								$floor_operator=0; 
								$floor_helper=0; 
								$floor_working_hour=0;
								$floor_input_qnty=0; 
								
								$location_machine_qnty=0; 
								$location_worker=0; 
								$location_operator=0; 
								$location_helper=0; 
								$location_working_hour=0;
								$location_input_qnty=0; 
							}
							
						  echo '<tr bgcolor="#EFEFEF"><td colspan="14"><b>Floor name: '.$floorArr[$row[csf('floor_id')]].'; Location name: '.$locationArr[$row[csf('location_id')]].'</b></td></tr>';
							
							$location_array[]=$row[csf("location_id")];
							$floor_array=array();
							$floor_array[]=$row[csf("floor_id")];
						}
						
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						
						$sewing_line='';
						$line_number=explode(",",$row[csf('line_number')]);
						foreach($line_number as $val)
						{
							if($sewing_line=='') $sewing_line=$lineArr[$val]; else $sewing_line.=",".$lineArr[$val];
						}
						
						$po_no=''; $style_ref=''; $gmts_item=''; $buyer=''; $po_qnty=0;
						$po_id=array_unique(explode(",",substr($inputDataArray[$row[csf('location_id')]][$row[csf('floor_id')]][$row[csf('id')]]['po_id'],0,-1)));
						foreach($po_id as $val)
						{
							if($po_no=='') 
							{
								$po_no=$poDataArr[$val]['no'];
								$buyer=$buyerArr[$poDataArr[$val]['buyer_name']];
								$style_ref=$poDataArr[$val]['style_ref'];
							}
							else 
							{
								$po_no.=",".$poDataArr[$val]['no'];
								$buyer.=",".$buyerArr[$poDataArr[$val]['buyer_name']];
								$style_ref.=",".$poDataArr[$val]['style_ref'];
							}
							$po_qnty+=$poDataArr[$val]['qty'];
						}
						
						$item_id=array_unique(explode(",",substr($inputDataArray[$row[csf('location_id')]][$row[csf('floor_id')]][$row[csf('id')]]['item_id'],0,-1)));
						foreach($item_id as $gmts_id)
						{
							if($gmts_item=='') $gmts_item=$garments_item[$gmts_id]; else $gmts_item.=",".$garments_item[$gmts_id];
						}
						
						$buyer=array_filter(array_unique(explode(",",$buyer)));
						$style_ref=array_filter(array_unique(explode(",",$style_ref)));
						$input_qnty=$inputDataArray[$row[csf('location_id')]][$row[csf('floor_id')]][$row[csf('id')]]['qnty'];
					?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        	<td width="40"><? echo $i; ?></td>
                            <td width="80"><p><? echo $sewing_line; ?>&nbsp;</p></td>
                            <td width="70"><p><? echo implode(",",$buyer); ?>&nbsp;</p></td>
                            <td width="130"><p><? echo $po_no; ?>&nbsp;</p></td>
                            <td width="130"><p><? echo implode(",",$style_ref); ?>&nbsp;</p></td>
                            <td width="150"><p><? echo $gmts_item; ?>&nbsp;</p></td>
                            <td width="80" align="right"><p>&nbsp;<? echo $po_qnty; ?></p></td>
                            <td width="75" align="right">&nbsp;<? echo $row[csf('active_machine')]; ?></th>
                            <td width="75" align="right">&nbsp;<? echo $row[csf('man_power')]; ?></th>
                            <td width="75" align="right">&nbsp;<? echo $row[csf('operator')]; ?></th>
                            <td width="75" align="right">&nbsp;<? echo $row[csf('helper')]; ?></td>
                            <td width="75" align="right">&nbsp;<? echo $row[csf('working_hour')]; ?></td>
                            <td width="120"><? echo $row[csf('line_chief')]; ?>&nbsp;</td>
                            <td align="right">&nbsp;<? echo $input_qnty; ?></td>
                        </tr>
                    <?
						$i++;
						
						$floor_machine_qnty+=$row[csf('active_machine')];
						$floor_worker+=$row[csf('man_power')];
						$floor_operator+=$row[csf('operator')];
						$floor_helper+=$row[csf('helper')];
						$floor_working_hour+=$row[csf('working_hour')];
						$floor_input_qnty+=$input_qnty;
						
						$location_machine_qnty+=$row[csf('active_machine')];
						$location_worker+=$row[csf('man_power')];
						$location_operator+=$row[csf('operator')];
						$location_helper+=$row[csf('helper')];
						$location_working_hour+=$row[csf('working_hour')];
						$location_input_qnty+=$input_qnty;
						
						$tot_machine_qnty+=$row[csf('active_machine')];
						$tot_worker+=$row[csf('man_power')];
						$tot_operator+=$row[csf('operator')];
						$tot_helper+=$row[csf('helper')];
						$tot_working_hour+=$row[csf('working_hour')];
						$tot_input_qnty+=$input_qnty;
					}
					
					if(count($result)>0)
					{
						echo '<tr bgcolor="#CCCCCC">
								<td colspan="7" align="right">Floor Total</td>
								<td align="right">'.$floor_machine_qnty.'</td>
								<td align="right">'.$floor_worker.'</td>
								<td align="right">'.$floor_operator.'</td>
								<td align="right">'.$floor_helper.'</td>
								<td align="right">'.$floor_working_hour.'</td>
								<td>&nbsp;</td>
								<td align="right">'.$floor_input_qnty.'</td>
							</tr>
							<tr bgcolor="#E9F3FF">
								<td colspan="7" align="right">Location Total</td>
								<td align="right">'.$location_machine_qnty.'</td>
								<td align="right">'.$location_worker.'</td>
								<td align="right">'.$location_operator.'</td>
								<td align="right">'.$location_helper.'</td>
								<td align="right">'.$location_working_hour.'</td>
								<td>&nbsp;</td>
								<td align="right">'.$location_input_qnty.'</td>
							</tr>
							';
					}
				?>
					<tfoot>
                        <th colspan="7" align="right">Grand Total</th>
                        <th align="right"><? echo $tot_machine_qnty; ?></th>
                        <th align="right"><? echo $tot_worker; ?></th>
                        <th align="right"><? echo $tot_operator; ?></th>
                        <th align="right"><? echo $tot_helper; ?></th>
                        <th align="right"><? echo $tot_working_hour; ?></th>
                        <th>&nbsp;</th>
                        <th align="right"><? echo $tot_input_qnty; ?></th>
					</tfoot>
				</table>
			</div>
		</fieldset>
    <?
	}
	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename";
	exit();      
}



if($action=="remarks_popup")
	{
		echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	    extract($_REQUEST);
		//echo $company_id;
		//$sewing_line=explode("*",$sewing_line);
		//$sewing_line=implode(",",$sewing_line);
		$po_id=explode("*",$po_id);
		$po_id=implode(",",$po_id);
		$sql_line_remark=sql_select("select remarks,production_hour from pro_garments_production_mst where company_id=".$company_id." and  floor_id=$floor_id and sewing_line in($sewing_line) and po_break_down_id in($po_id) and production_date='".$prod_date."' and status_active=1 and is_deleted=0 order by production_hour");
		?>
		<fieldset style="width:520px;  ">
            <div id="report_container">
                    <table border="1" class="rpt_table" rules="all" width="500" cellpadding="0" cellspacing="0" align="center">
                        <thead>
                            <th width="40">SL</th>
                            <th width="460">Remarks</th>
                        </thead>
                        <tbody>
                        <?
						$i=1;
                        foreach($sql_line_remark as $inf)
						{
						 if ($i%2==0)    $bgcolor="#E9F3FF";
                         else            $bgcolor="#FFFFFF";
						 if(trim($inf[csf('remarks')])!="")
						 {
						 ?>		
						   <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="30"><? echo $i; ?></td>
                            <td align="left"><? echo $inf[csf('remarks')]; ?>&nbsp;</td>
                         
							   
                        </tr>
						<?
						$i++;
						 }
							
						}
                        
						
						?>
                        </tbody>
                        
                        
                    </table>
            </div>
        </fieldset>
           
              <?
	}


if($action=="prev_qnty")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	
	if($prod_type==4) $caption="Input"; else $caption="Production";
	
?>
<script>

	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";
	}	
	
</script>	
	<div style="width:455px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:440px; margin-left:17px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="420" cellpadding="0" cellspacing="0">
				<thead>
                	<th width="50">SL</th>
                    <th width="120"><? echo $caption; ?> Date</th>
                    <th><? echo $caption; ?> Qnty</th>
				</thead>
             </table>
             <div style="width:437px; max-height:320px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="420" cellpadding="0" cellspacing="0">
                    <? 
                    $i=1; $total_qnty=0;
                    $sql="SELECT production_date, sum(production_quantity) AS qnty from pro_garments_production_mst 
                    where po_break_down_id in(".$po_id.") and item_number_id='".$item_id."' and location='".$location."' and floor_id='".$floor_id."' and sewing_line='".$sewing_line."' and prod_reso_allo='".$prod_reso_allo."' and production_type='$prod_type' and production_date<'$prod_date' and is_deleted=0 and status_active=1 group by production_date";
                    $result=sql_select($sql);
                    $result=sql_select($sql);
        			foreach($result as $row)
                    {
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";	
                    
                        $total_qnty+=$row[csf('qnty')];
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="50"><? echo $i; ?></td>
                            <td width="120" align="center"><? echo change_date_format($row[csf('production_date')]); ?></td>
                            <td align="right"><? echo $row[csf('qnty')]; ?>&nbsp;</td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                    <tfoot>
                        <th colspan="2" align="right">Total</th>
                        <th align="right"><? echo $total_qnty; ?>&nbsp;</th>
                    </tfoot>
                </table>
            </div>	
        </div>
	</fieldset>   
<?
exit();
}

if($action=="tot_prod")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);

?>
<script>

	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";
	}	
	
</script>	
	<div style="width:455px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:340px; margin-left:70px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="320" cellpadding="0" cellspacing="0">
				<thead>
                	<th width="50">SL</th>
                    <th width="120">Production Date</th>
                    <th>Production Qnty</th>
				</thead>
             </table>
             <div style="width:337px; max-height:270px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="320" cellpadding="0" cellspacing="0">
                    <?
                    $i=1; $total_qnty=0;
                    $sql="SELECT production_date, sum(production_quantity) AS qnty from pro_garments_production_mst 
                    where po_break_down_id in (".$po_id.") and item_number_id='".$item_id."' and location='".$location."' and floor_id='".$floor_id."' and sewing_line='".$sewing_line."' and prod_reso_allo='".$prod_reso_allo."' and production_type='$prod_type' and production_date<='$prod_date' and is_deleted=0 and status_active=1 group by production_date";
                    $result=sql_select($sql);
        			foreach($result as $row)
                    {
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";	
                    
                        $total_qnty+=$row[csf('qnty')];
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="50"><? echo $i; ?></td>
                            <td width="120" align="center"><? echo change_date_format($row[csf('production_date')]); ?></td>
                            <td align="right"><? echo $row[csf('qnty')]; ?>&nbsp;</td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                    <tfoot>
                        <th colspan="2" align="right">Total</th>
                        <th align="right"><? echo $total_qnty; ?>&nbsp;</th>
                    </tfoot>
                </table>
            </div>	
        </div>
	</fieldset>   
<?
exit();
}

if($action=="today_prod")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);

?>
<script>

	function print_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
	}	
	
</script>	
	<div style="width:980px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:970px; margin-left:5px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="970" cellpadding="0" cellspacing="0">
				
                <!--   <thead> <th width="60">1</th>
                    <th width="70">2</th>
                    <th width="70">3</th>
                    <th width="70">4</th>
                    <th width="70">5</th>
                    <th width="70">6</th>
                    <th width="70">7</th>
                    <th width="70">8</th>
                    <th width="70">9</th>
                    <th width="70">10</th>
                    <th width="70">11</th>
                    <th width="70">12</th>
				</thead>-->
              
                <?
					/*
				if($db_type==0)
				{
				$sql="SELECT  
					sum(case when production_hour>'00:00' and  production_hour<='01:00' then  production_quantity else 0 end ) AS am1,
					sum(case when production_hour>'01:00' and  production_hour<='02:00' then production_quantity else 0 end ) AS am2,
					sum(case when production_hour>'02:00' and  production_hour<='03:00' then production_quantity else 0 end ) AS am3,
					sum(case when production_hour>'03:00' and  production_hour<='04:00' then production_quantity else 0 end ) AS am4,
					sum(case when production_hour>'04:00' and  production_hour<='05:00' then production_quantity else 0 end ) AS am5,
					sum(case when production_hour>'05:00' and  production_hour<='06:00' then production_quantity else 0 end ) AS am6,
					sum(case when production_hour>'06:00' and  production_hour<='07:00' then production_quantity else 0 end ) AS am7,
					sum(case when production_hour>'07:00' and  production_hour<='08:00' then production_quantity else 0 end ) AS am8,
					sum(case when production_hour>'08:00' and  production_hour<='09:00' then production_quantity else 0 end ) AS am9,
					sum(case when production_hour>'09:00' and  production_hour<='10:00' then production_quantity else 0 end ) AS am10,
					sum(case when production_hour>'10:00' and  production_hour<='11:00' then production_quantity else 0 end ) AS am11,
					sum(case when production_hour>'11:00' and  production_hour<='12:00' then production_quantity else 0 end ) AS pm12,
					sum(case when production_hour>'12:00' and  production_hour<='13:00' then production_quantity else 0 end ) AS pm13,
					sum(case when production_hour>'13:00' and  production_hour<='14:00' then production_quantity else 0 end ) AS pm14,
					sum(case when production_hour>'14:00' and  production_hour<='15:00' then production_quantity else 0 end ) AS pm15,
					sum(case when production_hour>'15:00' and  production_hour<='16:00' then production_quantity else 0 end ) AS pm16,
					sum(case when production_hour>'16:00' and  production_hour<='17:00' then production_quantity else 0 end ) AS pm17,
					sum(case when production_hour>'17:00' and  production_hour<='18:00' then production_quantity else 0 end ) AS pm18,
					sum(case when production_hour>'18:00' and  production_hour<='19:00' then production_quantity else 0 end ) AS pm19,
					sum(case when production_hour>'19:00' and  production_hour<='20:00' then production_quantity else 0 end ) AS pm20,
					sum(case when production_hour>'20:00' and  production_hour<='21:00' then production_quantity else 0 end ) AS pm21,
					sum(case when production_hour>'21:00' and  production_hour<='22:00' then production_quantity else 0 end ) AS pm22,
					sum(case when production_hour>'22:00' and  production_hour<='23:00' then production_quantity else 0 end ) AS pm23,
					sum(case when production_hour>'23:00' and  production_hour<='23:59' then production_quantity else 0 end ) AS pm24
					
					 from pro_garments_production_mst 
					where po_break_down_id in(".$po_id.") and item_number_id='".$item_id."' and location='".$location."' and floor_id='".$floor_id."' and sewing_line='".$sewing_line."' and prod_reso_allo='".$prod_reso_allo."' and production_type='$prod_type' and production_date='$prod_date' and is_deleted=0 and status_active=1";
				}
				else
				{
	$sql="SELECT  
	sum(case when TO_CHAR(production_hour,'HH24:MI')>'00:00' and  TO_CHAR(production_hour,'HH24:MI')<='01:00' then  production_quantity else 0 end ) AS am1,
	sum(case when TO_CHAR(production_hour,'HH24:MI')>'01:00' and  TO_CHAR(production_hour,'HH24:MI')<='02:00' then production_quantity else 0 end ) AS am2,
	sum(case when TO_CHAR(production_hour,'HH24:MI')>'02:00' and  TO_CHAR(production_hour,'HH24:MI')<='03:00' then production_quantity else 0 end ) AS am3,
	sum(case when TO_CHAR(production_hour,'HH24:MI')>'03:00' and  TO_CHAR(production_hour,'HH24:MI')<='04:00' then production_quantity else 0 end ) AS am4,
	sum(case when TO_CHAR(production_hour,'HH24:MI')>'04:00' and  TO_CHAR(production_hour,'HH24:MI')<='05:00' then production_quantity else 0 end ) AS am5,
	sum(case when TO_CHAR(production_hour,'HH24:MI')>'05:00' and  TO_CHAR(production_hour,'HH24:MI')<='06:00' then production_quantity else 0 end ) AS am6,
	sum(case when TO_CHAR(production_hour,'HH24:MI')>'06:00' and  TO_CHAR(production_hour,'HH24:MI')<='07:00' then production_quantity else 0 end ) AS am7,
	sum(case when TO_CHAR(production_hour,'HH24:MI')>'07:00' and  TO_CHAR(production_hour,'HH24:MI')<='08:00' then production_quantity else 0 end ) AS am8,
	sum(case when TO_CHAR(production_hour,'HH24:MI')>'08:00' and  TO_CHAR(production_hour,'HH24:MI')<='09:00' then production_quantity else 0 end ) AS am9,
	sum(case when TO_CHAR(production_hour,'HH24:MI')>'09:00'  and  TO_CHAR(production_hour,'HH24:MI')<='10:00' then production_quantity else 0 end ) AS am10,
	sum(case when TO_CHAR(production_hour,'HH24:MI')>'10:00' and  TO_CHAR(production_hour,'HH24:MI')<='11:00' then production_quantity else 0 end ) AS am11,
	sum(case when TO_CHAR(production_hour,'HH24:MI')>'11:00' and  TO_CHAR(production_hour,'HH24:MI')<='12:00' then production_quantity else 0 end ) AS pm12,
	
	sum(case when TO_CHAR(production_hour,'HH24:MI')>'12:00' and  TO_CHAR(production_hour,'HH24:MI')<='13:00' then production_quantity else 0 end ) AS pm13,
	sum(case when TO_CHAR(production_hour,'HH24:MI')>'13:00' and  TO_CHAR(production_hour,'HH24:MI')<='14:00' then production_quantity else 0 end ) AS pm14,
	sum(case when TO_CHAR(production_hour,'HH24:MI')>'14:00' and  TO_CHAR(production_hour,'HH24:MI')<='15:00' then production_quantity else 0 end ) AS pm15,
	sum(case when TO_CHAR(production_hour,'HH24:MI')>'15:00' and  TO_CHAR(production_hour,'HH24:MI')<='16:00' then production_quantity else 0 end ) AS pm16,
	sum(case when TO_CHAR(production_hour,'HH24:MI')>'16:00' and  TO_CHAR(production_hour,'HH24:MI')<='17:00' then production_quantity else 0 end ) AS pm17,
	sum(case when TO_CHAR(production_hour,'HH24:MI')>'17:00' and  TO_CHAR(production_hour,'HH24:MI')<='18:00' then production_quantity else 0 end ) AS pm18,
	sum(case when TO_CHAR(production_hour,'HH24:MI')>'18:00' and  TO_CHAR(production_hour,'HH24:MI')<='19:00' then production_quantity else 0 end ) AS pm19,
	sum(case when TO_CHAR(production_hour,'HH24:MI')>'19:00' and  TO_CHAR(production_hour,'HH24:MI')<='20:00' then production_quantity else 0 end ) AS pm20,
	sum(case when TO_CHAR(production_hour,'HH24:MI')>'20:00' and  TO_CHAR(production_hour,'HH24:MI')<='21:00' then production_quantity else 0 end ) AS pm21,
	sum(case when TO_CHAR(production_hour,'HH24:MI')>'21:00' and  TO_CHAR(production_hour,'HH24:MI')<='22:00' then production_quantity else 0 end ) AS pm22,
	sum(case when TO_CHAR(production_hour,'HH24:MI')>'22:00' and  TO_CHAR(production_hour,'HH24:MI')<='23:00' then production_quantity else 0 end ) AS pm23,
	sum(case when TO_CHAR(production_hour,'HH24:MI')>'23:00' and  TO_CHAR(production_hour,'HH24:MI')<='23:59' then production_quantity else 0 end ) AS pm24
	
	 from pro_garments_production_mst 
	where po_break_down_id in(".$po_id.") and item_number_id='".$item_id."' and location='".$location."' and floor_id='".$floor_id."' and sewing_line='".$sewing_line."' and prod_reso_allo='".$prod_reso_allo."' and production_type='$prod_type' and production_date='$prod_date' and is_deleted=0 and status_active=1";
				}
				//echo $sql;
				$result=sql_select($sql);
				foreach($result as $row);
			
			 $total_qnty=$row[csf('am1')]+$row[csf('am2')]+$row[csf('am3')]+$row[csf('am4')]+$row[csf('am5')]+$row[csf('am6')]+$row[csf('am7')]+$row[csf('am8')]+$row[csf('am9')]+$row[csf('am10')]+$row[csf('am11')]+$row[csf('pm12')]+$row[csf('pm13')]+$row[csf('pm14')]+$row[csf('pm15')]+$row[csf('pm16')]+$row[csf('pm17')]+$row[csf('pm18')]+$row[csf('pm19')]+$row[csf('pm20')]+$row[csf('pm21')]+$row[csf('pm22')]+$row[csf('pm23')]+$row[csf('pm24')];*/
					 
				
				if($db_type==0)
				{
					$dataArray=sql_select("select TIME_FORMAT( d.prod_start_time, '%H:%i' ) as prod_start_time, TIME_FORMAT( d.prod_start_time, '%H' ) as start_hour, TIME_FORMAT( d.prod_start_time, '%i' ) as start_min from prod_resource_mst a, prod_resource_dtls b, prod_resource_dtls_time d where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and d.shift_id=1 and b.pr_date='$prod_date' and a.id='$sewing_line'");
					$prod_start_hour=$dataArray[0][csf('prod_start_time')];
					if($prod_start_hour=="") $prod_start_hour="08:00";
					$start_time=explode(":",$prod_start_hour);
					$hour=$start_time[0]; $minutes=$start_time[1]; $last_hour=23;
					$start_hour_arr=array(); $s=1;
					
					$start_hour=$prod_start_hour;
					for($j=$hour;$j<$last_hour;$j++)
					{
						$start_hour=add_time($start_hour,60);
						$start_hour_arr[$j+1]=$start_hour;
					}
					$start_hour_arr[$j+1]='23:59:59';
					
					$sql="SELECT "; 
					foreach($start_hour_arr as $val)
					{
						$z++;
						if($s==1)
						{
							$sql.=" sum(case when production_hour<='$val' then production_quantity else 0 end) AS am$z ";
						}
						else
						{
							$sql.=", sum(case when production_hour>'$prev_hour' and production_hour<='$val' then production_quantity else 0 end) AS am$z ";
						}
						
						$prev_hour=$val;
						$s++;
					}
					
					$sql.="from pro_garments_production_mst where po_break_down_id in(".$po_id.") and item_number_id='".$item_id."' and location='".$location."' and floor_id='".$floor_id."' and sewing_line='".$sewing_line."' and prod_reso_allo='".$prod_reso_allo."' and production_type='$prod_type' and production_date='$prod_date' and is_deleted=0 and status_active=1";
					
					/*$sql="SELECT  
						sum(case when production_hour>'00:00' and  production_hour<='01:00' then  production_quantity else 0 end ) AS am1,
						sum(case when production_hour>'01:00' and  production_hour<='02:00' then production_quantity else 0 end ) AS am2,
						sum(case when production_hour>'02:00' and  production_hour<='03:00' then production_quantity else 0 end ) AS am3,
						sum(case when production_hour>'03:00' and  production_hour<='04:00' then production_quantity else 0 end ) AS am4,
						sum(case when production_hour>'04:00' and  production_hour<='05:00' then production_quantity else 0 end ) AS am5,
						sum(case when production_hour>'05:00' and  production_hour<='06:00' then production_quantity else 0 end ) AS am6,
						sum(case when production_hour>'06:00' and  production_hour<='07:00' then production_quantity else 0 end ) AS am7,
						sum(case when production_hour>'07:00' and  production_hour<='08:00' then production_quantity else 0 end ) AS am8,
						sum(case when production_hour>'08:00' and  production_hour<='09:00' then production_quantity else 0 end ) AS am9,
						sum(case when production_hour>'09:00' and  production_hour<='10:00' then production_quantity else 0 end ) AS am10,
						sum(case when production_hour>'10:00' and  production_hour<='11:00' then production_quantity else 0 end ) AS am11,
						sum(case when production_hour>'11:00' and  production_hour<='12:00' then production_quantity else 0 end ) AS pm12,
						sum(case when production_hour>'12:00' and  production_hour<='13:00' then production_quantity else 0 end ) AS pm13,
						sum(case when production_hour>'13:00' and  production_hour<='14:00' then production_quantity else 0 end ) AS pm14,
						sum(case when production_hour>'14:00' and  production_hour<='15:00' then production_quantity else 0 end ) AS pm15,
						sum(case when production_hour>'15:00' and  production_hour<='16:00' then production_quantity else 0 end ) AS pm16,
						sum(case when production_hour>'16:00' and  production_hour<='17:00' then production_quantity else 0 end ) AS pm17,
						sum(case when production_hour>'17:00' and  production_hour<='18:00' then production_quantity else 0 end ) AS pm18,
						sum(case when production_hour>'18:00' and  production_hour<='19:00' then production_quantity else 0 end ) AS pm19,
						sum(case when production_hour>'19:00' and  production_hour<='20:00' then production_quantity else 0 end ) AS pm20,
						sum(case when production_hour>'20:00' and  production_hour<='21:00' then production_quantity else 0 end ) AS pm21,
						sum(case when production_hour>'21:00' and  production_hour<='22:00' then production_quantity else 0 end ) AS pm22,
						sum(case when production_hour>'22:00' and  production_hour<='23:00' then production_quantity else 0 end ) AS pm23,
						sum(case when production_hour>'23:00' and  production_hour<='23:59' then production_quantity else 0 end ) AS pm24
						
						 from pro_garments_production_mst 
						where po_break_down_id in(".$po_id.") and item_number_id='".$item_id."' and location='".$location."' and floor_id='".$floor_id."' and sewing_line='".$sewing_line."' and prod_reso_allo='".$prod_reso_allo."' and production_type='$prod_type' and production_date='$prod_date' and is_deleted=0 and status_active=1";*/
				}
				else
				{
					$dataArray=sql_select("select TO_CHAR(d.prod_start_time,'HH24:MI') as prod_start_time, TO_CHAR(d.prod_start_time,'HH24') as start_hour, TO_CHAR(d.prod_start_time,'MI') as start_min, TO_CHAR(d.lunch_start_time,'HH24:MI') as lunch_start_time from prod_resource_mst a, prod_resource_dtls b, prod_resource_dtls_time d where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and d.shift_id=1 and b.pr_date='$prod_date' and a.id='$sewing_line'");
					$prod_start_hour=$dataArray[0][csf('prod_start_time')];
					if($prod_start_hour=="") $prod_start_hour="08:00";
					$start_time=explode(":",$prod_start_hour);
					$hour=$start_time[0]; $minutes=$start_time[1]; $last_hour=23;
					$start_hour_arr=array(); $s=1;
					
					$start_hour=$prod_start_hour;
					for($j=$hour;$j<$last_hour;$j++)
					{
						$start_hour=add_time($start_hour,60);
						$start_hour_arr[$j+1]=$start_hour;
					}
					$start_hour_arr[$j+1]='23:59:59';
					
					$sql="SELECT "; 
					foreach($start_hour_arr as $val)
					{
						$z++;
						if($s==1)
						{
							$sql.="sum(case when TO_CHAR(production_hour,'HH24:MI:SS')<='$val' then production_quantity else 0 end) AS am$z ";
						}
						else
						{
							$sql.=", sum(case when TO_CHAR(production_hour,'HH24:MI:SS')>'$prev_hour' and TO_CHAR(production_hour,'HH24:MI:SS')<='$val' then production_quantity else 0 end) AS am$z ";
						}
						
						$prev_hour=$val;
						$s++;
					}
					
					$sql.="from pro_garments_production_mst where po_break_down_id in(".$po_id.") and item_number_id='".$item_id."' and location='".$location."' and floor_id='".$floor_id."' and sewing_line='".$sewing_line."' and prod_reso_allo='".$prod_reso_allo."' and production_type='$prod_type' and production_date='$prod_date' and is_deleted=0 and status_active=1";
					//echo $sql;
					
					/*$sql="SELECT  
						sum(case when TO_CHAR(production_hour,'HH24:MI')>'00:00' and  TO_CHAR(production_hour,'HH24:MI')<='01:00' then  production_quantity else 0 end ) AS am1,
						sum(case when TO_CHAR(production_hour,'HH24:MI')>'01:00' and  TO_CHAR(production_hour,'HH24:MI')<='02:00' then production_quantity else 0 end ) AS am2,
						sum(case when TO_CHAR(production_hour,'HH24:MI')>'02:00' and  TO_CHAR(production_hour,'HH24:MI')<='03:00' then production_quantity else 0 end ) AS am3,
						sum(case when TO_CHAR(production_hour,'HH24:MI')>'03:00' and  TO_CHAR(production_hour,'HH24:MI')<='04:00' then production_quantity else 0 end ) AS am4,
						sum(case when TO_CHAR(production_hour,'HH24:MI')>'04:00' and  TO_CHAR(production_hour,'HH24:MI')<='05:00' then production_quantity else 0 end ) AS am5,
						sum(case when TO_CHAR(production_hour,'HH24:MI')>'05:00' and  TO_CHAR(production_hour,'HH24:MI')<='06:00' then production_quantity else 0 end ) AS am6,
						sum(case when TO_CHAR(production_hour,'HH24:MI')>'06:00' and  TO_CHAR(production_hour,'HH24:MI')<='07:00' then production_quantity else 0 end ) AS am7,
						sum(case when TO_CHAR(production_hour,'HH24:MI')>'07:00' and  TO_CHAR(production_hour,'HH24:MI')<='08:00' then production_quantity else 0 end ) AS am8,
						sum(case when TO_CHAR(production_hour,'HH24:MI')>'08:00' and  TO_CHAR(production_hour,'HH24:MI')<='09:00' then production_quantity else 0 end ) AS am9,
						sum(case when TO_CHAR(production_hour,'HH24:MI')>'09:00'  and  TO_CHAR(production_hour,'HH24:MI')<='10:00' then production_quantity else 0 end ) AS am10,
						sum(case when TO_CHAR(production_hour,'HH24:MI')>'10:00' and  TO_CHAR(production_hour,'HH24:MI')<='11:00' then production_quantity else 0 end ) AS am11,
						sum(case when TO_CHAR(production_hour,'HH24:MI')>'11:00' and  TO_CHAR(production_hour,'HH24:MI')<='12:00' then production_quantity else 0 end ) AS pm12,
						
					
						sum(case when TO_CHAR(production_hour,'HH24:MI')>'12:00' and  TO_CHAR(production_hour,'HH24:MI')<='13:00' then production_quantity else 0 end ) AS pm13,
						sum(case when TO_CHAR(production_hour,'HH24:MI')>'13:00' and  TO_CHAR(production_hour,'HH24:MI')<='14:00' then production_quantity else 0 end ) AS pm14,
						sum(case when TO_CHAR(production_hour,'HH24:MI')>'14:00' and  TO_CHAR(production_hour,'HH24:MI')<='15:00' then production_quantity else 0 end ) AS pm15,
						sum(case when TO_CHAR(production_hour,'HH24:MI')>'15:00' and  TO_CHAR(production_hour,'HH24:MI')<='16:00' then production_quantity else 0 end ) AS pm16,
						sum(case when TO_CHAR(production_hour,'HH24:MI')>'16:00' and  TO_CHAR(production_hour,'HH24:MI')<='17:00' then production_quantity else 0 end ) AS pm17,
						sum(case when TO_CHAR(production_hour,'HH24:MI')>'17:00' and  TO_CHAR(production_hour,'HH24:MI')<='18:00' then production_quantity else 0 end ) AS pm18,
						sum(case when TO_CHAR(production_hour,'HH24:MI')>'18:00' and  TO_CHAR(production_hour,'HH24:MI')<='19:00' then production_quantity else 0 end ) AS pm19,
						sum(case when TO_CHAR(production_hour,'HH24:MI')>'19:00' and  TO_CHAR(production_hour,'HH24:MI')<='20:00' then production_quantity else 0 end ) AS pm20,
						sum(case when TO_CHAR(production_hour,'HH24:MI')>'20:00' and  TO_CHAR(production_hour,'HH24:MI')<='21:00' then production_quantity else 0 end ) AS pm21,
						sum(case when TO_CHAR(production_hour,'HH24:MI')>'21:00' and  TO_CHAR(production_hour,'HH24:MI')<='22:00' then production_quantity else 0 end ) AS pm22,
						sum(case when TO_CHAR(production_hour,'HH24:MI')>'22:00' and  TO_CHAR(production_hour,'HH24:MI')<='23:00' then production_quantity else 0 end ) AS pm23,
						sum(case when TO_CHAR(production_hour,'HH24:MI')>'23:00' and  TO_CHAR(production_hour,'HH24:MI')<='23:59' then production_quantity else 0 end ) AS pm24
					
						 from pro_garments_production_mst 
						where po_break_down_id in(".$po_id.") and item_number_id='".$item_id."' and location='".$location."' and floor_id='".$floor_id."' and sewing_line='".$sewing_line."' and prod_reso_allo='".$prod_reso_allo."' and production_type='$prod_type' and production_date='$prod_date' and is_deleted=0 and status_active=1";*/
									
				}

				$result=sql_select($sql);
				foreach($result as $row);
				//$total_qnty=$row[csf('am1')]+$row[csf('am2')]+$row[csf('am3')]+$row[csf('am4')]+$row[csf('am5')]+$row[csf('am6')]+$row[csf('am7')]+$row[csf('am8')]+$row[csf('am9')]+$row[csf('am10')]+$row[csf('am11')]+$row[csf('pm12')]+$row[csf('pm13')]+$row[csf('pm14')]+$row[csf('pm15')]+$row[csf('pm16')]+$row[csf('pm17')]+$row[csf('pm18')]+$row[csf('pm19')]+$row[csf('pm20')]+$row[csf('pm21')]+$row[csf('pm22')]+$row[csf('pm23')]+$row[csf('pm24')];
				// bgcolor="#E9F3FF"
				echo '<thead><tr>';
				$x=1;
				foreach($start_hour_arr as $val)
				{
					if($x<13)
					{
						echo '<th width="70">'.$val.'</th>';
						$x++;
					}
				}
				echo '</tr></thead><tr bgcolor="#E9F3FF">';
				
				$x=1; $total_qnty=0;
				foreach($start_hour_arr as $val)
				{
					if($x<13)
					{
						echo '<td width="70" align="right">'.$row[csf('am'.$x)].'&nbsp;&nbsp;</td>';
						$total_qnty+=$row[csf('am'.$x)];
						$x++;
					}
				}
				echo '</tr>';

				array_splice($start_hour_arr,0, 12);
				$x=13;
				if(count($start_hour_arr)>0)
				{
					echo '<thead><tr>';
					foreach($start_hour_arr as $val)
					{
						echo '<th width="70">'.$val.'</th>';
						$x++;
					}
					$x=13;
					echo '</tr></thead><tr bgcolor="#E9F3FF">';
					foreach($start_hour_arr as $val)
					{
						echo '<td width="70" align="right">'.$row[csf('am'.$x)].'&nbsp;&nbsp;</td>';
						$total_qnty+=$row[csf('am'.$x)];
						$x++;
					}
					echo '</tr>';
				}
				?>
                <!--<tr bgcolor="<?echo $bgcolor; ?>" onclick="change_color('tr_<?echo $i; ?>','<?echo $bgcolor;?>')" id="tr_<?echo $i;?>">
                    <td width="60" align="right"><?echo $row[csf('am1')]; ?>&nbsp;</td>
                    <td width="70" align="right"><?echo $row[csf('am2')]; ?>&nbsp;</td>
                    <td width="70" align="right"><?echo $row[csf('am3')]; ?>&nbsp;</td>
                    <td width="70" align="right"><?echo $row[csf('am4')]; ?>&nbsp;</td>
                    <td width="70" align="right"><?echo $row[csf('am5')]; ?>&nbsp;</td>
                    <td width="70" align="right"><?echo $row[csf('am6')]; ?>&nbsp;</td>
                    <td width="70" align="right"><?echo $row[csf('am7')]; ?>&nbsp;</td>
                    <td width="70" align="right"><?echo $row[csf('am8')]; ?>&nbsp;</td>
                    <td width="70" align="right"><?echo $row[csf('am9')]; ?>&nbsp;</td>
                    <td width="70" align="right"><?echo $row[csf('am10')]; ?>&nbsp;</td>
                    <td width="70" align="right"><?echo $row[csf('am11')]; ?>&nbsp;</td>
                    <td width="70" align="right"><?echo $row[csf('pm12')]; ?>&nbsp;</td>
                </tr><tr><td></td></tr>
                <thead>
                    <th width="60">13</th>
                    <th width="70">14</th>
                    <th width="70">15</th>
                    <th width="70">16</th>
                    <th width="70">17</th>
                    <th width="70">18</th>
                    <th width="70">19</th>
                    <th width="70">20</th>
                    <th width="70">21</th>
                    <th width="70">22</th>
                    <th width="70">23</th>
                    <th width="70">24</th>
				</thead>
              
             	<tr bgcolor="<?echo $bgcolor; ?>" onclick="change_color('tr_<?echo $i; ?>','<?echo $bgcolor;?>')" id="tr_<?echo $i;?>">
                    <td width="60" align="right"><?echo $row[csf('pm13')]; ?>&nbsp;</td>
                    <td width="70" align="right"><?echo $row[csf('pm14')]; ?>&nbsp;</td>
                    <td width="70" align="right"><?echo $row[csf('pm15')]; ?>&nbsp;</td>
                    <td width="70" align="right"><?echo $row[csf('pm16')]; ?>&nbsp;</td>
                    <td width="70" align="right"><?echo $row[csf('pm17')]; ?>&nbsp;</td>
                    <td width="70" align="right"><?echo $row[csf('pm18')]; ?>&nbsp;</td>
                    <td width="70" align="right"><?echo $row[csf('pm19')]; ?>&nbsp;</td>
                    <td width="70" align="right"><?echo $row[csf('pm20')]; ?>&nbsp;</td>
                    <td width="70" align="right"><?echo $row[csf('pm21')]; ?>&nbsp;</td>
                    <td width="70" align="right"><?echo $row[csf('pm22')]; ?>&nbsp;</td>
                    <td width="70" align="right"><?echo $row[csf('pm23')]; ?>&nbsp;</td>
                    <td width="70" align="right"><?echo $row[csf('pm24')]; ?>&nbsp;</td>
                </tr>-->
                <tr><td colspan="12"><strong>Total: &nbsp;&nbsp;<? echo $total_qnty;?> </strong></td></tr>
			</table>
        </div>
	</fieldset>   
<?
exit();
}

if($action=="tot_smv_used")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$fstinput_date=$prod_type;
	
	$prod_resource_array=array();
	$dataArray=sql_select("select b.pr_date, b.man_power, b.working_hour, b.smv_adjust, b.smv_adjust_type from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.company_id=$company_id and a.id='$sewing_line'");

	foreach($dataArray as $row)
	{
		$prod_resource_array[$row[csf('pr_date')]]['smv']=$row[csf('man_power')]*$row[csf('working_hour')]*60;
		$prod_resource_array[$row[csf('pr_date')]]['mp']=$row[csf('man_power')];
		$prod_resource_array[$row[csf('pr_date')]]['wh']=$row[csf('working_hour')];
		$prod_resource_array[$row[csf('pr_date')]]['smv_adjust']=$row[csf('smv_adjust')];
		$prod_resource_array[$row[csf('pr_date')]]['smv_adjust_type']=$row[csf('smv_adjust_type')];
	}
?>
<script>

	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";
	}	
	
</script>	
	<div style="width:680px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:680px; margin-left:3px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="660" cellpadding="0" cellspacing="0">
				<thead>
                	<th width="40">SL</th>
                    <th width="90">Production Date</th>
                    <th width="70">Manpower</th>
                    <th width="80">Working Hour</th>
                    <th width="80">SMV</th>
                    <th width="80">Adj. Type</th>
                    <th width="80">Adj. SMV</th>
                    <th>Actual SMV</th>
				</thead>
             </table>
             <div style="width:678px; max-height:280px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="660" cellpadding="0" cellspacing="0">
                    <?
                    $i=1; $total_smv_used=0;
                    $sql="select b.date_calc from lib_capacity_calc_mst a, lib_capacity_calc_dtls b where a.id=b.mst_id and a.comapny_id='$company_id' and b.date_calc between '$fstinput_date' and '$prod_date' and day_status=1";
                    $result=sql_select($sql);
        			foreach($result as $row)
                    {
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";	
                    	
						$total_adjustment=0;
						$smv_adjustmet_type=$prod_resource_array[$row[csf('date_calc')]]['smv_adjust_type'];
						if(str_replace("'","",$smv_adjustmet_type)==1) $total_adjustment=$prod_resource_array[$row[csf('date_calc')]]['smv_adjust'];
						if(str_replace("'","",$smv_adjustmet_type)==2) $total_adjustment=($prod_resource_array[$row[csf('date_calc')]]['smv_adjust'])*(-1);
						
						$day_smv=$prod_resource_array[$row[csf('date_calc')]]['smv']+$total_adjustment;
                        $total_smv_used+=$day_smv;
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="40"><? echo $i; ?></td>
                            <td width="90" align="center"><? echo change_date_format($row[csf('date_calc')]); ?></td>
                            <td width="70" align="center"><? echo $prod_resource_array[$row[csf('date_calc')]]['mp']; ?>&nbsp;</td>
                            <td width="80" align="center"><? echo $prod_resource_array[$row[csf('date_calc')]]['wh']; ?>&nbsp;</td>
                            <td width="80" align="right"><? echo $prod_resource_array[$row[csf('date_calc')]]['smv']; ?>&nbsp;</td>
                            <td width="80" align="center"><? echo $increase_decrease[$prod_resource_array[$row[csf('date_calc')]]['smv_adjust_type']]; ?>&nbsp;</td>
                            <td width="80" align="right"><? echo $prod_resource_array[$row[csf('date_calc')]]['smv_adjust']; ?>&nbsp;</td>
                            <td align="right"><? echo number_format($day_smv,2); ?>&nbsp;</td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                    <tfoot>
                        <th colspan="7" align="right">Total</th>
                        <th align="right"><? echo number_format($total_smv_used,2); ?>&nbsp;</th>
                    </tfoot>
                </table>
            </div>	
        </div>
	</fieldset>     
<?
exit();
}
