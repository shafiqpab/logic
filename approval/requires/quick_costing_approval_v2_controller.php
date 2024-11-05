<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
extract($_REQUEST);
$permission=$_SESSION['page_permission'];

include('../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$menu_id=$_SESSION['menu_id'];
 

if($db_type==0) $year_cond="SUBSTRING_INDEX(a.insert_date, '-', 1) as year";
else if($db_type==2) $year_cond="to_char(a.insert_date,'YYYY') as year";

if($db_type==0) $year_cond_groupby="SUBSTRING_INDEX(a.insert_date, '-', 1)";
else if($db_type==2) $year_cond_groupby="to_char(a.insert_date,'YYYY')";

$userCredential = sql_select("SELECT brand_id, single_user_id FROM user_passwd where id=$user_id");
$userbrand_id = $userCredential[0][csf('brand_id')];
$single_user_id = $userCredential[0][csf('single_user_id')];

$userbrand_idCond = ""; $filterBrandId = "";
if ($userbrand_id !='' && $single_user_id==1) {
    $userbrand_idCond = "and id in ($userbrand_id)";
	$filterBrandId=$userbrand_id;
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

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$sequence_no='';
	$company_name=str_replace("'","",$cbo_company_name);

	//echo $txt_internal_ref; echo $txt_file_no;
	$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
	if($txt_alter_user_id!="")
	{
		if(str_replace("'","",$cbo_buyer_name)==0)
		{
			$log_sql = sql_select("SELECT user_level,buyer_id,unit_id,is_data_level_secured FROM user_passwd WHERE id = '$txt_alter_user_id' AND valid = 1");
			foreach($log_sql as $r_log)
			{
				if($r_log[csf('is_data_level_secured')]==1)
				{
					if($r_log[csf('buyer_id')]!="") $buyer_id_cond=" and a.buyer_id in (".$r_log[csf('buyer_id')].")"; else $buyer_id_cond="";
				}
				else $buyer_id_cond="";
			}
		}
		else $buyer_id_cond=" and a.buyer_id=$cbo_buyer_name";
		
		$user_id=$txt_alter_user_id;
	}
	else
	{
		if(str_replace("'","",$cbo_buyer_name)==0)
		{
			if ($_SESSION['logic_erp']["data_level_secured"]==1)
			{
				if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
			}
			else $buyer_id_cond="";
		}
		else $buyer_id_cond=" and a.buyer_id=$cbo_buyer_name";
	}
	
	$cbo_brand=str_replace("'","",$cbo_brand);
	$cbo_season_id=str_replace("'","",$cbo_season_id);
	$cbo_season_year=str_replace("'","",$cbo_season_year);
	$cbo_year=str_replace("'","",$cbo_year);
	
	if ($cbo_brand!=0) $brandCond=" and a.brand_id='".trim($cbo_brand)."' "; else if ($filterBrandId!="") $brandCond=" and a.brand_id in ($filterBrandId)"; else $brandCond="";
	if ($cbo_season_id=="" || $cbo_season_id==0) $seasonCond=""; else $seasonCond=" and a.season_id='".trim($cbo_season_id)."' ";
	if ($cbo_season_year=="" || $cbo_season_year==0) $seasonYearCond=""; else $seasonYearCond=" and a.season_year='".trim($cbo_season_year)."' ";
	
	if ($cbo_year=="" || $cbo_year==0) $year_cond="";
	else
	{
		if($db_type==2) $year_cond=" and to_char(a.insert_date,'YYYY')='".trim($cbo_year)."'";
		else $year_cond=" and YEAR(a.insert_date)='".trim($cbo_year)."'";
	}
	
	if($db_type==2) $year="TO_CHAR(a.insert_date,'YYYY')"; else $year="YEAR(a.insert_date)";

	$txt_costshit_no=str_replace("'","",$txt_costshit_no);
	$txt_style_no=str_replace("'","",$txt_style_ref);

	if ($txt_style_no=="") $style_cond=""; else $style_cond=" and a.style_ref='".trim($txt_style_no)."' ";
	if ($txt_costshit_no=="") $txt_costshit_no_cond=""; else $txt_costshit_no_cond=" and a.cost_sheet_no='".trim($txt_costshit_no)."' ";
	$date_cond='';
	if(str_replace("'","",$txt_date)!="")
	{
		if(str_replace("'","",$cbo_get_upto)==1) $date_cond=" and a.costing_date>$txt_date";
		else if(str_replace("'","",$cbo_get_upto)==2) $date_cond=" and a.costing_date<=$txt_date";
		else if(str_replace("'","",$cbo_get_upto)==3) $date_cond=" and a.costing_date=$txt_date";
		else $date_cond='';
	}

	$approval_type=str_replace("'","",$cbo_approval_type);
	if($previous_approved==1 && $approval_type==1)
	{
		$previous_approved_type=1;
	}
	//$user_id=133;
	$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup"," page_id=$menu_id and user_id=$user_id and is_deleted=0");
	$min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup"," page_id=$menu_id and is_deleted=0");
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	$user_arr=return_library_array( "select id, user_name from user_passwd", "id", "user_name");

	$buyer_ids_array = array();
	$buyerData = sql_select("select user_id,BRAND_ID, sequence_no, buyer_id,BYPASS from electronic_approval_setup where page_id=$menu_id and is_deleted=0 ");//and bypass=2//echo "select user_id, sequence_no, buyer_id from electronic_approval_setup where company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0 and bypass=2";
	foreach($buyerData as $row)
	{
		$buyer_ids_array[$row[csf('user_id')]]['u']=$row[csf('buyer_id')];
		$buyer_ids_array[$row[csf('sequence_no')]]['s']=$row[csf('buyer_id')];
		$brand_ids_array[$row[csf('user_id')]]['u']=$row[csf('BRAND_ID')];
		$brand_ids_array[$row[csf('sequence_no')]]['s']=$row[csf('BRAND_ID')];
		$seq_bypass_status_array[$row[csf('sequence_no')]]=$row[csf('BYPASS')];
		
	}
	//print_r($buyer_ids_array);
	if($user_sequence_no=="")
	{
		echo "<font style='color:#F00; font-size:14px; font-weight:bold'>You Have No Authority To Sign Quick Costing Approval V2.</font>";
		die;
	}
	 
	 //echo $previous_approved;die;
	if($previous_approved==1 && $approval_type==1)
	{
		$sequence_no_cond=" and c.sequence_no<'$user_sequence_no'";

		$sql="select a.id, a.qc_no, a.inquery_id, b.tot_fob_cost, a.brand_id, a.season_id, a.season_year, a.cost_sheet_id, a.cost_sheet_no, a.entry_form, $year as year, a.style_ref, a.buyer_id, a.delivery_date, a.exchange_rate, a.offer_qty, a.costing_date, a.revise_no, a.option_id, c.id as approval_id, c.sequence_no, c.approved_by,  c.approved_date, a.approved, a.inserted_by, a.revise_no, a.option_id, d.job_id, d.id as confirm_id from qc_mst a, qc_tot_cost_summary b, approval_history c, qc_confirm_mst d where c.mst_id=a.id and c.entry_form=45 and  a.qc_no=b.mst_id and b.mst_id=d.cost_sheet_id and a.status_active=1 and a.is_deleted=0 and a.approved in (1,3) and d.ready_to_approve=1 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.current_approval_status=1 $sequence_no_cond $buyer_id_cond $buyer_id_cond2 $date_cond $brandCond $seasonCond $seasonYearCond $year_cond $style_cond $txt_costshit_no ";
		//$buyer_id_cond //and d.job_id>0 
		//echo $sql;
	}
	else if($approval_type==2)
	{
		if($db_type==0)
		{
			$sequence_no = return_field_value("max(sequence_no) as seq","electronic_approval_setup"," page_id=$menu_id and sequence_no<$user_sequence_no and bypass = 2 and is_deleted=0","seq");
		}
		else
		{
			$sequence_no = return_field_value("max(sequence_no) as seq","electronic_approval_setup"," page_id=$menu_id and sequence_no<$user_sequence_no and bypass=2 and is_deleted=0","seq");
		}
		
		
		if($user_sequence_no==$min_sequence_no)
		{
			$buyer_ids = $buyer_ids_array[$user_id]['u'];
			if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.buyer_id in($buyer_ids)";
			$brand_ids = $brand_ids_array[$user_id]['u'];
			if($brand_ids=="") $brand_id_cond2=""; else $brand_id_cond2=" and a.BRAND_ID in($brand_ids)";
			
			//$buyer_id_cond=""; $buyer_id_cond2=""; 
			$sql="select a.id, a.qc_no, a.inquery_id, b.tot_fob_cost, a.brand_id, a.season_id, a.season_year, a.cost_sheet_id, a.cost_sheet_no, a.entry_form, $year as year, a.style_ref, a.buyer_id, a.delivery_date, a.exchange_rate, a.offer_qty, a.costing_date, a.revise_no, a.option_id, 0 as approval_id, a.approved, a.inserted_by, a.revise_no, a.option_id, c.job_id, c.id as confirm_id from qc_mst a, qc_tot_cost_summary b, qc_confirm_mst c where a.qc_no=b.mst_id and b.mst_id=c.cost_sheet_id and a.approved in (0,2) and c.ready_to_approve=1 and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $buyer_id_cond $buyer_id_cond2 $brand_id_cond2 $date_cond $brandCond $seasonCond $seasonYearCond $year_cond $style_cond $txt_costshit_no ";
			//echo $sql;die;
		}
		else if($sequence_no=="")
		{
			$buyer_ids=$buyer_ids_array[$user_id]['u'];
			if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.buyer_id in($buyer_ids)";
			if($buyer_ids=="") $buyer_id_cond3=""; else $buyer_id_cond3=" and a.buyer_id in($buyer_ids)";

			$brand_ids=$brand_ids_array[$user_id]['u'];
			if($brand_ids=="") $brand_id_cond2=""; else $brand_id_cond2=" and a.brand_id in($brand_ids)";
			if($brand_ids=="") $brand_id_cond3=""; else $brand_id_cond3=" and a.brand_id in($brand_ids)";
			//$buyer_id_cond=""; $buyer_id_cond2=""; $buyer_id_cond3=""; 

			$seqSql="select sequence_no, bypass, buyer_id,brand_id from electronic_approval_setup where company_id=0 and page_id=$menu_id and sequence_no<$user_sequence_no and is_deleted=0 order by sequence_no desc";
			$seqData=sql_select($seqSql);
			
			$brandIds=''; $buyerIds=''; $sequence_no_by_yes=''; $sequence_no_by_no=''; $approved_string=''; $check_buyerIds_arr=array();$check_brandIds_arr=array();
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
							$query_string.=" (b.sequence_no=".$sRow[csf('sequence_no')]." and a.buyer_id in(".implode(",",$result).")) or ";
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
				$buyerIds_cond=" and a.buyer_id not in($buyerIds)"; 
				$seqCond=" and (".chop($query_string,'or ').")";
			}
			
			$brandIds=chop($brandIds,',');
			if($brandIds=="")
			{
				$brandIds_cond=""; $brand_seqCond="";
			}
			else
			{
				$brandIds_cond=" and a.brand_id not in($brandIds)"; 
				$brand_seqCond=" and (".chop($brand_query_string,'or ').")";
			}
			
			
			$sequence_no_by_no=chop($sequence_no_by_no,',');
			$sequence_no_by_yes=chop($sequence_no_by_yes,',');

			if($sequence_no_by_yes=="") $sequence_no_by_yes=0;
			if($sequence_no_by_no=="") $sequence_no_by_no=0;

			$qc_id='';
			$qc_id_sql="select distinct (b.mst_id) as qc_id from qc_mst a, approval_history b where a.id=b.mst_id and b.sequence_no in ($sequence_no_by_no) and b.entry_form=45 and b.current_approval_status=1 $buyer_id_cond3  $seqCond $brand_seqCond
			union
			select distinct (b.mst_id) as qc_id from qc_mst a, approval_history b where a.id=b.mst_id  and b.sequence_no in ($sequence_no_by_yes) and b.entry_form=45 and b.current_approval_status=1 $buyer_id_cond3 ";
			
			  //echo $qc_id_sql;die;
			
			
			$bResult=sql_select($qc_id_sql);
			foreach($bResult as $bRow)
			{
				$qc_id.=$bRow[csf('qc_id')].",";
			}

			$qc_id=chop($qc_id,',');

			$qc_id_app_sql=sql_select("select b.mst_id as qc_id from qc_mst a, approval_history b where a.id=b.mst_id  and b.sequence_no=$user_sequence_no and b.entry_form=45 and b.current_approval_status=1");

			foreach($qc_id_app_sql as $inf)
			{
				if($qc_id_app_byuser!="") $qc_id_app_byuser.=",".$inf[csf('pre_cost_id')];
				else $qc_id_app_byuser.=$inf[csf('pre_cost_id')];
			}

			$qc_id_app_byuser=implode(",",array_unique(explode(",",$qc_id_app_byuser)));
			

			$qc_id_app_byuser=chop($qc_id_app_byuser,',');
			$result=array_diff(explode(',',$qc_id),explode(',',$qc_id_app_byuser));
			$qc_id=implode(",",$result);
			//echo $pre_cost_id;die;
			$qc_id_cond="";

			if($qc_id_app_byuser!="")
			{
				$qc_id_app_byuser_arr=explode(",",$qc_id_app_byuser);
				if(count($qc_id_app_byuser_arr)>995)
				{
					$qc_id_app_byuser_arr_chunk_arr=array_chunk(explode(",",$qc_id_app_byuser_arr),995) ;
					foreach($qc_id_app_byuser_arr_chunk_arr as $chunk_arr)
					{
						$chunk_arr_value=implode(",",$chunk_arr);
						$qc_id_cond.=" and b.id not in($chunk_arr_value)";
					}
				}
				else
				{
					$qc_id_cond=" and b.id not in($pre_cost_id_app_byuser)";
				}
			}
			else $qc_id_cond="";

			$sql="select a.id, a.qc_no, a.inquery_id, b.tot_fob_cost, a.brand_id, a.season_id, a.season_year, a.cost_sheet_id, a.cost_sheet_no, a.entry_form, $year as year, a.style_ref, a.buyer_id, a.delivery_date, a.exchange_rate, a.offer_qty, a.costing_date, a.revise_no, a.option_id, 0 as approval_id, a.approved, a.inserted_by, a.revise_no, a.option_id, c.job_id, c.id as confirm_id from qc_mst a, qc_tot_cost_summary b, qc_confirm_mst c where a.qc_no=b.mst_id and b.mst_id=c.cost_sheet_id and a.status_active=1 and a.is_deleted=0  and a.approved in (0,2) and c.ready_to_approve=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $qc_id_cond $buyer_id_cond $buyer_id_cond2 $brand_id_cond2 $date_cond  $brandCond $seasonCond $seasonYearCond $year_cond $style_cond $txt_costshit_no ";
			 //echo $sql;die;
			 //echo $qc_id;die;
			if($qc_id!="")
			{
				$sql.=" union all
				select a.id, a.qc_no, a.inquery_id, b.tot_fob_cost, a.brand_id, a.season_id, a.season_year, a.cost_sheet_id, a.cost_sheet_no, a.entry_form, $year as year, a.style_ref, a.buyer_id, a.delivery_date, a.exchange_rate, a.offer_qty, a.costing_date, a.revise_no, a.option_id, 0 as approval_id, a.approved, a.inserted_by, a.revise_no, a.option_id, c.job_id, c.id as confirm_id from qc_mst a, qc_tot_cost_summary b, qc_confirm_mst c
				where  a.qc_no=b.mst_id and b.mst_id=c.cost_sheet_id  and a.status_active=1 and a.is_deleted=0  and a.approved in (1,3) and c.ready_to_approve=1 and  b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
				  and (a.id in($qc_id)) $buyer_id_cond $buyer_id_cond2 $brand_id_cond2 $date_cond $brandCond $seasonCond $seasonYearCond $year_cond $style_cond $txt_costshit_no";
				  //and c.job_id>0 
			}
			
			  //echo $sql;die;
			
		}
		else
		{
			$buyer_ids=$buyer_ids_array[$user_id]['u'];
			if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.buyer_id in($buyer_ids)";
			
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
					$sequence_no_by_pass=return_field_value("group_concat(sequence_no)","electronic_approval_setup"," page_id=$menu_id and sequence_no between $sequence_no and $user_sequence_no and is_deleted=0");// and bypass=1
				}
				else
				{
					$sequence_no_by_pass=return_field_value("LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no","electronic_approval_setup"," page_id=$menu_id and sequence_no between $sequence_no and $user_sequence_no and is_deleted=0","sequence_no");//bypass=1 and
				}
			}

			if($sequence_no_by_pass=="") $sequence_no_cond=" and c.sequence_no='$sequence_no'";
			else $sequence_no_cond=" and c.sequence_no in ($sequence_no_by_pass)";
			
			
			 //echo $user_sequence_no;die;
			
			
			$sql="select a.id, a.qc_no, a.inquery_id, b.tot_fob_cost, a.brand_id, a.season_id, a.season_year, a.cost_sheet_id, a.cost_sheet_no, a.entry_form, $year as year, a.style_ref, a.buyer_id, a.delivery_date, a.exchange_rate, a.offer_qty, a.costing_date, a.revise_no, a.option_id, c.id as approval_id, c.sequence_no, c.approved_by,  c.approved_date, a.approved, a.inserted_by, a.revise_no, a.option_id, d.job_id, d.id as confirm_id from qc_mst a, qc_tot_cost_summary b, approval_history c, qc_confirm_mst d where c.mst_id=a.id and c.entry_form=45 and a.qc_no=b.mst_id and b.mst_id=d.cost_sheet_id  and a.status_active=1 and a.is_deleted=0 and a.approved in (1,3) and d.ready_to_approve=1 and  b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.current_approval_status=1 $sequence_no_cond $buyer_id_cond $buyer_id_cond2 $brand_id_cond2 $date_cond $brandCond $seasonCond $seasonYearCond $year_cond $style_cond $txt_costshit_no";
			
			//for self buyer and brand...............
			
			if($brand_ids_array[$user_sequence_no]['s']!='' && $sequence_no_by_pass==''){
				foreach(explode(',',$brand_ids_array[($user_sequence_no+1)]['s']) as $ubid){
					$useq=$brand_id_wise_seq[$ubid];
					$sql.=" union all select a.id, a.qc_no, a.inquery_id, b.tot_fob_cost, a.brand_id, a.season_id, a.season_year, a.cost_sheet_id, a.cost_sheet_no,  a.entry_form, $year as year, a.style_ref, a.buyer_id, a.delivery_date, a.exchange_rate, a.offer_qty, a.costing_date, a.revise_no, a.option_id, c.id as approval_id, c.sequence_no, c.approved_by,  c.approved_date, a.approved, a.inserted_by, a.revise_no, a.option_id, d.job_id, d.id as confirm_id from qc_mst a, qc_tot_cost_summary b, approval_history c, qc_confirm_mst d where c.mst_id=a.id and c.entry_form=45 and a.qc_no=b.mst_id and b.mst_id=d.cost_sheet_id  and a.status_active=1 and a.is_deleted=0 and a.approved in (1,3) and d.ready_to_approve=1 and  b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.current_approval_status=1  and c.sequence_no='$useq'  and a.brand_id=$ubid $buyer_id_cond $buyer_id_cond2  $date_cond $brandCond $seasonCond $seasonYearCond $year_cond $style_cond $txt_costshit_no";
				}
			}
			
			
			if($brand_id_cond3!=''){
			$sql.=" union all
				select a.id, a.qc_no, a.inquery_id, b.tot_fob_cost, a.brand_id, a.season_id, a.season_year, a.cost_sheet_id, a.cost_sheet_no, a.entry_form, $year as year, a.style_ref, a.buyer_id, a.delivery_date, a.exchange_rate, a.offer_qty, a.costing_date, a.revise_no, a.option_id, 0 as approval_id, 0 as sequence_no, 0 as approved_by,  null as approved_date, a.approved, a.inserted_by, a.revise_no, a.option_id, d.job_id, d.id as confirm_id from qc_mst a, qc_tot_cost_summary b, qc_confirm_mst d where a.qc_no=b.mst_id and b.mst_id=d.cost_sheet_id  and a.status_active=1 and a.is_deleted=0 and a.approved in (0,2) and d.ready_to_approve=1 and  b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $date_cond $brandCond $seasonCond $seasonYearCond $year_cond $style_cond $txt_costshit_no  $buyer_id_cond3 $brand_id_cond3";
			}
				  //and c.job_id>0 
				  
				     //echo $sql;
		}
	}
	else
	{ 
		$buyer_ids=$buyer_ids_array[$user_id]['u'];
		if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.buyer_id in($buyer_ids)";
		$brand_ids=$brand_ids_array[$user_id]['u'];
		if($brand_ids=="") $brand_id_cond2=""; else $brand_id_cond2=" and a.brand_id in($brand_ids)";
		
		//$buyer_id_cond=""; $buyer_id_cond2=""; $buyer_id_cond3=""; 
		$sequence_no_cond=" and c.sequence_no='$user_sequence_no'";
		$sql="select a.id, a.qc_no, a.inquery_id, b.tot_fob_cost, a.brand_id, a.season_id, a.season_year, a.cost_sheet_id, a.cost_sheet_no, a.entry_form, $year as year, a.style_ref, a.buyer_id, a.delivery_date, a.exchange_rate, a.offer_qty, a.costing_date, a.revise_no, a.option_id, c.id as approval_id, c.sequence_no, c.approved_by, c.approved_date, a.approved, a.inserted_by, a.revise_no, a.option_id, d.job_id, d.id as confirm_id from qc_mst a, qc_tot_cost_summary b, approval_history c, qc_confirm_mst d where c.mst_id=a.id and c.entry_form=45 and a.qc_no=b.mst_id and b.mst_id=d.cost_sheet_id  and a.status_active=1 and a.is_deleted=0  and a.approved in (1,3) and d.ready_to_approve=1 and  b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.current_approval_status=1 $sequence_no_cond $buyer_id_cond $buyer_id_cond2 $brand_id_cond2 $date_cond $brandCond $seasonCond $seasonYearCond $year_cond $style_cond $txt_costshit_no ";
	}
	  // echo $sql; 
	
	//$isApproved=return_library_array( "select qc_no, approved from lib_supplier", "qc_no", "approved");
	
	/*$qcmargin="Select a.qc_no, a.buyer_id, a.approved, a.delivery_date, b.smv, b.efficency, b.available_min from qc_mst a, qc_margin_mst b where a.qc_no=b.qc_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	
	$marginArr=array(); $bookMinArr=array();
	$qcmarginData=sql_select($qcmargin);
	
	foreach($qcmarginData as $mrow)
	{
		$marginArr[$mrow[csf('qc_no')]]['smv']=$mrow[csf('smv')];
		$marginArr[$mrow[csf('qc_no')]]['eff']=$mrow[csf('efficency')];
		$marginArr[$mrow[csf('qc_no')]]['avlmin']=$mrow[csf('available_min')];
		if($mrow[csf('approved')]==1)
		{
			$exdeldate=explode("-",change_date_format($mrow[csf('delivery_date')]));
			//print_r($exdeldate);
			
			$m=ltrim($exdeldate[1], '0'); $y=$exdeldate[2];
			$mmyy=$m.','.$y;
			$bookMinArr[$mrow[csf('buyer_id')]][$mmyy]+=$mrow[csf('available_min')];
		}
	}
	unset($qcmarginData);
	
	$salesforcust="select a.buyer_id, b.year_month_name, b.sales_target_mint from wo_sales_target_mst a, wo_sales_target_dtls b where a.id=b.sales_target_mst_id and a.status_active=1 and a.is_deleted=0";
	
	$salesforArr=array();
	$salesforcustData=sql_select($salesforcust);
	
	foreach($salesforcustData as $srow)
	{
		if($srow[csf('sales_target_mint')]!="") $salesforArr[$srow[csf('buyer_id')]][$srow[csf('year_month_name')]]+=$srow[csf('sales_target_mint')];
		//$salesforArr[$srow[csf('qc_no')]]['eff']=$srow[csf('efficency')];
	}
	unset($salesforcustData);*/
	//print_r($salesforArr[65]['7,2020']); die;
	?>
    <script>
		function openmypage_app_cause(wo_id,app_type,i)
		{
			var txt_appv_cause = $("#txt_appv_cause_"+i).val();
			var approval_id = $("#approval_id_"+i).val();
			var data=wo_id+"_"+app_type+"_"+txt_appv_cause+"_"+approval_id;
			var title = 'Approval Cause Info';
			var page_link = 'requires/quick_costing_approval_v2_controller.php?data='+data+'&action=appcause_popup';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=250px,center=1,resize=1,scrolling=0','');
			emailwindow.onclose=function()
			{
				var appv_cause=this.contentDoc.getElementById("hidden_appv_cause");
				$('#txt_appv_cause_'+i).val(appv_cause.value);
			}
		}

		function openmypage_app_instrac(wo_id,app_type,i)
		{
			var txt_appv_instra = $("#txt_appv_instra_"+i).val();
			var approval_id = $("#approval_id_"+i).val();
			var data=wo_id+"_"+app_type+"_"+txt_appv_instra+"_"+approval_id;
			var title = 'Approval Instruction';
			var page_link = 'requires/quick_costing_approval_v2_controller.php?data='+data+'&action=appinstra_popup';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=250px,center=1,resize=1,scrolling=0','');
			emailwindow.onclose=function()
			{
				var appv_cause=this.contentDoc.getElementById("hidden_appv_cause");
				$('#txt_appv_instra_'+i).val(appv_cause.value);
			}
		}

		function openmypage_unapp_request(wo_id,app_type,i)
		{
			var data=wo_id;
			var title = 'Un Approval Request';
			var page_link = 'requires/quick_costing_approval_v2_controller.php?data='+data+'&action=unappcause_popup';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=180px,center=1,resize=1,scrolling=0','');
			emailwindow.onclose=function()
			{
				var unappv_request=this.contentDoc.getElementById("hidden_unappv_request");
				$('#txt_unappv_req_'+i).val(unappv_request.value);
			}
		}
	</script>
    <?
	$print_report_format=return_field_value("format_id","lib_report_template","module_id=2 and report_id=83 and is_deleted=0 and status_active=1");
	//echo $print_report_format;die;
	$format_ids=explode(",",$print_report_format);
	$row_id=$format_ids[0];
	$report_action="quick_costing_print";
	//if($row_id=84) $report_action="quick_costing_print2";
	
	$sql_request="select booking_id, approval_cause from fabric_booking_approval_cause where entry_form=28 and approval_type=2 and status_active=1 and is_deleted=0 order by id Desc";
	$unappRequest_arr=array();

	$nameArray_request=sql_select($sql_request);
	foreach($nameArray_request as $approw)
	{
		$unappRequest_arr[$approw[csf("booking_id")]]=$approw[csf("approval_cause")];
	}
	$seasonArr = return_library_array("select id,season_name from lib_buyer_season ","id","season_name");
	$brandArr = return_library_array("select id,brand_name from lib_buyer_brand ","id","brand_name");
	$concernMarchantArr=return_library_array( "select id, concern_marchant from wo_quotation_inquery where entry_form=434", "id", "concern_marchant");
	$teamMemberinfoArr=return_library_array( "select id, team_member_name from lib_mkt_team_member_info", "id", "team_member_name");
	//echo $report_action;die;
	?>
    <form name="requisitionApproval_2" id="requisitionApproval_2">
        <fieldset style="width:1450px; margin-top:10px">
        <legend>Quick Costing Approval V2</legend>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1450" class="rpt_table" >
                <thead>
                	<th width="30">&nbsp;</th>
                    <th width="30">SL</th>
                    <th width="120">Buyer</th>
                    <th width="120">Master Style</th>
                    <th width="80">Brand</th>
                    <th width="80">Season</th>
                    <th width="50">Season Year</th>
                    <th width="100">Cost Sheet No</th>
                    <th width="50">Year</th>
                   	<th width="70">Revise No</th>
                   	<th width="70">Option No</th>
                    <th width="65">Costing Date</th>
                    <th width="100">Insert By</th>
                    <th width="70">Offer Qty.</th>
                    <th width="70">FOB Cost</th>
                   	<th width="70">Concern Merchant</th>
                    <th width="70">Approved Date</th>
                    <?
					if($approval_type==0) echo "<th width='80'>Appv Instra</th>";
					if($approval_type==1) echo "<th width='80'>Un-Appv Request</th>";
					?>
                    <th><? if($approval_type==0) echo "Not Appv. Cause" ; else echo "Not Un-Appv. Cause"; ?></th>
                </thead>
            </table>
            <div style="width:1450px; overflow-y:scroll; max-height:330px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1432" class="rpt_table" id="tbl_list_search">
                    <tbody>
                        <?
                        $i=1;
						$nameArray=sql_select( $sql );
						// print ($sql);die;
						$ref_no=""; $file_numbers="";
						foreach ($nameArray as $row)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							if($approval_type==2) $value=$row[csf('id')]; else $value=$row[csf('id')]."**".$row[csf('approval_id')]."**".$row[csf('confirm_id')];;
							
							$fob_cost=$row[csf('tot_fob_cost')];
							if($fob_cost=='' || $fob_cost==0) $fob_cost=0; else $fob_cost=$fob_cost;
							if($fob_cost<0 || $fob_cost==0) $td_color="#F00"; else $td_color="";
							
							//$exdeldate=explode("-",change_date_format($row[csf('delivery_date')]));
							//print_r($exdeldate);
							
							//$m=ltrim($exdeldate[1], '0'); $y=$exdeldate[2];
							//$mmyy=$m.','.$y;
							
							//$styleAvlMin=$buyerForcutMin=$balanceMin=0;
							//$bookedMin=0;
							//$styleAvlMin=$marginArr[$row[csf('qc_no')]]['avlmin'];//$marginArr[$row[csf('qc_no')]]['smv']*$row[csf('offer_qty')]*($marginArr[$row[csf('qc_no')]]['eff']/100);
							//$buyerForcutMin=$salesforArr[$row[csf('buyer_id')]][$mmyy];
							//$bookedMin=$bookMinArr[$row[csf('buyer_id')]][$mmyy];
							/*if($approval_type==1)
							{
								$bookedMin+=$styleAvlMin;
							}*/
							
							//$balanceMin=$buyerForcutMin-$bookedMin;
							
							//$bookMinArr[$row[csf('buyer_id')]][$mmyy]-=$buyerForcutMin;
							
							?>
							<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>')" id="tr_<?=$i; ?>" align="center">
								<td width="27" align="center" valign="middle">
									<input type="checkbox" id="tbl_<?=$i;?>" />
									<input id="booking_id_<?=$i;?>" name="booking_id[]" type="hidden" value="<?=$value; ?>" />
									<input id="confirm_id_<?=$i;?>" name="confirm_id[]" type="hidden" value="<?=$row[csf('confirm_id')]; ?>" />
									<input id="booking_no_<?=$i;?>" name="booking_no[]" type="hidden" value="<?=$row[csf('qc_no')]; ?>" />
									<input id="approval_id_<?=$i;?>" name="approval_id[]" type="hidden" value="<?=$row[csf('approval_id')]; ?>" />
									<input id="<?=strtoupper($row[csf('cost_sheet_no')]); ?>" name="no_joooob[]" type="hidden" value="<?=$i;?>" />
									<input id="cm_cost_id_<?=$i;?>" name="cm_cost_id[]" style="width:20px;" type="hidden" value="<?=$fob_cost; ?>" />
								</td>
								<td width="30" align="center"><?=$i; ?></td>
                                <td width="120" style="word-break:break-all"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?>&nbsp;</td>
                                <td width="120" align="center" style="word-break:break-all"><a href='##' onClick="fnc_print_report(<?=$row[csf('qc_no')];?>,<?=$row[csf('cost_sheet_no')]; ?>,<?=$row[csf('entry_form')]; ?>,'<?=$report_action; ?>' )"><?=$row[csf('style_ref')]; ?></a></td>
                                
                                <td width="80" style="word-break:break-all"><?=$brandArr[$row[csf('brand_id')]]; ?></td>
                                <td width="80" style="word-break:break-all"><?=$seasonArr[$row[csf('season_id')]]; ?></td>
                                <td width="50" style="word-break:break-all"><?=$row[csf('season_year')]; ?></td>
                                
                                <td width="100" style="word-break:break-all;"><?=$row[csf('cost_sheet_no')]; ?></td>
                                <td width="50" style="word-break:break-all" align="center"><?=$row[csf('year')]; ?>&nbsp;</td>
                                <td width="70" style="word-break:break-all"><?=$row[csf('revise_no')]; ?></td>
								<td width="70" style="word-break:break-all"><?=$row[csf('option_id')]; ?></td>
                                <td width="65" align="center"><? if($row[csf('costing_date')]!="0000-00-00") echo change_date_format($row[csf('costing_date')]); ?>&nbsp;</td>
                                <td width="100" style="word-break:break-all"><?=ucfirst($user_arr[$row[csf('inserted_by')]]); ?>&nbsp;</td>
								<td width="70" style="word-break:break-all" align="right"><?=$row[csf('offer_qty')]; ?></td>
								<td width="70" align="right"><p style="color:<?=$td_color; ?>"><?=number_format($fob_cost,2); ?>&nbsp;</p></td>
								<td width="70" style="word-break:break-all"><?=$teamMemberinfoArr[$concernMarchantArr[$row[csf('inquery_id')]]]; ?></td>
								<td width="70" align="center"><? if($row[csf('approved_date')]!="0000-00-00") echo $row[csf('approved_date')]; ?>&nbsp;</td>
								<?
									if($approval_type==0)echo "<td align='center' width='80'>
										<Input name='txt_appv_instra[]' class='text_boxes' readonly placeholder='Please Browse' ID='txt_appv_instra_".$i."' style='width:70px' maxlength='50' onClick='openmypage_app_instrac(".$row[csf('qc_no')].",".$approval_type.",".$i.")' ></td>";
									if($approval_type==1)echo "<td align='center' width='80'>
										<Input name='txt_unappv_req[]' class='text_boxes' readonly placeholder='Please Browse' ID='txt_unappv_req_".$i."' style='width:70px' maxlength='50' onClick='openmypage_unapp_request(".$row[csf('qc_no')].",".$approval_type.",".$i.")' value='".$unappRequest_arr[$row[csf('qc_no')]]."'></td>";
								?>
								<td align="center">
									<Input name="txt_appv_cause[]" class="text_boxes" readonly placeholder="Please Browse" ID="txt_appv_cause_<?=$i; ?>" style="width:70px" maxlength="50" title="Maximum 50 Character" onClick="openmypage_app_cause(<?=$row[csf('qc_no')]; ?>,<?=$approval_type; ?>,<?=$i; ?>)">&nbsp;</td>
							</tr>
							<?
							$i++;
						}
                        ?>
                    </tbody>
                </table>
            </div>
            <table cellspacing="0" cellpadding="0" border="0" rules="all" width="1450" class="rpt_table">
				<tfoot>
                    <td width="50" align="center" ><input type="checkbox" id="all_check" onClick="check_all('all_check');" /></td>
                    <td colspan="2" align="left"><input type="button" value="<? if($approval_type==1) echo "Un-Approve"; else echo "Approve"; ?>" class="formbutton" style="width:100px" onClick="submit_approved(<?=$i; ?>,<?=$approval_type; ?>)"/></td>
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
	if($txt_alter_user_id!="") $user_id_approval=$txt_alter_user_id; else $user_id_approval=$user_id;

	$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup"," page_id=$menu_id and user_id=$user_id_approval and is_deleted=0");

	$buyer_arr=return_library_array( "select a.id, a.buyer_id from qc_mst a where a.is_deleted=0 and a.status_active=1 and a.id in ($booking_ids)", "id", "buyer_id");
	$brand_arr=return_library_array( "select a.id, a.brand_id from qc_mst a where a.is_deleted=0 and a.status_active=1 and a.id in ($booking_ids)", "id", "brand_id");
	$min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup"," page_id=$menu_id and is_deleted=0");
	
	$electronic_setup_sql = sql_select("select b.USER_ID,b.BUYER_ID,B.BRAND_ID from electronic_approval_setup b where b.page_id=$menu_id and b.user_id <> $user_id_approval and sequence_no>$user_sequence_no and b.is_deleted=0 group by b.USER_ID,b.BUYER_ID,B.BRAND_ID"); 
	foreach ($electronic_setup_sql as $row) {
		$otherUserBuyerArr[$row[USER_ID]] = $row[BUYER_ID];
		$otherUserBrandArr[$row[USER_ID]] = $row[BRAND_ID];
	}
	
	$otherUserBuyerArr=array_filter(array_unique(explode(',',implode(',',$otherUserBuyerArr))));
	$otherUserBrandArr=array_filter(array_unique(explode(',',implode(',',$otherUserBrandArr))));
	
	
	if($approval_type==2)
	{
		if($min_sequence_no != $user_sequence_no)
		{
			$sql = sql_select("select b.buyer_id as buyer_id, b.sequence_no,b.BRAND_ID from electronic_approval_setup b where b.page_id=$menu_id and b.sequence_no < $user_sequence_no and b.is_deleted=0 and bypass=2 group by b.buyer_id,b.sequence_no,b.BRAND_ID order by b.sequence_no ASC");
			
			foreach ($sql as $key => $buyerID) {
				$allUserBuyersArr[$buyerID[csf('sequence_no')]] = $buyerID[csf('buyer_id')];
				$allBrandByUserSeqArr[$buyerID[csf('sequence_no')]] =explode(',',$buyerID[csf('BRAND_ID')]);
				$buyerIds.=$buyerID[csf('buyer_id')].",";
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



			$sql = sql_select("select b.buyer_id as buyer_id,B.BRAND_ID from electronic_approval_setup b where b.page_id=$menu_id and b.sequence_no = $user_sequence_no and b.is_deleted=0 group by b.buyer_id,B.BRAND_ID"); 
			
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

			
			$userBuyer=0;
			if(count($currUserBuyersArr)>0)
			{
				foreach ($currUserBuyersArr as $user_id => $buyer_string) {
					$user_buyer_arr = explode(',',$buyer_string);
					foreach ($user_buyer_arr as $buyer_id) {
						$curr_buyer_by_seq[$buyer_id] = $user_id;
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
			
			
			
			
			if((count($match_seq)>0 || $userBuyer==1) and (count($brand_match_seq) || $userBrand==1))
			{
				$previous_user_seq = implode(',', $match_seq);
				$previous_user_app = sql_select("select id from approval_history where mst_id in($booking_ids) and entry_form=45 and sequence_no <$user_sequence_no and current_approval_status=1 group by id");
				
				
				if(count($previous_user_app)==0)
				{
					$previous_user_app = sql_select("select id from approval_history where mst_id in($booking_ids) and entry_form=45 and sequence_no in ($previous_user_seq) and current_approval_status=1 group by id");
				}
				
				if(count($previous_user_app)==0)
				{
					echo "25**approved"; 
					disconnect($con);
					die;
				}
			}				
		}
		
		if($db_type==2) { $buyer_id_cond=" and a.buyer_id is null"; $buyer_id_cond2=" and b.buyer_id is not null"; }
		else { $buyer_id_cond=" and a.buyer_id=''"; $buyer_id_cond2=" and b.buyer_id!=''";}
		//echo "22**";
		$is_not_last_user=return_field_value("a.sequence_no","electronic_approval_setup a"," a.page_id=$menu_id and a.sequence_no>$user_sequence_no and a.bypass=2 and a.is_deleted=0 $buyer_id_cond","sequence_no");

		// echo " a.page_id=$menu_id and a.sequence_no>$user_sequence_no and a.bypass=2 and a.is_deleted=0 $buyer_id_cond";die;

		$partial_approval = "";
		if($is_not_last_user == "")
		{
			//$credentialUserBuyersArr = [];
			$sql = sql_select("select (b.buyer_id) as buyer_id,b.brand_id from electronic_approval_setup b where b.page_id=$menu_id and b.sequence_no>$user_sequence_no and b.is_deleted=0 and b.bypass=2  group by b.buyer_id,b.brand_id");
			foreach ($sql as $key => $buyerID) {
				$credentialUserBuyersArr[] = $buyerID[csf('buyer_id')];
				$credentialUserBrandArr[] = $buyerID[csf('brand_id')];
			}

			$credentialUserBuyersArr = implode(',',$credentialUserBuyersArr);
			$credentialUserBuyersArr = explode(',',$credentialUserBuyersArr);
			$credentialUserBuyersArr = array_unique($credentialUserBuyersArr);
			
			$credentialUserBrandArr=array_unique(explode(',',implode(',',$credentialUserBrandArr)));
			
			
		}
		else
		{
			$check_user_buyer = sql_select("select b.user_id as user_id,b.brand_id from user_passwd a, electronic_approval_setup b where a.id = b.user_id and b.page_id=$menu_id and b.sequence_no>$user_sequence_no and b.is_deleted=0 and b.bypass=2 $buyer_id_cond  group by b.user_id,b.brand_id");
			//echo "21**".count($check_user_buyer);die;
			if(count($check_user_buyer)==0)
			{

				$sql = sql_select("select b.buyer_id as buyer_id,b.brand_id from user_passwd b, electronic_approval_setup a where b.id = a.user_id and  a.page_id=$menu_id and a.sequence_no>$user_sequence_no and a.is_deleted=0 and a.bypass=2 $buyer_id_cond $buyer_id_cond2 group by b.buyer_id,b.brand_id");
				foreach ($sql as $key => $buyerID) {
					$credentialUserBuyersArr[] = $buyerID[csf('buyer_id')];
					$credentialUserBrandArr[] = $buyerID[csf('brand_id')];
				}

				$sql = sql_select("select (b.buyer_id) as buyer_id,b.brand_id from electronic_approval_setup b where  b.page_id=$menu_id and b.sequence_no>$user_sequence_no and b.is_deleted=0 and b.bypass=2 $buyer_id_cond2  group by b.buyer_id,b.brand_id");
				foreach ($sql as $key => $buyerID) {
					$credentialUserBuyersArr[] = $buyerID[csf('buyer_id')];
					$credentialUserBrandArr[] = $buyerID[csf('brand_id')];
				}

				$credentialUserBuyersArr = implode(',',$credentialUserBuyersArr);
				$credentialUserBuyersArr = explode(',',$credentialUserBuyersArr);
				$credentialUserBuyersArr = array_unique($credentialUserBuyersArr);
				
				$credentialUserBrandArr=array_unique(explode(',',implode(',',$credentialUserBrandArr)));
				
			}
			//print_r($credentialUserBuyersArr);die;
		}
		// if($is_not_last_user!="") $partial_approval=3; else $partial_approval=1;

		$response=$booking_ids;
		$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date, inserted_by, insert_date";
		$id=return_next_id( "id","approval_history", 1 ) ;

		$max_approved_no_arr = return_library_array("select mst_id, max(approved_no) as approved_no from approval_history where mst_id in($booking_ids) and entry_form=45 group by mst_id","mst_id","approved_no");

		$approved_status_arr = return_library_array("select id, approved from qc_mst where id in($booking_ids)","id","approved");
		//echo "21**";
	//	print_r($approved_status_arr);die;
		$approved_no_array=array();
		$booking_ids_all=explode(",",$booking_ids);
		$booking_nos_all=explode(",",$booking_nos);
		$confirm_ids_all=explode(",",$confirm_ids);
		$book_nos='';
		//print_r($credentialUserBuyersArr);die;
		for($i=0;$i<count($booking_nos_all);$i++)
		{
			$val=$booking_nos_all[$i];
			$booking_id=$booking_ids_all[$i];
			$confirm_id=$confirm_ids_all[$i];

			$approved_no=$max_approved_no_arr[$booking_id];
			$approved_status=$approved_status_arr[$booking_id];
			$buyer_id=$buyer_arr[$booking_id];
			$brand_id=$brand_arr[$booking_id];

			if($approved_status==0 || $approved_status==2)
			{
				$approved_no=$approved_no+1;
				$approved_no_array[$val]=$approved_no;
				if($book_nos=="") $book_nos=$val; else $book_nos.=",".$val;
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
			$confirm_id_arr[]=$confirm_id;
			$data_array_confirm_update[$confirm_id]=explode("*",($partial_approval));

			if($partial_approval==1)
			{
				$full_approve_booking_id_arr[]=$booking_id;
				$full_approve_confirm_id_arr[]=$confirm_id;
				$data_array_full_approve_booking_update[$booking_id]=explode("*",($user_id_approval."*'".$pc_date_time."'"));
			}

			if($data_array!="") $data_array.=",";
			$data_array.="(".$id.",45,".$booking_id.",".$approved_no.",'".$user_sequence_no."',1,".$user_id_approval.",'".$pc_date_time."',".$user_id.",'".$pc_date_time."')";
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
					$approved_string.=" WHEN $key THEN '".$value."'";
				}
			}

			$approved_string_mst="CASE qc_no ".$approved_string." END";
			$approved_string_dtls="CASE mst_id ".$approved_string." END";
			$approved_string_confirm="CASE cost_sheet_id ".$approved_string." END";

			$confirm_mst_sql="insert into qc_confirm_mst_history(id, approved_no, confirm_mst_id,cost_sheet_id, lib_item_id, confirm_style, confirm_order_qty, confirm_fob, deal_merchant, ship_date, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, job_id, approved,  approved_by, approved_date) 
				select '', $approved_string_confirm, id, cost_sheet_id, lib_item_id, confirm_style, confirm_order_qty, confirm_fob,  deal_merchant, ship_date, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, job_id, approved, approved_by, approved_date from qc_confirm_mst where cost_sheet_id in ($book_nos)";

			$confirm_dtls_sql="insert into qc_confirm_dtls_history( id, approved_no, confirm_dtls_id, mst_id, cost_sheet_id, item_id, fab_cons_kg, fab_cons_yds, fab_amount, sp_oparation_amount, acc_amount, fright_amount, lab_amount, misce_amount, other_amount, comm_amount, fob_amount, cm_amount,  rmg_ratio, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, fab_cons_mtr, cppm_amount, smv_amount) 
			select '', $approved_string_confirm, id, mst_id, cost_sheet_id, item_id, fab_cons_kg, fab_cons_yds, fab_amount, sp_oparation_amount, acc_amount,  fright_amount, lab_amount, misce_amount, other_amount, comm_amount, fob_amount, cm_amount, rmg_ratio, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, fab_cons_mtr, cppm_amount, smv_amount from qc_confirm_dtls where cost_sheet_id in ($book_nos)";

			$sql_insert_cons_rate="insert into  qc_cons_rate_dtls_histroy( id,approved_no, cons_rate_dtls_id, mst_id, item_id, type, particular_type_id, formula, consumption, unit, is_calculation, rate, rate_data, value, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, ex_percent)
			select '',$approved_string_dtls, id, mst_id, item_id, type, particular_type_id, formula, consumption, unit, is_calculation, rate, rate_data, value, inserted_by, insert_date, updated_by,update_date, status_active, is_deleted, ex_percent from qc_cons_rate_dtls where mst_id in ($book_nos) ";
			//echo $sql_insert;die;

			$sql_fabric_dtls="insert into  qc_fabric_dtls_history(id,approved_no, fabric_dtls_id, mst_id,  item_id, body_part, des,value, alw, inserted_by,insert_date, updated_by, update_date, status_active, is_deleted, uniq_id)
			select '', $approved_string_dtls, id, mst_id, item_id, body_part, des, value, alw, inserted_by, insert_date, updated_by, update_date, status_active,  is_deleted, uniq_id from qc_fabric_dtls where mst_id in ($book_nos)";
			//echo $sql_fabric_dtls;die;
			//--------------------------------------qc_item_cost_summary_his---------------------------------------------------------------------------

			$sql_item_cost_dtls="insert into qc_item_cost_summary_his(id, approved_no, item_sum_id, mst_id, item_id, fabric_cost, sp_operation_cost,accessories_cost, smv, efficiency, cm_cost, frieght_cost, lab_test_cost, miscellaneous_cost, other_cost, commission_cost,fob_pcs, inserted_by, insert_date, updated_by, update_date, status_active,is_deleted, rmg_ratio, cpm)
				select '', $approved_string_dtls, id, mst_id, item_id, fabric_cost, sp_operation_cost, accessories_cost, smv, efficiency, cm_cost,frieght_cost, lab_test_cost, miscellaneous_cost, other_cost, commission_cost, fob_pcs, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, rmg_ratio, cpm from qc_item_cost_summary where mst_id in ($book_nos)";
			//echo $sql_item_cost_dtls;die;

			//--------------------------------------qc_meeting_mst_history---------------------------------------------------------------------------
			$sql_meeting_mst="insert into qc_meeting_mst_history(id, approved_no, metting_mst_id, mst_id, meeting_no, buyer_agent_id, location_id, meeting_date, meeting_time, remarks, inserted_by, insert_date, updated_by,  update_date, status_active, is_deleted)
				select '', $approved_string_dtls, id, mst_id, meeting_no, buyer_agent_id, location_id, meeting_date, meeting_time, remarks, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted from qc_meeting_mst where  mst_id in ($book_nos)";
				//echo $sql_meeting_mst;die;

			//----------------------------------qc_mst_history----------------------------------------
			$sql_qc_mst="insert into qc_mst_history( id, approved_no, qc_mst_id, cost_sheet_id, cost_sheet_no, temp_id, style_des, buyer_id, cons_basis, season_id, style_ref, department_id, delivery_date, exchange_rate, offer_qty, quoted_price, tgt_price, stage_id, costing_date, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, lib_item_id, pre_cost_sheet_id, revise_no, option_id, buyer_remarks, option_remarks, meeting_no, qc_no, uom, approved, approved_by, approved_date, from_client)
				select '', $approved_string_mst, id, cost_sheet_id, cost_sheet_no, temp_id, style_des, buyer_id, cons_basis, season_id, style_ref, department_id, delivery_date, exchange_rate, offer_qty, quoted_price, tgt_price, stage_id, costing_date, inserted_by, insert_date, updated_by, update_date,  status_active, is_deleted, lib_item_id, pre_cost_sheet_id, revise_no, option_id, buyer_remarks, option_remarks, meeting_no, qc_no, uom, approved, approved_by, approved_date, from_client from qc_mst where  qc_no in ($book_nos)";
				//echo $sql_qc_mst;die;

			//-------------------------------------qc_tot_cost_summary_history-------------------------------------------
			$sql_tot_cost="insert into qc_tot_cost_summary_history( id, approved_no, tot_sum_id, mst_id, buyer_agent_id, location_id, no_of_pack, is_confirm, is_cm_calculative, mis_lumsum_cost, commision_per, tot_fab_cost, tot_sp_operation_cost, tot_accessories_cost, tot_cm_cost, tot_fright_cost, tot_lab_test_cost, tot_miscellaneous_cost, tot_other_cost, tot_commission_cost, tot_cost, tot_fob_cost, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted,  tot_rmg_ratio)
			select '', $approved_string_dtls, id, mst_id, buyer_agent_id,location_id, no_of_pack, is_confirm, is_cm_calculative, mis_lumsum_cost, commision_per, tot_fab_cost, tot_sp_operation_cost, tot_accessories_cost, tot_cm_cost, tot_fright_cost, tot_lab_test_cost, tot_miscellaneous_cost, tot_other_cost, tot_commission_cost,  tot_cost, tot_fob_cost, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, tot_rmg_ratio from qc_tot_cost_summary where mst_id in ($book_nos)";
			//	echo $sql_tot_cost;die;

			if(count($confirm_mst_sql)>0)
			{
				$rID12=execute_query($confirm_mst_sql,1);
				if($flag==1)
				{
					if($rID12) $flag=1; else $flag=130;
				}
			}

			if(count($confirm_dtls_sql)>0)
			{
				$rID13=execute_query($confirm_dtls_sql,1);
				if($flag==1)
				{
					if($rID13) $flag=1; else $flag=120;
				}
			}

			if(count($sql_insert_cons_rate)>0)
			{
				$rID13=execute_query($sql_insert_cons_rate,1);
				if($flag==1)
				{
					if($rID13) $flag=1; else $flag=110;
				}
			}

			if(count($sql_fabric_dtls)>0)
			{
				$rID3=execute_query($sql_fabric_dtls,0);
				if($flag==1)
				{
					if($rID3) $flag=1; else $flag=100;
				}
			}
			//echo '895='.$flag; die;
			if(count($sql_item_cost_dtls)>0)
			{
				$rID4=execute_query($sql_item_cost_dtls,1);
				if($flag==1)
				{
					if($rID4) $flag=1; else $flag=90;
				}
			}

			if(count($sql_meeting_mst)>0)
			{
				$rID5=execute_query($sql_meeting_mst,1);
				if($flag==1)
				{
					if($rID5) $flag=1; else $flag=80;
				}
			}

			if(count($sql_qc_mst)>0)
			{
				$rID6=execute_query($sql_qc_mst,1);
				if($flag==1)
				{
					if($rID6) $flag=1; else $flag=70;
				}
			}

			if(count($sql_tot_cost)>0)
			{
				//echo "21**".$sql_tot_cost;die;
				$rID7=execute_query($sql_tot_cost,1);
				if($flag==1)
				{
					if($rID7) $flag=1; else $flag=60;
				}
			}
		}

		$rID8=$rID9=1;
		if(count($full_approve_booking_id_arr)>0)
		{
			$field_array_full_approved_booking_update = "approved_by*approved_date";
			$rID8=execute_query(bulk_update_sql_statement( "qc_mst", "id", $field_array_full_approved_booking_update, $data_array_full_approve_booking_update, $full_approve_booking_id_arr));
			if($flag==1)
			{
				if($rID8) $flag=1; else $flag=50;
			}

			$rID9=execute_query(bulk_update_sql_statement( "qc_confirm_mst", "id", $field_array_full_approved_booking_update, $data_array_full_approve_booking_update, $full_approve_confirm_id_arr));
			if($flag==1)
			{
				if($rID9) $flag=1; else $flag=40;
			}
			//sql_multirow_update("qc_confirm_mst","approved_by*approved_date",$updateData,"id",$booking_ids,1);
		}

		$field_array_booking_update = "approved";
		$rID=execute_query(bulk_update_sql_statement( "qc_mst", "id", $field_array_booking_update, $data_array_booking_update, $booking_id_arr));

		if($flag==1)
		{
			if($rID) $flag=1; else $flag=30;
		}

		$rIDConfirm=execute_query(bulk_update_sql_statement( "qc_confirm_mst", "id", $field_array_booking_update, $data_array_confirm_update, $confirm_id_arr));

		if($flag==1)
		{
			if($rIDConfirm) $flag=1; else $flag=20;
		}
		//echo $flag; die;

		$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=45 and mst_id in ($booking_ids)";
		$rIDapp=execute_query($query,1);
		if($flag==1)
		{
			if($rIDapp) $flag=1; else $flag=10;
		}

		/*if($approval_ids!="")
		{
			$rIDapp=sql_multirow_update("approval_history","current_approval_status",0,"id",$approval_ids,0);
			if($flag==1)
			{
				if($rIDapp) $flag=1; else $flag=0;
			}
		}*/
//echo "21** insert into approval_history($field_array)values".$data_array;die;
		$rID2=sql_insert("approval_history",$field_array,$data_array,0);
		if($flag==1)
		{
			if($rID2) $flag=1; else $flag=0;
		}

		//echo "21**".$flag;die;
		if($flag==1) $msg='19'; else $msg='21';
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
			$confirm_id=$data[2];

			if($booking_ids=='') $booking_ids=$booking_id; else $booking_ids.=",".$booking_id;
			if($app_ids=='') $app_ids=$app_id; else $app_ids.=",".$app_id;
			if($confirm_ids=='') $confirm_ids=$confirm_id; else $confirm_ids.=",".$confirm_id;
		}


		$rID=sql_multirow_update("qc_mst","approved",'2',"id",$booking_ids,0);
		if($rID) $flag=1; else $flag=0;
		$rID=sql_multirow_update("qc_confirm_mst","approved*ready_to_approve",'2*2',"id",$confirm_ids,0);
		if($rID) $flag=1; else $flag=0;

		//$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=45 and current_approval_status=1 and mst_id in ($confirm_ids)";
		$query="UPDATE approval_history SET current_approval_status=0, un_approved_by='".$user_id_approval."', un_approved_date='".$pc_date_time."', updated_by='".$user_id."', update_date='".$pc_date_time."' WHERE entry_form=45 and current_approval_status=1 and mst_id in ($confirm_ids)";
		$rID2=execute_query($query,1);
		if($flag==1)
		{
			if($rID2) $flag=1; else $flag=0;
		}

		/*$data=$user_id_approval."*'".$pc_date_time."'*".$user_id."*'".$pc_date_time."'";
		$rID3=sql_multirow_update("approval_history","un_approved_by*un_approved_date*updated_by*update_date",$data,"id",$approval_ids,1);
		if($flag==1)
		{
			if($rID3) $flag=1; else $flag=0;
		}*/

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

if($action=="confirmStyle_popup")
{
	echo load_html_head_contents("Confirm Style PopUp","../../", 1, 1, '','1','');
	extract($_REQUEST);
	$permission=$_SESSION['page_permission'];
	//echo $data;
	$exdata=explode('__',$data);
	$qc_no=$exdata[0];
	$updateid=$exdata[1];
	$user_id=$_SESSION['logic_erp']['user_id'];
	$user_level=$_SESSION['logic_erp']["user_level"];
	$sql_data=sql_select("Select cost_sheet_no, buyer_id, season_id, department_id, temp_id, lib_item_id, style_ref, offer_qty, revise_no, option_id, delivery_date, uom from qc_mst where qc_no='$qc_no' ");
	
	$uom=$sql_data[0][csf('uom')];
	
	$lib_temp_arr=return_library_array("select id, item_name from lib_qc_template","id","item_name");
	if($db_type==0) $concat_cond="group_concat(lib_item_id)";
	else if($db_type==2) $concat_cond="listagg(cast(lib_item_id as varchar2(4000)),',') within group (order by lib_item_id)";
	else $concat_cond="";
	$sql_tmp="select temp_id, $concat_cond as lib_item_id from qc_template where status_active=1 and is_deleted=0 group by temp_id order by temp_id ASC";
	$sql_tmp_res=sql_select($sql_tmp);
	//print_r($sql_tmp_res);die;
	$template_name_arr=array();
	foreach($sql_tmp_res as $row)
	{
		$lib_temp_id='';
		
		$ex_temp_id=explode(',',$sql_data[0][csf('lib_item_id')]);
		foreach($ex_temp_id as $lib_id)
		{
			if($lib_temp_id=="") $lib_temp_id=$lib_temp_arr[$lib_id]; else $lib_temp_id.=','.$lib_temp_arr[$lib_id];
		}
		$template_name_arr[$sql_data[0][csf('temp_id')]]=$lib_temp_id;
	}
	$gmt_type_arr=array(1=>'Pcs',2=>'Set');
	$gmt_itm_count=count(explode(',',$template_name_arr[$sql_data[0][csf('temp_id')]]));
	$selected_gmt_type=0;
	if($gmt_itm_count>1) $selected_gmt_type=2; else $selected_gmt_type=1;
	
	$sql_summ=sql_select("select no_of_pack, tot_fob_cost from qc_tot_cost_summary where mst_id='$qc_no' and status_active=1 and is_deleted=0");
	//$sql_cons_rate="select id, item_id, type, particular_type_id, consumption, unit, is_calculation, rate, rate_data, value from qc_cons_rate_dtls where mst_id='$data[0]' and status_active=1 and is_deleted=0 order by id asc";
	$sql_cons=sql_select("select item_id, sum(CASE WHEN particular_type_id in (1,20,4,6,7,998) THEN consumption ELSE 0 END) as qty_kg, sum(CASE WHEN particular_type_id=999 THEN consumption ELSE 0 END) as qty_yds from qc_cons_rate_dtls where mst_id='$qc_no' and type=1 group by item_id");//type ='1' and
	$item_wise_cons_arr=array();
	foreach($sql_cons as $cRow)
	{
		if($uom==12)
		{
			$item_wise_cons_arr[$cRow[csf("item_id")]]['qty_kg']=$cRow[csf("qty_kg")];
			$item_wise_cons_arr[$cRow[csf("item_id")]]['qty_mtr']=0;
			$item_wise_cons_arr[$cRow[csf("item_id")]]['qty_yds']=$cRow[csf("qty_yds")];
		}
		else if($uom==23)
		{
			$item_wise_cons_arr[$cRow[csf("item_id")]]['qty_kg']=0;
			$item_wise_cons_arr[$cRow[csf("item_id")]]['qty_mtr']=$cRow[csf("qty_kg")];
			$item_wise_cons_arr[$cRow[csf("item_id")]]['qty_yds']=$cRow[csf("qty_yds")];
		}
		else if($uom==27)
		{
			$item_wise_cons_arr[$cRow[csf("item_id")]]['qty_kg']=0;
			$item_wise_cons_arr[$cRow[csf("item_id")]]['qty_mtr']=0;
			$item_wise_cons_arr[$cRow[csf("item_id")]]['qty_yds']=$cRow[csf("qty_kg")]+$cRow[csf("qty_yds")];
		}
	}
	//$sql_result_summ=sql_select($sql_summ);
	//print_r($item_wise_cons_arr);
	

	$team_dtls_sql=sql_select("select a.user_tag_id from lib_marketing_team a, lib_mkt_team_member_info b where a.id=b.team_id and b.user_tag_id='$user_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.user_tag_id");
	if(count($team_dtls_sql)==1) $team_dtls_arr[$user_id]=$team_dtls_sql[0][csf('user_tag_id')];
	else $team_dtls_arr[$user_id]='';
	//print_r($team_dtls_arr);
	$disable="";
	if($user_level==2 || $team_dtls_arr[$user_id]!="") $disable=""; else $disable="disabled";
	
	$isteam_leader=return_field_value("user_tag_id","lib_marketing_team","user_tag_id='$user_id' and is_deleted=0 and status_active=1","user_tag_id");
	//echo $user_level.'-'.$isteam_leader;
	if($user_level==2 || $isteam_leader!='') $admin_or_leader="";  else $admin_or_leader="none";
	
	?>
    <script>
		var permission='<? echo $permission; ?>'; 
		
		
		function js_set_value( )
		{
			parent.emailwindow.hide();
		}
		
		function fnc_openJobPopup()
		{
			var cbo_approved_status=$('#cbo_approved_status').val();
			if(cbo_approved_status==1)
			{
				alert("This Option (QC) is Approved.");
				release_freezing();
				return;
			}
			var data=document.getElementById('cbo_buyer_id').value;
			page_link='quick_costing_controller.php?action=style_tag_popup&data='+data;
			emailwindow=dhtmlmodal.open('EmailBox','iframe',page_link,'Job and Style Popup', 'width=780px, height=380px, center=1, resize=0, scrolling=0','../../');
			emailwindow.onclose=function()
			{
				var theemail=this.contentDoc.getElementById("hidd_job_data");  
				//alert (theemail.value);return;
				var job_val=theemail.value.split("_");
				if (theemail.value!="")
				{
					$("#txt_job_id").val(job_val[0]);
					$("#txt_job_style").val(job_val[1]);
					$("#txt_style_job").val(job_val[2]);
					fnc_bom_data_load();
				}
			}
		}
		
		function fnc_bom_data_load()
		{
			var job_no=$("#txt_job_style").val();
			if(job_no!="")
			{
				var str_data=return_global_ajax_value( job_no, 'budgete_cost_validate', '', 'quick_costing_controller');
				
				var spdata=str_data.split("##");
				var fab_cons_kg=spdata[0]; var fab_cons_mtr=spdata[1]; var fab_cons_yds=spdata[2]; var fab_amount=spdata[3]; var sp_oparation_amount=spdata[4]; var acc_amount=spdata[5]; var fright_amount=spdata[6]; var lab_amount=spdata[7]; var misce_amount=spdata[8]; var other_amount=spdata[9]; var comm_amount=spdata[10]; var fob_amount=spdata[11]; var cm_amount=spdata[12]; var rmg_ratio=spdata[13];
				
				$("#txtFabConkg_bom").val(fab_cons_kg);
				$("#txtFabConmtr_bom").val(fab_cons_mtr);
				$("#txtFabConyds_bom").val(fab_cons_yds);
				$("#txtFabCst_bom").val(fab_amount);
				$("#txtSpOpa_bom").val(sp_oparation_amount);
				$("#txtAcc_bom").val(acc_amount);
				$("#txtFrightCst_bom").val(fright_amount);
				$("#txtLabCst_bom").val(lab_amount);
				$("#txtMiscCst_bom").val(misce_amount);
				$("#txtOtherCst_bom").val(other_amount);
				$("#txtCommCst_bom").val(comm_amount);
				$("#txtFobDzn_bom").val(fob_amount);
				$("#txtCmCst_bom").val(cm_amount);
				$("#txtPack_bom").val(rmg_ratio);
			}
		}
		
		function fnc_total_calculate()
		{
			var temp_id=$('#txtItem_id').val();
			var split_tmep_id=temp_id.split(',');
			var ab=0;
			var qc_fab_kg=0; var qc_fab_mtr=0; var qc_fab_yds=0; var qc_fab_amt=0; var qc_sp_amt=0; var qc_acc_amt=0; var qc_fri_amt=0; var qc_lab_amt=0; var qc_misce_amt=0; var qc_other_amt=0; var qc_comm_amt=0; var qc_fob_amt=0; var qc_cpm_amt=0; var qc_smv_amt=0; var qc_cm_amt=0; var qc_rmg_amt=0;
			for(j=1; j<=split_tmep_id.length; j++)
			{
				var item_tot_amount=0; var item_tot_cm=0;
				var itm_id=trim(split_tmep_id[ab]);
				
				qc_fab_kg+=$("#txtFabConkg_"+itm_id).val()*1;
				qc_fab_mtr+=$("#txtFabConmtr_"+itm_id).val()*1;
				qc_fab_yds+=$("#txtFabConyds_"+itm_id).val()*1;
				qc_fab_amt+=$("#txtFabCst_"+itm_id).val()*1;
				qc_sp_amt+=$("#txtSpOpa_"+itm_id).val()*1;
				qc_acc_amt+=$("#txtAcc_"+itm_id).val()*1;
				qc_fri_amt+=$("#txtFrightCst_"+itm_id).val()*1;
				qc_lab_amt+=$("#txtLabCst_"+itm_id).val()*1;
				qc_misce_amt+=$("#txtMiscCst_"+itm_id).val()*1;
				qc_other_amt+=$("#txtOtherCst_"+itm_id).val()*1;
				qc_comm_amt+=$("#txtCommCst_"+itm_id).val()*1;
				qc_fob_amt+=$("#txtFobDzn_"+itm_id).val()*1;
				
				qc_cpm_amt+=$("#txtCpm_"+itm_id).val()*1;
				qc_smv_amt+=$("#txtSmv_"+itm_id).val()*1;
				
				qc_cm_amt+=$("#txtCmCst_"+itm_id).val()*1;
				qc_rmg_amt+=$("#txtPack_"+itm_id).val()*1;
				
				item_tot_amount=($("#txtFabCst_"+itm_id).val()*1)+($("#txtSpOpa_"+itm_id).val()*1)+($("#txtAcc_"+itm_id).val()*1)+($("#txtFrightCst_"+itm_id).val()*1)+($("#txtLabCst_"+itm_id).val()*1)+($("#txtMiscCst_"+itm_id).val()*1)+($("#txtOtherCst_"+itm_id).val()*1)+($("#txtCommCst_"+itm_id).val()*1);
				
				item_tot_cm=($("#txtFobDzn_"+itm_id).val()*1)-item_tot_amount;
				
				$("#txtCmCst_"+itm_id).val( number_format(item_tot_cm,2,'.',''))
				
				ab++;
			}
			
			$("#txtFabConkg_qc").val( number_format(qc_fab_kg,2,'.','') );
			$("#txtFabConmtr_qc").val( number_format(qc_fab_mtr,2,'.','') );
			$("#txtFabConyds_qc").val( number_format(qc_fab_yds,2,'.','') );
			$("#txtFabCst_qc").val( number_format(qc_fab_amt,2,'.','') );
			$("#txtSpOpa_qc").val( number_format(qc_sp_amt,2,'.','') );
			$("#txtAcc_qc").val( number_format(qc_acc_amt,2,'.','') );
			$("#txtFrightCst_qc").val( number_format(qc_fri_amt,2,'.','') );
			$("#txtLabCst_qc").val( number_format(qc_lab_amt,2,'.','') );
			$("#txtMiscCst_qc").val( number_format(qc_misce_amt,2,'.','') );
			$("#txtOtherCst_qc").val( number_format(qc_other_amt,2,'.','') );
			$("#txtCommCst_qc").val( number_format(qc_comm_amt,2,'.','') );
			$("#txtFobDzn_qc").val( number_format(qc_fob_amt,2,'.','') );
			
			$("#txtCpm_qc").val( number_format(qc_cpm_amt,4,'.','') );
			$("#txtSmv_qc").val( number_format(qc_smv_amt,2,'.','') );
			
			$("#txtPack_qc").val( number_format(qc_rmg_amt,2,'.','') );
			
			var total_amount=qc_fab_amt+qc_sp_amt+qc_acc_amt+qc_fri_amt+qc_lab_amt+qc_misce_amt+qc_other_amt+qc_comm_amt;
			var cal_cm=qc_fob_amt-total_amount;
			$("#txtCmCst_qc").val( number_format(cal_cm,2,'.','') );
		}
		
		function fnc_select()
		{
			$(document).ready(function() {
				$("input:text").focus(function() { $(this).select(); } );
			});
		}
		
		function fnc_confirm()
		{
			var job_no=$('#txt_job_style').val();
			
			if(job_no=="")
			{
				alert("Please Add Job no with this option.");
				return;
			}
			else
			{
				fnc_confirm_entry(3);
			}
		}
		
		function fnc_cppm_cal(item_id)
		{
			var txtSmv=$("#txtSmv_"+item_id).val()*1;
			var txtCm=$("#txtCmCst_"+item_id).val()*1;
			
			var cppm=( txtCm/txtSmv);
			var cppm_nf=number_format((cppm/12),4,'.','');
			if(cppm_nf=="nan") cppm_nf=0;
			$("#txtCpm_"+item_id).val( cppm_nf );
			
			fnc_total_calculate();
		}
		
	</script>
	</head>
	<body>
    <div id="confirm_style_details" align="center">  
    <div style="display:none"><? echo load_freeze_divs ("../../../",$permission);  ?></div>       
        <form name="confirmStyle_1" id="confirmStyle_1" autocomplete="off">
        	<table width="850">
                <tr>
                    <td width="90"><strong>Buyer</strong><input style="width:40px;" type="hidden" class="text_boxes" name="txt_costSheet_id" id="txt_costSheet_id" value="<? echo $qc_no; ?>" /><input style="width:40px;" type="hidden" class="text_boxes" name="txtConfirm_id" id="txtConfirm_id" value="" /><input style="width:40px;" type="hidden" class="text_boxes" name="txtItem_id" id="txtItem_id" value="<? echo $sql_data[0][csf('lib_item_id')]; ?>" /></td>
                    <td width="120"><? echo create_drop_down( "cbo_buyer_id", 120, "select buy.id, buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0 $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buyer_name","id,buyer_name", 1, "-Select Buyer-", $sql_data[0][csf('buyer_id')], "load_drop_down( 'quick_costing_controller', this.value, 'load_drop_down_season_conf', 'season_conf_td'); load_drop_down( 'quick_costing_controller',this.value, 'load_drop_down_sub_depConf', 'subConf_td' );",1 ); ?></td>
                    <td width="90">&nbsp;&nbsp;<strong>Season</strong></td>
                    <td width="100" id="season_conf_td"><? echo create_drop_down( "cbo_season_id", 100, "select id, season_name from lib_buyer_season where buyer_id='".$sql_data[0][csf('buyer_id')]."' and status_active =1 and is_deleted=0 order by season_name ASC","id,season_name", 1, "-Select Season-",$sql_data[0][csf('season_id')], "",1 ); ?></td>
                    <td width="90">&nbsp;&nbsp;<strong>Department</strong></td>
                    <td width="100" id="subConf_td"><? echo create_drop_down( "cbo_subDept_id", 100, "select id, sub_department_name from lib_pro_sub_deparatment where buyer_id='".$sql_data[0][csf('buyer_id')]."' and status_active=1 and is_deleted=0 order by sub_department_name","id,sub_department_name", 1, "-- Select Dept--",$sql_data[0][csf('department_id')], "",1 ); ?></td>
                    <td colspan="3" align="center">&nbsp;</td>
                </tr>
                <tr>
                    <td><strong>Style Type</strong></td>
                    <td><? echo create_drop_down( "cbo_style_type", 120, $template_name_arr,"", 1, "-Select-", $selected, "",1 ); ?> </td>
                    <td>&nbsp;&nbsp;<strong>Gmts Type</strong></td>
                    <td><? echo create_drop_down( "cbo_gmts_type", 100, $gmt_type_arr,'', 1, "-Gmts Type-",$selected_gmt_type, "" ,1); ?></td>
                    
                    <td>&nbsp;&nbsp;<strong>Revise No</strong></td>
                    <td><? echo create_drop_down( "cbo_revise", 100, "select revise_no from qc_mst where cost_sheet_no='".$sql_data[0][csf('cost_sheet_no')]."' and status_active=1 and is_deleted=0 order by cost_sheet_no Desc","revise_no,revise_no", 0, "-Select-", $sql_data[0][csf('revise_no')], "",1 ); ?> </td>
                    <td width="90">&nbsp;&nbsp;<strong>Option</strong></td>
                    <td width="100"><? echo create_drop_down( "cbo_option", 100, "select option_id from qc_mst where cost_sheet_no='".$sql_data[0][csf('cost_sheet_no')]."' and status_active=1 and is_deleted=0 order by cost_sheet_no Desc","option_id,option_id", 0, "-Select-",$sql_data[0][csf('option_id')], "" ,1); ?></td>
                </tr>
                <tr>
                    <td><strong>Estimate Style</strong></td>
                    <td><input style="width:110px;" type="text" class="text_boxes" name="txt_style_ref" id="txt_style_ref" value="<? echo $sql_data[0][csf('style_ref')]; ?>" disabled /></td>
                    <td>&nbsp;&nbsp;<strong>Cofirm Style</strong></td>
                    <td><input style="width:90px;" type="text" class="text_boxes" name="txt_confirm_style" id="txt_confirm_style" value="<? echo $sql_data[0][csf('style_ref')]; ?>" <? echo $disable; ?> /></td>
                    <td>&nbsp;&nbsp;<strong>Order Qty.</strong></td>
                    <td><input style="width:90px;" type="text" class="text_boxes_numeric" name="txt_order_qty" id="txt_order_qty" value="<? echo $sql_data[0][csf('offer_qty')]; ?>" <? echo $disable; ?> /></td>
                    <td>&nbsp;&nbsp;<strong>Cofirm FOB</strong></td>
                    <td><input style="width:90px;" type="text" class="text_boxes_numeric" name="txt_confirm_fob" id="txt_confirm_fob" value="<? echo $sql_summ[0][csf('tot_fob_cost')]; ?>" <? echo $disable; ?> /></td>
                </tr>
                <tr>
                	<td><strong>Ship Date</strong></td>
                    <td><input style="width:110px;" type="text" class="datepicker" name="txt_ship_date" id="txt_ship_date" value="<? echo change_date_format($sql_data[0][csf('delivery_date')]); ?>" readonly <? echo $disable; ?>/></td>
                    <td>&nbsp;&nbsp;<strong>Job No</strong></td>
                    <td><input style="width:90px;" type="text" class="text_boxes" name="txt_job_style" id="txt_job_style" placeholder="Browse Job" onDblClick="fnc_openJobPopup();" readonly /><input style="width:40px;" type="hidden" class="text_boxes" name="txt_job_id" id="txt_job_id" /></td>
                    <td>&nbsp;&nbsp;<strong>Master Style</strong></td>
                    <td><input style="width:90px;" type="text" class="text_boxes" name="txt_style_job" id="txt_style_job" disabled /></td>
                    <td>&nbsp;&nbsp;<strong>Approved</strong></td>
                	<td><? echo create_drop_down( "cbo_approved_status", 100, $yes_no,"", 0, "", 2, "",1,"" ); ?></td> 
                    <td>&nbsp;</td>
                </tr>
            </table>
            <table width="400" cellspacing="0" class="" border="0">
                <tr>
                    <td align="center" width="100%" class="button_container">
						<input type="button" class="formbutton" value="Close" style="width:80px" onClick="js_set_value();"/>
                    </td> 
                </tr>
            </table>
            <div id="confirm_data_div">
            <table width="925" cellspacing="0" border="1" rules="all" class="rpt_table">
        	<thead>
            	<th width="80">Item</th>
                <th width="50">Fab. Cons. Kg</th>
                <th width="50">Fab. Cons. Mtr</th>
                <th width="50">Fab. Cons. Yds</th>
                <th width="50">Fab. Amount</th>
                <th width="50">Special Opera.</th>
                <th width="50">Access.</th>
                <th width="50">Frieght Cost</th>
                <th width="50">Lab - Test</th>
                <th width="50">Misce.</th>
                <th width="50">Other Cost</th>
                <th width="50">Commis.</th>
                <th width="50">FOB ($/DZN)</th>
                <th width="50" title="((CPM*100)/Efficiency)">CPPM</th>
                <th width="50">SMV</th>
                <th width="50">CM</th>
                <th>RMG Qty(Pcs)</th>
            </thead>
			<?
            $sql_item_summ="select item_id, fabric_cost, sp_operation_cost, accessories_cost, cpm, smv, efficiency, cm_cost, frieght_cost, lab_test_cost, miscellaneous_cost, other_cost, commission_cost, fob_pcs from qc_item_cost_summary where mst_id='$qc_no' and status_active=1 and is_deleted=0";
            $sql_result_item_summ=sql_select($sql_item_summ); $z=1;
            foreach($sql_result_item_summ as $rowItemSumm)
            {
				if ($z%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if ($z%2==0) $bgcolorN="#E9F50F"; else $bgcolorN="#D078F6";
				
				$cppm=0;
				if($rowItemSumm[csf("efficiency")]!=0 && $rowItemSumm[csf("cpm")]!=0) $cppm=(($rowItemSumm[csf("cpm")]*100)/$rowItemSumm[csf("efficiency")]);
				
				if($cppm=="nan") $cppm=0;
                ?>
                <tr id="trVal_<? echo $z; ?>" bgcolor="<? echo $bgcolor; ?>">
                    <td><? echo $lib_temp_arr[$rowItemSumm[csf("item_id")]]; ?></td>
                    <td align="right"><? echo number_format($item_wise_cons_arr[$rowItemSumm[csf("item_id")]]['qty_kg'],4); ?></td>
                    <td align="right"><? echo number_format($item_wise_cons_arr[$rowItemSumm[csf("item_id")]]['qty_mtr'],4); ?></td>
                    <td align="right"><? echo number_format($item_wise_cons_arr[$rowItemSumm[csf("item_id")]]['qty_yds'],4); ?></td>
                    <td align="right"><? echo number_format($rowItemSumm[csf("fabric_cost")],4); ?></td>
                    <td align="right"><? echo number_format($rowItemSumm[csf("sp_operation_cost")],4); ?></td>
                    <td align="right"><? echo number_format($rowItemSumm[csf("accessories_cost")],4); ?></td>
                    <td align="right"><? echo number_format($rowItemSumm[csf("frieght_cost")],4); ?></td>
                    <td align="right"><? echo number_format($rowItemSumm[csf("lab_test_cost")],4); ?></td>
                    <td align="right"><? echo number_format($rowItemSumm[csf("miscellaneous_cost")],4); ?></td>
                    <td align="right"><? echo number_format($rowItemSumm[csf("other_cost")],4); ?></td>
                    <td align="right"><? echo number_format($rowItemSumm[csf("commission_cost")],4); ?></td>
                    <td align="right"><? echo number_format($rowItemSumm[csf("fob_pcs")],4); ?></td>
                    
                    <td align="right" title="((CPM*100)/Efficiency)"><? if($cppm==0) echo "0"; else echo number_format($cppm,4); ?></td>
                    <td align="right"><? echo number_format($rowItemSumm[csf("smv")],4); ?></td>
                    
                    <td align="right"><? echo number_format($rowItemSumm[csf("cm_cost")],4); ?></td>
                    <td align="right">&nbsp;<? //echo number_format($rowItemSumm[csf("sp_operation_cost")],4); ?></td>
                </tr>
                <tr id="tr_<? echo $z; ?>" bgcolor="<? echo $bgcolorN; ?>">
                    <td>QC BOM Limit<input style="width:40px;" type="hidden" name="txtitemid_<? echo $rowItemSumm[csf("item_id")]; ?>" id="txtitemid_<? echo $rowItemSumm[csf("item_id")]; ?>" value="<? echo $rowItemSumm[csf("item_id")]; ?>" /><input style="width:40px;" type="hidden" name="txtdtlsupid_<? echo $rowItemSumm[csf("item_id")]; ?>" id="txtdtlsupid_<? echo $rowItemSumm[csf("item_id")]; ?>" value="" /></td>
                    <td><input style="width:38px;" type="text" class="text_boxes_numeric" name="txtFabConkg_<? echo $rowItemSumm[csf("item_id")]; ?>" id="txtFabConkg_<? echo $rowItemSumm[csf("item_id")]; ?>" value="<? echo number_format($item_wise_cons_arr[$rowItemSumm[csf("item_id")]]['qty_kg'],4); ?>" onChange="fnc_total_calculate();" <? echo $disable; ?> /></td>
                    <td><input style="width:38px;" type="text" class="text_boxes_numeric" name="txtFabConmtr_<? echo $rowItemSumm[csf("item_id")]; ?>" id="txtFabConmtr_<? echo $rowItemSumm[csf("item_id")]; ?>" value="<? echo number_format($item_wise_cons_arr[$rowItemSumm[csf("item_id")]]['qty_mtr'],4); ?>" onChange="fnc_total_calculate();" <? echo $disable; ?> /></td>
                    <td><input style="width:38px;" type="text" class="text_boxes_numeric" name="txtFabConyds_<? echo $rowItemSumm[csf("item_id")]; ?>" id="txtFabConyds_<? echo $rowItemSumm[csf("item_id")]; ?>" value="<? echo number_format($item_wise_cons_arr[$rowItemSumm[csf("item_id")]]['qty_yds'],4); ?>" onChange="fnc_total_calculate();" <? echo $disable; ?>/></td>
                    <td><input style="width:38px;" type="text" class="text_boxes_numeric" name="txtFabCst_<? echo $rowItemSumm[csf("item_id")]; ?>" id="txtFabCst_<? echo $rowItemSumm[csf("item_id")]; ?>" value="<? echo number_format($rowItemSumm[csf("fabric_cost")],4); ?>" onChange="fnc_total_calculate();" <? echo $disable; ?> /></td>
                    <td><input style="width:38px;" type="text" class="text_boxes_numeric" name="txtSpOpa_<? echo $rowItemSumm[csf("item_id")]; ?>" id="txtSpOpa_<? echo $rowItemSumm[csf("item_id")]; ?>" value="<? echo number_format($rowItemSumm[csf("sp_operation_cost")],4); ?>" onChange="fnc_total_calculate();" <? echo $disable; ?> /></td>
                    <td><input style="width:38px;" type="text" class="text_boxes_numeric" name="txtAcc_<? echo $rowItemSumm[csf("item_id")]; ?>" id="txtAcc_<? echo $rowItemSumm[csf("item_id")]; ?>" value="<? echo number_format($rowItemSumm[csf("accessories_cost")],4); ?>" onChange="fnc_total_calculate();" <? echo $disable; ?> /></td>
                    <td><input style="width:38px;" type="text" class="text_boxes_numeric" name="txtFrightCst_<? echo $rowItemSumm[csf("item_id")]; ?>" id="txtFrightCst_<? echo $rowItemSumm[csf("item_id")]; ?>" value="<? echo number_format($rowItemSumm[csf("frieght_cost")],4); ?>" onChange="fnc_total_calculate();" <? echo $disable; ?> /></td>
                    <td><input style="width:38px;" type="text" class="text_boxes_numeric" name="txtLabCst_<? echo $rowItemSumm[csf("item_id")]; ?>" id="txtLabCst_<? echo $rowItemSumm[csf("item_id")]; ?>" value="<? echo number_format($rowItemSumm[csf("lab_test_cost")],4); ?>" onChange="fnc_total_calculate();" <? echo $disable; ?> /></td>
                    <td><input style="width:38px;" type="text" class="text_boxes_numeric" name="txtMiscCst_<? echo $rowItemSumm[csf("item_id")]; ?>" id="txtMiscCst_<? echo $rowItemSumm[csf("item_id")]; ?>" value="<? echo number_format($rowItemSumm[csf("miscellaneous_cost")],4); ?>" onChange="fnc_total_calculate();" <? echo $disable; ?> /></td>
                    <td><input style="width:38px;" type="text" class="text_boxes_numeric" name="txtOtherCst_<? echo $rowItemSumm[csf("item_id")]; ?>" id="txtOtherCst_<? echo $rowItemSumm[csf("item_id")]; ?>" value="<? echo number_format($rowItemSumm[csf("other_cost")],4); ?>" onChange="fnc_total_calculate();" <? echo $disable; ?> /></td>
                    <td><input style="width:38px;" type="text" class="text_boxes_numeric" name="txtCommCst_<? echo $rowItemSumm[csf("item_id")]; ?>" id="txtCommCst_<? echo $rowItemSumm[csf("item_id")]; ?>" value="<? echo number_format($rowItemSumm[csf("commission_cost")],4); ?>" onChange="fnc_total_calculate();" <? echo $disable; ?> /></td>
                    <td><input style="width:38px;" type="text" class="text_boxes_numeric" name="txtFobDzn_<? echo $rowItemSumm[csf("item_id")]; ?>" id="txtFobDzn_<? echo $rowItemSumm[csf("item_id")]; ?>" value="<? echo number_format($rowItemSumm[csf("fob_pcs")],4); ?>" onChange="fnc_total_calculate();" <? echo $disable; ?> /></td>
                    
                    <td title="((CPM*100)/Efficiency)"><input style="width:38px;" type="text" class="text_boxes_numeric" name="txtCpm_<? echo $rowItemSumm[csf("item_id")]; ?>" id="txtCpm_<? echo $rowItemSumm[csf("item_id")]; ?>" value="<? if($cppm==0) echo "0"; else echo number_format($cppm,4); ?>" onChange="fnc_total_calculate();" disabled <? //echo $disable; ?> /></td>
                    <td><input style="width:38px;" type="text" class="text_boxes_numeric" name="txtSmv_<? echo $rowItemSumm[csf("item_id")]; ?>" id="txtSmv_<? echo $rowItemSumm[csf("item_id")]; ?>" value="<? echo number_format($rowItemSumm[csf("smv")],4); ?>" onChange="fnc_total_calculate();" onBlur="fnc_cppm_cal(<? echo $rowItemSumm[csf("item_id")]; ?>);" <? echo $disable; ?> /></td>
                    
                    <td><input style="width:38px;" type="text" class="text_boxes_numeric" name="txtCmCst_<? echo $rowItemSumm[csf("item_id")]; ?>" id="txtCmCst_<? echo $rowItemSumm[csf("item_id")]; ?>" value="<? echo number_format($rowItemSumm[csf("cm_cost")],4); ?>" onChange="fnc_total_calculate();" <? echo $disable; ?> /></td>
                    <td><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtPack_<? echo $rowItemSumm[csf("item_id")]; ?>" id="txtPack_<? echo $rowItemSumm[csf("item_id")]; ?>" value="" onChange="fnc_total_calculate();" <? echo $disable; ?> />&nbsp;</td>
                </tr>
                <?
				$z++;
            }
			$sql="select fab_cons_kg, fab_cons_yds, fab_amount, sp_oparation_amount, acc_amount, fright_amount, lab_amount, misce_amount, other_amount, comm_amount, fob_amount, cm_amount, rmg_ratio from qc_confirm_dtls where cost_sheet_id='$cost_sheet_id' and status_active=1 and is_deleted=0";
			$dataArr=sql_select($sql);
            ?>
        </table>
        
        <table width="925" cellspacing="0" border="1" rules="all" class="rpt_table">
        	<tr id="tr_qc" bgcolor="#CCFFCC">
                <td width="80"><font color="#0000FF">QC Limit Total</font></td>
                <td width="50"><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtFabConkg_qc" id="txtFabConkg_qc" value="" disabled /></td>
                <td width="50"><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtFabConmtr_qc" id="txtFabConmtr_qc" value="" disabled /></td>
                <td width="50"><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtFabConyds_qc" id="txtFabConyds_qc" value="" disabled /></td>
                <td width="50"><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtFabCst_qc" id="txtFabCst_qc" value="" disabled /></td>
                <td width="50"><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtSpOpa_qc" id="txtSpOpa_qc" value="" disabled /></td>
                <td width="50"><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtAcc_qc" id="txtAcc_qc" value="" disabled /></td>
                <td width="50"><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtFrightCst_qc" id="txtFrightCst_qc" value="" disabled /></td>
                <td width="50"><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtLabCst_qc" id="txtLabCst_qc" value="" disabled /></td>
                <td width="50"><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtMiscCst_qc" id="txtMiscCst_qc" value="" disabled /></td>
                <td width="50"><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtOtherCst_qc" id="txtOtherCst_qc" value="" disabled /></td>
                <td width="50"><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtCommCst_qc" id="txtCommCst_qc" value="" disabled /></td>
                <td width="50"><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtFobDzn_qc" id="txtFobDzn_qc" value="" disabled /></td>
                
                <td width="50"><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtCpm_qc" id="txtCpm_qc" value="" disabled /></td>
                <td width="50"><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtSmv_qc" id="txtSmv_qc" value="" disabled /></td>
                
                <td width="50"><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtCmCst_qc" id="txtCmCst_qc" value="" disabled /></td>
                <td><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtPack_qc" id="txtPack_qc" value="" disabled />&nbsp;</td>
            </tr>
        	<tr id="tr_bom" bgcolor="#CCCCCC">
                <td width="80"><font color="#FF0000">Current BOM</font></td>
                <td><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtFabConkg_bom" id="txtFabConkg_bom" value="" readonly /></td>
                <td><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtFabConmtr_bom" id="txtFabConmtr_bom" value="" readonly /></td>
                <td><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtFabConyds_bom" id="txtFabConyds_bom" value="" readonly /></td>
                <td><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtFabCst_bom" id="txtFabCst_bom" value="" readonly /></td>
                <td><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtSpOpa_bom" id="txtSpOpa_bom" value="" readonly /></td>
                <td><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtAcc_bom" id="txtAcc_bom" value="" readonly /></td>
                <td><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtFrightCst_bom" id="txtFrightCst_bom" value="" readonly /></td>
                <td><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtLabCst_bom" id="txtLabCst_bom" value="" readonly /></td>
                <td><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtMiscCst_bom" id="txtMiscCst_bom" value="" readonly /></td>
                <td><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtOtherCst_bom" id="txtOtherCst_bom" value="" readonly /></td>
                <td><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtCommCst_bom" id="txtCommCst_bom" value="" readonly /></td>
                <td><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtFobDzn_bom" id="txtFobDzn_bom" value="" readonly /></td>
                
                <td><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtCpm_bom" id="txtCpm_bom" value="" readonly /></td>
                <td><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtSmv_bom" id="txtSmv_bom" value="" readonly /></td>
                
                <td><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtCmCst_bom" id="txtCmCst_bom" value="" readonly /></td>
                <td><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtPack_bom" id="txtPack_bom" value="" readonly />&nbsp;</td>
            </tr>
        </table>
            </div>
        </form>
	</div>
    </body> 
    <script>get_php_form_data($('#txt_costSheet_id').val()+'__'+$('#txtItem_id').val(),'populate_confirm_style_form_data','quick_costing_controller'); fnc_bom_data_load(); fnc_total_calculate(); fnc_select();</script>          
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();	
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
		$sql="select a.id, a.user_name, a.department_id, a.user_full_name, a.designation from  user_passwd a,electronic_approval_setup b where a.id=b.user_id and b.company_id=0 and b.page_id=$menu_id and valid=1 and a.id!=$user_id  and b.is_deleted=0  and b.page_id=$menu_id order by b.sequence_no";
			//echo $sql;
		$arr=array (2=>$custom_designation,3=>$Department);
		echo  create_list_view ( "list_view", "User ID,Full Name,Designation,Department", "100,120,150,150,","630","220",0, $sql, "js_set_value", "id,user_name", "", 1, "0,department_id,designation,department_id", $arr , "user_name,user_full_name,designation,department_id", "requires/user_creation_controller", 'setFilterGrid("list_view",-1);' ) ;
		?>
	</form>
	<script language="javascript" type="text/javascript">
	  setFilterGrid("tbl_style_ref");
	</script>
	<?
	exit();
}

if ($action=="appcause_popup")
{
	echo load_html_head_contents("Approval Cause Info", "../../", 1, 1,'','','');
	extract($_REQUEST);

	$data_all=explode('_',$data);
	$wo_id=$data_all[0];
	$app_type=$data_all[1];
	$app_cause=$data_all[2];
	$approval_id=$data_all[3];

	if($app_cause=="")
	{
		$sql_cause="select MAX(id) as id from fabric_booking_approval_cause where entry_form=28 and user_id='$user_id' and booking_id='$wo_id' and status_active=1 and is_deleted=0 and NOT_APPROVAL_CAUSE is not null";
		//echo $sql_cause; die;page_id='$menu_id' and 
		$nameArray_cause=sql_select($sql_cause);
		if(count($nameArray_cause)>0){
			foreach($nameArray_cause as $row)
			{
				$app_cause1=return_field_value("NOT_APPROVAL_CAUSE", "fabric_booking_approval_cause", "id='".$row[csf("id")]."' and status_active=1 and is_deleted=0 and NOT_APPROVAL_CAUSE is not null ");
				$app_cause = str_replace(array("\r", "\n"), ' ', $app_cause1);
			}
		}
		else $app_cause = '';
	}
	?>
    <script>
		$( document ).ready(function() {
			document.getElementById("appv_cause").value='<? echo $app_cause; ?>';
		});

		var permission='<? echo $permission; ?>';
		function fnc_appv_entry(operation)
		{
			var appv_cause = $('#appv_cause').val();

			if (form_validation('appv_cause','Approval Cause')==false)
			{
				if (appv_cause=='')
				{
					alert("Please write cause.");
				}
				return;
			}
			else
			{
				var data="action=save_update_delete_appv_cause&operation="+operation+get_submitted_data_string('appv_cause*wo_id*appv_type*page_id*user_id*approval_id',"../../");
				//alert (data);return;
				freeze_window(operation);
				http.open("POST","quick_costing_approval_v2_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange=fnc_appv_entry_Reply_info;
			}
		}

		function fnc_appv_entry_Reply_info()
		{
			if(http.readyState == 4)
			{
				var reponse=trim(http.responseText).split('**');
				show_msg(reponse[0]);
				//set_button_status(1, permission, 'fnc_appv_entry',1);
				release_freezing();
				//generate_worder_mail(reponse[2],reponse[3],reponse[4],reponse[5]);
			}
		}

		function fnc_close()
		{
			appv_cause= $("#appv_cause").val();
			document.getElementById('hidden_appv_cause').value=appv_cause;
			parent.emailwindow.hide();
		}

		function generate_worder_mail(woid,mail,appvtype,user)
		{
			var data="action=app_cause_mail&woid="+woid+'&mail='+mail+'&appvtype='+appvtype+'&user='+user;
			//alert (data);return;
			freeze_window(6);
			http.open("POST","quick_costing_approval_v2_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange=fnc_appv_mail_Reply_info;
		}

		function fnc_appv_mail_Reply_info()
		{
			if(http.readyState == 4)
			{
				var response=trim(http.responseText).split('**');
				release_freezing();
			}
		}
		
		
	function mail_send(){
		
	   if (confirm('Mail Send?')==false)
		{
			return;
		}
		else
		{
			get_php_form_data('<?=$data;?>','quick_approvail_mail','../../auto_mail/woven/quick_costing_app_mail');
		}
	}
		

    </script>
    <body>
		<div align="center" style="width:100%;">
        <? echo load_freeze_divs ("../../",$permission,1); ?>
        <form name="size_1" id="size_1">
			<fieldset style="width:450px;">
            <table align="center" cellspacing="0" width="450" class="rpt_table" border="1" rules="all" id="size_tbl" >
                <tr id="row_1">
                    <td width="150" align="center" >
                    	<textarea name="appv_cause" id="appv_cause" class="text_area" style="width:430px; height:100px;" maxlength="500" title="Maximum 500 Character"></textarea>
                        <Input type="hidden" name="wo_id" class="text_boxes" ID="wo_id" value="<? echo $wo_id; ?>" style="width:30px" />
                        <Input type="hidden" name="appv_type" class="text_boxes" ID="appv_type" value="<? echo $app_type; ?>" style="width:30px" />
                        <Input type="hidden" name="page_id" class="text_boxes" ID="page_id" value="<? echo $menu_id; ?>" style="width:30px" />
                        <Input type="hidden" name="user_id" class="text_boxes" ID="user_id" value="<? echo $user_id; ?>" style="width:30px" />
                        <Input type="hidden" name="approval_id" class="text_boxes" ID="approval_id" value="<? echo $approval_id; ?>" style="width:30px" />
                    </td>
                </tr>
            </table>

            <table align="center" cellspacing="0" width="450" class="rpt_table" border="1" rules="all" id="" >

                <tr>
                    <td align="center" class="button_container">
                        <?
						//print_r ($id_up_all);
                            if($id_up!='')
                            {
                                echo load_submit_buttons($permission, "fnc_appv_entry", 1,0,"reset_form('size_1','','','','','');",1);
                            }
                            else
                            {
                                echo load_submit_buttons($permission, "fnc_appv_entry", 0,0,"reset_form('size_1','','','','','');",1);
                            }
                        ?>
                        <input type="hidden" name="hidden_appv_cause" id="hidden_appv_cause" class="text_boxes /">
                    </td>
                </tr>
                <tr>
                    <td align="center">
                        <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />						
                        <input style="width:80px;" type="button" id="copy_btn" class="formbutton" value="Mail Send" onClick="mail_send();" />		

                    </td>
                </tr>
            </table>
            </fieldset>
            </form>
        </div>
    </body>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if ($action=="appinstra_popup")
{
	echo load_html_head_contents("Approval Cause Info", "../../", 1, 1,'','','');
	extract($_REQUEST);

	$data_all=explode('_',$data);
	$wo_id=$data_all[0];
	$app_type=$data_all[1];
	$app_cause=$data_all[2];
	$approval_id=$data_all[3];
	?>
    <script>

		$( document ).ready(function() {
			document.getElementById("appv_cause").value='<? echo $app_cause; ?>';
		});

		var permission='<? echo $permission; ?>';

		function fnc_close()
		{
			appv_cause= $("#appv_cause").val();
			document.getElementById('hidden_appv_cause').value=appv_cause;
			parent.emailwindow.hide();
		}
    </script>
    <body>
		<div align="center" style="width:100%;">
        <? echo load_freeze_divs ("../../",$permission,1); ?>
        <form name="size_1" id="size_1">
			<fieldset style="width:450px;">
            <table align="center" cellspacing="0" width="450" class="rpt_table" border="1" rules="all" id="size_tbl" >
                <tr id="row_1">
                    <td width="150" align="center" >
                    	<textarea name="appv_cause" id="appv_cause" class="text_area" style="width:430px; height:100px;" maxlength="500" title="Maximum 500 Character"></textarea>
                        <Input type="hidden" name="wo_id" class="text_boxes" ID="wo_id" value="<? echo $wo_id; ?>" style="width:30px" />
                        <Input type="hidden" name="appv_type" class="text_boxes" ID="appv_type" value="<? echo $app_type; ?>" style="width:30px" />
                        <Input type="hidden" name="page_id" class="text_boxes" ID="page_id" value="<? echo $menu_id; ?>" style="width:30px" />
                        <Input type="hidden" name="user_id" class="text_boxes" ID="user_id" value="<? echo $user_id; ?>" style="width:30px" />
                        <Input type="hidden" name="approval_id" class="text_boxes" ID="approval_id" value="<? echo $approval_id; ?>" style="width:30px" />
                    </td>
                </tr>
            </table>

            <table align="center" cellspacing="0" width="450" class="rpt_table" border="1" rules="all" id="" >

                <tr>
                    <td align="center" class="button_container">
                        <?
						//print_r ($id_up_all);
                            /*if($id_up!='')
                            {
                                echo load_submit_buttons($permission, "fnc_appv_instru_entry", 1,0,"reset_form('size_1','','','','','');",1);
                            }
                            else
                            {
                                echo load_submit_buttons($permission, "fnc_appv_instru_entry", 0,0,"reset_form('size_1','','','','','');",1);
                            }*/
                        ?>
                        <input type="hidden" name="hidden_appv_cause" id="hidden_appv_cause" class="text_boxes"/>

                    </td>
                </tr>
                <tr>
                    <td align="center">
                        <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
                    </td>
                </tr>
            </table>
            </fieldset>
            </form>
        </div>
    </body>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if ($action=="unappcause_popup")
{
	echo load_html_head_contents("Un Approval Request", "../../", 1, 1,'','','');
	extract($_REQUEST);
	$wo_id=$data;
	$sql_req="select approval_cause,approval_no from fabric_booking_approval_cause where entry_form=28 and booking_id='$wo_id' and approval_type=2 and status_active=1 and is_deleted=0 order by approval_no ";
	//echo $sql_req;
	$nameArray_req=sql_select($sql_req);
	foreach($nameArray_req as $row)
	{
		$unappv_req=$row[csf('approval_cause')];
	}
	?>
    <script>

		//var permission='<?// echo $permission; ?>';

		$( document ).ready(function() {
			document.getElementById("unappv_req").value='<? echo $unappv_req; ?>';
		});


		function fnc_close()
		{
			unappv_request= $("#unappv_req").val();
			document.getElementById('hidden_unappv_request').value=unappv_request;
			parent.emailwindow.hide();
		}

    </script>
    <body>
		<div align="center" style="width:100%;">
        <? echo load_freeze_divs ("../../",$permission,1); ?>
        <form name="size_1" id="size_1">
			<fieldset style="width:450px;">
            <table align="center" cellspacing="0" width="450" class="rpt_table" border="1" rules="all" id="size_tbl" >
                <tr id="row_1">
                    <td width="150" align="center" >
                    	<textarea name="unappv_req" id="unappv_req" class="text_area" style="width:430px; height:100px;"></textarea>
                    </td>
                </tr>
            </table>

            <table align="center" cellspacing="0" width="450" class="rpt_table" border="1" rules="all" id="" >
                <tr>
                    <td align="center">
                    	<input type="hidden" name="hidden_unappv_request" id="hidden_unappv_request" class="text_boxes /">
                        <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
                    </td>
                </tr>
            </table>
            </fieldset>
            </form>
        </div>
    </body>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
	<?
	exit();
}

if ($action=="save_update_delete_appv_cause")
{
	//$approval_id
	$approval_type=str_replace("'","",$appv_type);
		
	if($approval_type==2)
	{
   		$process = array( &$_POST );
		extract(check_magic_quote_gpc( $process ));

		if ($operation==0)  // Insert Here
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}

			$approved_no_history=return_field_value("approved_no","approval_history","entry_form=28 and mst_id=$wo_id and approved_by=$user_id");
			$approved_no_cause=return_field_value("approval_no","fabric_booking_approval_cause","entry_form=28 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");
			 //echo "10**".$approved_no_history.'='.$approved_no_cause; die;
			if($approved_no_cause==0){$approved_no_cause="";}
			
			if($approved_no_history=="" && $approved_no_cause=="")
			{
				//echo "insert"; die;

				$id_mst=return_next_id( "id", "fabric_booking_approval_cause", 1 ) ;

				$field_array="id,page_id,entry_form,user_id,booking_id,approval_type,approval_no,NOT_APPROVAL_CAUSE,inserted_by,insert_date,status_active,is_deleted";
				$data_array="(".$id_mst.",".$page_id.",28,".$user_id.",".$wo_id." ,".$appv_type.",0,".$appv_cause.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				$rID=sql_insert("fabric_booking_approval_cause",$field_array,$data_array,0);
				 //echo $rID; die;
				//echo "10**INSERT INTO fabric_booking_approval_cause (".$field_array.") VALUES ".$data_array; die;
				if($db_type==0)
				{
					if($rID )
					{
						mysql_query("COMMIT");
						echo "0**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
					}
					else
					{
						mysql_query("ROLLBACK");
						echo "10**".$rID;
					}
				}
				else if($db_type==2 || $db_type==1 )
				{
					if($rID )
					{
						oci_commit($con);
						echo "0**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
					}
					else
					{
						oci_rollback($con);
						echo "10**".$rID;
					}
				}
				disconnect($con);
				die;
			}
			else if($approved_no_history=="" && $approved_no_cause!="")
			{
				$con = connect();
				if($db_type==0)

				{
					mysql_query("BEGIN");
				}

				$id_cause=return_field_value("max(id)","fabric_booking_approval_cause","entry_form=28 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");

				$field_array="user_id*booking_id*approval_type*approval_no*NOT_APPROVAL_CAUSE*updated_by*update_date*status_active*is_deleted";
				$data_array="".$user_id."*".$wo_id."*".$appv_type."*0*".$appv_cause."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";

				$rID=sql_update("fabric_booking_approval_cause",$field_array,$data_array,"id","".$id_cause."",0);

				if($db_type==0)
				{
					if($rID )
					{
						mysql_query("COMMIT");
						echo "1**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
					}
					else
					{
						mysql_query("ROLLBACK");
						echo "10**".$rID;
					}
				}
				else if($db_type==2 || $db_type==1 )
				{
					if($rID )
					{
						oci_commit($con);
						echo "1**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
					}
					else
					{
						oci_rollback($con);
						echo "10**".$rID;
					}
				}
				disconnect($con);
				die;
			}
			else if($approved_no_history!="" && $approved_no_cause!="")
			{
				$max_appv_no_his=return_field_value("max(approved_no)","approval_history","entry_form=28 and mst_id=$wo_id and approved_by=$user_id");
				$max_appv_no_cause=return_field_value("max(approval_no)","fabric_booking_approval_cause","entry_form=28 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");

				if($max_appv_no_his!=$max_appv_no_cause)
				{
					$id_mst=return_next_id( "id", "fabric_booking_approval_cause", 1 ) ;

					$field_array="id,page_id,entry_form,user_id,booking_id,approval_type,approval_no,NOT_APPROVAL_CAUSE,inserted_by,insert_date,status_active,is_deleted";
					$data_array="(".$id_mst.",".$page_id.",28,".$user_id.",".$wo_id." ,".$appv_type.",".$max_appv_no_his.",".$appv_cause.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
					$rID=sql_insert("fabric_booking_approval_cause",$field_array,$data_array,0);
					//echo $rID; die;

					if($db_type==0)
					{
						if($rID )
						{
							mysql_query("COMMIT");
							echo "0**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
						}
						else
						{
							mysql_query("ROLLBACK");
							echo "10**".$rID;
						}
					}
					else if($db_type==2 || $db_type==1 )
					{
						if($rID )
						{
							oci_commit($con);
							echo "0**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
						}
						else
						{
							oci_rollback($con);
							echo "10**".$rID;
						}
					}
					disconnect($con);
					die;
				}
				else if($max_appv_no_his==$max_appv_no_cause)
				{
					$con = connect();
					if($db_type==0)
					{
						mysql_query("BEGIN");
					}

					$id_cause=return_field_value("max(id)","fabric_booking_approval_cause","entry_form=28 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");

					$field_array="user_id*booking_id*approval_type*approval_no*approval_cause*updated_by*update_date*status_active*is_deleted";
					$data_array="".$user_id."*".$wo_id."*".$appv_type."*".$max_appv_no_his."*".$appv_cause."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";

					 $rID=sql_update("fabric_booking_approval_cause",$field_array,$data_array,"id","".$id_cause."",0);

					if($db_type==0)
					{
						if($rID )
						{
							mysql_query("COMMIT");
							echo "1**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
						}
						else
						{
							mysql_query("ROLLBACK");
							echo "10**".$rID;
						}
					}
					else if($db_type==2 || $db_type==1 )
					{
						if($rID )
						{
							oci_commit($con);
							echo "1**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
						}
						else
						{
							oci_rollback($con);
							echo "10**".$rID;
						}
					}
					disconnect($con);
					die;
				}
			}
		}
		if ($operation==1)  // Update Here
		{

		}

	}//type=0
	if($approval_type==1)
	{
		$process = array( &$_POST );
		extract(check_magic_quote_gpc( $process ));

		if ($operation==0)  // Insert Here
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			$unapproved_cause_id=return_field_value("id","fabric_booking_approval_cause","entry_form=28 and user_id=$user_id and booking_id=$wo_id and approval_type=$appv_type and approval_history_id=$approval_id");
			
			$mst_id=return_field_value("id","qc_mst","qc_no=$wo_id and status_active=1 and is_deleted=0");
			$max_appv_no_his=return_field_value("max(approved_no)","approval_history","entry_form=45 and mst_id=$mst_id and approved_by=$user_id");
			if($unapproved_cause_id==0){$unapproved_cause_id="";}
			
			if($unapproved_cause_id=="")
			{
				$id_mst=return_next_id( "id", "fabric_booking_approval_cause", 1);

				$field_array="id, page_id, entry_form, user_id, booking_id, approval_type, approval_no, approval_history_id, NOT_APPROVAL_CAUSE, inserted_by, insert_date, status_active, is_deleted";
				$data_array="(".$id_mst.",".$page_id.",28,".$user_id.",".$wo_id." ,".$appv_type.",".$max_appv_no_his.",".$approval_id.",".$appv_cause.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";

				$rID=sql_insert("fabric_booking_approval_cause",$field_array,$data_array,0);
				//echo $rID; 
				 //echo "10**INSERT INTO fabric_booking_approval_cause (".$field_array.") VALUES ".$data_array; die;	

				if($db_type==0)
				{
					if($rID )
					{
						mysql_query("COMMIT");
						echo "0**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
					}
					else
					{
						mysql_query("ROLLBACK");
						echo "10**".$rID;
					}
				}
				else if($db_type==2 || $db_type==1 )
				{
					if($rID )
					{
						oci_commit($con);
						echo "0**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
					}
					else
					{
						oci_rollback($con);
						echo "10**".$rID;
					}
				}
				disconnect($con);
				die;
			}
			else
			{
				//echo "10**entry_form=7 and user_id=$user_id and booking_id=$wo_id  and approval_type=$appv_type and approval_history_id=$approval_id";die;
				$id_cause=return_field_value("max(id)","fabric_booking_approval_cause","entry_form=28 and user_id=$user_id and booking_id=$wo_id  and approval_type=$appv_type and approval_history_id=$approval_id");

				$field_array="user_id*booking_id*approval_type*approval_no*approval_history_id*NOT_APPROVAL_CAUSE*updated_by*update_date*status_active*is_deleted";
				$data_array="".$user_id."*".$wo_id."*".$appv_type."*".$max_appv_no_his."*".$approval_id."*".$appv_cause."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";

				$rID=sql_update("fabric_booking_approval_cause",$field_array,$data_array,"id","".$id_cause."",0);

				if($db_type==0)
				{
					if($rID )
					{
						mysql_query("COMMIT");
						echo "1**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
					}
					else
					{
						mysql_query("ROLLBACK");
						echo "10**".$rID;
					}
				}
				else if($db_type==2 || $db_type==1 )
				{
					if($rID )
					{
						oci_commit($con);
						echo "1**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
					}
					else
					{
						oci_rollback($con);
						echo "10**".$rID;
					}
				}
				disconnect($con);
				die;
			}
		}

	}//type=1
}

if ( $action=="app_cause_mail" )
{
	//echo $woid.'_'.$mail.'_'.$appvtype; die;
	ob_start();
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	?>
        <table width="800" cellpadding="0" cellspacing="0" border="1">
            <tr>
                <td valign="top" align="center"><strong><font size="+2">Subject : Quick Costing &nbsp;<?  if($appvtype==0) echo "Approval Request"; else echo "Un-Approval Request"; ?>&nbsp;Refused</font></strong></td>
            </tr>
            <tr>
                <td valign="top">
                    Dear Mr. <?
								$to="";

								$sql ="SELECT c.team_member_name FROM wo_booking_mst a, wo_po_details_master b, lib_mkt_team_member_info c where b.job_no=a.job_no and b.dealing_marchant=c.id and a.id=$woid";
								$result=sql_select($sql);
								foreach($result as $row)
								{
									if ($to=="")  $to=$row[csf('team_member_name')]; else $to=$to.", ".$row[csf('team_member_name')];
								}
								echo $to;
							?>
                            <br> Your Cost Sheet No. &nbsp;
							<?
								$sql1 ="SELECT booking_no,buyer_id FROM wo_booking_mst where id=$woid";
								$result1=sql_select($sql1);
								foreach($result1 as $row1)
								{
									$wo_no=$row1[csf('booking_no')];
									$buyer=$row1[csf('buyer_id')];
								}


							?>&nbsp;<?  echo $wo_no;  ?>,&nbsp; <? echo $buyer_arr[$buyer]; ?>&nbsp;of buyer has been refused due to following reason.
                </td>
            </tr>
            <tr>
                <td valign="top">
                    <?  echo $mail; ?>
                </td>
            </tr>
            <tr>
                <td valign="top">
                    Thanks,<br>
					<?
						$user_name=return_field_value("user_name","user_passwd","id=$user");
						echo $user_name;
					?>
                </td>
            </tr>
        </table>
    <?

	$to="";

	$sql2 ="SELECT c.team_member_email FROM wo_booking_mst a,wo_po_details_master b,lib_mkt_team_member_info c where b.job_no=a.job_no and b.dealing_marchant=c.id and a.id=$woid";

		$result2=sql_select($sql2);
		foreach($result2 as $row2)
		{
			if ($to=="")  $to=$row2[csf('team_member_email')]; else $to=$to.", ".$row2[csf('team_member_email')];
		}

 		$subject="Approval Status";
    	$message="";
    	$message=ob_get_contents();
    	ob_clean();

		//echo $message;
		 //$to='akter.babu@gmail.com,saeed@fakirapparels.com,akter.hossain@fakirapparels.com,bdsaeedkhan@gmail.com,shajjadhossain81@gmail.com';
		//$to='shajjad@logicsoftbd.com';
		//$to='shajjadhossain81@gmail.com';
		$header=mail_header();

		echo send_mail_mailer( $to, $subject, $message, $header );

		/*if (mail($to,$subject,$message,$header))
			echo "****Mail Sent.---".date("Y-m-d");
		else
			echo "****Mail Not Sent.---".date("Y-m-d");*/

		//echo "222**".$woid;
		exit();
}

if($action=="populate_cm_compulsory")
{
	$cm_cost_compulsory=return_field_value("cm_cost_compulsory","variable_order_tracking","company_name ='".$data."' and variable_list=22 and is_deleted=0 and status_active=1");
	echo $cm_cost_compulsory;
	exit();
}
?>
