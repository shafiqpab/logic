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
	echo create_drop_down( "cbo_location", 100, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-Select Location-", $selected, "load_drop_down( 'requires/date_wise_production_report_controller', this.value, 'load_drop_down_floor', 'floor_td' );",0 );     					
	exit();
}
	
if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor", 100, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' order by floor_name","id,floor_name", 1, "-Select Floor-", $selected, "",0 );  
	exit();   	 
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 110, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-Select Buyer-", $selected, "load_drop_down( 'requires/order_wise_production_report_controller', this.value, 'load_drop_down_season', 'season_td');" );     	 
	exit();
}

if ($action=="load_drop_down_team_member")
{
	echo create_drop_down( "cbo_team_member", 100, "select id,team_member_name from lib_mkt_team_member_info where team_id='$data' and status_active=1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-Select Member-", $selected, "" ); 
	exit();  	 
}

if ($action=="load_drop_down_agent")
{
	echo create_drop_down( "cbo_agent", 90, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company='$data' and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (20,21))  order by buyer_name","id,buyer_name", 1, "-Agent-", $selected, "" );  
	exit(); 	 
} 

if ($action=="load_drop_down_season")
{
	echo create_drop_down( "cbo_season_name", 70, "select id, season_name from lib_buyer_season where buyer_id='$data' and status_active =1 and is_deleted=0 order by season_name ASC","id,season_name", 1, "-Season-", "", "" );
	exit();
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$tna_process_type=return_field_value("tna_process_type"," variable_order_tracking","variable_list=31 and company_name=".$cbo_company_name.""); 

	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
 	$buyer_short_library=return_library_array( "select id,short_name from lib_buyer", "id", "short_name");
 	$location_library=return_library_array( "select id,location_name from lib_location", "id", "location_name"); 
	$floor_library=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name"); 
 	$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name");
	$team_name_arr=return_library_array( "select id,team_name from lib_marketing_team", "id", "team_name");
	$team_member_arr=return_library_array( "select id,team_member_name from lib_mkt_team_member_info", "id", "team_member_name");
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where file_type=1 and form_name='knit_order_entry'",'master_tble_id','image_location');

	$shipping_status=str_replace("'","",$shipping_status);
	$cbo_order_status=str_replace("'","",$cbo_order_status);
	$garments_nature=str_replace("'","",$cbo_garments_nature);
	if($garments_nature==1) $garments_nature="";
	$cbo_season_name = str_replace("'","",$cbo_season_name);
	$type = str_replace("'","",$cbo_type);
		
	if(str_replace("'","",$txt_item_catgory)==0) $item_cat_cond=""; else $item_cat_cond=" and b.product_category=$txt_item_catgory";
	if(str_replace("'","",$cbo_company_name)==0) $company_name=""; else $company_name=" and b.company_name=$cbo_company_name";
	
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_name=" and b.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_name="";
		}
		else
		{
			$buyer_name="";
		}
	}
	else
	{
		$buyer_name=" and b.buyer_name=$cbo_buyer_name";
	}

	if($cbo_order_status!=0) $order_status_cond=" and a.is_confirmed=$cbo_order_status";else $order_status_cond="";
	
	if(str_replace("'","",$cbo_team_name)==0) $team_name_cond="";else $team_name_cond=" and b.team_leader=$cbo_team_name";
	if(str_replace("'","",$cbo_team_member)==0) $team_member_cond="";else $team_member_cond=" and b.dealing_marchant=$cbo_team_member";
	if(str_replace("'","",$cbo_agent)==0) $agent_cond=""; else $agent_cond=" and b.agent_name=$cbo_agent";
	
	if(str_replace("'","",$cbo_location)==0) $location=""; else $location=" and c.location=$cbo_location";
	if(str_replace("'","",$cbo_floor)==0) $floor=""; else $floor=" and c.floor_id=$cbo_floor";
	
	
	
	$cbo_string_search_type=str_replace("'","",$cbo_string_search_type);
	if($cbo_string_search_type==1)
	{
		if(str_replace("'","",trim($txt_order_no))!="") $search_string="".str_replace("'","",trim($txt_order_no)).""; else $search_string="%%";
		if(str_replace("'","",trim($txt_file_no))!="") $file_no=" and LOWER(a.file_no) = LOWER('".str_replace("'","",trim($txt_file_no))."')"; else $file_no="";
		if(str_replace("'","",trim($txt_ref_no))!="") $ref_no=" and LOWER(a.grouping) = LOWER('".str_replace("'","",trim($txt_ref_no))."')"; else $ref_no="";
	}
	else if($cbo_string_search_type==4 || $cbo_string_search_type==0)
	{
		if(str_replace("'","",trim($txt_order_no))!="") $search_string="%".str_replace("'","",trim($txt_order_no))."%"; else $search_string="%%";
		if(str_replace("'","",trim($txt_file_no))!="") $file_no=" and LOWER(a.file_no) like LOWER('%".str_replace("'","",trim($txt_file_no))."%')"; else $file_no="";
		if(str_replace("'","",trim($txt_ref_no))!="") $ref_no=" and LOWER(a.grouping) like LOWER('%".str_replace("'","",trim($txt_ref_no))."%')"; else $ref_no="";
	}
	else if($cbo_string_search_type==2)
	{
		if(str_replace("'","",trim($txt_order_no))!="") $search_string="".str_replace("'","",trim($txt_order_no))."%"; else $search_string="%%";
		if(str_replace("'","",trim($txt_file_no))!="") $file_no=" and LOWER(a.file_no) like LOWER('".str_replace("'","",trim($txt_file_no))."%')"; else $file_no="";
		if(str_replace("'","",trim($txt_ref_no))!="") $ref_no=" and LOWER(a.grouping) like LOWER('".str_replace("'","",trim($txt_ref_no))."%')"; else $ref_no="";
	}
	else if($cbo_string_search_type==3)
	{
		if(str_replace("'","",trim($txt_order_no))!="") $search_string="%".str_replace("'","",trim($txt_order_no)).""; else $search_string="%%";
		if(str_replace("'","",trim($txt_file_no))!="") $file_no=" and LOWER(a.file_no) like LOWER('%".str_replace("'","",trim($txt_file_no))."')"; else $file_no="";
		if(str_replace("'","",trim($txt_ref_no))!="") $ref_no=" and LOWER(a.grouping) like LOWER('%".str_replace("'","",trim($txt_ref_no))."')"; else $ref_no="";
	}
	/*if(str_replace("'","",trim($txt_order_no))!="") $search_string="%".str_replace("'","",trim($txt_order_no))."%"; else $search_string="%%";
	if(str_replace("'","",trim($txt_file_no))!="") $file_no=" and LOWER(a.file_no) like LOWER('%".str_replace("'","",trim($txt_file_no))."%')"; else $file_no="";
	if(str_replace("'","",trim($txt_ref_no))!="") $ref_no=" and LOWER(a.grouping) like LOWER('%".str_replace("'","",trim($txt_ref_no))."%')"; else $ref_no="";
*/	
	if(str_replace("'","",trim($txt_date_from))=="" || str_replace("'","",trim($txt_date_to))=="") $txt_date="";
	else $txt_date=" and a.pub_shipment_date between $txt_date_from and $txt_date_to";
	
	if(str_replace("'","",trim($txt_date_from))=="" || str_replace("'","",trim($txt_date_to))=="") $country_ship_date="";
	else $country_ship_date=" and c.country_ship_date between $txt_date_from and $txt_date_to";
	$cbo_year=str_replace("'","",$cbo_year);
	$year_cond="";

	if($type==5)
	{
		if($cbo_string_search_type==1)
		{
			if(str_replace("'","",trim($txt_order_no))!="") $style_cond="and LOWER(b.style_ref_no) = LOWER('$search_string')"; else $style_cond="";
		}
		else if($cbo_string_search_type==4 || $cbo_string_search_type==0)
		{
			if(str_replace("'","",trim($txt_order_no))!="") $style_cond="and LOWER(b.style_ref_no) like LOWER('".$search_string."')"; else $style_cond="";
		}
		else if($cbo_string_search_type==2)
		{
			if(str_replace("'","",trim($txt_order_no))!="") $style_cond="and LOWER(b.style_ref_no) like LOWER('".$search_string."')"; else $style_cond="";
			
		}
		else if($cbo_string_search_type==3)
		{
			if(str_replace("'","",trim($txt_order_no))!="") $style_cond="and LOWER(b.style_ref_no) like LOWER('".$search_string."')"; else $style_cond="";
		}
		//if(str_replace("'","",trim($txt_order_no))!="") $style_cond="and LOWER(b.style_ref_no) like LOWER('$search_string')"; else $style_cond="";
	}
	else if($type==7)
	{
		if($cbo_string_search_type==1)
		{
			if(str_replace("'","",trim($txt_order_no))!="") $style_cond="and b.job_no_prefix_num=$txt_order_no"; else $style_cond="";
		}
		else if($cbo_string_search_type==4 || $cbo_string_search_type==0)
		{
			if(str_replace("'","",trim($txt_order_no))!="") $style_cond="and  LOWER(b.job_no_prefix_num) like LOWER('%".str_replace("'","",trim($txt_order_no))."%')"; else $style_cond="";
		}
		else if($cbo_string_search_type==2)
		{
			if(str_replace("'","",trim($txt_order_no))!="") $style_cond="and  LOWER(b.job_no_prefix_num) like LOWER('".str_replace("'","",trim($txt_order_no))."%')"; else $style_cond="";
		}
		else if($cbo_string_search_type==3)
		{
			if(str_replace("'","",trim($txt_order_no))!="") $style_cond="and  LOWER(b.job_no_prefix_num) like LOWER('%".str_replace("'","",trim($txt_order_no))."')"; else $style_cond="";
		}
		
		//if(str_replace("'","",trim($txt_order_no))!="") $style_cond="and b.job_no_prefix_num=$txt_order_no"; else $style_cond="";
	}
	if($cbo_year!=0)
	{
		if($db_type==0) $year_cond="and year(b.insert_date)='$cbo_year'"; 
		else if($db_type==2) $year_cond="and to_char(b.insert_date,'YYYY')='$cbo_year'";	
	}
	
	$fromDate = change_date_format( str_replace("'","",trim($txt_date_from)) );
	$toDate = change_date_format( str_replace("'","",trim($txt_date_to)));
	if($cbo_season_name!=0) $season_cond="and b.season_matrix='$cbo_season_name'"; else $season_cond="";
	ob_start(); 
	
    if($type==1) //order wise
    {
		$orderWiseDataArr=array(); $ship_status_cond=""; $poIds='';
		if($shipping_status!=0) $ship_status_cond=" and a.shiping_status=$shipping_status";
		if($db_type==0){$select_job_year="year(b.insert_date) as job_year";} else{ $select_job_year="to_char(b.insert_date,'YYYY') as job_year";}
		 
	 	$order_sql="select a.is_confirmed, a.insert_date as po_insert_date, a.id, b.job_no_prefix_num, $select_job_year , b.job_no, b.team_leader, b.dealing_marchant, b.agent_name, a.po_number, a.po_quantity, a.unit_price, a.pub_shipment_date as shipment_date, a.shiping_status, a.excess_cut, a.plan_cut, a.file_no, a.grouping, a.details_remarks, b.company_name, b.buyer_name, b.set_break_down, b.style_ref_no, b.total_set_qnty as ratio, c.smv_pcs, c.gmts_item_id from wo_po_details_master b, wo_po_break_down a,wo_po_details_mas_set_details c where a.job_no_mst=b.job_no and b.job_no=c.job_no and a.status_active=1 and a.is_deleted=0 and LOWER(a.po_number) like LOWER('$search_string') and b.status_active=1 and b.is_deleted=0 $txt_date $company_name $buyer_name $season_cond $garmentsNature $team_name_cond $team_member_cond $file_no $ref_no $ship_status_cond $agent_cond $order_status_cond $item_cat_cond  order by a.pub_shipment_date, b.job_no_prefix_num, a.id";
		
		//$order_sql="select a.id, b.job_no_prefix_num, $select_job_year , b.job_no, b.team_leader, b.dealing_marchant, b.agent_name, a.po_number, a.po_quantity, a.unit_price, a.pub_shipment_date as shipment_date, a.shiping_status, a.excess_cut, a.plan_cut, a.file_no, a.grouping, a.details_remarks, b.company_name, b.buyer_name, b.set_break_down, b.style_ref_no, b.total_set_qnty as ratio from wo_po_details_master b, wo_po_break_down a where a.job_no_mst=b.job_no  and a.status_active=1 and a.is_deleted=0 and LOWER(a.po_number) like LOWER('$search_string') and b.status_active=1 and b.is_deleted=0 $txt_date $company_name $buyer_name $garmentsNature $team_name_cond $team_member_cond $file_no $ref_no $ship_status_cond $agent_cond $order_status_cond order by a.pub_shipment_date, b.job_no_prefix_num, a.id";
		 
		 
		 
		  //echo $order_sql;
		$result=sql_select($order_sql);
		//echo count($result)."Fuad";
		if(count($result)<1)
		{
			echo "<div style='width:1000px;' align='center'><font color='#FF0000'>No Data Found</font></div>"; die;
		}
		
		$tot_rows=0;
		foreach($result as $orderRes)
		{
			$tot_rows++;
			//$poIds.=$orderRes[csf("id")].",";
			$poIds[$orderRes[csf("id")]]=$orderRes[csf("id")];
			$orderWiseDataArr[$orderRes[csf("id")]]=$orderRes[csf("job_no_prefix_num")]."##".$orderRes[csf("job_no")]."##".$orderRes[csf("team_leader")]."##".$orderRes[csf("dealing_marchant")]."##".$orderRes[csf("agent_name")]."##".$orderRes[csf("po_number")]."##".$orderRes[csf("po_quantity")]."##".$orderRes[csf("unit_price")]."##".$orderRes[csf("shipment_date")]."##".$orderRes[csf("shiping_status")]."##".$orderRes[csf("excess_cut")]."##".$orderRes[csf("plan_cut")]."##".$orderRes[csf("file_no")]."##".$orderRes[csf("grouping")]."##".$orderRes[csf("company_name")]."##".$orderRes[csf("buyer_name")]."##".$orderRes[csf("set_break_down")]."##".$orderRes[csf("style_ref_no")]."##".$orderRes[csf("ratio")]."##".$orderRes[csf("job_year")]."##".$orderRes[csf("details_remarks")]."##".$orderRes[csf("po_insert_date")]."##".$orderRes[csf("is_confirmed")];
		
		$smvArr[$orderRes[csf("job_no")].$orderRes[csf("gmts_item_id")]]=$orderRes[csf("smv_pcs")];
		}
		
		unset($result);
		//$poIds=chop($poIds,','); 
		$poIds=implode(',',$poIds); 
		$poIds_cond="";
		if($db_type==2 && $tot_rows>1000)
		{
			$poIds_cond=" and (";
			$poIdsArr=array_chunk(explode(",",$poIds),999);
			foreach($poIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$poIds_cond.=" po_break_down_id in($ids) or ";
			}
			$poIds_cond=chop($poIds_cond,'or ');
			$poIds_cond.=")";
		}
		else
		{
			$poIds_cond=" and po_break_down_id in($poIds)";
		}
		
		//echo $poIds_cond;
		//echo $poIds;
		$ex_factory_arr=array();
		$ex_factory_data=sql_select("select po_break_down_id, item_number_id, MAX(ex_factory_date) AS ex_factory_date,sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END)-sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) AS ex_factory_qnty from pro_ex_factory_mst where status_active=1 and is_deleted=0 $poIds_cond group by po_break_down_id, item_number_id");
		foreach($ex_factory_data as $exRow)
		{
			$ex_factory_arr[$exRow[csf('po_break_down_id')]][$exRow[csf('item_number_id')]]['date']=$exRow[csf('ex_factory_date')];
			$ex_factory_arr[$exRow[csf('po_break_down_id')]][$exRow[csf('item_number_id')]]['qty']=$exRow[csf('ex_factory_qnty')];
		}
		
		
		
		
		if($db_type==2)
		{
			$poIds_cond_approval=" and (";
			$poIdsArrs=array_chunk(explode(",",$poIds),999);
			foreach($poIdsArrs as $ids)
			{
				$idss=implode(",",$ids);
				$poIds_cond_approval.=" po_break_down_id in($idss) or ";
			}
			$poIds_cond_approval=chop($poIds_cond_approval,'or ');
			$poIds_cond_approval.=")";
		}
		else
		{
			$poIds_cond_approval=" and po_break_down_id in($poIds)";
		}
		$ppApprovalDateArr=array();
		$ppApprovalDate_sql=sql_select("select po_break_down_id,approval_status_date,approval_status from wo_po_sample_approval_info where approval_status=3 $poIds_cond_approval");
		
		foreach($ppApprovalDate_sql as $row)
		{
			$ppApprovalDateArr[$row[csf('po_break_down_id')]]['approval_status_date']=$row[csf('approval_status_date')];
			$ppApprovalDateArr[$row[csf('po_break_down_id')]]['approval_status']=$row[csf('approval_status')];
		}
		//print_r($ex_factory_arr[4591][2]);die;
		unset($ex_factory_data);
		/*if($db_type==0)
		{
			$prod_sql="SELECT c.po_break_down_id, c.item_number_id,
				IFNULL(sum(CASE WHEN c.production_type ='1' THEN c.production_quantity ELSE 0 END),0) AS cutting_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='2' THEN c.production_quantity ELSE 0 END),0) AS printing_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='2' and embel_name=1 THEN c.production_quantity ELSE 0 END),0) AS print,
				IFNULL(sum(CASE WHEN c.production_type ='2' and embel_name=2 THEN c.production_quantity ELSE 0 END),0) AS emb,
				IFNULL(sum(CASE WHEN c.production_type ='2' and embel_name=3 THEN c.production_quantity ELSE 0 END),0) AS wash,
				IFNULL(sum(CASE WHEN c.production_type ='2' and embel_name=4 THEN c.production_quantity ELSE 0 END),0) AS special,
				IFNULL(sum(CASE WHEN c.production_type ='3' THEN c.production_quantity ELSE 0 END),0) AS printreceived_qnty, 
				IFNULL(sum(CASE WHEN c.production_type ='3' and embel_name=1 THEN c.production_quantity ELSE 0 END),0) AS printr,
				IFNULL(sum(CASE WHEN c.production_type ='3' and embel_name=2 THEN c.production_quantity ELSE 0 END),0) AS embr,
				IFNULL(sum(CASE WHEN c.production_type ='3' and embel_name=3 THEN c.production_quantity ELSE 0 END),0) AS washr,
				IFNULL(sum(CASE WHEN c.production_type ='3' and embel_name=4 THEN c.production_quantity ELSE 0 END),0) AS specialr,
				IFNULL(sum(CASE WHEN c.production_type ='4' THEN c.production_quantity ELSE 0 END),0) AS sewingin_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='5' THEN c.production_quantity ELSE 0 END),0) AS sewingout_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='7' THEN c.production_quantity ELSE 0 END),0) AS iron_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='7' THEN c.re_production_qty ELSE 0 END),0) AS re_iron_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='8' THEN c.production_quantity ELSE 0 END),0) AS finish_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='1' THEN c.reject_qnty ELSE 0 END),0) AS cutting_rej_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='3' THEN c.reject_qnty ELSE 0 END),0) AS emb_rej_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='5' THEN c.reject_qnty ELSE 0 END),0) AS sewingout_rej_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='7' THEN c.reject_qnty ELSE 0 END),0) AS iron_rej_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='8' THEN c.reject_qnty ELSE 0 END),0) AS finish_rej_qnty
				
			from 
				pro_garments_production_mst c
			where  
				c.status_active=1 and c.is_deleted=0 and c.company_id=$cbo_company_name group by c.po_break_down_id, c.item_number_id";
		}
		else
		{
			$prod_sql= "SELECT c.po_break_down_id, c.item_number_id, 
				NVL(sum(CASE WHEN c.production_type ='1' THEN c.production_quantity ELSE 0 END),0) AS cutting_qnty,
				NVL(sum(CASE WHEN c.production_type ='2' THEN c.production_quantity ELSE 0 END),0) AS printing_qnty,
				NVL(sum(CASE WHEN c.production_type ='2' and embel_name=1 THEN c.production_quantity ELSE 0 END),0) AS print,
				NVL(sum(CASE WHEN c.production_type ='2' and embel_name=2 THEN c.production_quantity ELSE 0 END),0) AS emb,
				NVL(sum(CASE WHEN c.production_type ='2' and embel_name=3 THEN c.production_quantity ELSE 0 END),0) AS wash,
				NVL(sum(CASE WHEN c.production_type ='2' and embel_name=4 THEN c.production_quantity ELSE 0 END),0) AS special,
				NVL(sum(CASE WHEN c.production_type ='3' THEN c.production_quantity ELSE 0 END),0) AS printreceived_qnty, 
				NVL(sum(CASE WHEN c.production_type ='3' and embel_name=1 THEN c.production_quantity ELSE 0 END),0) AS printr,
				NVL(sum(CASE WHEN c.production_type ='3' and embel_name=2 THEN c.production_quantity ELSE 0 END),0) AS embr,
				NVL(sum(CASE WHEN c.production_type ='3' and embel_name=3 THEN c.production_quantity ELSE 0 END),0) AS washr,
				NVL(sum(CASE WHEN c.production_type ='3' and embel_name=4 THEN c.production_quantity ELSE 0 END),0) AS specialr,
				NVL(sum(CASE WHEN c.production_type ='4' THEN c.production_quantity ELSE 0 END),0) AS sewingin_qnty,
				NVL(sum(CASE WHEN c.production_type ='5' THEN c.production_quantity ELSE 0 END),0) AS sewingout_qnty,
				NVL(sum(CASE WHEN c.production_type ='7' THEN c.production_quantity ELSE 0 END),0) AS iron_qnty,
				NVL(sum(CASE WHEN c.production_type ='7' THEN c.re_production_qty ELSE 0 END),0) AS re_iron_qnty,
				NVL(sum(CASE WHEN c.production_type ='8' THEN c.production_quantity ELSE 0 END),0) AS finish_qnty,
				NVL(sum(CASE WHEN c.production_type ='1' THEN c.reject_qnty ELSE 0 END),0) AS cutting_rej_qnty,
				NVL(sum(CASE WHEN c.production_type ='3' THEN c.reject_qnty ELSE 0 END),0) AS emb_rej_qnty,
				NVL(sum(CASE WHEN c.production_type ='5' THEN c.reject_qnty ELSE 0 END),0) AS sewingout_rej_qnty,
				NVL(sum(CASE WHEN c.production_type ='7' THEN c.reject_qnty ELSE 0 END),0) AS iron_rej_qnty,
				NVL(sum(CASE WHEN c.production_type ='8' THEN c.reject_qnty ELSE 0 END),0) AS finish_rej_qnty
			from 
				pro_garments_production_mst c
			where  
				c.status_active=1 and c.is_deleted=0 and c.company_id=$cbo_company_name group by c.po_break_down_id, c.item_number_id";
		}*/
		//echo $prod_sql."<br>";echo $prod_sql2;die;
		$emb_arr=array(1=>'print',2=>'emb',3=>'wash',4=>'special',5=>'gmt'); $gmts_prod_arr=array();
		//$prod_sql= "SELECT po_break_down_id, item_number_id, production_type, production_quantity, embel_name, re_production_qty, reject_qnty from pro_garments_production_mst c where status_active=1 and is_deleted=0 and company_id=$cbo_company_name $poIds_cond $location";
		$prod_sql= "SELECT po_break_down_id, item_number_id, production_type, production_quantity, embel_name, re_production_qty, reject_qnty from pro_garments_production_mst c where status_active=1 and is_deleted=0  $poIds_cond $location $floor";
		//echo $prod_sql;die;
		
		$res_gmtsData=sql_select($prod_sql);
		foreach($res_gmtsData as $gmtsRow)
		{
			/*$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][1]['cQty']=$gmtsRow[csf('cutting_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][2]['pQty']=$gmtsRow[csf('printing_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][3]['prQty']=$gmtsRow[csf('printreceived_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][4]['sQty']=$gmtsRow[csf('sewingin_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][5]['soQty']=$gmtsRow[csf('sewingout_qnty')];
			
			
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][2]['print']=$gmtsRow[csf('print')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][2]['emb']=$gmtsRow[csf('emb')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][2]['wash']=$gmtsRow[csf('wash')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][2]['special']=$gmtsRow[csf('special')];
			
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][3]['print']=$gmtsRow[csf('printr')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][3]['emb']=$gmtsRow[csf('embr')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][3]['wash']=$gmtsRow[csf('washr')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][3]['special']=$gmtsRow[csf('specialr')];
			
			
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][7]['iQty']=$gmtsRow[csf('iron_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][7]['riQty']=$gmtsRow[csf('re_iron_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][8]['fQty']=$gmtsRow[csf('finish_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][1]['crQty']=$gmtsRow[csf('cutting_rej_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][3]['embQty']=$gmtsRow[csf('emb_rej_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][5]['sorQty']=$gmtsRow[csf('sewingout_rej_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][7]['irnQty']=$gmtsRow[csf('iron_rej_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][8]['frQty']=$gmtsRow[csf('finish_rej_qnty')];*/
			
			if($gmtsRow[csf('production_type')]==2 || $gmtsRow[csf('production_type')]==3)
			{
				$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('production_type')]][$emb_arr[$gmtsRow[csf('embel_name')]]]+=$gmtsRow[csf('production_quantity')];
			}
			
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('production_type')]]['cQty']+=$gmtsRow[csf('production_quantity')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('production_type')]]['pQty']+=$gmtsRow[csf('production_quantity')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('production_type')]]['prQty']+=$gmtsRow[csf('production_quantity')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('production_type')]]['sQty']+=$gmtsRow[csf('production_quantity')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('production_type')]]['soQty']+=$gmtsRow[csf('production_quantity')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('production_type')]]['iQty']+=$gmtsRow[csf('production_quantity')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('production_type')]]['riQty']+=$gmtsRow[csf('re_production_qty')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('production_type')]]['fQty']+=$gmtsRow[csf('production_quantity')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('production_type')]]['crQty']+=$gmtsRow[csf('reject_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('production_type')]]['embQty']+=$gmtsRow[csf('reject_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('production_type')]]['sorQty']+=$gmtsRow[csf('reject_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('production_type')]]['irnQty']+=$gmtsRow[csf('reject_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('production_type')]]['frQty']+=$gmtsRow[csf('reject_qnty')];

		}
		//unset($res_gmtsData);
		$template_id_arr=return_library_array("select po_number_id, template_id from tna_process_mst group by po_number_id, template_id","po_number_id","template_id");
		
		//var_dump($orderWiseDataArr);
		
		$buyer_array=array();
		$i=0; $k=0; $date=date("Y-m-d"); 
		foreach($orderWiseDataArr as $po_id=>$po_data)
		{ 
			$po_data_arr=explode("##",$po_data);
			$job_no_prefix_num=$po_data_arr[0];
			$job_no=$po_data_arr[1];
			$team_leader=$po_data_arr[2];
			$dealing_marchant=$po_data_arr[3];
			$agent_name=$po_data_arr[4];
			$po_number=$po_data_arr[5];
			$po_quantity=$po_data_arr[6];
			$unit_price=$po_data_arr[7];
			$shipment_date=$po_data_arr[8];
			$shiping_status=$po_data_arr[9];
			$excess_cut=$po_data_arr[10];
			$plan_cut=$po_data_arr[11];
			$file_no=$po_data_arr[12];
			$grouping=$po_data_arr[13];
			$company_name=$po_data_arr[14];
			$buyer_name=$po_data_arr[15];
			$set_break_down=$po_data_arr[16];
			$style_ref_no=$po_data_arr[17];
			$ratio=$po_data_arr[18];
			$job_year=$po_data_arr[19];
			$details_remarks=$po_data_arr[20];
			$po_insert_date=$po_data_arr[21];
			$po_status=$po_data_arr[22];
			//echo "<pre>";
			//print_r($po_data_arr);die;
			//echo $set_break_down;
			
			$i++;
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

			$setArr = explode("__",$set_break_down );
			$countArr = count($setArr); 

			if($countArr==0) $countArr=1; 
			for($j=0;$j<$countArr;$j++)
			{
				$setItemArr = explode("_",$setArr[$j]);
				$item_id=$setItemArr[0];
				$set_qnty=$setItemArr[1];
				$smv_arr[$setItemArr[0]]=$setItemArr[2];
				if($item_id>0)
				{  //echo $unit_price.'/'.$ratio.'*'.$set_qnty.',';
				   	$k++;
				   	$po_quantity_in_pcs = $po_quantity*$set_qnty;
					$unitPrice=($unit_price/$ratio)*$set_qnty;  
					
				   	$ex_factory_date=$ex_factory_arr[$po_id][$item_id]['date'];
					$ex_factory_qnty=$ex_factory_arr[$po_id][$item_id]['qty'];
					
					$color=""; $days_remian="";
					$ex_factory_date_color=date("Y-m-d",strtotime($ex_factory_date));

					$shipment_date=date("Y-m-d",strtotime($shipment_date));
					if($shiping_status==1 || $shiping_status==2)
					{
						$days_remian=datediff("d",$date,$shipment_date)-1; 
						if($shipment_date > $date) 
						{
							$color="";
						}
						else if($shipment_date < $date) 
						{
							$color="red";
						}														
						else if($shipment_date >= $date && $days_remian<=5 ) 
						{
							$color="orange";
						}
					} 
					else if($shiping_status==3)
					{
						$days_remian=datediff("d",$ex_factory_date_color,$shipment_date)-1;
						if($shipment_date >= $ex_factory_date_color) 
						{ 
							$color="green";
						}
						else if($shipment_date < $ex_factory_date_color) 
						{ 
							$color="#2A9FFF";
						}
						
					}//end if condition
					
					$cutting_qnty=$gmts_prod_arr[$po_id][$item_id][1]['cQty'];
					$embl_recv_qnty=$gmts_prod_arr[$po_id][$item_id][3]['prQty'];
					$sewingin_qnty=$gmts_prod_arr[$po_id][$item_id][4]['sQty'];
					$sewingout_qnty=$gmts_prod_arr[$po_id][$item_id][5]['soQty'];
					$iron_qnty=$gmts_prod_arr[$po_id][$item_id][7]['iQty'];
					$re_iron_qnty=$gmts_prod_arr[$po_id][$item_id][7]['riQty'];
					$finish_qnty=$gmts_prod_arr[$po_id][$item_id][8]['fQty'];

					$buyer_array[$buyer_name]['buyer_name']=$buyer_name;
					$buyer_array[$buyer_name]['poQty']+=$po_quantity_in_pcs;
					$buyer_array[$buyer_name]['poVal']+=$po_quantity_in_pcs*$unitPrice;
					$buyer_array[$buyer_name]['ex']+=$ex_factory_qnty;
					$buyer_array[$buyer_name]['ex_val']+=$ex_factory_qnty*$unitPrice;
					$buyer_array[$buyer_name]['cQty']+=$cutting_qnty;
					$buyer_array[$buyer_name]['prQty']+=$embl_recv_qnty;
					$buyer_array[$buyer_name]['sQty']+=$sewingin_qnty;
					$buyer_array[$buyer_name]['soQty']+=$sewingout_qnty;
					$buyer_array[$buyer_name]['iQty']+=$iron_qnty;
					$buyer_array[$buyer_name]['reiQty']+=$re_iron_qnty;
					$buyer_array[$buyer_name]['fQty']+=$finish_qnty;

					
					$actual_exces_cut = $gmts_prod_arr[$po_id][$item_id][1]['cQty'];
					if($actual_exces_cut < $po_quantity_in_pcs) $actual_exces_cut=""; else $actual_exces_cut= number_format((($actual_exces_cut-$po_quantity_in_pcs)/$po_quantity_in_pcs)*100,2);

					$issue_print = $gmts_prod_arr[$po_id][$item_id][2]['print'];
					$issue_emb = $gmts_prod_arr[$po_id][$item_id][2]['emb'];
					$issue_wash = $gmts_prod_arr[$po_id][$item_id][2]['wash'];
					$issue_special = $gmts_prod_arr[$po_id][$item_id][2]['special'];
					$issue_gd = $gmts_prod_arr[$po_id][$item_id][2]['gmt'];
					
					$embl_iss_qty=$issue_print+$issue_emb+$issue_wash+$issue_special+$issue_gd;
					
					$embl_issue_total="";
					if($issue_print!=0) $embl_issue_total .= 'PR='.$issue_print;
					if($issue_emb!=0) 
					{
						if($embl_issue_total=="") $embl_issue_total .= 'EM='.$issue_emb; else $embl_issue_total .= ', EM='.$issue_emb;
					}
					
					if($issue_wash!=0) 
					{
						if($embl_issue_total=="") $embl_issue_total .= 'WA='.$issue_wash; else $embl_issue_total .= ', WA='.$issue_wash;
					}
					
					if($issue_special!=0) 
					{
						if($embl_issue_total=="") $embl_issue_total .= 'SP='.$issue_special; else $embl_issue_total .= ', SP='.$issue_special;
					}
					if($issue_gd!=0) 
					{
						if($embl_issue_total=="") $embl_issue_total .= 'GD='.$issue_gd; else $embl_issue_total .= ', GD='.$issue_gd;
					}

					$rcv_print = $gmts_prod_arr[$po_id][$item_id][3]['print'];
					$rcv_emb = $gmts_prod_arr[$po_id][$item_id][3]['emb'];
					$rcv_wash = $gmts_prod_arr[$po_id][$item_id][3]['wash'];
					$rcv_special = $gmts_prod_arr[$po_id][$item_id][3]['special'];
					$rcv_gd = $gmts_prod_arr[$po_id][$item_id][3]['gmt'];
					
					$embl_receive_total="";	
					if($rcv_print!=0) $embl_receive_total .= 'PR='.$rcv_print;
					
					if($rcv_emb!=0) 
					{
						if($embl_receive_total=="") $embl_receive_total .= 'EM='.$rcv_emb; else $embl_receive_total .= ', EM='.$rcv_emb;
					}
					
					if($rcv_wash!=0) 
					{
						if($embl_receive_total=="") $embl_receive_total .= 'WA='.$rcv_wash; else $embl_receive_total .= ', WA='.$rcv_wash;
					}
					
					if($rcv_special!=0) 
					{
						if($embl_receive_total=="") $embl_receive_total .= 'SP='.$rcv_special; else $embl_receive_total .= ', SP='.$rcv_special;
					}
					if($rcv_gd!=0) 
					{
						if($embl_receive_total=="") $embl_receive_total .= 'GD='.$rcv_gd; else $embl_receive_total .= ', GD='.$rcv_gd;
					}
					
					$embl_recv_qty=$rcv_print+$rcv_emb+$rcv_wash+$rcv_special+$rcv_gd;
					
					//$rej_value=$proRes[csf("finish_rej_qnty")]+$proRes[csf("sewingout_rej_qnty")];	
					$rej_value=$gmts_prod_arr[$po_id][$item_id][1]['crQty']+$gmts_prod_arr[$po_id][$item_id][3]['embQty']+$gmts_prod_arr[$po_id][$item_id][5]['sorQty']+$gmts_prod_arr[$po_id][$item_id][7]['irnQty']+$gmts_prod_arr[$po_id][$item_id][8]['frQty'];
					$shortage = $po_quantity_in_pcs-$ex_factory_qnty;
					$finish_status = $finish_qnty*100/$po_quantity_in_pcs;
					
					if($j==0) 
					{
						$display_font_color="";
						$font_end="";
					}
					else 
					{
						$display_font_color="&nbsp;<font style='display:none'>";
						$font_end="</font>";
					}
					
					if(($ex_factory_date=="" || $ex_factory_date=="0000-00-00")) $ex_factory_date="&nbsp;"; else $ex_factory_date=change_date_format($ex_factory_date);
					//if($orderRes[csf("shiping_status")]==3) $days_remian=$days_remian; else $days_remian="---";
					if($actual_exces_cut > number_format($excess_cut,2)) $excess_bgcolor="bgcolor='#FF0000'"; else $excess_bgcolor="";
					if($actual_exces_cut==0) $actual_exces_cut_per=""; else  $actual_exces_cut_per=$actual_exces_cut." %";
					
					$total_rej_value+=$rej_value; 
					
					$template_id=$template_id_arr[$po_id];
					 //change_date_format($orderRes[csf("shipment_date")])
				   	$html.="<tr bgcolor='$bgcolor' onclick=change_color('tr_2nd$k','$bgcolor') id=tr_2nd$k>";
					$html.='<td width="30">'.$display_font_color.$i.$font_end.'</td>
								<td width="130"><p><a href="##" onclick="progress_comment_popup('.$po_id.",'".$template_id."',".$tna_process_type.')">'.$display_font_color.$po_number.$font_end.'</a></p></td>
								<td width="60"><p>'.$display_font_color.$buyer_short_library[$buyer_name].$font_end.'</p></td>
								<td width="50" align="center"><p>'.$display_font_color.$job_year.$font_end.'</p></td>
								<td width="80" align="center"><p>'.$display_font_color.$job_no_prefix_num.$font_end.'</p></td>
								<td width="120"><p>'.$display_font_color.$style_ref_no.$font_end.'</p></td>
								<td width="100"><p>'.$display_font_color.$file_no.$font_end.'&nbsp;</p></td>
								<td width="100"><p>'.$display_font_color.$grouping.$font_end.'&nbsp;</p></td>
								<td width="80"><p>'.$display_font_color.$team_name_arr[$team_leader].$font_end.'</p></td>
								<td width="90"><p>'.$display_font_color.$team_member_arr[$dealing_marchant].$font_end.'</p></td>
								<td width="60"><p>'.$display_font_color.$buyer_short_library[$agent_name].$font_end.'</p></td>
								<td width="35" onclick="openmypage_image(\'requires/order_wise_production_report_controller.php?action=show_image&job_no='.$job_no.'\',\'Image View\')"><img src="../../'.$imge_arr[$job_no].'" height="25" width="30" /></td>
								<td width="130"><p>'.$garments_item[$item_id].'</p></td>

								<td width="50" align="center"><p>'.$smvArr[$job_no.$item_id].'</p></td>
								<td width="80" align="right"><a href="##" onclick="openmypage_order('.$po_id.",".$company_name.",".$item_id.",0,'OrderPopup'".')">'.$po_quantity_in_pcs.'</a></td>
								
								<td width="80" align="right" title=" Unite Price='.$unitPrice.'">'.number_format($po_quantity_in_pcs*$unitPrice,2).'</td>
								
								<td width="80" align="center">'.change_date_format($po_insert_date).'</td>
								<td width="80" align="center" bgcolor="'.$color.'">'.change_date_format($shipment_date).'</td>
								<td width="80" align="right"><a href="##" onclick="openmypage('.$po_id.",".$item_id.",'exfactory','','','','0'".')">'.$ex_factory_date.'</a></td>
								
								
								
								<td width="80" align="center">'.change_date_format($ppApprovalDateArr[$po_id]['approval_status_date']).'</a></td>



								<td width="80" align="center">'.$days_remian.'&nbsp;</td>
								<td width="80" align="right">&nbsp;'.number_format($excess_cut,2)." %".'</td>
								<td width="80" align="right">'.$plan_cut.'</td>
								<td width="80" align="right"><a href="##" onclick="openmypage('.$po_id.",".$item_id.",'1','','','$type',''".')">'.$cutting_qnty.'</a></td>
								
								<td width="80" align="right">'.($po_quantity_in_pcs-$cutting_qnty).'</td>
								
								<td width="80" align="right"'.$excess_bgcolor.'>&nbsp;'.$actual_exces_cut_per.'</td>
								<td width="80" align="right"><font color="$bgcolor" style="display:none">'.$embl_iss_qty.'</font>&nbsp;<a href="##" onclick="openmypage('.$po_id.",".$item_id.",'2','','','$type',''".')">'.$embl_issue_total.'</a></td>
								<td width="80" align="right"><font color="$bgcolor" style="display:none">'.$embl_recv_qty.'</font>&nbsp;<a href="##" onclick="openmypage('.$po_id.",".$item_id.",'3','','','$type',''".')">'.$embl_receive_total.'</a></td>
								<td width="80" align="right"><a href="##" onclick="openmypage('.$po_id.",".$item_id.",'4','','','$type',''".')">'.$sewingin_qnty.'</a></td>
								<td width="80" align="right"><a href="##" onclick="openmypage('.$po_id.",".$item_id.",'5','','','$type',''".')">'.$sewingout_qnty.'</a></td>
								<td width="80" align="right"><a href="##" onclick="openmypage('.$po_id.",".$item_id.",'7','','','$type',''".')">'.$iron_qnty.'</a></td>
								<td width="80" align="right"><a href="##" onclick="openmypage('.$po_id.",".$item_id.",'9','','','$type',''".')">'.$re_iron_qnty.'</a></td>
								<td width="80" align="right"><a href="##" onclick="openmypage('.$po_id.",".$item_id.",'8','','','$type',''".')">'.$finish_qnty.'</a></td>
								<td width="80" align="right">&nbsp;'.number_format($finish_status,2)." %".'</td>
								<td width="80" align="right"><a href="##" onclick="openmypage_rej('.$po_id.",".$item_id.",'reject_qty','','','$type',''".')">'.$rej_value.'</a></td>
								<td width="80" align="right">'.$ex_factory_qnty.'</td>
								
								<td width="80" align="right">'.number_format($ex_factory_qnty*$unitPrice,2).'</td>
								
								<td width="80" align="right">'.$shortage.'</td>
								<td width="60">'.$order_status[$po_status].'</td>
								<td width="85">'.$shipment_status[$shiping_status].'</td>
								<td width="85" align="center">&nbsp;<a href="##" onclick="openmypage_remark('.$po_id.",".$item_id.",0,'date_wise_production_report'".')">Veiw</a></td>
								<td align="center">'.$details_remarks.'</td>
							</tr>';
				}
			} //end for loop
		}// end main foreach 
	    ?>
	    <div>
	    	<table width="1500" cellspacing="0" >
	            <tr class="form_caption" style="border:none;">
	                <td colspan="20" align="center" style="border:none;font-size:16px; font-weight:bold" >
	                    <?  echo "Order Wise Production Report"; ?>    
	                </td>
	            </tr>
	            <tr style="border:none;">
	                <td colspan="20" align="center" style="border:none; font-size:14px;">
	                    Company Name : <? echo $company_library[str_replace("'","",$cbo_company_name)]; ?>                                
	                </td>
	            </tr>
	            <tr style="border:none;">
	                <td colspan="20" align="center" style="border:none;font-size:12px; font-weight:bold">
	                    <?
	                        if(str_replace("'","",trim($txt_date_from))!="" && str_replace("'","",trim($txt_date_to))!="")
	                        {
	                            echo "From $fromDate To $toDate" ;
	                        }
	                    ?>
	                </td>
	            </tr>
	        </table>
	        <div id="data_panel" align="center" style="width:100%">
	         <script>
			 	function new_window()
				{
					document.getElementById('scroll_body1').style.overflow="auto";
					document.getElementById('scroll_body1').style.maxHeight="none";
					var w = window.open("Surprise", "#");
					
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
					
					document.getElementById('scroll_body1').style.overflowY="scroll";
					document.getElementById('scroll_body1').style.maxHeight="425px";
				 }
	          </script>
	        
	        </div>
	        <div style="float:left; width:1200px">
	        	<input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
	        </div>
	        <div style="float:left; width:1430px" id="details_reports">
	            <table width="1410" cellspacing="0" border="1" class="rpt_table" rules="all" id="" >
	                <thead>
	                    <tr>
	                        <th colspan="17" >In-House Order Production 
	                        <br />
	                        <?
	                        if(str_replace("'","",trim($txt_date_from))!="" && str_replace("'","",trim($txt_date_to))!="")
	                        {
								echo ' Company Name : '.$company_library[str_replace("'","",$cbo_company_name)].'<br/>';  
	                            echo "From $fromDate To $toDate" ;
	                        }
	                    	?>
	                        </th>
	                     </tr>
	                    <tr style="font-size:11px">
	                        <th width="30">Sl.</th>    
	                        <th width="100">Buyer Name</th>
	                        <th width="110">Order Qty.(Pcs)</th>
	                        <th width="110">Order Value</th>
	                        <th width="80">Total Cut Qty</th>
	                        <th width="80">Cutting balance</th>
	                        <th width="80">Total Emb. Rcv. Qty</th>
	                        <th width="80">Total Sew Input Qty</th>
	                        <th width="80">Total Sew Qty</th>
	                        <th width="80">Total Iron Qty</th>
	                        <th width="80">Total Re-Iron Qty</th>
	                        <th width="80">Total Finish Qty</th>
	                        <th width="80">Fin Goods Status %</th>
	                        <th width="80">Ex-Fac Qty</th>
	                        <th width="80">Ex-Fac-Balance</th>
	                        <th width="80">Ex-Fac-Value</th>
	                        <th>Ex-Fac%</th>
	                     </tr>
	                </thead>
	            </table>
	            <div style="max-height:425px; overflow-y:scroll; width:1430px" id="scroll_body1" >
	                <table cellspacing="0" border="1" class="rpt_table"  width="1410" rules="all" id="" >
	                <?
						$i=1; $total_po_quantity=0;$total_po_value=0; $total_cut=0; $total_print_iss=0;
						$total_print_re=0; $total_sew_input=0; $total_sew_out=0; $total_iron=0; $total_re_iron=0; $total_finish=0;$total_ex_factory=0;
						$buyer_po_value=array();$total_ex_factory_balance=$total_ex_factory_val=0;
						foreach($buyer_array as $row)
						{
							$buyer_po_value[$row["poVal"]]=$row["poVal"];
						}
						array_multisort($buyer_po_value, SORT_DESC,$buyer_array);
						//var_dump($buyer_array);die;
						foreach($buyer_array as $buyer_id=>$value)
	                    {
	                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							
	                        ?>
	                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
	                            <td width="30"><? echo $i;?></td>
	                            <td width="100" style="font-size:11px"><? echo $buyer_short_library[$value["buyer_name"]]; ?></td>
	                            <td width="110" align="right"><? echo number_format($value["poQty"]);?></td>
	                            <td width="110" align="right"><? echo number_format($value["poVal"],2);?></td>
	                            <td width="80" align="right"><? echo number_format($value["cQty"]); ?></td>
	                            <td width="80" align="right"><? echo number_format($value["poQty"]-$value["cQty"]); ?></td>
	                           
	                            <td width="80" align="right"><? echo number_format($value["prQty"]); ?></td>
	                            <td width="80" align="right"><? echo number_format($value["sQty"]); ?></td>
	                            <td width="80" align="right"><? echo number_format($value["soQty"]); ?></td>
	                            <td width="80" align="right"><? echo number_format($value["iQty"]); ?></td>
	                            <td width="80" align="right"><? echo number_format($value["reiQty"]); ?></td>
	                            <td width="80" align="right"><? echo number_format($value["fQty"]); ?></td>
	                            <? $finish_gd_status = ($value["fQty"]/$value["poQty"])*100; ?>
	                            <td width="80" align="right"><? echo number_format($finish_gd_status,2); ?></td>
	                            <td width="80" align="right"><? echo number_format($value["ex"]); ?></td>
	                             <td width="80" align="right" title="PO Qty-Ex-Fac Qty"><? echo number_format($value["poQty"]-$value["ex"]); ?></td>
	                              <td width="80" align="right"><? echo number_format($value["ex_val"]); ?></td>
	                            <? $ex_gd_status = ($value["ex"]/$value["poQty"])*100; ?>
	                            <td align="right"><? echo  number_format($ex_gd_status,2); ?></td>
	                        </tr>	
	                        <?		
	                            $total_po_quantity+=$value["poQty"];
	                            $total_po_value+=$value["poVal"];
	                            $total_cut+=$value["cQty"];
	                            $total_print_re+=$value["prQty"];
	                            $total_sew_input+=$value["sQty"];
	                            $total_sew_out+=$value["soQty"];
	                            $total_iron+=$value["iQty"];
	                            $total_re_iron+=$value["reiQty"];
	                            $total_finish+=$value["fQty"];
	                            $total_ex_factory+=$value["ex"];
								$total_ex_factory_balance+=$value["poQty"]-$value["ex"];
								$total_ex_factory_val+=$value["ex_val"];
	                           
	                        $i++;
	                    }//end foreach 1st
	                    
	                    $chart_data_qnty="Order Qty;".$total_po_quantity."\n"."Cutting;".$total_cut."\n"."Sew In;".$total_sew_input."\n"."Sew Out ;".$total_sew_out."\n"."Iron ;".$total_iron."\n"."Finish ;".$total_finish."\n"."Ex-Fact;".$total_ex_factory."\n";
	                ?>
	                </table>
	                <table border="1" class="tbl_bottom"  width="1410" rules="all" id="" >
	                    <tr> 
	                        <td width="30">&nbsp;</td> 
	                        <td width="100" align="right">Total</td> 
	                        <td width="110" id="tot_po_quantity" align="right"><? echo number_format($total_po_quantity); ?></td> 
	                        <td width="110" id="tot_po_value" align="right"><? echo number_format($total_po_value,2); ?></td> 
	                        <td width="80" id="tot_cutting" align="right"><? echo number_format($total_cut); ?></td>
	                        <td width="80" id="tot_cutting" align="right"><? echo number_format($total_po_quantity-$total_cut); ?></td>
	                        
	                        
	                        <td width="80" id="tot_emb_rcv" align="right"><? echo number_format($total_print_re); ?></td> 
	                        <td width="80" id="tot_sew_in" align="right"><? echo number_format($total_sew_input); ?></td> 
	                        <td width="80" id="tot_sew_out" align="right"><? echo number_format($total_sew_out); ?></td>   
	                        <td width="80" id="tot_iron" align="right"><? echo number_format($total_iron); ?></td> 
	                        <td width="80" id="tot_re_iron" align="right"><? echo number_format($total_re_iron); ?></td> 
	                        <td width="80" id="tot_finish" align="right"><? echo number_format($total_finish); ?></td>
	                        <? $total_finish_gd_status = ($total_finish/$total_po_quantity)*100; ?>
	                        <td width="80" align="right"><? echo number_format($total_finish_gd_status,2); ?></td >
	                        <td width="80" align="right"><? echo number_format($total_ex_factory); ?></td >
	                        <td width="80" align="right"><? echo number_format($total_ex_factory_balance); ?></td >
	                        <td width="80" align="right"><? echo number_format($total_ex_factory_val); ?></td >
	                        <? $total_ex_status = ($total_ex_factory/$total_po_quantity)*100; ?>
	                        <td align="right"><? echo number_format($total_ex_status,2); ?></td>
	                    </tr>
	                 </table>
	                 <input type="hidden" id="graph_data" value="<? echo substr($chart_data_qnty,0,-1); ?>"/>
	            </div>
	        </div>
	        <div style="float:left; width:600px">   
	            <table>
	                <tr>
	                    <td height="22" width="600"><div id="chartdiv"> </div></td>
	                </tr>    
	            </table>
	        </div> 
	        <div style="clear:both"></div>
	        <table width="3440" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1">
	            <thead>
	                <tr style="font-size:11px">
	                    <th width="30">SL</th>    
	                    <th width="130">Order Number</th>
	                    <th width="60">Buyer Name</th>
	                    <th width="50">Job Year</th>
	                    <th width="80">Job Number</th>
	                    <th width="120">Style Name</th>
	                    <th width="100">File No</th>
	                    <th width="100">Internal Ref.</th>
	                    <th width="80">Team Name</th>
	                    <th width="90">Team Member</th>
	                    <th width="60">Agent Name</th>
	                    <th width="35">Img</th>
	                    <th width="130">Item Name</th>
	                    <th width="50">SMV</th>
	                    <th width="80">Order Qty.</th>
	                    <th width="80">Order Value</th>
	                    <th width="80">PO insert Date</th>
	                    <th width="80">Ship Date</th>
	                    <th width="80">Ex-Factory Date</th>
	                    <th width="80">PP Approved Date</th>
	                    <th width="80">Days in Hand</th>
	                    <th width="80">Stan. Exc. Cut %</th>
	                    <th width="80">Plan Cut Qty</th>
	                    <th width="80">Total Cut Qty</th>
	                    <th width="80">Cutting balance</th>
	                    <th width="80">Actual Exc. Cut %</th>
	                    <th width="80">Total Emb. Issue Qty</th>
	                    <th width="80">Total Emb. Rcv. Qty</th>
	                    <th width="80">Total Sew Input Qty</th>
	                    <th width="80">Total Sew Output Qty</th>
	                    <th width="80">Total Iron Qty</th>
	                    <th width="80">Total Re-Iron Qty</th>
	                    <th width="80">Total Finish Qty</th>
	                    <th width="80">Fin Goods Status</th>
	                    <th width="80">Reject Qty</th>
	                    <th width="80">Total Ship Out</th>
	                    <th width="80">Ship Out Value</th>
	                    <th width="80">Shortage/ Excess</th>
	                    <th width="60">Order Status</th>
	                    <th width="85">Shipping Status</th>
	                    <th width="85">production remarks</th>
	                    <th>Order Remarks</th>
	                 </tr>
	            </thead>
	        </table>
	        <div style="max-height:425px; overflow-y:scroll; width:3460px" id="scroll_body">
	            <table border="1" class="rpt_table" width="3440" rules="all" id="table_body">
					<? echo $html; ?>  
	            </table>	
	            <!-- <table border="1" class="tbl_bottom" width="3050" rules="all" id="report_table_footer_1" >
	                <tr>
	                    <td width="30"></td>
	                    <td width="130"></td>
	                    <td width="60"></td>
	                    <td width="50"></td>
	                    <td width="80"></td>
	                    <td width="120"></td>
	                    <td width="100"></td>
	                    <td width="100"></td>
	                    <td width="80"></td>
	                    <td width="90"></td>
	                    <td width="60"></td>
	                    <td width="35"></td>
	                    <td width="50"></td>
	                    <td width="80">Total</td>

	                    <td width="80" id="total_order_quantitysss"></td>
	                    <td width="80"></td>
	                    <td width="80"></td>
	                    <td width="80"></td>
	                    <td width="80" ></td>
	                    <td width="80" id="tot_plan_cut"></td>
	                    
	                    <td width="80" id="total_cutting"></td>
	                    <td width="80" id="total_cutting_bal"></td>
	                    <td width="80"></td>
	                    <td width="80" id="total_emb_issue"></td>
	                    <td width="80" id="total_emb_receive"></td>
	                    <td width="80" id="total_sewing_input"></td>
	                    <td width="80" id="total_sewing_out"></td>
	                    <td width="80" id="total_iron_qnty"></td>
	                    <td width="80" id="total_re_iron_qnty"></td>
	                    <td width="80" id="total_finish_qnty"></td>
	                    <td width="80"></td>
	                    <td width="80" align="right" id="total_rej_value_td"></td>
	                    <td width="80" id="total_out"></td>
	                    <td width="80" id="total_shortage"></td>
	                    <td width="85" id="ship_status"></td>
	                    <td width="85" id="ship_status"></td>
	                    <td></td>
	                 </tr>
				</table> -->

				<table border="1" class="tbl_bottom" width="3440" rules="all" id="report_table_footer_1" >
	                <tr>
	                    <td width="30"></td>
	                    <td width="130"></td>
	                    <td width="60"></td>
	                    <td width="50"></td>
	                    <td width="80"></td>
	                    <td width="120"></td>
	                    <td width="100"></td>
	                    <td width="100"></td>
	                    <td width="80"></td>
	                    <td width="90"></td>
	                    <td width="60"></td>
	                    <td width="35"></td>
	                    <td width="130"></td>
	                    <td width="50">Total</td>
	                    <td width="80" id="total_order_quantity"></td>
	                    <td width="80" id="order_value"></td>
	                    <td width="80"></td>
	                    <td width="80"></td>
	                    <td width="80"></td>
	                    <td width="80"></td>
	                    <td width="80"></td>
	                    <td width="80"></td>
	                    <td width="80" id="tot_plan_cut"></td>
	                    <td width="80" id="total_cutting"></td>
	                    <td width="80" id="total_cutting_bal"></td>
	                    <td width="80"></td>
	                    <td width="80" id="total_emb_issue"></td>
	                    <td width="80" id="total_emb_receive"></td>
	                    <td width="80" id="total_sewing_input"></td>
	                    <td width="80" id="total_sewing_out"></td>
	                    <td width="80" id="total_iron_qnty"></td>
	                    <td width="80" id="total_re_iron_qnty"></td>
	                    <td width="80" id="total_finish_qnty"></td>
	                    <td width="80" ></td>
	                    <td width="80" align="right" id="total_rej_value_td"></td>
	                    <td width="80" id="total_out"></td>
	                    <td width="80" id="ship_out_value"></td>
	                    <td width="80" id="total_shortage"></td>
	                    <td width="60"></td>
	                    <td width="85" id="ship_status"></td>
	                    <td width="85" id="ship_status"></td>
	                    <td></td>
	                 </tr>
				</table>
	        </div>
	     </div>   
	    <?
    }
	else if($type==2)//Order Location & Floor Wise
    {
		
		$ex_factory_arr=array();
		$ex_factory_data=sql_select("select po_break_down_id, item_number_id, location, MAX(ex_factory_date) AS ex_factory_date,sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END)-sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) AS ex_factory_qnty from pro_ex_factory_mst where status_active=1 and is_deleted=0 group by po_break_down_id, item_number_id, location");
		foreach($ex_factory_data as $exRow)
		{
			$ex_factory_arr[$exRow[csf('po_break_down_id')]][$exRow[csf('item_number_id')]][$exRow[csf('location')]]['date']=$exRow[csf('ex_factory_date')];
			$ex_factory_arr[$exRow[csf('po_break_down_id')]][$exRow[csf('item_number_id')]][$exRow[csf('location')]]['qty']=$exRow[csf('ex_factory_qnty')];
		}
		//print_r($ex_factory_arr[107]);die;
		unset($ex_factory_data);
		
		$buyer_array=array(); 
		$ship_status_cond="";
		if($shipping_status!=0) $ship_status_cond=" and a.shiping_status=$shipping_status";
		if($db_type==0) $select_job_year="year(b.insert_date) as job_year"; else $select_job_year="to_char(b.insert_date,'YYYY') as job_year";
		 $order_sql="select a.id, b.job_no_prefix_num, $select_job_year, b.team_leader, b.dealing_marchant, a.po_number, sum(a.po_quantity) as po_quantity, a.unit_price, a.po_total_price, a.job_no_mst, a.pub_shipment_date as shipment_date, a.shiping_status, a.excess_cut, a.plan_cut, b.company_name, b.buyer_name, b.set_break_down, b.style_ref_no, c.location, c.floor_id from wo_po_details_master b,wo_po_break_down a left join pro_garments_production_mst c on c.po_break_down_id=a.id and c.status_active=1 and c.is_deleted=0 $location $floor where a.job_no_mst=b.job_no and a.status_active=1 and b.status_active=1 and LOWER(a.po_number) like LOWER('$search_string') $txt_date $company_name $buyer_name $season_cond $garmentsNature $team_name_cond $team_member_cond $ship_status_cond $order_status_cond $item_cat_cond 
		group by a.id, b.job_no_prefix_num, b.insert_date, b.team_leader, b.dealing_marchant, a.po_number, a.job_no_mst, a.pub_shipment_date, a.shiping_status, a.excess_cut, a.unit_price, a.po_total_price, a.plan_cut, b.company_name, b.buyer_name, b.set_break_down, b.style_ref_no, c.location, c.floor_id order by a.id, c.location, c.floor_id";


		$result_po=sql_select($order_sql);
		$po_id_str="";
		foreach($result_po as $poID_data)
		{ 
			$po_id_str.=$poID_data[csf("id")].',';
		}
		$po_id_strs=chop($po_id_str,',');

		if ($db_type==0) 
		{
			if($po_id_strs!="") {$po_id_strs_cond="and po_break_down_id in($po_id_strs)";}else{$po_id_strs_cond="";}
		}
		else
		{
				$poID=explode(",",$po_id_strs);  
				$poID=array_chunk($poID,999);
				$po_id_strs_cond=" and";
				foreach($poID as $dtls_id)
				{
					if($po_id_strs_cond==" and")  $po_id_strs_cond.="(po_break_down_id in(".implode(',',$dtls_id).")"; else $po_id_strs_cond.=" or po_break_down_id in(".implode(',',$dtls_id).")";
				}
				$po_id_strs_cond.=")";
				//echo $po_id_strs_cond;die;
		}
		//------------------------
			
		/*if($db_type==0)
		{
			$prod_sql="SELECT c.po_break_down_id as po_id, c.item_number_id as item_id, location, floor_id,
				IFNULL(sum(CASE WHEN c.production_type ='1' THEN c.production_quantity ELSE 0 END),0) AS cutting_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='2' THEN c.production_quantity ELSE 0 END),0) AS printing_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='2' and embel_name=1 THEN c.production_quantity ELSE 0 END),0) AS print,
				IFNULL(sum(CASE WHEN c.production_type ='2' and embel_name=2 THEN c.production_quantity ELSE 0 END),0) AS emb,
				IFNULL(sum(CASE WHEN c.production_type ='2' and embel_name=3 THEN c.production_quantity ELSE 0 END),0) AS wash,
				IFNULL(sum(CASE WHEN c.production_type ='2' and embel_name=4 THEN c.production_quantity ELSE 0 END),0) AS special,
				IFNULL(sum(CASE WHEN c.production_type ='3' THEN c.production_quantity ELSE 0 END),0) AS printreceived_qnty, 
				IFNULL(sum(CASE WHEN c.production_type ='3' and embel_name=1 THEN c.production_quantity ELSE 0 END),0) AS printr,
				IFNULL(sum(CASE WHEN c.production_type ='3' and embel_name=2 THEN c.production_quantity ELSE 0 END),0) AS embr,
				IFNULL(sum(CASE WHEN c.production_type ='3' and embel_name=3 THEN c.production_quantity ELSE 0 END),0) AS washr,
				IFNULL(sum(CASE WHEN c.production_type ='3' and embel_name=4 THEN c.production_quantity ELSE 0 END),0) AS specialr,
				IFNULL(sum(CASE WHEN c.production_type ='4' THEN c.production_quantity ELSE 0 END),0) AS sewingin_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='5' THEN c.production_quantity ELSE 0 END),0) AS sewingout_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='7' THEN c.production_quantity ELSE 0 END),0) AS iron_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='7' THEN c.re_production_qty ELSE 0 END),0) AS re_iron_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='8' THEN c.production_quantity ELSE 0 END),0) AS finish_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='1' THEN c.reject_qnty ELSE 0 END),0) AS cutting_rej_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='8' THEN c.reject_qnty ELSE 0 END),0) AS finish_rej_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='5' THEN c.reject_qnty ELSE 0 END),0) AS sewingout_rej_qnty
			from 
				pro_garments_production_mst c
			where  
				c.status_active=1 and c.is_deleted=0 and c.company_id=$cbo_company_name group by c.po_break_down_id, c.item_number_id, location, floor_id";
		}
		else
		{
			$prod_sql= "SELECT c.po_break_down_id as po_id, c.item_number_id as item_id, location, floor_id, 
				NVL(sum(CASE WHEN c.production_type ='1' THEN c.production_quantity ELSE 0 END),0) AS cutting_qnty,
				NVL(sum(CASE WHEN c.production_type ='2' THEN c.production_quantity ELSE 0 END),0) AS printing_qnty,
				NVL(sum(CASE WHEN c.production_type ='2' and embel_name=1 THEN c.production_quantity ELSE 0 END),0) AS print,
				NVL(sum(CASE WHEN c.production_type ='2' and embel_name=2 THEN c.production_quantity ELSE 0 END),0) AS emb,
				NVL(sum(CASE WHEN c.production_type ='2' and embel_name=3 THEN c.production_quantity ELSE 0 END),0) AS wash,
				NVL(sum(CASE WHEN c.production_type ='2' and embel_name=4 THEN c.production_quantity ELSE 0 END),0) AS special,
				NVL(sum(CASE WHEN c.production_type ='3' THEN c.production_quantity ELSE 0 END),0) AS printreceived_qnty, 
				NVL(sum(CASE WHEN c.production_type ='3' and embel_name=1 THEN c.production_quantity ELSE 0 END),0) AS printr,
				NVL(sum(CASE WHEN c.production_type ='3' and embel_name=2 THEN c.production_quantity ELSE 0 END),0) AS embr,
				NVL(sum(CASE WHEN c.production_type ='3' and embel_name=3 THEN c.production_quantity ELSE 0 END),0) AS washr,
				NVL(sum(CASE WHEN c.production_type ='3' and embel_name=4 THEN c.production_quantity ELSE 0 END),0) AS specialr,
				NVL(sum(CASE WHEN c.production_type ='4' THEN c.production_quantity ELSE 0 END),0) AS sewingin_qnty,
				NVL(sum(CASE WHEN c.production_type ='5' THEN c.production_quantity ELSE 0 END),0) AS sewingout_qnty,
				NVL(sum(CASE WHEN c.production_type ='7' THEN c.production_quantity ELSE 0 END),0) AS iron_qnty,
				NVL(sum(CASE WHEN c.production_type ='7' THEN c.re_production_qty ELSE 0 END),0) AS re_iron_qnty,
				NVL(sum(CASE WHEN c.production_type ='8' THEN c.production_quantity ELSE 0 END),0) AS finish_qnty,
				NVL(sum(CASE WHEN c.production_type ='1' THEN c.reject_qnty ELSE 0 END),0) AS cutting_rej_qnty,
				NVL(sum(CASE WHEN c.production_type ='8' THEN c.reject_qnty ELSE 0 END),0) AS finish_rej_qnty,
				NVL(sum(CASE WHEN c.production_type ='5' THEN c.reject_qnty ELSE 0 END),0) AS sewingout_rej_qnty
			from 
				pro_garments_production_mst c
			where  
				c.status_active=1 and c.is_deleted=0 and c.company_id=$cbo_company_name group by c.po_break_down_id, c.item_number_id, location, floor_id";
		}*/

		if(str_replace("'","",$cbo_location)==0) $location_cond=""; else $location_cond=" and location=$cbo_location";
		if(str_replace("'","",$cbo_floor)==0) $floor_cond=""; else $floor_cond=" and floor_id=$cbo_floor";
		$emb_arr=array(1=>'print',2=>'emb',3=>'wash',4=>'special',5=>'gmt'); $gmts_prod_arr=array();
		$prod_sql= "SELECT location, floor_id, po_break_down_id, item_number_id, production_type, production_quantity, embel_name, re_production_qty, reject_qnty from 
				pro_garments_production_mst where status_active=1 and is_deleted=0 and company_id=$cbo_company_name $po_id_strs_cond $location_cond $floor_cond";
		
		$res_gmtsData=sql_select($prod_sql);
		foreach($res_gmtsData as $gmtsRow)
		{
			/*$gmts_prod_arr[$gmtsRow[csf('po_id')]][$gmtsRow[csf('item_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][2]['print']=$gmtsRow[csf('print')];
			$gmts_prod_arr[$gmtsRow[csf('po_id')]][$gmtsRow[csf('item_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][2]['emb']=$gmtsRow[csf('emb')];
			$gmts_prod_arr[$gmtsRow[csf('po_id')]][$gmtsRow[csf('item_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][2]['wash']=$gmtsRow[csf('wash')];
			$gmts_prod_arr[$gmtsRow[csf('po_id')]][$gmtsRow[csf('item_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][2]['special']=$gmtsRow[csf('special')];
			
			$gmts_prod_arr[$gmtsRow[csf('po_id')]][$gmtsRow[csf('item_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][3]['print']=$gmtsRow[csf('printr')];
			$gmts_prod_arr[$gmtsRow[csf('po_id')]][$gmtsRow[csf('item_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][3]['emb']=$gmtsRow[csf('embr')];
			$gmts_prod_arr[$gmtsRow[csf('po_id')]][$gmtsRow[csf('item_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][3]['wash']=$gmtsRow[csf('washr')];
			$gmts_prod_arr[$gmtsRow[csf('po_id')]][$gmtsRow[csf('item_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][3]['special']=$gmtsRow[csf('specialr')];*/
			
			if($gmtsRow[csf('production_type')]==2 || $gmtsRow[csf('production_type')]==3)
			{
				$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][$gmtsRow[csf('production_type')]][$emb_arr[$gmtsRow[csf('embel_name')]]]+=$gmtsRow[csf('production_quantity')];
			}
			
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][$gmtsRow[csf('production_type')]]['prQty']+=$gmtsRow[csf('production_quantity')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][$gmtsRow[csf('production_type')]]['pQty']+=$gmtsRow[csf('production_quantity')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][$gmtsRow[csf('production_type')]]['cQty']+=$gmtsRow[csf('production_quantity')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][$gmtsRow[csf('production_type')]]['sQty']+=$gmtsRow[csf('production_quantity')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][$gmtsRow[csf('production_type')]]['soQty']+=$gmtsRow[csf('production_quantity')];
			
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][$gmtsRow[csf('production_type')]]['iQty']+=$gmtsRow[csf('production_quantity')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][$gmtsRow[csf('production_type')]]['riQty']+=$gmtsRow[csf('re_production_qty')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][$gmtsRow[csf('production_type')]]['fQty']+=$gmtsRow[csf('production_quantity')];
			
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][$gmtsRow[csf('production_type')]]['crQty']+=$gmtsRow[csf('reject_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][$gmtsRow[csf('production_type')]]['sorQty']+=$gmtsRow[csf('reject_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][$gmtsRow[csf('production_type')]]['frQty']+=$gmtsRow[csf('reject_qnty')];
		}
		unset($res_gmtsData);








		$result=sql_select($order_sql);
		$i=0; $k=0; $date=date("Y-m-d"); 
		foreach($result as $orderRes)
		{ 
		   	$i++;
		   	if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
		   	$setArr = explode("__",$orderRes[csf("set_break_down")] );
		  	$countArr = count($setArr); 
		   	if($countArr==0) $countArr=1; 
		   	for($j=0;$j<$countArr;$j++)
			{	
				$company_name=$orderRes[csf("company_name")];
				$po_id=$orderRes[csf("id")];
				$location=$orderRes[csf("location")];
				$floor_id=$orderRes[csf("floor_id")];
					
			   $setItemArr = explode("_",$setArr[$j]);
			   $item_id=$setItemArr[0];
			   $set_qnty=$setItemArr[1];
			   $smv_arr[$setItemArr[0]]=$setItemArr[2];
			   if($item_id>0)
			   {
				   	$k++;
				   	$po_quantity_in_pcs = $orderRes[csf("po_quantity")]*$set_qnty;
					$unit_price=$orderRes[csf("unit_price")]/$set_qnty;
				   	$ex_factory_date=$ex_factory_arr[$orderRes[csf("id")]][$item_id][$location]['date'];
					$ex_factory_qnty=$ex_factory_arr[$orderRes[csf("id")]][$item_id][$location]['qty'];
					$color=""; $days_remian="";
					if($orderRes[csf("shiping_status")]==1 || $orderRes[csf("shiping_status")]==2)
					{
						$days_remian=datediff("d",$date,$orderRes[csf("shipment_date")])-1; 
						if($orderRes[csf("shipment_date")] > $date) 
						{
							$color="";
						}
						else if($orderRes[csf("shipment_date")] < $date) 
						{
							$color="red";
						}														
						else if($orderRes[csf("shipment_date")] >= $date && $days_remian<=5 ) 
						{
							$color="orange";
						}
					} 
					else if($orderRes[csf("shiping_status")]==3)
					{
						$days_remian=datediff("d",$ex_factory_date,$orderRes[csf("shipment_date")])-1;
						if($orderRes[csf("shipment_date")] >= $ex_factory_date) 
						{ 
							$color="green";
						}
						else if($orderRes[csf("shipment_date")] < $ex_factory_date) 
						{ 
							$color="#2A9FFF";
						}
						
					}//end if condition
					
					$cutting_qnty=$gmts_prod_arr[$orderRes[csf("id")]][$item_id][$location][$floor_id][1]['cQty'];
					$embl_recv_qnty=$gmts_prod_arr[$orderRes[csf("id")]][$item_id][$location][$floor_id][3]['prQty'];
					$sewingin_qnty=$gmts_prod_arr[$orderRes[csf("id")]][$item_id][$location][$floor_id][4]['sQty'];
					$sewingout_qnty=$gmts_prod_arr[$orderRes[csf("id")]][$item_id][$location][$floor_id][5]['soQty'];
					$iron_qnty=$gmts_prod_arr[$orderRes[csf("id")]][$item_id][$location][$floor_id][7]['iQty'];
					$re_iron_qnty=$gmts_prod_arr[$orderRes[csf("id")]][$item_id][$location][$floor_id][7]['riQty'];
					$finish_qnty=$gmts_prod_arr[$orderRes[csf("id")]][$item_id][$location][$floor_id][8]['fQty'];
					if($orderRes[csf("buyer_name")]!="")
					{
						$buyer_array[$orderRes[csf("buyer_name")]]['buyer_name']=$orderRes[csf("buyer_name")];
						$buyer_array[$orderRes[csf("buyer_name")]]['poQty']+=$po_quantity_in_pcs;
						$buyer_array[$orderRes[csf("buyer_name")]]['poVal']+=$po_quantity_in_pcs*$unit_price;
						$buyer_array[$orderRes[csf("buyer_name")]]['ex']+=$ex_factory_qnty;
						$buyer_array[$orderRes[csf("buyer_name")]]['cQty']+=$cutting_qnty;
						$buyer_array[$orderRes[csf("buyer_name")]]['prQty']+=$embl_recv_qnty;
						$buyer_array[$orderRes[csf("buyer_name")]]['sQty']+=$sewingin_qnty;
						$buyer_array[$orderRes[csf("buyer_name")]]['soQty']+=$sewingout_qnty;
						$buyer_array[$orderRes[csf("buyer_name")]]['iQty']+=$iron_qnty;
						$buyer_array[$orderRes[csf("buyer_name")]]['reiQty']+=$re_iron_qnty;
						$buyer_array[$orderRes[csf("buyer_name")]]['fQty']+=$finish_qnty;
					}
					
					$actual_exces_cut = $gmts_prod_arr[$orderRes[csf("id")]][$item_id][$location][$floor_id][1]['cQty'];//$gmts_prod_arr[$orderRes[csf("id")]][$item_id][1]['cQty'];
					if($actual_exces_cut < $po_quantity_in_pcs) $actual_exces_cut=""; else $actual_exces_cut=number_format((($actual_exces_cut-$po_quantity_in_pcs)/$po_quantity_in_pcs)*100,2);

					$issue_print = $gmts_prod_arr[$orderRes[csf("id")]][$item_id][$location][$floor_id][2]['print'];
					$issue_emb = $gmts_prod_arr[$orderRes[csf("id")]][$item_id][2][$location][$floor_id]['emb'];
					$issue_wash = $gmts_prod_arr[$orderRes[csf("id")]][$item_id][2][$location][$floor_id]['wash'];
					$issue_special = $gmts_prod_arr[$orderRes[csf("id")]][$item_id][$location][$floor_id][2]['special'];
					$issue_gd = $gmts_prod_arr[$orderRes[csf("id")]][$item_id][$location][$floor_id][2]['gmt'];
					
					$embl_iss_qty=$issue_print+$issue_emb+$issue_wash+$issue_special+$issue_gd;
					
					$embl_issue_total="";
					if($issue_print!=0) $embl_issue_total .= 'PR='.$issue_print;
					if($issue_emb!=0) $embl_issue_total .= ', EM='.$issue_emb;
					if($issue_wash!=0) $embl_issue_total .= ', WA='.$issue_wash;
					if($issue_special!=0) $embl_issue_total .= ', SP='.$issue_special;
					if($issue_gd!=0) $embl_issue_total .= ', GD='.$issue_gd;

					$rcv_print = $gmts_prod_arr[$orderRes[csf("id")]][$item_id][$location][$floor_id][3]['print'];
					$rcv_emb = $gmts_prod_arr[$orderRes[csf("id")]][$item_id][$location][$floor_id][3]['emb'];
					$rcv_wash = $gmts_prod_arr[$orderRes[csf("id")]][$item_id][$location][$floor_id][3]['wash'];
					$rcv_special = $gmts_prod_arr[$orderRes[csf("id")]][$item_id][$location][$floor_id][3]['special'];
					$rcv_gd = $gmts_prod_arr[$orderRes[csf("id")]][$item_id][$location][$floor_id][3]['gmt'];
					
					$embl_receive_total="";	
					if($rcv_print!=0) $embl_receive_total .= 'PR='.$rcv_print;
					if($rcv_emb!=0) $embl_receive_total .= ', EM='.$rcv_emb;
					if($rcv_wash!=0) $embl_receive_total .= ', WA='.$rcv_wash;
					if($rcv_special!=0) $embl_receive_total .= ', SP='.$rcv_special;
					if($rcv_gd!=0) $embl_receive_total .= ', GD='.$rcv_gd;
					
					$embl_recv_qty=$rcv_print+$rcv_emb+$rcv_wash+$rcv_special+$rcv_gd;
					
					//$rej_value=$proRes[csf("finish_rej_qnty")]+$proRes[csf("sewingout_rej_qnty")];	
					$rej_value=$gmts_prod_arr[$orderRes[csf("id")]][$item_id][$location][$floor_id][1]['crQty']+$gmts_prod_arr[$orderRes[csf("id")]][$item_id][$location][$floor_id][5]['sorQty']+$gmts_prod_arr[$orderRes[csf("id")]][$item_id][$location][$floor_id][8]['frQty'];
					$shortage = $po_quantity_in_pcs-$ex_factory_qnty;
					$finish_status = $finish_qnty*100/$po_quantity_in_pcs;
					
					if($j==0) 
					{
						$display_font_color="";
						$font_end="";
					}
					else 
					{
						$display_font_color="&nbsp;<font style='display:none'>";
						$font_end="</font>";
					}
					
					if(($ex_factory_date=="" || $ex_factory_date=="0000-00-00")) $ex_factory_date="&nbsp;"; else $ex_factory_date=change_date_format($ex_factory_date);
					//if($orderRes[csf("shiping_status")]==3) $days_remian=$days_remian; else $days_remian="---";
					if($actual_exces_cut > number_format($orderRes[csf("excess_cut")],2)) $excess_bgcolor="bgcolor='#FF0000'"; else $excess_bgcolor="";
					if($actual_exces_cut==0) $actual_exces_cut_per=""; else $actual_exces_cut_per=$actual_exces_cut." %";
					$total_rej_value+=$rej_value; 
				   	$html.="<tr bgcolor='$bgcolor' onclick=change_color('tr_2nd$k','$bgcolor') id=tr_2nd$k>";
					$html.='<td width="30">'.$display_font_color.$i.$font_end.'</td>
								<td width="130"><p>'.$display_font_color.$orderRes[csf("po_number")].$font_end.'</p></td>
								<td width="60"><p>'.$display_font_color.$buyer_short_library[$orderRes[csf("buyer_name")]].$font_end.'</p></td>
								<td width="50" align="center"><p>'.$display_font_color.$orderRes[csf("job_year")].$font_end.'</p></td>
								<td width="80" align="center"><p>'.$display_font_color.$orderRes[csf("job_no_prefix_num")].$font_end.'</p></td>
								<td width="120"><p>'.$display_font_color.$orderRes[csf("style_ref_no")].$font_end.'</p></td>
								<td width="80"><p>'.$display_font_color.$team_name_arr[$orderRes[csf("team_leader")]].$font_end.'</p></td>
								<td width="90"><p>'.$display_font_color.$team_member_arr[$orderRes[csf("dealing_marchant")]].$font_end.'</p></td>
								<td width="130"><p>'.$garments_item[$item_id].'</p></td>
								<td width="50"><p>'.$smv_arr[$item_id].'</p></td>
								<td width="80" align="right"><a href="##" onclick="openmypage_order('.$po_id.",".$company_name.",".$item_id.",0,'OrderPopup'".')">'.$display_font_color.$po_quantity_in_pcs.$font_end.'</a></td>
								<td width="80" align="center" bgcolor="'.$color.'">'.change_date_format($orderRes[csf("shipment_date")]).'</td>
                                <td width="80"><p>'.$location_library[$orderRes[csf("location")]].'&nbsp;</p></td>
                                <td width="80"><p>'.$floor_library[$orderRes[csf("floor_id")]].'&nbsp;</p></td>
								<td width="80" align="right"><a href="##" onclick="openmypage('.$po_id.",".$item_id.",'exfactory','$location','','','0'".')">'.$ex_factory_date.'</a></td>
								<td width="80" align="center">'.$days_remian.'</td>
								<td width="80" align="right">'.number_format($orderRes[csf("excess_cut")],2)." %".'</td>
								<td width="80" align="right"><a href="##" onclick="openmypage('.$po_id.",".$item_id.",'1','$location','$floor_id','$type',''".')">'.$cutting_qnty.'</a></td>
								
								<td width="80" align="right">'.($po_quantity_in_pcs-$cutting_qnty).'</td>
								
								<td width="80" align="right" '.$excess_bgcolor.'>'.$actual_exces_cut_per.'</td>
								<td width="80" align="right"><font color="$bgcolor" style="display:none">'.$embl_iss_qty.'</font>&nbsp;<a href="##" onclick="openmypage('.$po_id.",".$item_id.",'2','','','$type',''".')">'.$embl_issue_total.'</a></td>
								<td width="80" align="right"><font color="$bgcolor" style="display:none">'.$embl_recv_qty.'</font><a href="##" onclick="openmypage('.$po_id.",".$item_id.",'3','','','$type',''".')">'.$embl_receive_total.'</a></td>
								<td width="80" align="right"><a href="##" onclick="openmypage('.$po_id.",".$item_id.",'4','$location','$floor_id','$type',''".')">'.$sewingin_qnty.'</a></td>
								<td width="80" align="right"><a href="##" onclick="openmypage('.$po_id.",".$item_id.",'5','$location','$floor_id','$type',''".')">'.$sewingout_qnty.'</a></td>
								<td width="80" align="right"><a href="##" onclick="openmypage('.$po_id.",".$item_id.",'7','$location','$floor_id','$type',''".')">'.$iron_qnty.'</a></td>
								<td width="80" align="right"><a href="##" onclick="openmypage('.$po_id.",".$item_id.",'9','$location','$floor_id','$type',''".')">'.$re_iron_qnty.'</a></td>
								<td width="80" align="right"><a href="##" onclick="openmypage('.$po_id.",".$item_id.",'8','$location','$floor_id','$type',''".')">'.$finish_qnty.'</a></td>
								<td width="80" align="right">'.number_format($finish_status,2)." %".'</td>
								<td width="80" align="right"><a href="##" onclick="openmypage_rej('.$po_id.",".$item_id.",'reject_qty','$location','$floor_id','$type',''".')">'.$rej_value.'</a></td>
								<td width="80" align="right">'.$ex_factory_qnty.'</td>
								<td width="80" align="right">'.$shortage.'</td>
								<td width="85">'.$shipment_status[$orderRes[csf("shiping_status")]].'</td>
								<td>&nbsp;<a href="##" onclick="openmypage_remark('.$po_id.",".$item_id.",0,'date_wise_production_report'".')">Veiw</a></td>
							</tr>';
				}
			} //end for loop
		}// end main foreach 
	    ?>
	    <div>
	    	<table width="1580" cellspacing="0" >
	            <tr class="form_caption" style="border:none;">
	                <td colspan="29" align="center" style="border:none;font-size:16px; font-weight:bold" >
	                    <? echo "Order Location & Floor Wise Production Report"; ?>    
	                </td>
	            </tr>
	            <tr style="border:none;">
	                <td colspan="29" align="center" style="border:none; font-size:14px;">
	                    Company Name : <? echo $company_library[str_replace("'","",$cbo_company_name)]; ?>                                
	                </td>
	            </tr>
	            <tr style="border:none;">
	                <td colspan="29" align="center" style="border:none;font-size:12px; font-weight:bold">
	                    <?
	                        if(str_replace("'","",trim($txt_date_from))!="" && str_replace("'","",trim($txt_date_to))!="")
	                        {
	                            echo "From $fromDate To $toDate" ;
	                        }
	                    ?>
	                </td>
	            </tr>
	        </table>
			<div style="float:left; width:1230px">
	            <table width="1180" cellspacing="0" border="1" class="rpt_table" rules="all" id="" >
	                <thead>
	                    <tr>
	                        <th colspan="15" >In-House Order Production</th>
	                    </tr>
	                    <tr>
	                        <th width="30">Sl.</th>    
	                        <th width="80">Buyer Name</th>
	                        <th width="80">Order Qty.(Pcs)</th>
	                        <th width="80">PO Value</th>
	                        <th width="80">Total Cut Qty</th>
	                        <th width="80">Cutting Balance</th>
	                        <th width="80">Total Emb. Rcv. Qty</th>
	                        <th width="80">Total Sew Input Qty</th>
	                        <th width="80">Total Sew Qty</th>
	                        <th width="80">Total Iron Qty</th>
	                        <th width="80">Total Re-Iron Qty</th>
	                        <th width="80">Total Finish Qty</th>
	                        <th width="80">Fin Goods Status</th>
	                        <th width="80">Ex-Fac</th>
	                        <th>Ex-Fac%</th>
	                     </tr>
	                </thead>
	            </table>
	            <div style="max-height:425px; overflow-y:scroll; width:1200px" >
	                <table cellspacing="0" border="1" class="rpt_table"  width="1180" rules="all" id="" >
	                <?
						$i=1; $total_po_quantity=0;$total_po_value=0; $total_cut=0; $total_print_iss=0;
						$total_print_re=0; $total_sew_input=0; $total_sew_out=0; $total_iron=0; $total_re_iron=0; $total_finish=0;$total_ex_factory=0;
						$buyer_po_value=array();
						foreach($buyer_array as $row)
						{
							$buyer_po_value[$row["poVal"]]=$row["poVal"];
						}
						array_multisort($buyer_po_value, SORT_DESC,$buyer_array);
						foreach($buyer_array as $buyer_id=>$value)
	                    {
	                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							
	                        ?>
	                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
	                            <td width="30"><? echo $i;?></td>
	                            <td width="80"><? echo $buyer_short_library[$value["buyer_name"]]; ?></td>
	                            <td width="80" align="right"><? echo number_format($value["poQty"]);?></td>
	                            <td width="80" align="right"><? echo number_format($value["poVal"],2);?></td>
	                            <td width="80" align="right"><? echo number_format($value["cQty"]); ?></td>
	                            <td width="80" align="right"><? echo number_format($value["poQty"]-$value["cQty"]); ?></td>
	                            
	                            <td width="80" align="right"><? echo number_format($value["prQty"]); ?></td>
	                            <td width="80" align="right"><? echo number_format($value["sQty"]); ?></td>
	                            <td width="80" align="right"><? echo number_format($value["soQty"]); ?></td>
	                            <td width="80" align="right"><? echo number_format($value["iQty"]); ?></td>
	                            <td width="80" align="right"><? echo number_format($value["reiQty"]); ?></td>
	                            <td width="80" align="right"><? echo number_format($value["fQty"]); ?></td>
	                            <? $finish_gd_status = ($value["fQty"]/$value["poQty"])*100; ?>
	                            <td width="80" align="right"><? echo number_format($finish_gd_status,2)." %"; ?></td>
	                            <td width="80" align="right"><? echo number_format($value["ex"]); ?></td>
	                            <? $ex_gd_status = ($value["ex"]/$value["poQty"])*100; ?>
	                            <td width="" align="right"><? echo  number_format($ex_gd_status,2)." %"; ?></td>
	                        </tr>	
	                        <?		
	                            $total_po_quantity+=$value["poQty"];
	                            $total_po_value+=$value["poVal"];
	                            $total_cut+=$value["cQty"];
	                            $total_print_re+=$value["prQty"];
	                            $total_sew_input+=$value["sQty"];
	                            $total_sew_out+=$value["soQty"];
	                            $total_iron+=$value["iQty"];
	                            $total_re_iron+=$value["reiQty"];
	                            $total_finish+=$value["fQty"];
	                            $total_ex_factory+=$value["ex"];
	                           
	                        $i++;
	                    }//end foreach 1st
	                    
	                    $chart_data_qnty="Order Qty;".$total_po_quantity."\n"."Cutting;".$total_cut."\n"."Sew In;".$total_sew_input."\n"."Sew Out ;".$total_sew_out."\n"."Iron ;".$total_iron."\n"."Finish ;".$total_finish."\n"."Ex-Fact;".$total_ex_factory."\n";
	                ?>
	                </table>
	                <table border="1" class="tbl_bottom" width="1180" rules="all" id="" >
	                    <tr> 
	                        <td width="30">&nbsp;</td> 
	                        <td width="80" align="right">Total</td> 
	                        <td width="80" id="tot_po_quantity"><? echo number_format($total_po_quantity); ?></td> 
	                        <td width="80" id="tot_po_value"><? echo number_format($total_po_value,2); ?></td> 
	                        <td width="80" id="tot_cutting"><? echo number_format($total_cut); ?></td>
	                        <td width="80" id="tot_cutting"><? echo number_format($total_po_quantity-$total_cut); ?></td>
	                        <td width="80" id="tot_emb_rcv"><? echo number_format($total_print_re); ?></td> 
	                        <td width="80" id="tot_sew_in"><? echo number_format($total_sew_input); ?></td> 
	                        <td width="80" id="tot_sew_out"><? echo number_format($total_sew_out); ?></td>   
	                        <td width="80" id="tot_iron"><? echo number_format($total_iron); ?></td> 
	                        <td width="80" id="tot_re_iron"><? echo number_format($total_re_iron); ?></td> 
	                        <td width="80" id="tot_finish"><? echo number_format($total_finish); ?></td>
	                        <? $total_finish_gd_status = ($total_finish/$total_po_quantity)*100; ?>
	                        <td width="80"><? echo number_format($total_finish_gd_status,2); ?></td >
	                        <td width="80"><? echo number_format($total_ex_factory); ?></td >
	                        <? $total_ex_status = ($total_ex_factory/$total_po_quantity)*100; ?>
	                        <td width=""><? echo number_format($total_ex_status,2); ?></td>
	                    </tr>
	                 </table>
	                 <input type="hidden" id="graph_data" value="<? echo substr($chart_data_qnty,0,-1); ?>"/>
	            </div>
	        </div>
	        <div style="float:left; width:600px">   
	            <table>
	                <tr>
	                    <td height="21" width="600"><div id="chartdiv"> </div></td>
	                </tr>    
	            </table>
	        </div> 
	        <div style="clear:both"></div>
	        <table width="2700" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1">
	            <thead>
	                <tr>
	                    <th width="30">SL</th>    
	                    <th width="130">Order Number</th>
	                    <th width="60">Buyer Name</th>
	                    <th width="50">Job Year</th>
	                    <th width="80">Job Number</th>
	                    <th width="120">Style Name</th>
	                    <th width="80">Team Name</th>
	                    <th width="90">Team Member</th>
	                    <th width="130">Item Name</th>
	                    <th width="50">SMV</th>
	                    <th width="80">Order Qty.</th>
	                    <th width="80">Ship Date</th>
	                    <th width="80">Location</th>
	                    <th width="80">Floor</th>
	                    <th width="80">Ex-Factory Date</th>
	                    <th width="80">Days in Hand</th>
	                    <th width="80">Stan. Exc. Cut %</th>
	                    <th width="80">Total Cut Qty</th>
	                    <th width="80">Cutting Balance</th>
	                    <th width="80">Actual Exc. Cut %</th>
	                    <th width="80">Total Emb. Issue Qty</th>
	                    <th width="80">Total Emb. Rcv. Qty</th>
	                    <th width="80">Total Sew Input Qty</th>
	                    <th width="80">Total Sew Output Qty</th>
	                    <th width="80">Total Iron Qty</th>
	                    <th width="80">Total Re-Iron Qty</th>
	                    <th width="80">Total Finish Qty</th>
	                    <th width="80">Fin Goods Status</th>
	                    <th width="80">Reject Qty</th>
	                    <th width="80">Total Ship Out</th>
	                    <th width="80">Shortage/ Excess</th>
	                    <th width="85">Shipping Status</th>
	                    <th>Remarks</th>
	                 </tr>
	            </thead>
	        </table>
	        <div style="max-height:425px; overflow-y:scroll; width:2700px" id="scroll_body">
	            <table border="1" class="rpt_table" width="2680" rules="all" id="table_body">
					<? echo $html; ?>  
	            </table>	
	            <table border="1" class="tbl_bottom" width="2680" rules="all" id="report_table_footer_1" >
	                <tr>
	                    <td width="30">&nbsp;</td>
	                    <td width="130">&nbsp;</td>
	                    <td width="60">&nbsp;</td>
	                    <td width="50">&nbsp;</td>
	                    <td width="80">&nbsp;</td>
	                    <td width="120">&nbsp;</td>
	                    <td width="80">&nbsp;</td>
	                    <td width="90">&nbsp;</td>
	                    <td width="130">Total</td> 
	                    <td width="50">&nbsp;</td>
	                    <td width="80" id="total_order_quantity">&nbsp;</td>
	                    <td width="80">&nbsp;</td>
	                    <td width="80">&nbsp;</td>
	                    <td width="80">&nbsp;</td>
	                    <td width="80">&nbsp;</td>
	                    <td width="80">&nbsp;</td>
	                    <td width="80">&nbsp;</td>
	                    <td width="80" id="total_cutting">&nbsp;</td>
	                    <td width="80" id="total_cutting_bal">&nbsp;</td>
	                    <td width="80">&nbsp;</td>
	                    <td width="80" id="total_emb_issue">&nbsp;</td>
	                    <td width="80" id="total_emb_receive">&nbsp;</td>
	                    <td width="80" id="total_sewing_input">&nbsp;</td>
	                    <td width="80" id="total_sewing_out">&nbsp;</td>
	                    <td width="80" id="total_iron_qnty">&nbsp;</td>
	                    <td width="80" id="total_re_iron_qnty">&nbsp;</td>
	                    <td width="80" id="total_finish_qnty">&nbsp;</td>  
	                    <td width="80">&nbsp;</td>
	                    <td width="80" align="right" id="total_rej_value_td">&nbsp;</td>
	                    <td width="80" id="total_out">&nbsp;</td>
	                    <td width="80" id="total_shortage">&nbsp;</td>
	                    <td width="85" id="ship_status">&nbsp;</td>
	                    <td>&nbsp;</td>
	                 </tr>
				</table>
	        </div>
	     </div>   
	    <?
    }
	else if($type==5 || $type==7)// 5 is Style Wise and 7 is Job wise
	{
		
		$ex_factory_arr=array();
		$ex_factory_data=sql_select("select po_break_down_id, item_number_id, MAX(ex_factory_date) AS ex_factory_date,sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END)-sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) AS ex_factory_qnty from pro_ex_factory_mst where status_active=1 and is_deleted=0 group by po_break_down_id, item_number_id");
		foreach($ex_factory_data as $exRow)
		{
			$ex_factory_arr[$exRow[csf('po_break_down_id')]][$exRow[csf('item_number_id')]]['date']=$exRow[csf('ex_factory_date')];
			$ex_factory_arr[$exRow[csf('po_break_down_id')]][$exRow[csf('item_number_id')]]['qty']=$exRow[csf('ex_factory_qnty')];
		}
		//print_r($ex_factory_arr[107]);die;
		
		unset($ex_factory_data);
		//------------------------------------
		$buyer_array=array(); 
		if($db_type==0)
		{
			$select_job_year="year(b.insert_date) as job_year";
			$order_sql="select group_concat(distinct(a.id)) as id, b.job_no_prefix_num, $select_job_year, group_concat(distinct(a.po_number)) as po_number, group_concat(concat_ws('**',a.id,a.po_quantity,a.unit_price)) as po_data, sum(a.po_quantity) as po_quantity, a.job_no_mst, MAX(a.pub_shipment_date) as shipment_date, a.shiping_status, avg(a.excess_cut) as excess_cut, sum(a.plan_cut) as plan_cut,min(DATE_FORMAT(a.insert_date,'%d-%b-%Y')) as po_insert_date, b.company_name, b.buyer_name, b.set_break_down, b.team_leader, b.dealing_marchant, b.style_ref_no from wo_po_details_master b,wo_po_break_down a where a.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $style_cond $year_cond $txt_date $company_name $buyer_name $season_cond $garmentsNature $team_name_cond $team_member_cond $order_status_cond $item_cat_cond  group by b.id";		
		}
		else
		{
			$select_job_year="to_char(b.insert_date,'YYYY') as job_year";
			$order_sql="select LISTAGG(a.id, ',') WITHIN GROUP (ORDER BY a.id) as id, LISTAGG(a.id || '**' || a.po_quantity || '**' || a.unit_price, ',') WITHIN GROUP (ORDER BY a.id) as po_data, b.job_no_prefix_num, $select_job_year, LISTAGG(a.po_number, ',') WITHIN GROUP (ORDER BY a.id) as po_number, sum(a.po_quantity) as po_quantity, MAX(a.pub_shipment_date) as shipment_date, b.job_no as job_no_mst, min(a.shiping_status) as shiping_status, avg(a.excess_cut) as excess_cut, sum(a.plan_cut) as plan_cut, min(to_char(a.insert_date,'dd-mm-yyyy')) as po_insert_date, b.company_name, b.buyer_name, b.set_break_down, b.team_leader, b.dealing_marchant, b.style_ref_no from wo_po_details_master b,wo_po_break_down a where a.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $style_cond $year_cond $txt_date $company_name $buyer_name $season_cond $garmentsNature $team_name_cond $team_member_cond $order_status_cond $item_cat_cond  group by b.id, b.job_no, b.job_no_prefix_num, b.insert_date, b.company_name, b.buyer_name, b.set_break_down, b.team_leader, b.dealing_marchant, b.style_ref_no";	
		}
		//echo $order_sql;
		$result=sql_select($order_sql);
		
		//------------------------------------------------
		$po_id_str="";
		foreach($result as $rows)
		{ 
			$job_arr[$rows[csf("job_no_mst")]]=$rows[csf("job_no_mst")];
			$po_id_str.=$rows[csf("id")].',';
		}
		$job_string=implode("','",$job_arr);
		$po_id_strs=chop($po_id_str,',');
		//echo $po_id_strs;

		if ($db_type==0) 
		{
			if($po_id_strs!="") {$po_id_strs_cond="and c.po_break_down_id in($po_id_strs)";}else{$po_id_strs_cond="";}
		}
		else
		{
				$poID=explode(",",$po_id_strs);  

				$poID=array_chunk($poID,999);
				$po_id_strs_cond=" and";
				foreach($poID as $dtls_id)
				{
				if($po_id_strs_cond==" and")  $po_id_strs_cond.="(c.po_break_down_id in(".implode(',',$dtls_id).")"; else $po_id_strs_cond.=" or c.po_break_down_id in(".implode(',',$dtls_id).")";
				}
				$po_id_strs_cond.=")";
				//echo $po_id_strs_cond;die;
		}
		//------------------------

		if($db_type==0)
		{
			$prod_sql="SELECT c.po_break_down_id, c.item_number_id,
				IFNULL(sum(CASE WHEN c.production_type ='1' and d.production_type ='1' THEN d.production_qnty ELSE 0 END),0) AS cutting_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='2' and d.production_type ='2' THEN d.production_qnty ELSE 0 END),0) AS printing_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='2' and d.production_type ='2' and c.embel_name=1 THEN d.production_qnty ELSE 0 END),0) AS print,
				IFNULL(sum(CASE WHEN c.production_type ='2' and d.production_type ='2' and c.embel_name=2 THEN d.production_qnty ELSE 0 END),0) AS emb,
				IFNULL(sum(CASE WHEN c.production_type ='2' and d.production_type ='2' and c.embel_name=3 THEN d.production_qnty ELSE 0 END),0) AS wash,
				IFNULL(sum(CASE WHEN c.production_type ='2' and d.production_type ='2' and c.embel_name=4 THEN d.production_qnty ELSE 0 END),0) AS special,
				IFNULL(sum(CASE WHEN c.production_type ='2' and d.production_type ='2' and c.embel_name=5 THEN d.production_qnty ELSE 0 END),0) AS gmt_dyeing,
				IFNULL(sum(CASE WHEN c.production_type ='3' and d.production_type ='3' THEN d.production_qnty ELSE 0 END),0) AS printreceived_qnty, 
				IFNULL(sum(CASE WHEN c.production_type ='3' and d.production_type ='3' and c.embel_name=1 THEN d.production_qnty ELSE 0 END),0) AS printr,
				IFNULL(sum(CASE WHEN c.production_type ='3' and d.production_type ='3' and c.embel_name=2 THEN d.production_qnty ELSE 0 END),0) AS embr,
				IFNULL(sum(CASE WHEN c.production_type ='3' and d.production_type ='3' and c.embel_name=3 THEN d.production_qnty ELSE 0 END),0) AS washr,
				IFNULL(sum(CASE WHEN c.production_type ='3' and d.production_type ='3' and c.embel_name=4 THEN d.production_qnty ELSE 0 END),0) AS specialr,
				IFNULL(sum(CASE WHEN c.production_type ='3' and d.production_type ='3' and c.embel_name=5 THEN d.production_qnty ELSE 0 END),0) AS gmt_dyeingr,
				IFNULL(sum(CASE WHEN c.production_type ='4' and d.production_type ='4' THEN d.production_qnty ELSE 0 END),0) AS sewingin_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='5' and d.production_type ='5' THEN d.production_qnty ELSE 0 END),0) AS sewingout_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='7' and d.production_type ='7' THEN d.production_qnty ELSE 0 END),0) AS iron_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='7' and d.production_type ='7' THEN c.re_production_qty ELSE 0 END),0) AS re_iron_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='8' and d.production_type ='8' THEN d.production_qnty ELSE 0 END),0) AS finish_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='1' and d.production_type ='1' THEN c.reject_qnty ELSE 0 END),0) AS cutting_rej_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='3' and d.production_type ='3' THEN c.reject_qnty ELSE 0 END),0) AS emb_rej_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='5' and d.production_type ='5' THEN c.reject_qnty ELSE 0 END),0) AS sewingout_rej_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='7' and d.production_type ='7' THEN c.reject_qnty ELSE 0 END),0) AS iron_rej_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='8' and d.production_type ='8' THEN c.reject_qnty ELSE 0 END),0) AS finish_rej_qnty
				
			from 
				pro_garments_production_mst c,pro_garments_production_dtls d 
			where  
				c.id=d.mst_id and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.company_id=$cbo_company_name $po_id_strs_cond $floor $location group by c.po_break_down_id, c.item_number_id";
		}
		else
		{
			$prod_sql= "SELECT c.po_break_down_id, c.item_number_id, 
				NVL(sum(CASE WHEN c.production_type ='1' and d.production_type ='1' THEN d.production_qnty ELSE 0 END),0) AS cutting_qnty,
				NVL(sum(CASE WHEN c.production_type ='2' and d.production_type ='2' THEN d.production_qnty ELSE 0 END),0) AS printing_qnty,
				NVL(sum(CASE WHEN c.production_type ='2' and d.production_type ='2' and c.embel_name=1 THEN d.production_qnty ELSE 0 END),0) AS print,
				NVL(sum(CASE WHEN c.production_type ='2' and d.production_type ='2' and c.embel_name=2 THEN d.production_qnty ELSE 0 END),0) AS emb,
				NVL(sum(CASE WHEN c.production_type ='2' and d.production_type ='2' and c.embel_name=3 THEN d.production_qnty ELSE 0 END),0) AS wash,
				NVL(sum(CASE WHEN c.production_type ='2' and d.production_type ='2' and c.embel_name=4 THEN d.production_qnty ELSE 0 END),0) AS special,
				NVL(sum(CASE WHEN c.production_type ='2' and d.production_type ='2' and c.embel_name=5 THEN d.production_qnty ELSE 0 END),0) AS gmt_dyeing,
				NVL(sum(CASE WHEN c.production_type ='3' and d.production_type ='3' THEN d.production_qnty ELSE 0 END),0) AS printreceived_qnty, 
				NVL(sum(CASE WHEN c.production_type ='3' and d.production_type ='3' and c.embel_name=1 THEN d.production_qnty ELSE 0 END),0) AS printr,
				NVL(sum(CASE WHEN c.production_type ='3' and d.production_type ='3' and c.embel_name=2 THEN d.production_qnty ELSE 0 END),0) AS embr,
				NVL(sum(CASE WHEN c.production_type ='3' and d.production_type ='3' and c.embel_name=3 THEN d.production_qnty ELSE 0 END),0) AS washr,
				NVL(sum(CASE WHEN c.production_type ='3' and d.production_type ='3' and c.embel_name=4 THEN d.production_qnty ELSE 0 END),0) AS specialr,
				NVL(sum(CASE WHEN c.production_type ='3' and d.production_type ='3' and c.embel_name=5 THEN d.production_qnty ELSE 0 END),0) AS gmt_dyeingr,
				NVL(sum(CASE WHEN c.production_type ='4' and d.production_type ='4' THEN d.production_qnty ELSE 0 END),0) AS sewingin_qnty,
				NVL(sum(CASE WHEN c.production_type ='5' and d.production_type ='5' THEN d.production_qnty ELSE 0 END),0) AS sewingout_qnty,
				NVL(sum(CASE WHEN c.production_type ='7' and d.production_type ='7' THEN d.production_qnty ELSE 0 END),0) AS iron_qnty,
				NVL(sum(CASE WHEN c.production_type ='7' and d.production_type ='7' THEN c.re_production_qty ELSE 0 END),0) AS re_iron_qnty,
				NVL(sum(CASE WHEN c.production_type ='8' and d.production_type ='8' THEN d.production_qnty ELSE 0 END),0) AS finish_qnty,
				NVL(sum(CASE WHEN c.production_type ='1' and d.production_type ='1' THEN c.reject_qnty ELSE 0 END),0) AS cutting_rej_qnty,
				NVL(sum(CASE WHEN c.production_type ='3' and d.production_type ='3' THEN c.reject_qnty ELSE 0 END),0) AS emb_rej_qnty,
				NVL(sum(CASE WHEN c.production_type ='5' and d.production_type ='5' THEN c.reject_qnty ELSE 0 END),0) AS sewingout_rej_qnty,
				NVL(sum(CASE WHEN c.production_type ='7' and d.production_type ='7' THEN c.reject_qnty ELSE 0 END),0) AS iron_rej_qnty,
				NVL(sum(CASE WHEN c.production_type ='8' and d.production_type ='8' THEN c.reject_qnty ELSE 0 END),0) AS finish_rej_qnty
				
			from 
				pro_garments_production_mst c,pro_garments_production_dtls d 
			where  
				c.id=d.mst_id and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.company_id=$cbo_company_name $po_id_strs_cond $floor $location group by c.po_break_down_id, c.item_number_id";
		}
		//echo $prod_sql;
		$gmts_prod_arr=array();
		$res_gmtsData=sql_select($prod_sql);
		foreach($res_gmtsData as $gmtsRow)
		{
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][1]['cQty']=$gmtsRow[csf('cutting_qnty')];
			
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][2]['pQty']=$gmtsRow[csf('printing_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][2]['print']=$gmtsRow[csf('print')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][2]['emb']=$gmtsRow[csf('emb')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][2]['wash']=$gmtsRow[csf('wash')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][2]['special']=$gmtsRow[csf('special')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][2]['gmt_dyeing']=$gmtsRow[csf('gmt_dyeing')];
			
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][3]['prQty']=$gmtsRow[csf('printreceived_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][3]['print']=$gmtsRow[csf('printr')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][3]['emb']=$gmtsRow[csf('embr')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][3]['wash']=$gmtsRow[csf('washr')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][3]['special']=$gmtsRow[csf('specialr')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][3]['gmt_dyeingr']=$gmtsRow[csf('gmt_dyeingr')];
			
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][4]['sQty']=$gmtsRow[csf('sewingin_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][5]['soQty']=$gmtsRow[csf('sewingout_qnty')];
			
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][7]['iQty']=$gmtsRow[csf('iron_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][7]['riQty']=$gmtsRow[csf('re_iron_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][8]['fQty']=$gmtsRow[csf('finish_qnty')];
			
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][1]['crQty']=$gmtsRow[csf('cutting_rej_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][3]['embQty']=$gmtsRow[csf('emb_rej_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][5]['sorQty']=$gmtsRow[csf('sewingout_rej_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][7]['irnQty']=$gmtsRow[csf('iron_rej_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][8]['frQty']=$gmtsRow[csf('finish_rej_qnty')];
		}
		unset($res_gmtsData);

		//-------------------

		$smv_result=sql_select("select job_no,gmts_item_id,smv_pcs from wo_po_details_mas_set_details where job_no in('".$job_string."')");
		foreach($smv_result as $rows)
		{ 
			$smvArr[$rows[csf("job_no")].$rows[csf("gmts_item_id")]]=$rows[csf("smv_pcs")];
		}
		
		
		
		$job_string_all="'".$job_string."'";
			
		if($db_type==2)
		{
			$jobIds_cond_approval=" and (";
			$jobArrs=array_chunk(explode(",",$job_string_all),999);
			foreach($jobArrs as $ids)
			{
				$idss=implode(",",$ids);
				$jobIds_cond_approval.=" job_no_mst in($idss) or ";
			}
			$jobIds_cond_approval=chop($jobIds_cond_approval,'or ');
			$jobIds_cond_approval.=")";
		}
		else
		{
			$jobIds_cond_approval=" and job_no_mst in($job_string_all)";
		}

		$ppApprovalDateArr=array();
		$ppApprovalDate_sql=sql_select("select po_break_down_id,job_no_mst,approval_status_date,approval_status from wo_po_sample_approval_info where approval_status=3 $jobIds_cond_approval order by approval_status_date");
		
		
		foreach($ppApprovalDate_sql as $row)
		{
			$ppApprovalDateArr[$row[csf('job_no_mst')]]['approval_status_date']=$row[csf('approval_status_date')];
			$ppApprovalDateArr[$row[csf('job_no_mst')]]['approval_status']=$row[csf('approval_status')];
		}
		
		//----------------------------------------------------
		
		$i=0; $k=0; $date=date("Y-m-d"); 
		foreach($result as $orderRes)
		{ 
		   $i++;
		   if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
		   $setArr = explode("__",$orderRes[csf("set_break_down")] );
		   $countArr = count($setArr); 
		   if($countArr==0) $countArr=1; 
		   for($j=0;$j<$countArr;$j++)
		   {
			   $setItemArr = explode("_",$setArr[$j]);
			   $item_id=$setItemArr[0];
			   $set_qnty=$setItemArr[1];
			   $smv_arr[$setItemArr[0]]=$setItemArr[2];
			   
			   if($item_id>0)
			   {
				   	$k++;
					$po_data=explode(",",$orderRes[csf("po_data")]); 
					$po_quantity_in_pcs=0; $poValue=0; $ex_factory_date=''; $ex_factory_qnty=0; $po_id='';
					$cutting_qnty=0; $embl_recv_qnty=0; $sewingin_qnty=0; $sewingout_qnty=0; $iron_qnty=0; $re_iron_qnty=0; $finish_qnty=0;$rcv_gd=0;$issue_gd=0;
					$issue_print=0; $issue_emb=0; $issue_wash=0; $issue_special=0; $rcv_print=0; $rcv_emb=0; $rcv_wash=0; $rcv_special=0; $rej_value=0;
					foreach($po_data as $value)
					{
						$po_value=explode("**",$value);
						$order_id=$po_value[0];
						$po_quantity=$po_value[1];
						$unit_price=$po_value[2]/$set_qnty;
						$po_quantity_in_pcs+=$po_quantity*$set_qnty;
						$poValue+=$po_quantity*$set_qnty*$unit_price;
						
						if($po_id=='') $po_id=$order_id; else $po_id.=",".$order_id;
						
						$exDate=$ex_factory_arr[$order_id][$item_id]['date'];
						if($exDate > $ex_factory_date) $ex_factory_date=$exDate; 
						$ex_factory_qnty+=$ex_factory_arr[$order_id][$item_id]['qty'];
						
						$cutting_qnty+=$gmts_prod_arr[$order_id][$item_id][1]['cQty'];
						$embl_recv_qnty+=$gmts_prod_arr[$order_id][$item_id][3]['prQty'];
						$sewingin_qnty+=$gmts_prod_arr[$order_id][$item_id][4]['sQty'];
						$sewingout_qnty+=$gmts_prod_arr[$order_id][$item_id][5]['soQty'];
						$iron_qnty+=$gmts_prod_arr[$order_id][$item_id][7]['iQty'];
						$re_iron_qnty+=$gmts_prod_arr[$order_id][$item_id][7]['riQty'];
						$finish_qnty+=$gmts_prod_arr[$order_id][$item_id][8]['fQty'];
						
						$issue_print += $gmts_prod_arr[$order_id][$item_id][2]['print'];
						$issue_emb += $gmts_prod_arr[$order_id][$item_id][2]['emb'];
						$issue_wash += $gmts_prod_arr[$order_id][$item_id][2]['wash'];
						$issue_special += $gmts_prod_arr[$order_id][$item_id][2]['special'];
						$issue_gd += $gmts_prod_arr[$order_id][$item_id][2]['gmt_dyeing'];
						
						$rcv_print += $gmts_prod_arr[$order_id][$item_id][3]['print'];
						$rcv_emb += $gmts_prod_arr[$order_id][$item_id][3]['emb'];
						$rcv_wash += $gmts_prod_arr[$order_id][$item_id][3]['wash'];
						$rcv_special += $gmts_prod_arr[$order_id][$item_id][3]['special'];
						$rcv_gd += $gmts_prod_arr[$order_id][$item_id][3]['gmt_dyeingr'];
						
						$rej_value+=$gmts_prod_arr[$order_id][$item_id][1]['crQty']+$gmts_prod_arr[$order_id][$item_id][3]['embQty']+$gmts_prod_arr[$order_id][$item_id][5]['sorQty']+$gmts_prod_arr[$order_id][$item_id][7]['irnQty']+$gmts_prod_arr[$order_id][$item_id][8]['frQty'];
					}
				   
					$color=""; $days_remian=""; 
					$shipment_date=date("Y-m-d",strtotime($orderRes[csf("shipment_date")]));
					if($orderRes[csf("shiping_status")]==1 || $orderRes[csf("shiping_status")]==2)
					{
						$days_remian=datediff("d",$date,$shipment_date)-1; 
						if($shipment_date > $date) 
						{
							$color="";
						}
						else if($shipment_date < $date) 
						{
							$color="red";
						}														
						else if($shipment_date >= $date && $days_remian<=5 ) 
						{
							$color="orange";
						}
					} 
					else if($orderRes[csf("shiping_status")]==3)
					{
						$days_remian=datediff("d",$ex_factory_date,$shipment_date)-1;
						if($shipment_date>= $ex_factory_date) 
						{ 
							$color="green";
						}
						else if($shipment_date < $ex_factory_date) 
						{ 
							$color="#2A9FFF";
						}
						
					}//end if condition
					if($orderRes[csf("buyer_name")]!="")
					{
						$buyer_array[$orderRes[csf("buyer_name")]]['buyer_name']=$orderRes[csf("buyer_name")];
						$buyer_array[$orderRes[csf("buyer_name")]]['poQty']+=$po_quantity_in_pcs;
						$buyer_array[$orderRes[csf("buyer_name")]]['poVal']+=$poValue;
						$buyer_array[$orderRes[csf("buyer_name")]]['ex']+=$ex_factory_qnty;
						$buyer_array[$orderRes[csf("buyer_name")]]['cQty']+=$cutting_qnty;
						$buyer_array[$orderRes[csf("buyer_name")]]['prQty']+=$embl_recv_qnty;
						$buyer_array[$orderRes[csf("buyer_name")]]['sQty']+=$sewingin_qnty;
						$buyer_array[$orderRes[csf("buyer_name")]]['soQty']+=$sewingout_qnty;
						$buyer_array[$orderRes[csf("buyer_name")]]['iQty']+=$iron_qnty;
						$buyer_array[$orderRes[csf("buyer_name")]]['reiQty']+=$re_iron_qnty;
						$buyer_array[$orderRes[csf("buyer_name")]]['fQty']+=$finish_qnty;
					}
					
					$actual_exces_cut = $cutting_qnty;
					if($actual_exces_cut < $po_quantity_in_pcs) $actual_exces_cut=""; else $actual_exces_cut=number_format((($actual_exces_cut-$po_quantity_in_pcs)/$po_quantity_in_pcs)*100,2);

					$embl_iss_qty=$issue_print+$issue_emb+$issue_wash+$issue_special+$issue_gd;
					$embl_issue_total="";	
					if($issue_print!=0) $embl_issue_total .= 'PR='.$issue_print;
					if($issue_emb!=0) $embl_issue_total .= ', EM='.$issue_emb;
					if($issue_wash!=0) $embl_issue_total .= ', WA='.$issue_wash;
					if($issue_special!=0) $embl_issue_total .= ', SP='.$issue_special;
					if($issue_gd!=0) $embl_issue_total .= ', GD='.$issue_gd;

					$embl_receive_total="";	
					if($rcv_print!=0) $embl_receive_total .= 'PR='.$rcv_print;
					if($rcv_emb!=0) $embl_receive_total .= ', EM='.$rcv_emb;
					if($rcv_wash!=0) $embl_receive_total .= ', WA='.$rcv_wash;
					if($rcv_special!=0) $embl_receive_total .= ', SP='.$rcv_special;
					if($rcv_gd!=0) $embl_receive_total .= ', GD='.$rcv_gd;
					$embl_recv_qty=$rcv_print+$rcv_emb+$rcv_wash+$rcv_special+$rcv_gd;
					$shortage_ex=0;
					$shortage_ex=$po_quantity_in_pcs-$ex_factory_qnty;
					$finish_status = $finish_qnty*100/$po_quantity_in_pcs;
					
					if($j==0) 
					{
						$display_font_color="";
						$font_end="";
					}
					else 
					{
						$display_font_color="&nbsp;<font style='display:none'>";
						$font_end="</font>";
					}
					
					$company_name=$orderRes[csf("company_name")];
					$po_id=$orderRes[csf("id")];
					
					if(($ex_factory_date=="" || $ex_factory_date=="0000-00-00")) $ex_factory_date="&nbsp;"; else $ex_factory_date=change_date_format($ex_factory_date);
					//if($orderRes[csf("shiping_status")]==3) $days_remian=$days_remian; else $days_remian="---";
					if($actual_exces_cut > number_format($orderRes[csf("excess_cut")],2)) $excess_bgcolor="bgcolor='#FF0000'"; else $excess_bgcolor="";
					if($actual_exces_cut==0) $actual_exces_cut_per=""; else $actual_exces_cut_per=number_format($actual_exces_cut,2)." %";
					$total_rej_value+=$rej_value; 
					 	
				   	$html.="<tr bgcolor='$bgcolor' onclick=change_color('tr_2nd$k','$bgcolor') id=tr_2nd$k>";
					$html.='<td width="30">'.$display_font_color.$i.$font_end.'</td>
								<td width="120"><p>'.$display_font_color.$orderRes[csf("style_ref_no")].$font_end.'</p></td>
								<td width="50" align="center"><p>'.$display_font_color.$orderRes[csf("job_year")].$font_end.'</p></td>
								<td width="80" align="center"><p>'.$display_font_color.$orderRes[csf("job_no_prefix_num")].$font_end.'</p></td>
								<td width="130"><p>'.$display_font_color.$orderRes[csf("po_number")].$font_end.'</p></td>
								<td width="60"><p>'.$display_font_color.$buyer_short_library[$orderRes[csf("buyer_name")]].$font_end.'</p></td>
								<td width="80"><p>'.$display_font_color.$team_name_arr[$orderRes[csf("team_leader")]].$font_end.'</p></td>
								<td width="90"><p>'.$display_font_color.$team_member_arr[$orderRes[csf("dealing_marchant")]].$font_end.'</p></td>
								<td width="130"><p>'.$garments_item[$item_id].'</p></td>
								<td width="50" align="center"><p>'.$smvArr[$orderRes[csf("job_no_mst")].$item_id].'</p></td>
								<td width="80" align="right"><a href="##" onclick="openmypage_order('."'".$po_id."',".$company_name.",".$item_id.",0,'OrderPopup'".')">'.$po_quantity_in_pcs.'</a></td>
								<td width="80" align="center">'.change_date_format($orderRes[csf("po_insert_date")]).'</td>

								<td width="80" align="center" bgcolor="'.$color.'">'.change_date_format($orderRes[csf("shipment_date")]).'</td>
								<td width="80" align="right"><a href="##" onclick="openmypage('."'".$po_id."',".$item_id.",'exfactory','','','','0'".')">'.$ex_factory_date.'</a></td>
								
								
								<td width="80" align="center"><a href="##" onclick="openmypage('."'".$po_id."',".$item_id.",'pp_approved_date_popup','','','','0'".')">'.change_date_format($ppApprovalDateArr[$orderRes[csf("job_no_mst")]]['approval_status_date']).'</a></td>
								
								<td width="80" align="center">'.$days_remian.'&nbsp;</td>
								<td width="80" align="right">&nbsp;'.number_format($orderRes[csf("excess_cut")],2)." %".'</td>
								<td width="80" align="right"><a href="##" onclick="openmypage('."'".$po_id."',".$item_id.",'1','','','$type',''".')">'.$cutting_qnty.'</a></td>
								<td width="80" align="right">'.($po_quantity_in_pcs-$cutting_qnty).'</td>
								
								<td width="80" align="right" '.$excess_bgcolor.'>&nbsp;'.$actual_exces_cut_per.'</td>
								<td width="80" align="right"><font color="$bgcolor" style="display:none">'.$embl_iss_qty.'</font>&nbsp;<a href="##" onclick="openmypage('."'".$po_id."',".$item_id.",'2','','','$type',''".')">'.$embl_issue_total.'</a></td>
								<td width="80" align="right"><font color="$bgcolor" style="display:none">'.$embl_recv_qty.'</font>&nbsp;<a href="##" onclick="openmypage('."'".$po_id."',".$item_id.",'3','','','$type',''".')">'.$embl_receive_total.'</a></td>
								<td width="80" align="right"><a href="##" onclick="openmypage('."'".$po_id."',".$item_id.",'4','','','$type',''".')">'.$sewingin_qnty.'</a></td>
								<td width="80" align="right"><a href="##" onclick="openmypage('."'".$po_id."',".$item_id.",'5','','','$type',''".')">'.$sewingout_qnty.'</a></td>
								<td width="80" align="right"><a href="##" onclick="openmypage('."'".$po_id."',".$item_id.",'7','','','$type',''".')">'.$iron_qnty.'</a></td>
								<td width="80" align="right"><a href="##" onclick="openmypage('."'".$po_id."',".$item_id.",'9','','','$type',''".')">'.$re_iron_qnty.'</a></td>
								<td width="80" align="right"><a href="##" onclick="openmypage('."'".$po_id."',".$item_id.",'8','','','$type',''".')">'.$finish_qnty.'</a></td>
								<td width="80" align="right">&nbsp;'.number_format($finish_status,2)." %".'</td>
								<td width="80" align="right"><a href="##" onclick="openmypage_rej('."'".$po_id."',".$item_id.",'reject_qty','','','$type',''".')">'.$rej_value.'</a></td>
								<td width="80" align="right">'.$ex_factory_qnty.'</td>
								<td width="80" align="right">'.$shortage_ex.'</td>
								<td width="85">'.$shipment_status[$orderRes[csf("shiping_status")]].'</td>
								<td>&nbsp;<a href="##" onclick="openmypage_remark('."'".$po_id."',".$item_id.",0,'date_wise_production_report'".')">Veiw</a></td>
							</tr>';
				}
			} //end for loop
		}// end main foreach 
		
	    ?>
		<div>
	    	<table width="1580" cellspacing="0" >
	            <tr class="form_caption" style="border:none;">
	                <td colspan="21" align="center" style="border:none;font-size:16px; font-weight:bold" >
	                    <? echo "Style Wise Production Report"; ?>    
	                </td>
	            </tr>
	            <tr style="border:none;">
	                <td colspan="21" align="center" style="border:none; font-size:14px;">
	                    Company Name : <? echo $company_library[str_replace("'","",$cbo_company_name)]; ?>                                
	                </td>
	            </tr>
	            <tr style="border:none;">
	                <td colspan="21" align="center" style="border:none;font-size:12px; font-weight:bold">
	                    <?
	                        if(str_replace("'","",trim($txt_date_from))!="" && str_replace("'","",trim($txt_date_to))!="")
	                        {
	                            echo "From $fromDate To $toDate" ;
	                        }
	                    ?>
	                </td>
	            </tr>
	        </table>
			<div style="float:left; width:1220px">
	            <table width="1200" cellspacing="0" border="1" class="rpt_table" rules="all" id="" >
	                <thead>
	                    <tr>
	                        <th colspan="16" >In-House Order Production </th>
	                    </tr>
	                    <tr>
	                        <th width="30">Sl.</th>    
	                        <th width="80">Buyer Name</th>
	                        <th width="100">Order Qty.(Pcs)</th>
	                        <th width="110">PO Value</th>
	                        <th width="80">Total Cut Qty</th>
	                        <th width="80">Cutting Balance</th>
	                        <th width="80">Total Emb. Rcv. Qty</th>
	                        <th width="80">Total Sew Input Qty</th>
	                        <th width="80">Total Sew Qty</th>
	                        <th width="80">Total Iron Qty</th>
	                        <th width="80">Total Re-Iron Qty</th>
	                        <th width="80">Total Finish Qty</th>
	                        <th width="80">Fin Goods Status</th>
	                        <th width="80">Ex-Fac</th>
	                        <th>Ex-Fac%</th>
	                     </tr>
	                </thead>
	            </table>
	            <div style="max-height:425px; overflow-y:scroll; width:1220px" >
	                <table cellspacing="0" border="1" class="rpt_table"  width="1200" rules="all" id="" >
	                <?
						$i=1; $total_po_quantity=0;$total_po_value=0; $total_cut=0; $total_print_iss=0;
						$total_print_re=0; $total_sew_input=0; $total_sew_out=0; $total_iron=0; $total_re_iron=0; $total_finish=0;$total_ex_factory=0;
						$buyer_po_value=array();
						foreach($buyer_array as $row)
						{
							$buyer_po_value[$row["poVal"]]=$row["poVal"];
						}
						array_multisort($buyer_po_value, SORT_DESC,$buyer_array);
						
						foreach($buyer_array as $buyer_id=>$value)
	                    {
	                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							
	                        ?>
	                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
	                            <td width="30"><? echo $i;?></td>
	                            <td width="80"><? echo $buyer_short_library[$value["buyer_name"]]; ?></td>
	                            <td width="100" align="right"><? echo number_format($value["poQty"]);?></td>
	                            <td width="110" align="right"><? echo number_format($value["poVal"],2);?></td>
	                            <td width="80" align="right"><? echo number_format($value["cQty"]); ?></td>
	                            
	                            <td width="80" align="right"><? echo number_format($value["poQty"]-$value["cQty"]); ?></td>
	                            
	                            
	                            <td width="80" align="right"><? echo number_format($value["prQty"]); ?></td>
	                            <td width="80" align="right"><? echo number_format($value["sQty"]); ?></td>
	                            <td width="80" align="right"><? echo number_format($value["soQty"]); ?></td>
	                            <td width="80" align="right"><? echo number_format($value["iQty"]); ?></td>
	                            <td width="80" align="right"><? echo number_format($value["reiQty"]); ?></td>
	                            <td width="80" align="right"><? echo number_format($value["fQty"]); ?></td>
	                            <? $finish_gd_status = ($value["fQty"]/$value["poQty"])*100; ?>
	                            <td width="80" align="right"><? echo number_format($finish_gd_status,2)." %"; ?></td>
	                            <td width="80" align="right"><? echo number_format($value["ex"]); ?></td>
	                            <? $ex_gd_status = ($value["ex"]/$value["poQty"])*100; ?>
	                            <td width="" align="right"><? echo  number_format($ex_gd_status,2)." %"; ?></td>
	                        </tr>	
	                        <?		
	                            $total_po_quantity+=$value["poQty"];
	                            $total_po_value+=$value["poVal"];
	                            $total_cut+=$value["cQty"];
	                            $total_print_re+=$value["prQty"];
	                            $total_sew_input+=$value["sQty"];
	                            $total_sew_out+=$value["soQty"];
	                            $total_iron+=$value["iQty"];
	                            $total_re_iron+=$value["reiQty"];
	                            $total_finish+=$value["fQty"];
	                            $total_ex_factory+=$value["ex"];
	                           
	                        $i++;
	                    }//end foreach 1st
	                    
	                    $chart_data_qnty="Order Qty;".$total_po_quantity."\n"."Cutting;".$total_cut."\n"."Sew In;".$total_sew_input."\n"."Sew Out ;".$total_sew_out."\n"."Iron ;".$total_iron."\n"."Finish ;".$total_finish."\n"."Ex-Fact;".$total_ex_factory."\n";
	                ?>
	                </table>
	                <table border="1" class="tbl_bottom"  width="1200" rules="all" id="" >
	                    <tr> 
	                        <td width="30">&nbsp;</td> 
	                        <td width="80" align="right">Total</td> 
	                        <td width="100" id="tot_po_quantity"><div style="word-wrap:break-word; width:100px"><? echo number_format($total_po_quantity); ?></div></td> 
	                        <td width="110" id="tot_po_value"><div style="word-wrap:break-word; width:110px"><? echo number_format($total_po_value,2); ?></div></td> 
	                        <td width="80" id="tot_cutting"><? echo number_format($total_cut); ?></td>
	                        
	                        <td width="80" id="tot_cutting"><? echo number_format($total_po_quantity-$total_cut); ?></td>
	                        
	                        <td width="80" id="tot_emb_rcv"><? echo number_format($total_print_re); ?></td> 
	                        <td width="80" id="tot_sew_in"><? echo number_format($total_sew_input); ?></td> 
	                        <td width="80" id="tot_sew_out"><? echo number_format($total_sew_out); ?></td>   
	                        <td width="80" id="tot_iron"><? echo number_format($total_iron); ?></td> 
	                        <td width="80" id="tot_re_iron"><? echo number_format($total_re_iron); ?></td> 
	                        <td width="80" id="tot_finish"><? echo number_format($total_finish); ?></td>
	                        <? $total_finish_gd_status = ($total_finish/$total_po_quantity)*100; ?>
	                        <td width="80"><? echo number_format($total_finish_gd_status,2); ?></td >
	                        <td width="80"><? echo number_format($total_ex_factory); ?></td >
	                        <? $total_ex_status = ($total_ex_factory/$total_po_quantity)*100; ?>
	                        <td width=""><? echo number_format($total_ex_status,2); ?></td>
	                    </tr>
	                 </table>
	                 <input type="hidden" id="graph_data" value="<? echo substr($chart_data_qnty,0,-1); ?>"/>
	            </div>
	        </div>
	        <div style="float:left; width:600px">   
	            <table>
	                <tr>
	                    <td height="21" width="600"><div id="chartdiv"> </div></td>
	                </tr>    
	            </table>
	        </div> 
	        <div style="clear:both"></div>
	        <table width="2810" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1">
	            <thead>
	                <tr>
	                    <th width="30">SL</th>
	                    <th width="120">Style Name</th>  
	                    <th width="50">Job Year</th> 
	                    <th width="80">Job Number</th> 
	                    <th width="130">Order Number</th>
	                    <th width="60">Buyer Name</th>
	                    <th width="80">Team Name</th>
	                    <th width="90">Team Member</th>
	                    <th width="130">Item Name</th>
	                    <th width="50">SMV</th>
	                    <th width="80">Order Qty.</th>
	                    <th width="80">PO insert Date</th>

	                    <th width="80">Ship Date</th>
	                    <th width="80">Ex-Factory Date</th>
	                    <th width="80">PP Approved Date</th>
	                    
	                    <th width="80">Days in Hand</th>
	                    <th width="80">Stan. Exc. Cut %</th>
	                    <th width="80">Total Cut Qty</th>
	                    <th width="80">Cutting Balance</th>
	                    <th width="80">Actual Exc. Cut %</th>
	                    <th width="80">Total Emb. Issue Qty</th>
	                    <th width="80">Total Emb. Rcv. Qty</th>
	                    <th width="80">Total Sew Input Qty</th>
	                    <th width="80">Total Sew Output Qty</th>
	                    <th width="80">Total Iron Qty</th>
	                    <th width="80">Total Re-Iron Qty</th>
	                    <th width="80">Total Finish Qty</th>
	                    <th width="80">Fin Goods Status</th>
	                    <th width="80">Reject Qty</th>
	                    <th width="80">Total Ship Out</th>
	                    <th width="80">Shortage/ Excess</th>
	                    <th width="85">Shipping Status</th>
	                    <th>Remarks</th>
	                 </tr>
	            </thead>
	        </table>
	        <div style="max-height:425px; overflow-y:scroll; width:2780px" id="scroll_body">
	            <table border="1" class="rpt_table" width="2760" rules="all" id="table_body">
					<? echo $html; ?>  
	            </table>	
	            <table border="1" class="tbl_bottom" width="2760" rules="all" id="report_table_footer_1" >
	                <tr>
	                    <td width="30"></td>
	                    <td width="120"></td>
	                    <td width="50"></td>
	                    <td width="80"></td>
	                    <td width="130"></td>
	                    <td width="60"></td>
	                    <td width="80"></td>
	                    <td width="90"></td>
	                    <td width="130"></td>
	                    <td width="50">Total</td>
	                    <td width="80" id="total_order_quantity"></td>
	                    <td width="80"></td>
	                    <td width="80"></td>
	                    <td width="80"></td>
	                    <td width="80"></td>
	                    <td width="80"></td>
	                    <td width="80"></td>
	                    <td width="80" id="total_cutting"></td>
	                    <td width="80" id="total_cutting_bal"></td>
	                    <td width="80"></td>
	                    <td width="80" id="total_emb_issue"></td>
	                    <td width="80" id="total_emb_receive"></td>
	                    <td width="80" id="total_sewing_input"></td>
	                    <td width="80" id="total_sewing_out"></td>
	                    <td width="80" id="total_iron_qnty"></td>
	                    <td width="80" id="total_re_iron_qnty"></td>
	                    <td width="80" id="total_finish_qnty"></td>
	                    <td width="80"></td>
	                    <td width="80" align="right" id="total_rej_value_td"></td>
	                    <td width="80" id="total_out"></td>
	                    <td width="80" id="total_shortage"></td>
	                    <td width="85" id="ship_status"></td>
	                    <td></td>
	                 </tr>
				</table>

					<!-- <table border="1" class="tbl_bottom" width="3000" rules="all" id="report_table_footer_1" >
	                <tr>
	                    <td width="30"></td>
	                    <td width="130"></td>
	                    <td width="60"></td>
	                    <td width="50"></td>
	                    <td width="80"></td>
	                    <td width="120"></td>
	                    <td width="100"></td>
	                    <td width="100"></td>
	                    <td width="80"></td>
	                    <td width="90"></td>
	                    <td width="60"></td>
	                    <td width="35"></td>
	                    <td width="130">Total</td>
	                    <td width="50"></td>
	                    <td width="80" id="total_order_quantity"></td>
	                    <td width="80"></td>
	                    <td width="80"></td>
	                    <td width="80"></td>
	                    <td width=""></td>
	                    <td width="80" id="tot_plan_cut"></td>
	                    <td width="80" id="total_cutting"></td>
	                    <td width="80" id="total_cutting_bal"></td>
	                    <td width="80"></td>
	                    <td width="80" id="total_emb_issue"></td>
	                    <td width="80" id="total_emb_receive"></td>
	                    <td width="80" id="total_sewing_input"></td>
	                    <td width="80" id="total_sewing_out"></td>
	                    <td width="80" id="total_iron_qnty"></td>
	                    <td width="80" id="total_re_iron_qnty"></td>
	                    <td width="80" id="total_finish_qnty"></td>
	                    <td width="80" ></td>
	                    <td width="80" align="right" id="total_rej_value_td"></td>
	                    <td width="80" id="total_out"></td>
	                    <td width="80" id="total_shortage"></td>
	                    <td width="85" id="ship_status"></td>
	                    <td width="85" id="ship_status"></td>
	                    <td></td>
	                 </tr>
				</table> -->


			</div>
		</div>       
		<?
	}
	else if($type==3)//Order Country Wise
	{
		$orderWiseDataArr=array(); $ship_status_cond=""; $poIds='';
		
		if($db_type==0) $select_job_year="year(b.insert_date) as job_year"; else $select_job_year="to_char(b.insert_date,'YYYY') as job_year"; 
		
		$order_sql="select a.id, b.job_no_prefix_num, $select_job_year, b.job_no, b.team_leader, b.dealing_marchant, b.agent_name, a.po_number, a.po_quantity, a.unit_price, a.pub_shipment_date as shipment_date, a.shiping_status, a.excess_cut, a.plan_cut, a.file_no, a.grouping, a.details_remarks, b.company_name, b.buyer_name, b.set_break_down, b.style_ref_no, b.total_set_qnty as ratio 
			from wo_po_details_master b, wo_po_break_down a 
			where a.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 and LOWER(a.po_number) like LOWER('$search_string') and b.status_active=1 and b.is_deleted=0 $txt_date $company_name $buyer_name $season_cond $garmentsNature $team_name_cond $team_member_cond $file_no $ref_no $ship_status_cond $agent_cond $order_status_cond $item_cat_cond  order by a.pub_shipment_date, b.job_no_prefix_num, a.id";
		/*select a.id, b.job_no_prefix_num, b.job_no, b.team_leader, b.dealing_marchant, b.agent_name, a.po_number, a.po_quantity, a.unit_price, a.pub_shipment_date as shipment_date, a.shiping_status, a.excess_cut, a.plan_cut, a.file_no, a.grouping, b.company_name, b.buyer_name, b.set_break_down, b.style_ref_no, b.total_set_qnty as ratio from wo_po_details_master b, wo_po_break_down a where a.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 and a.po_number like '$search_string' and b.status_active=1 and b.is_deleted=0 $txt_date $company_name $buyer_name $garmentsNature $team_name_cond $team_member_cond $file_no $ref_no $ship_status_cond $agent_cond order by a.pub_shipment_date, b.job_no_prefix_num, a.id*/
		$result=sql_select($order_sql);
		//echo count($result)."Fuad";
		if(count($result)<1)
		{
			echo "<div style='width:1000px;' align='center'><font color='#FF0000'>No Data Found</font></div>"; die;
		}
		
		$tot_rows=0;
		foreach($result as $orderRes)
		{
			$tot_rows++;
			$poIds.=$orderRes[csf("id")].",";
			$orderWiseDataArr[$orderRes[csf("id")]]=$orderRes[csf("job_no_prefix_num")]."##".$orderRes[csf("job_no")]."##".$orderRes[csf("team_leader")]."##".$orderRes[csf("dealing_marchant")]."##".$orderRes[csf("agent_name")]."##".$orderRes[csf("po_number")]."##".$orderRes[csf("po_quantity")]."##".$orderRes[csf("unit_price")]."##".$orderRes[csf("shipment_date")]."##".$orderRes[csf("shiping_status")]."##".$orderRes[csf("excess_cut")]."##".$orderRes[csf("plan_cut")]."##".$orderRes[csf("file_no")]."##".$orderRes[csf("grouping")]."##".$orderRes[csf("details_remarks")]."##".$orderRes[csf("company_name")]."##".$orderRes[csf("buyer_name")]."##".$orderRes[csf("set_break_down")]."##".$orderRes[csf("style_ref_no")]."##".$orderRes[csf("ratio")]."##".$orderRes[csf("job_year")];
		}
		
		unset($result);
		$poIds=chop($poIds,','); $poIds_cond="";
		if($db_type==2 && count($tot_rows)>1000)
		{
			$poIds_cond=" and (";
			$poIdsArr=array_chunk(explode(",",$poIds),999);
			foreach($poIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$poIds_cond.=" po_break_down_id in($ids) or ";
			}
			$poIds_cond=chop($poIds_cond,'or ');
			$poIds_cond.=")";
		}
		else
		{
			$poIds_cond=" and po_break_down_id in($poIds)";
		}
		$ex_factory_arr=array();
		
		$ex_factory_data=sql_select("select po_break_down_id, item_number_id, country_id, MAX(ex_factory_date) AS ex_factory_date,sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END)-sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) AS ex_factory_qnty  from pro_ex_factory_mst where status_active=1 and is_deleted=0 $poIds_cond group by po_break_down_id, item_number_id, country_id");
		foreach($ex_factory_data as $exRow)
		{
			$ex_factory_arr[$exRow[csf('po_break_down_id')]][$exRow[csf('item_number_id')]][$exRow[csf('country_id')]]['date']=$exRow[csf('ex_factory_date')];
			$ex_factory_arr[$exRow[csf('po_break_down_id')]][$exRow[csf('item_number_id')]][$exRow[csf('country_id')]]['qty']=$exRow[csf('ex_factory_qnty')];
		}
		//print_r($ex_factory_arr[107]);die;
		unset($ex_factory_data);
		/*if($db_type==0)
		{
			$prod_sql="SELECT c.po_break_down_id, c.item_number_id, country_id,
				IFNULL(sum(CASE WHEN c.production_type ='1' THEN c.production_quantity ELSE 0 END),0) AS cutting_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='2' THEN c.production_quantity ELSE 0 END),0) AS printing_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='2' and embel_name=1 THEN c.production_quantity ELSE 0 END),0) AS print,
				IFNULL(sum(CASE WHEN c.production_type ='2' and embel_name=2 THEN c.production_quantity ELSE 0 END),0) AS emb,
				IFNULL(sum(CASE WHEN c.production_type ='2' and embel_name=3 THEN c.production_quantity ELSE 0 END),0) AS wash,
				IFNULL(sum(CASE WHEN c.production_type ='2' and embel_name=4 THEN c.production_quantity ELSE 0 END),0) AS special,
				IFNULL(sum(CASE WHEN c.production_type ='3' THEN c.production_quantity ELSE 0 END),0) AS printreceived_qnty, 
				IFNULL(sum(CASE WHEN c.production_type ='3' and embel_name=1 THEN c.production_quantity ELSE 0 END),0) AS printr,
				IFNULL(sum(CASE WHEN c.production_type ='3' and embel_name=2 THEN c.production_quantity ELSE 0 END),0) AS embr,
				IFNULL(sum(CASE WHEN c.production_type ='3' and embel_name=3 THEN c.production_quantity ELSE 0 END),0) AS washr,
				IFNULL(sum(CASE WHEN c.production_type ='3' and embel_name=4 THEN c.production_quantity ELSE 0 END),0) AS specialr,
				IFNULL(sum(CASE WHEN c.production_type ='4' THEN c.production_quantity ELSE 0 END),0) AS sewingin_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='5' THEN c.production_quantity ELSE 0 END),0) AS sewingout_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='7' THEN c.production_quantity ELSE 0 END),0) AS iron_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='7' THEN c.re_production_qty ELSE 0 END),0) AS re_iron_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='8' THEN c.production_quantity ELSE 0 END),0) AS finish_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='1' THEN c.reject_qnty ELSE 0 END),0) AS cutting_rej_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='8' THEN c.reject_qnty ELSE 0 END),0) AS finish_rej_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='5' THEN c.reject_qnty ELSE 0 END),0) AS sewingout_rej_qnty
			from 
				pro_garments_production_mst c
			where  
				c.status_active=1 and c.is_deleted=0 and c.company_id=$cbo_company_name group by c.po_break_down_id, c.item_number_id, country_id";
		}
		else
		{
			$prod_sql= "SELECT c.po_break_down_id, c.item_number_id, country_id,
				NVL(sum(CASE WHEN c.production_type ='1' THEN c.production_quantity ELSE 0 END),0) AS cutting_qnty,
				NVL(sum(CASE WHEN c.production_type ='2' THEN c.production_quantity ELSE 0 END),0) AS printing_qnty,
				NVL(sum(CASE WHEN c.production_type ='2' and embel_name=1 THEN c.production_quantity ELSE 0 END),0) AS print,
				NVL(sum(CASE WHEN c.production_type ='2' and embel_name=2 THEN c.production_quantity ELSE 0 END),0) AS emb,
				NVL(sum(CASE WHEN c.production_type ='2' and embel_name=3 THEN c.production_quantity ELSE 0 END),0) AS wash,
				NVL(sum(CASE WHEN c.production_type ='2' and embel_name=4 THEN c.production_quantity ELSE 0 END),0) AS special,
				NVL(sum(CASE WHEN c.production_type ='3' THEN c.production_quantity ELSE 0 END),0) AS printreceived_qnty, 
				NVL(sum(CASE WHEN c.production_type ='3' and embel_name=1 THEN c.production_quantity ELSE 0 END),0) AS printr,
				NVL(sum(CASE WHEN c.production_type ='3' and embel_name=2 THEN c.production_quantity ELSE 0 END),0) AS embr,
				NVL(sum(CASE WHEN c.production_type ='3' and embel_name=3 THEN c.production_quantity ELSE 0 END),0) AS washr,
				NVL(sum(CASE WHEN c.production_type ='3' and embel_name=4 THEN c.production_quantity ELSE 0 END),0) AS specialr,
				NVL(sum(CASE WHEN c.production_type ='4' THEN c.production_quantity ELSE 0 END),0) AS sewingin_qnty,
				NVL(sum(CASE WHEN c.production_type ='5' THEN c.production_quantity ELSE 0 END),0) AS sewingout_qnty,
				NVL(sum(CASE WHEN c.production_type ='7' THEN c.production_quantity ELSE 0 END),0) AS iron_qnty,
				NVL(sum(CASE WHEN c.production_type ='7' THEN c.re_production_qty ELSE 0 END),0) AS re_iron_qnty,
				NVL(sum(CASE WHEN c.production_type ='8' THEN c.production_quantity ELSE 0 END),0) AS finish_qnty,
				NVL(sum(CASE WHEN c.production_type ='1' THEN c.reject_qnty ELSE 0 END),0) AS cutting_rej_qnty,
				NVL(sum(CASE WHEN c.production_type ='8' THEN c.reject_qnty ELSE 0 END),0) AS finish_rej_qnty,
				NVL(sum(CASE WHEN c.production_type ='5' THEN c.reject_qnty ELSE 0 END),0) AS sewingout_rej_qnty
			from 
				pro_garments_production_mst c
			where  
				c.status_active=1 and c.is_deleted=0 and c.company_id=$cbo_company_name group by c.po_break_down_id, c.item_number_id, country_id";
		}*/
		if(str_replace("'","",$cbo_location)==0) $location_cond=""; else $location_cond=" and location=$cbo_location";
		if(str_replace("'","",$cbo_floor)==0) $floor_cond=""; else $floor_cond=" and floor_id=$cbo_floor";
		$emb_arr=array(1=>'print',2=>'emb',3=>'wash',4=>'special',5=>'gmt'); $gmts_prod_arr=array();
		$prod_sql= "SELECT po_break_down_id, country_id, item_number_id, production_type, production_quantity, embel_name, re_production_qty, reject_qnty from 
				pro_garments_production_mst where status_active=1 and is_deleted=0 and company_id=$cbo_company_name $poIds_cond $location_cond $floor_cond";	
		$gmts_prod_arr=array();
		$res_gmtsData=sql_select($prod_sql);
		foreach($res_gmtsData as $gmtsRow)
		{
			/*$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][2]['print']=$gmtsRow[csf('print')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][2]['emb']=$gmtsRow[csf('emb')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][2]['wash']=$gmtsRow[csf('wash')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][2]['special']=$gmtsRow[csf('special')];
			
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][3]['print']=$gmtsRow[csf('printr')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][3]['emb']=$gmtsRow[csf('embr')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][3]['wash']=$gmtsRow[csf('washr')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][3]['special']=$gmtsRow[csf('specialr')];*/
			if($gmtsRow[csf('production_type')]==2 || $gmtsRow[csf('production_type')]==3)
			{
				$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][$gmtsRow[csf('production_type')]][$emb_arr[$gmtsRow[csf('embel_name')]]]+=$gmtsRow[csf('production_quantity')];
			}
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][$gmtsRow[csf('production_type')]]['cQty']+=$gmtsRow[csf('production_quantity')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][$gmtsRow[csf('production_type')]]['pQty']+=$gmtsRow[csf('production_quantity')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][$gmtsRow[csf('production_type')]]['prQty']+=$gmtsRow[csf('production_quantity')];
			
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][$gmtsRow[csf('production_type')]]['sQty']+=$gmtsRow[csf('production_quantity')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][$gmtsRow[csf('production_type')]]['soQty']+=$gmtsRow[csf('production_quantity')];
			
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][$gmtsRow[csf('production_type')]]['iQty']+=$gmtsRow[csf('production_quantity')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][$gmtsRow[csf('production_type')]]['riQty']+=$gmtsRow[csf('re_production_qty')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][$gmtsRow[csf('production_type')]]['fQty']+=$gmtsRow[csf('production_quantity')];
			
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][$gmtsRow[csf('production_type')]]['crQty']+=$gmtsRow[csf('reject_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][$gmtsRow[csf('production_type')]]['sorQty']+=$gmtsRow[csf('reject_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][$gmtsRow[csf('production_type')]]['frQty']+=$gmtsRow[csf('reject_qnty')];
		}
		unset($res_gmtsData);
		$po_country_arr=array(); $po_country_data_arr=array();
		$ship_status_cond="";
		if($shipping_status!=0) $ship_status_cond=" and shiping_status=$shipping_status";
		$poCountryData=sql_select("select po_break_down_id, item_number_id, country_id, shiping_status, sum(order_quantity) as qnty, sum(order_total) as value from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 $ship_status_cond $poIds_cond group by po_break_down_id, item_number_id, country_id, shiping_status");
		foreach($poCountryData as $row)
		{
			$po_country_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]['qnty']=$row[csf('qnty')];
			$po_country_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]['value']=$row[csf('value')];
			$po_country_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]['shiping_status']=$row[csf('shiping_status')];
			$po_country_arr[$row[csf('po_break_down_id')]].=$row[csf('country_id')].",";
		}
		unset($poCountryData);
		//print_r($po_country_data_arr);die;
		$buyer_array=array();
		/*$order_sql="select a.id, b.job_no_prefix_num, b.team_leader, b.dealing_marchant, a.po_number, a.po_quantity, a.unit_price, a.po_total_price, a.job_no_mst, a.pub_shipment_date as shipment_date, a.shiping_status, a.excess_cut, a.plan_cut, b.company_name, b.buyer_name, b.set_break_down, b.style_ref_no from wo_po_details_master b,wo_po_break_down a where a.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 and a.po_number like '$search_string' and b.status_active=1 and b.is_deleted=0 $txt_date $company_name $buyer_name $garmentsNature $team_name_cond $team_member_cond order by a.pub_shipment_date, a.id";
		$result=sql_select($order_sql);*/
		$i=0; $k=0; $date=date("Y-m-d"); 
		$ship_status_id=0;
		foreach($orderWiseDataArr as $po_id=>$po_data)
		{ 
		   $i++;
		   if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
		   $po_data_arr=explode("##",$po_data);
			$job_no_prefix_num=$po_data_arr[0];
			$job_no=$po_data_arr[1];
			$team_leader=$po_data_arr[2];
			$dealing_marchant=$po_data_arr[3];
			$agent_name=$po_data_arr[4];
			$po_number=$po_data_arr[5];
			$po_quantity=$po_data_arr[6];
			$unit_price=$po_data_arr[7];
			$shipment_date=$po_data_arr[8];
			$shiping_status=$po_data_arr[9];
			$excess_cut=$po_data_arr[10];
			$plan_cut=$po_data_arr[11];
			$file_no=$po_data_arr[12];
			$grouping=$po_data_arr[13];
			$details_remarks=$po_data_arr[14];
			$company_name=$po_data_arr[15];
		    $buyer_name=$po_data_arr[16];
			$set_break_down=$po_data_arr[17];
			$style_ref_no=$po_data_arr[18];
			$ratio=$po_data_arr[19];
			$job_year=$po_data_arr[20];
			
			$i++;
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

			$setArr = explode("__",$set_break_down );
			$countArr = count($setArr); 
			if($countArr==0) $countArr=1; $s=0;
		  
		   for($j=0;$j<$countArr;$j++)
		   {
			  	$setItemArr = explode("_",$setArr[$j]);
			   	$item_id=$setItemArr[0];
			   	$set_qnty=$setItemArr[1];
			   	if($item_id>0)
			   	{
					
					$country=array_unique(explode(",",substr($po_country_arr[$po_id],0,-1)));
					
					foreach($country as $country_id)
					{
						$k++;
						$po_quantity_in_pcs = $po_country_data_arr[$po_id][$item_id][$country_id]['qnty'];
						$po_value = $po_country_data_arr[$po_id][$item_id][$country_id]['value'];
						$ship_status_id = $po_country_data_arr[$po_id][$item_id][$country_id]['shiping_status'];
						if($ship_status_id>0)
						{
							$unit_price=$orderRes[csf("unit_price")]/$set_qnty;
							$ex_factory_date=$ex_factory_arr[$po_id][$item_id][$country_id]['date'];
							$ex_factory_qnty=$ex_factory_arr[$po_id][$item_id][$country_id]['qty'];
							$color=""; $days_remian="";
							if($ship_status_id==1 || $ship_status_id==2)
							{
								$days_remian=datediff("d",$date,$shipment_date)-1; 
								if($shipment_date > $date) 
								{
									$color="";
								}
								else if($shipment_date < $date) 
								{
									$color="red";
								}														
								else if($shipment_date >= $date && $days_remian<=5 ) 
								{
									$color="orange";
								}
							} 
							else if($ship_status_id==3)
							{
								$days_remian=datediff("d",$ex_factory_date,$shipment_date)-1;
								if($shipment_date >= $ex_factory_date) 
								{ 
									$color="green";
								}
								else if($shipment_date < $ex_factory_date) 
								{ 
									$color="#2A9FFF";
								}
								
							}//end if condition
							
							$cutting_qnty=$gmts_prod_arr[$po_id][$item_id][$country_id][1]['cQty'];
							$embl_recv_qnty=$gmts_prod_arr[$po_id][$item_id][$country_id][3]['prQty'];
							$sewingin_qnty=$gmts_prod_arr[$po_id][$item_id][$country_id][4]['sQty'];
							$sewingout_qnty=$gmts_prod_arr[$po_id][$item_id][$country_id][5]['soQty'];
							$iron_qnty=$gmts_prod_arr[$po_id][$item_id][$country_id][7]['iQty'];
							$re_iron_qnty=$gmts_prod_arr[$po_id][$item_id][$country_id][7]['riQty'];
							$finish_qnty=$gmts_prod_arr[$po_id][$item_id][$country_id][8]['fQty'];
							if($buyer_name!="")
							{
								$buyer_array[$buyer_name]['buyer_name']=$buyer_name;
								$buyer_array[$buyer_name]['poQty']+=$po_quantity_in_pcs;
								$buyer_array[$buyer_name]['poVal']+=$po_value;
								$buyer_array[$buyer_name]['ex']+=$ex_factory_qnty;
								$buyer_array[$buyer_name]['cQty']+=$cutting_qnty;
								$buyer_array[$buyer_name]['prQty']+=$embl_recv_qnty;
								$buyer_array[$buyer_name]['sQty']+=$sewingin_qnty;
								$buyer_array[$buyer_name]['soQty']+=$sewingout_qnty;
								$buyer_array[$buyer_name]['iQty']+=$iron_qnty;
								$buyer_array[$buyer_name]['reiQty']+=$re_iron_qnty;
								$buyer_array[$buyer_name]['fQty']+=$finish_qnty;
							}
							
							$actual_exces_cut = $gmts_prod_arr[$po_id][$item_id][$country_id][1]['cQty'];
							if($actual_exces_cut < $po_quantity_in_pcs) $actual_exces_cut=""; else $actual_exces_cut=number_format((($actual_exces_cut-$po_quantity_in_pcs)/$po_quantity_in_pcs)*100,2);
		
							$issue_print = $gmts_prod_arr[$po_id][$item_id][$country_id][2]['print'];
							$issue_emb = $gmts_prod_arr[$po_id][$item_id][$country_id][2]['emb'];
							$issue_wash = $gmts_prod_arr[$po_id][$item_id][$country_id][2]['wash'];
							$issue_special = $gmts_prod_arr[$po_id][$item_id][$country_id][2]['special'];
							$issue_gd = $gmts_prod_arr[$po_id][$item_id][$country_id][2]['gmt'];
							
							$embl_iss_qty=$issue_print+$issue_emb+$issue_wash+$issue_special+$issue_gd;
							
							$embl_issue_total="";
							if($issue_print!=0) $embl_issue_total .= 'PR='.$issue_print;
							if($issue_emb!=0) $embl_issue_total .= ', EM='.$issue_emb;
							if($issue_wash!=0) $embl_issue_total .= ', WA='.$issue_wash;
							if($issue_special!=0) $embl_issue_total .= ', SP='.$issue_special;
							if($issue_gd!=0) $embl_issue_total .= ', GD='.$issue_gd;
		
							$rcv_print = $gmts_prod_arr[$po_id][$item_id][$country_id][3]['print'];
							$rcv_emb = $gmts_prod_arr[$po_id][$item_id][$country_id][3]['emb'];
							$rcv_wash = $gmts_prod_arr[$po_id][$item_id][$country_id][3]['wash'];
							$rcv_special = $gmts_prod_arr[$po_id][$item_id][$country_id][3]['special'];
							$rcv_gd = $gmts_prod_arr[$po_id][$item_id][$country_id][3]['gmt'];
							
							$embl_receive_total="";	
							if($rcv_print!=0) $embl_receive_total .= 'PR='.$rcv_print;
							if($rcv_emb!=0) $embl_receive_total .= ', EM='.$rcv_emb;
							if($rcv_wash!=0) $embl_receive_total .= ', WA='.$rcv_wash;
							if($rcv_special!=0) $embl_receive_total .= ', SP='.$rcv_special;
							if($rcv_gd!=0) $embl_receive_total .= ', GD='.$rcv_gd;
							
							$embl_recv_qty=$rcv_print+$rcv_emb+$rcv_wash+$rcv_special+$rcv_gd;
							
							//$rej_value=$proRes[csf("finish_rej_qnty")]+$proRes[csf("sewingout_rej_qnty")];	
							$rej_value=$gmts_prod_arr[$po_id][$item_id][$country_id][1]['crQty']+$gmts_prod_arr[$po_id][$item_id][$country_id][5]['sorQty']+$gmts_prod_arr[$po_id][$item_id][$country_id][8]['frQty'];
							$shortage = $po_quantity_in_pcs-$ex_factory_qnty;
							$finish_status = $finish_qnty*100/$po_quantity_in_pcs;
							
							if($s==0) 
							{
								$display_font_color="";
								$font_end="";
							}
							else 
							{
								$display_font_color="&nbsp;<font style='display:none'>";
								$font_end="</font>";
							}
							
							$company_name=$orderRes[csf("company_name")];
							
							if(($ex_factory_date=="" || $ex_factory_date=="0000-00-00")) $ex_factory_date="&nbsp;"; else $ex_factory_date=change_date_format($ex_factory_date);
							//if($orderRes[csf("shiping_status")]==3) $days_remian=$days_remian; else $days_remian="---";
							if($actual_exces_cut > number_format($excess_cut,2)) $excess_bgcolor="bgcolor='#FF0000'"; else $excess_bgcolor="";
							if($actual_exces_cut==0) $actual_exces_cut_per=""; else $actual_exces_cut_per=number_format($actual_exces_cut,2)." %";
							$total_rej_value+=$rej_value; 
							 
							$html.="<tr bgcolor='$bgcolor' onclick=change_color('tr_2nd$k','$bgcolor') id=tr_2nd$k>";
							$html.='<td width="30">'.$display_font_color.$i.$font_end.'</td>
										<td width="130"><p>'.$display_font_color.$po_number.$font_end.'</p></td>
										<td width="60"><p>'.$display_font_color.$buyer_short_library[$buyer_name].$font_end.'</p></td>
										<td width="50" align="center"><p>'.$display_font_color.$job_year.$font_end.'</p></td>
										<td width="80" align="center"><p>'.$display_font_color.$job_no_prefix_num.$font_end.'</p></td>
										<td width="120"><p>'.$display_font_color.$style_ref_no.$font_end.'</p></td>
										<td width="80"><p>'.$display_font_color.$team_name_arr[$team_leader].$font_end.'</p></td>
										<td width="90"><p>'.$display_font_color.$team_member_arr[$dealing_marchant].$font_end.'</p></td>
										<td width="130"><p>'.$garments_item[$item_id].'</p></td>
										<td width="80"><p>'.$country_library[$country_id].'&nbsp;</p></td>
										<td width="80" align="right"><a href="##" onclick="openmypage_order('.$po_id.",".$company_name.",".$item_id.",$country_id,'OrderPopupCountry'".')">'.$po_quantity_in_pcs.'</a></td>
										<td width="80" align="center" bgcolor="'.$color.'">'.change_date_format($shipment_date).'</td>
										<td width="80" align="right"><a href="##" onclick="openmypage('.$po_id.",".$item_id.",'exfactoryCountry','','','',".$country_id."".')">'.$ex_factory_date.'</a></td>
										<td width="80" align="center">'.$days_remian.'&nbsp;</td>
										<td width="80" align="right">&nbsp;'.number_format($excess_cut,2)." %".'</td>
										<td width="80" align="right"><a href="##" onclick="openmypage('.$po_id.",".$item_id.",'1','','','$type','$country_id'".')">'.$cutting_qnty.'</a></td>
										
										<td width="80" align="right">'.($po_quantity_in_pcs-$cutting_qnty).'</td>
										
										<td width="80" align="right" '.$excess_bgcolor.'>&nbsp;'.$actual_exces_cut_per.'</td>
										<td width="80" align="right"><font color="$bgcolor" style="display:none">'.$embl_iss_qty.'</font>&nbsp;<a href="##" onclick="openmypage('.$po_id.",".$item_id.",'2','','','$type','$country_id'".')">'.$embl_issue_total.'</a></td>
										<td width="80" align="right"><font color="$bgcolor" style="display:none">'.$embl_recv_qty.'</font>&nbsp;<a href="##" onclick="openmypage('.$po_id.",".$item_id.",'3','','','$type','$country_id'".')">'.$embl_receive_total.'</a></td>
										<td width="80" align="right"><a href="##" onclick="openmypage('.$po_id.",".$item_id.",'4','','','$type','$country_id'".')">'.$sewingin_qnty.'</a></td>
										<td width="80" align="right"><a href="##" onclick="openmypage('.$po_id.",".$item_id.",'5','','','$type','$country_id'".')">'.$sewingout_qnty.'</a></td>
										<td width="80" align="right"><a href="##" onclick="openmypage('.$po_id.",".$item_id.",'7','','','$type','$country_id'".')">'.$iron_qnty.'</a></td>
										<td width="80" align="right"><a href="##" onclick="openmypage('.$po_id.",".$item_id.",'9','','','$type','$country_id'".')">'.$re_iron_qnty.'</a></td>
										<td width="80" align="right"><a href="##" onclick="openmypage('.$po_id.",".$item_id.",'8','','','$type','$country_id'".')">'.$finish_qnty.'</a></td>
										<td width="80" align="right">&nbsp;'.number_format($finish_status,2)." %".'</td>
										<td width="80" align="right"><a href="##" onclick="openmypage_rej('.$po_id.",".$item_id.",'reject_qty','','','$type','$country_id'".')">'.$rej_value.'</a></td>
										<td width="80" align="right">'.$ex_factory_qnty.'</td>
										<td width="80" align="right">'.$shortage.'</td>
										<td width="85">'.$shipment_status[$ship_status_id].'</td>
										<td>&nbsp;<a href="##" onclick="openmypage_remark('.$po_id.",".$item_id.",0,'date_wise_production_report'".')">Veiw</a></td>
									</tr>';
							 $s++;
						}
								
					}
				}
			} //end for loop
		}// end main foreach 
	    ?>
	    <div>
	    	<table width="1580" cellspacing="0" >
	            <tr class="form_caption" style="border:none;">
	                <td colspan="21" align="center" style="border:none;font-size:16px; font-weight:bold" >
	                    <? echo "Order Country Wise Production Report"; ?>    
	                </td>
	            </tr>
	            <tr style="border:none;">
	                <td colspan="21" align="center" style="border:none; font-size:14px;">
	                    Company Name : <? echo $company_library[str_replace("'","",$cbo_company_name)]; ?>                                
	                </td>
	            </tr>
	            <tr style="border:none;">
	                <td colspan="21" align="center" style="border:none;font-size:12px; font-weight:bold">
	                    <?
	                        if(str_replace("'","",trim($txt_date_from))!="" && str_replace("'","",trim($txt_date_to))!="")
	                        {
	                            echo "From $fromDate To $toDate" ;
	                        }
	                    ?>
	                </td>
	            </tr>
	        </table>
	        <div id="data_panel" align="center" style="width:100%">
	         <script>
			 	function new_window()
				 {
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				 }
	          </script>
	        
	        </div>
	        <div style="float:left; width:1280px" id="details_reports">
	            <table width="1260" cellspacing="0" border="1" class="rpt_table" rules="all" id="" >
	                <thead>
	                    <tr>
	                    	<th colspan="2"><input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" /></th>
	                        <th colspan="13" >In-House Order Production 
	                        <br />
	                        <?
	                        if(str_replace("'","",trim($txt_date_from))!="" && str_replace("'","",trim($txt_date_to))!="")
	                        {
	                            echo "From $fromDate To $toDate" ;
	                        }
	                    	?>
	                        </th>
	                    </tr>
	                    <tr>
	                        <th width="30">Sl.</th>    
	                        <th width="80">Buyer Name</th>
	                        <th width="100">Order Qty.(Pcs)</th>
	                        <th width="110">PO Value</th>
	                        <th width="80">Total Cut Qty</th>
	                        <th width="80">Cutting Balance</th>
	                        <th width="80">Total Emb. Rcv. Qty</th>
	                        <th width="80">Total Sew Input Qty</th>
	                        <th width="80">Total Sew Qty</th>
	                        <th width="80">Total Iron Qty</th>
	                        <th width="80">Total Re-Iron Qty</th>
	                        <th width="80">Total Finish Qty</th>
	                        <th width="80">Fin Goods Status</th>
	                        <th width="80">Ex-Fac</th>
	                        <th>Ex-Fac%</th>
	                     </tr>
	                </thead>
	            </table>
	            <div style="max-height:425px; overflow-y:scroll; width:1260px" >
	                <table cellspacing="0" border="1" class="rpt_table"  width="1240" rules="all" id="" >
	                <?
						$i=1; $total_po_quantity=0;$total_po_value=0; $total_cut=0; $total_print_iss=0;
						$total_print_re=0; $total_sew_input=0; $total_sew_out=0; $total_iron=0; $total_re_iron=0; $total_finish=0;$total_ex_factory=0;
						$buyer_po_value=array();
						foreach($buyer_array as $row)
						{
							$buyer_po_value[$row["poVal"]]=$row["poVal"];
						}
						array_multisort($buyer_po_value, SORT_DESC,$buyer_array);
						//buyer_name
						foreach($buyer_array as $buyer_id=>$value)
	                    {
	                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							
	                        ?>
	                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
	                            <td width="30"><? echo $i;?></td>
	                            <td width="80"><? echo $buyer_short_library[$value["buyer_name"]]; ?></td>
	                            <td width="100" align="right"><? echo number_format($value["poQty"]);?></td>
	                            <td width="110" align="right"><? echo number_format($value["poVal"],2);?></td>
	                            <td width="80" align="right"><? echo number_format($value["cQty"]); ?></td>
	                            <td width="80" align="right"><? echo number_format($value["poQty"]-$value["cQty"]); ?></td>
	                            <td width="80" align="right"><? echo number_format($value["prQty"]); ?></td>
	                            <td width="80" align="right"><? echo number_format($value["sQty"]); ?></td>
	                            <td width="80" align="right"><? echo number_format($value["soQty"]); ?></td>
	                            <td width="80" align="right"><? echo number_format($value["iQty"]); ?></td>
	                            <td width="80" align="right"><? echo number_format($value["reiQty"]); ?></td>
	                            <td width="80" align="right"><? echo number_format($value["fQty"]); ?></td>
	                            <? $finish_gd_status = ($value["fQty"]/$value["poQty"])*100; ?>
	                            <td width="80" align="right"><? echo number_format($finish_gd_status,2)." %"; ?></td>
	                            <td width="80" align="right"><? echo number_format($value["ex"]); ?></td>
	                            <? $ex_gd_status = ($value["ex"]/$value["poQty"])*100; ?>
	                            <td width="" align="right"><? echo  number_format($ex_gd_status,2)." %"; ?></td>
	                        </tr>	
	                        <?		
	                            $total_po_quantity+=$value["poQty"];
	                            $total_po_value+=$value["poVal"];
	                            $total_cut+=$value["cQty"];
	                            $total_print_re+=$value["prQty"];
	                            $total_sew_input+=$value["sQty"];
	                            $total_sew_out+=$value["soQty"];
	                            $total_iron+=$value["iQty"];
	                            $total_re_iron+=$value["reiQty"];
	                            $total_finish+=$value["fQty"];
	                            $total_ex_factory+=$value["ex"];
	                           
	                        $i++;
	                    }//end foreach 1st
	                    
	                    $chart_data_qnty="Order Qty;".$total_po_quantity."\n"."Cutting;".$total_cut."\n"."Sew In;".$total_sew_input."\n"."Sew Out ;".$total_sew_out."\n"."Iron ;".$total_iron."\n"."Finish ;".$total_finish."\n"."Ex-Fact;".$total_ex_factory."\n";
	                ?>
	                </table>
	                <table border="1" class="tbl_bottom"  width="1240" rules="all" id="" >
	                    <tr> 
	                        <td width="30">&nbsp;</td> 
	                        <td width="80" align="right">Total</td> 
	                        <td width="100" id="tot_po_quantity"><div style="word-wrap:break-word; width:100px"><? echo number_format($total_po_quantity); ?></div></td> 
	                        <td width="110" id="tot_po_value"><div style="word-wrap:break-word; width:110px"><? echo number_format($total_po_value,2); ?></div></td> 
	                        <td width="80" id="tot_cutting"><div style="word-wrap:break-word; width:80px"><? echo number_format($total_cut); ?></div></td>
	                        
	                        <td width="80" id="tot_cutting"><div style="word-wrap:break-word; width:80px"><? echo number_format($total_po_quantity-$total_cut); ?></div></td>
	                        
	                        
	                        <td width="80" id="tot_emb_rcv"><div style="word-wrap:break-word; width:80px"><? echo number_format($total_print_re); ?></div></td> 
	                        <td width="80" id="tot_sew_in"><div style="word-wrap:break-word; width:80px"><? echo number_format($total_sew_input); ?></div></td> 
	                        <td width="80" id="tot_sew_out"><div style="word-wrap:break-word; width:80px"><? echo number_format($total_sew_out); ?></div></td>   
	                        <td width="80" id="tot_iron"><div style="word-wrap:break-word; width:80px"><? echo number_format($total_iron); ?></div></td> 
	                        <td width="80" id="tot_re_iron"><div style="word-wrap:break-word; width:80px"><? echo number_format($total_re_iron); ?></div></td> 
	                        <td width="80" id="tot_finish"><div style="word-wrap:break-word; width:80px"><? echo number_format($total_finish); ?></div></td>
	                        <? $total_finish_gd_status = ($total_finish/$total_po_quantity)*100; ?>
	                        <td width="80"><div style="word-wrap:break-word; width:80px"><? echo number_format($total_finish_gd_status,2); ?></div></td >
	                        <td width="80"><div style="word-wrap:break-word; width:80px"><? echo number_format($total_ex_factory); ?></div></td >
	                        <? $total_ex_status = ($total_ex_factory/$total_po_quantity)*100; ?>
	                        <td><div style="word-wrap:break-word; width:80px"><? echo number_format($total_ex_status,2); ?></div></td>
	                    </tr>
	                 </table>
	                 <input type="hidden" id="graph_data" value="<? echo substr($chart_data_qnty,0,-1); ?>"/>
	            </div>
	        </div>
	        <div style="float:left; width:600px">   
	            <table>
	                <tr>
	                    <td height="21" width="600"><div id="chartdiv"> </div></td>
	                </tr>    
	            </table>
	        </div> 
	        <div style="clear:both"></div>
	        <table width="2650" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1">
	            <thead>
	                <tr>
	                    <th width="30">SL</th>    
	                    <th width="130">Order Number</th>
	                    <th width="60">Buyer Name</th>
	                    <th width="50">Job Year</th>
	                    <th width="80">Job Number</th>
	                    <th width="120">Style Name</th>
	                    <th width="80">Team Name</th>
	                    <th width="90">Team Member</th>
	                    <th width="130">Item Name</th>
	                    <th width="80">Country</th>
	                    <th width="80">Order Qty.</th>
	                    <th width="80">Ship Date</th>
	                    <th width="80">Ex-Factory Date</th>
	                    <th width="80">Days in Hand</th>
	                    <th width="80">Stan. Exc. Cut %</th>
	                    <th width="80">Total Cut Qty</th>
	                    <th width="80">Cutting Balance</th>
	                    <th width="80">Actual Exc. Cut %</th>
	                    <th width="80">Total Emb. Issue Qty</th>
	                    <th width="80">Total Emb. Rcv. Qty</th>
	                    <th width="80">Total Sew Input Qty</th>
	                    <th width="80">Total Sew Output Qty</th>
	                    <th width="80">Total Iron Qty</th>
	                    <th width="80">Total Re-Iron Qty</th>
	                    <th width="80">Total Finish Qty</th>
	                    <th width="80">Fin Goods Status</th>
	                    <th width="80">Reject Qty</th>
	                    <th width="80">Total Ship Out</th>
	                    <th width="80">Shortage/ Excess</th>
	                    <th width="85">Shipping Status</th>
	                    <th>Remarks</th>
	                 </tr>
	            </thead>
	        </table>
	        <div style="max-height:425px; overflow-y:scroll; width:2670px" id="scroll_body">
	            <table border="1" class="rpt_table" width="2650" rules="all" id="table_body">
					<? echo $html; ?>  
	            </table>
	        </div>
	        <table border="1" class="tbl_bottom" width="2650" rules="all" id="report_table_footer_1" >
	            <tr>
	                <td width="30"></td>
	                <td width="130"></td>
	                <td width="60"></td>
	                <td width="50"></td>
	                <td width="80"></td>
	                <td width="120"></td>
	                <td width="80"></td>
	                <td width="90"></td>
	                <td width="130">Total</td>
	                <td width="80"></td>
	                <td width="80" id="total_order_quantity"></td>
	                <td width="80"></td>
	                <td width="80"></td>
	                <td width="80"></td>
	                <td width="80"></td>
	                <td width="80" id="total_cutting"></td>
	                <td width="80" id="total_cutting_bal"></td>
	                <td width="80"></td>
	                <td width="80" id="total_emb_issue"></td>
	                <td width="80" id="total_emb_receive"></td>
	                <td width="80" id="total_sewing_input"></td>
	                <td width="80" id="total_sewing_out"></td>
	                <td width="80" id="total_iron_qnty"></td>
	                <td width="80" id="total_re_iron_qnty"></td>
	                <td width="80" id="total_finish_qnty"></td>
	                <td width="80"></td>
	                <td width="80" align="right" id="total_rej_value_td"></td>
	                <td width="80" id="total_out"></td>
	                <td width="80" id="total_shortage"></td>
	                <td width="85" id="ship_status"></td>
	                <td></td>
	             </tr>
	        </table>
	     </div>   
	    <?
	}
	else if ($type==6)//Order Country Shipdate Wise
	{
		$smv_sql=sql_select(" select a.company_name,a.buyer_name,a.set_smv, a.agent_name,a.style_ref_no,a.job_no_prefix_num,a.set_break_down,b.po_number from wo_po_details_master a ,wo_po_break_down b
   where  
   a.job_no=b.job_no_mst  and  a.status_active=1 and  a.is_deleted=0   and b.status_active=1 and  b.is_deleted=0 and (a.company_name!=0 
   or a.company_name is not null) and (a.buyer_name!=0 
   or a.buyer_name is not null)  and (b.po_number!=0
   or b.po_number is not null)   and (a.job_no_prefix_num!=0
   or a.job_no_prefix_num is not null)");
		foreach($smv_sql as $values_smv)
		{
			$smv_arrs[$values_smv[csf("company_name")]][$values_smv[csf("buyer_name")]][$values_smv[csf("job_no_prefix_num")]]=$values_smv[csf("set_break_down")];
			$smv_new[$values_smv[csf("company_name")]][$values_smv[csf("buyer_name")]][$values_smv[csf("job_no_prefix_num")]] [$values_smv[csf("po_number")]]=$values_smv[csf("set_smv")];
		}
		// echo "<pre>";
		// print_r($smv_arrs);
		// echo "</pre>";
 		$ex_factory_arr=array();
		
		$ex_factory_data=sql_select("select po_break_down_id, item_number_id, country_id, MAX(ex_factory_date) AS ex_factory_date,sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END)-sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) AS ex_factory_qnty from pro_ex_factory_mst where status_active=1 and is_deleted=0 group by po_break_down_id, item_number_id, country_id");
		foreach($ex_factory_data as $exRow)
		{
			$ex_factory_arr[$exRow[csf('po_break_down_id')]][$exRow[csf('item_number_id')]][$exRow[csf('country_id')]]['date']=$exRow[csf('ex_factory_date')];
			$ex_factory_arr[$exRow[csf('po_break_down_id')]][$exRow[csf('item_number_id')]][$exRow[csf('country_id')]]['qty']=$exRow[csf('ex_factory_qnty')];
		}
		unset($ex_factory_data);
		
		//print_r($gmts_prod_arr[3904]);die;
		$po_country_arr=array(); $po_country_data_arr=array(); $total_qty_arr=array();
		$buyer_array=array(); 
		$ship_status_cond="";
		if($shipping_status!=0) $ship_status_cond=" and c.shiping_status=$shipping_status";
		if($db_type==0) $select_job_year="year(b.insert_date) as job_year"; else $select_job_year="to_char(b.insert_date,'YYYY') as job_year"; 
		$country_ship_date2=str_replace("c.country_ship_date","a.pub_shipment_date",$country_ship_date);
		if($db_type==2)
		{
			 $order_sql="select a.id,a.file_no,a.grouping as ref_no, b.job_no_prefix_num, $select_job_year, b.agent_name, a.po_number, b.insert_date, a.job_no_mst,
			listagg((c.item_number_id),',') within group (order by c.item_number_id) as item_number_id,
			listagg((c.country_id),',') within group (order by c.country_id) as country_id,
			listagg((c.shiping_status),',') within group (order by c.country_id) as shiping_status,
			listagg((c.excess_cut_perc),',') within group (order by c.country_id) as excess_cut_perc,
			b.company_name, b.buyer_name, b.style_ref_no, b.team_leader, b.dealing_marchant, c.country_ship_date as shipment_date,sum(c.order_quantity) AS po_quantity,sum(c.order_total) as order_total 
			from wo_po_details_master b,wo_po_break_down a,wo_po_color_size_breakdown c 
			where a.job_no_mst=b.job_no and c.po_break_down_id=a.id and a.job_no_mst=c.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and LOWER(a.po_number) like LOWER('$search_string') and b.status_active=1 and b.is_deleted=0 $country_ship_date2 $company_name $buyer_name $garmentsNature $team_name_cond $team_member_cond $ship_status_cond $year_cond $file_no $ref_no $order_status_cond $item_cat_cond  
			group by a.id, a.file_no,a.grouping,b.job_no_prefix_num, b.insert_date, b.agent_name, a.po_number, b.insert_date, a.job_no_mst, b.company_name, b.buyer_name, b.style_ref_no, b.team_leader, b.dealing_marchant, c.country_ship_date order by a.id,c.country_ship_date";
		}
		else
		{
			 $order_sql="select a.id,a.file_no,a.grouping as ref_no, b.job_no_prefix_num, $select_job_year, b.agent_name, a.po_number, b.insert_date, a.job_no_mst,
			group_concat(c.item_number_id)  as item_number_id,
			group_concat(c.country_id)  as country_id,
			group_concat(c.shiping_status)  as shiping_status,
			group_concat(c.excess_cut_perc)  as excess_cut_perc,
			b.company_name, b.buyer_name, b.style_ref_no, b.team_leader, b.dealing_marchant, c.country_ship_date as shipment_date,sum(c.order_quantity) AS po_quantity,sum(c.order_total) as order_total 
			from wo_po_details_master b,wo_po_break_down a,wo_po_color_size_breakdown c 
			where a.job_no_mst=b.job_no and c.po_break_down_id=a.id and a.job_no_mst=c.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and LOWER(a.po_number) like LOWER('$search_string') and b.status_active=1 and b.is_deleted=0 $country_ship_date2 $company_name $buyer_name $garmentsNature $team_name_cond $team_member_cond $ship_status_cond $year_cond  $file_no $ref_no  $order_status_cond $item_cat_cond  
			group by a.id,a.file_no,a.grouping, b.job_no_prefix_num, b.insert_date, b.agent_name, a.po_number, b.insert_date, a.job_no_mst, b.company_name, b.buyer_name, b.style_ref_no, b.team_leader, b.dealing_marchant, c.country_ship_date order by a.id,c.country_ship_date";
		}
	 	//echo  $order_sql;
		/*$order_sql="select a.id, b.job_no_prefix_num, a.po_number, b.insert_date,c.order_rate,c.order_total, a.job_no_mst,c.excess_cut_perc, c.shiping_status,c.item_number_id,  c.plan_cut_qnty, b.company_name, b.buyer_name, b.style_ref_no,c.country_ship_date as shipment_date,c.country_id,c.order_quantity AS po_quantity from wo_po_details_master b,wo_po_break_down a,wo_po_color_size_breakdown c where a.job_no_mst=b.job_no and c.po_break_down_id=a.id and a.job_no_mst=c.job_no_mst and a.status_active=1 and a.is_deleted=0 and a.po_number like '$search_string' and b.status_active=1 and b.is_deleted=0 $country_ship_date $company_name $buyer_name $garmentsNature group by a.id, b.job_no_prefix_num, a.po_number, b.insert_date,c.order_rate,c.order_total, a.job_no_mst,c.excess_cut_perc, c.shiping_status,c.item_number_id,  c.plan_cut_qnty, b.company_name, b.buyer_name, b.style_ref_no,c.country_ship_date,c.country_id,c.order_quantity order by a.id,c.country_ship_date";*/
		//echo $order_sql;die;
		
		$po_id_str="";
		$result_po=sql_select($order_sql);
		foreach($result_po as $poID_data)
		{ 
			$po_id_str.=$poID_data[csf("id")].',';
		}
		$po_id_strs=chop($po_id_str,',');
		if ($db_type==0) 
		{
			if($po_id_strs!="") {$po_id_strs_cond="and c.po_break_down_id in($po_id_strs)";}else{$po_id_strs_cond="";}
		}
		else
		{
				$poID=explode(",",$po_id_strs);  
				$poID=array_chunk($poID,999);
				$po_id_strs_cond=" and";
				foreach($poID as $dtls_id)
				{
				if($po_id_strs_cond==" and")  $po_id_strs_cond.="(c.po_break_down_id in(".implode(',',$dtls_id).")"; else $po_id_strs_cond.=" or c.po_break_down_id in(".implode(',',$dtls_id).")";
				}
				$po_id_strs_cond.=")";
				//echo $po_id_strs_cond;die;
		}
		//------------------------

		if($db_type==0)
		{
			$prod_sql="SELECT c.po_break_down_id, c.item_number_id, country_id,
				IFNULL(sum(CASE WHEN c.production_type ='1' THEN c.production_quantity ELSE 0 END),0) AS cutting_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='2' THEN c.production_quantity ELSE 0 END),0) AS printing_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='2' and embel_name=1 THEN c.production_quantity ELSE 0 END),0) AS print,
				IFNULL(sum(CASE WHEN c.production_type ='2' and embel_name=2 THEN c.production_quantity ELSE 0 END),0) AS emb,
				IFNULL(sum(CASE WHEN c.production_type ='2' and embel_name=3 THEN c.production_quantity ELSE 0 END),0) AS wash,
				IFNULL(sum(CASE WHEN c.production_type ='2' and embel_name=4 THEN c.production_quantity ELSE 0 END),0) AS special,
				IFNULL(sum(CASE WHEN c.production_type ='2' and embel_name=5 THEN c.production_quantity ELSE 0 END),0) AS gmt_dyeing,
				IFNULL(sum(CASE WHEN c.production_type ='3' THEN c.production_quantity ELSE 0 END),0) AS printreceived_qnty, 
				IFNULL(sum(CASE WHEN c.production_type ='3' and embel_name=1 THEN c.production_quantity ELSE 0 END),0) AS printr,
				IFNULL(sum(CASE WHEN c.production_type ='3' and embel_name=2 THEN c.production_quantity ELSE 0 END),0) AS embr,
				IFNULL(sum(CASE WHEN c.production_type ='3' and embel_name=3 THEN c.production_quantity ELSE 0 END),0) AS washr,
				IFNULL(sum(CASE WHEN c.production_type ='3' and embel_name=4 THEN c.production_quantity ELSE 0 END),0) AS specialr,
				IFNULL(sum(CASE WHEN c.production_type ='3' and embel_name=5 THEN c.production_quantity ELSE 0 END),0) AS gmt_dyeingr,
				IFNULL(sum(CASE WHEN c.production_type ='4' THEN c.production_quantity ELSE 0 END),0) AS sewingin_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='5' THEN c.production_quantity ELSE 0 END),0) AS sewingout_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='7' THEN c.production_quantity ELSE 0 END),0) AS iron_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='7' THEN c.re_production_qty ELSE 0 END),0) AS re_iron_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='8' THEN c.production_quantity ELSE 0 END),0) AS finish_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='1' THEN c.reject_qnty ELSE 0 END),0) AS cutting_rej_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='8' THEN c.reject_qnty ELSE 0 END),0) AS finish_rej_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='5' THEN c.reject_qnty ELSE 0 END),0) AS sewingout_rej_qnty
			from 
				pro_garments_production_mst c
			where  
				c.status_active=1 and c.is_deleted=0 and c.company_id=$cbo_company_name $po_id_strs_cond $floor $location group by c.po_break_down_id, c.item_number_id, country_id";
		}
		else
		{
			$prod_sql= "SELECT c.po_break_down_id, c.item_number_id, country_id,
				NVL(sum(CASE WHEN c.production_type ='1' THEN c.production_quantity ELSE 0 END),0) AS cutting_qnty,
				NVL(sum(CASE WHEN c.production_type ='2' THEN c.production_quantity ELSE 0 END),0) AS printing_qnty,
				NVL(sum(CASE WHEN c.production_type ='2' and embel_name=1 THEN c.production_quantity ELSE 0 END),0) AS print,
				NVL(sum(CASE WHEN c.production_type ='2' and embel_name=2 THEN c.production_quantity ELSE 0 END),0) AS emb,
				NVL(sum(CASE WHEN c.production_type ='2' and embel_name=3 THEN c.production_quantity ELSE 0 END),0) AS wash,
				NVL(sum(CASE WHEN c.production_type ='2' and embel_name=4 THEN c.production_quantity ELSE 0 END),0) AS special,
				NVL(sum(CASE WHEN c.production_type ='2' and embel_name=5 THEN c.production_quantity ELSE 0 END),0) AS gmt_dyeing,
				NVL(sum(CASE WHEN c.production_type ='3' THEN c.production_quantity ELSE 0 END),0) AS printreceived_qnty, 
				NVL(sum(CASE WHEN c.production_type ='3' and embel_name=1 THEN c.production_quantity ELSE 0 END),0) AS printr,
				NVL(sum(CASE WHEN c.production_type ='3' and embel_name=2 THEN c.production_quantity ELSE 0 END),0) AS embr,
				NVL(sum(CASE WHEN c.production_type ='3' and embel_name=3 THEN c.production_quantity ELSE 0 END),0) AS washr,
				NVL(sum(CASE WHEN c.production_type ='3' and embel_name=4 THEN c.production_quantity ELSE 0 END),0) AS specialr,
				NVL(sum(CASE WHEN c.production_type ='3' and embel_name=5 THEN c.production_quantity ELSE 0 END),0) AS gmt_dyeingr,
				NVL(sum(CASE WHEN c.production_type ='4' THEN c.production_quantity ELSE 0 END),0) AS sewingin_qnty,
				NVL(sum(CASE WHEN c.production_type ='5' THEN c.production_quantity ELSE 0 END),0) AS sewingout_qnty,
				NVL(sum(CASE WHEN c.production_type ='7' THEN c.production_quantity ELSE 0 END),0) AS iron_qnty,
				NVL(sum(CASE WHEN c.production_type ='7' THEN c.re_production_qty ELSE 0 END),0) AS re_iron_qnty,
				NVL(sum(CASE WHEN c.production_type ='8' THEN c.production_quantity ELSE 0 END),0) AS finish_qnty,
				NVL(sum(CASE WHEN c.production_type ='1' THEN c.reject_qnty ELSE 0 END),0) AS cutting_rej_qnty,
				NVL(sum(CASE WHEN c.production_type ='8' THEN c.reject_qnty ELSE 0 END),0) AS finish_rej_qnty,
				NVL(sum(CASE WHEN c.production_type ='5' THEN c.reject_qnty ELSE 0 END),0) AS sewingout_rej_qnty
			from 
				pro_garments_production_mst c
			where  
				c.status_active=1 and c.is_deleted=0 and c.company_id=$cbo_company_name $po_id_strs_cond $floor $location group by c.po_break_down_id, c.item_number_id, country_id";
		}
		//echo $prod_sql;die;
		$gmts_prod_arr=array();
		$res_gmtsData=sql_select($prod_sql);
		foreach($res_gmtsData as $gmtsRow)
		{
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][1]['cQty']=$gmtsRow[csf('cutting_qnty')];
			
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][2]['pQty']=$gmtsRow[csf('printing_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][2]['print']=$gmtsRow[csf('print')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][2]['emb']=$gmtsRow[csf('emb')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][2]['wash']=$gmtsRow[csf('wash')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][2]['special']=$gmtsRow[csf('special')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][2]['gmt_dyeing']=$gmtsRow[csf('gmt_dyeing')];
			
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][3]['prQty']=$gmtsRow[csf('printreceived_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][3]['print']=$gmtsRow[csf('printr')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][3]['emb']=$gmtsRow[csf('embr')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][3]['wash']=$gmtsRow[csf('washr')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][3]['special']=$gmtsRow[csf('specialr')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][3]['gmt_dyeingr']=$gmtsRow[csf('gmt_dyeingr')];
			
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][4]['sQty']=$gmtsRow[csf('sewingin_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][5]['soQty']=$gmtsRow[csf('sewingout_qnty')];
			
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][7]['iQty']=$gmtsRow[csf('iron_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][7]['riQty']=$gmtsRow[csf('re_iron_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][8]['fQty']=$gmtsRow[csf('finish_qnty')];
			
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][1]['crQty']=$gmtsRow[csf('cutting_rej_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][5]['sorQty']=$gmtsRow[csf('sewingout_rej_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][8]['frQty']=$gmtsRow[csf('finish_rej_qnty')];
		}
		unset($res_gmtsData);


		$result=sql_select($order_sql);
		$i=0; $k=0; $date=date("Y-m-d"); 
		foreach($result as $orderRes)
		{ 
		   $i++;
			$cutting_qnty=0; $embl_recv_qnty=0; $sewingin_qnty=0; $sewingout_qnty=0; $iron_qnty=0; $re_iron_qnty=0;
			$finish_qnty=0;  $ex_factory_date=""; $actual_exces_cut=0; $issue_print =0; $issue_emb =0;
			$issue_wash =0;  $issue_special =0 ;$embl_iss_qty=0; $rcv_print =0; $rcv_emb =0; 
			$rcv_wash = 0;  $rcv_special =0;  $embl_recv_qty=0; $rej_value=0; $ex_factory_qnty=0;$issue_gd=0;$rcv_gd=0;
			
			foreach(array_unique(explode(",",$orderRes[csf("item_number_id")])) as $item_id)
			{
				foreach(array_unique(explode(",",$orderRes[csf("country_id")])) as $c_id)
				{

					$cutting_qnty+=$gmts_prod_arr[$orderRes[csf("id")]][$item_id][$c_id][1]['cQty'];
					$embl_recv_qnty+=$gmts_prod_arr[$orderRes[csf("id")]][$item_id][$c_id][3]['prQty'];
					$sewingin_qnty+=$gmts_prod_arr[$orderRes[csf("id")]][$item_id][$c_id][4]['sQty'];
					$sewingout_qnty+=$gmts_prod_arr[$orderRes[csf("id")]][$item_id][$c_id][5]['soQty'];
					$iron_qnty+=$gmts_prod_arr[$orderRes[csf("id")]][$item_id][$c_id][7]['iQty'];
					$re_iron_qnty+=$gmts_prod_arr[$orderRes[csf("id")]][$item_id][$c_id][7]['riQty'];
					$finish_qnty+=$gmts_prod_arr[$orderRes[csf("id")]][$item_id][$c_id][8]['fQty'];
					
				    $po_country_data_arr[$orderRes[csf("id")]][$orderRes[csf("shipment_date")]][ex_factory_date][]=$ex_factory_arr[$orderRes[csf("id")]][$item_id][$c_id]['date'];
					
					$ex_factory_qnty+=$ex_factory_arr[$orderRes[csf("id")]][$item_id][$c_id]['qty'];
					$actual_exces_cut+=$gmts_prod_arr[$orderRes[csf("id")]][$item_id][$c_id][1]['cQty'];
					$issue_print+= $gmts_prod_arr[$orderRes[csf("id")]][$item_id][$c_id][2]['print'];
					$issue_emb += $gmts_prod_arr[$orderRes[csf("id")]][$item_id][$c_id][2]['emb'];
					$issue_wash+= $gmts_prod_arr[$orderRes[csf("id")]][$item_id][$c_id][2]['wash'];
					$issue_special+= $gmts_prod_arr[$orderRes[csf("id")]][$item_id][$c_id][2]['special'];
					$issue_gd+= $gmts_prod_arr[$orderRes[csf("id")]][$item_id][$c_id][2]['gmt_dyeing'];
					$embl_iss_qty+=$issue_print+$issue_emb+$issue_wash+$issue_special+$issue_gd;
					$rcv_print+= $gmts_prod_arr[$orderRes[csf("id")]][$item_id][$c_id][3]['print'];
					$rcv_emb+=$gmts_prod_arr[$orderRes[csf("id")]][$item_id][$c_id][3]['emb'];
					$rcv_wash+= $gmts_prod_arr[$orderRes[csf("id")]][$item_id][$c_id][3]['wash'];
					$rcv_special+= $gmts_prod_arr[$orderRes[csf("id")]][$item_id][$c_id][3]['special'];
					$rcv_gd+= $gmts_prod_arr[$orderRes[csf("id")]][$item_id][$c_id][3]['gmt_dyeingr'];
					$embl_recv_qty+=$rcv_print+$rcv_emb+$rcv_wash+$rcv_special+$rcv_gd;
					$rej_value+=$gmts_prod_arr[$orderRes[csf("id")]][$item_id][$c_id][1]['crQty']+$gmts_prod_arr[$orderRes[csf("id")]][$item_id][$c_id][5]['sorQty']+$gmts_prod_arr[$orderRes[csf("id")]][$item_id][$c_id][8]['frQty'];
				}
			}
			if($orderRes[csf("buyer_name")]!="")
			{
				$buyer_array[$orderRes[csf("buyer_name")]]['buyer_name']=$orderRes[csf("buyer_name")];
				$buyer_array[$orderRes[csf("buyer_name")]]['poQty']+=$orderRes[csf("po_quantity")];
				$buyer_array[$orderRes[csf("buyer_name")]]['poVal']+=$orderRes[csf("order_total")];
				$buyer_array[$orderRes[csf("buyer_name")]]['ex']+=$ex_factory_qnty;
				$buyer_array[$orderRes[csf("buyer_name")]]['cQty']+=$cutting_qnty;
				$buyer_array[$orderRes[csf("buyer_name")]]['prQty']+=$embl_recv_qnty;
				$buyer_array[$orderRes[csf("buyer_name")]]['sQty']+=$sewingin_qnty;
				$buyer_array[$orderRes[csf("buyer_name")]]['soQty']+=$sewingout_qnty;
				$buyer_array[$orderRes[csf("buyer_name")]]['iQty']+=$iron_qnty;
				$buyer_array[$orderRes[csf("buyer_name")]]['reiQty']+=$re_iron_qnty;
				$buyer_array[$orderRes[csf("buyer_name")]]['fQty']+=$finish_qnty;
			}
			
			//**************************************************************************************************************************
			$po_country_data_arr[$orderRes[csf("id")]][$orderRes[csf("shipment_date")]][po_qty]+=$orderRes[csf("po_quantity")];
			$po_country_data_arr[$orderRes[csf("id")]][$orderRes[csf("shipment_date")]][po_value]+=$orderRes[csf("order_total")];
			$po_country_data_arr[$orderRes[csf("id")]][$orderRes[csf("shipment_date")]][po_name]=$orderRes[csf("po_number")];
			$po_country_data_arr[$orderRes[csf("id")]][$orderRes[csf("shipment_date")]][file_no]=$orderRes[csf("file_no")];
			$po_country_data_arr[$orderRes[csf("id")]][$orderRes[csf("shipment_date")]][ref_no]=$orderRes[csf("ref_no")];
			$po_country_data_arr[$orderRes[csf("id")]][$orderRes[csf("shipment_date")]][buyer_name]=$orderRes[csf("buyer_name")];
			$po_country_data_arr[$orderRes[csf("id")]][$orderRes[csf("shipment_date")]][job_number]=$orderRes[csf("job_no_prefix_num")];
			$po_country_data_arr[$orderRes[csf("id")]][$orderRes[csf("shipment_date")]][job_year]=$orderRes[csf("job_year")];
			$po_country_data_arr[$orderRes[csf("id")]][$orderRes[csf("shipment_date")]][full_job]=$orderRes[csf("job_no_mst")];
			$po_country_data_arr[$orderRes[csf("id")]][$orderRes[csf("shipment_date")]][style_name]=$orderRes[csf("style_ref_no")];
			$po_country_data_arr[$orderRes[csf("id")]][$orderRes[csf("shipment_date")]][agent_name]=$orderRes[csf("agent_name")];
			$po_country_data_arr[$orderRes[csf("id")]][$orderRes[csf("shipment_date")]][team_leader]=$orderRes[csf("team_leader")]; 
			$po_country_data_arr[$orderRes[csf("id")]][$orderRes[csf("shipment_date")]][dealing_marchant]=$orderRes[csf("dealing_marchant")];                       
			$po_country_data_arr[$orderRes[csf("id")]][$orderRes[csf("shipment_date")]][shipment_date]=$orderRes[csf("shipment_date")];
			$po_country_data_arr[$orderRes[csf("id")]][$orderRes[csf("shipment_date")]][cut_qty]+=$cutting_qnty;
			$po_country_data_arr[$orderRes[csf("id")]][$orderRes[csf("shipment_date")]][exfactory]+=$ex_factory_qnty;
			$po_country_data_arr[$orderRes[csf("id")]][$orderRes[csf("shipment_date")]][emblish_receive]+=$embl_recv_qnty;                        
			$po_country_data_arr[$orderRes[csf("id")]][$orderRes[csf("shipment_date")]][sewing_in]+=$sewingin_qnty;
			$po_country_data_arr[$orderRes[csf("id")]][$orderRes[csf("shipment_date")]][sewing_out]+=$sewingout_qnty;
			$po_country_data_arr[$orderRes[csf("id")]][$orderRes[csf("shipment_date")]][rej_value]+=$rej_value;
			//$po_country_data_arr[$orderRes[csf("id")]][$orderRes[csf("shipment_date")]][ex_factory_qty]+=$ex_factory_qnty;
			
			$po_country_data_arr[$orderRes[csf("id")]][$orderRes[csf("shipment_date")]][item_id]=$orderRes[csf("item_number_id")];
			$po_country_data_arr[$orderRes[csf("id")]][$orderRes[csf("shipment_date")]][country_id]=$orderRes[csf("country_id")];
			//$po_country_data_arr[$orderRes[csf("id")]][$orderRes[csf("shipment_date")]][item_id][]=$orderRes[csf("item_number_id")];
			$po_country_data_arr[$orderRes[csf("id")]][$orderRes[csf("shipment_date")]][excess_cut_perc]=$orderRes[csf("excess_cut_perc")];
			$po_country_data_arr[$orderRes[csf("id")]][$orderRes[csf("shipment_date")]][shiping_status]=$orderRes[csf("shiping_status")]; 
			$po_country_data_arr[$orderRes[csf("id")]][$orderRes[csf("shipment_date")]][insert_date]=$orderRes[csf("insert_date")];	
			$po_country_data_arr[$orderRes[csf("id")]][$orderRes[csf("shipment_date")]][iron_qty]+=$iron_qnty;
			$po_country_data_arr[$orderRes[csf("id")]][$orderRes[csf("shipment_date")]][reiron]+=$re_iron_qnty;
			$po_country_data_arr[$orderRes[csf("id")]][$orderRes[csf("shipment_date")]][finish_qty]+=$finish_qnty;  
			$po_country_data_arr[$orderRes[csf("id")]][$orderRes[csf("shipment_date")]][print_issue]+=$issue_print;
			$po_country_data_arr[$orderRes[csf("id")]][$orderRes[csf("shipment_date")]][embet_issue]+=$issue_emb;
			$po_country_data_arr[$orderRes[csf("id")]][$orderRes[csf("shipment_date")]][issue_gd]+=$issue_gd;
			
			$po_country_data_arr[$orderRes[csf("id")]][$orderRes[csf("shipment_date")]][wash_issue]+=$issue_wash; 
			$po_country_data_arr[$orderRes[csf("id")]][$orderRes[csf("shipment_date")]][special_issue]+=$issue_special;
			$po_country_data_arr[$orderRes[csf("id")]][$orderRes[csf("shipment_date")]][print_receive]+=$rcv_print;
			$po_country_data_arr[$orderRes[csf("id")]][$orderRes[csf("shipment_date")]][embet_receive]+=$rcv_emb;
			$po_country_data_arr[$orderRes[csf("id")]][$orderRes[csf("shipment_date")]][wash_receive]+=$rcv_wash; 
			$po_country_data_arr[$orderRes[csf("id")]][$orderRes[csf("shipment_date")]][special_receive]+=$rcv_special; 
			$po_country_data_arr[$orderRes[csf("id")]][$orderRes[csf("shipment_date")]][rcv_gd]+=$rcv_gd; 
			
			//*************************************************************************************************************************
			$total_qty_arr[po_qty]+=$orderRes[csf("po_quantity")];
			$total_qty_arr[po_value]+=$orderRes[csf("order_total")];
			if($s==0) 
			{
				$display_font_color="";
				$font_end="";
			}
			else 
			{
				$display_font_color="&nbsp;<font style='display:none'>";
				$font_end="</font>";
			}
			$company_name=$orderRes[csf("company_name")];
			$po_id=$orderRes[csf("id")];
			if(($ex_factory_date=="" || $ex_factory_date=="0000-00-00")) $ex_factory_date="&nbsp;"; else $ex_factory_date=change_date_format($ex_factory_date);
			$s++;		
		}// end main foreach 
		//print_r($po_country_data_arr[3904]);die;
		$template_id_arr=return_library_array("select po_number_id, template_id from tna_process_mst group by po_number_id, template_id","po_number_id","template_id");
		
		//print_r($po_country_data_arr[4044]);die;
    ?>
    <div>
    	<table width="1580" cellspacing="0" >
            <tr class="form_caption" style="border:none;">
                <td colspan="21" align="center" style="border:none;font-size:16px; font-weight:bold" >
                    <? echo "Order Country Wise Production Report"; ?>    
                </td>
            </tr>
            <tr style="border:none;">
                <td colspan="21" align="center" style="border:none; font-size:14px;">
                    Company Name : <? echo $company_library[str_replace("'","",$cbo_company_name)]; ?>                                
                </td>
            </tr>
            <tr style="border:none;">
                <td colspan="21" align="center" style="border:none;font-size:12px; font-weight:bold">
                    <?
                        if(str_replace("'","",trim($txt_date_from))!="" && str_replace("'","",trim($txt_date_to))!="")
                        {
                            echo "From $fromDate To $toDate" ;
                        }
                    ?>
                </td>
            </tr>
        </table>
        <div id="data_panel" align="center" style="width:100%">
         <script>
		 	function new_window()
			 {
				document.getElementById('scroll_body1').style.overflow="auto";
				document.getElementById('scroll_body1').style.maxHeight="none";
				
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write(document.getElementById('details_reports').innerHTML);
				d.close();
				
				document.getElementById('scroll_body1').style.overflowY="scroll";
				document.getElementById('scroll_body1').style.maxHeight="425px";
			 }
          </script>
        
        </div>
        <div style="float:left; width:1150px">
        	<input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
        </div>
        <div style="float:left; width:1230px" id="details_reports">
            <table width="1180" cellspacing="0" border="1" class="rpt_table" rules="all" id="" >
                <thead>
                    <tr>
                        <th colspan="15" >In-House Order Production
                        <br /> 
                         <?
                        if(str_replace("'","",trim($txt_date_from))!="" && str_replace("'","",trim($txt_date_to))!="")
                        {
                            echo "From $fromDate To $toDate" ;
                        }
                    	?>
                        </th>
                    </tr>
                    <tr style="font-size:11px">
                        <th width="30">Sl.</th>    
                        <th width="80">Buyer Name</th>
                        <th width="80">Order Qty.(Pcs)</th>
                        <th width="80">PO Value</th>
                        <th width="80">Total Cut Qty</th>
                        <th width="80">Cutting Balance</th>
                        <th width="80">Total Emb. Rcv. Qty</th>
                        <th width="80">Total Sew Input Qty</th>
                        <th width="80">Total Sew Qty</th>
                        <th width="80">Total Iron Qty</th>
                        <th width="80">Total Re-Iron Qty</th>
                        <th width="80">Total Finish Qty</th>
                        <th width="80">Fin Goods Status</th>
                        <th width="80">Ex-Fac</th>
                        <th>Ex-Fac%</th>
                     </tr>
                </thead>
            </table>
            <div style="max-height:425px; overflow-y:scroll; width:1200px" id="scroll_body1" >
                <table cellspacing="0" border="1" class="rpt_table"  width="1180" rules="all" id="" >
                <?
					$i=1; $total_po_quantity=0;$total_po_value=0; $total_cut=0; $total_print_iss=0;
					$total_print_re=0; $total_sew_input=0; $total_sew_out=0; $total_iron=0; $total_re_iron=0; $total_finish=0;$total_ex_factory=0;
					$buyer_po_value=array();
					foreach($buyer_array as $row)
					{
						$buyer_po_value[$row["poVal"]]=$row["poVal"];
					}
					array_multisort($buyer_po_value, SORT_DESC,$buyer_array);//buyer_name
					foreach($buyer_array as $buyer_id=>$value)
                    {
                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>"  style="font-size:11px">
                            <td width="30"><? echo $i;?></td>
                            <td width="80"><? echo $buyer_short_library[$value["buyer_name"]]; ?></td>
                            <td width="80" align="right"><? echo number_format($value["poQty"]);?></td>
                            <td width="80" align="right"><? echo number_format($value["poVal"],2);?></td>
                            <td width="80" align="right"><? echo number_format($value["cQty"]); ?></td>
                            <td width="80" align="right"><? echo number_format($value["poQty"]-$value["cQty"]); ?></td>
                            <td width="80" align="right"><? echo number_format($value["prQty"]); ?></td>
                            <td width="80" align="right"><? echo number_format($value["sQty"]); ?></td>
                            <td width="80" align="right"><? echo number_format($value["soQty"]); ?></td>
                            <td width="80" align="right"><? echo number_format($value["iQty"]); ?></td>
                            <td width="80" align="right"><? echo number_format($value["reiQty"]); ?></td>
                            <td width="80" align="right"><? echo number_format($value["fQty"]); ?></td>
                            <? $finish_gd_status = ($value["fQty"]/$value["poQty"])*100; ?>
                            <td width="80" align="right"><? echo number_format($finish_gd_status,2)." %"; ?></td>
                            <td width="80" align="right"><? echo number_format($value["ex"]); ?></td>
                            <? $ex_gd_status = ($value["ex"]/$value["poQty"])*100; ?>
                            <td width="" align="right"><? echo  number_format($ex_gd_status,2)." %"; ?></td>
                        </tr>	
                        <?		
                            $total_po_quantity+=$value["poQty"];
                            $total_po_value+=$value["poVal"];
                            $total_cut+=$value["cQty"];
                            $total_print_re+=$value["prQty"];
                            $total_sew_input+=$value["sQty"];
                            $total_sew_out+=$value["soQty"];
                            $total_iron+=$value["iQty"];
                            $total_re_iron+=$value["reiQty"];
                            $total_finish+=$value["fQty"];
                            $total_ex_factory+=$value["ex"];
                           
                        $i++;
                    }//end foreach 1st
                    
                    $chart_data_qnty="Order Qty;".$total_po_quantity."\n"."Cutting;".$total_cut."\n"."Sew In;".$total_sew_input."\n"."Sew Out ;".$total_sew_out."\n"."Iron ;".$total_iron."\n"."Finish ;".$total_finish."\n"."Ex-Fact;".$total_ex_factory."\n";
                ?>
                </table>
                <table border="1" class="tbl_bottom"  width="1180" rules="all" id="" >
                    <tr style="font-size:11px"> 
                        <td width="30">&nbsp;</td> 
                        <td width="80" align="right">Total</td> 
                        <td width="80" align="right" id="tot_po_quantity"><? echo number_format($total_po_quantity); ?></td> 
                        <td width="80" align="right" id="tot_po_value"><? echo number_format($total_po_value,2); ?></td> 
                        <td width="80" align="right" id="tot_cutting"><? echo number_format($total_cut); ?></td>
                        <td width="80" align="right" id="tot_cutting"><? echo number_format($total_po_quantity-$total_cut); ?></td>
                        <td width="80" align="right" id="tot_emb_rcv"><? echo number_format($total_print_re); ?></td> 
                        <td width="80" align="right" id="tot_sew_in"><? echo number_format($total_sew_input); ?></td> 
                        <td width="80" align="right" id="tot_sew_out"><? echo number_format($total_sew_out); ?></td>   
                        <td width="80" align="right" id="tot_iron"><? echo number_format($total_iron); ?></td> 
                        <td width="80" align="right" id="tot_re_iron"><? echo number_format($total_re_iron); ?></td> 
                        <td width="80" align="right" id="tot_finish"><? echo number_format($total_finish); ?></td>
                        <? $total_finish_gd_status = ($total_finish/$total_po_quantity)*100; ?>
                        <td width="80" align="right"><? echo number_format($total_finish_gd_status,2); ?></td >
                        <td width="80" align="right"><? echo number_format($total_ex_factory); ?></td >
                        <? $total_ex_status = ($total_ex_factory/$total_po_quantity)*100; ?>
                        <td align="right"><? echo number_format($total_ex_status,2); ?></td>
                    </tr>
                 </table>
                 <input type="hidden" id="graph_data" value="<? echo substr($chart_data_qnty,0,-1); ?>"/>
            </div>
        </div>
        <div style="float:left; width:600px">   
            <table>
                <tr>
                    <td height="21" width="600"><div id="chartdiv"> </div></td>
                </tr>    
            </table>
        </div> 
        <div style="clear:both"></div>
        <table width="2920" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1">
            <thead>
                <tr>
                    <th width="30">SL</th>    
                    <th width="130">Order Number</th>
                    <th width="60">Buyer Name</th>
                    <th width="50">Job Year</th>
                    <th width="80">Job Number</th>
                  
                    <th width="120">Style Name </th>
                    <th width="80">File No</th>
                    <th width="80">Ref. No</th>
                    <th width="80">Team Name</th>
                    <th width="90">Team Member</th>
                    <th width="90">Agent Name</th>
                    <th width="50">Image</th>
                    <th width="130">Item Name</th>
                    <th width="50">SMV</th>
                    <th width="80">Order Qty.</th>
                    <th width="80">Ship Date</th>
                    <th width="80">Ex-Factory Date</th>
                    <th width="80">Days in Hand</th>
                    <th width="80">Stan. Exc. Cut %</th>
                    <th width="80">Total Cut Qty</th>
                    <th width="80">Cutting Balance</th>
                    <th width="80">Actual Exc. Cut %</th>
                    <th width="80">Total Emb. Issue Qty</th>
                    <th width="80">Total Emb. Rcv. Qty</th>
                    <th width="80">Total Sew Input Qty</th>
                    <th width="80">Total Sew Output Qty</th>
                    <th width="80">Total Iron Qty</th>
                    <th width="80">Total Re-Iron Qty</th>
                    <th width="80">Total Finish Qty</th>
                    <th width="80">Fin Goods Status</th>
                    <th width="80">Reject Qty</th>
                    <th width="80">Total Ship Out</th>
                    <th width="80">Shortage/ Excess</th>
                    <th width="85">Shipping Status</th>
                    <th>Remarks</th>
                 </tr>
            </thead>
        </table>
        <div style="max-height:425px; overflow-y:scroll; width:2890px" id="scroll_body">
            <table border="1" class="rpt_table" width="2870" rules="all" id="table_body">
			  <?
              $date=date("Y-m-d");
            
              $sl=0;  $total_po_qty=0;  $total_cut_qty=0; $total_embl_issue=0; $total_embl_receive=0; $total_exfac_qty=0; $total_finish_qty=0;
              $total_reject_qty=0; $total_reiron_qty=0; $total_iron_qty=0; $total_sewin_out=0; $total_sewin_in=0;
				foreach($po_country_data_arr as $po_id=>$po_value)
				{
					foreach($po_value as $c_date=>$date_value)
					{
						$sl++;
						
						$embl_issue_total="";	
						if($date_value[print_issue]!=0)   $embl_issue_total .= 'PR='.$date_value[print_issue];
						if($date_value[embet_issue]!=0)   $embl_issue_total .= '<br> EM='.$date_value[embet_issue];
						if($date_value[wash_issue]!=0)    $embl_issue_total .= '<br> WA='.$date_value[wash_issue];
						if($date_value[special_issue]!=0) $embl_issue_total .= '<br> SP='.$date_value[special_issue];
						if($date_value[issue_gd]!=0) $embl_issue_total .= '<br> GD='.$date_value[issue_gd];
						$embl_receive_total="";	
						if($date_value[print_receive]!=0) $embl_receive_total .= 'PR='.$date_value[print_receive];
						if($date_value[embet_receive]!=0) $embl_receive_total .= '<br> EM='.$date_value[embet_receive];
						if($date_value[wash_receive]!=0) $embl_receive_total .= '<br> WA='.$date_value[wash_receive];
						if($date_value[special_receive]!=0) $embl_receive_total .= '<br> SP='.$date_value[special_receive];
						if($date_value[rcv_gd]!=0) $embl_receive_total .= '<br> GD='.$date_value[rcv_gd];
						$item_id_all=array_unique(explode(',',$date_value[item_id]));
						$item_name=array();
						foreach($item_id_all as $i_id)
						{
							$item_name[]=$garments_item[$i_id];
						}
						$country_id_all=implode("*",array_unique(explode(",",($date_value[country_id]))));
						//echo $country_id_all."#";
						$p=0;
						$total_exces_cut=0;
						$total_exces_cut_percentage=0;
						//echo $date_value[excess_cut_perc];
						foreach(array_unique(explode(",",$date_value[excess_cut_perc])) as $qty_id)
						{
							$total_exces_cut+=$qty_id;
							$p++;
							
						}
						$total_exces_cut_percentage=$total_exces_cut/$p;
						
                        $ex_factory_date='';
						foreach(array_unique($date_value[ex_factory_date]) as $e_date)
						{
							if($e_date!="" && $e_date!="0000-00-00")
							{
								$e_date=date("Y-m-d",strtotime($e_date));
								if($e_date>$ex_factory_date) $ex_factory_date=$e_date;
							}
						}
                       $shipping_status=0;
                       if(count(array_unique(explode(',',$date_value[shiping_status])))>1)
					   {
                        $shipping_status=2;
					   }
                       else
					   {
					    $shipping_status=$date_value[shiping_status][0];
					   }
					   
					   $shipment_date=date("Y-m-d",strtotime($date_value[shipment_date]));
					   
                       if($shipping_status==1 || $shipping_status==2)
						{
						    $days_remian=datediff("d",$date,$shipment_date)-1; 
							
							if($shipment_date > $date) 
							{
								$color="";
							}
							else if($shipment_date < $date) 
							{
								$color="red";
							}														
							else if($shipment_date >= $date && $days_remian<=5 ) 
							{
								$color="orange";
							}
						} 
						else if($shipping_status==3)
						{
							$days_remian=datediff("d",$ex_factory_date,$shipment_date)-1;
							if($shipment_date >= $ex_factory_date) 
							{ 
								$color="green";
							}
							else if($shipment_date < $ex_factory_date) 
							{ 
								$color="#2A9FFF";
							}
							
						}//end if conditioncut_qty
                        $finish_status = $date_value[finish_qty]*100/$date_value[po_qty];
						$actual_exces_cut="";
						if($date_value[cut_qty] > $date_value[po_qty]) $actual_exces_cut=number_format((($date_value[cut_qty]-$date_value[po_qty])/$date_value[po_qty])*100,2);
						//if($date_value[cut_qty] < $date_value[po_qty]) $actual_exces_cut=""; else $actual_exces_cut=number_format( (($date_value[cut_qty]-$date_value[po_qty])/$date_value[po_qty])*100,2)."%";
					   // $days_remian=datediff("d",$shipment_date,$date)-1; 
						//if($finish_status==3) $days_remian=$days_remian; else $days_remian="---";
						if($actual_exces_cut > number_format($orderRes[csf("excess_cut")],2)) $excess_bgcolor="bgcolor='#FF0000'"; else $excess_bgcolor="";
						if($actual_exces_cut==0) $actual_exces_cut_per=""; else $actual_exces_cut_per=number_format($actual_exces_cut,2)." %";
						$template_id=$template_id_arr[$po_id];
						if ($sl%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						
					 ?>
					  <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2nd<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $sl; ?>">
							<td width="30"><? echo $sl; ?></td>
                            <td width="130" align="center"><p><a href="##" onclick="progress_comment_popup('<? echo $po_id; ?>','<? echo $template_id; ?>','<? echo $tna_process_type; ?>')"><? echo $date_value[po_name]; ?></a></p></td>
                            <td width="60" align="center"><p><? echo $buyer_short_library[$date_value[buyer_name]]; ?></p></td>
                            <td width="50" align="center"><p><? echo $date_value[job_year]; ?></p></td>
                            <td width="80" align="center"><p><? echo $date_value[job_number]; ?></p></td>
                          
                            <td width="120" align="center"><p><? echo $date_value[style_name]; ?></p></td>
                            <td width="80" align="center"><p><? echo $date_value[file_no]; ?></p></td>
                            <td width="80" align="center"><p><? echo $date_value[ref_no]; ?></p></td>
                            <td width="80" align="center"><p><? echo $team_name_arr[$date_value[team_leader]]; ?></p></td>
                            <td width="90" align="center"><p><? echo $team_member_arr[$date_value[dealing_marchant]]; ?></p></td>
                            <td width="90" align="center"><p><? echo $buyer_short_library[$date_value[agent_name]]; ?></p></td>
                            <td width="50" onclick="openmypage_image('requires/order_wise_production_report_controller.php?action=show_image&job_no=<? echo $date_value[full_job]; ?>','Image View')"><img src="../../<? echo $imge_arr[$date_value[full_job]]; ?>" height="25" width="30" /></td>
                            <td width="130"><p><? echo implode(",",$item_name); ?></p></td>
                            <td width="50"><p>
                            <?php
								  $set_break_down=$smv_arrs[$company_name][$date_value[buyer_name]][$date_value[job_number]];
							      $smv="";
	                              if(strpos( $set_break_down, '__' ) !== false )
	                              {  
	                             	$set_break_down= explode("__", $set_break_down);
	                             	foreach($set_break_down as $val)
	                            	{
	                            		$sm=explode("_", $val);
			                        	 $smv=$sm[2].',';
	                           		}
	                           		  echo rtrim($smv,",");
	                               }
	                               else 
	                               {  
	                               		//echo "od";$set_break_down2=explode("_", $set_break_down);
			                        	foreach($set_break_down2 as $new_val)
			                        	{
			                        		if($new_val[2]!="")
			                        		{
			                        			$smv=$new_val[2].",";
			                        		}
			                        		

			                        	}
			                        	echo $smv_new[$company_name][$date_value[buyer_name]][$date_value[job_number]][$date_value[po_name]];


	                               }
 	                           


                            ?>


                             </p> </td>
                            <td width="80" align="right"><a href="##" onclick="openmypage_order(<? echo $po_id; ?>,<? echo $company_name; ?>,'<? echo implode("*",$item_id_all); ?>','<? echo $date_value[shipment_date]; ?>','country_ship_po_qty')"><? echo $date_value[po_qty]; $total_po_qty+=$date_value[po_qty]; ?></a></td>
                            <td width="80" align="center" bgcolor="<? echo $color; ?>"><? echo change_date_format($date_value[shipment_date]); //$total_po_qty+=$date_value[shipment_date]; ?></td>
                            <td width="80" align="center"><p><a href="##" onclick="openmypage_country_ship_date(<? echo $po_id.",'".implode("*",$item_id_all)."','exfactoryCountry_shipdate','','',".$type.",'$country_id_all'"; ?>)"><? echo change_date_format($ex_factory_date); ?></a></p></td>
                            <td width="80" align="center"><?  echo $days_remian; ?></td>
                            <td width="80" align="right"><? echo $total_exces_cut_percentage;  ?></td>
                            <td width="80" align="right"><a href="##" onclick="openmypage_country_ship_date(<? echo $po_id.",'".implode("*",$item_id_all)."','country_shipdate_wise_qty','1','',".$type.",'$country_id_all'"; ?>)"><? echo $date_value[cut_qty]; $total_cut_qty+=$date_value[cut_qty];?></a></td>
                            <td width="80" align="right"><? echo ($date_value[po_qty]-$date_value[cut_qty]);?></td>
                            <td width="80" align="right" '.$excess_bgcolor.'><? echo $actual_exces_cut_per; ?></td>
                            <td width="80" align="right"><a href="##" onclick="openmypage_country_ship_date(<? echo $po_id.",'".implode("*",$item_id_all)."','country_shipdate_wise_embl','2','',".$type.",'$country_id_all'"; ?>)"><? echo $embl_issue_total; $total_embl_issue+=$embl_issue_total?></a></td>
                            <td width="80" align="right"><a href="##" onclick="openmypage_country_ship_date(<? echo $po_id.",'".implode("*",$item_id_all)."','country_shipdate_wise_embl','3','',".$type.",'$country_id_all'"; ?>)"><? echo $embl_receive_total;  $total_embl_receive+=$embl_receive_total; ?></a></td>
                            <td width="80" align="right"><a href="##" onclick="openmypage_country_ship_date(<? echo $po_id.",'".implode("*",$item_id_all)."','country_shipdate_display_qty','4','',".$type.",'$country_id_all'"; ?>)"><? echo $date_value[sewing_in]; $total_sewin_in+=$date_value[sewing_in];?></a></td>
                            <td width="80" align="right"><a href="##" onclick="openmypage_country_ship_date(<? echo $po_id.",'".implode("*",$item_id_all)."','country_shipdate_wise_qty','5','',".$type.",'$country_id_all'"; ?>)"><? echo $date_value[sewing_out]; $total_sewin_out+=$date_value[sewing_out];?></a></td>
                            <td width="80" align="right"><a href="##" onclick="openmypage_country_ship_date(<? echo $po_id.",'".implode("*",$item_id_all)."','country_shipdate_display_qty','7','',".$type.",'$country_id_all'"; ?>)"><? echo $date_value[iron_qty]; $total_iron_qty+=$date_value[iron_qty];?></a></td>
                            <td width="80" align="right"><a href="##" onclick="openmypage_country_ship_date(<? echo $po_id.",'".implode("*",$item_id_all)."','country_shipdate_display_qty','9','',".$type.",'$country_id_all'"; ?>)"><? echo $date_value[reiron]; $total_reiron_qty+=$date_value[reiron];?></a></td>
                            <td width="80" align="right"><a href="##" onclick="openmypage_country_ship_date(<? echo $po_id.",'".implode("*",$item_id_all)."','country_shipdate_display_qty','8','',".$type.",'$country_id_all'"; ?>)"><? echo $date_value[finish_qty]; $total_finish_qty+=$date_value[finish_qty];?></a></td>
                            <td width="80" align="right"><? echo number_format($finish_status,2)." %"; ?></td>
                            <td width="80" align="right"><a href="##" onclick="openmypage_rej(<? echo $po_id.",'".implode("*",$item_id_all)."','reject_qty_country','','',".$type.",'$country_id_all'"; ?>)"><? echo $date_value[rej_value]; $total_reject_qty+=$date_value[rej_value];?></a></td>
                            <td width="80" align="right"><? echo $date_value[exfactory]; $total_exfac_qty+=$date_value[exfactory];?></td>
                            <td width="80" align="right"><? $sortage=$date_value[po_qty]-$date_value[exfactory];
                            if($sortage>0) { $total_sortage+=$sortage; echo $sortage; } ?></td>
                            <td width="85"><? echo $shipment_status[$shipping_status];  ?></td>
                            <td align="center"><a href="##" onclick="openmypage_remark('<? echo $po_id?>','<? echo implode("*",$item_id_all); ?>','<? echo $country_id_all; ?>','date_wise_production_report_shipdate')">Veiw</a></td>
                    </tr>
					<?
					}
				}
				 ?>  
            </table>	
            <table border="1" class="tbl_bottom" width="2870" rules="all" id="report_table_footer_1" >
                <tr>
                    <td width="30"></td>
                    <td width="130"></td>
                    <td width="60"></td>
                    <td width="50"></td>
                    <td width="80"></td>
                  
                    <td width="120"></td>
                     <td width="80"></td>
                      <td width="80"></td>
                    <td width="80"></td>
                    <td width="90"></td>
                    <td width="90"></td>
                    <td width="50"></td>
                    <td width="130">Total</td>
                    <td width="80" id="total_order_quantity"><? echo number_format($total_qty_arr[po_qty]); ?></td>
                    <td width="80"><? // echo number_format($total_po_value,2); ?></td>
                    <td width="80"></td>
                    <td width="80"></td>
                    <td width="80"></td>
                    <td width="80" id="total_cutting"><? echo $total_cut_qty; ?></td>
                    <td width="80" id="total_cutting_bal"><? //echo $total_cut_qty_bal; ?></td>
                    <td width="80"></td>
                    <td width="80" id="total_emb_issue"><? echo $total_embl_issue; ?></td>
                    <td width="80" id="total_emb_receive"><? echo $total_embl_receive; ?></td>
                    <td width="80" id="total_sewing_input"><? echo $total_sewin_in; ?></td>
                    <td width="80" id="total_sewing_out"><? echo $total_sewin_out; ?></td>
                    <td width="80" id="total_iron_qnty"><? echo $total_iron_qty; ?></td>
                    <td width="80" id="total_re_iron_qnty"><? echo $total_reiron_qty; ?></td>
                    <td width="80" id="total_finish_qnty"><? echo $total_finish_qty; ?></td>
                    <td width="80"></td>
                    <td width="80" align="right" id="total_rej_value_td"><? echo $total_reject_qty; ?></td>
                    <td width="80" id="total_out"><? echo $total_exfac_qty; ?></td>
                    <td width="80" id="total_shortage"><? echo $total_sortage; ?></td>
                    <td width="85" id="ship_status"></td>
                    <td></td>
                 </tr>
			</table>
        </div>
     </div>   
    <?
	}
	else
	{
		$ex_factory_arr=array();
		
		$ex_factory_data=sql_select("select po_break_down_id as po_id, item_number_id, country_id, location, MAX(ex_factory_date) AS ex_factory_date,sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END)-sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) AS ex_factory_qnty from pro_ex_factory_mst where status_active=1 and is_deleted=0 group by po_break_down_id, item_number_id, country_id, location");
		foreach($ex_factory_data as $exRow)
		{
			$ex_factory_arr[$exRow[csf('po_id')]][$exRow[csf('item_number_id')]][$exRow[csf('country_id')]][$exRow[csf('location')]]['date']=$exRow[csf('ex_factory_date')];
			$ex_factory_arr[$exRow[csf('po_id')]][$exRow[csf('item_number_id')]][$exRow[csf('country_id')]][$exRow[csf('location')]]['qty']=$exRow[csf('ex_factory_qnty')];
		}
		//print_r($ex_factory_arr[3834]);die;
		unset($ex_factory_data);
		
		

		//echo "select po_break_down_id, item_number_id, country_id, sum(order_quantity) as qnty, sum(order_total) as value from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 $ship_status_cond group by po_break_down_id, item_number_id, country_id";
		$po_country_arr=array(); $po_country_data_arr=array();
		$ship_status_cond="";
		if($shipping_status!=0) $ship_status_cond=" and shiping_status=$shipping_status";
		$poCountryData=sql_select("select po_break_down_id, item_number_id, country_id, shiping_status, sum(order_quantity) as qnty, sum(order_total) as value from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 $ship_status_cond group by po_break_down_id, item_number_id, country_id, shiping_status");
		foreach($poCountryData as $row)
		{
			$po_country_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]['qnty']=$row[csf('qnty')];
			$po_country_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]['value']=$row[csf('value')];
			$po_country_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]['shiping_status']=$row[csf('shiping_status')];
			$po_country_arr[$row[csf('po_break_down_id')]].=$row[csf('country_id')].",";
		}
		unset($poCountryData);
		
		
		
		$buyer_array=array(); 
		
		if($db_type==0) $select_job_year="year(b.insert_date) as job_year"; else $select_job_year="to_char(b.insert_date,'YYYY') as job_year"; 
		
		$order_sql="select a.id, b.job_no_prefix_num, $select_job_year, b.team_leader, b.dealing_marchant, a.po_number, a.po_quantity, a.unit_price, a.po_total_price, a.job_no_mst, a.pub_shipment_date as shipment_date,a.shiping_status,a.excess_cut,a.plan_cut, b.company_name, b.buyer_name, b.set_break_down, b.style_ref_no, c.location, c.floor_id from wo_po_details_master b,wo_po_break_down a left join pro_garments_production_mst c on c.po_break_down_id=a.id and c.status_active=1 and c.is_deleted=0 $location $floor where a.job_no_mst=b.job_no and a.status_active=1 and b.status_active=1 and LOWER(a.po_number) like LOWER('$search_string') $txt_date $company_name $buyer_name $garmentsNature $team_name_cond $team_member_cond  $order_status_cond $item_cat_cond  
		group by a.id, b.job_no_prefix_num, b.insert_date, b.team_leader, b.dealing_marchant, a.po_number, a.po_quantity, a.job_no_mst, a.pub_shipment_date, a.shiping_status, a.excess_cut, a.unit_price, a.po_total_price, a.plan_cut, b.company_name, b.buyer_name, b.set_break_down, b.style_ref_no, c.location, c.floor_id order by a.id, c.location, c.floor_id";
		//echo $order_sql;


		$po_id_str="";
		$result_po=sql_select($order_sql);
		foreach($result_po as $orderRes_po)
		{ 
			$po_id_str.=$orderRes_po[csf("id")].',';
		}
		$po_id_strs=chop($po_id_str,',');
		if ($db_type==0) 
		{
			if($po_id_strs!="") {$po_id_strs_cond="and c.po_break_down_id in($po_id_strs)";}else{$po_id_strs_cond="";}
		}
		else
		{
				$poID=explode(",",$po_id_strs);  

				$poID=array_chunk($poID,999);
				$po_id_strs_cond=" and";
				foreach($poID as $dtls_id)
				{
				if($po_id_strs_cond==" and")  $po_id_strs_cond.="(c.po_break_down_id in(".implode(',',$dtls_id).")"; else $po_id_strs_cond.=" or c.po_break_down_id in(".implode(',',$dtls_id).")";
				}
				$po_id_strs_cond.=")";
				//echo $po_id_strs_cond;die;
		}
		//------------------------
		if($db_type==0)
		{
			$prod_sql="SELECT c.po_break_down_id, c.item_number_id, country_id, location, floor_id,
				IFNULL(sum(CASE WHEN c.production_type ='1' THEN c.production_quantity ELSE 0 END),0) AS cutting_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='2' THEN c.production_quantity ELSE 0 END),0) AS printing_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='2' and embel_name=1 THEN c.production_quantity ELSE 0 END),0) AS print,
				IFNULL(sum(CASE WHEN c.production_type ='2' and embel_name=2 THEN c.production_quantity ELSE 0 END),0) AS emb,
				IFNULL(sum(CASE WHEN c.production_type ='2' and embel_name=3 THEN c.production_quantity ELSE 0 END),0) AS wash,
				IFNULL(sum(CASE WHEN c.production_type ='2' and embel_name=4 THEN c.production_quantity ELSE 0 END),0) AS special,
				IFNULL(sum(CASE WHEN c.production_type ='2' and embel_name=5 THEN c.production_quantity ELSE 0 END),0) AS gmt_dyeing,
				IFNULL(sum(CASE WHEN c.production_type ='3' THEN c.production_quantity ELSE 0 END),0) AS printreceived_qnty, 
				IFNULL(sum(CASE WHEN c.production_type ='3' and embel_name=1 THEN c.production_quantity ELSE 0 END),0) AS printr,
				IFNULL(sum(CASE WHEN c.production_type ='3' and embel_name=2 THEN c.production_quantity ELSE 0 END),0) AS embr,
				IFNULL(sum(CASE WHEN c.production_type ='3' and embel_name=3 THEN c.production_quantity ELSE 0 END),0) AS washr,
				IFNULL(sum(CASE WHEN c.production_type ='3' and embel_name=4 THEN c.production_quantity ELSE 0 END),0) AS specialr,
				IFNULL(sum(CASE WHEN c.production_type ='3' and embel_name=5 THEN c.production_quantity ELSE 0 END),0) AS gmt_dyeingr,
				IFNULL(sum(CASE WHEN c.production_type ='4' THEN c.production_quantity ELSE 0 END),0) AS sewingin_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='5' THEN c.production_quantity ELSE 0 END),0) AS sewingout_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='7' THEN c.production_quantity ELSE 0 END),0) AS iron_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='7' THEN c.re_production_qty ELSE 0 END),0) AS re_iron_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='8' THEN c.production_quantity ELSE 0 END),0) AS finish_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='1' THEN c.reject_qnty ELSE 0 END),0) AS cutting_rej_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='8' THEN c.reject_qnty ELSE 0 END),0) AS finish_rej_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='5' THEN c.reject_qnty ELSE 0 END),0) AS sewingout_rej_qnty
			from 
				pro_garments_production_mst c
			where  
				c.status_active=1 and c.is_deleted=0 and c.company_id=$cbo_company_name $po_id_strs_cond $location $floor group by c.po_break_down_id, c.item_number_id, country_id, location, floor_id";
		}
		else
		{
			$prod_sql= "SELECT c.po_break_down_id, c.item_number_id, country_id, location, floor_id,
				NVL(sum(CASE WHEN c.production_type ='1' THEN c.production_quantity ELSE 0 END),0) AS cutting_qnty,
				NVL(sum(CASE WHEN c.production_type ='2' THEN c.production_quantity ELSE 0 END),0) AS printing_qnty,
				NVL(sum(CASE WHEN c.production_type ='2' and embel_name=1 THEN c.production_quantity ELSE 0 END),0) AS print,
				NVL(sum(CASE WHEN c.production_type ='2' and embel_name=2 THEN c.production_quantity ELSE 0 END),0) AS emb,
				NVL(sum(CASE WHEN c.production_type ='2' and embel_name=3 THEN c.production_quantity ELSE 0 END),0) AS wash,
				NVL(sum(CASE WHEN c.production_type ='2' and embel_name=4 THEN c.production_quantity ELSE 0 END),0) AS special,
				NVL(sum(CASE WHEN c.production_type ='2' and embel_name=5 THEN c.production_quantity ELSE 0 END),0) AS gmt_dyeing,
				NVL(sum(CASE WHEN c.production_type ='3' THEN c.production_quantity ELSE 0 END),0) AS printreceived_qnty, 
				NVL(sum(CASE WHEN c.production_type ='3' and embel_name=1 THEN c.production_quantity ELSE 0 END),0) AS printr,
				NVL(sum(CASE WHEN c.production_type ='3' and embel_name=2 THEN c.production_quantity ELSE 0 END),0) AS embr,
				NVL(sum(CASE WHEN c.production_type ='3' and embel_name=3 THEN c.production_quantity ELSE 0 END),0) AS washr,
				NVL(sum(CASE WHEN c.production_type ='3' and embel_name=4 THEN c.production_quantity ELSE 0 END),0) AS specialr,
				NVL(sum(CASE WHEN c.production_type ='3' and embel_name=5 THEN c.production_quantity ELSE 0 END),0) AS gmt_dyeingr,
				NVL(sum(CASE WHEN c.production_type ='4' THEN c.production_quantity ELSE 0 END),0) AS sewingin_qnty,
				NVL(sum(CASE WHEN c.production_type ='5' THEN c.production_quantity ELSE 0 END),0) AS sewingout_qnty,
				NVL(sum(CASE WHEN c.production_type ='7' THEN c.production_quantity ELSE 0 END),0) AS iron_qnty,
				NVL(sum(CASE WHEN c.production_type ='7' THEN c.re_production_qty ELSE 0 END),0) AS re_iron_qnty,
				NVL(sum(CASE WHEN c.production_type ='8' THEN c.production_quantity ELSE 0 END),0) AS finish_qnty,
				NVL(sum(CASE WHEN c.production_type ='1' THEN c.reject_qnty ELSE 0 END),0) AS cutting_rej_qnty,
				NVL(sum(CASE WHEN c.production_type ='8' THEN c.reject_qnty ELSE 0 END),0) AS finish_rej_qnty,
				NVL(sum(CASE WHEN c.production_type ='5' THEN c.reject_qnty ELSE 0 END),0) AS sewingout_rej_qnty
			from 
				pro_garments_production_mst c
			where  
				c.status_active=1 and c.is_deleted=0 and c.company_id=$cbo_company_name $po_id_strs_cond $location $floor group by c.po_break_down_id, c.item_number_id, country_id, location, floor_id";
		}
		
		//echo $prod_sql;
		
		$gmts_prod_arr=array();
		$res_gmtsData=sql_select($prod_sql);
		foreach($res_gmtsData as $gmtsRow)
		{
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][1]['cQty']=$gmtsRow[csf('cutting_qnty')];
			
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][2]['pQty']=$gmtsRow[csf('printing_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][2]['print']=$gmtsRow[csf('print')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][2]['emb']=$gmtsRow[csf('emb')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][2]['wash']=$gmtsRow[csf('wash')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][2]['special']=$gmtsRow[csf('special')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][2]['gmt_dyeing']=$gmtsRow[csf('gmt_dyeing')];
			
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][3]['prQty']=$gmtsRow[csf('printreceived_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][3]['print']=$gmtsRow[csf('printr')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][3]['emb']=$gmtsRow[csf('embr')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][3]['wash']=$gmtsRow[csf('washr')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][3]['special']=$gmtsRow[csf('specialr')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][3]['gmt_dyeingr']=$gmtsRow[csf('gmt_dyeingr')];
			
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][4]['sQty']=$gmtsRow[csf('sewingin_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][5]['soQty']=$gmtsRow[csf('sewingout_qnty')];
			
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][7]['iQty']=$gmtsRow[csf('iron_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][7]['riQty']=$gmtsRow[csf('re_iron_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][8]['fQty']=$gmtsRow[csf('finish_qnty')];
			
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][1]['crQty']=$gmtsRow[csf('cutting_rej_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][5]['sorQty']=$gmtsRow[csf('sewingout_rej_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][8]['frQty']=$gmtsRow[csf('finish_rej_qnty')];
		}
		
		unset($res_gmtsData);









		$result=sql_select($order_sql);
		$i=0; $k=0; $date=date("Y-m-d"); 
		foreach($result as $orderRes)
		{ 
		   	$i++;
		   	if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
		   	$setArr = explode("__",$orderRes[csf("set_break_down")] );
		   	$countArr = count($setArr); 
		   	if($countArr==0) $countArr=1; $s=0;
		   	for($j=0;$j<$countArr;$j++)
		   	{
		   		$smv_arr[$setItemArr[0]]=$setItemArr[2];
			   	$company_name=$orderRes[csf("company_name")];
				$po_id=$orderRes[csf("id")];
				$location=$orderRes[csf("location")];
				$floor_id=$orderRes[csf("floor_id")];
			  	$setItemArr = explode("_",$setArr[$j]);
			   	$item_id=$setItemArr[0];
			   	$set_qnty=$setItemArr[1];
			   	if($item_id>0)
			   	{
					$country=array_unique(explode(",",substr($po_country_arr[$orderRes[csf("id")]],0,-1)));
					foreach($country as $country_id)
					{
						$k++;
						$po_quantity_in_pcs = $po_country_data_arr[$orderRes[csf("id")]][$item_id][$country_id]['qnty'];
						$po_value = $po_country_data_arr[$orderRes[csf("id")]][$item_id][$country_id]['value'];
						$ship_status_id = $po_country_data_arr[$orderRes[csf("id")]][$item_id][$country_id]['shiping_status'];
						if($ship_status_id>0)
						{
							$unit_price=$orderRes[csf("unit_price")]/$set_qnty;
							$ex_factory_date=$ex_factory_arr[$orderRes[csf("id")]][$item_id][$country_id][$location]['date'];
							$ex_factory_qnty=$ex_factory_arr[$orderRes[csf("id")]][$item_id][$country_id][$location]['qty'];
							
							$color=""; $days_remian="";
							if($ship_status_id==1 || $ship_status_id==2)
							{
								$days_remian=datediff("d",$date,$orderRes[csf("shipment_date")])-1; 
								if($orderRes[csf("shipment_date")] > $date) 
								{
									$color="";
								}
								else if($orderRes[csf("shipment_date")] < $date) 
								{
									$color="red";
								}														
								else if($orderRes[csf("shipment_date")] >= $date && $days_remian<=5 ) 
								{
									$color="orange";
								}
							} 
							else if($ship_status_id==3)
							{
								$days_remian=datediff("d",$ex_factory_date,$orderRes[csf("shipment_date")])-1;
								if($orderRes[csf("shipment_date")] >= $ex_factory_date) 
								{ 
									$color="green";
								}
								else if($orderRes[csf("shipment_date")] < $ex_factory_date) 
								{ 
									$color="#2A9FFF";
								}
								
							}//end if condition
							
							$cutting_qnty=$gmts_prod_arr[$orderRes[csf("id")]][$item_id][$country_id][$location][$floor_id][1]['cQty'];
							$embl_recv_qnty=$gmts_prod_arr[$orderRes[csf("id")]][$item_id][$country_id][$location][$floor_id][3]['prQty'];
							$sewingin_qnty=$gmts_prod_arr[$orderRes[csf("id")]][$item_id][$country_id][$location][$floor_id][4]['sQty'];
							$sewingout_qnty=$gmts_prod_arr[$orderRes[csf("id")]][$item_id][$country_id][$location][$floor_id][5]['soQty'];
							$iron_qnty=$gmts_prod_arr[$orderRes[csf("id")]][$item_id][$country_id][$location][$floor_id][7]['iQty'];
							$re_iron_qnty=$gmts_prod_arr[$orderRes[csf("id")]][$item_id][$country_id][$location][$floor_id][7]['riQty'];
							$finish_qnty=$gmts_prod_arr[$orderRes[csf("id")]][$item_id][$country_id][$location][$floor_id][8]['fQty'];
							if($orderRes[csf("buyer_name")]!="")
							{
								$buyer_array[$orderRes[csf("buyer_name")]]['buyer_name']=$orderRes[csf("buyer_name")];
								$buyer_array[$orderRes[csf("buyer_name")]]['poQty']+=$po_quantity_in_pcs;
								$buyer_array[$orderRes[csf("buyer_name")]]['poVal']+=$po_value;
								$buyer_array[$orderRes[csf("buyer_name")]]['ex']+=$ex_factory_qnty;
								$buyer_array[$orderRes[csf("buyer_name")]]['cQty']+=$cutting_qnty;
								$buyer_array[$orderRes[csf("buyer_name")]]['prQty']+=$embl_recv_qnty;
								$buyer_array[$orderRes[csf("buyer_name")]]['sQty']+=$sewingin_qnty;
								$buyer_array[$orderRes[csf("buyer_name")]]['soQty']+=$sewingout_qnty;
								$buyer_array[$orderRes[csf("buyer_name")]]['iQty']+=$iron_qnty;
								$buyer_array[$orderRes[csf("buyer_name")]]['reiQty']+=$re_iron_qnty;
								$buyer_array[$orderRes[csf("buyer_name")]]['fQty']+=$finish_qnty;
							}
							
							
							$actual_exces_cut = $gmts_prod_arr[$orderRes[csf("id")]][$item_id][$country_id][$location][$floor_id][1]['cQty'];
							if($actual_exces_cut < $po_quantity_in_pcs) $actual_exces_cut=""; else $actual_exces_cut=number_format((($actual_exces_cut-$po_quantity_in_pcs)/$po_quantity_in_pcs)*100,2);
		
							$issue_print = $gmts_prod_arr[$orderRes[csf("id")]][$item_id][$country_id][$location][$floor_id][2]['print'];
							$issue_emb = $gmts_prod_arr[$orderRes[csf("id")]][$item_id][$country_id][$location][$floor_id][2]['emb'];
							$issue_wash = $gmts_prod_arr[$orderRes[csf("id")]][$item_id][$country_id][$location][$floor_id][2]['wash'];
							$issue_special = $gmts_prod_arr[$orderRes[csf("id")]][$item_id][$country_id][$location][$floor_id][2]['special'];
							$issue_gd = $gmts_prod_arr[$orderRes[csf("id")]][$item_id][$country_id][$location][$floor_id][2]['gmt_dyeing'];
							
							$embl_iss_qty=$issue_print+$issue_emb+$issue_wash+$issue_special+$issue_gd;
							
							$embl_issue_total="";
							if($issue_print!=0) $embl_issue_total .= 'PR='.$issue_print;
							if($issue_emb!=0) $embl_issue_total .= ', EM='.$issue_emb;
							if($issue_wash!=0) $embl_issue_total .= ', WA='.$issue_wash;
							if($issue_special!=0) $embl_issue_total .= ', SP='.$issue_special;
							if($issue_gd!=0) $embl_issue_total .= ', GD='.$issue_gd;
		
							$rcv_print = $gmts_prod_arr[$orderRes[csf("id")]][$item_id][$country_id][$location][$floor_id][3]['print'];
							$rcv_emb = $gmts_prod_arr[$orderRes[csf("id")]][$item_id][$country_id][$location][$floor_id][3]['emb'];
							$rcv_wash = $gmts_prod_arr[$orderRes[csf("id")]][$item_id][$country_id][$location][$floor_id][3]['wash'];
							$rcv_special = $gmts_prod_arr[$orderRes[csf("id")]][$item_id][$country_id][$location][$floor_id][3]['special'];
							$rcv_gd = $gmts_prod_arr[$orderRes[csf("id")]][$item_id][$country_id][$location][$floor_id][3]['gmt_dyeingr'];
							
							$embl_receive_total="";	
							if($rcv_print!=0) $embl_receive_total .= 'PR='.$rcv_print;
							if($rcv_emb!=0) $embl_receive_total .= ', EM='.$rcv_emb;
							if($rcv_wash!=0) $embl_receive_total .= ', WA='.$rcv_wash;
							if($rcv_special!=0) $embl_receive_total .= ', SP='.$rcv_special;
							if($rcv_gd!=0) $embl_receive_total .= ', GD='.$rcv_gd;
							
							$embl_recv_qty=$rcv_print+$rcv_emb+$rcv_wash+$rcv_special+$rcv_gd;
							
							//$rej_value=$proRes[csf("finish_rej_qnty")]+$proRes[csf("sewingout_rej_qnty")];	
							$rej_value=$gmts_prod_arr[$orderRes[csf("id")]][$item_id][$country_id][$location][$floor_id][1]['crQty']+$gmts_prod_arr[$orderRes[csf("id")]][$item_id][$country_id][$location][$floor_id][5]['sorQty']+$gmts_prod_arr[$orderRes[csf("id")]][$item_id][$country_id][$location][$floor_id][8]['frQty'];
							$shortage = $po_quantity_in_pcs-$ex_factory_qnty;
							$finish_status = $finish_qnty*100/$po_quantity_in_pcs;
							
							if($s==0) 
							{
								$display_font_color="";
								$font_end="";
							}
							else 
							{
								$display_font_color="&nbsp;<font style='display:none'>";
								$font_end="</font>";
							}
	
							if(($ex_factory_date=="" || $ex_factory_date=="0000-00-00")) $ex_factory_date="&nbsp;"; else $ex_factory_date=change_date_format($ex_factory_date);
							//if($orderRes[csf("shiping_status")]==3) $days_remian=$days_remian; else $days_remian="---";
							if($actual_exces_cut > number_format($orderRes[csf("excess_cut")],2)) $excess_bgcolor="bgcolor='#FF0000'"; else $excess_bgcolor="";
							if($actual_exces_cut==0) $actual_exces_cut_per=""; else $actual_exces_cut_per=number_format($actual_exces_cut,2)." %";
							$total_rej_value+=$rej_value; 
							 
							$html.="<tr bgcolor='$bgcolor' onclick=change_color('tr_2nd$k','$bgcolor') id=tr_2nd$k>";
							$html.='<td width="30">'.$display_font_color.$i.$font_end.'</td>
										<td width="130"><p>'.$display_font_color.$orderRes[csf("po_number")].$font_end.'</p></td>
										<td width="60"><p>'.$display_font_color.$buyer_short_library[$orderRes[csf("buyer_name")]].$font_end.'</p></td>
										<td width="50" align="center"><p>'.$display_font_color.$orderRes[csf("job_year")].$font_end.'</p></td>
										<td width="80" align="center"><p>'.$display_font_color.$orderRes[csf("job_no_prefix_num")].$font_end.'</p></td>
										<td width="120"><p>'.$display_font_color.$orderRes[csf("style_ref_no")].$font_end.'</p></td>
										<td width="80"><p>'.$display_font_color.$team_name_arr[$orderRes[csf("team_leader")]].$font_end.'</p></td>
										<td width="90"><p>'.$display_font_color.$team_member_arr[$orderRes[csf("dealing_marchant")]].$font_end.'</p></td>
										<td width="130"><p>'.$garments_item[$item_id].'</p></td>
										<td width="80"><p>'.$country_library[$country_id].'&nbsp;</p></td>
										<td width="80" align="right"><a href="##" onclick="openmypage_order('.$po_id.",".$company_name.",".$item_id.",$country_id,'OrderPopupCountry'".')">'.$po_quantity_in_pcs.'</a></td>
										<td width="80" align="center" bgcolor="'.$color.'">'.change_date_format($orderRes[csf("shipment_date")]).'&nbsp;</td>
										<td width="80"><p>'.$location_library[$orderRes[csf("location")]].'&nbsp;</p></td>
										<td width="80"><p>'.$floor_library[$orderRes[csf("floor_id")]].'&nbsp;</p></td>
										<td width="80" align="right"><a href="##" onclick="openmypage('.$po_id.",".$item_id.",'exfactoryCountry','$location','','','$country_id'".')">'.$ex_factory_date.'</a></td>
										<td width="80" align="center">'.$days_remian.'&nbsp;</td>
										<td width="80" align="right">&nbsp;'.number_format($orderRes[csf("excess_cut")],2)." %".'</td>
										<td width="80" align="right"><a href="##" onclick="openmypage('.$po_id.",".$item_id.",'1','$location','$floor_id','$type','$country_id'".')">'.$cutting_qnty.'</a></td>
										<td width="80" align="right" '.$excess_bgcolor.'>&nbsp;'.$actual_exces_cut_per.'</td>
										<td width="80" align="right"><font color="$bgcolor" style="display:none">'.$embl_iss_qty.'</font>&nbsp;<a href="##" onclick="openmypage('.$po_id.",".$item_id.",'2','$location','$floor_id','$type','$country_id'".')">'.$embl_issue_total.'</a></td>
										<td width="80" align="right"><font color="$bgcolor" style="display:none">'.$embl_recv_qty.'</font>&nbsp;<a href="##" onclick="openmypage('.$po_id.",".$item_id.",'3','$location','$floor_id','$type','$country_id'".')">'.$embl_receive_total.'</a></td>
										<td width="80" align="right"><a href="##" onclick="openmypage('.$po_id.",".$item_id.",'4','$location','$floor_id','$type','$country_id'".')">'.$sewingin_qnty.'</a></td>
										<td width="80" align="right"><a href="##" onclick="openmypage('.$po_id.",".$item_id.",'5','$location','$floor_id','$type','$country_id'".')">'.$sewingout_qnty.'</a></td>
										<td width="80" align="right"><a href="##" onclick="openmypage('.$po_id.",".$item_id.",'7','$location','$floor_id','$type','$country_id'".')">'.$iron_qnty.'</a></td>
										<td width="80" align="right"><a href="##" onclick="openmypage('.$po_id.",".$item_id.",'9','$location','$floor_id','$type','$country_id'".')">'.$re_iron_qnty.'</a></td>
										<td width="80" align="right"><a href="##" onclick="openmypage('.$po_id.",".$item_id.",'8','$location','$floor_id','$type','$country_id'".')">'.$finish_qnty.'</a></td>
										<td width="80" align="right">&nbsp;'.number_format($finish_status,2)." %".'</td>
										<td width="80" align="right"><a href="##" onclick="openmypage_rej('.$po_id.",".$item_id.",'reject_qty','$location','$floor_id','$type','$country_id'".')">'.$rej_value.'</a></td>
										<td width="80" align="right">'.$ex_factory_qnty.'</td>
										<td width="80" align="right">'.$shortage.'</td>
										<td width="85">'.$shipment_status[$ship_status_id].'</td>
										<td>&nbsp;<a href="##" onclick="openmypage_remark('.$po_id.",".$item_id.",0,'date_wise_production_report'".')">Veiw</a></td>
									</tr>';
							 $s++;
						}
								
					}
				}
			} //end for loop
		}// end main foreach 
    ?>
    <div>
    	<table width="1580" cellspacing="0" >
            <tr class="form_caption" style="border:none;">
                <td colspan="21" align="center" style="border:none;font-size:16px; font-weight:bold" >
                    <? echo "Order Country Wise Production Report"; ?>    
                </td>
            </tr>
            <tr style="border:none;">
                <td colspan="21" align="center" style="border:none; font-size:14px;">
                    Company Name : <? echo $company_library[str_replace("'","",$cbo_company_name)]; ?>                                
                </td>
            </tr>
            <tr style="border:none;">
                <td colspan="21" align="center" style="border:none;font-size:12px; font-weight:bold">
                    <?
                        if(str_replace("'","",trim($txt_date_from))!="" && str_replace("'","",trim($txt_date_to))!="")
                        {
                            echo "From $fromDate To $toDate" ;
                        }
                    ?>
                </td>
            </tr>
        </table>
		<div style="float:left; width:1830px">
            <table width="1180" cellspacing="0" border="1" class="rpt_table" rules="all" id="" >
                <thead>
                    <tr>
                        <th colspan="15" >In-House Order Production </th>
                    </tr>
                    <tr>
                        <th width="30">Sl.</th>    
                        <th width="80">Buyer Name</th>
                        <th width="80">Order Qty.(Pcs)</th>
                        <th width="80">PO Value</th>
                        <th width="80">Total Cut Qty</th>
                        <th width="80">Cutting Balance</th>
                        <th width="80">Total Emb. Rcv. Qty</th>
                        <th width="80">Total Sew Input Qty</th>
                        <th width="80">Total Sew Qty</th>
                        <th width="80">Total Iron Qty</th>
                        <th width="80">Total Re-Iron Qty</th>
                        <th width="80">Total Finish Qty</th>
                        <th width="80">Fin Goods Status</th>
                        <th width="80">Ex-Fac</th>
                        <th>Ex-Fac%</th>
                     </tr>
                </thead>
            </table>
            <div style="max-height:425px; overflow-y:scroll; width:1200px" >
                <table cellspacing="0" border="1" class="rpt_table"  width="1180" rules="all" id="" >
                <?
					$i=1; $total_po_quantity=0;$total_po_value=0; $total_cut=0; $total_print_iss=0;
					$total_print_re=0; $total_sew_input=0; $total_sew_out=0; $total_iron=0; $total_re_iron=0; $total_finish=0;$total_ex_factory=0;
					$buyer_po_value=array();
					foreach($buyer_array as $row)
					{
						$buyer_po_value[$row["poVal"]]=$row["poVal"];
					}
					array_multisort($buyer_po_value, SORT_DESC,$buyer_array);//buyer_name
					foreach($buyer_array as $buyer_id=>$value)
                    {
                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                            <td width="30"><? echo $i;?></td>
                            <td width="80"><? echo $buyer_short_library[$value["buyer_name"]]; ?></td>
                            <td width="80" align="right"><? echo number_format($value["poQty"]);?></td>
                            <td width="80" align="right"><? echo number_format($value["poVal"],2);?></td>
                            <td width="80" align="right"><? echo number_format($value["cQty"]); ?></td>
                            <td width="80" align="right"><? echo number_format($value["poQty"]-$value["cQty"]); ?></td>
                            <td width="80" align="right"><? echo number_format($value["prQty"]); ?></td>
                            <td width="80" align="right"><? echo number_format($value["sQty"]); ?></td>
                            <td width="80" align="right"><? echo number_format($value["soQty"]); ?></td>
                            <td width="80" align="right"><? echo number_format($value["iQty"]); ?></td>
                            <td width="80" align="right"><? echo number_format($value["reiQty"]); ?></td>
                            <td width="80" align="right"><? echo number_format($value["fQty"]); ?></td>
                            <? $finish_gd_status = ($value["fQty"]/$value["poQty"])*100; ?>
                            <td width="80" align="right"><? echo number_format($finish_gd_status,2)." %"; ?></td>
                            <td width="80" align="right"><? echo number_format($value["ex"]); ?></td>
                            <? $ex_gd_status = ($value["ex"]/$value["poQty"])*100; ?>
                            <td width="" align="right"><? echo  number_format($ex_gd_status,2)." %"; ?></td>
                        </tr>	
                        <?		
                            $total_po_quantity+=$value["poQty"];
                            $total_po_value+=$value["poVal"];
                            $total_cut+=$value["cQty"];
                            $total_print_re+=$value["prQty"];
                            $total_sew_input+=$value["sQty"];
                            $total_sew_out+=$value["soQty"];
                            $total_iron+=$value["iQty"];
                            $total_re_iron+=$value["reiQty"];
                            $total_finish+=$value["fQty"];
                            $total_ex_factory+=$value["ex"];
                           
                        $i++;
                    }//end foreach 1st
                    
                    $chart_data_qnty="Order Qty;".$total_po_quantity."\n"."Cutting;".$total_cut."\n"."Sew In;".$total_sew_input."\n"."Sew Out ;".$total_sew_out."\n"."Iron ;".$total_iron."\n"."Finish ;".$total_finish."\n"."Ex-Fact;".$total_ex_factory."\n";
                ?>
                </table>
                <table border="1" class="tbl_bottom"  width="1180" rules="all" id="" >
                    <tr> 
                        <td width="30">&nbsp;  </td> 
                        <td width="80" align="right">Total</td> 
                        <td width="80" id="tot_po_quantity"><? echo number_format($total_po_quantity); ?></td> 
                        <td width="80" id="tot_po_value"><? echo number_format($total_po_value,2); ?></td> 
                        <td width="80" id="tot_cutting"><? echo number_format($total_cut); ?></td>
                        <td width="80" id="tot_cutting"><? echo number_format($total_po_quantity-$total_cut); ?></td>
                        <td width="80" id="tot_emb_rcv"><? echo number_format($total_print_re); ?></td> 
                        <td width="80" id="tot_sew_in"><? echo number_format($total_sew_input); ?></td> 
                        <td width="80" id="tot_sew_out"><? echo number_format($total_sew_out); ?></td>   
                        <td width="80" id="tot_iron"><? echo number_format($total_iron); ?></td> 
                        <td width="80" id="tot_re_iron"><? echo number_format($total_re_iron); ?></td> 
                        <td width="80" id="tot_finish"><? echo number_format($total_finish); ?></td>
                        <? $total_finish_gd_status = ($total_finish/$total_po_quantity)*100; ?>
                        <td width="80"><? echo number_format($total_finish_gd_status,2); ?></td >
                        <td width="80"><? echo number_format($total_ex_factory); ?></td >
                        <? $total_ex_status = ($total_ex_factory/$total_po_quantity)*100; ?>
                        <td width=""><? echo number_format($total_ex_status,2); ?></td>
                    </tr>
                 </table>
                 <input type="hidden" id="graph_data" value="<? echo substr($chart_data_qnty,0,-1); ?>"/>
            </div>
        </div>
        <div style="float:left; width:600px">   
            <table>
                <tr>
                    <td height="21" width="600"><div id="chartdiv"> </div></td>
                </tr>    
            </table>
        </div> 
        <div style="clear:both"></div>
        <table width="2780" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1">
            <thead>
                <tr>
                    <th width="30">SL</th>    
                    <th width="130">Order Number</th>
                    <th width="60">Buyer Name</th>
                    <th width="50">Job Year</th>
                    <th width="80">Job Number</th>
                    <th width="120">Style Name</th>
                    <th width="80">Team Name</th>
                    <th width="90">Team Member</th>
                    <th width="130">Item Name</th>
                    <th width="80">Country</th>
                    <th width="80">Order Qty.</th>
                    <th width="80">Ship Date</th>
                    <th width="80">Location</th>
					<th width="80">Floor</th>
                    <th width="80">Ex-Factory Date</th>
                    <th width="80">Days in Hand</th>
                    <th width="80">Stan. Exc. Cut %</th>
                    <th width="80">Total Cut Qty</th>
                    <th width="80">Cutting Balance</th>
                    <th width="80">Actual Exc. Cut %</th>
                    <th width="80">Total Emb. Issue Qty</th>
                    <th width="80">Total Emb. Rcv. Qty</th>
                    <th width="80">Total Sew Input Qty</th>
                    <th width="80">Total Sew Output Qty</th>
                    <th width="80">Total Iron Qty</th>
                    <th width="80">Total Re-Iron Qty</th>
                    <th width="80">Total Finish Qty</th>
                    <th width="80">Fin Goods Status</th>
                    <th width="80">Reject Qty</th>
                    <th width="80">Total Ship Out</th>
                    <th width="80">Shortage/ Excess</th>
                    <th width="85">Shipping Status</th>
                    <th>Remarks</th>
                 </tr>
            </thead>
        </table>
        <div style="max-height:425px; overflow-y:scroll; width:2800px" id="scroll_body">
            <table border="1" class="rpt_table" width="2780" rules="all" id="table_body">
				<? echo $html; ?>  
            </table>	
            <table border="1" class="tbl_bottom" width="2780" rules="all" id="report_table_footer_1" >
                <tr>
                    <td width="30"></td>
                    <td width="130"></td>
                    <td width="60"></td>
                    <td width="50"></td>
                    <td width="80"></td>
                    <td width="120"></td>
                    <td width="80"></td>
                    <td width="90"></td>
                    <td width="130">Total</td>
                    <td width="80"></td>
                    <td width="80" id="total_order_quantity"></td>
                    <td width="80"></td>
                    <td width="80"></td>
                    <td width="80"></td>
                    <td width="80"></td>
                    <td width="80"></td>
                    <td width="80"></td>
                    <td width="80" id="total_cutting"></td>
                    <td width="80" id="total_cutting_bal"></td>
                    <td width="80"></td>
                    <td width="80" id="total_emb_issue"></td>
                    <td width="80" id="total_emb_receive"></td>
                    <td width="80" id="total_sewing_input"></td>
                    <td width="80" id="total_sewing_out"></td>
                    <td width="80" id="total_iron_qnty"></td>
                    <td width="80" id="total_re_iron_qnty"></td>
                    <td width="80" id="total_finish_qnty"></td>
                    <td width="80"></td>
                    <td width="80" align="right" id="total_rej_value_td"></td>
                    <td width="80" id="total_out"></td>
                    <td width="80" id="total_shortage"></td>
                    <td width="85" id="ship_status"></td>
                    <td></td>
                 </tr>
			</table>
        </div>
	</div>   
	<?
	}

	$html = ob_get_contents();
	ob_clean();
	$new_link=create_delete_report_file( $html, 1, 1, "../../../" );
	
	echo "$html";
	exit();	
}
//-------------------------------------------END Show Date Wise------------------------
//-------------------------------------------END Show Date Location Floor & Line Wise------------------------
//-------------------------------------------end-----------------------------------------------------------------------------//
		
if($action=='date_wise_production_report') 
{	
	extract($_REQUEST); 
 	echo load_html_head_contents("Remarks", "../../../", 1, 1,$unicode,'',''); 
	?>
    <div align="center">
        <fieldset style="width:480px">
       
        
        <legend>Cutting</legend>
            <? 
			  	$tot_cutting_qty="";
                if($db_type==0)
				{
                 $sql= "SELECT production_date,sum(production_quantity) as production_quantity,group_concat(remarks) as remarks from pro_garments_production_mst where po_break_down_id in ($po_break_down_id) and item_number_id=$item_id and production_type='1' and is_deleted=0 and status_active=1 group by production_date";
				}
				else
				{
				$sql= "SELECT production_date,sum(production_quantity) as production_quantity,listagg((cast(remarks as varchar2(4000))),',') within group (order by remarks) as remarks from pro_garments_production_mst where po_break_down_id in ($po_break_down_id) and item_number_id=$item_id and production_type='1' and is_deleted=0 and status_active=1 group by production_date";	
				}
				$total_cutting_qty=sql_select($sql);
				foreach( $total_cutting_qty as $cuttingQty)
				{
					$tot_cutting_qty+=$cuttingQty[csf("production_quantity")];
				}

                 echo  create_list_view ( "list_view_1", "Date,Production Qnty,Remarks", "100,120,280","470","220",1, $sql, "", "","", 1, '0,0,0', $arr, "production_date,production_quantity,remarks", "../requires/order_wise_production_report_controller", '','3,1,0');
                
            ?>
             <table>
             	<tfoot>
                <tr>
                	<td width="80"></td>
                	<td width="100" align="right">Total = </td>
                    <td width="150" align="right"> <? echo $tot_cutting_qty; ?></td>
                    <td width="250"></td>
                 </tr>
                </tfoot>
             </table>
        </fieldset>
        
        <fieldset style="width:480px">
        <legend>Print/Embr Issue</legend>
            <? 
                $total_printEmbrIssue_qty=""; 
                  //$sql= "SELECT production_date,production_quantity,remarks from pro_garments_production_mst where po_break_down_id in ($po_break_down_id) and item_number_id=$item_id and production_type='2' and is_deleted=0 and status_active=1";
               if($db_type==0)
				{
                 $sql= "SELECT production_date,sum(production_quantity) as production_quantity,group_concat(remarks) as remarks from pro_garments_production_mst where po_break_down_id in ($po_break_down_id) and item_number_id=$item_id and production_type='2' and is_deleted=0 and status_active=1 group by production_date";
				}
				else
				{
				$sql= "SELECT production_date,sum(production_quantity) as production_quantity,listagg((cast(remarks as varchar2(4000))),',') within group (order by remarks) as remarks from pro_garments_production_mst where po_break_down_id in ($po_break_down_id) and item_number_id=$item_id and production_type='2' and is_deleted=0 and status_active=1 group by production_date";	
				}
				$total_printEmbrIssue_arr=sql_select($sql);
				foreach( $total_printEmbrIssue_arr as $printEmbrIssueQty)
				{
					$total_printEmbrIssue_qty+=$printEmbrIssueQty[csf("production_quantity")];
				}
				  
                 echo  create_list_view ( "list_view_2", "Date,Production Qnty,Remarks", "100,120,280","470","220",1, $sql, "", "","", 1, '0,0,0', $arr, "production_date,production_quantity,remarks", "../requires/order_wise_production_report_controller", '','3,1,0');
            ?>
            
             <table>
             	<tfoot>
                <tr>
                	<td width="80"></td>
                	<td width="100" align="right">Total = </td>
                    <td width="150" align="right"> <? echo $total_printEmbrIssue_qty; ?></td>
                    <td width="250"></td>
                 </tr>
                </tfoot>
             </table>
        </fieldset>
        
        <fieldset style="width:480px">
        <legend>Print/Embr Receive</legend>
            <? 
                $total_printEmbrRec_qty="";   
                 // $sql= "SELECT production_date,production_quantity,remarks from pro_garments_production_mst where po_break_down_id in ($po_break_down_id) and item_number_id=$item_id and production_type='3' and is_deleted=0 and status_active=1";
				  
				if($db_type==0)
				{
                 $sql= "SELECT production_date,sum(production_quantity) as production_quantity,group_concat(remarks) as remarks from pro_garments_production_mst where po_break_down_id in ($po_break_down_id) and item_number_id=$item_id and production_type='3' and is_deleted=0 and status_active=1 group by production_date";
				}
				else
				{
				$sql= "SELECT production_date,sum(production_quantity) as production_quantity,listagg((cast(remarks as varchar2(4000))),',') within group (order by remarks) as remarks from pro_garments_production_mst where po_break_down_id in ($po_break_down_id) and item_number_id=$item_id and production_type='3' and is_deleted=0 and status_active=1 group by production_date";	
				}
				$total_printEmbrRec_arr=sql_select($sql);
				foreach( $total_printEmbrRec_arr as $printEmbrRecQty)
				{
					$total_printEmbrRec_qty+=$printEmbrRecQty[csf("production_quantity")];
				}
                  
                 echo  create_list_view ( "list_view_3", "Date,Production Qnty,Remarks", "100,120,280","470","220",1, $sql, "", "","", 1, '0,0,0', $arr, "production_date,production_quantity,remarks", "../requires/order_wise_production_report_controller", '','3,1,0');
            ?>
             <table>
             	<tfoot>
                <tr>
                	<td width="80"></td>
                	<td width="100" align="right">Total = </td>
                    <td width="150" align="right"> <? echo $total_printEmbrRec_qty; ?></td>
                    <td width="250"></td>
                 </tr>
                </tfoot>
             </table>
        </fieldset>
        
        
        <fieldset style="width:480px">
        <legend>Sewing Input</legend>
            <? 
                 $total_sewing_qty="";
                 // $sql= "SELECT production_date,production_quantity,remarks from pro_garments_production_mst where po_break_down_id in ($po_break_down_id) and item_number_id=$item_id and production_type='4' and is_deleted=0 and status_active=1";
				 if($db_type==0)
				{
                 $sql= "SELECT production_date,sum(production_quantity) as production_quantity,group_concat(remarks) as remarks from pro_garments_production_mst where po_break_down_id in ($po_break_down_id) and item_number_id=$item_id and production_type='4' and is_deleted=0 and status_active=1 group by production_date";
				}
				else
				{
				$sql= "SELECT production_date,sum(production_quantity) as production_quantity,listagg((cast(remarks as varchar2(4000))),',') within group (order by remarks) as remarks from pro_garments_production_mst where po_break_down_id in ($po_break_down_id) and item_number_id=$item_id and production_type='4' and is_deleted=0 and status_active=1 group by production_date";	
				}
                $total_sewing_arr=sql_select($sql);
				foreach( $total_sewing_arr as $printSewingQty)
				{
					$total_sewing_qty+=$printSewingQty[csf("production_quantity")];
				}
                 echo  create_list_view ( "list_view_4", "Date,Production Qnty,Remarks", "100,120,280","470","220",1, $sql, "", "","", 1, '0,0,0', $arr, "production_date,production_quantity,remarks", "../requires/order_wise_production_report_controller", '','3,1,0');
            ?>
             <table>
             	<tfoot>
                <tr>
                	<td width="80"></td>
                	<td width="100" align="right">Total = </td>
                    <td width="150" align="right"> <? echo $total_sewing_qty; ?></td>
                    <td width="250"></td>
                 </tr>
                </tfoot>
             </table>
        </fieldset>
        
        
        <fieldset>
        <legend style="width:480px">Sewing Output</legend>
            <? 
                 $total_sewingOut_qty="";
                 // $sql= "SELECT production_date,production_quantity,remarks from pro_garments_production_mst where po_break_down_id in ($po_break_down_id) and item_number_id=$item_id and production_type='5' and is_deleted=0 and status_active=1";
				  	if($db_type==0)
				{
                 $sql= "SELECT production_date,sum(production_quantity) as production_quantity,group_concat(remarks) as remarks from pro_garments_production_mst where po_break_down_id in ($po_break_down_id) and item_number_id=$item_id and production_type='5' and is_deleted=0 and status_active=1 group by production_date";
				}
				else
				{
				$sql= "SELECT production_date,sum(production_quantity) as production_quantity,listagg((cast(remarks as varchar2(4000))),',') within group (order by remarks) as remarks from pro_garments_production_mst where po_break_down_id in ($po_break_down_id) and item_number_id=$item_id and production_type='5' and is_deleted=0 and status_active=1 group by production_date";	
				}
				 $total_sewingOut_arr=sql_select($sql);
				foreach( $total_sewingOut_arr as $printSewingOutQty)
				{
					$total_sewingOut_qty+=$printSewingOutQty[csf("production_quantity")];
				}
                  
                 echo  create_list_view ( "list_view_5", "Date,Production Qnty,Remarks", "100,120,280","470","220",1, $sql, "", "","", 1, '0,0,0', $arr, "production_date,production_quantity,remarks", "../requires/order_wise_production_report_controller", '','3,1,0');
            ?>
             <table>
             	<tfoot>
                <tr>
                	<td width="80"></td>
                	<td width="100" align="right">Total = </td>
                    <td width="150" align="right"> <? echo $total_sewingOut_qty; ?></td>
                    <td width="250"></td>
                 </tr>
                </tfoot>
             </table>
        </fieldset>
        
        
        <fieldset style="width:480px">
        <legend>Finish Input</legend>
            <? 
                 $total_finishIn_qty="";
                 // $sql= "SELECT production_date,production_quantity,remarks from pro_garments_production_mst where po_break_down_id in ($po_break_down_id) and item_number_id=$item_id and production_type='6' and is_deleted=0 and status_active=1";
				if($db_type==0)
				{
                 $sql= "SELECT production_date,sum(production_quantity) as production_quantity,group_concat(remarks) as remarks from pro_garments_production_mst where po_break_down_id in ($po_break_down_id) and item_number_id=$item_id and production_type='6' and is_deleted=0 and status_active=1 group by production_date";
				}
				else
				{
				$sql= "SELECT production_date,sum(production_quantity) as production_quantity,listagg((cast(remarks as varchar2(4000))),',') within group (order by remarks) as remarks from pro_garments_production_mst where po_break_down_id in ($po_break_down_id) and item_number_id=$item_id and production_type='6' and is_deleted=0 and status_active=1 group by production_date";	
				}
				$total_finishIn_arr=sql_select($sql);
				foreach( $total_finishIn_arr as $printFinishInQty)
				{
					$total_finishIn_qty+=$printFinishInQty[csf("production_quantity")];
				}
                  
                 echo  create_list_view ( "list_view_6", "Date,Production Qnty,Remarks", "100,120,280","470","220",1, $sql, "", "","", 1, '0,0,0', $arr, "production_date,production_quantity,remarks", "../requires/order_wise_production_report_controller", '','3,1,0');
            ?>
             <table>
             	<tfoot>
                <tr>
                	<td width="80"></td>
                	<td width="100" align="right">Total = </td>
                    <td width="150" align="right"> <? echo $total_finishIn_qty; ?></td>
                    <td width="250"></td>
                 </tr>
                </tfoot>
             </table>
        </fieldset>
        
        <fieldset style="width:480px">
        <legend>Finish Output</legend>
            <? 
                 $total_finishOut_qty="";
                 // $sql= "SELECT production_date,production_quantity,remarks from pro_garments_production_mst where po_break_down_id in ($po_break_down_id) and item_number_id=$item_id and production_type='8' and is_deleted=0 and status_active=1";
				if($db_type==0)
				{
                 $sql= "SELECT production_date,sum(production_quantity) as production_quantity,group_concat(remarks) as remarks from pro_garments_production_mst where po_break_down_id in ($po_break_down_id) and item_number_id=$item_id and production_type='8' and is_deleted=0 and status_active=1 group by production_date";
				}
				else
				{
				$sql= "SELECT production_date,sum(production_quantity) as production_quantity,listagg((cast(remarks as varchar2(4000))),',') within group (order by remarks) as remarks from pro_garments_production_mst where po_break_down_id in ($po_break_down_id) and item_number_id=$item_id and production_type='8' and is_deleted=0 and status_active=1 group by production_date";	
				}
				$total_finishOut_arr=sql_select($sql);
				foreach( $total_finishOut_arr as $printFinishOutQty)
				{
					$total_finishOut_qty+=$printFinishOutQty[csf("production_quantity")];
				}
                 
                  echo  create_list_view ( "list_view_7", "Date,Production Qnty,Remarks", "100,120,280","470","220",1, $sql, "", "","", 1, '0,0,0', $arr, "production_date,production_quantity,remarks", "../requires/order_wise_production_report_controller", '','3,1,0');
            ?>
             <table>
             	<tfoot>
                <tr>
                	<td width="80"></td>
                	<td width="100" align="right">Total = </td>
                    <td width="150" align="right"> <? echo $total_finishOut_qty; ?></td>
                    <td width="250"></td>
                 </tr>
                </tfoot>
             </table>
        </fieldset>
	</div>  
<?
exit();
}//end if 

if ($action=='country_ship_po_qty')
{
	echo load_html_head_contents("Ship Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
	$po_break_down_id=$_REQUEST['po_break_down_id'];
	$item_id=$_REQUEST['item_id'];
	$country_id=$_REQUEST['country_id'];
	
?>

	
 <div id="data_panel" align="center" style="width:100%">
         <script>
		 	function new_window()
			 {
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write(document.getElementById('details_reports').innerHTML);
				d.close();
			 }
         </script>
 	<input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
 </div>
  
<div style="width:700px" align="center" id="details_reports"> 
  	<legend>Color And Size Wise Summary</legend>
    <table id="tbl_id" class="rpt_table" width="680" border="1" rules="all" >
    	<thead>
        	<tr>
            	<th width="100">Buyer</th>
                <th width="100">Job Number</th>
                <th width="100">Style Name</th>
                <th width="100">Order Number</th>
                <th width="100">Ship Date</th>
                <th width="100">Item Name</th>
                <th width="">Order Qnty.</th>
               
            </tr>
        </thead>
       	<?
		    $item_id=implode(",",explode("*",$item_id));
        	$buyer_short_library=return_library_array( "select id,short_name from lib_buyer", "id", "short_name"  );
			$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name"  );

 			$sql = sql_select("select a.job_no_mst, a.po_number,c.country_ship_date,sum(c.order_quantity) AS po_quantity,a.packing,b.company_name, b.order_uom, b.buyer_name, b.style_ref_no from wo_po_details_master b,wo_po_break_down a,wo_po_color_size_breakdown c where a.job_no_mst=b.job_no and c.po_break_down_id=a.id and a.job_no_mst=c.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.country_ship_date='$country_id' and c.po_break_down_id in ($po_break_down_id) and c.item_number_id in ($item_id)   group by a.job_no_mst, a.po_number,c.country_ship_date,a.packing,b.company_name, b.order_uom, b.buyer_name, b.style_ref_no");
			//echo $sql;
			foreach($sql as $resultRow);
			
			$cons_embr=return_field_value("sum(cons_dzn_gmts) as cons_dzn_gmts","wo_pre_cost_embe_cost_dtls","job_no='".$resultRow[csf("job_no_mst")]."' and status_active=1 and is_deleted=0","cons_dzn_gmts");
			
			$po_qnty=return_field_value("sum(order_quantity) as qnty","wo_po_color_size_breakdown","po_break_down_id in ($po_break_down_id) and item_number_id='$item_id' and country_id='$country_id' and status_active=1 and is_deleted=0","qnty");	
 		?> 
        <tr>
        	<td><?    echo $buyer_short_library[$resultRow[csf("buyer_name")]]; ?></td>
            <td><p><? echo $resultRow[csf("job_no_mst")]; ?></p></td>
            <td><p><? echo $resultRow[csf("style_ref_no")]; ?></p></td>
            <td><?    echo $resultRow[csf("po_number")]; ?></td>
            <td><?    echo change_date_format($resultRow[csf("country_ship_date")]); ?></td>
             <td><p><? 
			   
			 foreach(explode(",",$item_id) as $i_id)
						{
							$item_name[]=$garments_item[$i_id];
						}
			 echo implode(",",$item_name); ?></p></td>
            <td align="center"><?   echo $resultRow[csf("po_quantity")]; ?></td>
           
        </tr>
         <?
         $prod_sewing_sql=sql_select("SELECT sum(alter_qnty) as alter_qnty, sum(reject_qnty) as reject_qnty from pro_garments_production_mst where production_type=5 and po_break_down_id in ($po_break_down_id) and is_deleted=0 and status_active=1");
		 foreach($prod_sewing_sql as $sewingRow);
		?> 	
        <tr>
        	<td colspan="2">Total Alter Sewing Qnty : <b><? echo $sewingRow[csf("alter_qnty")]; ?></b></td>
        	<td colspan="2">Total Reject Sewing Qnty : <b><? echo $sewingRow[csf("reject_qnty")]; ?></b></td>
            <td colspan="2">Pack Assortment: <b><? echo $packing[$resultRow[csf("packing")]]; ?></b></td>
        </tr>
    </table>
    <?
				  
	  $size_Arr_library=return_library_array( "select id,size_name from lib_size", "id", "size_name" );
	  $color_Arr_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );	
	  
	  $color_library=sql_select("select distinct(color_number_id) from wo_po_color_size_breakdown where po_break_down_id in ($po_break_down_id) and color_mst_id!=0 and status_active=1 and country_ship_date='$country_id' and item_number_id in ($item_id)");
	  $size_library=sql_select("select distinct(size_number_id) from wo_po_color_size_breakdown where po_break_down_id in ($po_break_down_id) and size_mst_id!=0 and status_active=1 and country_ship_date='$country_id' and item_number_id in ($item_id)");
	
	  $count = count($size_library);	
	  $width= $count*70+350; 		
	?>
    <table id="tblDtls_id" class="rpt_table" width="<? echo $width; ?>" border="1" rules="all" >
	 	<thead>
        	<tr>
            	<th width="100">Color Name</th>
                <th width="200">Production Type</th>
 				<?
				foreach($size_library as $sizeRes)
				{
				 	?><th width="80"><? echo $size_Arr_library[$sizeRes[csf("size_number_id")]]; ?></th><?
				}
				?>
     		    <th width="60">Total</th>
           </tr>
        </thead>
        <?
		  
		  foreach($color_library as $colorRes)
		  {
			?>	  
			<tr>
				<td rowspan="10"><? echo $color_Arr_library[$colorRes[csf("color_number_id")]]; ?></td>
			
 			<?
            	  $i=0;$j=0;$sqlPart="";
				  foreach($size_library as $sizeRes)
				  {
					  $i++;$j++;
					  if($i>1) $sqlPart .=",";
					  $sqlPart .= 'SUM( CASE WHEN color_number_id='.$colorRes[csf("color_number_id")].' and size_number_id='.$sizeRes[csf("size_number_id")].' THEN order_quantity ELSE 0 END ) as '."col".$i;
					  $sqlPart .= ',SUM( CASE WHEN color_number_id='.$colorRes[csf("color_number_id")].' and size_number_id='.$sizeRes[csf("size_number_id")].' THEN plan_cut_qnty ELSE 0 END ) as '."pcut".$i;
					  $sqlPart .= ',SUM( CASE WHEN color_number_id='.$colorRes[csf("color_number_id")].' and size_number_id='.$sizeRes[csf("size_number_id")].' THEN excess_cut_perc ELSE 0 END ) as '."excess_cut".$i;
				  }
				  if($j>1)
				  {
					 $sqlPart .=',SUM( CASE WHEN color_number_id='.$colorRes[csf("color_number_id")].' THEN order_quantity ELSE 0 END ) as totalorderqnty';
					 $sqlPart .=',SUM( CASE WHEN color_number_id='.$colorRes[csf("color_number_id")].' THEN plan_cut_qnty ELSE 0 END ) as totalplancutqnty';
				  }
/*		  echo "select avg(excess_cut_perc) as avg_excess_cut_perc,". $sqlPart ." from wo_po_color_size_breakdown where status_active=1 and po_break_down_id in ($po_break_down_id) and item_number_id in($item_id) and country_ship_date='$country_id'";die;*/
				$sql = sql_select("select avg(excess_cut_perc) as avg_excess_cut_perc,". $sqlPart ." from wo_po_color_size_breakdown where status_active=1 and po_break_down_id in ($po_break_down_id) and item_number_id in($item_id) and country_ship_date='$country_id'");
				//echo $sql;die;
				foreach($sql as $resRow); 
 					$bgcolor1="#E9F3FF"; 
					$bgcolor2="#FFFFFF";
				?>
					 
					<tr bgcolor="<? echo $bgcolor1; ?>">
						<td><b>Order Quantity</b></td>	
                        <? for($k=1;$k<=$i;$k++) {	$col = 'col'.$k; ?>	
                         	<td><? echo $resRow[csf($col)]; ?></td>
						<? } ?>
                         <td><? echo $resRow[csf("totalorderqnty")]; ?></td> 
					</tr>
                    <tr bgcolor="<? echo $bgcolor2; ?>">
                    	<td><b>Plan To Cut (AVG <? echo $resRow[csf("avg_excess_cut_perc")]; ?>)% </b></td>	
                        <? for($k=1;$k<=$i;$k++){ $col = 'pcut'.$k;$excess_cut = 'excess_cut'.$k;	?>	
                         	<td title="Excess Cut <? echo $resRow[csf($excess_cut)]; ?>%"><? echo $resRow[csf($col)]; ?></td>
						<? } ?>
                         <td><? echo $resRow[csf("totalplancutqnty")]; ?></td> 
                    </tr>
					
                <?
 				$total_cutting=0;$total_emb_issue=0;$total_emb_rcv=0;$total_sew_in=0;$total_sew_out=0;$total_fin_in=0;$total_fin_out=0;$total_iron_out=0;
				$cutting_html='';$embiss_html='';$embrcv_html='';$sewin_html='';$sewout_html='';$finisin_html='';$finisout_html=''; $iron_html='';
				$k=0;
				foreach($size_library as $sizeRes)
				{
					$k++;
					if($db_type==0)
					{
						$prod_sql= sql_select("SELECT  
								IFNULL(sum(CASE WHEN c.production_type ='1' THEN  c.production_qnty  ELSE 0 END),0) AS cutting_qnty,
								IFNULL(sum(CASE WHEN c.production_type ='2' THEN  c.production_qnty  ELSE 0 END),0) AS printing_qnty,
								IFNULL(sum(CASE WHEN c.production_type ='3' THEN  c.production_qnty  ELSE 0 END),0) AS printreceived_qnty, 
								IFNULL(sum(CASE WHEN c.production_type ='4' THEN  c.production_qnty  ELSE 0 END),0) AS sewingin_qnty,
								IFNULL(sum(CASE WHEN c.production_type ='5' THEN  c.production_qnty  ELSE 0 END),0) AS sewingout_qnty,
								IFNULL(sum(CASE WHEN c.production_type ='6' THEN  c.production_qnty  ELSE 0 END),0) AS finishin_qnty, 
								IFNULL(sum(CASE WHEN c.production_type ='7' THEN  c.production_qnty  ELSE 0 END),0) AS iron_qnty,
								
								IFNULL(sum(CASE WHEN c.production_type ='8' THEN  c.production_qnty  ELSE 0 END),0) AS finish_qnty 
							from 
								pro_garments_production_dtls c,wo_po_color_size_breakdown d
							where  
							    d.po_break_down_id in (".$po_break_down_id.") and d.item_number_id in ($item_id) and d.country_ship_date='$country_id' and d.color_number_id=".$colorRes[csf("color_number_id")]." and d.size_number_id=".$sizeRes[csf("size_number_id")]." and c.color_size_break_down_id=d.id and c.status_active=1   ");
					}
					else
					{
						$prod_sql= sql_select("SELECT  
							NVL(sum(CASE WHEN c.production_type ='1' THEN  c.production_qnty  ELSE 0 END),0) AS cutting_qnty,
							NVL(sum(CASE WHEN c.production_type ='2' THEN  c.production_qnty  ELSE 0 END),0) AS printing_qnty,
							NVL(sum(CASE WHEN c.production_type ='3' THEN  c.production_qnty  ELSE 0 END),0) AS printreceived_qnty, 
							NVL(sum(CASE WHEN c.production_type ='4' THEN  c.production_qnty  ELSE 0 END),0) AS sewingin_qnty,
							NVL(sum(CASE WHEN c.production_type ='5' THEN  c.production_qnty  ELSE 0 END),0) AS sewingout_qnty,
							NVL(sum(CASE WHEN c.production_type ='6' THEN  c.production_qnty  ELSE 0 END),0) AS finishin_qnty, 
							NVL(sum(CASE WHEN c.production_type ='7' THEN  c.production_qnty  ELSE 0 END),0) AS iron_qnty,
							NVL(sum(CASE WHEN c.production_type ='8' THEN  c.production_qnty  ELSE 0 END),0) AS finish_qnty 
						from 
							pro_garments_production_dtls c,wo_po_color_size_breakdown d
						where  
							d.po_break_down_id in (".$po_break_down_id.") and d.item_number_id in ($item_id) and d.country_ship_date='$country_id' and d.color_number_id=".$colorRes[csf("color_number_id")]." and d.size_number_id=".$sizeRes[csf("size_number_id")]." and c.color_size_break_down_id=d.id and c.status_active=1");	
					}
					
					foreach($prod_sql as $prodRow);  
					$col = 'col'.$k;
                    if($prodRow[csf("cutting_qnty")]==0)$bgCol="bgcolor='#FF0000'"; 
					else if($prodRow[csf("cutting_qnty")] < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
					else if($prodRow[csf("cutting_qnty")] >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'"; 
					$cutting_html .='<td '.$bgCol.'>'.$prodRow[csf("cutting_qnty")].'</td>';
                    $total_cutting+=$prodRow[csf("cutting_qnty")];
                 	
					if($cons_embr>0)
					{
						if($prodRow[csf("printing_qnty")]==0)$bgCol="bgcolor='#FF0000'"; 
						else if($prodRow[csf("printing_qnty")] < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
						else if($prodRow[csf("printing_qnty")] >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
					}
					else $bgCol='';
                    $embiss_html .='<td '.$bgCol.'>'.$prodRow[csf("printing_qnty")].'</td>';
                    $total_emb_issue+=$prodRow[csf("printing_qnty")];
                    
					if($cons_embr>0)
					{
						if($prodRow[csf("printreceived_qnty")]==0)$bgCol="bgcolor='#FF0000'"; 
						else if($prodRow[csf("printreceived_qnty")] < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
						else if($prodRow[csf("printreceived_qnty")] >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
					}
					else $bgCol='';
					
                    $embrcv_html .='<td '.$bgCol.'>'.$prodRow[csf("printreceived_qnty")].'</td>';
                    $total_emb_rcv+=$prodRow[csf("printreceived_qnty")];
                    
					if($prodRow[csf("sewingin_qnty")]==0)$bgCol="bgcolor='#FF0000'"; 
					else if($prodRow[csf("sewingin_qnty")] < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
					else if($prodRow[csf("sewingin_qnty")] >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'"; 
                    $sewin_html .='<td '.$bgCol.'>'.$prodRow[csf("sewingin_qnty")].'</td>';
                    $total_sew_in+=$prodRow[csf("sewingin_qnty")];
                    
					if($prodRow[csf("sewingout_qnty")]==0)$bgCol="bgcolor='#FF0000'"; 
					else if($prodRow[csf("sewingout_qnty")] < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
					else if($prodRow[csf("sewingout_qnty")] >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";  
                    $sewout_html .='<td '.$bgCol.'>'.$prodRow[csf("sewingout_qnty")].'</td>';
                    $total_sew_out+=$prodRow[csf("sewingout_qnty")];
                    
					if($prodRow[csf("iron_qnty")]==0)$bgCol="bgcolor='#FF0000'"; 
					else if($prodRow[csf("iron_qnty")] < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
					else if($prodRow[csf("iron_qnty")] >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'"; 
                    $iron_html .='<td '.$bgCol.'>'.$prodRow[csf("iron_qnty")].'</td>';
                    $total_iron_out+=$prodRow[csf("iron_qnty")];
					
				
					if($prodRow[csf("finish_qnty")]==0)$bgCol="bgcolor='#FF0000'"; 
					else if($prodRow[csf("finish_qnty")] < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
					else if($prodRow[csf("finish_qnty")] >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'"; 
                    $finisout_html .='<td '.$bgCol.'>'.$prodRow[csf("finish_qnty")].'</td>';
                    $total_fin_out+=$prodRow[csf("finish_qnty")];
					
					
                    
				}// end size foreach loop	
				
				?>
					<tr bgcolor="<? echo $bgcolor1; ?>">
                    	<td><b>Cutting</b></td>
                        <? echo $cutting_html; ?> 
                        <td><? echo $total_cutting; ?></td> 
                    </tr>
                    <tr bgcolor="<? echo $bgcolor2; ?>">
                    	<td><b>Print/Embro Issue</b></td>
                        <? echo $embiss_html; ?> 
                        <td><? echo $total_emb_issue; ?></td> 
                    </tr>
                    <tr bgcolor="<? echo $bgcolor1; ?>">
                    	<td><b>Print/Embro Received</b></td>
                        <? echo $embrcv_html; ?> 
                        <td><? echo $total_emb_rcv; ?></td> 
                    </tr>
                    <tr bgcolor="<? echo $bgcolor2; ?>">
                    	<td><b>Sewing Input</b></td>
                       <? echo $sewin_html; ?> 
                        <td><? echo $total_sew_in; ?></td> 
                    </tr>
                    <tr bgcolor="<? echo $bgcolor1; ?>">
                    	<td><b>Sewing Output</b></td>
                        <? echo $sewout_html; ?> 
                        <td><? echo $total_sew_out; ?></td> 
                    </tr>
                    <tr bgcolor="<? echo $bgcolor2; ?>">
                    	<td><b>Iron Output</b></td>
                        <? echo $iron_html; ?> 
                        <td><? echo  $total_iron_out; ?></td> 
                    </tr>
                    <tr bgcolor="<? echo $bgcolor1; ?>">
                    	<td><b>Finishing Output</b></td>
                       <? echo $finisout_html; ?> 
                        <td><? echo $total_fin_out; ?></td> 
                    </tr> 
			<?	
			}// end color foreach loop
			?>
           
		 
 </table>
</div>    


<?
exit();

}// end if condition

if($action=='date_wise_production_report_country') 
{	
	extract($_REQUEST); 
 	echo load_html_head_contents("Remarks", "../../../", 1, 1,$unicode,'','');
 ?>
 	<div align="center">
        <fieldset style="width:480px">
        <legend>Cutting</legend>
            <? 
                 
                 $sql= "SELECT id,production_date,production_quantity,remarks from pro_garments_production_mst where po_break_down_id in ($po_break_down_id) and item_number_id=$item_id  and country_id='$country_id' and production_type='1' and is_deleted=0 and status_active=1";
                 //echo $sql;
                 echo  create_list_view ( "list_view_1", "Date,Production Qnty,Remarks", "100,120,280","470","220",1, $sql, "", "","", 1, '0,0,0', $arr, "production_date,production_quantity,remarks", "../requires/order_wise_production_report_controller", '','3,1,0');
                
            ?>
        </fieldset>
        
        <fieldset style="width:480px">
        <legend>Print/Embr Issue</legend>
            <? 
                 
                  $sql= "SELECT production_date,production_quantity,remarks from pro_garments_production_mst where po_break_down_id in ($po_break_down_id) and item_number_id=$item_id and country_id='$country_id' and production_type='2' and is_deleted=0 and status_active=1";
                  
                 echo  create_list_view ( "list_view_2", "Date,Production Qnty,Remarks", "100,120,280","470","220",1, $sql, "", "","", 1, '0,0,0', $arr, "production_date,production_quantity,remarks", "../requires/order_wise_production_report_controller", '','3,1,0');
            ?>
        </fieldset>
        
        <fieldset style="width:480px">
        <legend>Print/Embr Receive</legend>
            <? 
                 
                  $sql= "SELECT production_date,production_quantity,remarks from pro_garments_production_mst where po_break_down_id in ($po_break_down_id) and item_number_id=$item_id and country_id='$country_id' and production_type='3' and is_deleted=0 and status_active=1";
                  
                 echo  create_list_view ( "list_view_3", "Date,Production Qnty,Remarks", "100,120,280","470","220",1, $sql, "", "","", 1, '0,0,0', $arr, "production_date,production_quantity,remarks", "../requires/order_wise_production_report_controller", '','3,1,0');
            ?>
        </fieldset>
        
        
        <fieldset style="width:480px">
        <legend>Sewing Input</legend>
            <? 
                 
                  $sql= "SELECT production_date,production_quantity,remarks from pro_garments_production_mst where po_break_down_id in ($po_break_down_id) and item_number_id=$item_id and country_id='$country_id' and production_type='4' and is_deleted=0 and status_active=1";
                  
                 echo  create_list_view ( "list_view_4", "Date,Production Qnty,Remarks", "100,120,280","470","220",1, $sql, "", "","", 1, '0,0,0', $arr, "production_date,production_quantity,remarks", "../requires/order_wise_production_report_controller", '','3,1,0');
            ?>
        </fieldset>
        
        
        <fieldset style="width:480px">
        <legend>Sewing Output</legend>
            <? 
                 
                  $sql= "SELECT production_date,production_quantity,remarks from pro_garments_production_mst where po_break_down_id ($po_break_down_id) and item_number_id=$item_id and country_id='$country_id' and production_type='5' and is_deleted=0 and status_active=1";
                  
                 echo  create_list_view ( "list_view_5", "Date,Production Qnty,Remarks", "100,120,280","470","220",1, $sql, "", "","", 1, '0,0,0', $arr, "production_date,production_quantity,remarks", "../requires/order_wise_production_report_controller", '','3,1,0');
            ?>
        </fieldset>
        
        
        <fieldset style="width:480px">
        <legend>Finish Input</legend>
            <? 
                 
                  $sql= "SELECT production_date,production_quantity,remarks from pro_garments_production_mst where po_break_down_id in ($po_break_down_id) and item_number_id=$item_id and country_id='$country_id' and production_type='6' and is_deleted=0 and status_active=1";
                  
                 echo  create_list_view ( "list_view_6", "Date,Production Qnty,Remarks", "100,120,280","470","220",1, $sql, "", "","", 1, '0,0,0', $arr, "production_date,production_quantity,remarks", "../requires/order_wise_production_report_controller", '','3,1,0');
            ?>
        </fieldset>
        
        <fieldset style="width:480px">
        <legend>Finish Output</legend>
            <? 
                 
                  $sql= "SELECT production_date,production_quantity,remarks from pro_garments_production_mst where po_break_down_id in ($po_break_down_id) and item_number_id=$item_id and country_id='$country_id' and production_type='8' and is_deleted=0 and status_active=1";
                 
                  echo  create_list_view ( "list_view_7", "Date,Production Qnty,Remarks", "100,120,280","470","220",1, $sql, "", "","", 1, '0,0,0', $arr, "production_date,production_quantity,remarks", "../requires/order_wise_production_report_controller", '','3,1,0');
            ?>
        </fieldset>
	</div>
<?
exit();
}//end if 
  
if ($action=='OrderPopup2')//previous
{
	echo load_html_head_contents("Order Wise Production Report", "../../../", 1, 1,$unicode,'','');
	$po_break_down_id=$_REQUEST['po_break_down_id'];
	$item_id=$_REQUEST['item_id'];
	$company_name=str_replace("'","",$_REQUEST['company_name']);
	$color_variable_setting=return_field_value("ex_factory","variable_settings_production","company_name='$company_name' and variable_list=1 and status_active=1 and is_deleted=0","ex_factory");
	$ex_fact_qty_arr=array();
	if($color_variable_setting==2 || $color_variable_setting==3)
	{
		$sql_exfect="select c.color_number_id, c.size_number_id, sum(b.production_qnty) as production_qnty from pro_ex_factory_mst a,pro_ex_factory_dtls b, wo_po_color_size_breakdown c where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id in ($po_break_down_id) and a.status_active=1 and a.is_deleted=0 group by  c.color_number_id, c.size_number_id";
		$sql_result_exfact=sql_select($sql_exfect);
		foreach($sql_result_exfact as $row)
		{
			$ex_fact_qty_arr[$row[csf("color_number_id")]][$row[csf("size_number_id")]]=$row[csf("production_qnty")];
		}
	}
	//var_dump($ex_fact_qty_arr);
	
?>
 <div id="data_panel" align="center" style="width:100%">
         <script>
		 	function new_window()
			 {
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write(document.getElementById('details_reports').innerHTML);
				d.close();
			 }
         </script>
 	<input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
 </div>
  
<div style="width:700px" align="center" id="details_reports"> 
  	<legend>Color And Size Wise Summary</legend>
    <table id="tbl_id" class="rpt_table" width="" border="1" rules="all" >
    	<thead>
        	<tr>
            	<th width="100">Buyer</th>
                <th width="100">Job Number</th>
                <th width="100">Style Name</th>
                <th width="300">Order Number</th>
                <th width="100">Ship Date</th>
                <th width="100">Item Name</th>
                <th width="100">Order Qty.</th>
            </tr>
        </thead>
       	<?
        	$buyer_short_library=return_library_array( "select id,short_name from lib_buyer", "id", "short_name"  );
			if($db_type==0)
			{
 				$sql = "select a.job_no_mst,group_concat(distinct(a.po_number)) as po_number,a.pub_shipment_date, sum(a.po_quantity) as po_quantity,a.packing,b.set_break_down, b.company_name, b.order_uom, b.buyer_name, b.style_ref_no,c.set_item_ratio 
					from wo_po_break_down a, wo_po_details_master b, wo_po_details_mas_set_details c 
					where a.job_no_mst=b.job_no and b.job_no=c.job_no and a.id in ($po_break_down_id) and c.gmts_item_id=$item_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
			}
			else
			{
				$sql = "select a.job_no_mst, LISTAGG(a.po_number, ',') WITHIN GROUP (ORDER BY a.id) as po_number,max(a.pub_shipment_date) as pub_shipment_date, sum(a.po_quantity) as po_quantity,a.packing,b.set_break_down, b.company_name, b.order_uom, b.buyer_name, b.style_ref_no,c.set_item_ratio 
					from wo_po_break_down a, wo_po_details_master b, wo_po_details_mas_set_details c 
					where a.job_no_mst=b.job_no and b.job_no=c.job_no and a.id in ($po_break_down_id) and c.gmts_item_id=$item_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.job_no_mst, a.packing,b.set_break_down, b.company_name, b.order_uom, b.buyer_name, b.style_ref_no,c.set_item_ratio";
			}
			//echo $sql;die;
			$resultRow=sql_select($sql);
				
			$cons_embr=return_field_value("sum(cons_dzn_gmts) as cons_dzn_gmts","wo_pre_cost_embe_cost_dtls","job_no='".$resultRow[0][csf("job_no_mst")]."' and status_active=1 and is_deleted=0","cons_dzn_gmts");
			
 		?> 
        <tr>
        	<td><? echo $buyer_short_library[$resultRow[0][csf("buyer_name")]]; ?></td>
            <td><p><? echo $resultRow[0][csf("job_no_mst")]; ?></p></td>
            <td><p><? echo $resultRow[0][csf("style_ref_no")]; ?></p></td>
            <td><p><? echo implode(",",array_unique(explode(",",$resultRow[0][csf("po_number")]))); ?></p></td>
            <td><? echo change_date_format($resultRow[0][csf("pub_shipment_date")]); ?></td>
            <td><? echo $garments_item[$item_id]; ?></td>
            <td><? echo $resultRow[0][csf("po_quantity")]*$resultRow[0][csf("set_item_ratio")]; ?></td>
        </tr>
         <?
         $prod_sewing_sql=sql_select("SELECT sum(alter_qnty) as alter_qnty, sum(reject_qnty) as reject_qnty from pro_garments_production_mst where production_type=5 and po_break_down_id in ($po_break_down_id) and is_deleted=0 and status_active=1");
		 foreach($prod_sewing_sql as $sewingRow);
		?> 	
        <tr>
        	<td colspan="2">Total Alter Sewing Qty : <b><? echo $sewingRow[csf("alter_qnty")]; ?></b></td>
        	<td colspan="2">Total Reject Sewing Qty : <b><? echo $sewingRow[csf("reject_qnty")]; ?></b></td>
            <td colspan="2">Pack Assortment: <b><? echo $packing[$resultRow[csf("packing")]]; ?></b></td>
        </tr>
    </table>
    <?
				  
	  $size_Arr_library=return_library_array( "select id,size_name from lib_size", "id", "size_name" );
	  $color_Arr_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );	
	  
	  $color_library=sql_select("select distinct(color_number_id) as color_number_id from wo_po_color_size_breakdown where po_break_down_id in ($po_break_down_id) and color_mst_id!=0 and status_active=1");
	  $size_library=sql_select("select distinct(size_number_id) as size_number_id from wo_po_color_size_breakdown where po_break_down_id in ($po_break_down_id) and size_number_id!=0 and status_active=1");
	  $count = count($size_library);	
	  $width= $count*70+350; 		
	?>
    <table id="tblDtls_id" class="rpt_table" width="<? echo $width; ?>" border="1" rules="all" >
	 	<thead>
        	<tr>
            	<th width="100">Color Name</th>
                <th width="170">Production Type</th>
 				<?
				foreach($size_library as $sizeRes)
				{
				 	?><th width="80"><? echo $size_Arr_library[$sizeRes[csf("size_number_id")]]; ?></th><?
				}
				?>
     		    <th width="60">Total</th>
           </tr>
        </thead>
        <?
		  
		  foreach($color_library as $colorRes)
		  {
			  if($color_variable_setting==2 || $color_variable_setting==3) $row_span=17; else $row_span=16;  
			?>	  
			<tr>
				<td rowspan="<? echo $row_span; ?>"><? echo $color_Arr_library[$colorRes[csf("color_number_id")]]; ?></td>
			
 			<?
            	  $i=0;$j=0;$sqlPart="";
				  foreach($size_library as $sizeRes)
				  {
					  $i++;$j++;
					  if($i>1) $sqlPart .=",";
					  $sqlPart .= 'SUM( CASE WHEN color_number_id='.$colorRes[csf("color_number_id")].' and size_number_id='.$sizeRes[csf("size_number_id")].' THEN order_quantity ELSE 0 END ) as '."col".$i;
					  $sqlPart .= ',SUM( CASE WHEN color_number_id='.$colorRes[csf("color_number_id")].' and size_number_id='.$sizeRes[csf("size_number_id")].' THEN plan_cut_qnty ELSE 0 END ) as '."pcut".$i;
					  $sqlPart .= ',SUM( CASE WHEN color_number_id='.$colorRes[csf("color_number_id")].' and size_number_id='.$sizeRes[csf("size_number_id")].' THEN excess_cut_perc ELSE 0 END ) as '."excess_cut".$i;
				  }
				  if($j>1)
				  {
					 $sqlPart .=',SUM( CASE WHEN color_number_id='.$colorRes[csf("color_number_id")].' THEN order_quantity ELSE 0 END ) as totalorderqnty';
					 $sqlPart .=',SUM( CASE WHEN color_number_id='.$colorRes[csf("color_number_id")].' THEN plan_cut_qnty ELSE 0 END ) as totalplancutqnty';
				  }
				$sql = sql_select("select avg(excess_cut_perc) as avg_excess_cut_perc,max(excess_cut_perc) as excess_cut_perc,". $sqlPart ." from wo_po_color_size_breakdown where status_active=1 and po_break_down_id in ($po_break_down_id) and item_number_id=$item_id");
				//echo $sql;die;
				foreach($sql as $resRow); 
 					$bgcolor1="#E9F3FF"; 
					$bgcolor2="#FFFFFF";
				?>
					</tr>
					<tr bgcolor="<? echo $bgcolor1; ?>">
						<td><b>Order Quantity</b></td>	
                        <? for($k=1;$k<=$i;$k++) {	$col = 'col'.$k; ?>	
                         	<td><? echo $resRow[csf($col)]; ?></td>
						<? } ?>
                         <td><? echo $resRow[csf("totalorderqnty")]; ?></td> 
					</tr>
                    <tr bgcolor="<? echo $bgcolor2; ?>">
                    	<td><b>Plan To Cut (AVG <? echo number_format($resRow[csf("avg_excess_cut_perc")],2); ?>)% </b></td>	
                        <? for($k=1;$k<=$i;$k++){ $col = 'pcut'.$k;$excess_cut = 'excess_cut'.$k;	?>	
                         	<td title="Excess Cut <? echo $resRow[csf($excess_cut)]; ?>%"><? echo $resRow[csf($col)]; ?></td>
						<? } ?>
                         <td><? echo $resRow[csf("totalplancutqnty")]; ?></td> 
                    </tr>
					
                <?
 				$total_cutting=0;$total_sew_in=0;$total_sew_out=0;$total_fin_in=0;$total_fin_out=0;$total_iron_out=0; $total_exfact_qnty=0;
				$total_print_issue=0;$total_print_rcv=0;$total_embro_issue=0;$total_embro_rcv=0; $total_sp_issue=0;$total_sp_rcv=0; $total_wash_issue=0;$total_wash_rcv=0;
				$cutting_html='';$sewin_html='';$sewout_html='';$finisin_html='';$finisout_html='';$iron_html=''; $exfact_html='';
				$printiss_html=''; $printrcv_html=''; $embroiss_html=''; $embrorcv_html=''; $spiss_html=''; $sprcv_html=''; $washiss_html=''; $washrcv_html='';
				$k=0;
				foreach($size_library as $sizeRes)
				{ 
					$k++;
					if($db_type==0)
					{
						$prod_sql= sql_select("SELECT  
								IFNULL(sum(CASE WHEN c.production_type ='1' THEN c.production_qnty  ELSE 0 END),0) AS cutting_qnty,
								IFNULL(sum(CASE WHEN c.production_type ='2' AND a.embel_name=1 THEN c.production_qnty ELSE 0 END),0) AS printing_qnty,
								IFNULL(sum(CASE WHEN c.production_type ='3' AND a.embel_name=1 THEN c.production_qnty ELSE 0 END),0) AS printreceived_qnty, 
								IFNULL(sum(CASE WHEN c.production_type ='2' AND a.embel_name=2 THEN c.production_qnty ELSE 0 END),0) AS emb_qnty,
								IFNULL(sum(CASE WHEN c.production_type ='3' AND a.embel_name=2 THEN c.production_qnty ELSE 0 END),0) AS embreceived_qnty, 
								IFNULL(sum(CASE WHEN c.production_type ='2' AND a.embel_name=3 THEN c.production_qnty ELSE 0 END),0) AS wash_qnty,
								IFNULL(sum(CASE WHEN c.production_type ='3' AND a.embel_name=3 THEN c.production_qnty ELSE 0 END),0) AS washreceived_qnty, 
								IFNULL(sum(CASE WHEN c.production_type ='2' AND a.embel_name=4 THEN c.production_qnty ELSE 0 END),0) AS sp_qnty,
								IFNULL(sum(CASE WHEN c.production_type ='3' AND a.embel_name=4 THEN c.production_qnty ELSE 0 END),0) AS spreceived_qnty, 
								IFNULL(sum(CASE WHEN c.production_type ='4' THEN c.production_qnty ELSE 0 END),0) AS sewingin_qnty,
								IFNULL(sum(CASE WHEN c.production_type ='5' THEN c.production_qnty ELSE 0 END),0) AS sewingout_qnty,
								IFNULL(sum(CASE WHEN c.production_type ='6' THEN c.production_qnty ELSE 0 END),0) AS finishin_qnty, 
								IFNULL(sum(CASE WHEN c.production_type ='7' THEN c.production_qnty ELSE 0 END),0) AS iron_qnty,
								IFNULL(sum(CASE WHEN c.production_type ='8' THEN c.production_qnty ELSE 0 END),0) AS finish_qnty 
							from 
								pro_garments_production_mst a, pro_garments_production_dtls c,wo_po_color_size_breakdown d
							where  
								a.id=c.mst_id and d.po_break_down_id in (".$po_break_down_id.") and d.item_number_id='$item_id' and d.color_number_id=".$colorRes[csf("color_number_id")]." and d.size_number_id=".$sizeRes[csf("size_number_id")]." and c.color_size_break_down_id=d.id and c.status_active=1 $location $floor ");
								/*IFNULL(sum(CASE WHEN c.production_type ='2' THEN c.production_qnty ELSE 0 END),0) AS printing_qnty,
								IFNULL(sum(CASE WHEN c.production_type ='3' THEN c.production_qnty ELSE 0 END),0) AS printreceived_qnty, */
					}
					else
					{
						$prod_sql=sql_select("SELECT  
								NVL(sum(CASE WHEN c.production_type ='1' THEN c.production_qnty  ELSE 0 END),0) AS cutting_qnty,
								NVL(sum(CASE WHEN c.production_type ='2' AND a.embel_name=1 THEN c.production_qnty ELSE 0 END),0) AS printing_qnty,
								NVL(sum(CASE WHEN c.production_type ='3' AND a.embel_name=1 THEN c.production_qnty ELSE 0 END),0) AS printreceived_qnty,
								NVL(sum(CASE WHEN c.production_type ='2' AND a.embel_name=2 THEN c.production_qnty ELSE 0 END),0) AS emb_qnty,
								NVL(sum(CASE WHEN c.production_type ='3' AND a.embel_name=2 THEN c.production_qnty ELSE 0 END),0) AS embreceived_qnty, 
								NVL(sum(CASE WHEN c.production_type ='2' AND a.embel_name=3 THEN c.production_qnty ELSE 0 END),0) AS wash_qnty,
								NVL(sum(CASE WHEN c.production_type ='3' AND a.embel_name=3 THEN c.production_qnty ELSE 0 END),0) AS washreceived_qnty, 
								NVL(sum(CASE WHEN c.production_type ='2' AND a.embel_name=4 THEN c.production_qnty ELSE 0 END),0) AS sp_qnty,
								NVL(sum(CASE WHEN c.production_type ='3' AND a.embel_name=4 THEN c.production_qnty ELSE 0 END),0) AS spreceived_qnty, 
								NVL(sum(CASE WHEN c.production_type ='4' THEN c.production_qnty ELSE 0 END),0) AS sewingin_qnty,
								NVL(sum(CASE WHEN c.production_type ='5' THEN c.production_qnty ELSE 0 END),0) AS sewingout_qnty,
								NVL(sum(CASE WHEN c.production_type ='6' THEN c.production_qnty ELSE 0 END),0) AS finishin_qnty, 
								NVL(sum(CASE WHEN c.production_type ='7' THEN c.production_qnty ELSE 0 END),0) AS iron_qnty,
								NVL(sum(CASE WHEN c.production_type ='8' THEN c.production_qnty ELSE 0 END),0) AS finish_qnty 
							from 
								pro_garments_production_mst a, pro_garments_production_dtls c, wo_po_color_size_breakdown d
							where  
								a.id=c.mst_id and d.po_break_down_id in (".$po_break_down_id.") and d.item_number_id='$item_id' and d.color_number_id=".$colorRes[csf("color_number_id")]." and d.size_number_id=".$sizeRes[csf("size_number_id")]." and c.color_size_break_down_id=d.id and c.status_active=1 $location $floor ");	
								/*NVL(sum(CASE WHEN c.production_type ='2' THEN  c.production_qnty  ELSE 0 END),0) AS printing_qnty,
								NVL(sum(CASE WHEN c.production_type ='3' THEN  c.production_qnty  ELSE 0 END),0) AS printreceived_qnty, */
					}
					//echo $prod_sql;
					foreach($prod_sql as $prodRow);  
					$col = 'col'.$k;
                    if($prodRow[csf("cutting_qnty")]==0)$bgCol="bgcolor='#FF0000'"; 
					else if($prodRow[csf("cutting_qnty")] < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
					else if($prodRow[csf("cutting_qnty")] >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'"; 
					$cutting_html .='<td '.$bgCol.'>'.$prodRow[csf("cutting_qnty")].'</td>';
                    $total_cutting+=$prodRow[csf("cutting_qnty")];
                 	
					if($cons_embr>0)
					{
						if($prodRow[csf("printing_qnty")]==0)$bgCol="bgcolor='#FF0000'"; 
						else if($prodRow[csf("printing_qnty")] < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
						else if($prodRow[csf("printing_qnty")] >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
					}
					else $bgCol='';
                    $printiss_html .='<td '.$bgCol.'>'.$prodRow[csf("printing_qnty")].'</td>';
                    $total_print_issue+=$prodRow[csf("printing_qnty")];
                    
					if($cons_embr>0)
					{
						if($prodRow[csf("printreceived_qnty")]==0)$bgCol="bgcolor='#FF0000'"; 
						else if($prodRow[csf("printreceived_qnty")] < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
						else if($prodRow[csf("printreceived_qnty")] >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
					}
					else $bgCol='';
					
                    $printrcv_html .='<td '.$bgCol.'>'.$prodRow[csf("printreceived_qnty")].'</td>';
                    $total_print_rcv+=$prodRow[csf("printreceived_qnty")];
					
					if($cons_embr>0)
					{
						if($prodRow[csf("emb_qnty")]==0)$bgCol="bgcolor='#FF0000'"; 
						else if($prodRow[csf("emb_qnty")] < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
						else if($prodRow[csf("emb_qnty")] >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
					}
					else $bgCol='';
                    $embroiss_html .='<td '.$bgCol.'>'.$prodRow[csf("emb_qnty")].'</td>';
                    $total_embro_issue+=$prodRow[csf("emb_qnty")];
                    
					if($cons_embr>0)
					{
						if($prodRow[csf("embreceived_qnty")]==0)$bgCol="bgcolor='#FF0000'"; 
						else if($prodRow[csf("embreceived_qnty")] < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
						else if($prodRow[csf("embreceived_qnty")] >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
					}
					else $bgCol='';
					
                    $embrorcv_html .='<td '.$bgCol.'>'.$prodRow[csf("embreceived_qnty")].'</td>';
                    $total_embro_rcv+=$prodRow[csf("embreceived_qnty")];
					
					if($cons_embr>0)
					{
						if($prodRow[csf("sp_qnty")]==0)$bgCol="bgcolor='#FF0000'"; 
						else if($prodRow[csf("sp_qnty")] < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
						else if($prodRow[csf("sp_qnty")] >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
					}
					else $bgCol='';
                    $spiss_html .='<td '.$bgCol.'>'.$prodRow[csf("sp_qnty")].'</td>';
                    $total_sp_issue+=$prodRow[csf("sp_qnty")];
                    
					if($cons_embr>0)
					{
						if($prodRow[csf("spreceived_qnty")]==0)$bgCol="bgcolor='#FF0000'"; 
						else if($prodRow[csf("spreceived_qnty")] < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
						else if($prodRow[csf("spreceived_qnty")] >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
					}
					else $bgCol='';
					
                    $sprcv_html .='<td '.$bgCol.'>'.$prodRow[csf("spreceived_qnty")].'</td>';
                    $total_sp_rcv+=$prodRow[csf("spreceived_qnty")];
					
					if($cons_embr>0)
					{
						if($prodRow[csf("wash_qnty")]==0)$bgCol="bgcolor='#FF0000'"; 
						else if($prodRow[csf("wash_qnty")] < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
						else if($prodRow[csf("wash_qnty")] >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
					}
					else $bgCol='';
                    $washiss_html .='<td '.$bgCol.'>'.$prodRow[csf("wash_qnty")].'</td>';
                    $total_wash_issue+=$prodRow[csf("wash_qnty")];
                    
					if($cons_embr>0)
					{
						if($prodRow[csf("washreceived_qnty")]==0)$bgCol="bgcolor='#FF0000'"; 
						else if($prodRow[csf("washreceived_qnty")] < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
						else if($prodRow[csf("washreceived_qnty")] >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
					}
					else $bgCol='';
					
                    $washrcv_html .='<td '.$bgCol.'>'.$prodRow[csf("washreceived_qnty")].'</td>';
                    $total_wash_rcv+=$prodRow[csf("washreceived_qnty")];
                    
					if($prodRow[csf("sewingin_qnty")]==0)$bgCol="bgcolor='#FF0000'"; 
					else if($prodRow[csf("sewingin_qnty")] < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
					else if($prodRow[csf("sewingin_qnty")] >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'"; 
                    $sewin_html .='<td '.$bgCol.'>'.$prodRow[csf("sewingin_qnty")].'</td>';
                    $total_sew_in+=$prodRow[csf("sewingin_qnty")];
                    
					if($prodRow[csf("sewingout_qnty")]==0)$bgCol="bgcolor='#FF0000'"; 
					else if($prodRow[csf("sewingout_qnty")] < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
					else if($prodRow[csf("sewingout_qnty")] >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";  
                    $sewout_html .='<td '.$bgCol.'>'.$prodRow[csf("sewingout_qnty")].'</td>';
                    $total_sew_out+=$prodRow[csf("sewingout_qnty")];
                    
					/*if($prodRow[csf("finishin_qnty")]==0)$bgCol="bgcolor='#FF0000'"; 
					else if($prodRow[csf("finishin_qnty")] < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
					else if($prodRow[csf("finishin_qnty")] >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'"; 
                    $finisin_html .='<td '.$bgCol.'>'.$prodRow[csf("finishin_qnty")].'</td>';
                    $total_fin_in+=$prodRow[csf("finishin_qnty")];*/
                    
					if($prodRow[csf("finish_qnty")]==0)$bgCol="bgcolor='#FF0000'"; 
					else if($prodRow[csf("finish_qnty")] < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
					else if($prodRow[csf("finish_qnty")] >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'"; 
                    $finisout_html .='<td '.$bgCol.'>'.$prodRow[csf("finish_qnty")].'</td>';
                    $total_fin_out+=$prodRow[csf("finish_qnty")];
					
					if($prodRow[csf("iron_qnty")]==0)$bgCol="bgcolor='#FF0000'"; 
					else if($prodRow[csf("iron_qnty")] < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
					else if($prodRow[csf("iron_qnty")] >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'"; 
                    $iron_html .='<td '.$bgCol.'>'.$prodRow[csf("iron_qnty")].'</td>';
                    $total_iron_out+=$prodRow[csf("iron_qnty")];
					
					//if($prodRow[csf("iron_qnty")]==0)$bgCol="bgcolor='#FF0000'"; 
					//else if($prodRow[csf("iron_qnty")] < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
					//else if($prodRow[csf("iron_qnty")] >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
					if($color_variable_setting==2 || $color_variable_setting==3)
					{ 
						$bgCol=="bgcolor='#FFFFFF'";
						$exfact_html.='<td>'.$ex_fact_qty_arr[$colorRes[csf("color_number_id")]][$sizeRes[csf("size_number_id")]].'&nbsp;</td>';
						
						$total_exfact_qnty+=$ex_fact_qty_arr[$colorRes[csf("color_number_id")]][$sizeRes[csf("size_number_id")]];
					}
					
 				 
				}// end size foreach loop	
				
				?>
					<tr bgcolor="<? echo $bgcolor1; ?>">
                    	<td><b>Cutting</b></td>
                        <? echo $cutting_html; ?> 
                        <td><? echo $total_cutting; ?></td> 
                    </tr>
                    <tr bgcolor="<? echo $bgcolor2; ?>">
                    	<td><b>Print Issue</b></td>
                        <? echo $printiss_html; ?> 
                        <td><? echo $total_print_issue; ?></td> 
                    </tr>
                    <tr bgcolor="<? echo $bgcolor1; ?>">
                    	<td><b>Print Received</b></td>
                        <? echo $printrcv_html; ?> 
                        <td><? echo $total_print_rcv; ?></td> 
                    </tr>
                    <tr bgcolor="<? echo $bgcolor2; ?>">
                    	<td><b>Embro Issue</b></td>
                        <? echo $embroiss_html; ?> 
                        <td><? echo $total_embro_issue; ?></td> 
                    </tr>
                    <tr bgcolor="<? echo $bgcolor1; ?>">
                    	<td><b>Embro Received</b></td>
                        <? echo $embrorcv_html; ?> 
                        <td><? echo $total_embro_rcv; ?></td> 
                    </tr>
                    <tr bgcolor="<? echo $bgcolor2; ?>">
                    	<td><b>Issue For Special Works</b></td>
                        <? echo $spiss_html; ?> 
                        <td><? echo $total_sp_issue; ?></td> 
                    </tr>
                    <tr bgcolor="<? echo $bgcolor1; ?>">
                    	<td><b>Recv. From Special Works</b></td>
                        <? echo $sprcv_html; ?> 
                        <td><? echo $total_sp_rcv; ?></td> 
                    </tr>
                    <tr bgcolor="<? echo $bgcolor2; ?>">
                    	<td><b>Sewing Input</b></td>
                       <? echo $sewin_html; ?> 
                        <td><? echo $total_sew_in; ?></td> 
                    </tr>
                    <tr bgcolor="<? echo $bgcolor1; ?>">
                    	<td><b>Sewing Output</b></td>
                        <? echo $sewout_html; ?> 
                        <td><? echo $total_sew_out; ?></td> 
                    </tr>
                    <tr bgcolor="<? echo $bgcolor2; ?>">
                    	<td><b>Issue For Wash</b></td>
                        <? echo $washiss_html; ?> 
                        <td><? echo $total_wash_issue; ?></td> 
                    </tr>
                    <tr bgcolor="<? echo $bgcolor1; ?>">
                    	<td><b>Recv. From Wash</b></td>
                        <? echo $washrcv_html; ?> 
                        <td><? echo $total_wash_rcv; ?></td> 
                    </tr>
                    <tr bgcolor="<? echo $bgcolor2; ?>">
                    	<td><b>Iron Output</b></td>
                        <? echo $iron_html; ?> 
                        <td><? echo $total_iron_out; ?></td> 
                    </tr>
                   <tr bgcolor="<? echo $bgcolor1; ?>">
                    	<td><b>Finishing Output</b></td>
                       <? echo $finisout_html; ?> 
                        <td><? echo $total_fin_out; ?></td> 
                    </tr>
                    <? 
					if($color_variable_setting==2 || $color_variable_setting==3)
					{
						?>
						<tr>
							<td><b>Ex-Factory Qty.</b></td>
							 <? echo $exfact_html; ?> 
							<td><? echo $total_exfact_qnty; ?>&nbsp;</td> 
						</tr>
						<?
					}
					?>
			<?	
			}// end color foreach loop
			?>
           
		 
 </table>
</div>    


<?
exit();

}// end if condition


if ($action=='OrderPopup')
{
	echo load_html_head_contents("Order Wise Production Report", "../../../", 1, 1,$unicode,'','');
	$po_break_down_id=$_REQUEST['po_break_down_id'];
	$item_id=$_REQUEST['item_id'];
	$company_name=str_replace("'","",$_REQUEST['company_name']);
	$color_variable_setting=return_field_value("ex_factory","variable_settings_production","company_name='$company_name' and variable_list=1 and status_active=1 and is_deleted=0","ex_factory");
	$ex_fact_qty_arr=array();
	if($color_variable_setting==2 || $color_variable_setting==3)
	{
		$sql_exfect="select c.color_number_id, c.size_number_id, sum(b.production_qnty) as production_qnty from pro_ex_factory_mst a,pro_ex_factory_dtls b, wo_po_color_size_breakdown c where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id in ($po_break_down_id) and a.item_number_id=$item_id and a.status_active=1 and a.is_deleted=0 group by  c.color_number_id, c.size_number_id";
		$sql_result_exfact=sql_select($sql_exfect);
		foreach($sql_result_exfact as $row)
		{
			$ex_fact_qty_arr[$row[csf("color_number_id")]][$row[csf("size_number_id")]]=$row[csf("production_qnty")];
		}
	}
	//var_dump($ex_fact_qty_arr);
	
?>
 <div id="data_panel" align="center" style="width:100%">
         <script>
		 	function new_window()
			 {
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write(document.getElementById('details_reports').innerHTML);
				d.close();
			 }
         </script>
 	<input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
 </div>
  
<div style="width:700px" align="center" id="details_reports"> 
  	<legend>Color And Size Wise Summary</legend>
    <table id="tbl_id" class="rpt_table" width="" border="1" rules="all" >
    	<thead>
        	<tr>
            	<th width="100">Buyer</th>
                <th width="100">Job Number</th>
                <th width="100">Style Name</th>
                <th width="300">Order Number</th>
                <th width="100">Ship Date</th>
                <th width="100">Item Name</th>
                <th width="100">Order Qty.</th>
            </tr>
        </thead>
       	<?
        	$buyer_short_library=return_library_array( "select id,short_name from lib_buyer", "id", "short_name"  );
			if($db_type==0)
			{
 				$sql = "select a.job_no_mst,group_concat(distinct(a.po_number)) as po_number,a.pub_shipment_date, sum(a.po_quantity) as po_quantity,a.packing,b.set_break_down, b.company_name, b.order_uom, b.buyer_name, b.style_ref_no,c.set_item_ratio 
					from wo_po_break_down a, wo_po_details_master b, wo_po_details_mas_set_details c 
					where a.job_no_mst=b.job_no and b.job_no=c.job_no and a.id in ($po_break_down_id) and c.gmts_item_id=$item_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
			}
			else
			{
				$sql = "select a.job_no_mst, LISTAGG(a.po_number, ',') WITHIN GROUP (ORDER BY a.id) as po_number,max(a.pub_shipment_date) as pub_shipment_date, sum(a.po_quantity) as po_quantity,a.packing,b.set_break_down, b.company_name, b.order_uom, b.buyer_name, b.style_ref_no,c.set_item_ratio 
					from wo_po_break_down a, wo_po_details_master b, wo_po_details_mas_set_details c 
					where a.job_no_mst=b.job_no and b.job_no=c.job_no and a.id in ($po_break_down_id) and c.gmts_item_id=$item_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.job_no_mst, a.packing,b.set_break_down, b.company_name, b.order_uom, b.buyer_name, b.style_ref_no,c.set_item_ratio";
			}
			//echo $sql;die;
			$resultRow=sql_select($sql);
				
			$cons_embr=return_field_value("sum(cons_dzn_gmts) as cons_dzn_gmts","wo_pre_cost_embe_cost_dtls","job_no='".$resultRow[0][csf("job_no_mst")]."' and status_active=1 and is_deleted=0","cons_dzn_gmts");
			
 		?> 
        <tr>
        	<td><? echo $buyer_short_library[$resultRow[0][csf("buyer_name")]]; ?></td>
            <td><p><? echo $resultRow[0][csf("job_no_mst")]; ?></p></td>
            <td><p><? echo $resultRow[0][csf("style_ref_no")]; ?></p></td>
            <td><p><? echo implode(",",array_unique(explode(",",$resultRow[0][csf("po_number")]))); ?></p></td>
            <td><? echo change_date_format($resultRow[0][csf("pub_shipment_date")]); ?></td>
            <td><? echo $garments_item[$item_id]; ?></td>
            <td><? echo $resultRow[0][csf("po_quantity")]*$resultRow[0][csf("set_item_ratio")]; ?></td>
        </tr>
         <?
         $prod_sewing_sql=sql_select("SELECT sum(alter_qnty) as alter_qnty, sum(reject_qnty) as reject_qnty from pro_garments_production_mst where production_type=5 and po_break_down_id in ($po_break_down_id) and item_number_id=$item_id and is_deleted=0 and status_active=1");
		 foreach($prod_sewing_sql as $sewingRow);
		?> 	
        <tr>
        	<td colspan="2">Total Alter Sewing Qty : <b><? echo $sewingRow[csf("alter_qnty")]; ?></b></td>
        	<td colspan="2">Total Reject Sewing Qty : <b><? echo $sewingRow[csf("reject_qnty")]; ?></b></td>
            <td colspan="2">Pack Assortment: <b><? echo $packing[$resultRow[csf("packing")]]; ?></b></td>
        </tr>
    </table>
    <?
				  
	  $size_Arr_library=return_library_array( "select id,size_name from lib_size", "id", "size_name" );
	  $color_Arr_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );	
	  
	  $color_library=array(); $size_library=array(); $color_library_plan=array(); $dataQty=array();
	  $colorSizeData=sql_select("select color_number_id, size_number_id, order_quantity, plan_cut_qnty, excess_cut_perc from wo_po_color_size_breakdown where po_break_down_id in ($po_break_down_id) and item_number_id=$item_id and status_active=1 and is_deleted=0");
	  foreach($colorSizeData as $csRow)
	  {
		  if($csRow[csf('color_number_id')]>0)
		  {
			  $color_library[$csRow[csf('color_number_id')]]+=$csRow[csf('order_quantity')];
			  $color_library_plan[$csRow[csf('color_number_id')]]+=$csRow[csf('plan_cut_qnty')];
		  }
		  
		  if($csRow[csf('size_number_id')]>0)
		  {
			  $size_library[$csRow[csf('size_number_id')]]=$csRow[csf('size_number_id')];
		  }
		  
		  $dataQty[$csRow[csf('color_number_id')]][$csRow[csf('size_number_id')]][1]+=$csRow[csf('order_quantity')];
		  $dataQty[$csRow[csf('color_number_id')]][$csRow[csf('size_number_id')]][2]+=$csRow[csf('plan_cut_qnty')];
		  $dataQty[$csRow[csf('color_number_id')]][$csRow[csf('size_number_id')]][3]+=$csRow[csf('excess_cut_perc')];
		  $dataQty[$csRow[csf('color_number_id')]][$csRow[csf('size_number_id')]][4]+=1;
	  }
	  
	  $prodDataQty=array();
	  if($db_type==0)
	  {
	 	  $prod_sql= sql_select("SELECT d.color_number_id, d.size_number_id,   
				IFNULL(sum(CASE WHEN c.production_type ='1' THEN c.production_qnty  ELSE 0 END),0) AS cutting_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='2' AND a.embel_name=1 THEN c.production_qnty ELSE 0 END),0) AS printing_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='3' AND a.embel_name=1 THEN c.production_qnty ELSE 0 END),0) AS printreceived_qnty, 
				IFNULL(sum(CASE WHEN c.production_type ='2' AND a.embel_name=2 THEN c.production_qnty ELSE 0 END),0) AS emb_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='3' AND a.embel_name=2 THEN c.production_qnty ELSE 0 END),0) AS embreceived_qnty, 
				IFNULL(sum(CASE WHEN c.production_type ='2' AND a.embel_name=3 THEN c.production_qnty ELSE 0 END),0) AS wash_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='3' AND a.embel_name=3 THEN c.production_qnty ELSE 0 END),0) AS washreceived_qnty, 
				IFNULL(sum(CASE WHEN c.production_type ='2' AND a.embel_name=4 THEN c.production_qnty ELSE 0 END),0) AS sp_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='3' AND a.embel_name=4 THEN c.production_qnty ELSE 0 END),0) AS spreceived_qnty, 
				IFNULL(sum(CASE WHEN c.production_type ='4' THEN c.production_qnty ELSE 0 END),0) AS sewingin_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='5' THEN c.production_qnty ELSE 0 END),0) AS sewingout_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='6' THEN c.production_qnty ELSE 0 END),0) AS finishin_qnty, 
				IFNULL(sum(CASE WHEN c.production_type ='7' THEN c.production_qnty ELSE 0 END),0) AS iron_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='8' THEN c.production_qnty ELSE 0 END),0) AS finish_qnty 
			from 
				pro_garments_production_mst a, pro_garments_production_dtls c,wo_po_color_size_breakdown d
			where  
				a.id=c.mst_id and d.po_break_down_id in (".$po_break_down_id.") and a.item_number_id='$item_id' and c.color_size_break_down_id=d.id and c.status_active=1 $location $floor group by d.color_number_id, d.size_number_id");
	 }
	 else
	 {
		 $prod_sql=sql_select("SELECT d.color_number_id, d.size_number_id, 
				NVL(sum(CASE WHEN c.production_type ='1' THEN c.production_qnty  ELSE 0 END),0) AS cutting_qnty,
				NVL(sum(CASE WHEN c.production_type ='2' AND a.embel_name=1 THEN c.production_qnty ELSE 0 END),0) AS printing_qnty,
				NVL(sum(CASE WHEN c.production_type ='3' AND a.embel_name=1 THEN c.production_qnty ELSE 0 END),0) AS printreceived_qnty,
				NVL(sum(CASE WHEN c.production_type ='2' AND a.embel_name=2 THEN c.production_qnty ELSE 0 END),0) AS emb_qnty,
				NVL(sum(CASE WHEN c.production_type ='3' AND a.embel_name=2 THEN c.production_qnty ELSE 0 END),0) AS embreceived_qnty, 
				NVL(sum(CASE WHEN c.production_type ='2' AND a.embel_name=3 THEN c.production_qnty ELSE 0 END),0) AS wash_qnty,
				NVL(sum(CASE WHEN c.production_type ='3' AND a.embel_name=3 THEN c.production_qnty ELSE 0 END),0) AS washreceived_qnty, 
				NVL(sum(CASE WHEN c.production_type ='2' AND a.embel_name=4 THEN c.production_qnty ELSE 0 END),0) AS sp_qnty,
				NVL(sum(CASE WHEN c.production_type ='3' AND a.embel_name=4 THEN c.production_qnty ELSE 0 END),0) AS spreceived_qnty, 
				NVL(sum(CASE WHEN c.production_type ='4' THEN c.production_qnty ELSE 0 END),0) AS sewingin_qnty,
				NVL(sum(CASE WHEN c.production_type ='5' THEN c.production_qnty ELSE 0 END),0) AS sewingout_qnty,
				NVL(sum(CASE WHEN c.production_type ='6' THEN c.production_qnty ELSE 0 END),0) AS finishin_qnty, 
				NVL(sum(CASE WHEN c.production_type ='7' THEN c.production_qnty ELSE 0 END),0) AS iron_qnty,
				NVL(sum(CASE WHEN c.production_type ='8' THEN c.production_qnty ELSE 0 END),0) AS finish_qnty 
			from 
				pro_garments_production_mst a, pro_garments_production_dtls c, wo_po_color_size_breakdown d
			where  
				a.id=c.mst_id and d.po_break_down_id in (".$po_break_down_id.") and a.item_number_id='$item_id' and c.color_size_break_down_id=d.id and c.status_active=1 and a.status_active=1 $location $floor group by d.color_number_id, d.size_number_id");	
	 }
	 
	 foreach($prod_sql as $row)
	 {
		 $prodDataQty[$row[csf('color_number_id')]][$row[csf('size_number_id')]]['cutting_qnty']=$row[csf('cutting_qnty')];
		 $prodDataQty[$row[csf('color_number_id')]][$row[csf('size_number_id')]]['printing_qnty']=$row[csf('printing_qnty')];
		 $prodDataQty[$row[csf('color_number_id')]][$row[csf('size_number_id')]]['printreceived_qnty']=$row[csf('printreceived_qnty')];
		 $prodDataQty[$row[csf('color_number_id')]][$row[csf('size_number_id')]]['emb_qnty']=$row[csf('emb_qnty')];
		 $prodDataQty[$row[csf('color_number_id')]][$row[csf('size_number_id')]]['embreceived_qnty']=$row[csf('embreceived_qnty')];
		 $prodDataQty[$row[csf('color_number_id')]][$row[csf('size_number_id')]]['wash_qnty']=$row[csf('wash_qnty')];
		 $prodDataQty[$row[csf('color_number_id')]][$row[csf('size_number_id')]]['washreceived_qnty']=$row[csf('washreceived_qnty')];
		 $prodDataQty[$row[csf('color_number_id')]][$row[csf('size_number_id')]]['sp_qnty']=$row[csf('sp_qnty')];
		 $prodDataQty[$row[csf('color_number_id')]][$row[csf('size_number_id')]]['spreceived_qnty']=$row[csf('spreceived_qnty')];
		 $prodDataQty[$row[csf('color_number_id')]][$row[csf('size_number_id')]]['sewingin_qnty']=$row[csf('sewingin_qnty')];
		 $prodDataQty[$row[csf('color_number_id')]][$row[csf('size_number_id')]]['sewingout_qnty']=$row[csf('sewingout_qnty')];
		 $prodDataQty[$row[csf('color_number_id')]][$row[csf('size_number_id')]]['finishin_qnty']=$row[csf('finishin_qnty')];
		 $prodDataQty[$row[csf('color_number_id')]][$row[csf('size_number_id')]]['iron_qnty']=$row[csf('iron_qnty')];
		 $prodDataQty[$row[csf('color_number_id')]][$row[csf('size_number_id')]]['finish_qnty']=$row[csf('finish_qnty')];
	 }
	 // var_dump($color_library1);
	 // echo "<br>";
	//  print_r($size_library1);]

	  /*$color_library=sql_select("select distinct(color_number_id) as color_number_id from wo_po_color_size_breakdown where po_break_down_id in ($po_break_down_id) and color_mst_id!=0 and status_active=1");
	  $size_library=sql_select("select distinct(size_number_id) as size_number_id from wo_po_color_size_breakdown where po_break_down_id in ($po_break_down_id) and size_number_id!=0 and status_active=1");*/
	  $count = count($size_library);	
	  $width= $count*70+350; 		
	?>
    <div style="color:#FF0000; font-size:14px; font-weight:bold; float:left; width:700px">This Pop-Up Will Be Perfect When Order, Production and All Procedure Follow Color or Color & Size Level.</div>
    <table id="tblDtls_id" class="rpt_table" width="<? echo $width; ?>" border="1" rules="all" >
	 	<thead>
        	<tr>
            	<th width="100">Color Name</th>
                <th width="170">Production Type</th>
 				<?
				foreach($size_library as $sizeId=>$val)
				{
				?>
                	<th width="80"><? echo $size_Arr_library[$sizeId]; ?></th>
				<?
				}
				?>
     		    <th width="60">Total</th>
           </tr>
        </thead>
        <?
		  
		  foreach($color_library as $colorId=>$totalorderqnty)
		  {
			  if($color_variable_setting==2 || $color_variable_setting==3) $row_span=17; else $row_span=16;  
			?>	  
			<tr>
				<td rowspan="<? echo $row_span; ?>"><? echo $color_Arr_library[$colorId]; ?></td>
 				<?
 					$bgcolor1="#E9F3FF"; 
					$bgcolor2="#FFFFFF";
				?>
            </tr>
            <tr bgcolor="<? echo $bgcolor1; ?>">
                <td><b>Order Quantity</b></td>	
                <? 
				$color_size_qty=0;
                foreach($size_library as $sizeId=>$sizeRes)
                {
					$color_size_qty=$dataQty[$colorId][$sizeId][1];
                ?>	
                    <td><? echo $dataQty[$colorId][$sizeId][1]; ?></td>
                <? 
                } 
                ?>
                <td><? echo $totalorderqnty; ?></td> 
            </tr>
            <tr bgcolor="<? echo $bgcolor2; ?>">
                <td><b>Plan To Cut (AVG <? echo number_format($dataQty[$colorId][$sizeId][3]/$dataQty[$colorId][$sizeId][4],2); ?>)% </b></td>	
                <? 
                foreach($size_library as $sizeId=>$sizeRes)
                {
                ?>	
                    <td title="Excess Cut <? echo $dataQty[$colorId][$sizeId][3]; ?>%"><? echo $dataQty[$colorId][$sizeId][2]; ?></td>
                <? 
                } 
                ?>
                <td><? echo $color_library_plan[$colorId]; ?></td> 
            </tr>
            <?
 				$total_cutting=0;$total_sew_in=0;$total_sew_out=0;$total_fin_in=0;$total_fin_out=0;$total_iron_out=0; $total_exfact_qnty=0;
				$total_print_issue=0;$total_print_rcv=0;$total_embro_issue=0;$total_embro_rcv=0; $total_sp_issue=0;$total_sp_rcv=0; $total_wash_issue=0;$total_wash_rcv=0;
				$cutting_html='';$sewin_html='';$sewout_html='';$finisin_html='';$finisout_html='';$iron_html=''; $exfact_html='';
				$printiss_html=''; $printrcv_html=''; $embroiss_html=''; $embrorcv_html=''; $spiss_html=''; $sprcv_html=''; $washiss_html=''; $washrcv_html='';
				foreach($size_library as $sizeId=>$sizeRes)
				{ 
					$cutting_qnty=$prodDataQty[$colorId][$sizeId]['cutting_qnty'];
					$printing_qnty=$prodDataQty[$colorId][$sizeId]['printing_qnty'];
					$printreceived_qnty=$prodDataQty[$colorId][$sizeId]['printreceived_qnty'];
					$emb_qnty=$prodDataQty[$colorId][$sizeId]['emb_qnty'];
					$embreceived_qnty=$prodDataQty[$colorId][$sizeId]['embreceived_qnty'];
					$wash_qnty=$prodDataQty[$colorId][$sizeId]['wash_qnty'];
					$washreceived_qnty=$prodDataQty[$colorId][$sizeId]['washreceived_qnty'];
					$sp_qnty=$prodDataQty[$colorId][$sizeId]['sp_qnty'];
					$spreceived_qnty=$prodDataQty[$colorId][$sizeId]['spreceived_qnty'];
					$sewingin_qnty=$prodDataQty[$colorId][$sizeId]['sewingin_qnty'];
					$sewingout_qnty=$prodDataQty[$colorId][$sizeId]['sewingout_qnty'];
					$finishin_qnty=$prodDataQty[$colorId][$sizeId]['finishin_qnty'];
					$iron_qnty=$prodDataQty[$colorId][$sizeId]['iron_qnty'];
					$finish_qnty=$prodDataQty[$colorId][$sizeId]['finish_qnty'];
					
					$resRow[csf($col)]=$dataQty[$colorId][$sizeId][2];
                    if($cutting_qnty==0)$bgCol="bgcolor='#FF0000'"; 
					else if($cutting_qnty < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
					else if($cutting_qnty >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'"; 
					$cutting_html .='<td '.$bgCol.'>'.$cutting_qnty.'</td>';
                    $total_cutting+=$cutting_qnty;
                 	
					if($cons_embr>0)
					{
						if($printing_qnty==0)$bgCol="bgcolor='#FF0000'"; 
						else if($printing_qnty < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
						else if($printing_qnty >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
					}
					else $bgCol='';
                    $printiss_html .='<td '.$bgCol.'>'.$printing_qnty.'</td>';
                    $total_print_issue+=$printing_qnty;
                    
					if($cons_embr>0)
					{
						if($printreceived_qnty==0)$bgCol="bgcolor='#FF0000'"; 
						else if($printreceived_qnty < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
						else if($printreceived_qnty >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
					}
					else $bgCol='';
					
                    $printrcv_html .='<td '.$bgCol.'>'.$printreceived_qnty.'</td>';
                    $total_print_rcv+=$printreceived_qnty;
					
					if($cons_embr>0)
					{
						if($emb_qnty==0)$bgCol="bgcolor='#FF0000'"; 
						else if($emb_qnty < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
						else if($emb_qnty >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
					}
					else $bgCol='';
                    $embroiss_html .='<td '.$bgCol.'>'.$emb_qnty.'</td>';
                    $total_embro_issue+=$emb_qnty;
                    
					if($cons_embr>0)
					{
						if($embreceived_qnty==0)$bgCol="bgcolor='#FF0000'"; 
						else if($embreceived_qnty < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
						else if($embreceived_qnty >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
					}
					else $bgCol='';
					
                    $embrorcv_html .='<td '.$bgCol.'>'.$embreceived_qnty.'</td>';
                    $total_embro_rcv+=$embreceived_qnty;
					
					if($cons_embr>0)
					{
						if($sp_qnty==0)$bgCol="bgcolor='#FF0000'"; 
						else if($sp_qnty < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
						else if($sp_qnty >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
					}
					else $bgCol='';
                    $spiss_html .='<td '.$bgCol.'>'.$sp_qnty.'</td>';
                    $total_sp_issue+=$sp_qnty;
                    
					if($cons_embr>0)
					{
						if($spreceived_qnty==0)$bgCol="bgcolor='#FF0000'"; 
						else if($spreceived_qnty < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
						else if($spreceived_qnty >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
					}
					else $bgCol='';
					
                    $sprcv_html .='<td '.$bgCol.'>'.$spreceived_qnty.'</td>';
                    $total_sp_rcv+=$spreceived_qnty;
					
					if($cons_embr>0)
					{
						if($wash_qnty==0)$bgCol="bgcolor='#FF0000'"; 
						else if($wash_qnty < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
						else if($wash_qnty >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
					}
					else $bgCol='';
                    $washiss_html .='<td '.$bgCol.'>'.$wash_qnty.'</td>';
                    $total_wash_issue+=$wash_qnty;
                    
					if($cons_embr>0)
					{
						if($washreceived_qnty==0)$bgCol="bgcolor='#FF0000'"; 
						else if($washreceived_qnty < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
						else if($washreceived_qnty >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
					}
					else $bgCol='';
					
                    $washrcv_html .='<td '.$bgCol.'>'.$washreceived_qnty.'</td>';
                    $total_wash_rcv+=$washreceived_qnty;
                    
					if($sewingin_qnty==0)$bgCol="bgcolor='#FF0000'"; 
					else if($sewingin_qnty < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
					else if($sewingin_qnty >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'"; 
                    $sewin_html .='<td '.$bgCol.'>'.$sewingin_qnty.'</td>';
                    $total_sew_in+=$sewingin_qnty;
                    
					if($sewingout_qnty==0)$bgCol="bgcolor='#FF0000'"; 
					else if($sewingout_qnty < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
					else if($sewingout_qnty >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";  
                    $sewout_html .='<td '.$bgCol.'>'.$sewingout_qnty.'</td>';
                    $total_sew_out+=$sewingout_qnty;
                    
					/*if($prodRow[csf("finishin_qnty")]==0)$bgCol="bgcolor='#FF0000'"; 
					else if($prodRow[csf("finishin_qnty")] < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
					else if($prodRow[csf("finishin_qnty")] >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'"; 
                    $finisin_html .='<td '.$bgCol.'>'.$prodRow[csf("finishin_qnty")].'</td>';
                    $total_fin_in+=$prodRow[csf("finishin_qnty")];*/
                    
					if($finish_qnty==0)$bgCol="bgcolor='#FF0000'"; 
					else if($finish_qnty < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
					else if($finish_qnty >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'"; 
                    $finisout_html .='<td '.$bgCol.'>'.$finish_qnty.'</td>';
                    $total_fin_out+=$finish_qnty;
					
					if($iron_qnty==0)$bgCol="bgcolor='#FF0000'"; 
					else if($iron_qnty < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
					else if($iron_qnty >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'"; 
                    $iron_html .='<td '.$bgCol.'>'.$iron_qnty.'</td>';
                    $total_iron_out+=$iron_qnty;
					
					//if($prodRow[csf("iron_qnty")]==0)$bgCol="bgcolor='#FF0000'"; 
					//else if($prodRow[csf("iron_qnty")] < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
					//else if($prodRow[csf("iron_qnty")] >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
					if($color_variable_setting==2 || $color_variable_setting==3)
					{ 
						$bgCol=="bgcolor='#FFFFFF'";
						$exfact_html.='<td>'.$ex_fact_qty_arr[$colorId][$sizeId].'&nbsp;</td>';
						
						$total_exfact_qnty+=$ex_fact_qty_arr[$colorId][$sizeId];
					}
					
 				 
				}// end size foreach loop	
				
				?>
					<tr bgcolor="<? echo $bgcolor1; ?>">
                    	<td><b>Cutting</b></td>
                        <? echo $cutting_html; ?> 
                        <td><? echo $total_cutting; ?></td> 
                    </tr>
                    <tr bgcolor="<? echo $bgcolor2; ?>">
                    	<td><b>Print Issue</b></td>
                        <? echo $printiss_html; ?> 
                        <td><? echo $total_print_issue; ?></td> 
                    </tr>
                    <tr bgcolor="<? echo $bgcolor1; ?>">
                    	<td><b>Print Received</b></td>
                        <? echo $printrcv_html; ?> 
                        <td><? echo $total_print_rcv; ?></td> 
                    </tr>
                    <tr bgcolor="<? echo $bgcolor2; ?>">
                    	<td><b>Embro Issue</b></td>
                        <? echo $embroiss_html; ?> 
                        <td><? echo $total_embro_issue; ?></td> 
                    </tr>
                    <tr bgcolor="<? echo $bgcolor1; ?>">
                    	<td><b>Embro Received</b></td>
                        <? echo $embrorcv_html; ?> 
                        <td><? echo $total_embro_rcv; ?></td> 
                    </tr>
                    <tr bgcolor="<? echo $bgcolor2; ?>">
                    	<td><b>Issue For Special Works</b></td>
                        <? echo $spiss_html; ?> 
                        <td><? echo $total_sp_issue; ?></td> 
                    </tr>
                    <tr bgcolor="<? echo $bgcolor1; ?>">
                    	<td><b>Recv. From Special Works</b></td>
                        <? echo $sprcv_html; ?> 
                        <td><? echo $total_sp_rcv; ?></td> 
                    </tr>
                    <tr bgcolor="<? echo $bgcolor2; ?>">
                    	<td><b>Sewing Input</b></td>
                       <? echo $sewin_html; ?> 
                        <td><? echo $total_sew_in; ?></td> 
                    </tr>
                    <tr bgcolor="<? echo $bgcolor1; ?>">
                    	<td><b>Sewing Output</b></td>
                        <? echo $sewout_html; ?> 
                        <td><? echo $total_sew_out; ?></td> 
                    </tr>
                    <tr bgcolor="<? echo $bgcolor2; ?>">
                    	<td><b>Issue For Wash</b></td>
                        <? echo $washiss_html; ?> 
                        <td><? echo $total_wash_issue; ?></td> 
                    </tr>
                    <tr bgcolor="<? echo $bgcolor1; ?>">
                    	<td><b>Recv. From Wash</b></td>
                        <? echo $washrcv_html; ?> 
                        <td><? echo $total_wash_rcv; ?></td> 
                    </tr>
                    <tr bgcolor="<? echo $bgcolor2; ?>">
                    	<td><b>Iron Output</b></td>
                        <? echo $iron_html; ?> 
                        <td><? echo $total_iron_out; ?></td> 
                    </tr>
                   <tr bgcolor="<? echo $bgcolor1; ?>">
                    	<td><b>Finishing Output</b></td>
                       <? echo $finisout_html; ?> 
                        <td><? echo $total_fin_out; ?></td> 
                    </tr>
                    <? 
					if($color_variable_setting==2 || $color_variable_setting==3)
					{
						?>
						<tr>
							<td><b>Ex-Factory Qty.</b></td>
							 <? echo $exfact_html; ?> 
							<td><? echo $total_exfact_qnty; ?>&nbsp;</td> 
						</tr>
						<?
					}
					?>
			<?	
			}// end color foreach loop
			?>
           
		 
 </table>
</div>    


<?
exit();

}// end if condition

if ($action=='OrderPopupCountry')
{
	echo load_html_head_contents("Order Wise Production Report", "../../../", 1, 1,$unicode,'','');
	$po_break_down_id=$_REQUEST['po_break_down_id'];
	$item_id=$_REQUEST['item_id'];
	$country_id=$_REQUEST['country_id'];
	
?>

	
 <div id="data_panel" align="center" style="width:100%">
         <script>
		 	function new_window()
			 {
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write(document.getElementById('details_reports').innerHTML);
				d.close();
			 }
         </script>
 	<input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
 </div>
  
<div style="width:700px" align="center" id="details_reports"> 
  	<legend>Color And Size Wise Summary</legend>
    <table id="tbl_id" class="rpt_table" width="680" border="1" rules="all" >
    	<thead>
        	<tr>
            	<th width="100">Buyer</th>
                <th width="100">Job Number</th>
                <th width="100">Style Name</th>
                <th width="100">Order Number</th>
                <th width="100">Ship Date</th>
                <th width="100">Item Name</th>
                <th width="100">Order Qnty.</th>
                <th>Country</th>
            </tr>
        </thead>
       	<?
        	$buyer_short_library=return_library_array( "select id,short_name from lib_buyer", "id", "short_name"  );
			$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name"  );
 			$sql = sql_select("select a.job_no_mst,a.po_number,a.pub_shipment_date,a.po_quantity,a.packing,b.set_break_down, b.company_name, b.order_uom, b.buyer_name, b.style_ref_no,c.set_item_ratio 
					from wo_po_break_down a, wo_po_details_master b, wo_po_details_mas_set_details c 
					where a.job_no_mst=b.job_no and b.job_no=c.job_no and a.id in ($po_break_down_id) and c.gmts_item_id=$item_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
			//echo $sql;
			foreach($sql as $resultRow);
			
			$cons_embr=return_field_value("sum(cons_dzn_gmts) as cons_dzn_gmts","wo_pre_cost_embe_cost_dtls","job_no='".$resultRow[csf("job_no_mst")]."' and status_active=1 and is_deleted=0","cons_dzn_gmts");
			
			$po_qnty=return_field_value("sum(order_quantity) as qnty","wo_po_color_size_breakdown","po_break_down_id in ($po_break_down_id) and item_number_id='$item_id' and country_id='$country_id' and status_active=1 and is_deleted=0","qnty");	
 		?> 
        <tr>
        	<td><? echo $buyer_short_library[$resultRow[csf("buyer_name")]]; ?></td>
            <td><p><? echo $resultRow[csf("job_no_mst")]; ?></p></td>
            <td><p><? echo $resultRow[csf("style_ref_no")]; ?></p></td>
            <td><? echo $resultRow[csf("po_number")]; ?></td>
            <td><? echo change_date_format($resultRow[csf("pub_shipment_date")]); ?></td>
             <td><p><? echo $garments_item[$item_id]; ?></p></td>
            <td><? echo $po_qnty; ?></td>
            <td><? echo $country_library[$country_id]; ?></td>
        </tr>
         <?
         $prod_sewing_sql=sql_select("SELECT sum(alter_qnty) as alter_qnty, sum(reject_qnty) as reject_qnty from pro_garments_production_mst where production_type=5 and po_break_down_id in ($po_break_down_id) and is_deleted=0 and status_active=1");
		 foreach($prod_sewing_sql as $sewingRow);
		?> 	
        <tr>
        	<td colspan="2">Total Alter Sewing Qnty : <b><? echo $sewingRow[csf("alter_qnty")]; ?></b></td>
        	<td colspan="2">Total Reject Sewing Qnty : <b><? echo $sewingRow[csf("reject_qnty")]; ?></b></td>
            <td colspan="2">Pack Assortment: <b><? echo $packing[$resultRow[csf("packing")]]; ?></b></td>
        </tr>
    </table>
    <?
				  
	  $size_Arr_library=return_library_array( "select id,size_name from lib_size", "id", "size_name" );
	  $color_Arr_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );	
	  
	  $color_library=sql_select("select distinct(color_number_id) from wo_po_color_size_breakdown where po_break_down_id in ($po_break_down_id) and color_mst_id!=0 and status_active=1 ");
	  $size_library=sql_select("select distinct(size_number_id) from wo_po_color_size_breakdown where po_break_down_id in ($po_break_down_id) and size_mst_id!=0 and status_active=1");
	  $count = count($size_library);	
	  $width= $count*70+350; 		
	?>
    <table id="tblDtls_id" class="rpt_table" width="<? echo $width; ?>" border="1" rules="all" >
	 	<thead>
        	<tr>
            	<th width="100">Color Name</th>
                <th width="200">Production Type</th>
 				<?
				foreach($size_library as $sizeRes)
				{
				 	?><th width="80"><? echo $size_Arr_library[$sizeRes[csf("size_number_id")]]; ?></th><?
				}
				?>
     		    <th width="60">Total</th>
           </tr>
        </thead>
        <?
		  
		  foreach($color_library as $colorRes)
		  {
			?>	  
			<tr>
				<td rowspan="10"><? echo $color_Arr_library[$colorRes[csf("color_number_id")]]; ?></td>
			
 			<?
            	  $i=0;$j=0;$sqlPart="";
				  foreach($size_library as $sizeRes)
				  {
					  $i++;$j++;
					  if($i>1) $sqlPart .=",";
					  $sqlPart .= 'SUM( CASE WHEN color_number_id='.$colorRes[csf("color_number_id")].' and size_number_id='.$sizeRes[csf("size_number_id")].' THEN order_quantity ELSE 0 END ) as '."col".$i;
					  $sqlPart .= ',SUM( CASE WHEN color_number_id='.$colorRes[csf("color_number_id")].' and size_number_id='.$sizeRes[csf("size_number_id")].' THEN plan_cut_qnty ELSE 0 END ) as '."pcut".$i;
					  $sqlPart .= ',SUM( CASE WHEN color_number_id='.$colorRes[csf("color_number_id")].' and size_number_id='.$sizeRes[csf("size_number_id")].' THEN excess_cut_perc ELSE 0 END ) as '."excess_cut".$i;
				  }
				  if($j>1)
				  {
					 $sqlPart .=',SUM( CASE WHEN color_number_id='.$colorRes[csf("color_number_id")].' THEN order_quantity ELSE 0 END ) as totalorderqnty';
					 $sqlPart .=',SUM( CASE WHEN color_number_id='.$colorRes[csf("color_number_id")].' THEN plan_cut_qnty ELSE 0 END ) as totalplancutqnty';
				  }
		  
				$sql = sql_select("select avg(excess_cut_perc) as avg_excess_cut_perc,excess_cut_perc,". $sqlPart ." from wo_po_color_size_breakdown where status_active=1 and po_break_down_id in ($po_break_down_id) and item_number_id=$item_id and country_id='$country_id'");
				//echo $sql;die;
				foreach($sql as $resRow); 
 					$bgcolor1="#E9F3FF"; 
					$bgcolor2="#FFFFFF";
				?>
					 
					<tr bgcolor="<? echo $bgcolor1; ?>">
						<td><b>Order Quantity</b></td>	
                        <? for($k=1;$k<=$i;$k++) {	$col = 'col'.$k; ?>	
                         	<td><? echo $resRow[csf($col)]; ?></td>
						<? } ?>
                         <td><? echo $resRow[csf("totalorderqnty")]; ?></td> 
					</tr>
                    <tr bgcolor="<? echo $bgcolor2; ?>">
                    	<td><b>Plan To Cut (AVG <? echo $resRow[csf("avg_excess_cut_perc")]; ?>)% </b></td>	
                        <? for($k=1;$k<=$i;$k++){ $col = 'pcut'.$k;$excess_cut = 'excess_cut'.$k;	?>	
                         	<td title="Excess Cut <? echo $resRow[csf($excess_cut)]; ?>%"><? echo $resRow[csf($col)]; ?></td>
						<? } ?>
                         <td><? echo $resRow[csf("totalplancutqnty")]; ?></td> 
                    </tr>
					
                <?
 				$total_cutting=0;$total_emb_issue=0;$total_emb_rcv=0;$total_sew_in=0;$total_sew_out=0;$total_fin_in=0;$total_fin_out=0;$total_iron_out=0;
				$cutting_html='';$embiss_html='';$embrcv_html='';$sewin_html='';$sewout_html='';$finisin_html='';$finisout_html=''; $iron_html='';
				$k=0;
				foreach($size_library as $sizeRes)
				{
					$k++;
					if($db_type==0)
					{
						$prod_sql= sql_select("SELECT  
								IFNULL(sum(CASE WHEN c.production_type ='1' THEN  c.production_qnty  ELSE 0 END),0) AS cutting_qnty,
								IFNULL(sum(CASE WHEN c.production_type ='2' THEN  c.production_qnty  ELSE 0 END),0) AS printing_qnty,
								IFNULL(sum(CASE WHEN c.production_type ='3' THEN  c.production_qnty  ELSE 0 END),0) AS printreceived_qnty, 
								IFNULL(sum(CASE WHEN c.production_type ='4' THEN  c.production_qnty  ELSE 0 END),0) AS sewingin_qnty,
								IFNULL(sum(CASE WHEN c.production_type ='5' THEN  c.production_qnty  ELSE 0 END),0) AS sewingout_qnty,
								IFNULL(sum(CASE WHEN c.production_type ='6' THEN  c.production_qnty  ELSE 0 END),0) AS finishin_qnty, 
								IFNULL(sum(CASE WHEN c.production_type ='7' THEN  c.production_qnty  ELSE 0 END),0) AS iron_qnty,
								
								IFNULL(sum(CASE WHEN c.production_type ='8' THEN  c.production_qnty  ELSE 0 END),0) AS finish_qnty 
							from 
								pro_garments_production_dtls c,wo_po_color_size_breakdown d
							where  
								d.po_break_down_id in (".$po_break_down_id.") and d.item_number_id='$item_id' and d.country_id='$country_id' and d.color_number_id=".$colorRes[csf("color_number_id")]." and d.size_number_id=".$sizeRes[csf("size_number_id")]." and c.color_size_break_down_id=d.id and c.status_active=1 $location $floor ");
					}
					else
					{
						$prod_sql= sql_select("SELECT  
							NVL(sum(CASE WHEN c.production_type ='1' THEN  c.production_qnty  ELSE 0 END),0) AS cutting_qnty,
							NVL(sum(CASE WHEN c.production_type ='2' THEN  c.production_qnty  ELSE 0 END),0) AS printing_qnty,
							NVL(sum(CASE WHEN c.production_type ='3' THEN  c.production_qnty  ELSE 0 END),0) AS printreceived_qnty, 
							NVL(sum(CASE WHEN c.production_type ='4' THEN  c.production_qnty  ELSE 0 END),0) AS sewingin_qnty,
							NVL(sum(CASE WHEN c.production_type ='5' THEN  c.production_qnty  ELSE 0 END),0) AS sewingout_qnty,
							NVL(sum(CASE WHEN c.production_type ='6' THEN  c.production_qnty  ELSE 0 END),0) AS finishin_qnty, 
							NVL(sum(CASE WHEN c.production_type ='7' THEN  c.production_qnty  ELSE 0 END),0) AS iron_qnty,
							NVL(sum(CASE WHEN c.production_type ='8' THEN  c.production_qnty  ELSE 0 END),0) AS finish_qnty 
						from 
							pro_garments_production_dtls c,wo_po_color_size_breakdown d
						where  
							d.po_break_down_id in (".$po_break_down_id.") and d.item_number_id='$item_id' and d.country_id='$country_id' and d.color_number_id=".$colorRes[csf("color_number_id")]." and d.size_number_id=".$sizeRes[csf("size_number_id")]." and c.color_size_break_down_id=d.id and c.status_active=1 $location $floor ");	
					}
					
					foreach($prod_sql as $prodRow);  
					$col = 'col'.$k;
                    if($prodRow[csf("cutting_qnty")]==0)$bgCol="bgcolor='#FF0000'"; 
					else if($prodRow[csf("cutting_qnty")] < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
					else if($prodRow[csf("cutting_qnty")] >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'"; 
					$cutting_html .='<td '.$bgCol.'>'.$prodRow[csf("cutting_qnty")].'</td>';
                    $total_cutting+=$prodRow[csf("cutting_qnty")];
                 	
					if($cons_embr>0)
					{
						if($prodRow[csf("printing_qnty")]==0)$bgCol="bgcolor='#FF0000'"; 
						else if($prodRow[csf("printing_qnty")] < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
						else if($prodRow[csf("printing_qnty")] >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
					}
					else $bgCol='';
                    $embiss_html .='<td '.$bgCol.'>'.$prodRow[csf("printing_qnty")].'</td>';
                    $total_emb_issue+=$prodRow[csf("printing_qnty")];
                    
					if($cons_embr>0)
					{
						if($prodRow[csf("printreceived_qnty")]==0)$bgCol="bgcolor='#FF0000'"; 
						else if($prodRow[csf("printreceived_qnty")] < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
						else if($prodRow[csf("printreceived_qnty")] >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
					}
					else $bgCol='';
					
                    $embrcv_html .='<td '.$bgCol.'>'.$prodRow[csf("printreceived_qnty")].'</td>';
                    $total_emb_rcv+=$prodRow[csf("printreceived_qnty")];
                    
					if($prodRow[csf("sewingin_qnty")]==0)$bgCol="bgcolor='#FF0000'"; 
					else if($prodRow[csf("sewingin_qnty")] < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
					else if($prodRow[csf("sewingin_qnty")] >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'"; 
                    $sewin_html .='<td '.$bgCol.'>'.$prodRow[csf("sewingin_qnty")].'</td>';
                    $total_sew_in+=$prodRow[csf("sewingin_qnty")];
                    
					if($prodRow[csf("sewingout_qnty")]==0)$bgCol="bgcolor='#FF0000'"; 
					else if($prodRow[csf("sewingout_qnty")] < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
					else if($prodRow[csf("sewingout_qnty")] >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";  
                    $sewout_html .='<td '.$bgCol.'>'.$prodRow[csf("sewingout_qnty")].'</td>';
                    $total_sew_out+=$prodRow[csf("sewingout_qnty")];
                    
					if($prodRow[csf("iron_qnty")]==0)$bgCol="bgcolor='#FF0000'"; 
					else if($prodRow[csf("iron_qnty")] < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
					else if($prodRow[csf("iron_qnty")] >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'"; 
                    $iron_html .='<td '.$bgCol.'>'.$prodRow[csf("iron_qnty")].'</td>';
                    $total_iron_out+=$prodRow[csf("iron_qnty")];
					
				
					if($prodRow[csf("finish_qnty")]==0)$bgCol="bgcolor='#FF0000'"; 
					else if($prodRow[csf("finish_qnty")] < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
					else if($prodRow[csf("finish_qnty")] >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'"; 
                    $finisout_html .='<td '.$bgCol.'>'.$prodRow[csf("finish_qnty")].'</td>';
                    $total_fin_out+=$prodRow[csf("finish_qnty")];
					
					
                    
				}// end size foreach loop	
				
				?>
					<tr bgcolor="<? echo $bgcolor1; ?>">
                    	<td><b>Cutting</b></td>
                        <? echo $cutting_html; ?> 
                        <td><? echo $total_cutting; ?></td> 
                    </tr>
                    <tr bgcolor="<? echo $bgcolor2; ?>">
                    	<td><b>Print/Embro Issue</b></td>
                        <? echo $embiss_html; ?> 
                        <td><? echo $total_emb_issue; ?></td> 
                    </tr>
                    <tr bgcolor="<? echo $bgcolor1; ?>">
                    	<td><b>Print/Embro Received</b></td>
                        <? echo $embrcv_html; ?> 
                        <td><? echo $total_emb_rcv; ?></td> 
                    </tr>
                    <tr bgcolor="<? echo $bgcolor2; ?>">
                    	<td><b>Sewing Input</b></td>
                       <? echo $sewin_html; ?> 
                        <td><? echo $total_sew_in; ?></td> 
                    </tr>
                    <tr bgcolor="<? echo $bgcolor1; ?>">
                    	<td><b>Sewing Output</b></td>
                        <? echo $sewout_html; ?> 
                        <td><? echo $total_sew_out; ?></td> 
                    </tr>
                    <tr bgcolor="<? echo $bgcolor2; ?>">
                    	<td><b>Iron Output</b></td>
                        <? echo $iron_html; ?> 
                        <td><? echo  $total_iron_out; ?></td> 
                    </tr>
                    <tr bgcolor="<? echo $bgcolor1; ?>">
                    	<td><b>Finishing Output</b></td>
                       <? echo $finisout_html; ?> 
                        <td><? echo $total_fin_out; ?></td> 
                    </tr> 
			<?	
			}// end color foreach loop
			?>
           
		 
 </table>
</div>    


<?
exit();

}// end if condition


//cutting-1,sewing ouput-5--------------------popup-----------//
if ($action=='exfactoryCountry_shipdate')  // exfactory date popup
{
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,1,1);
	
	$country_id=implode(",",array_unique(explode("*",$country_id)));
	
 	?>
    <fieldset>
    <div style="margin-left:60px">
        <table width="500" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1" >
            <thead>
                    <tr>
                        <th width="50">Sl.</th>    
                        <th width="200">Ex Factory Date</th>
                        <th width="">Ex Factory Qnty</th>
               		</tr>
            </thead>
        </table>
        <div style="max-height:425px; overflow-y:scroll; width:518px;" id="scroll_body">
            <table cellspacing="0" border="1" class="rpt_table"  width="500" rules="all" id="table_body" >
            <?
             $total_quantity=0;
             $sql=sql_select("select sum(ex_factory_qnty) as ex_factory_qnty, ex_factory_date 		  
				  from pro_ex_factory_mst where po_break_down_id in ($po_break_down_id) and item_number_id in ($item_id) and country_id in ($country_id) and status_active=1 and is_deleted=0 group by ex_factory_date order by ex_factory_date"); 
           $i=1; 
            foreach($sql as $resultRow)
            {
                 if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
             	?>
                 <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                    <td width="50"><? echo $i;?></td>
                    <td width="200" align="right"><? if($resultRow[csf("ex_factory_date")]!="0000-00-00") echo change_date_format($resultRow[csf("ex_factory_date")]); ?></td>
                    <td width="" align="right"><? echo number_format($resultRow[csf("ex_factory_qnty")]); ?></td>
                 </tr>	
                 <?		
                    $total_quantity+=$resultRow[csf("ex_factory_qnty")];
            	 	$i++;
            
        }//end foreach 1st
        ?>
        </table>
        <table cellspacing="0" border="1" class="tbl_bottom"  width="500" rules="all" id="body_bottom" >
                 <tr> 
                    <td width="50">&nbsp;</td> 
                    <td width="200">Total</td> 
                    <td width=""><? echo number_format($total_quantity); ?></td>
                  </tr>
         </table>
       </div>
     </div>
     </fieldset>
    <?
 	
 exit();
 
}

if($action=='country_shipdate_wise_embl') 
{
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,1,1);
	
	if($country_id=='') $country_cond=""; else $country_cond=" and country_id='$country_id'";
	$item_id=implode(",",explode("*",$item_id));
 	?>
    <div id="data_panel" align="center" style="width:100%">
         <script>
		 	function new_window()
			 {
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write(document.getElementById('details_reports').innerHTML);
				d.close();
			 }
          </script>
 	<input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
 	</div>
    <div id="details_reports">
        <table width="1260" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1" >
            <thead>
                   
                 <? if ($production_type==2) { ?>  
                   <tr>
                        <th width="30" rowspan="2">Sl.</th>    
                        <th width="80" rowspan="2">Date</th>
                        <th colspan="3">Printing Issue</th>
                        <th colspan="3">Embroidery Issue</th>
                        <th colspan="3">Wash Issue</th>
                        <th colspan="3">Special Work Issue</th>
                         <th colspan="3">Gmt Dyeing Issue</th>
                    </tr> 
                 <? } else {?>
                 	<tr>
                        <th width="30" rowspan="2">Sl.</th>    
                        <th width="70" rowspan="2">Date</th>
                        <th colspan="3">Printing Receive</th>
                        <th colspan="3">Embroidery Receive</th>
                        <th colspan="3">Wash Receive</th>
                        <th colspan="3">Special Work Receive</th>
                         <th colspan="3">Gmt Dyeing Issue</th>
                    </tr> 
                 <? } ?>   
                    
                    <tr>
                      
                      <th width="70">In-house</th>
                      <th width="70">Outside</th>
                      <th width="80">Embl. Company</th>
                      
                      <th width="70">In-house</th>
                      <th width="70">Outside</th>
                      <th width="80">Embl. Company</th>
                      
                      <th width="70">In-house</th>
                      <th width="70">Outside</th>
                      <th width="80">Embl. Company</th>
                      
                       <th width="70">In-house</th>
                      <th width="70">Outside</th>
                      <th width="80">Embl. Company</th>
                      
                      <th width="70">In-house</th>
                      <th width="70">Outside</th>
                      <th>Embl. Company</th>
                    </tr>
            </thead>
        </table>
        <div style="max-height:425px; overflow-y:scroll; width:1260px;" id="scroll_body">
            <table cellspacing="0" border="1" class="rpt_table"  width="1242" rules="all" id="table_body" >
            <?
			$company_library=return_library_array( "select id,company_short_name from lib_company", "id", "company_short_name" );
 			$supplier_library=return_library_array( "select id,short_name from  lib_supplier", "id", "short_name" );	
 			
			$sql = sql_select("SELECT production_date,production_source,serving_company,
						SUM(CASE WHEN production_source =1 AND embel_name=1 THEN production_quantity ELSE 0 END) AS prod11,  
						SUM(CASE WHEN production_source =1 AND embel_name=2 THEN production_quantity ELSE 0 END) AS prod12,
						SUM(CASE WHEN production_source =1 AND embel_name=3 THEN production_quantity ELSE 0 END) AS prod13,
						SUM(CASE WHEN production_source =1 AND embel_name=4 THEN production_quantity ELSE 0 END) AS prod14,
						SUM(CASE WHEN production_source =1 AND embel_name=5 THEN production_quantity ELSE 0 END) AS prod15,
						
						SUM(CASE WHEN production_source =3 AND embel_name=1 THEN production_quantity ELSE 0 END) AS prod31,  
						SUM(CASE WHEN production_source =3 AND embel_name=2 THEN production_quantity ELSE 0 END) AS prod32,
						SUM(CASE WHEN production_source =3 AND embel_name=3 THEN production_quantity ELSE 0 END) AS prod33,
						SUM(CASE WHEN production_source =3 AND embel_name=4 THEN production_quantity ELSE 0 END) AS prod34,
						SUM(CASE WHEN production_source =3 AND embel_name=5 THEN production_quantity ELSE 0 END) AS prod35
 					FROM
						pro_garments_production_mst 
					WHERE 
						status_active=1 and is_deleted=0 and production_type=$production_type and po_break_down_id in ($po_break_down_id) and item_number_id in ($item_id) $country_cond
					GROUP BY production_date,production_source,serving_company");
			// echo $sql; die;
			
		   	$printing_in_qnty=0;$emb_in_qnty=0;$wash_in_qnty=0;$special_in_qnty=0;
			$printing_out_qnty=0;$emb_out_qnty=0;$wash_out_qnty=0;$special_out_qnty=0;$gd_in_qnty=0;$gd_out_qnty=0;
			$dataArray=array();$companyArray=array();
            $i=1;
			foreach($sql as $resultRow)
            {
                 if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				 
				 if($resultRow[csf('production_source')]==3)
					$serving_company= $supplier_library[$resultRow[csf('serving_company')]];
				else
					$serving_company= $company_library[$resultRow[csf('serving_company')]];
				$td_count = 2;	
             	?>
                 <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                    <td width="30"><? echo $i;?></td>
                    <td width="80" align="center"><? echo change_date_format($resultRow[csf("production_date")]); ?></td>
                    
                    <td width="70" align="right"><? if($resultRow[csf('production_source')]==1){ echo $resultRow[csf("prod11")];$printing_in_qnty+=$resultRow[csf("prod11")];}else echo "0"; ?></td>
                    <td width="70" align="right"><? if($resultRow[csf('production_source')]==3){ echo $resultRow[csf("prod31")];$printing_out_qnty+=$resultRow[csf("prod31")];}else echo "0"; ?></td>
                    <td width="80"><p>&nbsp;<? if($resultRow[csf('prod11')]>0 || $resultRow[csf('prod31')]>0) echo $serving_company; ?></p></td>
                    <? 
					$companyArray[$serving_company]=$serving_company;
					$dataArray[1][$serving_company]+=$resultRow[csf("prod11")]+$resultRow[csf("prod31")] ?>
                    
                    <td width="70" align="right"><? if($resultRow[csf('production_source')]==1){ echo $resultRow[csf("prod12")];$emb_in_qnty+=$resultRow[csf("prod12")];}else echo "0"; ?></td>
                    <td width="70" align="right"><? if($resultRow[csf('production_source')]==3){ echo $resultRow[csf("prod32")];$emb_out_qnty+=$resultRow[csf("prod32")];}else echo "0"; ?></td>
                    <td width="80"><p>&nbsp;<? if($resultRow[csf('prod12')]>0 || $resultRow[csf('prod32')]>0) echo $serving_company; ?></p></td>
                    <? 
 					$dataArray[2][$serving_company]+=$resultRow[csf("prod12")]+$resultRow[csf("prod32")] ?>
                    
                    <td width="70" align="right"><? if($resultRow[csf('production_source')]==1){ echo $resultRow[csf("prod13")];$wash_in_qnty+=$resultRow[csf("prod13")];}else echo "0"; ?></td>
                    <td width="70" align="right"><? if($resultRow[csf('production_source')]==3){ echo $resultRow[csf("prod33")];$wash_out_qnty+=$resultRow[csf("prod33")];}else echo "0"; ?></td>
                    <td width="80"><p>&nbsp;<? if($resultRow[csf('prod13')]>0 || $resultRow[csf('prod33')]>0) echo $serving_company; ?></p></td>
                    <? 
 					$dataArray[3][$serving_company]+=$resultRow[csf("prod13")]+$resultRow[csf("prod33")] ?>
                    
                    <td width="70" align="right"><? if($resultRow[csf('production_source')]==1){ echo $resultRow[csf("prod14")];$special_in_qnty+=$resultRow[csf("prod14")];}else echo "0"; ?></td>
                    <td width="70" align="right"><? if($resultRow[csf('production_source')]==3){ echo $resultRow[csf("prod34")];$special_out_qnty+=$resultRow[csf("prod34")];}else echo "0"; ?></td>
                    <td width="80"><p>&nbsp;<? if($resultRow[csf('prod14')]>0 || $resultRow[csf('prod34')]>0) echo $serving_company; ?></p></td>
                    <? 
 					$dataArray[4][$serving_company]+=$resultRow[csf("prod14")]+$resultRow[csf("prod34")];
					$dataArray[5][$serving_company]+=$resultRow[csf("prod15")]+$resultRow[csf("prod35")] 
					 ?>
                    <td width="70" align="right"><? if($resultRow[csf('production_source')]==1){ echo $resultRow[csf("prod15")];$gd_in_qnty+=$resultRow[csf("prod15")];}else echo "0"; ?></td>
                    <td width="70" align="right"><? if($resultRow[csf('production_source')]==3){ echo $resultRow[csf("prod35")];$gd_out_qnty+=$resultRow[csf("prod35")];}else echo "0"; ?></td>
                    <td><p>&nbsp;<? if($resultRow[csf('prod15')]>0 || $resultRow[csf('prod35')]>0) echo $serving_company; ?></p></td>
                  </tr> 
 				 <?		
             	$i++;
            
        }//end foreach 1st
        ?>
        		<tfoot>
                    <tr>
                       <th align="right" colspan="2">Grand Total</th>
                       <th align="right"><? echo $printing_in_qnty; ?></th>
                       <th align="right"><? echo $printing_out_qnty; ?></th>
                       <th align="right">&nbsp;</th>
                       <th align="right"><? echo $emb_in_qnty; ?></th>
                       <th align="right"><? echo $emb_out_qnty; ?></th>
                       <th align="right">&nbsp;</th>
                       <th align="right"><? echo $wash_in_qnty; ?></th>
                       <th align="right"><? echo $wash_out_qnty; ?></th>
                       <th align="right">&nbsp;</th>
                       <th align="right"><? echo $special_in_qnty; ?></th>
                       <th align="right"><? echo $special_out_qnty; ?></th>
                       <th align="right">&nbsp;</th>
                       <th align="right"><? echo $gd_in_qnty; ?></th>
                       <th align="right"><? echo $gd_out_qnty; ?></th>
                       <th align="right">&nbsp;</th>
                     </tr>
               </tfoot>      
        </table>
       </div>
       
       <div style="clear:both">&nbsp;</div>
       
       <div style="width:500px; float:left"> 
       <table width="450" cellspacing="0" border="1" class="rpt_table" rules="all" > 
       		<? if($production_type==2){?> <label><h3>Issue Summary</h3></label><? } else {?> <label><h3>Receive Summary</h3></label> <? } ?>               	
             <thead> 
                <tr>
                    <th>SL</th>
                    <th>Emb.Company</th>
                    <th>Print</th>
                    <th>Embroidery</th>
                    <th>Emb	Wash</th>
                    <th>Special Work</th>
                    <th>Gmt Dyeing</th>
                 </tr>
              </thead>  
			 <?
			 $printing_total=0;$emb_total=0;$wash_total=0;$special_total=0;$gd_total=0;
			 $i=1;	 
			 foreach($companyArray as $com){
			 	if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; 
			 ?>
                 <tr bgcolor="<? echo $bgcolor; ?>">
                 		<td><? echo $i; ?></td>
                        <td><? echo $com; ?></td>
                        <td align="right"><? echo number_format($dataArray[1][$com]);$printing_total+=$dataArray[1][$com]; ?></td>
                        <td align="right"><? echo number_format($dataArray[2][$com]);$emb_total+=$dataArray[2][$com]; ?></td>
                        <td align="right"><? echo number_format($dataArray[3][$com]);$wash_total+=$dataArray[3][$com]; ?></td>
                        <td align="right"><? echo number_format($dataArray[4][$com]);$special_total+=$dataArray[4][$com]; ?></td>
                         <td align="right"><? echo number_format($dataArray[5][$com]);$gd_total+=$dataArray[5][$com]; ?></td>
                 </tr>   
              <? $i++; } ?>
              <tfoot>
                    <tr>
                       <th align="right" colspan="2">Grand Total</th>
                       <th align="right"><? echo number_format($printing_total); ?></th>
                       <th align="right"><? echo number_format($emb_total); ?></th>
                       <th align="right"><? echo number_format($wash_total); ?></th>
                       <th align="right"><? echo number_format($special_total); ?></th>
                       <th align="right"><? echo number_format($gd_total); ?></th>
                    </tr>
              </tfoot>          
    	 </table>
     </div>
     
     <div style="width:450px; float:left; "> 
     	<? if($production_type!=2) //only for receive
		 { 
			?> 	
			<table width="450" cellspacing="0" border="1" class="rpt_table" rules="all" > 
            <label><h3>Balance</h3></label>
              <thead> 
                <tr>
                    <th>SL</th>
                    <th>Particulers</th>
                    <th>Print</th>
                    <th>Embroidery</th>
                    <th> Wash</th>
                    <th>Special Work </th>
                    <th>Gmts Dyeing </th>
                 </tr>
              </thead>  
 			<?
 				$sql_order = sql_select("SELECT 
						SUM(CASE WHEN b.emb_name=1 and b.emb_type!=0 THEN a.po_quantity*b.cons_dzn_gmts ELSE 0 END) AS print,  
						SUM(CASE WHEN b.emb_name=2 and b.emb_type!=0 THEN a.po_quantity*b.cons_dzn_gmts ELSE 0 END) AS emb,
						SUM(CASE WHEN b.emb_name=3 and b.emb_type!=0 THEN a.po_quantity*b.cons_dzn_gmts ELSE 0 END) AS wash,
						SUM(CASE WHEN b.emb_name=4 and b.emb_type!=0 THEN a.po_quantity*b.cons_dzn_gmts ELSE 0 END) AS special,
						SUM(CASE WHEN b.emb_name=5 and b.emb_type!=0 THEN a.po_quantity*b.cons_dzn_gmts ELSE 0 END) AS gmt_dyeing
   					FROM
						wo_po_break_down a, wo_pre_cost_embe_cost_dtls b 
					WHERE
						a.id in ($po_break_down_id) and a.job_no_mst=b.job_no");
				foreach($sql_order as $resultRow);	
						
				$sql_mst = sql_select("SELECT 
						SUM(CASE WHEN embel_name=1 THEN production_quantity ELSE 0 END) AS print_issue,  
						SUM(CASE WHEN embel_name=2 THEN production_quantity ELSE 0 END) AS emb_issue,
						SUM(CASE WHEN embel_name=3 THEN production_quantity ELSE 0 END) AS wash_issue,
						SUM(CASE WHEN embel_name=4 THEN production_quantity ELSE 0 END) AS special_issue,
						SUM(CASE WHEN embel_name=5 THEN production_quantity ELSE 0 END) AS gmt_issue
 					FROM
						pro_garments_production_mst
					WHERE
						po_break_down_id in ($po_break_down_id) and item_number_id in ($item_id) and production_type=2 $country_cond
					");		
				//echo $sql_mst;die;
				foreach($sql_mst as $resultMst);
				//echo $sql;die;
				$i=1;		
				 
					 ?>
						 <tr bgcolor="#E9F3FF">
								<td><? echo $i++; ?></td>
								<td>Req Qnty</td>
								<td align="right"><? echo number_format($resultRow[csf('print')]); ?></td>
								<td align="right"><? echo number_format($resultRow[csf('emb')]); ?></td>
								<td align="right"><? echo number_format($resultRow[csf('wash')]); ?></td>
								<td align="right"><? echo number_format($resultRow[csf('special')]); ?></td>
                                <td align="right"><? echo number_format($resultRow[csf('gmt_dyeing')]); ?></td>
						 </tr> 
                         <tr bgcolor="#FFFFFF">
								<td><? echo $i++; ?></td>
                                <td>Total Sent for</td>
 								<td align="right"><? echo number_format($resultMst[csf('print_issue')]); ?></td>
								<td align="right"><? echo number_format($resultMst[csf('emb_issue')]); ?></td>
								<td align="right"><? echo number_format($resultMst[csf('wash_issue')]); ?></td>
								<td align="right"><? echo number_format($resultMst[csf('special_issue')]); ?></td>
                                <td align="right"><? echo number_format($resultMst[csf('gmt_issue')]); ?></td>
						 </tr>
                         <tr bgcolor="#E9F3FF">
								<td><? echo $i++; ?></td>
                                <td>Total Receive</td>
 								<td align="right"><? echo number_format($printing_total); ?></td>
								<td align="right"><? echo number_format($emb_total); ?></td>
								<td align="right"><? echo number_format($wash_total); ?></td>
								<td align="right"><? echo number_format($special_total); ?></td>
                                <td align="right"><? echo number_format($gd_total); ?></td>
						 </tr>
                         <tr bgcolor="#FFFFFF">
								<td><? echo $i++; ?></td>
                                <td>Receive Balance</td>
                                <? $rcv_print_balance = $resultMst[csf('print_issue')]-$printing_total; ?>
 								<td align="right"><? echo number_format($rcv_print_balance); ?></td>
								<? $rcv_emb_balance = $resultMst[csf('emb_issue')]-$emb_total; ?>
 								<td align="right"><? echo number_format($rcv_emb_balance); ?></td>
								<? $rcv_wash_balance = $resultMst[csf('wash_issue')]-$wash_total; ?>
 								<td align="right"><? echo number_format($rcv_wash_balance); ?></td>
								<? $rcv_special_balance = $resultMst[csf('special_issue')]-$special_total;
									$rcv_gd_balance = $resultMst[csf('gmt_issue')]-$gd_total;
								 ?>
 								<td align="right"><? echo number_format($rcv_special_balance); ?></td>
                                <td align="right"><? echo number_format($rcv_gd_balance); ?></td>
						 </tr> 
                         <tr bgcolor="#E9F3FF">
								<td><? echo $i++; ?></td>
 								<td>Issue Balance</td>
 								<td align="right"><? echo  number_format($resultRow[csf('print')]-$resultMst[csf('print_issue')]); ?></td>
								<td align="right"><? echo  number_format($resultRow[csf('emb')]-$resultMst[csf('emb_issue')]); ?></td>
                                <td align="right"><? echo  number_format($resultRow[csf('wash')]-$resultMst[csf('wash_issue')]); ?></td>
                                <td align="right"><? echo  number_format($resultRow[csf('special')]-$resultMst[csf('special_issue')]); ?></td>
                                 <td align="right"><? echo  number_format($resultRow[csf('gmt_dyeing')]-$resultMst[csf('gmt_issue')]); ?></td>
 						 </tr>  
					 <? 
 				} 
			?>
            </table> 
        
     </div>
 </div>    
    
<?
  exit();
}
	
if($action=='date_wise_production_report_shipdate') 
{	
	extract($_REQUEST); 
 	echo load_html_head_contents("Remarks", "../../../", 1, 1,$unicode,'',''); 
	//echo $country_id;
	  if($country_id!="") $country_cond=" and country_id in ($country_id)";	
	  $item_id=implode(",",explode("*",$item_id));
	?>
    <div align="center">
        <fieldset style="width:480px">
        <legend>Cutting</legend>
            <? 
                 $tot_cutting_qty="";
                 $sql= "SELECT id,production_date,production_quantity,remarks from pro_garments_production_mst where po_break_down_id in ($po_break_down_id) and item_number_id in ($item_id) $country_cond and production_type='1'   and is_deleted=0 and status_active=1";
                // echo $sql;
				$total_cutting_arr=sql_select($sql);
				foreach( $total_cutting_arr as $cuttingQty)
				{
					$tot_cutting_qty+=$cuttingQty[csf("production_quantity")];
				}
                 echo  create_list_view ( "list_view_1", "Date,Production Qnty,Remarks", "100,120,280","470","220",1, $sql, "", "","", 1, '0,0,0', $arr, "production_date,production_quantity,remarks", "../requires/order_wise_production_report_controller", '','3,1,0');
                
            ?>
  			<table>
             	<tfoot>
                <tr>
                	<td width="80"></td>
                	<td width="100" align="right">Total = </td>
                    <td width="150" align="right"> <? echo $tot_cutting_qty; ?></td>
                    <td width="250"></td>
                 </tr>
                </tfoot>
             </table>
        </fieldset>
        
        <fieldset style="width:480px">
        <legend>Print/Embr Issue</legend>
            <? 
                  $tot_embrIssue_qty=""; 
                  $sql= "SELECT production_date,production_quantity,remarks from pro_garments_production_mst where po_break_down_id in ($po_break_down_id) and item_number_id in ($item_id) $country_cond and production_type='2' and is_deleted=0 and status_active=1";
                  $total_embrIssue_arr=sql_select($sql);
					foreach( $total_embrIssue_arr as $embrIssueQty)
					{
						$tot_embrIssue_qty+=$embrIssueQty[csf("production_quantity")];
					}
                 echo  create_list_view ( "list_view_2", "Date,Production Qnty,Remarks", "100,120,280","470","220",1, $sql, "", "","", 1, '0,0,0', $arr, "production_date,production_quantity,remarks", "../requires/order_wise_production_report_controller", '','3,1,0');
            ?>
            <table>
             	<tfoot>
                <tr>
                	<td width="80"></td>
                	<td width="100" align="right">Total = </td>
                    <td width="150" align="right"> <? echo $tot_embrIssue_qty; ?></td>
                    <td width="250"></td>
                 </tr>
                </tfoot>
             </table>
        </fieldset>
        
        <fieldset style="width:480px">
        <legend>Print/Embr Receive</legend>
            <? 
                 	$tot_embrRec_qty="";
                  	$sql= "SELECT production_date,production_quantity,remarks from pro_garments_production_mst where po_break_down_id in ($po_break_down_id) and item_number_id in ($item_id) $country_cond and production_type='3' and is_deleted=0 and status_active=1";
                    $total_embrRec_arr=sql_select($sql);
					foreach( $total_embrRec_arr as $embrRecQty)
					{
						$tot_embrRec_qty+=$embrRecQty[csf("production_quantity")];
					}
                 echo  create_list_view ( "list_view_3", "Date,Production Qnty,Remarks", "100,120,280","470","220",1, $sql, "", "","", 1, '0,0,0', $arr, "production_date,production_quantity,remarks", "../requires/order_wise_production_report_controller", '','3,1,0');
            ?>
             <table>
             	<tfoot>
                <tr>
                	<td width="80"></td>
                	<td width="100" align="right">Total = </td>
                    <td width="150" align="right"> <? echo $tot_embrRec_qty; ?></td>
                    <td width="250"></td>
                 </tr>
                </tfoot>
             </table>
        </fieldset>
        
        
        <fieldset style="width:480px">
        <legend>Sewing Input</legend>
            <? 
                 	$tot_sewingIn_qty="";
                  	$sql= "SELECT production_date,production_quantity,remarks from pro_garments_production_mst where po_break_down_id in ($po_break_down_id) and item_number_id in ($item_id) $country_cond and production_type='4' and is_deleted=0 and status_active=1";
                    $total_sewingIn_arr=sql_select($sql);
					foreach( $total_sewingIn_arr as $sewingInQty)
					{
						$tot_sewingIn_qty+=$sewingInQty[csf("production_quantity")];
					}
                 echo  create_list_view ( "list_view_4", "Date,Production Qnty,Remarks", "100,120,280","470","220",1, $sql, "", "","", 1, '0,0,0', $arr, "production_date,production_quantity,remarks", "../requires/order_wise_production_report_controller", '','3,1,0');
            ?>
             <table>
             	<tfoot>
                <tr>
                	<td width="80"></td>
                	<td width="100" align="right">Total = </td>
                    <td width="150" align="right"> <? echo $tot_sewingIn_qty; ?></td>
                    <td width="250"></td>
                 </tr>
                </tfoot>
             </table>
        </fieldset>
        
        
        <fieldset>
        <legend style="width:480px">Sewing Output</legend>
            <? 
                  $tot_sewingIn_qty="";
                  $sql= "SELECT production_date,production_quantity,remarks from pro_garments_production_mst where po_break_down_id in ($po_break_down_id) and item_number_id in ($item_id) $country_cond and production_type='5' and is_deleted=0 and status_active=1";
                   $total_sewingOut_arr=sql_select($sql);
					foreach( $total_sewingOut_arr as $sewingOutQty)
					{
						$tot_sewingIn_qty+=$sewingOutQty[csf("production_quantity")];
					}
                 echo  create_list_view ( "list_view_5", "Date,Production Qnty,Remarks", "100,120,280","470","220",1, $sql, "", "","", 1, '0,0,0', $arr, "production_date,production_quantity,remarks", "../requires/order_wise_production_report_controller", '','3,1,0');
            ?>
            <table>
             	<tfoot>
                <tr>
                	<td width="80"></td>
                	<td width="100" align="right">Total = </td>
                    <td width="150" align="right"> <? echo $tot_sewingIn_qty; ?></td>
                    <td width="250"></td>
                 </tr>
                </tfoot>
             </table>
        </fieldset>
        
        
        <fieldset style="width:480px">
        <legend>Finish Input</legend>
            <? 
                  $tot_finishIn_qty="";
                  $sql= "SELECT production_date,production_quantity,remarks from pro_garments_production_mst where po_break_down_id in ($po_break_down_id) and item_number_id in ($item_id) $country_cond and production_type='6' and is_deleted=0 and status_active=1";
                   $total_finishIn_arr=sql_select($sql);
					foreach( $total_finishIn_arr as $finishInQty)
					{
						$tot_sewingIn_qty+=$finishInQty[csf("production_quantity")];
					}
                 echo  create_list_view ( "list_view_6", "Date,Production Qnty,Remarks", "100,120,280","470","220",1, $sql, "", "","", 1, '0,0,0', $arr, "production_date,production_quantity,remarks", "../requires/order_wise_production_report_controller", '','3,1,0');
            ?>
             <table>
             	<tfoot>
                <tr>
                	<td width="80"></td>
                	<td width="100" align="right">Total = </td>
                    <td width="150" align="right"> <? echo $tot_finishIn_qty; ?></td>
                    <td width="250"></td>
                 </tr>
                </tfoot>
             </table>
        </fieldset>
        
        <fieldset style="width:480px">
        <legend>Finish Output</legend>
            <? 
                 $tot_finishOut_qty="";
                  $sql= "SELECT production_date,production_quantity,remarks from pro_garments_production_mst where po_break_down_id in ($po_break_down_id) and item_number_id in ($item_id) $country_cond and production_type='8' and is_deleted=0 and status_active=1";
                  $total_finishOut_arr=sql_select($sql);
					foreach( $total_finishOut_arr as $finishOutQty)
					{
						$tot_finishOut_qty+=$finishOutQty[csf("production_quantity")];
					}
                  echo  create_list_view ( "list_view_7", "Date,Production Qnty,Remarks", "100,120,280","470","220",1, $sql, "", "","", 1, '0,0,0', $arr, "production_date,production_quantity,remarks", "../requires/order_wise_production_report_controller", '','3,1,0');
            ?>
             <table>
             	<tfoot>
                <tr>
                	<td width="80"></td>
                	<td width="100" align="right">Total = </td>
                    <td width="150" align="right"> <? echo $tot_finishOut_qty; ?></td>
                    <td width="250"></td>
                 </tr>
                </tfoot>
             </table>
        </fieldset>
	</div>  
<?
exit();
}//end if 
//---- sewing input-4, iron input-7, finish-8, re_iron input-9-----------popup--------// 

if ($action=="reject_qty_country")
{
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,1,1);
	//echo $po_id;
	?>
     <div style="width:500px;" align="center"> 
     <div style="color:#FF0000; font-size:14px; font-weight:bold; float:left; width:500px">This Pop-Up Will Be Perfect When Order, Production and All Procedure Follow Color or Color & Size Level.</div>
       <table width="490" cellspacing="0" border="1" class="rpt_table" rules="all" > 
             <thead>
             	<tr>
                	<th colspan="5">Reject Qty Details</th>
                </tr>
                <tr>
                    <th width="30">SL</th>
                    <th width="110">Cutting Reject Qty</th>
                    <th width="110">Sewing Out Reject Qty</th>
                    <th width="110">Finish Reject Qty.</th>
                    <th width="110">Total Reject Qty.</th>
                 </tr>
              </thead>
              <tbody> 
			 <?
			 
		  if($country_cond!="") $country_cond=" and country_id in ($country_id)";	
		  $item_id=implode(",",explode("*",$item_id));
		   
			$sql_qry="Select sum(CASE WHEN production_type ='1' THEN reject_qnty ELSE 0 END) AS cutting_rej_qnty,
			 				sum(CASE WHEN production_type ='8' THEN reject_qnty ELSE 0 END) AS finish_rej_qnty,
							sum(CASE WHEN production_type ='5' THEN reject_qnty ELSE 0 END) AS sewingout_rej_qnty
							from pro_garments_production_mst 
							where po_break_down_id in ($po_id) and item_number_id in ($item_id) and status_active=1 and is_deleted=0 $country_cond group by po_break_down_id";
			//echo $sql_qry;
			$sql_result=sql_select($sql_qry);

			$i=1;	 
			foreach($sql_result as $row)
			{
			 	if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; 
			?>
                 <tr bgcolor="<? echo $bgcolor; ?>">
                    <td><? echo $i; ?></td>
                    <td align="right"><? echo $row[csf('cutting_rej_qnty')]; ?>&nbsp;</td>
                    <td align="right"><? echo $row[csf('sewingout_rej_qnty')]; ?>&nbsp;</td>
                    <td align="right"><? echo $row[csf('finish_rej_qnty')]; ?>&nbsp;</td>
                    <td align="right"><? $total_reject=$row[csf('cutting_rej_qnty')]+$row[csf('sewingout_rej_qnty')]+$row[csf('finish_rej_qnty')]; echo $total_reject; ?>&nbsp;</td>
                 </tr>   
             <? 
			  	$i++; 
			 } 
			 ?> 
             </tbody>
         </table>
     </div>    
	<?
	exit();
}

if ($action=="country_shipdate_display_qty") // popup
{
	 
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
		?>
	 <script>
	 var tableFilters_sewin = 
	{
		//col_0: "none",col_1:"none",display_all_text: " -- All --",
		col_operation: { 
			id: ["total_issue_qty"],
			col: [5],
			operation: ["sum"],
			write_method: ["innerHTML"]
		}
 	}
	</script>
    <?
	$country_ex=implode(',',explode('*',$country_id));
	if($country_ex=='') $country_cond=""; else $country_cond=" and country_id in ($country_ex) ";
	$item_id=implode(",",explode("*",$item_id));
	
 	?>
    <fieldset>
    <div style="margin-left:60px">
        <table width="500" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1" >
            <thead>
				<? if($production_type==2){ ?>
                
                    <tr>
                        <th width="50">Sl.</th>    
                        <th width="200">Print/ Emb. Issue Date</th>
                        <th width="">Print/ Emb. Issue Qnty</th>
                    </tr>
                
				<? } else if($production_type==3){ ?>
               
                    <tr>
                        <th width="50">Sl.</th>    
                        <th width="200">Print/ Emb. Receive Date</th>
                        <th width="">Print/ Emb. Receive Qnty</th>
                    </tr>
                
				<? } else if($production_type==4){ ?>
                
                    <tr>
                        <th width="30">Sl.</th>    
                        <th width="70">Sewing Date</th>
                        <th width="80">PO No</th>
                        <th width="80">Floor</th>
                        <th width="80">Sewing Line</th>
                        <th width="80">Sewing Qty</th>
                        <th width="">Source</th>
                    </tr>
                <? } else if($production_type==7){ ?>
                
                    <tr>
                        <th width="50">Sl.</th>    
                        <th width="200">Iron Output Date</th>
                        <th width="">Iron Output Qnty</th>
                    </tr>
                <? } else if($production_type==8){ ?>
                
                    <tr>
                        <th width="50">Sl.</th>    
                        <th width="200">Finish Date</th>
                        <th width="">Finish Qty</th>
                    </tr>
                <? } else if($production_type==9){ ?>
                    <tr>
                        <th width="50">Sl.</th>    
                        <th width="200">Iron Output Date</th>
                        <th width="">Re-Iron Output Qty</th>
                    </tr>
               <? } ?> 
            </thead>
        </table>
        <div style="max-height:425px; overflow-y:scroll; width:518px;" id="scroll_body">
            <table width="500" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_body" >
            <?
			 $po_array=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number");
			 $sewing_library=return_library_array( "select id,line_name from  lib_sewing_line", "id", "line_name");
			 $prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
			 $floor_library=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name"  ); 
             $total_quantity=0;
             $i=1;
			 $location="";$floor="";
			 if($dateOrLocWise==2 || $dateOrLocWise==4) // only for location floor and line wise
			 {
				 if($location_id!="") $location=" and location=$location_id";
				 if($floor_id!="") $floor=" and floor_id=$floor_id";
			 }
			 if ($production_type==9)
			 {
				 $sql=sql_select("select production_date, sum(re_production_qty) as production_quantity	  
				  from pro_garments_production_mst where po_break_down_id in ($po_break_down_id) and re_production_qty!=0 and item_number_id in ($item_id )and status_active=1 and is_deleted=0 and production_type=7 $location $floor $country_cond group by production_date");
			 }
			 else if ($production_type==4)
			 {
				 $sql=sql_select("select po_break_down_id,production_date,prod_reso_allo,floor_id,sewing_line,production_source,sum(production_quantity) as production_quantity
				  from pro_garments_production_mst where po_break_down_id in ($po_break_down_id) and item_number_id in  ($item_id )and status_active=1 and is_deleted=0 and production_type=$production_type $location $floor $country_cond group by po_break_down_id,production_date,prod_reso_allo,floor_id,sewing_line,production_source");
				  
			 }
			 else
			 {
				 $sql=sql_select("select production_date,sum(production_quantity) as production_quantity
				  from pro_garments_production_mst where po_break_down_id in ($po_break_down_id) and item_number_id in  ($item_id ) and status_active=1 and is_deleted=0 and production_type=$production_type $location $floor $country_cond group by production_date"); 
			 }
 
            foreach($sql as $resultRow)
            {
                 if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				 if ($production_type==4)
				 {
					 $sewing_line='';
					if($resultRow[csf('prod_reso_allo')]==1)
					{
						$line_number=explode(",",$prod_reso_arr[$resultRow[csf('sewing_line')]]);
						foreach($line_number as $val)
						{
							if($sewing_line=='') $sewing_line=$sewing_library[$val]; else $sewing_line.=",".$sewing_library[$val];
						}
					}
					else $sewing_line=$sewing_library[$resultRow[csf('sewing_line')]];
             	?>
                     <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                        <td width="30"><? echo $i;?></td>
                        <td width="70" align="center"><? echo change_date_format($resultRow[csf("production_date")]); ?></td>
                        <td width="80" align="center"><p><? echo $po_array[$resultRow[csf("po_break_down_id")]]; ?></p></td>
                        <td width="80" align="center"><p><? echo $floor_library[$resultRow[csf("floor_id")]]; ?></p></td>
                        <td width="80" align="center"><? echo $sewing_line; ?></td>
                        <td width="80" align="right"><? echo number_format($resultRow[csf("production_quantity")],0); ?>&nbsp;</td>
                        <td><? echo $knitting_source[$resultRow[csf("production_source")]]; ?></td>
                     </tr>	
                 <?	
				 }
				 else
				 {
             	?>
                 <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                    <td width="50"><? echo $i;?></td>
                    <td width="200" align="right"><? echo change_date_format($resultRow[csf("production_date")]); ?></td>
                    <td width="" align="right"><? echo number_format($resultRow[csf("production_quantity")],0); ?>&nbsp;</td>
                 </tr>	
                 <?	
				 }
                    $total_quantity+=$resultRow[csf("production_quantity")];
            	 	$i++;
            
        }//end foreach 1st
        ?>
        </table>
        <table width="500" cellspacing="0" border="1" class="tbl_bottom" rules="all" id="body_bottom" >
        	<?
			if ($production_type==4)
			{
			?>
                 <tr> 
                    <td width="30">&nbsp;</td>
                    <td width="70">&nbsp;</td>
                    <td width="80">&nbsp;</td>
                    <td width="80">&nbsp;</td>
                    <td width="80">Total</td> 
                    <td width="80" id="total_issue_qty"><? echo number_format($total_quantity,0); ?>&nbsp;</td>
                    <td>&nbsp;</td>
                 </tr>
             <?
			}
			else
			{
			?>
                 <tr> 
                    <td width="50">&nbsp;</td> 
                    <td width="200">Total</td> 
                    <td><? echo number_format($total_quantity,0); ?>&nbsp;</td>
                 </tr>
             <?
			}
			 ?>
         </table>
       </div>
     </div>
     </fieldset>
      <script>
	var action=<? echo $production_type; ?>;
	
	//if (action==4) setFilterGrid("tableFilters_sewin",-1);
	if(action==4) setFilterGrid("table_body",-1,tableFilters_sewin);
	
	</script>
    <?
 	
 exit();
 
}

if ($action=="country_shipdate_wise_qty") 
{
	 
 	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,1,1);
		?>
	 <script>
	 var tableFilters_sewout = 
	{
		//col_0: "none",col_1:"none",display_all_text: " -- All --",
		col_operation: { 
			id: ["total_sew_qty"],
			col: [4],
			operation: ["sum"],
			write_method: ["innerHTML"]
		}
 	}
	</script>
    <?
	
	if($country_id=='') $country_cond=""; else 
	{
		$country_id=implode(",",explode("*",$country_id));
		$country_cond=" and country_id in ($country_id)";
	}
	$item_id=implode(",",explode("*",$item_id));
 	?>

    <fieldset>
    <div style="margin-left:50px">
        <table width="620" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1" >
            <thead>
                 <? if($production_type==1){ ?>
                    <tr>
                        <th width="50">Sl.</th>    
                        <th width="100">Cutting Date</th>
                        <th width="160">Cutt. Qty(In-house)</th>
                        <th width="160">Cutt. Qty(Out-bound)</th>
                        <th width="">Cutting Company</th>
                 	</tr>
				<? } else if($production_type==5){ ?>
                    <tr>
                        <th width="30">Sl.</th>    
                        <th width="80">Output Date</th>
                        <th width="80">PO No</th>
                        <th width="80">Sewing Line</th>
                        <th width="80">Sew.Qty</th>
                        <th width="100">Source</th>
                        <th width="">Sewing Company</th>
                    </tr>
 				<? } ?>
            </thead>
        </table>
        <div style="max-height:425px; overflow-y:scroll; width:638px;" id="scroll_body">
            <table cellspacing="0" border="1" class="rpt_table" width="620" rules="all" id="table_body" >
            <?
             $total_in_quantity=0;$total_out_quantity=0;
             $i=1;
 			 $company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
 			 $supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id", "supplier_name" );
			 $po_array=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number");
			 $sewing_library=return_library_array( "select id,line_name from  lib_sewing_line", "id", "line_name");
			 $prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
			
			 if($production_type==5)
			 {
				 $sql=sql_select("select po_break_down_id,production_date,prod_reso_allo,sewing_line,production_source,serving_company,
					  SUM(production_quantity) as production_quantity
					  from pro_garments_production_mst where po_break_down_id in ($po_break_down_id) and item_number_id in ($item_id) and production_type=$production_type and status_active=1  $country_cond group by serving_company,production_date,po_break_down_id,prod_reso_allo,sewing_line,production_source"); 
			 }
			 else
			 {
				 $sql=sql_select("select po_break_down_id,production_date,production_source,serving_company,
					  SUM(CASE WHEN production_source=1 THEN production_quantity ELSE 0 END) as in_house_cut_qnty,
					  SUM(CASE WHEN production_source=3 THEN production_quantity ELSE 0 END) as out_bound_cut_qnty
					  from pro_garments_production_mst where po_break_down_id in ($po_break_down_id) and item_number_id in ($item_id) and production_type=$production_type and status_active=1  $country_cond group by po_break_down_id,serving_company,production_date,production_source");
			 }
				  
            foreach($sql as $resultRow)
            {
                 if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				 if($production_type==5)
				 {
					$sewing_line='';
					if($resultRow[csf('prod_reso_allo')]==1)
					{
						$line_number=explode(",",$prod_reso_arr[$resultRow[csf('sewing_line')]]);
						foreach($line_number as $val)
						{
							if($sewing_line=='') $sewing_line=$sewing_library[$val]; else $sewing_line.=",".$sewing_library[$val];
						}
					}
					else $sewing_line=$sewing_library[$resultRow[csf('sewing_line')]];
             	?>
                 <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                    <td width="30"><? echo $i;?></td>
                    <td width="80"><a href="##" onclick="openmypage('<? echo $po_break_down_id ?>','<? echo $item_id ?>','<? echo $action ?>','<? echo $location_id ?>','<? echo $floor_id ?>','<? echo $dateOrLocWise ?>','<? echo $country_id ?>','<? echo $resultRow[csf("production_date")]; ?>','challanPopup')"><? echo change_date_format($resultRow[csf("production_date")]); ?></a></td>
                    <td width="80"><p><? echo $po_array[$resultRow[csf("po_break_down_id")]]; ?></p></td>
                    <td width="80"><? echo $sewing_line; ?></td>
                    <td width="80" align="right"><? echo number_format($resultRow[csf("production_quantity")]); ?>&nbsp;</td>
                    <td width="100"><? echo $knitting_source[$resultRow[csf("production_source")]]; ?></td>
                    <?
                    	$source= $resultRow[csf('production_source')];
					    if($source==3)
						{
							$serving_company= $supplier_library[$resultRow[csf('serving_company')]];
						}
						else
						{
							$serving_company= $company_library[$resultRow[csf('serving_company')]];
						}
					?>
                    <td width=""><p><? echo $serving_company; ?></p></td>
                 </tr>	
                 <?	
				 }
				 else
				 {
             	?>
                 <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                    <td width="50"><? echo $i;?></td>
                    <td width="100"><a href="##" onclick="openmypage('<? echo $po_break_down_id ?>','<? echo $item_id ?>','<? echo $action ?>','<? echo $location_id ?>','<? echo $floor_id ?>','<? echo $dateOrLocWise ?>','<? echo $country_id ?>','<? echo $resultRow[csf("production_date")]; ?>','challanPopup')"><? echo change_date_format($resultRow[csf("production_date")]); ?></a></td>
                    <td width="160" align="right"><? echo number_format($resultRow[csf("in_house_cut_qnty")]); ?></td>
                    <td width="160" align="right"><? echo number_format($resultRow[csf("out_bound_cut_qnty")]); ?></td>
                    <?
                    	$source= $resultRow[csf('production_source')];
					    if($source==3)
						{
							$serving_company= $supplier_library[$resultRow[csf('serving_company')]];
						}
						else
						{
							$serving_company= $company_library[$resultRow[csf('serving_company')]];
						}
					?>
                    <td width=""><p><? echo $serving_company; ?></p></td>
                 </tr>	
                 <?	
				 }
				 	
				$total_sewing_quantity+=$resultRow[csf("production_quantity")];
				$total_in_quantity+=$resultRow[csf("in_house_cut_qnty")];
				$total_out_quantity+=$resultRow[csf("out_bound_cut_qnty")];
				$i++;
			}//end foreach 1st
			  ?>
           </table>
           <table width="620" cellspacing="0" border="1" class="tbl_bottom" rules="all" id="body_bottom" >
        	<?	
			if($production_type==5)
			{
			?>
            	<tfoot class="tbl_bottom">
                     <tr> 
                        <td width="30">&nbsp;</td> 
                        <td width="80">&nbsp;</td> 
                        <td width="80">&nbsp;</td> 
                        <td width="80">Total</td> 
                        <td width="80" id="total_sew_qty"><? echo number_format($total_sewing_quantity); ?>&nbsp;</td>
                        <td width="100">&nbsp;</td>
                        <td>&nbsp;</td> 
                     </tr>
                 </tfoot>
			 <?
			 }
			 else
			 {
			?>
            	<tfoot class="tbl_bottom">
                     <tr> 
                        <td width="50">&nbsp;</td> 
                        <td width="100">Total</td> 
                        <td width="160"><? echo number_format($total_in_quantity); ?> </td>
                        <td width="160"><? echo number_format($total_out_quantity); ?></td>
                        <td width="">&nbsp;</td> 
                     </tr>
                 </tfoot>
			 <?
			 }
			 ?>
			</table>
		</div>
	</div>
	</fieldset>
       <script>
	var action=<? echo $production_type; ?>;
	
	//if (action==4) setFilterGrid("tableFilters_sewin",-1);
	if(action==5) setFilterGrid("table_body",-1,tableFilters_sewout);
	
	</script>
    <?
 exit();
}

if ($action==1 || $action==5) 
{
 	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,1,1);
	
	if($country_id=='') $country_cond=""; else $country_cond=" and country_id='$country_id'";
 	
    if ($action==5)
	{
	?>
		 <script>
         var tableFilters_sewout = 
        {
            //col_0: "none",col_1:"none",display_all_text: " -- All --",
            col_operation: { 
                id: ["total_sew_in","total_sew_out"],
                col: [4,5],
                operation: ["sum","sum"],
                write_method: ["innerHTML","innerHTML"]
            }
        }
        </script>
    
    <?
	}
	else 
	{
	?>
    	 <script>
         var tableFilters_sewout = 
        {
            //col_0: "none",col_1:"none",display_all_text: " -- All --",
            col_operation: { 
                id: ["total_sew_qty_in"],
                col: [2],
                operation: ["sum"],
                write_method: ["innerHTML"]
            }
        }
        </script>
    
    <?	
	}
	?>
     <div id="data_panel" align="center" style="width:100%">
         <script>
			function new_window()
			{
				document.getElementById('scroll_body').style.overflow="auto";
				document.getElementById('scroll_body').style.maxHeight="none";
				$('#table_body tr:first').hide();
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write(document.getElementById('details_reports').innerHTML);
				d.close();
				$('#table_body tr:first').show();
				document.getElementById('scroll_body').style.overflowY="scroll";
				document.getElementById('scroll_body').style.maxHeight="none";
			}
         </script>
 	<input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
   </div>
    <script>
		
		function openmypage(po_break_down_id,item_id,prod_type,location_id,floor_id,dateOrLocWise,country_id,prod_date,action)
		{
			var popupWidth = "width=550px,height=320px,";	
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'order_wise_production_report_controller.php?po_break_down_id='+po_break_down_id+'&item_id='+item_id+'&action='+action+'&location_id='+location_id+'&floor_id='+floor_id+'&dateOrLocWise='+dateOrLocWise+'&country_id='+country_id+'&prod_date='+prod_date+'&prod_type='+prod_type, 'Production Quantity', popupWidth+'center=1,resize=0,scrolling=0','../../');
		}
	</script>
    <div style="color:#FF0000; font-size:14px; font-weight:bold; float:left; width:635px">This Pop-Up Will Be Perfect When Order, Production and All Procedure Follow Color or Color & Size Level.</div>
    <fieldset>
    
    <div style="" align="center" id="details_reports"> 
    
         <table id="tbl_id" class="rpt_table" width="" border="1" rules="all" >
            <thead>
                <tr>
                    <th width="100">Buyer</th>
                    <th width="100">Job Number</th>
                    <th width="100">Style Name</th>
                    <th width="200">Order Number</th>
                    <th width="100">Ship Date</th>
                    <th width="100">Item Name</th>
                    <th width="60">Order Qty.</th>
                </tr>
            </thead>
            <?
                $buyer_short_library=return_library_array( "select id,short_name from lib_buyer", "id", "short_name"  );
                if($db_type==0)
                {
                    $sql = "select a.job_no_mst,group_concat(distinct(a.po_number)) as po_number,a.pub_shipment_date, sum(a.po_quantity) as po_quantity,a.packing,b.set_break_down, b.company_name, b.order_uom, b.buyer_name, b.style_ref_no,c.set_item_ratio 
                        from wo_po_break_down a, wo_po_details_master b, wo_po_details_mas_set_details c 
                        where a.job_no_mst=b.job_no and b.job_no=c.job_no and a.id in ($po_break_down_id) and c.gmts_item_id=$item_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
                }
                else
                {
                    $sql = "select a.job_no_mst, LISTAGG(a.po_number, ',') WITHIN GROUP (ORDER BY a.id) as po_number,max(a.pub_shipment_date) as pub_shipment_date, sum(a.po_quantity) as po_quantity,a.packing,b.set_break_down, b.company_name, b.order_uom, b.buyer_name, b.style_ref_no,c.set_item_ratio 
                        from wo_po_break_down a, wo_po_details_master b, wo_po_details_mas_set_details c 
                        where a.job_no_mst=b.job_no and b.job_no=c.job_no and a.id in ($po_break_down_id) and c.gmts_item_id=$item_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.job_no_mst, a.packing,b.set_break_down, b.company_name, b.order_uom, b.buyer_name, b.style_ref_no,c.set_item_ratio";
                }
                //echo $sql;die;
                $resultRow=sql_select($sql);
                    
                $cons_embr=return_field_value("sum(cons_dzn_gmts) as cons_dzn_gmts","wo_pre_cost_embe_cost_dtls","job_no='".$resultRow[0][csf("job_no_mst")]."' and status_active=1 and is_deleted=0","cons_dzn_gmts");
                
            ?> 
            <tr>
                <td><? echo $buyer_short_library[$resultRow[0][csf("buyer_name")]]; ?></td>
                <td><p><? echo $resultRow[0][csf("job_no_mst")]; ?></p></td>
                <td><p><? echo $resultRow[0][csf("style_ref_no")]; ?></p></td>
                <td><p><? echo implode(",",array_unique(explode(",",$resultRow[0][csf("po_number")]))); ?></p></td>
                <td><? echo change_date_format($resultRow[0][csf("pub_shipment_date")]); ?></td>
                <td><? echo $garments_item[$item_id]; ?></td>
                <td><? echo $resultRow[0][csf("po_quantity")]*$resultRow[0][csf("set_item_ratio")]; ?></td>
            </tr>
             <?
			 if($action==5)
			 {
				 $prod_sewing_sql=sql_select("SELECT sum(alter_qnty) as alter_qnty, sum(reject_qnty) as reject_qnty from pro_garments_production_mst where production_type=$action and po_break_down_id in ($po_break_down_id) and is_deleted=0 and status_active=1");
				 foreach($prod_sewing_sql as $sewingRow);
				?> 	
				<tr>
					<td colspan="2">Total Alter Sewing Qty : <b><? echo $sewingRow[csf("alter_qnty")]; ?></b></td>
					<td colspan="2">Total Reject Sewing Qty : <b><? echo $sewingRow[csf("reject_qnty")]; ?></b></td>
					<td colspan="2">Pack Assortment: <b><? echo $packing[$resultRow[csf("packing")]]; ?></b></td>
				</tr>
				<?
			}
			?>
        </table>
    	<br/>
        <table width="720" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1" >
            <thead>
                 <? if($action==1){ ?>
                 	<tr style="font-size:12px">
                        <th width="50" rowspan="2">Sl.</th>
                        <th width="100" rowspan="2">Cutting Date</th>
                        <th colspan="2">Cutting Qty</th>
                        <th width="150" rowspan="2">Cutting Company</th>
                        <th width="" rowspan="2">Location</th>
                    </tr>
                    <tr style="font-size:12px">
                        <th width="100">In-house</th>
                        <th width="100">Out-bound</th>
                 	</tr>
				<? } else if($action==5){ ?>
                    <tr style="font-size:12px">
                        <th width="30" rowspan="2">Sl.</th>    
                        <th width="80" rowspan="2">Sewing Out Date</th>
                        <th width="80" rowspan="2">Floor</th>
                        <th width="80" rowspan="2">Sewing Line</th>
                        <th colspan="2">Sewing Qty</th>
                        <th width="120" rowspan="2">Sewing Company</th>
                        <th width="" rowspan="2">Location</th>
                    </tr>
                    <tr style="font-size:12px">
                        <th width="100">In-house</th>
                        <th width="100">Outside</th>
                    </tr>
 				<? } ?>
            </thead>
        </table>
        <div style="max-height:300px; overflow-y:scroll; width:718px;" id="scroll_body">
            <table cellspacing="0" border="1" class="rpt_table" width="700" rules="all" id="table_body" >
            <?
             $total_in_quantity=0;$total_out_quantity=0;
             $i=1;
 			 $company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
 			 $supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id", "supplier_name" );
			 $po_array=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number");
			 $sewing_library=return_library_array( "select id,line_name from  lib_sewing_line", "id", "line_name");
			 $floor_arr=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name");
			 $prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
			 $location_library=return_library_array( "select id,location_name from  lib_location", "id", "location_name"  );
			
			 $location="";$floor="";
			 if($dateOrLocWise==2 || $dateOrLocWise==4) // only for location floor and line wise
			 {
				 if($location_id!="") $location=" and location=$location_id";
				 if($floor_id!="") $floor=" and floor_id=$floor_id";
			 }
			 if($action==5)
			 {
				 $sql_prod=sql_select("select po_break_down_id, production_date, prod_reso_allo, sewing_line, production_source, serving_company, location, floor_id,
					  SUM(case when production_source=1 then production_quantity else 0 end) as in_house_qnty,
					  SUM(case when production_source=3 then production_quantity else 0 end) as out_bound_qnty
					  from pro_garments_production_mst where po_break_down_id in ($po_break_down_id) and item_number_id=$item_id and production_type=$action and status_active=1 $location $floor $country_cond group by serving_company, production_date, po_break_down_id, prod_reso_allo, sewing_line, production_source, location, floor_id  order by production_date"); 
					  
			 }
			 else
			 {
				 $sql_prod=sql_select("select po_break_down_id, production_date, production_source, serving_company, location,
					  SUM(CASE WHEN production_source=1 THEN production_quantity ELSE 0 END) as in_house_cut_qnty,
					  SUM(CASE WHEN production_source=3 THEN production_quantity ELSE 0 END) as out_bound_cut_qnty
					  from pro_garments_production_mst where po_break_down_id in ($po_break_down_id) and item_number_id=$item_id and production_type=$action and status_active=1 $location $floor $country_cond group by po_break_down_id,serving_company,location,production_date,production_source  order by production_date");
			 }
			 
			 //echo $sql_prod;
			$total_in_quantity=0; $total_out_quantity=0;
            foreach($sql_prod as $row)
            {
                 if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				 if($action==5)
				 {
					$sewing_line='';
					if($row[csf('prod_reso_allo')]==1)
					{
						$line_number=explode(",",$prod_reso_arr[$row[csf('sewing_line')]]);
						foreach($line_number as $val)
						{
							if($sewing_line=='') $sewing_line=$sewing_library[$val]; else $sewing_line.=",".$sewing_library[$val];
						}
					}
					else $sewing_line=$sewing_library[$row[csf('sewing_line')]];
             	?>
                 <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>" style="font-size:12px">
                    <td width="30" align="center"><? echo $i;?></td>
                    <td width="80" align="center"><a href="##" onclick="openmypage('<? echo $po_break_down_id ?>','<? echo $item_id ?>','<? echo $action ?>','<? echo $location_id ?>','<? echo $floor_id ?>','<? echo $dateOrLocWise ?>','<? echo $country_id ?>','<? echo $row[csf("production_date")]; ?>','challanPopup')"><? echo change_date_format($row[csf("production_date")]); ?></a></td>
                    <td width="80" align="center"><? echo $floor_arr[$row[csf("floor_id")]]; ?></td>
                    <td width="80" align="center"><? echo $sewing_line; ?></td>
                    <td width="100" align="right"><? echo number_format($row[csf("in_house_qnty")],0); ?>&nbsp;</td>
                    <td width="100" align="right"><? echo number_format($row[csf("out_bound_qnty")],0); ?>&nbsp;</td>
                    <td width="120"><div style="word-wrap:break-word; width:120px">
					<?
                    	$source= $row[csf('production_source')];
					    if($source==3)
						{
							$serving_company= $supplier_library[$row[csf('serving_company')]];
						}
						else
						{
							$serving_company= $company_library[$row[csf('serving_company')]];
						}
						echo $serving_company;
					?>
                    &nbsp;</div></td>
                    <td><p><? echo $location_library[$row[csf('location')]]; ?></p></td>
                 </tr>	
                 <?	
				 //echo $resultRow[csf("in_house_cut_qnty")].'==<br>';
				$total_in_quantity+=$row[csf("in_house_qnty")];
				$total_out_quantity+=$row[csf("out_bound_qnty")];
				 }
				 else
				 {
             	?>
                 <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>" style="font-size:12px">
                    <td width="50" align="center"><? echo $i;?></td>
                    <td width="100" align="center"><a href="##" onclick="openmypage('<? echo $po_break_down_id ?>','<? echo $item_id ?>','<? echo $action ?>','<? echo $location_id ?>','<? echo $floor_id ?>','<? echo $dateOrLocWise ?>','<? echo $country_id ?>','<? echo $row[csf("production_date")]; ?>','challanPopup')"><? echo change_date_format($row[csf("production_date")]); ?></a></td>
                    <td width="100" align="right"><? echo number_format($row[csf("in_house_cut_qnty")]); ?></td>
                    <td width="100" align="right"><? echo number_format($row[csf("out_bound_cut_qnty")]); ?></td>
                    <?
                    	$source= $row[csf('production_source')];
					    if($source==3)
						{
							$serving_company= $supplier_library[$row[csf('serving_company')]];
						}
						else
						{
							$serving_company= $company_library[$row[csf('serving_company')]];
						}
					?>
                    <td width="150"><p><? echo $serving_company; ?></p></td>
                    <td width=""><p><? echo $location_library[$row[csf('location')]]; ?></p></td>
                 </tr>	
                 <?	
				 $total_in_quantity+=$row[csf("in_house_cut_qnty")];
				 $total_out_quantity+=$row[csf("out_bound_cut_qnty")];
				 }
				$i++;
			}//end foreach 1st
			?>
            </table>
            <table cellspacing="0" cellpadding="0" border="1" class="rpt_table" width="700" rules="all">
            <?
			if($action==5)
			{
			?>
            	<tfoot class="tbl_bottom">
                     <tr> 
                        <td width="30">&nbsp;</td> 
                        <td width="80">&nbsp;</td> 
                        <td width="80">&nbsp;</td> 
                        <td width="80">Total</td> 
                        <td width="100" id="total_sew_in" align="right"><? echo number_format($total_in_quantity); ?></td> 
                        <td width="100" id="total_sew_out" align="right"><? echo number_format($total_out_quantity); ?>&nbsp;</td>
                        <td width="120">&nbsp;</td>
                        <td>&nbsp;</td> 
                     </tr>
                 </tfoot>
			 <?
			 }
			 else
			 {
			?>
            	<tfoot class="tbl_bottom">
                     <tr> 
                        <td width="50">&nbsp;</td> 
                        <td width="100">Total</td> 
                        <td width="100" id="total_sew_qty_in" align="right"><? echo number_format($total_in_quantity); ?></td>
                        <td width="100" id="total_sew_qty_out" align="right"><? echo number_format($total_out_quantity); ?></td>
                        <td width="150">&nbsp;</td> 
                        <td width="">&nbsp;</td> 
                     </tr>
                 </tfoot>
			 <?
			 }
			 ?>
		</table>
		</div>
	</div>
</fieldset>
    <script>
	/*var action=<? //echo $action; ?>;
	if(action==5) setFilterGrid("table_body",-1,tableFilters_sewout);
	else */setFilterGrid("table_body",-1,tableFilters_sewout);
	
	</script>
    <?
 exit();
 
}
//---- sewing input-4, iron input-7, finish-8, re_iron input-9-----------popup--------// 
if ($action==4 || $action==7 || $action==8 || $action==9) // popup
{
	 
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	echo $action.'sddddd';
	if ($action==4)
	{
	?>
		 <script>
         var tableFilters_sewin = 
        {
            //col_0: "none",col_1:"none",display_all_text: " -- All --",
            col_operation: { 
                id: ["total_issue_qty","total_issue_qty_out"],
                col: [4,5],
                operation: ["sum","sum"],
                write_method: ["innerHTML","innerHTML"]
            }
        }
        </script>
    
    <?
	}
	else if ($action==7 || $action==8)
	{ ?>
    
		 <script>
         var tableFilters_sewin = 
        {
            //col_0: "none",col_1:"none",display_all_text: " -- All --",
            col_operation: { 
                id: ["total_issue_qty","total_issue_qty_out"],
                col: [2,3],
                operation: ["sum","sum"],
                write_method: ["innerHTML","innerHTML"]
            }
        }
        </script>
<?		
	}
	else 
	{
	?>
    	 <script>
         var tableFilters_sewin = 
        {
            //col_0: "none",col_1:"none",display_all_text: " -- All --",
            col_operation: { 
                id: ["total_issue_qty"],
                col: [2],
                operation: ["sum"],
                write_method: ["innerHTML"]
            }
        }
        </script>
    
    <?	
	}
	
	?>
    
    <div id="data_panel" align="center" style="width:100%">
         <script>
			function new_window()
			{
				document.getElementById('scroll_body').style.overflow="auto";
				document.getElementById('scroll_body').style.maxHeight="none";
				$('#table_body tr:first').hide();
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write(document.getElementById('details_reports').innerHTML);
				d.close();
				$('#table_body tr:first').show();
				document.getElementById('scroll_body').style.overflowY="scroll";
				document.getElementById('scroll_body').style.maxHeight="none";
			}
         </script>
 	 <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
	 </div> 
    
    
    <?
	if($country_id=='') $country_cond=""; else $country_cond=" and country_id='$country_id'";
	if($country_id=='') $country_cond1=""; else $country_cond1=" and a.country_id='$country_id'";

	
 	?>
     <div style="color:#FF0000; font-size:14px; font-weight:bold; float:left; width:635px">This Pop-Up Will Be Perfect When Order, Production and All Procedure Follow Color or Color & Size Level.</div>
    <fieldset>
    <div style="margin-left:60px" id="details_reports">
            <table id="tbl_id" class="rpt_table" width="" border="1" rules="all" >
                <thead>
                    <tr>
                        <th width="100">Buyer</th>
                        <th width="100">Job Number</th>
                        <th width="100">Style Name</th>
                        <th width="300">Order Number</th>
                        <th width="100">Ship Date</th>
                        <th width="100">Item Name</th>
                        <th width="100">Order Qty.</th>
                    </tr>
                </thead>
                <?
                    $buyer_short_library=return_library_array( "select id,short_name from lib_buyer", "id", "short_name"  );
                    if($db_type==0)
                    {
                        $sql = "select a.job_no_mst,group_concat(distinct(a.po_number)) as po_number,a.pub_shipment_date, sum(a.po_quantity) as po_quantity,a.packing,b.set_break_down, b.company_name, b.order_uom, b.buyer_name, b.style_ref_no,c.set_item_ratio 
                            from wo_po_break_down a, wo_po_details_master b, wo_po_details_mas_set_details c 
                            where a.job_no_mst=b.job_no and b.job_no=c.job_no and a.id in ($po_break_down_id) and c.gmts_item_id=$item_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
                    }
                    else
                    {
                        $sql = "select a.job_no_mst, LISTAGG(a.po_number, ',') WITHIN GROUP (ORDER BY a.id) as po_number,max(a.pub_shipment_date) as pub_shipment_date, sum(a.po_quantity) as po_quantity,a.packing,b.set_break_down, b.company_name, b.order_uom, b.buyer_name, b.style_ref_no,c.set_item_ratio 
                            from wo_po_break_down a, wo_po_details_master b, wo_po_details_mas_set_details c 
                            where a.job_no_mst=b.job_no and b.job_no=c.job_no and a.id in ($po_break_down_id) and c.gmts_item_id=$item_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.job_no_mst, a.packing,b.set_break_down, b.company_name, b.order_uom, b.buyer_name, b.style_ref_no,c.set_item_ratio";
                    }
                    //echo $sql;die;
                    $resultRow=sql_select($sql);
                        
                    $cons_embr=return_field_value("sum(cons_dzn_gmts) as cons_dzn_gmts","wo_pre_cost_embe_cost_dtls","job_no='".$resultRow[0][csf("job_no_mst")]."' and status_active=1 and is_deleted=0","cons_dzn_gmts");
                    
                ?> 
                <tr>
                    <td><? echo $buyer_short_library[$resultRow[0][csf("buyer_name")]]; ?></td>
                    <td><p><? echo $resultRow[0][csf("job_no_mst")]; ?></p></td>
                    <td><p><? echo $resultRow[0][csf("style_ref_no")]; ?></p></td>
                    <td><p><? echo implode(",",array_unique(explode(",",$resultRow[0][csf("po_number")]))); ?></p></td>
                    <td><? echo change_date_format($resultRow[0][csf("pub_shipment_date")]); ?></td>
                    <td><? echo $garments_item[$item_id]; ?></td>
                    <td><? echo $resultRow[0][csf("po_quantity")]*$resultRow[0][csf("set_item_ratio")]; ?></td>
                </tr>
                 <?
                 /*$prod_sewing_sql=sql_select("SELECT sum(alter_qnty) as alter_qnty, sum(reject_qnty) as reject_qnty from pro_garments_production_mst where production_type=5 and po_break_down_id in ($po_break_down_id) and is_deleted=0 and status_active=1");
                 foreach($prod_sewing_sql as $sewingRow);*/
                ?> 	
                <!--<tr>
                    <td colspan="2">Total Alter Sewing Qty : <b><?// echo $sewingRow[csf("alter_qnty")]; ?></b></td>
                    <td colspan="2">Total Reject Sewing Qty : <b><?// echo $sewingRow[csf("reject_qnty")]; ?></b></td>
                    <td colspan="2">Pack Assortment: <b><?// echo $packing[$resultRow[csf("packing")]]; ?></b></td>
                </tr>-->
            </table>
        
            <br/>
            <table width="700" cellspacing="0" border="1" class="rpt_table" rules="all" >
                <thead>
                    <? if($action==2){ ?>
                    
                        <tr style="font-size:12px">
                            <th width="50">Sl.</th>    
                            <th width="200">Print/ Emb. Issue Date</th>
                            <th width="">Print/ Emb. Issue Qnty</th>
                        </tr>
                    
                    <? } else if($action==3){ ?>
                   
                        <tr style="font-size:12px">
                            <th width="50">Sl.</th>    
                            <th width="200">Print/ Emb. Receive Date</th>
                            <th width="">Print/ Emb. Receive Qnty</th>
                        </tr>
                    
                    <? } else if($action==4){ ?>
                    
                        <tr style="font-size:12px">
                            <th width="30" rowspan="2">Sl.</th>    
                            <th width="70" rowspan="2">Sewing Date</th>
                            <th width="70" rowspan="2">Floor</th>
                            <th width="70" rowspan="2">Sewing Line</th>
                            <th  colspan="2">Sewing Qty</th>
                            <th width="140" rowspan="2">Sewing Company</th>
                            <th rowspan="2">Location</th>
                        </tr>
                        <tr style="font-size:12px">
                            <th width="80">In-house</th>
                            <th width="80">Outside</th>
                        </tr>
                    <? } else if($action==7){ ?>
                    	<tr style="font-size:12px">
                            <th width="50" rowspan="2">Sl.</th>    
                            <th width="100" rowspan="2">Iron Date</th>
                            <th  colspan="2">Iron Qty</th>
                            <th width="140" rowspan="2">Iron Company</th>
                            <th rowspan="2">Location</th>
                        </tr>
                        <tr style="font-size:12px">
                            <th width="100">In-house</th>
                            <th width="100">Outside</th>
                        
                    <? } else if($action==8){ ?>
                    
                        <tr style="font-size:12px">
                            <th width="50" rowspan="2">Sl.</th>    
                            <th width="100" rowspan="2">Finish Date</th>
                            <th  colspan="2">Finish Qty</th>
                            <th width="140" rowspan="2">Finish Company</th>
                            <th rowspan="2">Location</th>
                        </tr>
                        <tr style="font-size:12px">
                            <th width="100">In-house</th>
                            <th width="100">Outside</th>
                        </tr>
                    <? } else if($action==9){ ?>
                        <tr style="font-size:12px">
                            <th width="50">Sl.</th>    
                            <th width="200">Iron Output Date</th>
                            <th width="">Re-Iron Output Qty</th>
                        </tr>
                   <? } ?> 
                </thead>
            </table>
        <div style="max-height:425px; overflow-y:scroll; width:720px;" id="scroll_body">
            <table width="700" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_body" >
            <?
				//$po_array=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number");
				$sewing_library=return_library_array( "select id,line_name from  lib_sewing_line", "id", "line_name");
				$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
				$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name" );
				$supplier_library=return_library_array( "select id,short_name from  lib_supplier", "id", "short_name" );
				$location_library=return_library_array( "select id,location_name from  lib_location", "id", "location_name"  );	
				$floor_arr=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name");
             $total_quantity=0;
             $i=1;
			 $location="";$floor="";
			 if($dateOrLocWise==2 || $dateOrLocWise==4) // only for location floor and line wise
			 {
				 if($location_id!="") $location=" and location=$location_id";
				 if($floor_id!="") $floor=" and floor_id=$floor_id";
				 
				 if($location_id!="") $location1=" and a.location=$location_id";
				 if($floor_id!="") $floor1=" and a.floor_id=$floor_id"; 
			 }
			 if ($action==9)
			 {
				 $sql=sql_select("select production_date, sum(re_production_qty) as production_quantity	  
				  from pro_garments_production_mst where po_break_down_id in ($po_break_down_id) and re_production_qty!=0 and item_number_id=$item_id and status_active=1 and is_deleted=0 and production_type=7 $location $floor $country_cond group by production_date order by production_date");
			 }
			 else if ($action==4)
			 {
				 
				  $sql=sql_select("select a.po_break_down_id,a.production_date,a.prod_reso_allo,a.sewing_line,a.production_source,a.serving_company,a.location,a.floor_id, 
				  sum(case when a.production_source=1 then b.production_qnty else 0 end) as in_quantity, 
 sum(case when a.production_source=3 then b.production_qnty else 0 end) as out_quantity 
 from pro_garments_production_mst a,pro_garments_production_dtls b 
				 where a.id=b.mst_id and a.po_break_down_id in ($po_break_down_id) and a.item_number_id=$item_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and a.production_type=$action and b.production_type=$action $location1  $floor1  $country_cond1 group by a.po_break_down_id,a.production_date,a.prod_reso_allo,a.sewing_line,a.production_source,a.serving_company,a.location,a.floor_id order by production_date");
				/* 
				 $sql=sql_select("select po_break_down_id,production_date,prod_reso_allo,sewing_line,production_source,serving_company,location,floor_id,
				 sum(case when production_source=1 then production_quantity else 0 end) as in_quantity,
				 sum(case when production_source=3 then production_quantity else 0 end) as out_quantity
				 from pro_garments_production_mst 
				 where po_break_down_id in ($po_break_down_id) and item_number_id=$item_id and status_active=1 and is_deleted=0 and production_type=$action $location $floor $country_cond group by po_break_down_id,production_date,prod_reso_allo,sewing_line,production_source,serving_company,location,floor_id order by production_date");*/
				 
			 }
			 else if ($action==7 || $action==8)
			 {
				 $sql=sql_select("select po_break_down_id,production_date,prod_reso_allo,production_source,serving_company,location,
				 sum(case when production_source=1 then production_quantity else 0 end) as in_quantity,
				 sum(case when production_source=3 then production_quantity else 0 end) as out_quantity
				 from pro_garments_production_mst 
				 where po_break_down_id in ($po_break_down_id) and item_number_id=$item_id and status_active=1 and is_deleted=0 and production_type=$action $location $floor $country_cond group by po_break_down_id,production_date,prod_reso_allo,production_source,serving_company,location order by production_date");
				 
			 }
			 else
			 {
				 $sql=sql_select("select production_date,sum(production_quantity) as production_quantity
				  from pro_garments_production_mst where po_break_down_id in ($po_break_down_id) and item_number_id=$item_id and status_active=1 and is_deleted=0 and production_type=$action $location $floor $country_cond group by production_date order by production_date"); 
			 }
 
            foreach($sql as $resultRow)
            {
                 if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				 if ($action==4)
				 {
					 $sewing_line='';
					if($resultRow[csf('prod_reso_allo')]==1)
					{
						$line_number=explode(",",$prod_reso_arr[$resultRow[csf('sewing_line')]]);
						foreach($line_number as $val)
						{
							if($sewing_line=='') $sewing_line=$sewing_library[$val]; else $sewing_line.=",".$sewing_library[$val];
						}
					}
					else $sewing_line=$sewing_library[$resultRow[csf('sewing_line')]];
             		?>
                     <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>" style="font-size:12px">
                        <td width="30"><? echo $i;?></td>
                        <td width="70" align="center"><? echo change_date_format($resultRow[csf("production_date")]); ?></td>
                        <td width="70" align="center"><? echo $floor_arr[$resultRow[csf("floor_id")]]; ?></td>
                        <td width="70" align="center"><? echo $sewing_line; ?></td>
                        <td width="80" align="right"><? echo number_format($resultRow[csf("in_quantity")],0); ?></td>
                        <td width="80" align="right"><? echo number_format($resultRow[csf("out_quantity")],0); ?></td>
                        <td  width="140"><? if($resultRow[csf("production_source")]==1) echo $company_library[$resultRow[csf("serving_company")]]; else if($resultRow[csf("production_source")]==3) echo $supplier_library[$resultRow[csf("serving_company")]]; ?></td>
                        <td><? echo $location_library[$resultRow[csf("location")]]; ?></td>
                     </tr>	
					 <?	
                     $total_quantity_in+=$resultRow[csf("in_quantity")];
                     $total_quantity_out+=$resultRow[csf("out_quantity")];
				 }
				 else if ($action==7 || $action==8)
				 {
             		?>
                     <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>" style="font-size:12px">
                        <td width="50"><? echo $i;?></td>
                        <td width="100" align="center"><? echo change_date_format($resultRow[csf("production_date")]); ?></td>
                        <td width="100" align="right"><? echo number_format($resultRow[csf("in_quantity")],0); ?></td>
                        <td width="100" align="right"><? echo number_format($resultRow[csf("out_quantity")],0); ?></td>
                        <td  width="140"><? if($resultRow[csf("production_source")]==1) echo $company_library[$resultRow[csf("serving_company")]]; else if($resultRow[csf("production_source")]==3) echo $supplier_library[$resultRow[csf("serving_company")]]; ?></td>
                        <td><? echo $location_library[$resultRow[csf("location")]]; ?></td>
                     </tr>	
					<?	
                     $total_quantity_in+=$resultRow[csf("in_quantity")];
                     $total_quantity_out+=$resultRow[csf("out_quantity")];	
				 }
				 else
				 {
             	?>
                 <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>" style="font-size:12px">
                    <td width="50"><? echo $i;?></td>
                    <td width="200" align="right"><? echo change_date_format($resultRow[csf("production_date")]); ?></td>
                    <td align="right"><? echo number_format($resultRow[csf("production_quantity")],0); ?>&nbsp;</td>
                 </tr>	
                 <?	
				 }
                    $total_quantity+=$resultRow[csf("production_quantity")];
            	 	$i++;
            
        }//end foreach 1st
        ?>
        </table>
        <table width="700" cellspacing="0" border="1" class="tbl_bottom" rules="all" id="body_bottom" >
        	<?
			if ($action==4)
			{
			?>
                 <tr> 
                    <td width="30">&nbsp;</td>
                    <td width="70">&nbsp;</td>
                    <td width="70">&nbsp;</td>
                    <td width="70">Total</td> 
                    <td width="80" id="total_issue_qty" align="right"><? echo number_format($total_quantity_in,0); ?>&nbsp;</td>
                    <td width="80" id="total_issue_qty_out" align="right"><? echo number_format($total_quantity_out,0); ?>&nbsp;</td>
                    <td width="140">&nbsp;</td>
                    <td>&nbsp;</td>
                 </tr>
             <?
			}
			else if ($action==7 || $action==8)
			{
			?>
                 <tr> 
                    <td width="50">&nbsp;</td>
                    <td width="100">Total</td>
                    <td width="100" id="total_issue_qty" align="right"><? echo number_format($total_quantity_in,0); ?>&nbsp;</td>
                    <td width="100" id="total_issue_qty_out" align="right"><? echo number_format($total_quantity_out,0); ?>&nbsp;</td>
                    <td width="140">&nbsp;</td>
                    <td>&nbsp;</td>
                 </tr>
             <?
			}
			else
			{
			?>
                 <tr> 
                    <td width="50">&nbsp;</td> 
                    <td width="200">Total</td> 
                    <td id="total_issue_qty" align="right"><? echo number_format($total_quantity,0); ?>&nbsp;</td>
                 </tr>
             <?
			}
			 ?>
         </table>
       </div>
     </div>
     </fieldset>
     <script>
	var action=<? echo $action; ?>;
	
	//if (action==4) setFilterGrid("tableFilters_sewin",-1);
	//if(action==4) setFilterGrid("table_body",-1,tableFilters_sewin);
	setFilterGrid("table_body",-1,tableFilters_sewin);
	
	</script>
    <?
 	
 exit();
 
}

if ($action=='exfactory')  // exfactory date popup
{
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,1,1);
	
 	?>
    <fieldset>
    <div style="margin-left:60px">
        <table width="500" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1" >
            <thead>
                    <tr>
                        <th width="50">Sl.</th>    
                        <th width="200">Ex Factory Date</th>
                        <th width="">Ex Factory Qnty</th>
               		</tr>
            </thead>
        </table>
        <div style="max-height:425px; overflow-y:scroll; width:518px;" id="scroll_body">
            <table cellspacing="0" border="1" class="rpt_table"  width="500" rules="all" id="table_body" >
            <?
			
             $total_quantity=0;
             $sql=sql_select("select sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END)-sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) AS ex_factory_qnty, ex_factory_date from pro_ex_factory_mst where po_break_down_id in ($po_break_down_id) and item_number_id='$item_id' and status_active=1 and is_deleted=0 group by ex_factory_date"); 
            //echo $sql; 
			$i=1;
            foreach($sql as $resultRow)
            {
                 if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
             	?>
                 <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                    <td width="50"><? echo $i;?></td>
                    <td width="200" align="right"><? if($resultRow[csf("ex_factory_date")]!="0000-00-00") echo change_date_format($resultRow[csf("ex_factory_date")]); ?></td>
                    <td width="" align="right"><? echo number_format($resultRow[csf("ex_factory_qnty")]); ?></td>
                 </tr>	
                 <?		
                    $total_quantity+=$resultRow[csf("ex_factory_qnty")];
            	 	$i++;
            
        }//end foreach 1st
        ?>
        </table>
        <table cellspacing="0" border="1" class="tbl_bottom"  width="500" rules="all" id="body_bottom" >
                 <tr> 
                    <td width="50">&nbsp;</td> 
                    <td width="200">Total</td> 
                    <td width=""><? echo number_format($total_quantity); ?></td>
                  </tr>
         </table>
       </div>
     </div>
     </fieldset>
    <?
 	
 exit();
 
}

if ($action=='exfactoryCountry')  // exfactory date popup
{
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,1,1);
	
	if($location_id=="") $location_mst=""; else $location_mst=" and location='$location_id'";
	if($floor_id=="") $floor_mst=""; else $floor_mst=" and floor_id='$floor_id'";
	
 	?>
    <fieldset>
    <div style="margin-left:60px">
        <table width="500" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1" >
            <thead>
                    <tr>
                        <th width="50">Sl.</th>    
                        <th width="200">Ex Factory Date</th>
                        <th width="">Ex Factory Qnty</th>
               		</tr>
            </thead>
        </table>
        <div style="max-height:425px; overflow-y:scroll; width:518px;" id="scroll_body">
            <table cellspacing="0" border="1" class="rpt_table"  width="500" rules="all" id="table_body" >
            <?
             $total_quantity=0;
             
			 
             $sql=sql_select("select sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END)-sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) AS ex_factory_qnty, ex_factory_date 		  
				  from pro_ex_factory_mst where po_break_down_id in ($po_break_down_id) and item_number_id=$item_id and country_id='$country_id' and status_active=1 and is_deleted=0 group by ex_factory_date"); 
            //echo $sql; 
            foreach($sql as $resultRow)
            {
                 if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
             	?>
                 <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                    <td width="50"><? echo $i;?></td>
                    <td width="200" align="right"><? if($resultRow[csf("ex_factory_date")]!="0000-00-00") echo change_date_format($resultRow[csf("ex_factory_date")]); ?></td>
                    <td width="" align="right"><? echo number_format($resultRow[csf("ex_factory_qnty")]); ?></td>
                 </tr>	
                 <?		
                    $total_quantity+=$resultRow[csf("ex_factory_qnty")];
            	 	$i++;
            
        }//end foreach 1st
        ?>
        </table>
        <table cellspacing="0" border="1" class="tbl_bottom"  width="500" rules="all" id="body_bottom" >
                 <tr> 
                    <td width="50">&nbsp;</td> 
                    <td width="200">Total</td> 
                    <td width=""><? echo number_format($total_quantity); ?></td>
                  </tr>
         </table>
       </div>
     </div>
     </fieldset>
    <?
 	
 exit();
 
}
//--print/emb issue-2,print/emb receive-3,
if ($action==2 || $action==3)
{
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,1,1);
	//echo $action;
	if($country_id=='') $country_cond=""; else $country_cond=" and country_id='$country_id'";
	
 	?>
    <div id="data_panel" align="center" style="width:100%">
         <script>
		 	
			function new_window()
			{
				document.getElementById('scroll_body').style.overflow="auto";
				document.getElementById('scroll_body').style.maxHeight="none";
				//$('#table_body tr:first').hide();
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write(document.getElementById('details_reports').innerHTML);
				d.close();
				//$('#table_body tr:first').show();
				document.getElementById('scroll_body').style.overflowY="scroll";
				document.getElementById('scroll_body').style.maxHeight="none";
			}
          </script>
 	<input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
 	</div>
    <div id="details_reports">
    
  
        <table id="tbl_id" class="rpt_table" width="" border="1" rules="all" >
            <thead>
                <tr>
                    <th width="100">Buyer</th>
                    <th width="100">Job Number</th>
                    <th width="100">Style Name</th>
                    <th width="300">Order Number</th>
                    <th width="100">Ship Date</th>
                    <th width="100">Item Name</th>
                    <th width="100">Order Qty.</th>
                </tr>
            </thead>
            <?
                $buyer_short_library=return_library_array( "select id,short_name from lib_buyer", "id", "short_name"  );
                if($db_type==0)
                {
                    $sql = "select a.job_no_mst,group_concat(distinct(a.po_number)) as po_number,a.pub_shipment_date, sum(a.po_quantity) as po_quantity,a.packing,b.set_break_down, b.company_name, b.order_uom, b.buyer_name, b.style_ref_no,c.set_item_ratio 
                        from wo_po_break_down a, wo_po_details_master b, wo_po_details_mas_set_details c 
                        where a.job_no_mst=b.job_no and b.job_no=c.job_no and a.id in ($po_break_down_id) and c.gmts_item_id=$item_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
                }
                else
                {
                    $sql = "select a.job_no_mst, LISTAGG(a.po_number, ',') WITHIN GROUP (ORDER BY a.id) as po_number,max(a.pub_shipment_date) as pub_shipment_date, sum(a.po_quantity) as po_quantity,a.packing,b.set_break_down, b.company_name, b.order_uom, b.buyer_name, b.style_ref_no,c.set_item_ratio 
                        from wo_po_break_down a, wo_po_details_master b, wo_po_details_mas_set_details c 
                        where a.job_no_mst=b.job_no and b.job_no=c.job_no and a.id in ($po_break_down_id) and c.gmts_item_id=$item_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.job_no_mst, a.packing,b.set_break_down, b.company_name, b.order_uom, b.buyer_name, b.style_ref_no,c.set_item_ratio";
                }
                //echo $sql;die;
                $resultRow=sql_select($sql);
                    
                $cons_embr=return_field_value("sum(cons_dzn_gmts) as cons_dzn_gmts","wo_pre_cost_embe_cost_dtls","job_no='".$resultRow[0][csf("job_no_mst")]."' and status_active=1 and is_deleted=0","cons_dzn_gmts");
                
            ?> 
            <tr style=" background-color:#FFFFFF">
                <td><? echo $buyer_short_library[$resultRow[0][csf("buyer_name")]]; ?></td>
                <td><p><? echo $resultRow[0][csf("job_no_mst")]; ?></p></td>
                <td><p><? echo $resultRow[0][csf("style_ref_no")]; ?></p></td>
                <td><p><? echo implode(",",array_unique(explode(",",$resultRow[0][csf("po_number")]))); ?></p></td>
                <td><? echo change_date_format($resultRow[0][csf("pub_shipment_date")]); ?></td>
                <td><? echo $garments_item[$item_id]; ?></td>
                <td><? echo $resultRow[0][csf("po_quantity")]*$resultRow[0][csf("set_item_ratio")]; ?></td>
            </tr>
             <?
             /*$prod_sewing_sql=sql_select("SELECT sum(alter_qnty) as alter_qnty, sum(reject_qnty) as reject_qnty from pro_garments_production_mst where production_type=5 and po_break_down_id in ($po_break_down_id) and is_deleted=0 and status_active=1");
             foreach($prod_sewing_sql as $sewingRow);*/
			 
			if ($action==2) 
			{
				$th_head="Sys ID";
			}
			else if($action==3) 
			{
				$th_head="Challan";
			}
            ?> 	
           <!-- <tr>
                <td colspan="2">Total Alter Sewing Qty : <b><?// echo $sewingRow[csf("alter_qnty")]; ?></b></td>
                <td colspan="2">Total Reject Sewing Qty : <b><?// echo $sewingRow[csf("reject_qnty")]; ?></b></td>
                <td colspan="2">Pack Assortment: <b><?// echo $packing[$resultRow[csf("packing")]]; ?></b></td>
            </tr>-->
        </table>
    
    <br/>
    
        <table width="2010" cellspacing="0" border="1" class="rpt_table" rules="all">
            <thead>
                   
                 <? if ($action==2) { ?>
                  <tr style="font-size:12px">
                        <th width="25" rowspan="2">Sl.</th>    
                        <th width="70" rowspan="2">Date</th>
                        <th colspan="5">Printing Issue</th>
                        <th colspan="5">Embroidery Issue</th>
                        <th colspan="5">Wash Issue</th>
                        <th colspan="5">Special Work Issue</th>
                        <th colspan="5">Gmts Dyeing Issue</th>
                    </tr> 
                 <? } else {?>
                 	<tr style="font-size:12px">
                        <th width="25" rowspan="2">Sl.</th>    
                        <th width="70" rowspan="2">Date</th>
                        <th colspan="5">Printing Receive</th>
                        <th colspan="5">Embroidery Receive</th>
                        <th colspan="5">Wash Receive</th>
                        <th colspan="5">Special Work Receive</th>
                        <th colspan="5">Gmts Dyeing Receive</th>
                    </tr> 
                 <? } ?>   
                    
                    <tr style="font-size:12px">
                      <th width="70"><? echo $th_head ?></th>
                      <th width="70">InHouse</th>
                      <th width="70">Outside</th>
                      <th width="80">Print Com.</th>
                      <th width="80">Locat.</th>
                      
                      <th width="70"><? echo $th_head ?></th>
                      <th width="70">InHouse</th>
                      <th width="70">Outside</th>
                      <th width="80">Embl. Com.</th>
                      <th width="80">Locat.</th>
                      
                      <th width="70"><? echo $th_head ?></th>
                      <th width="70">InHouse</th>
                      <th width="70">Outside</th>
                      <th width="80">Wash Com.</th>
                      <th width="80">Locat.</th>
                      
                      <th width="70"><? echo $th_head ?></th>
                      <th width="70">InHouse</th>
                      <th width="70">Outside</th>
                      <th width="80">Dyeing Com.</th>
                      <th width="80">Locat.</th>
                      
                      <th width="70"><? echo $th_head ?></th>
                      <th width="70">InHouse</th>
                      <th width="70">Outside</th>
                      <th width="80">Comp.</th>
                      <th>Locat.</th>
                    </tr>
            </thead>
        </table>
        <div style="max-height:425px; overflow-y:scroll; width:2010px;" id="scroll_body">
            <table cellspacing="0" border="1" class="rpt_table"  width="1990" rules="all" id="table_body" >
            <?
			$company_library=return_library_array( "select id,company_short_name from lib_company", "id", "company_short_name" );
 			$supplier_library=return_library_array( "select id,short_name from  lib_supplier", "id", "short_name" );
			$location_library=return_library_array( "select id,location_name from  lib_location", "id", "location_name"  );	
 			 
			  $sql_arr= sql_select("SELECT 
						 production_date,production_source,serving_company,location,id as sys_id,challan_no,
						(CASE WHEN production_source in(1,3) AND embel_name=1 THEN id ELSE 0 END) AS sys_ids1,  
						(CASE WHEN production_source in(1,3) AND embel_name=2 THEN id ELSE 0 END) AS sys_ids2,
						(CASE WHEN production_source in(1,3) AND embel_name=3 THEN id ELSE 0 END) AS sys_ids3,
						(CASE WHEN production_source in(1,3) AND embel_name=4 THEN id ELSE 0 END) AS sys_ids4,
						(CASE WHEN production_source in(1,3) AND embel_name=5 THEN id ELSE 0 END) AS sys_ids5,
						
						(CASE WHEN production_source in(1,3) AND embel_name=1 THEN challan_no ELSE null END) AS challan_no5,  
						(CASE WHEN production_source in(1,3) AND embel_name=2 THEN challan_no ELSE null END) AS challan_no6,
						(CASE WHEN production_source in(1,3) AND embel_name=3 THEN challan_no ELSE null END) AS challan_no7,
						(CASE WHEN production_source in(1,3) AND embel_name=4 THEN challan_no ELSE null END) AS challan_no8,
						(CASE WHEN production_source in(1,3) AND embel_name=5 THEN challan_no ELSE null END) AS challan_no9
												
 					FROM
						pro_garments_production_mst 
					WHERE 
						status_active=1 and is_deleted=0 and production_type=$action and po_break_down_id in ($po_break_down_id) and item_number_id=$item_id $country_cond order by production_date");
						$prod_arr=array();
						foreach($sql_arr as $row)
						{
							if($action==2)
							{
							$prod_arr[$row[csf('production_date')]][$row[csf('production_source')]][$row[csf('serving_company')]]['1'].=$row[csf('sys_ids1')].",";	
							$prod_arr[$row[csf('production_date')]][$row[csf('production_source')]][$row[csf('serving_company')]]['2'].=$row[csf('sys_ids2')].",";
							$prod_arr[$row[csf('production_date')]][$row[csf('production_source')]][$row[csf('serving_company')]]['3'].=$row[csf('sys_ids3')].",";
							
							$prod_arr[$row[csf('production_date')]][$row[csf('production_source')]][$row[csf('serving_company')]]['4'].=$row[csf('sys_ids4')].",";
							$prod_arr[$row[csf('production_date')]][$row[csf('production_source')]][$row[csf('serving_company')]]['5'].=$row[csf('sys_ids5')].",";
							}
							else if($action==3)
							{
							$prod_arr[$row[csf('production_date')]][$row[csf('production_source')]][$row[csf('serving_company')]]['1'].=$row[csf('challan_no5')].",";	
							$prod_arr[$row[csf('production_date')]][$row[csf('production_source')]][$row[csf('serving_company')]]['2'].=$row[csf('challan_no6')].",";
							$prod_arr[$row[csf('production_date')]][$row[csf('production_source')]][$row[csf('serving_company')]]['3'].=$row[csf('challan_no7')].",";
							
							$prod_arr[$row[csf('production_date')]][$row[csf('production_source')]][$row[csf('serving_company')]]['4'].=$row[csf('challan_no8')].",";
							$prod_arr[$row[csf('production_date')]][$row[csf('production_source')]][$row[csf('serving_company')]]['5'].=$row[csf('challan_no9')].",";	
							}
						
						}
					//print_r($prod_arr);
			 
			$sql = sql_select("SELECT production_date,production_source,serving_company,
						max(CASE WHEN  embel_name=1 THEN location ELSE 0 END) AS print_location,
						max(CASE WHEN  embel_name=2 THEN location ELSE 0 END) AS emb_location,
						max(CASE WHEN  embel_name=3 THEN location ELSE 0 END) AS wash_location,
						max(CASE WHEN  embel_name=4 THEN location ELSE 0 END) AS sp_location,
						max(CASE WHEN  embel_name=5 THEN location ELSE 0 END) AS gd_location,
						SUM(CASE WHEN production_source =1 AND embel_name=1 THEN production_quantity ELSE 0 END) AS prod11,  
						SUM(CASE WHEN production_source =1 AND embel_name=2 THEN production_quantity ELSE 0 END) AS prod12,
						SUM(CASE WHEN production_source =1 AND embel_name=3 THEN production_quantity ELSE 0 END) AS prod13,
						SUM(CASE WHEN production_source =1 AND embel_name=4 THEN production_quantity ELSE 0 END) AS prod14,
						SUM(CASE WHEN production_source =1 AND embel_name=5 THEN production_quantity ELSE 0 END) AS prod15,
						
						SUM(CASE WHEN production_source =3 AND embel_name=1 THEN production_quantity ELSE 0 END) AS prod31,  
						SUM(CASE WHEN production_source =3 AND embel_name=2 THEN production_quantity ELSE 0 END) AS prod32,
						SUM(CASE WHEN production_source =3 AND embel_name=3 THEN production_quantity ELSE 0 END) AS prod33,
						SUM(CASE WHEN production_source =3 AND embel_name=4 THEN production_quantity ELSE 0 END) AS prod34,
						SUM(CASE WHEN production_source =3 AND embel_name=5 THEN production_quantity ELSE 0 END) AS prod35
						
						
						
 					FROM
						pro_garments_production_mst 
					WHERE 
						status_active=1 and is_deleted=0 and production_type=$action and po_break_down_id in ($po_break_down_id) and item_number_id=$item_id $country_cond
					GROUP BY production_date,production_source,serving_company order by production_date");
			 /*echo "SELECT production_date,production_source,serving_company,
			 			max(CASE WHEN  embel_name=1 THEN location ELSE 0 END) AS print_location,
						max(CASE WHEN  embel_name=2 THEN location ELSE 0 END) AS emb_location,
						max(CASE WHEN  embel_name=3 THEN location ELSE 0 END) AS wash_location,
						max(CASE WHEN  embel_name=4 THEN location ELSE 0 END) AS sp_location,
						SUM(CASE WHEN production_source =1 AND embel_name=1 THEN production_quantity ELSE 0 END) AS prod11,  
						SUM(CASE WHEN production_source =1 AND embel_name=2 THEN production_quantity ELSE 0 END) AS prod12,
						SUM(CASE WHEN production_source =1 AND embel_name=3 THEN production_quantity ELSE 0 END) AS prod13,
						SUM(CASE WHEN production_source =1 AND embel_name=4 THEN production_quantity ELSE 0 END) AS prod14,
						
						SUM(CASE WHEN production_source =3 AND embel_name=1 THEN production_quantity ELSE 0 END) AS prod31,  
						SUM(CASE WHEN production_source =3 AND embel_name=2 THEN production_quantity ELSE 0 END) AS prod32,
						SUM(CASE WHEN production_source =3 AND embel_name=3 THEN production_quantity ELSE 0 END) AS prod33,
						SUM(CASE WHEN production_source =3 AND embel_name=4 THEN production_quantity ELSE 0 END) AS prod34
 					FROM
						pro_garments_production_mst 
					WHERE 
						status_active=1 and is_deleted=0 and production_type=$action and po_break_down_id in ($po_break_down_id) and item_number_id=$item_id $country_cond
					GROUP BY production_date,production_source,serving_company";*/
			
		   	$printing_in_qnty=0;$emb_in_qnty=0;$wash_in_qnty=0;$special_in_qnty=0;
			$printing_out_qnty=0;$emb_out_qnty=0;$wash_out_qnty=0;$special_out_qnty=0;$gd_out_qnty=0;$gd_in_qnty=0;
			$dataArray=array();$companyArray=array();
            $i=1;
			foreach($sql as $resultRow)
            {
                 if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				 
				 if($resultRow[csf('production_source')]==3)
					$serving_company= $supplier_library[$resultRow[csf('serving_company')]];
				else
					$serving_company= $company_library[$resultRow[csf('serving_company')]];
				$td_count = 2;	
				 $print_sys_is=implode(",",array_unique(explode(",",$prod_arr[$resultRow[csf('production_date')]][$resultRow[csf('production_source')]][$resultRow[csf('serving_company')]]['1'])));
				  $embo_sys_is=$prod_arr[$resultRow[csf('production_date')]][$resultRow[csf('production_source')]][$resultRow[csf('serving_company')]]['2'];
				   $wash_sys_is=$prod_arr[$resultRow[csf('production_date')]][$resultRow[csf('production_source')]][$resultRow[csf('serving_company')]]['3'];
				    $sp_sys_is=$prod_arr[$resultRow[csf('production_date')]][$resultRow[csf('production_source')]][$resultRow[csf('serving_company')]]['4'];
					  $gd_sys_is=$prod_arr[$resultRow[csf('production_date')]][$resultRow[csf('production_source')]][$resultRow[csf('serving_company')]]['5'];
					 
             	?>
                 <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>" style="font-size:12px">
                    <td width="25"><? echo $i;?></td>
                    <td width="70"><div style="word-wrap:break-word; width:70px"><? echo change_date_format($resultRow[csf("production_date")]); ?></div></td>
                    
                     <td width="70" align="right"><p><?  echo rtrim($print_sys_is,","); ?></p></td>
                     
                    <td width="70" align="right"><? if($resultRow[csf('production_source')]==1){ echo $resultRow[csf("prod11")];$printing_in_qnty+=$resultRow[csf("prod11")];}else echo "0"; ?></td>
                    <td width="70" align="right"><? if($resultRow[csf('production_source')]==3){ echo $resultRow[csf("prod31")];$printing_out_qnty+=$resultRow[csf("prod31")];}else echo "0"; ?></td>
                    <td width="80"><p>&nbsp;<? if($resultRow[csf('prod11')]>0 || $resultRow[csf('prod31')]>0) echo $serving_company; ?></p></td>
                    <td width="80"><p>&nbsp;<?  echo $location_library[$resultRow[csf('print_location')]]; 
					$companyArray[$serving_company]=$serving_company;
					$dataArray[1][$serving_company]+=$resultRow[csf("prod11")]+$resultRow[csf("prod31")]; ?></p></td>
                    
                     <td width="70" align="right"><p><? echo rtrim($embo_sys_is,","); ?></p></td>
                    <td width="70" align="right"><? if($resultRow[csf('production_source')]==1){ echo $resultRow[csf("prod12")];$emb_in_qnty+=$resultRow[csf("prod12")];}else echo "0"; ?></td>
                    <td width="70" align="right"><? if($resultRow[csf('production_source')]==3){ echo $resultRow[csf("prod32")];$emb_out_qnty+=$resultRow[csf("prod32")];}else echo "0"; ?></td>
                    <td width="80"><p>&nbsp;<? if($resultRow[csf('prod12')]>0 || $resultRow[csf('prod32')]>0) echo $serving_company; ?></p></td>
                    <td width="80"><p>&nbsp;<?  echo $location_library[$resultRow[csf('emb_location')]];
                    
 					$dataArray[2][$serving_company]+=$resultRow[csf("prod12")]+$resultRow[csf("prod32")]; ?></p></td>
                    
                     <td width="70" align="right"><? echo  rtrim($wash_sys_is,",");//if($resultRow[csf('production_source')]==1){ echo $resultRow[csf("prod13")];$wash_in_qnty+=$resultRow[csf("prod13")];}else echo "0"; ?></td>
                    <td width="70" align="right"><? if($resultRow[csf('production_source')]==1){ echo $resultRow[csf("prod13")];$wash_in_qnty+=$resultRow[csf("prod13")];}else echo "0"; ?></td>
                    <td width="70" align="right"><? if($resultRow[csf('production_source')]==3){ echo $resultRow[csf("prod33")];$wash_out_qnty+=$resultRow[csf("prod33")];}else echo "0"; ?></td>
                    <td width="80"><p>&nbsp;<? if($resultRow[csf('prod13')]>0 || $resultRow[csf('prod33')]>0) echo $serving_company; ?></p></td>
                    <td width="80"><p>&nbsp;<? echo  $location_library[$resultRow[csf('wash_location')]]; 
                     
 					$dataArray[3][$serving_company]+=$resultRow[csf("prod13")]+$resultRow[csf("prod33")]; ?></p></td>
                    
                     <td width="70" align="right"><? echo  rtrim($sp_sys_is,",");//if($resultRow[csf('production_source')]==1){ echo $resultRow[csf("prod13")];$wash_in_qnty+=$resultRow[csf("prod13")];}else echo "0"; ?></td>
                    <td width="70" align="right"><? if($resultRow[csf('production_source')]==1){ echo $resultRow[csf("prod14")];$wash_in_qnty+=$resultRow[csf("prod14")];}else echo "0"; ?></td>
                    <td width="70" align="right"><? if($resultRow[csf('production_source')]==3){ echo $resultRow[csf("prod34")];$wash_out_qnty+=$resultRow[csf("prod34")];}else echo "0"; ?></td>
                    <td width="80"><p>&nbsp;<? if($resultRow[csf('prod14')]>0 || $resultRow[csf('prod34')]>0) echo $serving_company; ?></p></td>
                    <td width="80"><p>&nbsp;<? echo  $location_library[$resultRow[csf('wash_location')]]; 
                     
 					$dataArray[3][$serving_company]+=$resultRow[csf("prod14")]+$resultRow[csf("prod34")]; ?></p></td>
                    
                    
                     <td width="70" align="right"><? echo rtrim($sp_sys_is,",");//if($resultRow[csf('production_source')]==1){ echo $resultRow[csf("prod14")];$special_in_qnty+=$resultRow[csf("prod14")];}else echo "0"; ?></td>
                    <td width="70" align="right"><? if($resultRow[csf('production_source')]==1){ echo $resultRow[csf("prod15")];$gd_in_qnty+=$resultRow[csf("prod15")];}else echo "0"; ?></td>
                    <td width="70" align="right"><? if($resultRow[csf('production_source')]==3){ echo $resultRow[csf("prod35")];$gd_out_qnty+=$resultRow[csf("prod35")];}else echo "0"; ?></td>
                    <td width="80"><p>&nbsp;<? if($resultRow[csf('prod15')]>0 || $resultRow[csf('prod35')]>0) echo $serving_company; ?></p></td>
                    <td><p>&nbsp;<? echo  $location_library[$resultRow[csf('gd_location')]]; $dataArray[5][$serving_company]+=$resultRow[csf("prod15")]+$resultRow[csf("prod35")]; ?> </p></td>
                  </tr> 
 				 <?		
             	$i++;
            
        }//end foreach 1st
        ?>
        		<tfoot>
                    <tr>
                       <th align="right" colspan="2">Total</th>
                        <th align="right"><? //echo $printing_in_qnty; ?></th>
                       <th align="right"><? echo $printing_in_qnty; ?></th>
                       <th align="right"><? echo $printing_out_qnty; ?></th>
                       <th align="right">&nbsp;</th>
                       <th align="right">&nbsp;</th>
                        <th align="right"><? //echo $emb_in_qnty; ?></th>
                       <th align="right"><? echo $emb_in_qnty; ?></th>
                       <th align="right"><? echo $emb_out_qnty; ?></th>
                       <th align="right">&nbsp;</th>
                       <th align="right">&nbsp;</th>
                        <th align="right"><? //echo $wash_in_qnty; ?></th>
                       <th align="right"><? echo $wash_in_qnty; ?></th>
                       <th align="right"><? echo $wash_out_qnty; ?></th>
                       <th align="right">&nbsp;</th>
                       <th align="right">&nbsp;</th>
                        <th align="right"><? //echo $special_in_qnty; ?></th>
                       <th align="right"><? echo $special_in_qnty; ?></th>
                       <th align="right"><? echo $special_out_qnty; ?></th>
                       <th align="right">&nbsp;</th>
                       <th align="right">&nbsp;</th>
                       
                        <th align="right"><? //echo $special_in_qnty; ?></th>
                       <th align="right"><? echo $gd_in_qnty; ?></th>
                       <th align="right"><? echo $gd_out_qnty; ?></th>
                       <th align="right">&nbsp;</th>
                       <th align="right">&nbsp;</th>
                     </tr>
               </tfoot>      
        </table>
       </div>
       
       <div style="clear:both">&nbsp;</div>
       
       <div style="width:490px; float:left"> 
       <table width="470" cellspacing="0" border="1" class="rpt_table" rules="all" > 
       		<? if($action==2){?> <label><h3>Issue Summary</h3></label><? } else {?> <label><h3>Receive Summary</h3></label> <? } ?>               	
             <thead> 
                <tr>
                    <th>SL</th>
                    <th>Emb.Company</th>
                    <th>Print</th>
                    <th>Embroidery</th>
                    <th>Emb	Wash</th>
                    <th>Special Work</th>
                     <th>Gmt Dyeing</th>
                 </tr>
              </thead>  
			 <?
			 $printing_total=0;$emb_total=0;$wash_total=0;$special_total=0;$gd_total=0;
			 $i=1;	 
			 foreach($companyArray as $com){
			 	if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; 
			 ?>
                 <tr bgcolor="<? echo $bgcolor; ?>">
                 		<td><? echo $i; ?></td>
                        <td><? echo $com; ?></td>
                        <td align="right"><? echo number_format($dataArray[1][$com]);$printing_total+=$dataArray[1][$com]; ?></td>
                        <td align="right"><? echo number_format($dataArray[2][$com]);$emb_total+=$dataArray[2][$com]; ?></td>
                        <td align="right"><? echo number_format($dataArray[3][$com]);$wash_total+=$dataArray[3][$com]; ?></td>
                        <td align="right"><? echo number_format($dataArray[4][$com]);$special_total+=$dataArray[4][$com]; ?></td>
                         <td align="right"><? echo number_format($dataArray[5][$com]);$gd_total+=$dataArray[5][$com]; ?></td>
                 </tr>   
              <? $i++; } ?>
              <tfoot>
                    <tr>
                       <th align="right" colspan="2">Grand Total</th>
                       <th align="right"><? echo number_format($printing_total); ?></th>
                       <th align="right"><? echo number_format($emb_total); ?></th>
                       <th align="right"><? echo number_format($wash_total); ?></th>
                       <th align="right"><? echo number_format($special_total); ?></th>
                        <th align="right"><? echo number_format($gd_total); ?></th>
                    </tr>
              </tfoot>          
    	 </table>
     </div>
     
     <div style="width:450px; float:left; "> 
     	<? if($action!=2) //only for receive
		 { 
			?> 	
			<table width="450" cellspacing="0" border="1" class="rpt_table" rules="all" > 
            <label><h3>Balance </h3></label>
              <thead> 
                <tr>
                    <th>SL</th>
                    <th>Particulers</th>
                    <th>Print</th>
                    <th>Embroidery</th>
                    <th> Wash</th>
                    <th>Special Work</th>
                    <th>Gmt Dyeing</th>
                  
                 </tr>
              </thead>  
 			<?
 				$sql_order = sql_select("SELECT 
						SUM(CASE WHEN b.emb_name=1 and b.emb_type!=0 THEN a.po_quantity*b.cons_dzn_gmts ELSE 0 END) AS print,  
						SUM(CASE WHEN b.emb_name=2 and b.emb_type!=0 THEN a.po_quantity*b.cons_dzn_gmts ELSE 0 END) AS emb,
						SUM(CASE WHEN b.emb_name=3 and b.emb_type!=0 THEN a.po_quantity*b.cons_dzn_gmts ELSE 0 END) AS wash,
						SUM(CASE WHEN b.emb_name=4 and b.emb_type!=0 THEN a.po_quantity*b.cons_dzn_gmts ELSE 0 END) AS special,
						SUM(CASE WHEN b.emb_name=5 and b.emb_type!=0 THEN a.po_quantity*b.cons_dzn_gmts ELSE 0 END) AS gmt_dyeing
   					FROM
						wo_po_break_down a, wo_pre_cost_embe_cost_dtls b 
					WHERE
						a.id in ($po_break_down_id) and a.job_no_mst=b.job_no");
				foreach($sql_order as $resultRow);	
						
				$sql_mst = sql_select("SELECT 
						SUM(CASE WHEN embel_name=1 THEN production_quantity ELSE 0 END) AS print_issue,  
						SUM(CASE WHEN embel_name=2 THEN production_quantity ELSE 0 END) AS emb_issue,
						SUM(CASE WHEN embel_name=3 THEN production_quantity ELSE 0 END) AS wash_issue,
						SUM(CASE WHEN embel_name=4 THEN production_quantity ELSE 0 END) AS special_issue,
						SUM(CASE WHEN embel_name=5 THEN production_quantity ELSE 0 END) AS gmt_issue
 					FROM
						pro_garments_production_mst
					WHERE
						po_break_down_id in ($po_break_down_id) and item_number_id=$item_id and production_type=2 $country_cond
					");		
				//echo $sql_mst;die;
				foreach($sql_mst as $resultMst);
				//echo $sql;die;
				$i=1;		
				 
					 ?>
						 <tr bgcolor="#E9F3FF">
								<td><? echo $i++; ?></td>
								<td>Req Qnty</td>
								<td align="right"><? echo number_format($resultRow[csf('print')]); ?></td>
								<td align="right"><? echo number_format($resultRow[csf('emb')]); ?></td>
								<td align="right"><? echo number_format($resultRow[csf('wash')]); ?></td>
								<td align="right"><? echo number_format($resultRow[csf('special')]); ?></td>
                                <td align="right"><? echo number_format($resultRow[csf('gmt_dyeing')]); ?></td>
						 </tr> 
                         <tr bgcolor="#FFFFFF">
								<td><? echo $i++; ?></td>
                                <td>Total Sent for</td>
 								<td align="right"><? echo number_format($resultMst[csf('print_issue')]); ?></td>
								<td align="right"><? echo number_format($resultMst[csf('emb_issue')]); ?></td>
								<td align="right"><? echo number_format($resultMst[csf('wash_issue')]); ?></td>
								<td align="right"><? echo number_format($resultMst[csf('special_issue')]); ?></td>
                                <td align="right"><? echo number_format($resultMst[csf('gmt_issue')]); ?></td>
						 </tr>
                         <tr bgcolor="#E9F3FF">
								<td><? echo $i++; ?></td>
                                <td>Total Receive</td>
 								<td align="right"><? echo number_format($printing_total); ?></td>
								<td align="right"><? echo number_format($emb_total); ?></td>
								<td align="right"><? echo number_format($wash_total); ?></td>
								<td align="right"><? echo number_format($special_total); ?></td>
                                <td align="right"><? echo number_format($gd_total); ?></td>
						 </tr>
                         <tr bgcolor="#FFFFFF">
								<td><? echo $i++; ?></td>
                                <td>Receive Balance</td>
                                <? $rcv_print_balance = $resultMst[csf('print_issue')]-$printing_total; ?>
 								<td align="right"><? echo number_format($rcv_print_balance); ?></td>
								<? $rcv_emb_balance = $resultMst[csf('emb_issue')]-$emb_total; ?>
 								<td align="right"><? echo number_format($rcv_emb_balance); ?></td>
								<? $rcv_wash_balance = $resultMst[csf('wash_issue')]-$wash_total; ?>
 								<td align="right"><? echo number_format($rcv_wash_balance); ?></td>
								<? $rcv_special_balance = $resultMst[csf('special_issue')]-$special_total;
								$rcv_gd_balance = $resultMst[csf('gmt_issue')]-$gd_total;
								 ?>
 								<td align="right"><? echo number_format($rcv_special_balance); ?></td>
                                <td align="right"><? echo number_format($rcv_gd_balance); ?></td>
						 </tr> 
                         <tr bgcolor="#E9F3FF">
								<td><? echo $i++; ?></td>
 								<td>Issue Balance</td>
 								<td align="right"><? echo  number_format($resultRow[csf('print')]-$resultMst[csf('print_issue')]); ?></td>
								<td align="right"><? echo  number_format($resultRow[csf('emb')]-$resultMst[csf('emb_issue')]); ?></td>
                                <td align="right"><? echo  number_format($resultRow[csf('wash')]-$resultMst[csf('wash_issue')]); ?></td>
                                <td align="right"><? echo  number_format($resultRow[csf('special')]-$resultMst[csf('special_issue')]); ?></td>
                                <td align="right"><? echo  number_format($resultRow[csf('gmt_dyeing')]-$resultMst[csf('gmt_issue')]); ?></td>
 						 </tr>  
					 <? 
 				} 
			?>
            </table> 
        
     </div>
 </div>    
    
<?
  exit();
 
}
if($action=="pp_approved_date_popup")
{
	
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,1,1);
	
 	?>
    <fieldset>
    <div style="margin-left:60px">
        <table width="400" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1" >
            <thead>
                    <tr>
                        <th width="50">Sl.</th>    
                        <th width="200">PO Number</th>
                        <th width="">PP Approved Date</th>
               		</tr>
            </thead>
        </table>
        <div style="max-height:425px; overflow-y:scroll; width:418px;" id="scroll_body">
            <table cellspacing="0" border="1" class="rpt_table"  width="400" rules="all" id="table_body" >
            <?
			$ppApprovalDate_sql=sql_select("select po_break_down_id,job_no_mst,approval_status_date,approval_status from wo_po_sample_approval_info where approval_status=3 and  po_break_down_id in($po_break_down_id) ");
				 
			 $orderNumber_sql=sql_select("select id,po_number from wo_po_break_down where status_active=1 and is_deleted=0 and id in ($po_break_down_id)");
			 foreach($orderNumber_sql as $row)
			 {
				$po_numbers[$row[csf('id')]]['po_number']=$row[csf('po_number')];
			 }
            //echo $sql; 
			$i=1;
            foreach($ppApprovalDate_sql as $resultRow)
            {
                 if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
             	?>
                 <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                    <td width="50"><? echo $i;?></td>
                    <td width="200" align="left"><? echo $po_numbers[$resultRow[csf("po_break_down_id")]]['po_number']; ?></td>
                    <td width="" align="center"><? echo change_date_format($resultRow[csf("approval_status_date")]); ?></td>
                 </tr>	
                 <?		
            	 	$i++;
            
        }//end foreach 1st
        ?>
        </table>
        
       </div>
     </div>
     </fieldset>
    <?
 	
 exit();
 

}
if ($action=="reject_qty")
{
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,1,1);
	//echo $po_id;
	//echo $company_name;die;
	if($reportType==1 || $reportType==5  || $reportType==7)
	{
		$location_cond=""; 
		$floor_cond="";
		$country_cond="";
	}
	else if($reportType==2)
	{
		$location_cond=" and a.location=$location_id"; 
		$floor_cond=" and a.floor_id=$floor_id";
		$country_cond="";
	}
	else if($reportType==3)
	{
		$location_cond=""; 
		$floor_cond="";
		$country_cond=" and a.country_id='$country_id'";	
	}
	else
	{
		$location_cond=" and a.location=$location_id"; 
		$floor_cond=" and a.floor_id=$floor_id";
		$country_cond=" and a.country_id='$country_id'";	
	}
	
	
	/*echo "Select sum(CASE WHEN production_type ='1' THEN reject_qnty ELSE 0 END) AS cutting_rej_qnty,
					sum(CASE WHEN production_type ='8' THEN reject_qnty ELSE 0 END) AS finish_rej_qnty,
					sum(CASE WHEN production_type ='5' THEN reject_qnty ELSE 0 END) AS sewingout_rej_qnty
					from pro_garments_production_mst 
					where po_break_down_id in ($po_id) and item_number_id='$item_id' and status_active=1 and is_deleted=0 $location_cond $floor_cond $country_cond group by po_break_down_id";*/
	
	$sql_variable=sql_select("select cutting_update, printing_emb_production, sewing_production, iron_update, finishing_update from variable_settings_production where company_name=$company_name and variable_list=28 and status_active=1 and is_deleted=0");
	$cutting_variable=$sql_variable[0][csf('cutting_update')];
	$printing_variable=$sql_variable[0][csf('printing_emb_production')];
	$sewing_variable=$sql_variable[0][csf('sewing_production')];
	$iron_variable=$sql_variable[0][csf('iron_update')];
	$finishing_variable=$sql_variable[0][csf('finishing_update')];
	//$cutting_variable_setting=return_field_value("cutting_update","variable_settings_production","company_name=$company_name and variable_list=28 and status_active=1 and is_deleted=0","cutting_update");
	$po_array=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number");
	$color_Arr_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");

	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id in($po_id)","size_number_id","size_number_id");
	
	if($cutting_variable==1)
	{
		$sql_cutting=sql_select("Select a.po_break_down_id, sum(a.reject_qnty)  AS cutting_rej_qnty
					from pro_garments_production_mst  a
					where a.production_type =1 and a.po_break_down_id in ($po_id) and a.item_number_id='$item_id' and a.status_active=1 and a.is_deleted=0 $location_cond   
		$floor_cond 
		$country_cond group by po_break_down_id");
	}
	else
	{
		
		$sql_cutting=sql_select("Select a.po_break_down_id,c.color_number_id, c.size_number_id,sum(b.reject_qty) AS cutting_rej_qnty
					from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c 
					where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.production_type=1 and a.po_break_down_id in ($po_id) and a.item_number_id='$item_id' and a.status_active=1 and a.is_deleted=0 $location_cond   
		$floor_cond 
		$country_cond  group by a.po_break_down_id,c.color_number_id, c.size_number_id");
		
		foreach($sql_cutting as $row)
		{
			if($row[csf('cutting_rej_qnty')]>0)
			{
				$cutting_data[$row[csf('po_break_down_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]] +=$row[csf('cutting_rej_qnty')];
			}
		}
	}
	
	//var_dump($cutting_data);die;
	
	if($printing_variable==1)
	{
		$sql_printing=sql_select("Select a.po_break_down_id, sum(a.reject_qnty)  AS printing_rej_qnty
					from pro_garments_production_mst a 
					where a.production_type =3 and a.po_break_down_id in ($po_id) and a.item_number_id='$item_id' and a.status_active=1 and a.is_deleted=0 $location_cond   
		$floor_cond 
		$country_cond  group by po_break_down_id");
	}
	else
	{
		$sql_printing=sql_select("Select a.po_break_down_id,c.color_number_id, c.size_number_id,sum(b.reject_qty) AS printing_rej_qnty
					from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c 
					where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.production_type=3 and a.po_break_down_id in ($po_id) and a.item_number_id='$item_id' and a.status_active=1 and a.is_deleted=0 $location_cond   
		$floor_cond 
		$country_cond  group by a.po_break_down_id,c.color_number_id, c.size_number_id");
		
		foreach($sql_printing as $row)
		{
			if($row[csf('printing_rej_qnty')]>0)
			{
				$printing_data[$row[csf('po_break_down_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]] +=$row[csf('printing_rej_qnty')];
			}
		}
	}
	
	if($sewing_variable==1)
	{
		$sql_sewing=sql_select("Select a.po_break_down_id, sum(a.reject_qnty)  AS sewingout_rej_qnty
					from pro_garments_production_mst a 
					where a.production_type =5 and a.po_break_down_id in ($po_id) and a.item_number_id='$item_id' and a.status_active=1 and a.is_deleted=0 $location_cond   
		$floor_cond 
		$country_cond group by po_break_down_id");
	}
	else
	{
		$sql_sewing=sql_select("Select a.po_break_down_id,c.color_number_id, c.size_number_id,sum(b.reject_qty) AS sewingout_rej_qnty
					from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c 
					where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.production_type=5 and a.po_break_down_id in ($po_id) and a.item_number_id='$item_id' and a.status_active=1 and a.is_deleted=0 $location_cond   
		$floor_cond 
		$country_cond  group by a.po_break_down_id,c.color_number_id, c.size_number_id");
					
		foreach($sql_sewing as $row)
		{
			if($row[csf('sewingout_rej_qnty')]>0)
			{
				$sewing_data[$row[csf('po_break_down_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]] +=$row[csf('sewingout_rej_qnty')];
			}
		}
	}
	
	if($iron_variable==1)
	{
		$sql_iron=sql_select("Select a.po_break_down_id, sum(a.reject_qnty)  AS iron_rej_qnty
					from pro_garments_production_mst a 
					where a.production_type =7 and a.po_break_down_id in ($po_id) and a.item_number_id='$item_id' and a.status_active=1 and a.is_deleted=0 $location_cond   
		$floor_cond 
		$country_cond  group by po_break_down_id");
	}
	else
	{
		$sql_iron=sql_select("Select a.po_break_down_id,c.color_number_id, c.size_number_id,sum(b.reject_qty) AS iron_rej_qnty
					from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c 
					where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.production_type=7 and a.po_break_down_id in ($po_id) and a.item_number_id='$item_id' and a.status_active=1 and a.is_deleted=0 $location_cond   
		$floor_cond 
		$country_cond  group by a.po_break_down_id,c.color_number_id, c.size_number_id");
		
		foreach($sql_iron as $row)
		{
			if($row[csf('iron_rej_qnty')]>0)
			{
				$iron_data[$row[csf('po_break_down_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]] +=$row[csf('iron_rej_qnty')];
			}
		}
	}
	
	if($finishing_variable==1)
	{
		$sql_finishing=sql_select("Select a.po_break_down_id, sum(a.reject_qnty)  AS finish_rej_qnty
					from pro_garments_production_mst a
					where a.production_type =8 and a.po_break_down_id in ($po_id) and a.item_number_id='$item_id' and a.status_active=1 and a.is_deleted=0 $location_cond   
		$floor_cond 
		$country_cond  group by po_break_down_id");
	}
	else
	{
		$sql_finishing=sql_select("Select a.po_break_down_id,c.color_number_id, c.size_number_id,sum(b.reject_qty) AS finish_rej_qnty
					from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c 
					where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.production_type=8 and a.po_break_down_id in ($po_id) and a.item_number_id='$item_id' and a.status_active=1 and a.is_deleted=0  $location_cond   
		$floor_cond 
		$country_cond  group by a.po_break_down_id,c.color_number_id, c.size_number_id");
		
		foreach($sql_finishing as $row)
		{
			if($row[csf('finish_rej_qnty')]>0)
			{
				$finishing_data[$row[csf('po_break_down_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]] +=$row[csf('finish_rej_qnty')];
			}
		}
	}
	
	
	?>
    <div id="data_panel" align="center" style="width:100%">
		<script>
        function new_window()
        {
        var w = window.open("Surprise", "#");
        var d = w.document.open();
        d.write(document.getElementById('details_reports').innerHTML);
        d.close();
        }
        </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
    </div>
    <div style="color:#FF0000; font-size:14px; font-weight:bold; float:left; width:635px">This Pop-Up Will Be Perfect When Order, Production and All Procedure Follow Color or Color & Size Level.</div>
    <div style="width:635px" align="center" id="details_reports"> 
    <table id="tbl_id" class="rpt_table" width="" border="1" rules="all" >
    	<thead>
        	<tr>
            	<th width="60">Buyer</th>
                <th width="90">Job Number</th>
                <th width="90">Style Name</th>
                <th width="150">Order Number</th>
                <th width="70">Ship Date</th>
                <th width="100">Item Name</th>
                <th >Order Qty.</th>
            </tr>
        </thead>
       	<?
        	$buyer_short_library=return_library_array( "select id,short_name from lib_buyer", "id", "short_name"  );
			if($db_type==0)
			{
 				$sql = "select a.job_no_mst,group_concat(distinct(a.po_number)) as po_number,a.pub_shipment_date, sum(a.po_quantity) as po_quantity,a.packing,b.set_break_down, b.company_name, b.order_uom, b.buyer_name, b.style_ref_no,c.set_item_ratio 
					from wo_po_break_down a, wo_po_details_master b, wo_po_details_mas_set_details c 
					where a.job_no_mst=b.job_no and b.job_no=c.job_no and a.id in ($po_id) and c.gmts_item_id='$item_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
			}
			else
			{
				$sql = "select a.job_no_mst, LISTAGG(a.po_number, ',') WITHIN GROUP (ORDER BY a.id) as po_number,max(a.pub_shipment_date) as pub_shipment_date, sum(a.po_quantity) as po_quantity,a.packing,b.set_break_down, b.company_name, b.order_uom, b.buyer_name, b.style_ref_no,c.set_item_ratio 
					from wo_po_break_down a, wo_po_details_master b, wo_po_details_mas_set_details c 
					where a.job_no_mst=b.job_no and b.job_no=c.job_no and a.id in ($po_id) and c.gmts_item_id='$item_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.job_no_mst, a.packing,b.set_break_down, b.company_name, b.order_uom, b.buyer_name, b.style_ref_no,c.set_item_ratio";
			}
			//echo $sql;
			$resultRow=sql_select($sql);
				
 		?> 
        <tr>
        	<td><? echo $buyer_short_library[$resultRow[0][csf("buyer_name")]]; ?></td>
            <td><p><? echo $resultRow[0][csf("job_no_mst")]; ?></p></td>
            <td><p><? echo $resultRow[0][csf("style_ref_no")]; ?></p></td>
            <td><p><? echo implode(",",array_unique(explode(",",$resultRow[0][csf("po_number")]))); ?></p></td>
            <td><? echo change_date_format($resultRow[0][csf("pub_shipment_date")]); ?></td>
            <td><? echo $garments_item[$item_id]; ?></td>
            <td><? echo $resultRow[0][csf("po_quantity")]*$resultRow[0][csf("set_item_ratio")]; ?></td>
        </tr>
    </table>
    <br />
    <?
	
	
	//Cutting Data Display Here
	if($cutting_variable==1)
	{
		if(!empty($sql_cutting))
		{
			if($reportType==5  || $reportType==7) $tbl_width=350; else $tbl_width=200;
			?>
            <span style="font-size:18px; font-weight:bold;">Cutting Reject Quantity</span>
            <table width="<? echo $tbl_width; ?>" cellspacing="0" border="1" class="rpt_table" rules="all" >
            	<thead>
                	<th width="50">SL</th>
                    <?
					if($reportType==5  || $reportType==7)
					{
						?>
                        <th width="150">PO Number</th>
                        <?
					}
					?>
                	<th>Reject Quantity</th>
                </thead>
                <tbody>
                	<?
					$i=1;
					foreach($sql_cutting as $row)
					{
						?>
                        <tr>
                            <td><? echo $i; ?></td>
                            <?
							if($reportType==5  || $reportType==7)
							{
								?>
								<td><? echo $po_array[$row[csf("po_break_down_id")]]; ?></td>
								<?
							}
							?>
                            <td align="right"><? echo number_format($row[csf("cutting_rej_qnty")],2);?></td>
                        </tr>
                        <?
						$i++;
					}
					?>
                </tbody>
            </table>
            <br />
            <?
			
		}
	}
	else
	{
		$collspan=count($sizearr_order);
		if($reportType==5)
		{
			$table_width=(330+($collspan*60));
			$colspan=3;
		}
		else 
		{
			$table_width=(230+($collspan*60));
			$colspan=2;
		}
		
		
		if(!empty($sql_cutting))
		{
			?>
            <span style="font-size:18px; font-weight:bold;">Cutting Reject Quantity</span>
            <table width="<? echo $table_width; ?>" cellspacing="0" border="1" class="rpt_table" rules="all" >
            	<thead>
                	<th width="50">SL</th>
                    <?
					if($reportType==5  || $reportType==7)
					{
						?>
                        <th width="100">PO Number</th>
                        <?
					}
					?>
                    <th width="100">Color</th>
                    <?
					foreach($sizearr_order as $size_id)
					{
						?>
                        <th width="60"><? echo $sizearr[$size_id]; ?></th>
                        <?
					}
					?>
                    <th >Color Total</th>
                </thead>
                <tbody>
                	<?
					$i=1;$grand_total_cutting=0;
					foreach($cutting_data as $order_id=>$value)
					{
						foreach($value as $color_id=>$val)
						{
							?>
							<tr>
								<td><? echo $i; ?></td>
                                <?
								if($reportType==5  || $reportType==7)
								{
									
									if(!in_array($order_id,$temp_cut_arr))
									{
										$temp_cut_arr[]=$order_id;
										?>
										<td><? echo $po_array[$order_id]; ?></td>
										<?
									}
									else
									{
										?>
                                        <td>&nbsp;</td>
                                        <?
									}
								}
								?>
                                <td><? echo $color_Arr_library[$color_id]; ?></td>
                                <?
								$color_total_cutting=0;
								foreach($sizearr_order as $size_id)
								{
									?>
                                    <td align="right">
									<? 
										echo number_format($cutting_data[$order_id][$color_id][$size_id],0);
										$color_total_cutting+= $cutting_data[$order_id][$color_id][$size_id];
										$color_size_cutting [$size_id]+=$cutting_data[$order_id][$color_id][$size_id];
									?>
                                    </td>
                                    <?
								}
								?>
                                <td align="right"><? echo number_format($color_total_cutting,0); $grand_total_cutting+=$color_total_cutting;?></td>
							</tr>
							<?
							$i++;
						}
					}
					?>
                    <tr bgcolor="#CCCCCC" style="font-size:16px; font-weight:bold;">
                        <td colspan="<? echo $colspan; ?>" align="right">Total:</td>
                        <?
                        foreach($sizearr_order as $size_id)
						{
							?>
							<td align="right"><? echo number_format($color_size_cutting [$size_id],0);?></td>
							<?
						}
						?>
                        <td align="right"><? echo number_format($grand_total_cutting,0); ?></td>
                    </tr>
                </tbody>
            </table>
            <br />
            <?
			
		}
	}
	//emblish Data Display Here
	if($printing_variable==1)
	{
		if(!empty($sql_printing))
		{
			if($reportType==5  || $reportType==7) $tbl_width=350; else $tbl_width=200;
			?>
            <span style="font-size:18px; font-weight:bold;">Embellishment Reject Quantity</span>
            <table width="<? echo $tbl_width; ?>" cellspacing="0" border="1" class="rpt_table" rules="all" >
            	<thead>
                	<th width="50">SL</th>
                    <?
					if($reportType==5)
					{
						?>
                        <th width="150">PO Number</th>
                        <?
					}
					?>
                	<th>Reject Quantity</th>
                </thead>
                <tbody>
                	<?
					$i=1;
					foreach($sql_printing as $row)
					{
						?>
                        <tr>
                            <td><? echo $i; ?></td>
                            <?
							if($reportType==5  || $reportType==7)
							{
								?>
								<td><? echo $po_array[$row[csf("po_break_down_id")]]; ?></td>
								<?
							}
							?>
                            <td align="right"><? echo number_format($row[csf("printing_rej_qnty")],2);?></td>
                        </tr>
                        <?
						$i++;
					}
					?>
                </tbody>
            </table>
            <br />
            <?
			
		}
	}
	else
	{
		$collspan=count($sizearr_order);
		if($reportType==5)
		{
			$table_width=(330+($collspan*60));
			$colspan=3;
		}
		else
		{
			$table_width=(230+($collspan*60));
			$colspan=2;
		}
		
		
		if(!empty($sql_printing))
		{
			?>
            <span style="font-size:18px; font-weight:bold;">Embellishment Reject Quantity</span>
            <table width="<? echo $table_width; ?>" cellspacing="0" border="1" class="rpt_table" rules="all" >
            	<thead>
                	<th width="50">SL</th>
                    <?
					if($reportType==5  || $reportType==7)
					{
						?>
                        <th width="100">PO Number</th>
                        <?
					}
					?>
                    <th width="100">Color</th>
                    <?
					foreach($sizearr_order as $size_id)
					{
						?>
                        <th width="60"><? echo $sizearr[$size_id]; ?></th>
                        <?
					}
					?>
                    <th>Color Total</th>
                </thead>
                <tbody>
                	<?
					$i=1; $grand_total_printing=0;
					foreach($printing_data as $order_id=>$value)
					{
						foreach($value as $color_id=>$val)
						{
							?>
							<tr>
								<td><? echo $i; ?></td>
                                <?
								if($reportType==5  || $reportType==7)
								{
									if(!in_array($order_id,$temp_print_arr))
									{
										$temp_print_arr[]=$order_id;
										?>
										<td><? echo $po_array[$order_id]; ?></td>
										<?
									}
									else
									{
										?>
                                        <td>&nbsp;</td>
                                        <?
									}
								}
								?>
                                <td><? echo $color_Arr_library[$color_id]; ?></td>
                                <?
								$color_total_printing=0;
								foreach($sizearr_order as $size_id)
								{
									?>
                                    <td align="right">
									<? 
										echo number_format($printing_data[$order_id][$color_id][$size_id],0);
										$color_total_printing+= $printing_data[$order_id][$color_id][$size_id];
										$color_size_printing[$size_id]+=$printing_data[$order_id][$color_id][$size_id];
									?>
                                    </td>
                                    <?
								}
								?>
                                <td align="right"><? echo number_format($color_total_printing,0); $grand_total_printing+=$color_total_printing;?></td>
							</tr>
							<?
							$i++;
						}
					}
					?>
                    <tr bgcolor="#CCCCCC" style="font-size:16px; font-weight:bold;">
                        <td colspan="<? echo $colspan; ?>" align="right">Total:</td>
                        <?
                        foreach($sizearr_order as $size_id)
						{
							?>
							<td align="right"><? echo number_format($color_size_printing[$size_id],0);?></td>
							<?
						}
						?>
                        <td align="right"><? echo number_format($grand_total_printing,0); ?></td>
                    </tr>
                </tbody>
            </table>
            <br />
            <?
			
		}
	}
	
	
	//Sewing Data Display Here
	if($sewing_variable==1)
	{
		if(!empty($sql_sewing))
		{
			if($reportType==5  || $reportType==7) $tbl_width=350; else $tbl_width=200;
			?>
            <span style="font-size:18px; font-weight:bold;">Sewing Reject Quantity</span>
            <table width="<? echo $tbl_width; ?>" cellspacing="0" border="1" class="rpt_table" rules="all" >
            	<thead>
                	<th width="50">SL</th>
                    <?
					if($reportType==5  || $reportType==7)
					{
						?>
                        <th width="100">PO Number</th>
                        <?
					}
					?>
                	<th>Reject Quantity</th>
                </thead>
                <tbody>
                	<?
					$i=1;
					foreach($sql_sewing as $row)
					{
						?>
                        <tr>
                            <td><? echo $i; ?></td>
                            <?
							if($reportType==5  || $reportType==7)
							{
								?>
								<td><? echo $po_array[$row[csf("po_break_down_id")]]; ?></td>
								<?
							}
							?>
                            <td align="right"><? echo number_format($row[csf("sewingout_rej_qnty")],2);?></td>
                        </tr>
                        <?
						$i++;
					}
					?>
                </tbody>
            </table>
            <br />
            <?
			
		}
	}
	else
	{
		$collspan=count($sizearr_order);
		if($reportType==5  || $reportType==7)
		{
			$table_width=(330+($collspan*60));
			$colspan=3;
		}
		else
		{
			$table_width=(230+($collspan*60));
			$colspan=2;
		}
		
		if(!empty($sql_sewing))
		{
			?>
            <span style="font-size:18px; font-weight:bold;">Sewing Reject Quantity</span>
            <table width="<? echo $table_width; ?>" cellspacing="0" border="1" class="rpt_table" rules="all" >
            	<thead>
                	<th width="50">SL</th>
                    <?
					if($reportType==5  || $reportType==7)
					{
						?>
                        <th width="100">PO Number</th>
                        <?
					}
					?>
                    <th width="100">Color</th>
                    <?
					foreach($sizearr_order as $size_id)
					{
						?>
                        <th width="60"><? echo $sizearr[$size_id]; ?></th>
                        <?
					}
					?>
                    <th >Color Total</th>
                </thead>
                <tbody>
                	<?
					$i=1;$grand_total_sewing=0;
					foreach($sewing_data as $order_id=>$value)
					{
						foreach($value as $color_id=>$val)
						{
							?>
							<tr>
								<td><? echo $i; ?></td>
                                <?
								if($reportType==5  || $reportType==7)
								{
									if(!in_array($order_id,$temp_sewing_arr))
									{
										$temp_sewing_arr[]=$order_id;
										?>
										<td><? echo $po_array[$order_id]; ?></td>
										<?
									}
									else
									{
										?>
                                        <td>&nbsp;</td>
                                        <?
									}
								}
								?>
                                <td><? echo $color_Arr_library[$color_id]; ?></td>
                                <?
								$color_total_sewing=0;
								foreach($sizearr_order as $size_id)
								{
									?>
                                    <td align="right">
									<? 
										echo number_format($sewing_data[$order_id][$color_id][$size_id],0);
										$color_total_sewing+= $sewing_data[$order_id][$color_id][$size_id];
										$color_size_sewing[$size_id]+=$sewing_data[$order_id][$color_id][$size_id];
									?>
                                    </td>
                                    
                                    <?
								}
								?>
                                <td align="right"><? echo number_format($color_total_sewing,0); $grand_total_sewing+=$color_total_sewing;?></td>
							</tr>
							<?
							$i++;
						}
					}
					?>
                    <tr bgcolor="#CCCCCC" style="font-size:16px; font-weight:bold;">
                        <td colspan="<? echo $colspan; ?>" align="right">Total:</td>
                        <?
                        foreach($sizearr_order as $size_id)
						{
							?>
							<td align="right"><? echo number_format($color_size_sewing[$size_id],0);?></td>
							<?
						}
						?>
                        <td align="right"><? echo number_format($grand_total_sewing,0); ?></td>
                    </tr>
                </tbody>
            </table>
            <br />
            <?
			
		}
	}
	
	
	//Iron Data Display Here
	if($iron_variable==1)
	{
		if(!empty($sql_iron))
		{
			if($reportType==5  || $reportType==7) $tbl_width=350; else $tbl_width=200;
			?>
            <span style="font-size:18px; font-weight:bold;">Iron  Reject Quantity</span>
            <table width="<? echo $tbl_width;?>" cellspacing="0" border="1" class="rpt_table" rules="all" >
            	<thead>
                	<th width="50">SL</th>
                    <?
					if($reportType==5  || $reportType==7)
					{
						?>
                        <th width="100">PO Number</th>
                        <?
					}
					?>
                	<th>Reject Quantity</th>
                </thead>
                <tbody>
                	<?
					$i=1;
					foreach($sql_iron as $row)
					{
						?>
                        <tr>
                            <td><? echo $i; ?></td>
                            <?
							if($reportType==5  || $reportType==7)
							{
								?>
								<td><? echo $po_array[$row[csf("po_break_down_id")]]; ?></td>
								<?
							}
							?>
                            <td align="right"><? echo number_format($row[csf("iron_rej_qnty")],2);?></td>
                        </tr>
                        <?
						$i++;
					}
					?>
                </tbody>
            </table>
            <br />
            <?
			
		}
	}
	else
	{
		$collspan=count($sizearr_order);
		if($reportType==5  || $reportType==7)
		{
			$table_width=(330+($collspan*60));
			$colspan=3;
		}
		else
		{
			$table_width=(230+($collspan*60));
			$colspan=2;
		}
		
		if(!empty($sql_iron))
		{
			?>
            <span style="font-size:18px; font-weight:bold;">Iron Reject Quantity</span>
            <table width="<? echo $table_width; ?>" cellspacing="0" border="1" class="rpt_table" rules="all" >
            	<thead>
                	<th width="50">SL</th>
                    <?
					if($reportType==5  || $reportType==7)
					{
						?>
                        <th width="100">PO Number</th>
                        <?
					}
					?>
                    <th width="100">Color</th>
                    <?
					foreach($sizearr_order as $size_id)
					{
						?>
                        <th width="60"><? echo $sizearr[$size_id]; ?></th>
                        <?
					}
					?>
                    <th >Color Total</th>
                </thead>
                <tbody>
                	<?
					$i=1;$grand_total_iron=0;
					foreach($iron_data as $order_id=>$value)
					{
						foreach($value as $color_id=>$val)
						{
							?>
							<tr>
								<td><? echo $i; ?></td>
                                <?
								if($reportType==5  || $reportType==7)
								{
									if(!in_array($order_id,$temp_iron_arr))
									{
										$temp_iron_arr[]=$order_id;
										?>
										<td><? echo $po_array[$order_id]; ?></td>
										<?
									}
									else
									{
										?>
                                        <td>&nbsp;</td>
                                        <?
									}
								}
								?>
                                <td><? echo $color_Arr_library[$color_id]; ?></td>
                                <?
								$color_total_iron=0;
								foreach($sizearr_order as $size_id)
								{
									?>
                                    <td align="right">
									<? 
										echo number_format($iron_data[$order_id][$color_id][$size_id],0);
										$color_total_iron+= $iron_data[$order_id][$color_id][$size_id];
										$color_size_iron[$size_id]+=$iron_data[$order_id][$color_id][$size_id];
									?>
                                    </td>
                                    <?
								}
								?>
                                <td align="right"><? echo number_format($color_total_iron,0); $grand_total_iron+=$color_total_iron;?></td>
							</tr>
							<?
							$i++;
						}
					}
					?>
                    <tr bgcolor="#CCCCCC" style="font-size:16px; font-weight:bold;">
                        <td colspan="<? echo $colspan; ?>" align="right">Total:</td>
                        <?
                        foreach($sizearr_order as $size_id)
						{
							?>
							<td align="right"><? echo number_format($color_size_iron[$size_id],0);?></td>
							<?
						}
						?>
                        <td align="right"><? echo number_format($grand_total_iron,0); ?></td>
                    </tr>
                </tbody>
            </table>
            <br />
            <?
			
		}
	}
	
	
	//Finish Data Display Here
	if($finishing_variable==1)
	{
		if(!empty($sql_finishing))
		{
			if($reportType==5  || $reportType==7) $tbl_width=350; else $tbl_width=200;
			?>
            <span style="font-size:18px; font-weight:bold;">Finishing  Reject Quantity</span>
            <table width="<? echo $tbl_width; ?>" cellspacing="0" border="1" class="rpt_table" rules="all" >
            	<thead>
                	<th width="50">SL</th>
                    <?
					if($reportType==5 || $reportType==7)
					{
						?>
                        <th width="100">PO Number</th>
                        <?
					}
					?>
                	<th>Reject Quantity</th>
                </thead>
                <tbody>
                	<?
					$i=1;
					foreach($sql_finishing as $row)
					{
						?>
                        <tr>
                            <td><? echo $i; ?></td>
                            <?
							if($reportType==5 || $reportType==7)
							{
								?>
								<td><? echo $po_array[$row[csf("po_break_down_id")]]; ?></td>
								<?
							}
							?>
                            <td align="right"><? echo number_format($row[csf("finish_rej_qnty")],2);?></td>
                        </tr>
                        <?
						$i++;
					}
					?>
                </tbody>
            </table>
            <br />
            <?
			
		}
	}
	else
	{
		$collspan=count($sizearr_order);
		if($reportType==5 || $reportType==7)
		{
			$table_width=(330+($collspan*60));
			$colspan=3;
		}
		else
		{
			$table_width=(230+($collspan*60));
			$colspan=2;
		}
		
		if(!empty($sql_finishing))
		{
			?>
            <span style="font-size:18px; font-weight:bold;">Finishing Reject Quantity</span>
            <table width="<? echo $table_width; ?>" cellspacing="0" border="1" class="rpt_table" rules="all" >
            	<thead>
                	<th width="50">SL</th>
                    <?
					if($reportType==5 || $reportType==7)
					{
						?>
                        <th width="100">PO Number</th>
                        <?
					}
					?>
                    <th width="100">Color</th>
                    <?
					foreach($sizearr_order as $size_id)
					{
						?>
                        <th width="60"><? echo $sizearr[$size_id]; ?></th>
                        <?
					}
					?>
                    <th>Color Total</th>
                </thead>
                <tbody>
                	<?
					$i=1;$grand_total_finish=0;
					foreach($finishing_data as $order_id=>$value)
					{
						foreach($value as $color_id=>$val)
						{
							?>
							<tr>
								<td><? echo $i; ?></td>
                                <?
								if($reportType==5 || $reportType==7)
								{
									if(!in_array($order_id,$temp_finish_arr))
									{
										$temp_finish_arr[]=$order_id;
										?>
										<td><? echo $po_array[$order_id]; ?></td>
										<?
									}
									else
									{
										?>
                                        <td>&nbsp;</td>
                                        <?
									}
								}
								?>
                                <td><? echo $color_Arr_library[$color_id]; ?></td>
                                <?
								$color_total_finish=0;
								foreach($sizearr_order as $size_id)
								{
									?>
                                    <td align="right">
									<? 
										echo number_format($finishing_data[$order_id][$color_id][$size_id],0);
										$color_total_finish+= $finishing_data[$order_id][$color_id][$size_id];
										$color_size_finish[$size_id]+=$finishing_data[$order_id][$color_id][$size_id];
									?>
                                    </td>
                                    <?
								}
								?>
                                <td align="right"><? echo number_format($color_total_finish,0); $grand_total_finish+=$color_total_finish;?></td>
							</tr>
							<?
							$i++;
						}
					}
					?>
                    <tr bgcolor="#CCCCCC" style="font-size:16px; font-weight:bold;">
                        <td colspan="<? echo $colspan; ?>" align="right">Total:</td>
                        <?
                        foreach($sizearr_order as $size_id)
						{
							?>
							<td align="right"><? echo number_format($color_size_finish[$size_id],0);?></td>
							<?
						}
						?>
                        <td align="right"><? echo number_format($grand_total_finish,0); ?></td>
                    </tr>
                </tbody>
            </table>
            <?
			
		}
	}
	?>
    </div>
    
    <?
	exit();
	
	 
	 /*if($reportType==1 || $reportType==5)
	{
		$location_cond=""; 
		$floor_cond="";
		$country_cond="";
	}
	else if($reportType==2)
	{
		$location_cond=" and location=$location_id"; 
		$floor_cond=" and floor_id=$floor_id";
		$country_cond="";
	}
	else if($reportType==3)
	{
		$location_cond=""; 
		$floor_cond="";
		$country_cond=" and country_id='$country_id'";	
	}
	else
	{
		$location_cond=" and location=$location_id"; 
		$floor_cond=" and floor_id=$floor_id";
		$country_cond=" and country_id='$country_id'";	
	}
	$sql_qry="Select sum(CASE WHEN production_type ='1' THEN reject_qnty ELSE 0 END) AS cutting_rej_qnty,
					sum(CASE WHEN production_type ='8' THEN reject_qnty ELSE 0 END) AS finish_rej_qnty,
					sum(CASE WHEN production_type ='5' THEN reject_qnty ELSE 0 END) AS sewingout_rej_qnty
					from pro_garments_production_mst 
					where po_break_down_id in ($po_id) and item_number_id='$item_id' and status_active=1 and is_deleted=0 $location_cond $floor_cond $country_cond group by po_break_down_id";
	
	
	?>
     <div style="width:500px;" align="center"> 
       <table width="490" cellspacing="0" border="1" class="rpt_table" rules="all" > 
             <thead>
             	<tr>
                	<th colspan="5">Reject Qty Details</th>
                </tr>
                <tr>
                    <th width="30">SL</th>
                    <th width="110">Cutting Reject Qty</th>
                    <th width="110">Sewing Out Reject Qty</th>
                    <th width="110">Finish Reject Qty.</th>
                    <th width="110">Total Reject Qty.</th>
                 </tr>
              </thead>
              <tbody> 
			 <?
			 
			
			//echo $sql_qry;
			$sql_result=sql_select($sql_qry);

			$i=1;	 
			foreach($sql_result as $row)
			{
			 	if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; 
			?>
                 <tr bgcolor="<? echo $bgcolor; ?>">
                    <td><? echo $i; ?></td>
                    <td align="right"><? echo $row[csf('cutting_rej_qnty')]; ?>&nbsp;</td>
                    <td align="right"><? echo $row[csf('sewingout_rej_qnty')]; ?>&nbsp;</td>
                    <td align="right"><? echo $row[csf('finish_rej_qnty')]; ?>&nbsp;</td>
                    <td align="right"><? $total_reject=$row[csf('cutting_rej_qnty')]+$row[csf('sewingout_rej_qnty')]+$row[csf('finish_rej_qnty')]; echo $total_reject; ?>&nbsp;</td>
                 </tr>   
             <? 
			  	$i++; 
			 } 
			 ?> 
             </tbody>
         </table>
     </div>    
	<?
	*/
}

//cutting-1,sewing ouput-5--------------------popup-----------//
if ($action=="challanPopup") 
{
 	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,1,1);
	
	if($country_id=='') $country_cond=""; else $country_cond=" and country_id='$country_id'";
	
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
			document.getElementById('scroll_body').style.maxHeight="260px";
		}	
		
	</script>
    <div style="width:530px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
    <fieldset style="width:530px; margin-left:5px">
        <div id="report_container">
            <table width="500" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1" >
                <thead>
                    <th width="50">Sl.</th>    
                    <th width="80">Production Date</th>
                    <th width="80">Unit/ Floor</th>
                    <th width="140">Challan No</th>
                    <th>Quantity</th>
                </thead>
            </table>
            <div style="max-height:260px; overflow-y:scroll; width:520px;" id="scroll_body">
                <table cellspacing="0" border="1" class="rpt_table" width="500" rules="all" id="table_body" >
                <?
					$floorArr=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name"); 
					 $i=1; $total_quantity=0; $location="";$floor="";
					 if($dateOrLocWise==2 || $dateOrLocWise==4) // only for location floor and line wise
					 {
						 if($location_id!="") $location=" and location=$location_id";
						 if($floor_id!="") $floor=" and floor_id=$floor_id";
					 }
					 
					 $sql=sql_select("select production_date, challan_no, floor_id, SUM(production_quantity) as production_quantity from pro_garments_production_mst where po_break_down_id in ($po_break_down_id) and item_number_id=$item_id and production_type=$prod_type and status_active=1 and production_date='$prod_date' $location $floor $country_cond group by production_date, challan_no, floor_id");
					foreach($sql as $resultRow)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    ?>
                         <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                            <td width="50"><? echo $i;?></td>
                            <td width="80" align="center"><? echo change_date_format($resultRow[csf("production_date")]); ?></td>
                            <td width="80" align="center"><? echo $floorArr[$resultRow[csf("floor_id")]]; ?></td>
                            <td width="140"><? echo $resultRow[csf("challan_no")]; ?>&nbsp;</td>
                            <td align="right" style="padding-right:2px"><? echo number_format($resultRow[csf("production_quantity")]); ?></td>
                         </tr>	
                        <?	
                        $total_sewing_quantity+=$resultRow[csf("production_quantity")];
                        $i++;
                    }
                    ?>
                   <tfoot class="tbl_bottom">
                        <td>&nbsp;</td> 
                        <td>&nbsp;</td>
                        <td>&nbsp;</td> 
                        <td align="right">Total</td> 
                        <td align="right" style="padding-right:2px"><? echo number_format($total_sewing_quantity); ?></td>
					</tfoot>
                </table>
            </div>
        </div>
	</fieldset>
    <?
 exit();
 
}

if($action=="update_tna_progress_comment")
{
	//echo load_html_head_contents("TNA Info","../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	
	if($db_type==0) $blank_date="0000-00-00"; else $blank_date="";

	$tna_task_arr=return_library_array( "select task_name, task_short_name from lib_tna_task",'task_name','task_short_name');
	
	$tna_task_id=array(); $plan_start_array=array(); $plan_finish_array=array();
	$actual_start_array=array();
	$actual_finish_array=array();
	
	$notice_start_array=array();
	$notice_finish_array=array();
	//<a onclick="progress_comment_popup('6337','1','1')" href="##">582263-1686</a>
	
	$task_sql= sql_select("select a.task_number,a.task_start_date,a.task_finish_date,a.actual_start_date,a.actual_finish_date,a.notice_date_start,a.notice_date_end from tna_process_mst a, lib_tna_task b where a.task_number=b.task_name and a.template_id=$template_id and a.po_number_id=$po_id and b.status_active=1 and b.is_deleted=0 order by b.task_sequence_no asc");
	foreach ($task_sql as $row_task)
	{
		$tna_task_id[]=$row_task[csf("task_number")];
		
		$plan_start_array[$row_task[csf("task_number")]] =$row_task[csf("task_start_date")];
		$plan_finish_array[$row_task[csf("task_number")]]=$row_task[csf("task_finish_date")];
		
		$actual_start_array[$row_task[csf("task_number")]] =$row_task[csf("actual_start_date")];
		$actual_finish_array[$row_task[csf("task_number")]]=$row_task[csf("actual_finish_date")];
		
		$notice_start_array[$row_task[csf("task_number")]] =$row_task[csf("notice_date_start")];
		$notice_finish_array[$row_task[csf("task_number")]]=$row_task[csf("notice_date_end")];
	}
	
	
	
	$comments_array=array(); $responsible_array=array();
	$res_comm_sql= sql_select("select task_id, comments, responsible from tna_progress_comments where tamplate_id=$template_id and order_id=$po_id");
	foreach ($res_comm_sql as $row_res_comm)
	{
		$comments_array[$row_res_comm[csf("task_id")]] =$row_res_comm[csf("comments")];
		$responsible_array[$row_res_comm[csf("task_id")]]=$row_res_comm[csf("responsible")];
	}
	
	$execution_time_array=array();
	$execution_time_sql= sql_select("select tna_task_id, execution_days from tna_task_template_details where task_template_id=$template_id");
	foreach ($execution_time_sql as $row_execution_time)
	{
		$execution_time_array[$row_execution_time[csf("tna_task_id")]] =$row_execution_time[csf("execution_days")];
	}
	
	$lead_time=return_library_array("select task_template_id,lead_time from tna_task_template_details group by task_template_id,lead_time","task_template_id","lead_time");
?>


	<fieldset style="width:1010px"> 
        <div class="form_caption" align="center"><strong>TNA Progress Comment</strong></div>
        <table style="margin-top:10px" width="1000" border="1" rules="all" class="rpt_table">
            <?php
			$sql ="select b.company_name,b.buyer_name,a.po_number,b.job_no,b.style_ref_no,b.gmts_item_id,a.po_received_date,a.shipment_date from wo_po_break_down a,wo_po_details_master b where a.id=$po_id and a.job_no_mst=b.job_no";
			$result=sql_select($sql);
            foreach($result as $row)
            {
            ?>
            	<thead>
                    <tr bgcolor="#E9F3FF">
                        <th width="130">Company</th>
                        <td width="196" style="padding-left:5px"><?php echo $company_short_name_arr[$row[csf('company_name')]];  ?></td>
                        <th width="130">Buyer</th>
                        <td width="186" style="padding-left:5px"><?php echo $buyer_short_name_arr[$row[csf('buyer_name')]];  ?></td>
                        <th width="130">Order No</th>
                        <td width="186" style="padding-left:5px"><p><?php echo $row[csf('po_number')]; ?></p></td>
                    </tr>
                    <tr bgcolor="#FFFFFF">
                        <th>Style Ref.</th>
                        <td style="padding-left:5px"><p><?php echo $row[csf('style_ref_no')]; ?></p></td>
                        <th>RMG Item</th>
                        <td style="padding-left:5px"><p><?php echo $garments_item[$row[csf('gmts_item_id')]]; ?></p></td>
                        <th>Order Recv. Date</th>
                        <td style="padding-left:5px"><?php echo change_date_format($row[csf('po_received_date')]); ?></td>
                    </tr>
                    <tr bgcolor="#E9F3FF">
                        <th>Ship Date</th>
                        <td style="padding-left:5px"><?php echo change_date_format($row[csf('shipment_date')]); ?></td>
                        <th>Lead Time</th>
                        <td style="padding-left:5px">
                            <?
								$template_id=str_replace("'","",$template_id);
								if(str_replace("'","",$tna_process_type)==1)
								{
									$lead_timee=$lead_time[$template_id];
								}
								else
								{
									$lead_timee=$template_id;
								}
								//echo $lead_time=return_field_value("lead_time","tna_task_template_details", "task_template_id='$template_id' and status_active=1 and is_deleted=0");
								echo $lead_timee;
							?>
                        </td>
                        <th>Job Number</th>
                        <td style="padding-left:5px">
							<? echo $row[csf('job_no')];   ?>
                        </td>
                    </tr>
                </thead>
            <?php
            }
            ?>
        </table>
        <table style="margin-top:5px" cellpadding="0" width="1000" class="rpt_table" rules="all" border="1">
            <thead>
                <th width="50">Task No</th>
                <th width="150">Task Name</th>
                <th width="60">Allowed Days</th>
                <th width="80">Plan Start Date</th>
                <th width="80">Plan Finish Date</th>
                <th width="80">Actual Start Date</th>
                <th width="80">Actual Finish Date</th>
                <th width="80">Start Delay/ Early By</th>
                <th width="80">Finish Delay/ Early By</th>
                <th width="100">Responsible</th>
                <th>Comments</th>
            </thead> 	 	
        </table>
        
          
        
            <table cellpadding="0" width="1000" cellspacing="0" border="1" rules="all" class="rpt_table">
                <? 
				
				
				$i=1;
                foreach($tna_task_id as $key)
                { 
                    if($i%2==0) $trcolor="#E9F3FF"; else $trcolor="#FFFFFF";
					
					$bgcolor1=""; $bgcolor="";
									
					if ($plan_start_array[$key]!=$blank_date) 
					{
						if (strtotime($notice_start_array[$key])<=strtotime(date("Y-m-d",time())) && strtotime(date("Y-m-d",time()))<=strtotime($plan_start_array[$key]))  $bgcolor="#FFFF00";
						else if (strtotime($plan_start_array[$key])<strtotime(date("Y-m-d",time())))  $bgcolor="#FF0000";
						else $bgcolor="";
						
					}
					 
					if ($plan_finish_array[$key]!=$blank_date) {
						if (strtotime($notice_finish_array[$key])<=strtotime(date("Y-m-d",time())) && strtotime(date("Y-m-d",time()))<=strtotime($plan_finish_array[$key]))  $bgcolor1="#FFFF00";
						else if (strtotime($plan_finish_array[$key])<strtotime(date("Y-m-d",time())))  $bgcolor1="#FF0000"; else $bgcolor1="";
					}
					
					if ($actual_start_array[$key]!=$blank_date) $bgcolor="";
					if ($actual_finish_array[$key]!=$blank_date) $bgcolor1="";
					
					// Delay / Early............
									
					$bgcolor5=""; $bgcolor6="";
					$delay=""; $early="";
					
					if($actual_start_array[$key]!=$blank_date)
					{
						$start_diff1 = datediff( "d", $actual_start_array[$key], $plan_start_array[$key]);
						$finish_diff1 = datediff( "d", $actual_finish_array[$key], $plan_finish_array[$key]);
						
						$start_diff=$start_diff1-1;
						$finish_diff=$finish_diff1-1;
						
						if($start_diff<0)
						{
							$bgcolor5="#2A9FFF";	//Blue
							$start="(Delay)";
						}
						if($start_diff>0)
						{
							$bgcolor5="";
							$start="(Early)";
							
						}
						if($finish_diff<0)
						{
							$bgcolor6="#2A9FFF";
							$finish="(Delay)";
						}
						if($finish_diff>0)
						{	
							$bgcolor6="";
							$finish="(Early)";
						}
						
						
					}
					else
					{
						if(date("Y-m-d")>$plan_start_array[$key])
						{
							$start_diff1 = datediff( "d", $plan_start_array[$key], date("Y-m-d"));
							$start_diff=$start_diff1-1;
							$bgcolor5="#FF0000";		//Red
							$start="(Delay)";
						}
						if(date("Y-m-d")>$plan_finish_array[$key])
						{
							$finish_diff1 = datediff( "d", $plan_finish_array[$key], date("Y-m-d"));
							$finish_diff=$finish_diff1-1;
							$bgcolor6="#FF0000";
							$finish="(Delay)";
						}
						if(date("Y-m-d")<=$plan_start_array[$key])
						{
							$start_diff = "";
							$bgcolor5="";
							$start="(Ac. Start Dt. Not Found)";
						}
						if(date("Y-m-d")<=$plan_finish_array[$key])
						{
							$finish_diff = "";
							$bgcolor6="";
							$finish="(Ac. Finish Dt. Not Found)";
							
						}
					}
							
                    ?>
                    <tr bgcolor="<? echo $trcolor; ?>" id="tr_<? echo $i; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')">
                        <td align="center" width="50"><? echo $i; ?></td>
                        <td width="150"><? echo $tna_task_arr[$key]; ?></td>
                        <td align="center" width="60"><? echo $execution_time_array[$key]; ?></td>
                        <td align="center" width="80"><? echo change_date_format($plan_start_array[$key]); ?>&nbsp;</td>
                        <td align="center" width="80"><? echo change_date_format($plan_finish_array[$key]); ?>&nbsp;</td>
                        <td align="center" width="80" bgcolor="<? echo $bgcolor;  ?>">
                            <? 
                                if($actual_start_array[$key]=="0000-00-00" || $actual_start_array[$key]=="") echo "&nbsp;";
                                else echo change_date_format($actual_start_array[$key]);
                            ?>
                        </td>
                        <td align="center" width="80" bgcolor="<? echo $bgcolor1;  ?>">
                            <?  
                                 if($actual_finish_array[$key]=="0000-00-00" || $actual_finish_array[$key]=="") echo "&nbsp;";
                                 else echo change_date_format($actual_finish_array[$key]);
                            ?>
                        </td>
                        <td align="center" width="80" bgcolor="<? echo $bgcolor5;  ?>">
							<?  
                                echo $start_diff." ".$start;
                            ?>
                        </td>
                        <td align="center" width="80" bgcolor="<? echo $bgcolor6;  ?>">
                            <?  
                                echo $finish_diff." ".$finish;
                            ?>
                        </td>
                        <td width="100"><p><?php echo $responsible_array[$key]; ?>&nbsp;</p></td>
                        <td><p><?php echo $comments_array[$key]; ?>&nbsp;</p></td>
                    </tr>
              	<? 
                    $i++;
                }
                ?>
            </table>
    </fieldset>
<?
exit();
}

if($action=="show_image")
{
	echo load_html_head_contents("Set Entry","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	//echo "select image_location  from common_photo_library  where master_tble_id='$job_no' and form_name='knit_order_entry' and is_deleted=0 and file_type=1";
	$data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$job_no' and form_name='knit_order_entry' and is_deleted=0 and file_type=1");
	
	?>
    <table>
    <tr>
    <?
    foreach ($data_array as $row)
	{ 
	?>
    <td><img src='../../../<? echo $row[csf('image_location')]; ?>' height='250' width='300' /></td>
    <?
	}
	?>
    </tr>
    </table>
    
    <?
	exit();
}
?>