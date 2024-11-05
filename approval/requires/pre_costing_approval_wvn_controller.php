<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
extract($_REQUEST);
$permission=$_SESSION['page_permission'];

include('../../includes/common.php');
include('../../includes/class4/class.conditions.php');
include('../../includes/class4/class.reports.php');
include('../../includes/class4/class.fabrics.php');
include('../../includes/class4/class.emblishments.php');
include('../../includes/class4/class.washes.php');
//include('../../includes/class4/class.yarns.php');
include('../../includes/class4/class.conversions.php');

$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$menu_id=$_SESSION['menu_id'];

$userCredential = sql_select("SELECT unit_id as company_id, brand_id FROM user_passwd where id=$user_id");

$brand_id = $userCredential[0][csf('brand_id')];
$userbrand_idCond="";

if ($brand_id !='') {
    $userbrand_idCond = " and id in ( $brand_id)";
}

if($db_type==0) $year_cond="SUBSTRING_INDEX(a.insert_date, '-', 1) as year";
else if($db_type==2) $year_cond="to_char(a.insert_date,'YYYY') as year";

if($db_type==0) $year_cond_groupby="SUBSTRING_INDEX(a.insert_date, '-', 1)";
else if($db_type==2) $year_cond_groupby="to_char(a.insert_date,'YYYY')";

if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "load_drop_down( 'requires/pre_costing_approval_wvn_controller', this.value, 'load_drop_down_brand', 'brand_td'); load_drop_down('requires/pre_costing_approval_wvn_controller', this.value, 'load_drop_down_season', 'season_td');" );
	exit();
}

if ($action=="load_drop_down_season")
{
	echo create_drop_down( "cbo_season_id", 80, "select id, season_name from lib_buyer_season where buyer_id='$data' and status_active =1 and is_deleted=0 order by season_name ASC","id,season_name", 1, "-Select Season-", "", "" );
	exit();
}

if ($action=="load_drop_down_brand")
{
	echo create_drop_down( "cbo_brand", 80, "select id, brand_name from lib_buyer_brand where buyer_id='$data' and status_active =1 and is_deleted=0 $userbrand_idCond order by brand_name ASC","id,brand_name", 1, "-Brand-", $selected, "" );
	exit();
}

if($action=="load_drop_down_buyer_new_user")
{
	$data=explode("_",$data);
	//	echo "SELECT password,user_level,id,access_ip,buyer_id,unit_id,is_data_level_secured FROM user_passwd WHERE user_name = '$data[1]' AND valid = 1";die;
	$log_sql = sql_select("SELECT user_level,buyer_id,unit_id,is_data_level_secured FROM user_passwd WHERE id = '$data[1]' AND valid = 1");
	//print_r($log_sql);die;
	foreach($log_sql as $r_log)
	{
		if($r_log[csf('IS_DATA_LEVEL_SECURED')]==1)
		{
			if($r_log[csf('BUYER_ID')]!="") $buyer_cond=" and buy.id in (".$r_log[csf('BUYER_ID')].")"; else $buyer_cond="";
		}
		else $buyer_cond="";
	}
	echo create_drop_down( "cbo_buyer_name", 152, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );
	exit();
}


if ($action=="populate_cm_compulsory")
{
	$cm_cost_compulsory=return_field_value("cm_cost_compulsory","variable_order_tracking","company_name ='".$data."' and variable_list=22 and is_deleted=0 and status_active=1");
	echo $cm_cost_compulsory;
	exit();
}
$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
$user_arr=return_library_array( "select id, user_name from user_passwd", "id", "user_name"  );
$brand_arr=return_library_array( "select id, brand_name from lib_buyer_brand", "id", "brand_name"  );
$brand_arr[0]=0;

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
 
 	if(str_replace("'","",$cbo_season_id)!=0)
	{
		$season_con=" and a.SEASON_BUYER_WISE=$cbo_season_id";
	}

 	if(str_replace("'","",$txt_styleref)!="")
	{
		$style_con=" and a.style_ref_no like('%".trim(str_replace("'","",$txt_styleref))."%')";
	}



	$sequence_no='';
	$company_name=str_replace("'","",$cbo_company_name);

	//echo $txt_internal_ref; echo $txt_file_no;
	$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
	if($txt_alter_user_id!="")
	{
		
		$log_sql = sql_select("SELECT user_level, buyer_id, unit_id,BRAND_ID, is_data_level_secured FROM user_passwd WHERE id = '$txt_alter_user_id' AND valid = 1");
		
		
		if(str_replace("'","",$cbo_buyer_name)==0)
		{
			foreach($log_sql as $r_log)
			{
				if($r_log[csf('is_data_level_secured')]==1)
				{
					if($r_log[csf('buyer_id')]!="") $buyer_id_cond=" and a.buyer_name in (".$r_log[csf('buyer_id')].")"; else $buyer_id_cond="";
				}
				else $buyer_id_cond="";
			}
		}
		else $buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";
		
		
		
		if(str_replace("'","",$cbo_brand)==0)
		{
			foreach($log_sql as $r_log)
			{
				if($r_log[csf('is_data_level_secured')]==1)
				{
					if($r_log[BRAND_ID]!="") $brand_id_cond=" and a.BRAND_ID in (".$r_log[BRAND_ID].")"; else $brand_id_cond="";
				}
				else $brand_id_cond="";
			}
		}
		else {$brand_id_cond=" and a.BRAND_ID=$cbo_brand";}
		
		$user_id=$txt_alter_user_id;
	}
	else
	{
		if(str_replace("'","",$cbo_buyer_name)==0)
		{
			if ($_SESSION['logic_erp']["data_level_secured"]==1)
			{
				if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
			}
			else $buyer_id_cond="";
		}
		else $buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";
		
		
		
		if(str_replace("'","",$cbo_brand)==0)
		{
			if ($_SESSION['logic_erp']["data_level_secured"]==1)
			{
				if($_SESSION['logic_erp']["brand_id"]!="") $brand_id_cond=" and a.BRAND_ID in (".$_SESSION['logic_erp']["brand_id"].")"; else $brand_id_cond="";
			}
			else $brand_id_cond="";
		}
		else{$brand_id_cond=" and a.BRAND_ID=$cbo_brand";}
		
	}
	$job_no=str_replace("'","",$txt_job_no);
	$file_no=str_replace("'","",$txt_file_no);
	$internal_ref=str_replace("'","",$txt_internal_ref);
	$job_year=str_replace("'","",$cbo_year);

	if ($file_no=="") $file_no_cond=""; else $file_no_cond=" and d.file_no='".trim($file_no)."' ";
	if ($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and d.grouping='".trim($internal_ref)."' ";
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num='".trim($job_no)."' ";
	if ($job_year=="" || $job_year==0) $job_year_cond="";
	else
	{
		if($db_type==2) $job_year_cond=" and to_char(a.insert_date,'YYYY')='".trim($job_year)."' ";
		else $job_year_cond=" and YEAR(a.insert_date)='".trim($job_year)."' ";
	}

	$date_cond='';
	if(str_replace("'","",$txt_date)!="")
	{
		if(str_replace("'","",$cbo_get_upto)==1) $date_cond=" and b.costing_date>$txt_date";
		else if(str_replace("'","",$cbo_get_upto)==2) $date_cond=" and b.costing_date<=$txt_date";
		else if(str_replace("'","",$cbo_get_upto)==3) $date_cond=" and b.costing_date=$txt_date";
		else $date_cond='';
	}

	$approval_type=str_replace("'","",$cbo_approval_type);
	if($previous_approved==1 && $approval_type==1) $previous_approved_type=1;
	//$user_id=133;
	$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id and is_deleted=0");
	$min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0");
	$buyer_ids_array = array();
	$buyerData = sql_select("select user_id, sequence_no,BRAND_ID, buyer_id from electronic_approval_setup where company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0 ");
	foreach($buyerData as $row)
	{
		
		if($row[csf('buyer_id')]==''){$row[csf('buyer_id')]=implode(',',array_keys($buyer_arr));}
		if($row[csf('BRAND_ID')]==''){$row[csf('BRAND_ID')]=implode(',',array_keys($brand_arr));}
		
		$buyer_ids_array[$row[csf('user_id')]]['u']=$row[csf('buyer_id')];
		$buyer_ids_array[$row[csf('sequence_no')]]['s']=$row[csf('buyer_id')];
		$brand_ids_array[$row[csf('user_id')]]['u']=$row[csf('BRAND_ID')];
		$brand_ids_array[$row[csf('sequence_no')]]['s']=$row[csf('BRAND_ID')];
	}
	
	//print_r($buyer_ids_array);die;
	
	if($user_sequence_no=="")
	{
		echo "<font style='color:#F00; font-size:14px; font-weight:bold'>You Have No Authority To Sign Pre-Costing.</font>";
		die;
	}
	if($db_type==2)
	{
		$internalRefCond="rtrim(xmlagg(xmlelement(e,d.grouping,',').extract('//text()') order by d.grouping).GetClobVal(),',')"; 
		$fileNoCond="rtrim(xmlagg(xmlelement(e,d.file_no,',').extract('//text()') order by d.file_no).GetClobVal(),',')"; 
	}
	else 
	{
		$internalRefCond="group_concat(d.grouping)";
		$fileNoCond="group_concat(d.file_no)";
	}

	  //echo $previous_approved.'--'.$approval_type;die;
	if($previous_approved==1 && $approval_type==1)
	{
		$sequence_no_cond=" and c.sequence_no<'$user_sequence_no'";
		$sql="select b.costing_per,b.id, a.quotation_id, a.job_no_prefix_num, $year_cond, a.id as job_id, a.job_no, a.buyer_name,a.brand_id, a.SEASON_BUYER_WISE as season, a.season_year, a.style_ref_no, b.costing_date, b.approved, b.inserted_by, c.id as approval_id, c.sequence_no, c.approved_by, c.id as approval_id, min(d.shipment_date) as minship_date, max(d.shipment_date) as maxship_date, a.job_quantity, (a.job_quantity*a.total_set_qnty) as job_qty_pcs, a.total_price, $internalRefCond as internalRef, $fileNoCond as fileNo
			      from wo_pre_cost_mst b, wo_po_details_master a, approval_history c, wo_po_break_down d
				  where b.id=c.mst_id and c.entry_form=15 and a.job_no=b.job_no and a.company_name=$company_name and a.job_no=d.job_no_mst  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and c.current_approval_status=1 and b.ready_to_approved=1 and b.is_deleted=0 and b.approved in (1,3) $buyer_id_cond $brand_id_cond $style_con $season_con $date_cond $job_no_cond $file_no_cond $internal_ref_cond $sequence_no_cond group by b.costing_per,b.id, a.quotation_id, a.job_no_prefix_num, $year_cond_groupby, a.id, a.job_no, a.buyer_name,a.brand_id, a.SEASON_BUYER_WISE, a.season_year, a.style_ref_no, b.costing_date, b.approved, b.inserted_by, c.id, c.sequence_no, c.approved_by, c.id, a.job_quantity, a.total_set_qnty, a.total_price";
		//$buyer_id_cond
	}
	else if($approval_type==2)
	{
		/*		if($db_type==0)
				{
					$sequence_no = return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and bypass = 2 and buyer_id='' and is_deleted=0","seq");
				}
				else
				{
					$sequence_no = return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and bypass=2 and buyer_id is null and is_deleted=0","seq");
				}
		*/		
		if($db_type==0)
		{
			$sequence_no = return_field_value("max(sequence_no) as seq","electronic_approval_setup"," page_id=$menu_id and sequence_no<$user_sequence_no and bypass = 2 and is_deleted=0","seq");
		}
		else
		{
			$sequence_no = return_field_value("max(sequence_no) as seq","electronic_approval_setup"," page_id=$menu_id and sequence_no<$user_sequence_no and bypass=2 and is_deleted=0","seq");
		}
		
		
		
		
		//echo $user_sequence_no.'--'.$min_sequence_no.'--'.$sequence_no; die;
		
		
		if($user_sequence_no==$min_sequence_no)
		{
			$buyer_ids = $buyer_ids_array[$user_id]['u'];
			if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.buyer_name in($buyer_ids)";

			$brand_ids = $brand_ids_array[$user_id]['u'];
			if($brand_ids=="") $brand_id_cond2=""; else $brand_id_cond2=" and a.BRAND_ID in($brand_ids)";

			 //$sql="select b.id,a.quotation_id,a.job_no_prefix_num,$year_cond,a.job_no, a.buyer_name, a.style_ref_no,b.costing_date,'0' as approval_id, b.approved,b.inserted_by from wo_pre_cost_mst b,  wo_po_details_master a where a.job_no=b.job_no and a.company_name=$company_name and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and b.ready_to_approved=1 and b.approved=2 $buyer_id_cond $buyer_id_cond2 $date_cond $job_no_cond $job_year_cond";

			$sql="select b.costing_per,b.id, a.quotation_id, a.job_no_prefix_num, $year_cond, a.id as job_id, a.job_no, a.buyer_name,a.brand_id, a.SEASON_BUYER_WISE as season, a.season_year, a.style_ref_no, b.costing_date, '0' as approval_id, b.approved, b.inserted_by, b.entry_from, min(d.shipment_date) as minship_date, max(d.shipment_date) as maxship_date, a.job_quantity, (a.job_quantity*a.total_set_qnty) as job_qty_pcs, a.total_price, $internalRefCond as internalRef, $fileNoCond as fileNo from wo_pre_cost_mst b,  wo_po_details_master a ,wo_po_break_down d  where a.job_no=b.job_no and a.job_no=d.job_no_mst and a.company_name=$company_name and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and b.ready_to_approved=1 and b.approved in(3,2) $buyer_id_cond $buyer_id_cond2 $brand_id_cond2 $date_cond $job_no_cond $job_year_cond $internal_ref_cond $file_no_cond $brand_id_cond $style_con $season_con group by b.id, b.costing_per,a.quotation_id, a.job_no_prefix_num, $year_cond_groupby,  a.id, a.job_no, a.buyer_name,a.brand_id, a.SEASON_BUYER_WISE , a.season_year, a.style_ref_no, b.costing_date, '0', b.approved, b.inserted_by, b.entry_from, a.job_quantity, a.total_set_qnty, a.total_price";
			 //echo $sql;die;
		}
		else if($sequence_no == "")
		{
			$buyer_ids=$buyer_ids_array[$user_id]['u'];
			if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.buyer_name in($buyer_ids)";
			if($buyer_ids=="") $buyer_id_cond3=""; else $buyer_id_cond3=" and c.buyer_name in($buyer_ids)";

			$brand_ids=$brand_ids_array[$user_id]['u'];
			if($brand_ids=="") $brand_id_cond2=""; else $brand_id_cond2=" and a.brand_id in($brand_ids)";
			if($brand_ids=="") $brand_id_cond3=""; else $brand_id_cond3=" and c.brand_id in($brand_ids)";

			
			$seqSql="select sequence_no, bypass, buyer_id,brand_id from electronic_approval_setup where company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and is_deleted=0 order by sequence_no desc";
			//echo $seqSql; die;
			$seqData=sql_select($seqSql);

			$buyerIds=''; $sequence_no_by_yes=''; $sequence_no_by_no=''; $approved_string=''; $check_buyerIds_arr=array();
			foreach($seqData as $sRow)
			{
				if($sRow[csf('bypass')]==2)
				{
					$sequence_no_by_no.=$sRow[csf('sequence_no')].",";
					if($sRow[csf('buyer_id')]!="")
					{
						$buyerIds.=$sRow[csf('buyer_id')].",";
						$buyer_id_arr=explode(",",$sRow[csf('buyer_id')]);
						$result=array_diff($buyer_id_arr,$check_buyerIds_arr);
						if(count($result)>0)
						{
							$query_string.=" (b.sequence_no=".$sRow[csf('sequence_no')]." and c.buyer_name in(".implode(",",$result).")) or ";
						}
						$check_buyerIds_arr=array_unique(array_merge($check_buyerIds_arr,$buyer_id_arr));
					}
					
					
					if($sRow[csf('brand_id')]!="")
					{
						
						$brandIds.=$sRow[csf('brand_id')].",";
						$brand_id_arr=explode(",",$sRow[csf('brand_id')]);
						$brandResult=array_diff($brand_id_arr,$check_brandIds_arr); 
						if(count($brandResult)>0)
						{
							$brand_query_string.=" (b.sequence_no=".$sRow[csf('sequence_no')]." and a.brand_id in(".implode(",",$brandResult).")) or ";
						}
						$check_brandIds_arr=array_unique(array_merge($check_brandIds_arr,$brand_id_arr));
					}
				}
				else $sequence_no_by_yes.=$sRow[csf('sequence_no')].",";
				
				
				
				
			}

			$buyerIds=chop($buyerIds,',');
			if($buyerIds=="")
			{
				$buyerIds_cond=""; $seqCond="";
			}
			else
			{
				$buyerIds_cond=" and a.buyer_name not in($buyerIds)"; 
				$seqCond=" and (".chop($query_string,'or ').")";
			}
			
			
			$brandIds=chop($brandIds,',');
			if($brandIds=="")
			{
				$brandIds_cond=""; $brand_seqCond="";
			}
			else
			{
				$buyerIds_cond="";//buyer not in esc if brand id found
				$brandIds_cond=" and a.brand_id not in($brandIds)"; 
				$brand_seqCond=" and (".chop($brand_query_string,'or ').")";
			}
			
			//print_r($buyerIds_cond); 
			//print_r($brandIds_cond);die;
			
			$sequence_no_by_no=chop($sequence_no_by_no,',');
			$sequence_no_by_yes=chop($sequence_no_by_yes,',');

			if($sequence_no_by_yes=="") $sequence_no_by_yes=0;
			if($sequence_no_by_no=="") $sequence_no_by_no=0;

			$pre_cost_id='';
			$pre_cost_id_sql="select distinct (mst_id) as pre_cost_id from wo_pre_cost_mst a, approval_history b, wo_po_details_master c where a.id=b.mst_id and a.job_no=c.job_no and c.company_name=$company_name and b.sequence_no in ($sequence_no_by_no) and b.entry_form=15 and b.current_approval_status=1 $buyer_id_cond3 $brand_id_cond3 $seqCond
			union
			select distinct (mst_id) as pre_cost_id from wo_pre_cost_mst a, approval_history b, wo_po_details_master c where a.id=b.mst_id and a.job_no=c.job_no and c.company_name=$company_name and b.sequence_no in ($sequence_no_by_yes) and b.entry_form=15 and b.current_approval_status=1 $buyer_id_cond3 $brand_id_cond3 ";
			  //echo $pre_cost_id_sql;die;
			$bResult=sql_select($pre_cost_id_sql);
			//print_r($bResult);die;
			
			foreach($bResult as $bRow)
			{
				$pre_cost_id.=$bRow[csf('pre_cost_id')].",";
			}

			$pre_cost_id=chop($pre_cost_id,',');

			$pre_cost_id_app_sql=sql_select("select b.mst_id as pre_cost_id from wo_pre_cost_mst a, approval_history b, wo_po_details_master c
			where a.id=b.mst_id and a.job_no=c.job_no and c.company_name=$company_name and b.sequence_no=$user_sequence_no and b.entry_form=15 and a.ready_to_approved=1 and b.current_approval_status=1");

			foreach($pre_cost_id_app_sql as $inf)
			{
				if($pre_cost_id_app_byuser!="") $pre_cost_id_app_byuser.=",".$inf[csf('pre_cost_id')];
				else $pre_cost_id_app_byuser.=$inf[csf('pre_cost_id')];
			}

			$pre_cost_id_app_byuser=implode(",",array_unique(explode(",",$pre_cost_id_app_byuser)));

			$pre_cost_id_app_byuser=chop($pre_cost_id_app_byuser,',');
			$result=array_diff(explode(',',$pre_cost_id),explode(',',$pre_cost_id_app_byuser));
			$pre_cost_id=implode(",",$result);
			//echo $pre_cost_id;die;
			$pre_cost_id_cond="";

			if($pre_cost_id_app_byuser!="")
			{
				$pre_cost_id_app_byuser_arr=explode(",",$pre_cost_id_app_byuser);
				if(count($pre_cost_id_app_byuser_arr)>995)
				{
					$pre_cost_id_app_byuser_chunk_arr=array_chunk(explode(",",$pre_cost_id_app_byuser),995) ;
					foreach($pre_cost_id_app_byuser_chunk_arr as $chunk_arr)
					{
						$chunk_arr_value=implode(",",$chunk_arr);
						$pre_cost_id_cond.=" and b.id not in($chunk_arr_value)";
					}
				}
				else $pre_cost_id_cond=" and b.id not in($pre_cost_id_app_byuser)";
			}
			else $pre_cost_id_cond="";

			$sql="select b.costing_per,b.id, a.quotation_id, a.job_no_prefix_num, $year_cond, a.id as job_id, a.job_no, a.buyer_name,a.brand_id, a.SEASON_BUYER_WISE as season, a.season_year, a.style_ref_no, b.costing_date, 0 as approval_id, b.approved, b.inserted_by, b.entry_from, min(d.shipment_date) as minship_date, max(d.shipment_date) as maxship_date, a.job_quantity, (a.job_quantity*a.total_set_qnty) as job_qty_pcs, a.total_price, $internalRefCond as internalRef, $fileNoCond as fileNo
			from wo_pre_cost_mst b, wo_po_details_master a, wo_po_break_down d
			where a.job_no=b.job_no and a.job_no=d.job_no_mst and a.company_name=$company_name and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and b.ready_to_approved=1 and b.approved in (0,2) $buyer_id_cond $date_cond $pre_cost_id_cond $buyerIds_cond $brandIds_cond $buyer_id_cond2 $brand_id_cond2 $brand_id_cond $style_con $season_con $job_no_cond $file_no_cond $internal_ref_cond group by b.id,b.costing_per, a.quotation_id, a.job_no_prefix_num, $year_cond_groupby, a.id, a.job_no, a.buyer_name,a.brand_id, a.SEASON_BUYER_WISE, a.season_year, a.style_ref_no, b.costing_date, b.approved, b.inserted_by, b.entry_from, a.job_quantity, a.total_set_qnty, a.total_price";
			 // echo $sql;die;
			if($pre_cost_id!="")
			{
				$pre_cost_id_cond2="and ";
				$pre_cost_id_arr=explode(",",$pre_cost_id);
				if(count($pre_cost_id_arr)>995)
				{
					$pre_cost_id_cond2.=" ( ";
					$pre_cost_id_arr_chunk_arr=array_chunk(explode(",",$pre_cost_id),995) ;
					$slcunk=0;
					foreach($pre_cost_id_arr_chunk_arr as $chunk_arr)
					{
						if($slcunk>0) $pre_cost_id_cond2.=" or";
						$chunk_arr_value=implode(",",$chunk_arr);	
						$pre_cost_id_cond2.="  b.id  in($chunk_arr_value)";
						$slcunk++;	
					}
					$pre_cost_id_cond2.=" )";
				}
				else $pre_cost_id_cond2.="  b.id  in($pre_cost_id)";	 
				
				$sql.=" union all
				select b.costing_per,b.id, a.quotation_id, a.job_no_prefix_num, $year_cond, a.id as job_id, a.job_no, a.buyer_name,a.brand_id, a.SEASON_BUYER_WISE as season, a.season_year, a.style_ref_no, b.costing_date, 0 as approval_id, b.approved, b.inserted_by, b.entry_from, min(d.shipment_date) as minship_date, max(d.shipment_date) as maxship_date, a.job_quantity, (a.job_quantity*a.total_set_qnty) as job_qty_pcs, a.total_price, $internalRefCond as internalRef, $fileNoCond as fileNo
				from wo_pre_cost_mst b, wo_po_details_master a, wo_po_break_down d
				where a.job_no=b.job_no and a.job_no=d.job_no_mst and a.company_name=$company_name  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and b.ready_to_approved=1 and b.approved in(1,3) $pre_cost_id_cond2 $buyer_id_cond $buyer_id_cond2 $brand_id_cond2 $brand_id_cond $style_con $season_con $date_cond $job_no_cond $file_no_cond $internal_ref_cond group by b.id,b.costing_per, a.quotation_id, a.job_no_prefix_num, $year_cond_groupby, a.id, a.job_no, a.buyer_name,a.brand_id, a.SEASON_BUYER_WISE, a.season_year, a.style_ref_no, b.costing_date, b.approved, b.inserted_by, b.entry_from, a.job_quantity, a.total_set_qnty, a.total_price";
				 //echo $sql;
			}
		}
		else
		{
			$buyer_ids=$buyer_ids_array[$user_id]['u'];
			if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.buyer_name in($buyer_ids)";
			$brand_ids=$brand_ids_array[$user_id]['u'];
			if($brand_ids=="") $brand_id_cond2=""; else $brand_id_cond2=" and a.brand_id in($brand_ids)";
			
			//self unique buyer and brand.......................................;
			$previous_all_user_selected_brand_id_arr=array();
			$previous_all_user_selected_buyer_id_arr=array();$brand_id_wise_seq=array();
			for($useq=1;$useq<$user_sequence_no;$useq++){
				if($seq_bypass_status_array[$useq]==2){
					$previous_all_user_selected_brand_id_arr[]=$brand_ids_array[$useq]['s'];
					$previous_all_user_selected_buyer_id_arr[]=$buyer_ids_array[$useq]['s'];
				}
				
				foreach(explode(',',$brand_ids_array[$useq]['s']) as $bid){
					$brand_id_wise_seq[$bid]=$useq;
				}
				
			}
			
			//print_r($brand_id_wise_seq);die;
			
			
			$previous_all_user_selected_brand_id_arr = array_unique(explode(',',implode(',',$previous_all_user_selected_brand_id_arr)));
			$self_unique_brand_id=implode(',',array_diff(explode(',',$brand_ids_array[$user_sequence_no]['s']),$previous_all_user_selected_brand_id_arr));
			
			$previous_all_user_selected_buyer_id_arr = array_unique(explode(',',implode(',',$previous_all_user_selected_buyer_id_arr)));
			$self_unique_buyer_id=implode(',',array_diff(explode(',',$buyer_ids_array[$user_sequence_no]['s']),$previous_all_user_selected_buyer_id_arr));
			
			if($self_unique_buyer_id!=""){$buyer_id_cond3=" and a.buyer_id in($self_unique_buyer_id)";}
			if($self_unique_brand_id!=""){$brand_id_cond3=" and a.brand_id in($self_unique_brand_id)";}
		//.......................................self unique buyer and brand end;

			
			$user_sequence_no=$user_sequence_no-1;
			if($sequence_no==$user_sequence_no) $sequence_no_by_pass='';
			else
			{
				if($db_type==0)
				{
					$sequence_no_by_pass=return_field_value("group_concat(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no between $sequence_no and $user_sequence_no and is_deleted=0");// and bypass=1
				}
				else
				{
					$sequence_no_by_pass=return_field_value("LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no between $sequence_no and $user_sequence_no and is_deleted=0","sequence_no");//bypass=1 and
				}
			}
			
			//echo $sequence_no.'='.$user_sequence_no;die;
			
			if($sequence_no_by_pass=="") $sequence_no_cond=" and c.sequence_no='$sequence_no'";
			else $sequence_no_cond=" and c.sequence_no in ($sequence_no_by_pass)";

			$sql="select b.costing_per,b.id, a.quotation_id, a.job_no_prefix_num, $year_cond, a.id as job_id, a.job_no, a.buyer_name,a.brand_id, a.SEASON_BUYER_WISE as season, a.season_year, a.style_ref_no, b.costing_date, b.approved, b.inserted_by, c.id as approval_id, c.sequence_no, c.approved_by, c.id as approval_id, b.entry_from, min(d.shipment_date) as minship_date, max(d.shipment_date) as maxship_date, a.job_quantity, (a.job_quantity*a.total_set_qnty) as job_qty_pcs, a.total_price, $internalRefCond as internalRef, $fileNoCond as fileNo
			from wo_pre_cost_mst b, wo_po_details_master a, approval_history c, wo_po_break_down d
			where b.id=c.mst_id and c.entry_form=15 and a.job_no=b.job_no and a.company_name=$company_name and a.job_no=d.job_no_mst  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and c.current_approval_status=1 and b.ready_to_approved=1 and b.is_deleted=0 and b.approved in(1,3) $buyer_id_cond $buyer_id_cond2 $brand_id_cond2 $brand_id_cond $style_con $season_con $sequence_no_cond $date_cond $job_no_cond $file_no_cond $internal_ref_cond group by  b.id,b.costing_per, a.quotation_id, a.job_no_prefix_num, $year_cond_groupby, a.id, a.job_no, a.buyer_name,a.brand_id, a.SEASON_BUYER_WISE, a.season_year, a.style_ref_no, b.costing_date, b.approved, b.inserted_by, c.id, c.sequence_no, c.approved_by, b.entry_from, a.job_quantity, a.total_set_qnty, a.total_price ";
			// echo $sql; die;
		}
	}
	else
	{ 
		$buyer_ids=$buyer_ids_array[$user_id]['u'];
		if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.buyer_name in($buyer_ids)";
		$brand_ids=$brand_ids_array[$user_id]['u'];
		if($brand_ids=="") $brand_id_cond2=""; else $brand_id_cond2=" and a.brand_id in($brand_ids)";
		
		
		$sequence_no_cond=" and c.sequence_no='$user_sequence_no'";

		$sql="select b.costing_per,b.id, a.quotation_id, a.job_no_prefix_num, $year_cond, a.id as job_id, a.job_no, a.buyer_name,a.brand_id, a.SEASON_BUYER_WISE as season, a.season_year, a.style_ref_no, b.costing_date, b.approved, b.inserted_by, c.id as approval_id, c.sequence_no, c.approved_date, c.approved_by, c.id as approval_id, b.entry_from, min(d.shipment_date) as minship_date, max(d.shipment_date) as maxship_date, a.job_quantity, (a.job_quantity*a.total_set_qnty) as job_qty_pcs, a.total_price, $internalRefCond as internalRef, $fileNoCond as fileNo
			      from wo_pre_cost_mst b, wo_po_details_master a, approval_history c, wo_po_break_down d
				  where b.id=c.mst_id and c.entry_form=15 and a.job_no=b.job_no and a.company_name=$company_name and a.job_no=d.job_no_mst and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and c.current_approval_status=1 and b.ready_to_approved=1 and b.is_deleted=0 and b.approved in (1,3) $buyer_id_cond $buyer_id_cond2 $brand_id_cond2 $brand_id_cond $style_con $season_con $sequence_no_cond $date_cond $job_no_cond $file_no_cond $internal_ref_cond group by b.id,b.costing_per, a.quotation_id, a.quotation_id, a.job_no_prefix_num, $year_cond_groupby, a.id, a.job_no, a.buyer_name,a.brand_id, a.SEASON_BUYER_WISE, a.season_year, a.style_ref_no, b.costing_date, b.approved, b.inserted_by, c.id, c.sequence_no, c.approved_by, c.id, c.approved_date, b.entry_from, a.job_quantity, a.total_set_qnty, a.total_price ";
	}
	
	
	 // echo $sql;  
	
	
	$nameArray=sql_select( $sql );
	// print_r($nameArray);die;
	$jobFobValue_arr=array(); $jobIds="";
	foreach ($nameArray as $row)
	{
		$jobFobValue_arr[$row[csf('job_no')]]=$row[csf('total_price')];
		if($jobIds=='') $jobIds=$row[csf('job_id')]; else $jobIds.=','.$row[csf('job_id')];
	}
	
	$jobIds=implode(",",array_filter(array_unique(explode(",",$jobIds))));
	
	//echo $jobIds;die;
	
	
	$job_ids=count(explode(",",$jobIds)); $jobId_cond="";
	if($db_type==2 && $job_ids>1000)
	{
		$jobId_cond=" and (";
		$jobIdsArr=array_chunk(explode(",",$jobIds),999);
		foreach($jobIdsArr as $ids)
		{
			$ids=implode(",",$ids);
			$jobId_cond.=" job_id in($ids) or"; 
		}
		$jobId_cond=chop($jobId_cond,'or ');
		$jobId_cond.=")";
	}
	else $jobId_cond=" and job_id in($jobIds)";
	
	$bomDtls_arr=array();
	$bomDtlssql=sql_select( "select job_no, fabric_cost_percent, trims_cost_percent, embel_cost_percent, wash_cost_percent, cm_cost_percent, margin_pcs_set_percent from wo_pre_cost_dtls where status_active=1 and is_deleted=0 $jobId_cond");
	foreach ($bomDtlssql as $row)
	{
		$bomDtls_arr[$row[csf('job_no')]]['trimper']=$row[csf('trims_cost_percent')];
		$bomDtls_arr[$row[csf('job_no')]]['cm']=$row[csf('cm_cost_percent')];
		$bomDtls_arr[$row[csf('job_no')]]['ms']=$row[csf('fabric_cost_percent')]+$row[csf('trims_cost_percent')]+$row[csf('embel_cost_percent')]+$row[csf('wash_cost_percent')];
		$bomDtls_arr[$row[csf('job_no')]]['margin']=$row[csf('margin_pcs_set_percent')];
	}
	unset($bomDtlssql);
	
	$condition= new condition();
	$condition->company_name("=$cbo_company_name");
	if(str_replace("'","",$cbo_buyer_name)>0){
		$condition->buyer_name("=$cbo_buyer_name");
	}
	
	
	if($jobIds!=''){
		$condition->jobid_in("$jobIds");
	}
	if(str_replace("'","",$txt_file_no)!='')
	{
		$condition->file_no("=$txt_file_no"); 
	}
	if(str_replace("'","",$txt_internal_ref)!='')
	{
		$condition->grouping("=$txt_internal_ref"); 
	}
	
	$condition->init();
	//$yarn= new yarn($condition);
	//$yarn_data_array=$yarn->getJobWiseYarnAmountArray();
	
	$fabric= new fabric($condition);
	$fabric_amount_job_uom=$fabric->getAmountArray_by_job_knitAndwoven_greyAndfinish();
	
	 //print_r($fabric_amount_job_uom);die;
	
	
	$wash= new wash($condition);
	$wash_data_array=$wash->getAmountArray_by_jobAndEmbtype();
	
	 
	 
	$emblishment= new emblishment($condition);
	$emblishment_data_array=$emblishment->getAmountArray_by_jobAndEmbname();
	
	
	
	
	
	$conversion= new conversion($condition);
	//$conv_amount_arr=$conversion->getAmountArray_by_jobAndProcess();
	//echo $conversion->getQuery(); die;
	//print_r($conv_amount_arr);
	
/*	$sql_fabric = "select id, job_no, uom, fabric_source from wo_pre_cost_fabric_cost_dtls where status_active=1 and is_deleted=0 $jobId_cond";
	$data_arr_fabric=sql_select($sql_fabric); $fabricPurchesamt_arr=array();
	foreach($data_arr_fabric as $fab_row)
	{
		$purchase_amt=0;
		if($fab_row[csf("fab_source")]==2)
		{
			$purchase_amt=$fabric_amount['knit']['grey'][$fab_row[csf("id")]][$fab_row[csf("uom")]]+$fabric_amount['woven']['grey'][$fab_row[csf("id")]][$fab_row[csf("uom")]];
			$fabricPurchesamt_arr[$fab_row[csf("job_no")]]['fabpur']+=$purchase_amt;
		}
	}
*/	
	//print_r($fabricPurchesamt_arr);die;
	
	
	unset($data_arr_fabric);

	$sql_unapproved=sql_select("select * from fabric_booking_approval_cause where  entry_form=15 and approval_type=2 and is_deleted=0 and status_active=1");
	$unapproved_request_arr=array();
	foreach($sql_unapproved as $rowu)
	{
		$unapproved_request_arr[$rowu[csf('booking_id')]]=$rowu[csf('approval_cause')];
	}
	$seasonArr = return_library_array("select id,season_name from lib_buyer_season ","id","season_name");
	$brandArr = return_library_array("select id,brand_name from lib_buyer_brand ","id","brand_name");
	//Pre cost button---------------------------------
	$print_report_format_ids = return_field_value("format_id","lib_report_template","template_name=".$cbo_company_name." and module_id=2 and report_id =122 and is_deleted=0 and status_active=1");
	$format_ids=explode(",",$print_report_format_ids);
	$row_id=$format_ids[0];
	// echo $row_id.'d';

	//Order Wise Budget Report button---------------------------------
	$print_report_format_ids2 = return_field_value("format_id","lib_report_template","template_name=".$cbo_company_name." and module_id=11 and report_id=18 and is_deleted=0 and status_active=1");
	$format_ids2=explode(",",$print_report_format_ids2);
	$row_id2=$format_ids2[0];
	$width=1780;
	?>
    <form name="requisitionApproval_2" id="requisitionApproval_2">
        <fieldset style="width:<?=$width+20;?>px; margin-top:10px">
        <legend>Pre-Costing Approval  WVN</legend>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?=$width;?>" class="rpt_table" align="left" >
                <thead>
                    <th width="40">&nbsp;</th>
                    <th width="30">SL</th>
                    <th width="50">Job No</th>
                    <th width="70">Master Style/Internal Ref.</th>
                    <th width="110">Buyer</th>
                    <th width="40">Year</th>
					<th width="80">Brand</th>
                    <th width="80">Season</th>
                    <th width="50">Season Year</th>
                    <th width="130">Style Ref.</th>
                    <th width="70">Costing Date</th>
                    <th width="70">Ship Start</th>
                    <th width="70">Ship End</th>
                    <th width="70">Job Qty(Pcs)</th>
                    <th width="60">Avg. Rate</th>
                    <th width="80">Total Value</th>
                    <th width="60">Fabric %</th>
                    <th width="60">Trims %</th>
                    <th width="60">Embel. Cost %</th>
                    <th width="60">Gmts.Wash%</th>
                    <th width="60">CM %</th>
                    <th width="60">Margin %</th>
                    <th width="140">Unapproved Request</th>
                    <th width="65">Insert By</th>
                    <th>Approved Date</th>
                </thead>
            </table>
            <div style="width:<?=$width+20;?>px; overflow-y:scroll; max-height:330px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?=$width;?>" class="rpt_table" id="tbl_list_search" align="left">
                    <tbody>
                        <?
                        $i=1; //die;
						$aop_cost_arr=array(35,36,37,40);

					
						foreach ($nameArray as $row)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$value=$row[csf('id')];
							if($row[csf('approval_id')]==0) $print_cond=1;
							else
							{
								if($duplicate_array[$row[csf('id')]][$row[csf('sequence_no')]][$row[csf('approved_by')]]=="")
								{
									$duplicate_array[$row[csf('id')]][$row[csf('sequence_no')]][$row[csf('approved_by')]]=$row[csf('approval_id')];
									$print_cond=1;
								}
								else
								{
									if($all_approval_id=="") $all_approval_id=$row[csf('approval_id')]; else $all_approval_id.=",".$row[csf('approval_id')];
									$print_cond=0;
								}
							}
							if($row_id2==23){$type=1;/*Summary;*/}
							else if($row_id2==24){$type=2;}
							else if($row_id2==25){$type=3;/*Budget Report2;*/}
							else if($row_id2==26){$type=4;/*Quote Vs Budget;*/}
							else if($row_id2==27){$type=5;/*Budget On Shipout;*/}
							else if($row_id2==29){$type=6;/*C.Date Budget On Shipout;*/}
							else if($row_id2==182){$type=7;/*Budget Report 3;*/}

							$function2="generat_print_report($type,$cbo_company_name,0,'','',{$row[csf('job_no_prefix_num')]},'','','',".$row[csf('year')].",0,1,'','','','')";
							//{$row[csf('buyer_name')]}
							if($print_cond==1)
							{
								
								
								if($row_id==51) $action='preCostRpt2';
								else if($row_id==307)$action='basic_cost';
								else if($row_id==311)$action='bom_epm_woven';
								else if($row_id==313)$action='mkt_source_cost';
								else if($row_id==158) $action='preCostRptWoven';
								else if($row_id==159)$action='bomRptWoven';
								else if($row_id==192)$action='checkListRpt';
								else if($row_id==761) $action='bom_pcs_woven';
								else if($row_id==381) $action='mo_sheet_2';
								else if($row_id==403) $action='mo_sheet_3';
			
								
								$function="generate_worder_report('".$action."','".$row[csf('job_no')]."',".$cbo_company_name.",".$row[csf('buyer_name')].",'".$row[csf('style_ref_no')]."','".$row[csf('costing_date')]."','','".$row[csf('costing_per')]."');"; 
								
								$jobavgRate=0; $int_ref = ""; $file_numbers = "";
								$jobavgRate=$row[csf('total_price')]/$row[csf('job_quantity')];
								if($db_type==2) $row[csf('internalRef')]= $row[csf('internalRef')]->load();
								
								$int_ref=implode(",",array_unique(explode(",",chop($row[csf('internalRef')],","))));
								$finishPercent=$trimPercent=$fabpurchase_per=$aopamt=$yarn_dyeingAmt=$yarn_dyeingPer=$msper=$aopPer=$cmper=$marginper=0;
								$trimPercent=$bomDtls_arr[$row[csf('job_no')]]['trimper'];
								
								$finishPercent=(array_sum($fabric_amount_job_uom[woven][finish][$row[csf('job_no')]])/$row[csf('total_price')])*100;
								$washPercent=(array_sum($wash_data_array[$row[csf('job_no')]])/$row[csf('total_price')])*100;
								$emblishmentPercent=(array_sum($emblishment_data_array[$row[csf('job_no')]])/$row[csf('total_price')])*100;
								//$yarn_dyeingAmt=array_sum($conv_amount_arr[$row[csf('job_no')]][30]);
								//$yarn_dyeingPer=($yarn_dyeingAmt/$row[csf('total_price')])*100;
								
								foreach($aop_cost_arr as $aop_process_id)
								{
									$aopamt+=array_sum($conv_amount_arr[$row[csf('job_no')]][$aop_process_id]);
								}
								$aopPer=($aopamt/$row[csf('total_price')])*100;
								
								//$btwob_per=$yarnPercent+$fabpurchase_per+$trimPercent+$yarn_dyeingPer+$aopPer;
								
								$msper=$bomDtls_arr[$row[csf('job_no')]]['ms'];
								$cmper=$bomDtls_arr[$row[csf('job_no')]]['cm'];
								$marginper=$bomDtls_arr[$row[csf('job_no')]]['margin'];
								
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>')" id="tr_<?=$i; ?>" align="center">
                                	<td width="40" align="center" valign="middle">
                                        <input type="checkbox" id="tbl_<?=$i;?>" />
                                        <input id="booking_id_<?=$i;?>" name="booking_id[]" type="hidden" value="<?=$value; ?>" />
                                        <input id="booking_no_<?=$i;?>" name="booking_no[]" type="hidden" value="<?=$row[csf('job_no')]; ?>" />
                                        <input id="approval_id_<?=$i;?>" name="approval_id[]" type="hidden" value="<?=$row[csf('approval_id')]; ?>" />
                                        <input id="<?=strtoupper($row[csf('job_no')]); ?>" name="no_joooob[]" type="hidden" value="<?=$i;?>" />
                                        <input id="cm_cost_id_<?=$i;?>" name="cm_cost_id[]" style="width:20px;" type="hidden" value="<?=$cm_cost; ?>" />
                                    </td>
									<td width="30" align="center"><?=$i; ?></td>
									<td width="50"><a href='##' onclick="<?=$function; ?>"><?=$row[csf('job_no_prefix_num')]; ?></a></td>
                                    <td width="70" style="word-break:break-all;"><?=$int_ref; ?></td>
                                    <td width="110" style="word-break:break-all;"><?=$buyer_arr[$row[csf('buyer_name')]]; ?></td>
                                    <td width="40" style="word-break:break-all;"><?=$row[csf('year')]; ?></td>
									<td width="80" style="word-break:break-all"><?=$brandArr[$row[csf('brand_id')]]; ?></td>
                            	    <td width="80" style="word-break:break-all"><?=$seasonArr[$row[csf('season')]]; ?></td>
                              		<td width="50" style="word-break:break-all"><?=$row[csf('season_year')]; ?></td>
                                    <td width="130" align="center" style="word-break:break-all;"><?=$row[csf('style_ref_no')]; ?></td>
                                    <td width="70" align="center"><? if($row[csf('costing_date')]!="0000-00-00") echo change_date_format($row[csf('costing_date')]); ?>&nbsp;</td>
                                    <td align="center" width="70"><? if($row[csf('minship_date')]!="0000-00-00") echo change_date_format($row[csf('minship_date')]); ?>&nbsp;</td>
                                    <td align="center" width="70"><? if($row[csf('maxship_date')]!="0000-00-00") echo change_date_format($row[csf('maxship_date')]); ?>&nbsp;</td>
                                    <td width="70" align="right" style="word-break:break-all;"><?=number_format($row[csf('job_qty_pcs')]); ?></td>
                                    <td width="60" align="right" style="word-break:break-all;"><?=number_format($jobavgRate,4); ?></td>
                                    <td width="80" align="right" style="word-break:break-all;"><?=number_format($row[csf('total_price')],2); ?></td>
                                    
                                    <td width="60" align="right" style="word-break:break-all;"><?=number_format($finishPercent,2); ?></td>
                                    <td width="60" align="right" style="word-break:break-all;"><?=number_format($trimPercent,2); ?></td>
                                    <td width="60" align="right" style="word-break:break-all;"><?=number_format($emblishmentPercent,2); ?></td>
                                    <td width="60" align="right" style="word-break:break-all;"><?=number_format($washPercent,2); ?></td>
                                    <td width="60" align="right" style="word-break:break-all;" id="tdCm_<?=$i;?>"><?=number_format($cmper,2); ?></td>
                                    <td width="60" align="right" style="word-break:break-all;"><?=number_format($marginper,2); ?></td>
                                    
                                    <td width="140" style="word-break:break-all"><? if($approval_type==1) echo $unapproved_request_arr[$value]; ?> </td>
                                    <td width="65" style="word-break:break-all;"><? echo ucfirst ($user_arr[$row[csf('inserted_by')]]); ?>&nbsp;</td>
                                    <td align="center"><? if($row[csf('approved_date')]!="0000-00-00") echo change_date_format($row[csf('approved_date')]); ?>&nbsp;</td>
								</tr>
								<?
								$i++;
							}

							if($all_approval_id!="")
							{
								$con = connect();
								$rID=sql_multirow_update("approval_history","current_approval_status",0,"id",$all_approval_id,1);
								//echo $rID."**";
								if($db_type==0)
								{
									if($rID==1)
									{
										mysql_query("COMMIT");
										echo $msg."**".$response;
									}
									else
									{
										mysql_query("ROLLBACK");
										echo $msg."**".$response;
									}
								}
								else if($db_type==2 || $db_type==1 )
								{
									if($rID==1)
									{
										oci_commit($con);
										echo $msg."**".$response;
									}
									else
									{
										oci_rollback($con);
										echo $msg."**".$response;
									}
								}
								disconnect($con);
							}
						}
                        ?>
                    </tbody>
                </table>
            </div>
            <table cellspacing="0" cellpadding="0" border="0" rules="all" width="<?=$width;?>" class="rpt_table" align="left">
				<tfoot>
                    <td width="40" align="center" ><input type="checkbox" id="all_check" onclick="check_all('all_check')" /></td>
                    <td colspan="2" align="left"><input type="button" value="<? if($approval_type==1) echo "Un-Approve"; else echo "Approve"; ?>" class="formbutton" style="width:100px" onclick="submit_approved(<? echo $i; ?>,<? echo $approval_type; ?>)"/></td>
				</tfoot>
			</table>
        </fieldset>
    </form>
	<?
	exit();
}

if ($action=="approve")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
	$msg=''; $flag=''; $response='';
	$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
	if($txt_alter_user_id!="") 	$user_id_approval=$txt_alter_user_id;
	else						$user_id_approval=$user_id;

	$user_sequence=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id_approval and is_deleted=0");
	
	
	$min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0");

	$buyer_arr=return_library_array( "select b.id, a.buyer_name   from wo_pre_cost_mst b,  wo_po_details_master a where  a.job_no=b.job_no and a.is_deleted=0 and  a.status_active=1 and b.status_active=1 and  b.is_deleted=0 and b.id in ($booking_ids)", "id", "buyer_name"  );
	
	
	$brand_arr=return_library_array( "select b.id, a.brand_id   from wo_pre_cost_mst b,  wo_po_details_master a where  a.job_no=b.job_no and a.is_deleted=0 and  a.status_active=1 and b.status_active=1 and  b.is_deleted=0 and b.id in ($booking_ids)", "id", "brand_id"  );
	
	
	$electronic_setup_sql = sql_select("select b.USER_ID,b.BUYER_ID,B.BRAND_ID from electronic_approval_setup b where b.page_id=$menu_id and b.user_id <> $user_id_approval and sequence_no>$user_sequence and b.is_deleted=0 group by b.USER_ID,b.BUYER_ID,B.BRAND_ID"); 
	foreach ($electronic_setup_sql as $row) {
		$otherUserBuyerArr[$row[USER_ID]] = $row[BUYER_ID];
		$otherUserBrandArr[$row[USER_ID]] = $row[BRAND_ID];
	}
	$otherUserBuyerArr=array_filter(array_unique(explode(',',implode(',',$otherUserBuyerArr))));
	$otherUserBrandArr=array_filter(array_unique(explode(',',implode(',',$otherUserBrandArr))));

	
	if($approval_type==2)
	{
		$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id_approval and is_deleted=0 and bypass=2");
		$min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0 and bypass=2");
		 
		 
		 //echo "10**".$min_sequence_no.'--'.$user_sequence_no; die;
		
		
		if($min_sequence_no != $user_sequence_no)
		{
			
			$sql = sql_select("select b.buyer_id as buyer_id,b.sequence_no,b.BRAND_ID from electronic_approval_setup b where b.company_id=$cbo_company_name and b.page_id=$menu_id and b.sequence_no < $user_sequence_no and b.is_deleted=0 and bypass=2 group by b.buyer_id,b.BRAND_ID ,b.sequence_no order by b.sequence_no ASC");
			
			foreach ($sql as $key => $buyerID) {
				$allUserBuyersArr[$buyerID[csf('sequence_no')]] = $buyerID[csf('buyer_id')];
				$buyerIds.=$buyerID[csf('buyer_id')].",";
				$allBrandByUserSeqArr[$buyerID[csf('sequence_no')]] =explode(',',$buyerID[csf('BRAND_ID')]);
			}

			if(count($allUserBuyersArr)>0)
			{
				foreach ($allUserBuyersArr as $user_id => $buyer_string) {
					$user_buyer_arr = explode(',',$buyer_string);
					foreach ($user_buyer_arr as $buyer_id) {
						$all_buyer_by_seq[$buyer_id] = $user_id;
					}
				}
			}
			
			if(count($allBrandByUserSeqArr)>0)
			{
				foreach ($allBrandByUserSeqArr as $seq_id => $brandArr) {
					foreach ($brandArr as $brand_id) {
						$all_brand_by_seq[$brand_id] = $seq_id;
					}
				}
			}

			

			$sql = sql_select("select b.buyer_id as buyer_id,B.BRAND_ID from electronic_approval_setup b where b.company_id=$cbo_company_name and b.page_id=$menu_id and b.sequence_no = $user_sequence and b.is_deleted=0 group by b.buyer_id,B.BRAND_ID"); 
			
			$userBuyer=0;
			foreach ($sql as $key => $buyerID) {
				if($buyerID[csf('buyer_id')]!='')
				{
					$currUserBuyersArr[$user_sequence_no] = $buyerID[csf('buyer_id')];
				}
				else
				{
					$currUserBuyersArr[$user_sequence_no] = chop($buyerIds,',');;
				}
				$currUserBrandsArr[$user_sequence_no] = explode($buyerID[csf('BRAND_ID')]);
				
			}
			
			
			//brand start................................
			$userBrand=0;
			if(count($currUserBrandsArr)>0)
			{
				foreach ($currUserBrandsArr as $seq_id => $brand_string) {
					foreach ($brand_string as $brand_id) {
						$curr_brand_by_seq[$brand_id] = $seq_id;
					}
				}
			}
			else
			{
				$userBrand=1;
			}
			
			foreach ($curr_brand_by_seq as $brand_id=>$sequence_id) {
				if (array_key_exists($brand_id,$all_brand_by_seq))
			    {
			    	$brand_key_arr[$brand_id] = $all_brand_by_seq[$brand_id];			    
			    }
			}
			foreach ($brand_arr as $booking => $brand) {
				if (array_key_exists($brand,$brand_key_arr))
			    {
			    	$brand_match_seq[$buyer_id] = $brand_key_arr[$buyer_id];			    
			    }
			}
			//---------------------brand end;

			
			
			if(count($currUserBuyersArr)>0)
			{
				foreach ($currUserBuyersArr as $userId => $buyer_string) {
					$user_buyer_arr = explode(',',$buyer_string);
					foreach ($user_buyer_arr as $buyer_id) {
						$curr_buyer_by_seq[$buyer_id] = $userId;
					}
				}
			}
			else
			{
				$userBuyer=1;
			}
			foreach ($curr_buyer_by_seq as $buyer_id=>$sequence_id) {
				if (array_key_exists($buyer_id,$all_buyer_by_seq))
			    {
			    	$key_arr[$buyer_id] = $all_buyer_by_seq[$buyer_id];			    
			    }
			}
			
			foreach ($buyer_arr as $booking => $buyer) {
				if (array_key_exists($buyer,$key_arr))
			    {
			    	$match_seq[$buyer_id] = $key_arr[$buyer_id];			    
			    }
			}
			
			
			if(count($match_seq)>0 || $userBuyer==1)
			{
				$previous_user_seq = implode(',', $match_seq);
				$previous_user_app = sql_select("select id from approval_history where mst_id in($booking_ids) and entry_form=15 and sequence_no <$user_sequence_no and current_approval_status=1 group by id");
				
				//echo "select id from approval_history where mst_id in($booking_ids) and entry_form=15 and sequence_no <$user_sequence_no and current_approval_status=1 group by id";
				
				
				if(count($previous_user_app)==0)
				{
					$previous_user_app = sql_select("select id from approval_history where mst_id in($booking_ids) and entry_form=15 and sequence_no in ($previous_user_seq) and current_approval_status=1 group by id");
					//echo "25**".count($previous_user_app);die;
				}
				
				
				if(count($previous_user_app)==0)
				{
					echo "25**approved"; 
					disconnect($con);
					die;
				}
			}				
		}

		if($db_type==2) {$buyer_id_cond=" and a.buyer_id is null "; $buyer_id_cond2=" and b.buyer_id is not null ";}
		else {$buyer_id_cond=" and a.buyer_id='' "; $buyer_id_cond2=" and b.buyer_id!='' ";}
		//echo "22**";
		$is_not_last_user=return_field_value("a.sequence_no","electronic_approval_setup a"," a.company_id=$cbo_company_name and a.page_id=$menu_id and a.sequence_no>$user_sequence_no and a.bypass=2 and a.is_deleted=0","sequence_no"); //$buyer_id_cond

		 //echo $user_sequence;disconnect($con);die;
		
		$partial_approval = "";
		if($is_not_last_user == "")
		{
			//$credentialUserBuyersArr = [];
			$sql = sql_select("select (b.buyer_id) as buyer_id,b.brand_id from electronic_approval_setup b where b.company_id=$cbo_company_name and b.page_id=$menu_id and b.sequence_no>$user_sequence_no and b.is_deleted=0 and b.bypass=2  group by b.buyer_id,b.brand_id");
			foreach ($sql as $key => $buyerID) {
				$credentialUserBuyersArr[] = $buyerID[csf('buyer_id')];
				$credentialUserBrandArr[] = $buyerID[csf('brand_id')];
			}

			$credentialUserBuyersArr = implode(',',$credentialUserBuyersArr);
			$credentialUserBuyersArr = explode(',',$credentialUserBuyersArr);
			$credentialUserBuyersArr = array_unique($credentialUserBuyersArr);
			$credentialUserBrandArr=array_unique(explode(',',implode(',',$credentialUserBrandArr)));
			//print_r($credentialUserBrandArr);die;
		}
		else
		{
			$check_user_buyer = sql_select("select b.user_id as user_id from user_passwd a, electronic_approval_setup b where a.id = b.user_id and b.company_id=$cbo_company_name and b.page_id=$menu_id and b.sequence_no>$user_sequence_no and b.is_deleted=0 and b.bypass=2 $buyer_id_cond  group by b.user_id");
			//echo "21**".count($check_user_buyer);die;
			if(count($check_user_buyer)==0)
			{

				$sql = sql_select("select b.buyer_id as buyer_id from user_passwd b, electronic_approval_setup a where b.id = a.user_id and a.company_id=$cbo_company_name and a.page_id=$menu_id and a.sequence_no>$user_sequence_no and a.is_deleted=0 and a.bypass=2 $buyer_id_cond $buyer_id_cond2 group by b.buyer_id");
				foreach ($sql as $key => $buyerID) {
					$credentialUserBuyersArr[] = $buyerID[csf('buyer_id')];
				}

				$sql = sql_select("select (b.buyer_id) as buyer_id from electronic_approval_setup b where b.company_id=$cbo_company_name and b.page_id=$menu_id and b.sequence_no>$user_sequence_no and b.is_deleted=0 and b.bypass=2 $buyer_id_cond2  group by b.buyer_id");
				foreach ($sql as $key => $buyerID) {
					$credentialUserBuyersArr[] = $buyerID[csf('buyer_id')];
				}

				$credentialUserBuyersArr = implode(',',$credentialUserBuyersArr);
				$credentialUserBuyersArr = explode(',',$credentialUserBuyersArr);
				$credentialUserBuyersArr = array_unique($credentialUserBuyersArr);
			}
			
		}
		// if($is_not_last_user!="") $partial_approval=3; else $partial_approval=1;
 		//print_r($credentialUserBuyersArr);die;
		
		$response=$booking_ids;
		$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date,inserted_by,insert_date";
		$id=return_next_id( "id","approval_history", 1 ) ;

		
		$max_approved_no_arr = return_library_array("select mst_id, max(approved_no) as approved_no from approval_history where mst_id in($booking_ids) and entry_form=15 group by mst_id","mst_id","approved_no");

		$approved_status_arr = return_library_array("select id, approved from wo_pre_cost_mst where id in($booking_ids)","id","approved");
		$approved_no_array=array();
		$booking_ids_all=explode(",",$booking_ids);
		$booking_nos_all=explode(",",$booking_nos);
		$book_nos='';
		
		
		 //print count($booking_nos_all);die;
		
		for($i=0;$i<count($booking_nos_all);$i++)
		{
			$val=$booking_nos_all[$i];
			$booking_id=$booking_ids_all[$i];

			$approved_no=$max_approved_no_arr[$booking_id];
			$approved_status=$approved_status_arr[$booking_id];
			$buyer_id=$buyer_arr[$booking_id];
			$brand_id=$brand_arr[$booking_id];			
			
			//echo "10**".$approved_no; die;
			if($approved_status==2)
			{
				$approved_no=$approved_no+1;
				$approved_no_array[$val]=$approved_no;
				if($book_nos=="") $book_nos=$val; else $book_nos.=",".$val;
			}
			if($approved_status==0 && $approved_no=='')
			{
				$approved_no=1;
			}


			if($is_not_last_user == "")
			{
				 
				
				if((count($credentialUserBuyersArr)>0 || count($credentialUserBrandArr)>0) || (count($otherUserBuyerArr)>0 || count($otherUserBrandArr)>0))
				{
					
					
					if( (count($otherUserBuyerArr)>0) && (in_array($buyer_id,$otherUserBuyerArr)) && (count($otherUserBrandArr)==0) ||  (count($credentialUserBuyersArr)>0) && (in_array($buyer_id,$credentialUserBuyersArr)) && (count($credentialUserBrandArr)==0)){
						$partial_approval=3;
					}
					else if((count($otherUserBuyerArr)>0) && (in_array($buyer_id,$otherUserBuyerArr)) && (count($otherUserBrandArr)>0) && (in_array($brand_id,$otherUserBrandArr)) || (count($credentialUserBuyersArr)>0) && (in_array($buyer_id,$credentialUserBuyersArr)) && (count($credentialUserBrandArr)>0) && (in_array($brand_id,$credentialUserBrandArr)) ){
						$partial_approval=3;
					}
					else
					{
						$partial_approval=1;
					}
				}
				else{
					$partial_approval=3;
				}
				
			}
			else
			{
				if( (count($otherUserBuyerArr)>0) && (in_array($buyer_id,$otherUserBuyerArr)) && (count($otherUserBrandArr)==0) ||  (count($credentialUserBuyersArr)>0) && (in_array($buyer_id,$credentialUserBuyersArr)) && (count($credentialUserBrandArr)==0)){
					$partial_approval=3;
				}
				else if((count($otherUserBuyerArr)>0) && (in_array($buyer_id,$otherUserBuyerArr)) && (count($otherUserBrandArr)>0) && (in_array($brand_id,$otherUserBrandArr)) || (count($credentialUserBuyersArr)>0) && (in_array($buyer_id,$credentialUserBuyersArr)) && (count($credentialUserBrandArr)>0) && (in_array($brand_id,$credentialUserBrandArr)) ){
					$partial_approval=3;
				}
				else
				{
					$partial_approval=1;
				}
			}
			 //echo $partial_approval;die;
			$booking_id_arr[]=$booking_id;
			$data_array_booking_update[$booking_id]=explode("*",($partial_approval));

			if($partial_approval==1)
			{
				$full_approve_booking_id_arr[]=$booking_id;
				$data_array_full_approve_booking_update[$booking_id]=explode("*",($user_id_approval."*'".$pc_date_time."'"));
			}

			if($data_array!="") $data_array.=",";
			$data_array.="(".$id.",15,".$booking_id.",".$approved_no.",'".$user_sequence."',1,".$user_id_approval.",'".$pc_date_time."',".$user_id.",'".$pc_date_time."')";
			$id=$id+1;
		}
		
		$flag=1;
		if(count($approved_no_array)>0)
		{
			$approved_string="";

			if($db_type==0)
			{
				foreach($approved_no_array as $key=>$value)
				{
					$approved_string.=" WHEN $key THEN $value";
				}
			}
			else
			{
				foreach($approved_no_array as $key=>$value)
				{
					$approved_string.=" WHEN TO_NCHAR($key) THEN '".$value."'";
				}
			}
			//echo "10**";
			$approved_string_mst="CASE job_no ".$approved_string." END";
			$approved_string_dtls="CASE job_no ".$approved_string." END";
			$sql_insert="insert into wo_pre_cost_mst_histry(id, approved_no, pre_cost_mst_id, garments_nature, job_no, costing_date, incoterm, incoterm_place,
			machine_line, prod_line_hr, costing_per, remarks, copy_quatation, cm_cost_predefined_method_id, exchange_rate, sew_smv, cut_smv, sew_effi_percent,
			cut_effi_percent, efficiency_wastage_percent, approved, approved_by, approved_date, inserted_by, insert_date, updated_by, update_date, status_active,
			is_deleted)
					select
					'', $approved_string_mst, id, garments_nature, job_no, costing_date, incoterm, incoterm_place, machine_line, prod_line_hr, costing_per,
			remarks, copy_quatation, cm_cost_predefined_method_id, exchange_rate, sew_smv, cut_smv, sew_effi_percent, cut_effi_percent,
			efficiency_wastage_percent, approved, approved_by, approved_date, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted
			from wo_pre_cost_mst where job_no in ($book_nos)";
			//echo $sql_insert;die;


			$sql_precost_dtls="insert into wo_pre_cost_dtls_histry(id,approved_no,pre_cost_dtls_id,job_no,costing_per_id,order_uom_id,fabric_cost,  fabric_cost_percent,trims_cost,trims_cost_percent,embel_cost,embel_cost_percent,wash_cost,wash_cost_percent,comm_cost,comm_cost_percent,commission,
		commission_percent,	lab_test,lab_test_percent,inspection, inspection_percent,cm_cost,cm_cost_percent,freight,freight_percent,currier_pre_cost,
		currier_percent, certificate_pre_cost,certificate_percent,common_oh,common_oh_percent,total_cost,total_cost_percent,price_dzn,price_dzn_percent,
		margin_dzn,margin_dzn_percent,cost_pcs_set,cost_pcs_set_percent,price_pcs_or_set,price_pcs_or_set_percent,margin_pcs_set,margin_pcs_set_percent,
		cm_for_sipment_sche,margin_pcs_bom, margin_bom_per,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted)
					select
					'', $approved_string_dtls, id,job_no,costing_per_id,order_uom_id,fabric_cost,  fabric_cost_percent,trims_cost,trims_cost_percent,embel_cost,embel_cost_percent,wash_cost,wash_cost_percent,comm_cost,comm_cost_percent,commission,
		commission_percent,	lab_test,lab_test_percent,inspection, inspection_percent,cm_cost,cm_cost_percent,freight,freight_percent,currier_pre_cost,
		currier_percent, certificate_pre_cost,certificate_percent,common_oh,common_oh_percent,total_cost,total_cost_percent,price_dzn,price_dzn_percent,
		margin_dzn,margin_dzn_percent,cost_pcs_set,cost_pcs_set_percent,price_pcs_or_set,price_pcs_or_set_percent,margin_pcs_set,margin_pcs_set_percent,
		cm_for_sipment_sche,margin_pcs_bom, margin_bom_per,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted from wo_pre_cost_dtls  where  job_no in ($book_nos)";
			//echo $sql_precost_dtls;die;


			//--------------------------------------wo_pre_cost_fabric_cost_dtls_h---------------------------------------------------------------------------
			$sql_precost_fabric_cost_dtls="insert into wo_pre_cost_fabric_cost_dtls_h(id,approved_no,pre_cost_fabric_cost_dtls_id, job_no, item_number_id, body_part_id, fab_nature_id, color_type_id,lib_yarn_count_deter_id,construction,composition, fabric_description, gsm_weight,color_size_sensitive,	color, avg_cons, fabric_source, rate, amount,avg_finish_cons,avg_process_loss, inserted_by, insert_date,updated_by,update_date, status_active, is_deleted, company_id, costing_per,consumption_basis,process_loss_method,cons_breack_down,msmnt_break_down,color_break_down,yarn_breack_down,marker_break_down,width_dia_type)
				select
				'', $approved_string_dtls, id,job_no, item_number_id, body_part_id, fab_nature_id, color_type_id,lib_yarn_count_deter_id,construction,composition, fabric_description, gsm_weight,color_size_sensitive,	color, avg_cons, fabric_source, rate,amount,avg_finish_cons,avg_process_loss, inserted_by, insert_date,updated_by,update_date, status_active, is_deleted, company_id, costing_per,consumption_basis,process_loss_method,cons_breack_down,msmnt_break_down,color_break_down,yarn_breack_down,marker_break_down,width_dia_type from wo_pre_cost_fabric_cost_dtls where  job_no in ($book_nos)";
			//echo $sql_precost_fabric_cost_dtls;die;

			//--------------------------------------wo_pre_cost_fab_yarn_cst_dtl_h---------------------------------------------------------------------------
			$sql_precost_fab_yarn_cst="insert into wo_pre_cost_fab_yarn_cst_dtl_h(id,approved_no,pre_cost_fab_yarn_cost_dtls_id,fabric_cost_dtls_id,job_no,count_id,copm_one_id,percent_one,copm_two_id,percent_two,type_id,cons_ratio,cons_qnty,rate,amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted)
				select
				'', $approved_string_dtls, id,fabric_cost_dtls_id,job_no,count_id,copm_one_id,percent_one,copm_two_id,percent_two,type_id,cons_ratio,cons_qnty,rate,amount,
		inserted_by,insert_date,updated_by,update_date,status_active,is_deleted from wo_pre_cost_fab_yarn_cost_dtls  where  job_no in ($book_nos)";
				//echo $sql_precost_fab_yarn_cst;die;

			//----------------------------------wo_pre_cost_comarc_cost_dtls_h----------------------------------------
			$sql_precost_fcomarc_cost_dtls="insert into wo_pre_cost_comarc_cost_dtls_h(id,approved_no,pre_cost_comarci_cost_dtls_id,job_no,item_id,rate,amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted)
				select
				'', $approved_string_dtls,id,job_no,item_id,rate,amount,inserted_by,insert_date,updated_by,update_date,status_active,
		is_deleted from wo_pre_cost_comarci_cost_dtls where  job_no in ($book_nos)";
				//echo $sql_precost_fcomarc_cost_dtls;die;


			//-------------------------------------pre_cost_commis_cost_dtls_h-------------------------------------------
			$sql_precost_commis_cost_dtls="insert into wo_pre_cost_commis_cost_dtls_h(id,approved_no,pre_cost_commiss_cost_dtls_id,job_no,particulars_id,
			commission_base_id,commision_rate,commission_amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted)
				select
				'', $approved_string_dtls, id,job_no,particulars_id,commission_base_id,commision_rate,commission_amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted from wo_pre_cost_commiss_cost_dtls where  job_no in ($book_nos)";
			//	echo $sql_precost_commis_cost_dtls;die;

			//--------------------------------------   wo_pre_cost_embe_cost_dtls_his---------------------------------------------------------------------------
			$sql_precost_embe_cost_dtls="insert into  wo_pre_cost_embe_cost_dtls_his(id,approved_no,pre_cost_embe_cost_dtls_id,job_no,emb_name,
			emb_type,cons_dzn_gmts,rate,amount,charge_lib_id,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted)
				select
				'', $approved_string_dtls, id,job_no,emb_name,
		emb_type,cons_dzn_gmts,rate,amount,charge_lib_id,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted from wo_pre_cost_embe_cost_dtls  where  job_no in ($book_nos)";
				//echo $sql_precost_commis_cost_dtls;die;

			//---------------------------------wo_pre_cost_fab_yarnbkdown_his------------------------------------------------

			$sql_precost_fab_yarnbkdown_his="insert into  wo_pre_cost_fab_yarnbkdown_his(id,approved_no,pre_cost_fab_yarnbreakdown_id,fabric_cost_dtls_id,job_no,count_id,copm_one_id,percent_one,copm_two_id,percent_two,type_id,cons_ratio,cons_qnty,rate,amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted)
				select
				'', $approved_string_dtls, id,fabric_cost_dtls_id,job_no,count_id,copm_one_id,percent_one,copm_two_id,percent_two,type_id,cons_ratio,cons_qnty,rate,amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted from wo_pre_cost_fab_yarnbreakdown  where  job_no in ($book_nos)";
				//echo $sql_precost_fab_yarnbkdown_his;die;

			//------------------------------wo_pre_cost_sum_dtls_histroy-----------------------------------------------

			$sql_precost_fab_sum_dtls="insert into  wo_pre_cost_sum_dtls_histroy(id,approved_no,pre_cost_sum_dtls_id,job_no,fab_yarn_req_kg,fab_woven_req_yds,fab_knit_req_kg,fab_woven_fin_req_yds,fab_knit_fin_req_kg,fab_amount,avg,yarn_cons_qnty,yarn_amount,conv_req_qnty,conv_charge_unit,conv_amount,trim_cons,trim_rate,trim_amount,emb_amount,comar_rate,
			comar_amount,commis_rate,commis_amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted)
				select
				'', $approved_string_dtls, id,job_no,fab_yarn_req_kg,fab_woven_req_yds,fab_knit_req_kg,fab_woven_fin_req_yds,fab_knit_fin_req_kg,fab_amount,avg,yarn_cons_qnty,yarn_amount,conv_req_qnty,conv_charge_unit,conv_amount,trim_cons,trim_rate,trim_amount,emb_amount,comar_rate,
			comar_amount,commis_rate,commis_amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted from wo_pre_cost_sum_dtls  where  job_no in ($book_nos)";
				//echo $sql_precost_fab_sum_dtls;die;
				//----------------------------------------------------wo_pre_cost_trim_cost_dtls_his------------------------------	------------------------------------------

			$sql_precost_trim_cost_dtls="insert into  wo_pre_cost_trim_cost_dtls_his(id,approved_no,pre_cost_trim_cost_dtls_id,job_no, trim_group,description,brand_sup_ref, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp,cons_breack_down,inserted_by, insert_date,updated_by,update_date, status_active,is_deleted)
				select
				'', $approved_string_dtls, id,job_no, trim_group,description,brand_sup_ref, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp,cons_breack_down,inserted_by, insert_date,updated_by,update_date, status_active,is_deleted from wo_pre_cost_trim_cost_dtls  where  job_no in ($book_nos)";
				//echo $sql_precost_trim_cost_dtls;die;


			//---------------------------wo_pre_cost_trim_co_cons_dtl_h--------------------------------------------------

			$sql_precost_trim_co_cons_dtl="insert into   wo_pre_cost_trim_co_cons_dtl_h(id,approved_no,pre_cost_trim_co_cons_dtls_id,wo_pre_cost_trim_cost_dtls_id,job_no, po_break_down_id,item_size, cons, place, pcs,country_id)
				select
				'', $approved_string_dtls, id,wo_pre_cost_trim_cost_dtls_id,job_no,po_break_down_id,item_size, cons,place, pcs,country_id from wo_pre_cost_trim_co_cons_dtls  where  job_no in ($book_nos)";
			//-----------------------------------------wo_pre_cost_fab_con_cst_dtls_h-----------------------------------------

			$sql_precost_fab_con_cst_dtls="insert into   wo_pre_cost_fab_con_cst_dtls_h(id,approved_no,pre_cost_fab_conv_cst_dtls_id,job_no, fabric_description,cons_process,req_qnty,charge_unit,amount,color_break_down,charge_lib_id,inserted_by,insert_date,updated_by,update_date, status_active,is_deleted)
				select
				'', $approved_string_dtls, id,job_no, fabric_description,cons_process,req_qnty,charge_unit,amount,color_break_down,charge_lib_id,inserted_by,insert_date,updated_by,update_date, status_active,is_deleted from wo_pre_cost_fab_conv_cost_dtls  where  job_no in ($book_nos)";


			if(count($sql_precost_trim_cost_dtls)>0)
			{
				$rID5=execute_query($sql_precost_trim_cost_dtls,1);
				if($flag==1)
				{
					if($rID5) $flag=1; else $flag=0;
				}
			}


			if(count($sql_precost_trim_cost_dtls)>0)
			{
				$rID6=execute_query($sql_precost_trim_co_cons_dtl,1);
				if($flag==1)
				{
					if($rID6) $flag=1; else $flag=0;
				}
			}

					//echo $sql_precost_fab_con_cst_dtls;die;
			$rID7=execute_query($sql_precost_fab_con_cst_dtls,1);
			if($flag==1)
			{
				if($rID7) $flag=1; else $flag=0;
			}

			if(count($sql_insert)>0)
			{
				$rID8=execute_query($sql_insert,0);
				if($flag==1)
				{
					if($rID8) $flag=1; else $flag=0;
				}
			}
			//echo '895='.$flag; die;
			if(count($sql_precost_dtls)>0)
			{
				$rID9=execute_query($sql_precost_dtls,1);
				if($flag==1)
				{
					if($rID9) $flag=1; else $flag=0;
				}
			}

			if(count($sql_precost_fabric_cost_dtls)>0)
			{
				$rID10=execute_query($sql_precost_fabric_cost_dtls,1);
				if($flag==1)
				{
					if($rID10) $flag=1; else $flag=0;
				}
			}

			if(count($sql_precost_fab_yarn_cst)>0)
			{
				$rID11=execute_query($sql_precost_fab_yarn_cst,1);
				if($flag==1)
				{
					if($rID11) $flag=1; else $flag=0;
				}
			}

			if(count($sql_precost_fcomarc_cost_dtls)>0)
			{
				$rID12=execute_query($sql_precost_fcomarc_cost_dtls,1);
				if($flag==1)
				{
					if($rID12) $flag=1; else $flag=0;
				}
			}
			if(count($sql_precost_commis_cost_dtls)>0)
			{
				$rID13=execute_query($sql_precost_commis_cost_dtls,1);
				if($flag==1)
				{
					if($rID13) $flag=1; else $flag=0;
				}
			}
			if(count($sql_precost_embe_cost_dtls)>0)
			{
				$rID14=execute_query($sql_precost_embe_cost_dtls,1);
				if($flag==1)
				{
					if($rID14) $flag=1; else $flag=0;
				}
			}

			if(count($sql_precost_fab_yarnbkdown_his)>0)
			{
				$rID15=execute_query($sql_precost_fab_yarnbkdown_his,1);
				if($flag==1)
				{
					if($rID15) $flag=1; else $flag=0;
				}
			}

			if(count($sql_precost_fab_sum_dtls)>0)
			{
				$rID16=execute_query($sql_precost_fab_sum_dtls,1);
				if($flag==1)
				{
					if($rID16) $flag=1; else $flag=0;
				}
			}
		}

		//echo count($full_approve_booking_id_arr);die;
		
		$rID9=1;
		if(count($full_approve_booking_id_arr)>0)
		{

			$field_array_full_approved_booking_update = "approved_by*approved_date";
			$rID4=execute_query(bulk_update_sql_statement( "wo_pre_cost_mst", "id", $field_array_full_approved_booking_update, $data_array_full_approve_booking_update, $full_approve_booking_id_arr));
			if($flag==1)
			{
				if($rID4) $flag=1; else $flag=0;
			}
			//sql_multirow_update("wo_pre_cost_mst","approved_by*approved_date",$updateData,"id",$booking_ids,1);
		}
 		
		
		$field_array_booking_update = "approved";
		//$rID=sql_multirow_update("wo_pre_cost_mst","approved",$partial_approval,"id",$booking_ids,0);

		$rID=execute_query(bulk_update_sql_statement( "wo_pre_cost_mst", "id", $field_array_booking_update, $data_array_booking_update, $booking_id_arr));

		if($flag==1)
		{
			if($rID) $flag=1; else $flag=0;
		}
		//echo $flag; die;
		
		$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=15 and mst_id in ($booking_ids)";
		$rIDapp=execute_query($query,1);
		
		if($flag==1)
		{
			if($rIDapp) $flag=1; else $flag=0;
		}
		
		$rID2=sql_insert("approval_history",$field_array,$data_array,0);
		 
		 
		 //print_r($data_array);die;
		 //echo $rID2; die;
		 
		 
		 
		 
		if($flag==1)
		{
			if($rID2) $flag=1; else $flag=0;
		}
		
		if($flag==1) $msg='19'; else $msg='21';
		//echo $msg."**".$flag;die;
	}
	else
	{
		//echo "10**".$booking_ids."**".$approval_ids;die;
		$booking_ids_all=explode(",",$booking_ids);

		$booking_ids=''; $app_ids='';

		foreach($booking_ids_all as $value)
		{
			$data = explode('**',$value);
			$booking_id=$data[0];
			$app_id=$data[1];

			if($booking_ids=='') $booking_ids=$booking_id; else $booking_ids.=",".$booking_id;
			if($app_ids=='') $app_ids=$app_id; else $app_ids.=",".$app_id;
		}
		
		$next_user_app = sql_select("select id from approval_history where mst_id in($booking_ids) and entry_form=15 and sequence_no >$user_sequence_no and current_approval_status=1 group by id");
				
		if(count($next_user_app)>0)
		{
			echo "25**unapproved"; 
			disconnect($con);
			die;
		}

		$rID=sql_multirow_update("wo_pre_cost_mst","approved*ready_to_approved",'2*0',"id",$booking_ids,0);
		if($rID) $flag=1; else $flag=0;
		//echo "22**".$rID;die;
		//$rID2=sql_multirow_update2("approval_history","current_approval_status",0,"entry_form*mst_id",15*$booking_ids,0);
		$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=15 and mst_id in ($booking_ids)";
		$rID2=execute_query($query,1);
		if($flag==1)
		{
			if($rID2) $flag=1; else $flag=0;
		}

		$unapproved_status="UPDATE fabric_booking_approval_cause SET status_active=0,is_deleted=1 WHERE entry_form=15 and approval_type=2 and is_deleted=0 and status_active=1 and booking_id in ($booking_ids)";
		//echo $unapproved_status;die;
		$rIDunapp=execute_query($unapproved_status,1);
		if($flag==1)
		{
			if($rIDunapp) $flag=1; else $flag=0;
		}

		$data=$user_id_approval."*'".$pc_date_time."'*".$user_id."*'".$pc_date_time."'";
		$rID3=sql_multirow_update("approval_history","un_approved_by*un_approved_date*updated_by*update_date",$data,"id",$approval_ids,1);
		if($flag==1)
		{
			if($rID3) $flag=1; else $flag=0;
		}
		$response=$booking_ids;
		if($flag==1) $msg='20'; else $msg='22';
	}
	
	if($db_type==0)
	{
		if($flag==1)
		{
			mysql_query("COMMIT");
			echo $msg."**".$response;
		}
		else
		{
			mysql_query("ROLLBACK");
			echo $msg."**".$response;
		}
	}
	else if($db_type==2 || $db_type==1 )
	{
		if($flag==1)
		{
			oci_commit($con);
			echo $msg."**".$response;
		}
		else
		{
			oci_rollback($con);
			echo $msg."**".$response;
		}
	}
	disconnect($con);
	die;

}

if($action=="img")
{
	echo load_html_head_contents("Image View", "../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:600px; margin-left:5px">
		<div style="width:100%; word-wrap:break-word" id="scroll_body">
             <table border="0" rules="all" width="100%" cellpadding="2" cellspacing="2">
             	<tr>
					<?
					$i=0;
                    $sql="select image_location from common_photo_library where master_tble_id='$id' and form_name='knit_order_entry' and file_type=1";

                    $result=sql_select($sql);
                    foreach($result as $row)
                    {
						$i++;
                    ?>
                    <!--<td align="center"><? echo $row[csf('image_location')];?></td>-->
                    	<td align="center"><img width="300px" height="180px" src="../../<? echo $row[csf('image_location')];?>" /></td>
                    <?
						if($i%2==0) echo "</tr><tr>";
                    }
                    ?>
                </tr>
            </table>
        </div>
	</fieldset>
	<?
	exit();
}

if($action=='user_popup')
{
	echo load_html_head_contents("Popup Info","../../",1, 1,'',1,'');
	?>

	<script>

	// flowing script for multy select data------------------------------------------------------------------------------start;
  function js_set_value(id)
  {
 	// alert(id)
	document.getElementById('selected_id').value=id;
	  parent.emailwindow.hide();
  }

	// avobe script for multy select data------------------------------------------------------------------------------end;

	</script>

	<form>
        <input type="hidden" id="selected_id" name="selected_id" />
       <?php
        $custom_designation=return_library_array( "select id,custom_designation from lib_designation ",'id','custom_designation');
		 $Department=return_library_array( "select id,department_name from  lib_department ",'id','department_name');	;
		 	//$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id and is_deleted=0");
			$sql="select a.id, a.user_name, a.department_id, a.user_full_name, a.designation from  user_passwd a,electronic_approval_setup b where a.id=b.user_id and b.company_id=$cbo_company_name and b.page_id=$menu_id and valid=1 and a.id!=$user_id  and b.is_deleted=0  and b.page_id=$menu_id order by b.sequence_no";
			//echo $sql;
		 $arr=array (2=>$custom_designation,3=>$Department);
		 echo  create_list_view ( "list_view", "User ID,Full Name,Designation,Department", "100,120,150,150,","630","220",0, $sql, "js_set_value", "id,user_name", "", 1, "0,department_id,designation,department_id", $arr , "user_name,user_full_name,designation,department_id", "requires/user_creation_controller", 'setFilterGrid("list_view",-1);' ) ;
		?>

	</form>
	<script language="javascript" type="text/javascript">
	  setFilterGrid("tbl_style_ref");
	</script>


	<?
}


/*if($action=="file")
{
	echo load_html_head_contents("File View", "../../", 1, 1,'','','');
	extract($_REQUEST);

	?>
	<fieldset style="width:600px; margin-left:5px">
		<div style="width:100%; word-wrap:break-word" id="scroll_body">
             <table border="0" rules="all" width="100%" cellpadding="2" cellspacing="2">
             	<tr>
					<?

					$i=0;
                    $sql="select image_location from common_photo_library where master_tble_id='$id' and form_name='quotation_entry' and file_type=2";
                    $result=sql_select($sql);
                    foreach($result as $row)
                    {
						$i++;
                    ?>
                    	<td width="100" align="center"><a href="../../<? echo $row[csf('image_location')]; ?>"><img width="89" height="97" src="../../file_upload/blank_file.png"><br>File-<? echo $i; ?></a></td>
                    <?
						if($i%6==0) echo "</tr><tr>";
                    }
                    ?>
                </tr>
            </table>
        </div>
	</fieldset>
	<?
	exit();
}*/

if($action=="populate_cm_compulsory")
{
	$cm_cost_compulsory=return_field_value("cm_cost_compulsory","variable_order_tracking","company_name ='".$data."' and variable_list=22 and is_deleted=0 and status_active=1");
	echo $cm_cost_compulsory;
	exit();
}



if ( $action=="app_mail_notification" )
{

require('../../mailer/class.phpmailer.php');
require('../../auto_mail/setting/mail_setting.php');

list($sysId,$mailId,$txt_alter_user)=explode('__',$data);
$sysId=str_replace('*',',',$sysId);

$txt_alter_user=str_replace("'","",$txt_alter_user);
$user_id=($txt_alter_user!='')?$txt_alter_user:$user_id;

	$sql="select a.ID,b.JOB_NO,b.STYLE_REF_NO,b.COMPANY_NAME,b.BUYER_NAME from wo_pre_cost_mst a,wo_po_details_master b where a.JOB_NO=b.JOB_NO and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id in($sysId)";  
	
	$sql_dtls=sql_select($sql);
	$dataArr=array();
	foreach($sql_dtls as $rows){
		$dataArr[company][$rows[COMPANY_NAME]]=$rows[COMPANY_NAME];
		$dataArr[data][$rows[COMPANY_NAME]][$rows[ID]]=$rows;
	}
	

	
	
			
	foreach($dataArr[company] as $company_name){
		
		$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$company_name and page_id=$menu_id and user_id=$user_id and is_deleted=0");
		
		
		$mailToArr=array();
			
		$elcetronicSql = "SELECT a.BUYER_ID,a.SEQUENCE_NO,a.BYPASS,b.USER_EMAIL  from electronic_approval_setup a join user_passwd b on a.user_id=b.id where b.valid=1 AND a.IS_DELETED=0 and a.page_id in(428,1717,2150) and a.company_id=$company_name and a.SEQUENCE_NO > $user_sequence_no order by a.SEQUENCE_NO"; //and a.page_id=2150 and a.entry_form=46
		 //echo $elcetronicSql;die;
		
		$elcetronicSqlRes=sql_select($elcetronicSql);
		foreach($elcetronicSqlRes as $rows){
			
			if($rows[BUYER_ID]!=''){
				foreach(explode(',',$rows[BUYER_ID]) as $bi){
					if($rows[USER_EMAIL]!='' && $bi==$buyer_name_id){$mailToArr[]=$rows[USER_EMAIL];}
					if($rows[BYPASS]==2){break;}
				}
			}
			else{
				if($rows[USER_EMAIL]){$mailToArr[]=$rows[USER_EMAIL];}
				if($rows[BYPASS]==2){break;}
			}
		}

		
		
			ob_start();	
			?>
			Dear Concerned,	<br />			
			Please approve following reference.				
			
			<table rules="all" border="1">
				<tr bgcolor="#CCCCCC">
					<td>SL</td>
					<td>Company</td>
					<td>Job No</td>
					<td>Style Ref</td>
					<td>Buyer</td>
				</tr>
				
				<?php 
				$i=1;
				foreach($dataArr[data][$company_name] as $row){ 
					$mailArr[$row[INSERTED_BY]]=$user_maill_arr[$row[INSERTED_BY]];
				?>
				<tr>
					<td><?=$i;?></td>
					<td><?=$company_arr[$company_name]?></td>
					<td><?=$row[JOB_NO]?></td>
					<td><?=$row[STYLE_REF_NO]?></td>
					<td><?=$buyer_arr[$row[BUYER_NAME]]?></td>
				</tr>
				<?php } ?>
			</table>
			<?	
				
				$message=ob_get_contents();
				ob_clean();
				$header=mailHeader();
				$to=implode(',',$mailToArr);
				$subject="Pre-costing approval WVN";
				if($to!="") echo sendMailMailer( $to, $subject, $message, $from_mail);
				//echo $message;
				  //echo $to;
		}
	exit();
}


?>
