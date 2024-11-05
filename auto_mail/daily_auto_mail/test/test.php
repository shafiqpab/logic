<?		
ini_set('precision', 8);
ini_set("display_errors", 0);
require_once('../../includes/common.php');

 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));


	$current_date = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("H:i:s",time()),0))),'','',1);
	$previous_date= change_date_format(date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))),'','',1);
	//$previous_date='12-Aug-2021';$current_date='12-Aug-2021';
	$str_cond	=" and insert_date between '".$previous_date."' and '".$current_date." 11:59:59 PM'";
	$txt_date=$previous_date;
	$to_date=$previous_date;
	$companyStr=implode(',',array_keys($company_library));

 
	$cbo_company_id=	"'4'";
	$cbo_location_id	="'2'";
 

	$cbo_company_id=str_replace("'", "",$cbo_company_id);
	
	$companyArr = return_library_array("SELECT id,company_name from lib_company","id","company_name"); 
	$buyerArr = return_library_array("SELECT id,short_name from lib_buyer","id","short_name"); 
	$locationArr = return_library_array("SELECT id,location_name from lib_location","id","location_name"); 
	$floorArr = return_library_array("SELECT id,floor_name from lib_prod_floor","id","floor_name"); 
	//$lineArr = return_library_array("select id, line_name from lib_sewing_line","id","line_name"); 
	$prod_reso_arr=return_library_array( "SELECT id, line_number from prod_resource_mst",'id','line_number');
	
	if($type==1) $report_title .=" (Poly Output)"; else $report_title .=" (Sewing Output)";
	if(str_replace("'","",$cbo_company_id)==0) $company_name_subcon=""; else $company_name_subcon="and a.company_id in(".str_replace("'","",$cbo_company_id).")";
	if(str_replace("'","",$cbo_location_id)==0) $location_subcon=""; else $location_subcon="and a.location_id in(".str_replace("'","",$cbo_location_id).")";
	if(str_replace("'","",$cbo_floor_id)==0) $floor=""; else $floor="and a.floor_id in(".str_replace("'","",$cbo_floor_id).")";
	if(str_replace("'","",$hidden_line_id)==0) $line=""; else $line="and a.lide_id in(".str_replace("'","",$hidden_line_id).")";
	if(str_replace("'","",$cbo_buyer_name)==0) $buyer=""; else $buyer="and a.lide_id in(".str_replace("'","",$cbo_buyer_name).")";
	if(str_replace("'","",trim($txt_date))=="") $txt_date_from=""; else $txt_date_from=" and a.production_date='$txt_date'";
 			



$lineDataArr = sql_select("select id, line_name, sewing_line_serial from lib_sewing_line where is_deleted=0 and status_active=1
		order by sewing_line_serial"); 
		foreach($lineDataArr as $lRow)
		{
			$lineArr[$lRow[csf('id')]]=$lRow[csf('line_name')];
			$lineSerialArr[$lRow[csf('id')]]=$lRow[csf('sewing_line_serial')];
			$lastSlNo=$lRow[csf('sewing_line_serial')];
		}
		
		$comapny_id=str_replace("'","",$cbo_company_id);
		
		$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name in($comapny_id) and variable_list=23 and is_deleted=0 and status_active=1");

 

		
		//echo $prod_reso_allo."eee";die;
		
		if($db_type==0)
		{
			$min_shif_start=return_field_value("min(TIME_FORMAT(d.prod_start_time, '%H:%i' ))  as line_start_time","prod_resource_mst a,prod_resource_dtls  b,prod_resource_dtls_time d ","a.id=d.mst_id and a.id=b.mst_id  and pr_date='$txt_date'","line_start_time");	
		}//and  a.company_id=$comapny_id and shift_id=1
		else if($db_type==2)
		{
			$min_shif_start=return_field_value("min(TO_CHAR(d.prod_start_time,'HH24:MI')) as line_start_time","prod_resource_mst a,prod_resource_dtls b,prod_resource_dtls_time d ","a.id=d.mst_id and a.id=b.mst_id and pr_date='$txt_date'","line_start_time");
		}//
		
		if($min_shif_start=="")
		{
			echo "<p style='font-size:20px', align='center'>No Line Engage for the selected Date.Please Check Actual Production Resource Entry.<p/>";die;
			
		}
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
		
		if(str_replace("'","",$cbo_company_id)==0) $company_name=""; else $company_name="and a.serving_company in(".str_replace("'","",$cbo_company_id).")";
		if(str_replace("'","",$cbo_location_id)==0) $location=""; else $location="and a.location=".str_replace("'","",$cbo_location_id)."";
		if(str_replace("'","",$cbo_floor_id)==0) $floor=""; else $floor="and a.floor_id=".str_replace("'","",$cbo_floor_id)."";
	    if(str_replace("'","",$hidden_line_id)==0) $line=""; else $line="and a.sewing_line in(".str_replace("'","",$hidden_line_id).")";
		if(str_replace("'","",$cbo_buyer_name)==0) $buyer=""; else $buyer="and b.buyer_name in(".str_replace("'","",$cbo_buyer_name).")";
		if(str_replace("'","",trim($txt_date))=="") $txt_date_from=""; else $txt_date_from=" and a.production_date='$txt_date'";
		
		if($db_type==0) $prod_start_cond="prod_start_time";
		else if($db_type==2) $prod_start_cond="TO_CHAR(prod_start_time,'HH24:MI')";
		
		$variable_start_time_arr='';
		$prod_start_time=sql_select("select $prod_start_cond as prod_start_time from variable_settings_production where company_name in($comapny_id) and variable_list=26 and status_active=1 and is_deleted=0 and shift_id=1");

		
		foreach($prod_start_time as $row)
		{
			$ex_time=explode(" ",$row[csf('prod_start_time')]);
			$variable_start_time_arr=$row[csf('prod_start_time')];
		}//die;
		unset($prod_start_time);
		$current_date_time=date('d-m-Y H:i');
		$ex_date_time=explode(" ",$current_date_time);
		$current_date=$ex_date_time[0];
		$current_time=$ex_date_time[1];
		$ex_time=explode(":",$current_time);
		
		$search_prod_date=change_date_format(str_replace("'","",$txt_date),'yyyy-mm-dd');
		$current_eff_min=($ex_time[0]*60)+$ex_time[1];
		
		$variable_time= explode(":",$variable_start_time_arr);
		$vari_min=($variable_time[0]*60)+$variable_time[1];
		$difa_time=explode(".",number_format(($current_eff_min-$vari_min)/60,2));//datediff("",$ctime,$variable_start_time_arr);
		$dif_time=$difa_time[0];
		$dif_hour_min=date("H:i", strtotime($dif_time));
		
		if($prod_reso_allo==1)
		{
			$prod_resource_array=array();
			$dataArray=sql_select("SELECT a.id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power, b.operator, b.helper, b.smv_adjust, b.smv_adjust_type, b.line_chief, b.target_per_hour, b.working_hour, c.from_date, c.to_date, c.capacity as mc_capacity from prod_resource_mst a, prod_resource_dtls b, prod_resource_dtls_mast c where a.id=c.mst_id and a.id=b.mst_id and c.id=b.mast_dtl_id and a.company_id in($comapny_id) and pr_date='$txt_date'");// and a.id=1 and c.from_date=$txt_date			
			
			foreach($dataArray as $val)
			{
				$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['man_power']=$val[csf('man_power')];
				$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['operator']=$val[csf('operator')];
				$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['helper']=$val[csf('helper')];
				$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['terget_hour']=$val[csf('target_per_hour')];
				$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['working_hour']=$val[csf('working_hour')];
				$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['tpd']=$val[csf('target_per_hour')]*$val[csf('working_hour')];
				$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['day_start']=$val[csf('from_date')];
				$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['day_end']=$val[csf('to_date')];
				$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['capacity']=$val[csf('mc_capacity')];
				$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['smv_adjust']=$val[csf('smv_adjust')];
				$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['smv_adjust_type']=$val[csf('smv_adjust_type')];
			}
		}
		// print_r($prod_resource_array);die;

		//***************************************************************************************************************

		if($db_type==0)
		{
			// $country_ship_date_fld="a.country_ship_date";
			$manufacturing_company=return_field_value("group_concat(comp.id) as company_id","lib_company as comp", "comp.core_business=1 and comp.status_active=1 and comp.is_deleted=0 $company_cond","company_id");
		}
		else
		{
			// $country_ship_date_fld="to_char(a.country_ship_date,'YYYY-MM-DD')";
			$manufacturing_company=return_field_value("listagg(comp.id,',') within group (order by comp.id) as company_id","lib_company comp", "comp.core_business=1 and comp.status_active=1 and comp.is_deleted=0 $company_cond","company_id");
		}
		
		$smv_source=return_field_value("smv_source","variable_settings_production","company_name in ($manufacturing_company) and variable_list=25 and status_active=1 and is_deleted=0");
		

		//print_r($prod_resource_array);
		if($db_type==2)
		{
			$pr_date=str_replace("'","",$txt_date);
			$pr_date_old=explode("-",str_replace("'","",$txt_date));
			$month=strtoupper($pr_date_old[1]);
			$year=substr($pr_date_old[2],2);
			$pr_date=$pr_date_old[0]."-".$month."-".$year;
		}
		else if($db_type==0)
		{
			$pr_date=str_replace("'","",$txt_date);
		}
		//echo $pr_date;die; 
		$prod_start_hour="08:00";
		$start_time=explode(":",$prod_start_hour);
		$hour=substr($start_time[0],1,1); $minutes=$start_time[1]; $last_hour=23;
		
		$prod_arr=array(); $start_hour_arr=array();
		$start_hour=$prod_start_hour;
		$start_hour_arr[$hour]=$start_hour;
		for($j=$hour; $j<$last_hour; $j++)
		{
			$start_hour=add_time($start_hour,60);
			$start_hour_arr[$j+1]=substr($start_hour,0,5);
		}
		//echo $pc_date_time;die;
		$start_hour_arr[$j+1]='23:59';
		
		
		$first_hour_time=explode(":",$min_shif_start);
		$hour_line=substr($first_hour_time[0],1,1); $minutes_one=$start_time[1];
		$line_start_hour_arr[$hour_line]=$min_shif_start;
		
		for($l=$hour_line;$l<$last_hour;$l++)
		{
			$min_shif_start=add_time($min_shif_start,60);
			$line_start_hour_arr[$l+1]=substr($min_shif_start,0,5);
		}
		
		$line_start_hour_arr[$j+1]='23:59';		
		
		
		/* ======================================================================
		/								Get Inhouse Data						 /
		/====================================================================== */
		$i=1; $grand_total_good=0; $grand_alter_good=0; $grand_total_reject=0;
		$html="";
		$floor_html="";
	    $check_arr=array();
		
		if($db_type==2)
		{
			$production_hour_subcon="TO_CHAR(hour,'HH24:MI')";
			$production_hour="TO_CHAR(production_hour,'HH24:MI')";
			
			$sql_query="SELECT  a.serving_company as company_id, a.location, a.floor_id, a.prod_reso_allo, a.production_date, a.sewing_line, b.buyer_name as buyer_name, b.style_ref_no,b.set_break_down, b.set_smv, a.po_break_down_id, a.item_number_id, c.po_number as po_number,c.unit_price,d.color_type_id,
				sum(d.production_qnty) as good_qnty";
				 
				$sql_query.=", sum(CASE WHEN $production_hour>='$start_hour_arr[$last_hour]' and $production_hour<='$start_hour_arr[24]' and d.production_type=5 THEN d.production_qnty else 0 END) AS good_23 
				
				FROM pro_garments_production_mst a,pro_garments_production_dtls d, wo_po_details_master b, wo_po_break_down c,wo_po_color_size_breakdown e
				where a.production_type=5 and d.production_type=5 and a.id=d.mst_id and   a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.po_break_down_id=e.po_break_down_id and d.color_size_break_down_id=e.id and b.job_no=e.job_no_mst and c.id=e.po_break_down_id and  a.status_active=1 and a.is_deleted=0 and c.is_deleted=0 and d.status_active=1 and c.is_deleted=0 and c.status_active in(1,2,3)  and e.status_active in(1,2,3) and e.is_deleted=0   $company_name $buyer $location $floor $line $txt_date_from 
				group by a.serving_company, a.location, a.floor_id, a.po_break_down_id, a.prod_reso_allo, a.production_date, a.sewing_line, b.buyer_name, b.style_ref_no, b.set_break_down,b.set_smv, a.item_number_id, c.po_number,c.unit_price,d.color_type_id order by a.location, a.floor_id, a.sewing_line";
				//echo $h;
		}
		 //echo $sql_query; die; 
 
		$sql=sql_select($sql_query);	
		$production_data_arr=array();$last_pro_hour=array();
		$production_po_data_arr=array();$all_style_arr=array();$style_wise_po_arr=array();
		$all_color_type_array = array();
		$production_color_type_data_arr=array();
		foreach($sql as $val)
		{
			

			
			if($val[csf('prod_reso_allo')]==1)
			{
				$sewing_line_ids=$prod_reso_arr[$val[csf('sewing_line')]];
				$sl_ids_arr = explode(",", $sewing_line_ids);
				$sewing_line_id = $sl_ids_arr[0]; // always 1st line id will take
			}
			else
			{
				$sewing_line_id=$val[csf('sewing_line')];
			}
			
			if($lineSerialArr[$sewing_line_id]=="")
			{
				$lastSlNo++;
				$slNo=$lastSlNo;
				$lineSerialArr[$sewing_line_id]=$slNo;
			}
			else $slNo=$lineSerialArr[$sewing_line_id];
			// echo $sewing_line_id."**".$lineSerialArr[$sewing_line_id]."**".$slNo."<br>";
			
			$production_data_arr2[$val[csf('po_break_down_id')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('item_number_id')]]+=$val[csf('good_qnty')];
			$production_data_arr3[$val[csf('po_break_down_id')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]=$val[csf('set_break_down')];


			$production_serial_arr[$floorArr[$val[csf('floor_id')]]][$slNo][$val[csf('sewing_line')]]=$val[csf('floor_id')];
			$set_ratio_arr=explode("__",$val[csf('set_break_down')]);
			
			$production_po_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]['qty']+=$val[csf('good_qnty')];
			$production_po_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]['unit_price']=$val[csf('unit_price')]/count($set_ratio_arr);


			$production_color_type_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('color_type_id')]][$val[csf('style_ref_no')]][$val[csf('item_number_id')]]['good_qnty']+=$val[csf('good_qnty')];



			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['prod_reso_allo']=$val[csf('prod_reso_allo')]; 
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_break_down_id'].=$val[csf('po_break_down_id')].","; 

			$all_color_type_array[$val[csf('floor_id')]][$val[csf('sewing_line')]]['color_type_id'] .= $val[csf('color_type_id')].","; 
			
			
			if($production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['set_break_down']!="")
			{
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['set_break_down'].=",".$val[csf('set_break_down')]; 
			}
			else
			{
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['set_break_down']=$val[csf('set_break_down')]; 
			}

			if($production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['buyer_name']!="")
			{
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['buyer_name'].=",".$val[csf('buyer_name')]; 
			}
			else
			{
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['buyer_name']=$val[csf('buyer_name')]; 
			}
			
			
			if($production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['set_smv']!="")
			{
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['set_smv'].=",".number_format($val[csf('set_smv')],2); 
			}
			else
			{
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['set_smv']=number_format($val[csf('set_smv')],2); 
			}
			
			if($production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_number']!="")
			{
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_number'].=",".$val[csf('po_number')]; 
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['style_ref'].=",".$val[csf('style_ref_no')]; 
			}
			else
			{
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_number']=$val[csf('po_number')];
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['style_ref']=$val[csf('style_ref_no')]; 
			}
			
			if($production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['item_number_id']!="")
			{
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['item_number_id'].="****".$val[csf('po_break_down_id')]."**".$val[csf('item_number_id')]."**".$val[csf('style_ref_no')]."**".$val[csf('color_type_id')]; 
			}
			else
			{
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['item_number_id']=$val[csf('po_break_down_id')]."**".$val[csf('item_number_id')]."**".$val[csf('style_ref_no')]."**".$val[csf('color_type_id')]; 
			}
			
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['quantity']+=$val[csf('good_qnty')];
			
			$all_po_id_arr[$val[csf('po_break_down_id')]]=$val[csf('po_break_down_id')];
			
			$all_style_arr[$val[csf('style_ref_no')]]=$val[csf('style_ref_no')];
			$style_wise_po_arr[$val[csf('style_ref_no')]][$val[csf('po_break_down_id')]]=$val[csf('po_break_down_id')];
		
		
				for($iii=1; $iii<=24; $iii++){
					if($iii<10){$iii='0'.$iii;}
					if(!empty($val[csf("good_".$iii)]))
					{
						$last_pro_hour[$val[csf('floor_id')].'**'.$val[csf('sewing_line')]][$iii]=$iii;
					}
				}
		
		}
		
	 	// echo "<pre>"; print_r($production_data_arr);die();
				
		foreach($last_pro_hour as $fl=>$ph){
			ksort($ph);
			list($f,$l)=explode('**',$fl);
			for($ni=9; $ni<=end($ph); $ni++)
			{
				if($ni != 13){
					$production_data_arr[$f][$l]['production_hour']+=1;
				}
			}
		}

 

		/* ======================================================================
		/								Get Subcon  Data						 /
		/====================================================================== */
					
		$sql_query_subcon="SELECT  a.company_id, a.location_id as location, a.floor_id, a.prod_reso_allo, a.production_date, a.line_id as sewing_line, b.party_id as buyer_name, c.cust_style_ref as style_ref_no, c.smv as set_smv, a.order_id as po_break_down_id, a.gmts_item_id as item_number_id , c.order_no as po_number,c.rate as unit_price,
		sum(d.prod_qnty) as good_qnty";
		$first=1;
		for($h=$hour;$h<$last_hour;$h++)
		{
			$bg=$start_hour_arr[$h];
			$end=substr(add_time($start_hour_arr[$h],60),0,5);
			$prod_hour="good_".substr($bg,0,2);
			if($first==1)
			{
				$sql_query_subcon.=", sum(CASE WHEN $production_hour_subcon<='$end' and d.production_type=2 THEN d.prod_qnty else 0 END) AS $prod_hour";
			}
			else
			{
				$sql_query_subcon.=", sum(CASE WHEN $production_hour_subcon>='$bg' and $production_hour_subcon<'$end' and d.production_type=2 THEN d.prod_qnty else 0 END) AS $prod_hour";
			}
			$first=$first+1;
		}
		$sql_query_subcon.=", sum(CASE WHEN $production_hour_subcon>='$start_hour_arr[$last_hour]' and $production_hour_subcon<='$start_hour_arr[24]' and d.production_type=2 THEN d.prod_qnty else 0 END) AS good_23 from subcon_gmts_prod_dtls a,subcon_gmts_prod_col_sz d, subcon_ord_mst b, subcon_ord_dtls c,subcon_ord_breakdown e
		where a.production_type=2 and d.production_type=2 and a.id=d.dtls_id and a.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0   and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0   and e.id=d.ord_color_size_id and e.order_id=c.id and a.order_id=e.order_id $company_name_subcon $location_subcon $floor $line_subcon   $txt_date_from 
		group by a.company_id, a.location_id, a.floor_id, a.order_id, a.prod_reso_allo, a.production_date, a.line_id, b.party_id, c.cust_style_ref, c.smv, a.gmts_item_id, c.order_no,c.rate order by a.location_id, a.floor_id, a.order_id"; 
		 //echo $sql_query_subcon;
		$sql_subcon=sql_select($sql_query_subcon);
		$last_pro_hour=array();
		$subcon_fob_calc = 0;
		foreach( $sql_subcon as $val)
		{

			
			//floor and line wise 
			if($val[csf('prod_reso_allo')]==1)
			{
				$sewing_line_ids=$prod_reso_arr[$val[csf('sewing_line')]];
				$sl_ids_arr = explode(",", $sewing_line_ids);
				$sewing_line_id = $sl_ids_arr[0]; // always 1st line id will take
			}
			else
			{
				$sewing_line_id=$val[csf('sewing_line')];
			}
			
			if($lineSerialArr[$sewing_line_id]=="")
			{
				$lastSlNo++;
				$slNo=$lastSlNo;
				$lineSerialArr[$sewing_line_id]=$slNo;
			}
			else $slNo=$lineSerialArr[$sewing_line_id];
			$production_serial_arr[$floorArr[$val[csf('floor_id')]]][$slNo][$val[csf('sewing_line')]]=$val[csf('floor_id')];
			
			$production_po_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]['qty']+=$val[csf('good_qnty')];
			$production_po_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]['unit_price']=$val[csf('unit_price')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['prod_reso_allo']=$val[csf('prod_reso_allo')]; 
			
			if($production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['buyer_name']!="")
			{
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['buyer_name'].=",".$val[csf('buyer_name')]; 
			}
			else
			{
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['buyer_name']=$val[csf('buyer_name')]; 
			}
			
			
			if($production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['set_smv']!="")
			{
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['set_smv'].=",".number_format($val[csf('set_smv')],2); 
			}
			else
			{
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['set_smv']=number_format($val[csf('set_smv')],2); 
			}
			
			if($production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['set_smv_subcon']!="")
			{
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['set_smv_subcon'].=",".number_format($val[csf('set_smv')],2); 
			}
			else
			{
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['set_smv_subcon']=number_format($val[csf('set_smv')],2); 
			}
			
			if($production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_number']!="")
			{
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_number'].=",".$val[csf('po_number')]; 
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['style_ref'].=",".$val[csf('style_ref_no')]; 
			}
			else
			{
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_number']=$val[csf('po_number')]; 
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['style_ref']=$val[csf('style_ref_no')]; 
			}
			
			if($production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['item_number_id']!="")
			{
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['item_number_id'].="****".$val[csf('po_break_down_id')]."**".$val[csf('item_number_id')].'**'."subcon**".$val[csf('style_ref_no')]; 
			}
			else
			{
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['item_number_id']=$val[csf('po_break_down_id')]."**".$val[csf('item_number_id')].'**'."subcon**".$val[csf('style_ref_no')]; 
			}

			$subcon_smv_calc[$val[csf('floor_id')]][$val[csf('sewing_line')]]+=$val[csf('set_smv')]*$val[csf('good_qnty')];
			$subcon_fob_calc[$val[csf('floor_id')]][$val[csf('sewing_line')]]+=$val[csf('unit_price')]*$val[csf('good_qnty')];

			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['quantity']+=$val[csf('good_qnty')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['1am']+=$val[csf('good_01')];  
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['2am']+=$val[csf('good_02')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['3am']+=$val[csf('good_03')]; 
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['4am']+=$val[csf('good_04')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['5am']+=$val[csf('good_05')]; 
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['6am']+=$val[csf('good_06')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['7am']+=$val[csf('good_07')]; 
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['8am']+=$val[csf('good_08')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['9am']+=$val[csf('good_09')]; 
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['10am']+=$val[csf('good_10')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['11am']+=$val[csf('good_11')]; 
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['12am']+=$val[csf('good_00')];
			
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['1pm']+=$val[csf('good_13')]; 
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['2pm']+=$val[csf('good_14')]; 
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['3pm']+=$val[csf('good_15')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['4pm']+=$val[csf('good_16')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['5pm']+=$val[csf('good_17')]; 
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['6pm']+=$val[csf('good_18')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['7pm']+=$val[csf('good_19')]; 
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['8pm']+=$val[csf('good_20')]; 
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['9pm']+=$val[csf('good_21')]; 
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['10pm']+=$val[csf('good_22')]; 
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['11pm']+=$val[csf('good_23')]; 
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['12pm']+=$val[csf('good_12')]; 

			$all_po_id_arr_sub[$val[csf('po_break_down_id')]]=$val[csf('po_break_down_id')];	
			
			$all_style_arr[$val[csf('style_ref_no')]]=$val[csf('style_ref_no')];
			$style_wise_po_arr[$val[csf('style_ref_no')]][$val[csf('po_break_down_id')]]=$val[csf('po_break_down_id')];

				for($iii=1; $iii<=24; $iii++){
					if($iii<10){$iii='0'.$iii;}
					if(!empty($val[csf("good_".$iii)]))
					{
						$last_pro_hour[$val[csf('floor_id')].'**'.$val[csf('sewing_line')]][$iii]=$iii;
					}
				}
		
		}
		
				
		foreach($last_pro_hour as $fl=>$ph)
		{
			ksort($ph);
			list($f,$l)=explode('**',$fl);
			for($ni=9; $ni<=end($ph); $ni++)
			{
				if($ni != 13){
					$production_data_arr[$f][$l]['production_hour']+=1;
				}
			}
		}

		
		
		
		$all_po_ids=implode(",", array_unique($all_po_id_arr));
 		$all_po_ids_subcon=implode(",", array_unique($all_po_id_arr_sub));
		$subcon_days_run_sql=sql_select("SELECT min(production_date) as min_date,order_id,line_id from subcon_gmts_prod_dtls where order_id in ($all_po_ids_subcon) and production_type=2 group by order_id,line_id");
		foreach ($subcon_days_run_sql as $key => $value) 
		{
			 $subcon_days_run_arr[$value[csf("order_id")]][$value[csf("line_id")]]=$value[csf("min_date")];
		}
		$days_run_sqls=sql_select("SELECT min(production_date) as min_date,po_break_down_id,sewing_line from pro_garments_production_mst where po_break_down_id in($all_po_ids)  and production_type=5 group by po_break_down_id,sewing_line");
 		foreach ($days_run_sqls as $key => $value) 
		{
			 $days_run_main_arr[$value[csf("po_break_down_id")]][$value[csf("sewing_line")]]=$value[csf("min_date")];
		}

		 // echo "<pre>";print_r($style_wise_po_arr);echo "</pre>";die;
		$item_smv_array2 =array();
		
		// echo $smv_source;
		if($smv_source=="") $smv_source=0; else $smv_source=$smv_source;
		if($smv_source==3)
		{			
			$style_nos=implode("','",$all_style_arr);
			$color_type_ids="'".implode("','",$color_type_array)."'";
			$gsdSql="SELECT a.id,a.APPLICABLE_PERIOD,a.BULLETIN_TYPE,A.TOTAL_SMV,A.STYLE_REF,a.GMTS_ITEM_ID,a.COLOR_TYPE  from PPL_GSD_ENTRY_MST a where a.APPLICABLE_PERIOD <= '$txt_date' and A.IS_DELETED=0 and A.STATUS_ACTIVE=1 and a.BULLETIN_TYPE=4  and A.STYLE_REF in('".$style_nos."') and a.APPROVED=1 
			group by a.id,a.APPLICABLE_PERIOD,a.BULLETIN_TYPE,A.TOTAL_SMV,A.STYLE_REF,a.GMTS_ITEM_ID,a.COLOR_TYPE
			 ORDER BY  a.GMTS_ITEM_ID ,a.APPLICABLE_PERIOD  DESC , a.id DESC"; //and a.BULLETIN_TYPE in(3,4)
			//echo $gsdSql; die;
			$gsdSqlResult = sql_select($gsdSql); 
			//$gsdDataArr=array();
			foreach($gsdSqlResult as $rows)
			{
				// echo $rows[TOTAL_SMV]."<br>";
				foreach($style_wise_po_arr[$rows[STYLE_REF]] as $po_id)
				{
					if($item_smv_array[$po_id][$rows[COLOR_TYPE]][$rows[GMTS_ITEM_ID]]=='')
					{
						$item_smv_array[$po_id][$rows[COLOR_TYPE]][$rows[GMTS_ITEM_ID]]=$rows[TOTAL_SMV];
					}
					if($item_smv_array2[$po_id][$rows[GMTS_ITEM_ID]]=='')
					{
						$item_smv_array2[$po_id][$rows[GMTS_ITEM_ID]]=$rows[TOTAL_SMV];
					}
				}
			}

			//print_r($item_smv_array2);die;
			
		}
		else
		{
			$sql_item="SELECT b.id, a.set_break_down, c.gmts_item_id, c.set_item_ratio, smv_pcs, smv_pcs_precost from wo_po_details_master a, wo_po_break_down b, wo_po_details_mas_set_details c where b.job_no_mst=a.job_no and b.job_no_mst=c.job_no   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active in(1,2,3) and b.id in($all_po_ids)"; //echo $sql_item;die;
			$resultItem=sql_select($sql_item);
			foreach($resultItem as $itemData)
			{
				if($smv_source==1)
				{
					$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('smv_pcs')];
				}
				else if($smv_source==2)
				{
					$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('smv_pcs_precost')];
				}
			}
		}
	 
		
			
		$before_8_am=$production_hour1=$production_hour2=$production_hour3=$production_hour4=$production_hour5=$production_hour6=$production_hour7=$production_hour8=0;   
	    $production_hour9=$production_hour10=$production_hour11=$production_hour12=$production_hour13=$production_hour14=$production_hour15=$production_hour16=$avable_min=0;
		$production_hour17=$production_hour18=$production_hour19=$production_hour20=$production_hour21=$production_hour22=$production_hour23=$production_hour24=$today_product=0;
		$floor_hour1=$floor_hour2=$floor_hour3=$floor_hour4=$floor_hour5=$floor_hour6==$floor_hour7=$floor_hour8=$floor_before_9am=0;  $floor_name="";   
	    $floor_hour9=$floor_hour10=$floor_hour11=$floor_hour12=$floor_hour13=$floor_hour14=$floor_hour15=$floor_hour16=$floor_man_power=0;
		$floor_hour17=$floor_hour18=$floor_hour19=$floor_hour20=$floor_hour21=$floor_hour22=$floor_hour23=$floor_hour24=$floor_operator=$floor_produc_min=0;
		$floor_smv=$floor_row=$floor_helper=$floor_tgt_h=$floor_days_run=$floor_before_8_am=$floor_working_hour=$floor_ttl_tgt=$floor_today_product=$floor_fob_val=$floor_avale_minute=0;
		$total_hour1=$total_hour2=$total_hour3=$total_hour4=$total_hour5=$total_hour6==$total_hour7=$total_hour8=$total_before_8am=$total_operator=$total_helper=$gnd_hit_rate=0;   
	    $total_hour9=$total_hour10=$total_hour11=$total_hour12=$total_hour13=$total_hour14=$total_hour15=$total_hour16=$total_smv=$total_terget=$grand_total_product=$gnd_line_effi=0;
		$total_hour17=$total_hour18=$total_hour19=$total_hour20=$total_hour21=$total_hour22=$total_hour23=$total_hour24=$total_man_power=$gnd_avable_min=$gnd_product_min=0;
		$item_smv=$item_smv_total=$line_efficiency=$days_run=$total_working_hour=$gnd_total_tgt_h=$total_capacity=0;
		$j=1;
		ob_start();
		$line_number_check_arr=array();
		$smv_for_item="";
		ksort($production_serial_arr); 
		foreach($production_serial_arr as $f_name=>$fname)
		{
			ksort($fname);
			foreach ($fname as $line_sl => $sl_data) 
			{				
				// ksort($sl_data);
				foreach($sl_data as $l_id=>$ldata)
				{
					$f_id = $ldata;
					if($i!=1)
					{
						if(!in_array($f_id, $check_arr))
						{
							 
							$floor_name=""; $floor_smv=0; $floor_row=0; $floor_operator=0; $floor_helper=0; $floor_tgt_h=0; $floor_man_power=0; $floor_days_run=0; $floor_before_9_am=0;
							$floor_hour9=0; $floor_hour10=0; $floor_hour11=0; $floor_hour12=0; $floor_hour13=0; $floor_hour14=0; $floor_hour15=0; $floor_hour16=0; $floor_hour17=0; $floor_hour18=0; $floor_hour19=0; $floor_hour20=0; $floor_hour21=0; $floor_hour22=0; $floor_hour23=0; $floor_hour24=0;
							$floor_working_hour=0; $floor_ttl_tgt=0; $floor_today_product=0;$floor_fob_val; $floor_avale_minute=0; $floor_produc_min=0; $floor_efficency=0; $floor_man_power=0; $floor_capacity=0;
							$j++;
						}
					}
					$floor_row++;	
				
					$po_number=array_unique(explode(',',$row[csf('po_number')]));
					$germents_item=array_unique(explode('****',$production_data_arr[$f_id][$l_id]['item_number_id']));
					$buyer_neme_all=array_unique(explode(',',$production_data_arr[$f_id][$l_id]['buyer_name']));
					$style_ref=implode(',',array_unique(explode(',',$production_data_arr[$f_id][$l_id]['style_ref'])));
					$po_break_down_id=implode(',',array_unique(explode(',',$production_data_arr[$f_id][$l_id]['po_break_down_id'])));
					$color_type_arr = array_unique(explode(",", chop($all_color_type_array[$f_id][$l_id]['color_type_id'],',')));

					$set_smv_all="";
					$itm_smv_arr = array();
					$chk_smv_array = array();
					$produce_minit = 0;
					$production_color_type_chk_arr = array();
					foreach ($germents_item as $value) 
					{
						$po_item = explode("**", $value);
						$po_id = $po_item[0];
						$item_id = $po_item[1];
						$style = $po_item[2];
						$color_type_id = $po_item[3];
						$qty = 0;
						if($production_color_type_chk_arr[$f_id][$l_id][$color_type_id][$style][$item_id]=="")
						{
							$qty = $production_color_type_data_arr[$f_id][$l_id][$color_type_id][$style][$item_id]['good_qnty'];
							$production_color_type_chk_arr[$f_id][$l_id][$color_type_id][$style][$item_id] = 1;
						}
		
						$color_type_smv = ($item_smv_array[$po_id][$color_type_id][$item_id]!="") ? $item_smv_array[$po_id][$color_type_id][$item_id] : $item_smv_array2[$po_id][$item_id];

					


						if($production_data_arr2[$po_id][$f_id][$l_id][$item_id] !="")
						{
							$break_down_smv = $production_data_arr3[$po_id][$f_id][$l_id];
							$break_down_smv_arr = explode("__", $break_down_smv);

							foreach ($break_down_smv_arr as $smv) 
							{
								$set_smv = explode("_", $smv);


								if($item_id==$set_smv[0])
								{ 
									if(!isset($chk_smv_array[$style][$color_type_id][$item_id]))
									{
										//
										$set_smv[2] = ($color_type_smv !="")?$color_type_smv:$set_smv[2];

										
										$produce_minit += ($qty*$set_smv[2]);
							 
										$chk_smv_array[$style][$color_type_id][$item_id] = $set_smv[2];
										echo $item_id.'*'. $qty.'*'.$set_smv[2].'='.$color_type_smv."<br>";
									}
									
								}
								
							}

						}	
					}
					
				

					$po_break_down_id = explode(",", $po_break_down_id);
					foreach (array_filter($po_break_down_id) as $po) 
					{
						if(!empty($itm_smv_arr[$po][$f_id][$l_id])){
							$set_smv_all .= number_format($itm_smv_arr[$po][$f_id][$l_id],2).",";
						}
					}
					
					$subcontact_smvs = explode(",", $production_data_arr[$f_id][$l_id]['set_smv_subcon']);
					foreach (array_filter($subcontact_smvs) as $set_smv_subcon) 
					{
						$set_smv_all .= $set_smv_subcon.",";
					}


					$buyer_name="";
					foreach($buyer_neme_all as $buy)
					{
						if($buyer_name!='') $buyer_name.=',';
						$buyer_name.=$buyerArr[$buy];
					}
					$chk_item_array = array();$garment_item_id_arr = array();
					$garment_itemname=''; $item_smv=""; $smv_for_item="";  
					$order_no_total="";$days_run="";
					$prod_fob_val = 0;
					foreach($germents_item as $g_val)
					{
						$po_garment_item=explode('**',$g_val);
						if($po_garment_item[2]=="subcon" )
							{  
								 
								 $sewing_day=$subcon_days_run_arr[$po_garment_item[0]][$l_id];

							}
							else
							{  
								$sewing_day=$days_run_main_arr[$po_garment_item[0]][$l_id];
							}

							if($sewing_day!="")
							{ 
								if($days_run=="")
								{  
									  $days_run .=datediff("d",$sewing_day,$pr_date);
								}
								else
								{
									  
									 $days_run .=', '.datediff("d",$sewing_day,$pr_date);
								}
								
							}
						
						
						if(!isset($chk_item_array[$style_ref][$po_garment_item[1]]))
						{	
							if($garment_itemname!=''){$garment_itemname.=',';}
								$garment_itemname.=$garments_item[$po_garment_item[1]];
								$garment_item_id_arr[$po_garment_item[1]]=$po_garment_item[1];
								
						}
						$chk_item_array[$style_ref][$po_garment_item[1]] = $po_garment_item[1];
						foreach ($color_type_arr as $color_type_key2 => $value2) 
						{
							if($item_smv!='') $item_smv.='/';
								$item_smv.=$item_smv_array[$po_garment_item[0]][$value2][$po_garment_item[1]];


							if($smv_for_item!="") 
								$smv_for_item.="****".$po_garment_item[0]."**".$item_smv_array[$po_garment_item[0]][$value2][$po_garment_item[1]];
							else
								$smv_for_item=$po_garment_item[0]."**".$item_smv_array[$po_garment_item[0]][$value2][$po_garment_item[1]];

							if($temp_data_arr[$f_id][$l_id][$po_garment_item[0]]==''){


							$prod_fob_val += $production_po_data_arr[$f_id][$l_id][$po_garment_item[0]]['unit_price']*$production_po_data_arr[$f_id][$l_id][$po_garment_item[0]]['qty'];

							$temp_data_arr[$f_id][$l_id][$po_garment_item[0]]=1;
						}
						}
							
						if($order_no_total!="") $order_no_total.=",";
							$order_no_total.=$po_garment_item[0];

					}
					$produce_minit+=$subcon_smv_calc[$f_id][$l_id];
					$prod_fob_val+=$subcon_fob_calc[$f_id][$l_id];
					
				

					$sewing_line='';
					if($production_data_arr[$f_id][$l_id]['prod_reso_allo']==1)
					{
						$line_number=explode(",",$prod_reso_arr[$l_id]);
						foreach($line_number as $val)
						{
							if($sewing_line=='') $sewing_line=$lineArr[$val]; else $sewing_line.=",".$lineArr[$val];
						}
					}
					else $sewing_line=$lineArr[$l_id];
				
					$total_eff_hour=0;
					 
					$production_hour_all=$production_data_arr[$f_id][$l_id]['quantity']; if($production_hour24!=0) $total_eff_hour+=1;
				
				
					if($total_eff_hour>$prod_resource_array[$l_id][$pr_date]['working_hour'])
					{
						$total_eff_hour=$prod_resource_array[$l_id][$pr_date]['working_hour'];
					}
					$before_8_am=$production_hour1+$production_hour2+$production_hour3+$production_hour4+$production_hour5+$production_hour6+$production_hour7+$production_hour8;//$before_8_am+
					$today_product=$production_hour9+$production_hour10+$production_hour11+$production_hour12+$production_hour13+$production_hour14+$production_hour15+$production_hour16+$production_hour17+$production_hour18+$production_hour19+$production_hour20+$production_hour21+$production_hour22+$production_hour23+$production_hour24;
					$today_product=$production_hour_all;
					
				
					
					$current_wo_time=0;
					if($current_date==$search_prod_date)
					{
						$prod_wo_hour=$prod_resource_array[$l_id][$pr_date]['working_hour'];
						
						if ($dif_time<$prod_wo_hour)//
						{
							$current_wo_time=$dif_hour_min;
							$cla_cur_time=$dif_time;
						}
						else
						{
							$current_wo_time=$prod_wo_hour;
							$cla_cur_time=$prod_wo_hour;
						}
					}
					else
					{
						$current_wo_time=$prod_resource_array[$l_id][$pr_date]['working_hour'];
						$cla_cur_time=$prod_resource_array[$l_id][$pr_date]['working_hour'];
					}
					
					
					 //effiecency***************************['']
					$smv_adjustmet_type=$prod_resource_array[$l_id][$pr_date]['smv_adjust_type'];
					
					$eff_target=($prod_resource_array[$l_id][$pr_date]['terget_hour']*$prod_resource_array[$l_id][$pr_date]['working_hour']);
					
					 	$total_adjustment=0;
						if(str_replace("'","",$smv_adjustmet_type)==1)
						{ 
							$total_adjustment=$prod_resource_array[$l_id][$pr_date]['smv_adjust'];
						}
						else if(str_replace("'","",$smv_adjustmet_type)==2)
						{
							$total_adjustment=($prod_resource_array[$l_id][$pr_date]['smv_adjust'])*(-1);
						}
					 
					
					$efficiency_min=$total_adjustment+($prod_resource_array[$l_id][$pr_date]['man_power'])*$cla_cur_time*60;
				
				
 				
				
					$line_efficiency=(($produce_minit)*100)/$efficiency_min;
					 
					$floor_name=$floorArr[$f_id];	
					 
					$floor_before_8_am+=$before_8_am;
					$floor_smv+=$item_smv;
					$floor_capacity+=$prod_resource_array[$l_id][$pr_date]['capacity'];
					$floor_operator+=$prod_resource_array[$l_id][$pr_date]['operator'];
					$floor_helper+=$prod_resource_array[$l_id][$pr_date]['helper'];
					$floor_tgt_h+=$prod_resource_array[$l_id][$pr_date]['terget_hour'];	
					$floor_days_run+=$days_run;
					$floor_working_hour+=$prod_resource_array[$l_id][$pr_date]['working_hour']; 
					$floor_ttl_tgt+=$eff_target;
					$floor_today_product+=$today_product;
					$floor_fob_val+=$prod_fob_val;
					$floor_avale_minute+=$efficiency_min;
					$floor_produc_min+=$produce_minit; 
					$floor_efficency=($floor_produc_min/$floor_avale_minute)*100;
					$floor_man_power+=$prod_resource_array[$l_id][$pr_date]['man_power'];
					
					
					//**************************** calclution total ********************************************************************
					 
					$total_before_8am+=$before_8_am;
					$total_capacity+=$prod_resource_array[$l_id][$pr_date]['capacity'];
					$gnd_total_tgt_h+=$prod_resource_array[$l_id][$pr_date]['terget_hour'];	
					$total_working_hour+=$prod_resource_array[$l_id][$pr_date]['working_hour']; 
					$total_operator+=$prod_resource_array[$l_id][$pr_date]['operator'];
					$total_helper+=$prod_resource_array[$l_id][$pr_date]['helper'];
					$total_man_power+=$prod_resource_array[$l_id][$pr_date]['man_power'];
					//$total_smv+=$item_smv;
					$total_terget+=$eff_target;
					$grand_total_product+=$today_product;
					$grand_total_fob_val+=$prod_fob_val;
					$gnd_avable_min+=$efficiency_min;
					$gnd_product_min+=$produce_minit; 
					//$gnd_hit_rate=($grand_total_product/$total_terget)*100;
					//$gnd_line_effi=($gnd_product_min/$gnd_avable_min)*100;

					$color_type_id = chop($all_color_type_array[$f_id][$l_id]['color_type_id'],',');
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					
					 
					 
					 
					$i++;
					$check_arr[]=$f_id;
				}
			}
		}
		 
	 
		 
	    $smv_for_item="";
		?>



		<table id="table_header_2" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
	            <thead>
	                <tr height="50">
	                    <th width="40">SL</th>
	                    <th width="80">Floor Name</th>
	                    <th width="70">Hourly Terget</th>
	                    <th width="70">Capacity</th>
	                    <th width="60">Total Man  Power</th>
	                    <th width="70">Operator</th>
	                    <th width="50">Helper</th>
	                    <th width="60">Line Hour</th>
	                    <th width="80">Day Target</th>
	                    <th width="80">Total Prod.</th>
	                    <th width="80">Production FOB value</th>
	                    <th width="80">Variance </th>
	                    <th width="100">SMV Available</th>
	                    <th width="100">SMV Achieved</th>
	                    <th width="90">Achievement %</th>
	                    <th width="90">Floor Eff. %</th>
	                    
	                     
	                </tr>
	            </thead>
	  
	                <tfoot>
	                    <tr>
	                        <th width="40">&nbsp;</th>
	                        <th width="80">Total</th>
	                        <th width="70"><? echo $gnd_total_tgt_h;   ?> </th>
	                        <th width="70" align="right"><? echo $total_capacity; ?> </th>
	                        <th width="60"><? echo $total_man_power; ?></th>
	                        <th width="70"><? echo $total_operator; ?></th>
	                        <th width="50"><? echo $total_helper; ?></th>
	                        <th align="right" width="60"><? echo number_format($total_working_hour,2); ?></th>
	                        <th align="right" width="80"><? echo number_format($total_terget,0); ?></th>
	                        <th align="right" width="80"><? echo number_format($grand_total_product,0); ?></th>
	                        <th align="right" width="80"><? echo number_format($grand_total_fob_val,0); ?></th>
	                        <th align="right" width="80"><? echo number_format($grand_total_product-$total_terget,0); ?></th>
	                        <th align="right" width="100"><? echo $gnd_avable_min; ?></th>
	                        <th align="right" width="100"><? echo number_format($gnd_product_min,2); ?></th>
	                        <th align="right" width="90"><? echo number_format(($grand_total_product/$total_terget)*100,2)."%"; ?></th>
	                        <th align="center" width="90" title="<?= $gnd_avable_min;?>"><?  echo number_format(($gnd_product_min/$gnd_avable_min)*100,2)."%"; ?></th>
	                         
	                    </tr>
	                </tfoot>
	            </table>
	  
 