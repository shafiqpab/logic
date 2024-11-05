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
	echo create_drop_down( "cbo_location_id", 140, "select id,location_name from lib_location where status_active=1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/line_wise_productivity_analysis_cm_with_value_report_controller', this.value, 'load_drop_down_floor', 'floor_td' );load_drop_down( 'requires/line_wise_productivity_analysis_cm_with_value_report_controller',document.getElementById('cbo_floor_id').value+'_'+this.value+'_'+document.getElementById('cbo_company_id').value+'_'+document.getElementById('txt_date').value, 'load_drop_down_line', 'line_td' );",0 ); 
	exit();    	 
}

if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor_id", 130, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "load_drop_down( 'requires/line_wise_productivity_analysis_cm_with_value_report_controller', this.value+'_'+document.getElementById('cbo_location_id').value+'_'+document.getElementById('cbo_company_id').value+'_'+document.getElementById('txt_date').value, 'load_drop_down_line', 'line_td' );",0 );     	 	
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
	
	$lineArr=array(); $lineSerialArr=array(); $lastSlNo='';
	$lineDataArr = sql_select("select id, line_name, sewing_line_serial from lib_sewing_line where is_deleted=0 and status_active=1 order by sewing_line_serial"); 
	foreach($lineDataArr as $lRow)
	{
		$lineArr[$lRow[csf('id')]]=$lRow[csf('line_name')];
		$lineSerialArr[$lRow[csf('id')]]=$lRow[csf('sewing_line_serial')];
		$lastSlNo=$lRow[csf('sewing_line_serial')];
	}
	
	$type=str_replace("'","",$type);
	$comapny_id=str_replace("'","",$cbo_company_id);
	
	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$comapny_id and variable_list=23 and is_deleted=0 and status_active=1");

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
		$costing_per_arr = return_library_array("select job_no,costing_per from wo_pre_cost_mst","job_no","costing_per"); 
		$tot_cost_arr = return_library_array("select job_no, cm_for_sipment_sche from wo_pre_cost_dtls","job_no","cm_for_sipment_sche"); 
		$prod_reso_arr=return_library_array( "select id,line_number from prod_resource_mst",'id','line_number');
		
		$cm_cost_arr = return_library_array("select a.job_no,
        CASE WHEN a.costing_per = 2 THEN (b.cm_cost*12)
             WHEN a.costing_per = 3 THEN (b.cm_cost/2)
             WHEN a.costing_per = 4 THEN (b.cm_cost/3)
             WHEN a.costing_per = 5 THEN (b.cm_cost/4)
         ELSE b.cm_cost END as cm_cost from wo_pre_cost_mst a, wo_pre_cost_dtls b where a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","job_no","cm_cost"); 



		if($db_type==0)
		{
			$job_po_id_arr=return_library_array( "select job_no_mst, group_concat(id) as po_id from wo_po_break_down group by job_no_mst",'job_no_mst','po_id');
		}
		else
		{
			$job_po_id_arr=return_library_array("select job_no_mst,LISTAGG(id,',') WITHIN GROUP (ORDER BY id) as po_id from wo_po_break_down group by job_no_mst",'job_no_mst','po_id');
		}

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
			$sql="select a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.order_uom, a.gmts_item_id, a.set_break_down, a.total_set_qnty as ratio, group_concat(distinct(b.id)) as po_id, group_concat(concat_ws('**',b.id,b.po_number,b.po_quantity,b.unit_price)) as po_data, c.item_number_id, c.location, c.floor_id, c.prod_reso_allo, c.sewing_line, sum(c.production_quantity) as qnty from wo_po_details_master a, wo_po_break_down b, pro_garments_production_mst c where a.job_no=b.job_no_mst and a.company_name='$comapny_id' and b.id=c.po_break_down_id and c.location like '$location' and c.floor_id like '$floor' and c.sewing_line like '$line' and production_date=$txt_date and production_type=5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $buyer_id_cond group by a.job_no, c.location, c.floor_id, c.prod_reso_allo, c.sewing_line order by c.location, c.floor_id, c.sewing_line";//, c.item_number_id
		}
		else
		{
			$sql="select a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.order_uom, a.gmts_item_id, a.set_break_down, a.total_set_qnty as ratio, LISTAGG(b.id, ',') WITHIN GROUP (ORDER BY b.id) as po_id, LISTAGG(cast(b.id || '**' || b.po_number || '**' || b.po_quantity || '**' || b.unit_price as varchar2(4000)), ',') WITHIN GROUP (ORDER BY b.id) as po_data, c.item_number_id, c.location, c.floor_id, c.prod_reso_allo, c.sewing_line, sum(c.production_quantity) as qnty from wo_po_details_master a, wo_po_break_down b, pro_garments_production_mst c where a.job_no=b.job_no_mst and a.company_name='$comapny_id' and b.id=c.po_break_down_id and c.location like '$location' and c.floor_id like '$floor' and c.sewing_line like '$line' and production_date=$txt_date and production_type=5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $buyer_id_cond group by a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.order_uom, a.gmts_item_id, a.set_break_down, a.total_set_qnty, c.location, c.floor_id, c.prod_reso_allo, c.sewing_line, c.item_number_id order by c.location, c.floor_id";//, c.sewing_line
		}
		//echo $sql;
		$line_data_array=array(); $job_arr=array();
		$result = sql_select($sql);
		foreach($result as $row )
		{ 
			$sewing_line_id='';
			if($row[csf('prod_reso_allo')]==1)
			{
				$sewing_line_id=$prod_reso_arr[$row[csf('sewing_line')]];
			}
			else
			{
				$sewing_line_id=$row[csf('sewing_line')];
			}
			
			if($lineSerialArr[$sewing_line_id]=="")
			{
				$lastSlNo++;
				$slNo=$lastSlNo;
				$lineSerialArr[$sewing_line_id];
			}
			else $slNo=$lineSerialArr[$sewing_line_id];
			
			//echo $slNo."**".$sewing_line_id."<br>";
			$po_data=implode(",",array_unique(explode(",",$row[csf('po_data')])));
			
			$line_data_array[$row[csf('location')]][$row[csf('floor_id')]][$slNo].=$row[csf('job_no')]."##".$row[csf('company_name')]."##".$row[csf('buyer_name')]."##".$row[csf('style_ref_no')]."##".$row[csf('order_uom')]."##".$row[csf('gmts_item_id')]."##".$row[csf('set_break_down')]."##".$row[csf('ratio')]."##".$row[csf('po_id')]."##".$row[csf('item_number_id')]."##".$row[csf('prod_reso_allo')]."##".$row[csf('sewing_line')]."##".$row[csf('qnty')]."##".$po_data.":";
		}
		unset($result);
		$po_rate_arr=array();
		$itemRatedata=sql_select("select po_break_down_id, item_number_id, sum(order_quantity) as qty, sum(order_total) as amnt from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 group by po_break_down_id, item_number_id"); 
		foreach($itemRatedata as $iRow)
		{
			$po_rate_arr[$iRow[csf('po_break_down_id')]][$iRow[csf('item_number_id')]][1]=$iRow[csf('amnt')];
			$po_rate_arr[$iRow[csf('po_break_down_id')]][$iRow[csf('item_number_id')]][2]=$iRow[csf('qty')];
		}
		unset($itemRatedata);
		
		$sql_subconProd="select a.party_id, a.subcon_job, c.gmts_item_id, b.cust_style_ref, b.id as po_id, b.order_no, b.order_quantity, b.rate, b.smv, c.location_id, c.floor_id, c.prod_reso_allo, c.line_id, sum(c.production_qnty) as qnty FROM subcon_ord_mst a, subcon_ord_dtls b, subcon_gmts_prod_dtls c WHERE a.subcon_job=b.job_no_mst and c.order_id=b.id and a.company_id='$comapny_id' and c.production_type=2 and c.line_id like '$line' and c.location_id like '$location' and c.floor_id like '$floor' and c.production_date=$txt_date and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.party_id, a.subcon_job, c.gmts_item_id, b.cust_style_ref, b.id, b.order_no,b.order_quantity, b.rate, b.smv, c.location_id, c.floor_id, c.prod_reso_allo, c.line_id order by c.location_id, c.floor_id";
		$resultSub = sql_select($sql_subconProd);
		
		$i=1; $k=1; $html=''; $buyer_data_array=array();
		$total_order_qnty=0; $total_machine_qnty=0; $tot_worker=0; $tot_operator=0; $tot_helper=0; $tot_prev_input_qnty=0; $tot_prev_out_qnty=0; 
		$tot_prev_wip=0; $tot_today_target=0; $tot_today_input_qnty=0; $tot_today_out_qnty=0; $tot_today_smv=0; $tot_item_smv=0; $tot_achv_smv=0; 
		$tot_cm_value=0; $grand_tot_prod=0; $tot_wip=0; $grand_tot_smv_used=0; $grand_tot_achv_smv=0;
		
		foreach($line_data_array as $location=>$locData )
		{
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
			$location_today_tar_fob_val=0;
			$location_today_fob_arc_qnty=0; 
			$location_today_out_qnty=0; 
			$location_today_smv=0; 
			$location_item_smv=0; 
			$location_achv_smv=0; 
			$location_cm_value=0; 
			$location_tot_prod=0; 
			$location_wip=0; 
			$location_tot_smv_used=0; 
			$location_tot_achv_smv=0;
			foreach($locData as $floor=>$floorData )
			{
				$html.='<tr bgcolor="#EFEFEF"><td colspan="36"><b>Floor name: '.$floorArr[$floor].'; Location name: '.$locationArr[$location].'</b></td></tr>';
			
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
				
				$floor_today_tar_fob_val=0;
				$floor_today_fob_arc_qnty=0; 

				$floor_today_out_qnty=0; 
				$floor_today_smv=0; 
				$floor_item_smv=0; 
				$floor_achv_smv=0; 
				$floor_cm_value=0; 
				$floor_tot_prod=0; 
				$floor_wip=0; 
				$floor_tot_smv_used=0; 
				$floor_tot_achv_smv=0;
				
				ksort($floorData);
				foreach($floorData as $slNo=>$lineData )
				{
					$lineSlData=explode(":",chop($lineData,':'));
					foreach($lineSlData as $value )
					{
						$lineDataArr=explode("##",$value);
						$row[csf('job_no')]=$lineDataArr[0];
						$row[csf('company_name')]=$lineDataArr[1];
						$row[csf('buyer_name')]=$lineDataArr[2];
						$row[csf('style_ref_no')]=$lineDataArr[3];
						$row[csf('order_uom')]=$lineDataArr[4];
						$row[csf('gmts_item_id')]=$lineDataArr[5];
						$row[csf('set_break_down')]=$lineDataArr[6];
						$row[csf('ratio')]=$lineDataArr[7];
						$row[csf('po_id')]=$lineDataArr[8];
						$row[csf('item_number_id')]=$lineDataArr[9];
						$row[csf('prod_reso_allo')]=$lineDataArr[10];
						$row[csf('sewing_line')]=$lineDataArr[11];
						$row[csf('qnty')]=$lineDataArr[12];
						$row[csf('po_data')]=$lineDataArr[13];
						
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				
						$po_number=''; $po_quantity=0; $item_smv=0; $po_array=array(); $item_po_amnt=0; $item_po_quantity=0;
						 
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
							$po_array[$po_val[0]]['rate']=$po_val[3];
						}
						
						$po_ids = array_unique(explode(",",$row[csf("po_id")]));
						foreach($po_ids as $id)
						{
							if($po_number=="") $po_number=$po_array[$id]['no']; else $po_number.=",".$po_array[$id]['no'];
							$po_quantity+=$po_array[$id]['qnty']*$set_qty;
							$item_po_amnt+=$po_rate_arr[$id][$row[csf('item_number_id')]][1];
							$item_po_quantity+=$po_rate_arr[$id][$row[csf('item_number_id')]][2];
							//$po_rate=$po_array[$id]['rate'];
						}
						
						$po_rate=number_format($item_po_amnt/$item_po_quantity,2,'.','');
						//$job_po_id=$job_po_id_arr[$row[csf("job_no")]]; 
						$job_po_id=$row[csf("po_id")];
						if($job_po_id!="") $job_po_id=$job_po_id;else $job_po_id=0;
						$mst_sql= "SELECT  
										min(CASE WHEN production_type ='4' THEN production_date END) AS frstinput_date,
										sum(CASE WHEN production_type ='4' and production_date=$txt_date and po_break_down_id in(".$job_po_id.") THEN production_quantity ELSE 0 END) AS today_input_qnty,
										sum(CASE WHEN production_type ='4' and production_date<$txt_date THEN production_quantity ELSE 0 END) AS prev_input_qnty,
										sum(CASE WHEN production_type ='5' and production_date<$txt_date THEN production_quantity ELSE 0 END) AS prev_out_qnty
									from 
										pro_garments_production_mst 
									where  
										po_break_down_id in(".$job_po_id.") and item_number_id='".$row[csf("item_number_id")]."' and location='".$location."' and floor_id='".$floor."' and prod_reso_allo='".$row[csf("prod_reso_allo")]."' and sewing_line='".$row[csf("sewing_line")]."' and is_deleted=0 and status_active=1"; 
						//echo $mst_sql;die;
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
						$no_of_days=0; $total_smv_used=0; $total_adjust=0;
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
						
						$dzn_qnty=0; $cm_value=0;
						if($costing_per_arr[$row[csf('job_no')]]==1) $dzn_qnty=12;
						else if($costing_per_arr[$row[csf('job_no')]]==3) $dzn_qnty=12*2;
						else if($costing_per_arr[$row[csf('job_no')]]==4) $dzn_qnty=12*3;
						else if($costing_per_arr[$row[csf('job_no')]]==5) $dzn_qnty=12*4;
						else $dzn_qnty=1;
						
						$dzn_qnty=$dzn_qnty*$row[csf('ratio')];
						$cm_value=($tot_cost_arr[$row[csf('job_no')]]/$dzn_qnty)*$row[csf('qnty')];
						
						$actual_line_arr.=$row[csf('sewing_line')].",";
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
								<td width="50"><p>'.$sewing_line.'&nbsp;</p></td>
								<td width="70"><p>'.$buyerArr[$row[csf('buyer_name')]].'</p></td>
								<td width="110"><p>'.$po_number.'</p></td>
								<td width="110"><p>'.$row[csf('style_ref_no')].'</p></td>
								<td width="140"><p>'.$garments_item[$row[csf('item_number_id')]].'</p></td>
								<td width="75" align="right">'.number_format($cm_cost_arr[$row[csf('job_no')]],4).'&nbsp;</td>
								<td width="75" align="right">'.$po_quantity.'&nbsp;</td>
								<td width="70" align="right">'.$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['active_machine'].'&nbsp;</td>
								<td width="60" align="right">'.$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['man_power'].'&nbsp;</td>
								<td width="60" align="right">'.$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['operator'].'&nbsp;</td>
								<td width="60" align="right">'.$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['helper'].'&nbsp;</td>
								<td width="120"><p>'.$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['line_chief'].'&nbsp;</p></td>
								<td width="80" align="center">'.change_date_format($fstinput_date).'&nbsp;</td>
								<td width="75" align="right"><a href="##" onclick="openmypage('.$comapny_id.",'".$row[csf('po_id')]."',".$row[csf('item_number_id')].",".$location.",".$row[csf('floor_id')].",".$row[csf('sewing_line')].",".$txt_date.",'prev_qnty',4".",".$row[csf('prod_reso_allo')].')">'.$prev_input_qnty.'</a>&nbsp;</td>
								<td width="75" align="right"><a href="##" onclick="openmypage('.$comapny_id.",'".$row[csf('po_id')]."',".$row[csf('item_number_id')].",".$location.",".$row[csf('floor_id')].",".$row[csf('sewing_line')].",".$txt_date.",'prev_qnty',5".",".$row[csf('prod_reso_allo')].')">'.$prev_out_qnty.'</a>&nbsp;</td>
								<td width="75" align="right">'.$prev_wip.'&nbsp;</td>
								
								<td width="75" align="right">'.$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['tpd'].'&nbsp;</td>
								<td width="75" align="right">'.$today_input_qnty.'&nbsp;</td>
								
								<td width="75" align="right">'.$po_rate.'</td>
								<td width="75" align="right">'.number_format($po_rate*$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['tpd'],2,'.','').'</td>
								<td width="75" align="right">'.number_format($po_rate*$row[csf('qnty')],2,'.','').'</td>
								
								<td width="75" align="right"><a href="##" onclick="openmypage('.$comapny_id.",'".$row[csf('po_id')]."',".$row[csf('item_number_id')].",".$location.",".$floor.",".$row[csf('sewing_line')].",".$txt_date.",'today_prod',5".",".$row[csf('prod_reso_allo')].')">'.$row[csf('qnty')].'</a>&nbsp;</td>
								<td width="75" align="right">'.number_format($today_ach_perc,2).'&nbsp;</td>
								<td width="75" align="right">'.$today_smv.'&nbsp;</p></td>
								<td width="70" align="right">'.$item_smv.'&nbsp;</td>
								<td width="100" align="right">'.$achv_smv.'&nbsp;</td>
								<td width="75" align="right">'.number_format($today_aff_perc,2).'&nbsp;</td>
								<td width="110" align="right">'.number_format($cm_value,2,'.','').'&nbsp;</td>
								<td width="90" align="right"><a href="##" onclick="openmypage('.$comapny_id.",'".$job_po_id."',".$row[csf('item_number_id')].",".$location.",".$floor.",".$row[csf('sewing_line')].",".$txt_date.",'tot_prod',5".",".$row[csf('prod_reso_allo')].')">'.$total_prod.'</a>&nbsp;</td>
								<td width="80" align="right">'.number_format($avg_per_day,2,'.','').'&nbsp;</td>
								<td width="80" align="right">'.$wip.'&nbsp;</td>
								<td width="75" align="right"><a href="##" onclick="openmypage('.$comapny_id.",'".$row[csf('po_id')]."',".$row[csf('item_number_id')].",".$location.",".$floor.",".$row[csf('sewing_line')].",".$txt_date.",'tot_smv_used',"."'".$fstinput_date."'".",".$row[csf('prod_reso_allo')].')">'.number_format($total_smv_used,2,'.','').'</a>&nbsp;</td>
								<td width="75" align="right">'.number_format($total_smv_achv,2,'.','').'&nbsp;</td>
								<td align="right" width="75">'.number_format($avg_aff_perc,2,'.','').'&nbsp;</td>';
									
						 $total_po_id=explode(",",$row[csf("po_id")]);
						 $total_po_id=implode("*",$total_po_id);
						 $line_number_id=$row[csf('sewing_line')];
						
						 $html.='<td><input type="button"  value="View"  class="formbutton" onclick="show_line_remarks('.$comapny_id.",'".$total_po_id."','".$floor."','".$line_number_id."','remarks_popup',".$txt_date.')"/></td>
							</tr>';
			   
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
						
						$tot_today_tar_fob_val+=($prod_resource_array[$row[csf('sewing_line')]][$pr_date]['tpd']*$po_rate); 
						$tot_today_fob_arc_qnty+=($row[csf('qnty')]*$po_rate); 
						
						$tot_today_out_qnty+=$row[csf('qnty')]; 
						$tot_today_smv+=$today_smv; 
						$tot_item_smv+=$item_smv; 
						$tot_achv_smv+=$achv_smv; 
						$tot_cm_value+=$cm_value; 
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
						
						$location_today_tar_fob_val+=($prod_resource_array[$row[csf('sewing_line')]][$pr_date]['tpd']*$po_rate); 
						$location_today_fob_arc_qnty+=($row[csf('qnty')]*$po_rate); 
						
						$location_today_out_qnty+=$row[csf('qnty')]; 
						$location_today_smv+=$today_smv; 
						$location_item_smv+=$item_smv; 
						$location_achv_smv+=$achv_smv; 
						$location_cm_value+=$cm_value; 
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
						
						$floor_today_tar_fob_val+=($prod_resource_array[$row[csf('sewing_line')]][$pr_date]['tpd']*$po_rate); 
						$floor_today_fob_arc_qnty+=($row[csf('qnty')]*$po_rate); 
						
						$floor_today_out_qnty+=$row[csf('qnty')]; 
						$floor_today_smv+=$today_smv; 
						$floor_item_smv+=$item_smv; 
						$floor_achv_smv+=$achv_smv; 
						$floor_cm_value+=$cm_value; 
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
						$buyer_data_array[$row[csf('buyer_name')]]['cm']+=$cm_value;
						
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
				}
				
				$floor_today_ach_perc=$floor_today_out_qnty/$floor_today_target*100;
				$floor_today_aff_perc=$floor_achv_smv/$floor_today_smv*100;
				$floor_avg_aff_perc=$floor_tot_achv_smv/$floor_tot_smv_used*100;
				
				$html.='<tr bgcolor="#CCCCCC">
						<td colspan="6" align="right">Floor Total</td>
						<td align="right">&nbsp;</td>
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

						<td width="75" align="right">&nbsp;</td>
						<td width="75" align="right">'.number_format($floor_today_tar_fob_val,2,'.','').'</td>
						<td width="75" align="right">'.number_format($floor_today_fob_arc_qnty,2,'.','').'</td>
						
						<td align="right">'.$floor_today_out_qnty.'&nbsp;</td>
						<td align="right">'.number_format($floor_today_aff_perc,2,'.','').'&nbsp;</td>
						<td align="right">'.$floor_today_smv.'&nbsp;</td>
						<td align="right">&nbsp;</td>
						<td align="right">'.$floor_achv_smv.'&nbsp;</td>
						<td align="right">'.number_format($floor_today_aff_perc,2,'.','').'&nbsp;</td>
						<td align="right">'.number_format($floor_cm_value,2,'.','').'&nbsp;</td>
						<td align="right">'.$floor_tot_prod.'&nbsp;</td>
						<td>&nbsp;</td>
						<td align="right">'.$floor_wip.'&nbsp;</td>
						<td align="right">'.number_format($floor_tot_smv_used,2,'.','').'&nbsp;</td>
						<td align="right">'.number_format($floor_tot_achv_smv,2,'.','').'&nbsp;</td>
						<td align="right">'.number_format($floor_avg_aff_perc,2,'.','').'&nbsp;</td>
						<td>&nbsp;</td>
					</tr>';
			}//'.$floor_item_smv.'
			
			$location_today_ach_perc=$location_today_out_qnty/$location_today_target*100;
			$location_today_aff_perc=$location_achv_smv/$location_today_smv*100;
			$location_avg_aff_perc=$location_tot_achv_smv/$location_tot_smv_used*100;
			$html.='<tr bgcolor="#E9F3FF">
						<td colspan="6" align="right">Location Total</td>
						<td align="right">&nbsp;</td>
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
						
						<td width="75" align="right">&nbsp;</td>
						<td width="75" align="right">'.number_format($location_today_tar_fob_val,2,'.','').'</td>
						<td width="75" align="right">'.number_format($location_today_fob_arc_qnty,2,'.','').'</td>
						
						<td align="right">'.$location_today_out_qnty.'&nbsp;</td>
						<td align="right">'.number_format($location_today_ach_perc,2,'.','').'&nbsp;</td>
						<td align="right">'.$location_today_smv.'&nbsp;</td>
						<td align="right">&nbsp;</td>
						<td align="right">'.$location_achv_smv.'&nbsp;</td>
						<td align="right">'.number_format($location_today_aff_perc,2,'.','').'&nbsp;</td>
						<td align="right">'.number_format($location_cm_value,2,'.','').'&nbsp;</td>
						<td align="right">'.$location_tot_prod.'&nbsp;</td>
						<td>&nbsp;</td>
						<td align="right">'.$location_wip.'&nbsp;</td>
						<td align="right">'.number_format($location_tot_smv_used,2,'.','').'&nbsp;</td>
						<td align="right">'.number_format($location_tot_achv_smv,2,'.','').'&nbsp;</td>
						<td align="right">'.number_format($location_avg_aff_perc,2,'.','').'&nbsp;</td>
						<td>&nbsp;</td>
					</tr>';
		}//'.$location_item_smv.'
		
		
		$suncon_line_data_array=array();
		foreach($resultSub as $row )
		{
			$sewing_line_id='';
			if($row[csf('prod_reso_allo')]==1)
			{
				$sewing_line_id=$prod_reso_arr[$row[csf('line_id')]];
			}
			else
			{
				$sewing_line_id=$row[csf('line_id')];
			}
			
			if($lineSerialArr[$sewing_line_id]=="")
			{
				$lastSlNo++;
				$slNo=$lastSlNo;
				$lineSerialArr[$sewing_line_id];
			}
			else $slNo=$lineSerialArr[$sewing_line_id];
			
			$suncon_line_data_array[$row[csf('location_id')]][$row[csf('floor_id')]][$slNo].=$row[csf('subcon_job')]."##".$row[csf('party_id')]."##".$row[csf('cust_style_ref')]."##".$row[csf('gmts_item_id')]."##".$row[csf('po_id')]."##".$row[csf('order_no')]."##".$row[csf('order_quantity')]."##".$row[csf('rate')]."##".$row[csf('smv')]."##".$row[csf('prod_reso_allo')]."##".$row[csf('line_id')]."##".$row[csf('qnty')].":";
		}
		//print_r($suncon_line_data_array);
		$html_sub=''; $i=1;
		foreach($suncon_line_data_array as $location=>$locData )
		{
			$location_order_qnty_sub=0; 
			$location_machine_qnty_sub=0; 
			$location_worker_sub=0; 
			$location_operator_sub=0; 
			$location_helper_sub=0; 
			$location_prev_out_qnty_sub=0;
			$location_today_target_sub=0; 
			$location_today_tar_fob_val_sub=0;
			$location_today_fob_acv_sub=0; 
			$location_today_out_qnty_sub=0; 
			$location_today_smv_sub=0; 
			$location_item_smv_sub=0; 
			$location_achv_smv_sub=0; 
			$location_tot_prod_sub=0; 
			$location_tot_smv_used_sub=0; 
			$location_tot_achv_smv_sub=0;
			
			foreach($locData as $floor=>$floorData )
			{
				$html_sub.='<tr bgcolor="#EFEFEF"><td colspan="35"><b>Floor name: '.$floorArr[$floor].'; Location name: '.$locationArr[$location].'</b></td></tr>';
			
				$floor_order_qnty_sub=0; 
				$floor_machine_qnty_sub=0; 
				$floor_worker_sub=0; 
				$floor_operator_sub=0; 
				$floor_helper_sub=0; 
				$floor_prev_out_qnty_sub=0;
				$floor_today_target_sub=0; 
				$floor_today_tar_fob_val_sub=0;
				$floor_today_fob_acv_sub=0; 
				$floor_today_out_qnty_sub=0; 
				$floor_today_smv_sub=0; 
				$floor_item_smv_sub=0; 
				$floor_achv_smv_sub=0; 
				$floor_tot_prod_sub=0; 
				$floor_tot_smv_used_sub=0; 
				$floor_tot_achv_smv_sub=0;
				
				ksort($floorData);
				foreach($floorData as $slNo=>$lineData )
				{
					$lineSlData=explode(":",chop($lineData,':'));
					foreach($lineSlData as $value )
					{
						$lineDataArr=explode("##",$value);
						$subcon_job=$lineDataArr[0];
						$party_id=$lineDataArr[1];
						$cust_style_ref=$lineDataArr[2];
						$gmts_item_id=$lineDataArr[3];
						$po_id=$lineDataArr[4];
						$po_number=$lineDataArr[5];
						$po_quantity=$lineDataArr[6];
						$po_rate=$lineDataArr[7];
						$item_smv=$lineDataArr[8];
						$prod_reso_allo=$lineDataArr[9];
						$line_id=$lineDataArr[10];
						$qnty=$lineDataArr[11];
						
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$mst_sql= "SELECT sum(production_qnty) AS prev_out_qnty, min(production_date) as fstinput_date from subcon_gmts_prod_dtls where order_id=".$po_id." and production_date<$txt_date and gmts_item_id='".$gmts_item_id."' and location_id='".$location."' and floor_id='".$floor."' and prod_reso_allo='".$prod_reso_allo."' and line_id='".$line_id."' and is_deleted=0 and status_active=1"; 
						//echo $mst_sql;//die;
						$dataArray = sql_select($mst_sql);
						$fstinput_date=$dataArray[0][csf('fstinput_date')]; 
						$prev_out_qnty=$dataArray[0][csf('prev_out_qnty')]; 
						$today_ach_perc=$qnty/$prod_resource_array[$line_id][$pr_date]['tpd']*100;
						
						$total_adjustment=0;
						$smv_adjustmet_type=$prod_resource_array[$line_id][$pr_date]['smv_adjust_type'];
						if(str_replace("'","",$smv_adjustmet_type)==1) $total_adjustment=$prod_resource_array[$line_id][$pr_date]['smv_adjust'];
						if(str_replace("'","",$smv_adjustmet_type)==2) $total_adjustment=($prod_resource_array[$line_id][$pr_date]['smv_adjust'])*(-1);
						
						$today_smv=$prod_resource_array[$line_id][$pr_date]['tsmv']+$total_adjustment;
						$achv_smv=$qnty*$item_smv; 
						$today_aff_perc=$achv_smv/$today_smv*100;
						$total_prod=$qnty+$prev_out_qnty;	
						
						$dataLibArray=sql_select("select b.date_calc from lib_capacity_calc_mst a, lib_capacity_calc_dtls b where a.id=b.mst_id and a.comapny_id='$comapny_id' and b.date_calc between '$fstinput_date' and $txt_date and day_status=1");
						$no_of_days=0; $total_smv_used=0; $total_adjust=0;
						foreach($dataLibArray as $libRow)
						{
							$date_calc=change_date_format($libRow[csf('date_calc')]);
							$smv_adjustmet_type=$prod_resource_array[$line_id][$date_calc]['smv_adjust_type'];
							if(str_replace("'","",$smv_adjustmet_type)==1) $total_adjust=$prod_resource_array[$line_id][$date_calc]['smv_adjust'];
							else if(str_replace("'","",$smv_adjustmet_type)==2) $total_adjust=($prod_resource_array[$line_id][$date_calc]['smv_adjust'])*(-1);
							else $total_adjust=0;
							$total_smv_used+=$prod_resource_array[$line_id][$date_calc]['tsmv']+$total_adjust;
							$no_of_days++;
						}

						$avg_per_day=$total_prod/$no_of_days;
						$total_smv_achv=$item_smv*$total_prod;
						$avg_aff_perc=$total_smv_achv/$total_smv_used*100;
						
						$actual_line_arr.=$line_id.",";
						$sewing_line='';
						if($prod_reso_allo==1)
						{
							$line_number=explode(",",$prod_reso_arr[$line_id]);
							foreach($line_number as $val)
							{
								if($sewing_line=='') $sewing_line=$lineArr[$val]; else $sewing_line.=",".$lineArr[$val];
							}
						}
						else $sewing_line=$lineArr[$line_id];
						
						$html_sub.="<tr bgcolor='$bgcolor' onclick=change_color('tr2_$i','$bgcolor') id=tr2_$i>";
						$html_sub.='<td width="40">'.$i.'</td>
								<td width="50"><p>'.$sewing_line.'&nbsp;</p></td>
								<td width="70"><p>'.$buyerArr[$party_id].'</p></td>
								<td width="110"><p>'.$po_number.'</p></td>
								<td width="110"><p>'.$cust_style_ref.'</p></td>
								<td width="140"><p>'.$garments_item[$gmts_item_id].'</p></td>
								<td width="75" align="right">'.$po_quantity.'&nbsp;</td>
								<td width="70" align="right">'.$prod_resource_array[$line_id][$pr_date]['active_machine'].'&nbsp;</td>
								<td width="60" align="right">'.$prod_resource_array[$line_id][$pr_date]['man_power'].'&nbsp;</td>
								<td width="60" align="right">'.$prod_resource_array[$line_id][$pr_date]['operator'].'&nbsp;</td>
								<td width="60" align="right">'.$prod_resource_array[$line_id][$pr_date]['helper'].'&nbsp;</td>
								<td width="120"><p>'.$prod_resource_array[$line_id][$pr_date]['line_chief'].'&nbsp;</p></td>
								<td width="80" align="center">'.change_date_format($fstinput_date).'&nbsp;</td>
								<td width="75" align="right"><a href="##" onclick="openmypage('.$comapny_id.",'".$po_id."',".$gmts_item_id.",".$location.",".$floor.",".$line_id.",".$txt_date.",'prev_qnty_sub',2".",".$prod_reso_allo.')">'.number_format($prev_out_qnty,0,'.','').'</a>&nbsp;</td>
								<td width="75" align="right">'.$prod_resource_array[$line_id][$pr_date]['tpd'].'&nbsp;</td>
								<td width="75" align="right">'.$po_rate.'</td>
								<td width="75" align="right">'.number_format($po_rate*$prod_resource_array[$line_id][$pr_date]['tpd'],2,'.','').'</td>
								<td width="75" align="right">'.number_format($po_rate*$qnty,2,'.','').'</td>
								<td width="75" align="right"><a href="##" onclick="openmypage('.$comapny_id.",'".$po_id."',".$gmts_item_id.",".$location.",".$floor.",".$line_id.",".$txt_date.",'today_prod_sub',2".",".$prod_reso_allo.')">'.$qnty.'</a>&nbsp;</td>
								<td width="75" align="right">'.number_format($today_ach_perc,2).'&nbsp;</td>
								<td width="75" align="right">'.$today_smv.'&nbsp;</p></td>
								<td width="70" align="right">'.$item_smv.'&nbsp;</td>
								<td width="100" align="right">'.$achv_smv.'&nbsp;</td>
								<td width="75" align="right">'.number_format($today_aff_perc,2).'&nbsp;</td>
								<td width="90" align="right"><a href="##" onclick="openmypage('.$comapny_id.",'".$po_id."',".$gmts_item_id.",".$location.",".$floor.",".$line_id.",".$txt_date.",'tot_prod_sub',2".",".$prod_reso_allo.')">'.$total_prod.'</a>&nbsp;</td>
								<td width="80" align="right">'.number_format($avg_per_day,2,'.','').'&nbsp;</td>
								<td width="100" align="right"><a href="##" onclick="openmypage('.$comapny_id.",'".$po_id."',".$gmts_item_id.",".$location.",".$floor.",".$line_id.",".$txt_date.",'tot_smv_used',"."'".$fstinput_date."'".",".$prod_reso_allo.')">'.number_format($total_smv_used,2,'.','').'</a>&nbsp;</td>
								<td width="100" align="right">'.number_format($total_smv_achv,2,'.','').'&nbsp;</td>
								<td align="right" width="100">'.number_format($avg_aff_perc,2,'.','').'&nbsp;</td>';
									
						 $html_sub.='<td><input type="button" value="View" class="formbutton" onclick="show_line_remarks('.$comapny_id.",'".$po_id."','".$floor."','".$line_id."','remarks_popup_sub',".$txt_date.')"/></td>
							</tr>';
			   
						$i++;
						
						$total_order_qnty_sub+=$po_quantity; 
						$total_machine_qnty_sub+=$prod_resource_array[$line_id][$pr_date]['active_machine']; 
						$tot_worker_sub+=$prod_resource_array[$line_id][$pr_date]['man_power']; 
						$tot_operator_sub+=$prod_resource_array[$line_id][$pr_date]['operator']; 
						$tot_helper_sub+=$prod_resource_array[$line_id][$pr_date]['helper']; 
						$tot_prev_out_qnty_sub+=$prev_out_qnty;
						$tot_today_targe_subt+=$prod_resource_array[$line_id][$pr_date]['tpd']; 
						
						$tot_today_tar_fob_val_sub+=($prod_resource_array[$line_id][$pr_date]['tpd']*$po_rate); 
						$tot_today_fob_arc_qnty_sub+=($qnty*$po_rate); 
						
						$tot_today_out_qnty_sub+=$qnty; 
						$tot_today_smv_sub+=$today_smv; 
						$tot_item_smv_sub+=$item_smv; 
						$tot_achv_smv_sub+=$achv_smv; 
						$grand_tot_prod_sub+=$total_prod; 
						$grand_tot_smv_used_sub+=$total_smv_used; 
						$grand_tot_achv_smv_sub+=$total_smv_achv;
						
						$location_order_qnty_sub+=$po_quantity; 
						$location_machine_qnty_sub+=$prod_resource_array[$line_id][$pr_date]['active_machine']; 
						$location_worker_sub+=$prod_resource_array[$line_id][$pr_date]['man_power']; 
						$location_operator_sub+=$prod_resource_array[$line_id][$pr_date]['operator']; 
						$location_helper_sub+=$prod_resource_array[$line_id][$pr_date]['helper']; 
						$location_prev_out_qnty_sub+=$prev_out_qnty;
						$location_today_target_sub+=$prod_resource_array[$line_id][$pr_date]['tpd']; 
						
						$location_today_tar_fob_val_sub+=($prod_resource_array[$line_id][$pr_date]['tpd']*$po_rate); 
						$location_today_fob_arc_qnty_sub+=($qnty*$po_rate); 
						
						$location_today_out_qnty_sub+=$qnty; 
						$location_today_smv_sub+=$today_smv; 
						$location_item_smv_sub+=$item_smv; 
						$location_achv_smv_sub+=$achv_smv; 
						$location_tot_prod_sub+=$total_prod; 
						$location_tot_smv_used_sub+=$total_smv_used; 
						$location_tot_achv_smv_sub+=$total_smv_achv;
						
						$floor_order_qnty_sub+=$po_quantity; 
						$floor_machine_qnty_sub+=$prod_resource_array[$line_id][$pr_date]['active_machine']; 
						$floor_worker_sub+=$prod_resource_array[$line_id][$pr_date]['man_power']; 
						$floor_operator_sub+=$prod_resource_array[$line_id][$pr_date]['operator']; 
						$floor_helper_sub+=$prod_resource_array[$line_id][$pr_date]['helper']; 
						$floor_prev_input_qnty_sub+=$prev_input_qnty; 
						$floor_prev_out_qnty_sub+=$prev_out_qnty;
						$floor_prev_wip_sub+=$prev_wip;
						$floor_today_target_sub+=$prod_resource_array[$line_id][$pr_date]['tpd']; 
						$floor_today_input_qnty_sub+=$today_input_qnty; 
						
						$floor_today_tar_fob_val_sub+=($prod_resource_array[$line_id][$pr_date]['tpd']*$po_rate); 
						$floor_today_fob_arc_qnty_sub+=($qnty*$po_rate); 
						
						$floor_today_out_qnty_sub+=$qnty; 
						$floor_today_smv_sub+=$today_smv; 
						$floor_item_smv_sub+=$item_smv; 
						$floor_achv_smv_sub+=$achv_smv; 
						$floor_cm_value_sub+=$cm_value; 
						$floor_tot_prod_sub+=$total_prod; 
						$floor_wip_sub+=$wip; 
						$floor_tot_smv_used_sub+=$total_smv_used; 
						$floor_tot_achv_smv_sub+=$total_smv_achv;

						if($duplicate_sub_array[$prod_reso_allo][$line_id]=="")
						{
							$total_actual_machine_qnty_sub+=$prod_resource_array[$line_id][$pr_date]['active_machine']; 
							$tot_actual_worker_sub+=$prod_resource_array[$line_id][$pr_date]['man_power']; 
							$tot_actual_operator_sub+=$prod_resource_array[$line_id][$pr_date]['operator']; 
							$tot_actual_helper_sub+=$prod_resource_array[$line_id][$pr_date]['helper'];
							$tot_actual_today_target_sub+=$prod_resource_array[$line_id][$pr_date]['tpd']; 
							$tot_actual_today_smv_sub+=$today_smv; 
							$grand_tot_actual_smv_used_sub+=$total_smv_used; 
							
							$duplicate_sub_array[$prod_reso_allo][$line_id]=$line_id;
						}
					
					}
				}
				
				$floor_today_ach_perc_sub=$floor_today_out_qnty_sub/$floor_today_target_sub*100;
				$floor_today_aff_perc_sub=$floor_achv_smv_sub/$floor_today_smv_sub*100;
				$floor_avg_aff_perc_sub=$floor_tot_achv_smv_sub/$floor_tot_smv_used_sub*100;
				
				$html_sub.='<tr bgcolor="#CCCCCC">
						<td colspan="6" align="right">Floor Total</td>
						<td align="right">&nbsp;</td>
						<td align="right">'.$floor_machine_qnty_sub.'&nbsp;</td>
						<td align="right">'.$floor_worker_sub.'&nbsp;</td>
						<td align="right">'.$floor_operator_sub.'&nbsp;</td>
						<td align="right">'.$floor_helper_sub.'&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td align="right">'.$floor_prev_out_qnty_sub.'&nbsp;</td>
						<td align="right">'.$floor_today_target_sub.'&nbsp;</td>

						<td width="75" align="right">&nbsp;</td>
						<td width="75" align="right">'.number_format($floor_today_tar_fob_val_sub,2,'.','').'</td>
						<td width="75" align="right">'.number_format($floor_today_fob_arc_qnty_sub,2,'.','').'</td>
						
						<td align="right">'.$floor_today_out_qnty_sub.'&nbsp;</td>
						<td align="right">'.number_format($floor_today_aff_perc_sub,2,'.','').'&nbsp;</td>
						<td align="right">'.$floor_today_smv_sub.'&nbsp;</td>
						<td align="right">'.$floor_item_smv_sub.'&nbsp;</td>
						<td align="right">'.$floor_achv_smv_sub.'&nbsp;</td>
						<td align="right">'.number_format($floor_today_ach_perc_sub,2,'.','').'&nbsp;</td>
						<td align="right">'.$floor_tot_prod_sub.'&nbsp;</td>
						<td>&nbsp;</td>
						<td align="right">'.number_format($floor_tot_smv_used_sub,2,'.','').'&nbsp;</td>
						<td align="right">'.number_format($floor_tot_achv_smv_sub,2,'.','').'&nbsp;</td>
						<td align="right">'.number_format($floor_avg_aff_perc_sub,2,'.','').'&nbsp;</td>
						<td>&nbsp;</td>
					</tr>';
			}
			
			$location_today_ach_perc_sub=$location_today_out_qnty_sub/$location_today_target_sub*100;
			$location_today_aff_perc_sub=$location_achv_smv_sub/$location_today_smv_sub*100;
			$location_avg_aff_perc_sub=$location_tot_achv_smv_sub/$location_tot_smv_used_sub*100;
			$html_sub.='<tr bgcolor="#E9F3FF">
						<td colspan="6" align="right">Location Total</td>
						<td align="right">&nbsp;</td>
						<td align="right">'.$location_machine_qnty_sub.'&nbsp;</td>
						<td align="right">'.$location_worker_sub.'&nbsp;</td>
						<td align="right">'.$location_operator_sub.'&nbsp;</td>
						<td align="right">'.$location_helper_sub.'&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td align="right">'.$location_prev_out_qnty_sub.'&nbsp;</td>
						<td align="right">'.$location_today_target_sub.'&nbsp;</td>
						
						<td width="75" align="right">&nbsp;</td>
						<td width="75" align="right">'.number_format($location_today_tar_fob_val_sub,2,'.','').'</td>
						<td width="75" align="right">'.number_format($location_today_fob_arc_qnty_sub,2,'.','').'</td>
						
						<td align="right">'.$location_today_out_qnty_sub.'&nbsp;</td>
						<td align="right">'.number_format($location_today_ach_perc_sub,2,'.','').'&nbsp;</td>
						<td align="right">'.$location_today_smv_sub.'&nbsp;</td>
						<td align="right">'.$location_item_smv_sub.'&nbsp;</td>
						<td align="right">'.$location_achv_smv_sub.'&nbsp;</td>
						<td align="right">'.number_format($location_today_aff_perc_sub,2,'.','').'&nbsp;</td>
						<td align="right">'.$location_tot_prod_sub.'&nbsp;</td>
						<td>&nbsp;</td>
						<td align="right">'.number_format($location_tot_smv_used_sub,2,'.','').'&nbsp;</td>
						<td align="right">'.number_format($location_tot_achv_smv_sub,2,'.','').'&nbsp;</td>
						<td align="right">'.number_format($location_avg_aff_perc_sub,2,'.','').'&nbsp;</td>
						<td>&nbsp;</td>
					</tr>';
		}
		ob_start();	
		?>
		<fieldset style="width:2880px">
			<table width="2870" cellpadding="0" cellspacing="0"> 
				<tr class="form_caption">
					<td colspan="35" align="center"><strong><? echo $report_title; ?></strong></td> 
				</tr>
				<tr class="form_caption">
					<td colspan="35" align="center"><strong><? echo $companyArr[$comapny_id]; ?></strong></td> 
				</tr>
			</table>
			<table id="table_header_1" class="rpt_table" width="2870" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
					<th width="40">SL</th>
					<th width="50">Line No</th>
					<th width="70">Buyer</th>
					<th width="110">Order No</th>
					<th width="110">Style Ref.</th>
					<th width="140">Garments Item</th>
					<th width="75">CM per dzn(As per Budget)</th>
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
					<th width="75">FOB/PCs</th>
					<th width="75">Target FOB Value</th>
					<th width="75">FOB Achieve Value</th>
					<th width="75">Today Prod.</th>
					<th width="75">Today Achv. %</th>
					<th width="75">Today SMV</th>
					<th width="70">Item SMV</th>
					<th width="100">Achieved SMV</th>
					<th width="75" title="(Today Achieved SMV/Today SMV)*100">Today Eff. %</th>
					<th width="110">Today CM Value</th>
					<th width="90">Total Prod.</th>
					<th width="80">Avg. Prod./Day</th>
					<th width="80">WIP</th>
					<th width="75">TTL SMV Used</th>
					<th width="75">TTL SMV Achv.</th>
					<th width="75">Avg. Eff. %</th>
                    <th>Remarks</th>
				</thead>
			</table>
			<div style="width:2890px; max-height:400px; overflow-y:scroll" id="scroll_body">
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
							<th>&nbsp;</th>
							<th align="right"><? echo number_format($tot_today_tar_fob_val,2,'.',''); ?>&nbsp;</th>
							<th align="right"><? echo number_format($tot_today_fob_arc_qnty,2,'.',''); ?>&nbsp;</th>
							<th align="right"><? echo $tot_today_out_qnty; ?>&nbsp;</th>
							<th align="right"><? echo number_format($grand_today_ach_perc,2,'.',''); ?>&nbsp;</th>
							<th align="right"><? echo $tot_actual_today_smv; ?>&nbsp;</th>
							<th align="right"><? echo $tot_item_smv; ?>&nbsp;</th>
							<th align="right"><? echo $tot_achv_smv; ?>&nbsp;</th>
							<th align="right"><? echo number_format($grand_today_aff_perc,2,'.',''); ?>&nbsp;</th>
							<th align="right"><? echo number_format($tot_cm_value,2,'.',''); ?>&nbsp;</th>
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
            <br />
            <u><b>Sub-Contract</b></u> 
            <table id="table_header_2" class="rpt_table" width="2470" cellpadding="0" cellspacing="0" border="1" rules="all">
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
					<th width="75">Prev. Prod. Qnty</th>
					<th width="75">Today Target</th>
					<th width="75">FOB/PCs</th>
					<th width="75">Target FOB Value</th>
					<th width="75">FOB Achieve Value</th>
					<th width="75">Today Prod.</th>
					<th width="75">Today Achv. %</th>
					<th width="75">Today SMV</th>
					<th width="70">Item SMV</th>
					<th width="100">Achieved SMV</th>
					<th width="75">Today Eff. %</th>
					<th width="90">Total Prod.</th>
					<th width="80">Avg. Prod./Day</th>
					<th width="100">TTL SMV Used</th>
					<th width="100">TTL SMV Achv.</th>
					<th width="100">Avg. Eff. %</th>
                    <th>Remarks</th>
				</thead>
			</table>
			<div style="width:2470px; max-height:400px; overflow-y:scroll" id="scroll_body">
				<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
					<? echo $html_sub; ?>
					<tfoot>
						<?
							$grand_today_ach_perc_sub=$tot_today_out_qnty_sub/$tot_actual_today_target_sub*100;
							$grand_today_aff_perc_sub=$tot_achv_smv_sub/$tot_actual_today_smv_sub*100;
							$grand_avg_aff_perc_sub=$grand_tot_achv_smv_sub/$grand_tot_actual_smv_used_sub*100;
                        ?>
                        <tr>
							<th colspan="6" align="right">Actual Total</th>
							<th align="right"><? //echo $total_order_qnty_sub; ?>&nbsp;</th>
							<th align="right"><? echo $total_actual_machine_qnty_sub; ?>&nbsp;</th>
							<th align="right"><? echo $tot_actual_worker_sub; ?>&nbsp;</th>
							<th align="right"><? echo $tot_actual_operator_sub; ?>&nbsp;</th>
							<th align="right"><? echo $tot_actual_helper_sub; ?>&nbsp;</th>
							<th>&nbsp;</th>
							<th>&nbsp;</th>
							<th align="right"><? echo $tot_prev_out_qnty_sub; ?>&nbsp;</th>
							<th align="right"><? echo $tot_actual_today_target_sub; ?>&nbsp;</th>
							<th>&nbsp;</th>
							<th align="right"><? echo number_format($tot_today_tar_fob_val_sub,2,'.',''); ?>&nbsp;</th>
							<th align="right"><? echo number_format($tot_today_fob_arc_qnty_sub,2,'.',''); ?>&nbsp;</th>
							<th align="right"><? echo $tot_today_out_qnty_sub; ?>&nbsp;</th>
							<th align="right"><? echo number_format($grand_today_ach_perc_sub,2,'.',''); ?>&nbsp;</th>
							<th align="right"><? echo $tot_actual_today_smv_sub; ?>&nbsp;</th>
							<th align="right"><? echo $tot_item_smv_sub; ?>&nbsp;</th>
							<th align="right"><? echo $tot_achv_smv_sub; ?>&nbsp;</th>
							<th align="right"><? echo number_format($grand_today_aff_perc_sub,2,'.',''); ?>&nbsp;</th>
                            <th align="right"><? echo $grand_tot_prod_sub; ?>&nbsp;</th>
							<th>&nbsp;</th>
							<th align="right"><? echo number_format($grand_tot_actual_smv_used_sub,2,'.',''); ?>&nbsp;</th>
							<th align="right"><? echo number_format($grand_tot_achv_smv_sub,2,'.',''); ?>&nbsp;</th>
							<th align="right"><? echo number_format($grand_avg_aff_perc_sub,2,'.',''); ?>&nbsp;</th>
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
					if($actual_line_arr!="") 
					{
						$actual_line_arr=implode(",",array_unique(explode(",",chop($actual_line_arr,","))));
						$line_cond=" and a.id not in ($actual_line_arr)";
					}
			
			 		$dataArray=sql_select("select a.id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power, b.operator, b.helper, b.line_chief, b.target_per_hour, b.working_hour,d.remarks from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_time d where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and a.company_id=$comapny_id and b.pr_date=".$txt_date." and d.shift_id=1 and a.is_deleted=0 and b.is_deleted=0 $line_cond");
					$l=1;
					foreach( $dataArray as $row )
					{
						if ($l%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$sewing_line='';
						$line_number=explode(",",$row[csf('line_number')]);
						foreach($line_number as $val)
						{
							if($sewing_line=='') $sewing_line=$lineArr[$val]; else $sewing_line.=",".$lineArr[$val];
						}
					?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tbltr_<? echo $l; ?>','<? echo $bgcolor; ?>')" id="tbltr_<? echo $l; ?>">
                        	<td width="40"><? echo $l; ?></td>
                            <td width="100"><p><? echo $sewing_line; ?>&nbsp;</p></td>
                            <td width="100" align="right"><p>&nbsp;<? echo $floorArr[$row[csf('floor_id')]]; ?></p></td>
                            <td width="75" align="right">&nbsp;<? echo $row[csf('man_power')]; ?></th>
                            <td width="75" align="right">&nbsp;<? echo $row[csf('operator')]; ?></th>
                            <td width="75" align="right">&nbsp;<? echo $row[csf('helper')]; ?></td>
                            <td width="75" align="right">&nbsp;<? echo $row[csf('working_hour')]; ?></td>
                            <td><? echo $row[csf('remarks')]; ?>&nbsp;</td>
                        </tr>
                    <?
						$l++;
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
//Two Button End
if($action=="report_generate2") //Inclouds No Prod. Line here...
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$companyArr = return_library_array("select id,company_name from lib_company","id","company_name"); 
	$buyerArr = return_library_array("select id,short_name from lib_buyer","id","short_name"); 
	$locationArr = return_library_array("select id,location_name from lib_location","id","location_name"); 
	$floorArr = return_library_array("select id,floor_name from lib_prod_floor","id","floor_name");
	
	$lineArr=array(); $lineSerialArr=array(); $lastSlNo='';
	$lineDataArr = sql_select("select id, line_name, sewing_line_serial from lib_sewing_line where is_deleted=0 and status_active=1 order by sewing_line_serial"); 
	foreach($lineDataArr as $lRow)
	{
		$lineArr[$lRow[csf('id')]]=$lRow[csf('line_name')];
		$lineSerialArr[$lRow[csf('id')]]=$lRow[csf('sewing_line_serial')];
		$lastSlNo=$lRow[csf('sewing_line_serial')];
	}
	
	 $type=str_replace("'","",$type);
	$cbo_no_prod_type=str_replace("'","",$cbo_no_prod_type);
	$comapny_id=str_replace("'","",$cbo_company_id);
	
	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$comapny_id and variable_list=23 and is_deleted=0 and status_active=1");

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
	
	if($type==3)// Button 3 , No Prod. Line Here...
	{
		$costing_per_arr = return_library_array("select job_no,costing_per from wo_pre_cost_mst","job_no","costing_per"); 
		$tot_cost_arr = return_library_array("select job_no, cm_for_sipment_sche from wo_pre_cost_dtls","job_no","cm_for_sipment_sche"); 
		$prod_reso_arr=return_library_array( "select id,line_number from prod_resource_mst",'id','line_number');
		
		$cm_cost_arr = return_library_array("select a.job_no,
        CASE WHEN a.costing_per = 2 THEN (b.cm_cost*12)
             WHEN a.costing_per = 3 THEN (b.cm_cost/2)
             WHEN a.costing_per = 4 THEN (b.cm_cost/3)
             WHEN a.costing_per = 5 THEN (b.cm_cost/4)
         ELSE b.cm_cost END as cm_cost from wo_pre_cost_mst a, wo_pre_cost_dtls b where a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","job_no","cm_cost"); 



		if($db_type==0)
		{
			$job_po_id_arr=return_library_array( "select job_no_mst, group_concat(id) as po_id from wo_po_break_down group by job_no_mst",'job_no_mst','po_id');
		}
		else
		{
			$job_po_id_arr=return_library_array("select job_no_mst,LISTAGG(id,',') WITHIN GROUP (ORDER BY id) as po_id from wo_po_break_down group by job_no_mst",'job_no_mst','po_id');
		}
		//For Summary Report New Add No Prodcut
	

		$prod_resource_array=array();$prod_resource_array2=array(); $floor_line_arr=array();$location_floor_line_arr=array();
		/*echo "select a.id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power, b.operator, b.helper, b.line_chief, b.smv_adjust, b.smv_adjust_type, b.target_per_hour, b.working_hour from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id   and a.company_id=$comapny_id  and b.is_deleted=0";*/
		$dataArray=sql_select("select a.id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power, b.operator, b.helper, b.line_chief, b.smv_adjust, b.smv_adjust_type, b.target_per_hour, b.working_hour from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_time d where a.id=b.mst_id  and b.mast_dtl_id=d.mast_dtl_id and a.company_id=$comapny_id  and d.shift_id=1 and b.is_deleted=0");
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
			
			//$prod_resource_array2[$row[csf('line_number')]][$conv_pr_date]['id']=$row[csf('id')];
			$prod_resource_array2[$row[csf('line_number')]][$conv_pr_date]['man_power']+=$row[csf('man_power')];
			$prod_resource_array2[$row[csf('line_number')]][$conv_pr_date]['operator']+=$row[csf('operator')];
			$prod_resource_array2[$row[csf('line_number')]][$conv_pr_date]['helper']+=$row[csf('helper')];
			$prod_resource_array2[$row[csf('line_number')]][$conv_pr_date]['line_chief']+=$row[csf('line_chief')];
			$prod_resource_array2[$row[csf('line_number')]][$conv_pr_date]['active_machine']+=$row[csf('active_machine')];
			$prod_resource_array2[$row[csf('line_number')]][$conv_pr_date]['tpd']+=$row[csf('target_per_hour')]*$row[csf('working_hour')];
			$prod_resource_array2[$row[csf('line_number')]][$conv_pr_date]['tsmv']+=$row[csf('man_power')]*$row[csf('working_hour')]*60;
			$prod_resource_array2[$row[csf('line_number')]][$conv_pr_date]['smv_adjust']+=$row[csf('smv_adjust')];
			$prod_resource_array2[$row[csf('line_number')]][$conv_pr_date]['smv_adjust_type']=$row[csf('smv_adjust_type')];
			
			$floor_line_arr[$row[csf('location_id')]][$row[csf('floor_id')]][$conv_pr_date].=$row[csf('id')].",";
			$location_floor_line_arr[$row[csf('location_id')]][$conv_pr_date].=$row[csf('floor_id')].",";
		}
		//print_r($location_floor_line_arr);
		$pr_date=change_date_format(str_replace("'","",$txt_date));
		
		if(str_replace("'","",$cbo_source)!=0){$source_con=" and c.production_source=$cbo_source";}
		else{$source_con="";}
		
		if($db_type==0)
		{
			$sql="select a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.order_uom, a.gmts_item_id, a.set_break_down, a.total_set_qnty as ratio, group_concat(distinct(b.id)) as po_id, group_concat(concat_ws('**',b.id,b.po_number,b.po_quantity,b.unit_price)) as po_data, c.item_number_id, c.location, c.floor_id, c.prod_reso_allo, c.sewing_line, sum(c.production_quantity) as qnty from wo_po_details_master a, wo_po_break_down b, pro_garments_production_mst c where a.job_no=b.job_no_mst and a.company_name='$comapny_id' and b.id=c.po_break_down_id and c.location like '$location' and c.floor_id like '$floor' and c.sewing_line like '$line' and production_date=$txt_date and production_type=5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $source_con $buyer_id_cond group by a.job_no, c.location, c.floor_id, c.prod_reso_allo, c.sewing_line order by c.location, c.floor_id, c.sewing_line";//, c.item_number_id
		}
		else
		{
			$sql="select a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.order_uom, a.gmts_item_id, a.set_break_down, a.total_set_qnty as ratio, LISTAGG(b.id, ',') WITHIN GROUP (ORDER BY b.id) as po_id, LISTAGG(cast(b.id || '**' || b.po_number || '**' || b.po_quantity || '**' || b.unit_price as varchar2(4000)), ',') WITHIN GROUP (ORDER BY b.id) as po_data, c.item_number_id, c.location, c.floor_id, c.prod_reso_allo, c.sewing_line, sum(c.production_quantity) as qnty from wo_po_details_master a, wo_po_break_down b, pro_garments_production_mst c where a.job_no=b.job_no_mst and a.company_name='$comapny_id' and b.id=c.po_break_down_id and c.location like '$location' and c.floor_id like '$floor' and c.sewing_line like '$line' and production_date=$txt_date and production_type=5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $source_con $buyer_id_cond group by a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.order_uom, a.gmts_item_id, a.set_break_down, a.total_set_qnty, c.location, c.floor_id, c.prod_reso_allo, c.sewing_line, c.item_number_id order by c.location, c.floor_id";//, c.sewing_line
		}
		  //echo $sql;
		$line_data_array=array(); $job_arr=array();
		$result = sql_select($sql);
		foreach($result as $row )
		{ 
			$sewing_line_id='';
			if($row[csf('prod_reso_allo')]==1)
			{
				$sewing_line_id=$prod_reso_arr[$row[csf('sewing_line')]];
			}
			else
			{
				$sewing_line_id=$row[csf('sewing_line')];
			}
			
			if($lineSerialArr[$sewing_line_id]=="")
			{
				$lastSlNo++;
				$slNo=$lastSlNo;
				$lineSerialArr[$sewing_line_id];
			}
			else $slNo=$lineSerialArr[$sewing_line_id];
			
			//echo $slNo."**".$sewing_line_id."<br>";
			$po_data=implode(",",array_unique(explode(",",$row[csf('po_data')])));
			$line_type=1;
			
			$line_data_array[$row[csf('location')]][$row[csf('floor_id')]][$slNo].=$row[csf('job_no')]."##".$row[csf('company_name')]."##".$row[csf('buyer_name')]."##".$row[csf('style_ref_no')]."##".$row[csf('order_uom')]."##".$row[csf('gmts_item_id')]."##".$row[csf('set_break_down')]."##".$row[csf('ratio')]."##".$row[csf('po_id')]."##".$row[csf('item_number_id')]."##".$row[csf('prod_reso_allo')]."##".$row[csf('sewing_line')]."##".$row[csf('qnty')]."##".$line_type."##".$po_data.":";
		}
		//print_r($line_data_array).'aa';
		unset($result);
	
	
		$po_rate_arr=array();
		$itemRatedata=sql_select("select po_break_down_id, item_number_id, sum(order_quantity) as qty, sum(order_total) as amnt from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 group by po_break_down_id, item_number_id"); 
		foreach($itemRatedata as $iRow)
		{
			$po_rate_arr[$iRow[csf('po_break_down_id')]][$iRow[csf('item_number_id')]][1]=$iRow[csf('amnt')];
			$po_rate_arr[$iRow[csf('po_break_down_id')]][$iRow[csf('item_number_id')]][2]=$iRow[csf('qty')];
		}
		unset($itemRatedata);
		
		$sql_subconProd="select a.party_id, a.subcon_job, c.gmts_item_id, b.cust_style_ref, b.id as po_id, b.order_no, b.order_quantity, b.rate, b.smv, c.location_id, c.floor_id, c.prod_reso_allo, c.line_id, sum(c.production_qnty) as qnty FROM subcon_ord_mst a, subcon_ord_dtls b, subcon_gmts_prod_dtls c WHERE a.subcon_job=b.job_no_mst and c.order_id=b.id and a.company_id='$comapny_id' and c.production_type=2 and c.line_id like '$line' and c.location_id like '$location' and c.floor_id like '$floor' and c.production_date=$txt_date and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.party_id, a.subcon_job, c.gmts_item_id, b.cust_style_ref, b.id, b.order_no,b.order_quantity, b.rate, b.smv, c.location_id, c.floor_id, c.prod_reso_allo, c.line_id order by c.location_id, c.floor_id";
		$resultSub = sql_select($sql_subconProd);
		
		$i=1; $k=1; $html=''; $buyer_data_array=array();
		$total_order_qnty=0; $total_machine_qnty=0; $tot_worker=0; $tot_operator=0; $tot_helper=0; $tot_prev_input_qnty=0; $tot_prev_out_qnty=0; 
		$tot_prev_wip=0; $tot_today_target=0; $tot_today_input_qnty=0; $tot_today_out_qnty=0; $tot_today_smv=0; $tot_item_smv=0; $tot_achv_smv=0; 
		$tot_cm_value=0; $grand_tot_prod=0; $tot_wip=0; $grand_tot_smv_used=0; $grand_tot_achv_smv=0;
		$prod_lines='';
		foreach($line_data_array as $location=>$locData )
		{
			//echo 'ddd';
			
			
					
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
			$location_today_tar_fob_val=0;
			$location_today_fob_arc_qnty=0; 
			$location_today_out_qnty=0; 
			$location_today_smv=0; 
			$location_item_smv=0; 
			$location_achv_smv=0; 
			$location_cm_value=0; 
			$location_tot_prod=0; 
			$location_wip=0; 
			$location_tot_smv_used=0; 
			$location_tot_achv_smv=0;
			
			foreach($locData as $floor=>$floorData )
			{
				$html.='<tr bgcolor="#EFEFEF"><td colspan="36"><b>Floor name: '.$floorArr[$floor].'; Location name: '.$locationArr[$location].'</b></td></tr>';
			
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
				
				$floor_today_tar_fob_val=0;
				$floor_today_fob_arc_qnty=0; 

				$floor_today_out_qnty=0; 
				$floor_today_smv=0; 
				$floor_item_smv=0; 
				$floor_achv_smv=0; 
				$floor_cm_value=0; 
				$floor_tot_prod=0; 
				$floor_wip=0; 
				$floor_tot_smv_used=0; 
				$floor_tot_achv_smv=0;
				
				
				ksort($floorData);
				foreach($floorData as $slNo=>$lineData )
				{
					//print_r($lineData);
					$lineSlData=explode(":",chop($lineData,':'));
					foreach($lineSlData as $value )
					{
						$lineDataArr=explode("##",$value);
						$row[csf('job_no')]=$lineDataArr[0];
						$row[csf('company_name')]=$lineDataArr[1];
						$row[csf('buyer_name')]=$lineDataArr[2];
						$row[csf('style_ref_no')]=$lineDataArr[3];
						$row[csf('order_uom')]=$lineDataArr[4];
						$row[csf('gmts_item_id')]=$lineDataArr[5];
						$row[csf('set_break_down')]=$lineDataArr[6];
						$row[csf('ratio')]=$lineDataArr[7];
						$row[csf('po_id')]=$lineDataArr[8];
						$row[csf('item_number_id')]=$lineDataArr[9];
						$row[csf('prod_reso_allo')]=$lineDataArr[10];
					    $row[csf('sewing_line')]=$lineDataArr[11];
						$row[csf('qnty')]=$lineDataArr[12];
						$line_type=$lineDataArr[13];
						$row[csf('po_data')]=$lineDataArr[14];
						//echo $row[csf('sewing_line')].'<br>';
						//echo $line_type.'aa';
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				
						$po_number=''; $po_quantity=0; $item_smv=0; $po_array=array(); $item_po_amnt=0; $item_po_quantity=0;
						 
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
							$po_array[$po_val[0]]['rate']=$po_val[3];
						}
						
						$po_ids = array_unique(explode(",",$row[csf("po_id")]));
						foreach($po_ids as $id)
						{
							if($po_number=="") $po_number=$po_array[$id]['no']; else $po_number.=",".$po_array[$id]['no'];
							$po_quantity+=$po_array[$id]['qnty']*$set_qty;
							$item_po_amnt+=$po_rate_arr[$id][$row[csf('item_number_id')]][1];
							$item_po_quantity+=$po_rate_arr[$id][$row[csf('item_number_id')]][2];
							//$po_rate=$po_array[$id]['rate'];
						}
						
						$po_rate=number_format($item_po_amnt/$item_po_quantity,2,'.','');
						//$job_po_id=$job_po_id_arr[$row[csf("job_no")]]; 
						$job_po_id=$row[csf("po_id")];
						if($job_po_id!="") $job_po_id=$job_po_id;else $job_po_id=0;
						$mst_sql= "SELECT  
										min(CASE WHEN production_type ='4' THEN production_date END) AS frstinput_date,
										sum(CASE WHEN production_type ='4' and production_date=$txt_date and po_break_down_id in(".$job_po_id.") THEN production_quantity ELSE 0 END) AS today_input_qnty,
										sum(CASE WHEN production_type ='4' and production_date<$txt_date THEN production_quantity ELSE 0 END) AS prev_input_qnty,
										sum(CASE WHEN production_type ='5' and production_date<$txt_date THEN production_quantity ELSE 0 END) AS prev_out_qnty
									from 
										pro_garments_production_mst 
									where  
										po_break_down_id in(".$job_po_id.") and item_number_id='".$row[csf("item_number_id")]."' and location='".$location."' and floor_id='".$floor."' and prod_reso_allo='".$row[csf("prod_reso_allo")]."' and sewing_line='".$row[csf("sewing_line")]."' and is_deleted=0 and status_active=1"; 
						//echo $mst_sql;die;
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
						$no_of_days=0; $total_smv_used=0; $total_adjust=0;
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
						
						$dzn_qnty=0; $cm_value=0;
						if($costing_per_arr[$row[csf('job_no')]]==1) $dzn_qnty=12;
						else if($costing_per_arr[$row[csf('job_no')]]==3) $dzn_qnty=12*2;
						else if($costing_per_arr[$row[csf('job_no')]]==4) $dzn_qnty=12*3;
						else if($costing_per_arr[$row[csf('job_no')]]==5) $dzn_qnty=12*4;
						else $dzn_qnty=1;
						
						$dzn_qnty=$dzn_qnty*$row[csf('ratio')];
						$cm_value=($tot_cost_arr[$row[csf('job_no')]]/$dzn_qnty)*$row[csf('qnty')];
						
						$actual_line_arr.=$row[csf('sewing_line')].",";
						$sewing_line='';
						if($row[csf('prod_reso_allo')]==1)
						{
							
							$line_number=explode(",",$prod_reso_arr[$row[csf('sewing_line')]]);
							foreach($line_number as $val)
							{
								if($sewing_line=='') $sewing_line=$lineArr[$val]; else $sewing_line.=",".$lineArr[$val];
							}
							
						}
						else 
						{ 
						 
						  $sewing_line=$lineArr[$row[csf('sewing_line')]];
						}
						
						$prod_lines.=$row[csf('sewing_line')].',';
						$res_id=$prod_resource_array2[$row[csf('id')]][$conv_pr_date]['id'];
					
						//echo $row[csf('sewing_line')];
						$po_number=implode(",",array_unique(explode(",",$po_number)));
						$html.="<tr bgcolor='$bgcolor' onclick=change_color('tr_$i','$bgcolor') id=tr_$i>";
						$html.='<td width="40">'.$i.'</td>
								<td width="50"><p>'.$sewing_line.'</p></td>
								<td width="70"><p>'.$buyerArr[$row[csf('buyer_name')]].'</p></td>
								<td width="110"><p>'.$po_number.'</p></td>
								<td width="110"><p>'.$row[csf('style_ref_no')].'</p></td>
								<td width="140"><p>'.$garments_item[$row[csf('item_number_id')]].'</p></td>
								<td width="75" align="right">'.number_format($cm_cost_arr[$row[csf('job_no')]],4).'</td>
								<td width="75" align="right">'.$po_quantity.'</td>
								<td width="70" align="right">'.$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['active_machine'].'</td>
								<td width="60" align="right">'.$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['man_power'].'</td>
								<td width="60" align="right">'.$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['operator'].'</td>
								<td width="60" align="right">'.$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['helper'].'</td>
								<td width="120"><p>'.$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['line_chief'].'</p></td>
								<td width="80" align="center">'.change_date_format($fstinput_date).'</td>
								<td width="75" align="right"><a href="##" onclick="openmypage('.$comapny_id.",'".$row[csf('po_id')]."',".$row[csf('item_number_id')].",".$location.",".$row[csf('floor_id')].",".$row[csf('sewing_line')].",".$txt_date.",'prev_qnty',4".",".$row[csf('prod_reso_allo')].')">'.$prev_input_qnty.'</a></td>
								<td width="75" align="right"><a href="##" onclick="openmypage('.$comapny_id.",'".$row[csf('po_id')]."',".$row[csf('item_number_id')].",".$location.",".$row[csf('floor_id')].",".$row[csf('sewing_line')].",".$txt_date.",'prev_qnty',5".",".$row[csf('prod_reso_allo')].')">'.$prev_out_qnty.'</a></td>
								<td width="75" align="right">'.$prev_wip.'</td>
								
								<td width="75" align="right">'.$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['tpd'].'</td>
								<td width="75" align="right">'.$today_input_qnty.'</td>
								
								<td width="75" align="right">'.$po_rate.'</td>
								<td width="75" align="right">'.number_format($po_rate*$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['tpd'],2,'.','').'</td>
								<td width="75" align="right">'.number_format($po_rate*$row[csf('qnty')],2,'.','').'</td>
								
								<td width="75" align="right"><a href="##" onclick="openmypage('.$comapny_id.",'".$row[csf('po_id')]."',".$row[csf('item_number_id')].",".$location.",".$floor.",".$row[csf('sewing_line')].",".$txt_date.",'today_prod',5".",".$row[csf('prod_reso_allo')].')">'.$row[csf('qnty')].'</a></td>
								<td width="75" align="right">'.number_format($today_ach_perc,2).'</td>
								<td width="75" align="right">'.$today_smv.'</p></td>
								<td width="70" align="right">'.$item_smv.'</td>
								<td width="100" align="right">'.$achv_smv.'</td>
								<td width="75" align="right">'.number_format($today_aff_perc,2).'</td>
								<td width="110" align="right">'.number_format($cm_value,2,'.','').'</td>
								<td width="90" align="right"><a href="##" onclick="openmypage('.$comapny_id.",'".$job_po_id."',".$row[csf('item_number_id')].",".$location.",".$floor.",".$row[csf('sewing_line')].",".$txt_date.",'tot_prod',5".",".$row[csf('prod_reso_allo')].')">'.$total_prod.'</a></td>
								<td width="80" align="right">'.number_format($avg_per_day,2,'.','').'</td>
								<td width="80" align="right">'.$wip.'</td>
								<td width="75" align="right"><a href="##" onclick="openmypage('.$comapny_id.",'".$row[csf('po_id')]."',".$row[csf('item_number_id')].",".$location.",".$floor.",".$row[csf('sewing_line')].",".$txt_date.",'tot_smv_used',"."'".$fstinput_date."'".",".$row[csf('prod_reso_allo')].')">'.number_format($total_smv_used,2,'.','').'</a></td>
								<td width="75" align="right">'.number_format($total_smv_achv,2,'.','').'</td>
								<td align="right" width="75">'.number_format($avg_aff_perc,2,'.','').'</td>';
									
						 $total_po_id=explode(",",$row[csf("po_id")]);
						 $total_po_id=implode("*",$total_po_id);
						 $line_number_id=$row[csf('sewing_line')];
						
						 $html.='<td><input type="button"  value="View"  class="formbutton" onclick="show_line_remarks('.$comapny_id.",'".$total_po_id."','".$floor."','".$line_number_id."','remarks_popup',".$txt_date.')"/></td>
							</tr>';
			   
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
						
						$tot_today_tar_fob_val+=($prod_resource_array[$row[csf('sewing_line')]][$pr_date]['tpd']*$po_rate); 
						$tot_today_fob_arc_qnty+=($row[csf('qnty')]*$po_rate); 
						
						$tot_today_out_qnty+=$row[csf('qnty')]; 
						$tot_today_smv+=$today_smv; 
						$tot_item_smv+=$item_smv; 
						$tot_achv_smv+=$achv_smv; 
						$tot_cm_value+=$cm_value; 
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
						
						$location_today_tar_fob_val+=($prod_resource_array[$row[csf('sewing_line')]][$pr_date]['tpd']*$po_rate); 
						$location_today_fob_arc_qnty+=($row[csf('qnty')]*$po_rate); 
						
						$location_today_out_qnty+=$row[csf('qnty')]; 
						$location_today_smv+=$today_smv; 
						$location_item_smv+=$item_smv; 
						$location_achv_smv+=$achv_smv; 
						$location_cm_value+=$cm_value; 
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
						
						$floor_today_tar_fob_val+=($prod_resource_array[$row[csf('sewing_line')]][$pr_date]['tpd']*$po_rate); 
						$floor_today_fob_arc_qnty+=($row[csf('qnty')]*$po_rate); 
						
						$floor_today_out_qnty+=$row[csf('qnty')]; 
						$floor_today_smv+=$today_smv; 
						$floor_item_smv+=$item_smv; 
						$floor_achv_smv+=$achv_smv; 
						$floor_cm_value+=$cm_value; 
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
						$buyer_data_array[$row[csf('buyer_name')]]['cm']+=$cm_value;
						
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
					$floorLineArr=array_unique(explode(",",chop($floor_line_arr[$location][$floor][$pr_date],',')));
					$prod_lines_arr=explode(",",chop($prod_lines,','));
					//echo $prod_lines;
					//print_r($floorLineArr);
					$noProdLinesArr=array_diff($floorLineArr, $prod_lines_arr);
					if($cbo_no_prod_type==155) //No Production line Start ....
					{/* 
					
					//print_r($floorLineArr);
					if(count($noProdLinesArr)>0)
					{
						foreach($noProdLinesArr as $line)
						{
							$sewing_line='';
							$line_number=$line_number=explode(",",$prod_reso_arr[$line]);
							foreach($line_number as $val)
							{
								if($sewing_line=='') $sewing_line=$lineArr[$val]; else $sewing_line.=",".$lineArr[$val];
							}
							$total_adjustment=0;
						$smv_adjustmet_type=$prod_resource_array[$line][$pr_date]['smv_adjust_type'];
						if(str_replace("'","",$smv_adjustmet_type)==1) $total_adjustment=$prod_resource_array[$line][$pr_date]['smv_adjust'];
						if(str_replace("'","",$smv_adjustmet_type)==2) $total_adjustment=($prod_resource_array[$line][$pr_date]['smv_adjust'])*(-1);
						
						
							$today_smv=$prod_resource_array[$line][$pr_date]['tsmv']+$total_adjustment;
						    $achv_smv=$row[csf('qnty')]*$item_smv; 
							$today_ach_perc=$row[csf('qnty')]/$prod_resource_array[$line][$pr_date]['tpd']*100;
							$today_aff_perc=$achv_smv/$today_smv*100;
							$total_prod=$row[csf('qnty')]+$prev_out_qnty;	
							$wip=$prev_input_qnty+$today_input_qnty-$total_prod;
							
						$no_of_days=0; $total_smv_used=0; $total_adjust=0;
						foreach($dataLibArray as $libRow)
						{
							$date_calc=change_date_format($libRow[csf('date_calc')]);
							$smv_adjustmet_type=$prod_resource_array[$line][$date_calc]['smv_adjust_type'];
							if(str_replace("'","",$smv_adjustmet_type)==1) $total_adjust=$prod_resource_array[$line][$date_calc]['smv_adjust'];
							else if(str_replace("'","",$smv_adjustmet_type)==2) $total_adjust=($prod_resource_array[$line][$date_calc]['smv_adjust'])*(-1);
							else $total_adjust=0;
							$total_smv_used+=$prod_resource_array[$line][$date_calc]['tsmv']+$total_adjust;
							$no_of_days++;
						}
						//$total_smv_used=$today_smv*$no_of_days;
						$avg_per_day=$total_prod/$no_of_days;
						$total_smv_achv=$item_smv*$total_prod;
						$avg_aff_perc=$total_smv_achv/$total_smv_used*100;
						
						$dzn_qnty=0; $cm_value=0;
						if($costing_per_arr[$row[csf('job_no')]]==1) $dzn_qnty=12;
						else if($costing_per_arr[$row[csf('job_no')]]==3) $dzn_qnty=12*2;
						else if($costing_per_arr[$row[csf('job_no')]]==4) $dzn_qnty=12*3;
						else if($costing_per_arr[$row[csf('job_no')]]==5) $dzn_qnty=12*4;
						else $dzn_qnty=1;
						
						$dzn_qnty=$dzn_qnty*$row[csf('ratio')];
						$cm_value=($tot_cost_arr[$row[csf('job_no')]]/$dzn_qnty)*$row[csf('qnty')];
							
							$floor_machine_qnty+=$prod_resource_array[$line][$pr_date]['active_machine'];
							$floor_worker+=$prod_resource_array[$line][$pr_date]['man_power'];
							$floor_operator+=$prod_resource_array[$line][$pr_date]['operator']; 
							$floor_helper+=$prod_resource_array[$line][$pr_date]['helper']; 
							$floor_today_fob_arc_qnty+=($row[csf('qnty')]*$po_rate); 
							$floor_today_out_qnty+=$row[csf('qnty')]; 
							//$floor_today_out_qnty+=$row[csf('qnty')]; 
							$floor_today_smv+=$today_smv; 
							$floor_item_smv+=$item_smv; 
							$floor_achv_smv+=$achv_smv; 
							$floor_cm_value+=$cm_value; 
							$floor_tot_prod+=$total_prod; 
							$floor_wip+=$wip; 
							$floor_tot_smv_used+=$total_smv_used; 
							$floor_tot_achv_smv+=$total_smv_achv;
							
							$location_operator+=$prod_resource_array[$line][$pr_date]['operator']; 
							$location_helper+=$prod_resource_array[$line][$pr_date]['helper']; 
							$location_worker+=$prod_resource_array[$line][$pr_date]['man_power']; 
							$location_today_target+=$prod_resource_array[$line][$pr_date]['tpd']; 
						    $floor_today_target+=$prod_resource_array[$line][$pr_date]['tpd'];
							$location_today_input_qnty+=$today_input_qnty; 
							$location_today_tar_fob_val+=($prod_resource_array[$line][$pr_date]['tpd']*$po_rate); 
						    $location_today_fob_arc_qnty+=($row[csf('qnty')]*$po_rate); 
							$location_today_smv+=$today_smv; 
							$location_item_smv+=$item_smv; 
							$location_achv_smv+=$achv_smv; 
							$location_cm_value+=$cm_value; 
							$location_tot_prod+=$total_prod; 
							$location_wip+=$wip; 
							$location_tot_smv_used+=$total_smv_used; 
							$location_tot_achv_smv+=$total_smv_achv;
							//$location_today_fob_arc_qnty+=($row[csf('qnty')]*$po_rate); 
						   
							$location_today_tar_fob_val+=($prod_resource_array[$line][$pr_date]['tpd']*$po_rate); 
							//$location_today_fob_arc_qnty+=($row[csf('qnty')]*$po_rate); 
							$location_today_out_qnty+=$row[csf('qnty')]; 

							if($duplicate_array[$row[csf('prod_reso_allo')]][$line]=="")
							{
								$total_actual_machine_qnty+=$prod_resource_array[$line][$pr_date]['active_machine']; 
								$tot_actual_worker+=$prod_resource_array[$line][$pr_date]['man_power']; 
								$tot_actual_operator+=$prod_resource_array[$line][$pr_date]['operator']; 
								$tot_actual_helper+=$prod_resource_array[$line][$pr_date]['helper'];
								$tot_actual_today_target+=$prod_resource_array[$line][$pr_date]['tpd']; 
								$tot_actual_today_smv+=$today_smv; 
								$grand_tot_actual_smv_used+=$total_smv_used; 
								
								$duplicate_array[$row[csf('prod_reso_allo')]][$line]=$line;
							}
						
								$tot_today_target+=$prod_resource_array[$line][$pr_date]['tpd'];
								$tot_today_out_qnty+=$row[csf('qnty')]; 
								$floor_today_tar_fob_val+=($prod_resource_array[$line][$pr_date]['tpd']*$po_rate); 
								$tot_today_tar_fob_val+=($prod_resource_array[$line][$pr_date]['tpd']*$po_rate); 	
								$tot_today_fob_arc_qnty+=($row[csf('qnty')]*$po_rate); 
								
								$tot_today_smv+=$today_smv; 
								$tot_item_smv+=$item_smv; 
								$tot_achv_smv+=$achv_smv; 
								$tot_cm_value+=$cm_value; 
								$grand_tot_prod+=$total_prod; 
								$tot_wip+=$wip; 
								$grand_tot_smv_used+=$total_smv_used; 
								$grand_tot_achv_smv+=$total_smv_achv;
							
							$html.="<tr bgcolor='$bgcolor' onclick=change_color('tr_$i','$bgcolor') id=tr_$i>";
							$html.='<td width="40">'.$i.'</td>
									<td width="50"><p>'.$sewing_line.'&nbsp;</p></td>
									<td width="70"><p>&nbsp;</p></td>
									<td width="110"><p>&nbsp;</p></td>
									<td width="110"><p>&nbsp;</p></td>
									<td width="140"><p>&nbsp;</p></td>
									<td width="75" align="right">&nbsp;</td>
									<td width="75" align="right">&nbsp;</td>
									<td width="70" align="right">'.$prod_resource_array[$line][$pr_date]['active_machine'].'&nbsp;</td>
									<td width="60" align="right">'.$prod_resource_array[$line][$pr_date]['man_power'].'&nbsp;</td>
									<td width="60" align="right">'.$prod_resource_array[$line][$pr_date]['operator'].'&nbsp;</td>
									<td width="60" align="right">'.$prod_resource_array[$line][$pr_date]['helper'].'&nbsp;</td>
									<td width="120"><p>'.$prod_resource_array[$line][$pr_date]['line_chief'].'&nbsp;</p></td>
									<td width="80" align="center">'.change_date_format($fstinput_date).'&nbsp;</td>
									<td width="75" align="right"><a href="##" onclick="openmypage('.$comapny_id.",'".$row[csf('po_id')]."',".$row[csf('item_number_id')].",".$location.",".$row[csf('floor_id')].",".$line.",".$txt_date.",'prev_qnty',4".",".$row[csf('prod_reso_allo')].')">'.$prev_input_qnty.'</a>&nbsp;</td>
									<td width="75" align="right"><a href="##" onclick="openmypage('.$comapny_id.",'".$row[csf('po_id')]."',".$row[csf('item_number_id')].",".$location.",".$row[csf('floor_id')].",".$line.",".$txt_date.",'prev_qnty',5".",".$row[csf('prod_reso_allo')].')">'.$prev_out_qnty.'</a>&nbsp;</td>
									<td width="75" align="right">'.$prev_wip.'&nbsp;</td>
									
									<td width="75" align="right">'.$prod_resource_array[$line][$pr_date]['tpd'].'&nbsp;</td>
									<td width="75" align="right">'.$today_input_qnty.'&nbsp;</td>
									
									<td width="75" align="right">'.$po_rate.'</td>
									<td width="75" align="right">'.number_format($po_rate*$prod_resource_array[$line][$pr_date]['tpd'],2,'.','').'</td>
									<td width="75" align="right">'.number_format($po_rate*$row[csf('qnty')],2,'.','').'</td>
									
									<td width="75" align="right"><a href="##" onclick="openmypage('.$comapny_id.",'".$row[csf('po_id')]."',".$row[csf('item_number_id')].",".$location.",".$floor.",".$line.",".$txt_date.",'today_prod',5".",".$row[csf('prod_reso_allo')].')">'.$row[csf('qnty')].'</a>&nbsp;</td>
									<td width="75" align="right">'.number_format($today_ach_perc,2).'&nbsp;</td>
									<td width="75" align="right">'.$today_smv.'&nbsp;</p></td>
									<td width="70" align="right">'.$item_smv.'&nbsp;</td>
									<td width="100" align="right">'.$achv_smv.'&nbsp;</td>
									<td width="75" align="right">'.number_format($today_aff_perc,2).'&nbsp;</td>
									<td width="110" align="right">'.number_format($cm_value,2,'.','').'&nbsp;</td>
									<td width="90" align="right"><a href="##" onclick="openmypage('.$comapny_id.",'".$job_po_id."',".$row[csf('item_number_id')].",".$location.",".$floor.",".$line.",".$txt_date.",'tot_prod',5".",".$row[csf('prod_reso_allo')].')">'.$total_prod.'</a>&nbsp;</td>
									<td width="80" align="right">'.number_format($avg_per_day,2,'.','').'&nbsp;</td>
									<td width="80" align="right">'.$wip.'&nbsp;</td>
									<td width="75" align="right"><a href="##" onclick="openmypage('.$comapny_id.",'".$row[csf('po_id')]."',".$row[csf('item_number_id')].",".$location.",".$floor.",".$line.",".$txt_date.",'tot_smv_used',"."'".$fstinput_date."'".",".$row[csf('prod_reso_allo')].')">'.number_format($total_smv_used,2,'.','').'</a>&nbsp;</td>
									<td width="75" align="right">'.number_format($total_smv_achv,2,'.','').'&nbsp;</td>
									<td align="right" width="75">'.number_format($avg_aff_perc,2,'.','').'&nbsp;</td>';
										
							 $line_number_id=$line;
							
							 $html.='<td><input type="button" value="View" class="formbutton" onclick="show_line_remarks('.$comapny_id.",'".$total_po_id."','".$floor."','".$line_number_id."','remarks_popup',".$txt_date.')"/></td>
								</tr>';
							$i++;	
						}
					}
					*/}//No Prod. End
				}
				
			
				$floor_today_ach_perc=$floor_today_out_qnty/$floor_today_target*100;
				$floor_today_aff_perc=$floor_achv_smv/$floor_today_smv*100;
				$floor_avg_aff_perc=$floor_tot_achv_smv/$floor_tot_smv_used*100;
				
				$html.='<tr bgcolor="#CCCCCC">
						<td colspan="6" align="right">Floor Total</td>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
						<td align="right">'.$floor_machine_qnty.'</td>
						<td align="right">'.$floor_worker.'</td>
						<td align="right">'.$floor_operator.'</td>
						<td align="right">'.$floor_helper.'</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td align="right">'.$floor_prev_input_qnty.'</td>
						<td align="right">'.$floor_prev_out_qnty.'</td>
						<td align="right">'.$floor_prev_wip.'</td>
						<td align="right">'.$floor_today_target.'</td>
						<td align="right">'.$floor_today_input_qnty.'</td>

						<td width="75" align="right">&nbsp;</td>
						<td width="75" align="right">'.number_format($floor_today_tar_fob_val,2,'.','').'</td>
						<td width="75" align="right">'.number_format($floor_today_fob_arc_qnty,2,'.','').'</td>
						
						<td align="right">'.$floor_today_out_qnty.'</td>
						<td align="right">'.number_format($floor_today_aff_perc,2,'.','').'</td>
						<td align="right">'.$floor_today_smv.'</td>
						<td align="right">&nbsp;</td>
						<td align="right">'.$floor_achv_smv.'</td>
						<td align="right">'.number_format($floor_today_aff_perc,2,'.','').'</td>
						<td align="right">'.number_format($floor_cm_value,2,'.','').'</td>
						<td align="right">'.$floor_tot_prod.'</td>
						<td>&nbsp;</td>
						<td align="right">'.$floor_wip.'</td>
						<td align="right">'.number_format($floor_tot_smv_used,2,'.','').'</td>
						<td align="right">'.number_format($floor_tot_achv_smv,2,'.','').'</td>
						<td align="right">'.number_format($floor_avg_aff_perc,2,'.','').'</td>
						<td>&nbsp;</td>
					</tr>';
			}//'.$floor_item_smv.'
			$floorLineArr_no=array_unique(explode(",",chop($location_floor_line_arr[$location][$pr_date],',')));
			$prod_location_arr=explode(",",chop($prod_location,','));
			$nolineProdFloorsArr=array_diff($floorLineArr_no, $prod_location_arr);
			//foreach($nolineProdFloorsArr as $floor)
			//{}
			
			$location_today_ach_perc=$location_today_out_qnty/$location_today_target*100;
			$location_today_aff_perc=$location_achv_smv/$location_today_smv*100;
			$location_avg_aff_perc=$location_tot_achv_smv/$location_tot_smv_used*100;
			$html.='<tr bgcolor="#E9F3FF">
						<td colspan="6" align="right">Location Total</td>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
						<td align="right">'.$location_machine_qnty.'</td>
						<td align="right">'.$location_worker.'</td>
						<td align="right">'.$location_operator.'</td>
						<td align="right">'.$location_helper.'</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td align="right">'.$location_prev_input_qnty.'</td>
						<td align="right">'.$location_prev_out_qnty.'</td>
						<td align="right">'.$location_prev_wip.'</td>
						<td align="right">'.$location_today_target.'</td>
						<td align="right">'.$location_today_input_qnty.'</td>
						
						<td width="75" align="right">&nbsp;</td>
						<td width="75" align="right">'.number_format($location_today_tar_fob_val,2,'.','').'</td>
						<td width="75" align="right">'.number_format($location_today_fob_arc_qnty,2,'.','').'</td>
						
						<td align="right">'.$location_today_out_qnty.'</td>
						<td align="right">'.number_format($location_today_ach_perc,2,'.','').'</td>
						<td align="right">'.$location_today_smv.'</td>
						<td align="right">&nbsp;</td>
						<td align="right">'.$location_achv_smv.'</td>
						<td align="right">'.number_format($location_today_aff_perc,2,'.','').'</td>
						<td align="right">'.number_format($location_cm_value,2,'.','').'</td>
						<td align="right">'.$location_tot_prod.'</td>
						<td>&nbsp;</td>
						<td align="right">'.$location_wip.'</td>
						<td align="right">'.number_format($location_tot_smv_used,2,'.','').'</td>
						<td align="right">'.number_format($location_tot_achv_smv,2,'.','').'</td>
						<td align="right">'.number_format($location_avg_aff_perc,2,'.','').'</td>
						<td>&nbsp;</td>
					</tr>';
						
		}//'.$location_item_smv.'
		//$location_floor_line_arr
		//echo $prod_location;
		//$prod_lines=explode(",",chop($prod_lines,','));
		$prod_lines=rtrim($prod_lines,",");
		//print_r($prod_lines);
		
	if($cbo_no_prod_type==1) //No Production line Start ....
	{
			if(str_replace("'","",$cbo_location_id)==0) $location_cond="%%"; else $location_cond=str_replace("'","",$cbo_location_id);
			if(str_replace("'","",$cbo_floor_id)==0) $floor_cond="%%"; else $floor_cond=str_replace("'","",$cbo_floor_id);
    		if(str_replace("'","",$cbo_line)==0) $line_cond="%%"; else $line_cond=str_replace("'","",$cbo_line);
	
			 $sql_data="select a.company_id,a.id, a.floor_id,a.location_id as location,1 as prod_reso_allo,2 as type_line, a.line_number as sewing_line from  prod_resource_dtls b,prod_resource_mst a  where a.id=b.mst_id and a.company_id=$comapny_id and b.pr_date=".$txt_date."  and a.is_deleted=0 and b.is_deleted=0 and a.floor_id like '$floor_cond' and a.location_id like '$location_cond' and a.id not in($prod_lines)  group by a.company_id,a.id, a.floor_id,a.location_id, a.line_number";
			  $dataArray_row=sql_select($sql_data);
			 $noline_line_data_array=array();
			 foreach($dataArray_row as $row)
			 { 
			 	$sewing_line_id='';
				//$line_cond=explode(",",$row[csf('sewing_line')]);
				//foreach($line_cond as $lin_id)
				//{
					//$lin_ids=$lin_id;
					if($row[csf('prod_reso_allo')]==1)
					{
						$sewing_line_id=$prod_reso_arr[$row[csf('sewing_line')]];
					}
					else
					{
						$sewing_line_id=$row[csf('sewing_line')];
					}
				//}
				
				
				if($lineSerialArr[$sewing_line_id]=="")
				{
					$lastSlNo++;
					$slNo=$lastSlNo;
					$lineSerialArr[$sewing_line_id];
				}
				else $slNo=$lineSerialArr[$sewing_line_id];
				$row[csf('job_no')]=0;
				$row[csf('company_name')]=$row[csf('company_id')];
				$row[csf('buyer_name')]=0;	
				$row[csf('style_ref_no')]=0;	
				$row[csf('order_uom')]=0;	
				$row[csf('gmts_item_id')]=0;
				$row[csf('set_break_down')]=0;
				$row[csf('ratio')]=0;
				$row[csf('po_id')]=0;
				$row[csf('item_number_id')]=0;
				$row[csf('prod_reso_allo')]=$row[csf('prod_reso_allo')];
				$row[csf('sewing_line')]=$row[csf('sewing_line')];
				$row[csf('type_line')]=$row[csf('type_line')];
				$res_id=$row[csf('id')];
				
				
				$row[csf('qnty')]=0;
				$po_data=0;
							//echo $slNo."**".$sewing_line_id.'AA'."<br>";	
				$noline_line_data_array[$row[csf('location')]][$row[csf('floor_id')]][$slNo].=$row[csf('job_no')]."##".$row[csf('company_name')]."##".$row[csf('buyer_name')]."##".$row[csf('style_ref_no')]."##".$row[csf('order_uom')]."##".$row[csf('gmts_item_id')]."##".$row[csf('set_break_down')]."##".$row[csf('ratio')]."##".$row[csf('po_id')]."##".$row[csf('item_number_id')]."##".$row[csf('prod_reso_allo')]."##".$row[csf('sewing_line')]."##".$row[csf('qnty')]."##".$res_id."##".$po_data.":";
			 }
			 	unset($dataArray_row);
			 //print_r($noline_line_data_array).'bb';
			
			
	//unset($dataArray_row);
	} //End
	foreach($noline_line_data_array as $location=>$locData )
		{
			//echo 'ddd';
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
			$location_today_tar_fob_val=0;
			$location_today_fob_arc_qnty=0; 
			$location_today_out_qnty=0; 
			$location_today_smv=0; 
			$location_item_smv=0; 
			$location_achv_smv=0; 
			$location_cm_value=0; 
			$location_tot_prod=0; 
			$location_wip=0; 
			$location_tot_smv_used=0; 
			$location_tot_achv_smv=0;
			
			foreach($locData as $floor=>$floorData )
			{
				$html.='<tr bgcolor="#EFEFEF"><td colspan="36"><b>Floor name: '.$floorArr[$floor].'; Location name: '.$locationArr[$location].'</b></td></tr>';
			
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
				
				$floor_today_tar_fob_val=0;
				$floor_today_fob_arc_qnty=0; 

				$floor_today_out_qnty=0; 
				$floor_today_smv=0; 
				$floor_item_smv=0; 
				$floor_achv_smv=0; 
				$floor_cm_value=0; 
				$floor_tot_prod=0; 
				$floor_wip=0; 
				$floor_tot_smv_used=0; 
				$floor_tot_achv_smv=0;
				$prod_lines='';
				
				ksort($floorData);
				foreach($floorData as $slNo=>$lineData )
				{
					//print_r($lineData);
					$lineSlData=explode(":",chop($lineData,':'));
					foreach($lineSlData as $value )
					{
						$lineDataArr=explode("##",$value);
						$row[csf('job_no')]=$lineDataArr[0];
						$row[csf('company_name')]=$lineDataArr[1];
						$row[csf('buyer_name')]=$lineDataArr[2];
						$row[csf('style_ref_no')]=$lineDataArr[3];
						$row[csf('order_uom')]=$lineDataArr[4];
						$row[csf('gmts_item_id')]=$lineDataArr[5];
						$row[csf('set_break_down')]=$lineDataArr[6];
						$row[csf('ratio')]=$lineDataArr[7];
						$row[csf('po_id')]=$lineDataArr[8];
						$row[csf('item_number_id')]=$lineDataArr[9];
						$row[csf('prod_reso_allo')]=$lineDataArr[10];
					    $row[csf('sewing_line')]=$lineDataArr[11];
						$row[csf('qnty')]=$lineDataArr[12];
						$res_id=$lineDataArr[13];
						$row[csf('po_data')]=$lineDataArr[14];
						//echo $row[csf('sewing_line')].'<br>';
						//echo $line_type.'aa';
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				
						$po_number=''; $po_quantity=0;  $po_array=array(); $item_po_amnt=0; $item_po_quantity=0;
						 //$item_smv=0;
						$exp_grmts_item = explode("__",$row[csf("set_break_down")]);
						foreach($exp_grmts_item as $value)
						{
							$grmts_item_qty = explode("_",$value);
							if($row[csf('item_number_id')]==$grmts_item_qty[0])
							{
								//$set_qty=$grmts_item_qty[1];
								//$item_smv=$grmts_item_qty[2];
								break;
							}
						}
						
						$po_data = explode(",",$row[csf("po_data")]);
						foreach($po_data as $val)
						{
							$po_val=explode("**",$val);
							$po_array[$po_val[0]]['no']=$po_val[1];
							$po_array[$po_val[0]]['qnty']=$po_val[2];
							$po_array[$po_val[0]]['rate']=$po_val[3];
						}
						
						$po_ids = array_unique(explode(",",$row[csf("po_id")]));
						foreach($po_ids as $id)
						{
							if($po_number=="") $po_number=$po_array[$id]['no']; else $po_number.=",".$po_array[$id]['no'];
							$po_quantity+=$po_array[$id]['qnty']*$set_qty;
							$item_po_amnt+=$po_rate_arr[$id][$row[csf('item_number_id')]][1];
							$item_po_quantity+=$po_rate_arr[$id][$row[csf('item_number_id')]][2];
							//$po_rate=$po_array[$id]['rate'];
						}
						
						//$po_rate=number_format($item_po_amnt/$item_po_quantity,2,'.','');
						//$job_po_id=$job_po_id_arr[$row[csf("job_no")]]; 
						$job_po_id=$row[csf("po_id")];
						if($job_po_id!="") $job_po_id=$job_po_id;else $job_po_id=0;
						$mst_sql= "SELECT  
										min(CASE WHEN production_type ='4' THEN production_date END) AS frstinput_date,
										sum(CASE WHEN production_type ='4' and production_date=$txt_date and po_break_down_id in(".$job_po_id.") THEN production_quantity ELSE 0 END) AS today_input_qnty,
										sum(CASE WHEN production_type ='4' and production_date<$txt_date THEN production_quantity ELSE 0 END) AS prev_input_qnty,
										sum(CASE WHEN production_type ='5' and production_date<$txt_date THEN production_quantity ELSE 0 END) AS prev_out_qnty
									from 
										pro_garments_production_mst 
									where  
										po_break_down_id in(".$job_po_id.") and item_number_id='".$row[csf("item_number_id")]."' and location='".$location."' and floor_id='".$floor."' and prod_reso_allo='".$row[csf("prod_reso_allo")]."' and sewing_line='".$row[csf("sewing_line")]."' and is_deleted=0 and status_active=1"; 
						//echo $mst_sql;die;
						$dataArray = sql_select($mst_sql);
						$fstinput_date=$dataArray[0][csf('frstinput_date')]; 
						$prev_input_qnty=$dataArray[0][csf('prev_input_qnty')];  
						$prev_out_qnty=$dataArray[0][csf('prev_out_qnty')]; 
						$prev_wip=$prev_input_qnty-$prev_out_qnty; 
						$today_input_qnty=$dataArray[0][csf('today_input_qnty')]; 
						$today_ach_perc=$row[csf('qnty')]/$prod_resource_array2[$row[csf('sewing_line')]][$pr_date]['tpd']*100;
						
						$total_adjustment=0;
						$smv_adjustmet_type=$prod_resource_array2[$row[csf('sewing_line')]][$pr_date]['smv_adjust_type'];
						if(str_replace("'","",$smv_adjustmet_type)==1) $total_adjustment=$prod_resource_array2[$row[csf('sewing_line')]][$pr_date]['smv_adjust'];
						if(str_replace("'","",$smv_adjustmet_type)==2) $total_adjustment=($prod_resource_array2[$row[csf('sewing_line')]][$pr_date]['smv_adjust'])*(-1);
						
						$today_smv=$prod_resource_array2[$row[csf('sewing_line')]][$pr_date]['tsmv']+$total_adjustment;
						$achv_smv=$row[csf('qnty')]*$item_smv; 
					//	echo $row[csf('qnty')].'*'.$item_smv.'<br>'; 
						$today_aff_perc=$achv_smv/$today_smv*100;
						$total_prod=$row[csf('qnty')]+$prev_out_qnty;	
						$wip=$prev_input_qnty+$today_input_qnty-$total_prod;
						
					   // $no_of_days=return_field_value("count(id)","lib_capacity_calc_dtls","date_calc between '$fstinput_date' and $txt_date and day_status=1");
						$dataLibArray=sql_select("select b.date_calc from lib_capacity_calc_mst a, lib_capacity_calc_dtls b where a.id=b.mst_id and a.comapny_id='$comapny_id' and b.date_calc between '$fstinput_date' and $txt_date and day_status=1");
						$no_of_days=0; $total_smv_used=0; $total_adjust=0;
						foreach($dataLibArray as $libRow)
						{
							$date_calc=change_date_format($libRow[csf('date_calc')]);
							$smv_adjustmet_type=$prod_resource_array2[$row[csf('sewing_line')]][$date_calc]['smv_adjust_type'];
							if(str_replace("'","",$smv_adjustmet_type)==1) $total_adjust=$prod_resource_array2[$row[csf('sewing_line')]][$date_calc]['smv_adjust'];
							else if(str_replace("'","",$smv_adjustmet_type)==2) $total_adjust=($prod_resource_array2[$row[csf('sewing_line')]][$date_calc]['smv_adjust'])*(-1);
							else $total_adjust=0;
							$total_smv_used+=$prod_resource_array2[$row[csf('sewing_line')]][$date_calc]['tsmv']+$total_adjust;
							$no_of_days++;
						}
						//$total_smv_used=$today_smv*$no_of_days;
						$avg_per_day=$total_prod/$no_of_days;
						$total_smv_achv=$item_smv*$total_prod;
						$avg_aff_perc=$total_smv_achv/$total_smv_used*100;
						
						$dzn_qnty=0; $cm_value=0;
						if($costing_per_arr[$row[csf('job_no')]]==1) $dzn_qnty=12;
						else if($costing_per_arr[$row[csf('job_no')]]==3) $dzn_qnty=12*2;
						else if($costing_per_arr[$row[csf('job_no')]]==4) $dzn_qnty=12*3;
						else if($costing_per_arr[$row[csf('job_no')]]==5) $dzn_qnty=12*4;
						else $dzn_qnty=1;
						
						$dzn_qnty=$dzn_qnty*$row[csf('ratio')];
						$cm_value=($tot_cost_arr[$row[csf('job_no')]]/$dzn_qnty)*$row[csf('qnty')];
						
						//$actual_line_arr.=$row[csf('sewing_line')].",";
						$sewing_line='';
						if($row[csf('prod_reso_allo')]==1)
						{
							
							$line_number=explode(",",$row[csf('sewing_line')]);
							//$line_number=explode(",",$prod_reso_arr[$row[csf('sewing_line')]]);
							foreach($line_number as $val)
							{
								if($sewing_line=='') $sewing_line=$lineArr[$val]; else $sewing_line.=",".$lineArr[$val];
							}
							
						}
						else 
						{ 
						 
						  $sewing_line=$lineArr[$row[csf('sewing_line')]];
						}
						
						$prod_lines.=$row[csf('sewing_line')].',';
					
						//echo $row[csf('sewing_line')].'<br>';
					 // $man_power=$prod_resource_array2[$res_id][$conv_pr_date]['man_power'];
						$po_number=implode(",",array_unique(explode(",",$po_number)));
						$html.="<tr bgcolor='$bgcolor' onclick=change_color('tr_$i','$bgcolor') id=tr_$i>";
						$html.='<td width="40">'.$i.'</td>
								<td width="50"><p>'.$sewing_line.'</p></td>
								<td width="70"><p>'.$buyerArr[$row[csf('buyer_name')]].'</p></td>
								<td width="110"><p>'.$po_number.'</p></td>
								<td width="110"><p>'.$row[csf('style_ref_no')].'</p></td>
								<td width="140"><p>'.$garments_item[$row[csf('item_number_id')]].'</p></td>
								<td width="75" align="right">'.number_format($cm_cost_arr[$row[csf('job_no')]],4).'</td>
								<td width="75" align="right">'.$po_quantity.'</td>
								<td width="70" align="right">'.$prod_resource_array[$row[csf('sewing_line')]][$pr_date]['active_machine'].'</td>
								<td width="60" align="right">'.$prod_resource_array2[$row[csf('sewing_line')]][$pr_date]['man_power'].'</td>
								<td width="60" align="right">'.$prod_resource_array2[$row[csf('sewing_line')]][$pr_date]['operator'].'</td>
								<td width="60" align="right">'.$prod_resource_array2[$row[csf('sewing_line')]][$pr_date]['helper'].'</td>
								<td width="120"><p>'.$prod_resource_array2[$row[csf('sewing_line')]][$pr_date]['line_chief'].'</p></td>
								<td width="80" align="center">'.change_date_format($fstinput_date).'</td>
								<td width="75" align="right"><a href="##" onclick="openmypage('.$comapny_id.",'".$row[csf('po_id')]."',".$row[csf('item_number_id')].",".$location.",".$row[csf('floor_id')].",".$row[csf('sewing_line')].",".$txt_date.",'prev_qnty',4".",".$row[csf('prod_reso_allo')].')">'.$prev_input_qnty.'</a></td>
								<td width="75" align="right"><a href="##" onclick="openmypage('.$comapny_id.",'".$row[csf('po_id')]."',".$row[csf('item_number_id')].",".$location.",".$row[csf('floor_id')].",".$row[csf('sewing_line')].",".$txt_date.",'prev_qnty',5".",".$row[csf('prod_reso_allo')].')">'.$prev_out_qnty.'</a></td>
								<td width="75" align="right">'.$prev_wip.'</td>
								
								<td width="75" align="right">'.$prod_resource_array2[$row[csf('sewing_line')]][$pr_date]['tpd'].'</td>
								<td width="75" align="right">'.$today_input_qnty.'</td>
								
								<td width="75" align="right">'.$po_rate.'</td>
								<td width="75" align="right">'.number_format($po_rate*$prod_resource_array2[$row[csf('sewing_line')]][$pr_date]['tpd'],2,'.','').'</td>
								<td width="75" align="right">'.number_format($po_rate*$row[csf('qnty')],2,'.','').'</td>
								
								<td width="75" align="right"><a href="##" onclick="openmypage('.$comapny_id.",'".$row[csf('po_id')]."',".$row[csf('item_number_id')].",".$location.",".$floor.",".$row[csf('sewing_line')].",".$txt_date.",'today_prod',5".",".$row[csf('prod_reso_allo')].')">'.$row[csf('qnty')].'</a></td>
								<td width="75" align="right">'.number_format($today_ach_perc,2).'</td>
								<td width="75" align="right">'.$today_smv.'</p></td>
								<td width="70" align="right">'.$item_smv.'</td>
								<td width="100" align="right">'.$achv_smv.'</td>
								<td width="75" align="right">'.number_format($today_aff_perc,2).'</td>
								<td width="110" align="right">'.number_format($cm_value,2,'.','').'</td>
								<td width="90" align="right"><a href="##" onclick="openmypage('.$comapny_id.",'".$job_po_id."',".$row[csf('item_number_id')].",".$location.",".$floor.",".$row[csf('sewing_line')].",".$txt_date.",'tot_prod',5".",".$row[csf('prod_reso_allo')].')">'.$total_prod.'</a></td>
								<td width="80" align="right">'.number_format($avg_per_day,2,'.','').'</td>
								<td width="80" align="right">'.$wip.'</td>
								<td width="75" align="right"><a href="##" onclick="openmypage('.$comapny_id.",'".$row[csf('po_id')]."',".$row[csf('item_number_id')].",".$location.",".$floor.",".$row[csf('sewing_line')].",".$txt_date.",'tot_smv_used',"."'".$fstinput_date."'".",".$row[csf('prod_reso_allo')].')">'.number_format($total_smv_used,2,'.','').'</a></td>
								<td width="75" align="right">'.number_format($total_smv_achv,2,'.','').'</td>
								<td align="right" width="75">'.number_format($avg_aff_perc,2,'.','').'</td>';
									
						 $total_po_id=explode(",",$row[csf("po_id")]);
						 $total_po_id=implode("*",$total_po_id);
						 $line_number_id=$row[csf('sewing_line')];
						
						 $html.='<td><input type="button"  value="View"  class="formbutton" onclick="show_line_remarks('.$comapny_id.",'".$total_po_id."','".$floor."','".$line_number_id."','remarks_popup',".$txt_date.')"/></td>
							</tr>';
			   
						$i++;
						
						$total_order_qnty+=$po_quantity; 
						$total_machine_qnty+=$prod_resource_array2[$row[csf('sewing_line')]][$pr_date]['active_machine']; 
						$tot_worker+=$prod_resource_array2[$row[csf('sewing_line')]][$pr_date]['man_power']; 
						$tot_operator+=$prod_resource_array2[$row[csf('sewing_line')]][$pr_date]['operator']; 
						$tot_helper+=$prod_resource_array2[$row[csf('sewing_line')]][$pr_date]['helper']; 
						$tot_prev_input_qnty+=$prev_input_qnty; 
						$tot_prev_out_qnty+=$prev_out_qnty;
						$tot_prev_wip+=$prev_wip;
						$tot_today_target+=$prod_resource_array2[$row[csf('sewing_line')]][$pr_date]['tpd']; 
						$tot_today_input_qnty+=$today_input_qnty; 
						
						$tot_today_tar_fob_val+=($prod_resource_array2[$row[csf('sewing_line')]][$pr_date]['tpd']*$po_rate); 
						$tot_today_fob_arc_qnty+=($row[csf('qnty')]*$po_rate); 
						
						$tot_today_out_qnty+=$row[csf('qnty')]; 
						$tot_today_smv+=$today_smv; 
						$tot_item_smv+=$item_smv; 
						$tot_achv_smv+=$achv_smv; 
						$tot_cm_value+=$cm_value; 
						$grand_tot_prod+=$total_prod; 
						$tot_wip+=$wip; 
						$grand_tot_smv_used+=$total_smv_used; 
						$grand_tot_achv_smv+=$total_smv_achv;
						
						$location_order_qnty+=$po_quantity; 
						$location_machine_qnty+=$prod_resource_array2[$row[csf('sewing_line')]][$pr_date]['active_machine']; 
						$location_worker+=$prod_resource_array2[$row[csf('sewing_line')]][$pr_date]['man_power']; 
						$location_operator+=$prod_resource_array2[$row[csf('sewing_line')]][$pr_date]['operator']; 
						$location_helper+=$prod_resource_array2[$row[csf('sewing_line')]][$pr_date]['helper']; 
						$location_prev_input_qnty+=$prev_input_qnty; 
						$location_prev_out_qnty+=$prev_out_qnty;
						$location_prev_wip+=$prev_wip;
						$location_today_target+=$prod_resource_array2[$row[csf('sewing_line')]][$pr_date]['tpd']; 
						$location_today_input_qnty+=$today_input_qnty; 
						
						$location_today_tar_fob_val+=($prod_resource_array2[$row[csf('sewing_line')]][$pr_date]['tpd']*$po_rate); 
						$location_today_fob_arc_qnty+=($row[csf('qnty')]*$po_rate); 
						
						$location_today_out_qnty+=$row[csf('qnty')]; 
						$location_today_smv+=$today_smv; 
						$location_item_smv+=$item_smv; 
						$location_achv_smv+=$achv_smv; 
						$location_cm_value+=$cm_value; 
						$location_tot_prod+=$total_prod; 
						$location_wip+=$wip; 
						$location_tot_smv_used+=$total_smv_used; 
						$location_tot_achv_smv+=$total_smv_achv;
						
						$floor_order_qnty+=$po_quantity; 
						$floor_machine_qnty+=$prod_resource_array2[$row[csf('sewing_line')]][$pr_date]['active_machine']; 
						$floor_worker+=$prod_resource_array2[$row[csf('sewing_line')]][$pr_date]['man_power']; 
						$floor_operator+=$prod_resource_array2[$row[csf('sewing_line')]][$pr_date]['operator']; 
						$floor_helper+=$prod_resource_array2[$row[csf('sewing_line')]][$pr_date]['helper']; 
						$floor_prev_input_qnty+=$prev_input_qnty; 
						$floor_prev_out_qnty+=$prev_out_qnty;
						$floor_prev_wip+=$prev_wip;
						$floor_today_target+=$prod_resource_array2[$row[csf('sewing_line')]][$pr_date]['tpd']; 
						$floor_today_input_qnty+=$today_input_qnty; 
						
						$floor_today_tar_fob_val+=($prod_resource_array2[$row[csf('sewing_line')]][$pr_date]['tpd']*$po_rate); 
						$floor_today_fob_arc_qnty+=($row[csf('qnty')]*$po_rate); 
						
						$floor_today_out_qnty+=$row[csf('qnty')]; 
						$floor_today_smv+=$today_smv; 
						$floor_item_smv+=$item_smv; 
						$floor_achv_smv+=$achv_smv; 
						$floor_cm_value+=$cm_value; 
						$floor_tot_prod+=$total_prod; 
						$floor_wip+=$wip; 
						$floor_tot_smv_used+=$total_smv_used; 
						$floor_tot_achv_smv+=$total_smv_achv;
						
						$buyer_data_array[$row[csf('buyer_name')]]['toin']+=$today_input_qnty;
						$buyer_data_array[$row[csf('buyer_name')]]['topd']+=$row[csf('qnty')];
						$buyer_data_array[$row[csf('buyer_name')]]['tosmv']+=$today_smv;
						$buyer_data_array[$row[csf('buyer_name')]]['achv_smv']+=$achv_smv;
						$buyer_data_array[$row[csf('buyer_name')]]['tpd']+=$prod_resource_array2[$row[csf('sewing_line')]][$pr_date]['tpd'];
						$buyer_data_array[$row[csf('buyer_name')]]['man_power']+=$prod_resource_array2[$row[csf('sewing_line')]][$pr_date]['man_power'];
						$buyer_data_array[$row[csf('buyer_name')]]['operator']+=$prod_resource_array2[$row[csf('sewing_line')]][$pr_date]['operator'];
						$buyer_data_array[$row[csf('buyer_name')]]['helper']+=$prod_resource_array2[$row[csf('sewing_line')]][$pr_date]['helper'];
						$buyer_data_array[$row[csf('buyer_name')]]['cm']+=$cm_value;
						
						if($duplicate_array[$row[csf('prod_reso_allo')]][$row[csf('sewing_line')]]=="")
						{
							$total_actual_machine_qnty+=$prod_resource_array2[$row[csf('sewing_line')]][$pr_date]['active_machine']; 
							$tot_actual_worker+=$prod_resource_array2[$row[csf('sewing_line')]][$pr_date]['man_power']; 
							$tot_actual_operator+=$prod_resource_array2[$row[csf('sewing_line')]][$pr_date]['operator']; 
							$tot_actual_helper+=$prod_resource_array2[$row[csf('sewing_line')]][$pr_date]['helper'];
							$tot_actual_today_target+=$prod_resource_array2[$row[csf('sewing_line')]][$pr_date]['tpd']; 
							$tot_actual_today_smv+=$today_smv; 
							$grand_tot_actual_smv_used+=$total_smv_used; 
							
							$duplicate_array[$row[csf('prod_reso_allo')]][$row[csf('sewing_line')]]=$row[csf('sewing_line')];
						}
					
					}
					$floorLineArr=array_unique(explode(",",chop($floor_line_arr[$location][$floor][$pr_date],',')));
					$prod_lines_arr=explode(",",chop($prod_lines,','));
					//echo $prod_lines;
					//print_r($floorLineArr);
					$noProdLinesArr=array_diff($floorLineArr, $prod_lines_arr);
					if($cbo_no_prod_type==155) //No Production line Start ....
					{/* 
					
					//print_r($floorLineArr);
					if(count($noProdLinesArr)>0)
					{
						foreach($noProdLinesArr as $line)
						{
							$sewing_line='';
							$line_number=$line_number=explode(",",$prod_reso_arr[$line]);
							foreach($line_number as $val)
							{
								if($sewing_line=='') $sewing_line=$lineArr[$val]; else $sewing_line.=",".$lineArr[$val];
							}
							$total_adjustment=0;
						$smv_adjustmet_type=$prod_resource_array[$line][$pr_date]['smv_adjust_type'];
						if(str_replace("'","",$smv_adjustmet_type)==1) $total_adjustment=$prod_resource_array[$line][$pr_date]['smv_adjust'];
						if(str_replace("'","",$smv_adjustmet_type)==2) $total_adjustment=($prod_resource_array[$line][$pr_date]['smv_adjust'])*(-1);
						
						
							$today_smv=$prod_resource_array[$line][$pr_date]['tsmv']+$total_adjustment;
						    $achv_smv=$row[csf('qnty')]*$item_smv; 
							$today_ach_perc=$row[csf('qnty')]/$prod_resource_array[$line][$pr_date]['tpd']*100;
							$today_aff_perc=$achv_smv/$today_smv*100;
							$total_prod=$row[csf('qnty')]+$prev_out_qnty;	
							$wip=$prev_input_qnty+$today_input_qnty-$total_prod;
							
						$no_of_days=0; $total_smv_used=0; $total_adjust=0;
						foreach($dataLibArray as $libRow)
						{
							$date_calc=change_date_format($libRow[csf('date_calc')]);
							$smv_adjustmet_type=$prod_resource_array[$line][$date_calc]['smv_adjust_type'];
							if(str_replace("'","",$smv_adjustmet_type)==1) $total_adjust=$prod_resource_array[$line][$date_calc]['smv_adjust'];
							else if(str_replace("'","",$smv_adjustmet_type)==2) $total_adjust=($prod_resource_array[$line][$date_calc]['smv_adjust'])*(-1);
							else $total_adjust=0;
							$total_smv_used+=$prod_resource_array[$line][$date_calc]['tsmv']+$total_adjust;
							$no_of_days++;
						}
						//$total_smv_used=$today_smv*$no_of_days;
						$avg_per_day=$total_prod/$no_of_days;
						$total_smv_achv=$item_smv*$total_prod;
						$avg_aff_perc=$total_smv_achv/$total_smv_used*100;
						
						$dzn_qnty=0; $cm_value=0;
						if($costing_per_arr[$row[csf('job_no')]]==1) $dzn_qnty=12;
						else if($costing_per_arr[$row[csf('job_no')]]==3) $dzn_qnty=12*2;
						else if($costing_per_arr[$row[csf('job_no')]]==4) $dzn_qnty=12*3;
						else if($costing_per_arr[$row[csf('job_no')]]==5) $dzn_qnty=12*4;
						else $dzn_qnty=1;
						
						$dzn_qnty=$dzn_qnty*$row[csf('ratio')];
						$cm_value=($tot_cost_arr[$row[csf('job_no')]]/$dzn_qnty)*$row[csf('qnty')];
							
							$floor_machine_qnty+=$prod_resource_array[$line][$pr_date]['active_machine'];
							$floor_worker+=$prod_resource_array[$line][$pr_date]['man_power'];
							$floor_operator+=$prod_resource_array[$line][$pr_date]['operator']; 
							$floor_helper+=$prod_resource_array[$line][$pr_date]['helper']; 
							$floor_today_fob_arc_qnty+=($row[csf('qnty')]*$po_rate); 
							$floor_today_out_qnty+=$row[csf('qnty')]; 
							//$floor_today_out_qnty+=$row[csf('qnty')]; 
							$floor_today_smv+=$today_smv; 
							$floor_item_smv+=$item_smv; 
							$floor_achv_smv+=$achv_smv; 
							$floor_cm_value+=$cm_value; 
							$floor_tot_prod+=$total_prod; 
							$floor_wip+=$wip; 
							$floor_tot_smv_used+=$total_smv_used; 
							$floor_tot_achv_smv+=$total_smv_achv;
							
							$location_operator+=$prod_resource_array[$line][$pr_date]['operator']; 
							$location_helper+=$prod_resource_array[$line][$pr_date]['helper']; 
							$location_worker+=$prod_resource_array[$line][$pr_date]['man_power']; 
							$location_today_target+=$prod_resource_array[$line][$pr_date]['tpd']; 
						    $floor_today_target+=$prod_resource_array[$line][$pr_date]['tpd'];
							$location_today_input_qnty+=$today_input_qnty; 
							$location_today_tar_fob_val+=($prod_resource_array[$line][$pr_date]['tpd']*$po_rate); 
						    $location_today_fob_arc_qnty+=($row[csf('qnty')]*$po_rate); 
							$location_today_smv+=$today_smv; 
							$location_item_smv+=$item_smv; 
							$location_achv_smv+=$achv_smv; 
							$location_cm_value+=$cm_value; 
							$location_tot_prod+=$total_prod; 
							$location_wip+=$wip; 
							$location_tot_smv_used+=$total_smv_used; 
							$location_tot_achv_smv+=$total_smv_achv;
							//$location_today_fob_arc_qnty+=($row[csf('qnty')]*$po_rate); 
						   
							$location_today_tar_fob_val+=($prod_resource_array[$line][$pr_date]['tpd']*$po_rate); 
							//$location_today_fob_arc_qnty+=($row[csf('qnty')]*$po_rate); 
							$location_today_out_qnty+=$row[csf('qnty')]; 

							if($duplicate_array[$row[csf('prod_reso_allo')]][$line]=="")
							{
								$total_actual_machine_qnty+=$prod_resource_array[$line][$pr_date]['active_machine']; 
								$tot_actual_worker+=$prod_resource_array[$line][$pr_date]['man_power']; 
								$tot_actual_operator+=$prod_resource_array[$line][$pr_date]['operator']; 
								$tot_actual_helper+=$prod_resource_array[$line][$pr_date]['helper'];
								$tot_actual_today_target+=$prod_resource_array[$line][$pr_date]['tpd']; 
								$tot_actual_today_smv+=$today_smv; 
								$grand_tot_actual_smv_used+=$total_smv_used; 
								
								$duplicate_array[$row[csf('prod_reso_allo')]][$line]=$line;
							}
						
								$tot_today_target+=$prod_resource_array[$line][$pr_date]['tpd'];
								$tot_today_out_qnty+=$row[csf('qnty')]; 
								$floor_today_tar_fob_val+=($prod_resource_array[$line][$pr_date]['tpd']*$po_rate); 
								$tot_today_tar_fob_val+=($prod_resource_array[$line][$pr_date]['tpd']*$po_rate); 	
								$tot_today_fob_arc_qnty+=($row[csf('qnty')]*$po_rate); 
								
								$tot_today_smv+=$today_smv; 
								$tot_item_smv+=$item_smv; 
								$tot_achv_smv+=$achv_smv; 
								$tot_cm_value+=$cm_value; 
								$grand_tot_prod+=$total_prod; 
								$tot_wip+=$wip; 
								$grand_tot_smv_used+=$total_smv_used; 
								$grand_tot_achv_smv+=$total_smv_achv;
							
							$html.="<tr bgcolor='$bgcolor' onclick=change_color('tr_$i','$bgcolor') id=tr_$i>";
							$html.='<td width="40">'.$i.'</td>
									<td width="50"><p>'.$sewing_line.'&nbsp;</p></td>
									<td width="70"><p>&nbsp;</p></td>
									<td width="110"><p>&nbsp;</p></td>
									<td width="110"><p>&nbsp;</p></td>
									<td width="140"><p>&nbsp;</p></td>
									<td width="75" align="right">&nbsp;</td>
									<td width="75" align="right">&nbsp;</td>
									<td width="70" align="right">'.$prod_resource_array[$line][$pr_date]['active_machine'].'&nbsp;</td>
									<td width="60" align="right">'.$prod_resource_array[$line][$pr_date]['man_power'].'&nbsp;</td>
									<td width="60" align="right">'.$prod_resource_array[$line][$pr_date]['operator'].'&nbsp;</td>
									<td width="60" align="right">'.$prod_resource_array[$line][$pr_date]['helper'].'&nbsp;</td>
									<td width="120"><p>'.$prod_resource_array[$line][$pr_date]['line_chief'].'&nbsp;</p></td>
									<td width="80" align="center">'.change_date_format($fstinput_date).'&nbsp;</td>
									<td width="75" align="right"><a href="##" onclick="openmypage('.$comapny_id.",'".$row[csf('po_id')]."',".$row[csf('item_number_id')].",".$location.",".$row[csf('floor_id')].",".$line.",".$txt_date.",'prev_qnty',4".",".$row[csf('prod_reso_allo')].')">'.$prev_input_qnty.'</a>&nbsp;</td>
									<td width="75" align="right"><a href="##" onclick="openmypage('.$comapny_id.",'".$row[csf('po_id')]."',".$row[csf('item_number_id')].",".$location.",".$row[csf('floor_id')].",".$line.",".$txt_date.",'prev_qnty',5".",".$row[csf('prod_reso_allo')].')">'.$prev_out_qnty.'</a>&nbsp;</td>
									<td width="75" align="right">'.$prev_wip.'&nbsp;</td>
									
									<td width="75" align="right">'.$prod_resource_array[$line][$pr_date]['tpd'].'&nbsp;</td>
									<td width="75" align="right">'.$today_input_qnty.'&nbsp;</td>
									
									<td width="75" align="right">'.$po_rate.'</td>
									<td width="75" align="right">'.number_format($po_rate*$prod_resource_array[$line][$pr_date]['tpd'],2,'.','').'</td>
									<td width="75" align="right">'.number_format($po_rate*$row[csf('qnty')],2,'.','').'</td>
									
									<td width="75" align="right"><a href="##" onclick="openmypage('.$comapny_id.",'".$row[csf('po_id')]."',".$row[csf('item_number_id')].",".$location.",".$floor.",".$line.",".$txt_date.",'today_prod',5".",".$row[csf('prod_reso_allo')].')">'.$row[csf('qnty')].'</a>&nbsp;</td>
									<td width="75" align="right">'.number_format($today_ach_perc,2).'&nbsp;</td>
									<td width="75" align="right">'.$today_smv.'&nbsp;</p></td>
									<td width="70" align="right">'.$item_smv.'&nbsp;</td>
									<td width="100" align="right">'.$achv_smv.'&nbsp;</td>
									<td width="75" align="right">'.number_format($today_aff_perc,2).'&nbsp;</td>
									<td width="110" align="right">'.number_format($cm_value,2,'.','').'&nbsp;</td>
									<td width="90" align="right"><a href="##" onclick="openmypage('.$comapny_id.",'".$job_po_id."',".$row[csf('item_number_id')].",".$location.",".$floor.",".$line.",".$txt_date.",'tot_prod',5".",".$row[csf('prod_reso_allo')].')">'.$total_prod.'</a>&nbsp;</td>
									<td width="80" align="right">'.number_format($avg_per_day,2,'.','').'&nbsp;</td>
									<td width="80" align="right">'.$wip.'&nbsp;</td>
									<td width="75" align="right"><a href="##" onclick="openmypage('.$comapny_id.",'".$row[csf('po_id')]."',".$row[csf('item_number_id')].",".$location.",".$floor.",".$line.",".$txt_date.",'tot_smv_used',"."'".$fstinput_date."'".",".$row[csf('prod_reso_allo')].')">'.number_format($total_smv_used,2,'.','').'</a>&nbsp;</td>
									<td width="75" align="right">'.number_format($total_smv_achv,2,'.','').'&nbsp;</td>
									<td align="right" width="75">'.number_format($avg_aff_perc,2,'.','').'&nbsp;</td>';
										
							 $line_number_id=$line;
							
							 $html.='<td><input type="button" value="View" class="formbutton" onclick="show_line_remarks('.$comapny_id.",'".$total_po_id."','".$floor."','".$line_number_id."','remarks_popup',".$txt_date.')"/></td>
								</tr>';
							$i++;	
						}
					}
					*/}//No Prod. End
				}
				
			
				$floor_today_ach_perc=$floor_today_out_qnty/$floor_today_target*100;
				$floor_today_aff_perc=$floor_achv_smv/$floor_today_smv*100;
				$floor_avg_aff_perc=$floor_tot_achv_smv/$floor_tot_smv_used*100;
				
				$html.='<tr bgcolor="#CCCCCC">
						<td colspan="6" align="right">Floor Total</td>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
						<td align="right">'.$floor_machine_qnty.'</td>
						<td align="right">'.$floor_worker.'</td>
						<td align="right">'.$floor_operator.'</td>
						<td align="right">'.$floor_helper.'</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td align="right">'.$floor_prev_input_qnty.'</td>
						<td align="right">'.$floor_prev_out_qnty.'</td>
						<td align="right">'.$floor_prev_wip.'</td>
						<td align="right">'.$floor_today_target.'</td>
						<td align="right">'.$floor_today_input_qnty.'</td>

						<td width="75" align="right">&nbsp;</td>
						<td width="75" align="right">'.number_format($floor_today_tar_fob_val,2,'.','').'</td>
						<td width="75" align="right">'.number_format($floor_today_fob_arc_qnty,2,'.','').'</td>
						
						<td align="right">'.$floor_today_out_qnty.'</td>
						<td align="right">'.number_format($floor_today_aff_perc,2,'.','').'</td>
						<td align="right">'.$floor_today_smv.'</td>
						<td align="right">&nbsp;</td>
						<td align="right">'.$floor_achv_smv.'</td>
						<td align="right">'.number_format($floor_today_aff_perc,2,'.','').'</td>
						<td align="right">'.number_format($floor_cm_value,2,'.','').'</td>
						<td align="right">'.$floor_tot_prod.'</td>
						<td>&nbsp;</td>
						<td align="right">'.$floor_wip.'</td>
						<td align="right">'.number_format($floor_tot_smv_used,2,'.','').'</td>
						<td align="right">'.number_format($floor_tot_achv_smv,2,'.','').'</td>
						<td align="right">'.number_format($floor_avg_aff_perc,2,'.','').'</td>
						<td>&nbsp;</td>
					</tr>';
			}//'.$floor_item_smv.'
			$floorLineArr_no=array_unique(explode(",",chop($location_floor_line_arr[$location][$pr_date],',')));
			$prod_location_arr=explode(",",chop($prod_location,','));
			$nolineProdFloorsArr=array_diff($floorLineArr_no, $prod_location_arr);
			//foreach($nolineProdFloorsArr as $floor)
			//{}
			
			$location_today_ach_perc=$location_today_out_qnty/$location_today_target*100;
			$location_today_aff_perc=$location_achv_smv/$location_today_smv*100;
			$location_avg_aff_perc=$location_tot_achv_smv/$location_tot_smv_used*100;
			$html.='<tr bgcolor="#E9F3FF">
						<td colspan="6" align="right">Location Total</td>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
						<td align="right">'.$location_machine_qnty.'</td>
						<td align="right">'.$location_worker.'</td>
						<td align="right">'.$location_operator.'</td>
						<td align="right">'.$location_helper.'</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td align="right">'.$location_prev_input_qnty.'</td>
						<td align="right">'.$location_prev_out_qnty.'</td>
						<td align="right">'.$location_prev_wip.'</td>
						<td align="right">'.$location_today_target.'</td>
						<td align="right">'.$location_today_input_qnty.'</td>
						
						<td width="75" align="right">&nbsp;</td>
						<td width="75" align="right">'.number_format($location_today_tar_fob_val,2,'.','').'</td>
						<td width="75" align="right">'.number_format($location_today_fob_arc_qnty,2,'.','').'</td>
						
						<td align="right">'.$location_today_out_qnty.'</td>
						<td align="right">'.number_format($location_today_ach_perc,2,'.','').'</td>
						<td align="right">'.$location_today_smv.'</td>
						<td align="right">&nbsp;</td>
						<td align="right">'.$location_achv_smv.'</td>
						<td align="right">'.number_format($location_today_aff_perc,2,'.','').'</td>
						<td align="right">'.number_format($location_cm_value,2,'.','').'</td>
						<td align="right">'.$location_tot_prod.'</td>
						<td>&nbsp;</td>
						<td align="right">'.$location_wip.'</td>
						<td align="right">'.number_format($location_tot_smv_used,2,'.','').'</td>
						<td align="right">'.number_format($location_tot_achv_smv,2,'.','').'</td>
						<td align="right">'.number_format($location_avg_aff_perc,2,'.','').'</td>
						<td>&nbsp;</td>
					</tr>';
						
		}
	
	
	
		$suncon_line_data_array=array();
		foreach($resultSub as $row )
		{
			$sewing_line_id='';
			if($row[csf('prod_reso_allo')]==1)
			{
				$sewing_line_id=$prod_reso_arr[$row[csf('line_id')]];
			}
			else
			{
				$sewing_line_id=$row[csf('line_id')];
			}
			
			if($lineSerialArr[$sewing_line_id]=="")
			{
				$lastSlNo++;
				$slNo=$lastSlNo;
				$lineSerialArr[$sewing_line_id];
			}
			else $slNo=$lineSerialArr[$sewing_line_id];
			
			$suncon_line_data_array[$row[csf('location_id')]][$row[csf('floor_id')]][$slNo].=$row[csf('subcon_job')]."##".$row[csf('party_id')]."##".$row[csf('cust_style_ref')]."##".$row[csf('gmts_item_id')]."##".$row[csf('po_id')]."##".$row[csf('order_no')]."##".$row[csf('order_quantity')]."##".$row[csf('rate')]."##".$row[csf('smv')]."##".$row[csf('prod_reso_allo')]."##".$row[csf('line_id')]."##".$row[csf('qnty')].":";
		}
		//print_r($suncon_line_data_array);
		$html_sub=''; $i=1;
		foreach($suncon_line_data_array as $location=>$locData )
		{
			$location_order_qnty_sub=0; 
			$location_machine_qnty_sub=0; 
			$location_worker_sub=0; 
			$location_operator_sub=0; 
			$location_helper_sub=0; 
			$location_prev_out_qnty_sub=0;
			$location_today_target_sub=0; 
			$location_today_tar_fob_val_sub=0;
			$location_today_fob_acv_sub=0; 
			$location_today_out_qnty_sub=0; 
			$location_today_smv_sub=0; 
			$location_item_smv_sub=0; 
			$location_achv_smv_sub=0; 
			$location_tot_prod_sub=0; 
			$location_tot_smv_used_sub=0; 
			$location_tot_achv_smv_sub=0;
			
			foreach($locData as $floor=>$floorData )
			{
				$html_sub.='<tr bgcolor="#EFEFEF"><td colspan="35"><b>Floor name: '.$floorArr[$floor].'; Location name: '.$locationArr[$location].'</b></td></tr>';
			
				$floor_order_qnty_sub=0; 
				$floor_machine_qnty_sub=0; 
				$floor_worker_sub=0; 
				$floor_operator_sub=0; 
				$floor_helper_sub=0; 
				$floor_prev_out_qnty_sub=0;
				$floor_today_target_sub=0; 
				$floor_today_tar_fob_val_sub=0;
				$floor_today_fob_acv_sub=0; 
				$floor_today_out_qnty_sub=0; 
				$floor_today_smv_sub=0; 
				$floor_item_smv_sub=0; 
				$floor_achv_smv_sub=0; 
				$floor_tot_prod_sub=0; 
				$floor_tot_smv_used_sub=0; 
				$floor_tot_achv_smv_sub=0;
				
				ksort($floorData);
				foreach($floorData as $slNo=>$lineData )
				{
					$lineSlData=explode(":",chop($lineData,':'));
					foreach($lineSlData as $value )
					{
						$lineDataArr=explode("##",$value);
						$subcon_job=$lineDataArr[0];
						$party_id=$lineDataArr[1];
						$cust_style_ref=$lineDataArr[2];
						$gmts_item_id=$lineDataArr[3];
						$po_id=$lineDataArr[4];
						$po_number=$lineDataArr[5];
						$po_quantity=$lineDataArr[6];
						$po_rate=$lineDataArr[7];
						$item_smv=$lineDataArr[8];
						$prod_reso_allo=$lineDataArr[9];
						$line_id=$lineDataArr[10];
						$qnty=$lineDataArr[11];
						
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$mst_sql= "SELECT sum(production_qnty) AS prev_out_qnty, min(production_date) as fstinput_date from subcon_gmts_prod_dtls where order_id=".$po_id." and production_date<$txt_date and gmts_item_id='".$gmts_item_id."' and location_id='".$location."' and floor_id='".$floor."' and prod_reso_allo='".$prod_reso_allo."' and line_id='".$line_id."' and is_deleted=0 and status_active=1"; 
						//echo $mst_sql;//die;
						$dataArray = sql_select($mst_sql);
						$fstinput_date=$dataArray[0][csf('fstinput_date')]; 
						$prev_out_qnty=$dataArray[0][csf('prev_out_qnty')]; 
						$today_ach_perc=$qnty/$prod_resource_array[$line_id][$pr_date]['tpd']*100;
						
						$total_adjustment=0;
						$smv_adjustmet_type=$prod_resource_array[$line_id][$pr_date]['smv_adjust_type'];
						if(str_replace("'","",$smv_adjustmet_type)==1) $total_adjustment=$prod_resource_array[$line_id][$pr_date]['smv_adjust'];
						if(str_replace("'","",$smv_adjustmet_type)==2) $total_adjustment=($prod_resource_array[$line_id][$pr_date]['smv_adjust'])*(-1);
						
						$today_smv=$prod_resource_array[$line_id][$pr_date]['tsmv']+$total_adjustment;
						$achv_smv=$qnty*$item_smv; 
						$today_aff_perc=$achv_smv/$today_smv*100;
						$total_prod=$qnty+$prev_out_qnty;	
						
						$dataLibArray=sql_select("select b.date_calc from lib_capacity_calc_mst a, lib_capacity_calc_dtls b where a.id=b.mst_id and a.comapny_id='$comapny_id' and b.date_calc between '$fstinput_date' and $txt_date and day_status=1");
						$no_of_days=0; $total_smv_used=0; $total_adjust=0;
						foreach($dataLibArray as $libRow)
						{
							$date_calc=change_date_format($libRow[csf('date_calc')]);
							$smv_adjustmet_type=$prod_resource_array[$line_id][$date_calc]['smv_adjust_type'];
							if(str_replace("'","",$smv_adjustmet_type)==1) $total_adjust=$prod_resource_array[$line_id][$date_calc]['smv_adjust'];
							else if(str_replace("'","",$smv_adjustmet_type)==2) $total_adjust=($prod_resource_array[$line_id][$date_calc]['smv_adjust'])*(-1);
							else $total_adjust=0;
							$total_smv_used+=$prod_resource_array[$line_id][$date_calc]['tsmv']+$total_adjust;
							$no_of_days++;
						}

						$avg_per_day=$total_prod/$no_of_days;
						$total_smv_achv=$item_smv*$total_prod;
						$avg_aff_perc=$total_smv_achv/$total_smv_used*100;
						
						$actual_line_arr.=$line_id.",";
						$sewing_line='';
						if($prod_reso_allo==1)
						{
							$line_number=explode(",",$prod_reso_arr[$line_id]);
							foreach($line_number as $val)
							{
								if($sewing_line=='') $sewing_line=$lineArr[$val]; else $sewing_line.=",".$lineArr[$val];
							}
						}
						else $sewing_line=$lineArr[$line_id];
						
						$html_sub.="<tr bgcolor='$bgcolor' onclick=change_color('tr2_$i','$bgcolor') id=tr2_$i>";
						$html_sub.='<td width="40">'.$i.'</td>
								<td width="50"><p>'.$sewing_line.'</p></td>
								<td width="70"><p>'.$buyerArr[$party_id].'</p></td>
								<td width="110"><p>'.$po_number.'</p></td>
								<td width="110"><p>'.$cust_style_ref.'</p></td>
								<td width="140"><p>'.$garments_item[$gmts_item_id].'</p></td>
								<td width="75" align="right">'.$po_quantity.'</td>
								<td width="70" align="right">'.$prod_resource_array[$line_id][$pr_date]['active_machine'].'</td>
								<td width="60" align="right">'.$prod_resource_array[$line_id][$pr_date]['man_power'].'</td>
								<td width="60" align="right">'.$prod_resource_array[$line_id][$pr_date]['operator'].'</td>
								<td width="60" align="right">'.$prod_resource_array[$line_id][$pr_date]['helper'].'</td>
								<td width="120"><p>'.$prod_resource_array[$line_id][$pr_date]['line_chief'].'</p></td>
								<td width="80" align="center">'.change_date_format($fstinput_date).'</td>
								<td width="75" align="right"><a href="##" onclick="openmypage('.$comapny_id.",'".$po_id."',".$gmts_item_id.",".$location.",".$floor.",".$line_id.",".$txt_date.",'prev_qnty_sub',2".",".$prod_reso_allo.')">'.number_format($prev_out_qnty,0,'.','').'</a></td>
								<td width="75" align="right">'.$prod_resource_array[$line_id][$pr_date]['tpd'].'</td>
								<td width="75" align="right">'.$po_rate.'</td>
								<td width="75" align="right">'.number_format($po_rate*$prod_resource_array[$line_id][$pr_date]['tpd'],2,'.','').'</td>
								<td width="75" align="right">'.number_format($po_rate*$qnty,2,'.','').'</td>
								<td width="75" align="right"><a href="##" onclick="openmypage('.$comapny_id.",'".$po_id."',".$gmts_item_id.",".$location.",".$floor.",".$line_id.",".$txt_date.",'today_prod_sub',2".",".$prod_reso_allo.')">'.$qnty.'</a></td>
								<td width="75" align="right">'.number_format($today_ach_perc,2).'</td>
								<td width="75" align="right">'.$today_smv.'</p></td>
								<td width="70" align="right">'.$item_smv.'</td>
								<td width="100" align="right">'.$achv_smv.'</td>
								<td width="75" align="right">'.number_format($today_aff_perc,2).'</td>
								<td width="90" align="right"><a href="##" onclick="openmypage('.$comapny_id.",'".$po_id."',".$gmts_item_id.",".$location.",".$floor.",".$line_id.",".$txt_date.",'tot_prod_sub',2".",".$prod_reso_allo.')">'.$total_prod.'</a></td>
								<td width="80" align="right">'.number_format($avg_per_day,2,'.','').'</td>
								<td width="100" align="right"><a href="##" onclick="openmypage('.$comapny_id.",'".$po_id."',".$gmts_item_id.",".$location.",".$floor.",".$line_id.",".$txt_date.",'tot_smv_used',"."'".$fstinput_date."'".",".$prod_reso_allo.')">'.number_format($total_smv_used,2,'.','').'</a></td>
								<td width="100" align="right">'.number_format($total_smv_achv,2,'.','').'</td>
								<td align="right" width="100">'.number_format($avg_aff_perc,2,'.','').'</td>';
									
						 $html_sub.='<td><input type="button" value="View" class="formbutton" onclick="show_line_remarks('.$comapny_id.",'".$po_id."','".$floor."','".$line_id."','remarks_popup_sub',".$txt_date.')"/></td>
							</tr>';
			   
						$i++;
						
						$total_order_qnty_sub+=$po_quantity; 
						$total_machine_qnty_sub+=$prod_resource_array[$line_id][$pr_date]['active_machine']; 
						$tot_worker_sub+=$prod_resource_array[$line_id][$pr_date]['man_power']; 
						$tot_operator_sub+=$prod_resource_array[$line_id][$pr_date]['operator']; 
						$tot_helper_sub+=$prod_resource_array[$line_id][$pr_date]['helper']; 
						$tot_prev_out_qnty_sub+=$prev_out_qnty;
						$tot_today_targe_subt+=$prod_resource_array[$line_id][$pr_date]['tpd']; 
						
						$tot_today_tar_fob_val_sub+=($prod_resource_array[$line_id][$pr_date]['tpd']*$po_rate); 
						$tot_today_fob_arc_qnty_sub+=($qnty*$po_rate); 
						
						$tot_today_out_qnty_sub+=$qnty; 
						$tot_today_smv_sub+=$today_smv; 
						$tot_item_smv_sub+=$item_smv; 
						$tot_achv_smv_sub+=$achv_smv; 
						$grand_tot_prod_sub+=$total_prod; 
						$grand_tot_smv_used_sub+=$total_smv_used; 
						$grand_tot_achv_smv_sub+=$total_smv_achv;
						
						$location_order_qnty_sub+=$po_quantity; 
						$location_machine_qnty_sub+=$prod_resource_array[$line_id][$pr_date]['active_machine']; 
						$location_worker_sub+=$prod_resource_array[$line_id][$pr_date]['man_power']; 
						$location_operator_sub+=$prod_resource_array[$line_id][$pr_date]['operator']; 
						$location_helper_sub+=$prod_resource_array[$line_id][$pr_date]['helper']; 
						$location_prev_out_qnty_sub+=$prev_out_qnty;
						$location_today_target_sub+=$prod_resource_array[$line_id][$pr_date]['tpd']; 
						
						$location_today_tar_fob_val_sub+=($prod_resource_array[$line_id][$pr_date]['tpd']*$po_rate); 
						$location_today_fob_arc_qnty_sub+=($qnty*$po_rate); 
						
						$location_today_out_qnty_sub+=$qnty; 
						$location_today_smv_sub+=$today_smv; 
						$location_item_smv_sub+=$item_smv; 
						$location_achv_smv_sub+=$achv_smv; 
						$location_tot_prod_sub+=$total_prod; 
						$location_tot_smv_used_sub+=$total_smv_used; 
						$location_tot_achv_smv_sub+=$total_smv_achv;
						
						$floor_order_qnty_sub+=$po_quantity; 
						$floor_machine_qnty_sub+=$prod_resource_array[$line_id][$pr_date]['active_machine']; 
						$floor_worker_sub+=$prod_resource_array[$line_id][$pr_date]['man_power']; 
						$floor_operator_sub+=$prod_resource_array[$line_id][$pr_date]['operator']; 
						$floor_helper_sub+=$prod_resource_array[$line_id][$pr_date]['helper']; 
						$floor_prev_input_qnty_sub+=$prev_input_qnty; 
						$floor_prev_out_qnty_sub+=$prev_out_qnty;
						$floor_prev_wip_sub+=$prev_wip;
						$floor_today_target_sub+=$prod_resource_array[$line_id][$pr_date]['tpd']; 
						$floor_today_input_qnty_sub+=$today_input_qnty; 
						
						$floor_today_tar_fob_val_sub+=($prod_resource_array[$line_id][$pr_date]['tpd']*$po_rate); 
						$floor_today_fob_arc_qnty_sub+=($qnty*$po_rate); 
						
						$floor_today_out_qnty_sub+=$qnty; 
						$floor_today_smv_sub+=$today_smv; 
						$floor_item_smv_sub+=$item_smv; 
						$floor_achv_smv_sub+=$achv_smv; 
						$floor_cm_value_sub+=$cm_value; 
						$floor_tot_prod_sub+=$total_prod; 
						$floor_wip_sub+=$wip; 
						$floor_tot_smv_used_sub+=$total_smv_used; 
						$floor_tot_achv_smv_sub+=$total_smv_achv;

						if($duplicate_sub_array[$prod_reso_allo][$line_id]=="")
						{
							$total_actual_machine_qnty_sub+=$prod_resource_array[$line_id][$pr_date]['active_machine']; 
							$tot_actual_worker_sub+=$prod_resource_array[$line_id][$pr_date]['man_power']; 
							$tot_actual_operator_sub+=$prod_resource_array[$line_id][$pr_date]['operator']; 
							$tot_actual_helper_sub+=$prod_resource_array[$line_id][$pr_date]['helper'];
							$tot_actual_today_target_sub+=$prod_resource_array[$line_id][$pr_date]['tpd']; 
							$tot_actual_today_smv_sub+=$today_smv; 
							$grand_tot_actual_smv_used_sub+=$total_smv_used; 
							
							$duplicate_sub_array[$prod_reso_allo][$line_id]=$line_id;
						}
					
					}
				}
				
				$floor_today_ach_perc_sub=$floor_today_out_qnty_sub/$floor_today_target_sub*100;
				$floor_today_aff_perc_sub=$floor_achv_smv_sub/$floor_today_smv_sub*100;
				$floor_avg_aff_perc_sub=$floor_tot_achv_smv_sub/$floor_tot_smv_used_sub*100;
				
				$html_sub.='<tr bgcolor="#CCCCCC">
						<td colspan="6" align="right">Floor Total</td>
						<td align="right">&nbsp;</td>
						<td align="right">'.$floor_machine_qnty_sub.'</td>
						<td align="right">'.$floor_worker_sub.'</td>
						<td align="right">'.$floor_operator_sub.'</td>
						<td align="right">'.$floor_helper_sub.'</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td align="right">'.$floor_prev_out_qnty_sub.'</td>
						<td align="right">'.$floor_today_target_sub.'</td>

						<td width="75" align="right">&nbsp;</td>
						<td width="75" align="right">'.number_format($floor_today_tar_fob_val_sub,2,'.','').'</td>
						<td width="75" align="right">'.number_format($floor_today_fob_arc_qnty_sub,2,'.','').'</td>
						
						<td align="right">'.$floor_today_out_qnty_sub.'</td>
						<td align="right">'.number_format($floor_today_aff_perc_sub,2,'.','').'</td>
						<td align="right">'.$floor_today_smv_sub.'</td>
						<td align="right">'.$floor_item_smv_sub.'</td>
						<td align="right">'.$floor_achv_smv_sub.'</td>
						<td align="right">'.number_format($floor_today_ach_perc_sub,2,'.','').'</td>
						<td align="right">'.$floor_tot_prod_sub.'</td>
						<td>&nbsp;</td>
						<td align="right">'.number_format($floor_tot_smv_used_sub,2,'.','').'</td>
						<td align="right">'.number_format($floor_tot_achv_smv_sub,2,'.','').'</td>
						<td align="right">'.number_format($floor_avg_aff_perc_sub,2,'.','').'</td>
						<td>&nbsp;</td>
					</tr>';
			}
			
			$location_today_ach_perc_sub=$location_today_out_qnty_sub/$location_today_target_sub*100;
			$location_today_aff_perc_sub=$location_achv_smv_sub/$location_today_smv_sub*100;
			$location_avg_aff_perc_sub=$location_tot_achv_smv_sub/$location_tot_smv_used_sub*100;
			$html_sub.='<tr bgcolor="#E9F3FF">
						<td colspan="6" align="right">Location Total</td>
						<td align="right">&nbsp;</td>
						<td align="right">'.$location_machine_qnty_sub.'</td>
						<td align="right">'.$location_worker_sub.'</td>
						<td align="right">'.$location_operator_sub.'</td>
						<td align="right">'.$location_helper_sub.'</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td align="right">'.$location_prev_out_qnty_sub.'</td>
						<td align="right">'.$location_today_target_sub.'</td>
						
						<td width="75" align="right">&nbsp;</td>
						<td width="75" align="right">'.number_format($location_today_tar_fob_val_sub,2,'.','').'</td>
						<td width="75" align="right">'.number_format($location_today_fob_arc_qnty_sub,2,'.','').'</td>
						
						<td align="right">'.$location_today_out_qnty_sub.'</td>
						<td align="right">'.number_format($location_today_ach_perc_sub,2,'.','').'</td>
						<td align="right">'.$location_today_smv_sub.'</td>
						<td align="right">'.$location_item_smv_sub.'</td>
						<td align="right">'.$location_achv_smv_sub.'</td>
						<td align="right">'.number_format($location_today_aff_perc_sub,2,'.','').'</td>
						<td align="right">'.$location_tot_prod_sub.'</td>
						<td>&nbsp;</td>
						<td align="right">'.number_format($location_tot_smv_used_sub,2,'.','').'</td>
						<td align="right">'.number_format($location_tot_achv_smv_sub,2,'.','').'</td>
						<td align="right">'.number_format($location_avg_aff_perc_sub,2,'.','').'</td>
						<td>&nbsp;</td>
					</tr>';
		}
		ob_start();	
		?>
		<fieldset style="width:2880px">
			<table width="2870" cellpadding="0" cellspacing="0"> 
				<tr class="form_caption">
					<td colspan="35" align="center"><strong><? echo $report_title; ?></strong></td> 
				</tr>
				<tr class="form_caption">
					<td colspan="35" align="center"><strong><? echo $companyArr[$comapny_id]; ?></strong></td> 
				</tr>
			</table>
			<table id="table_header_1" class="rpt_table" width="2870" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
					<th width="40">SL</th>
					<th width="50">Line No</th>
					<th width="70">Buyer</th>
					<th width="110">Order No</th>
					<th width="110">Style Ref.</th>
					<th width="140">Garments Item</th>
					<th width="75">CM per dzn(As per Budget)</th>
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
					<th width="75">FOB/PCs</th>
					<th width="75">Target FOB Value</th>
					<th width="75">FOB Achieve Value</th>
					<th width="75">Today Prod.</th>
					<th width="75">Today Achv. %</th>
					<th width="75">Today SMV</th>
					<th width="70">Item SMV</th>
					<th width="100">Achieved SMV</th>
					<th width="75" title="(Today Achieved SMV/Today SMV)*100">Today Eff. %</th>
					<th width="110">Today CM Value</th>
					<th width="90">Total Prod.</th>
					<th width="80">Avg. Prod./Day</th>
					<th width="80">WIP</th>
					<th width="75">TTL SMV Used</th>
					<th width="75">TTL SMV Achv.</th>
					<th width="75">Avg. Eff. %</th>
                    <th>Remarks</th>
				</thead>
			</table>
			<div style="width:2890px; max-height:400px; overflow-y:scroll" id="scroll_body">
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
							<th align="right"><? //echo $total_order_qnty; ?>&nbsp;</th>
							<th align="right"><? echo $total_actual_machine_qnty; ?></th>
							<th align="right"><? echo $tot_actual_worker; ?></th>
							<th align="right"><? echo $tot_actual_operator; ?></th>
							<th align="right"><? echo $tot_actual_helper; ?></th>
							<th>&nbsp;</th>
							<th>&nbsp;</th>
							<th align="right"><? echo $tot_prev_input_qnty; ?></th>
							<th align="right"><? echo $tot_prev_out_qnty; ?></th>
							<th align="right"><? echo $tot_prev_wip; ?></th>
							<th align="right"><? echo $tot_actual_today_target; ?></th>
							<th align="right"><? echo $tot_today_input_qnty; ?></th>
							<th>&nbsp;</th>
							<th align="right"><? echo number_format($tot_today_tar_fob_val,2,'.',''); ?></th>
							<th align="right"><? echo number_format($tot_today_fob_arc_qnty,2,'.',''); ?></th>
							<th align="right"><? echo $tot_today_out_qnty; ?></th>
							<th align="right"><? echo number_format($grand_today_ach_perc,2,'.',''); ?></th>
							<th align="right"><? echo $tot_actual_today_smv; ?></th>
							<th align="right"><? echo $tot_item_smv; ?></th>
							<th align="right"><? echo $tot_achv_smv; ?></th>
							<th align="right"><? echo number_format($grand_today_aff_perc,2,'.',''); ?></th>
							<th align="right"><? echo number_format($tot_cm_value,2,'.',''); ?></th>
                            <th align="right"><? echo $grand_tot_prod; ?></th>
							<th>&nbsp;</th>
							<th align="right"><? echo $tot_wip; ?></th>
							<th align="right"><? echo number_format($grand_tot_actual_smv_used,2,'.',''); ?></th>
							<th align="right"><? echo number_format($grand_tot_achv_smv,2,'.',''); ?></th>
							<th align="right"><? echo number_format($grand_avg_aff_perc,2,'.',''); ?></th>
                            <th>&nbsp;</th>
						</tr>
					</tfoot>
				</table>
            </div>
            <br />
            <u><b>Sub-Contract</b></u> 
            <table id="table_header_2" class="rpt_table" width="2470" cellpadding="0" cellspacing="0" border="1" rules="all">
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
					<th width="75">Prev. Prod. Qnty</th>
					<th width="75">Today Target</th>
					<th width="75">FOB/PCs</th>
					<th width="75">Target FOB Value</th>
					<th width="75">FOB Achieve Value</th>
					<th width="75">Today Prod.</th>
					<th width="75">Today Achv. %</th>
					<th width="75">Today SMV</th>
					<th width="70">Item SMV</th>
					<th width="100">Achieved SMV</th>
					<th width="75">Today Eff. %</th>
					<th width="90">Total Prod.</th>
					<th width="80">Avg. Prod./Day</th>
					<th width="100">TTL SMV Used</th>
					<th width="100">TTL SMV Achv.</th>
					<th width="100">Avg. Eff. %</th>
                    <th>Remarks</th>
				</thead>
			</table>
			<div style="width:2470px; max-height:400px; overflow-y:scroll" id="scroll_body">
				<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
					<? echo $html_sub; ?>
					<tfoot>
						<?
							$grand_today_ach_perc_sub=$tot_today_out_qnty_sub/$tot_actual_today_target_sub*100;
							$grand_today_aff_perc_sub=$tot_achv_smv_sub/$tot_actual_today_smv_sub*100;
							$grand_avg_aff_perc_sub=$grand_tot_achv_smv_sub/$grand_tot_actual_smv_used_sub*100;
                        ?>
                        <tr>
							<th colspan="6" align="right">Actual Total</th>
							<th align="right"><? //echo $total_order_qnty_sub; ?>&nbsp;</th>
							<th align="right"><? echo $total_actual_machine_qnty_sub; ?></th>
							<th align="right"><? echo $tot_actual_worker_sub; ?></th>
							<th align="right"><? echo $tot_actual_operator_sub; ?></th>
							<th align="right"><? echo $tot_actual_helper_sub; ?></th>
							<th>&nbsp;</th>
							<th>&nbsp;</th>
							<th align="right"><? echo $tot_prev_out_qnty_sub; ?></th>
							<th align="right"><? echo $tot_actual_today_target_sub; ?></th>
							<th>&nbsp;</th>
							<th align="right"><? echo number_format($tot_today_tar_fob_val_sub,2,'.',''); ?></th>
							<th align="right"><? echo number_format($tot_today_fob_arc_qnty_sub,2,'.',''); ?></th>
							<th align="right"><? echo $tot_today_out_qnty_sub; ?></th>
							<th align="right"><? echo number_format($grand_today_ach_perc_sub,2,'.',''); ?></th>
							<th align="right"><? echo $tot_actual_today_smv_sub; ?></th>
							<th align="right"><? echo $tot_item_smv_sub; ?></th>
							<th align="right"><? echo $tot_achv_smv_sub; ?></th>
							<th align="right"><? echo number_format($grand_today_aff_perc_sub,2,'.',''); ?></th>
                            <th align="right"><? echo $grand_tot_prod_sub; ?></th>
							<th>&nbsp;</th>
							<th align="right"><? echo number_format($grand_tot_actual_smv_used_sub,2,'.',''); ?></th>
							<th align="right"><? echo number_format($grand_tot_achv_smv_sub,2,'.',''); ?></th>
							<th align="right"><? echo number_format($grand_avg_aff_perc_sub,2,'.',''); ?></th>
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
					if($actual_line_arr!="") 
					{
						if(str_replace("","",$actual_line_arr)!="") $actual_line_arr.=",";
						$actual_line_arr=implode(",",array_unique(explode(",",chop($actual_line_arr,","))));
						$line_cond=" and a.id not in ($actual_line_arr)";
					}
			
			/*echo "select a.id, a.location_id, a.floor_id, a.line_number, sum(b.active_machine) as active_machine, b.pr_date, sum(b.man_power) as man_power, sum(b.operator) as operator, sum(b.helper) as helper, sum(b.line_chief) as line_chief, sum(b.target_per_hour) as target_per_hour, sum(b.working_hour) as working_hour,d.remarks from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_time d where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and a.company_id=$comapny_id and b.pr_date=".$txt_date." and d.shift_id=1 and a.is_deleted=0 and b.is_deleted=0 $line_cond group by a.id, a.location_id, a.floor_id, a.line_number, b.pr_date,d.remarks";*/
			 		$dataArray=sql_select("select a.id, a.location_id, a.floor_id, a.line_number, sum(b.active_machine) as active_machine, b.pr_date, sum(b.man_power) as man_power, sum(b.operator) as operator, sum(b.helper) as helper, sum(b.line_chief) as line_chief, sum(b.target_per_hour) as target_per_hour, sum(b.working_hour) as working_hour,d.remarks from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_time d where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and a.company_id=$comapny_id and b.pr_date=".$txt_date." and d.shift_id=1 and a.is_deleted=0 and b.is_deleted=0 $line_cond group by a.id, a.location_id, a.floor_id, a.line_number, b.pr_date,d.remarks");
					$l=1;
					foreach( $dataArray as $row )
					{
						if ($l%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$sewing_line='';
						$line_number=explode(",",$row[csf('line_number')]);
						foreach($line_number as $val)
						{
							if($sewing_line=='') $sewing_line=$lineArr[$val]; else $sewing_line.=",".$lineArr[$val];
						}
					?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tbltr_<? echo $l; ?>','<? echo $bgcolor; ?>')" id="tbltr_<? echo $l; ?>">
                        	<td width="40"><? echo $l; ?></td>
                            <td width="100"><p><? echo $sewing_line; ?></p></td>
                            <td width="100" align="right"><p><? echo $floorArr[$row[csf('floor_id')]]; ?></p></td>
                            <td width="75" align="right"><? echo $row[csf('man_power')]; ?></th>
                            <td width="75" align="right"><? echo $row[csf('operator')]; ?></th>
                            <td width="75" align="right"><? echo $row[csf('helper')]; ?></td>
                            <td width="75" align="right"><? echo $row[csf('working_hour')]; ?></td>
                            <td><? echo $row[csf('remarks')]; ?></td>
                        </tr>
                    <?
						$l++;
					}
				?>
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

if($action=="prev_qnty_sub")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	
	$caption="Production";
	
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
                    $sql="SELECT production_date, sum(production_qnty) AS qnty from subcon_gmts_prod_dtls 
                    where order_id in(".$po_id.") and gmts_item_id='".$item_id."' and location_id='".$location."' and floor_id='".$floor_id."' and line_id='".$sewing_line."' and prod_reso_allo='".$prod_reso_allo."' and production_type='2' and production_date<'$prod_date' and is_deleted=0 and status_active=1 group by production_date";
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

if($action=="tot_prod_sub")
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
					$sql="SELECT production_date, sum(production_qnty) AS qnty from subcon_gmts_prod_dtls 
                    where order_id in(".$po_id.") and gmts_item_id='".$item_id."' and location_id='".$location."' and floor_id='".$floor_id."' and line_id='".$sewing_line."' and prod_reso_allo='".$prod_reso_allo."' and production_type='2' and production_date<='$prod_date' and is_deleted=0 and status_active=1 group by production_date";
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
		<div id="report_container" >
			<table border="1" class="rpt_table" rules="all" width="970" cellpadding="0" cellspacing="0">
                <?
				
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
                <tr><td colspan="12"><strong>Total: &nbsp;&nbsp;<? echo  $total_qnty;?> </strong></td></tr>
			</table>
        </div>
	</fieldset>   
<?
exit();
}

if($action=="today_prod_sub")
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
		<div id="report_container" >
			<table border="1" class="rpt_table" rules="all" width="970" cellpadding="0" cellspacing="0">
                <?
				
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
							$sql.=" sum(case when hour<='$val' then production_qnty else 0 end) AS am$z ";
						}
						else
						{
							$sql.=", sum(case when hour>'$prev_hour' and hour<='$val' then production_qnty else 0 end) AS am$z ";
						}
						
						$prev_hour=$val;
						$s++;
					}
					$sql.="from subcon_gmts_prod_dtls where order_id in(".$po_id.") and gmts_item_id='".$item_id."' and location_id='".$location."' and floor_id='".$floor_id."' and line_id='".$sewing_line."' and prod_reso_allo='".$prod_reso_allo."' and production_type='2' and production_date='$prod_date' and is_deleted=0 and status_active=1";
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
							$sql.="sum(case when TO_CHAR(hour,'HH24:MI:SS')<='$val' then production_qnty else 0 end) AS am$z ";
						}
						else
						{
							$sql.=", sum(case when TO_CHAR(hour,'HH24:MI:SS')>'$prev_hour' and TO_CHAR(hour,'HH24:MI:SS')<='$val' then production_qnty else 0 end) AS am$z ";
						}
						
						$prev_hour=$val;
						$s++;
					}
					$sql.="from subcon_gmts_prod_dtls where order_id in(".$po_id.") and gmts_item_id='".$item_id."' and location_id='".$location."' and floor_id='".$floor_id."' and line_id='".$sewing_line."' and prod_reso_allo='".$prod_reso_allo."' and production_type='2' and production_date='$prod_date' and is_deleted=0 and status_active=1";
					//echo $sql;
				}

				$result=sql_select($sql);
				foreach($result as $row);
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
                <tr><td colspan="12"><strong>Total: &nbsp;&nbsp;<? echo  $total_qnty;?> </strong></td></tr>
			</table>
        </div>
	</fieldset>   
<?
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


if($action=="remarks_popup_sub")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	
	$sql_line_remark=sql_select("select remarks from subcon_gmts_prod_dtls where company_id=".$company_id." and floor_id=$floor_id and line_id=$sewing_line and order_id in($po_id) and production_type='2' and production_date='".$prod_date."' and status_active=1 and is_deleted=0");

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
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>		
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="30"><? echo $i; ?></td>
                            <td align="left"><? echo $inf[csf('remarks')]; ?>&nbsp;</td>
                        </tr>
						<?
                        $i++;
                    }
                    ?>
                </tbody>
            </table>
        </div>
	</fieldset>
<?
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

